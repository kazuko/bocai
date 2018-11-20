<?php
namespace app\index\controller;
use app\index\controller;
use think\Db;
use think\facade\Session;
use think\facade\Config;
class SetCoin extends Common
{

    public function index(){
        if($this->request->isPost()){
            $param = $this->request->param();
            $param['coin1_desc'] = ltrim(ltrim($param['coin1_desc'],'描述：'));
            $param['coin2_desc'] = ltrim(ltrim($param['coin2_desc'],'描述：'));
            if(!empty($param['recharge'])){
                $param['recharge'] = json_encode($param['recharge']);
            }
            // echo json_encode($param);return;
            // return json_encode($param);
            if($param['id']){
                if(DB::name('system_info')->update($param)){
                    $result = [
                        'code'=>0,
                        'msg'=>'保存成功！'
                    ];
                }else{
                    $result = [
                        'code'=>-1,
                        'msg'=>'保存失败！'
                    ];
                }
            }else{
                if(DB::name('system_info')->insert($param)){
                    $result = [
                        'code'=>0,
                        'msg'=>'保存成功！'
                    ];
                }else{
                    $result = [
                        'code'=>-1,
                        'msg'=>'保存失败！'
                    ];
                }
            }
            echo json_encode($result);
            return;
        }
        $info = DB::name('system_info')->field('id,coin1_name,coin1_desc,coin2_name,coin2_desc,coin1_bank_percent,coin1_recharge,recharge,security_key,official_key')->find();
        $info['recharge'] = $info['recharge'] ? json_decode($info['recharge'],true) : [];
        $this->assign([
            'title'=>'货币设置',
            'back_url'=>url('Index/Index/index'),
            'info'=>$info,
        ]);
        return $this->fetch();
    }
}