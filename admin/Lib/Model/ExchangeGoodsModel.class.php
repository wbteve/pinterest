<?php
class ExchangeGoodsModel extends CommonModel
{
	protected $_validate = array(
		array('name','require','{%NAME_EMPTY_TIP}'),
		array('stock',0,'{%STOCK_MIN_TIP}',0,'gt'),
		array('user_num',0,'{%USER_NUM_MIN_TIP}',0,'gt'),
		
	);

	protected $_auto = array( 
		array('status','1'),  // 新增的时候把status字段设置为1	
	);
}
?>