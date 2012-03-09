<?php 
define('MODULE_NAME','Style');
define('ACTION_NAME','index');
define('IS_DAPAI',true);

require dirname(__FILE__).'/core/fanwe.php';
$fanwe = &FanweService::instance();
$fanwe->initialize();

require fimport('module/style');
StyleModule::index();
?>