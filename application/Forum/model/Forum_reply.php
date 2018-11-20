<?php
namespace app\forum\model;
use think\Model;

/**
* 
*/
class Forum_reply extends Model
{
	// protected $table = "bc_Forum_reply";
	function add($param,$systemInfo,$user) {
		$param ['addtime'] = time(); // 时间
		$param ['post_id']='';
		$param ['comment_id']=$param ['id'];
		$param ['id']='';
		$comment=$this->where([ //判断是否有重复的内容
				'user_id' => $param ['user_id'],
				'comment_id' => $param ['id'],
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
					$this->commit ();//不再奖励帖数内，则只添加帖数
					return $result = 200;
				}
			}else if ($systemInfo [0] ['huitie_repeate'] == 0){
				$this->create ( $param ); // 写入数据
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
	function replyinfo($param) {
		return $this->where( 'user_id', $param ['user_id'] )->order ( 'addtime desc' )->find ();
	}
}