<?php


/**
 * 
 * 三公数据
 */
//前端请求数据
$sanGong = [
    "game"=>"SanGong",
    //加入房间
    "type"=>"enterRoom",
    //加入牌桌
    "type"=>"enterDesk",
    "id"=>$id,//用户id
    //下注
    "type"=>"play",
    //
];
//返回数据
$sanGong = [
    //加入房间
    $result['roomInfo'],
    //加入牌桌
    $result['']
];