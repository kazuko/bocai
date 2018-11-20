<?php

namespace app\ssc\model;

use think\Model;
use app\forum\model\Gold_history;
use app\forum\model\User_user;
/**
 */
class Games_cqbet extends Model {
	// protected $table = "bc_games_cqbet";
	/**
	 * 计算注数
	 * 
	 * @param unknown $param        	
	 * @param unknown $user        	
	 */
	function betCount($param) {
		$param ['goldCount'] = 0; // 下注金额
		$param ['bet'] = (count ( $param ['lmp'],1)-9)
		+(count ( $param ['OneTofive'],1)-5)
		+(count ( $param ['OneWord'],1)-4)
		+(count ( $param ['TwoWord'],1)-3)
		+(count ( $param ['ThreeWord'],1)-3)
		+(count ( $param ['span'],1)-3)
		+(count ( $param ['and'],1)-10)
		+(count ( $param ['niuniu'],1)-3);
		//----------------------------------------二定位下注数  （直选复式）---------------------
		if ((count ( $param ['Twolocation'],1)-10)) {
			//万千
			if (count($param['Twolocation']['OOXXX']['firstball']) > 0 && count($param['Twolocation']['OOXXX']['secondball']) > 0) {
				if (count($param['Twolocation']['OOXXX']['firstball']) === 10 && count($param['Twolocation']['OOXXX']['secondball']) === 10) {
					$param ['bet']+=(count($param['Twolocation']['OOXXX']['firstball'])*count($param['Twolocation']['OOXXX']['secondball'])-1);
				}else if (count($param['Twolocation']['OOXXX']['firstball']) === 1 && count($param['Twolocation']['OOXXX']['secondball']) === 1){
					$param ['bet']+=1;//如果第一球和第二球各选了一个号码，则是一注
				}else {//否则都是第一球乘以第二球 =下注数
					$param ['bet']+=(count($param['Twolocation']['OOXXX']['firstball'])*count($param['Twolocation']['OOXXX']['secondball']));
				}
			}
			//万百
			if (count($param['Twolocation']['OXOXX']['firstball']) > 0 && count($param['Twolocation']['OXOXX']['secondball']) > 0) {
				if (count($param['Twolocation']['OXOXX']['firstball']) === 10 && count($param['Twolocation']['OXOXX']['secondball']) === 10) {
					$param ['bet']+=(count($param['Twolocation']['OXOXX']['firstball'])*count($param['Twolocation']['OXOXX']['secondball'])-1);
				}else if (count($param['Twolocation']['OXOXX']['firstball']) === 1 && count($param['Twolocation']['OXOXX']['secondball']) === 1){
					$param ['bet']+=1;//如果第一球和第二球各选了一个号码，则是一注
				}else {//否则都是第一球乘以第二球 =下注数
					$param ['bet']+=(count($param['Twolocation']['OXOXX']['firstball'])*count($param['Twolocation']['OXOXX']['secondball']));
				}
			}
			//万十
			if (count($param['Twolocation']['OXXOX']['firstball']) > 0 && count($param['Twolocation']['OXXOX']['secondball']) > 0) {
				if (count($param['Twolocation']['OXXOX']['firstball']) === 10 && count($param['Twolocation']['OXXOX']['secondball']) === 10) {
					$param ['bet']+=(count($param['Twolocation']['OXXOX']['firstball'])*count($param['Twolocation']['OXXOX']['secondball'])-1);
				}else if (count($param['Twolocation']['OXXOX']['firstball']) === 1 && count($param['Twolocation']['OXXOX']['secondball']) === 1){
					$param ['bet']+=1;//如果第一球和第二球各选了一个号码，则是一注
				}else {//否则都是第一球乘以第二球 =下注数
					$param ['bet']+=(count($param['Twolocation']['OXXOX']['firstball'])*count($param['Twolocation']['OXXOX']['secondball']));
				}
			}
			//万个
			if (count($param['Twolocation']['OXXXO']['firstball']) > 0 && count($param['Twolocation']['OXXXO']['secondball']) > 0) {
				if (count($param['Twolocation']['OXXXO']['firstball']) === 10 && count($param['Twolocation']['OXXXO']['secondball']) === 10) {
					$param ['bet']+=(count($param['Twolocation']['OXXXO']['firstball'])*count($param['Twolocation']['OXXXO']['secondball'])-1);
				}else if (count($param['Twolocation']['OXXXO']['firstball']) === 1 && count($param['Twolocation']['OXXXO']['secondball']) === 1){
					$param ['bet']+=1;//如果第一球和第二球各选了一个号码，则是一注
				}else {//否则都是第一球乘以第二球 =下注数
					$param ['bet']+=(count($param['Twolocation']['OXXXO']['firstball'])*count($param['Twolocation']['OXXXO']['secondball']));
				}
			}
			//千百
			if (count($param['Twolocation']['XOOXX']['firstball']) > 0 && count($param['Twolocation']['XOOXX']['secondball']) > 0) {
				if (count($param['Twolocation']['XOOXX']['firstball']) === 10 && count($param['Twolocation']['XOOXX']['secondball']) === 10) {
					$param ['bet']+=(count($param['Twolocation']['XOOXX']['firstball'])*count($param['Twolocation']['XOOXX']['secondball'])-1);
				}else if (count($param['Twolocation']['XOOXX']['firstball']) === 1 && count($param['Twolocation']['XOOXX']['secondball']) === 1){
					$param ['bet']+=1;//如果第一球和第二球各选了一个号码，则是一注
				}else {//否则都是第一球乘以第二球 =下注数
					$param ['bet']+=(count($param['Twolocation']['XOOXX']['firstball'])*count($param['Twolocation']['XOOXX']['secondball']));
				}
			}
			//千十
			if (count($param['Twolocation']['XOXOX']['firstball']) > 0 && count($param['Twolocation']['XOXOX']['secondball']) > 0) {
				if (count($param['Twolocation']['XOXOX']['firstball']) === 10 && count($param['Twolocation']['XOXOX']['secondball']) === 10) {
					$param ['bet']+=(count($param['Twolocation']['XOXOX']['firstball'])*count($param['Twolocation']['XOXOX']['secondball'])-1);
				}else if (count($param['Twolocation']['XOXOX']['firstball']) === 1 && count($param['Twolocation']['XOXOX']['secondball']) === 1){
					$param ['bet']+=1;//如果第一球和第二球各选了一个号码，则是一注
				}else {//否则都是第一球乘以第二球 =下注数
					$param ['bet']+=(count($param['Twolocation']['XOXOX']['firstball'])*count($param['Twolocation']['XOXOX']['secondball']));
				}
			}
			//千个
			if (count($param['Twolocation']['XOXXO']['firstball']) > 0 && count($param['Twolocation']['XOXXO']['secondball']) > 0) {
				if (count($param['Twolocation']['XOXXO']['firstball']) === 10 && count($param['Twolocation']['XOXXO']['secondball']) === 10) {
					$param ['bet']+=(count($param['Twolocation']['XOXXO']['firstball'])*count($param['Twolocation']['XOXXO']['secondball'])-1);
				}else if (count($param['Twolocation']['XOXXO']['firstball']) === 1 && count($param['Twolocation']['XOXXO']['secondball']) === 1){
					$param ['bet']+=1;//如果第一球和第二球各选了一个号码，则是一注
				}else {//否则都是第一球乘以第二球 =下注数
					$param ['bet']+=(count($param['Twolocation']['XOXXO']['firstball'])*count($param['Twolocation']['XOXXO']['secondball']));
				}
			}
			//百十
			if (count($param['Twolocation']['XXOOX']['firstball']) > 0 && count($param['Twolocation']['XXOOX']['secondball']) > 0) {
				if (count($param['Twolocation']['XXOOX']['firstball']) === 10 && count($param['Twolocation']['XXOOX']['secondball']) === 10) {
					$param ['bet']+=(count($param['Twolocation']['XXOOX']['firstball'])*count($param['Twolocation']['XXOOX']['secondball'])-1);
				}else if (count($param['Twolocation']['XXOOX']['firstball']) === 1 && count($param['Twolocation']['XXOOX']['secondball']) === 1){
					$param ['bet']+=1;//如果第一球和第二球各选了一个号码，则是一注
				}else {//否则都是第一球乘以第二球 =下注数
					$param ['bet']+=(count($param['Twolocation']['XXOOX']['firstball'])*count($param['Twolocation']['XXOOX']['secondball']));
				}
			}
			//百个
			if (count($param['Twolocation']['XXOXO']['firstball']) > 0 && count($param['Twolocation']['XXOXO']['secondball']) > 0) {
				if (count($param['Twolocation']['XXOXO']['firstball']) === 10 && count($param['Twolocation']['XXOXO']['secondball']) === 10) {
					$param ['bet']+=(count($param['Twolocation']['XXOXO']['firstball'])*count($param['Twolocation']['XXOXO']['secondball'])-1);
				}else if (count($param['Twolocation']['XXOXO']['firstball']) === 1 && count($param['Twolocation']['XXOXO']['secondball']) === 1){
					$param ['bet']+=1;//如果第一球和第二球各选了一个号码，则是一注
				}else {//否则都是第一球乘以第二球 =下注数
					$param ['bet']+=(count($param['Twolocation']['XXOXO']['firstball'])*count($param['Twolocation']['XXOXO']['secondball']));
				}
			}
			//十个
			if (count($param['Twolocation']['XXXOO']['firstball']) > 0 && count($param['Twolocation']['XXXOO']['secondball']) > 0) {
				if (count($param['Twolocation']['XXXOO']['firstball']) === 10 && count($param['Twolocation']['XXXOO']['secondball']) === 10) {
					$param ['bet']+=(count($param['Twolocation']['XXXOO']['firstball'])*count($param['Twolocation']['XXXOO']['secondball'])-1);
				}else if (count($param['Twolocation']['XXXOO']['firstball']) === 1 && count($param['Twolocation']['XXXOO']['secondball']) === 1){
					$param ['bet']+=1;//如果第一球和第二球各选了一个号码，则是一注
				}else {//否则都是第一球乘以第二球 =下注数
					$param ['bet']+=(count($param['Twolocation']['XXXOO']['firstball'])*count($param['Twolocation']['XXXOO']['secondball']));
				}
			}
		}
			// ----------------------三定位下注数（复式直选）-------------
		if ((count ( $param ['Threelocation'], 1 ) - 12)) {
			// 前三
			if (count ( $param ['Threelocation'] ['firstThree'] ['firstball'] ) > 0 && count ( $param ['Threelocation'] ['firstThree'] ['secondball'] ) > 0 && count ( $param ['Threelocation'] ['firstThree'] ['thirdball'] ) > 0) {
				if (count ( $param ['Threelocation'] ['firstThree'] ['firstball'] ) === 10 && count ( $param ['Threelocation'] ['firstThree'] ['secondball'] ) === 10 && count ( $param ['Threelocation'] ['firstThree'] ['thirdball'] ) === 10) {
					$param ['bet'] = $param ['bet'] + (count ( $param ['Threelocation'] ['firstThree'] ['firstball'] ) * count ( $param ['Threelocation'] ['firstThree'] ['secondball'] ) * count ( $param ['Threelocation'] ['firstThree'] ['thirdball'] ) - 1);
				} else if (count ( $param ['Threelocation'] ['firstThree'] ['firstball'] ) === 1 && count ( $param ['Threelocation'] ['firstThree'] ['secondball'] ) === 1 && count ( $param ['Threelocation'] ['firstThree'] ['thirdball'] ) === 1) {
					$param ['bet'] = $param ['bet'] + 1; // 如果第一球和第二球各选了一个号码，则是一注
				} else {
					$param ['bet'] = $param ['bet'] + (count ( $param ['Threelocation'] ['firstThree'] ['firstball'] ) * count ( $param ['Threelocation'] ['firstThree'] ['secondball'] ) * count ( $param ['Threelocation'] ['firstThree'] ['thirdball'] ));
				}
			}
			// 中三
			if (count ( $param ['Threelocation'] ['middleThree'] ['firstball'] ) > 0 && count ( $param ['Threelocation'] ['middleThree'] ['secondball'] ) > 0 && count ( $param ['Threelocation'] ['middleThree'] ['thirdball'] ) > 0) {
				if (count ( $param ['Threelocation'] ['middleThree'] ['firstball'] ) === 10 && count ( $param ['Threelocation'] ['middleThree'] ['secondball'] ) === 10 && count ( $param ['Threelocation'] ['middleThree'] ['thirdball'] ) === 10) {
					$param ['bet'] = $param ['bet'] + (count ( $param ['Threelocation'] ['middleThree'] ['firstball'] ) * count ( $param ['Threelocation'] ['middleThree'] ['secondball'] ) * count ( $param ['Threelocation'] ['middleThree'] ['thirdball'] ) - 1);
				} else if (count ( $param ['Threelocation'] ['middleThree'] ['firstball'] ) === 1 && count ( $param ['Threelocation'] ['middleThree'] ['secondball'] ) === 1 && count ( $param ['Threelocation'] ['middleThree'] ['thirdball'] ) === 1) {
					$param ['bet'] = $param ['bet'] + 1; // 如果第一球和第二球各选了一个号码，则是一注
				} else {
					$param ['bet'] = $param ['bet'] + (count ( $param ['Threelocation'] ['middleThree'] ['firstball'] ) * count ( $param ['Threelocation'] ['middleThree'] ['secondball'] ) * count ( $param ['Threelocation'] ['middleThree'] ['thirdball'] ));
				}
			}
			// 后三
			if (count ( $param ['Threelocation'] ['lastThree'] ['firstball'] ) > 0 && count ( $param ['Threelocation'] ['lastThree'] ['secondball'] ) > 0 && count ( $param ['Threelocation'] ['lastThree'] ['thirdball'] ) > 0) {
				if (count ( $param ['Threelocation'] ['lastThree'] ['firstball'] ) === 10 && count ( $param ['Threelocation'] ['lastThree'] ['secondball'] ) === 10 && count ( $param ['Threelocation'] ['lastThree'] ['thirdball'] ) === 10) {
					$param ['bet'] = $param ['bet'] + (count ( $param ['Threelocation'] ['lastThree'] ['firstball'] ) * count ( $param ['Threelocation'] ['lastThree'] ['secondball'] ) * count ( $param ['Threelocation'] ['lastThree'] ['thirdball'] ) - 1);
				} else if (count ( $param ['Threelocation'] ['lastThree'] ['firstball'] ) === 1 && count ( $param ['Threelocation'] ['lastThree'] ['secondball'] ) === 1 && count ( $param ['Threelocation'] ['lastThree'] ['thirdball'] ) === 1) {
					$param ['bet'] = $param ['bet'] + 1; // 如果第一球和第二球各选了一个号码，则是一注
				} else {
					$param ['bet'] = $param ['bet'] + (count ( $param ['Threelocation'] ['lastThree'] ['firstball'] ) * count ( $param ['Threelocation'] ['lastThree'] ['secondball'] ) * count ( $param ['Threelocation'] ['lastThree'] ['thirdball'] ));
				}
			}
		}
		//---------------------组选三下注数（组选复式）-------------------
	if ((count ( $param ['groupThree'],1)-3)) {
				//前三
				if (count ( $param ['groupThree']['firstThree']) > 0) {
					if (count ( $param ['groupThree']['firstThree']) > 2) {
						$param ['bet']+=array_product(range(1,count($param['groupThree']['firstThree'])))
						/(array_product(range(1,count($param['groupThree']['firstThree'])-2))*array_product(range(1,2)));
					}else if (count ( $param ['groupThree']['firstThree']) ===2){
						$param ['bet']+=1;//刚好等于2就是一注
					}
				}
				//中三
				if (count ( $param ['groupThree']['middleThree']) > 0) {
					if (count ( $param ['groupThree']['middleThree']) > 2) {
						$param ['bet']+=array_product(range(1,count($param['groupThree']['middleThree'])))
						/(array_product(range(1,count($param['groupThree']['middleThree'])-2))*array_product(range(1,2)));
					}else if (count ( $param ['groupThree']['middleThree']) ===2){
						$param ['bet']+=1;//刚好等于2就是一注
					}
				}
				//后三
				if (count ( $param ['groupThree']['lastThree']) > 0) {
					if (count ( $param ['groupThree']['lastThree']) > 2) {
						$param ['bet']+=array_product(range(1,count($param['groupThree']['lastThree'])))
						/(array_product(range(1,count($param['groupThree']['lastThree'])-2))*array_product(range(1,2)));
					}else if (count ( $param ['groupThree']['lastThree']) ===2){
						$param ['bet']+=1;//刚好等于2就是一注
					}
				}
			}
		//---------------------组选六下注数（组选复式）-------------------
		if ((count ( $param ['groupSix'],1)-3)) {
				//前三
				if (count ( $param ['groupSix']['firstThree']) > 0) {
					if (count ( $param ['groupSix']['firstThree']) > 3) {
						$param ['bet']+=array_product(range(1,count($param['groupSix']['firstThree'])))
						/(array_product(range(1,count($param['groupSix']['firstThree'])-3))*array_product(range(1,3)));
					}else if (count ( $param ['groupSix']['firstThree']) ===3){
						$param ['bet']+=1;//刚好等于2就是一注
					}
				}
				//前三
				if (count ( $param ['groupSix']['middleThree']) > 0) {
					if (count ( $param ['groupSix']['middleThree']) > 3) {
						$param ['bet']+=array_product(range(1,count($param['groupSix']['middleThree'])))
						/(array_product(range(1,count($param['groupSix']['middleThree'])-3))*array_product(range(1,3)));
					}else if (count ( $param ['groupSix']['middleThree']) ===3){
						$param ['bet']+=1;//刚好等于2就是一注
					}
				}
				//前三
				if (count ( $param ['groupSix']['lastThree']) > 0) {
					if (count ( $param ['groupSix']['lastThree']) > 3) {
						$param ['bet']+=array_product(range(1,count($param['groupSix']['lastThree'])))
						/(array_product(range(1,count($param['groupSix']['lastThree'])-3))*array_product(range(1,3)));
					}else if (count ( $param ['groupSix']['lastThree']) ===3){
						$param ['bet']+=1;//刚好等于2就是一注
					}
				}
			}
		$param ['goldCount'] = $param ['gold'] * $param ['bet'];//下注总金额
		return $param;
	}
	/**
	 * 数据库操作
	 * @param unknown $param
	 * @param unknown $user
	 */
	function bet($param,$user) {
		$data = array (
				'gold' => $user ['gold'] - $param ['goldCount']
		);
		$cqbet = array ( // 要插入数据库的数据
				'user_id' => $user ['id'],
				'detail' => json_encode ( $param ),
				'expect' => $param ['expect'],
				'time' => $_SERVER['REQUEST_TIME']
		);
		$gdetail = array ( // 金币账单详情
				'当前金币' => $user ['gold'] + $user ['bank'],
				'下注扣去' => ( float ) $param ['goldCount'],
				'结果' => '-' . $param ['goldCount'],
				'剩余金币' => $data ['gold'] + $user ['bank']
		);
		$gold = array ( // 金币账单
				'username' => $user ['username'], // 用户账号
				'operation' => '重庆时时彩下注', // 操作说明
				'detail' => json_encode ( $gdetail ), // 账单详情
				'time' => $_SERVER['REQUEST_TIME']
		);
		$this->startTrans (); // 开启事务
		try {
			$this->create ( $cqbet );
			if (User_user::where ( 'id', $cqbet ['user_id'] )->update ( $data )) {
				if (Gold_history::create ( $gold )) {
					$param['surplusGold']=$data ['gold'];
					$this->commit (); // 成功，提交事务
				} else {
					$this->rollback ();
				}
			} else { // 失败，回滚事务
				$this->rollback ();
			}
		} catch ( Exception $e ) {
			$this->rollback ();
		}
		return $param;
	}
}