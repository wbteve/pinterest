<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/**  
 * album.service.php
 *
 * 专辑服务
 *
 * @package service
 * @author awfigq <awfigq@qq.com>
 */
class AlbumService
{
	public function getAlbumById($id,$is_tag = true)
	{
		$id = (int)$id;
		if(!$id)
			return false;
		
		$album = FDB::fetchFirst('SELECT * FROM '.FDB::table('album').' WHERE id = '.$id);
		if($album)
		{
			$album['tags'] = array();
			if($is_tag)
			{
				$res = FDB::query('SELECT tag_name FROM '.FDB::table('album_tags_related').' WHERE album_id = '.$id);
				while($data = FDB::fetch($res))
				{
					$album['tags'][] = $data['tag_name'];
				}
				$album['tags_str'] = implode(',',$album['tags']);
			}
			return $album;
		}
		else
			return false;
	}
	
	public function getAlbumListByUid($uid,$limit = '')
	{
		$uid = (int)$uid;
		if(!$uid)
			return false;
		
		if(empty($limit))
			return FDB::fetchAll('SELECT * FROM '.FDB::table('album').' WHERE uid = '.$uid.' ORDER BY id DESC');
		else
			return FDB::fetchAll('SELECT * FROM '.FDB::table('album').' WHERE uid = '.$uid.' ORDER BY id DESC LIMIT '.$limit);
	}
	
	//is_delete_share 为真时删除专辑下的分享
	public function deleteAlbum($aid,$is_delete_share = false)
	{
		$aid = (int)$aid;
		if(!$aid)
			return false;

		$album = AlbumService::getAlbumById($aid);
		if(empty($album))
			return false;
		
		if($is_delete_share)
		{
			$res = FDB::query('SELECT share_id FROM '.FDB::table('album_share').' WHERE album_id = '.$aid);
			while($data = FDB::fetch($res))
			{
				AlbumService::deleteAlbumItem($data['share_id'],false);
			}
		}
		
		FDB::delete('album','id = '.$aid);
		FDB::delete('album_best','album_id = '.$aid);
		FDB::delete('album_share','album_id = '.$aid);
		FDB::delete('album_tags_related','album_id = '.$aid);
		
		if(!empty($album['flash_img']))
			@unlink(FANWE_ROOT.$album['flash_img']);
			
		if(!empty($album['best_img']))
			@unlink(FANWE_ROOT.$album['best_img']);
		
		if(!empty($album['tags']))
			FDB::query('UPDATE '.FDB::table('album_tags')." SET album_count = album_count - 1 WHERE tag_name ".FDB::createIN($album['tags']));
		
        FS('Share')->deleteShare($album['share_id']);

		FDB::query('UPDATE '.FDB::table('user_count').' SET
				albums = albums - 1
				WHERE uid = '.$album['uid']);
		
		FDB::query('UPDATE '.FDB::table('share')." SET type = 'default' 
			WHERE rec_id = ".$aid." AND type IN ('album_item','album_best','album_rec')");
		
		FS('Medal')->runAuto($album['uid'],'albums');
	}
	
	/**
	 * 获取会员是否已推荐专辑
	 * @return bool
	 */
	public function getIsBest($aid,$uid)
	{
		global $_FANWE;
		
		$aid = (int)$aid;
		$uid = (int)$uid;
		if(!$aid || !$uid)
			return false;
			
		$is_best = (int)FDB::resultFirst('SELECT COUNT(uid) FROM '.FDB::table('album_best').' WHERE album_id = '.$aid.' AND uid = '.$uid);
		if($is_best == 0)
			return false;
		else
			return true;
	}
	
	public function bestAlbum($aid,$uid,$content = '',$is_pub = 0)
	{
		global $_FANWE;
		
		$aid = (int)$aid;
		$uid = (int)$uid;
		if(!$aid || !$uid)
			return -1;
		
		$album = AlbumService::getAlbumById($aid);
		if(empty($album) || $album['uid'] == $uid)
			return -1;
		
		if(AlbumService::getIsBest($aid,$uid))
		{
			FDB::query('DELETE FROM '.FDB::table('album_best').' WHERE album_id = '.$aid.' AND uid = '.$uid);
			FDB::query('UPDATE '.FDB::table('album').' SET best_count = best_count - 1 WHERE id = '.$aid);
			return 0;
		}
		else
		{
			FDB::query('INSERT INTO '.FDB::table('album_best').'(album_id,uid) VALUES('.$aid.','.$uid.')');
			FDB::query('UPDATE '.FDB::table('album').' SET best_count = best_count + 1 WHERE id = '.$aid);
			
			$data = array();
			$data['share']['uid'] = $_FANWE['uid'];
			$data['share']['rec_id'] = $album['id'];
			$data['share']['title'] = addslashes($album['title']);
			$data['share']['content'] = $content;
			$data['share']['type'] = "album_best";
			$data['pub_out_check'] = $is_pub;
			
			$share = FS("Share")->save($data);
			//添加推荐消息提示
			if($share['status'])
			{		
				FS("User")->setUserTips($album['uid'],4,$share['share_id']);
			}
			return 1;
		}
	}
	
	public function deleteAlbumItem($share_id,$is_update = true)
	{
		$share_id = (int)$share_id;
		if(!$share_id)
			return false;
		
		$rec = FDB::fetchFirst('SELECT * FROM '.FDB::table('album_rec').' WHERE ashare_id = '.$share_id);
		if($rec)
		{
			FDB::delete('album_rec','ashare_id = '.$share_id);
			FDB::query('UPDATE '.FDB::table('share_rec')." SET rec_count = rec_count - 1 WHERE share_id = ".$rec['share_id']);
			$res = FDB::query('SELECT share_id,server_code FROM '.FDB::table('share_rec').' WHERE rec_count = 0');
			$id_list = array();
			while($data = FDB::fetch($res))
			{
				$id_list[$data['share_id']] = $data['server_code'];
			}
			
			if(count($id_list) > 0)
			{
				$ids = array_keys($id_list);
				$ids = implode(',',$ids);
				$res = FDB::query('SELECT share_id FROM '.FDB::table('share').' WHERE share_id IN ('.$ids.')');
				while($data = FDB::fetch($res))
				{
					unset($id_list[$data['share_id']]);
				}
				
				foreach($id_list as $id => $server_code)
				{
					FS("Share")->deleteShareImages($id,$server_code);
				}
			}
			FDB::delete('share_rec','share_id = '.$rec['share_id'].' AND rec_count = 0');
		}
		
		if($is_update)
		{
			$album_id = (int)FDB::resultFirst('SELECT album_id FROM '.FDB::table('album_share').' WHERE share_id = '.$share_id);
			FDB::delete('album_share','	share_id = '.$share_id);
			AlbumService::updateAlbumByShare($album_id,$share_id,false);
			AlbumService::updateAlbum($album_id);
		}
		FS('Share')->deleteShare($share_id);
	}
	
	public function updateAlbumByShare($aid,$share_id,$type = true)
	{
		$share = FS("Share")->getShareById($share_id);
		$update = array();
		$goods_count = (int)FDB::resultFirst('SELECT COUNT(DISTINCT goods_id) FROM '.FDB::table('share_goods').' 
			WHERE share_id = '.$share_id);
		if($goods_count > 0)
		{
			if($type)
				$update[] = 'goods_count = goods_count + '.$goods_count;
			else
				$update[] = 'goods_count = goods_count - '.$goods_count;
		}
			
		$photo_count = (int)FDB::resultFirst('SELECT COUNT(DISTINCT photo_id) FROM '.FDB::table('share_photo').' 
			WHERE share_id = '.$share_id);
		if($photo_count > 0)
		{
			if($type)
				$update[] = 'photo_count = photo_count + '.$photo_count;
			else
				$update[] = 'photo_count = photo_count - '.$photo_count;
		}
		
		$img_count = $photo_count + $goods_count;
		if($img_count > 0)
		{
			if($type)
				$update[] = 'img_count = img_count + '.$img_count;
			else
				$update[] = 'img_count = img_count - '.$img_count;
		}
		
		if($share['collect_count'] > 0)
		{
			if(!$type)
				$update[] = 'collect_count = collect_count - '.$photo_count;
		}
		
		$update = implode(',',$update);
		if(!empty($update))
			FDB::query('UPDATE '.FDB::table('album').' SET '.$update.' WHERE id = '.$aid);
	}
	
	public function updateAlbum($aid)
	{
		$aid = (int)$aid;
		if(!$aid)
			return false;

		$album = AlbumService::getAlbumById($aid);
		if(empty($album))
			return false;
		
		$share_count = (int)FDB::resultFirst('SELECT COUNT(DISTINCT share_id) FROM '.FDB::table('album_share').' WHERE album_id = '.$aid);
		
		$cache_data = array();
		
		$res = FDB::query('SELECT share_id FROM '.FDB::table('album_share').' 
			WHERE album_id = '.$aid.' ORDER BY share_id DESC LIMIT 0,6');
		$ids = array();
		while($data = FDB::fetch($res))
		{
			$ids[] = $data['share_id'];
		}
		
		if(count($ids) > 0)
		{
			$imags = array();
			$ids = implode(',',$ids);
			$res = FDB::query('SELECT share_id,cache_data FROM '.FDB::table('share').' WHERE share_id IN ('.$ids.') ORDER BY share_id DESC');
			while($data = FDB::fetch($res))
			{
				$data['cache_data'] = fStripslashes(unserialize($data['cache_data']));
				FS('Share')->shareImageFormat($data);
				unset($data['cache_data']);
				foreach($data['imgs'] as $img)
				{
					$imags[] = $img;
					if(count($imags) >= 6)
						break;
				}
				if(count($imags) >= 6)
					break;
			}
			$cache_data['imgs'] = $imags;
		}
		
		$cache_data = addslashes(serialize($cache_data));
		
		$album = array();
		$album['share_count'] = $share_count;
		$album['cache_data'] = $cache_data;
		FDB::update('album',$album,'id = '.$aid);
	}
	
	/**  
	 * 保存专辑标签
	 * @param int $aid 专辑编号
	 * @param array $tags 标签数组
	 * @return void
	 */
	public function saveTags($aid,$tags)
	{
		$aid = (int)$aid;
		if(!$aid)
			return;
		
		FDB::query('UPDATE '.FDB::table('album_tags').' SET album_count = album_count - 1 
			WHERE tag_name IN (SELECT tag_name FROM '.FDB::table('album_tags_related').' WHERE album_id = '.$aid.')');
		FDB::delete('album_tags_related','album_id = '.$aid);
		foreach($tags as $tag)
		{
			if(empty($tag))
				continue;
			
			$related = array();
			$related['tag_name'] = $tag;
			$related['album_id'] = $aid;
			if(FDB::insert('album_tags_related',$related,false,false,true))
			{
				$album_tag = FDB::fetchFirst('SELECT * FROM '.FDB::table('album_tags')." WHERE tag_name = '$tag'");
				if($album_tag)
					FDB::query('UPDATE '.FDB::table('album_tags')." SET album_count = album_count + 1 WHERE tag_name = '$tag'");
				else
				{
					$album_tag = array();
					$album_tag['tag_name'] = $tag;
					$album_tag['album_count'] = 1;
					$album_tag['is_new'] = 1;
					FDB::insert('album_tags',$album_tag);
				}
			}
		}
		FDB::fetchFirst('UPDATE '.FDB::table('album')." SET tags = '".implode(' ',$tags)."' WHERE id = $aid");
	}
	
	/**  
	 * 获取首页热门杂志
	 * @return void
	 */
	public function getIndexAlbums($num = 3)
	{
		global $_FANWE;
		$album_list = array();
		
		$sql = 'SELECT * FROM '.FDB::table('album').' 
			WHERE is_index = 1 AND img_count > 0 ORDER BY sort ASC,id DESC LIMIT 0,'.$num;
		$res = FDB::query($sql);
		while($data = FDB::fetch($res))
		{
			$data['img'] = array();
			if(!empty($data['flash_img']))
				$data['img']['img'] = $data['flash_img'];
			else
			{
				if(!empty($data['cache_data']))
				{
					$cache_data = fStripslashes(unserialize($data['cache_data']));
					$data['img'] = current($cache_data['imgs']);
					unset($data['cache_data']);
				}
			}
			$data['url'] = FU('album/show',array('id'=>$data['id']));
			$album_list[$data['id']] = $data;
		}
		return $album_list;
	}
	
	/**  
	 * 获取Flash专辑
	 * @return void
	 */
	public function getFlashAlbums($num = 3)
	{
		global $_FANWE;
		$album_list = array();
		
		$sql = 'SELECT * FROM '.FDB::table('album').' 
			WHERE is_flash = 1 AND img_count > 0 ORDER BY sort ASC,id DESC LIMIT 0,'.$num;
		$res = FDB::query($sql);
		while($data = FDB::fetch($res))
		{
			$data['img'] = array();
			if(!empty($data['flash_img']))
				$data['img']['img'] = $data['flash_img'];
			else
			{
				if(!empty($data['cache_data']))
				{
					$cache_data = fStripslashes(unserialize($data['cache_data']));
					$data['img'] = current($cache_data['imgs']);
					unset($data['cache_data']);
				}
			}
			$data['url'] = FU('album/show',array('id'=>$data['id']));
			$album_list[$data['id']] = $data;
		}
		return $album_list;
	}
	
	
	
	/**  
	 * 获取推荐专辑
	 * @return void
	 */
	public function getBestAlbums($num = 6)
	{
		global $_FANWE;
		$album_list = array();
		
		$sql = 'SELECT * FROM '.FDB::table('album').' 
			WHERE is_best = 1 AND img_count > 0 ORDER BY sort ASC,id DESC LIMIT 0,'.$num;
		$res = FDB::query($sql);
		while($data = FDB::fetch($res))
		{
			$data['img'] = array();
			if(!empty($data['best_img']))
				$data['img']['img'] = $data['best_img'];
			else
			{
				if(!empty($data['cache_data']))
				{
					$cache_data = fStripslashes(unserialize($data['cache_data']));
					$data['img'] = current($cache_data['imgs']);
					unset($data['cache_data']);
				}
			}
			$data['url'] = FU('album/show',array('id'=>$data['id']));
			$album_list[$data['id']] = $data;
		}
		return $album_list;
	}
	
	/**  
	 * 获取最新专辑作者
	 * @return void
	 */
	public function getNewUsers($num = 6)
	{
		global $_FANWE;
		$list = array();
		$user_list = array();
		
		$sql = 'SELECT DISTINCT uid FROM '.FDB::table('album').' 
			WHERE img_count > 0 ORDER BY id DESC LIMIT 0,'.$num;
		$res = FDB::query($sql);
		while($data = FDB::fetch($res))
		{
			$list[$data['uid']] = $data;
			$list[$data['uid']]['user'] = &$user_list[$data['uid']];
		}
		FS('User')->usersFormat($user_list);
		return $list;
	}
	
	/**  
	 * 获取最热专辑作者
	 * @return void
	 */
	public function getHotUsers($num = 6)
	{
		global $_FANWE;
		$list = array();
		$user_list = array();
		
		$sql = 'SELECT DISTINCT uid FROM '.FDB::table('album').' 
			WHERE img_count > 0 ORDER BY collect_count DESC,id DESC LIMIT 0,'.$num;
		$res = FDB::query($sql);
		while($data = FDB::fetch($res))
		{
			$list[$data['uid']] = $data;
			$list[$data['uid']]['user'] = &$user_list[$data['uid']];
		}
		
		FS('User')->usersFormat($user_list);
		return $list;
	}
}
?>