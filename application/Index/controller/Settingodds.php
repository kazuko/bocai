<?php
namespace app\Index\controller;

use app\GD\controller\Gd11x5;
use think\Controller;
use think\facade\Request;

// class Settingodds extends Controller{
class Settingodds
{
    public function __construct()
    {
    }
    // class Settingodds{
    /****************************************************修改六合彩赔率********************** */

    //六合彩的数据类型
    public function SixDataType()
    {
        $oddsNew = [
            '0' => [ //合肖
                "type" => "hq",
                "leiXin" => "",
                "key" => "shu",
                "value" => 100,
            ],
            "1" => [ //连肖连尾
                "type" => "lqlw",
                "leiXin" => "2lq",
                "key" => "shu",
                "value" => 100,
            ],
            "2" => [ //正码过关
                "type" => "zhmgg",
                "leiXin" => "",
                "key" => "zhyd_dan",
                "value" => 100,
            ],
            "3" => [ //正码1-6
                "type" => "zhm1-6",
                "leiXin" => "",
                "key" => "zh1d_dan",
                "value" => 100,
            ],
            "4" => [ //普通一层
                "type" => "lm",
                "leiXin" => "",
                "key" => "lmtedan",
                "value" => 100,
            ],
            "5" => [ //普通两层
                "type" => "qmwx",
                "leiXin" => "qm",
                "key" => "qmd0sh7",
                "value" => "10000",
            ],
        ];
    }

    public function settingSix()
    {
        $param;
        $param = Request::param();
        //修改赔率
        if ($param) {
            try {
                $oddsNew = $param['odds'];

                //把赔率修改数组整合成发送形式
                $oddsNew = self::allToOne($oddsNew);
                //修改发送文件
                $oddsNew = self::changeSixSendFile($oddsNew);
                //修改后台赔率文件
                self::changeSixOddsFile($oddsNew);

                $result = [
                    "code" => 1, //成功
                ];
                return json_encode($result);
            } catch (\Exception $e) {
                throw $e;
                $result = [
                    "code" => 0, //失败
                ];
                return json_encode($result);
            }

        }
        //返回赔率
        $deskInfo = file_get_contents(dirname(dirname(dirname(__FILE__))) . "/Six/send.json");
        $deskInfo = json_decode($deskInfo, true);
        $data = [
            "odds" => $deskInfo['odds'],
            "haoma" => $deskInfo['haoma'],
        ];
        echo json_encode($data);
        return;
    }

    //整合成后台发送文件形式
    public function allToOne($oddsNew)
    {
        $deskInfo = file_get_contents(dirname(dirname(dirname(__FILE__))) . "/Six/send.json");
        $deskInfo = json_decode($deskInfo, true);
        $odds = $deskInfo["odds"];
        foreach ($oddsNew as $key => $vo) {
            $type = $vo['type']; //大玩法
            $leiXin = $vo['leiXin']; //小玩法，没有传空
            $key = $vo['key']; //修改的键
            $value = floatval($vo["value"]); //修改的值
            switch ($type) {

                case "hq": //合肖
                    $odds["hq"]["show"][$key] = $value;
                    break;

                case "lqlw": //连肖连尾
                    $odds["lqlw"]["show"][$leiXin][$key] = $value;
                    break;

                case "zhmgg": //正码过关
                    preg_match("/zhy|zher|zhsi|zhs|zhw|zhl/", $key, $dir);
                    $odds["zhmgg"][$dir[0]][$key] = $value;
                    break;
                case "zhm1-6": //正码1-6
                    preg_match("/zh1|zh2|zh3|zh4|zh5|zh6/", $key, $dir);
                    $odds["zhm1-6"][$dir[0]][$key] = $value;
                    break;

                default:
                    if ($leiXin == "") { //一层
                        $odds[$type][$key] = $value;
                    } else { //两层
                        $odds[$type][$leiXin][$key] = $value;
                    }
                    break;
            }
        }
        return $odds;
    }

    /**修改/Six/send.json */
    public function changeSixSendFile($oddsNew)
    {
        //合肖分类
        $oddsNew['hq']['odds']['fenlei']['ysh']['yshplv'] = round($oddsNew["hq"]['show']['shu'] / 6, 3);
        $oddsNew['hq']['odds']['fenlei']['jq']['jqplv'] = round($oddsNew["hq"]['show']['gou'] / 6, 3);
        $oddsNew['hq']['odds']['fenlei']['dan']['danplv'] = round($oddsNew["hq"]['show']['gou'] / 6, 3);
        $oddsNew['hq']['odds']['fenlei']['shuang']['shuangplv'] = round($oddsNew["hq"]['show']['shu'] / 6, 3);
        $oddsNew['hq']['odds']['fenlei']['qq']['qqplv'] = round($oddsNew["hq"]['show']['shu'] / 6, 3);
        $oddsNew['hq']['odds']['fenlei']['houq']['hqplv'] = round($oddsNew["hq"]['show']['gou'] / 6, 3);
        $oddsNew['hq']['odds']['fenlei']['tq']['tqplv'] = round($oddsNew["hq"]['show']['shu'] / 6, 3);
        $oddsNew['hq']['odds']['fenlei']['dq']['dqplv'] = round($oddsNew["hq"]['show']['gou'] / 6, 3);

        //合肖分散没狗
        $oddsNew['hq']['odds']['fensang']['else']['else2'] = round($oddsNew["hq"]['show']['shu'] / 2, 3);
        $oddsNew['hq']['odds']['fensang']['else']['else3'] = round($oddsNew["hq"]['show']['shu'] / 3, 3);
        $oddsNew['hq']['odds']['fensang']['else']['else4'] = round($oddsNew["hq"]['show']['shu'] / 4, 3);
        $oddsNew['hq']['odds']['fensang']['else']['else5'] = round($oddsNew["hq"]['show']['shu'] / 5, 3);
        $oddsNew['hq']['odds']['fensang']['else']['else6'] = round($oddsNew["hq"]['show']['shu'] / 6, 3);
        $oddsNew['hq']['odds']['fensang']['else']['else7'] = round($oddsNew["hq"]['show']['shu'] / 7, 3);
        $oddsNew['hq']['odds']['fensang']['else']['else8'] = round($oddsNew["hq"]['show']['shu'] / 8, 3);
        $oddsNew['hq']['odds']['fensang']['else']['else9'] = round($oddsNew["hq"]['show']['shu'] / 9, 3);
        $oddsNew['hq']['odds']['fensang']['else']['else10'] = round($oddsNew["hq"]['show']['shu'] / 10, 3);
        $oddsNew['hq']['odds']['fensang']['else']['else11'] = round($oddsNew["hq"]['show']['shu'] / 11, 3);

        //合肖分散有狗
        $oddsNew['hq']['odds']['fensang']['gou']['gou2'] = round($oddsNew["hq"]['show']['gou'] / 6, 3);
        $oddsNew['hq']['odds']['fensang']['gou']['gou3'] = round($oddsNew["hq"]['show']['gou'] / 6, 3);
        $oddsNew['hq']['odds']['fensang']['gou']['gou4'] = round($oddsNew["hq"]['show']['gou'] / 6, 3);
        $oddsNew['hq']['odds']['fensang']['gou']['gou5'] = round($oddsNew["hq"]['show']['gou'] / 6, 3);
        $oddsNew['hq']['odds']['fensang']['gou']['gou6'] = round($oddsNew["hq"]['show']['gou'] / 6, 3);
        $oddsNew['hq']['odds']['fensang']['gou']['gou7'] = round($oddsNew["hq"]['show']['gou'] / 6, 3);
        $oddsNew['hq']['odds']['fensang']['gou']['gou8'] = round($oddsNew["hq"]['show']['gou'] / 6, 3);
        $oddsNew['hq']['odds']['fensang']['gou']['gou9'] = round($oddsNew["hq"]['show']['gou'] / 6, 3);
        $oddsNew['hq']['odds']['fensang']['gou']['go10'] = round($oddsNew["hq"]['show']['gou'] / 6, 3);
        $oddsNew['hq']['odds']['fensang']['gou']['gou11'] = round($oddsNew["hq"]['show']['gou'] / 6, 3);

        //连肖连尾
        $oddsNew["lqlw"]["odds"]["2lq"]["nogou"] = $oddsNew["lqlw"]["show"]["2lq"]["shu"];
        $oddsNew["lqlw"]["odds"]["2lq"]["gou"] = $oddsNew["lqlw"]["show"]["2lq"]["gou"];
        $oddsNew["lqlw"]["odds"]["3lq"]["nogou"] = $oddsNew["lqlw"]["show"]["3lq"]["shu"];
        $oddsNew["lqlw"]["odds"]["3lq"]["gou"] = $oddsNew["lqlw"]["show"]["3lq"]["gou"];
        $oddsNew["lqlw"]["odds"]["4lq"]["nogou"] = $oddsNew["lqlw"]["show"]["4lq"]["shu"];
        $oddsNew["lqlw"]["odds"]["4lq"]["gou"] = $oddsNew["lqlw"]["show"]["4lq"]["gou"];
        $oddsNew["lqlw"]["odds"]["5lq"]["nogou"] = $oddsNew["lqlw"]["show"]["5lq"]["shu"];
        $oddsNew["lqlw"]["odds"]["5lq"]["gou"] = $oddsNew["lqlw"]["show"]["5lq"]["gou"];
        $oddsNew["lqlw"]["odds"]["2lw"] = $oddsNew["lqlw"]["show"]["2lw"]["lw1"];
        $oddsNew["lqlw"]["odds"]["3lw"] = $oddsNew["lqlw"]["show"]["3lw"]["lw1"];
        $oddsNew["lqlw"]["odds"]["4lw"] = $oddsNew["lqlw"]["show"]["4lw"]["lw1"];
        $oddsNew["lqlw"]["odds"]["5lw"] = $oddsNew["lqlw"]["show"]["5lw"]["lw1"];

        //取出整个桌面信息
        $deskInfo = file_get_contents(dirname(dirname(dirname(__FILE__))) . "/Six/send.json");
        $deskInfo = json_decode($deskInfo, true);

        //修改状态为已修改
        $deskInfo["version"]++; //版本号+1
        //修改桌面信息的赔率信息
        $deskInfo["odds"] = $oddsNew;
        //保存桌面信息回去
        $filePath = dirname(dirname(dirname(__FILE__))) . "/Six/send.json";
        file_put_contents($filePath, json_encode($deskInfo));
        //返回新的赔率修改结算赔率文件
        return $oddsNew;
    }
    //修改/Six/odds.json
    public function changeSixOddsFile($oddsNew)
    {
        $oddsOld = file_get_contents(dirname(dirname(dirname(__FILE__))) . "/Six/odds.json");
        $oddsOld = json_decode($oddsOld, true);
        //连码
        foreach ($oddsNew["lm"] as $key => $vo) {
            $oddsOld[$key] = $vo;
        }
        //七码五行
        foreach ($oddsNew["qmwx"]["qm"] as $key => $vo) {
            $oddsOld[$key] = $vo;
        }

        foreach ($oddsNew["qmwx"]["wx"] as $key => $vo) {
            $oddsOld[$key] = $vo;
        }
        // 尾数
        foreach ($oddsNew["wsh"]["twsh"] as $key => $vo) {
            $oddsOld[$key] = $vo;
        }

        foreach ($oddsNew["wsh"]["zhtwsh"] as $key => $vo) {
            $oddsOld[$key] = $vo;
        }
        //色波
        foreach ($oddsNew["sb"]["ssb"] as $key => $vo) {
            $oddsOld[$key] = $vo;
        }
        foreach ($oddsNew["sb"]["bb"] as $key => $vo) {
            $oddsOld[$key] = $vo;
        }
        foreach ($oddsNew["sb"]["bbb"] as $key => $vo) {
            $oddsOld[$key] = $vo;
        }
        foreach ($oddsNew["sb"]["qsb"] as $key => $vo) {
            $oddsOld[$key] = $vo;
        }
        // 合肖
        $oddsOld["yshplv"] = $oddsNew["hq"]["odds"]["fenlei"]["ysh"]["yshplv"];
        $oddsOld["jqplv"] = $oddsNew["hq"]["odds"]["fenlei"]["jq"]["jqplv"];
        $oddsOld["danplv"] = $oddsNew["hq"]["odds"]["fenlei"]["dan"]["danplv"];
        $oddsOld["shuangplv"] = $oddsNew["hq"]["odds"]["fenlei"]["shuang"]["shuangplv"];
        $oddsOld["qqplv"] = $oddsNew["hq"]["odds"]["fenlei"]["qq"]["qqplv"];
        $oddsOld["hqplv"] = $oddsNew["hq"]["odds"]["fenlei"]["houq"]["hqplv"];
        $oddsOld["tqplv"] = $oddsNew["hq"]["odds"]["fenlei"]["tq"]["tqplv"];
        $oddsOld["dqplv"] = $oddsNew["hq"]["odds"]["fenlei"]["dq"]["dqplv"];
        foreach ($oddsNew["hq"]["odds"]['fensang']['else'] as $key => $vo) {
            $oddsOld[$key] = $vo;
        }
        foreach ($oddsNew["hq"]["odds"]["fensang"]['gou'] as $key => $vo) {
            $oddsOld[$key] = $vo;
        }

        // 生肖
        foreach ($oddsNew["shx"]["zhq"] as $key => $vo) {
            $index = "zhq" . $key;
            $oddsOld[$index] = $vo;
        }
        foreach ($oddsNew["shx"]["tq"] as $key => $vo) {
            $index = "tq" . $key;
            $oddsOld[$index] = $vo;
        }
        foreach ($oddsNew["shx"]["yq"] as $key => $vo) {
            $index = "yq" . $key;
            $oddsOld[$index] = $vo;
        }
        foreach ($oddsNew["shx"]["zq"] as $key => $vo) {
            $oddsOld[$key] = $vo;
        }

        // 正选不中
        foreach ($oddsNew["zxbzh"] as $key => $vo) {
            $oddsOld[$key] = $vo;
        }
        // 连肖连尾
        $oddsOld["2lqnogou"] = $oddsNew["lqlw"]["show"]["2lq"]["shu"];
        $oddsOld["2lqgou"] = $oddsNew["lqlw"]["show"]["2lq"]["gou"];
        $oddsOld["3lqnogou"] = $oddsNew["lqlw"]["show"]["3lq"]["shu"];
        $oddsOld["3lqgou"] = $oddsNew["lqlw"]["show"]["3lq"]["gou"];
        $oddsOld["4lqnogou"] = $oddsNew["lqlw"]["show"]["4lq"]["shu"];
        $oddsOld["4lqgou"] = $oddsNew["lqlw"]["show"]["4lq"]["gou"];
        $oddsOld["5lqnogou"] = $oddsNew["lqlw"]["show"]["5lq"]["shu"];
        $oddsOld["5lqgou"] = $oddsNew["lqlw"]["show"]["5lq"]["gou"];
        $oddsOld["2lw"] = $oddsNew["lqlw"]["show"]["2lw"]["lw1"];
        $oddsOld["3lw"] = $oddsNew["lqlw"]["show"]["3lw"]["lw1"];
        $oddsOld["4lw"] = $oddsNew["lqlw"]["show"]["4lw"]["lw1"];
        $oddsOld["5lw"] = $oddsNew["lqlw"]["show"]["5lw"]["lw1"];

        // 连码
        foreach ($oddsNew["lma"] as $key => $vo) {
            $oddsOld[$key] = $vo;
        }
        // 正码过关
        foreach ($oddsNew["zhmgg"]["zhy"] as $key => $vo) {
            $oddsOld[$key] = $vo;
        }
        foreach ($oddsNew["zhmgg"]["zher"] as $key => $vo) {
            $oddsOld[$key] = $vo;
        }
        foreach ($oddsNew["zhmgg"]["zhs"] as $key => $vo) {
            $oddsOld[$key] = $vo;
        }
        foreach ($oddsNew["zhmgg"]["zhsi"] as $key => $vo) {
            $oddsOld[$key] = $vo;
        }
        foreach ($oddsNew["zhmgg"]["zhw"] as $key => $vo) {
            $oddsOld[$key] = $vo;
        }
        foreach ($oddsNew["zhmgg"]["zhl"] as $key => $vo) {
            $oddsOld[$key] = $vo;
        }
        // 正码1-6
        foreach ($oddsNew["zhm1-6"]["zh1"] as $key => $vo) {
            $oddsOld[$key] = $vo;
        }
        foreach ($oddsNew["zhm1-6"]["zh2"] as $key => $vo) {
            $oddsOld[$key] = $vo;
        }
        foreach ($oddsNew["zhm1-6"]["zh3"] as $key => $vo) {
            $oddsOld[$key] = $vo;
        }
        foreach ($oddsNew["zhm1-6"]["zh4"] as $key => $vo) {
            $oddsOld[$key] = $vo;
        }
        foreach ($oddsNew["zhm1-6"]["zh5"] as $key => $vo) {
            $oddsOld[$key] = $vo;
        }
        foreach ($oddsNew["zhm1-6"]["zh6"] as $key => $vo) {
            $oddsOld[$key] = $vo;
        }
        // 正码特
        foreach ($oddsNew["zhmt"] as $key => $vo) {
            foreach ($oddsNew["zhmt"][$key] as $keymt => $vo) {
                $oddsOld[$keymt] = $vo;
            }
        }
        // 正码
        foreach ($oddsNew["zhm"] as $key => $vo) {
            $oddsOld[$key] = $vo;
        }
        // 特码
        foreach ($oddsNew["tm"] as $key => $vo) {
            $oddsOld[$key] = $vo;
        }
        // 中一
        foreach ($oddsNew["zhy"] as $key => $vo) {
            $oddsOld[$key] = $vo;
        }
        $oddsOld = json_encode($oddsOld);
        file_put_contents(dirname(dirname(dirname(__FILE__))) . "/Six/odds.json", $oddsOld);
    }
/***************************************************修改广东十一选五赔率************************** */
    //代码有错误，暂时搁一边去
    public function settingGD11x5()
    {
        $param;
        $param = Request::param();
        //修改赔率
        if ($param) {
            // file_put_contents(dirname(dirname(dirname(__FILE__)))."/odds.json");
            return "commit success";
        }
        //返回赔率
        $Gd = new Gd11x5;
        $odds = $Gd->getGdOdds();
        dump($odds);
        return;
    }
}
