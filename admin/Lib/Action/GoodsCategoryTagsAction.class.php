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
 * 商品分类标签管理
 +------------------------------------------------------------------------------
 */
class GoodsCategoryTagsAction extends CommonAction
{
	public function index()
	{
		if(isset($_REQUEST['cate_id']))
			$cate_id = intval($_REQUEST['cate_id']);
		else
			$cate_id = intval($_SESSION['goods_category_tags_cate_id']);
		
		$_SESSION['goods_category_tags_cate_id'] = $cate_id;
		
		$parameter = array();
		$parameter['cate_id'] = $cate_id;
		$where = 'gct.cate_id = '.$cate_id;
		$this->assign("cate_id",$cate_id);
		
		$tag_name = trim($_REQUEST['tag_name']);
		if(!empty($tag_name))
		{
			$this->assign("tag_name",$tag_name);
			$parameter['tag_name'] = $tag_name;
			$where .= " AND gt.tag_name LIKE '%".mysqlLikeQuote($tag_name)."%'";
		}
		
		$model = M();
		
		$sql = 'SELECT COUNT(DISTINCT gt.tag_id) AS tcount FROM '.C("DB_PREFIX").'goods_category_tags as gct 
				LEFT JOIN '.C("DB_PREFIX").'goods_tags as gt ON gt.tag_id = gct.tag_id 
				WHERE '.$where;

		$count = $model->query($sql);
		$count = $count[0]['tcount'];
		
		$sql = 'SELECT gct.*,gt.tag_name FROM '.C("DB_PREFIX").'goods_category_tags as gct 
				LEFT JOIN '.C("DB_PREFIX").'goods_tags as gt ON gt.tag_id = gct.tag_id 
				WHERE '.$where;
		
		$this->_sqlList($model,$sql,$count,$parameter,'sort',true);
		
		$this->display ();
		return;
	}
	
	public function insert()
	{
		$cate_id = intval($_SESSION['goods_category_tags_cate_id']);
		if($cate_id == 0)
			exit;
		
		$category_tags = trim($_REQUEST['category_tags']);
		$custom_tags = trim($_REQUEST['custom_tags']);
		
		$tag_list = array();
		
		$sql = 'SELECT gct.*,gt.tag_name FROM '.C("DB_PREFIX").'goods_category_tags as gct 
			LEFT JOIN '.C("DB_PREFIX").'goods_tags as gt ON gt.tag_id = gct.tag_id 
			WHERE gct.cate_id = '.$cate_id;
		$cattag_list = M()->query($sql);
		foreach($cattag_list as $cattag)
		{
			$tag_list[$cattag['tag_name']] = array('weight'=>$cattag['weight'],'tag_id'=>$cattag['tag_id']);
		}
		
		if(!empty($category_tags))
		{
			$category_tags = explode('   ',$category_tags);
			foreach($category_tags as $category_tag)
			{
				$category_tag = explode('|',$category_tag);
				$tag_name = trim(urldecode($category_tag[0]));
				$tag_weight = intval($category_tag[1]);
				if(!empty($tag_name))
				{
					if(isset($tag_list[$tag_name]))
					{
						$tag_list[$tag_name]['weight'] = $tag_weight;
					}
					else
						$tag_list[$tag_name] = array('weight'=>$tag_weight,'tag_id'=>0);
				}
			}
		}
		
		if(!empty($custom_tags))
		{
			$custom_tags = explode(',',$custom_tags);
			foreach($custom_tags as $custom)
			{
				$custom = explode('|',$custom);
				$tag_name = trim($custom[0]);
				$tag_weight = intval($custom[1]);
				if(!empty($tag_name))
				{
					if(isset($tag_list[$tag_name]))
					{
						$tag_list[$tag_name]['weight'] = $tag_weight;
					}
					else
						$tag_list[$tag_name] = array('weight'=>$tag_weight,'tag_id'=>0);
				}
			}
		}
		
		foreach($tag_list as $tag_name => $tag_item)
		{
			if($tag_item['tag_id'] > 0)
			{
				M()->query('UPDATE '.C("DB_PREFIX").'goods_category_tags SET weight = '.$tag_item['weight'].' WHERE cate_id = '.$cate_id.' AND tag_id = '.$tag_item['tag_id']);
			}
			else
			{
				$tag_id = intval(D('GoodsTags')->where("tag_name = '$tag_name'")->getField('tag_id'));
				if($tag_id == 0)
				{
					$data = array(
						'tag_name'=>$tag_name,
						'tag_code'=>$tag_name,
						'sort'=>100,
						'is_hot'=>0,
						'count'=>0
					);
					$tag_id = D('GoodsTags')->add($data);
				}
				
				if($tag_id > 0)
				{
					$data = array(
						'cate_id'=>$cate_id,
						'tag_id'=>$tag_id,
						'weight'=>$tag_item['weight']
					);
					D("GoodsCategoryTags")->add($data);
				}
			}
		}
		
		$this->assign ( 'jumpUrl', U('GoodsCategoryTags/index'));
		$this->success (L('ADD_SUCCESS'));
	}
	
	public function setting()
	{
		$this->display();
	}
	
	public function editField()
	{
		$id = intval($_REQUEST['id']);
		if($id == 0)
			exit;
		
		$val = trim($_REQUEST['val']);
		if($val == '')
			exit;
			
		$field = trim($_REQUEST['field']);
		if(empty($field))
			exit;
		
		$cate_id = intval($_SESSION['goods_category_tags_cate_id']);
		
		$result = array('isErr'=>0,'content'=>'');
		$model = D("GoodsCategoryTags");
		$condition = array('cate_id' => $cate_id,'tag_id'=>$id);
		
		$data = array();
		$data[$field] = $val;
		
		if(false !== $model->where($condition)->save($data))
		{
			$this->saveLog(1,$id,$field);
			$result['content'] = $val;
		}
		else
		{
			$this->saveLog(0,$id,$field);
			$result['isErr'] = 1;
			$result['content'] = L('EDIT_ERROR');
		}
		
		die(json_encode($result));
	}
	
	public function remove()
	{
		//删除指定记录
		$result = array('isErr'=>0,'content'=>'');
		$id = $_REQUEST['id'];
		$cate_id = intval($_SESSION['goods_category_tags_cate_id']);
			
		if(!empty($id) && $cate_id > 0)
		{
			$model = D("GoodsCategoryTags");
			$condition = array('cate_id' => $cate_id,'tag_id'=>array('in',explode (',',$id)));
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
		else
		{
			$result['isErr'] = 1;
			$result['content'] = L('ACCESS_DENIED');
		}
		
		die(json_encode($result));
	}
}
?>