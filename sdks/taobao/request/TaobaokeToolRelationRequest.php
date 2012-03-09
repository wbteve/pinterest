<?php
/**
 * TOP API: taobao.taobaoke.tool.relation request
 * 
 * @author auto create
 * @since 1.0, 2011-11-14 13:22:04
 */
class TaobaokeToolRelationRequest
{
	/** 
	 * 用户的pubid 用来判断这个pubid是否是appkey关联的开发者的注册用户
	 **/
	private $pubid;
	
	private $apiParas = array();
	
	public function setPubid($pubid)
	{
		$this->pubid = $pubid;
		$this->apiParas["pubid"] = $pubid;
	}

	public function getPubid()
	{
		return $this->pubid;
	}

	public function getApiMethodName()
	{
		return "taobao.taobaoke.tool.relation";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->pubid,"pubid");
	}
}
