<?php
class InviteModule
{
	public function index()
	{
		global $_FANWE;
		if($_FANWE['uid'] == 0)
			fHeader("location: ".FU('user/login',array('refer'=>FU('invite/index'))));
		
		FanweService::instance()->cache->loadCache('medals');
		$medal_list = array();
		foreach($_FANWE['cache']['medals']['referrals'] as $id)
		{
			$medal = $_FANWE['cache']['medals']['all'][$id];
			$medal_list[] = $medal;
		}
		
		$invite_title = $_FANWE['setting']['site_title'];
		$invite_content = lang('invite','invite_content');
		$invite_content = sprintf($invite_content,$_FANWE['setting']['site_name']);
		$invite_url = $_FANWE['site_url'].'?invite='.$_FANWE['uid'];
		$invite_types = array('kaixin','renren','sina','tqq','douban','qzone','baidu');
		$invite_pic = $_FANWE['site_url'].$_FANWE['setting']['site_logo'];
		$sns_links = getSnsLink($invite_types,$invite_title,$invite_url,$invite_content,$invite_pic);
		
		include template('page/invite/invite_index');
		display();
	}
}
?>