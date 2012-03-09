jQuery(function($){
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
	
		var reg = /^[\u4e00-\u9fa5a-zA-Z0-9_]+$/;
		if(!reg.test(username))
		{
			Reg_Err_Handler(obj,LANG.user_name_error2);
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
	});
});

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