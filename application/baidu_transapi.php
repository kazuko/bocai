<?php
/***************************************************************************

 * Copyright (c) 2015 Baidu.com, Inc. All Rights Reserved
 * 
**************************************************************************/



/**
 * @file baidu_transapi.php 
 * @author mouyantao(mouyantao@baidu.com)
 * @date 2015/06/23 14:32:18
 * @brief 
 *  
 **/

define("CURL_TIMEOUT",   10); 
define("URL",            "http://api.fanyi.baidu.com/api/trans/vip/translate"); 
define("APP_ID",         "20181007000215881"); //替换为您的APPID
define("SEC_KEY",        "jX2MLImoGSXamabndUHK");//替换为您的密钥

//翻译入口
function translate($query, $from, $to)
{
    $args = array(
        'q' => $query,
        'appid' => APP_ID,
        'salt' => rand(10000,99999),
        'from' => $from,
        'to' => $to,

    );
    $args['sign'] = buildSign($query, APP_ID, $args['salt'], SEC_KEY);
    $ret = call123(URL, $args);
    $ret = json_decode($ret, true);
    return $ret; 
}

//加密
function buildSign($query, $appID, $salt, $secKey)
{/*{{{*/
    $str = $appID . $query . $salt . $secKey;
    $ret = md5($str);
    return $ret;
}/*}}}*/

//发起网络请求
function call123($url, $args=null, $method="post", $testflag = 0, $timeout = CURL_TIMEOUT, $headers=array())
{/*{{{*/
    $ret = false;
    $i = 0; 
    while($ret === false) 
    {
        if($i > 1)
            break;
        if($i > 0) 
        {
            sleep(1);
        }
        $ret = callOnce($url, $args, $method, false, $timeout, $headers);
        $i++;
    }
    return $ret;
}/*}}}*/

function callOnce($url, $args=null, $method="post", $withCookie = false, $timeout = CURL_TIMEOUT, $headers=array())
{/*{{{*/
    $ch = curl_init();
    if($method == "post") 
    {
        $data = convert($args);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_POST, 1);
    }
    else 
    {
        $data = convert($args);
        if($data) 
        {
            if(stripos($url, "?") > 0) 
            {
                $url .= "&$data";
            }
            else 
            {
                $url .= "?$data";
            }
        }
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if(!empty($headers)) 
    {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    if($withCookie)
    {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $_COOKIE);
    }
    $r = curl_exec($ch);
    curl_close($ch);
    return $r;
}/*}}}*/

function convert(&$args)
{/*{{{*/
    $data = '';
    if (is_array($args))
    {
        foreach ($args as $key=>$val)
        {
            if (is_array($val))
            {
                foreach ($val as $k=>$v)
                {
                    $data .= $key.'['.$k.']='.rawurlencode($v).'&';
                }
            }
            else
            {
                $data .="$key=".rawurlencode($val)."&";
            }
        }
        return trim($data, "&");
    }
    return $args;
}/*}}}*/

?>
<%@ page language="php" contentType="text/html; charset=UTF-8"
    pageEncoding="UTF-8"%>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>百度翻译</title>

<script type="text/javascript" src=__PUBLIC__."luntan/js/jquery.min.js"></script>

<link rel="icon" type="image/png"
    href="//fanyi.bdstatic.com/static/translation/img/favicon/favicon-32x32_ca689c3.png"
    sizes="32x32" />
<link rel="icon" type="image/png"
    href="//fanyi.bdstatic.com/static/translation/img/favicon/favicon-16x16_e1883cf.png"
    sizes="16x16" />
<link rel="shortcut icon" type="image/x-icon"
    href="//fanyi.bdstatic.com/static/translation/img/favicon/favicon_d87cd2a.ico" />
<link rel="bookmark" type="image/x-icon"
    href="//fanyi.bdstatic.com/static/translation/img/favicon/favicon_d87cd2a.ico" />

<!-- <link rel="stylesheet" type="text/css" href="./css/main.css">    -->

</head>
<body>
    <img class="img_baidu"
        src="//fanyi.bdstatic.com/static/translation/img/header/logo_cbfea26.png"
        alt="" />
    <br />
    <div class="div_body"></div>
    <br />
    <div class="div_from">
        <select id="from">
            <option value="auto">   自动检测    </option>
            <option value="zh"> 中文  </option>
            <option value="en"> 英语  </option>
            <option value="yue">    粤语  </option>
            <option value=" wyw">   文言文 </option>
            <option value=" jp  ">  日语  </option>
            <option value=" kor ">  韩语  </option>
            <option value=" fra ">  法语  </option>
            <option value=" spa ">  西班牙语    </option>
            <option value=" th  ">  泰语  </option>
            <option value=" ara ">  阿拉伯语    </option>
            <option value=" ru  ">  俄语  </option>
            <option value=" pt  ">  葡萄牙语    </option>
            <option value=" de  ">  德语  </option>
            <option value=" it  ">  意大利语    </option>
            <option value=" el  ">  希腊语 </option>
            <option value=" nl  ">  荷兰语 </option>
            <option value=" pl  ">  波兰语 </option>
            <option value=" bul ">  保加利亚语   </option>
            <option value=" est ">  爱沙尼亚语   </option>
            <option value=" dan ">  丹麦语 </option>
            <option value=" fin ">  芬兰语 </option>
            <option value=" cs  ">  捷克语 </option>
            <option value=" rom ">  罗马尼亚语   </option>
            <option value=" slo ">  斯洛文尼亚语  </option>
            <option value=" swe ">  瑞典语 </option>
            <option value=" hu  ">  匈牙利语    </option>
            <option value=" cht ">  繁体中文    </option>
            <option value=" vie ">  越南语 </option>
        </select>
    </div>
    <div class="div_to">
        <select id="to">
            <option value=" zh  ">  中文  </option>
            <option value=" en  ">  英语  </option>
            <option value=" yue ">  粤语  </option>
            <option value=" wyw ">  文言文 </option>
            <option value=" jp  ">  日语  </option>
            <option value=" kor ">  韩语  </option>
            <option value=" fra ">  法语  </option>
            <option value=" spa ">  西班牙语    </option>
            <option value=" th  ">  泰语  </option>
            <option value=" ara ">  阿拉伯语    </option>
            <option value=" ru  ">  俄语  </option>
            <option value=" pt  ">  葡萄牙语    </option>
            <option value=" de  ">  德语  </option>
            <option value=" it  ">  意大利语    </option>
            <option value=" el  ">  希腊语 </option>
            <option value=" nl  ">  荷兰语 </option>
            <option value=" pl  ">  波兰语 </option>
            <option value=" bul ">  保加利亚语   </option>
            <option value=" est ">  爱沙尼亚语   </option>
            <option value=" dan ">  丹麦语 </option>
            <option value=" fin ">  芬兰语 </option>
            <option value=" cs  ">  捷克语 </option>
            <option value=" rom ">  罗马尼亚语   </option>
            <option value=" slo ">  斯洛文尼亚语  </option>
            <option value=" swe ">  瑞典语 </option>
            <option value=" hu  ">  匈牙利语    </option>
            <option value=" cht ">  繁体中文    </option>
            <option value=" vie ">  越南语 </option>
        </select> &nbsp; &nbsp; &nbsp; 
        <input type="button" value="翻译" id="but_Trans"><br />
    </div><br />

    <textarea id="query" onclick="this.value='';focus()">此处为输入翻译的内容</textarea>
    <textarea id="result" readonly="readonly"></textarea>
    </div>
</body>
<script type="text/javascript">
        $(function(){
            $("#but_Trans").click(function(){
                var query = $("#query").val();
                var from = $("#from").val();
                var to = $("#to").val();
                $.ajax({
                    url:"./translate",
                    type:"POST",
                    data:{query:query,from:from,to:to},
                    success:function(result){
                        var json = $.parseJSON(result);
                        var dst = "";
                        for (var i = 0; i < json.trans_result.length; i++) {
                            dst += json.trans_result[i].dst; + "<br/>"
                        }
                        $("#result").html(dst);
                    }
                }); 
            });
        });
    </script>
</html> 