<?php
require dirname(__FILE__).'/core/fanwe.php';
$fanwe = &FanweService::instance();
$fanwe->initialize();

$index = intval($_REQUEST['index']);
@set_time_limit(0);
if(function_exists('ini_set'))
    ini_set('max_execution_time',0);

$limit = 500;
$shares = FDB::fetchAll('SELECT share_id,uid
    FROM '.FDB::table('share').'
    ORDER BY share_id ASC LIMIT '.$index.','.$limit);

if(count($shares) == 0)
{
	echo "ok";
	exit;
}

foreach($shares as $share)
{
    $share_id = $share['share_id'];
	FS('Share')->updateShareCache($share_id);
    echo "创建缓存 $share_id 成功<br/>";
	flush();
	ob_flush();
    usleep(100);
}

echo "<script type=\"text/javascript\">var fun = function(){location.href='cache.php?index=".($index + $limit)."&time=".time()."';}; setTimeout(fun,500);</script>"."\r\n";
flush();
ob_flush();
exit;
?>