<?php
$uid = (int)$_FANWE['request']['uid'];
if($uid == 0)
	exit;

require_once fimport('dynamic/user');
$cache_file = getTplCache('services/user/tip',$_FANWE['request'],2);
if(getCacheIsUpdate($cache_file,600))
{
	$user = FS("User")->getUserById($uid);
	if($user)
	{
		$user['url'] = FU('u/index',array('uid'=>$uid));
		$user['city'] = '';
		Cache::getInstance()->loadCache('citys');
		$reside_province = $_FANWE['cache']['citys']['all'][$user['reside_province']]['name'];
		$reside_city = $_FANWE['cache']['citys']['all'][$user['reside_city']]['name'];
		if(!empty($reside_province))
			$user['city'] .= $reside_province;
			
		if(!empty($reside_city))
			$user['city'] .= '&nbsp;'.$reside_city;
		
		$medals = array();
		if(!empty($user['medals']))
			$medals = explode(',',$user['medals']);
		
		$user['medals'] = array();
		if($user['is_buyer'] == 1)
		{
			$user['medals'][] = array(
				'name'=>lang('user','buyer_alt'),
				'url'=>FU('settings/buyerverifier'),
				'small_img'=>'./tpl/images/medal_buyer.png',
			);
		}
		
		if($user['is_daren'] == 1)
		{
			$user['medals'][] = array(
				'name'=>sprintf(lang('user','daren_alt'),$_FANWE['setting']['site_name']),
				'url'=>FU('daren/apply'),
				'small_img'=>'./tpl/images/medal_daren.png',
			);
		}
		
		Cache::getInstance()->loadCache('medals');
		foreach($medals as $mid)
		{
			$medal = $_FANWE['cache']['medals']['all'][$mid];
			$medal['url'] = FU('medal/index');
			$user['medals'][] = $medal;
		}
	}
	include template('services/user/tip');
	display($cache_file,false);
}
else
{
	include $cache_file;
	display();
}
?>