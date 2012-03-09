<?php 
define('MODULE_NAME','Invite');
define('ACTION_NAME','index');

require dirname(__FILE__).'/core/fanwe.php';
$fanwe = &FanweService::instance();
$fanwe->initialize();

require fimport('module/invite');
$_FANWE['nav_title'] = lang('common','invite');
InviteModule::index();
?>