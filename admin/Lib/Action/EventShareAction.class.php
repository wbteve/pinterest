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
 * 话题回复
 +------------------------------------------------------------------------------
 */
class EventShareAction extends CommonAction
{
    function index() {
    	$where = '';
		$parameter = array();
		$keyword = trim($_REQUEST['keyword']);
		$id = intval($_REQUEST['id']);
    	if($keyword){
    		$this->assign("keyword",$keyword);
			$parameter['keyword'] = $keyword;
			$where .= " AND s.content like '%$keyword%'";
    	}
    	
    	if($id > 0)
    	{
    		$this->assign("id",$id);
			$parameter['event_id'] = $id;
			$where .= " AND e.event_id = ".$id;
    	}
    	
    	if (!isset ( $_REQUEST ['_order'] )) 
			$_REQUEST ['_order'] = " s.share_id ";
    	
    	$model = D("EventShare");
    	$sql = "select count(DISTINCT s.share_id) as scount from ".C("DB_PREFIX")."event_share as e Left join ".C("DB_PREFIX")."user as u on e.uid=u.uid left join ".C("DB_PREFIX")."share as s on s.share_id =e.share_id  where 1=1 $where ";
    	
    	$count = $model->query($sql);
		$count = $count[0]['scount'];
		
    	$sql = "select e.*,s.content,s.create_time,u.user_name from ".C("DB_PREFIX")."event_share as e Left join ".C("DB_PREFIX")."user as u on e.uid=u.uid left join ".C("DB_PREFIX")."share as s on s.share_id =e.share_id where 1=1 $where GROUP BY s.share_id ";
    	
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
			$model = D("EventShare");
			$condition = array ("share_id" => array ('in', explode ( ',', $id ) ) );
			$list = $model->where ( $condition )->findAll();
			
			if(false !== $model->where ( $condition )->delete())
			{
				foreach($list as $k => $v){
					M("Event")->setDec('thread_count','id='.$v['event_id']); // 减1
				} 
				D("Share")->removeHandler($id);
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
