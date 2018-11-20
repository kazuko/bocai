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

/**
 * Worker 命令行服务类
 */
class SecondServer extends Server
{
    protected $output;
    protected $app;
    protected $host,$port;
    protected $userCons;
    /**
     * 架构函数
     * @access public
     * @param  string $host 监听地址
     * @param  int    $port 监听端口
     * @param  array  $context 参数
     */
    public function __construct($host, $port, $context = [])
    {
        $this->host = $host;
        $this->port = $port;
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
        // 设置参数
        if (!empty($option)) {
            foreach ($option as $key => $val) {
                $this->worker->$key = $val;
            }
        }
    }

    /**
     * onWorkerStart 事件回调
     * @access public
     * @param  \Workerman\Worker    $worker
     * @return void
     */
    public function onWorkerStart($worker)
    {
        // windows下开启处理服务器，输出提示
        if (DIRECTORY_SEPARATOR == '\\') {
            $this->output->writeln("WebSocket has started: <http://{$this->host}:{$this->port}>");
            $this->output->writeln('You can exit with <info>`CTRL-C`</info>');
        }
        $this->output->writeln('listening...');
        // dump($worker);
    }

    /**
     * onMessage 事件回调
     * @access public
     * @param  \Workerman\Connection\TcpConnection    $connection
     * @param  mixed                                  $data
     * @return void
     */
    public function onMessage($connection, $data)
    {
        // echo "recive:".$data->getMessage()."\n";
        // echo "recive:".$data."\n";
        $data = json_decode($data,true);
        if(isset($data['handle'])&&$data['handle']){
            if(!isset($this->userCons[$data['me']])){
                $message = "欢迎".$data['me']."进入房间！";
                if(!empty($this->userCons)){
                    foreach($this->userCons as $k => $con){
                        if($con){
                            $con->send($message);
                        }
                    }
                }
                echo "user[".$data['me']."] is comming\n";
                $this->userCons[$data['me']] = $connection;
            }
        }else{
            echo "user[".$data['me']."] tall to user[".$data['toUser']."]: ".$data['text']."       ".date("Y-m-d H:i:s")."\n";

            if(isset($this->userCons[$data['toUser']])){
                $message = $data['text'].":".$data['me'];
                $connection = $this->userCons[$data['toUser']];
            }else{
                $message = $data['toUser']."不在线哦!";
            }
            $connection->send($message);
        }
    }
    
    /**
     * 启动
     * @access public
     * @return void
     */
    public function start()
    {
        MyWebServer::runAll();
    }

    /**
     * 停止
     * @access public
     * @return void
     */
    public function stop()
    {
        MyWebServer::stopAll();
    }
}
