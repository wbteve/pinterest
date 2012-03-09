<?php 
define('MODULE_NAME','Shop');

$actions = array('index','show');
$action = 'index';

if(isset($_REQUEST['action']))
{
	$action = strtolower($_REQUEST['action']);
	if(!in_array($action,$actions))
		$action = 'index';
}

define('ACTION_NAME',$action);

require dirname(__FILE__).'/core/fanwe.php';
$fanwe = &FanweService::instance();
$fanwe->cache_list[] = 'shops';
$fanwe->initialize();
require fimport('module/shop');

$_FANWE['nav_title'] = lang('common','shop');

switch(ACTION_NAME)
{
	case 'index':
		ShopModule::index();
	break;
	case 'show':
		ShopModule::show();
	break;
}
?>