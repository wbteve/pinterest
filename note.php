<?php 
define('MODULE_NAME','Note');
$actions = array('index','g','m');
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

require fimport('module/note');

switch(ACTION_NAME)
{
	case 'index':
		NoteModule::index();
		break;
	case 'g':
		NoteModule::goods();
		break;
	case 'm':
		NoteModule::photo();
		break;
}
?>