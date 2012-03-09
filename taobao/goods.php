<?php
$id = (int)$_REQUEST['id'];
if($id == 0)
	exit;
else
	define('GOODS_ID',$id);

require "init.php";
$goods = FDB::fetchFirst('SELECT sg.*,s.cache_data FROM '.FDB::table('second_goods').' AS sg 
	INNER JOIN '.FDB::table('share').' AS s ON s.share_id =  sg.share_id 
	WHERE sg.gid = '.GOODS_ID);
if($goods)
{
	header('Content-type: text/xml; charset=utf-8');
	$goods['cache_data'] = fStripslashes(unserialize($goods['cache_data']));
	FS('Share')->shareImageFormat($goods);
	unset($goods['cache_data']);

	$xml="<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n";
	$xml.="<goods>\r\n";
	$xml.="<ID>".GOODS_ID."</ID>\r\n";
	$xml.="<title><![CDATA[$goods[name]]]></title>\r\n";
	$xml.="<description><![CDATA[$goods[content]]]></description>\r\n";
	$xml.="<price>$goods[price]</price>\r\n";
	$xml.="<totalNumber>$goods[num]</totalNumber>\r\n";
	$valid_time = fToDate($goods['valid_time'],'Ymd').'000000';
	$xml.="<validTime>$valid_time</validTime>\r\n";
	$xml.="<transportFee>$goods[transport_fee]</transportFee>\r\n";
	$xml.="<picPath>\r\n";
	foreach($goods['imgs'] as $img)
	{
		$xml.="<path>".str_replace('./',$_FANWE['site_url'],$img['img'])."</path>\r\n";
	}
	$xml.="</picPath>\r\n";
	$xml.="<sign>$goods[sign]</sign>\r\n";
	$xml.="</goods>";
	echo $xml;
}
?>
