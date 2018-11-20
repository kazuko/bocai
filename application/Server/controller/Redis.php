<?php
namespace app\Server\controller;

use think\facade\Config;

class Redis
{
    public static function Init()
    {
        $redisConfig = Config::get('worker.redis');
        
        $redis = new \Redis();
        $redis->connect($redisConfig['host'], $redisConfig['port']);
        return $redis;
    }

    public static function set($arr){
        if(!is_array($arr)){
            return false;
        }
        $redis = self::Init();
        $res = [];
        foreach($arr as $key => $val){
            $res[$key] = $redis->set($key, $val);
        }
        return $res;
    }

    public static function setex($arr){
        if(!is_array($arr)){
            return false;
        }
        $redis = self::Init();
        $res = [];
        foreach($arr as $key => $val){
            if(is_array($val)){
                dump("setex...............");
                echo $val[0]."-".$val[1]."-".$val[2]."\n";
                $res[$key] = $redis->setex($val[0], $val[1], $val[2]);
            }else{
                dump("set...............");
                $res[$key] = $redis->set($key, $val);
            }
        }
        return $res;
    }

    public static function get($index = null)
    {
        if ($index === null || ((is_array($index) || is_object($index)) && empty($index))) {
            return false;
        }
        $redis = self::Init();
        if(is_array($index) || is_object($index)){
            $res = [];
            foreach($index as $key => $vo){
                $res[$key] = $redis->get($vo);
            }
            return $res;
        }else{
            return $redis->get($index);
        }
    }

    public static function del($index = null)
    {
        if($index === null || ((is_array($index) || is_object($index)) && empty($index))){
            return false;
        }
        $redis = self::Init();
        if(is_array($index) || is_object($index)){
            $res = [];
            foreach($index as $key => $vo){
                $res[$key] = $redis->del($vo);
            }
            return $res;
        }else{
            return $redis->del($index);
        }
    }
}
