<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

class Mail
{
	var $error = array();
	/**
	 * 邮件发送
	 *
	 * @param: $name[string]        接收人姓名
	 * @param: $email[string]       接收人邮件地址
	 * @param: $subject[string]     邮件标题
	 * @param: $content[string]     邮件内容
	 * @param: $type[int]           0 普通邮件， 1 HTML邮件
	 * @param: $notification[bool]  true 要求回执， false 不用回执
	 *
	 * @return boolean
	 */
	function send($name, $email, $subject, $content, $type = 0, $notification=false)
	{
		@set_time_limit(3600);
		if(function_exists('ini_set'))
			ini_set('max_execution_time',3600);
			
		global $_FANWE;
		$charset = 'utf-8';
		/* 邮件的头部信息 */
        $content_type = ($type == 0) ?
            'Content-Type: text/plain; charset=' . $charset : 'Content-Type: text/html; charset=' . $charset;
        $content   =  base64_encode($content);

        $headers = array();
        $headers[] = 'Date: ' . gmdate('D, j M Y H:i:s') . ' +0000';
        $headers[] = 'To: "' . '=?' . $charset . '?B?' . base64_encode($name) . '?=' . '" <' . $email. '>';
        $headers[] = 'From: "' . '=?' . $charset . '?B?' . base64_encode($_FANWE['setting']['site_name']) . '?='.'" <' . $_FANWE['setting']['smtp_account'] . '>';
        $headers[] = 'Subject: ' . '=?' . $charset . '?B?' . base64_encode($subject) . '?=';
        $headers[] = $content_type . '; format=flowed';
        $headers[] = 'Content-Transfer-Encoding: base64';
        $headers[] = 'Content-Disposition: inline';
        if ($notification)
        {
            $headers[] = 'Disposition-Notification-To: ' . '=?' . $charset . '?B?' . base64_encode($_FANWE['setting']['site_name']) . '?='.'" <' . $_FANWE['setting']['smtp_account'] . '>';
        }
		
        /* 获得邮件服务器的参数设置 */
		$params['host'] = $_FANWE['setting']['smtp_server'];
        $params['port'] = $_FANWE['setting']['smtp_port'];
        $params['user'] = $_FANWE['setting']['smtp_account'];
        $params['pass'] = $_FANWE['setting']['smtp_password'];
		
        if (empty($params['host']) || empty($params['port']))
        {
            // 如果没有设置主机和端口直接返回 false
            $this->error[] = '邮件服务器设置信息不完整';

            return false;
        }
        else
        {
            // 发送邮件
            if (!function_exists('fsockopen'))
            {
                //如果fsockopen被禁用，直接返回
                $this->error[] = 'fsockopen函数被禁用';
                return false;
            }

            include fimport('class/smtp');
            static $smtp;

            $send_params['recipients'] = $email;
            $send_params['headers']    = $headers;
            $send_params['from']       = $_FANWE['setting']['smtp_account'];
            $send_params['body']       = $content;

            if (!isset($smtp))
            {
                $smtp = new Smtp($params);
            }

            if ($smtp->connect() && $smtp->send($send_params))
            {
                return true;
            }
            else
            {
                $err_msg = $smtp->error_msg();
                if (empty($err_msg))
                {
                    $this->error[] = 'Unknown Error';
                }
                else
                {
                    if (strpos($err_msg, 'Failed to connect to server') !== false)
                    {
                        $this->error[] = sprintf("无法连接到邮件服务器 %s", $params['host'] . ':' . $params['port']);
                    }
                    else if (strpos($err_msg, 'AUTH command failed') !== false)
                    {
                        $this->error[] = '邮件服务器验证帐号或密码不正确';
                    }
                    elseif (strpos($err_msg, 'bad sequence of commands') !== false)
                    {
                       $this->error[] = '服务器拒绝发送该邮件';
                    }
                    else
                    {
                        $this->error[] = $err_msg;
                    }
                }

                return false;
            }
        }
	}
	
	function getError()
	{
		return $this->error;
	}
}
?>