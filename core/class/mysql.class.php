<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/**
 * mysql.class.php
 * Mysql数据库驱动类
 * @package class
 */
class FDbMySql
{
	var $tablepre;
	var $version = '';
	var $query_num = 0;
	var $curlink;
	var $link = array();
	var $config = array();
	var $sql_debug = array();
	var $map = array();

	function FDbMySql($config = array())
	{
		if(!empty($config))
			$this->setConfig($config);
	}

	function setConfig($config)
	{
		$this->config = &$config;
		$this->tablepre = $config['1']['tablepre'];
		if(!empty($this->config['map']))
		{
			$this->map = $this->config['map'];
		}
	}

	function connect($serverid = 1)
	{
		if(empty($this->config) || empty($this->config[$serverid]))
			$this->halt('config_not_found');

		$this->link[$serverid] = $this->_dbconnect(
			$this->config[$serverid]['dbhost'],
			$this->config[$serverid]['dbuser'],
			$this->config[$serverid]['dbpwd'],
			$this->config[$serverid]['dbcharset'],
			$this->config[$serverid]['dbname'],
			$this->config[$serverid]['pconnect']
		);

		$this->curlink = $this->link[$serverid];
	}

	function _dbConnect($dbhost, $dbuser, $dbpwd, $dbcharset, $dbname, $pconnect)
	{
		$link = null;
		$func = empty($pconnect) ? 'mysql_connect' : 'mysql_pconnect';
		if(!$link = @$func($dbhost, $dbuser, $dbpwd, 1))
			$this->halt('not_connect');
		else
		{
			$this->curlink = $link;
			if($this->version() > '4.1')
			{
				$dbcharset = $dbcharset ? $dbcharset : $this->config[1]['dbcharset'];
				$serverset = $dbcharset ? 'character_set_connection='.$dbcharset.', character_set_results='.$dbcharset.', character_set_client=binary' : '';
				$serverset .= $this->version() > '5.0.1' ? ((empty($serverset) ? '' : ',').'sql_mode=\'\'') : '';
				$serverset && mysql_query("SET $serverset", $link);
			}
			$dbname && @mysql_select_db($dbname, $link);
		}
		return $link;
	}

	function tableName($table_name)
	{
		if(!empty($this->map) && !empty($this->map[$table_name]))
		{
			$id = $this->map[$table_name];
			if(!$this->link[$id])
				$this->connect($id);

			$this->curlink = $this->link[$id];
		}
		else
			$this->curlink = $this->link[1];

		return $this->tablepre.$table_name;
	}

	function selectDb($dbname)
	{
		return mysql_select_db($dbname, $this->curlink);
	}

	function fetchArray($query, $result_type = MYSQL_ASSOC)
	{
		return mysql_fetch_array($query, $result_type);
	}

	function fetchFirst($sql)
	{
		return $this->fetchArray($this->query($sql));
	}

    function fetchAll($sql)
	{
        $res = $this->query($sql);
        if ($res !== false)
        {
            $arr = array();
            while ($row = mysql_fetch_assoc($res))
            {
                $arr[] = $row;
            }

            return $arr;
        }
        else
        {
            return false;
        }
	}

	function resultFirst($sql)
	{
		return $this->result($this->query($sql), 0);
	}

	function query($sql, $type = '')
	{
		if(defined('SYS_DEBUG') && SYS_DEBUG)
		{
			$start_time = fMicrotime();
		}

		$func = $type == 'UNBUFFERED' && @function_exists('mysql_unbuffered_query') ? 'mysql_unbuffered_query' : 'mysql_query';
		if(!($query = $func($sql, $this->curlink)))
		{
			if(in_array($this->errno(), array(2006, 2013)) && substr($type, 0, 5) != 'RETRY')
			{
				$this->connect();
				return $this->query($sql, 'RETRY'.$type);
			}

			if($type != 'SILENT' && substr($type, 5) != 'SILENT')
			{
				$this->halt('query_error', $sql);
			}
		}

		if(defined('SYS_DEBUG') && SYS_DEBUG)
		{
			$this->sql_debug[] = array($sql, number_format((fMicrotime() - $start_time), 6), debug_backtrace());
		}

		$this->query_num++;
		return $query;
	}

	function affectedRows()
	{
		return mysql_affected_rows($this->curlink);
	}

	function error()
	{
		return (($this->curlink) ? mysql_error($this->curlink) : mysql_error());
	}

	function errno()
	{
		return intval(($this->curlink) ? mysql_errno($this->curlink) : mysql_errno());
	}

	function result($query, $row = 0)
	{
		$query = @mysql_result($query, $row);
		return $query;
	}

	function numRows($query)
	{
		$query = mysql_num_rows($query);
		return $query;
	}

	function numFields($query)
	{
		return mysql_num_fields($query);
	}

	function freeResult($resourceid)
	{
		return mysql_free_result($resourceid);
	}

	function insertId()
	{
		return ($id = mysql_insert_id($this->curlink)) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
	}

	function fetchRow($query)
	{
		$query = mysql_fetch_row($query);
		return $query;
	}

	function fetchFields($query) {
		return mysql_fetch_field($query);
	}

	function version()
	{
		if(empty($this->version))
		{
			$this->version = mysql_get_server_info($this->curlink);
		}
		return $this->version;
	}

	function close()
	{
		return mysql_close($this->curlink);
	}

	function halt($message = '', $sql = '')
	{
		require_once fimport('class/error');
		FanweError::dbError($message, $sql);
	}
}
?>