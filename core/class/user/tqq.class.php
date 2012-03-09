<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

require_once FANWE_ROOT.'core/class/user/userbase.class.php';
@set_include_path(FANWE_ROOT.'/sdks/tqq/');
require_once 'OpenSDK/Tencent/Weibo.php';

class TqqUser extends UserBase
{
	public $config;
	private $type = 'tqq';
	
	public function TqqUser($access_token = '',$oauth_token_secret = '')
	{
		$this->config = $this->getConfig($this->type);
		OpenSDK_Tencent_Weibo::init($this->config['app_key'],$this->config['app_secret']);
		if(!empty($access_token) && !empty($oauth_token_secret))
		{
			global $_FANWE;
			$_FANWE['login_oauth']['tqq'][OpenSDK_Tencent_Weibo::ACCESS_TOKEN] = $access_token;
			$_FANWE['login_oauth']['tqq'][OpenSDK_Tencent_Weibo::OAUTH_TOKEN_SECRET] = $oauth_token_secret;
		}
	}
	
	public function loginHandler()
	{
		global $_FANWE;
		$user = $this->getUserInfo();
		$bind_user = $this->getUserByTypeKeyId($this->type,$user['name']);
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
			$data['user_email'] = $user['email'];
			$data['type'] = $this->type;
			$data['user'] = $user;
			$this->jumpUserBindReg($data,$user['nick']);
		}
	}
	
	public function bindHandler()
	{
		global $_FANWE;
		if($_FANWE['uid'] == 0)
			exit;
		
		$user = $this->getUserInfo();
		$bind_user = $this->getUserByTypeKeyId($this->type,$user['name']);
		if($bind_user && $bind_user['uid'] != $_FANWE['uid'])
		{
			$data = array();
			$data['short_name'] = $this->config['short_name'];
			$data['keyid'] = $user['name'];
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
		$info['oauth_token_secret'] = $user['oauth_token_secret'];
		unset($user['access_token']);
		unset($user['oauth_token_secret']);
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
			$data['keyid'] = $user['name'];
			$data['refresh_time'] = 0;
			
			$info = array();
			$info['access_token'] = $user['access_token'];
			$info['oauth_token_secret'] = $user['oauth_token_secret'];
			unset($user['access_token']);
			unset($user['oauth_token_secret']);
			$info['user'] = $user;
			$data['info'] = addslashes(serialize($info));
			
			$sync = array();
			$sync['weibo'] = 1;
			$sync['topic'] = 1;
			$sync['medal'] = 1;
			$data['sync'] = serialize($sync);
			
			if(!empty($user['head']) && !FS('User')->getIsAvatar($_FANWE['uid']))
			{
				$img = copyFile($user['head'].'/180');
				if($img !== false)
					FS('User')->saveAvatar($_FANWE['uid'],$img['path']);
			}
			
			FDB::insert('user_bind',$data,false,true);
			//绑定后推送网站信息
			if((int)$_FANWE['setting']['bind_push_weibo'] == 1)
			{
				$weibo = array();
				$weibo['content'] = sprintf(lang('user','bind_weibo_message'),$_FANWE['setting']['site_name'],$_FANWE['setting']['site_description'],$_FANWE['setting']['site_name']);
				$weibo['img'] = "";
				$weibo['ip'] = $_FANWE['client_ip'];
				$weibo['url'] = $_FANWE['site_url'].FU('u/me',array('uid'=>$_FANWE['uid']));
				$this->sentShare($_FANWE['uid'],$weibo);
			}
		}
	}
	
	public function sentShare($uid,$data)
	{
		global $_FANWE;
		static $bln = false;
		if(!$bln)
		{
			$uid = (int)$uid;
			$bind = FS("User")->getUserBindByType($uid,'tqq');
			
			$_FANWE['login_oauth']['tqq'][OpenSDK_Tencent_Weibo::ACCESS_TOKEN] = $bind['access_token'];
			$_FANWE['login_oauth']['tqq'][OpenSDK_Tencent_Weibo::OAUTH_TOKEN_SECRET] = $bind['oauth_token_secret'];
			$bln = true;
		}
		
		$data['content'] .= ' '.$data['url'];
		
		if(empty($data['img']))
		{
			OpenSDK_Tencent_Weibo::call('t/add', array(
				'content' => $data['content'],
				'clientip' => $_FANWE['client_ip'],
			), 'POST');
		}
		else
		{
			$content = getUrlContent($data['img']);
			$filename = reset(explode('?',basename($data['img'])));
			OpenSDK_Tencent_Weibo::call('t/add_pic', array(
				'content' => $data['content'],
				'clientip' => $_FANWE['client_ip'],
			), 'POST', array(
				'pic' => array(
					'type' => 'image/jpg',
					'name' => $filename,
					'data' => $content,
				),
			));
		}
	}
	
	public function getUserInfo()
	{
		global $_FANWE;
		$user = OpenSDK_Tencent_Weibo::call('user/info');
		
		if((int)$user['errcode'] != 0 || (int)$user['ret'] != 0)
			exit($user['msg']);
		
		$user = $user['data'];
		$user['access_token'] = $_FANWE['login_oauth']['tqq'][OpenSDK_Tencent_Weibo::ACCESS_TOKEN];
		$user['oauth_token_secret'] = $_FANWE['login_oauth']['tqq'][OpenSDK_Tencent_Weibo::OAUTH_TOKEN_SECRET];
		
		return $user;
	}
}
?>