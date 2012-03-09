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
 订单
 +------------------------------------------------------------------------------
 */
class OrderAction extends CommonAction
{
	public function index()
	{
		$where = '';
		$parameter = array();
		$name = trim($_REQUEST['name']);
		$sn = trim($_REQUEST['sn']);
		$user_name = trim($_REQUEST['user_name']);
		$goods_status = trim($_REQUEST['goods_status']);

		if(!empty($name))
		{
			$this->assign("name",$name);
			$parameter['name'] = $name;
            $like_name = mysqlLikeQuote($name);
            $where .= ' AND data_name LIKE \'%'.$like_name.'%\'';
		}
		
		if(!empty($sn))
		{
			$this->assign("sn",$sn);
			$parameter['sn'] = $sn;
            $where .= ' AND sn = \''.$sn.'\'';
		}
		
		if(!empty($user_name))
		{
			$this->assign("user_name",$user_name);
			$parameter['user_name'] = $user_name;
            $where .= ' AND user_name = \''.$user_name.'\'';
		}
		
		if($goods_status != "" && $goods_status >= 0)
		{
			$this->assign("goods_status",$goods_status);
			$parameter['goods_status'] = $goods_status;
            $where .= ' AND goods_status = '.$goods_status;
		}
		else
			$this->assign("goods_status",-1);

		$model = M();

		if(!empty($where))
			$where = 'WHERE 1' . $where;

		$sql = 'SELECT COUNT(id) AS tcount
			FROM '.C("DB_PREFIX").'order '.$where;

		$count = $model->query($sql);
		$count = $count[0]['tcount'];

		$sql = 'SELECT * FROM '.C("DB_PREFIX").'order '.$where;
		$this->_sqlList($model,$sql,$count,$parameter,'id');
		$this->display();
		return;
	}
	
	public function show()
	{
		$id = intval($_REQUEST['id']);
		$order = D("Order")->where("id = $id")->find();
		$order['goods_status_name'] = L("ORDER_GOODS_STATUS_".$order['goods_status']);
		$this->assign ( 'order', $order );
		$goods = D("ExchangeGoods")->where("id = ".$order['rec_id'])->find();
		$this->assign ('goods',$goods );
		$this->display ('show');
	}
	
	public function update()
	{
		$id = intval($_REQUEST['id']);
		$status = intval($_REQUEST['status']);
		$order = D("Order")->where("id = $id")->find();
		$order['adm_memo'] = trim($_REQUEST['adm_memo']);
		$order['goods_status'] = $status;
		D("Order")->save($order);
		$this->success (L('EDIT_SUCCESS'));
	}
}

function getOrderGoodsStatus($status)
{
	return L("ORDER_GOODS_STATUS_".$status);
}

function getOrderEdit($id,$status)
{
	if($status == 0)
		return "<a href='javascript:showOrder(".$id.");'>". L('EDIT')."</a>&nbsp;&nbsp;<a href=\"javascript:;\" onclick=\"removeData(this,'$id','uid')\">". L('REMOVE')."</a>";
	elseif ($status == 1)
		return "<a href='javascript:showOrder(".$id.");'>". L('VIEW')."</a>";
	else
		return "<a href='javascript:showOrder(".$id.");'>". L('VIEW')."</a>&nbsp;&nbsp;<a href=\"javascript:;\" onclick=\"removeData(this,'$id','uid')\">". L('REMOVE')."</a>";
}
?>