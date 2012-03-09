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
 敏感词
 +------------------------------------------------------------------------------
 */
class WordAction extends CommonAction
{
	public function index()
	{
		$where = '';
		$parameter = array();
		$word = trim($_REQUEST['word']);
		$type = intval($_REQUEST['type']);
		$cid = intval($_REQUEST['cid']);
		
		if(!empty($word))
		{
			$where .= " AND w.word LIKE '%".mysqlLikeQuote($word)."%'";
			$this->assign("word",$word);
			$parameter['word'] = $word;
		}

		if($type > 0)
		{
			$this->assign("type",$type);
			$parameter['type'] = $type;
			$where .= " AND w.type = '$type'";
		}
		else
			$this->assign("type",0);

		if($cid > 0)
		{
			$this->assign("cid",$cid);
			$parameter['cid'] = $cid;
			$where .= " AND w.cid = '$cid'";
		}

		$model = M();
		
		if(!empty($where))
			$where = 'WHERE 1' . $where;
		
		$sql = 'SELECT COUNT(DISTINCT w.id) AS wcount 
			FROM '.C("DB_PREFIX").'word AS w '.$where;

		$count = $model->query($sql);
		$count = $count[0]['wcount'];

		$sql = 'SELECT w.*,wt.name AS cname  
			FROM '.C("DB_PREFIX").'word AS w 
			LEFT JOIN '.C("DB_PREFIX").'word_type AS wt ON wt.id = w.cid '.$where;
		$this->_sqlList($model,$sql,$count,$parameter,'w.id');
		
		$type_list = M("WordType")->where('status = 1')->findAll();
		$this->assign("type_list",$type_list);
		
		$this->display ();
	}
	
	public function add()
	{
		$type_list = M("WordType")->where('status = 1')->findAll();
		$this->assign("type_list",$type_list);
		parent::add();
	}
	
	public function insert()
	{
		$words = trim($_REQUEST['words']);
		$words = explode("\n",$words);
		$obj = array();
		$obj['cid'] = intval($_REQUEST['cid']);
		$obj['type'] = intval($_REQUEST['type']);
		if($obj['type'] == 2)
			$obj['replacement'] = trim($_REQUEST['replacement']);
			
		foreach($words as $word)
		{
			$word = trim($word);
			if(!empty($word))
			{
				$obj['word'] = $word;
				$old = D('Word')->where("word = '$word'")->find();
				if(isset($old['id']))
					D('Word')->where("id = ".intval($old['id']))->save($obj);
				else
					D('Word')->add($obj);
			}
		}
		
		$this->success (L('ADD_SUCCESS'));
	}
	
	public function edit()
	{	
		$type_list = M("WordType")->where('status = 1')->findAll();
		$this->assign("type_list",$type_list);
		parent::edit();
	}
	
	public function update()
	{
		$id = intval($_REQUEST['id']);
		$_REQUEST['word'] = trim($_REQUEST['word']);
		$model = D("Word");
		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}
		
		if($data['type'] != 2)
			$data['replacement'] = '';
		
		// 更新数据
		$list=$model->save($data);
		if (false !== $list)
		{
			$this->assign('jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success (L('EDIT_SUCCESS'));
		}
		else
		{
			$this->error (L('EDIT_ERROR'));
		}
	}
	
	public function import()
	{
		$type_list = M("WordType")->where('status = 1')->findAll();
		$this->assign("type_list",$type_list);
		$this->display();
	}
	
	public function save()
	{
		$source = FANWE_ROOT."public/upload/temp/word.txt";
		if(!isset($_FILES['word_file']))
		{
			$this->error (L('WORD_FILE_EMPTY'));
		}
		elseif($_FILES['word_file']['tmp_name'] == 'none')
		{
			$this->error (L('WORD_FILE_ERROR'));
		}
		elseif(!move_uploaded_file($_FILES['word_file']['tmp_name'],$source))
		{
			$this->error (L('WORD_FILE_ERROR'));
		}
		
		$words = file_get_contents($source);
		$words = explode("\n",$words);
		
		$obj = array();
		$obj['cid'] = intval($_REQUEST['cid']);
		$obj['type'] = intval($_REQUEST['type']);
		if($obj['type'] == 2)
			$obj['replacement'] = trim($_REQUEST['replacement']);
			
		foreach($words as $word)
		{
			$word = trim($word);
			if(!empty($word))
			{
				$word = explode("|",$word);
				$obj['word'] = trim($word[0]);
				if(!empty($obj['word']))
				{
					if(count($word) > 1)
					{
						$obj['type'] = intval($word[1]);
						if($obj['type'] == 2 && isset($word[2]))
							$obj['replacement'] = trim($word[2]);
					}
					
					$old = D('Word')->where("word = '".addslashes($word)."'")->find();
					if(isset($old['id']))
						D('Word')->where("id = ".intval($old['id']))->save($obj);
					else
						D('Word')->add($obj);
				}
			}
		}
		
		$this->success (L('IMPORT_SUCCESS'));
	}
	
	public function export()
	{
		$word_file = 'word.txt';
		$word_content = '';
		$words = D('Word')->where("status = 1")->findAll();
		foreach($words as $word)
		{
			switch($word['type'])
			{
				case 1:
					$word_content .= $word['word']."|".$word['type']."\n";
				break;
				
				case 2:
					$word_content .= $word['word']."|".$word['type']."|".$word['replacement']."\n";
				break;	
			}
		}
		
		$word_content = trim($word_content);
		$time = gmtTime();

		header('Last-Modified: '.gmdate('D, d M Y H:i:s',$time).' GMT');
		header('Cache-control: no-cache');
		header('Content-Encoding: none');
		header('Content-Disposition: attachment; filename="'.$word_file.'"');
		header('Content-type: txt');
		header('Content-Length: '.strlen($word_content));
		echo $word_content;
		exit;
	}
}

function getTypeName($type)
{
	return L("TYPE_".intval($type));	
}
?>