<?php
$sync_bind_exists = $_FANWE['cookie']['sync_bind_exists'];
$result = array();
if(empty($sync_bind_exists))
{
	$result['status'] = 0;
	outputJson($result);
}

$sync_bind_exists = unserialize(authcode($sync_bind_exists, 'DECODE'));
$is_bind = $_FANWE['request']['is_bind'];

if($is_bind)
{
	$avatar = '';
	$type = $sync_bind_exists['type'];
	$keyid = $sync_bind_exists['keyid'];
	FDB::delete('user_bind',"type = '".$type."' AND keyid = '".$keyid."'");
	require_once FANWE_ROOT."core/class/user/".$type.".class.php";
	$class = ucfirst($type).'User';
	$class = new $class();
	$class->bindByData($sync_bind_exists);
	$result['status'] = 1;
}
else
{
	$result['status'] = 0;
}

fSetCookie('sync_bind_exists','');
outputJson($result);
?>