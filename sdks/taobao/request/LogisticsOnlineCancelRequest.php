<?php
/**
 * TOP API: taobao.logistics.online.cancel request
 * 
 * @author auto create
 * @since 1.0, 2011-11-14 13:22:04
 */
class LogisticsOnlineCancelRequest
{
	/** 
	 * 淘宝交易ID
	 **/
	private $tid;
	
	private $apiParas = array();
	
	public function setTid($tid)
	{
		$this->tid = $tid;
		$this->apiParas["tid"] = $tid;
	}

	public function getTid()
	{
		return $this->tid;
	}

	public function getApiMethodName()
	{
		return "taobao.logistics.online.cancel";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->tid,"tid");
	}
}
