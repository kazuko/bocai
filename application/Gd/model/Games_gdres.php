<?php

namespace app\gd\model;

use think\Model;
use app\forum\model\Gold_history;
use app\forum\model\User_user;

/**
 */
class Games_gdres extends Model {
	// protected $table = "bc_games_gdlmp";
	function saveResult($param, $odds) {
		foreach ($param as $key => $value) {
		$result = explode ( ",", $value ['opencode'] );
		$iswin = 0; // 赢了几注
		$value ['win'] ['单注金额'] = $value ['gold'];
		$value ['gold']=0;
		// -----------------------------两面盘（计算中奖结果和赔率）-------------------------------(☄⊙ω⊙)☄
		// 总和大小
		if ((count($value ['lmp'],1)-6) > 0) {
		if (count($value ['lmp']['alls']) >0) {
			//通过用对换键值对的方法来isset判断变量是否存在，其效率比in_array高很多，内存占用也少
			$value['lmp']['alls'] = array_flip($value['lmp']['alls']);
		if ((array_sum ( $result ) === 30 && isset($value['lmp']['alls']['totalBig'] )) || (array_sum ( $result ) === 30 && isset($value['lmp']['alls']['totalSmall'] ))) {
			$value ['win'] ['两面盘'] ['总和大小'] = '总和和';
		} else if (array_sum ( $result ) > 30 && isset($value['lmp']['alls']['totalBig'] )) {
			$value ['win'] ['两面盘'] ['总和大小'] = '总和大';
			$iswin ++;
			$value ['gold'] +=($odds ['lmp'] ['totalBig'] * $value ['win'] ['单注金额']);
		} else if (array_sum ( $result ) < 30 && isset($value['lmp']['alls']['totalSmall'] )) {
			$value ['win'] ['两面盘'] ['总和大小'] = '总和小';
			$iswin ++;
			$value ['gold'] += ($odds ['lmp'] ['totalSmall'] * $value ['win'] ['单注金额']);
		} // 总和单双
		if (array_sum ( $result ) % 2 === 0 && isset($value['lmp']['alls']['totalBoth'] )) {
			$value ['win'] ['两面盘'] ['总和单双'] = '双';
			$iswin ++;
			$value ['gold'] += ($odds ['lmp'] ['totalBoth'] * $value ['win'] ['单注金额']);
		} else if (array_sum ( $result ) % 2 !== 0 && isset($value['lmp']['alls']['totalOne'] )) {
			$value ['win'] ['两面盘'] ['总和单双'] = '单';
			$iswin ++;
			$value ['gold'] += ($odds ['lmp'] ['totalOne'] * $value ['win'] ['单注金额']);
		}
		// 总和尾大小
		if (((array_sum ( $result ) % 10) === 30 && isset($value['lmp']['alls']['endBig'] )) || ((array_sum ( $result ) % 10) === 30 && isset($value['lmp']['alls']['endSmall'] ))) {
			$value ['win'] ['两面盘'] ['总和尾大小'] = '总和尾和';
		} else if ((array_sum ( $result ) % 10) >= 5 && isset($value['lmp']['alls']['endBig'] )) {
			$value ['win'] ['两面盘'] ['总和尾大小'] = '总和尾大';
			$iswin ++;
			$value ['gold'] += ($odds ['lmp'] ['endBig'] * $value ['win'] ['单注金额']);
		} else if ((array_sum ( $result ) % 10) <= 4 && isset($value['lmp']['alls']['endSmall'] )) {
			$value ['win'] ['两面盘'] ['总和尾大小'] = '总和尾小';
			$iswin ++;
			$value ['gold'] +=($odds ['lmp'] ['endSmall'] * $value ['win'] ['单注金额']);
		} // 总和尾单双
		if ((array_sum ( $result ) % 10) % 2 === 0 && isset($value['lmp']['alls']['endBoth'] )) {
			$value ['win'] ['两面盘'] ['总和尾单双'] = '双';
			$iswin ++;
			$value ['gold'] += ($odds ['lmp'] ['endBoth'] * $value ['win'] ['单注金额']);
		} else if ((array_sum ( $result ) % 10) % 2 !== 0 && isset($value['lmp']['alls']['endOne'] )) {
			$value ['win'] ['两面盘'] ['总和尾单双'] = '单';
			$iswin ++;
			$value ['gold'] += ($odds ['lmp'] ['endOne'] * $value ['win'] ['单注金额']);
		}
		// 龙虎
		if (( int ) $result [0] > ( int ) $result [4] && isset($value['lmp']['alls']['dragon'] )) {
			$value ['win'] ['两面盘'] ['龙虎'] = '龙';
			$iswin ++;
			$value ['gold'] += ($odds ['lmp'] ['dragon'] * $value ['win'] ['单注金额']);
		} else if (( int ) $result [0] < ( int ) $result [4] && isset($value['lmp']['alls']['tiger'] )) {
			$value ['win'] ['两面盘'] ['龙虎'] = '虎';
			$iswin ++;
			$value ['gold'] += ($odds ['lmp'] ['tiger'] * $value ['win'] ['单注金额']);
		}
		}
		// -------第一球
		if (count($value ['lmp']['firstball']) > 0) {
			//通过用对换键值对的方法来isset判断变量是否存在，其效率比in_array高很多，内存占用也少
			$value['lmp']['firstball'] = array_flip($value['lmp']['firstball']);
		if ((( int ) $result [0] === 11 && isset($value['lmp']['firstball']['big'] )) || (( int ) $result [0] === 11 && isset($value['lmp']['firstball']['small'] ))) {
			$value ['win'] ['两面盘'] ['第一球大小'] = '和';
		} else {
			if (( int ) $result [0] >= 6 && isset($value['lmp']['firstball']['big'] )) {
				$iswin ++;
				$value ['win'] ['两面盘'] ['第一球大小'] = '大';
				$value ['gold'] += ($odds ['lmp']['firstball']['big'] * $value ['win'] ['单注金额']);
			} else if (( int ) $result [0] <= 5 && isset($value['lmp']['firstball']['small'] )) {
				$value ['win'] ['两面盘'] ['第一球大小'] = '小';
				$iswin ++;
				$value ['gold'] += ($odds ['lmp']['firstball']['small'] * $value ['win'] ['单注金额']);
			}
			if (( int ) $result [0] % 2 === 0 && isset($value['lmp']['firstball']['Both'] ) && ( int ) $result [0] !== 11) {
				$iswin ++;
				$value ['win'] ['两面盘'] ['第一球单双'] = '双';
				$value ['gold'] += ($odds ['lmp']['firstball']['Both'] * $value ['win'] ['单注金额']) ;
			} else if (( int ) $result [0] % 2 !== 0 && isset($value['lmp']['firstball']['One'] ) && ( int ) $result [0] !== 11) {
				$iswin ++;
				$value ['win'] ['两面盘'] ['第一球单双'] = '单';
				$value ['gold'] += ($odds ['lmp']['firstball']['One'] * $value ['win'] ['单注金额']) ;
			}
		}
		}
		// -------第二球
		if (count($value ['lmp']['secondball']) > 0) {
			//通过用对换键值对的方法来isset判断变量是否存在，其效率比in_array高很多，内存占用也少
			$value['lmp']['secondball'] = array_flip($value['lmp']['secondball']);
		if ((( int ) $result [1] === 11 && isset($value['lmp']['secondball']['big'] )) || (( int ) $result [1] === 11 && isset($value['lmp']['secondball']['small'] ))) {
			$value ['win'] ['两面盘'] ['第二球大小'] = '和';
		} else {
			if (( int ) $result [1] >= 6 && isset($value['lmp']['secondball']['big'] )) {
				$iswin ++;
				$value ['win'] ['两面盘'] ['第二球大小'] = '大';
				$value ['gold'] += ($odds ['lmp']['secondball']['big'] * $value ['win'] ['单注金额']);
			} else if (( int ) $result [1] <= 5 && isset($value['lmp']['secondball']['small'] )) {
				$iswin ++;
				$value ['win'] ['两面盘'] ['第二球大小'] = '小';
				$value ['gold'] += ($odds ['lmp']['secondball']['small'] * $value ['win'] ['单注金额']);
			}
			if (( int ) $result [1] % 2 === 0 && isset($value['lmp']['secondball']['Both'] )) {
				$iswin ++;
				$value ['win'] ['两面盘'] ['第二球单双'] = '双';
				$value ['gold'] += ($odds ['lmp']['secondball']['Both'] * $value ['win'] ['单注金额']) ;
			} else if (( int ) $result [1] % 2 !== 0 && isset($value['lmp']['secondball']['One'] )) {
				$iswin ++;
				$value ['win'] ['两面盘'] ['第二球单双'] = '单';
				$value ['gold'] += ($odds ['lmp']['secondball']['One'] * $value ['win'] ['单注金额']);
			}
		}
		}
		// -------第三球
		if (count($value ['lmp']['thirdball']) > 0) {
			//通过用对换键值对的方法来isset判断变量是否存在，其效率比in_array高很多，内存占用也少
			$value['lmp']['thirdball'] = array_flip($value['lmp']['thirdball']);
		if ((( int ) $result [2] === 11 && isset($value['lmp']['thirdball']['big'] )) || (( int ) $result [2] === 11 && isset($value['lmp']['thirdball']['small'] ))) {
			$value ['win'] ['两面盘'] ['第三球大小'] = '和';
		} else {
			if (( int ) $result [2] >= 6 && isset($value['lmp']['thirdball']['big'] )) {
				$iswin ++;
				$value ['win'] ['两面盘'] ['第三球大小'] = '大';
				$value ['gold'] += ($odds ['lmp']['thirdball']['big'] * $value ['win'] ['单注金额']);
			} else if (( int ) $result [2] <= 5 && isset($value['lmp']['thirdball']['small'] )) {
				$iswin ++;
				$value ['win'] ['两面盘'] ['第三球大小'] = '小';
				$value ['gold'] += ($odds ['lmp']['thirdball']['small'] * $value ['win'] ['单注金额']) ;
			}
			if (( int ) $result [2] % 2 === 0 && isset($value['lmp']['thirdball']['Both'] )) {
				$iswin ++;
				$value ['win'] ['两面盘'] ['第三球单双'] = '双';
				$value ['gold'] += ($odds ['lmp']['thirdball']['Both'] * $value ['win'] ['单注金额']) ;
			} else if (( int ) $result [2] % 2 !== 0 && isset($value['lmp']['thirdball']['One'] )) {
				$iswin ++;
				$value ['win'] ['两面盘'] ['第三球单双'] = '单';
				$value ['gold'] += ($odds ['lmp']['thirdball']['One'] * $value ['win'] ['单注金额']) ;
			}
		}
		}
		// -------第四球
		if (count($value ['lmp']['thouthball']) > 0) {
			//通过用对换键值对的方法来isset判断变量是否存在，其效率比in_array高很多，内存占用也少
			$value['lmp']['thouthball'] = array_flip($value['lmp']['thouthball']);
		if ((( int ) $result [3] === 11 && isset($value['lmp']['thouthball']['big'] )) || (( int ) $result [3] === 11 && isset($value['lmp']['thouthball']['small'] ))) {
			$value ['win'] ['两面盘'] ['第四球大小'] = '和';
		} else {
			if (( int ) $result [3] >= 6 && isset($value['lmp']['thouthball']['big'] )) {
				$iswin ++;
				$value ['win'] ['两面盘'] ['第四球大小'] = '大';
				$value ['gold'] += ($odds ['lmp']['thouthball']['big'] * $value ['win'] ['单注金额']);
			} else if (( int ) $result [3] <= 5 && isset($value['lmp']['thouthball']['small'] )) {
				$iswin ++;
				$value ['win'] ['两面盘'] ['第四球大小'] = '小';
				$value ['gold'] += ($odds ['lmp']['thouthball']['small'] * $value ['win'] ['单注金额']);
			}
			if (( int ) $result [3] % 2 === 0 && isset($value['lmp']['thouthball']['Both'] )) {
				$iswin ++;
				$value ['win'] ['两面盘'] ['第四球单双'] = '双';
				$value ['gold'] += ($odds ['lmp']['thouthball']['Both'] * $value ['win'] ['单注金额']);
			} else if (( int ) $result [3] % 2 !== 0 && isset($value['lmp']['thouthball']['One'] )) {
				$iswin ++;
				$value ['win'] ['两面盘'] ['第四球单双'] = '单';
				$value ['gold'] += ($odds ['lmp']['thouthball']['One'] * $value ['win'] ['单注金额']);
			}
		}
		}
		// -------第五球
		if (count($value ['lmp']['fifthball']) > 0) {
			//通过用对换键值对的方法来isset判断变量是否存在，其效率比in_array高很多，内存占用也少
			$value['lmp']['fifthball'] = array_flip($value['lmp']['fifthball']);
		if ((( int ) $result [4] === 11 && isset($value['lmp']['fifthball']['big'] )) || (( int ) $result [4] === 11 && isset($value['lmp']['fifthball']['small'] ))) {
			$value ['win'] ['两面盘'] ['第五球大小'] = '和';
		} else {
			if (( int ) $result [4] >= 6 && isset($value['lmp']['fifthball']['big'] )) {
				$iswin ++;
				$value ['win'] ['两面盘'] ['第五球大小'] = '大';
				$value ['gold'] += ($odds ['lmp']['fifthball']['big'] * $value ['win'] ['单注金额']) ;
			} else if (( int ) $result [4] <= 5 && isset($value['lmp']['fifthball']['small'] )) {
				$iswin ++;
				$value ['win'] ['两面盘'] ['第五球大小'] = '小';
				$value ['gold'] += ($odds ['lmp']['fifthball']['small'] * $value ['win'] ['单注金额']);
			}
			if (( int ) $result [4] % 2 === 0 && isset($value['lmp']['fifthball']['Both'] )) {
				$iswin ++;
				$value ['win'] ['两面盘'] ['第五球单双'] = '双';
				$value ['gold'] += ($odds ['lmp']['fifthball']['Both'] * $value ['win'] ['单注金额']);
			} else if (( int ) $result [4] % 2 !== 0 && isset($value['lmp']['fifthball']['One'] )) {
				$iswin ++;
				$value ['win'] ['两面盘'] ['第五球单双'] = '单';
				$value ['gold'] += ($odds ['lmp']['fifthball']['One'] * $value ['win'] ['单注金额']);
			}}}}
// 		-----------------------------两面盘-------------------------------↑
// 		-----------------------------单码-------------------------------(☄⊙ω⊙)☄
// 		-----------------------------第一球---------(☄⊙ω⊙)☄
		if ((count($value ['dm'],1)-5) > 0) {
		if (count($value ['dm']['firstball']) > 0) {
			//通过用对换键值对的方法来isset判断变量是否存在，其效率比in_array高很多，内存占用也少
			$value['dm']['firstball'] = array_flip($value['dm']['firstball']);
		switch (( int )$result[0]) {
			case 1:
				if (isset($value['dm']['firstball'][1] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['firstball'] [1] * $value ['win'] ['单注金额']);
				}
			break;
			case 2:
				if (isset($value['dm']['firstball'][2] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['firstball'] [2] * $value ['win'] ['单注金额']);
				}
			break;
			case 3:
				if (isset($value['dm']['firstball'][3] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['firstball'] [3] * $value ['win'] ['单注金额']);
				}
			break;
			case 4:
				if (isset($value['dm']['firstball'][4] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['firstball'] [4] * $value ['win'] ['单注金额']);
				}
			break;
			case 5:
				if (isset($value['dm']['firstball'][5] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['firstball'] [5] * $value ['win'] ['单注金额']);
				}
			break;
			case 6:
				if (isset($value['dm']['firstball'][6] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['firstball'] [6] * $value ['win'] ['单注金额']) ;
				}
			break;
			case 7:
				if (isset($value['dm']['firstball'][7] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['firstball'] [7] * $value ['win'] ['单注金额']);
				}
			break;
			case 8:
				if (isset($value['dm']['firstball'][8] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['firstball'] [8] * $value ['win'] ['单注金额']) ;
				}
			break;
			case 9:
				if (isset($value['dm']['firstball'][9] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['firstball'] [9] * $value ['win'] ['单注金额']) ;
				}
			break;
			case 10:
				if (isset($value['dm']['firstball'][10] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['firstball'] [10] * $value ['win'] ['单注金额']) ;
				}
			break;
			case 11:
				if (isset($value['dm']['firstball'][11] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['firstball'] [11] * $value ['win'] ['单注金额']) ;
				}
			break;
		}
		}
		// -----------------------------第二球---------(☄⊙ω⊙)☄
		if (count($value ['dm']['secondball']) > 0) {
			//通过用对换键值对的方法来isset判断变量是否存在，其效率比in_array高很多，内存占用也少
			$value['dm']['secondball'] = array_flip($value['dm']['secondball']);
		switch (( int )$result[1]) {
			case 1:
				if (isset($value['dm']['secondball'][1] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['secondball'] [1] * $value ['win'] ['单注金额']);
				}
			break;
			case 2:
				if (isset($value['dm']['secondball'][2] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['secondball'] [2] * $value ['win'] ['单注金额']);
				}
			break;
			case 3:
				if (isset($value['dm']['secondball'][3] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['secondball'] [3] * $value ['win'] ['单注金额']);
				}
			break;
			case 4:
				if (isset($value['dm']['secondball'][4] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['secondball'] [4] * $value ['win'] ['单注金额']) ;
				}
			break;
			case 5:
				if (isset($value['dm']['secondball'][5] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['secondball'] [5] * $value ['win'] ['单注金额']);
				}
			break;
			case 6:
				if (isset($value['dm']['secondball'][6] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['secondball'] [6] * $value ['win'] ['单注金额']);
				}
			break;
			case 7:
				if (isset($value['dm']['secondball'][7] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['secondball'] [7] * $value ['win'] ['单注金额']);
				}
			break;
			case 8:
				if (isset($value['dm']['secondball'][8] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['secondball'] [8] * $value ['win'] ['单注金额']);
				}
			break;
			case 9:
				if (isset($value['dm']['secondball'][9] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['secondball'] [9] * $value ['win'] ['单注金额']) ;
				}
			break;
			case 10:
				if (isset($value['dm']['secondball'][10] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['secondball'] [10] * $value ['win'] ['单注金额']);
				}
			break;
			case 11:
				if (isset($value['dm']['secondball'][11] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['secondball'] [11] * $value ['win'] ['单注金额']);
				}
			break;
		}
		}
		// -----------------------------第三球---------(☄⊙ω⊙)☄
		if (count($value ['dm']['thirdball']) > 0) {
			//通过用对换键值对的方法来isset判断变量是否存在，其效率比in_array高很多，内存占用也少
			$value['dm']['thirdball'] = array_flip($value['dm']['thirdball']);
		switch (( int )$result[2]) {
			case 1:
				if (isset($value['dm']['thirdball'][1] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['thirdball'] [1] * $value ['win'] ['单注金额']) ;
				}
			break;
			case 2:
				if (isset($value['dm']['thirdball'][2] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['thirdball'] [2] * $value ['win'] ['单注金额']) ;
				}
			break;
			case 3:
				if (isset($value['dm']['thirdball'][3] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['thirdball'] [3] * $value ['win'] ['单注金额']) ;
				}
			break;
			case 4:
				if (isset($value['dm']['thirdball'][4] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['thirdball'] [4] * $value ['win'] ['单注金额']) ;
				}
			break;
			case 5:
				if (isset($value['dm']['thirdball'][5] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['thirdball'] [5] * $value ['win'] ['单注金额']) ;
				}
			break;
			case 6:
				if (isset($value['dm']['thirdball'][6] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['thirdball'] [6] * $value ['win'] ['单注金额']);
				}
			break;
			case 7:
				if (isset($value['dm']['thirdball'][7] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['thirdball'] [7] * $value ['win'] ['单注金额']) ;
				}
			break;
			case 8:
				if (isset($value['dm']['thirdball'][8] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['thirdball'] [8] * $value ['win'] ['单注金额']) ;
				}
			break;
			case 9:
				if (isset($value['dm']['thirdball'][9] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['thirdball'] [9] * $value ['win'] ['单注金额']) ;
				}
			break;
			case 10:
				if (isset($value['dm']['thirdball'][10] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['thirdball'] [10] * $value ['win'] ['单注金额']) ;
				}
			break;
			case 11:
				if (isset($value['dm']['thirdball'][11] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['thirdball'] [11] * $value ['win'] ['单注金额']);
				}
			break;
		}
		}
		// -----------------------------第四球---------(☄⊙ω⊙)☄
		if (count($value ['dm']['thouthball']) > 0) {
			//通过用对换键值对的方法来isset判断变量是否存在，其效率比in_array高很多，内存占用也少
			$value['dm']['thouthball'] = array_flip($value['dm']['thouthball']);
		switch (( int )$result[3]) {
			case 1:
				if (isset($value['dm']['thouthball'][1] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['thouthball'] [1] * $value ['win'] ['单注金额']);
				}
				break;
			case 2:
				if (isset($value['dm']['thouthball'][2] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['thouthball'] [2] * $value ['win'] ['单注金额']);
				}
				break;
			case 3:
				if (isset($value['dm']['thouthball'][3] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['thouthball'] [3] * $value ['win'] ['单注金额']);
				}
				break;
			case 4:
				if (isset($value['dm']['thouthball'][4] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['thouthball'] [4] * $value ['win'] ['单注金额']);
				}
				break;
			case 5:
				if (isset($value['dm']['thouthball'][5] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['thouthball'] [5] * $value ['win'] ['单注金额']);
				}
				break;
			case 6:
				if (isset($value['dm']['thouthball'][6] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['thouthball'] [6] * $value ['win'] ['单注金额']);
				}
				break;
			case 7:
				if (isset($value['dm']['thouthball'][7] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['thouthball'] [7] * $value ['win'] ['单注金额']) ;
				}
				break;
			case 8:
				if (isset($value['dm']['thouthball'][8] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['thouthball'] [8] * $value ['win'] ['单注金额']);
				}
				break;
			case 9:
				if (isset($value['dm']['thouthball'][9] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['thouthball'] [9] * $value ['win'] ['单注金额']) ;
				}
				break;
			case 10:
				if (isset($value['dm']['thouthball'][10] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['thouthball'] [10] * $value ['win'] ['单注金额']) ;
				}
				break;
			case 11:
				if (isset($value['dm']['thouthball'][11] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['thouthball'] [11] * $value ['win'] ['单注金额']);
				}
				break;
		}
		}
		// -----------------------------第五球---------(☄⊙ω⊙)☄
		if (count($value ['dm']['fifthball']) > 0) {
			//通过用对换键值对的方法来isset判断变量是否存在，其效率比in_array高很多，内存占用也少
			$value['dm']['fifthball'] = array_flip($value['dm']['fifthball']);
		switch (( int )$result[4]) {
			case 1:
				if (isset($value['dm']['fifthball'][1] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['fifthball'] [1] * $value ['win'] ['单注金额']) ;
				}
				break;
			case 2:
				if (isset($value['dm']['fifthball'][2] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['fifthball'] [2] * $value ['win'] ['单注金额']) ;
				}
				break;
			case 3:
				if (isset($value['dm']['fifthball'][3] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['fifthball'] [3] * $value ['win'] ['单注金额']) ;
				}
				break;
			case 4:
				if (isset($value['dm']['fifthball'][4] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['fifthball'] [4] * $value ['win'] ['单注金额']) ;
				}
				break;
			case 5:
				if (isset($value['dm']['fifthball'][5] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['fifthball'] [5] * $value ['win'] ['单注金额']) ;
				}
				break;
			case 6:
				if (isset($value['dm']['fifthball'][6] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['fifthball'] [6] * $value ['win'] ['单注金额']) ;
				}
				break;
			case 7:
				if (isset($value['dm']['fifthball'][7] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['fifthball'] [7] * $value ['win'] ['单注金额']) ;
				}
				break;
			case 8:
				if (isset($value['dm']['fifthball'][8] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['fifthball'] [8] * $value ['win'] ['单注金额']) ;
				}
				break;
			case 9:
				if (isset($value['dm']['fifthball'][9] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['fifthball'] [9] * $value ['win'] ['单注金额']) ;
				}
				break;
			case 10:
				if (isset($value['dm']['fifthball'][10] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['fifthball'] [10] * $value ['win'] ['单注金额']) ;
				}
				break;
			case 11:
				if (isset($value['dm']['fifthball'][11] )) {
					$iswin ++;
					$value ['gold'] += ($odds ['dm']['fifthball'] [11] * $value ['win'] ['单注金额']) ;
				}
				break;
		}}}
		// -----------------------------单码-------------------------------↑
		// -----------------------------任选-------------------------------(☄⊙ω⊙)☄
		//一中一
		if ((count($value ['rx'],1)-8) > 0) {
		if (count($value ['rx']['oneToOne']) > 0) {
			$sum=0;//有几个号码中了
			$value ['rx']['oneToOne'] = array_flip($value ['rx']['oneToOne']);
			foreach ($result as $vone) {
				if (isset($value['rx']['oneToOne'][(int)$vone] )) {
					$sum ++;
				};
			}
			$winCount =array_product ( range ( $sum, $sum ) )
			/ array_product ( range ( 1, 1 ) );
			if ($winCount > 0) {
				$iswin+=$winCount;
			$value ['gold']+=(($odds['rx']['oneToOne'][1] * $value ['win'] ['单注金额'])*$winCount);
			}
		}
		//二中二
		if (count($value ['rx']['twoToTwo']) > 0) {
			$value ['rx']['twoToTwo'] = array_flip($value ['rx']['twoToTwo']);
			$sum=0;//有几个号码中了
			foreach ($result as $vtwo) {
				if (isset($value['rx']['twoToTwo'][(int)$vtwo] )) {
					$sum++;
				};
			}
			$winCount =array_product ( range ( $sum, $sum-2+1 ) )
			/ array_product ( range ( 1, 2 ) );//计算中了几注
			if ($winCount > 0) {
				$iswin+=$winCount;
			$value ['gold']+=(($odds['rx']['twoToTwo'][1] * $value ['win'] ['单注金额'])*$winCount);
			}
		}
		//三中三
		if (count($value ['rx']['threeToThree']) > 0) {
			$value ['rx']['threeToThree'] = array_flip($value ['rx']['threeToThree']);
			$sum=0;//有几个号码中了
			foreach ($result as $vthree) {
				if (isset($value['rx']['threeToThree'][(int)$vthree] )) {
					$sum++;
				};
			}
			$winCount =array_product ( range ( $sum, $sum-3+1 ) )
			/ array_product ( range ( 1, 3 ) );//计算中了几注
			if ($winCount > 0) {
				$iswin+=$winCount;
			$value ['gold']+=(($odds['rx']['threeToThree'][1] * $value ['win'] ['单注金额'])*$winCount);
			}
		}
		//四中四
		if (count($value ['rx']['fourTofour']) > 0) {
			$value ['rx']['fourTofour'] = array_flip($value ['rx']['fourTofour']);
			$sum=0;//有几个号码中了
			foreach ($result as $vfour) {
				if (isset($value['rx']['fourTofour'][(int)$vfour] )) {
					$sum++;
				};
			}
			$winCount =array_product ( range ( $sum, $sum-4+1 ) )
			/ array_product ( range ( 1, 4 ) );//计算中了几注
			if ($winCount > 0) {
				$iswin+=$winCount;
			$value ['gold']+=(($odds['rx']['fourTofour'][1] * $value ['win'] ['单注金额'])*$winCount);
			}
		}
		//五中五
		if (count($value ['rx']['fiveToFive']) > 0) {
			$value ['rx']['fiveToFive'] = array_flip($value ['rx']['fiveToFive']);
			$sum=0;//有几个号码中了
			foreach ($result as $vfive) {
				if (isset($value['rx']['fiveToFive'][(int)$vfive] )) {
					$sum++;
				};
			}
			$winCount =array_product ( range ( $sum, $sum-5+1 ) )
			/ array_product ( range ( 1, 5 ) );
			if ($winCount > 0) {
				$iswin+=$winCount;
			$value ['gold']+=(($odds['rx']['fiveToFive'][1] * $value ['win'] ['单注金额'])*$winCount);
			}
		}
		//六中五
		if (count($value ['rx']['sixToFive']) > 0) {
			$value ['rx']['sixToFive'] = array_flip($value ['rx']['sixToFive']);
			$sum=1;//有几个号码中了
			foreach ($result as $vsix) {
				if (isset($value['rx']['sixToFive'][(int)$vsix] )) {
					continue;
				}else {
					$sum=0;
				}
			}
			if ($sum) {
			$n=count($value ['rx']['sixToFive'])-5;
			$x=6-5;
			$winCount =array_product ( range ( $n, ($n-$x+1) ) )
			/ array_product ( range ( 1, $x ) );
			$iswin+=$winCount;
			$value ['gold']+=(($odds['rx']['sixToFive'][1] * $value ['win'] ['单注金额'])*$winCount);
			}
			
		}
		//七中五
		if (count($value ['rx']['sevenToFive']) > 0) {
			$value ['rx']['sevenToFive'] = array_flip($value ['rx']['sevenToFive']);
			$sum=1;//有几个号码中了
			foreach ($result as $vseven) {
				if (isset($value['rx']['sevenToFive'][(int)$vseven] )) {
					continue;
				}else {
					$sum=0;
				}
			}
			if ($sum) {
			$n=count($value ['rx']['sevenToFive'])-5;
			$x=7-5;
			$winCount =array_product ( range ( $n, ($n-$x+1) ) )
			/ array_product ( range ( 1, $x ) );
			$iswin+=$winCount;
			$value ['gold']+=(($odds['rx']['sevenToFive'][1] * $value ['win'] ['单注金额'])*$winCount);
			}
		}
		//八中五
		if (count($value ['rx']['eightToFive']) > 0) {
			$value ['rx']['eightToFive'] = array_flip($value ['rx']['eightToFive']);
			$sum=1;//有几个号码中了
			foreach ($result as $veight) {
				if (isset($value['rx']['eightToFive'][(int)$veight] )) {
					continue;
				}else {
					$sum=0;
				}
			}
			if ($sum) {
			$n=count($value ['rx']['eightToFive'])-5;
			$x=8-5;
			$winCount =array_product ( range ( $n, ($n-$x+1) ) )
			/ array_product ( range ( 1, $x ) );
			$iswin+=$winCount;
			$value ['gold']+=(($odds['rx']['eightToFive'][1] * $value ['win'] ['单注金额'])*$winCount);
			}
		}}
		// -----------------------------任选-------------------------------↑
		// -----------------------------组选-------------------------------(☄⊙ω⊙)☄
		//组选 前二
		if ((count($value ['zux'],1)-2) > 0) {
		if (count($value ['zux']['firstTwo']) > 0) {
			$value ['zux']['firstTwo'] = array_flip($value ['zux']['firstTwo']);
			$sum=0;//有几个号码中了
			$zuxres[0]=(int)$result[0];//第一个球
			$zuxres[1]=(int)$result[1];//第二个球
		foreach ($zuxres as $vtwo) {
				if (isset($value['zux']['firstTwo'][$vtwo] )) {
					$sum++;
				}
		}
		$winCount =array_product ( range ( $sum, $sum-2+1 ) )
		/ array_product ( range ( 1, 2 ) );
		if ($winCount > 0) {
			$iswin+=$winCount;
			$value ['gold']+=(($odds['zux']['firstTwo'][1] * $value ['win'] ['单注金额'])*$winCount);
		}
		}
		//组选 前三
		if (count($value ['zux']['firstThree']) > 0) {
			$value ['zux']['firstThree'] = array_flip($value ['zux']['firstThree']);
			$sum=0;//有几个号码中了
			$zuxres[0]=(int)$result[0];//第一个球
			$zuxres[1]=(int)$result[1];//第二个球
			$zuxres[2]=(int)$result[2];//第三个球
		foreach ($zuxres as $vhre) {
				if (isset($value['zux']['firstThree'][$vhre] )) {
					$sum++;
				}
		}
		$winCount =array_product ( range ( $sum, $sum-2+1 ) )
		/ array_product ( range ( 1, 2 ) );
		if ($winCount > 0) {
			$iswin+=$winCount;
			$value ['gold']+=(($odds['zux']['firstThree'][1] * $value ['win'] ['单注金额'])*$winCount);
		}
		}
		}
		// -----------------------------组选-------------------------------↑
		// -----------------------------直选-------------------------------(☄⊙ω⊙)☄
		if ((count($value['zhix'],1)-7)> 0) {
			//前二
		if (count($value['zhix']['firstTwo']['firstball']) >0 && count($value['zhix']['firstTwo']['secondball'])) {
			//通过用对换键值对的方法来isset判断变量是否存在，其效率比in_array高很多，内存占用也少
			$value['zhix']['firstTwo']['firstball'] = array_flip($value['zhix']['firstTwo']['firstball']);
			$value['zhix']['firstTwo']['secondball'] = array_flip($value['zhix']['firstTwo']['secondball']);
		if (isset($value['zhix']['firstTwo']['firstball'][(int)$result[0]] )
				&& isset($value['zhix']['firstTwo']['secondball'][(int)$result[1]] )) {
					$iswin++;
					$value ['gold']+=($odds['zhix']['firstTwo'][1] * $value ['win'] ['单注金额']);
		}
		}
		//前三
		if (count($value['zhix']['firstThree']['firstball']) >0 && count($value['zhix']['firstThree']['secondball'])
				&& count($value['zhix']['firstThree']['thirdball']) >0) {
			//通过用对换键值对的方法来isset判断变量是否存在，其效率比in_array高很多，内存占用也少
			$value['zhix']['firstThree']['firstball'] = array_flip($value['zhix']['firstThree']['firstball']);
			$value['zhix']['firstThree']['secondball'] = array_flip($value['zhix']['firstThree']['secondball']);
			$value['zhix']['firstThree']['thirdball'] = array_flip($value['zhix']['firstThree']['thirdball']);
		if (isset($value['zhix']['firstThree']['firstball'][(int)$result[0]] )
				&& isset($value['zhix']['firstThree']['secondball'][(int)$result[1]] )
				&& isset($value['zhix']['firstThree']['thirdball'][(int)$result[2]] )) {
					$iswin++;
					$value ['gold']+=($odds['zhix']['firstThree'][1] * $value ['win'] ['单注金额']);
		}
		}	
		}
		// -----------------------------直选-------------------------------↑
		$value ['win'] ['开奖号码'] =$value ['opencode'];
		$value ['win'] ['中奖'] = '中了' . $iswin . '注';
		$value ['win'] ['获利'] = $value ['gold'] ;
		$user = User_user::get ( $value ['userInfo'] ['user_id'] );
		$gdres = array ( // 要插入数据库的数据
				'user_id' => $user ['id'],
				'iswin' => 0,
				'detail' => json_encode ( $value ),
				'expect' => $value ['issue'],
				'time' => time()
		);
		if ($iswin > 0) {
			$gdres ['iswin'] = 1;
			$data = array (
					'gold' => $user ['gold'] + $value ['win'] ['获利'] 
			); // 中奖获利金币
			$gdetail = array ( // 金币账单详情
					'当前金币' => $user ['gold'] + $user ['bank'],
					'中奖获得' => ( int ) $value ['win'] ['获利'],
					'结果' => '+' . $value ['win'] ['获利'],
					'剩余金币' => $data ['gold'] + $user ['bank'] 
			);
			$gold = array ( // 金币账单
					'username' => $user ['username'], // 用户账号
					'operation' => '广东11选5中奖', // 操作说明
					'detail' => json_encode ( $gdetail ), // 账单详情
					'time' => time() 
			);
			$this->startTrans (); // 开启事务
			try {
				$this->create ( $gdres );
				if (User_user::where ( 'id', $gdres ['user_id'] )->update ( $data )) {
					if (Gold_history::create ( $gold )) {
						$this->commit (); // 成功，提交事务
						 $msg[$key]['surplusGold'] =$gdetail ['剩余金币'];
						 $msg[$key]['winGold'] = $value ['win'] ['获利'];
						 $msg[$key]['conId']=$value ['conId'];
// 						 $msg[$key][$user['username']]['win']=$value ['win'];
					} else {
						$this->rollback ();
						$msg[$user['nickname']] = '插入账单失败';
					}
				} else { // 失败，回滚事务
					$this->rollback ();
					$msg[$key][$user['nickname']] = '异常';
				}
			} catch ( Exception $e ) {
				$this->rollback ();
			$msg[$user['nickname']] = '异常';
			}
		}else {
			$msg[$key]['surplusGold'] = $gdetail ['剩余金币'];
			$msg[$key]['winGold'] = $value ['win'] ['获利'];
			$msg[$key]['conId']=$value ['conId'];
// 			$msg[$key][$user['username']]['win']=$value ['win'];
			$this->startTrans (); // 开启事务
			try {
				$this->create ( $gdres );
				$this->commit (); // 成功，提交事务
			} catch ( Exception $e ) {
				$this->rollback ();
				$msg[$user['nickname']] = '异常';
			}
		}
	}
	return $msg;
	}
	
}