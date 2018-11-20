<?php
namespace app\Server\controller;

use think\Db;
use app\Server\controller\Redis;

class Rules
{
    /**
     * 将规则保存到redis缓存器
     */
    public static function saveRulesToRedis()
    {
        // $redis = Redis::Init();
        $systemInfo = DB::name('system_info')->order('id desc')->find();
        return Redis::set('system_info', json_encode($systemInfo));
    }

    /**
     * 获取注册规则
     */
    public static function getRegisterRules()
    {
        $systemInfo = json_decode(Redis::get('system_info'), true);
        return [
            'type'=>'responeGetRegisterRules',
            'rules'=>[
                'register_prefix'=>$systemInfo['register_prefix'],
                'register_mod'=>$systemInfo['register_mod'],
                'register_start_number'=>$systemInfo['register_start_number'],
                'register_keyword'=>$systemInfo['register_keyword'],
                'register_max_length'=>$systemInfo['register_max_length'],
                'register_check_key'=>$systemInfo['register_check_key']
            ],
        ];
    }

    /**
     * 获取用户回帖的限制规则和过滤规则
     * `huitie_open` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否允许回帖：0、允许；1、禁止；2、限时禁止',
     * `huitie_time_start` int(11) NOT NULL COMMENT '限时禁止的起始时间',
     * `huitie_time_end` int(11) NOT NULL COMMENT '限时禁止的终止时间',
     * `huitie_order` tinyint(3) NOT NULL DEFAULT '1' COMMENT '回帖楼层排序：0、顺序；1、倒序',
     * `huitie_num` int(11) NOT NULL DEFAULT '0' COMMENT '回帖奖励帖数',
     * `huitie_gold` int(11) NOT NULL DEFAULT '0' COMMENT '回帖奖励金币',
     * `huitie_sliver` int(11) NOT NULL DEFAULT '0' COMMENT '回帖奖励积分',
     * `huitie_vip_gold` int(11) NOT NULL DEFAULT '0' COMMENT '回帖奖励vip额外奖励',
     * `huitie_vip_sliver` int(11) NOT NULL DEFAULT '0' COMMENT '回帖奖励vip额外奖励',
     * `huitie_repeate` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否允许发重复的内容：0、允许；1、不允许',
     * `huitie_refender_seconds` int(11) NOT NULL DEFAULT '60' COMMENT '防刷间隔',
    */
    public static function getHuitieFilters()
    {
        $systemInfo = json_decode(Redis::get('system_info'), true);
        return [
            'huitie_open'=>$systemInfo['huitie_open'],
            'huitie_time_start'=>$systemInfo['huitie_time_start'],
            'huitie_time_end'=>$systemInfo['huitie_time_end'],
            'huitie_order'=>$systemInfo['huitie_order'],
            'huitie_num'=>$systemInfo['huitie_num'],
            'huitie_gold'=>$systemInfo['huitie_gold'],
            'huitie_sliver'=>$systemInfo['huitie_sliver'],
            'huitie_vip_gold'=>$systemInfo['huitie_vip_gold'],
            'huitie_repeate'=>$systemInfo['huitie_repeate'],
            'huitie_refender_seconds'=>$systemInfo['huitie_refender_seconds'],

            'filter_mod'=>$systemInfo['filter_mod'], //过滤模式（0：关闭；1：替代；2：禁止输入；3：禁止发布）
            'filter_replace'=>$systemInfo['filter_replace'], // 替换字符串
            'filter_words'=>$systemInfo['filter_words'], // 过滤关键词
            'filter_show'=>$systemInfo['filter_show'], // 高亮显示关键词（0：关闭；1：开启）
            'filter_tips'=>$systemInfo['filter_tips'], // 警告语
            'filter_allow'=>$systemInfo['filter_allow'], // 允许次数
            'defriend_mod'=>$systemInfo['defriend_mod'], // 加黑类型（0：关闭；1：禁止发言；2：禁止登陆）
            'filter_notice'=>$systemInfo['filter_notice'] // 是否通知版主（0：关闭；1：开启）
        ];
    }


    /**
    * 获取发帖规则
    */
    public static function getPostRules()
    {
        // 获取系统设置
        $systemInfo = json_decode(Redis::get('system_info'), true);
        return [
            'show'=>(int)$systemInfo['tie_pride_show'], //'发帖奖励：1、显示；2、关闭'
            'num'=>$systemInfo['tie_pride_num'], //'发帖奖励帖数'
            'gold'=>$systemInfo['tie_pride_gold'], //'奖励金币'
            'sliver'=>$systemInfo['tie_pride_sliver'], //'发帖奖励积分'
            'title_words'=>$systemInfo['tie_limit_words'], //'发帖标题字数'
            'new_seconds'=>$systemInfo['tie_limit_seconds'], //'新用户限制：秒'
            'recharge_flag'=>$systemInfo['tie_limit_relieve'], //'充值解除限制：1表示开启；0表示关闭'
            'refender_seconds'=>$systemInfo['tie_limit_refender_seconds'], //'防刷间隔：秒'
            'filter_mod'=>$systemInfo['filter_mod'], //过滤模式（0：关闭；1：替代；2：禁止输入；3：禁止发布）
            'filter_replace'=>$systemInfo['filter_replace'], // 替换字符串
            'filter_words'=>$systemInfo['filter_words'], // 过滤关键词
            'filter_show'=>$systemInfo['filter_show'], // 高亮显示关键词（0：关闭；1：开启）
            'filter_tips'=>$systemInfo['filter_tips'], // 警告语
            'filter_allow'=>$systemInfo['filter_allow'], // 允许次数
            'defriend_mod'=>$systemInfo['defriend_mod'], // 加黑类型（0：关闭；1：禁止发言；2：禁止登陆）
            'filter_notice'=>$systemInfo['filter_notice'] // 是否通知版主（0：关闭；1：开启）
        ];
    }
}
