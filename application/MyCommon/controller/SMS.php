<?php
namespace app\MyCommon\controller;
use Think\Controller;
use Aliyun\Core\Config; 
use Aliyun\Core\Profile\DefaultProfile; 
use Aliyun\Core\DefaultAcsClient; 
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest; 
use Aliyun\Api\Sms\Request\V20170525\QuerySendDetailsRequest; 
// header('Access-Control-Allow-Origin:*');//设置跨域
// header('Access-Control-Allow-Credentials: true');
// header('Access-Control-Allow-Headers:*');
// header('Access-Control-Expose-Headers:*');
class SMS{
    // 发送短信
    public static function sendmsg($phone,$code){ 
        // C('SESSION_OPTIONS'); 
        // $mobile=I('post.phoneNumber');
        // $mobile='16620632082';
        require_once 'H:/phpstudy/www/bcweb/extend/Api/Aliyun/vendor/autoload.php';    //此处为你放置API的路径
        Config::load();             //加载区域结点配置
        // $accessKeyId = 'LTAIIqXKIRgKBbQ9';
        $accessKeyId = 'LTAIDM4vCtxf9VXg';
        // $accessKeyId = 'LTAInaCOC7WnqYkY';
        // $accessKeySecret = 'q5OR7Ooo6fV4dxnSGvFn7D9sMeBn1E';
        $accessKeySecret = '5k5i8by51HA8vY90a4FUUsG2rrDU8u';
        // $accessKeySecret = 'IBwmchgLydBEiF9k3ex5jeJqk16sps';
        //$templateCode = '88055149';   //短信模板ID
        //短信API产品名（短信产品名固定，无需修改）
        $product = "Dysmsapi";
        //短信API产品域名（接口地址固定，无需修改）
        $domain = "dysmsapi.aliyuncs.com";
        //暂时不支持多Region（目前仅支持cn-hangzhou请勿修改）
        $region = "cn-hangzhou";
        // 初始化用户Profile实例
        $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);
        // dump($profile);die;
        // 增加服务结点
        DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);
        $acsClient = new DefaultAcsClient($profile);
        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendSmsRequest();
        // 必填，设置短信接收号码 
        $request->setPhoneNumbers($phone); // $moblie是我前台传入的电话
        // 必填，设置签名名称
        // $request->setSignName("购享链");      //此处需要填写你在阿里上创建的签名
        $request->setSignName("黄伟杰");      //此处需要填写你在阿里上创建的签名
        // $request->setSignName("便民服务");      //此处需要填写你在阿里上创建的签名
        // 必填，设置模板CODE
        // $request->setTemplateCode('SMS_125026757');    //短信模板编号
        $request->setTemplateCode('SMS_141515012');    //短信模板编号
        // $request->setTemplateCode('SMS_130929690');    //短信模板编号
        //选填-假如模板中存在变量需要替换则为必填(JSON格式)
        $request->setTemplateParam("{\"code\":\"$code\"}");
        /* //选填-发送短信流水号
            $request->setOutId("1234");*/
        //发起访问请求
        $data['status'] = true;
        $acsResponse = $acsClient->getAcsResponse($request);
        $ss=get_object_vars($acsResponse);
        if($ss['Code']=='OK'){
            return true;
        }else{
            return $ss;
        }
    }

    /**
     * 短信发送记录查询
     * @param string $phoneNumbers 必填, 短信接收号码 (e.g. 12345678901)
     * @param string $sendDate 必填，短信发送日期，格式Ymd，支持近30天记录查询 (e.g. 20170710)
     * @param int $pageSize 必填，分页大小
     * @param int $currentPage 必填，当前页码
     * @param string $bizId 选填，短信发送流水号 (e.g. abc123)
     * @return stdClass
     */
    public static function queryDetails($phoneNumber, $sendDate, $pageSize = 10, $currentPage = 1, $bizId=null) {
        require_once  './Api/Aliyun/vendor/autoload.php';    //此处为你放置API的路径
        Config::load();             //加载区域结点配置
        // $accessKeyId = 'LTAIIqXKIRgKBbQ9';
        $accessKeyId = 'LTAInaCOC7WnqYkY';
        // $accessKeySecret = 'q5OR7Ooo6fV4dxnSGvFn7D9sMeBn1E';
        $accessKeySecret = 'IBwmchgLydBEiF9k3ex5jeJqk16sps';
        //$templateCode = '88055149';   //短信模板ID
        //短信API产品名（短信产品名固定，无需修改）
        $product = "Dysmsapi";
        //短信API产品域名（接口地址固定，无需修改）
        $domain = "dysmsapi.aliyuncs.com";
        //暂时不支持多Region（目前仅支持cn-hangzhou请勿修改）
        $region = "cn-hangzhou";
        // 初始化用户Profile实例
        $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);
        // dump($profile);die;
        // 增加服务结点
        DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);
        $acsClient = new DefaultAcsClient($profile);
        // dump($acsClient);
        // 初始化QuerySendDetailsRequest实例用于设置短信查询的参数
        $request = new QuerySendDetailsRequest();
        // print_r($request);die; 
        // 必填，短信接收号码
        $request->setPhoneNumber($phoneNumber);
        // 选填，短信发送流水号
        $request->setBizId($bizId);
        // 必填，短信发送日期，支持近30天记录查询，格式Ymd
        $request->setSendDate($sendDate);
        // 必填，分页大小
        $request->setPageSize($pageSize);
        // 必填，当前页码
        $request->setCurrentPage($currentPage);
        // 发起访问请求
        $acsResponse = $acsClient->getAcsResponse($request);
        //返回一个对象
        return $acsResponse; 
    }

}   