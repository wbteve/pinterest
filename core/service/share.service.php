<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/**
 * share.service.php
 *
 * 分享服务类
 *
 * @package service
 * @author awfigq <awfigq@qq.com>
 */
class ShareService
{
	/*===========分享列表、详细 Begin==============*/
	/**
	 * 检测分享中的敏感词
	 * @param string $content 分享内容
	 * @param string $type content,title,tag 检测类型
	 * @return array(
	 *   'error_code' => '错误代码'
	 *   'error_msg' => 错误描述
	 * )
	 */
	public function checkWord(&$content,$type)
	{
		$result = array('error_code'=>0,'error_msg'=>'');
		$server = FS("ContentCheck");
		if($server->check($content) > 0)
		{
			$words_found = implode("，", $server->words_found);
			$tt_str = lang('share','word_'.$type);
			$result['error_code'] = $server->result;
			$result['error_msg'] = sprintf(lang('share','word_tips_'. $server->result),$tt_str,$words_found);
		}
		return $result;
	}

	/**
	 * 通过的分享提交表单的数据处理
	 * @param mix $_POST 为标准分享表单 $_POST['type'] default:默认,bar:主题,ershou:二手,ask:问答
	 * $_POST['share_data'] = photo 有图 goods 有产品 goods_photo:有图有商品 default:都没有
	 * 	* 返回
	 *  array(
	 *   'status' => xxx  状态  bool
	 *   'share_id' => share_id
	 *   'error_code' => '错误代码'
	 *   'error_msg' => 错误描述
	 * )
	 */
	public function submit($_POST,$is_check = true,$is_score = true)
	{
		//创建分享数据
		global $_FANWE;
		$share_content = htmlspecialchars(trim($_POST['content']));
		$share_data = array();
		$share_data['content'] = $share_content;
		$share_data['uid'] = intval($_FANWE['uid']);
		$share_data['parent_id'] = intval($_POST['parent_id']); //分享的转发
		$share_data['rec_id'] = intval($_POST['rec_id']); //关联的编号
		$share_data['base_id'] = intval($_POST['base_id']);
		$share_data['albumid'] = intval($_POST['albumid']);
		
		if($is_check)
		{
			$check_result = ShareService::checkWord($share_data['content'],'content');
			if($check_result['error_code'] == 1)
			{
				$check_result['status'] = false;
				return $check_result;
			}
		}

		/*//当为转发的时候，获取原创ID
		if($share_data['parent_id'] > 0 && $share_data['base_id'] == 0)
		{
			$base_id = intval(FDB::resultFirst('SELECT base_id
				FROM '.FDB::table("share").'
				WHERE share_id = '.$share_data['parent_id']));

			$share_data['base_id'] = $base_id == 0 ? $share_data['parent_id'] : $base_id;
		}*/

		if(isset($_POST['type']))
			$share_data['type'] = $_POST['type'];

		$share_data['title'] = isset($_POST['title']) ? htmlspecialchars(trim($_POST['title'])) : '';
		if(!empty($share_data['title']) && $is_check)
		{
			$check_result = ShareService::checkWord($share_data['title'],'title');
			if($check_result['error_code'] == 1)
			{
				$check_result['status'] = false;
				return $check_result;
			}
		}
		
		$data['share'] = $share_data;

		//创建分享商品数据
		$share_goods_data = array();
		if(isset($_POST['goods']) && is_array($_POST['goods']) && count($_POST['goods']) > 0)
		{
			$share_goods = $_POST['goods'];
			foreach($share_goods as $goods)
			{
				$goods = unserialize(authcode($goods,'DECODE'));
				$gkey = $goods['item']['key'];
				$c_data = array();
				$c_data['img'] = $goods['item']['img'];
				$c_data['server_code'] = $goods['item']['server_code'];
				$c_data['goods_key'] = $gkey;
				$c_data['name'] = addslashes(htmlspecialchars($goods['item']['name']));
				$c_data['url'] = $goods['item']['url'];
				$c_data['taoke_url'] = $goods['item']['taoke_url'];
				$c_data['price'] = $goods['item']['price'];
				$c_data['sort'] = isset($_POST['goods_sort'][$gkey]) ? intval($_POST['goods_sort'][$gkey]) : 10;
				$c_data['shop_name'] = addslashes(htmlspecialchars($goods['shop']['name']));
				$c_data['shop_logo'] = $goods['shop']['logo'];
				$c_data['shop_server_code'] = $goods['shop']['server_code'];
				$c_data['shop_url'] = $goods['shop']['url'];
				$c_data['shop_taoke_url'] = $goods['shop']['taoke_url'];
				array_push($share_goods_data,$c_data);
			}
		}
		$data['share_goods'] = $share_goods_data;
		//创建图库数据
		$share_photos_data = array();
		if(isset($_POST['pics']) && is_array($_POST['pics']) && count($_POST['pics']) > 0)
		{
			$share_photos = $_POST['pics'];
			foreach($share_photos as $pkey => $photo)
			{
				$photo = authcode($photo,'DECODE');
				$photo = unserialize($photo);
				$c_data = array();
				$c_data['img'] = $photo['path'];
				$c_data['server_code'] = $photo['server_code'];

				$type = $photo['type'];
				if(empty($type) || !in_array($type,array('default', 'dapei', 'look')))
					$type = 'default';
				$c_data['type'] = $type;
				$c_data['sort'] = isset($_POST['pics_sort'][$pkey]) ? intval($_POST['pics_sort'][$pkey]) : 10;
				array_push($share_photos_data,$c_data);
			}
		}
		$data['share_photo'] = $share_photos_data;
		
		if($share_data['albumid'] > 0 && count($share_photos_data) == 0 && count($share_goods_data) == 0)
			exit;

		$data['share_tag'] = array();

		if(isset($_POST['tags']) && trim($_POST['tags']) != '')
		{
			$tags = htmlspecialchars(trim($_POST['tags']));
			if($is_check)
			{
				$check_result = ShareService::checkWord($tags,'tag');
				if($check_result['error_code'] == 1)
				{
					$check_result['status'] = false;
					return $check_result;
				}
			}
			$tags = str_replace('　',' ',$tags);
			$data['share_tag'] = explode(' ',$tags);
		}
		
		$data['pub_out_check'] = intval($_POST['pub_out_check']);  //发送到外部微博
		$result = ShareService::save($data,$is_score);
		return $result;
	}

	/**
	 * 保存分享数据
	 * 注：所有图片地址经处理过并转存过的临时图片或远程图片
	 * $data = array( //分享的基本数据
	 *  'share'=>array(
	 * 	  'uid'=> xxx, //分享的会员ID
	 * 	  'parent_id'	=>	xxx //转发的分享ID
	 * 	  'content'	=>	xxx //分享的内容
	 * 	  'type'=> xxx  //分享的来源，默认为default
	 *    'title' => xxx //分享的标题
	 *    'base_id' => xxx //原创ID
	 * 	),
	 *
	 *  'share_photo'=>array( //图库  #可选#
	 *    array(  //多张图
	 *    'img' => xxx //原图
	 *    )
	 *  ),
	 *  'share_goods'=>array( //分享的商品 #可选#
	 *    array(
	 *    'img' => xxx  //商品图
	 *    'name' => xxx //品名
	 *    'url'  => xxx //商品地址
	 *    'price' => xxx  //价格
	 *    'shop_name' => xxx //商户名称
	 *    'shop_logo' => xxx //商户的logo
	 *    'shop_url' => xxx //商户地址
	 *    ) //多个商品
	 *  ),
	 *  'share_tag' => array(xxx,xxx,xxx),  //该分享的标签
	 * );
	 *
	 * 返回
	 * array(
	 *   'status' => xxx  状态  bool
	 *   'share_id' => share_id
	 * )
	 */
	public function save($data,$is_score = true)
	{
		global $_FANWE;
		//保存分享数据
		$share_data = $data['share'];
		$share_album_id = (int)$share_data['albumid'];
		unset($share_data['albumid']);
		$share_data['create_time'] = TIME_UTC;
		$share_data['day_time'] = getTodayTime();
		$share_id = FDB::insert('share',$share_data,true);
		if(intval($share_id)>0)
		{
			$share_data_now_type = $share_data['type'];
			$share_data_rec_id = $share_data['rec_id'];
			$share_server_code = array();
			if(empty($share_data_now_type))
				$share_data_now_type = 'default';
						
			/*//是否是回复 是的 话 添加评论消息提示
			if(intval($share_data['parent_id']) > 0)
			{
				$base_share_id = FDB::resultFirst("select uid from ".FDB::table('share')." where share_id = ".$share_data['parent_id']);
				$result = FDB::query("INSERT INTO ".FDB::table('user_notice')."(uid, type, num, create_time) VALUES('$base_share_id',3,1,'".TIME_UTC."')", 'SILENT');
				if(!$result)
					FDB::query("UPDATE ".FDB::table('user_notice')." SET num = num + 1, create_time='".TIME_UTC."' WHERE uid='$base_share_id' AND type=3");
			}*/
			
			//保存话题
			$is_event = false;
			$event_list = array();
			$pattern = "/#([^\f\n\r\t\v]{1,80}?)#/";
			preg_match_all($pattern,$share_data['content'],$event_list);
			if(!empty($event_list[1]))
			{
				array_unique($event_list[1]);
				foreach($event_list[1] as $v)
				{			
					$event_id = (int)FDB::resultFirst("select id from ".FDB::table("event")." where `title`='".$v."'");
					if($event_id == 0)
					{
						$event_data = array();
						$event_data['uid'] = $share_data['uid'];
						$event_data['title'] = $v;
						$event_data['share_id'] = $share_id;
						$event_data['create_time'] = TIME_UTC;
						$event_data['last_share'] = $share_id;
						$event_data['last_time'] = TIME_UTC;
						FDB::insert("event",$event_data);
					}
					else
					{
						$event_data = array();
						$event_data['event_id'] = $event_id;
						$event_data['uid'] = $share_data['uid'];
						$event_data['share_id'] = $share_id;
						FDB::insert("event_share",$event_data);
						FDB::query("update ".FDB::table("event")." set thread_count = thread_count+1,last_share=".$share_id.",last_time=".TIME_UTC." where id = $event_id");
					}
				}
			}
			
			$share_cates = array();
			$result['status'] = true;
			$result['share_id'] = $share_id;
			
			/*$content_match = FS('Words')->segment(clearExpress($share_data['content']),100);
			$title_tags = FS('Words')->segment($share_data['title'],100);
            if(!empty($title_tags))
				$content_match = array_merge($content_match, $title_tags);*/

			$content_match = clearExpress($share_data['content']);
            $content_match .= $share_data['title'];
			
			$is_rel_imgage = false;
			$weibo_img = '';
			$weibo_img_sort = 100000;
			$photo_count = 0;

			$server_args = array();
			FS("Image")->getImageArgs(&$server_args);

			//保存分享图片
			$share_photo = $data['share_photo'];
			foreach($share_photo as $k=>$photo)
			{
				if($photo_count >= $_FANWE['setting']['share_pic_count'])
					break;
				
				$o_img = false;
				if(FS("Image")->getIsServer() && !empty($photo['server_code']))
				{
					$server = FS("Image")->getServer($photo['server_code']);
					if(!empty($server))
					{
						$server_args['share_id'] = $share_id;
						$server_args['img_path'] = $photo['img'];
						$server = FS("Image")->getImageUrlToken($server_args,$server,1);
						$body = FS("Image")->sendRequest($server,'saveshare',true);
						if(!empty($body))
						{
							$o_img = unserialize($body);
							FS("Image")->setServerUploadCount($o_img['server_code']);
							$share_server_code[] = $o_img['server_code'];
							$o_img['url'] = str_replace('./','./'.$o_img['server_code'].'/',$o_img['url']);
							$weibo_img_url = FS("Image")->getImageUrl($o_img['url'],1);
						}
					}
				}
				else
				{
					$o_img = copyImage($photo['img'],array(),'share',true,$share_id);
					$weibo_img_url = FS("Image")->getImageUrl($o_img['url'],1);
				}

				if(!empty($o_img))
				{
					if($photo['sort'] < $weibo_img_sort)
					{
						$weibo_img = $weibo_img_url;
						$weibo_img_sort = $photo['sort'];
					}
					
					$share_photo_data['uid'] = $_FANWE['uid'];
					$share_photo_data['share_id'] = $share_id;
					$share_photo_data['img'] =  $o_img['url'];
					$share_photo_data['type'] =  $photo['type'];
					$share_photo_data['sort'] =  $photo['sort'];
					$share_photo_data['img_width'] =  $o_img['width'];
					$share_photo_data['img_height'] =  $o_img['height'];
					$share_photo_data['server_code'] =  $photo['server_code'];
					FDB::insert('share_photo',$share_photo_data,true);
					$photo_count++;
				}
			}
			
			//保存引用图片
			if(isset($data['rel_photo']))
			{
				$share_photo = $data['rel_photo'];
				foreach($share_photo as $share_photo_data)
				{
					if($photo_count >= $_FANWE['setting']['share_pic_count'])
						break;
					
					$is_rel_imgage = true;
					if($share_photo_data['sort'] < $weibo_img_sort)
					{
						$weibo_img = FS("Image")->getImageUrl($share_photo_data['img'],1);
						$weibo_img_sort = $share_photo_data['sort'];
					}
					
					$share_photo_data['uid'] = $_FANWE['uid'];
					$share_photo_data['share_id'] = $share_id;
					FDB::insert('share_photo',$share_photo_data,true);
					$photo_count++;
				}
			}
			
			$shop_ids = array();
			$goods_count = 0;
			//保存分享的商品
			if(isset($data['share_goods']))
			{
				$share_goods = $data['share_goods'];
				foreach($share_goods as $goods)
				{
					if($goods_count >= $_FANWE['setting']['share_goods_count'])
						break;
	
					$shop_id = 0;
					if(!empty($goods['shop_url']))
					{
						$shop_id = FDB::resultFirst('SELECT shop_id
							FROM '.FDB::table('shop').'
							WHERE shop_url = \''.$goods['shop_url'].'\'');
	
						if(intval($shop_id) == 0)
						{
							$content_match .= $goods['shop_name'];
							$shop_logo['url'] = '';
							if(!empty($goods['shop_logo']))
							{
								if(FS("Image")->getIsServer() && !empty($goods['shop_server_code']))
								{
									$server = FS("Image")->getServer($goods['shop_server_code']);
									if(!empty($server))
									{
										$args = array();
										$args['pic_url'] = $goods['shop_logo'];
										$server = FS("Image")->getImageUrlToken($args,$server,1);
										$body = FS("Image")->sendRequest($server,'saveshop',true);
										if(!empty($body))
										{
											$shop_logo = unserialize($body);
											FS("Image")->setServerUploadCount($shop_logo['server_code']);
											$shop_logo['url'] = str_replace('./','./'.$shop_logo['server_code'].'/',$shop_logo['url']);
										}
									}
								}
								else
									$shop_logo = copyFile($goods['shop_logo'],'shop',true);
							}

							$shop_data['shop_name'] = $goods['shop_name'];
							$shop_data['shop_logo'] =  $shop_logo['url'];
							$shop_data['server_code'] = $goods['shop_server_code'];
							$shop_data['shop_url'] = $goods['shop_url'];
							$shop_data['taoke_url'] = $goods['shop_taoke_url'];
							$shop_id = FDB::insert('shop',$shop_data,true);
						}
						
						if($shop_id > 0)
							$shop_ids[] = $shop_id;
					}

					if(FS("Image")->getIsServer() && !empty($goods['server_code']))
					{
						$server = FS("Image")->getServer($goods['server_code']);
						if(!empty($server))
						{
							$server_args['share_id'] = $share_id;
							$server_args['img_path'] = $goods['img'];
							$server = FS("Image")->getImageUrlToken($server_args,$server,1);
							$body = FS("Image")->sendRequest($server,'saveshare',true);
							if(!empty($body))
							{
								$goods_img = unserialize($body);
								FS("Image")->setServerUploadCount($goods_img['server_code']);
								$share_server_code[] = $goods_img['server_code'];
								$goods_img['url'] = str_replace('./','./'.$goods_img['server_code'].'/',$goods_img['url']);
								$weibo_img_url = FS("Image")->getImageUrl($goods_img['url'],1);
							}
						}
					}
					else
					{
						$goods_img = copyImage($goods['img'],array(),'share',true,$share_id);
						$weibo_img_url = FS("Image")->getImageUrl($goods_img['url'],1);
					}
					
					if(!empty($goods_img))
					{
						if($goods['sort'] < $weibo_img_sort)
						{
							$weibo_img = $weibo_img_url;
							$weibo_img_sort = $goods['sort'];
						}
							
						$shop_id = intval($shop_id);
						//开始保存分享的商品
						$share_goods_data['uid'] = $_FANWE['uid'];
						$share_goods_data['share_id'] = $share_id;
						$share_goods_data['shop_id'] = $shop_id;
						$share_goods_data['img'] =  $goods_img['url'];
						$share_goods_data['name'] = $goods['name'];
						$share_goods_data['url'] = $goods['url'];
						$share_goods_data['price'] = $goods['price'];
						$share_goods_data['sort'] = $goods['sort'];
						$share_goods_data['taoke_url'] = $goods['taoke_url'];
						$share_goods_data['goods_key'] = $goods['goods_key'];
						$share_goods_data['img_width'] =  $goods_img['width'];
						$share_goods_data['img_height'] =  $goods_img['height'];
						$share_goods_data['server_code'] = $goods['server_code'];
						FDB::insert('share_goods',$share_goods_data,true);
	
						$goods_tags = FS('Words')->segment($goods['name'],10);
						if(!empty($goods_tags))
						{
							$share_cates[] = ShareService::getCateByTags($goods_tags);
						}
						$content_match .= $goods['name'];
						$goods_count++;
					}
				}
			}
			
			//保存引用商品
			if(isset($data['rel_goods']))
			{
				$share_goods = $data['rel_goods'];
				foreach($share_goods as $share_goods_data)
				{
					if($goods_count >= $_FANWE['setting']['share_goods_count'])
						break;
					
					$is_rel_imgage = true;
					$shop_ids[] =  $share_goods_data['shop_id'];
					if($share_goods_data['sort'] < $weibo_img_sort)
					{
						$weibo_img = $weibo_img = FS("Image")->getImageUrl($share_goods_data['img'],1);
						$weibo_img_sort = $share_goods_data['sort'];
					}
					
					$share_goods_data['uid'] = $_FANWE['uid'];
					$share_goods_data['share_id'] = $share_id;
					FDB::insert('share_goods',$share_goods_data,true);
					$goods_tags = FS('Words')->segment($goods['name'],10);
					if(!empty($goods_tags))
					{
						$share_cates[] = ShareService::getCateByTags($goods_tags);
					}
					$content_match .= $goods['name'];
					$goods_count++;
				}
			}

			if($goods_count > 0 && $photo_count > 0)
				$share_data_type = 'goods_photo';
			elseif($goods_count > 0)
				$share_data_type = 'goods';
			elseif($photo_count > 0)
				$share_data_type = 'photo';
			else
				$share_data_type = 'default';
			
			$update_share = array();
			$update_share['share_data'] = $share_data_type;
			if(count($share_server_code) > 0)
			{
				$share_server_code = array_unique($share_server_code);
				$update_share['server_code'] = implode(',',$share_server_code);
			}
			
			if($share_album_id > 0 && in_array($share_data_type,array('goods','photo','goods_photo')))
			{
				$album = FDB::fetchFirst('SELECT cid,id,title FROM '.FDB::table('album').' WHERE id = '.$share_album_id);
				if($album)
				{
					$update_share['type'] = 'album_item';
					$share_data_now_type = 'album_item';
					$share_data_rec_id = $album['id'];
					$share_data_rec_cate = $album['cid'];
					$update_share['rec_id'] = $album['id'];
					$update_share['title'] = addslashes($album['title']);
				}
				else
				{
					$update_share['rec_id'] = 0;
					$share_data_rec_id = 0;
				}
			}
			else
				$share_data_rec_id = 0;
			
			FDB::update("share",$update_share,"share_id=".$share_id);

			//更新会员统计
			FDB::query('UPDATE '.FDB::table('user_count').'
				SET shares = shares + 1,goods = goods + '.$goods_count.',photos = photos + '.$photo_count.'
				WHERE uid = '.$share_data['uid']);
			
			FS('Medal')->runAuto($share_data['uid'],'shares');
			FS('User')->medalBehavior($share_data['uid'],'continue_share');
			
			switch($share_data_type)
			{
				case 'goods_photo':
					FS('Medal')->runAuto($share_data['uid'],'goods');
					FS('User')->medalBehavior($share_data['uid'],'continue_goods');
					FS('Medal')->runAuto($share_data['uid'],'photos');
					FS('User')->medalBehavior($share_data['uid'],'continue_photo');
				break;
				case 'goods':
					FS('Medal')->runAuto($share_data['uid'],'goods');
					FS('User')->medalBehavior($share_data['uid'],'continue_goods');
				break;
				case 'photo':
					FS('Medal')->runAuto($share_data['uid'],'photos');
					FS('User')->medalBehavior($share_data['uid'],'continue_photo');
				break;
			}

			if(in_array($share_data_type,array('goods','photo','goods_photo')))
			{
				//更新会员发布的有图分享编号
				//FS('User')->setShareIds($share_data['uid'],$share_id);

				if(!empty($share_cates))
				{
					$cids = ShareService::getCidsByCates($share_cates);
				}

				if(!empty($data['share_tag']))
				{
					if(empty($cids))
					{
						$share_cates = array();
						$share_cates[] = ShareService::getCateByTags($data['share_tag']);
						$cids = ShareService::getCidsByCates($share_cates);
					}

					//$content_match = array_merge($content_match, $data['share_tag']);
				}

				//保存标签
				$share_tags = array();
				foreach($data['share_tag'] as $tag)
				{
					if(trim($tag) != '' && !in_array($tag,$share_tags))
					{
						array_push($share_tags,$tag);

						//为已存在的tags更新统计
						FDB::query('UPDATE '.FDB::table('goods_tags').'
							SET count = count + 1
							WHERE tag_name = \''.$tag.'\'');

						//数量大于100时为热门标签
						FDB::query('UPDATE '.FDB::table('goods_tags').'
							SET is_hot = 1
							WHERE tag_name = \''.$tag.'\' AND count >= 100');

						$content_match.=$tag;
						$tag_data = array();
						$tag_data['share_id'] = $share_id;
						$tag_data['tag_name'] = $tag;
						FDB::insert('share_tags',$tag_data);
					}
				}
				unset($share_tags);

				if(!empty($cids))
				{
					foreach($cids as $cid)
					{
						$cate_data = array();
						$cate_data['share_id'] = $share_id;
						$cate_data['cate_id'] = $cid;
						FDB::insert('share_category',$cate_data);
					}
				}

				//保存匹配查询
				$share_match['share_id'] = $share_id;
				$share_match['content_match'] = segmentToUnicode(clearSymbol($content_match));
				FDB::insert("share_match",$share_match);
			}
			
			ShareService::updateShareCache($share_id);
			
			if($share_data_rec_id > 0)
			{
				$album_share = array();
				$album_share['album_id'] = $share_data_rec_id;
				$album_share['share_id'] = $share_id;
				$album_share['cid'] = $share_data_rec_cate;
				$album_share['create_day'] = getTodayTime();
				FDB::insert("album_share",$album_share);
				
				FS('Album')->updateAlbumByShare($share_data_rec_id,$share_id);
				FS('Album')->updateAlbum($share_data_rec_id);
			}
			
			if(count($shop_ids) > 0)
				FS("Shop")->updateShopStatistic($shop_ids);
			
			//保存提到我的
			$atme_share_type = FDB::resultFirst("select `type` from ".FDB::table("share")." where `share_id`='".$share_id."'");
			if($atme_share_type != "fav")
			{
				$atme_list = array();
				$pattern = "/@([^\f\n\r\t\v@ ]{2,20}?)(?:\:| )/";
				preg_match_all($pattern,$share_data['content'],$atme_list);
				if(!empty($atme_list[1]))
				{
					$atme_list[1] = array_unique($atme_list[1]);
					$users = array();
					foreach($atme_list[1] as $user)
					{
						if(!empty($user))
						{
							$users[] = $user;
						}
					}
					
					$res = FDB::query('SELECT uid 
						FROM '.FDB::table('user').'
						WHERE user_name '.FDB::createIN($users));
					while($data = FDB::fetch($res))
					{
						FS("User")->setUserTips($data['uid'],4,$share_id);
					}
				}
			}
			
			if($is_score && !in_array($share_data_now_type,array('fav','album_best','album_rec')))
			{
				if(!$is_rel_imgage && in_array($share_data_type,array('goods','photo','goods_photo')))
					FS("User")->updateUserScore($share_data['uid'],'share','image',$share_data['content'],$share_id);
				else
					FS("User")->updateUserScore($share_data['uid'],'share','default',$share_data['content'],$share_id);
			}
			
			if($data['pub_out_check'])
			{
				$weibo = array();
				$weibo['content'] = $share_data['content'];
				$weibo['img'] = $weibo_img;
				$weibo['ip'] = $_FANWE['client_ip'];
				$weibo['url'] = $_FANWE['site_url'].FU('note/index',array('sid'=>$share_id));
                $weibo['url'] = str_replace('//','/',$weibo['url']);
				$weibo['url'] = str_replace(':/','://',$weibo['url']);
				$weibo = base64_encode(serialize($weibo));
				if(empty($share_data['type']))
					$share_data['type'] = 'default';
				
				//转发到外部微博
				$uid = $_FANWE['uid'];
				$user_binds = FS("User")->getUserBindList($uid);
				$is_open = false;
				foreach($user_binds as $class => $bind)
				{
					if($bind['sync'] && file_exists(FANWE_ROOT."login/".$class.".php"))
					{
						$check_field = "";
						if(in_array($share_data['type'],array('bar','ask')))
							$check_field = "topic";
						elseif($share_data['type'] == 'default')
							$check_field = "weibo";
						
						if($bind['sync'][$check_field] == 1)
						{
							$is_open = true;
							//开始推送
							$schedule['uid'] = $uid;
							$schedule['type'] = $class;
							$schedule['data'] = $weibo;
							$schedule['pub_time'] = TIME_UTC;
							FDB::insert('pub_schedule',$schedule,true);
						}
					}
				}
				
				if($is_open)
				{
					if(function_exists('fsockopen'))
						$fp=fsockopen($_SERVER['HTTP_HOST'],80,&$errno,&$errstr,5);
					elseif(function_exists('pfsockopen'))
						$fp=pfsockopen($_SERVER['HTTP_HOST'],80,&$errno,&$errstr,5);

					if($fp)
					{
						$request = "GET ".SITE_URL."login.php?loop=true&uid=".$uid." HTTP/1.0\r\n";
						$request .= "Host: ".$_SERVER['HTTP_HOST']."\r\n";
						$request .= "Connection: Close\r\n\r\n";
						fwrite($fp, $request);
						while(!feof($fp))
						{
							fgets($fp, 128);
							break;
						}
						fclose($fp);
					}
				}
			}
		}
		else
		{
			$result['status'] = false;
		}
		return $result;
	}

	/*根据标签获取所属分类*/
	public function getCateByTags($tags)
	{
		static $tag_cates = array();

		$tags = array_unique($tags);

		$cates = array();
		foreach($tags as $tag)
		{
			if(!empty($tag))
			{
				if(isset($tag_cates[$tag]))
				{
					foreach($tag_cates[$tag] as $data)
					{
						if(isset($cates[$data['cate_id']]))
							$cates[$data['cate_id']] += $data['weight'];
						else
							$cates[$data['cate_id']] = $data['weight'];
					}
					continue;
				}

				$is_bln = true;
				$res = FDB::query('SELECT gct.cate_id,gct.weight
					FROM '.FDB::table('goods_tags').' AS gt
					LEFT JOIN '.FDB::table('goods_category_tags')." AS gct ON gct.tag_id = gt.tag_id
					WHERE gt.tag_name = '$tag'");
				while($data = FDB::fetch($res))
				{
					if($data['weight'] < 1)
						$data['weight'] = 1;

					if(isset($cates[$data['cate_id']]))
						$cates[$data['cate_id']] += $data['weight'];
					else
						$cates[$data['cate_id']] = $data['weight'];

					$tag_cates[$tag][] = $data;
					$is_bln = false;
				}

				if($is_bln)
				{
					$like_tag = fMysqlLikeQuote($tag);
					$res = FDB::query('SELECT gct.cate_id,gct.weight
						FROM '.FDB::table('goods_tags').' AS gt
						LEFT JOIN '.FDB::table('goods_category_tags')." AS gct ON gct.tag_id = gt.tag_id
						WHERE gt.tag_name LIKE '%".$like_tag."%' OR INSTR('$tag',gt.tag_name) > 0");
					while($data = FDB::fetch($res))
					{
						if($data['weight'] < 1)
							$data['weight'] = 1;

						if(isset($cates[$data['cate_id']]))
							$cates[$data['cate_id']] += $data['weight'];
						else
							$cates[$data['cate_id']] = $data['weight'];

						$tag_cates[$tag] = $data;
					}
				}
			}
		}

		if(empty($cates))
			return array();

		$cids = array();
		foreach($cates as $cate_id => $weight)
		{
			$cids[$weight][] = $cate_id;
		}

		krsort($cids);
		return each($cids);
	}

	public function getCidsByCates($cates)
	{
		$list = array();
		$cids = array();
		foreach($cates as $cate)
		{
			foreach($cate['value'] as $cid)
			{
				if(isset($cids[$cid]))
					$cids[$cid] = $cate['key'];
				else
					$cids[$cid] += $cate['key'];
			}
		}

		foreach($cates as $cate)
		{
			$id = 0;
			$weight = 0;
			foreach($cate['value'] as $cid)
			{
				if($cids[$cid] > $weight)
					$id = $cid;
			}

			if($id > 0)
				$list[] = $id;
		}

		return array_unique($list);
	}

	public function deleteShare($share_id,$is_score = true)
	{
		$share = ShareService::getShareById($share_id);
		if(!empty($share))
		{
			$goods_count = FDB::resultFirst('SELECT COUNT(goods_id) FROM '.FDB::table('share_goods').' WHERE share_id = '.$share_id);
			$photo_count = FDB::resultFirst('SELECT COUNT(photo_id) FROM '.FDB::table('share_photo').' WHERE share_id = '.$share_id);
			$collect_count = FDB::resultFirst('SELECT COUNT(c_uid) FROM '.FDB::table('user_collect').' WHERE share_id = '.$share_id);

			$shop_list = array();
			$res = FDB::query('SELECT shop_id FROM '.FDB::table('share_goods').' WHERE share_id = '.$share_id.' GROUP BY shop_id');
			while($shop = FDB::fetch($res))
			{
				$shop_list[] = $shop['shop_id'];
			}

			FDB::delete('share','share_id = '.$share_id);
			FDB::delete('share_goods','share_id = '.$share_id);
			FDB::delete('share_photo','share_id = '.$share_id);
			FDB::delete('share_category','share_id = '.$share_id);
			FDB::delete('share_comment','share_id = '.$share_id);
			FDB::delete('share_match','share_id = '.$share_id);
			FDB::delete('share_tags','share_id = '.$share_id);
			FDB::delete('user_collect','share_id = '.$share_id);

			if(count($shop_list) > 0)
			{
				FS('Shop')->updateShopStatistic($shop_list);
			}
			
			$pattern = "/#([^\f\n\r\t\v]{1,80}?)#/";
			if(preg_match($pattern,$share['content']))
			{
				FS("Event")->deleteEvent($share_id);
			}
			
			$pattern = "/@([^\f\n\r\t\v@ ]{2,20}?)(?:\:| )/";
			if(preg_match($pattern,$share['content']))
			{
				FDB::delete('atme','share_id = '.$share_id);
			}
			
			if(defined('MANAGE_HANDLER') && MANAGE_HANDLER && $is_score)
			{
				if(!in_array($share['type'],array('fav','album_best','album_rec')))
				{
					if($share['rec_uid'] == 0 && in_array($share['share_data'],array('goods','photo','goods_photo')))
						FS("User")->updateUserScore($share['uid'],'delete_share','image',$share['content'],$share_id);
					else
						FS("User")->updateUserScore($share['uid'],'delete_share','default',$share['content'],$share_id);
				}	
			}
			
			FDB::query('UPDATE '.FDB::table('user_count').' SET
				shares = shares - 1,
				photos = photos - '.$photo_count.',
				goods = goods - '.$goods_count.',
				collects = collects - '.$collect_count.' WHERE uid = '.$share['uid']);

			$key = getDirsById($share_id);
			clearCacheDir('share/'.$key);
			
			$count = (int)FDB::resultFirst('SELECT COUNT(share_id) FROM '.FDB::table('share_rec').' WHERE share_id = '.$share_id);
			if($count == 0)
				ShareService::deleteShareImages($share_id,$share['server_code']);
		}
	}

	public function deleteShareImages($share_id,$server_code)
	{
		if(empty($server_code))
		{
			$key = getDirsById($share_id);
			clearDir(PUBLIC_ROOT.'./upload/share/'.$key,true);
		}
		else
		{
			$server = FS("Image")->getServer($server_code);
			if($server)
			{
				$args = array();
				$args['share_id'] = $share_id;
				$server = FS("Image")->getImageUrlToken($args,$server,1);
				FS("Image")->sendRequest($server,'removeshare');
			}
		}
	}

	public function deleteShareCache()
	{
		
	}

	/**
	 * 根据编号获取分享
	 * @param int $share_id 分享编号
	 * @return array
	 */
	public function getShareById($share_id)
	{
		$share_id = (int)$share_id;
		if(!$share_id)
			return false;
		
		static $list = array();
		if(!isset($list[$share_id]))
		{
			$share = FDB::fetchFirst('SELECT * FROM '.FDB::table('share').' WHERE share_id = '.$share_id);
			if($share)
				$share['url'] = FU('note/index',array('sid'=>$share_id));
			$list[$share_id] = $share;
		}
		return $list[$share_id];
	}

    /**
	 * 更新分享内容
	 * @param int $share_id 分享编号
	 * @return void
	 */
	public function updateShare($share_id,$title,$content)
	{
		if(empty($title) && empty($content))
			return;
		
		$data = array();
		if(!empty($title))
        	$data['title'] = $title;
			
		if(!empty($content))
        	$data['content'] = $content;
		
        FDB::update('share',$data,"share_id = '$share_id'");
        ShareService::updateShareMatch($share_id);
	}

	/**
	 * 获取分享详细
	 * @param int $share_id 分享编号
	 * @return array
	 */
	public function getShareDetail($share_id,$is_collect = false,$is_tag = false,$collect_count = 20)
	{
		$share = ShareService::getShareById($share_id);
		if($share)
		{
			$share['cache_data'] = fStripslashes(unserialize($share['cache_data']));
			$share['authoritys'] = ShareService::getIsEditShare($share);
			$share['time'] = getBeforeTimelag($share['create_time']);
			ShareService::shareImageFormat($share);
		}
		return $share;
	}

	/**
	 * 获取分享的动态数据
	 * @param int $share_id 分享编号
	 * @return array
	 */
	public function getShareDynamic($share_id)
	{
		$dynamic = FDB::fetchFirst('SELECT collect_count,comment_count,relay_count,click_count
				FROM '.FDB::table('share').'
				WHERE share_id = '.$share_id);
		return $dynamic;
	}

	/**
	 * 分享列表详细数据
	 * @param array $list 分享列表
	 * @param bool $is_parent 是否获取转发信息
	 * @param bool $is_collect 是否获取喜欢的会员
	 * @param bool $is_parent 是否获取分享标签
	 * @return array
	 */
	public function getShareDetailList($list,$is_parent = false,$is_collect = false,$is_tag = false,$is_comment = false,$comment_count = 10,$collect_count = 20,$is_user = false)
	{
		global $_FANWE;
		$shares = array();
		$share_ids = array();
		$rec_shares_ids = array();
		$share_users = array();
		$share_collects = array();
		$share_comments = array();
		$share_follows = array();
		
		foreach($list as $item)
		{
			$share_id = $item['share_id'];
			$share_ids[] = $share_id;
			$item['cache_data'] = fStripslashes(unserialize($item['cache_data']));
			$item['authoritys'] = ShareService::getIsEditShare($item);
			$item['time'] = getBeforeTimelag($item['create_time']);
			$item['url'] = FU('note/index',array('sid'=>$share_id));
			ShareService::shareImageFormat($item);
			$shares[$share_id] = $item;
			unset($shares[$share_id]['cache_data']);
			
			//分享会员
			if($is_user)
			{
				$shares[$share_id]['user'] = &$share_users[$item['uid']];
				if($item['rec_uid'] > 0)
					$shares[$share_id]['rec_user'] = &$share_users[$item['rec_uid']];
			}
			
			//分享评论
			if($is_comment)
			{
				$shares[$share_id]['comments'] = array();
				if(!empty($item['cache_data']['comments']))
				{
					$comment_ids = array_slice($item['cache_data']['comments'],0,$comment_count);
					foreach($comment_ids as $comment_id)
					{
						$shares[$share_id]['comments'][$comment_id] = &$share_comments[$comment_id];
					}
				}
			}
			
			//喜欢分享的会员
			if($is_collect)
			{
				$shares[$share_id]['collects'] = array();
				if(!empty($item['cache_data']['collects']))
				{
					$collect_ids = array_slice($item['cache_data']['collects'],0,$collect_count);
					foreach($collect_ids as $collect_uid)
					{
						if($is_user)
							$shares[$share_id]['collects'][$collect_uid] = &$share_users[$collect_uid];
						else
							$shares[$share_id]['collects'][$collect_uid] = $collect_uid;
					}
				}
			}

			if($is_tag)
			{
				$shares[$share_id]['is_eidt_tag'] = ShareService::getIsEditTag($item);
				$shares[$share_id]['tags'] = $item['cache_data']['tags'];
				ShareService::tagsFormat($shares[$share_id]['tags']['user']);
			}

			$shares[$share_id]['is_relay'] = false;
			$shares[$share_id]['is_parent'] = false;
			
			if($is_parent)
			{
				if($item['base_id'] > 0)
				{
					$shares[$share_id]['is_relay'] = true;
					$rec_shares_ids[$item['base_id']] = false;
					$shares[$share_id]['relay_share'] = &$rec_shares_ids[$item['base_id']];

					if($item['parent_id'] > 0 && $item['parent_id'] != $item['base_id'])
					{
						$shares[$share_id]['is_parent'] = true;
						$rec_shares_ids[$item['parent_id']] = false;
						$shares[$share_id]['parent_share'] = &$rec_shares_ids[$item['parent_id']];
					}
				}
			}
		}
		
		$rec_ids = array_keys($rec_shares_ids);
		if(count($rec_ids) > 0)
		{
			$intersects = array_intersect($share_ids,$rec_ids);
			$temp_ids = array();
			foreach($intersects as $share_id)
			{
				$rec_shares_ids[$share_id] = $shares[$share_id];
				$temp_ids[] = $share_id;
			}
			
			$diffs = array_diff($rec_ids,$temp_ids);
			if(count($diffs) > 0)
			{
				$res = FDB::query('SELECT * FROM '.FDB::table('share').' WHERE share_id IN ('.implode(',',$diffs).')');
				while($item = FDB::fetch($res))
				{
					$share_id = $item['share_id'];
					$share_ids[] = $share_id;
					$item['cache_data'] = fStripslashes(unserialize($item['cache_data']));
					$item['authoritys'] = ShareService::getIsEditShare($item);
					$item['time'] = getBeforeTimelag($item['create_time']);
					$item['url'] = FU('note/index',array('sid'=>$share_id));
					ShareService::shareImageFormat($item);
					$rec_shares_ids[$share_id] = $item;
					unset($rec_shares_ids[$share_id]['cache_data']);
					
					//分享会员
					if($is_user)
					{
						$rec_shares_ids[$share_id]['user'] = &$share_users[$item['uid']];
						if($item['rec_uid'] > 0)
							$rec_shares_ids[$share_id]['rec_user'] = &$share_users[$item['rec_uid']];
					}
					
					//分享评论
					if($is_comment)
					{
						$rec_shares_ids[$share_id]['comments'] = array();
						if(!empty($item['cache_data']['comments']))
						{
							$comment_ids = array_slice($item['cache_data']['comments'],0,$comment_count);
							foreach($comment_ids as $comment_id)
							{
								$rec_shares_ids[$share_id]['comments'][$comment_id] = &$share_comments[$comment_id];
							}
						}
					}
					
					//喜欢分享的会员
					if($is_collect)
					{
						$rec_shares_ids[$share_id]['collects'] = array();
						if(!empty($item['cache_data']['collects']))
						{
							$collect_ids = array_slice($item['cache_data']['collects'],0,$collect_count);
							foreach($collect_ids as $collect_uid)
							{
								if($is_user)
									$rec_shares_ids[$share_id]['collects'][$collect_uid] = &$share_users[$collect_uid];
								else
									$rec_shares_ids[$share_id]['collects'][$collect_uid] = $collect_uid;
							}
						}
					}
		
					if($is_tag)
					{
						$rec_shares_ids[$share_id]['is_eidt_tag'] = ShareService::getIsEditTag($item);
						$rec_shares_ids[$share_id]['tags'] = $item['cache_data']['tags'];
						ShareService::tagsFormat($rec_shares_ids[$share_id]['tags']['user']);
					}
				}
			}
		}
		
		$comment_ids = array_keys($share_comments);
		if(count($comment_ids) > 0)
		{
			$res = FDB::query("SELECT * FROM ".FDB::table('share_comment').' WHERE comment_id IN ('.implode(',',$comment_ids).')');
			while($item = FDB::fetch($res))
			{
				$item['time'] = getBeforeTimelag($item['create_time']);
				$share_comments[$item['comment_id']] = $item;
				if($is_user)
					$share_comments[$item['comment_id']]['user'] = &$share_users[$item['uid']];
			}
		}
		
		if($is_user)
			FS('User')->usersFormat($share_users);
		
		return $shares;
	}
	
	/**
	 * 获取分享的图片
	 */
	public function getShareImage($share_id,$data_type)
	{
		$share_id = (int)$share_id;
		$list = array();
		switch($data_type)
		{
			case 'goods':
				$sql = 'SELECT share_id,goods_id AS id,img,\'g\' AS type,name,url,price,taoke_url,server_code,img_width,img_height 
					FROM '.FDB::table('share_goods').'
					WHERE share_id = '.$share_id.' ORDER BY sort ASC';
				$res = FDB::query($sql);
				while($data = FDB::fetch($res))
				{
					$data['name'] = addslashes($data['name']);
					$data['url'] = addslashes($data['url']);
					$pkey = $data['type'].$data['id'];
					$list['all'][$pkey] = $data;
				}
			break;
			case 'photo':
				$sql = 'SELECT share_id,photo_id AS id,img,type AS ptype,\'m\' AS type,\'\' AS name,\'\' AS url,0 AS price,server_code,img_width,img_height 
					FROM '.FDB::table('share_photo').'
					WHERE share_id = '.$share_id.' ORDER BY sort ASC';
				$res = FDB::query($sql);
				while($data = FDB::fetch($res))
				{
					$pkey = $data['type'].$data['id'];
					$ptype = $data['ptype'];
					unset($data['ptype']);
					$list['all'][$pkey] = $data;
					if($ptype != 'default')
					{
						$list[$ptype][] = $pkey;
					}
				}
			break;
			case 'goods_photo':
				$sql = '(SELECT share_id,goods_id AS id,img,\'default\' AS ptype,\'g\' AS type,name,url,price,taoke_url,sort,server_code,img_width,img_height 
					FROM '.FDB::table('share_goods').'
					WHERE share_id = '.$share_id.')
					UNION
					(SELECT share_id,photo_id AS id,img,type AS ptype,\'m\' AS type,\'\' AS name,\'\' AS url,0 AS price,\'\' AS taoke_url,sort,server_code,img_width,img_height 
					FROM '.FDB::table('share_photo').'
					WHERE share_id = '.$share_id.')
					ORDER BY sort ASC';

				$res = FDB::query($sql);
				while($data = FDB::fetch($res))
				{
					$data['name'] = addslashes($data['name']);
					$data['url'] = addslashes($data['url']);
					$pkey = $data['type'].$data['id'];
					$ptype = $data['ptype'];
					unset($data['ptype']);
					unset($data['sort']);
					$list['all'][$pkey] = $data;
					if($ptype != 'default')
					{
						$list[$ptype][] = $pkey;
					}
				}
			break;
		}
		return $list;
	}

	/**
	 * 获取分享的图片集合
	 */
	public function getShareImages(&$share_datas)
	{
		foreach($share_datas as $share_data => $share_ids)
		{
			if($share_data == 'default' || count($share_ids) == 0)
				continue;
			
			$share_ids = array_keys($share_ids);
			$list = array();
			switch($share_data)
			{
				case 'goods':
					$sql = 'SELECT share_id,goods_id AS id,img,\'g\' AS type,name,url,price,taoke_url,server_code,img_width,img_height
						FROM '.FDB::table('share_goods').'
						WHERE share_id IN ('.implode(',',$share_ids).') ORDER BY sort ASC';
					$res = FDB::query($sql);
					while($data = FDB::fetch($res))
					{
						$list[$data['share_id']]['all'][] = $data;
					}
				break;
				case 'photo':
					$sql = 'SELECT share_id,photo_id AS id,img,type AS ptype,\'m\' AS type,\'\' AS name,\'\' AS url,0 AS price,server_code,img_width,img_height
						FROM '.FDB::table('share_photo').'
						WHERE share_id IN ('.implode(',',$share_ids).') ORDER BY sort ASC';
					$res = FDB::query($sql);
					while($data = FDB::fetch($res))
					{
						$pkey = $data['type'].$data['id'];
						$ptype = $data['ptype'];
						unset($data['ptype']);
						$list[$data['share_id']]['all'][$pkey] = $data;
						if($ptype != 'default')
						{
							$list[$data['share_id']][$ptype][] = $pkey;
						}
					}
				break;
				case 'goods_photo':
					$sql = '(SELECT share_id,goods_id AS id,img,\'default\' AS ptype,\'g\' AS type,name,url,price,taoke_url,sort,server_code,img_width,img_height
						FROM '.FDB::table('share_goods').'
						WHERE share_id IN ('.implode(',',$share_ids).'))
						UNION
						(SELECT share_id,photo_id AS id,img,type AS ptype,\'m\' AS type,\'\' AS name,\'\' AS url,0 AS price,\'\' AS taoke_url,sort,server_code,img_width,img_height
						FROM '.FDB::table('share_photo').'
						WHERE share_id IN ('.implode(',',$share_ids).'))
						ORDER BY sort ASC';

					$res = FDB::query($sql);
					while($data = FDB::fetch($res))
					{
						$pkey = $data['type'].$data['id'];
						$ptype = $data['ptype'];
						unset($data['ptype']);
						unset($data['sort']);
						$list[$data['share_id']]['all'][$pkey] = $data;
						if($ptype != 'default')
						{
							$list[$data['share_id']][$ptype][] = $pkey;
						}
					}
				break;
			}
			
			foreach($list as $share_id => $item)
			{
				foreach($item['all'] as $ik => $img)
				{
					if($img['type'] == 'g')
					{
						$img['goods_url'] = $img['url'];
						if(empty($img['taoke_url']))
							$img['to_url'] = FU('tgo',array('url'=>$img['url']));
						else
							$img['to_url'] = FU('tgo',array('url'=>$img['taoke_url']));
	
						$img['price_format'] = priceFormat($img['price']);
					}
					
					$img['url'] = FU('note/'.$img['type'],array('sid'=>$img['share_id'],'id'=>$img['id']));
					$item['all'][$ik] = $img;
				}
				$share_datas[$share_data][$share_id] = $item;
			}
		}
	}
	
	public function shareImageFormat(&$share,$pic_num = 0)
	{		
		$images = $share['cache_data']['imgs'];
		foreach($images['all'] as $ik => $img)
		{
			if($img['type'] == 'g')
			{
				$img['goods_url'] = $img['url'];
				if(empty($img['taoke_url']))
					$img['to_url'] = FU('tgo',array('url'=>$img['url']));
				else
					$img['to_url'] = FU('tgo',array('url'=>$img['taoke_url']));

				$img['price_format'] = priceFormat($img['price']);
			}
			
			$img['url'] = FU('note/'.$img['type'],array('sid'=>$img['share_id'],'id'=>$img['id']));
			$images['all'][$ik] = $img;
			$share['imgs'][] = $img;
		}
		
		foreach($images['dapei'] as $ik)
		{
			$share['dapei_imgs'][] = $images['all'][$ik];
		}
		
		foreach($images['look'] as $ik)
		{
			$share['look_imgs'][] = $images['all'][$ik];
		}
		
		if($pic_num > 0 && count($share['imgs']) > $pic_num)
			$share['imgs'] = array_slice($share['imgs'],0,$pic_num);
		unset($images);
	}

	/**
	 * 获取会员的上一个和下一个有图片分享
	 * @param array $_POST 提交的数据
	 * @return array(
			'prev'=>上一个分享,
			'next'=>下一个分享,
		)
	 */
	public function getPrevNextShares($uid,$share_id)
	{
		$arr = array('prev'=>0,'next'=>0);
		$share_ids = FS('User')->getShareIds($uid);
		$key = array_search($share_id,$share_ids);
		if($key !== false)
		{
			$count = count($share_ids);
			if($count > 1)
			{
				if($key == 0)
					$arr['prev'] = $share_ids[1];
				elseif($key == $count - 1)
					$arr['next'] = $share_ids[$key - 1];
				else
				{
					$arr['next'] = $share_ids[$key - 1];
					if(isset($share_ids[$key + 1]))
						$arr['prev'] = $share_ids[$key + 1];
				}
			}
		}
		return $arr;
	}
	
	public function updateShareCache($share_id,$type = 'all')
	{
		$share_id = (int)$share_id;
		if(!$share_id)
			return;
		
		$share = FDB::fetchFirst('SELECT cache_data,share_data FROM '.FDB::table('share').' WHERE share_id = '.$share_id);
		if(!$share)
			return;
			
		$cache_data = fStripslashes(unserialize($share['cache_data']));
		switch($type)
		{
			case 'tags':
				$cache_data['tags'] = ShareService::getShareTags($share_id,true);
			break;
			
			case 'collects':
				$cache_data['collects'] = ShareService::getShareCollectUser($share_id,50);
			break;
			
			case 'comments':
				$cache_data['comments'] = ShareService::getNewCommentIdsByShare($share_id,10);
			break;
			
			case 'imgs':
				$cache_data['imgs'] = ShareService::getShareImage($share_id,$share['share_data']);
			break;
			
			case 'all':
				$cache_data['tags'] = ShareService::getShareTags($share_id,true);
				$cache_data['collects'] = ShareService::getShareCollectUser($share_id,50);
				$cache_data['comments'] = ShareService::getNewCommentIdsByShare($share_id,10);
				$cache_data['imgs'] = ShareService::getShareImage($share_id,$share['share_data']);
			break;
		}
		unset($share['share_data']);
		$share['cache_data'] = addslashes(serialize($cache_data));
		FDB::update("share",$share,'share_id = '.$share_id);
	}

    public function updateShareMatch($share_id)
    {
        $share = ShareService::getShareById($share_id);
        if(!in_array($share['share_data'],array('goods','photo','goods_photo')))
            return;
		
		$share['cache_data'] = fStripslashes(unserialize($share['cache_data']));

		$content_match = clearExpress($share['content']);
        $content_match .= $share['title'];

        if(isset($share['cache_data']['tags']['user']))
        {
            foreach($share['cache_data']['tags']['user'] as $tag)
            {
				$content_match.=$tag['tag_name'];
            }
        }

        if(isset($share['cache_data']['tags']['admin']))
        {
            foreach($share['cache_data']['tags']['admin'] as $tag)
            {
				$content_match.=$tag['tag_name'];
            }
        }

        if(isset($share['cache_data']['imgs']['all']))
        {
            foreach($share['cache_data']['imgs']['all'] as $img)
            {
                if(!empty($img['name']))
				{
					$content_match.=$img['name'];
				}
            }
        }
		
        //保存匹配查询
        $share_match = array();
        $share_match['share_id'] = $share_id;
        $share_match['content_match'] = segmentToUnicode(clearSymbol($content_match));
        FDB::insert("share_match",$share_match,false,true);
    }

	/**
	 * 获取是否可编辑分享
	 * @param int $share 分享
	 * @return array
	 */
	public function getIsEditShare(&$share)
	{
		static $edits = array();
		if(!isset($edits[$share['share_id']]))
		{
			global $_FANWE;
			$type = array('ask','bar');
			$is_edit = 0;
			$post = array('ask_post','bar_post');
			if(in_array($share['type'],$post))
			{
				if($share['uid'] == $_FANWE['uid'])
					$is_edit = 1;

				if($share['type'] == 'ask_post')
					$thread = FS('ask')->getTopicById($share['rec_id']);
				else
					$thread = FS('Topic')->getTopicById($share['rec_id']);

				if($thread['uid'] == $_FANWE['uid'])
					$is_edit = 2;
			}
			else
			{
				if(!in_array($share['type'],$type) && $share['uid'] == $_FANWE['uid'])
					$is_edit = 1;
			}

			$edits[$share['share_id']] = $is_edit;
		}

		return $edits[$share['share_id']];
	}
	/*===========分享列表、详细 END  ==============*/

	/*===========分享标签 BEGIN  ==============*/
	/**
	 * 获取是否可编辑分享标签
	 * @param int $share 分享
	 * @return array
	 */
	public function getIsEditTag(&$share)
	{
		global $_FANWE;
		$_img_data = array('goods','photo','goods_photo');
		$is_edit_tag = false;
		if(in_array($share['share_data'],$_img_data) && $share['uid'] == $_FANWE['uid'])
			$is_edit_tag = true;
		return $is_edit_tag;
	}

	/**
	 * 获取分享标签
	 * @param int $share_id 分享编号
	 * @return array
	 */
	public function getShareTags($share_id,$is_update = false)
	{
		$share_id = (int)$share_id;
		if(!$share_id)
			return array();
		
		static $list = array();
		if(!isset($list[$share_id]) || $is_update)
		{
			$res = FDB::query('SELECT tag_name,is_admin
				FROM '.FDB::table('share_tags').'
				WHERE share_id = '.$share_id);
			while($data = FDB::fetch($res))
			{
				$data['tag_name'] = addslashes($data['tag_name']);
				if($data['is_admin'] == 0)
					$list[$share_id]['user'][] = $data;
				else
					$list[$share_id]['admin'][] = $data;
			}
		}
		
		return $list[$share_id];
	}
	
	public function tagsFormat(&$tags)
	{
		foreach($tags as $tk => $tag)
		{
			$tags[$tk]['url'] = FU('book/shopping',array('tag'=>urlencode($tag['tag_name'])));
		}
	}

	/**
	 * 更新分享标签缓存
	 * @param int $share_id 分享编号
	 * @param array $tags = array(
	 		'user'=>会员设置标签,
			'admin'=>管理员设置标签,(如果不存在admin键名，则不删除会员设置标签)
	 	);
	 * @return array
	 */
	public function updateShareTags($share_id,$tags)
	{
		global $_FANWE;
		//更新分享的会员标签
		FDB::delete('share_tags','share_id = '.$share_id.' AND is_admin = 0');
		if(isset($tags['user']))
		{
			$tags['user'] = str_replace('　',' ',$tags['user']);
			$tags['user'] = explode(' ',htmlspecialchars(trim($tags['user'])));
            $tags['user'] = array_unique($tags['user']);
            $tags['user'] = array_slice($tags['user'],0,$_FANWE['setting']['share_tag_count']);

			$share_tags = array();
			foreach($tags['user'] as $tag)
			{
				if(trim($tag) != '' && !in_array($tag,$share_tags))
				{
					array_push($share_tags,$tag);

					//为已存在的tags更新统计
					FDB::query('UPDATE '.FDB::table('goods_tags').'
						SET count = count + 1
						WHERE tag_name = \''.$tag.'\'');

					//数量大于100时为热门标签
					FDB::query('UPDATE '.FDB::table('goods_tags').'
						SET is_hot = 1
						WHERE tag_name = \''.$tag.'\' AND count >= 100');

					$tag_data = array();
					$tag_data['share_id'] = $share_id;
					$tag_data['tag_name'] = $tag;
					FDB::insert('share_tags',$tag_data);
				}
			}
			ShareService::updateShareCache($share_id,'tags');
		}

		//更新分享的管理员标签
		if(isset($tags['admin']))
		{
			FDB::delete('share_tags','share_id = '.$share_id.' AND is_admin = 1');

			$tags['admin'] = str_replace('　',' ',$tags['admin']);
			$tags['admin'] = explode(' ',htmlspecialchars(trim($tags['admin'])));
            $tags['admin'] = array_unique($tags['admin']);

			$share_tags = array();
			foreach($tags['admin'] as $tag)
			{
				if(trim($tag) != '' && !in_array($tag,$share_tags))
				{
					array_push($share_tags,$tag);

					//为已存在的tags更新统计
					FDB::query('UPDATE '.FDB::table('goods_tags').'
						SET count = count + 1
						WHERE tag_name = \''.$tag.'\'');

					//数量大于100时为热门标签
					FDB::query('UPDATE '.FDB::table('goods_tags').'
						SET is_hot = 1
						WHERE tag_name = \''.$tag.'\' AND count >= 100');

					$tag_data = array();
					$tag_data['share_id'] = $share_id;
					$tag_data['tag_name'] = $tag;
					$tag_data['is_admin'] = 1;
					FDB::insert('share_tags',$tag_data);
				}
			}
		}
        ShareService::updateShareMatch($share_id);
		
	}
	/*===========分享标签 END  ==============*/

	/*===========分享转发 BEGIN  ==============*/
	/**
	 * 转发分享
	 * @param array $_POST 提交的数据
	 * @return array(
			'share_id'=>分享编号,
			'pc_id'=>评论编号(如果勾选评论给转发分享),
			'bc_id'=>原文评论编号(如果勾选评论给原文分享),
		)
	 */
	public function saveRelay($_POST)
	{
		global $_FANWE;
		$share_id = intval($_POST['share_id']);
		$share = ShareService::getShareById($share_id);
		if(empty($share))
			return false;

		$data = array();
		$data['share']['uid'] = $_FANWE['uid'];
		$data['share']['parent_id'] = $share_id;
		$content = htmlspecialchars(trim($_POST['content']));
		$data['share']['content'] = $content;
		$type = 'default';
		$base_id = $share['base_id'];
		if($base_id > 0)
		{
			$base = ShareService::getShareById($share['base_id']);
			if(!empty($base))
				$base_id = $base['share_id'];
			else
				$base_id = 0;
		}

		$rec_id = $share['rec_id'];

		if($share['type'] == 'ask' || $share['type'] == 'ask_post')
			$type = 'ask_post';
		elseif($share['type'] == 'bar' || $share['type'] == 'bar_post')
			$type = 'bar_post';

		$data['share']['rec_id'] = $share['rec_id'];
		$data['share']['title'] = addslashes($share['title']);
		$data['share']['base_id'] = $base_id > 0 ? $base_id : $share_id;
		$data['share']['type'] = $type;

		$relay_share = ShareService::save($data);
		if(!$relay_share['status'])
			return false;

		FDB::query('UPDATE '.FDB::table('share').'
			SET relay_count = relay_count + 1
			WHERE share_id = '.$share_id);

		if($base_id > 0 && $share_id != $base_id)
		{
			FDB::query('UPDATE '.FDB::table('share').'
				SET relay_count = relay_count + 1
				WHERE share_id = '.$base_id);
		}

		$is_no_post = isset($_POST['is_no_post']) ? intval($_POST['is_no_post']) : 0;
		$share_id = $relay_share['share_id'];
		if($rec_id > 0 && $is_no_post == 0)
		{
			if($type == 'bar_post')
				FS('Topic')->saveTopicPost($rec_id,$content,$share_id);
			elseif($type == 'ask_post')
				FS('Ask')->saveTopicPost($rec_id,$content,$share_id);
		}

		$is_comment_parent = isset($_POST['is_comment_parent']) ? intval($_POST['is_comment_parent']) : 0;
		$is_comment_base = isset($_POST['is_comment_base']) ? intval($_POST['is_comment_base']) : 0;

		//评论给分享
		$parent_comment_id = 0;
		if($is_comment_parent == 1)
		{
			$data = array();
			$data['content'] = 	$_POST['content'];
			$data['share_id'] = $share['share_id'];
			$parent_comment_id = ShareService::saveComment($data);
		}

		//评论给原创分享
		$base_comment_id = 0;
		if($is_comment_base == 1 && $base_id > 0)
		{
			$data = array();
			$data['content'] = 	$_POST['content'];
			$data['share_id'] = $base_id;
			$base_comment_id = ShareService::saveComment($data);
		}

		return array(
			'share_id'=>$share_id,
			'pc_id'=>$parent_comment_id,
			'bc_id'=>$base_comment_id,
		);
	}

	/*===========分享转发 END  ==============*/

	/*===========喜欢收藏分享 BEGIN  ==============*/
	/**
	 * 保存喜欢分享
	 * @param int $share 分享
	 * @return void
	 */
	public function saveFav($share)
	{
		if($share['type'] == 'fav')
			return false;

		global $_FANWE;
		ShareService::setShareCollectUser($share['share_id'],$share['uid']);

		$base_id = $share['base_id'];
		if($base_id > 0)
		{
			$base = ShareService::getShareById($share['base_id']);
			if(!empty($base))
			{
				ShareService::setShareCollectUser($base['share_id'],$base['uid']);
				$base_id = $base['share_id'];
			}
			else
				$base_id = 0;
		}

		$share_user = FS('User')->getUserCache($share['uid']);
		$data = array();
		$data['share']['uid'] = $_FANWE['uid'];
		$data['share']['rec_id'] = $share['rec_id'];
		$data['share']['parent_id'] = $share['share_id'];
		$data['share']['content'] = lang('share','fav_share').'//@'.$share_user['user_name'].':'.$share['content'];
		$data['share']['type'] = "fav";
		$data['share']['base_id'] = $base_id > 0 ? $base_id : $share['share_id'];
		
		//添加关注消息提示
		FS("User")->setUserTips($share['uid'],2);
		ShareService::save($data);
	}

	/**
	 * 获取喜欢这个分享的会员
	 * @param int $share_id 分享编号
	 * @param int $num 获取数量
	 * @return array
	 */
	public function getShareCollectUser($share_id,$num = 12)
	{
		$num = (int)$num;
		if($num == 0)
			$num = 1000;
		
		$uids = array();
		$res = FDB::query('SELECT c_uid FROM '.FDB::table('user_collect').'
			WHERE share_id = '.$share_id.'
			ORDER BY create_time DESC LIMIT 0,'.$num);

		while($data = FDB::fetch($res))
		{
			$uids[$data['c_uid']] = $data['c_uid'];
		}
		
		return $uids;
	}

	/**
	 * 添加喜欢这个分享的会员
	 * @param int $share_id 分享编号
	 * @param int $uid 会员会员数量
	 * @return array
	 */
	public function setShareCollectUser($share_id,$uid)
	{
		$share_id = (int)$share_id;
		$uid = (int)$uid;
		
		if(!$share_id || !$uid)
			return false;
			
		global $_FANWE;
		
		$share = ShareService::getShareById($share_id);
		if(empty($share))
			return false;
		
		$c_uid = $_FANWE['uid'];
		$data = array();
		$data['uid'] = $uid;
		$data['c_uid'] = $c_uid;
		$data['share_id'] = $share_id;
		$data['create_time'] = TIME_UTC;
		FDB::insert('user_collect',$data);
		
		//为专辑添加喜欢
		if($share['type'] == 'album_item')
		{
			$album_share = (int)FDB::resultFirst('SELECT share_id FROM '.FDB::table('album').' WHERE id = '.$share['rec_id']);
			FDB::query('UPDATE '.FDB::table('share').' SET collect_count = collect_count + 1 WHERE share_id = '.$album_share);
			FDB::query('UPDATE '.FDB::table('album').' SET collect_count = collect_count + 1 WHERE id = '.$share['rec_id']);
		}
		
		if($share['type'] == 'album')
		{
			FDB::query('UPDATE '.FDB::table('album').' SET collect_count = collect_count + 1 WHERE share_id = '.$share_id);
		}

		//分享被喜欢数加1
		FDB::query('UPDATE '.FDB::table('share').'
			SET collect_count = collect_count + 1
			WHERE share_id = '.$share_id);

		//分享会员被喜欢数加1
		FDB::query('UPDATE '.FDB::table('user_count').'
			SET collects = collects + 1
			WHERE uid = '.$uid);
		
		FS('Medal')->runAuto($uid,'collects');
		ShareService::updateShareCache($share_id,'collects');
	}

	/**
	 * 获取会员是否已喜欢这个分享
	 * @param int $share_id 分享编号
	 * @return array
	 */
	public function getIsCollectByUid($share_id,$uid)
	{
		$share_id = (int)$share_id;
		$uid = (int)$uid;
		
		if(!$share_id || !$uid)
			return false;
		
		$count = FDB::resultFirst('SELECT COUNT(*) FROM '.FDB::table('user_collect').'
				WHERE share_id = '.$share_id.' AND c_uid = '.$uid);
		
		if((int)$count == 0)
			return false;
		else
			return true;
	}

	public function deleteShareCollectUser($share_id,$uid)
	{
		$share_id = (int)$share_id;
		$uid = (int)$uid;
		
		if(!$share_id || !$uid)
			return false;
			
		$share = ShareService::getShareById($share_id);
		if(empty($share))
			return false;

		FDB::query('DELETE FROM '.FDB::table('user_collect').'
			WHERE c_uid = '.$uid.' AND share_id = '.$share_id);
			
		//为专辑添加喜欢
		if($share['type'] == 'album_item')
		{
			$album_share = (int)FDB::resultFirst('SELECT share_id FROM '.FDB::table('album').' WHERE id = '.$share['rec_id']);
			FDB::query('UPDATE '.FDB::table('share').' SET collect_count = collect_count - 1 WHERE share_id = '.$album_share);
			FDB::query('UPDATE '.FDB::table('album').' SET collect_count = collect_count - 1 WHERE id = '.$share['rec_id']);
		}
		
		if($share['type'] == 'album')
		{
			FDB::query('UPDATE '.FDB::table('album').' SET collect_count = collect_count - 1 WHERE share_id = '.$share_id);
		}

		//分享被喜欢数减1
		FDB::query('UPDATE '.FDB::table('share').'
			SET collect_count = collect_count - 1
			WHERE share_id = '.$share_id.' and collect_count >=1 ');

		//分享会员被喜欢数减1
		FDB::query('UPDATE '.FDB::table('user_count').'
			SET collects = collects - 1
			WHERE uid = '.$share['uid'].' and collects >=1 ');
		
		FS('Medal')->runAuto($share['uid'],'collects');
		ShareService::updateShareCache($share_id,'collects');
		return true;
	}
	/*===========喜欢收藏分享 END  ==============*/

	/*===========分享评论 BEGIN  ==============*/
	/**
	 * 获取是否可删除评论
	 * @param int $share 分享
	 * @return array
	 */
	public function getIsRemoveComment(&$share)
	{
		global $_FANWE;
		$is_bln = false;
		if($share['uid'] == $_FANWE['uid'])
			$is_bln = true;
		return $is_bln;
	}

	/**
	 * 保存分享的评论
	 * @param array $_POST 提交的数据
	 * @return int 评论编号
	 */
	public function saveComment($_POST)
	{
		global $_FANWE;
		$share_id = intval($_POST['share_id']);
		$data = array();
		$data['content'] = 	htmlspecialchars(trim($_POST['content']));
		$data['uid'] = $_FANWE['uid'];
		$data['parent_id'] = intval($_POST['parent_id']);
		$data['share_id'] = $share_id;
		$data['create_time'] = TIME_UTC;
		$comment_id = FDB::insert('share_comment',$data,true);

		$is_relay = isset($_POST['is_relay']) ? intval($_POST['is_relay']) : 0;
		//转发分享
		if($is_relay == 1)
		{
			$share = ShareService::getShareById($share_id);
			if($share['base_id'] > 0)
			{
				$share_user = FS('User')->getUserCache($share['uid']);
				$_POST['content'] = trim($_POST['content']).'//@'.$share_user['user_name'].':'.$share['content'];
			}
			//添加评论消息提示
			$result = FDB::query("INSERT INTO ".FDB::table('user_notice')."(uid, type, num, create_time) VALUES('$share[uid]',3,1,'".TIME_UTC."')", 'SILENT');
			if(!$result)
				FDB::query("UPDATE ".FDB::table('user_notice')." SET num = num + 1, create_time='".TIME_UTC."' WHERE uid='$share[uid]' AND type=3");
			
			ShareService::saveRelay($_POST);
		}

		//分享评论数量加1
		FDB::query('UPDATE '.FDB::table('share').'
			SET comment_count = comment_count + 1
			WHERE share_id = '.$share_id);

		//清除分享评论列表缓存
		ShareService::updateShareCache($share_id,'comments');
		return $comment_id;
	}

	/**
	 * 获取评论
	 * @param int $comment_id
	 * @return array
	 */
	public function getShareComment($comment_id)
	{
		return FDB::fetchFirst('SELECT *
			FROM '.FDB::table("share_comment").'
			WHERE comment_id = '.$comment_id);
	}

	/**
	 * 删除分享评论
	 * @param int $comment_id 评论编号
	 * @return void
	 */
	public function deleteShareComment($comment_id)
	{
		$comment = ShareService::getShareComment($comment_id);
		if(empty($comment))
			return;

		FDB::delete('share_comment','comment_id = '.$comment_id);
		$share_id = $comment['share_id'];
		//分享评论数量减1
		FDB::query('UPDATE '.FDB::table('share').'
			SET comment_count = comment_count - 1
			WHERE share_id = '.$share_id.' and comment_count >=1 ');

		//清除分享评论列表缓存
		ShareService::updateShareCache($share_id,'comments');
	}
	
	public function getShareComments($share_id,$count = 10)
	{
		return ShareService::getShareCommentList($share_id,'0,'.(int)$count);
	}

	/**
	 * 获取分享的最新评论列表
	 * @param int $share_id 分享编号
	 * @param int $count 数量
	 * @return array
	 */
	public function getNewCommentIdsByShare($share_id,$count = 10)
	{
		$list = array();
		$res = FDB::query('SELECT comment_id 
			FROM '.FDB::table("share_comment").'
			WHERE share_id = '.$share_id.'
			ORDER BY comment_id DESC LIMIT 0,'.$count);
		while($data = FDB::fetch($res))
		{
			$list[] = $data['comment_id'];
		}
		return $list;
	}
	
	public function commentsFormat(&$comments)
	{
		if($comments)
		{
			$comment_uids = array();
			foreach($comments as $key => $comment)
			{
				$comment['user'] = &$comment_uids[$comment['uid']];
				$comment['time'] = getBeforeTimelag($comment['create_time']);
				$comments[$key] = $comment;
			}
			FS('User')->usersFormat($comment_uids);
		}
	}

	/**
	 * 获取分享的分页评论列表
	 * @param int $share_id 分享编号
	 * @param int $count 分页
	 * @return array
	 */
	public function getShareCommentList($share_id,$limit = '0,10')
	{
		$comments = FDB::fetchAll('SELECT *
			FROM '.FDB::table("share_comment").'
			WHERE share_id = '.$share_id.'
			ORDER BY comment_id DESC LIMIT '.$limit);
		
		if($comments)
		{
			ShareService::commentsFormat($comments);
			return $comments;
		}
		else
			return array();
	}
	/*===========分享评论 END  ==============*/
	
	/**
	 * 获取喜欢这个分享的会员还喜欢的分享（有图片或商品的分享）
	 * @param int $share_id 分享编号
	 * @param int $num 获取数量
	 * @return array
	 */
	public function getCollectShareByShare($share_id,$num = 20)
	{
		$list = array();
		$share_id = (int)$share_id;
		if(!$share_id)
			return $list;
			
		$uids = ShareService::getShareCollectUser($share_id,0);

		if(count($uids) > 0)
		{
			$share_ids = array();
			$res = FDB::query('SELECT GROUP_CONCAT(DISTINCT s.share_id
					ORDER BY s.share_id DESC SEPARATOR \',\') AS share_ids,s.uid
				FROM '.FDB::table('user_collect').' AS uc
				INNER JOIN '.FDB::table('share').' AS s ON s.share_id = uc.share_id
				WHERE uc.c_uid IN ('.implode(',',$uids).')
					AND s.share_id <> '.$share_id.'
					AND s.share_data IN (\'goods\',\'photo\',\'goods_photo\')
				GROUP BY s.uid LIMIT 0,'.$num);
			while($data = FDB::fetch($res))
			{
				$share_ids = explode(',',$data['share_ids']);
				$share_ids[] = current($share_ids);
			}
		}
		
		if(count($share_ids) > 0)
		{
			$list = FDB::fetchAll('SELECT * FROM '.FDB::table('share').' 
				WHERE share_id IN ('.implode(',',$share_ids).') LIMIT 0,'.$num);
			$list = ShareService::getShareDetailList($list);
		}
		return $list;
	}

	/**
	 * 获取会员喜欢的分享（有图片或商品的分享）
	 * @param int $uid 会员编号
	 * @param int $num 获取数量
	 * @return array
	 */
	public function getCollectShareByUser($uid,$num = 10)
	{
		$list = array();
		$uid = (int)$uid;
		if(!$uid)
			return $list;
			
		$share_ids = array();
		$res = FDB::query('SELECT GROUP_CONCAT(DISTINCT s.share_id
				ORDER BY s.share_id DESC SEPARATOR \',\') AS share_ids,s.uid
			FROM '.FDB::table('user_collect').' AS uc 
			INNER JOIN '.FDB::table('share').' AS s ON s.share_id = uc.share_id 
				AND s.share_data IN (\'goods\',\'photo\',\'goods_photo\') 
			WHERE uc.c_uid = '.$uid.' 
			GROUP BY s.uid
			LIMIT 0,'.$num);
		while($data = FDB::fetch($res))
		{
			$share_id = explode(',',$data['share_ids']);
			$share_ids[] = current($share_id);
		}
		
		if(count($share_ids) > 0)
		{
			$list = FDB::fetchAll('SELECT * FROM '.FDB::table('share').' 
				WHERE share_id IN ('.implode(',',$share_ids).')');
			$list = ShareService::getShareDetailList($list);
		}
		return $list;
	}

	/**
	 * 获取会员最被喜欢的宝贝分享
	 * @param int $uid 会员编号
	 * @param int $num 获取数量
	 * @return array
	 */
	public function getBestCollectGoodsShareByUser($uid,$num = 9)
	{
		$list = array();
		$res = FDB::query('SELECT s.share_id,sg.img,sg.goods_id 
			FROM '.FDB::table('share').' AS s
			INNER JOIN '.FDB::table('share_goods').' AS sg ON sg.share_id = s.share_id
			WHERE s.share_data IN (\'goods\',\'goods_photo\') AND s.uid ='.$uid.' 
			ORDER BY s.collect_count DESC LIMIT 0,'.$num);
		while($data = FDB::fetch($res))
		{
			$data['url'] = FU('note/g',array('sid'=>$data['share_id'],'id'=>$data['goods_id']));
			$list[] = $data;
		}

		return $list;
	}

	/**
	 * 获取会员喜欢的宝贝分享
	 * @param int $uid 会员编号
	 * @param int $num 获取数量
	 * @return array
	 */
	public function getUserFavGoodsShare($uid,$num = 9)
	{
		$list = array();
		$uid = (int)$uid;
		if(!$uid)
			return $list;
		
		$res = FDB::query('SELECT sg.share_id,sg.img,sg.goods_id 
			FROM '.FDB::table('user_collect').' AS uc 
			INNER JOIN '.FDB::table('share_goods').' AS sg ON sg.share_id = uc.share_id 
			WHERE uc.c_uid = '.$uid.' GROUP BY sg.share_id ORDER BY sg.share_id DESC LIMIT 0,'.$num);
		while($data = FDB::fetch($res))
		{
			$data['url'] = FU('note/g',array('sid'=>$data['share_id'],'id'=>$data['goods_id']));
			$list[] = $data;
		}

		return $list;
	}

	/**
	 * 获取会员最被喜欢的照片分享
	 * @param int $uid 会员编号
	 * @param int $num 获取数量
	 * @return array
	 */
	public function getBestCollectPhotoShareByUser($uid,$num = 9)
	{
		$list = array();
		$uid = (int)$uid;
		if(!$uid)
			return $list;
			
		$res = FDB::query('SELECT s.share_id,sp.img,sp.photo_id 
			FROM '.FDB::table('share').' AS s 
			INNER JOIN '.FDB::table('share_photo').' AS sp ON sp.share_id = s.share_id 
			WHERE s.share_data IN (\'photo\',\'goods_photo\') AND s.uid ='.$uid.' 
			ORDER BY s.collect_count DESC LIMIT 0,'.$num);
		while($data = FDB::fetch($res))
		{
			$data['url'] = FU('note/m',array('sid'=>$data['share_id'],'id'=>$data['photo_id']));
			$list[] = $data;
		}

		return $list;
	}

	/**
	 * 获取会员喜欢的照片分享
	 * @param int $uid 会员编号
	 * @param int $num 获取数量
	 * @return array
	 */
	public function getUserFavPhotoShare($uid,$num = 9)
	{
		$list = array();
		$uid = (int)$uid;
		if(!$uid)
			return $list;
		
		$res = FDB::query('SELECT sp.share_id,sp.img,sp.photo_id 
			FROM '.FDB::table('user_collect').' AS uc 
			INNER JOIN '.FDB::table('share_photo').' AS sp ON sp.share_id = uc.share_id 
			WHERE uc.c_uid = '.$uid.' GROUP BY sp.share_id ORDER BY sp.share_id DESC LIMIT 0,'.$num);
		while($data = FDB::fetch($res))
		{
			$data['url'] = FU('note/m',array('sid'=>$data['share_id'],'id'=>$data['photo_id']));
			$list[] = $data;
		}

		return $list;
	}

	/**
	 * 获取当前的最新商品\图片分享
	 * @param int $num 获取数量
	 * @param int $pic_num 获取图片数量
	 * @return int
	 */
	public function getNewShare($num = 20)
	{
        $sql = 'SELECT * FROM '.FDB::table('share').' 
			WHERE share_data IN (\'goods\',\'goods_photo\',\'photo\') 
			ORDER BY share_id DESC 
			LIMIT 0,'.$num;
        
        $list = FDB::fetchAll($sql);
		$list = ShareService::getShareDetailList($list);
		return $list;
	}

	/**
	 * 获取首页显示的分享分类最近7天最热标签
	 * @return array
	 */
	public function getIndexShareCateHotTags($num = 30)
	{
		static $list = NULL;
		if($list === NULL)
		{
			global $_FANWE;
			FanweService::instance()->cache->loadCache('goods_category');
			$cate_indexs = $_FANWE['cache']['goods_category']['index'];
			$day7_time = getTodayTime() - 604800;
			$list = array();
			foreach($cate_indexs as $cate_id)
			{
				$cids = array();
				FS('Share')->getChildCids($cate_id,$cids);
				$sql = 'SELECT st.tag_name,(gct.tag_id > 0) AS gt_tag,COUNT(DISTINCT t.share_id) AS share_count,
					GROUP_CONCAT(DISTINCT t.share_id ORDER BY t.is_best DESC,t.collect_count DESC SEPARATOR \',\') AS share_ids
					FROM (SELECT DISTINCT(s.share_id),s.is_best,s.collect_count,sc.cate_id
                        FROM '.FDB::table('share_category').' AS sc FORCE INDEX (cate_id)
                        INNER JOIN '.FDB::table('share').' AS s ON s.share_id = sc.share_id
                        WHERE sc.cate_id IN ('.implode(',',$cids).')
                        ORDER BY s.share_id DESC LIMIT 0,2000) AS t
                    STRAIGHT_JOIN '.FDB::table('share_tags').' AS st ON st.share_id = t.share_id
                    LEFT JOIN '.FDB::table('goods_tags').' AS gt ON gt.tag_name = st.tag_name
                    LEFT JOIN '.FDB::table('goods_category_tags').' AS gct ON gct.cate_id IN ('.implode(',',$cids).') AND gct.tag_id = gt.tag_id
                    GROUP BY st.tag_name
                    ORDER BY gt_tag DESC,gt.sort ASC,share_count DESC LIMIT 0,'.$num;

				$res = FDB::query($sql);
				while($data = FDB::fetch($res))
				{
					$tag_encode = urlencode($data['tag_name']);
					$list[$cate_id]['tags'][$data['tag_name']] = $tag_encode;
					$share_ids = explode(',',$data['share_ids']);
					$share_ids = array_slice($share_ids,0,11);
                    if(count($share_ids) > 1)
					    array_pop($share_ids);

					foreach($share_ids as $share_id)
					{
						if(!isset($list['shares'][$share_id]))
						{
							$list['share_list'][] = $share_id;
							$list['shares'][$share_id] = array(
								'cate_id'=>$cate_id,
								'tag_name'=>$data['tag_name'],
								'tag_encode'=>$tag_encode);

							break;
						}
					}
				}
			}
		}

		return $list;
	}
	
	/**
	 * 获取首页显示的分享最近最热标签
	 * @return array
	 */
	public function getIndexShareHotTags($num = 40)
	{
		global $_FANWE;
		$list = array();
		
		$sql = 'SELECT st.tag_name,(gt.tag_name IS NOT NULL) as gt_tag,COUNT(DISTINCT t.share_id) AS share_count,
			GROUP_CONCAT(DISTINCT t.share_id ORDER BY t.is_best DESC,t.collect_count DESC SEPARATOR \',\') AS share_ids
			FROM (SELECT DISTINCT(share_id),is_best,collect_count 
				FROM '.FDB::table('share').' 
				WHERE share_data IN (\'photo\',\'goods\',\'goods_photo\') 
				ORDER BY share_id DESC LIMIT 0,2000) AS t
			STRAIGHT_JOIN '.FDB::table('share_tags').' AS st ON st.share_id = t.share_id
			LEFT JOIN '.FDB::table('goods_tags').' AS gt ON gt.tag_name = st.tag_name 
			GROUP BY st.tag_name
			ORDER BY gt_tag DESC,gt.sort ASC,share_count DESC LIMIT 0,'.$num;
		$res = FDB::query($sql);
		while($data = FDB::fetch($res))
		{
			$tag_encode = urlencode($data['tag_name']);
			$list['tags'][$data['tag_name']] = $tag_encode;
			$share_ids = explode(',',$data['share_ids']);
			$share_ids = array_slice($share_ids,0,11);
			if(count($share_ids) > 1)
				array_pop($share_ids);

			foreach($share_ids as $share_id)
			{
				if(!isset($list['shares'][$share_id]))
				{
					$list['share_list'][] = $share_id;
					$list['shares'][$share_id] = array(
						'tag_name'=>$data['tag_name'],
						'tag_encode'=>$tag_encode);
					break;
				}
			}
		}
		
		if(count($list['share_list']) > 0)
		{
			$list['uids'] = array();
			$sql = 'SELECT share_id,uid,collect_count,cache_data,create_time FROM '.FDB::table('share').' 
				WHERE share_id IN ('.implode(',',$list['share_list']).')';
			$res = FDB::query($sql);
			while($data = FDB::fetch($res))
			{
				$share_id = $data['share_id'];
				$tag_name = $list['shares'][$share_id]['tag_name'];
				$tag_encode = $list['shares'][$share_id]['tag_encode'];
				
				$list['uids'][$data['uid']] = 1;
				$data['cache_data'] = fStripslashes(unserialize($data['cache_data']));
				$data['time'] = getBeforeTimelag($data['create_time']);
				$data['tag_name'] = $tag_name;
				$data['url'] = FU('book/shopping',array('sid'=>$data['share_id'],'tag'=>$tag_encode));
				FS('Share')->shareImageFormat($data,1);
				unset($data['cache_data']);
				$list['shares'][$share_id] = $data;
			}
			unset($list['share_list']);
			return $list;
		}
		else
			return false;
	}

	/**
	 * 获取首页显示的分享分类最近7天最热标签的分享
	 * @return array
	 */
	public function getIndexCateTagShares($num = 30)
	{
		global $_FANWE;
		$day7_time = getTodayTime() - 604800;
		$cate_list = &ShareService::getIndexShareCateHotTags($num);
		if(!empty($cate_list['share_list']))
		{
			$list = array();
			$share_datas = array();
			$sql = 'SELECT * FROM '.FDB::table('share').' 
				WHERE share_id IN ('.implode(',',$cate_list['share_list']).')';
			$list = FDB::fetchAll($sql);
			$list = ShareService::getShareDetailList($list);
			foreach($list as $data)
			{
				$cate_list['shares'][$data['share_id']]['share'] = $data;
			}
		}

		foreach($cate_list['shares'] as $share)
		{
			$cate_id = $share['cate_id'];
			unset($share['cate_id']);

			$uid = $share['share']['uid'];
			if(!isset($cate_list[$cate_id]['user'][$uid]))
				$cate_list[$cate_id]['user'][$uid] = $share['share']['user_name'];
			$cate_list[$cate_id]['shares'][] = $share;
		}

		unset($cate_list['shares']);
		unset($cate_list['share_list']);
		
		return $cate_list;
	}

	public function getChildCids($rid,&$cids)
	{
		global $_FANWE;
		$root_cate = $_FANWE['cache']['goods_category']['all'][$rid];
		$cids[] = $rid;
		if(isset($root_cate['child']))
		{
			foreach($root_cate['child'] as $cid)
			{
				ShareService::getChildCids($cid,$cids);
			}
		}
	}
	
	public function getPhotoListByType($type,$num = 6)
	{
		$list = array();
		$sql = 'SELECT DISTINCT(share_id),uid,photo_id,img FROM '.FDB::table('share_photo').' 
			WHERE type = \''.$type.'\' ORDER BY share_id DESC,sort ASC LIMIT 0,'.$num;
        $res = FDB::query($sql);
		while($data = FDB::fetch($res))
		{
			$data['url'] = FU('book/'.$type,array('sid'=>$data['share_id']));
			$list[] = $data;
		}
		return $list;
	}
}
?>