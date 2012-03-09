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
 * 商品标签管理
 +------------------------------------------------------------------------------
 */
class GoodsTagsAction extends CommonAction
{
    public function index()
	{
		$where = '';
		$parameter = array();
		$tag_name = trim($_REQUEST['tag_name']);

		if(!empty($tag_name))
		{
			$this->assign("tag_name",$tag_name);
			$parameter['tag_name'] = $tag_name;
			$where .= " AND tag_name LIKE '%".mysqlLikeQuote($tag_name)."%'";
		}

		$model = M();

		if(!empty($where))
			$where = 'WHERE 1' . $where;

		$sql = 'SELECT COUNT(tag_id) AS tcount
			FROM '.C("DB_PREFIX").'goods_tags '.$where;

		$count = $model->query($sql);
		$count = $count[0]['tcount'];

		$sql = 'SELECT * FROM '.C("DB_PREFIX").'goods_tags '.$where;
		$this->_sqlList($model,$sql,$count,$parameter,'tag_id');
		$this->display();
	}

    public function insert()
	{
		$_POST['tag_code'] = $_POST['tag_name'];
		parent::insert();
	}

	public function update()
	{
		$_POST['tag_code'] = $_POST['tag_name'];
		parent::update();
	}

	public function remove()
	{
		//删除指定记录
		$result = array('isErr'=>0,'content'=>'');
		$id = $_REQUEST['id'];
		if(!empty($id))
		{
			$model = D("GoodsTags");
			$condition = array('tag_id' => array('in',explode (',',$id)));
			if(false !== $model->where ( $condition )->delete())
			{
				D("GoodsCategoryTags")->where($condition)->delete();
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

	public function search()
	{
		$cid = intval($_REQUEST['cid']);
		$sid = intval($_REQUEST['sid']);
		$key = trim($_REQUEST['key']);
		$type = intval($_REQUEST['type']);
		$tag_name = trim($_REQUEST['tag_name']);
		$custom_tags = trim($_REQUEST['custom_tags']);

		$where = '';

		if(!empty($key))
			$where .= ' AND tag_name LIKE \'%'.mysqlLikeQuote($key).'%\'';

		if($cid > 0)
		{
			$cids = D('GoodsCategoryTags')->getTagIDs($cid);
			if(count($cids) > 0)
				$where .= ' AND tag_id NOT IN ('.implode(',',$cids).')';
		}

		if($sid > 0)
		{
			$sids = D('StyleCategoryTags')->getTagIDs($sid);
			if(count($cids) > 0)
				$where .= ' AND tag_id NOT IN ('.implode(',',$sids).')';
		}

		$tag_names = array();
		if(!empty($tag_name))
		{
			$tag_name = explode('   ',$tag_name);
			foreach($tag_name as $name)
			{
				$tag_names[] = addslashes($name);
			}
		}

		if(!empty($custom_tags))
		{
			$custom_tags = explode(',',$custom_tags);
			foreach($custom_tags as $custom)
			{
				$custom = explode('|',$custom);
				$tag_names[] = addslashes($custom[0]);
			}
		}

		if(count($tag_names) > 0)
			$where .= ' AND tag_name NOT '.createIN($tag_names);

		$list = array();
		if(empty($where))
			$list = D('GoodsTags')->limit('0,60')->order('sort ASC,tag_id ASC')->findAll();
		else
			$list = D('GoodsTags')->where('1'.$where)->limit('0,60')->order('sort ASC,tag_id ASC')->findAll();

		if($type == 1)
			echo json_encode($list);
		else
		{
			$this->assign("tag_list",$list);
			echo $this->fetch('GoodsTags:tags');
		}
	}
}
?>