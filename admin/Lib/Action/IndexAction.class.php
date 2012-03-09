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
 * 后台首页
 +------------------------------------------------------------------------------
 */
class IndexAction extends FanweAction
{
	public function index()
	{
		if (isset($_SESSION[C('USER_AUTH_KEY')]))
			$this->display();
		else
			$this->redirect('Public/login');
	}

	public function top()
	{
		$list = D('RoleNav')->where('status=1')->field('id,name')->order("sort")->findAll();
		$this->assign('role_navs',$list);
		$this->display();
	}

	public function left()
	{
		$id	= intval($_REQUEST['id']);
        $menus  = array();
        //if(isset($_SESSION['menu_'.$id.'_'.$_SESSION[C('USER_AUTH_KEY')]]))
		if(false)
        	$menus = $_SESSION['menu_'.$id.'_'.$_SESSION[C('USER_AUTH_KEY')]];
        else
		{
			if($id == 0)
				$id = D("RoleNav")->where('status=1')->order("sort ASC,id ASC")->getField('id');

			if($id == 0)
				return;

			$where = array();
			$where['status']    = 1;
			$where['nav_id']    = $id;
			$where['is_show']   = 1;
			$where['auth_type'] = 0;

			$no_modules = explode(',',strtoupper(C('NOT_AUTH_MODULE')));

			$access_list = $_SESSION['_ACCESS_LIST'];
			$node_list = D("RoleNode")->where($where)->field('id,action,action_name,module,module_name')->order('sort ASC,id ASC')->select();
			foreach($node_list as $key=>$node)
			{
				if((isset($access_list[strtoupper($node['module'])]['MODULE']) || isset($access_list[strtoupper($node['module'])][strtoupper($node['action'])])) || $_SESSION['administrator'] || in_array(strtoupper($node['module']),$no_modules))
				{
					$menus[$node['module']]['nodes'][] = $node;
					$menus[$node['module']]['name']	= $node['module_name'];
				}
            }

			$_SESSION['menu_'.$id.'_'.$_SESSION[C('USER_AUTH_KEY')]] = $menus;
		}

		$this->assign('menus',$menus);
		$this->display();
	}

	public function main()
	{
        $this->redirect('Share/index');
		//$this->display();
	}

	public function password()
	{
		$id = $_SESSION[C('USER_AUTH_KEY')];
		$admin = D('Admin')->getById($id);
		$this->assign('admin',$admin);
		$this->display();
	}

	public function changePwd()
	{
		$old_pwd = $_REQUEST['old_pwd'];
		$new_pwd = $_REQUEST['new_pwd'];
		$confirm_pwd = $_REQUEST['confirm_pwd'];

		if($old_pwd == '')
			$this->error(L('OLD_PWD_REQUIRE'));

		if($new_pwd == '')
			$this->error(L('NEW_PWD_REQUIRE'));

		if($new_pwd != $confirm_pwd)
			$this->error(L('CONFIRM_ERROR'));

		$id = $_SESSION[C('USER_AUTH_KEY')];
		$admin = D('Admin')->getById($id);

		$old_pwd = md5($old_pwd);
		if($old_pwd != $admin['admin_pwd'])
			$this->error(L('OLD_PWD_ERROR'));

		D("Admin")->where('id = '.$id)->setField('admin_pwd',md5($new_pwd));
		$this->assign('jumpUrl',U('Index/password'));
		$this->success (L('EDIT_SUCCESS'));
	}

	public function footer()
	{
		$this->display();
	}
}
?>