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
 * 系统设置
 +------------------------------------------------------------------------------
 */
class SysConfAction extends CommonAction
{
	public function index()
	{
		$list = D('SysConf')->where("status=1 and group_id > 0 and is_show = 1")->order("sort asc")->findAll();
		$conf_list = array(); //用于输出分组格式化后的数组

		foreach($list as $k=>$v)
		{
			if($v['name']=='DEFAULT_LANG')
			{
			   $lang_list = Dir::getList(LANG_PATH);
			   $v['val_arr'] = array();
			   foreach($lang_list as $lang_item)
			   {
				   if($lang_item != '.' && $lang_item != '..')
				   {
					   $v['val_arr'][] = $lang_item;
				   }
			   }
			}
			elseif($v['name']=='SITE_TMPL')
			{
			   $tmpl_list = Dir::getList(FANWE_ROOT.'tpl');
			   $v['val_arr'] = array();
			   foreach($tmpl_list as $tmpl_item)
			   {
				   if($tmpl_item != '.' && $tmpl_item != '..')
				   {
					   $v['val_arr'][] = $tmpl_item;
				   }
			   }
			}
			else
			{
				$v['val_arr'] = explode(",",$v['val_arr']);
			}

			$conf_list[L("SYSCONF_GROUP_".$v['group_id'])][$k] = $v;
		}
		$this->assign("conf_list",$conf_list);
		$this->display();
	}

	public function update()
	{
		$upload_list = $this->uploadImages();
		if($upload_list)
		{
			foreach($upload_list as $upload_item)
			{
				if($upload_item['key']=="SITE_LOGO")
				{
					$site_logo = $upload_item['recpath'].$upload_item['savename'];
					$site_logo = moveFile($site_logo,'./logo.gif');
				}
				if($upload_item['key']=="WATER_IMAGE")
				{
					$water_image = $upload_item['recpath'].$upload_item['savename'];
				}
				if($upload_item['key']=="FOOT_LOGO")
				{
					$foot_logo = $upload_item['recpath'].$upload_item['savename'];
					$foot_logo = moveFile($foot_logo,'./foot_logo.gif');
				}
				if($upload_item['key']=="LINK_LOGO")
				{
					$link_logo = $upload_item['recpath'].$upload_item['savename'];
					$link_logo = moveFile($link_logo,'./link_logo.gif');
				}
			}
		}

		$list = D('SysConf')->where("status=1")->findAll();
		foreach($list as $k=>$v)
		{
			$v['val'] = isset($_REQUEST[$v['name']])?$_REQUEST[$v['name']]:$v['val'];
			if($v['name']=="SITE_LOGO" && !empty($site_logo))
			{
				if($site_logo != $v['val'])
				{
					@unlink(FANWE_ROOT.$v['val']);
					$v['val'] = $site_logo;
				}
			}

			if($v['name']=="WATER_IMAGE" && !empty($water_image))
			{
				if($water_image != $v['val'])
				{
					@unlink(FANWE_ROOT.$v['val']);
					$v['val'] = $water_image;
				}
			}

			if($v['name']=="FOOT_LOGO" && !empty($foot_logo))
			{
				if($foot_logo != $v['val'])
				{
					@unlink(FANWE_ROOT.$v['val']);
					$v['val'] = $foot_logo;
				}
			}

			if($v['name']=="LINK_LOGO" && !empty($link_logo))
			{
				if($link_logo != $v['val'])
				{
					@unlink(FANWE_ROOT.$v['val']);
					$v['val'] = $link_logo;
				}
			}

			D('SysConf')->save($v);
		}

		$this->saveLog(1);
		$this->success(L('EDIT_SUCCESS'));
	}
}

function moveFile($file_name,$target_name)
{
	$name = $file_name;

	if (function_exists("move_uploaded_file"))
	{
		if (move_uploaded_file(FANWE_ROOT.$file_name,FANWE_ROOT.$target_name))
		{
			chmod(FANWE_ROOT.$target_name,0755);
			$name = $target_name;
			unlink(FANWE_ROOT.$file_name);
		}
		else if (copy(FANWE_ROOT.$file_name,FANWE_ROOT.$target_name))
		{
			chmod(FANWE_ROOT.$target_name,0755);
			$name = $target_name;
			unlink(FANWE_ROOT.$file_name);
		}
	}
	elseif (copy(FANWE_ROOT.$file_name,FANWE_ROOT.$target_name))
	{
		chmod(FANWE_ROOT.$target_name,0755);
		$name = $target_name;
		unlink(FANWE_ROOT.$file_name);
	}

	return $name;
}
?>