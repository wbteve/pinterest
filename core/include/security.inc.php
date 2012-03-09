<?php
if(!defined('IN_FANWE'))
	exit('Access Denied');

global $_FANWE;

if(is_string($this->config['security']['attack_evasive']))
{
	$attackevasive_tmp = explode('|', $this->config['security']['attack_evasive']);
	$attackevasive = 0;
	foreach($attackevasive_tmp AS $key => $value)
	{
		$attackevasive += intval($value);
	}
	unset($attackevasive_tmp);
}
else
{
	$attackevasive = $this->config['security']['attack_evasive'];
}

$last_request = isset($_FANWE['cookie']['last_request']) ? authcode($_FANWE['cookie']['last_request'], 'DECODE') : '';

if($attackevasive & 1 || $attackevasive & 4)
{
	fSetCookie('last_request', authcode(TIME_UTC, 'ENCODE'), TIME_UTC + 816400, 1, true);
}

if($attackevasive & 1)
{
	if(TIME_UTC - $last_request < 1)
	{
		securityMessage(lang('common','attackevasive_1_subject'),lang('common','attackevasive_1_message'));
	}
}

if(($attackevasive & 2) && ($_SERVER['HTTP_X_FORWARDED_FOR'] ||
	$_SERVER['HTTP_VIA'] || $_SERVER['HTTP_PROXY_CONNECTION'] ||
	$_SERVER['HTTP_USER_AGENT_VIA'] || $_SERVER['HTTP_CACHE_INFO'] ||
	$_SERVER['HTTP_PROXY_CONNECTION']))
{
		securityMessage(lang('common','attackevasive_2_subject'), lang('common','attackevasive_2_message'),FALSE);
}

if($attackevasive & 4)
{
	if(empty($last_request) || TIME_UTC - $last_request > 300)
	{
		securityMessage(lang('common','attackevasive_4_subject'), lang('common','attackevasive_4_message'));
	}
}

function securityMessage($subject, $message, $reload = TRUE)
{
	global $_FANWE;

	if($_FANWE['isajax'])
	{
		securityAjaxShowHeader();
		echo '<div id="attackevasive_1" class="popupmenu_option"><b style="font-size: 16px">'.$subject.'</b><br /><br />'.$message.'</div>';
		securityAjaxShowFooter();
	}
	else
	{
		echo '<html>';
		echo '<head>';
		echo '<title>'.$subject.'</title>';
		echo '</head>';
		echo '<body bgcolor="#FFFFFF">';
		if($reload) {
			echo '<script language="JavaScript">';
			echo 'function reload() {';
			echo '	document.location.reload();';
			echo '}';
			echo 'setTimeout("reload()", 2001);';
			echo '</script>';
		}
		echo '<table cellpadding="0" cellspacing="0" border="0" width="700" align="center" height="85%">';
		echo '  <tr align="center" valign="middle">';
		echo '    <td>';
		echo '    <table cellpadding="10" cellspacing="0" border="0" width="80%" align="center" style="font-family: Verdana, Tahoma; color: #666666; font-size: 11px">';
		echo '    <tr>';
		echo '      <td valign="middle" align="center" bgcolor="#EBEBEB">';
		echo '     	<br /><br /> <b style="font-size: 16px">'.$subject.'</b> <br /><br />';
		echo $message;
		echo '        <br /><br />';
		echo '      </td>';
		echo '    </tr>';
		echo '    </table>';
		echo '    </td>';
		echo '  </tr>';
		echo '</table>';
		echo '</body>';
		echo '</html>';
	}
	exit();
}


function securityAjaxShowHeader()
{
	$charset = $this->var['config']['output']['charset'];
	ob_end_clean();
	@header("Expires: -1");
	@header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
	@header("Pragma: no-cache");
	header("Content-type: application/xml");
	echo "<?xml version=\"1.0\" encoding=\"".$charset."\"?>\n<root><![CDATA[";
}

function securityAjaxShowFooter()
{
	echo ']]></root>';
	exit();
}
?>