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
 * 管理员
 +------------------------------------------------------------------------------
 */
class ShareGoodsAction extends CommonAction
{
	public function index()
	{
		vendor("common");
		parent::index();
	}

	public function edit()
	{
		vendor("common");
		$name = $this->getActionName();
		$vo = M($name)->getByGoodsId($_REQUEST['goods_id']);
		$this->assign ( 'vo', $vo );

		//$category = FDB::fetchAll("select cate_id,cate_name from ".FDB::table("goods_category")." where parent_id = 0");
		//$this->assign ( 'category', $category );

		$shop = FDB::fetchFirst("select shop_name,shop_id from ".FDB::table("shop")." where shop_id = ".$vo['shop_id']);
		$this->assign ( 'shop', $shop );
		$this->display ();
	}

	public function searchShop()
	{
		vendor("common");
		$kw = trim($_REQUEST['kw']);
		$result['kw'] = $kw;
		$shop_list = FDB::fetchAll("select * from ".FDB::table("shop")." where shop_name like '%".$kw."%' order by rand() limit 20 ");
		$shop_count = intval(FDB::resultFirst("select count(*) from ".FDB::table("shop")." where shop_name like '%".$kw."%'"));
		$result['shop_list'] = $shop_list;
		$result['shop_count'] = $shop_count;
		die(json_encode($result));
	}

	public function update()
	{
		$name=$this->getActionName();
		$model = D ( $name );
		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$list=$model->save ();
		$id = $data[$model->getPk()];
		if (false !== $list) {
			vendor("common");
            $share_id = $model->where("goods_id = '$id'")->getField('share_id');
			deleteCache('share/'.getDirsById($share_id).'/imgs');
            deleteCache('share/'.getDirsById($share_id).'/detail');
			$this->saveLog(1,$id);
			//$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success (L('EDIT_SUCCESS'));
		} else {
			//错误提示
			$this->saveLog(0,$id);
			$this->error (L('EDIT_ERROR'));
		}
	}
}

?>