<?php
namespace app\gd\model;
use think\Model;
use app\forum\model\Gold_history;
use app\forum\model\User_user;
/**
* 
*/
class Games_gdbet extends Model
{
	// protected $table = "bc_games_gdbet";
	/**
	 * 数组处理
	 * @param unknown $param
	 * @param unknown $user
	 * 
	 */
	function HandleArrays($detail,$gdbet,$odds) {
		$info=array();
		$gold=$detail['gold'];
		$bid=$gdbet['id'];
		$time=date('Y-m-d H:i:s',$gdbet['time']);
		//两面盘
		if ((count($detail['lmp'],1)-6)>0) {
			//总和尾大小单双
			if (count($detail['lmp']['alls'])>0) {
				$info['lmp']['alls']['number']=implode("|",$detail['lmp']['alls']);
				$info['lmp']['alls']['bet']=$bid;
				$info['lmp']['alls']['time']=$time;
				$info['lmp']['alls']['type']='alls';
				$info['lmp']['alls']['gold']=count($detail['lmp']['alls']).'注 '.(count($detail['lmp']['alls']) * $gold).'元';
				$info['lmp']['alls']['odds']=$odds['lmp']['totalBig'];
			}//第一球大小单双
			if (count($detail['lmp']['firstball'])>0) {
				$info['lmp']['firstball']['number']=implode("|",$detail['lmp']['firstball']);
				$info['lmp']['firstball']['bet']=$bid;
				$info['lmp']['firstball']['time']=$time;
				$info['lmp']['firstball']['type']='firstball';
				$info['lmp']['firstball']['gold']=count($detail['lmp']['firstball']).'注 '.(count($detail['lmp']['firstball']) * $gold).'元';
				$info['lmp']['firstball']['odds']=$odds['lmp']['firstball']['big'];
			}//第二球大小单双
			if (count($detail['lmp']['secondball'])>0) {
				$info['lmp']['secondball']['number']=implode("|",$detail['lmp']['secondball']);
				$info['lmp']['secondball']['bet']=$bid;
				$info['lmp']['secondball']['time']=$time;
				$info['lmp']['secondball']['type']='secondball';
				$info['lmp']['secondball']['gold']=count($detail['lmp']['secondball']).'注 '.(count($detail['lmp']['secondball']) * $gold).'元';
				$info['lmp']['secondball']['odds']=$odds['lmp']['secondball']['big'];
			}//第三球大小单双
			if (count($detail['lmp']['thirdball'])>0) {
				$info['lmp']['thirdball']['number']=implode("|",$detail['lmp']['thirdball']);
				$info['lmp']['thirdball']['bet']=$bid;
				$info['lmp']['thirdball']['time']=$time;
				$info['lmp']['thirdball']['type']='thirdball';
				$info['lmp']['thirdball']['gold']=count($detail['lmp']['thirdball']).'注 '.(count($detail['lmp']['thirdball']) * $gold).'元';
				$info['lmp']['thirdball']['odds']=$odds['lmp']['thirdball']['big'];
			}//第四球大小单双
			if (count($detail['lmp']['thouthball'])>0) {
				$info['lmp']['thouthball']['number']=implode("|",$detail['lmp']['thouthball']);
				$info['lmp']['thouthball']['bet']=$bid;
				$info['lmp']['thouthball']['time']=$time;
				$info['lmp']['thouthball']['type']='thouthball';
				$info['lmp']['thouthball']['gold']=count($detail['lmp']['thouthball']).'注 '.(count($detail['lmp']['thouthball']) * $gold).'元';
				$info['lmp']['thouthball']['odds']=$odds['lmp']['thouthball']['big'];
			}//第五球大小单双
			if (count($detail['lmp']['fifthball'])>0) {
				$info['lmp']['fifthball']['number']=implode("|",$detail['lmp']['fifthball']);
				$info['lmp']['fifthball']['bet']=$bid;
				$info['lmp']['fifthball']['time']=$time;
				$info['lmp']['fifthball']['type']='fifthball';
				$info['lmp']['fifthball']['gold']=count($detail['lmp']['fifthball']).'注 '.(count($detail['lmp']['fifthball']) * $gold).'元';
				$info['lmp']['fifthball']['odds']=$odds['lmp']['fifthball']['big'];
			}
		}
		if ((count($detail['dm'],1)-5)>0) {//单码
			if (count($detail ['dm']['firstball']) > 0) {//第一球
				$info['dm']['firstball']['number']=implode(",",$detail['dm']['firstball']);
				$info['dm']['firstball']['bet']=$bid;
				$info['dm']['firstball']['time']=$time;
				$info['dm']['firstball']['type']='fifthball';
				$info['dm']['firstball']['gold']=count($detail['dm']['firstball']).'注 '.(count($detail['dm']['firstball']) * $gold).'元';
				$info['dm']['firstball']['odds']=$odds['dm']['firstball'][1];
			}
			if (count($detail ['dm']['secondball']) > 0) {//第二球
				$info['dm']['secondball']['number']=implode(",",$detail['dm']['secondball']);
				$info['dm']['secondball']['bet']=$bid;
				$info['dm']['secondball']['time']=$time;
				$info['dm']['secondball']['type']='secondball';
				$info['dm']['secondball']['gold']=count($detail['dm']['secondball']).'注 '.(count($detail['dm']['secondball']) * $gold).'元';
				$info['dm']['secondball']['odds']=$odds['dm']['secondball'][1];
			}
			if (count($detail ['dm']['thirdball']) > 0) {//第三球
				$info['dm']['thirdball']['number']=implode(",",$detail['dm']['thirdball']);
				$info['dm']['thirdball']['bet']=$bid;
				$info['dm']['thirdball']['time']=$time;
				$info['dm']['thirdball']['type']='secondball';
				$info['dm']['thirdball']['gold']=count($detail['dm']['thirdball']).'注 '.(count($detail['dm']['thirdball']) * $gold).'元';
				$info['dm']['thirdball']['odds']=$odds['dm']['thirdball'][1];
			}
			if (count($detail ['dm']['thouthball']) > 0) {//第四球
				$info['dm']['thouthball']['number']=implode(",",$detail['dm']['thouthball']);
				$info['dm']['thouthball']['bet']=$bid;
				$info['dm']['thouthball']['time']=$time;
				$info['dm']['thouthball']['type']='thouthball';
				$info['dm']['thouthball']['gold']=count($detail['dm']['thouthball']).'注 '.(count($detail['dm']['thouthball']) * $gold).'元';
				$info['dm']['thouthball']['odds']=$odds['dm']['thouthball'][1];
			}
			if (count($detail ['dm']['fifthball']) > 0) {//第五球
				$info['dm']['fifthball']['number']=implode(",",$detail['dm']['fifthball']);
				$info['dm']['fifthball']['bet']=$bid;
				$info['dm']['fifthball']['time']=$time;
				$info['dm']['fifthball']['type']='fifthball';
				$info['dm']['fifthball']['gold']=count($detail['dm']['fifthball']).'注 '.(count($detail['dm']['fifthball']) * $gold).'元';
				$info['dm']['fifthball']['odds']=$odds['dm']['fifthball'][1];
			}
		}
		if ((count($detail['rx'],1)-8)>0) {//任选
			if (count($detail ['rx']['oneToOne']) > 0) {//一中一
				$info['rx']['oneToOne']['number']=implode(",",$detail['rx']['oneToOne']);
				$info['rx']['oneToOne']['bet']=$bid;
				$info['rx']['oneToOne']['time']=$time;
				$info['rx']['oneToOne']['type']='oneToOne';
				$info['rx']['oneToOne']['gold']=count($detail['rx']['oneToOne']).'注 '.(count($detail['rx']['oneToOne']) * $gold).'元';
				$info['rx']['oneToOne']['odds']=$odds['rx']['oneToOne'][1];
			}
			if (count($detail ['rx']['twoToTwo']) > 0) {//二中二
				$bet=0;
				if (count($detail ['rx'] ['twoToTwo']) > 2) {//二中二两个为一注
					$bet =array_product(range(1,count($detail['rx']['twoToTwo'])))
					/(array_product(range(1,count($detail['rx']['twoToTwo'])-2))*array_product(range(1,2)));
				}else if (count($detail ['rx'] ['twoToTwo']) === 2){
					$bet =1;//刚好等于2就是一注
				}
				$info['rx']['twoToTwo']['number']=implode(",",$detail['rx']['twoToTwo']);
				$info['rx']['twoToTwo']['bet']=$bid;
				$info['rx']['twoToTwo']['time']=$time;
				$info['rx']['twoToTwo']['type']='twoToTwo';
				$info['rx']['twoToTwo']['gold']=$bet.'注 '.($bet * $gold).'元';
				$info['rx']['twoToTwo']['odds']=$odds['rx']['twoToTwo'][1];
			}
			if (count($detail ['rx']['threeToThree']) > 0) {
				$bet=0;
				if (count($detail ['rx'] ['threeToThree']) > 3) {//三中三两个为一注
					$bet=array_product(range(1,count($detail['rx']['threeToThree'])))
					/(array_product(range(1,count($detail['rx']['threeToThree'])-3))*array_product(range(1,3)));
				}else if (count($detail ['rx'] ['threeToThree']) === 3){
					$bet=1;//刚好等于3就是一注
				}
				$info['rx']['threeToThree']['number']=implode(",",$detail['rx']['threeToThree']);
				$info['rx']['threeToThree']['bet']=$bid;
				$info['rx']['threeToThree']['time']=$time;
				$info['rx']['threeToThree']['type']='threeToThree';
				$info['rx']['threeToThree']['gold']=$bet.'注 '.($bet * $gold).'元';
				$info['rx']['threeToThree']['odds']=$odds['rx']['threeToThree'][1];
			}
			if (count($detail ['rx']['fourTofour']) > 0) {//四中四
				$bet=0;
				if (count($detail ['rx'] ['fourTofour']) > 4) {
					$bet=array_product(range(1,count($detail['rx']['fourTofour'])))
					/(array_product(range(1,count($detail['rx']['fourTofour'])-4))*array_product(range(1,4)));
				}else if (count($detail ['rx'] ['fourTofour']) === 4){
					$bet=1;
				}
				$info['rx']['fourTofour']['number']=implode(",",$detail['rx']['fourTofour']);
				$info['rx']['fourTofour']['bet']=$bid;
				$info['rx']['fourTofour']['time']=$time;
				$info['rx']['fourTofour']['type']='fourTofour';
				$info['rx']['fourTofour']['gold']=$bet.'注 '.($bet * $gold).'元';
				$info['rx']['fourTofour']['odds']=$odds['rx']['fourTofour'][1];
			}
			if (count($detail ['rx']['fiveToFive']) > 0) {//五中五
				$bet=0;
				if (count($detail ['rx'] ['fiveToFive']) > 5) {
					$bet=array_product(range(1,count($detail['rx']['fiveToFive'])))
					/(array_product(range(1,count($detail['rx']['fiveToFive'])-5))*array_product(range(1,5)));
				}else if (count($detail ['rx'] ['fiveToFive']) === 5){
					$bet=1;
				}
				$info['rx']['fiveToFive']['number']=implode(",",$detail['rx']['fiveToFive']);
				$info['rx']['fiveToFive']['bet']=$bid;
				$info['rx']['fiveToFive']['time']=$time;
				$info['rx']['fiveToFive']['type']='fiveToFive';
				$info['rx']['fiveToFive']['gold']=$bet.'注 '.($bet * $gold).'元';
				$info['rx']['fiveToFive']['odds']=$odds['rx']['fiveToFive'][1];
			}
			if (count($detail ['rx']['sixToFive']) > 0) {//六中六
				$bet=0;
				if (count($detail ['rx'] ['sixToFive']) > 6) {
					$bet=array_product(range(1,count($detail['rx']['sixToFive'])))
					/(array_product(range(1,count($detail['rx']['sixToFive'])-6))*array_product(range(1,6)));
				}else if (count($detail ['rx'] ['sixToFive']) === 6){
					$bet=1;
				}
				$info['rx']['sixToFive']['number']=implode(",",$detail['rx']['sixToFive']);
				$info['rx']['sixToFive']['bet']=$bid;
				$info['rx']['sixToFive']['time']=$time;
				$info['rx']['sixToFive']['type']='sixToFive';
				$info['rx']['sixToFive']['gold']=$bet.'注 '.($bet * $gold).'元';
				$info['rx']['sixToFive']['odds']=$odds['rx']['sixToFive'][1];
			}
			if (count($detail ['rx']['sevenToFive']) > 0) {//七中七
				$bet=0;
				if (count($detail ['rx'] ['sevenToFive']) > 7) {
					$bet=array_product(range(1,count($detail['rx']['sevenToFive'])))
					/(array_product(range(1,count($detail['rx']['sevenToFive'])-7))*array_product(range(1,7)));
				}else if (count($detail ['rx'] ['sevenToFive']) === 7){
					$bet=1;
				}
				$info['rx']['sevenToFive']['number']=implode(",",$detail['rx']['sevenToFive']);
				$info['rx']['sevenToFive']['bet']=$bid;
				$info['rx']['sevenToFive']['time']=$time;
				$info['rx']['sevenToFive']['type']='sevenToFive';
				$info['rx']['sevenToFive']['gold']=$bet.'注 '.($bet * $gold).'元';
				$info['rx']['sevenToFive']['odds']=$odds['rx']['sevenToFive'][1];
			}
			if (count($detail ['rx']['eightToFive']) > 0) {//八中八
				$bet=0;
				if (count($detail ['rx'] ['eightToFive']) > 8) {
					$bet=array_product(range(1,count($detail['rx']['eightToFive'])))
					/(array_product(range(1,count($detail['rx']['eightToFive'])-8))*array_product(range(1,8)));
				}else if (count($detail ['rx'] ['eightToFive']) === 8){
					$bet=1;
				}
				$info['rx']['eightToFive']['number']=implode(",",$detail['rx']['eightToFive']);
				$info['rx']['eightToFive']['bet']=$bid;
				$info['rx']['eightToFive']['time']=$time;
				$info['rx']['eightToFive']['type']='eightToFive';
				$info['rx']['eightToFive']['gold']=$bet.'注 '.($bet * $gold).'元';
				$info['rx']['eightToFive']['odds']=$odds['rx']['eightToFive'][1];
			}
		}
		if ((count($detail['zux'],1)-2)>0) {//组选
			if (count($detail ['zux'] ['firstTwo']) > 0) {
				$bet=0;
				if (count($detail ['zux'] ['firstTwo']) > 2) {//二中二两个为一注
					$bet=array_product(range(1,count($detail['zux'] ['firstTwo'])))
					/(array_product(range(1,count($detail['zux'] ['firstTwo'])-2))*array_product(range(1,2)));
				}else if (count($detail ['zux'] ['firstTwo']) === 2){
					$bet=1;//刚好等于2就是一注
				}
				$info['zux']['firstTwo']['number']=implode(",",$detail['zux']['firstTwo']);
				$info['zux']['firstTwo']['bet']=$bid;
				$info['zux']['firstTwo']['time']=$time;
				$info['zux']['firstTwo']['type']='firstTwo';
				$info['zux']['firstTwo']['gold']=$bet.'注 '.($bet * $gold).'元';
				$info['zux']['firstTwo']['odds']=$odds['zux']['firstTwo'][1];
			}
			if (count($detail ['zux'] ['firstThree']) > 0) {
				$bet=0;
				if (count($detail ['zux'] ['firstThree']) > 3) {//二中二两个为一注
					$bet=array_product(range(1,count($detail['zux'] ['firstThree'])))
					/(array_product(range(1,count($detail['zux'] ['firstThree'])-3))*array_product(range(1,3)));
				}else if (count($detail ['zux'] ['firstThree']) === 3){
					$bet=1;//刚好等于2就是一注
				}
				$info['zux']['firstThree']['number']=implode(",",$detail['zux']['firstThree']);
				$info['zux']['firstThree']['bet']=$bid;
				$info['zux']['firstThree']['time']=$time;
				$info['zux']['firstThree']['type']='firstThree';
				$info['zux']['firstThree']['gold']=$bet.'注 '.($bet * $gold).'元';
				$info['zux']['firstThree']['odds']=$odds['zux']['firstThree'][1];
			}
		}
		if ((count($detail['zhix'],1)-7)>0) {//直选
			if (count($detail['zhix']['firstTwo']['firstball']) >0 && count($detail['zhix']['firstTwo']['secondball']) > 0) {
				$firsttowcount=0;//第一球和第二球的相同球数
				$bet=0;
				foreach ($detail['zhix']['firstTwo']['firstball'] as  $va) {
					if (in_array($va, $detail['zhix']['firstTwo']['secondball'])) {
						$firsttowcount++;
					}
				}
				if (count($detail['zhix']['firstTwo']['firstball']) === 1 && count($detail['zhix']['firstTwo']['secondball']) === 1){
					$bet=1;//如果第一球和第二球各选了一个号码，则是一注
				}else {//否则都是第一球乘以第二球减去相同的球数=下注数
					$bet=(count($detail['zhix']['firstTwo']['firstball'])*count($detail['zhix']['firstTwo']['secondball'])-$firsttowcount);
				}
				$info['zhix']['firstTwo']['number']=implode(",",$detail['zhix']['firstTwo']['firstball']).'|'.implode(",",$detail['zhix']['firstTwo']['secondball']);
				$info['zhix']['firstTwo']['bet']=$bid;
				$info['zhix']['firstTwo']['time']=$time;
				$info['zhix']['firstTwo']['type']='firstThree';
				$info['zhix']['firstTwo']['gold']=$bet.'注 '.($bet * $gold).'元';
				$info['zhix']['firstTwo']['odds']=$odds['zhix']['firstTwo'][1];
			}
			if (count($detail['zhix']['firstThree']['firstball']) >0 && count($detail['zhix']['firstThree']['secondball']) > 0
					&& count($detail['zhix']['firstThree']['thirdball']) > 0) {
						//三个号码相乘
						$all = count($detail['zhix']['firstThree']['firstball'])*count($detail['zhix']['firstThree']['secondball'])
						*count($detail['zhix']['firstThree']['thirdball']);
						foreach ($detail['zhix']['firstThree']['firstball'] as  $vo) {
							if(in_array($vo, $detail['zhix']['firstThree']['secondball'])){
								//如果第一球和第二球有相同的球号，则减去第三球的数
								$all = $all-count($detail['zhix']['firstThree']['thirdball']);
							}
							if(in_array($vo, $detail['zhix']['firstThree']['thirdball'])){
								//如果第一球和第三球有相同的球号，则减去第二球的数
								$all = $all-count($detail['zhix']['firstThree']['secondball']);
							}
							if(in_array($vo, $detail['zhix']['firstThree']['secondball'])
									&&in_array($vo, $detail['zhix']['firstThree']['thirdball'])){
										//如果第一球和第二球并且和第三球有相同的球号,则+2
										$all = $all+2;
							}
						}
						foreach ($detail['zhix']['firstThree']['secondball'] as $vt) {
							if(in_array($vt, $detail['zhix']['firstThree']['thirdball'])){
								//如果第二球和第三球有相同的球号，则减去第一球的数
								$all = $all-count($detail['zhix']['firstThree']['firstball']);
							}
						}
						$info['zhix']['firstThree']['number']=implode(",",$detail['zhix']['firstThree']['firstball'])
						.'|'.implode(",",$detail['zhix']['firstThree']['secondball'])
						.'|'.implode(",",$detail['zhix']['firstThree']['thirdball']);
						$info['zhix']['firstThree']['bet']=$bid;
						$info['zhix']['firstThree']['time']=$time;
						$info['zhix']['firstThree']['type']='firstThree';
						$info['zhix']['firstThree']['gold']=$all.'注 '.($all * $gold).'元';
						$info['zhix']['firstThree']['odds']=$odds['zhix']['firstThree'][1];
					}
		}
		return $info;
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
		$param['surplusGold']=$data['gold'];
		unset($param['type']);
		unset($param['game']);
		$gdbet = array ( // 要插入数据库的数据
				'id' => eightOrderNumber(),
				'user_id' => $user ['id'],
				'detail' => json_encode ( $param ),
				'expect' => $param ['issue'],
				'time' => time()
		);
		$gdetail = array ( // 金币账单详情
				'当前金币' => $user ['gold'] + $user ['bank'],
				'下注扣去' => ( float ) $param ['goldCount'],
				'结果' => '-' . $param ['goldCount'],
				'剩余金币' => $data ['gold'] + $user ['bank']
		);
		$gold = array ( // 金币账单
				'username' => $user ['username'], // 用户账号
				'operation' => '广东11选5下注', // 操作说明
				'detail' => json_encode ( $gdetail ), // 账单详情
				'time' => time()
		);
		$msg=array();
		$this->startTrans (); // 开启事务
		try {
			$bet=$this->get($gdbet['id']);
			if (!$bet) {
			$this->create ( $gdbet );
			}else {
				$start=mt_rand(4,7);//生成随机数
				$substr=substr($orderNumber,$start,1);//截取
				$res=substr_replace($orderNumber,++$substr,$start,1);//替换截取到的数加1
				$gdbet['id']=$res;
				$this->create ( $gdbet );
			}
			if (User_user::where ( 'id', $gdbet ['user_id'] )->update ( $data )) {
				if (Gold_history::create ( $gold )) {
					$msg['surplusGold']=$data ['gold'];
					$msg['goldCount']=$param ['goldCount'];
					$msg['bet']=$param ['bet'];
					$this->commit (); // 成功，提交事务
				} else {
					$this->rollback ();
				}
			} else { // 失败，回滚事务
				$this->rollback ();
			}
		} catch ( \Exception $e ) {
			$this->rollback ();
		}
		return $msg;
	}
}