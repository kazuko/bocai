<?php
namespace app\Server\controller;

use app\Server\controller\DataProcer;

class Deal
{
    /**
     * 获取发帖规则
     */
    public static function getPostRules($connection, $data, $userCons)
    {
        $result = DataProcer::getPostRules($data);
        $connection->send(json_encode($result));
    }

    /**
     * 转账过期返还
     */
    public static function giveBackTransfer($connection, $data, $userCons)
    {
        if ($data['con_id']) {
            if (isset($userCons[$data['con_id']]['con']) && $userCons[$data['con_id']]['con'] && isset($userCons[$data['con_id']]['uid']) && $userCons[$data['con_id']]['uid'] == $data['uid']) {
                // $data['systemNotice']['content'] = str_replace('/', '\\', $data['systemNotice']['content']);
                $userCons[$data['con_id']]['con']->send(json_encode([
                    'type'=>'giveBackTransferNotice',
                    'fid'=>$data['fid'],
                    'tid'=>$data['tid'],
                    'gold'=>$data['gold'],
                    'systemNotice'=>$data['systemNotice']
                ]));
            }
        }
        $fcon_id = $data['fcon_id'];
        if ($fcon_id) {
            if (isset($userCons[$fcon_id]['uid']) && $userCons[$fcon_id]['uid'] == $data['fid']) {
                $userCons[$fcon_id]['con']->send(json_encode([
                    'type'=>'giveBackTransferNotice',
                    'tid'=>$data['tid'],
                    'fid'=>$data['uid'],
                    'gold'=>0,
                ]));
            } else {
                // dump('HHHHH');
            }
        } else {
            // dump('GGGGGG');
        }
    }

    /**
     * 抢红包，群发红包
     */
    public static function chatRoomRedbag($connection, $data, $userCons)
    {
        $result = DataProcer::chatRoomRedbag($data);
        $connection->send(json_encode($result));
    }
    
    /**
     * 红包退还消息
     */
    public static function giveBackRedbag($connection, $data, $userCons)
    {
        // 判断用户是否在线
        if ($data['con_id']) {
            // 判断当前用户是否为发红包的用户
            if (isset($userCons[$data['con_id']]['con']) && $userCons[$data['con_id']]['con'] && isset($userCons[$data['con_id']]['uid']) && $userCons[$data['con_id']]['uid'] == $data['uid']) {
                $userCons[$data['con_id']]['con']->send(json_encode([
                    'type'=>'giveBackRedbag',
                    'gold'=>$data['gold'],
                    'red_id'=>$data['red_id'],
                    'fid'=>$data['fid']
                ]));
            }
        }
        if ($data['fid'] && $data['red_type'] == 1) {
            $fcon_id = DataProcer::getUserConnectionID($data['fid']);
            // dump("======================");
            // dump($fcon_id);
            if ($fcon_id) {
                // dump($userCons[$fcon_id]['uid']);
                // dump($data['fid']);
                // if(isset($userCons[$fcon_id]['con'])){
                //     dump($userCons[$fcon_id]['con']);
                // }
                if (isset($userCons[$fcon_id]['uid']) && $userCons[$fcon_id]['uid'] == $data['fid']) {
                    // dump($data);
                    $userCons[$fcon_id]['con']->send(json_encode([
                        'type'=>'changeRedbagStatus',
                        'red_id'=>$data['red_id'],
                        'code'=>1,
                        'send_id'=>$data['uid'],
                    ]));
                } else {
                    // dump('HHHHH');
                }
            } else {
                // dump('GGGGGG');
            }
            // dump("======================");
        } elseif ($data['red_type'] == 2) {
            if (!empty($userCons)) {
                // dump($data);
                // dump($userCons);
                foreach ($userCons as $k => $v) {
                    // echo "userID:".$v['uid']."; rType:".$v['rType']."\n";
                    if (isset($v['rType']) && $v['rType'] && isset($v['con']) && $v['con']) {
                        $v['con']->send(json_encode([
                            'type'=>'changeRedbagStatus',
                            'red_id'=>$data['red_id'],
                            'code'=>2,
                        ]));
                    }
                }
            }
        }
    }

    /**
     * 获取个人聊天记录
     */
    public static function getChatRecords($connection, $data, $userCons)
    {
        $result = DataProcer::getChatRecords($data);
        $connection->send(json_encode($result));
    }
    /**
     * 用户离开聊天室通知
     */
    public static function leaveChatRoom($connection, $data, $userCons)
    {
        try {
            $userCons[$data['con_id']]['rType'] = false;
        } catch (\Exception $e) {
            dump("leaveChatRoom->catch::".$e->getMessage()."; line:".$e->getLine());
        }
    }
    /**
     * 发送聊天信息（公共聊天室）
     */
    public static function ChatRoomMsg($connection, $data, $userCons)
    {
        $result = DataProcer::ChatRoomMsg($data);
        // dump();
        $connection->send(json_encode($result));
        if ($result['status'] && !empty($userCons)) {
            $data['msg']['id'] = $result['id'];
            dump("sssssssssssss");
            dump(count($userCons));
            dump($userCons);
            dump("sssssssssssss");
            foreach ($userCons as $k => $v) {
                if (isset($v['uid'])) {
                    echo "userID:".$v['uid'].";rType:".$v['rType']."\n";
                } else {
                    echo "nonononononono";
                }
                if (isset($v['rType']) && $v['rType'] && $k != $data['con_id'] && isset($v['con']) && $v['con']) {
                    $v['con']->send(json_encode([
                        'type'=>'ChatRoomMsg',
                        'msg'=>$data['msg'],
                    ]));
                }
            }
        }
    }
    /**
     * 获取聊天室聊天记录
     */
    public static function getChatRoomMsgList($connection, $data, $userCons)
    {
        $result = DataProcer::getChatRoomMsgList($data);
        $connection->send(json_encode($result));
    }

    /**
     * 注册功能
     */
    public static function registerMember($connection, $data, $userCons)
    {
        $result = DataProcer::registerMember($data);
        $connection->send(json_encode($result));
    }
    /**
     * 获取验证码
     */
    public static function getYanZhengMa($connection, $data, $userCons)
    {
        $result = DataProcer::getYanZhengMa($data);
        $connection->send(json_encode($result));
    }
    /**
     * 获取注册规则
     */
    public static function getRegisterRules($connection, $data, $userCons)
    {
        $result = DataProcer::getRegisterRules($data);
        $connection->send(json_encode($result));
    }
    /**
     * 发广播，直接发送，不保存记录
     */
    public static function sendRadio($connection, $data, $userCons)
    {
        $connection->send(json_encode([
            'type'=>'resopneSendRadio',
            'status'=>true,
            'radiotext'=>[
                'content'=>$data['content'],
                'send_user'=>$data['send_name'],
                'send_id'=>$data['send_id'],
            ],
        ]));
        if (!empty($userCons)) {
            // 向所有用户推送广播消息
            foreach ($userCons as $key => $vo) {
                if (isset($vo['con']) && $vo['con'] && isset($vo['broadcast']) && $vo['broadcast'] && $vo['con'] != $connection) {
                    $vo['con']->send(json_encode([
                        'type'=>'radioText',
                        'content'=>$data['content'],
                        'send_user'=>$data['send_name'],
                        'send_id'=>$data['send_id'],
                    ]));
                }
            }
        }
    }
    /**
     * 修改个人勋章状态
     */
    public static function changeMedalStatus($connection, $data, $userCons)
    {
        $result = DataProcer::changeMedalStatus($data);
        $connection->send(json_encode($result));
    }
    /**
     * 获取勋章列表
     */
    public static function getMedalList($connection, $data, $userCons)
    {
        $result = DataProcer::getMedalList($data);
        $connection->send(json_encode($result));
    }
    /**
     * 领取转账
     */
    public static function reciveTransfer($connection, $data, $userCons)
    {
        $result = DataProcer::reciveTransfer($data);
        dump($result);
        // 想当前用户反馈结果
        $connection->send(json_encode($result));
        if ($result['status']) {
            if ($data['send_con']) {
                // 若发送方在线，则向发送方发送被领取提醒
                if (isset($userCons[$data['send_con']]['uid']) && $userCons[$data['send_con']]['uid']) {
                    $userCons[$data['send_con']]['con']->send(json_encode([
                        'type'=>'recivedTransferNotice',
                        'friend_id'=>$data['user_id'],//朋友id
                        'transfer_id'=>$data['transfer_id'],//转账信息id
                        'transfer_gold'=>$result['transfer_gold'], //转账金额（带小数点）
                    ]));
                }
            }
        }
    }
    /**
     * 获取转账信息
     */
    public static function getTransferInfo($connection, $data, $userCons)
    {
        $result = DataProcer::getTransferInfo($data);
        $connection->send(json_encode($result));
    }
    /**
     * 转账功能
     */
    public static function transferAccounts($connection, $data, $userCons)
    {
        $result = DataProcer::transferAccounts($data);
        // 向用户反馈转账结果
        $connection->send(json_encode([
            'type'=>$result['type'],
            'gold'=>$result['gold'],
            'status'=>$result['status'],
            'msg'=>$result['msg'],
            'systemMsg'=>isset($result['systemMsg'])?$result['systemMsg']:false,
        ]));
        if ($result['status'] && $data['recive_connectionID']) {
            // 转账成功
            // 判断对方是否已经登陆
            if (isset($userCons[$data['recive_connectionID']]['uid']) && $userCons[$data['recive_connectionID']]['uid'] == $data['recive_id']) {
                $userCons[$data['recive_connectionID']]['con']->send(json_encode([
                    'type'=>'text',
                    'data'=>[
                        'msg'=>$result['msg'],
                        'userInfo'=>$result['userInfo']
                    ],
                ]));
            }
        }
    }
    /**
     * 获取我的回帖
     */
    public static function getMyReplyList($connection, $data, $userCons)
    {
        $result = DataProcer::getMyReplyList($data);
        $connection->send(json_encode($result));
    }
    /**
     * 获取我的帖子
     */
    public static function getMyThemeList($connection, $data, $userCons)
    {
        $result = DataProcer::getMyThemeList($data);
        $result['type'] = 'responeGetMyThemeList';
        $connection->send(json_encode($result));
    }
    /**
     * 发帖子
     */
    public static function postTheme($connection, $data, $userCons)
    {
        $result = DataProcer::postTheme($data);
        $connection->send(json_encode($result));
    }
    /**
     * 回复评论
     */
    public static function ReplyComment($connection, $data, $userCons)
    {
        $result = DataProcer::ReplyComment($data);
        $connection->send(json_encode($result));
    }
    /**
     * 评论点赞
     */
    public static function zanComment($connection, $data, $userCons)
    {
        $result = DataProcer::zanComment($data);
        $connection->send(json_encode($result));
    }

    /**
     * 发表评论
     */
    public static function themeComment($connection, $data, $userCons)
    {
        $result = DataProcer::themeComment($data);
        $connection->send(json_encode($result));
    }
    /**
     * 获取帖子评论
     */
    public static function getComment($connection, $data, $userCons)
    {
        $result = DataProcer::getComment($data);
        // dump("________________________________");
        // dump($result);
        // dump("________________________________");
        if (isset($result['msgData']) && $result['msgData']) {
            $msgData = $result['msgData'];
            unset($result['msgData']);
        } else {
            $msgData = false;
        }
        // dump("++++++++++++++++++++++++++++++++");
        // dump($msgData);
        // dump("++++++++++++++++++++++++++++++++");
        $connection->send(json_encode($result));
        if ($msgData && isset($result['postInfo']['connectionID']) && $result['postInfo']['connectionID']) {
            $con_id = $result['postInfo']['connectionID'];
            // dump("=========================");
            // dump($con_id);
            // dump($userCons[$con_id]);
            // dump("=========================");
            if (isset($userCons[$con_id]['uid']) && $userCons[$con_id]['uid'] == $result['postInfo']['user_id']) {
                // dump("----------------------------");
                // dump($userCons[$con_id]['uid']);
                // dump("----------------------------");
                $userCons[$con_id]['con']->send(json_encode([
                    'type'=>'updateUserGold',
                    'gold'=>$result['postInfo']['gold'],
                    'noticeMsg'=>$msgData,
                ]));
            }
        }
    }
    /**
     * 获取帖子列表
     */
    public static function getPostList($connection, $data, $userCons)
    {
        $result = DataProcer::getPostList($data);
        $connection->send(json_encode($result));
    }
    /**
     * 获取论坛区列表
     */
    public static function getThemeList($connection, $data, $userCons)
    {
        $result = DataProcer::getThemeList($data);
        $connection->send(json_encode($result));
    }

    /**
     * 获取收到的红包列表
     */
    public static function getReciveRedList($connection, $data, $userCons)
    {
        $result = DataProcer::getReciveRedList($data);
        $connection->send(json_encode($result));
    }
    /**
     * 获取发送的红包列表
     */
    public static function getSendRedList($connection, $data, $userCons)
    {
        $result = DataProcer::getSendRedList($data);
        $connection->send(json_encode($result));
    }
    /**
     * 修改登录密码
     */
    public static function changeLoginPassword($connection, $data, $userCons)
    {
        $result = DataProcer::changeLoginPassword($data);
        $connection->send(json_encode($result));
    }
    /**
     * 修改签名
     */
    public static function changeSignature($connection, $data, $userCons)
    {
        $result = DataProcer::changeSignature($data);
        $connection->send(json_encode($result));
    }
    /**
     * 修改昵称
     */
    public static function changeNickName($connection, $data, $userCons)
    {
        $result = DataProcer::changeNickName($data);
        $connection->send(json_encode($result));
    }
    /**
     * 修改个人设置
     */
    public static function settingChange($connection, $data, $userCons)
    {
        $result = DataProcer::settingChange($data);
        $connection->send(json_encode($result));
        if (isset($data['index']) && $data['index'] == 'broadcast' && $result['status']) {
            $userCons[$data['con_id']]['broadcast'] = $data['value'];
        } elseif ($data['index'] == 'stmessage' && $result['status']) {
            // 修改接受陌生人消息状态时，向当前陌生人列表的所有人发送修改状态
            if (!empty($userCons)) {
                foreach ($data['strangers'] as $key => $vo) {
                    if (isset($userCons[$vo['scon_id']]) && isset($userCons[$vo['scon_id']]['uid']) && $userCons[$vo['scon_id']]['uid'] == $vo['stranger_id']) {
                        $userCons[$vo['scon_id']]['con']->send(json_encode([
                            'type'=>'reciveStangerMessageChange',
                            'stranger_id'=>$data['send_id'],
                            'stmessage'=>$data['value'],
                        ]));
                    }
                }
            }
        }
    }
    /**
     * 从银行取出金币
     */
    public static function getGoldFromBank($connection, $data, $userCons)
    {
        $result = DataProcer::getGoldFromBank($data);
        $connection->send(json_encode($result));
    }
    /**
     * 存储金币
     */
    public static function storageGold($connection, $data, $userCons)
    {
        $result = DataProcer::storageGold($data);
        $connection->send(json_encode($result));
    }
    /**
     * 获取红包详情
     */
    public static function RedDetail($connection, $data, $userCons)
    {
        $result = DataProcer::RedDetail($data);
        // dump($result);
        $connection->send(json_encode([
            'result'=>$result,
            'type'=>'responseRedDetail'
        ]));
    }

    /**
     * 登陆请求处理函数
     */
    public static function logining($connection, $data, $userCons)
    {
        $data['ip_address'] = $connection->getRemoteIp();
        // dump($data);
        $result = DataProcer::logining($data);
        $connection->send(json_encode($result));
    }
    /**
     * 获取app信息请求处理函数
     */
    public static function getAppInfo($connection, $data, $userCons)
    {
        $result = DataProcer::getAppInfo($data);
        $connection->send(json_encode($result));
    }

    /**
     * 领取朋友发的红包
     */
    public static function reciveRedBagFromFriend($connection, $data, $userCons)
    {
        $result = DataProcer::reciveRedBagFromFriend($data);
        $connection->send(json_encode($result));
        if ($result['status']) {
            if ($data['red_send_con']) {
                if (isset($userCons[$data['red_send_con']]['uid']) && $userCons[$data['red_send_con']]['uid'] == $data['red_send_id']) {
                    $userCons[$data['red_send_con']]['con']->send(json_encode([
                        'type'=>'redbagRecivedNotice',
                        'status'=>true,
                        'red_id'=>$data['red_id'],
                        'friend_id'=>$data['send_id'],
                    ]));
                }
            }
        }
    }
    /**
     * 发送红包给朋友
     */
    public static function sendRedBagToFriend($connection, $data, $userCons)
    {
        // dump("+++++++++++++SENDREDBAG++++++++++++++");
        $result = DataProcer::sendRedBagToFriend($data);
        // dump($result);
        // dump("+++++++++++++SENDREDBAG++++++++++++++");
        if ($result['status']) {
            if ($data['redType'] == 1) {
                // 发个人红包
                $connection->send(json_encode([
                    'status'=>true,
                    // 'red_id'=>$result['red_id'],
                    'msg'=>$result['send_info']
                ]));
                if ($data['fcon_id']) {
                    // 向对方推送红包信息
                    if (isset($userCons[$data['fcon_id']]['uid']) && $userCons[$data['fcon_id']]['uid'] == $data['get_id']) {
                        $userCons[$data['fcon_id']]['con']->send(json_encode([
                            'type'=>'text',
                            'data'=>[
                                'msg'=>$result['send_info'], //红包信息
                                'userInfo'=>$result['userInfo'] //如果彼此是陌生人，则向对方推送本人信息，否则该值为null
                            ],
                        ]));
                    }
                }
            } else {
                // 发群发红包
                $connection->send(json_encode([
                    'status'=>true,
                    // 'red_id'=>$result['red_id'],
                    'msg'=>$result['send_info']
                ]));
                // 向群里的人推送红包信息
                if (!empty($userCons)) {
                    foreach ($userCons as $k => $vo) {
                        if (isset($vo['rType']) && $vo['rType'] && isset($vo['con']) && $vo['con'] && $vo['con']!=$connection) {
                            $vo['con']->send(json_encode([
                                'type'=>'ChatRoomMsg',
                                // 'data'=>[
                                'msg'=>$result['send_info'], //红包信息
                                    // 'userInfo'=>$result['userInfo'] //用户信息
                                // ],
                            ]));
                        }
                    }
                }
            }
        } else {
            // 返回发送失败的错误信息
            $connection->send(json_encode([
                'status'=>false,
                'errMsg'=>$result['errMsg']
            ]));
        }
    }
    /**
     * 修改交易密码
     */
    public static function changeTrading($connection, $data, $userCons)
    {
        $result = DataProcer::changeTrading($data);
        // dump($result);
        $connection->send(json_encode($result));
    }
    /**
     * 删除陌生人
     */
    public static function delStrange($connection, $data, $userCons)
    {
        $result = DataProcer::delStrange($data);
        $connection->send(json_encode([
            'status'=>$result,
        ]));
    }
    /**
     * 修改系统消息的阅读状态
     */
    public static function changeSystemReadStatus($connection, $data, $userCons)
    {
        $result = DataProcer::changeSystemReadStatus($data);
        $connection->send(json_encode([
            'status'=>$result,
            'type'=>'ResponeChangeSystemReadStatus'
        ]));
    }
    /**
     * 同意或者拒绝好友申请
     */
    public static function AgreeFriendRequest($connection, $data, $userCons)
    {
        dump("I am comming the AgreeFriendRequest\n");
        $result = DataProcer::AgreeFriendRequest($data);
        if (!$result) {
            dump("It is fail to operate the Request of addFriend!\n");
            // 操作失败，
            $connection->send(json_encode([
                'type'=>"ResponeAgreeFriendRequest",
                'result'=>false
            ]));
        } else {
            dump("It is OK!\n");
            // 操作成功
            // 返回消息
            $connection->send(json_encode([
                'type'=>'ResponeAgreeFriendRequest',
                'result'=>true,
                'msg'=>$result['user_data']
            ]));
            if ($data['fcon_id']) {
                // 判断对法是否上线
                if (isset($userCons[$data['fcon_id']]['uid']) && $userCons[$data['fcon_id']]['uid'] == $data['friend_id']) {
                    if ($data['msg_status']==6) {
                        // 同意好友申请，发送好友信息
                        $userCons[$data['fcon_id']]['con']->send(json_encode([
                            'type'=>'PassFriendRequest',
                            'data'=>$result['friend_data']
                        ]));
                    } else {
                        // 拒绝好友申请，发送拒绝提醒
                        $userCons[$data['fcon_id']]['con']->send(json_encode([
                            'type'=>'RefuseFriendRequest',
                            'msg'=>$result['friend_data']
                        ]));
                    }
                }
            }
        }
    }

    /**
     * 删除消息
     */
    public static function DeleteMessage($connection, $data, $userCons)
    {
        if ($connection) {
            $result = DataProcer::DeleteMessage($data);
            $connection->send(json_encode([
                'type'=>'ResopneDeleteMessage',
                'result'=>$result
            ]));
        }
    }
    /**
     * 删除好友
     */
    public static function deleteFriend($connection, $data, $userCons)
    {
        $result = DataProcer::deleteFriend($data);
        if (!$result) {
            // 删除失败
            $connection->send(json_encode([
                'result'=>false,
            ]));
        } else {
            // 删除成功
            $connection->send(json_encode([
                'result'=>true,
            ]));
            if ($data['fcon_id']) {
                // 判断对方是否在线
                if (isset($userCons[$data['fcon_id']]['uid']) && $userCons[$data['fcon_id']]['uid'] == $data['friend_id']) {
                    // 向对方发送删除好友提示消息
                    $userCons[$data['fcon_id']]['con']->send(json_encode([
                        'type'=>'deleteFriendNotice',
                        'msg'=>$result
                    ]));
                }
            }
        }
    }
    /**
     * 下线通知
     */
    public static function downLine($connection, $data, $userCons)
    {
        dump(self::iconv("下线通知："));
        dump($data);
        $friendIds = DataProcer::downLine($data);
        dump($friendIds);
        $connection->send(json_encode([
            'type'=>'responeDownLine',
            'status'=>$friendIds['status'],
            // 'msg'=>isset($friendIds['msg'])?$friendIds['msg']:'',S
        ]));
        if ($friendIds['status'] && !empty($friendIds['ids'])) {
            foreach ($friendIds['ids'] as $key => $vo) {
                foreach ($vo as $k => $v) {
                    // 下线通知
                    if (isset($userCons[$v['connectionID']]['uid']) && $userCons[$v['connectionID']]['uid'] == $v['id']) {
                        $noticeInfo = [
                            'type'=>'onlineNotice',
                            'msg'=>$userCons[$v['connectionID']]['nickname'].'已经下线',
                            'fType'=> $key,
                            'index'=>$data['send_id'],
                            'status'=>0,
                            'connectionID'=>'',
                        ];
                        $userCons[$v['connectionID']]['con']->send(json_encode($noticeInfo));
                    }
                }
            }
        }
    }
    /**
     * 添加好友
     */
    public static function addFriend($connection, $data, $userCons)
    {
        dump(date('Y-m-d H:i:s').": user(".$data['send_id'].") send a request of addFriend to user(".$data['get_id'].")\n");
        $result = DataProcer::addFriendRequest($data);
        if ($result) {
            if ($data['fcon_id']) {
                // 判断对方是否在线
                if (isset($userCons[$data['fcon_id']]['uid']) && $userCons[$data['fcon_id']]['uid'] == $data['get_id']) {
                    if (isset($result['get_info'])) {
                        // 对方没有开启好友验证，添加好友成功，向对方发送添加人的信息以及添加成功的好友消息
                        $userCons[$data['fcon_id']]['con']->send(json_encode([
                            'type'=>'addFriendRequest',
                            'fInfo'=>$result['send_info'],
                            'msg'=>$result['data']
                        ]));
                    } else {
                        $result['send_info']['content'] = $result['data']['content'];
                        $result['send_info']['flag'] = $result['data']['flag'];
                        $result['send_info']['status'] = $result['data']['status'];
                        $result['send_info']['mid'] = $result['data']['mid'];
                        // 对方开启了好友验证，向对方发送好友申请请求
                        $userCons[$data['fcon_id']]['con']->send(json_encode([
                            'type'=>'addFriendRequest',
                            'fInfo'=>'',
                            'msg'=>$result['send_info']
                        ]));
                    }
                }
            }

            // 判断是否添加好友成功
            if (isset($result['get_info'])) {
                // 添加好友成功，返回好友信息，以及添加成功提示消息
                $connection->send(json_encode([
                    'fInfo'=>$result['get_info'],
                    'msg'=>$result['data'],
                    'type'=> "ResoneAddFriendRequest"
                ]));
            } else {
                // 对法开启验证，返回提示信息
                $result['data']['content'] = "对方开启了好友验证，等待对方通过验证！";
                $connection->send(json_encode([
                    'fInfo'=>'',
                    'msg'=>$result['data'],
                    'type'=> "ResoneAddFriendRequest"
                ]));
            }
        } else {
            // 请求信息保存失败
            $connection->send(json_encode([
                'fInfo'=>'',
                'msg'=> false,
                'type'=> "ResoneAddFriendRequest"
            ]));
        }
    }
    /**
     * 查找好友
     */
    public static function searchFrinds($connection, $data, $userCons)
    {
        $result = DataProcer::searchFriends($data);
        $connection->send(json_encode([
            'type'=>'searchFriendsResult',
            'result'=>$result
        ]));
    }
    /**
     * 握手处理
     */
    public static function handle($connection, $data, $userCons)
    {
        $data['con_id'] = $connection->id;
        // 判断是否存在用户id
        if ($data['send_id']) {
            // 获取好友信息和未读消息
            $result = DataProcer::handle($data);
            $connection->send(json_encode([
                'type'=>'handle',
                'userMsgs'=>$result['userMsg'],
                'friendsList'=>$result['friendList'],
                'con_id'=>$data['con_id'],
                'gold'=>$result['gold']
            ]));
            // 向在线好友发送上线通知
            foreach ($result['friendList'] as $key => $vo) {
                foreach ($vo as $k => $v) {
                    if ($v['connectionID'] && isset($userCons[$v['connectionID']]['uid']) && $userCons[$v['connectionID']]['uid'] == $v['id']) {
                        $noticeInfo = [
                            'type'=>'onlineNotice',
                            'msg'=>$data['nickname'].'已经上线',
                            'fType'=> $key, //好友类型
                            'index'=>$data['send_id'], //好友id
                            'status'=>1, //好友在线状态
                            'connectionID'=>$data['con_id'], //好友链接标识符
                        ];
                        $userCons[$v['connectionID']]['con']->send(json_encode($noticeInfo));
                    }
                }
            }
            // 判断用户是否进入聊天室 rType为0时表示聊天室的用户； 功能：向聊天室广播用户进来的消息，需要时去掉注释
            // if (!empty($userCons) && !$data['rType']) {
            //     // 向聊天室的用户广播消息
            //     $message = json_encode([
            //         'msg'=>"欢迎".$data['nickname']."进入房间！",
            //         'type'=>'text',
            //         'role'=>0,
            //         'send_id'=>''
            //     ]);
            //     foreach ($userCons as $k => $con) {
            //         if ($con['con'] && !$con['rType']) {
            //             $con['con']->send($message);
            //         }
            //     }
            // }
            

            // 保存用户的链接资源到资源池
            $conInfo['con'] = $connection;
            $conInfo['uid'] = $data['send_id']; //用户id
            
            $conInfo['nickname'] = $data['nickname']; // 用户昵称
            $conInfo['lasttime'] = time(); //最后活动时间(作用于心跳)
            $conInfo['broadcast'] = $data['broadcast']; //0：不接收广播；1：接收广播
            $conInfo['online'] = 0; //在线时长
            if (!isset($userCons[$data['con_id']]['rType'])) {
                $conInfo['rType'] = false; // true：表示在聊天室，false：表示不再聊天室
            } else {
                $conInfo['rType'] = $userCons[$data['con_id']['rType']];
            }
            return [
                'conInfo'=>$conInfo,
                'con_id'=>$data['con_id'],
            ];
            // $userCons[$data['con_id']]['stmessage'] = $data['stmessage']; //0：不接收陌生人消息；1：接收陌生人消息
    
            // dump("=====================用户资源信息=======================\n");
            // dump("connectionID:".$connection->id."\n");
            // dump("data-connectionID:".$data['con_id']."\n");
            // dump("uid:".$userCons[$data['con_id']]['uid']."\n");
            // dump("=====================用户资源信息=======================\n");
            dump(self::iconv("user[(".$data['send_id'].")".$data['nickname']."] is comming\n"));
        } else {
            dump(self::iconv(date("Y-m-d H:i:s")."有非法用户进入\n"));
            $connection->send(json_encode([
                'type'=>'handle',
                'msg'=>'非法用户！',
                'role'=>0,
                'send_id'=>-1,
                'userMsgs'=>'',
                'friendsList'=>''
            ]));
            return false;
        }
    }

    /**
     * 客户端发送过来的关闭socket请求
     */
    public static function closeSocket($data)
    {
        // 关闭资源。
        // $userCons[$data['send_id']]['con']->close();
        // // 判断用户是否在聊天室
        // if (!$userCons[$data['send_id']]['rType'] && !empty($userCons)) {
        //     $message = json_encode([
        //          'msg'=>"用户".$data['nickname']."离开了房间！",
        //          'type'=>'text',
        //          'role'=>0,
        //          'send_id'=>''
        //      ]);
        //     // 向聊天室的人广播离开的消息
        //     foreach ($userCons as $k => $con) {
        //         if ($con['con'] && !$con['rType'] && $k != $data['send_id']) {
        //             $con['con']->send(json_encode($message));
        //         }
        //     }
        // }
        // // 删除当前用户资源信息
        // unset($userCons[$data['send_id']]);
        // dump("user[(".$data['send_id'].")".$data['nickname']."] is leaving\n");
    }


    /**
    * 一对一发送消息
    * @access public static
    * @return void
    * @param $connection 发送人的资源链接
    * @param $data 发送的数据
    * @param $oneToOne 默认单对单聊天，关闭单对单则为广播消息
    */
    public static function sendMessage($connection, $data, $userCons)
    {
        try {
            // dump("[MyWorkerServer->sendMessage]\n");
            /*===============后台消息处理，上线时可以注释掉==================*/
            $msg = "user[(".$data['send_id'].")] tall to user[(".$data['get_id'].")]: ".$data['content']."【".date("Y-m-d H:i:s")."】\n";
            
            // 转换编码格式
            $msg = self::iconv($msg);
       
            // 后台输出消息
            dump($msg);
            /*===============后台消息处理，上线时可以注释掉==================*/

            // if ($data['rType']) {
            // if ($data['status']==2) {
            //     if($data['fcon_id']){
            //         if(isset($userCons[$data['fcon_id']]['stmessage']) && !$userCons[$data['fcon_id']]['stmessage']){
            //             $connection->send(json_encode([
            //                 'status'=>false,
            //                 'msg'=>'对方设置了'
            //             ]));
            //         }
            //     }else{
            //         $data['check_recive_strange_msg'] = true;
            //     }
            // }
            // 1对1聊天
            $result = DataProcer::chatRecord($data);
            if (!$result) {
                $connection->send(json_encode([
                    'status'=>false,
                    'type'=>'responseTextMsg'
                ]));
            } else {
                $connection->send(json_encode([
                        'status'=>true
                    ]));
                if ($data['fcon_id']) {
                    // dump("+++++++++++++++++++++++++++++++++++\n");
                    // dump($data['fcon_id']);
                    // dump(array_keys($userCons));
                    // dump("+++++++++++++++++++++++++++++++++++\n");
                    // 判断对方是否在线
                    if (isset($userCons[$data['fcon_id']]['con']) && $userCons[$data['fcon_id']]['uid'] == $data['get_id']) {
                        // 向对方推送消息
                        $userCons[$data['fcon_id']]['con']->send(json_encode([
                            'type'=>'text',
                            'data'=>$result
                        ]));
                    }
                }
            }
            // } else {
            //     // 聊天室
            //     $message = json_encode([
            //         // ''
            //     ]);
            //     // for()
            // }
        } catch (\Exception $e) {
            dump("sendMessage->catch::".$e->getMessage()."; line:".$e->getLine()."; file:".$e->getFile());
        }
    }

    public static function iconv($msg)
    {
        if (is_array($msg)||is_object($msg)) {
            foreach ($msg as $key => $vo) {
                $msg[$key] = self::iconv($vo);
            }
        } else {
            // 获取去字符串的编码格式
            $encode = mb_detect_encoding($msg, array("ASCII",'UTF-8','GB2312',"GBK",'BIG5'));
            // 转换编码格式
            $msg = iconv($encode, 'GB2312', $msg);
        }
        return $msg;
    }
}
