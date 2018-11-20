<?php
namespace app\ssc\model;
use think\Model;

/**
* 
*/
class Games_cqodds extends Model
{
	// protected $table = "bc_games_cqodds";
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
						case 0://总和大小单双  龙虎 和
							switch ($value['number']) {
								case 1:
									$odds['两面盘']['总和大']=$value['odds'];
									break;
								case 2:
									$odds['两面盘']['总和小']=$value['odds'];
									break;
								case 3:
									$odds['两面盘']['总和单']=$value['odds'];
									break;
								case 4:
									$odds['两面盘']['总和双']=$value['odds'];
									break;
								case 5:
									$odds['两面盘']['龙']=$value['odds'];
									break;
								case 6:
									$odds['两面盘']['虎']=$value['odds'];
									break;
								case 7:
									$odds['两面盘']['和']=$value['odds'];
									break;
							}
							break;
						case 1://['两面盘']第一球万位的赔率
							switch ($value['number']) {
								case 1:
									$odds['两面盘']['第一球']['大']=$value['odds'];
									break;
								case 2:
									$odds['两面盘']['第一球']['小']=$value['odds'];
									break;
								case 3:
									$odds['两面盘']['第一球']['单']=$value['odds'];
									break;
								case 4:
									$odds['两面盘']['第一球']['双']=$value['odds'];
									break;
								case 5:
									$odds['两面盘']['第一球']['质']=$value['odds'];
									break;
								case 6:
									$odds['两面盘']['第一球']['合']=$value['odds'];
									break;
							};
							break;
						case 2://['两面盘']第二球千位的赔率
							switch ($value['number']) {
								case 1:
									$odds['两面盘']['第二球']['大']=$value['odds'];
									break;
								case 2:
									$odds['两面盘']['第二球']['小']=$value['odds'];
									break;
								case 3:
									$odds['两面盘']['第二球']['单']=$value['odds'];
									break;
								case 4:
									$odds['两面盘']['第二球']['双']=$value['odds'];
									break;
								case 5:
									$odds['两面盘']['第二球']['质']=$value['odds'];
									break;
								case 6:
									$odds['两面盘']['第二球']['合']=$value['odds'];
									break;
							};
							break;
						case 3://['两面盘']第三球百位的赔率
							switch ($value['number']) {
								case 1:
									$odds['两面盘']['第三球']['大']=$value['odds'];
									break;
								case 2:
									$odds['两面盘']['第三球']['小']=$value['odds'];
									break;
								case 3:
									$odds['两面盘']['第三球']['单']=$value['odds'];
									break;
								case 4:
									$odds['两面盘']['第三球']['双']=$value['odds'];
									break;
								case 5:
									$odds['两面盘']['第三球']['质']=$value['odds'];
									break;
								case 6:
									$odds['两面盘']['第三球']['合']=$value['odds'];
									break;
							};
							break;
						case 4://['两面盘']第四球十位的赔率
							switch ($value ['number']) {
								case 1 :
									$odds ['两面盘'] ['第四球'] ['大'] = $value ['odds'];
									break;
								case 2 :
									$odds ['两面盘'] ['第四球'] ['小'] = $value ['odds'];
									break;
								case 3 :
									$odds ['两面盘'] ['第四球'] ['单'] = $value ['odds'];
									break;
								case 4 :
									$odds ['两面盘'] ['第四球'] ['双'] = $value ['odds'];
									break;
								case 5 :
									$odds ['两面盘'] ['第四球'] ['质'] = $value ['odds'];
									break;
								case 6 :
									$odds ['两面盘'] ['第四球'] ['合'] = $value ['odds'];
									break;
							};
							break;
						case 5://['两面盘']第五球个位的赔率
							switch ($value ['number']) {
								case 1 :
									$odds ['两面盘'] ['第五球'] ['大'] = $value ['odds'];
									break;
								case 2 :
									$odds ['两面盘'] ['第五球'] ['小'] = $value ['odds'];
									break;
								case 3 :
									$odds ['两面盘'] ['第五球'] ['单'] = $value ['odds'];
									break;
								case 4 :
									$odds ['两面盘'] ['第五球'] ['双'] = $value ['odds'];
									break;
								case 5 :
									$odds ['两面盘'] ['第五球'] ['质'] = $value ['odds'];
									break;
								case 6 :
									$odds ['两面盘'] ['第五球'] ['合'] = $value ['odds'];
									break;
							};
							break;
						case 6://['两面盘']前三
							switch ($value ['number']) {
								case 1 :
									$odds ['两面盘'] ['前三'] ['豹子'] = $value ['odds'];
									break;
								case 2 :
									$odds ['两面盘'] ['前三'] ['顺子'] = $value ['odds'];
									break;
								case 3 :
									$odds ['两面盘'] ['前三'] ['对子'] = $value ['odds'];
									break;
								case 4 :
									$odds ['两面盘'] ['前三'] ['半顺'] = $value ['odds'];
									break;
								case 5 :
									$odds ['两面盘'] ['前三'] ['杂六'] = $value ['odds'];
									break;
							};
							break;
						case 7://['两面盘']中三
							switch ($value ['number']) {
								case 1 :
									$odds ['两面盘'] ['中三'] ['豹子'] = $value ['odds'];
									break;
								case 2 :
									$odds ['两面盘'] ['中三'] ['顺子'] = $value ['odds'];
									break;
								case 3 :
									$odds ['两面盘'] ['中三'] ['对子'] = $value ['odds'];
									break;
								case 4 :
									$odds ['两面盘'] ['中三'] ['半顺'] = $value ['odds'];
									break;
								case 5 :
									$odds ['两面盘'] ['中三'] ['杂六'] = $value ['odds'];
									break;
							};
							break;
						case 8://['两面盘']后三
							switch ($value ['number']) {
								case 1 :
									$odds ['两面盘'] ['后三'] ['豹子'] = $value ['odds'];
									break;
								case 2 :
									$odds ['两面盘'] ['后三'] ['顺子'] = $value ['odds'];
									break;
								case 3 :
									$odds ['两面盘'] ['后三'] ['对子'] = $value ['odds'];
									break;
								case 4 :
									$odds ['两面盘'] ['后三'] ['半顺'] = $value ['odds'];
									break;
								case 5 :
									$odds ['两面盘'] ['后三'] ['杂六'] = $value ['odds'];
									break;
							};
							break;
					}
					break;
				case 2://1~5----↓
					switch ($value['sort']) {
						case 1://第一球
							switch ($value['number']) {
								case 0:
									$odds['一至五'][0]=$value['odds'];
									break;
								case 1:
									$odds['一至五']['大']=$value['odds'];
									break;
								case 2:
									$odds['一至五']['小']=$value['odds'];
									break;
								case 3:
									$odds['一至五']['单']=$value['odds'];
									break;
								case 4:
									$odds['一至五']['双']=$value['odds'];
									break;
								case 5:
									$odds['一至五']['质']=$value['odds'];
									break;
								case 6:
									$odds['一至五']['合']=$value['odds'];
									break;
							}
							break;
					}//1~5---↑
					break;
				case 3://一字
					switch ($value['sort']) {
						case 1://前三
							switch ($value['number']) {
								case 0:
									$odds['一字']['前三']=$value['odds'];
									break;
							}//前三
							break;
						case 2://中三
							switch ($value['number']) {
								case 0:
									$odds['一字']['中三']=$value['odds'];
									break;
							}//中三
							break;
						case 3://后三
							switch ($value['number']) {
								case 0:
									$odds['一字']['后三']=$value['odds'];
									break;
							}//后三
							break;
						case 4://全五
							switch ($value['number']) {
								case 0:
									$odds['一字']['全五']=$value['odds'];
									break;
							}//全五
							break;
					}//一字
					break;
				case 4://二字
					switch ($value['sort']) {
						case 1:
							switch ($value['number']) {
								case 0:
									$odds['二字'][0]=$value['odds'];
									break;
								case 1:
									$odds['二字'][1]=$value['odds'];
									break;
							}
							break;
					}//二字
					break;
				case 5://三字
					switch ($value['sort']) {
						case 1:
							switch ($value['number']) {
								case 0:
									$odds['三字'][0]=$value['odds'];
									break;
								case 1:
									$odds['三字'][1]=$value['odds'];
									break;
								case 2:
									$odds['三字'][2]=$value['odds'];
									break;
							}
							break;
					}//三字
					break;
				case 6://二定位
					switch ($value['sort']) {
						case 1:
							switch ($value['number']) {
								case 0:
									$odds['二定位']=$value['odds'];
									break;
							}
							break;
					}//二定位
					break;
				case 7://三定位
					switch ($value['sort']) {
						case 1:
							switch ($value['number']) {
								case 0:
									$odds['三定位']=$value['odds'];
									break;
							}
							break;
					}//三定位
					break;
				case 8://组选三
					switch ($value['sort']) {
						case 1:
							switch ($value['number']) {
								case 0:
									$odds['组选三']=$value['odds'];
									break;
							}
							break;
					}//组选三
					break;
				case 9://组选六
					switch ($value['sort']) {
						case 1:
							switch ($value['number']) {
								case 0:
									$odds['组选六']=$value['odds'];
									break;
							}
							break;
					}//组选六
					break;
				case 9://组选六
					switch ($value['sort']) {
						case 1:
							switch ($value['number']) {
								case 0:
									$odds['组选六']=$value['odds'];
									break;
							}
							break;
					}//组选六
					break;
				case 10://跨度
					switch ($value['sort']) {
						case 1:
							switch ($value['number']) {
								case 0:
									$odds['跨度']=$value['odds'];
									break;
							}
							break;
					}//跨度
					break;
				case 11://和数
					switch ($value['sort']) {
						case 1://单
							switch ($value['number']) {
								case 0:
									$odds['和数']['单']=$value['odds'];
									break;
							}
							break;
						case 2://双
							switch ($value['number']) {
								case 0:
									$odds['和数']['双']=$value['odds'];
									break;
							}
							break;
					}//和数
					break;
				case 12://牛牛
					switch ($value['sort']) {
						case 1://双面
							switch ($value['number']) {
								case 1:
									$odds['牛牛双面']['大']=$value['odds'];
									break;
								case 2:
									$odds['牛牛双面']['小']=$value['odds'];
									break;
								case 3:
									$odds['牛牛双面']['单']=$value['odds'];
									break;
								case 4:
									$odds['牛牛双面']['双']=$value['odds'];
									break;
								case 5:
									$odds['牛牛双面']['质']=$value['odds'];
									break;
								case 6:
									$odds['牛牛双面']['合']=$value['odds'];
									break;
							}
							break;
						case 2://点数
							switch ($value['number']) {
								case 0:
									$odds['牛牛点数'][0]=$value['odds'];
									break;
								case 1:
									$odds['牛牛点数']['无牛']=$value['odds'];
									break;
								case 2:
									$odds['牛牛点数']['牛牛']=$value['odds'];
									break;
							}
							break;
						case 3://梭哈
							switch ($value['number']) {
								case 1:
									$odds['牛牛梭哈']['五条']=$value['odds'];
									break;
								case 2:
									$odds['牛牛梭哈']['炸弹']=$value['odds'];
									break;
								case 3:
									$odds['牛牛梭哈']['葫芦']=$value['odds'];
									break;
								case 4:
									$odds['牛牛梭哈']['顺子']=$value['odds'];
									break;
								case 5:
									$odds['牛牛梭哈']['三条']=$value['odds'];
									break;
								case 6:
									$odds['牛牛梭哈']['两对']=$value['odds'];
									break;
								case 7:
									$odds['牛牛梭哈']['单对']=$value['odds'];
									break;
								case 8:
									$odds['牛牛梭哈']['散号']=$value['odds'];
									break;
							}
							break;
					}//牛牛
					break;
			}//玩法----↑
		}
		return $odds;
	}
}