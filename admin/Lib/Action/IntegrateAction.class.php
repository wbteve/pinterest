<?php
// +----------------------------------------------------------------------
// | Fanwe 多语商城建站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 
// +----------------------------------------------------------------------

class IntegrateAction extends CommonAction{
	/**
	 * 获得所有模块的名称以及链接地址
	 *
	 * @access      public
	 * @param       string      $directory      插件存放的目录
	 * @return      array
	 */
	function read_modules($directory = '.')
	{
	    $dir         = @opendir($directory);
	    $set_modules = true;
	    $modules     = array();
	
	    while (false !== ($file = @readdir($dir)))
	    {
	        if (preg_match("/^.*?\.php$/", $file))
	        {	        	
	            include_once($directory. '/' .$file);
	        }
	    }
	    @closedir($dir);
	    unset($set_modules);
	
	    foreach ($modules AS $key => $value)
	    {
	        ksort($modules[$key]);
	    }
	    ksort($modules);
	
	    return $modules;
	}
	
	/**
	 *  返回字符集列表数组
	 *
	 * @access  public
	 * @param
	 *
	 * @return void
	 */
	function get_charset_list()
	{
	    return array(
	        'utf8'   => 'UTF-8',
	    	'gbk' => 'GB2312/GBK',
	        'big5'   => 'BIG5',
	    );
	}
	
	/**
	 * 文件或目录权限检查函数
	 *
	 * @access          public
	 * @param           string  $file_path   文件路径
	 * @param           bool    $rename_prv  是否在检查修改权限时检查执行rename()函数的权限
	 *
	 * @return          int     返回值的取值范围为{0 <= x <= 15}，每个值表示的含义可由四位二进制数组合推出。
	 *                          返回值在二进制计数法中，四位由高到低分别代表
	 *                          可执行rename()函数权限、可对文件追加内容权限、可写入文件权限、可读取文件权限。
	 */
	function file_mode_info($file_path)
	{
	    /* 如果不存在，则不可读、不可写、不可改 */
	    if (!file_exists($file_path))
	    {
	        return false;
	    }
	
	    $mark = 0;
	
	    if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')
	    {
	        /* 测试文件 */
	        $test_file = $file_path . '/cf_test.txt';
	
	        /* 如果是目录 */
	        if (is_dir($file_path))
	        {
	            /* 检查目录是否可读 */
	            $dir = @opendir($file_path);
	            if ($dir === false)
	            {
	                return $mark; //如果目录打开失败，直接返回目录不可修改、不可写、不可读
	            }
	            if (@readdir($dir) !== false)
	            {
	                $mark ^= 1; //目录可读 001，目录不可读 000
	            }
	            @closedir($dir);
	
	            /* 检查目录是否可写 */
	            $fp = @fopen($test_file, 'wb');
	            if ($fp === false)
	            {
	                return $mark; //如果目录中的文件创建失败，返回不可写。
	            }
	            if (@fwrite($fp, 'directory access testing.') !== false)
	            {
	                $mark ^= 2; //目录可写可读011，目录可写不可读 010
	            }
	            @fclose($fp);
	
	            @unlink($test_file);
	
	            /* 检查目录是否可修改 */
	            $fp = @fopen($test_file, 'ab+');
	            if ($fp === false)
	            {
	                return $mark;
	            }
	            if (@fwrite($fp, "modify test.\r\n") !== false)
	            {
	                $mark ^= 4;
	            }
	            @fclose($fp);
	
	            /* 检查目录下是否有执行rename()函数的权限 */
	            if (@rename($test_file, $test_file) !== false)
	            {
	                $mark ^= 8;
	            }
	            @unlink($test_file);
	        }
	        /* 如果是文件 */
	        elseif (is_file($file_path))
	        {
	            /* 以读方式打开 */
	            $fp = @fopen($file_path, 'rb');
	            if ($fp)
	            {
	                $mark ^= 1; //可读 001
	            }
	            @fclose($fp);
	
	            /* 试着修改文件 */
	            $fp = @fopen($file_path, 'ab+');
	            if ($fp && @fwrite($fp, '') !== false)
	            {
	                $mark ^= 6; //可修改可写可读 111，不可修改可写可读011...
	            }
	            @fclose($fp);
	
	            /* 检查目录下是否有执行rename()函数的权限 */
	            if (@rename($test_file, $test_file) !== false)
	            {
	                $mark ^= 8;
	            }
	        }
	    }
	    else
	    {
	        if (@is_readable($file_path))
	        {
	            $mark ^= 1;
	        }
	
	        if (@is_writable($file_path))
	        {
	            $mark ^= 14;
	        }
	    }
	
	    return $mark;
	}	
	
	
	/**
	 *
	 *
	 * @access  public
	 * @param
	 *
	 * @return void
	 */
	function save_integrate_config ($code, $user_field, $cfg)
	{
	    $sql = "SELECT COUNT(*) as number FROM ".C("DB_PREFIX")."sys_conf WHERE name = 'INTEGRATE_CODE'";
		$number = M()->query($sql);
		
	    if (intval($number[0]['number']) == 0)
	    {
	        $sql = "INSERT INTO ".C("DB_PREFIX")."sys_conf(name, is_show, status, val) VALUES ('INTEGRATE_CODE', 0, 1,'$code')";
	    }
	    else
	    {
	        $sql = "SELECT val FROM ".C("DB_PREFIX")."sys_conf WHERE name = 'INTEGRATE_CODE'";
	        $tmp = M()->query($sql);
	        $sql = "UPDATE ".C("DB_PREFIX")."sys_conf SET val = '$code' WHERE name = 'INTEGRATE_CODE'";
	    }
	
	    M()->query($sql);
	    
	    /* 当前的域名 */
	    if (isset($_SERVER['HTTP_X_FORWARDED_HOST']))
	    {
	        $cur_domain = $_SERVER['HTTP_X_FORWARDED_HOST'];
	    }
	    elseif (isset($_SERVER['HTTP_HOST']))
	    {
	        $cur_domain = $_SERVER['HTTP_HOST'];
	    }
	    else
	    {
	        if (isset($_SERVER['SERVER_NAME']))
	        {
	            $cur_domain = $_SERVER['SERVER_NAME'];
	        }
	        elseif (isset($_SERVER['SERVER_ADDR']))
	        {
	            $cur_domain = $_SERVER['SERVER_ADDR'];
	        }
	    }
	
	    /* 整合对象的域名 */
	    $int_domain = str_replace(array('http://', 'https://'), array('', ''), $cfg['integrate_url']);
	    if (strrpos($int_domain, '/'))
	    {
	        $int_domain = substr($int_domain, 0, strrpos($int_domain, '/'));
	    }
	
	    if ($cur_domain != $int_domain)
	    {
	        $same_domain    = true;
	        $domain         = '';
	
	        /* 域名不一样，检查是否在同一域下 */
	        $cur_domain_arr = explode(".", $cur_domain);
	        $int_domain_arr = explode(".", $int_domain);
	
	        if (count($cur_domain_arr) != count($int_domain_arr) || $cur_domain_arr[0] == '' || $int_domain_arr[0] == '')
	        {
	            /* 域名结构不相同 */
	            $same_domain = false;
	        }
	        else
	        {
	            /* 域名结构一致，检查除第一节以外的其他部分是否相同 */
	            $count = count($cur_domain_arr);
	
	            for ($i = 1; $i < $count; $i++)
	            {
	                if ($cur_domain_arr[$i] != $int_domain_arr[$i])
	                {
	                    $domain         = '';
	                    $same_domain    = false;
	                    break;
	                }
	                else
	                {
	                    $domain .= ".$cur_domain_arr[$i]";
	                }
	            }
	        }
	
	        if ($same_domain == false)
	        {
	            /* 不在同一域，设置提示信息 */
	            $cfg['cookie_domain']   = '';
	            $cfg['cookie_path']     = '/';
	        }
	        else
	        {
	            $cfg['cookie_domain']   = $domain;
	            $cfg['cookie_path']     = '/';
	        }
	    }
	    else
	    {
	        $cfg['cookie_domain']   = '';
	        $cfg['cookie_path']     = '/';
	    }
	
	    
	    $sql = "SELECT COUNT(*) as number FROM ".C("DB_PREFIX")."sys_conf WHERE name = 'INTEGRATE_CONFIG'";
	    $number = M()->query($sql);
	    if (intval($number[0]['number']) == 0)
	    {
	        $sql =  "INSERT INTO ".C("DB_PREFIX")."sys_conf (name, is_show, status, val) ".
	                "VALUES ('INTEGRATE_CONFIG', 0, 1, '" . serialize($cfg) . "')";
	    }
	    else
	    {
	        $sql = "UPDATE ".C("DB_PREFIX")."sys_conf SET val='". serialize($cfg) ."' ".
	                "WHERE name='INTEGRATE_CONFIG'";
	    }
	
	    M()->query($sql);
	    
	    
	    $sql = "SELECT COUNT(*) as number FROM ".C("DB_PREFIX")."sys_conf WHERE name = 'INTEGRATE_FIELD_ID'";
	    $number = M()->query($sql);
	    if (intval($number[0]['number']) == 0)
	    {
	    	$sql =  "INSERT INTO ".C("DB_PREFIX")."sys_conf (name, is_show, status, val) ".
	    	                "VALUES ('INTEGRATE_FIELD_ID', 0, 1, '" . $user_field . "')";
	    }
	    else
	    {
	    	$sql = "UPDATE ".C("DB_PREFIX")."sys_conf SET val='". $user_field ."' ".
	    	                "WHERE name='INTEGRATE_FIELD_ID'";
	    }
	    
	    M()->query($sql);	    
	    return true;
	}	
	
	public function index() {
		//echo FANWE_ROOT.'core/include/integrates';
		$modules = $this->read_modules(FANWE_ROOT.'core/include/integrates');
		//dump($modules);exit;
		$code = fanweC('INTEGRATE_CODE');
		//dump($code);
		
	    for ($i = 0; $i < count($modules); $i++)
	    {
	         $modules[$i]['installed'] = ($modules[$i]['code'] == $code) ? 1 : 0;
	    }
	
	    $allow_set_points = fanweC('INTEGRATE_CODE') == 'fanwe' ? 0 : 1;
	
	    $allow_set_points = 0;
	    $this->assign('allow_set_points',  $allow_set_points);
	    $this->assign('modules', $modules);
		
	    //dump($modules);
		
	    //assign_query_info();
	    //$this->display('integrates_list.htm');
		$this->display ();
	}

	/*------------------------------------------------------ */
	//-- 安装会员数据整合插件
	/*------------------------------------------------------ */
	public function install()
	{
	    /* 增加ucenter设置时先检测uc_client与uc_client/data是否可写 */
	    if ($_GET['code'] == 'ucenter')
	    {
	        $uc_client_dir = $this->file_mode_info(FANWE_ROOT. 'uc_client/data');
	        if ($uc_client_dir === false)
	        {
	            $this->error('uc_client目录不存在，请先把uc_client目录上传到程序目录下再进行整合');
	            //return;
	        }
	        if ($uc_client_dir < 7)
	        {
	            $this->error ('uc_client/data目录不可写，请先把uc_client/data目录权限设置为777');
	            //return;
	        }
	        //eval()
	        
	        $a = M()->query('select user_name from '.C("DB_PREFIX").'user group by user_name having count(*) > 1');
	        if (!empty($a)){
	        	$this->error ('会员用户名不唯一，无法进行整合.<br>'.M('User')->getLastSql());
	        }
	    }
	    
	    if ($_GET['code'] == 'fanwe')
	    {
	        D("SysConf")->where("status=1 and name='INTEGRATE_CODE'")->setField("val", "fanwe");
	        D("SysConf")->where("status=1 and name='INTEGRATE_FIELD_ID'")->setField("val", "uid");
	        D("SysConf")->where("status=1 and name='POINTS_RULE'")->setField("val", "");
	        //clear_cache_files();
	        
			clearCache();
		
	        $this->assign ('jumpUrl', u('Integrate/index'));
	        $this->success ( '设置会员数据整合插件已经成功。');
	        return;
	    }
	    else
	    {
	    	//D("User")->where("sync_flag > 0")->setField(array("sync_flag","nickname"), array("0",""));
	        
	        $set_modules = true;
	        include_once(FANWE_ROOT.'core/include/integrates/'.$_GET['code'].".php");
	        $set_modules = false;
	
            $cfg = $modules[0]['default'];
            $cfg['integrate_url'] = "http://";
            
            if (empty($cfg['db_charset']))
            	$cfg['db_charset'] = 'UTF-8';
	        
            $this->assign('cfg',      $cfg);
	        $this->assign('save',     0);
	      	$this->assign('set_list', $this->get_charset_list());
	        $this->assign('code',     $_GET['code']);
	        $this->assign('user_field', $modules[0]['user_field']);
	        
	        //dump($this->get_charset_list());
	        $this->display("install");
	    }
	}


	/*------------------------------------------------------ */
	//-- 保存UCenter设置
	/*------------------------------------------------------ */
	public function setup_ucenter()
	{
		require_once(FANWE_ROOT.'core/class/transport.class.php');
	    $result = array('status' => 0, 'info' => '', 'data' => '');
	 
	 
	    $app_type   = 'OTHER';
	    $app_name   = 'FANWE';//fanweC('SHOP_NAME'); //$db->getOne('SELECT value FROM ' . $ecs->table('shop_config') . " WHERE code = 'shop_name'");
	    $app_url    = 'http://'.$_SERVER['HTTP_HOST'].__ROOT__;
	    $app_charset = 'UTF-8';
	    $app_dbcharset = strtolower((str_replace('-', '', 'UTF-8')));
		$ucapi = trim($_REQUEST['uc_url']);
		    
	    $ucfounderpw = trim($_REQUEST['uc_pass']);
	    $postdata ="m=app&a=add&ucfounder=&ucfounderpw=".urlencode($ucfounderpw)."&apptype=".urlencode($app_type).
	        "&appname=".urlencode($app_name)."&appurl=".urlencode($app_url)."&appip=&appcharset=".$app_charset.
	        '&appdbcharset='.$app_dbcharset;
	    $t = new transport;
	    
	    $ucconfig = $t->request($ucapi.'/index.php', $postdata);
	   // dump($ucconfig);exit;
	    $ucconfig = $ucconfig['body'];
	    if(empty($ucconfig))
	    {
	        //ucenter 验证失败
	        $result['error'] = 1;
	        $result['info'] = '验证失败:'.$ucapi.'/index.php?'.$postdata;
	
	    }
	    elseif($ucconfig == '-1')
	    {
	        //管理员密码无效
	        $result['error'] = 1;
	        $result['info'] = '创始人密码错误';
	    }
	    else
	    {
	        list($appauthkey, $appid) = explode('|', $ucconfig);
	        if(empty($appauthkey) || empty($appid))
	        {
	            //ucenter 安装数据错误
	            $result['error'] = 1;
	            $result['info'] = '安装数据错误:'.$ucconfig;
	        }
	        else
	        {
	            $result['error'] = 0;
	            $result['data'] = $ucconfig;
	            $result['info'] = '服务器通信连接成功！';
	        }
	    }
		return $result; 
	}	

	/*------------------------------------------------------ */
	//-- 第一次保存UCenter安装的资料
	/*------------------------------------------------------ */
	public function save_uc_config_first()
	{
		
		
		$result = $this->setup_ucenter();
		if ($result['error'] == 1)
			$this->error($result['info']);
		
		
		$ucconfig = $result['data'];
		$user_field = $_REQUEST['user_field'];
	    $code = $_REQUEST['code'];
	    //echo $ucconfig; exit;
	    list($appauthkey, $appid, $ucdbhost, $ucdbname, $ucdbuser, $ucdbpw, $ucdbcharset, $uctablepre, $uccharset, $ucapi, $ucip) = explode('|', $ucconfig);
	    $uc_url = !empty($ucapi)? $ucapi : trim($_REQUEST['uc_url']);
	    $cfg = array(
	                    'uc_id' => $appid,
	                    'uc_key' => $appauthkey,
	                    'uc_url' => $uc_url,
	                    'uc_ip' => '',
	                    'uc_connect' => 'post',
	                    'uc_charset' => $uccharset,
	                    'db_host' => $ucdbhost,
	                    'db_user' => $ucdbuser,
	                    'db_name' => $ucdbname,
	                    'db_pass' => $ucdbpw,
	                    'db_pre' => $uctablepre,
	                    'db_charset' => strtolower($ucdbcharset),
	                );
	    /* 增加UC语言项 */
	    //$cfg['uc_lang'] = $_LANG['uc_lang'];
	
	    /* 检测成功临时保存论坛配置参数 */
	    $_SESSION['cfg'] = $cfg;
	    $_SESSION['code'] = $code;
	
	    /* 直接保存修改 */
	    if ($_POST['save'] == 1)
	    {
	        if ($this->save_integrate_config($code, $user_field, $cfg))
	        {
	            $this->assign ('jumpUrl', U('Integrate/index'));
	            $this->success ('保存成功!');
	        }
	        else
	        {
	            $this->assign ('jumpUrl', U('Integrate/index'));
	            $this->error('保存出错!');
	        }
	    }
	
	    /* 保存完成整合 */
	    $this->save_integrate_config($code, $user_field, $cfg);
	
	    //include_once(VENDOR_PATH."mysql.php");
	    //$ucdb = new cls_mysql($cfg['db_host'], $cfg['db_user'], $cfg['db_pass'], $cfg['db_name'], $cfg['db_charset'], null, 1);	    
	    
	    $user_startid_intro = "强烈要求合并会员时，进行数据备份！";
	    
	    $this->assign('user_startid_intro', $user_startid_intro);
	    //$this->assign('user_startid_intro', "方维会员起始ID为".$user_maxid."; UC会员起始ID为".$maxuid."。<br>如原 ID 为 888 的会员将变为 ".$maxuid."+888 的值。");
	    $this->display('uc_import');
	}	
	

	/*------------------------------------------------------ */
	//-- 保存UCenter填写的资料
	/*------------------------------------------------------ */
	public function save_uc_config()
	{
	    $code = $_POST['code'];
	    $user_field = $_POST['user_field'];
	    
	    $cfg = unserialize(fanweC('INTEGRATE_CONFIG'));
	    if ($_POST['cfg']['uc_connect'] == 'mysql'){
	    	 include_once(VENDOR_PATH."integrates/".$code.".php");
		    $_POST['cfg']['quiet'] = 1;
		    $cls_user = new $code ($_POST['cfg']);
		
		    if ($cls_user->error)
		    {
		        if ($cls_user->error == 1)
		        {
		            $this->error('数据库地址、用户或密码不正确');
		        }
		        elseif ($cls_user->error == 2)
		        {
		            $this->error('整合论坛关键数据表不存在，你填写的信息有误');
		        }
		        elseif ($cls_user->error == 1049)
		        {
		            $this->error('数据库不存在');
		        }
		        else
		        {
		            sys_msg($cls_user->db->error());
		        }
		    }	    	
	    }

	
	    /* 合并数组，保存原值 */
	    $cfg = array_merge($cfg, $_POST['cfg']);
	
	    /* 直接保存修改 */
	    if ($this->save_integrate_config($code, $user_field, $cfg))
	    {
	        $this->assign ('jumpUrl', U('Integrate/index'));
	        $this->success('保存成功!');
	    }
	    else
	    {
	        $this->assign ('jumpUrl', U('Integrate/index'));
	        $this->error('保存出错!');
	    }
	}
	
	
	public function import_user()
	{
		//导入前，检查会员名，是否有重复的，有重复的不能执行导入
		ini_set("memory_limit","100M");
		
	    $cfg = unserialize(fanweC('INTEGRATE_CONFIG'));// INTEGRATE_CONFIG $_SESSION['cfg'];
	    
	    //echo VENDOR_PATH."mysql.php"; exit;
	    //include_once(VENDOR_PATH."mysql.php");
	    /*
	    //include_once(__ROOT__."/app/source/class/mysql_db.php");
	    include_once(FANWE_ROOT."/core/class/db.class.php");
	    include_once(FANWE_ROOT."/core/class/mysql.class.php");
	    $db_cfg = array(
	    'DB_HOST'=>$cfg['db_host'],
	    'DB_NAME'=>$cfg['db_name'],
	    'DB_USER'=>$cfg['db_user'],
	    'DB_PWD'=>$cfg['db_pass'],
	    'DB_PORT'=>3306,
	    'DB_PREFIX'=>$cfg['db_pre'],
	    );
	    $class = 'FDbMySql';	     
	    $ucdb = &FDB::object($class);
	    $ucdb->setConfig($db_cfg);
	    $ucdb->connect();
	     */
	    
	    $db_cfg = array(
	    	    'dbhost'=>$cfg['db_host'],
	    	    'dbname'=>$cfg['db_name'],
	    	    'dbuser'=>$cfg['db_user'],
	    	    'dbpwd'=>$cfg['db_pass'],
	    	    'dbcharset'=>$cfg['db_charset'],
	    	    'pconnect'=>'',
	    );	    

	    Vendor('mysql');
	    $ucdb = new mysqldb($db_cfg);
	    //dump($ucdb); exit;
	    Log::record("==================uc会员整合 begin======================");
	
	    
	    $item_list = M()->query("SELECT uid as id,user_name,password as user_pwd, ucenter_id, email, '' as last_ip,reg_time as create_time FROM " . C("DB_PREFIX") . "user ORDER BY `id` ASC");
	    foreach ($item_list AS $data)
	    {
	        $salt = rand(100000, 999999);
	        $password = md5($data['user_pwd'].$salt); //uc口令方式：md5(md5(明文)+随机值)
	        if (strtolower($cfg['db_charset']) == 'gbk'){
	        	$data['username'] = addslashes(utf8ToGB($data['user_name']));
	        }else{
	        	$data['username'] = addslashes($data['user_name']);
	        }
	        
	        $uc_userinfo = $ucdb->fetchFirst("SELECT `uid`, `password`, `salt` FROM ".$cfg['db_pre']."members WHERE `username`='$data[username]'");
	        //dump($uc_userinfo);
	        if(!$uc_userinfo) //用户在uc中，不存在，则直接插入到UC中
	        {
	            $ucdb->query("INSERT INTO ".$cfg['db_pre']."members SET username='$data[username]', password='$password', email='$data[email]', regip='$data[last_ip]', regdate='$data[create_time]', salt='$salt'", 'SILENT');            
	            $lastuid = $ucdb->insertId();
	            $ucdb->query("INSERT INTO ".$cfg['db_pre']."memberfields SET uid='$lastuid'",'SILENT');
	            
	            M()->query("UPDATE " . C("DB_PREFIX") . "user SET `ucenter_id`='" . $lastuid . "' WHERE `uid`='" . $data['id'] . "'");
	            	            
	            
	            //Log::record("INSERT INTO ".$cfg['db_pre']."members SET username='$data[username]', password='$password', email='$data[email]', regip='$data[last_ip]', regdate='$data[create_time]', salt='$salt'");
	            //Log::record("INSERT INTO ".$cfg['db_pre']."memberfields SET uid='$lastuid'");
	            
	            //M()->query("UPDATE " . C("DB_PREFIX") . "user SET `id`= $lastuid "." where id = ".$data['id']);
	        }
	        else
	        {
	        	M()->query("UPDATE " . C("DB_PREFIX") . "user SET `ucenter_id`='" . $uc_userinfo['uid'] . "' WHERE `uid`='" . $data['id'] . "'");
	        	/*
	            if ($merge_method == 1)//1:将与UC用户名和密码相同的用户强制为同一用户
	            {
	                if (md5($data['user_pwd'].$uc_userinfo['salt']) == $uc_userinfo['password'])
	                {
	                    //$merge_uid[] = $data['id'];
	                    $uc_uid[] = array('user_id' => $data['id'],   	//旧会员ID
	                    				  'uid' => $uc_userinfo['uid']	//新会员ID
	                    				  );
	                    continue;
	                }
	            }
	            */
	            $ucdb->query("REPLACE INTO ".$cfg['db_pre']."mergemembers SET appid='".UC_APPID."', username='$data[username]'", 'SILENT');
	            //Log::record("REPLACE INTO ".$cfg['db_pre']."mergemembers SET appid='".UC_APPID."', username='$data[username]'");
	        }
	    }
	    
	    
		//M()->query("UPDATE " . C("DB_PREFIX") . "user SET `ucenter_id`= ucenter_id_tmp");

	    //Log::record("==================uc会员整合 end======================");
	    //Log::save();
	
		clearCache();
		    
		$this->assign ('jumpUrl', u('Integrate/index'));
		$this->success ('成功将会员数据导入到 UCenter');	    
	}	

	public function edit()
	{
	/*
		$set_modules = true;
		include_once(FANWE_ROOT.'core/include/integrates/'.$_GET['code'].".php");
		$set_modules = false;
	*/			
		$user_field = fanweC('INTEGRATE_FIELD_ID');
		
		$this->assign('user_field', $user_field);// $modules[0]['user_field']);
		
		if ($_GET['code'] == 'fanwe')
		{
			$this->assign ('jumpUrl', U('Integrate/index'));
			$this->error('当您采用FANWE会员系统时，无须进行设置。');
		}
		else
		{
			$cfg = unserialize(fanweC('INTEGRATE_CONFIG'));
			$this->assign('save', 1);
			$this->assign('set_list', $this->get_charset_list());
			$this->assign('code',     $_GET['code']);
			$this->assign('cfg',      $cfg);
			//dump($this->get_charset_list());
			$this->display('edit');
		}
	}	
}
?>
