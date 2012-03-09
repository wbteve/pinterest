<?php
class MessageModule
{
	public function index()
	{
		$this->inbox();	
	}
	
	public function inbox()
	{
		define("ACTION_NAME","inbox");
		if(intval($GLOBALS['fanwe']->var['uid'])==0)
		{			
			fHeader("location: ".FU('user/login'));	
		}
		global $_FANWE;
		
		//收件箱
		$sql = "select m.mid,m.title,m.content,m.create_time,u.uid,u.user_name,u.server_code from ".FDB::table("user_msg")." as m left join ".
				FDB::table("user_msg_rel")." as ml on ml.mid = m.mid left join ".
				FDB::table("user")." as u on u.uid = m.author_id where m.status = 1 and ml.uid = ".intval($GLOBALS['fanwe']->var['uid'])." order by m.create_time desc,m.author_id asc ";
		$sql_count = "select count(m.mid) from ".FDB::table("user_msg")." as m left join ".
				FDB::table("user_msg_rel")." as ml on ml.mid = m.mid left join ".
				FDB::table("user")." as u on u.uid = m.author_id where m.status = 1 and ml.uid = ".intval($GLOBALS['fanwe']->var['uid']);
		
		$page_size = 10;
			
		$count=FDB::resultFirst($sql_count);
		$pager = buildPage('message/inbox',array(),$count,$_FANWE['page'],$page_size);
		$sql  = $sql." limit ".$pager['limit'];	
		
		$list = FDB::fetchAll($sql);
		
		include template('page/message_inbox');
					
		display();	
	}

	public function outbox()
	{
		define("ACTION_NAME","outbox");
		if(intval($GLOBALS['fanwe']->var['uid'])==0)
		{			
			fHeader("location: ".FU('user/login'));	
		}
		global $_FANWE;

		//发件箱
		$sql = "select m.mid,m.title,m.content,m.create_time,u.uid,u.user_name,u.server_code from ".FDB::table("user_msg")." as m left join ".
				FDB::table("user_msg_rel")." as ml on ml.mid = m.mid left join ".
				FDB::table("user")." as u on u.uid = ml.uid where m.status = 1 and m.author_id = ".intval($GLOBALS['fanwe']->var['uid'])." order by m.create_time desc ";
		$sql_count = "select count(m.mid) from ".FDB::table("user_msg")." as m left join ".
				FDB::table("user_msg_rel")." as ml on ml.mid = m.mid left join ".
				FDB::table("user")." as u on u.uid = ml.uid where m.status = 1 and m.author_id = ".intval($GLOBALS['fanwe']->var['uid']);
		
		$page_size = 10;
			
		$count=FDB::resultFirst($sql_count);
		$pager = buildPage('message/outbox',array(),$count,$_FANWE['page'],$page_size);
		$sql  = $sql." limit ".$pager['limit'];	
		
		$list = FDB::fetchAll($sql);
		
		include template('page/message_outbox');					
		display();	
	}
	
	public function target()
	{
		define("ACTION_NAME","target");
		if(intval($GLOBALS['fanwe']->var['uid'])==0)
		{			
			fHeader("location: ".FU('user/login'));	
		}
		global $_FANWE;
		
		$sql = "select f.f_uid as uid,u.user_name from ".FDB::table("user_follow")." as f left join ".
				FDB::table("user")." as u on f.f_uid = u.uid where f.uid = ".intval($GLOBALS['fanwe']->var['uid']);
		$sql_count = "select count(f.f_uid) from ".FDB::table("user_follow")." as f left join ".
				FDB::table("user")." as u on f.f_uid = u.uid where f.uid = ".intval($GLOBALS['fanwe']->var['uid']);
				
		$page_size = 21;
			
		$count=FDB::resultFirst($sql_count);
		$pager = buildPage('message/target',array(),$count,$_FANWE['page'],$page_size);
		$sql  = $sql." limit ".$pager['limit'];	
		
		$follow_user = FDB::fetchAll($sql);
		
		include template('page/message_target');
					
		display();		
	}
	
	public function pm()
	{
		define("ACTION_NAME","pm");
		if(intval($GLOBALS['fanwe']->var['uid'])==0)
		{			
			fHeader("location: ".FU('user/login'));	
		}
		$uname = addslashes($_POST['uname']);
		$uid = intval(FDB::resultFirst("select uid from ".FDB::table("user")." where user_name = '".$uname."'"));
		fHeader("location: ".FU('message/send',array('uid'=>$uid)));	
	}
	public function send()
	{
		define("ACTION_NAME","send");
		if(intval($GLOBALS['fanwe']->var['uid'])==0)
		{			
			fHeader("location: ".FU('user/login'));	
		}
		global $_FANWE;
		$uid = intval($_REQUEST['uid']);
		
		$sql = "select u.uid,u.user_name from ".FDB::table("user")." as u left join ".
				FDB::table("user_follow")." as f on f.f_uid = u.uid where u.uid = ".$uid." and f.uid = ".intval($GLOBALS['fanwe']->var['uid']);
				
		$user = FDB::fetchFirst($sql); 

		if($user)
		{
			include template('page/message_send');					
			display();		
		}
		else
		{
			showError("只能给fans发私信","只能给fans发私信");
		}	
	}
	
	public function dosend()
	{
		define("ACTION_NAME","dosend");
		if(intval($GLOBALS['fanwe']->var['uid'])==0)
		{			
			fHeader("location: ".FU('user/login'));	
		}
		global $_FANWE;
		$uid = intval($_REQUEST['uid']);
		
		$sql = "select u.uid,u.user_name from ".FDB::table("user")." as u left join ".
				FDB::table("user_follow")." as f on f.f_uid = u.uid where u.uid = ".$uid." and f.uid = ".intval($GLOBALS['fanwe']->var['uid']);
				
		$user = FDB::fetchFirst($sql); 
		if($user)
		{
			$msg['title'] = addslashes(htmlspecialchars(trim($_POST['message_title'])));
			$msg['content'] = addslashes(htmlspecialchars(trim($_POST['message_content'])));	
			$msg['author_id'] = intval($GLOBALS['fanwe']->var['uid']);			
			$msg['create_time'] = fGmtTime();
			$msg['status'] = 1;
			if($msg['title']=='')
			showError("标题不能为空");
			if($msg['content']=='')
			showError("内容不能为空");			
			$mid = FDB::insert("user_msg",$msg,true);
			if($mid>0)
			{
				$rel_data['mid'] = $mid;
				$rel_data['uid'] = $user['uid'];
				FDB::insert("user_msg_rel",$rel_data,true);
				showSuccess("成功发送","成功发送");	
			}			
		}
		else
		{
			showError("只能给fans发私信","只能给fans发私信");
		}	
	}	
	
	public function read()
	{
		define("ACTION_NAME","read");
		global $_FANWE;
		
		if(intval($GLOBALS['fanwe']->var['uid'])==0)
		{			
			fHeader("location: ".FU('user/login'));	
		}
		
		$mid = intval($_REQUEST['mid']);
		$uid = intval($GLOBALS['fanwe']->var['uid']);
		$sql = "select m.mid,m.title,m.content,m.create_time,au.uid as auid,au.user_name as auser_name,u.uid,u.user_name from ".FDB::table("user_msg")." as m left join ".
				FDB::table("user_msg_rel")." as ml on ml.mid = m.mid left join ".
				FDB::table("user")." as u on u.uid = ml.uid left join ".
				FDB::table("user")." as au on au.uid = m.author_id ".
				" where m.mid = ".$mid." and m.status = 1 and (u.uid = ".$uid." or au.uid = ".$uid.")";
		$msg = FDB::fetchFirst($sql);
		
		if($msg)
		{
			include template('page/message_read');					
			display();
		}
		else
		{
			showError("不存在的信息","不存在的信息");
		}
	}
	
	
	public function reply()
	{
		define("ACTION_NAME","reply");
		if(intval($GLOBALS['fanwe']->var['uid'])==0)
		{			
			fHeader("location: ".FU('user/login'));	
		}
		global $_FANWE;
		$mid = intval($_REQUEST['mid']);
		
		$sql = "select m.mid,m.title,m.content,m.create_time,au.uid as auid,au.user_name as auser_name,u.uid,u.user_name from ".FDB::table("user_msg")." as m left join ".
				FDB::table("user_msg_rel")." as ml on ml.mid = m.mid left join ".
				FDB::table("user")." as u on u.uid = ml.uid left join ".
				FDB::table("user")." as au on au.uid = m.author_id ".
				" where m.mid = ".$mid." and m.status = 1 and ml.uid = ".intval($GLOBALS['fanwe']->var['uid']);
				
		$msg = FDB::fetchFirst($sql); 

		if($msg)
		{
			include template('page/message_reply');					
			display();		
		}
		else
		{
			showError("非法的私信","非法的私信");
		}	
	}
	
	public function delete()
	{
		if(intval($GLOBALS['fanwe']->var['uid'])==0)
		{			
			fHeader("location: ".FU('user/login'));	
		}
		$id = $_REQUEST['id'];

		FDB::query("delete from ".FDB::table("user_msg_rel")." where mid in (".$id.")");
		FDB::query("delete from ".FDB::table("user_msg")." where mid in (".$id.") and author_id > 0");
	}
}
?>