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
 会员勋章
 +------------------------------------------------------------------------------
 */
class MedalAction extends CommonAction
{
	public function add()
	{	
		$user_groups = D("UserGroup")->where('status = 1')->findAll();
		$this->assign("user_groups",$user_groups);
		parent::add();
	}
	
	public function insert()
	{
		$desc = trim($_REQUEST['desc']);
		$_POST['allow_group'] = '';
		if(isset($_POST['allow_gid']))
			$_POST['allow_group'] = implode(',',$_POST['allow_gid']);
		
		if($_POST['give_type'] == 1)
		{
			$_POST['conditions'] = '';
		}
		parent::insert();
	}
	
	public function edit()
	{	
		$id = intval($_REQUEST['mid']);
		
		$vo = D("Medal")->getById($id);
		$vo['allow_group'] = explode(',',$vo['allow_group']);
		
		$continues = array('continue_login','continue_share','continue_goods','continue_photo','continue_forum','continue_ask');
		$this->assign ('continues', $continues );
		
		$user_groups = D("UserGroup")->where('status = 1')->findAll();
		$this->assign("user_groups",$user_groups);
		
		$this->assign ('vo', $vo );
		$this->display();
	}
	
	public function update()
	{
		$desc = trim($_REQUEST['desc']);
		unset($_POST['conditions']);
		$_POST['allow_group'] = '';
		if(isset($_POST['allow_gid']))
			$_POST['allow_group'] = implode(',',$_POST['allow_gid']);
		parent::update();
	}
	
	public function remove()
	{
		//删除指定记录
		$result = array('isErr'=>0,'content'=>'');
		$id = $_REQUEST['id'];
		if(!empty($id))
		{
			$name=$this->getActionName();
			$model = D($name);
			$pk = $model->getPk ();
			$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
			if(false !== $model->where($condition )->delete())
			{
				$condition = array ('mid' => array ('in', explode(',',$id)));
				D('UserMedal')->where($condition )->delete();
				D('MedalApply')->where($condition )->delete();
				$this->saveLog(1,$id);
			}
			else
			{
				$this->saveLog(0,$id);
				$result['isErr'] = 1;
				$result['content'] = L('REMOVE_ERROR');
			}
		}
		else
		{
			$result['isErr'] = 1;
			$result['content'] = L('ACCESS_DENIED');
		}
		
		die(json_encode($result));
	}
	
	public function user()
	{
		$where = '';
		$parameter = array();
		$uname = trim($_REQUEST['uname']);
		$mid = intval($_REQUEST['mid']);
		$type = !isset($_REQUEST['type']) ? -1 : intval($_REQUEST['type']);
		
		if(!empty($uname))
		{
			$this->assign("uname",$uname);
			$parameter['uname'] = $uname;
			$match_key = segmentToUnicodeA($uname,'+');
			$where.=" AND match(u.user_name_match) against('".$match_key."' IN BOOLEAN MODE) ";
            $like_name = mysqlLikeQuote($uname);
            $where .= ' AND u.user_name LIKE \'%'.$like_name.'%\'';
		}
		
		if($mid > 0)
		{
			$this->assign("mid",$mid);
			$parameter['mid'] = $mid;
			$where .= " AND um.mid = $mid";
		}
		
		if($type != -1)
		{
			$this->assign("type",$type);
			$parameter['type'] = $type;
			$where .= " AND um.type = $type";
		}
		else
			$this->assign("type",-1);
		
		if(!empty($where))
		{
			$where = ' WHERE'.$where;
			$where = str_replace('WHERE AND','WHERE',$where);
		}
		
		$model = M();

		$sql = 'SELECT COUNT(DISTINCT um.id) AS scount
			FROM '.C("DB_PREFIX").'user_medal AS um 
			LEFT JOIN '.C("DB_PREFIX").'medal AS m ON m.mid = um.mid  
			LEFT JOIN '.C("DB_PREFIX").'user AS u ON u.uid = um.uid 
			'.$where;

		$count = $model->query($sql);
		$count = $count[0]['scount'];

		$sql = 'SELECT um.id,um.type,um.create_time,um.deadline,u.user_name,m.name,m.image,m.give_type 
			FROM '.C("DB_PREFIX").'user_medal AS um 
			LEFT JOIN '.C("DB_PREFIX").'medal AS m ON m.mid = um.mid  
			LEFT JOIN '.C("DB_PREFIX").'user AS u ON u.uid = um.uid 
			'.$where;
		$this->_sqlList($model,$sql,$count,$parameter,'um.id');
		
		$medal_list = D('Medal')->where('status = 1')->order('sort asc')->findAll();
		$this->assign("medal_list",$medal_list);
		
		$this->display ();
	}
	
	public function send()
	{
		$medal_list = D('Medal')->where('status = 1 AND give_type = 1')->order('sort asc')->findAll();
		$this->assign("medal_list",$medal_list);
		$this->display ();
	}
	
	public function award()
	{
		Vendor('common');
		$result = FS('Medal')->awardMedal($_POST['uid'],$_POST['mid'],false,array(),$_POST['desc']);
		list($status,$error) = $result;
		if (false === $status)
			$this->error($error);
		else
		{
			FS('Medal')->updateUserMedal($_POST['uid']);
			$this->success (L('SEND_SUCCESS'));
		}
	}
	
	public function removeAward()
	{
		
		$result = array('isErr'=>0,'content'=>'');
		$id = $_REQUEST['id'];
		if(!empty($id))
		{
			$condition = array ('id' => array ('in',explode(',',$id)));
			$award_list = D('UserMedal')->where($condition )->findAll();
			$uids = array();
			
			Vendor('common');
			foreach($award_list as $award)
			{
				$uids[] = $award['uid'];
				FS('Medal')->recoverMedal($award,$_POST['desc']);
			}
			
			$uids = array_unique($uids);
			foreach($uids as $uid)
			{
				FS('Medal')->updateUserMedal($uid);
			}
				
			$this->saveLog(1,$id);
		}
		else
		{
			$result['isErr'] = 1;
			$result['content'] = L('ACCESS_DENIED');
		}
		
		die(json_encode($result));
	}
	
	public function check()
	{
		$where = '';
		$parameter = array();
		$uname = trim($_REQUEST['uname']);
		$mid = intval($_REQUEST['mid']);
		
		if(!empty($uname))
		{
			$this->assign("uname",$uname);
			$parameter['uname'] = $uname;
			$match_key = segmentToUnicodeA($uname,'+');
			$where.=" AND match(u.user_name_match) against('".$match_key."' IN BOOLEAN MODE) ";
            $like_name = mysqlLikeQuote($uname);
            $where .= ' AND u.user_name LIKE \'%'.$like_name.'%\'';
		}
		
		if($mid > 0)
		{
			$this->assign("mid",$mid);
			$parameter['mid'] = $mid;
			$where .= " AND ma.mid = $mid";
		}
		
		if(!empty($where))
		{
			$where = ' WHERE'.$where;
			$where = str_replace('WHERE AND','WHERE',$where);
		}
		
		$model = M();

		$sql = 'SELECT COUNT(DISTINCT ma.id) AS scount
			FROM '.C("DB_PREFIX").'medal_apply AS ma 
			LEFT JOIN '.C("DB_PREFIX").'medal AS m ON m.mid = ma.mid  
			LEFT JOIN '.C("DB_PREFIX").'user AS u ON u.uid = ma.uid 
			'.$where;

		$count = $model->query($sql);
		$count = $count[0]['scount'];

		$sql = 'SELECT ma.id,ma.reason,ma.create_time,u.user_name,m.name,m.image,m.give_type 
			FROM '.C("DB_PREFIX").'medal_apply AS ma 
			LEFT JOIN '.C("DB_PREFIX").'medal AS m ON m.mid = ma.mid  
			LEFT JOIN '.C("DB_PREFIX").'user AS u ON u.uid = ma.uid 
			'.$where;
		$this->_sqlList($model,$sql,$count,$parameter,'ma.id');
		
		$medal_list = D('Medal')->where('status = 1 AND give_type = 1')->order('sort asc')->findAll();
		$this->assign("medal_list",$medal_list);
		
		$this->display ();
	}
	
	public function checkApply()
	{
		
		$result = array('isErr'=>0,'content'=>'');
		$id = $_REQUEST['id'];
		$status = (int)$_REQUEST['status'];
		if(!empty($id))
		{
			$ids = explode(',',$id);
			$award_list = D('MedalApply')->where($condition )->findAll();
			$uids = array();
			
			Vendor('common');
			foreach($ids as $maid)
			{
				if($status == 1)
					FS('Medal')->adoptApplyMedal($maid);
				else
					FS('Medal')->refuseApplyMedal($maid);
			}
			$this->saveLog(1,$id);
		}
		else
		{
			$result['isErr'] = 1;
			$result['content'] = L('ACCESS_DENIED');
		}
		
		die(json_encode($result));
	}
}

function getMedalImg($img)
{
	if(empty($img))
		return '';
	else
		return "<img src='".__ROOT__."/public/medal/small/$img' width='24' />";
}

function getTypeName($type)
{
	return L('GIVE_TYPE_'.$type);
}

function getSendName($type)
{
	return L('SEND_TYPE_'.$type);
}

function getRecoverLink($id,$item)
{
	if($item['give_type'] == 0)
		return '--';
	else
		return '<a href="javascript:;" onclick="removeAward(this,'.$id.')">'.L('RECOVER').'</a>';
}

function getApplyLink($id,$item)
{
	return '<a href="javascript:;" onclick="applyHandler(this,'.$id.',1)">'.L('ADOPT').'</a>&nbsp;&nbsp;'.
		'<a href="javascript:;" onclick="applyHandler(this,'.$id.',0)">'.L('REFUSE').'</a>';
}
?>