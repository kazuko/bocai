<?php 
namespace app\Home\controller;
use think\Controller;
use think\facade\Session;
use think\Db;
use think\facade\Request;
class Chat extends Controller{
	public function index(){
		return $this->fetch();
		}
	}
 ?>