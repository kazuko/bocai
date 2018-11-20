<?php
namespace app\Server\controller;

use think\Controller;

class Client extends Controller
{
    public function client($id, $rules)
    {
        $id = $this->checkId($id, $rules);
        dump($id);
        return $this->fetch();
    }

    private function checkId($id, $rules)
    {
        $rule = str_replace('*', '(.*)', $rules);
        if($res = preg_match('/^('.$rule.')$/', (string)$id, $result, PREG_OFFSET_CAPTURE)){
            $prev = '';
            $next = '';
            foreach($result as $key => $vo){
                if($vo[0] != $id && $vo[0]){
                    if($vo[1] == 0){
                        $prev .= $vo[0];
                        $id = str_replace($vo[0], '', $id);
                    }else{
                        $stemp = explode($vo[0], $id);
                        if($stemp[1]){
                            $prev .= $stemp[0].$vo[0];
                            $id = $stemp[1];
                        }else{
                            $next .= $vo[0];
                            $id = str_replace($vo[0], '', $id);
                        }
                    }
                }
            }
            $id += 1;
            $id = $prev.$id.$next;
            $id = $this->checkId($id, $rules);
        }
        return (int)$id;
    }
    public function client1()
    {
        // if(function_exists("popen")){
        //     echo "exist the function POPEN";
        // }else{
        //     echo "unexist the function POPEN";
        // }
        // $fp = popen('H://phpstudy/WWW/bc/application/http/MyWorker.php', 'r');
        // dump(fgetss($fp));
        // pclose($fp);
        // $IP = [
        //     "21.234.234.345",
        //     "234.24.342.234",
        // ];

        // echo json_encode($IP);

        $data = [0,3,4,5,6,7,8,9];
        for ($i=0; $i<3; $i++) {
            dump($data);
            $id = $data[0];
            $data = array_slice($data, 1);
            echo "ID:".$id."<br/>";
        }
        // $list = $this->calculate($data);
        // dump($list);
        return $this->fetch();
    }

    public function calculate($data)
    {
        if (is_array($data)) {
            if (count($data)<3) {
                return false;
            } else {
                $arrchache = [];
                $list = [];
                foreach ($data as $key => $vo) {
                    foreach ($data as $k => $v) {
                        foreach ($data as $ky => $val) {
                            if ($vo < $v) {
                                if ($v<$val) {
                                    $str = $vo.$v.$val;
                                } else {
                                    if ($vo<$val) {
                                        $str = $vo.$val.$v;
                                    } else {
                                        $str = $val.$vo.$v;
                                    }
                                }
                            } else {
                                if ($vo<$val) {
                                    $str = $v.$vo.$val;
                                } else {
                                    if ($v<$val) {
                                        $str = $v.$val.$vo;
                                    } else {
                                        $str = $val.$v.$vo;
                                    }
                                }
                            }
                            if (!in_array($str, $arrchache)) {
                                $list[] = $vo.$v.$val;
                                $arrchache[] = $vo.$v.$val;
                                $arrchache[] = $vo.$val.$v;
                                $arrchache[] = $v.$vo.$val;
                                $arrchache[] = $v.$val.$vo;
                                $arrchache[] = $val.$vo.$v;
                                $arrchache[] = $val.$v.$vo;
                            }
                        }
                    }
                }
                return $list;
            }
        } else {
            return false;
        }
    }
    public function client2()
    {
        $command = "php think MyWorker";
        system($command, $aaa);
        // echo $aaa;
        // if($output = shell_exec($command)){
        //     echo $output;
        // }else{
        //     echo "fail...";
        // }
        // echo 'sss';
        return $this->fetch();
    }

    public function test()
    {
        echo json_encode([
            '1.jpg',
            '2.jpg',
            '3.jpg',
            '4.jpg',
            '5.jpg'
        ]);
        $niuTime = 0;
        $niu1Time = 0;
        $str = '';
        for ($j=0; $j<1000; $j++) {
            for ($i=0; $i<5; $i++) {
                $data[$i] = rand(0, 9);
            }
            $str .= '['.implode('-', $data).']';
            $startTime = microtime();
            $str .= $this->niuniu($data);
            $endTime = microtime();
            $niuTime += ($endTime - $startTime);
            $startTime = microtime();
            $str .= $this->niuniu1($data);
            $endTime = microtime();
            $niu1Time += ($endTime-$startTime);
            $str .= "<hr/>";
        }
        echo "<hr/>方法1(if-else)耗时：$niuTime-秒,方法2(for循环)耗时：$niu1Time-秒<hr/>";
        echo $str;
    }
    public function niuniu($data)
    {
        list($a, $b, $c, $d, $e) = $data;
        if (!(($a + $b + $c)%10)) {
            // echo "<br/>a+b+c=".($a+$b+$c);
            $result = ($d + $e) % 10;
        } elseif (!(($a + $b + $d)%10)) {
            // echo "<br/>a+b+d=".($a+$b+$d);
            // echo "<br/>c=".$c."<br/>e=".$e;
            $result = ($c + $e) % 10;
        } elseif (!(($a + $b + $e)%10)) {
            // echo "<br/>a+b+e=".($a+$b+$e);
            $result = ($c + $d) % 10;
        } elseif (!(($a + $c + $d)%10)) {
            // echo "<br/>a+c+d=".($a+$c+$d);
            $result = ($b + $e) % 10;
        } elseif (!(($a + $c + $e)%10)) {
            // echo "<br/>a+c+e=".($a+$c+$e);
            $result = ($b+ $d) % 10;
        } elseif (!(($a + $d + $e)%10)) {
            // echo "<br/>a+d+e=".($a+$d+$e);
            $result = ($b + $c) % 10;
        } elseif (!(($b + $c + $d)%10)) {
            // echo "<br/>b+c+d=".($b+$c+$d);
            $result = ($a + $e) % 10;
        } elseif (!(($b + $c + $e)%10)) {
            // echo "<br/>b+c+e=".($b+$c+$e);
            $result = ($a + $d) % 10;
        } elseif (!(($b + $d + $e)%10)) {
            // echo "<br/>b+d+e=".($b+$d+$e);
            $result = ($a + $c) % 10;
        } elseif (!(($c + $d + $e)%10)) {
            // echo "<br/>c+d+e=".($c+$d+$e);
            $result = ($a + $d) % 10;
        } else {
            $result = '无牛';
        }
        return "【牛牛（计算结果：".$result."）】";
    }

    public function niuniu1($data)
    {
        $data1 = $data;
        $result = '无牛';
        $str = '[';
        foreach ($data as $k1 => $v1) {
            array_splice($data, 0, 1);
            // $str .= '{'.implode('-',$data).'}';
            $stmp1 = $data;
            if (count($stmp1)) {
                foreach ($stmp1 as $k2 => $v2) {
                    array_splice($stmp1, 0, 1);
                    if ($data) {
                        foreach ($stmp1 as $k3 => $v3) {
                            // $str .= $v1.'-'.$v2.'-'.$v3.'#';
                            if (!(($v1+$v2+$v3)%10)) {
                                $result = ($data1[0]+$data1[1]+$data1[2]+$data1[3]+$data1[4]-$v1-$v2-$v3)%10;
                                break;
                            }
                        }
                    }
                }
            } else {
            }
        }
        $str .= ']';
        return "【牛牛1（计算结果：".$result."）".$str."】";
    }

    public function niuniu2($data)
    {
        // dump(';;;;sd');
        // $array = [[0,9,1],[0,8,2],[0,7,3],[0,6,4],[0,5,5],[1,2,7],[1,3,6],[1,4,5],[2,3,5],[2,4,4],[3,4,3],[9,9,2],[9,8,3],[9,7,4]];
        // $result = '无牛';

        // foreach ($array as $arr) {
        //     echo "-----------key----------<br/>";
        //     dump($arr);
        //     $stmp = $data;
        //     dump($stmp);
        //     $key1 = array_search($arr[0], $stmp);
        //     if ($key1) {
        //         array_splice($stmp, $key1, 1);
        //         echo "===========key1=========<br/>";
        //         dump($stmp);
        //         $key2 = array_search($arr[1], $data);
        //         if ($key2) {
        //             array_splice($stmp, $key2, 1);
        //             echo "++++++++++++++++key2++++++++++++++<br/>";
        //             dump($stmp);
        //             $key3 = array_search($arr[2], $data);
        //             if ($key3) {
        //                 array_splice($stmp, $key3, 1);
        //                 echo "<<<<<<<<<<<<<<key3>>>>>>>>>>>><br/>";
        //                 dump($stmp);
        //                 $result = ($stmp[0]+$data[1])%10;
        //                 break;
        //             }
        //         }
        //     }
        // }
        echo "计算结果：" . $result;
    }

    public function phpInfo()
    {
          //连接本地的 Redis 服务
        // $redis = new \Redis();
        // $redis->connect('127.0.0.1', 6379);
        // echo "Connection to server sucessfully";
        // //存储数据到列表中
        // $redis->lpush("tutorial-list", "Redis");
        // $redis->lpush("tutorial-list", "Mongodb");
        // $redis->lpush("tutorial-list", "Mysql");
        // // 获取存储的数据并输出
        // $arList = $redis->lrange("tutorial-list", 0 ,5);
        // echo "Stored string in redis";
        // print_r($arList);
        phpinfo();
    }

    public function maxtest()
    {
        $n = 50;//条数
        $m = 5;

        $min = 1;
        $max = 11;
        $result = array();
        $data = [
            'firstBall:',
            'secondBall:',
            'thirdBall:',
            'fourthBall:',
            'fifthBall:',
        ];
        for($i=0; $i<$n; $i++){
            for($j=0; $j<$m; $j++){
                $result[$i][$j] = rand($min, $max);
                $data[$j] .= $result[$i][$j].'-';
            }
        }
        dump($data);
        echo "<hr>";
        $max = $this->maxOut($result);
        dump($max);
    }

    public function maxOut($resultArr)
    {
        $elements = array(1,2,3,4,5,6,7,8,9,10,11);
        $max = array(
            'first'=>array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0),
            'second'=>array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0),
            'third'=>array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0),
            'fourth'=>array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0),
            'fifth'=>array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0),
        );
        $temp = [
            'first' => [1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0],
            'second' => [1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0],
            'third' => [1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0],
            'fourth' => [1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0],
            'fifth' => [1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0],
        ];        
        $index = ['first', 'second', 'third', 'fourth', 'fifth'];
        foreach ($resultArr as $key => $vo) {
            foreach ($vo as $ky => $o) {
                // dump($first);
                $idex = $index[$ky];
                // dump($$idex);
                foreach ($temp[$idex] as $k => $v) {
                    if ($o == $k) {
                        $temp[$idex][$k]++;
                        if ($max[$idex][$k] < $temp[$idex][$k]) {
                            $max[$idex][$k] = $temp[$idex][$k];
                        }
                    } else {
                        $temp[$idex][$k] = 0;
                    }
                }
            }
        }
        return $max;
    }
}
