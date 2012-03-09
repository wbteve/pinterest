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

 +------------------------------------------------------------------------------
 */
class AskThreadAction extends CommonAction
{
	public function index()
	{
		$where = '';
		$parameter = array();
		$keyword = trim($_REQUEST['keyword']);
		$uname = trim($_REQUEST['uname']);
		$aid = intval($_REQUEST['aid']);

		if(!empty($keyword))
		{
			$this->assign("keyword",$keyword);
			$parameter['keyword'] = $keyword;
			$where.=" AND at.title LIKE '%".mysqlLikeQuote($keyword)."%' ";
		}

		if(!empty($uname))
		{
			$this->assign("uname",$uname);
			$parameter['uname'] = $uname;
			$match_key = segmentToUnicodeA($uname,'+');
			$where.=" AND match(u.user_name_match) against('".$match_key."' IN BOOLEAN MODE) ";
		}
		
		if($aid > 0)
		{
			$this->assign("aid",$aid);
			$parameter['aid'] = $aid;
			$where .= " AND at.aid = $aid";
		}

		$model = M();

		if(!empty($where))
			$where = 'WHERE 1' . $where;

		$sql = 'SELECT COUNT(DISTINCT at.tid) AS tcount
			FROM '.C("DB_PREFIX").'ask_thread AS at 
			LEFT JOIN '.C("DB_PREFIX").'user AS u ON u.uid = at.uid 
			'.$where;

		$count = $model->query($sql);
		$count = $count[0]['tcount'];

		$sql = 'SELECT at.tid,LEFT(at.title,80) AS title,u.user_name,at.create_time,at.post_count,at.is_top,at.is_best,
			a.name AS aname,at.share_id  
			FROM '.C("DB_PREFIX").'ask_thread AS at 
			LEFT JOIN '.C("DB_PREFIX").'user AS u ON u.uid = at.uid 
			LEFT JOIN '.C("DB_PREFIX").'ask AS a ON a.aid = at.aid 
			'.$where.' GROUP BY at.tid';
			
		$this->_sqlList($model,$sql,$count,$parameter,'at.tid');
		
		$cate_tree = M("Ask")->order('sort asc,aid asc')->findAll();
		$this->assign("cate_tree",$cate_tree);
		$this->display ();
		return;
	}

	public function edit()
	{
		$cate_tree = M("Ask")->findAll();
		$this->assign("cate_tree",$cate_tree);
		Cookie::set ( '_currentUrl_',NULL );
		parent::edit();
	}

    public function update()
    {
        Vendor("common");
        $tid = intval($_REQUEST['tid']);
		$topic = D('AskThread')->where("tid = '$tid'")->find();
        if($topic['share_id'] > 0)
        {
			$share_id = $topic['share_id'];
            $title = trim($_REQUEST['title']);
            $content = trim($_REQUEST['content']);
			if($topic['title'] != $title || $topic['content'] != $content)
            	FS("Share")->updateShare($share_id,$title,$content);

			if($topic['title'] != $title)
				FS("Ask")->updateTopicRec($tid,$title);
        }
        parent::update();
    }

	public function remove()
	{
		//删除指定记录
		Vendor("common");
		$result = array('isErr'=>0,'content'=>'');
		$id = $_REQUEST['id'];
		if(!empty($id))
		{
			$name=$this->getActionName();
			$model = D($name);
			$pk = $model->getPk ();
			$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
			$res = $model->where ( $condition )->findAll();

			foreach($res as $item)
			{
				FS("Ask")->deleteTopic(intval($item['share_id']));
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


function getAskName($aid)
{
	return M("Ask")->where("aid=".$aid)->getField("name");
}

function getPostCount($count,$tid)
{
	if($count>0)
		return "(".$count.")&nbsp;&nbsp; <a href='".u("AskPost/index",array("tid"=>$tid))."'>".l("CHECK_REPLY")."</a>";
	else
		return $count;
}

?>