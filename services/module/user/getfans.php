<?php
$count = FDB::resultFirst('SELECT COUNT(uid) FROM '.FDB::table('user_follow').' WHERE uid = '.$_FANWE['uid']);
$pager = buildPage('',array(),$count,$_FANWE['page'],30);

$user_list = FDB::fetchAll('SELECT u.uid,u.user_name,u.server_code 
	FROM '.FDB::table('user_follow').' AS uf
	INNER JOIN '.FDB::table('user').' AS u ON u.uid = uf.f_uid 
	WHERE uf.uid = '.$_FANWE['uid'].' ORDER BY uf.create_time DESC LIMIT '.$pager['limit']);

include template('services/user/fans');
display('',false);
?>