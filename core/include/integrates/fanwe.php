<?php

/* 模块的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
	$i = (isset($modules)) ? count($modules) : 0;

	/* 会员数据整合插件的代码必须和文件名保持一致 */
	$modules[$i]['code']    = 'fanwe';
	$modules[$i]['user_field']    = 'uid';
	/* 被整合的第三方程序的名称 */
	$modules[$i]['name']    = 'FANWE';

	/* 被整合的第三方程序的版本 */
	$modules[$i]['version'] = '2.0';

	/* 插件的作者 */
	$modules[$i]['author']  = 'FANWE R&D TEAM';

	/* 插件作者的官方网站 */
	$modules[$i]['website'] = 'http://www.fanwe.com';

	return;
}

class fanwe
{
    /*------------------------------------------------------ */
    //-- PUBLIC ATTRIBUTEs
    /*------------------------------------------------------ */

    var $error          = 0;

    var $dbcfg = null;
    /*------------------------------------------------------ */
    //-- PRIVATE ATTRIBUTEs
    /*------------------------------------------------------ */
    
	function __construct($cfg){
		
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
         return 0;
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
    	//$sqladd = $uid !== 0 ? "AND uid<>'$uid'" : '';
    	//$user_name = FDB::fetchFirst("SELECT user_name FROM  ".FDB::table('user')." WHERE user_name='$username' $sqladd"); 
    	//$this->error = $username.':应用户名已经被其它用户占用';
    	//return !empty($user_name);
    	return true;
    }

    /**
     *  用户登录函数
     *
     * @access  public
     * @param   string  $username
     * @param   string  $password
     *
     * @return void
     */
    function synlogin($uid)
    {
    	return '';    	
    } 
   	
    //isuid 0:username; 1:uid; 2:email
    public function getUser($username,$user_pwd,$isuid = 0){
    	$user = array(
    	    				'integrate_id' => 0,
    	    				'email' => '',
    	    				'user_name' => '',    				
    	    				'password'  => '',
    	    				'info'  => '',
    	);
    	return $user;    	
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
    	//$sqladd = $uid !== 0 ? "AND uid<>'$uid'" : '';
    	//$email = FDB::fetchFirst("SELECT email FROM  ".FDB::table('user')." WHERE email='$email' $sqladd");    	
    	//return !empty($email);   

    	return true;
    }   
     
	/**
     * 编辑用户
     *
     * @access  public
     * @param
     *
     * @return void
     */
   	function edit_user($username, $newpw='', $newemail='')
    {
        return true;
    }

    //删除用户
    function del_user($uid){
    	return 0;
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
        return '';
    }
        

	
}

?>