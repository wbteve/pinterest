<?php
class newslistMapi
{
	public function run()
	{
		global $_FANWE;	
		
		$root = array();
		$root['return'] = 1;

		$root['newslist'] = $_FANWE['MConfig']['newslist'];	
		
		m_display($root);
	}
}
?>