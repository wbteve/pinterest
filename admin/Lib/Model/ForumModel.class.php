<?php
class ForumModel extends TreeModel
{
	protected $_validate = array(
		array('name','require','{%NAME_EMPTY_TIP}'),
	);

	protected $_auto = array( 
		array('create_time','gmtTime',1,'function'),
		array('status','1'),  // 新增的时候把status字段设置为1	
	);
}
?>