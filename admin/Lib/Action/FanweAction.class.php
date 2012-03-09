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
 * 
 +------------------------------------------------------------------------------
 */
class FanweAction extends CommonAction
{
	public function __construct()
	{
		//$this->initModule();
		parent::__construct();
	}
	
	//同步数据库中的Module
	private function initModule()
	{
		$actions = array(
			'index'=>1,
			'add'=>1,
			'insert'=>0,
			'update'=>0,
			'remove'=>0,
			'editfield'=>0,
			'togglestatus'=>0,
		);
		
		$custom_modules = array(
			'Cache' => array(),
			'Index' => array(),
		);
		$disable_modules = array('Fanwe','Common','Public');
		
		$files = Dir::getList(ADMIN_PATH . '/Lib/Action/');
		//$modules = D('RoleNode')->where('auth_type = 1')->findAll();
		$exists_modules = array();
		foreach ($files as $file)
		{
			if ($file != '.' && $file != '..' && stripos($file,'Action.class.php') !== false)
			{
				$module = str_ireplace('Action.class.php','', $file);
				if(!in_array($module,$disable_modules))
				{
					if (D("RoleNode")->where("module='" . $module . "' AND action=''")->count() == 0)
					{
						//增加该模块授权
						$module_data = array();
						$module_data['status'] = 1;
						$module_data['module'] = $module;
						$module_data['module_name'] = L($module);
						$module_data['auth_type'] = 1;
						$module_data['is_show'] = 0;
						$module_data['sort'] = 10;
						D("RoleNode")->add($module_data);
						
						if(array_key_exists($module,$custom_modules))
							$module_actions = $custom_modules[$module];
						else
							$module_actions = $actions;
							
						foreach($module_actions as $action => $is_show)
						{
							//增加该模块操作授权
							$action_data = array();
							$action_data['module'] = $module;
							$action_data['module_name'] = L($module);
							$action_data['action'] = $action;
							$action_data['action_name'] = L($module.'_'.$action);
							$action_data['auth_type'] = 0;
							$action_data['is_show'] = $is_show;
							$action_data['status'] = 1;
							$action_data['sort'] = 10;
							if($is_show)
								$action_data['nav_id'] = 3;
							D("RoleNode")->add($action_data);
						}
					}
					array_push($exists_modules, $module);
				}
			}
		}
		//删除多余的module
		D("RoleNode")->where(array("module" => array("not in" , $exists_modules) , "action" => ''))->delete();
	}
}
?>