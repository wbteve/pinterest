<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/**
 * fanwe.service
 *
 * 全局服务类
 *
 * @package service
 * @author awfigq <awfigq@qq.com>
 */

define('IN_FANWE', true);
error_reporting(E_ERROR);

class FanweService
{
	public $db = NULL;
	public $cache = NULL;
	public $session = NULL;
	public $memory = NULL;
	public $is_init = false;
	public $is_memory = true;
	public $is_session = true;
	public $is_admin = false;
	public $is_user = true;
	public $is_cron = true;
	public $is_setting = true;
	public $is_misc = true;
	public $is_group_city = false;
	public $config = array();
	public $var = array();
	public $cache_list = array('goods_category','image_servers','links','navs');

	public $allow_global = array(
		'GLOBALS' => 1,
		'_GET' => 1,
		'_POST' => 1,
		'_REQUEST' => 1,
		'_COOKIE' => 1,
		'_SERVER' => 1,
		'_ENV' => 1,
		'_FILES' => 1,
	);


	public function &instance()
	{
		static $_instance = NULL;
		if($_instance === NULL)
			$_instance = new FanweService();
		return $_instance;
	}

	public function FanweService()
	{
		if(phpversion() < '5.3.0')
			set_magic_quotes_runtime(0);
		
		if(!defined('FANWE_ROOT'))
			define('FANWE_ROOT', str_replace('\\', '/',substr(dirname(__FILE__), 0, -12)));

		if(!file_exists(FANWE_ROOT.'./public/install.lock'))
		{
			header('Location: install/index.php');
			exit;
		}
		
		define('MAGIC_QUOTES_GPC', function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc());
		define('ICONV_ENABLE', function_exists('iconv'));
		define('MB_ENABLE', function_exists('mb_convert_encoding'));
		define('EXT_OBGZIP', function_exists('ob_gzhandler'));
		define('TIMESTAMP', time());

		if(!include(FANWE_ROOT.'./core/function/global.func.php'))
		{
			exit('not found global.func.php');
		}
		
		@require(FANWE_ROOT.'./public/constant.global.php');
		require fimport("function/time");

		define('IS_ROBOT', checkRobot());

		if(function_exists('ini_get'))
		{
			$memory_limit = @ini_get('memory_limit');
			if($memory_limit && getBytes($memory_limit) < 33554432 && function_exists('ini_set'))
			{
				ini_set('memory_limit', '128M');
			}
		}
			
		if(!$this->is_admin)
		{
			foreach ($GLOBALS as $key => $value)
			{
				if (!isset($this->allow_global[$key]))
				{
					$GLOBALS[$key] = NULL;
					unset($GLOBALS[$key]);
				}
			}
		}

		global $_FANWE;
		$_FANWE = array();
		$_FANWE['uid'] = 0;
		$_FANWE['user_name'] = '';
		$_FANWE['gid'] = 0;
		$_FANWE['sid'] = '';
		$_FANWE['form_hash'] = '';
		$_FANWE['client_ip'] = getFClientIp();
		$_FANWE['referer'] = '';

		$_FANWE['php_self'] = htmlspecialchars(getPhpSelf());
		if($_FANWE['php_self'] === false)
			systemError('request_tainting');
		
		$_FANWE['module_name'] = MODULE_NAME;
		$_FANWE['module_filename'] = basename($_FANWE['php_self']);
		$_FANWE['site_url'] = '';
		$_FANWE['site_root'] = '';
		$_FANWE['site_port'] = '';

		$_FANWE['config'] = array();
		$_FANWE['setting'] = array();
		$_FANWE['user'] = array();
		$_FANWE['group'] = array();
		$_FANWE['cookie'] = array();
		$_FANWE['cache'] = array();
		$_FANWE['session'] = array();
		$_FANWE['lang'] = array();
		$_FANWE['tpl_user_formats'] = array();

		$site_path = substr($_FANWE['php_self'], 0, strrpos($_FANWE['php_self'], '/'));
		$_FANWE['site_url'] = htmlspecialchars('http://'.$_SERVER['HTTP_HOST'].$site_path.'/');

		$url = parse_url($_FANWE['site_url']);
		$_FANWE['site_root'] = isset($url['path']) ? $url['path'] : '';
		$_FANWE['site_port'] = empty($_SERVER['SERVER_PORT']) || $_SERVER['SERVER_PORT'] == '80' ? '' : ':'.$_SERVER['SERVER_PORT'];

		if(defined('SUB_DIR'))
		{
			$_FANWE['site_url'] = str_replace(SUB_DIR, '', $_FANWE['site_url']);
			$_FANWE['site_root'] = str_replace(SUB_DIR, '', $_FANWE['site_root']);
		}
		
		define('PUBLIC_ROOT', FANWE_ROOT.'./public/');
		define('PUBLIC_PATH', $_FANWE['site_root'].'public/');
		
		define('SITE_URL', $_FANWE['site_root']);

		require fimport("class/cache");
		$this->cache = Cache::getInstance();

		$this->var = &$_FANWE;

		$this->buildConfig();
		$this->buildInput();
		$this->buildOutput();
	}

	public function initialize()
	{
		if(!$this->is_init)
		{
			$this->buildDb();
			$this->buildMemory();
			$this->buildSetting();
			$this->buildSession();
			//$this->buildCron();
			$this->buildCache();
			$this->buildUser();
			//$this->buildRewriteArgs();
			$this->buildMisc();
		}
		
		$this->is_init = true;

		define('TPL_PATH', $this->var['site_root'].'tpl/'.$this->var['setting']['site_tmpl'].'/');
		define('TMPL', $this->var['setting']['site_tmpl']);
		@include(FANWE_ROOT.'./tpl/'.$this->var['setting']['site_tmpl'].'/functions.php');

		if($this->var['setting']['shop_closed'] == 1 && !$this->is_admin)
		{
			showError(lang('common','site_close'),lang('common','site_close_content'),'',0,true);
		}
	}

	private function buildConfig()
	{
		$config = array();
		@include FANWE_ROOT.'./public/config.global.php';
		if(empty($config))
		{
			if(!file_exists(FANWE_ROOT.'./public/install.lock'))
			{
				header('Location: install');
				exit;
			}
			else
			{
				systemError('config_not_found');
			}
		}

		if(empty($config['security']['authkey']))
		{
			$config['security']['authkey'] = md5($config['cookie']['cookie_pre'].$config['db'][1]['dbname']);
		}

		if(empty($config['debug']) || !file_exists(fimport('function/debug')))
		{
			define('SYS_DEBUG', false);
		}
		elseif($config['debug'] === 1 || $config['debug'] === 2 || !empty($_REQUEST['debug']) && $_REQUEST['debug'] === $config['debug'])
		{
			define('SYS_DEBUG', true);
			if($config['debug'] == 2)
				error_reporting(E_ALL);
		}
		else
		{
			define('SYS_DEBUG', false);
		}

		timezoneSet($config['time_zone']);
		define('TIME_UTC', fGmtTime());

		$this->config = & $config;
		$this->var['config'] = & $config;

		if(substr($config['cookie']['cookie_path'], 0, 1) != '/')
			$this->var['config']['cookie']['cookie_path'] = '/'.$this->var['config']['cookie']['cookie_path'];

		$this->var['config']['cookie']['cookie_pre'] = $this->var['config']['cookie']['cookie_pre'].substr(md5($this->var['config']['cookie']['cookie_path'].'|'.$this->var['config']['cookie']['cookie_domain']), 0, 4).'_';
	}

	private function buildInput()
	{
		if (isset($_GET['GLOBALS']) || isset($_POST['GLOBALS']) || isset($_COOKIE['GLOBALS']) || isset($_FILES['GLOBALS']))
		{
			systemError('request_tainting');
		}

		if(!MAGIC_QUOTES_GPC && !$this->is_admin)
		{
			$_GET = fAddslashes($_GET);
			$_POST = fAddslashes($_POST);
			$_COOKIE = fAddslashes($_COOKIE);
			$_FILES = fAddslashes($_FILES);
		}

		$pre_length = strlen($this->config['cookie']['cookie_pre']);
		foreach($_COOKIE as $key => $val)
		{
			if(substr($key, 0, $pre_length) == $this->config['cookie']['cookie_pre'])
			{
				$this->var['cookie'][substr($key, $pre_length)] = $val;
			}
		}

		if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST))
			$_GET = array_merge($_GET, $_POST);

		foreach($_GET as $k => $v)
		{
			$this->var['request'][$k] = $v;
		}
		
		$this->var['isajax'] = empty($this->var['request']['isajax']) ? 0 : 1;
		$this->var['page'] = empty($this->var['request']['page']) ? 1 : max(1, intval($this->var['request']['page']));
		$this->var['sid'] = $this->var['cookie']['sid'] = isset($this->var['cookie']['sid']) ? htmlspecialchars($this->var['cookie']['sid']) : '';
		if(empty($this->var['cookie']['saltkey']))
		{
			$this->var['cookie']['saltkey'] = random(8);
			fSetCookie('saltkey', $this->var['cookie']['saltkey'], 86400 * 30, 1, 1);
		}
		$this->var['authkey'] = md5($this->var['config']['security']['authkey'].$this->var['cookie']['saltkey']);
	}

	private function buildOutput()
	{
		if($this->config['security']['url_xss_defend'] && $_SERVER['REQUEST_METHOD'] == 'GET' && !empty($_SERVER['REQUEST_URI']))
		{
			$this->_xssCheck();
		}
		
		$attack_evasive = true;
		if(!empty($this->var['cookie']['from_header']))
		{
			$from_header_time = (int)authcode($this->var['cookie']['from_header'], 'DECODE');
			$attack_evasive = (TIME_UTC - $from_header_time < 10) ? false : true;
			fSetCookie('from_header','');
		}
		
		/*$module_action = strtolower(MODULE_NAME.'/'.ACTION_NAME);
		if($this->config['security']['attack_evasive'] && $attack_evasive && !in_array($module_action, $this->config['security']['attack_ignore']))
		{
			require_once fimport('include/security');
		}*/

		if(!empty($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') === false)
		{
			$this->config['output']['gzip'] = false;
		}

		$allow_gzip = $this->config['output']['gzip'] && empty($this->var['ajax']) && EXT_OBGZIP;
		$this->config['gzip_compress'] = $allow_gzip;
		ob_start($allow_gzip ? 'ob_gzhandler' : NULL);
		$this->config['charset'] = $this->config['output']['charset'];
		define('CHARSET', $this->config['output']['charset']);
		if($this->config['output']['forceheader'])
			@header('Content-Type: text/html; charset='.CHARSET);
	}

	private function buildDb()
	{
		require fimport('class/db');
		require fimport('class/mysql');
		$class = 'FDbMySql';
		if(count($this->var['config']['db']['slave']))
		{
			require fimport('class/mysqlslave');
			$class = 'FDbMysqlSlave';
		}

		$this->db = &FDB::object($class);
		$this->db->setConfig($this->config['db']);
		$this->db->connect();
	}

	private function buildMemory()
	{
		require fimport('class/memory');
		$this->memory = new Memory();
		if($this->is_memory)
		{
			$this->memory->init($this->config['memory']);
		}
		$this->var['memory'] = $this->memory->type;
	}

	private function buildSession()
	{
		if($this->is_session)
		{
			require fimport('class/session');
			$this->session = new Session();
			$this->session->init($this->var['cookie']['sid'], $this->var['client_ip'], $this->var['uid']);
			$this->var['sid'] = $this->session->sid;
			$this->var['session'] = $this->session->var;

			if($this->var['sid'] != $this->var['cookie']['sid'])
			{
				fSetCookie('sid', $this->var['sid'], 86400);
			}

			if($this->session->is_new)
			{
				if(ipBanned($this->var['client_ip']))
					$this->session->set('gid', 6);
			}

			/*if($this->session->get('gid') == 6)
			{
				$this->var['user']['gid'] = 6;
				systemError('user_banned');
			}*/

			if($this->var['uid'] && ($this->session->isnew || ($this->session->get('last_activity') + 600) < TIME_UTC))
			{
				$this->session->set('last_activity', TIME_UTC);
				if($this->session->is_new)
				{
					//FDB::update('user_status', array('last_ip' => $this->var['client_ip'], 'last_visit' => TIME_UTC), "uid='".$this->var['uid']."'");
				}
			}
		}
	}

	public function buildUser($uid)
	{
		if($this->is_user)
		{
			if($auth = $this->var['cookie']['auth'])
			{
				$auth = fAddslashes(explode("\t", authcode($auth, 'DECODE')));
			}
			
			list($password, $uid) = empty($auth) || count($auth) < 2 ? array('','') : $auth;

			if($uid)
			{
				$user = FS('user')->getUserById($uid);
			}
			
			if(!empty($user) && $user['password'] == $password)
			{
				$this->var['user'] = $user;
				$this->var['authoritys'] = FS('User')->getAuthoritys($uid);
				FS('User')->init($user);
			}
			else
			{
				$this->buildGuest();
			}
		}
		else
		{
			$this->buildGuest();
		}
		if(empty($this->var['cookie']['last_visit']))
		{
			$this->var['user']['last_visit'] = TIME_UTC - 3600;
			fSetCookie('last_visit', TIME_UTC - 3600, 86400 * 30);
		}
		else
		{
			$this->var['user']['last_visit'] = $this->var['cookie']['last_visit'];
		}
		
		$this->var['uid'] = $this->var['user']['uid'];
		$this->var['user_name'] = addslashes($this->var['user']['user_name']);
		$this->var['gid'] = $this->var['user']['gid'];
		FS('User')->setReferrals();
	}

	private function buildGuest()
	{
		$this->var['user'] = array( 'uid' => 0, 'user_name' => '', 'email' => '', 'gid' => 6);
	}

	private function buildCron()
	{
		if($this->is_cron)
		{
			require fimport("class/cron");
			Cron::run();
		}
	}

	private function buildMisc()
	{
		if(!$this->is_misc)
			return false;

		$this->var['form_hash'] = formHash();
		define('FORM_HASH', $this->var['form_hash']);

		if($this->init_user)
		{
			if($this->var['user']['status'] == -1)
			{
				systemError('user_banned',null);
			}
		}

		if($this->var['setting']['ip_access'] && !ipAccess($this->var['client_ip'], $this->var['setting']['ip_access']))
		{
			systemError('user_banned', null);
		}

		if($this->var['setting']['nocacheheaders'])
		{
			@header("Expires: -1");
			@header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
			@header("Pragma: no-cache");
		}
	}

	private function buildSetting()
	{
		if($this->is_setting)
			$this->cache->loadCache('setting');

		if(!is_array($this->var['setting']))
			$this->var['setting'] = array();
	}

	private function buildCache()
	{
		!empty($this->cache_list) && $this->cache->loadCache($this->cache_list);
	}

	private function buildRewriteArgs()
	{
		if(intval($this->var['setting']['url_route']) > 0)
		{
			switch(MODULE_NAME.'/'.ACTION_NAME)
			{
				case 'index/index':
				case 'index/search':
				case 'index/today':
				case 'index/custom':
					getRewriteArgs(array('cat','city_py','sort','prices','keyword','page'));
				break;

				case 'goods/index':
				case 'goods/search':
					getRewriteArgs(array('site','cat','date','city_py','sort','prices','keyword','page'));
				break;
			}
		}
	}

	private function _xssCheck()
	{
		$temp = strtoupper(urldecode(urldecode($_SERVER['REQUEST_URI'])));
		if(strpos($temp, '<') !== false || strpos($temp, '"') !== false || strpos($temp, 'CONTENT-TRANSFER-ENCODING') !== false)
		{
			systemError('request_tainting');
		}
		return true;
	}
}
?>