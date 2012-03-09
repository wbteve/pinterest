<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/**  
 * exchange.service.php
 *
 * 积分兑换服务
 *
 * @package service
 * @author awfigq <awfigq@qq.com>
 */
class ExchangeService
{
	/**
	 * 最新兑换
	*/
	public function getOrderTop($num = 5)
	{
		$sql = 'SELECT o.data_name,o.uid,o.user_name,o.create_time,o.data_num  
				FROM '.FDB::table('order').' AS o 
				INNER JOIN '.FDB::table('exchange_goods').' AS eg ON eg.id = o.rec_id 
				ORDER BY o.id DESC LIMIT 0,'.$num;
		
		$list = array();
		$query = FDB::query($sql);
		while($data = FDB::fetch($query))
		{
			$data['create_time_format']  = fToDate($data['create_time'],'H:i:s');
			$list[] = $data;
		}
		
		return $list;
	}
	
	/**
	 * 积分排行
	*/
	public function getScoreTop($num = 10)
	{
		$sql = 'SELECT uid,user_name,server_code,credits FROM '.FDB::table('user').' 
				WHERE status = 1 ORDER BY credits DESC LIMIT 0,'.$num;
		
		$list = array();
		$query = FDB::query($sql);
		while($data = FDB::fetch($query))
		{
			$list[] = $data;
		}
		
		return $list;
	}
	
	/**
	 * 兑换排行
	*/
	public function getExchangeTop($num = 10)
	{
		$sql = "SELECT o.uid,o.user_name,SUM(o.data_num) AS sum_count ".
			    'FROM '.FDB::table('order').' AS o '.
				"GROUP BY o.uid ORDER BY sum_count DESC LIMIT 0,$num";
		
		$list = array();
		$query = FDB::query($sql);
		while($data = FDB::fetch($query))
		{
			$list[] = $data;
		}
		
		return $list;
	}
}
?>