<?php

class ShareutilModule
{
	public function expression()
	{		
		$result = FDB::fetchAll("select `type`,`title`,`emotion`,`filename` from ".FDB::table('expression')." order by type");
		$expression = array();
		foreach($result as $k=>$v)
		{
			$v['filename'] = "./public/expression/".$v['type']."/".$v['filename'];
			$v['emotion'] = str_replace(array('[',']'),array('',''),$v['emotion']);
			$expression[$v['type']][] = $v;
		}
		
		global $_FANWE;
		$cache_file = getTplCache('inc/shareutil/expression');
		if(!@include($cache_file))
			include template('inc/shareutil/expression');				
		display($cache_file);		
	}
	
	public function addpic()
	{
		global $_FANWE;
		$cache_file = getTplCache('inc/shareutil/addpic');
		if(!@include($cache_file))
			include template('inc/shareutil/addpic');				
		display($cache_file);
	}
	
	public function addgoods()
	{
		$share_module = FDB::fetchAll("select `name`,`class`,`icon`,`url` from ".FDB::table('sharegoods_module')." where status = 1 and is_install = 1");
		global $_FANWE;
		$cache_file = getTplCache('inc/shareutil/addgoods');
		if(!@include($cache_file))
			include template('inc/shareutil/addgoods');				
		display($cache_file);
	}
}
?>