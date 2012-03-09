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
 * 会员信件
 +------------------------------------------------------------------------------
 */
class UserMsgAction extends CommonAction
{
	public function index()
	{
		$where = '';
		$parameter = array();
		$user_name = trim($_REQUEST['user_name']);
		$time = trim($_REQUEST['time']);
		
		if(!empty($user_name))
		{
			$this->assign("user_name",$user_name);
			$parameter['user_name'] = $user_name;
			$uid  = D('User')->where("user_name = '$user_name'")->getField('uid');
			$uid = intval($uid);
			$where .= " AND mm.uid = '$uid'";
		}
		
		if(!empty($time))
		{
			$this->assign("time",$time);
			$parameter['time'] = $time;
			$min_time = strZTime($time);
			$max_time = $min_time + 86400;
			$where .= " AND mm.last_dateline >= '$min_time' AND mm.last_dateline <= '$max_time'";
		}
		
		$model = M();
		
		if(!empty($where))
		{
			$where = 'WHERE' . $where;
			$where = str_replace('WHERE AND','WHERE',$where);
		}
		
		$sql = 'SELECT COUNT(DISTINCT ml.mlid) AS mcount 
			FROM '.C("DB_PREFIX").'user_msg_member AS mm 
			LEFT JOIN '.C("DB_PREFIX")."user_msg_list AS ml ON ml.mlid=mm.mlid ".$where;
		
		$count = $model->query($sql);
		$count = $count[0]['mcount'];
		
		$sql ='SELECT DISTINCT(ml.mlid),mm.last_dateline,ml.min_max,msg_config  
			FROM '.C("DB_PREFIX").'user_msg_member AS mm 
			LEFT JOIN '.C("DB_PREFIX").'user_msg_list AS ml ON ml.mlid=mm.mlid '.$where;
		$this->_sqlList($model,$sql,$count,$parameter,'mm.last_dateline');
		$list = $this->get('list');
		
		foreach($list as $key => $item)
		{
			$users = explode('_', $item['min_max']);
			$user1 = D('User')->where("uid = '$users[0]'")->getField('user_name');
			$user2 = D('User')->where("uid = '$users[1]'")->getField('user_name');
			$item['min_max'] = $user1.' — '.$user2;
			$msg_config = unserialize($item['msg_config']);
			$item['message'] = $msg_config['last_msg'];
			$list[$key] = $item;
		}
		$this->assign("list",$list);
		$this->display();
	}
	
	public function show()
	{
		$mlid = intval($_REQUEST['mlid']);
		$this->assign("mlid",$mlid);
		if($mlid > 0)
		{
			$parameter['mlid'] = $mlid;
			
			$mlist = D('UserMsgList')->where("mlid = '$mlid'")->find();
			if(!empty($mlist))
			{
				$model = M();
				
				$sql = 'SELECT COUNT(mlid) AS mcount 
					FROM '.D('UserMsg')->getTablaName($mlid)."
					WHERE mlid = '$mlid'";
				$count = $model->query($sql);
				$count = $count[0]['mcount'];
				
				$sql ='SELECT m.*,u.user_name   
					FROM '.D('UserMsg')->getTablaName($mlid).' AS m 
					LEFT JOIN '.C("DB_PREFIX")."user AS u ON u.uid=m.uid 
					WHERE m.mlid = '$mlid'";
				
				$this->_sqlList($model,$sql,$count,$parameter,'m.dateline');
			}
		}
		$this->display();
	}
	
	public function delByMlid()
	{
		//删除指定记录
		$result = array('isErr'=>0,'content'=>'');
		$id = $_REQUEST['id'];
		if(!empty($id))
		{
			$condition = createIN(explode (',',$id));
			M()->query("DELETE FROM ".D('UserMsg')->getTablaName($mlid)." WHERE mlid $condition");
			M()->query("DELETE FROM ".C("DB_PREFIX")."user_msg_list WHERE mlid $condition");
			M()->query("DELETE FROM ".C("DB_PREFIX")."user_msg_member WHERE mlid $condition");
			M()->query("DELETE FROM ".C("DB_PREFIX")."user_msg_index WHERE mlid $condition");
		}
		else
		{
			$result['isErr'] = 1;
			$result['content'] = L('ACCESS_DENIED');
		}
		
		die(json_encode($result));
	}
	
	public function delByMiid()
	{
		//删除指定记录
		$result = array('isErr'=>0,'content'=>'');
		$mlid = intval($_REQUEST['mlid']);
		$id = $_REQUEST['id'];
		if(!empty($id) && !empty($mlid))
		{
			$mlist = D('UserMsgList')->where("mlid = '$mlid'")->find();
			if(!empty($mlist))
			{
				$ids = explode (',',$id);
				foreach($ids as $miid)
				{
					if($id > 0)
						M()->query("DELETE FROM ".D('UserMsg')->getTablaName($mlid)." WHERE miid='$miid'");
				}
			}
			
			$sql = 'SELECT COUNT(mlid) AS mcount 
				FROM '.D('UserMsg')->getTablaName($mlid)."
				WHERE mlid = '$mlid'";
			$count = M()->query($sql);
			$count = intval($count[0]['mcount']);
			
			if($count == 0)
			{
				M()->query("DELETE FROM ".C("DB_PREFIX")."user_msg_list WHERE mlid='$mlid'");
				M()->query("DELETE FROM ".C("DB_PREFIX")."user_msg_member WHERE mlid='$mlid'");
				M()->query("DELETE FROM ".C("DB_PREFIX")."user_msg_index WHERE mlid='$mlid'");
			}
			else
			{
				$users = explode('_', $mlist['min_max']);
				if($mlist['uid'] == $users[0])
				{
					$fuid = $users[0];
					$tuid = $users[1];
				}
				else
				{
					$fuid = $users[1];
					$tuid = $users[0];
				}
				
				$sql = 'SELECT COUNT(mlid) AS mcount 
					FROM '.D('UserMsg')->getTablaName($mlid)."
					WHERE mlid = '$mlid' AND uid='$fuid' AND status IN (0,1)";
				$count = M()->query($sql);
				$count = intval($count[0]['mcount']);
				M()->query("UPDATE ".C("DB_PREFIX")."user_msg_member SET num = '$count' WHERE mlid='$mlid' AND uid='$tuid'");
				
				$sql = 'SELECT COUNT(mlid) AS mcount 
					FROM '.D('UserMsg')->getTablaName($mlid)."
					WHERE mlid = '$mlid' AND uid='$tuid' AND status IN (0,2)";
				$count = M()->query($sql);
				$count = intval($count[0]['mcount']);
				M()->query("UPDATE ".C("DB_PREFIX")."user_msg_member SET num = '$count' WHERE mlid='$mlid' AND uid='$fuid'");
			}
		}
		else
		{
			$result['isErr'] = 1;
			$result['content'] = L('ACCESS_DENIED');
		}
		
		die(json_encode($result));
	}
	
	public function groupSend()
	{
		$group_list = D("UserGroup")->getField('gid,name');
		$this->assign("group_list",$group_list);
		
		$this->display();
	}
	
	public function saveSend()
	{
		$msg = array();
		$msg['title'] = trim($_REQUEST['title']);
		$msg['message'] = trim($_REQUEST['message']);
		$msg['create_time'] = gmtTime();
		$end_time = trim($_REQUEST['end_time']);
		if(empty($end_time))
			$msg['end_time'] = 0;
		else
			$msg['end_time'] = strZTime($end_time);
		
		$mid = D('SysMsg')->add($msg);
		
		$user_group = trim($_REQUEST['user_group']);
		if(!empty($user_group))
		{
			$user_group = explode(',',$user_group);
			foreach($user_group as $gid)
			{
				$data = array();
				$data['mid'] = $mid;
				$data['gid'] = $gid;
				D('SysMsgUserGroup')->add($data);
			}
		}
		
		$user_yes = trim($_REQUEST['user_yes']);
		if(!empty($user_yes))
		{
			$user_yes = explode(',',$user_yes);
			$user_yes = array_unique($user_yes);
			foreach($user_yes as $uname)
			{
				$data = array();
				$data['mid'] = $mid;
				$data['uid'] = D('User')->where("user_name = '$uname'")->getField('uid');
				if($data['uid'] > 0)
					D('SysMsgUserYes')->add($data);
			}
		}
		
		$user_no = trim($_REQUEST['user_no']);
		if(!empty($user_no))
		{
			$user_no = explode(',',$user_no);
			$user_no = array_unique($user_no);
			foreach($user_no as $uname)
			{
				$data = array();
				$data['mid'] = $mid;
				$data['uid'] = D('User')->where("user_name = '$uname'")->getField('uid');
				if($data['uid'] > 0)
					D('SysMsgUserNo')->add($data);
			}
		}
		
		$this->assign ('jumpUrl',U('UserMsg/groupList'));
		$this->success (L('ADD_SUCCESS'));
	}
	
	public function updateSend()
	{
		$mid = intval($_REQUEST['mid']);
		
		$msg = array();
		$msg['title'] = trim($_REQUEST['title']);
		$msg['message'] = trim($_REQUEST['message']);
		$msg['create_time'] = gmtTime();
		$end_time = trim($_REQUEST['end_time']);
		if(empty($end_time))
			$msg['end_time'] = 0;
		else
			$msg['end_time'] = strZTime($end_time);
		
		D('SysMsg')->where("mid = '$mid'")->save($msg);
		
		D('SysMsgUserGroup')->where("mid = '$mid'")->delete();
		$user_group = trim($_REQUEST['user_group']);
		if(!empty($user_group))
		{
			$user_group = explode(',',$user_group);
			foreach($user_group as $gid)
			{
				$data = array();
				$data['mid'] = $mid;
				$data['gid'] = $gid;
				D('SysMsgUserGroup')->add($data);
			}
		}
		
		D('SysMsgUserYes')->where("mid = '$mid'")->delete();
		$user_yes = trim($_REQUEST['user_yes']);
		if(!empty($user_yes))
		{
			$user_yes = explode(',',$user_yes);
			$user_yes = array_unique($user_yes);
			foreach($user_yes as $uname)
			{
				$data = array();
				$data['mid'] = $mid;
				$data['uid'] = D('User')->where("user_name = '$uname'")->getField('uid');
				if($data['uid'] > 0)
					D('SysMsgUserYes')->add($data);
			}
		}
		
		D('SysMsgUserNo')->where("mid = '$mid'")->delete();
		$user_no = trim($_REQUEST['user_no']);
		if(!empty($user_no))
		{
			$user_no = explode(',',$user_no);
			$user_no = array_unique($user_no);
			foreach($user_no as $uname)
			{
				$data = array();
				$data['mid'] = $mid;
				$data['uid'] = D('User')->where("user_name = '$uname'")->getField('uid');
				if($data['uid'] > 0)
					D('SysMsgUserNo')->add($data);
			}
		}
		
		$this->assign ('jumpUrl',U('UserMsg/groupList'));
		$this->success (L('EDIT_SUCCESS'));
	}
	
	public function groupEdit()
	{
		$mid = intval($_REQUEST['mid']);
		$msg = D('SysMsg')->where("mid = '$mid'")->find();
		$msg['user_group'] = array();
		$msg['user_yes'] = array();
		$msg['user_no'] = array();
		
		$user_group = D('SysMsgUserGroup')->where("mid = '$mid'")->findAll();
		foreach($user_group as $group)
		{
			$msg['user_group'][] = $group['gid'];
		}
		
		$sql ='SELECT u.user_name   
				FROM '.C("DB_PREFIX").'sys_msg_user_yes AS mu 
				LEFT JOIN '.C("DB_PREFIX")."user AS u ON u.uid=mu.uid 
				WHERE mu.mid = '$mid'";
					
		$users = M()->query($sql);
		foreach($users as $user)
		{
			$msg['user_yes'][] = $user['user_name'];
		}
		$msg['user_yes'] = implode(',',$msg['user_yes']);
		
		$sql ='SELECT u.user_name   
				FROM '.C("DB_PREFIX").'sys_msg_user_no AS mu 
				LEFT JOIN '.C("DB_PREFIX")."user AS u ON u.uid=mu.uid 
				WHERE mu.mid = '$mid'";
					
		$users = M()->query($sql);
		foreach($users as $user)
		{
			$msg['user_no'][] = $user['user_name'];
		}
		$msg['user_no'] = implode(',',$msg['user_no']);
		
		$this->assign("msg",$msg);
		
		$group_list = D("UserGroup")->getField('gid,name');
		$this->assign("group_list",$group_list);
		
		$this->display();
	}
	
	public function groupList()
	{
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$model = D('SysMsg');
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
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
		$model = D('SysMsg');
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
		
		die(json_encode($result));
	}
	
	public function remove()
	{
		//删除指定记录
		$result = array('isErr'=>0,'content'=>'');
		$id = $_REQUEST['id'];
		if(!empty($id))
		{
			$model = D('SysMsg');
			$condition = array('mid' => array('in',explode (',',$id)));
			if(false !== $model->where ( $condition )->delete())
			{
				D('SysMsgUserGroup')->where($condition)->delete();
				D('SysMsgUserYes')->where($condition)->delete();
				D('SysMsgUserNo')->where($condition)->delete();
				D('SysMsgUserMember')->where($condition)->delete();
				$this->saveLog(1,$id);
			}
			else
			{
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
}
?>