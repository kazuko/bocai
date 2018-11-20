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
namespace app\Sangong\controller;
use app\Sangong\controller\Index;
/**
 * Worker 命令行服务类
 */
class Abnormal
{
    protected $Index;
    protected $time = 10;//心跳时间间隔
    public function __construct(){
        //启动服务器的时候同时实例化Abnormal类与Index类可能会省点资源  
        $this->Index = new Index();
    }

    //加入房间
    public function enterRoom(){
        $result = $this->Index->enterRoom();
        return $result;
    }

    //加入牌桌
    public function enterDesk($data){
        $result = $this->Index->enterDesk($data);
        return $result;
    }

    //开始游戏   
    public function play($data){
        
    }
}