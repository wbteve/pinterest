<? if(!defined('IN_FANWE')) exit('Access Denied'); ?>
<div class="lb_hd">
<ul class="lb_tab" id="expression_tabs"><? if(is_array($expressions_js)) { foreach($expressions_js as $key => $item) { ?><li class="<? if($key =='qq') { ?>c<? } ?> rt3 " f="<?=$key?>" title="<? echo lang('template','expression_'.$key); ?>"><a href="javascript:;"><? echo lang('template','expression_'.$key); ?></a></li>
<? } } ?>
</ul>
<a href="javascript:;" class="lb_close" onclick="$.weeboxs.close()"></a>
</div>
<div class="lb_bd" id="lb_face">
<ul class="face_l" id="expression_items">
<?=$current_exp?>
</ul>
</div>
<div class="clear"></div>
<script type="text/javascript">
var Expression_Items = <?=$expressions_json?>;
</script>