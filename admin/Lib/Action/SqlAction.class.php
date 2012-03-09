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
 * SQL
 +------------------------------------------------------------------------------
 */
class SqlAction extends CommonAction
{
	public function index()
	{
		$tables = $this->getAllTable();
        $this->assign('tables',$tables);
		$this->assign('db_name',C('DB_NAME'));
		$this->display();
	}
	
	public function execute()
	{
		$sql  = trim($_REQUEST['sql']);
		$result = array('status'=>1,'info'=>0,'html'=>'');
		$db = Db::getInstance();
        if(is_string($sql))
		{
			$sql = str_replace("\r", '', $sql);
            $sql = explode(";\n",trim($sql));
        }
		
		$queryIps = 'INSERT|UPDATE|DELETE|REPLACE|CREATE|DROP|LOAD DATA|SELECT .* INTO|COPY|ALTER|GRANT|TRUNCATE|REVOKE|LOCK|UNLOCK';
		
		$start_time = microtime(true);
		$count = 0;
		
        foreach($sql as $query)
		{
			$query = trim($query);
            if(!empty($query))
			{
				if (preg_match('/^\s*"?('.$queryIps.')\s+/i', $query))
				{
					$data = $db->execute($query);
					$type = 'execute';
				}
				else
				{
					$data = $db->query($query);
					$type = 'query';
				}
				
				if(false !== $data)
				{
					if($type == 'query' && !empty($data))
					{
						$count = count($data);
						$fields = array_keys($data[0]);
						$val_list = array();
						foreach($data as $key => $val)
						{
							$val  = array_values($val);
							$val_list[] = $val;
						}
						
						$this->assign('fields',$fields);
						$this->assign('val_list',$val_list);
						$result['html'] = $this->fetch('Sql:table');
					}
				}
				
                if($db->getError() != "")
				{
					$result['status'] = 0;
					$this->assign('msg',$db->getError());
					$result['html'] = $this->fetch('Sql:error');
                }
            }
        }
		
		$run_time = number_format((microtime(true) - $start_time),6);
		if($result['status'] == 1)
			$result['info'] = sprintf(L('SQL_TIPS3'),$count,$run_time);	
		
		die(json_encode($result));
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
}
?>