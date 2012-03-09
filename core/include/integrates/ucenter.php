<?php

/**
 * UCenter 会员数据处理类
 */


/* 模块的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = (isset($modules)) ? count($modules) : 0;

    /* 会员数据整合插件的代码必须和文件名保持一致 */
    $modules[$i]['code']    = 'ucenter';

    $modules[$i]['user_field']    = 'ucenter_id';
    
    /* 被整合的第三方程序的名称 */
    $modules[$i]['name']    = 'UCenter';

    /* 被整合的第三方程序的版本 */
    $modules[$i]['version'] = '1.5.x';

    /* 插件的作者 */
    $modules[$i]['author']  = 'FANWE R&D TEAM';

    /* 插件作者的官方网站 */
    $modules[$i]['website'] = 'http://www.fanwe.com';

    /* 插件的初始的默认值 */
    $modules[$i]['default']['db_host'] = 'localhost';
    $modules[$i]['default']['db_user'] = 'root';
    $modules[$i]['default']['prefix'] = 'uc_';
    $modules[$i]['default']['cookie_prefix'] = 'xnW_';

    return;
}

class ucenter
{
    /*------------------------------------------------------ */
    //-- PUBLIC ATTRIBUTEs
    /*------------------------------------------------------ */

    /* 数据库所使用编码 */
    var $charset        = '';

    var $error          = 0;

    var $dbcfg = null;
    /*------------------------------------------------------ */
    //-- PRIVATE ATTRIBUTEs
    /*------------------------------------------------------ */

    /**
     * 构造函数
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function __construct($cfg)
    {
        /* 使用默认数据库连接 */
        //$this->ucenter($cfg);
        //$cfg['quiet'] = 1;
        //parent::integrate($cfg);
        
       	$this->dbcfg = $cfg;
        $this->charset = strtolower(isset($cfg['db_charset'])?$cfg['db_charset']:'utf8');
        
        /* 初始化UC需要常量 */
        if (!defined('UC_CONNECT') && isset($cfg['uc_id']) && isset($cfg['db_host']) && isset($cfg['db_user']) && isset($cfg['db_name']))
        {
            if(strpos($cfg['db_pre'], '`' . $cfg['db_name'] . '`') === 0)
            {
                $db_pre = $cfg['db_pre'];
            }
            else
            {
                $db_pre = '`' . $cfg['db_name'] . '`.' . $cfg['db_pre'];
            }
            
			//dump($cfg);
            define('UC_CONNECT', isset($cfg['uc_connect'])?$cfg['uc_connect']:'');
            define('UC_DBHOST', isset($cfg['db_host'])?$cfg['db_host']:'');
            define('UC_DBUSER', isset($cfg['db_user'])?$cfg['db_user']:'');
            define('UC_DBPW', isset($cfg['db_pass'])?$cfg['db_pass']:'');
            define('UC_DBNAME', isset($cfg['db_name'])?$cfg['db_name']:'');
            define('UC_DBCHARSET', isset($cfg['db_charset'])?$cfg['db_charset']:'utf8');
            define('UC_DBTABLEPRE', $db_pre);
            define('UC_DBCONNECT', '0');
            define('UC_KEY', isset($cfg['uc_key'])?$cfg['uc_key']:'');
            define('UC_API', isset($cfg['uc_url'])?$cfg['uc_url']:'');
            define('UC_CHARSET', isset($cfg['uc_charset'])?$cfg['uc_charset']:'');
            define('UC_IP', isset($cfg['uc_ip'])?$cfg['uc_ip']:'');
            define('UC_APPID', isset($cfg['uc_id'])?$cfg['uc_id']:'');
            define('UC_PPP', '20');
            
            include_once(FANWE_ROOT . 'uc_client/client.php');
        }        
    }

    //isuid 0:username; 1:uid; 2:email
    public function getUser($username,$user_pwd,$isuid = 0, $uid = 0){
    	
    	if (empty($user_pwd) || $user_pwd == ''){
    		$passwordmd5 = $user_pwd;
    	}else{
    		$passwordmd5 = preg_match('/^\w{32}$/', $user_pwd) ? $user_pwd : md5($user_pwd);
    	}
    	    	
    	if ($this->charset == 'gbk'){
    		$username = addslashes(gbToUTF8($username));
    		$passwordmd5 = addslashes(gbToUTF8($passwordmd5));
    	}else{
    		$username = addslashes($username);
    		$passwordmd5 = addslashes($passwordmd5);
    	}
    	if ($uid > 0){
    		//echo '<br>aaa<br>';
    		//include_once(FANWE_ROOT . 'uc_client/client.php');
    		//print_r(uc_get_user($uid,1));
    		list($uid, $uname, $email) = uc_get_user($uid,1);
    		//echo 'uname:'.$uname; exit;
    	}else{
    		list($uid, $uname, $pwd, $email, $repeat) = uc_user_login($username, $passwordmd5, $isuid);
    	}
    	
    	
    	if ($this->charset == 'gbk'){
    		$uname = addslashes(gbToUTF8($uname));
    		$email = addslashes(gbToUTF8($email));
    		$passwordmd5 = addslashes(gbToUTF8($passwordmd5));
    	}else{
    		$uname = addslashes($uname);
    		$passwordmd5 = addslashes($passwordmd5);
    	}
    	
    	if($uid > 0){
    		$this->error = '登陆成功';
    	}elseif($uid == -1)
    	{
    		$this->error = '无效的Email';
    	}
    	elseif ($uid == -2)
    	{
    		$this->error = '密码错误';
    	}
    	else
    	{
    		$this->error = '登陆异常';
    	}
    	    	
    	$user = array(
    				'integrate_id' => $uid,
    				'email' => $email,
    				'user_name' => $uname,    				
    				'password'  => $passwordmd5,
    				'info'  => $this->error,
    	);
    	return $user;
    }
        
    /**
     *  用户登录函数
     *
     * @access  public
     *
     * @return void
     */
    function synlogin($uid)
    {
		$this->checkapps();
    	return uc_user_synlogin($uid);
    }

    /**
     * 用户退出
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function logout()
    {
        return uc_user_synlogout();   //同步退出
    }

    /**
     * 
     * 添加用户
     * @param string $username
     * @param string $password
     * @param string $email
     * 
     * @return int >0 为：第三方系统的用户ID; < 0出错
     * 
     */
    function add_user($username, $password, $email)
    {   
        if ($this->charset == 'gbk'){
        	$username = addslashes(utf8ToGB($username));
        	$password = addslashes(utf8ToGB($password));
        	$email = addslashes(utf8ToGB($email));
	    }else{
        	$username = addslashes($username);
        	$password = addslashes($password);
        	$email = addslashes($email);
	    }
	            
        $uid = uc_user_register($username, $password, $email);
       
        if($uid >0 )
        {
        	$this->error = '添加成功';
        }
        else if($uid == -1)
        {
        	$this->error = '无效的用户名';
        }
        elseif($uid == -2)
        {
        	$this->error = '用户名不允许注册';
        }
        elseif($uid == -3)
        {
        	$this->error = '用户名已经存在';
        }
        elseif($uid == -4)
        {
        	$this->error = '无效的邮箱';
        }
        elseif($uid == -5)
        {
        	$this->error = '邮箱不允许注册';
        }
        elseif($uid == -6)
        {
        	$this->error = '邮箱已经存在';
        }
        else
        {
        	$this->error = '未知错误错码：'.$uid;
        }
        
        return $uid;
    }
    /**
     *  检查指定用户是否存在及密码是否正确
     *
     * @access  public
     * @param   string  $username   用户名
     *
     * @return  boolen true 可用; false不可用
     */
    function check_user($username,$uid = 0)
    {
    	//dump($username);
    	if ($this->charset == 'gbk'){
        	$username = addslashes(utf8ToGB($username));
	    }else{
        	$username = addslashes($username);
	    } 
	        	
        $userdata = uc_user_checkname($username);
        //var_dump($userdata);
        
		if($userdata > 0) {
			$this->error =  '用户名可用';
			return true;
		} elseif($userdata == -1) {
			$this->error =  '用户名不合法';
			return true;
		} elseif($userdata == -2) {
			$this->error =  '包含要允许注册的词语';
			return true;
		} elseif($userdata == -3) {
			//已经被占用
			if ($uid > 0){
				list($uid, $uname, $email2) = uc_get_user($uid,1);//isuid:0:username; 1:uid
				//print_r($status); exit;
				if ($uname == $username){
					//email已经存在,是属于$username这个用户的
					return true;
				}
			}
			if ($this->charset == 'gbk'){
				$username = gbToUTF8($username);
			}
			$this->error = $username.':应用户名已经被其它用户占用';
			return false;
		}
    }

    /**
     * 
     * 检查email是否存在（或被其它用户占用）
     * @param string $email
     * @param string $uid 对方系统用户关键字 (如果$uid，不为空的话，则过滤$uid)
     * 
     * return true; //email不存在(或属于$username用户的)，可用
     * return false;//email存在，或格式不对，不可用
     */
    function check_email($email,$uid = 0){
    	
    	if ($this->charset == 'gbk'){
    		$email = addslashes(utf8ToGB($email));
    		//$username = addslashes(utf8ToGB($username));
    	}else{
    		$email = addslashes($email);
    		//$username = addslashes($username);
    	}  

/*
    	if ($this->charset == 'gbk'){
    		$uname = addslashes(gbToUTF8($uname));
    	}else{
    		$username = addslashes($uname);
    	}
*/    	    	
		$email_exist = uc_user_checkemail($email);
		//echo $email."<br>".$email_exist."<br>".$username;
		if ($email_exist == -4){
			$this->error = '无效的email格式';
			return false;			
		}else if ($email_exist == -5){
			$this->error = '限制注册的email';
			return false;			
		}else if ($email_exist == -6){
			//'email已经存在'
			
			if ($uid > 0){
				list($uid, $uname, $email2) = uc_get_user($uid,1);//isuid:0:username; 1:uid	
				//print_r($status); exit;			
				if ($email2 == $email){
					//email已经存在,是属于$username这个用户的
					return true;
				}				
			}		
			
			if ($this->charset == 'gbk'){
				$email = gbToUTF8($email);
			}			
			$this->error = $email.':邮件已经被其它用户占用';
			return false;			
		}else if ($email_exist == 1){
			//email不存在，可用
			return true;
		}    	
    }
    
    /* 编辑用户密码或email信息
     * 
     */
    function edit_user($uid, $newpw='', $newemail='', $username = '')
    {
    	if ($this->charset == 'gbk'){
        	$username = addslashes(utf8ToGB($username));	 
        	$newpw = addslashes(utf8ToGB($newpw));
        	$email = addslashes(utf8ToGB($email));       
	    }else{
        	$username = addslashes($username);
        	$newpw = addslashes($newpw);
        	$newemail = addslashes($newemail);
	    }    	

	    list($uid, $uname, $email) =  uc_get_user($uid,1);//isuid:0:username; 1:uid
	    $username = $uname;
	    /*
	    
	    if (!empty($username)){
	    	 $ucresult = uc_user_merge($uname,$username,$uid,'',$email);
	    	 if ($ucresult > 0){
	    	 	$username = $uname;
	    	 }else if($uid == -1) {
	    	 	$this->error = '用户名不合法';
	    	 	return false;
	    	 } elseif($uid == -2) {
	    	 	$this->error = '包含要允许注册的词语';
	    	 	return false;
	    	 } elseif($uid == -3) {
	    	 	$this->error = '用户名已经存在';
	    	 	return false;
	    	 }	    	 
	    }else{
	    	$username = $uname;
	    }
	    
		*/
	    $ucresult = uc_user_edit($username, '', $newpw, $newemail, 1);
	   // echo $ucresult; exit;
	    if ($ucresult == -1){
	    	$this->error = '旧密码不对';
	    	return false;
	    }else if ($ucresult == -4){
	    	$this->error = '无效的email格式';
	    	return false;
	    }else if ($ucresult == -5){
	    	$this->error = '限制注册的email';
	    	return false;
	    }else if ($ucresult == -6){
	    	$this->error = 'email已经存在';
	    	return false;
	    }else if ($ucresult == -7){
	    	$this->error = '没用需要更新的数据';
	    	return false;
	    }else if ($ucresult == -8){
	    	$this->error = '受保护的会员密码';
	    	return false;	    			    	
	    }else if ($ucresult >= 0){
	    	//email不存在，可用
	    	return true;
	    }
	    
    }
    
    //删除用户
    function del_user($uid){
    	return uc_user_delete($uid);
    }
    
	function checkapps()
    { 
		@include(FANWE_ROOT.'uc_client/data/cache/apps.php');
		
		if(count($_CACHE['apps']) == 0)
		{
			$appls = uc_app_ls();
			$cachefile = FANWE_ROOT . 'uc_client/data/cache/apps.php';
			$fp = fopen($cachefile, 'w');
			$s = "<?php\r\n";
			$s .= '$_CACHE[\'apps\'] = '.var_export($appls, TRUE).";\r\n";
			fwrite($fp, $s);
			fclose($fp);
		} 
    }
}

?>