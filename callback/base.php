<?php
define('ROOT_PATH', str_replace('callback/base.php', '', str_replace('\\', '/', __FILE__)));
define('SUB_DIR','/callback');
define('MODULE_NAME','callback');

require ROOT_PATH.'core/fanwe.php';
$fanwe = &FanweService::instance();
$fanwe->cache_list = array('logins');
$fanwe->is_cron = false;
$fanwe->is_misc = false;
$fanwe->initialize();

$callback_type = $_FANWE['cookie']['callback_type'];
if(empty($callback_type))
	exit;
?>