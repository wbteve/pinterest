<?php
class SettingsModule
{
	public function personal()
	{
		global $_FANWE;
        $msg = $_FANWE['cookie']['save_personal'];
		fSetCookie('save_personal','');
		include template('page/settings/settings_personal');
		display();
	}

	public function savePersonal()
	{
		global $_FANWE;

		$user = array(
			'gender'           => intval($_FANWE['request']['gender']),
			'reside_province'  => intval($_FANWE['request']['province']),
			'reside_city'      => intval($_FANWE['request']['city']),
			'weibo'            => trim($_FANWE['request']['weibo']),
			'introduce'        => trim($_FANWE['request']['introduce']),
		);
		
		$vservice = FS('Validate');
		
		$user_name = trim($_FANWE['request']['user_name']);
		$email = trim($_FANWE['request']['email']);
				
		if($user_name != $_FANWE['user']['user_name'])
		{
			$data = array(
				'user_name' => $user_name,
				'user_name_match'=>segmentToUnicode($user_name),
			);

			if($_FANWE['user']['edit_name_count'] == 0)
			{
								
				$validate = array(
					array('user_name','required',lang('user','register_user_name_require')),
					array('user_name','range_length',lang('user','register_user_name_len'),2,20),
					array('user_name','/^[\x{4e00}-\x{9fa5}a-zA-Z][\x{4e00}-\x{9fa5}a-zA-Z0-9_]+$/u',lang('user','register_user_name_error')),
				);

				if($vservice->validation($validate,$data))
				{
					if(FS('User')->getUserNameExists($data['user_name']))
					{
						$msg	= lang('user','register_user_name_exist');
					}
					else
					{
						//==================添加同步用户名修改 chenfq 2011-10-15================
						$user_field = $_FANWE['setting']['integrate_field_id'];
						$sql = "SELECT {$user_field} FROM ".FDB::table('user')." WHERE uid = '{$_FANWE['uid']}'";
						$integrate_id = intval(FDB::resultFirst($sql));
						if ($integrate_id > 0 && !FS("Integrate")->getCheckUserName($user_name,$integrate_id)){
							$msg = FS("Integrate")->getInfo();
						}else{
							if ($integrate_id > 0 ){
								FS("Integrate")->editUser($integrate_id, '','',$user_name);
							}

							FDB::update('user',$data,'uid = '.$_FANWE['uid']);
							FDB::query('UPDATE '.FDB::table('user_status').' SET edit_name_count = edit_name_count + 1 WHERE uid = '.$_FANWE['uid']);								
						}
						//==================添加同步用户名修改 chenfq 2011-10-15================					
					}
				}
				else
				{
					$msg = $vservice->getError();
				}
			}
		}
		
		if(empty($msg) && !empty($email) && empty($_FANWE['user']['email']))
		{
			$data = array(
				'email' => $email
			);
			
			$validate = array(
				array('email','required',lang('user','register_email_require')),
				array('email','email',lang('user','register_email_error')),
			);
			
			if($vservice->validation($validate,$data))
			{
				if(FS('User')->getEmailExists($data['email']))
				{
					$msg = lang('user','register_email_exist');
				}
				else
				{
					FDB::update('user',$data,'uid = '.$_FANWE['uid']);
				}
			}
			else
			{
				$msg = $vservice->getError();
			}
		}
		
		if(empty($msg)){
			FDB::update('user_profile',$user,'uid = '.$_FANWE['uid']);
			FS('User')->deleteUserCache($_FANWE['uid']);
			fSetCookie('save_personal','');
		}else
			fSetCookie('save_personal',$msg);
		
		fHeader("location: ".FU('settings/personal'));
	}

	public function avatar()
	{
		global $_FANWE;
		include template('page/settings/settings_avatar');
		display();
	}

	public function saveAvatar()
	{
		global $_FANWE;
		if(FS("Image")->getIsServer())
		{
			$avartar_info = unserialize(authcode($_FANWE['request']['avatar_info'],'DECODE'));
			if(!empty($avartar_info))
			{
				$server = FS("Image")->getServer($avartar_info['server_code']);
				if(!empty($server))
				{
					$args = array();
					$args['pic_url'] = $avartar_info['path'];
					$args['types'] = array(
						'small' =>'32',
						'middle'=>'64',
						'big'   =>'160',
					);

					$server = FS("Image")->getImageUrlToken($args,$server,1);
					$body = FS("Image")->sendRequest($server,'saveavatar',true);
					if(!empty($body))
					{
						$avatar = unserialize($body);
						FS("Image")->setServerUploadCount($avatar['server_code']);
						FS('User')->updateAvatar($_FANWE['uid'],$avatar['server_code']);
					}
				}
			}
		}
		else
		{
			$avatar_url = trim($_FANWE['request']['user_avartar']);
			if(!empty($avatar_url) && file_exists(FANWE_ROOT.$avatar_url))
			{
				FS('User')->saveAvatar($_FANWE['uid'],FANWE_ROOT.$avatar_url);
			}
		}
		fHeader("location: ".FU('settings/avatar'));
	}

	public function password()
	{
		global $_FANWE;
        $sync_password = $_FANWE['cookie']['sync_password'];
        if(!empty($sync_password))
        {
            $sync_password = authcode($sync_password, 'DECODE');
            $msg = sprintf(lang('settings','setting_tip_1'),$_FANWE['setting']['site_name'],$sync_password);
        }
        include template('page/settings/settings_password');
		display();
	}

	public function savePassword()
	{
		global $_FANWE;
		$data = array(
			'oldpassword'       => $_FANWE['request']['oldpassword'],
			'newpassword'       => $_FANWE['request']['newpassword'],
			'confirm_password'  => $_FANWE['request']['newpasswordagain'],
		);

		$vservice = FS('Validate');
		$validate = array(
			array('oldpassword','required','请输入现在的密码'),
			array('newpassword','range_length','请输入正确的新密码',6,32),
			array('confirm_password','equal','确认密码不一致',$data['newpassword']),
		);

		if(!$vservice->validation($validate,$data))
		{
			$msg = $vservice->getError();
		}
		else
		{
			if(md5($data['oldpassword']) != $_FANWE['user']['password'])
			{
				$msg = '原密码不正确';
			}
			else
			{
				FDB::update('user',array('password'=>md5($data['newpassword'])),'uid = '.$_FANWE['uid']);
				$msg = '修改成功';
                fSetCookie('sync_password','');
				$user = array(
					'uid'=>$_FANWE['uid'],
					'password'=>md5($data['newpassword']),
				);

				FS('User')->setSession($user);
				
				//==================添加同步密码修改 chenfq 2011-10-15================
				$user_field = $_FANWE['setting']['integrate_field_id'];
				$sql = "SELECT {$user_field} FROM ".FDB::table('user')." WHERE uid = '{$_FANWE['uid']}'";
				$integrate_id = intval(FDB::resultFirst($sql));
				//echo $integrate_id."<br>";
				if ($integrate_id > 0 ){
					FS("Integrate")->editUser($integrate_id, $data['newpassword'],'','');
					//echo  FS("Integrate")->getInfo();
				}
				//==================添加同步密码修改 chenfq 2011-10-15================
			}
		}

		include template('page/settings/settings_password');
		display();
	}

	public function bind()
	{
		global $_FANWE;
		
		$login_modules = getLoginModuleList();
		$bind_list = FS('User')->getUserBindList($_FANWE['uid']);
		
		$sync_bind_exists = $_FANWE['cookie']['sync_bind_exists'];
        if(!empty($sync_bind_exists))
        {
            $sync_bind_exists = unserialize(authcode($sync_bind_exists, 'DECODE'));
            $bind_exists = sprintf(lang('settings','setting_tip_2'),$sync_bind_exists['short_name'],$_FANWE['setting']['site_name'],$sync_bind_exists['short_name'],$_FANWE['setting']['site_name']);
        }

		include template('page/settings/settings_bind');
		display();
	}

	public function setsyn()
	{
		global $_FANWE;
		$uid = $_FANWE['uid'];
		$data = array();
		$sync = array();
		$sync['weibo'] = (int)$_FANWE['request']['syn_weibo'];
		$sync['topic'] = (int)$_FANWE['request']['syn_topic'];
		$sync['medal'] = 1;
		$data['sync'] = serialize($sync);
		$type = $_FANWE['request']['cls'];
		FDB::update('user_bind',$data,"uid = $uid AND type = '$type'");
		
	}
	
	public function buyerVerifier()
	{
		global $_FANWE;
		$taobao_user = FS("User")->getUserBindByType($_FANWE['uid'],'taobao');
		include template('page/settings/settings_buyerverifier');
		display();
	}
	
	public function bindBuyerVerifier()
	{
		global $_FANWE;
		require_once FANWE_ROOT."sdks/taobao/taobao.func.php";
		
		$uid = $_FANWE['uid'];
		$data['is_buyer'] = 0;
		FDB::update('user',$data,"uid = $uid");
		
		FanweService::instance()->cache->loadCache('logins');
		$url = GetTaoBaoLoginUrl($_FANWE['cache']['logins']['taobao']['app_key']);
		$url = FU('tgo',array('url'=>$url));
		
		fSetCookie('callback_type','buyer');
		fHeader("location:".$url);
	}
	
	public function unBuyerverifier()
	{
		global $_FANWE;
		$uid = $_FANWE['uid'];
		$data['is_buyer'] = -1;
		FDB::update('user',$data,"uid = $uid");
		fHeader("location: ".FU('settings/buyerverifier'));
	}
}
?>