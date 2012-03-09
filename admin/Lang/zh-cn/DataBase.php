<?php
return array(
	'DATABASE'	=>	'数据库',
	'DATABASE_INDEX' =>	'备份列表',
	'DATABASE_DUMP' =>'数据备份',
	'DATABASE_DUMPTABLE'=>'数据备份',
	'DATABASE_RESTORE' =>'数据恢复',
	'DATABASE_RESTORETABLE'=>'数据恢复',
	'DATABASE_DELETE' =>'删除备份',
	'DATABASE_DELETETABLE'=>'删除备份',

	'DUMP'=>'备份',
	'DUMP_SUCCESS'=>'备份成功',
	'RESTORE'=>'恢复',
	'RESTORE_SUCCESS'=>'恢复成功',
	'DUMP_NAME'=>'备份名称',
	'DUMP_TIME'=>'备份时间',
	
	'DUMP_TIPS'=>'<span style="color:#f00;">备份成功后，最好将备份文件通过FTP下载到本地保存，以免发生信息泄露(备份文件保存在网站 /public/db_backup 目录下)；</span><br/><span style="color:#f00;">如果要恢复数据库可将备份文件上传到网站 /public/db_backup 目录下，然后进行恢复操作； </span>',
	'DUMP_TIPS0'=>'提示：如果备份停顿时间过长，可点击【<a href="javascript:;" onclick="location.reload(true)" style="color:#00f;">刷新</a>】，重新生成。',
	'DUMP_TIPS1'=>'共需要备份 <strong>%d</strong> 张表，当前备份的是第 <strong>%d</strong> 张表[%s]',
	'DUMP_TIPS2'=>'备份 %s 表结构失败，点击【<a href="%s" style="color:#00f;">生成</a>】，重新生成',
	'DUMP_TIPS3'=>'备份 %s 表结构成功',
	'DUMP_TIPS4'=>'开始备份 %s 表数据 %d 到 %d',
	'DUMP_TIPS5'=>'备份 %s 表数据 %d 到 %d 行失败，点击【<a href="%s" style="color:#00f;">生成</a>】，重新生成',
	
	'RESTORE_TIPS0'=>'提示：如果恢复停顿时间过长，可点击【<a href="javascript:;" onclick="showmessage(\'%s\',2)" style="color:#00f;">刷新</a>】，重新恢复。',
	'RESTORE_TIPS1'=>'共需要恢复 <strong>%d</strong> 张表，当前恢复的是第 <strong>%d</strong> 张表[%s]',
	'RESTORE_TIPS2'=>'恢复 %s 表结构失败，点击【<a href="%s" style="color:#00f;">生成</a>】，重新恢复',
	'RESTORE_TIPS3'=>'恢复 %s 表结构成功',
	'RESTORE_TIPS4'=>'开始恢复 %s 表数据 %d 到 %d',
	'RESTORE_TIPS5'=>'恢复 %s 表第 %d 行数据失败，点击【<a href="%s" style="color:#00f;">生成</a>】，重新恢复',
	
	'DELETE_TIPS0'=>'提示：如果删除停顿时间过长，可点击【<a href="javascript:;" onclick="location.reload(true)" style="color:#00f;">刷新</a>】，重新删除。',
	'DELETE_TIPS1'=>'开始删除备份 %s',
	'DELETE_TIPS2'=>'开始删除表 %s',
	'DELETE_TIPS3'=>'开始删除表 %s 文件 %s',
	'DELETE_TIPS4'=>'删除备份 %s 成功',
);
return $array;
?>