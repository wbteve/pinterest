<?php
/**
 * 由数据库取出系统的配置
 *
 * @access  public
 * @param   mix     $name
 *
 * @return  mix
 */
function fanweC($name)
{
	static $sys_conf = NULL;

	if($name == 'SITE_URL')
		return "http://".$_SERVER['HTTP_HOST'];
	else
	{
		if ($sys_conf === NULL)
		{
			$sys_conf = D("SysConf")->where("status=1")->getField("name,val");
		}

		return $sys_conf[$name];
	}
}

/**
 * 递归方式的对变量中的特殊字符进行转义
 *
 * @access  public
 * @param   mix     $value
 *
 * @return  mix
 */
function addslashesDeep($value)
{
    if (empty($value))
    {
        return $value;
    }
    else
    {
        return is_array($value) ? array_map('addslashesDeep', $value) : addslashes($value);
    }
}

/**
 * 将对象成员变量或者数组的特殊字符进行转义
 *
 * @access   public
 * @param    mix        $obj      对象或者数组
 * @author   Xuan Yan
 *
 * @return   mix                  对象或者数组
 */
function addslashesDeepObj($obj)
{
    if (is_object($obj) == true)
    {
        foreach ($obj AS $key => $val)
        {
            $obj->$key = addslashesDeep($val);
        }
    }
    else
    {
        $obj = addslashesDeep($obj);
    }

    return $obj;
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

/**
 *  将一个字串中含有全角的数字字符、字母、空格或'%+-()'字符转换为相应半角字符
 *
 * @access  public
 * @param   string       $str         待转换字串
 *
 * @return  string       $str         处理后字串
 */
function makeSemiangle($str)
{
    $arr = array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',
                 '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
                 'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',
                 'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',
                 'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',
                 'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',
                 'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',
                 'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',
                 'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',
                 'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',
                 'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',
                 'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
                 'ｙ' => 'y', 'ｚ' => 'z',
                 '（' => '(', '）' => ')', '〔' => '[', '〕' => ']', '【' => '[',
                 '】' => ']', '〖' => '[', '〗' => ']', '“' => '[', '”' => ']',
                 '‘' => '[', '’' => ']', '｛' => '{', '｝' => '}', '《' => '<',
                 '》' => '>',
                 '％' => '%', '＋' => '+', '—' => '-', '－' => '-', '～' => '-',
                 '：' => ':', '。' => '.', '、' => ',', '，' => '.', '、' => '.',
                 '；' => ',', '？' => '?', '！' => '!', '…' => '-', '‖' => '|',
                 '”' => '"', '’' => '`', '‘' => '`', '｜' => '|', '〃' => '"',
                 '　' => ' ');

    return strtr($str, $arr);
}

// 获取客户端IP地址
function getClientIp()
{
   if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
	   $ip = getenv("HTTP_CLIENT_IP");
   else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
	   $ip = getenv("HTTP_X_FORWARDED_FOR");
   else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
	   $ip = getenv("REMOTE_ADDR");
   else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
	   $ip = $_SERVER['REMOTE_ADDR'];
   else
	   $ip = "unknown";
   return($ip);
}

/**
 +----------------------------------------------------------
 * 字符串截取，支持中文和其他编码
 +----------------------------------------------------------
 * @static
 * @access public
 +----------------------------------------------------------
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true)
{
    if(function_exists("mb_substr"))
	{
		if ($suffix && strlen($str)>$length)
			return mb_substr($str, $start, $length, $charset)."...";
        else
			return mb_substr($str, $start, $length, $charset);
    }
    elseif(function_exists('iconv_substr'))
	{
		if ($suffix && strlen($str)>$length)
			return iconv_substr($str,$start,$length,$charset)."...";
        else
			return iconv_substr($str,$start,$length,$charset);
    }

    $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("",array_slice($match[0], $start, $length));
    if($suffix)
		return $slice."…";
    return $slice;
}


/**
 * 获得当前格林威治时间的时间戳
 *
 * @return  integer
 */
function gmtTime()
{
	return (time() - date('Z'));
}

function toDate($time,$format='Y-m-d H:i:s')
{
	if( empty($time))
		return '';

	$format = str_replace('#',':',$format);
	$time_zone = intval(fanweC('TIME_ZONE'));
	$time = $time + $time_zone * 3600;
	return date($format,$time);
}

function strZTime($str)
{
	$str = trim($str);

	if(empty($str))
		return 0;

	$time_zone = intval(fanweC('TIME_ZONE'));
	$time = strtotime($str) - $time_zone * 3600;

	return $time;
}

function getStatusImg($status)
{
	$status = intval($status);
	return '<img status="'.$status.'" src="'.APP_TMPL_PATH.'Static/Images/status-'.$status.'.gif" />';
}

function clearCache()
{
	Dir::delDir(FANWE_ROOT.'./public/data/admin/runtime');
    @mkdir(FANWE_ROOT.'./public/data/admin/runtime', 0777);
    @chmod(FANWE_ROOT.'./public/data/admin/runtime', 0777);
}

function request($url, $post = '', $timeout = 15)
{
	$context = array();
	if(is_array($post))
		$post = requestData($post);

	$context['http'] = array
	(
		'timeout' => $timeout,
		'method'  => 'POST',
		'header'=>"Content-Type: application/x-www-form-urlencoded\r\n".
					"Content-Length: ".strlen($post)."\r\n".
					"Connection: Close\r\n".
					"Cache-Control: no-cache\r\n",
		'content' => $post,
	);

	return file_get_contents($url, false, stream_context_create($context));
}

function requestData($arg='')
{
	$s = $sep = '';
	foreach($arg as $k => $v)
	{
		$k = urlencode($k);
		if(is_array($v))
		{
			$s2 = $sep2 = '';
			foreach($v as $k2 => $v2)
			{
				$k2 = urlencode($k2);
				$s2 .= "$sep2{$k}[$k2]=".urlencode(stripslashesDeep($v2));
				$sep2 = '&';
			}
			$s .= $sep.$s2;
		}
		else
		{
			$s .= "$sep$k=".urlencode(stripslashesDeep($v));
		}
		$sep = '&';
	}

	return $s;
}

if(!function_exists("mysqlLikeQuote"))
{
	/**
	 * 对 MYSQL LIKE 的内容进行转义
	 *
	 * @access      public
	 * @param       string      string  内容
	 * @return      string
	 */
	function mysqlLikeQuote($str)
	{
		return strtr($str, array("\\\\" => "\\\\\\\\", '_' => '\_', '%' => '\%', "\'" => "\\\\\'"));
	}
}

function createIN($item_list, $field_name = '')
{
	if (empty($item_list))
	{
		return $field_name . " IN ('') ";
	}
	else
	{
		if (! is_array($item_list))
		{
			$item_list = explode(',', $item_list);
		}
		$item_list = array_unique($item_list);
		$item_list_tmp = '';
		foreach ($item_list as $item)
		{
			if ($item !== '')
			{
				$item_list_tmp .= $item_list_tmp ? ",'$item'" : "'$item'";
			}
		}
		if (empty($item_list_tmp))
		{
			return $field_name . " IN ('') ";
		}
		else
		{
			return $field_name . ' IN (' . $item_list_tmp . ') ';
		}
	}
}

function getRelateShare($share_id)
{
	$share_data = M("Share")->getByShareId($share_id);
	if($share_data)
		return "<a href='".u("Share/edit",array("share_id"=>$share_id))."' target='_blank'>".l("RELATE_SHARE")."</a>";
	else
		return "<span style='text-decoration:line-through;'>".l("SHARE_DELETE")."</span>";
}

function getLang($key,$file)
{
	if(!empty($file))
		L(include LANG_PATH . FANWE_LANG_SET . '/'.$file.'.php');

	return L($key);
}

function echoFlush($str)
{
	echo str_repeat(' ',4096);
	echo $str;
}

/**
 * utf8字符转Unicode字符
 * @param string $char 要转换的单字符
 * @return void
 */
function utf8ToUnicodeA($char)
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
function segmentToUnicodeA($str,$pre = '')
{
	$arr = array();
	$str_len = mb_strlen($str,'UTF-8');
	for($i = 0;$i < $str_len;$i++)
	{
		$s = mb_substr($str,$i,1,'UTF-8');
		if($s != ' ' && $s != '　')
		{
			$arr[] = $pre.'ux'.utf8ToUnicodeA($s);
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
function clearSymbolA($str)
{
	static $symbols = NULL;
	if($symbols === NULL)
	{
		$symbols = file_get_contents(FANWE_ROOT.'public/table/symbol.table');
		$symbols = explode("\r\n",$symbols);
	}

	return str_replace($symbols,"",$str);
}

function getRefUrl()
{
	$ref_url = $_SERVER['HTTP_REFERER'];
	$url = $_SERVER['REQUEST_URI'];
	echo $ref_url.'<br/>';
	echo $url.'<br/>';
}

function getAllFiles ($path)
{
	$list = array();
	foreach (glob($path . '/*') as $item)
	{
		if (is_dir($item))
		{
			$list = array_merge($list, getAllFiles($item));
		}
		else
		{
			//if(eregi(".php",$item)){}//这里可以增加判断文件名或其他。changed by:edlongren
			$list[] = $item;
		}
	}
	return $list;
}
?>