<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
namespace think;
//解决同源策略
header('Access-Control-Allow-Origin: *'); //数据来源
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept'); //访问方式
// 加载基础文件
require __DIR__ . '/thinkphp/base.php';
define('__ROOT__', __DIR__);
define('DS', DIRECTORY_SEPARATOR);
// 支持事先使用静态方法设置Request对象和Config对象
// 执行应用并响应
Container::get('app')->run()->send();

