<?php
define('ROOT_PATH', str_replace('services/service.php', '', str_replace('\\', '/', __FILE__)));
define('SUB_DIR','/services');
define('MODULE_NAME','service');

if(isset($_REQUEST['m']) && isset($_REQUEST['a']))
{
	$module = strtolower($_REQUEST['m']);
	$action = strtolower($_REQUEST['a']);

    if(preg_match('/[^a-z0-9_]/',$module) || preg_match('/[^a-z0-9_]/',$action))
        exit;

	define('ACTION_NAME',$action);

	define('HANDLER_FILE',ROOT_PATH.'services/module/'.$module.'/'.$action.'.php');

	if(!file_exists(HANDLER_FILE))
		exit;
	define('COMMON_FILE',ROOT_PATH.'services/module/'.$module.'/common.php');
}
else
	exit;

require ROOT_PATH.'core/fanwe.php';
$fanwe = &FanweService::instance();
$fanwe->initialize();
//如果模块全局文件存在，引用模块全局文件common.php
if(file_exists(COMMON_FILE))
	include COMMON_FILE;

include HANDLER_FILE;
?>