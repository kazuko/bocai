<?php
// 应用公共文件

/**
 * 18位的订单号
 * @param string $gameNumber 游戏编号
 * 	10北京pk10,20分分彩,30江苏快3,40重庆时时彩,50广东11选5,60六合彩
 * @param number $uid 用户Id
 * @param string $expect 下注期数
 * 
 */
function eighteenOrderNumber($gameNumber,$uid,$expect='') {
	$delicate = (microtime ( true ) - time ()) * 1000;//微妙
	/**
	 * * @param string $gameNumber 游戏编号
 * 	10北京pk10,20分分彩,30江苏快3,40重庆时时彩,50广东11选5,60六合彩
 * @param number $uid 用户Id
 * @param string $expect 下注期数
	 */
	switch ($gameNumber) {
		case 10://北京Pk10
		$md=date('md',time());//月份和天数四位
		$expect=substr($expect,-3);//期号后三位
		$smallSubtle=substr($delicate,-1);//微妙最后一位
		$random=sprintf("%03d",mt_rand(0,999));//0-999的随机数三位
		$subtle=sprintf ( "%03d",$delicate);//微妙三位数
		$uid=sprintf("%02d",substr($uid,-2));//用户ID后两位
		break;
		case 20://分分彩
		$md=date('md',time());//月份和天数四位
		$expect=sprintf ( "%04d",twentyFourExpect('expect'));//期号四位
		$smallSubtle='';
		$random=sprintf("%03d",mt_rand(0,999));//0-999的随机数三位
		$subtle=sprintf ( "%03d",$delicate);//微妙三位数
		$uid=sprintf("%02d",substr($uid,-2));//用户ID后两位
		break;
		case 30://江苏快3
		$md=date('md',time());//月份和天数四位
		$expect=substr($expect,-2);//期号后两位
		$smallSubtle=sprintf ( "%03d",substr($delicate,-3));//微微妙(取最后三位)
		$random=sprintf("%02d",mt_rand(0,99));//0-99的随机数两位
		$subtle=sprintf ( "%03d",$delicate);//微妙三位数
		$uid=sprintf("%02d",substr($uid,-2));//用户ID后四位
		break;
		case 40://重庆时时彩 
		$md=date('md',time());//月份和天数四位
		$expect=substr($expect,-3);//期号后三位
		$smallSubtle=substr($delicate,-1);//微微妙(取最后三位)
		$random=sprintf("%03d",mt_rand(0,999));//0-999的三位随机数
		$subtle=sprintf ( "%03d",$delicate);//微妙三位数
		$uid=sprintf("%02d",substr($uid,-2));//用户ID后四位
		break;
		case 50://广东11x5
		$md=date('md',time());//月份和天数四位
		$expect=substr($expect,-2);//期号后两位
		$smallSubtle=sprintf ( "%03d",substr($delicate,-3));//微微妙(取最后三位)
		$random=sprintf("%02d",mt_rand(0,99));//0-99的随机数两位数
		$subtle=sprintf ( "%03d",$delicate);//微妙三位数
		$uid=sprintf("%02d",substr($uid,-2));//用户ID后四位
		break;
		case 60://六合彩
		$md=date('md',time());//月份和天数四位
		$expect=substr($expect,-3);//期号后三位
		$smallSubtle=substr($delicate,-1);//微微妙(取最后一位)
		$random=sprintf("%03d",mt_rand(0,999));//0-999的三位随机数
		$subtle=sprintf ( "%03d",$delicate);//微妙三位数
		$uid=sprintf("%02d",substr($uid,-2));//用户ID后四位
		break;
	}
	
	$res=$gameNumber.$md.$expect.$smallSubtle.$random.$subtle.$uid;//拼接
	return $res;
}
/**
 * 帖数
 * @param unknown $Count
 * @return string
 */
function friendlyCount($Count=6) {
	if (!$Count)
		return '';
	$dCount = strlen($Count);
	if ($dCount == 5) {
		return substr($Count, 0,1).'万';
	}elseif ($dCount == 6){
		return substr($Count, 0,2).'万';
	}elseif ($dCount == 7){
		return substr($Count, 0,3).'万';
	}elseif ($dCount == 8){
		return substr($Count, 0,4).'万';
	}
}

/**
 * 24小时开奖期号
 * 分分彩
 * @param unknown $Count        	
 * @return string
 */
function twentyFourExpect($type='open') {
	$hour=( int ) date ( 'H', time () );
	$Ymd=date ( 'Ymd', time () );
	$minute=date ( 'i', time () );
	switch ($hour) {
		case 0 :
			$expect = $Ymd . sprintf ( "%04d", $minute );
			break;
		case 1 :
			$minute +=60;
			
			$expect = $Ymd . sprintf ( "%04d", $minute );
			break;
		case 2 :
			$minute +=60*2;
			$expect = $Ymd . sprintf ( "%04d", $minute );
			break;
		case 3 :
			$minute +=60*3;
			$expect = $Ymd . sprintf ( "%04d", $minute );
			break;
		case 4 :
			$minute +=60*4;
			$expect = $Ymd . sprintf ( "%04d", $minute );
			break;
		case 5 :
			$minute +=60*5;
			$expect = $Ymd . sprintf ( "%04d", $minute );
			break;
		case 6 :
			$minute +=60*6;
			$expect = $Ymd . sprintf ( "%04d", $minute );
			break;
		case 7 :
			$minute +=60*7;
			$expect = $Ymd . sprintf ( "%04d", $minute );
			break;
		case 8 :
			$minute +=60*8;
			$expect = $Ymd . sprintf ( "%04d", $minute );
			break;
		case 9 :
			$minute +=60*9;
			$expect = $Ymd . sprintf ( "%04d", $minute );
			break;
		case 10 :
			$minute +=60*10;
			$expect = $Ymd . sprintf ( "%04d", $minute );
			break;
		case 11 :
			$minute +=60*11;
			$expect = $Ymd . sprintf ( "%04d", $minute );
			break;
		case 12 :
			$minute +=60*12;
			$expect = $Ymd . sprintf ( "%04d", $minute );
			break;
		case 13 :
			$minute +=60*13;
			$expect = $Ymd . sprintf ( "%04d", $minute );
			break;
		case 14 :
			$minute +=60*14;
			$expect = $Ymd . sprintf ( "%04d", $minute );
			break;
		case 15 :
			$minute +=60*15;
			$expect = $Ymd . sprintf ( "%04d", $minute );
			break;
		case 16 :
			$minute +=60*16;
			$expect = $Ymd . sprintf ( "%04d", $minute );
			break;
		case 17 :
			$minute +=60*17;
			$expect = $Ymd . sprintf ( "%04d", $minute );
			break;
		case 18 :
			$minute +=60*18;
			$expect = $Ymd . sprintf ( "%04d", $minute );
			break;
		case 19 :
			$minute +=60*19;
			$expect = $Ymd . sprintf ( "%04d", $minute );
			break;
		case 20 :
			$minute +=60*20;
			$expect = $Ymd . sprintf ( "%04d", $minute );
			break;
		case 21 :
			$minute +=60*21;
			$expect = $Ymd . sprintf ( "%04d", $minute );
			break;
		case 22 :
			$minute +=60*22;
			$expect = $Ymd . sprintf ( "%04d", $minute );
			break;
		case 23 :
			$minute +=60*23;
			$expect = $Ymd . sprintf ( "%04d", $minute );
			break;
	}
	if ($type==='open') {
		return $expect;
	}else if ($type==='expect'){
		return (string)sprintf ( "%04d", $minute+1 );
	}
}

/**
 * 时间转换
 * @param unknown $sTime
 * @param string $type 类型
 */
function friendlyDate($sTime,$type = 'forum') {
	if (!$sTime)
		return '';
		//sTime=源时间，cTime=当前时间，dTime=时间差
		$cTime      =   time();
		$dTime      =   $cTime - $sTime;
		$dSecond    =   intval(date("s",$cTime)) - intval(date("s",$sTime));
		$dMinute    =   intval(date("i",$cTime)) - intval(date("i",$sTime));
		$dDay       =   intval(date("d",$cTime)) - intval(date("d",$sTime));
		$dMonth     =   intval(date("n",$cTime)) - intval(date("n",$sTime));
		//$dDay     =   intval($dTime/3600/24);
		$dYear      =   intval(date("Y",$cTime)) - intval(date("Y",$sTime));
		//normal：n秒前，n分钟前，n小时前，日期
		if($type=='forum'){
			if( $dTime < 60 ){
				if($dTime < 10){
					return '刚刚';    //by yangjs
				}else{
					return intval(floor($dTime / 10) * 10)."秒前";
				}
			}elseif( $dTime < 3600 ){
				return intval($dTime/60)."分钟前";
				//今天的数据.年份相同.日期相同.
			}elseif( $dTime >= 3600 && $dDay == 0 && $dYear==0 && $dMonth==0 && $dTime <= 39600 ){
				return intval($dTime/3600)."小时前";
			}elseif( $dDay > 0 && $dDay <= 1 && $dYear==0 && $dMonth==0){
				return "昨天".date('G:i',$sTime);
			}elseif( $dDay > 1 && $dDay <= 2 && $dYear==0 && $dMonth==0){
				return "前天".date('G:i',$sTime);
			}elseif( $dYear==0 && $dDay == 0 && $dMonth==0 ){
				//return intval($dTime/3600)."小时前";
				return '今天'.date('G:i',$sTime);
			}elseif( $dDay > 0 && $dDay<=7 && $dYear==0 && $dMonth==0){
				return intval($dDay)."天前";
			}elseif($dYear==0){
				return date("n月j日 G:i",$sTime);
			}else{
				return date("Y-n-j",$sTime);
			}
		}elseif($type=='mohu'){
			if( $dTime < 60 ){
				return $dTime."秒前";
			}elseif( $dTime < 3600 ){
				return intval($dTime/60)."分钟前";
			}elseif( $dTime >= 3600 && $dDay == 0  ){
				return intval($dTime/3600)."小时前";
			}elseif( $dDay > 0 && $dDay<=7 ){
				return intval($dDay)."天前";
			}elseif( $dDay > 7 &&  $dDay <= 30 ){
				return intval($dDay/7) . '周前';
			}elseif( $dDay > 30 ){
				return intval($dDay/30) . '个月前';
			}
			//full: Y-m-d , H:i:s
		}elseif($type=='games'){
			return abs(date('i',60-$dTime)).':'.abs(date('s',60-$dTime));
		}elseif($type=='ymd'){
			return date("Y-m-d",$sTime);
		}else{
			if( $dTime < 60 ){
				return $dTime."秒前";
			}elseif( $dTime < 3600 ){
				return intval($dTime/60)."分钟前";
			}elseif( $dTime >= 3600 && $dDay == 0  ){
				return intval($dTime/3600)."小时前";
			}elseif($dYear==0){
				return date("Y-m-d H:i:s",$sTime);
			}else{
				return date("Y-m-d H:i:s",$sTime);
			}
		}
}