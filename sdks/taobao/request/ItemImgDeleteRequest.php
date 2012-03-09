<?php
/**
 * TOP API: taobao.item.img.delete request
 * 
 * @author auto create
 * @since 1.0, 2011-11-14 13:22:04
 */
class ItemImgDeleteRequest
{
	/** 
	 * 商品图片ID
	 **/
	private $id;
	
	/** 
	 * 商品数字ID，必选
	 **/
	private $numIid;
	
	private $apiParas = array();
	
	public function setId($id)
	{
		$this->id = $id;
		$this->apiParas["id"] = $id;
	}

	public function getId()
	{
		return $this->id;
	}

	public function setNumIid($numIid)
	{
		$this->numIid = $numIid;
		$this->apiParas["num_iid"] = $numIid;
	}

	public function getNumIid()
	{
		return $this->numIid;
	}

	public function getApiMethodName()
	{
		return "taobao.item.img.delete";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->id,"id");
		RequestCheckUtil::checkNotNull($this->numIid,"numIid");
		RequestCheckUtil::checkMinValue($this->numIid,0,"numIid");
	}
}
