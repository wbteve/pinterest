<?php
$cache_file = getTplCache('services/share/atme');
if(!@include($cache_file))
	include template('services/share/atme');		
display($cache_file);
?>