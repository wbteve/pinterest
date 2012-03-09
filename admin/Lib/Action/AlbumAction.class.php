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
 专辑
 +------------------------------------------------------------------------------
 */
class AlbumAction extends CommonAction
{
	public function index()
	{
		$where = '';
		$parameter = array();
		$keyword = trim($_REQUEST['keyword']);
		$uname = trim($_REQUEST['uname']);
		$cid = (int)$_REQUEST['cid'];

		if(!empty($keyword))
		{
			$this->assign("keyword",$keyword);
			$parameter['keyword'] = $keyword;
			$where.=" AND a.title LIKE '%".mysqlLikeQuote($keyword)."%' ";
		}

		if(!empty($uname))
		{
			$this->assign("uname",$uname);
			$parameter['uname'] = $uname;
			$match_key = segmentToUnicodeA($uname,'+');
			$where.=" AND match(u.user_name_match) against('".$match_key."' IN BOOLEAN MODE) ";
            $like_name = mysqlLikeQuote($uname);
            $where .= ' AND u.user_name LIKE \'%'.$like_name.'%\'';
		}
		
		if($cid > 0)
		{
			$this->assign("cid",$cid);
			$parameter['cid'] = $cid;
			$where .= " AND a.cid = $cid";
		}

		$model = M();

		if(!empty($where))
		{
			$where = 'WHERE' . $where;
			$where = str_replace('WHERE AND','WHERE',$where);
		}

		$sql = 'SELECT COUNT(DISTINCT a.id) AS tcount 
			FROM '.C("DB_PREFIX").'album AS a 
			LEFT JOIN '.C("DB_PREFIX").'user AS u ON u.uid = a.uid 
			'.$where;

		$count = $model->query($sql);
		$count = $count[0]['tcount'];

		$sql = 'SELECT a.*,ac.name AS cname,u.user_name   
			FROM '.C("DB_PREFIX").'album AS a 
			LEFT JOIN '.C("DB_PREFIX").'user AS u ON u.uid = a.uid 
			LEFT JOIN '.C("DB_PREFIX").'album_category AS ac ON ac.id = a.cid 
			'.$where.' GROUP BY a.id';
		$this->_sqlList($model,$sql,$count,$parameter,'a.id');
		
		$category_list = D("AlbumCategory")->order('sort asc,id asc')->findAll();
		$this->assign("category_list",$category_list);
		
		$this->display ();
		return;
	}

	public function edit()
	{
		$category_list = D("AlbumCategory")->order('sort asc,id asc')->findAll();
		$this->assign("category_list",$category_list);
		parent::edit();
	}

	public function update()
	{
        $id = intval($_REQUEST['id']);
		$_POST['is_flash'] = intval($_REQUEST['is_flash']);
		$_POST['is_best'] = intval($_REQUEST['is_best']);
		if($_POST['is_flash'] == 0)
			unset($_FILES['flash_img']);
			
		if($_POST['is_best'] == 0)
			unset($_FILES['best_img']);
			
		$model = D ('Album');
		$album = $model->getById($id);
		
		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}
		
		// 更新数据
		$list=$model->save($data);
		if (false !== $list)
		{
			if($_POST['is_flash'] == 0 && !empty($album['flash_img']))
			{
				@unlink(FANWE_ROOT.$album['flash_img']);
				$album['flash_img'] = '';
			}
				
			if($_POST['is_best'] == 0 && !empty($album['best_img']))
			{
				@unlink(FANWE_ROOT.$album['best_img']);
				$album['best_img'] = '';
			}
							
			if($upload_list = $this->uploadImages())
			{
				foreach($upload_list as $upload_item)
				{
					$img = $upload_item['recpath'].$upload_item['savename'];
					if($upload_item['key'] == 'flash_img')
					{
						if(!empty($album['flash_img']))
							@unlink(FANWE_ROOT.$album['flash_img']);
						
						D("Album")->where('id = '.$id)->setField('flash_img',$img);
					}
					elseif($upload_item['key'] == 'best_img')
					{
						if(!empty($album['best_img']))
							@unlink(FANWE_ROOT.$album['best_img']);
						
						D("Album")->where('id = '.$id)->setField('best_img',$img);
					}
				}
			}
			
			Vendor("common");
			$tags = str_replace('　',' ',$_REQUEST['tags']);
			$tags = explode(' ',$tags);
			$tags = array_unique($tags);
			
			FS('Share')->updateShare($album['share_id'],$_REQUEST['title'],$_REQUEST['content']);
			FS("Album")->saveTags($id,$tags);
			if($_REQUEST['cid'] != $album['cid'])
			{
				FDB::query('UPDATE '.FDB::table("album_share").' SET cid = '.$_REQUEST['cid'].' WHERE album_id = '.$id);
			}
			
			$this->saveLog(1,$id);
			$this->assign('jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success (L('EDIT_SUCCESS'));
		}
		else
		{
			//错误提示
			$this->saveLog(0,$id);
			$this->error (L('EDIT_ERROR'));
		}
	}

	public function remove()
	{
		@set_time_limit(0);
		if(function_exists('ini_set'))
			ini_set('max_execution_time',0);
	
		//删除指定记录
		Vendor("common");
		$result = array('isErr'=>0,'content'=>'');
		$id = $_REQUEST['id'];
		if(!empty($id))
		{
			$name=$this->getActionName();
			$model = D($name);
			$pk = $model->getPk ();
			$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
            $ids = explode (',',$id);
            foreach($ids as $aid)
            {
                FS("Album")->deleteAlbum($aid,true);
            }

			$this->saveLog(1,$id);
		}
		else
		{
			$result['isErr'] = 1;
			$result['content'] = L('ACCESS_DENIED');
		}

		die(json_encode($result));
	}
}
?>