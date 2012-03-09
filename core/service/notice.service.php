<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/**  
 * notice.service.php
 *
 * 系统通知服务
 *
 * @package service
 * @author awfigq <awfigq@qq.com>
 */
class NoticeService
{
	/**
	 * 发送系统通知
	 * @return
	 */
	public function send($data)
	{
		$data['uid'] = (int)$data['uid'];
		$data['create_time'] = TIME_UTC;
		FDB::insert('sys_notice',$data);
		FS('User')->updateNotice($data['uid'],5);
	}
	
	public function delete($uid,$id)
	{
		$uid = (int)$uid;
		$id = (int)$id;
		
		FDB::query("DELETE FROM ".FDB::table('sys_notice')." WHERE uid='$uid' AND id='$id'");
		return 1;
	}
	
	public function getList($uid)
	{
		$uid = (int)$uid;
		$list = FDB::fetchAll('SELECT *  
			FROM '.FDB::table('sys_notice').' 
			WHERE uid = '.$uid.' 
			ORDER BY id DESC');
		
		FDB::query("UPDATE ".FDB::table('sys_notice')." SET status = 1 WHERE uid='$uid' AND status = 0");
		return $list;
	}
}
?>