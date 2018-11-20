<?php

namespace app\ssc\controller;

use app\mycommon\controller\Gameapi;
use think\Controller;
use app\ssc\model\Games_cqopen;
use app\ssc\model\Games_cqodds;
use app\ssc\model\Games_cqres;
use app\ssc\model\Games_cqbet;
use app\gd\model\Games_lower_limit;
use app\forum\model\User_user;

/*
 * 游戏（重庆时时彩）控制层
 * @author Motley
 * @getCqssc ---获取开奖结果接口
 * @cq_res ------开奖时间到了之后结算（用户下注后的开奖）结果
 * @cq_bet ------用户下注（计算注数并且扣除下注金额）
 * @getLower_limit 获取下注规则
 * @
 */
class Cqssc extends Controller {
	
	protected $games_cqbet;
	protected $games_cqodds;
	protected $games_cqres;
	protected $games_cqopen;
	protected $cqopen;
	
	protected function initialize()
	{
		parent::initialize();
		$this->games_cqbet = new Games_cqbet ();
		$this->games_cqodds = new Games_cqodds ();
		$this->games_cqres = new Games_cqres ();
		$this->games_cqopen = new Games_cqopen ();
		$this->cqopen=$this->getCqssc();
	}
	/**
	 * 获取数据
	 */
	public function getCqssc() {
		$gameapi = new Gameapi ();
		$cqopen = $gameapi->getGameData ( 'cqssc', 1 );
		if ($cqopen ['data'] === null) {
			$games_cqopen = array (
					'info' => $cqopen ['json'],
					'expect' => null,
					'opencode' => null,
					'opentime' => null 
			);
			
			return $games_cqopen;
		} else {
			$games_cqopen = array (
					'info' => $cqopen ['json'],
					'expect' => $cqopen ['data'] ['data'] [0] ['qishu'],
					'opencode' => $cqopen ['data'] ['data'] [0] ['result'],
					'opentime' => $cqopen ['data'] ['data'] [0] ['open_time'] 
			);
			if (!$this->games_cqopen->where ( 'expect', $games_cqopen ['expect'] )->find ()) {
				$this->games_cqopen->create ( $games_cqopen );
				// return json(array('code'=>200,
				// 'msg'=>$games_gdopen['expect'].'期已开奖！'
				// ));
			}
			return $games_cqopen;
		}
	}
	/**
	 * 开奖历史
	 */
	public function cq_history() {
		$param = $this->request->param ();
		if ($param) {
			$games_cqopen = Games_cqopen::where ( 'opentime', 'like', $param ['opentime'] . '%' )->order ( 'opentime', 'desc' )->limit ( 10 )->select ();
		} else {
			$games_cqopen = Games_cqopen::order ( 'opentime', 'desc' )->limit ( 10 )->select ();
		}
		if ($this->request->isPost ()) {
			$param = $this->request->param ();
			if ($param) {
				dump ( $param );
				$this->assign ( [ 
						'games_cqopen' => $games_cqopen 
				] );
				return $this->fetch ( 'cq_history' );
			} else {
				return json ( array (
						'code' => 0,
						'msg' => '参数获取失败' 
				) );
			}
		}
		$this->assign ( [ 
				'games_cqopen' => $games_cqopen 
		] );
		return $this->fetch ( 'cq_history' );
	}
	/**
	 * 开奖走势
	 */
	public function cq_trend() {
		$games_cqopen = Games_cqopen::order ( 'opentime', 'desc' )->limit ( 16 )->select ();
		$param = $this->request->param ();
		foreach ( $games_cqopen as $key => $value ) {
			$games_cqopen [$key] ['opencode'] = explode ( ",", $value ['opencode'] );
			if (! $param) {
				$param ['number'] = 1;
			}
			switch ($param ['number']) {
				case 1 :
					$games_cqopen [$key] ['opencode'] = ( int ) $games_cqopen [$key] ['opencode'] [0];
					break;
				case 2 :
					$games_cqopen [$key] ['opencode'] = ( int ) $games_cqopen [$key] ['opencode'] [1];
					break;
				case 3 :
					$games_cqopen [$key] ['opencode'] = ( int ) $games_cqopen [$key] ['opencode'] [2];
					break;
				case 4 :
					$games_cqopen [$key] ['opencode'] = ( int ) $games_cqopen [$key] ['opencode'] [3];
					break;
				case 5 :
					$games_cqopen [$key] ['opencode'] = ( int ) $games_cqopen [$key] ['opencode'] [4];
					break;
			}
		}
		$this->assign ( [ 
				'games_cqopen' => $games_cqopen 
		] );
		return $this->fetch ( 'cq_trend' );
	}
	/**
	 * 获取下注规则，（最低下注积分或金币）
	 */
	public function getLower_limit() {
		return Games_lower_limit::where ( 'type', 8 )->cache ( 'cq_limit' )->find ();
	}
	/**
	 * 下注
	 */
	public function cq_bet() {
		$games_cqopen = self::getCqssc (); // 开奖结果
		$lower_limit = self::getLower_limit (); // 获取下注规则
		dump ( $games_cqopen );
		$param = $this->request->param ();
		if ($this->request->isPost ()) {
			$param = array (
					'userinfo' => array (
							'user_id' => 2,
							'nickname' => "Moltey" 
					),
					'lmp' => array (
							'alls' => [ 
									'totalBig',
									'totalSmall',
									'totalOne',
									'totalBoth',
									'dragon',
									'tiger',
									'and' 
							],
							'firstball' => [ 
									'big',
									'small',
									'One',
									'Both',
									"Zhi",
									"He" 
							],
							'secondball' => [ 
									'big',
									'small',
									'One',
									'Both',
									"Zhi",
									"He" 
							],
							'thirdball' => [ 
									'big',
									'small',
									'One',
									'Both',
									"Zhi",
									"He" 
							],
							'thouthball' => [ 
									'big',
									'small',
									'One',
									'Both',
									"Zhi",
									"He" 
							],
							'fifthball' => [ 
									'big',
									'small',
									'One',
									'Both',
									"Zhi",
									"He" 
							],
							'firstthree' => [ 
									'bz',
									'sz',
									'dz',
									'bs',
									"zl" 
							],
							'middlethree' => [ 
									'bz',
									'sz',
									'dz',
									'bs',
									"zl" 
							],
							'lastthree' => [ 
									'bz',
									'sz',
									'dz',
									'bs',
									"zl" 
							] 
					),
					'OneTofive' => array (
							'firstball' => [ 
									'big',
									'small',
									'One',
									'Both',
									"Zhi",
									"He",
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'secondball' => [ 
									'big',
									'small',
									'One',
									'Both',
									"Zhi",
									"He",
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'thirdball' => [ 
									'big',
									'small',
									'One',
									'Both',
									"Zhi",
									"He",
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'thouthball' => [ 
									'big',
									'small',
									'One',
									'Both',
									"Zhi",
									"He",
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'fifthball' => [ 
									'big',
									'small',
									'One',
									'Both',
									"Zhi",
									"He",
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							] 
					),
					'OneWord' => array (
							'firstThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'middleThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'lastThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'allFive' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							] 
					),
					'TwoWord' => array (
							'firstThree' => [0,1,2,3,4,5,6,7,8,9,11,12,13,14,15,16,17,18,19,
		22,23,24,25,26,27,28,29,33,34,35,36,37,38,39,44,45,46,47,48,
		49,55,56,57,58,59,66,67,68,69,77,78,79,88,89,99],
							'middleThree' => [0,1,2,3,4,5,6,7,8,9,11,12,13,14,15,16,17,18,19,
		22,23,24,25,26,27,28,29,33,34,35,36,37,38,39,44,45,46,47,48,
		49,55,56,57,58,59,66,67,68,69,77,78,79,88,89,99],
							'lastThree' => [0,1,2,3,4,5,6,7,8,9,11,12,13,14,15,16,17,18,19,
		22,23,24,25,26,27,28,29,33,34,35,36,37,38,39,44,45,46,47,48,
		49,55,56,57,58,59,66,67,68,69,77,78,79,88,89,99] 
					),
					'ThreeWord' => array (
							'firstThree' => [0,1,2,3,4,5,6,7,8,9,11,12,13,14,15,16,17,18,19,
		22,23,24,25,26,27,28,29,33,34,35,36,37,38,39,44,45,46,47,48,
		49,55,56,57,58,59,66,67,68,69,77,78,79,88,89,99,111,112,113,
		114,115,116,117,118,119,122,123,124,125,126,127,128,129,133,
		134,135,136,137,138,139,144,145,146,147,148,149,155,156,157,
		158,189,166,167,168,169,177,178,179,188,189,199,222,223,224,
		225,226,227,228,229,233,234,235,236,237,238,239,244,245,246,
		247,248,249,255,256,257,258,259,266,267,268,269,277,278,279,
		288,289,299,333,334,335,336,337,338,339,344,345,346,347,348,
		349,355,356,357,358,359,366,367,368,369,377,378,379,388,389,
		399,444,445,446,447,448,449,455,456,457,458,459,466,467,468,
		469,477,478,479,488,489,499,555,556,557,558,559,566,567,568,
		569,577,578,579,588,589,599,666,667,668,669,677,678,679,688,
		689,699,777,778,779,788,789,799,888,889,899,999],
							'middleThree' => [0,1,2,3,4,5,6,7,8,9,11,12,13,14,15,16,17,18,19,
		22,23,24,25,26,27,28,29,33,34,35,36,37,38,39,44,45,46,47,48,
		49,55,56,57,58,59,66,67,68,69,77,78,79,88,89,99,111,112,113,
		114,115,116,117,118,119,122,123,124,125,126,127,128,129,133,
		134,135,136,137,138,139,144,145,146,147,148,149,155,156,157,
		158,189,166,167,168,169,177,178,179,188,189,199,222,223,224,
		225,226,227,228,229,233,234,235,236,237,238,239,244,245,246,
		247,248,249,255,256,257,258,259,266,267,268,269,277,278,279,
		288,289,299,333,334,335,336,337,338,339,344,345,346,347,348,
		349,355,356,357,358,359,366,367,368,369,377,378,379,388,389,
		399,444,445,446,447,448,449,455,456,457,458,459,466,467,468,
		469,477,478,479,488,489,499,555,556,557,558,559,566,567,568,
		569,577,578,579,588,589,599,666,667,668,669,677,678,679,688,
		689,699,777,778,779,788,789,799,888,889,899,999],
							'lastThree' => [0,1,2,3,4,5,6,7,8,9,11,12,13,14,15,16,17,18,19,
		22,23,24,25,26,27,28,29,33,34,35,36,37,38,39,44,45,46,47,48,
		49,55,56,57,58,59,66,67,68,69,77,78,79,88,89,99,111,112,113,
		114,115,116,117,118,119,122,123,124,125,126,127,128,129,133,
		134,135,136,137,138,139,144,145,146,147,148,149,155,156,157,
		158,189,166,167,168,169,177,178,179,188,189,199,222,223,224,
		225,226,227,228,229,233,234,235,236,237,238,239,244,245,246,
		247,248,249,255,256,257,258,259,266,267,268,269,277,278,279,
		288,289,299,333,334,335,336,337,338,339,344,345,346,347,348,
		349,355,356,357,358,359,366,367,368,369,377,378,379,388,389,
		399,444,445,446,447,448,449,455,456,457,458,459,466,467,468,
		469,477,478,479,488,489,499,555,556,557,558,559,566,567,568,
		569,577,578,579,588,589,599,666,667,668,669,677,678,679,688,
		689,699,777,778,779,788,789,799,888,889,899,999] 
					),
					'Twolocation' => array (
							'OOXXX' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'OXOXX' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'OXXOX' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'OXXXO' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'XOOXX' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'XOXOX' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'XOXXO' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'XXOOX' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'XXOXO' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'XXXOO' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							] 
					),
					'Threelocation' => array (
							'firstThree' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'thirdball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'middleThree' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'thirdball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'lastThree' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'thirdball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							] 
					),
					'groupThree' => array (
							'firstThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'middleThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'lastThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							] 
					),
					'groupSix' => array (
							'firstThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'middleThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'lastThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							] 
					),
					'span' => array (
							'firstThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'middleThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'lastThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							] 
					),
					'and' => array (
							'OOXXX' => [ 
									'One',
									'Both' 
							],
							'OXOXX' => [ 
									'One',
									'Both' 
							],
							'OXXOX' => [ 
									'One',
									'Both' 
							],
							'OXXXO' => [ 
									'One',
									'Both' 
							],
							'XOOXX' => [ 
									'One',
									'Both' 
							],
							'XOXOX' => [ 
									'One',
									'Both' 
							],
							'XOXXO' => [ 
									'One',
									'Both' 
							],
							'XXOOX' => [ 
									'One',
									'Both' 
							],
							'XXOXO' => [ 
									'One',
									'Both' 
							],
							'XXXOO' => [ 
									'One',
									'Both' 
							] 
					),
					'niuniu' => array (
							'lm' => [ 
									'big',
									'small',
									'One',
									'Both',
									"Zhi",
									"He" 
							],
							'points' => [ 
									'not',
									'nn',
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'stud' => [ 
									'wut',
									'zhad',
									'hul',
									'shunz',
									"sant",
									"liangd",
									"dand",
									"sanh" 
							] 
					),
					'opencode' => $games_cqopen ['opencode'], // 开奖号码
					'expect' => $games_cqopen ['expect'] + 1, // 开奖期数
					'gold' => 100, // 下注金币
					'time' => $_SERVER ['REQUEST_TIME'] 
			);
			if ($_SERVER ['REQUEST_TIME'] < strtotime ( $games_cqopen ['opentime'] ) + 480) {
				$user = User_user::get ( $param ['userinfo'] ['user_id'] );
				$param = $this->games_cqbet->betCount ( $param ); // 下注数量计算
				if ($user ['gold'] > $param ['goldCount']) {
					$param = $this->games_cqbet->bet ( $param, $user ); // 下注处理扣去下注金币
					dump ( $param ); // 下注完成返回
				} else {
					return json ( array (
							'code' => 0,
							'msg' => '金币不足！所需要的金币是：' . $param ['goldCount'] . '共下了' . $param ['bet'] 
					) );
				}
			} else {
				return json ( array (
						'code' => 0,
						'msg' => '抱歉，现在已停止下注！' 
				) );
			}
		} // param
		if ((date ( 'H', strtotime ( $games_cqopen ['opentime'] ) )) < (date ( 'H', $_SERVER ['REQUEST_TIME'] ))) {
			$games_cqopen ['closetime'] = '未开始';
			$games_cqopen ['opentime'] = '未开始';
		} else if ((date ( 'Ymd', strtotime ( $games_cqopen ['opentime'] ) ) . '108') <= $games_cqopen ['expect']) {
			if (strtotime ( $games_cqopen ['opentime'] ) + 300 >= $_SERVER ['REQUEST_TIME']) { // 判断开奖时间十分钟一次。也就是600秒，如果大于现在的时间
				if (strtotime ( $games_cqopen ['opentime'] ) + 180 >= $_SERVER ['REQUEST_TIME']) { //
					$games_cqopen ['closetime'] = '晚上：' . friendlyDate ( strtotime ( $games_cqopen ['opentime'] ) + 120, 'games' );
				} else {
					$games_cqopen ['closetime'] = '晚上：' . '停止下注';
				}
				$games_cqopen ['opentime'] = '晚上：' . friendlyDate ( strtotime ( $games_cqopen ['opentime'] ) + 240, 'games' );
			} else {
				$games_cqopen ['closetime'] = '晚上：' . friendlyDate ( strtotime ( $games_cqopen ['opentime'] ) + 420, 'games' );
				$games_cqopen ['opentime'] = '晚上：' . friendlyDate ( strtotime ( $games_cqopen ['opentime'] ) + 540, 'games' );
			}
		} else if ((date ( 'Ymd', strtotime ( $games_cqopen ['opentime'] ) ) . '108') >= $games_cqopen ['expect'] && (date ( 'H', strtotime ( $games_cqopen ['opentime'] ) )) === (date ( 'H', $_SERVER ['REQUEST_TIME'] ))) {
			if (strtotime ( $games_cqopen ['opentime'] ) + 600 >= $_SERVER ['REQUEST_TIME']) { // 判断开奖时间十分钟一次。也就是600秒，如果大于现在的时间
				if (strtotime ( $games_cqopen ['opentime'] ) + 480 >= $_SERVER ['REQUEST_TIME']) { //
					$games_cqopen ['closetime'] = '白天：' . friendlyDate ( strtotime ( $games_cqopen ['opentime'] ) + 420, 'games' );
				} else {
					$games_cqopen ['closetime'] = '白天：' . '停止下注';
				}
				$games_cqopen ['opentime'] = '白天：' . friendlyDate ( strtotime ( $games_cqopen ['opentime'] ) + 540, 'games' );
			} else {
				$games_cqopen ['closetime'] = '白天：' . friendlyDate ( strtotime ( $games_cqopen ['opentime'] ) + 1020, 'games' );
				$games_cqopen ['opentime'] = '白天：' . friendlyDate ( strtotime ( $games_cqopen ['opentime'] ) + 1140, 'games' );
			}
		}
		$this->assign ( [ 
				'games_cqopen' => $games_cqopen,
				'lower_limit' => $lower_limit 
		] );
		return $this->fetch ( 'cq_bet' );
	}
	/**
	 * 开奖计算结果
	 * 
	 * @return \think\response\Json|mixed
	 */
	public function cq_res() {
		$games_cqopen = self::getCqssc (); // 开奖结果
		$lower_limit = self::getLower_limit (); // 获取下注规则
		$odds = $this->games_cqodds->getOdds (); // 获取赔率
		dump ( $games_cqopen );
		if ($this->request->isPost ()) {
			$param = $this->request->param ();
			$param=array(
					array (
							'userinfo' => array (
									'user_id' => 2,
									'nickname' => "Moltey"
							),
							'lmp' => array (
									'alls' => [
											'totalBig',
											'totalSmall',
											'totalOne',
											'totalBoth',
											'dragon',
											'tiger',
											'and'
									],
									'firstball' => [
											'big',
											'small',
											'One',
											'Both',
											"Zhi",
											"He"
									],
									'secondball' => [
											'big',
											'small',
											'One',
											'Both',
											"Zhi",
											"He"
									],
									'thirdball' => [
											'big',
											'small',
											'One',
											'Both',
											"Zhi",
											"He"
									],
									'thouthball' => [
											'big',
											'small',
											'One',
											'Both',
											"Zhi",
											"He"
									],
									'fifthball' => [
											'big',
											'small',
											'One',
											'Both',
											"Zhi",
											"He"
									],
									'firstthree' => [
											'bz',
											'sz',
											'dz',
											'bs',
											"zl"
									],
									'middlethree' => [
											'bz',
											'sz',
											'dz',
											'bs',
											"zl"
									],
									'lastthree' => [
											'bz',
											'sz',
											'dz',
											'bs',
											"zl"
									]
							),
							'OneTofive' => array (
									'firstball' => [
											'big',
											'small',
											'One',
											'Both',
											"Zhi",
											"He",
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9
									],
									'secondball' => [
											'big',
											'small',
											'One',
											'Both',
											"Zhi",
											"He",
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9
									],
									'thirdball' => [
											'big',
											'small',
											'One',
											'Both',
											"Zhi",
											"He",
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9
									],
									'thouthball' => [
											'big',
											'small',
											'One',
											'Both',
											"Zhi",
											"He",
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9
									],
									'fifthball' => [
											'big',
											'small',
											'One',
											'Both',
											"Zhi",
											"He",
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9
									]
							),
							'OneWord' => array (
									'firstThree' => [
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9
									],
									'middleThree' => [
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9
									],
									'lastThree' => [
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9
									],
									'allFive' => [
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9
									]
							),
							'TwoWord' => array (
									'firstThree' => [0,1,2,3,4,5,6,7,8,9,11,12,13,14,15,16,17,18,19,
		22,23,24,25,26,27,28,29,33,34,35,36,37,38,39,44,45,46,47,48,
		49,55,56,57,58,59,66,67,68,69,77,78,79,88,89,99],
									'middleThree' => [0,1,2,3,4,5,6,7,8,9,11,12,13,14,15,16,17,18,19,
		22,23,24,25,26,27,28,29,33,34,35,36,37,38,39,44,45,46,47,48,
		49,55,56,57,58,59,66,67,68,69,77,78,79,88,89,99],
									'lastThree' => [0,1,2,3,4,5,6,7,8,9,11,12,13,14,15,16,17,18,19,
		22,23,24,25,26,27,28,29,33,34,35,36,37,38,39,44,45,46,47,48,
		49,55,56,57,58,59,66,67,68,69,77,78,79,88,89,99]
							),
							'ThreeWord' => array (
									'firstThree' => [0,1,2,3,4,5,6,7,8,9,11,12,13,14,15,16,17,18,19,
		22,23,24,25,26,27,28,29,33,34,35,36,37,38,39,44,45,46,47,48,
		49,55,56,57,58,59,66,67,68,69,77,78,79,88,89,99,111,112,113,
		114,115,116,117,118,119,122,123,124,125,126,127,128,129,133,
		134,135,136,137,138,139,144,145,146,147,148,149,155,156,157,
		158,189,166,167,168,169,177,178,179,188,189,199,222,223,224,
		225,226,227,228,229,233,234,235,236,237,238,239,244,245,246,
		247,248,249,255,256,257,258,259,266,267,268,269,277,278,279,
		288,289,299,333,334,335,336,337,338,339,344,345,346,347,348,
		349,355,356,357,358,359,366,367,368,369,377,378,379,388,389,
		399,444,445,446,447,448,449,455,456,457,458,459,466,467,468,
		469,477,478,479,488,489,499,555,556,557,558,559,566,567,568,
		569,577,578,579,588,589,599,666,667,668,669,677,678,679,688,
		689,699,777,778,779,788,789,799,888,889,899,999],
									'middleThree' => [0,1,2,3,4,5,6,7,8,9,11,12,13,14,15,16,17,18,19,
		22,23,24,25,26,27,28,29,33,34,35,36,37,38,39,44,45,46,47,48,
		49,55,56,57,58,59,66,67,68,69,77,78,79,88,89,99,111,112,113,
		114,115,116,117,118,119,122,123,124,125,126,127,128,129,133,
		134,135,136,137,138,139,144,145,146,147,148,149,155,156,157,
		158,189,166,167,168,169,177,178,179,188,189,199,222,223,224,
		225,226,227,228,229,233,234,235,236,237,238,239,244,245,246,
		247,248,249,255,256,257,258,259,266,267,268,269,277,278,279,
		288,289,299,333,334,335,336,337,338,339,344,345,346,347,348,
		349,355,356,357,358,359,366,367,368,369,377,378,379,388,389,
		399,444,445,446,447,448,449,455,456,457,458,459,466,467,468,
		469,477,478,479,488,489,499,555,556,557,558,559,566,567,568,
		569,577,578,579,588,589,599,666,667,668,669,677,678,679,688,
		689,699,777,778,779,788,789,799,888,889,899,999],
									'lastThree' => [0,1,2,3,4,5,6,7,8,9,11,12,13,14,15,16,17,18,19,
		22,23,24,25,26,27,28,29,33,34,35,36,37,38,39,44,45,46,47,48,
		49,55,56,57,58,59,66,67,68,69,77,78,79,88,89,99,111,112,113,
		114,115,116,117,118,119,122,123,124,125,126,127,128,129,133,
		134,135,136,137,138,139,144,145,146,147,148,149,155,156,157,
		158,189,166,167,168,169,177,178,179,188,189,199,222,223,224,
		225,226,227,228,229,233,234,235,236,237,238,239,244,245,246,
		247,248,249,255,256,257,258,259,266,267,268,269,277,278,279,
		288,289,299,333,334,335,336,337,338,339,344,345,346,347,348,
		349,355,356,357,358,359,366,367,368,369,377,378,379,388,389,
		399,444,445,446,447,448,449,455,456,457,458,459,466,467,468,
		469,477,478,479,488,489,499,555,556,557,558,559,566,567,568,
		569,577,578,579,588,589,599,666,667,668,669,677,678,679,688,
		689,699,777,778,779,788,789,799,888,889,899,999]
							),
							'Twolocation' => array (
									'OOXXX' => [
											'firstball' => [
													0,
													1,
													2,
													3,
													4,
													5,
													6,
													7,
													8,
													9
											],
											'secondball' => [
													0,
													1,
													2,
													3,
													4,
													5,
													6,
													7,
													8,
													9
											]
									],
									'OXOXX' => [
											'firstball' => [
													0,
													1,
													2,
													3,
													4,
													5,
													6,
													7,
													8,
													9
											],
											'secondball' => [
													0,
													1,
													2,
													3,
													4,
													5,
													6,
													7,
													8,
													9
											]
									],
									'OXXOX' => [
											'firstball' => [
													0,
													1,
													2,
													3,
													4,
													5,
													6,
													7,
													8,
													9
											],
											'secondball' => [
													0,
													1,
													2,
													3,
													4,
													5,
													6,
													7,
													8,
													9
											]
									],
									'OXXXO' => [
											'firstball' => [
													0,
													1,
													2,
													3,
													4,
													5,
													6,
													7,
													8,
													9
											],
											'secondball' => [
													0,
													1,
													2,
													3,
													4,
													5,
													6,
													7,
													8,
													9
											]
									],
									'XOOXX' => [
											'firstball' => [
													0,
													1,
													2,
													3,
													4,
													5,
													6,
													7,
													8,
													9
											],
											'secondball' => [
													0,
													1,
													2,
													3,
													4,
													5,
													6,
													7,
													8,
													9
											]
									],
									'XOXOX' => [
											'firstball' => [
													0,
													1,
													2,
													3,
													4,
													5,
													6,
													7,
													8,
													9
											],
											'secondball' => [
													0,
													1,
													2,
													3,
													4,
													5,
													6,
													7,
													8,
													9
											]
									],
									'XOXXO' => [
											'firstball' => [
													0,
													1,
													2,
													3,
													4,
													5,
													6,
													7,
													8,
													9
											],
											'secondball' => [
													0,
													1,
													2,
													3,
													4,
													5,
													6,
													7,
													8,
													9
											]
									],
									'XXOOX' => [
											'firstball' => [
													0,
													1,
													2,
													3,
													4,
													5,
													6,
													7,
													8,
													9
											],
											'secondball' => [
													0,
													1,
													2,
													3,
													4,
													5,
													6,
													7,
													8,
													9
											]
									],
									'XXOXO' => [
											'firstball' => [
													0,
													1,
													2,
													3,
													4,
													5,
													6,
													7,
													8,
													9
											],
											'secondball' => [
													0,
													1,
													2,
													3,
													4,
													5,
													6,
													7,
													8,
													9
											]
									],
									'XXXOO' => [
											'firstball' => [
													0,
													1,
													2,
													3,
													4,
													5,
													6,
													7,
													8,
													9
											],
											'secondball' => [
													0,
													1,
													2,
													3,
													4,
													5,
													6,
													7,
													8,
													9
											]
									]
							),
							'Threelocation' => array (
									'firstThree' => [
											'firstball' => [
													0,
													1,
													2,
													3,
													4,
													5,
													6,
													7,
													8,
													9
											],
											'secondball' => [
													0,
													1,
													2,
													3,
													4,
													5,
													6,
													7,
													8,
													9
											],
											'thirdball' => [
													0,
													1,
													2,
													3,
													4,
													5,
													6,
													7,
													8,
													9
											]
									],
									'middleThree' => [
											'firstball' => [
													0,
													1,
													2,
													3,
													4,
													5,
													6,
													7,
													8,
													9
											],
											'secondball' => [
													0,
													1,
													2,
													3,
													4,
													5,
													6,
													7,
													8,
													9
											],
											'thirdball' => [
													0,
													1,
													2,
													3,
													4,
													5,
													6,
													7,
													8,
													9
											]
									],
									'lastThree' => [
											'firstball' => [
													0,
													1,
													2,
													3,
													4,
													5,
													6,
													7,
													8,
													9
											],
											'secondball' => [
													0,
													1,
													2,
													3,
													4,
													5,
													6,
													7,
													8,
													9
											],
											'thirdball' => [
													0,
													1,
													2,
													3,
													4,
													5,
													6,
													7,
													8,
													9
											]
									]
							),
							'groupThree' => array (
									'firstThree' => [
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9
									],
									'middleThree' => [
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9
									],
									'lastThree' => [
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9
									]
							),
							'groupSix' => array (
									'firstThree' => [
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9
									],
									'middleThree' => [
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9
									],
									'lastThree' => [
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9
									]
							),
							'span' => array (
									'firstThree' => [
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9
									],
									'middleThree' => [
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9
									],
									'lastThree' => [
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9
									]
							),
							'and' => array (
									'OOXXX' => [
											'One',
											'Both'
									],
									'OXOXX' => [
											'One',
											'Both'
									],
									'OXXOX' => [
											'One',
											'Both'
									],
									'OXXXO' => [
											'One',
											'Both'
									],
									'XOOXX' => [
											'One',
											'Both'
									],
									'XOXOX' => [
											'One',
											'Both'
									],
									'XOXXO' => [
											'One',
											'Both'
									],
									'XXOOX' => [
											'One',
											'Both'
									],
									'XXOXO' => [
											'One',
											'Both'
									],
									'XXXOO' => [
											'One',
											'Both'
									]
							),
							'niuniu' => array (
									'lm' => [
											'big',
											'small',
											'One',
											'Both',
											"Zhi",
											"He"
									],
									'points' => [
											'not',
											'nn',
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9
									],
									'stud' => [
											'wut',
											'zhad',
											'hul',
											'shunz',
											"sant",
											"liangd",
											"dand",
											"sanh"
									]
							),
							'opencode' => $games_cqopen ['opencode'], // 开奖号码
							'expect' => $games_cqopen ['expect'] + 1, // 开奖期数
							'gold' => 100, // 下注金币
							'time' => $_SERVER ['REQUEST_TIME']
					),array (
					'userinfo' => array (
							'user_id' => 1,
							'nickname' => "ni_ba" 
					),
					'lmp' => array (
							'alls' => [ 
									'totalBig',
									'totalSmall',
									'totalOne',
									'totalBoth',
							],
							'firstball' => [ 
									'big',
									'small',
									"He" 
							],
							'secondball' => [ 
									"Zhi",
									"He" 
							],
							'thirdball' => [ 
									'big',
									'small',
							],
							'thouthball' => [ 
									'big',
									'One',
									'Both',
							],
							'fifthball' => [ 
									'One',
									'Both',
							],
							'firstthree' => [ 
							],
							'middlethree' => [ 
									'bz',
									'bs',
									"zl" 
							],
							'lastthree' => [ 
									'bs',
									"zl" 
							] 
					),
					'OneTofive' => array (
							'firstball' => [ 
									'big',
									'small',
									'One',
									'Both',
									"Zhi",
									"He",
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'secondball' => [ 
									'big',
									'small',
									'One',
									'Both',
									"Zhi",
									"He",
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'thirdball' => [ 
									'big',
									'small',
									'One',
									'Both',
									"Zhi",
									"He",
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'thouthball' => [ 
									'big',
									'small',
									'One',
									'Both',
									"Zhi",
									"He",
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'fifthball' => [ 
									'big',
									'small',
									'One',
									'Both',
									"Zhi",
									"He",
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							] 
					),
					'OneWord' => array (
							'firstThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'middleThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'lastThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'allFive' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							] 
					),
					'TwoWord' => array (
							'firstThree' => [0,1,2,3,4,5,6,7,8,9,11,12,13,14,15,16,17,18,19,
		22,23,24,25,26,27,28,29,33,34,35,36,37,38,39,44,45,46,47,48,
		49,55,56,57,58,59,66,67,68,69,77,78,79,88,89,99],
							'middleThree' => [0,1,2,3,4,5,6,7,8,9,11,12,13,14,15,16,17,18,19,
		22,23,24,25,26,27,28,29,33,34,35,36,37,38,39,44,45,46,47,48,
		49,55,56,57,58,59,66,67,68,69,77,78,79,88,89,99],
							'lastThree' => [0,1,2,3,4,5,6,7,8,9,11,12,13,14,15,16,17,18,19,
		22,23,24,25,26,27,28,29,33,34,35,36,37,38,39,44,45,46,47,48,
		49,55,56,57,58,59,66,67,68,69,77,78,79,88,89,99] 
					),
					'ThreeWord' => array (
							'firstThree' => [0,1,2,3,4,5,6,7,8,9,11,12,13,14,15,16,17,18,19,
		22,23,24,25,26,27,28,29,33,34,35,36,37,38,39,44,45,46,47,48,
		49,55,56,57,58,59,66,67,68,69,77,78,79,88,89,99,111,112,113,
		114,115,116,117,118,119,122,123,124,125,126,127,128,129,133,
		134,135,136,137,138,139,144,145,146,147,148,149,155,156,157,
		158,189,166,167,168,169,177,178,179,188,189,199,222,223,224,
		225,226,227,228,229,233,234,235,236,237,238,239,244,245,246,
		247,248,249,255,256,257,258,259,266,267,268,269,277,278,279,
		288,289,299,333,334,335,336,337,338,339,344,345,346,347,348,
		349,355,356,357,358,359,366,367,368,369,377,378,379,388,389,
		399,444,445,446,447,448,449,455,456,457,458,459,466,467,468,
		469,477,478,479,488,489,499,555,556,557,558,559,566,567,568,
		569,577,578,579,588,589,599,666,667,668,669,677,678,679,688,
		689,699,777,778,779,788,789,799,888,889,899,999],
							'middleThree' => [0,1,2,3,4,5,6,7,8,9,11,12,13,14,15,16,17,18,19,
		22,23,24,25,26,27,28,29,33,34,35,36,37,38,39,44,45,46,47,48,
		49,55,56,57,58,59,66,67,68,69,77,78,79,88,89,99,111,112,113,
		114,115,116,117,118,119,122,123,124,125,126,127,128,129,133,
		134,135,136,137,138,139,144,145,146,147,148,149,155,156,157,
		158,189,166,167,168,169,177,178,179,188,189,199,222,223,224,
		225,226,227,228,229,233,234,235,236,237,238,239,244,245,246,
		247,248,249,255,256,257,258,259,266,267,268,269,277,278,279,
		288,289,299,333,334,335,336,337,338,339,344,345,346,347,348,
		349,355,356,357,358,359,366,367,368,369,377,378,379,388,389,
		399,444,445,446,447,448,449,455,456,457,458,459,466,467,468,
		469,477,478,479,488,489,499,555,556,557,558,559,566,567,568,
		569,577,578,579,588,589,599,666,667,668,669,677,678,679,688,
		689,699,777,778,779,788,789,799,888,889,899,999],
							'lastThree' => [0,1,2,3,4,5,6,7,8,9,11,12,13,14,15,16,17,18,19,
		22,23,24,25,26,27,28,29,33,34,35,36,37,38,39,44,45,46,47,48,
		49,55,56,57,58,59,66,67,68,69,77,78,79,88,89,99,111,112,113,
		114,115,116,117,118,119,122,123,124,125,126,127,128,129,133,
		134,135,136,137,138,139,144,145,146,147,148,149,155,156,157,
		158,189,166,167,168,169,177,178,179,188,189,199,222,223,224,
		225,226,227,228,229,233,234,235,236,237,238,239,244,245,246,
		247,248,249,255,256,257,258,259,266,267,268,269,277,278,279,
		288,289,299,333,334,335,336,337,338,339,344,345,346,347,348,
		349,355,356,357,358,359,366,367,368,369,377,378,379,388,389,
		399,444,445,446,447,448,449,455,456,457,458,459,466,467,468,
		469,477,478,479,488,489,499,555,556,557,558,559,566,567,568,
		569,577,578,579,588,589,599,666,667,668,669,677,678,679,688,
		689,699,777,778,779,788,789,799,888,889,899,999] 
					),
					'Twolocation' => array (
							'OOXXX' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'OXOXX' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'OXXOX' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'OXXXO' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'XOOXX' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'XOXOX' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'XOXXO' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'XXOOX' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'XXOXO' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'XXXOO' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							] 
					),
					'Threelocation' => array (
							'firstThree' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'thirdball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'middleThree' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'thirdball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'lastThree' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'thirdball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							] 
					),
					'groupThree' => array (
							'firstThree' => [ 
									0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9
							],
							'middleThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'lastThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							] 
					),
					'groupSix' => array (
							'firstThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'middleThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'lastThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							] 
					),
					'span' => array (
							'firstThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'middleThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'lastThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							] 
					),
					'and' => array (
							'OOXXX' => [ 
									'One',
									'Both' 
							],
							'OXOXX' => [ 
									'One',
									'Both' 
							],
							'OXXOX' => [ 
									'One',
									'Both' 
							],
							'OXXXO' => [ 
									'One',
									'Both' 
							],
							'XOOXX' => [ 
									'One',
									'Both' 
							],
							'XOXOX' => [ 
									'One',
									'Both' 
							],
							'XOXXO' => [ 
									'One',
									'Both' 
							],
							'XXOOX' => [ 
									'One',
									'Both' 
							],
							'XXOXO' => [ 
									'One',
									'Both' 
							],
							'XXXOO' => [ 
									'One',
									'Both' 
							] 
					),
					'niuniu' => array (
							'lm' => [ 
									'big',
									'small',
									'One',
									'Both',
									"Zhi",
									"He" 
							],
							'points' => [ 
									'not',
									'nn',
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'stud' => [ 
									'wut',
									'zhad',
									'hul',
									'shunz',
									"sant",
									"liangd",
									"dand",
									"sanh" 
							] 
					),
					'opencode' => $games_cqopen ['opencode'], // 开奖号码
					'expect' => $games_cqopen ['expect'] + 1, // 开奖期数
					'gold' => 100, // 下注金币
					'time' => $_SERVER ['REQUEST_TIME'] 
			),array (
					'userinfo' => array (
							'user_id' => 10,
							'nickname' => "bc10" 
					),
					'lmp' => array (
							'alls' => [ 
									'and' 
							],
							'firstball' => [ 
									"Zhi",
									"He" 
							],
							'secondball' => [ 
							],
							'thirdball' => [ 
									'big',
									'small',
									"He" 
							],
							'thouthball' => [ 
							],
							'fifthball' => [ 
									'big',
									'small',
									'One',
							],
							'firstthree' => [ 
									'bs',
									"zl" 
							],
							'middlethree' => [ 
									'bz',
							],
							'lastthree' => [ 
									'dz',
									'bs',
									"zl" 
							] 
					),
					'OneTofive' => array (
							'firstball' => [ 
									'big',
									'small',
									'One',
									'Both',
									"Zhi",
									"He",
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'secondball' => [ 
									'big',
									'small',
									'One',
									'Both',
									"Zhi",
									"He",
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'thirdball' => [ 
									'big',
									'small',
									'One',
									'Both',
									"Zhi",
									"He",
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'thouthball' => [ 
									'big',
									'small',
									'One',
									'Both',
									"Zhi",
									"He",
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'fifthball' => [ 
									'big',
									'small',
									'One',
									'Both',
									"Zhi",
									"He",
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							] 
					),
					'OneWord' => array (
							'firstThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'middleThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'lastThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'allFive' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							] 
					),
					'TwoWord' => array (
							'firstThree' => [0,1,2,3,4,5,6,7,8,9,11,12,13,14,15,16,17,18,19,
		22,23,24,25,26,27,28,29,33,34,35,36,37,38,39,44,45,46,47,48,
		49,55,56,57,58,59,66,67,68,69,77,78,79,88,89,99],
							'middleThree' => [0,1,2,3,4,5,6,7,8,9,11,12,13,14,15,16,17,18,19,
		22,23,24,25,26,27,28,29,33,34,35,36,37,38,39,44,45,46,47,48,
		49,55,56,57,58,59,66,67,68,69,77,78,79,88,89,99],
							'lastThree' => [0,1,2,3,4,5,6,7,8,9,11,12,13,14,15,16,17,18,19,
		22,23,24,25,26,27,28,29,33,34,35,36,37,38,39,44,45,46,47,48,
		49,55,56,57,58,59,66,67,68,69,77,78,79,88,89,99] 
					),
					'ThreeWord' => array (
							'firstThree' => [0,1,2,3,4,5,6,7,8,9,11,12,13,14,15,16,17,18,19,
		22,23,24,25,26,27,28,29,33,34,35,36,37,38,39,44,45,46,47,48,
		49,55,56,57,58,59,66,67,68,69,77,78,79,88,89,99,111,112,113,
		114,115,116,117,118,119,122,123,124,125,126,127,128,129,133,
		134,135,136,137,138,139,144,145,146,147,148,149,155,156,157,
		158,189,166,167,168,169,177,178,179,188,189,199,222,223,224,
		225,226,227,228,229,233,234,235,236,237,238,239,244,245,246,
		247,248,249,255,256,257,258,259,266,267,268,269,277,278,279,
		288,289,299,333,334,335,336,337,338,339,344,345,346,347,348,
		349,355,356,357,358,359,366,367,368,369,377,378,379,388,389,
		399,444,445,446,447,448,449,455,456,457,458,459,466,467,468,
		469,477,478,479,488,489,499,555,556,557,558,559,566,567,568,
		569,577,578,579,588,589,599,666,667,668,669,677,678,679,688,
		689,699,777,778,779,788,789,799,888,889,899,999],
							'middleThree' => [0,1,2,3,4,5,6,7,8,9,11,12,13,14,15,16,17,18,19,
		22,23,24,25,26,27,28,29,33,34,35,36,37,38,39,44,45,46,47,48,
		49,55,56,57,58,59,66,67,68,69,77,78,79,88,89,99,111,112,113,
		114,115,116,117,118,119,122,123,124,125,126,127,128,129,133,
		134,135,136,137,138,139,144,145,146,147,148,149,155,156,157,
		158,189,166,167,168,169,177,178,179,188,189,199,222,223,224,
		225,226,227,228,229,233,234,235,236,237,238,239,244,245,246,
		247,248,249,255,256,257,258,259,266,267,268,269,277,278,279,
		288,289,299,333,334,335,336,337,338,339,344,345,346,347,348,
		349,355,356,357,358,359,366,367,368,369,377,378,379,388,389,
		399,444,445,446,447,448,449,455,456,457,458,459,466,467,468,
		469,477,478,479,488,489,499,555,556,557,558,559,566,567,568,
		569,577,578,579,588,589,599,666,667,668,669,677,678,679,688,
		689,699,777,778,779,788,789,799,888,889,899,999],
							'lastThree' => [0,1,2,3,4,5,6,7,8,9,11,12,13,14,15,16,17,18,19,
		22,23,24,25,26,27,28,29,33,34,35,36,37,38,39,44,45,46,47,48,
		49,55,56,57,58,59,66,67,68,69,77,78,79,88,89,99,111,112,113,
		114,115,116,117,118,119,122,123,124,125,126,127,128,129,133,
		134,135,136,137,138,139,144,145,146,147,148,149,155,156,157,
		158,189,166,167,168,169,177,178,179,188,189,199,222,223,224,
		225,226,227,228,229,233,234,235,236,237,238,239,244,245,246,
		247,248,249,255,256,257,258,259,266,267,268,269,277,278,279,
		288,289,299,333,334,335,336,337,338,339,344,345,346,347,348,
		349,355,356,357,358,359,366,367,368,369,377,378,379,388,389,
		399,444,445,446,447,448,449,455,456,457,458,459,466,467,468,
		469,477,478,479,488,489,499,555,556,557,558,559,566,567,568,
		569,577,578,579,588,589,599,666,667,668,669,677,678,679,688,
		689,699,777,778,779,788,789,799,888,889,899,999] 
					),
					'Twolocation' => array (
							'OOXXX' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'OXOXX' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'OXXOX' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'OXXXO' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'XOOXX' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'XOXOX' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'XOXXO' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'XXOOX' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'XXOXO' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'XXXOO' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							] 
					),
					'Threelocation' => array (
							'firstThree' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'thirdball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'middleThree' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'thirdball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							],
							'lastThree' => [ 
									'firstball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'secondball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									],
									'thirdball' => [ 
											0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9 
									] 
							] 
					),
					'groupThree' => array (
							'firstThree' => [ 
									0,
											1,
											2,
											3,
											4,
											5,
											6,
											7,
											8,
											9
							],
							'middleThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'lastThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							] 
					),
					'groupSix' => array (
							'firstThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'middleThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'lastThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							] 
					),
					'span' => array (
							'firstThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'middleThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'lastThree' => [ 
									0,
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							] 
					),
					'and' => array (
							'OOXXX' => [ 
									'One',
									'Both' 
							],
							'OXOXX' => [ 
									'One',
									'Both' 
							],
							'OXXOX' => [ 
									'One',
									'Both' 
							],
							'OXXXO' => [ 
									'One',
									'Both' 
							],
							'XOOXX' => [ 
									'One',
									'Both' 
							],
							'XOXOX' => [ 
									'One',
									'Both' 
							],
							'XOXXO' => [ 
									'One',
									'Both' 
							],
							'XXOOX' => [ 
									'One',
									'Both' 
							],
							'XXOXO' => [ 
									'One',
									'Both' 
							],
							'XXXOO' => [ 
									'One',
									'Both' 
							] 
					),
					'niuniu' => array (
							'lm' => [ 
									'big',
									'small',
									'One',
									'Both',
									"Zhi",
									"He" 
							],
							'points' => [ 
									'not',
									'nn',
									1,
									2,
									3,
									4,
									5,
									6,
									7,
									8,
									9 
							],
							'stud' => [ 
									'wut',
									'zhad',
									'hul',
									'shunz',
									"sant",
									"liangd",
									"dand",
									"sanh" 
							] 
					),
					'opencode' => $games_cqopen ['opencode'], // 开奖号码
					'expect' => $games_cqopen ['expect'] + 1, // 开奖期数
					'gold' => 100, // 下注金币
					'time' => $_SERVER ['REQUEST_TIME'] 
			));
// 			foreach ($param as $value) {
// 				$result = explode ( ",", $value ['opencode'] );
// 				$iswin =0;//赢了几注
// 				$value ['bet'] =0;//赢了几注
// 				$value ['win'] ['单注金额'] = $value ['gold'];
// 				$value ['gold'] = 0;
// 				$value ['bet']=(count ( $value ['niuniu'],1)-3);
// 			//前三
// 			$firstthree[0]=
// 			(int)$result[0]
// 			;//第一个球
// 			$firstthree[1]=
// 			(int)$result[1]
// 			;//第二个球
// 			$firstthree[2]=
// 			(int)$result[2]
// 			;//第三个球
// 			sort($firstthree);//正序 排序
// 			$firstNumber=array();
// 			//计算豹子、对子
// 			//如果第三球减去第二球等于零，真则返回1，否则则返回零
// 			$firstNumber[0] = $firstthree[2] - $firstthree[1] === 0 ? 1 : 0;
// 			//如果第二球减去第一球等于零，真则自增1（如果上面的$resNumber[0]是1则+1=2），否则返回上面的出的值，如果上面是1则还是1，如果不是1则是零
// 			$firstNumber[0] = $firstthree[1] - $firstthree[0] === 0 ? ++$firstNumber[0] : $firstNumber[0];
// 			//计算顺子、半顺、杂六
// 			if ($firstthree[2] === 9 && ($firstthree[1] === 8 || $firstthree[1] === 1) &&$firstthree[0] === 0 ) {
// 				$firstNumber[1]=3;//如果有两个球分别等于0和9，另一个球如果等于8或1，那就是顺子
// 			}else if ($firstthree[2] === 9 && $firstthree[1] !== 8 && $firstthree[0] === 0 && $firstthree[1] !== 1){
// 				$firstNumber[1]=4;//如果有两个球分别等于0和9，另一个球如果不等于8或1，那就是半顺
// 			}else {//除去以上两种情况
// 				//第三球减去第二球等于1 真则把1赋值给$resNumber[1] 否则把0赋值给他
// 				$firstNumber[1] = $firstthree[2] - $firstthree[1] === 1 ? 1 : 0;
// 				//第二球减去第一球等于1 真则自增1（如果上面的$resNumber[1]是1则+1=2），否则返回上面的出的值，如果上面是1则还是1，如果不是1则是零
// 				$firstNumber[1] = $firstthree[1] - $firstthree[0] === 1 ? ++$firstNumber[1] : $firstNumber[1];
// 			}
// 			//中三
// 			$middlethree[0]=(int)$result[1];//第二个球
// 			$middlethree[1]=(int)$result[2];//第三个球
// 			$middlethree[2]=(int)$result[3];//第四个球
// 			sort($middlethree);//正序 排序
// 			$middleNumber=array();
// 			//计算豹子、对子
// 			//如果第三球减去第二球等于零，真则返回1，否则则返回零
// 			$middleNumber[0] = $middlethree[2] - $middlethree[1] === 0 ? 1 : 0;
// 			//如果第二球减去第一球等于零，真则自增1（如果上面的$resNumber[0]是1则+1=2），否则返回上面的出的值，如果上面是1则还是1，如果不是1则是零
// 			$middleNumber[0] = $middlethree[1] - $middlethree[0] === 0 ? ++$middleNumber[0] : $middleNumber[0];
// 			//计算顺子、半顺、杂六
// 			if ($middlethree[2] === 9 && ($middlethree[1] === 8 || $middlethree[1] === 1) &&$middlethree[0] === 0 ) {
// 				$middleNumber[1]=3;//如果有两个球分别等于0和9，另一个球如果等于8或1，那就是顺子
// 			}else if ($middlethree[2] === 9 && $middlethree[1] !== 8 && $middlethree[0] === 0 && $middlethree[1] !== 1){
// 				$middleNumber[1]=4;//如果有两个球分别等于0和9，另一个球如果不等于8或1，那就是半顺
// 			}else {//除去以上两种情况
// 				//第三球减去第二球等于1 真则把1赋值给$resNumber[1] 否则把0赋值给他
// 				$middleNumber[1] = $middlethree[2] - $middlethree[1] === 1 ? 1 : 0;
// 				//第二球减去第一球等于1 真则自增1（如果上面的$resNumber[1]是1则+1=2），否则返回上面的出的值，如果上面是1则还是1，如果不是1则是零
// 				$middleNumber[1] = $middlethree[1] - $middlethree[0] === 1 ? ++$middleNumber[1] : $middleNumber[1];
// 			}
// 			//后三
// 			$lastthree[0]=(int)$result[2];//第三个球
// 			$lastthree[1]=(int)$result[3];//第四个球
// 			$lastthree[2]=(int)$result[4];//第五个球
// 			sort($lastthree);//正序 排序
// 			$lastNumber=array();
// 			//计算豹子、对子
// 			//如果第三球减去第二球等于零，真则返回1，否则则返回零
// 			$lastNumber[0] = $lastthree[2] - $lastthree[1] === 0 ? 1 : 0;
// 			//如果第二球减去第一球等于零，真则自增1（如果上面的$resNumber[0]是1则+1=2），否则返回上面的出的值，如果上面是1则还是1，如果不是1则是零
// 			$lastNumber[0] = $lastthree[1] - $lastthree[0] === 0 ? ++$lastNumber[0] : $lastNumber[0];
// 			//计算顺子、半顺、杂六
// 			if ($lastthree[2] === 9 && ($lastthree[1] === 8 || $lastthree[1] === 1) &&$lastthree[0] === 0 ) {
// 				$lastNumber[1]=3;//如果有两个球分别等于0和9，另一个球如果等于8或1，那就是顺子
// 			}else if ($lastthree[2] === 9 && $lastthree[1] !== 8 && $lastthree[0] === 0 && $lastthree[1] !== 1){
// 				$lastNumber[1]=4;//如果有两个球分别等于0和9，另一个球如果不等于8或1，那就是半顺
// 			}else {//除去以上两种情况
// 				//第三球减去第二球等于1 真则把1赋值给$resNumber[1] 否则把0赋值给他
// 				$lastNumber[1] = $lastthree[2] - $lastthree[1] === 1 ? 1 : 0;
// 				//第二球减去第一球等于1 真则自增1（如果上面的$resNumber[1]是1则+1=2），否则返回上面的出的值，如果上面是1则还是1，如果不是1则是零
// 				$lastNumber[1] = $lastthree[1] - $lastthree[0] === 1 ? ++$lastNumber[1] : $lastNumber[1];
// 			}
// 			//--------------------------------------------牛牛(☄⊙ω⊙)☄---------------------
// 			$value ['win'] ['下注'] = '下了' . $value['bet'] . '注';
// 			$value ['win'] ['中奖'] = '中了' . $iswin . '注';
// 			$value ['win'] ['获利'] = $value ['gold'] ;
// 			$msg [$value ['userinfo']['nickname']] ['win'] = $value ['win'];
// 			}
			$msg=$this->games_cqres->saveResult($param,$odds);
			dump($msg);
			
		}//post
		dump ( '开奖的时间：' . date ( 'Y-m-d H:i:s', strtotime ( $games_cqopen ['opentime'] ) + 600 ) );
		dump ( '现在的时间：' . date ( 'Y-m-d H:i:s', $_SERVER ['REQUEST_TIME'] ) );
		dump ( '开奖的时间戳：' . strtotime ( $games_cqopen ['opentime'] ) );
		dump ( '现在的时间戳：' . $_SERVER ['REQUEST_TIME'] );
		if ((date ( 'H', strtotime ( $games_cqopen ['opentime'] ) )) < (date ( 'H', $_SERVER ['REQUEST_TIME'] ))) {
			$games_cqopen ['closetime'] = '未开始';
			$games_cqopen ['opentime'] = '未开始';
		} else if ((date ( 'Ymd', strtotime ( $games_cqopen ['opentime'] ) ) . '108') <= $games_cqopen ['expect']) {
			if (strtotime ( $games_cqopen ['opentime'] ) + 300 >= $_SERVER ['REQUEST_TIME']) { // 判断开奖时间十分钟一次。也就是600秒，如果大于现在的时间
				if (strtotime ( $games_cqopen ['opentime'] ) + 180 >= $_SERVER ['REQUEST_TIME']) { //
					$games_cqopen ['closetime'] = '晚上：' . friendlyDate ( strtotime ( $games_cqopen ['opentime'] ) + 120, 'games' );
				} else {
					$games_cqopen ['closetime'] = '晚上：' . '停止下注';
				}
				$games_cqopen ['opentime'] = '晚上：' . friendlyDate ( strtotime ( $games_cqopen ['opentime'] ) + 240, 'games' );
			} else {
				$games_cqopen ['closetime'] = '晚上：' . friendlyDate ( strtotime ( $games_cqopen ['opentime'] ) + 420, 'games' );
				$games_cqopen ['opentime'] = '晚上：' . friendlyDate ( strtotime ( $games_cqopen ['opentime'] ) + 540, 'games' );
			}
		} else if ((date ( 'Ymd', strtotime ( $games_cqopen ['opentime'] ) ) . '108') >= $games_cqopen ['expect'] && (date ( 'H', strtotime ( $games_cqopen ['opentime'] ) )) === (date ( 'H', $_SERVER ['REQUEST_TIME'] ))) {
			if (strtotime ( $games_cqopen ['opentime'] ) + 600 >= $_SERVER ['REQUEST_TIME']) { // 判断开奖时间十分钟一次。也就是600秒，如果大于现在的时间
				if (strtotime ( $games_cqopen ['opentime'] ) + 480 >= $_SERVER ['REQUEST_TIME']) { //
					$games_cqopen ['closetime'] = '白天：' . friendlyDate ( strtotime ( $games_cqopen ['opentime'] ) + 420, 'games' );
				} else {
					$games_cqopen ['closetime'] = '白天：' . '停止下注';
				}
				$games_cqopen ['opentime'] = '白天：' . friendlyDate ( strtotime ( $games_cqopen ['opentime'] ) + 540, 'games' );
			} else {
				$games_cqopen ['closetime'] = '白天：' . friendlyDate ( strtotime ( $games_cqopen ['opentime'] ) + 1020, 'games' );
				$games_cqopen ['opentime'] = '白天：' . friendlyDate ( strtotime ( $games_cqopen ['opentime'] ) + 1140, 'games' );
			}
		}
		$this->assign ( [ 
				'games_cqopen' => $games_cqopen,
				'lower_limit' => $lower_limit 
		] );
		return $this->fetch ( 'cq_res' );
	}
	
}
?>