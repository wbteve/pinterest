<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/**
 * 错误处理类
 *
 * @package class
 * @author awfigq <awfigq@qq.com>
 */
class FanweError
{
	/**  
	 * 系统错误 
	 * @param string $message 错误代码
	 * @param bool $show 是否显示错误
	 * @param bool $save 是否保存错误日志
	 * @param bool $halt 是否停止程序
	 * @return void
	 */ 
	function systemError($message, $show = true, $save = true, $halt = true)
	{
		if(!empty($message))
		{
			$message = lang('error', $message);
			
		}
		else 
		{
			$message = lang('error', 'error unknow');
		}

		list($show_trace, $log_trace) = FanweError::debugBacktrace();
		
		if($save)
		{
			$message_save = '<b>'.$message.'</b><br><b>PHP:</b>'.$log_trace;
			FanweError::writeErrorLog($message_save);
		}

		if($show)
		{
			FanweError::showError('system', "<li>$message</li>", $show_trace, 0);
		}

		if($halt)
		{
			exit();
		}
		else
		{
			return $message;
		}
	}
	
	/**  
	 * 模板错误 
	 * @param string $message 错误代码
	 * @param string $tpl_name 模板名称
	 * @return void
	 */ 
	function templateError($message, $tpl_name)
	{
		$message = lang('error', $message);
		$tpl_name = str_replace(FANWE_ROOT, '', $tpl_name);
		$message = $message.': '.$tpl_name;
		FanweError::systemError($message);
	}
	
	/**  
	 * 回溯追踪记录
	 * @return array
	 */ 
	function debugBacktrace()
	{
		$skipFunc[] = 'fanweError->debugBacktrace';
		$skipFunc[] = 'fanweError->dbError';
		$skipFunc[] = 'fanweError->templateError';
		$skipFunc[] = 'fanweError->systemError';
		$skipFunc[] = 'dbMySql->halt';
		$skipFunc[] = 'dbMySql->query';
		$skipFunc[] = 'FDB::_execute';

		$show = $log = '';
		$debugBacktrace = debug_backtrace();
		krsort($debugBacktrace);
		foreach ($debugBacktrace as $k => $error)
		{
			$file = str_replace(FANWE_ROOT, '', $error['file']);
			$func = isset($error['class']) ? $error['class'] : '';
			$func .= isset($error['type']) ? $error['type'] : '';
			$func .= isset($error['function']) ? $error['function'] : '';
			if(in_array($func, $skipFunc))
			{
				break;
			}
			
			$error[line] = sprintf('%04d', $error['line']);

			$show .= "<li>[Line: $error[line]]".$file."($func)</li>";
			$log .= !empty($log) ? ' -> ' : '';$file.':'.$error['line'];
			$log .= $file.':'.$error['line'];
		}
		
		return array($show, $log);
	}
	
	/**  
	 * 数据库错误 
	 * @param string $message 错误代码
	 * @param string $sql 查询语句
	 * @return void
	 */
	function dbError($message, $sql)
	{
		global $_FANWE;

		list($show_trace, $log_trace) = FanweError::debugBacktrace();

		$title = lang('error', 'db'.$message);
		$title_msg = lang('error', 'db_error_message');
		$title_sql = lang('error', 'db_query_sql');
		$title_backtrace = lang('error', 'backtrace');
		$title_help = lang('error', 'db_help_link');

		$db = &FDB::object();
		$db_errno = $db->errno();
		$db_error = str_replace($db->tablepre,  '', $db->error());
		$sql = htmlspecialchars(str_replace($db->tablepre,  '', $sql));

		$msg = '<li>[Type] '.$title.'</li>';
		$msg .= $db_errno ? '<li>['.$db_errno.'] '.$db_error.'</li>' : '';
		$msg .= $sql ? '<li>[Query] '.$sql.'</li>' : '';

		FanweError::showError('db', $msg, $show_trace, false);
		unset($msg, $show_trace);

		$error_msg = '<b>'.$title.'</b>';
		$error_msg .= "[$db_errno]<br /><b>ERR:</b> $db_error<br />";
		if($sql)
		{
			$error_msg .= '<b>SQL:</b> '.$sql;
		}
		$error_msg .= "<br />";
		$error_msg .= '<b>PHP:</b> '.$log_trace;

		FanweError::writeErrorLog($error_msg);
		exit();

	}
	
	/**  
	 * 显示错误 
	 * @param string $type 错误类型
	 * @param string $error_msg 错误提示
	 * @param string $show_trace
	 * @return void
	 */
	function showError($type, $error_msg, $show_trace = '')
	{
		global $_FANWE;

		ob_end_clean();
		$gzip = $_FANWE['gzip_compress'];
		ob_start($gzip ? 'ob_gzhandler' : NULL);

		$host = $_SERVER['HTTP_HOST'];
		$show_trace = trim($show_trace);
		$title = $type == 'db' ? 'Database' : 'System';
		echo <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title>$host - $title Error</title>
	<meta http-equiv="Content-Type" content="text/html; charset={$_FANWE['config']['output']['charset']}" />
	<meta name="ROBOTS" content="NOINDEX,NOFOLLOW,NOARCHIVE" />
	<style type="text/css">
	<!--
	body { background-color: white; color: black; }
	#container { width: 650px; }
	#message   { width: 650px; color: black; background-color: #FFFFCC; }
	#bodytitle { font: 13pt/15pt verdana, arial, sans-serif; height: 35px; vertical-align: top; }
	.bodytext  { font: 8pt/11pt verdana, arial, sans-serif; }
	.help  { font: 12px verdana, arial, sans-serif; color: red;}
	.red  {color: red;}
	a:link     { font: 8pt/11pt verdana, arial, sans-serif; color: red; }
	a:visited  { font: 8pt/11pt verdana, arial, sans-serif; color: #4e4e4e; }
	-->
	</style>
</head>
<body>
<table cellpadding="1" cellspacing="5" id="container">
<tr>
	<td id="bodytitle" width="100%">FANWE $title Error </td>
</tr>
EOT;

		if($type == 'db') {
			$help_link = "http://help.fanwe.com/?type=mysql&db_errno=".rawurlencode(FDB::errno())."&db_error=".rawurlencode(FDB::error());
			echo <<<EOT
<tr>
	<td class="bodytext">The database has encountered a problem. </td>
</tr>
EOT;
		} else {
			echo <<<EOT
<tr>
	<td class="bodytext">Your request has encountered a problem. </td>
</tr>
EOT;
		}

		echo <<<EOT
<tr><td><hr size="1"/></td></tr>
<tr><td class="bodytext">Error messages: </td></tr>
<tr>
	<td class="bodytext" id="message">
		<br/>
		<ul> $error_msg</ul>
	</td>
</tr>
EOT;

		if(!empty($show_trace)) {
			echo <<<EOT
<tr><td class="bodytext">&nbsp;</td></tr>
<tr><td class="bodytext">Program messages: </td></tr>
<tr>
	<td class="bodytext">
		<ul> $show_trace </ul>
	</td>
</tr>
EOT;
		}

		//$end_msg = lang('error', 'error_end_message', array('host'=>$host));
		echo <<<EOT
<tr>
	<td class="help"><br><br>$end_msg</td>
</tr>
</table>
</body>
</html>
EOT;
		$exit && exit();

	}
	
	/**  
	 * 格式化错误提示 
	 * @param string $message 错误提示
	 * @return string
	 */
	function clear($message)
	{
		return str_replace(array("\t", "\r", "\n"), " ", $message);
	}
	
	/**  
	 * 写入错误日志 
	 * @param string $message 错误提示
	 * @return void
	 */
	function writeErrorLog($message)
	{
		global $_FANWE;
		$message = FanweError::clear($message);
		$time = TIMESTAMP;
		$file =  FANWE_ROOT.'./public/logs/error'.date("Ym").'.log.php';
		$hash = md5($message);

		$user = '<b>User:</b> uid='.$_FANWE['uid'].'; IP='.$_FANWE['client_ip'].'; RIP:'.$_SERVER['REMOTE_ADDR'];
		$uri = 'Request: '.htmlspecialchars(FanweError::clear($_SERVER['REQUEST_URI']));
		$message = "<?PHP exit;?>\t{$time}\t$message\t$hash\t$user $uri\n";
		
		if($fp = @fopen($file, 'rb'))
		{
			$lastlen = 10000;
			$maxtime = 60 * 10;
			$offset = filesize($file) - $lastlen;
			if($offset > 0)
			{
				fseek($fp, $offset);
			}
			if($data = fread($fp, $lastlen))
			{
				$array = explode("\n", $data);
				if(is_array($array)) foreach($array as $key => $val)
				{
					$row = explode("\t", $val);
					if($row[0] != '<?PHP exit;?>') continue;
					if($row[3] == $hash && ($row[1] > $time - $maxtime))
					{
						return;
					}
				}
			}
		}
		error_log($message, 3, $file);
	}

}
?>