<?php
namespace app\Server\controller;

use think\Db;

class Chat
{
    public function __construct()
    {
    }

    public static function getChatRecords($data)
    {
        $listRows = 10;
        if (!isset($data['page'])) {
            $data['page'] = 0;
        }
        $firstRow = $data['page'] * $listRows;
        $nowTime = time();
        if (!$data['page']) {
            $totalNum = DB::name('user_message')->where("get_id|send_id", $data['user_id'])->where('send_id|get_id', $data['frined_id'])->where('time', 'egt', ($nowTime-(30*24*60*60)))->count();
            $totalNum = ceil($totalNum/$listRows);
        } else {
            $totalNum = '';
        }
        //获取30天内的消息记录
        $recordsList = DB::name('user_message')->where("get_id|send_id", $data['user_id'])->where('send_id|get_id', $data['frined_id'])->where('time', 'egt', ($nowTime-(30*24*60*60)))->order('time desc')->limit($firstRow, $listRows)->select();
        if (count($recordsList) < $listRows) {
            $end = true;
        } else {
            $end = false;
        }
        return [
            'type'=>'responeGetChatRecords',
            'recordsList'=>$recordsList,
            'total'=>$totalNum,
            'end'=>$end,
        ];
    }


    public static function ChatRoomMsg($data)
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


    public static function getChatRoomMsgList($data)
    {
        try {
            $listRows = 20;
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
            if (count($list) < $listRows) {
                $end = true;
            } else {
                $end = false;
            }
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
            'end'=>$end,
        ];
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
    public static function chatRecord($data)
    {
        // if(isset($data['check_recive_strange_msg']) && $data['check_recive_strange_msg']){
        //     $stange_flag = DB::name('user_user')->where('id',$data['get_id'])->value('stmessage');
        //     if(!$stange_flag){
        //         return [
        //             'check_result'=>true,
        //             'msg'=>'对方拒收陌生消息',
        //         ];
        //     }
        // }
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
                ->field('id,head,nickname,on_status,connectionID')
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
    public static function changeSystemReadStatus($data)
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
    public static function AgreeFriendRequest($data)
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
    public static function DeleteMessage($data)
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
    public static function deleteFriend($data)
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
    public static function downLine($data)
    {
        try {
            $timeInfo = DB::name('user_user')->where('id', $data['send_id'])->field('lasttime,online')->find();
            $updateInfo = [
                'on_status'=>0, //在线状态
                'connectionID'=>0, //workerman链接资源标识符
                'id'=>$data['send_id'], //用户id
                'online'=>($timeInfo['online']+(time() - $timeInfo['lasttime'])) // 在线时长
            ];
            if (isset($data['login_status'])) {
                $updateInfo['login_status'] = $data['login_status']; //登陆状态
            }
            // 修改用户在线状态和链接标识符为0
            if (!DB::name('user_user')->update($updateInfo)) {
                throw new \Exception("fail to update the status in MySql!");
            }
            // 获取用户朋友id
            $friendIds = DB::name('user_friends')
            ->alias('f')
            ->join('bc_user_user u', 'u.id=f.friend_id')
            ->where('f.user_id', $data['send_id'])
            ->where('u.connectionID', 'gt', 0)
            ->field('u.id,u.connectionID')
            ->select();
            $fids = [];
            foreach ($friendIds as $k => $v) {
                $fids[] = $v['id'];
            }
            // 获取用户陌生人id
            $strangeIds = Db::name("user_message")
            ->alias('m')
            ->join('bc_user_user u', '(u.id=m.send_id||u.id=m.get_id)&&u.id!='.$data['send_id'])
            ->where('m.get_id|m.send_id', $data['send_id'])
            ->where('m.status', 2)
            ->where('m.time', '>', (time()-30*24*60*60))
            ->where('m.send_id|m.get_id', 'notin', $fids)
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
        } catch (\Exception $e) {
            return [
                'status'=>false,
                'msg'=>$e->getMessage(),
                'line'=>$e->getLine(),
            ];
        }
        return [
            'status'=>true,
            'ids'=>[
                'friends'=>$friendIds,
                'strange'=>$strangeIds,
                'system'=>$systemIds
            ]
        ];
    }

    public static function addFriendRequest($data)
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
    public static function searchFriends($data)
    {
        $listRows = 15;
        // 判断是否翻页
        if (!isset($data['page'])) {
            $data['page'] = 0;
        }
        $firstRow = $data['page'] * $listRows;
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
        // 获取满足条件的陌生人信息
        $resultList = DB::name('user_user')
        ->alias('u')
        ->leftJoin('user_message m', 'm.get_id=u.id&&m.status=3&&m.send_id='.$data['send_id'])
        ->field('u.id,u.head,u.nickname,u.username,u.on_status,u.connectionID,m.status,m.time,u.stmessage')
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
    public static function updateMsgReadStatus($data)
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
