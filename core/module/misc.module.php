<?php
class MiscModule
{
	public function verify()
	{
		global $_FANWE;
		
		$seccode = random(6, 1);
		$seccodeunits = '';
		$s = sprintf('%04s', base_convert($seccode, 10, 24));
		$seccodeunits = 'BCEFGHJKMPQRTVWXY2346789';
		
		$seccode = '';
		for($i = 0; $i < 4; $i++)
		{
			$unit = ord($s{$i});
			$seccode .= ($unit >= 0x30 && $unit <= 0x39) ? $seccodeunits[$unit - 0x30] : $seccodeunits[$unit - 0x57];
		}
		
		$rhash = $_FANWE['request']['rhash'];
		
		fSetCookie('verify'.$rhash, authcode(strtoupper($seccode)."\t".(TIME_UTC - 180)."\t".$rhash."\t".FORM_HASH, 'ENCODE', $_FANWE['config']['security']['authkey']), 0, 1, true);
	
		@header("Expires: -1");
		@header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
		@header("Pragma: no-cache");
	
		require fimport('class/verify');
	
		$verify = new Verify();
		$verify->code = $seccode;
		$verify->width = 100;
		$verify->height = 36;
		$verify->display();
	}
}
?>