<?php
define('ROOT_PATH', str_replace('taobao/notify.php', '', str_replace('\\', '/', __FILE__)));
include ROOT_PATH.'public/data/caches/system/setting.cache.php';
$id = (int)$_REQUEST['exId'];
$sign = trim($_REQUEST['exIdSign']);

if($id == 0 || empty($sign))
	exit;
else
{
	$sign1 = md5($id.$data['setting']['second_taobao_sign']);
	if($sign != $sign1)
		exit;
}

define('GOODS_ID',$id);
require "init.php";
$goods['page'] = trim($_FANWE['request']['pages']);
$goods['alipay_gid'] = trim($_FANWE['request']['alipayGoodsId']);
$goods['status'] = 1;
FDB::update('second_goods',$goods,'gid = '.GOODS_ID);
?>