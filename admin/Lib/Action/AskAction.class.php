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
class AskAction extends CommonAction
{
	public function index()
	{
		$list = D("Ask")->order('aid asc')->findAll();
		$this->assign("list",$list);
		$this->display();
	}
	
	public function add()
	{
		parent::add();
	}
	
	public function insert()
	{
		$_POST['create_time'] = gmtTime();
		$_POST['status'] = 1;
		parent::insert();
	}

	
	public function remove()
	{
		//删除指定记录
		$result = array('isErr'=>0,'content'=>'');
		$id = $_REQUEST['id'];
		if(!empty($id))
		{
			$name=$this->getActionName();
			$model = D($name);
			$pk = $model->getPk ();
			$condition = array ($pk => array ('in', explode ( ',', $id ) ) );

			if(M("AskThread")->where(array ("aid" => array ('in', explode ( ',', $id ) ) ))->count()>0)
			{
				$result['isErr'] = 1;
				$result['content'] = L('THREAD_EXIST');
				die(json_encode($result));
			}
			if(false !== $model->where ( $condition )->delete ())
			{
				$this->saveLog(1,$id);
			}
			else
			{
				$this->saveLog(0,$id);
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