<?php
class initMapi
{
	function getCityArray(){
	
		global $_FANWE;
	
		//$region_lv2 = FanweService::instance()->cache->loadCache("region_lv2");		
		$region_lv2 = $_FANWE['cache']['region_lv2'];
		if($region_lv2==false)
		{
			//init_config_data();//检查初始化数据
	
			//$region_lv2 = array();
			$region_lv2 = FDB::fetchAll("select * from ".FDB::table("region")." where level = 2");
			
			FanweService::instance()->cache->saveCache("region_lv2",$region_lv2);
		}
		return $region_lv2;
	}

	function getCatalogArray(){
	
		global $_FANWE;
	
		$categorylist = $_FANWE['cache']['merchant_category'];	
		if($categorylist==false)
		{
			//init_config_data();//检查初始化数据
			$categorylist = array();
			//$region_lv2 = array();
			$list = FDB::fetchAll("select * from ".FDB::table("merchant_category")." where status = 1");
			//print_r($list);exit
			foreach($list as $item){
					
				$categorylist[] = array("id"=>$item['id'],
										"name"=>$item['name'],
										"icon"=> $_FANWE['site_url'].$item['icon']
					
				);
			}
			//print_r($categorylist);exit;
			FanweService::instance()->cache->saveCache("merchant_category",$categorylist);
		}	
		return $categorylist;
	}
	
		
    public function run()
	{
        global $_FANWE;
        //echo $_FANWE['requestData']['act']; exit;
        $root = array();
        $root['return'] = 1;
        $imei = $_FANWE['requestData']['imei'];  //手机串号        
        /*
        $user_name = addslashes($_FANWE['requestData']['user_name']);
        $password = addslashes($_FANWE['requestData']['password']);
        $user_info = FDB::fetchFirst("select user_name,password,uid,is_account from ".FDB::table("user")." where user_name = '".$user_name."' and password = '".$password."' and status = 1 ");
        if(!$user_info)
        $user_info = FDB::fetchFirst("select user_name,password,uid,is_account from ".FDB::table("user")." where status = 1 and imei <> '' and imei = '".$imei."'");
        if(!$user_info)
        {
        	//通过imei自动创建用户
        	$user_info['password'] = md5($imei);
        	$user_info['imei'] = $imei;
        	$user_info['status'] = 1;
        	$user_info['reg_time'] = TIME_UTC;
        	$uid = FDB::insert("user",$user_info,true); 
        	$user_info['user_name'] = "u_".$uid;
        	$user_info['uid'] = $uid;
        	FDB::query("update ".FDB::table("user")." set user_name = '".$user_info['user_name']."' where uid = ".$uid);
        	
        	
        	if($uid > 0)
			{
				FDB::insert('user_count',array('uid' => $uid));	
				$user_profile = array(
					'uid' => $uid,
				);
				FDB::insert('user_profile',$user_profile);
				$user_status = array(
					'uid' => $uid,
					'reg_ip' => $_FANWE['client_ip'],
					'last_ip' => $_FANWE['client_ip'],
					'last_time' => TIME_UTC,
					'last_activity' => TIME_UTC,
				);
				FDB::insert('user_status',$user_status);
			}
        	
        	
        }
        $root['uid'] = intval($user_info['uid']);
        $root['user_name'] = $user_info['user_name'];
        $root['password'] = $user_info['password'];    
        $root['is_account'] = intval($user_info['is_account']);    
        */
        //$root['city_id'] = $_FANWE['city_id'];//C_CITY_ID;//默认城市id
        //$root['catalog_id'] = intval($MConfig['catalog_id']);//默认分类id
        
        //$root['citylist'] = array();// $this->getCityArray();//城市列表
        //$root['cataloglist'] = $this->getCatalogArray();//分类列表
        //$root['quanlist'] = $this->getQuanArray($_FANWE['city_id']);//商圈列表
        $root['program_title'] = $_FANWE['MConfig']['program_title'];//程序标题名称
        $root['kf_phone'] = $_FANWE['MConfig']['kf_phone'];//客服电话
        $root['kf_email'] = $_FANWE['MConfig']['kf_email'];//客服邮箱
        $root['about_info'] = str_replace("/public/upload/images/",$_FANWE['site_url']."public/upload/images/",$_FANWE['MConfig']['about_info']) ;

        
        $root['page_size'] = PAGE_SIZE;//默认分页大小
        
        
        $root['newslist'] = $_FANWE['MConfig']['newslist'];//新闻列表
        //$root['addr_tlist'] = $_FANWE['MConfig']['addr_tlist'];//保存地址标题
        
        //$root['adv_youhui'] = m_adv_youhui();
        
       	m_display($root);
                
        //FanweService::instance()->cache->saveCache('merchant_category', $category);
	}
}
?>