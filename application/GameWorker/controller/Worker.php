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
namespace app\GameWorker\controller;

use Workerman\Worker as WorkerServer;
use think\worker\Server;
use Workerman\lib\Timer;
/*
* worker onworkerstart:创建时钟函数检测游戏开启状态
*        onmessage:接受前端穿过来的数据并且传给active.php处理
*        onclose:关闭连接卸掉对应的链接与时间标志
*        onconnect:初始化链接与时间标志并且告知active.php处理新的链接
*/

/**
 * Worker 命令行服务类
 */
class Worker extends Server
{
    protected $user = array();
    protected $active;
    protected $sanGong;
    protected $Gd11x5;
    /**
     * 架构函数
     * @access public
     * @param  string $host 监听地址
     * @param  int    $port 监听端口
     * @param  array  $context 参数
     */
    public function __construct($host, $port, $context = [])
    {
        $this->worker = new WorkerServer('websocket://' . $host . ':' . $port, $context);
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


    /*
     * onMessage 事件回调
     * @access public
     * @param  \Workerman\Connection\TcpConnection    $connection
     * @param  mixed                                  $data
     * @return vpopmail_del_domain(domain)
     */
    public function onMessage($connection, $data)
    {
        /**
        *enterRoom
        */
        echo "-----onmessage-----json.data-----\n";
        file_put_contents("./user.json", $data);
        // echo $data."\n";
        $data = json_decode($data,true);
        var_dump($data);
        echo "-----array.data-----\n";
        echo "\n";
        /*
            1 ping维持心跳
            2 业务代码
        */
        if($data['type'] == "ping" ){
            $this->user[$connection->id]['time'] = time();
        }else if (isset($data['game'])){
            switch ($data['game']) {
                case 'gd':
                    $this->Gd11x5->enter($connection,$data);//广东十一选五
                break;
                case 'Bjpk10':
                    $this->BjPK10->enter($connection,$data);//北京pk10
                break;
                case 'Cqssc':
                    $this->Cqssc->enter($connection,$data);//重庆时时彩
                    break;   
                case 'SanGong':
                    $this->SanGong->enter($connection,$data);   
                    break;          
                default://六合彩,江苏快三
                    $this->active->enter($connection,$data);//统一规格游戏
                    break;
            }
        }   
    }

    /*
    * 初始化链接及心跳时间
    */
    public function onConnect($connection)
    {     
        echo "-----onconnect-----".$connection->id."\n";
        $this->user[$connection->id]['connection'] = $connection;
        $this->user[$connection->id]['time'] = time();
    }                                       

    public function onclose($connection){
        /**
         * 只卸载用户连接信息
         * 最好弄成内部函数删除protected变量的形式吧
         *  */
        unset($this->user[$connection->id]);

        if(isset($this->active->connection["six"][$connection->id])){
            unset($this->active->connection["six"][$connection->id]);
        }
        else if(isset($this->active->connection["jsks"][$connection->id])){
            unset($this->active->connection["jsks"][$connection->id]);
        }
        else if(isset($this->active->connection["bjpk10"][$connection->id])){
            unset($this->active->connection["bjpk10"][$connection->id]);
        }
        else if(isset($this->BjPK10->connection[$connection->id])){
            unset($this->BjPK10->connection[$connection->id]);
        }
        else if(isset($this->Cqssc->connection[$connection->id])){
            unset($this->Cqssc->connection[$connection->id]);
        }
        else if(isset($this->Gd11x5->connection[$connection->id])){
            unset($this->Gd11x5->connection[$connection->id]);
        }
        // 三公设置的是enterDesk保存链接信息
        else if(isset($this->SanGong->connection[$connection->id])){
                $data = [
                    "type"=>"loseConnection",
                ];
                $this->SanGong->enter($connection,$data);
        }
        echo $connection->id."  has been closed\n";
    }

    //worker服务器启动时触发的函数
    public function onWorkerStart(){
        $this->active = new Active();//接口游戏处理类
        $this->Gd11x5 =  new Gd11x5();
        $this->SanGong = new SanGong();
        $this->SanGong->onWorkerStart();//开启三公游戏

        $this->Gd11x5->gameStatus();//开启广东十一选五游戏检测 游戏流程与其它数字彩票一致，区别:里面采用了redis缓存记录信息
        $this->active->gameStatus();//接口游戏开启检测->包含游戏:六合彩，江苏快三
        // 定时检测心跳
        $heart_interval = Timer::add(10, function()
        {  
            // 更新用户的心跳时间
            if(!empty($this->user)){
                foreach($this->user as $conId => $vo){
                    if($vo['time'] < time()){
                        // $vo['connection']->close();
                    }
                    else{
                        $vo['connection']->send('{"code":"pong"}');
                    }   

                }
                //输出还在线的用户
                foreach($this->user as $conId => $vo){
                    if(isset($vo['connection'])){
                        // $vo->send('online');
                        echo $conId."-----is online\n";
                    }
                }
            }
            else{
                echo "heart:no user online\n";
            }

        });

    }


    /*
     * 启动
     * @access public
     * @return void
     */
    public function start()
    {
        WorkerServer::runAll();
    }

    /**
     * 停止
     * @access public
     * @return void
     */
    public function stop()
    {
        WorkerServer::stopAll();
    }
}
