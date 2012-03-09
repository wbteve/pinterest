jQuery(function($){
	$("#checkcode_change").click(function(){
		changeCheckCode();
	}); 
	
	$("#loginForm").submit(function(){
		$(".err_name").hide();
		$(".err_pass").hide();
		$(".lg_login_loading").hide();
		$(".iserror").hide();
		$(".iserror2").hide();
		
		var form = this;
		var name_tip = this.email_name.getAttribute('tooltip');
		var uname = $.trim(this.email_name.value);
		if(uname == '' || uname == name_tip)
		{
			$(".err_name span").html(LANG.user_name_require);
			$(".err_name").show();
			this.email_name.focus();
			return false;	
		}
		
		if($.trim(this.pass.value) == '')
		{
			$(".err_pass span").html(LANG.user_pass_require);
			$(".err_pass").show();
			this.pass.focus();
			return false;	
		}
		
		$("#login_submit").attr("disabled",true);
		$(".lg_login_loading").show();
		$.ajax({ 
			url: SITE_PATH+"user.php?action=ajax_login",
			type: "POST",
			data:$(form).serialize(),
			cache:false,
			dataType:'json',
			success:function(result){
				$(".lg_login_loading").hide();
				$("#login_submit").attr("disabled",false);
				if(result.status == 0)
					$("#iserror").show();
				else if(result.status == 2)
					$("#iserror2").show();
				else
				{
					var fun = function(){
						location.href = form.refer.value;
					};
					setTimeout(fun,1);
				}
			},
			error:function(){
				$(".lg_login_loading").hide();
				$("#login_submit").attr("disabled",false);
				alert(LANG.login_error);
			}
		});
		
		return false;
	});
	
	$("#loginForm input.text,#registerForm input.text").focus(function(){
		$(this).addClass('activetext');
	}).blur(function(){
		$(this).removeClass('activetext');
	});
	
	$("#registerForm input[istip]").focus(function(){
		var istip = parseInt(this.getAttribute('istip'));
		var rel = this.getAttribute('rel');
		if(istip == 0)
		{
			this.setAttribute('istip',1);
			$(rel).css("visibility",'visible');
		}
	});
	
	$("#reg_email").blur(function(){
		var obj = this;
		this.setAttribute('check',0);
		var email = $.trim(obj.value);
		
		if(email == '')
		{
			Reg_Err_Handler(obj,LANG.email_require);
			return;	
		}
		
		if(!$.checkEmail(email))
		{
			Reg_Err_Handler(obj,LANG.email_error);
			return;
		}
		
		var query = new Object();
		query.field = 'email';
		query.email = email;
		Reg_Check_Loading(obj);
		$.ajax({ 
			url: SITE_PATH+"services/service.php?m=user&a=check",
			type:"POST",
			data:query,
			cache:true,
			dataType:'json',
			success:function(result){
				if(result.status == 1)
				{
					Reg_OK_Handler(obj);
					obj.value = email;
				}
				else
					Reg_Err_Handler(obj,LANG.email_error1);
			},
			error:function(){
				Reg_Err_Handler(obj,LANG.email_error2);
			}
		});
	});
	
	$("#reg_user_name").blur(function(){
		var obj = this;
		this.setAttribute('check',0);
		var username = $.trim(obj.value);
		var len = $.getStringLength(username);
		if(len == 0)
		{
			Reg_Err_Handler(obj,LANG.user_name_error);
			return;
		}
		
		if(len < 2 || len > 20)
		{
			Reg_Err_Handler(obj,LANG.user_name_error1);
			return;
		}
	
		var reg = /^[\u4e00-\u9fa5a-zA-Z0-9]+$/;
		if(!reg.test(username))
		{
			Reg_Err_Handler(obj,LANG.user_name_error2);
			return;
		}
		
		reg = /^[0-9].+$/;
		if(reg.test(username))
		{
			Reg_Err_Handler(obj,LANG.user_name_error3);
			return;
		}
		
		var query = new Object();
		query.field = 'user_name';
		query.user_name = username;
		Reg_Check_Loading(obj);
		$.ajax({ 
			url: SITE_PATH+"services/service.php?m=user&a=check",
			type:"POST",
			data:query,
			dataType:'json',
			cache:true,
			success:function(result){
				if(result.status == 1)
				{
					obj.value = username;
					Reg_OK_Handler(obj);
				}
				else
					Reg_Err_Handler(obj,LANG.user_name_error4);
			},
			error:function(){
				Reg_Err_Handler(obj,LANG.user_name_error5);
			}
		});
	});
	
	$("#reg_password").blur(function(){
		var obj = this;
		this.setAttribute('check',0);
		var password = $.trim(obj.value);
		var len = password.length;
		if(len == 0)
		{
			Reg_Err_Handler(obj,LANG.password_error);
			Reg_Password_Change(true);
			return;
		}
		
		if(len < 6)
		{
			Reg_Err_Handler(obj,LANG.password_error1);
			Reg_Password_Change(true);
			return;
		}
		
		if(len > 20)
		{
			Reg_Err_Handler(obj,LANG.password_error2);
			Reg_Password_Change(true);
			return;
		}
		
		obj.value = password;
		Reg_OK_Handler(obj);
		Reg_Password_Change(false);
	});
	
	$("#reg_cpassword").blur(function(){
		this.setAttribute('check',0);
		var obj = this;
		var cpassword = $.trim(obj.value);
		var password = $.trim($("#reg_password").val());
		var pcheck = $("#reg_password").attr('check');
		pcheck = parseInt(pcheck);
		var len = cpassword.length;
		
		if(password == '' || pcheck == 0)
		{
			Reg_Err_Handler(obj,LANG.cpassword_error1);
			return;
		}
		
		if(len == 0)
		{
			Reg_Err_Handler(obj,LANG.cpassword_error2);
			return;
		}
		
		if(password != cpassword)
		{
			Reg_Err_Handler(obj,LANG.cpassword_error);
			return;
		}
		
		obj.value = cpassword;
		Reg_OK_Handler(obj);
	});
	
	$("#reg_agreement").change(function(){
		if(this.checked)
			$("#reg_submit").attr('disabled',false);
		else
			$("#reg_submit").attr('disabled',true);
	});
	
	$("#registerForm").submit(function(){
		$(".lg_reg_loading").hide();
		$(".lg_reg_check").hide();
		if($("#registerForm input[check=1]").length < 4)
		{
			$(".lg_reg_check").show();
			return false;
		}
		
		var form = this;
		$("#reg_submit").attr("disabled",true);
		$(".lg_reg_loading").show();
		$.ajax({ 
			url: SITE_PATH+"user.php?action=ajax_register",
			type: "POST",
			data:$(form).serialize(),
			cache:false,
			dataType:'json',
			success:function(result){
				$(".lg_reg_loading").hide();
				$("#reg_submit").attr("disabled",false);
				if(result.status == 0)
				{
					alert(result.msg);
					changeCheckCode();
				}
				else
				{
					var fun = function(){
						location.href = form.refer.value;
					};
					setTimeout(fun,1);
				}
			},
			error:function(){
				$(".lg_reg_loading").hide();
				$("#reg_submit").attr("disabled",false);
				changeCheckCode();
				alert(LANG.js_reg_error);
			}
		});
		return false;
	});
	
	$("#getPwdForm").submit(function(){
		$("#getPwdForm .get_password_fail").hide();
		
		var username = $.trim(this.user_name.value);
		var len = $.getStringLength(username);
		if(len == 0)
		{
			Pwd_Check_Handler(this.user_name,LANG.user_name_require);
			return false;
		}
		
		var reg = /^[\u4e00-\u9fa5a-zA-Z0-9]+$/;
		if(len < 2 || len > 20 || !reg.test(username))
		{
			Pwd_Check_Handler(this.user_name,LANG.getpwd_err1);
			return false;
		}
		this.user_name.value = username;
		
		var email = $.trim(this.email.value);
		if(email == '')
		{
			Pwd_Check_Handler(this.email,LANG.email_require);
			return false;
		}
		
		if(!$.checkEmail(email))
		{
			Pwd_Check_Handler(this.email,LANG.email_error);
			return false;
		}
		this.email.value = email;
	});
	
	$("#resetPwdForm").submit(function(){
		$("#resetPwdForm .get_password_fail").hide();
		
		var password = $.trim(this.password.value);
		var len = password.length;
		if(len == 0)
		{
			Pwd_Check_Handler(this.password,LANG.password_error);
			return false;
		}
		
		if(len < 6)
		{
			Pwd_Check_Handler(this.password,LANG.password_error1);
			return false;
		}
		
		if(len > 20)
		{
			Pwd_Check_Handler(this.password,LANG.password_error2);
			return false;
		}

		var cpassword = $.trim(this.confirm_password.value);
		len = cpassword.length;
		
		if(len == 0)
		{
			Pwd_Check_Handler(this.confirm_password,LANG.cpassword_error2);
			return false;
		}
		
		if(password != cpassword)
		{
			Pwd_Check_Handler(this.confirm_password,LANG.cpassword_error);
			return false;
		}
		this.confirm_password.value = cpassword;
	});
});

function changeCheckCode()
{
	var rhash = $("#regRHash").val();
	var d = new Date();
	document.getElementById("img_checkcode").src = SITE_PATH + "misc.php?action=verify&rhash="+rhash+"&time="+d.getTime();
} 

function Pwd_Check_Handler(obj,msg)
{
	$(obj).next().html(msg).show();
}

function Reg_Password_Change(isError)
{
	var obj = $("#reg_cpassword").get(0);
	var istip = obj.getAttribute('istip');
	istip = parseInt(istip);
	if(istip == 1)
	{
		if(isError)
			Reg_Err_Handler(obj,LANG.cpassword_error1);
		else
		{
			if($("#reg_password").val() != $("#reg_cpassword").val())
				Reg_Err_Handler(obj,LANG.cpassword_error2);
		}
	}
}

function Reg_Check_Loading(obj)
{
	var rel = obj.getAttribute('rel');
	$(rel).html('<div><img src="'+ TPL_PATH +'images/loading_blue1.gif" /></div>');
}

function Reg_OK_Handler(obj)
{
	var rel = obj.getAttribute('rel');
	obj.setAttribute('check',1);
	$(rel).html('<div><img src="'+ TPL_PATH +'images/ok_01.png" /></div>');
}

function Reg_Err_Handler(obj,msg)
{
	var rel = obj.getAttribute('rel');
	obj.setAttribute('check',0);
	$(rel).html('<div style="width:100%;"><img src="'+ TPL_PATH +'images/error_01.png" style="float:left;"/><span style="color:#ff0000; float:left; margin-left:4px;">' + msg + '</span></div>');
}