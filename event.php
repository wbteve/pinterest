<?php 
define('MODULE_NAME','Event');
$actions = array('index','detail','list');
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
$fanwe->initialize();

require fimport('module/event');

switch(ACTION_NAME)
{
	case 'index':
		EventModule::index();
	break;
	case 'detail':
		EventModule::detail();
	break;
	case 'list':
		EventModule::listdetail();
	break;
}
?>