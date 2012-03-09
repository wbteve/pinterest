<?php
function bindCacheLink()
{
	global $_FANWE;
	$list = array();
	$res = FDB::query("SELECT * FROM ".FDB::table('friend_link')." WHERE status = 1 ORDER BY sort ASC,id ASC");
	while($data = FDB::fetch($res))
	{
        $list['all'][$data['id']] = $data;

        if(empty($data['img']))
            $list['texts'][] = $data['id'];
        else
		    $list['imgs'][] = $data['id'];
	}

	FanweService::instance()->cache->saveCache('links', $list);
}
?>