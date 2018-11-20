<?php
namespace app\Main\controller;
use think\Controller;
use think\Db;
/**
 * 
 */
class Index extends Controller
{
	public function index(){
		// $data = DB::name('forum_zone')->alias('a')->join('forum_post b','b.zone_id=a.id','left')->order('b.addtime desc')->->select();
		// dump($data);
		return $this->fetch();
	}
}