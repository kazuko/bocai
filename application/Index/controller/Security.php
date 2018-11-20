<?php
namespace app\index\controller;
use app\index\controller;
use think\Db;
use think\facade\Session;
use think\facade\Config;
class Security extends Common
{
	public function index(){
		$info = DB::name('system_info')->field('id,security_ip_open,security_time_open,security_time_start,security_time_end,security_password')->find();
		if($info['security_time_start']){
			$info['security_time_start'] = explode(':', $info['security_time_start']);
		}else{
			$info['security_time_start'] = [0,0,0];
		}
		if($info['security_time_end']){
			$info['security_time_end'] = explode(':', $info['security_time_end']);
		}else{
			$info['security_time_end'] = [0,0,0];
		}
		$this->assign([
			'title'=>'后台安全',
			'back_url'=>url('Index/Index/index'),
			'info'=>$info,
		]);
		return $this->fetch();
	}

	/**
	 * 安全设置
	 */
	public function dataDeal(){
		$param = $this->request->param();
		$data['security_ip_open'] = (int)$param['security_ip_open'];
		$data['security_time_open'] = (int)$param['security_time_open'];
		if($data['security_time_open']){
			$data['security_time_start'] = trim($param['start_hours']).':'.trim($param['start_munite']).':'.trim($param['start_seconds']);
			$data['security_time_end'] = trim($param['end_hours']).':'.trim($param['end_munite']).':'.trim($param['end_seconds']);
		}
		$data['id'] = $param['id'];
		$data['security_password'] = str_replace(' ', '', $param['security_password']);
		if(DB::name('system_info')->update($data)){
			$result = [
				'code' => 0,
				'msg' => '保存成功！',
			];
		}else{
			$result = [
				'code' => -1,
				'msg' => '保存失败！',
			];
		}
		echo json_encode($result);
	}
}