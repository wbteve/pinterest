<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/**  
 * validate.service
 *
 * 数据验证服务类
 *
 * @package service
 * @author awfigq <awfigq@qq.com>
 */
class ValidateService
{
	private $error = '';
	
	/**  
	 * 验证数据 
	 * @param array $validate 验证设置
	 * @param array $data 数据
	 * @return bool
	 */
	public function validation($validate,$data)
	{
		$is_check = true;
		
		foreach($validate as $val)
		{
			$key = $val[0];
			
			switch($val[1])
			{
				case 'min_length': // 验证字符串最小长度
					$is_check = $this->minLength($data[$key] ,$val[3]);
					break;
				case 'max_length': // 验证字符串最大长度
					$is_check = $this->maxLength($data[$key] ,$val[3]);
					break;
				case 'range_length': // 验证字符是否在某个长度范围内
					$is_check = $this->rangeLength($data[$key] ,$val[3],$val[4]);
					break;
				case 'min': // 验证数字最小值
					$is_check = $this->min($data[$key] ,$val[3]);
					break;
				case 'max': // 验证数字最大值
					$is_check = $this->max($data[$key] ,$val[3]);
					break;
				case 'range': // 验证数字是否在某个大小范围内
					$is_check = $this->range($data[$key] ,$val[3],$val[4]);
					break;
				case 'confirm': // 验证两个字段是否相同
					$is_check = $data[$key] == $data[$val[3]];
					break;
				case 'in': // 验证是否在某个数组范围之内
					$is_check = in_array($data[$key] ,$val[3]);
					break;
				case 'equal': // 验证是否等于某个值
					$is_check = $data[$key] == $val[3];
					break;
				case 'qq_msn': // 验证QQ或MSN是否合法
					$is_check = $this->qqMsn($data[$key]);
					break;
				case 'regex':
				default:    // 默认使用正则验证 可以使用验证类中定义的验证名称
					// 检查附加规则
					$is_check = $this->regex($data[$key],$val[1]);
					break;
			}
			
			if(!$is_check)
			{
				$this->error = $val[2];
				break;	
			}
		}
		
        return $is_check;
    }
	
	/**  
	 * 获取错误信息 
	 * @return string
	 */
	public function getError()
	{
		return $this->error;
	}
	
	public function required($value)
	{
		return $this->regex($value,'required');
	}
	
	public function minLength($value,$length)
	{
		return $length <= getStrLen($value);
	}
	
	public function maxLength($value,$length)
	{
		return $length >= getStrLen($value);
	}
	
	public function rangeLength($value,$min_length,$max_length)
	{
		return $min_length <= getStrLen($value) && $max_length >= getStrLen($value);
	}
	
	public function min($value,$num)
	{
		return $num <= $value;
	}
	
	public function max($value,$num)
	{
		return $num >= $value;
	}
	
	public function range($value,$min_num,$max_num)
	{
		return $min_num <= $value && $max_num >= $value;
	}
	
	public function email($value)
	{
		return $this->regex($value,'email');
	}
	
	public function url($value)
	{
		return $this->regex($value,'url');
	}
	
	public function date($value)
	{
		return $this->regex($value,'date');
	}
	
	public function currency($value)
	{
		return $this->regex($value,'currency');
	}
	
	public function digits($value)
	{
		return $this->regex($value,'digits');
	}
	
	public function double($value)
	{
		return $this->regex($value,'double');
	}
	
	public function number($value)
	{
		return $this->regex($value,'number');
	}
	
	public function equal($value,$value1)
	{
		return $value == $value1;
	}
	
	public function qqMsn($value)
	{
		return $this->regex($value,'digits') || $this->regex($value,'email');
	}
	
	public function english($value)
	{
		return $this->regex($value,'english');
	}
	
	public function regex($value,$rule)
	{
        $validate = array(
            'required'=> '/.+/',
            'email' => "/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/i",
            'url' => "/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/i",
			'date' => '/^\d{4}[\/-]\d{1,2}[\/-]\d{1,2}(?:|\s\d{1,2}:\d{1,2}(?:|:\d{1,2}))$/',
			'currency' => '/^\d+(\.\d+)?$/',
            'digits' => '/^\d+$/',
            'number' => '/^-?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/',
            'zip' => '/^[1-9]\d{5}$/',
            'integer' => '/^[-\+]?\d+$/',
            'double' => '/^[-\+]?\d+(\.\d+)?$/',
            'english' => '/^[A-Za-z]+$/',
        );
		
        // 检查是否有内置的正则表达式
        if(isset($validate[strtolower($rule)]))
            $rule = $validate[strtolower($rule)];

        return preg_match($rule,$value)===1;
    }
}
?>