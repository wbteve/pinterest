<?php
if (!defined('THINK_PATH')) exit();
if(file_exists(FANWE_ROOT.'./public/db.global.php'))
	$db_config	=	require FANWE_ROOT.'./public/db.global.php';

$array=array(
	'APP_AUTOLOAD_PATH'     => '@.ORG.,Think.Util.',// __autoLoad 机制额外检测路径设置,注意搜索顺序
	'URL_MODEL'	=>	0,
	'DIRS_CHECK'	=> array(
		//该系统需要检测的文件夹权限
		'public/',  		//公共目录
		'public/db.global.php',
		'install/runtime/',   //后台编译缓存目录
        'logo.gif',
        'foot_logo.gif',
        'link_logo.gif',
		'rewrite/',
		'rewrite/httpd.ini',
		'rewrite/httpd.parse.errors',
	),
	'DB_CHECK'	=> array(
		//该系统需要检测的文件夹权限
		'CREATE TEMPORARY TABLES',
		'ALTER ROUTINE',
		'CREATE ROUTINE',
		'EXECUTE',
	),
	'FUNCTiON_CHECK'	=> array(
		'mysql_connect',
		'fsockopen',
		'gethostbyname',
		'file_get_contents',
		'xml_parser_create',
		'mb_strlen',
        'curl_exec',
	),
	'FROM_ITEMS' => array(
		'dbinfo' => array
		(
			'DB_HOST' => array('type' => 'text','required' => 1, 'reg' => '/^.+$/', 'value' => 'localhost','name'=>'数据库主机名/IP','error'=>0,'notice'=>'数据库主机名, 一般为 localhost','msg'=>'数据库主机名为空，或者格式错误，请检查'),
			'DB_NAME' => array('type' => 'text','required' => 1, 'reg' => '/^.+$/', 'value' => '','name'=>'数据库名','error'=>0,'notice'=>'','msg'=>'数据库名为空，或者格式错误，请检查'),
			'DB_USER' => array('type' => 'text','required' => 1, 'reg' => '/^.+$/', 'value' => 'root','name'=>'数据库用户名','error'=>0,'notice'=>'','msg'=>'数据库用户名为空，或者格式错误，请检查'),
			'DB_PWD' => array('type' => 'text','required' => 0, 'reg' => '/^.*$/', 'value' => '','name'=>'数据库密码','error'=>0,'notice'=>'','msg'=>''),
			'DB_PORT' => array('type' => 'text','required' => 1, 'reg' => '/^\d+$/', 'value' => '3306','name'=>'端口号','error'=>0,'notice'=>'','msg'=>'端口号为空，或者格式错误，请检查'),
			'DB_PREFIX' => array('type' => 'text','required' => 1, 'reg' => '/^[a-z0-9_]+$/', 'value' => 'fanwe_','name'=>'表前缀','error'=>0,'notice'=>'同一数据库运行多个程序时，请确保前缀为唯一，安装时将会覆盖相同名称的数据表','msg'=>'表前缀为空，或者格式错误，请检查'),
		),
		'admin' => array
		(
			'ADM_NAME' => array('type' => 'text','required' => 1, 'reg' => '/^.+$/', 'value' => 'fanwe','name'=>'管理员账号','error'=>0,'notice'=>'','msg'=>'管理员账号为空，或者格式错误，请检查'),
			'ADM_PWD' => array('type' => 'password','required' => 1, 'reg' => '/^.+$/', 'value' => 'fanwe','name'=>'管理员密码','error'=>0,'notice'=>'默认密码为fanwe','msg'=>'管理员密码为空，请检查'),
			'ADM_PWD2' => array('type' => 'password','required' => 1, 'reg' => '/^.+$/', 'value' => 'fanwe','name'=>'管理员重复密码','error'=>0,'notice'=>'','msg'=>'两次密码不一致，请检查'),
		)
	)
);
if(is_array($db_config))
    $config = array_merge($array,$db_config);
else
    $config = $array;

return $config;
?>