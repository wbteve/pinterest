<?php
/**
 * TOP API: taobao.taohua.childcates.get request
 * 
 * @author auto create
 * @since 1.0, 2011-11-14 13:22:04
 */
class TaohuaChildcatesGetRequest
{
	/** 
	 * 通过类目ID获取它的子类目列表
	 **/
	private $cateId;
	
	private $apiParas = array();
	
	public function setCateId($cateId)
	{
		$this->cateId = $cateId;
		$this->apiParas["cate_id"] = $cateId;
	}

	public function getCateId()
	{
		return $this->cateId;
	}

	public function getApiMethodName()
	{
		return "taobao.taohua.childcates.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
	}
}
