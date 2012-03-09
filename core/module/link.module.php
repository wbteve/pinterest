<?php
class LinkModule
{
	public function index()
	{
		global $_FANWE;
		$cache_args = array(
			'index',
		);

        $cache_file = getTplCache('page/link_index',$cache_args);
        if(!@include($cache_file))
        {
		    include template('page/link_index');
        }

		display($cache_file);
	}
}
?>