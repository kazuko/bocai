<?php 
namespace app\Jsks\controller;
use think\Controller;
use think\facade\Session;
use think\Db;
use think\facade\Request;
use app\Jsks\model\Games_jsks_odds as Odds;
/*  功能函数
    ** 计算和值-和值 sum($dice)
    ** 计算和值-大小单双 size($dice)
    ** 计算三不同号 threeDiffrent($dice)
    ** 计算三连号通选 threeConAll($dice)
    ** 计算三同号通选 threeSameDb($dice)
    ** 计算三同号单选 threeSameSg($dice)
    ** 二同号复选 twoSameDb($dice)
    ** 二同号单选 twoSameSg($dice)
    ** 二不同号 twoDiffrent($dice)
    ** 更新用户剩余金币，以数据库为准 refreshRemaining(
    ** 记录用户最新金币 table:user_user  refleshUser(
    ** 结算订单 table:games_bet_history recordeBetHistory($dice)
    ** 记录和值走势 table:games_jsks_sum_trend recordeSumTrend($dice)
    ** 记录基本走势 table:games_jsks_basic_trend recordeBasicTrend($dice)
    ** 记录开奖记录 table:games_jsks_dice_history recordeDiceHistory($dice);
    ** 
*/

class Active extends Controller
{

	//计算和值-
    public function sum($dice){
        $sum = $dice[0]+$dice[1]+$dice[2];
        $result = 'sum_'.$sum;
        return $sum;
    }

    //计算和值-大小单双
    public function size($dice){
        $result = [];
        $sum = $dice[0]+$dice[1]+$dice[2];
        if($sum%2 == 0){
            $result[count($result)] = 'double';
            if($sum >=11){
                $result[count($result)] = 'big';
                $result[count($result)] = 'bigDouble';
            }
            else{
                $result[count($result)] = 'small';
                $result[count($result)] = 'smallDouble';                
            }
        }
        else{
            $result[count($result)] = 'single';
            if($sum >=11){
                $result[count($result)] = 'big';
                $result[count($result)] = 'bigSingle';
            }
            else{
                $result[count($result)] = 'small';
                $result[count($result)] = 'smallSingle';                
            }
        }
        return $result;
    }

    //计算三不同号
    public function threeDiffrent($dice){
        if(($dice[0]!=$dice[1])&&($dice[0]!=$dice[2])&&($dice[1]!=$dice[2])){
            $result = ['code'=>1,'dice'=>$dice];
        }
        else{
            $result = ['code'=>0];
        }
        return $result;
    }

    //计算三连号通选
    public function threeConAll($dice){
        if((abs($dice[2]-$dice[0])<=2)&&(abs($dice[2]-$dice[1])<=2)&&(abs($dice[1]-$dice[0])<=2)){
            $result = ['code'=>1];
        }
        else{
            $result = ['code'=>0];
        }
        return $result;
    }

    //计算三同号通选
    public function threeSameDb($dice){
        $result = (($dice[0]==$dice[1])&&($dice[0]==$dice[2])&&($dice[1]==$dice[2])) ? ['code'=>1] : ['code'=>0];
        return $result;
    }

    //计算三同号单选
    public function threeSameSg($dice){
        $result = (($dice[0]==$dice[1])&&($dice[0]==$dice[2])&&($dice[1]==$dice[2])) ? ['code'=>1,'dice'=>$dice] : ['code'=>0];
        return $result;        
    }   

    //二同号复选 不含豹子号码
    public function twoSameDb($dice){
        if(($dice[0]==$dice[1])&&($dice[0]!=$dice[2])){
            $result = ['code'=>1,'same'=>$dice[0]];
        }
        else if(($dice[0]==$dice[2])&&($dice[0]!=$dice[1])){
            $result = ['code'=>1,'same'=>$dice[0]];
        }
        else if(($dice[1]==$dice[2])&&($dice[1]!=$dice[0])){
            $result = ['code'=>1,'same'=>$dice[1]];
        } 
        else {
            $result = ['code'=>0];
        }      
        return $result; 
    }

    //二同号单选
    public function twoSameSg($dice){
        if(($dice[0]==$dice[1])&&($dice[0]!=$dice[2])){
            $result = ['code'=>1,'same'=>$dice[0],'diffrent'=>$dice[2]];
        }
        else if(($dice[0]==$dice[2])&&($dice[0]!=$dice[1])){
            $result = ['code'=>1,'same'=>$dice[0],'diffrent'=>$dice[1]];
        }
        else if(($dice[1]==$dice[2])&&($dice[1]!=$dice[0])){
            $result = ['code'=>1,'same'=>$dice[1],'diffrent'=>$dice[0]];
        } 
        else {
            $result = ['code'=>0];
        }      
        return $result; 
    }

    //二不同号
    public function twoDiffrent($dice){
        if(($dice[0]==$dice[1])&&($dice[1]==$dice[2])){
            $result = ['code'=>0];
        }
        else{
            $result = ['code'=>1,'dice'=>$dice];
        }
        return $result;
    }

    //更新用户剩余金币，以数据库为准
    public function refreshRemaining($order){
        $gold = Db::name('user_user')->where(['id'=>$order['id']])->value('gold');
        $remaining = $gold - $order['allGold'];
        Db::name('user_user')->where(['id'=>$order['id']])->update(['gold'=>$remaining]);
        return $remaining;
    }

    //记录用户最新金币 table:user_user 
    public function refleshUser($id,$remaining){
        echo $id;
        Db::name('user_user')->where('id',$id)->update(['gold'=>$remaining]);
    }

    //结算订单 table:games_bet_history
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


    //记录和值走势 table:games_jsks_sum_trend
    public function recordeSumTrend($dice){
        //查找最后一条记录
        $lastRecord = Db::name('games_jsks_sum_trend')->field('id',true)->order('id', 'desc')->find();
        $opencode = explode(',', $dice['opencode']);
        $sumo = $opencode[0]+$opencode[1]+$opencode[2];
        $index = "sum_".$sumo;//数组索引
        if($lastRecord){
            //数组索引下标刚好对上
            $sum_trend = $lastRecord;
            foreach($sum_trend as $sum => $trend){
                //与新和值相同数值变为0
                
                if($index == $sum){
                    $sum_trend[$sum] = 0;
                }
                //新和值与下标不同并且上一次出现的和值不是下标
                else if($index != $sum&&$sum_trend[$sum]!=0){
                    $sum_trend[$sum]++;
                }
                //新和值与下标不同并且上一次出现的和值是下标
                else{
                    $sum_trend[$sum]=1;
                }
            }
        }
        //初始化记录
        else{
            $sum_trend = [
                'sum_3'=>1,
                'sum_4'=>1,
                'sum_5'=>1,
                'sum_6'=>1,
                'sum_7'=>1,
                'sum_8'=>1,
                'sum_9'=>1,
                'sum_10'=>1,
                'sum_11'=>1,
                'sum_12'=>1,
                'sum_13'=>1,
                'sum_14'=>1,
                'sum_15'=>1,
                'sum_16'=>1,
                'sum_17'=>1,
                'sum_18'=>1
            ];
            foreach($sum_trend as $sum =>$trend){
                if($index == $sum){
                    echo $sum."-".$sumo;
                    $sum_trend[$sum] = 0;
                }
            }
        }
        $sum_trend['issue'] = $dice['expect'];
        $sum_trend['sum']   = $sumo;
        Db::name('games_jsks_sum_trend')->Insert($sum_trend);
    }

    //记录基本走势 table:games_jsks_basic_trend
    public function recordeBasicTrend($dice){
        $opencode = explode(',',$dice['opencode']);
        $type = "";
        if(($opencode[0]==$opencode[1])&&($opencode[1]==$opencode[2])){
            $type = "三同号";
        }
        else if(($opencode[0]==$opencode[1])||($opencode[1]==$opencode[2])||($opencode[0]==$opencode[2])){
            $type = "二同号";
        }
        else{
            $type = "三不同号";
        }
        $data = [
            'issue' =>$dice['expect'],//期号
            'opencode'=>$dice['opencode'],//开奖号码
            'span'=>max($opencode)-min($opencode),//跨度
            'sum' =>($opencode[0]+$opencode[1]+$opencode[2]),
            'type'=>$type
        ];
        Db::name('games_jsks_basic_trend')->insert($data);
    }

    //记录开奖结果 table:games_jsks_dice_history
    public function recordeDiceHistory($dice){
        $opencode = explode(',', $dice['opencode']);
        $sum = $opencode[0]+$opencode[1]+$opencode[2];
        $codeImg = [
            '0'=>'static/dice/'.$opencode[0].'.png',
            '1'=>'static/dice/'.$opencode[1].'.png',
            '2'=>'static/dice/'.$opencode[2].'.png'
        ];

        $data = [
            'opencode'=>$dice['opencode'],
            'issue' =>$dice['expect'],
            'open_time'=>$dice['opentime'],
            'sum'=>$sum,
            'size'=>$sum>10?'大':'小',
            's_b'=>$sum%2==0?'双':'单',
            'code_img'=>json_encode($codeImg)
        ];
        Db::name('games_jsks_dice_history')->insert($data);
    }

}
 ?>