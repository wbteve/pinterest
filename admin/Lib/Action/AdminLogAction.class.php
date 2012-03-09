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
 * 后台日志
 +------------------------------------------------------------------------------
 */
class AdminLogAction extends CommonAction
{
	public function index()
	{
		$where = '';
		$parameter = array();
		$begin_time = trim($_REQUEST['begin_time']);
		$end_time = trim($_REQUEST['end_time']);
		
		if(!empty($begin_time))
		{
			$this->assign("begin_time",$begin_time);
			$parameter['begin_time'] = $begin_time;
			$begin_time = strZTime($begin_time);
			$where .= " AND log_time >= '$begin_time'";
		}
		else
			$begin_time = 0;
		
		if(!empty($end_time) && strZTime($end_time) > $begin_time)
		{
			$this->assign("end_time",$end_time);
			$parameter['end_time'] = $end_time;
			$end_time = strZTime($end_time);
			$where .= " AND log_time <= '$end_time'";
		}
		
		$model = M();
		
		if(!empty($where))
			$where = 'WHERE 1' . $where;
		
		$sql = 'SELECT COUNT(id) AS tcount FROM '.C("DB_PREFIX").'admin_log '.$where;
		
		$count = $model->query($sql);
		$count = $count[0]['tcount'];
		
		$sql = 'SELECT * FROM '.C("DB_PREFIX").'admin_log '.$where;
			
		$this->_sqlList($model,$sql,$count,$parameter);
		$this->display();
	}
	
	public function clear()
	{
		$where = '';
		$begin_time = trim($_REQUEST['begin_time']);
		$end_time = trim($_REQUEST['end_time']);
		
		if(!empty($begin_time))
		{
			$begin_time = strZTime($begin_time);
			$where .= " AND log_time >= '$begin_time'";
		}
		else
			$begin_time = 0;
		
		if(!empty($end_time) && strZTime($end_time) > $begin_time)
		{
			$end_time = strZTime($end_time);
			$where .= " AND log_time <= '$end_time'";
		}
		
		$model = M();
		
		if(!empty($where))
			$where = 'WHERE 1' . $where;
		
		$sql = 'DELETE FROM '.C("DB_PREFIX").'admin_log '.$where;
		
		M()->query($sql);
		$this->redirect('AdminLog/index');
	}
}

function getResult($result)
{
	return L('LOG_RESULT_'.$result);
}

function getAdminName($id)
{
	return D("Admin")->where('id = '.$id)->getField('admin_name');
}
?>