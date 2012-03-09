<?php
$table = C('DB_PREFIX').'goods';
$table_match = C('DB_PREFIX').'goods_match';

$begin = isset($_REQUEST['begin']) ? intval($_REQUEST['begin']) : 0;
$begin = max($begin,0);

$data_num = $db->query('SELECT COUNT(id) AS num FROM '.$table);
if(count($data_num) > 0)
    $data_num = $data_num[0]['num'];
else
    $data_num = 0;

if($data_num > $begin)
{
    $limit = $data_num - $begin;
    if($limit > 1000)
        $limit = 1000;

    $data_list = $db->query("SELECT id,site_name,name FROM $table LIMIT ".$begin.','.$limit);

    showjsmessage("开始更新数据表 goods $begin 到 ".($begin + $limit)." 行");

    $data_count = count($data_list);
    for($j=0; $j< $data_count; $j++)
    {
        $data = $data_list[$j];
        $db->query('INSERT INTO '.$table_match.' (id,content) VALUES('.$data['id'].',\''.segmentToUnicode(clearSymbol($data['site_name'].$data['name'])).'\')');
    }

    showjsmessage("更新数据表 goods $begin 到 ".($begin + $limit)." 行  成功");

    if($limit < 1000)
    {
        showjsmessage(U('Index/updatetable',array('table'=>'area','begin'=>0)),5);
	    exit;
    }
    else
    {
        showjsmessage(U('Index/updatetable',array('table'=>'goods','begin'=>$begin + $limit)),5);
        exit;
    }
}

/**
 * utf8字符转Unicode字符
 * @param string $char 要转换的单字符
 * @return void
 */
function utf8ToUnicode($char)
{
	switch(strlen($char))
	{
		case 1:
			return ord($char);
		case 2:
			$n = (ord($char[0]) & 0x3f) << 6;
			$n += ord($char[1]) & 0x3f;
			return $n;
		case 3:
			$n = (ord($char[0]) & 0x1f) << 12;
			$n += (ord($char[1]) & 0x3f) << 6;
			$n += ord($char[2]) & 0x3f;
			return $n;
		case 4:
			$n = (ord($char[0]) & 0x0f) << 18;
			$n += (ord($char[1]) & 0x3f) << 12;
			$n += (ord($char[2]) & 0x3f) << 6;
			$n += ord($char[3]) & 0x3f;
			return $n;
	}
}

/**
 * utf8字符串分隔为unicode字符串
 * @param string $str 要转换的字符串
 * @param string $pre
 * @return string
 */
function segmentToUnicode($str,$pre = '')
{
	$arr = array();
	$str_len = mb_strlen($str,'UTF-8');
	for($i = 0;$i < $str_len;$i++)
	{
		$s = mb_substr($str,$i,1,'UTF-8');
		if($s != ' ' && $s != '　')
		{
			$arr[] = $pre.'ux'.utf8ToUnicode($s);
		}
	}

	$arr = array_unique($arr);

	return implode(' ',$arr);
}

/**
 * 清除符号
 * @param string $str 要清除符号的字符串
 * @return string
 */
function clearSymbol($str)
{
	static $symbols = NULL;
	if($symbols === NULL)
	{
		$symbols = file_get_contents(ROOT_PATH.'./public/table/symbol.table');
		$symbols = explode("\r\n",$symbols);
	}

	return str_replace($symbols,"",$str);
}
?>