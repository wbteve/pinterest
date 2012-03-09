<?php
/**
 * TOP API: taobao.fenxiao.product.add request
 * 
 * @author auto create
 * @since 1.0, 2011-11-14 13:22:04
 */
class FenxiaoProductAddRequest
{
	/** 
	 * 警戒库存必须是0到29999。
	 **/
	private $alarmNumber;
	
	/** 
	 * 所属类目id，参考Taobao.itemcats.get，不支持成人等类目，输入成人类目id保存提示类目属性错误。
	 **/
	private $categoryId;
	
	/** 
	 * 所在地：市，例：“杭州”
	 **/
	private $city;
	
	/** 
	 * 采购价格，单位：元。例：“10.56”。必须在0.01元到10000000元之间。
	 **/
	private $costPrice;
	
	/** 
	 * 产品描述，长度为5到25000字符。
	 **/
	private $desc;
	
	/** 
	 * 折扣ID
	 **/
	private $discountId;
	
	/** 
	 * 是否有保修，可选值：false（否）、true（是），默认false。
	 **/
	private $haveGuarantee;
	
	/** 
	 * 是否有发票，可选值：false（否）、true（是），默认false。
	 **/
	private $haveInvoice;
	
	/** 
	 * 添加产品时，添加入参isAuthz:yes|no 
yes:需要授权 
no:不需要授权 
默认是需要授权
	 **/
	private $isAuthz;
	
	/** 
	 * 产品名称，长度不超过60个字节。
	 **/
	private $name;
	
	/** 
	 * 商家编码，长度不能超过60个字节。
	 **/
	private $outerId;
	
	/** 
	 * ems费用，单位：元。例：“10.56”。 大小为0.00元到999999元之间。
	 **/
	private $postageEms;
	
	/** 
	 * 快递费用，单位：元。例：“10.56”。 大小为0.01元到999999元之间。
	 **/
	private $postageFast;
	
	/** 
	 * 运费模板ID，参考taobao.postages.get。
	 **/
	private $postageId;
	
	/** 
	 * 平邮费用，单位：元。例：“10.56”。 大小为0.01元到999999元之间。
	 **/
	private $postageOrdinary;
	
	/** 
	 * 运费类型，可选值：seller（供应商承担运费）、buyer（分销商承担运费）,默认seller。
	 **/
	private $postageType;
	
	/** 
	 * 产品线ID
	 **/
	private $productcatId;
	
	/** 
	 * 所在地：省，例：“浙江”
	 **/
	private $prov;
	
	/** 
	 * 产品库存必须是1到999999。
	 **/
	private $quantity;
	
	/** 
	 * 最高零售价，单位：元。例：“10.56”。必须在0.01元到10000000元之间，最高零售价必须大于最低零售价。
	 **/
	private $retailPriceHigh;
	
	/** 
	 * 最低零售价，单位：元。例：“10.56”。必须在0.01元到10000000元之间。
	 **/
	private $retailPriceLow;
	
	/** 
	 * 标准价格，单位：元。例：“10.56”。必须在0.01元到10000000元之间。
	 **/
	private $standardPrice;
	
	/** 
	 * 分销方式：AGENT（只做代销，默认值）、DEALER（只做经销）、ALL（代销和经销都做）
	 **/
	private $tradeType;
	
	private $apiParas = array();
	
	public function setAlarmNumber($alarmNumber)
	{
		$this->alarmNumber = $alarmNumber;
		$this->apiParas["alarm_number"] = $alarmNumber;
	}

	public function getAlarmNumber()
	{
		return $this->alarmNumber;
	}

	public function setCategoryId($categoryId)
	{
		$this->categoryId = $categoryId;
		$this->apiParas["category_id"] = $categoryId;
	}

	public function getCategoryId()
	{
		return $this->categoryId;
	}

	public function setCity($city)
	{
		$this->city = $city;
		$this->apiParas["city"] = $city;
	}

	public function getCity()
	{
		return $this->city;
	}

	public function setCostPrice($costPrice)
	{
		$this->costPrice = $costPrice;
		$this->apiParas["cost_price"] = $costPrice;
	}

	public function getCostPrice()
	{
		return $this->costPrice;
	}

	public function setDesc($desc)
	{
		$this->desc = $desc;
		$this->apiParas["desc"] = $desc;
	}

	public function getDesc()
	{
		return $this->desc;
	}

	public function setDiscountId($discountId)
	{
		$this->discountId = $discountId;
		$this->apiParas["discount_id"] = $discountId;
	}

	public function getDiscountId()
	{
		return $this->discountId;
	}

	public function setHaveGuarantee($haveGuarantee)
	{
		$this->haveGuarantee = $haveGuarantee;
		$this->apiParas["have_guarantee"] = $haveGuarantee;
	}

	public function getHaveGuarantee()
	{
		return $this->haveGuarantee;
	}

	public function setHaveInvoice($haveInvoice)
	{
		$this->haveInvoice = $haveInvoice;
		$this->apiParas["have_invoice"] = $haveInvoice;
	}

	public function getHaveInvoice()
	{
		return $this->haveInvoice;
	}

	public function setIsAuthz($isAuthz)
	{
		$this->isAuthz = $isAuthz;
		$this->apiParas["is_authz"] = $isAuthz;
	}

	public function getIsAuthz()
	{
		return $this->isAuthz;
	}

	public function setName($name)
	{
		$this->name = $name;
		$this->apiParas["name"] = $name;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setOuterId($outerId)
	{
		$this->outerId = $outerId;
		$this->apiParas["outer_id"] = $outerId;
	}

	public function getOuterId()
	{
		return $this->outerId;
	}

	public function setPostageEms($postageEms)
	{
		$this->postageEms = $postageEms;
		$this->apiParas["postage_ems"] = $postageEms;
	}

	public function getPostageEms()
	{
		return $this->postageEms;
	}

	public function setPostageFast($postageFast)
	{
		$this->postageFast = $postageFast;
		$this->apiParas["postage_fast"] = $postageFast;
	}

	public function getPostageFast()
	{
		return $this->postageFast;
	}

	public function setPostageId($postageId)
	{
		$this->postageId = $postageId;
		$this->apiParas["postage_id"] = $postageId;
	}

	public function getPostageId()
	{
		return $this->postageId;
	}

	public function setPostageOrdinary($postageOrdinary)
	{
		$this->postageOrdinary = $postageOrdinary;
		$this->apiParas["postage_ordinary"] = $postageOrdinary;
	}

	public function getPostageOrdinary()
	{
		return $this->postageOrdinary;
	}

	public function setPostageType($postageType)
	{
		$this->postageType = $postageType;
		$this->apiParas["postage_type"] = $postageType;
	}

	public function getPostageType()
	{
		return $this->postageType;
	}

	public function setProductcatId($productcatId)
	{
		$this->productcatId = $productcatId;
		$this->apiParas["productcat_id"] = $productcatId;
	}

	public function getProductcatId()
	{
		return $this->productcatId;
	}

	public function setProv($prov)
	{
		$this->prov = $prov;
		$this->apiParas["prov"] = $prov;
	}

	public function getProv()
	{
		return $this->prov;
	}

	public function setQuantity($quantity)
	{
		$this->quantity = $quantity;
		$this->apiParas["quantity"] = $quantity;
	}

	public function getQuantity()
	{
		return $this->quantity;
	}

	public function setRetailPriceHigh($retailPriceHigh)
	{
		$this->retailPriceHigh = $retailPriceHigh;
		$this->apiParas["retail_price_high"] = $retailPriceHigh;
	}

	public function getRetailPriceHigh()
	{
		return $this->retailPriceHigh;
	}

	public function setRetailPriceLow($retailPriceLow)
	{
		$this->retailPriceLow = $retailPriceLow;
		$this->apiParas["retail_price_low"] = $retailPriceLow;
	}

	public function getRetailPriceLow()
	{
		return $this->retailPriceLow;
	}

	public function setStandardPrice($standardPrice)
	{
		$this->standardPrice = $standardPrice;
		$this->apiParas["standard_price"] = $standardPrice;
	}

	public function getStandardPrice()
	{
		return $this->standardPrice;
	}

	public function setTradeType($tradeType)
	{
		$this->tradeType = $tradeType;
		$this->apiParas["trade_type"] = $tradeType;
	}

	public function getTradeType()
	{
		return $this->tradeType;
	}

	public function getApiMethodName()
	{
		return "taobao.fenxiao.product.add";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->alarmNumber,"alarmNumber");
		RequestCheckUtil::checkNotNull($this->categoryId,"categoryId");
		RequestCheckUtil::checkNotNull($this->city,"city");
		RequestCheckUtil::checkNotNull($this->costPrice,"costPrice");
		RequestCheckUtil::checkNotNull($this->desc,"desc");
		RequestCheckUtil::checkNotNull($this->haveGuarantee,"haveGuarantee");
		RequestCheckUtil::checkNotNull($this->haveInvoice,"haveInvoice");
		RequestCheckUtil::checkNotNull($this->name,"name");
		RequestCheckUtil::checkNotNull($this->postageType,"postageType");
		RequestCheckUtil::checkNotNull($this->productcatId,"productcatId");
		RequestCheckUtil::checkNotNull($this->prov,"prov");
		RequestCheckUtil::checkNotNull($this->quantity,"quantity");
		RequestCheckUtil::checkNotNull($this->retailPriceHigh,"retailPriceHigh");
		RequestCheckUtil::checkNotNull($this->retailPriceLow,"retailPriceLow");
		RequestCheckUtil::checkNotNull($this->standardPrice,"standardPrice");
	}
}
