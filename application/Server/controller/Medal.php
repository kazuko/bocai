<?php
namespace app\Server\controller;

use think\Db;
use app\Server\controller\Redis;

class Medal
{
    /**
     * 修改个人勋章的状态
     */
    public static function changeMedalStatus($data)
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
    public static function getMedalList($data)
    {
        // $names = DB::name('system_info')->field('coin1_name,coin2_name')->find();
        $systemInfo = json_decode(Redis::get('system_info'), true);
        // $systemInfo = $this->redis->get('system_info');
        $names = [
            'coin1_name'=>$systemInfo['coin1_name'],
            'coin2_name'=>$systemInfo['coin2_name']
        ];
        $list = DB::name('system_ranks')->order('min asc')->select();
        
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
}
