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
 * 商品分类管理
 +------------------------------------------------------------------------------
 */
class GoodsCategoryAction extends CommonAction
{
	public function index()
	{
		if(isset($_REQUEST['parent_id']))
			$parent_id = intval($_REQUEST['parent_id']);
		else
			$parent_id = intval($_SESSION['goods_category_parent_id']);
		
		$_SESSION['goods_category_parent_id'] = $parent_id;
		
		$map['parent_id'] = $parent_id;
		$model = D("GoodsCategory");
		if (! empty ( $model ))
		{
			$this->assign("parent_id",$parent_id);
			if($parent_id > 0)
			{
				$pp_id = $model->where('cate_id = '.$parent_id)->getField('parent_id');
				$this->assign("pp_id",$pp_id);
			}
			
			$count = $model->where('parent_id = '.$parent_id)->count('cate_id');
			$sql = 'SELECT gc.*,COUNT(gc1.cate_id) AS child 
					FROM '.C("DB_PREFIX").'goods_category as gc 
					LEFT JOIN '.C("DB_PREFIX").'goods_category as gc1 ON gc1.parent_id = gc.cate_id 
					WHERE gc.parent_id = '.$parent_id.' GROUP BY gc.cate_id';
			
			$this->_sqlList($model,$sql,$count,$map);
		}
		$this->display ();
		return;
	}
	
	public function add()
	{
		$cate_list = D("GoodsCategory")->where('status = 1 AND parent_id = 0')->field('cate_id,parent_id,cate_name')->order('sort ASC,cate_id ASC')->findAll();
		$this->assign("cate_list",$cate_list);
		$this->display();
	}
	
	public function insert()
	{
		$_POST['is_root'] = isset($_POST['is_root']) ? intval($_POST['is_root']) : 0;
		$_POST['is_index'] = isset($_POST['is_index']) ? intval($_POST['is_index']) : 0;
		
		$model = D("GoodsCategory");
		if(false === $data = $model->create())
		{
			$this->error($model->getError());
		}
		
		//保存当前数据对象
		$list=$model->add($data);
		if ($list !== false)
		{
			if($_POST['is_root'] == 1)
				D("GoodsCategory")->where('cate_id <> '.$list)->setField('is_root',0);
				
			if($upload_list = $this->uploadImages())
			{
				$cate_icon = $upload_list[0]['recpath'].$upload_list[0]['savename'];
				D("GoodsCategory")->where('cate_id = '.$list)->setField('cate_icon',$cate_icon);
			}
			
			$this->saveLog(1,$list);
			$this->success (L('ADD_SUCCESS'));

		}
		else
		{
			$this->saveLog(0,$list);
			$this->error (L('ADD_ERROR'));
		}
	}
	
	public function edit()
	{
		$id = intval($_REQUEST['cate_id']);
		$vo = D("GoodsCategory")->getById($id);
		$this->assign ( 'vo', $vo );
		
		$childs = D("GoodsCategory")->getChildIds($id,'cate_id');
		$childs[] = $id;
		$cate_list = D("GoodsCategory")->where('status = 1 AND parent_id = 0 AND cate_id not in ('.implode(',', $childs).')')->field('cate_id,parent_id,cate_name')->order('sort ASC,cate_id ASC')->findAll();
		$cate_list = D("GoodsCategory")->toFormatTree($cate_list,array('cate_name'),'cate_id');
		
		$this->assign("cate_list",$cate_list);
		$this->display();
	}
	
	public function update()
	{
		$id = intval($_REQUEST['cate_id']);
		
		$_POST['is_root'] = isset($_POST['is_root']) ? intval($_POST['is_root']) : 0;
		$_POST['is_index'] = isset($_POST['is_index']) ? intval($_POST['is_index']) : 0;
		
		$model = D("GoodsCategory");
		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}
		
		// 更新数据
		$list=$model->save($data);
		if (false !== $list)
		{
			if($_POST['is_root'] == 1)
				D("GoodsCategory")->where('cate_id <> '.$id)->setField('is_root',0);
			
			if($upload_list = $this->uploadImages())
			{
				$cate_icon = $upload_list[0]['recpath'].$upload_list[0]['savename'];
				if(!empty($cate_icon))
				{
					$old_icon = D("GoodsCategory")->where('cate_id = '.$id)->getField('cate_icon');
					if(!empty($old_icon))
						@unlink(FANWE_ROOT.$old_icon);
						
					D("GoodsCategory")->where('cate_id = '.$id)->setField('cate_icon',$cate_icon);
				}
			}
				
			$this->saveLog(1,$id);
			$this->assign('jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success (L('EDIT_SUCCESS'));
		}
		else
		{
			//错误提示
			$this->saveLog(0,$id);
			$this->error (L('EDIT_ERROR'));
		}
	}
	
	public function remove()
	{
		//删除指定记录
		$result = array('isErr'=>0,'content'=>'');
		$id = $_REQUEST['id'];
		if(!empty($id))
		{
			$model = D("GoodsCategory");
			$condition = array('cate_id' => array('in',explode (',',$id)));
			$condition1 = array('parent_id' => array('in',explode (',',$id)));
			if($model->where($condition1)->count() > 0)
			{
				$result['isErr'] = 1;
				$result['content'] = L('CATE_EXIST_CHILD');
			}
			else
			{
				if(false !== $model->where ( $condition )->delete())
				{
					$this->saveLog(1,$id);
				}
				else
				{
					$result['isErr'] = 1;
					$result['content'] = L('REMOVE_ERROR');
				}
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

function getChildLink($child,$id)
{
	if($child > 0)
		return '<a href="'.U('GoodsCategory/index',array('parent_id'=>$id)).'">'.L('SHOW_CHILD').'</a>';
	else
		return '';
}

function getTagsLink($id)
{
	return '<a href="'.U('GoodsCategoryTags/index',array('cate_id'=>$id)).'">'.L('SETTING_CATE_TAG').'</a>';
}
?>