<?php 
namespace app\Home\Controller;
use think\Controller;

class Friends extends Base{
	public function index(){
		return $this->fetch();
	}

}


 ?>