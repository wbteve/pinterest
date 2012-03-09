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
class ShopCategoryAction extends CommonAction
{
	public function index()
	{
		$cate_tree = D("ShopCategory")->order('sort asc,id asc')->findAll();
		$cate_tree = D("ShopCategory")->toFormatTree($cate_tree,'name','id','parent_id');
		$this->assign("list",$cate_tree);
		$this->display ();
		return;
	}
	
	public function add()
	{	
		$cate_tree = D("ShopCategory")->where('parent_id = 0')->order('sort asc,id asc')->findAll();
		$this->assign("cate_tree",$cate_tree);
		parent::add();
	}
	
	public function edit()
	{	
		$id = intval($_REQUEST['id']);
		
		$ids = D("ShopCategory")->getChildIds($id,'id','parent_id');
		$ids[] = $id;
		$condition['parent_id'] = 0;
		$condition['id'] = array('not in',$ids);

		$cate_tree = D("ShopCategory")->where($condition)->order('sort asc,id asc')->findAll();
		$this->assign("cate_tree",$cate_tree);
		
		$vo = D("ShopCategory")->getById($id);
		
		$this->assign ( 'vo', $vo );
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
				$result['content'] = L('SUB_CATE_EXIST');
				die(json_encode($result));
			}
			
			if(M("Shop")->where(array ("cate_id" => array ('in', explode ( ',', $id ) ) ))->count()>0)
			{
				$result['isErr'] = 1;
				$result['content'] = L('SUB_SHOP_EXIST');
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