<?php
/**
 * UCenter API
 */

define('UC_CLIENT_VERSION', '1.5.1');
define('UC_CLIENT_RELEASE', '20091001');

define('API_DELETEUSER', 1);
define('API_RENAMEUSER', 1);
define('API_GETTAG', 1);
define('API_SYNLOGIN', 1);
define('API_SYNLOGOUT', 1);
define('API_UPDATEPW', 1);
define('API_UPDATEBADWORDS', 1);
define('API_UPDATEHOSTS', 1);
define('API_UPDATEAPPS', 1);
define('API_UPDATECLIENT', 1);
define('API_UPDATECREDIT', 1);
define('API_GETCREDITSETTINGS', 1);
define('API_GETCREDIT', 1);
define('API_UPDATECREDITSETTINGS', 1);

define('API_RETURN_SUCCEED', '1');
define('API_RETURN_FAILED', '-1');
define('API_RETURN_FORBIDDEN', '-2');

include './init.php';


if(!defined('IN_UC')) {

	error_reporting(0);
	set_magic_quotes_runtime(0);

	defined('MAGIC_QUOTES_GPC') || define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());

	$_DCACHE = $get = $post = array();

	$code = @$_GET['code'];
	parse_str(_authcode($code, 'DECODE', UC_KEY), $get);
	if(MAGIC_QUOTES_GPC) {
		$get = _stripslashes($get);
	}

	$timestamp = time();
	if(empty($get)) {
		exit('Invalid Request');
	} elseif($timestamp - $get['time'] > 3600) {
		exit('Authracation has expiried');
	}
}

$action = $get['action'];
include(FANWE_ROOT . 'uc_client/lib/xml.class.php');
$post = xml_unserialize(file_get_contents('php://input'));

if(in_array($get['action'], array('test', 'deleteuser', 'renameuser', 'gettag', 'synlogin', 'synlogout', 'updatepw', 'updatebadwords', 'updatehosts', 'updateapps', 'updateclient', 'updatecredit', 'getcreditsettings', 'updatecreditsettings')))
{
    $uc_note = new uc_note();
    exit($uc_note->$get['action']($get, $post));
}
else
{
    exit(API_RETURN_FAILED);
}

class uc_note
{
	var $appdir = '';
    /* 数据库所使用编码 */
    var $charset        = '';
    		
    function _serialize($arr, $htmlon = 0)
    {
        if(!function_exists('xml_serialize'))
        {
            include($this->appdir . 'uc_client/lib/xml.class.php');
        }
        return xml_serialize($arr, $htmlon);
    }

    function uc_note()
    {
		$this->appdir = FANWE_ROOT;
		$this->charset = UC_DBCHARSET;   	
    }

    function test($get, $post)
    {
        return API_RETURN_SUCCEED;
    }

    function deleteuser($get, $post)
    {
        $uids = $get['ids'];
        if(!API_DELETEUSER)
        {
            return API_RETURN_FORBIDDEN;
        }

        return API_RETURN_SUCCEED;
        /*
        if (FS('User')->setSession($user,0))
        {
            return API_RETURN_SUCCEED;
        }
        */
    }

    function renameuser($get, $post)
    {
        $uid = $get['uid'];
        $usernamenew = $get['newusername'];
    	if ($this->charset == 'gbk'){
          $usernamenew = addslashes(gbToUTF8($usernamenew));
	    };         
        if(!API_RENAMEUSER)
        {
            return API_RETURN_FORBIDDEN;
        }
       FDB::query("UPDATE " . FDB::table('user') . " SET user_name='$usernamenew' WHERE ucenter_id='$uid' limit 1");
        return API_RETURN_SUCCEED; 
    }

    function gettag($get, $post)
    {
        if(!API_GETTAG)
        {
            return API_RETURN_FORBIDDEN;
        }
    }

    function synlogin($get, $post)
    {
    	
    	global $_FANWE;
    	
    	//uc_get_user()
        $integrate_id = intval($get['uid']);
        
        $username = $get['username'];
    	if ($this->charset == 'gbk'){
          $username = addslashes(gbToUTF8($username));
	    };        
        if(!API_SYNLOGIN)
        {
            return API_RETURN_FORBIDDEN;
        }
        //$sql = "update fanwe_user set last_ip = 'aaaa'";
        //$GLOBALS['db']->query($sql);        
        header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
        //set_login($uid, $username, $this->charset);
        
        $user_field = $_FANWE['setting']['integrate_field_id'];
        
        $sql = "SELECT uid,password FROM ".FDB::table('user')." WHERE {$user_field} = '$integrate_id'";
        $user_info = FDB::fetchFirst($sql);
        
        
        $password = $user_info['password'];
        $uid = intval($user_info['uid']);
        //echo 'ddd2<br>'.$uid; exit;
        if ($uid <= 0){        	
        	include_once(FANWE_ROOT . 'uc_client/client.php');
        	//echo 'aa';
        	list($uid, $uname, $email) = uc_get_user($integrate_id,1);
        	//echo 'aa';
        	$user = array(
  	    				'integrate_id' => $uid,
   	    				'email' => $email,
   	    				'user_name' => $uname,    				
   	    				'password'  => md5(time().rand(100000, 999999)),
        	);        	
        	//print_r($user);
        	        	
        	$uid = FS("Integrate")->addUserToLoacl($username,$password, 1, $user);
        }
        
        if ($uid > 0){
        	$sql = "SELECT password FROM ".FDB::table('user')." WHERE uid = '$uid'";
        	$password = FDB::resultFirst($sql);
        	
        	$user = array(
        	        	'uid'=>$uid,
        	        	'password'=>$password,
        	);
        	FS('User')->setSession($user,0);        	
        }
    }

    function synlogout($get, $post)
    {
        if(!API_SYNLOGOUT)
        {
            return API_RETURN_FORBIDDEN;
        }
        
        header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
        //set_cookie();
        FS('User')->clearSession();
    }

    function updatepw($get, $post)
    {
        if(!API_UPDATEPW)
        {
            return API_RETURN_FORBIDDEN;
        }
        $username = $get['username'];  
        $uid = intval($get['uid']);
    	if ($this->charset == 'gbk'){
          $username = addslashes(gbToUTF8($username));
	    };  	                  	
        $newpw = md5(time().rand(100000, 999999));	
        FDB::query("UPDATE " . FDB::table('user') . " SET password='$newpw' WHERE user_name='$username'  limit 1");
        return API_RETURN_SUCCEED;
    }

    function updatebadwords($get, $post)
    {
        if(!API_UPDATEBADWORDS)
        {
            return API_RETURN_FORBIDDEN;
        }
        $cachefile = $this->appdir.'uc_client/data/cache/badwords.php';
        $fp = fopen($cachefile, 'w');
        $data = array();
        if(is_array($post)) {
            foreach($post as $k => $v) {
                $data['findpattern'][$k] = $v['findpattern'];
                $data['replace'][$k] = $v['replacement'];
            }
        }
        $s = "<?php\r\n";
        $s .= '$_CACHE[\'badwords\'] = '.var_export($data, TRUE).";\r\n";
        fwrite($fp, $s);
        fclose($fp);
        return API_RETURN_SUCCEED;
    }

    function updatehosts($get, $post)
    {
        if(!API_UPDATEHOSTS)
        {
            return API_RETURN_FORBIDDEN;
        }
        $cachefile = $this->appdir. 'uc_client/data/cache/hosts.php';
        $fp = fopen($cachefile, 'w');
        $s = "<?php\r\n";
        $s .= '$_CACHE[\'hosts\'] = '.var_export($post, TRUE).";\r\n";
        fwrite($fp, $s);
        fclose($fp);
        return API_RETURN_SUCCEED;
    }

    function updateapps($get, $post)
    {
        if(!API_UPDATEAPPS)
        {
            return API_RETURN_FORBIDDEN;
        }
        $UC_API = $post['UC_API'];

        $cachefile = $this->appdir . 'uc_client/data/cache/apps.php';
        $fp = fopen($cachefile, 'w');
        $s = "<?php\r\n";
        $s .= '$_CACHE[\'apps\'] = '.var_export($post, TRUE).";\r\n";
        fwrite($fp, $s);
        fclose($fp);
        #clear_cache_files();
        return API_RETURN_SUCCEED;
    }

    function updateclient($get, $post)
    {
        if(!API_UPDATECLIENT)
        {
            return API_RETURN_FORBIDDEN;
        }
        $cachefile = $this->appdir. 'uc_client/data/cache/settings.php';
        $fp = fopen($cachefile, 'w');
        $s = "<?php\r\n";
        $s .= '$_CACHE[\'settings\'] = '.var_export($post, TRUE).";\r\n";
        fwrite($fp, $s);
        fclose($fp);
        return API_RETURN_SUCCEED;
    }

    function updatecredit($get, $post)
    {
        if(!API_UPDATECREDIT)
        {
            return API_RETURN_FORBIDDEN;
        }
    }

    function getcredit($get, $post)
    {
        if(!API_GETCREDIT)
        {
            return API_RETURN_FORBIDDEN;
        }
    }

    function getcreditsettings($get, $post)
    {
        if(!API_GETCREDITSETTINGS)
        {
            return API_RETURN_FORBIDDEN;
        }
    }

    function updatecreditsettings($get, $post)
    {
        if(!API_UPDATECREDITSETTINGS)
        {
            return API_RETURN_FORBIDDEN;
        }
    }
}


function _setcookie($var, $value, $life = 0, $prefix = 1) {
	global $cookiepre, $cookiedomain, $cookiepath, $timestamp, $_SERVER;
	setcookie(($prefix ? $cookiepre : '').$var, $value,
		$life ? $timestamp + $life : 0, $cookiepath,
		$cookiedomain, $_SERVER['SERVER_PORT'] == 443 ? 1 : 0);
}

function _authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
	$ckey_length = 4;

	$key = md5($key ? $key : UC_KEY);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + gmtTime() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
				return '';
			}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}

}

function _stripslashes($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = _stripslashes($val);
		}
	} else {
		$string = stripslashes($string);
	}
	return $string;
}
?>