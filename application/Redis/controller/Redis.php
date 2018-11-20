<?php
namespace app\Redis\controller;

use think\facade\Config;
use think\Db;
use app\MyCommon\controller\Base;

class Redis
{
    // protected $redis = null;
    // protected $socket = null;
    protected $Config = null;
    // public function __construct()
    // {
    // }
    public function start()
    {
        // 获取配置信息
        $this->Config = Config::pull('worker');
        // 创建一个socket
        
        // 实例化redis对象
        $redis = new \Redis();
        // 链接redis服务器
        $redis->connect($this->Config['redis']['host'], $this->Config['redis']['port']) or die("redis connect failed!");
        //
        $redis->setOption(\Redis::OPT_READ_TIMEOUT, -1) or die("set time out failed!");
        echo "redis listening...\n";
        // redis过期监听事件
        $redis->psubscribe(array('__keyevent@0__:expired'), function ($redis, $pattern, $chan, $msg) {
            // 获取获取红包的id以及红包消息的id
            $data = json_decode($msg, true);
            dump($data);
            if (isset($data['tid'])) {
                // 转账获取
                // 更新转账的状态
                DB::name("user_transfer")->where('id', $data['tid'])->update(['status'=>2]);
                // 获取转账信息的内容
                $msgInfo = DB::name('user_message')->where('id', $data['mid'])->field('id,content')->find();
                $msgInfo['content'] = "true:".$msgInfo['content'];
                // 更新转账信息的领取状态
                DB::name("user_message")->update($msgInfo);
                // 获转账用户的id和转账金额
                $tansInfo = DB::name('user_transfer')
                ->alias('t')
                ->join('bc_user_user u','u.id=t.recive_id')
                ->where('t.id', $data['tid'])
                ->field('t.gold,t.send_id,t.recive_id,u.nickname,u.connectionID')
                ->find();
                
                // 退还给用户，更新用户金币信息
                DB::name('user_user')->where('id', $tansInfo['send_id'])->setInc('gold', $tansInfo['gold']);
                $userInfo = DB::name("user_user")->where('id', $tansInfo['send_id'])->field('gold,connectionID')->find();
                $sysMsg = [
                    'send_id'=>-10086,
                    'get_id'=>$tansInfo['send_id'],
                    'time'=>time(),
                    'status'=>-1,
                    'content'=>'(@title:转账过期退还通知@)由于用户“'.$tansInfo['nickname'].'”过期未领取转账，转账金额'.$tansInfo['gold'].'个金币于'.date('Y-m-d H:i:s').'已退还至您的账户当中！',
                    'flag'=>0
                ];
                $id = DB::name('user_message')->insertGetId($sysMsg);
                $sysMsg['id'] = $id;
                // dump($sysMsg);
                if ($userInfo['connectionID']) {
                    // 向用户推送消息
                    try {
                        // 判断金币是否存在两位小数
                        if (strpos($tansInfo['gold'], '.') === false) {
                            $str = '@transfer#'.$data['tid'].'-'.$tansInfo['gold'].'.00#transfer@';
                        } else {
                            $stmp = explode(".", $tansInfo['gold']);
                            $str = '@transfer#'.$tansInfo['id'].'-'.$stmp[0].'.00#transfer@';
                        }
                        // 向workerman推送转账过期退还信息
                        $data = [
                            'type'=>'giveBackTransfer',
                            'con_id'=>$userInfo['connectionID'],
                            'uid'=>$tansInfo['send_id'],
                            'gold'=>$userInfo['gold'],
                            'tid'=>$str,
                            'fcon_id'=>$tansInfo['connectionID'],
                            'systemNotice'=>$sysMsg,
                            'fid'=>$tansInfo['recive_id']
                        ];
                        $this->send($data);
                    } catch (\Exception $e) {
                        echo $e->getMessage();
                    }
                }
            } else {
                // 获取红包信息
                $redInfo = DB::name('user_red')->where('id', $data['red_id'])->find();
                // 判断红包是否已经被领取完
                if (!$redInfo['status'] && $redInfo['recive_num'] > $redInfo['recived_num']) {
                    // DB::startTrans();
                    // 红包未领取或者未领取完
                    try {
                        // 获取发红包的用户信息
                        $userInfo = DB::name('user_user')->where('id', $redInfo['send_id'])->field('username,gold,id,connectionID')->find();
                        // 记录当前金币
                        $gold = $userInfo['gold'];

                        // 加上退还金额
                        if ($redInfo['type'] == 1) {
                            // 个人红包
                            $userInfo['gold'] += $redInfo['money'];
                        } else {
                            // 群发红包
                            if ($redInfo['detail']) {
                                $redDetail = json_decode($redInfo['detail'], true);
                            } else {
                                $redDetail = [];
                            }
                            if (!empty($redDetail)) {
                                foreach ($redDetail as $k => $v) {
                                    $redInfo['money'] -= $v['gold'];
                                }
                            }
                            $userInfo['gold'] += $redInfo['money'];
                        }
                        // 更新用户信息
                        DB::name('user_user')->update($userInfo);

                        // 金币流水详情
                        $goldDetail = [
                            '退还前'=>$gold,
                            '退还金额'=>$redInfo['money'],
                            '退还后'=>$userInfo['gold'],
                        ];
                        Base::goldHistory($userInfo['username'], '退还红包', $goldDetail);
                        // 更新红包消息记录的红包领取状态为过期状态，true表示过期，false表示已领取
                        if ($redInfo['type']==1) {
                            $content = DB::name('user_message')->field('id,content')->where('id', $data['mid'])->find();
                            $content['content'] = 'true:'.$content['content'];
                            DB::name('user_message')->update($content);
                        } else {
                            $content = DB::name('chat_room')->field('id,content')->where('id', $data['mid'])->find();
                            $content['content'] = 'true:'.$content['content'];
                            DB::name('chat_room')->update($content);
                        }
                        // 修改红包的领取状态为过期状态，statu=1为领取状态，status=0为待领取状态，status=2为过期状态
                        DB::name('user_red')->where('id', $redInfo['id'])->update(['status'=>2]);
                        // 判断用户是否在线
                        if ($userInfo['connectionID']) {
                            // 向用户推送红包过期退还消息
                            try {
                                $data = [
                                    'type'=>'giveBackRedbag',
                                    'con_id'=>$userInfo['connectionID'],
                                    'uid'=>$userInfo['id'],
                                    'gold'=>$userInfo['gold'],
                                    'red_id'=>'@redbag'.$redInfo['id'].'redbag@',
                                    'fid'=>$redInfo['get_id'],
                                    'red_type'=>$redInfo['type'],
                                    // 'send_id'=>$redInfo['send_id'],
                                ];
                                $this->send($data);
                                // echo socket_strerror(socket_last_error());
                            } catch (\Exception $e) {
                                echo $e->getMessage();
                            }
                        }
                   
                        // DB::commit();
                    } catch (\Exception $e) {
                        // DB::rollback();
                        echo "errorMsg:".$e->getMessage()."\n";
                    }
                } else {
                    dump("已被领取");
                }
            }
        });
    }


    public function send($data)
    {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if (!$socket) {
            throw new Exception("redis creates socket failed!");
        } else {
            // 链接服务器
            $conn = socket_connect($socket, $this->Config['redis']['host'], $this->Config['port']);
            if ($conn) {
                // dump($data);
                $data = "#@#redis-data-request#@#" . json_encode($data);
                // dump($data);
                if(socket_send($socket, $data, strlen($data), MSG_DONTROUTE)){
                    echo "send successful!\n";
                }else{
                    throw new Exception("send failed!");
                }
            } else {
                throw new Exception("redis socket connects workerman failed!");
            }
            socket_close($socket);
        }
    }
}
