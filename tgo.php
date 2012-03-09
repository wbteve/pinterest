<?php
$url = base64_decode($_REQUEST['url']);
if(empty($url))
	exit;
header("Location: $url");
?>