<?php 
define('MODULE_NAME','Medal');
$actions = array('index','u','apply','save');
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
$fanwe->cache_list[] = 'medals';
$fanwe->initialize();

require fimport('module/medal');
switch(ACTION_NAME)
{
	case 'index':
		MedalModule::index();
		break;
	case 'u':
		MedalModule::u();
		break;
	case 'apply':
		MedalModule::apply();
		break;
	case 'save':
		MedalModule::save();
		break;
}
?>