<?php
if(!isset($_FILES['image']) || empty($_FILES['image']))
	exit;

$result = array();
$pic = $_FILES['image'];
include_once fimport('class/image');
$image = new Image();
if(intval($_FANWE['setting']['max_upload']) > 0)
	$image->max_size = intval($_FANWE['setting']['max_upload']);
$image->init($pic);

if($image->save())
{
	//$type = $_FANWE['request']['photo_type'];
	//if(empty($type) || !in_array($type,array('default', 'dapei', 'look')))
		//$type = 'default';
	
	$result['img'] = $image->file['target'];
	$result['status'] = 1;
	$info = array('path'=>$image->file['local_target'],'type'=>$_FANWE['request']['photo_type'],'server_code'=>'');
	$result['info'] = authcode(serialize($info), 'ENCODE');
	$args = array('result'=>$result);
	$result['html'] = tplFetch("services/share/pic_item",$args);
}
else
{
	$result['status'] = 0;
}

$json = getJson($result);
echo "<textarea>$json</textarea>";
?>