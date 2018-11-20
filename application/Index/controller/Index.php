<?php
namespace app\index\controller;
use app\index\controller;
use think\Db;
use think\facade\Session;
use think\facade\Config;
class Index extends Common
{
    public function index()
    {
    	$this->assign([
            'title'=>'欢迎'.Session::get('admin.account'),
            'back_url'=>'',
        ]);
    	return $this->fetch();
    }
}
