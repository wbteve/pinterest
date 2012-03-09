<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/**  
 * statistics.service.php
 *
 * 统计服务
 *
 * @package service
 * @author awfigq <awfigq@qq.com>
 */
class StatisticsService
{
	/**
	 * 获取统计信息
	 * @param int $uid 会员编号
	 * @param string $type 类型
	 * @return
	 */
	public function get($uid,$type)
	{
		$uid = (int)$uid;
		$type = StatisticsService::getTypeByKey($type);		
		if (!$type || !$uid)
			return array();
		
		return FDB::fetchFirst('SELECT * FROM '.FDB::table('user_statistics').' WHERE uid = '.$uid.' AND type = '.$type);
	}
	
	public function getUserStatistics($uid,$is_update = false)
	{
		$uid = (int)$uid;
		if (!$uid)
			return array();
		
		static $statistics = array();
		if(!isset($statistics[$uid]) || $is_update)
		{
			$types = StatisticsService::getTypes();
			$res = FDB::query('SELECT * FROM '.FDB::table('user_statistics').' WHERE uid = '.$uid);
			while($data = FDB::fetch($res))
			{
				$type = $types[$data['type']];
				$statistics[$uid][$type] = $data['num'];
			}
		}
		return $statistics[$uid];
	}
	
	public function updateUserStatistics($uid)
	{
		$uid = (int)$uid;
		if (!$uid)
			return array();
		
		$bln = false;
		$today_time = getTodayTime();
		$res = FDB::query('SELECT * FROM '.FDB::table('user_statistics').' WHERE uid = '.$uid);
		while($data = FDB::fetch($res))
		{
			if ($data['last_time'] < $today_time && $data['type'] != 1)
			{
				$change_day = ($today_time - $data['last_time']) / 86400;
				$change = $change_day == 1 ? 1 : 2 - $change_day;
				$new_num = $statistic['num'] + $change;
				
				if ($new_num <= 0)
					$new_num = 1;
				
				StatisticsService::update(array('last_time'=>$today_time,'num'=>$new_num), $uid, $data['type']);
				$bln = true;
			}
		}
		
		if($bln)
			StatisticsService::getUserStatistics($uid,true);
	}
	
	/**
	 * 记录一个会员统计
	 * @param int $uid 会员编号
	 * @param string $type 类型
	 * @param int $last_time 最后统计时间
	 * @param bool $clear 未连续时清零
	 * @return array($num,$change)
	 */
	public function add($uid,$type,$last_time=0,$clear = false)
	{
		global $_FANWE;
		
		if($_FANWE['setting']['user_is_medal'] == 0)
			array(false,'无需操作');
		
		$today_time = getTodayTime();
		if ($last_time && $last_time > $today_time)
			array(false,'无需操作');
		
		$uid = (int)$uid;
		$type = StatisticsService::getTypeByKey($type);
		if (!$type || !$uid)
			return array(false,'数据有误');
		
		$statistic = StatisticsService::get($uid,$type);
		if (!$statistic)
		{
			StatisticsService::insert(array('uid'=>$uid,'type'=>$type,'last_time'=>$today_time,'num'=>1));
			return array(1,1);
		}
		else
		{
			if ($statistic['last_time'] >= $today_time)
				return array($statistic['num'],0);
			else
			{
				$change_day = ($today_time - $statistic['last_time']) / 86400;
				$change = $change_day == 1 ? 1 : 2 - $change_day;
				if ($clear)
				{
					$new_num = $change_day == 1 ? $statistic['num'] + 1 : 1;
				}
				else
				{
					$new_num = $statistic['num'] + $change;
				}
				
				if ($new_num <= 0)
					$new_num = 1;
				
				StatisticsService::update(array('last_time'=>$today_time,'num'=>$new_num), $uid, $type);
				return array($new_num,$change);
			}
		}
	}
	
	/**
	 * 添加一条统计信息
	 * @param array $data
	 * @return
	 */
	public function insert($data)
	{
		$data['uid'] = (int)$data['uid'];
		$data['num'] = (int)$data['num'];
		$data['last_time'] = (int) $data['lastday'];
		$data['type'] = StatisticsService::getTypeByKey($data['type']);
		if(!$data['last_time'])
			$data['last_time'] = getTodayTime();
		
		if (!$data['uid'] || !$data['type'])
			return false;
		
		FDB::insert('user_statistics',$data);
		return true;
	}
	
	/**
	 * 更新一条记录
	 * @param array $data
	 * @param int $uid 会员编号
	 * @param string $type 类型
	 * @return
	 */
	function update($data,$uid,$type)
	{
		$uid = (int) $uid;
		$type = StatisticsService::getTypeByKey($type);
		if (!$type || !$uid)
			return false;
		
		return FDB::update('user_statistics',$data,'uid = '.$uid.' AND type = '.$type);
	}
	
	/**
	 * 获取所有的类型
	 * @return array
	 */
	public function getTypes()
	{
		//编号7已经使用为今日会员积分
		return array(
			'1' => 'continue_login',
			'2' => 'continue_share',
			'3' => 'continue_goods',
			'4' => 'continue_photo',
			'5' => 'continue_forum',
			'6' => 'continue_ask',
		);
	}
	
	public function getTypeByKey($key)
	{
		$types = StatisticsService::getTypes();
		if(is_numeric($key))
			return isset($types[$key]) ? $key : false;
		else
			return array_search($key,$types);
	}
}
?>