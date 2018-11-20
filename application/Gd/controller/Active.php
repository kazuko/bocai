<?php 
namespace app\Gd\controller;
use think\Controller;
use think\facade\Session;
use think\Db;
use think\facade\Request;
use app\Gd\model\Games_Gd_odds as Odds;
/*  功能函数
    ** 更新用户剩余金币，以数据库为准 refreshRemaining(
    ** 记录用户最新金币 table:user_user  refleshUser(
    ** 结算订单 table:games_bet_history recordeBetHistory($dice)
    
    ** 记录和值走势 table:bc_games_bjkp10_guanyahe_liangmian bc_games_bjkp10_guanyahe_haomafenbu recordeGuanYaHe($dice)
    ** 记录基本走势 table:games_Gd_basic_trend recordeBasicTrend($dice)
    ** 记录定胆位  table:games_Gd_dindanwei recordeDinDanWei($data);
    ** 记录开奖记录 table:games_Gd_dice_history recordeDiceHistory($dice);
    ** 
*/

class Active extends Controller
{


    //更新用户剩余金币，以数据库为准 公用
    public function refreshRemaining($order){
        $gold = Db::name('user_user')->where(['id'=>$order['id']])->value('gold');
        $remaining = $gold - $order['allGold'];
        Db::name('user_user')->where(['id'=>$order['id']])->update(['gold'=>$remaining]);
        return $remaining;
    }

    //记录用户最新金币 table:user_user 公用
    public function refleshUser($id,$remaining){
        Db::name('user_user')->where('id',$id)->update(['gold'=>$remaining]);
    }

    //结算订单 table:games_bet_history 公用
    public function recordeBetHistory($orderNumber,$dice,$win){
        $id = Db::name('games_bet_history')->where(['order_number'=>$orderNumber])->value('id');
        $status = 1;//开奖1
        if($win > 0){
            $status = 2;//中奖2
        }
        $data = [
            'open_code'=>$dice['opencode'],
            'win'=>$win,
            'open_time'=>$dice['opentime'],
            'status'=>$status,
        ];
        Db::name('games_bet_history')->where(['id'=>$id])->update($data);
    }



    //记录基本走势 table:games_Gd_basic_trend
    public function recordeBasicTrend($dice){
        $opencode = explode(',',$dice['opencode']);
        $issue =$dice['expect'];//期号

        $sum_sum = array_sum($opencode);
        $daxiao = $sum_sum==30?"he":($sum_sum>30?"da":($sum_sum<30?"xiao":"fault"));
        $danshuang = $sum_sum%2?"dan":"shuang";
        $sum = [
            "sum"=>$sum_sum,
            "daxiao"=>$daxiao,
            "danshuang"=>$danshuang,
        ];
        $sum = json_encode($sum);
        
        $open_time = $dice['opentime'];
        $data = [
            "expect"=>$issue,//期号
            "open_code"=>json_encode($opencode),//开奖号码字符串
            "sum"=>$sum,
            // "longhu"=>$longhu,//玩法里面找不到龙虎，走势图看不出龙虎规则
            // "qiansan"=>$qiansan,//没有相应的玩法
            // "zhongsan"=>$zhongsan,//没有相应的玩法
            // "housan"=>$housan,//没有相应的玩法
        ];
        Db::name('games_gd_basic_trend')->insert($data);
    }

    /**
     * 记录定胆位 table:games_Gd_dindanwei
     * 冠亚和：3-11小   12-19 大
     */
    public function recordeDinDanWei($dice){
        $opencode = explode(',',$dice['opencode']);
        $issue =$dice['expect'];//期号
        $open_time = $dice['opentime'];
        //一个5*11的大表
        $list = Db::name("games_gd_dindanwei")->field('id,expect,open_code',true)->order('id desc')->find();
        if($list){
            for($i = 1;$i <= 5;$i++){
                //i为第几球
                $index = "ball_".$i;
                $list[$index] = json_decode($list[$index],true);
                //j为第几球的第几位号码
                for($j = 1;$j <= 11;$j++){
                    $num = "num".$j;
                    if( $j == $opencode[$i-1]){
                        $list[$index][$num] = 0;
                    }else{
                        $list[$index][$num]++;
                    }
                }
            }
        }
        else{
            $list = [];
            for($i = 1;$i <= 5;$i++){
                $index = "ball_".$i;
                for($j = 1;$j <= 11;$j++){
                    //i为第几球
                    $num = "num".$j;
                    if( $j == $opencode[$i-1]){
                        $list[$index][$num] = 0;
                    }else{
                        $list[$index][$num] = 1;
                    }
                }
            }
        }
        //分别把每个号码表进行转换
        $data = [];
        foreach($list as $key=>$vo){
            $data[$key] = json_encode($list[$key]);
        }
        //添加开奖号码以及期数
        $data['open_code'] = json_encode($opencode);//开奖号码
        $data['expect'] = $issue;  //期号
        $data['open_time']=$dice['opentime'];//开奖时间
        Db::name('games_gd_dindanwei')->Insert($data);
    }


    /**
     * 记录开奖结果 table:games_Gd_dice_history
     * 冠亚和：3-11小   12-19 大
     */
    public function recordeDiceHistory($dice){
        $opencode = explode(',',$dice['opencode']);
        $issue = $dice['expect'];//期号
        $sum = array_sum($opencode);
        $kuadu = max($opencode)-min($opencode);
        $open_time = $dice['opentime'];//开奖时间
        $data = [
            "expect"=>$issue,
            "open_code"=>json_encode($opencode),
            "open_time"=>$dice['opentime'],
            "sum"=>$sum,
            "kuadu"=>$kuadu,
        ];
        Db::name('games_gd_dice_history')->insert($data);
    }

}
 ?>