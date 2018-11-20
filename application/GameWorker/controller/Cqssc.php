<?php 
namespace app\GameWorker\controller;

use think\Controller;
use Workerman\lib\Timer;
use think\facade\Request;

use app\Jsks\controller\Index as Jsks;
use app\GD\controller\Index as GD;
use app\Six\controller\Index as Six;

class Cqssc extends Controller
{
        public $timer = array();//游戏的定时器
        public $Index = [];  //所有游戏的业务代码对象
        public $deskInfo = []; //牌桌信息
        public $interval = [];//游戏的时间间隔
        public $recentInfo = [] ; //最新游戏信息
        public $gameHistory = [];//走势图以及开奖记录
        public $orderPrefix = [];//订单号前缀
        public $orderSuffix = 0;//订单号后缀
        public $beginTime = []; //每局游戏的开始时间===========
       

        public $player=array();//===========
        public $gameStatus = [];//游戏开启状态===========
        public $connection = [];//玩家的链接数组 包括游戏类型与链接对象
        public $data = [];//下注信息组
        public $issue = [];//订单
        public $diceResult;//色子的分析结果
        public function __construct(){
            $this->Index = [
                'Jsks' => new Jsks(),
                // 'Six' => new Six(),
                // 'Zucai'=>new Zucai(),
            ];//游戏的逻辑层对象

            $this->deskInfo = [
                'Jsks' => $this->Index['Jsks']->deskInfo(),
                // 'Six'=>$this->Index['Six']->deskInfo(),
                // 'Zucai'=>$this->Index['Zucai']->deskInfo(),
            ];//游戏的牌桌信息

            $this->timer = [
                'Jsks'=>'Jsks',
                // 'Six'=>'Six',
                // 'Zucai'=>'Zucai',
            ];//游戏的定时器

            $this->interval = [
                'Jsks'=>10,
                // 'Six'=>15,
                // 'Zucai'=>20,
            ];

            $this->recentInfo = [
                'Jsks'=>$this->Index['Jsks']->recentInfo(),
                // 'Six'=>$this->Index['Six']->recentInfo(),
                // 'Zucai'=>$this->Index['Zucai']->recentInfo(),
            ];

            $this->gameHistory = [
                'Jsks'=>$this->Index['Jsks']->gameHistory(),
                // 'Six'=>$this->Index['Six']->gameHistory(),
                // 'Zucai'=>$this->Index['Zucai']->gameHistory(),
            ];
            $this->gameStatus = [
                'Jsks'=>0,
                // 'Six'=>0,
                // 'Zucai'=>0,
            ];

            $this->data = [
                'Jsks'=>[],
                // 'Six'=>[],
                // 'Zucai'=>[],
            ];
            $this->orderSuffix = 1000;
            $this->orderPrefix = [ //订单号前缀
                "Jsks" =>'001',//江苏快三
                // "Six"=>'005',//六合彩
                // "Zucai"=>'006',//足球彩
            ];
        }

        //业务：产生订单 撤销订单 请求历史记录+走势图 牌桌信息
        public function enter($connection,$data){
            //游戏类型
            // var_dump($data);
            $game = $data['game'];
            echo "game------";
            switch($data['type']){
                case 'deskInfo':
                $result = [
                    'deskInfo'=>$this->deskInfo[$game],
                    'recentInfo'=>$this->recentInfo[$game],
                    'gameHistory'=>$this->gameHistory[$game]
                ]; 

                //给玩家一个标志
                $this->connection[$connection->id]['game'] = $game;
                //返回房间信息
                $connection->send(json_encode($result));
                break;
                //订单
                case "create" :

                 //给玩家一个标志
                $this->connection[$connection->id]['game'] = $game;
                //游戏是否已经开启
                if($this->gameStatus[$game]){
                    //期号发生了变更
                    if($data['order']['issue']!=($this->recentInfo[$game][0]['issue']+1)){
                        $result = [
                            "code"=>"0",
                            "issue"=>$this->recentInfo[$game][0]['issue']+1
                        ];
                    }
                    else{
                        $orderNumber = ($this->recentInfo[$game][0]['issue']+1).$this->orderPrefix[$game].$this->orderSuffix;//生成订单号
                        $this->orderSuffix++;
                        $this->data[$game][$connection->id][$orderNumber] = $data['order'];
                        $this->issue[$orderNumber] = $this->Index[$game]->createOrder($data['order'],$orderNumber);//生成订单
                        $result = $this->issue[$orderNumber];
                    }
                }
                else{
                    //该游戏还没开启
                    $result = [
                        "code" => "999999"
                    ];
                }
                $connection->send(json_encode($result));
                break;

                //撤销订单
                case "destroy":
                $this->Index->destroyOrder($data['orderNumber']);
                unset($this->data[$game][$connection->id][$data['orderNumber']]);
                unset($this->issue[$data['orderNumber']]);
                break;
                /*
                *dimention-first:走势图type = 'sum||basic'||开奖记录type = 'dice'  
                *dimention-second:size 记录条数
                */
                case "history" :
                //例子 {"game":"Jsks","class":"sum","size":30} 江苏快三 和值 前面30条数据
                $connection->send(json_encode(($this->gameHistory[$game][$data['class']]['size'])));
                default:
                $result = '{"baga":"are you kidding me"}';
                break;
            }
        }

        //开启与关闭游戏
        public function gameStatus(){
            //一秒钟检测一次
            Timer::add(1,function(){
                //以开奖服务器为准
                //相关游戏开始开启游戏
                // self::gameBegin('Jsks');
                //相关游戏结束关闭游戏
                // self::gameOver('Jsks');
            });
            self::gameBegin('Jsks');
            // self::gameBegin('Six');
            // self::gameBegin('Zucai');
        }

        //开启游戏
        public function gameBegin($game){
            //开启标志
            $this->gameStatus[$game] = 1;
            $this->timer[$game] = Timer::add($this->interval[$game], function()use($game)
            { 
                    $i = 0;
                    //更新开局时间，用于新加入用户倒计时计算
                    $this->beginTime[$game] = time();
                    //把色子的分析结果求出来
                    $this->diceResult = $this->Index[$game]->diceResult();
                    echo " ---".$i++."--- ";
                    //分类结算游戏
                    foreach($this->data as $type=>$gameData){
                        echo " ---".$i++."--- ";
                        if(count($gameData) > 0){
                            //获取游戏结果
                            var_dump($gameData);
                            $result[$type] = $this->Index[$type]->begin($gameData,$this->diceResult);

                            file_put_contents('./player.json', "result------\n".json_encode($result)."\n",FILE_APPEND);
                        }

                    }
                    //向前端返回游戏结果
                    foreach($this->connection as $conId => $conInfo){
                        $game = $conInfo['game'];
                        $connection = $conInfo['connection'];
                        //该游戏有玩家下注分下注与非下注返回
                        if(count($this->data[$game]) > 0){

                            if(isset($this->data[$game][$conId])){
                                //押注玩家返回结算数据+最新游戏结果
                                $result[$game][$conId]['recentInfo'] = $this->recentInfo[$game];
                                $connection->send(json_encode($result[$game][$conId]));
                            }
                            //非押注玩家返回最新的游戏结果
                            else{
                                $connection->send(json_encode($this->recentInfo[$game]));
                            }

                        }
                        // 没有玩家押注直接返回最新的游戏结果
                        else{
                            $connection->send(json_encode($this->recentInfo[$game]));
                        }
                    }

                    echo " ---".$i++."--- ";
                    //更新走势图，开奖结果，
                    $this->gameHistory[$game] = $this->Index[$game]->gameHistory();
                    echo " ---".$i++."--- ";
                    // file_put_contents('./player.json', "gameHistory------\n".json_encode($this->gameHistory)."\n",FILE_APPEND);
                    //更新最近游戏结果

                    $this->recentInfo[$game] = $this->Index[$game]->recentInfo();
                    echo " ---".$i++."--- ";
                    // file_put_contents('./player.json', "recentInfo------\n".json_encode($this->recentInfo)."\n",FILE_APPEND);
                    //假如牌桌赔率变化频繁，此处应该更新牌桌信息
                    // $this->deskInfo = $this->Index->deskInfo();

                    //删除用户的押注表单信息 与玩家押注数据
                    $this->data[$game] = [];
                    $this->issue[$game] = [];
            });
        }

        public function gameOver($game){
            $this->gameStatus[$game] = 0;
            Timer::del($this->timer[$game]);
        }

}
 ?>