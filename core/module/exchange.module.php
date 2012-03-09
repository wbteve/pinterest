<?php
class ExchangeModule
{
	public function index()
	{
		global $_FANWE;
		$where = ' WHERE status = 1 AND (begin_time <= '.TIME_UTC.' OR begin_time = 0) AND (end_time >= '.TIME_UTC.' OR end_time = 0)';
		$order = ' ORDER BY sort ASC,end_time ASC,id DESC';
		
		$best_list = array();
		$best_ids = array();
		$sql = 'SELECT * FROM '.FDB::table('exchange_goods').$where.' AND is_best = 1 '.$order.' LIMIT 0,4';
		$res = FDB::query($sql);
		while($data = FDB::fetch($res))
		{
			$data['num'] = $data['stock'] - $data['buy_count'];
			$data['url'] = FU('exchange/show',array('id'=>$data['id']));
			$best_list[] = $data;
			$best_ids[] = $data['id'];
		}
		
		if(count($best_ids) > 0)
		{
			$best_ids = implode(',',$best_ids);
			$where.= ' AND id NOT IN ('.$best_ids.')';
		}
		
		$sql = 'SELECT COUNT(id) FROM '.FDB::table('exchange_goods').$where;
		$goods_count = FDB::resultFirst($sql);
		
		$page_size = 10;
		$pager = buildPage('exchange/index',array(),$goods_count,$_FANWE['page'],$page_size);
		
		$sql = 'SELECT * FROM '.FDB::table('exchange_goods').$where.$order.' LIMIT '.$pager['limit'];
		$goods_list = array();
		$res = FDB::query($sql);
		while($data = FDB::fetch($res))
		{
			$data['num'] = $data['stock'] - $data['buy_count'];
			$data['url'] = FU('exchange/show',array('id'=>$data['id']));
			$goods_list[] = $data;
		}
		
		$order_list = FS("Exchange")->getOrderTop();
		$score_list = FS("Exchange")->getScoreTop();
		$exchange_list = FS("Exchange")->getExchangeTop();
		
		if($_FANWE['uid'] > 0)
			$consignee = FDB::fetchFirst('SELECT * FROM '.FDB::table('user_consignee').' WHERE uid = '.$_FANWE['uid']);
		
		include template('page/exchange/exchange_index');
		display();
	}
	
	public function rule()
	{
		global $_FANWE;
		$title = sprintf(lang('common','user_sore_rule'),$_FANWE['setting']['site_name']);
		$_FANWE['nav_title'] = $title;
		
		$cache_file = getTplCache('page/exchange/exchange_rule');
		if(!@include($cache_file))
		{
			include template('page/exchange/exchange_rule');
		}
		display($cache_file);
	}
}
?>