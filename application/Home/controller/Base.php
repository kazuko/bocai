<?php 
namespace app\Home\controller;
use think\Controller;
use think\facade\Session;
use think\facade\Cookie;
use think\Db;
class Base extends Controller{
	// 初始化函数，检测用户是否有登录
 //    // $_SESSION['id']与Session::set('id',)设置的结果是$_SESSION['think']['id']
    public	function initialize(){
 //    	$time = time();

 //        //用户没有点击退出登录时进行登录时长判断
 //        if(session::has('logintime')){
 //            //设置cookie记录访问时间
 //            if(cookie::has('bc_active_time')){
 //                /** 1 记录活动时长 time()-cookie::get('bc_vie_time')的值
 //                **  2 把活动时间累计到数据库中
 //                **  3 重新更新cookie的值为当前时间并且更新cookie的有效时间
 //                */
        //用户没有点击退出登录时进行登录时长判断
        if(session::has('logintime')){
            //设置cookie记录访问时间
            if(cookie::has('bocai_active_time')){
                /** 1 记录活动时长 time()-cookie::get('bocai_vie_time')的值
                **  2 把活动时间累计到数据库中
                **  3 重新更新cookie的值为当前时间并且更新cookie的有效时间
                */
 //        //用户没有点击退出登录时进行登录时长判断
 //        if(session::has('logintime')){
 //            //设置cookie记录访问时间
 //            if(cookie::has('bocai_active_time')){
 //                /** 1 记录活动时长 time()-cookie::get('bocai_vie_time')的值
 //                **  2 把活动时间累计到数据库中
 //                **  3 重新更新cookie的值为当前时间并且更新cookie的有效时间
 //                */

 //                //记录新的时间长度
 //                $newlong = time() - cookie::get('bc_active_time');
                //记录新的时间长度
                $newlong = time() - cookie::get('bocai_active_time');
 //                //记录新的时间长度
 //                $newlong = time() - cookie::get('bocai_active_time');


 //                //充值cookie活动间隔
 //                cookie::set('bc_active_time',time(),60*30);
                //充值cookie活动间隔
                cookie::set('bocai_active_time',time(),60*30);
 //                //充值cookie活动间隔
 //                cookie::set('bocai_active_time',time(),60*30);

 //                //已有的时长+新的活动时长添加到数据库中；
 //                $res = Db::name('user_user')->where(['id'=>session::get('id')])->find();
 //                $oldlong =  $res['online'];
 //                $online = $oldlong + $newlong;
 //                $res=Db::name('user_user')->where(['id'=>session::get('id')])->update(['online'=>$online]);
 //                Db::name('user_user')->where(['id'=>session::get('id')])->update(['lasttime'=>time()]);
 //            }
 //            else{
 //                 $res = Db::name('user_user')->field('seven')->where(['id'=>session::get('id')])->find();
 //                 //用户设置了7天免登陆
 //                 if($res['seven']){
 //                     $logintime = session::get('logintime')+(3600*24*7);
 //                    //设定登录有效时间限制为7天 超出时长销毁用户登录信息并跳转到登录界面
 //                    if($logintime<=$time){
 //                        session::delete('id');
 //                        session::delete('username');
 //                        session::delete('logintime');
 //                        cookie::set('bc_active_time',time(),0);
 //                        $this->success('登录时间过长，请重新登录',url('Home/Login/login'));
 //                    }
 //                    else{
 //                        //用户有效活动时间间隔最长为30分钟
 //                        cookie::set('bc_active_time',time(),60*30);
 //                        self::initialize();
 //                    }
 //                 }
 //                 else{
 //                    $this->success('长时间没活动，请重新登录',url('Home/Login/login'));
 //                 }
 //            }

 //        }
 //        else{
 //            return $this->redirect('Home/Login/login');
 //        }
	// }

    //充值金币触发添加冲击记录及金币统计事件
    public function tradingactive($money=0,$gold=0){
        //添加充值记录
        $trading['money'] = $money;
        $trading['gold'] = $gold;
        $trading['user_id'] = session::get('id');
        $trading['time'] = time();
        $res = Db::name('user_trading')->insert($trading);
        //累计用户充值金额及更新用户金币
        if($res){
            $trading = Db::name('user_user')->where(['id'=>session::get('id')])->find();
            $money = $money+$trading['trading'];
            $gold = $gold + $trading['property'];
            $info = Db::name('user_user')->where(['id'=>session::get('id')])->update(['trading'=>$money,'property'=>$gold]);
            if(!$info){
                // Db::name('trading')->where('id'=>$res)->delete();
                $message['first'] = 0;
                $message['tips'] = '充值失败';
                return json_encode($message);
                //已有的时长+新的活动时长添加到数据库中；
                $res = Db::name('user')->where(['id'=>session::get('id')])->find();
                $oldlong =  $res['online'];
                $online = $oldlong + $newlong;
                $res=Db::name('user')->where(['id'=>session::get('id')])->update(['online'=>$online]);
                Db::name('user')->where(['id'=>session::get('id')])->update(['lasttime'=>time()]);
 //                //已有的时长+新的活动时长添加到数据库中；
 //                $res = Db::name('user_user')->where(['id'=>session::get('id')])->find();
 //                $oldlong =  $res['online'];
 //                $online = $oldlong + $newlong;
 //                $res=Db::name('user_user')->where(['id'=>session::get('id')])->update(['online'=>$online]);
 //                Db::name('user_user')->where(['id'=>session::get('id')])->update(['lasttime'=>time()]);
 //            }
 //            else{
 //                 $res = Db::name('user_user')->field('seven')->where(['id'=>session::get('id')])->find();
 //                 //用户设置了7天免登陆
 //                 if($res['seven']){
 //                     $logintime = session::get('logintime')+(3600*24*7);
 //                    //设定登录有效时间限制为7天 超出时长销毁用户登录信息并跳转到登录界面
 //                    if($logintime<=$time){
 //                        session::delete('id');
 //                        session::delete('username');
 //                        session::delete('logintime');
 //                        cookie::set('bocai_active_time',time(),0);
 //                        $this->success('登录时间过长，请重新登录',url('Home/Login/login'));
 //                    }
 //                    else{
 //                        //用户有效活动时间间隔最长为30分钟
 //                        cookie::set('bocai_active_time',time(),60*30);
 //                        self::initialize();
 //                    }
 //                 }
 //                 else{
 //                    $this->success('长时间没活动，请重新登录',url('Home/Login/login'));
 //                 }
 //            }

 //        }
 //        else{
 //            return $this->redirect('Home/Login/login');
 //        }
	// }

    //充值金币触发添加冲击记录及金币统计事件
    public function tradingactive($money=0,$gold=0){
        //添加充值记录
        $trading['money'] = $money;
        $trading['gold'] = $gold;
        $trading['user_id'] = session::get('id');
        $trading['time'] = time();
        $res = Db::name('user_trading')->insert($trading);
        //累计用户充值金额及更新用户金币
        if($res){
            $trading = Db::name('user_user')->where(['id'=>session::get('id')])->find();
            $money = $money+$trading['trading'];
            $gold = $gold + $trading['property'];
            $info = Db::name('user_user')->where(['id'=>session::get('id')])->update(['trading'=>$money,'property'=>$gold]);
            if(!$info){
                // Db::name('trading')->where('id'=>$res)->delete();
                $message['first'] = 0;
                $message['tips'] = '充值失败';
                return json_encode($message);
            }
        }
        //充值成功统计用户金币等级
        $res = Db::name('user_user')->where(['id'=>session::get('id')])->find();
        if($res){
            self::checktrading($res['trading']);
        }
    }

    //用户活动触发积分统计事件
    public function integralactive($weight=0){
        $res = Db::name('user_user')->where(['id'=>session::get('id')])->find();
        if(!$res){
            $message['first'] = 99;
            $message['tips']  ="系统出错，找不到用户";
            return $message;
        }

        //更新用户积分
        $res['integral'] = $res['integral'] + $weight;
        $info = Db::name('user_user')->where(['id'=>session::get('id')])->update($res);
        if(!$info){
            $message['first'] = 0;
            $message['tips'] ='积分增加失败';
            return $message;
        }

        //更新成功进行积分检测
        $res = Db::name('user_user')->where(['id'=>session::get('id')])->find();
        if($res){
            self::checkintegral($res['integral']);
        }

    }

    //根据session::get('id')检测用户的积分授予用户积分勋章
    //此函数用于所有前台发评论，签到，收藏，发大喇叭等一系列触发积分增加按钮ajax
    public function checkintegral($integral=0){
        $rank = Db::name('system_ranks')->where([['min','<=',$integral],['flag','=',0]])->select(); 
        //查找用户已有的勋章
        $medal = Db::name('user_medal')->where('user_id','=',session::get('id'))->select();

        foreach($medal as $key=>$vo){
            $oldmedal[$key]=$vo['medal_id'];
        }
        //$sub返回去勋章的下标
        $sub=0;

        foreach($rank as $key=>$vo){
            //检测用户已有的勋章与解锁的勋章是否有不同
            //解锁新勋章：1 把新勋章id存进用户勋章表并默认不展示，（测试阶段默认展示） 2 把新勋章的信息返回前台
            //没解锁新勋章： 返回充值成功；
            if(!in_array($vo['id'],$oldmedal)){
                $tmp['medal_id'] = $vo['id'];
                $tmp['user_id'] = session::get('id');
                $tmp['status'] = 0;
                $res = Db::name('user_medal')->field('medal_id,user_id,status')->insert($tmp);
                // 勋章解锁失败
                if(!$res){
                    $message['first'] = 2;
                    $message['tips'] = '积分增加成功，解锁勋章出错';
                    //查询充值完毕后的总资产 property
                    $res = Db::name('user_user')->where(['id'=>session::get('id')])->find();
                    $message['integral'] = $res['integral'];
                }
                // 勋章解锁成功
                else{
                  //成一个数组
                  $message['first'][$sub] = $vo;
                  $sub++;
                  $res = Db::name('user_user')->where(['id'=>session::get('id')])->find();
                  $message['integral'] = $res['integral'];    
                }

            }
        }
        //假如没有解锁新的勋章，仅仅提示积分增加成功
        if(!isset($message)){
            $message['first']=1;
            $message['tips'] ="积分增加成功";
            $res = Db::name('user_user')->where(['id'=>session::get('id')])->find();
            $message['integral'] = $res['integral'];

        }
        $message = json_encode($message);
        echo $message;
        // 0 充值失败 1 积分增加成功但没有触发新勋章事件 2 积分增加成功触发解锁新勋章但是系统解锁出错 3解锁成功
        return;
    }
    //根据session::get('id')检测用户的积分授予用户金币勋章
    //根据用户的充值历史总额判断用户可以拥有的勋章
    public function checktrading($trading=0){
        //查找用户可以解锁的勋章
        $rank = Db::name('system_ranks')->where([['min','<=',$trading],['flag','=',1]])->select(); 
        //查找用户已有的勋章
        $medal = Db::name('user_medal')->where('user_id','=',session::get('id'))->select();

        foreach($medal as $key=>$vo){
            $oldmedal[$key]=$vo['medal_id'];
        }

        // 返回去的勋章的下标
        $sub = 0;
        foreach($rank as $key=>$vo){
            //检测用户已有的勋章与解锁的勋章是否有不同
            //解锁新勋章：1 把新勋章id存进用户勋章表并默认不展示，（测试阶段默认展示） 2 把新勋章的信息返回前台
            //没解锁新勋章： 返回充值成功；
            if(!in_array($vo['id'],$oldmedal)){
                $tmp['medal_id'] = $vo['id'];
                $tmp['user_id'] = session::get('id');
                $tmp['status'] = 0;
                $res = Db::name('user_medal')->insert($tmp);
                // 勋章解锁失败
                if(!$res){
                    $message['first'] = 2;
                    $message['tips'] = '充值成功，解锁勋章出错';
                    //查询充值完毕后的总资产 property
                    $res = Db::name('user_user')->where(['id'=>session::get('id')])->find();
                    $message['property'] = $res['property'];
                }
                // 勋章解锁成功
                else{
                  //成一个数组
                  $message['first'][$sub] = $vo;
                  $sub++;
                  $res = Db::name('user_user')->where(['id'=>session::get('id')])->find();
                  $message['property'] = $res['property'];  
                }

            }
        }
        //假如没有解锁新的勋章，仅仅提示充值成功
        if(!isset($message)){
            $message['first']=1;
            $message['tips'] ="充值成功";
            $res = Db::name('user_user')->where(['id'=>session::get('id')])->find();
            $message['property'] = $res['property'];

        }
        $message = json_encode($message);
        echo $message;
        // 0 充值失败 1 充值成功但没有触发新勋章事件 2 充值成功触发解锁新勋章但是系统解锁出错 3解锁成功
        return;
    }


}

 ?>