<?php
namespace app\forum\model;
use think\Model;
use app\forum\model\Sliver_history;
use app\forum\model\Gold_history;
/**
* 
*/
class Forum_comment extends Model
{
	// protected $table = "bc_forum_comment";
	/**
	 * 获取该贴的评论(按照配置，是正序还是倒序)
	 */
	function getComment($post,$systemInfo) {
		if ($systemInfo[0]['huitie_order'] ==0) {
			return $this->where('post_id',$post ['id'])->order ( 'addtime asc' )->select();
		}
		if ($systemInfo[0]['huitie_order'] ==1){
			return $this->where('post_id',$post ['id'])->order ( 'addtime desc' )->select();
		}
	}
	/**
	 * 添加评论，并且前几个帖子有奖励
	 */
	function add($param,$systemInfo,$user,$AscCount) {
		$param ['addtime'] = time(); // 时间
		$data = array (
				'gold' => $user ['gold'] + $systemInfo [0] ['huitie_gold'], // 评论奖励金币
				// 评论奖励积分
				'integral' => $user ['integral'] + $systemInfo [0] ['huitie_sliver']
		);
		$comment=$this->where([ //判断是否有重复的内容
					'user_id' => $param ['user_id'],
					'post_id' => $param ['post_id'],
					'content' => $param ['content'] 
			])->count();
		$this->startTrans (); // 开启事务
		try {
			if ($systemInfo [0] ['huitie_repeate'] == 1 ) {//重复内容开关
				if ($comment > 1) {//如果有重复的内容则返回失败
					$this->rollback ();
					return $result = 501;
				}else {
					$this->create ( $param ); // 写入数据
				}
			}else if ($systemInfo [0] ['huitie_repeate'] == 0){
				$this->create ( $param ); // 写入数据
			}
			if ($AscCount < $systemInfo [0] ['huitie_num']) { //前几次发的帖子有奖励
				if ($user ['vip_time']  > time ()) {//如果会员没过期会有额外奖励
					$data['gold']=$data['gold']+$systemInfo [0] ['huitie_vip_gold'];
					$data['integral']=$data['integral']+$systemInfo [0] ['huitie_vip_sliver'];
				}
				if (User_user::where ( 'id', $param ['user_id'] )->update ( $data )) {
					$gdetail=array(//金币账单详情
							'当前金币' => $user ['gold']+$user['bank'],
							'奖励金币' =>(int)$systemInfo [0] ['huitie_gold'],
							'结果' =>'+'.$systemInfo [0] ['huitie_gold'],
							'剩余金币' => $data['gold']+$user['bank']
					);
					$gold=array(//金币账单
							'username' => $user ['username'], // 用户账号
							'operation' =>'回帖奖励金币', // 操作说明
							'detail' =>json_encode($gdetail), // 账单详情
							// 发帖奖励积分
							'time' => time()
					);
					$iintegral=array(//积分账单详情
							'当前积分' => $user ['integral'],
							'奖励积分' =>(int)$systemInfo [0] ['huitie_sliver'],
							'结果' =>'+'.$systemInfo [0] ['huitie_sliver'],
							'剩余积分' => $data['integral']
					);
					$integral=array(//积分账单
							'username' => $user ['username'], // 用户账号
							'operation' =>'回帖奖励积分', // 操作说明
							'detail' =>json_encode($iintegral), // 账单详情
							// 发帖奖励积分
							'time' => time()
					);
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
				$this->commit ();//不再奖励帖数内，则只添加帖数
				return $result = 200;
			}
		} catch ( Exception $e ) {
			$this->rollback ();
			return $result = 500;
		}
	}
	/**
	 * 根据用户id查询最新的评论
	 */
	function LastDesc($param) {
		return $this->where( 'user_id', $param ['user_id'] )->order ( 'addtime desc' )->field('addtime')->find ();
	}
	/**
	 * 发帖总次数，前几次有奖励
	 */
	function LastsAscCount($param) {
		return $this->where ( 'user_id', $param ['user_id'] )->count ();
	}
}