<?php
namespace app\Home\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
  // index()数据的展示；
class Index extends Controller
{
    public function index()
    {
        // if(session::has('id')){
        //     $userid = session::get('id');
        // }
        //获取用户基本信息
        $userid = 11;
        $user = Db::name('user_user')->field('property,integral')->where('id',$userid)->find();

        //获取用户勋章
        $usermedal = Db::name('user_medal')->where(['user_id'=>$user['id'],'status'=>1])->limit(5)->select();
        foreach($usermedal as $key=>$vo){
          $medal[$key] = Db::name('system_ranks')->where(['id'=>$vo['medal_id']])->find();
        }

        $friends = Db::name('user_friends')->where('id',$userid)->select();
        //前台单页面渲染数据测试
        $data=[
            'user'=>$user
        ];
        echo json_encode($data);
        return;
        //获取用户好友
       

        //后端数据渲染测试
        // $res = [
        //     'user'=>$user,
        //     'medal'=>$medal,
        //     'friends'=>$friends,
        // ];
        // echo json_encode($res);

        // $this->assign([
        //             'user'  =>$user,    
        //             'medal' =>$medal,
        //             'friends' =>$friends
        //         ]);
        // return $this->fetch('index');
    }
//获取用户的等级
public function rank(){
    
}
//获取用户的头衔

}