<?php
class userxypointMapi
{
	public function run()
	{
		global $_FANWE;	
		
		$root = array();
		$root['return'] = 1;

		$email = $_FANWE['requestData']['email'];
		$pwd = $_FANWE['requestData']['pwd'];
		
		$uid = intval(FDB::resultFirst("select uid from ".FDB::table("user")." where user_name='".$email."' and password = '".$pwd."'"));
		
		$latitude = floatval($_FANWE['requestData']['latitude']);//ypoint
		$longitude = floatval($_FANWE['requestData']['longitude']);//xpoint

		
		if ($uid > 0 && $latitude > 0 && $longitude > 0){
			$user_x_y_point = array(
								'uid' => $uid,
								'xpoint' => $longitude,
								'ypoint' => $latitude,							
								'locate_time' => fGmtTime(),
			);
			
			//$root['user_x_y_point'] = $user_x_y_point;
			
			$id = FDB::insert('user_x_y_point',$user_x_y_point,true);
			//FDB::lastSql();
			
			$sql = "update ".FDB::table("user")." set xpoint = $longitude, ypoint = $latitude, locate_time = ".fGmtTime()." where uid = $uid";
			//$root['sql'] = $sql;
			FDB::query($sql);
		}
		
		m_display($root);
	}
}
?>