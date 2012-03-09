<?php
class HomeModule
{
	public function me()
	{			
		define("ACTION_NAME","me");
		require fimport('function/user');
		require fimport("function/share");
		if(intval($GLOBALS['fanwe']->var['uid'])==0)
		{			
			fHeader("location: ".FU('user/login'));	
		}
		global $_FANWE;
		$list_html = getMeList();
		
		include template('page/home');
					
		display();			
	}
	
	public function talk()
	{			
		define("ACTION_NAME","talk");
		require fimport('function/user');
		require fimport("function/share");
		if(intval($GLOBALS['fanwe']->var['uid'])==0)
		{			
			fHeader("location: ".FU('user/login'));	
		}
		global $_FANWE;

		$list_html = getTalkList();
		
		include template('page/home');
					
		display();			
	}
	
	public function atme()
	{			
		define("ACTION_NAME","atme");
		require fimport('function/user');
		require fimport("function/share");
		if(intval($GLOBALS['fanwe']->var['uid'])==0)
		{			
			fHeader("location: ".FU('user/login'));	
		}
		global $_FANWE;

		$list_html = getAtmeList();
		
		include template('page/home');
					
		display();			
	}

	public function comments()
	{			
		define("ACTION_NAME","comments");
		require fimport('function/user');
		require fimport("function/share");
		if(intval($GLOBALS['fanwe']->var['uid'])==0)
		{			
			fHeader("location: ".FU('user/login'));	
		}
		global $_FANWE;

		$list_html = getCommentsList();
		
		include template('page/home');
					
		display();			
	}

	public function all()
	{			
		define("ACTION_NAME","all");
		require fimport('function/user');
		require fimport("function/share");
		if(intval($GLOBALS['fanwe']->var['uid'])==0)
		{			
			fHeader("location: ".FU('user/login'));	
		}
		global $_FANWE;

		$list_html = getAllList();
		
		include template('page/home');
					
		display();			
	}	
	
}
?>