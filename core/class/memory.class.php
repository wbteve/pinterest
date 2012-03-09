<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/**  
 * memory.class.php
 *
 * 内存服务器中间层
 *
 * @package class
 * @author awfigq <awfigq@qq.com>
 */
class Memory
{
	var $config;
	var $extension = array();
	var $memory;
	var $prefix;
	var $type;
	var $keys;
	var $enable = false;

	/**  
	 * 构造函数
	 * @return void
	 */ 
	function Memory()
	{
		$this->extension['eaccelerator'] = function_exists('eaccelerator_get');
		$this->extension['apc'] = function_exists('apc_fetch');
		$this->extension['xcache'] = function_exists('xcache_get');
		$this->extension['memcache'] = extension_loaded('memcache');
	}

	/**  
	 * 初始化设置 
	 * @param array $config 内存服务器优化设置
	 * @return void
	 */ 
	function init($config)
	{
		$this->config = $config;
		$this->prefix = empty($config['prefix']) ? substr(md5($_SERVER['HTTP_HOST']), 0, 6).'_' : $config['prefix'];
		$this->keys = array();

		if($this->extension['memcache'] && !empty($config['memcache']['server']))
		{
			require_once fimport('class/memcache');
			$this->memory = new Memcache();
			$this->memory->init($this->config['memcache']);
			if(!$this->memory->enable)
			{
				$this->memory = NULL;
			}
		}

		if(!is_object($this->memory) && $this->extension['eaccelerator'] && $this->config['eaccelerator'])
		{
			require_once fimport('class/eaccelerator');
			$this->memory = new Eaccelerator();
			$this->memory->init(NULL);
		}

		if(!is_object($this->memory) && $this->extension['xcache'] && $this->config['xcache'])
		{
			require_once fimport('class/xcache');
			$this->memory = new Xcache();
			$this->memory->init(NULL);
		}
		
		if(!is_object($this->memory) && $this->extension['apc'] && $this->config['apc'])
		{
			require_once fimport('class/apc');
			$this->memory = new Apc();
			$this->memory->init(null);
		}

		if(is_object($this->memory))
		{
			$this->enable = true;
			$this->type = get_class($this->memory);
			$this->keys = $this->get('memory_system_keys');
			$this->keys = !is_array($this->keys) ? array() : $this->keys;
		}
	}
	
	/**  
	 * 获取缓存内容 
	 * @param string $key 键
	 * @return mixed
	 */
	function get($key)
	{
		$ret = NULL;
		if($this->enable)
		{
			$ret = $this->memory->get($this->_key($key));
			if(!is_array($ret))
			{
				$ret = NULL;
				if(array_key_exists($key, $this->keys))
				{
					unset($this->keys[$key]);
					$this->memory->set($this->_key('memory_system_keys'), array($this->keys));
				}
			}
			else
			{
				return $ret[0];
			}
		}
		return $ret;
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

		$ret = null;
		if($this->enable)
		{
			$ret = $this->memory->set($this->_key($key), array($value), $ttl);
			if($ret)
			{
				$this->keys[$key] = true;
				$this->memory->set($this->_key('memory_system_keys'), array($this->keys));
			}
		}
		return $ret;
	}
	
	/**  
	 * 清除指定缓存
	 * @param string $key 键
	 * @return bool
	 */
	function rm($key)
	{
		$ret = null;
		if($this->enable)
		{
			$ret = $this->memory->rm($this->_key($key));
			unset($this->keys[$key]);
			$this->memory->set($this->_key('memory_system_keys'), array($this->keys));
		}
		return $ret;
	}
	
	/**  
	 * 清除所有缓存
	 * @return bool
	 */
	function clear()
	{
		if($this->enable && is_array($this->keys))
		{
			if(method_exists($this->memory, 'clear'))
			{
				$this->memory->clear();
			}
			else
			{
				$this->keys['memory_system_keys'] = true;
				foreach ($this->keys as $k => $v)
				{
					$this->memory->rm($this->_key($k));
				}
			}
		}
		$this->keys = array();
		return true;
	}
	
	/**  
	 * 获取带前缀的键名
	 * @param string $key 键名
	 * @return mixed
	 */
	function _key($str)
	{
		return ($this->prefix).$str;
	}
}
?>