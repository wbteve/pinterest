<?php
/**
 * 分页的信息加入条件的数组
 *
 * @access  public
 * @return  array
 */
function pageAndSize($filter)
{
	global $_FANWE;
	
	$filter['pageSize'] = ($filter['pageSize'] == 0) ? ($_FANWE['setting']['page_listrows'] > 0 ? $_FANWE['setting']['page_listrows'] : 20) : $filter['pageSize'];

	/* 每页显示 */
	$filter['page'] = (empty($filter['page']) || intval($filter['page']) <= 0) ? 1 : intval($filter['page']);

	/* page 总数 */
	$filter['pageCount'] = (!empty($filter['recordCount']) && $filter['recordCount'] > 0) ? ceil($filter['recordCount'] / $filter['pageSize']) : 1;

	/* 边界处理 */
	if ($filter['page'] > $filter['pageCount'])
	{
		$filter['page'] = $filter['pageCount'];
	}
	
	$filter['pageNumber'] = array();
	
	for ($i=1;$i<=$filter['pageCount'];$i++)
	{
		$filter['pageNumber'][] = $i;
	}

	$filter['start'] = ($filter['page'] - 1) * $filter['pageSize'];

	return $filter;
}

function getPageNumList($page,$total,$num = 5)
{
	$nums = array();
	
	if($total <= $num * 2)
	{
		for ($i=1;$i<=$total;$i++)
		{
			$nums[] = $i;
		}
	}
	else
	{
		if($page - $num < 2)
		{
			$temp = $num * 2;
			
			for ($i=1;$i<=$temp;$i++)
			{
				$nums[] = $i;
			}
			
			$nums[] = "...";
			$nums[] = $total;
		}
		else
		{
			$nums[] = 1;
			$nums[] = "...";
			$start = $page - $num + 1;
			$end = $page + $num - 1;
			
			if($total - $end > 1)
			{
				for ($i=$start;$i<=$end;$i++)
				{
					$nums[] = $i;
				}
				
				$nums[] = "...";
				$nums[] = $total;
			}
			else
			{
				$start = $pager['page_count'] - $offset * 2 + 1;
				$end = $total;
				for ($i=$start;$i<=$end;$i++)
				{
					$nums[] = $i;
				}
			}
		}
	}
	
	return $nums;
}

function sortFlag($filter,$pathName = 'sort')
{
	$flag['tag']    = 'sort_' . preg_replace('/^.*\./', '', $filter['sort_by']);
	$flag['img']    = '<img src="'.TPL_PATH.'images/'.$pathName. ($filter['sort_order'] == "DESC" ? '_desc.gif' : '_asc.gif') . '"/>';

	return $flag;
}
?>
