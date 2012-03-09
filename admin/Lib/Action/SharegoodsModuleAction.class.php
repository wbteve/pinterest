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
 * 商品接口模块
 +------------------------------------------------------------------------------
 */
class SharegoodsModuleAction extends CommonAction
{
	public function edit()
	{
		$id = intval($_REQUEST['id']);
		$vo = D("SharegoodsModule")->getById($id);
		$vo['api_data'] = unserialize($vo['api_data']);
		$this->assign('vo',$vo);
		$this->display();
	}
	
	public function update()
	{
		$_POST['api_data'] = serialize($_REQUEST['api_item']);
		$model = D("SharegoodsModule");
		if (false === $data = $model->create ()) {
			echo $model->getError ();
			exit;
			$this->error ( $model->getError () );
		}
		
		// 更新数据
		$list=$model->save($data);
		if (false !== $list)
		{
			$this->saveLog(1,$id);
			$this->assign('jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success (L('EDIT_SUCCESS'));
		}
		else
		{
			//错误提示
			$this->saveLog(0,$id);
			$this->error (L('EDIT_ERROR'));
		}
	}
}
?>