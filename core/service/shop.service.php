<?php
/**
 * 店铺服务类
 * @author awfigq
 *
 */
class ShopService
{
	public function getShopExistsByUrl($url)
	{
		$sql = 'SELECT COUNT(shop_id) FROM '.FDB::table('shop')." WHERE shop_url = '$url'";
		return (int)FDB::resultFirst($sql) > 0;
	}

	public function updateShopStatistic($shop_ids)
	{
		if(is_array($shop_ids))
		{
			$shop_ids = array_unique($shop_ids);	
			foreach($shop_ids as $shop_id)
			{
				$shop_id = (int)$shop_id;
				if($shop_id > 0)
				{
					$data = array();
					$data['recommend_count'] = ShopService::getShopUserCount($shop_id);
					$temp = array();
					$temp['tags'] = ShopService::getShopTags($shop_id);
					$temp['goods'] = ShopService::getShopGoods($shop_id);
					$data['data'] = addslashes(serialize($temp));
					FDB::update('shop',$data,'shop_id = '.$shop_id);
				}
			}
		}
	}
	
	public function formatShopList($shop_ids)
	{
		$shop_ids = implode(',',$shop_ids);
		if(empty($shop_ids))
			return array();
		
		$sql = 'SELECT goods_id FROM '.FDB::table('share_goods').' 
			WHERE shop_id = '.$shop_id.' ORDER BY goods_id DESC LIMIT 0,'.$num;
		$list = array();
		$res = FDB::query($sql);
		while($data = FDB::fetch($res))
		{
			$list[] = $data['goods_id'];
		}
		return $list;
	}
	
	public function getShopGoods($shop_id,$num = 10)
	{
		$sql = 'SELECT goods_id FROM '.FDB::table('share_goods').' 
			WHERE shop_id = '.$shop_id.' ORDER BY goods_id DESC LIMIT 0,'.$num;
		$list = array();
		$res = FDB::query($sql);
		while($data = FDB::fetch($res))
		{
			$list[] = $data['goods_id'];
		}
		return $list;
	}
	
	public function getShopUserCount($shop_id)
	{
		$sql = 'SELECT COUNT(DISTINCT uid) AS user_count FROM '.FDB::table('share_goods').' WHERE shop_id = '.$shop_id;
		return FDB::resultFirst($sql);
	}
	
	public function getShopTags($shop_id,$num = 20)
	{
		$sql = 'SELECT st.tag_name,COUNT(st.tag_name) AS tag_count FROM '.FDB::table('share_goods').' AS sg 
			INNER JOIN '.FDB::table('share_tags').' AS st ON st.share_id = sg.share_id  
			WHERE sg.shop_id = '.$shop_id.' GROUP BY st.tag_name ORDER BY tag_count DESC LIMIT 0,'.$num;
		$list = array();
		$res = FDB::query($sql);
		while($data = FDB::fetch($res))
		{
			$list[] = $data['tag_name'];
		}
		return $list;
	}
	
	public function getUserOtherShopAndTags($shop_id,$shop_num = 4,$tag_num = 12)
	{
		$sql = 'SELECT DISTINCT uid FROM '.FDB::table('share_goods').' 
			WHERE shop_id = '.$shop_id.' ORDER BY goods_id DESC LIMIT 0,5000';
		$uids = array();
		$res = FDB::query($sql);
		while($data = FDB::fetch($res))
		{
			$uids[] = $data['uid'];
		}
		
		$uids = implode(',',$uids);
		if(!$uids)
			return ;
		$shop_count = FDB::resultFirst('SELECT COUNT(DISTINCT shop_id) FROM '.FDB::table('share_goods').' 
			WHERE uid IN ('.$uids.') AND shop_id <> '.$shop_id);
		
		$list['shops'] = array();
		$list['tags'] = array();
			
		if($shop_count > 0)
		{
			$begin = 0;
			if($shop_count > $shop_num)
				$begin = mt_rand(0,$shop_count - $shop_num);
			
			$sql = 'SELECT DISTINCT shop_id FROM '.FDB::table('share_goods').' 
				WHERE uid IN ('.$uids.') AND shop_id <> '.$shop_id.' ORDER BY shop_id DESC LIMIT '.$begin.','.$shop_num;
			$shop_ids = array();
			$res = FDB::query($sql);
			while($data = FDB::fetch($res))
			{
				$shop_ids[] = $data['shop_id'];
			}
			
			$shop_ids = implode(',',$shop_ids);
			$sql = 'SELECT * FROM '.FDB::table('shop').' WHERE shop_id IN ('.$shop_ids.')';
			$res = FDB::query($sql);
			while($data = FDB::fetch($res))
			{
				$cache_data = fStripslashes(unserialize($data['data']));
				$data['tags'] = array();
				if($cache_data)
				{
					if($cache_data['tags'] && is_array($cache_data['tags']))
						$data['tags'] = $cache_data['tags'];
				}
				unset($data['data']);
				$list['tags'] = array_merge($list['tags'],$data['tags']);
				$data['url'] = FU('shop/show',array('id'=>$data['shop_id']));
				$list['shops'][] = $data;
			}
			
			$list['tags'] = array_slice(array_unique($list['tags']),0,$tag_num);
		}
		
		return $list;
	}
	
	/**
	 * 获取会员分享的店铺信息
	 * @param int $uid 会员编号
	 * @param int $num
	 * @return array
	 */
	public function getUserShareShops($uid,$num = 10)
	{
		$list = array();
		$shop_count = FDB::resultFirst('SELECT COUNT(goods_id) FROM '.FDB::table('share_goods').' WHERE shop_id > 0 AND uid = '.$uid);
		if($shop_count > 0)
		{
			$total = FDB::resultFirst('SELECT COUNT(DISTINCT shop_id) FROM '.FDB::table('share_goods').' WHERE shop_id > 0 AND uid = '.$uid);
			$sql = 'SELECT sg.shop_id,COUNT(sg.shop_id) AS shop_count,s.shop_name,s.shop_url,s.shop_logo,s.taoke_url  
					FROM '.FDB::table('share_goods').' AS sg 
					INNER JOIN '.FDB::table('shop').' AS s ON s.shop_id = sg.shop_id 
					WHERE sg.uid = '.$uid.' 
					GROUP BY sg.shop_id ORDER BY shop_count DESC LIMIT 0,'.$num;
			$res = FDB::query($sql);
			while($shop = FDB::fetch($res))
			{
				if(empty($shop['taoke_url']))
					$shop['to_url'] = FU('tgo',array('url'=>$shop['shop_url']));
				else
					$shop['to_url'] = FU('tgo',array('url'=>$shop['taoke_url']));
				
				$shop['percent'] = round($shop['shop_count'] / $shop_count * 100,1).'%';
				$list[] = $shop;
			}
		}
		return array('total'=>$total,'list'=>$list);
	}
	
	/**
	 * 获取会员分享的店铺百分比信息HTML
	 * @param int $uid 会员编号
	 * @param int $num 显示数量
	 * @return string
	 */
	public function getUserShareShopHtml($uid,$num = 10)
	{
		$user = FS('User')->getUserById($uid);
		$user_lang = $_FANWE['uid'] == $uid ? lang('user','me') : lang('user','ta_'.$user['gender']);
		$shops = ShopService::getUserShareShops($uid,$num);
		
		$args = array(
			'shops'=>&$shops,
			'user'=>&$user,
			'user_lang'=>&$user_lang,
		);
		
		return tplFetch("inc/shop/user_shop_percent",$args);
	}
}
?>