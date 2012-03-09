<?php
/**
 * TOP API: taobao.traderate.list.add request
 * 
 * @author auto create
 * @since 1.0, 2011-11-14 13:22:04
 */
class TraderateListAddRequest
{
	/** 
	 * 是否匿名，卖家评不能匿名。可选值:true(匿名),false(非匿名)。 注意：输入非可选值将会自动转为false；
	 **/
	private $anony;
	
	/** 
	 * 评价内容,最大长度: 500个汉字 .注意：当评价结果为good时就不用输入评价内容.评价内容为neutral/bad的时候需要输入评价内容
	 **/
	private $content;
	
	/** 
	 * 子订单ID
	 **/
	private $oid;
	
	/** 
	 * 评价结果。可选值:good(好评),neutral(中评),bad(差评)
	 **/
	private $result;
	
	/** 
	 * 评价者角色。可选值:seller(卖家),buyer(买家)
	 **/
	private $role;
	
	/** 
	 * 交易ID
	 **/
	private $tid;
	
	private $apiParas = array();
	
	public function setAnony($anony)
	{
		$this->anony = $anony;
		$this->apiParas["anony"] = $anony;
	}

	public function getAnony()
	{
		return $this->anony;
	}

	public function setContent($content)
	{
		$this->content = $content;
		$this->apiParas["content"] = $content;
	}

	public function getContent()
	{
		return $this->content;
	}

	public function setOid($oid)
	{
		$this->oid = $oid;
		$this->apiParas["oid"] = $oid;
	}

	public function getOid()
	{
		return $this->oid;
	}

	public function setResult($result)
	{
		$this->result = $result;
		$this->apiParas["result"] = $result;
	}

	public function getResult()
	{
		return $this->result;
	}

	public function setRole($role)
	{
		$this->role = $role;
		$this->apiParas["role"] = $role;
	}

	public function getRole()
	{
		return $this->role;
	}

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
		return "taobao.traderate.list.add";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->result,"result");
		RequestCheckUtil::checkNotNull($this->role,"role");
		RequestCheckUtil::checkNotNull($this->tid,"tid");
	}
}
