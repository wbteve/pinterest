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
 * 会员组
 +------------------------------------------------------------------------------
 */
class UserGroupAction extends CommonAction
{
	public function add()
	{
		L(include LANG_PATH . FANWE_LANG_SET . '/UserAuthority.php');
		$authoritys = L('AUTHORITYS');
		$this->assign("authoritys",$authoritys);
		$this->display();
	}
	
	public function insert()
	{
		$name=$this->getActionName();
		$model = D ($name);
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		
		//保存当前数据对象
		$gid=$model->add ();
		if ($gid!==false)
		{
			$access_list = $_REQUEST['access_node'];
			foreach($access_list as $module => $actions)
			{
				foreach($actions as $action)
				{
					$item = array();
					$item['gid'] = $gid;
					$item['module'] = $module;
					$item['action'] = $action;
					D('UserGroupAuthority')->add($item);
				}
			}
			
			$this->saveLog(1,$gid);
			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success (L('ADD_SUCCESS'));
		} else {
			//失败提示
			$this->saveLog(0,$gid);
			$this->error (L('ADD_ERROR'));
		}
	}
	
	public function edit()
	{
		L(include LANG_PATH . FANWE_LANG_SET . '/UserAuthority.php');
		$authoritys = L('AUTHORITYS');
		
		$id = intval($_REQUEST['gid']);
		$vo = D("UserGroup")->getById($id);
		
		$uga_list = array();
		$ug_authoritys = D('UserGroupAuthority')->where('gid = '.$id)->findAll();
		
		foreach($ug_authoritys as $uga)
		{
			$uga_list[$uga['module']][$uga['action']] = 1;
		}
		
		$this->assign ( 'vo', $vo );
		$this->assign("authoritys",$authoritys);
		$this->assign("uga_list",$uga_list);
		$this->display();
	}
	
	public function update()
	{
		$id = intval($_REQUEST['gid']);
		$name=$this->getActionName();
		$model = D ( $name );
		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}
		
		// 更新数据
		$list=$model->save($data);
		if (false !== $list)
		{
			D('UserGroupAuthority')->where('gid = '.$id)->delete();
			$access_list = $_REQUEST['access_node'];
			foreach($access_list as $module => $actions)
			{
				foreach($actions as $action)
				{
					$item = array();
					$item['gid'] = $id;
					$item['module'] = $module;
					$item['action'] = $action;
					D('UserGroupAuthority')->add($item);
				}
			}
			
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
	
	public function remove()
	{
		//删除指定记录
		$result = array('isErr'=>0,'content'=>'');
		$id = $_REQUEST['id'];
		if(!empty($id))
		{
			$model = D("UserGroup");
			$condition = array('gid' => array('in',explode (',',$id)));
			$condition1 = array(
				'gid' => array('in',explode (',',$id)),
				'type' => 'system',
			);
			if(D("User")->where($condition)->count('uid') > 0)
			{
				$result['isErr'] = 1;
				$result['content'] = L('GROUP_EXIST_USER');
			}
			elseif($model->where($condition1)->count('gid') > 0)
			{
				$result['isErr'] = 1;
				$result['content'] = L('GROUP_SYSTEY_DEL');
			}
			else
			{
				if(false !== $model->where ( $condition )->delete())
				{
					$this->saveLog(1,$id);
				}
				else
				{
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
	
	function authoritys()
	{
		L(include LANG_PATH . FANWE_LANG_SET . '/UserAuthority.php');
		$authoritys = L('AUTHORITYS');
		
		$id = intval($_REQUEST['gid']);
		$uga_list = array();
		$ug_authoritys = D('UserGroupAuthority')->where('gid = '.$id)->findAll();
		
		foreach($ug_authoritys as $uga)
		{
			$uga_list[$uga['module']][$uga['action']] = 1;
		}
		
		$this->assign("authoritys",$authoritys);
		$this->assign("uga_list",$uga_list);
		$this->display();
	}
}

function getTypeName($type)
{
	return L('TYPE_'.$type);	
}

function getDelLink($type,$gid)
{
	if($type != 'system')
	{
		return '<a href="javascript:;" onclick="removeData(this,\''.$gid.'\',\'gid\')">'.L('REMOVE').'</a>';
	}
}
?>