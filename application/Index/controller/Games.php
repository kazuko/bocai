<?php
namespace app\Index\controller;
use think\Db;
use app\Index\controller;
use app\MyCommon\controller\Gameapi;
/**
 * 
 */
class Games extends Common
{
	public function games(){
		// echo strtotime("2018-08-21 6:6:5");die;
		$this->assign([
			'title'=>'游戏管理',
			'back_url'=>url('Index/Index/index'),
		]);
		return $this->fetch();
	}

	public function bjle(){
		$this->assign([
			'title'=>'百家乐赔率设置',
			'back_url'=>url('Index/Games/games'),
		]);
		return $this->fetch();
	}

	public function sg(){
		if($this->request->isPost()){
			$param = $this->request->param();
			$result = [
				'code'=>0,
				'msg'=>'保存成功！'
			];
			DB::startTrans();
			try {
				foreach ($param as $key => $v) {
					$id = explode('_', $key);
					$data['id'] = $id[1];
					$data['odds'] = $v;
					DB::name("games_sg")->update($data);
				}
				DB::commit();
			} catch (\Exception $e) {
				$result = [
					'code'=>-1,
					'msg'=>'保存失败！'
				];
				DB::rollback();
			}
			echo json_encode($result);
			exit();
		}
		$infos = DB::name('games_sg')->select();
		$list = [];
		foreach ($infos as $key => $v) {
			$list[$v['type'].$v['result']]['id'] = $v['id'];
			$list[$v['type'].$v['result']]['odds'] = $v['odds'];
		}
		$this->assign([
			'title'=>'三公赔率设置',
			'back_url'=>url('Index/Games/games'),
			'list'=>$list,
		]);
		return $this->fetch();
	}

	public function bjPK10(){
		$this->assign([
			'title'=>'北京赛车PK10',
			'back_url'=>url('Index/Games/games'),
		]);
		return $this->fetch();
	}
	public function ffc(){
		$this->assign([
			'title'=>'分分彩',
			'back_url'=>url('Index/Games/games'),
		]);
		return $this->fetch();
	}


	// 广东11选5
	public function gd11x5(){
		$info = DB::name('games_gd11x5')->order('type asc,sort asc,number asc')->select();
		$list = [];
		foreach ($info as $key => $val) {
			$index = $val['type'].'_'.$val['sort'].'_'.$val['number'];
			switch ($val['type']) {
				case 1:
					$list['twoface'][$index]['odds'] = $val['odds'];
					$list['twoface'][$index]['id'] = $val['id'];
					break;
				case 2:
					$list['onecode'][$index]['odds'] = $val['odds'];
					$list['onecode'][$index]['id'] = $val['id'];
					$data['y'] = [1,2,3,4,5,6,7,8,9,10,11];
					$data['x'] = [1,2,3,4,5];
					$this->assign('data',$data);
					break;
				case 3:
					if(!isset($list['everything']['3_'.$val['sort']])){
						$list['everything']['3_'.$val['sort']] = $val['odds'];
					}
					break;
				case 4:
					if(!isset($list['group']['4_'.$val['sort']])){
						$list['group']['4_'.$val['sort']] = $val['odds'];
					}
					break;
				case 5:
					if(!isset($list['direct']['5_'.$val['sort']])){
						$list['direct']['5_'.$val['sort']] = $val['odds'];
					}
					break;
			}
		}
		// dump($list);die;
		$this->assign([
			'title'=>'广东11选5赔率设置',
			'back_url'=>url('Index/Games/games'),
			'list'=>$list,
		]);
		return $this->fetch();
	}

	public function gd11x5DataDeal(){
		if($this->request->isPost()){
			$param = $this->request->param();
			$result = [
				'code'=>0,
				'msg'=>'保存成功！'
			];
			DB::startTrans();
			try {
				dump($param);
				foreach ($param as $key => $value) {
					$data['odds'] = $value;
					$id = explode('_', $key);
					if($id[2]=='group'){
						DB::name('games_gdodds')->where(['type'=>$id[0],'sort'=>$id[1]])->update($data);
					}else{
						$data['id'] = $id[0];
						DB::name('games_gdodds')->update($data);
					}
				}
				DB::commit();
			} catch (\Exception $e) {
				$result = [
					'code'=>-1,
					'msg'=>'保存失败！'
				];
				DB::rollback();
			}
		}else{
			$result = [
				'code'=>404,
				'msg'=>NULL
			];
		}
		echo json_encode($result);
	}


	public function jsk3(){
		$this->assign([
			'title'=>'江苏快3',
			'back_url'=>url('Index/Games/games'),
		]);
		return $this->fetch();
	}
	public function xglhc(){
		$this->assign([
			'title'=>'香港六合彩',
			'back_url'=>url('Index/Games/games'),
		]);
		return $this->fetch();
	}
	public function cqssc(){
		$this->assign([
			'title'=>'重庆时时彩',
			'back_url'=>url('Index/Games/games'),
		]);
		return $this->fetch();
	}


	public function zqc(){
		$this->assign([
			'title'=>'足球彩',
			'back_url'=>url('Index/Games/games'),
		]);
		return $this->fetch();
	}

	public function lowerLimit(){
		if($this->request->isPost()){
			$param = $this->request->param();
			// dump($param);die;
			$result = [
				'code'=>0,
				'msg'=>'保存成功！'
			];
			DB::startTrans();
			try {
				foreach ($param as $key => $value) {
					$index = explode('_', $key);
					if($index[0]=='gold'){
						$data['gold'] = $value;
						$data['sliver'] = $param['sliver_'.$index[1]];
						$data['id'] = $index[1];
						DB::name('games_lower_limit')->update($data);				
					}
				}
				DB::commit();
			} catch (\Exception $e) {
				$result = [
					'code'=>-1,
					'msg'=>'保存失败！'
				];
				DB::rollback();
			}
			echo json_encode($result);
			exit();
		}
		$infos = DB::name('games_lower_limit')->select();
		foreach ($infos as $key => $value) {
			$list['game_'.$value['type']]['sliver'] = $value['sliver'];
			$list['game_'.$value['type']]['gold'] = $value['gold'];
			$list['game_'.$value['type']]['id'] = $value['id'];
		}
		$this->assign([
			'title'=>'下限设置',
			'back_url'=>url('Index/Games/games'),
			'list'=>$list,
		]);
		return $this->fetch();
	}
}