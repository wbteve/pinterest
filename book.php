<?php 
define('MODULE_NAME','Book');

$actions = array('cate','shopping','search','dapei','look');

if(isset($_REQUEST['action']))
{
	$action = strtolower($_REQUEST['action']);
	if(!in_array($action,$actions))
		$action = 'shopping';
}

define('ACTION_NAME',$action);

require dirname(__FILE__).'/core/fanwe.php';
$fanwe = &FanweService::instance();
$fanwe->initialize();

require fimport('module/book');

switch(ACTION_NAME)
{
	case 'cate':
		BookModule::cate();
	break;
	case 'shopping':
		BookModule::shopping();
	break;
	case 'search':
		BookModule::search();
	break;
	case 'dapei':
		define('IS_DAPAI',true);
		BookModule::dapei();
	break;
	case 'look':
		define('IS_DAPAI',true);
		BookModule::look();
	break;
}
?>