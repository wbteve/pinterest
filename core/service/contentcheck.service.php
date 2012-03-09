<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/**  
 * contentcheck.service.php
 *
 * 敏感词检测服务
 *
 * @package service
 * @author awfigq <awfigq@qq.com>
 */
 
define('FANWE_WORD_SUCCEED', 0);
define('FANWE_WORD_BANNED', 1);
define('FANWE_WORD_REPLACED', 2);

class ContentCheckService
{
	var $result;
	var $words_found = array();
	var $highlight = '#f00';
	var $content = '';
	
	function ContentCheckService()
	{
		Cache::getInstance()->loadCache('words');
	}
	
	/**  
	 * 检测敏感词
	 * @param string $content 需要检测的文本
	 * @return bool
	 */
	public function check(&$content)
	{
		global $_FANWE;
		$words = &$_FANWE['cache']['words'];
		$limit_num = 1000;
		$this->words_found = array();
		if(is_array($words['banned']) && !empty($words['banned']))
		{
			foreach($words['banned'] as $banned_words)
			{
				if(preg_match_all($banned_words,$content, $matches))
				{
					$this->words_found = $matches[0];
					$this->result = FANWE_WORD_BANNED;
					$this->words_found = array_unique($this->words_found);
					$this->highlight($content,$banned_words);
					return FANWE_WORD_BANNED;
				}
			}
		}
		
		if(!empty($words['filter']))
		{
			$i = 0;
			while($find_words = array_slice($words['filter']['find'], $i, $limit_num))
			{
				if(empty($find_words))
					break;
				
				$replace_words = array_slice($words['filter']['replace'],$i,$limit_num);
				$i += $limit_num;
				$content = preg_replace($find_words,$replace_words,$content);
			}
			$this->result = FANWE_WORD_REPLACED;
			return FANWE_WORD_REPLACED;
		}
		
		$this->result = FANWE_WORD_SUCCEED;
		return FANWE_WORD_SUCCEED;
	}
	
	public function highlight($content, $words_regex)
	{
		$color = $this->highlight;
		if(empty($color))
			return;
		
		$this->content = preg_replace($words_regex, '<span style="color:'.$color.';">\\1</span>', $content);
	}
}
?>