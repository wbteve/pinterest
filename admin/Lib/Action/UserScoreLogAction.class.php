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
 会员积分日志
 +------------------------------------------------------------------------------
 */
class UserScoreLogAction extends CommonAction
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
			$where .= " AND usl.create_day >= '".$begin_time."'";
		}
		
		if ($end_time > 0)
		{
			$this->assign("end_time",$end_time_str);
			$parameter['end_time'] = $end_time_str;
			$where .= " AND usl.create_day < '".($end_time + 86400)."'";
		}

		$model = M();

		if(!empty($where))
			$where = 'WHERE 1' . $where;

		$sql = 'SELECT COUNT(DISTINCT usl.id) AS tcount
			FROM '.C("DB_PREFIX").'user_score_log AS usl 
			LEFT JOIN '.C("DB_PREFIX").'user AS u ON u.uid = usl.uid 
			'.$where;

		$count = $model->query($sql);
		$count = $count[0]['tcount'];

		$sql = 'SELECT usl.*,u.user_name   
			FROM '.C("DB_PREFIX").'user_score_log AS usl 
			LEFT JOIN '.C("DB_PREFIX").'user AS u ON u.uid = usl.uid 
			'.$where.' GROUP BY usl.id';
		$this->_sqlList($model,$sql,$count,$parameter,'usl.id');
		$list = $this->get('list');

		foreach($list as $k=>$v)
		{
			if($v['score']>=0)
				$list[$k]['inc_score'] = abs($v['score']);
			else
				$list[$k]['dec_score'] = abs($v['score']);
		}
		$this->assign('list',$list);
		$this->display();
		return;
	}
}
?>