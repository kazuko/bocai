<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace app\http\controller;

use Workerman\Worker as MyWebServer;
use think\worker\Server;
use think\worker\Application;
use think\console\Output;
use Workerman\Lib\Timer;
use app\Home\controller\DataProcer;

// use app\Home\controller\Game;

// use think\

/**
 * Worker 命令行服务类
 */
class MyWorkerServer extends Server
{
    protected $output;
    protected $app;
    protected $host;
    protected $port;
    protected $userCons;
    protected $time_interval; // 心跳间隔
    protected $timer_handle; // 定时器句柄
    protected $gameProcer; // 游戏处理

    protected $dataProcer;
    // 保存已经存在的链接ID
    // protected $procers = [
    //     'app\Home\controller\',
    // ];
    // 已经存在但退出或者掉线的链接ID
    // protected $outConnectionId = [];　
    // protected $maxConnectionId = 0;

    /**
     * 架构函数
     * @access public
     * @param  string $host 监听地址
     * @param  int    $port 监听端口
     * @param  array  $context 参数
     */
    public function __construct($host, $port, $context = [])
    {
        // dump("[MyWorkerServer->construct]\n");
        defined("DS") || define('DS', DIRECTORY_SEPARATOR);
        defined('ROOT') || define('ROOT', __DIR__.'/../../../');
        // header("Content-Type: text/html;charset=GB2312");
        $this->host = $host;
        $this->port = $port;
        // 初始化心跳间隔为30秒
        $this->time_interval = 30;
        // 初始化定时器句柄
        $this->timer_handle = '';
        //
        $this->output = new Output();
        $this->worker = new MyWebServer('websocket://' . $host . ':' . $port, $context);

        // 设置回调
        foreach ($this->event as $event) {
            if (method_exists($this, $event)) {
                $this->worker->$event = [$this, $event];
            }
        }
    }

    /**
     * 设置参数
     * @access public
     * @param  array    $option 参数
     * @return void
     */
    public function option(array $option)
    {
        // dump("[MyWorkerServer->option]\n");
        // 设置参数
        if (!empty($option)) {
            foreach ($option as $key => $val) {
                $this->worker->$key = $val;
            }
        }
    }
    
    /**
     * onWorkerStart 启动事件回调
     * @access public
     * @param  \Workerman\Worker    $worker
     * @return void
     */
    public function onWorkerStart($worker)
    {
        // file_put_contents('H:\phpstudy\WWW\workerman.json', json_encode(['workerStatus'=>true]));
        $this->timer_handle = Timer::add($this->time_interval, function () {
            try {
                dump("one time heart\n");
                if (!empty($this->userCons)) {
                    $nowtime = time();
                    foreach ($this->userCons as $k => $con) {
                        if (isset($con['con']) && $con['con']) {
                            // 判断是否超过五次心跳没有反映
                            if ($nowtime > (5 * $this->time_interval + $con['lasttime'])) {
                                if (isset($con['uid']) && $con['uid']) {
                                    // 下线通知
                                    $noticeInfo = [
                                        'send_id'=>$con['uid'],
                                        'type'=>'downLine',
                                        'con_id'=>$k,
                                    ];
                                    $this->downLine($con['con'], $noticeInfo);
                                }
                                // 关闭没有反映的链接并清除资源
                                $con['con']->close();
                                $this->userCons[$k] = null;
                                unset($this->userCons[$k]);
                            } elseif ($nowtime >= ($con['lasttime'] + $this->time_interval)) {
                                // 向间隔一个心跳以上没活动的用户发送心跳信息
                                $con['con']->send(json_encode([
                                    'msg'=>'heart',
                                    'type'=>'heart',
                                    'role'=>0,
                                    'send_id'=>'',
                                ]));
                            }
                        } else {
                            unset($this->userCons[$k]);
                        }
                    }
                }
            } catch (\Exception $e) {
                dump("onWorkerStart->catch::".$e->getMessage());
            }
        });
        $this->dataProcer = new DataProcer();
        // $this->gameProcer = new Game();
        
        // windows下开启处理服务器，输出提示
        if (DIRECTORY_SEPARATOR == '\\') {
            $this->output->writeln("WebSocket has started: <http://{$this->host}:{$this->port}>");
            $this->output->writeln('You can exit with <info>`CTRL-C`</info>');
        }
        $this->output->writeln('listening...');
        // dump($worker);
    }

    /**
     * onConnect 链接事件回调
     * @access public
     * @param \Workerman\Connection\TcpConnection    $connection
     * @return void
     */
    public function onConnect($connection)
    {
        // dump("connectionID:".$connection->id."\n");
        // $this->userCons[$connection->id]['con'] = $connection;
        // $this->userCons[$connection->id]['lasttime'] = time();
        $connection->send(json_encode([
            'type'=>'ConnectionSuccess',
            'connectionId'=>$connection->id,
            'msg'=>'成功链接服务器',
        ]));
    }
    /**
     * onMessage 消息事件回调
     * @access public
     * @param  \Workerman\Connection\TcpConnection    $connection
     * @param  mixed                                  $data
     * @return void
     */
    public function onMessage($connection, $data)
    {
        try {
            // dump("[MyWorkerServer->onMessage]\n");
            $data = str_replace('\\', '/', $data);
            // dump("Recive:".$data."\n");
            $data = json_decode($data, true);
            echo "accept message:\n";
            dump($this->iconv($data));
            switch ($data['type']) {
                case "handle":
                    $this->handle($connection, $data);
                    break;
                case "text":
                    $this->sendMessage($connection, $data);
                    break;
                case "img":
                    $this->sendImg($connection, $data);
                    break;
                case "close":
                    $this->closeSocket($data);
                    break;
                case "searchFriends":
                    $this->searchFrinds($connection, $data);
                    break;
                case "addFriendRequest":
                    $this->addFriend($connection, $data);
                    break;
                case "downLine":
                    $this->downLine($connection, $data);
                    break;
                case "deleteFriend":
                    $this->deleteFriend($connection, $data);
                    break;
                case "DeleteMessage":
                    $this->DeleteMessage($connection, $data);
                    break;
                case "AgreeFriendRequest":
                    dump("I case the AgreeFriendRequest\n");
                    $this->AgreeFriendRequest($connection, $data);
                    break;
                case "changeSystemReadStatus":
                    $this->changeSystemReadStatus($connection, $data);
                    break;
                case "delStrange":
                    $this->delStrange($connection, $data);
                    break;
                case "changeTrading":
                    $this->changeTrading($connection, $data);
                    break;
                case "sendRedBagToFriend":
                    $this->sendRedBagToFriend($connection, $data);
                    break;
                case "reciveRedBagFromFriend":
                    $this->reciveRedBagFromFriend($connection, $data);
                    break;
                case "getAppInfo":
                    $this->getAppInfo($connection, $data);
                    break;
                case "logining":
                    $this->logining($connection, $data);
                    break;
                case "RedDetail":
                    $this->RedDetail($connection, $data);
                    break;
                case "storageGold":
                    $this->storageGold($connection, $data);
                    break;
                case "getGoldFromBank":
                    $this->getGoldFromBank($connection, $data);
                    break;
                case "settingChange":
                    $this->settingChange($connection, $data);
                    break;
                case "changeNickName":
                    $this->changeNickName($connection, $data);
                    break;
                case "changeSignature":
                    $this->changeSignature($connection, $data);
                    break;
                case "changeLoginPassword":
                    $this->changeLoginPassword($connection, $data);
                    break;
                case "getSendRedList":
                    $this->getSendRedList($connection, $data);
                    break;
                case "getReciveRedList":
                    $this->getReciveRedList($connection, $data);
                    break;
                case "getThemeList":
                    $this->getThemeList($connection, $data);
                    break;
                case "getPostList":
                    $this->getPostList($connection, $data);
                    break;
                case "getComment":
                    $this->getComment($connection, $data);
                    break;
                case "themeComment":
                    $this->themeComment($connection, $data);
                    break;
                case "zanComment":
                    $this->zanComment($connection, $data);
                    break;
                case "ReplyComment":
                    $this->ReplyComment($connection, $data);
                    break;
                case "postTheme":
                    $this->postTheme($connection, $data);
                    break;
                case "getMyThemeList":
                    $this->getMyThemeList($connection, $data);
                    break;
                case "getMyReplyList":
                    $this->getMyReplyList($connection, $data);
                    break;
                case "transferAccounts":
                    $this->transferAccounts($connection, $data);
                    break;
                case "getTransferInfo":
                    $this->getTransferInfo($connection, $data);
                    break;
                case "reciveTransfer":
                    $this->reciveTransfer($connection, $data);
                    break;
                case "getMedalList":
                    $this->getMedalList($connection, $data);
                    break;
                case "changeMedalStatus":
                    $this->changeMedalStatus($connection, $data);
                    break;
                case "sendRadio":
                    $this->sendRadio($connection, $data);
                    break;
                case "getRegisterRules":
                    $this->getRegisterRules($connection, $data);
                    break;
                case "getYanZhengMa":
                    $this->getYanZhengMa($connection, $data);
                    break;
                case "registerMember":
                    $this->registerMember($connection, $data);
                    break;
                case "getChatRoomMsgList":
                    $this->getChatRoomMsgList($connection, $data);
                    break;
                case "ChatRoomMsg":
                    $this->ChatRoomMsg($connection, $data);
                    break;
                case "leaveChatRoom":
                    $this->leaveChatRoom($connection, $data);
                    break;
                default:
                    $this->dataProcer->mainProcer($data);
                    break;
            }
            if (isset($this->userCons[$data['con_id']]['con']) && $this->userCons[$data['con_id']]['con']) {
                // 保存用户最后活动的时间
                $this->userCons[$data['con_id']]['lasttime'] = time();
            }
        } catch (\Exception $e) {
            dump("onmessage->catch::".$e->getMessage());
        }
    }

    /**
     * 用户离开聊天室通知
     */
    private function leaveChatRoom($connection, $data)
    {
        try {
            $this->userCons[$data['con_id']]['rType'] = false;
        } catch (\Exception $e) {
            dump("leaveChatRoom->catch::".$e->getMessage());
        }
    }
    /**
     * 发送聊天信息（公共聊天室）
     */
    private function ChatRoomMsg($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        $connection->send(json_encode($result));
        if ($result['status'] && !empty($this->userCons)) {
            $data['msg']['id'] = $result['id'];
            foreach ($this->userCons as $k => $v) {
                if (isset($v['rType']) && $v['rType'] && $k != $data['con_id'] && isset($v['con']) && $v['con']) {
                    $v['con']->send(json_encode([
                        'type'=>'ChatRoomMsg',
                        'msg'=>$data['msg'],
                    ]));
                }
            }
        }
    }
    /**
     * 获取聊天室聊天记录
     */
    private function getChatRoomMsgList($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        $this->userCons[$data['con_id']]['rType'] = true;
        $connection->send(json_encode($result));
    }
    /**
     * 注册功能
     */
    private function registerMember($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        $connection->send(json_encode($result));
    }
    /**
     * 获取验证码
     */
    private function getYanZhengMa($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        $connection->send(json_encode($result));
    }
    /**
     * 获取注册规则
     */
    private function getRegisterRules($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        $connection->send(json_encode($result));
    }
    /**
     * 发广播，直接发送，不保存记录
     */
    private function sendRadio($connection, $data)
    {
        $connection->send(json_encode([
            'type'=>'resopneSendRadio',
            'status'=>true,
            'radiotext'=>[
                'content'=>$data['content'],
                'send_user'=>$data['send_name'],
                'send_id'=>$data['send_id'],
            ],
        ]));
        if (!empty($this->userCons)) {
            // 向所有用户推送广播消息
            foreach ($this->userCons as $key => $vo) {
                if (isset($vo['con']) && $vo['con'] && $vo['con'] != $connection) {
                    $vo['con']->send(json_encode([
                        'type'=>'radioText',
                        'content'=>$data['content'],
                        'send_user'=>$data['send_name'],
                        'send_id'=>$data['send_id'],
                    ]));
                }
            }
        }
    }
    /**
     * 修改个人勋章状态
     */
    private function changeMedalStatus($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        $connection->send(json_encode($result));
    }
    /**
     * 获取勋章列表
     */
    private function getMedalList($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        $connection->send(json_encode($result));
    }
    /**
     * 领取转账
     */
    private function reciveTransfer($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        // 想当前用户反馈结果
        $connection->send(json_encode($result));
        if ($result['status']) {
            if ($data['send_con']) {
                // 若发送方在线，则向发送方发送被领取提醒
                if (isset($this->userCons[$data['send_con']]['uid']) && $this->userCons[$data['send_con']]['uid']) {
                    $this->userCons[$data['send_con']]['con']->send(json_encode([
                        'type'=>'recivedTransferNotice',
                        'friend_id'=>$data['user_id'],//朋友id
                        'transfer_id'=>$data['transfer_id'],//转账信息id
                        'transfer_gold'=>$result['transfer_gold'], //转账金额（带小数点）
                    ]));
                }
            }
        }
    }
    /**
     * 获取转账信息
     */
    private function getTransferInfo($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        $connection->send(json_encode($result));
    }
    /**
     * 转账功能
     */
    private function transferAccounts($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        // 向用户反馈转账结果
        $connection->send(json_encode([
            'type'=>$result['type'],
            'gold'=>$result['gold'],
            'status'=>$result['status'],
            'msg'=>$result['msg']
        ]));
        if ($result['status'] && $data['recive_connectionID']) {
            // 转账成功
            // 判断对方是否已经登陆
            if (isset($this->userCons[$data['recive_connectionID']]['uid']) && $this->userCons[$data['recive_connectionID']]['uid'] == $data['recive_id']) {
                $this->userCons[$data['recive_connectionID']]['con']->send(json_encode([
                    'type'=>'text',
                    'data'=>[
                        'msg'=>$result['msg'],
                        'userInfo'=>$result['userInfo']
                    ],
                ]));
            }
        }
    }
    /**
     * 获取我的回帖
     */
    private function getMyReplyList($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        $connection->send(json_encode($result));
    }
    /**
     * 获取我的帖子
     */
    private function getMyThemeList($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        $connection->send(json_encode($result));
    }
    /**
     * 发帖子
     */
    private function postTheme($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        $connection->send(json_encode($result));
    }
    /**
     * 回复评论
     */
    private function ReplyComment($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        $connection->send(json_encode($result));
    }
    /**
     * 评论点赞
     */
    private function zanComment($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        $connection->send(json_encode($result));
    }

    /**
     * 发表评论
     */
    private function themeComment($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        $connection->send(json_encode($result));
    }
    /**
     * 获取帖子评论
     */
    private function getComment($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        // dump('==============================================');
        // dump($result);
        // dump('==============================================');
        $connection->send(json_encode($result));
    }
    /**
     * 获取帖子列表
     */
    private function getPostList($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        $connection->send(json_encode($result));
    }
    /**
     * 获取论坛区列表
     */
    private function getThemeList($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        $connection->send(json_encode($result));
    }

    /**
     * 获取收到的红包列表
     */
    private function getReciveRedList($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        $connection->send(json_encode($result));
    }
    /**
     * 获取发送的红包列表
     */
    private function getSendRedList($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        $connection->send(json_encode($result));
    }
    /**
     * 修改登录密码
     */
    private function changeLoginPassword($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        $connection->send(json_encode($result));
    }
    /**
     * 修改签名
     */
    private function changeSignature($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        $connection->send(json_encode($result));
    }
    /**
     * 修改昵称
     */
    private function changeNickName($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        $connection->send(json_encode($result));
    }
    /**
     * 修改个人设置
     */
    private function settingChange($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        $connection->send(json_encode($result));
    }
    /**
     * 从银行取出金币
     */
    private function getGoldFromBank($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        $connection->send(json_encode($result));
    }
    /**
     * 存储金币
     */
    private function storageGold($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        $connection->send(json_encode($result));
    }
    /**
     * 获取红包详情
     */
    private function RedDetail($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        $connection->send(json_encode($result));
    }

    /**
     * 登陆请求处理函数
     */
    private function logining($connection, $data)
    {
        $data['ip_address'] = $connection->getRemoteIp();
        $result = $this->dataProcer->mainProcer($data);
        var_dump($result);
        $connection->send(json_encode($result));
    }
    /**
     * 获取app信息请求处理函数
     */
    private function getAppInfo($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        $connection->send(json_encode($result));
    }

    /**
     * 领取朋友发的红包
     */
    private function reciveRedBagFromFriend($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        $connection->send(json_encode($result));
        if ($result['status']) {
            if ($data['red_send_con']) {
                if (isset($this->userCons[$data['red_send_con']]['uid']) && $this->userCons[$data['red_send_con']]['uid'] == $data['red_send_id']) {
                    $this->userCons[$data['red_send_con']]['con']->send(json_encode([
                        'type'=>'redbagRecivedNotice',
                        'status'=>true,
                        'red_id'=>$data['red_id'],
                        'friend_id'=>$data['send_id'],
                    ]));
                }
            }
        }
    }
    /**
     * 发送红包给朋友
     */
    private function sendRedBagToFriend($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        if ($result['status']) {
            if ($data['redType'] == 1) {
                $connection->send(json_encode([
                    'status'=>true,
                    'red_id'=>$result['red_id']
                ]));
                if ($data['fcon_id']) {
                    if (isset($this->userCons[$data['fcon_id']]['uid']) && $this->userCons[$data['fcon_id']]['uid'] == $data['get_id']) {
                        $this->userCons[$data['fcon_id']]['con']->send(json_encode([
                            'type'=>'text',
                            'data'=>[
                                'msg'=>$result['send_info'],
                                'userInfo'=>$result['userInfo']
                            ],
                        ]));
                    }
                }
            } else {
                $connection->send(json_encode([
                    'status'=>true,
                    'red_id'=>$result['red_id'],
                    'msg'=>$result['send_info']
                ]));
                if (!empty($this->userCons)) {
                    foreach ($this->userCons as $k => $vo) {
                        if (isset($vo['rType']) && $vo['rType'] && isset($vo['con']) && $vo['con'] && $vo['con']!=$connection) {
                            $vo['con']->send(json_encode([
                                'type'=>'ChatRoomMsg',
                                'data'=>[
                                    'msg'=>$result['send_info'],
                                    'userInfo'=>$result['userInfo']
                                ],
                            ]));
                        }
                    }
                }
            }
        } else {
            $connection->send(json_encode([
                'status'=>false,
                'errMsg'=>$result['errMsg']
            ]));
        }
    }
    /**
     * 修改交易密码
     */
    private function changeTrading($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        // dump($result);
        $connection->send(json_encode($result));
    }
    /**
     * 删除陌生人
     */
    private function delStrange($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        $connection->send(json_encode([
            'status'=>$result,
        ]));
    }
    /**
     * 修改系统消息的阅读状态
     */
    private function changeSystemReadStatus($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        $connection->send(json_encode([
            'status'=>$result,
            'type'=>'ResponeChangeSystemReadStatus'
        ]));
    }
    /**
     * 同意或者拒绝好友申请
     */
    private function AgreeFriendRequest($connection, $data)
    {
        dump("I am comming the AgreeFriendRequest\n");
        $result = $this->dataProcer->mainProcer($data);
        if (!$result) {
            dump("It is fail to operate the Request of addFriend!\n");
            // 操作失败，
            $connection->send(json_encode([
                'type'=>"ResponeAgreeFriendRequest",
                'result'=>false
            ]));
        } else {
            dump("It is OK!\n");
            // 操作成功
            // 返回消息
            $connection->send(json_encode([
                'type'=>'ResponeAgreeFriendRequest',
                'result'=>true,
                'msg'=>$result['user_data']
            ]));
            if ($data['fcon_id']) {
                // 判断对法是否上线
                if (isset($this->userCons[$data['fcon_id']]['uid']) && $this->userCons[$data['fcon_id']]['uid'] == $data['friend_id']) {
                    if ($data['msg_status']==6) {
                        // 同意好友申请，发送好友信息
                        $this->userCons[$data['fcon_id']]['con']->send(json_encode([
                            'type'=>'PassFriendRequest',
                            'data'=>$result['friend_data']
                        ]));
                    } else {
                        // 拒绝好友申请，发送拒绝提醒
                        $this->userCons[$data['fcon_id']]['con']->send(json_encode([
                            'type'=>'RefuseFriendRequest',
                            'msg'=>$result['friend_data']
                        ]));
                    }
                }
            }
        }
    }

    /**
     * 删除消息
     */
    private function DeleteMessage($connection, $data)
    {
        if ($connection) {
            $result = $this->dataProcer->mainProcer($data);
            $connection->send(json_encode([
                'type'=>'ResopneDeleteMessage',
                'result'=>$result
            ]));
        }
    }
    /**
     * 删除好友
     */
    private function deleteFriend($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        if (!$result) {
            // 删除失败
            $connection->send(json_encode([
                'result'=>false,
            ]));
        } else {
            // 删除成功
            $connection->send(json_encode([
                'result'=>true,
            ]));
            if ($data['fcon_id']) {
                // 判断对方是否在线
                if (isset($this->userCons[$data['fcon_id']]['uid']) && $this->userCons[$data['fcon_id']]['uid'] == $data['friend_id']) {
                    // 向对方发送删除好友提示消息
                    $this->userCons[$data['fcon_id']]['con']->send(json_encode([
                        'type'=>'deleteFriendNotice',
                        'msg'=>$result
                    ]));
                }
            }
        }
    }
    /**
     * 下线通知
     */
    private function downLine($connection, $data)
    {
        $friendIds = $this->dataProcer->mainProcer($data);
        if (!empty($friendIds)) {
            foreach ($friendIds as $key => $vo) {
                foreach ($vo as $k => $v) {
                    // 下线通知
                    if (isset($this->userCons[$v['connectionID']]['uid']) && $this->userCons[$v['connectionID']]['uid'] == $v['id']) {
                        $noticeInfo = [
                            'type'=>'onlineNotice',
                            'msg'=>$this->userCons[$v['connectionID']]['nickname'].'已经下线',
                            'fType'=> $key,
                            'index'=>$data['send_id'],
                            'status'=>0,
                            'connectionID'=>'',
                        ];
                        $this->userCons[$v['connectionID']]['con']->send(json_encode($noticeInfo));
                    }
                }
            }
        }
    }
    /**
     * 添加好友
     */
    private function addFriend($connection, $data)
    {
        dump(date('Y-m-d H:i:s').": user(".$data['send_id'].") send a request of addFriend to user(".$data['get_id'].")\n");
        $result = $this->dataProcer->mainProcer($data);
        if ($result) {
            if ($data['fcon_id']) {
                // 判断对方是否在线
                if (isset($this->userCons[$data['fcon_id']]['uid']) && $this->userCons[$data['fcon_id']]['uid'] == $data['get_id']) {
                    if (isset($result['get_info'])) {
                        // 对方没有开启好友验证，添加好友成功，向对方发送添加人的信息以及添加成功的好友消息
                        $this->userCons[$data['fcon_id']]['con']->send(json_encode([
                            'type'=>'addFriendRequest',
                            'fInfo'=>$result['send_info'],
                            'msg'=>$result['data']
                        ]));
                    } else {
                        $result['send_info']['content'] = $result['data']['content'];
                        $result['send_info']['flag'] = $result['data']['flag'];
                        $result['send_info']['status'] = $result['data']['status'];
                        $result['send_info']['mid'] = $result['data']['mid'];
                        // 对方开启了好友验证，向对方发送好友申请请求
                        $this->userCons[$data['fcon_id']]['con']->send(json_encode([
                            'type'=>'addFriendRequest',
                            'fInfo'=>'',
                            'msg'=>$result['send_info']
                        ]));
                    }
                }
            }

            // 判断是否添加好友成功
            if (isset($result['get_info'])) {
                // 添加好友成功，返回好友信息，以及添加成功提示消息
                $connection->send(json_encode([
                    'fInfo'=>$result['get_info'],
                    'msg'=>$result['data'],
                    'type'=> "ResoneAddFriendRequest"
                ]));
            } else {
                // 对法开启验证，返回提示信息
                $result['data']['content'] = "对方开启了好友验证，等待对方通过验证！";
                $connection->send(json_encode([
                    'fInfo'=>'',
                    'msg'=>$result['data'],
                    'type'=> "ResoneAddFriendRequest"
                ]));
            }
        } else {
            // 请求信息保存失败
            $connection->send(json_encode([
                'fInfo'=>'',
                'msg'=> false,
                'type'=> "ResoneAddFriendRequest"
            ]));
        }
    }
    /**
     * 查找好友
     */
    private function searchFrinds($connection, $data)
    {
        $result = $this->dataProcer->mainProcer($data);
        $connection->send(json_encode([
            'type'=>'searchFriendsResult',
            'result'=>$result
        ]));
    }
    /**
     * 握手处理
     */
    private function handle($connection, $data)
    {
        $data['con_id'] = $connection->id;
        // 判断是否存在用户id
        if ($data['send_id']) {
            // 获取好友信息和未读消息
            $result = $this->dataProcer->mainProcer($data);
            $connection->send(json_encode([
                'msg'=>'ok',
                'type'=>'handle',
                'role'=>0,
                'send_id'=>'',
                'userMsgs'=>$result['userMsg'],
                'friendsList'=>$result['friendList'],
                'con_id'=>$data['con_id'],
            ]));
            // 向在线好友发送上线通知
            foreach ($result['friendList'] as $key => $vo) {
                foreach ($vo as $k => $v) {
                    if ($v['connectionID'] && isset($this->userCons[$v['connectionID']]['uid']) && $this->userCons[$v['connectionID']]['uid'] == $v['id']) {
                        $noticeInfo = [
                            'type'=>'onlineNotice',
                            'msg'=>$data['nickname'].'已经上线',
                            'fType'=> $key, //好友类型
                            'index'=>$data['send_id'], //好友id
                            'status'=>1, //好友在线状态
                            'connectionID'=>$data['con_id'], //好友链接标识符
                        ];
                        $this->userCons[$v['connectionID']]['con']->send(json_encode($noticeInfo));
                    }
                }
            }
            // 判断用户是否进入聊天室 rType为0时表示聊天室的用户； 功能：向聊天室广播用户进来的消息，需要时去掉注释
            // if (!empty($this->userCons) && !$data['rType']) {
            //     // 向聊天室的用户广播消息
            //     $message = json_encode([
            //         'msg'=>"欢迎".$data['nickname']."进入房间！",
            //         'type'=>'text',
            //         'role'=>0,
            //         'send_id'=>''
            //     ]);
            //     foreach ($this->userCons as $k => $con) {
            //         if ($con['con'] && !$con['rType']) {
            //             $con['con']->send($message);
            //         }
            //     }
            // }
            // 保存用户的链接资源到资源池
            $this->userCons[$data['con_id']]['con'] = $connection;
            $this->userCons[$data['con_id']]['uid'] = $data['send_id']; //用户id
            $this->userCons[$data['con_id']]['rType'] = $data['rType']; // 链接类型：0表示聊天室，1表示个人链接
            $this->userCons[$data['con_id']]['nickname'] = $data['nickname']; // 用户昵称
            $this->userCons[$data['con_id']]['lasttime'] = time(); //最后活动时间

            // dump("=====================用户资源信息=======================\n");
            // dump("connectionID:".$connection->id."\n");
            // dump("data-connectionID:".$data['con_id']."\n");
            // dump("uid:".$this->userCons[$data['con_id']]['uid']."\n");
            // dump("=====================用户资源信息=======================\n");
            dump($this->iconv("user[(".$data['send_id'].")".$data['nickname']."] is comming\n"));
        } else {
            dump($this->iconv(date("Y-m-d H:i:s")."有非法用户进入\n"));
            $connection->send(json_encode([
                'type'=>'handle',
                'msg'=>'非法用户！',
                'role'=>0,
                'send_id'=>-1,
                'userMsgs'=>'',
                'friendsList'=>''
            ]));
        }
    }

    /**
     * 客户端发送过来的关闭socket请求
     */
    private function closeSocket($data)
    {
        // 关闭资源。
        // $this->userCons[$data['send_id']]['con']->close();
        // // 判断用户是否在聊天室
        // if (!$this->userCons[$data['send_id']]['rType'] && !empty($this->userCons)) {
        //     $message = json_encode([
        //          'msg'=>"用户".$data['nickname']."离开了房间！",
        //          'type'=>'text',
        //          'role'=>0,
        //          'send_id'=>''
        //      ]);
        //     // 向聊天室的人广播离开的消息
        //     foreach ($this->userCons as $k => $con) {
        //         if ($con['con'] && !$con['rType'] && $k != $data['send_id']) {
        //             $con['con']->send(json_encode($message));
        //         }
        //     }
        // }
        // // 删除当前用户资源信息
        // unset($this->userCons[$data['send_id']]);
        // dump("user[(".$data['send_id'].")".$data['nickname']."] is leaving\n");
    }

    /**
     * onClose 关闭事件回调
     * @param \Workerman\Connection\TcpConnection    $connection
     * @return void
     */
    public function onClose($connection)
    {
        try {
            // dump("[MyWorkerServer->onClose]\n");
            // 删除关闭的客户端
            dump($this->iconv('标识符为“'.$connection->id."”的链接已经关闭\n"));
            if (isset($this->userCons[$connection->id]['con']) && $this->userCons[$connection->id]['con']) {
                // 下线通知
                $noticeInfo = [
                    'send_id'=>$this->userCons[$connection->id]['uid'],
                    'type'=>'downLine',
                    'con_id'=>$connection->id,
                ];
                $this->downLine($connection, $noticeInfo);
                unset($this->userCons[$connection->id]);
            }
            // if (!empty($this->userCons)) {
            // foreach ($this->userCons as $k => $con) {
            //     if (isset($con['con']) && $con['con'] == $connection) {
            //         unset($this->userCons[$k]);
            //         // 如果用户离开聊天室广播到聊天室中则使用这段代码
            //         // if (!$con['rType']) {
            //         //     $message = json_encode([
            //         //                  'msg'=>"用户".$con['nickname']."离开了房间！",
            //         //                  'type'=>'text',
            //         //                  'role'=>0,
            //         //                  'send_id'=>''
            //         //              ]);
            //         //     foreach ($this->userCons as $ccon) {
            //         //         if (!$ccon['rType']) {
            //         //             $ccon['con']->send($message);
            //         //         }
            //         //     }
            //         // }
            //         break;
            //     }
            // }
        // }
        } catch (\Exception $e) {
            dump("onClose->catch::".$e->getMessage());
        }
    }

    /**
     * onError 错误事件回调
     * @param \Workerman\Connection\TcpConnection    $connection
     * @param $code
     * @param $msg
     * @return void
     */
    public function onError($connection, $code, $msg)
    {
        // dump("[MyWorkerServer->onError]\n");
    }

    /**
     * onBufferFull 事件回调
     * @param \Workerman\Connection\TcpConnection    $connection
     * @param $buffer
     * @return void
     */
    public function onBufferFull($connection, $buffer)
    {
        // dump("[MyWorkerServer->onBufferFull]\n");
    }
    /**
     * onBufferDrain 事件回调
     * @param \Workerman\Connection\TcpConnection    $connection
     * @param $buffer
     * @return void
     */
    public function onBufferDrain($connection, $buffer)
    {
        // dump("[MyWorkerServer->onBufferDrain]\n");
    }

    /**
     * onWorkerReload  重启事件回调
     * @param \Workerman\Worker    $worker
     * @return void
     */
    public function onWorkerReload($worker)
    {
        // dump("[MyWorkerServer->onWorkerReload]\n");
    }
    /**
     * 启动
     * @access public
     * @return void
     */
    public function start()
    {
        // dump("[MyWorkerServer->start]\n");
        MyWebServer::runAll();
    }

    /**
     * 停止
     * @access public
     * @return void
     */
    public function stop()
    {
        // dump("[MyWorkerServer->stop]\n");
        // 关闭定时器
        Timer::del($this->timer_handle);
        MyWebServer::stopAll();

        // file_put_contents($_SERVER["DOCUMENT_ROOT"].'/workerman.json', json_encode(['workerStatus'=>false]));
    }

    /**
    * 一对一发送消息
    * @access public
    * @return void
    * @param $connection 发送人的资源链接
    * @param $data 发送的数据
    * @param $oneToOne 默认单对单聊天，关闭单对单则为广播消息
    */
    private function sendMessage($connection, $data)
    {
        try {
            // dump("[MyWorkerServer->sendMessage]\n");
            /*===============后台消息处理，上线时可以注释掉==================*/
            $msg = "user[(".$data['send_id'].")] tall to user[(".$data['get_id'].")]: ".$data['content']."【".date("Y-m-d H:i:s")."】\n";
            
            // 转换编码格式
            $msg = $this->iconv($msg);
       
            // 后台输出消息
            dump("charset:".$encode."\n", $msg);
            /*===============后台消息处理，上线时可以注释掉==================*/

            if ($data['rType']) {
                // 1对1聊天
                $result = $this->dataProcer->mainProcer($data);
                if (!$result) {
                    $connection->send(json_encode([
                    'status'=>false,
                ]));
                } else {
                    $connection->send(json_encode([
                    'status'=>true
                ]));
                    if ($data['fcon_id']) {
                        // dump("+++++++++++++++++++++++++++++++++++\n");
                        // dump($data['fcon_id']);
                        // dump(array_keys($this->userCons));
                        // dump("+++++++++++++++++++++++++++++++++++\n");
                        // 判断对方是否在线
                        if (isset($this->userCons[$data['fcon_id']]['con']) && $this->userCons[$data['fcon_id']]['uid'] == $data['get_id']) {
                            // 向对方推送消息
                            $this->userCons[$data['fcon_id']]['con']->send(json_encode([
                            'type'=>'text',
                            'data'=>$result
                        ]));
                        }
                    }
                }
            } else {
                // 聊天室
                $message = json_encode([
                // ''
            ]);
                // for()
            }
        } catch (\Exception $e) {
            dump("sendMessage->catch::".$e->getMessage());
        }
    }

    private function iconv($msg)
    {
        if (is_array($msg)||is_object($msg)) {
            foreach ($msg as $key => $vo) {
                $msg[$key] = $this->iconv($vo);
            }
        } else {
            // 获取去字符串的编码格式
            $encode = mb_detect_encoding($msg, array("ASCII",'UTF-8','GB2312',"GBK",'BIG5'));
            // 转换编码格式
            $msg = iconv($encode, 'GB2312', $msg);
        }
        return $msg;
    }
}
