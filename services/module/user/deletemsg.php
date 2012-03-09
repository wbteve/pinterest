<?php
$miid = intval($_FANWE['request']['miid']);

if(empty($miid))
	exit;

$result['status'] = FS('Message')->deleteByMiid($_FANWE['uid'],$miid);
outputJson($result);
?>