<?php
namespace app\Server\controller;

use think\Db;
use app\Server\controller\Redis;
use app\MyCommon\controller\Base;

class Transfer
{
    /**
     * 转账
     */
    public static function transferAccounts($data)
    {
        $sendUserInfo = DB::name('user_user')
        ->where('id', $data['send_id'])
        ->field('id,head,nickname,on_status,connectionID,username,gold,phonenumber')
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
                $systemMsg = [
                    'send_id'=>-10086,
                    'get_id'=>$data['send_id'],
                    'time'=>$transferData['addtime'],
                    'status'=>-1,
                    'content'=>"(@title:金币扣除通知@)您于".date('Y-m-d H:i:s').'转给用户“'.$data['recive_name'].'”'.$data['gold'].'个金币',
                    'flag'=>0
                ];
                $sysMsgId = DB::name('user_message')->insertGetId($systemMsg);
                $systemMsg['id'] = $sysMsgId;

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
                Base::goldHistory($sendUserInfo['username']?$sendUserInfo['username']:$sendUserInfo['phonenumber'], '转账', $detail);
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
        $index = json_encode(['tid'=>(int)$id,'mid'=>(int)$msgId]);
        Redis::setex([[$index, EXPIRE_TIME, $data['gold']]]);
        return [
            'type'=>'responeTransferAccounts',
            'status'=>true,
            'gold'=>$remark_gold,
            'msg'=>$messageData,//发送给好友的
            'userInfo'=>$userInfo,
            'systemMsg'=>$systemMsg,
        ];
    }

    /**
     * 获取转账信息
     */
    public static function getTransferInfo($data)
    {
        $info = DB::name('user_transfer')->where('id', $data['transfer_id'])->find();
        $info['addtime'] = date('Y-m-d H:i', $info['addtime']);
        return [
            'type'=>'responeGetTransferInfo',
            'info'=>$info,
        ];
    }
    /**
     * 领取好友转账
     */
    public static function reciveTransfer($data)
    {
        $index = json_encode(['tid'=>(int)$data['transfer_id'], 'mid'=>(int)$data['msg_id']]);
        $trans_gold = Redis::get($index);
        if ($trans_gold) {
            // DB::startTrans();
            try {
                // 获取转账信息
                // $transferInfo = DB::name('user_transfer')->where('id', $data['transfer_id'])->find();
                // 获取用户金币信息
                $userInfo = DB::name('user_user')->field('id,gold,username,phonenumber')->where('id', $data['user_id'])->find();
                $userInfo['gold'] += $trans_gold;
                // 更新用户金币信息
                DB::name('user_user')->update($userInfo);
                // 更新转账信息的收取状体
                DB::name('user_transfer')->where('id', $data['transfer_id'])->update(['status'=>1]);
                // 判断金币是否存在两位小数
                // if (strpos($transferInfo['gold'], '.') === false) {
                //     $gold = $transferInfo['gold'].'.00';
                //     $str = '(@transfer#'.$transferInfo['id'].'-'.$transferInfo['gold'].'.00#transfer@)';
                // } else {
                //     $stmp = explode(".", $transferInfo['gold']);
                //     $gold = $stmp[0].'.00';
                //     $str = '(@transfer#'.$transferInfo['id'].'-'.$stmp[0].'.00#transfer@)';
                // }
                // 获取消息，更新消息收取状态
                $messageInfo = DB::name('user_message')->field('id,content')->where('id', $data['msg_id'])->find();
                // dump($messageInfo);
                $messageInfo['content'] = 'false:' . $messageInfo['content'];
                DB::name('user_message')->update($messageInfo);
                // 保存金币流水
                $goldHistory = [
                    '收账前金额'=>$userInfo['gold'] - $trans_gold,
                    '收账金额'=>$trans_gold,
                    '收账后金额'=> $userInfo['gold'],
                ];
                Base::goldHistory($userInfo['username']?$userInfo['username']:$userInfo['phonenumber'], '收账', $goldHistory);
                // DB::commit();
            } catch (\Exception $e) {
                // DB::rollback();
                dump($e->getMessage());
                return [
                    'status'=>false,
                    'msg'=>'收账失败！',
                    'type'=>'responseRecivedTransfer'
                ];
            }
            Redis::del($index);
            return [
                'status'=>true,
                'gold'=>$userInfo['gold'],
                'transfer_gold'=>$trans_gold,
                'type'=>'responseRecivedTransfer'
            ];
        } else {
            return [
                'status'=>false,
                'msg'=>'转账已过期！',
                'type'=>'responseRecivedTransfer'
            ];
        }
    }
}
