<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 定义ThinkPHP框架路径
define('THINK_PATH', './ThinkPHP');

//定义项目名称和路径
define('ADMIN_PATH', str_replace('\\', '/',getcwd()));
define('APP_NAME', basename(ADMIN_PATH));
define('APP_PATH', '.');
define('FANWE_ROOT', str_replace('\\', '/',substr(ADMIN_PATH, 0, -(strlen(APP_NAME) + 1))).'/');

// 加载框架公共入口文件
require(THINK_PATH."/ThinkPHP.php");

//实例化一个网站应用实例
App::run();
?>