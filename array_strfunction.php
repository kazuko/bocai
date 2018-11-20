<?php
$valueArr = [
    '1', '2','3','4','5'
];
$keyValue = [
    "a"=>'1', "b"=>'2',"c"=>'3',"d"=>'4',"e"=>'5'
];
echo "valueArr->\n";
    var_dump(key($valueArr));
echo "\n";
echo "keyValue->\n";
    var_dump(key($keyValue));
echo "\n";
echo "current->\n";
var_dump(next($keyValue));
var_dump(next($keyValue));
var_dump(key($keyValue));
echo "\n";
$barr = list($a,$b,$c) = array('1','2','3');

echo $a.$b.$c;
shuffle($valueArr);
print_r($valueArr);
shuffle($keyValue);
print_r($keyValue);
$a=array("hello"=>"Dog",1=>"Cat",2=>"Horse");
print_r(array_flip($a));

$a = 'ball_1';
echo explode("_",$a)[1];
