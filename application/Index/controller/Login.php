<?php
namespace app\index\controller;
use think\Db;
use think\Controller;
use think\facade\Session;
use app\MyCommon\controller\Base;
/**
 * 
 */
class Login extends Controller
{
	private $security_password;
	function __construct(){
		parent::__construct();
		// Base::lock('abcdefghijklmnopqrstuvwxyz','ABCDEFGHIJKLMNOPQRSTUVWXYZ');die;
		// 检查是否符合登陆要求
		$result = Base::check();
		// 如何发生错误则将错误信息输出
		if($result['code']){
			echo json_encode($result);
			return;
		}
		// 保存认证密码
		$this->security_password = $result['security_password'];
	}
	// 获取验证码
	public function vertify(){
		$config =    [
		    // 验证码字体大小
			'fontSize'    =>    30,    
		    // 验证码位数
			'length'      =>    4,   
		    // 关闭验证码杂点
			'useNoise'    =>    false, 
		];
		$captcha = new \think\captcha\Captcha($config);
		return $captcha->entry();
	}
	public function login(){
		// 判断是否登陆
		if($this->request->isPost()){
			$param = $this->request->param();
			// 检测验证码是否正确
			$captcha = new \think\captcha\Captcha();
			if( !$captcha->check($param['checkNum']))
			{
				$result = [
					'code'=>-1,
					'msg'=>'验证码错误',
				];
				echo json_encode($result);
				return;
			}
			// echo json_encode($this->security_password.':'.base64_encode(md5($param['password'])));
			// return;
			// 检测认证码是否正确
			if($param['password'] != $this->security_password){
				$result = [
					'code'=>-2,
					'msg'=>'认证码错误',
				];
				echo json_encode($result);
				return;
			}
			// 判断账号、密码是否正确
			if($admin = DB::name('system_user')->where(['account'=>$param['account'],'pwd'=>md5($param['pwd'])])->find()){
				Session::set('admin',$admin);
				$result = [
					'code'=>0,
					'msg'=>'登陆成功',
				];
			}else{
				$result = [
					'code'=>-3,
					'msg'=>'账号或密码错误',
				];
			}
			echo json_encode($result);
			return;
		}
		// 获取logo路径
		$logo = Db::name('system_info')->field('logo_src')->find();
		$this->assign([
			'web_title'=>'菠菜——登录',
			'logo'=>$logo['logo_src'],
		]);
		return $this->fetch();
	}

	public function logout(){
		// 退出登录，清除session
		Session::delete('admin');
		// 获取logo路径
		$logo = Db::name('system_info')->field('logo_src')->find();
		$this->assign([
			'web_title'=>'菠菜——登录',
			'logo'=>$logo['logo_src'],
		]);
		// 退回登陆页面
		return $this->fetch('login');
	}
}