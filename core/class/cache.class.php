<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/**
 * cache.class.php
 *
 * Cache 处理类
 *
 * @package class
 * @author awfigq <awfigq@qq.com>
 */
class Cache
{
	protected static $_instance = NULL;
	protected $cache_path = '';

	public static function getInstance()
	{
		if(self::$_instance == NULL)
			self::$_instance = new Cache();

		return self::$_instance;
	}

	protected function Cache()
	{
		$this->cache_path = PUBLIC_ROOT.'./data/caches/system/';
	}

	/**
	 * 加载缓存
	 * @param mixed $cache_names 缓存键名
     * @param mixed $cache_names 缓存键名
	 * @param bool  $force
	 * @return mixed
	 */
	public function loadCache($cache_names, $force = false)
	{
		global $_FANWE;
		static $loaded_cache = array();
		$cache_names = is_array($cache_names) ? $cache_names : array($cache_names);
		$caches = array();
		foreach ($cache_names as $k)
		{
			if(!isset($loaded_cache[$k]) || $force)
			{
				$caches[] = $k;
				$loaded_cache[$k] = true;
			}
		}

		if(!empty($caches))
		{
			$cache_data = $this->cacheData($caches);
			foreach($cache_data as $cname => $data)
			{
				if($cname == 'setting')
				{
					$_FANWE['setting'] = $data;
				}
				else
				{
					$_FANWE['cache'][$cname] = $data;
				}
			}
		}
		return true;
	}

	/**
	 * 获取缓存数据
	 * @param mixed $cache_names 缓存键名
	 * @return mixed
	 */
	public function cacheData($cache_names)
	{
		global $_FANWE;
		static $isfile_cache, $allow_memory;

		if($isfile_cache === NULL)
		{
			$isfile_cache = true;
			$allow_memory = $this->memoryHandler('check');
		}

		$data = array();
		$cache_names = is_array($cache_names) ? $cache_names : array($cache_names);
		if($allow_memory)
		{
			$new_array = array();
			foreach ($cache_names as $name)
			{
				$data[$name] = $this->memoryHandler('get', $name);
				if($data[$name] === NULL)
				{
					$data[$name] = NULL;
					$new_array[] = $name;
				}
			}
			if(empty($new_array))
			{
				return $data;
			}
			else
			{
				$cache_names = $new_array;
			}
		}

		if($isfile_cache)
		{
			$lost_caches = array();
			foreach($cache_names as $cache_name)
			{
				if(!@include_once($this->cache_path.$cache_name.'.cache.php'))
				{
					$lost_caches[] = $cache_name;
				}
			}

			if(!$lost_caches)
			{
				return $data;
			}

			$cache_names = $lost_caches;

			unset($lost_caches);
		}

		foreach($cache_names as $name)
		{
			if($data[$name] === NULL)
			{
				$data[$name] = NULL;
				$allow_memory && ($this->memoryHandler('set', $name, array()));
			}
		}

		return $data;
	}

	/**
	 * 保存缓存数据
	 * @param string $cache_name 缓存键名
	 * @param mixed $data 数据
	 * @return void
	 */
	public function saveCache($cache_name, $data)
	{
		global $_FANWE;
		static $isfile_cache = NULL, $allow_memory = NULL;
		if($isfile_cache === NULL)
		{
			$isfile_cache = true;
			$allow_memory = $this->memoryHandler('check');
		}

		$allow_memory && $this->memoryHandler('rm', $cache_name);
		$allow_memory && $this->memoryHandler('set', $cache_name, $data);
		if($isfile_cache)
		{
			$cache_data = "<?php\n".'$data[\''.$cache_name."'] = ".var_export($data, true).";\n\n?>";
			writeFile($this->cache_path.$cache_name.'.cache.php',$cache_data);
		}
	}

	/**
	 * 更新缓存数据
	 * @param string $cache_name 缓存键名 为空则更新所有缓存
	 * @return void
	 */
	public function updateCache($cache_names = '')
	{
		$update_list = empty($cache_names) ? array() : (is_array($cache_names) ? $cache_names : array($cache_names));

		if(!$update_list)
		{
			@require_once fimport('cache/setting');
			bindCacheSetting();
			$cache_dir = FANWE_ROOT.'./core/cache';
			$cache_dir_handle = dir($cache_dir);
			while($entry = $cache_dir_handle->read())
			{
				if(!in_array($entry, array('.', '..')) && preg_match("/^([\w]+)\.cache\.php$/", $entry, $entryr) && $entryr[1] != 'setting' && substr($entry, -10) == '.cache.php' && is_file($cache_dir.'/'.$entry))
				{
					require_once fimport('cache/'.$entryr[1]);
					call_user_func('bindCache'.ucfirst($entryr[1]));
				}
			}
		}
		else
		{
			foreach($update_list as $entry)
			{
				require_once fimport('cache/'.$entry);
				call_user_func('bindCache'.ucfirst($entry));
			}
		}

	}

	/**
	 * 缓存处理
	 * @param string $cmd 操作类型
	 * @param string $key 键名
	 * @param mixed $value 数据
	 * @param int $ttl 缓存时间(秒)
	 * @return mixed
	 */
	public function memoryHandler($cmd, $key='', $value='', $ttl = 0)
	{
		$fanwe = & FanweService::instance();
		if($cmd == 'check')
		{
			return  $fanwe->memory->enable ? $fanwe->memory->type : '';
		}
		elseif($fanwe->memory->enable && in_array($cmd, array('set', 'get', 'rm')))
		{
			switch ($cmd)
			{
				case 'set': return $fanwe->memory->set($key, $value, $ttl); break;
				case 'get': return $fanwe->memory->get($key); break;
				case 'rm': return $fanwe->memory->rm($key); break;
			}
		}
		return NULL;
	}
}
?>