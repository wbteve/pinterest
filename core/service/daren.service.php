<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/**  
 * daren.service.php
 *
 * 达人服务类
 *
 * @package service
 * @author awfigq <awfigq@qq.com>
 */
class DarenService
{
	public function insertDaren()
	{
		clearCacheDir('daren');
	}
	
	/**  
	 * 获取首页今日达人
	 * @param int $num 数量
	 * @return array
	 */
	public function getIndexTodayDaren($num = 1)
	{
		global $_FANWE;
		$today_time = getTodayTime();
		$data = getCache('daren/index/today');
		if($data === NULL || $data['time'] != $today_time)
		{
			$sql = 'SELECT uid,index_img,img  
				FROM '.FDB::table('user_daren').' 
				WHERE status = 1 AND is_index = 1 AND index_img <> \'\' AND day_time = '.$today_time;
			
			$data['list'] = FDB::fetchAll($sql);
			if(empty($data['list']))
			{
				$sql = 'SELECT uid,index_img,img  
					FROM '.FDB::table('user_daren').' 
					WHERE status = 1 AND is_index = 1 AND index_img <> \'\' ORDER BY day_time DESC LIMIT 0,5';
				$data['list'] = FDB::fetchAll($sql);
			}
			
			$data['time'] = $today_time;
			
			setCache('daren/index/today',$data);
		}
		
		if(empty($data['list']))
			return false;
		
		$daren = $data['list'][array_rand($data['list'])];
		$_FANWE['index_today_daren'] = $daren['uid'];
		$daren['user'] = FS('User')->getUserCache($daren['uid']);
		return $daren;
	}
	
	/**  
	 * 获取推荐达人
	 * @return array
	 */
	public function getBestDarens($num = 4)
	{
		global $_FANWE;
		$list = getCache('daren/index/best');
		if($list === NULL)
		{
			$list = array();
			$sql = 'SELECT *  
				FROM '.FDB::table('user_daren').' 
				WHERE status = 1 AND is_best = 1 
				ORDER BY id DESC';
			$res = FDB::query($sql);
			while($data = FDB::fetch($res))
			{
				$list[$data['uid']] = $data;
			}
			setCache('daren/index/best',$list);
		}
		
		if(!empty($_FANWE['index_today_daren']))
		{
			unset($list[$_FANWE['index_today_daren']]);
		}
		
		$list = array_slice($list,0,$num);
		return $list;
	}
	
	/**  
	 * 获取达人列表
	 * @return array
	 */
	public function getDarens($limit = '0,15')
	{
		$key = str_replace(',','_',$limit);
		$list = getCache('daren/index/list_'.$key);
		if($list === NULL)
		{
			$today_time = getTodayTime() + 86400;
			$sql = 'SELECT ud.uid,ud.reason,u.user_name,u.server_code 
				FROM '.FDB::table('user_daren').' AS ud 
				INNER JOIN '.FDB::table('user').' AS u ON u.uid = ud.uid  
				WHERE ud.status = 1 AND ud.day_time < '.$today_time.'
				ORDER BY ud.id DESC LIMIT '.$limit;
			$list = FDB::fetchAll($sql);
			setCache('daren/index/list_'.$key,$list);
		}
		
		return $list;
	}
	
	/**
	 * 取消达人
	 * @return bool
	 * */
	public function removeDaren($uid)
	{
		if(!$uid)
			return false;
		$sql = 'UPDATE '.FDB::table('user').' SET is_daren = 0 WHERE uid = '.$uid;

		if(FDB::query($sql))
		{
			$del_sql = "DELETE FROM ".FDB::table("user_daren")." WHERE uid = ".$uid;
			if(FDB::query($del_sql))
				return true;
			else
				return false;
		}
		else
			return false;
	}
}
?>