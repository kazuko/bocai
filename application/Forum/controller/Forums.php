<?php

namespace app\forum\controller;

use think\Controller;
use think\facade\Cookie;
use app\forum\model\User_user;
use app\forum\model\Forum_zone;
use app\forum\model\Forum_post;
use app\forum\model\Forum_comment;
use app\forum\model\Forum_reply;
use app\forum\model\System_info;
use app\forum\model\User_trading;
use app\forum\model\User_like;
use app\forum\model\User_medal;
/**
 * 论坛（社区）控制层
 * @author Motley
 *@forum_list  -----社区主页面（分区页面）
 *@forum_details  --帖子列表页面
 *@forum_post_like -点赞方法
 *@forum_issue  ----发帖页面
 *@forum_comment  --评论
 *@forum_reply  ----回复
 */
class Forums extends Controller {
	
	protected $forum_zone;
	protected $forum_reply;
	protected $comment;
	protected $user_user;
	protected $user_trading;
	protected $forum_post;
	
	protected function initialize()
	{
		parent::initialize();
		$this->forum_zone = new Forum_zone ();
		$this->forum_reply = new Forum_reply ();
		$this->comment = new Forum_comment ();
		$this->user_user = new User_user ();
		$this->user_trading = new User_trading ();
		$this->forum_post = new Forum_post ();
	}
	public function __construct() {
		parent::__construct ();
		$systemInfo = System_info::find ()->column ( array (
				'tie_pride_show', // 发帖奖励：1、显示；2、关闭
				'tie_pride_num', // 发帖奖励帖数
				'tie_pride_gold', // 奖励金币
				'tie_pride_sliver', // 发帖奖励积分
				'tie_limit_words', // 发帖标题字数
				'tie_limit_seconds', // 新用户限制：秒
				'tie_limit_relieve', // 充值解除限制：1表示开启；0表示关闭
				'tie_limit_refender_seconds', // 防刷间隔：秒
				'huitie_open', // 是否允许回帖：0、允许；1、禁止；2、限时禁止
				'huitie_time_start', // 限时禁止的起始时间
				'huitie_time_end', // 限时禁止的终止时间
				'huitie_order', // 回帖楼层排序：0、顺序；1、倒序
				'huitie_num', // 回帖奖励帖数
				'huitie_gold', // 回帖奖励金币
				'huitie_sliver', // 回帖奖励积分
				'huitie_vip_gold', // 回帖奖励vip额外奖励金币
				'huitie_vip_sliver', // 回帖奖励vip额外奖励积分
				'huitie_repeate', // 是否允许发重复的内容：0、允许；1、不允许
				'huitie_refender_seconds' 
		) ); // 防刷间隔
		Cookie::set ( 'systemInfo', $systemInfo );
		if (Cookie::has ( 'user_id' )) {
			$userid = Cookie::get ( 'user_id' );
// 			$user = $this->user_user->get ( $userid ); // 用户表
// 			$this->assign ( 'user_id', $userid );
		}
	}
	
	// 论坛主页面
	public function forum_list() {
		// if (Cookie::has ( 'user_id' )) {
		// $user = Cookie::get ( 'user_id' );
		// $this->assign ( 'user_id', $user );
		// } else {
		// // return json ( array (
		// // 'code' => 0,
		// // 'result' => '请先登录！'
		// // ) );
		// }
		 $zone = $this->forum_zone->find ();
		foreach ( $zone as $key => $value ) {
			// 时间转换
			if (! $zone [$key] ['post_id']) {
				$zone [$key] ['post_title'] = '该区暂时没有帖子';
				$zone [$key] ['ADDTIME'] = '';
				$zone [$key] ['user_name'] = '';
			} else {
				$zone [$key] ['ADDTIME'] = friendlyDate ( $value ['ADDTIME'] );
				if (strlen ( $value ['COUNT'] ) > 5) {
					$zone [$key] ['COUNT'] = friendlyCount ( $value ['COUNT'] );
				}
			}
		}
		return json ( array (
				'zone' => $zone,
				'result' => '',
				'code' => 200 
		) );
// 		$this->assign ( 'zone', $zone );
// 		return $this->fetch ( 'forum_list' );
	}
	// 帖子页面
	public function forum_details() {
		// if (Cookie::has ( 'user_id' )) {
		// $user_id = Cookie::get ( 'user_id' );
		// $this->assign ( 'user_id', $user_id );
		// }
		$param = $this->request->param ();
		if (isset ( $param )) { // 判断参数
			$user = $this->user_user->get ( $param ['user_id'] ); // 用户表
			$post = $this->forum_post->where ( 'zone_id', $param ['id'] )->select (); // 帖子表
			$zone = $this->forum_zone->get ( $param ['id'] ); // 论坛分区
			Cookie::set ( 'zone_id', $zone ['id'] );
			if (count ( $post ) !== 0) { // 判断是否有帖子
				foreach ( $post as $key => $val ) {
					$regImg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
					$post [$key] ['isimg'] = preg_match_all ( $regImg, $post [$key] ['content'], $allImg );
					$post [$key] ['addtime'] = friendlyDate ( $val ['addtime'] );
					if ($post [$key] ['isimg'] !== 0) {
						$post [$key] ['allImg'] = $allImg [0];
					}
					$post [$key] ['head'] = $this->user_user->where ( 'id', $val ['user_id'] )->column ( 'head' );
					$post [$key] ['commentcunt'] = $this->comment->where ( 'post_id', $post [$key] ['id'] )->count (); // 该帖子的评论数
					$post [$key] ['like'] = User_like::where ( 'post_id', $post [$key] ['id'] )->count (); // 该帖子的点赞数
				}
				if ($zone ['door'] == 1) { // 判断是否是会员才可能进入
					if ($user ['vip_time'] > time ()) { // 判断用户的vip时间是否到期
						return json ( array (
								'zone' => $zone,
								'result' => '',
								'user' => $user,
								'zone' => $zone,
								'post' => $post,
								'code' => 200 
						) );
// 						$this->assign ( [ 
// 								'user' => $user,
// 								'result' => '',
// 								'zone' => $zone,
// 								'post' => $post 
// 						] );
// 						return $this->fetch ( 'forum_details' );
					} else { // 会员已过期
						return json ( array (
								'code' => 0,
								'result' => '尊敬的用户，该区只限定会员才可进入！' 
						) );
					}
				} else { // 不用会员也可以进
				         return json(array('zone' => $zone,
				         'user'=>$user,
				         'zone'=>$zone,
				         'post'=>$post,
				         'result'=>'',
				         'code'=>200));
// 					$this->assign ( [ 
// 							'user' => $user,
// 							'zone' => $zone,
// 							'result' => '',
// 							'post' => $post 
// 					] );
// 					return $this->fetch ( 'forum_details' );
				}
			} else { // 判断是否有帖子（没有）
			         return json(array('zone' => $zone,
			         'user'=>$user,
			         'zone'=>$zone,
			         'post'=>'',
			         'code'=>200,
			         'result' => '暂时没有帖子'));
// 				$this->assign ( [ 
// 						'post' => $post,
// 						'user' => $user,
// 						'result' => '',
// 						'zone' => $zone 
// 				] );
// 				return $this->fetch ( 'forum_details' );
			}
		} else { // 参数获取失败，需重新登录（防止未登录直接进入）
			return json ( array (
					'code' => 404,
					'result' => '登录超时，请重新登录！' 
			) );
		}
	}
	// 回复
	public function reply($param, $systemInfo, $user, $newtrading) {
		$replyinfo = $this->forum_reply->replyinfo ( $param ); // 查询是否有回复
		if ($replyinfo) { // 如果有回复
			if (time () > ($replyinfo ['addtime'] + $systemInfo [0] ['huitie_refender_seconds'])) {
				$result = $this->forum_reply->add ( $param, $systemInfo, $user );
				return $this->returnMsg ( $result, $systemInfo, $user );
			} else { // 否则则显示操作过快，提示请等待
				if ($systemInfo [0] ['tie_limit_relieve'] == 1) { // 充值解除限制开启
					if ($newtrading) { // 如果有充值
						$result = $this->forum_reply->add ( $param, $systemInfo, $user );
						return $this->returnMsg ( $result, $systemInfo, $user );
					}
				}
				return json ( array (
						'code' => 403,
						'result' => "操作过快，点击<a href=''>充值金币</a>不再限制或稍后再试" 
				) );
			}
		} else { // 没有回复
		         // 如果现在的时间大于注册时间则执行添加帖子
			if (time () > ($user ['addtime'] + $systemInfo [0] ['tie_limit_seconds'])) {
				$result = $this->forum_reply->add ( $param, $systemInfo, $user );
				return $this->returnMsg ( $result, $systemInfo, $user );
			} else { // 否则则显示操作过快，充值或者等待
				if ($systemInfo [0] ['tie_limit_relieve'] == 1) { // 充值解除限制开启
					if ($newtrading) { // 如果有充值
						$result = $this->forum_reply->add ( $param, $systemInfo, $user );
						return $this->returnMsg ( $result, $systemInfo, $user );
					}
				}
				return json ( array (
						'code' => 405,
						'result' => "新用户需稍后才可发帖，点击<a href=''>充值金币</a>不再限制或稍后再试" 
				) );
			}
		}
	}
	// 点赞
	public function forum_post_like() {
		$param = $this->request->param ();
		if ($param) {
			$param ['time'] = time ();
			$like = User_like::where ( [  // 判断是否有重复的内容
					'user_id' => $param ['user_id'],
					'post_id' => $param ['post_id'],
					'type' => 1 
			] )->find ();
			if (! $like) {
				$param ['type'] = 1;
				User_like::create ( $param );
				return json ( array (
						'code' => 200,
						'result' => '' 
				) );
			} else {
				if (User_like::destroy ( $like ['id'] )) {
					return json ( array (
							'code' => 200,
							'result' => '' 
					) );
				} else {
					return json ( array (
							'code' => 0,
							'result' => '取消失败' 
					) );
				}
			}
		} else {
			return json ( array (
					'code' => 0,
					'result' => '系统异常，请重新登录或联系管理员！' 
			) );
		}
	}
	// 评论
	public function forum_comment() {
		if (Cookie::has ( 'user_id' )) {
			$user_id = Cookie::get ( 'user_id' );
			$this->assign ( 'user_id', $user_id );
		} else {
			// return json ( array (
			// 'code' => 0,
			// 'result' => '请先登录！'
			// ) );
		}
		if (Cookie::has ( 'systemInfo' )) { // 加载配置
			$systemInfo = Cookie::get ( 'systemInfo' );
		} else {
			return json ( array (
					'code' => 0,
					'result' => '系统异常，请重新登录或联系管理员！' 
			) );
		}
		if (Cookie::has ( 'zone_id' )) { // 分区
			$zone_id = Cookie::get ( 'zone_id' );
		} else {
			return json ( array (
					'code' => 0,
					'result' => '系统异常，请重新登录或联系管理员！' 
			) );
		}
		$user = $this->user_user->get ( $user_id ); // 用户表
		if ($this->request->isPost ()) {
			$param = $this->request->param ();
			$param ['user_name'] = $user ['nickname'];
			$AscCount = $this->comment->LastsAscCount ( $param ); // 评论总次数，前几次有奖励
			$LastDesc = $this->comment->LastDesc ( $param ); // 根据用户id查询距离现在时间最近的评论
			$newtrading = $this->user_trading->LastTopUpDesc ( $param, $systemInfo ); // 获取最近的充值记录
			if ($systemInfo [0] ['huitie_open'] == 0) { // 是否开启回帖
				if ($LastDesc) { // 有评论，则判断上一次操作距离现在的时间
					if ($param ['id']) { // 如果ID存在，就表示这是回复
						return $this->reply ( $param, $systemInfo, $user, $newtrading );
					} else { // 否则ID不存在，就表示这是评论
					         // 如果现在的时间大于评论时间
						if (time () > ($LastDesc ['addtime'] + $systemInfo [0] ['huitie_refender_seconds'])) {
							$result = $this->comment->add ( $param, $systemInfo, $user, $AscCount );
							return $this->returnMsg ( $result, $systemInfo, $user );
						} else { // 否则则显示操作过快，提示请等待
							if ($systemInfo [0] ['tie_limit_relieve'] == 1) { // 充值解除限制开启
								if ($newtrading) { // 如果有充值
									$result = $this->comment->add ( $param, $systemInfo, $user, $AscCount );
									return $this->returnMsg ( $result, $systemInfo, $user );
								}
							}
							return json ( array (
									'code' => 403,
									'result' => "操作过快，点击<a href=''>充值金币</a>不再限制或稍后再试" 
							) );
						}
					}
				} else {
					if ($param ['id']) { // 如果ID存在，就表示这是回复
						return $this->reply ( $param, $systemInfo, $user, $newtrading);
					} else { // 否则ID不存在，就表示这是评论
					         // 如果现在的时间大于注册时间则执行添加帖子
						if (time () > ($user ['addtime'] + $systemInfo [0] ['tie_limit_seconds'])) {
							$result = $this->comment->add ( $param, $systemInfo, $user, $AscCount );
							return $this->returnMsg ( $result, $systemInfo, $user );
						} else { // 否则则显示操作过快，充值或者等待
							if ($systemInfo [0] ['tie_limit_relieve'] == 1) { // 充值解除限制开启
								if ($newtrading) { // 如果有充值
									$result = $this->comment->add ( $param, $systemInfo, $user, $AscCount );
									return $this->returnMsg ( $result, $systemInfo, $user );
								}
							}
							return json ( array (
									'code' => 405,
									'result' => "新用户需稍后才可发帖，点击<a href=''>充值金币</a>不再限制或稍后再试" 
							) );
						}
					}
				}
			} elseif ($systemInfo [0] ['huitie_open'] == 1) { // 是否关闭回帖
				return json ( array (
						'code' => 0,
						'result' => '该贴禁止评论！' 
				) );
			} elseif ($systemInfo [0] ['huitie_open'] == 2) { // 是否开启限时禁止
				if ($systemInfo [0] ['huitie_time_start'] <= time () && $systemInfo [0] ['huitie_time_end'] >= time ()) {
					$startTime = date ( 'Y-m-d H:i', $systemInfo [0] ['huitie_time_start'] );
					$endTime = date ( 'Y-m-d H:i', $systemInfo [0] ['huitie_time_end'] );
					return json ( array (
							'code' => 0,
							'result' => "该贴在" . $startTime . "到" . $endTime . "禁止评论！" 
					) );
				} else {
					if ($LastDesc) { // 有评论，则判断上一次操作距离现在的时间
						if ($param ['id']) { // 如果ID存在，就表示这是回复
							return $this->reply ( $param, $systemInfo, $user, $newtrading);
						} else { // 否则ID不存在，就表示这是评论
						         // 如果现在的时间大于评论时间
							if (time () > ($LastDesc ['addtime'] + $systemInfo [0] ['huitie_refender_seconds'])) {
								$result = $this->comment->add ( $param, $systemInfo, $user, $AscCount );
								return $this->returnMsg ( $result, $systemInfo, $user );
							} else { // 否则则显示操作过快，提示请等待
								if ($systemInfo [0] ['tie_limit_relieve'] == 1) { // 充值解除限制开启
									if ($newtrading) { // 如果有充值
										$result = $this->comment->add ( $param, $systemInfo, $user, $AscCount );
										return $this->returnMsg ( $result, $systemInfo, $user );
									}
								}
								return json ( array (
										'code' => 403,
										'result' => "操作过快，点击<a href=''>充值金币</a>不再限制或稍后再试" 
								) );
							}
						}
					} else {
						if ($param ['id']) { // 如果ID存在，就表示这是回复
							return $this->reply ( $param, $systemInfo, $user, $newtrading);
						} else { // 否则ID不存在，就表示这是评论
						         // 如果现在的时间大于注册时间则执行添加帖子
							if (time () > ($user ['addtime'] + $systemInfo [0] ['tie_limit_seconds'])) {
								$result = $this->comment->add ( $param, $systemInfo, $user, $AscCount );
								return $this->returnMsg ( $result, $systemInfo, $user );
							} else { // 否则则显示操作过快，充值或者等待
								if ($systemInfo [0] ['tie_limit_relieve'] == 1) { // 充值解除限制开启
									if ($newtrading) { // 如果有充值
										$result = $this->comment->add ( $param, $systemInfo, $user, $AscCount );
										return $this->returnMsg ( $result, $systemInfo, $user );
									}
								}
								return json ( array (
										'code' => 405,
										'result' => "新用户需稍后才可发帖，点击<a href=''>充值金币</a>不再限制或稍后再试" 
								) );
							}
						}
					}
				}
			}
		} else {
			$param = $this->request->param ();
			if (isset ( $param )) { // 判断参数
				$zone = $this->forum_zone->get ( $zone_id ); // 分区表
				$post = $this->forum_post->get ( $param ['post_id'] ); // 帖子表
				$author = $this->user_user->get ( $post ['user_id'] ); // 用户表
				$like = User_like::where ( 'post_id', $post ['id'] )->count (); // 该帖子的点赞数
				$comment = $this->comment->getComment ( $post, $systemInfo );
				$this->forum_post->where ( 'id', $param ['post_id'] )->setInc ( 'visitor', 1 ); // 访问量
				$post ['addtime'] = friendlyDate ( $post ['addtime'] );
				if ($zone ['gold'] == 0) { // 看帖如果不需要金币
					if (count ( $comment ) !== 0) { // 该帖子有评论
						foreach ( $comment as $key => $val ) {
							$comment [$key] ['user'] = $this->user_user->get ( $comment [$key] ['user_id'] );
							$comment [$key] ['addtime'] = friendlyDate ( $val ['addtime'] );
							$comment [$key] ['reply'] = $this->forum_reply->where ( [ 
									'comment_id' => $comment [$key] ['id'] 
							] )->select ();
							foreach ( $comment [$key] ['reply'] as $i => $v ) {
								$v ['addtime'] = friendlyDate ( $v ['addtime'] );
							}
							if (count ( $comment [$key] ['reply'] ) == 0) {
								$comment [$key] ['reply'] = '';
							}
							$comment [$key] ['medal'] = User_medal::where ( [ 
									'user_id' => $comment [$key] ['user_id'],
									'status' => 1 
							] )->select ();
							if (count ( $comment [$key] ['medal'] ) == 0) {
								$comment [$key] ['medal'] = '';
							}
						}
						return json(array('author' => $author,
						'like'=>$like,
						'comment'=>$comment,
						'post'=>$post,
						'code'=>0,
						'result' => ''));
// 						$this->assign ( [ 
// 								'author' => $author,
// 								'like' => $like,
// 								'comment' => $comment,
// 								'post' => $post 
// 						] );
					} else { // 没有评论
					         return json(array('author' => $author,
					         'like'=>$like,
					         'comment'=>$comment,
					         'post'=>$post,
					         'code'=>0,
					         'result' => '暂时没有评论，来坐沙发吧'));
// 						$this->assign ( [ 
// 								'like' => $like,
// 								'author' => $author,
// 								'comment' => $comment,
// 								'post' => $post 
// 						] );
					}
				} else {//如果需要金币
					if ($user ['gold'] >= $zone ['gold']) {//查看用户金币是否足够
						$result = $this->user_user->findByGold ( $zone, $user, $author );
						if (count ( $comment ) !== 0) { // 该帖子有评论
							foreach ( $comment as $key => $val ) {
								$comment [$key] ['user'] =$this->user_user->get ( $comment [$key] ['user_id'] );
								$comment [$key] ['addtime'] = friendlyDate ( $val ['addtime'] );
								$comment [$key] ['reply'] = $this->forum_reply->where ( [ 
										'comment_id' => $comment [$key] ['id'] 
								] )->select ();
								foreach ( $comment [$key] ['reply'] as $i => $v ) {
									$v ['addtime'] = friendlyDate ( $v ['addtime'] );
								}
								if (count ( $comment [$key] ['reply'] ) == 0) {
									$comment [$key] ['reply'] = '';
								}
								$comment [$key] ['medal'] = User_medal::where ( [ 
										'user_id' => $comment [$key] ['user_id'],
										'status' => 1 
								] )->select ();
								if (count ( $comment [$key] ['medal'] ) == 0) {
									$comment [$key] ['medal'] = '';
								}
							}
							return json(array('author' => $author,
							'like'=>$like,
							'comment'=>$comment,
							'post'=>$post,
							'code'=>0,
							'result' => $result));
// 							$this->assign ( [ 
// 									'author' => $author,
// 									'like' => $like,
// 									'comment' => $comment,
// 									'post' => $post 
// 							] );
						} else { // 没有评论
						         return json(array('author' => $author,
						         'like'=>$like,
						         'comment'=>$comment,
						         'post'=>$post,
						         'code'=>0,
						         'result' => $result));
// 							$this->assign ( [ 
// 									'like' => $like,
// 									'author' => $author,
// 									'comment' => $comment,
// 									'post' => $post 
// 							] );
						}
					} else { // 金币不足
						return json ( array (
								'code' => 405,
								'result' => '金币不足看不了帖啦！' 
						) );
					}
				}
			} else { // 参数获取失败，需重新登录（防止未登录直接进入）
				return json ( array (
						'code' => 404,
						'result' => '迷路了吧？，请重新登录！' 
				) );
			}
// 			return $this->fetch ( 'forum_comment' );
		}
	}
	// 发帖
	public function forum_issue() {
		// if (Cookie::has ( 'user_id' )) {
		// $user = Cookie::get ( 'user_id' );
		// $this->assign ( 'user_id', $user );
		// } else {
		// // return json ( array (
		// // 'code' => 0,
		// // 'result' => '请先登录！'
		// ) );}
		if ($this->request->isPost ()) {
			$param = $this->request->param ();
			$user = $this->user_user->get ( $param ['user_id'] ); // 用户表
			$param ['user_name'] = $user ['nickname']; // 用户名
			if (Cookie::has ( 'systemInfo' )) { // 加载配置
				$systemInfo = Cookie::get ( 'systemInfo' );
			} else {
				return json ( array (
						'code' => 0,
						'result' => '系统异常，请重新登录或联系管理员！' 
				) );
			}
			// 如果标题字数超过指定字数则返回指定提示
			if (strlen ( $param ['title'] ) > $systemInfo [0] ['tie_limit_words']) {
				return json ( array (
						'code' => 0,
						'result' => '标题太长，超过设定值了！请重新输入' 
				) );
			}
			$post = $this->forum_post->LastsAscCount ( $param ); // 发帖总次数，前几次有奖励
			$posttime = $this->forum_post->LastDesc ( $param ); // 根据用户id查询距离现在时间最近的帖子
			$newtrading =  $this->user_trading->LastTopUpDesc ( $param, $systemInfo ); // 获取最近的充值记录
			if (! $posttime) { // 没有帖子，需要判断是否为新注册用户
			                   // 如果现在的时间大于注册时间则执行添加帖子
				if (time () > ($user ['addtime'] + $systemInfo [0] ['tie_limit_seconds'])) {
					$result = $this->forum_post->add ( $param, $systemInfo, $user, $post ); // 添加帖子
					return $this->returnMsg ( $result, $systemInfo, $user );
				} else { // 否则则显示操作过快，充值或者等待
					if ($systemInfo [0] ['tie_limit_relieve'] == 1) { // 充值解除限制开启
						if ($newtrading) { // 如果有充值
							$result = $this->forum_post->add ( $param, $systemInfo, $user, $post ); // 添加帖子
							return $this->returnMsg ( $result, $systemInfo, $user );
						}
					}
					return json ( array (
							'code' => 405,
							'result' => "新用户需稍后才可发帖，点击<a href=''>充值金币</a>不再限制或稍后再试" 
					) );
				}
			} else { // 有帖子，则判断上一次操作距离现在的时间
			         // 如果现在的时间大于发帖时间
				if (time () > ($posttime ['addtime'] + $systemInfo [0] ['tie_limit_refender_seconds'])) {
					$result = $this->forum_post->add ( $param, $systemInfo, $user, $post );
					return $this->returnMsg ( $result, $systemInfo, $user );
				} else { // 否则则显示操作过快，充值或者等待
					if ($systemInfo [0] ['tie_limit_relieve'] == 1) { // 充值解除限制开启
						if ($newtrading) { // 如果有充值
							$result = $this->forum_post->add ( $param, $systemInfo, $user, $post ); // 添加帖子
							return $this->returnMsg ( $result, $systemInfo, $user );
						}
					}
					return json ( array (
							'code' => 403,
							'result' => "操作过快，点击<a href=''>充值金币</a>不再限制或稍后再试" 
					) );
				}
			}
		} // 不是post请求，没提交表单↓
		$param = $this->request->param ();
		if ($param) {
			$zone = $this->forum_zone->get ( $param ['zone_id'] ); // 论坛分区
			if ($zone ['limit_tie'] == 1) { // 是否限制发帖（限制）
				return json ( array (
						'code' => 0,
						'result' => '该区已被限制发帖！请联系管理员解除限制！' 
				) );
			} else { // 是否限制发帖（不限制）
			         return json ( array (
			         'param' => $param,
			         'code' => 200,
			         'result' => ''
			         ) );
// 				$this->assign ( 'param', $param );
// 				return $this->fetch ( 'forum_issue' );
			}
		} else {
			return json ( array (
					'code' => 404,
					'result' => '迷路了吧？请重新登录！' 
			) );
		}
	}
	// 登录
	public function forum_login() {
		if ($this->request->ispost ()) {
			$param = $this->request->param ();
			$user = $this->user_user->where ( [ 
					'username' => $param ['username'],
					'password' => $param ['password'] 
			] )->find ();
			if (empty ( $user )) {
				$result = 500;
			} else {
				Cookie::set ( 'user_id', $user ['id'] );
				$this->redirect ( "forum_list" );
			}
		} else {
			if (Cookie::has ( 'result' )) {
				$result = Cookie::get ( 'result' );
				Cookie::set ( 'result', '' );
				Cookie::delete ( 'result' );
			} else {
				$result = 0;
				$param = '';
			}
		}
		$this->assign ( 'result', $result );
		return $this->fetch ( 'forum_login' );
	}
	// 登出
	public function forum_exit() {
		if (Cookie::has ( 'user_id' )) {
			Cookie::set ( 'user_id', '' );
			Cookie::delete ( 'user_id' );
			$result = 200;
		}
		Cookie::set ( 'result', $result );
		$this->redirect ( "Forum/Forums/forum_login", [ 
				'result' => $result 
		] );
	}
	// 返回信息
	public function returnMsg($result, $systemInfo, $user) {
		switch ($result) {
			case 500 :
				return json ( array (
						'code' => 0,
						'result' => '操作异常，请联系管理员！' 
				) );
				break;
			case 501 :
				return json ( array (
						'code' => 0,
						'result' => '操作失败，不允许发重复的内容！' 
				) );
				break;
			case 200 :
				return json ( array (
						'code' => 200,
						'result' => '操作成功' 
				) );
				break;
			case 201 :
				if ($systemInfo [0] ['tie_pride_show'] = 1) {
					$gold = $systemInfo [0] ['huitie_gold'];
					$sliver = $systemInfo [0] ['huitie_sliver'];
					$vipgold = $systemInfo [0] ['huitie_vip_gold'];
					$vipsliver = $systemInfo [0] ['huitie_vip_sliver'];
					$num = $systemInfo [0] ['huitie_num'];
					if ($user ['vip_time'] > time ()) {
						return json ( array (
								'code' => 200,
								'result' => "奖励金币" . $gold . "奖励积分" . $sliver . "由于您是尊贵的会员，额外奖励金币" . $vipgold . "额外奖励积分" . $vipsliver . "(温馨提示：用户前" . $num . "次评论回复或发帖才有奖励)",
								'gold' => $gold,
								'sliver' => $sliver,
								'vipgold' => $vipgold,
								'vipsliver' => $vipsliver,
								'num' => $num 
						) );
					} else {
						return json ( array (
								'code' => 200,
								'result' => "奖励金币" . $gold . "奖励积分" . $sliver . "(温馨提示：用户前" . $num . "次评论回复或发帖才有奖励)",
								'gold' => $gold,
								'sliver' => $sliver,
								'num' => $num 
						) );
					}
				} else {
					return json ( array (
							'code' => 200,
							'result' => '操作成功' 
					) );
				}
				break;
		}
	}
}
