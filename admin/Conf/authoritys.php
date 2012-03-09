<?php
$authoritys = array();
//不需要认证的模板中的操作
$authoritys['no']['region']['getcitys'] = 1;
$authoritys['no']['goodstags']['search'] = 1;
$authoritys['no']['user']['getuserlist'] = 1;
$authoritys['no']['usergroup']['authoritys'] = 1;


//全局权限
$authoritys['all']['add'] = array('insert');
$authoritys['all']['edit'] = array('update');
$authoritys['all']['index'] = array('search');

//缓存管理权限
$authoritys['actions']['cache']['system'] = array('clear','systemclear');
$authoritys['actions']['cache']['custom'] = array('clear','customclear');

//数据库管理权限
$authoritys['actions']['database']['dump'] = array('dumptable');
$authoritys['actions']['database']['delete'] = array('deletetable');
$authoritys['actions']['database']['restore'] = array('restoretable');

//商品分类标签管理权限
$authoritys['actions']['database']['setting'] = array('insert');

//分享管理权限
$authoritys['actions']['share']['edit'] = array('editcomment','updatecomment');
$authoritys['actions']['share']['index'] = array('comments');
$authoritys['actions']['share']['remove'] = array('removephoto','removegoods','removecomment');

//sql管理权限
$authoritys['actions']['sysconf']['index'] = array('update');

//sql管理权限
$authoritys['actions']['tempfile']['index'] = array('clear','fileclear');

//信件管理
$authoritys['actions']['usermsg']['index'] = array('show','delbymlid','delbymiid');
$authoritys['actions']['usermsg']['groupsend'] = array('savesend','updatesend','groupedit');
$authoritys['actions']['usermsg']['grouplist'] = array('togglestatus','remove');
?>