<?php
$array = array(
	'ROLENODE'=>'权限节点管理',
	'ROLENODE_INDEX'=>'权限节点列表',
	'ROLENODE_ADD'=>'添加权限节点',
	'ROLENODE_EDIT'=>'编辑权限节点',
	'ACTION'=>'操作',
	'ACTION_NAME'=>'操作名称',
	'MODULE'=>'模块',
	'MODULE_NAME'=>'模块名称',
	'NAV_ID'=>'所属导航',
	'SELECT_NAV_ID'=>'不设置',
	
	'AUTH_TYPE'=>'授权类型',
	'AUTH_TYPE_1'=>'模块授权',
	'AUTH_TYPE_2'=>'操作授权',
	'AUTH_TYPE_0'=>'节点授权',
	
	'IS_SHOW'=>'菜单显示',
	'SORT'=>'节点排序',
	
	'ROLENODE_TIPS' => '1. 只填写模块，为模块的公共授权<br/>2. 填写操作与模块，为指定模块中节点的授权',
	'ACTION_TIPS' => '(请填写控制器的相应Action，如index,insert... 非系统管理员请勿随意修改)',
	'MODULE_TIPS' => '(请填写控制器的相应名称，如RoleNode,Article... 非系统管理员请勿随意修改)',
	
	'ROLENODE_UNIQUE' => '已存在该节点',
	
	'ROLE_NODE_MODULE_REQUIRE'=>'模块不能为空',
);
return $array;
?>