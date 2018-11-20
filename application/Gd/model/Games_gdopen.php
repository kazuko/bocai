<?php

namespace app\gd\model;

use think\Model;

/**
 */
class Games_gdopen extends Model {
	// protected $table = "bc_games_gdopen";
	// 遗漏
	public function computeOmit($games_gdopen, $lastRecord) {
		$opencode = explode ( ",", $games_gdopen ['opencode'] );
		$opencodeArray = [ 
				1 => $opencode [0],
				2 => $opencode [1],
				3 => $opencode [2],
				4 => $opencode [3],
				5 => $opencode [4] 
		];
		if ($lastRecord) {
			$code_trend = [ 
					1 => json_decode ( $lastRecord ['one'], true ),
					2 => json_decode ( $lastRecord ['two'], true ),
					3 => json_decode ( $lastRecord ['three'], true ),
					4 => json_decode ( $lastRecord ['four'], true ),
					5 => json_decode ( $lastRecord ['five'], true ) 
			];
			foreach ( $opencodeArray as $sub => $opencode ) {
				switch ($sub) {
					case 1 :
						$index = 'num' . ( int ) $opencode;
						foreach ( $code_trend [$sub] as $codeSub => $code ) {
							if ($index == $codeSub) {
								$code_trend [$sub] [$codeSub] = 0;
							} 							// 新和值与下标不同并且上一次出现的和值不是下标
							else if ($index != $codeSub && $code_trend [$sub] [$codeSub] != 0) {
								$code_trend [$sub] [$codeSub] ++;
							} 							// 新和值与下标不同并且上一次出现的和值是下标
							else {
								$code_trend [$sub] [$codeSub] = 1;
							}
						}
						break;
					case 2 :
						$index = 'num' . ( int ) $opencode;
						foreach ( $code_trend [$sub] as $codeSub => $code ) {
							if ($index == $codeSub) {
								$code_trend [$sub] [$codeSub] = 0;
							} 							// 新和值与下标不同并且上一次出现的和值不是下标
							else if ($index != $codeSub && $code_trend [$sub] [$codeSub] != 0) {
								$code_trend [$sub] [$codeSub] ++;
							} 							// 新和值与下标不同并且上一次出现的和值是下标
							else {
								$code_trend [$sub] [$codeSub] = 1;
							}
						}
						break;
					case 3 :
						$index = 'num' . ( int ) $opencode;
						foreach ( $code_trend [$sub] as $codeSub => $code ) {
							if ($index == $codeSub) {
								$code_trend [$sub] [$codeSub] = 0;
							} 							// 新和值与下标不同并且上一次出现的和值不是下标
							else if ($index != $codeSub && $code_trend [$sub] [$codeSub] != 0) {
								$code_trend [$sub] [$codeSub] ++;
							} 							// 新和值与下标不同并且上一次出现的和值是下标
							else {
								$code_trend [$sub] [$codeSub] = 1;
							}
						}
						break;
					case 4 :
						$index = 'num' . ( int ) $opencode;
						foreach ( $code_trend [$sub] as $codeSub => $code ) {
							if ($index == $codeSub) {
								$code_trend [$sub] [$codeSub] = 0;
							} 							// 新和值与下标不同并且上一次出现的和值不是下标
							else if ($index != $codeSub && $code_trend [$sub] [$codeSub] != 0) {
								$code_trend [$sub] [$codeSub] ++;
							} 							// 新和值与下标不同并且上一次出现的和值是下标
							else {
								$code_trend [$sub] [$codeSub] = 1;
							}
						}
						break;
					case 5 :
						$index = 'num' . ( int ) $opencode;
						foreach ( $code_trend [$sub] as $codeSub => $code ) {
							if ($index == $codeSub) {
								$code_trend [$sub] [$codeSub] = 0;
							} 							// 新和值与下标不同并且上一次出现的和值不是下标
							else if ($index != $codeSub && $code_trend [$sub] [$codeSub] != 0) {
								$code_trend [$sub] [$codeSub] ++;
							} 							// 新和值与下标不同并且上一次出现的和值是下标
							else {
								$code_trend [$sub] [$codeSub] = 1;
							}
						}
						break;
				}
			}
		} else {
					$oneOmit=array(
							'num1'=>1,
							'num2'=>1,
							'num3'=>1,
							'num4'=>1,
							'num5'=>1,
							'num6'=>1,
							'num7'=>1,
							'num8'=>1,
							'num9'=>1,
							'num10'=>1,
							'num11'=>1
					);
					$twoOmit=array(
							'num1'=>1,
							'num2'=>1,
							'num3'=>1,
							'num4'=>1,
							'num5'=>1,
							'num6'=>1,
							'num7'=>1,
							'num8'=>1,
							'num9'=>1,
							'num10'=>1,
							'num11'=>1
					);
					$threeOmit=array(
							'num1'=>1,
							'num2'=>1,
							'num3'=>1,
							'num4'=>1,
							'num5'=>1,
							'num6'=>1,
							'num7'=>1,
							'num8'=>1,
							'num9'=>1,
							'num10'=>1,
							'num11'=>1
					);
					$fourOmit=array(
							'num1'=>1,
							'num2'=>1,
							'num3'=>1,
							'num4'=>1,
							'num5'=>1,
							'num6'=>1,
							'num7'=>1,
							'num8'=>1,
							'num9'=>1,
							'num10'=>1,
							'num11'=>1
					);
					$fiveOmit=array(
							'num1'=>1,
							'num2'=>1,
							'num3'=>1,
							'num4'=>1,
							'num5'=>1,
							'num6'=>1,
							'num7'=>1,
							'num8'=>1,
							'num9'=>1,
							'num10'=>1,
							'num11'=>1
					);
					$code_trend=[
							1 =>$oneOmit,
							2 =>$twoOmit,
							3 =>$threeOmit,
							4 =>$fourOmit,
							5 =>$fiveOmit,
					];
			foreach ( $opencodeArray as $sub => $opencode ) {
				$index = 'num'.( int ) $opencode;
				switch ($sub) {
					case 1:
						foreach ( $code_trend [$sub] as $codeSub => $code ) {
							if ($index == $codeSub) {
							$code_trend [$sub] [$codeSub] = 0;
							}
						}
					break;
					case 2:
						foreach ( $code_trend [$sub] as $codeSub => $code ) {
							if ($index == $codeSub) {
							$code_trend [$sub] [$codeSub] = 0;
							}
						}
					break;
					case 3:
						foreach ( $code_trend [$sub] as $codeSub => $code ) {
							if ($index == $codeSub) {
							$code_trend [$sub] [$codeSub] = 0;
							}
						}
					break;
					case 4:
						foreach ( $code_trend [$sub] as $codeSub => $code ) {
							if ($index == $codeSub) {
							$code_trend [$sub] [$codeSub] = 0;
							}
						}
					break;
					case 5:
						foreach ( $code_trend [$sub] as $codeSub => $code ) {
							if ($index == $codeSub) {
							$code_trend [$sub] [$codeSub] = 0;
							}
						}
					break;
				}
			}
		}
		$data = array (
				'info' => $games_gdopen ['info'],
				'expect' => $games_gdopen ['expect'],
				'opencode' => $games_gdopen ['opencode'],
				'opentime' => $games_gdopen ['opentime'],
				'one' => json_encode ( $code_trend [1] ),
				'two' => json_encode ( $code_trend [2] ),
				'three' => json_encode ( $code_trend [3] ),
				'four' => json_encode ( $code_trend [4] ),
				'five' => json_encode ( $code_trend [5] ) 
		);
		$this->create ( $data );
		return $data;
	}
}