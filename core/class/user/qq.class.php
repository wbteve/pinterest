<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

require_once 'userbase.class.php';
require_once FANWE_ROOT."sdks/qq/qq.func.php";
class QqUser extends UserBase
{
	public $config;
	private $type = 'qq';
	
	public function QqUser()
	{
		$this->config = $this->getConfig($this->type);
	}
	
	public function loginHandler($access_token,$openid)
	{
		global $_FANWE;
		$user = $this->getUserInfo($access_token,$openid);
		$bind_user = $this->getUserByTypeKeyId($this->type,$openid);
		if($bind_user)
		{
			if($bind_user['status'] == 0)
				showError('登陆失败','该帐户已被管理员锁定',FU('index'));
			
			$_FANWE['uid'] = $bind_user['uid'];
			$this->updateBindInfo($user);
			FS('User')->setSession($bind_user,1209600);
		}
		else
		{
			$data = array();
			$data['type'] = $this->type;
			$data['user'] = $user;
			$this->jumpUserBindReg($data,$user['nickname']);
		}
	}
	
	public function bindHandler($access_token,$openid)
	{
		global $_FANWE;
		if($_FANWE['uid'] == 0)
			exit;
		
		$user = $this->getUserInfo($access_token,$openid);
		$bind_user = $this->getUserByTypeKeyId($this->type,$openid);
		if($bind_user && $bind_user['uid'] != $_FANWE['uid'])
		{
			$data = array();
			$data['short_name'] = $this->config['short_name'];
			$data['keyid'] = $openid;
			$data['type'] = $this->type;
			$data['user'] = $user;
			fSetCookie('sync_bind_exists',authcode(serialize($data),'ENCODE'));
		}
		else
		{
			$this->bindUser($user);
		}
	}
	
	public function bindByData($data)
	{
		$this->bindUser($data['user']);
	}
	
	private function updateBindInfo($user)
	{
		global $_FANWE;	
		$info = array();
		$info['access_token'] = $user['access_token'];
		unset($user['access_token']);
		$info['user'] = $user;

		$data = array();
		$data['info'] = addslashes(serialize($info));
		FDB::update('user_bind',$data,"uid = ".$_FANWE['uid']." AND type = '".$this->type."'");
	}
	
	public function bindUser($user)
	{
		if($user)
		{
			global $_FANWE;	
			$data = array();
			$data['uid'] = $_FANWE['uid'];
			$data['type'] = $this->type;
			$data['keyid'] = $user['openid'];
			unset($user['openid']);
			$data['refresh_time'] = 0;
			
			$info = array();
			$info['access_token'] = $user['access_token'];
			unset($user['access_token']);
			$info['user'] = $user;
			$data['info'] = addslashes(serialize($info));
			
			if(!empty($user['figureurl_2']) && !FS('User')->getIsAvatar($_FANWE['uid']))
			{
				$img = copyFile($user['figureurl_2']);
				if($img !== false)
					FS('User')->saveAvatar($_FANWE['uid'],$img['path']);
			}
			FDB::insert('user_bind',$data,false,true);
		}
	}
	
	public function getUserInfo($access_token,$openid)
	{
		global $_FANWE;
		$user = getQqUserInfo($this->config['app_key'],$access_token,$openid);
		if(!isset($user['nickname']))
			exit;
		$user['access_token'] = $access_token;
		$user['openid'] = $openid;
		return $user;
	}
}
?>