<?php
Vendor('common');

$begin = isset($_REQUEST['begin']) ? intval($_REQUEST['begin']) : 0;
$begin = max($begin,0);
$data_num = (int)FDB::resultFirst('SELECT COUNT(share_id) FROM '.FDB::table('share'));

if($data_num > $begin)
{
    $limit = $data_num - $begin;
    if($limit > 100)
        $limit = 100;
	
	$res = FDB::query('SELECT share_id FROM '.FDB::table('share').' LIMIT '.$begin.','.$limit);
    showjsmessage("开始更新数据表 share $begin 到 ".($begin + $limit)." 行");
	while($data = FDB::fetch($res))
	{
		$share_id = $data['share_id'];
		FS('Share')->updateShareCache($share_id);
		showjsmessage("更新分享 $share_id 成功");
		usleep(10);
	}

    showjsmessage("更新数据表 share $begin 到 ".($begin + $limit)." 行  成功");

    if($limit < 1000)
    {
        showjsmessage("更新完成",4);
	    exit;
    }
    else
    {
        showjsmessage(U('Index/updatetable',array('table'=>'share','begin'=>$begin + $limit)),5);
        exit;
    }
}
?>