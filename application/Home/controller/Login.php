<?php
namespace app\Home\controller;

use think\Controller;
// use think\facade\Session;
use think\facade\Request;
use think\Db;
use think\facade\Cookie;
// use app\Home\controller\SmsDemo;
// use think\db\connector\Mysql;
// use app\Home\controller\Index;

class Login extends Controller
{

    public function __construct(){
        parent::__construct();
        header("Access-Control-Allow-Headers: Content-Type,XFILENAME,XFILECATEGORY,XFILESIZE");
        header('Access-Control-Allow-Origin:*');
    }
    // public function test(){
    //     dump(json_decode('{"type":"AgreeFriendRequest","send_id":11,"nickname":"bc11","head":"/bcweb/public/heads/20180827/400815396b1ed5168b4035d1404e7571.png","friend_id":12,"msg_id":162,"msg_status":4}',true));
    //     die;
    // }
    public $i=0;
    //  login()用户登录 ；
    //  logout()用户退出登录；
    //  register()用户注册函数；
    // 登录模块
    public function login()
    {
        
        // 检测是否为手机登陆和登陆IP地址是在IP黑名单中,检测状态，通过检测，code为0
        $checkResult = CheckLogin::loginCheck();
        if ($checkResult['code']) {
            echo json_encode($checkResult);
            exit();
        }
        // 获取传递过来的账号和密码
        $request = $this->request->param();
        if (!empty($request)) {
            // 判断账号是否为空
            $username = $request['username'];
            if (!$username) {
                echo json_encode([
                    'code'=>1,
                    'msg'=>'账号不能为空',
                ]);
                exit;
            }
            // 判断密码是否为空
            $password = $request['password'];
            if (!$password) {
                echo json_encode([
                    'code'=>2,
                    'msg'=>'密码不能为空',
                ]);
                exit;
            }
            // 获取用户信息
            $user = Db::name('user_user')->where('username|phonenumber', $username)->find();
            // 判断用户是否存在
            if (empty($user)) {
                echo json_encode([
                    'code'=>3,
                    'msg'=>'账号不正确'
                ]);
                exit;
            }
            // 判断用户是否被查封
            if ($user['status']) {
                echo json_encode([
                    'code'=>4,
                    'msg'=>'该账号已经被查封！'
                ]);
                exit;
            }
            // 判断用户是否已经上线
            if ($user['on_status']) {
                echo json_encode([
                    'code'=>5,
                    'msg'=>'该账号已经登陆！'
                ]);
                exit;
            }

            //用户登录成功给前台返回1标志；同时设定登录时间
            if ($user['password']==$password) {
                $logintime = time();
                // 缓存用户信息
                // Session::set('userInfo', $user);
                // 保存加密后的id到客户的cookie中
                Cookie::set('uid', md5($user['id']));
                //更新用户最后登录时间和登陆状态
                $data = [
                    'lasttime'=>$logintime,
                    'on_status'=>1,
                    'id'=>$user['id']
                ];
                Db::name('user_user')->update($data);
                // 获取用户勋章信息
                $usermedal = DB::name('user_medal')
                ->alias('m')
                ->join('bc_system_ranks r', 'r.id=m.medal_id')
                ->where(['m.status'=>1,'m.user_id'=>$user['id']])
                ->limit(5)
                ->column('r.src');
                $user['medals'] = $usermedal;
                
                // 获取app的名称和logo
                // $appInfo = self::appInfo();
                echo json_encode([
                    'code'=>0,
                    'msg'=>'ok',
                    'userInfo'=>$user,
                    // 'sysNews'=>$sysNews,
                    // 'appInfo'=>$appInfo
                ]);
                exit;
            } else {
                echo json_encode([
                    'code'=>6,
                    'msg'=>'密码不正确'
                ]);
                // return 0;
                exit();
            }
        } else {
            echo json_encode([
                'code'=>7,
                'msg'=>'登陆信息不能为空！'
            ]);
            exit;
        }
        // return $this->fetch('login');
    }
    
   
    // public static function
    
    // 获取系广播统消息
    public static function sysNews()
    {
        $news = DB::name('system_news')->where('status', 1)->column('content');
        return $news;
    }
    // 获取app的logo和app名称
    public function appInfo()
    {
        // 获取系统广播消息
        $sysNews = self::sysNews();
        // 获取app信息
        $info = DB::name('system_info')->field('app_name,logo_src,error_str')->find();
        echo json_encode([
            "sysNews"=>$sysNews,
            "appInfo"=>$info
        ]);
    }
    // 获取用户未读消息条数
    public static function getAcceptMsg($uid)
    {
        $messages = DB::name('user_message')->where(['get_id'=>$uid,'flag'=>0])->select();
        $msgs = [
            'system'=>0, //系统消息
        ];
        foreach ($messages as $k => $v) {
            // 判断是否为7天内的系统消息
            if ($v['status'] > 2 && $v['time'] > (time()-7*24*60*60)) {
                $msgs['system']++;
            } elseif ($v['status'] <= 2) {
                $msgs[$v['send_id']][] = $v;
            }
        }
        return $msgs;
    }
 
    //用户好友信息
    public static function friends($userid)
    {
        // 查询好友信息
        $friends = Db::name('user_friends')
        ->alias('f')
        ->join('bc_user_user u', 'u.id=f.friend_id')
        ->field('u.id,u.head,u.nickname,u.username,u.on_status')
        ->where(['f.user_id'=>$userid])
        ->order('u.on_status desc')
        ->select();
        $friendIds = [];
        $friendList = [];
        if (!empty($friends)) {
            foreach ($friends as $key => $vo) {
                if($vo){
                    // 查询好友的勋章信息
                    $usermedal = Db::name('user_medal')
                    ->alias('m')
                    ->join('bc_system_ranks r', 'r.id=m.medal_id')
                    ->where(['m.user_id'=>$vo['id'],'m.status'=>1])
                    ->limit(5)
                    ->column('r.src');
                    $vo['medals'] = $usermedal;
                    $friendList[$vo['id']] = $vo;
                    $friendIds[] = $vo['id'];
                }
            }
        }
        return [
            'friends'=>$friendList,
            'friendIds'=>$friendIds
        ];
    }
    // 获取好友列表
    public static function listInfo($userid)
    {
        // 全部好友信息
        $friendsInfo = self::friends($userid);
        // 查询一个月之内发送过消息给当前用户的陌生人信息
        $date = time()-30*24*60*60;
        $strangeInfo = Db::name("user_message")
        ->alias('m')
        ->join('bc_user_user u', 'u.id=m.send_id or u.id=m.get_id')
        ->field('u.id,u.head,u.nickname,u.username,u.on_status')
        ->where('m.get_id|m.send_id', $userid)
        ->where('m.status', 2)
        ->where('m.time', 'gt', $date)
        ->where('u.id','neq',$userid)
        ->where('m.send_id|m.get_id', 'notin', $friendsInfo['friendIds'])
        ->order('u.on_status,m.time desc')
        ->group('m.send_id')
        ->select();
        // $strangeIds = [];
        $strangeFriends = [];
        if (!empty($strangeInfo)) {
            foreach ($strangeInfo as $key => $val) {
                if ($val) {
                    // 获取陌生人的勋章信息
                    $medals = Db::name('user_medal')
                    ->alias('m')
                    ->join('bc_system_ranks r', 'r.id=m.medal_id')
                    ->where(['m.user_id'=>$val['id'],'m.status'=>1])
                    ->limit(5)
                    ->column('r.src');
                    $val['medals'] = $medals;
                    $strangeFriends[$val['id']] = $val;
                    // $strangeIds[] = $val['id'];
                }
            }
        }
        // 查询一个月之内当前用户发送过消息的陌生人信息
        // $strangeInfo1 = Db::name("user_message")
        // ->alias('m')
        // ->join('bc_user_user u', 'u.id=m.get_id')
        // ->field('u.id,u.head,u.nickname,u.on_status')
        // ->where('m.send_id', $userid)
        // ->where('m.status', 2)
        // ->where('m.time', '>', $date)
        // ->where('m.get_id', 'notin', $friendsInfo['friendIds'])
        // ->where('m.get_id', 'notin', $strangeIds)
        // ->order('u.on_status,m.time desc')
        // ->group('m.get_id')
        // ->select();
        // if (!empty($strangeInfo1)) {
        //     foreach ($strangeInfo1 as $key => $val) {
        //         if ($val) {
        //             // 获取陌生人的勋章信息
        //             $medals = Db::name('user_medal')
        //             ->alias('m')
        //             ->join('bc_system_ranks r', 'r.id=m.medal_id')
        //             ->where(['m.user_id'=>$val['id'],'m.status'=>1])
        //             ->limit(5)
        //             ->column('r.src');
        //             $val['medals'] = $medals;
        //             $strangeFriends[$val['id']] = $val;
        //         }
        //     }
        // }
        // $strangesInfo = array_merge($strangeInfo, $strangeInfo1);
        $system = self::system($userid);
        return [
            'friends'=>$friendsInfo['friends'],
            'strange'=>$strangeFriends,
            'system'=>$system
        ];
    }

    //获取七天内删除和拒绝好友以及添加好友的信息
    public static function system($userid)
    {
        // 获取7天内的系统消息以及相关的用户信息
        $system = Db::name("user_message")
        ->alias('m')
        ->join('user_user u', 'u.id=m.send_id')
        ->field('u.id,u.on_status,u.nickname,u.username,u.head,m.content,m.flag,m.status,m.id as mid')
        ->where('m.get_id', $userid)
        ->where('m.status', 'gt', 2)
        ->where('m.time', '>', (time()-7*24*60*60))
        ->order('m.time desc')
        ->select();
        $systemList = [];
        foreach ($system as $k => $v) {
            if ($v) {
                // 获取勋章信息
                $medals = Db::name('user_medal')
                ->alias('m')
                ->join('bc_system_ranks r', 'r.id=m.medal_id')
                ->where(['m.user_id'=>$v['id'],'m.status'=>1])
                ->limit(5)
                ->column('r.src');
                $v['medals'] = $medals;
                $systemList[$v['id'].'@'.$k] = $v;
            }
        }
        return $systemList;
    }
    //系统规则
    public function rule()
    {
        //系统规则
        $rule = Db::name('system_info')->field('register_max_length')->find();
        return $rule;
    }

    //退出登录
    public static function logout($userid = '')
    {
        // if(!$userid){
        //     $userid = Session::get('userInfo.id');
        // }
        //记录用户最后一次登录时间
        // $user['lasttime'] = time();
        //记录用户在线时间
        //销毁用户登录信息
        // Session::delete('userInfo');
        Cookie::delete('uid');
        DB::name("user_user")->where('id',$userid)->update(['on_status'=>0]);
        // session::delete('id');
        // session::delete('username');
        // session::delete('logintime');
        // cookie::set('bocai_active_time', time(), 0);
        // return $this->fetch('login');
    }

    //   注   册
    public function register()
    {
        $request = Request::param();
        if ($request) {
            //注册方式 验证信息***************************************************
            
            //注册方式一：手机验证码注册
            if (isset($request['verify'])) {
                if (!Cookie::has('bocai_verify')) {
                    //code 0验证码过期
                    echo '{"code":0}';
                    return;
                } else {
                    //1 验证码不正确
                    if (cookie::get('bocai_verify')!=$request['verify']) {
                        echo '{"code":1}';
                        return;
                    }
                    //验证码正确 检测手机号码
                    unset($request['verify']);
                    $res = Db::name('user_user')->where('phonenumber', $request['phonenumber'])->find();
                    if ($res) {
                        echo '{"code":2}';
                        return;
                    }
                }
            }
            //注册方式二： 手机号码|用户名

            else {
                // 登录名+密码注册
                if (!empty($request['username'])) {
                    //2 用户名已存在
                    $res = Db::name('user_user')->where('username', $request['username'])->find();
                    if ($res) {
                        echo "{'code':2}";
                        return;
                    }
                }
                // 手机号码+密码注册
                if (!empty($request['phonenumber'])) {
                    $res = Db::name('user_user')->where('phoneumber', $request['phonenumber'])->find();
                    echo "{'code':2}";
                    return;
                }
            }
            
            //验证通过建立用户************************************************************
            //initializeuser():建立用户信息
            $user =self::initializeuser($request);
            //注册成功，把登录id，用户名存进Session用于用户页面信息的查找
            if ($user) {
                session::set('id', $user['id']);
                session::set('username', $user['username']);
                session::set('logintime', time());
                //注册成功
                $welcome = Db::name('system_info')->value('register_welcome');
                $welcome = str_replace("昵称", $user['nickname'], $welcome);
                $data=[
                        "code"=>$welcome,
                        "nickname"=>$user['nickname']
                    ];
                echo json_encode($data);
                return;
            } else {
                echo '{"code":"检测入库字段是否正确"}';
                return 0;
            }
        }
        //第一次请求返回注册类型
        else {
            //返回注册类型
            // $registerMod = Db::name('system_info')->value('register_mod');
            // echo '{"code":'.$registerMod.'}';
            // return;
            return $this->fetch();
        }
    }

    //阿里云注册获取手机验证码
    public function getverify()
    {
        $request = Request::param();
        //ajax获取验证码
        $phonenumber = $request['phonenumber'];
        SmsDemo::Sendsms($phonenumber);
        //用cookie存储$phonenumber;用于前台验证
    }
   


    public function createuserid()
    {
        //从register_stat_numbs开始
        $register_start_numbs = Db::name('system_info')->value('register_start_number');
        $newId = self::ifHave($register_start_numbs);
        if ($newId) {
            return $newId;
        }
        //获取最后一个id
        $lastId = Db::name('user_user')->order('id', 'desc')->limit(1)->value('id');
        $newId = $lastId;
        $banIdJson = Db::name('system_info')->value('register_rules');
        do {
            $newId++;
        } while (preg_match("/".$banIdJson."/", $newId));
        return $newId;
        // if()
    }

    //开始的id
    public function ifHave($register_start_numbs)
    {
        $register_start_numbs++;
        $res = Db::name('user_user')->where(['id'=>$register_start_numbs])->find();
        if (!$res) {
            return $register_start_numbs;
        } else {
            return 0;
        }
    }

    /*初始化用户的信息
    *createuserid():建立用户的id
    *head:head.png 默认头像
    *intgral：0 默认积分0
    *默认000000000000*/
    public function initializeuser($request)
    {
        //获取系统配置 __PUBLIC__:  /bcweb/public;
        $config = config();
        $public=$config['template']['tpl_replace_string']['__PUBLIC__'];
        
        //进行用户数据初始化

        //查询系统初始化规则
        
        //    `register_mod` int(11) NOT NULL COMMENT '会员注册：注册模式。0、表示禁止；1、表示任意；2、表示手机号码；3、表示短信验证（注意：手机号码不需要发短信，短信验证才需要发短信）',
        
        //    `register_rules` varchar(150) NOT NULL COMMENT '会员注册：保留规则（多个使用‘|’隔开），出现在规则范围内的账号不允许注册',
        //    `register_signature` varchar(150) NOT NULL COMMENT '会员注册：默认签名',
        //    `register_keyword` varchar(150) NOT NULL COMMENT '会员注册：昵称禁止关键词（多个关键词用‘|’隔开），昵称不可以出现以上的关键词',
        //    `register_max_length` int(11) NOT NULL COMMENT '会员注册：昵称长度（单位：字）',
        //    `register_welcome` varchar(150) NOT NULL COMMENT '会员注册：欢迎信息',
        //    `register_gold` int(11) NOT NULL COMMENT '会员注册：初始金币',
        //    `register_integral` int(11) NOT NULL COMMENT '会员注册：初始积分',
        //    `register_check_key` varchar(32) NOT NULL COMMENT '会员注册：验证密钥（32位字符长度）',
        //    `register_end_time` int(11) NOT NULL COMMENT '会员注册：到期时间（时间戳）',
        
        $systemrule = Db::name('system_info')->find();
        // 	用户信息 id username password  gold bank integral head phonenumber nickname trading signature frvalidation stmessage  broadcast online lasttime seven recharge

        //初始化用户
        //初始化用户id // username password phonenumber 前台获取
        // *  `register_prefix` int(11) NOT NULL COMMENT '会员注册：会员前缀',
        $request['id'] = self::createuserid($request);
        //初始化用户昵称 = 前缀+id
        $request['nickname'] = $systemrule['register_prefix'].$request['id'];
        //初始化头部
        $request['head'] = $public.$systemrule['register_head'];
        
        //初始化金币
        $request['gold'] = $systemrule['register_gold'];
        //初始化积分
        $request['integral'] = $systemrule['register_integral'];
        //初始化个性签名
        $request['signature'] = $systemrule['register_signature'];
        //初始化账号到期时间
        $request['end_time'] = time()+$systemrule['register_end_time'];
        //初始化积分
        $request['integral'] = $systemrule['register_integral'];
        
        //系统规则之外初始化
        $request['bank'] = 0;//初始化银行
        $request['vip_time'] = time();		//vip到期时间
        $request['trading']	= 0;	 //交易密码
        $request['frvalidation'] = 1; //好友验证
        $request['stmessage'] = 1;		//陌生信息
        $request['broadcast'] = 1;		//广播消息
        $request['online'] = 0;			//在线时长
        $request['lasttime'] = 0;		//最后一次活动时间
        $request['recharge'] = 0;		//累计充值金币
        $request['status'] =1;			//是否封ip
        $request['ips'] = 0;			//已经登录ip
        //默认medal1为新手勋章
        $medal['medal_id'] = 1;
        $medal['user_id'] =$request['id'];
        $medal['status'] = 1;//默认展示新手勋章


    
        Db::startTrans();//启动事务
        try {
            Db::name('user_user')->insert($request);
            Db::name('user_medal')->insert($medal);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            dump($e);
            Db::rollback();
        }
        return $request;
        // $conn = new mysql("localhost_3306","root","root","bocai");
        // if(!$conn){
        //     die('链接错误'.mysql_error($conn));
        // }
        // mysql_query($conn,"set names utf8");
        // $query="INSERT INTO bc_user"." (id,head,integral,property) "."VALUES"." ('$id','$head,'$integral','$property'";
        // $res = mysql_query($query);
        // if($res){
        //     return 1;
        // }
        // if($res&&$med){
        // 	return $request;
        // }
        // else{
        // 	return 0;
        // }
    }

    //查找用户 根据用户id或者昵称 nickname
    public function searchFriend()
    {
        // $request = [
        // 	'nickname'=>'',
        // 	'id'=>'10'
        // ];
        $request = Request::param();
        // dump($request);
        if (!empty($request['nickname'])) {
            $user = Db::name('user_user')
            ->field('id,nickname,head')
            ->where(['nickname'=>$request['nickname']])
            ->select();

            if ($user) {
                //昵称可能存在多个相同的
                foreach ($user as $key => $vo) {
                    $medalid = Db::name('user_medal')->where(['user_id'=>$vo['id'],'status'=>1])->column('medal_id');
                    $user[$key]['medal'] = Db::name('system_ranks')->where('id', 'in', $medalid)->column('src');
                }
                echo json_encode($user);
                return;
            } else {
                echo '{"code":0}';
                return;
            }
        } else {
            $user = Db::name('user_user')->field('id,nickname,head')->where(['id'=>$request['id']])->find();
            if ($user) {
                $medalid = Db::name('user_medal')->where(['user_id'=>$user['id'],'status'=>1])->column('medal_id');
                $medal = Db::name('system_ranks')->where('id', 'in', $medalid)->column('src');
                $user['medal'] = $medal;
                echo json_encode($user);
                return;
            } else {
                echo '{"code":0}';
                return;
            }
        }
    }

    public function addFriend()
    {
        // $request = [
        // 	'id'=>16
        // ];
        
        $request = Request::param();
        $sendid = $request['id'];
        $uid = session::get('id');
        $frValidation = Db::name('user_user')->where(['id'=>$sendid])->value('frvalidation');
        //不开启好友验证直接添加成功
        if (!$frValidation) {
            $isHave = Db::name('user_friends')->where(['user_id'=>$uid,'friend_id'=>$sendid])->find();
            if ($isHave) {
                //已经是好友
                echo '{"code":2}';
                return;
            } else {
                $friend =[
                    ['user_id'=>$uid,'friend_id'=>$sendid],
                    ['user_id'=>$sendid,'friend_id'=>$uid]
                ];
                $message = [
                    [
                        'send_id'=>$uid,
                        'get_id'=>$sendid,
                        'time'=>time(),
                        'status'=>1,
                        'content'=>"你们已经是好友了,快来打个招呼吧",
                        'flag'=>0
                    ],
                    [
                        'send_id'=>$sendid,
                        'get_id'=>$uid,
                        'time'=>time(),
                        'status'=>1,
                        'content'=>"你们已经是好友了,
                        快来打个招呼吧",
                        'flag'=>0
                    ]
                ];
                $dealAdd = self::dealAdd($friend, $message);
                if ($dealAdd) {
                    //好友添加成功，更新好友栏
                    $friends = self::friends($uid);
                    //好友添加成功，更新信息为已读
                    Db::name('user_message')
                    ->where(['get_id'=>$uid,'send_id'=>$sendid,'content'=>"请求添加您为好友"])
                    ->update(['flag'=>1]);
                    Db::name('user_message')
                    ->where(['send_id'=>$uid,'get_id'=>$sendid,'content'=>"请求添加您为好友"])
                    ->update(['flag'=>1]);
                    $data = [
                        'code'=>4,
                        'friends'=>$friends
                    ];
                    echo json_encode($data);
                    return;
                } else {
                    //好友请求发送失败
                    echo '{"code":0}';
                    return;
                }
            }
        }
        //开启好友验证只是发送请求信息
        else {
            $isHave = Db::name('user_friends')->where(['user_id'=>$uid,'friend_id'=>$sendid])->find();
            if ($isHave) {
                //已经是好友
                echo '{"code":2}';
                return;
            } else {
                $message = [
                    'send_id'=>$uid,
                    'get_id' =>$sendid,
                    'content'=>"请求添加您为好友",
                    'status' =>'3',
                    'time'=>time()
                ];
                //请求添加信息还没有被读直接返回发送成功
                $res = Db::name('user_message')->where(['send_id'=>$uid,'get_id'=>$sendid,'flag'=>0])->find();
                if ($res) {
                    echo '{"code":3}';
                    return;
                } else {
                    //不存在请求信息更新数据库
                    $res = Db::name('user_message')->insert($message);
                    if ($res) {
                        //好友请求发送成功
                        echo '{"code":1}';
                        return;
                    } else {
                        //好友请求发送失败
                        echo '{"code":0}';
                        return;
                    }
                }
            }
        }
    }
    /*开启好友验证：
     *1同意添加好友 添加信息移除 好友信息+1
     *2拒绝添加好友 添加信息移除 系统消息+1
     */
    public function friendValidation()
    {
        // $request = ['id'=>15,'code'=>1];

        $request = Request::param();
        $uid = session::get('id');//我
        $sendid = $request['id'];//拒绝  同意
        if ($request['code']) {   //同意
            $friend =[
                ['user_id'=>$uid,'friend_id'=>$sendid],
                ['user_id'=>$sendid,'friend_id'=>$uid]
            ];
            $message = [
                [
                    'send_id'=>$uid,
                    'get_id'=>$sendid,
                    'time'=>time(),
                    'status'=>1,
                    'content'=>"你们已经是好友了,
                    快来打个招呼吧",
                    'flag'=>0
                ],
                [
                    'send_id'=>$sendid,
                    'get_id'=>$uid,
                    'time'=>time(),
                    'status'=>1,
                    'content'=>"你们已经是好友了,
                    快来打个招呼吧",
                    'flag'=>0
                ]
            ];
            $dealAdd = self::dealAdd($friend, $message);
            if ($dealAdd) {
                //好友添加成功，更新信息为已读
                Db::name('user_message')
                ->where(['get_id'=>$uid,'send_id'=>$sendid,'content'=>'请求添加您为好友'])
                ->update(['flag'=>1]);
                Db::name('user_message')
                ->where(['send_id'=>$uid,'get_id'=>$sendid,'content'=>"请求添加您为好友"])
                ->update(['flag'=>1]);
                $friends = self::friends($uid);
                //好友添加成功，更新信息为已读 返回整个好友信息给前端

                $data = [
                        'code'=>1,
                        'friends'=>$friends
                    ];
                echo json_encode($data);
                return;
            }
        } else {
            $nickname = Db::name('user_user')->where(['id'=>$uid])->value('nickname');
            //库里
            $message = [
                'get_id' =>$sendid,
                'send_id' =>$uid,
                'time'=>time(),
                'status'=>4,
                'content'=>"用户".$nickname."拒绝添加你为好友"
            ];
            $res = 1;
            Db::startTrans();
            try {
                Db::name('user_message')->where(['get_id'=>$uid,'send_id'=>$sendid])->update(['flag'=>1]);
                Db::name('user_message')->insert($message);
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                $res = 0;
                Db::rollback();
            }
            if ($res) {
                //拒绝成功返回系统消息
                echo '{"code":1}';
                return;
            } else {
                //拒绝失败
                echo '{"code":0}';
                return;
            }
        }
    }
    //同意添加好友 好友表互相添加好友 信息表互相发送一条添加成功信息
    public function dealAdd($friend, $message)
    {
        // 启动事务
        $res = 1;
        Db::startTrans();
        try {
            Db::name('user_friends')->insertAll($friend);

            Db::name('user_message')->insertAll($message);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            $res = 0;
            Db::rollback();
        }
        return $res;
    }

    //删除好友信息
    public function deleteFriend()
    {
        $uid = session::get('id');
        $request = Request::param();
        // dump($request);
        if ($request) {
            $friendid = $request['id'];
            // 启动事务
            $res = 1;
            Db::startTrans();
            try {
                // 互相删除好友
                Db::name('user_friends')->where(['friend_id'=>$uid,'user_id'=>$friendid])->delete();

                Db::name('user_friends')->where(['friend_id'=>$friendid,'user_id'=>$uid])->delete();

                $nickname = Db::name('user_user')->where(['id'=>$uid])->value('nickname');
                //给被删除好友的用户发送一条系统信息
                $message = [
                    'send_id'=>$uid,
                    'get_id'=>$friendid,
                    'content'=>"你被用户".$nickname."删除了好友",
                    'time' =>time(),
                    'status'=>4,
                    'flag' =>0
                ];
                Db::name('user_message')->insert($message);
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                $res = 0;
                Db::rollback();
            }
            if ($res) {
                //删除成功
                $friend = self::friends($uid);
                $data = [
                    'code'=>1,
                    'friend'=>$friend
                ];
                echo json_encode($friend);
            } else {
                echo '{"code":0}';
            }
        } else {
            echo '{"code":没有参数}';
            return;
        }
    }
    //把user.json插入库中
    public function userjson()
    {
        $path = str_replace('controller', '', dirname(__FILE__));
        $str = file_get_contents($path."/user.json");
        $strarray = json_decode($str, true);
        for ($i=0;$i<count($strarray);$i++) {
            $temparray['id'] = $strarray[$i]['id'];
            $temparray['username'] = $strarray[$i]['username'];
            $temparray['password'] = $strarray[$i]['password'];
            $temparray['integral'] = $strarray[$i]['integral'];
            $temparray['head'] = $strarray[$i]['head'];
            $temparray['phonenumber'] = $strarray[$i]['phonenumber'];
            $temparray['trading'] = $strarray[$i]['trading'];
            $temparray['signature'] = $strarray[$i]['signature'];
            $temparray['frvalidation'] = $strarray[$i]['frvalidation'];
            $temparray['stmessage'] = $strarray[$i]['stmessage'];
            $temparray['broadcast'] = $strarray[$i]['broadcast'];
            $temparray['online'] = $strarray[$i]['online'];
            $temparray['lasttime'] = $strarray[$i]['lasttime'];
            $temparray['seven'] = $strarray[$i]['seven'];
            $temparray['recharge'] = $strarray[$i]['recharge'];
            $temparray['status'] = $strarray[$i]['status'];
            $temparray['ips'] = $strarray[$i]['ips'];
            $userarray[$i]=$temparray;
            $res = Db::name('user_user')->insert($temparray);
            if (!$res) {
                dump('数据插入失败');
                break;
            }
        }
    }

    //把message.json消息插入库中
    public function messagejson()
    {
        $path = str_replace('controller', '', dirname(__FILE__));
        $str = file_get_contents($path."/message.json");
        $strarray = json_decode($str, true);
        for ($i=0;$i<count($strarray);$i++) {
            $temparray['id'] = $strarray[$i]['id'];
            $temparray['send_id'] = $strarray[$i]['send_id'];
            $temparray['get_id'] = $strarray[$i]['get_id'];
            $temparray['time'] = $strarray[$i]['time'];
            $temparray['status'] = $strarray[$i]['status'];
            $temparray['content'] = $strarray[$i]['content'];
            $temparray['flag'] = $strarray[$i]['flag'];
            // $messagearray[$i]=$temparray;
            $res = Db::name('user_message')->insert($temparray);
            if (!$res) {
                dump('数据插入失败');
                break;
            }
        }
    }

    //把red
    public function redjson()
    {
        $path = str_replace('controller', '', dirname(__FILE__));
        $str = file_get_contents($path."/red.json");
        $strarray = json_decode($str, true);
        for ($i=0;$i<count($strarray);$i++) {
            $temparray['id'] = $strarray[$i]['id'];
            $temparray['send_id'] = $strarray[$i]['send_id'];
            $temparray['get_id'] = $strarray[$i]['get_id'];
            $temparray['send_time'] = $strarray[$i]['send_time'];
            $temparray['get_time'] = $strarray[$i]['get_time'];
            $temparray['status'] = $strarray[$i]['status'];
            // $redarray[$i]=$temparray;
            $res = Db::name('user_red')->insert($temparray);
            if (!$res) {
                dump('数据插入失败');
                break;
            }
        }
    }
    //medal
    public function medaljson()
    {
        $path = str_replace('controller', '', dirname(__FILE__));
        $str = file_get_contents($path."/usermedal.json");
        $strarray = json_decode($str, true);
        for ($i=0;$i<count($strarray);$i++) {
            $temparray['id'] = $strarray[$i]['id'];
            $temparray['user_id'] = $strarray[$i]['user_id'];
            $temparray['status'] = $strarray[$i]['status'];
            $temparray['medal_id'] = $strarray[$i]['medal_id'];
            // $medalarray[$i]=$temparray;
            $res = Db::name('user_medal')->insert($temparray);
            if (!$res) {
                dump('数据插入失败');
                break;
            }
        }
    }

       
    //把user_message  sql数据转化成json数据
    public function messagesql()
    {
        $message = Db::name("user_message")->select();
        $message = json_encode($message);
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bcweb/application/Home/message.json', $message);
    }
    //把user_message  sql数据转化成json数据
    public function usersql()
    {
        $user = Db::name("user_user")->select();
        $user = json_encode($user);
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bcweb/application/Home/user.json', $user);
    }
    //把红包
    public function redsql()
    {
        $red = Db::name("user_red")->select();
        $red = json_encode($red);
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bcweb/application/Home/red.json', $red);
    }
    //把medal
    public function medalsql()
    {
        $medal = Db::name("user_medal")->select();
        $medal = json_encode($medal);
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bcweb/application/Home/medal.json', $medal);
    }

    //把前端传过来的信息传进json文件看看结果
    public function putInJson($request)
    {
        $str = '';
        foreach ($request as $key => $value) {
            $str .= $key.":".$value."\r\n";
        }
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bcweb/bank.json', $str, FILE_APPEND);
    }


    //用户信息
    //    public function user($userid)
    //    {
    //        // 基础信息：
    //        // phonenumber:用户名：（手机号）
    //        // password:密码
    //        // id:用户id：注册账号时赋予，每个用户唯一标识
    //        // username:昵称
    //        // signatrue个性签名
    //        // trading:交易密码
    //        $field = "id,signature,integral,gold,nickname,bank,frvalidation,stmessage,broadcast,head";
    //        $user = Db::name('user_user')
    //        ->field($field)
    //        ->where('id', $userid)
    //        ->find();
    //        //获取用户勋章 已佩戴的 最多5个
    //        // $usermedal = Db::name('user_medal')->field('medal_id')->limit(5)->where(['user_id'=>$userid,'status'=>1])->select();
    //        $usermedal = DB::name('user_medal')
    //        ->alias('m')
    //        ->join('bc_system_ranks r', 'r.id=m.medal_id')
    //        // ->field('r.src')
    //        ->where(['m.status'=>1,'m.user_id'=>$userid])
    //        ->limit(5)
    //        ->column('r.src');
    //        // ->select();
   
    //        // foreach($usermedal as $key=>$vo){
    //        //   $user['medal'][$key] = Db::name('system_ranks')->where(['id'=>$vo['medal_id']])->value('src');
    //        // }
    //        $user['medal'] = $usermedal;
    //        return $user;
    //    }


//      //用户登录成功后获取用户的基本信息
//      public function userinfo($user='')
//      {
//          //登录成功后返回用户的基本信息用于前台展示
//          if (!$user) {
//              $userid = session::get('userInfo.id');
//              $user = self::user($userid);
//          } else {
//              $userid = $user['id'];
//          }
//          //用户好友信息
//          $friends = self::friends($userid);
 
//          //陌生人信息
//          $strange = self::strange($userid);
//          //系统消息
//          $system = self::system($userid);
//          //系统规则
//          $rule = self::rule($userid);
//          $addfriends = self::addfriends($userid);
//          $data = [
//                  'code'=>0,
//                  'msg'=>'ok',
//                  'user'=>$user,
//                  'addfriends'=>$addfriends,
//                  // 'messagenum'=>$message,
//                  'friends'=>$friends,
//                  'strange'=>$strange,
//                  'system' => $system
//                  // 'rule' => $rule
//              ];
//          echo json_encode($data);
             
//          file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bcweb/text2.json', json_encode($data));
//          return;
         
//          //获取消息
//              //summessage:总消息条数
//              // friendsmessage:好友信息总条数
//              // strangemessage:陌生信息总条数
//              // systemmessage:系统消息总条数
//              // red:红包条数
//              //status 1 好友信息 2陌生人信息与  3添加好友 4系统信息与
//              // $message['sum'] = Db::name("user_message")->where(['flag'=>0,'get_id'=>$userid])->count('flag');
//  /******************************不要求总数*****************************************
//              // $message['strange'] = Db::name("user_message")->where(['flag'=>0,'status'=>2,'get_id'=>$userid])->count('flag');
//              // $message['system'] = Db::name("user_message")->where(['flag'=>0,'status'=>4,'get_id'=>$userid])->  count('flag');
//              // $message['red']  = Db::name('user_red')->where(['status'=>0,'get_id'=>$userid])->count();
//              // dump($message);die;
//  *************************************************************************************/
//              //前台单页面渲染数据测试
//      }

 /**************************此功能暂时不用********************************************
    /*用于随机产生id值
    **id的值从1到9
    */
    // public function randomkeys($length){
    // 	$partent = "1234567890";
    // 	$key=rand(1,10);
    // 	for($i=1;$i<$length;$i++){
    // 		$key.=$partent{mt_rand(0,9)};
    // 	}
    // 	return $key;
    // }
    /*
     **获取长度为6~10位的用户id
     **$length:id长度6~10位
     **radomkeys($length)：获取长度位$length的用户id
    public function createuserid($request){
        //把用户id按管理员规则初始化
        // $ruleId = Db::name('system_info')->filed('register_')->find();
        // 随机初始化6-10位数的id **暂时不用**
        $length = rand(6,10);
        $request['id'] = self::randomkeys($length);
        //防止id重复
        $id = $request['id'];
        $res = Db::name('user_user')->where('id',$request['id'])->find();

        //产生重复id再次循环直到id不重复为止
        //此处可能有bug
        if($res){
            $request['id'] = self::createuserid($request);
        }
        else{
            return $request['id'];
        }

    }
    ***********************************************************************/

    //添加好友信息 status = 3
    // public static function addfriends($userid)
    // {
    //     //添加好友组的信息
    //     $addfriend = Db::name('user_message')
    //     ->alias('m')
    //     ->join('user_user u', 'u.id=m.send_id')
    //     ->field('m.send_id,u.id,u.nickname,m.content,u.head,m.flag')
    //     ->where(['m.status'=>3,'m.get_id'=>$userid])
    //     ->select();
    //     return $addfriend;
    // }
}
