var advpositionHTML='<table cellpadding="0" cellspacing="0">\r\n{loop $adv_list $adv}\r\n<tr>\r\n<td>{$adv[html]}</td>\r\n</tr>\r\n{/loop}\r\n</table>';

var advPositionSwf="<script type=\"text/javascript\">\r\ndocument.write('<object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0\" width=\"{$adv_position[width]}\" height=\"{$adv_position[height]}\"><param name=\"allowScriptAccess\" value=\"sameDomain\"><param name=\"movie\" value=\"{$adv_path}\"><param name=\"quality\" value=\"high\"><param name=\"bgcolor\" value=\"#F0F0F0\"><param name=\"menu\" value=\"false\"><param name=wmode value=\"opaque\"><param name=\"FlashVars\" value=\"pics={$adv_pics}&links={$adv_links}&borderwidth={$adv_position[width]}&borderheight={$adv_position[height]}&textheight=0\"><embed src=\"{$adv_path}\" FlashVars=\"pics={$adv_pics}&links={$adv_links}&borderwidth={$adv_position[width]}&borderheight={$adv_position[height]}&textheight=0\" quality=\"high\" width=\"{$adv_position[width]}\" height=\"{$adv_position[height]}\" wmode=\"opaque\" allowScriptAccess=\"sameDomain\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" /></object>');\r\n</script>";

function changAdvType(obj)
{
	var html = '';
	var code = '';

	switch(obj.value)
	{
		case '1':
		case '2':
			if(obj.value == '1')
				$("#urlTr").show();
			else
				$("#urlTr").hide();
			
			html = '<input type="file" class="fileinput" name="code" size="30" />';
			code = $("#file_code").val();
			if(code != null)
				html += "&nbsp;&nbsp;" + code;

		break;
		
		case '3':
			$("#urlTr").hide();
			code = $("#textarea_code").val();
			if(code == null)
				code = '';
			html ='<textarea name="code" class="textinput areainput" rows="10">'+code+"</textarea>";
		break;
	}
	
	$("#codeTd").html(html);
}

function UpdatePositionStyle(obj)
{
	if(obj.checked)
	{
		$("#flash_style").attr('disabled',false);
		$("#style").val(advPositionSwf);
	}
	else
	{
		$("#flash_style").attr('disabled',true);
		$("#style").val(advpositionHTML);
	}
}

function getPageList()
{
	$.ajax({
		  url: APP+'?'+VAR_MODULE+'='+CURR_MODULE+'&'+VAR_ACTION+'=getPageList&tmpl='+$("#tmpl").val()+'&ajax=1',
		  cache: false,
		  dataType:"json",
		  success:function(data)
		  {
			  if(data!=null)
			  {
				  $("#page").empty();
				  try
				  {
					  var files = data.files;
					  for(var i=0;i<files.length;i++)
					  {
						  $("#page").append("<option value='"+files[i]+"'>"+files[i]+"</option>");
					  }
				  }catch(e){}
			  }
			  getLayoutList();
		  }
		}); 
	
}

function getLayoutList()
{
	var tmpl = $("#tmpl").val();
	var page = $("#page").val();
	$.ajax({
		  url: APP+'?'+VAR_MODULE+'='+CURR_MODULE+'&'+VAR_ACTION+'=getLayoutList&tmpl='+tmpl+'&page='+page,
		  cache: false,
		  dataType:"json",
		  success:function(data)
		  {	
			$("#layout_id").empty();
		  	$("#layout_id").append("<option value=''>请选择</option>");
			if(data!=null)
			{
				 try
				 {
					 for(var i=0;i<data.length;i++)
					{
						$("#layout_id").append("<option value='"+data[i]+"'>"+data[i]+"</option>");
					}
				}catch(e){}
			}
			  
		  }
		}); 
}


function initLoadLayout()
{
	getPageList();
	getLayoutList();
}
