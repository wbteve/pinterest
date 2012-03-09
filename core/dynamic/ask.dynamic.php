<?php
/**
 * 热心排行榜
 */
function getHotUser()
{
	$cache_file = getTplCache('inc/ask/hot_user',array(),1);
	if(getCacheIsUpdate($cache_file,300))
	{
		//热心排行榜
		$sql = "SELECT uc.uid,u.user_name,u.server_code,uc.ask,ask_posts,uc.ask_best_posts 
			FROM ".FDB::table("user_count")." AS uc 
			INNER JOIN ".FDB::table("user")." AS u ON u.uid = uc.uid 
			ORDER BY uc.ask_best_posts DESC ,uc.ask_posts DESC limit 10";
		
		$hot_users = FDB::fetchAll($sql);
		$args['hot_users'] = &$hot_users;
	}
	
	return tplFetch('inc/ask/hot_user',$args,'',$cache_file);
}
?>