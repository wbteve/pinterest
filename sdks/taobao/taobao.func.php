<?php
//淘宝
function GetTaoBaoLoginUrl($top_appkey)
{
	return "http://container.api.taobao.com/container?appkey=".$top_appkey."&encode=utf-8";
}

function CheckTaoBaoSign($top_appkey,$top_secret,$top_parameters,$top_session,$top_sign)
{
	$sign = base64_encode(md5($top_appkey.$top_parameters.$top_session.$top_secret,true));
	return $sign == $top_sign;
}

function GetTaoBaoParameters($top_parameters)
{
	$parameters = array();
	parse_str(base64_decode(urldecode($top_parameters)),$parameters);
	return $parameters;
}

function GetTaoBaoRefreshTokenUrl($appkey,$secret,$sessionkey,$refreshToken)
{
	$signs = array();
	$signs['appkey'] = $appkey;
	$signs['refresh_token'] = $refreshToken;
	$signs['sessionkey'] = $sessionkey;
	$sign = '';
	foreach($signs as $key=>$val)
	{
		$sign .= $key.$val;
	}
	$sign .= $secret;
	$signs['sign'] = strtoupper(md5($sign));
	return "http://container.open.taobao.com/container/refresh?".http_build_query($signs);
}
?>