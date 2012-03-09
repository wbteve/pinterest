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
 * 管理员
 +------------------------------------------------------------------------------
 */
class AdminAction extends CommonAction
{
	public function add()
	{
		$nav_list = D("Role")->getField('id,name');
		$this->assign("role_list",$nav_list);
		$this->display();
	}
	
	public function edit()
	{
		$id = intval($_REQUEST['id']);
		$vo = D("Admin")->getById($id);
		$this->assign ( 'vo', $vo );
		
		$nav_list = D("Role")->getField('id,name');
		$this->assign("role_list",$nav_list);
		$this->display();
	}
	
	public function update()
	{
		$id = intval($_REQUEST['id']);
		$name=$this->getActionName();
		$model = D ( $name );
		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}
		
		if($_REQUEST['admin_pwd'] == '')
			unset($data['admin_pwd']);
		
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

function getRoleName($id)
{
	return D("Role")->where('id = '.$id)->getField('name');
}
?>