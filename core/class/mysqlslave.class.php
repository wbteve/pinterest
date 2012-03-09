<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/**  
 * mysqlslave.class.php
 * Mysql 从服务器 数据库驱动类
 * @package class
 */
class FDbMysqlSlave extends FDbMySql
{
	var $slaveid = null;
	var $slave_query = 0;
	var $slave_except = false;
	var $except_tables = array();

	function setConfig($config)
	{
		parent::setConfig($config);
		if(!empty($this->config['slave']))
		{
			$sid = array_rand($this->config['slave']);
			$this->slaveid = 1000 + $sid;
			$this->config[$this->slaveid] = $this->config['slave'][$sid];

			if($this->config['common']['slave_except_table'])
			{
				$this->except_tables = explode(',', str_replace(' ', '', $this->config['common']['slave_except_table']));
			}
			unset($this->config['slave']);
		}
	}

    function tableName($table_name)
    {
		if($this->slaveid && !$this->slave_except && $this->except_tables)
		{
			if(in_array($table_name, $this->except_tables))
				$this->slave_except = true;
		}
		return parent::tableName($table_name);
	}

	function slaveConnect()
	{
		if($this->slaveid)
		{
			if(!isset($this->link[$this->slaveid]))
				$this->connect($this->slaveid);
			
			$this->slave_query++;
			$this->curlink = $this->link[$this->slaveid];
		}
	}

	function query($sql, $type = '')
	{
		if($this->slaveid && !$this->slave_except && strtoupper(substr($sql, 0 , 6)) == 'SELECT')
			$this->slaveConnect();
		
		$this->slave_except = false;
		return parent::query($sql, $type);
	}
}
?>