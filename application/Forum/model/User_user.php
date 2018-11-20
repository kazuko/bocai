<?php

namespace app\forum\model;

use think\Model;
use app\forum\model\Gold_history;

/**
 */
class User_user extends Model {
	// protected $table = "bc_User_user";
	function findByGold($zone, $user, $author) {
		$data = array (
				'gold' => $user ['gold'] - $zone ['gold'] 
		); // 看帖扣除金币
		$authordata = array (
				'addgold' => $author ['addgold'] + $zone ['gold'] 
		); // 作者增加金币
		$gdetail = array ( // 金币账单详情
				'当前金币' => $user ['gold'] + $user ['bank'],
				'扣除金币' => ( int ) $zone ['gold'],
				'结果' => '-' . $zone ['gold'],
				'剩余金币' => $data ['gold'] + $user ['bank'] 
		);
		$gold = array ( // 金币账单
				'username' => $user ['username'], // 用户账号
				'operation' => '看帖扣除金币', // 操作说明
				'detail' => json_encode ( $gdetail ), // 账单详情
				'time' => time () 
		); 
		$this->startTrans (); // 开启事务
		try { //
			if (User_user::where ( 'id', $user ['id'] )->update ( $data ) && User_user::where ( 'id', $author ['id'] )->update ( $authordata )) {
				if (Gold_history::create ( $gold )) { // 记录账单
					$newAuthor = User_user::get ( $author ['id'] ); // 要在插入数据之后查询最新的数据
					if ($newAuthor ['addgold'] >= 100) { // 如果累积看帖金币超过100或等于100
						// 累积的金币乘以百分比除100
						$rgold = ( int ) $newAuthor ['addgold'] * ($zone ['pride_percent'] / 100); 
						$reward = array (
								'gold' => $newAuthor ['gold'] + $rgold,
								'addgold' => 0 
						);
						$newdetail = array ( // 金币账单详情
								'当前金币' => $newAuthor ['gold'] + $newAuthor ['bank'],
								'奖励金币' => $rgold,
								'结果' => '+' . $rgold,
								'剩余金币' => $newAuthor ['gold'] + $newAuthor ['bank'] + $rgold 
						);
						$newgold = array ( // 金币账单
								'username' => $newAuthor ['username'], // 用户账号
								'operation' => '百分比发放奖励', // 操作说明
								'detail' => json_encode ( $newdetail ), // 账单详情
								'time' => time () 
						); 
						if (User_user::where ( 'id', $newAuthor ['id'] )->update ( $reward )) { // 发放奖励存入数据库
							if (Gold_history::create ( $newgold )) {//发放奖励账单存入数据库
								$this->commit (); // 提交事务
								return $result = '看帖扣除金币' . $zone ['gold'];
							} else {//发放奖励账单失败
								$this->rollback ();
								return $result = '发放奖励账单失败';
							}
						} else {//奖励发放失败
							$this->rollback ();
							return $result = '奖励发放失败';
						}
					} else { // 累积金币没超过阀值
						$this->commit (); // 提交事务
						return $result = '看帖扣除金币' . $zone ['gold'];
					}
				} else { // 记录账单失败
					$this->rollback ();
					return $result = '记录账单失败';
				}
			} else { // 看帖人扣除金币与作者金币累计值失败
				$this->rollback ();
				return $result = '看帖人扣除金币与作者金币累计值失败';
			}
		} catch ( Exception $e ) { // 数据库不支持事务
			$this->rollback (); // 回滚事务
			return $result = '数据库不支持事务';
		}
	}
}