<?php
namespace app\MyCommon\controller;

use app\MyCommon\controller\Base;
use think\Db;
use think\facade\Session;
use think\facade\Cookie;

class CheckLogin
{
    public static function loginCheck($userIP)
    {
        // 获取错误提示和网站开放时间的设置信息
        $info = DB::name('system_info')
        ->field('error_str,security_time_open,security_time_start,security_time_end,web_status')
        ->find();
        if(!$info['web_status']){
            return [
                'code'=>405,
                'msg'=>'该站点已经关闭'
            ];
        }
        // 判断是否为手机登录
        // if (!Base::isMobile()) {
        //     return [
        //         'code'=>440,
        //         'msg'=>$info['error_str']
        //     ];
        //     // Base::echo_error($info['error_str']); 
        // }
        
        // 判断是否在允许的时间段，如果需要
        if(Base::timeOpen($info)){
            return [
                'code'=>444,
                'msg'=>'当前时间无法访问该网站。'
            ];
            
            // Base::echo_error("当前时间无法访问该网站");
        }
        // 获取用户ip地址
        // $userIP = Base::getIP();
        // 检测是否当前ip是否被列为黑名单
        if(DB::name('system_ips')->where(['status'=>0,'ip'=>$userIP])->find()){
            return [
                'code'=>400,
                'msg'=>'当前IP已被管理员列为IP黑名单'
            ];
        }
        return [
            'code'=>0,
            'msg'=>'ok'
        ];
    }

    /**
     * checkUser 检测用户是否已经登陆
     * @param $uid  可选，用户id，没有的时候回去获取cookie中的uid
     * @return boolean 没登陆返回false，已经登陆返回true
     */
    public static function checkUser($uid='')
    {
        // 判断是否是正常渠道，正常时间段，以及用户IP是否被列为IP黑名单
        $checkResult = self::loginCheck();
        if($checkResult['code']){
            return [
                'result'=>false,
                'code'=>1,
                'msg'=>'该用户IP已被列为黑名单'
            ];
        }
        // 判断请求中是否带有uid过来
        if(!$uid){
            $uid = Cookie::get('uid');
        }
        // 判断有那个湖是否已经登陆
        if(!$uid || !Session::has('userInfo')){
            return [
                'result'=>false,
                'code'=>2,
                'msg'=>'该用户还没登陆'
            ];
        }
        // 获取session中的userid
        $userId = Session::get('userInfo.id');
        // 判断session中的userid是否与传过来的uid是否相等
        if($uid != md5($userId)){
            return [
                'result'=>false,
                'code'=>4,
                'msg'=>'验证失败！'
            ];
        }
        // 检测当前用户是否被查封
        if(DB::name('user_user')->where(['id'=>$userId,'status'=>1])->find()){
            return [
                'result'=>false,
                'code'=>3,
                'msg'=>'该用户已经查封'
            ];
        }
        
        return [
            'result'=>true,
            'code'=>200,
            'msg'=>'验证通过'
        ];
    }
}
