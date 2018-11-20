<?php
namespace app\MyCommon\controller;

use think\Db;

/**
 *
 */
class Base
{
    public static function updateMedal($uid)
    {
        // 获取用户的勋章列表
        $usermedal = DB::name('user_medal')->where('user_id', $uid)->column('medal_id');
        // 获取用户的金币和积分信息
        $userInfo = DB::name('user_user')->where('id', $uid)->field('gold,bank,integral')->find();
        // 获取积分和金币的勋章列表
        $list = DB::name('system_ranks')->where('flag', 'elt', 1)->field('id,min,flag')->select();
        // dump($list);
        $index = 0;
        foreach ($list as $K =>$v) {
            if ($v['flag']==0) {
                // 积分
                if ($v['min']<=$userInfo['integral'] && !in_array($v['id'], $usermedal)) {
                    // 判断是否满足获取条件并且不存在用户勋章列表，保存到用户勋章列表中
                    $insertData[$index]['user_id'] = $uid;
                    $insertData[$index]['medal_id'] = $v['id'];
                    $insertData[$index]['status'] = 0;
                    $index++;
                }
            } elseif ($v['flag']==1) {
                // 金币
                if ($v['min']<=($userInfo['gold']+$userInfo['bank']) && !in_array($v['id'], $usermedal)) {
                    // 判断是否满足获取条件并且不存在用户勋章列表，保存到用户勋章列表中
                    $insertData[$index]['user_id'] = $uid;
                    $insertData[$index]['medal_id'] = $v['id'];
                    $insertData[$index]['status'] = 0;
                    $index++;
                }
            }
        }
        if (isset($insertData) && !empty($insertData)) {
            DB::name('user_medal')->insertAll($insertData);
        }
    }
    /**
     * goldHistory  金币流水保存方法
     * @param   $username  用户账号
     * @param   $operation  操作说明（标题）
     * @param   $detail 金币流水详情（键值对数组）
     * @return  成功返回添加的id，失败返回false
     */
    public static function goldHistory($username, $operation, $detail)
    {
        // 金币流水记录
        $records = [
            'username'=>$username,
            'operation'=>$operation,
            'detail'=>json_encode($detail),
            'time'=>time(),
        ];
        if ($id = DB::name('gold_history')->insertGetId($records)) {
            return $id;
        } else {
            return false;
        }
    }
    /**
     * goldHistory  金币流水保存方法
     * @param   $username  用户账号
     * @param   $operation  操作说明（标题）
     * @param   $detail 金币流水详情（键值对数组）
     * @return  成功返回添加的id，失败返回false
     */
    public static function sliverHistory($username, $operation, $detail)
    {
        // 积分流水记录
        $records1 = [
            'username'=>$username,
            'operation'=>$operation,
            'detail'=>json_encode($detail),
            'time'=>time(),
        ];
        if ($id = DB::name('sliver_history')->insertGetId($records1)) {
            return $id;
        } else {
            return false;
        }
    }


    // 获取客户端IP地址
    public static function getIP()
    {
        //判断服务器是否允许$_SERVER
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $realip = $_SERVER['REMOTE_ADDR'];
            }
        } else {
            //不允许就使用getenv获取
            if (getenv("HTTP_X_FORWARDED_FOR")) {
                $realip = getenv("HTTP_X_FORWARDED_FOR");
            } elseif (getenv("HTTP_CLIENT_IP")) {
                $realip = getenv("HTTP_CLIENT_IP");
            } else {
                $realip = getenv("REMOTE_ADDR");
            }
        }
        if ($realip=='::1') {
            $realip = '127.0.0.1';
        }
        return $realip;
    }

    // 检测是否为微信客户端
    public static function isWeixin()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return 1;
        } else {
            return 0;
        }
    }
    // 检测是否为手机访问
    public static function isMobile()
    {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset($_SERVER['HTTP_VIA'])) {
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高。其中'MicroMessenger'是电脑微信
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array('nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile','MicroMessenger');
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        // 协议法，因为有可能不准确，放到最后判断
        if (isset($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }
    // $flag标记位，标志是否需要检测IP黑名单
    public static function check($flag = false)
    {
        // 获取相关信息
        $info = DB::name('system_info')
        ->field(
            'security_ip_open,
            security_time_open,
            security_time_start,
            security_time_end,
            security_password,
            error_str'
        )
        ->find();
        // 判断是否为手机登录
        if (!self::isMobile()) {
            self::echo_error($info['error_str']);
        }

        // 判断是否开启ip地址绑定
        if ($info['security_ip_open']) {
            // 获取白名单
            $ips = DB::name('system_ips')->field('ip,status')->select();
            // 获取客户端ip地址
            $ip = self::getIP();
            $f1 = false;
            $f2 = false;
            // 判断当前IP是否
            foreach ($ips as $key => $value) {
                if ($ip==$value['ip']) {
                    if ($value['status']) {
                        $f1 = true;
                    } else {
                        $f2 = true;
                    }
                    break;
                }
            }
            // 判断是否为IP黑名单
            if ($f2) {
                $result = [
                    'code' => -4,
                    'msg' => '该IP已被列为黑名单',
                ];
                return $result;
            }
            // 判断是否需要检测白名单，若需要则判定是否为IP白名单
            if (!$flag  && !$f1) {
                $result = [
                    'code'=>-2,
                    'msg'=>'已开启地址绑定功能，当前IP不在白名单中'
                ];
                return $result;
            }
            if (self::timeOpen($info)) {
                $result = [
                    'code'=>-3,
                    'msg'=>'当前时间段不允许访问该网站',
                ];
                return $result;
            }
        } else {
            // 判断是否需要判断ip是否为黑名单
            if ($flag) {
                // 获取ip黑名单
                $ips = DB::name('system_ips')->field('ip')->where('status', 0)->select();
                // 获取客户端ip地址
                $ip = self::getIP();
                $f2 = false;
                // 判断当前IP是否
                foreach ($ips as $key => $value) {
                    if ($ip==$value['ip']) {
                        $f2 = true;
                        break;
                    }
                }
                // 判断是否为IP黑名单
                if ($f2) {
                    $result = [
                        'code' => -4,
                        'msg' => '该IP已被列为黑名单',
                    ];
                    return $result;
                }
            }
            if (self::timeOpen($info)) {
                $result = [
                    'code'=>-3,
                    'msg'=>'当前时间段不允许访问该网站',
                ];
                return $result;
            }
        }
        $result = [
            'code' => 0,
            'msg' => 'ok',
            'security_password' => $info['security_password']
        ];
        return $result;
    }


    public static function timeOpen($info)
    {
        // 判断是否开启的定时设置
        if ($info['security_time_open']) {
            // 起始时间
            $start = date('Y-m-d').' '.$info['security_time_start'];
            // dump($start);
            // 结束时间
            $end = date('Y-m-d').' '.$info['security_time_end'];
            // dump($end);
            // 转换为时间戳
            $start = strtotime($start);
            $end = strtotime($end);
            // 当前时间戳
            $now = time();
            // dump($now);
            // dump($start);
            // dump($end);
            // die;
            // 判断是否在当前时间范围内
            if ($now>=$start && $now<=$end) {
                return true;
            }
        }
        return false;
    }
    // 输出错误信息并终结程序
    public static function echo_error($msg)
    {
        $str = '<!DOCTYPE html>
		<html>
		<head>
			<title>404-错误</title>
			<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
			<meta http-equiv="X-UA-Compatible" content="ie=edge">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
		</head>
		<body>
			<p style="display:block;width:100%;text-align:center;font-weight:blod;font-size:5vw;box-sizing:border-box;padding:10px;">'.$msg.'</p>
		</body>
		</html>';
        echo $str;
        exit();
    }

    // 上锁|加密（未完成算法）
    public static function lock($string, $key='')
    {
        $multiplicand = 1;
        if ($key) {
            $str = '';
            for ($i=0; $i < mb_strlen($key); $i++) {
                $asci = ord($key[$i]);
                $str .= $key[$i].'：'.$asci.'<br/>';
                // $multiplicand *= $asci;
            }
            echo '<hr/>key符串：'.$key.'<hr/>asci码：<br/>'.rtrim($str, '|').'<hr/>';
        }
        if ($string) {
            $str = '';
            for ($i=0; $i < mb_strlen($string); $i++) {
                $asci = ord($string[$i]);
                $str .= $string[$i].'：'.$asci.'<br/>';
            }
            echo '<hr/>源字符串：'.$string.'<hr/>asci码：<br/>'.rtrim($str, '|').'<hr/>';
        } else {
            return '';
        }
    }
}
