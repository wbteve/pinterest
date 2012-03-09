<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: awfigq <awfigq@qq.com>
// +----------------------------------------------------------------------
/**
 +------------------------------------------------------------------------------
 * 数据备份
 +------------------------------------------------------------------------------
 */
class DataBaseAction extends CommonAction
{
	public function index()
	{
		$db_back_dir = FANWE_ROOT."public/db_backup/";
		$sql_list = $this->dirFileInfo($db_back_dir);
		$this->assign("sql_list",$sql_list);
		$this->assign("name",toDate(gmtTime(),'YmdHis'));
		$this->display();
	}
	
	public function dump()
	{
		$time = gmtTime();
		$name = empty($_REQUEST['sql_file_name']) ? toDate($time,'YmdHis') : $_REQUEST['sql_file_name'];
		$tables = $this->getAllTable();
		
		$_SESSION['dump_table_data'] = array(
			'file_dir'=>$name,
			'tables'=>$tables,
			'perfix'=>C('DB_PREFIX'),
			'time'=>$time,
		);
		
		$this->redirect('DataBase/dumptable');
	}

	public function dumptable()
	{
		$table_data = $_SESSION['dump_table_data'];
		if(empty($table_data))
			$this->redirect('DataBase/index');
		else
			$_SESSION['dump_table_data'] = $table_data;
			
		@set_time_limit(3600);
		
		if(function_exists('ini_set'))
		{
			ini_set('max_execution_time',3600);
			ini_set("memory_limit","256M");
		}

		$begin = isset($_REQUEST['begin']) ? intval($_REQUEST['begin']) : 0;
		$index = isset($_REQUEST['index']) ? intval($_REQUEST['index']) : 0;
		
		$back_dir = FANWE_ROOT."public/db_backup/".$table_data['file_dir'].'/';

		if($index >= count($table_data['tables']))
		{
			$this->assign("tables",false);
			$this->display();
			ob_start();
			ob_end_flush(); 
			ob_implicit_flush(1);
			
			unset($_SESSION['dump_table_data']);
			echoFlush('<script type="text/javascript">showmessage(\''.L('DUMP_SUCCESS').'\',3);</script>');
			exit;
		}

		$table = $table_data['tables'][$index];
		$table_vars = array(
			'count'=>count($table_data['tables']),
			"index"=>$index + 1,
			"name"=>$table);

		$this->assign("tables",$table_vars);
		$this->display();
		
		ob_start();
		ob_end_flush(); 
		ob_implicit_flush(1);

		if($index == 0)
		{
			mk_dir($back_dir);
            $table_data = '$table_data = '.var_export($table_data, true).";";
			$db_table_file = $back_dir."tables.php";
			@file_put_contents($db_table_file,"<?php\n$table_data\n?>");
		}

		$tbname = 	str_replace(C('DB_PREFIX'),'%DB_PREFIX%',$table);
		$modelname = str_replace(C('DB_PREFIX'),'',$table);
		$table_dir = $back_dir.$modelname.'/';
		mk_dir($table_dir);
		$modelname = parse_name($modelname,1);
		$model=D($modelname);

		$data_num = $model->count();
		$dumpsql_vol = '';
		
		if($begin == 0)
		{
			 $sql_file_path = $table_dir."table.sql";
			 $dumpsql_vol .= "DROP TABLE IF EXISTS `$tbname`;\r\n";  //用于表结构导出处理的Sql语句
		 	 $tmp_arr = M()->query("SHOW CREATE TABLE `$table`");
		     $tmp_sql = $tmp_arr[0]['Create Table'].";\r\n";
		     $tmp_sql  = str_replace(C('DB_PREFIX'),'%DB_PREFIX%',$tmp_sql);
			 $dumpsql_vol .= $tmp_sql;   //表结构语句处理结束
			 if(@file_put_contents($sql_file_path,$dumpsql_vol) === false)
			 {
				echoFlush('<script type="text/javascript">showmessage(\''.sprintf(L('DUMP_TIPS2'),$table,U('DataBase/dumptable',array('index'=>$index,'begin'=>$begin))).'\',-1);</script>');
				exit;
			 }
			 else
			 {
				echoFlush('<script type="text/javascript">showmessage(\''.sprintf(L('DUMP_TIPS3'),$table).'\',1);</script>');
			 }
		}

		if($data_num > $begin)
		{
			$sql_file_path = $table_dir.$begin.".sql";
			$dumpsql_vol = '';
			$limit = $data_num - $begin;
            if($limit > 5000)
                $limit = 5000;

			echoFlush('<script type="text/javascript">showmessage(\''.sprintf(L('DUMP_TIPS4'),$table,$begin,$begin + $limit).'\',1);</script>');

			$data_list=$model->limit($begin.','.$limit)->findAll();
			foreach($data_list as $data_row)
			{
				 $dumpsql_row = "INSERT INTO `{$tbname}` VALUES (";   //用于每行数据插入的SQL脚本语句
				 foreach($data_row as $col_value)
				 {
				   $dumpsql_row .="'".mysql_real_escape_string($col_value)."',";
				 }
				 $dumpsql_row=substr($dumpsql_row,0,-1);  //删除最后一个逗号
				 $dumpsql_row .= ");\r\n";
				 $dumpsql_vol.= $dumpsql_row;
			}

			if(@file_put_contents($sql_file_path,$dumpsql_vol) === false)
			{
				echoFlush('<script type="text/javascript">showmessage(\''.sprintf(L('DUMP_TIPS5'),$table,$begin,$begin + $limit,U('DataBase/dumptable',array('index'=>$index,'begin'=>$begin))).'\',-1);</script>');
				exit;
			}
			else
			{
				if($limit < 5000)
				{
					echoFlush('<script type="text/javascript">showmessage(\''.U('DataBase/dumptable',array('index'=>$index + 1,'begin'=>0)).'\',2);</script>');
    				exit;
				}
				else
				{
					echoFlush('<script type="text/javascript">showmessage(\''.U('DataBase/dumptable',array('index'=>$index,'begin'=>$begin + $limit)).'\',2);</script>');
					exit;
				}
			}
		}
		else
		{
			echoFlush('<script type="text/javascript">showmessage(\''.U('DataBase/dumptable',array('index'=>$index + 1,'begin'=>0)).'\',2);</script>');
    		exit;
		}
	}

	public function delete()
	{
		$dir = $_REQUEST['dir'];
		if(empty($dir))
			exit;
		$_SESSION['delete_table_dir'] = $dir;
		$this->redirect('DataBase/deletetable');
	}

	public function deletetable()
	{
		$name = $_SESSION['delete_table_dir'];
		if(empty($name))
			$this->redirect('DataBase/index');
		else
			$_SESSION['delete_table_dir'] = $name;
			
		@set_time_limit(3600);
		if(function_exists('ini_set'))
		{
			ini_set('max_execution_time',3600);
			ini_set("memory_limit","256M");
		}

		$this->display();
		ob_start();
		ob_end_flush(); 
		ob_implicit_flush(1);
		
		echoFlush('<script type="text/javascript">showmessage(\''.sprintf(L('DELETE_TIPS1'),$name).'\',1);</script>');
		
		$dir = FANWE_ROOT."public/db_backup/".$name.'/';
		$dirhandle=opendir($dir);
		while(($file = readdir($dirhandle)) !== false)
		{
			if(($file!=".") && ($file!=".."))
			{
				if(is_dir($dir.$file))
				{
					echoFlush('<script type="text/javascript">showmessage(\''.sprintf(L('DELETE_TIPS2'),$file).'\',1);</script>');
                    usleep(10);
					$this->clearSqlDir($dir.$file.'/',$file);
					@rmdir($dir.$file.'/');
				}
				else
				{
					@unlink($dir.$file);
				}
			}
		}

		@closedir($dirhandle);
		usleep(10);
		@rmdir($dir);
		echoFlush('<script type="text/javascript">showmessage(\''.sprintf(L('DELETE_TIPS4'),$name).'\',3);</script>');
		exit;
	}

	public function restore()
	{
		$dir = $_REQUEST['dir'];
		if(empty($dir))
			exit;
		$_SESSION['restore_table_dir'] = $dir;
		$this->redirect('DataBase/restoretable');
	}

	public function restoretable()
	{
		$restore_table_dir = $_SESSION['restore_table_dir'];
		$back_dir = FANWE_ROOT."public/db_backup/".$restore_table_dir.'/';
		if(!@include($back_dir."tables.php"))
			$this->redirect('DataBase/index');
		else
			$_SESSION['restore_table_dir'] = $restore_table_dir;
			
		@set_time_limit(3600);
		if(function_exists('ini_set'))
		{
			ini_set('max_execution_time',3600);
			ini_set("memory_limit","256M");
		}

		$begin = isset($_REQUEST['begin']) ? intval($_REQUEST['begin']) : 0;
		$index = isset($_REQUEST['index']) ? intval($_REQUEST['index']) : 0;
		
		$this->assign("restore_tips",sprintf(L('RESTORE_TIPS0'),U('DataBase/restoretable',array('index'=>$index,'begin'=>0))));

		if($index >= count($table_data['tables']))
		{
			$this->assign("tables",false);
			$this->display();
			ob_start();
			ob_end_flush(); 
			ob_implicit_flush(1);

			echoFlush('<script type="text/javascript">showmessage(\''.L('RESTORE_SUCCESS').'\',3);</script>');
			exit;
		}

		$table = $table_data['tables'][$index];
		$table = str_replace($table_data['perfix'],'',$table);
		$table_dir = $back_dir.$table.'/';

		$table_vars = array(
			'count'=>count($table_data['tables']),
			"index"=>$index + 1,
			"name"=>$table);

		$this->assign("tables",$table_vars);
		$this->display();
		ob_start();
		ob_end_flush(); 
		ob_implicit_flush(1);
		
		if(!file_exists($table_dir.'table.sql'))
		{
			echoFlush('<script type="text/javascript">showmessage(\''.U('DataBase/restoretable',array('index'=>$index + 1,'begin'=>0)).'\',2);</script>');
    		exit;
		}

		$db = Db::getInstance();

		if($begin == 0)
		{
			 $sql = @file_get_contents($table_dir.'table.sql');
			 $sql = str_replace("\r", '', $sql);
			 $segmentSql = explode(";\n", $sql);

			 foreach($segmentSql as $itemSql)
			 {
				 $itemSql = trim($itemSql);
				 if(empty($itemSql))
				 	continue;

				 $itemSql = str_replace("%DB_PREFIX%",C('DB_PREFIX'),$itemSql);
				 $db->query($itemSql);
				 if($db->getError() != "")
				 {
					 echoFlush('<script type="text/javascript">showmessage(\''.sprintf(L('RESTORE_TIPS2'),$table,U('DataBase/restoretable',array('index'=>$index,'begin'=>0))).'\',-1);</script>');
					 exit;
				 }
			 }

			 echoFlush('<script type="text/javascript">showmessage(\''.sprintf(L('RESTORE_TIPS3'),$table).'\',1);</script>');
		}

		if(file_exists($table_dir.$begin.'.sql'))
		{
			$limit = 5000;
			echoFlush('<script type="text/javascript">showmessage(\''.sprintf(L('RESTORE_TIPS4'),$table,$begin,($begin + $limit)).'\',1);</script>');

			$sql = @file_get_contents($table_dir.$begin.'.sql');
			$sql = str_replace("\r", '', $sql);
			$segmentSql = explode(";\n", $sql);
			$sql_index = 0;
			foreach($segmentSql as $itemSql)
			{
				$sql_index++;
				
				if(!empty($itemSql))
				{
					$itemSql = str_replace("%DB_PREFIX%",C('DB_PREFIX'),$itemSql);
					$db->query($itemSql);
					if($db->getError() != "")
					{
						echoFlush('<script type="text/javascript">showmessage(\''.sprintf(L('RESTORE_TIPS5'),$table,$sql_index,U('DataBase/restoretable',array('index'=>$index,'begin'=>0))).'\',-1);</script>');
					 	exit;
					}
				}
			}
			
			echoFlush('<script type="text/javascript">showmessage(\''.U('DataBase/restoretable',array('index'=>$index,'begin'=>$begin + $limit)).'\',2);</script>');
			exit;
		}
		else
		{
			echoFlush('<script type="text/javascript">showmessage(\''.U('DataBase/restoretable',array('index'=>$index + 1,'begin'=>0)).'\',2);</script>');
    		exit;
		}
	}
	
	private function getAllTable()
	{
		$tables_all = Db::getInstance()->getTables(); 
		$tables = array();
		foreach($tables_all as $table)
		{
			if(preg_match("/".C('DB_PREFIX')."/",$table))
				array_push($tables,$table);
		}
		return $tables;
	}
	
	private function dirFileInfo($dir)
	{
		if(!is_dir($dir))
			return false;
		
		$dirhandle=opendir($dir);
		$list=array();
		while(($file = readdir($dirhandle)) !== false)
		{
			if(($file!=".") && ($file!="..") && is_dir($dir.$file) && file_exists($dir.$file.'/tables.php'))
			{
				include $dir.$file.'/tables.php';
				$list[]=array(
					'filename'=>$table_data['file_dir'],
					'filetime'=>$table_data['time'],
					'filedate'=>toDate($table_data['time'])
				);
			}
		}
		@closedir($dirhandle);
		usort($list,fileSort);
		return $list;
   }
   
	private function clearSqlDir($dir,$name)
	{
		$dirhandle=opendir($dir);
		while(($file = readdir($dirhandle)) !== false)
		{
			if(($file!=".") && ($file!=".."))
			{
				echoFlush('<script type="text/javascript">showmessage(\''.sprintf(L('DELETE_TIPS3'),$name,$file).'\',1);</script>');
				usleep(10);
				@unlink($dir.$file);
			}
		}
		@closedir($dirhandle);
	}
}

function fileSort($a, $b)
{
	if ($a['filetime'] == $a['filetime'])
        return 0;

    return ($a['filetime'] < $a['filetime']) ? 1 : -1;
}
?>