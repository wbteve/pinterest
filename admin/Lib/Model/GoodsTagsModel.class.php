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
 * 后台商品分类标签模型
 +------------------------------------------------------------------------------
 */
class GoodsTagsModel extends CommonModel
{
	protected $_validate = array(
		array('tag_name','require','{%TAG_NAME_REQUIRE}'),
		array('tag_name','','{%TAG_NAME_UNIQUE}',0,'unique',2),
		array('tag_code','require','{%TAG_CODE_REQUIRE}'),
		array('tag_code','','{%TAG_CODE_UNIQUE}',0,'unique',2),
	);

	protected $_auto = array( 
		array('status','1'),  // 新增的时候把status字段设置为1	
	);
}
?>