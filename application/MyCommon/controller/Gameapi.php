<?php
namespace app\MyCommon\controller;

/**
 * 游戏数据API
 */
class Gameapi
{

    /*免费接口参数
    *
    **江苏	快3	 jsks	福彩	每日:82 期,开奖频率:10分钟
    **重庆	时时彩	cqssc	福彩	每日:120 期,开奖频率:10分钟
    **北京	赛车PK10	 bjpks	福彩	每日:179 期,开奖频率:5分钟
    **广东	11选5	gdsyxw	体彩	每日:84 期,开奖频率:10分钟
    **香港	六合彩	xglhc	赛马会	每周:3 期,开奖星期:二、四、六

    **全国	胜负彩	sfc	足彩	每期:14 场,开奖频率:不定期
    **全国	半全场	bqc	足彩	每期:12 场,开奖频率:不定期
    **全国	进球彩	jqc	足彩	每期:8 场,开奖频率:不定期
    */
    // 获取游戏开奖数据
    public static function getGameData($code='', $row=5, $type='json')
    {
        $url = "http://www.caipiaojieguo.com/api/lottrey?biaoshi=".$code."&format=".$type."&rows=".$row;
        $data = self::httpRequest( $url, 30 );
        $returnValue[$type] = $data;
        if ($type == 'json') {
            $returnValue['data'] = json_decode($data, true);
        } else {
            $returnValue['data'] = json_decode(json_encode(simplexml_load_string($data)), true);
        }
        // dump($returnValue['data']['data']);die;
        if ((is_array($returnValue['data']['data']) || is_object($returnValue['data']['data']))&&!empty($returnValue['data']['data'])) {
            //装换成熟悉的格式
            foreach ($returnValue['data']['data'] as $key=>$vo) {
                $returnValue['data']['data'][$key]['opencode'] = $vo['result'];
                unset($returnValue['data']['data'][$key]['result']);
                $returnValue['data']['data'][$key]['expect'] = $vo['qishu'];
                unset($returnValue['data']['data'][$key]['qishu']);
                $returnValue['data']['data'][$key]['opentime'] = $vo['open_time'];
                unset($returnValue['data']['data'][$key]['open_time']);
            }
        }
        
        return $returnValue;
    }

    public static function gd11x5($data, $type, $callback='')
    {
        $data = self::getGameData('gd11x5', 1);
        $result = $data['data']['data'][0];
        $codes = explode(',', $result['opencode']);
        switch ($type) {
            case '':
                # code...
                break;
            
            default:
                # code...
                break;
        }
        dump($result);
        if ($callback) {
            $callback($result);
        }
    }

    /**
     * web请求数据
     * @param url 请求路径
     * @param data 请求参数
     * @param method 请求方式
     * @param time_out 请求超时时间
     * @param callback 回调方法
     */
    public static function httpRequest( $url, $time_out = 5, $data='', $method='GET', $callback='' )
    {
        if (!$url || $time_out <= 0) {
            return false;
        }
        $ch = curl_init();
        if (strtoupper($method)=='POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } elseif ($data) {
            $url .= '?'.http_bulid_query($data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, (int)$time_out);
        curl_setopt($ch, CURLOPT_URL, (string)$url);
        $info = curl_exec($ch);
        if ($callback) {
            $callback($info, $ch);
        }
        if ($info === false) {
            throw new \Exception('curl_errmsg:'.curl_errno($ch).":".curl_error($ch));
        }
        curl_close($ch);
        return $info;
    }
    // 输出帮助文档
    public static function getParam()
    {
        $str = '<!DOCTYPE html>
		<html>
		<head>
			<title>帮助文档</title>
			<meta http-equiv="Content-type" content="text/html; charset=utf-8">
			<meta http-equiv="X-UA-Compatible" content="ie=edge">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
		</head>
		<body>
			<table style="border:1px solid #ccc;">
				<tr>
					<td colspan="2" style="border:1px solid #ccc;padding:10px;">
						<p>function get_game_data($code[, $row = 5, $type = "json"]);</p>
						<p>功能解析：获取游戏数据</p>
					</td>
				</tr>
				<tr>
					<td style="border:1px solid #ccc;padding:10px;">参数$code</td>
					<td style="border:1px solid #ccc;padding:10px;">
						<p>cqssc：重庆时时彩；</p>
						<p>bjpk10：北京赛车PK10；</p>
						<p>gd11x5：广东11选5；</p>
						<p>jsk3：江苏快3。</p>
					</td>
				</tr>
				<tr>
					<td style="border:1px solid #ccc;padding:10px;">参数$row</td>
					<td style="border:1px solid #ccc;padding:10px;">请求数据的条数，默认为5条。</td>
				</tr>
				<tr>
					<td style="border:1px solid #ccc;padding:10px;">参数$type</td>
					<td style="border:1px solid #ccc;padding:10px;">返回数据的格式，可能值json|Xml，默认为json。</td>
				</tr>
			</table>
		</body>
		</html>';
        echo $str;
        exit();
    }
}
