<?php
define('MODULE_NAME','Link');
define('ACTION_NAME','index');

require dirname(__FILE__).'/core/fanwe.php';
$fanwe = &FanweService::instance();
$fanwe->initialize();

require fimport('module/link');
LinkModule::index();
?>