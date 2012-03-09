<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/**  
 * session.class.php
 *
 * Session处理类
 *
 * @package class
 * @author awfigq <awfigq@qq.com>
 */
class Session
{
	var $sid = NULL;
	var $var;
	var $is_new = false;
	var $guest = array('sid' => 0, 'ip1' => 0, 'ip2' => 0, 'ip3' => 0, 'ip4' => 0,'uid' => 0, 'user_name' => '', 'gid' => 6,'shop_id' => 0, 'share_id' => 0,'last_activity' => 0, 'last_update' => 0);

	var $old =  array('sid' =>  '', 'ip' =>  '', 'uid' =>  0);

	function Session($sid = '', $ip = '', $uid = 0)
	{
		$this->old = array('sid' =>  $sid, 'ip' =>  $ip, 'uid' =>  $uid);
		$this->var = $this->guest;
		if(!empty($ip))
		{
			$this->init($sid, $ip, $uid);
		}
	}

	function set($key, $value)
	{
		if(isset($this->guest[$key]))
			$this->var[$key] = $value;
		elseif ($key == 'ip')
		{
			$ips = explode('.', $value);
			$this->set('ip1', $ips[0]);
			$this->set('ip2', $ips[1]);
			$this->set('ip3', $ips[2]);
			$this->set('ip4', $ips[3]);
		}
	}

	function get($key)
	{
		if(isset($this->guest[$key]))
		{
			return $this->var[$key];
		}
		elseif ($key == 'ip')
		{
			return $this->get('ip1').'.'.$this->get('ip2').'.'.$this->get('ip3').'.'.$this->get('ip4');
		}
	}

	function init($sid, $ip, $uid)
	{
		$this->old = array('sid' =>  $sid, 'ip' =>  $ip, 'uid' =>  $uid);
		
		$session = array();
		if($sid)
		{
			$session = FDB::fetchFirst("SELECT * FROM ".FDB::table('sessions')." WHERE sid='$sid' AND CONCAT_WS('.', ip1,ip2,ip3,ip4)='$ip'");
		}
		
		if(empty($session) || $session['uid'] != $uid)
		{
			$session = $this->create($ip, $uid);
		}

		$this->var = $session;
		$this->sid = $session['sid'];
	}

	function create($ip, $uid)
	{
		$this->is_new = true;
		$this->var = $this->guest;
		$this->set('sid', random(6));
		$this->set('uid', $uid);
		$this->set('ip', $ip);
		$this->set('last_activity',TIME_UTC);
		$this->sid = $this->var['sid'];
		return $this->var;
	}

	function delete()
	{
		$online_hold = 60;
		$guest_span = 60;

		$online_hold = TIME_UTC - $online_hold;
		$guest_span = TIME_UTC - $guest_span;

		$condition = " sid='{$this->sid}' ";
		$condition .= " OR last_activity < $online_hold ";
		$condition .= " OR (uid='0' AND ip1='{$this->var['ip1']}' AND ip2='{$this->var['ip2']}' AND ip3='{$this->var['ip3']}' AND ip4='{$this->var['ip4']}' AND last_activity > $guest_span) ";
		$condition .= $this->var['uid'] ? " OR (uid='{$this->var['uid']}') " : '';
		FDB::delete('sessions', $condition);
	}

	function update()
	{
		if($this->sid !== NULL)
		{
			$data = fAddslashes($this->var);
			if($this->is_new)
			{
				$this->delete();
				FDB::insert('sessions', $data, false, false, true);
			}
			else
			{
				FDB::update('sessions', $data, "sid='$data[sid]'");
			}
			fSetCookie('sid', $this->sid, 86400);
		}
	}

	function onLineCount()
	{
		return FDB::resultFirst("SELECT count(*) FROM ".FDB::table('sessions').' WHERE uid > 0');
	}
}
?>