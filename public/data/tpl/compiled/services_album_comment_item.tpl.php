<? if(!defined('IN_FANWE')) exit('Access Denied'); ?>
<li>
<div class="pic_n"><?php echo setTplUserFormat($comment['uid'],1,0,'s',24,'','avt icard fl r3',''); ?></div>
<p><?php echo setTplUserFormat($comment['uid'],0,0,'',0,'n icard fl','',''); ?>：<?=$comment['content']?>
</p>
<a class="rpl fr" href="javascript:;" uname="<?=$comment['user']['user_name']?>" cid="<?=$comment['comment_id']?>" onclick="$.Reply_Comment(this);">回复</a>
</li>