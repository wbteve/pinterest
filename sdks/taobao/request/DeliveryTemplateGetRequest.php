<?php
/**
 * TOP API: taobao.delivery.template.get request
 * 
 * @author auto create
 * @since 1.0, 2011-11-14 13:22:04
 */
class DeliveryTemplateGetRequest
{
	/** 
	 * 需返回的字段列表。 
可选值:DeliveryTemplate结构体中的所有字段;字段之间用","隔开 
query_cod:查询货到付款运费信息；
query_post:查询平邮运费信息；
query_express:查询快递公司运费信息；
query_ems:查询EMS运费信息
template_id：查询模板ID
template_name:查询模板名称
assumer：查询服务承担放
valuation:查询计价规则
supports:查询增值服务列表
created:查询模板创建时间
modified:查询修改时间<br/><font color=red>
不能有空格</font>
	 **/
	private $fields;
	
	/** 
	 * 需要查询的运费模板ID列表
	 **/
	private $templateIds;
	
	private $apiParas = array();
	
	public function setFields($fields)
	{
		$this->fields = $fields;
		$this->apiParas["fields"] = $fields;
	}

	public function getFields()
	{
		return $this->fields;
	}

	public function setTemplateIds($templateIds)
	{
		$this->templateIds = $templateIds;
		$this->apiParas["template_ids"] = $templateIds;
	}

	public function getTemplateIds()
	{
		return $this->templateIds;
	}

	public function getApiMethodName()
	{
		return "taobao.delivery.template.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->fields,"fields");
		RequestCheckUtil::checkNotNull($this->templateIds,"templateIds");
	}
}
