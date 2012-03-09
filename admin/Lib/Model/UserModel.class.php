<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: awfigq <awfigq@qq.com>
// +----------------------------------------------------------------------
/**
 +------------------------------------------------------------------------------
 * 会员模型
 +------------------------------------------------------------------------------
 */
class UserModel extends CommonModel
{
	public $_validate = array(
		array('user_name','require','{%USER_NAME_REQUIRE}'),
		array('user_name','','{%USER_NAME_EXIST}',0,'unique'),
		array('email','require','{%EMAIL_REQUIRE}'),
		array('email','','{%EMAIL_EXIST}',0,'unique'),
		array('password','require','{%PASSWORD_REQUIRE}',0,'',1),
		array('password','confirm_password','{%CONFIRM_ERROR}',0,'confirm',1),
	);
	
	protected $_auto = array( 
		array('password','md5',3,'function'),
		array('reg_time','gmtTime',1,'function'),
		array('reg_time','strZTime',2,'function'),
	);
	
	public function deleteUser($id_arr)
	{
		$ids = implode(',',$id_arr);

		vendor("common");
		@set_time_limit(0);
		if(function_exists('ini_set'))
			ini_set('max_execution_time',0);
			
		//==================添加第三方整合会员添加 chenfq 2011-10-14================
		foreach($id_arr as $uid)
		{
			$user = $this->getById($uid);
			$user_field = fanweC('INTEGRATE_FIELD_ID');
			$integrate_id = intval($user[$user_field]);

			if ($integrate_id > 0){	
				FS("Integrate")->adminInit(fanweC('INTEGRATE_CODE'),fanweC('INTEGRATE_CONFIG'));				
				FS("Integrate")->delUser($integrate_id);
				//exit;
			}
		}		
		//==================添加第三方整合会员添加chenfq 2011-10-14================
		
		$condition = array ('uid' => array('in',$id_arr));
		if(false !== $this->where($condition)->delete())
		{
			D('AskPost')->where($condition)->delete();
			D('AskThread')->where($condition)->delete();
			D('Atme')->where($condition)->delete();
			D('ForumPost')->where($condition)->delete();
			D('ForumThread')->where($condition)->delete();
			D('ManageLog')->where($condition)->delete();
			D('NedalApply')->where($condition)->delete();
			D('Order')->where($condition)->delete();
			D('PubSchedule')->where($condition)->delete();
			D('Referrals')->where($condition)->delete();
			M()->query('DELETE FROM '.C("DB_PREFIX").'referrals WHERE rid IN ('.$rel_ids.'))');
			M()->query('UPDATE '.C("DB_PREFIX").'user SET invite_id = 0 WHERE invite_id IN ('.$rel_ids.'))');
			D('SecondGoods')->where($condition)->delete();
			D('ShareComment')->where($condition)->delete();
			D('Sessions')->where($condition)->delete();
			D('SysMsg')->where($condition)->delete();
			D('SysMsgMember')->where($condition)->delete();
			D('SysMsgUserNo')->where($condition)->delete();
			D('SysMsgUserYes')->where($condition)->delete();
			D('SysNotice')->where($condition)->delete();
			D('UserAttention')->where($condition)->delete();
			D('UserAuthority')->where($condition)->delete();
			D('UserCount')->where($condition)->delete();
			D('UserBind')->where($condition)->delete();
			D('UserConsignee')->where($condition)->delete();
			
			
			//删除粉丝关注
			$list = M()->query('SELECT uid FROM '.C("DB_PREFIX").'user_follow 
				WHERE f_uid IN ('.$ids.') GROUP BY uid');
			foreach($list as $data)
			{
				M()->query('UPDATE '.C("DB_PREFIX").'user_count 
					SET fans  = fans  - 1 
					WHERE uid = '.$data['uid']);
			}
			$list = M()->query('SELECT f_uid FROM '.C("DB_PREFIX").'user_follow 
				WHERE uid IN ('.$ids.') GROUP BY f_uid');
			foreach($list as $data)
			{
				M()->query('UPDATE '.C("DB_PREFIX").'user_count 
					SET follows  = follows  - 1 
					WHERE uid = '.$data['f_uid']);
			}
			M()->query('DELETE FROM '.C("DB_PREFIX").'user_follow 
				WHERE f_uid IN ('.$ids.')');
			M()->query('DELETE FROM '.C("DB_PREFIX").'user_follow 
				WHERE uid IN ('.$ids.')');
			
			D('UserDaren')->where($condition)->delete();
			D('UserMedal')->where($condition)->delete();
			D('UserMeTags')->where($condition)->delete();
			M()->query('DELETE FROM '.C("DB_PREFIX").'user_msg WHERE author_id IN ('.$ids.')');
			D('UserMsg0')->where($condition)->delete();
			D('UserMsg1')->where($condition)->delete();
			D('UserMsg2')->where($condition)->delete();
			D('UserMsg3')->where($condition)->delete();
			D('UserMsg4')->where($condition)->delete();
			D('UserMsg5')->where($condition)->delete();
			D('UserMsg6')->where($condition)->delete();
			D('UserMsg7')->where($condition)->delete();
			D('UserMsg8')->where($condition)->delete();
			D('UserMsg9')->where($condition)->delete();
			D('UserMsgList')->where($condition)->delete();
			D('UserMsgMember')->where($condition)->delete();
			D('UserNotice')->where($condition)->delete();
			D('UserProfile')->where($condition)->delete();
			D('UserStatistics')->where($condition)->delete();
			D('UserStatus')->where($condition)->delete();
			D('UserScoreLog')->where($condition)->delete();
			D('UserStatistics')->where($condition)->delete();
			foreach($id_arr as $uid)
			{
				$this->deleteUserAvatar($uid);
			}
			return true;
		}
		else
			return false;
	}
	
	public function deleteUserAvatar($uid)
	{
		$types = array(
			's'=>'small',
			'm'=>'middle',
			'b'=>'big',
		);
		$avatar_path = $this->getUserAvatarPath($uid);
		foreach($types as $type)
		{
			deleteImg($avatar_path['path'].'_'.$type.'.jpg');
		}
	}
	
	public function deleteUserAvatarDir($uid)
	{
		$uid = sprintf("%09d", $uid);
		$dir1 = substr($uid, 0, 3);
		$dir2 = substr($uid, 3, 2);
		$dir3 = substr($uid, 5, 2);
		@rmdir(FANWE_ROOT.'public/upload/avatar/'.$dir1.'/'.$dir2.'/'.$dir3.'/');
	}
	
	public function getUserAvatarPath($uid)
	{
		$uid = sprintf("%09d", $uid);
		$dir1 = substr($uid, 0, 3);
		$dir2 = substr($uid, 3, 2);
		$dir3 = substr($uid, 5, 2);
		$arr['path'] = FANWE_ROOT.'public/upload/avatar/'.$dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2);
		$arr['url'] = __ROOT__.'/public/upload/avatar/'.$dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2);
		return $arr;
	}
}
?>