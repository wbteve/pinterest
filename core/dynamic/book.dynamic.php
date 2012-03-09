<?php
function getShareByUserClickShareID()
{
	global $_FANWE;
	$share_id = (int)$_FANWE['user_click_share_id'];
	if($share_id > 0)
	{
		$share = FS('Share')->getShareById($share_id);
		if($share)
		{
			$temp_share[] = $share;
			$temp_share = FS('Share')->getShareDetailList($temp_share,false,false,false,true,5);
			$temp_share = $temp_share[$share_id];
			
			$args = array(
				'share'=>&$temp_share,
			);
			
			$result = array();
			$result['share_id'] = $share_id;
			switch(ACTION_NAME)
			{
				case 'dapei':
					$result['html'] = tplFetch('inc/book/book_dapei',$args);
				break;
				
				case 'look':
					$result['html'] = tplFetch('inc/book/book_look',$args);
				break;
				
				default:
					$result['html'] = tplFetch('inc/book/book_index',$args);
				break;
			}
			
			return "ShowUserClickShare(".outputJson($result,false).");";
		}
	}
	
	return '';
}
?>