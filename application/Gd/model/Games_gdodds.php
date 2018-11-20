<?php
namespace app\gd\model;
use think\Model;
use think\facade\Cache;
/**
* 
*/
class Games_gdodds extends Model
{
	// protected $table = "bc_games_gdodds";
	

	/**
	 * 获取赔率
	 */
	public function getOddsList() {
		$odds=$this->select();
		dump($odds);
		$res=array();
		foreach ($odds as $key => $value) {
			switch ($value['type']) {
				case 1://两面盘
					switch ($value['sort']) {
						case -1:
							$res['lmp']['allId']=$value['id'];
							$res['lmp']['alls']=$value['odds'];
							break;
						case 1:
							$res['lmp']['numId']=$value['id'];
							$res['lmp']['number']=$value['odds'];
							break;
					}
					break;
				case 2://单码
					if ($value['sort'] === 1) {
						$res['dmId']=$value['id'];
						$res['dm']=$value['odds'];
					}
					break;
				case 3://任选
					switch ($value['sort']) {
						case 1:
							$res['rx']['oneId']=$value['id'];
							$res['rx']['oneToOne']=$value['odds'];
							break;
						case 2:
							$res['rx']['twoId']=$value['id'];
							$res['rx']['twoToTwo']=$value['odds'];
							break;
						case 3:
							$res['rx']['threeId']=$value['id'];
							$res['rx']['threeToThree']=$value['odds'];
							break;
						case 4:
							$res['rx']['fourId']=$value['id'];
							$res['rx']['fourTofour']=$value['odds'];
							break;
						case 5:
							$res['rx']['fiveId']=$value['id'];
							$res['rx']['fiveToFive']=$value['odds'];
							break;
						case 6:
							$res['rx']['sixId']=$value['id'];
							$res['rx']['sixToFive']=$value['odds'];
							break;
						case 7:
							$res['rx']['sevenId']=$value['id'];
							$res['rx']['sevenToFive']=$value['odds'];
							break;
						case 8:
							$res['rx']['eightId']=$value['id'];
							$res['rx']['eightToFive']=$value['odds'];
							break;
					}
					break;
				case 4://组选
					switch ($value['sort']) {
						case 2://前二
							$res['zux']['zuxeId']=$value['id'];
							$res['zux']['firstTwo']=$value['odds'];
							break;
						case 3://前三
							$res['zux']['zuxsId']=$value['id'];
							$res['zux']['firstThree']=$value['odds'];
							break;
					}
					break;
				case 5://直选
					switch ($value['sort']) {
						case 2://前二
							$res['zhix']['zhixeId']=$value['id'];
							$res['zhix']['firstTwo']=$value['odds'];
							break;
						case 3://前三
							$res['zhix']['zhixsId']=$value['id'];
							$res['zhix']['firstThree']=$value['odds'];
							break;
					}
					break;
			};
		}
		Cache::set('gdOddsListCache', $res);
		return $res;
	}
	
	/**
	 * 获取赔率
	 */
	public function getOdds() {
		$oddsinfo=$this->select();
		$odds=array();
		foreach ( $oddsinfo as $key => $value ) {
			switch ($value['type']) {
				case 1://双面盘
					switch ($value['sort']) {//判断种类
						case -1://totalBig小单双 总和尾大小单双 龙虎
									$odds['lmp']['totalBig']=$value['odds'];
									$odds['lmp']['totalSmall']=$value['odds'];
									$odds['lmp']['totalOne']=$value['odds'];
									$odds['lmp']['totalBoth']=$value['odds'];
									$odds['lmp']['endBig']=$value['odds'];
									$odds['lmp']['endSmall']=$value['odds'];
									$odds['lmp']['tiger']=$value['odds'];
									$odds['lmp']['dragon']=$value['odds'];
									$odds['lmp']['endOne']=$value['odds'];
									$odds['lmp']['endBoth']=$value['odds'];
							break;
						case 1://['lmp']firstball的赔率
									$odds['lmp']['firstball']['big']=$value['odds'];
									$odds['lmp']['firstball']['small']=$value['odds'];
									$odds['lmp']['firstball']['One']=$value['odds'];
									$odds['lmp']['firstball']['Both']=$value['odds'];
									$odds['lmp']['secondball']['big']=$value['odds'];
									$odds['lmp']['secondball']['small']=$value['odds'];
									$odds['lmp']['secondball']['One']=$value['odds'];
									$odds['lmp']['secondball']['Both']=$value['odds'];
									$odds['lmp']['thirdball']['big']=$value['odds'];
									$odds['lmp']['thirdball']['small']=$value['odds'];
									$odds['lmp']['thirdball']['One']=$value['odds'];
									$odds['lmp']['thirdball']['Both']=$value['odds'];
									$odds['lmp']['thouthball']['big']=$value['odds'];
									$odds['lmp']['thouthball']['small']=$value['odds'];
									$odds['lmp']['thouthball']['One']=$value['odds'];
									$odds['lmp']['thouthball']['Both']=$value['odds'];
									$odds['lmp']['fifthball']['big']=$value['odds'];
									$odds['lmp']['fifthball']['small']=$value['odds'];
									$odds['lmp']['fifthball']['One']=$value['odds'];
									$odds['lmp']['fifthball']['Both']=$value['odds'];
					break;
					};
				case 2://dm----↓
					switch ($value['sort']) {
						case 1://firstball
									$odds['dm']['firstball'][1]=$value['odds'];
									$odds['dm']['firstball'][2]=$value['odds'];
									$odds['dm']['firstball'][3]=$value['odds'];
									$odds['dm']['firstball'][4]=$value['odds'];
									$odds['dm']['firstball'][5]=$value['odds'];
									$odds['dm']['firstball'][6]=$value['odds'];
									$odds['dm']['firstball'][7]=$value['odds'];
									$odds['dm']['firstball'][8]=$value['odds'];
									$odds['dm']['firstball'][9]=$value['odds'];
									$odds['dm']['firstball'][10]=$value['odds'];
									$odds['dm']['firstball'][11]=$value['odds'];
									$odds['dm']['secondball'][1]=$value['odds'];
									$odds['dm']['secondball'][2]=$value['odds'];
									$odds['dm']['secondball'][3]=$value['odds'];
									$odds['dm']['secondball'][4]=$value['odds'];
									$odds['dm']['secondball'][5]=$value['odds'];
									$odds['dm']['secondball'][6]=$value['odds'];
									$odds['dm']['secondball'][7]=$value['odds'];
									$odds['dm']['secondball'][8]=$value['odds'];
									$odds['dm']['secondball'][9]=$value['odds'];
									$odds['dm']['secondball'][10]=$value['odds'];
									$odds['dm']['secondball'][11]=$value['odds'];
									$odds['dm']['thirdball'][1]=$value['odds'];
									$odds['dm']['thirdball'][2]=$value['odds'];
									$odds['dm']['thirdball'][3]=$value['odds'];
									$odds['dm']['thirdball'][4]=$value['odds'];
									$odds['dm']['thirdball'][5]=$value['odds'];
									$odds['dm']['thirdball'][6]=$value['odds'];
									$odds['dm']['thirdball'][7]=$value['odds'];
									$odds['dm']['thirdball'][8]=$value['odds'];
									$odds['dm']['thirdball'][9]=$value['odds'];
									$odds['dm']['thirdball'][10]=$value['odds'];
									$odds['dm']['thirdball'][11]=$value['odds'];
									$odds['dm']['thouthball'][1]=$value['odds'];
									$odds['dm']['thouthball'][2]=$value['odds'];
									$odds['dm']['thouthball'][3]=$value['odds'];
									$odds['dm']['thouthball'][4]=$value['odds'];
									$odds['dm']['thouthball'][5]=$value['odds'];
									$odds['dm']['thouthball'][6]=$value['odds'];
									$odds['dm']['thouthball'][7]=$value['odds'];
									$odds['dm']['thouthball'][8]=$value['odds'];
									$odds['dm']['thouthball'][9]=$value['odds'];
									$odds['dm']['thouthball'][10]=$value['odds'];
									$odds['dm']['thouthball'][11]=$value['odds'];
									$odds['dm']['fifthball'][1]=$value['odds'];
									$odds['dm']['fifthball'][2]=$value['odds'];
									$odds['dm']['fifthball'][3]=$value['odds'];
									$odds['dm']['fifthball'][4]=$value['odds'];
									$odds['dm']['fifthball'][5]=$value['odds'];
									$odds['dm']['fifthball'][6]=$value['odds'];
									$odds['dm']['fifthball'][7]=$value['odds'];
									$odds['dm']['fifthball'][8]=$value['odds'];
									$odds['dm']['fifthball'][9]=$value['odds'];
									$odds['dm']['fifthball'][10]=$value['odds'];
									$odds['dm']['fifthball'][11]=$value['odds'];
					}//dm---↑
					break;
				case 3://rx
					switch ($value['sort']) {
						case 1://一中一
							switch ($value['number']) {
								case 1:
									$odds['rx']['oneToOne'][1]=$value['odds'];
									break;
							}//一中一
							break;
						case 2://二中二
							switch ($value['number']) {
								case 1:
									$odds['rx']['twoToTwo'][1]=$value['odds'];
									break;
							}//二中二
							break;
						case 3://三中三
							switch ($value['number']) {
								case 1:
									$odds['rx']['threeToThree'][1]=$value['odds'];
									break;
							}//三中三
							break;
						case 4://四中四
							switch ($value['number']) {
								case 1:
									$odds['rx']['fourTofour'][1]=$value['odds'];
									break;
							}//四中四
							break;
						case 5://五中五
							switch ($value['number']) {
								case 1:
									$odds['rx']['fiveToFive'][1]=$value['odds'];
									break;
							}//五中五
							break;
						case 6://六中五
							switch ($value['number']) {
								case 1:
									$odds['rx']['sixToFive'][1]=$value['odds'];
									break;
							}//六中五
							break;
						case 7://七中五
							switch ($value['number']) {
								case 1:
									$odds['rx']['sevenToFive'][1]=$value['odds'];
									break;
							}//七中五
							break;
						case 8://八中五
							switch ($value['number']) {
								case 1:
									$odds['rx']['eightToFive'][1]=$value['odds'];
									break;
							}//八中五
							break;
					}//rx
					break;
				case 4://组选
					switch ($value['sort']) {
						case 2://前二
							switch ($value['number']) {
								case 1:
									$odds['zux']['firstTwo'][1]=$value['odds'];
									break;
							}//前二
						break;
						case 3://前三
							switch ($value['number']) {
								case 1:
									$odds['zux']['firstThree'][1]=$value['odds'];
									break;
							}//前三
						break;
					}//组选
					break;
					case 5://直选
						switch ($value['sort']) {
						case 2://前二
							switch ($value['number']) {
								case 1:
									$odds['zhix']['firstTwo'][1]=$value['odds'];
									break;
							}//前二
						break;
						case 3://前三
							switch ($value['number']) {
								case 1:
									$odds['zhix']['firstThree'][1]=$value['odds'];
									break;
							}//前三
						break;
					}//直选
					break;
			}//玩法----↑
		}
		Cache::set('gdOddsCache', $odds);
		return $odds;
	}
}