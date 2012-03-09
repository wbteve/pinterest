<?php
define('QQ_SCOPE',"get_user_info,add_share,list_album,add_album,upload_pic,add_topic,add_one_blog,add_weibo");

function getQqLoginUrl($appid)
{
	global $_FANWE;
	$state = md5(uniqid(rand(),TRUE));
    $url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=" 
        . $appid . "&redirect_uri=".urlencode($_FANWE['site_url']."callback/qq.php")
        . "&state=" .$state
        . "&scope=".QQ_SCOPE;
	
	fSetCookie('qq_state',$state);
    return $url;
}

function getQqAccessToken($appid,$appkey)
{
	global $_FANWE;
	$qq_state = $_FANWE['cookie']['qq_state'];
	if($_REQUEST['state'] == $qq_state)
    {
        $token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&"
            . "client_id=" . $appid. "&redirect_uri=" . urlencode($_FANWE['site_url']."callback/qq.php")
            . "&client_secret=" . $appkey. "&code=" . $_REQUEST["code"];

        $response = file_get_contents($token_url);
        if (strpos($response, "callback") !== false)
        {
            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
            $msg = json_decode($response);
            if (isset($msg->error))
            {
                echo "<h3>error:</h3>" . $msg->error;
                echo "<h3>msg  :</h3>" . $msg->error_description;
                exit;
            }
        }

        $params = array();
        parse_str($response, $params);
        return $params["access_token"];

    }
    else 
    {
        echo("The state does not match. You may be a victim of CSRF.");
    }
}

function getQqOpenid($access_token)
{
    $graph_url = "https://graph.qq.com/oauth2.0/me?access_token=".$access_token;
    $str  = file_get_contents($graph_url);
    if (strpos($str, "callback") !== false)
    {
        $lpos = strpos($str, "(");
        $rpos = strrpos($str, ")");
        $str  = substr($str, $lpos + 1, $rpos - $lpos -1);
    }

    $user = json_decode($str);
    if (isset($user->error))
    {
        echo "<h3>error:</h3>" . $user->error;
        echo "<h3>msg  :</h3>" . $user->error_description;
        exit;
    }
    return $user->openid;
}

function getQqUserInfo($appid,$access_token,$openid)
{
    $get_user_info = "https://graph.qq.com/user/get_user_info?"
        . "access_token=" . $access_token
        . "&oauth_consumer_key=" . $appid
        . "&openid=" . $openid
        . "&format=json";

    $info = file_get_contents($get_user_info);
    $arr = json_decode($info, true);
    return $arr;
}
?>