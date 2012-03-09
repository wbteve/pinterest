<?php
class SecondModule
{
	public function index()
	{
		global $_FANWE;
		
		if($_FANWE['setting']['second_status'] == 0)
			fHeader("location: ".FU('index'));
		
		$sid = (int)$_FANWE['request']['sid'];
		$cid = (int)$_FANWE['request']['cid'];
		
		$where = ' WHERE s.status = 1 AND sg.status = 1 ';
		$page_args = array();
		if($sid > 0 && isset($_FANWE['cache']['seconds'][$sid]))
		{
			$where .= ' AND sg.sid = '.$sid;
			$page_args['sid'] = $sid;
		}
		else
			$sid = 0;
			
		if($cid > 0 && isset($_FANWE['cache']['citys']['all'][$cid]))
		{
			$where .= ' AND sg.city_id = '.$cid;
			$page_args['cid'] = $cid;
		}
		else
			$cid = 0;
		
		$seconds = array();
		$citys = array();
		
		$temp_args = $page_args;
		foreach($_FANWE['cache']['seconds'] as $second)
		{
			if($second['sid'] == $sid)
			{
				$_FANWE['nav_title'] = $second['name'].' - '.$_FANWE['nav_title'];
				$second['current'] = true;
			}
			else
				$second['current'] = false;
			
			$temp_args['sid'] = $second['sid'];
			$second['url'] = FU('second/index',$temp_args);
			$seconds[] = $second;
		}
		unset($temp_args['sid']);
		$second_all_url = FU('second/index',$temp_args);
		
		
		$temp_args = $page_args;
		foreach($_FANWE['cache']['citys']['province'] as $province)
		{
			$province = $_FANWE['cache']['citys']['all'][$province];
			if($province['id'] == $cid)
			{
				$_FANWE['nav_title'] = $province['name'].$_FANWE['nav_title'];
				$province['current'] = true;
			}
			else
				$province['current'] = false;
			
			$temp_args['cid'] = $province['id'];
			$province['url'] = FU('second/index',$temp_args);
			$citys[] = $province;
		}
		unset($temp_args['cid']);
		$city_all_url = FU('second/index',$temp_args);
		
		$sql = 'SELECT COUNT(gid) FROM '.FDB::table('second_goods').' AS sg 
			INNER JOIN '.FDB::table('share').' AS s ON s.share_id = sg.share_id '.$where;
		$goods_count = FDB::resultFirst($sql);
		
		$goods_list = array();
		if($goods_count > 0)
		{
			$pager = buildPage('book/'.ACTION_NAME,$page_args,$count,$_FANWE['page'],20);
			$sql = 'SELECT sg.*,s.collect_count,s.comment_count,relay_count,s.cache_data FROM '.FDB::table('second_goods').' AS sg 
				INNER JOIN '.FDB::table('share').' AS s ON s.share_id = sg.share_id '.$where.' ORDER BY sg.gid DESC LIMIT '.$pager['limit'];
			$res = FDB::query($sql);
			while($data = FDB::fetch($res))
			{
				$data['url'] = FU('note/index',array('sid'=>$data['share_id']));
				$data['cache_data'] = fStripslashes(unserialize($data['cache_data']));
				$data['time'] = getBeforeTimelag($data['create_time']);
				FS('Share')->shareImageFormat($data);
				unset($data['cache_data']);
				$goods_list[$data['share_id']] = $data;
			}
		}
		
		include template('page/second/second_index');
		display();
	}


	public function create()
	{
		global $_FANWE;
		if($_FANWE['uid'] == 0)
			fHeader("location: ".FU('user/login'));
			
		if($_FANWE['setting']['second_status'] == 0)
			fHeader("location: ".FU('index'));
		
		include template('page/second/second_create');
		display();
	}

	public function save()
	{
		global $_FANWE;
		
		if($_FANWE['uid'] == 0)
			fHeader("location: ".FU('user/login'));
			
		if($_FANWE['setting']['second_status'] == 0)
			fHeader("location: ".FU('index'));
			
		if(!isset($_FANWE['request']['pics']) || !is_array($_FANWE['request']['pics']) || count($_FANWE['request']['pics']) == 0)
			exit;
		
		$data = array(
			'name'          => trim($_FANWE['request']['title']),
			'content'       => trim($_FANWE['request']['content']),
			'sid'           => (int)$_FANWE['request']['sid'],
			'num'           => (int)$_FANWE['request']['num'],
			'price'         => (float)$_FANWE['request']['price'],
			'transport_fee' => (float)$_FANWE['request']['fare'],
			'valid_time'    => (int)$_FANWE['request']['valid_time'],
		);
		
		$vservice = FS('Validate');
		$validate = array(
			array('name','required',lang('second','name_require')),
			array('name','max_length',lang('second','name_max'),40),
			array('content','required',lang('second','content_require')),
			array('content','max_length',lang('second','content_max'),1000),
			array('sid','min',lang('second','sid_min'),1),
			array('num','range',lang('second','num_range'),1,3),
			array('price','min',lang('second','price_min'),0.01),
			array('transport_fee','min',lang('second','fee_min'),0),
			array('valid_time','range',lang('second','valid_time_range'),2,30),
		);
		
		if(!$vservice->validation($validate,$data))
			exit;
		
		if(!isset($_FANWE['cache']['seconds'][$data['sid']]))
			exit;
		
		if(!checkIpOperation("add_share",SHARE_INTERVAL_TIME))
		{
			showError('提交失败',lang('share','interval_tips'),-1);
		}
		
		$check_result = FS('Share')->checkWord($_FANWE['request']['content'],'content');
		if($check_result['error_code'] == 1)
		{
			showError('提交失败',$check_result['error_msg'],-1);
		}
		
		$check_result = FS('Share')->checkWord($_FANWE['request']['title'],'title');
		if($check_result['error_code'] == 1)
		{
			showError('提交失败',$check_result['error_msg'],-1);
		}
		
		$_FANWE['request']['uid'] = $_FANWE['uid'];
		$_FANWE['request']['type'] = 'ershou';
		$share = FS('Share')->submit($_FANWE['request']);
		
		if($share['status'])
		{
			$data['name'] = htmlspecialchars($_FANWE['request']['title']);
			$data['content'] = htmlspecialchars($_FANWE['request']['content']);
			$data['uid'] = $_FANWE['uid'];
			$data['share_id'] = $share['share_id'];
			$data['city_id'] = $_FANWE['user']['reside_province'];
			$data['valid_time'] = getTodayTime() + 86400 * $data['valid_time'];
			$data['create_time'] = TIME_UTC;
			$data['status'] = 0;
			
			$gid = FDB::insert('second_goods',$data,true);
			
			$sign = md5($gid.$_FANWE['setting']['second_taobao_sign']);
			
			FDB::query('UPDATE '.FDB::table('second_goods').' SET sign = \''.$sign.'\' WHERE gid = '.$gid);
			FDB::query('UPDATE '.FDB::table('share').' SET rec_id = '.$gid.' 
				WHERE share_id = '.$share['share_id']);
			FDB::query("update ".FDB::table("user_count")." set seconds = seconds + 1 where uid = ".$_FANWE['uid']);
			FS('Medal')->runAuto($_FANWE['uid'],'seconds');
			
			$url = "http://communityweb.alipay.com/dispatch.htm?type=exGuarantee&forumId=".$_FANWE['setting']['second_taobao_forumid']."&exId=".$gid."&userIP=".$_FANWE['client_ip']."&userIPSign=".md5($_FANWE['client_ip'].$_FANWE['setting']['second_taobao_sign']);
			fHeader('location: '.$url);
		}
		else
			showError('提交失败','添加数据失败',-1);
	}
}
?>