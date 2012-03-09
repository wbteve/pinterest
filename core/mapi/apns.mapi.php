<?php
class apnsMapi
{
	public function run()
	{
		global $_FANWE;	
		
		$root = array();
		$root['return'] = 1;
		
		$email = $_FANWE['requestData']['email'];
		$pwd = $_FANWE['requestData']['pwd'];
		$uid = intval(FDB::resultFirst("select uid from ".FDB::table("user")." where user_name='".$email."' and password = '".$pwd."'"));

		$appname = addslashes(trim($_FANWE['requestData']['appname']));
		$appversion = addslashes(trim($_FANWE['requestData']['appversion']));
		$deviceuid = addslashes(trim($_FANWE['requestData']['deviceuid']));
		$devicetoken = addslashes(trim($_FANWE['requestData']['devicetoken']));
		$devicename = addslashes(trim($_FANWE['requestData']['devicename']));
		$devicemodel = addslashes(trim($_FANWE['requestData']['devicemodel']));
		$deviceversion = addslashes(trim($_FANWE['requestData']['deviceversion']));
		$pushbadge = addslashes(trim($_FANWE['requestData']['pushbadge']));
		$pushalert = addslashes(trim($_FANWE['requestData']['pushalert']));
		$pushsound = addslashes(trim($_FANWE['requestData']['pushsound']));
		$clientid = $uid;
	
		$root['info'] = '';
		if(strlen($appname)==0) $root['info'] = 'Application Name must not be blank.';
		else if(strlen($appversion)==0) $root['info'] = 'Application Version must not be blank.';
		else if(strlen($deviceuid)>40) $root['info'] = 'Device ID may not be more than 40 characters in length.';
		else if(strlen($devicetoken)!=64) $root['info'] = 'Device Token must be 64 characters in length.';
		else if(strlen($devicename)==0) $root['info'] = 'Device Name must not be blank.';
		else if(strlen($devicemodel)==0) $root['info'] = 'Device Model must not be blank.';
		else if(strlen($deviceversion)==0) $root['info'] = 'Device Version must not be blank.';
		else if($pushbadge!='disabled' && $pushbadge!='enabled') $root['info'] = 'Push Badge must be either Enabled or Disabled.';
		else if($pushalert!='disabled' && $pushalert!='enabled') $root['info'] = 'Push Alert must be either Enabled or Disabled.';
		else if($pushsound!='disabled' && $pushsound!='enabled') $root['info'] = 'Push Sount must be either Enabled or Disabled.';

		// store device for push notifications
		if ($root['info'] == ''){
			$sql = "INSERT INTO ".FDB::table("apns_devices")."
					VALUES (
						NULL,
						'{$clientid}',
						'{$appname}',
						'{$appversion}',
						'{$deviceuid}',
						'{$devicetoken}',
						'{$devicename}',
						'{$devicemodel}',
						'{$deviceversion}',
						'{$pushbadge}',
						'{$pushalert}',
						'{$pushsound}',
						'production',
						'active',
						NOW(),
						NOW()
					)
					ON DUPLICATE KEY UPDATE
					`devicetoken`='{$devicetoken}',
					`devicename`='{$devicename}',
					`devicemodel`='{$devicemodel}',
					`deviceversion`='{$deviceversion}',
					`pushbadge`='{$pushbadge}',
					`pushalert`='{$pushalert}',
					`pushsound`='{$pushsound}',
					`status`='active',
					`modified`=NOW();";
			FDB::query($sql);
			$root['info'] = '注册成功';
			$root['return'] = 1;
		}else{
			$root['return'] = 0;
		}
		
		m_display($root);
	}
}
?>