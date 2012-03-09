<?php
function bindCacheWord()
{
	$list = array();
	$res = FDB::query("SELECT w.* FROM ".FDB::table('word')." AS w 
		LEFT JOIN ".FDB::table('word_type')." AS wt ON wt.id = w.cid 
		WHERE w.status = 1 AND (w.cid = 0 OR wt.status = 1)");
	while($data = FDB::fetch($res))
	{
		if(preg_match('/^\/(.+?)\/$/', $data['word'], $a)) {
			switch($data['type'])
			{
				case 1:
					$list['banned'][] = $data['word'];
					break;
				default:
					$list['filter']['find'][] = $data['word'];
					$list['filter']['replace'][] = preg_replace("/\((\d+)\)/", "\\\\1", $data['replacement']);
					break;
			}
		} else {
			$data['word'] = preg_replace("/\\\{(\d+)\\\}/", ".{0,\\1}", preg_quote($data['word'], '/'));
			switch($data['type'])
			{
				case 1:
					$banned[] = $data['word'];
					$bannednum ++;
					if($bannednum == 1000) {
						$list['banned'][] = '/('.implode('|', $banned).')/i';
						$banned = array();
						$bannednum = 0;
					}
					break;
				default:
					$list['filter']['find'][] = '/'.$data['word'].'/i';
					$list['filter']['replace'][] = $data['replacement'];
					break;
			}
		}
	}
	
	if($banned)
		$list['banned'][] = '/('.implode('|', $banned).')/i';
	
	FanweService::instance()->cache->saveCache('words', $list);
}
?>