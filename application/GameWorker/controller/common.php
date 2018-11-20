<?php 
namespace app\GameWorker\common;
class common{
    /** 广东11选5,60六合彩
    * @param number $uid 用户Id
    * @param string $expect 下注期数
    * 
    */
   function eighteenOrderNumber($game,$uid,$expect='') {
       $delicate = (microtime ( true ) - time ()) * 1000;//微妙
       /**
        * * @param string $gameNumber 游戏编号
    * 	10北京pk10,20分分彩,30江苏快3,40重庆时时彩,50广东11选5,60六合彩
    * @param number $uid 用户Id
    * @param string $expect 下注期数
        */
       switch ($game) {
           case "Bjpk10"://北京Pk10
           $md=date('md',time());//月份和天数四位
           $expect=substr($expect,-3);//期号后三位
           $smallSubtle=substr($delicate,-1);//微妙最后一位
           $random=sprintf("%03d",mt_rand(0,999));//0-999的随机数三位
           $subtle=sprintf ( "%03d",$delicate);//微妙三位数
           $uid=sprintf("%02d",substr($uid,-2));//用户ID后两位
           break;
           case "ffc"://分分彩
           $md=date('md',time());//月份和天数四位
           $expect=sprintf ( "%04d",twentyFourExpect('expect'));//期号四位
           $smallSubtle='';
           $random=sprintf("%03d",mt_rand(0,999));//0-999的随机数三位
           $subtle=sprintf ( "%03d",$delicate);//微妙三位数
           $uid=sprintf("%02d",substr($uid,-2));//用户ID后两位
           break;
           case "jsks"://江苏快3
           $md=date('md',time());//月份和天数四位
           $expect=substr($expect,-2);//期号后两位
           $smallSubtle=sprintf ( "%03d",substr($delicate,-3));//微微妙(取最后三位)
           $random=sprintf("%02d",mt_rand(0,99));//0-99的随机数两位
           $subtle=sprintf ( "%03d",$delicate);//微妙三位数
           $uid=sprintf("%02d",substr($uid,-2));//用户ID后四位
           break;
           case "cqssc"://重庆时时彩 
           $md=date('md',time());//月份和天数四位
           $expect=substr($expect,-3);//期号后三位
           $smallSubtle=substr($delicate,-1);//微微妙(取最后三位)
           $random=sprintf("%03d",mt_rand(0,999));//0-999的三位随机数
           $subtle=sprintf ( "%03d",$delicate);//微妙三位数
           $uid=sprintf("%02d",substr($uid,-2));//用户ID后四位
           break;
           case "gd11x5"://广东11x5
           $md=date('md',time());//月份和天数四位
           $expect=substr($expect,-2);//期号后两位
           $smallSubtle=sprintf ( "%03d",substr($delicate,-3));//微微妙(取最后三位)
           $random=sprintf("%02d",mt_rand(0,99));//0-99的随机数两位数
           $subtle=sprintf ( "%03d",$delicate);//微妙三位数
           $uid=sprintf("%02d",substr($uid,-2));//用户ID后四位
           break;
           case ://六合彩
           $md=date('md',time());//月份和天数四位
           $expect=substr($expect,-3);//期号后三位
           $smallSubtle=substr($delicate,-1);//微微妙(取最后一位)
           $random=sprintf("%03d",mt_rand(0,999));//0-999的三位随机数
           $subtle=sprintf ( "%03d",$delicate);//微妙三位数
           $uid=sprintf("%02d",substr($uid,-2));//用户ID后四位
           break;
       }
       //游戏编号（两位）月日（两位）期号（）微秒（最后几位）随机数（）微秒（三位）用户ID（后两位）
       $res=$gameNumber.$md.$expect.$smallSubtle.$random.$subtle.$uid;//拼接
       return $res;
   }
}