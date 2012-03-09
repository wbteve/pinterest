<?php
class IndexModule
{
	public function index()
	{
		global $_FANWE;
		clearTempImage();
		include template('page/index_index');
		display();
	}
}
?>