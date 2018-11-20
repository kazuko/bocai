<?php
namespace app\Server\controller;

use app\Server\controller\Chat;
use app\Server\controller\RedBag;
use app\Server\controller\Transfer;
use app\Server\controller\Redis;
use app\Server\controller\Rules;
use app\Server\controller\Medal;
use app\Server\controller\Forum;
use app\Server\controller\Index;

class DataProcer
{
    public function __construct()
    {
        Rules::saveRulesToRedis();
    }
    /**
     * 允许敏感词触发次数减一
     */
    public static function allowTimesDec($data)
    {
        Index::allowTimesDec($data);
    }

    /**
     * 抢红包（群发红包）
     */
    public static function chatRoomRedbag($data)
    {
        return RedBag::chatRoomRedbag($data);
    }
    
    /**
     * 获取个人聊天记录
     */
    public static function getChatRecords($data)
    {
        return Chat::getChatRecords($data);
    }
    /**
     * 保存聊天室记录
     */
    public static function ChatRoomMsg($data)
    {
        return Chat::ChatRoomMsg($data);
    }
    
    /**
     * 获取聊天室的信息列表
     */
    public static function getChatRoomMsgList($data)
    {
        return Chat::getChatRoomMsgList($data);
    }

    /**
     * 会员注册
     */
    public static function registerMember($data)
    {
        return Index::registerMember($data);
    }
    
    
    /**
     * 获取验证码
     */
    public static function getYanZhengMa($data)
    {
        return Index::getYanZhengMa($data);
    }
    
    /**
     * 获取注册规则
     */
    public static function getRegisterRules($data)
    {
        return Rules::getRegisterRules();
    }
    /**
     * 修改个人勋章的状态
     */
    public static function changeMedalStatus($data)
    {
        return Medal::changeMedalStatus($data);
    }
    /**
     * 获取勋章列表
     */
    public static function getMedalList($data)
    {
        return Medal::getMedalList($data);
    }
    /**
     * 领取好友转账
     */
    public static function reciveTransfer($data)
    {
        return Transfer::reciveTransfer($data);
    }
    /**
     * 获取转账信息
     */
    public static function getTransferInfo($data)
    {
        return Transfer::getTransferInfo($data);
    }
    /**
     * 转账
     */
    public static function transferAccounts($data)
    {
        return Transfer::transferAccounts($data);
    }
    /**
     * 获取我的回帖列表
     */
    public static function getMyReplyList($data)
    {
        return Forum::getMyReplyList($data);
    }
    /**
     * 获取我的发帖列表
     */
    public static function getMyThemeList($data)
    {
        return Forum::getMyThemeList($data);
    }
    /**
     * 发帖子
     */
    public static function postTheme($data)
    {
        return Forum::postTheme($data);
    }
    /**
     * 回复评论
     */
    public static function ReplyComment($data)
    {
        return Forum::ReplyComment($data);
    }

    /**
     * 点赞评论
     */
    public static function zanComment($data)
    {
        return Forum::zanComment($data);
    }
    /**
     * 发表评论
     */
    public static function themeComment($data)
    {
        return Forum::themeComment($data);
    }
    /**
     * 获取帖子评论
     */
    public static function getComment($data)
    {
        return Forum::getComment($data);
    }

    /**
     * 获取帖子列表
     */
    public static function getPostList($data)
    {
        return Forum::getPostList($data);
    }
    /**
     * 获取论坛列表
     */
    public static function getThemeList($data)
    {
        return Forum::getThemeList($data);
    }

    /**
     * 获取发帖规则
     */
    public static function getPostRules($data)
    {
        return Forum::getPostRules($data);
    }


    /**
     * 获取收到的红包列表
     */
    public static function getReciveRedList($data)
    {
        return RedBag::getReciveRedList($data);
    }
    /**
     * 获取发送的红包列表
     */
    public static function getSendRedList($data)
    {
        return RedBag::getSendRedList();
    }

    /**
     * 修改登录密码
     */
    public static function changeLoginPassword($data)
    {
        return Index::changeLoginPassword($data);
    }
    /**
     * 修改签名
     */
    public static function changeSignature($data)
    {
        return Index::changeSignature($data);
    }
    /**
     * 修改昵称
     */
    public static function changeNickName($data)
    {
        return Index::changeNickName($data);
    }

    /**
     * 修改我的设置
     */
    public static function settingChange($data)
    {
        return Index::settingChange($data);
    }
    /**
     * 从社区银行取出金币
     */
    public static function getGoldFromBank($data)
    {
        return Index::getGoldFromBank($data);
    }
    /**
     * 存储金币
     */
    public static function storageGold($data)
    {
        return Index::storageGold($data);
    }
    /**
     * 红包详情
     */
    public static function RedDetail($data)
    {
        return RedBag::RedDetail($data);
    }
    /**
     * 登陆处理
     */
    public static function logining($data)
    {
        return Index::logining($data);
    }

    /**
     * 握手处理
     */
    public static function handle($data)
    {
        return Index::handle($data);
    }
    /**
     * 获取app信息
     */
    public static function getAppInfo($data)
    {
        return Index::getAppInfo($data);
    }

    /**
     * 领取个人红包
     */
    public static function reciveRedBagFromFriend($data)
    {
        return RedBag::reciveRedBagFromFriend($data);
    }


    /**
     * 保存红包信息
     */
    public static function sendRedBagToFriend($data)
    {
        return RedBag::sendRedBagToFriend($data);
    }
    /**
     * 修改交易密码
     */
    public static function changeTrading($data)
    {
        return Index::changeTrading($data);
    }
    /**
     * 删除陌生人信息
     */
    public static function delStrange($data)
    {
        return Chat::delStrange($data);
    }
    /**
     * 聊天记录保存
     */
    public static function chatRecord($data)
    {
        return Chat::chatRecord($data);
    }

    /**
     * 修改系统消息的查阅状态
     */
    public static function changeSystemReadStatus($data)
    {
        return Chat::changeSystemReadStatus($data);
    }
    /**
     * 好友请求处理方法
     */
    public static function AgreeFriendRequest($data)
    {
        return Chat::AgreeFriendRequest($data);
    }


    /**
     * 删除消息
     */
    public static function DeleteMessage($data)
    {
        return Chat::DeleteMessage($data);
    }
    /**
     * 删除好友
     */
    public static function deleteFriend($data)
    {
        return Chat::deleteFriend($data);
    }
    /**
     * 下线处理
     */
    public static function downLine($data)
    {
        return Chat::downLine($data);
    }

    public static function addFriendRequest($data)
    {
        return Chat::addFriendRequest($data);
    }
    /**
     * 查找朋友
     */
    public static function searchFriends($data)
    {
        return Chat::searchFriends($data);
    }
    /**
     * 更新信息的阅读状态
     */
    public static function updateMsgReadStatus($data)
    {
        return Chat::updateMsgReadStatus($data);
    }

    /**
     * 获取用户的链接标识符
     */
    public static function getUserConnectionID($uid)
    {
        return Index::getUserConnectionID($uid);
    }
}
