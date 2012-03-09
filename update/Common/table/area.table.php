<?php
$table = C('DB_PREFIX').'business_circle';
$table_city = C('DB_PREFIX').'group_city';

$citys_path = 'citys.php';
$areas_path = 'areas.php';
include $citys_path;
include $areas_path;

$city_ids = array();
$parent_ids = array();

$index = 1;

$sql = 'INSERT INTO '.$table.'(`id`, `name`, `city_id`, `parent_id`) VALUES ';
$jg = '';
foreach($citys_data as $key => $citys)
{
    $city_id = $db->query('SELECT id FROM '.$table_city.' WHERE name = \''.$key.'\'');
    if(count($city_id) > 0)
        $city_id = $city_id[0]['id'];
    else
        $city_id = 0;

    if($city_id > 0)
    {
        $city_ids[$key] = $city_id;
        foreach($citys as $city)
        {
            $parent_ids[$key][$city] = $index;
            $sql .= $jg."($index,'$city',$city_id,0)";
            $index++;
            $jg=',';
        }
    }
}

foreach($citys_data as $key => $citys)
{
	$city_id = intval($city_ids[$key]);
    if($city_id > 0)
    {
        foreach($citys as $city)
        {
            $parent_id = $parent_ids[$key][$city];
            $areas = $areas_data[$key][$city];
            foreach($areas as $area)
            {
                $sql .= $jg."($index,'$area',$city_id,$parent_id)";
                $index++;
            }
        }
    }
}

$db->query($sql);

showjsmessage("更新完成",4);
exit;
?>