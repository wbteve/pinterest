<? if(!defined('IN_FANWE')) exit('Access Denied'); if(!empty($best_list)) { ?>
<div class="pic_fix">
<div class="pic_ps">
<div id="pic_tpk_tj" class="pic_tpk_tj">
<ul><? if(is_array($best_list)) { foreach($best_list as $topic) { ?><li class="pic_tpk_tj_f">
<a target="_blank" href="<?=$topic['url']?>"><img src="<?=$topic['imgs']['0']['img']?>"></a>
<div class="title">
<a target="_blank" href="<?=$topic['url']?>"><?php echo cutStr($topic['title'],34,'...');?></a>
</div>
</li>
<? } } ?>
</ul>
</div>
<div id="pic_tpk_tj_btn" class="pic_tpk_tj_btn">
<div></div>
</div>
</div>
</div>
<script type="text/javascript">
jQuery(function($){
$("#pic_tpk_tj ul").carouFredSel({
curcular: false,
infinite: false,
auto : true,
pauseDuration:3000,
pagination: "#pic_tpk_tj_btn div",
scroll: {
pauseOnHover: true
}
});
});
</script>
<? } ?>