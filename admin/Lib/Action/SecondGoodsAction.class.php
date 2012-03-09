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
 二手商品
 +------------------------------------------------------------------------------
 */
class SecondGoodsAction extends CommonAction
{
	public function index()
	{
		$where = '';
		$parameter = array();
		$keyword = trim($_REQUEST['keyword']);
		$uname = trim($_REQUEST['uname']);
		$sid = intval($_REQUEST['sid']);
		$city_id = intval($_REQUEST['city_id']);

		if(!empty($keyword))
		{
			$this->assign("keyword",$keyword);
			$parameter['keyword'] = $keyword;
			$where.=" AND sg.name LIKE '%".mysqlLikeQuote($keyword)."%' ";
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
		
		if($sid > 0)
		{
			$this->assign("sid",$sid);
			$parameter['sid'] = $sid;
			$where .= " AND sg.sid = $sid";
		}
		
		if($city_id > 0)
		{
			$this->assign("city_id",$city_id);
			$parameter['city_id'] = $city_id;
			$where .= " AND sg.city_id = $city_id";
		}

		$model = M();

		if(!empty($where))
			$where = 'WHERE 1' . $where;

		$sql = 'SELECT COUNT(DISTINCT sg.gid) AS tcount
			FROM '.C("DB_PREFIX").'second_goods AS sg 
			LEFT JOIN '.C("DB_PREFIX").'user AS u ON u.uid = sg.uid 
			'.$where;

		$count = $model->query($sql);
		$count = $count[0]['tcount'];

		$sql = 'SELECT sg.*,s.name AS sname,u.user_name   
			FROM '.C("DB_PREFIX").'second_goods AS sg 
			LEFT JOIN '.C("DB_PREFIX").'user AS u ON u.uid = sg.uid 
			LEFT JOIN '.C("DB_PREFIX").'second AS s ON s.sid = sg.sid 
			'.$where.' GROUP BY sg.gid';
		$this->_sqlList($model,$sql,$count,$parameter,'sg.gid');
		
		$second_list = D("Second")->order('sort asc,sid asc')->findAll();
		$this->assign("second_list",$second_list);
		
		$city_list = D("Region")->where('parent_id = 0')->order('sort asc,id asc')->findAll();
		$this->assign("city_list",$city_list);
		$this->display ();
		return;
	}

	public function edit()
	{
		$second_list = D("Second")->order('sort asc,sid asc')->findAll();
		$this->assign("second_list",$second_list);
		
		$city_list = D("Region")->where('parent_id = 0')->order('sort asc,id asc')->findAll();
		$this->assign("city_list",$city_list);
		Cookie::set ( '_currentUrl_',NULL );
		parent::edit();
	}

	public function update()
	{
        Vendor("common");
		$_POST['valid_time'] = strZTime($_POST['valid_time']);
		
        $gid = intval($_REQUEST['gid']);
        $goods = D('SecondGoods')->where("gid = '$gid'")->find();
        if($goods['share_id'] > 0)
        {
			$share_id = $goods['share_id'];
            $title = trim($_REQUEST['name']);
            $content = trim($_REQUEST['content']);
			if($goods['title'] != $title || $goods['content'] != $content)
            	FS("Share")->updateShare($share_id,$title,$content);
        }
		parent::update();
	}

	public function remove()
	{
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
            foreach($ids as $gid)
            {
                FS("Second")->deleteGoods($gid);
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