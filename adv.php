<?php
define('MODULE_NAME','adv');
define('ACTION_NAME','show');

require dirname(__FILE__).'/core/fanwe.php';
$fanwe = &FanweService::instance();
$fanwe->cache_list = array();
$fanwe->initialize();

require fimport('module/adv');
AdvModule::show();
?>