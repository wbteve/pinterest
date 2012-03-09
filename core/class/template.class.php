<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/**  
 * template.class.php
 *
 * 模板处理类
 *
 * @package class
 * @author awfigq <awfigq@qq.com>
 */
class Template {

	var $sub_templates = array();
	var $replace_code = array('search' => array(), 'replace' => array());
	var $blocks = array();
	var $language = array();
	var $file = '';
	
	/**  
	 * 解析模板并生成缓存文件
	 * @param string $tpl_file 模板名称
	 * @param string $tpl_dir 模板目录
	 * @param string $file 模板路径
	 * @param string $cache_file 模板缓存地址
	 * @return void
	 */ 
	function parseTemplate($tpl_file, $tpl_dir, $file, $cache_file)
	{
		$base_file = basename(FANWE_ROOT.$tpl_file, '.htm');
		$this->file = $file;
		
		if(!@$fp = fopen(FANWE_ROOT.$tpl_file, 'r'))
		{
			$tpl = $tpl_dir.'/'.$file.'.htm';
			$tpl_file = $tpl_file != $tpl ? $tpl.'", "'.$tpl_file : $tpl_file;
			$this->error('template_not_found', $tpl_file);
		}
		
		$template = @fread($fp, filesize(FANWE_ROOT.$tpl_file));
		fclose($fp);
		$var_regexp = "((\\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*(\-\>)?[a-zA-Z0-9_\x7f-\xff]*)(\[[a-zA-Z0-9_\-\.\"\'\[\]\$\x7f-\xff]+\])*)";
		$const_regexp = "([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)";

		$this->sub_templates = array();
		for($i = 1; $i <= 3; $i++)
		{
			if(strExists($template, '{subtemplate'))
			{
				$template = preg_replace("/[\n\r\t]*(\<\!\-\-)?\{subtemplate\s+([a-z0-9_:\/]+)\}(\-\-\>)?[\n\r\t]*/ies", "\$this->loadSubTemplate('\\2','$tpl_dir')", $template);
			}
		}
		
		$template = preg_replace("/([\n\r]+)\t+/s", "\\1", $template);
		$template = preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}", $template);
		$template = preg_replace("/\<\?php(.*?)\?\>/ies", "\$this->phpTags('\\1')", $template);
		$template = preg_replace("/[\n\r\t]*\{css\s+(.+?)\}[\n\r\t]*/ies", "\$this->cssTags('\\1')", $template);
		$template = preg_replace("/[\n\r\t]*\{script\s+(.+?)\}[\n\r\t]*/ies", "\$this->scriptTags('\\1')", $template);
		$template = preg_replace("/\{lang\s+(.+?)\}/ies", "\$this->languageVar('\\1')", $template);
		
		$template = preg_replace("/\{sprintf\s+(.+?)(?:|\s+(.+?))\}/ies", "\$this->sprintfTags('\\1','\\2')", $template);
		$template = preg_replace("/\{nl2br\s+(.+?)\}/ies", "\$this->nl2brTags('\\1')", $template);
		$template = preg_replace("/[\n\r\t]*\{advlayout\s+id=(\d+)(?:\scount=(\d+))*(?:\starget=(.+?))*\}[\n\r\t]*/is", "<!--dynamic advLayout args=\\1,\\2,\\3-->", $template);
		$template = preg_replace("/[\n\r\t]*\{advlayout\s+name='(.+?)'(?:\stmpl='(.+?)')*(?:\starget='(.+?)')*\}[\n\r\t]*/is", "<!--dynamic advLayoutName args=\\1,\\2,\\3-->", $template);
		$template = preg_replace("/[\n\r\t]*\{getimg\s+(.+?)\s+(.+?)\s+(.+?)(?:\s+(.+?))?\}[\n\r\t]*/ies", "\$this->getImgTags('\\1','\\2','\\3','\\4')", $template);
		$template = preg_replace("/[\n\r\t]*\{getuser\s+(.+?)\s+(\d+?)(?:\s+'(.*?)')?(?:\s+'(.*?)')?(?:\s+'(.*?)')?\}[\n\r\t]*/ies", "\$this->getUserTags('\\1','\\2','\\3','\\4','\\5')", $template);
		$template = preg_replace("/[\n\r\t]*\{getfollow\s+(.+?)\s+(.+?)\}[\n\r\t]*/is", "<!--getfollow \\1 \\2-->", $template);
		$template = preg_replace("/[\n\r\t]*\{date\s+(.+?)(?:|\s+'(.+?)')\}[\n\r\t]*/ie", "\$this->dateTags('\\1','\\2')", $template);
		$template = preg_replace("/[\n\r\t]*\{timelag\s+(.+?)\}[\n\r\t]*/ie", "\$this->timeLagTags('\\1')", $template);
		$template = preg_replace("/[\n\r\t]*\{avatar\s+(.+?)(?:\s+(.+?))?(?:\s+(.+?))?(?:\s+(.+?))?\}[\n\r\t]*/ies", "\$this->avatarTags('\\1','\\2','\\3','\\4')", $template);
		$template = preg_replace("/[\n\r\t]*\{cutstr\s+(.+?)\s+(.+?)(?:|\s(.+?))\}[\n\r\t]*/ies", "\$this->cutstrTags('\\1','\\2','\\3')", $template);
		$template = preg_replace("/[\n\r\t]*\{u\s+(.+?)(?:|\s+(.+?))\}[\n\r\t]*/ies", "\$this->uTags('\\1','\\2')", $template);
		$template = preg_replace("/[\n\r\t]*\{eval\s+(.+?)\s*\}[\n\r\t]*/ies", "\$this->evalTags('\\1')", $template);
		$template = str_replace("{LF}", "<?=\"\\n\"?>", $template);
		$template = preg_replace("/\{(\\\$[a-zA-Z0-9_\-\>\[\]\'\"\$\.\x7f-\xff]+)\}/s", "<?=\\1?>", $template);
		$template = preg_replace("/[\n\r\t]*\{dynamic\s+(.+?)\}[\n\r\t]*/is", "<!--dynamic \\1-->", $template);
		$template = preg_replace("/$var_regexp/es", "template::addQuote('<?=\\1?>')", $template);
		$template = preg_replace("/\<\?\=\<\?\=$var_regexp\?\>\?\>/es", "\$this->addQuote('<?=\\1?>')", $template);
		
		$header_add = '';
		if(!empty($this->sub_templates))
		{
			$header_add .= "\n0\n";
			foreach($this->sub_templates as $fname)
			{
				$header_add .= "|| checkTplRefresh('$tpl_file', '$fname', ".TIMESTAMP.", '$cache_file', '$tpl_dir', '$file')\n";
			}
			$header_add .= ';';
		}

		if(!empty($this->blocks))
		{
			//$header_add .= "\n";
			//$header_add .= "block_get('".implode(',', $this->blocks)."');";
		}

		$template = "<? if(!defined('IN_FANWE')) exit('Access Denied'); {$header_add}?>\n$template";

		$template = preg_replace("/[\n\r\t]*\{template\s+([a-z0-9_:\/]+)\}[\n\r\t]*/ies", "\$this->stripvTags('<? include template(\'\\1\'); ?>')", $template);
		$template = preg_replace("/[\n\r\t]*\{template\s+(.+?)\}[\n\r\t]*/ies", "\$this->stripvTags('<? include template(\'\\1\'); ?>')", $template);
		$template = preg_replace("/[\n\r\t]*\{echo\s+(.+?)\}[\n\r\t]*/ies", "\$this->stripvTags('<? echo \\1; ?>')", $template);
		$template = preg_replace("/([\n\r\t]*)\{if\s+(.+?)\}([\n\r\t]*)/ies", "\$this->stripvTags('\\1<? if(\\2) { ?>\\3')", $template);
		$template = preg_replace("/([\n\r\t]*)\{elseif\s+(.+?)\}([\n\r\t]*)/ies", "\$this->stripvTags('\\1<? } elseif(\\2) { ?>\\3')", $template);
		$template = preg_replace("/\{else\}/i", "<? } else { ?>", $template);
		$template = preg_replace("/\{\/if\}/i", "<? } ?>", $template);
		$template = preg_replace("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\}[\n\r\t]*/ies", "\$this->stripvTags('<? if(is_array(\\1)) { foreach(\\1 as \\2) { ?>')", $template);
		
		$template = preg_replace("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}[\n\r\t]*/ies", "\$this->stripvTags('<? if(is_array(\\1)) { foreach(\\1 as \\2 => \\3) { ?>')", $template);
		$template = preg_replace("/\{\/loop\}/i", "<? } } ?>", $template);

		$template = preg_replace("/\{$const_regexp\}/s", "<?=\\1?>", $template);
		if(!empty($this->replace_code))
		{
			$template = str_replace($this->replace_code['search'], $this->replace_code['replace'], $template);
		}
		$template = preg_replace("/ \?\>[\n\r]*\<\? /s", " ", $template);
		
		if(!@$fp = fopen(PUBLIC_ROOT.$cache_file, 'w'))
		{
			$this->error('directory_not_found', dirname(PUBLIC_ROOT.$cache_file));
		}
		
		//$template = preg_replace("/\"(http)?[\w\.\/:]+\?[^\"]+?&[^\"]+?\"/e", "\$this->transAmp('\\0')", $template);
		$template = preg_replace("/\<script[^\>]*?src=\"(.+?)\"(.*?)\>\s*\<\/script\>/ies", "\$this->stripScriptAmp('\\1', '\\2')", $template);
		//$template = preg_replace("/[\n\r\t]*\{block\s+([a-zA-Z0-9_\[\]]+)\}(.+?)\{\/block\}/ies", "\$this->stripBlock('\\1', '\\2')", $template);
		flock($fp, 2);
		fwrite($fp, $template);
		fclose($fp);
	}
	
	/**  
	 * 解析模板字符串
	 * @param string $template 模板字符串
	 * @return string
	 */ 
	function parseString($template)
	{
		$var_regexp = "((\\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*(\-\>)?[a-zA-Z0-9_\x7f-\xff]*)(\[[a-zA-Z0-9_\-\.\"\'\[\]\$\x7f-\xff]+\])*)";
		$const_regexp = "([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)";
		
		$template = preg_replace("/([\n\r]+)\t+/s", "\\1", $template);
		$template = preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}", $template);
		$template = preg_replace("/\<\?php(.*?)\?\>/ies", "\$this->phpTags('\\1')", $template);
		$template = preg_replace("/[\n\r\t]*\{css\s+(.+?)\}[\n\r\t]*/ies", "\$this->cssTags('\\1')", $template);
		$template = preg_replace("/[\n\r\t]*\{script\s+(.+?)\}[\n\r\t]*/ies", "\$this->scriptTags('\\1')", $template);
		$template = preg_replace("/\{lang\s+(.+?)\}/ies", "\$this->languageVar('\\1')", $template);
		$template = preg_replace("/\{sprintf\s+(.+?)(?:|\s+(.+?))\}/ies", "\$this->sprintfTags('\\1','\\2')", $template);
		$template = preg_replace("/\{nl2br\s+(.+?)\}/ies", "\$this->nl2brTags('\\1')", $template);
		$template = preg_replace("/[\n\r\t]*\{getimg\s+(.+?)\s+(.+?)\s+(.+?)(?:\s+(.+?))?\}[\n\r\t]*/ies", "\$this->getImgTags('\\1','\\2','\\3','\\4')", $template);
		$template = preg_replace("/[\n\r\t]*\{getuser\s+(.+?)\s+(\d+?)(?:\s+'(.*?)')?(?:\s+'(.*?)')?(?:\s+'(.*?)')?\}[\n\r\t]*/ies", "\$this->getUserTags('\\1','\\2','\\3','\\4','\\5')", $template);
		$template = preg_replace("/[\n\r\t]*\{getfollow\s+(.+?)\s+(.+?)\}[\n\r\t]*/is", "<!--getfollow \\1 \\2-->", $template);
		$template = preg_replace("/[\n\r\t]*\{date\s+(.+?)(?:|\s+'(.+?)')\}[\n\r\t]*/ie", "\$this->dateTags('\\1','\\2')", $template);
		$template = preg_replace("/[\n\r\t]*\{timelag\s+(.+?)\}[\n\r\t]*/ie", "\$this->timeLagTags('\\1')", $template);
		$template = preg_replace("/[\n\r\t]*\{avatar\s+(.+?)(?:\s+(.+?))?(?:\s+(.+?))?(?:\s+(.+?))?\}[\n\r\t]*/ies", "\$this->avatarTags('\\1','\\2','\\3','\\4')", $template);
		$template = preg_replace("/[\n\r\t]*\{cutstr\s+(.+?)\s+(.+?)(?:|\s(.+?))\}[\n\r\t]*/ies", "\$this->cutstrTags('\\1','\\2','\\3')", $template);
		$template = preg_replace("/[\n\r\t]*\{u\s+(.+?)(?:|\s+(.+?))\}[\n\r\t]*/ies", "\$this->uTags('\\1','\\2')", $template);
		$template = preg_replace("/[\n\r\t]*\{echo\s+(.+?)\}[\n\r\t]*/ies", "\$this->stripvTags('<? echo \\1; ?>')", $template);
		
		$template = preg_replace("/[\n\r\t]*\{eval\s+(.+?)\s*\}[\n\r\t]*/ies", "\$this->evalTags('\\1')", $template);
		$template = str_replace("{LF}", "<?=\"\\n\"?>", $template);
		$template = preg_replace("/\{(\\\$[a-zA-Z0-9_\-\>\[\]\'\"\$\.\x7f-\xff]+)\}/s", "<?=\\1?>", $template);
		$template = preg_replace("/$var_regexp/es", "template::addQuote('<?=\\1?>')", $template);
		$template = preg_replace("/\<\?\=\<\?\=$var_regexp\?\>\?\>/es", "\$this->addQuote('<?=\\1?>')", $template);

		$template = preg_replace("/([\n\r\t]*)\{if\s+(.+?)\}([\n\r\t]*)/ies", "\$this->stripvTags('\\1<? if(\\2) { ?>\\3')", $template);
		$template = preg_replace("/([\n\r\t]*)\{elseif\s+(.+?)\}([\n\r\t]*)/ies", "\$this->stripvTags('\\1<? } elseif(\\2) { ?>\\3')", $template);
		$template = preg_replace("/\{else\}/i", "<? } else { ?>", $template);
		$template = preg_replace("/\{\/if\}/i", "<? } ?>", $template);

		$template = preg_replace("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\}[\n\r\t]*/ies", "\$this->stripvTags('<? if(is_array(\\1)) foreach(\\1 as \\2) { ?>')", $template);
		$template = preg_replace("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}[\n\r\t]*/ies", "\$this->stripvTags('<? if(is_array(\\1)) foreach(\\1 as \\2 => \\3) { ?>')", $template);
		$template = preg_replace("/\{\/loop\}/i", "<? } ?>", $template);
		
		$template = preg_replace("/\{$const_regexp\}/s", "<?=\\1?>", $template);
		if(!empty($this->replace_code))
		{
			$template = str_replace($this->replace_code['search'], $this->replace_code['replace'], $template);
		}
		$template = preg_replace("/ \?\>[\n\r]*\<\? /s", " ", $template);

		//$template = preg_replace("/\"(http)?[\w\.\/:]+\?[^\"]+?&[^\"]+?\"/e", "\$this->transAmp('\\0')", $template);
		$template = preg_replace("/\<script[^\>]*?src=\"(.+?)\"(.*?)\>\s*\<\/script\>/ies", "\$this->stripScriptAmp('\\1', '\\2')", $template);
		return $template;
	}
	
	/**  
	 * 解析模板中的语言标签
	 * @param string $var 语言标签
	 * @return string
	 */ 
	function languageVar($var)
	{
		!isset($this->language['template']) && $this->language['template'] = array();
		$lang_var = &$this->language['template'];
		list($path,$name) = explode('/', $var);
		
		if(empty($name))
		{
			$name = $path;
			if(!isset($lang_var[$name]))
			{
				$lang = array();
				@include fimport('language/template');
				$this->language['template'] = array_merge($this->language['template'], $lang);
				if(@include(FANWE_ROOT.'tpl/'.TMPL.'/template.lang.php'))
					$this->language['template'] = array_merge($this->language['template'], $lang);
			}
		}
		
		if(!isset($lang_var[$name]))
		{
			$lang = array();
			@include fimport('language/'.$path);
			$this->language['template'] = array_merge($this->language['template'], $lang);
		}
		
		if(isset($lang_var[$name]))
			return $lang_var[$name];
		else
			return $var;
	}
	
	/**  
	 * 解析模板中的sprintf标签
	 */ 
	function sprintfTags($lang,$args = '')
	{
		$lang = $this->languageVar($lang);
		if(!empty($args))
		{
			$temp = '';
			$args = explode(',',$args);
			$jg=',';
			foreach($args as $arg)
			{
				if(preg_match('/\$.+/i',$arg))
					$temp.= $jg.$arg;	
				else
					$temp.= $jg.'"'.$arg.'"';
			}
		}
		else
			$temp='array()';
		
		$i = count($this->replace_code['search']);
		$this->replace_code['search'][$i] = $search = "<!--SPRINTF_TAG_$i-->";
		$this->replace_code['replace'][$i] = "<?php echo sprintf('$lang'$temp); ?>";
		return $search;
	}
	
	/**  
	 * 解析模板中的nl2br标签
	 */ 
	function nl2brTags($var)
	{
		if(preg_match('/\$.+/i',$var))
			$var = $var;	
		else
			$var = '"'.$var.'"';
		
		$i = count($this->replace_code['search']);
		$this->replace_code['search'][$i] = $search = "<!--SPRINTF_TAG_$i-->";
		$this->replace_code['replace'][$i] = "<?php echo nl2br($var); ?>";
		return $search;
	}
	
	/**  
	 * 解析模板中的广告位标签
	 * @param string $id 广告位ID
	 * @param string $count 显示数量
	 * @param string $target 显示条件
	 * @return string
	 */ 
	function advLayoutTags($id,$count,$target)
	{
		return "<!--dynamic advLayout args=$id,$count,$target-->";
	}
	
	/**  
	 * 解析模板中的eval标签
	 * @param string $php
	 * @return string
	 */ 
	function evalTags($php)
	{
		$php = str_replace('\"', '"', $php);
		$i = count($this->replace_code['search']);
		$this->replace_code['search'][$i] = $search = "<!--EVAL_TAG_$i-->";
		$this->replace_code['replace'][$i] = "<?php $php ?>";
		return $search;
	}
	
	/**  
	 * 解析模板中的php代码
	 * @param string $php
	 * @return string
	 */ 
	function phpTags($php)
	{
		$i = count($this->replace_code['search']);
		$this->replace_code['search'][$i] = $search = "<!--PHP_TAG_$i-->";
		$this->replace_code['replace'][$i] = "<?php $php ?>";
		return $search;
	}
	
	function avatarTags($id,$type = 's',$code = '',$is_src = 0)
	{
		if(empty($code))
			$code = '"'.$code.'"';

		$is_src = intval($is_src);
		$i = count($this->replace_code['search']);
		$this->replace_code['search'][$i] = $search = "<!--AVATAR_TAG_$i-->";
		$this->replace_code['replace'][$i] = "<?php echo avatar($id,'$type',$code,$is_src);?>";
		return $search;
	}
	
	function cutstrTags($str,$len,$dot = '')
	{
		if(empty($dot))
			$dot = '...';
		
		$i = count($this->replace_code['search']);
		$this->replace_code['search'][$i] = $search = "<!--CUTSTR_TAG_$i-->";
		$this->replace_code['replace'][$i] = "<?php echo cutStr($str,$len,'$dot');?>";
		return $search;
	}
	
	/**  
	 * 解析模板中的date标签
	 * @param string $php
	 * @return string
	 */ 
	function dateTags($time,$format = '')
	{
		$i = count($this->replace_code['search']);
		$this->replace_code['search'][$i] = $search = "<!--DATE_TAG_$i-->";
		if(empty($format))
			$this->replace_code['replace'][$i] = "<?php echo fToDate($time); ?>";
		else
			$this->replace_code['replace'][$i] = "<?php echo fToDate($time,'$format'); ?>";
		return $search;
	}
	
	/**  
	 * 解析模板中的timelag标签
	 * @param string $php
	 * @return string
	 */ 
	function timeLagTags($time)
	{
		$i = count($this->replace_code['search']);
		$this->replace_code['search'][$i] = $search = "<!--TIME_LAG_TAG_$i-->";
		$this->replace_code['replace'][$i] = "<?php echo getBeforeTimelag($time); ?>";
		return $search;
	}
	
	/**  
	 * 解析模板中的getimg标签
	 * @param string $php
	 * @return string
	 */ 
	function getImgTags($url,$width=0,$height=0,$gen=0)
	{
		if(preg_match('/\$.+/i',$width))
			$width = '"'.$width.'"';
		else
			$width = (int)$width;	
			
		if(preg_match('/\$.+/i',$height))
			$height = '"'.$height.'"';	
		else
			$height = (int)$height;
		
		$gen = (int)$gen;
		
		$i = count($this->replace_code['search']);
		$this->replace_code['search'][$i] = $search = "<!--GETIMG_TAG_$i-->";
		$this->replace_code['replace'][$i] = "<?php echo getImgName($url,$width,$height,$gen); ?>";
		return $search;
	}
	
	/**  
	 * 解析模板中的getuser标签
	 * @return string
	 */ 
	function getUserTags($uid,$is_mark,$type,$class,$tpl)
	{
		$user_type = 0;
		$is_mark = (int)$is_mark;
		$img_type = '';
		$img_size = '0';
		$link_class = '';
		$img_class = '';
		$tpl = trim($tpl);
		
		$type = trim($type);
		if(!empty($type))
		{
			$type = explode(',',$type);
			$img_type = $type[0];
			if(isset($type[1]) && !empty($type[1]))
				$img_size = $type[1];
			$user_type = 1;
		}
		
		$class = trim($class);
		if(!empty($class))
		{
			$class = explode(',',$class);
			$link_class = $class[0];
			if(isset($class[1]))
				$img_class = $class[1];
		}
		
		$i = count($this->replace_code['search']);
		$this->replace_code['search'][$i] = $search = "<!--GETUSER_TAG_$i-->";
		$this->replace_code['replace'][$i] = "<?php echo setTplUserFormat($uid,$user_type,$is_mark,'$img_type',$img_size,'$link_class','$img_class','$tpl'); ?>";
		return $search;
	}
	
	/**  
	 * 解析模板中的u标签
	 * @param string $php
	 * @return string
	 */ 
	function uTags($url,$args = '')
	{
		if(!empty($args))
		{
			$temp = 'array(';
			$args = explode(',',$args);
			$jg='';
			foreach($args as $arg)
			{
				$arg = explode('=',$arg);
				if(preg_match('/\$.+/i',$arg[1]))
					$temp.= $jg.'"'.$arg[0].'"=>'.$arg[1];	
				else
					$temp.= $jg.'"'.$arg[0].'"=>"'.$arg[1].'"';	
				
				$jg=',';
			}
			
			$temp.=')';
		}
		else
			$temp='array()';
		
		$i = count($this->replace_code['search']);
		$this->replace_code['search'][$i] = $search = "<!--U_TAG_$i-->";
		$this->replace_code['replace'][$i] = "<?php echo FU('$url',$temp); ?>";
		return $search;
	}
	
	/**  
	 * 解析模板中的css标签
	 * @param string $php
	 * @return string
	 */ 
	function cssTags($url)
	{
		$i = count($this->replace_code['search']);
		$this->replace_code['search'][$i] = $search = "<!--CSS_TAG_$i-->";
		$this->replace_code['replace'][$i] = "<?php echo cssParse($url); ?>";
		return $search;
	}
	
	/**  
	 * 解析模板中的script标签
	 * @param string $php
	 * @return string
	 */ 
	function scriptTags($url)
	{
		$i = count($this->replace_code['search']);
		$this->replace_code['search'][$i] = $search = "<!--SCRIPT_TAG_$i-->";
		$this->replace_code['replace'][$i] = "<?php echo scriptParse($url); ?>";
		return $search;
	}
	
	/**  
	 * 加载子模板
	 * @param string $file 模板文件
	 * @param string $tpl_dir 模板目录
	 * @return string
	 */ 
	function loadSubTemplate($file,$tpl_dir)
	{
		$tpl_file = template($file,$tpl_dir, 1);
		if($content = @implode('', file(FANWE_ROOT.$tpl_file)))
		{
			$this->sub_templates[] = $tpl_file;
			return $content;
		}
		else
		{
			return '<!-- '.$file.' -->';
		}
	}

	function transAmp($str)
	{
		$str = str_replace('&', '&amp;', $str);
		$str = str_replace('&amp;amp;', '&amp;', $str);
		$str = str_replace('\"', '"', $str);
		return $str;
	}

	function addQuote($var)
	{
		return str_replace("\\\"", "\"", preg_replace("/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s", "['\\1']", $var));
	}


	function stripvTags($expr, $statement = '')
	{
		$expr = str_replace("\\\"", "\"", preg_replace("/\<\?\=(\\\$.+?)\?\>/s", "\\1", $expr));
		$statement = str_replace("\\\"", "\"", $statement);
		return $expr.$statement;
	}

	function stripScriptAmp($s, $extra)
	{
		$extra = str_replace('\\"', '"', $extra);
		$s = str_replace('&amp;', '&', $s);
		return "<script src=\"$s\" type=\"text/javascript\"$extra></script>";
	}

	function stripBlock($var, $s)
	{
		$s = str_replace('\\"', '"', $s);
		$s = preg_replace("/<\?=\\\$(.+?)\?>/", "{\$\\1}", $s);
		preg_match_all("/<\?=(.+?)\?>/e", $s, $constary);
		$constadd = '';
		$constary[1] = array_unique($constary[1]);
		foreach($constary[1] as $const) {
			$constadd .= '$__'.$const.' = '.$const.';';
		}
		$s = preg_replace("/<\?=(.+?)\?>/", "{\$__\\1}", $s);
		$s = str_replace('?>', "\n\$$var .= <<<EOF\n", $s);
		$s = str_replace('<?', "\nEOF;\n", $s);
		return "<?\n$constadd\$$var = <<<EOF\n".$s."\nEOF;\n?>";
	}

	function error($message, $tpl_name)
	{
		require_once fimport('class/error');
		FanweError::templateError($message, $tpl_name);
	}
}

?>