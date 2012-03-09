<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/**
 * 数据库中间层实现类
 *
 * @package class
 * @author awfigq <awfigq@qq.com>
 */
class FDB
{
	/**
     * 创建数据库驱动
     * @access public
     * @param  string $dbclass 数据库驱动类名
     * @return object
     */
	public function &object($dbclass = 'FDbMySql')
	{
		static $db;
		if(empty($db))
			$db = new $dbclass();
		return $db;
	}
	
	/**
     * 获取带前缀的数据表名称
     * @access public
     * @param  string $table 表名
     * @return string
     */
	public function table($table)
	{
		return FDB::_execute('tableName', $table);
	}
	
	/**
     * 删除数据
     * @access public
     * @param  string $table 表名
     * @param  mixed $condition 条件(数组或字符串)
     * @param  int $limit 删除的行数（默认为0，删除所有符合条件的数据行）
     * @param  bool $unbuffered 为 true(默认) 不获取/缓存结果
     * @return mixed
     */
	public function delete($table, $condition, $limit = 0, $unbuffered = true)
	{
		if(empty($condition))
			$where = '1';
		elseif(is_array($condition))
			$where = FDB::implodeFieldValue($condition, ' AND ');
		else
			$where = $condition;
		
		$sql = "DELETE FROM ".FDB::table($table)." WHERE $where ".($limit ? "LIMIT $limit" : '');
		return FDB::query($sql, ($unbuffered ? '' : ''));
	}
	
	/**
     * 添加数据
     * @access public
     * @param  string $table 表名
     * @param  array  $data  数据
     * @param  bool   $return_insert_id 是否返回 INSERT 操作产生的 ID 默认为false不返回
     * @param  bool   $replace 是否为替换操作  默认为false
     * @param  bool   $silent  不显示错误 默认为flase(显示)
     * @return mixed
     */
	public function insert($table, $data, $return_insert_id = false, $replace = false, $silent = false)
	{
		$sql = FDB::implodeFieldValue($data);
		$cmd = $replace ? 'REPLACE INTO' : 'INSERT INTO';
		$table = FDB::table($table);
		$silent = $silent ? 'SILENT' : '';
		$return = FDB::query("$cmd $table SET $sql", $silent);
		return $return_insert_id ? FDB::insertId() : $return;
	}
	
	/**
     * 更新数据
     * @access public
     * @param  string $table 表名
     * @param  array  $data  数据
     * @param  mixed $condition 条件(数组或字符串)
     * @param  bool   $unbuffered 是否不获取/缓存结果   默认false(获取/缓存结果 ) 
     * @param  bool   $low_priority 是否延迟  默认为false
     * @return mixed
     */
	function update($table, $data, $condition, $unbuffered = false, $low_priority = false)
	{
		$sql = FDB::implodeFieldValue($data);
		$cmd = "UPDATE ".($low_priority ? 'LOW_PRIORITY' : '');
		$table = FDB::table($table);
		$where = '';
		if(empty($condition))
			$where = '1';
		elseif(is_array($condition))
			$where = FDB::implodeFieldValue($condition, ' AND ');
		else
			$where = $condition;
		
		$res = FDB::query("$cmd $table SET $sql WHERE $where", $unbuffered ? 'UNBUFFERED' : '');
		return $res;
	}
	
	/**
     * 格式化数组为sql查询
     * @access public
     * @param  array  $array  数据
     * @param  string $glue 间隔符
     * @return string
     */
	public function implodeFieldValue($array, $glue = ',')
	{
		$sql = $comma = '';
		foreach ($array as $k => $v)
		{
			$sql .= $comma."`$k`='$v'";
			$comma = $glue;
		}
		return $sql;
	}
	
	/**
     * 获取 INSERT 操作产生的 ID
     * @return mixed
     */
	public function insertId()
	{
		return FDB::_execute('insertId');
	}
	
	/**
     * 从结果集中取得一行作为关联数组
     * @access public
     * @param  resource $resourceid  结果集
     * @param  string   $type 返回类型 MYSQL_ASSOC、MYSQL_NUM、MYSQL_BOTH，默认为MYSQL_ASSOC(只取得关联索引)
     * @return array
     */
	public function fetch($resourceid, $type = MYSQL_ASSOC)
	{
		return FDB::_execute('fetchArray', $resourceid, $type);
	}
	
	/**
     * 根据查询语句获取第一行数据
     * @access public
     * @param  string $sql  查询语句
     * @return array
     */
	public function fetchFirst($sql)
	{
		FDB::checkQuery($sql);
		return FDB::_execute('fetchFirst', $sql);
	}
	
	/**
     * 根据查询语句获取所有数据
     * @access public
     * @param  string $sql  查询语句
     * @return array
     */
	public function fetchAll($sql)
	{
		FDB::checkQuery($sql);
		return FDB::_execute('fetchAll', $sql);
	}
	
	/**
     * 从结果集中取得指定单元的内容
     * @access public
     * @param  resource $resourceid  结果集
     * @param  int/string $row  单元索引或者字段名称 默认为0(第一个单元)
     * @return mixed
     */
	public function result($resourceid, $row = 0)
	{
		return FDB::_execute('result', $resourceid, $row);
	}
	
	/**
     * 根据查询语句获取第一个单元数据
     * @access public
     * @param  string $sql  查询语句
     * @return mixed
     */
	public function resultFirst($sql)
	{
		FDB::checkQuery($sql);
		return FDB::_execute('resultFirst', $sql);
	}
	
	/**
     * 执行查询
     * @access public
     * @param  string $sql  查询语句
     * @param  string $type
     * @return mixed
     */
	public function query($sql, $type = '')
	{
		FDB::checkQuery($sql);
		return FDB::_execute('query', $sql, $type);
	}
	
	/**
     * 取得结果集行数
     * @access public
     * @param  resource $resourceid  结果集(仅对 SELECT 语句有效)
     * @return int
     */
	public function numRows($resourceid)
	{
		return FDB::_execute('numRows', $resourceid);
	}
	
	
	/**
     * 取得前一次 MySQL 操作所影响的记录行数(INSERT，UPDATE 或 DELETE )
     * @return int
     */
	public function affectedRows()
	{
		return FDB::_execute('affectedRows');
	}
	
	/**
     * 放所有与结果标识符 result 所关联的内存
     * @return bool
     */
	public function freeResult($resourceid)
	{
		return FDB::_execute('freeResult', $resourceid);
	}
	
	/**
	 * 创建像这样的查询: "IN('a','b')";
	 *
	 * @access   public
	 * @param    mix      $item_list      列表数组或字符串
	 * @param    string   $field_name     字段名称
	 * @return   string
	 */
	public function createIN($item_list, $field_name = '')
	{
		if (empty($item_list))
		{
			return $field_name . " IN ('') ";
		}
		else
		{
			if (! is_array($item_list))
			{
				$item_list = explode(',', $item_list);
			}
			$item_list = array_unique($item_list);
			$item_list_tmp = '';
			foreach ($item_list as $item)
			{
				if ($item !== '')
				{
					$item_list_tmp .= $item_list_tmp ? ",'$item'" : "'$item'";
				}
			}
			if (empty($item_list_tmp))
			{
				return $field_name . " IN ('') ";
			}
			else
			{
				return $field_name . ' IN (' . $item_list_tmp . ') ';
			}
		}
	}

	function error()
	{
		return FDB::_execute('error');
	}

	function errno()
	{
		return FDB::_execute('errno');
	}
	
	/**
	 * 执行查询命令
	 *
	 * @access   private
	 * @param    string $cmd 查询命令
	 * @param    mixed $arg1   参数1
	 * @param    mixed $arg2  参数2
	 * @return   mixed
	 */
	private function _execute($cmd , $arg1 = '', $arg2 = '')
	{
		static $db;
		if(empty($db)) $db = & FDB::object();
		$res = $db->$cmd($arg1, $arg2);
		return $res;
	}
	
	/**
	 * 检测查询是否合法
	 *
	 * @access   private
	 * @param    string $sql  查询语句
	 * @return   bool
	 */
	private function checkQuery($sql)
	{
		static $status = null, $checkcmd = array('SELECT', 'UPDATE', 'INSERT', 'REPLACE', 'DELETE');
		
		global $_FANWE;
		
		if($status === null)
			$status = $_FANWE['config']['security']['query']['status'];
		
		if($status)
		{
			$cmd = trim(strtoupper(substr($sql, 0, strpos($sql, ' '))));
			if(in_array($cmd, $checkcmd))
			{
				$test = FDB::_doQuerySafe($sql);
				//if($test < 1)
					//FDB::_execute('halt', 'security_error', $sql);
			}
		}
		return true;
	}
	
	/**
	 * 检测查询
	 *
	 * @access   private
	 * @param    string $sql  查询语句
	 * @return   int
	 */
	private function _doQuerySafe($sql)
	{
		static $_CONFIG = null;
		
		global $_FANWE;
		
		if($_CONFIG === null)
			$_CONFIG = $_FANWE['config']['security']['query'];

		$sql = str_replace(array('\\\\', '\\\'', '\\"', '\'\''), '', $sql);
		$mark = $clean = '';
		if(strpos($sql, '/') === false && strpos($sql, '#') === false && strpos($sql, '-- ') === false) {
			$clean = preg_replace("/'(.+?)'/s", '', $sql);
		} else {
			$len = strlen($sql);
			$mark = $clean = '';
			for ($i = 0; $i <$len; $i++) {
				$str = $sql[$i];
				switch ($str) {
					case '\'':
						if(!$mark) {
							$mark = '\'';
							$clean .= $str;
						} elseif ($mark == '\'') {
							$mark = '';
						}
						break;
					case '/':
						if(empty($mark) && $sql[$i+1] == '*') {
							$mark = '/*';
							$clean .= $mark;
							$i++;
						} elseif($mark == '/*' && $sql[$i -1] == '*') {
							$mark = '';
							$clean .= '*';
						}
						break;
					case '#':
						if(empty($mark)) {
							$mark = $str;
							$clean .= $str;
						}
						break;
					case "\n":
						if($mark == '#' || $mark == '--') {
							$mark = '';
						}
						break;
					case '-':
						if(empty($mark)&& substr($sql, $i, 3) == '-- ') {
							$mark = '-- ';
							$clean .= $mark;
						}
						break;

					default:

						break;
				}
				$clean .= $mark ? '' : $str;
			}
		}
		
		$clean = preg_replace("/[^a-z0-9_\-\(\)#\*\/\"]+/is", "", strtolower($clean));

		if($_CONFIG['fullnote']) {
			$clean = str_replace('/**/','',$clean);
		}

		/*if(is_array($_CONFIG['function'])) {
			foreach($_CONFIG['function'] as $fun) {
				if(strpos($clean, $fun.'(') !== false) return '-1';
			}
		}*/

		if(is_array($_CONFIG['action'])) {
			foreach($_CONFIG['action'] as $action) {
				if(strpos($clean,$action) !== false) return '-3';
			}
		}

		if($_CONFIG['likehex'] && strpos($clean, 'like0x')) {
			return '-2';
		}

		if(is_array($_CONFIG['note'])) {
			foreach($_CONFIG['note'] as $note) {
				if(strpos($clean,$note) !== false) return '-4';
			}
		}

		return 1;

	}
}
?>