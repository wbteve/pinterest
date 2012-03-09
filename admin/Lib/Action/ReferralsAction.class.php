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
 会员邀请
 +------------------------------------------------------------------------------
 */
class ReferralsAction extends CommonAction
{
	public function index()
	{
		$where = '';
		$parameter = array();
		$uname = trim($_REQUEST['uname']);
		$begin_time_str = trim($_REQUEST['begin_time']);
		$end_time_str = trim($_REQUEST['end_time']);
		
		$begin_time = !empty($begin_time_str) ? strZTime($begin_time_str) : 0;
		$end_time = !empty($end_time_str) ? strZTime($end_time_str) : 0;

		if(!empty($uname))
		{
			$this->assign("uname",$uname);
			$parameter['uname'] = $uname;
			$match_key = segmentToUnicodeA($uname,'+');
			$where.=" AND match(u.user_name_match) against('".$match_key."' IN BOOLEAN MODE) ";
            $like_name = mysqlLikeQuote($uname);
            $where .= ' AND u.user_name LIKE \'%'.$like_name.'%\'';
		}
		
		if ($begin_time > 0)
		{
			$this->assign("begin_time",$begin_time_str);
			$parameter['begin_time'] = $begin_time_str;
			$where .= " AND r.create_day >= '".$begin_time."'";
		}
		
		if ($end_time > 0)
		{
			$this->assign("end_time",$end_time_str);
			$parameter['end_time'] = $end_time_str;
			$where .= " AND r.create_day < '".($end_time + 86400)."'";
		}

		$model = M();

		if(!empty($where))
			$where = 'WHERE 1' . $where;

		$sql = 'SELECT COUNT(DISTINCT r.id) AS tcount
			FROM '.C("DB_PREFIX").'referrals AS r 
			LEFT JOIN '.C("DB_PREFIX").'user AS u ON u.uid = r.rid 
			'.$where;

		$count = $model->query($sql);
		$count = $count[0]['tcount'];

		$sql = 'SELECT r.*,CONCAT(u.user_name,\':\',us.reg_ip) as ruser_name,CONCAT(u1.user_name,\':\',us1.reg_ip) as user_name   
			FROM '.C("DB_PREFIX").'referrals AS r 
			LEFT JOIN '.C("DB_PREFIX").'user AS u ON u.uid = r.rid 
			LEFT JOIN '.C("DB_PREFIX").'user_status AS us ON us.uid = r.rid 
			LEFT JOIN '.C("DB_PREFIX").'user AS u1 ON u1.uid = r.uid 
			LEFT JOIN '.C("DB_PREFIX").'user_status AS us1 ON us1.uid = r.uid 
			'.$where.' GROUP BY r.id';
		$this->_sqlList($model,$sql,$count,$parameter,'r.id');
		$this->display();
		return;
	}
	
	public function toggleStatus()
	{
		$id = intval($_REQUEST['id']);
		if($id == 0)
			exit;
		
		$val = intval($_REQUEST['val']) == 0 ? 1 : 0;
			
		$field = trim($_REQUEST['field']);
		if(empty($field))
			exit;
		
		$result = array('isErr'=>0,'content'=>'');
		$name=$this->getActionName();
		$model = D($name);
		$pk = $model->getPk();
		if(false !== $model->where($pk.' = '.$id)->setField($field,$val))
		{
			$this->saveLog(1,$id,$field);
			$result['content'] = $val;
		}
		else
		{
			$this->saveLog(0,$id,$field);
			$result['isErr'] = 1;
		}
		
		Vendor('common');
		$referral = $model->where($pk.' = '.$id)->find();
		$user_name = D('User')->where('uid = '.$referral['uid'])->getField('user_name');
		if($val == 1)
			FS("User")->updateUserScore($referral['rid'],"user","referral",$user_name,$referral['uid']);
		else
			FS("User")->updateUserScore($referral['rid'],"clear","referral",$user_name,$referral['uid']);
		
		die(json_encode($result));
	}
}
?>