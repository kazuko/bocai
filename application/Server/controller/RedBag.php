<?php
namespace app\Server\controller;

use think\Db;
use app\MyCommon\controller\Base;
use app\Server\controller\Redis;

class RedBag
{
    public static function chatRoomRedbag($data)
    {
        // DB::startTrans();
        try {
            // redis索引
            $index = json_encode(['red_id'=>(int)$data['red_id'],'mid'=>(int)$data['msg_id']]);
            $redinfo = Redis::get($index);
            if ($redinfo) {
                // 解析json字符串
                $redinfo = json_decode($redinfo, true);
                // 红包可接受人数
                $redinfo['n'] = intval($redinfo['n']);
                $redinfo['gold'] = intval($redinfo['gold']);
                $redInfo['send_time'] = intval($redinfo['send_time']);
                if ($redinfo['n']) {
                    if (in_array($data['user_id'], $redinfo['user'])) {
                        // 用户已领取过
                        return [
                            'status'=>false,
                            'msg'=>'你已领取！',
                            'code'=>303,
                            'type'=>'responseChatRoomRedbag',
                        ];
                    } else {
                        // 可领取人数减一
                        $redinfo['n']--;
                        // 添加可已接受用户id
                        $redinfo['user'][] = $data['user_id'];
                        if ($redinfo['n']>0) {
                            // 随机生成领取红包金额
                            $gold = rand(1, ($redinfo['gold']-$redinfo['n']+1));
                            $status = 0;

                            $redinfo['gold'] -= $gold;
                            // 重置红包过期时间
                            $expired = EXPIRE_TIME -(time()-$redinfo['send_time']);
                            // return $expired;
                            // 更新redis中红包信息
                            Redis::setex([[$index, $expired, json_encode($redinfo)]]);
                        } else {
                            // 剩余最后一人的金币
                            $gold = $redinfo['gold'];
                            $status = 1;
                            Redis::del($index);
                        }
                       
                        // 更新用户金币信息
                        $userInfo = DB::name('user_user')->where('id', $data['user_id'])->field('id, gold, username,head,nickname,phonenumber')->find();
                        $userInfo['gold'] += $gold;
                        // return $userInfo;
                        DB::name('user_user')->update($userInfo);
                        // 更新红包信息
                        $redbag = DB::name("user_red")->field('detail,recived_num,send_id,remark')->where('id', $data['red_id'])->find();
                        // return $redbag;
                        // 红包领取详情
                        if ($redbag['detail']) {
                            $redbag['detail'] = json_decode($redbag['detail'], true);
                        } else {
                            $redbag['detail'] = [];
                        }
                        $redbag['detail'][$data['user_id']] = [
                            'head'=>$userInfo['head'],
                            'nickname'=>$userInfo['nickname'],
                            'time'=>time(),
                            'gold'=>$gold,
                        ];
                        // 红包消息的内容
                        if ($status) {
                            $msgContent = 'false:[-'.$redbag['send_id'].'-';
                        } else {
                            $msgContent = '[-'.$redbag['send_id'].'-';
                        }
                        foreach ($redbag['detail'] as $key => $vo) {
                            $msgContent .= 'o-'.$key.'-';
                        }
                        $msgContent .= '](@redbag'.$data['red_id'].'redbag@)'.$redbag['remark'];

                        // 红包信息
                        $updateData['detail'] = json_encode($redbag['detail']);
                        $updateData['recived_num'] = $redbag['recived_num']+1;
                        $updateData['status'] = $status;
                        // $redbag['id'] = $data['red_id'];
                        // return $redbag;
                        // 更新红包信息
                        DB::name('user_red')->where('id', $data['red_id'])->update($updateData);
                        // 更新红包消息内容
                        $redMsg = [
                            'id'=>$data['msg_id'],
                            'content'=>$msgContent
                        ];
                        DB::name('chat_room')->update($redMsg);

                        // 金币记录详情
                        $detail = [
                            '领红包前'=>$userInfo['gold'] - $gold,
                            '红包金额'=>$gold,
                            '领红包后'=>$userInfo['gold']
                        ];
                        // 保存金币记录
                        Base::goldHistory($userInfo['username']?$userInfo['username']:$userInfo['phonenumber'], '领红包', $detail);
                    }
                } else {
                    return [
                        'status'=>false,
                        'msg'=>'红包已被抢完，下次出手要快点哦！',
                        'code'=>202,
                        'type'=>'responseChatRoomRedbag',
                        'redStatus'=>1
                    ];
                }
            } else {
                $status = DB::name("user_red")->where('id', $data['red_id'])->value('status');
                if ($status == 1) {
                    $errMsg = '红包已被抢完，下次出手要快点哦！';
                } else {
                    $errMsg = '红包已过期，下次早点哦！';
                }
                return [
                    'status'=>false,
                    'msg'=>$errMsg,
                    'code'=>101,
                    'type'=>'responseChatRoomRedbag',
                    'redStatus'=>$status
                ];
            }
            // DB::commit();
        } catch (\Exception $e) {
            // DB::rollback();
            echo "errMsg:".$e->getMessage()."\n";
            return [
                'status'=>false,
                'code'=>505,
                'type'=>'responseChatRoomRedbag',
                'msg'=>'领取失败，请检查网络是否正常!',
            ];
        }
        return [
            'status'=>true,
            'msg'=>'领取成功',
            'gold'=>$userInfo['gold'],
            'content'=>$msgContent,
            'code'=>0,
            'type'=>'responseChatRoomRedbag'
        ];
    }

    /**
     * 获取收到的红包列表
     */
    public static function getReciveRedList($data)
    {
        $listRows = 15;
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
    public static function getSendRedList($data)
    {
        $listRows = 15;
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
     * 红包详情
     */
    public static function RedDetail($data)
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
                $user = DB::name('user_user')->where('id', $k)->field('head,nickname')->find();
                dump($user);
                $redInfo['detail'][$k]['head'] = $user['head'];
                $redInfo['detail'][$k]['nickname'] = $user['nickname'];
            }
        }
        return $redInfo;
    }


    /**
     * 领取个人红包
     */
    public static function reciveRedBagFromFriend($data)
    {
        $index = json_encode(['red_id'=>(int)$data['red_id'],'mid'=>(int)$data['mid']]);
        dump($index);
        $redInfo = Redis::get($index);
        dump($redInfo);
        if ($redInfo) {
            $info = DB::name('user_red')->where('id', $data['red_id'])->find();
            // if ($info['type'] == 2) {
            //     return $this->reciveRedbagFromChatRoom($data);
            // }
            DB::startTrans();
            try {
                // 获取红包信息
                if ($info['status'] == 2 || (time()-$info['send_time'])>24*60*60) {
                    // 红包超过24小时
                    return [
                        'status'=>false,
                        'errMsg'=>'该红包已过期！',
                        'type'=>'responseReciveRedBag',
                        'code'=>1,
                    ];
                } else {
                    // status为1表示已领取，0表示未领取
                    if ($info['status'] == 1 || $info['recive_num'] <= $info['recived_num']) {
                        return [
                            'status'=>false,
                            'errMsg'=>'该红包已领取！',
                            'type'=>'responseReciveRedBag',
                            'code'=>3,
                        ];
                    } else {
                        // 获取用户当前剩余金币和账号
                        $user = DB::name("user_user")->where('id', $data['send_id'])->field('username,gold,head,nickname,phonenumber')->find();
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
                        $redInfo = [
                            'detail'=>json_encode($info['detail']),
                            'status'=>1,
                            'recived_num'=>$info['recive_num']
                        ];
                        DB::name('user_red')->where('id', $data['red_id'])->update($redInfo);
                        // 保存红包金额到用户金币池
                        DB::name('user_user')->where('id', $data['send_id'])->setInc('gold', $info['money']);
                        
                        // 金币记录详情
                        $detail = [
                            '领红包前'=>$user['gold'],
                            '红包金额'=>$info['money'],
                            '领红包后'=>$user['gold']+$info['money']
                        ];
                        // 保存金币记录
                        Base::goldHistory($user['username']?$user['username']:$user['phonenumber'], '领红包', $detail);
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
                            'type'=>'responseReciveRedBag',
                        ];
                        Redis::del(json_encode(['red_id'=>$info['id'],'mid'=>$mesgInfo['id']]));
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
                    'errMsg'=>'网络堵塞，请稍后再试！',
                    'type'=>'responseReciveRedBag',
                    'code'=>2,
                ];
            }
            return $retrunValue;
        } else {
            return [
                'status'=>false,
                'errMsg'=>'该红包已过期！',
                'type'=>'responseReciveRedBag',
                'code'=>1,
            ];
        }
    }


    /**
     * 保存红包信息
     */
    public static function sendRedBagToFriend($data)
    {
        // DB::startTrans();
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
            // return $id;
           
            // 获取用户当前金币
            $sendUserInfo = DB::name('user_user')
            ->where('id', $data['send_id'])
            ->field('id,head,nickname,on_status,connectionID,username,gold,phonenumber')
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
                    'id'=>'',
                    'send_id'=>$data['send_id'],
                    'get_id'=>$data['get_id'],
                    'time'=>$data['send_time'],
                    'status'=>$data['status'],
                    'content'=>'(@redbag'.$id.'redbag@)' . $data['remark'],
                    'flag'=>0,
                ];
                $mid = DB::name("user_message")->insertGetId($msgData);
                $msgData['id'] = $mid;
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
                    $userInfo = null;
                }
            } else {
                // 群发红包
                $msgData = [
                    'id'=>'',
                    'send_id'=>$data['send_id'],
                    'send_time'=>$data['send_time'],
                    'status'=>1,
                    'content'=>'[-'.$data['send_id'].'-](@redbag'.$id.'redbag@)' . $data['remark'],
                ];
                // dump($msgData);
                $mid = DB::name('chat_room')->insertGetId($msgData);
                $msgData['id'] = $mid;
                $msgData['head'] = $sendUserInfo['head'];
                $msgData['nickname'] = $sendUserInfo['nickname'];
                $userInfo = null;
            }
            // return $msgData;
            // return EXPIRE_TIME;
            $redbagInfo = [
                'n'=>$redbagrecord['recive_num'],
                'user'=>[],
                'gold'=>$redbagrecord['money'],
                'send_time'=>$redbagrecord['send_time']
            ];
            $index = json_encode(['red_id'=>(int)$id,'mid'=>(int)$mid]);
            $flag = Redis::setex([[$index, EXPIRE_TIME, json_encode($redbagInfo)]]);
            dump($flag);
            dump(EXPIRE_TIME);
            dump($index);
            dump(Redis::get($index));
            // 保存金币记录
            $detail = [
                '发红包前'=>$sendUserInfo['gold'],
                '红包金额'=>$data['gold'],
                '发红包后'=>$remark_gold
            ];
            // return $detail;
            Base::goldHistory($sendUserInfo['username']?$sendUserInfo['username']:$sendUserInfo['phonenumber'], '发红包', $detail);
            // DB::commit();
        } catch (\Exception $e) {
            echo "errmsg:". $e->getMessage()."\n";
            // DB::rollback();
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
            'msg_id'=>$mid,
            'send_info'=>$msgData,
            'userInfo'=>$userInfo,
        ];
    }
}
