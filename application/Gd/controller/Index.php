<?php
namespace app\Gd\controller;

use app\Gd\controller\Active;
use app\MyCommon\controller\Gameapi;
use think\Controller;
use think\Db;

//获取游戏开奖号码接口z
/*
 ** 组合公式 C($i,$n);
 ** 最近游戏开出的色子记录 recentInfo();--改表名字
 ** 生成订单     createOrder();--小改
 ** 取消订单     destroyOrder();--公用
 ** 获取历史记录  gameHistory();--大改
 ** 桌面信息      deskInfo();--公用
 ** 近期开奖信息  recentOpen()--改表名字
 ** 下注历史  betHistory();--公用
 ** 创建订单时检测是否有足够的金币 createCheck($allGold, $id)--公用

自用
 **
 */

class Index extends Controller
// class Index

{
    protected $function;
    protected $dice; //开奖详细
    protected $opencode; //开奖号码
    public function __construct()
    {
        $this->active = new Active();
    }

    //测试
    public function makeTest()
    {
        // 随机100期测试效果
        $time = time();
        $expect = 2018118;

        for ($i = 0; $i <= 100; $i++) {
            $opencode = [
                1, 2, 3, 4, 5, 5, 6, 7, 8, 9, 10,
            ];
            shuffle($opencode);
            $time--;

            $opencode = implode(',', $opencode);

            $data = [
                'expect' => $expect--,
                'opencode' => $opencode,
                'opentime' => date("Y-m-d H:i:s", $time),
            ];
            $dice = explode(",", $this->dice['data']['data'][0]['opencode']);

            // 记录和值走势 table:games_jsks_sum_trend
            // $this->active->recordeSumTrend($data);
            // 记录基本走向 table:games_jsks_basic_trend
            // $this->active->recordeBasicTrend($data);
            //记录开奖结果 table:games_jsks_dice_history
            // $this->active->recordeDiceHistory($data);
        }
    }

    //直接获取游戏结果
    public function diceResult()
    {
        // 采集数据
        // echo "caijishuju--gd11x5--\n";
        // $dice = new Gameapi();
        // $this->dice = $dice->getGameData('gdsyxw');

        // $data = $this->dice['data']['data'][0];
        // $opencode = explode(",", $this->dice['data']['data'][0]['opencode']);
        // $this->opencode = $opencode;
        // $dice = $opencode;
        // echo "caijishuju--gd11x5--done--\n";
        // 测试
        $opencode = [
            1,2,3,4,5,6,7,8,9,10
        ];
        shuffle($opencode);

        $dice = $opencode;
        $opencode = implode(',',$opencode);
        $expect = 2018118;
        $data = [
            'expect' => $expect--,
            'opencode' => $opencode,
            'opentime' => date("Y-m-d H:i:s",time()),
        ];
        $this->dice['data']['data'][0] = $data;
        // 测试

        // 记录基本走向 table:games_gd_basic_trend
        $this->active->recordeBasicTrend($data);
        echo "recorde--Gd--BasicTrend--\n";

        // 记录开奖结果 table:games_gd_dice_history
        $this->active->recordeDiceHistory($data);
        echo "recordeDiceHistory--\n";

        //记录定位胆 table:games_gd_dindanwei
        $this->active->recordeDinDanWei($data);
        echo "recorde--Gd--DinDanWei--\n";

        //广东十一选五也是数据结构太简单没有这一项
        // $gameResult = [
        // ];
        // echo "return GameResult--\n";
        // return $gameResult;
    }

    // 游戏结算    //bet就是workman传过来的数据
    public function begin($playerData = [], $diceResult = [])
    {
        $odds = file_get_contents(dirname(dirname(__FILE__)) . "./odds.json");
        $odds = json_decode($odds, true);
        $odds = $odds['odds'];
        foreach ($playerData as $conId => $player) {

            $playerWin = 0; //单个玩家赢的金币
            //求出每一注订单的下注结果
            foreach ($player as $orderNumber => $order) {

                //更新用户剩余金币，以数据库为准
                $playerData[$conId]['remaining'] = $this->active->refreshRemaining($order);

                $win = 0;
                //求出每一个结果
                $type = $order['type'];
                $leiXin = $order['leiXin'];
                $name = $order['name'];
                $number = $order['number'];
                $gold = $order['gold'];
                //按类型结算订单
                switch ($type) {
                    /**
                         * 三码
                         */
                    case "sanma":
                        switch ($name) {
                            /**
                                 * 直选复式
                                 *"tips": "从第一、二、三位中各选1个不同的号码组成一注。",
                                 *"rule": "所选号与开奖号前三位号码相同，且顺序一致，即为中奖。",
                                 *"case": "按序选号：05 06 02，开奖号：05 06 02**，即为中奖。"
                                 *
                                 */
                            case "zx_fushi":
                                if(in_array($this->opencode[0],$number[0])&&in_array($this->opencode[1],$number[1])&&in_array($this->opencode[2],$number[2])){
                                    $win = $gold*$odds[$type][$leiXin][$name];
                                }
                                break;
                            /**
                                 * 直选单式
                                 *"tips": "手动输入1个三位不同的号码组成一注。",
                                 *"rule": "输入号与开奖号前三位号码相同，且顺序一致，即为中奖。",
                                 *"case": "按序选号：05 06 02，开奖号：05 06 02**，即为中奖。"
                                 *
                                 */
                            case "zx_danshi":
                                if($this->opencode[0]==$number[0]&&$this->opencode[1]==$number[1]&&$this->opencode[2]==$number[2]){
                                    $win = $gold*$odds[$type][$leiXin][$name];
                                }
                                break;
                            /**
                                 * 组选复式
                                 *"tips": "从01-11中任选3个不同的号码组成一注。",
                                 *"rule": "不限顺序，开奖号前三位中包含全部所选号，即为中奖。",
                                 *"case": "按序选号：05 06 02，开奖号：06 02 05**，05 06 02**，即为中奖。"
                                 *
                                 */
                            case "zux_fushi":
                                if(in_array($this->opencode[0],$number)&&in_array($this->opencode[1],$number)&&in_array($this->opencode[2],$number)){
                                    $win = $gold*$odds[$type][$leiXin][$name];
                                }
                                break;
                            /**
                                 * 组选单式
                                 *"tips": "手动输入1个三位不同的号码组成一注。",
                                 *"rule": "不限顺序，开奖号前三位中包含全部所选号，即为中奖。",
                                 *"case": "按序选号：05 06 02，开奖号：06 02 05**，05 06 02**，即为中奖。"
                                 *
                                 */
                            case "zux_danshi":
                                if(in_array($this->opencode[0],$number)&&in_array($this->opencode[1],$number)&&in_array($this->opencode[2],$number)){
                                    $win = $gold*$odds[$type][$leiXin][$name];
                                }
                                break;
                        }
                        break;
                    /**
                         * 二码
                         */
                    case "erma":
                        switch ($name) {
                            /**
                                 * 直选复式
                                 *"tips": "从第一、二位中各选1个不同的号码组成一注。",
                                 *"rule": "所选号与开奖号前两位号码相同，且顺序一致，即为中奖。",
                                 *"case": "按序选号：02 09，开奖号：02 09***。"
                                 */
                            case "zx_fushi":
                                if(in_array($this->opencode[0],$number[0])&&in_array($this->opencode[1],$number[1])){
                                    $win = $gold*$odds[$type][$leiXin][$name];
                                }
                                break;
                            /**
                                 * 直选单式
                                 *"tips": "手动输入1个两位不同的号码组成一注。",
                                 *"rule": "输入号与开奖号前两位相同，且顺序一致，即为中奖。",
                                 *"case": "按序输入号05 06，开奖号：05 06***。"
                                 */
                            case "zx_danshi":
                                if($this->opencode[0]==$number[0]&&$this->opencode[1]==$number[1]){
                                    $win = $gold*$odds[$type][$leiXin][$name];
                                }
                                break;
                            /**
                                 * 组选复式
                                 *"tips": "从01-11中任选2个不同的号码组成一注。",
                                 *"rule": "不限顺序，开奖号前两位中包含全部所选号，即为中奖。",
                                 *"case": "按序选号08 11，开奖号：08 11***、11 08***，均为中奖。"
                                 *
                                 *
                                 */
                            case "zux_fushi":
                                if(in_array($this->opencode[0],$number)&&in_array($this->opencode[1],$number)){
                                    $win = $gold*$odds[$type][$leiXin][$name];
                                }
                                break;
                            /**
                                 * 组选单式
                                 *"tips": "手动输入1个两位不同的号码组成一注。",
                                 *"rule": "不限顺序，开奖号前两位中包含全部所选号，即为中奖。",
                                 *"case": "按序输入号：09 02，开奖号：09 02***、02 09 **，均为中奖。"
                                 *
                                 */
                            case "zux_danshi":
                                if($this->opencode[0]==$number&&$this->opencode[1]==$number){
                                    $win = $gold*$odds[$type][$leiXin][$name];
                                }
                                break;
                        }
                        break;
                    /**
                         * 不定胆
                         *
                         */
                    case "budingdan":
                        /**
                         * 前三位
                         *"tips": "从01-11中任选1个号码组成一注。",
                         *"rule": "不限顺序，开奖号前三位任意一位中包含所选号，即为中奖。",
                         *"case": "选号：08，开奖号：**08**、*08****、08****，均为中奖。"
                         */
                        foreach($this->opencode as $key=>$vo){
                            if($key<=3){
                                if(in_array($vo,$number)){
                                    $win = $gold * $odds[$type][$leiXin][$name];
                                }
                            }
                        }
                        break;
                    /**
                         * 定胆位
                         */
                    case "dingweidan":
                        /**
                         * 定胆位
                         *"tips": "从01-11中任选1个号码组成一注。",
                         *"rule": "不限顺序，开奖号中包含全部所选号，即为中奖。",
                         *"case": "选号：05，开奖号：**05*。"
                         */
                        foreach($this->opencode as $vo){
                            if(in_array($vo,$number)){
                                $win += $gold * $odds[$type][$leiXin][$name];
                            }
                        }
                        break;
                    /**
                         * 任选
                         */
                    case "renxuan":
                        switch ($leiXin) {
                            case "renxuanfushi":
                                switch ($name) {
                                    /**
                                         * 复选一
                                         *"tips": "从01-11中任选1个号码组成一注。",
                                         *"rule": "不限顺序，开奖号中包含全部所选号，即为中奖。",
                                         *"case": "选号：05，开奖号：**05*。"
                                         *
                                         */
                                    case "fx_one":
                                        foreach($this->opencode as $vo){
                                            if(in_array($vo,$number)){
                                                $win += $gold * $odds[$type][$leiXin][$name];
                                            }
                                        }                                        
                                        break;
                                    /**
                                         * 复选二
                                         *"tips": "从01-11中任选2个不同的号码组成一注。",
                                         *"rule": "不限顺序，开奖号中包含全部所选号，即为中奖。",
                                         *"case": "选号：0508，开奖号：08*05**。"
                                         *
                                         */
                                    case "fx_two":
                                        $i = 0;//中奖号码的个数
                                        foreach($this->opencode as $vo){
                                            if(in_array($vo,$number)){
                                                $i++;
                                            }
                                        }
                                        //C组合公式
                                        $win = $gold * self::C($i,2);
                                        break;
                                    /**
                                         * 复选三
                                         *"tips": "从01-11中任选3个不同的号码组成一注。",
                                         *"rule": "不限顺序，开奖号中包含全部所选号，即为中奖。",
                                         *"case": "按序选号：05 06 02，开奖号：06*02*05即为中奖。"
                                         *
                                         */
                                    case "fx_three":
                                        $i = 0;//中奖号码的个数
                                        foreach($this->opencode as $vo){
                                            if(in_array($vo,$number)){
                                                $i++;
                                            }
                                        }
                                        //C组合公式
                                        $win = $gold * self::C($i,3);
                                        break;
                                    /**
                                         * 复选四
                                         *"tips": "从01-11中任选4个不同的号码组成一注。",
                                         *"rule": "不限顺序，开奖号中包含全部所选号，即为中奖。",
                                         *"case": "按序选号：05 06 02 07，开奖号：06* 02 07 05，即为中奖。"
                                         *
                                         */
                                    case "fx_four":
                                        $i = 0;//中奖号码的个数
                                        foreach($this->opencode as $vo){
                                            if(in_array($vo,$number)){
                                                $i++;
                                            }
                                        }
                                        //C组合公式
                                        $win = $gold * self::C($i,4);

                                        break;
                                    /**
                                         * 复选五
                                         *"tips": "从01-11中任选5个不同的号码组成一注。",
                                         *"rule": "不限顺序，开奖号中包含全部所选号，即为中奖。",
                                         *"case": "选号：05 08 01 07 02，开奖号：08 02 05 07 01。"
                                         *
                                         */
                                    case "fx_five":
                                        $i = 0;//中奖号码的个数
                                        foreach($this->opencode as $vo){
                                            if(in_array($vo,$number)){
                                                $i++;
                                            }
                                        }
                                        //C组合公式
                                        $win = $gold * self::C($i,5);

                                        break;
                                    /**
                                         * 复选六中五
                                         *"tips": "从01-11中任选6个不同的号码组成一注。",
                                         *"rule": "不限顺序，开奖号中包含6个所选号中的任意5个号码，即为中奖。",
                                         *"case": "选号：03 05 08 01 07 02，开奖号：08 02 05 07 01。"
                                         *
                                         */
                                    case "fx_fiveInSix":
                                        $i = 0;//中奖号码的个数
                                        foreach($this->opencode as $vo){
                                            if(in_array($vo,$number)){
                                                $i++;
                                            }
                                        }
                                        if($i==5){
                                            //C组合公式
                                            $count = count($number);
                                            $win = $gold * self::C($count,6);
                                        }

                                        break;
                                    /**
                                         * 复选七中五
                                         *"tips": "从01-11中任选7个不同的号码组成一注。",
                                         *"rule": "不限顺序，开奖号中包含7个所选号中的任意5个号码，即为中奖。",
                                         *"case": "选号：03 09 05 08 01 07 02，开奖号：08 02 05 07 01。"
                                         *
                                         */
                                    case "fx_fiveInSeven":
                                        $i = 0;//中奖号码的个数
                                        foreach($this->opencode as $vo){
                                            if(in_array($vo,$number)){
                                                $i++;
                                            }
                                        }
                                        if($i==5){
                                            //C组合公式
                                            $count = count($number);
                                            $win = $gold * self::C($count,7);
                                        }                                    
                                        break;
                                    /**
                                         * 复选八中五
                                         *"tips": "从01-11中任选8个不同的号码组成一注。",
                                         *"rule": "不限顺序，开奖号中包含8个所选号中的任意5个号码，即为中奖。",
                                         *"case": "选号：03 09 05 08 01 07 02 06，开奖号：08 02 05 07 01。"
                                         *
                                         */
                                    case "fx_fiveInEight":
                                        $i = 0;//中奖号码的个数
                                        foreach($this->opencode as $vo){
                                            if(in_array($vo,$number)){
                                                $i++;
                                            }
                                        }
                                        if($i==5){
                                            //C组合公式
                                            $count = count($number);
                                            $win = $gold * self::C($count,8);
                                        }                                      
                                        break;
                                }
                                break;
                            //因为数据结构相同所以使用了复选的算法，其实这里面都是单注是不需要使用组合求注数的，中奖了就1注
                            case "renxuandanshi":
                                switch ($name) {
                                    /**
                                         * 单选一
                                         *"tips": "从01-11中手动输入1个号码组成一注。",
                                         *"rule": "不限顺序，开奖号中包含全部所选号，即为中奖。",
                                         *"case": "输入号：05，开奖号：**05**。"
                                         *
                                         */
                                    case "dx_one":
                                        foreach($this->opencode as $vo){
                                            if(in_array($vo,$number)){
                                                $win = $gold * $odds[$type][$leiXin][$name];
                                            }
                                        }                                     
                                        break;
                                    /**
                                         * 单选二
                                         *"tips": "从01-11中手动输入2个号码组成一注。",
                                         *"rule": "不限顺序，开奖号中包含全部所选号，即为中奖。",
                                         *"case": "输入号：05 08，开奖号：08*05**。"
                                         *
                                         */
                                    case "dx_two":
                                        $i = 0;//中奖号码的个数
                                        foreach($this->opencode as $vo){
                                            if(in_array($vo,$number)){
                                                $i++;
                                            }
                                        }
                                        //C组合公式
                                        $win = $gold * self::C($i,2);

                                        break;
                                    /**
                                         * 单选三
                                         *"tips": "从01-11中手动输入3个号码组成一注。",
                                         *"rule": "不限顺序，开奖号中包含全部所选号，即为中奖。",
                                         *"case": "输入号：05 08 01，开奖号：08*05*01。"
                                         *
                                         */
                                    case "dx_three":
                                        $i = 0;//中奖号码的个数
                                        foreach($this->opencode as $vo){
                                            if(in_array($vo,$number)){
                                                $i++;
                                            }
                                        }
                                        //C组合公式
                                        $win = $gold * self::C($i,3);
                                        break;
                                    /**
                                         * 单选四
                                         *"tips": "从01-11中手动输入4个号码组成一注。",
                                         *"rule": "不限顺序，开奖号中包含全部所选号，即为中奖。",
                                         *"case": "输入号：05 08 01 07，开奖号：08*05 07 01。"
                                         *
                                         */
                                    case "dx_four":
                                        $i = 0;//中奖号码的个数
                                        foreach($this->opencode as $vo){
                                            if(in_array($vo,$number)){
                                                $i++;
                                            }
                                        }
                                        //C组合公式
                                        $win = $gold * self::C($i,4);

                                        break;
                                    /**
                                         * 单选五
                                         *"tips": "从01-11中手动输入5个号码组成一注。",
                                         *"rule": "不限顺序，开奖号中包含全部所选号，即为中奖。",
                                         *"case": "输入号：05 08 01 07 02，开奖号：08 02 05 07 01。"
                                         *
                                         */
                                    case "dx_five":
                                        $i = 0;//中奖号码的个数
                                        foreach($this->opencode as $vo){
                                            if(in_array($vo,$number)){
                                                $i++;
                                            }
                                        }
                                        //C组合公式
                                        $win = $gold * self::C($i,5);

                                        break;
                                    /**
                                         * 单选六中五
                                         *"tips": "从01-11中手动输入6个号码组成一注。",
                                         *"rule": "不限顺序，开奖号中包含全部所选号，即为中奖。",
                                         *"case": "输入号：03 05 08 01 07 02，开奖号：08 02 05 07 01。"
                                         *
                                         */
                                    case "dx_fiveInSix":
                                        $i = 0;//中奖号码的个数
                                        foreach($this->opencode as $vo){
                                            if(in_array($vo,$number)){
                                                $i++;
                                            }
                                        }
                                        if($i==5){
                                            //C组合公式
                                            $count = count($number);
                                            $win = $gold * self::C($count,6);
                                        }

                                        break;
                                    /**
                                         * 单选七中五
                                         *"tips": "从01-11中手动输入7个号码组成一注。",
                                         *"rule": "不限顺序，开奖号中包含全部所选号，即为中奖。",
                                         *"case": "输入号：03 09 05 08 01 07 02，开奖号：08 02 05 07 01。"
                                         *
                                         */
                                    case "dx_fiveInSeven":
                                        $i = 0;//中奖号码的个数
                                        foreach($this->opencode as $vo){
                                            if(in_array($vo,$number)){
                                                $i++;
                                            }
                                        }
                                        if($i==5){
                                            //C组合公式
                                            $count = count($number);
                                            $win = $gold * self::C($count,7);
                                        }                                    
                                        break;
                                    /**
                                         * 单选八中五
                                         *"tips": "从01-11中手动输入8个号码组成一注。",
                                         *"rule": "不限顺序，开奖号中包含全部所选号，即为中奖。",
                                         *"case": "输入号：03 09 05 08 01 07 02 06，开奖号：08 02 05 07 01。"
                                         *
                                         */
                                    case "dx_fiveInEight":
                                        $i = 0;//中奖号码的个数
                                        foreach($this->opencode as $vo){
                                            if(in_array($vo,$number)){
                                                $i++;
                                            }
                                        }
                                        if($i==5){
                                            //C组合公式
                                            $count = count($number);
                                            $win = $gold * self::C($count,8);
                                        }                                      
                                        break;
                                }
                                break;
                        }
                        break;
                }
                $dice = $this->dice['data']['data'][0];
                //结算订单 table:games_bet_history:update
                $this->active->recordeBetHistory($orderNumber, $dice, $win);
                $playerWin += $win;
                $userId = $playerData[$conId][$orderNumber]['id'];
            }
            //清空数组只返回剩余金币
            $remaining = $playerData[$conId]['remaining'] + $playerWin;

            //更新用户金币数据： table:user_user : update
            $this->active->refleshUser($userId, $remaining);
            //清空数组
            $playerData[$conId] = [];
            //保留remaining
            $playerData[$conId]['remaining'] = $remaining;
        }
        // echo json_encode($playerData);exit;
        return $playerData;
    }

    /**
     * 组合公式，用于计算任选复选中的中奖注数
     */
    public function C($n,$r){
        if($n<$r){
            return 0;
        }
        if($n == $r){
            return 1;
        }
        else{
            $nJ = 1;
            $rJ = 1;
            $chaJ = 1;
            $cha = $n - $r;
            //n!
            do{
                $nJ *= $n;
                --$n;
            }while($n);
            do{
                $rJ *= $r;
                --$r;
            }while($r);
            do{
                $chaJ *= $cha;
                --$cha;
            }while($cha);
            return $nJ/($rJ*($chaJ));
        }
    }
    /*
     **请求个人的购奖记录
     */
    public function betHistory()
    {
        $betHistory = Db::name('games_bet_history')->field('id', true)->order('id desc')->limit(0, 30)->select();
        return $betHistory;
    }

    //最近游戏记录 table::'games_jsks_dice_history'
    public function recentInfo()
    {
        $data = Db::name('games_gd_dice_history')->field('id', true)->order('id desc')->limit(19)->select();
        foreach ($data as $key => $vo) {
            $data[$key]['open_code'] = json_decode($data[$key]['open_code'], true);
        }
        // echo json_encode($data);exit;
        return $data;
    }
    /*
     * localhost/bcweb/Gd/index/gameHistory?type=dingDanWei&size=30&yeshu=01
     *获取开奖结果以及历史走势并保存在worker.php的 $this->gameHistory 中;
     */
    public function gameHistory($type = "", $size = "", $yeshu = "")
    {
        //基本走势
        //定胆位
        //冠军和
        $findDinDan = [
            "30" => Db::name("games_gd_dindanwei")->field("id,open_code,open_time", true)->order('id desc')->limit(0, 30)->select(),
            "50" => Db::name("games_gd_dindanwei")->field("id,open_code,open_time", true)->order('id desc')->limit(0, 50)->select(),
            "100" => Db::name("games_gd_dindanwei")->field("id,open_code,open_time", true)->order('id desc')->limit(0, 100)->select(),
        ];
        $reverse = []; //倒转数组
        $issue = []; //期号数组
        foreach ($findDinDan as $key => $vo) {
            foreach ($vo as $key2 => $vo2) {
                foreach ($vo2 as $key3 => $vo3) {
                    if ($key3 != "expect") { //期号不需要倒转
                        //解码同时进行倒转
                        $reverse[$key][$key3][$key2] = json_decode($vo3, true);
                    } else {
                        $issue[$key][] = $vo3;
                    }
                }
            }
        }
        //平均遗漏，最大遗漏找的是历史所有数据
        $findAll = Db::name("games_gd_dindanwei")->field('id,expect,open_code,open_time', true)->select();
        $reverseAll = []; //所有数据的倒转数组
        $newReverseAll = [];
        /**
         * findAll = [
         *  "0"=>[
         *      "ball_1"=>"{"num1"=>1,"num2"=>2}"
         *  ]
         * ]
         * reverseAll = ["ball_1"=>[
         *  "0"=>[
         *      "num1"=>1,
         *      ]
         * ]]
         * newReverseAll = [
         *      "ball_1"=>[
         *          "num1"=>[
         *              "0"=>1,
         *          ]
         *      ]
         * ]
         */
        foreach ($findAll as $key => $vo) {
            foreach ($vo as $key2 => $vo2) {

                $reverseAll[$key2][$key] = json_decode($vo2, true);
                //装成5*All的数组形式
                foreach ($reverseAll[$key2][$key] as $key3 => $vo3) {
                    $newReverseAll[$key2][$key3][$key] = $vo3;
                }
            }
        }
        $zuidayilou = []; //最大遗漏
        $pinjunyilou = []; //平均遗漏
        foreach ($newReverseAll as $key => $vo) {
            foreach ($vo as $key2 => $vo2) {
                $zuidayilou[$key][$key2] = max($newReverseAll[$key][$key2]);
                //寻找该个数字出现的次数
                $count = array_count_values($newReverseAll[$key][$key2]);
                $cishu = isset($count[0]) ? $count[0] : 0;
                //获取平均遗漏
                $pinjunyilou[$key][$key2] = floor(count($newReverseAll[$key][$key2]) / ($cishu + 1));
            }
        }
        //3*10页的数据  往每一页分析数据里添加最大遗漏与平均遗漏组成总的分析表
        $dinDanWei = [
            "30" => [
                "ball_1" => [
                    "issue" => $issue['30'],
                    "table" => $reverse['30']['ball_1'],
                    "analyse" => self::dingDanWeiAnalyse($reverse['30']['ball_1']),
                ],
                "ball_2" => [
                    "issue" => $issue['30'],
                    "table" => $reverse['30']['ball_2'],
                    "analyse" => self::dingDanWeiAnalyse($reverse['30']['ball_2']),
                ],
                "ball_3" => [
                    "issue" => $issue['30'],
                    "table" => $reverse['30']['ball_3'],
                    "analyse" => self::dingDanWeiAnalyse($reverse['30']['ball_3']),
                ],
                "ball_4" => [
                    "issue" => $issue['30'],
                    "table" => $reverse['30']['ball_4'],
                    "analyse" => self::dingDanWeiAnalyse($reverse['30']['ball_4']),
                ],
                "ball_5" => [
                    "issue" => $issue['30'],
                    "table" => $reverse['30']['ball_5'],
                    "analyse" => self::dingDanWeiAnalyse($reverse['30']['ball_5']),
                ],
            ],
            "50" => [
                "ball_1" => [
                    "issue" => $issue['50'],
                    "table" => $reverse['50']['ball_1'],
                    "analyse" => self::dingDanWeiAnalyse($reverse['50']['ball_1']),
                ],
                "ball_2" => [
                    "issue" => $issue['50'],
                    "table" => $reverse['50']['ball_2'],
                    "analyse" => self::dingDanWeiAnalyse($reverse['50']['ball_2']),
                ],
                "ball_3" => [
                    "issue" => $issue['50'],
                    "table" => $reverse['50']['ball_3'],
                    "analyse" => self::dingDanWeiAnalyse($reverse['50']['ball_3']),
                ],
                "ball_4" => [
                    "issue" => $issue['50'],
                    "table" => $reverse['50']['ball_4'],
                    "analyse" => self::dingDanWeiAnalyse($reverse['50']['ball_4']),
                ],
                "ball_5" => [
                    "issue" => $issue['50'],
                    "table" => $reverse['50']['ball_5'],
                    "analyse" => self::dingDanWeiAnalyse($reverse['50']['ball_5']),
                ],
            ],
            "100" => [
                "ball_1" => [
                    "issue" => $issue['100'],
                    "table" => $reverse['100']['ball_1'],
                    "analyse" => self::dingDanWeiAnalyse($reverse['100']['ball_1']),
                ],
                "ball_2" => [
                    "issue" => $issue['100'],
                    "table" => $reverse['100']['ball_2'],
                    "analyse" => self::dingDanWeiAnalyse($reverse['100']['ball_2']),
                ],
                "ball_3" => [
                    "issue" => $issue['100'],
                    "table" => $reverse['100']['ball_3'],
                    "analyse" => self::dingDanWeiAnalyse($reverse['100']['ball_3']),
                ],
                "ball_4" => [
                    "issue" => $issue['100'],
                    "table" => $reverse['100']['ball_4'],
                    "analyse" => self::dingDanWeiAnalyse($reverse['100']['ball_4']),
                ],
                "ball_5" => [
                    "issue" => $issue['100'],
                    "table" => $reverse['100']['ball_5'],
                    "analyse" => self::dingDanWeiAnalyse($reverse['100']['ball_5']),
                ],
            ],
        ];
        //把最大遗漏与平均遗漏装到分析表去
        foreach ($dinDanWei as $key => $vo) {
            foreach ($vo as $keyI => $voI) {
                $dinDanWei[$key][$keyI]['analyse']['zuidayilou'] = $zuidayilou[$keyI];
                $dinDanWei[$key][$keyI]['analyse']['pinjunyilou'] = $pinjunyilou[$keyI];
            }
        }

        //基本走势
        $basic = [
            "30" => Db::name("games_gd_basic_trend")->order('id desc')->field("id", true)->limit(30)->select(),
            "50" => Db::name("games_gd_basic_trend")->order('id desc')->field("id", true)->limit(50)->select(),
            "100" => Db::name("games_gd_basic_trend")->order('id desc')->field("id", true)->limit(100)->select(),
        ];

        //转码opencode
        foreach ($basic as $key => $vo) {
            foreach ($vo as $key2 => $vo2) {
                $basic[$key][$key2]['open_code'] = json_decode($vo2['open_code'], true);
                $basic[$key][$key2]['sum'] = json_decode($vo2['sum'], true);
            }
        }

        $result = [
            "dingDanWei_Gd" => $dinDanWei,
            "basic_Gd" => $basic,
        ];

        /**
         * 测试用
         */
        if ($type != "") {
            if ($yeshu != "") {
                echo json_encode($result[$type][$size][$yeshu]);exit; //定胆位
            }
            echo json_encode($result[$type][$size]);exit;
        }
        //测试用
        // echo json_encode($result);exit;
        return $result;
    }
    /**
     * 定胆位分析
     * 分析十个数字在本次查询中的数据
     * "chuxiancishu":出现次数//本次
     * "zuidaliangchu":最大连出//本次
     * "pingjunyilou":平均遗漏//历史
     * "zuidayilou":最大遗漏//历史
     */
    public function dingDanWeiAnalyse($array)
    {
        $reverse = [];
        /**
         * 数组倒转
         * 原始数组：
         * array = [
         *      "0"=>["num"=>1,"num2"=>2]
         *
         * ]
         * 倒转数组：
         * reverse = [
         *      "num1"=>["0"=>1]
         * ]
         */

        foreach ($array as $key => $vo) {
            foreach ($vo as $keyI => $voI) {
                $reverse[$keyI][$key] = $voI;
            }
        }
        //出现次数
        $cishu = [
            "num1" => count(array_keys($reverse['num1'], "0")),
            "num2" => count(array_keys($reverse['num2'], "0")),
            "num3" => count(array_keys($reverse['num3'], "0")),
            "num4" => count(array_keys($reverse['num4'], "0")),
            "num5" => count(array_keys($reverse['num5'], "0")),
            "num6" => count(array_keys($reverse['num6'], "0")),
            "num7" => count(array_keys($reverse['num7'], "0")),
            "num8" => count(array_keys($reverse['num8'], "0")),
            "num9" => count(array_keys($reverse['num9'], "0")),
            "num10" => count(array_keys($reverse['num10'], "0")),
            "num11" => count(array_keys($reverse['num11'], "0")),
        ];
        //最大连出
        $dalianOut = [
            'num1' => [0],
            'num2' => [0],
            'num3' => [0],
            'num4' => [0],
            'num5' => [0],
            'num6' => [0],
            'num7' => [0],
            'num8' => [0],
            'num9' => [0],
            'num10' => [0],
            'num11' => [0],
        ];

        foreach ($reverse as $key => $vo) {
            //相同则增加一个和值连出数并且比上一个大1
            foreach ($vo as $keyI => $voI) {
                if ($voI == 0) {
                    $dalianOut[$key][count($dalianOut[$key])] = $dalianOut[$key][count($dalianOut[$key]) - 1] + 1;
                } else {
                    $dalianOut[$key][count($dalianOut[$key])] = 0;
                }
            }
        }
        //取最大数
        foreach ($dalianOut as $key => $vo) {
            $dalianOut[$key] = max($dalianOut[$key]);
        }
        $result = [];
        for ($i = 1; $i <= 11; $i++) {
            $index = "num" . $i;
            $result['zuidaliangchu'][$index] = $dalianOut[$index];
            $result["chuxiancishu"][$index] = $cishu[$index];
        }
        return $result;
    }
    /**
     * 生成下注订单
     * @$order： 前端传来的订单信息
     * @createTime:订单创建时间
     * $orderNumber:订单号
     * $fileOdds:赔率表
     *
     */
    public function createOrder($order, $createTime, $orderNumber, $fileOdds)
    {
        $type = $order['type']; //大分类
        $leiXin = $order["leiXin"]; //类型
        $name = $order["name"];
        $number = $order["number"]; //押注号码的数组
        /**
         * 构造赔率
         */
        $fileOdds = $fileOdds['odds'];
        switch ($type) {

        }
        $odds = $fileOdds[$type][$leiXin][$name];
        $data = [
            'user_id' => $order['id'], //用户id
            'issue' => $order['issue'], //押注期号
            'gold' => $order['zhuShu'] . "注  " . $order['gold'] . "金币", //押注金额 注数+金额
            'order_number' => $orderNumber, //押注单号
            'name' => $type . "_" . $name, //押注名称
            'return_point' => 0, //返点
            'create_time' => $createTime,
            'number' => json_encode($number), //押注数据
            'odds' => $odds, //押注赔率
            "avatar" => $order['avatar'], //头像
            "key" => $order['key'], //关键字
            "gameName" => $order['gameName'], //游戏名字
        ];

        $bet = Db::name('games_bet_history')->insert($data);
        if ($bet) {
            $result = [
                'create' => true,
            ];
        } else {
            $result = [
                'create' => false,
            ];

        }
        echo "------------create success--------------\n";
        return $result;
    }

    /**
     * 检测金币状态
     */
    public function createCheck($allGold, $id)
    {
        $findGold = Db::name("user_user")->where(['id' => $id])->value('gold');
        if ($findGold >= $allGold) {
            //金币足够扣除，返回剩余金币
            $result = [
                "status" => true,
                "remaining" => $findGold - $allGold,
            ];
        } else {
            //金币不够，不扣除返回剩下金币
            $result = [
                "status" => false,
                "remaining" => $findGold,
            ];
        }
        return $result;
    }

    /**
     * 江苏快三近期开奖记录
     * ajax 请求近期开奖
     */
    public function recentOpen()
    {
        $recentOpen = Db::name('games_gd_dice_history')->field('expect,open_time,open_code')->limit(0, 30)->order('id desc')->select();
        foreach ($recentOpen as $key => $vo) {
            $recentOpen[$key]['open_code'] = json_decode($recentOpen[$key]['open_code']);
        }
        return $recentOpen;
    }
    //撤销订单
    public function destroyOrder($orderNumber)
    {
        $data = Db::name('games_bet_history')->where(['order_number' => $orderNumber])->field('id,user_id,status,gold')->find();

        //订单还没有被结算可以撤销
        if ($data && $data['status'] == 0) {
            try {
                $Dgold = substr($data['gold'], strpos($data['gold'], ' ') + 1); //把金币数分割出来
                $gold = str_replace("金币", '', $Dgold); //分割金币数
            } catch (\Exception $e) {
                throw $e;
            }

            //更改订单状态为3已撤销
            Db::name('games_bet_history')->where(['id' => $data['id']])->update(['status' => 3]);
            $result = [
                'cancel' => true,
                'cancelGold' => $gold,
            ]; //撤销成功
            echo "---destroy success---\n";
        } else {
            $result = ['cancel' => false]; //订单 已经删除或者已经结算
            echo "---destroy faile---\n";
        }
        return $result;
    }

    //返回桌面信息 table::
    public function deskInfo()
    {
        $data = file_get_contents(dirname(dirname(__FILE__)) . "/send.json");
        return json_decode($data, true);
    }

    //数据结构
    public function dataType()
    {
        $playerData = [
            '2' => [
                '0' => [
                    "id" => 11, //用户的id
                    'type' => "sanma", //三码
                    'leiXin' => "qiansanzhixuan", //直选
                    "name" => "zx_fushi", //复式
                    "number" => [ //下注号码跟前端商量
                        "0" => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                        "1" => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                        "2" => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                    ],
                    "gold" => 10,
                    "allGold" => 50, //下注总金额
                    "total" => 5, //总注数
                    "remaining" => 1000, //剩余金币u
                ],
                '1' => [
                    "id" => 11, //用户的id
                    'type' => "sanma", //三码
                    'leiXin' => "qiansanzhixuan", //直选
                    "name" => "zx_danshi", //单式
                    "number" => ['0' => '1', '1' => '2', '2' => '3'],
                    "gold" => 10,
                    "allGold" => 50, //下注总金额
                    "total" => 5, //总注数
                    "remaining" => 1000, //剩余金币user_user表查询
                ],
                '30' => [
                    "id" => 11, //用户的id
                    'type' => "sanma", //三码
                    'leiXin' => "qiansanzuxuan", //组选
                    "name" => "zux_fushi", //复式
                    "number" => [ //下注号码跟前端商量
                        "0" => 11, "1" => 1, "2" => 2, "3" => 3, "4" => 4, "5" => 5, "6" => 6, "7" => 7, "8" => 8, "9" => 9, "10" => 10,
                    ],
                    "gold" => 10,
                    "allGold" => 50, //下注总金额
                    "total" => 5, //总注数
                    "remaining" => 1000, //剩余金币u
                ],
                '31' => [
                    "id" => 11, //用户的id
                    'type' => "sanma", //三码
                    'leiXin' => "qiansanzuxuan", //组选
                    "name" => "zux_danshi", //单式
                    "number" => ["0" => 11, "1" => 1, "2" => 2],
                    "gold" => 10,
                    "allGold" => 50, //下注总金额
                    "total" => 5, //总注数
                    "remaining" => 1000, //剩余金币user_user表查询
                ],
                '2' => [
                    "id" => 11, //用户的id
                    'type' => "erma", //二码
                    'leiXin' => "qianerzhixuan", //前二直选
                    "name" => "zx_fushi", //复式
                    "number" => [
                        "0" => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                        "1" => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                    ],
                    "gold" => 10,
                    "allGold" => 50, //下注总金额
                    "total" => 5, //总注数
                    "remaining" => 1000, //剩余金币user_user表查询
                ],
                '3' => [
                    "id" => 11, //用户的id
                    'type' => "erma", //二码
                    'leiXin' => "qianerzhixuan", //前二直选
                    "name" => "zx_danshi", //单式
                    "number" => [1, 2],
                    "gold" => 10,
                    "allGold" => 50, //下注总金额
                    "total" => 5, //总注数
                    "remaining" => 1000, //剩余金币user_user表查询
                ],
                '4' => [
                    "id" => 11, //用户的id
                    'type' => "erma", //二码
                    'leiXin' => "qianerzuxuan", //前二组选
                    "name" => "zux_fushi", //复式
                    "number" => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                    "gold" => 10,
                    "allGold" => 50, //下注总金额
                    "total" => 5, //总注数
                    "remaining" => 1000, //剩余金币user_user表查询
                ],
                '5' => [
                    "id" => 11, //用户的id
                    'type' => "erma", //二码
                    'leiXin' => "qianerzuxuan", //前二组选
                    "name" => "zux_danshi", //组选单式
                    "number" => [1, 2],
                    "gold" => 10,
                    "allGold" => 50, //下注总金额
                    "total" => 5, //总注数
                    "remaining" => 1000, //剩余金币user_user表查询
                ],
                '6' => [
                    "id" => 11, //用户的id
                    'type' => "budingdan", //不定胆
                    'leiXin' => "budingdan", //不定胆
                    "name" => "qiansanwei", //前三位
                    "number" => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                    "gold" => 10,
                    "allGold" => 50, //下注总金额
                    "total" => 5, //总注数
                    "remaining" => 1000, //剩余金币user_user表查询
                ],
                '7' => [
                    "id" => 11, //用户的id
                    'type' => "dingweidan", //定胆位
                    'leiXin' => "dingweidan", //定胆位
                    "name" => "dingweidan", //定胆位
                    "number" => [
                        "0" => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                        "1" => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                        "2" => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                        "3" => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                        "4" => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                    ],
                    "gold" => 10,
                    "allGold" => 50, //下注总金额
                    "total" => 5, //总注数
                    "remaining" => 1000, //剩余金币user_user表查询
                ],
                '8' => [
                    "id" => 11, //用户的id
                    'type' => "renxuan", //任选
                    'leiXin' => "renxuanfushi", //复式
                    "name" => "fx_one", //一中一
                    "number" => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                    "gold" => 10,
                    "allGold" => 50, //下注总金额
                    "total" => 5, //总注数
                    "remaining" => 1000, //剩余金币user_user表查询
                ],
                '9' => [
                    "id" => 11, //用户的id
                    'type' => "renxuan", //任选
                    'leiXin' => "renxuanfushi", //复式
                    "name" => "fx_two", //二中二
                    "number" => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                    "gold" => 10,
                    "allGold" => 50, //下注总金额
                    "total" => 5, //总注数
                    "remaining" => 1000, //剩余金币user_user表查询
                ],
                '10' => [
                    "id" => 11, //用户的id
                    'type' => "renxuan", //任选
                    'leiXin' => "renxuanfushi", //复式
                    "name" => "fx_three", //三中三
                    "number" => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                    "gold" => 10,
                    "allGold" => 50, //下注总金额
                    "total" => 5, //总注数
                    "remaining" => 1000, //剩余金币user_user表查询
                ],
                '12' => [
                    "id" => 11, //用户的id
                    'type' => "renxuan", //任选
                    'leiXin' => "renxuanfushi", //复式
                    "name" => "fx_four", //四中四
                    "number" => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                    "gold" => 10,
                    "allGold" => 50, //下注总金额
                    "total" => 5, //总注数
                    "remaining" => 1000, //剩余金币user_user表查询
                ],
                '13' => [
                    "id" => 11, //用户的id
                    'type' => "renxuan", //任选
                    'leiXin' => "renxuanfushi", //复式
                    "name" => "fx_five", //五中五
                    "number" => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                    "gold" => 10,
                    "allGold" => 50, //下注总金额
                    "total" => 5, //总注数
                    "remaining" => 1000, //剩余金币user_user表查询
                ],
                '14' => [
                    "id" => 11, //用户的id
                    'type' => "renxuan", //任选
                    'leiXin' => "renxuanfushi", //复式
                    "name" => "fx_fiveInSix", //六中五
                    "number" => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                    "gold" => 10,
                    "allGold" => 50, //下注总金额
                    "total" => 5, //总注数
                    "remaining" => 1000, //剩余金币user_user表查询
                ],
                '15' => [
                    "id" => 11, //用户的id
                    'type' => "renxuan", //任选
                    'leiXin' => "renxuanfushi", //复式
                    "name" => "fx_fiveInSeven", //七中五
                    "number" => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                    "gold" => 10,
                    "allGold" => 50, //下注总金额
                    "total" => 5, //总注数
                    "remaining" => 1000, //剩余金币user_user表查询
                ],
                '16' => [
                    "id" => 11, //用户的id
                    'type' => "renxuan", //任选
                    'leiXin' => "renxuanfushi", //复式
                    "name" => "fx_fiveInEight", //八中五
                    "number" => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                    "gold" => 10,
                    "allGold" => 50, //下注总金额
                    "total" => 5, //总注数
                    "remaining" => 1000, //剩余金币user_user表查询
                ],
                '17' => [
                    "id" => 11, //用户的id
                    'type' => "renxuan", //任选
                    'leiXin' => "renxuandanshi", //单式
                    "name" => "dx_one", //一中一
                    "number" => [1],
                    "gold" => 10,
                    "allGold" => 50, //下注总金额
                    "total" => 5, //总注数
                    "remaining" => 1000, //剩余金币user_user表查询
                ],
                '18' => [
                    "id" => 11, //用户的id
                    'type' => "renxuan", //任选
                    'leiXin' => "renxuandanshi", //单式
                    "name" => "dx_two", //二中二
                    "number" => [1, 2],
                    "gold" => 10,
                    "allGold" => 50, //下注总金额
                    "total" => 5, //总注数
                    "remaining" => 1000, //剩余金币user_user表查询
                ],
                '19' => [
                    "id" => 11, //用户的id
                    'type' => "renxuan", //任选
                    'leiXin' => "renxuandanshi", //单式
                    "name" => "dx_three", //三中三
                    "number" => [1, 2, 3],
                    "gold" => 10,
                    "allGold" => 50, //下注总金额
                    "total" => 5, //总注数
                    "remaining" => 1000, //剩余金币user_user表查询
                ],
                '20' => [
                    "id" => 11, //用户的id
                    'type' => "renxuan", //任选
                    'leiXin' => "renxuandanshi", //单式
                    "name" => "dx_four", //四中四
                    "number" => [1, 2, 3, 4],
                    "gold" => 10,
                    "allGold" => 50, //下注总金额
                    "total" => 5, //总注数
                    "remaining" => 1000, //剩余金币user_user表查询
                ],
                '21' => [
                    "id" => 11, //用户的id
                    'type' => "renxuan", //任选
                    'leiXin' => "renxuandanshi", //单式
                    "name" => "dx_five", //五中五
                    "number" => [1, 2, 3, 4, 5],
                    "gold" => 10,
                    "allGold" => 50, //下注总金额
                    "total" => 5, //总注数
                    "remaining" => 1000, //剩余金币user_user表查询
                ],
                '22' => [
                    "id" => 11, //用户的id
                    'type' => "renxuan", //任选
                    'leiXin' => "renxuandanshi", //单式
                    "name" => "dx_fiveInSix", //六中五
                    "number" => [1, 2, 3, 4, 5, 6],
                    "gold" => 10,
                    "allGold" => 50, //下注总金额
                    "total" => 5, //总注数
                    "remaining" => 1000, //剩余金币user_user表查询
                ],
                '23' => [
                    "id" => 11, //用户的id
                    'type' => "renxuan", //任选
                    'leiXin' => "renxuandanshi", //单式
                    "name" => "dx_fiveInSeven", //七中五
                    "number" => [1, 2, 3, 4, 5, 6, 7],
                    "gold" => 10,
                    "allGold" => 50, //下注总金额
                    "total" => 5, //总注数
                    "remaining" => 1000, //剩余金币user_user表查询
                ],
                '24' => [
                    "id" => 11, //用户的id
                    'type' => "renxuan", //任选
                    'leiXin' => "renxuandanshi", //单式
                    "name" => "dx_fiveInEight", //八中五
                    "number" => [1, 2, 3, 4, 5, 6, 7, 8],
                    "gold" => 10,
                    "allGold" => 50, //下注总金额
                    "total" => 5, //总注数
                    "remaining" => 1000, //剩余金币user_user表查询
                ],
            ],
        ];

        // $data = [];
        $result = [];
        $i = -10;
        do {
            $diceResult = self::diceResult();
            $result[] = self::begin($playerData, $diceResult);
            $i++;
        } while ($i);
        dump($result);
    }

}
