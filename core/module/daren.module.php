<?php
class DarenModule
{
	public function index()
	{			
		global $_FANWE;
		$is_best = true;
		$is_all = false;
		$_FANWE['nav_title'] = lang('common','daren');
		$count = FDB::resultFirst('SELECT COUNT(uid) 
			FROM '.FDB::table('user_daren').'  
			WHERE is_best = 1 AND status = 1');
			
		$pager = buildPage('daren/index',array(),$count,$_FANWE['page'],18);
		
		$col = 3;
		$index = 0;
		$list = array();
		$today_time = getTodayTime();
		$sql = 'SELECT ud.*,u.user_name,u.server_code,uc.fans,uc.goods,uc.shares,
			up.reside_province,up.reside_city,up.introduce 
			FROM '.FDB::table('user_daren').' AS ud 
			INNER JOIN '.FDB::table('user').' AS u ON u.uid = ud.uid 
			INNER JOIN '.FDB::table('user_count').' AS uc ON uc.uid = u.uid 
			INNER JOIN '.FDB::table('user_profile').' AS up ON up.uid = ud.uid 
			WHERE ud.is_best = 1 AND ud.status = 1 ORDER BY ud.day_time DESC,ud.id DESC LIMIT '.$pager['limit'];
		$res = FDB::query($sql);
		while($data = FDB::fetch($res))
		{
			$data['today_best'] = false;
			$data['url'] = FU('u/index',array('uid'=>$data['uid']));
			if($data['day_time'] == $today_time)
				$data['today_best'] = true;
			$province = $_FANWE['cache']['citys']['all'][$data['reside_province']]['name'];
			$city = $_FANWE['cache']['citys']['all'][$data['reside_city']]['name'];
			$data['city'] = $province.'&nbsp;'.$city;
			$list[$index % $col][] = $data;
			$index++;
		}
		
		include template('page/daren');		
		display();
	}
	
	public function all()
	{			
		global $_FANWE;
		$is_best = false;
		$is_all = true;
		$_FANWE['nav_title'] = lang('common','daren');
		$count = FDB::resultFirst('SELECT COUNT(uid) FROM '.FDB::table('user_daren').' WHERE status = 1');
			
		$pager = buildPage('daren/all',array(),$count,$_FANWE['page'],18);
		
		$col = 3;
		$index = 0;
		$list = array();
		$today_time = getTodayTime();
		$sql = 'SELECT ud.*,u.user_name,u.server_code,uc.fans,uc.goods,uc.shares,
			up.reside_province,up.reside_city,up.introduce 
			FROM '.FDB::table('user_daren').' AS ud 
			INNER JOIN '.FDB::table('user').' AS u ON u.uid = ud.uid 
			INNER JOIN '.FDB::table('user_count').' AS uc ON uc.uid = u.uid 
			INNER JOIN '.FDB::table('user_profile').' AS up ON up.uid = ud.uid 
			WHERE ud.status = 1 
			ORDER BY ud.day_time DESC,ud.id DESC LIMIT '.$pager['limit'];
		$res = FDB::query($sql);
		while($data = FDB::fetch($res))
		{
			$data['today_best'] = false;
			$data['url'] = FU('u/index',array('uid'=>$data['uid']));
			$province = $_FANWE['cache']['citys']['all'][$data['reside_province']]['name'];
			$city = $_FANWE['cache']['citys']['all'][$data['reside_city']]['name'];
			$data['city'] = $province.'&nbsp;'.$city;
			$list[$index % $col][] = $data;
			$index++;
		}
		
		include template('page/daren');		
		display();
	}
	
	public function apply()
	{
		global $_FANWE;
		include template('page/daren/daren_apply');		
		display();
	}
	
	public function save()
	{
		global $_FANWE;
		if($_FANWE['uid'] == 0)
			exit;
		
		include_once fimport('class/image');
		$image = new Image();
		if(intval($_FANWE['setting']['max_upload']) > 0)
			$image->max_size = intval($_FANWE['setting']['max_upload']);
		
		$daren = array();
		$daren['uid'] = $_FANWE['uid'];
		$daren['reason'] = $_FANWE['request']['reason'];
		$daren['status'] = 0;
		$daren['create_time'] = TIME_UTC;
		
		//个人街拍照
		$img = $_FILES['img'];
		if(!empty($img))
		{
			$image->init($img,'daren');
			if($image->save())
				$daren['img'] = $image->file['target'];
		}
		
		$index_img = $_FILES['index_img'];
		if(!empty($index_img))
		{
			$image->init($index_img,'daren');
			if($image->save())
				$daren['index_img'] = $image->file['target'];
		}
		
		$id = FDB::insert('user_daren',$daren,true,false,true);
		if($id > 0)
			showSuccess('提交申请成功','你的达人申请已经成功提交，我们会尽快处理你的达人申请！',FU('daren/index'));
		else
			showError('提交申请失败','你的达人申请提交失败，请重新提交达人申请',-1);
	}
}
?>