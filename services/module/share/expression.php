<?php
$cache_file = getTplCache('services/share/expression');
if(!@include($cache_file))
{
	$expressions = getCache('expression');
	if($expressions === NULL)
	{
		$expressions = array();
		$res = FDB::query('SELECT type,title,emotion,filename 
			FROM '.FDB::table('expression').' ORDER BY type');
		while($data = FDB::fetch($res))
		{
			$data['url'] = './public/expression/'.$data['type'].'/'.$data['filename'];
			$data['emotion'] = str_replace(array('[',']'),array('',''),$data['emotion']);
			$expressions[$data['type']][] = $data;
		}
		setCache('expression',$expressions);
	}
	
	$expressions_js = array();
	foreach($expressions as $ekty => $elist)
	{
		$args = array(
			'current_exp'=>&$elist,
		);
		
		$expressions_js[$ekty] = tplFetch("services/share/expression_item",$args);
	}
	
	$expressions_json = getJson($expressions_js);
	$current_exp = current($expressions_js);
	include template('services/share/expression');
}			
display($cache_file);
?>