<?php
class Redisuse
{
    public function __construct()
    {
        //链接redis服务
        $this->redis = new \Redis();
        $this->redis->pconnect('127.0.0.1', 6379);
    }

    /**
     * 普通函数
     * string, has string, list string, sort string, zsort string
     * 字符创   字符串哈希   字符串列表    字符串集合   有序集合字符串
     *  */
    public function common()
    {

        //清除当前数据库中的数据
        var_dump($this->redis->keys('*'));
        $this->redis->flushdb();
        //清除所有数据库中的数据
        //   $this->redis->flushall();
    }

    //redis只能保存字符串(serialize 或者 json)
    function array() {
        //保存数组
        $array = [
            'a' => 'a',
            'b' => 'b',
        ];
        //序列化
        $serialize = serialize($array);
        $redis->set("serialize", $serialize);
        //解除序列化
        var_dump(unserialize($redis->get('serialize')));

        //json化
        $json = json_encode($array);
        $redis->set('json', $json);
        var_dump(json_decode($json, true));
    }

    //管道技术
    public function pipe()
    {
        //开通管道 相当于延迟吧
        echo time() . "\n";

        //测试管道
        $pipe = $this->redis->multi(\Redis::PIPELINE);
        for ($i = 0; $i < 100000; $i++) {
            $key = "key::{$i}";
            $pipe->set($key, $i);
        }
        $pipe->exec();
        echo time() . "\n";
    }

    //事物操作
    public function multi()
    {
        //开启事务
        $this->redis->multi()
            ->hset('a', 'a', 1)
            ->hset('a', 'a1', 1)
            ->hset('a', 'a2', 1)
            ->hset('a', 'a3', 1)
            ->hset('a', 'a4', 1)
            ->hset('a', 'a5', 1)
            ->hset('a', 'a6', 1)
            ->hset('a', 'a7', 1)
            ->hset('a', 'a8', 1)
            ->exec();
        var_dump($this->redis->keys("*"));
    }

}
$redis = new Redisuse;

$redis->multi();
