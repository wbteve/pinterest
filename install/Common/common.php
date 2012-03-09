<?php
function showjsmessage($message,$isBack = 0){
	echo "<script type=\"text/javascript\">showmessage(\"".$message."\",".$isBack.");</script>"."\r\n";
	flush();
	ob_flush();
}

//全站通用的清除所有缓存的方法
function clear_cache()
{
	clearDir1(FANWE_ROOT."public/data/admin/runtime/");
	clearDir1(FANWE_ROOT."install/runtime/");
}

function clearDir1($dir)
{
	if(!file_exists($dir))
		return;

	$directory = dir($dir);

	while($entry = $directory->read())
	{
		if($entry != '.' && $entry != '..')
		{
			$filename = $dir.'/'.$entry;
			if(is_dir($filename))
				clearDir1($filename);

			if(is_file($filename))
				@unlink($filename);
		}
	}

	$directory->close();
}

//由数据库取出系统的配置
function fanweC1($name)
{
	return C($name);
}

//由数据库取出系统的配置
function fanweDBC($name)
{
	static $sysConf = array();

	if ($sysConf[$name] === NULL)
	{
        $db_config = include FANWE_ROOT."public/db_config.php";
        $db = Db::getInstance(array('dbms'=>'mysql','hostname'=>$db_config['DB_HOST'],'username'=>$db_config['DB_USER'],'password'=>$db_config['DB_PWD'],'hostport'=>$db_config['DB_PORT'],'database'=>$db_config['DB_NAME']));

		$value = $db->query('SELECT val FROM '.$db_config['DB_PREFIX']."sys_conf WHERE name = '$name'");
		if(count($value) > 0)
			$sysConf[$name] = $value[0]['val'];
		else
			$sysConf[$name] = false;
	}

	return $sysConf[$name];
}

function random1($length, $numeric = 0)
{
	$seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
	$seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
	$hash = '';
	$max = strlen($seed) - 1;
	for($i = 0; $i < $length; $i++)
	{
		$hash .= $seed{mt_rand(0, $max)};
	}
	return $hash;
}

/**
 * 递归方式的对变量中的特殊字符去除转义
 *
 * @access  public
 * @param   mix     $value
 *
 * @return  mix
 */
function stripslashesDeep($value)
{
    if (empty($value))
    {
        return $value;
    }
    else
    {
        return is_array($value) ? array_map('stripslashesDeep', $value) : stripslashes($value);
    }
}
?>