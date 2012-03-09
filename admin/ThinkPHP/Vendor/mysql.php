<?php
/**  
 * Mysql数据库驱动类
 * @package class
 */
class mysqldb
{
	var $tablepre;
	var $version = '';
	var $query_num = 0;
	var $link;
	var $config = array();

	function mysqldb($config = array())
	{
		if(!empty($config))
			$this->setConfig($config);
	}

	function setConfig($config)
	{
		$this->config = &$config;
		$this->tablepre = $config['tablepre'];
		$this->connect();
	}

	function connect()
	{
		if(empty($this->config))
			$this->halt('config_not_found');
		
		$this->link = $this->_dbconnect(
			$this->config['dbhost'],
			$this->config['dbuser'],
			$this->config['dbpwd'],
			$this->config['dbcharset'],
			$this->config['dbname'],
			$this->config['pconnect']
		);
	}

	function _dbConnect($dbhost, $dbuser, $dbpwd, $dbcharset, $dbname, $pconnect)
	{
		$link = null;
		$func = empty($pconnect) ? 'mysql_connect' : 'mysql_pconnect';
		if(!$link = @$func($dbhost, $dbuser, $dbpwd, 1))
			$this->halt('not_connect');
		else
		{
			$this->link = $link;
			if($this->version() > '4.1')
			{
				$dbcharset = $dbcharset ? $dbcharset : $this->config['dbcharset'];
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
		return $this->tablepre.$table_name;
	}

	function selectDb($dbname)
	{
		return mysql_select_db($dbname, $this->link);
	}

	function fetchArray($query, $result_type = MYSQL_ASSOC)
	{
		return mysql_fetch_array($query, $result_type);
	}

	function fetchFirst($sql)
	{
		return $this->fetchArray($this->query($sql));
	}

	function resultFirst($sql)
	{
		return $this->result($this->query($sql), 0);
	}

	function query($sql, $type = '')
	{
		$func = $type == 'UNBUFFERED' && @function_exists('mysql_unbuffered_query') ? 'mysql_unbuffered_query' : 'mysql_query';
		
		if(!($query = $func($sql, $this->link)))
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

		$this->query_num++;
		return $query;
	}

	function affectedRows()
	{
		return mysql_affected_rows($this->link);
	}

	function error()
	{
		return (($this->link) ? mysql_error($this->link) : mysql_error());
	}

	function errno()
	{
		return intval(($this->link) ? mysql_errno($this->link) : mysql_errno());
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
		return ($id = mysql_insert_id($this->link)) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
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
			$this->version = mysql_get_server_info($this->link);
		}
		return $this->version;
	}

	function close()
	{
		return mysql_close($this->link);
	}

	function halt($message = '', $sql = '')
	{
		echo $message ."\r\n".$sql;
		exit;
	}
}
?>