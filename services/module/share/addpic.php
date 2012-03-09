<?php
$cache_file = getTplCache('services/share/addpic');
if(!@include($cache_file))
	include template('services/share/addpic');		
display($cache_file);
?>