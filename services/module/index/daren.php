<?php
$page = $_FANWE['page'];
if($page == 0)
{
	echo '{"status":0}';
	exit;
}

$size = intval($_FANWE['request']['size']);
if($size == 0 || $size > 50)
	$size = 15;

$limit = ($page * $size).','.$size;
$daren_list = FS('Daren')->getDarens($limit);
$daren_count = count($daren_list);

if($size - $daren_count > 0)
{
	$limit = '0,'.($size - $daren_count);
	$daren_list1 = FS('Daren')->getDarens($limit);
	$daren_list = array_merge($daren_list,$daren_list1);
	$page = 0;
}
$args['no_lazyload'] = 1;
$args['daren_list'] = $daren_list;
$html = tplFetch('inc/index/daren_list',$args);

$page++;

outputJson(array('status'=>1,'html'=>$html,'page'=>$page));
?>