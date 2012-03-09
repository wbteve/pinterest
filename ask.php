<?php 
define('MODULE_NAME','Ask');

$actions = array('index','newtopic','forum','detail','donewtopic','publishtopic');
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
$fanwe->cache_list[] = 'asks';
$fanwe->initialize();

require fimport('module/ask');

switch(ACTION_NAME)
{
	case 'index':
		AskModule::index();
		break;
	case 'newtopic':
		AskModule::newtopic();
		break;
	case 'forum':
		AskModule::forum();
		break;
	case 'detail':
		AskModule::detail();
		break;
	case 'donewtopic':
		AskModule::donewtopic();
		break;
	case 'publishtopic':
		AskModule::publishtopic();
		break;
	default:
		AskModule::index();
		break;
}
?>