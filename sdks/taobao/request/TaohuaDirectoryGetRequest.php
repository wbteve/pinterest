<?php
/**
 * TOP API: taobao.taohua.directory.get request
 * 
 * @author auto create
 * @since 1.0, 2011-11-14 13:22:04
 */
class TaohuaDirectoryGetRequest
{
	/** 
	 * 文档商品ID
	 **/
	private $itemId;
	
	private $apiParas = array();
	
	public function setItemId($itemId)
	{
		$this->itemId = $itemId;
		$this->apiParas["item_id"] = $itemId;
	}

	public function getItemId()
	{
		return $this->itemId;
	}

	public function getApiMethodName()
	{
		return "taobao.taohua.directory.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->itemId,"itemId");
	}
}
