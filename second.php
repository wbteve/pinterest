<?php 
define('MODULE_NAME','Second');

$actions = array('index','create','save');
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
$fanwe->cache_list[] = 'seconds';
$fanwe->initialize();
require fimport('module/second');

$_FANWE['nav_title'] = lang('common','second');

switch(ACTION_NAME)
{
	case 'index':
		FanweService::instance()->cache->loadCache('citys');
		SecondModule::index();
	break;
	case 'create':
		SecondModule::create();
	break;
	case 'save':
		SecondModule::save();
	break;
}
?>