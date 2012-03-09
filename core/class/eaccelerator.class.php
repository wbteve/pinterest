<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/**  
 * eaccelerator.class.php
 *
 * 内存服务器 eaccelerator 操作类
 *
 * @package class
 * @author awfigq <awfigq@qq.com>
 */
class Eaccelerator
{

	function Eaccelerator()
	{

	}
	
	/**  
	 * 初始化设置 
	 * @param array $config 内存服务器优化设置
	 * @return void
	 */ 
	function init($config)
	{

	}
	
	/**  
	 * 获取缓存内容 
	 * @param string $key 键
	 * @return mixed
	 */
	function get($key)
	{
		return eaccelerator_get($key);
	}
	
	/**  
	 * 将内容写入缓存 
	 * @param string $key 键
	 * @param mixed $value 值
	 * @param int $ttl 在缓存中存储的时间(秒) 默认为0不过期
	 * @return bool
	 */
	function set($key, $value, $ttl = 0)
	{
		return eaccelerator_put($key, $value, $ttl);
	}
	
	/**  
	 * 清除指定缓存
	 * @param string $key 键
	 * @return bool
	 */
	function rm($key)
	{
		return eaccelerator_rm($key);
	}
}
?>