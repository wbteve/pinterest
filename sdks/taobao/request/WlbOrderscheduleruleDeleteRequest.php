<?php
/**
 * TOP API: taobao.wlb.orderschedulerule.delete request
 * 
 * @author auto create
 * @since 1.0, 2011-11-14 13:22:04
 */
class WlbOrderscheduleruleDeleteRequest
{
	/** 
	 * 订单调度规则ID
	 **/
	private $id;
	
	/** 
	 * 商品userNick
	 **/
	private $userNick;
	
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

	public function setUserNick($userNick)
	{
		$this->userNick = $userNick;
		$this->apiParas["user_nick"] = $userNick;
	}

	public function getUserNick()
	{
		return $this->userNick;
	}

	public function getApiMethodName()
	{
		return "taobao.wlb.orderschedulerule.delete";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->id,"id");
		RequestCheckUtil::checkNotNull($this->userNick,"userNick");
		RequestCheckUtil::checkMaxLength($this->userNick,64,"userNick");
	}
}
