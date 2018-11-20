<?php 
namespace app\Home\controller;
use think\Controller;
use think\facade\Session;
use think\facade\Request;
use think\Db;
use think\facade\Cookie;

class Medal extends Controller{     
	// 展示勋章        
	public function index(){ 
		$request = Request::param();
		
			$userid = session::get('id')?session::get('id'):11;
			$medal = Db::name('system_ranks')->select();
			//写勋章获取规则
			$userMedalId= Db::name('user_medal')->where(['user_id'=>$userid])->column('medal_id');
			$wearId = Db::name('user_medal')->where(['user_id'=>$userid,'status'=>1])->column('medal_id');
			foreach($medal as $key=>$vo){
				 $medal[$key]['rule'] = "勋章获取的最低积分为:".$medal[$key]['min']."最高积分为:".$medal[$key]['max'];
				 unset($medal[$key]['min']);
				 unset($medal[$key]['max']);
				 if(in_array($vo['id'], $userMedalId)){
				 	$medal[$key]['status'] = 1;
				 }
				 else{
				 	$medal[$key]['status'] = 0;
				 }
				 if(in_array($vo['id'], $wearId)){
				 	$medal[$key]['wear'] = 1;
				 }
				 else{
				 	$medal[$key]['wear'] = 0;
				 }
			}

		if(!$request){
				$data = [
					'medal'=>$medal
				];
				echo json_encode($data);
				return;
		
		}

		else{
			$wearId = $request;
			// 启动事务
			$res = 1;
			Db::startTrans();
			try {
				Db::name('user_medal')->where('medal_id','in',$wearId)->where('user_id',$userid)->update(['status'=>1]);
				Db::name('user_medal')->where('medal_id','notin',$wearId)->where('user_id',$userid)->update(['status'=>0]);
			    // 提交事务
			    Db::commit();
			} catch (\Exception $e) {
			    // 回滚事务
			    $res = 0;
			    Db::rollback();
			}

			if($res){
				echo '{"code":1}';
				return;
			}
			else {
				echo '{"code":0}';
				return;
			}
		}
	} 
}

?>