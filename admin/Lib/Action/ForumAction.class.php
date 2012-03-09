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

 +------------------------------------------------------------------------------
 */
class ForumAction extends CommonAction
{
	public function index()
	{
		$cate_tree = D("Forum")->order('sort asc,fid asc')->findAll();
		$cate_tree = D("Forum")->toFormatTree($cate_tree,'name','fid','parent_id');
		
		$this->assign("list",$cate_tree);
		$this->display ();
		return;
	}
	public function add()
	{	
		$this->assign("newsort",M(MODULE_NAME)->max("sort")+1);
		$cate_tree = M("Forum")->where('parent_id = 0')->order('sort asc,fid asc')->findAll();
		$this->assign("cate_tree",$cate_tree);
		parent::add();
	}
	public function insert()
	{
		$desc = trim($_REQUEST['desc']);
		$model = D("Forum");
		if(false === $data = $model->create())
		{
			$this->error($model->getError());
		}
		
		//保存当前数据对象
		$list=$model->add($data);
		if ($list !== false)
		{
			$data = array();
			$data['fid'] = $list;
			$data['desc'] = $desc;
			if($upload_list = $this->uploadImages())
			{
				foreach($upload_list as $img)
				{
					if($img['key'] == 'cate_icon')
						$data['logo'] = $img['recpath'].$img['savename'];
					
					if($img['key'] == 'cate_img')
						$data['img'] = $img['recpath'].$img['savename'];
				}
			}
			
			D("ForumFields")->add($data);
			
			$this->saveLog(1,$list);
			$this->success (L('ADD_SUCCESS'));

		}
		else
		{
			$this->saveLog(0,$list);
			$this->error (L('ADD_ERROR'));
		}
	}
	
	public function edit()
	{	
		$id = intval($_REQUEST['fid']);
		
		$ids = D(MODULE_NAME)->getChildIds($id,'fid','parent_id');
		$ids[] = $id;
		$condition['parent_id'] = 0;
		$condition['fid'] = array('not in',$ids);

		$cate_tree = D(MODULE_NAME)->where($condition)->order('sort asc,fid asc')->findAll();
		$this->assign("cate_tree",$cate_tree);
		
		$vo = D("Forum")->getById($id);
		$vo['fields'] = D("ForumFields")->where('fid = '.$id)->find();
		
		$this->assign ( 'vo', $vo );
		$this->display();
	}
	
	public function update()
	{
		$id = intval($_REQUEST['fid']);
		$desc = trim($_REQUEST['desc']);
		
		$model = D("Forum");
		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}
		
		// 更新数据
		$list=$model->save($data);
		if (false !== $list)
		{
			$data = array();
			$data['desc'] = $desc;

			if($upload_list = $this->uploadImages())
			{
				foreach($upload_list as $img)
				{
					if($img['key'] == 'cate_icon')
					{
						$old_img = D("ForumFields")->where('fid = '.$id)->getField('logo');
						if(!empty($old_img))
							@unlink(FANWE_ROOT.$old_img);
						
						$data['logo'] = $img['recpath'].$img['savename'];
					}
					
					if($img['key'] == 'cate_img')
					{
						$old_img = D("ForumFields")->where('fid = '.$id)->getField('img');
						if(!empty($old_img))
							@unlink(FANWE_ROOT.$old_img);
						
						$data['img'] = $img['recpath'].$img['savename'];
					}
				}
			}
			D("ForumFields")->where('fid = '.$id)->save($data);
			
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
			$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
			if($model->where(array ("parent_id" => array ('in', explode ( ',', $id ) ) ))->count()>0)
			{
				$result['isErr'] = 1;
				$result['content'] = L('SUB_CATE_EXIST');
				die(json_encode($result));
			}
			
			if(M("ForumThread")->where(array ("fid" => array ('in', explode ( ',', $id ) ) ))->count()>0)
			{
				$result['isErr'] = 1;
				$result['content'] = L('THREAD_EXIST');
				die(json_encode($result));
			}
			if(false !== $model->where ( $condition )->delete ())
			{
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