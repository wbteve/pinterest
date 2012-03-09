<?php
define('ROOT_PATH', str_replace('manage/manage.php', '', str_replace('\\', '/', __FILE__)));
define('SUB_DIR','/manage');
define('MODULE_NAME','manage');

if(isset($_REQUEST['m']) && isset($_REQUEST['a']))
{
	$module = strtolower($_REQUEST['m']);
	$action = strtolower($_REQUEST['a']);

    if(preg_match('/[^a-z0-9_]/',$module) || preg_match('/[^a-z0-9_]/',$action))
        exit;

	define('HANDLER_FILE',ROOT_PATH.'manage/module/'.$module.'/'.$action.'.php');
	if(!file_exists(HANDLER_FILE))
		exit;
}
else
	exit;

require ROOT_PATH.'core/fanwe.php';
$fanwe = &FanweService::instance();
$fanwe->initialize();

if($_FANWE['uid'] == 0)
	exit;

include HANDLER_FILE;
?>