<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/**
 * integrate.service.php
 *
 * 会员整合服务类
 *
 * @package service
 * @author awfigq <awfigq@qq.com>
 */

function &init_users($integrate_code,$integrate_config) {
	global $_FANWE;
	
	$set_modules = false;
	static $cls = null;
	if ($cls != null) {
		return $cls;
	}
	
	if (empty($integrate_code))
		$integrate_code = $_FANWE['setting']['integrate_code'];
	
	if (empty($integrate_config))
		$integrate_config = $_FANWE['setting']['integrate_config'];
		
	//$code = $_FANWE['setting']['integrate_code'];//a_fanweC('INTEGRATE_CODE');
	//print_r($integrate_config); exit;
	if (empty($integrate_code))
		$integrate_code = 'fanwe';
	include_once (FANWE_ROOT . 'core/include/integrates/' . $integrate_code . '.php');
	$cfg = unserialize($integrate_config);//unserialize($_FANWE['setting']['integrate_config']);
	$cls = new $integrate_code($cfg);

	return $cls;
}

class IntegrateService
{
	
	var $info;
	var $integrate_code;
	var $integrate_config;
	
	/**
	 * 
	 * 初始化参数
	 * @param unknown_type $integrate_code
	 * @param unknown_type $integrate_config
	 */	
	public function adminInit($integrate_code,$integrate_config){
		$this->integrate_code = $integrate_code;
		$this->integrate_config = $integrate_config;
	}	
	
	public function getInfo(){
		return $this->info;
	}
	
	/**
	 * 获取email是否已经存在
	 * @param string $email
	 * @param string $uid 对方系统用户关键字
	 * @return bool
	 */
	public function getCheckEmail($email,$uid=0)
	{
		$users = &init_users($this->integrate_code, $this->integrate_config);
		$result = $users->check_email($email,$uid);
		$this->info = $users->error;
		return $result;
	}

	/**
	 * 检查会员名称是否可用
	 * @param string $user_name 会员名称
	 * @param string $uid 对方系统用户关键字
	 * @return bool true,可用；flase不可用
	 */
	public function getCheckUserName($user_name,$uid=0)
	{
		$users = &init_users($this->integrate_code, $this->integrate_config);
		$result = $users->check_user($user_name,$uid);
		$this->info = $users->error;
		return $result;
	}

	/**
	 * 
	 * 编辑用户口令或email
	 * @param integer $uid 对方系统用户关键字
	 * @param string $new_pwd
	 * @param string $new_email
	 * @return bool true成功；false失败
	 */
	
	public function editUser($uid, $new_pwd, $new_email, $new_user_name){		
		$users = &init_users($this->integrate_code, $this->integrate_config);		
		$result = $users->edit_user($uid, $new_pwd, $new_email, $new_user_name);
		$this->info = $users->error;
		return $result;		
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
	public function addUser($username, $pwd, $email){
		$users = &init_users($this->integrate_code, $this->integrate_config);
		$result = $users->add_user($username, $pwd, $email);
		$this->info = $users->error;
		return $result;
	}

	public function delUser($uid){
		$users = &init_users($this->integrate_code, $this->integrate_config);
		$result = $users->del_user($uid);
		$this->info = $users->error;
		return $result;
	}
		
	/**
	 * 
	 * 用户异步退出系统，返回退出js代码,供前台浏览器调用
	 */
	public function synLogout(){
		$users = &init_users($this->integrate_code, $this->integrate_config);
		$result = $users->logout();
		$this->info = $users->error;
		return $result;
	}	
	
	
	/**
	*
	* 用户异步登陆系统，返回退出js代码,供前台浏览器调用
	*/
	public function synLogin($uid){
		$users = &init_users($this->integrate_code, $this->integrate_config);
		$result = $users->synlogin($uid);
		$this->info = $users->error;
		return $result;
	}
		
	//
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $user_name
	 * @param unknown_type $user_pwd
	 * @param int $integrate_id 第三方的数据ID
	 *     	$user = array(
    				'integrate_id' => $uid,
    				'email' => $email,
    				'user_name' => $uname,    				
    				'password'  => $passwordmd5,
    				'info'  => $this->error,
    	);
	 */
	public function getUser($user_name_or_email,$user_pwd, $integrate_id = 0){
		//int $isuid isuid 0:username; 1:uid; 2:email
		if ($integrate_id > 0){
			$isuid = 1;
		}else{
			if (FS('Validate')->email($user_name_or_email)){
				$isuid = 2;
			}else{
				$isuid = 0;
			}
		}
		
		//$isuid = 0;
		$users = &init_users($this->integrate_code, $this->integrate_config);
		$result = $users->getUser($user_name_or_email,$user_pwd,$isuid, $integrate_id);
		$this->info = $users->error;
		return $result;		
	}
	
	/**
	 * 
	 * 将第三方的数据，同步到本地数据库中
	 * @param string $user_name_or_email
	 * @param string $password
	 * @param int $gender 性别
	 * @param array $user 第三方的数据集，如果不为空的话，则直接使用这个数据集，如果为空的话，则调用：$this->getUser
	 *         	$user = array(
  	    				'integrate_id' => $uid,
   	    				'email' => $email,
   	    				'user_name' => $uname,    				
   	    				'password'  => md5(time().rand(100000, 999999)),
        	); 
	 *  @return int >0 为：本系统的用户ID; <= 0 出错
	 */
	public function addUserToLoacl($user_name_or_email,$password,$gender = 1, $user = null){
		global $_FANWE;
		$uid = 0;
		
		$password = preg_match('/^\w{32}$/', $password) ? $password : md5($password);
		
		$user_field = $_FANWE['setting']['integrate_field_id'];
		if (empty($user)){
			$user_info = $this->getUser($user_name_or_email,$password, 0);
			//print_r($user_info); exit;
		}else{
			$user_info = $user;
		}	
		
		$integrate_id = intval($user_info['integrate_id']);
		if ($integrate_id > 0){
		
			$sql = "SELECT uid FROM ".FDB::table('user')." WHERE {$user_field} = '$integrate_id'";
			$uid = intval(FDB::resultFirst($sql));
		
			if ($uid == 0){
				if (FS('Validate')->email($user_name_or_email)){
					$sql = "SELECT uid FROM ".FDB::table('user')." WHERE email = '{$user_info['email']}'";
				}else{
					$sql = "SELECT uid FROM ".FDB::table('user')." WHERE user_name = '{$user_info['user_name']}'";
				}
				$uid = intval(FDB::resultFirst($sql));
			}
		
			if ($uid > 0){
				//更新数据
				if ($user_field != 'uid'){
					$sql = "UPDATE ".FDB::table('user')." set {$user_field} = '$integrate_id', password = '{$password}' where uid = '{$uid}'";
				}else{
					$sql = "UPDATE ".FDB::table('user')." set password = '{$password}' where uid = '{$uid}'";
				}
				FDB::query($sql);
			}else{
				//添加用户数据
				$user = array(
						'email' => $user_info['email'],
						'user_name' => $user_info['user_name'],
						'user_name_match'=>segmentToUnicode($user_info['user_name']),
						'password'  => $password,
						'status'    => 1,
						'email_status' => 0,
						'avatar_status' => 0,
						'gid' => 7,
						'reg_time' => TIME_UTC,
						$user_field => $integrate_id,
				);
					
				$uid = FDB::insert('user',$user,true);
				if($uid > 0)
				{
					unset($user);
					FDB::insert('user_count',array('uid' => $uid));
						
					$user_profile = array(
											'uid' => $uid,
											'gender' => $gender, 
					);
					FDB::insert('user_profile',$user_profile);
					unset($user_profile);
						
					$user_status = array(
											'uid' => $uid,
											'reg_ip' => $_FANWE['client_ip'],
											'last_ip' => $_FANWE['client_ip'],
											'last_time' => TIME_UTC,
											'last_activity' => TIME_UTC,
					);
					FDB::insert('user_status',$user_status);
				}
				
				return $uid;
			}
		}			
		
		return $uid;
	}
}
?>