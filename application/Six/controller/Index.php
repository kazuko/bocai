<?php
namespace app\Six\controller;
use think\Controller;
use think\facade\Session;
use think\Db;
use think\facade\Request;
use app\Six\controller\Active; //功能函数
use app\MyCommon\controller\Gameapi;//获取游戏开奖号码接口
/*
    ** 最近游戏记录  recentInfo();
    ** 生成订单      createOrder();?
    ** 取消订单      destroyOrder();?
    ** 获取历史记录  gameHistory();?
    ** 生肖的分析表  animalAnalyse();
    ** 色波的分析表  colorAnalyse();
    ** 桌面信息      deskInfo();
    ** 订单历史      betHistory();
    ** 近期开奖      recentOpen()
    ** 创建订单时检测是否有足够的金币 createCheck($allGold, $id);
*/

class Index extends Controller
{
    protected $function;
    protected $dice; //开奖详细
    protected $rule;
    protected $max;
    protected $active;
    public function __construct()
    {
        parent::__construct();
        $this->max = 0;
        $this->active = new Active();
        $this->rule = [
            //波色；
                'hb'=>[1,2,7,8,12,13,18,19,23,24,29,30,34,35,40,45,46],
                'lb'=>[3,4,9,10,14,15,20,25,26,31,36,37,41,42,47,48],
                'lvb'=>[5,6,11,16,17,21,22,27,28,32,33,38,39,43,44,49],
            //生肖----------
            /* 生肖生成规则
            *   鼠、牛、虎、兔、龙、蛇、马、羊、猴、鸡、狗、猪
            *   以生肖年作为 1 开始依次+12
            */
                'shu'=>[11,23,35,47],
                'niu'=>[10,22,34,46],
                'hu'=>[9,21,33,45],
                'shXtu'=>[8,20,32,44],
                'long'=>[7,19,31,43],
                'she'=>[6,18,30,42],
                'ma'=>[5,17,29,41],
                'yang'=>[4,16,28,40],
                'hou'=>[3,15,27,39],
                'ji'=>[2,14,26,38],
                'gou'=>[1,13,25,37,49],
                'zhu'=>[12,24,36,48],
            //生肖合集
                'yeshou'=>[11,23,35,47,9,21,33,45,7,19,31,43,6,18,30,42,3,15,27,39],
                'jiaqin'=>[10,22,34,46,5,17,29,41,4,16,28,40,2,14,26,38,1,13,25,37,49,12,24,36,48],
                'dan'=>[11,23,35,47,9,21,33,45,7,19,31,43,5,17,29,41,3,15,27,39,1,13,25,37,49],
                'shuang'=>[10,22,34,46,8,20,32,44,6,18,30,42,4,16,28,40,2,14,26,38,12,24,36,48],
                'qianqiao'=>[11,23,35,47,10,22,34,46,9,21,33,45,8,20,32,44,7,19,31,43,6,18,30,42],
                'houqiao'=>[5,17,29,41,4,16,28,40,3,15,27,39,2,14,26,38,1,13,25,37,49,12,24,36,48],
                'tianqiao'=>[10,22,34,46,8,20,32,44,7,19,31,43,5,17,29,41,3,15,27,39,12,24,36,48],
                'diqiao'=>[11,23,35,47,9,21,33,45,6,18,30,42,4,16,28,40,2,14,26,38,1,13,25,37,49],
            //五行
                'jin'=> [4,5,18,19,26,27,34,35,48,49],
                'mu'=>  [1,8,9,16,17,30,31,38,39,46,47],
                'shui'=>[6,7,14,15,22,23,36,37,44,45],
                'huo'=> [2,3,10,11,24,25,32,33,40,41],
                'wxtu'=>  [12,13,20,21,28,29,42,43],
        ];
    }
/** 测试数据*/
public function caiji(){
        //采集最近15期的结果
        $data19 = [
            [
                'opencode' =>  '20,25,27,29,06,13,21', 
                'expect' =>  '2018121' ,
                'opentime' =>  '2018-10-25 21:34:48' ,
            ],
            [
                'opencode' =>  '36,44,17,20,34,06,15' ,
                'expect' =>  '2018120' ,
                'opentime' =>  '2018-10-23 21:34:40' ,
            ],
            [
                'opencode' =>  '22,42,13,25,10,40,05' ,
                'expect' =>  '2018119' ,
                'opentime' =>  '2018-10-20 21:34:51' ,
            ],
            [
                'opencode' =>  '45,07,11,19,46,32,37' ,
                'expect' =>  '2018118' ,
                'opentime' =>  '2018-10-16 21:35:05' ,
            ],
            [
                'opencode' =>  '43,24,08,34,21,14,01' ,
                'expect' =>  '2018117' ,
                'opentime' =>  '2018-10-14 21:35:00' ,
            ],
            [
                'opencode' =>  '42,19,29,07,39,09,41' ,
                'expect' =>  '2018116' ,
                'opentime' =>  '2018-10-11 21:35:00' ,

            ],
            [
                'opencode' =>  '45,18,44,07,27,09,15' ,
                'expect' =>  '2018115' ,
                'opentime' =>  '2018-10-08 21:35:00' ,

            ],
            [
                'opencode' =>  '49,35,39,44,10,02,08' ,
                'expect' =>  '2018114' ,
                'opentime' =>  '2018-10-05 21:35:00' ,

            ],
            [
                'opencode' =>  '34,35,45,19,13,46,27' ,
                'expect' =>  '2018113' ,
                'opentime' =>  '2018-10-02 21:35:00' ,

            ],
            [
                'opencode' =>  '13,49,28,18,23,33,19' ,
                'expect' =>  '2018112' ,
                'opentime' =>  '2018-09-29 21:35:00' ,

            ],
            [
                'opencode' =>  '38,31,37,34,35,09,16' ,
                'expect' =>  '2018111' ,
                'opentime' =>  '2018-09-26 21:35:00' ,

            ],
            [
                'opencode' =>  '48,49,17,14,30,46,36' ,
                'expect' =>  '2018110' ,
                'opentime' =>  '2018-09-23 21:35:00' ,

            ],
            [
                'opencode' =>  '14,20,22,24,05,48,04' ,
                'expect' =>  '2018109' ,
                'opentime' =>  '2018-09-20 21:35:00' ,

            ],
            [
                'opencode' =>  '40,32,21,02,23,13,30' ,
                'expect' =>  '2018108' ,
                'opentime' =>  '2018-09-17 21:35:00' ,

            ],
            [
                'opencode' =>  '17,06,40,26,41,05,46' ,
                'expect' =>  '2018107' ,
                'opentime' =>  '2018-09-14 21:35:00' ,

            ],
            [
                'opencode' =>  '11,18,32,23,48,09,26' ,
                'expect' =>  '2018106' ,
                'opentime' =>  '2018-09-11 21:35:00' ,

            ],
            [
                'opencode' =>  '06,04,48,11,37,33,41' ,
                'expect' =>  '2018105' ,
                'opentime' =>  '2018-09-08 21:35:00' ,

            ],
            [
                'opencode' =>  '14,20,41,39,22,48,35' ,
                'expect' =>  '2018104' ,
                'opentime' =>  '2018-09-05 21:35:00' ,

            ],
            [
                'opencode' =>  '38,45,47,31,49,36,37' ,
                'expect' =>  '2018103' ,
                'opentime' =>  '2018-09-02 21:35:00' ,

            ],
            [
                'opencode' =>  '41,46,07,09,12,18,33' ,
                'expect' =>  '2018102' ,
                'opentime' =>  '2018-08-29 21:35:00' ,

            ],
        ];
        for($i=18;$i>=0;$i--){
            $data = $data19[$i];
            $dice = explode(",", $data['opencode']);
            if($dice[6]==49){//特码为49两面盘会出现和值
                $this->max = 1;
            }
            
    
            $gameResult = $this->active->analyseCode($dice,$this->rule);
            //记录基本走势 table:games_six_trend
            $this->active->recordeBasicTrend($data,$this->rule);
            //记录色波 table:games_six_color;
            $this->active->recordeColor($data,$this->rule);
            //记录生肖 table:games_six_animal;
            $this->active->recordsAnimal($data,$this->rule);
            //记录开奖结果 table:games_jsks_dice_history
            $this->active->recordeDiceHistory($data,$this->rule);

        }
                //随机100期测试

        // $time = time();
        // $expect = 2018118;

        // for($i = 0; $i<=100;$i++){
        //     $opencode = [];
        //     $time--;
        //     do{
        //         $code = rand(0,49);
        //         if(!in_array($code,$opencode)){
        //             $opencode[] = $code;
        //             if(count($opencode)==7){
        //                 break;
        //             }
        //         }
        //     }while(1);

        //     $dice = $opencode;
        //     $opencode = implode(',',$opencode);
    
        //     $data = [
        //         'expect' => $expect--,
        //         'opencode' => $opencode,
        //         'opentime' => date("Y-m-d H:i:s"),
        //     ];
        //     $dice = explode(",", $this->dice['data']['data'][0]['opencode']);
        //     if($dice[6]==49){//特码为49两面盘会出现和值
        //         $this->max = 1;
        //     }
            
    
        //     $gameResult = $this->active->analyseCode($dice,$this->rule);
        //     //记录基本走势 table:games_six_trend
        //     $this->active->recordeBasicTrend($data,$this->rule);
        //     //记录色波 table:games_six_color;
        //     $this->active->recordeColor($data,$this->rule);
        //     //记录生肖 table:games_six_animal;
        //     $this->active->recordsAnimal($data,$this->rule);
        //     //记录开奖结果 table:games_jsks_dice_history
        //     $this->active->recordeDiceHistory($data,$this->rule);
        // }


}

    //直接获取游戏结果
    public function diceResult(){
        /**
        *'expect' => string '2018118' (length=7)    //期数
        *'opencode' => string '45,07,11,19,46,32,37' (length=20)//开奖结果
        *'opentime' => string '2018-10-16 21:35:05' (length=19)//开间时间
         */
        // $dice = new Gameapi();
        // $this->dice = $dice->getGameData('xglhc');
        // $data = $this->dice['data']['data'][0];
        // $dice = explode(",", $this->dice['data']['data'][0]['opencode']);

        //测试
        $flag = 0;
        $opencode = [];
        do{ 
            $i = rand(1,49);
            if(!in_array($i,$opencode)){
                $opencode[] = $i;
                ++$flag; 
            }
        }while($flag!=7);
        $dice = $opencode;
        $opencode = implode(',',$opencode);
        $expect = 2018118;
        $data = [
            'expect' => $expect--,
            'opencode' => $opencode,
            'opentime' => date("Y-m-d H:i:s",time()),
        ];
        $this->dice['data']['data'][0] = $data;

        if($dice[6]==49){//特码为49两面盘会出现和值
            $this->max = 1;
        }
        $gameResult = $this->active->analyseCode($dice,$this->rule);
        //记录基本走势 table:games_six_trend
        $this->active->recordeBasicTrend($data,$this->rule);
        //记录色波 table:games_six_color;
        $this->active->recordeColor($data,$this->rule);
        //记录生肖 table:games_six_animal;
        $this->active->recordsAnimal($data,$this->rule);
        //记录开奖结果 table:games_jsks_dice_history
        $this->active->recordeDiceHistory($data,$this->rule);
        // dump($gameResult);
        return $gameResult;
    }

    // 游戏结算    //bet就是workman传过来的数据
    public function begin($playerData,$diceResult)
    // public function begin()
    {   
        $odds = file_get_contents(dirname(dirname(__FILE__)).'./odds.json');
        $odds = json_decode($odds,true);
        $code = explode(",", $this->dice['data']['data'][0]['opencode']);
        /**
        * 三大参数的数据结构$playerData===$odds------$diceResult;
        * 1 赔率结算 $odds-----$playerData
        * 1 算法输赢 $playData-----$diceResult
        */

        foreach($playerData as $conId => $player){
            
            $playerWin = 0;//单个玩家赢的金币
            //求出每一注订单的下注结果
            foreach($player as $orderNumber=>$order){
                //更新用户剩余金币，以数据库为准
                echo "refreshRemaining--------------\n";
                $playerData[$conId]['remaining'] = $this->active->refreshRemaining($order);

                $win = 0;
                //结算所有订单
                $type = $order['type'];
                switch($type){
                    case 'lm': //两面 唯一一个会有和局的玩法
                    //$this->max 49标志
                    if($this->max){
                        if(preg_match("/lmtedan|lmteshuang|lmteda|lmtexiao|lmteheda|lmtehexiao|lmtehedan|lmteheshuang|lmteweida|lmteweixiao/",$data['number'])){
                            $win = $order['gold'];
                        }
                    }
                    else{
                        if(in_array($order['number'],$diceResult['lm'])){
                            $win = $order['gold']*$odds[$order['number']];
                        }
                    }
                    break;

                    case 'qmwx': //七码五行
                    if(in_array($order['number'],$diceResult['qmwx'])){
                        $win = $order['gold']*$odds[$order['number']];
                    }
                    break; 

                    case 'wsh': //尾数
                    if(in_array($order['number'],$diceResult['wsh'])){
                        $win = $order['gold']*$odds[$order['number']];
                    }
                    break;

                    case 'sb'://色波
                    if(in_array($order['number'],$diceResult['sb'])){
                        $win = $order['gold']*$odds[$order['number']];
                    }
                    break; 

                    case 'hq': //需要多传一个类型
                    if($this->active->hq($code,$this->rule,$order['number'])){
                        $win = $order['gold']*$odds[$order['leiXin']];
                    }
                    break;

                    case 'shx': 
                    if(in_array($order['number'],$diceResult['shx'])){
                        $win = $order['gold']*$order['number'];
                    }
                    break;

                    case 'zxbzh'://需要多传一个类型 
                    if($this->active->zxbzh($code,$order['number'])){
                        $win = $order['gold']*$odds[$order['leiXin']];
                    }
                    break;  
                    case 'lqlw': //需要多传一个类型
                    if($this->active->lqlw($code,$order['number'],$this->rule)){
                        if(count($order['number'])>1){//连肖
                            if(in_array("gou",$order['number'])){//有狗
                                $win = $order['gold']*$odds[$order['leiXin']]['gou'];
                            }
                            else{//无狗
                                $win = $order['gold']*$odds[$order['leiXin']]['gou'];
                            }
                        }
                        else{//连尾
                            $win = $order['gold']*$odds[$order['leiXin']];
                        }

                    }
                    break;  
                    case 'lma':
                    $result = $this->active->lma($code,$order['number'],$order['leiXin']);
                    if($result){
                        $win = $order['gold']*$order[$result];
                    }                     
                    break;  

                    case 'zhmgg': 
                    if($this->active->zhmgg($code,$this->rule,$order['number'])){
                        foreach($order['number'] as $vo){//中奖了所有赔率相乘
                            $win = $order['gold']*$odds[$vo];
                        }
                    }
                    break;  
                    case 'zhm1-6': 
                    if(in_array($order['number'],$diceResult['zhm16'][$order['leiXin']])){
                        $win = $order['gold']*$order['number'];
                    }
                    break;  
                    case 'zhmt':
                    if($order['number'] == $diceResult['zhmt'][$order['leiXin']]){
                        $win = $order['gold']*$odds[$order['leiXin']];
                    } 
                    break;  
                    
                    case 'zhm': 
                    if(in_array($order['number'],$diceResult['zhm'])){
                        $name = 'zhm'.$order['number'];
                        $win = $order['gold']*$odds[$name];
                    }   
                    break;  
                    
                    case 'tm': 
                    if($order['number']==$diceResult['tm']){
                        $name = 'tm'.$order['number'];
                        $win = $order['gold']*$odds[$name];
                    }   
                    break;  
                    
                    case 'zhy':
                    if($this->active->zhy($code,$order['number'])){
                        $win = $order['gold']*$odds[$order['leiXin']];
                    } 
                    break; 

                    default:
                    break;
                }
                $dice = $this->dice['data']['data'][0];
                // 结算订单 table:games_bet_history:update
                $this->active->recordeBetHistory($orderNumber,$dice,$win);
                $playerWin += $win;
                $userId = $playerData[$conId][$orderNumber]['id'];
            }
            //清空数组只返回剩余金币
            $remaining = $playerData[$conId]['remaining']+$win;

            //更新用户金币 table:user_user:update
            $this->active->refleshUser($userId,$remaining);
            
            //清空数组
            $playerData[$conId] = [];
            // $playerData[$conId]['win'] = $playerData[$conId]['win'];
            //保留remaining
            $playerData[$conId]['remaining'] = $remaining;
            //记录用户最新金币 table:user_user 
        }
        return $playerData;
    }

    /*
    **ajax请求个人的购奖记录
    */
    public function betHistory(){
        $betHistory = Db::name('games_bet_history')->field('id',true)->limit(0,15)->order('id desc')->select();
        return $betHistory;
    }

    /**
     * 六合彩近期开奖记录
     * ajax 请求近期开奖
     */
    public function recentOpen(){
        $recentOpen = Db::name('games_six_dice_history')->field('issue,open_time,code')->limit(0,30)->order('id desc')->select();
        return $recentOpen;
    }
    /*
    * ajax  1基本走势:总和，特码  2生肖  3色波  4近期开奖
    */
    public function gameHistory(){
        $trend = 'games_six_trend';
        $animal = 'games_six_animal';
        $color = 'games_six_color';

        $data = [
            'trend'=> [
                '30'=>[
                        "trend"=>Db::name($trend)->limit(0,30)->field('id',true)->order('id desc')->select(),
                    ],
                '50'=> [
                        "trend"=>Db::name($trend)->limit(0,50)->field('id',true)->order('id desc')->select(),
                    ],
                '100'=> [
                        "trend"=>Db::name($trend)->limit(0,100)->field('id',true)->order('id desc')->select(),
                    ],            		
            ],
            'animal'=>[
                '30'=>[
                        "animal"=>Db::name($animal)->limit(0,30)->field('id',true)->order('id desc')->select(),
                        'analyse'=>self::animalAnalyse(30),
                ],
                '50'=>[
                        "animal"=>Db::name($animal)->limit(0,50)->field('id',true)->order('id desc')->select(),
                        'analyse'=>self::animalAnalyse(50),
                ],
                '100'=>[
                        "animal"=>Db::name($animal)->limit(0,100)->field('id',true)->order('id desc')->select(),
                        'analyse'=>self::animalAnalyse(100),
                ],
            ],
         	'color'=>[
                 '30'=>[
                        "color"=>Db::name($color)->limit(0,30)->order('id desc')->field('id',true)->select(),
                        'analyse'=>self::colorAnalyse(30),
                 ],
                 '50'=>[
                        "color"=>Db::name($color)->limit(0,50)->order('id desc')->field('id',true)->select(),
                        'analyse'=>self::colorAnalyse(50),
                 ],
                 '100'=>[
                        "color"=>Db::name($color)->limit(0,100)->order('id desc')->field('id',true)->select(),
                        'analyse'=>self::colorAnalyse(100),
                 ],
         	],
        ];
        return $data;
    }

    //生肖的历史分析表
    public function animalAnalyse($amount){
        $dataAll =  Db::name('games_six_animal')->field('id,issue',true)->limit(0,30)->order('id desc')->select();
        
        $record = [
            "zh1" =>['shu'=>0,'niu'=>0,'hu'=>0,'tu'=>0,'hou'=>0,'ji'=>0,'gou'=>0,'zhu'=>0,'long'=>0,'she'=>0,'she'=>0,'ma'=>0,'yang'=>0],
            "zh2" =>['shu'=>0,'niu'=>0,'hu'=>0,'tu'=>0,'hou'=>0,'ji'=>0,'gou'=>0,'zhu'=>0,'long'=>0,'she'=>0,'she'=>0,'ma'=>0,'yang'=>0],
            "zh3" =>['shu'=>0,'niu'=>0,'hu'=>0,'tu'=>0,'hou'=>0,'ji'=>0,'gou'=>0,'zhu'=>0,'long'=>0,'she'=>0,'she'=>0,'ma'=>0,'yang'=>0],
            "zh4" =>['shu'=>0,'niu'=>0,'hu'=>0,'tu'=>0,'hou'=>0,'ji'=>0,'gou'=>0,'zhu'=>0,'long'=>0,'she'=>0,'she'=>0,'ma'=>0,'yang'=>0],
            "zh5" =>['shu'=>0,'niu'=>0,'hu'=>0,'tu'=>0,'hou'=>0,'ji'=>0,'gou'=>0,'zhu'=>0,'long'=>0,'she'=>0,'she'=>0,'ma'=>0,'yang'=>0],
            "zh6" =>['shu'=>0,'niu'=>0,'hu'=>0,'tu'=>0,'hou'=>0,'ji'=>0,'gou'=>0,'zhu'=>0,'long'=>0,'she'=>0,'she'=>0,'ma'=>0,'yang'=>0],
            "tm" =>['shu'=>0,'niu'=>0,'hu'=>0,'tu'=>0,'hou'=>0,'ji'=>0,'gou'=>0,'zhu'=>0,'long'=>0,'she'=>0,'she'=>0,'ma'=>0,'yang'=>0],
            'shu'=>['shu'=>0,'niu'=>"",'hu'=>"",'tu'=>"",'hou'=>"",'ji'=>"",'gou'=>"",'zhu'=>"",'long'=>"",'she'=>"",'ma'=>"",'yang'=>""],
            'niu'=>['shu'=>'','niu'=>0,'hu'=>"",'tu'=>"",'hou'=>"",'ji'=>"",'gou'=>"",'zhu'=>"",'long'=>"",'she'=>"",'ma'=>"",'yang'=>""],
            'hu'=>['shu'=>"",'niu'=>"",'hu'=>0,'tu'=>"",'hou'=>"",'ji'=>"",'gou'=>"",'zhu'=>"",'long'=>"",'she'=>"",'ma'=>"",'yang'=>""],
            'tu'=>['shu'=>"",'niu'=>"",'hu'=>"",'tu'=>0,'hou'=>"",'ji'=>"",'gou'=>"",'zhu'=>"",'long'=>"",'she'=>"",'ma'=>"",'yang'=>""],
            'hou'=>['shu'=>"",'niu'=>"",'hu'=>"",'tu'=>"",'hou'=>0,'ji'=>"",'gou'=>"",'zhu'=>"",'long'=>"",'she'=>"",'ma'=>"",'yang'=>""],
            'ji'=>['shu'=>"",'niu'=>"",'hu'=>"",'tu'=>"",'hou'=>"",'ji'=>0,'gou'=>"",'zhu'=>"",'long'=>"",'she'=>"",'ma'=>"",'yang'=>""],
            'gou'=>['shu'=>"",'niu'=>"",'hu'=>"",'tu'=>"",'hou'=>"",'ji'=>"",'gou'=>0,'zhu'=>"",'long'=>"",'she'=>"",'ma'=>"",'yang'=>""],
            'zhu'=>['shu'=>"",'niu'=>"",'hu'=>"",'tu'=>"",'hou'=>"",'ji'=>"",'gou'=>"",'zhu'=>0,'long'=>"",'she'=>"",'ma'=>"",'yang'=>""],
            'long'=>['shu'=>"",'niu'=>"",'hu'=>"",'tu'=>"",'hou'=>"",'ji'=>"",'gou'=>"",'zhu'=>"",'long'=>0,'she'=>"",'ma'=>"",'yang'=>""],
            'she'=>['shu'=>"",'niu'=>"",'hu'=>"",'tu'=>"",'hou'=>"",'ji'=>"",'gou'=>"",'zhu'=>"",'long'=>"",'she'=>0,'ma'=>"",'yang'=>""],
            'ma'=>['shu'=>"",'niu'=>"",'hu'=>"",'tu'=>"",'hou'=>"",'ji'=>"",'gou'=>"",'zhu'=>"",'long'=>"",'she'=>"",'ma'=>0,'yang'=>""],
            'yang'=>['shu'=>"",'niu'=>"",'hu'=>"",'tu'=>"",'hou'=>"",'ji'=>"",'gou'=>"",'zhu'=>"",'long'=>"",'she'=>"",'ma'=>"",'yang'=>0],
        ];
        $quanXiao =  ['shu','niu','hu','tu','hou','ji','gou','zhu','long','she','ma','yang'];
        foreach($dataAll as $sub=>$data){
            foreach($data as $key =>$vo){
                    //十二生肖总数++
                    if(in_array($key,$quanXiao)){
                        $record[$key][$key]+=$vo;
                    }
                    //七个正码十二生肖++
                    else{
                        $vo = json_decode($vo,true);
                        if($vo['shX'] == 'shu'){
                            $record[$key]['shu']++;
                        }
                        else if($vo['shX'] == 'niu'){
                            $record[$key]['niu']++;
                        }
                        else if($vo['shX'] == 'hu'){
                            $record[$key]['hu']++;
                        }
                        else if($vo['shX'] == 'tu'){
                            $record[$key]['tu']++;
                        }
                        else if($vo['shX'] == 'long'){
                            $record[$key]['long']++;
                        }
                        else if($vo['shX'] == 'she'){
                            $record[$key]['she']++;
                        }
                        else if($vo['shX'] == 'ma'){
                            $record[$key]['ma']++;
                        }
                        else if($vo['shX'] == 'yang'){
                            $record[$key]['yang']++;
                        }
                        else if($vo['shX'] == 'hou'){
                            $record[$key]['hou']++;
                        }
                        else if($vo['shX'] == 'ji'){
                            $record[$key]['ji']++;
                        }
                        else if($vo['shX'] == 'gou'){
                            $record[$key]['gou']++;
                        }
                        else if($vo['shX'] == 'zhu'){
                            $record[$key]['zhu']++;
                        }
    
                    } 
            }
    
        }
        //把数组反过来变成12x19
        $reverse = [];
        foreach($record as $key=>$vo){
            $reverse['shu'][$key] = $vo['shu'];
            $reverse['niu'][$key] = $vo['niu'];
            $reverse['hu'][$key] = $vo['hu'];
            $reverse['tu'][$key] = $vo['tu'];
            $reverse['long'][$key] = $vo['long'];
            $reverse['she'][$key] = $vo['she'];
            $reverse['ma'][$key] = $vo['ma'];
            $reverse['yang'][$key] = $vo['shu'];
            $reverse['hou'][$key] = $vo['hou'];
            $reverse['ji'][$key] = $vo['ji'];
            $reverse['gou'][$key] = $vo['gou'];
            $reverse['zhu'][$key] = $vo['zhu'];
        }
        return $reverse;
    }
    //波色的历史分析
    public function colorAnalyse($amount){
        $data =  Db::name('games_six_color')->field('id,issue',true)->limit(0,$amount)->order('id desc')->select();
        $record = [
            "zh1" =>['hong'=>0,'lan'=>0,'lv'=>0],
            "zh2" =>['hong'=>0,'lan'=>0,'lv'=>0],
            "zh3" =>['hong'=>0,'lan'=>0,'lv'=>0],
            "zh4" =>['hong'=>0,'lan'=>0,'lv'=>0],
            "zh5" =>['hong'=>0,'lan'=>0,'lv'=>0],
            "zh6" =>['hong'=>0,'lan'=>0,'lv'=>0],
            "tm" =>['hong'=>0,'lan'=>0,'lv'=>0],
            "sbb" =>['hong'=>0,'lan'=>0,'lv'=>0]//波色比
        ];
        //数据结构一致可以对应上来
        foreach($data as $key =>$vo){
            foreach($vo as $keyh => $voh){
                $voh = json_decode($voh,true);
                if($keyh!="sbb"){//非色波比+1
                    if($voh['bs'] == 'hong'){
                        $record[$keyh]['hong']++;
                    }
                    else if($voh['bs'] == 'lan'){
                        $record[$keyh]['lan']++;
                    }
                    else if($voh['bs'] == 'lv'){
                        $record[$keyh]['lv']++;
                    }
                }
                else{
                    $record[$keyh]['hong']+=$voh['hong'];
                    $record[$keyh]['lan']+=$voh['lan'];
                    $record[$keyh]['lv']+=$voh['lv'];
                }
            }
        }
        return $record;
    }

    //最近游戏记录 table::'games_jsks_dice_history'
    public function recentInfo(){
        $data = Db::name('games_six_dice_history')->field('id',true)->order('id desc')->limit(19)->select();
        return $data;
    }

    //生成下注订单
    public function createOrder($order,$createTime,$orderNumber){
        //合并数组成字符串
        if(is_array($order['number'])){
            $order['number'] = implode(',',$order['number']);
        }
        //炸成字符串
        if(is_array($order['odds'])){
            $order['odds'] = implode(',',$order['odds']);
        }

        if($order['leiXin'] != ""){
            $name = $order['type']."_".$order["leiXin"];
        }else{
            $name = $order['type'];   
        }   
        $data = [
            'user_id' =>$order['id'],//用户id
            'issue' => $order['issue'],//押注期号
            'gold'  => $order['zhuShu']."注  ".$order['gold']."金币",//押注金额 注数+金额
            'order_number'=> $orderNumber, //押注单号
            'name'=>$name,//押注名称
            'return_point'=>0,    //返点
            'create_time'=>$createTime,
            'number'=>$order['number'],//押注数据
            'odds'=>$order['odds'],//押注赔率
            "gameName"=>$order['gameName'],//游戏名字
            "key"=>$order['key'],//游戏关键字
            "avatar"=>$order['avatar']//logo地址
        ];

        $bet = Db::name('games_bet_history')->insert($data);
        if($bet){
            $result = [
                'create'=> true,
            ];
        }
        else{
            $result = [
                'create'=> false,
            ];

        }

        echo  "------------create success--------------\n";
        return $result;
    }

    //撤销订单
    public function destroyOrder($orderNumber){
        $data = Db::name('games_bet_history')->where(['order_number'=>$orderNumber])->field('id,user_id,status,gold')->find();
        
        //订单还没有被结算可以撤销
        if($data&&$data['status'] == 0){
            try{
                $Dgold = substr($data['gold'],strpos($data['gold'],' ')+1);//把金币数分割出来
                $gold = str_replace("金币",'',$Dgold);      //分割金币数
            }catch(\Exception $e){
                throw $e;

            }
           

            //更改订单状态为3已撤销
            Db::name('games_bet_history')->where(['id'=>$data['id']])->update(['status'=>3]);
            $result = [
                'cancel'=>true,
                'cancelGold'=>$gold,
            ];//撤销成功
            echo "---destroy success---\n";
        }
        else{
            $result = ['cancel'=>false];//订单 已经删除或者已经结算
            echo "---destroy fail---\n";
        }
        return $result;
    }

    /* 桌面信息 table::"games_six_odds"
	** 
    */

    public function deskInfo(){
        $deskInfo = file_get_contents(dirname(dirname(__FILE__)).'./send.json');
        $deskInfo = json_decode($deskInfo,true);
        return $deskInfo;
    }

    /* 
    ** 桌面信息 : games_six_odds
    ** 基本走势 : games_six_trend
    ** 色波：games_six_color
    ** 生肖 : games_six_animal
    */



    public function createTable(){
    Request::param();
    	$oddsBefore = "DROP TABLE IF EXISTS `bc_games_six_odds`";
    	$trendBefore = "DROP TABLE IF EXISTS `bc_games_six_trend";
    	$colorBefore = "DROP TABLE IF EXISTS `bc_games_six_color`";
    	$animalBefore = "DROP TABLE IF EXISTS `bc_games_six_animal`";
        $diceBefore = 'DROP TABLE IF EXISTS `bc_games_six_dice_history`';

    	$check = [$oddsBefore,$trendBefore,$colorBefore,$animalBefore,$diceBefore];
    	
        $odds = [
            'status'=>0,
            'haoma' =>[
                'wx' => [
                    'wxjin'=> [4, 5, 18, 19, 26, 27, 34, 25, 48, 19],
                    'wxmu'=> [4, 5, 18, 19, 26, 27, 34, 25, 48, 19],
                    'wxshui'=> [6, 7, 14, 15, 22, 23, 26, 36, 37, 44, 45],
                    'wxhuo'=> [2, 23, 10, 11, 24, 25, 32, 33, 40, 41],
                    'wxtu'=> [12, 13, 20, 21, 28, 29, 42, 43]
                ],
                  
                  /* 各种生肖 */
                'animals' => [
                    'shu'=> [11, 23, 35, 47],
                    'niu'=> [10, 22, 34, 46],
                    'hu'=> [9, 21, 33, 45],
                    'tu'=> [8, 20, 32, 44],
                    'long'=> [7, 19, 31, 43],
                    'she'=> [6, 18, 30, 42],
                    'ma'=> [5, 17, 29, 41],
                    'yang'=> [4, 16, 28, 40],
                    'hou'=> [3, 15, 27, 39],
                    'ji'=> [2, 14, 26, 38],
                    'gou'=> [1, 13, 25, 37, 49],
                    'zhu'=> [12, 24, 36, 48]
                ],
            ],
            'peilv'=>[
                'lm' =>[//连尾
                        'lmtedan'=>1.980,
                        'lmteshuang'=>1.980,
                        'lmteda'=>1.980,
                        'lmtexiao'=>1.980,
                        'lmtehedan'=>1.980,
                        'lmteheshuang'=>1.980,
                        'lmteheda'=>1.980,
                        'lmtehexiao'=>1.980,
                        'lmteweida'=>1.980,
                        'lmteweixiao'=>1.980,
                        'lmtetianqiao'=>1.980,
                        'lmtediqiao'=>1.980,
                        'lmteqianqiao'=>1.980,
                        'lmtehouqiao'=>1.980,
                        'lmtejiaqin'=>1.980,
                        'lmteyeshou'=>1.980,
                        'lmzongdan'=>1.980,
                        'lmzongshuang'=>1.980,
                        'lmzongda'=>1.980,
                        'lmzongxiao'=>1.980,
                    ],
                'qmwx'=>[//七码五行
                    'qm'=>[
                        'qmd0sh7'=>231.300,
                        'qmd1sh6'=>24.110,
                        'qmd2sh5'=>6.450,
                        'qmd3sh4'=>3.400,
                        'qmd4sh3'=>3.200,
                        'qmd5sh2'=>5.560,
                        'qmd6sh1'=>19.200,
                        'qmd7sh0'=>169.320,
                        'qmd0x7'=>231.300,
                        'qmd1x6'=>24.110,
                        'qmd2x5'=>6.450,
                        'qmd3x4'=>3.400,
                        'qmd4x3'=>3.200,
                        'qmd5x2'=>5.560,
                        'qmd6x1'=>19.200,
                        'qmd7x0'=>169.320,    				
                    ],
                    'wx'=>[
                        'wxjin'=>4.700,
                        'wxmu'=>4.700,
                        'wxshui'=>4.700,
                        'wxhuo'=>4.700,
                        'wxtu'=>4.700,
                    ],
                ],
                'wsh'=>[//尾数
                    'twsh'=>[//头尾数
                        'twsht0'=>5.000,
                        'twsht1'=>4.360,
                        'twsht2'=>4.360,
                        'twsht3'=>4.360,
                        'twsht4'=>4.360,
                        'twshw0'=>11.160,
                        'twshw1'=>9.250,
                        'twshw2'=>9.250,
                        'twshw3'=>9.250,
                        'twshw4'=>9.250,
                        'twshw5'=>9.250,
                        'twshw6'=>9.250,
                        'twshw7'=>9.250,
                        'twshw8'=>9.250,
                        'twshw9'=>9.250,
                    ],
                    'zhtwsh'=>[//正特尾数
                        'zhtwshw0'=>2.100,
                        'zhtwshw1'=>1.800,
                        'zhtwshw2'=>1.800,
                        'zhtwshw3'=>1.800,
                        'zhtwshw4'=>1.800,
                        'zhtwshw5'=>1.800,
                        'zhtwshw6'=>1.800,
                        'zhtwshw7'=>1.800,
                        'zhtwshw8'=>1.800,
                        'zhtwshw9'=>1.800,
                    ],
                ],
                'sb'=>[//色波
                    'ssb'=>[//三色波
                        'ssbhb_hong'=>2.780,
                        'ssblb_lan'=>2.860,
                        'ssblvb_lv'=>2.860,
                    ],
                    'bb'=>[//半波
                        'bbhd_hong'=>5.580,
                        'bbhsh_hong'=>5.60,
                        'bbhda_hong'=>6.500,
                        'bbhx_hong'=>4.500,
                        'bblvd_lv'=>5.580,
                        'bblvsh_lv'=>6.450,
                        'bblvda_lv'=>5.580,
                        'bblvx_lv'=>6.520,
                        'bbld_lan'=>5.580,
                        'bblsh_lan'=>5.580,
                        'bblda_lan'=>5.000,
                        'bblx_lan'=>6.580
                    ],
                    'bbb'=>[//半半波
                        'bbbhdad_hong'=>14.800,
                        'bbbhdash_hong'=>11.120,
                        'bbbhxd_hong'=>8.920,
                        'bbbhxsh_hong'=>8.920,
                        'bbblvdad_lv'=>11.120,
                        'bbblvdash_lv'=>11.120,
                        'bbblvxd_lv'=>11.120,
                        'bbblvxsh_lv'=>14.820,
                        'bbbldad_lan'=>8.920,
                        'bbbldash_lan'=>11.120,
                        'bbblxd_lan'=>14.820,
                        'bbblxsh_lan'=>11.120,
                    ],
                    'qsb'=>[//七色波
                        'qsbhb_hong'=>2.650,
                        'qsblvb_lv'=>3.000,
                        'qsblb_lan'=>3.000,
                        'qsbhj'=>25.000,
                    ],
                ],
                'hq'=>[//合肖
                    'fenlei'=>[
                        'ysh'=>[//野兽
                            'zhl'=>['shu','hu','tu','long','she','hou'],
                            'yshplv'=>3.870,
                        ],
                        'jq'=>[//家肖
                            'zhl'=>['niu','ma','yang','ji','gou','zhu'],
                            'jqplv'=>3.748,
                        ],
                        'dan'=>[//单
                            'zhl'=>['shu','hu','long','ma','hou','gou'],
                            'danplv'=>3.748,
                        ],
                        'shuang'=>[//双
                            'zhl'=>['niu','tu','she','yang','ji','zhu'],
                            'shuangplv'=>3.870,
                        ],
                        'qq'=>[//前肖
                            'zhl'=>['shu','niu','hu','tu','long','she'],
                            'qqplv'=>3.870,
                        ],
                        'hq'=>[//后肖
                            'zhl'=>['ma','yang','hou','ji','gou','zhu'],
                            'hqplv'=>3.748,
                        ],
                        'tq'=>[//天肖
                            'zhl'=>['niu','tu','long','ma','hou','zhu'],
                            'tqplv'=>3.870,
                        ],
                        'dq'=>[//地肖
                            'zhl'=>['shu','hu','she','yang','ji','gou'],
                            'dqplv'=>3.748,
                        ],
                    ],
                    'fensang'=>[
                        'else' => [
                            "else2"=>5.85,
                            "else3"=>3.87,
                            "else4"=>2.925,
                            "else5"=>2.322,
                            "else6"=>1.935,
                            "else7"=>1.6585,
                            "else8"=>1.451,
                            "else9"=>1.29,
                            "else10"=>1.161,
                            "else11"=>1.555,
                        ],
                        'gou' =>[
                            "gou2"=>5.2545,
                            "gou3"=>3.6255,
                            "gou4"=>2.765,
                            "gou5"=>2.234,
                            "gou6"=>1.874,
                            "gou7"=>1.6135,
                            "gou8"=>1.417,
                            "gou9"=>1.263,
                            "gou10"=>1.139,
                            "gou11"=>1.375,                         
                        ],
                    ],
                    'shu'=>11.610,
                    'niu'=>11.610,
                    'hu'=>11.610,
                    'tu'=>11.610,
                    'long'=>11.610,
                    'she'=>11.610,
                    'ma'=>11.610,
                    'yang'=>11.610,
                    'hou'=>11.610,
                    'ji'=>11.610,
                    'gou'=>9.48,
                    'zhu'=>11.610,
                ],
                'shx'=>[//生肖
                    'zhq'=>[//正肖
                        'shu'=>1.920,
                        'niu'=>1.920,
                        'hu'=>1.920,
                        'tu'=>1.920,
                        'long'=>1.920,
                        'she'=>1.920,
                        'ma'=>1.920,
                        'yang'=>1.920,
                        'hou'=>1.920,
                        'ji'=>1.920,
                        'gou'=>1.750,
                        'zhu'=>1.920,
                    ],
                    'tq'=>[//天肖
                        'shu'=>11.600,
                        'niu'=>11.600,
                        'hu'=>11.600,
                        'tu'=>11.600,
                        'long'=>11.600,
                        'she'=>11.600,
                        'ma'=>11.600,
                        'yang'=>11.600,
                        'hou'=>11.600,
                        'ji'=>11.600,
                        'gou'=>9.500,
                        'zhu'=>11.600,
                    ],
                    'yq'=>[//一肖
                        'shu'=>2.100,
                        'niu'=>2.100,
                        'hu'=>2.100,
                        'tu'=>2.100,
                        'long'=>2.100,
                        'she'=>2.100,
                        'ma'=>2.100,
                        'yang'=>2.100,
                        'hou'=>2.100,
                        'ji'=>2.100,
                        'gou'=>1.800,
                        'zhu'=>2.100,
                    ],
                    'zq'=>[//正肖
                        'zq234q'=>15.000,
                        'zq5q'=>3.70,
                        'zq6q'=>1.960,
                        'zq7q'=>5.400,
                        'zqzqd'=>1.980,
                        'zqzqsh'=>1.850,
                    ],
                ],
                'zxbzh'=>[//自选不中
                    'zxbzh5bzh'=>2.10,//五
                    'zxbzh6bzh'=>2.50,//六
                    'zxbzh7bzh'=>3.00,//七
                    'zxbzh8bzh'=>3.60,//八
                    'zxbzh9bzh'=>4.30,//九
                    'zxbzh10bzh'=>5.20,//十
                    'zxbzh11bzh'=>6.35,//十一
                    'zxbzh12bzh'=>7.80,//十二
                ],
                'lqlw'=>[//连肖连尾
                    '2lq'=>[//二连肖
                        'shu'=>4.120,
                        'niu'=>4.120,
                        'hu'=>4.120,
                        'tu'=>4.120,
                        'long'=>4.120,
                        'she'=>4.120,
                        'ma'=>4.120,
                        'yang'=>4.120,
                        'hou'=>4.120,
                        'ji'=>4.120,
                        'gou'=>3.320,
                        'zhu'=>4.120,    					
                    ],
                    '3lq'=>[//三连肖
                        'shu'=>11.120,
                        'niu'=>11.120,
                        'hu'=>11.120,
                        'tu'=>11.120,
                        'long'=>11.120,
                        'she'=>11.120,
                        'ma'=>11.120,
                        'yang'=>11.120,
                        'hou'=>11.120,
                        'ji'=>11.120,
                        'gou'=>9.20,
                        'zhu'=>11.120,    					
                    ],
                    '4lq'=>[//四连肖
                        'shu'=>32.000,
                        'niu'=>32.000,
                        'hu'=>32.000,
                        'tu'=>32.000,
                        'long'=>32.000,
                        'she'=>32.000,
                        'ma'=>32.000,
                        'yang'=>32.000,
                        'hou'=>32.000,
                        'ji'=>32.000,
                        'gou'=>28.000,
                        'zhu'=>32.000,    					
                    ],
                    '5lq'=>[//五连肖
                        'shu'=>98.000,
                        'niu'=>98.000,
                        'hu'=>98.000,
                        'tu'=>98.000,
                        'long'=>98.000,
                        'she'=>98.000,
                        'ma'=>98.000,
                        'yang'=>98.000,
                        'hou'=>98.000,
                        'ji'=>98.000,
                        'gou'=>87.000,
                        'zhu'=>98.000,    					
                    ],
                    '2lw'=>[//二连尾
                        'lw0'=>3.180,
                        'lw1'=>3.180,
                        'lw2'=>3.180,
                        'lw3'=>3.180,
                        'lw4'=>3.180,
                        'lw5'=>3.180,
                        'lw6'=>3.180,
                        'lw7'=>3.180,
                        'lw8'=>3.180,
                        'lw9'=>3.180,    					
                    ],
                    '3lw'=>[//三连尾
                        'lw0'=>7.80,
                        'lw1'=>7.80,
                        'lw2'=>7.80,
                        'lw3'=>7.80,
                        'lw4'=>7.80,
                        'lw5'=>7.80,
                        'lw6'=>7.80,
                        'lw7'=>7.80,
                        'lw8'=>7.80,
                        'lw9'=>7.80,      					
                    ],
                    '4lw'=>[//四连尾
                        'lw0'=>15.800,
                        'lw1'=>15.800,
                        'lw2'=>15.800,
                        'lw3'=>15.800,
                        'lw4'=>15.800,
                        'lw5'=>15.800,
                        'lw6'=>15.800,
                        'lw7'=>15.800,
                        'lw8'=>15.800,
                        'lw9'=>15.800,      					
                    ],
                    '5lw'=>[//五连尾
                        'lw0'=>45.000,
                        'lw1'=>40.000,
                        'lw2'=>40.000,
                        'lw3'=>40.000,
                        'lw4'=>40.000,
                        'lw5'=>40.000,
                        'lw6'=>40.000,
                        'lw7'=>40.000,
                        'lw8'=>40.000,
                        'lw9'=>40.000,      					
                    ],
                    'lqpeilv'=>[//连肖赔率
                        '2lq'=>['nogou'=>4.120,'gou'=>3.32],//二
                        '3lq'=>['nogou'=>11.120,'gou'=>9.20],//三
                        '4lq'=>['nogou'=>28.000,'gou'=>32.000],//四
                        '5lq'=>['nogou'=>98.000,'gou'=>87.000],//五
                        '2lw'=>3.180,//
                        '3lw'=>7.80,
                        '4lw'=>15.800,
                        '5lw'=>40.000,
                    ],
                ],
                'lma'=>[//连码
                    'lmasiqzh'=>10000.00,//四全中
                    'lmasqzh'=>650.00,
                    'lmaszher'=>['lmaszherzhs'=>100.00,'lmaszherzher'=>'23.00'],
                    'lmaerqzh'=>70.00,
                    'lmaerzht'=>['lmaszherzher'=>51.00,'lmaszherzht'=>31.00],
                    'lmatch'=>155.00,
                ],
                'zhmgg'=>[
                        'zhy'=>[
                            'zhyd_dan'=>1.970,
                            'zhysh_shuang'=>1.970,
                            'zhyda_da'=>1.970,
                            'zhyx_xiao'=>1.970,
                            'zhyhd_dan'=>1.970,
                            'zhyhsh_shuang'=>1.970,
                            'zhyhda_da'=>1.970,
                            'zhyhx_xiao'=>1.970,
                            'zhywda_da'=>1.970,
                            'zhywx_xiao'=>1.970,
                            'zhyhb_hong'=>2.700,
                            'zhylvb_lv'=>2.850,
                            'zhylb_lang'=>2.850,
                        ],
                        'zher'=>[
                                'zherd_dan'=>1.970,
                                'zhersh_shuang'=>1.970,
                                'zherda_da'=>1.970,
                                'zherx_xiao'=>1.970,
                                'zherhd_dan'=>1.970,
                                'zherhsh_shuang'=>1.970,
                                'zherhda_da'=>1.970,
                                'zherhx_xiao'=>1.970,
                                'zherwda_da'=>1.970,
                                'zherwx_xiao'=>1.970,
                                'zherhb_hong'=>2.700,
                                'zherlvb_lv'=>2.850,
                                'zherlb_lang'=>2.850,
                        ],
                        'zhs'=>[
                            'zhsd_dan'=>1.970,
                            'zhssh_shuang'=>1.970,
                            'zhsda_da'=>1.970,
                            'zhsx_xiao'=>1.970,
                            'zhshd_dan'=>1.970,
                            'zhshsh_shuang'=>1.970,
                            'zhshda_da'=>1.970,
                            'zhshx_xiao'=>1.970,
                            'zhswda_da'=>1.970,
                            'zhswx_xiao'=>1.970,
                            'zhshb_hong'=>2.700,
                            'zhslvb_lv'=>2.850,
                            'zhslb_lang'=>2.850,
                        ],
    
                        'zhsi'=>[
                            'zhsid_dan'=>1.970,
                            'zhsish_shuang'=>1.970,
                            'zhsida_da'=>1.970,
                            'zhsix_xiao'=>1.970,
                            'zhsihd_dan'=>1.970,
                            'zhsihsh_shuang'=>1.970,
                            'zhsihda_da'=>1.970,
                            'zhsihx_xiao'=>1.970,
                            'zhsiwda_da'=>1.970,
                            'zhsiwx_xiao'=>1.970,
                            'zhsihb_hong'=>2.700,
                            'zhsilvb_lv'=>2.850,
                            'zhsilb_lang'=>2.850,
                        ],
    
                        'zhw'=>[
                            'zhwd_dan'=>1.970,
                            'zhwsh_shuang'=>1.970,
                            'zhwda_da'=>1.970,
                            'zhwx_xiao'=>1.970,
                            'zhwhd_dan'=>1.970,
                            'zhwhsh_shuang'=>1.970,
                            'zhwhda_da'=>1.970,
                            'zhwhx_xiao'=>1.970,
                            'zhwwda_da'=>1.970,
                            'zhwwx_xiao'=>1.970,
                            'zhwhb_hong'=>2.700,
                            'zhwlvb_lv'=>2.850,
                            'zhwlb_lang'=>2.850,
                        ],
    
                        'zhl'=>[
                            'zhld_dan'=>1.970,
                            'zhlsh_shuang'=>1.970,
                            'zhlda_da'=>1.970,
                            'zhlx_xiao'=>1.970,
                            'zhlhd_dan'=>1.970,
                            'zhlhsh_shuang'=>1.970,
                            'zhlhda_da'=>1.970,
                            'zhlhx_xiao'=>1.970,
                            'zhlwda_da'=>1.970,
                            'zhlwx_xiao'=>1.970,
                            'zhlhb_hong'=>2.700,
                            'zhllvb_lv'=>2.850,
                            'zhllb_lang'=>2.850,
                        ],
                    ],
                    'zhm1-6'=>[
                        'zh1'=>[
                            'zh1d_dan'=>1.980,
                            'zh1sh_shuang'=>1.980,
                            'zh1da_da'=>1.980,
                            'zh1x_xiao'=>1.980,
                            'zh1hd_dan'=>1.980,
                            'zh1hsh_shuang'=>1.980,
                            'zh1hda_da'=>1.980,
                            'zh1hx_xiao'=>1.980,
                            'zh1wda_da'=>1.980,
                            'zh1wx_xiao'=>1.980,
                            'zh1hb_hong'=>2.780,
                            'zh1lvb_lv'=>2.860,
                            'zh1lb_lang'=>2.860,
                        ],
                        'zh2'=>[
                            'zh2d_dan'=>1.980,
                            'zh2sh_shuang'=>1.980,
                            'zh2da_da'=>1.980,
                            'zh2x_xiao'=>1.980,
                            'zh2hd_dan'=>1.980,
                            'zh2hsh_shuang'=>1.980,
                            'zh2hda_da'=>1.980,
                            'zh2hx_xiao'=>1.980,
                            'zh2wda_da'=>1.980,
                            'zh2wx_xiao'=>1.980,
                            'zh2hb_hong'=>2.780,
                            'zh2lvb_lv'=>2.860,
                            'zh2lb_lang'=>2.860,
                        ],
                        'zh3'=>[
                            'zh3d_dan'=>1.980,
                            'zh3sh_shuang'=>1.980,
                            'zh3da_da'=>1.980,
                            'zh3x_xiao'=>1.980,
                            'zh3hd_dan'=>1.980,
                            'zh3hsh_shuang'=>1.980,
                            'zh3hda_da'=>1.980,
                            'zh3hx_xiao'=>1.980,
                            'zh3wda_da'=>1.980,
                            'zh3wx_xiao'=>1.980,
                            'zh3hb_hong'=>2.780,
                            'zh3lvb_lv'=>2.860,
                            'zh3lb_lang'=>2.860,
                        ],
                        'zh4'=>[
                            'zh4d_dan'=>1.980,
                            'zh4sh_shuang'=>1.980,
                            'zh4da_da'=>1.980,
                            'zh4x_xiao'=>1.980,
                            'zh4hd_dan'=>1.980,
                            'zh4hsh_shuang'=>1.980,
                            'zh4hda_da'=>1.980,
                            'zh4hx_xiao'=>1.980,
                            'zh4wda_da'=>1.980,
                            'zh4wx_xiao'=>1.980,
                            'zh4hb_hong'=>2.780,
                            'zh4lvb_lv'=>2.860,
                            'zh4lb_lang'=>2.860,
                        ],
                        'zh5'=>[
                            'zh5d_dan'=>1.980,
                            'zh5sh_shuang'=>1.980,
                            'zh5da_da'=>1.980,
                            'zh5x_xiao'=>1.980,
                            'zh5hd_dan'=>1.980,
                            'zh5hsh_shuang'=>1.980,
                            'zh5hda_da'=>1.980,
                            'zh5hx_xiao'=>1.980,
                            'zh5wda_da'=>1.980,
                            'zh5wx_xiao'=>1.980,
                            'zh5hb_hong'=>2.780,
                            'zh5lvb_lv'=>2.860,
                            'zh5lb_lang'=>2.860,
                        ],
                        'zh6'=>[
                            'zh6d_dan'=>1.980,
                            'zh6sh_shuang'=>1.980,
                            'zh6da_da'=>1.980,
                            'zh6x_xiao'=>1.980,
                            'zh6hd_dan'=>1.980,
                            'zh6hsh_shuang'=>1.980,
                            'zh6hda_da'=>1.980,
                            'zh6hx_xiao'=>1.980,
                            'zh6wda_da'=>1.980,
                            'zh6wx_xiao'=>1.980,
                            'zh6hb_hong'=>2.780,
                            'zh6lvb_lv'=>2.860,
                            'zh6lb_lang'=>2.860,
                        ],
    
                    ],
                    'zhmt'=>[
                    'zhyt'=>[
                        'zhyt1'=>47.00,
                        'zhyt2'=>47.00,
                        'zhyt3'=>47.00,
                        'zhyt4'=>47.00,
                        'zhyt5'=>47.00,
                        'zhyt6'=>47.00,
                        'zhyt7'=>47.00,
                        'zhyt8'=>47.00,
                        'zhyt9'=>47.00,
                        'zhyt10'=>47.00,
                        'zhyt11'=>47.00,
                        'zhyt12'=>47.00,
                        'zhyt13'=>47.00,
                        'zhyt14'=>47.00,
                        'zhyt15'=>47.00,
                        'zhyt16'=>47.00,
                        'zhyt17'=>47.00,
                        'zhyt18'=>47.00,
                        'zhyt19'=>47.00,
                        'zhyt20'=>47.00,

                        'zhyt21'=>47.00,
                        'zhyt22'=>47.00,
                        'zhyt23'=>47.00,
                        'zhyt24'=>47.00,
                        'zhyt25'=>47.00,
                        'zhyt26'=>47.00,
                        'zhyt27'=>47.00,
                        'zhyt28'=>47.00,
                        'zhyt29'=>47.00,
                        'zhyt30'=>47.00,

                        'zhyt31'=>47.00,
                        'zhyt32'=>47.00,
                        'zhyt33'=>47.00,
                        'zhyt34'=>47.00,
                        'zhyt35'=>47.00,
                        'zhyt36'=>47.00,
                        'zhyt37'=>47.00,
                        'zhyt38'=>47.00,
                        'zhyt39'=>47.00,
                        'zhyt40'=>47.00,

                        'zhyt41'=>47.00,
                        'zhyt42'=>47.00,
                        'zhyt43'=>47.00,
                        'zhyt44'=>47.00,
                        'zhyt45'=>47.00,
                        'zhyt46'=>47.00,
                        'zhyt47'=>47.00,
                        'zhyt48'=>47.00,
                        'zhyt49'=>47.00,
                    ],
                    'zhert'=>[
                        'zhert1'=>47.00,
                        'zhert2'=>47.00,
                        'zhert3'=>47.00,
                        'zhert4'=>47.00,
                        'zhert5'=>47.00,
                        'zhert6'=>47.00,
                        'zhert7'=>47.00,
                        'zhert8'=>47.00,
                        'zhert9'=>47.00,
                        'zhert10'=>47.00,
                        'zhert11'=>47.00,
                        'zhert12'=>47.00,
                        'zhert13'=>47.00,
                        'zhert14'=>47.00,
                        'zhert15'=>47.00,
                        'zhert16'=>47.00,
                        'zhert17'=>47.00,
                        'zhert18'=>47.00,
                        'zhert19'=>47.00,
                        'zhert20'=>47.00,

                        'zhert21'=>47.00,
                        'zhert22'=>47.00,
                        'zhert23'=>47.00,
                        'zhert24'=>47.00,
                        'zhert25'=>47.00,
                        'zhert26'=>47.00,
                        'zhert27'=>47.00,
                        'zhert28'=>47.00,
                        'zhert29'=>47.00,
                        'zhert30'=>47.00,

                        'zhert31'=>47.00,
                        'zhert32'=>47.00,
                        'zhert33'=>47.00,
                        'zhert34'=>47.00,
                        'zhert35'=>47.00,
                        'zhert36'=>47.00,
                        'zhert37'=>47.00,
                        'zhert38'=>47.00,
                        'zhert39'=>47.00,
                        'zhert40'=>47.00,

                        'zhert41'=>47.00,
                        'zhert42'=>47.00,
                        'zhert43'=>47.00,
                        'zhert44'=>47.00,
                        'zhert45'=>47.00,
                        'zhert46'=>47.00,
                        'zhert47'=>47.00,
                        'zhert48'=>47.00,
                        'zhert49'=>47.00,
                    ],    				
                    'zhst'=>[
                        'zhst1'=>47.00,
                        'zhst2'=>47.00,
                        'zhst3'=>47.00,
                        'zhst4'=>47.00,
                        'zhst5'=>47.00,
                        'zhst6'=>47.00,
                        'zhst7'=>47.00,
                        'zhst8'=>47.00,
                        'zhst9'=>47.00,
                        'zhst10'=>47.00,
                        'zhst11'=>47.00,
                        'zhst12'=>47.00,
                        'zhst13'=>47.00,
                        'zhst14'=>47.00,
                        'zhst15'=>47.00,
                        'zhst16'=>47.00,
                        'zhst17'=>47.00,
                        'zhst18'=>47.00,
                        'zhst19'=>47.00,
                        'zhst20'=>47.00,

                        'zhst21'=>47.00,
                        'zhst22'=>47.00,
                        'zhst23'=>47.00,
                        'zhst24'=>47.00,
                        'zhst25'=>47.00,
                        'zhst26'=>47.00,
                        'zhst27'=>47.00,
                        'zhst28'=>47.00,
                        'zhst29'=>47.00,
                        'zhst30'=>47.00,

                        'zhst31'=>47.00,
                        'zhst32'=>47.00,
                        'zhst33'=>47.00,
                        'zhst34'=>47.00,
                        'zhst35'=>47.00,
                        'zhst36'=>47.00,
                        'zhst37'=>47.00,
                        'zhst38'=>47.00,
                        'zhst39'=>47.00,
                        'zhst40'=>47.00,

                        'zhst41'=>47.00,
                        'zhst42'=>47.00,
                        'zhst43'=>47.00,
                        'zhst44'=>47.00,
                        'zhst45'=>47.00,
                        'zhst46'=>47.00,
                        'zhst47'=>47.00,
                        'zhst48'=>47.00,
                        'zhst49'=>47.00,
                    ],    				
                    'zhsit'=>[
                        'zhsit1'=>47.00,
                        'zhsit2'=>47.00,
                        'zhsit3'=>47.00,
                        'zhsit4'=>47.00,
                        'zhsit5'=>47.00,
                        'zhsit6'=>47.00,
                        'zhsit7'=>47.00,
                        'zhsit8'=>47.00,
                        'zhsit9'=>47.00,
                        'zhsit10'=>47.00,
                        'zhsit11'=>47.00,
                        'zhsit12'=>47.00,
                        'zhsit13'=>47.00,
                        'zhsit14'=>47.00,
                        'zhsit15'=>47.00,
                        'zhsit16'=>47.00,
                        'zhsit17'=>47.00,
                        'zhsit18'=>47.00,
                        'zhsit19'=>47.00,
                        'zhsit20'=>47.00,

                        'zhsit21'=>47.00,
                        'zhsit22'=>47.00,
                        'zhsit23'=>47.00,
                        'zhsit24'=>47.00,
                        'zhsit25'=>47.00,
                        'zhsit26'=>47.00,
                        'zhsit27'=>47.00,
                        'zhsit28'=>47.00,
                        'zhsit29'=>47.00,
                        'zhsit30'=>47.00,

                        'zhsit31'=>47.00,
                        'zhsit32'=>47.00,
                        'zhsit33'=>47.00,
                        'zhsit34'=>47.00,
                        'zhsit35'=>47.00,
                        'zhsit36'=>47.00,
                        'zhsit37'=>47.00,
                        'zhsit38'=>47.00,
                        'zhsit39'=>47.00,
                        'zhsit40'=>47.00,

                        'zhsit41'=>47.00,
                        'zhsit42'=>47.00,
                        'zhsit43'=>47.00,
                        'zhsit44'=>47.00,
                        'zhsit45'=>47.00,
                        'zhsit46'=>47.00,
                        'zhsit47'=>47.00,
                        'zhsit48'=>47.00,
                        'zhsit49'=>47.00,
                    ],    				
                    'zhwt'=>[
                        'zhwt1'=>47.00,
                        'zhwt2'=>47.00,
                        'zhwt3'=>47.00,
                        'zhwt4'=>47.00,
                        'zhwt5'=>47.00,
                        'zhwt6'=>47.00,
                        'zhwt7'=>47.00,
                        'zhwt8'=>47.00,
                        'zhwt9'=>47.00,
                        'zhwt10'=>47.00,
                        'zhwt11'=>47.00,
                        'zhwt12'=>47.00,
                        'zhwt13'=>47.00,
                        'zhwt14'=>47.00,
                        'zhwt15'=>47.00,
                        'zhwt16'=>47.00,
                        'zhwt17'=>47.00,
                        'zhwt18'=>47.00,
                        'zhwt19'=>47.00,
                        'zhwt20'=>47.00,

                        'zhwt21'=>47.00,
                        'zhwt22'=>47.00,
                        'zhwt23'=>47.00,
                        'zhwt24'=>47.00,
                        'zhwt25'=>47.00,
                        'zhwt26'=>47.00,
                        'zhwt27'=>47.00,
                        'zhwt28'=>47.00,
                        'zhwt29'=>47.00,
                        'zhwt30'=>47.00,

                        'zhwt31'=>47.00,
                        'zhwt32'=>47.00,
                        'zhwt33'=>47.00,
                        'zhwt34'=>47.00,
                        'zhwt35'=>47.00,
                        'zhwt36'=>47.00,
                        'zhwt37'=>47.00,
                        'zhwt38'=>47.00,
                        'zhwt39'=>47.00,
                        'zhwt40'=>47.00,

                        'zhwt41'=>47.00,
                        'zhwt42'=>47.00,
                        'zhwt43'=>47.00,
                        'zhwt44'=>47.00,
                        'zhwt45'=>47.00,
                        'zhwt46'=>47.00,
                        'zhwt47'=>47.00,
                        'zhwt48'=>47.00,
                        'zhwt49'=>47.00,
                    ],    				
                    'zhlt'=>[
                        'zhlt1'=>47.00,
                        'zhlt2'=>47.00,
                        'zhlt3'=>47.00,
                        'zhlt4'=>47.00,
                        'zhlt5'=>47.00,
                        'zhlt6'=>47.00,
                        'zhlt7'=>47.00,
                        'zhlt8'=>47.00,
                        'zhlt9'=>47.00,
                        'zhlt10'=>47.00,
                        'zhlt11'=>47.00,
                        'zhlt12'=>47.00,
                        'zhlt13'=>47.00,
                        'zhlt14'=>47.00,
                        'zhlt15'=>47.00,
                        'zhlt16'=>47.00,
                        'zhlt17'=>47.00,
                        'zhlt18'=>47.00,
                        'zhlt19'=>47.00,
                        'zhlt20'=>47.00,

                        'zhlt21'=>47.00,
                        'zhlt22'=>47.00,
                        'zhlt23'=>47.00,
                        'zhlt24'=>47.00,
                        'zhlt25'=>47.00,
                        'zhlt26'=>47.00,
                        'zhlt27'=>47.00,
                        'zhlt28'=>47.00,
                        'zhlt29'=>47.00,
                        'zhlt30'=>47.00,

                        'zhlt31'=>47.00,
                        'zhlt32'=>47.00,
                        'zhlt33'=>47.00,
                        'zhlt34'=>47.00,
                        'zhlt35'=>47.00,
                        'zhlt36'=>47.00,
                        'zhlt37'=>47.00,
                        'zhlt38'=>47.00,
                        'zhlt39'=>47.00,
                        'zhlt40'=>47.00,

                        'zhlt41'=>47.00,
                        'zhlt42'=>47.00,
                        'zhlt43'=>47.00,
                        'zhlt44'=>47.00,
                        'zhlt45'=>47.00,
                        'zhlt46'=>47.00,
                        'zhlt47'=>47.00,
                        'zhlt48'=>47.00,
                        'zhlt49'=>47.00,
                    ],
                ],
                'zhm'=>[
                    'zhm1'=>8.2,
                    'zhm2'=>8.2,
                    'zhm3'=>8.2,
                    'zhm4'=>8.2,
                    'zhm5'=>8.2,
                    'zhm6'=>8.2,
                    'zhm7'=>8.2,
                    'zhm8'=>8.2,
                    'zhm9'=>8.2,
                    'zhm10'=>8.2,
                    'zhm11'=>8.2,
                    'zhm12'=>8.2,
                    'zhm13'=>8.2,
                    'zhm14'=>8.2,
                    'zhm15'=>8.2,
                    'zhm16'=>8.2,
                    'zhm17'=>8.2,
                    'zhm18'=>8.2,
                    'zhm19'=>8.2,
                    'zhm20'=>8.2,

                    'zhm21'=>8.2,
                    'zhm22'=>8.2,
                    'zhm23'=>8.2,
                    'zhm24'=>8.2,
                    'zhm25'=>8.2,
                    'zhm26'=>8.2,
                    'zhm27'=>8.2,
                    'zhm28'=>8.2,
                    'zhm29'=>8.2,
                    'zhm30'=>8.2,

                    'zhm31'=>8.2,
                    'zhm32'=>8.2,
                    'zhm33'=>8.2,
                    'zhm34'=>8.2,
                    'zhm35'=>8.2,
                    'zhm36'=>8.2,
                    'zhm37'=>8.2,
                    'zhm38'=>8.2,
                    'zhm39'=>8.2,
                    'zhm40'=>8.2,

                    'zhm41'=>8.2,
                    'zhm42'=>8.2,
                    'zhm43'=>8.2,
                    'zhm44'=>8.2,
                    'zhm45'=>8.2,
                    'zhm46'=>8.2,
                    'zhm47'=>8.2,
                    'zhm48'=>8.2,
                    'zhm49'=>8.2,
                ],
                'tm'=>[
                    'tm1'=>48.80,
                    'tm2'=>48.80,
                    'tm3'=>48.80,
                    'tm4'=>48.80,
                    'tm5'=>48.80,
                    'tm6'=>48.80,
                    'tm7'=>48.80,
                    'tm8'=>48.80,
                    'tm9'=>48.80,
                    'tm10'=>48.80,
                    'tm11'=>48.80,
                    'tm12'=>48.80,
                    'tm13'=>48.80,
                    'tm14'=>48.80,
                    'tm15'=>48.80,
                    'tm16'=>48.80,
                    'tm17'=>48.80,
                    'tm18'=>48.80,
                    'tm19'=>48.80,
                    'tm20'=>48.80,

                    'tm21'=>48.80,
                    'tm22'=>48.80,
                    'tm23'=>48.80,
                    'tm24'=>48.80,
                    'tm25'=>48.80,
                    'tm26'=>48.80,
                    'tm27'=>48.80,
                    'tm28'=>48.80,
                    'tm29'=>48.80,
                    'tm30'=>48.80,

                    'tm31'=>48.80,
                    'tm32'=>48.80,
                    'tm33'=>48.80,
                    'tm34'=>48.80,
                    'tm35'=>48.80,
                    'tm36'=>48.80,
                    'tm37'=>48.80,
                    'tm38'=>48.80,
                    'tm39'=>48.80,
                    'tm40'=>48.80,

                    'tm41'=>48.80,
                    'tm42'=>48.80,
                    'tm43'=>48.80,
                    'tm44'=>48.80,
                    'tm45'=>48.80,
                    'tm46'=>48.80,
                    'tm47'=>48.80,
                    'tm48'=>48.80,
                    'tm49'=>48.80,    				
                ],
                'zhy'=>[
                    'zhy5zh1'=>2.35,
                    'zhy6zh1'=>2.28,
                    'zhy7zh1'=>2.27,
                    'zhy8zh1'=>2.32,
                    'zhy9zh1'=>2.41,
                    'zhy10zh1'=>2.51,
                ],
            ],
    	];
        file_put_contents(dirname(dirname(__FILE__)).'./send.json', json_encode($odds));
    	$trend = "
    			CREATE TABLE `bc_games_six_trend` (
    			`id` int(8) NOT NULL AUTO_INCREMENT,
                `issue` int(10) NOT NULL COMMENT '期号',
                `sum` int(3) NOT NULL COMMENT '总和',
                `dsh` varchar(24) NOT NULL COMMENT '单双',
                `dax` varchar(24) NOT NULL COMMENT '大小',
                `qsb` varchar(24) NOT NULL COMMENT '七色波',
                `tm` int(3) NOT NULL COMMENT '特码',
                `tmdxdsh` varchar(24) NOT NULL COMMENT '特码大小单双',
                `hshdxdsh` varchar(24) NOT NULL COMMENT '合数大小单双',
                `tmshx` varchar(24) NOT NULL COMMENT '特码生肖',
                `tmwdx` varchar(24) NOT NULL COMMENT '特码尾大小',
                `tmwx` varchar(24) NOT NULL COMMENT '五行',
                `tmsb` varchar(24) NOT NULL COMMENT '特码色波',
    			PRIMARY KEY(`id`)
    			)ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT '六合彩基本走势'
    			";
    	$animal = "
    			CREATE TABLE `bc_games_six_animal` (
    			`id` int(8) NOT NULL AUTO_INCREMENT,
                `issue` varchar(24) NOT NULL COMMENT '期号',
                `zh1` varchar(24) NOT NULL COMMENT '正一',
                `zh2` varchar(24) NOT NULL COMMENT '正二',
                `zh3` varchar(24) NOT NULL COMMENT '正三',
                `zh4` varchar(24) NOT NULL COMMENT '正四',
                `zh5` varchar(24) NOT NULL COMMENT '正五',
                `zh6` varchar(24) NOT NULL COMMENT '正六',
                `tm` varchar(24) NOT NULL COMMENT '特码',
                `shu` int(1) NOT NULL COMMENT '鼠单期出现次数',
                `niu` int(1) NOT NULL COMMENT '牛单期出现次数',
                `hu` int(1) NOT NULL COMMENT '虎单期出现次数',
                `tu` int(1) NOT NULL COMMENT '兔单期出现次数',
                `long` int(1) NOT NULL COMMENT '龙单期出现次数',
                `she` int(1) NOT NULL COMMENT '蛇单期出现次数',
                `ma` int(1) NOT NULL COMMENT '马单期出现次数',
                `yang` int(1) NOT NULL COMMENT '羊单期出现次数',
                `hou` int(1) NOT NULL COMMENT '猴单期出现次数',
                `ji` int(1) NOT NULL COMMENT '鸡单期出现次数',
                `gou` int(1) NOT NULL COMMENT '狗单期出现次数',
                `zhu` int(1) NOT NULL COMMENT '猪单期出现次数',                
    			PRIMARY KEY(`id`)
    			)ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT '六合彩生肖'
    			";

        $color = "
                CREATE TABLE `bc_games_six_color` (
                `id` int(8) NOT NULL AUTO_INCREMENT,
                `issue` int(10) NOT NULL COMMENT '期号',
                `zh1` varchar(24) NOT NULL COMMENT '正一',
                `zh2` varchar(24) NOT NULL COMMENT '正二',
                `zh3` varchar(24) NOT NULL COMMENT '正三',
                `zh4` varchar(24) NOT NULL COMMENT '正四',
                `zh5` varchar(24) NOT NULL COMMENT '正五',
                `zh6` varchar(24) NOT NULL COMMENT '正六',
                `tm` varchar(24) NOT NULL COMMENT '特码',
                `sbb` VARCHAR(36) NOT NULL COMMENT '色波比',                 
                PRIMARY KEY(`id`)
                ) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT '六合彩色波'
                ";

        $dice = "
            CREATE TABLE `bc_games_six_dice_history`(
            `id` int (8) NOT NULL AUTO_INCREMENT,
            `issue` int (8) NOT NULL COMMENT '期号',
            `time` VARCHAR (24) NOT NULL COMMENT '日期字符串',
            `code` VARCHAR (124) NOT NULL COMMENT '开奖号码',
            `wx` VARCHAR (8) NOT NULL COMMENT '五行',
            `tt` VARCHAR (8) NOT NULL COMMENT '特头',
            `tw` VARCHAR (8) NOT NULL COMMENT '特尾',
            PRIMARY KEY(`id`)
            )ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT '六合彩色子历史记录'
        ";

    	Db::transaction(function()use($check,$trend,$color,$animal,$dice){
    		Db::batchQuery($check);
    		Db::query($trend);
			Db::query($color);
    		Db::query($animal);   
            Db::query($dice); 		
    	});

    }
    //检测是否有足够的金币创建订单
    public function createCheck($allGold,$id){
        $gold = Db::name('user_user')->where(['id'=>$id])->value('gold');
        $remaining = $gold - $allGold;
        if($remaining >=0){
            $result = [
                'status'=>true,
                'remaining'=>$remaining,
            ];
        }
        else{
            $result = [
                'status'=>false,
                'remaining'=>$gold,
            ];
        }
        return $result;
    }
    //数据结构
    public function dataType(){
        $playerData = [
            "11"=>[
                "0"=>[//两面盘
                    "id"=>11,
                    "type"=>"lm",
                    "leiXin"=>"",
                    "gold"=>10,
                    "allGold"=>10,
                    "number"=>'lmtedan',//押注按钮
                ],
                "1"=>[//七码五行
                    "id"=>11,
                    "type"=>"qmwx",
                    "gold"=>10,
                    "allGold"=>10,
                    "number"=>'qmd0sh7',//押注按钮
                ],
                "2"=>[//尾数
                    "id"=>11,
                    "type"=>"wsh",
                    "gold"=>10,
                    "allGold"=>10,
                    "number"=>'twsht1',//押注按钮
                ],
                "3"=>[//色波
                    "id"=>11,
                    "type"=>"sb",
                    "gold"=>10,
                    "allGold"=>10,
                    "number"=>'ssbhb_hong',//押注按钮
                ],
                "4"=>[//合肖
                    "id"=>11,
                    "type"=>"hq",
                    "gold"=>10,
                    "allGold"=>10,
                    "number"=>[                        
                        "shu",
                        "hu",
                        "tu",
                        "long",
                        "she",
                        "hou"
                    ],//押注按钮
                    "leixin"=>"ysh",//合肖的类型，这个比较特别
                ],
                "5"=>[//生肖
                    "id"=>11,
                    "type"=>"shx",
                    "gold"=>10,
                    "allGold"=>10,
                    "number"=>'shxshu',//押注按钮
                ],
                "6"=>[//自选不中
                    "id"=>11,
                    "type"=>"zxbzh",
                    "gold"=>10,
                    "allGold"=>10,
                    "number"=>[1,2,3,4,5],//押注按钮
                    "leiXin"=>"zxbzh5bzh",//下注小玩法
                ],
                "7"=>[//连肖连尾
                    "id"=>11,
                    "type"=>"lqlw",
                    "gold"=>10,
                    "allGold"=>10,
                    "number"=>['shu','niu'],//押注按钮
                    "leiXin"=>"2lq",//下注小玩法
                ],
                "8"=>[//连码
                    "id"=>11,
                    "type"=>"lma",
                    "gold"=>10,
                    "allGold"=>10,
                    "number"=>[01,02,03,04],//押注按钮
                    "leiXin"=>"lmasiqzh",//下注小玩法
                ],
                "9"=>[//正码过关
                    "id"=>11,
                    "type"=>"zhmgg",
                    "gold"=>10,
                    "allGold"=>10,
                    "number"=>["zhyd_dan","zherd_dan"],//押注按钮
                ],
                "10"=>[//
                    "id"=>11,
                    "type"=>"zhm1-6",//正码1-6
                    "gold"=>10,
                    "allGold"=>10,
                    "number"=>"zh1d_dan",//押注按钮
                    "leiXin"=>"zh1",
                ],
                "11"=>[//
                    "id"=>11,
                    "type"=>"zhmt",//正码特
                    "gold"=>10,
                    "allGold"=>10,
                    "number"=>1,//押注按钮
                    "leiXin"=>"zhyt",
                ],
                "12"=>[//
                    "id"=>11,
                    "type"=>"zhm",//正码
                    "gold"=>10,
                    "allGold"=>10,
                    "number"=>1,//押注按钮
                ],
                "13"=>[//
                    "id"=>11,
                    "type"=>"tm",//特码
                    "gold"=>10,
                    "allGold"=>10,
                    "number"=>1,//押注按钮
                ],
                "14"=>[//
                    "id"=>11,
                    "type"=>"zhy",//中一
                    "gold"=>10,
                    "allGold"=>10,
                    "number"=>[1,2,3,4,5],//押注按钮
                    "leiXin"=>"zhy5zh1",
                ],

            ],

        ];

        $diceResult = self::diceResult();

        return $playerData;  
    }

    }
