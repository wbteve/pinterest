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
 * 话题
 +------------------------------------------------------------------------------
 */
class EventAction extends CommonAction
{
    function index() {
    	$where = '';
		$parameter = array();
		$keyword = trim($_REQUEST['keyword']);
		
    	if($keyword){
    		$this->assign("keyword",$keyword);
			$parameter['keyword'] = $keyword;
			$where .= " WHERE e.title like '%$keyword%'";
    	}
    	
    	$model = D("Event");
    	$sql = "select count(DISTINCT e.id) as scount from ".C("DB_PREFIX")."event as e Left join ".C("DB_PREFIX")."user as u on e.uid=u.uid $where ";
    	$count = $model->query($sql);
		$count = $count[0]['scount'];
		
    	$sql = "select e.*,u.user_name from ".C("DB_PREFIX")."event as e Left join ".C("DB_PREFIX")."user as u on e.uid=u.uid $where GROUP BY e.id ";
    	
    	$this->_sqlList($model,$sql,$count,$parameter);
    	$this->display();
    }
    
    public function remove()
	{
		//删除指定记录
		$result = array('isErr'=>0,'content'=>'');
		$id = $_REQUEST['id'];
		if(!empty($id))
		{
			Vendor('common');
			$model = D("Event");
			$pk = $model->getPk ();
			$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
			$list = $model->where ( $condition )->findAll();
			
			if(false !== $model->where ( $condition )->delete())
			{
				foreach($list as $k=>$v)
				{
					D("Share")->removeHandler($v['share_id']);
					$post_share_ids = D("EventShare")->where("event_id=".$v['id'])->field("share_id")->findAll();
					foreach($post_share_ids as $k => $share_ids){
						D("Share")->removeHandler($share_ids['share_id']);
					}
					$model->where ("event_id=".$v['id'])->delete();
				}
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