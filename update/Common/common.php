<?php
function xCopy($source,$destination,$child)
{
	//用法：
	//   xCopy("feiy","feiy2",1):拷贝feiy下的文件到   feiy2,包括子目录
	//   xCopy("feiy","feiy2",0):拷贝feiy下的文件到   feiy2,不包括子目录
	//参数说明：
	//   $source:源目录名
	//   $destination:目的目录名
	//   $child:复制时，是不是包含的子目录
	if(!is_dir($source))
	{
	  echo("Error:the   $source   is   not   a   direction!");
	  return   0;
	}
	if(!is_dir($destination))
		mkdir($destination,0777);


	$handle=dir($source);
	while($entry=$handle->read())
	{
		if(($entry!=".")&&($entry!=".."))
		{
			if(is_dir($source."/".$entry))
			{
				if($child)
					xCopy($source."/".$entry,$destination."/".$entry,$child);
			}
			else
			{
				copy($source."/".$entry,$destination."/".$entry);
			}
		}
	}

	return   1;
}

function showjsmessage($message,$isBack = 0){
	echo "<script type=\"text/javascript\">showmessage(\"".$message."\",".$isBack.");</script>"."\r\n";
	flush();
	ob_flush();
}

//全站通用的清除所有缓存的方法
function clear_cache()
{
	uclearDir(FANWE_ROOT."update/runtime/");
}

function uclearDir($dir)
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
				uclearDir($filename);

			if(is_file($filename))
				@unlink($filename);
		}
	}

	$directory->close();
}

//由数据库取出系统的配置
function fanweC($name)
{
	return C($name);
}
?>