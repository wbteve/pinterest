<?php 
define('MODULE_NAME','Club');

$actions = array('index','donewtopic','newtopic','forum','best','detail','post');
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
$fanwe->cache_list[] = 'forums';
$fanwe->initialize();

require fimport('module/club');

switch(ACTION_NAME)
{
	case 'index':
		ClubModule::index();
		break;
	case 'donewtopic':
		ClubModule::donewtopic();
		break;
	case 'newtopic':
		ClubModule::newtopic();
		break;
	case 'forum':
		ClubModule::forum();
		break;
	case 'best':
		ClubModule::best();
		break;
	case 'detail':
		ClubModule::detail();
		break;
	case 'post':
		ClubModule::post();
		break;
	default:
		ClubModule::index();
		break;
}
?>