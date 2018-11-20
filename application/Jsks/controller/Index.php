<?php
namespace app\Jsks\controller;
use think\Controller;
use think\facade\Session;
use think\Db;
use think\facade\Request;
use app\Jsks\controller\Active;
use app\Jsks\model\Games_jsks_odds as Odds;
use app\MyCommon\controller\Gameapi;//获取游戏开奖号码接口
/*
    ** 最近游戏开出的色子记录 recentInfo();
    ** 生成订单     createOrder();
    ** 取消订单     destroyOrder();
    ** 获取历史记录  gameHistory();
    ** 和值的分析表  sumAnalyse();
    ** 桌面信息      deskInfo();
    ** 近期开奖信息  recentOpen()
    ** 下注历史  betHistory();
    ** 创建订单时检测是否有足够的金币 createCheck($allGold, $id)
*/

class Index extends Controller
// class Index
{
    protected $function;
    protected $dice; //开奖详细
    public function __construct()
    {
        $this->active = new Active();
    }

    //测试
    public function makeTest(){
        // 随机100期测试效果
        $time = time();
        $expect = 2018118;

        for($i = 0; $i<=100;$i++){
            $opencode = [];
            $opencode[] = rand(1,6);
            $opencode[] = rand(1,6);
            $opencode[] = rand(1,6);
            $time--;

            $opencode = implode(',',$opencode);
    
            $data = [
                'expect' => $expect--,
                'opencode' => $opencode,
                'opentime' => date("Y-m-d H:i:s",$time),
            ];
            $dice = explode(",", $this->dice['data']['data'][0]['opencode']);
            
            // 记录和值走向 table:games_jsks_sum_trend
            $this->active->recordeSumTrend($data);
            // 记录基本走向 table:games_jsks_basic_trend
            $this->active->recordeBasicTrend($data);
            //记录开奖结果 table:games_jsks_dice_history
            $this->active->recordeDiceHistory($data);
    }
}



    //直接获取游戏结果
    public function diceResult(){
        // 采集数据
        // echo "caijishuju--\n";
        // $dice = new Gameapi();
        // $this->dice = $dice->getGameData('jsks');
        // $data = $this->dice['data']['data'][0];
        // $opencode = explode(",", $this->dice['data']['data'][0]['opencode']);
        // $dice = $opencode;
        // echo "caijishuju done--\n";
        // 测试
        $opencode = [];
        $opencode[] = rand(1,6);
        $opencode[] = rand(1,6);
        $opencode[] = rand(1,6);

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

        //记录和值走向 table:games_jsks_sum_trend
        $this->active->recordeSumTrend($data);
        echo "recordeSumHistory--\n";
        //记录基本走向 table:games_jsks_basic_trend
        $this->active->recordeBasicTrend($data);
        echo "recordeBasicHistory--\n";
        //记录开奖结果 table:games_jsks_dice_history
        $this->active->recordeDiceHistory($data);
        echo "recordeDiceHistory--\n";
          
        $gameResult = [
            "sum"=>$this->active->sum($dice),
            "size"=>$this->active->size($dice),
            "threeDiffrent"=>$this->active->threeDiffrent($dice),
            "threeConAll"=>$this->active->threeConAll($dice),
            "threeSameDb"=>$this->active->threeSameDb($dice),
            "threeSameSg"=>$this->active->threeSameSg($dice),
            "twoSameDb"=>$this->active->twoSameDb($dice),
            "twoSameSg"=>$this->active->twoSameSg($dice),
            "twoDiffrent"=>$this->active->twoDiffrent($dice)
        ];
        echo "return GameResult--\n";
        return $gameResult;
    }

    // 游戏结算    //bet就是workman传过来的数据
    // public function begin()
    public function begin($playerData,$diceResult)
    {

        $odds = file_get_contents(dirname(dirname(__FILE__))."./odds.json");
        $odds = json_decode($odds,true);
        foreach($playerData as $conId => $player){
            
            $playerWin = 0;//单个玩家赢的金币
            //求出每一注订单的下注结果
            foreach($player as $orderNumber=>$order){

                //更新用户剩余金币，以数据库为准
                $playerData[$conId]['remaining'] = $this->active->refreshRemaining($order);

                $win = 0;
                //求出每一个结果 
                $type = $order['type'];
                $leiXin = $order['leiXin'];
                switch($type){

                    //和值   --对过
                    case 'sum':
                    switch($leiXin){
                        case "sum_sum":
                            if(in_array($diceResult["sum"],$order['number'])){
                                $index = 'sum'.$diceResult["sum"];
                                $win = $order['gold']*$odds[$index];
                            }
                        break;
                        
                        //大,小，大单，小单，大双，小双
                        case "sum_size":
                        foreach($order['number'] as $key => $vo){
                            if(in_array($vo, $diceResult["size"])){
                                $win += $order['gold']*$odds[$vo];
                            }
                        }
                        break;
                    }
                    break;
                    //三不同号   --对过 不管买多少个号码，只能中三个，即最多中一注
                    case 'threeDiffrent':
                        switch($leiXin){
                            case "threeDiffrent-commom"://标准选号
                                if($diceResult["threeDiffrent"]['code']==0){
                                    break;
                                }
                                else{
                                    $sum = 0;
                                    foreach($order['number'] as $key => $vo){
                                        if(in_array($vo, $diceResult["threeDiffrent"]['dice'])){
                                            $sum++;
                                        }
                                    }
                                    if($sum == 3){
                                        $win = $order['gold']*$odds["threeDiffrent"];
                                    }
                                }
                            break;

                            case "threeDiffrent-handy"://手动选号
                                if($diceResult["threeDiffrent"]['code']==0){
                                    break;
                                }
                                else{
                                    foreach($order["number"] as $index => $number){
                                        $numberArr = [];
                                        $numberArr[] = (int)($number/100);//求出百位
                                        $numberArr[] = (int)($number/10)%10;//求出十位
                                        $numberArr[] = $key%10;//求出个位
                                        $sum = 0;
                                        foreach($numberArr as $keyNum => $voNum){
                                            if(in_array($voNum, $diceResult["threeDiffrent"]['dice'])){
                                                $sum++;
                                            }
                                        }
                                        if($sum == 3){
                                            $win += $order['gold']*$odds["threeDiffrent"];
                                        }
                                    }
                                }
                            }
                    break;

                    //三同号  --对过
                    case 'threeSame':
                        if($leiXin == "threeSame-threeSameSg"){//三同号单选
                            if($diceResult["threeSameSg"]['code']==0){//不是三同号
                                break;
                            }
                            else{
                                if(in_array($diceResult["threeSameSg"]['dice'], $order['number'])){//包含相同的三同号
                                    $win = $order['gold']*$odds["threeSameSg"];
                                }
                            }
                            break;
                        }
                        else if($leiXin == "threeSame-threeSameDb"){//三同号复选
                            if($diceResult["threeSameDb"]['code']==0){//不是三同号
                                break;
                            }
                            else{
                                    $win = $order['gold']*$odds["threeSameDb"];
                                }
                            }
                    break;


                    //三连号通选 --对过
                    case 'threeConAll':
                    if($diceResult["threeConAll"]['code']==0){//不是三连号
                        break;
                    }
                    else{
                        $win = $order['gold']*$odds["threeConAll"];
                    }
                    break;

                    //二不同号 --checked
                    case 'twoDiffrent':  
                        switch($leiXin){
                            case "twoDiffrent-commom"://标准选号
                                if($diceResult["twoDiffrent"]['code']==0){
                                    break;
                                }
                                else{
                                    $sum = 0;
                                    foreach($order['number'] as $index => $vo){
                                        if(in_array($vo, $diceResult["twoDiffrent"]['dice'])){
                                            $sum++;
                                        }
                                    }
                                    if($sum == 2){//中了两个号码
                                        $win = $order['gold']*$odds["twoDiffrent"];
                                    }
                                    if($sum == 3){//中了三个号码
                                        $win = $order['gold']*$odds["twoDiffrent"]*3;
                                    }
                                }
                                break;
                            case "twoDiffrent-handy"://手动选号
                                if($diceResult["twoDiffrent"]['code']==0){
                                    break;
                                }
                                else{
                                    foreach($order['number'] as $index=>$number){
                                        $numberArr = [];
                                        $numberArr[] = substr($number,0,1);
                                        $numberArr[] = substr($number,1,1);
                                        $sum = 0;
                                        foreach($numberArr as $keyNum => $voNum){
                                            if(in_array($voNum, $diceResult["twoDiffrent"]['dice'])){
                                                $sum++;
                                            }
                                        }
                                        if($sum == 2){
                                            $win += $order['gold']*$odds["twoDiffrent"];
                                        }
                                    }

                                }
                                break;
                            case "twoDiffrent-dantuo"://胆拖选号
                                if($diceResult['twoDiffrent']['code']==0){//不是二不同号
                                    break;
                                }
                                else{
                                    $sum = 0;
                                    if(!in_array($order['number']['first'],$diceResult['twoDiffrent']['dice'])){//胆号不在数组里
                                        break;
                                    }
                                    else{
                                        foreach($order['number']['second'] as $tuoNumber){
                                            if(in_array($tuoNumber,$diceResult['twoDiffrent']['dice'])){
                                                $win += $order['gold']*$odds['twoDiffrent']; 
                                            }
                                        }
                                    }
                                }
                            }
                    break;

                    //二同号单选  
                    case 'twoSame':
                    switch($leiXin){
                        case 'twoSame-twoSameDb'://复选
                        if($diceResult["twoSameDb"]['code']==0){//不是二同号
                            break;
                        }
                        else{
                            if($diceResult["twoSameDb"]['same'] != $order['number']){
                                break;
                            } 
                            else{
                                $win = $order['gold']*$odds["twoSameDb"];
                            }
                        }
                        break;
    
                        case "twoSame-commom"://标准选号
                        if($diceResult["twoSameSg"]['code']==0){//不是二同号
                            break;
                        }
                        else{
                            //前端传过来的是两位数，后台转一位数结算 例：11 -> 1;
                            $order['number']['first'] = $order['number']['first']%10;
                            if($diceResult["twoSameSg"]['same'] != $order['number']['first']){//没选中相同的号
                                break;
                            } 
                            else{
                                if(in_array($diceResult["twoSameSg"]['diffrent'], $order['number']['second'])){//选中不相同的号
                                    $win = $order['gold']*$odds["twoSameSg"];
                                }   
                            }
                        }
                        break;
                        case "twoSame-handy"://手动选号
                        foreach($order['number'] as $index => $voNumber){
                            //把字符串拆开3个，并且求出相同的跟不同的
                            $a = substr($voNumber,0,1);
                            $b = substr($voNumber,1,1);
                            $c = substr($voNumber,2,1);
                            if($a == $b){
                                $same = $a;
                                $diffrent = $c;
                            }
                            else if($a == $c){
                                $same = $a;
                                $diffrent = $b;
                            }
                            else{//$b == $c
                                $same = $b;
                                $diffrent = $a;
                            }
                        }
                        if($diceResult["twoSameSg"]['code']==0){//不是二同号
                            break;
                        }
                        else{
                            if($diceResult["twoSameSg"]['same'] != $same){//没选中相同的号
                                break;
                            } 
                            else{
                                if($diceResult["twoSameSg"]['diffrent'] == $diffrent){//不相同的号
                                    $win = $order['gold']*$odds["twoSameSg"];
                                }   
                            }
                        }    
                        break;
                    }
                }
                $dice = $this->dice['data']['data'][0];
                var_dump($dice);
                //结算订单 table:games_bet_history:update
                $this->active->recordeBetHistory($orderNumber,$dice,$win);
                $playerWin +=$win;
                $userId = $playerData[$conId][$orderNumber]['id'];
            }
            //清空数组只返回剩余金币
            $remaining = $playerData[$conId]['remaining']+$playerWin;
            
            //更新用户金币数据： table:user_user : update
            $this->active->refleshUser($userId,$remaining);
            //清空数组
            $playerData[$conId] = [];
            //保留remaining
            $playerData[$conId]['remaining'] = $remaining;
        }
        return $playerData;
    }

    /*
    **请求个人的购奖记录
    */
    public function betHistory(){
        $betHistory = Db::name('games_bet_history')->field('id',true)->order('id desc')->limit(0,30)->select();
        return $betHistory;
    }

    //最近游戏记录 table::'games_jsks_dice_history'
    public function recentInfo(){
        $data = Db::name('games_jsks_dice_history')->field('id',true)->order('id desc')->limit(19)->select();
        foreach($data as $key=>$vo){
            $data[$key]['code_img'] = json_decode($data[$key]['code_img'],true);
            $data[$key]['open_code'] = explode(",",$data[$key]['open_code']);
        }
        return $data;
    }
    /*
    *获取开奖结果以及历史走势并保存在worker.php的 $this->gameHistory 中;
    */
    public function gameHistory(){
        //和值有分析表这一步
        $data = [
            'sum'=>[
                '30'=>[
                        'table'=>Db::name('games_jsks_sum_trend')->order('id desc')->limit(0,30)->select(),
                        'analyse'=>self::sumAnalyse(30),
                      ],
                '50'=>[
                        'table'=>Db::name('games_jsks_sum_trend')->order('id desc')->limit(0,50)->select(),
                        'analyse'=>self::sumAnalyse(50),
                      ],
                '100'=>[
                        'table'=>Db::name('games_jsks_sum_trend')->order('id desc')->limit(0,100)->select(),
                        'analyse'=>self::sumAnalyse(100),
                      ]                     
            ],
            'basic'=>[
                '30'=>Db::name('games_jsks_basic_trend')->order('id desc')->limit(0,30)->select(),
                '50'=>Db::name('games_jsks_basic_trend')->order('id desc')->limit(0,50)->select(),
                '100'=>Db::name('games_jsks_basic_trend')->order('id desc')->limit(0,100)->select(),                
            ],
        ];
        return $data;
    }

    //和值的历史分析表
    public function sumAnalyse($amount){
        $allSum = Db::name('games_jsks_sum_trend')->column('sum');//历史所有和值的记录
        $thisSum = Db::name('games_jsks_sum_trend')->order('id desc')->limit(0,$amount)->column('sum');//本次查询和值记录
        
        $count = array_count_values($thisSum);
        $sumAnalyse = [
            'times' =>[//单次查询出现次数
                '3'=>isset($count[3])?$count[3]:0,
                '4'=>isset($count[4])?$count[4]:0,
                '5'=>isset($count[5])?$count[5]:0,
                '6'=>isset($count[6])?$count[6]:0,
                '7'=>isset($count[7])?$count[7]:0,
                '8'=>isset($count[8])?$count[8]:0,
                '9'=>isset($count[9])?$count[9]:0,
                '10'=>isset($count[10])?$count[10]:0,
                '11'=>isset($count[11])?$count[11]:0,
                '12'=>isset($count[12])?$count[12]:0,
                '13'=>isset($count[13])?$count[13]:0,
                '14'=>isset($count[14])?$count[14]:0,
                '15'=>isset($count[15])?$count[15]:0,
                '16'=>isset($count[16])?$count[16]:0,
                '17'=>isset($count[17])?$count[17]:0,
                '18'=>isset($count[18])?$count[18]:0,
            ],
            'averageMiss'=>[//历史平均遗漏  总数/出现次数+1
                '3'=>floor(count($allSum)/(count(Db::name('games_jsks_sum_trend')->where('sum_3','0')->column('sum_3'))+1)),
                '4'=>floor(count($allSum)/(count(Db::name('games_jsks_sum_trend')->where('sum_4','0')->column('sum_4'))+1)),
                '5'=>floor(count($allSum)/(count(Db::name('games_jsks_sum_trend')->where('sum_5','0')->column('sum_5'))+1)),
                '6'=>floor(count($allSum)/(count(Db::name('games_jsks_sum_trend')->where('sum_6','0')->column('sum_6'))+1)),
                '7'=>floor(count($allSum)/(count(Db::name('games_jsks_sum_trend')->where('sum_7','0')->column('sum_7'))+1)),
                '8'=>floor(count($allSum)/(count(Db::name('games_jsks_sum_trend')->where('sum_8','0')->column('sum_8'))+1)),
                '9'=>floor(count($allSum)/(count(Db::name('games_jsks_sum_trend')->where('sum_9','0')->column('sum_9'))+1)),
                '10'=>floor(count($allSum)/(count(Db::name('games_jsks_sum_trend')->where('sum_10','0')->column('sum_10'))+1)),
                '11'=>floor(count($allSum)/(count(Db::name('games_jsks_sum_trend')->where('sum_11','0')->column('sum_11'))+1)),
                '12'=>floor(count($allSum)/(count(Db::name('games_jsks_sum_trend')->where('sum_12','0')->column('sum_12'))+1)),
                '13'=>floor(count($allSum)/(count(Db::name('games_jsks_sum_trend')->where('sum_13','0')->column('sum_13'))+1)),
                '14'=>floor(count($allSum)/(count(Db::name('games_jsks_sum_trend')->where('sum_14','0')->column('sum_14'))+1)),
                '15'=>floor(count($allSum)/(count(Db::name('games_jsks_sum_trend')->where('sum_15','0')->column('sum_15'))+1)),
                '16'=>floor(count($allSum)/(count(Db::name('games_jsks_sum_trend')->where('sum_16','0')->column('sum_16'))+1)),
                '17'=>floor(count($allSum)/(count(Db::name('games_jsks_sum_trend')->where('sum_17','0')->column('sum_17'))+1)),
                '18'=>floor(count($allSum)/(count(Db::name('games_jsks_sum_trend')->where('sum_18','0')->column('sum_18'))+1))
            ],
            'maxMiss'=>[//历史记录最大遗漏
                '3'=>max(Db::name('games_jsks_sum_trend')->column('sum_3')),
                '4'=>max(Db::name('games_jsks_sum_trend')->column('sum_4')),
                '5'=>max(Db::name('games_jsks_sum_trend')->column('sum_5')),
                '6'=>max(Db::name('games_jsks_sum_trend')->column('sum_6')),
                '7'=>max(Db::name('games_jsks_sum_trend')->column('sum_7')),
                '8'=>max(Db::name('games_jsks_sum_trend')->column('sum_8')),
                '9'=>max(Db::name('games_jsks_sum_trend')->column('sum_9')),
                '10'=>max(Db::name('games_jsks_sum_trend')->column('sum_10')),
                '11'=>max(Db::name('games_jsks_sum_trend')->column('sum_11')),
                '12'=>max(Db::name('games_jsks_sum_trend')->column('sum_12')),
                '13'=>max(Db::name('games_jsks_sum_trend')->column('sum_13')),
                '14'=>max(Db::name('games_jsks_sum_trend')->column('sum_14')),
                '15'=>max(Db::name('games_jsks_sum_trend')->column('sum_15')),
                '16'=>max(Db::name('games_jsks_sum_trend')->column('sum_16')),
                '17'=>max(Db::name('games_jsks_sum_trend')->column('sum_17')),
                '18'=>max(Db::name('games_jsks_sum_trend')->column('sum_18'))
            ],
            'dalianOut'=>[//当次查询条数最大连出
                '3'=>0,
                '4'=>0,
                '5'=>0,
                '6'=>0,
                '7'=>0,
                '8'=>0,
                '9'=>0,
                '10'=>0,
                '11'=>0,
                '12'=>0,
                '13'=>0,
                '14'=>0,
                '15'=>0,
                '16'=>0,
                '17'=>0,
                '18'=>0
            ]
        ];
        $dalianOut = [
            '3'=>[0],
            '4'=>[0],
            '5'=>[0],
            '6'=>[0],
            '7'=>[0],
            '8'=>[0],
            '9'=>[0],
            '10'=>[0],
            '11'=>[0],
            '12'=>[0],
            '13'=>[0],
            '14'=>[0],
            '15'=>[0],
            '16'=>[0],
            '17'=>[0],
            '18'=>[0]
        ];

        foreach($thisSum as $key=>$vo){
            //相同则增加一个和值连出数并且比上一个大1
            $dalianOut[$vo][count($dalianOut[$vo])] = $dalianOut[$vo][count($dalianOut[$vo])-1]+1;
            //下一个不相同该和值连出数置0
            if($key < count($thisSum)-1){
                if($thisSum[$key]!=$thisSum[$key+1]){
                    $dalianOut[$vo][count($dalianOut[$vo])]=0;
                }
            }
        }

        //取最大连出数返回前台
        foreach($sumAnalyse['dalianOut'] as $key=>$vo){
            $sumAnalyse['dalianOut'][$key] = max($dalianOut[$key]);
        }
         return $sumAnalyse;
    }
    /**
     * 生成下注订单
     * @$order： 前端传来的订单信息
     * @createTime:订单创建时间
     * $orderNumber:订单号
     * $fileOdds:赔率表
     * 
     */
    public function createOrder($order,$createTime,$orderNumber,$fileOdds){
        $type = $order['type'];//大分类
        $name = $order["leiXin"];//类型
        $number = $order["number"];//押注号码的数组
        /**
         * 构造赔率
         */
        $odds = [];
        if($type != "sum"){
            switch($name){
                case "twoSame-twoSameDb"://二同号复选
                    $odds[] = $fileOdds['twoSameDb'];
                break;
                case "twoSame-commom"://二同号标准选号
                    $odds[] = $fileOdds['twoSameSg'];
                break;
                case "twoSame-handy"://二同号手动选号
                $odds[] = $fileOdds['twoSameSg'];
                break;
                case "threeSame-threeSameDb":
                    $odds[] = $fileOdds['threeSameDb'];
                break;
                case "threeSame-threeSameSg":
                    $odds[] = $fileOdds['threeSameSg'];
                break;
                default:
                    $odds[] = $fileOdds[$type];
                break;
            }
        }
        else{
            foreach($number as $key=>$vo){
                $odds[] = $fileOdds[$vo];
            }
        }
        $odds = implode(',',$odds);
        
        //构造玩法名称
        switch($name){
            case "twoSame-commom"://二同号标准选号
                $number = $order['number']['first']."|".implode(',',$order['number']['second']);
            break;
            case "twoDiffrent-dantuo"://二不同号胆拖选号
                $number = $order['number']['first']."|".implode(',',$order['number']['second']);
            break;
            default:
                $number = implode(",",$order['number']);
            break;
        }

        $data = [
            'user_id' =>$order['id'],//用户id
            'issue' => $order['issue'],//押注期号
            'gold'  => $order['zhuShu']."注  ".$order['gold']."金币",//押注金额 注数+金额
            'order_number'=> $orderNumber, //押注单号
            'name'=>$name,//押注名称
            'return_point'=>0,    //返点
            'create_time'=>$createTime,
            'number'=>$number,//押注数据
            'odds'=>$odds,//押注赔率
            "avatar"=>$order['avatar'],//头像
            "key"=>$order['key'],//关键字
            "gameName"=>$order['gameName']//游戏名字
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

    /**
     * 检测金币状态
     */
    public function createCheck($allGold,$id){
        $findGold = Db::name("user_user")->where(['id'=>$id])->value('gold');
        if($findGold >= $allGold){
            //金币足够扣除，返回剩余金币
            $result = [
                "status" => true,
                "remaining" => $findGold - $allGold,
            ];
        }
        else{
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
    public function recentOpen(){
        $recentOpen = Db::name('games_jsks_dice_history')->field('issue,open_time,sum,code_img')->limit(0,30)->order('id desc')->select();
        foreach($recentOpen as $key => $vo){
            $recentOpen[$key]['code_img'] = json_decode($recentOpen[$key]['code_img']);
        }
        return $recentOpen;
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
            echo "---destroy faile---\n";
        }
        return $result;
    }


    //返回桌面信息 table::
    public function deskInfo(){
        $data = file_get_contents(dirname(dirname(__FILE__))."/send.json");
        return json_decode($data,true);
    }

   //数据结构
    public function dataType(){
        
        $playerData = [
            '2'=>[
                '0'=>[
                    "id" => 11, //用户的id
                    'type'=>"sum",
                    'leiXin'=>"sum-sum", //和值-和值
                    "number"=>['sum_3','sum_4'],                     
                    "gold" =>10,
                    "allGold"=>50,//下注总金额
                    "total"=>5,//总注数            
                    "remaining"=>1000//剩余金币user_user表查询
                ],
                '1'=>[
                    "id" => 11, //用户的id
                    "type"=>"sum",
                    'leiXin'=>"sum-size",//和值两面
                    "number"=>['big','small','bigSingle','bigDouble','smallSingle','smallDouble','single','double'],   //和值-大小单双
                    "gold" =>10,
                    "allGold"=>50,//下注总金额
                    "total"=>5,//总注数            
                    "remaining"=>1000//剩余金币user_user表查询
                ],
                '2'=>[
                    "id" => 11, //用户的id
                    "type"=>"threeDiffrent",
                    "leiXin"=>"threeDiffrent-commom",
                    "number"=>['1','2','3','4'], //三不同号标准选号
                    "gold" =>10,
                    "allGold"=>50,//下注总金额
                    "total"=>5,//总注数            
                    "remaining"=>1000//剩余金币user_user表查询
                ],
                '3'=>[
                    "id" => 11, //用户的id
                    "type"=>"threeDiffrent",
                    "leiXin"=>"threeDiffrent-handy",
                    "number"=>['123','234','345','456'], //三不同号手动选号
                    "gold" =>10,
                    "allGold"=>50,//下注总金额
                    "total"=>5,//总注数            
                    "remaining"=>1000//剩余金币user_user表查询
                ],
                '4'=>[
                    "id" => 11, //用户的id
                    "type"=>"twoSame",
                    "leiXin"=>"twoSame-twoSameDb",
                    "number" =>['1','2','3'],        //二同号复选
                    "gold" =>10,
                    "allGold"=>50,//下注总金额
                    "total"=>5,//总注数            
                    "remaining"=>1000//剩余金币user_user表查询
                ],
                '5'=>[
                    "id" => 11, //用户的id
                    "type"=>"twoSame",
                    "leiXin"=>"twoSame-commom", //二同号标准选号
                    "number" =>['first'=>1,'second'=>[2,3,4,5]],        //二同号标准选号
                    "gold" =>10,
                    "allGold"=>50,//下注总金额
                    "total"=>5,//总注数            
                    "remaining"=>1000//剩余金币user_user表查询
                ],
                '6'=>[
                    "id" => 11, //用户的id
                    "type"=>"twoSame",
                    "leiXin"=>"twoSame-handy",
                    "number" =>['112','211','322'],        //二同号手动选号
                    "gold" =>10,
                    "allGold"=>50,//下注总金额
                    "total"=>5,//总注数            
                    "remaining"=>1000//剩余金币user_user表查询
                ],
                '7'=>[
                    "id" => 11, //用户的id
                    "type"=>"twoDiffrent",
                    "leiXin"=>"twoDiffrent-commom",
                    "number"=>['1','2','3','4','5'], //二不同号标准选号
                    "gold" =>10,
                    "allGold"=>50,//下注总金额
                    "total"=>5,//总注数            
                    "remaining"=>1000//剩余金币user_user表查询
                ],
                '8'=>[
                    "id" => 11, //用户的id
                    "type"=>"twoDiffrent",
                    "leiXin"=>"twoDiffrent-handy",
                    "number"=>['12','23','32','45','65'], //二不同号手动选号
                    "gold" =>10,
                    "allGold"=>50,//下注总金额
                    "total"=>5,//总注数            
                    "remaining"=>1000//剩余金币user_user表查询
                ],
                '9'=>[
                    "id" => 11, //用户的id
                    "type"=>"twoDiffrent",
                    "leiXin"=>"twoDiffrent-dantuo",
                    "number"=>['first'=>1,'second'=>['4','2','3']], //二不同号胆拖选号
                    "gold" =>10,
                    "allGold"=>50,//下注总金额
                    "total"=>5,//总注数            
                    "remaining"=>1000//剩余金币user_user表查询
                ],
                '10'=>[
                    "id" => 11, //用户的id
                    "type"=>"threeConAll",
                    "leiXin"=>"threeConAll-threeConAllCommom",  
                    "number"=>['123','234','345','456'],        //三连号通选
                    "gold" =>10,
                    "allGold"=>50,//下注总金额
                    "total"=>5,//总注数            
                    "remaining"=>1000//剩余金币user_user表查询
                ],
                '11'=>[
                    "id" => 11, //用户的id
                    "type"=>"threeSame",
                    "leiXin"=>"threeSame-threeSameSg",
                    "number"=>['1','2','3','4'],         //三同号单选
                    "gold" =>10,
                    "allGold"=>50,//下注总金额
                    "total"=>5,//总注数            
                    "remaining"=>1000//剩余金币user_user表查询
                ],
                '12'=>[
                    "id" => 11, //用户的id
                    "type"=>"threeSame",
                    "leiXin"=>"threeSame-threeSameDb",
                    "number"=>['111','222','333','444','555','666'],         //三同号通选
                    "gold" =>10,
                    "allGold"=>50,//下注总金额
                    "total"=>5,//总注数            
                    "remaining"=>1000//剩余金币user_user表查询
                ],
            ]
        ];  

        // $data = [];
        $result = [];
        $i=-10;
        do{
            $diceResult = self::diceResult();
            $result[] = self::begin($playerData,$diceResult);
    
            $i++;
        }while($i);
        dump($result);  
    }

    }
