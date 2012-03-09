<?php
class youhuilistMapi
{
	public function run()
	{
		global $_FANWE;	
		
		$root = array();
		$root['return'] = 1;

		
		$city_id = intval($_FANWE['requestData']['city_id']);
		$quan_id = intval($_FANWE['requestData']['quan_id']);
		$cate_id = intval($_FANWE['requestData']['cate_id']);
		
		$page = intval($_FANWE['requestData']['page']); //分页
		
		$page=$page==0?1:$page;
		
		$page_size = PAGE_SIZE;
		$limit = (($page-1)*$page_size).",".$page_size;
				
		$sql_count = "select count(*) from ".FDB::table("merchant_youhui");
		
		$sql = "select id,merchant_id,title,content,merchant_logo,create_time,merchant_xpoint,merchant_ypoint,merchant_api_address,image_1 from ".FDB::table("merchant_youhui");
		
		$now = TIME_UTC;
		$where = "1 = 1 and status = 1 and begin_time<".$now." and (end_time = 0 or end_time > ".$now.")";
		
		if ($city_id > 0)
			$where .= " and merchant_city_id = $city_id";

		if ($quan_id > 0)
			$where .= " and merchant_quan_id = $quan_id";
		
		if ($cate_id > 0)
			$where .= " and merchant_category_id = $cate_id";
		
		
		$sql_count.=" where ".$where;
		$sql.=" where ".$where;
		$sql.=" limit ".$limit;
		
		//echo $sql; exit;
				
		$total = FDB::resultFirst($sql_count);
		$page_total = ceil($total/$page_size);

		
		$list = FDB::fetchAll($sql);
		$youhui_list = array();
		foreach($list as $item){
			$youhui_list[] = m_youhuiItem($item);
		}
		$root['item'] = $youhui_list;
		$root['page'] = array("page"=>$page,"page_total"=>$page_total);
		
		m_display($root);
	}
}
?>