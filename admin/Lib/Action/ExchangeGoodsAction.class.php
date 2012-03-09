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
 积分兑换商品
 +------------------------------------------------------------------------------
 */
class ExchangeGoodsAction extends CommonAction
{
	public function index()
	{
		$where = '';
		$parameter = array();
		$name = trim($_REQUEST['name']);
		$begin_time_str = trim($_REQUEST['begin_time']);
		$end_time_str = trim($_REQUEST['end_time']);
		
		$begin_time = !empty($begin_time_str) ? strZTime($begin_time_str) : 0;
		$end_time = !empty($end_time_str) ? strZTime($end_time_str) : 0;

		if(!empty($name))
		{
			$this->assign("name",$name);
			$parameter['name'] = $name;
            $like_name = mysqlLikeQuote($name);
            $where .= ' AND name LIKE \'%'.$like_name.'%\'';
		}
		
		if ($begin_time > 0)
		{
			$this->assign("begin_time",$begin_time_str);
			$parameter['begin_time'] = $begin_time_str;
			$where .= " AND begin_time >= '".$begin_time."'";
		}
		
		if ($end_time > 0)
		{
			$this->assign("end_time",$end_time_str);
			$parameter['end_time'] = $end_time_str;
			$where .= " AND end_time < '".($end_time + 86400)."'";
		}

		$model = M();

		if(!empty($where))
			$where = 'WHERE 1' . $where;

		$sql = 'SELECT COUNT(id) AS tcount
			FROM '.C("DB_PREFIX").'exchange_goods '.$where;

		$count = $model->query($sql);
		$count = $count[0]['tcount'];

		$sql = 'SELECT * FROM '.C("DB_PREFIX").'exchange_goods '.$where;
		$this->_sqlList($model,$sql,$count,$parameter,'id');
		$this->display();
		return;
	}
	
	public function insert()
	{
		$_POST['begin_time'] = strZTime($_POST['begin_time']);
		$_POST['end_time'] = strZTime($_POST['end_time']);
		$_POST['integral'] = (int)$_POST['integral'];
		$_POST['stock'] = (int)$_POST['stock'];
		$_POST['user_num'] = (int)$_POST['user_num'];
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
				$img = $upload_list[0]['recpath'].$upload_list[0]['savename'];
				if(!empty($img))
					$model->where("id=".$list)->setField("img",$img);
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
		$_POST['begin_time'] = strZTime($_POST['begin_time']);
		$_POST['end_time'] = strZTime($_POST['end_time']);
		$_POST['integral'] = (int)$_POST['integral'];
		$_POST['stock'] = (int)$_POST['stock'];
		$_POST['user_num'] = (int)$_POST['user_num'];
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
				$img = $upload_list[0]['recpath'].$upload_list[0]['savename'];
				if(!empty($img))
				{
					$old_img = $model->where("id=".$id)->getField('img');
					if(!empty($old_img))
						@unlink(FANWE_ROOT.$old_img);
					$model->where("id=".$id)->setField("img",$img);
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
			$datas = $model->where($condition )->field('img')->findAll();
			if(false !== $model->where ( $condition )->delete ())
			{
				foreach($datas as $data)
				{
					if(!empty($data['img']))
						@unlink(FANWE_ROOT.$data['img']);
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

function getTypeName($type)
{
	return L('GOODS_TYPE_'.($type));
}
?>