<?php
/**
 *
 * @param $cids  分类ID数组
 * @param $limit
 */
function getHotTags($cids = array(),$cate_code,$limit)
{
	$hot_tags = array();
	$sql = 'SELECT gt.tag_name,gt.tag_code,gt.count,gt.is_hot
		FROM '.FDB::table('goods_tags').' as gt
		INNER JOIN '.FDB::table("goods_category_tags").' AS gct ON gct.tag_id = gt.tag_id
		WHERE gct.cate_id IN ('.implode(',',$cids).')
		ORDER BY gt.count DESC,gt.sort ASC LIMIT 0,'.$limit;

	$res  = FDB::query($sql);
	while($data = FDB::fetch($res))
	{
		$data['url'] = FU("book/cate",array('cate'=>$cate_code,'tag'=>urlencode($data['tag_name'])));
		$hot_tags[] = $data;
	}
	return $hot_tags;
}
?>