<?php
function getUserName($uid)
{
	return FDB::resultFirst("select user_name from ".FDB::table("user")." where uid = ".$uid);
}
?>