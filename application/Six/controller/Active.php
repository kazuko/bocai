<?php 
namespace app\Six\controller;
use think\Controller;
use think\facade\Session;
use think\Db;
use think\facade\Request;
/**
 * author :newbrash_Greywolf
 * question_english:Color wave and the zodac is made up of three expression and settlement, although have been said to use three expression for understanding is not good, but please turn three expression for judgment or switch handle compare again, you'll find three expression how simple it is lovely and easy to understand, you have other better way has looked glad
 * question_chinese:色波和生肖是由三元表达式结算的，虽然说一直说使用三元表达式对对理解不好，但是请各位转三元表达式为判断或者开关句柄再对比一下，你们会发现三元表达式是多么的简洁可爱并且易于理解，各位有其它更好的方法还望不吝赐教 
 */

/*  功能函数
    ** 分析号码 analyseCode($code);
    ** 更新用户剩余金币，以数据库为准 refreshRemaining(
    ** 记录用户最新金币 table:user_user  refleshUser(
    ** 结算订单 table:games_bet_history recordeBetHistory($dice)
    **记录基本走势 table:games_six_trend  $this->active->recordeBasicTrend($data,$rule);
    **记录色波 table:games_six_color;    $this->active->recordeColor($data,$rule);
    **记录生肖 table:games_six_animal;   $this->active->recordsAnimal($data,$rule);
    **记录开奖结果 table:games_six_dice_history  $this->active->recordeDiceHistory($data,$rule);
    ** 
*/


 class Active extends Controller
// class Active
{
    //分析号码
    public function analyseCode($code,$rule){
        $codeResult = [
            'lm' => self::lm($code,$rule),
            'qmwx'=>self::qmwx($code,$rule),
            'wsh' =>self::wsh($code,$rule),
            'sb' =>self::sb($code,$rule),
            'shx'=>self::shx($code,$rule),
            'zhm16'=>self::zhm16($code,$rule),
            'zhmt'=>self::zhmt($code),
            'zhm'=>self::zhm($code),
            'tm'=>self::tm($code),
        ];
        
        return $codeResult;
    }
/*******************************************结算两面盘***************************************************/
    public  function lm($code,$rule){
        $lm = [];
        $tm = $code[6];
        $lm[] = $tm == 49?'lmtehe':($tm%2?'lmtedan' : 'lmteshuang');
        $lm[]  = ($tm>=25&&$tm<=48)?'lmteda':(($tm==49)?'lmtehe':'lmtexiao');
        if($tm >= 10){
            if($tm == 49){
                $heSum = 49;
                $weiShu = 49;
            }
            else{
                $heSum = floor($tm/10) + $tm%10;
                $weiShu = $tm%10;
            }
             
        }else{
            $heSum = $tm;
            $weiShu = $tm;
        }
        $lm[] = $heSum == 49?'lmtehehe':($heSum>7?'lmteheda':'lmtehexiao'); 
        $lm[] = $heSum == 49?'lmtehehe':($heSum%2?'lmtehedan':'lmteheshuang'); 
        $lm[] = $weiShu==49?'49':($weiShu>=5?'lmteweida':'lmteweixiao');
        $lm[] = in_array($tm,$rule['jiaqin'])?'lmtejiaqin':'';
        $lm[] = in_array($tm,$rule['yeshou'])?'lmteyeshou':'';
        $lm[] = in_array($tm,$rule['tianqiao'])?'lmtetianqiao':'';
        $lm[] = in_array($tm,$rule['diqiao'])?'lmtediqiao':'';
        $lm[] = in_array($tm,$rule['qianqiao'])?'lmteqianqiao':'';
        $lm[] = in_array($tm,$rule['houqiao'])?'lmtehouqiao':'';
        $lm[] = array_sum($code)>=175?'lmzongda':'lmzongxiao';
        $lm[] = array_sum($code)%2?'lmzongdan':'lmzongshuang';
        return $lm;
    }

/*******************************************结算七码五行***************************************************/
    public function qmwx($code,$rule){
        $qmwx = [];
        $dan = 0;
        $da = 0;
        $tm = $code[6];
        foreach($code as $vo){
            $dan = $vo%2?++$dan:$dan;
            $da = ($vo>=25)?++$da:$da;
        }
        $shuang = 7 - $dan;
        $xiao = 7 - $da;
        $qmwx[] = 'qmd'.$da.'x'.$xiao;
        $qmwx[] = 'qmd'.$dan.'sh'.$shuang;
        $qmwx[] = in_array($tm,$rule['jin'])?'wxjin':(
            in_array($tm,$rule['mu'])?'wxmu':(
                in_array($tm,$rule['shui'])?'wxshui':(
                    in_array($tm,$rule['huo'])?'wxhuo':(
                        in_array($tm,$rule['wxtu'])?'wxtu':'fault'
                    )
                ) 
            )
        );        
        return $qmwx;
    }
/*******************************************结算尾数***************************************************/
    public function wsh($code,$rule){
        $wsh = [];
        $tm = $code[6];

        $tou = floor($tm/10);
        $wei = $tm%10;

        $wsh[] = 'twsht'.$tou;
        $wsh[] = 'twshw'.$wei;

        $wshSum = 0;
        foreach($code as $num){
            $weiShu = $num%10;
            $wsh[] = 'zhtwshw'. $weiShu;
        }
        $wsh = array_unique($wsh);
        return $wsh;
    }

/*******************************************结算色波***************************************************/
    public function sb($code,$rule){
        $tm = $code[6];
        $sb = [];
        $yanse = in_array($tm,$rule['hb'])?'h':
                    (in_array($tm,$rule['lb'])?'l':'lv');
        $daxiao = $tm>=25?'da':'x';//大小
        $danshuang = $tm%2?'d':'sh';
        $sufix = $yanse=="h"?"hong":($yanse=="l"?"lan":($yanse=="lv"?"lv":"color false"));//颜色后缀
        $sb[] = "ssb".$yanse.'b'."_".$sufix;//三色波
        $sb[] = "bb".$yanse.$daxiao."_".$sufix;//半波颜色大小
        $sb[] = "bb".$yanse.$danshuang."_".$sufix;//半波颜色单双
        $sb[] = "bbb".$yanse.$daxiao.$danshuang."_".$sufix;//半半波
        
        //七色波
        $zhYanse = [];//正码颜色
        $tmYanse = '';//特码颜色
        foreach($code as $key=>$number){
            //特码
            if($key != 6){
                $yanse = in_array($number,$rule['hb'])?'h':
                ( in_array($number,$rule['lb'])?'l':'lv');
                 $zhYanse[] = $yanse;//正码颜色
            }else{
                $yanse = in_array($tm,$rule['hb'])?'h':
                   ( in_array($tm,$rule['lb'])?'l':'lv');
                $tmYanse = $yanse;//特码颜色
            }
        }
        $zhYanse = array_count_values($zhYanse);//"color"=>sum,整合索引数组，每种颜色的数量

        $number = [];//每种颜色的数量
        $name = [];//每种颜色的名字
        $he = 0;//初始化为非和局

        //判断是否是和局
        if(count($zhYanse)==2){
            foreach($zhYanse as $key => $vo){
                $number[] = $vo; 
                $name[] = $key;
            }
            echo $tmYanse;
            if($number[0]==$number[1]){//正码只有两种颜色并且与特码颜色不同为和局
                if(($name[0]!=$tmYanse)&&($name[1]!=$tmYanse)){
                    $he = 1;
                    $sb[] = 'qsbhj';
                }
            }
        }
        //不是和局
        if(!$he){
            if(count($zhYanse)==1){
                //六个正码色波一致
                foreach($zhYanse as $key=>$vo){//$key为颜色
                    $sufix = $key=="h"?"hong":($key=="lv"?"lv":($key=="l"?"lan":"color false"));
                    $sb[] = 'qsb'.$key.'b'.'_'.$sufix;
                }
            }
            else{//正码里有两种或者三种颜色非和局
                foreach($zhYanse as $key=>$vo){
                    if($key == $tmYanse){//七个号码只有两种颜色
                        $zhYanse[$key]++;
                    }
                    //把键值对数组装换成数字数组
                    $number[] = $zhYanse[$key];
                }
                $max = max($number);
                foreach($zhYanse as $key=>$vo){//$key为颜色
                    $sufix = $key=="h"?"hong":($key=="lv"?"lv":($key=="l"?"lan":"color false"));
                    if($vo == $max){
                        $sb[] = 'qsb'.$key.'b'.'_'.$sufix;
                    }
                }
            }
        }
        return $sb;
    }
/*******************************************结算合肖***************************************************/
    public function hq($code,$rule,$orderNumber){
        $status = 0;
        $tm = $code[6];
        // var_dump($rule['shXtu']);
        echo $tm."tm\n";
        $shengXiao = '';
        $shengXiao = in_array($tm,$rule['shu'])?'shu':(
            in_array($tm,$rule['niu'])?'niu':(
                in_array($tm,$rule['hu'])?'hu':(
                    in_array($tm,$rule['shXtu'])?'tu':(
                        in_array($tm,$rule['hou'])?'hou':(
                            in_array($tm,$rule['ji'])?'ji':(
                                in_array($tm,$rule['gou'])?'gou':(
                                    in_array($tm,$rule['zhu'])?'zhu':(
                                        in_array($tm,$rule['long'])?'long':(
                                            in_array($tm,$rule['she'])?'she':(
                                                in_array($tm,$rule['ma'])?'ma':(
                                                    in_array($tm,$rule['yang'])?'yang':'fault'
                                                )
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );
        if(in_array($shengXiao,$orderNumber)){
            $status = 1;
        }
        echo $shengXiao."---".$status."\n";
        return $status;
    }
/*******************************************结算生肖***************************************************/
    public function shx($code,$rule){
        $shx = [];
        $zhXiao = [];
        $tm = $code[6];
        unset($code[6]);
        $zhm = $code;
        foreach($zhm as $vo){
            $zhXiao[] = in_array($vo,$rule['shu'])?'shu':(
                in_array($vo,$rule['niu'])?'niu':(
                    in_array($vo,$rule['hu'])?'hu':(
                        in_array($vo,$rule['shXtu'])?'tu':(
                            in_array($vo,$rule['hou'])?'hou':(
                                in_array($vo,$rule['ji'])?'ji':(
                                    in_array($vo,$rule['gou'])?'gou':(
                                        in_array($vo,$rule['zhu'])?'zhu':(
                                            in_array($vo,$rule['long'])?'long':(
                                                in_array($vo,$rule['she'])?'she':(
                                                    in_array($vo,$rule['ma'])?'ma':(
                                                        in_array($vo,$rule['yang'])?'yang':'fault'
                                                    )
                                                )
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            );
        }
        $teXiao = in_array($tm,$rule['shu'])?'shu':(
            in_array($tm,$rule['niu'])?'niu':(
                in_array($tm,$rule['hu'])?'hu':(
                    in_array($tm,$rule['shXtu'])?'tu':(
                        in_array($tm,$rule['hou'])?'hou':(
                            in_array($tm,$rule['ji'])?'ji':(
                                in_array($tm,$rule['gou'])?'gou':(
                                    in_array($tm,$rule['zhu'])?'zhu':(
                                        in_array($tm,$rule['long'])?'long':(
                                            in_array($tm,$rule['she'])?'she':(
                                                in_array($tm,$rule['ma'])?'ma':(
                                                    in_array($tm,$rule['yang'])?'yang':'fault'
                                                )
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );
        $zhXiao = array_unique($zhXiao);
        $zongXiao = $zhXiao;
        $zongXiao[] = $teXiao;
        $zongXiao = array_unique($zongXiao);
        //变生肖为赔率生肖
        foreach($zhXiao as $key=>$vo){
            $zhXiao[$key] = 'zhq'.$vo;
        }
        foreach($zongXiao as $key=>$vo){
            $zongXiao[$key] = 'yq'.$vo;
        }
        $teXiao = 'tq'.$teXiao;

        $shx = array_merge($zhXiao,$zongXiao);
        $shx[] = $teXiao;
        
        $zhongLei = count($zongXiao);
        $shx[] = ($zhongLei==2)||($zhongLei==3)||($zhongLei==4)?"zq234q":(
            $zhongLei==5?"zq5q":(
                $zhongLei==6?"zq6q":(
                    $zhongLei==7?"zq7q":"zq1q"
                )
            )
        );
        
        $shx[] = $zhongLei%2?"zqzqd":"zqzqsh";
        return $shx;
    }
/*******************************************结算自选不中***************************************************/
    public function zxbzh($code,$orderNumber){
        $flag = 1;
        foreach($orderNumber as $vo){
            if(in_array($vo,$code)){
                $flag = 0;
                break;
            }
        }
        return $flag;
    }
/*******************************************结算连肖连尾***************************************************/
    public function lqlw($code,$orderNumber,$rule){
        $result = [];
        foreach($code as $key=>$vo){
            $result[] = in_array($vo,$rule['shu'])?'shu':(
                in_array($vo,$rule['niu'])?'niu':(
                    in_array($vo,$rule['hu'])?'hu':(
                        in_array($vo,$rule['shXtu'])?'tu':(
                            in_array($vo,$rule['hou'])?'hou':(
                                in_array($vo,$rule['ji'])?'ji':(
                                    in_array($vo,$rule['gou'])?'gou':(
                                        in_array($vo,$rule['zhu'])?'zhu':(
                                            in_array($vo,$rule['long'])?'long':(
                                                in_array($vo,$rule['she'])?'she':(
                                                    in_array($vo,$rule['ma'])?'ma':(
                                                        in_array($vo,$rule['yang'])?'yang':'fault'
                                                    )
                                                )
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            );
            $weiShu = 'lw'.$vo%10;
            $result[] = $weiShu;
        }

        $flag = 1;
        foreach($orderNumber as $vo){
            if(!in_array($vo,$result)){
                $flag = 0;
                break;
            }
        }
        $result = array_unique($result);
        return $flag;
    }


/*******************************************结算连码***************************************************/
    public function lma($code,$orderNumber,$orderType){
        $zhong = 0;
        $tm = $code[6];//特码
        unset($code[6]); //正码
        switch($orderType){

            case 'lmaszher':
            foreach($orderNumber as $vo){
                if(in_array($vo,$code)){
                    $zhong++;
                }
            }
            if($zhong == 2){//正码中两个
                $result = 'lmaszherzher';
            }
            else if($zhong == 3){//正码中三个
                $result = 'lmaszherzhs';
            }
            break;
            case 'lmaerzht':
            //中一个特码一个正码
            $cnt = array_unique(array_merge($orderNumber,$code));
                if(in_array($tm,$orderNumber)&&(count($cnt)==7)){
                    $result = 'lmaerzhtzht';
                }
                //中两个正码
                else if(count(array_merge($orderNumber,$code))==6){
                    $result = 'lmaerzhtzher';
                }
            break;

            case 'lmatch'://连码特串一个正码一个特码
                //中一个特码一个正码
                $cnt = array_unique(array_merge($orderNumber,$code));
                if(in_array($tm,$orderNumber)&&(count($cnt)==7)){
                    $result = 'lmatch';
                }
            break;
            //四全中 三全中 二全中
            default:
            $result = $orderType;//所选号码全部在正码里算中
                foreach($orderNumber as $vo){
                    if(!in_array($vo,$code)){
                        $result = 0;
                        break;
                    }
                }  
            break;
        }
        if(isset($result)){
            return $result;
        }
        else{
            return 0;
        }
    }
/*******************************************结算正码过关***************************************************/
    public function zhmgg($code,$rule,$orderNumber){
        //选择两个以上的正码形态组合下注，同位置上形态相同即为中奖，中奖赔率为对应位置上的形态的赔率
        unset($code[6]);
        $zhm = $code;
        $xingTai = [];//正码的形态
        $prefix = ['zhy','zher','zhs','zhsi','zhw','zhl'];
        foreach($zhm as $key => $vo){
            if($vo>=10){
                $heSum = floor($vo/10) + $vo%10;//合数
                $weiShu = $vo%10;
            }
            else{
                $heSum = $vo;
                $weiShu = $vo;
            }
            $xingTai[] = $vo%2?$prefix[$key].'d_dan' : $prefix[$key].'sh_shuang';//单双
            $xingTai[]  = ($vo>=25&&$vo<=48)?$prefix[$key].'da_da':$prefix[$key].'x_xiao';//大小
            $xingTai[] = $heSum>7?$prefix[$key].'hda_heda':$prefix[$key].'hx_hexiao';//合大小 
            $xingTai[] = $heSum%2?$prefix[$key].'hd_hedan':$prefix[$key].'hsh_heshuang'; //合单双
            $xingTai[] = $weiShu>=5?$prefix[$key].'wda_weida':$prefix[$key].'wx_weixiao';//尾大小
            $xingTai[] = in_array($vo,$rule['hb'])?$prefix[$key].'hb_hong':
            (in_array($vo,$rule['lb'])?$prefix[$key].'lb_lan':$prefix[$key].'lvb_lv');
        } 
        $flag = 1;
        foreach($orderNumber as $vo){//一个形态没有即没中奖
            if(!in_array($vo,$xingTai)){
                $flag = 0;
                break;
            }
        }
        return $flag;  
        // "zhmgg": {
        //     "zhy": {
        //         "zhyd": 1.97,
        //         "zhysh": 1.97,
        //         "zhyda": 1.97,
        //         "zhyx": 1.97,
        //         "zhyhd": 1.97,
        //         "zhyhsh": 1.97,
        //         "zhyhda": 1.97,
        //         "zhyhx": 1.97,
        //         "zhywda": 1.97,
        //         "zhywx": 1.97,
        //         "zhyhb": 2.7,
        //         "zhylvb": 2.85,
        //         "zhylb": 2.85
        //     },
        //     "zher": {
        //         "zherd": 1.97,
        //         "zhersh": 1.97,
        //         "zherda": 1.97,
        //         "zherx": 1.97,
        //         "zherhd": 1.97,
        //         "zherhsh": 1.97,
        //         "zherhda": 1.97,
        //         "zherhx": 1.97,
        //         "zherwda": 1.97,
        //         "zherwx": 1.97,
        //         "zherhb": 2.7,
        //         "zherlvb": 2.85,
        //         "zherlb": 2.85
        //     },
        //     "zhs": {
        //         "zhsd": 1.97,
        //         "zhssh": 1.97,
        //         "zhsda": 1.97,
        //         "zhsx": 1.97,
        //         "zhshd": 1.97,
        //         "zhshsh": 1.97,
        //         "zhshda": 1.97,
        //         "zhshx": 1.97,
        //         "zhswda": 1.97,
        //         "zhswx": 1.97,
        //         "zhshb": 2.7,
        //         "zhslvb": 2.85,
        //         "zhslb": 2.85
        //     },
        //     "zhsi": {
        //         "zhsid": 1.97,
        //         "zhsish": 1.97,
        //         "zhsida": 1.97,
        //         "zhsix": 1.97,
        //         "zhsihd": 1.97,
        //         "zhsihsh": 1.97,
        //         "zhsihda": 1.97,
        //         "zhsihx": 1.97,
        //         "zhsiwda": 1.97,
        //         "zhsiwx": 1.97,
        //         "zhsihb": 2.7,
        //         "zhsilvb": 2.85,
        //         "zhsilb": 2.85
        //     },
        //     "zhw": {
        //         "zhwd": 1.97,
        //         "zhwsh": 1.97,
        //         "zhwda": 1.97,
        //         "zhwx": 1.97,
        //         "zhwhd": 1.97,
        //         "zhwhsh": 1.97,
        //         "zhwhda": 1.97,
        //         "zhwhx": 1.97,
        //         "zhwwda": 1.97,
        //         "zhwwx": 1.97,
        //         "zhwhb": 2.7,
        //         "zhwlvb": 2.85,
        //         "zhwlb": 2.85
        //     },
        //     "zhl": {
        //         "zhld": 1.97,
        //         "zhlsh": 1.97,
        //         "zhlda": 1.97,
        //         "zhlx": 1.97,
        //         "zhlhd": 1.97,
        //         "zhlhsh": 1.97,
        //         "zhlhda": 1.97,
        //         "zhlhx": 1.97,
        //         "zhlwda": 1.97,
        //         "zhlwx": 1.97,
        //         "zhlhb": 2.7,
        //         "zhllvb": 2.85,
        //         "zhllb": 2.85
        //     }
        // },

    }
/*******************************************结算正码1-6***************************************************/
    public function zhm16($code,$rule){
            $tm = $code[6];
            unset($code[6]);
            $zhm = $code;
            $xingTai = [
                'zh1'=>[],
                'zh2'=>[],
                'zh3'=>[],
                'zh4'=>[],
                'zh5'=>[],
                'zh6'=>[],  
            ];//正码的形态
            $sub = ['zh1','zh2','zh3','zh4','zh5','zh6'];//形态下标
            foreach($zhm as $key => $vo){
                if($vo>=10){
                    $heSum = floor($vo/10) + $vo%10;//合数
                    $weiShu = $vo%10;
                }
                else{
                    $heSum = $vo;
                    $weiShu = $vo;
                }
                $xingTai[$sub[$key]][] = $vo%2?$sub[$key].'d_dan' : $sub[$key].'sh_shuang';//单双
                $xingTai[$sub[$key]][]  = ($vo>=25&&$vo<=48)?$sub[$key].'da_da':$sub[$key].'x_xiao';//大小
                $xingTai[$sub[$key]][] = $heSum>7?$sub[$key].'hda_heda':$sub[$key].'hx_hexiao';//合大小 
                $xingTai[$sub[$key]][] = $heSum%2?$sub[$key].'hd_hedan':$sub[$key].'hsh_heshuang'; //合单双
                $xingTai[$sub[$key]][] = $weiShu>=5?$sub[$key].'wda_weida':$sub[$key].'wx_weixiao';//尾大小
                $xingTai[$sub[$key]][] = in_array($tm,$rule['hb'])?$sub[$key].'hb_hong':
                (in_array($tm,$rule['lb'])?$sub[$key].'lb_lan':$sub[$key].'lvb_lv');//色波
            } 
            // var_dump($xingTai);
            return $xingTai;
        // "zhm1-6": {
        //     "zh1": {
        //         "zh1d": 1.98,
        //         "zh1sh": 1.98,
        //         "zh1da": 1.98,
        //         "zh1x": 1.98,
        //         "zh1hd": 1.98,
        //         "zh1hsh": 1.98,
        //         "zh1hda": 1.98,
        //         "zh1hx": 1.98,
        //         "zh1wda": 1.98,
        //         "zh1wx": 1.98,
        //         "zh1hb": 2.78,
        //         "zh1lvb": 2.86,
        //         "zh1lb": 2.86
        //     },
        //     "zh2": {
        //         "zh2d": 1.98,
        //         "zh2sh": 1.98,
        //         "zh2da": 1.98,
        //         "zh2x": 1.98,
        //         "zh2hd": 1.98,
        //         "zh2hsh": 1.98,
        //         "zh2hda": 1.98,
        //         "zh2hx": 1.98,
        //         "zh2wda": 1.98,
        //         "zh2wx": 1.98,
        //         "zh2hb": 2.78,
        //         "zh2lvb": 2.86,
        //         "zh2lb": 2.86
        //     },
        //     "zh3": {
        //         "zh3d": 1.98,
        //         "zh3sh": 1.98,
        //         "zh3da": 1.98,
        //         "zh3x": 1.98,
        //         "zh3hd": 1.98,
        //         "zh3hsh": 1.98,
        //         "zh3hda": 1.98,
        //         "zh3hx": 1.98,
        //         "zh3wda": 1.98,
        //         "zh3wx": 1.98,
        //         "zh3hb": 2.78,
        //         "zh3lvb": 2.86,
        //         "zh3lb": 2.86
        //     },
        //     "zh4": {
        //         "zh4d": 1.98,
        //         "zh4sh": 1.98,
        //         "zh4da": 1.98,
        //         "zh4x": 1.98,
        //         "zh4hd": 1.98,
        //         "zh4hsh": 1.98,
        //         "zh4hda": 1.98,
        //         "zh4hx": 1.98,
        //         "zh4wda": 1.98,
        //         "zh4wx": 1.98,
        //         "zh4hb": 2.78,
        //         "zh4lvb": 2.86,
        //         "zh4lb": 2.86
        //     },
        //     "zh5": {
        //         "zh5d": 1.98,
        //         "zh5sh": 1.98,
        //         "zh5da": 1.98,
        //         "zh5x": 1.98,
        //         "zh5hd": 1.98,
        //         "zh5hsh": 1.98,
        //         "zh5hda": 1.98,
        //         "zh5hx": 1.98,
        //         "zh5wda": 1.98,
        //         "zh5wx": 1.98,
        //         "zh5hb": 2.78,
        //         "zh5lvb": 2.86,
        //         "zh5lb": 2.86
        //     },
        //     "zh6": {
        //         "zh6d": 1.98,
        //         "zh6sh": 1.98,
        //         "zh6da": 1.98,
        //         "zh6x": 1.98,
        //         "zh6hd": 1.98,
        //         "zh6hsh": 1.98,
        //         "zh6hda": 1.98,
        //         "zh6hx": 1.98,
        //         "zh6wda": 1.98,
        //         "zh6wx": 1.98,
        //         "zh6hb": 2.78,
        //         "zh6lvb": 2.86,
        //         "zh6lb": 2.86
        //     }
        // },
  
    }
/*******************************************结算正码特***************************************************/
    public function zhmt($code){
        $new = [
            "zhyt"=>$code[0],
            "zhert"=>$code[1],
            "zhst"=>$code[2],
            "zhsit"=>$code[3],
            "zhwt"=>$code[4],
            "zhlt"=>$code[5],
        ];
        return $new;
        // "zhmt": {
        //     "zhyt": {
        //         "zhyt1": 47,
        //         "zhyt2": 47,
        //         "zhyt3": 47,
        //         "zhyt4": 47,
        //         "zhyt5": 47,
        //         "zhyt6": 47,
        //         "zhyt7": 47,
        //         "zhyt8": 47,
        //         "zhyt9": 47,
        //         "zhyt10": 47,
        //         "zhyt11": 47,
        //         "zhyt12": 47,
        //         "zhyt13": 47,
        //         "zhyt14": 47,
        //         "zhyt15": 47,
        //         "zhyt16": 47,
        //         "zhyt17": 47,
        //         "zhyt18": 47,
        //         "zhyt19": 47,
        //         "zhyt20": 47,
        //         "zhyt21": 47,
        //         "zhyt22": 47,
        //         "zhyt23": 47,
        //         "zhyt24": 47,
        //         "zhyt25": 47,
        //         "zhyt26": 47,
        //         "zhyt27": 47,
        //         "zhyt28": 47,
        //         "zhyt29": 47,
        //         "zhyt30": 47,
        //         "zhyt31": 47,
        //         "zhyt32": 47,
        //         "zhyt33": 47,
        //         "zhyt34": 47,
        //         "zhyt35": 47,
        //         "zhyt36": 47,
        //         "zhyt37": 47,
        //         "zhyt38": 47,
        //         "zhyt39": 47,
        //         "zhyt40": 47,
        //         "zhyt41": 47,
        //         "zhyt42": 47,
        //         "zhyt43": 47,
        //         "zhyt44": 47,
        //         "zhyt45": 47,
        //         "zhyt46": 47,
        //         "zhyt47": 47,
        //         "zhyt48": 47,
        //         "zhyt49": 47
        //     },
        //     "zhert": {
        //         "zhert1": 47,
        //         "zhert2": 47,
        //         "zhert3": 47,
        //         "zhert4": 47,
        //         "zhert5": 47,
        //         "zhert6": 47,
        //         "zhert7": 47,
        //         "zhert8": 47,
        //         "zhert9": 47,
        //         "zhert10": 47,
        //         "zhert11": 47,
        //         "zhert12": 47,
        //         "zhert13": 47,
        //         "zhert14": 47,
        //         "zhert15": 47,
        //         "zhert16": 47,
        //         "zhert17": 47,
        //         "zhert18": 47,
        //         "zhert19": 47,
        //         "zhert20": 47,
        //         "zhert21": 47,
        //         "zhert22": 47,
        //         "zhert23": 47,
        //         "zhert24": 47,
        //         "zhert25": 47,
        //         "zhert26": 47,
        //         "zhert27": 47,
        //         "zhert28": 47,
        //         "zhert29": 47,
        //         "zhert30": 47,
        //         "zhert31": 47,
        //         "zhert32": 47,
        //         "zhert33": 47,
        //         "zhert34": 47,
        //         "zhert35": 47,
        //         "zhert36": 47,
        //         "zhert37": 47,
        //         "zhert38": 47,
        //         "zhert39": 47,
        //         "zhert40": 47,
        //         "zhert41": 47,
        //         "zhert42": 47,
        //         "zhert43": 47,
        //         "zhert44": 47,
        //         "zhert45": 47,
        //         "zhert46": 47,
        //         "zhert47": 47,
        //         "zhert48": 47,
        //         "zhert49": 47
        //     },
        //     "zhst": {
        //         "zhst1": 47,
        //         "zhst2": 47,
        //         "zhst3": 47,
        //         "zhst4": 47,
        //         "zhst5": 47,
        //         "zhst6": 47,
        //         "zhst7": 47,
        //         "zhst8": 47,
        //         "zhst9": 47,
        //         "zhst10": 47,
        //         "zhst11": 47,
        //         "zhst12": 47,
        //         "zhst13": 47,
        //         "zhst14": 47,
        //         "zhst15": 47,
        //         "zhst16": 47,
        //         "zhst17": 47,
        //         "zhst18": 47,
        //         "zhst19": 47,
        //         "zhst20": 47,
        //         "zhst21": 47,
        //         "zhst22": 47,
        //         "zhst23": 47,
        //         "zhst24": 47,
        //         "zhst25": 47,
        //         "zhst26": 47,
        //         "zhst27": 47,
        //         "zhst28": 47,
        //         "zhst29": 47,
        //         "zhst30": 47,
        //         "zhst31": 47,
        //         "zhst32": 47,
        //         "zhst33": 47,
        //         "zhst34": 47,
        //         "zhst35": 47,
        //         "zhst36": 47,
        //         "zhst37": 47,
        //         "zhst38": 47,
        //         "zhst39": 47,
        //         "zhst40": 47,
        //         "zhst41": 47,
        //         "zhst42": 47,
        //         "zhst43": 47,
        //         "zhst44": 47,
        //         "zhst45": 47,
        //         "zhst46": 47,
        //         "zhst47": 47,
        //         "zhst48": 47,
        //         "zhst49": 47
        //     },
        //     "zhsit": {
        //         "zhsit1": 47,
        //         "zhsit2": 47,
        //         "zhsit3": 47,
        //         "zhsit4": 47,
        //         "zhsit5": 47,
        //         "zhsit6": 47,
        //         "zhsit7": 47,
        //         "zhsit8": 47,
        //         "zhsit9": 47,
        //         "zhsit10": 47,
        //         "zhsit11": 47,
        //         "zhsit12": 47,
        //         "zhsit13": 47,
        //         "zhsit14": 47,
        //         "zhsit15": 47,
        //         "zhsit16": 47,
        //         "zhsit17": 47,
        //         "zhsit18": 47,
        //         "zhsit19": 47,
        //         "zhsit20": 47,
        //         "zhsit21": 47,
        //         "zhsit22": 47,
        //         "zhsit23": 47,
        //         "zhsit24": 47,
        //         "zhsit25": 47,
        //         "zhsit26": 47,
        //         "zhsit27": 47,
        //         "zhsit28": 47,
        //         "zhsit29": 47,
        //         "zhsit30": 47,
        //         "zhsit31": 47,
        //         "zhsit32": 47,
        //         "zhsit33": 47,
        //         "zhsit34": 47,
        //         "zhsit35": 47,
        //         "zhsit36": 47,
        //         "zhsit37": 47,
        //         "zhsit38": 47,
        //         "zhsit39": 47,
        //         "zhsit40": 47,
        //         "zhsit41": 47,
        //         "zhsit42": 47,
        //         "zhsit43": 47,
        //         "zhsit44": 47,
        //         "zhsit45": 47,
        //         "zhsit46": 47,
        //         "zhsit47": 47,
        //         "zhsit48": 47,
        //         "zhsit49": 47
        //     },
        //     "zhwt": {
        //         "zhwt1": 47,
        //         "zhwt2": 47,
        //         "zhwt3": 47,
        //         "zhwt4": 47,
        //         "zhwt5": 47,
        //         "zhwt6": 47,
        //         "zhwt7": 47,
        //         "zhwt8": 47,
        //         "zhwt9": 47,
        //         "zhwt10": 47,
        //         "zhwt11": 47,
        //         "zhwt12": 47,
        //         "zhwt13": 47,
        //         "zhwt14": 47,
        //         "zhwt15": 47,
        //         "zhwt16": 47,
        //         "zhwt17": 47,
        //         "zhwt18": 47,
        //         "zhwt19": 47,
        //         "zhwt20": 47,
        //         "zhwt21": 47,
        //         "zhwt22": 47,
        //         "zhwt23": 47,
        //         "zhwt24": 47,
        //         "zhwt25": 47,
        //         "zhwt26": 47,
        //         "zhwt27": 47,
        //         "zhwt28": 47,
        //         "zhwt29": 47,
        //         "zhwt30": 47,
        //         "zhwt31": 47,
        //         "zhwt32": 47,
        //         "zhwt33": 47,
        //         "zhwt34": 47,
        //         "zhwt35": 47,
        //         "zhwt36": 47,
        //         "zhwt37": 47,
        //         "zhwt38": 47,
        //         "zhwt39": 47,
        //         "zhwt40": 47,
        //         "zhwt41": 47,
        //         "zhwt42": 47,
        //         "zhwt43": 47,
        //         "zhwt44": 47,
        //         "zhwt45": 47,
        //         "zhwt46": 47,
        //         "zhwt47": 47,
        //         "zhwt48": 47,
        //         "zhwt49": 47
        //     },
        //     "zhlt": {
        //         "zhlt1": 47,
        //         "zhlt2": 47,
        //         "zhlt3": 47,
        //         "zhlt4": 47,
        //         "zhlt5": 47,
        //         "zhlt6": 47,
        //         "zhlt7": 47,
        //         "zhlt8": 47,
        //         "zhlt9": 47,
        //         "zhlt10": 47,
        //         "zhlt11": 47,
        //         "zhlt12": 47,
        //         "zhlt13": 47,
        //         "zhlt14": 47,
        //         "zhlt15": 47,
        //         "zhlt16": 47,
        //         "zhlt17": 47,
        //         "zhlt18": 47,
        //         "zhlt19": 47,
        //         "zhlt20": 47,
        //         "zhlt21": 47,
        //         "zhlt22": 47,
        //         "zhlt23": 47,
        //         "zhlt24": 47,
        //         "zhlt25": 47,
        //         "zhlt26": 47,
        //         "zhlt27": 47,
        //         "zhlt28": 47,
        //         "zhlt29": 47,
        //         "zhlt30": 47,
        //         "zhlt31": 47,
        //         "zhlt32": 47,
        //         "zhlt33": 47,
        //         "zhlt34": 47,
        //         "zhlt35": 47,
        //         "zhlt36": 47,
        //         "zhlt37": 47,
        //         "zhlt38": 47,
        //         "zhlt39": 47,
        //         "zhlt40": 47,
        //         "zhlt41": 47,
        //         "zhlt42": 47,
        //         "zhlt43": 47,
        //         "zhlt44": 47,
        //         "zhlt45": 47,
        //         "zhlt46": 47,
        //         "zhlt47": 47,
        //         "zhlt48": 47,
        //         "zhlt49": 47
        //     }
        // },

    }

/*******************************************结算正码***************************************************/
    public function zhm($code){
        return $code;
        // "zhm": {
        //     "zhm1": 8.2,
        //     "zhm2": 8.2,
        //     "zhm3": 8.2,
        //     "zhm4": 8.2,
        //     "zhm5": 8.2,
        //     "zhm6": 8.2,
        //     "zhm7": 8.2,
        //     "zhm8": 8.2,
        //     "zhm9": 8.2,
        //     "zhm10": 8.2,
        //     "zhm11": 8.2,
        //     "zhm12": 8.2,
        //     "zhm13": 8.2,
        //     "zhm14": 8.2,
        //     "zhm15": 8.2,
        //     "zhm16": 8.2,
        //     "zhm17": 8.2,
        //     "zhm18": 8.2,
        //     "zhm19": 8.2,
        //     "zhm20": 8.2,
        //     "zhm21": 8.2,
        //     "zhm22": 8.2,
        //     "zhm23": 8.2,
        //     "zhm24": 8.2,
        //     "zhm25": 8.2,
        //     "zhm26": 8.2,
        //     "zhm27": 8.2,
        //     "zhm28": 8.2,
        //     "zhm29": 8.2,
        //     "zhm30": 8.2,
        //     "zhm31": 8.2,
        //     "zhm32": 8.2,
        //     "zhm33": 8.2,
        //     "zhm34": 8.2,
        //     "zhm35": 8.2,
        //     "zhm36": 8.2,
        //     "zhm37": 8.2,
        //     "zhm38": 8.2,
        //     "zhm39": 8.2,
        //     "zhm40": 8.2,
        //     "zhm41": 8.2,
        //     "zhm42": 8.2,
        //     "zhm43": 8.2,
        //     "zhm44": 8.2,
        //     "zhm45": 8.2,
        //     "zhm46": 8.2,
        //     "zhm47": 8.2,
        //     "zhm48": 8.2,
        //     "zhm49": 8.2
        // },

    }  
/*******************************************结算特码***************************************************/
    public function tm($code){
        return $code[6];
        // "tm": {
        //     "tm1": 48.8,
        //     "tm2": 48.8,
        //     "tm3": 48.8,
        //     "tm4": 48.8,
        //     "tm5": 48.8,
        //     "tm6": 48.8,
        //     "tm7": 48.8,
        //     "tm8": 48.8,
        //     "tm9": 48.8,
        //     "tm10": 48.8,
        //     "tm11": 48.8,
        //     "tm12": 48.8,
        //     "tm13": 48.8,
        //     "tm14": 48.8,
        //     "tm15": 48.8,
        //     "tm16": 48.8,
        //     "tm17": 48.8,
        //     "tm18": 48.8,
        //     "tm19": 48.8,
        //     "tm20": 48.8,
        //     "tm21": 48.8,
        //     "tm22": 48.8,
        //     "tm23": 48.8,
        //     "tm24": 48.8,
        //     "tm25": 48.8,
        //     "tm26": 48.8,
        //     "tm27": 48.8,
        //     "tm28": 48.8,
        //     "tm29": 48.8,
        //     "tm30": 48.8,
        //     "tm31": 48.8,
        //     "tm32": 48.8,
        //     "tm33": 48.8,
        //     "tm34": 48.8,
        //     "tm35": 48.8,
        //     "tm36": 48.8,
        //     "tm37": 48.8,
        //     "tm38": 48.8,
        //     "tm39": 48.8,
        //     "tm40": 48.8,
        //     "tm41": 48.8,
        //     "tm42": 48.8,
        //     "tm43": 48.8,
        //     "tm44": 48.8,
        //     "tm45": 48.8,
        //     "tm46": 48.8,
        //     "tm47": 48.8,
        //     "tm48": 48.8,
        //     "tm49": 48.8
        // },
  
    }
/*******************************************结算中一***************************************************/
    public function zhy($code,$orderNumber){
        $flag = 0;
        foreach($orderNumber as $vo){
            if(in_array($vo,$code)){
                $flag++;
                if($flag==2){
                    break;
                }
            }   
        }
        if($flag == 1){
            return 1;
        }
        else{
            return 0;
        }
        // "zhy": {
        //     "zhy5zh1": 2.35,
        //     "zhy6zh1": 2.28,
        //     "zhy7zh1": 2.27,
        //     "zhy8zh1": 2.32,
        //     "zhy9zh1": 2.41,
        //     "zhy10zh1": 2.51
        // }

    }
/*************************** 更新用户剩余金币，以数据库为准************************************/
        public function refreshRemaining($order){
            $gold = Db::name('user_user')->where(['id'=>$order['id']])->value('gold');
            $remaining = $gold - $order['allGold'];
            Db::name('user_user')->where(['id'=>$order['id']])->update(['gold'=>$remaining]);
            return $remaining;
        }
    
/*************************** 记录用户最新金币 table:user_user************************************/
        public function refleshUser($id,$remaining){
            Db::name('user_user')->where('id',$id)->update(['gold'=>$remaining]);
        }
    
/*************************** 结算订单 table:games_bet_historyr************************************/
        public function recordeBetHistory($orderNumber,$dice,$win){
            $id = Db::name('games_bet_history')->where(['order_number'=>$orderNumber])->value('id');
            $status = 1;
            if($win > 0){
                $status = 2;
            }
            $data = [
                'open_code'=>$dice['opencode'],
                'win'=>$win,
                'open_time'=>$dice['opentime'],
                'status'=>$status
            ];
            Db::name('games_bet_history')->where(['id'=>$id])->update($data);
        }
/**记录基本走势 table:games_six_trend recordeBasicTrend($data);******************************************/
        public function recordeBasicTrend($data,$rule){
            $openResult = explode(',',$data['opencode']);
            $sum = array_sum($openResult);

            $zhYanse = [];
            $tmYanse = '';
            foreach($openResult as $key=>$number){
                //特码
                if($key == 6){
                    $yanse = in_array($number,$rule['hb'])?'hong':
                       ( in_array($number,$rule['lb'])?'lan':'lv');
                    $tmYanse = $yanse;
                }else{
                    $yanse = in_array($number,$rule['hb'])?'hong':
                   ( in_array($number,$rule['lb'])?'lan':'lv');
                    $zhYanse[] = $yanse;
                }
            }
            $zhYanse = array_count_values($zhYanse);
    
            $number = [];
            $name = [];
            $he = 0;
            // 22 42 13 25 10 40 05
            //判断是否是和局
            if(count($zhYanse)==2){
                foreach($zhYanse as $key => $vo){
                    $number[] = $vo; 
                    $name[] = $key;
                }
                if($number[0]==$number[1]){
                    if(($name[0]!=$tmYanse)&&($name[1]!=$tmYanse)){
                        $he = 1;
                        $qsb = 'qsbhj';
                    }
                }
            }
            //不是和局
            if(!$he){
                if(count($zhYanse)==1){
                    //六个正码色波一致
                    foreach($zhYanse as $key=>$vo){
                        $suffix  = $key=="hong"?"_hong":($key=="lan"?"_lan":($key=="lv"?"_lv":"fault"));
                        $qsb = 'qsb'.$suffix;
                    }
                }
                else{
                    foreach($zhYanse as $key=>$vo){
                        if($key == $tmYanse){
                            $zhYanse[$key]++;
                        }
                        //把键值对数组装换成数字数组
                        $number[] = $zhYanse[$key];
                    }
                    $max = max($number);
                    foreach($zhYanse as $key=>$vo){
                        if($vo == $max){
                            $suffix  = $key=="hong"?"_hong":($key=="lan"?"_lan":($key=="lv"?"_lv":"fault"));
                            $qsb = 'qsb'.$suffix;
                        }
                    }
                }
            }
    
            $tm = $openResult[6];//特码大小单双,特码合数大小单双 特码位数大小
            $he;
            $wsh;
            if($tm >=10){
                $he = $tm/10 +$tm%10;//合数
                $wsh = $tm % 10;//尾数
            }
            else{
                $he = $tm;
                $wsh = $tm;
            } 
            $tmdxdsh = $tm>=25&&$tm%2==1?'tmDaDan':(//特码大单--已检测
                $tm>=25&&$tm%2==0?'tmDaShuang':(
                    $tm<25&&$tm%2==1?'tmXiaoDan':(
                        $tm<=25&&$tm%2==0?'tmXiaoShuang':(
                            "fault"
                        )
                    )
                )
            );
            $hshdxdsh = $he>=7&&$he%2?'heDaDan':(//特码合数大小单双--已检测
                $he>=7&&$he%2==0?'heDaShuang':(
                    $he<7&&$he%2?'heXiaoDan':(
                        $he<=7&&$he%2==0?'heXiaoShuang':(
                            "fault"
                        )
                    )
                )
            );

            $tmwdx = $he>=5?'weiDa':'weiXiao';
            $tmshx = in_array($tm,$rule['shu'])?'shu':(
                in_array($tm,$rule['niu'])?'niu':(
                    in_array($tm,$rule['hu'])?'hu':(
                        in_array($tm,$rule['shXtu'])?'tu':(
                            in_array($tm,$rule['hou'])?'hou':(
                                in_array($tm,$rule['ji'])?'ji':(
                                    in_array($tm,$rule['gou'])?'gou':(
                                        in_array($tm,$rule['zhu'])?'zhu':(
                                            in_array($tm,$rule['long'])?'long':(
                                                in_array($tm,$rule['she'])?'she':(
                                                    in_array($tm,$rule['ma'])?'ma':(
                                                        in_array($tm,$rule['yang'])?'yang':'fault'
                                                    )
                                                )
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            );
            $tmsb =  in_array($tm,$rule['hb'])?'hong':
            (in_array($tm,$rule['lb'])?'lan':'lv');

            $tmwx = in_array($tm,$rule['jin'])?'wxjin':(
                in_array($tm,$rule['mu'])?'wxmu':(
                    in_array($tm,$rule['shui'])?'wxshui':(
                        in_array($tm,$rule['huo'])?'wxhuo':(
                            in_array($tm,$rule['wxtu'])?'wxtu':'fault'
                        )
                    ) 
                )
            );

            $record = [
                "issue" =>$data['expect'],
                "sum" => $sum,//和值
                "dsh" => $sum%2?'dan':'shuang',//和值单双
                "dax" => $sum>=175?'da':'xiao',//和值大小
                "qsb" => $qsb,//七色波
                "tm" =>  $tm,//特码数值
                "tmdxdsh"=> $tmdxdsh,//特码大小单双
                "hshdxdsh"=>$hshdxdsh,//合数大小单双
                'tmshx'=>$tmshx,//特码生肖
                "tmwdx" =>$tmwdx,//特码尾大小
                'tmsb'=>$tmsb,//特码色波
                'tmwx'=>$tmwx,//特码五行
            ];
           Db::name('games_six_trend')->insert($record);
        }
/**记录色波 table:games_six_color;   recordeSumTrend($data);*********************************************/
        public function recordeColor($data,$rule){
            $code = explode(',',$data['opencode']);
            $result = [
                "zh1" =>[],
                "zh2" =>[],
                "zh3" =>[],
                "zh4" =>[],
                "zh5" =>[],
                "zh6" =>[],
                "tm" =>[],
            ];
            foreach($code as $key => $vo){
                if($key == 6){
                    $result['tm']['shX'] = in_array($vo,$rule['shu'])?'shu':(
                        in_array($vo,$rule['niu'])?'niu':(
                            in_array($vo,$rule['hu'])?'hu':(
                                in_array($vo,$rule['shXtu'])?'tu':(
                                    in_array($vo,$rule['hou'])?'hou':(
                                        in_array($vo,$rule['ji'])?'ji':(
                                            in_array($vo,$rule['gou'])?'gou':(
                                                in_array($vo,$rule['zhu'])?'zhu':(
                                                    in_array($vo,$rule['long'])?'long':(
                                                        in_array($vo,$rule['she'])?'she':(
                                                            in_array($vo,$rule['ma'])?'ma':(
                                                                in_array($vo,$rule['yang'])?'yang':'fault'
                                                            )
                                                        )
                                                    )
                                                )
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    );
                    $result['tm']['bs'] =  in_array($vo,$rule['hb'])?'hong':
                    (in_array($vo,$rule['lb'])?'lan':'lv');//波色
                    $result['tm']['shuZ'] = $vo;//数字
                }
                else{
                    $key = $key+1;
                    $name = 'zh'.$key;
                    $result[$name]['shX'] = in_array($vo,$rule['shu'])?'shu':(
                        in_array($vo,$rule['niu'])?'niu':(
                            in_array($vo,$rule['hu'])?'hu':(
                                in_array($vo,$rule['shXtu'])?'tu':(
                                    in_array($vo,$rule['hou'])?'hou':(
                                        in_array($vo,$rule['ji'])?'ji':(
                                            in_array($vo,$rule['gou'])?'gou':(
                                                in_array($vo,$rule['zhu'])?'zhu':(
                                                    in_array($vo,$rule['long'])?'long':(
                                                        in_array($vo,$rule['she'])?'she':(
                                                            in_array($vo,$rule['ma'])?'ma':(
                                                                in_array($vo,$rule['yang'])?'yang':'fault'
                                                            )
                                                        )
                                                    )
                                                )
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    );
                    $result[$name]['bs'] =  in_array($vo,$rule['hb'])?'hong':
                    (in_array($vo,$rule['lb'])?'lan':'lv');
                    $result[$name]['shuZ'] = $vo;
                }
                
            }
            $ssb = ['hong'=>0,'lan'=>0,'lv'=>0];
            foreach($result as $key => $vo){
               switch($vo['bs']){
                    case 'hong':
                        $ssb['hong']++;
                    break;
                    case 'lan':
                        $ssb['lan']++;
                    break;
                    case 'lv':
                        $ssb['lv']++;
                    break;
                    default:
                    break;
               }
               $result[$key] = json_encode($result[$key]);//保存数据库需要转字符串
            }
            $ssb = json_encode($ssb);
            $record = [
                "issue"=>$data['expect'],
                "zh1" =>$result['zh1'],
                "zh2" =>$result['zh2'],
                "zh3" =>$result['zh3'],
                "zh4" =>$result['zh4'],
                "zh5" =>$result['zh5'],
                "zh6" =>$result['zh6'],
                "tm" =>$result['tm'],
                "sbb" =>$ssb//波色比
            ];
            Db::name("games_six_color")->insert($record);
        }
/**记录生肖 table:games_six_animal;  recordsAnimal($data);***********************************************/
        public function recordsAnimal($data,$rule){
            $code = explode(',',$data['opencode']);
            $result = [
                "zh1" =>[],
                "zh2" =>[],
                "zh3" =>[],
                "zh4" =>[],
                "zh5" =>[],
                "zh6" =>[],
                "tm" =>[],
            ];
            foreach($code as$key => $vo){
                if($key ==6){
                    $result['tm']['shX'] = in_array($vo,$rule['shu'])?'shu':(
                        in_array($vo,$rule['niu'])?'niu':(
                            in_array($vo,$rule['hu'])?'hu':(
                                in_array($vo,$rule['shXtu'])?'tu':(
                                    in_array($vo,$rule['hou'])?'hou':(
                                        in_array($vo,$rule['ji'])?'ji':(
                                            in_array($vo,$rule['gou'])?'gou':(
                                                in_array($vo,$rule['zhu'])?'zhu':(
                                                    in_array($vo,$rule['long'])?'long':(
                                                        in_array($vo,$rule['she'])?'she':(
                                                            in_array($vo,$rule['ma'])?'ma':(
                                                                in_array($vo,$rule['yang'])?'yang':'fault'
                                                            )
                                                        )
                                                    )
                                                )
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    );
                    $result['tm']['bs'] =  in_array($vo,$rule['hb'])?'hong':
                    (in_array($vo,$rule['lb'])?'lan':'lv');
                    $result['tm']['shuZ'] = $vo;
                }
                else{
                    $key = $key+1;
                    $name = 'zh'.$key;
                    $result[$name]['shX'] = in_array($vo,$rule['shu'])?'shu':(
                        in_array($vo,$rule['niu'])?'niu':(
                            in_array($vo,$rule['hu'])?'hu':(
                                in_array($vo,$rule['shXtu'])?'tu':(
                                    in_array($vo,$rule['hou'])?'hou':(
                                        in_array($vo,$rule['ji'])?'ji':(
                                            in_array($vo,$rule['gou'])?'gou':(
                                                in_array($vo,$rule['zhu'])?'zhu':(
                                                    in_array($vo,$rule['long'])?'long':(
                                                        in_array($vo,$rule['she'])?'she':(
                                                            in_array($vo,$rule['ma'])?'ma':(
                                                                in_array($vo,$rule['yang'])?'yang':'fault'
                                                            )
                                                        )
                                                    )
                                                )
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    );
                    $result[$name]['bs'] =  in_array($vo,$rule['hb'])?'hong':
                    (in_array($vo,$rule['lb'])?'lan':'lv');
                    $result[$name]['shuZ'] = $vo;
                }
            }
            $shengXiao = [
                "shu" =>0,
                "niu" =>0,
                "hu" =>0,
                "tu" =>0,
                "long" =>0,
                "she" =>0,
                "ma" =>0,
                "yang" =>0,
                "hou" =>0,
                "ji" =>0,
                "gou" =>0,
                "zhu" =>0,
            ];
            foreach($result as $key => $vo){
               switch($vo['shX']){
                    case 'shu':
                        $shengXiao['shu']++;
                    break;
                    case 'niu':
                        $shengXiao['niu']++;
                    break;
                    case 'hu':
                        $shengXiao['hu']++;
                    break;
                    case 'tu':
                    $shengXiao['tu']++;
                    break;
                    case 'hou':
                        $shengXiao['hou']++;
                    break;
                    case 'ji':
                        $shengXiao['ji']++;
                    break;

                    case 'gou':
                    $shengXiao['gou']++;
                    break;
                    case 'zhu':
                        $shengXiao['zhu']++;
                    break;
                    case 'long':
                        $shengXiao['long']++;
                    break;

                    case 'she':
                    $shengXiao['she']++;
                    break;
                    case 'ma':
                        $shengXiao['ma']++;
                    break;
                    case 'yang':
                        $shengXiao['yang']++;
                    break;

                    default:
                    break;
               }
               $result[$key] = json_encode($result[$key]);//存库转字符串
            }

            
            
            $record = [
                "issue" =>$data['expect'],
                "zh1" => $result['zh1'],
                "zh2" =>$result['zh2'],
                "zh3" =>$result['zh3'],
                "zh4" =>$result['zh4'],
                "zh5" =>$result['zh5'],
                "zh6" =>$result['zh6'],
                "tm" =>$result['tm'],
                "shu" =>$shengXiao['shu'],
                "niu" =>$shengXiao['niu'],
                "hu" =>$shengXiao['hu'],
                "tu" =>$shengXiao['tu'],
                "long" =>$shengXiao['long'],
                "she" =>$shengXiao['she'],
                "ma" =>$shengXiao['ma'],
                "yang" =>$shengXiao['yang'],
                "hou" =>$shengXiao['hou'],
                "ji" =>$shengXiao['ji'],
                "gou" =>$shengXiao['gou'],
                "zhu" =>$shengXiao['zhu'],
            ];
            Db::name('games_six_animal')->insert($record);
        }
/**记录开奖结果 table:games_jsks_dice_history recordeDiceHistory($data,$rule);*********************************/
        public function recordeDiceHistory($data,$rule){
            $code = explode(',',$data['opencode']);
            $result = [
                "zh1" =>[],
                "zh2" =>[],
                "zh3" =>[],
                "zh4" =>[],
                "zh5" =>[],
                "zh6" =>[],
                "tm" =>[],
            ];
            foreach($code as $key => $vo){
                if($key < 6){
                    $key = $key+1;    
                    $name = 'zh'.$key;
                    $result[$name]['shX'] = in_array($vo,$rule['shu'])?'shu':(
                        in_array($vo,$rule['niu'])?'niu':(
                            in_array($vo,$rule['hu'])?'hu':(
                                in_array($vo,$rule['shXtu'])?'tu':(
                                    in_array($vo,$rule['hou'])?'hou':(
                                        in_array($vo,$rule['ji'])?'ji':(
                                            in_array($vo,$rule['gou'])?'gou':(
                                                in_array($vo,$rule['zhu'])?'zhu':(
                                                    in_array($vo,$rule['long'])?'long':(
                                                        in_array($vo,$rule['she'])?'she':(
                                                            in_array($vo,$rule['ma'])?'ma':(
                                                                in_array($vo,$rule['yang'])?'yang':'fault'
                                                            )
                                                        )
                                                    )
                                                )
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    );
                    $result[$name]['bs'] =  in_array($vo,$rule['hb'])?'hong':
                    (in_array($vo,$rule['lb'])?'lan':'lv');
                    $result[$name]['shuZ'] = $vo;
    
                }
                else{
                    $result['tm']['shX'] = in_array($vo,$rule['shu'])?'shu':(
                        in_array($vo,$rule['niu'])?'niu':(
                            in_array($vo,$rule['hu'])?'hu':(
                                in_array($vo,$rule['shXtu'])?'tu':(
                                    in_array($vo,$rule['hou'])?'hou':(
                                        in_array($vo,$rule['ji'])?'ji':(
                                            in_array($vo,$rule['gou'])?'gou':(
                                                in_array($vo,$rule['zhu'])?'zhu':(
                                                    in_array($vo,$rule['long'])?'long':(
                                                        in_array($vo,$rule['she'])?'she':(
                                                            in_array($vo,$rule['ma'])?'ma':(
                                                                in_array($vo,$rule['yang'])?'yang':'fault'
                                                            )
                                                        )
                                                    )
                                                )
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    );
                    $result['tm']['bs'] =  in_array($vo,$rule['hb'])?'hong':
                    (in_array($vo,$rule['lb'])?'lan':'lv');
                    $result['tm']['shuZ'] = $vo;
                    $wx = in_array($vo,$rule['jin'])?'wxjin':(
                        in_array($vo,$rule['mu'])?'wxmu':(
                            in_array($vo,$rule['shui'])?'wxshui':(
                                in_array($vo,$rule['huo'])?'wxhuo':(
                                    in_array($vo,$rule['wxtu'])?'wxtu':'fault'
                                )
                            ) 
                        )
                    );                        
                    if($vo >=10){
                        $tTou = floor($vo/10);
                        $tWei = $vo%10;
                    }
                    else{
                        $tTou = 0;
                        $tWei = $vo;
                    }   
                }
            };

            $record = [
                "issue" =>$data['expect'], //int (8) NOT NULL COMMENT '期号',
                "open_time" =>$data['opentime'], //VARCHAR (24) NOT NULL COMMENT '日期字符串',
                "code" =>json_encode($result), //VARCHAR (124) NOT NULL COMMENT '开奖号码',
                "wx" =>$wx, //VARCHAR (8) NOT NULL COMMENT '五行',
                "tt" =>$tTou, //VARCHAR (8) NOT NULL COMMENT '特头',
                "tw" =>$tWei, //VARCHAR (8) NOT NULL COMMENT '特尾',
            ];
            Db::name('games_six_dice_history')->insert($record);
        }        
}
        
$active = new Active();
$rule = [
    //波色；
    'hb'=>[1,2,7,8,12,13,18,19,23,24,29,30,34,35,40,45,46],
    'lb'=>[3,4,9,10,14,15,20,25,26,31,36,37,41,42,47,48],
    'lvb'=>[5,6,11,16,17,21,22,27,28,32,33,38,39,43,44,49],

    //生肖合集
    'yeshou'=>[11,23,35,47,9,21,33,45,7,19,31,43,6,18,30,42,3,15,27,39],
    'jiaqin'=>[10,22,34,46,5,17,29,41,4,16,28,40,2,14,26,38,1,13,25,37,49,12,24,36,48],
    'dan'=>[11,23,35,47,9,21,33,45,7,19,31,43,5,17,29,41,3,15,27,39,1,13,25,37,49],
    'shuang'=>[10,22,34,46,8,20,32,44,6,18,30,42,4,16,28,40,2,14,26,38,12,24,36,48],
    'qianqiao'=>[11,23,35,47,10,22,34,46,9,21,33,45,8,20,32,44,7,19,31,43,6,18,30,42],
    'houqiao'=>[5,17,29,41,4,16,28,40,3,15,27,39,2,14,26,38,1,13,25,37,49,12,24,36,48],
    'tianqiao'=>[10,22,34,46,8,20,32,44,7,19,31,43,5,17,29,41,3,15,27,39,12,24,36,48],
    'diqiao'=>[11,23,35,47,9,21,33,45,6,18,30,42,4,16,28,40,2,14,26,38,1,13,25,37,49],
    
    //生肖----------
    /* 生肖生成规则
    *   鼠、牛、虎、兔、蛇、马、羊、猴、鸡、狗、猪、龙
    *   以生肖年作为 1 开始依次+12
    */
    'shu'=>[11,23,35,47],
    'niu'=>[10,22,34,46],
    'hu'=>[9,21,33,45],
    'shxtu'=>[8,20,32,44],
    'long'=>[7,19,31,43],
    'she'=>[6,18,30,42],
    'ma'=>[5,17,29,41],
    'yang'=>[4,16,28,40],
    'hou'=>[3,15,27,39],
    'ji'=>[2,14,26,38],
    'gou'=>[1,13,25,37,49],
    'zhu'=>[12,24,36,48],
    
    //五行
    'jin'=> [4,5,18,19,26,27,34,35,48,49],
    'mu'=>  [1,8,9,16,17,30,31,38,39,46,47],
    'shui'=>[6,7,14,15,22,23,36,37,44,45],
    'huo'=> [2,3,10,11,24,25,32,33,40,41],
    'wxtu'=>  [12,13,20,21,28,29,42,43],

];

// $code = [01,3,35,47,9,21,12];
// $orderNumber = ["zhlhd","zhlx"];
// echo 1111;
// $active->zhm16($code,$rule);
 ?>