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
class ForumThreadAction extends CommonAction
{
	public function index()
	{
		$where = '';
		$parameter = array();
		$keyword = trim($_REQUEST['keyword']);
		$uname = trim($_REQUEST['uname']);
		$fid = intval($_REQUEST['fid']);

		if(!empty($keyword))
		{
			$this->assign("keyword",$keyword);
			$parameter['keyword'] = $keyword;
			$where.=" AND ft.title LIKE '%".mysqlLikeQuote($keyword)."%' ";
		}

		if(!empty($uname))
		{
			$this->assign("uname",$uname);
			$parameter['uname'] = $uname;
			$match_key = segmentToUnicodeA($uname,'+');
			$where.=" AND match(u.user_name_match) against('".$match_key."' IN BOOLEAN MODE) ";
		}
		
		if($fid > 0)
		{
			$this->assign("fid",$fid);
			$parameter['fid'] = $fid;
			$where .= " AND ft.fid = $fid";
		}

		$model = M();

		if(!empty($where))
			$where = 'WHERE 1' . $where;

		$sql = 'SELECT COUNT(DISTINCT ft.tid) AS tcount
			FROM '.C("DB_PREFIX").'forum_thread AS ft 
			LEFT JOIN '.C("DB_PREFIX").'user AS u ON u.uid = ft.uid 
			'.$where;

		$count = $model->query($sql);
		$count = $count[0]['tcount'];

		$sql = 'SELECT ft.tid,LEFT(ft.title,80) AS title,u.user_name,ft.create_time,ft.post_count,ft.is_top,ft.is_best,
			ft.is_event,f.name AS fname,ft.share_id  
			FROM '.C("DB_PREFIX").'forum_thread AS ft 
			LEFT JOIN '.C("DB_PREFIX").'user AS u ON u.uid = ft.uid 
			LEFT JOIN '.C("DB_PREFIX").'forum AS f ON f.fid = ft.fid 
			'.$where.' GROUP BY ft.tid';
		$this->_sqlList($model,$sql,$count,$parameter,'ft.tid');
		
		$cate_tree = M("Forum")->findAll();
		$cate_tree = D("Forum")->toFormatTree($cate_tree,'name','fid','parent_id');
		$this->assign("cate_tree",$cate_tree);
		$this->display ();
		return;
	}

	public function edit()
	{
		$cate_tree = M("Forum")->where()->findAll();
		$cate_tree = D("Forum")->toFormatTree($cate_tree,'name','fid','parent_id');
		$this->assign("cate_tree",$cate_tree);
		Cookie::set ( '_currentUrl_',NULL );
		parent::edit();
	}

	public function update()
	{
        Vendor("common");
		$_POST['is_best'] = intval($_POST['is_best']);
		$_POST['is_top'] = intval($_POST['is_top']);
		$_POST['is_event'] = intval($_POST['is_event']);
        $tid = intval($_REQUEST['tid']);
        $topic = D('ForumThread')->where("tid = '$tid'")->find();
		
        if($topic['share_id'] > 0)
        {
			$share_id = $topic['share_id'];
            $title = trim($_REQUEST['title']);
            $content = trim($_REQUEST['content']);
			if($topic['title'] != $title || $topic['content'] != $content)
            	FS("Share")->updateShare($share_id,$title,$content);
			
			if($topic['title'] != $title)
				FS("Topic")->updateTopicRec($tid,$title);
			
            FS("Topic")->updateTopicCache($tid);
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
            $ids = explode (',',$id);
            foreach($ids as $tid)
            {
                FS("Topic")->deleteTopic($tid);
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


function getForumName($fid)
{
	return M("Forum")->where("fid=".$fid)->getField("name");
}

function getPostCount($count,$tid)
{
	if($count>0)
		return "(".$count.")&nbsp;&nbsp; <a href='".u("ForumPost/index",array("tid"=>$tid))."'>".l("CHECK_REPLY")."</a>";
	else
		return $count;
}

?>