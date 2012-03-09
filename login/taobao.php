<?php
//淘宝登录接口
require_once FANWE_ROOT."sdks/taobao/taobao.func.php";
class taobao
{
	private $config;
	public function __construct()
	{
		global $_FANWE;
		$this->config = $_FANWE['cache']['logins']['taobao'];
	}

	public function getInfo()
	{
		global $_FANWE;
		$data['name'] = $this->config['name'];
		$data['short_name'] = $this->config['short_name'];
		$data['is_syn'] = $this->config['is_syn'];
		$data['bind_img'] = SITE_URL.'login/taobao/bind_taobao.png';
		$data['icon_img'] = SITE_URL.'login/taobao/icon_taobao.png';
		$data['login_img'] = SITE_URL.'login/taobao/login_taobao.png';
		$data['login_url'] = SITE_URL."login.php?mod=taobao";
		$data['bind_url'] = SITE_URL."login.php?bind=taobao";
		$data['unbind_url'] = SITE_URL."login.php?unbind=taobao";
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
		$url = FU('tgo',array('url'=>GetTaoBaoLoginUrl($this->config['app_key'])));
		fHeader("location:".$url);
	}

	public function unBind()
	{
		global $_FANWE;
		if($_FANWE['uid'] > 0)
		{
			FDB::delete('user_bind',"uid = ".$_FANWE['uid']." AND type = 'taobao'");
			
			$update = array();
			$update['buyer_level'] = 0;
			$update['seller_level'] = 0;
			$update['is_buyer'] = 0;
			FDB::update('user',$update,'uid = '.$_FANWE['uid']);
		}
		
		$redirect_uri = urlencode($_FANWE['site_url'].substr(FU('settings/bind'),1));
		$url = "https://oauth.taobao.com/logoff?client_id=".$this->config['app_key']."&redirect_uri=".$redirect_uri;
		fHeader("location: ".$url);
	}
}
?>