<?php
namespace app\Sangong\controller;
use think\Controller;
use think\Db;
use think\facade\Request;
use Workerman\lib\Timer;
use app\Sanggong\controller\Power;
/*第一步
用户进入房间：前端ajax请求判断是否金币足够进入房间 index
房间内：websocket接受前端传过来的用户信息整合成一个数组使用begin函数进行结算，并且分别返回每一个用户的信息；
*/

/************玩法规则****************************************
一  三公普通大小对比 size() 
二  对牌
    1   同花顺  straightFlush()
    2   同花    flush()
    3   顺子    straight()
    4   对子    pair()
    5   三条    three()
***********玩法规则******************************************/



class Index extends Controller
// class Index
{

    //玩家数据以及发牌格式
    public function dataFormat(){
           // $player['data'] = [
            //     '0'=>[
            //         "player" =>'player',
            //         "id" => 11, //用户的id
            //         "bet"=>[     //用户的赌局
            //            '0'=> ["player" =>"player2","status" =>"toDeal"],
            //            '1'=> ["player" =>"player1","status" =>"win"]
            //         ],
            //         "gold" =>10,
            //         "allGold"=>20,
            //         "remaining"=>1000
            //     ],
            //     '1'=>[
            //         "player" =>'player',
            //         "id" => 12, //用户的id
            //         "bet"=>[     //用户的赌局
            //            '0'=> ["player" =>"player2","status" =>"toDeal"],
            //            '1'=> ["player" =>"player1","status" =>"win"]
            //         ],
            //         "gold" =>10,
            //         "allGold"=>20,
            //         "remaining"=>1000
            //     ],
            //     '2'=>[
            //         "player" =>'player',
            //         "id" => 14, //用户的id
            //         "bet"=>[     //用户的赌局
            //            '0'=> ["player" =>"player2","status" =>"toDeal"],
            //            '1'=> ["player" =>"player1","status" =>"win"]
            //         ],
            //         "gold" =>10,
            //         "allGold"=>20,
            //         "remaining"=>1000 
            //     ]
            // ];


            // 根据玩家押注类型 发牌的结果 进行计算
            // $deal = [
            //     'banker'=>[
                    
            //             '0'=>['color'=>'red','size'=>10],
            //             '1'=>['color'=>'red','size'=>1],
            //             '2'=>['color'=>'red','size'=>10]
                  
            //     ],
            //     'player1'=>[
                   
            //             '0'=>['color'=>'red','size'=>13],
            //             '1'=>['color'=>'red','size'=>1],
            //             '2'=>['color'=>'red','size'=>2]
                     
            //     ],
            //     'player2'=>[
                    
            //             '0'=>['color'=>'red','size'=>1],
            //             '1'=>['color'=>'red','size'=>2],
            //             '2'=>['color'=>'red','size'=>3]
                   
            //     ],
            //     'player3'=>[
                     
            //             '0'=>['color'=>'red','size'=>11],
            //             '1'=>['color'=>'red','size'=>11],
            //             '2'=>['color'=>'red','size'=>11]
                   
            //     ]
            // ];

    }



    
        public function __construct(){
            parent::__construct();
        }
    /*
    */
    public function enterRoom(){
        $roomInfo = Db::name("games_sg_desk_setting")->select();
        return $roomInfo;
    }

/*********************************************************结算函数***************************************************/
    // public function begin()
    public function begin($bet,$deskId,$issue,$goldPool)
    {
        $player['data'] = $bet;
        echo "game begin";

        /**
         * 庄家不能押注
         * 庄家的信息要返回去
         * 庄家逃跑或者掉线，庄家不变
         * 每局游戏结束的时候要产生新的庄家
        */

        //防止庄家掉线
        $banker = Db::name('games_sg_banker')->
                    alias('banker')->order('time desc')->
                    join('user_user u','u.id = banker.banker_id')->
                    field('u.gold,u.id,u.bocai')->
                    find();
            //平台玩家
            $searchPower = Db::name("games_sg_power")->where(["issue"=>$issue,"desk_id"=>$deskId])->find();
            if($searchPower){
                //读取平台庄家的需求,把平台玩家所要收掉的金币池吃掉
                echo "----- power------ ";
                $power = new Power;
                //产生发牌顺序
                $orderSub = self::order();
                //发牌
                $deal = $power->bocaiDeal($searchPower['power'],$goldPool);
            }                
            // $request = array(
            //     "id" => 10, //用户的id
            //     "bet"=>[     //用户的赌局
            //        '0'=> ["player" =>"player2    ","status" =>"toDeal"],
            //        '1'=> ["player" =>"player1","status" =>"win"]
            //     ],
            //     "gold" =>100     
            // );

            //非平台庄家发牌
            else{
                //产生发牌顺序
                $orderSub = self::order();
                //发牌
                $deal = self::deal($orderSub);
            }
    /*
    *标准玩家信息与牌子信息+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    */
     /*
    *标准玩家信息与牌子信息++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    */
            //记录庄家开局前的金币
            $banker['gameBefore'] = $banker['gold'];

            //先把赌注收上来
            $bet = 0;
            foreach($player['data'] as $key=>$vo){
                if($vo['player']!="banker"){
                    $bet +=$vo['allGold'];
                }
            }
            $banker['gold'] += $bet;

            $odds = self::odds();//赔率

            $result = [];//保存用户赌注输赢

            //牌桌的倍率
            $multiple = Db::name("games_sg_desk_setting")->column("multiple");
            foreach($player['data'] as $key => $vo){
                $player['data'][$key]['gameBefore'] = Db::name("user_user")->where("id",$vo['id'])->value('gold');
                //用户总金币 - 用户押注的金币 = 用户剩下的金币
                $player['data'][$key]['remaining'] = $player['data'][$key]['gameBefore'] - $vo['allGold'];
                Db::name("user_user")->where("id",$vo['id'])->update(['gold'=>$player['data'][$key]['remaining']]);
                
                //结算围观下注玩家
                if($player['data'][$key]['player']!='banker'){
                    $result[$key]  = self::gameResult($vo,$deal,$odds,$multiple);
                }
            }

            //根据庄家及用户的押注结果求出最终的金币结果
            $total = 0;
            foreach($result as $key=>$vo){
                $total += $vo;
            }

            //庄家足够金币 全额赔钱
            if($banker['gold'] >= $total){
                foreach($player['data'] as $key => $vo){
                    if($vo['player'] != "banker"){
                       $player['data'][$key]['win'] = $result[$key];
                       $player['data'][$key]['remaining'] =  $player['data'][$key]['remaining'] + $player['data'][$key]['win'];
                    }
                } 
                $banker['gold'] = $banker['gold'] - $total;
                //开局后庄家的金币
                $banker['gameAfter'] = $banker['gold'];
                // echo "banker['gold']:";dump($banker['gold']);
            }
            else{
                //钱不够按比例赔
                foreach($player['data'] as $key => $vo){
                    if($vo['player'] != "banker"){
                        $player['data'][$key]['win'] = $result[$key]*($banker['gold']/$total);
                        $player['data'][$key]['remaining'] =  $player['data'][$key]['remaining'] + $player['data'][$key]['win'];
                    }
                }
                $banker['gold'] = 0;
                $banker['gameAfter'] = 0;
                // echo "banker['gold']:";dump($banker['gold']);
            }
            // //整理一个用户出来 用作游戏记录
            // $player['data'] = [
            //     '0'=>[
            //         "id" => 11, //用户的id
            //         "bet"=>[     //用户的赌局
            //            '0'=> ["player" =>"player2","status" =>"toDeal"],
            //            '1'=> ["player" =>"player1","status" =>"win"]
            //         ],
            //         "gold" =>10,
            //         "allGold"=>20,
            //         "remaining"=>1000
            //     ],
            //     '1'=>[
            //         "id" => 12, //用户的id
            //         "bet"=>[     //用户的赌局
            //            '0'=> ["player" =>"player2","status" =>"toDeal"],
            //            '1'=> ["player" =>"player1","status" =>"win"]
            //         ],
            //         "gold" =>10,
            //         "gameBefore"=>1020,
            //         "gameAfter"=>$result[$key]
            //     ]
            // ];

            foreach($player['data'] as $key=>$vo){
                if($player['data'][$key]['player']!='banker'){
                    $player['data'][$key]['gameBefore'] = $player['data'][$key]['allGold']+$player['data'][$key]['remaining'];
                    $player['data'][$key]['gameAfter'] = $result[$key];
                }
            }

            //把顺序扑克牌转成数字
            $sub = self::sub($orderSub);

            //转顺序为牌
            $orderSub = self::pokerOrder($orderSub);
            // echo "orderSub:";dump($orderSub);
            
            //开牌的类型2p3代表2公3
            $cardType = self::addCardType($deal);

            $deal = self::poker($deal);
            
            // dump($deal);

            //把游戏记录进数据表中
            self::record($banker,$player,$deal);

            $standard = Db::name("games_sg_desk_setting")->where(["desk_id"=>0])->value('gold_min');
            //整理返回去的玩家的信息
            foreach($player['data'] as $key => $vo){
                //庄家的信息
                $player['data'][$key]['status'] = 1;//默认足够金币

                if($vo['player'] == 'banker'){
                    $player['data'][$key]['code'] = 5;
                    $player['data'][$key]['orderSub'] = $orderSub;//发牌顺序
                    $player['data'][$key]['deal'] = $deal;   //发牌结果
                    $player['data'][$key]['remaining'] = $banker['gameAfter'];//庄家剩余金币
                    $player['data'][$key]['win'] = $banker['gameBefore']-$banker['gameAfter'];
                    $player['data'][$key]['sub'] = $sub;
                    $player['data'][$key]['cardType'] = $cardType;

                    unset($player['data'][$key]['player']);
                    // $player['data'][$key] = json_encode($player['data'][$key]);
                }
                else{
                //普通玩家的信息
                    $player['data'][$key]['code'] = 5;//发牌顺序
                    $player['data'][$key]['orderSub'] = $orderSub;//发牌顺序
                    $player['data'][$key]['deal'] = $deal;   //发牌结果
                    $player['data'][$key]['sub'] = $sub;
                    $player['data'][$key]['cardType'] =$cardType;


                    unset($player['data'][$key]['player']);
                    unset($player['data'][$key]['bet']);
                    unset($player['data'][$key]['gold']);
                    unset($player['data'][$key]['gameBefore']);
                    unset($player['data'][$key]['gameAfter']);
                    unset($player['data'][$key]['player']);
                    unset($player['data'][$key]['allGold']);
                    // unset($player['data'][$key]['remaining']);
                }
                if($player['data'][$key]['remaining']<$standard){
                    $player['data'][$key]['status'] = 0;
                }//金币不足,标志为0
            }

            $deal = [
                'code'=>4,
                'orderSub'=>$orderSub,
                'deal'=>$deal,
                'sub' =>$sub,
                'cardType' =>$cardType
            ];
            $player['deal'] = $deal;

           //单独记录一个牌型给没有押注的玩家
            // $player = json_encode($player);
            // echo "player['data']:";dump($player['data']);
            //把牌跟各个用户赌局结算后的金币返回给前台
            return $player;
            // echo json_encode($player);
            // return;
        // return $this->fetch();
    }
//开排顺序   0庄家  1闲一  2闲二  3闲3
public function sub($orderSub){
            switch($orderSub){
           /* 1 5 9 13 庄 闲一 闲二 闲三
            2 6 10  闲一 闲二 闲三 庄 
            3 7 11  闲二 闲三 庄 闲一
            4 8 12  闲三 庄 闲一 闲二
           */
            case 1:
            case 5:
            case 9:
            case 13:

            $sub = 0;
            break;

            case 2:
            case 6:
            case 10:
            $sub = 1;
            break;

            case 3:
            case 7:
            case 11:
            $sub = 2;

            break;

            case 4:
            case 8:
            case 12:
            $sub = 3;
            break;
            default:
            break;
        }
        return $sub;
}

/**
* 用于没有玩家押注时返回牌型
*/
public function returnDeal(){
    //产生发牌顺序
    $orderSub = self::order();
    $sub = self::sub($orderSub);

    //发牌
    $deal = self::deal($orderSub);
    //转顺序为牌
    $orderSub = self::pokerOrder($orderSub);
    // echo "orderSub:";dump($orderSub);
    //把牌组的数据转换成扑克牌

    $cardType = self::addCardType($deal);
    $deal = self::poker($deal);
    $deal = [
        'code' => 4,//没有玩家押注时发牌信号
        'orderSub'=>$orderSub,
        'deal'=>$deal,
        'sub' =>$sub,
        'cardType'=>$cardType
    ];

    //记录发牌历史

    $deal['deal'] = $deal;
    $deal['banker'] = '';
    //单独记录一个牌型给没有押注的玩家
    return $deal;
}

    //添加牌型
    public function addCardType($deal){
        foreach($deal as $player=>$cardArr){
            //p的数目
            $deal[$player]['p'] = 0;
            //个位数
            $deal[$player]['count'] = 0;
            foreach($cardArr as $sub=>$card){
                if($card['size'] > 10){
                    $deal[$player]['p']++;
                }
                else{
                    $deal[$player]['count']+=$card['size'];
                }
                
            }
            $deal[$player]['count']%=10;
            if($deal[$player]['p']>0){
                $cardType[$player]=$deal[$player]['p']."P".$deal[$player]['count'];
            }       
            else{
                $cardType[$player]=$deal[$player]['count']; 
            }
            unset($deal[$player]['p']);
            unset($deal[$player]['count']);
        }
        return $cardType;
    }   

    /**
    * 记录游戏
    * Db::name('user_user') 
      gold
    * Db::name('sg_desk_history')  
      player:banker:庄家 player1:闲家1  player2:闲家2   player3:闲家3
      result:  deal['banker']
      roomid:使用默认0
      time :time()
    * Db::name('sg_player_history')
      user_id:用户id
      time:time()
      bet:下注类型   $player['data'][$key]['bet']
      result:下珠结果  加金币 减金币
      game_before:开局前金币
      game_after:开局后金币
    */

      public function record($banker,$player,$deal){
        //更新庄家的金币 及游戏记录
        Db::name('user_user')->where(['id'=>$banker['id']])->update(['gold'=>$banker['gameAfter']]);
        Db::name('games_sg_player_history')->insert([
            'user_id'=>$banker['id'],
            'time'=>time(),
            'bet'=>'庄家',
            'result'=>$banker['gameAfter'] - $banker['gameBefore'],
            'game_before'=>$banker['gameBefore'],
            'game_after' =>$banker['gameAfter']
        ]);
        //更新玩家的金币 及游戏记录
        foreach($player['data'] as $key=>$vo){
            if($vo['player']!="banker"){

                    $gold = Db::name('user_user')->where(['id'=>$player['data'][$key]['id']])->value('gold');
                    $new = $gold + $player['data'][$key]['win']; 
                    Db::name('user_user')
                        ->where(['id'=>$player['data'][$key]['id']])
                        ->update(['gold'=>$new]);


                    Db::name('games_sg_player_history')->insert([
                    'user_id'=>$player['data'][$key]['id'],
                    'time'=>time(),
                    //整理用户的押注详情为一个json字符串
                    'bet'=>self::bet($player['data'][$key]['bet']),
                    'result'=>$player['data'][$key]['gameBefore'] - $player['data'][$key]['gameAfter'],
                    'game_before'=>$player['data'][$key]['gameBefore'],
                    'game_after' =>$player['data'][$key]['gameAfter']
                ]);
            }
            
        }

        //更新牌桌记录 记录牌的牌型
        $data = [
            "deal"=>[
                "banker"=>[
                    '0'=>$deal['banker'][0],
                    '1'=>$deal['banker'][1],
                    '2'=>$deal['banker'][2]
                ],
                "player1"=>[
                    '0'=>$deal['player1'][0],
                    '1'=>$deal['player1'][1],
                    '2'=>$deal['player1'][2]
                ],
                "player2"=>[
                    '0'=>$deal['player2'][0],
                    '1'=>$deal['player2'][1],
                    '2'=>$deal['player2'][2]
                ],
                "player3"=>[
                    '0'=>$deal['player3'][0],
                    '1'=>$deal['player3'][1],
                    '2'=>$deal['player3'][2]
                ]
            ]
        ];

        $data = json_encode($data);  
        try{
           Db::name('games_sg_desk_history')->insert(
            [
                'deal'=>$data,
                'desk_id'=>0,
                'time'=>time()
            ]
        );  
           echo '123';
        }  catch(\Exception $e){
            throw $e;
        }


      }
    //整理用户的押注结果为一个json字符串
      public function bet($bet){
        $bet = json_encode($bet);
        $bet=str_replace ( 'player' , '闲家' ,  $bet );
        $bet=str_replace ( 'win' , '赢' ,  $bet );
        $bet=str_replace ( 'lose' , '输' ,  $bet );
        $bet=str_replace ( 'peace' , '和' ,  $bet );
        $bet=str_replace ( 'toDeal' , '对牌' ,  $bet );
        $bet=str_replace ( 'sanGong' , '三公' ,  $bet );
        return $bet;
      }
    //获取游戏的赔率
    public function odds(){
        $odds['player1'] = Db::name('games_sg')->where(['type'=>'player1'])->column('result');
        $odds['player2'] = Db::name('games_sg')->where(['type'=>'player2'])->column('result');
        $odds['player3'] = Db::name('games_sg')->where(['type'=>'player3'])->column('result');
        foreach($odds as $player=>$result){
            foreach($result as $key=>$vo){
                $odds[$player][$vo] = Db::name('games_sg')->where(['result'=>$vo])->value('odds');
                unset($odds[$player][$key]);
            }
            
        }
        return $odds;
    }
   //把顺序牌数据转换成扑克牌
    public function pokerOrder($orderSub){
        $pokerOrder =$orderSub.'.png';
        return $pokerOrder;
    }
    //把牌组的数据转换成扑克牌
    public function poker($deal){
        //分成玩家跟牌组
        foreach($deal as $key =>$vo){
            //分成下标跟牌
            foreach($vo as $keysub=>$vo){
                $deal[$key][$keysub] = $vo['color'].'/'.$vo['size'].'.png';
            }
        }
        return $deal;
    }


    /*
    * 用户进入牌桌初始化牌桌的数据 
    * @var  percent 一个装有所有牌型赔率的数组
    * @var  limit['sub']用户下注的最低金额 limit['sup']最高金额积分同理
    * @var  jetton[] 下注的筹码组
    * @var  history 牌桌的近10盘的历史记录 
    * $var  banker 庄家的信息
    *      
    */
    public function enterDesk($data = []){
        //用户的积分或者金币之一达到下注的下限即可进入游戏牌桌
        $permision = Db::name('user_user')
                     ->alias('u')->where(['u.id'=>$data['id'],'g.desk_id'=>$data['desk']])
                     ->join('games_sg_desk_setting g','(u.gold >= g.gold_min and u.gold <= g.gold_max)or(u.integral >= g.sliver_min and u.integral <= g.sliver_max)')
                     ->find();
        //用户没达到进入房间的最低条件
        if(empty($permision)){
                $code=0;
        }
        //返回页面初始化数据 
        else{
            $code=1;
        }
            //赔率
            $percent = Db::name('games_sg_odds')->field('type,odds,result')->select();
            $stemp = [];
            foreach ($percent as $key => $value) {
                $stemp[$value['result']][$value['type']]['odds'] = $value['odds'];
                $stemp[$value['result']][$value['type']]['active'] = false;

                if($value['result'] == 'sanGong' || $value['result']=='win' || $value['result'] == 'lose'||$value['result'] == 'peace'){
                    $stemp[$value['result']]['show'] = true;
                }else{
                    $stemp[$value['result']]['show'] = false;
                }
                switch ($value['result']) {
                    case 'win':
                        $stemp[$value['result']]['name'] = '赢';
                        break;
                    case 'lose':
                        $stemp[$value['result']]['name'] = '输';
                        break;
                    case 'sanGong':
                        $stemp[$value['result']]['name'] = '三公';
                        break;
                    case 'peace':
                        $stemp[$value['result']]['name'] = '和';
                        break;
                    default:
                        $stemp[$value['result']]['name'] = '对牌以上';
                        $stemp['toDeal'][$value['type']]['active'] = false;
                        break;
                }
            }
            $percent = $stemp;
            //最低下注限制
            $limit = Db::name('games_lower_limit')->field('gold,sliver')->find();
            
            //筹码
            $jetton = Db::name('games_sg_jetton')->where(['game'=>1])->column('jetton');
            $stem = [];
            foreach ($jetton as $key => $value) {
                $stem[$key]['value'] = $value;
                $stem[$key]['active'] = false;
            }
            
            //牌桌的历史记录  deal:牌型的json字符串
            $history = Db::name('games_sg_desk_history')->field('deal')->order('time desc')->limit(10)->select();
            
            //庄家的信息
            $banker = Db::name('games_sg_banker')->alias('b')->order('time desc')->field('u.id,gold,head,nickname')->join('user_user u','u.id = b.banker_id')->find();

            $data = array(
                'code' => $code,//进入牌桌成功
                "desk"=>[
                  'deskId'=>$data['desk'],      
                  'percent' =>$percent,
                  'limit' =>$limit,
                  'jetton' =>$stem,
                  'history' =>$history,
                  'banker' =>$banker
                ]
          );
        // 后续得添加游戏进度
        return $data;
    }


    //发牌
    public function deal($orderSub){
        //生成52张牌
        for($i=1;$i<=13;$i++){
            $deck[$i] = ['color'=>'red','size'=>$i];//红心
            $deck[$i+13] = ['color'=>'spade','size' =>$i];//黑桃
            $deck[$i+26] = ['color' =>'diamond','size'=>$i];//方砖
            $deck[$i+39] = ['color'=>'wintersweet','size' =>$i];//梅花
        }
        //整合数组 令数组从零开始
        $deck = array_values($deck);
        //随机生成4副牌 banker player[]3;
        for($i=0;$i<4;$i++){
            for($j=0;$j<3;$j++){

              //随机生成一个数组下标
              $cardsub =  rand(0,count($deck)-1);

              //把下标的牌赋值给第一副牌
              $card[$i][$j] = $deck[$cardsub];

              //去掉这张牌防止重复
              unset($deck[$cardsub]);

              //array_values 重新整合数组并从零开始生成下标
              $deck = array_values($deck);
            }
        }
        //根据发排顺序产生最后发牌结果
        $card = self::fapaiOrder($card,$orderSub);
        return $card;
    }
    //发牌顺序
    public function order(){
        $orderSub = rand(1,13);
        return $orderSub;
    }
    public function fapaiOrder($card,$orderSub){
        switch($orderSub){
           /* 1 5 9 13 庄 闲一 闲二 闲三
            2 6 10  闲一 闲二 闲三 庄 
            3 7 11  闲二 闲三 庄 闲一
            4 8 12  闲三 庄 闲一 闲二
           */
            case 1:
            case 5:
            case 9:
            case 13:
            $orderCard['banker'] = $card[0];;
            $orderCard['player1'] = $card[1];
            $orderCard['player2'] = $card[2]; 
            $orderCard['player3'] = $card[3];
            break;

            case 2:
            case 6:
            case 10:
            $orderCard['player1'] = $card[0];   
            $orderCard['player2'] = $card[1];
            $orderCard['player3'] = $card[2];
            $orderCard['banker']  = $card[3];
            break;

            case 3:
            case 7:
            case 11:
            $orderCard['player2'] = $card[0];
            $orderCard['player3'] = $card[1];
            $orderCard['banker']  = $card[2];       
            $orderCard['player1'] = $card[3];
            break;

            case 4:
            case 8:
            case 12:
            $orderCard['player3'] = $card[0];
            $orderCard['banker']  = $card[1];        
            $orderCard['player1'] = $card[2];           
            $orderCard['player2'] = $card[3];
            break;
            default:
            break;
        }
        return $orderCard;
    }

    //获取游戏结果
    public function gameResult($request,$deal,$odds,$multiple){
        $allResult = 0;
        //request 玩家的信息 status 压牌类型 gold 压牌金额 $deal[$player]玩家压档口的牌(闲一，闲二，闲三)
        $classifyBanker = [
            'win','lose','peace'
        ];
        foreach($request['bet'] as $key=>$vo){
            //跟庄家对牌  'win' 'lose' 'peace'
            if(in_array($vo['status'],$classifyBanker)){
                $result = self::classifyBanker($request,$vo['status'],$deal[$vo['player']],$deal['banker'],$odds[$vo['player']])*$multiple[$vo['deskId']];
            }
            //不需要跟庄家对牌
            else {
                    $result = self::classify($request,$vo['status'],$deal[$vo['player']],$odds[$vo['player']]);
                }
                $allResult +=$result;  
        }
        //此处的结果是每个玩家输赢的金币数
        return $allResult;
    }
    //需要跟庄家比牌
    public function classifyBanker($request,$status,$playerCard,$bankerCard,$odds){
        switch($status){
            //赢
            case 'win':
            $result = self::win($request['gold'],$playerCard,$bankerCard,$odds['win']);
            break;
            //和
            case 'peace':
            $result = self::peace($request['gold'],$playerCard,$bankerCard,$odds['peace']);
            break;
            //输
            case 'lose':
            $result = self::lose($request['gold'],$playerCard,$bankerCard,$odds['lose']);
            break;  
            default:
            break;          
        }
        return $result;
    }

    //分类获取结果 不需要跟庄家比牌 ： 1 三公 2 对牌
    public function classify($request,$status,$card,$odds){
        switch($status){
            //三公
            case 'sanGong':
            $result = self::sanGong($request['gold'],$card,$odds['sanGong']);
            break;
            //对牌
            case 'toDeal':
            $result = self::toDeal($request['gold'],$card,$odds);
            break;
            default:
            $result = "参数出错";
        }
        return $result;
    }
// straightFlush 40 flush 3 pair 1 straight 6 three 30
public function toDeal($gold,$card,$odds){
    $result = self::straightFlush($gold,$card,$odds['straight'],$odds['flush'])?self::straightFlush($gold,$card,$odds['straight'],$odds['flush'])://同花顺
                (self::three($gold,$card,$odds['three'])?self::three($gold,$card,$odds['three']):         //三条 
                    (self::straight($gold,$card,$odds['straight'])?self::straight($gold,$card,$odds['straight']):    //顺子
                        (self::flush($gold,$card,$odds['flush'])?self::flush($gold,$card,$odds['flush']):        //同花
                            (self::pair($gold,$card,$odds['pair'])?self::pair($gold,$card,$odds['pair']):0))));    //对子
    return $result;


}
    //三公
    public function sanGong($gold,$card,$odds){
        //三公 三个牌子大于10
        if($card[0]['size']>10&&$card[1]['size']>10&&$card[2]['size']>10)
        $gold = $gold*$odds;

        else { 
            $gold = 0;
        }
        return $gold;
    }
    //赢
    public function win($gold,$playerCard,$bankerCard,$odds){
        $banker['p'] = 0;
        $banker['count'] =0;
        $player['p'] = 0;
        $player['count'] =0;

        //庄家牌组
        $bankerCard = [ 
            $bankerCard[0]['size'],
            $bankerCard[1]['size'],
            $bankerCard[2]['size']
        ];

        //闲家牌组
        $playerCard = [ 
            $playerCard[0]['size'],
            $playerCard[1]['size'],
            $playerCard[2]['size']
        ];

        //统计庄家的 p：公数 count:小数
        for($i=0;$i<3;$i++){
            if($bankerCard[$i]>10){
                $banker['p']++;
            }
            else{
                $banker['count'] = $banker['count']+$bankerCard[$i];
            }
        }
        $banker['count'] = $banker['count']%10;
        //统计闲家的 p：公数 count:小数
        for($i=0;$i<3;$i++){
            if($playerCard[$i]>10){
                $player['p']++;
            }
            else{
                $player['count'] = $player['count']+$playerCard[$i];
            }
        }
        $player['count'] = $player['count']%10;
        // dump($player);
        // dump($banker);
        if($banker['p']<$player['p']||($banker['p']==$player['p']&&$banker['count']<$player['count'])){
            $gold = $gold*$odds;//win
        }
        else {
            $gold = 0;
        }
        return $gold;
    }
    //和
    public function peace($gold,$playerCard,$bankerCard,$odds){
        $banker['p'] = 0;
        $banker['count'] =0;
        $player['p'] = 0;
        $player['count'] =0;

        //庄家牌组
        $bankerCard = 
        [ 
            $bankerCard[0]['size'],
            $bankerCard[1]['size'],
            $bankerCard[2]['size']
        ];

        //闲家牌组
        $playerCard = [ 
            $playerCard[0]['size'],
            $playerCard[1]['size'],
            $playerCard[2]['size']
        ];

        //统计庄家的 p：公数 count:小数
        for($i=0;$i<3;$i++){
            if($bankerCard[$i]>10){
                $banker['p']++;
            }
            else{
                $banker['count'] = $banker['count']+$bankerCard[$i];
            }
        }
        $banker['count'] = $banker['count']%10;
        //统计闲家的 p：公数 count:小数
        for($i=0;$i<3;$i++){
            if($playerCard[$i]>10){
                $player['p']++;
            }
            else{
                $player['count'] = $player['count']+$playerCard[$i];
            }
        }
        $player['count'] = $player['count']%10;
        if(($banker['p']==$player['p']&&$banker['count']==$player['count'])){
            $gold = $gold*$odds;//peace
        }
        else {
            $gold = 0;
        }
        return $gold;
    }
    //输
    public function lose($gold,$playerCard,$bankerCard,$odds){
        $banker['p'] = 0;
        $banker['count'] =0;
        $player['p'] = 0;
        $player['count'] =0;

        //庄家牌组
        $bankerCard = 
        [ 
            $bankerCard[0]['size'],
            $bankerCard[1]['size'],
            $bankerCard[2]['size']
        ];

        //闲家牌组
        $playerCard = [ 
            $playerCard[0]['size'],
            $playerCard[1]['size'],
            $playerCard[2]['size']
        ];

        //统计庄家的 p：公数 count:小数
        for($i=0;$i<3;$i++){
            if($bankerCard[$i]>10){
                $banker['p']++;
            }
            else{
                $banker['count'] = $banker['count']+$bankerCard[$i];
            }
        }
        $banker['count'] = $banker['count']%10;
        //统计闲家的 p：公数 count:小数
        for($i=0;$i<3;$i++){
            if($playerCard[$i]>10){
                $player['p']++;
            }
            else{
                $player['count'] = $player['count']+$playerCard[$i];
            }
        }
        $player['count'] = $player['count']%10;
        if($banker['p']>$player['p']||($banker['p']==$player['p']&&$banker['count']>$player['count'])){
            $gold = $gold*$odds;//lose
        }
        else {
            $gold = 0;
        }
        return $gold;
    }
           

    //计算是同花顺
    public function straightFlush($gold,$card,$straight,$flush){

      //同花顺 3个牌子的花色一致
        $straight = self::straight($gold,$card,$straight);
        $flush = self::flush($gold,$card,$flush);

        if($straight&&$flush){
            $gold =  $straight+$flush;
        }
        else $gold = 0;
        return $gold;

    }

    //计算同花  flush  pair straight three
    public function flush($gold,$card,$odds){
        if(($card[0]['color'] == $card[1]['color']&&$card[1]['color'] == $card[2]['color'])){
            $gold =  $gold*$odds;
        }
        else $gold = 0;
        return $gold;

    }

    //计算对子
    public function pair($gold,$card,$odds){
        // 把数组的大小取出来
        $array = [
            '0'=>$card[0]['size'],
            '1'=>$card[1]['size'],
            '2'=>$card[2]['size']
        ]; 
        if($array[0]==$array[1]||$array[0]==$array[2]||$array[1]==$array[2]){
            $gold = $gold*$odds;
        } 
        else $gold = 0;
        return $gold;
    }

    //计算顺子
    public function straight($gold,$card,$odds){
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
            $gold =  $gold*$odds;
        }
        else $gold = 0;
        return $gold;
    }

    //计算3条
    public function three($gold,$card,$odds){
        // 把数组的大小取出来
        $array = [
            '0'=>$card[0]['size'],
            '1'=>$card[1]['size'],
            '2'=>$card[2]['size']
        ];
        if($array[0]==$array[1]&&$array[1]==$array[2]){
            $gold = $gold*$odds;
        }

        else $gold = 0;
        return $gold;
    }
}
