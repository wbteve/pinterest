<?php
// +----------------------------------------------------------------------
// | ThinkPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2008 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$
define('SUB_DIR','/update');
define('FANWE_ROOT',str_replace('\\', '/',substr(dirname(__FILE__), 0, -6)));
// 定义ThinkPHP框架路径
define('THINK_PATH', FANWE_ROOT.'admin/ThinkPHP');
//定义项目名称和路径
define('APP_NAME', 'update');
define('APP_PATH', FANWE_ROOT.'update');
define('RUNTIME_PATH', FANWE_ROOT.'update/runtime/');

// 加载框架入口文件
require(THINK_PATH."/ThinkPHP.php");
define('NO_CACHE_RUNTIME',True);
define('STRIP_RUNTIME_SPACE',false);
//实例化一个网站应用实例
$AppWeb = new App();
//应用程序初始化
$AppWeb->run();
?>