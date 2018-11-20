<?php
$action=$_REQUEST['action'];
$tel=$_REQUEST['tel'];
$yzm=$_REQUEST['yzm'];

function randomkeys($length){
    $pattern='1234567890'; //字符池
    for($i=0;$i<$length;$i++){
        $key.=$pattern{mt_rand(0,9)};//生成php随机数
    }
    return $key;
}


function give_ms($yzm,$tel){ //发送短信    短信开关，帐号，密码，内容，手机呈

file_get_contents("http://api.sms.cn/mt/?uid=xxxxx&pwd=".md5(xxxxxxx)."&mobile=".$tel."&mobileids=&content=".$msg."");


}

$yzm=randomkeys(6);

if ($action=="chk"){

    if ($tel=="" || $yzm=="" ){
        $instrs =  "短信验证码发送失败！";
        $outstrs = iconv('GBK','UTF-8',$instrs);
        echo $outstrs;
        exit;
    }


    //查询号码是否预约过
    if($tel == true){
        //查询数据库  leave_tell
        $db = require('../caches/configs/database.php');
        $con_arr = $db['default'];
        $conn=mysql_connect($con_arr['hostname'],$con_arr['username'],$con_arr['password']) or die("error connecting") ; //连接数据库
        mysql_query("set names 'utf8'"); 
        mysql_select_db($con_arr['database']);
        $tablename = $con_arr['tablepre'].'guestbook';
        $sql ="select * from $tablename where leave_tell = ".$tel; 
        $result = mysql_query($sql,$conn); 
        $res = mysql_fetch_row($result);
        if(!empty($res) && $res == true){
            $instr =  "您的手机号已经注册过！";
            $outstr = iconv('GBK','UTF-8',$instr);
            echo $outstr;
            exit;
        }
    }



    give_ms($yzm,$tel);
    echo "right|".$yzm;
    //echo "right|666666";
    exit;


}

?>