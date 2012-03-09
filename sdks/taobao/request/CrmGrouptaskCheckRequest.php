<?php
/**
 * TOP API: taobao.crm.grouptask.check request
 * 
 * @author auto create
 * @since 1.0, 2011-11-14 13:22:04
 */
class CrmGrouptaskCheckRequest
{
	/** 
	 * 分组id
	 **/
	private $groupId;
	
	private $apiParas = array();
	
	public function setGroupId($groupId)
	{
		$this->groupId = $groupId;
		$this->apiParas["group_id"] = $groupId;
	}

	public function getGroupId()
	{
		return $this->groupId;
	}

	public function getApiMethodName()
	{
		return "taobao.crm.grouptask.check";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->groupId,"groupId");
	}
}
