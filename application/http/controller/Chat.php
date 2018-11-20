<?php
namespace app\http\controller;
// use think\Controller;
use think\Db;


class Chat{
    private $friendsRecords;
    private $userFriends;

    public static function friendRecords($data){
        if(isset($this->userFriends[$data['fid']])){
            $friendList = $this->userFriends[$data['fid']];
        }else{
            $list = DB::name('user_friends')->where('user_id',$data['fid'])->select();
            
        }
        $reocrd = [
            'send_id'=>$data['uid'],
            'get_id'=>$data['fid'],
            'time'=>time(),
            'status'=>$status,
            'content'=>$data['msg'],
            'flag'=>0
        ];
        $this->friendsRecords[] = $data;
    }
}