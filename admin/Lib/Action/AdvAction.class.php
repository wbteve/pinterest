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
 广告
 +------------------------------------------------------------------------------
 */
class AdvAction extends CommonAction
{
	public function index()
	{
		$where = '';
		$parameter = array();
		$name = trim($_REQUEST['name']);
		$position_id = intval($_REQUEST['position_id']);
		
		if(!empty($name))
		{
			$where .= " AND a.name LIKE '%".mysqlLikeQuote($name)."%'";
			$this->assign("name",$name);
			$parameter['name'] = $name;
		}

		if($position_id > 0)
		{
			$this->assign("position_id",$position_id);
			$parameter['position_id'] = $position_id;
			$where .= " AND a.position_id = '$position_id'";
		}

		$model = M();
		
		if(!empty($where))
			$where = 'WHERE 1' . $where;
		
		$sql = 'SELECT COUNT(DISTINCT a.id) AS acount 
			FROM '.C("DB_PREFIX").'adv AS a '.$where;

		$count = $model->query($sql);
		$count = $count[0]['acount'];

		$sql = 'SELECT a.*,ap.name AS position_name  
			FROM '.C("DB_PREFIX").'adv AS a 
			LEFT JOIN '.C("DB_PREFIX").'adv_position AS ap ON ap.id = a.position_id '.$where;
		$this->_sqlList($model,$sql,$count,$parameter,'a.id');
		
		$ap_list = M("AdvPosition")->where('status = 1')->findAll();
		$this->assign("ap_list",$ap_list);
		
		$this->display ();
	}
	
	public function add()
	{
		$ap_list = M("AdvPosition")->where('status = 1')->findAll();
		$this->assign("ap_list",$ap_list);
		parent::add();
	}
	
	public function insert()
	{
		$_POST['desc'] = trim($_POST['desc']);
		if($_POST['type']!= 1)
			$_POST['url'] = '';
			
		$model = D("Adv");
		if(false === $data = $model->create())
		{
			$this->error($model->getError());
		}
		
		//保存当前数据对象
		$id = $model->add($data);
		if ($id !== false)
		{
			$upload_list = $this->uploadImages(0,'adv',false,array(),true);
			if($upload_list)
			{
				foreach($upload_list as $plist){
					if($plist['key']=='code')
					{
						$img = $plist['recpath'].$plist['savename'];
						if(!empty($img))
							D("Adv")->where('id = '.$id)->setField('code',$img);
					}
					if($plist['key']=='small')
					{
						$img = $plist['recpath'].$plist['savename'];
						if(!empty($img))
							D("Adv")->where('id = '.$id)->setField('small',$img);
					}
				}
			}
			
			$this->saveLog(1,$id);
			$this->success (L('ADD_SUCCESS'));

		}
		else
		{
			$this->saveLog(0);
			$this->error (L('ADD_ERROR'));
		}
	}
	
	public function edit()
	{
		$ap_list = M("AdvPosition")->where('status = 1')->findAll();
		$this->assign("ap_list",$ap_list);
		parent::edit();
	}
	
	public function update()
	{
		$id = intval($_REQUEST['id']);
		$_POST['desc'] = trim($_POST['desc']);
		if($_POST['type']!= 1)
			$_POST['url'] = '';

		$old_img = D("Adv")->where('id = '.$id.' AND type IN (1,2)')->getField('code');
		if($_POST['type'] == 3 && !empty($old_img))
			@unlink(FANWE_ROOT.$old_img);
		
		$model = D("Adv");
		if(false === $data = $model->create())
		{
			$this->error($model->getError());
		}
		
		//保存当前数据对象
		$list=$model->save($data);
		if (false !== $list)
		{
			$upload_list = $this->uploadImages(0,'adv',false,array(),true);
			if($upload_list)
			{
				foreach($upload_list as $plist){
					if($plist['key']=='code')
					{
						$img = $plist['recpath'].$plist['savename'];
						if(!empty($img))
						{
							if(!empty($old_img))
								@unlink(FANWE_ROOT.$old_img);
							D("Adv")->where('id = '.$id)->setField('code',$img);
						}
					}
					
					if($plist['key']=='small')
					{
						$img = $plist['recpath'].$plist['savename'];
						if(!empty($img))
						{
							$old_small_img = D("Adv")->where('id = '.$id)->getField('small');
							if(!empty($old_small_img))
								@unlink(FANWE_ROOT.$old_small_img);
							D("Adv")->where('id = '.$id)->setField('small',$img);
						}
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
			$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
			$advs = $model->where($condition)->findAll();
			if(false !== $model->where ( $condition )->delete ())
			{
				foreach($advs as $adv)
				{
					if(!empty($adv['code']) && ($adv['type'] == 1 || $adv['type'] == 2))
						@unlink(FANWE_ROOT.$adv['code']);
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