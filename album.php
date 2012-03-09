<?php 
define('MODULE_NAME','Album');

$actions = array('index','show','category','tag','create','edit','save');
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
$fanwe->cache_list[] = 'albums';
$fanwe->initialize();
require fimport('module/album');

$_FANWE['nav_title'] = lang('common','album');

switch(ACTION_NAME)
{
	case 'index':
		AlbumModule::index();
	break;
	case 'show':
		AlbumModule::show();
	break;
	case 'category':
		AlbumModule::category();
	break;
	case 'tag':
		AlbumModule::tag();
	break;
	case 'create':
		AlbumModule::create();
	break;
	case 'edit':
		AlbumModule::edit();
	break;
	case 'save':
		AlbumModule::save();
	break;
}
?>