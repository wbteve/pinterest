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
 * 
 +------------------------------------------------------------------------------
 */
class CommonAction extends Action
{
	function _initialize()
	{
		/* 对用户传入的变量进行转义操作。*/
		if (MAGIC_QUOTES_GPC)
		{
			if (!empty($_GET))
			{
				$_GET  = stripslashesDeep($_GET);
			}
			if (!empty($_POST))
			{
				$_POST = stripslashesDeep($_POST);
			}
		
			$_COOKIE   = stripslashesDeep($_COOKIE);
			$_REQUEST  = stripslashesDeep($_REQUEST);
		}
		
		$this->assign ('module_name',MODULE_NAME);
		$this->assign ('action_name',ACTION_NAME);
		
		$langSet = C('DEFAULT_LANG');
		// 定义当前语言
		define('FANWE_LANG_SET', strtolower($langSet));
		$this->assign ('default_lang',FANWE_LANG_SET);
		
		// 读取项目公共语言包
		if (is_file(LANG_PATH . $langSet . '/common.php'))
			L(include LANG_PATH . $langSet . '/common.php');
			
		// 读取当前模块语言包
		if (is_file(LANG_PATH . $langSet . '/' . MODULE_NAME . '.php'))
			L(include LANG_PATH . $langSet . '/' . MODULE_NAME . '.php');
			
		$this->assign ('ur_href',L(MODULE_NAME).' > '.L(MODULE_NAME.'_'.ACTION_NAME));
		
		if (Session::isExpired())
		{
			unset($_SESSION[C('USER_AUTH_KEY')]);
			unset($_SESSION);
			session_destroy();
		}
		
		Session::setExpire(time() + fanweC("EXPIRED_TIME") * 60);
		
		// 用户权限检查
		if (C('USER_AUTH_ON') && !in_array(MODULE_NAME,explode(',',C('NOT_AUTH_MODULE'))))
		{
			import ('@.ORG.RBAC');
			if (!RBAC::AccessDecision())
			{
				//检查认证识别号
				if (!$_SESSION [C('USER_AUTH_KEY')])
				{
					//跳转到认证网关
					redirect(PHP_FILE . C('USER_AUTH_GATEWAY'));
				}
				// 没有权限 抛出错误
				if (C('RBAC_ERROR_PAGE'))
				{
					// 定义权限错误页面
					redirect(C('RBAC_ERROR_PAGE'));
				}
				else
				{
					if (C('GUEST_AUTH_ON'))
					{
						$this->assign ('jumpUrl',PHP_FILE . C('USER_AUTH_GATEWAY'));
					}
					
					// 提示错误信息
					if (intval($_REQUEST['ajax']) == 2)
					{
						echo L('_VALID_ACCESS_');
						exit();
					}
					else
					{
						$this->assign("jumpUrl", u("Index/main"));
						$this->error(L('_VALID_ACCESS_'));
					}
				}
			}
		}
	}
	
	public function index() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
		return;
	}
	/**
     +----------------------------------------------------------
	 * 取得操作成功后要返回的URL地址
	 * 默认返回当前模块的默认操作
	 * 可以在action控制器中重载
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return string
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	function getReturnUrl() {
		return __URL__ . '?' . C ( 'VAR_MODULE' ) . '=' . MODULE_NAME . '&' . C ( 'VAR_ACTION' ) . '=' . C ( 'DEFAULT_ACTION' );
	}

	/**
     +----------------------------------------------------------
	 * 根据表单生成查询条件
	 * 进行列表过滤
     +----------------------------------------------------------
	 * @access protected
     +----------------------------------------------------------
	 * @param string $name 数据对象名称
     +----------------------------------------------------------
	 * @return HashMap
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	protected function _search($name = '') {
		//生成查询条件
		if (empty ( $name )) {
			$name = $this->getActionName();
		}
		$name=$this->getActionName();
		$model = D ( $name );
		$map = array ();
		foreach ( $model->getDbFields () as $key => $val ) {
			if (isset ( $_REQUEST [$val] ) && $_REQUEST [$val] != '') {
				$map [$val] = $_REQUEST [$val];
			}
		}
		return $map;

	}

	/**
     +----------------------------------------------------------
	 * 根据表单生成查询条件
	 * 进行列表过滤
     +----------------------------------------------------------
	 * @access protected
     +----------------------------------------------------------
	 * @param Model $model 数据对象
	 * @param HashMap $map 过滤条件
	 * @param string $sortBy 排序
	 * @param boolean $asc 是否正序
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	protected function _list($model, $map, $sortBy = '', $asc = false,$returnUrl = 'returnUrl') {
		//排序字段 默认为主键名
		if (isset ( $_REQUEST ['_order'] )) {
			$order = $_REQUEST ['_order'];
		} else {
			$order = ! empty ( $sortBy ) ? $sortBy : $model->getPk ();
		}
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}
		//取得满足条件的记录数
		$count = $model->where ( $map )->count ($model->getPk ());
	
		if ($count > 0) {
			import ( "@.ORG.Page" );
			//创建分页对象
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $count, $listRows );
			//分页查询数据

			$voList = $model->where($map)->order( "`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->findAll ( );
			
			//echo $model->getlastsql();
			//分页跳转的时候保证查询条件
			foreach ( $map as $key => $val ) {
				if (! is_array ( $val )) {
					$p->parameter .= "$key=" . urlencode ( $val ) . "&";
				}
			}
			//分页显示
			$page = $p->show ();
			//列表排序显示
			$sortImg = $sort; //排序图标
			$sortAlt = $sort == 'desc' ? L('ASC_TITLE') : L('DESC_TITLE'); //排序提示
			$sort = $sort == 'desc' ? 1 : 0; //排序方式
			//模板赋值显示
			$this->assign ( 'list', $voList );
			$this->assign ( 'sort', $sort );
			$this->assign ( 'order', $order );
			$this->assign ( 'sortImg', $sortImg );
			$this->assign ( 'sortType', $sortAlt );
			$this->assign ( "page", $page );
			Cookie::set ('_currentUrl_',$p->currentUrl);
			Cookie::set ($returnUrl,$p->currentUrl);
		}
		else
		{
			Cookie::set ( '_currentUrl_', U($this->getActionName() . "/index"));
			Cookie::set ($returnUrl, U($this->getActionName() . "/index"));
		}
		return;
	}
	
	/**
     +----------------------------------------------------------
	 * 根据查询语句
	 * 进行列表过滤
     +----------------------------------------------------------
	 * @access protected
     +----------------------------------------------------------
	 * @param Model $model 数据对象
	 * @param string $sql 查询语句
	 * @param int $count 数据总量,用于分页
	 * @param array $parameter 分页跳转的时候保证查询条件
	 * @param string $sortBy 排序
	 * @param boolean $asc 是否正序
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	protected function _sqlList($model, $sql, $count, $parameter = array(),$sortBy = '', $asc = false,$returnUrl = 'returnUrl') {
		//排序字段 默认为主键名
		if (isset ( $_REQUEST ['_order'] )) {
			$order = $_REQUEST ['_order'];
		} else {
			$order = ! empty ( $sortBy ) ? $sortBy : $model->getPk ();
		}
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}
	
		if ($count > 0) {
			import ( "@.ORG.Page" );
			//创建分页对象
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $count, $listRows );
			
			//分页查询数据
			if (!empty($order))
				$sql .= ' ORDER BY ' . $order . ' ' . $sort;
			
			$sql .= ' LIMIT ' . $p->firstRow . ',' . $p->listRows;
			
			$voList = $model->query($sql, false);
			
			//echo $model->getlastsql();
			//分页跳转的时候保证查询条件
			foreach ( $parameter as $key => $val ) {
				if (! is_array ( $val )) {
					$p->parameter .= "&$key=" . urlencode ( $val );
				}
			}
			
			//分页显示
			$page = $p->show ();
			//列表排序显示
			$sortImg = $sort; //排序图标
			$sortAlt = $sort == 'desc' ? L('ASC_TITLE') : L('DESC_TITLE'); //排序提示
			$sort = $sort == 'desc' ? 1 : 0; //排序方式
			//模板赋值显示
			$this->assign ( 'list', $voList );
			$this->assign ( 'sort', $sort );
			$this->assign ( 'order', $order );
			$this->assign ( 'sortImg', $sortImg );
			$this->assign ( 'sortType', $sortAlt );
			$this->assign ( "page", $page );
			Cookie::set ('_currentUrl_',$p->currentUrl);
			Cookie::set ($returnUrl,$p->currentUrl);
		}
		else
		{
			Cookie::set ( '_currentUrl_', U($this->getActionName() . "/index"));
			Cookie::set ($returnUrl, U($this->getActionName() . "/index"));
		}
		return;
	}

	function insert() {
		//B('FilterString');
		$name=$this->getActionName();
		$model = D ($name);
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		
		//保存当前数据对象
		$list=$model->add ();
		if ($list!==false) { //保存成功
			$this->saveLog(1,$list);
			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success (L('ADD_SUCCESS'));
		} else {
			//失败提示
			$this->saveLog(0,$list);
			$this->error (L('ADD_ERROR'));
		}
	}

	public function add() {
		$this->display ();
	}

	public function read() {
		$this->edit ();
	}

	public function edit() {
		$name = $this->getActionName();
		$model = D($name);
		
		$id = $_REQUEST [$model->getPk ()];
		$vo = $model->getById($id);
		$this->assign ( 'vo', $vo );
		$this->display ();
	}

	public function update() {
		//B('FilterString');
		$name=$this->getActionName();
		$model = D ( $name );
		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$list=$model->save ();
		$id = $data[$model->getPk()];
		if (false !== $list) {
			//成功提示
			$this->saveLog(1,$id);
			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success (L('EDIT_SUCCESS'));
		} else {
			//错误提示
			$this->saveLog(0,$id);
			$this->error (L('EDIT_ERROR'));
		}
	}
	/**
     +----------------------------------------------------------
	 * 默认删除操作
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return string
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	public function remove() {
		//删除指定记录
		$result = array('isErr'=>0,'content'=>'');
		$id = $_REQUEST['id'];
		if(!empty($id))
		{
			$name=$this->getActionName();
			$model = D($name);
			$pk = $model->getPk ();
			$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
			if(false !== $model->where ( $condition )->delete ())
			{
				$this->saveLog(1,$id);
			}
			else
			{
				$this->saveLog(0,$id);
				$result['isErr'] = 1;
				$result['content'] = L('REMOVE_ERROR');
			}
		}
		else
		{
			$result['isErr'] = 1;
			$result['content'] = L('ACCESS_DENIED');
		}
		
		die(json_encode($result));
	}

	public function editField()
	{
		$id = intval($_REQUEST['id']);
		if($id == 0)
			exit;
		
		$val = trim($_REQUEST['val']);
		if($val == '')
			exit;
			
		$field = trim($_REQUEST['field']);
		if(empty($field))
			exit;
		
		$result = array('isErr'=>0,'content'=>'');
		$name=$this->getActionName();
		$model = D($name);
		$pk = $model->getPk();
		
		$data = array();
		$data[$pk] = $id;
		$data[$field] = $val;
		
		$validation = $model->validationData($data);
		if($validation === true)
		{
			if(false !== $model->save($data))
			{
				$this->saveLog(1,$id,$field);
				$result['content'] = $val;
			}
			else
			{
				$this->saveLog(0,$id,$field);
				$result['isErr'] = 1;
				$result['content'] = L('EDIT_ERROR');
			}
		}
		else
		{
			$this->saveLog(0,$id,$field);
			$result['isErr'] = 1;
			$result['content'] = $validation;
		}
		
		die(json_encode($result));
	}
	
	public function toggleStatus()
	{
		$id = intval($_REQUEST['id']);
		if($id == 0)
			exit;
		
		$val = intval($_REQUEST['val']) == 0 ? 1 : 0;
			
		$field = trim($_REQUEST['field']);
		if(empty($field))
			exit;
		
		$result = array('isErr'=>0,'content'=>'');
		$name=$this->getActionName();
		$model = D($name);
		$pk = $model->getPk();
		if(false !== $model->where($pk.' = '.$id)->setField($field,$val))
		{
			$this->saveLog(1,$id,$field);
			$result['content'] = $val;
		}
		else
		{
			$this->saveLog(0,$id,$field);
			$result['isErr'] = 1;
		}
		
		die(json_encode($result));
	}
	
	//删除图片
	public function deleteImg()
	{
		$id = intval($_REQUEST['id']);
		if($id == 0)
			exit;
		
		$field = trim($_REQUEST['field']);
		if(empty($field))
			exit;
		
		$result = array('isErr'=>0,'content'=>'');
		
		$rel_mod = trim($_REQUEST['rel_mod']);
		if(!empty($rel_mod))
			$name=$rel_mod;
		else
			$name=$this->getActionName();
		
		$model = D($name);
		$pk = $model->getPk();
		$img = $model->where($pk.' = '.$id)->getField($field);
		
		if(!empty($img))
			@unlink(FANWE_ROOT.$img);
		
		if(false !== $model->where($pk.' = '.$id)->setField($field,''))
		{
			$result['content'] = $val;
		}
		else
		{
			$result['isErr'] = 1;
		}
		
		die(json_encode($result));
	}
	
	/**
	 * 上传图片的通公基础方法
	 *
	 * @param integer $water  0:不加水印 1:打印水印
	 * @param string $dir  上传的文件夹
	 * @param bool $is_thumb 是否保存为缩略图
	 * @return array
	 */
	protected function uploadImages($water = 0, $dir = 'images',$is_thumb = false,$whs = array(),$is_swf = false)
	{
		$water_mark = FANWE_ROOT . fanweC("WATER_IMAGE"); //配置于config
		$alpha = fanweC("WATER_ALPHA");
		$place = fanweC("WATER_POSITION");
		$upload = new UploadFile();
		
		//设置上传文件大小
		$max_upload = intval(fanweC('MAX_UPLOAD'));
		
		if($max_upload > 0)
			$upload->maxSize = $max_upload * 1024; /* 配置于config */
		
		//设置上传文件类型
		$upload_exts = fanweC('ALLOW_UPLOAD_EXTS');
		if(!empty($upload_exts))
			$upload->allowExts = explode(',', fanweC('ALLOW_UPLOAD_EXTS')); /* 配置于config */
			
		if($is_swf)
			$upload->allowExts[] = 'swf';
		
		if($is_thumb)
		{
			$upload->thumb = true;
			if($width > 0)
				$upload->thumbMaxWidth = $width;
			else
				$upload->thumbMaxWidth = $width;
		}
		
		if ($is_thumb)
			$save_rec_Path = "./public/upload/" . $dir . "/" . toDate(gmtTime(), 'Ym/d') . "/origin/"; //上传至服务器的相对路径  
		else
			$save_rec_Path = "./public/upload/" . $dir . "/" . toDate(gmtTime(), 'Ym/d') . "/"; //上传至服务器的相对路径  
		
		$save_path = FANWE_ROOT . $save_rec_Path; //绝对路径
		
		if (!is_dir($save_path))
			mk_dir($save_path);
		
		$upload->saveRule = "uniqid"; //唯一
		$upload->savePath = $save_path;
		
		if ($upload->upload())
		{
			$upload_list = $upload->getUploadFileInfo();
			foreach ($upload_list as $k => $file_item)
			{
				if ($is_thumb) //生成缩略图时
				{
					$file_name = $file_item['savepath'] . $file_item['savename']; //上图原图的地址
					
					//开始缩放处理产品大图
					if(isset($whs['big_width']))
						$big_width = $whs['big_width'];
					else
						$big_width = fanweC("BIG_WIDTH");
						
					if(isset($whs['big_height']))
						$big_height = $whs['big_height'];
					else
						$big_height = fanweC("BIG_HEIGHT");
						
					$big_save_path = str_replace("origin", "big", $save_path); //大图存放图径
					
					if (!is_dir($big_save_path))
						mk_dir($big_save_path);
						
					$big_file_name = str_replace("origin", "big", $file_name);
					
					$big_save_path = str_replace("origin", "big", $savePath); //大图存放图径
					if (! is_dir($big_save_path))
					{
						mk_dir($big_save_path);
					}
					$big_file_name = str_replace("origin", "big", $file_name);
					
					if (fanweC("AUTO_GEN_IMAGE") == 1)
						Image::thumb($file_name, $big_file_name, '', $big_width, $big_height);
					else
						@copy($file_name, $big_file_name);
					
					if ($water && file_exists($water_mark))
					{
						Image::water($big_file_name, $water_mark, $big_file_name, $alpha, $place);
					}
					
					//开始缩放处理产品小图
					if(isset($whs['small_width']))
						$small_width = $whs['small_width'];
					else
						$small_width = fanweC("SMALL_WIDTH");
						
					if(isset($whs['small_height']))
						$small_height = $whs['small_height'];
					else
						$small_height = fanweC("SMALL_HEIGHT");
						
					$small_save_path = str_replace("origin", "small", $save_path); //小图存放图径
					
					if (!is_dir($small_save_path))
						mk_dir($small_save_path);

					$small_file_name = str_replace("origin", "small", $file_name);
					Image::thumb($file_name, $small_file_name, '', $small_width, $small_height);
					
					$big_save_rec_Path = str_replace("origin", "big", $save_rec_Path); //大图存放的相对路径
					$small_save_rec_Path = str_replace("origin", "small", $save_rec_Path); //大图存放的相对路径
					
					$upload_list[$k]['recpath'] = $save_rec_Path;
					$upload_list[$k]['big_recpath'] = $big_save_rec_Path;
					$upload_list[$k]['small_recpath'] = $small_save_rec_Path;
				}
				else
				{
					$upload_list[$k]['recpath'] = $save_rec_Path;
					$file_name = $file_item['savepath'] . $file_item['savename'];
					if ($water && file_exists($water_mark))
					{
						Image::water($file_name, $water_mark, $file_name, $alpha, $place);
					}
				}
			}
			
			return $upload_list;
		}
		else
		{
			return false;
		}
	}
	
	//用于日志的记录
	protected function saveLog ($result = '1', $data_id = 0, $msg = '')
	{
		if (fanweC("APP_LOG") == 0)
			return;
		
		$log_app = C("LOG_APP");
		$log_module = MODULE_NAME;
		$log_action = ACTION_NAME;
		
		if (in_array($log_action, $log_app[$log_module]))
		{
			$log_data = array();
			$log_data['log_module'] = $log_module;
			$log_data['log_action'] = $log_action;
			if (!$data_id)
			{
				$pk = M(MODULE_NAME)->getPk();
				$data_id = intval($_REQUEST[$pk]);
			}
			$log_data['data_id'] = $data_id;
			$log_data['log_time'] = gmtTime();
			$log_data['admin_id'] = intval($_SESSION[C("USER_AUTH_KEY")]);
			$log_data['ip'] = getClientIp();
			$log_data['log_result'] = $result;
			$log_data['log_msg'] = $msg;
			D("AdminLog")->add($log_data);
		}
	}
}
?>