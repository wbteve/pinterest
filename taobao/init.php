<?php
if(!defined('ROOT_PATH'))
	define('ROOT_PATH', str_replace('taobao/init.php', '', str_replace('\\', '/', __FILE__)));
define('SUB_DIR','/taobao');
define('MODULE_NAME','taobao');

require ROOT_PATH.'core/fanwe.php';
$fanwe = &FanweService::instance();
$fanwe->is_session = false;
$fanwe->is_user = false;
$fanwe->is_cron = false;
$fanwe->is_misc = false;
$fanwe->cache_list = array();
$fanwe->initialize();
?>