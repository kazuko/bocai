<?php
namespace redis;
      //链接redis服务
      $redis = new \Redis();

      $redis->pconnect('127.0.0.1', 6379);
      //开通管道 相当于延迟吧
      echo time() . "\n";
      //测试管道
      $pipe = $redis->multi(\Redis::PIPELINE);
      for ($i = 0; $i < 10000000; $i++) {
      $key = "key::{$i}";
      $pipe->set($key, $i);
      }
      $pipe->exec();
      echo time() . "\n";
      //开通管道 相当于延迟吧
