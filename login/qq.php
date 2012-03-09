<?php
// QQ的api登录接口
require_once FANWE_ROOT."sdks/qq/qq.func.php";
class qq
{
	private $config;
	public function __construct()
	{
		global $_FANWE;
		$this->config = $_FANWE['cache']['logins']['qq'];
	}

	public function getInfo()
	{
		global $_FANWE;
		$data['name'] = $this->config['name'];
		$data['short_name'] = $this->config['short_name'];
		$data['is_syn'] = $this->config['is_syn'];
		$data['bind_img'] = SITE_URL.'login/qq/bind_qq.png';
		$data['icon_img'] = SITE_URL.'login/qq/icon_qq.png';
		$data['login_img'] = SITE_URL.'login/qq/login_qq.png';
		$data['login_url'] = SITE_URL."login.php?mod=qq";
		$data['bind_url'] = SITE_URL."login.php?bind=qq";
		$data['unbind_url'] = SITE_URL."login.php?unbind=qq";
		return $data;
	}

	public function loginJump()
	{
		global $_FANWE;
		if($_FANWE['uid'] > 0)
		{
			$this->bindJump();
			exit;
		}
		
		fSetCookie('callback_type','login');
		$this->jump();
	}

	public function bindJump()
	{
		global $_FANWE;
		if($_FANWE['uid'] == 0)
		{
			$this->loginJump();
			exit;
		}
		
		fSetCookie('callback_type','bind');
		$this->jump();
	}
	
	private function jump()
	{
		global $_FANWE;
		$url = getQqLoginUrl($this->config['app_key']);
		$url = FU('tgo',array('url'=>$url));
		fHeader("location:".$url);
	}

	public function unBind()
	{
		global $_FANWE;
		if($_FANWE['uid'] > 0)
		{
			FDB::delete('user_bind',"uid = ".$_FANWE['uid']." AND type = 'qq'");
		}
		fHeader("location: ".FU('settings/bind'));
	}
}
?>