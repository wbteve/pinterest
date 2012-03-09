<?php
//未登陆直接退出
$uid = $_FANWE['uid'];
if($uid == 0)
	exit;

$result = array();
$check_result = FS('Share')->checkWord($_FANWE['request']['content'],'content');
if($check_result['error_code'] == 1)
{
	$result['status'] = 0;
	$result['error'] = $check_result['error_msg'];
	outputJson($result);
}

$data = FS('Share')->saveRelay($_FANWE['request']);
$result['status'] = 1; 
outputJson($result);
?>