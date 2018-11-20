<?php
namespace app\Server\controller;

use think\Db;
use app\MyCommon\controller\CheckLogin;
use app\MyCommon\controller\SMS;
use app\Server\controller\Redis;

class Index
{
    public static function registerMember($data)
    {
        $index = [
            'systemInfo'=>'system_info',
            'code'=>'VerificationCode_'.$data['con_id']
        ];
        // 获取注册的初始信息
        $res = Redis::get($index);
        $systemInfo = json_decode($res['systemInfo'], true);
        $code = $res['code'];
        // 检验验证码
        if ($code || $data['mode'] == 3) {
            if (!$code) {
                return [
                    'type'=>'resopneRegisterMember',
                    'status'=>false,
                    'msg'=>'验证码已过期！',
                ];
            } elseif ($data['code'] != $code) {
                return [
                    'type'=>'resopneRegisterMember',
                    'status'=>false,
                    'msg'=>'验证码不正确！',
                ];
            }
            Redis::del('VerificationCode_'.$data['con_id']);
        }
        // 判断邀请码是否正确
        if ($data['invitedCode'] != $systemInfo['register_check_key']) {
            return [
                'type'=>'responeRegisterMember',
                'status'=>false,
                'msg'=>'邀请码不正确！',
            ];
        }
        // 计算用户名长度
        $length = mb_strlen($data['username']);
        if ($data['mode'] == 1) {
            // 用户名注册
            if ($length > $systemInfo['register_max_length']) {
                return [
                    'type'=>'resopneRegisterMember',
                    'status'=>false,
                    'msg'=>"用户名超出了最大长度".$systemInfo['register_max_length']."了",
                ];
            }
            //判断用户名是否存在非法字眼
            if (preg_match("/".$systemInfo['register_keyword']."/", $data['username'], $result)) {
                return  [
                    'type'=>"resopneRegisterMember",
                    'status'=>false,
                    'msg'=>'用户名存在非法字眼：“'.$result[1].'"!',
                ];
            }
            $username = $data['username'];
            $phone = '';
            if (DB::name('user_user')->where('username', $username)->value('id')) {
                return [
                    'type'=>'resopneRegisterMember',
                    'status'=>false,
                    'msg'=>'该用户名已经被注册！'
                ];
            }
        } else {
            // 手机注册
            if ($length != 11 || preg_match('/[^0-9]+/', $data['username'])) {
                return [
                    'type'=>'resopneRegisterMember',
                    'status'=>false,
                    'msg'=>'非法手机号！',
                ];
            }
            $username = '';
            $phone = $data['username'];
            if (DB::name('user_user')->where('phonenumber', $phone)->value('id')) {
                return [
                    'type'=>'resopneRegisterMember',
                    'status'=>false,
                    'msg'=>'该手机号已经被注册！'
                ];
            }
        }
        // 生产非重复性id
        $maxNumber = DB::name('user_user')->order('id desc')->value('id');
        if ($maxNumber <= $systemInfo['register_start_number']) {
            $maxNumber = $systemInfo['register_start_number'] + 1;
        } else {
            $maxNumber++;
        }
        $maxNumber = self::checkId($maxNumber, $systemInfo['register_rules']);
        // 用户信息数据
        $userInfo = [
            'id'=>$maxNumber,
            'username'=>$username,
            'password'=>$data['password'],
            'gold'=>$systemInfo['register_gold'],
            'bank'=>0,
            'integral'=>$systemInfo['register_integral'],
            'head'=>$systemInfo['register_head'],
            'phonenumber'=>$phone,
            'nickname'=>$systemInfo['register_prefix'].$maxNumber,
            'signature'=>$systemInfo['register_signature'],
            'end_time'=>$systemInfo['register_end_time'],
            'register_time'=>time(),
            'allow_times'=>$systemInfo['filter_allow'],
        ];
        // 保存数据库
        if (DB::name('user_user')->insert($userInfo)) {
            return [
                'type'=>'responeRegisterMember',
                'status'=>true,
                'wellcome'=>str_replace('[@用户]', "“".$data['username']."”", $systemInfo['register_welcome']),
            ];
        } else {
            return [
                'type'=>'responeRegisterMember',
                'status'=>false,
                'msg'=>'注册失败！',
            ];
        }
    }

    /**
     * 产生非重复性的符合规则的id
     */
    public static function checkId($id, $rules)
    {
        // 重组规则
        $rule = str_replace('*', '(.*)', $rules);
        // 判断是否存在匹配项
        if ($res = preg_match('/^('.$rule.')$/', (string)$id, $result, PREG_OFFSET_CAPTURE)) {
            $prev = '';
            $next = '';
            foreach ($result as $key => $vo) {
                if ($vo[0] != $id && $vo[0]) {
                    if ($vo[1] == 0) {
                        $prev .= $vo[0];
                        $id = str_replace($vo[0], '', $id);
                    } else {
                        $stemp = explode($vo[0], $id);
                        if ($stemp[1]) {
                            $prev .= $stemp[0].$vo[0];
                            $id = $stemp[1];
                        } else {
                            $next .= $vo[0];
                            $id = str_replace($vo[0], '', $id);
                        }
                    }
                }
            }
            // id自加1
            $id += 1;
            // 重组id
            $id = $prev.$id.$next;
            // 再次检查
            $id = $this->checkId($id, $rules);
        }
        // 返回int类型的id
        return (int)$id;
    }


    public static function getYanZhengMa($data)
    {
        $code = rand(100000, 999999);
        $result = SMS::sendmsg($data['phone'], $code);
        if ($result===true) {
            // 保存产生的验证码
            // $this->codes[$data['con_id']] = $code;
            // 验证码保存60秒
            // $redis->setex('VerificationCode_'.$data['con_id'], 60, $code);
            Redis::setex([['VerificationCode_'.$data['con_id'], 60, $code]]);
            return [
                'type'=>'responeGetYanZhengMa',
                'code'=>$code,
                'status'=>true,
            ];
        } else {
            return [
                'type'=>'responeGetYanZhengMa',
                'status'=>false,
                'msg'=>'验证码发送失败！',
                'result'=>$result
            ];
        }
    }


    /**
     * 修改登录密码
     */
    public static function changeLoginPassword($data)
    {
        $userPassword = DB::name('user_user')->where('id', $data['send_id'])->field('id,password')->find();
        if ($userPassword['password'] == $data['originalPsd']) {
            if ($data['newPsd1'] && $data['newPsd1'] == $data['newPsd2']) {
                $userPassword['password'] = $data['newPsd1'];
                if (DB::name('user_user')->update($userPassword)) {
                    return [
                        'status'=>true
                    ];
                } else {
                    return [
                        'status'=>false,
                        'msg'=>'修改密码失败！'
                    ];
                }
            } else {
                return [
                    'status'=>false,
                    'msg'=>'两次密码不一致！'
                ];
            }
        } else {
            return [
                'status'=>false,
                'msg'=>'原密码错误！'
            ];
        }
    }
    /**
     * 修改签名
     */
    public static function changeSignature($data)
    {
        if (DB::name('user_user')->where('id', $data['send_id'])->update(['signature'=>$data['newsignature']])) {
            return [
                'status'=>true
            ];
        } else {
            return [
                'status'=>false,
                'msg'=>'修改失败！'
            ];
        }
    }
    /**
     * 修改昵称
     */
    public static function changeNickName($data)
    {
        if (DB::name("user_user")->where('id', $data['send_id'])->update(['nickname'=>$data['newname']])) {
            return [
                'status'=>true
            ];
        } else {
            return [
                'status'=>false,
                'msg'=>'修改失败！'
            ];
        }
    }

    /**
     * 修改我的设置
     */
    public static function settingChange($data)
    {
        if (DB::name("user_user")->where('id', $data['send_id'])->update([$data['index']=>$data['value']])) {
            return [
                'status'=>true
            ];
        } else {
            return [
                'status'=>false,
                'msg'=>'修改失败！'
            ];
        }
    }
    /**
     * 从社区银行取出金币
     */
    public static function getGoldFromBank($data)
    {
        $goldInfo = DB::name('user_user')->where('id', $data['send_id'])->field('id,gold,bank,trading')->find();
        if ($goldInfo['trading'] != $data['tradingPsd']) {
            // 判断交易密码正不正确
            return [
                'type'=>'responeGetGoldFromBank',
                'status'=>false,
                'msg'=>'交易密码不正确',
            ];
        }
        if ($goldInfo['bank']<$data['gold']) {
            return [
                'type'=>'responeGetGoldFromBank',
                'status'=>false,
                'msg'=>'银行金币不足！',
            ];
        } else {
            $goldInfo['bank'] -= $data['gold'];
            $goldInfo['gold'] += $data['gold'];
            if (DB::name('user_user')->update($goldInfo)) {
                return [
                    'type'=>'responeGetGoldFromBank',
                    'status'=>true,
                    'msg'=>'ok',
                    'goldInfo'=>$goldInfo
                ];
            } else {
                return [
                    'type'=>'responeGetGoldFromBank',
                    'status'=>false,
                    'msg'=>'取出金币失败！'
                ];
            }
        }
    }
    /**
     * 存储金币
     */
    public static function storageGold($data)
    {
        // 获取用户的金币信息
        $usergold = DB::name('user_user')->where('id', $data['send_id'])->field('id,gold,bank,trading')->find();
        if ($usergold['trading'] != $data['tradingPsd']) {
            // 判断交易密码正不正确
            return [
                'type'=>'responeStorageGold',
                'status'=>false,
                'msg'=>'交易密码不正确',
            ];
        }
        if ($usergold['gold'] < $data['gold']) {
            // 用户金币不足
            return [
                'type'=>'responeStorageGold',
                'status'=>false,
                'msg'=>'金币不足以支付',
            ];
        } else {
            // 执行存储过程
            $usergold['gold'] -= $data['gold'];
            $usergold['bank'] += $data['gold'];
            if (DB::name('user_user')->update($usergold)) {
                // 返回存储后的金币信息
                return [
                    'type'=>'responeStorageGold',
                    'status'=>true,
                    'msg'=>'ok',
                    'goldInfo'=>$usergold
                ];
            } else {
                // 存储失败
                return [
                    'type'=>'responeStorageGold',
                    'status'=>false,
                    'msg'=>'存储金币失败！'
                ];
            }
        }
    }


    /**
     * 登陆处理
     */
    public static function logining($data)
    {
        // 检测是否为手机登陆和登陆IP地址是在IP黑名单中,检测状态，通过检测，code为0
        $checkResult = CheckLogin::loginCheck($data['ip_address']);
        if ($checkResult['code']) {
            return $checkResult;
        }
        // 判断账号是否为空
        if (!$data['username']) {
            return [
                'code'=>1,
                'msg'=>'账号不能为空',
            ];
        }
        // 判断密码是否为空
        if (!$data['password']) {
            return [
                'code'=>2,
                'msg'=>'密码不能为空',
            ];
        }
        // 获取用户信息
        $user = Db::name('user_user')->where('username|phonenumber', $data['username'])->find();
        // 判断用户是否存在
        if (empty($user)) {
            return [
                'code'=>3,
                'msg'=>'该账号尚未注册！'
            ];
        }
        // 判断用户是否已经上线
        if ($user['login_status']) {
            return [
                'code'=>5,
                'msg'=>'该账号已经登陆！'
            ];
        }
        // 判断用户是否被查封
        if ($user['status']) {
            return [
                'code'=>4,
                'msg'=>'该账号已经被查封！'
            ];
        }
        if ($user['allow_times']<=0) {
            $systemInfo = json_decode(Redis::get('system_info'), true);
            if ($systemInfo['defriend_mod'] == 2) {
                return [
                    'code'=>6,
                    'msg'=>'您因为输入过滤关键词次数过多，已被禁止登录！',
                ];
            }
        }
        //用户登录成功给前台返回1标志；同时设定登录时间
        if ($user['password']==$data['password']) {
            $logintime = time();
            // 更新用户最后登录时间和登陆状态
            $updateData = [
                'lasttime'=>$logintime,
                'on_status'=>1,
                'id'=>$user['id'],
                'connectionID'=>$data['con_id'],
                'login_status'=>1,
            ];
            if ($user['ips']) {
                $ips = json_decode($user['ips'], true);
                if (!empty($ips)) {
                    if (!in_array($data['ip_address'], $ips)) {
                        $ips[] = $data['ip_address'];
                        $updateData['ips'] = json_encode($ips);
                    }
                } else {
                    $ips[] = $data['ip_address'];
                    $updateData['ips'] = json_encode($ips);
                }
            } else {
                $ips[] = $data['ip_address'];
                $updateData['ips'] = json_encode($ips);
            }
            
            Db::name('user_user')->update($updateData);
            // 获取用户勋章信息
            $usermedal = DB::name('user_medal')
            ->alias('m')
            ->join('bc_system_ranks r', 'r.id=m.medal_id')
            ->where(['m.status'=>1,'m.user_id'=>$user['id']])
            ->limit(5)
            ->column('r.src');
            $user['medals'] = $usermedal;
            return [
                'code'=>0,
                'msg'=>'ok',
                'userInfo'=>$user,
            ];
        } else {
            return [
                'code'=>6,
                'msg'=>'密码不正确'
            ];
        }
    }


    /**
     * 握手处理
     */
    public static function handle($data)
    {
        // 更新用户的在线状态和链接资源标识符
        $updateValue = [
            'lasttime'=>time(),
            'on_status'=>1,
            'id'=>$data['send_id'],
            'connectionID'=>$data['con_id'],
        ];
        DB::name('user_user')->update($updateValue);
        // 查询好友信息
        $friends = Db::name('user_friends')
        ->alias('f')
        ->join('bc_user_user u', 'u.id=f.friend_id')
        ->field('u.id,u.head,u.nickname,u.username,u.on_status,u.connectionID')
        ->where(['f.user_id'=>$data['send_id']])
        ->order('u.on_status desc')
        ->select();

        $friendIds = [];
        $friendList = [];
        if (!empty($friends)) {
            foreach ($friends as $key => $vo) {
                if ($vo) {
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
        // 查询一个月之内发送过消息给当前用户的陌生人信息
        $date = time()-30*24*60*60;
        $strangeInfo = Db::name("user_message")
         ->alias('m')
         ->join('bc_user_user u', '(u.id=m.send_id or u.id=m.get_id) and u.id!='.$data['send_id'])
         ->field('u.id,u.head,u.nickname,u.username,u.on_status,u.connectionID,u.stmessage')
         ->where('m.get_id|m.send_id', $data['send_id'])
         ->where('m.status', 2)
         ->where('m.time', 'gt', $date)
         ->where('m.send_id', 'notin', $friendIds)
         ->where('m.get_id', 'notin', $friendIds)
         ->order('u.on_status,m.time desc')
         ->group('u.id')
         ->select();
        // 获取陌生人的勋章信息
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
                }
            }
        }

        // 获取7天内的系统消息以及相关的用户信息
        $system = Db::name("user_message")
        ->alias('m')
        ->join('user_user u', 'u.id=m.send_id')
        ->field('u.id,u.on_status,u.nickname,u.username,u.head,u.connectionID,m.content,m.flag,m.status,m.id as mid')
        ->where('m.get_id', $data['send_id'])
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


        // 获取未读消息
        $messages = DB::name('user_message')->where(['get_id'=>$data['send_id'],'flag'=>0])->select();
        $msgs = [
            'system'=>0, //系统消息
        ];

        // 分类未读消息
        foreach ($messages as $k => $v) {
            // 判断是否为7天内的系统消息
            if ($v['status'] > 2 && $v['time'] > (time()-7*24*60*60)) {
                $msgs['system']++;
            } elseif ($v['status'] >= 0 && $v['status'] <= 2) {
                $msgs[$v['send_id']][] = $v;
            } else {
                $msgs['systemNotice'][] = $v;
            }
        }
        // 获取用户当前（数据库）金币
        $gold = DB::name('user_user')->where('id', $data['send_id'])->value('gold');
        // $gold = $gold ? $gold : false;
        return [
            'userMsg'=>$msgs,
            'friendList'=>[
                'friends'=>$friendList,
                'strange'=>$strangeFriends,
                'system'=>$systemList,
            ],
            'gold'=> $gold
        ];
    }
    /**
     * 获取app信息
     */
    public static function getAppInfo($data)
    {
        // 获取系统广播消息
        $sysNews = DB::name('system_news')->where('status', 1)->column('content');
        // 获取app信息
        $systemInfo = json_decode(Redis::get('system_info'), true);
        return [
            "sysNews"=>$sysNews,
            "appInfo"=>[
                'app_name'=>$systemInfo['app_name'],
                'logo_src'=>$systemInfo['logo_src'],
                'error_str'=>$systemInfo['error_str'],
                'coin1_name'=>$systemInfo['coin1_name'],
                'coin2_name'=>$systemInfo['coin2_name']
            ],
            "type"=>"appInfoAndSysNews"
        ];
    }

    /**
     * 修改交易密码
     */
    public static function changeTrading($data)
    {
        if ($data['signature']) {
            // 验证处理
        }
        $oldTradingPwd = DB::name('user_user')->where('id', $data['send_id'])->value('trading');
        if ($oldTradingPwd == $data['oldPsd']) {
            if ($data['newPsd'] && $data['newPsd1'] && $data['newPsd'] == $data['newPsd1'] && strlen($data['newPsd']) == 6) {
                if (DB::name('user_user')->where('id', $data['send_id'])->update(['trading'=>$data['newPsd']])) {
                    return [
                        'code'=>0,
                        'status'=>true,
                        'msg'=>'ok'
                    ];
                } else {
                    return [
                        'code'=>303,
                        'status'=>false,
                        'msg'=>'修改失败'
                    ];
                }
            } else {
                return [
                    'code'=>202,
                    'status'=>false,
                    'msg'=>'两次输入不一致或者格式不正确'
                ];
            }
        } else {
            return [
                'code'=>101,
                'status'=>false,
                'msg'=>'原密码不正确'
            ];
        }
    }

    /**
     * 获取用户的链接标识符
     */
    public static function getUserConnectionID($uid)
    {
        return DB::name('user_user')->where('id', $uid)->value('connectionID');
    }

    /**
     * 允许敏感词触发次数减一
     */
    public function allowTimesDec($data)
    {
        DB::name('user_user')->where('id', $data['uid'])->setDec('allow_times');
    }
}
