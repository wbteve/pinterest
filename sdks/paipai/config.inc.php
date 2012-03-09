<?php
require_once 'Util.php';

/*通过授权方式调用拍拍API的程序一般要提供四个参数，其中uin和token用于认证操作用户的身份，spid用于指示操作者所用的APP程序，secretKey用于验证请求包是否被篡改。
uin、token、spid作为参数直接传递给API服务器，而secretKey则用于将相关参数通过md5加密为一个sign值，放入请求参数中。*/
//API地址
define('PAIPAI_API_URL', 'http://api.paipai.com');

//以下错误字典，可以根据拍拍的文档自行设置
$errorinfo[0] = array('en'=>'Unknown Error','cn'=>'未知错误');
$errorinfo[3] = array('en'=>'Upload fail','cn'=>'上传附件失败');
$errorinfo[4] = array('en'=>'User Call limited','cn'=>'用户对api的调用超过限制');
$errorinfo[5] = array('en'=>'Session Call limited','cn'=>'用户会话期呼叫频度受限');
$errorinfo[7] = array('en'=>'App Call Limited','cn'=>'app对api的调用超过限制');
$errorinfo[8] = array('en'=>'App call exceeds limited frequency','cn'=>'每分钟调用此app的次数超过了限制');
$errorinfo[9] = array('en'=>'Http action not allowed','cn'=>'该方法不允许使用此Http动作');
$errorinfo[10] = array('en'=>'Service currently unavailable','cn'=>'服务不可用');
$errorinfo[11] = array('en'=>'Insufficient ISV permissions','cn'=>'第三方程序权限不够');
$errorinfo[12] = array('en'=>'Insufficient user permissions','cn'=>'用户权限不够');
$errorinfo[15] = array('en'=>'Remote service error','cn'=>'执行远程服务时出错');
$errorinfo[21] = array('en'=>'Missing Method','cn'=>'方法丢失');
$errorinfo[22] = array('en'=>'Invalid Method','cn'=>'方法无效');
$errorinfo[23] = array('en'=>'Invalid Format','cn'=>'响应格式无效');
$errorinfo[24] = array('en'=>'Missing signature','cn'=>'签名或 APP SECRET丢失');
$errorinfo[25] = array('en'=>'Invalid signature','cn'=>'签名或 APP SECRET无效');
$errorinfo[26] = array('en'=>'Missing session','cn'=>'会话期识别码丢失');
$errorinfo[27] = array('en'=>'Invalid session','cn'=>'会话期识别码无效');
$errorinfo[28] = array('en'=>'Missing API Key','cn'=>'App_Key丢失');
$errorinfo[29] = array('en'=>'Invalid API Key','cn'=>'App_Key无效');
$errorinfo[30] = array('en'=>'Missing timestamp','cn'=>'时间戳丢失');
$errorinfo[31] = array('en'=>'Invalid timestamp','cn'=>'时间戳无效');
$errorinfo[32] = array('en'=>'Missing version','cn'=>'版本丢失');
$errorinfo[33] = array('en'=>'Invalid version','cn'=>'版本错误');
$errorinfo[34] = array('en'=>'Unsupported version','cn'=>'版本未被该API支持');
$errorinfo[40] = array('en'=>'Missing required arguments','cn'=>'参数丢失，指除 method ,session ,timestamp ,format ,app_key ,v ,sign外的其他参数丢失');
$errorinfo[41] = array('en'=>'Invalid arguments','cn'=>'参数格式错误');
$errorinfo[550] = array('en'=>'User service unvailable','cn'=>'用户数据服务不可用');
$errorinfo[551] = array('en'=>'Item service unvailable','cn'=>'商品数据服务不可用');
$errorinfo[552] = array('en'=>'Item image service unvailable','cn'=>'商品图片数据服务不可用');
$errorinfo[553] = array('en'=>'Item simple update service unavailable','cn'=>'上下架，推荐，取消推荐 服务不可用');
$errorinfo[560] = array('en'=>'Trade service unvailable','cn'=>'交易数据服务不可用');
$errorinfo[590] = array('en'=>'Shop service unavailable','cn'=>'店铺服务不可用');
$errorinfo[591] = array('en'=>'Shop showcase remainCount unavailable','cn'=>'店铺剩余推荐数 服务不可用');
$errorinfo[601] = array('en'=>'User not exist','cn'=>'用户不存在 ');


?>