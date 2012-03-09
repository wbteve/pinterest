<?php
define('SUB_DIR','/'.APP_NAME);
define('MANAGE_HANDLER',true);

session_start();
error_reporting(E_ALL ^ E_NOTICE);

require FANWE_ROOT.'core/fanwe.php';
$fanwe = &FanweService::instance();
$fanwe->is_admin = true;
$fanwe->is_session = false;
$fanwe->is_user = false;
$fanwe->is_cron = false;
$fanwe->is_misc = false;
$fanwe->cache_list = array();
$fanwe->initialize();
if(!MAGIC_QUOTES_GPC)
{
	$_GET = stripslashesDeep($_GET);
	$_POST = stripslashesDeep($_POST);
	$_COOKIE = stripslashesDeep($_COOKIE);
}

include_once FANWE_ROOT.'./common/common.php';
?>