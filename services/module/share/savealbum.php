<?php
$result = array();
$result['status'] = 0;

$data = array(
	'title' => trim($_FANWE['request']['title']),
	'cid' => (int)$_FANWE['request']['cid'],
);

if($data['title'] == '输入新专辑名')
	exit;

$vservice = FS('Validate');
$validate = array(
	array('title','required',lang('album','name_require')),
	array('title','max_length',lang('album','name_max'),60),
	array('cid','min',lang('album','cid_min'),1),
);

if(!$vservice->validation($validate,$data))
{
	$result['msg'] = $vservice->getError();
	outputJson($result);
}

FanweService::instance()->cache->loadCache('albums');
if(!isset($_FANWE['cache']['albums']['category'][$data['cid']]))
	exit;


$check_result = FS('Share')->checkWord($_FANWE['request']['title'],'title');
if($check_result['error_code'] == 1)
{
	$result['msg'] = $check_result['error_msg'];
	outputJson($result);
}

$_FANWE['request']['uid'] = $_FANWE['uid'];
$_FANWE['request']['type'] = 'album';
$_FANWE['request']['content'] = $_FANWE['request']['title'];
$share = FS('Share')->submit($_FANWE['request'],false);

if($share['status'])
{
	$data['title'] = htmlspecialchars($_FANWE['request']['title']);
	$data['uid'] = $_FANWE['uid'];
	$data['share_id'] = $share['share_id'];
	$data['create_day'] = getTodayTime();
	$data['create_time'] = TIME_UTC;
	$data['show_type'] = 2;
	
	$aid = FDB::insert('album',$data,true);
	
	FDB::query('UPDATE '.FDB::table('share').' SET rec_id = '.$aid.' 
		WHERE share_id = '.$share['share_id']);
	FDB::query("update ".FDB::table("user_count")." set albums = albums + 1 where uid = ".$_FANWE['uid']);
	$result['aid'] = $aid;
	$result['title'] = $data['title'];
	$result['status'] = 1;
}
else
	$result['msg'] = '添加数据失败';

outputJson($result);
?>