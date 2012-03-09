<?php
$share_id = intval($_FANWE['request']['share_id']);
if($share_id == 0)
	exit;

if(!checkAuthority('share','edit'))
	exit;

$manage_lock = checkIsManageLock('share',$share_id);
if($manage_lock !== false)
	exit;


$old_share = FS("Share")->getShareById($share_id);
if(empty($old_share))
{
	deleteManageLock('share',$share_id);
	exit;
}

$is_index = (int)$_FANWE['request']['is_index'];
if($is_index == 1)
{
	include_once fimport('class/image');
	$image = new Image();
	if(intval($_FANWE['setting']['max_upload']) > 0)
		$image->max_size = intval($_FANWE['setting']['max_upload']);

	$img = $_FILES['index_img'];
	$index_img ="";
	if(!empty($img))
	{
		$image->init($img,'images');
		if($image->save())
			$index_img = $image->file['target'];
	}

	if(!empty($index_img) && !empty($old_share['index_img']))
		@unlink(FANWE_ROOT.$old_share['index_img']);
}
else
{
	$data['index_img'] = '';
	if(!empty($old_share['index_img']))
		@unlink(FANWE_ROOT.$old_share['index_img']);
}

$data['title'] = $_FANWE['request']['title'];
$data['content'] = $_FANWE['request']['content'];
if($index_img)
	$data['index_img'] = $index_img;
$data['sort'] = (int)$_FANWE['request']['sort'];
$data['status'] = (int)$_FANWE['request']['status'];
$data['is_index'] = $is_index;

if(FDB::update("share",$data,"share_id=".$share_id)){
	$rec_data['title'] = $data['title'];
	$rec_data['content'] = $data['content'];
	switch($old_share['type'])
	{
		case 'ask':
			FDB::update('ask_thread',$rec_data,"share_id = '$share_id'");
			if($old_share['title'] !=  $data['title'])
				FS("Ask")->updateTopicRec($share['rec_id'],$share['title']);
		break;

		case 'ask_post':
			if($old_share['content'] !=  $data['content'])
				FDB::update('ask_post',$rec_data,"share_id = '$share_id'");
		break;

		case 'bar':
			FDB::update('forum_thread',$rec_data,"share_id = '$share_id'");
			if($old_share['title'] !=  $data['title'])
				FS("Topic")->updateTopicRec($data['rec_id'],$data['title']);
			FS("Topic")->updateTopicCache($data['rec_id']);
		break;

		case 'bar_post':
			if($old_share['content'] !=  $data['content'])
				FDB::update('forum_post',$rec_data,"share_id = '$share_id'");
		break;
		
		case 'ershou':
			$rec_data1 = array();
			$rec_data1['name'] = $data['title'];
			$rec_data1['content'] = $data['content'];
			FDB::update('second_goods',$rec_data1,"share_id = '$share_id'");
		break;
	}
	
	$tags = $_FANWE['request']['tags'];
	$tags = explode(" ",$tags);

    FS('Share')->updateShareTags($share_id,array('user'=>implode(' ',$tags)));
    
    //更新喜欢统计
	FDB::query("UPDATE ".FDB::table("share")." set collect_count = (select count(*) from ".FDB::table("user_collect")." where share_id = '".$share_id."' ) where share_id = '".$share_id."'");
	//更新评论统计
	FDB::query("UPDATE ".FDB::table("share")." set comment_count = (select count(*) from ".FDB::table("share_comment")." where share_id = '".$share_id."' ) where share_id = '".$share_id."'");
	
	//更新分类
	$cates_arr = explode(",",$_FANWE['request']['share_cates']);
	foreach($cates_arr as $k=>$v)
	{
		$cates[] = intval($v);
	}

	FDB::query("delete from ".FDB::table("share_category")." where share_id = ".$share_id);
	foreach($cates as $cate_id)
	{
		if(intval($cate_id) > 0)
        {
            FDB::query("insert into ".FDB::table("share_category")."(`share_id`,`cate_id`) values($share_id,$cate_id)");
        }
	}
    FS('Share')->deleteShareCache($share_id);
    createManageLog('share','edit',$share_id,lang('manage','manage_edit_success'));
	deleteManageLock('share',$share_id);
	$msg = lang('manage','manage_edit_success');
	include template('manage/tooltip');
	display();
}

?>
