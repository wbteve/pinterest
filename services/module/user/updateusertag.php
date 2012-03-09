<?php
if($_FANWE['uid'] == 0)
	exit;

$user_tags = array();
$tags = explode(',',$_FANWE['request']['tags']);
$i = 0;
foreach($tags as $tag)
{
	if($tag != '' && $i < 20)
	{
		$user_tags[] = addslashes(urldecode($tag));
	}
}

FS('User')->updateUserTags($_FANWE['uid'],$user_tags);

outputJson(array('status'=>true,'tags'=>$user_tags));
?>