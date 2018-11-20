<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace app\GameWorker\controller;
use think\Db;
use app\Sangong\controller\Index;
use workerman\lib\Timer;

/**
 * Worker 命令行服务类
 */
class SanGong 
{
        public $player=array();//用户的信息
        protected $time = 10;//心跳时间间隔
        /*
           $beginTime : 发牌时间     
        */
        protected $beginTime = 0;//开局时间
        protected $connection = [];

        /**
         * $dealTime = $qiangZhuangTime + $dealDown
         * $qiangZhuangTime = $qiangZhuangDown +$account
         * 
         */
        protected $dealTime = 30;//每一局时间长度
        protected $qiangZhuangDown = 8;
        protected $qiangZhuangTime = 16;//决定庄家 
        protected $account = 8;//结算时间
        protected $dealDown = 14; //押注时间

        protected $Abnormal;

        protected $lastInfo = "" ; //最新一局游戏的信息

        protected $online = ['0'=>0,'1'=>0,'2'=>0,'3'=>0];//在线人数

        protected $qiangZhuang = [];//抢庄

        protected $issue = ['0'=>0,'1'=>0,'2'=>0,'3'=>0];//期数

        protected $goldPool = [
            '0'=>[
                "player1" => [
                    "toDeal" => 0,
                    "sanGong" => 0,
                    "win" => 0,
                    "peace" => 0,
                    "lose" => 0,
                ],
                "player2" => [
                    "toDeal" => 0,
                    "sanGong" => 0,
                    "win" => 0,
                    "peace" => 0,
                    "lose" => 0,
    
                ],
                "player3" => [
                    "toDeal" => 0,
                    "sanGong" => 0,
                    "win" => 0,
                    "peace" => 0,
                    "lose" => 0,
                ]
            ],
            '1'=>[
                "player1" => [
                    "toDeal" => 0,
                    "sanGong" => 0,
                    "win" => 0,
                    "peace" => 0,
                    "lose" => 0,
                ],
                "player2" => [
                    "toDeal" => 0,
                    "sanGong" => 0,
                    "win" => 0,
                    "peace" => 0,
                    "lose" => 0,
    
                ],
                "player3" => [
                    "toDeal" => 0,
                    "sanGong" => 0,
                    "win" => 0,
                    "peace" => 0,
                    "lose" => 0,
                ]
            ],
            '2'=>[
                "player1" => [
                    "toDeal" => 0,
                    "sanGong" => 0,
                    "win" => 0,
                    "peace" => 0,
                    "lose" => 0,
                ],
                "player2" => [
                    "toDeal" => 0,
                    "sanGong" => 0,
                    "win" => 0,
                    "peace" => 0,
                    "lose" => 0,
    
                ],
                "player3" => [
                    "toDeal" => 0,
                    "sanGong" => 0,
                    "win" => 0,
                    "peace" => 0,
                    "lose" => 0,
                ]
            ],
            '3'=>[
                "player1" => [
                    "toDeal" => 0,
                    "sanGong" => 0,
                    "win" => 0,
                    "peace" => 0,
                    "lose" => 0,
                ],
                "player2" => [
                    "toDeal" => 0,
                    "sanGong" => 0,
                    "win" => 0,
                    "peace" => 0,
                    "lose" => 0,
    
                ],
                "player3" => [
                    "toDeal" => 0,
                    "sanGong" => 0,
                    "win" => 0,
                    "peace" => 0,
                    "lose" => 0,
                ]
            ],

        ];//金币池
    /*
     * onMessage 事件回调
     * @access public
     * @param  \Workerman\Connection\TcpConnection    $connection
     * @param  mixed                                  $data
     * @return vpopmail_del_domain(domain)
     */

     public function __construct(){
         
     } 
    public function enter($connection, $data)
    {
        /**
        * 三公的socket接口
        * 一 ：enter()入口  返回结果 0:没有资格进入房间 否则:牌桌详情
        * 二 ：play() 玩家下注 返回由Index.php处理函数的结果
        * 三 ：
        */
        var_dump($data);
        echo "-----array.data-----\n";
        echo "\n";
        switch($data['type']){
            //加入房间 code2
            case "enterRoom":
            $result['roomInfo'] = $this->Index->enterRoom();
            
            //分别为房间加上房间人数 online_setting = online_real * 10; 
            //设置的人数是真实人数的十倍
            foreach($result['roomInfo'] as $key => $vo){
                $result['roomInfo'][$key]['online_real'] = $this->online[$key];
                $result['roomInfo'][$key]['online_setting'] = $this->online[$key]*10;
            }
            $result['code'] = 2;
            $this->connection[$connection->id] = $connection;
            break;

            //牌桌初始化工作 
            case "enterDesk":
            $this->online[$data['deskId']]++;//牌桌人数
            /**
             * 允许旁观
             * deskId房间Id $connection->id 用户链接id 'conn'链接对象
             */
            $this->player['enterDesk'][$data['deskId']][$connection->id]['conn'] = $connection;//金币足够的玩家的id 

            echo $connection->id."  clickDesk ".$data['deskId'];
            $lastInfo = $this->lastInfo[$data['deskId']];
            file_put_contents("./player.json", json_encode($lastInfo));
            
            $result = $this->Index->enterDesk($data); //code:0金币或者积分不足  

            if($result['code']){//金币或者积分足够
                // accountDown大于零用于提醒新加入用户正在结算
                $result['accountDown'] = $this->account - (time() - $this->beginTime);
                
                //结算期间返回正在结算的信息($lastInfo)给用户否则返回0
                $result["account"] = $result['accountDown'] <=0 ? 0 : $lastInfo;
                
                //抢庄倒计时 = 结算倒计时+抢庄时长
                $result['qiangZhuangDown'] =  $this->qiangZhuangDown + $result['accountDown']; 
                
                //抢庄结束了返回新的庄家信息 
                $result['banker'] = $result['qiangZhuangDown']<=0 ? $banker[$deskId] : '';
                //押注时间 = 押注时间+抢庄倒计时
                $result["countDown"] = $this->dealDown + $result['qiangZhuangDown'];
                
                //进入了牌桌才有发牌信息
                $this->player['enterDesk'][$data['deskId']][$connection->id]['id'] = $data['id'];//金币足够的玩家的id 
            }

            break;

            case "play":
            echo "-----player-----".$connection->id."-----has-----played-----\n";
            //把下注记录进金币池
            if($data['issue'] == $this->issue[$data['deskId']]){
                $this->goldPool[$data['deskId']][$data['bet']['player']][$data['bet']['status']] += $data['gold'];

                $this->player['data'][$data['deskId']][$connection->id] = $data;
                $result = [
                    "code"=>6 //返回一个押注成功的信号
                ];
            }
            else{
                $result = [
                    "code"=>66,
                    "issue"=>$this->issue[$deskId]
                ];
            }
            // echo "Client->".$connection->id." play the Game in room: ".$data['room'];
            break;

            //玩家开挂
            case "power":
                unset($data['type']);
                // //把开挂信息存进数据库
                $data = [
                    "power_id"=>$data['id'],
                    "power"=>$data['power'],
                    "time"=>time(),
                    "issue"=>$data['issue'],
                    "desk_id"=>$data['deskId'],
                ];
                $status = Db::name("games_sg_power")->insert($data);
                //10表示本次抢庄有效 11表示无效
                if($status){
                    $result = [
                        "code"=>10,
                    ];
                }
                else{
                    $result = [
                        "code"=>11,
                    ];
                }
            break;
            case "leaveDesk":
            //正常离开牌桌,卸载相关的链接数据并且返回8表示离开成功同时牌桌人数-1
            unset($this->player['enterDesk'][$data['deskId']][$connection->id]);
            $result = [
                "code"=>8
            ];
            $this->online[$data['deskId']]--;
            break; 
    
            //掉线   这段代码逻辑有问题
            case "loseConnection":
            foreach($this->player['enterDesk'] as $deskId => $conn){
                foreach($conn as $conId => $data){
                    if($conId == $connection->id){
                        $result = [
                            "code"=>8
                        ];
                        $this->online[$data['deskId']]--;
                        unset($this->player['enterDeak'][$deskId][$connection->id]);
                    }
                }
            }
            break;


            case "qiangzhuang"://抢庄
            $this->qiangZhuang[$data['deskId']][] = $data['id'];//记录抢庄用户的id
            break;
            default:
            $result = '{"baga":"are you kidding me"}';
            break;
        }
        $connection->send(json_encode($result));
    }

//worker服务器启动时触发的函数
 public function onWorkerStart(){
        $this->qiangZhuang = [
            '0'=>[],
            '1'=>[],
            '2'=>[],
            '3'=>[],
        ];//加入房间并且拥有足够金币或者积分的玩家
        $this->player['data'] = [
            '0'=>[],
            '1'=>[],
            '2'=>[],
            '3'=>[],
        ]; //用户的结算数组
        $this->player['enterDesk'] = [
            '0'=>[],
            '1'=>[],
            '2'=>[],
            '3'=>[],
        ];    //牌桌的用户信息
        $this->beginTime = time();  //每一局游戏的发牌时间
        $this->Index = new Index(); //逻辑层函数
    /**
    * 控制牌局时间
    * 游戏从服务器启动就一直发牌，发回去的倒计时+结算时间+1s=总的发牌间隔
    */
    $sanGong_deal_interval = Timer::add($this->dealTime,function(){

        foreach($this->qiangZhuang as $deskId => $idArray){
            $qiangZhuang[$deskId] = Timer::add($this->qiangZhuangTime,function()use($deskId){
                //没人抢庄,金币最高的为庄家
                $player[] = [];//用于保存用户id及金币

                //没有人抢庄从有资格押注的玩家里选最高金币的玩家坐庄
                if( count( $this->qiangZhuang ) == 0 ){
                    foreach($this->player['enterDesk'][$deskId] as $conId => $data){
                        if(isset($data['id'])){
                            //分别查出每个玩家的金币
                            $player[$data['id']] = Db::name("user_user")->where(['id'=>$playerId])->value('gold');
                        }
                    }
                }
                //有人抢庄 参与抢庄金币最高的坐庄
                else{
                    foreach($this->qiangZhuang[$deskId] as $playerId){
                        //分别查出每个玩家的金币
                        $player[$playerId] = Db::name("user_user")->where(['id'=>$playerId])->value('gold');
                    }
                }
                $banker['banker_id'] = \array_search(max($player),$player);
                $banker['time'] = time();
                $banker['desk_id'] = $deskId;
                $banker['issue'] = $this->issue;
                
                //保存新产生的庄家到全局变量用于新加进用户查看
                $this->banker[$deskId] = Db::name("user_user")->field("head,gold,nickname")->
                                            where(['id'=>$banker['banker_id']])
                                            ->find();
                
                Db::name('games_sg_banker')->insert($banker);//把庄家信息插入数据库
                
                //把庄家的信息传回前端
                foreach($this->player['enterDesk'][$deskId] as $conId=>$vo){
                    $vo['connection']->send($banker);
                }
            },array(),false);//抢庄
    
        }

            //重置开局时间
            $this->beginTime = time();

            $sangong = new Index();
            foreach($this->player['data'] as $deskId => $data){
            //期数+1作为下一期期数
                $this->issue[$deskId]++;
                //没有玩家押注直接给所有玩家发牌
             if(count($data)<=0){
                $deal[$deskId] = $sangong->returnDeal();//没有人下注的时候随便发牌啦
                       
                $this->lastInfo[$deskId]['deal'] = $deal[$deskId];
                
                $this->lastInfo[$deskId]['issue'] = $this->issue[$deskId];
                
                if(count($this->player['enterDesk'][$deskId]) > 0){
                    foreach($this->player['enterDesk'][$deskId] as $conId => $vo){
                        $vo['connection']->send($this->lastInfo[$deskId]);
                    }
                }
             }
             // 有玩家押注
             else{
                 //'data'玩家的数据   $deskId牌桌号  issue期号：看该期有没有平台玩家  $goldPool:金币池
                     $result = $sangong->begin($this->player['data'][$deskId],$deskId,$this->issue[$deskId],$this->goldPool[$deskId]);//押注玩家组
                     file_put_contents("./player.json", json_encode($result,true));
                     
                     $this->lastInfo[$deskId]['deal'] = $result['deal']; //围观玩家
                     $this->lastInfo[$deskId]['issue'] = $this->issue[$deskId];

                     //把结果返回给还在线的玩家
                     echo "-----have player play return-----\n";
                     foreach($this->player['connection'] as $k => $con){
                        //没有参与游戏的玩家
                        if(!isset($this->player['data'][$deskId][$k])){
                            echo "-----player no bet*return-----\n";
                            // echo $k;
                            self::timeSend($con,$this->lastInfo[$deskId]);
                        }
                        else{
                        //参与游戏的玩家
                             if($this->player['data'][$deskId][$k]){
                                 echo "-----player bet*return-----\n";
                                 $result['data'][$k]['issue'] = $this->issue[$deskId];
                                 $data = $result['data'][$k];
                                 self::timeSend($con,$data);
                                 unset($this->player['data'][$deskId][$k]);
                                 echo "hadn't send player data on desk ".$deskId."---->".count($this->player['data'][$deskId])."\n";
                             }
                        }
                     } 
                }
                //结算完毕 
                //  卸掉牌组以及押注的玩家信息,
                $this->player['data'][$deskId] = [];

                //卸掉抢庄玩家的信息
                $this->qiangZhuang[$deskId] = []; 
                
                //重新把金币池置零
                $this->goldPool[$deskId] = [
                        "player1" => [
                            "toDeal" => 0,
                            "sanGong" => 0,
                            "win" => 0,
                            "peace" => 0,
                            "lose" => 0,
                        ],
                        "player2" => [
                            "toDeal" => 0,
                            "sanGong" => 0,
                            "win" => 0,
                            "peace" => 0,
                            "lose" => 0,
            
                        ],
                        "player3" => [
                            "toDeal" => 0,
                            "sanGong" => 0,
                            "win" => 0,
                            "peace" => 0,
                            "lose" => 0,
                        ],
                ];//金币池
        
            }
    });
 }    // Timer::del($sanGong_deal_interval);


//给所有发送回去的信息添加倒计时
public function timeSend($con,$deal){
    // echo $deal;
    $data = [];

    $deal['countDown'] = $this->dealDown;
    //一次返回所有信息
    $deal = json_encode($deal);
    $con->send($deal);
    
    $deal=json_decode($deal,true);
    $i = 4;



    foreach($deal['deal'] as $player=>$card){
        $i--;
        $data[$i]['code'] = 3;
        $data[$i]['player'] = $player;
        $data[$i]['card'] = $card;
        // echo "connectionid : ".$con->id."-card : ".$i."\n";
        // $send = json_encode($data);
        // $con->send($send);
    }


    // //每一秒发一次牌
    // Timer::add(1,function()use($con,$data,$deal){
    //     $send = json_encode($data[3]);
    //     $con->send($send);

    //     Timer::add(1,function()use($con,$data,$deal){
    //         $send = json_encode($data[2]);
    //         $con->send($send);

    //         Timer::add(1,function()use($con,$data,$deal){
    //             $send = json_encode($data[1]);
    //             $con->send($send);

    //             Timer::add(1,function()use($con,$data,$deal){
    //                 $send = json_encode($data[0]);
    //                 $con->send($send);
    //                 /*
    //                     开局信号
    //                 */
    //                 Timer::add(1,function()use($con,$data,$deal){
    //                     $userInfo['code'] = 5;
    //                     $userInfo['countDown'] = $this->beginTime + $this->dealTime - time()-1;
    //                     //非押注玩家
    //                     if($deal['code'] == 4){
    //                         $userInfo['result'] = 0;
    //                     }
    //                     else{
    //                         //押注玩家
    //                         $userInfo['result'] = $deal['result'];
    //                     }
    //                     $send = json_encode($userInfo);
    //                     $con->send($send);
    //                 },array(),false);
    //             },array(),false);
    //         },array(),false);
    //     },array(),false);
    // },array(),false);
}
}


