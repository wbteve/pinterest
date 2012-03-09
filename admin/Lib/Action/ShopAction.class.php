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
class ShopAction extends CommonAction
{
	public function add()
	{	
		$cate_tree = M("ShopCategory")->findAll();
		$cate_tree = D("ShopCategory")->toFormatTree($cate_tree,'name','id','parent_id');
		$this->assign("cate_tree",$cate_tree);
		parent::add();
	}
	
	public function insert()
	{
		vendor("common");
		$res = FS("Image")->save($key='shop_logo',$dir='share',$is_thumb=false,$whs=array(),$is_delete_origin = false,$is_water = false);
		$_POST['shop_logo'] = $res['url'];
		parent::insert();
	}
	
	
	public function edit()
	{
		vendor("common");
		$cate_tree = M("ShopCategory")->findAll();
		$cate_tree = D("ShopCategory")->toFormatTree($cate_tree,'name','id','parent_id');
		$this->assign("cate_tree",$cate_tree);
		parent::edit();
	}
	
	public function update()
	{
		vendor("common");
		$res = FS("Image")->save($key='shop_logo',$dir='share',$is_thumb=false,$whs=array(),$is_delete_origin = false,$is_water = false);
		if($res)
			$_POST['shop_logo'] = $res['url'];
		Cookie::set ( '_currentUrl_' ,null);
		parent::update();
	}
}

?>