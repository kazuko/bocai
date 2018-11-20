<?php
namespace app\Server\controller;

use think\Db;
use app\Server\controller\Redis;
use app\Server\controller\Rules;

class Forum
{
    /**
     * *****************************************************************************************************************
     * 获取我的回帖
     * *****************************************************************************************************************
     */
    public static function getMyReplyList($data)
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
     * *****************************************************************************************************************
     * 获取我的发帖列表
     * *****************************************************************************************************************
     */
    public static function getMyThemeList($data)
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
     * *****************************************************************************************************************
     * 发帖子
     * *****************************************************************************************************************
     */
    public static function postTheme($data)
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
        if ($id = DB::name('forum_post')->insertGetId($insertValue)) {
            // 获取系统配置信息
            $systemInfo = json_decode(Redis::get('system_info'), true);
            if ($data['tieNum'] < $systemInfo['tie_pride_num']) {
                // 发帖数小于奖励发帖数，更新用户金额信息，同时保存用户金币流水
                $userData = DB::name("user_user")->where('id', $data['user_id'])->field('id, gold, integral, username, phonenumber')->find();
                $goldDetail = [
                    '奖励前'=>$userData['gold'],
                    '奖励金额'=>$systemInfo['tie_pride_gold'],
                    '奖励后'=>$systemInfo['tie_pride_gold'] + $userData['gold']
                ];
                $sliverDetail = [
                    '奖励前'=>$userData['integral'],
                    '奖励积分'=>$systemInfo['tie_pride_sliver'],
                    '奖励后'=>$systemInfo['tie_pride_sliver'] + $userData['integral']
                ];
                $userData['gold'] += $systemInfo['tie_pride_gold'];
                $userData['integral'] += $systemInfo['tie_pride_sliver'];
                if (DB::name('user_user')->update($userData)) {
                    // 保存金币流水
                    Base::goldHistory($userData['username']?$userData['username']:$userData['phonenumber'], '发帖奖励', $goldDetail);
                    // 保存积分流水
                    Base::sliverHistory($userData['username']?$userData['username']:$userData['phonenumber'], '发帖奖励', $sliverDetail);
                    // 检查和更新用户的勋章信息
                    Base::updateMedal($data['user_id']);
                    return [
                        'status'=>true,
                        'gold'=>$userData['gold'],
                        'sliver'=>$userData['integral'],
                        'type'=>'responsePostTheme'
                    ];
                }
            }
            return [
                'status'=>true,
                'type'=>'responsePostTheme',
            ];
        } else {
            return [
                'status'=>false,
                'msg'=>'发布失败！',
                'type'=>'responsePostTheme',
            ];
        }
    }
    /**
     * *****************************************************************************************************************
     * 回复评论
     * *****************************************************************************************************************
     */
    public static function ReplyComment($data)
    {
        unset($data['type']);
        unset($data['con_id']);
        $data['addtime'] = time();
        if ($id = DB::name('forum_reply')->insertGetId($data)) {
            return [
                'status'=>true,
                'id'=>$id,
                'addtime'=>$data['addtime'],
                'type'=>'responseReplyComment'
            ];
        } else {
            return [
                'status'=>false,
                'msg'=>'回复失败！',
                'type'=>'responseReplyComment'
            ];
        }
    }

    /**
     * *****************************************************************************************************************
     * 点赞评论
     * *****************************************************************************************************************
     */
    public static function zanComment($data)
    {
        if ($id = DB::name('forum_zan')->where('comment_id', $data['comment_id'])->where('user_id', $data['send_id'])->value('id')) {
            return [
                'status'=>false,
                'msg'=>'您已经过赞了',
                'zan_status'=>$id,
            ];
        } else {
            if (DB::name('forum_comment')->where('id', $data['comment_id'])->setInc('zan')) {
                $id = DB::name("forum_zan")->insertGetId(['comment_id'=>$data['comment_id'], 'user_id'=>$data['send_id']]);
                return [
                    'status'=>true,
                    'msg'=>'点赞成功',
                    'zan_status'=>$id,
                ];
            } else {
                return [
                    'status'=>false,
                    'msg'=>'点赞失败',
                    'zan_status'=>false,
                ];
            }
        }
    }
    /**
     * *****************************************************************************************************************
     * 发表评论
     * *****************************************************************************************************************
     */
    public static function themeComment($data)
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
     * *****************************************************************************************************************
     * 获取帖子评论
     * *****************************************************************************************************************
     */
    public static function getComment($data)
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
            ->join('bc_forum_reader r', 'r.post_id=p.id and r.user_id='.$data['send_id'], 'left')
            ->field('p.*,u.head,u.gold,u.username,u.phonenumber,u.connectionID,u.nickname as user_name,r.id as read_status')
            ->where('p.id', $data['post_id'])
            ->find();
            if ($postInfo['imgs']) {
                $postInfo['imgs'] = json_decode($postInfo['imgs']);
            }
            // 获取分区信息
            $zoneInfo = DB::name('forum_zone')->where('id', $data['zone_id'])->find();
            
            // 看帖需要花费金币
            if ($zoneInfo['gold'] && !$postInfo['read_status'] && $postInfo['user_id'] != $data['send_id']) {
                $result = self::payForTheme($data['send_id'], $postInfo, $zoneInfo);
                if(!$result['status']){
                    return $result;
                }
            }
            $lastTime = DB::name('forum_comment')->where(['user_id'=>$data['send_id'],'post_id'=>$data['post_id']])->order('addtime desc')->value('addtime');
            // if($lastTime){
                $totalNum = DB::name('forum_comment')->where('user_id',$data['send_id'])->count();
            // }else{
                // $totalNum = 0;
            // }
            $rules = Rules::getHuitieFilters();
            
        } else {
            $postInfo = false;
            $zoneInfo = false;
            $lastTime = false;
            $totalNum = false;
            $rules = false;
        }
        if (!isset($data['order'])) {
            $data['order'] = 'desc';
        }
        $firstRow = $data['page'] * $listRows;
        // 获取评论列表
        $commentList = DB::name('forum_comment')
        ->alias('c')
        ->join('bc_user_user u', 'u.id=c.user_id')
        ->join('bc_forum_zan z', 'z.comment_id=c.id and z.user_id='.$data['send_id'], 'left')
        ->field('c.*,u.head,u.nickname as user_name,z.id as zan_status')
        ->where('c.post_id', $data['post_id'])
        ->order('c.addtime '.$data['order'])
        ->limit($firstRow, $listRows)
        ->select();
        foreach ($commentList as $k => $v) {
            $commentList[$k]['imgs'] = json_decode($v['imgs']);
            // 获取每个评论的回复
            $commentList[$k]['replys'] = DB::name('forum_reply')
            ->alias('r')
            ->join('bc_user_user u1', 'u1.id=r.user_id')
            ->join('bc_user_user u2', 'u2.id=r.reply_id')
            ->field('r.*, u1.nickname as user_name, u2.nickname as reply_user')
            ->where('comment_id', $v['id'])
            ->order('addtime asc')
            ->select();
            $commentList[$k]['addtime'] = date('m-d H:i', $v['addtime']);
        }
        if(count($commentList) < $listRows){
            $end = true;
        }else{
            $end = false;
        }
        return [
            'type'=>'resopneGetComment',
            'status'=>true,
            'commentList'=>$commentList,
            'zoneInfo'=>$zoneInfo,
            'postInfo'=>$postInfo,
            'gold'=> isset($result['userInfo']['gold']) ? $result['userInfo']['gold'] : -1,
            'noticeMsg'=>isset($result['noticeMsg']) ? $result['noticeMsg'] : false,
            'msgData'=>isset($result['msgData']) ? $result['msgData'] : false,
            'lastTime'=>$lastTime,
            'totalNum'=>$totalNum,
            'rules'=>$rules,
            'end'=>$end,
        ];
    }

    /**
     * *****************************************************************************************************************
     * 帖子支付
     * *****************************************************************************************************************
     */
    public static function payForTheme($user_id, $postInfo, $zoneInfo)
    {
        // 获取用户的金币信息
        $userInfo = DB::name('user_user')->where('id', $user_id)->field('gold,username,phonenumber')->find();
        if ($userInfo['gold'] < $zoneInfo['gold']) {
            // 用户金币不足够看帖所需金币
            return [
                'type'=>'resopneGetComment',
                'status'=>false,
                'msg'=>'当前金币为'.$userInfo['gold'].'，不足以支付看帖所需金币！',
            ];
        }
        // 用户金币流水
        $userGoldDetail = [
            '看帖前'=>$userInfo['gold'],
            '看帖花费'=>$zoneInfo['gold'],
            '看帖后'=>$userInfo['gold']-$zoneInfo['gold'],
        ];
        $userInfo['gold'] -= $zoneInfo['gold'];
        $userInfo['id'] = $user_id;
        // 更新用户的金币信息
        if (DB::name('user_user')->update($userInfo)) {
            DB::name('forum_reader')->insert(['post_id'=>$postInfo['id'], 'user_id'=>$user_id]);
            $noticeMsg = [
                'send_id'=>-10086,
                'get_id'=>$user_id,
                'time'=>time(),
                'status'=>-1,
                'content'=>'(@title:金币扣除通知@)您因查看帖子“'.$postInfo['title'].'”消费了'.$zoneInfo['gold'].'个金币',
                'flag'=>0,
            ];
            $nmid = DB::name('user_message')->insertGetId($noticeMsg);
            $noticeMsg['id'] = $nmid;
            // 保存用户的金币流水
            Base::goldHistory($userInfo['username']?$userInfo['username']:$userInfo['phonenumber'], '看帖开销', $userGoldDetail);

            if ($zoneInfo['pride_percent']) {
                $pride_pool = $postInfo['pride_pool'];
                // 看帖奖励
                $postInfo['pride_pool'] += $zoneInfo['gold'] * $zoneInfo['pride_percent'] * 0.01;
                // 判断看帖奖励金币池是否大于或等于1
                if ($postInfo['pride_pool'] >= 1) {
                    // 获取奖励池的整数部分
                    $prideGold = floor($postInfo['pride_pool']);
                    // 作者的金币流水详情
                    $authorGoldDetail = [
                        '奖励前'=>$postInfo['gold'],
                        '奖励金额'=>$prideGold,
                        '奖励后'=>$postInfo['gold'] + $prideGold,
                    ];
                    // 更新作者的金币细腻些
                    if (DB::name('user_user')->where('id', $postInfo['user_id'])->setInc('gold', $prideGold)) {
                        $postInfo['pride_pool'] -= $prideGold;
                        if ($pride_pool == $postInfo['pride_pool']) {
                            $msgData = [
                                'send_id'=>-10086,
                                'get_id'=>$postInfo['user_id'],
                                'time'=>time(),
                                'status'=>-1,
                                'content'=>'(@title:帖子分红通知@)您的帖子“'.$postInfo['title'].'”获利'.$prideGold.'个金币，已经发放',
                                'flag'=>0,
                            ];
                            $mid = DB::name('user_message')->insertGetId($msgData);
                            $msgData['id'] = $mid;
                            // 保存作者的金币流水
                            Base::goldHistory($postInfo['username']?$postInfo['username']:$postInfo['phonenumber'], '帖子分红', $authorGoldDetail);
                        } else {
                            // 更新金币池信息
                            if (DB::name('forum_post')->where('id', $postInfo['id'])->update(['pride_pool'=>$postInfo['pride_pool']])) {
                                $postInfo['gold'] += $prideGold;
                                $msgData = [
                                            'send_id'=>-10086,
                                            'get_id'=>$postInfo['user_id'],
                                            'time'=>time(),
                                            'status'=>-1,
                                            'content'=>'(@title:帖子分红通知@)您的帖子“'.$postInfo['title'].'”获利'.$prideGold.'个金币，已经发放',
                                            'flag'=>0,
                                        ];
                                $mid = DB::name('user_message')->insertGetId($msgData);
                                $msgData['id'] = $mid;
                                // $postInfo['pride_gold'] = $prideGold;
                                // 保存作者的金币流水
                                Base::goldHistory($postInfo['username']?$postInfo['username']:$postInfo['phonenumber'], '帖子分红', $authorGoldDetail);
                            } else {
                                $msgData = false;
                                DB::name("user_user")->where('id', $postInfo['user_id'])->setDec('gold', $prideGold);
                            }
                        }
                    }else{
                        $msgData = false;
                    }
                }else{
                    $msgData = false;
                }
            }else{
                $msgData = false;
            }
            return [
                'type'=>'resopneGetComment',
                'status'=>true,
                'noticeMsg'=>$noticeMsg,
                'msgData'=>$msgData,
                'userInfo'=>$userInfo
            ];
        } else {
            return [
                'type'=>'resopneGetComment',
                'status'=>false,
                'msg'=>'金币支付失败！',
            ];
        }
    }

    /**
     * *****************************************************************************************************************
     * 获取帖子列表
     * *****************************************************************************************************************
     */
    public static function getPostList($data)
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
        ->join('bc_forum_reader r', 'r.post_id=p.id and r.user_id='.$data['send_id'], 'left')
        ->field('u.head,r.id as read_status,p.*,u.nickname as user_name')
        ->where('zone_id', $data['zone_id'])
        ->order('addtime desc')
        ->limit($firstRow, $listRows)
        ->select();
        foreach ($postList as $key => $vo) {
            if ($vo['imgs']) {
                $postList[$key]['imgs'] = json_decode($vo['imgs']);
            }
        }
        if (count($postList) < $listRows) {
            $end = true;
        } else {
            $end = false;
        }
        return [
            'type'=>'resopneGetPostList',
            'postList'=>$postList,
            'zoneInfo'=>$zoneInfo,
            'end'=>$end,
        ];
    }
    /**
     * *****************************************************************************************************************
     * 获取论坛列表
     * *****************************************************************************************************************
     */
    public static function getThemeList($data)
    {
        $listRows = 20;
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
        if (count($zoneList) < $listRows) {
            $end = true;
        } else {
            $end = false;
        }
        return [
            'type'=>'ResponeGetThemeList',
            'zoneList'=>$zoneList,
            'end'=>$end,
        ];
    }

    /**
    * *****************************************************************************************************************
    * 获取发帖规则
    * *****************************************************************************************************************
    */
    public static function getPostRules($data)
    {
        // 获取用户最后发帖时间
        $lastTime = DB::name("forum_post")->where(['user_id'=>$data['uid'],'zone_id'=>$data['zone_id']])->order('addtime desc')->value('addtime');
        // if($lastTime){
        $tieNum = DB::name('forum_post')->where('user_id', $data['uid'])->count();
        $rules = Rules::getPostRules();
        return [
            'type'=>"responseGetPostRules",
            'tieNum'=>$tieNum, //用户已发贴数·
            'lastTime'=>$lastTime, //用户最后一次发帖的时间
            'rules'=>$rules,
        ];
    }
}
