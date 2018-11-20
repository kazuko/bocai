<?php
namespace app\index\controller;
use app\index\controller;
use think\Db;
use think\facade\Session;
use think\facade\Config;
class Register extends Common
{
	

    public function index(){

        $info = DB::name('system_info')->field('id,register_prefix,register_start_number,register_mod,register_rules,register_signature,register_keyword,register_max_length,register_welcome,register_gold,register_head,register_integral,register_check_key,register_end_time,register_init_account,register_init_password')->find();
        $this->assign([
            'title'=>'会员注册',
            'back_url'=>url('Index/Index/Index'),
            'info'=>$info,
        ]);
        return $this->fetch();
    }


    public function save(){
        $file = $this->saveImg();
        if($file['code']){
            echo json_encode($file);
            exit();
        }
        $param = $this->request->param();
        $defaultHead = DB::name('system_info')->field('register_head,register_start_number')->find();
        if(isset($file['srcs']['register_head'])&&$file['srcs']['register_head']){
            $head = $file['srcs']['register_head'];
            $param['register_head'] = $head;
        }else{
            $head = $defaultHead['register_head'];
        }
        // 判断是否已经设置过
        if(!$defaultHead['register_start_number']){
            if(!(int)$param['register_start_number']){
                $param['register_start_number'] = 1;
            }
            for($j=$param['register_start_number'],$i=1; $j>0; $j--,$i++){
                $userList[] = [
                    'id'=>$i,
                    'username'=>$param['register_init_account'].$j,
                    'password'=>$param['register_init_password'],
                    'gold'=>$param['register_gold'],
                    'integral'=>$param['register_integral'],
                    'head'=>$head,
                    'nickname'=>$param['register_prefix'].$i,
                    'signature'=>$param['register_signature']
                ];
            }
        }
        $param['register_end_time'] = strtotime($param['register_end_time']);
        $result = [
            'code'=>0,
            'msg'=>'保存成功！',
        ];
        DB::startTrans();
        try {
            if($param['id']){
                DB::name('system_info')->update($param);
            }else{
                DB::name('system_info')->insert($param);
            }
            if(isset($userList)&&$userList){
                DB::name('user_user')->insertAll($userList);
            }
            DB::commit();
        } catch (\Exception $e) {
            $result = [
                'code'=>-2,
                'msg'=>'保存失败！'
            ];
            DB::rollback();
        }
        if(isset($param['register_head'])&&$param['register_head']&&$param['id']&&!$result['code']){
            if(is_file($_SERVER['DOCUMENT_ROOT'].$defaultHead['register_head'])){
                unlink($_SERVER['DOCUMENT_ROOT'].$defaultHead['register_head']);
            }
        }
        echo json_encode($result);
    }

    // 保存勋章图标
    public function saveImg(){
        $files = $this->request->file();
        // 用户存放上传图标列表
        $stemp = [];
        // 判断是否上传图标
        if(!empty($files)){
            // 根目录
            $rootPath = $_SERVER['DOCUMENT_ROOT'];
            // 图标路径
            $savePath = Config::get('template.tpl_replace_string.__PUBLIC__').DS.'uploads'.DS.date('Ymd');
            foreach ($files as $key => $file) {
                if(!is_dir($rootPath.$savePath)){
                    mkdir($rootPath.$savePath,0777,true);
                }
                // 实例化图片上传处理器
                $image = \think\Image::open($file);
                // 产生一个随机数，减少文件重名的几率
                $n = mt_rand();
                $savePath .= DS.md5('xunzhang').time().$n.'.'.$image->type();
                // 保持图片缩略图
                $image->thumb(100,100)->save($rootPath.$savePath);
                // 判断是否保存成功
                if(!is_file($rootPath.$savePath)){
                    // 保存失败，删除刚上传的所有图片
                    foreach ($stemp as $k => $v) {
                        if(is_file($rootPath.$v)){
                            unlink($rootPath.$v);
                        }
                    }
                    return ['code'=>-1,'msg'=>'下标为'.$key.'的图片保存失败'];
                }else{
                    $stemp[$key] = $savePath;
                }
            }
        }
        return ['code'=>0,'srcs'=>$stemp];
    }


}