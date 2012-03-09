<?php 
define('MODULE_NAME','Misc');

$actions = array('verify','topicattention');
$action = '';

if(isset($_REQUEST['action']))
{
	$action = strtolower($_REQUEST['action']);
	if(!in_array($action,$actions))
		$action = '';
}

if(empty($action))
	exit('Access Denied');

define('ACTION_NAME',$action);

require dirname(__FILE__).'/core/fanwe.php';
$fanwe = &FanweService::instance();
switch ('ACTION_NAME')
{
	case 'verify':
		$fanwe->is_session = false;
		$fanwe->is_user = false;
		$fanwe->is_cron = false;
	break;
	
	default:
	break;
}

$fanwe->initialize();

require fimport('module/misc');

switch(ACTION_NAME)
{
	case 'verify':
		MiscModule::verify();
		break;
	case 'topicattention':
		MiscModule::TopicAttention();
		break;
	break;
}
?>