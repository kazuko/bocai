<?php
namespace app\Home\controller;

// use think\Controller;
use think\Db;
use app\MyCommon\controller\Base;
use app\MyCommon\controller\CheckLogin;
use app\MyCommon\controller\SMS;

// use Aliyun\Core\Config;
// use Aliyun\Core\Profile\DefaultProfile;
// use Aliyun\Core\DefaultAcsClient;
// use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;

// use think\facade\Session;
// use think\facade\Cookie;

class DataProcer
{
    protected $codes = []; //保存注册验证码
    public function mainProcer($data)
    {
        switch ($data['type']) {
            case "readStatus":
                return $this->updateMsgReadStatus($data);
                break;
            case "searchFriends":
                return $this->searchFriends($data);
                break;
            case "addFriendRequest":
                return $this->addFriendRequest($data);
                break;
            case "downLine":
                return $this->downLine($data);
                break;
            case "deleteFriend":
                return $this->deleteFriend($data);
                break;
            case "DeleteMessage":
                return $this->DeleteMessage($data);
                break;
            case "AgreeFriendRequest":
                return $this->AgreeFriendRequest($data);
                break;
            case "changeSystemReadStatus":
                return $this->changeSystemReadStatus($data);
                break;
            case "text":
                return $this->chatRecord($data);
                break;
            case "delStrange":
                return $this->delStrange($data);
                break;
            case "changeTrading":
                return $this->changeTrading($data);
                break;
            case "sendRedBagToFriend":
                return $this->sendRedBagToFriend($data);
                break;
            case "reciveRedBagFromFriend":
                return $this->reciveRedBagFromFriend($data);
                break;
            case "getAppInfo":
                return $this->getAppInfo($data);
                break;
            case "handle":
                return $this->handle($data);
                break;
            case "logining":
                return $this->logining($data);
                break;
            case "RedDetail":
                return $this->RedDetail($data);
                break;
            case "storageGold":
                return $this->storageGold($data);
                break;
            case "getGoldFromBank":
                return $this->getGoldFromBank($data);
                break;
            case "settingChange":
                return $this->settingChange($data);
                break;
            case "changeNickName":
                return $this->changeNickName($data);
                break;
            case "changeSignature":
                return $this->changeSignature($data);
                break;
            case "changeLoginPassword":
                return $this->changeLoginPassword($data);
                break;
            case "getSendRedList":
                return $this->getSendRedList($data);
                break;
            case "getReciveRedList":
                return $this->getReciveRedList($data);
                break;
            case "getThemeList":
                return $this->getThemeList($data);
                break;
            case "getPostList":
                return $this->getPostList($data);
                break;
            case "getComment":
                return $this->getComment($data);
                break;
            case "themeComment":
                return $this->themeComment($data);
                break;
            case "zanComment":
                return $this->zanComment($data);
                break;
            case "ReplyComment":
                return $this->ReplyComment($data);
                break;
            case "postTheme":
                return $this->postTheme($data);
                break;
            case "getMyThemeList":
                return $this->getMyThemeList($data);
                break;
            case "getMyReplyList":
                return $this->getMyReplyList($data);
                break;
            case "transferAccounts":
                return $this->transferAccounts($data);
                break;
            case "getTransferInfo":
                return $this->getTransferInfo($data);
                break;
            case "reciveTransfer":
                return $this->reciveTransfer($data);
                break;
            case "getMedalList":
                return $this->getMedalList($data);
                break;
            case "changeMedalStatus":
                return $this->changeMedalStatus($data);
                break;
            case "getRegisterRules":
                return $this->getRegisterRules($data);
                break;
            case "getYanZhengMa":
                return $this->getYanZhengMa($data);
                break;
            case "registerMember":
                return $this->registerMember($data);
                break;
            case "getChatRoomMsgList":
                return $this->getChatRoomMsgList($data);
                break;
            case "ChatRoomMsg":
                return $this->ChatRoomMsg($data);
                break;
            default:
                
        }
    }

    public function ChatRoomMsg($data)
    {
        try {
            $insertValue = [
                'send_id'=>$data['msg']['send_id'],
                'send_time'=>$data['msg']['send_time'],
                'status'=>$data['msg']['status'],
                'content'=>$data['msg']['content'],
            ];
            $id = DB::name('chat_room')->insertGetId($insertValue);
        } catch (\Exception　$e) {
            dump("ChatRoomMsg->catch:".$e->getMesssage());
            return [
                'type'=>"responeChatRoomMsg",
                'status'=>false,
                'msg'=>'发送失败，请检查网络是否正常！',
            ];
        }
        return [
            'type'=>'responeChatRoomMsg',
            'status'=>true,
            'id'=>$id,
        ];
    }
    
    /**
     * 获取聊天室的信息列表
     */
    public function getChatRoomMsgList($data)
    {
        try {
            $listRows = 10;
            if (!isset($data['page'])) {
                $data['page'] = 0;
            }
            $firstRow = $data['page'] * $listRows;

            $list = DB::name('chat_room')
            ->alias('r')
            ->join('bc_user_user u', 'u.id=r.send_id and r.status>=0')
            ->field("r.*,u.head,u.nickname")
            ->order('send_time desc')
            ->limit($firstRow, $listRows)
            ->select();
        } catch (\Exception $e) {
            dump("getChatRoomMsgList->catch->errMsg:".$e->getMessage());
            return [
                'type'=>'responeGetChatRoomMsgList',
                'status'=>false,
                'msg'=>'获取消息失败，请检查网络是否正常！',
            ];
        }
        return [
            'type'=>'responeGetChatRoomMsgList',
            'status'=>true,
            'MsgList'=>$list,
        ];
    }

    /**
     * 会员注册
     */
    public function registerMember($data)
    {
        // 获取注册的初始信息
        $registerInfo = DB::name('system_info')
        ->field([
            'register_prefix',
            'register_start_number',
            'register_rules',
            'register_signature',
            'register_keyword',
            'register_max_length',
            'register_welcome',
            'register_gold',
            'register_integral',
            'register_check_key',
            'register_head',
            'register_end_time'
        ])
        ->find();
        // 计算用户名长度
        $length = mb_strlen($data['username']);
        if ($data['mode'] == 1) {
            // 用户名注册
            if ($length > $registerInfo['register_max_length']) {
                return [
                    'type'=>'resopneRegisterMember',
                    'status'=>false,
                    'msg'=>"用户名超出了最大长度".$registerInfo['register_max_length']."了",
                ];
            }
            //判断用户名是否存在非法字眼
            if (preg_match("/".$registerInfo['register_keyword']."/", $data['username'], $result)) {
                return  [
                    'type'=>"resopneRegisterMember",
                    'status'=>false,
                    'msg'=>'用户名存在非法字眼：“'.$result[1].'"!',
                ];
            }
            $username = $data['username'];
            $phone = '';
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
        }
        // 判断是否存在验证码
        if (isset($this->codes[$data['con_id']]) && $this->codes[$data['con_id']] && $data['mode'] == 3) {
            if ($data['code'] != $this->codes[$data['con_id']]) {
                return [
                    'type'=>'resopneRegisterMember',
                    'status'=>false,
                    'msg'=>'验证码不争确！',
                ];
            }
        }
        // 判断邀请码是否正确
        if ($data['invitedCode'] != $registerInfo['register_check_key']) {
            return [
                'type'=>'responeRegisterMember',
                'status'=>false,
                'msg'=>'邀请码不正确！',
            ];
        }
        // 生产非重复性id
        $maxNumber = DB::name('user_user')->order('id desc')->value('id');
        if ($maxNumber <= $registerInfo['register_start_number']) {
            $maxNumber = $registerInfo['register_start_number'] + 1;
        } else {
            $maxNumber++;
        }
        $maxNumber = $this->checkId($maxNumber, $registerInfo['register_rules']);
        // 用户信息数据
        $userInfo = [
            'id'=>$maxNumber,
            'username'=>$username,
            'password'=>$data['password'],
            'gold'=>$registerInfo['register_gold'],
            'bank'=>0,
            'integral'=>$registerInfo['register_integral'],
            'head'=>$registerInfo['register_head'],
            'phonenumber'=>$phone,
            'nickname'=>$registerInfo['register_prefix'].$maxNumber,
            'signature'=>$registerInfo['register_signature'],
            'end_time'=>$registerInfo['register_end_time'],
        ];
        // 保存数据库
        if (DB::name('user_user')->insert($userInfo)) {
            // 清除验证码
            if (isset($this->codes[$data['con_id']])) {
                unset($this->codes[$data['con_id']]);
            }
            return [
                'type'=>'responeRegisterMember',
                'status'=>true,
                'wellcome'=>str_replace('[@用户]', "“".$data['username']."”", $registerInfo['register_welcome']),
            ];
        } else {
            // 清除验证码
            if (isset($this->codes[$data['con_id']])) {
                unset($this->codes[$data['con_id']]);
            }
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
    private function checkId($id, $rules)
    {
        if (stripos("|".$rules."|", "|".$id."|") !== false) {
            $id++;
            $id = $this->checkId($id, $rules);
        }
        return $id;
    }

    /**
     * 获取验证码
     */
    public function getYanZhengMa($data)
    {
        $code = rand(100000, 999999);
        $result = SMS::sendmsg($data['phone'], $code);
        if ($result) {
            // 保存产生的验证码
            $this->codes[$data['con_id']] = $code;
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
            ];
        }
    }
    
    /**
     * 获取注册规则
     */
    public function getRegisterRules($data)
    {
        $registerRules = DB::name('system_info')
        ->field('register_prefix,register_mod,register_start_number,register_keyword,register_max_length,register_check_key')
        ->find();
        return [
            'type'=>'responeGetRegisterRules',
            'rules'=>$registerRules,
        ];
    }
    /**
     * 修改个人勋章的状态
     */
    public function changeMedalStatus($data)
    {
        if (DB::name('user_medal')->where('user_id', $data['user_id'])->where('medal_id', $data['medal_id'])->update(['status'=>$data['status']])) {
            return [
                'type'=>'responeChangeMedalStatus',
                'status'=>true,
            ];
        } else {
            return [
                'type'=>'responeChangeMedalStatus',
                'status'=>false,
                'msg'=>'更新失败！'
            ];
        }
    }
    /**
     * 获取勋章列表
     */
    public function getMedalList($data)
    {
        $list = DB::name('system_ranks')->order('min asc')->select();
        $names = DB::name('system_info')->field('coin1_name,coin2_name')->find();
        $medalList = [];
        foreach ($list as $k => $v) {
            if ($v['flag'] == 0) {
                // 积分勋章
                if (!isset($medalList['sliver']['title'])) {
                    $medalList['sliver']['title'] = $names['coin2_name'].'勋章';
                }
                $medalList['sliver']['list'][] = $v;
            } elseif ($v['flag'] == 1) {
                // 金币勋章
                if (!isset($medalList['gold']['title'])) {
                    $medalList['gold']['title'] = $names['coin1_name'].'勋章';
                }
                $medalList['gold']['list'][] = $v;
            } else {
                // 特殊勋章
                if (!isset($medalList['special']['title'])) {
                    $medalList['special']['title'] = '特殊勋章';
                }
                $medalList['special']['list'][] = $v;
            }
        }
        $medalAlls[0] = $medalList['gold'];
        $medalAlls[1] = $medalList['sliver'];
        $medalAlls[2] = $medalList['special'];

        $userMedalList = DB::name('user_medal')->field('status,medal_id')->where('user_id', $data['user_id'])->select();
        // dump($userMedalList);
        $userMedalStr = '';
        foreach ($userMedalList as $k => $v) {
            $userMedalStr .= '['.$v['medal_id'].'-'.$v['status'].']';
        }
        // $userMedalList = implode('-',$userMedalList);
        return [
            'type'=>'resopneGetMedalList',
            'medalList'=>$medalAlls,
            'userMedalList'=>$userMedalStr,
        ];
    }
    /**
     * 领取好友转账
     */
    public function reciveTransfer($data)
    {
        DB::startTrans();
        try {
            // 获取转账信息
            $transferInfo = DB::name('user_transfer')->where('id', $data['transfer_id'])->find();
            // 获取用户金币信息
            $userInfo = DB::name('user_user')->field('id,gold,username')->where('id', $data['user_id'])->find();
            $remark_gold = $userInfo['gold'] + $transferInfo['gold'];
            // 更新用户金币信息
            DB::name('user_user')->where('id', $data['user_id'])->update(['gold'=>$remark_gold]);
            // 更新转账信息的收取状体
            DB::name('user_transfer')->where('id', $data['transfer_id'])->update(['status'=>1]);
            // 判断金币是否存在两位小数
            if (strpos($transferInfo['gold'], '.') === false) {
                $gold = $transferInfo['gold'].'.00';
                $str = '(@transfer#'.$transferInfo['id'].'-'.$transferInfo['gold'].'.00#transfer@)';
            } else {
                $stmp = explode(".", $transferInfo['gold']);
                $gold = $stmp[0].'.00';
                $str = '(@transfer#'.$transferInfo['id'].'-'.$stmp[0].'.00#transfer@)';
            }
            // 获取消息，更新消息收取状态
            $messageInfo = DB::name('user_message')->field('id,content')->where('content', 'like', '%'.$str.'%')->find();
            // dump($messageInfo);
            $messageInfo['content'] = 'false:'+$messageInfo['content'];
            DB::name('user_message')->update($messageInfo);
            // 保存金币流水
            $goldHistory = [
                '收账前金额'=>$userInfo['gold'],
                '收账金额'=>$transferInfo['gold'],
                '收账后金额'=>$remark_gold,
            ];
            Base::goldHistory($userInfo['username'], '收账', $goldHistory);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dump($e->getMessage());
            return [
                'status'=>false,
                'msg'=>'收账失败！',
            ];
        }
        return [
            'status'=>true,
            'gold'=>$remark_gold,
            'transfer_gold'=>$gold,
        ];
    }
    /**
     * 获取转账信息
     */
    public function getTransferInfo($data)
    {
        $info = DB::name('user_transfer')->where('id', $data['transfer_id'])->find();
        $info['addtime'] = date('Y-m-d H:i', $info['addtime']);
        return [
            'type'=>'responeGetTransferInfo',
            'info'=>$info,
        ];
    }
    /**
     * 转账
     */
    public function transferAccounts($data)
    {
        $sendUserInfo = DB::name('user_user')
        ->where('id', $data['send_id'])
        ->field('id,head,nickname,on_status,connectionID,username,gold')
        ->find();
        DB::startTrans();
        try {
            if ($sendUserInfo['gold']<$data['gold']) {
                return [
                    'type'=>'responeTransferAccounts',
                    'status'=>false,
                    'msg'=>'您当前的金币为：'.$sendUserInfo['gold'].',不足以转账。',
                    'gold'=>$sendUserInfo['gold'],
                ];
            } else {
                $remark_gold = $sendUserInfo['gold'] - $data['gold'];
                DB::name('user_user')->where('id', $data['send_id'])->update(['gold'=>$remark_gold]);
                $transferData = [
                    'send_id'=>$data['send_id'],
                    'recive_id'=>$data['recive_id'],
                    'gold'=>$data['gold'],
                    'status'=>0,
                    'remark'=>$data['remark'],
                    'addtime'=>time(),
                ];
                $id = DB::name('user_transfer')->insertGetId($transferData);
                $messageData = [
                    'send_id'=>$data['send_id'],
                    'get_id'=>$data['recive_id'],
                    'time'=>$transferData['addtime'],
                    'status'=>$data['status'],
                    'content'=>"(@transfer#".$id."-".$data['gold']."#transfer@)".$data['remark'],
                    'flag'=>0
                ];
                $msgId = DB::name('user_message')->insertGetId($messageData);
                $messageData['id'] = $msgId;
                if ($data['status'] == 2) {
                    $userInfo = [
                        'id'=>$sendUserInfo['id'],
                        'head'=>$sendUserInfo['head'],
                        'nickname'=>$sendUserInfo['nickname'],
                        'on_status'=>$sendUserInfo['on_status'],
                        'connectionID'=>$sendUserInfo['connectionID'],
                        // 'username'=>$sendUserInfo['username'],
                    ];
                    // 获取陌生人勋章信息
                    $userInfo['medals'] = DB::name('user_medal')
                        ->alias('m')
                        ->join('bc_system_ranks r', 'r.id=m.medal_id')
                        ->where('m.status', 1)
                        ->where('m.user_id', $data['send_id'])
                        ->order('flag desc,min desc')
                        ->limit(5)
                        ->column('r.src');
                } else {
                    $userInfo = '';
                }
                // 保存金币记录
                $detail = [
                    '转账前'=>$sendUserInfo['gold'],
                    '转账金额'=>$data['gold'],
                    '转账后'=>$remark_gold
                ];
                Base::goldHistory($sendUserInfo['username'], '转账', $detail);
            }
            DB::commit();
        } catch (\Exception $e) {
            dump($e->getMessage());
            DB::rollback();
            return [
                'type'=>'responeTransferAccounts',
                'status'=>false,
                'msg'=>'转账发生错误！',
                'gold'=>$sendUserInfo['gold'],
            ];
        }

        return [
            'type'=>'responeTransferAccounts',
            'status'=>true,
            'gold'=>$remark_gold,
            'msg'=>$messageData,//发送给好友的
            'userInfo'=>$userInfo,
        ];
    }
    /**
     * 获取我的回帖
     */
    public function getMyReplyList($data)
    {
        $listRows = 10;
        if (!isset($data['page'])) {
            $data['page'] = 0;
        }
        $firstRow = $data['page'] * $listRows;
        $order = isset($data['order']) ? $data['order'] : 'addtime desc';
        $list = DB::name('forum_post')
        ->alias('p')
        ->join('bc_forum_comment c', 'c.post_id=p.id', 'right')
        ->join('bc_forum_reply r', 'r.comment_id=c.id', 'left')
        ->field('p.*,FROM_UNIXTIME(p.addtime,"%Y/%m/%d %H:%i") as addtime')
        ->where('c.user_id|r.user_id', $data['user_id'])
        ->group('p.id')
        ->order($order)
        ->limit($firstRow, $listRows)
        ->select();
        return [
            'status'=>true,
            'list'=>$list
        ];
    }
    /**
     * 获取我的发帖列表
     */
    public function getMyThemeList($data)
    {
        $listRows = 10;
        if (!isset($data['page'])) {
            $data['page'] = 0;
        }
        $firstRow = $data['page'] * $listRows;
        $order = isset($data['order']) ? $data['order'] : 'addtime desc';
        $list = DB::name("forum_post")->field("*,FROM_UNIXTIME(addtime,'%Y/%m/%d %H:%i') as addtime")->where('user_id', $data['user_id'])->order($order)->limit($firstRow, $listRows)->select();
        return [
            'status'=>true,
            'list'=>$list,
        ];
    }
    /**
     * 发帖子
     */
    public function postTheme($data)
    {
        $insertValue = [
            'user_id'=>$data['user_id'],
            'zone_id'=>$data['zone_id'],
            'title'=>$data['title'],
            'content'=>$data['content'],
            'imgs'=>json_encode($data['imgs']),
            'addtime'=>time(),
            'user_name'=>$data['user_name'],
        ];
        if (DB::name('forum_post')->insert($insertValue)) {
            return [
                'status'=>true,
            ];
        } else {
            return [
                'status'=>false,
                'msg'=>'发布失败！'
            ];
        }
    }
    /**
     * 回复评论
     */
    public function ReplyComment($data)
    {
        unset($data['type']);
        unset($data['con_id']);
        $data['addtime'] = time();
        if ($id = DB::name('forum_reply')->insertGetId($data)) {
            return [
                'status'=>true,
                'id'=>$id,
                'addtime'=>$data['addtime']
            ];
        } else {
            return [
                'status'=>false,
                'msg'=>'回复失败！'
            ];
        }
    }

    /**
     * 点赞评论
     */
    public function zanComment($data)
    {
        if (DB::name('forum_comment')->where('id', $data['comment_id'])->setInc('zan')) {
            return [
                'status'=>true,
            ];
        } else {
            return [
                'status'=>false,
            ];
        }
    }
    /**
     * 发表评论
     */
    public function themeComment($data)
    {
        $saveData = [
            'user_id'=>$data['send_id'],
            'post_id'=>$data['post_id'],
            'content'=>$data['content'],
            'addtime'=>time(),
            'user_name'=>$data['nickname'],
            'imgs'=>json_encode($data['imgs'])
        ];
        if ($id = DB::name('forum_comment')->insertGetId($saveData)) {
            DB::name('forum_post')->where('id', $data['post_id'])->setInc('reply');
            return [
                'status'=>true,
                'type'=>'resopneThemeComment',
                'addtime'=>date('m-d H:i', $saveData['addtime']),
                'id'=>$id
            ];
        } else {
            return [
                'status'=>false,
                'msg'=>'发表失败',
            ];
        }
    }
    /**
     * 获取帖子评论
     */
    public function getComment($data)
    {
        $listRows = 150;
        if (!isset($data['page'])) {
            $data['page'] = 0;
        }
        if ($data['page'] <= 0) {
            // 主题浏览量自增1
            DB::name('forum_post')->where('id', $data['post_id'])->setInc('visitor');
            // 获取主题信息
            $postInfo = DB::name('forum_post')
            ->alias('p')
            ->join('bc_user_user u', 'u.id=p.user_id')
            ->field('u.head,p.*')
            ->where('p.id', $data['post_id'])
            ->find();
            if ($postInfo['imgs']) {
                $postInfo['imgs'] = json_decode($postInfo['imgs']);
            }
            // 获取分区信息
            $zoneInfo = DB::name('forum_zone')->where('id', $data['zone_id'])->find();
        } else {
            $postInfo = false;
            $zoneInfo = false;
        }
        if (!isset($data['order'])) {
            $data['order'] = 'desc';
        }
        $firstRow = $data['page'] * $listRows;
        // 获取评论列表
        $commentList = DB::name('forum_comment')
        ->alias('c')
        ->join('bc_user_user u', 'u.id=c.user_id')
        ->field('c.*,u.head')
        ->where('c.post_id', $data['post_id'])
        ->order('c.addtime '.$data['order'])
        ->limit($firstRow, $listRows)
        ->select();
        foreach ($commentList as $k => $v) {
            $commentList[$k]['imgs'] = json_decode($v['imgs']);
            // 获取每个评论的回复
            $commentList[$k]['replys'] = DB::name('forum_reply')
            ->where('comment_id', $v['id'])
            ->order('addtime asc')
            ->select();
            $commentList[$k]['addtime'] = date('m-d H:i', $v['addtime']);
        }
        // dump($commentList);
        return [
            'type'=>'resopneGetComment',
            'commentList'=>$commentList,
            'zoneInfo'=>$zoneInfo,
            'postInfo'=>$postInfo,
        ];
    }
    /**
     * 获取帖子列表
     */
    public function getPostList($data)
    {
        $listRows = 5;
        if (!isset($data['page'])) {
            $data['page'] = 0;
        }
        if ($data['page'] == 0) {
            // 获取分区信息
            $zoneInfo = DB::name('forum_zone')->where('id', $data['zone_id'])->find();
        } else {
            $zoneInfo = false;
        }
        $firstRow = $data['page'] * $listRows;
        // 获取主题列表
        $postList = DB::name('forum_post')
        ->alias('p')
        ->join('bc_user_user u', 'u.id=p.user_id')
        ->field('u.head,p.*')
        ->where('zone_id', $data['zone_id'])
        ->order('addtime desc')
        ->limit($firstRow, $listRows)
        ->select();
        foreach ($postList as $key => $vo) {
            if ($vo['imgs']) {
                $postList[$key]['imgs'] = json_decode($vo['imgs']);
            }
        }
        return [
            'type'=>'resopneGetPostList',
            'postList'=>$postList,
            'zoneInfo'=>$zoneInfo
        ];
    }
    /**
     * 获取论坛列表
     */
    public function getThemeList($data)
    {
        $listRows = 5;
        if (!isset($data['page'])) {
            $data['page'] = 0;
        }
        $firstRow = $data['page'] * $listRows;
        // 获取分区列表
        $zoneList = DB::name('forum_zone')->limit($firstRow, $listRows)->select();
        foreach ($zoneList as $key => $vo) {
            // 统计分区中的主题数目
            $zoneList[$key]['COUNT'] = DB::name('forum_post')->where('zone_id', $vo['id'])->count();
        }
        return [
            'type'=>'ResponeGetThemeList',
            'zoneList'=>$zoneList
        ];
    }
    /**
     * 获取收到的红包列表
     */
    public function getReciveRedList($data)
    {
        $listRows = 1;
        // 页数
        $firstRow = $data['page'] * $listRows;
        // 年份
        $startTime = strtotime($data['year'].'-1-1');
        $endTime = strtotime(($data['year']+1).'-1-1');
        if (!$data['page']) {
            // 计算发了多少个红包
            $totalNum = DB::name('user_red')
            ->where('detail', 'like', '%"'.$data['send_id'].'":{%')
            ->where('send_time', 'between', [$startTime, $endTime])
            ->count();
            // 统计发了多少金币
            $totalSum = DB::name('user_red')
            ->where('detail', 'like', '%"'.$data['send_id'].'":{%')
            ->where('send_time', 'between', [$startTime, $endTime])
            ->sum('money');
            $returnValue['totalNum'] = $totalNum;
            $returnValue['totalSum'] = $totalSum;
        }
        // 获取红包信息
        $list = DB::name('user_red')->field('id,type,money,recive_num,recived_num,send_time')
        ->where('detail', 'like', '%"'.$data['send_id'].'":{%')
        ->where('send_time', 'between', [$startTime, $endTime])
        ->order('send_time desc')
        ->limit($firstRow, $listRows)
        ->select();
        // 格式处理
        foreach ($list as $k => $v) {
            if ($v['type'] == 1) {
                $list[$k]['type'] = '个人红包';
            } else {
                $list[$k]['type'] = '群发红包';
            }
            $list[$k]['send_time'] = date('m-d', $v['send_time']);
        }
        $returnValue['type'] = 'responeGetReciveRedList';
        $returnValue['list'] = $list;
        return $returnValue;
    }
    /**
     * 获取发送的红包列表
     */
    public function getSendRedList($data)
    {
        $listRows = 1;
        // 页数
        $firstRow = $data['page'] * $listRows;
        // 年份
        $startTime = strtotime($data['year'].'-1-1');
        $endTime = strtotime(($data['year']+1).'-1-1');
        if (!$data['page']) {
            // 计算发了多少个红包
            $totalNum = DB::name('user_red')->where('send_id', $data['send_id'])
            ->where('send_time', 'between', [$startTime, $endTime])
            ->count();
            // 统计发了多少金币
            $totalSum = DB::name('user_red')->where('send_id', $data['send_id'])
            ->where('send_time', 'between', [$startTime, $endTime])
            ->sum('money');
            $returnValue['totalNum'] = $totalNum;
            $returnValue['totalSum'] = $totalSum;
        }
        // 获取红包信息
        $list = DB::name('user_red')
        ->field('id,type,money,recive_num,recived_num,send_time')
        ->where('send_id', $data['send_id'])
        ->where('send_time', 'between', [$startTime, $endTime])
        ->order('send_time desc')
        ->limit($firstRow, $listRows)
        ->select();
        // 格式处理
        foreach ($list as $k => $v) {
            if ($v['type'] == 1) {
                $list[$k]['type'] = '个人红包';
            } else {
                $list[$k]['type'] = '群发红包';
            }
            $list[$k]['send_time'] = date('m-d', $v['send_time']);
        }
        $returnValue['type'] = 'responeGetSendRedList';
        $returnValue['list'] = $list;
        return $returnValue;
    }

    /**
     * 修改登录密码
     */
    public function changeLoginPassword($data)
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
    public function changeSignature($data)
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
    public function changeNickName($data)
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
    public function settingChange($data)
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
    public function getGoldFromBank($data)
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
    public function storageGold($data)
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
     * 红包详情
     */
    public function RedDetail($data)
    {
        $redInfo = DB::name('user_red')
        ->alias('r')
        ->join('bc_user_user u', 'u.id=r.send_id')
        ->where('r.id', $data['red_id'])
        ->field('r.*,u.head,u.nickname')
        ->find();
        if ($redInfo['detail']) {
            $redInfo['detail'] = json_decode($redInfo['detail'], true);
            foreach ($redInfo['detail'] as $k => $v) {
                $redInfo['detail'][$k]['time'] = date('m-d H:i:s', $v['time']);
            }
        }
        return $redInfo;
    }
    /**
     * 登陆处理
     */
    public function logining($data)
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
                'msg'=>'账号不正确'
            ];
        }
        // 判断用户是否被查封
        if ($user['status']) {
            return [
                'code'=>4,
                'msg'=>'该账号已经被查封！'
            ];
        }
        // 判断用户是否已经上线
        if ($user['on_status']) {
            return [
                'code'=>5,
                'msg'=>'该账号已经登陆！'
            ];
        }
        //用户登录成功给前台返回1标志；同时设定登录时间
        if ($user['password']==$data['password']) {
            $logintime = time();
            // 缓存用户信息
            // Session::set('userInfo', $user);
            // 保存加密后的id到客户的cookie中
            // Cookie::set('uid', md5($user['id']));
            //更新用户最后登录时间和登陆状态
            // $data = [
            //     'lasttime'=>$logintime,
            //     'on_status'=>1,
            //     'id'=>$user['id'],
            //     'connectionID'=>$data['con_id'],
            // ];
            // Db::name('user_user')->update($data);
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
    public function handle($data)
    {
        // 检查更新用户的勋章列表信息
        Base::updateMedal($data['send_id']);
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
         ->join('bc_user_user u', '(u.id=m.send_id or u.id=m.get_id) and u.id <> '.$data['send_id'])
         ->field('u.id,u.head,u.nickname,u.username,u.on_status,u.connectionID')
         ->where('m.get_id|m.send_id', $data['send_id'])
         ->where('m.status', 2)
         ->where('m.time', 'gt', $date)
         ->where('m.send_id|m.get_id', 'notin', $friendIds)
         ->order('u.on_status,m.time desc')
         ->group('u.id')
         ->select();
        //  dump("++++++++++++result++++++++++\n");
        //  dump($strangeInfo);
        //  dump("++++++++++++result++++++++++\n");
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

        //
        $messages = DB::name('user_message')->where(['get_id'=>$data['send_id'],'flag'=>0])->select();
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
        return [
            'userMsg'=>$msgs,
            'friendList'=>[
                'friends'=>$friendList,
                'strange'=>$strangeFriends,
                'system'=>$systemList,
            ]
        ];
    }
    /**
     * 获取app信息
     */
    public function getAppInfo($data)
    {
        // 获取系统广播消息
        $sysNews = DB::name('system_news')->where('status', 1)->column('content');
        // 获取app信息
        $info = DB::name('system_info')->field('app_name,logo_src,error_str,coin1_name,coin2_name')->find();
        return [
            "sysNews"=>$sysNews,
            "appInfo"=>$info,
            "type"=>"appInfoAndSysNews"
        ];
    }

    /**
     * 领取红包
     */
    public function reciveRedBagFromFriend($data)
    {
        DB::startTrans();
        try {
            // 获取红包信息
            $info = DB::name('user_red')->field('send_time,money,status,detail,type,recive_num,recived_num')->where('id', $data['red_id'])->where('get_id', $data['send_id'])->find();
            if ((time()-$info['send_time'])>24*60*60) {
                // 红包超过24小时
                return [
                    'status'=>false,
                    'errMsg'=>'红包已过时'
                ];
            } else {
                // status为1表示已领取，0表示未领取
                if ($info['status']) {
                    if ($info['type'] == 1) {
                        // 个人红包
                        return [
                            'status'=>false,
                            'errMsg'=>'红包已领取'
                        ];
                    } else {
                        // 群发红包
                        return [
                            'status'=>false,
                            'errMsg'=>'红包已领完'
                        ];
                    }
                } else {
                    if ($info['type'] == 1) {
                        // 个人红包
                        // 获取用户当前剩余金币和账号
                        $user = DB::name("user_user")->where('id', $data['send_id'])->field('username,gold,head,nickname')->find();
                        if ($info['detail']) {
                            $info['detail'] = json_decode($info['detail'], true);
                        }
                        $info['detail'][$data['send_id']] = [
                            'head'=>$user['head'],
                            'nickname'=>$user['nickname'],
                            'time'=>time(),
                            'gold'=>$info['money'],
                        ];
                        // 修改红包领取状态 和 红包详情
                        DB::name('user_red')->where('id', $data['red_id'])->update(['detail'=>json_encode($info['detail']), 'status'=>1, 'recived_num'=>($info['recived_num']+1)]);
                        // 保存红包金额到用户金币池
                        DB::name('user_user')->where('id', $data['send_id'])->setInc('gold', $info['money']);
                        
                        // 金币记录详情
                        $detail = [
                            '领红包前'=>$user['gold'],
                            '红包金额'=>$info['money'],
                            '领红包后'=>$user['gold']+$info['money']
                        ];
                        // 保存金币记录
                        Base::goldHistory($user['username'], '领红包', $detail);
                        // 修改红包消息的接收状态
                        $mesgInfo = DB::name('user_message')
                        ->field('id,content')
                        ->where('send_id', $data['red_send_id'])
                        ->where('get_id', $data['send_id'])
                        ->where('content', 'like', '(@redbag'.$data['red_id'].'redbag@)%')
                        ->find();
                        $mesgInfo['content'] = 'false:' . $mesgInfo['content'];
                        DB::name('user_message')->update($mesgInfo);
                        $retrunValue = [
                            'status'=>true,
                            'gold'=>$info['money']+$user['gold'],
                        ];
                    } else {
                        // 群发红包
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            dump("++++++++++++++++++++++++++++++++++\n");
            dump($e->getMessage());
            dump("++++++++++++++++++++++++++++++++++\n");
            DB::rollback();
            return [
                'status'=>false,
                'errMsg'=>'网络堵塞，请稍后再试！'
            ];
        }
        return $retrunValue;
    }
    /**
     * 保存红包信息
     */
    public function sendRedBagToFriend($data)
    {
        DB::startTrans();
        try {
            // 红包记录
            $redbagrecord = [
                'send_id'=>$data['send_id'],
                'get_id'=>$data['get_id'],
                'send_time'=>$data['send_time'],
                'status'=>0, //标志是否查收，0未收；1已收
                'remark'=>$data['remark'],
                'money'=>$data['gold'],
                'type'=>$data['redType'],
                'recive_num'=>$data['recive_num']
            ];
            // 红包信息id
            $id = DB::name('user_red')->insertGetId($redbagrecord);

           
            // 获取用户当前金币
            $sendUserInfo = DB::name('user_user')
            ->where('id', $data['send_id'])
            ->field('id,head,nickname,on_status,connectionID,username,gold')
            ->find();
            // 扣取用户金币
            $remark_gold = $sendUserInfo['gold'] - $data['gold'];
            // 更新用户的金币数目
            DB::name('user_user')->where('id', $data['send_id'])->update(['gold'=>$remark_gold]);
            if ($data['redType'] == 1) {
                // 个人红包
                // 添加聊天记录
                 // 聊天记录
                $msgData = [
                    'send_id'=>$data['send_id'],
                    'get_id'=>$data['get_id'],
                    'time'=>$data['send_time'],
                    'status'=>$data['status'],
                    'content'=>'(@redbag'.$id.'redbag@)' . $data['remark'],
                    'flag'=>0,
                ];
                DB::name("user_message")->insert($msgData);
                if ($data['status']==2) {
                    // 陌生人消息，获取陌生人信息
                    $userInfo = [
                        'id'=>$sendUserInfo['id'],
                        'head'=>$sendUserInfo['head'],
                        'nickname'=>$sendUserInfo['nickname'],
                        'on_status'=>$sendUserInfo['on_status'],
                        'connectionID'=>$sendUserInfo['connectionID'],
                        // 'username'=>$sendUserInfo['username'],
                    ];
                    // 获取陌生人勋章信息
                    $userInfo['medals'] = DB::name('user_medal')
                    ->alias('m')
                    ->join('bc_system_ranks r', 'r.id=m.medal_id')
                    ->where('m.status', 1)
                    ->where('m.user_id', $data['send_id'])
                    ->order('flag desc,min desc')
                    ->limit(5)
                    ->column('r.src');
                } else {
                    $userInfo = '';
                }
            } else {
                // 群发红包
                $msgData = [
                    'id'=>'',
                    'send_id'=>$data['send_id'],
                    'send_time'=>$data['send_time'],
                    'status'=>1,
                    'content'=>'(@redbag'.$id.'redbag@)' . $data['remark'],
                ];
                $mid = DB::name('chat_room')->insertGetId($msgData);
                $userInfo = '';
            }
            // 保存金币记录
            $detail = [
                '发红包前'=>$sendUserInfo['gold'],
                '红包金额'=>$data['gold'],
                '发红包后'=>$remark_gold
            ];
            Base::goldHistory($sendUserInfo['username'], '发红包', $detail);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return [
                'code'=>101,
                'status'=>false,
                'errMsg'=>'红包发送失败！'
            ];
        }
        return [
            'code'=>0,
            'status'=>true,
            'red_id'=>$id,
            'send_info'=>$msgData,
            'userInfo'=>$userInfo
        ];
    }
    /**
     * 修改交易密码
     */
    public function changeTrading($data)
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
     * 删除陌生人信息
     */
    public function delStrange($data)
    {
        if (DB::name('user_message')->where('get_id|send_id', $data['get_id'])->where('send_id|get_id', $data['send_id'])->delete()) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * 聊天记录保存
     */
    private function chatRecord($data)
    {
        $insertValue = [
            'send_id'=>$data['send_id'],
            'get_id'=>$data['get_id'],
            'time'=>$data['time'],
            'content'=>$data['content'],
            'status'=>$data['status'],
            'flag'=>0
        ];
        if (DB::name('user_message')->insert($insertValue)) {
            $insertValue['chatType'] = true;
            if ($data['status']==2) {
                // 陌生人消息，获取陌生人信息
                $userInfo = DB::name('user_user')
                ->field('id,head,nickname,on_status')
                ->where('id', $data['send_id'])
                ->find();
                // 获取陌生人勋章信息
                $userInfo['medals'] = DB::name('user_medal')
                ->alias('m')
                ->join('bc_system_ranks r', 'r.id=m.medal_id')
                ->where('m.status', 1)
                ->where('m.user_id', $data['send_id'])
                ->order('flag desc,min desc')
                ->limit(5)
                ->column('r.src');
                return [
                    'msg'=>$insertValue,
                    'userInfo'=>$userInfo
                ];
            } else {
                return $insertValue;
            }
        } else {
            return false;
        }
    }

    /**
     * 修改系统消息的查阅状态
     */
    private function changeSystemReadStatus($data)
    {
        if (DB::name("user_message")->where('get_id', $data['send_id'])->where('status', 'gt', 2)->update(['flag'=>1])) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * 好友请求处理方法
     */
    public function AgreeFriendRequest($data)
    {
        DB::startTrans();
        try {
            // 更新消息状态
            DB::name('user_message')->where('id', $data['msg_id'])->update(['status'=>$data['msg_status'],'flag'=>1]);
            if ($data['msg_status']==6) {
                // 同意好友申请
                $friendData = [
                    [
                        'user_id'=>$data['send_id'],
                        'friend_id'=>$data['friend_id']
                    ],
                    [
                        'user_id'=>$data['friend_id'],
                        'friend_id'=>$data['send_id']
                    ]
                ];
                // 保存好友关系列表
                DB::name('user_friends')->insertAll($friendData);
                // 发送给好友的消息
                $insertValue = [
                    'send_id'=>$data['send_id'],
                    'get_id'=>$data['friend_id'],
                    'status'=>0,
                    'content'=>'恭喜你们已经成为好朋友',
                    'time'=>time(),
                    'flag'=>0
                ];
                $id = DB::name('user_message')->insertGetId($insertValue);
                $insertValue['id'] = $id;
                // 将消息和用户信息发送给好友
                $insertValue1 = [
                    'msg'=>$insertValue,
                    'fInfo'=>[
                        'id'=>$data['send_id'],
                        'head'=>$data['head'],
                        'on_status'=>1,
                        'nickname'=>$data['nickname'],
                        'username'=>$data['username'],
                        'connectionID'=>$data['con_id']
                    ]
                ];
                // 返回给用户的消息
                $insertValue2 = [
                    'send_id'=>$data['friend_id'],
                    'get_id'=>$data['send_id'],
                    'status'=>0,
                    'content'=>'恭喜你们已经成为好朋友',
                    'time'=>time(),
                    'flag'=>0
                ];
                $id = DB::name('user_message')->insertGetId($insertValue2);
                $insertValue2['id'] = $id;
            } else {
                // 拒绝好友申请
                $insertValue1 = [
                    'send_id'=>$data['send_id'],
                    'get_id'=>$data['friend_id'],
                    'status'=>5,
                    'content'=>$data['nickname'].'拒绝了你的好友申请！',
                    'time'=>time(),
                    'flag'=>0
                ];
                $id = DB::name('user_message')->insertGetId($insertValue1);
                // 发送给对方的拒绝提醒消息
                $insertValue1 = [
                    'id'=>$data['send_id'],
                    'head'=>$data['head'],
                    'nickname'=>$data['nickname'],
                    'username'=>$data['username'],
                    'connectionID'=>$data['con_id'],
                    'on_status'=>1,
                    'content'=>$data['nickname']."拒绝了你的好友申请！",
                    'flag'=>0,
                    'status'=>5,
                    'mid'=>$id
                ];
                $insertValue2 =  '';
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
        return [
            'user_data'=>$insertValue2,
            'friend_data'=>$insertValue1
        ];
    }


    /**
     * 删除消息
     */
    public function DeleteMessage($data)
    {
        if (DB::name('user_message')->where('id', $data['msg_id'])->delete()) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * 删除好友
     */
    public function deleteFriend($data)
    {
        DB::startTrans();
        try {
            // 双向删除好友信息
            DB::name('user_friends')->where(['user_id'=>$data['send_id'],'friend_id'=>$data['friend_id']])->delete();
            DB::name('user_friends')->where(['user_id'=>$data['friend_id'],'friend_id'=>$data['send_id']])->delete();
            // 删除两者的所有消息记录
            DB::name('user_message')->where(['send_id'=>$data['send_id'],'get_id'=>$data['friend_id']])->delete();
            DB::name('user_message')->where(['send_id'=>$data['friend_id'],'get_id'=>$data['send_id']])->delete();
            // 插入删除好友消息记录，发送给对方
            $insertValue = [
                'send_id'=>$data['send_id'],
                'get_id'=>$data['friend_id'],
                'content'=>$data['nickname']."已经把你从好友列表删除！",
                'time'=>time(),
                'status'=>5,
                'flag'=>0,
            ];
            $id = DB::name('user_message')->insertGetId($insertValue);
            $insertValue['mid'] = $id;
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
        return $insertValue;
    }
    /**
     * 下线处理
     */
    public function downLine($data)
    {
        // 修改用户在线状态和链接标识符为0
        DB::name('user_user')->where('id', $data['send_id'])->update(['on_status'=>0,'connectionID'=>0]);
        // 获取用户朋友id
        $friendIds = DB::name('user_friends')
        ->alias('f')
        ->join('bc_user_user u', 'u.id=f.friend_id')
        ->where('f.user_id', $data['send_id'])
        ->where('u.connectionID', 'gt', 0)
        ->field('u.id,u.connectionID')
        ->select();
        // 获取用户陌生人id
        $strangeIds = Db::name("user_message")
        ->alias('m')
        ->join('bc_user_user u', '(u.id=m.send_id||u.id=m.get_id)&&u.id!='.$data['send_id'])
        ->where('m.get_id|m.send_id', $data['send_id'])
        ->where('m.status', 2)
        ->where('m.time', '>', (time()-30*24*60*60))
        ->where('m.send_id|m.get_id', 'notin', $friendIds)
        ->where('u.connectionID', 'gt', 0)
        ->order('u.on_status,m.time desc')
        ->group('m.send_id')
        ->field('u.id,u.connectionID')
        ->select();
        // 获取系统消息的用户状态
        $systemIds = Db::name("user_message")
        ->alias('m')
        ->join('user_user u', 'u.id=m.send_id')
        ->where('m.get_id', $data['send_id'])
        ->where('m.status', 'in', [3,4])
        ->where('m.time', '>', (time()-7*24*60*60))
        ->where('u.connectionID', 'gt', 0)
        ->order('m.time desc')
        ->field('u.id,u.connectionID')
        ->select();
        return [
            'friends'=>$friendIds,
            'strange'=>$strangeIds,
            'system'=>$systemIds
        ];
    }

    public function addFriendRequest($data)
    {
        // 判断用户是否开启验证
        $frvalidation = DB::name('user_user')->where('id', $data['get_id'])->value('frvalidation');
        if (!$frvalidation) {
            // 没有开启好友验证，写入好友关系列表
            $friend = [
                [
                    "user_id"=>$data['get_id'],
                    "friend_id"=>$data['send_id']
                ],
                [
                    "user_id"=>$data['send_id'],
                    "friend_id"=>$data['get_id']
                ]
            ];
        }
        $data['time'] = time();
        $data['flag'] = 0;
        unset($data['type']);
        unset($data['con_id']);
        unset($data['fcon_id']);
        try {
            $send_info = DB::name('user_user')->field('id,head,nickname,username,on_status,connectionID')->where('id', $data['send_id'])->find();
            if (isset($friend) && !empty($friend)) {
                // 没有开启好友验证
                DB::name('user_friends')->insertAll($friend);
                // 获取好友信息
                $get_info = DB::name('user_user')->field('id,head,nickname,username,on_status,connectionID')->where('id', $data['get_id'])->find();
                $data['content'] = '恭喜你们已经成为好朋友！';
                $data['status'] = 0;
                // 添加聊天记录
                DB::name('user_message')->insert($data);
                $retrunValue = [
                    'data'=>$data,
                    'send_info'=>$send_info,
                    'get_info'=>$get_info
                ];
            } else {
                // 开启好友验证
                $id = DB::name('user_message')->insertGetId($data);
                $data['mid'] = $id;
                $retrunValue = [
                    'data'=>$data,
                    'send_info'=>$send_info
                ];
            }
        } catch (\Exception $e) {
            return false;
        }
        return $retrunValue;
    }
    /**
     * 查找朋友
     */
    public function searchFriends($data)
    {
        // dump("user[(".$data['send_id'].")".$data['nickname']."]:".$data['msg']);
        $firstRow = 0;
        $listRows = 5;
        $keyword = $data['fname'];
        // 获取用户的朋友id
        $friendIds = DB::name('user_friends')->where('user_id', $data['send_id'])->column('friend_id');
        // 计算满足条件的陌生人数
        $num = DB::name('user_user')
        ->where("username|phonenumber|nickname", "like", "%".$keyword."%")
        ->where('id', 'notin', $friendIds)
        ->where('id', 'neq', $data['send_id'])
        ->count();
        if (!$num) {
            return [
                'totalNum'=>$num,
                'searchResult'=>[]
            ];
        }
        // 判断是否翻页
        if (isset($data['page'])&&$data['page']) {
            $firstRow = $data['page'] * $listRows;
        }
        // 获取满足条件的陌生人信息
        $resultList = DB::name('user_user')
        ->alias('u')
        ->leftJoin('user_message m', 'm.get_id=u.id&&m.status=3&&m.send_id='.$data['send_id'])
        ->field('u.id,u.head,u.nickname,u.username,u.on_status,u.connectionID,m.status,m.time')
        ->where("u.username|u.phonenumber|u.nickname", "like", "%".$keyword."%")
        ->where('u.id', 'notin', $friendIds)
        ->where('u.id', 'neq', $data['send_id'])
        ->group('u.id')
        ->limit($firstRow, $listRows)
        ->select();
        
        // 获取勋章信息
        foreach ($resultList as $key => $val) {
            // 判断是否发送过好友请求
            if ($val['status']==3) {
                // 判断是否已经超过7天
                if ($val['time'] < (time()-7*24*60*60)) {
                    $resultList[$key]['status'] = 0;
                }
            }
            $resultList[$key]['medals'] = DB::name('user_medal')
            ->alias('m')
            ->join('system_ranks r', 'm.medal_id=r.id')
            ->where('m.status', 1)
            ->where('user_id', $val['id'])
            ->limit(5)
            ->order('m.id desc')
            ->column('r.src');
        }
        return [
            'totalNum'=>$num,
            'rearchResult'=>$resultList,
            'listRows'=>$listRows
        ];
    }
    /**
     * 更新信息的阅读状态
     */
    public function updateMsgReadStatus($data)
    {
        try {
            dump("updateReadStatus\n");
            DB::name('user_message')
            ->where('send_id', $data['send_id'])
            ->where('get_id', $data['get_id'])
            ->where('status', '<=', 2)
            ->update(['flag'=>1]);
        } catch (\Exception $e) {
            dump("error:".$e->message."\n");
            return false;
        }
        return true;
    }
}
