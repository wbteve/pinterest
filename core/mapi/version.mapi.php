<?php
class versionMapi
{
	public function run()
	{
		global $_FANWE;	
		
		$root = array();
		$root['serverVersion'] = $_FANWE['MConfig']['version'];
		$root['filename'] = $_FANWE['site_url'].$_FANWE['MConfig']['filename'];
		if(file_exists(APP_ROOT_PATH.$_FANWE['MConfig']['filename']))
		{
			$root['hasfile'] = 1;
		}
		else 
		{
			$root['hasfile'] = 0;
		}
		$root['filesize'] = filesize(APP_ROOT_PATH.$_FANWE['MConfig']['filename']);

		m_display($root);
	}
}
?>