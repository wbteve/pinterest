<?php
/**
 * 错误处理类
 */
class Taoapi_Exception
{
    private $_ErrorInfo;

    public function __construct ($error, $paramArr = null, $closeerror = false,$Errorlog = false)
    {
        return $this->ViewError($error, $paramArr, $closeerror,$Errorlog);
    }

    public function getErrorInfo()
    {
        return $this->_ErrorInfo;
    }

    private function ErrorInfo ($errorcode)
    {
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
		
        if (! array_key_exists($errorcode, $errorinfo)) {
            $errorcode = 0;
        }
        return $errorinfo[$errorcode];
    }

    public function WriteError ($error, $paramArr)
    {
        $errorpath = dirname(__FILE__) . '/api_error_log';
        if (! is_dir($errorpath)) {
            @mkdir($errorpath);
        }
        if ($fp = @fopen($errorpath . '/' . date('Y-m-d') . '.log', 'a')) {
            $errorinfotext[] = date('Y-m-d H:i:s');
            $errorinfotext[] = "Error:" . $error['msg'];
            foreach ($paramArr as $key => $value) {
                $errorinfotext[] = $key . " : " . $value;
            }
            $errorinfotext = implode("\t", $errorinfotext) . "\r\n";
            @fwrite($fp, $errorinfotext);
            fclose($fp);
        }
    }

    public function ViewError ($error, $paramArr = null, $closeerror = false,$Errorlog = false)
    {
        $debug = debug_backtrace(false);
        rsort($debug);
        if (is_array($error)) {
            if ($error['code'] < 100) {
                $errorlevel = '系统级错误 ';
            } else {
                $errorlevel = '业务级错误';
            }
            $errortitle = $this->ErrorInfo($error['code']);
            $this->_ErrorInfo = implode("\n",$errortitle);
			$errortitle = (object)$errortitle;
			if($Errorlog)
			{
				$this->WriteError($error, $paramArr);
			}
            if($closeerror) {
                return false;
            }
            $errortitlediy = $errorlevel . ": " . $errortitle->en . " (" . $errortitle->cn . ")";
        } else {
            $errortitlediy = $error;
        }

        $view[] = "<br /><font size='1'><table dir='ltr' border='1' cellspacing='0' cellpadding='1' width=\"100%\">";

        $view[] = "<tr><th align='left' bgcolor='#f57900' colspan=\"3\"><span style='background-color: #cc0000; color: #fce94f; font-size: x-large;'>( ! )</span> " . $errortitlediy . " in " . $debug[count($debug) - 2]['file'] . " on line <i>" . $debug[count($debug) - 2]['line'] . "</i></th></tr>";

        $view[] = "<tr><th align='left' bgcolor='#e9b96e' colspan='3'>调用函数</th></tr>";
        $view[] = "<tr><th align='center' bgcolor='#eeeeec' width='30'>#</th><th align='left' bgcolor='#eeeeec'>函数名</th><th align='left' bgcolor='#eeeeec'>所在文件</th></tr>";
        $mainfile = basename($debug[0]['file']);

        $view[] = "<tr><td bgcolor='#eeeeec' align='center'>1</td><td bgcolor='#eeeeec'>{main}(  )</td><td bgcolor='#eeeeec'>../{$mainfile}<b>:</b>0</td></tr>";

        foreach ($debug as $key => $value) {
            $value['file'] = basename($value['file']);
            $key = $key + 2;
            $view[] = "<tr><td bgcolor='#eeeeec' align='center'>$key</td><td bgcolor='#eeeeec'>{$value['class']}{$value['type']}{$value['function']}(  )</td><td title='{$value['file']}' bgcolor='#eeeeec'>../{$value['file']}<b>:</b>{$value['line']}</td></tr>";
        }

        $view[] = '</table></font>';
        if ($paramArr) {
            $view[] = "<br /><font size='1'><table dir='ltr' border='1' cellspacing='0' cellpadding='1' width=\"100%\">";
            $view[] = "<tr><th align='left' bgcolor='#e9b96e' colspan='4' height='25px'>API 调用参数列表</th></tr>";
            $view[] = "<tr><th align='center' bgcolor='#eeeeec' width='30px'>#</th><th width='120' align='left' bgcolor='#eeeeec'>参数名称</th><th align='left' bgcolor='#eeeeec'>参数</th><th align='left' bgcolor='#eeeeec' width='50px'>长度</th></tr>";
            $i = 1;
            foreach ($paramArr as $key => $value) {
                $view[] = "<tr><td bgcolor='#eeeeec' align='center'>$i</td><td bgcolor='#eeeeec'>{$key}</td><td bgcolor='#eeeeec'>" . implode(', ', explode(',', $value)) . "</td><td bgcolor='#eeeeec'><b>" . strlen($value) . "</b></td></tr>";
                $i ++;
            }
            $view[] = '</table></font>';
        }

        $this->_ErrorInfo =  implode("\n", $view);
    }
}