<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: awfigq <awfigq@qq.com>
// +----------------------------------------------------------------------
/**
 +------------------------------------------------------------------------------
 广告位
 +------------------------------------------------------------------------------
 */
class AdvPositionAction extends CommonAction
{
	public function add()
	{
		$adflashdir = FANWE_ROOT."public/adflash/";
		$adflashlist = new Dir($adflashdir);
		$adflashs = array();
		foreach($adflashlist as $adflash)
		{
			if($adflash['ext'] == "swf")
				$adflashs[] = str_replace(".swf", "", $adflash['filename']);
		}
		$this->assign('adflashs',$adflashs);
		parent::add();
	}
	
	public function insert()
	{
		if(!isset($_POST['is_flash']))
		{
			$_POST['is_flash'] = 0;
			$_POST['flash_style'] = '';
		}
		
		$_POST['width'] = intval($_POST['width']);
		$_POST['height'] = intval($_POST['height']);
		parent::insert();
	}
	
	public function edit()
	{
		$adflashdir = FANWE_ROOT."public/adflash/";
		$adflashlist = new Dir($adflashdir);
		$adflashs = array();
		foreach($adflashlist as $adflash)
		{
			if($adflash['ext'] == "swf")
				$adflashs[] = str_replace(".swf", "", $adflash['filename']);
		}
		$this->assign('adflashs',$adflashs);
		parent::edit();
	}
	
	public function update()
	{
		if(!isset($_POST['is_flash']))
		{
			$_POST['is_flash'] = 0;
			$_POST['flash_style'] = '';
		}
		$_POST['width'] = intval($_POST['width']);
		$_POST['height'] = intval($_POST['height']);
		parent::update();
	}
}

?>