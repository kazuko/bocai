<?php 
namespace app\GameWorker\controller;

use think\Controller;
use Workerman\lib\Timer;
use think\facade\Request;

use app\Jsks\controller\Index as Jsks;//江苏快三
use app\Six\controller\Index as Six;//六合彩
use app\Bjpk10\controller\Index as Bjpk10;//六合彩

class Active extends Controller
{
        public $timer = array();//游戏的定时器
        public $Index = [];  //所有游戏的业务代码对象
        public $deskInfo = []; //牌桌信息
        public $interval = [];//游戏的时间间隔
        public $recentInfo = [] ; //最新游戏信息
        public $orderPrefix = [];//订单号前缀
        public $orderSuffix = 1000;//订单号后缀
        public $beginTime = []; //每局游戏的开始时间===========
       
        public $player=array();//===========
        public $gameStatus = [];//游戏开启状态===========
        public $connection = [];//玩家的链接数组 包括游戏类型与链接对象
        public $data = [];  //下注信息组
        public $order = []; //订单
        public $diceResult; //色子的分析结果
        public $issue = []; //订单信息
        public $gameHistory = [];
        public $odds = [];//赔率数组
        public function __construct(){
            $this->connection = [
                'jsks'=>[],
                'six'=>[],
                'bjpk10'=>[],
                // 'Zucai'=>[],
            ];//链接数组

            $this->Index = [
                'jsks' => new Jsks(),//江苏快三对象
                'six' => new Six(),//六合彩对象
                'bjpk10'=>new Bjpk10(),//北京pk10
                // 'Zucai'=>new Zucai(),
            ];//游戏的逻辑层对象

            //先采集一期数据
            $this->Index['jsks']->diceResult();
            $this->Index['six']->diceResult();
            $this->Index['bjpk10']->diceResult();

            $this->deskInfo = [
                'jsks' => $this->Index['jsks']->deskInfo(),
                'six'=>$this->Index['six']->deskInfo(),
                'bjpk10'=>$this->Index['bjpk10']->deskInfo(),
                // 'Zucai'=>$this->Index['Zucai']->deskInfo(),
            ];//游戏的牌桌信息

            $this->gameHistory = [
                'jsks' => $this->Index['jsks']->gameHistory(),
                'six' => $this->Index['six']->gameHistory(),
                'bjpk10'=>$this->Index['bjpk10']->gameHistory(),
            ];
            $this->timer = [
                'jsks'=>'jsks',
                'six'=>'six',
                'bjpk10'=>'bjpk10',
                // 'Zucai'=>'Zucai',
            ];//游戏的定时器

            $this->interval = [
                'jsks'=>300,
                'six'=>120,
                'bjpk10'=>300,
                // 'Zucai'=>20,
            ];

            $this->recentInfo = [
                'jsks'=>$this->Index['jsks']->recentInfo(),
                'six'=>$this->Index['six']->recentInfo(),
                'bjpk10'=>$this->Index['bjpk10']->recentInfo(),
                // 'Zucai'=>$this->Index['Zucai']->recentInfo(),
            ];

            $this->gameStatus = [
                'jsks'=>0,
                'six'=>0,
                'bjpk10'=>0,
                // 'Zucai'=>0,
            ];

            $this->beginTime = [
                "jsks"=>[
                "shijiancuo"=>time()+$this->interval['jsks'],
                "zifuchuan"=>date("Y:m:d H:i:s",time()+$this->interval['jsks'])
                ],
                "six"=>[
                    "shijiancuo"=>time()+$this->interval['six'],
                    "zifuchuan"=>date("Y:m:d H:i:s",time()+$this->interval['six'])
                        ],
                'bjpk10'=>[
                    "shijiancuo"=>time()+$this->interval['bjpk10'],
                    "zifuchuan"=>date("Y:m:d H:i:s",time()+$this->interval['bjpk10'])
                ],
            ];//游戏开始时间

            $this->data = [
                'jsks'=>[],
                'six'=>[],
                'bjpk10'=>[],
                // 'Zucai'=>[],
            ];
            $this->issue = [
                "jsks"=>$this->recentInfo['jsks'][0]['issue']+1,
                "six" =>$this->recentInfo['six'][0]['issue']+1,
                "bjpk10" =>$this->recentInfo['bjpk10'][0]['issue']+1,
            ];

            $this->orderPrefix = [ //订单号前缀
                "jsks" =>'001',//江苏快三
                "six"=>'005',//六合彩
                'bjpk10'=>'002',//北京pk10
                // "Zucai"=>'006',//足球彩
            ];
            $this->odds = [
                "jsks"=>json_decode(file_get_contents(dirname(dirname(dirname(__FILE__)))."/Jsks/odds.json"),true),
                "six"=>json_decode(file_get_contents(dirname(dirname(dirname(__FILE__)))."/Six/odds.json"),true),
                "bjpk10"=>json_decode(file_get_contents(dirname(dirname(dirname(__FILE__)))."/Bjpk10/odds.json"),true),
            ];
        }

        //业务：产生订单:'create' 撤销订单:'destroy' 请求历史记录+走势图 牌桌信息
        public function enter($connection,$data){
            //游戏类型
            // var_dump($data);
            $game = $data['game']; 
            echo "enter--game--".$data['game']."\n";
            echo "type-------".$data['type']."\n";
            switch($data['type']){
                //后台修改赔率桌面信息重新给全局变量赋值
                case 'changeDeskInfo':  
                    $this->deskInfo[$game] = $this->Index[$game]->deskInfo();
                break;

                case 'deskInfo'://牌桌信息
                //版本号不正确返回所有信息
                if($data['version'] != $this->deskInfo[$game]['version'] ){
                    $deskInfo = $this->deskInfo[$game];
                }
                else{
                    $deskInfo["version"] = $data['version'];
                }
                $result = [
                    "interval" => $this->interval[$game],//测试使用
                    'issue'=>$this->issue[$game],//游戏期号
                    'deskInfo'=>$deskInfo,//桌面信息
                    'recentInfo'=>$this->recentInfo[$game],//最近游戏记录
                    'countdown'=>$this->beginTime[$game],//返回时间戳
                ];
                //给玩家一个标志
                $this->connection[$game][$connection->id] = $connection;
                
                //返回房间信息
                $connection->send(json_encode($result));
                break;

                //订单
                case "create" :
                //游戏是否已经开启
                if($this->gameStatus[$game]){
                    //期号发生了变更
                    if($data['issue']!=$this->issue[$game]){
                        $result = [
                            "code"=>"期号发生了变更",
                            "issue"=>$this->issue[$game],
                        ];
                    }
                    else{
                        $result = [];
                        //检测消息是否传对了
                        if(!isset($data['allGold'])||!isset($data['id'])){
                            $result = '{"baga":"error message! please give me allGold and id"}';
                            $connection->send($result);
                            break;
                        }

                        $check = $this->Index[$game]->createCheck($data['allGold'],$data['id']);
                        //金币不足 
                        if(!$check['status']){
                            $result = [
                                "create" => false,
                                "remaining" => $check['remaining'],
                            ];
                        }
                        else{
                            foreach($data['order'] as $key=>$vo){
                                $this->orderSuffix++;
                                //先产生订单号：期号+前缀+后缀
                                $orderNumber = ($this->issue[$game]).$this->orderPrefix[$game].$this->orderSuffix;//生成订单号
                                // $orderNumber = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
                                // $this->Index[$game]->checkOrderNumber($orderNumber,$vo['id']);
    
                                $this->data[$game][$connection->id][$orderNumber] = $data['order'][$key];
                                $this->order[$orderNumber] = $this->Index[$game]->createOrder($data['order'][$key],$data['createTime'],$orderNumber,$this->odds[$game]);//生成订单
                                //把订单消息返回给前台
                            }
                            $result = [
                                "create"=> true,
                                "remaining" =>$check['remaining'],
                            ];
                        }
                    }
                }
                else{
                    //该游戏还没开启
                    $result = [
                        "code" => "游戏还没有开启"
                    ];
                }
                $connection->send(json_encode($result));
                break;

                //撤销订单
                case "destroy":
                try{
                    echo "---destroy---\n";
                    $result = $this->Index[$game]->destroyOrder($data['orderNumber']);
                    //同一次链接删除订单
                    if(isset($this->data[$game][$connection->id][$data['orderNumber']])){
                        unset($this->data[$game][$connection->id][$data['orderNumber']]);
                    }
                    //重新链接删除订单
                    foreach($this->data[$game] as $conId => $order){
                        foreach($order as $orderNumber => $order){
                            if($orderNumber == $data['orderNumber']){
                                unset($this->data[$game][$connection->id][$data['orderNumber']]);
                            }
                        }
                    }
                    if(isset($this->order[$data['orderNumber']])){
                        unset($this->order[$data['orderNumber']]);
                    }
                }catch( \Exception $e){
                    throw $e;
                    $result = [
                        "cancel"=>false,
                    ];
                    echo "destroy failed-----\n";
                }
                $connection->send(json_encode($result));
                break;
                /*
                *dimention-first:走势图type = 'sum||basic'||开奖记录type = 'dice'  
                *dimention-second:size 记录条数
                */
                
                /**
                 * $game:six 六合彩走势
                 * trend: 走势
                 * size:条数
                 */
                case "trend":
                    $result = $this->gameHistory[$game]['trend'][$data['size']];
                    $connection->send(json_encode($result));
                break;

                /**
                 * $game:six 六合彩生肖
                 * animal: 生肖
                 * size:条数
                 */
                case "animal":
                    $result = $this->gameHistory[$game]['animal'][$data['size']];
                    var_dump($result);
                    $connection->send(json_encode($result));
                break;

                /**
                 * $game:six 六合彩色波
                 * color: 色波
                 * size:条数
                 */                
                case "color":
                    $result = $this->gameHistory["six"]['color'][$data['size']];
                    $connection->send(json_encode($result));
                break;

                /**
                 * $game:jsks 江苏快三的和值走势图
                 * $type:sum 和值走势图
                 * size:30|50|100条数
                 */ 
                case "sum":
                    $result['sum'] = $this->gameHistory['jsks']['sum'][$data['size']];
                    $connection->send(json_encode($result));
                break;

                
                /**
                 * $game:jsks 江苏快三基本走势图
                 * basic: 基本走势图
                 * size:条数
                 */ 
                case "basic":
                    $result['basic'] = $this->gameHistory['jsks']['basic'][$data['size']];
                    $connection->send(json_encode($result));
                break;

                /**
                 * 近期开奖记录
                 * ajax 请求近期开奖
                 */
                case "recentOpen":
                    $result = $this->Index[$game]->recentOpen();
                    $connection->send(json_encode($result));
                break;

                case "betHistory":
                    $result = $this->Index[$game]->betHistory();
                    $connection->send(json_encode($result));
                break;

                /**
                 * 北京pk10基本走势
                 */
                case "basicPk10":
                    $result['basicPk10'] = $this->gameHistory['bjpk10']['basicPk10'][$data['size']];
                    $connection->send(json_encode($result));
                break;

                /**
                 * 北京pk10定胆位
                 * yeshu:第几页
                 */
                case "dingDanWei":
                try{
                    $result['dingDanWei'] = $this->gameHistory['bjpk10']['dingDanWei'][$data['size']][$data["yeshu"]];
                    $connection->send(json_encode($result));
                }
                catch(\Exception $e){
                    throw $e;
                }
                break;

                /**
                 * 北京pk10冠亚和
                 */
                case "guanYaHe":
                    $result['guanYaHe'] = $this->gameHistory['bjpk10']['guanYaHe'][$data['size']];
                    $connection->send(json_encode($result));
                break;
                //错误信息提示
                default:
                    $result = '{"baga":"error message"}';
                    $connection->send($result);
                break;
            }
        }

        //开启与关闭游戏
        public function gameStatus(){
            //一秒钟检测一次

            /**
             * 此处为非实时接口：慢了多长时间还未清楚
             * 时间对准
             * 测试时使用的随意开启游戏
             * 上线时使用确切的开启时间关闭时间
             */
            // $pk10OpenTime = strtotime($this->recentInfo['bjpk10'][0]['open_time'])+600;//以两期作为开启时间检测
            // $jsksOpenTime = strtotime($this->recentInfo['jsks'][0]['open_time'])+1200;//以两期作为开启时间检测
            // $sixOpenTime = strtotime($this->recentInfo['six'][0]['open_time'])+$this->interval['six'];//多出10分钟作为时间检测

            // //以开奖服务器为准
            // Timer::add(1,function()use($pk10OpenTime,$jsksOpenTime,$sixOpenTime){
            //     $i = 0;
            //     $time = time();
            //     //开启游戏
            //     echo $time."\n";
            //     echo $pk10OpenTime."\n";
            //     if($time == $pk10OpenTime){
            //         self::gameBegin('bjpk10');
            //     }
            //     if($time == $jsksOpenTime){
            //         self::gameBegin('jsks');
            //     }
            //     if($time == $sixOpenTime){
            //         self::gameBegin('six');
            //     }
                //关闭
                // if($time == $pk10CloseTime){
                //     self::gameOver();
                // }
            // });

            /**
             * 不对准时间
             */
            self::gameBegin('bjpk10');
            self::gameBegin('jsks');
            self::gameBegin('six');

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
                    $this->beginTime[$game]["shijiancuo"] = time()+$this->interval[$game];
                    $this->beginTime[$game]["zifuchuan"] = date("Y:m:d H:i:s",$this->beginTime[$game]["shijiancuo"]);
                    echo $game."begin--------------------------------------\n";
                    //把色子的分析结果求出来
                    $this->diceResult = $this->Index[$game]->diceResult();
                    echo $game." diceResult\n";

                    //更新走势图，开奖结果，最新期数
                    $this->gameHistory[$game] = $this->Index[$game]->gameHistory();
                    echo $game." gameHistory\n";
                    $this->recentInfo[$game] = $this->Index[$game]->recentInfo();
                    echo $game." recentInfo\n";
                    $this->issue[$game] =  $this->recentInfo[$game][0]['issue']+1;
                    echo $game." issue\n";
                    //结算游戏
                    if(count($this->data[$game]) > 0){
                        //获取游戏结果
                        echo $game." jiesuan------\n";
                        $result[$game] = $this->Index[$game]->begin($this->data[$game],$this->diceResult);
                        echo $game." jiesuan success------\n";
                        file_put_contents('./player.json', "result------\n".json_encode($result)."\n",FILE_APPEND);
                    }
                    
                    //向前端返回游戏结果
                    foreach($this->connection[$game] as $conId => $connection){
                        echo $game." return result ------\n";
                        //该游戏有玩家下注分下注与非下注返回
                        if(count($this->data[$game]) > 0){
                            echo $game." has player play\n";
                            if(isset($this->data[$game][$conId])){
                                //押注玩家返回结算数据+最新游戏结果
                                echo "return player\n";
                                $result[$game][$conId]['recentInfo'] = $this->recentInfo[$game];
                                $result[$game][$conId]['issue'] = $this->issue[$game];
                                $connection->send(json_encode($result[$game][$conId]));
                            }
                            //非押注玩家返回最新的游戏结果
                            else{
                                $Nresult = [
                                    "recentInfo"=>$this->recentInfo[$game],
                                    "issue" => $this->issue[$game],
                                ];
                                $connection->send(json_encode($Nresult));
                            }
                        }
                        // 没有玩家押注直接返回最新的游戏结果
                        else{
                            $Nresult = [
                                "recentInfo"=>$this->recentInfo[$game],
                                "issue" => $this->issue[$game],
                            ];
                            "no player playReturn $game";
                            $connection->send(json_encode($Nresult));
                        }
                        echo $game." return result success------\n";
                    }

                    //假如牌桌赔率变化频繁，此处应该更新牌桌信息
                    // $this->deskInfo = $this->Index->deskInfo();

                    //删除用户的押注表单信息 与玩家押注数据
                    $this->data[$game] = [];
                    $this->order[$game] = [];
            });
        }

        public function gameOver($game){
            $this->gameStatus[$game] = 0;
            Timer::del($this->timer[$game]);
        }

}
 ?>