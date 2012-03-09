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
 * 缓存管理
 +------------------------------------------------------------------------------
 */
class CacheAction extends CommonAction
{
	public function system()
	{
		$this->display();
	}
	
	public function custom()
	{
		$root_dir = FANWE_ROOT.'public/data/caches/custom/';
		$dir = trim($_REQUEST['dir']);
		$prev = '';
		
		if(empty($dir) || !file_exists($dir))
			$dir = $root_dir;
		else
		{
			$paths = pathinfo($dir);
			$prev = $paths['dirname'].'/';
			$dir = $prev.$paths['basename'].'/';
			if($root_dir == $dir || strpos($dir, $root_dir) === false)
			{
				$dir = $root_dir;
				$prev = '';
			}
		}
		
		$file_list = $this->getFileList($dir);
		$this->assign("dir",$dir);
		$this->assign("prev",$prev);
		$this->assign("file_list",$file_list);
		$this->display();
	}
	
	public function clear()
	{
		$type = trim($_REQUEST['type']);
		switch($type)
		{
			case 'system':
				$is_admin = isset($_REQUEST['is_admin']) ? intval($_REQUEST['is_admin']) : 0;
				$is_data = isset($_REQUEST['is_data']) ? intval($_REQUEST['is_data']) : 0;
				$is_tpl = isset($_REQUEST['is_tpl']) ? intval($_REQUEST['is_tpl']) : 0;
				$bln = false;
				unset($_SESSION['cache_system_clear']);
				if($is_admin == 1)
				{
					$bln = true;
					$_SESSION['cache_system_clear']['is_admin'] = 1;
				}
				
				if($is_data == 1)
				{
					$bln = true;
					$_SESSION['cache_system_clear']['is_data'] = 1;
				}
				
				if($is_tpl == 1)
				{
					$bln = true;
					$_SESSION['cache_system_clear']['is_tpl'] = 1;
				}
				
				if(!$bln)
					$this->redirect('Cache/system');
				else
					$this->redirect('Cache/systemClear');
			break;
			
			case 'custom':
				$file = isset($_REQUEST['file']) ? trim($_REQUEST['file']) : '';
				unset($_SESSION['cache_custom_clear']);
				if(empty($file) || !file_exists($file))
					$this->redirect('Cache/custom');
				else
				{
					if(!is_dir($file))
					{
						@unlink($file);	
						$this->redirect('Cache/custom');
					}
					else
					{
						$_SESSION['cache_custom_clear'] = array('root'=>$file,'current'=>$file);
						$this->redirect('Cache/customClear');
					}
				}
			break;
			
			default:
				$this->redirect('Cache/system');
			break;
		}
	}
	
	public function systemClear()
	{
		$system_clear = $_SESSION['cache_system_clear'];
		if(empty($system_clear))
			$this->redirect('Cache/system');
		else
			$_SESSION['cache_system_clear'] = $system_clear;
		
		@set_time_limit(3600);
		if(function_exists('ini_set'))
		{
			ini_set('max_execution_time',3600);
			ini_set("memory_limit","256M");
		}
		
		$this->display();
		
		ob_start();
		ob_end_flush(); 
		ob_implicit_flush(1);
		
		if(isset($system_clear['is_admin']))
		{
			clearCache();
			echoFlush('<script type="text/javascript">showmessage(\''.L('SYSTEM_TIPS6').'\',1);</script>');
		}
		
		Vendor('common');
		if(isset($system_clear['is_data']))
		{
			usleep(100);
			include_once fimport('class/cache');
			Cache::getInstance()->updateCache();
			echoFlush('<script type="text/javascript">showmessage(\''.L('SYSTEM_TIPS7').'\',1);</script>');
		}
		
		if(isset($system_clear['is_tpl']))
		{
			usleep(50);
			clearDir(FANWE_ROOT.'./public/data/tpl/caches/static');
			usleep(50);
			clearDir(FANWE_ROOT.'./public/data/tpl/caches/dynamic');
			usleep(50);
    		clearDir(FANWE_ROOT.'./public/data/tpl/compiled');
    		usleep(50);
    		clearDir(FANWE_ROOT.'./public/data/tpl/caches/adv_position');  			
			@file_put_contents(FANWE_ROOT.'./public/data/tpl/caches/page/is_clear.lock',TIMESTAMP);
			echoFlush('<script type="text/javascript">showmessage(\''.L('SYSTEM_TIPS8').'\',1);</script>');
		}
		
		usleep(100);
		
		echoFlush('<script type="text/javascript">showmessage(\''.L('SYSTEM_TIPS9').'\',3);</script>');
		exit;
	}
	
	public function customClear()
	{
		$custom_dir = FANWE_ROOT.'public/data/caches/custom/';
		$custom_clear = $_SESSION['cache_custom_clear'];
		
		if(empty($custom_clear))
			$this->redirect('Cache/custom');
		else
			$_SESSION['cache_custom_clear'] = $custom_clear;
		
		$root_dir = $custom_clear['root'];
		$current_dir = $custom_clear['current'];
		
		if(!file_exists($root_dir) || !is_dir($root_dir) || strpos($root_dir, $custom_dir) === false)
			$root_dir = $custom_dir;
			
		if(!file_exists($current_dir) || !is_dir($current_dir))
			$current_dir = $root_dir;
		
		$paths = pathinfo($root_dir);
		$root_dir = $paths['dirname'].'/'.$paths['basename'].'/';
		
		$paths = pathinfo($current_dir);
		$current_dir = $paths['dirname'].'/'.$paths['basename'].'/';
		
		if(strpos($current_dir, $root_dir) === false)
			$current_dir = $root_dir;
			
		@set_time_limit(3600);
		if(function_exists('ini_set'))
		{
			ini_set('max_execution_time',3600);
			ini_set("memory_limit","256M");
		}
		
		$this->display();
		ob_start();
		ob_end_flush(); 
		ob_implicit_flush(1);
		
		echoFlush('<script type="text/javascript">showmessage(\''.sprintf(L('CUSTOM_TIPS2'),$current_dir).'\',1);</script>');
		
		$files = array();
		$dirhandle=opendir($current_dir);
		while(($file = readdir($dirhandle)) !== false)
		{
			if(($file!=".") && ($file!=".."))
			{
				$file = $current_dir.$file;
				if(is_dir($file))
				{
					@closedir($dirhandle);
					$current_dir = $file.'/';
					$_SESSION['cache_custom_clear'] = array('root'=>$root_dir,'current'=>$current_dir);
					echoFlush('<script type="text/javascript">showmessage(\''.U('Cache/customClear').'\',2);</script>');
					exit;
				}
				else
				{
					$files[] = $file;
					if(count($files) > 5000)
						break;
				}
			}
		}
		@closedir($dirhandle);
		
		foreach($files as $file)
		{
			@unlink($file);
			echoFlush('<script type="text/javascript">showmessage(\''.sprintf(L('CUSTOM_TIPS3'),$file).'\',1);</script>');
			usleep(20);
		}
		
		if(count($files) > 5000)
		{
			$_SESSION['cache_custom_clear'] = array('root'=>$root_dir,'current'=>$current_dir);
			echoFlush('<script type="text/javascript">showmessage(\''.U('Cache/customClear').'\',2);</script>');
			exit;	
		}
		
		if($root_dir == $current_dir)
		{
			if($custom_dir != $root_dir)
				@rmdir($root_dir);
			
			echoFlush('<script type="text/javascript">showmessage(\''.L('CUSTOM_TIPS5').'\',3);</script>');
			exit;
		}
		else
		{
			$paths = pathinfo($current_dir);
			@rmdir($current_dir);
			$current_dir = $paths['dirname'].'/';
			$_SESSION['cache_custom_clear'] = array('root'=>$root_dir,'current'=>$current_dir);
			echoFlush('<script type="text/javascript">showmessage(\''.U('Cache/customClear').'\',2);</script>');
			exit;
		}
	}
	
	private function getFileList($dir)
	{
		$dirhandle=opendir($dir);
		$list=array();
		while(($file = readdir($dirhandle)) !== false)
		{
			if(($file!=".") && ($file!=".."))
			{
				$list[]=array(
					'name'=>$file,
					'path'=>$dir.$file.(is_dir($dir.$file) ? '/' : ''),
					'is_dir'=>is_dir($dir.$file) ? 1 : 0
				);
			}
		}
		
		@closedir($dirhandle);
		return $list;
	}
}
?>