<?php
    $mem = new Memcache();
    if(!$mem->connect("192.168.2.100",11211)){
        die('连接失败!');
    }
    $mem->set('key2','this is test');
    $re = $mem->get('key2');
    $mem->set('key1','this is test2',NUll,5);
    $re = $mem->get ('key1');
    echo $re;
    $re = $mem->get('key1');
    $mem->flush();
    echo $re;
    $array = [
        'a'=>'stringa',
        'b'=>'stringb'
    ];
    $mem->set('array',$array);
    var_dump($mem->get('array'));