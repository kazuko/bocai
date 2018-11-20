<?php
namespace app\ssc\model;
use think\Model;
use app\forum\model\Gold_history;
use app\forum\model\User_user;
/**
* 
*/
class Games_cqres extends Model
{
	// protected $table = "bc_games_cqres";
	function saveResult($param, $odds) {
		foreach ($param as $value) {
			$result = explode ( ",", $value ['opencode'] );
			//前三
			$firstthree[0]=(int)$result[0];//第一个球
			$firstthree[1]=(int)$result[1];//第二个球
			$firstthree[2]=(int)$result[2];//第三个球
			sort($firstthree);//正序 排序
			//中三
			$middlethree[0]=(int)$result[1];//第二个球
			$middlethree[1]=(int)$result[2];//第三个球
			$middlethree[2]=(int)$result[3];//第四个球
			sort($middlethree);//正序 排序
			//后三
			$lastthree[0]=(int)$result[2];//第三个球
			$lastthree[1]=(int)$result[3];//第四个球
			$lastthree[2]=(int)$result[4];//第五个球
			sort($lastthree);//正序 排序
			$iswin =0;//赢了几注
			$value ['win'] ['单注金额'] = $value ['gold'];
			$value ['bet'] = (count ( $value ['lmp'],1)-9)
			+(count ( $value ['OneTofive'],1)-5)
			+(count ( $value ['OneWord'],1)-4)
			+(count ( $value ['TwoWord'],1)-3)
			+(count ( $value ['ThreeWord'],1)-3)
			+(count ( $value ['span'],1)-3)
			+(count ( $value ['and'],1)-10)
			+(count ( $value ['niuniu'],1)-3);
			//----------------------------------------二定位下注数  （直选复式）---------------------
			if ((count ( $value ['Twolocation'],1)-10)) {
				//万千
				if (count($value['Twolocation']['OOXXX']['firstball']) > 0 && count($value['Twolocation']['OOXXX']['secondball']) > 0) {
					if (count($value['Twolocation']['OOXXX']['firstball']) === 10 && count($value['Twolocation']['OOXXX']['secondball']) === 10) {
						$value ['bet']+=(count($value['Twolocation']['OOXXX']['firstball'])*count($value['Twolocation']['OOXXX']['secondball'])-1);
					}else if (count($value['Twolocation']['OOXXX']['firstball']) === 1 && count($value['Twolocation']['OOXXX']['secondball']) === 1){
						$value ['bet']+=1;//如果第一球和第二球各选了一个号码，则是一注
					}else {//否则都是第一球乘以第二球 =下注数
						$value ['bet']+=(count($value['Twolocation']['OOXXX']['firstball'])*count($value['Twolocation']['OOXXX']['secondball']));
					}
				}
				//万百
				if (count($value['Twolocation']['OXOXX']['firstball']) > 0 && count($value['Twolocation']['OXOXX']['secondball']) > 0) {
					if (count($value['Twolocation']['OXOXX']['firstball']) === 10 && count($value['Twolocation']['OXOXX']['secondball']) === 10) {
						$value ['bet']+=(count($value['Twolocation']['OXOXX']['firstball'])*count($value['Twolocation']['OXOXX']['secondball'])-1);
					}else if (count($value['Twolocation']['OXOXX']['firstball']) === 1 && count($value['Twolocation']['OXOXX']['secondball']) === 1){
						$value ['bet']+=1;//如果第一球和第二球各选了一个号码，则是一注
					}else {//否则都是第一球乘以第二球 =下注数
						$value ['bet']+=(count($value['Twolocation']['OXOXX']['firstball'])*count($value['Twolocation']['OXOXX']['secondball']));
					}
				}
				//万十
				if (count($value['Twolocation']['OXXOX']['firstball']) > 0 && count($value['Twolocation']['OXXOX']['secondball']) > 0) {
					if (count($value['Twolocation']['OXXOX']['firstball']) === 10 && count($value['Twolocation']['OXXOX']['secondball']) === 10) {
						$value ['bet']+=(count($value['Twolocation']['OXXOX']['firstball'])*count($value['Twolocation']['OXXOX']['secondball'])-1);
					}else if (count($value['Twolocation']['OXXOX']['firstball']) === 1 && count($value['Twolocation']['OXXOX']['secondball']) === 1){
						$value ['bet']+=1;//如果第一球和第二球各选了一个号码，则是一注
					}else {//否则都是第一球乘以第二球 =下注数
						$value ['bet']+=(count($value['Twolocation']['OXXOX']['firstball'])*count($value['Twolocation']['OXXOX']['secondball']));
					}
				}
				//万个
				if (count($value['Twolocation']['OXXXO']['firstball']) > 0 && count($value['Twolocation']['OXXXO']['secondball']) > 0) {
					if (count($value['Twolocation']['OXXXO']['firstball']) === 10 && count($value['Twolocation']['OXXXO']['secondball']) === 10) {
						$value ['bet']+=(count($value['Twolocation']['OXXXO']['firstball'])*count($value['Twolocation']['OXXXO']['secondball'])-1);
					}else if (count($value['Twolocation']['OXXXO']['firstball']) === 1 && count($value['Twolocation']['OXXXO']['secondball']) === 1){
						$value ['bet']+=1;//如果第一球和第二球各选了一个号码，则是一注
					}else {//否则都是第一球乘以第二球 =下注数
						$value ['bet']+=(count($value['Twolocation']['OXXXO']['firstball'])*count($value['Twolocation']['OXXXO']['secondball']));
					}
				}
				//千百
				if (count($value['Twolocation']['XOOXX']['firstball']) > 0 && count($value['Twolocation']['XOOXX']['secondball']) > 0) {
					if (count($value['Twolocation']['XOOXX']['firstball']) === 10 && count($value['Twolocation']['XOOXX']['secondball']) === 10) {
						$value ['bet']+=(count($value['Twolocation']['XOOXX']['firstball'])*count($value['Twolocation']['XOOXX']['secondball'])-1);
					}else if (count($value['Twolocation']['XOOXX']['firstball']) === 1 && count($value['Twolocation']['XOOXX']['secondball']) === 1){
						$value ['bet']+=1;//如果第一球和第二球各选了一个号码，则是一注
					}else {//否则都是第一球乘以第二球 =下注数
						$value ['bet']+=(count($value['Twolocation']['XOOXX']['firstball'])*count($value['Twolocation']['XOOXX']['secondball']));
					}
				}
				//千十
				if (count($value['Twolocation']['XOXOX']['firstball']) > 0 && count($value['Twolocation']['XOXOX']['secondball']) > 0) {
					if (count($value['Twolocation']['XOXOX']['firstball']) === 10 && count($value['Twolocation']['XOXOX']['secondball']) === 10) {
						$value ['bet']+=(count($value['Twolocation']['XOXOX']['firstball'])*count($value['Twolocation']['XOXOX']['secondball'])-1);
					}else if (count($value['Twolocation']['XOXOX']['firstball']) === 1 && count($value['Twolocation']['XOXOX']['secondball']) === 1){
						$value ['bet']+=1;//如果第一球和第二球各选了一个号码，则是一注
					}else {//否则都是第一球乘以第二球 =下注数
						$value ['bet']+=(count($value['Twolocation']['XOXOX']['firstball'])*count($value['Twolocation']['XOXOX']['secondball']));
					}
				}
				//千个
				if (count($value['Twolocation']['XOXXO']['firstball']) > 0 && count($value['Twolocation']['XOXXO']['secondball']) > 0) {
					if (count($value['Twolocation']['XOXXO']['firstball']) === 10 && count($value['Twolocation']['XOXXO']['secondball']) === 10) {
						$value ['bet']+=(count($value['Twolocation']['XOXXO']['firstball'])*count($value['Twolocation']['XOXXO']['secondball'])-1);
					}else if (count($value['Twolocation']['XOXXO']['firstball']) === 1 && count($value['Twolocation']['XOXXO']['secondball']) === 1){
						$value ['bet']+=1;//如果第一球和第二球各选了一个号码，则是一注
					}else {//否则都是第一球乘以第二球 =下注数
						$value ['bet']+=(count($value['Twolocation']['XOXXO']['firstball'])*count($value['Twolocation']['XOXXO']['secondball']));
					}
				}
				//百十
				if (count($value['Twolocation']['XXOOX']['firstball']) > 0 && count($value['Twolocation']['XXOOX']['secondball']) > 0) {
					if (count($value['Twolocation']['XXOOX']['firstball']) === 10 && count($value['Twolocation']['XXOOX']['secondball']) === 10) {
						$value ['bet']+=(count($value['Twolocation']['XXOOX']['firstball'])*count($value['Twolocation']['XXOOX']['secondball'])-1);
					}else if (count($value['Twolocation']['XXOOX']['firstball']) === 1 && count($value['Twolocation']['XXOOX']['secondball']) === 1){
						$value ['bet']+=1;//如果第一球和第二球各选了一个号码，则是一注
					}else {//否则都是第一球乘以第二球 =下注数
						$value ['bet']+=(count($value['Twolocation']['XXOOX']['firstball'])*count($value['Twolocation']['XXOOX']['secondball']));
					}
				}
				//百个
				if (count($value['Twolocation']['XXOXO']['firstball']) > 0 && count($value['Twolocation']['XXOXO']['secondball']) > 0) {
					if (count($value['Twolocation']['XXOXO']['firstball']) === 10 && count($value['Twolocation']['XXOXO']['secondball']) === 10) {
						$value ['bet']+=(count($value['Twolocation']['XXOXO']['firstball'])*count($value['Twolocation']['XXOXO']['secondball'])-1);
					}else if (count($value['Twolocation']['XXOXO']['firstball']) === 1 && count($value['Twolocation']['XXOXO']['secondball']) === 1){
						$value ['bet']+=1;//如果第一球和第二球各选了一个号码，则是一注
					}else {//否则都是第一球乘以第二球 =下注数
						$value ['bet']+=(count($value['Twolocation']['XXOXO']['firstball'])*count($value['Twolocation']['XXOXO']['secondball']));
					}
				}
				//十个
				if (count($value['Twolocation']['XXXOO']['firstball']) > 0 && count($value['Twolocation']['XXXOO']['secondball']) > 0) {
					if (count($value['Twolocation']['XXXOO']['firstball']) === 10 && count($value['Twolocation']['XXXOO']['secondball']) === 10) {
						$value ['bet']+=(count($value['Twolocation']['XXXOO']['firstball'])*count($value['Twolocation']['XXXOO']['secondball'])-1);
					}else if (count($value['Twolocation']['XXXOO']['firstball']) === 1 && count($value['Twolocation']['XXXOO']['secondball']) === 1){
						$value ['bet']+=1;//如果第一球和第二球各选了一个号码，则是一注
					}else {//否则都是第一球乘以第二球 =下注数
						$value ['bet']+=(count($value['Twolocation']['XXXOO']['firstball'])*count($value['Twolocation']['XXXOO']['secondball']));
					}
				}
			}
			// ----------------------三定位下注数（复式直选）-------------
			if ((count ( $value ['Threelocation'], 1 ) - 12)) {
				// 前三
				if (count ( $value ['Threelocation'] ['firstThree'] ['firstball'] ) > 0 && count ( $value ['Threelocation'] ['firstThree'] ['secondball'] ) > 0 && count ( $value ['Threelocation'] ['firstThree'] ['thirdball'] ) > 0) {
					if (count ( $value ['Threelocation'] ['firstThree'] ['firstball'] ) === 10 && count ( $value ['Threelocation'] ['firstThree'] ['secondball'] ) === 10 && count ( $value ['Threelocation'] ['firstThree'] ['thirdball'] ) === 10) {
						$value ['bet'] += (count ( $value ['Threelocation'] ['firstThree'] ['firstball'] ) * count ( $value ['Threelocation'] ['firstThree'] ['secondball'] ) * count ( $value ['Threelocation'] ['firstThree'] ['thirdball'] ) - 1);
					} else if (count ( $value ['Threelocation'] ['firstThree'] ['firstball'] ) === 1 && count ( $value ['Threelocation'] ['firstThree'] ['secondball'] ) === 1 && count ( $value ['Threelocation'] ['firstThree'] ['thirdball'] ) === 1) {
						$value ['bet'] += 1; // 如果第一球和第二球各选了一个号码，则是一注
					} else {
						$value ['bet'] += (count ( $value ['Threelocation'] ['firstThree'] ['firstball'] ) * count ( $value ['Threelocation'] ['firstThree'] ['secondball'] ) * count ( $value ['Threelocation'] ['firstThree'] ['thirdball'] ));
					}
				}
				// 中三
				if (count ( $value ['Threelocation'] ['middleThree'] ['firstball'] ) > 0 && count ( $value ['Threelocation'] ['middleThree'] ['secondball'] ) > 0 && count ( $value ['Threelocation'] ['middleThree'] ['thirdball'] ) > 0) {
					if (count ( $value ['Threelocation'] ['middleThree'] ['firstball'] ) === 10 && count ( $value ['Threelocation'] ['middleThree'] ['secondball'] ) === 10 && count ( $value ['Threelocation'] ['middleThree'] ['thirdball'] ) === 10) {
						$value ['bet'] += (count ( $value ['Threelocation'] ['middleThree'] ['firstball'] ) * count ( $value ['Threelocation'] ['middleThree'] ['secondball'] ) * count ( $value ['Threelocation'] ['middleThree'] ['thirdball'] ) - 1);
					} else if (count ( $value ['Threelocation'] ['middleThree'] ['firstball'] ) === 1 && count ( $value ['Threelocation'] ['middleThree'] ['secondball'] ) === 1 && count ( $value ['Threelocation'] ['middleThree'] ['thirdball'] ) === 1) {
						$value ['bet'] += 1; // 如果第一球和第二球各选了一个号码，则是一注
					} else {
						$value ['bet'] += (count ( $value ['Threelocation'] ['middleThree'] ['firstball'] ) * count ( $value ['Threelocation'] ['middleThree'] ['secondball'] ) * count ( $value ['Threelocation'] ['middleThree'] ['thirdball'] ));
					}
				}
				// 后三
				if (count ( $value ['Threelocation'] ['lastThree'] ['firstball'] ) > 0 && count ( $value ['Threelocation'] ['lastThree'] ['secondball'] ) > 0 && count ( $value ['Threelocation'] ['lastThree'] ['thirdball'] ) > 0) {
					if (count ( $value ['Threelocation'] ['lastThree'] ['firstball'] ) === 10 && count ( $value ['Threelocation'] ['lastThree'] ['secondball'] ) === 10 && count ( $value ['Threelocation'] ['lastThree'] ['thirdball'] ) === 10) {
						$value ['bet'] += (count ( $value ['Threelocation'] ['lastThree'] ['firstball'] ) * count ( $value ['Threelocation'] ['lastThree'] ['secondball'] ) * count ( $value ['Threelocation'] ['lastThree'] ['thirdball'] ) - 1);
					} else if (count ( $value ['Threelocation'] ['lastThree'] ['firstball'] ) === 1 && count ( $value ['Threelocation'] ['lastThree'] ['secondball'] ) === 1 && count ( $value ['Threelocation'] ['lastThree'] ['thirdball'] ) === 1) {
						$value ['bet'] += 1; // 如果第一球和第二球各选了一个号码，则是一注
					} else {
						$value ['bet'] += (count ( $value ['Threelocation'] ['lastThree'] ['firstball'] ) * count ( $value ['Threelocation'] ['lastThree'] ['secondball'] ) * count ( $value ['Threelocation'] ['lastThree'] ['thirdball'] ));
					}
				}
			}
			//---------------------组选三下注数（组选复式）-------------------
			if ((count ( $value ['groupThree'],1)-3)) {
				//前三
				if (count ( $value ['groupThree']['firstThree']) > 0) {
					if (count ( $value ['groupThree']['firstThree']) > 2) {
						$value ['bet']+=array_product(range(1,count($value['groupThree']['firstThree'])))
						/(array_product(range(1,count($value['groupThree']['firstThree'])-2))*array_product(range(1,2)));
					}else if (count ( $value ['groupThree']['firstThree']) ===2){
						$value ['bet']+=1;//刚好等于2就是一注
					}
				}
				//中三
				if (count ( $value ['groupThree']['middleThree']) > 0) {
					if (count ( $value ['groupThree']['middleThree']) > 2) {
						$value ['bet']+=array_product(range(1,count($value['groupThree']['middleThree'])))
						/(array_product(range(1,count($value['groupThree']['middleThree'])-2))*array_product(range(1,2)));
					}else if (count ( $value ['groupThree']['middleThree']) ===2){
						$value ['bet']+=1;//刚好等于2就是一注
					}
				}
				//后三
				if (count ( $value ['groupThree']['lastThree']) > 0) {
					if (count ( $value ['groupThree']['lastThree']) > 2) {
						$value ['bet']+=array_product(range(1,count($value['groupThree']['lastThree'])))
						/(array_product(range(1,count($value['groupThree']['lastThree'])-2))*array_product(range(1,2)));
					}else if (count ( $value ['groupThree']['lastThree']) ===2){
						$value ['bet']+=1;//刚好等于2就是一注
					}
				}
			}
			//---------------------组选六下注数（组选复式）-------------------
			if ((count ( $value ['groupSix'],1)-3)) {
				//前三
				if (count ( $value ['groupSix']['firstThree']) > 0) {
					if (count ( $value ['groupSix']['firstThree']) > 3) {
						$value ['bet']+=array_product(range(1,count($value['groupSix']['firstThree'])))
						/(array_product(range(1,count($value['groupSix']['firstThree'])-3))*array_product(range(1,3)));
					}else if (count ( $value ['groupSix']['firstThree']) ===3){
						$value ['bet']+=1;//刚好等于2就是一注
					}
				}
				//前三
				if (count ( $value ['groupSix']['middleThree']) > 0) {
					if (count ( $value ['groupSix']['middleThree']) > 3) {
						$value ['bet']+=array_product(range(1,count($value['groupSix']['middleThree'])))
						/(array_product(range(1,count($value['groupSix']['middleThree'])-3))*array_product(range(1,3)));
					}else if (count ( $value ['groupSix']['middleThree']) ===3){
						$value ['bet']+=1;//刚好等于2就是一注
					}
				}
				//前三
				if (count ( $value ['groupSix']['lastThree']) > 0) {
					if (count ( $value ['groupSix']['lastThree']) > 3) {
						$value ['bet']+=array_product(range(1,count($value['groupSix']['lastThree'])))
						/(array_product(range(1,count($value['groupSix']['lastThree'])-3))*array_product(range(1,3)));
					}else if (count ( $value ['groupSix']['lastThree']) ===3){
						$value ['bet']+=1;//刚好等于2就是一注
					}
				}
			}
			$value ['win'] ['下注金额'] = $value ['gold'] * $value['bet'];
			$value ['gold']=0;
			//----------------------------------两面盘计算结果------------------
			if ((count ( $value ['lmp'],1)-9)>0) {
				$zhi=array(1,2,3,5,7);//质量
				$he=array(0,4,6,8,9);//合
				$zhi = array_flip($zhi);
				$he = array_flip($he);
				if (count($value['lmp']['alls'])>0) {
					//通过用对换键值对的方法来isset判断变量是否存在，其效率比in_array高很多，内存占用也少
					$value['lmp']['alls'] = array_flip($value['lmp']['alls']);
					//总和大小
					if (array_sum ( $result ) >= 23 && isset($value['lmp']['alls']['totalBig'])) {
						$value ['win'] ['两面盘'] ['总和大小'] = '总和大';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['总和大'] * $value ['win'] ['单注金额']);
					}else if (array_sum ( $result ) <= 22 && isset($value['lmp']['alls']['totalSmall'])){
						$value ['win'] ['两面盘'] ['总和大小'] = '总和小';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['总和小'] * $value ['win'] ['单注金额']);
					}
					//总和单双
					if (array_sum ( $result ) %2 === 0  && isset($value['lmp']['alls']['totalBoth'])) {
						$value ['win'] ['两面盘'] ['总和单双'] = '总和双';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['总和双'] * $value ['win'] ['单注金额']);
					}else if (array_sum ( $result ) %2 !== 0 && isset($value['lmp']['alls']['totalOne'])){
						$value ['win'] ['两面盘'] ['总和单双'] = '总和单';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['总和单'] * $value ['win'] ['单注金额']);
					}
					//龙虎
					if (( int ) $result [0] > ( int ) $result [4]  && isset($value['lmp']['alls']['dragon'])) {
						$value ['win'] ['两面盘'] ['龙和虎'] = '龙';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['龙'] * $value ['win'] ['单注金额']);
					}else if (( int ) $result [0] < ( int ) $result [4] && isset($value['lmp']['alls']['tiger'])){
						$value ['win'] ['两面盘'] ['龙和虎'] = '虎';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['虎'] * $value ['win'] ['单注金额']);
					}else if (( int ) $result [0] === ( int ) $result [4] && isset($value['lmp']['alls']['and'])){
						$value ['win'] ['两面盘'] ['龙和虎'] = '和';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['和'] * $value ['win'] ['单注金额']);
					}
				}
				//第一球（万位）
				if (count($value['lmp']['firstball']) > 0) {
					$value['lmp']['firstball'] = array_flip($value['lmp']['firstball']);
					//大小
					if (( int ) $result [0] >= 5 && isset($value['lmp']['firstball']['big'])) {
						$value ['win'] ['两面盘'] ['第一球大小'] = '大';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['第一球']['大'] * $value ['win'] ['单注金额']);
					}else if (( int ) $result [0] <= 4 && isset($value['lmp']['firstball']['small'])){
						$value ['win'] ['两面盘'] ['第一球大小'] = '小';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['第一球']['小'] * $value ['win'] ['单注金额']);
					}
					//单双
					if (( int ) $result [0] % 2 === 0 && isset($value['lmp']['firstball']['Both'])) {
						$value ['win'] ['两面盘'] ['第一球单双'] = '双';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['第一球']['双'] * $value ['win'] ['单注金额']);
					}else if (( int ) $result [0] % 2 !== 0 && isset($value['lmp']['firstball']['One'])){
						$value ['win'] ['两面盘'] ['第一球单双'] = '单';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['第一球']['单'] * $value ['win'] ['单注金额']);
					}
				//质合
				if (isset($zhi [( int ) $result [0]]) && isset ( $value ['lmp'] ['firstball'] ['Zhi'] )) {
					$value ['win'] ['两面盘'] ['第一球质合'] = '质';
					$iswin ++;
					$value ['gold'] += ($odds ['两面盘'] ['第一球'] ['质'] * $value ['win'] ['单注金额']);
				} else if (isset($he [( int ) $result [0]]) && isset ( $value ['lmp'] ['firstball'] ['He'] )) {
					$value ['win'] ['两面盘'] ['第一球质合'] = '合';
					$iswin ++;
					$value ['gold'] += ($odds ['两面盘'] ['第一球'] ['合'] * $value ['win'] ['单注金额']);
				}
				}
				//第二球（千位）
				if (count($value['lmp']['secondball']) > 0) {
					$value['lmp']['secondball'] = array_flip($value['lmp']['secondball']);
					//大小
					if (( int ) $result [1] >= 5 && isset($value['lmp']['secondball']['big'])) {
						$value ['win'] ['两面盘'] ['第二球大小'] = '大';
						$iswin ++;
						$value ['gold']+= ($odds ['两面盘'] ['第二球']['大'] * $value ['win'] ['单注金额']);
					}else if (( int ) $result [1] <= 4 && isset($value['lmp']['secondball']['small'])){
						$value ['win'] ['两面盘'] ['第二球大小'] = '小';
						$iswin ++;
						$value ['gold']+= ($odds ['两面盘'] ['第二球']['小'] * $value ['win'] ['单注金额']);
					}
					//单双
					if (( int ) $result [1] % 2 === 0 && isset($value['lmp']['secondball']['Both'])) {
						$value ['win'] ['两面盘'] ['第二球单双'] = '双';
						$iswin ++;
						$value ['gold']+= ($odds ['两面盘'] ['第二球']['双'] * $value ['win'] ['单注金额']);
					}else if (( int ) $result [1] % 2 !== 0 && isset($value['lmp']['secondball']['One'])){
						$value ['win'] ['两面盘'] ['第二球单双'] = '单';
						$iswin ++;
						$value ['gold']+= ($odds ['两面盘'] ['第二球']['单'] * $value ['win'] ['单注金额']);
					}
					//质合
					if (isset($zhi [( int ) $result [1]]) && isset ( $value ['lmp'] ['secondball'] ['Zhi'] )) {
						$value ['win'] ['两面盘'] ['第二球质合'] = '质';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['第二球'] ['质'] * $value ['win'] ['单注金额']);
					} else if (isset($he [( int ) $result [1]]) && isset ( $value ['lmp'] ['secondball'] ['He'] )) {
						$value ['win'] ['两面盘'] ['第二球质合'] = '合';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['第二球'] ['合'] * $value ['win'] ['单注金额']);
					}
				}
				//第三球（百位）
				if (count($value['lmp']['thirdball']) > 0) {
					$value['lmp']['thirdball'] = array_flip($value['lmp']['thirdball']);
					//大小
					if (( int ) $result [2] >= 5 && isset($value['lmp']['thirdball']['big'])) {
						$value ['win'] ['两面盘'] ['第三球大小'] = '大';
						$iswin ++;
						$value ['gold']+= ($odds ['两面盘'] ['第三球']['大'] * $value ['win'] ['单注金额']);
					}else if (( int ) $result [2] <= 4 && isset($value['lmp']['thirdball']['small'])){
						$value ['win'] ['两面盘'] ['第三球大小'] = '小';
						$iswin ++;
						$value ['gold']+= ($odds ['两面盘'] ['第三球']['小'] * $value ['win'] ['单注金额']);
					}
					//单双
					if (( int ) $result [2] % 2 === 0 && isset($value['lmp']['thirdball']['Both'])) {
						$value ['win'] ['两面盘'] ['第三球单双'] = '双';
						$iswin ++;
						$value ['gold']+= ($odds ['两面盘'] ['第三球']['双'] * $value ['win'] ['单注金额']);
					}else if (( int ) $result [2] % 2 !== 0 && isset($value['lmp']['thirdball']['One'])){
						$value ['win'] ['两面盘'] ['第三球单双'] = '单';
						$iswin ++;
						$value ['gold']+= ($odds ['两面盘'] ['第三球']['单'] * $value ['win'] ['单注金额']);
					}
					//质合
					if (isset($zhi [( int ) $result [2]]) && isset ( $value ['lmp'] ['thirdball'] ['Zhi'] )) {
						$value ['win'] ['两面盘'] ['第三球质合'] = '质';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['第三球'] ['质'] * $value ['win'] ['单注金额']);
					} else if (isset($he [( int ) $result [2]]) && isset ( $value ['lmp'] ['thirdball'] ['He'] )) {
						$value ['win'] ['两面盘'] ['第三球质合'] = '合';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['第三球'] ['合'] * $value ['win'] ['单注金额']);
					}
				}
				//第四球（十位）
				if (count($value['lmp']['thouthball']) > 0) {
					$value['lmp']['thouthball'] = array_flip($value['lmp']['thouthball']);
					//大小
					if (( int ) $result [3] >= 5 && isset($value['lmp']['thouthball']['big'])) {
						$value ['win'] ['两面盘'] ['第四球大小'] = '大';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['第四球']['大'] * $value ['win'] ['单注金额']);
					}else if (( int ) $result [3] <= 4 && isset($value['lmp']['thouthball']['small'])){
						$value ['win'] ['两面盘'] ['第四球大小'] = '小';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['第四球']['小'] * $value ['win'] ['单注金额']);
					}
					//单双
					if (( int ) $result [3] % 2 === 0 && isset($value['lmp']['thouthball']['Both'])) {
						$value ['win'] ['两面盘'] ['第四球单双'] = '双';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['第四球']['双'] * $value ['win'] ['单注金额']);
					}else if (( int ) $result [3] % 2 !== 0 && isset($value['lmp']['thouthball']['One'])){
						$value ['win'] ['两面盘'] ['第四球单双'] = '单';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['第四球']['单'] * $value ['win'] ['单注金额']);
					}
					//质合
					if (isset($zhi [( int ) $result [3]]) && isset ( $value ['lmp'] ['thouthball'] ['Zhi'] )) {
						$value ['win'] ['两面盘'] ['第四球质合'] = '质';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['第四球'] ['质'] * $value ['win'] ['单注金额']);
					} else if (isset($he [( int ) $result [3]]) && isset ( $value ['lmp'] ['thouthball'] ['He'] )) {
						$value ['win'] ['两面盘'] ['第四球质合'] = '合';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['第四球'] ['合'] * $value ['win'] ['单注金额']);
					}
				}
				//第五球（个位）
				if (count($value['lmp']['fifthball']) > 0) {
					$value['lmp']['fifthball'] = array_flip($value['lmp']['fifthball']);
					//大小
					if (( int ) $result [4] >= 5 && isset($value['lmp']['fifthball']['big'])) {
						$value ['win'] ['两面盘'] ['第五球大小'] = '大';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['第五球']['大'] * $value ['win'] ['单注金额']);
					}else if (( int ) $result [4] <= 4 && isset($value['lmp']['fifthball']['small'])){
						$value ['win'] ['两面盘'] ['第五球大小'] = '小';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['第五球']['小'] * $value ['win'] ['单注金额']);
					}
					//单双
					if (( int ) $result [4] % 2 === 0 && isset($value['lmp']['fifthball']['Both'])) {
						$value ['win'] ['两面盘'] ['第五球单双'] = '双';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['第五球']['双'] * $value ['win'] ['单注金额']);
					}else if (( int ) $result [4] % 2 !== 0 && isset($value['lmp']['fifthball']['One'])){
						$value ['win'] ['两面盘'] ['第五球单双'] = '单';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['第五球']['单'] * $value ['win'] ['单注金额']);
					}
					//质合
					if (isset($zhi [( int ) $result [4]]) && isset ( $value ['lmp'] ['fifthball'] ['Zhi'] )) {
						$value ['win'] ['两面盘'] ['第五球质合'] = '质';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['第五球'] ['质'] * $value ['win'] ['单注金额']);
					} else if (isset($he [( int ) $result [4]]) && isset ( $value ['lmp'] ['fifthball'] ['He'] )) {
						$value ['win'] ['两面盘'] ['第五球质合'] = '合';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['第五球'] ['合'] * $value ['win'] ['单注金额']);
					}
				}
				//前三
				if (count($value['lmp']['firstthree']) > 0) {
					$firstNumber=array();
					//计算豹子、对子
					//如果第三球减去第二球等于零，真则返回1，否则则返回零
					$firstNumber[0] = $firstthree[2] - $firstthree[1] === 0 ? 1 : 0;
					//如果第二球减去第一球等于零，真则自增1（如果上面的$resNumber[0]是1则+1=2），否则返回上面的出的值，如果上面是1则还是1，如果不是1则是零
					$firstNumber[0] = $firstthree[1] - $firstthree[0] === 0 ? ++$firstNumber[0] : $firstNumber[0];
					//计算顺子、半顺、杂六
					if ($firstthree[2] === 9 && ($firstthree[1] === 8 || $firstthree[1] === 1) &&$firstthree[0] === 0 ) {
						$firstNumber[1]=3;//如果有两个球分别等于0和9，另一个球如果等于8或1，那就是顺子
					}else if ($firstthree[2] === 9 && $firstthree[1] !== 8 && $firstthree[0] === 0 && $firstthree[1] !== 1){
						$firstNumber[1]=4;//如果有两个球分别等于0和9，另一个球如果不等于8或1，那就是半顺
					}else {//除去以上两种情况
						//第三球减去第二球等于1 真则把1赋值给$resNumber[1] 否则把0赋值给他
						$firstNumber[1] = $firstthree[2] - $firstthree[1] === 1 ? 1 : 0;
						//第二球减去第一球等于1 真则自增1（如果上面的$resNumber[1]是1则+1=2），否则返回上面的出的值，如果上面是1则还是1，如果不是1则是零
						$firstNumber[1] = $firstthree[1] - $firstthree[0] === 1 ? ++$firstNumber[1] : $firstNumber[1];
					}
					$value['lmp']['firstthree'] = array_flip($value['lmp']['firstthree']);
					if ( $firstNumber[0] === 2 && isset($value['lmp']['firstthree']['bz'])) {
						$value ['win'] ['两面盘'] ['前三'] = '豹子';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['前三']['豹子'] * $value ['win'] ['单注金额']);
					}else if (($firstNumber[1] === 2 || $firstNumber[1] === 3) && isset($value['lmp']['firstthree']['sz'])){
						$value ['win'] ['两面盘'] ['前三'] ='顺子';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['前三']['顺子'] * $value ['win'] ['单注金额']);
					}else if ($firstNumber[0] === 1 && isset($value['lmp']['firstthree']['dz'])){
						$value ['win'] ['两面盘'] ['前三'] ='对子';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['前三']['对子'] * $value ['win'] ['单注金额']);
					}else if (($firstNumber[1] === 1 || $firstNumber[1]  === 4) && isset($value['lmp']['firstthree']['bs'])){
						$value ['win'] ['两面盘'] ['前三'] ='半顺';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['前三']['半顺'] * $value ['win'] ['单注金额']);
					}else if ($firstNumber[1] === 0 && $firstNumber[0] === 0 && isset($value['lmp']['firstthree']['zl'])){
						$value ['win'] ['两面盘'] ['前三'] ='杂六';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['前三']['杂六'] * $value ['win'] ['单注金额']);
					}
		
				}
				//中三
				if (count($value['lmp']['middlethree']) > 0) {
					$middleNumber=array();
					//计算豹子、对子
					//如果第三球减去第二球等于零，真则返回1，否则则返回零
					$middleNumber[0] = $middlethree[2] - $middlethree[1] === 0 ? 1 : 0;
					//如果第二球减去第一球等于零，真则自增1（如果上面的$resNumber[0]是1则+1=2），否则返回上面的出的值，如果上面是1则还是1，如果不是1则是零
					$middleNumber[0] = $middlethree[1] - $middlethree[0] === 0 ? ++$middleNumber[0] : $middleNumber[0];
					//计算顺子、半顺、杂六
					if ($middlethree[2] === 9 && ($middlethree[1] === 8 || $middlethree[1] === 1) &&$middlethree[0] === 0 ) {
						$middleNumber[1]=3;//如果有两个球分别等于0和9，另一个球如果等于8或1，那就是顺子
					}else if ($middlethree[2] === 9 && $middlethree[1] !== 8 && $middlethree[0] === 0 && $middlethree[1] !== 1){
						$middleNumber[1]=4;//如果有两个球分别等于0和9，另一个球如果不等于8或1，那就是半顺
					}else {//除去以上两种情况
						//第三球减去第二球等于1 真则把1赋值给$resNumber[1] 否则把0赋值给他
						$middleNumber[1] = $middlethree[2] - $middlethree[1] === 1 ? 1 : 0;
						//第二球减去第一球等于1 真则自增1（如果上面的$resNumber[1]是1则+1=2），否则返回上面的出的值，如果上面是1则还是1，如果不是1则是零
						$middleNumber[1] = $middlethree[1] - $middlethree[0] === 1 ? ++$middleNumber[1] : $middleNumber[1];
					}
					$value['lmp']['middlethree'] = array_flip($value['lmp']['middlethree']);
					if ( $middleNumber[0] === 2 && isset($value['lmp']['middlethree']['bz'])) {
						$value ['win'] ['两面盘'] ['中三'] = '豹子';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['中三']['豹子'] * $value ['win'] ['单注金额']);
					}else if (($middleNumber[1] === 2 || $middleNumber[1] === 3) && isset($value['lmp']['middlethree']['sz'])){
						$value ['win'] ['两面盘'] ['中三'] ='顺子';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['中三']['顺子'] * $value ['win'] ['单注金额']);
					}else if ($middleNumber[0] === 1 && isset($value['lmp']['middlethree']['dz'])){
						$value ['win'] ['两面盘'] ['中三'] ='对子';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['中三']['对子'] * $value ['win'] ['单注金额']);
					}else if (($middleNumber[1] === 1 || $middleNumber[1]  === 4) && isset($value['lmp']['middlethree']['bs'])){
						$value ['win'] ['两面盘'] ['中三'] ='半顺';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['中三']['半顺'] * $value ['win'] ['单注金额']);
					}else if ($middleNumber[1] === 0 && $middleNumber[0] === 0 && isset($value['lmp']['middlethree']['zl'])){
						$value ['win'] ['两面盘'] ['中三'] ='杂六';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['中三']['杂六'] * $value ['win'] ['单注金额']);
					}
		
				}
				//后三
				if (count($value['lmp']['lastthree']) > 0) {
					$lastNumber=array();
					//计算豹子、对子
					//如果第三球减去第二球等于零，真则返回1，否则则返回零
					$lastNumber[0] = $lastthree[2] - $lastthree[1] === 0 ? 1 : 0;
					//如果第二球减去第一球等于零，真则自增1（如果上面的$resNumber[0]是1则+1=2），否则返回上面的出的值，如果上面是1则还是1，如果不是1则是零
					$lastNumber[0] = $lastthree[1] - $lastthree[0] === 0 ? ++$lastNumber[0] : $lastNumber[0];
					//计算顺子、半顺、杂六
					if ($lastthree[2] === 9 && ($lastthree[1] === 8 || $lastthree[1] === 1) &&$lastthree[0] === 0 ) {
						$lastNumber[1]=3;//如果有两个球分别等于0和9，另一个球如果等于8或1，那就是顺子
					}else if ($lastthree[2] === 9 && $lastthree[1] !== 8 && $lastthree[0] === 0 && $lastthree[1] !== 1){
						$lastNumber[1]=4;//如果有两个球分别等于0和9，另一个球如果不等于8或1，那就是半顺
					}else {//除去以上两种情况
						//第三球减去第二球等于1 真则把1赋值给$resNumber[1] 否则把0赋值给他
						$lastNumber[1] = $lastthree[2] - $lastthree[1] === 1 ? 1 : 0;
						//第二球减去第一球等于1 真则自增1（如果上面的$resNumber[1]是1则+1=2），否则返回上面的出的值，如果上面是1则还是1，如果不是1则是零
						$lastNumber[1] = $lastthree[1] - $lastthree[0] === 1 ? ++$lastNumber[1] : $lastNumber[1];
					}
					$value['lmp']['lastthree'] = array_flip($value['lmp']['lastthree']);
					if ( $lastNumber[0] === 2 && isset($value['lmp']['lastthree']['bz'])) {
						$value ['win'] ['两面盘'] ['后三'] = '豹子';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['后三']['豹子'] * $value ['win'] ['单注金额']);
					}else if (($lastNumber[1] === 2 || $lastNumber[1] === 3) && isset($value['lmp']['lastthree']['sz'])){
						$value ['win'] ['两面盘'] ['后三'] ='顺子';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['后三']['顺子'] * $value ['win'] ['单注金额']);
					}else if ($lastNumber[0] === 1 && isset($value['lmp']['lastthree']['dz'])){
						$value ['win'] ['两面盘'] ['后三'] ='对子';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['后三']['对子'] * $value ['win'] ['单注金额']);
					}else if (($lastNumber[1] === 1 || $lastNumber[1]  === 4) && isset($value['lmp']['lastthree']['bs'])){
						$value ['win'] ['两面盘'] ['后三'] ='半顺';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['后三']['半顺'] * $value ['win'] ['单注金额']);
					}else if ($lastNumber[1] === 0 && $lastNumber[0] === 0 && isset($value['lmp']['lastthree']['zl'])) {
						$value ['win'] ['两面盘'] ['后三'] ='杂六';
						$iswin ++;
						$value ['gold'] += ($odds ['两面盘'] ['后三']['杂六'] * $value ['win'] ['单注金额']);
					}
		
				}
			}
			//----------------------------------1~5计算结果------------------
					if ((count ( $value ['OneTofive'],1)-5)>0) {
						$zhi=array(1,2,3,5,7);//质
						$he=array(0,4,6,8,9);//合
						$zhi = array_flip($zhi);
						$he = array_flip($he);
				//第一球
					if (count($value ['OneTofive']['firstball']) > 0){
						$value['OneTofive']['firstball'] = array_flip($value['OneTofive']['firstball']);
					switch (( int )$result[0]) {
						case 0:
							if (isset($value['OneTofive']['firstball'][0])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 1:
							if (isset($value['OneTofive']['firstball'][1])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 2:
							if (isset($value['OneTofive']['firstball'][2])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 3:
							if (isset($value['OneTofive']['firstball'][3])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 4:
							if (isset($value['OneTofive']['firstball'][4])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 5:
							if (isset($value['OneTofive']['firstball'][5])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 6:
							if (isset($value['OneTofive']['firstball'][6])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 7:
							if (isset($value['OneTofive']['firstball'][7])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 8:
							if (isset($value['OneTofive']['firstball'][8])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 9:
							if (isset($value['OneTofive']['firstball'][9])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
					}
					//大小
					if (( int ) $result [0] >= 5 && isset($value['OneTofive']['firstball']['big'])) {
						$value ['win'] ['一至五'] ['第一球大小'] = '大';
						$iswin ++;
						$value ['gold']+= ($odds ['一至五'] ['大'] * $value ['win'] ['单注金额']);
					}else if (( int ) $result [0] <= 4 && isset($value['OneTofive']['firstball']['small'])){
						$value ['win'] ['一至五'] ['第一球大小'] = '小';
						$iswin ++;
						$value ['gold']+= ($odds ['一至五'] ['小'] * $value ['win'] ['单注金额']);
					}
					//单双
					if (( int ) $result [0] % 2 === 0 && isset($value['OneTofive']['firstball']['Both'])) {
						$value ['win'] ['一至五'] ['第一球单双'] = '双';
						$iswin ++;
						$value ['gold'] +=($odds ['一至五'] ['双'] * $value ['win'] ['单注金额']);
					}else if (( int ) $result [0] % 2 !== 0 && isset($value['OneTofive']['firstball']['One'])){
						$value ['win'] ['一至五'] ['第一球单双'] = '单';
						$iswin ++;
						$value ['gold']+= ($odds ['一至五'] ['单'] * $value ['win'] ['单注金额']);
					}
					//质合
					if (isset($zhi [( int ) $result [0]]) && isset ( $value ['OneTofive'] ['firstball'] ['Zhi'] )) {
						$value ['win'] ['一至五'] ['第一球质合'] = '质';
						$iswin ++;
						$value ['gold'] += ($odds ['一至五'] ['质'] * $value ['win'] ['单注金额']);
					} else if (isset($he [( int ) $result [0]]) && isset ( $value ['OneTofive'] ['firstball'] ['He'] )) {
						$value ['win'] ['一至五'] ['第一球质合'] = '合';
						$iswin ++;
						$value ['gold'] += ($odds ['一至五'] ['合'] * $value ['win'] ['单注金额']);
					}
				}
				//第二球
				if (count($value ['OneTofive']['secondball']) > 0){
					$value['OneTofive']['secondball'] = array_flip($value['OneTofive']['secondball']);
					switch (( int )$result[1]) {
						case 0:
							if (isset($value['OneTofive']['secondball'][0])) {
								$iswin ++;
								$value ['gold']+= ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 1:
							if (isset($value['OneTofive']['secondball'][1])) {
								$iswin ++;
								$value ['gold']+= ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 2:
							if (isset($value['OneTofive']['secondball'][2])) {
								$iswin ++;
								$value ['gold']+= ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 3:
							if (isset($value['OneTofive']['secondball'][3])) {
								$iswin ++;
								$value ['gold']+= ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 4:
							if (isset($value['OneTofive']['secondball'][4])) {
								$iswin ++;
								$value ['gold']+= ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 5:
							if (isset($value['OneTofive']['secondball'][5])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 6:
							if (isset($value['OneTofive']['secondball'][6])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 7:
							if (isset($value['OneTofive']['secondball'][7])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 8:
							if (isset($value['OneTofive']['secondball'][8])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 9:
							if (isset($value['OneTofive']['secondball'][9])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
					}
					//大小
					if (( int ) $result [1] >= 5 && isset($value['OneTofive']['secondball']['big'])) {
						$value ['win'] ['一至五'] ['第二球大小'] = '大';
						$iswin ++;
						$value ['gold'] += ($odds ['一至五'] ['大'] * $value ['win'] ['单注金额']);
					}else if (( int ) $result [1] <= 4 && isset($value['OneTofive']['secondball']['small'])){
						$value ['win'] ['一至五'] ['第二球大小'] = '小';
						$iswin ++;
						$value ['gold'] += ($odds ['一至五'] ['小'] * $value ['win'] ['单注金额']);
					}
					//单双
					if (( int ) $result [1] % 2 === 0 && isset($value['OneTofive']['secondball']['Both'])) {
						$value ['win'] ['一至五'] ['第二球单双'] = '双';
						$iswin ++;
						$value ['gold'] += ($odds ['一至五'] ['双'] * $value ['win'] ['单注金额']);
					}else if (( int ) $result [1] % 2 !== 0 && isset($value['OneTofive']['secondball']['One'])){
						$value ['win'] ['一至五'] ['第二球单双'] = '单';
						$iswin ++;
						$value ['gold'] += ($odds ['一至五'] ['单'] * $value ['win'] ['单注金额']);
					}
					//质合
					if (isset($zhi [( int ) $result [1]]) && isset ( $value ['OneTofive'] ['secondball'] ['Zhi'] )) {
						$value ['win'] ['一至五'] ['第二球质合'] = '质';
						$iswin ++;
						$value ['gold'] += ($odds ['一至五'] ['质'] * $value ['win'] ['单注金额']);
					} else if (isset($he [( int ) $result [1]]) && isset ( $value ['OneTofive'] ['secondball'] ['He'] )) {
						$value ['win'] ['一至五'] ['第二球质合'] = '合';
						$iswin ++;
						$value ['gold'] += ($odds ['一至五'] ['合'] * $value ['win'] ['单注金额']);
					}
				}
				//第三球
				if (count($value ['OneTofive']['thirdball']) > 0){
					$value['OneTofive']['thirdball'] = array_flip($value['OneTofive']['thirdball']);
					switch (( int )$result[2]) {
						case 0:
							if (isset($value['OneTofive']['thirdball'][0])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 1:
							if (isset($value['OneTofive']['thirdball'][1])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 2:
							if (isset($value['OneTofive']['thirdball'][2])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 3:
							if (isset($value['OneTofive']['thirdball'][3])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 4:
							if (isset($value['OneTofive']['thirdball'][4])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 5:
							if (isset($value['OneTofive']['thirdball'][5])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 6:
							if (isset($value['OneTofive']['thirdball'][6])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 7:
							if (isset($value['OneTofive']['thirdball'][7])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 8:
							if (isset($value['OneTofive']['thirdball'][8])) {
								$iswin ++;
								$value ['gold'] +=($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 9:
							if (isset($value['OneTofive']['thirdball'][9])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
					}
					//大小
					if (( int ) $result [2] >= 5 && isset($value['OneTofive']['thirdball']['big'])) {
						$value ['win'] ['一至五'] ['第三球大小'] = '大';
						$iswin ++;
						$value ['gold'] +=($odds ['一至五'] ['大'] * $value ['win'] ['单注金额']);
					}else if (( int ) $result [2] <= 4 && isset($value['OneTofive']['thirdball']['small'])){
						$value ['win'] ['一至五'] ['第三球大小'] = '小';
						$iswin ++;
						$value ['gold']+= ($odds ['一至五'] ['小'] * $value ['win'] ['单注金额']);
					}
					//单双
					if (( int ) $result [2] % 2 === 0 && isset($value['OneTofive']['thirdball']['Both'])) {
						$value ['win'] ['一至五'] ['第三球单双'] = '双';
						$iswin ++;
						$value ['gold']+= ($odds ['一至五'] ['双'] * $value ['win'] ['单注金额']);
					}else if (( int ) $result [2] % 2 !== 0 && isset($value['OneTofive']['thirdball']['One'])){
						$value ['win'] ['一至五'] ['第三球单双'] = '单';
						$iswin ++;
						$value ['gold']+= ($odds ['一至五'] ['单'] * $value ['win'] ['单注金额']);
					}
					//质合
					if (isset($zhi [( int ) $result [2]]) && isset ( $value ['OneTofive'] ['thirdball'] ['Zhi'] )) {
						$value ['win'] ['一至五'] ['第三球质合'] = '质';
						$iswin ++;
						$value ['gold'] += ($odds ['一至五'] ['质'] * $value ['win'] ['单注金额']);
					} else if (isset($he [( int ) $result [2]]) && isset ( $value ['OneTofive'] ['thirdball'] ['He'] )) {
						$value ['win'] ['一至五'] ['第三球质合'] = '合';
						$iswin ++;
						$value ['gold'] += ($odds ['一至五'] ['合'] * $value ['win'] ['单注金额']);
					}
				}
				//第四球
				if (count($value ['OneTofive']['thouthball']) > 0){
					$value['OneTofive']['thouthball'] = array_flip($value['OneTofive']['thouthball']);
					switch (( int )$result[3]) {
						case 0:
							if (isset($value['OneTofive']['thouthball'][0])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 1:
							if (isset($value['OneTofive']['thouthball'][1])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 2:
							if (isset($value['OneTofive']['thouthball'][2])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 3:
							if (isset($value['OneTofive']['thouthball'][3])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 4:
							if (isset($value['OneTofive']['thouthball'][4])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 5:
							if (isset($value['OneTofive']['thouthball'][5])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 6:
							if (isset($value['OneTofive']['thouthball'][6])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 7:
							if (isset($value['OneTofive']['thouthball'][7])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 8:
							if (isset($value['OneTofive']['thouthball'][8])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 9:
							if (isset($value['OneTofive']['thouthball'][9])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
					}
					//大小
					if (( int ) $result [3] >= 5 && isset($value['OneTofive']['thouthball']['big'])) {
						$value ['win'] ['一至五'] ['第四球大小'] = '大';
						$iswin ++;
						$value ['gold'] += ($odds ['一至五'] ['大'] * $value ['win'] ['单注金额']);
					}else if (( int ) $result [3] <= 4 && isset($value['OneTofive']['thouthball']['small'])){
						$value ['win'] ['一至五'] ['第四球大小'] = '小';
						$iswin ++;
						$value ['gold'] += ($odds ['一至五'] ['小'] * $value ['win'] ['单注金额']);
					}
					//单双
					if (( int ) $result [3] % 2 === 0 && isset($value['OneTofive']['thouthball']['Both'])) {
						$value ['win'] ['一至五'] ['第四球单双'] = '双';
						$iswin ++;
						$value ['gold'] += ($odds ['一至五'] ['双'] * $value ['win'] ['单注金额']);
					}else if (( int ) $result [3] % 2 !== 0 && isset($value['OneTofive']['thouthball']['One'])){
						$value ['win'] ['一至五'] ['第四球单双'] = '单';
						$iswin ++;
						$value ['gold'] += ($odds ['一至五'] ['单'] * $value ['win'] ['单注金额']);
					}
					//质合
					if (isset($zhi [( int ) $result [3]]) && isset ( $value ['OneTofive'] ['thouthball'] ['Zhi'] )) {
						$value ['win'] ['一至五'] ['第四球质合'] = '质';
						$iswin ++;
						$value ['gold'] += ($odds ['一至五'] ['质'] * $value ['win'] ['单注金额']);
					} else if (isset($he [( int ) $result [3]]) && isset ( $value ['OneTofive'] ['thouthball'] ['He'] )) {
						$value ['win'] ['一至五'] ['第四球质合'] = '合';
						$iswin ++;
						$value ['gold'] += ($odds ['一至五'] ['合'] * $value ['win'] ['单注金额']);
					}
				}
				//第五球
				if (count($value ['OneTofive']['fifthball']) > 0){
					$value['OneTofive']['fifthball'] = array_flip($value['OneTofive']['fifthball']);
					switch (( int )$result[4]) {
						case 0:
							if (isset($value['OneTofive']['fifthball'][0])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 1:
							if (isset($value['OneTofive']['fifthball'][1])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 2:
							if (isset($value['OneTofive']['fifthball'][2])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 3:
							if (isset($value['OneTofive']['fifthball'][3])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 4:
							if (isset($value['OneTofive']['fifthball'][4])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 5:
							if (isset($value['OneTofive']['fifthball'][5])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 6:
							if (isset($value['OneTofive']['fifthball'][6])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 7:
							if (isset($value['OneTofive']['fifthball'][7])) {
								$iswin ++;
								$value ['gold'] +=($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 8:
							if (isset($value['OneTofive']['fifthball'][8])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
						case 9:
							if (isset($value['OneTofive']['fifthball'][9])) {
								$iswin ++;
								$value ['gold'] += ($odds ['一至五'] [0] * $value ['win'] ['单注金额']);
							}
							break;
					}
					//大小
					if (( int ) $result [4] >= 5 && isset($value['OneTofive']['fifthball']['big'])) {
						$value ['win'] ['一至五'] ['第五球大小'] = '大';
						$iswin ++;
						$value ['gold'] += ($odds ['一至五'] ['大'] * $value ['win'] ['单注金额']);
					}else if (( int ) $result [4] <= 4 && isset($value['OneTofive']['fifthball']['small'])){
						$value ['win'] ['一至五'] ['第五球大小'] = '小';
						$iswin ++;
						$value ['gold'] += ($odds ['一至五'] ['小'] * $value ['win'] ['单注金额']);
					}
					//单双
					if (( int ) $result [4] % 2 === 0 && isset($value['OneTofive']['fifthball']['Both'])) {
						$value ['win'] ['一至五'] ['第五球单双'] = '双';
						$iswin ++;
						$value ['gold'] += ($odds ['一至五'] ['双'] * $value ['win'] ['单注金额']);
					}else if (( int ) $result [4] % 2 !== 0 && isset($value['OneTofive']['fifthball']['One'])){
						$value ['win'] ['一至五'] ['第五球单双'] = '单';
						$iswin ++;
						$value ['gold'] += ($odds ['一至五'] ['单'] * $value ['win'] ['单注金额']);
					}
					//质合
					if (isset($zhi [( int ) $result [4]]) && isset ( $value ['OneTofive'] ['fifthball'] ['Zhi'] )) {
						$value ['win'] ['一至五'] ['第五球质合'] = '质';
						$iswin ++;
						$value ['gold'] += ($odds ['一至五'] ['质'] * $value ['win'] ['单注金额']);
					} else if (isset($he [( int ) $result [4]]) && isset ( $value ['OneTofive'] ['fifthball'] ['He'] )) {
						$value ['win'] ['一至五'] ['第五球质合'] = '合';
						$iswin ++;
						$value ['gold'] += ($odds ['一至五'] ['合'] * $value ['win'] ['单注金额']);
					}
				}
					
			}
			//--------------------------------一字(☄⊙ω⊙)☄----------------
			if ((count ( $value ['OneWord'],1)-4) > 0) {
				//前三
				if (count($value ['OneWord']['firstThree'])) {
					//通过用对换键值对的方法来isset判断变量是否存在，其效率比in_array高很多，内存占用也少
					$value['OneWord']['firstThree'] = array_flip($value['OneWord']['firstThree']);
					foreach ($firstthree as $va) {
						if (isset($value['OneWord']['firstThree'][$va])) {
							$iswin ++;
							$value ['gold'] += ($odds ['一字']['前三'] * $value ['win'] ['单注金额']) ;
							break;
						}
					}
				}
				//中三
				if (count($value ['OneWord']['middleThree'])) {
					//通过用对换键值对的方法来isset判断变量是否存在，其效率比in_array高很多，内存占用也少
					$value['OneWord']['middleThree'] = array_flip($value['OneWord']['middleThree']);
					foreach ($middlethree as $va) {
						if (isset($value['OneWord']['middleThree'][$va])) {
							$iswin ++;
							$value ['gold'] += ($odds ['一字']['中三'] * $value ['win'] ['单注金额']) ;
							break;
						}
					}
				}
				//后三
				if (count($value ['OneWord']['lastThree'])) {
					//通过用对换键值对的方法来isset判断变量是否存在，其效率比in_array高很多，内存占用也少
					$value['OneWord']['lastThree'] = array_flip($value['OneWord']['lastThree']);
					foreach ($lastthree as $va) {
						if (isset($value['OneWord']['lastThree'][$va])) {
							$iswin ++;
							$value ['gold'] += ($odds ['一字']['后三'] * $value ['win'] ['单注金额']) ;
							break;
						}
					}
				}
				//全五
				if (count($value ['OneWord']['allFive'])) {
					//通过用对换键值对的方法来isset判断变量是否存在，其效率比in_array高很多，内存占用也少
					$value['OneWord']['allFive'] = array_flip($value['OneWord']['allFive']);
					$allFive=$result;
					$allFive = array_flip($allFive);//去除相同的
					$allFive = array_flip($allFive);
					foreach ($allFive as $va) {
						if (isset($value['OneWord']['allFive'][$va])) {
							$iswin ++;
							$value ['gold'] += ($odds ['一字']['全五'] * $value ['win'] ['单注金额']) ;
							break;
						}
					}
				}
			}
			//-----------------------------------二字组合 (☄⊙ω⊙)☄----------------
			if ((count ( $value ['TwoWord'],1)-3) > 0) {
				$twoWord=array(00,11,22,33,44,55,66,77,88,99);//相同的数赔率不一样
				$twoWord = array_flip ( $twoWord);
				//前三
				if (count($value ['TwoWord']['firstThree']) > 0) {
					$ftwo[0]=(int)($firstthree[0].$firstthree[1]);//组合
					$ftwo[1]=(int)($firstthree[0].$firstthree[2]);
					$ftwo[2]=(int)($firstthree[1].$firstthree[2]);
					$ftwo = array_flip($ftwo);//去除相同的
					$ftwo = array_flip($ftwo);
					$value['TwoWord']['firstThree'] = array_flip($value['TwoWord']['firstThree']);
					$true=false;
					foreach ($ftwo as $va) {
						if (isset($twoWord[$va])) {
							$true=true;
						}
						if (isset($value['TwoWord']['firstThree'][$va]) && $true===true){
							$iswin++;
							$value ['gold'] += ($odds ['二字'][1] * $value ['win'] ['单注金额']);
						}else if (isset($value['TwoWord']['firstThree'][$va]) && $true===false ){
							$iswin++;
							$value ['gold'] += ($odds ['二字'][0] * $value ['win'] ['单注金额']);
						}
					}
				}
				//中三
				if (count($value ['TwoWord']['middleThree']) > 0) {
					$mtwo[0]=(int)($middlethree[0].$middlethree[1]);//组合
					$mtwo[1]=(int)($middlethree[0].$middlethree[2]);
					$mtwo[2]=(int)($middlethree[1].$middlethree[2]);
					$mtwo = array_flip($mtwo);//去除相同的
					$mtwo = array_flip($mtwo);
					$value['TwoWord']['middleThree'] = array_flip($value['TwoWord']['middleThree']);
					$true=false;
					foreach ($mtwo as $va) {
						if (isset($twoWord[$va])) {
							$true=true;
						}
						if (isset($value['TwoWord']['middleThree'][$va]) && $true===true){
							$iswin++;
							$value ['gold'] += ($odds ['二字'][1] * $value ['win'] ['单注金额']);
						}else if (isset($value['TwoWord']['middleThree'][$va]) && $true===false ){
							$iswin++;
							$value ['gold'] += ($odds ['二字'][0] * $value ['win'] ['单注金额']);
						}
					}
				}
				//后三
				if (count($value ['TwoWord']['lastThree']) > 0) {
					$ltwo[0]=(int)($lastthree[0].$lastthree[1]);//组合
					$ltwo[1]=(int)($lastthree[0].$lastthree[2]);
					$ltwo[2]=(int)($lastthree[1].$lastthree[2]);
					$ltwo = array_flip($ltwo);//去除相同的
					$ltwo = array_flip($ltwo);
					$value['TwoWord']['lastThree'] = array_flip($value['TwoWord']['lastThree']);
					$true=false;
					foreach ($ltwo as $va) {
						if (isset($twoWord[$va])) {
							$true=true;
						}
						if (isset($value['TwoWord']['lastThree'][$va]) && $true===true){
							$iswin++;
							$value ['gold'] += ($odds ['二字'][1] * $value ['win'] ['单注金额']);
						}else if (isset($value['TwoWord']['lastThree'][$va]) && $true===false ){
							$iswin++;
							$value ['gold'] += ($odds ['二字'][0] * $value ['win'] ['单注金额']);
						}
					}
				}
			}
			//--------------------------------------------三字组合(☄⊙ω⊙)☄---------------------
			if ((count ( $value ['ThreeWord'],1)-3) > 0) {
				$twoWord=array(001,002,003,004,005,006,007,008,009,011,022,044,077,088,099,112,113,114,115,
						116,117,118,119,122,133,144,155,166,177,188,199,223,224,225,226,227,228,229,233,244,
						255,266,277,288,299,334,335,336,337,338,339,344,355,366,377,388,399,445,446,447,448,
						449,455,466,477,488,499,556,557,558,559,566,577,588,599,667,668,669,677,688,699,778,
						779,788,799,889,899);//相同的数赔率不一样
				$threeWord=array(000,111,222,333,444,555,666,777,888,999);//相同的数赔率不一样
				$twoWord = array_flip ( $twoWord);
				$threeWord = array_flip ( $threeWord);
					// 前三
				if (count ( $value ['ThreeWord'] ['firstThree'] ) > 0) {
					$value ['ThreeWord'] ['firstThree'] = array_flip ( $value ['ThreeWord'] ['firstThree'] );
					$number = ( int ) ($firstthree [0] . $firstthree [1] . $firstthree [2]);
					$true = 0;
					if (isset ( $twoWord [$number] )) {
						$true = 1;
					} else if (isset ( $threeWord [$number] )) {
						$true = 2;
					}
					if (isset ( $value ['ThreeWord'] ['firstThree'] [$number] ) && $true === 1) {
						$iswin ++;
						$value ['gold'] += ($odds ['三字'] [2] * $value ['win'] ['单注金额']);
					} else if (isset ( $value ['ThreeWord'] ['firstThree'] [$number] ) && $true === 2) {
						$iswin ++;
						$value ['gold'] += ($odds ['三字'] [1] * $value ['win'] ['单注金额']);
					} else if (isset ( $value ['ThreeWord'] ['firstThree'] [$number] ) && $true === 0) {
						$iswin ++;
						$value ['gold'] += ($odds ['三字'] [0] * $value ['win'] ['单注金额']);
					}
				}
				// 中三
				if (count ( $value ['ThreeWord'] ['middleThree'] ) > 0) {
					$value ['ThreeWord'] ['middleThree'] = array_flip ( $value ['ThreeWord'] ['middleThree'] );
					$number = ( int ) ($middlethree [0] . $middlethree [1] . $middlethree [2]);
					$true = 0;
					if (isset ( $twoWord [$number] )) {
						$true = 1;
					} else if (isset ( $threeWord [$number] )) {
						$true = 2;
					}
					if (isset ( $value ['ThreeWord'] ['middleThree'] [$number] ) && $true === 1) {
						$iswin ++;
						$value ['gold'] += ($odds ['三字'] [2] * $value ['win'] ['单注金额']);
					} else if (isset ( $value ['ThreeWord'] ['middleThree'] [$number] ) && $true === 2) {
						$iswin ++;
						$value ['gold'] += ($odds ['三字'] [1] * $value ['win'] ['单注金额']);
					} else if (isset ( $value ['ThreeWord'] ['middleThree'] [$number] ) && $true === 0) {
						$iswin ++;
						$value ['gold'] += ($odds ['三字'] [0] * $value ['win'] ['单注金额']);
					}
				}
				// 后三
				if (count ( $value ['ThreeWord'] ['lastThree'] ) > 0) {
					$value ['ThreeWord'] ['lastThree'] = array_flip ( $value ['ThreeWord'] ['lastThree'] );
					$number = ( int ) ($lastthree [0] . $lastthree [1] . $lastthree [2]);
					$true = 0;
					if (isset ( $twoWord [$number] )) {
						$true = 1;
					} else if (isset ( $threeWord [$number] )) {
						$true = 2;
					}
					if (isset ( $value ['ThreeWord'] ['lastThree'] [$number] ) && $true === 1) {
						$iswin ++;
						$value ['gold'] += ($odds ['三字'] [2] * $value ['win'] ['单注金额']);
					} else if (isset ( $value ['ThreeWord'] ['lastThree'] [$number] ) && $true === 2) {
						$iswin ++;
						$value ['gold'] += ($odds ['三字'] [1] * $value ['win'] ['单注金额']);
					} else if (isset ( $value ['ThreeWord'] ['lastThree'] [$number] ) && $true === 0) {
						$iswin ++;
						$value ['gold'] += ($odds ['三字'] [0] * $value ['win'] ['单注金额']);
					}
				}
			}
			//--------------------------------------------二字定位(☄⊙ω⊙)☄---------------------
			if ((count ( $value ['Twolocation'],1)-10)) {
				//万千
				if (count($value['Twolocation']['OOXXX']['firstball']) > 0 && count($value['Twolocation']['OOXXX']['secondball']) > 0) {
					$value['Twolocation']['OOXXX']['firstball'] = array_flip($value['Twolocation']['OOXXX']['firstball']);
					$value['Twolocation']['OOXXX']['secondball'] = array_flip($value['Twolocation']['OOXXX']['secondball']);
					if (isset($value['Twolocation']['OOXXX']['firstball'][(int)$result[0]] )
							&& isset($value['Twolocation']['OOXXX']['secondball'][(int)$result[1]] )) {
								$iswin++;
								$value ['gold']+=($odds['二定位'] * $value ['win'] ['单注金额']);
							}
				}
				//万百
				if (count($value['Twolocation']['OXOXX']['firstball']) > 0 && count($value['Twolocation']['OXOXX']['secondball']) > 0) {
					$value['Twolocation']['OXOXX']['firstball'] = array_flip($value['Twolocation']['OXOXX']['firstball']);
					$value['Twolocation']['OXOXX']['secondball'] = array_flip($value['Twolocation']['OXOXX']['secondball']);
					if (isset($value['Twolocation']['OXOXX']['firstball'][(int)$result[0]] )
							&& isset($value['Twolocation']['OXOXX']['secondball'][(int)$result[2]] )) {
								$iswin++;
								$value ['gold']+=($odds['二定位'] * $value ['win'] ['单注金额']);
							}
				}
				//万十
				if (count($value['Twolocation']['OXXOX']['firstball']) > 0 && count($value['Twolocation']['OXXOX']['secondball']) > 0) {
					$value['Twolocation']['OXXOX']['firstball'] = array_flip($value['Twolocation']['OXXOX']['firstball']);
					$value['Twolocation']['OXXOX']['secondball'] = array_flip($value['Twolocation']['OXXOX']['secondball']);
					if (isset($value['Twolocation']['OXXOX']['firstball'][(int)$result[0]] )
							&& isset($value['Twolocation']['OXXOX']['secondball'][(int)$result[3]] )) {
								$iswin++;
								$value ['gold']+=($odds['二定位'] * $value ['win'] ['单注金额']);
							}
				}
				//万个
				if (count($value['Twolocation']['OXXXO']['firstball']) > 0 && count($value['Twolocation']['OXXXO']['secondball']) > 0) {
					$value['Twolocation']['OXXXO']['firstball'] = array_flip($value['Twolocation']['OXXXO']['firstball']);
					$value['Twolocation']['OXXXO']['secondball'] = array_flip($value['Twolocation']['OXXXO']['secondball']);
					if (isset($value['Twolocation']['OXXXO']['firstball'][(int)$result[0]] )
							&& isset($value['Twolocation']['OXXXO']['secondball'][(int)$result[4]] )) {
								$iswin++;
								$value ['gold']+=($odds['二定位'] * $value ['win'] ['单注金额']);
							}
				}
				//千百
				if (count($value['Twolocation']['XOOXX']['firstball']) > 0 && count($value['Twolocation']['XOOXX']['secondball']) > 0) {
					$value['Twolocation']['XOOXX']['firstball'] = array_flip($value['Twolocation']['XOOXX']['firstball']);
					$value['Twolocation']['XOOXX']['secondball'] = array_flip($value['Twolocation']['XOOXX']['secondball']);
					if (isset($value['Twolocation']['XOOXX']['firstball'][(int)$result[1]] )
							&& isset($value['Twolocation']['XOOXX']['secondball'][(int)$result[2]] )) {
								$iswin++;
								$value ['gold']+=($odds['二定位'] * $value ['win'] ['单注金额']);
							}
				}
				//千十
				if (count($value['Twolocation']['XOXOX']['firstball']) > 0 && count($value['Twolocation']['XOXOX']['secondball']) > 0) {
					$value['Twolocation']['XOXOX']['firstball'] = array_flip($value['Twolocation']['XOXOX']['firstball']);
					$value['Twolocation']['XOXOX']['secondball'] = array_flip($value['Twolocation']['XOXOX']['secondball']);
					if (isset($value['Twolocation']['XOXOX']['firstball'][(int)$result[1]] )
							&& isset($value['Twolocation']['XOXOX']['secondball'][(int)$result[3]] )) {
								$iswin++;
								$value ['gold']+=($odds['二定位'] * $value ['win'] ['单注金额']);
							}
				}
				//千个
				if (count($value['Twolocation']['XOXXO']['firstball']) > 0 && count($value['Twolocation']['XOXXO']['secondball']) > 0) {
					$value['Twolocation']['XOXXO']['firstball'] = array_flip($value['Twolocation']['XOXXO']['firstball']);
					$value['Twolocation']['XOXXO']['secondball'] = array_flip($value['Twolocation']['XOXXO']['secondball']);
					if (isset($value['Twolocation']['XOXXO']['firstball'][(int)$result[1]] )
							&& isset($value['Twolocation']['XOXXO']['secondball'][(int)$result[4]] )) {
								$iswin++;
								$value ['gold']+=($odds['二定位'] * $value ['win'] ['单注金额']);
							}
				}
				//百十
				if (count($value['Twolocation']['XXOOX']['firstball']) > 0 && count($value['Twolocation']['XXOOX']['secondball']) > 0) {
					$value['Twolocation']['XXOOX']['firstball'] = array_flip($value['Twolocation']['XXOOX']['firstball']);
					$value['Twolocation']['XXOOX']['secondball'] = array_flip($value['Twolocation']['XXOOX']['secondball']);
					if (isset($value['Twolocation']['XXOOX']['firstball'][(int)$result[2]] )
							&& isset($value['Twolocation']['XXOOX']['secondball'][(int)$result[3]] )) {
								$iswin++;
								$value ['gold']+=($odds['二定位'] * $value ['win'] ['单注金额']);
							}
				}
				//百个
				if (count($value['Twolocation']['XXOXO']['firstball']) > 0 && count($value['Twolocation']['XXOXO']['secondball']) > 0) {
					$value['Twolocation']['XXOXO']['firstball'] = array_flip($value['Twolocation']['XXOXO']['firstball']);
					$value['Twolocation']['XXOXO']['secondball'] = array_flip($value['Twolocation']['XXOXO']['secondball']);
					if (isset($value['Twolocation']['XXOXO']['firstball'][(int)$result[2]] )
							&& isset($value['Twolocation']['XXOXO']['secondball'][(int)$result[4]] )) {
								$iswin++;
								$value ['gold']+=($odds['二定位'] * $value ['win'] ['单注金额']);
							}
				}
				//十个
				if (count($value['Twolocation']['XXXOO']['firstball']) > 0 && count($value['Twolocation']['XXXOO']['secondball']) > 0) {
					$value['Twolocation']['XXXOO']['firstball'] = array_flip($value['Twolocation']['XXXOO']['firstball']);
					$value['Twolocation']['XXXOO']['secondball'] = array_flip($value['Twolocation']['XXXOO']['secondball']);
					if (isset($value['Twolocation']['XXXOO']['firstball'][(int)$result[3]] )
							&& isset($value['Twolocation']['XXXOO']['secondball'][(int)$result[4]] )) {
								$iswin++;
								$value ['gold']+=($odds['二定位'] * $value ['win'] ['单注金额']);
							}
				}
			}
			//--------------------------------------------三字定位(☄⊙ω⊙)☄---------------------
			if ((count ( $value ['Threelocation'],1)-12)) {
				//前三
				if (count ( $value ['Threelocation'] ['firstThree'] ['firstball'] ) > 0 && count ( $value ['Threelocation'] ['firstThree'] ['secondball'] ) > 0 && count ( $value ['Threelocation'] ['firstThree'] ['thirdball'] ) > 0) {
					$value['Threelocation'] ['firstThree'] ['firstball'] = array_flip($value['Threelocation'] ['firstThree'] ['firstball']);
					$value['Threelocation'] ['firstThree'] ['secondball'] = array_flip($value['Threelocation'] ['firstThree'] ['secondball']);
					$value['Threelocation'] ['firstThree'] ['thirdball'] = array_flip($value['Threelocation'] ['firstThree'] ['thirdball']);
					if (isset($value['Threelocation'] ['firstThree']['firstball'][(int)$result[0]] )
							&& isset($value['Threelocation'] ['firstThree']['secondball'][(int)$result[1]] )
							&& isset($value['Threelocation'] ['firstThree']['thirdball'][(int)$result[2]] )) {
								$iswin++;
								$value ['gold']+=($odds['三定位'] * $value ['win'] ['单注金额']);
							}
				}
				//中三
				if (count ( $value ['Threelocation'] ['middleThree'] ['firstball'] ) > 0 && count ( $value ['Threelocation'] ['middleThree'] ['secondball'] ) > 0 && count ( $value ['Threelocation'] ['middleThree'] ['thirdball'] ) > 0) {
					$value['Threelocation'] ['middleThree'] ['firstball'] = array_flip($value['Threelocation'] ['middleThree'] ['firstball']);
					$value['Threelocation'] ['middleThree'] ['secondball'] = array_flip($value['Threelocation'] ['middleThree'] ['secondball']);
					$value['Threelocation'] ['middleThree'] ['thirdball'] = array_flip($value['Threelocation'] ['middleThree'] ['thirdball']);
					if (isset($value['Threelocation'] ['middleThree']['firstball'][(int)$result[1]] )
							&& isset($value['Threelocation'] ['middleThree']['secondball'][(int)$result[2]] )
							&& isset($value['Threelocation'] ['middleThree']['thirdball'][(int)$result[3]] )) {
								$iswin++;
								$value ['gold']+=($odds['三定位'] * $value ['win'] ['单注金额']);
							}
				}
				//后三
				if (count ( $value ['Threelocation'] ['lastThree'] ['firstball'] ) > 0 && count ( $value ['Threelocation'] ['lastThree'] ['secondball'] ) > 0 && count ( $value ['Threelocation'] ['lastThree'] ['thirdball'] ) > 0) {
					$value['Threelocation'] ['lastThree'] ['firstball'] = array_flip($value['Threelocation'] ['lastThree'] ['firstball']);
					$value['Threelocation'] ['lastThree'] ['secondball'] = array_flip($value['Threelocation'] ['lastThree'] ['secondball']);
					$value['Threelocation'] ['lastThree'] ['thirdball'] = array_flip($value['Threelocation'] ['lastThree'] ['thirdball']);
					if (isset($value['Threelocation'] ['lastThree']['firstball'][(int)$result[2]] )
							&& isset($value['Threelocation'] ['lastThree']['secondball'][(int)$result[3]] )
							&& isset($value['Threelocation'] ['lastThree']['thirdball'][(int)$result[4]] )) {
								$iswin++;
								$value ['gold']+=($odds['三定位'] * $value ['win'] ['单注金额']);
							}
				}
			}
			//--------------------------------------------组选三(☄⊙ω⊙)☄---------------------
			if ((count ( $value ['groupThree'],1)-3)) {
				//前三
				if (count($value ['groupThree']['firstThree']) > 0) {
					$value ['groupThree']['firstThree'] = array_flip($value ['groupThree']['firstThree']);
					$number=array();
					$number[0]=(int)$result[0];
					$number[1]=(int)$result[1];
					$number[2]=(int)$result[2];
					$group[0]=$number[2] - $number[1] === 0 ? 1 : 0;
					$group[0] = $number[1] - $number[0] === 0 ? ++$group[0] : $group[0];
					$i=0;
					foreach ($number as $vf) {
						if ($group[0] === 1 && isset($value ['groupThree']['firstThree'][$vf])) {
							$i++;
						}
					}
					if ($i > 2) {
						$iswin ++;
						$value ['gold'] += ($odds ['组选三'] * $value ['win'] ['单注金额']);
					}
				}
				//中三
				if (count($value ['groupThree']['middleThree']) > 0) {
					$value ['groupThree']['middleThree'] = array_flip($value ['groupThree']['middleThree']);
					$number[0]=(int)$result[1];
					$number[1]=(int)$result[2];
					$number[2]=(int)$result[3];
					$group[0]=$number[2] - $number[1] === 0 ? 1 : 0;
					$group[0] = $number[1] - $number[0] === 0 ? ++$group[0] : $group[0];
					$i=0;
					foreach ($number as $vf) {
						if ($group[0] === 1 && isset($value ['groupThree']['middleThree'][$vf])) {
							$i++;
						}
					}
					if ($i > 2) {
						$iswin ++;
						$value ['gold'] += ($odds ['组选三'] * $value ['win'] ['单注金额']);
					}
				}
				//后三
				if (count($value ['groupThree']['lastThree']) > 0) {
					$value ['groupThree']['lastThree'] = array_flip($value ['groupThree']['lastThree']);
					$number[0]=(int)$result[2];
					$number[1]=(int)$result[3];
					$number[2]=(int)$result[4];
					$group[0]=$number[2] - $number[1] === 0 ? 1 : 0;
					$group[0] = $number[1] - $number[0] === 0 ? ++$group[0] : $group[0];
					$i=0;
					foreach ($number as $vf) {
						if ($group[0] === 1 && isset($value ['groupThree']['lastThree'][$vf])) {
							$i++;
						}
					}
					if ($i > 2) {
						$iswin ++;
						$value ['gold'] += ($odds ['组选三'] * $value ['win'] ['单注金额']);
					}
				}
			}
			//--------------------------------------------组选六(☄⊙ω⊙)☄---------------------
			if ((count ( $value ['groupSix'],1)-3)) {
				//前三
				if (count($value ['groupSix']['firstThree']) > 0) {
					$firstNumber=array();//计算豹子、对子
					//如果第三球减去第二球等于零，真则返回1，否则则返回零
					$firstNumber[0] = $firstthree[2] - $firstthree[1] === 0 ? 1 : 0;
					//如果第二球减去第一球等于零，真则自增1（如果上面的$resNumber[0]是1则+1=2），否则返回上面的出的值，如果上面是1则还是1，如果不是1则是零
					$firstNumber[0] = $firstthree[1] - $firstthree[0] === 0 ? ++$firstNumber[0] : $firstNumber[0];
					$value ['groupSix']['firstThree'] = array_flip ( $value ['groupSix']['firstThree'] );
					$str='';
					$number = ( int ) ($firstthree [0] . $firstthree [1] . $firstthree [2]);
					foreach ($firstthree as $ve) {
						if (isset($value ['groupSix']['firstThree'][$ve])) {
							$str.=$ve;//下注的值叠加起来
						}
					}
					if ($firstNumber[0] === 0 && (int)$str === $number) {
						$iswin ++;
						$value ['gold'] += ($odds ['组选六'] * $value ['win'] ['单注金额']);
					}
				}
				//中三
				if (count($value ['groupSix']['middleThree']) > 0) {
					$middleNumber=array();//计算豹子、对子
					//如果第三球减去第二球等于零，真则返回1，否则则返回零
					$middleNumber[0] = $middlethree[2] - $middlethree[1] === 0 ? 1 : 0;
					//如果第二球减去第一球等于零，真则自增1（如果上面的$resNumber[0]是1则+1=2），否则返回上面的出的值，如果上面是1则还是1，如果不是1则是零
					$middleNumber[0] = $middlethree[1] - $middlethree[0] === 0 ? ++$middleNumber[0] : $middleNumber[0];
					$value ['groupSix']['middleThree'] = array_flip ( $value ['groupSix']['middleThree'] );
					$str='';
					$number = ( int ) ($middlethree [0] . $middlethree [1] . $middlethree [2]);
					foreach ($middlethree as $ve) {
						if (isset($value ['groupSix']['middleThree'][$ve])) {
							$str.=$ve;//下注的值叠加起来
						}
					}
					if ($middleNumber[0] === 0 && (int)$str === $number) {
						$iswin ++;
						$value ['gold'] += ($odds ['组选六'] * $value ['win'] ['单注金额']);
					}
				}
				//后三
				if (count($value ['groupSix']['lastThree']) > 0) {
					$lastNumber=array();//计算豹子、对子
					//如果第三球减去第二球等于零，真则返回1，否则则返回零
					$lastNumber[0] = $lastthree[2] - $lastthree[1] === 0 ? 1 : 0;
					//如果第二球减去第一球等于零，真则自增1（如果上面的$resNumber[0]是1则+1=2），否则返回上面的出的值，如果上面是1则还是1，如果不是1则是零
					$lastNumber[0] = $lastthree[1] - $lastthree[0] === 0 ? ++$lastNumber[0] : $lastNumber[0];
					$value ['groupSix']['lastThree'] = array_flip ( $value ['groupSix']['lastThree'] );
					$str='';
					$number = ( int ) ($lastthree [0] . $lastthree [1] . $lastthree [2]);
					foreach ($lastthree as $ve) {
						if (isset($value ['groupSix']['lastThree'][$ve])) {
							$str.=$ve;//下注的值叠加起来
						}
					}
					if ($lastNumber[0] === 0 && (int)$str === $number) {
						$iswin ++;
						$value ['gold'] += ($odds ['组选六'] * $value ['win'] ['单注金额']);
					}
				}
			}
			//--------------------------------------------跨度(☄⊙ω⊙)☄---------------------
			if ((count ( $value ['span'],1)-3)) {
				//前三
				if (count($value ['span']['firstThree'])>0) {
					$value ['span']['firstThree'] = array_flip ( $value ['span']['firstThree'] );
					$span=max($firstthree)-min($firstthree);//最大值减去最小值得出的值便是跨度
					if (isset($value ['span']['firstThree'][$span])) {
						$iswin ++;
						$value ['gold'] += ($odds ['跨度'] * $value ['win'] ['单注金额']);
					}
				}
				//中三
				if (count($value ['span']['middleThree'])>0) {
					$value ['span']['middleThree'] = array_flip ( $value ['span']['middleThree'] );
					$span=max($middlethree)-min($middlethree);//最大值减去最小值得出的值便是跨度
					if (isset($value ['span']['middleThree'][$span])) {
						$iswin ++;
						$value ['gold'] += ($odds ['跨度'] * $value ['win'] ['单注金额']);
					}
				}
				//后三
				if (count($value ['span']['lastThree'])>0) {
					$value ['span']['lastThree'] = array_flip ( $value ['span']['lastThree'] );
					$span=max($lastthree)-min($lastthree);//最大值减去最小值得出的值便是跨度
					if (isset($value ['span']['lastThree'][$span])) {
						$iswin ++;
						$value ['gold'] += ($odds ['跨度'] * $value ['win'] ['单注金额']);
					}
				}
			
			}
			//--------------------------------------------跨度(☄⊙ω⊙)☄---------------------
			if ((count ( $value ['and'],1)-10)) {
				//万千
				if (count($value ['and']['OOXXX'])) {
					$value['and']['OOXXX'] = array_flip($value['and']['OOXXX']);
					if (((int)$result[0]+(int)$result[1]) % 2 === 0 && isset($value ['and']['OOXXX']['Both'])) {
						$value ['win'] ['万千单双'] = '双';
						$iswin ++;
						$value ['gold']+= ($odds ['和数']['双'] * $value ['win'] ['单注金额']);
					}else if (((int)$result[0]+(int)$result[1]) % 2 !== 0 && isset($value['and']['OOXXX']['One'])){
						$value ['win'] ['万千单双'] = '单';
						$iswin ++;
						$value ['gold']+= ($odds ['和数']['单'] * $value ['win'] ['单注金额']);
					}
				}
				//万百
				if (count($value ['and']['OXOXX'])) {
					$value['and']['OXOXX'] = array_flip($value['and']['OXOXX']);
					if (((int)$result[0]+(int)$result[2]) % 2 === 0 && isset($value ['and']['OXOXX']['Both'])) {
						$value ['win'] ['万百单双'] = '双';
						$iswin ++;
						$value ['gold']+= ($odds ['和数']['双'] * $value ['win'] ['单注金额']);
					}else if (((int)$result[0]+(int)$result[2]) % 2 !== 0 && isset($value['and']['OXOXX']['One'])){
						$value ['win'] ['万百单双'] = '单';
						$iswin ++;
						$value ['gold']+= ($odds ['和数']['单'] * $value ['win'] ['单注金额']);
					}
				}
				//万十
				if (count($value ['and']['OXXOX'])) {
					$value['and']['OXXOX'] = array_flip($value['and']['OXXOX']);
					if (((int)$result[0]+(int)$result[3]) % 2 === 0 && isset($value ['and']['OXXOX']['Both'])) {
						$value ['win'] ['万十单双'] = '双';
						$iswin ++;
						$value ['gold']+= ($odds ['和数']['双'] * $value ['win'] ['单注金额']);
					}else if (((int)$result[0]+(int)$result[3]) % 2 !== 0 && isset($value['and']['OXXOX']['One'])){
						$value ['win'] ['万十单双'] = '单';
						$iswin ++;
						$value ['gold']+= ($odds ['和数']['单'] * $value ['win'] ['单注金额']);
					}
				}
				//万个
				if (count($value ['and']['OXXXO'])) {
					$value['and']['OXXXO'] = array_flip($value['and']['OXXXO']);
					if (((int)$result[0]+(int)$result[4]) % 2 === 0 && isset($value ['and']['OXXXO']['Both'])) {
						$value ['win'] ['万个单双'] = '双';
						$iswin ++;
						$value ['gold']+= ($odds ['和数']['双'] * $value ['win'] ['单注金额']);
					}else if (((int)$result[0]+(int)$result[4]) % 2 !== 0 && isset($value['and']['OXXXO']['One'])){
						$value ['win'] ['万个单双'] = '单';
						$iswin ++;
						$value ['gold']+= ($odds ['和数']['单'] * $value ['win'] ['单注金额']);
					}
				}
				//千百
				if (count($value ['and']['XOOXX'])) {
					$value['and']['XOOXX'] = array_flip($value['and']['XOOXX']);
					if (((int)$result[1]+(int)$result[2]) % 2 === 0 && isset($value ['and']['XOOXX']['Both'])) {
						$value ['win'] ['千百单双'] = '双';
						$iswin ++;
						$value ['gold']+= ($odds ['和数']['双'] * $value ['win'] ['单注金额']);
					}else if (((int)$result[1]+(int)$result[2]) % 2 !== 0 && isset($value['and']['XOOXX']['One'])){
						$value ['win'] ['千百单双'] = '单';
						$iswin ++;
						$value ['gold']+= ($odds ['和数']['单'] * $value ['win'] ['单注金额']);
					}
				}
				//千十
				if (count($value ['and']['XOXOX'])) {
					$value['and']['XOXOX'] = array_flip($value['and']['XOXOX']);
					if (((int)$result[1]+(int)$result[3]) % 2 === 0 && isset($value ['and']['XOXOX']['Both'])) {
						$value ['win'] ['千十单双'] = '双';
						$iswin ++;
						$value ['gold']+= ($odds ['和数']['双'] * $value ['win'] ['单注金额']);
					}else if (((int)$result[1]+(int)$result[3]) % 2 !== 0 && isset($value['and']['XOXOX']['One'])){
						$value ['win'] ['千十单双'] = '单';
						$iswin ++;
						$value ['gold']+= ($odds ['和数']['单'] * $value ['win'] ['单注金额']);
					}
				}
				//千个
				if (count($value ['and']['XOXXO'])) {
					$value['and']['XOXXO'] = array_flip($value['and']['XOXXO']);
					if (((int)$result[1]+(int)$result[4]) % 2 === 0 && isset($value ['and']['XOXXO']['Both'])) {
						$value ['win'] ['千个单双'] = '双';
						$iswin ++;
						$value ['gold']+= ($odds ['和数']['双'] * $value ['win'] ['单注金额']);
					}else if (((int)$result[1]+(int)$result[4]) % 2 !== 0 && isset($value['and']['XOXXO']['One'])){
						$value ['win'] ['千个单双'] = '单';
						$iswin ++;
						$value ['gold']+= ($odds ['和数']['单'] * $value ['win'] ['单注金额']);
					}
				}
				//百十
				if (count($value ['and']['XXOOX'])) {
					$value['and']['XXOOX'] = array_flip($value['and']['XXOOX']);
					if (((int)$result[2]+(int)$result[3]) % 2 === 0 && isset($value ['and']['XXOOX']['Both'])) {
						$value ['win'] ['百十单双'] = '双';
						$iswin ++;
						$value ['gold']+= ($odds ['和数']['双'] * $value ['win'] ['单注金额']);
					}else if (((int)$result[2]+(int)$result[3]) % 2 !== 0 && isset($value['and']['XXOOX']['One'])){
						$value ['win'] ['百十单双'] = '单';
						$iswin ++;
						$value ['gold']+= ($odds ['和数']['单'] * $value ['win'] ['单注金额']);
					}
				}
				//百个
				if (count($value ['and']['XXOXO'])) {
					$value['and']['XXOXO'] = array_flip($value['and']['XXOXO']);
					if (((int)$result[2]+(int)$result[4]) % 2 === 0 && isset($value ['and']['XXOXO']['Both'])) {
						$value ['win'] ['百个单双'] = '双';
						$iswin ++;
						$value ['gold']+= ($odds ['和数']['双'] * $value ['win'] ['单注金额']);
					}else if (((int)$result[2]+(int)$result[4]) % 2 !== 0 && isset($value['and']['XXOXO']['One'])){
						$value ['win'] ['百个单双'] = '单';
						$iswin ++;
						$value ['gold']+= ($odds ['和数']['单'] * $value ['win'] ['单注金额']);
					}
				}
				//十个
				if (count($value ['and']['XXXOO'])) {
					$value['and']['XXXOO'] = array_flip($value['and']['XXXOO']);
					if (((int)$result[3]+(int)$result[4]) % 2 === 0 && isset($value ['and']['XXXOO']['Both'])) {
						$value ['win'] ['十个单双'] = '双';
						$iswin ++;
						$value ['gold']+= ($odds ['和数']['双'] * $value ['win'] ['单注金额']);
					}else if (((int)$result[3]+(int)$result[4]) % 2 !== 0 && isset($value['and']['XXXOO']['One'])){
						$value ['win'] ['十个单双'] = '单';
						$iswin ++;
						$value ['gold']+= ($odds ['和数']['单'] * $value ['win'] ['单注金额']);
					}
				}
			}
			//--------------------------------------------牛牛(☄⊙ω⊙)☄---------------------
			if ((count ( $value ['niuniu'],1)-3)) {
				//计算五个号码中有多少个三组和数为十的倍数的组合
				list($a, $b, $c, $d, $e) = $result;
				if(!(($a + $b + $c)%10) && $a + $b + $c!==0){
					$res = ($d + $e) % 10;
				}else if(!(($a + $b + $d)%10) && $a + $b + $d!==0){
					$res = ($c + $e) % 10;
				}else if(!(($a + $b + $e)%10) && $a + $b + $e!==0){
					$res = ($c + $d) % 10;
				}else if(!(($a + $c + $d)%10) && $a + $c + $d!==0){
					$res = ($b + $e) % 10;
				}else if(!(($a + $c + $e)%10) && $a + $c + $e!==0){
					$res = ($b + $d) % 10;
				}else if(!(($a + $d + $e)%10) && $a + $d + $e!==0){
					$res = ($b + $c) % 10;
				}else if(!(($b + $c + $d)%10) && $b + $c + $d!==0){
					$res = ($a + $e) % 10;
				}else if(!(($b + $c + $e)%10) && $b + $c + $e!==0){
					$res = ($a + $d) % 10;
				}else if(!(($b + $d + $e)%10) && $b + $d + $e!==0){
					$res = ($a + $c) % 10;
				}else if(!(($c + $d + $e)%10) && $c + $d + $e!==0){
					$res = ($a + $d) % 10;
				}else{
					$res = '无牛';
				}
				//牛牛点数
				if (count($value ['niuniu']['points'])>0) {
					$value ['niuniu']['points']=array_flip($value ['niuniu']['points']);
					switch ($res) {
						case '无牛':
							if (isset($value ['niuniu']['points']['not'])) {
								$value ['win'] ['牛牛点数'] = '无牛';
								$iswin ++;
								$value ['gold'] += ($odds ['牛牛点数']['无牛'] * $value ['win'] ['单注金额']);
							}
							break;
						case 0:
							if (isset($value ['niuniu']['points']['nn'])) {
								$value ['win'] ['牛牛点数'] = '牛牛';
								$iswin ++;
								$value ['gold'] += ($odds ['牛牛点数']['牛牛'] * $value ['win'] ['单注金额']);
							}
							break;
						case 1:
							if (isset($value ['niuniu']['points'][1])) {
								$value ['win'] ['牛牛点数'] = '牛1';
								$iswin ++;
								$value ['gold'] += ($odds ['牛牛点数'][0] * $value ['win'] ['单注金额']);
							}
							break;
						case 2:
							if (isset($value ['niuniu']['points'][2])) {
								$value ['win'] ['牛牛点数'] = '牛2';
								$iswin ++;
								$value ['gold'] += ($odds ['牛牛点数'][0] * $value ['win'] ['单注金额']);
							}
							break;
						case 3:
							if (isset($value ['niuniu']['points'][3])) {
								$value ['win'] ['牛牛点数'] = '牛3';
								$iswin ++;
								$value ['gold'] += ($odds ['牛牛点数'][0] * $value ['win'] ['单注金额']);
							}
							break;
						case 4:
							if (isset($value ['niuniu']['points'][4])) {
								$value ['win'] ['牛牛点数'] = '牛4';
								$iswin ++;
								$value ['gold'] += ($odds ['牛牛点数'][0] * $value ['win'] ['单注金额']);
							}
							break;
						case 5:
							if (isset($value ['niuniu']['points'][5])) {
								$value ['win'] ['牛牛点数'] = '牛5';
								$iswin ++;
								$value ['gold'] += ($odds ['牛牛点数'][0] * $value ['win'] ['单注金额']);
							}
							break;
						case 6:
							if (isset($value ['niuniu']['points'][6])) {
								$value ['win'] ['牛牛点数'] = '牛6';
								$iswin ++;
								$value ['gold'] += ($odds ['牛牛点数'][0] * $value ['win'] ['单注金额']);
							}
							break;
						case 7:
							if (isset($value ['niuniu']['points'][7])) {
								$value ['win'] ['牛牛点数'] = '牛7';
								$iswin ++;
								$value ['gold'] += ($odds ['牛牛点数'][0] * $value ['win'] ['单注金额']);
							}
							break;
						case 8:
							if (isset($value ['niuniu']['points'][8])) {
								$value ['win'] ['牛牛点数'] = '牛8';
								$iswin ++;
								$value ['gold'] += ($odds ['牛牛点数'][0] * $value ['win'] ['单注金额']);
							}
							break;
						case 9:
							if (isset($value ['niuniu']['points'][9])) {
								$value ['win'] ['牛牛点数'] = '牛9';
								$iswin ++;
								$value ['gold'] += ($odds ['牛牛点数'][0] * $value ['win'] ['单注金额']);
							}
							break;
					}
				}
				//牛牛双面
				if (count($value ['niuniu']['lm'])>0) {
					$zhi=array(1,2,3,5,7);//质量
					$he=array(0,4,6,8,9);//合
					$zhi = array_flip($zhi);
					$he = array_flip($he);
					$value ['niuniu']['lm']=array_flip($value ['niuniu']['lm']);
					//大小
					if ($res > 0 && $res < 6 && isset($value ['niuniu']['lm']['small'])) {
						$value ['win'] ['牛牛双面'] ['大小'] = '小';
						$iswin ++;
						$value ['gold'] += ($odds ['牛牛双面'] ['小'] * $value ['win'] ['单注金额']);
					}else if ($res ===0 || $res >= 6 && isset($value ['niuniu']['lm']['big'])) {
						$value ['win'] ['牛牛双面'] ['大小'] = '大';
						$iswin ++;
						$value ['gold'] += ($odds ['牛牛双面'] ['大'] * $value ['win'] ['单注金额']);
					}
					//单双
					if ($res %2 === 0 && !is_string($res) && isset($value ['niuniu']['lm']['Both'])) {
						$value ['win'] ['牛牛双面'] ['单双'] = '双';
						$iswin ++;
						$value ['gold'] += ($odds ['牛牛双面'] ['双'] * $value ['win'] ['单注金额']);
					}else if ($res %2 !== 0 && isset($value ['niuniu']['lm']['One'])) {
						$value ['win'] ['牛牛双面'] ['单双'] = '单';
						$iswin ++;
						$value ['gold'] += ($odds ['牛牛双面'] ['单'] * $value ['win'] ['单注金额']);
					}
					//质合
					if (isset($zhi[$res]) && isset($value ['niuniu']['lm']['Zhi'])) {
						$value ['win'] ['牛牛双面'] ['质合'] = '质';
						$iswin ++;
						$value ['gold'] += ($odds ['牛牛双面'] ['质'] * $value ['win'] ['单注金额']);
					}else if (isset($he[$res]) && isset($value ['niuniu']['lm']['He'])) {
						$value ['win'] ['牛牛双面'] ['质合'] = '合';
						$iswin ++;
						$value ['gold'] += ($odds ['牛牛双面'] ['合'] * $value ['win'] ['单注金额']);
					}
				}
				//牛牛
				if (count($value ['niuniu']['stud'])>0) {
					$stud=array_count_values($result);//统计数组中所有值出现的次数。
					$value ['niuniu']['stud']=array_flip($value ['niuniu']['stud']);
					switch (max($stud)) {
						case 5://最大重复值
							if (isset($value ['niuniu']['stud']['wut'])) {
								$value ['win'] ['牛牛梭哈'] = '五条';
								$iswin ++;
								$value ['gold'] += ($odds ['牛牛梭哈'] ['五条'] * $value ['win'] ['单注金额']);
							}
							break;
						case 4://最大重复值
							if (isset($value ['niuniu']['stud']['zhad'])) {
								$value ['win'] ['牛牛梭哈'] = '炸弹';
								$iswin ++;
								$value ['gold'] += ($odds ['牛牛梭哈'] ['炸弹'] * $value ['win'] ['单注金额']);
							}
							break;
						case 3://最大重复值
							if (min($stud) ===2 && isset($value ['niuniu']['stud']['hul'])) {
								$value ['win'] ['牛牛梭哈'] = '葫芦';
								$iswin ++;
								$value ['gold'] += ($odds ['牛牛梭哈'] ['葫芦'] * $value ['win'] ['单注金额']);
							}elseif (count($stud) ===3 &&isset($value ['niuniu']['stud']['sant'])){
								$value ['win'] ['牛牛梭哈'] = '三条';
								$iswin ++;
								$value ['gold'] += ($odds ['牛牛梭哈'] ['三条'] * $value ['win'] ['单注金额']);
							}
							break;
						case 2://最大重复值
							if (count($stud) ===3 && isset($value ['niuniu']['stud']['liangd'])) {
								$value ['win'] ['牛牛梭哈'] = '两对';
								$iswin ++;
								$value ['gold'] += ($odds ['牛牛梭哈'] ['两对'] * $value ['win'] ['单注金额']);
							}elseif (count($stud) ===4 && isset($value ['niuniu']['stud']['dand'])){
								$value ['win'] ['牛牛梭哈'] = '单对';
								$iswin ++;
								$value ['gold'] += ($odds ['牛牛梭哈'] ['单对'] * $value ['win'] ['单注金额']);
							}
							break;
						case 1://最大重复值
							$sum=[10,15,20,25,30,35];//五个数相加的和
							$sum=array_flip($sum);
							if (isset($sum[array_sum($result)]) && isset($value ['niuniu']['stud']['shunz'])) {
								$value ['win'] ['牛牛梭哈'] = '顺子';
								$iswin ++;
								$value ['gold'] += ($odds ['牛牛梭哈'] ['顺子'] * $value ['win'] ['单注金额']);
							}elseif (count($stud) ===5 && isset($value ['niuniu']['stud']['sanh'])){
								$value ['win'] ['牛牛梭哈'] = '散号';
								$iswin ++;
								$value ['gold'] += ($odds ['牛牛梭哈'] ['散号'] * $value ['win'] ['单注金额']);
							}
							break;
					}
						
				}
			}
			$value ['win'] ['开奖号码'] =$value ['opencode'];
			$value ['win'] ['下注'] = '下了' . $value['bet'] . '注';
			$value ['win'] ['中奖'] = '中了' . $iswin . '注';
			$value ['win'] ['获利'] = $value ['gold'] ;
			$msg [$value ['userinfo']['nickname']] ['win'] = $value ['win'];
			$user = User_user::get ( $value ['userinfo'] ['user_id'] );
			$cqres = array ( // 要插入数据库的数据
					'user_id' => $user ['id'],
					'iswin' => 0,
					'detail' => json_encode ( $value ),
					'expect' => $value ['expect'],
					'time' => time()
			);
			if ($iswin > 0) {
				$cqres ['iswin'] = 1;
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
						'operation' => '重庆时时彩中奖', // 操作说明
						'detail' => json_encode ( $gdetail ), // 账单详情
						'time' => $_SERVER['REQUEST_TIME']
				);
				$this->startTrans (); // 开启事务
				try {
					$this->create ( $cqres );
					if (User_user::where ( 'id', $cqres ['user_id'] )->update ( $data )) {
						if (Gold_history::create ( $gold )) {
							$this->commit (); // 成功，提交事务
							$msg[$user['nickname']] ='恭喜'.$user['nickname'].'中奖了'.'共获利：'.$value ['win'] ['获利'];
							$msg[$user['username']]['win']=$value ['win'];
						} else {
							$this->rollback ();
							$msg[$user['nickname']] = '插入账单失败';
						}
					} else { // 失败，回滚事务
						$this->rollback ();
						$msg[$user['nickname']] = $user['nickname'].'没有中奖→金钱没有变化';
						$msg[$user['username']]['win']=$value ['win'];
					}
				} catch ( Exception $ex ) {
					$this->rollback ();
					$msg[$user['nickname']] = '异常';
				}
			}else {
			$msg[$user['nickname']] = $user['nickname'].'没有中奖';
			$msg[$user['username']]['win']=$value ['win'];
			$this->startTrans (); // 开启事务
			try {
				$this->create ( $cqres );
				$this->commit (); // 成功，提交事务
			} catch ( Exception $ex ) {
				$this->rollback ();
				$msg[$user['nickname']] = '异常';
			}
			}
		}
		return $msg;
	}
}