<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/**  
 * time.func.php
 *
 * 时间处理函数
 *
 * @package function
 * @author awfigq <awfigq@qq.com>
 */
 
 
/**  
 * 设置时区 
 * @param int $timezone 时区
 * @return void
 */ 
function timezoneSet($timezone)
{
	if(function_exists('date_default_timezone_set'))
		@date_default_timezone_set('Etc/GMT'.($timezone > 0 ? '-' : '+').(abs($timezone)));
}

/**  
 * 获取当前毫秒 
 * @return array
 */ 
function fMicrotime()
{
	return array_sum(explode(' ',microtime()));
}

/**
 * 获得当前格林威治时间的时间戳
 *
 * @return  integer
 */
function fGmtTime()
{
	static $time = NULL;
	if($time === NULL)
		$time = time() - date('Z');
    return $time;
}

/**
 * 获得今日零时格林威治时间的时间戳
 *
 * @return  integer
 */
function getTodayTime()
{
	static $today_time = NULL;
	if($today_time === NULL)
		$today_time = mktime(0,0,0,date('m'),date('d'),date('Y')) - date('Z');
    return $today_time;
}

/**
 * 将GMT时间戳格式化为用户自定义时区日期
 *
 * @param  string       $format
 * @param  integer      $time       该参数必须是一个GMT的时间戳
 *
 * @return  string
 */
function fToDate($time = NULL,$format = 'Y-m-d H:i:s')
{
	global $_FANWE;
	
	if ($time === NULL)
	{
		$time = fGmtTime();
	}
	
	if(empty($time))
		return 0;
	
	$time_zone = intval($_FANWE['setting']['time_zone']);
	
	$time += $time_zone * 3600;
	
	$format = str_replace('#', ':', $format);
	
	return date($format, $time);
}

/**
 * 将一个用户自定义时区的日期转为GMT时间戳
 *
 * @access  public
 * @param   string      $str
 *
 * @return  integer
 */
function str2Time($str)
{
	global $_FANWE;
	
	$str = trim($str);
	
	if(empty($str))
		return 0;
	
	$time_zone = intval($_FANWE['setting']['time_zone']);
	
	$time = strtotime($str) - $time_zone * 3600;
	
	return $time;
}

/**
 * 获取指定时间与当前时间的时间间隔
 *
 * @access  public
 * @param   integer      $time
 *
 * @return  string
 */
function getBeforeTimelag($time)
{
	if($time == 0)
		return "";
	
	static $today_time = NULL,
			$before_lang = NULL,
			$beforeday_lang = NULL,
			$today_lang = NULL,
			$yesterday_lang = NULL,
			$hours_lang = NULL,
			$minutes_lang = NULL,
			$months_lang = NULL,
			$date_lang = NULL,
			$sdate = 86400;
	
	if($today_time === NULL)
	{
		$today_time = fGmtTime();
		$before_lang = lang('time','before');
		$beforeday_lang = lang('time','beforeday');
		$today_lang = lang('time','today');
		$yesterday_lang = lang('time','yesterday');
		$hours_lang = lang('time','hours');
		$minutes_lang = lang('time','minutes');
		$months_lang = lang('time','months');
		$date_lang = lang('time','date');
	}
	
	$now_day = str2Time(fToDate($today_time,"Y-m-d")); //今天零点时间 
	$pub_day = str2Time(fToDate($time,"Y-m-d")); //发布期零点时间

	$timelag = $now_day - $pub_day;
	
	$year_time = fToDate($time,'Y');
	$today_year = fToDate($today_time,'Y');
	
	if($year_time < $today_year)
		return fToDate($time,'Y:m:d H:i');
		
	$timelag_str = fToDate($time,' H:i');
	
	$day_time = 0;
	if($timelag / $sdate >= 1)
	{
		$day_time = floor($timelag / $sdate);
		$timelag = $timelag % $sdate;
	}
	
	switch($day_time)
	{
		case '0':
			$timelag_str = $today_lang.$timelag_str;
		break;
		
		case '1':
			$timelag_str = $yesterday_lang.$timelag_str;
		break;
		
		case '2':
			$timelag_str = $beforeday_lang.$timelag_str;
		break;
		
		default:
			$timelag_str = fToDate($time,'m'.$months_lang.'d'.$date_lang.' H:i');
		break;
	}
	return $timelag_str;
}

/**
 * 获取当前时间与指定时间的时间间隔
 *
 * @access  public
 * @param   integer      $time
 * @param   boolean      $is_arr
 *
 * @return  string or array
 */
function getEndTimelag($time,$is_arr = false)
{
	static $today_time = NULL,
			$timelag_lang = NULL,
			$sdate = 86400,
			$shours = 3600,
			$sminutes = 60;
	
	if($today_time === NULL)
	{
		$today_time = fGmtTime();
		$timelag_lang = lang('time','timelag');
	}
	
	$timelag_arr = array('d'=>0,'h'=>0,'m'=>0,'s'=>0);
	$timelag = ($time - $today_time);
	
	if($timelag / $sdate >= 1)
	{
		$timelag_arr["d"] = floor($timelag / $sdate);
		$timelag = $timelag % $sdate;
	}
	
	if($timelag / $shours >= 1)
	{
		$timelag_arr["h"] = floor($timelag / $shours);
		$timelag = $timelag % $shours;
	}
	
	if($timelag / $sminutes >= 1)
	{
		$timelag_arr["m"] = floor($timelag / $sminutes);
		$timelag = $timelag % $sminutes;
	}
	
	if($timelag > 0)
		$timelag_arr["s"] = $timelag;
	
	if($is_arr)
		return $timelag_arr;
	else
		return sprintf($timelag_lang,$timelag_arr["d"],$timelag_arr["h"],$timelag_arr["m"],$timelag_arr["s"]);
}
?>