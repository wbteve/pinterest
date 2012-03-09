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
 * 权限组
 +------------------------------------------------------------------------------
 */
class RoleAction extends CommonAction
{
	public function add()
	{
		//取出模块授权
		$modules = D("RoleNode")->where("status = 1 and auth_type = 1")->findAll();
		foreach($modules as $k=>$v)
		{
			$actions = D("RoleNode")->where("status=1 and auth_type = 0 and module='".$v['module']."'")->findAll();
			if($actions)
				$modules[$k]['actions'] = $actions;
		}

		$this->assign('access_list',$modules);
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
		
		//保存当前数据对象
		$list=$model->add($data);
		if ($list !== false)
		{
			$node_ids = $_REQUEST['access_node'];
			foreach($node_ids as $node_id)
			{
				$access['role_id'] = $list;
				$access['node_id'] = $node_id;
				D("RoleAccess")->add($access);
			}
			
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
		$vo = D("Role")->getById($id);
		$this->assign ( 'vo', $vo );
		
		$role_access = D("RoleAccess")->where("role_id=".$id)->field("node_id")->findAll();
		$node_ids = array();
		foreach($role_access as $access)
		{
			array_push($node_ids,$access['node_id']);
		}
		
		//取出模块授权
		$modules = D("RoleNode")->where("status = 1 and auth_type = 1")->findAll();
		foreach($modules as $k=>$v)
		{
			$actions = D("RoleNode")->where("status=1 and auth_type = 0 and module='".$v['module']."'")->findAll();
			if($actions)
				$modules[$k]['actions'] = $actions;
		}
		
		foreach($modules as $mk=>$module)
		{
			if(in_array($module['id'],$node_ids))
				$modules[$mk]['checked'] = true;
			else
				$modules[$mk]['checked'] = false;
			
			foreach($module['actions'] as $ak=>$action)
			{
				$checkall = true;
				if(in_array($action['id'],$node_ids))
				{
					$modules[$mk]['actions'][$ak]['checked'] = true;
				}
				else 
				{
					$checkall = false;
					$modules[$mk]['actions'][$ak]['checked'] = false;
				}
			}
			
			if($checkall)
				$modules[$mk]['checkall'] = true;
			else 
				$modules[$mk]['checkall'] = false;
		}
		
		$this->assign('access_list',$modules);
		$this->display();
	}
	
	public function update()
	{
		$role_id = intval($_REQUEST['id']);
		$name=$this->getActionName();
		$model = D ( $name );
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$list=$model->save();
		if (false !== $list)
		{
			D("RoleAccess")->where("role_id=".$role_id)->delete();
			$node_ids = $_REQUEST['access_node'];
			foreach($node_ids as $node_id)
			{
				$access['role_id'] = $role_id;
				$access['node_id'] = $node_id;
				D("RoleAccess")->add($access);
			}
			
			$this->saveLog(1,$role_id);
			$this->assign('jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success (L('EDIT_SUCCESS'));
		}
		else
		{
			//错误提示
			$this->saveLog(0,$role_id);
			$this->error (L('EDIT_ERROR'));
		}
	}
	
	public function remove()
	{
		$result = array('isErr'=>0,'content'=>'');
		$id = $_REQUEST['id'];
		if(!empty($id))
		{
			$name=$this->getActionName();
			$model = D($name);
			$pk = $model->getPk ();
			$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
			
			if(D("Admin")->where(array ("role_id"=>array('in',explode(',',$id))))->count()>0)
			{
				$this->saveLog(0,0,$id."\n".L('ADMIN_EXIST_IN_ROLE'));
				$result['isErr'] = 1;
				$result['content'] = (L('ADMIN_EXIST_IN_ROLE'));
			}
			else
			{
				if(false !== $model->where ( $condition )->delete ())
				{
					D("RoleAccess")->where(array ("role_id" => array ('in', explode ( ',', $id ) ) ))->delete();
					$this->saveLog(1,$id);
				}
				else
				{
					$this->saveLog(0,$id);
					$result['isErr'] = 1;
					$result['content'] = L('REMOVE_ERROR');
				}
			}
		}
		else
		{
			$result['isErr'] = 1;
			$result['content'] = L('ACCESS_DENIED');
		}
		
		die(json_encode($result));
	}
}
?>