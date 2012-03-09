<?php
class CommonModel extends Model
{
	function _initialize()
	{
		
	}
	
	public function getById($id)
	{
		$pk = $this->getPk();
		return $this->where($pk.' = '.$id)->find();
	}
	
	public function validationData($data)
	{
		if(!empty($this->_validate))
		{
			foreach($this->_validate as $validate)
			{
				// 判断是否需要执行验证
				if(isset($data[$validate[0]]) && (empty($validate[5]) || $validate[5]== 3 || $validate[5]== 2))
				{
					if(0==strpos($validate[2],'{%') && strpos($validate[2],'}'))
						$validate[2]  =  L(substr($validate[2],2,-1));
					
					$validate[3]  =  isset($validate[3]) ? $validate[3] : 0;
					$validate[4]  =  isset($validate[4]) ? $validate[4] : 'regex';
					switch($validate[3])
					{
						case 2:    // 值不为空的时候才验证
							if('' != trim($data[$validate[0]]))
								if(false === $this->_validationField($data,$validate))
									return $validate[2];
							break;
						default:    // 默认表单存在该字段就验证
							if(false === $this->_validationField($data,$validate))
								return $validate[2];
						break;
					}
				}
			}	
		}
		
		return true;
	}
	
	/**
     +----------------------------------------------------------
     * 根据验证因子验证字段
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param array $data 创建数据
     * @param string $val 验证规则
     +----------------------------------------------------------
     * @return boolean
     +----------------------------------------------------------
     */
    protected function _validationField($data,$val)
	{
        switch($val[4]) {
            case 'function':// 使用函数进行验证
            case 'callback':// 调用方法进行验证
                $args = isset($val[6])?$val[6]:array();
                array_unshift($args,$data[$val[0]]);
                if('function'==$val[4]) {
                    return call_user_func_array($val[1], $args);
                }else{
                    return call_user_func_array(array(&$this, $val[1]), $args);
                }
            case 'confirm': // 验证两个字段是否相同
                return $data[$val[0]] == $data[$val[1]];
            case 'in': // 验证是否在某个数组范围之内
                return in_array($data[$val[0]] ,$val[1]);
            case 'equal': // 验证是否等于某个值
                return $data[$val[0]] == $val[1];
			case 'gt': // 验证是否大于某个值
                return (float)$data[$val[0]] > $val[1];
			case 'lt': // 验证是否小于某个值
                return (float)$data[$val[0]] < $val[1];
            case 'unique': // 验证某个值是否唯一
                if(is_string($val[0]) && strpos($val[0],','))
                    $val[0]  =  explode(',',$val[0]);
                $map = array();
                if(is_array($val[0])) {
                    // 支持多个字段验证
                    foreach ($val[0] as $field)
                        $map[$field]   =  $data[$field];
                }else{
                    $map[$val[0]] = $data[$val[0]];
                }
                if(!empty($data[$this->getPk()])) { // 完善编辑的时候验证唯一
                    $map[$this->getPk()] = array('neq',$data[$this->getPk()]);
                }
                if($this->where($map)->find())
                    return false;
                break;
            case 'regex':
            default:    // 默认使用正则验证 可以使用验证类中定义的验证名称
                // 检查附加规则
                return $this->regex($data[$val[0]],$val[1]);
        }
        return true;
    }
	
	/**
     * 把返回的数据集转换成Tree
     * @access public
     * @param array $list 要转换的数据集
     * @param string $pid parent标记字段
     * @param string $level level标记字段
     * @return array
     */
    public function toTree($list=null, $pk='id',$pid = 'parent_id',$child = '_child')
    {
        if(null === $list)
			return;

        // 创建Tree
        $tree = array();
        if(is_array($list))
		{
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data)
			{
                $_key = is_object($data)?$data->$pk:$data[$pk];
                $refer[$_key] =& $list[$key];
            }            

            foreach ($list as $key => $data)
			{
                // 判断是否存在parent
                $parentId = is_object($data)?$data->$pid:$data[$pid];
                $is_exist_pid = false;
                foreach($refer as $k=>$v)
                {
                	if($parentId==$k)
                	{
                		$is_exist_pid = true;
                		break;
                	}
                }

                if ($is_exist_pid)
				{ 
                    if (isset($refer[$parentId]))
					{
                        $parent =& $refer[$parentId];
                        $parent[$child][] =& $list[$key];
                    }
                }
				else
				{
                    $tree[] =& $list[$key];
                }
            }
        }
        return $tree;
    }
	
	/**
	 * 将格式数组转换为树
	 * @param array $list
	 * @param integer $level 进行递归时传递用的参数
	 * @param string dispname 显示的名称的列的集合
	 */
	private $formatTree; //用于树型数组完成递归格式的全局变量
	private function _toFormatTree($list,$level=0,$dispname_arr=array('title')) 
	{

		  foreach($list as $key=>$val)
		  {
			$tmp_str=str_repeat("&nbsp;&nbsp;",$level*2);
			$tmp_str.="|--";
			foreach($dispname_arr as $dispname)
			{
				$val[$dispname]=$tmp_str."&nbsp;&nbsp;".$val[$dispname];
			}

			$val['level'] = $level;
			if(!array_key_exists('_child',$val))
			{
			   array_push($this->formatTree,$val);
			}
			else
			{
			   $tmp_ary = $val['_child'];
			   unset($val['_child']);
			   array_push($this->formatTree,$val);
			   $this->_toFormatTree($tmp_ary,$level+1,$dispname_arr); //进行下一层递归
			}
		  }
		  return;
	}

	public function toFormatTree($list,$dispname_arr=array('title'),$pk='id',$pid = 'parent_id')
	{
		if(!is_array($dispname_arr))
			$dispname_arr = array($dispname_arr);
			
		$list = $this->toTree($list,$pk,$pid);
		$this->formatTree = array();
		$this->_toFormatTree($list,0,$dispname_arr);
		return $this->formatTree;
	}
	
	//无限递归获取子数据ID集合

	private $childIds;
	private function _getChildIds($pid = '0', $pk_str='id' , $pid_str ='parent_id')
	{
		$childItem_arr = $this->field($pk_str)->where($pid_str."=".$pid)->findAll();
		if($childItem_arr)
		{
			foreach($childItem_arr as $childItem)
			{
				$this->childIds[] = $childItem[$pk_str];
				$this->_getChildIds($childItem[$pk_str],$pk_str,$pid_str);
			}
		}
	}

	public function getChildIds($pid = '0', $pk_str='id' , $pid_str ='parent_id')
	{
		$this->childIds = array();
		$this->_getChildIds($pid,$pk_str,$pid_str);
		return $this->childIds;
	}
}
?>