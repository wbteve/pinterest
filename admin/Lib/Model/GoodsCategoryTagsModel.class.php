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
 * 后台分类标签模型
 +------------------------------------------------------------------------------
 */
class GoodsCategoryTagsModel extends CommonModel
{
	public function getTags($cate_id)
	{
		return $this->where('cate_id = '.$cate_id)->findAll();
	}
	
	public function getTagIDs($cate_id)
	{
		$list = $this->where('cate_id = '.$cate_id)->field('tag_id')->findAll();
		$ids = array();
		foreach($list as $tag)
		{
			$ids[] = $tag['tag_id'];
		}
		
		return $ids;
	}
}
?>