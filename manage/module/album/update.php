<?php
$id = intval($_FANWE['request']['id']);
if($id == 0)
	exit;

if(!checkAuthority('album','edit'))
	exit;

$manage_lock = checkIsManageLock('album',$id);
if($manage_lock !== false)
	exit;
	
$old = FS('Album')->getAlbumById($id);
if(empty($old))
{
	deleteManageLock('album',$id);
	exit;
}

$is_flash = (int)$_FANWE['request']['is_flash'];
$is_best = (int)$_FANWE['request']['is_best'];

if($is_flash == 0)
{
	if(!empty($old['flash_img']))
		@unlink(FANWE_ROOT.$old['flash_img']);
}

if($is_best == 0)
{
	if(!empty($old['best_img']))
		@unlink(FANWE_ROOT.$old['best_img']);
}

$share_id = $old['share_id'];
$update = array(
	'title'=>htmlspecialchars(trim($_FANWE['request']['title'])),
	'content'=>htmlspecialchars(trim($_FANWE['request']['content'])),
	'cid'=>(int)$_FANWE['request']['cid'],
	'show_type'=>(int)$_FANWE['request']['show_type'],
	'is_flash'=> $is_flash,
	'is_best'=> $is_best,
);

if($is_flash == 1)
{
	$img = FS("Image")->save('flash_img','album');
	if($img)
		$update['flash_img'] = $img['url'];
}

if($is_best == 1)
{
	$img = FS("Image")->save('best_img','album');
	if($img)
		$update['best_img'] = $img['url'];
}

$tags = str_replace('***','',$_FANWE['request']['tags']);
$tags = str_replace('　',' ',$tags);
$tags = explode(' ',$tags);
$tags = array_unique($tags);
$update['tags'] = implode(' ',$tags);

FDB::update('album',$update,'id = '.$id);
FS('Share')->updateShare($share_id,$update['title'],$update['content']);
FS("Album")->saveTags($id,$tags);
if($update['cid'] != $old['cid'])
	FDB::query('UPDATE '.FDB::table("album_share").' SET cid = '.$update['cid'].' WHERE album_id = '.$id);

createManageLog('album','edit',$id,lang('manage','manage_edit_success'));
deleteManageLock('album',$id);
$msg = lang('manage','manage_edit_success');
include template('manage/tooltip');
display();
?>