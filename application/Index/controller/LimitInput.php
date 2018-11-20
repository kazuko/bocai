<?php
namespace app\index\controller;
use app\index\controller;
use think\Db;
use think\facade\Session;
use think\facade\Config;
class LimitInput extends Common
{
	
    public function index(){
        // dump(time());die;
        if($this->request->isPost()){
            $param = $this->request->param();
            if($param['id']){
                if(DB::name('system_info')->update($param)){
                    $result = [
                        'code'=>0,
                        'msg'=>'保存成功！',
                    ];
                }else{
                    $result = [
                        'code'=>-1,
                        'msg'=>'保存失败！',
                    ];
                }
            }else{
                if(DB::name('system_info')->insert($param)){
                    $result = [
                        'code'=>0,
                        'msg'=>'保存成功！',
                    ];
                }else{
                    $result = [
                        'code'=>-1,
                        'msg'=>'保存失败！',
                    ];
                }
            }
            echo json_encode($result);
            return;
        }
        $info = DB::name('system_info')->field('id,filter_mod,filter_replace,filter_words,filter_input,filter_show,filter_tips,filter_allow,filter_defriend,defriend_mod,filter_notice')->find();
        $this->assign([
            'title'=>'输入限制',
            'back_url'=>url('Index/Index/index'),
            'info'=>$info,
        ]);
        return $this->fetch();
    }
}