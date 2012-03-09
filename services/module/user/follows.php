<?php
if($_FANWE['uid'] == 0)
{
	echo '{"status":0}';
	exit;
}

$uids = explode(',',$_FANWE['request']['uids']);
if(empty($uids))
{
	echo '{"status":0}';
	exit;
}

foreach($uids as $uid)
{
	$uid = intval($uid);
	if($uid == 0)
		continue;
	
	if(!FS('User')->getUserExists($uid))
		continue;
		
	if(!FS('User')->getIsFollowUId($uid))
	{
		FS('User')->followUser($uid);
	}
}

outputJson(array('status'=>1));
?>