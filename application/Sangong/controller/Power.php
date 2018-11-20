<?php 
namespace app\Sangong\controller;
use think\Controller;


    /**特殊牌型  每发一张牌就卸掉一张牌
     * 三公:  [10,11,12, 23,24,25, 36,37,38, 49,50,51 ];
     * 所有玩法可在对应的函数里找规则牌型
     * 注意事项:牌型可以进行扩充与调整以达到发牌多样性以及优化发牌速度
     */

class Power extends Controller{
    // class Power{
    //平台玩家发牌

    public $deck = array();//总的牌组
    public $p = array();   // 公牌
    public $notP = array();//非公牌
    public function __construct(){
        for($i=1;$i<=13;$i++){
            $this->deck[$i] = ['color'=>'red','size'=>$i];//红心
            $this->deck[$i+13] = ['color'=>'spade','size' =>$i];//黑桃
            $this->deck[$i+26] = ['color' =>'diamond','size'=>$i];//方砖
            $this->deck[$i+39] = ['color'=>'wintersweet','size' =>$i];//梅花
        }
        //整合数组 令数组从零开始
        $this->p = [
            11,12,13,
            24,25,26,
            37,38,39,
            50,51,52,
        ];
        $this->notP = [
            1,2,3,4,5,6,7,8,9,10,
            14,15,16,17,18,19,20,21,22,23,
            27,28,29,30,31,32,33,34,35,36,
            40,41,42,43,44,45,46,47,48,49,
        ];
    }
    // public function bocaiDeal($power,$goldPool){
    public function bocaiDeal(){
        // $power = json_decode($power,true);
        
        // $goldPool = [
        //         "player1" => [
        //             "toDeal" => 0,
        //             "sanGong" => 0,
        //             "win" => 100,
        //             "peace" => 0,
        //             "lose" => 10,
        //         ],
        //         "player2" => [
        //             "toDeal" => 0,
        //             "sanGong" => 0,
        //             "win" => 10,
        //             "peace" => 0,
        //             "lose" => 100,
    
        //         ],
        //         "player3" => [
        //             "toDeal" => 0,
        //             "sanGong" => 0,
        //             "win" => 0,
        //             "peace" => 0,
        //             "lose" => 0,
        //         ],
        // ];//金币池
/**
 * 庄家要求
 * 测试1000次，没问题
 */

        // $power = [
        //     "player"=>"banker",
        // ];
        
/**
 * 闲家要求     ：情景假设
 * 情景1: 庄家:1p*   闲家1 : 三公 闲家2: 赢    闲家3:三公//done
 * 情景2: 庄家:1p*   闲家1 : 三公 闲家2: 输    闲家3:三公//done
 * 情景3: 庄家:1p*   闲家1 : 三公 闲家2: 赢    闲家3:和局//done
 * 情景4: 庄家:1p*   闲家1 : 三公 闲家2: 对牌  闲家3:三公//done
 * 情景5: 庄家:1p*   闲家1 : 赢   闲家2: 和局  闲家3:三公//done
 * 情景6: 庄家:1p*   闲家1 : 和 闲家2: 和  闲家3:和//有点问题
 * 情景7  庄家:1p*   闲家1 : 空 闲家2： 空 闲家3： 空 //done
 * 测试：各个测1000次，结果有些少出入
 */
        // $power = [
        //     "player"=>"player",
        //     "bet"=>[
        //         '0'=> ["player" =>"player2","status" =>""],
        //         '1'=> ["player" =>"player1","status" =>""],
        //         '2'=> ["player" =>"player3","status" =>""],
        //     ],
        //     "gold "=>10,
        // ];

        $card = array();
        //平台玩家是庄家
        if($power["player"] == "banker"){
            
            //根据金币池里的金币数量开牌，目前只根据输赢两个池开牌，默认其它金币池全收，只开输赢
            $card = self::bankerPower($goldPool);
        }
        else{//平台玩家是闲家
            $card = self::playerPower($power);
        }
        return $card;
    }

    /**   第一版完成
     * 1：庄家1个p，两个非p的随机牌
     *    闲家赢：2个非对子非同花p，一个2到9的随机牌
     *    闲家输：3个非对子非同花非顺子并且非p的随机牌
     * 2：上面设定决定闲家不开三公，不开和牌
     *    闲家开非 对子 顺子 同花顺 三条 同花
     */

    public function bankerPower($goldPool){

         $countDeck = count($this->deck);//总牌组长度;
         $countP = count($this->p);//公牌组长度;
         $countNotP = count($this->notP);//非公牌长度;
         $banker = [];
         $player1 = [];
         $player2 = [];
         $player3 = [];

        /**
         * 产生庄家的牌：1个p，两个非p的随机牌
         */
        //生成公牌
         $bankerPSub = rand(0,$countP-1);
         $bankerP = $this->p[$bankerPSub];
         
         //卸掉已开的公牌,并且重新整理牌组
         unset($this->p[$bankerPSub]);
         $this->p = array_values($this->p);

         //生成非公牌
         $bankerNotP1Sub = rand(0,$countNotP-1);
         $bankerNotP1 = $this->notP[$bankerNotP1Sub];
         //卸掉已开的非公牌,并且重新整理牌组
         unset($this->notP[$bankerNotP1Sub]);
         $this->notP = array_values($this->notP);

         $countNotP = count($this->notP);
         //生成非公牌2
         $bankerNotP2Sub = rand(0,$countNotP-1);
         $bankerNotP2 = $this->notP[$bankerNotP2Sub];
         //卸掉已开的非公牌,并且重新整理牌组
         unset($this->notP[$bankerNotP2Sub]);
         $this->notP = array_values($this->notP);

         $banker = [
                $this->deck[$bankerP],
                $this->deck[$bankerNotP1],
                $this->deck[$bankerNotP2],
          ]; 


         //根据规则产生对应闲家的牌
         foreach($goldPool as $player => $pool){
            
            $PK = $pool['win'] - $pool['lose'];
            //吃掉金币池比较大的那一个
            switch($PK){   

               //    $$player: 对应 $player1 $player2 $player3
               case $PK >= 0:
                   $$player = self::playerLose();
               break;

               case $PK < 0:
                   $$player = self::playerWin();
               break;

               default:
               break;
            }
         }
         $card = [
             "banker" => $banker,
             "player1" => $player1,
             "player2" => $player2,
             "player3" => $player3,
         ];
         return $card;
    }
    
    /**  
     * 1:三公,对牌
     * 2：庄家1个p，两个非p的随机牌
     *    闲家和：1个p，一个随机牌，庄家点数-随机牌的值 
     *    闲家赢：2个非对子非同花p，一个2到9的随机牌
     *    闲家输：3个非对子非同花非顺子并且非p的随机牌
     * 3：上面设定决定闲家不开三公，不开和牌
     *    闲家开非 对子 顺子 同花顺 三条 同花,非公牌组合里找出对子 顺子 同花顺 三条 同花
     */
    public function playerPower($power){
        /**
         * 产生庄家的牌：1个p，两个非p的随机牌
         */
        //生成公牌
        $countP = count($this->p);
        $countNotP = count($this->notP);

        $bankerPSub = rand(0,$countP-1);
        $bankerP = $this->p[$bankerPSub];
        
        //卸掉已开的公牌,并且重新整理牌组
        unset($this->p[$bankerPSub]);
        $this->p = array_values($this->p);

        //生成非公牌
        $bankerNotP1Sub = rand(0,$countNotP-1);
        $bankerNotP1 = $this->notP[$bankerNotP1Sub];
        //卸掉已开的非公牌,并且重新整理牌组
        unset($this->notP[$bankerNotP1Sub]);
        $this->notP = array_values($this->notP);

        $countNotP = count($this->notP);
        //生成非公牌2
        $bankerNotP2Sub = rand(0,$countNotP-1);
        $bankerNotP2 = $this->notP[$bankerNotP2Sub];
        //卸掉已开的非公牌,并且重新整理牌组
        unset($this->notP[$bankerNotP2Sub]);
        $this->notP = array_values($this->notP);

        $banker = [
               $this->deck[$bankerP],
               $this->deck[$bankerNotP1],
               $this->deck[$bankerNotP2],
         ]; 

        $abc = [
            'player1'=>'',
            'player2'=>'',
            'player3'=>'',
        ];

        $deck = [
            'player1'=>[],
            'player2'=>[],
            'player3'=>[],
        ];//牌组
        foreach($power['bet'] as $key => $vo){
            $player = $vo['player'];
            $bet = $vo['status'];
            $abc[$player] = $bet;
        }
         /**
         * 空值 3张非公牌
         */ 
        var_dump($abc);
        foreach($abc as $player => $bet){
            switch($bet){
                case '':
                $deck[$player] = self::notRquiement();
                break;

                case 'sanGong':
                $deck[$player] = self::sanGong();
                break;

                case 'toDeal':
                $deck[$player] = self::toDeal();
                break;
               
                case 'win':
                $deck[$player] = self::win();
                break;

                case 'peace':
                $deck[$player] = self::peace($banker);
                break;

                case 'lose':
                $deck[$player] = self::lose();
                break;

                default:
                break;
            }
        }
        $deck['banker'] = $banker;
        return $deck;
    }


/*********************************************playerPower*************************** */
    //没请求
    public function notRquiement(){
        $countNotP = count($this->notP);
        $Deck1Sub = rand(0,$countNotP-1);
        $Deck1 = $this->notP[$Deck1Sub];
        unset($this->notP[$Deck1Sub]);
        $this->notP = array_values($this->notP);

        $countNotP = count($this->notP);
        $Deck2Sub = rand(0,$countNotP-1);
        $Deck2 = $this->notP[$Deck2Sub];
        unset($this->notP[$Deck2Sub]);
        $this->notP = array_values($this->notP);

        $countNotP = count($this->notP);
        $Deck3Sub = rand(0,$countNotP-1);
        $Deck3 = $this->notP[$Deck3Sub];
        unset($this->notP[$Deck3Sub]);
        $this->notP = array_values($this->notP);

        $deck = [
            $this->deck[$Deck1],
            $this->deck[$Deck2],
            $this->deck[$Deck3]
        ];
        return $deck;
    }

    //三公
    public function sanGong(){
        $countP = count($this->p);
        $Deck1Sub = rand(0,$countP-1);
        $Deck1 = $this->p[$Deck1Sub];
        unset($this->p[$Deck1Sub]);
        $this->p = array_values($this->p);

        $countP = count($this->p);
        $Deck2Sub = rand(0,$countP-1);
        $Deck2 = $this->p[$Deck2Sub];
        unset($this->p[$Deck2Sub]);
        $this->p = array_values($this->p);

        $countP = count($this->p);
        $Deck3Sub = rand(0,$countP-1);
        $Deck3 = $this->p[$Deck3Sub];
        unset($this->p[$Deck3Sub]);
        $this->p = array_values($this->p);

        $deck = [$this->deck[$Deck1],$this->deck[$Deck2],$this->deck[$Deck3]];
        return $deck;
    }

    /**
     * 假如这是最后一个要求对牌：
     *      庄家拿走两个，闲家都是对牌或者无要求拿走6个，即最多拿走8个非公牌
     * 非公牌总数39张最少剩下31张最多剩下37张
     * 顺子以及对子产生的概率比较高算法时间较短，顺子，同花顺概率比较小耗时长但是都做一下
     * 非公牌中产生 顺子，同花，同花顺，对子，三条
     * 对牌
     * 目前第一版只产生对子，同花
     */
    public function toDeal(){
        //对牌总的牌型
        $cardTypeAll = ['pair','three','straight','straightFlush'];
        //生成要产生的牌型
        $cardTypeSub = rand(0,count($cardTypeAll)-1);
        $cardType = $cardTypeAll[$cardTypeSub];

        switch($cardType){
            //产生对子
            case "pair":
                $card = self::createPair();
            break;

            //产生顺子
            case "straight":
                $card = self::createStraight();
            break;

            //产生同花 有点难找，先不做算法
            case "flush":
                $card = self::createFlush();
            break;
            
            //产生三条
            case "three":
                $card = self::createThree();
            break;

            //产生同花顺
            case "straightFlush":
                $card = self::createStraightFlush();
            break;

            default:
            echo "kaigua toDeal parameter fault---\n";
            break;
        }
        // $card['type'] = $cardType;
        return $card;
    }   

    /**
     * 生成对子 
     * 先随机生成一张非公牌 
     * 第一张牌牌除以13求整$beishu 
     * 第二张牌比第一张牌大(|1,2,3|-$beishu)13
     * 直找到两张牌同时存在，第三张牌随机
     * 可能产生三条
     * 当前随机牌没有对子，随机牌下标+1 - floor(随机牌下标/($countNotP-1))
     */
    public function createPair(){
        $flag = 0;
        $temp = $this->notP;
        do{
            //重置非公牌组
            $this->notP = $temp;
            $countNotP = count($this->notP);
            //当前随机牌没有对子，随机牌下标+1 - floor(随机牌下标/($countNotP-1))
            if(isset($NotPSub)){
                $NotP1Sub++;
            }
            else{
                $NotP1Sub =  rand(0,$countNotP-1);
            }
            //生成随机非公牌下标
            $NotP1 = $this->notP[$NotP1Sub];
            unset($this->notP[$NotP1Sub]);
            $this->notP = array_values($this->notP);
            
            //第一张牌除以13求整$beishu 
            $beishu = floor($NotP1/13);
            $yvshu = $NotP1%13;
            //第二张牌比第一张牌大(|1,2,3|-$beishu)13
            $in = 0;//内层循环标识
            for($i = 1; $i<=3; $i++){
                $NotP2 = $yvshu + abs(($i - $beishu))*13;
                //寻找第二张牌
                if(in_array($NotP2,$this->notP)){
                    $in = 1;
                    $NotP2Sub = array_keys($this->notP,$NotP2);
                }
                if($in == 1){
                    $flag = 0;
                    unset($this->notP[$NotP2Sub[0]]);
                    $this->notP = array_values($this->notP);
                    
                    //随机产生第三张牌
                    $NotP3Sub = rand(0,count($this->notP)-1);
                    $NotP3 = $this->notP[$NotP3Sub];
                    unset($this->notP[$NotP3Sub]);
                    $this->notP = array_values($this->notP);
                    break;
                }
            }
        }while($flag);
        $card = [
            $this->deck[$NotP1],$this->deck[$NotP2],$this->deck[$NotP3],
        ];
        return $card;
    }
    /**
     * 生成顺子
     * 先随机生成一张非公牌 
     * 牌下标除以13求整$beishu 
     * 第二张牌比第一张牌大(|0,1,2,3|-$beishu)13+1
     * 第二张牌除以13求整$beishu2 
     * 第三张牌比第二张牌大(|3,2,1,0|-$beishu2)13+1 
     * 当都为0的时候 为同花顺
     * 当前随机牌没有顺子，随机牌数组下标+1 - floor(随机牌下标/($countNotP-1))
     */
    public function createStraight(){
        $flag = 1;
        $temp = $this->notP;

        do{
            //重置非公牌组
            $this->notP = $temp;
            $countNotP = count($this->notP);
            //当前随机牌没有对子，随机牌下标+1 - floor(随机牌下标/($countNotP-1))
            if(isset($NotP1Sub)){
                $NotP1Sub++;
                //$NotP1Sub == 10 重置0
                if($NotP1Sub == $countNotP){
                    $NotP1Sub = 0;
                }                
            }
            else{
                $NotP1Sub =  rand(0,$countNotP-1);
            }
            //生成随机非公牌下标
            $NotP1 = $this->notP[$NotP1Sub];
            unset($this->notP[$NotP1Sub]);
            $this->notP = array_values($this->notP);
            
            //第一张牌除以13求整$beishu 
            $beishu = floor($NotP1/13);
            $yvshu = $NotP1%13;
            //第二张牌比第一张牌大(|1,2,3|-$beishu)13+1 繁殖同花顺所以不给0
            $in = 0;//内层循环标识
            for($i = 1; $i<=3; $i++){
                $NotP2 = $yvshu + abs(($i - $beishu))*13+1;

                //寻找第二张牌
                if(in_array($NotP2,$this->notP)){
                    $in = 1;
                    $NotP2Sub = array_keys($this->notP,$NotP2);
                }
                //寻找第三张牌
                if($in == 1){
                    unset($this->notP[$NotP2Sub[0]]);
                    $this->notP = array_values($this->notP);
                    
                    //第二张牌除以13求整$beishu2 
                    $beishu2 = floor($NotP2/13);
                    $yvshu2 = $NotP2%13;
                    // 第三张牌比第二张牌大(|3,2,1,0|-$beishu2)13+1
                    $in2 = 0;
                    for($j = 3; $j >= 0; $j--){
                        $NotP3 = $yvshu2 + abs(($j - $beishu2))*13+1;

                        //寻找第三张牌
                        foreach($this->notP as $key=>$vo){
                            if($vo == $NotP3){
                                $in2 = 1;
                                $NotP3Sub = $key;
                                break;
                            }
                        }
                        if($in2 == 1){
                            $flag = 0;
                            unset($this->notP[$NotP3Sub]);
                            $this->notP = array_values($this->notP);
                            break;
                        }
                    }
                    if($in2 ==1 ){
                        break;
                    }
                }
            }
        }while($flag);
        $card = [ 
            $this->deck[$NotP1],$this->deck[$NotP2],$this->deck[$NotP3]
        ];
        return $card;
    }
    /**
     * 生成同花
     * 同花有点难找,先不管
     */
    public function createFlush(){

    }

    /**
     * 生成同花顺
     * 先随机生成一张非公牌
     * 第二张牌比第一张牌下标大1 第三张比第一张牌下标大2
     * 当前随机牌没有同花，随机牌下标+1 - floor(随机牌下标/($countNotP-1))
     */
    public function createStraightFlush(){
        $flag = 1;
        $temp = $this->notP;
        $countNotP = count($this->notP);

        do{
            //重置非公牌组
            $this->notP = $temp;
            //当前随机牌没有同花，随机牌下标+1 
            if(isset($NotP1Sub)){
                $NotP1Sub++;
                //$NotP1Sub == 10 重置0
                if($NotP1Sub == $countNotP){
                    $NotP1Sub = 0;
                }
            }
            else{
                $NotP1Sub =  rand(0,$countNotP-1);
            }
            //生成随机非公牌下标
            $NotP1 = $this->notP[$NotP1Sub];
            unset($this->notP[$NotP1Sub]);
            $this->notP = array_values($this->notP);

            // 第二张牌比第一张牌下标大1 第三张比第一张牌下标大2
            $NotP2 = $NotP1+1;
            $NotP3 = $NotP2+1;
            if(in_array($NotP2,$this->notP)&&in_array($NotP3,$this->notP)){
                $flag = 0;
                $key1 = array_keys($this->notP,$NotP2);
                $key2 = array_keys($this->notP,$NotP3);
                unset($this->notP[$key1[0]]);
                unset($this->notP[$key2[0]]);
                $this->notP = array_values($this->notP);
            }
            
       }while($flag);
       $card = [
           $this->deck[$NotP1],$this->deck[$NotP2],$this->deck[$NotP3]
       ];
       return $card;
    }

    /**
     * 生成三条 
     * 先随机生成一张非公牌 
     * 牌除以13求整$beishu 第二张牌比第一张牌大(|1|-$beishu)13
     * 第三张牌比第一张牌张牌大(|2,3|-$beishu)13
     * 当前随机牌没有三条，随机牌下标+1 - floor(随机牌下标/($countNotP-1))
     */
    public function createThree(){
        $flag = 0;
        $temp = $this->notP;
        do{
            //重置非公牌组
            $this->notP = $temp;
            $countNotP = count($this->notP);
            //当前随机牌没有对子，随机牌下标+1 - floor(随机牌下标/($countNotP-1))
            if(isset($NotP1Sub)){
                $NotP1Sub++;
                //$NotP1Sub == 10 重置0
                if($NotP1Sub == $countNotP){
                    $NotP1Sub = 0;
                }
            }
            else{
                $NotP1Sub =  rand(0,$countNotP-1);
            }
            //生成随机非公牌下标
            $NotP1 = $this->notP[$NotP1Sub];
            unset($this->notP[$NotP1Sub]);
            $this->notP = array_values($this->notP);
            
            //第一张牌除以13求整$beishu 
            $beishu = floor($NotP1/13);
            $yvshu = $NotP1%13;
            //第二张牌比第一张牌大(|1,2,3|-$beishu)13

            $NotP2 = $yvshu + abs((1 - $beishu)*13);
            //找不到第二张牌直接下一次随机
            if(!in_array($NotP2,$this->notP)){
                continue;
            }
            $NotP2Sub = array_keys($this->notP,$NotP2);

            unset($this->notP[$NotP2Sub[0]]);
            $this->notP = array_values($this->notP);

            for($i = 2; $i<=3; $i++){
                $NotP3 = $yvshu + abs(($i - $beishu))*13;
                //寻找第二张牌 
                if(in_array($NotP3,$this->notP)){
                    $NotP3Sub = array_keys($this->notP,$NotP3);
                    unset($this->notP[$NotP3Sub[0]]);
                    $this->notP = array_values($this->notP);
                    $flag = 0;
                }
            }
        }while($flag);
        $card = [
            $this->deck[$NotP1],
            $this->deck[$NotP2],
            $this->deck[$NotP3],
        ];
        return $card;

    }    
    /**
     * 赢
     */
    public function win(){
        $countP = count($this->p);//公牌组长度;
        $countNotP = count($this->notP);//非公牌长度;
        $flag = 0;//规则标志
        do{
            $playerP1Sub = rand(0,$countP-1);
            $playerP2Sub = rand(0,$countP-1);

            $playerP1 = $this->p[$playerP1Sub];
            $playerP2 = $this->p[$playerP2Sub];

            $playerP1Deck = $this->deck[$playerP1];
            $playerP2Deck = $this->deck[$playerP2];

            
            //同一张牌，对子，同花不通过
            if( ($playerP1Deck == $playerP2Deck) || ($playerP1Deck['size'] == $playerP2Deck['size']) || ($playerP1Deck['color'] == $playerP2Deck['color'])){
                $flag = 1;
            }
            else{
                $flag = 0;
            }
        }while($flag);

        unset($this->p[$playerP1Sub]);
        unset($this->p[$playerP2Sub]);
        $this->p = array_values($this->p);
  
        //2-9的牌
        $flag = 0;
        do{
            $playerNotPSub = rand(0,$countNotP-1); 
            $playerNotP = $this->notP[$playerNotPSub];
            $playerNotPDeck = $this->deck[$playerNotP];
            if($playerNotPDeck['size']>9&&$playerNotPDeck['size']<2){
                $flag = 1;
            }
            else{
                $flag = 0;
            }
        }while($flag);

        unset($this->notP[$playerNotPSub]);
        $this->notP = array_values($this->notP);

        $playerCard = [
            $playerP1Deck,$playerP2Deck,$playerNotPDeck,
        ];
        return $playerCard;
    }
    /**
     * 输
     */
    public function lose(){
        $card = [];
        $countNotP = count($this->notP);
        do{
            $playerNotP1Sub = rand(0,$countNotP-1); //2
            $playerNotP2Sub = rand(0,$countNotP-1); //2
            $playerNotP3Sub = rand(0,$countNotP-1); //2
            
            $playerNotP1 = $this->notP[$playerNotP1Sub];
            $playerNotP2 = $this->notP[$playerNotP2Sub];
            $playerNotP3 = $this->notP[$playerNotP3Sub];

            $card[] = $this->deck[$playerNotP1];
            $card[] = $this->deck[$playerNotP2];
            $card[] = $this->deck[$playerNotP3];
 
            $straight = self::straight($card); //顺子
            $flush = self::flush($card); //同花
            $pair = self::pair($card); //对子
            

            if($straight||$flush||$pair){//9
                $flag = 1;
                $card = [];
            }
            else{
                $flag = 0;
            }
        }while($flag);
        unset($this->notP[$playerNotP1Sub]);
        unset($this->notP[$playerNotP2Sub]);
        unset($this->notP[$playerNotP3Sub]);

        $this->notP = array_values($this->notP);
        return $card;
    }

    /**
     * 和
     * 先产生p牌
     * 把庄家牌的点数求出来
     * 再随机产生一个非p牌
     * 求出庄家点数与新产生牌的点数的差
     * 从非p找到这个差值的点数的牌并且赋值给第三张牌
     * 检测是否是同一张牌
     */
    public function peace($banker){

        $countP = count($this->p);
        $countNotP = count($this->notP);

        //先产生p牌
        $deckPSub = rand(0,$countP-1);
        $deckP = $this->p[$deckPSub];
        unset($this->p[$deckPSub]);
        $this->p = array_values($this->p);

        $bankerSize = 0;
        //先把庄家牌的点数求出来
        $bankerSize = ($banker[1]['size'] + $banker[2]['size'])%10;
        //再随机产生一个非p牌
        $flag = 1;
        do{
            $deckNotP1Sub = rand(0,$countNotP-1);
            $deckNotP1 = $this->notP[$deckNotP1Sub];
            $deckNotP = $this->deck[$deckNotP1];

            //求出庄家点数与新产生牌的点数的差
            $distinct = $bankerSize - $deckNotP['size'];
            if($distinct < 0){
                $distinct = $distinct + 10;
            }

            //从非p找到这个差值的点数的牌并且赋值给第三张牌
            foreach( $this->notP as $key => $vo ){
                if( $vo%10 == $distinct ){
                    $deckNotP2Sub = $key;
                    //检测是否是同一张牌
                    if($deckNotP2Sub != $deckNotP1Sub){
                        $flag = 0;
                        break;
                    }
                }
            }
        }while($flag);

        //找到刚刚产生的两张牌
        $deckNotP = $this->notP[$deckNotP1Sub];
        $deckNotP2 = $this->notP[$deckNotP2Sub];
        //去掉刚刚产生的两张牌
        unset($this->notP[$deckNotP1Sub]);
        unset($this->notP[$deckNotP2Sub]);
        $this->notP = array_values($this->notP);

        $card = [
            $this->deck[$deckP],$this->deck[$deckNotP],$this->deck[$deckNotP2]
        ];
        return $card;
    } 
/*********************************************bankerPower********************************* */

    /**
     * 闲家赢：2个非对子非同花p，一个2到9的随机牌
     */
    public function playerWin(){
        $countP = count($this->p);//公牌组长度;
        $countNotP = count($this->notP);//非公牌长度;
        $flag = 0;//规则标志
        do{
            $playerP1Sub = rand(0,$countP-1);
            $playerP2Sub = rand(0,$countP-1);

            $playerP1 = $this->p[$playerP1Sub];
            $playerP2 = $this->p[$playerP2Sub];

            $playerP1Deck = $this->deck[$playerP1];
            $playerP2Deck = $this->deck[$playerP2];

            
            //同一张牌，对子，同花不通过
            if( ($playerP1Deck == $playerP2Deck) || ($playerP1Deck['size'] == $playerP2Deck['size']) || ($playerP1Deck['color'] == $playerP2Deck['color'])){
                $flag = 1;
            }
            else{
                $flag = 0;
            }
        }while($flag);

        unset($this->p[$playerP1Sub]);
        unset($this->p[$playerP2Sub]);
        $this->p = array_values($this->p);
  
        //2-9的牌
        $flag = 0;
        do{
            $playerNotPSub = rand(0,$countNotP-1); 
            $playerNotP = $this->notP[$playerNotPSub];
            $playerNotPDeck = $this->deck[$playerNotP];

            if($playerNotPDeck['size']>9&&$playerNotPDeck['size']<2){
                $flag = 1;
            }
            else{
                $flag = 0;
            }
        }while($flag);

        unset($this->notP[$playerNotPSub]);
        $this->notP = array_values($this->notP);

        $playerCard = [
            $playerP1Deck,$playerP2Deck,$playerNotPDeck,
        ];
        return $playerCard;
    }

    // 闲家输：3张非对子非同花非顺子并且非p的牌
    public function playerLose(){
        $card = [];
        $countNotP = count($this->notP);
        do{
            $playerNotP1Sub = rand(0,$countNotP-1); 
            $playerNotP2Sub = rand(0,$countNotP-1); 
            $playerNotP3Sub = rand(0,$countNotP-1); 
            
            $playerNotP1 = $this->notP[$playerNotP1Sub];
            $playerNotP2 = $this->notP[$playerNotP2Sub];
            $playerNotP3 = $this->notP[$playerNotP3Sub];

            $card[] = $this->deck[$playerNotP1];
            $card[] = $this->deck[$playerNotP2];
            $card[] = $this->deck[$playerNotP3];
 
            $straight = self::straight($card); //顺子
            $flush = self::flush($card); //同花
            $pair = self::pair($card); //对子
            
            //同一张牌必定是对子，同花
            if($straight||$flush||$pair){
                $flag = 1;
                $card = [];
            }
            else{
                $flag = 0;
            }
        }while($flag);
        unset($this->notP[$playerNotP1Sub]);
        unset($this->notP[$playerNotP2Sub]);
        unset($this->notP[$playerNotP3Sub]);

        $this->notP = array_values($this->notP);
        return $card;
    }
/*********************************************情景假设******************************************************************* */
    //计算顺子
    public function straight($card){
        $flag = 0;
        // 把数组的大小取出来
        $array = [
            '0'=>$card[0]['size'],
            '1'=>$card[1]['size'],
            '2'=>$card[2]['size']
        ];
        // 升序排列数组
        sort($array);
        //最大值比最小值大2或者A Q K牌型
        if(($array[2]-$array[0]==2)||($array[0]==1&&$array[1]==12&&$array[2]==13)){
            $flag = 1;
        }
        return $flag;
    }
    
    //计算对子
    public function pair($card){
        $flag = 0;
        // 把数组的大小取出来
        $array = [
            '0'=>$card[0]['size'],
            '1'=>$card[1]['size'],
            '2'=>$card[2]['size']
        ]; 
        if($array[0]==$array[1]||$array[0]==$array[2]||$array[1]==$array[2]){
            $flag = 1;
        } 
        return $flag;
    }

    //计算同花  flush  pair straight three
    public function flush($card){
        $flag = 0;
        if(($card[0]['color'] == $card[1]['color']&&$card[1]['color'] == $card[2]['color'])){
            $flag = 1;
        }
        return $flag;

    }

    //各种牌型数组
    public function cardType(){
        /**
         * 特征
         * 顺子
         *  
         */        
        $p = [
            10,11,12,
            23,24,25, 
            36,37,38, 
            49,50,51,
        ];
        echo $p[0];

        $notP = [
            0,1,2,3,4,5,6,7,8,9,
            13,14,15,16,17,18,19,20,21,22,
            26,27,28,29,30,31,32,33,34,35,
            39,40,41,42,43,44,45,46,47,48,
        ];
    }


/**
 * 情景假设
 * 情景1: 庄家:0p7   闲家1 : 三公 闲家2: 赢    闲家3:三公
 * 情景2: 庄家:1p7   闲家1 : 三公 闲家2: 输    闲家3:三公
 * 情景3: 庄家:2p7   闲家1 : 三公 闲家2: 赢    闲家3:和局
 * 情景4: 庄家:0p7   闲家1 : 三公 闲家2: 对牌  闲家3:三公
 * 情景5: 庄家:1p7   闲家1 : 赢   闲家2: 和局  闲家3:三公
 * 情景6: 庄家:2p7   闲家1 : 和 闲家2: 和  闲家3:和
 */
}
/**
 * notRquiement //done
 * sanGong //done
 * toDeal  //
 * createPair //done
 * createStraight //
 * createStraightFlush//done
 * createThree//done
 * win //done
 * lose//done
 * peace//done
 *  */


file_put_contents(dirname(__FILE__)."./power.json",'');
for($i=0;$i<=1000;$i++){
    $bocaiPower = new Power;
    $banker = $bocaiPower->bocaiDeal();
    $temp = json_encode($banker);
    file_put_contents(dirname(__FILE__)."./power.json",$temp.",\n",FILE_APPEND);
}
