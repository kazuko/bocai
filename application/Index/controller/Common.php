<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\facade\Config;
use think\facade\Session;
use app\MyCommon\controller\Base;
/**
 * 
 */
class Common extends Controller
{
	
	function __construct()
	{
		parent::__construct();
		$result = Base::check();
		// 如何发生错误则将错误信息输出
		if($result['code']){
			Base::echo_error($result['msg']);
		}
		// 判断是否登陆
		if(!Session::has('admin')){
			$this->redirect('Index/Login/login');
		}
		// 获取logo
		$logo = Db::name('system_info')->field('logo_src')->find();
		$this->assign([
			'web_title'=>'管理员后台',
			'logo'=>$logo['logo_src'],
		]);

	}

	
}