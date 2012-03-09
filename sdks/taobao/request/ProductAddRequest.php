<?php
/**
 * TOP API: taobao.product.add request
 * 
 * @author auto create
 * @since 1.0, 2011-11-14 13:22:04
 */
class ProductAddRequest
{
	/** 
	 * 非关键属性结构:pid:vid;pid:vid.非关键属性不包含关键属性、销售属性、用户自定义属性、商品属性;调用taobao.itemprops.get获取pid,调用taobao.itempropvalues.get获取vid.<br><font color=red>注:支持最大长度为512字节</font>
	 **/
	private $binds;
	
	/** 
	 * 商品类目ID.调用taobao.itemcats.get获取;注意:必须是叶子类目 id.
	 **/
	private $cid;
	
	/** 
	 * 用户自定义属性,结构：pid1:value1;pid2:value2，如果有型号，系列,货号等用: 隔开 例如：“20000:优衣库:型号:001:632501:1234”，表示“品牌:优衣库:型号:001:货号:1234”
	 **/
	private $customerProps;
	
	/** 
	 * 产品描述.最大25000个字节
	 **/
	private $desc;
	
	/** 
	 * 产品主图片.最大1M,目前仅支持GIF,JPG.
	 **/
	private $image;
	
	/** 
	 * 是不是主图
	 **/
	private $major;
	
	/** 
	 * 上市时间。目前只支持鞋城类目传入此参数
	 **/
	private $marketTime;
	
	/** 
	 * 产品名称,最大60个字节.
	 **/
	private $name;
	
	/** 
	 * 外部产品ID
	 **/
	private $outerId;
	
	/** 
	 * 产品市场价.精确到2位小数;单位为元.如：200.07
	 **/
	private $price;
	
	/** 
	 * 销售属性值别名。格式为pid1:vid1:alias1;pid1:vid2:alia2。只有少数销售属性值支持传入别名，比如颜色和尺寸
	 **/
	private $propertyAlias;
	
	/** 
	 * 关键属性 结构:pid:vid;pid:vid.调用taobao.itemprops.get获取pid,调用taobao.itempropvalues.get获取vid;如果碰到用户自定义属性,请用customer_props.
	 **/
	private $props;
	
	/** 
	 * 销售属性结构:pid:vid;pid:vid.调用taobao.itemprops.get获取is_sale_prop＝true的pid,调用taobao.itempropvalues.get获取vid.
	 **/
	private $saleProps;
	
	private $apiParas = array();
	
	public function setBinds($binds)
	{
		$this->binds = $binds;
		$this->apiParas["binds"] = $binds;
	}

	public function getBinds()
	{
		return $this->binds;
	}

	public function setCid($cid)
	{
		$this->cid = $cid;
		$this->apiParas["cid"] = $cid;
	}

	public function getCid()
	{
		return $this->cid;
	}

	public function setCustomerProps($customerProps)
	{
		$this->customerProps = $customerProps;
		$this->apiParas["customer_props"] = $customerProps;
	}

	public function getCustomerProps()
	{
		return $this->customerProps;
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

	public function setImage($image)
	{
		$this->image = $image;
		$this->apiParas["image"] = $image;
	}

	public function getImage()
	{
		return $this->image;
	}

	public function setMajor($major)
	{
		$this->major = $major;
		$this->apiParas["major"] = $major;
	}

	public function getMajor()
	{
		return $this->major;
	}

	public function setMarketTime($marketTime)
	{
		$this->marketTime = $marketTime;
		$this->apiParas["market_time"] = $marketTime;
	}

	public function getMarketTime()
	{
		return $this->marketTime;
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

	public function setPrice($price)
	{
		$this->price = $price;
		$this->apiParas["price"] = $price;
	}

	public function getPrice()
	{
		return $this->price;
	}

	public function setPropertyAlias($propertyAlias)
	{
		$this->propertyAlias = $propertyAlias;
		$this->apiParas["property_alias"] = $propertyAlias;
	}

	public function getPropertyAlias()
	{
		return $this->propertyAlias;
	}

	public function setProps($props)
	{
		$this->props = $props;
		$this->apiParas["props"] = $props;
	}

	public function getProps()
	{
		return $this->props;
	}

	public function setSaleProps($saleProps)
	{
		$this->saleProps = $saleProps;
		$this->apiParas["sale_props"] = $saleProps;
	}

	public function getSaleProps()
	{
		return $this->saleProps;
	}

	public function getApiMethodName()
	{
		return "taobao.product.add";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkMaxLength($this->binds,512,"binds");
		RequestCheckUtil::checkNotNull($this->cid,"cid");
		RequestCheckUtil::checkNotNull($this->image,"image");
		RequestCheckUtil::checkNotNull($this->name,"name");
		RequestCheckUtil::checkNotNull($this->price,"price");
	}
}
