<?php
/**
 * TOP API: taobao.huabao.poster.goodsinfo.get request
 * 
 * @author auto create
 * @since 1.0, 2011-11-14 13:22:04
 */
class HuabaoPosterGoodsinfoGetRequest
{
	/** 
	 * 画报的ID
	 **/
	private $posterId;
	
	private $apiParas = array();
	
	public function setPosterId($posterId)
	{
		$this->posterId = $posterId;
		$this->apiParas["poster_id"] = $posterId;
	}

	public function getPosterId()
	{
		return $this->posterId;
	}

	public function getApiMethodName()
	{
		return "taobao.huabao.poster.goodsinfo.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->posterId,"posterId");
	}
}
