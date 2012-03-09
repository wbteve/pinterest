<?php
$db_config = require 'db.global.php';

/**
 * 数据库主服务器设置, 支持多组服务器设置, 当设置多组服务器时, 则会根据分布式策略使用某个服务器
 * @example
 * $config['db']['1']['dbhost']    = 'localhost'; // 服务器地址
 * $config['db']['1']['dbuser']    = 'root'; // 用户
 * $config['db']['1']['dbpwd']     = 'root';// 密码
 * $config['db']['1']['dbname']    = 'db1';// 数据库
 * $config['db']['1']['tablepre']  = 'fanwe_';// 表名前缀
 * $config['db']['1']['dbcharset'] = 'utf8';// 字符集
 * $config['db']['1']['pconnect']  = '0';// 是否持续连接
 
 * $config['db']['2']['dbhost'] = 'localhost';
 * ...
 */
$config['db'][1]['dbhost']    = $db_config['DB_HOST'].':'.$db_config['DB_PORT'];
$config['db'][1]['dbuser']    = $db_config['DB_USER'];
$config['db'][1]['dbpwd'] 	  = $db_config['DB_PWD'];
$config['db'][1]['dbname']    = $db_config['DB_NAME'];
$config['db'][1]['tablepre']  = $db_config['DB_PREFIX'];
$config['db'][1]['dbcharset'] = 'utf8';
$config['db'][1]['pconnect']  = 0;
unset($db_config);

/**
 * 数据库从服务器设置( slave, 只读 ), 支持多组服务器设置, 当设置多组服务器时, 系统每次随机使用
 * @example
 * $config['db']['slave']['1']['dbhost']    = 'localhost';
 * $config['db']['slave']['1']['dbuser']    = 'root';
 * $config['db']['slave']['1']['dbpwd']     = 'root';
 * $config['db']['slave']['1']['dbname']    = 'db1';
 * $config['db']['slave']['1']['tablepre']  = 'fanwe_';
 * $config['db']['slave']['1']['dbcharset'] = 'utf8';
 * $config['db']['slave']['1']['pconnect']  = '0';
 * 
 * $config['db']['slave']['2']['dbhost'] = 'localhost';
 * ...
 */
$config['db']['slave'] = array();

/**
 * 数据库 分布部署策略设置
 *
 * @example 将 member 部署到第二服务器, goods 部署在第三服务器, 则设置为
 * $config['db']['map']['member'] = 2;
 * $config['db']['map']['goods'] = 3;
 *
 * 对于没有明确声明服务器的表, 则一律默认部署在第一服务器上
 *
 */
$config['db']['map'] = array();

/**
 * 数据库 公共设置, 此类设置通常对针对每个部署的服务器
 */
$config['db']['common'] = array();

/**
 *  禁用从数据库的数据表, 表名字之间使用逗号分割
 *
 * @example member,goods 这两个表仅从主服务器读写, 不使用从服务器
 * $config['db']['common']['slave_except_table'] = 'member,goods';
 *
 */
$config['db']['common']['slave_except_table'] = '';

/**
 * 内存服务器优化设置
 * 以下设置需要PHP扩展组件支持，其中 memcache 优先于其他设置，
 * 当 memcache 无法启用时，会自动开启另外的两种优化模式
 */

//内存变量前缀, 可更改,避免同服务器中的程序引用错乱
$config['memory']['prefix'] = '3PSQ3Z_';
$config['memory']['eaccelerator'] = 1;					// 启动对 eaccelerator 的支持
$config['memory']['apc'] = 1;							// 启动对 apc 的支持
$config['memory']['xcache'] = 0;						// 启动对 xcache 的支持
$config['memory']['memcache']['server'] = '';			// memcache 服务器地址
$config['memory']['memcache']['port'] = 11211;			// memcache 服务器端口
$config['memory']['memcache']['pconnect'] = 1;			// memcache 是否长久连接
$config['memory']['memcache']['timeout'] = 1;			// memcache 服务器连接超时

/**CDN设置
 * @example
 * $config['cdn']['image'] = 'http://img.shopshare.com/'; // 图片
 * $config['cdn']['css']   = 'http://css.shopshare.com/'; // CSS
 * $config['cdn']['js']    = 'http://js.shopshare.com/';  // JS
 */
$config['cdn']['image'] = '';
$config['cdn']['css'] = '';
$config['cdn']['js'] = '';

//服务器时区
$config['time_zone'] = '8';

//模板语言
$config['default_lang'] = 'zh-cn';

// 页面输出设置
$config['output']['charset'] 			= 'utf-8';	// 页面字符集
$config['output']['forceheader']		= 1;		// 强制输出页面字符集，用于避免某些环境乱码
$config['output']['gzip'] 			    = 0;		// 是否采用 Gzip 压缩输出
$config['output']['tpl_refresh'] 		= 1;		// 模板自动刷新开关 0=关闭, 1=打开

// COOKIE 设置
$config['cookie']['cookie_pre'] = '49z8_'; 	// COOKIE前缀
$config['cookie']['cookie_domain'] = ''; 		// COOKIE作用域
$config['cookie']['cookie_path']   = '/'; 		// COOKIE作用路径

// 站点安全设置
$config['security']['authkey'] = '8b5e5fmh8eL0CUR4';	// 站点加密密钥
$config['security']['url_xss_defend']	= true;		// 自身 URL XSS 防御
$config['security']['attack_evasive']	= 1;		// CC 攻击防御 1|2|4

// CC 攻击防御忽略操作
$config['security']['attack_ignore'] = array(
	'misc/verify',
);

$config['security']['query']['status']	= 1;		// 是否开启SQL安全检测，可自动预防SQL注入攻击
$config['security']['query']['function']	= array('load_file','hex','substring','ord','char');
$config['security']['query']['action']	= array('intooutfile','intodumpfile','unionselect');
$config['security']['query']['note']	= array('/*','*/','#','--','"');
$config['security']['query']['likehex']	= 1;
$config['security']['query']['fullnote']	= 1;


?>