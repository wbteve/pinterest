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
 * 搭配分类
 +------------------------------------------------------------------------------
 */
class StyleCategoryAction extends CommonAction
{
	public function index()
	{
		if(isset($_REQUEST['parent_id']))
			$parent_id = intval($_REQUEST['parent_id']);
		else
			$parent_id = intval($_SESSION['style_category_parent_id']);
		
		$_SESSION['style_category_parent_id'] = $parent_id;
		
		$map['parent_id'] = $parent_id;
		$model = D("StyleCategory");
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
					FROM '.C("DB_PREFIX").'style_category as gc 
					LEFT JOIN '.C("DB_PREFIX").'style_category as gc1 ON gc1.parent_id = gc.cate_id 
					WHERE gc.parent_id = '.$parent_id.' GROUP BY gc.cate_id';
			
			$this->_sqlList($model,$sql,$count,$map,'gc.cate_id',true);
		}
		$this->display ();
		return;
	}
	
	public function add()
	{
		$cate_list = D("StyleCategory")->where('status = 1 AND parent_id = 0')->field('cate_id,parent_id,cate_name')->order('sort ASC,cate_id ASC')->findAll();
		$cate_list = D("StyleCategory")->toFormatTree($cate_list,array('cate_name'),'cate_id');
		$this->assign("cate_list",$cate_list);
		$this->display();
	}
	
	public function edit()
	{
		$id = intval($_REQUEST['cate_id']);
		$vo = D("StyleCategory")->getById($id);
		$this->assign ( 'vo', $vo );
		
		$childs = D("StyleCategory")->getChildIds($id,'cate_id');
		$childs[] = $id;
		$cate_list = D("StyleCategory")->where('status = 1 AND parent_id = 0 AND cate_id not in ('.implode(',', $childs).')')->field('cate_id,parent_id,cate_name')->order('sort ASC,cate_id ASC')->findAll();
		$cate_list = D("StyleCategory")->toFormatTree($cate_list,array('cate_name'),'cate_id');
		
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
			$model = D("StyleCategory");
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
		return '<a href="'.U('StyleCategory/index',array('parent_id'=>$id)).'">'.L('SHOW_CHILD').'</a>';
	else
		return '';
}

function getTagsLink($id)
{
	return '<a href="'.U('StyleCategoryTags/index',array('cate_id'=>$id)).'">'.L('SETTING_CATE_TAG').'</a>';
}

function getDelLink($is_fix,$id)
{
	if($is_fix == 1)
		return '';
	else
		return '<a href="javascript:;" onclick="removeData(this,'.$id.',\'cate_id\')">'.L('REMOVE').'</a>&nbsp;&nbsp;';	
}
?>