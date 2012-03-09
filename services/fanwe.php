<?php
define('FANWE_ROOT', str_replace('services/fanwe.php', '', str_replace('\\', '/', __FILE__)));
require FANWE_ROOT.'core/fanwe.php';

@set_include_path(FANWE_ROOT.'services/');
require "Zend/Amf/Server.php";
require "fanwe.service.php";

$fanwe = &FanweService::instance();
$fanwe->initialize();

$server = new Zend_Amf_Server();
$server -> setClass('FanweAmfService');
echo $server -> handle();
?>