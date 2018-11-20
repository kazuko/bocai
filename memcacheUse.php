<?php
    class memcacheUse{
        /**
         * memcache 常用函数
         */
        protected $mem;
        function __construct(){
            $this->mem = new \Memcache;
            $this->mem->connect('127.0.0.1',11211);
        }

        

        //memcache可以直接保存数组变量，redis需要字符串化(serialize或者json)
        function array(){
            // 保存变量
            $paramStr = "string";
            $this->mem->set("string",$paramStr);
            echo $this->mem->get("string")."\n";
            $paramArr = [
                "a"=>"a",
                "b"=>"b",
            ];
            $this->mem->set("array",$paramArr);
            var_dump($this->mem->get("array"));
        }
    }
    $memcache = new memcacheUse();
    $memcache->common();