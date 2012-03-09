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
 * 前台菜单分类
 +------------------------------------------------------------------------------
 */
class NavCategoryAction extends CommonAction
{
	public function index()
	{
		$list = D('NavCategory')->order('sort ASC,id ASC')->findAll();
		$list = D('NavCategory')->toFormatTree($list,'name','id','parent_id');
		$this->assign("list",$list);
		$this->display ();
	}
	
	public function add()
	{
		$cate_list = D('NavCategory')->where('parent_id = 0')->order('sort ASC,id ASC')->findAll();
		$cate_list = D('NavCategory')->toFormatTree($cate_list,'name','id');
		$this->assign("cate_list",$cate_list);
		parent::add();
	}
	
	public function edit()
	{
		$id = intval($_REQUEST['id']);
		$vo = D("NavCategory")->getById($id);
		$this->assign ( 'vo', $vo );
		$childs = D("NavCategory")->getChildIds($id);
		$childs[] = $id;
		$cate_list = D("NavCategory")->where('id not in ('.implode(',', $childs).')')->field('id,parent_id,name')->order('sort ASC,id ASC')->findAll();
		$cate_list = D("NavCategory")->toFormatTree($cate_list,array('name'),'id');
		
		$this->assign("cate_list",$cate_list);
		$this->display();
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
			if($model->where(array ("parent_id" => array ('in', explode ( ',', $id ) ) ))->count()>0)
			{
				$result['isErr'] = 1;
				$result['content'] = L('CATE_EXIST_CHILD');
				die(json_encode($result));
			}
			
			if($model->where(array("id"=>array('in',explode(',',$id)),'is_fix'=>1))->count()>0)
			{
				$result['isErr'] = 1;
				$result['content'] = L('CATE_FIX_DEL');
				die(json_encode($result));
			}
			
			if(D("Nav")->where(array ("cid" => array ('in', explode ( ',', $id ) ) ))->count()>0)
			{
				$result['isErr'] = 1;
				$result['content'] = L('CATE_EXIST_NAV');
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