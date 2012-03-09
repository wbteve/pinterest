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
 * 会员
 +------------------------------------------------------------------------------
 */
class UserAction extends CommonAction
{
	public function index()
	{
		$where = '';
		$parameter = array();
		$gid = intval($_REQUEST['gid']);
		$user_name = trim($_REQUEST['user_name']);
		$email = trim($_REQUEST['email']);

		if($gid > 0)
		{
			$this->assign("gid",$gid);
			$where.=" AND gid = $gid";
			$parameter['gid'] = $gid;
		}

		if(!empty($user_name))
		{
			$this->assign("user_name",$user_name);
			$parameter['user_name'] = $user_name;
			$match_key = segmentToUnicodeA($user_name,'+');
			$where.=" AND match(user_name_match) against('".$match_key."' IN BOOLEAN MODE) ";
            $like_name = mysqlLikeQuote($user_name);
            $where .= ' AND user_name LIKE \'%'.$like_name.'%\'';
		}

		if(!empty($email))
		{
			$this->assign("email",$email);
			$parameter['email'] = $email;
			$where .= " AND email = '$email'";
		}

		$model = D('User');

		if(!empty($where))
			$where = 'WHERE 1' . $where;

		$sql = 'SELECT COUNT(uid) AS tcount FROM '.C("DB_PREFIX").'user '.$where;

		$count = $model->query($sql);
		$count = $count[0]['tcount'];

		$sql = 'SELECT * FROM '.C("DB_PREFIX").'user '.$where;
		$this->_sqlList($model,$sql,$count,$parameter);

		$group_list = D("UserGroup")->getField('gid,name');
		$this->assign("group_list",$group_list);
		$this->display();
	}

	public function add()
	{
		$group_list = D("UserGroup")->getField('gid,name');
		$this->assign("group_list",$group_list);

		L(include LANG_PATH . FANWE_LANG_SET . '/UserAuthority.php');
		$authoritys = L('AUTHORITYS');

		$this->assign("authoritys",$authoritys);

		$province_list = D("Region")->where('parent_id = 0')->getField('id,name');
		$province_id = $vo['reside_province'] > 0 ? $vo['reside_province'] : 1;
		$city_list = D("Region")->where('parent_id = '.$province_id)->getField('id,name');
		$this->assign("province_list",$province_list);
		$this->assign("city_list",$city_list);

		$this->display();
	}

	public function insert()
	{
		$name=$this->getActionName();
		$model = D ($name);
		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}
		
		$avatar_img= '';
		if($upload_list = $this->uploadImages())
			$avatar_img = $upload_list[0]['recpath'].$upload_list[0]['savename'];

		$data['user_name_match'] = segmentToUnicodeA($data['user_name']);
		
		Vendor("common");
		//==================添加第三方整合会员添加 chenfq 2011-10-14================
		//第三方整合关联ID,在对应的user表中，要创建应该字段
		if (fanweC('INTEGRATE_CODE') != 'fanwe')
		{		
			$user_field = fanweC('INTEGRATE_FIELD_ID');
			$integrate_id = intval($old_user[$user_field]);
			
			$user_name = $_REQUEST['user_name'];
			$password = $_REQUEST['password'];
			$email = $_REQUEST['email'];
			
			FS("Integrate")->adminInit(fanweC('INTEGRATE_CODE'),fanweC('INTEGRATE_CONFIG'));
			$integrate_id = FS("Integrate")->addUser($user_name,$password,$email);
			//echo $integrate_id; exit;
			if ($integrate_id < 0){
				//失败提示
				$info = FS("Integrate")->getInfo();
				//$this->saveLog(0,$uid);
				$this->error ("整合会员添加返回出错:".$integrate_id.';'.$info);
			}
			
			$data[$user_field] = $integrate_id;
		}
		//==================添加第三方整合会员添加chenfq 2011-10-14================

		//保存当前数据对象
		$uid=$model->add($data);
		if ($uid!==false)
		{
			if(!empty($avatar_img))
				FS('User')->saveAvatar($uid,FANWE_ROOT.$avatar_img);
			
			D('UserCount')->add(array('uid' => $uid));
			
			$user_status = array(
				'uid' => $uid,
				'reg_ip' => getClientIp(),
			);
			D('UserStatus')->add($user_status);

			$data = $_REQUEST['up'];
			$data['uid'] = $uid;

			D('UserProfile')->add($data);

			$access_list = $_REQUEST['access_node'];
			foreach($access_list as $module => $actions)
			{
				$index = 0;
				foreach($actions as $action)
				{
					$item = array();
					$item['uid'] = $uid;
					$item['module'] = $module;
					$item['action'] = $action;
					$item['sort'] = $index++;
					D('UserAuthority')->add($item);
				}
			}

			$this->saveLog(1,$uid);
			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success (L('ADD_SUCCESS'));
		}
		else
		{
			//失败提示
			$this->saveLog(0,$uid);
			$this->error (L('ADD_ERROR'));
		}
	}

	public function edit()
	{
		$uid = intval($_REQUEST['uid']);

		$vo = M()->query('SELECT u.*, uc.*, us.*, up.*
				FROM '.C("DB_PREFIX").'user u
				LEFT JOIN '.C("DB_PREFIX").'user_count uc USING(uid)
				LEFT JOIN '.C("DB_PREFIX").'user_status us USING(uid)
				LEFT JOIN '.C("DB_PREFIX").'user_profile up USING(uid)
				WHERE u.uid= '.$uid);
		if(count($vo) > 0)
			$vo = $vo[0];

		$this->assign ('vo', $vo );

		L(include LANG_PATH . FANWE_LANG_SET . '/UserAuthority.php');
		$authoritys = L('AUTHORITYS');
		$ua_list = array();
		$u_authoritys = D('UserAuthority')->where('uid = '.$uid)->findAll();

		foreach($u_authoritys as $ua)
		{
			$ua_list[$ua['module']][$ua['action']] = 1;
		}

		$this->assign("authoritys",$authoritys);
		$this->assign("ua_list",$ua_list);

		$group_list = D("UserGroup")->getField('gid,name');
		$this->assign("group_list",$group_list);

		$province_list = D("Region")->where('parent_id = 0')->getField('id,name');
		$province_id = $vo['reside_province'] > 0 ? $vo['reside_province'] : 1;
		$city_list = D("Region")->where('parent_id = '.$province_id)->getField('id,name');
		$this->assign("province_list",$province_list);
		$this->assign("city_list",$city_list);
		$this->display();
	}

	public function update()
	{
		$uid = intval($_REQUEST['uid']);
		$name=$this->getActionName();
		$model = D ( $name );
		
		$avatar_img= '';
		if($upload_list = $this->uploadImages())
			$avatar_img = $upload_list[0]['recpath'].$upload_list[0]['savename'];
		
		//if (fanweC('INTEGRATE_CODE') != 'fanwe'){
        //==================添加email,user_name是否允许修改判断chenfq 2011-10-14================
        	Vendor("common");
        	
        	$old_user = $model->getById($uid);
        	//第三方整合关联ID,在对应的user表中，要创建应该字段
        	$user_field = fanweC('INTEGRATE_FIELD_ID');
        	$integrate_id = intval($old_user[$user_field]);
        	
        	$old_user_name = $old_user['user_name'];
        	$old_email = $old_user['email'];
        	
        	$new_user_name = $_REQUEST['user_name'];
        	$new_email = $_REQUEST['email'];
        	
        	//$user_name = $old_user_name;
        	        	
        	if ($old_email == $new_email){
        		$new_email = '';//新旧email一至，无需修改
        	}

        	if ($old_user_name == $new_user_name){
        		$new_user_name = '';//新旧email一至，无需修改
        	}
        	        	
        	
        	if ($_REQUEST['password'] == ''){
        		$new_pwd = '';
        	}else{
        		$new_pwd = $_REQUEST['password'];
        	}
        	
        	if (!empty($new_email) && $integrate_id > 0){
        		FS("Integrate")->adminInit(fanweC('INTEGRATE_CODE'),fanweC('INTEGRATE_CONFIG'));
        		if (!FS("Integrate")->getCheckEmail($new_email,$integrate_id)){
        			
        			$info = FS("Integrate")->getInfo();
        			$this->saveLog(0,$uid);
        			$this->error ($info);
        		}
        	}
        	
        	if (!empty($new_user_name) && $integrate_id > 0){
        		FS("Integrate")->adminInit(fanweC('INTEGRATE_CODE'),fanweC('INTEGRATE_CONFIG'));
        		if (!FS("Integrate")->getCheckUserName($new_user_name,$integrate_id)){        			 
        			$info = FS("Integrate")->getInfo();
        			$this->saveLog(0,$uid);
        			$this->error ($info);
        		}
        	}        	
        	//==================添加email,user_name是否允许修改判断chenfq 2011-10-14================
        //}	

		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}

		if($_REQUEST['password'] == '')
			unset($data['password']);
			
		$data['user_name_match'] = segmentToUnicodeA($data['user_name']);

		// 更新数据
		$list=$model->save($data);
		if (false !== $list)
		{
			if($_REQUEST['delete_avatar'] == 1)
				D('User')->deleteUserAvatar($uid);
				
			if(!empty($avatar_img))
				FS('User')->saveAvatar($uid,FANWE_ROOT.$avatar_img);
			
			D('UserStatus')->where('uid = '.$uid)->save($_REQUEST['us']);
			D('UserProfile')->where('uid = '.$uid)->save($_REQUEST['up']);
			D('UserAuthority')->where('uid = '.$uid)->delete();
			$access_list = $_REQUEST['access_node'];
			foreach($access_list as $module => $actions)
			{
				$index = 0;
				foreach($actions as $action)
				{
					$item = array();
					$item['uid'] = $uid;
					$item['module'] = $module;
					$item['action'] = $action; 
					$item['sort'] = $index++;
					D('UserAuthority')->add($item);
				}
			}

            //if (fanweC('INTEGRATE_CODE') == 'fanwe'){
			//	Vendor("common");
			//}
            
            FS("User")->deleteUserCache($uid);

            //==================添加email,user_name修改chenfq 2011-10-14================
	        if ($integrate_id > 0 && (!empty($new_pwd) || !empty($new_email) || !empty($new_user_name))){
	           FS("Integrate")->adminInit(fanweC('INTEGRATE_CODE'),fanweC('INTEGRATE_CONFIG'));
	           FS("Integrate")->editUser($integrate_id,$new_pwd,$new_email,$new_user_name);
	           $info = FS("Integrate")->getInfo();
	        }
	        //==================添加email,user_name修改chenfq 2011-10-14================

			$this->saveLog(1,$uid);
			$this->assign('jumpUrl', Cookie::get ( '_currentUrl_' ));
			$this->success (L('EDIT_SUCCESS'));
		}
		else
		{
			//错误提示
			$this->saveLog(0,$uid);
			$this->error (L('EDIT_ERROR'));
		}
	}

	public function remove()
	{
		$id = $_REQUEST['id'];
		if(empty($id))
			$this->error(L('SELECT_USER'));
		
		$id_arr = explode(',',$id);
		if(empty($id_arr))
			$this->error(L('SELECT_USER'));
		
		$this->display();
		$condition = array ('uid' => array('in',$id_arr));
		$index = (int)$_REQUEST['index'];
		$count = 100;
		$min = $index * $count;
		$max = $min + $count;
		$type = $_REQUEST['type'];
		switch($type)
		{
			case "album":
				$list = D('Album')->where($condition)->limit('0,'.$count)->findAll();
				if(count($list) > 0)
				{
					echoFlush('<script type="text/javascript">showmessage(\''.sprintf(L('DELETE_TIPS_2'),$min,$max).'\',1);</script>');
					Vendor("common");
					@set_time_limit(0);
					if(function_exists('ini_set'))
						ini_set('max_execution_time',0);
					
					foreach($list as $item)
					{
						FS("Album")->deleteAlbum($item['id'],true);
						usleep(10);
					}
					usleep(100);
					$index++;
					echoFlush('<script type="text/javascript">showmessage(\''.U('User/remove',array('id'=>$id,'type'=>'album','index'=>$index)).'\',2);</script>');
				}
				echoFlush('<script type="text/javascript">showmessage(\''.U('User/remove',array('id'=>$id,'type'=>'share','index'=>0)).'\',2);</script>');
			break;

			case "share":
				$list = D('Share')->where($condition)->limit('0,'.$count)->findAll();
				if(count($list) > 0)
				{
					echoFlush('<script type="text/javascript">showmessage(\''.sprintf(L('DELETE_TIPS_3'),$min,$max).'\',1);</script>');
					Vendor("common");
					@set_time_limit(0);
					if(function_exists('ini_set'))
						ini_set('max_execution_time',0);
					
					foreach($list as $item)
					{
						FS("Share")->deleteShare($item['share_id'],true);
						usleep(10);
					}
					usleep(100);
					$index++;
					echoFlush('<script type="text/javascript">showmessage(\''.U('User/remove',array('id'=>$id,'type'=>'share','index'=>$index)).'\',2);</script>');
				}
				$this->saveLog(1,$id);
				echoFlush('<script type="text/javascript">showmessage(\''.L('DELETE_TIPS_4').'\',3);</script>');
			break;

			default:
				echoFlush('<script type="text/javascript">showmessage(\''.L('DELETE_TIPS_1').'\',1);</script>');
				D('User')->deleteUser($id_arr);
				usleep(500);
				echoFlush('<script type="text/javascript">showmessage(\''.U('User/remove',array('id'=>$id,'type'=>'album','index'=>0)).'\',2);</script>');
			break;
		}
	}

	public function getUserList()
	{
		$key = trim($_REQUEST['key']);
		$where = '';
		if(!empty($key))
        {
			$match_key = segmentToUnicodeA($key,'+');
			$where.=" AND match(user_name_match) against('".$match_key."' IN BOOLEAN MODE) ";
            $like_name = mysqlLikeQuote($key);
            $where .= ' AND user_name LIKE \'%'.$like_name.'%\'';
        }

		$sql = 'SELECT uid,user_name FROM '.C("DB_PREFIX").'user WHERE status = 1 '.$where.' ORDER BY uid DESC limit 0,30';

		$userList = M()->query($sql);
		echo json_encode($userList);
	}
}

function getGroupName($gid)
{
	return D("UserGroup")->where('gid = '.$gid)->getField('name');
}

function getUserAvatar($uid)
{
	$avatar_path = D('User')->getUserAvatarPath($uid);
	$avatar_url = $avatar_path['url'].'_middle.jpg';
	$avatar_path = $avatar_path['path'].'_middle.jpg';
	if(!file_exists($avatar_path))
		$avatar_url = __ROOT__.'/public/upload/avatar/noavatar_middle.jpg';
	return $avatar_url;
}
?>