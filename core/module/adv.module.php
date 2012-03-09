<?php
class AdvModule
{
	public function show()
	{
		global $_FANWE;
		$adv_id = intval($_FANWE['request']['id']);
    	$adv = FDB::fetchFirst('SELECT url FROM '.FDB::table('adv').' WHERE id = '.$adv_id.' AND status = 1');
		
		if($adv)
		{
	    	if(!empty($adv['url']))
	    	{
				fHeader('Location:'.$adv['url']);
	    	}
	    	else
	    	{
	    		fHeader('Location:./');
	    	}
	    }
	    else
	    {
	    	fHeader('Location:./');
	    }
	}
}
?>