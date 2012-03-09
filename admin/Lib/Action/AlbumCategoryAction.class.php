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
 专辑分类
 +------------------------------------------------------------------------------
 */
class AlbumCategoryAction extends CommonAction
{
	public function insert()
	{
		$name=$this->getActionName();
		$model = D ($name);
		if(false === $data = $model->create())
		{
			$this->error($model->getError());
		}
		
		//保存当前数据对象
		$list=$model->add($data);
		if ($list !== false)
		{
			if($upload_list = $this->uploadImages())
			{
				foreach($upload_list as $upload_item)
				{
					$img = $upload_item['recpath'].$upload_item['savename'];
					if($upload_item['key'] == 'img')
						$model->where("id=".$list)->setField("img",$img);
					elseif($upload_item['key'] == 'img_hover')
						$model->where("id=".$list)->setField("img_hover",$img);
				}
			}
			
			$this->saveLog(1,$list);
			$this->success (L('ADD_SUCCESS'));
		}
		else
		{
			$this->saveLog(0,$list);
			$this->error (L('ADD_ERROR'));
		}
	}
	
	public function update()
	{
		$id = intval($_REQUEST['id']);
		$name=$this->getActionName();
		$model = D ($name);
		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}
		
		// 更新数据
		$list=$model->save($data);
		if (false !== $list)
		{
			if($upload_list = $this->uploadImages())
			{
				$cate = $model->getById($id);
				foreach($upload_list as $upload_item)
				{
					$img = $upload_item['recpath'].$upload_item['savename'];
					if($upload_item['key'] == 'img')
					{
						if(!empty($cate['img']))
							@unlink(FANWE_ROOT.$cate['img']);
						$model->where("id=".$id)->setField("img",$img);
					}
					elseif($upload_item['key'] == 'img_hover')
					{
						if(!empty($cate['img_hover']))
							@unlink(FANWE_ROOT.$cate['img_hover']);
						$model->where("id=".$id)->setField("img_hover",$img);
					}
				}
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
		//删除指定记录
		$result = array('isErr'=>0,'content'=>'');
		$id = $_REQUEST['id'];
		if(!empty($id))
		{
			$name=$this->getActionName();
			$model = D($name);
			$pk = $model->getPk ();
			
			if(D("Album")->where(array("cid"=>array('in',explode(',',$id))))->count()>0)
			{
				$result['isErr'] = 1;
				$result['content'] = L('ALBUM_EXIST');
				die(json_encode($result));
			}
			
			$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
			$datas = $model->where($condition )->field('img,img_hover')->findAll();
			if(false !== $model->where ( $condition )->delete ())
			{
				foreach($datas as $data)
				{
					if(!empty($data['img']))
						@unlink(FANWE_ROOT.$data['img']);
					
					if(!empty($data['img_hover']))
						@unlink(FANWE_ROOT.$data['img_hover']);
				}
				$this->saveLog(1,$id);
			}
			else
			{
				$this->saveLog(0,$id);
				$result['isErr'] = 1;
				$result['content'] = L('REMOVE_ERROR');
			}
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