<?php
namespace app\index\controller;
use app\index\controller;
use think\Db;
use think\facade\Session;
use think\facade\Config;
class Computer extends Common
{
    public function index(){
        $param = $this->request->param();
        $ipList = DB::name('system_ips')->select();
        $bai_ming_dan_ip = [];
        $hei_ming_dan_ip = [];
        $ips_list = '[';
        foreach ($ipList as $key => $value) {
            $ips_list .= $value['ip'].'-'.$value['status'].'-'.$value['id'].'][';
            $value['ip'] = explode('.', $value['ip']);
            if($value['status']==1){
                $bai_ming_dan_ip[] = $value;
            }else{
                $hei_ming_dan_ip[] = $value;
            }
        }
        $ips_list .= ']';
        Session::set('ips_list',$ips_list);
        $error_content = DB::name('system_info')->field(['id','error_str'])->find();
        $this->assign([
            'title'=>'IP地址管理',
            'bai_ming_dan_ip'=>$bai_ming_dan_ip,
            'hei_ming_dan_ip'=>$hei_ming_dan_ip,
            'error_content'=>$error_content,
            'back_url'=>url('Index/Index/index')
        ]);
        return $this->fetch();
    }   
    // 修改电脑访问时的提示信息
    public function saveError(){
        $param = $this->request->param();
        $result = [
            'code'=>404,
            'msg'=>NULL
        ];
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
                    'code'=>-2,
                    'msg'=>'保存失败！'
                ];
            }
        }
        echo json_encode($result);
    }

    // 添加ip（黑|白）名单
    public function add(){
        $param = $this->request->param();
        $data['ip'] = $param['ip0'].'.'.$param['ip1'].'.'.$param['ip2'].'.'.$param['ip3'];
        $data['status'] = $param['status'];
        $ipList = Session::get('ips_list');
        $ipStr = '['.$data['ip'].'-'.$data['status'].'-';
        $result = [
            'code'=>404,
            'msg'=>NULL
        ];
        if(stripos($ipList, $ipStr)!==false){
            $result = [
                'code'=>303,
                'msg'=>'该IP已经存在列表中',
            ];
        }else{
            if($id = DB::name('system_ips')->insertGetId($data)){
                $param['ip'] = $data['ip'];
                $param['id'] = $id;
                Session::set('ips_list',$ipList.'['.$ipStr.$id.']');
                $result = [
                    'code'=>0,
                    'msg'=>'添加成功！',
                    'data'=>$param
                ];
            }else{
                $result = [
                    'code'=>-1,
                    'msg'=>'添加失败！',
                ];
            }
        }
        echo json_encode($result);
    }
    // 删除IP名单
    public function delete(){
        $param = $this->request->param();
        $result = [
            'code'=>404,
            'msg'=>NULL
        ];
        $ipStr = "[".$param['ip']."-".$param['status']."-".$param['id']."]";
        $ipList = Session::get('ips_list');
        if(stripos($ipList, $ipStr)===false){
            // Session::set('tipsStr','该IP不存在！');
            $result = [
                'code'=>-1,
                'msg'=>'该IP不存在！',
            ];
        }else{
            if(DB::name('system_ips')->where(['id'=>$param['id']])->delete()){
                // Session::set('tipsStr','删除成功！');
                $ipList = str_replace($ipStr, '', $ipList);
                Session::set('ips_list',$ipList);
                $result = [
                    'code'=>0,
                    'msg'=>'删除成功！'
                ];
            }else{
                // Session::set('tipsStr','删除失败！');
                $result = [
                    'code'=>-2,
                    'msg'=>'删除失败！'
                ];
            }
        }
        echo json_encode($result);
    }

}