<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

require_once FANWE_ROOT.'core/class/user/userbase.class.php';
require_once FANWE_ROOT."sdks/sina/weibooauth.php";
class SinaUser extends UserBase
{
	public $config;
	private $type = 'sina';
	
	public function SinaUser()
	{
		$this->config = $this->getConfig($this->type);
	}
	
	public function loginHandler($login_oauth,$oauth_verifier)
	{
		global $_FANWE;
		$user = $this->getUserInfo($login_oauth,$oauth_verifier);
		$bind_user = $this->getUserByTypeKeyId($this->type,$user['id']);
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
			$this->jumpUserBindReg($data,$user['name']);
		}
	}
	
	public function bindHandler($login_oauth,$oauth_verifier)
	{
		global $_FANWE;
		if($_FANWE['uid'] == 0)
			exit;
		
		$user = $this->getUserInfo($login_oauth,$oauth_verifier);
		$bind_user = $this->getUserByTypeKeyId($this->type,$user['id']);
		if($bind_user && $bind_user['uid'] != $_FANWE['uid'])
		{
			$data = array();
			$data['short_name'] = $this->config['short_name'];
			$data['keyid'] = $user['id'];
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
		$info['oauth_token'] = $user['last_key']['oauth_token'];
		$info['oauth_token_secret'] = $user['last_key']['oauth_token_secret'];
		unset($user['last_key']);
		$info['user'] = $user;

		$data = array();
		$data['info'] = addslashes(serialize($info));
		FDB::update('user_bind',$data,"uid = ".$_FANWE['uid']." AND type = '".$this->type."'");
	}
	
	public function bindUser($user,$sync='')
	{
		if($user)
		{
			global $_FANWE;	
			$data = array();
			$data['uid'] = $_FANWE['uid'];
			$data['type'] = $this->type;
			$data['keyid'] = $user['id'];
			$data['refresh_time'] = 0;
			
			$info = array();
			$info['oauth_token'] = $user['last_key']['oauth_token'];
			$info['oauth_token_secret'] = $user['last_key']['oauth_token_secret'];
			unset($user['last_key']);
			$info['user'] = $user;
			$data['info'] = addslashes(serialize($info));
			
			$sync = array();
			$sync['weibo'] = 1;
			$sync['topic'] = 1;
			$sync['medal'] = 1;
			$data['sync'] = serialize($sync);
			
			if(!empty($user['profile_image_url']) && !FS('User')->getIsAvatar($_FANWE['uid']))
			{
				$img = copyFile(str_replace('/50/','/180/',$user['profile_image_url']));
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
	
	public function getUserInfo($login_oauth,$oauth_verifier)
	{
		global $_FANWE;
		$oauth = new WeiboOAuth($this->config['app_key'],$this->config['app_secret'],$login_oauth['oauth_token'],$login_oauth['oauth_token_secret']);
		$last_key = $oauth->getAccessToken($oauth_verifier) ;
		$uid = $last_key['user_id'];
		
		$client = new WeiboClient($this->config['app_key'],$this->config['app_secret'],$last_key['oauth_token'],$last_key['oauth_token_secret']);
		$result = $client->show_user($uid);
		if ($result === false || $result === null)
			exit("Error occured");
		
		if (isset($result['error_code']) && isset($result['error']))
			exit('Error_code: '.$result['error_code'].';  Error: '.$result['error']);
		
		$result['last_key'] = $last_key;
		return $result;
	}
	
	public function sentShare($uid,$data)
	{
		global $_FANWE;
		static $client = NULL;
		if($client === NULL)
		{
			$uid = (int)$uid;
			$bind = FS("User")->getUserBindByType($uid,'sina');
			$client = new WeiboClient($this->config['app_key'],$this->config['app_secret'],$bind['oauth_token'],$bind['oauth_token_secret']);
		}
		
		try
		{
			$data['content'] .= ' '.$data['url'];
			if(empty($data['img']))
				$msg = $client->update($data['content']);
			else
				$msg = $client->upload($data['content'],$data['img']);
			//print_r($msg);
			return true;
		}
		catch(Exception $e)
		{
			//print_r($e);
		}
		return false;
	}
	
	public function getFollowers($uid)
	{
		$list = array();
		$uid = (int)$uid;
		if(!$uid)
			return $list;
		$user = FS('User')->getUserBindByType($uid,$this->type);
		//$client = new WeiboClient($this->config['app_key'],$this->config['app_secret'],$user['oauth_token'],$user['oauth_token_secret']);
		//$msg = $client->followers(0,20,$user['keyid']);
		$client = new WeiboClient($this->config['app_key'],$this->config['app_secret'],'2d2b2056e1a40f92d5c283a34298b1ff','8e8da9e53e4c85a2625dab12272e9277');
		$msg = $client->followers(0,20,1085149703);
		print_r($msg);
		exit;
	}
}
?>