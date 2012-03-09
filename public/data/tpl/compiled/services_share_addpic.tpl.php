<? if(!defined('IN_FANWE')) exit('Access Denied'); ?>
<div id="lb_pic">
<div class="pic_box PIC_BOX">
<span><?php echo sprintf('选择您要上传的图片（支持GIF/JPG/PNG，最大%dKB）。',$_FANWE['setting']['max_upload']); ?></span>
<form>
<? if(FS('Image')->getIsServer()) { ?>
<div class="blank12"></div>
<div id="UPLOAD_PHOTO_FLASH_BOX"></div>
<? } else { ?>
<input class="gray_button r3" value="从电脑中选择图片..." type="button">
<div class="tfile_box">
<input class="tfile PUB_PIC_FILE" name="image" type="file" />
</div>
<? } ?>
<div class="blank12"></div>
<div class="type_box">
<label><input type="radio" class="photo_type" name="photo_type" value="default" checked="checked" /><span>一般图片</span></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" class="photo_type" name="photo_type" value="dapei" /><span>我的搭配</span></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" class="photo_type" name="photo_type" value="look" /><span>我的晒货</span></label>
</div>
</form>
</div>
<div class="lb_loading PUB_LOADING">
<img class="fl" src="./tpl/images/loading.gif">&nbsp;&nbsp;请稍候......</a>
</div>
</div>