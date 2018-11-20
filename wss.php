<?php
require_once "D:\phpstudy\PHPTutorial\WWW\bc\\vendor\workerman\workerman\Autoloader.php";
use Workerman\Worker;

// 证书最好是申请的证书
$context = array(
    // 更多ssl选项请参考手册 http://php.net/manual/zh/context.ssl.php
    'ssl' => array(
        // 请使用绝对路径
        'local_cert'                 => '磁盘路径/server.pem', // 也可以是crt文件
        'local_pk'                   => '磁盘路径/server.key',
        'verify_peer'                => false,
        // 'allow_self_signed' => true, //如果是自签名证书需要开启此选项
    )
);
// 这里设置的是websocket协议（端口任意，但是需要保证没被其它程序占用）
$worker = new Worker('websocket://0.0.0.0:443', $context);
// 设置transport开启ssl，websocket+ssl即wss
$worker->transport = 'ssl';
$worker->onMessage = function($con, $msg) {
    $con->send('ok');
};

Worker::runAll();