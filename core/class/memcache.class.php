<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/**  
 * memcache.class.php
 *
 * 内存服务器 Memcache 操作类
 *
 * @package class
 * @author awfigq <awfigq@qq.com>
 */
class Memcache
{
	var $enable;
	var $obj;

	function Memcache()
	{

	}
	
	/**  
	 * 初始化设置 
	 * @param array $config 内存服务器优化设置
	 * @return void
	 */ 
	function init($config)
	{
		if(!empty($config['server']))
		{
			$this->obj = new Memcache;
			if($config['pconnect'])
			{
				$connect = @$this->obj->pconnect($config['server'], $config['port']);
			}
			else
			{
				$connect = @$this->obj->connect($config['server'], $config['port']);
			}
			
			$this->enable = $connect ? true : false;
		}
	}
	
	/**  
	 * 获取缓存内容 
	 * @param string $key 键
	 * @return mixed
	 */
	function get($key)
	{
		return $this->obj->get($key);
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
		return $this->obj->set($key, $value, MEMCACHE_COMPRESSED, $ttl);
	}

	/**  
	 * 清除指定缓存
	 * @param string $key 键
	 * @return bool
	 */
	function rm($key)
	{
		return $this->obj->delete($key);
	}

}
?>