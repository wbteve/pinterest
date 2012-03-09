<?php
// +----------------------------------------------------------------------
// | Fanwe 多语商城建站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

// 模板布局
class AdvLayoutAction extends CommonAction{

	public function add()
	{
		$arr  =   Dir::getList(FANWE_ROOT."/tpl/");
		foreach($arr as $item)
		{
			if(strpos($item,'.') === FALSE)
			{
				$themes[] = $item;
			}
		}
		
		$this->assign("themes",$themes);	
		
		$adv = D("advPosition")->field("id,name")-> findAll();
		$this->assign("adv",$adv);	
			
		parent::add();
	}
	public function edit()
	{
		
		$arr  =   Dir::getList(FANWE_ROOT."/tpl/");
		foreach($arr as $item)
		{
			if(strpos($item,'.') === FALSE)
			{
				$themes[] = $item;
			}
		}
		
		$adv = D("advPosition")->field("id,name")-> findAll();
		$this->assign("adv",$adv);	
		
		$this->assign("themes",$themes);
		parent::edit();
	}
	
	public function getPageList()
	{
		$tmpl = $_REQUEST['tmpl'];
		$arr =  getAllFiles(FANWE_ROOT."/tpl/".$tmpl."/");
		
		foreach($arr as $item)
		{
			if(substr($item,-4)==".htm")
			{
				$item = preg_replace("/.htm/", "", $item);
				$item = explode("/".$tmpl."/",$item);
				$item = $item[1];
				$files[] = $item;
			}
		}
        $xml = simplexml_load_file(FANWE_ROOT."/tpl/".$tmpl."/pages.xml");
        $pages = array();
        
        if($xml)
        {
	        $xml = ((array)($xml));
	        foreach($xml['page'] as $item)
	        {
	        	$item = (array)$item;
	        	if(count($item['file'])==0)
	        	$item['file'] = '';
	        	$pages[] = $item;
	        }
        }
		
        $res['files'] = $files;
        $res['pages'] = $pages;
		echo json_encode($res);
		
	}
	
	public function getLayoutList()
	{
		$tmpl = $_REQUEST['tmpl'];
		$page = $_REQUEST['page'];
		
		$file_content = @file_get_contents(FANWE_ROOT."/tpl/".$tmpl.$page.".htm");
		
		$layout_array = array();
		preg_match_all("/\{advlayout(\s+)name='(.+?)'(.*?)\}/",$file_content,$layout_array);

		foreach($layout_array[2] as $item)
		{
			$layout_ids[] = $item;
		}
		
		echo json_encode($layout_ids);
	}
	
}

?>