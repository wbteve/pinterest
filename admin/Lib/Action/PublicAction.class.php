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
 * 公共
 +------------------------------------------------------------------------------
 */
class PublicAction extends CommonAction
{
	/**
     +----------------------------------------------------------
	 * 检查用户是否登录
     +----------------------------------------------------------
	 */
	public function checkUser()
	{
		if (!isset($_SESSION[C('USER_AUTH_KEY')]))
		{
			$this->assign('jumpUrl', 'Public/login');
			$this->error(L('NOT_LOGIN'));
		}
	}

	/**
     +----------------------------------------------------------
	 * 用户登录页面
     +----------------------------------------------------------
	 */
	public function login()
	{
		if (!isset($_SESSION[C('USER_AUTH_KEY')]))
		{
			$this->display();
		}
		else
		{
			$this->redirect('Index/index');
		}
	}

	public function index()
	{
		//如果通过认证跳转到首页
		redirect(__APP__);
	}

	/**
     +----------------------------------------------------------
	 * 用户登出
     +----------------------------------------------------------
	 */
	public function logout()
	{
		if (isset($_SESSION[C('USER_AUTH_KEY')]))
		{
			$loginout_success = L('LOGOUT_SUCCESS');
			unset($_SESSION[C('USER_AUTH_KEY')]);
			unset($_SESSION);
			session_destroy();
			$this->assign("jumpUrl", U("Public/login"));
			$this->success($loginout_success);
		}
		else
		{
			$this->error(L('LOGOUT_ALREADY'));
		}
	}

	/**
     +----------------------------------------------------------
	 * 登录检测
     +----------------------------------------------------------
	 */
	public function checkLogin()
	{
		if (empty($_POST['admin_name']))
		{
			$this->error(L('ADMIN_NAME_REQUIRE'));
		}
		elseif (empty($_POST['admin_pwd']))
		{
			$this->error(L('ADMIN_PWD_REQUIRE'));
		}
		elseif (empty($_POST['verify']))
		{
			$this->error(L('VERIFY_REQUIRE'));
		}

		//生成认证条件

		$map = array();

		// 支持使用绑定帐号登录
		$map['admin_name'] = $_POST['admin_name'];
		$map["status"] = array('gt' , 0);

		if ($_SESSION['verify'] != md5($_POST['verify']))
		{
			$this->error(L('VERIFY_ERROR'));
		}

		import('@.ORG.RBAC');
		$auth_info = RBAC::authenticate($map);

		//使用用户名、密码和状态的方式进行认证
		if (false === $auth_info)
		{
			$this->saveLog(0, 0);
			$this->error(L('ADMIN_NAME_NOT_EXIST'));
		}
		else
		{
			if ($auth_info['admin_pwd'] != md5($_POST['admin_pwd']))
			{
				$this->saveLog(0, 0);
				$this->error(L('ADMIN_PWD_ERROR'));
			}

			Session::setExpire(time() + fanweC("EXPIRED_TIME") * 60);

			$_SESSION[C('USER_AUTH_KEY')] = $auth_info['id'];
			$_SESSION['admin_name'] = $auth_info['admin_name'];
			$_SESSION['last_time'] = $auth_info['last_time'];
			$_SESSION['login_count'] = $auth_info['login_count'];

			if ($auth_info['admin_name'] == fanweC('SYS_ADMIN'))
			{
				$_SESSION[C('ADMIN_AUTH_KEY')] = true;
			}

			//保存登录信息
			$admin = M(C('USER_AUTH_MODEL'));
			$ip = getClientIp();
			$time = gmtTime();
			$data = array();
			$data['id'] = $auth_info['id'];
			$data['last_login_time'] = $time;
			$data['login_count'] = array('exp' , 'login_count + 1');
			$data['last_login_ip'] = $ip;
			$admin->save($data);

			// 缓存访问权限
			RBAC::saveAccessList();
			$this->saveLog(1, 0);
			$this->success(L('LOGIN_SUCCESS'));
		}
	}

	public function verify ()
	{
		$type = isset($_GET['type']) ? $_GET['type'] : 'png';
		$type = 'png';
		import("@.ORG.Image");
		Image::buildImageVerify(4, 1, $type);
	}
}
?>