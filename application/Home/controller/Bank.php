<?php 
namespace app\Home\controller;
use think\Controller;
use think\facade\Session;
use think\Db;
use think\facade\Request;
use think\Container;
class Bank extends Controller{
	protected $socket = 'http://0.0.0.0:2000';
	public function onMessage($connection,$data)
	{
		$connection->send(json_encode($data));
	}
	public function index(){	
		$rules = Db::name('system_info')->find();
		$rules = json_encode($rules);
		file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bcweb/bank.json',$rules);
		$strarray = json_decode($rules);
		$request = Request::param();
		$userid = session::get('id');
		// $userid = 17812144;
		$gold = Db::name("user_user")->field('gold,bank')->where(['id'=>$userid])->find();
		if($request){
			$str = "----------------------------------\r\n";
			foreach($request as $key=>$vo){
				$str .= $key.":".$vo."\r\n";
			}
			file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bcweb/bank.json',$str,FILE_APPEND);
			if($request['operationBankType'] == 0)//取金币
			{
				$gold['gold'] = $gold['gold'] + $request['takeout'];
				$gold['bank'] = $gold['bank'] - $request['takeout'];
				$detail = [
					"当前金币" => $gold['gold'] + $request['takeout'],
					"取金币数量" => $request['takeout'],
					"银行存款" => $request['newBank'],
					"金币余额" => $request['newProperty']
				];
				$detail = json_encode($detail);
				$gold_history = array(
					'user_id' => $userid,
					'operation'=>"社区银行",
					'detail' =>$detail,
					'time'=>time()
				);
			}
			else{	 //存金币
				$gold['gold'] = $gold['gold'] - $request['storage'];
				$gold['bank'] = $gold['bank'] + $request['storage'];
				$detail = [
					"当前金币" => $gold['gold'] - $request['storage'],
					"存金币数量" => $request['storage'],
					"银行存款" => $request['newBank'],
					"金币余额" => $request['newProperty']
				];
				$detail = json_encode($detail);
				$gold_history = array(
					'user_id' => $userid,
					'operation'=>"社区银行",
					'detail' =>$detail,
					'time'=>time()
				);
			}
			//入库操作
			$res = Db::name('user_user')->where(['id'=>$userid])->update($gold);
			if($res){
				$resg = Db::name('gold_history')->insert($gold_history);
				if($resg){
					echo '{"code":1}';
				}
				else{
					echo '{"code":0}';
				}
				return;
			}
		}
		else{
			$this->assign('gold',$gold);
			return $this->fetch();
		}

	}
}
 ?>
