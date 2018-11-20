<?php
namespace app\index\controller;
use app\index\controller;
use think\Db;
use think\facade\Session;
use think\facade\Config;
use app\MyCommon\controller\Base;

class User extends Common
{
    public function index(){
        $firstRow = 0;
        $listRows = 15;
        $where = [];
        if($this->request->isPost()){
            $param = $this->request->param();
            // 查封/解封操作
            if(isset($param['status'])){
                if(DB::name('user_user')->update($param)){
                    $result = [
                        'code'=>0,
                        'msg'=>'ok',
                    ];
                }else{
                    $result = [
                        'code'=>-1,
                        'msg'=>'保存失败！',
                    ];
                }
                echo json_encode($result);
                exit();
            }else if(isset($param['soso'])){
                // 搜索 查封
                if($param['soso']=='查封'){
                    $where = [['status','=',0]];
                // 搜索 解封
                }else if($param['soso']=='解封'){
                    $where = [['status','=',1]];
                // 用户账号、ip、昵称、电话 搜索
                }else{
                    $where = [
                        ['nickname|username|ips|phoneNumber','like','%'.$param['soso'].'%'],
                    ];
                }
            }else{
                //翻页 
                $firstRow = $param['page'] * $listRows;
                if($param['condition']=='查封'){
                    $where = [['status','=',0]];
                }else if($param['condition']=='解封'){
                    $where = [['status','=',1]];
                }else{
                    $where = [
                        ['nickname|username|ips|phoneNumber','like','%'.$param['condition'].'%'],
                    ];
                }
                // 获取用户列表
                $list = DB::name('user_user')
                ->where($where)
                ->field('id,username,nickname,integral,phoneNumber,gold,bank,ips,status')
                ->order('lasttime', 'desc')
                ->limit($firstRow,$listRows)
                ->select();

                foreach ($list as $key => $value) {
                    $list[$key]['ips'] = json_decode($value['ips'],true);
                }
                echo json_encode($list);
                exit();
            }
        }
        // 获取用户信息
        $list = DB::name('user_user')
        ->where($where)
        ->field('id,phoneNumber,integral,nickname,username,gold,bank,ips,status')
        ->order('lasttime', 'desc')
        ->limit($firstRow,$listRows)
        ->select();
        // 将IP字符串变成数组
        foreach ($list as $key => $value) {
            if($value['ips']){
                $list[$key]['ips'] = json_decode($value['ips'],true);
            }else{
                $list[$key]['ips'] = [];
            }
        }
        if(!isset($param['soso'])){
            $param['soso'] = '';
        }
        $this->assign([
            'title'=>'用户管理',
            'back_url'=>url('Index/Index/index'),
            'list'=>$list,
            'soso'=>$param['soso']
        ]);
        return $this->fetch();
    }

    

    // 发放奖励
    public function pride(){
        $param = $this->request->param();
        // 获取所有勋章信息
        $xunzhangs = DB::name('system_ranks')->order('flag asc')->select();
        // 获取货币1和货币2的货币名称
        $coin = DB::name('system_info')->field('coin1_name,coin2_name')->find();
        $list = [];
        // 勋章分类 积分勋章、金币勋章、特殊勋章
        foreach ($xunzhangs as $key => $value) {
            $list[$value['flag']][] = $value;
        }
        $this->assign([
            'title'=>'奖励发放',
            'back_url'=>url('Index/User/index'),
            'userInfo'=>$param,
            'xunzhangs'=>$list,
            'coin'=>$coin,
        ]);
        return $this->fetch();
    }

    // 获取用户勋章信息
    public function getUserRanks(){
        $username = rtrim($this->request->param('username'));
        // 判断当前用户是否存在
        if($userId = DB::name('user_user')->where('username',$username)->value('id')){
            // 获取用户已经拥有的勋章
            $ranks = DB::name('user_medal')
            ->field('medal_id')
            ->where('user_id',$userId)
            ->select();
        }else{
            // 用户不存在
            $ranks['code'] = 'unfind';
        }
        echo json_encode($ranks);
    }

    // 执行奖励操作
    public function updateUserRanks(){
        $param = $this->request->param();
        // 获取用户原本的金币和机锋
        $userInfo = DB::name('user_user')
        ->where('username',$param['username'])
        ->field('id,gold,bank,integral')
        ->find();
        // 判断该用户是否存在
        if(!$userInfo||empty($userInfo)){
            echo json_encode(['code'=>404,'msg'=>'没有该用户！']);
            exit();
        }
        // 判断有没有赠送金币和积分
        if((int)$param['gold']||(int)$param['sliver']){
            // 判断是否需要保存金币流水
            if((int)$param['gold']){
                // 金币流水详情
                $goldDetail['奖励前'] = $userInfo['gold']+$userInfo['bank'];
                $goldDetail['奖励账号'] = $param['username'];
                $goldDetail['奖励金额'] = (int)$param['gold'];
                $goldDetail['奖励后'] = $userInfo['gold']+$userInfo['bank']+(int)$param['gold'];
            }
            // 判断是否需要保持积分流水
            if((int)$param['sliver']){
                // 积分流水详情
                $sliverDetail['奖励前'] = $userInfo['integral'];
                $sliverDetail['奖励账号'] = $param['username'];
                $sliverDetail['奖励积分'] = (int)$param['sliver'];
                $sliverDetail['奖励后'] = $userInfo['integral'] + (int)$param['sliver'];
            }
            // 加上赠送积分和金币
            $userInfo['gold'] += (int)$param['gold'];
            $userInfo['integral'] += (int)$param['sliver'];
        }
        // 判断是否赠送勋章
        if(!empty($param['medal_id'])){
            // 赠送勋章列表
            foreach ($param['medal_id'] as $key => $value) {
                $data[] = [
                    'user_id' =>    $userInfo['id'],
                    'medal_id' =>   $value,
                    'status'=>0
                ];
            }
        }
        // 返回结果
        $result = [
            'code'=>0,
            'msg'=>'保存成功！',
        ];
        // 启动事务
        DB::startTrans();
        try {
            // 保存用户金币积分数据
            if((int)$param['gold']||(int)$param['sliver']){
                DB::name('user_user')->where('id',$userInfo['id'])->update($userInfo);
            }
            //保存金币流水记录
            if(isset($goldDetail)&&!empty($goldDetail)){
                Base::goldHistory($param['username'],'奖励发放',$goldDetail);
            }
            // 保存积分流水记录
            if(isset($sliverDetail)&&!empty($sliverDetail)){
                Base::sliverHistory($param['username'],'奖励发放',$sliverDetail);
            }
            // 保存勋章信息
            if(isset($data)&&!empty($data)){
                DB::name('user_medal')->insertAll($data);
            }
            // 提交事务
            DB::commit();
        } catch (\Exception $e) {
            $result = [
                'code'=>-1,
                'msg'=>'保存失败！',
                'error'=>$e->message,
            ];
            // 回滚事务
            DB::rollback();
        }
        echo json_encode($result);
    }

    // 积分流水记录
    public function sliver(){
        $this->getData('sliver_history');
        $this->assign([
            'title'=>'积分流水',
            'url'=>url('Index/User/sliver'),
        ]);
        return $this->fetch('gold');
    }
    // 金币流水记录
    public function gold(){
        $this->getData('gold_history');
        $this->assign([
            'title'=>'金币流水',
            'url'=>url('Index/User/gold'),
        ]);
        return $this->fetch();
    }

    public function getData($tabName){
        $firstRow = 0;
        $listRows = 15;
        $param = $this->request->param();
        // 判断是否是翻页
        if(isset($param['page'])&&$param['page']){
            $firstRow = $param['page'] * $listRows;
            // 获取流水记录列表
            $list = DB::name($tabName)
            ->field('operation,detail,time')
            ->where('username',$param['username'])
            ->limit($firstRow,$listRows)
            ->select();
            $str = '';
            // 拼接流水记录字符串
            foreach ($list as $key => $value) {
                if($value){
                    $str .= '<li>';
                    $str .= '<dl>';
                    $str .= '<span>'.date('Y-m-d H:i:s',$value['time']).'</span>';
                    $str .= '<span>'.$value['operation'].'</span>';
                    $str .= '</dl>';
                    $str .= '<dl class="clear"></dl>';
                    $value['detail'] = json_decode($value['detail'],true);
                    foreach ($value['detail'] as $k => $v) {
                        $str .= '<dl>';
                        $str .= '<span>'.$k.'</span>';
                        $str .= '<span>'.$k.'</span>';
                        $str .= '</dl>';
                        $str .= '<dl class="clear"></dl>';
                    }
                    $str .= '</li>';
                }
            }
            $result['str'] = $str;
            echo json_encode($result);
            exit();
        }
        // 获取流水记录列表
        $list = DB::name($tabName)
        ->field('operation,detail,time')
        ->where('username',$param['username'])
        ->limit($firstRow,$listRows)
        ->select();
        foreach ($list as $key => $value) {
            if($value['detail']){
                $list[$key]['detail'] = json_decode($value['detail'],true);
            }
        }
        $this->assign([
            'back_url'=>'javascript:history.go(-1)',
            'list'=>$list,
            'username'=>$param['username'],
        ]);
    }
}