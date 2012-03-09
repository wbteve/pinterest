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
 * 达人模型
 +------------------------------------------------------------------------------
 */
class UserDarenModel extends CommonModel
{
	public $_validate = array(
		array('uid','require','{%USER_REQUIRE}',1),
		array('uid','','{%USER_UNIQUE}',0,'unique',3),
	);
	
	protected $_auto = array(
		array('day_time','strZTime',3,'function'),
		array('create_time','gmtTime',1,'function'),
	);
}
?>