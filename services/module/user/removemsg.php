<?php
$mlid = trim($_FANWE['request']['mlid']);
$mid = trim($_FANWE['request']['mid']);
$nid = trim($_FANWE['request']['nid']);
if(empty($mlid) && empty($mid) && empty($nid))
	exit;

$mlids = explode(',',$mlid);
$mids = explode(',',$mid);
$nids = explode(',',$nid);

if(count($mlids) > 0)
{
	foreach($mlids as $mlid)
	{
		$result['mlid'][$mlid] = FS('Message')->deleteByMlid($_FANWE['uid'],$mlid);
	}
}

if(count($mids) > 0)
{
	foreach($mids as $mid)
	{
		$result['mid'][$mid] = FS('Message')->deleteSysMsg($_FANWE['uid'],$mid);
	}
}

if(count($nids) > 0)
{
	foreach($nids as $nid)
	{
		$result['nid'][$nid] = FS('Notice')->delete($_FANWE['uid'],$nid);
	}
}

outputJson($result);
?>