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
 * 后台权限节点
 +------------------------------------------------------------------------------
 */
class RoleNodeAction extends CommonAction
{
	public function add()
	{
		$nav_list = D("RoleNav")->getField('id,name');
		$this->assign("nav_list",$nav_list);
		$this->display();
	}
	
	public function insert()
	{
		$name=$this->getActionName();
		$model = D($name);
		if(false === $data = $model->create())
		{
			$this->error($model->getError());
		}
		
		if($data['module_name'] == '')
			$data['module_name'] = $data['module'];
		if($_REQUEST['module'] == "" && $_REQUEST['action'] != "")
			$data['auth_type'] = 2;
		elseif($_REQUEST['module'] != "" && $_REQUEST['action'] == "")
			$data['auth_type'] = 1;
		else
			$data['auth_type'] = 0;
			
		if(D("RoleNode")->where("module='".$data['module']."' and action='".$data['action']."'")->count()>0)
			$this->error(L('ROLENODE_UNIQUE'));
		
		//保存当前数据对象
		$list=$model->add($data);
		if ($list !== false)
		{
			$this->saveLog(1,$list);
			$this->success (L('ADD_SUCCESS'));

		}
		else
		{
			$this->saveLog(0,$list);
			$this->error (L('ADD_ERROR'));
		}
	}
	
	public function edit()
	{
		$id = intval($_REQUEST['id']);
		$vo = D("RoleNode")->getById($id);
		$this->assign ( 'vo', $vo );
		
		$nav_list = D("RoleNav")->getField('id,name');
		$this->assign("nav_list",$nav_list);
		$this->display();
	}
}

function getRoleNavName($id)
{
	return D("RoleNav")->where('id = '.$id)->getField('name');
}

function getAuthType($type)
{
	return L('AUTH_TYPE_'.$type);
}
?>