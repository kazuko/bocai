<?php
namespace app\forum\model;
use think\Model;
use app\forum\model\Sliver_history;
use app\forum\model\Gold_history;
/**
* 
*/
class Forum_post extends Model
{
	// protected $table = "bc_Forum_post";
	//添加帖子，并且前几个帖子有奖励
	function add($param,$systemInfo,$user,$post) {
		$param ['addtime'] = time(); // 时间
		$data = array (
				'gold' => $user ['gold'] + $systemInfo [0] ['tie_pride_gold'], // 发帖奖励金币
				'integral' => $user ['integral'] + $systemInfo [0] ['tie_pride_sliver']// 发帖奖励积分
		);
		$gdetail=array(//金币账单详情
				'当前金币' => $user ['gold']+$user['bank'], 
				'奖励金币' =>(int)$systemInfo [0] ['tie_pride_gold'], 
				'结果' =>'+'.$systemInfo [0] ['tie_pride_gold'], 
				'剩余金币' => $data['gold']+$user['bank']
		);
		$gold=array(//金币账单
				'username' => $user ['username'], // 用户账号
				'operation' =>'发帖奖励金币', // 操作说明
				'detail' =>json_encode($gdetail), // 账单详情
				// 发帖奖励积分
				'time' => time()
		);
		$iintegral=array(//积分账单详情
				'当前积分' => $user ['integral'], 
				'奖励积分' =>(int)$systemInfo [0] ['tie_pride_sliver'], 
				'结果' =>'+'.$systemInfo [0] ['tie_pride_sliver'], 
				'剩余积分' => $data['integral']
		);
		$integral=array(//积分账单
				'username' => $user ['username'], // 用户账号
				'operation' =>'发帖奖励积分', // 操作说明
				'detail' =>json_encode($iintegral), // 账单详情
				// 发帖奖励积分
				'time' => time()
		);
		$this->startTrans (); // 开启事务
		try {
			$this->create ( $param ); // 写入数据
			if ($post < $systemInfo [0] ['tie_pride_num']) { //前几次发的帖子有奖励
				if (User_user::where ( 'id', $param ['user_id'] )->update ( $data )) {
					if (Gold_history::create($gold) && Sliver_history::create($integral)) {
						$this->commit (); // 成功，提交事务
						return $result = 201;
					}else {
						$this->rollback ();
						return $result = 500;
					}
				} else { // 失败，回滚事务
					$this->rollback ();
					return $result = 500;
				}
			} else {
				$this->commit ();
				return $result = 200;
			}
		} catch ( Exception $e ) {
			$this->rollback ();
			return $result = 500;
		}
	}
	//根据用户id查询最新的帖子
	function LastDesc($param) {
		return $this->where( 'user_id', $param ['user_id'] )->order ( 'addtime desc' )->field('addtime')->find ();
	}
	//发帖总次数，前几次有奖励
	function LastsAscCount($param) {
		return $this->where ( 'user_id', $param ['user_id'] )->count ();
	}
}