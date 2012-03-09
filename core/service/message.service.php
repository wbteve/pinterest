<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/**
 * message.service.php
 *
 * 信件服务类
 *
 * @package service
 * @author awfigq <awfigq@qq.com>
 */

//没有发送的会员
define('MSG_SEND_NONE_ERROR','-10');
define('MSG_MLIST_NONE_ERROR','-20');
define('MSG_PRIVILEGE_NONE_ERROR','-30');

class MessageService
{
	/**
	 * 获取是否有系统信件
	 * @return void
	 */
	public function sysMsgInit($uid,$gid)
	{
		$count = 0;
		$res = FDB::query('SELECT m.mid 
			FROM '.FDB::table('sys_msg').' AS m 
			LEFT JOIN '.FDB::table('sys_msg_member').' AS mm ON mm.mid = m.mid AND mm.uid = '.$uid.' 
			WHERE mm.mid IS NULL AND (m.end_time = 0 OR m.end_time >= '.TIME_UTC.')');
			
		while($data = FDB::fetch($res))
		{
			$mid = $data['mid'];
			$is_no = FDB::resultFirst('SELECT COUNT(mid) 
				FROM '.FDB::table('sys_msg_user_no')." 
				WHERE mid = '$mid' AND uid = '$uid'");
			
			if($is_no > 0)
				continue;
			
			$is_send = false;
			$is_yes = FDB::resultFirst('SELECT COUNT(mid) 
				FROM '.FDB::table('sys_msg_user_yes')." 
				WHERE mid = '$mid' AND uid = '$uid'");
			
			if($is_yes > 0)
				$is_send = true;
			else
			{
				$is_yes = FDB::resultFirst('SELECT COUNT(mid) 
					FROM '.FDB::table('sys_msg_user_group')." 
					WHERE mid = '$mid' AND gid = '$gid'");
				if($is_yes > 0)
					$is_send = true;
			}
			
			if($is_send)
			{
				$count++;
				$msg = array();
				$msg['mid'] = $mid;
				$msg['uid'] = $uid;
				$msg['dateline'] = TIME_UTC;
				FDB::insert('sys_msg_member',$msg);
			}
		}
		
		if($count > 0)
		{
			$result = FDB::query("INSERT INTO ".FDB::table('user_notice')."(uid, type, num, create_time) VALUES('$uid',5,'$count','".TIME_UTC."')", 'SILENT');
			if(!$result)
				FDB::query("UPDATE ".FDB::table('user_notice')." SET num = num + '$count', create_time='".TIME_UTC."' WHERE uid='$uid' AND type=5");
		}
	}
	
	/**
	 * 获取系统信件列表
	 * @return void
	 */
	public function getSysMsgs($uid)
	{
		$list = FDB::fetchAll('SELECT *  
			FROM '.FDB::table('sys_msg_member').' AS mm 
			LEFT JOIN '.FDB::table('sys_msg').' AS m ON m.mid = mm.mid 
			WHERE mm.status < 2 AND mm.uid = '.$uid.' 
			ORDER BY m.mid DESC');
		
		FDB::query("UPDATE ".FDB::table('sys_msg_member')." SET status = 1 WHERE uid='$uid' AND status = 0");
		return $list;
	}
	
	/**
	 * 获取系统信件
	 * @return void
	 */
	public function getSysMsgByMid($uid,$mid)
	{
		return FDB::fetchFirst('SELECT *  
			FROM '.FDB::table('sys_msg_member').' AS mm 
			LEFT JOIN '.FDB::table('sys_msg').' AS m ON m.mid = mm.mid 
			WHERE mm.status < 2 AND mm.mid = '.$mid.' AND mm.uid = '.$uid);
	}
	
	/**
	 * 删除系统信件
	 * @return void
	 */
	public function deleteSysMsg($uid,$mid)
	{
		FDB::query("UPDATE ".FDB::table('sys_msg_member')." SET status = 2 WHERE uid='$uid' AND mid='$mid'");
		return 1;
	}
	
	/**
	 * 发送信息
	 * @param int $fuid 发信会员编号
	 * @param string $fusername 发信会员名称
	 * @param array  $tuids 收信会员编号数组
	 * @param string $subject 信件主题
	 * @param string $message 信件内容
	 * @param int $type 信件类型
	 * @return int
	 */
	public function sendMsg($fuid, $fusername, $tuids, $subject, $message, $type = 0)
	{
		if(empty($fuid) || empty($fusername) || empty($tuids) || empty($message))
			return 0;
		
		//清除重复会员编号
		$tuids = array_unique($tuids);
		
		$relations = $mlids = array();
		$tmp_tuids = $tuids;
		
		foreach($tmp_tuids as $key => $uid)
		{
			if($fuid == $uid || empty($uid))
			{
				unset($tuids[$key]);
				continue;
			}
			$relations[$uid] = MessageService::getRelation($fuid, $uid);
		}
		
		if(empty($tuids))
			return MSG_SEND_NONE_ERROR;

		if(!$subject)
		{
			$subject = htmlspecialchars(cutStr(clearExpress(trim($message)), 80));
		}
		else
		{
			$subject = htmlspecialchars($subject);
		}
		
		$last_msg = htmlspecialchars(cutStr(clearExpress(trim($message)), 150));
		$type = 0;
		
		if($type == 0)
		{
			$res = FDB::query('SELECT mlid,min_max FROM '.FDB::table('user_msg_list')." WHERE min_max".FDB::createIN($relations));
			while($data = FDB::fetch($res))
			{
				$mlids[$data['min_max']] = $data['mlid'];
			}
			
			$msg_config = array('last_uid' => $fuid, 'last_user_name' => $fusername, 'last_msg' => $last_msg);
			$msg_config = addslashes(serialize($msg_config));
			foreach($relations as $key => $value)
			{
				if(!isset($mlids[$value]))
				{
					FDB::query("INSERT INTO ".FDB::table('user_msg_list')."(uid,type,subject,members,min_max,dateline,msg_config) VALUES('$fuid', '1', '$subject', 2, '$value', '".TIME_UTC."', '$msg_config')");
					$mlid = FDB::insertId();
					FDB::query("INSERT INTO ".FDB::table('user_msg_index')."(mlid) VALUES('$mlid')");
					$miid = FDB::insertId();
					FDB::query("INSERT INTO ".MessageService::getTablaName($mlid)."(miid,mlid,uid,message,dateline,status) VALUES('$miid', '$mlid', '$fuid', '$message', '".TIME_UTC."', 0)");
					FDB::query("INSERT INTO ".FDB::table('user_msg_member')."(mlid, uid, is_new, num, last_update, last_dateline) VALUES('$mlid', '$key', '1', '1', '0', '".TIME_UTC."')");
					FDB::query("INSERT INTO ".FDB::table('user_msg_member')."(mlid, uid, is_new, num, last_update, last_dateline) VALUES('$mlid', '$fuid', '0', '1', '".TIME_UTC."', '".TIME_UTC."')");
				}
				else
				{
					$mlid = $mlids[$value];
					FDB::query("INSERT INTO ".FDB::table('user_msg_index')."(mlid) VALUES('$mlid')");
					$miid = FDB::insertId();
					FDB::query("INSERT INTO ".MessageService::getTablaName($mlid)."(miid,mlid,uid,message,dateline,status) VALUES('$miid', '$mlid', '$fuid', '$message', '".TIME_UTC."', 0)");
					$result = FDB::query("INSERT INTO ".FDB::table('user_msg_member')."(mlid, uid, is_new, num, last_update, last_dateline) VALUES('$mlid', '$key', '1', '1', '0', '".TIME_UTC."')", 'SILENT');
					if(!$result)
						FDB::query("UPDATE ".FDB::table('user_msg_member')." SET is_new = 1, num = num + 1, last_dateline='".TIME_UTC."' WHERE mlid='$mlid' AND uid='$key'");
					
					$result = FDB::query("INSERT INTO ".FDB::table('user_msg_member')."(mlid, uid, is_new, num, last_update, last_dateline) VALUES('$mlid', '$fuid', '0', '1', '".TIME_UTC."', '".TIME_UTC."')", 'SILENT');
					if(!$result)
						FDB::query("UPDATE ".FDB::table('user_msg_member')." SET is_new = 1, num = num + 1, last_update='".TIME_UTC."', last_dateline='".TIME_UTC."' WHERE mlid='$mlid' AND uid='$fuid'");
					
					FDB::query("UPDATE ".FDB::table('user_msg_list')." SET msg_config='$msg_config' WHERE mlid='$mlid'");
				}
			}
		}
		else
		{
			
		}
		
		foreach($tuids as $uid)
		{
			$result = FDB::query("INSERT INTO ".FDB::table('user_notice')."(uid, type, num, create_time) VALUES('$uid',5,1,'".TIME_UTC."')", 'SILENT');
			if(!$result)
				FDB::query("UPDATE ".FDB::table('user_notice')." SET num = num + 1, create_time='".TIME_UTC."' WHERE uid='$uid' AND type=5");
		}

		return $miid;
	}
	
	/**
	 * 回复信息
	 * @param int $mlid 信件组编号
	 * @param int $fuid 发信会员编号
	 * @param string $fusername 发信会员名称
	 * @param string $message 信件内容
	 * @return int
	 */
	public function replyMsg($mlid, $fuid, $fusername, $message)
	{
		if(empty($mlid) || empty($fuid) || empty($fusername) || empty($message))
			return 0;

		$mlist = FDB::fetchFirst("SELECT * FROM ".FDB::table('user_msg_list')." WHERE mlid='$mlid'");
		if(empty($mlist))
			return MSG_MLIST_NONE_ERROR;

		if($mlist['type'] == 1)
		{
			$users = explode('_', $mlist['min_max']);
			if($users[0] == $fuid)
				$tuid = $users[1];
			elseif($users[1] == $fuid)
				$tuid = $users[0];
			else
				return MSG_PRIVILEGE_NONE_ERROR;
		}

		$members = array();
		$query = FDB::query("SELECT * FROM ".FDB::table('user_msg_member')." WHERE mlid='$mlid'");
		while($member = FDB::fetch($query))
		{
			$members[$member['uid']] = "('$member[uid]')";
		}
		
		if(!isset($members[$fuid]))
			return MSG_PRIVILEGE_NONE_ERROR;
		
		$last_msg = htmlspecialchars(cutStr(clearExpress(trim($message)), 150));
		$type = 0;
		
		FDB::query("INSERT INTO ".FDB::table('user_msg_index')."(mlid) VALUES('$mlid')");
		$miid = FDB::insertId();
		FDB::query("INSERT INTO ".MessageService::getTablaName($mlid)."(miid,mlid,uid,message,dateline,status) VALUES('$miid', '$mlid', '$fuid', '$message', '".TIME_UTC."', 0)");
		if($mlist['type'] == 1)
		{
			$msg_config = array('last_uid' => $fuid, 'last_user_name' => $fusername, 'last_msg' => $last_msg);
			$msg_config = addslashes(serialize($msg_config));
			
			$result = FDB::query("INSERT INTO ".FDB::table('user_msg_member')."(mlid, uid, is_new, num, last_update, last_dateline) VALUES('$mlid', '$tuid', '1', '1', '0', '".TIME_UTC."')", 'SILENT');
			if(!$result)
				FDB::query("UPDATE ".FDB::table('user_msg_member')." SET is_new = 1, num = num + 1, last_dateline='".TIME_UTC."' WHERE mlid='$mlid' AND uid='$tuid'");
			
			FDB::query("UPDATE ".FDB::table('user_msg_member')." SET is_new = 0, num = num + 1, last_update='".TIME_UTC."', last_dateline='".TIME_UTC."' WHERE mlid='$mlid' AND uid='$fuid'");
		}
		else
		{
			
		}
		
		FDB::query("UPDATE ".FDB::table('user_msg_list')." SET msg_config='$msg_config' WHERE mlid='$mlid'");
		
		$result = FDB::query("INSERT INTO ".FDB::table('user_notice')."(uid, type, num, create_time) VALUES('$tuid',5,1,'".TIME_UTC."')", 'SILENT');
		if(!$result)
			FDB::query("UPDATE ".FDB::table('user_notice')." SET num = num + 1, create_time='".TIME_UTC."' WHERE uid='$tuid' AND type=5");
		
		return $miid;
	}
	
	public function deleteByMiid($uid,$miid)
	{
		if(empty($miid) || empty($uid))
			return 0;
		
		$index = FDB::fetchFirst("SELECT * FROM ".FDB::table('user_msg_index')." AS mi 
			LEFT JOIN ".FDB::table('user_msg_list')." AS ml ON ml.mlid=mi.mlid 
			WHERE mi.miid='$miid'");
		
		$users = explode('_', $index['min_max']);
		if(!in_array($uid, $users))
			return MSG_PRIVILEGE_NONE_ERROR;
		
		$mlid = $index['mlid'];
		if($index['uid'] != $uid)
		{
			FDB::query("UPDATE ".MessageService::getTablaName($mlid)." SET status = 2 WHERE miid='$miid' AND status=0");
			$update_num = FDB::affectedRows();
			FDB::query("DELETE FROM ".MessageService::getTablaName($mlid)." WHERE miid='$miid' AND status=1");
			$delete_num = FDB::affectedRows();
		}
		else
		{
			FDB::query("UPDATE ".MessageService::getTablaName($mlid)." SET status = 1 WHERE miid='$miid' AND status=0");
			$update_num = FDB::affectedRows();
			FDB::query("DELETE FROM ".MessageService::getTablaName($mlid)." WHERE miid='$miid' AND status=2");
			$delete_num = FDB::affectedRows();
		}

		if(!FDB::resultFirst("SELECT COUNT(*) FROM ".MessageService::getTablaName($mlid)." WHERE mlid='$index[mlid]'"))
		{
			FDB::query("DELETE FROM ".FDB::table('user_msg_list')." WHERE mlid='$mlid'");
			FDB::query("DELETE FROM ".FDB::table('user_msg_member')." WHERE mlid='$mlid'");
			FDB::query("DELETE FROM ".FDB::table('user_msg_index')." WHERE mlid='$mlid'");
		}
		else
		{
			FDB::query("UPDATE ".FDB::table('user_msg_member')." SET num = num - ".($update_num + $delete_num)." WHERE mlid='".$mlid."' AND uid='$uid'");
		}
		
		return 1;
	}
	
	public function deleteByMlid($uid, $mlid)
	{
		if(empty($mlid) || empty($uid))
			return 0;

		$list = FDB::fetchFirst("SELECT * FROM ".FDB::table('user_msg_list')." WHERE mlid='$mlid'");
		if(empty($list))
			return MSG_MLIST_NONE_ERROR;
		
		if($list['type'] == 1)
		{
			$user = explode('_', $list['min_max']);
			if(!in_array($uid, $user))
				return MSG_PRIVILEGE_NONE_ERROR;
		}
		else
		{
			if($uid != $list['uid'])
				return MSG_PRIVILEGE_NONE_ERROR;
		}

		if($list['type'] == 1)
		{
			if($uid == $list['uid'])
			{
				FDB::query("DELETE FROM ".MessageService::getTablaName($mlid)." WHERE mlid='$mlid' AND status = 2");
				FDB::query("UPDATE ".MessageService::getTablaName($mlid)." SET status = 1 WHERE mlid='$mlid' AND status=0");
			}
			else
			{
				FDB::query("DELETE FROM ".MessageService::getTablaName($mlid)." WHERE mlid='$mlid' AND status=1");
				FDB::query("UPDATE ".MessageService::getTablaName($mlid)." SET status=2 WHERE mlid='$mlid' AND status=0");
			}
			
			$count = FDB::resultFirst("SELECT COUNT(*) FROM ".MessageService::getTablaName($mlid)." WHERE mlid='$mlid'");
			if(!$count)
			{
				FDB::query("DELETE FROM ".FDB::table('user_msg_list')." WHERE mlid='$mlid'");
				FDB::query("DELETE FROM ".FDB::table('user_msg_member')." WHERE mlid='$mlid'");
				FDB::query("DELETE FROM ".FDB::table('user_msg_index')." WHERE mlid='$mlid'");
			}
			else
			{
				FDB::query("DELETE FROM ".FDB::table('user_msg_member')." WHERE mlid='$mlid' AND uid='$uid'");
			}
		}
		else
		{
			
		}
		return 1;
	}
	
	public function getMsgCount($uid)
	{
		return FDB::resultFirst('SELECT COUNT(mlid) FROM '.FDB::table('user_msg_member')." WHERE uid='$uid'");
	}
	
	public function getMsgList($uid,$limit)
	{
		$list = array();
		if(empty($uid))
			return $list;

		$members = $tuids = array();

		$query = FDB::query('SELECT * 
			FROM '.FDB::table('user_msg_member').' AS mm 
			LEFT JOIN '.FDB::table('user_msg_list')." AS ml ON ml.mlid=mm.mlid 
			WHERE mm.uid='$uid' ORDER BY mm.last_dateline DESC LIMIT $limit");
		
		while($member = FDB::fetch($query))
		{
			if($member['type'] == 1)
			{
				$users = explode('_', $member['min_max']);
				$member['tuid'] = $users[0] == $uid ? $users[1] : $users[0];
			}
			else
				$member['tuid'] = 0;
			
			$tuids[$member['tuid']] = $member['tuid'];
			$members[] = $member;
		}

		if($members)
		{
			$user_ids = array();
			foreach($members as $key => $data)
			{
				$daterange = 5;
				$data['founddateline'] = $data['dateline'];
				$data['time'] = getBeforeTimelag($data['last_dateline']);
				$msg_config = unserialize($data['msg_config']);
				
				if($msg_config['first_uid'])
				{
					$data['first_uid'] = $msg_config['first_uid'];
					$data['first_user_name'] = $msg_config['first_user_name'];
					$data['first_msg'] = $msg_config['first_msg'];
				}
				
				if($msg_config['last_uid'])
				{
					$data['last_uid'] = $msg_config['last_uid'];
					$data['last_user_name'] = $msg_config['last_user_name'];
					$data['last_msg'] = $msg_config['last_msg'];
				}
				
				$data['msg_fuid'] = $msg_config['last_uid'];
				$data['msg_fuser_name'] = $msg_config['last_user_name'];
				$data['message'] = $msg_config['last_msg'];

				$data['new'] = $data['is_new'];
				unset($data['min_max']);
				$list[$key] = $data;
				$list[$key]['msg_tuser'] = &$user_ids[$data['tuid']];
			}
			FS('User')->usersFormat($user_ids);
		}
		
		return $list;
	}
	
	public function getListByMlid($mlid,$uid)
	{
		static $list = array();
		$key = $mlid.'_'.$uid;
		
		if(!isset($list[$key]))
		{
			$data = FDB::fetchFirst('SELECT * 
				FROM '.FDB::table('user_msg_member').' AS mm 
				LEFT JOIN '.FDB::table('user_msg_list')." AS ml ON ml.mlid=mm.mlid 
				WHERE mm.uid='$uid' AND ml.mlid = '$mlid'");
				
			if(!empty($data))
			{
				if($data['type'] == 1)
				{
					$users = explode('_', $data['min_max']);
					$data['tuid'] = $users[0] == $uid ? $users[1] : $users[0];
				}
				else
					$data['tuid'] = 0;
				
				$data['msg_tuser'] = FS('User')->getUserCache($data['tuid']);
			}
			
			$list[$key] = $data;
		}
		return $list[$key];
	}
	
	public function getMsgsByMlid($mlid,$uid,$limit)
	{
		$list = array();
		
		if(empty($mlid))
			return $list;
		
		$mlist = MessageService::getListByMlid($mlid,$uid);
		if(empty($mlist) || $mlist['type'] != 1)
			return $list;
		
		$where = '';
		if($mlist['uid'] == $uid)
			$where .= ' AND status IN (0,2)';
		else
			$where .= ' AND status IN (0,1)';
		
		$query = FDB::query('SELECT * 
			FROM '.MessageService::getTablaName($mlid)." 
			WHERE mlid='$mlid' $where ORDER BY dateline DESC LIMIT $limit");
		
		while($data = FDB::fetch($query))
		{
			$data['time'] = getBeforeTimelag($data['dateline']);
			$list[] = $data;
		}
		
		FDB::query("UPDATE ".FDB::table('user_msg_member')." SET is_new=0 WHERE mlid='$mlid' AND uid='$uid' AND is_new=1");
		return array_reverse($list);
	}
	
	public function getRelation($fuid, $tuid)
	{
		if($fuid < $tuid)
			return $fuid.'_'.$tuid;
		elseif($fuid > $tuid)
			return $tuid.'_'.$fuid;
		else
			return '';
	}
	
	public function getTablaName($id)
	{
		$id = substr((string)$id, -1, 1);
		return FDB::table('user_msg_'.$id);
	}
	
	/**
	 * 获取会员的拒收名单
	 * @param mix $uid 会员编号或者会员编号数组
	 * @return array
	 */
	public function getBlackUsers($uid)
	{
		$users = array();
		if(is_array($uid))
		{
			$uid = implode(',',$uid);
			$res = FDB::query('SELECT uid,black_users FROM '.FDB::table('user_status')." WHERE uid IN ($uid)");
			while($data = FDB::fetch($res))
			{
				$users[$data['uid']] = explode("\n",$data['black_users']);
			}
		}
		else
		{
			$users = FDB::resultFirst("SELECT black_users FROM ".FDB::table('user_status')." WHERE uid='$uid'");
			$users = explode("\n",$users);
		}
		
		return $users;
	}
	
	/**
	 * 设置会员的拒收名单
	 * @param int $uid 会员编号
	 * @param mix $user_name 会员名称或者会员名称数组
	 * @return void
	 */
	public function setBlackUsers($uid,$user_name)
	{
		$user_name = !is_array($user_name) ? array($user_name) : $user_name;
		if(!in_array('{ALL}', $user_name))
			$user_name = '{ALL}';
		else
			$user_name = implode('\n', $user_name);
			
		FDB::query("UPDATE ".FDB::table('user_status')." SET black_users='$user_name' WHERE uid='$uid'");
	}
}
?>