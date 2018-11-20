<?php 
namespace app\Home\Controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\facade\Cookie;

class Active extends base{
	public function index(){
		$user = Db::name('user_user')->where(['id'=>session::get('id')])->find();
		$usermedal = Db::name('user_medal')->where(['user_id'=>session::get('id')])->select();
		foreach($usermedal as $key=>$vo){
			$medal[$key] = Db::name('system_ranks')->where(['id'=>$vo['medal_id']])->find();
		}
		$this->assign('medal',$medal);
		$this->assign('user',$user);
		return $this->fetch();
	}
}


 ?>