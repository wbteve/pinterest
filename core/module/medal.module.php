<?php
class MedalModule
{
	public function index()
	{			
		global $_FANWE;
		$_FANWE['nav_title'] = lang('common','user_medal');
		$type = (int)$_FANWE['request']['type'];
		$medal_uid = (int)$_FANWE['request']['uid'];
		if($medal_uid > 0)
			$medal_names = FS('User')->getUserShowName($medal_uid);
		
		$medal_list = array();
		switch($type)
		{
			case 1:
				foreach( $_FANWE['cache']['medals']['auto'] as $mid)
				{
					$medal_list[] = $_FANWE['cache']['medals']['all'][$mid];
				}
			break;
			case 2:
				foreach( $_FANWE['cache']['medals']['manual'] as $mid)
				{
					$medal_list[] = $_FANWE['cache']['medals']['all'][$mid];
				}
			break;
			default:
				$medal_list = $_FANWE['cache']['medals']['all'];
			break;
		}
		
		if($_FANWE['uid'] > 0 && count($medal_list) > 0)
			FS('Medal')->medalFormat($_FANWE['uid'],$medal_list);
		
		include template('page/medal/medal_index');		
		display();
	}
	
	public function u()
	{			
		global $_FANWE;
		$_FANWE['nav_title'] = lang('common','user_medal');
		$medal_uid = (int)$_FANWE['request']['uid'];
		if($medal_uid > 0)
			$medal_names = FS('User')->getUserShowName($medal_uid);
		else
			fHeader("location: ".FU('medal/index'));
		
		$medal_list = array();
		$award_list = FS('Medal')->getAwardsByUid($medal_uid);
		foreach($award_list as $mid=>$award)
		{
			$medal_list[] = $_FANWE['cache']['medals']['all'][$mid];
		}
		
		if($_FANWE['uid'] > 0 && count($medal_list) > 0)
			FS('Medal')->medalFormat($_FANWE['uid'],$medal_list);
		
		include template('page/medal/medal_index');
		display();
	}
	
	public function apply()
	{
		global $_FANWE;
		$_FANWE['nav_title'] = lang('common','user_medal');
		$mid = (int)$_FANWE['request']['mid'];
		$medal_list = array();
		$current_medal = NULL;
		foreach( $_FANWE['cache']['medals']['manual'] as $id)
		{
			$medal = $_FANWE['cache']['medals']['all'][$id];
			if($_FANWE['uid'] > 0 && !FS('Medal')->checkAllowGroup($_FANWE['gid'],$medal['allow_group']))
				continue;
			
			if($mid == $id)
				$current_medal = $medal;
			
			if($current_medal === NULL)
				$current_medal = $medal;
			
			$medal_list[] = $medal;
		}
		include template('page/medal/medal_apply');		
		display();
	}
	
	public function save()
	{
		global $_FANWE;
		if($_FANWE['uid'] == 0)
			exit;
		
		$result = FS('Medal')->applyMedal($_FANWE['uid'],(int)$_FANWE['request']['mid'],htmlspecialchars($_FANWE['request']['reason']));
		list($status,$error) = $result;
		
		if($status === false)
			showError('提交申请失败',$error,-1);
		else
			showSuccess('提交申请成功','你的勋章申请已经成功提交，我们会尽快处理你的勋章申请！',FU('medal/index',array('uid'=>$_FANWE['uid'])));
	}
}
?>