<?php
namespace app\forum\model;
use think\Model;

/**
* 用户充值记录表
*/
class User_trading extends Model
{
	// protected $table = "bc_User_trading";
	//根据用户id按照时间倒序查询充值记录
	function LastTopUpDesc($param,$systemInfo) {
		return $this->where ( 'user_id', $param ['user_id'] )->where ( 'time', '>', time () - $systemInfo [0] ['tie_limit_refender_seconds'] )->order ( 'time desc' )->find ();
	}
}