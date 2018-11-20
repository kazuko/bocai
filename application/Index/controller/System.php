<?php
namespace app\index\controller;
use app\index\controller;
use think\Db;
use think\facade\Session;
use think\facade\Config;
class System extends Common
{
    public function index(){
        // 获取系统设置的配置信息
        $info = DB::name('system_info')->field('id,app_name,web_url,web_status,web_link,web_info,logo_src')->find();
        // 判断是否为更新或者添加
        if($this->request->isPost()){
            $param = $this->request->param();
            // 实例化图标上传对象
            $uploads = new Uploads();
            // 设置图标保存路径
            $path = __ROOT__.DS.'public'.DS.'uploads';
            $uploads->setSavePath($path);
            // 保存图片
            $res = $uploads->uploadImg();
            // 判断图片是否保存成功
            if($res['code']>0&&$res['code']!=404){
                $result = [
                    'errno'=>$res['code'],
                    'errMsg'=>$res['msg']
                ];
                echo json_encode($result);
                return ;
            }else{
                foreach ($res['source'] as $key => $value) {
                    $param[$key] = 'uploads'.DS.$value;
                }
            }
            // 判断执行更新还是添加
            if($param['id']){
                $res1 = DB::name('system_info')->update($param);
            }else{
                $res1 = DB::name('system_info')->insert($param);
            }
            // 判断添加或更新是否成功
            if($res1){
                // 添加或者更新成功，把原来的图片删除
                if(isset($param['logo_src'])&&$info['logo_src']){
                    if(is_file(__ROOT__.DS.'public'.DS.$info['logo_src'])){
                        unlink(__ROOT__.DS.'public'.DS.$info['logo_src']);
                    }
                }
                $result = [
                    'errno'=>0,
                    'msg'=>'ok'
                ];
            }else{
                // 添加或者更新失败，删除刚刚上传的所有图片
                foreach ($res['source'] as $key => $value) {
                    if(is_file($path.DS.$value)){
                        unlink($path.DS.$value);
                    }
                }
                $result = [
                    'errno'=>-1,
                    'msg'=>'add fail!'
                ];
            }
            echo json_encode($result);
            exit();
        }
        if(!$info){
            $info = [
                'id'=>'',
                'app_name'=>'',
                'web_info'=>'',
                'web_url'=>'',
                'web_status'=>1,
                'web_link'=>0,
            ];
        }
        $this->assign([
            'title'=>'网站设置',
            'info'=>$info,
            'back_url'=>url('Index/Index/index')
        ]);
        return $this->fetch();
    }

    // 消息管理
    public function news(){
        // 获取系统消息
        $news = DB::name('system_news')->order('id asc')->select();
        foreach ($news as $key => $value) {
            $system_news[$value['id']] = [
                'status'=>$value['status'],
                'content'=>$value['content']
            ]; 
        }
        // 获取用户喇叭消息停留最长时间
        $laba = DB::name('system_info')->field('id,laba_time')->find();
        if(!$laba){
            $laba = [
                'id' => '',
                'laba_time'=>0,
            ];
        }
        $newss = [
            'system_news'=>$system_news,
            'user_laba'=>$laba
        ];
        // 保存到session中，方便对比
        Session::set('allnewsini',$newss);
        $this->assign([
            'title'=>'消息系统',
            'back_url'=>url('Index/Index/index'),
            'news'=>$news,
            'laba'=>$laba
        ]);
        return $this->fetch();
    }
    // 更新系统消息
    public function dealNews(){
        $param = $this->request->param();
        // 获取更新前保存的数据
        $news = Session::get('allnewsini.system_news');
        $laba = Session::get('allnewsini.user_laba');

        foreach ($param['id'] as $key => $value) {
            // 判断是为更新还是新增 ,id存在则进行更新
            if($value){
                // 判断当前系统消息是否显示
                if(isset($param['status'])&&in_array($value, $param['status'])){
                    $status = 1;
                }else{
                    $status = 0;
                }
                // 判断内容和状态是否发生改变
                if($param['news'.$key]!=$news[$value]['content']&&$status!=$news[$value]['status']){
                    $upData[] = [
                        'id'=>$value,
                        'status'=>$status,
                        'content'=>$param['news'.$key],
                        'time'=>time(),
                    ];
                // 只有状态发生改变
                }elseif($status!=$news[$value]['status']){
                    $upData[] = [
                        'id'=>$value,
                        'status'=>$status,
                        'time'=>time(),
                    ];
                // 只有内容发生改变
                }elseif($param['news'.$key]!=$news[$value]['content']){
                    $upData[] = [
                        'id'=>$value,
                        'content'=>$param['news'.$key],
                        'time'=>time(),
                    ];
                }
            // 判断是否存在新增内容
            }else if($param['news'.$key]){
                // 判断新增内容是否显示
                if(in_array('on_'.$key, $param['status'])){
                    $status = 1;
                }else{
                    $status = 0;
                }

                $addData[] = [
                    'content'=>$param['news'.$key],
                    'status'=>$status,
                    'time'=>time()
                ];
            }
        }
        $result = [
            'code'=>0,
            'msg'=>'ok',
        ];
        // 启动事务
        Db::startTrans();
        try {
            // 判断是否需要更新数据
            if(isset($upData)&&!empty($upData)){
                foreach ($upData as $key => $value) {
                    DB::name('system_news')->update($value);
                }
            }
            // 判断是否需要新增数据
            if(isset($addData)&&!empty($addData)){
                DB::name('system_news')->insertAll($addData);
            }
            // 判断是否需要更新喇叭信息
            if($param['laba_time'] && ((int)$param['laba_time']!=(int)$laba['laba_time'])){
                $laba['laba_time'] = $param['laba_time'];
                $laba['id'] = $param['laba_id'];
                if($laba['id']){
                    DB::name('system_info')->update($laba);
                }else{
                    DB::name('system_info')->insert($laba);
                }
            }
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 修改数据库失败，事务回滚
            Db::rollback();
            $result = [
                'code'=>-1,
                'msg'=>'保存失败',
            ];
        }
        echo json_encode($result);
    }

    // 删除系统消息
    public function delNews(){
        $id = $this->request->param('id');
        if(DB::name('system_news')->where('id',$id)->delete()){
            $result = [
                'code'=>0,
                'msg'=>'删除成功！'
            ];
        }else{
            $result = [
                'code'=>-1,
                'msg'=>'删除失败！'
            ];
        }
        echo json_encode($result);
    }
}