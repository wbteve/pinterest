<?php
//系统转换
class IndexAction extends Action
{
	private function getRealPath()
	{
		return FANWE_ROOT;
	}

	public function __construct()
	{
		import("ORG.Io.Dir");
		parent::__construct();
	}

    public function index()
	{
		clear_cache();
		$note = file_get_contents(FANWE_ROOT.'update/update.txt');
		$this->assign("note",nl2br($note));
		$this->display();
    }

	public function msg($msg)
    {
		$this->assign("msg",$msg);
		$this->display('msg');
		exit;
    }

    public function update()
    {
		$sqls = file_get_contents(FANWE_ROOT.'update/update.sql');
		$sqls = str_replace("\r", '', $sqls);
		$version = explode(";\n", $sqls);
		if(empty($version[0]))
		{
			$this->msg("脚本没有版本号，无法更新");
		}
		else
		{
			$version = $version[0];
			$db = $this->getDB();
			$db_version = $db->query("select val from ".C('DB_PREFIX')."sys_conf where name='SYS_VERSION'");
			$db_version = $db_version[0]['val'];

			if(floatval($db_version) == floatval($version))
			{
				$this->msg("已经是最新版本");
			}

			if(floatval($db_version) > floatval($version))
			{
				$this->msg("不能更新旧版本");
			}
		}

		$this->doupdate();
    }

	public function doupdate()
	{
        @set_time_limit(0);
        if(function_exists('ini_set'))
            ini_set('max_execution_time',3600);

		$this->display("doupdate");
		flush();
		ob_flush();

		showjsmessage('',-1);
		showjsmessage("开始更新分享程序",2);

        usleep(100);

        if($this->restore(FANWE_ROOT."update/update.sql"))
			showjsmessage("更新 数据库 成功");
		else
		{
			showjsmessage("更新 数据库 失败");
			exit;
		}

        showjsmessage("更新成功",4);
		exit;
	}

	public function updatetable()
	{
		set_time_limit(0);

		$tables = array(
            'share'=>'table/share.table.php',
		);

		$table = $_REQUEST['table'];
		$begin = intval($_REQUEST['begin']);
		$begin = max($begin,0);
		$this->display();

		flush();
		ob_flush();

		if(array_key_exists($table,$tables))
		{
			global $db;
			$db = $this->getDB();
			@include FANWE_ROOT.'update/Common/'.$tables[$table];
		}
		else
		{
			showjsmessage("没有此转换数据表",1);
			exit;
		}
	}

	private function getDB()
	{
		static $db = NULL;
		if($db == NULL)
		{
			$db_config['DB_HOST'] = C('DB_HOST');
			$db_config['DB_NAME'] = C('DB_NAME');
			$db_config['DB_USER'] = C('DB_USER');
			$db_config['DB_PWD'] = C('DB_PWD');
			$db_config['DB_PORT'] = C('DB_PORT');
			$db_config['DB_PREFIX'] = C('DB_PREFIX');

			$db = Db::getInstance(array('dbms'=>'mysql','hostname'=>$db_config['DB_HOST'],'username'=>$db_config['DB_USER'],'password'=>$db_config['DB_PWD'],'hostport'=>$db_config['DB_PORT'],'database'=>$db_config['DB_NAME']));
		}

		return $db;
	}

   /**
     * 执行SQL脚本文件
     *
     * @param array $filelist
     * @return string
     */
    private function restore($file)
    {
		$db = $this->getDB();
		$sql = file_get_contents($file);
		$sql = $this->remove_comment($sql);
		$sql = trim($sql);

		$bln = true;

		$tables = array();

		$sql = str_replace("\r", '', $sql);
		$segmentSql = explode(";\n", $sql);
		unset($segmentSql[0]);
		$table = "";

		foreach($segmentSql as $k=>$itemSql)
		{
			$itemSql = trim(str_replace("%DB_PREFIX%",C('DB_PREFIX'),$itemSql));

			if(strtoupper(substr($itemSql, 0, 12)) == 'CREATE TABLE')
			{
				$table = preg_replace("/CREATE TABLE (?:IF NOT EXISTS |)(?:`|)([a-z0-9_]+)(?:`|).*/is", "\\1", $itemSql);

				if(!in_array($table,$tables))
					$tables[] = $table;

				if($db->query($itemSql) === false)
				{
					$bln = false;
					showjsmessage("建立数据表 ".$table." ... 失败",1);
					break;
				}
				else
				{
					showjsmessage("建立数据表 ".$table." ... 成功");
				}
			}
			else
			{
				if($db->query($itemSql) === false)
				{
					$bln = false;
					showjsmessage("执行查询  ".$itemSql." ... 失败",1);
					break;
				}
			}
		}

		return $bln;
    }



    /**
     * 过滤SQL查询串中的注释。该方法只过滤SQL文件中独占一行或一块的那些注释。
     *
     * @access  public
     * @param   string      $sql        SQL查询串
     * @return  string      返回已过滤掉注释的SQL查询串。
     */
    private function remove_comment($sql)
    {
        /* 删除SQL行注释，行注释不匹配换行符 */
        $sql = preg_replace('/^\s*(?:--|#).*/m', '', $sql);

        /* 删除SQL块注释，匹配换行符，且为非贪婪匹配 */
        //$sql = preg_replace('/^\s*\/\*(?:.|\n)*\*\//m', '', $sql);
        $sql = preg_replace('/^\s*\/\*.*?\*\//ms', '', $sql);

        return $sql;
    }
}
?>