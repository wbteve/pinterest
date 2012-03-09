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
 * 达人
 +------------------------------------------------------------------------------
 */
class UserDarenAction extends CommonAction
{
	public function index()
	{
		$where = '';
		$parameter = array();
		$user_name = trim($_REQUEST['user_name']);
		$day_time = trim($_REQUEST['day_time']);
		$status = !isset($_REQUEST['status']) ? -1 : intval($_REQUEST['status']);
		
		if(!empty($user_name))
		{
			$this->assign("user_name",$user_name);
			$parameter['user_name'] = $user_name;
			$match_key = segmentToUnicodeA($user_name,'+');
			$where.=" AND match(u.user_name_match) against('".$match_key."' IN BOOLEAN MODE) ";
		}
		
		if(!empty($day_time))
		{
			$this->assign("day_time",$day_time);
			$parameter['day_time'] = $day_time;
			$day_time = strZTime($day_time);
			$where .= " AND ud.day_time = '$day_time'";
		}
		
		if($status > -1)
		{
			$this->assign("status",$status);
			$parameter['status'] = $status;
			$where .= " AND ud.status = $status";
		}
		else
			$this->assign("status",-1);
		
		$model = M();
		
		if(!empty($where))
			$where = 'WHERE 1' . $where;
		
		$sql = 'SELECT COUNT(ud.id) AS tcount 
			FROM '.C("DB_PREFIX").'user_daren AS ud
			INNER JOIN '.C("DB_PREFIX").'user AS u ON u.uid = ud.uid '.$where;
		
		$count = $model->query($sql);
		$count = $count[0]['tcount'];
		
		$sql = 'SELECT ud.*,u.user_name  
			FROM '.C("DB_PREFIX").'user_daren AS ud
			INNER JOIN '.C("DB_PREFIX").'user AS u ON u.uid = ud.uid '.$where;
			
		$this->_sqlList($model,$sql,$count,$parameter);
		$this->display();
	}
	
	public function insert()
	{
		$_POST['is_best'] = intval($_REQUEST['is_best']);
		$_POST['is_index'] = intval($_REQUEST['is_index']);
		$uid = (int)$_REQUEST['uid'];
		$name=$this->getActionName();
		$model = D ($name);
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		
		//保存当前数据对象
		$id=$model->add ();
		if ($id!==false)
		{
			if($upload_list = $this->uploadImages())
			{
				foreach($upload_list as $upload_item)
				{
					$img = $upload_item['recpath'].$upload_item['savename'];
					if($upload_item['key'] == 'img_file')
						D("UserDaren")->where('id = '.$id)->setField('img',$img);
					elseif($upload_item['key'] == 'index_img_file')
						D("UserDaren")->where('id = '.$id)->setField('index_img',$img);
				}
			}
			
			D("User")->where('uid = '.$uid)->setField('is_daren',1);
			
			Vendor("common");
			clearCacheDir('daren');
			
			$this->saveLog(1,$id);
			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success (L('ADD_SUCCESS'));
		} else {
			//失败提示
			$this->saveLog(0,$id);
			$this->error (L('ADD_ERROR'));
		}
	}
	
	public function edit()
	{
		$id = intval($_REQUEST['id']);
		$vo = D("UserDaren")->getById($id);
		$user_name = D("User")->where('uid = '.$vo['uid'])->getField('user_name');
		$this->assign ('vo',$vo);
		$this->assign ('user_name',$user_name);
		$this->display();
	}
	
	public function update()
	{
		$id = intval($_REQUEST['id']);
		$_POST['is_best'] = intval($_REQUEST['is_best']);
		$_POST['is_index'] = intval($_REQUEST['is_index']);
		$name=$this->getActionName();
		$model = D ( $name );
		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}
		
		// 更新数据
		$list=$model->save($data);
		if (false !== $list)
		{
			if($upload_list = $this->uploadImages())
			{
				$daren = D("UserDaren")->getById($id);
				foreach($upload_list as $upload_item)
				{
					$img = $upload_item['recpath'].$upload_item['savename'];
					if($upload_item['key'] == 'img_file')
					{
						@unlink(FANWE_ROOT.$daren['img']);
						D("UserDaren")->where('id = '.$id)->setField('img',$img);
					}
					elseif($upload_item['key'] == 'index_img_file')
					{
						@unlink(FANWE_ROOT.$daren['index_img']);
						D("UserDaren")->where('id = '.$id)->setField('index_img',$img);
					}
				}
			}
			
			Vendor("common");
			clearCacheDir('daren');
			
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
	
	public function toggleStatus()
	{
		$id = intval($_REQUEST['id']);
		if($id == 0)
			exit;
		
		$val = intval($_REQUEST['val']) == 0 ? 1 : 0;
			
		$field = trim($_REQUEST['field']);
		if(empty($field))
			exit;
		
		$result = array('isErr'=>0,'content'=>'');
		$name=$this->getActionName();
		$model = D($name);
		$pk = $model->getPk();
		if(false !== $model->where($pk.' = '.$id)->setField($field,$val))
		{
			if($field == 'status')
			{
				$uid = (int)$model->where($pk.' = '.$id)->getField('uid');
				D("User")->where('uid = '.$uid)->setField('is_daren',$val);
			}

			$this->saveLog(1,$id,$field);
			$result['content'] = $val;
			
			Vendor("common");
			clearCacheDir('daren');
		}
		else
		{
			$this->saveLog(0,$id,$field);
			$result['isErr'] = 1;
		}
		
		die(json_encode($result));
	}
	
	public function remove()
	{
		//删除指定记录
		$result = array('isErr'=>0,'content'=>'');
		$id = $_REQUEST['id'];
		if(!empty($id))
		{
			$model = D("UserDaren");
			$condition = array('id' => array('in',explode (',',$id)));
			$list = $model->where($condition)->findAll();
			
			if(false !== $model->where ($condition)->delete())
			{
				foreach($list as $item)
				{
					if(!empty($item['img']))
						@unlink(FANWE_ROOT.$item['img']);
						
					if(!empty($item['index_img']))
						@unlink(FANWE_ROOT.$daren['index_img']);
						
					D("User")->where('uid = '.$item['uid'])->setField('is_daren',0);
				}
				
				Vendor("common");
				clearCacheDir('daren');
			
				$this->saveLog(1,$id);
			}
			else
			{
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