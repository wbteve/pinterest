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
 * 专辑配置
 +------------------------------------------------------------------------------
 */
class AlbumSettingAction extends CommonAction
{
	public function index()
	{
		$settings = array();
		$list = D("SysConf")->where('group_id = 8')->findAll();
		foreach($list as $item)
		{
			if($item['name'] == 'ALBUM_DEFAULT_TAGS')
			{
				$item['val'] = unserialize($item['val']);
				$item['val'] = implode(' ',$item['val']);
			}
			$settings[$item['name']] = $item['val'];
		}
		$this->assign("settings",$settings);
		$this->display();
	}
	
	public function update()
	{
		$settings['ALBUM_TAG_COUNT'] = (int)$_REQUEST['ALBUM_TAG_COUNT'];
		$tags = trim($_REQUEST['ALBUM_DEFAULT_TAGS']);
		$tags = str_replace('　',' ',$tags);
		$tags = explode(' ',$tags);
		$tags = array_unique($tags);
		$settings['ALBUM_DEFAULT_TAGS'] = serialize($tags);
		
		foreach($settings as $key => $val)
		{
			D("SysConf")->where("name = '$key'")->setField('val',$val);
		}
		
		$this->saveLog(1);
		$this->success(L('EDIT_SUCCESS'));
	}
}
?>