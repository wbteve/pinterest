<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/**  
 * cron.class.php
 *
 * 计划任务处理类
 *
 * @package class
 * @author awfigq <awfigq@qq.com>
 */
class Cron
{
	public function run()
	{
		$crons = array();
		$res = FDB::query("SELECT * FROM ".FDB::table('cron')." WHERE run_time <= '".TIME_UTC."' ORDER BY run_time DESC");
		while($data = FDB::fetch($res))
		{
			$crons[$data['type']][] = $data;
		}
		
		if(count($crons) > 0)
		{
			$query = FDB::query("DELETE FROM ".FDB::table('cron')." WHERE run_time <= '".TIME_UTC."'");
			if($query !== FALSE && FDB::affectedRows() > 0)
			{
				@set_time_limit(1800);
				if(function_exists('ini_set'))
					ini_set('max_execution_time',1800);
					
				foreach($crons as $ctype => $cron_list)
				{
					FS($ctype)->runCron($cron_list);
				}
			}
		}
	}
}
?>