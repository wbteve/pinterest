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
 * 闲置配置
 +------------------------------------------------------------------------------
 */
class SecondSettingAction extends CommonAction
{
	public function index()
	{
		$settings = array();
		$list = D("SysConf")->where('group_id = 7')->findAll();
		foreach($list as $item)
		{
			$settings[$item['name']] = $item['val'];
		}
		$this->assign("settings",$settings);
		$this->display();
	}
	
	public function update()
	{
		$settings['SECOND_STATUS'] = (int)$_REQUEST['SECOND_STATUS'];
		$settings['SECOND_TAOBAO_FORUMID'] = trim($_REQUEST['SECOND_TAOBAO_FORUMID']);
		$settings['SECOND_TAOBAO_SIGN'] = trim($_REQUEST['SECOND_TAOBAO_SIGN']);
		$settings['SECOND_ARTICLES'] = $_REQUEST['SECOND_ARTICLES'];
		foreach($settings as $key => $val)
		{
			D("SysConf")->where("name = '$key'")->setField('val',$val);
		}
		
		$this->saveLog(1);
		$this->success(L('EDIT_SUCCESS'));
	}
}
?>