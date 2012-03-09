<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: awfigq <awfigq@qq.com>
// +----------------------------------------------------------------------
/**
 +------------------------------------------------------------------------------
 * 管理员模型
 +------------------------------------------------------------------------------
 */
class ShareModel extends CommonModel
{
	public function removeHandler($share_ids)
	{
		if(!is_array($share_ids))
			$share_ids = array($share_ids);
			
		$condition = array ('share_id' => array ('in',$share_ids));
        $res = D('Share')->where ( $condition )->findAll();
		
		foreach($res as $item)
		{
			$share_id = intval($item['share_id']);
			switch($item['type'])
			{
				case 'ask':
					FS("Ask")->deleteTopic($share_id);
				break;

				case 'ask_post':
					FS("Ask")->deletePost($share_id);
				break;

				case 'bar':
					FS("Topic")->deleteTopic($item['rec_id']);
				break;

				case 'bar_post':
					FS("Topic")->deletePost($share_id);
				break;
				
				case 'ershou':
					FS("Second")->deleteGoods($item['rec_id']);
				break;
				
				case 'album':
					FS('Album')->deleteAlbum($share['rec_id']);
				break;
				
				case 'album_item':
					FS('Album')->deleteAlbumItem($share_id);
				break;

				default:
					FS("Share")->deleteShare($share_id);
				break;
			}
		}
	}
}
?>