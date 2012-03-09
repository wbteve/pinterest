<?php
class shareMapi
{
	public function run()
	{
		global $_FANWE;	
		
		$root = array();
		$root['return'] = 1;

		$share_id = (int)$_FANWE['requestData']['share_id'];
		$share = FS('Share')->getShareById($share_id);
		if($share)
		{
			$cache_data = fStripslashes(unserialize($share['cache_data']));
			unset($share['cache_data']);
			$share['time'] = getBeforeTimelag($share['create_time']);
			$share['url'] = FU('note/index',array('sid'=>$share_id),true);
			$parses = m_express(&$share['content']);
			$share['parse_users'] = $parses['users'];
			$share['parse_events'] = $parses['events'];
			$share_user = FS('User')->getUserById($share['uid']);
			$share['user_name'] = $share_user['user_name'];
			$share['user_avatar'] = avatar($share_user['uid'],'m',$share_user['server_code'],1,true);

			if($_FANWE['uid'] == $share['uid'])
				$share['is_follow_user'] = -1;
			else
			{
				if(FS('User')->getIsFollowUId($share['uid']))
					$share['is_follow_user'] = 1;
				else
					$share['is_follow_user'] = 0;
			}

			$share['collects'] = array();
			if(!empty($cache_data['collects']))
			{
				$collect_ids = array_slice($cache_data['collects'],0,20);
				if($share['is_follow_user'] == 1)
				{
					if($ckey = array_search($_FANWE['uid'],$collect_ids) === FALSE)
					{
						array_unshift($collect_ids,$_FANWE['uid']);
						array_pop($collect_ids);
					}
					else
					{
						unset($collect_ids[$ckey]);
						array_unshift($collect_ids,$_FANWE['uid']);
					}
				}

				$collect_ids = implode(',',$collect_ids);
				$res = FDB::query("SELECT uid,user_name,server_code FROM ".FDB::table('user').' 
					WHERE uid IN ('.$collect_ids.')');
				while($item = FDB::fetch($res))
				{
					$item['user_avatar'] = avatar($item['uid'],'m',$item['server_code'],1,true);
					unset($item['server_code']);
					$share['collects'][] = $item;
				}
			}
			
			$share['imgs'] = array();
			if(!empty($cache_data['imgs']))
			{
				foreach($cache_data['imgs']['all'] as $img)
				{
					if($img['type'] == 'g')
					{
						$img['goods_url'] = $img['url'];
						$img['price_format'] = priceFormat($img['price']);
					}
					else
					{
						$img['name'] = '';
						$img['price'] = '';
						$img['goods_url'] = '';
						$img['taoke_url'] = '';
						$img['price_format'] = '';
					}
					unset($img['url']);
					$img['small_img'] = getImgName($img['img'],468,468,0,true);
					$share['imgs'][] = $img;
				}
			}
		}

		$root['item'] = $share;
		m_display($root);
	}
}
?>