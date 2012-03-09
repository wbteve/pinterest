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
 * 城市
 +------------------------------------------------------------------------------
 */
class RegionAction extends CommonAction
{
	public function index()
	{
		if(isset($_REQUEST['parent_id']))
			$parent_id = intval($_REQUEST['parent_id']);
		else
			$parent_id = intval($_SESSION['region_parent_id']);
		
		$_SESSION['region_parent_id'] = $parent_id;
		
		$map['parent_id'] = $parent_id;
		$model = D("Region");
		$this->assign("parent_id",$parent_id);
		if($parent_id > 0)
		{
			$pp_id = $model->where('id = '.$parent_id)->getField('parent_id');
			$this->assign("pp_id",$pp_id);
		}
		
		$count = $model->where('parent_id = '.$parent_id)->count('id');
		$sql = 'SELECT r.*,COUNT(r1.id) AS child 
				FROM '.C("DB_PREFIX").'region as r 
				LEFT JOIN '.C("DB_PREFIX").'region as r1 ON r1.parent_id = r.id 
				WHERE r.parent_id = '.$parent_id.' GROUP BY r.id';
		
		$this->_sqlList($model,$sql,$count,$map,'id',true);
		$this->display ();
		return;
	}
	
	public function add()
	{
		$list = D("Region")->where('parent_id = 0')->field('id,parent_id,name')->findAll();
		$list = D("Region")->toFormatTree($list,array('name'),'id');
		$this->assign("list",$list);
		$this->display();
	}
	
	public function edit()
	{
		$id = intval($_REQUEST['id']);
		$vo = D("Region")->getById($id);
		$this->assign ( 'vo', $vo );
		
		$list = D("Region")->where('parent_id = 0 AND id <> '.$id)->field('id,parent_id,name')->findAll();
		$list = D("Region")->toFormatTree($list,array('name'),'id');
		$this->assign("list",$list);
		$this->display();
	}
	
	public function remove()
	{
		//删除指定记录
		$result = array('isErr'=>0,'content'=>'');
		$id = $_REQUEST['id'];
		if(!empty($id))
		{
			$model = D("Region");
			$condition = array('id' => array('in',explode (',',$id)));
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
	
	public function getCitys()
	{
		$pid = intval($_REQUEST['pid']);
		$city_list = D("Region")->where('parent_id = '.$pid)->findAll();
		die(json_encode($city_list));
	}
}

function getChildLink($child,$id)
{
	if($child > 0)
		return '<a href="'.U('Region/index',array('parent_id'=>$id)).'">'.L('SHOW_CHILD').'</a>';
	else
		return '';
}

function getCityName($id)
{
	if($id == 0)
		return L('PARENT_ID_0');
	
	return D("Region")->where('id = '.$id)->getField('name');
}
?>