<?php
namespace app\index\controller;
use app\index\controller;
use think\Db;
use think\facade\Session;
use think\facade\Config;
class Online extends Common
{
	
    public function index(){
        if($this->request->isPost()){
            $param = $this->request->param();
            $param['online_time_start'] = strtotime($param['online_time_start']);
            $param['online_time_end'] = strtotime($param['online_time_end']);
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
        $info = DB::name('system_info')->field('id,online_count,online_vister,online_nickname,online_man,online_munite,online_gold,online_sliver,online_bookmark,online_time_start,online_time_end')->find();
        $this->assign([
            'title'=>'在线设置',
            'back_url'=>url('Index/Index/index'),
            'info'=>$info,
        ]);
        return $this->fetch();
    }

}