<?php
function bindCacheMedal()
{
	$list = array();
	
	$res = FDB::query("SELECT * FROM ".FDB::table('medal')." WHERE status = 1");
	while($data = FDB::fetch($res))
	{
		$data['big_img'] = './public/medal/big/'.$data['image'];
		$data['small_img'] = './public/medal/small/'.$data['image'];
		$list['all'][$data['mid']] = $data;
		if($data['give_type'] == 0)
		{
			$list['auto'][] = $data['mid'];
			$list[$data['conditions']][] = $data['mid'];
		}
		else
			$list['manual'][] = $data['mid'];
	}
	FanweService::instance()->cache->saveCache('medals', $list);
}
?>