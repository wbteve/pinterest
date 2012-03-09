<? if(!defined('IN_FANWE')) exit('Access Denied'); if(is_array($current_exp)) { foreach($current_exp as $exp) { ?> 
<a href="javascript:;" title="<?=$exp['title']?>" rel="<?=$exp['emotion']?>"><img src="<?=$exp['url']?>" height="24" /></a> 
<? } } ?>