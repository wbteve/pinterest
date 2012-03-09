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
class AskPostAction extends CommonAction
{
	public function index()
	{
		if(isset($_REQUEST['tid']))
			$tid = intval($_REQUEST['tid']);
		else
			$tid = intval($_SESSION['ask_post_tid']);
		
		$_SESSION['ask_post_tid'] = $tid;
		
		$where = 'WHERE ap.tid = ' . $tid;
		$parameter = array();
		$uname = trim($_REQUEST['uname']);

		if(!empty($uname))
		{
			$this->assign("uname",$uname);
			$parameter['uname'] = $uname;
			$match_key = segmentToUnicodeA($uname,'+');
			$where.=" AND match(u.user_name_match) against('".$match_key."' IN BOOLEAN MODE) ";
		}

		$model = M();
		
		$sql = 'SELECT COUNT(DISTINCT ap.pid) AS pcount 
			FROM '.C("DB_PREFIX").'ask_post AS ap 
			LEFT JOIN '.C("DB_PREFIX").'user AS u ON u.uid = ap.uid 
			'.$where;

		$count = $model->query($sql);
		$count = $count[0]['pcount'];

		$sql = 'SELECT ap.pid,LEFT(ap.content,80) AS content,u.user_name,ap.create_time,ap.share_id  
			FROM '.C("DB_PREFIX").'ask_post AS ap 
			LEFT JOIN '.C("DB_PREFIX").'user AS u ON u.uid = ap.uid 
			'.$where.' GROUP BY ap.pid';
		$this->_sqlList($model,$sql,$count,$parameter,'ap.pid',false,'returnUrl1');
		
		$this->display ();
		return;
	}

    public function update()
    {
        Vendor("common");
        $pid = intval($_REQUEST['pid']);
        $share_id = D('AskPost')->where("pid = '$pid'")->getField('share_id');
        if($share_id > 0)
        {
            $content = trim($_REQUEST['content']);
            FS("Share")->updateShare($share_id,'',$content);
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
			$count = $model->where ( $condition )->count();
			$res = $model->where ( $condition )->findAll();

			foreach($res as $item)
			{
                $share_id = intval($item['share_id']);
                FS("Ask")->deletePost($share_id);
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

	public function edit()
	{
		Cookie::set ( '_currentUrl_',NULL );
		parent::edit();
	}
}

?>