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
namespace app\Server\controller;

use Workerman\Worker as MyWebServer;
use think\worker\Server;
use think\console\Output;
use Workerman\Lib\Timer;

use app\Server\controller\DataProcer;
use app\Server\controller\Deal;

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


    // protected $gameProcer; // 游戏处理

    // protected $dataProcer;
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

        define("EXPIRE_TIME", 100); //红包和转账过期时间

        // header("Content-Type: text/html;charset=GB2312");
        $this->host = $host;
        $this->port = $port;
        // 初始化心跳间隔为30秒
        $this->time_interval = 30;
        // 初始化定时器句柄
        $this->timer_handle = null;
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
                    // $giveBackInfo = $this->dataProcer->getGiveBackInfo();
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
                                        // 'login_status'=>1,
                                    ];
                                    Deal::downLine($con['con'], $noticeInfo, $this->userCons);
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
                        
                        // 在线时长统计（奖励积分或者金币）
                        // if(isset($con['online'])){
                            
                        // }
                    }
                }
            } catch (\Exception $e) {
                dump("onWorkerStart->catch::".$e->getMessage()."; line:".$e->getLine());
            }
        });

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
        // dump("Recive:".$data."\n");
        try {
            // dump("[MyWorkerServer->onMessage]\n");
            echo $data."\n";
            // $data = str_replace('\\', '/', $data);
            // $data = str_replace('"', "'", $data);
            
            $data = json_decode($data, true);
            echo "接受的消息:\n";
            dump(Deal::iconv($data));
            
            switch ($data['type']) {
                case "handle":
                    $conInfo = Deal::handle($connection, $data, $this->userCons);
                    if ($conInfo) {
                        dump($conInfo['con_id']);
                        $this->userCons[$conInfo['con_id']] = $conInfo['conInfo'];
                    }
                    break;
                case "text":
                    Deal::sendMessage($connection, $data, $this->userCons);
                    break;
                case "img":
                    Deal::sendImg($connection, $data, $this->userCons);
                    break;
                case "close":
                    Deal::closeSocket($data, $this->userCons);
                    break;
                case "searchFriends":
                    Deal::searchFrinds($connection, $data, $this->userCons);
                    break;
                case "addFriendRequest":
                    Deal::addFriend($connection, $data, $this->userCons);
                    break;
                case "downLine":
                    $data['login_status'] = 0;
                    Deal::downLine($connection, $data, $this->userCons);
                    break;
                case "deleteFriend":
                    Deal::deleteFriend($connection, $data, $this->userCons);
                    break;
                case "DeleteMessage":
                    Deal::DeleteMessage($connection, $data, $this->userCons);
                    break;
                case "AgreeFriendRequest":
                    Deal::AgreeFriendRequest($connection, $data, $this->userCons);
                    break;
                case "changeSystemReadStatus":
                    Deal::changeSystemReadStatus($connection, $data, $this->userCons);
                    break;
                case "delStrange":
                    Deal::delStrange($connection, $data, $this->userCons);
                    break;
                case "changeTrading":
                    Deal::changeTrading($connection, $data, $this->userCons);
                    break;
                case "sendRedBagToFriend":
                    Deal::sendRedBagToFriend($connection, $data, $this->userCons);
                    break;
                case "reciveRedBagFromFriend":
                    Deal::reciveRedBagFromFriend($connection, $data, $this->userCons);
                    break;
                case "getAppInfo":
                    Deal::getAppInfo($connection, $data, $this->userCons);
                    break;
                case "logining":
                    Deal::logining($connection, $data, $this->userCons);
                    break;
                case "RedDetail":
                    Deal::RedDetail($connection, $data, $this->userCons);
                    break;
                case "storageGold":
                    Deal::storageGold($connection, $data, $this->userCons);
                    break;
                case "getGoldFromBank":
                    Deal::getGoldFromBank($connection, $data, $this->userCons);
                    break;
                case "settingChange":
                    Deal::settingChange($connection, $data, $this->userCons);
                    break;
                case "changeNickName":
                    Deal::changeNickName($connection, $data, $this->userCons);
                    break;
                case "changeSignature":
                    Deal::changeSignature($connection, $data, $this->userCons);
                    break;
                case "changeLoginPassword":
                    Deal::changeLoginPassword($connection, $data, $this->userCons);
                    break;
                case "getSendRedList":
                    Deal::getSendRedList($connection, $data, $this->userCons);
                    break;
                case "getReciveRedList":
                    Deal::getReciveRedList($connection, $data, $this->userCons);
                    break;
                case "getThemeList":
                    Deal::getThemeList($connection, $data, $this->userCons);
                    break;
                case "getPostList":
                    Deal::getPostList($connection, $data, $this->userCons);
                    break;
                case "getComment":
                    Deal::getComment($connection, $data, $this->userCons);
                    break;
                case "themeComment":
                    Deal::themeComment($connection, $data, $this->userCons);
                    break;
                case "zanComment":
                    Deal::zanComment($connection, $data, $this->userCons);
                    break;
                case "ReplyComment":
                    Deal::ReplyComment($connection, $data, $this->userCons);
                    break;
                case "postTheme":
                    Deal::postTheme($connection, $data, $this->userCons);
                    break;
                case "getMyThemeList":
                    Deal::getMyThemeList($connection, $data, $this->userCons);
                    break;
                case "getMyReplyList":
                    Deal::getMyReplyList($connection, $data, $this->userCons);
                    break;
                case "transferAccounts":
                    Deal::transferAccounts($connection, $data, $this->userCons);
                    break;
                case "getTransferInfo":
                    Deal::getTransferInfo($connection, $data, $this->userCons);
                    break;
                case "reciveTransfer":
                    Deal::reciveTransfer($connection, $data, $this->userCons);
                    break;
                case "getMedalList":
                    Deal::getMedalList($connection, $data, $this->userCons);
                    break;
                case "changeMedalStatus":
                    Deal::changeMedalStatus($connection, $data, $this->userCons);
                    break;
                case "sendRadio":
                    Deal::sendRadio($connection, $data, $this->userCons);
                    break;
                case "getRegisterRules":
                    Deal::getRegisterRules($connection, $data, $this->userCons);
                    break;
                case "getYanZhengMa":
                    Deal::getYanZhengMa($connection, $data, $this->userCons);
                    break;
                case "registerMember":
                    Deal::registerMember($connection, $data, $this->userCons);
                    break;
                case "getChatRoomMsgList":
                    Deal::getChatRoomMsgList($connection, $data, $this->userCons);
                    // no break
                case "comimgToChatRoom":
                    dump($data['con_id']);
                    dump($this->userCons[$data['con_id']]['rType']);
                    $this->userCons[$data['con_id']]['rType'] = true;
                    break;
                case "ChatRoomMsg":
                    Deal::ChatRoomMsg($connection, $data, $this->userCons);
                    break;
                case "leaveChatRoom":
                    Deal::leaveChatRoom($connection, $data, $this->userCons);
                    break;
                case "getChatRecords":
                    Deal::getChatRecords($connection, $data, $this->userCons);
                    break;
                case "giveBackRedbag":
                    Deal::giveBackRedbag($connection, $data, $this->userCons);
                    break;
                case "chatRoomRedbag":
                    Deal::chatRoomRedbag($connection, $data, $this->userCons);
                    break;
                case "giveBackTransfer":
                    Deal::giveBackTransfer($connection, $data, $this->userCons);
                    break;
                case "getPostRules":
                    Deal::getPostRules($connection, $data, $this->userCons);
                    break;
                case "readStatus":
                    DataProcer::updateMsgReadStatus($data);
                    break;
                case "allowTimesDec":
                    DataProcer::allowTimesDec($data);
                    break;
            }
            if (isset($this->userCons[$data['con_id']]['con']) && $this->userCons[$data['con_id']]['con']) {
                // 保存用户最后活动的时间
                $this->userCons[$data['con_id']]['lasttime'] = time();
            }
        } catch (\Exception $e) {
            dump("onmessage->catch::".$e->getMessage()."; line:".$e->getLine());
        }
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
            dump(Deal::iconv('标识符为“'.$connection->id."”的链接已经关闭\n"));
            if (isset($this->userCons[$connection->id]['con']) && $this->userCons[$connection->id]['con']) {
                // 下线通知
                $noticeInfo = [
                    'send_id'=>$this->userCons[$connection->id]['uid'],
                    'type'=>'downLine',
                    'con_id'=>$connection->id,
                    // 'login_status'=>1,
                ];
                // dump($this->iconv("下线通知"));
                // dump($noticeInfo);
                Deal::downLine($connection, $noticeInfo, $this->userCons);
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
            dump("onClose->catch::".$e->getMessage()."; line:".$e->getLine());
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
        echo Deal::iconv($code.":".$msg."\n");
    }

    /**
     * onBufferFull 事件回调
     * @param \Workerman\Connection\TcpConnection    $connection
     * @param $buffer
     * @return void
     */
    // public function onBufferFull($connection, $buffer)
    // {
    //     // dump("[MyWorkerServer->onBufferFull]\n");
    // }
    /**
     * onBufferDrain 事件回调
     * @param \Workerman\Connection\TcpConnection    $connection
     * @param $buffer
     * @return void
     */
    // public function onBufferDrain($connection, $buffer)
    // {
    //     // dump("[MyWorkerServer->onBufferDrain]\n");
    // }

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
}
