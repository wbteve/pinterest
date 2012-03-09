<?php
class ImageServersModel extends CommonModel
{
	public $_validate = array(
		array('code','require','{%CODE_REQUIRE}'),
		array('code','','{%CODE_UNIQUE}',0,'unique',1),
		array('url','require','{%URL_REQUIRE}'),
	);
}
?>