<?php
function bindCacheSetting()
{
	global $_FANWE;
	$settings = array();
	$js_settings = array();
	$res = FDB::query("SELECT name,val,is_js FROM ".FDB::table('sys_conf')." WHERE status = 1");
	while($data = FDB::fetch($res))
	{
		$name = strtolower($data['name']);
		$settings[$name] = $data['val'];
		if($data['is_js'] == 1)
			$js_settings[$name] = $data['val'];
	}

    $settings['site_title'] .= ' - '.'F'.'A'.'N'.'W'.'E';
	$settings['footer_html'] .= '<'.'p'.'>'.'<'.'a'.' '.'h'.'r'.'e'.'f'.'='.'"'.'h'.'t'.'t'.'p'.':'.'/'.'/'.'w'.'w'.'w'.'.'.'f'.'a'.'n'.'w'.'e'.'.'.'c'.'o'.'m'.'"'.' '.'t'.'a'.'r'.'g'.'e'.'t'.'='.'"'.'_'.'b'.'l'.'a'.'n'.'k'.'"'.'>'.'f'.'a'.'n'.'w'.'e'.'.'.'i'.'n'.'c'.'<'.'/'.'a'.'>'.'<'.'/'.'p'.'>';

	writeFile(PUBLIC_ROOT.'./js/setting.js','var SETTING = '.getJson($js_settings).';');

	$config_file = @file_get_contents(PUBLIC_ROOT.'config.global.php');
	$config_file = trim($config_file);
	$config_file = preg_replace("/[$]config\['time_zone'\].*?=.*?'.*?'.*?;/is", "\$config['time_zone'] = '".$settings['time_zone']."';", $config_file);
	$config_file = preg_replace("/[$]config\['default_lang'\].*?=.*?'.*?'.*?;/is", "\$config['default_lang'] = '".$settings['default_lang']."';", $config_file);
	@file_put_contents(PUBLIC_ROOT.'config.global.php', $config_file);
	unset($config_file);

	$lang_arr = array();
	$lang_files = array(
		FANWE_ROOT.'./core/language/'.$settings['default_lang'].'/template.lang.php',
		FANWE_ROOT.'./tpl/'.$settings['site_tmpl'].'/template.lang.php',
	);

	foreach($lang_files as $lang_file)
	{
		if(@include $lang_file)
		{
			foreach($lang as $lkey=>$lval)
			{
				$lang_pre = strtolower(substr($lkey,0,3));
				if($lang_pre == 'js_')
				{
					$lang_key = substr($lkey,3);
					if($lang_key != '')
						$lang_arr[$lang_key] = $lval;
				}
			}
		}
	}
	writeFile(PUBLIC_ROOT.'./js/lang.js','var LANG = '.getJson($lang_arr).';');

	clearDir(FANWE_ROOT.'./public/data/tpl/css/');
	clearDir(FANWE_ROOT.'./public/data/tpl/js/');

	$css_dir = FANWE_ROOT.'./tpl/'.$settings['site_tmpl'].'/css/';
	$css_cache_dir = FANWE_ROOT.'./public/data/tpl/css/';
	$css_site_path = $_FANWE['site_root'].'tpl/'.$settings['site_tmpl'].'/';

	$directory = dir($css_dir);
	while($entry = $directory->read())
	{
		if($entry != '.' && $entry != '..' && stripos($entry,'.css') !== false)
		{
			$css_path = $css_dir.$entry;
			$css_content = @file_get_contents($css_path);
			$css_content = preg_replace("/\.\.\//",$css_site_path,$css_content);
			$css_cache_path = $css_cache_dir.'/'.$entry;
			writeFile($css_cache_path,$css_content);
		}
	}
	$directory->close();
	FanweService::instance()->cache->saveCache('setting', $settings);
}
?>