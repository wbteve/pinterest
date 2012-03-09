<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/**  
 * words.service.php
 *
 * 单词服务
 *
 * @package service
 * @author awfigq <awfigq@qq.com>
 */
class SecondService
{
	public function deleteGoods($gid)
	{
		global $_FANWE;
		$gid = (int)$gid;
		$goods = FDB::fetchFirst('SELECT *
			FROM '.FDB::table('second_goods').' WHERE gid = '.$gid);
		if(empty($goods))
			return;
		
		$share_id = $goods['share_id'];
		$share = FS('Share')->getShareById($share_id);
		FS('Share')->deleteShare($share_id);
		$uid = (int)$share['uid'];
		FDB::query('UPDATE '.FDB::table('user_count').' SET seconds = seconds - 1 WHERE uid = '.$uid);
		FDB::delete('second_goods','gid = '.$gid);
		FS('Medal')->runAuto($uid,'seconds');
	}
}
?>