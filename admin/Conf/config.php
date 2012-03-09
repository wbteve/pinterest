<?php
if (!defined('THINK_PATH'))
	exit();
$config	=	require '../public/db.global.php';
$array=array(
	'USER_AUTH_ON'=>true,
	'USER_AUTH_TYPE'		=>1,		        // 默认认证类型 1 登录认证 2 实时认证
	'AUTH_TYPE'             => array('NODE','MODULE','ACTION'), //授权类型的常量
	'USER_AUTH_KEY'			=>'fanwe_shop_share',	        // 用户认证SESSION标记
    'ADMIN_AUTH_KEY'		=>'administrator',
	'USER_AUTH_MODEL'		=>'Admin',	        // 默认验证数据表模型
	'AUTH_PWD_ENCODER'		=>'md5',	        // 用户认证密码加密方式
	'USER_AUTH_GATEWAY'	    =>'?m=Public&a=login',	// 默认认证网关
	'NOT_AUTH_MODULE'		=>'Public,Index',		// 默认无需认证模块
	'REQUIRE_AUTH_MODULE'   =>'',		    // 默认需要认证模块
	'NOT_AUTH_ACTION'		=>'',		    // 默认无需认证操作
	'REQUIRE_AUTH_ACTION'   =>'',		    // 默认需要认证操作
    'GUEST_AUTH_ON'         =>false,       // 是否开启游客授权访问
    'GUEST_AUTH_ID'         => 0,        // 游客的用户ID
	'RBAC_ROLE_TABLE'       =>'role' ,
	'RBAC_USER_TABLE'       =>'admin' ,
	'RBAC_ACCESS_TABLE'     =>'role_access' ,
	'RBAC_NODE_TABLE'       =>'role_node',
	'TOKEN_ON'              =>false , // 是否开启令牌验证
	'TOKEN_NAME'            =>'_fanwe_hash__' , // 令牌验证的表单隐藏字段名称
	'TOKEN_TYPE'            =>'md5' , //令牌哈希验证规则默认为MD5
	'TMPL_CACHE_ON'         =>false , // 是否开启模板编译缓存,设为false则每次都会重新编译
	'SHOW_RUN_TIME'         =>false,	    // 运行时间显示
	'SHOW_ADV_TIME'         =>false,		// 显示详细的运行时间
	'SHOW_DB_TIMES'         =>false,		// 显示数据库查询和写入次数
	'SHOW_CACHE_TIMES'      =>false,		// 显示缓存操作次数
	'SHOW_USE_MEM'          =>false,		// 显示内存开销
	'PAGE_ROLLPAGE'         =>5 , // 分页显示页数
	'PAGE_LISTROWS'         =>20 , // 分页每页显示记录数
	'URL_MODEL'             =>0,
    'DB_LIKE_FIELDS'        =>'title|remark',
	'RBAC_ROLE_TABLE'       =>'role',
	'RBAC_USER_TABLE'	    =>'admin',
	'RBAC_ACCESS_TABLE'     =>'role_access',
	'RBAC_NODE_TABLE'	    =>'role_node',
	'APP_AUTOLOAD_PATH'     =>'@.ORG.,Think.Util.',
	'DEFAULT_THEME'         =>'Default',
	'TMPL_ACTION_ERROR'     =>'Public:error',
	'DEFAULT_LANG'          =>'zh-cn',
	//记录日志操作的模块与操作
	'LOG_APP' => array(
		'RoleNode' => array('insert' , 'update' , 'remove' , 'editfield' , 'togglestatus') , //权限节点的增删改日志记录
		'SysConf' => array('update') , //修改系统设置
	),
);
return array_merge($config,$array);
?>