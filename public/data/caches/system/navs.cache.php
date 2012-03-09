<?php
$data['navs'] = array (
  'all' => 
  array (
    1 => 
    array (
      'id' => '1',
      'parent_id' => '0',
      'name' => '主导航',
      'sort' => '100',
      'status' => '1',
      'is_fix' => '1',
    ),
    2 => 
    array (
      'id' => '2',
      'parent_id' => '0',
      'name' => '底部导航',
      'sort' => '100',
      'status' => '1',
      'is_fix' => '1',
      'childs' => 
      array (
        0 => '4',
        1 => '5',
        2 => '6',
      ),
    ),
    3 => 
    array (
      'id' => '3',
      'parent_id' => '0',
      'name' => '固定链接',
      'sort' => '100',
      'status' => '1',
      'is_fix' => '1',
    ),
    4 => 
    array (
      'id' => '4',
      'parent_id' => '2',
      'name' => '网站',
      'sort' => '100',
      'status' => '1',
      'is_fix' => '0',
    ),
    5 => 
    array (
      'id' => '5',
      'parent_id' => '2',
      'name' => '团队',
      'sort' => '100',
      'status' => '1',
      'is_fix' => '0',
    ),
    6 => 
    array (
      'id' => '6',
      'parent_id' => '2',
      'name' => '帮助',
      'sort' => '100',
      'status' => '1',
      'is_fix' => '0',
    ),
  ),
  'root' => 
  array (
    0 => '1',
    1 => '2',
    2 => '3',
  ),
);

?>