<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/**  
 * medal.service.php
 *
 * 勋章服务
 *
 * @package service
 * @author awfigq <awfigq@qq.com>
 */
class MedalService
{
	public function get($mid)
	{
		global $_FANWE;
		FanweService::instance()->cache->loadCache('medals');
		if(isset($_FANWE['cache']['medals']['all'][$mid]))
			return $_FANWE['cache']['medals']['all'][$mid];
		else
			return false;
	}
	
	private function getUserInfo($uid)
	{
		$uid = (int)$uid;
		static $users = array();
		if(!isset($users[$uid]))
		{
			$users[$uid] = FDB::fetchFirst("SELECT u.*, uc.*, us.* FROM ".FDB::table('user')." u
				LEFT JOIN ".FDB::table('user_count')." uc USING(uid)
				LEFT JOIN ".FDB::table('user_status')." us USING(uid) 
				WHERE u.uid='$uid'");
		}
		return $users[$uid];
	}
	
	public function medalFormat($uid,&$medals)
	{
		$uid = (int)$uid;
		if(!$uid)
			return;
		
		$statistic = FS('Statistics')->getUserStatistics($uid);
		$user = MedalService::getUserInfo($uid);
		$award_medals = explode(',', $user['medals']);
		$award_list = array();
		if(count($award_medals) > 0)
			$award_list = MedalService::getAwardsByUid($uid);
		$apply_list = MedalService::getApplysByUid($uid);
		
		foreach($medals as $key => $medal)
		{
			$mid = $medal['mid'];
			if(isset($award_list[$mid]))
				$medal['is_award'] = 1;
			else
				$medal['is_award'] = 0;
			
			$medal['is_allow'] = 1;
			
			if($medal['give_type'] == 1)
			{
				if($medal['is_award'] == 1)
				{
					$medal['award_time'] = fToDate($award_list[$mid]['create_time'],'Y-m-d');
					$medal['end_time'] = fToDate($award_list[$mid]['deadline'],'Y-m-d');
				}
				else
				{
					if(isset($apply_list[$mid]))
					{
						$medal['is_apply'] = 1;
					}
					else
					{
						$medal['is_apply'] = 0;
						if(!MedalService::checkAllowGroup($user['gid'],$medal['allow_group']))
							$medal['is_allow'] = 0;
					}	
				}
			}
			else
			{
				$condition = $medal['conditions'];
				$is_continue = strpos($condition,'continue');

				if($medal['is_award'] == 1)
				{
					if($is_continue === false)
					{
						$medal['tips1'] = sprintf(lang('medal','medal_award'),$medal['name']);
					}
					else
					{
						$medal['tips1'] = sprintf(lang('medal',$condition.'_1'),$medal['name'],(int)$statistic[$condition]);
						$medal['tips2'] = sprintf(lang('medal',$condition.'_alert'));
					}
				}
				else
				{
					if($is_continue === false)
					{
						$medal['tips1'] = sprintf(lang('medal','auto_tip_'.$condition),(int)$medal['confine'] - (int)$user[$condition],(int)$user[$condition]);
					}
					else
					{
						$medal['tips1'] = sprintf(lang('medal',$condition.'_0'),(int)$medal['confine'] - (int)$statistic[$condition],(int)$statistic[$condition]);
						$medal['tips2'] = sprintf(lang('medal',$condition.'_alert'));
					}
				}
			}
			
			$medals[$key] = $medal;
		}
	}
	
	/**
	 * 自动颁发和回收自动勋章
	 * @param int $uid
	 * @param string $type 行为类型
	 * @return
	 */
	public function runAuto($uid,$type)
	{
		$uid = (int)$uid;
		
		//获取勋章
		$medals = MedalService::getAutoMedalByType($type);
		
		if (!$uid || !$medals)
			return false;
		
		$user = MedalService::getUserInfo($uid);
		foreach($medals as $medal)
		{
			$num = MedalService::getNum($uid,$medal['conditions']);
			if($num >= $medal['confine'])
				MedalService::autoAward($user,$medal);
			else
				MedalService::autoRecover($user,$medal);
		}
		MedalService::updateUserMedal($uid);
	}
	
	/**
	 * 通过uid获取已获取的勋章信息
	 * @param int $uid
	 * @return array
	 */
	public function getAwardsByUid($uid)
	{
		$uid = (int)$uid;
		static $list = array();
		if(!isset($list[$uid]))
		{
			$res = FDB::query('SELECT * FROM '.FDB::table('user_medal').' WHERE uid = '.$uid);
			$list[$uid] = array();
			while($data = FDB::fetch($res))
			{
				$list[$uid][$data['mid']] = $data;
			}
		}
		return $list[$uid];
	}
	
	/**
	 * 通过uid和勋章id获取已获取的勋章信息
	 * @param int $uid
	 * @param int $mid
	 * @return array
	 */
	public function getAwardByUidAndMid($uid,$mid) {
		$uid = (int)$uid;
		$mid = (int)$mid;
		return FDB::fetchFirst('SELECT * FROM '.FDB::table('user_medal').' 
			WHERE uid = '.$uid.' AND mid = '.$mid);
	}
	
	/**
	 * 给一个用户颁发勋章
	 * type{0:系统自动颁发,1:用户申请,2:管理员颁发}
	 * @param int $uid
	 * @param int $mid
	 * @param bool $is_apply 是否是申请勋章
	 * @param array $medal 勋章信息（可选）
	 * @
	 * @return bool
	 */
	public function awardMedal($uid,$mid,$is_apply=false,$medal = array(),$desc='')
	{
		if (MedalService::getAwardByUidAndMid($uid,$mid))
			return array(false,'勋章已存在');
		
		$medal = $medal ? $medal : MedalService::get($mid);
		if (!$medal)
			return array(false,'勋章不存在');
		
		$data = array('uid'=>$uid,'mid'=>$mid,'create_time'=>TIME_UTC);
		if ($medal['give_type'] == 1 && $medal['expiration'])
		{
			$today_time = getTodayTime();
			$data['deadline'] = $today_time + $medal['expiration'] * 86400;
		}
		
		$data['type'] = $medal['give_type'] == 1 ? ($is_apply ? 1 : 2) : 0;
		
		$result = FDB::insert('user_medal',$data,true);
		if($result)
			MedalService::sendAwardNotice($uid, $medal,$data['type'],$desc);
		return array($result);
	}
	
	/**
	 * 回收一个勋章
	 * @param int $id
	 * @return
	 */
	public function recoverMedal($award,$desc='')
	{
		$id = (int)$award['id'];
		$result = FDB::delete('user_medal','id = '.$id);
		if($result)
			MedalService::sendRecoverNotice($award['uid'],$award['mid'],$desc);
		return $result;
	}
	
	public function getApply($id)
	{
		$id = (int)$id;
		return FDB::fetchFirst('SELECT * FROM '.FDB::table('medal_apply').' 
			WHERE id = '.$id);
	}
	
	/**
	 * 通过uid获取已申请的勋章信息
	 * @param int $uid
	 * @return array
	 */
	public function getApplysByUid($uid)
	{
		$uid = (int)$uid;
		static $list = array();
		if(!isset($list[$uid]))
		{
			$res = FDB::query('SELECT * FROM '.FDB::table('medal_apply').' WHERE uid = '.$uid);
			$list[$uid] = array();
			while($data = FDB::fetch($res))
			{
				$list[$uid][$data['mid']] = $data;
			}
		}
		return $list[$uid];
	}
	
	/**
	 * 通过uid和勋章id获取已申请的勋章信息
	 * @param int $uid
	 * @param int $mid
	 * @return array
	 */
	public function getApplyByUidAndMid($uid,$mid) {
		$uid = (int)$uid;
		$mid = (int)$mid;
		return FDB::fetchFirst('SELECT * FROM '.FDB::table('medal_apply').' 
			WHERE uid = '.$uid.' AND mid = '.$mid);
	}
	
	/**
	 * 申请一个勋章
	 * @param int $uid
	 * @param int $medalId
	 * @param string $reason
	 * @return
	 */
	public function applyMedal($uid,$mid,$reason)
	{
		$uid = (int) $uid;
		$mid = (int) $mid;
		if (!$uid || !$mid)
			return array(false,lang('common','data_error'));
		
		if (MedalService::getAwardByUidAndMid($uid,$mid))
			return array(false,lang('medal','error_award_medal'));
		
		if (MedalService::getApplyByUidAndMid($uid, $mid))
			return array(false,lang('medal','error_apply_medal'));
		
		$medal = MedalService::get($mid);
		if (!$medal || $medal['give_type'] != 1 || !$medal['status'])
			return array(false,lang('medal','error_medal_noapply'));
		
		$user = MedalService::getUserInfo($uid);
		if (!MedalService::checkAllowGroup($user['gid'],$medal['allow_group']))
			array(false,lang('medal','error_group_noapply'));
			
		$data = array('uid'=>$uid,'mid'=>$mid,'reason'=>$reason,'create_time'=>TIME_UTC);
		$id = FDB::insert('medal_apply',$data);
		return array($id);
	}
	
	/**
	 * 通过一个勋章申请
	 * @param int $id
	 * @return
	 */
	public function adoptApplyMedal($id)
	{
		$apply = MedalService::getApply($id);
		if (!$apply)
			return false;
		
		$result = MedalService::awardMedal($apply['uid'],$apply['mid'],1);
		if (!$result)
			return false;
		
		FDB::delete('medal_apply','id = '.$id);
		MedalService::updateUserMedal($uid);
		return true;
	}
	
	/**
	 * 拒绝一个勋章申请
	 * @param int $id
	 * @return
	 */
	public function refuseApplyMedal($id)
	{
		$apply = MedalService::getApply($id);
		if (!$apply)
			return false;
		
		MedalService::sendRefuseNotice($apply['uid'], $apply['mid']);
		return FDB::delete('medal_apply','id = '.$id);
	}
	
	/**
	 * 根据类型获取自动颁发勋章
	 * @param string $type
	 * @return
	 */
	public function getAutoMedalByType($type)
	{
		global $_FANWE;
		FanweService::instance()->cache->loadCache('medals');
		$medal_list = $_FANWE['cache']['medals'];
		$medals = array();
		if($type == 'continue_login')
		{
			$types = FS('Statistics')->getTypes();
			foreach($types as $type)
			{
				foreach($medal_list[$type] as $mid)
				{
					$medals[] = $medal_list['all'][$mid];
				}
			}
			
			return $medals;
		}
		
		if(!isset($medal_list[$type]))
			return false;
		else
		{
			foreach($medal_list[$type] as $mid)
			{
				$medals[] = $medal_list['all'][$mid];
			}
			return $medals;
		}
	}
	
	
	private function getNum($uid,$type)
	{
		$types = FS('Statistics')->getTypes();
		if(array_search($type,$types) !== false)
		{
			$statistic = FS('Statistics')->getUserStatistics($uid);
			return (int)$statistic[$type];
		}
		else
		{
			$user = MedalService::getUserInfo($uid);
			if(isset($user[$type]))
				return (int)$user[$type];
			else
				return 0;
		}
	}
	
	public function checkIsHaveMedal($mid,$medals) {
		if (!$medals)
			return false;
			
		$medals = explode(',', $medals);
		return in_array($mid,$medals);
	}
	
	public function checkAllowGroup($gid,$groups)
	{
		if (!$groups)
			return true;
		
		$groups = explode(',', $groups);
		return in_array($gid,$groups);
	}
	
	private function autoAward($user,$medal)
	{
		if (MedalService::checkAllowGroup($user['gid'],$medal['allow_group']) && !MedalService::checkIsHaveMedal($medal['mid'],$user['medals']))
			MedalService::awardMedal($user['uid'], $medal['mid'],false,$medal);
	}
	
	private function autoRecover($user,$medal)
	{
		if (MedalService::checkIsHaveMedal($medal['mid'],$user['medals']))
		{
			$award_medal = MedalService::getAwardByUidAndMid($user['uid'], $medal['mid']);
			if($award_medal)
				MedalService::recoverMedal($award_medal);
		}
	}
	
	public function updateUserMedal($uid)
	{
		$uid = (int)$uid;
		$mids = array();
		$res = FDB::query('SELECT mid FROM '.FDB::table('user_medal').' WHERE uid = '.$uid);
		while($data = FDB::fetch($res))
		{
			$mids[] = $data['mid'];
		}
		$mids = implode(',', $mids);
		FDB::query('UPDATE '.FDB::table('user_status')." SET medals = '$mids' WHERE uid = $uid");
	}
	
	private function sendAwardNotice($uid, $medal, $type, $admin_content = '')
	{
		$user = MedalService::getUserInfo($uid);
		$title = lang('medal','new_medal_title');
		$content = sprintf(lang('medal','new_medal_content'),FU('medal/u',array('uid'=>$uid)),$medal['name']);
		switch($type)
		{
			case 0:
				$content .= sprintf(lang('medal','new_medal_content_0'),lang('medal','medal_'.$medal['conditions']),$medal['confine']);
			break;
			case 1:
				$content .= lang('medal','new_medal_content_1');
			break;
			case 2:
				$content .= empty($admin_content) ? sprintf(lang('medal','new_medal_content_2'),$medal['name']) : $admin_content;
			break;
		}
		
		$notice = array();
		$notice['uid'] = $uid;
		$notice['title'] = $title;
		$notice['content'] = $content;
		
		FS('Notice')->send($notice);
	}
	
	private function sendRecoverNotice($uid, $mid, $admin_content='')
	{
		$user = MedalService::getUserInfo($uid);
		$medal = MedalService::get($mid);
		$title = lang('medal','recover_medal_title');
		$content = sprintf(lang('medal','recover_medal_content'),FU('medal/index'),$medal['name']);

		if($medal['give_type'] == 0)
		{
			$content .= sprintf(lang('medal','recover_medal_content_0'),lang('medal','medal_'.$medal['conditions']),$medal['confine']);
		}
		elseif($medal['give_type'] == 1)
		{
			$content .= empty($admin_content) ? sprintf(lang('medal','recover_medal_content_2'),$medal['name']) : $admin_content;
		}
		
		$notice = array();
		$notice['uid'] = $uid;
		$notice['title'] = $title;
		$notice['content'] = $content;
		
		FS('Notice')->send($notice);
	}
	
	private function sendRefuseNotice($uid, $mid)
	{
		$user = MedalService::getUserInfo($uid);
		$medal = MedalService::get($mid);
		
		$title = lang('medal','refuse_medal_title');
		$content = sprintf(lang('medal','refuse_medal_content'),FU('medal/index'),$medal['name']);
		
		$notice = array();
		$notice['uid'] = $uid;
		$notice['title'] = $title;
		$notice['content'] = $content;
		
		FS('Notice')->send($notice);
	}
}
?>