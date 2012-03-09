<?php
$array = array(
	'WORD'=>'敏感词管理',
	'WORD_INDEX'=>'敏感词列表',
	'WORD_ADD'=>'添加敏感词',
	'WORD_EDIT'=>'编辑敏感词',
	'WORD_IMPORT'=>'导入词库',
	'WORD_EXPORT'=>'导出词库',
	
	'WORD'=>'敏感词',
	'WORD_TIPS'=>'可批量添加，一行一个',
	'CID'=>'分类',
	'CID_DEFAULT'=>'选择分类',
	'CID_ALL'=>'所有分类',
	'TYPE'=>'类型',
	'TYPE_ALL'=>'所有类型',
	'TYPE_TIPS'=>'禁用：无法提交含禁用词汇的分享，提示（内容中含有非法词语，返回编辑） <br/>替换：发布时，分享里含有替换词的话，会使用后台设置好的词汇替换',
	'TYPE_1'=>'禁用',
	'TYPE_2'=>'替换',
	'REPLACEMENT'=>'替换为',
	'IMPORT'=>'导入词库',
	'IMPORT_SUCCESS'=>'导入词库成功',
	'EXPORT'=>'导出词库',
	'WORD_FILE'=>'词库文件',
	'WORD_FILE_EMPTY'=>'请选择要导入的词库文件',
	'WORD_FILE_ERROR'=>'上传词库文件失败',
	'IMPORT_TIPS'=>'词库文件请保存为UTF-8编码格式<br/>词库文件每行表示一个敏感词，各类型格式如下：<br/>禁用：敏感词|1 ，例 abc|1<br/>替换：敏感词|2|替换词 ，例 abc|2|efg',
);
return $array;
?>