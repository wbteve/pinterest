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
 * 管理员模型
 +------------------------------------------------------------------------------
 */
class AdminModel extends CommonModel
{
	public $_validate = array(
		array('admin_name','require','{%ADMIN_NAME_REQUIRE}'),
		array('admin_name','','{%ADMIN_NAME_UNIQUE}',0,'unique',2),
		array('admin_pwd','require','{%ADMIN_PWD_REQUIRE}',0,'',1),
	);

	protected $_auto = array( 
		array('status','1'),  // 新增的时候把status字段设置为1
		array('admin_pwd','md5',3,'function'),
		array('create_time','gmtTime',1,'function'),
		array('update_time','gmtTime',2,'function'),
	);
}
?>