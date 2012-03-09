<?php

if (__FILE__ == '')
{
    die('Fatal error code: 0');
}

/* 取得当前fanwe所在的根目录 */
define('ROOT_PATH', str_replace('api', '', str_replace('\\', '/', dirname(__FILE__))));
require ROOT_PATH.'core/fanwe.php';
$fanwe = &FanweService::instance();
$fanwe->initialize();

init_constant();

	/**
	 * 初始化UC常量
	 */
	function init_constant()
	{
		global $_FANWE;
		
		$integrate_config = $_FANWE['setting']['integrate_config'];
		
	    //$sql = "select val from ".DB_PREFIX."sys_conf where name = 'INTEGRATE_CONFIG' limit 1";
	    
	    //print_r($sql);
	    
	    $cfg = unserialize($integrate_config);
	    
	    //print_r($cfg);
	    
        define('UC_CONNECT', isset($cfg['uc_connect'])?$cfg['uc_connect']:'');
        define('UC_DBHOST', isset($cfg['db_host'])?$cfg['db_host']:'');
        define('UC_DBUSER', isset($cfg['db_user'])?$cfg['db_user']:'');
        define('UC_DBPW', isset($cfg['db_pass'])?$cfg['db_pass']:'');
        define('UC_DBNAME', isset($cfg['db_name'])?$cfg['db_name']:'');
        define('UC_DBCHARSET', isset($cfg['db_charset'])?$cfg['db_charset']:'utf8');
        define('UC_DBTABLEPRE', $cfg['db_pre']);
        define('UC_DBCONNECT', '0');
        define('UC_KEY', isset($cfg['uc_key'])?$cfg['uc_key']:'');
        define('UC_API', isset($cfg['uc_url'])?$cfg['uc_url']:'');
        define('UC_CHARSET', isset($cfg['uc_charset'])?$cfg['uc_charset']:'');
        define('UC_IP', isset($cfg['uc_ip'])?$cfg['uc_ip']:'');
        define('UC_APPID', isset($cfg['uc_id'])?$cfg['uc_id']:'');
        define('UC_PPP', '20');
	}
	

?>