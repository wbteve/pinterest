var darenIndex = 1;
var shareTimer;
FANWE.INDEX_SHARE_WIDTH = 415;

jQuery(function($){
	$(".topic_list_new li").hover(function(){
		$(this).addClass('h');
	},function(){
		$(this).removeClass('h');
	});

	$('.cate_tag_item').hover(function(){
		$('.sk',this).show();
	},function(){
		$('.sk',this).hide();
	});

	$('.fanwe_talk li').css({"position":"absolute","top":0,"left":3 * FANWE.INDEX_SHARE_WIDTH,"z-index":1});
	$('.fanwe_talk li').eq(0).css({"left":0});
	$('.fanwe_talk li').eq(1).css({"left":FANWE.INDEX_SHARE_WIDTH});
	$('.fanwe_talk li').eq(2).css({"left":2 * FANWE.INDEX_SHARE_WIDTH});
	scrollShareInit();
});

function scrollShareInit()
{
	$('.fanwe_talk li').eq(1).css({"z-index":2});
	$('.fanwe_talk li').eq(2).css({"z-index":2});
	FANWE.INDEX_CURRENT_SHARE = $('.fanwe_talk li').eq(1);
	FANWE.INDEX_NEXT_SHARE = $('.fanwe_talk li').eq(2);
	scrollNewShare();
}

function scrollNewShare()
{
	FANWE.INDEX_CURRENT_SHARE.animate({
		left:0
	},
	{
		duration:500,
		easing:"easeInCubic",
		step:function(){
			var left = parseInt(FANWE.INDEX_CURRENT_SHARE.css("left"));
			FANWE.INDEX_NEXT_SHARE.css({"left":left + FANWE.INDEX_SHARE_WIDTH});
		},
		complete:function(){
			$('.fanwe_talk ul').append($('.fanwe_talk li').eq(0));
			$('.fanwe_talk li').css({"position":"absolute","top":0,"left":3 * FANWE.INDEX_SHARE_WIDTH,"z-index":1});
			$('.fanwe_talk li').eq(0).css({"left":0});
			$('.fanwe_talk li').eq(1).css({"left":FANWE.INDEX_SHARE_WIDTH});
			$('.fanwe_talk li').eq(2).css({"left":2 * FANWE.INDEX_SHARE_WIDTH});
			shareTimer = setTimeout('scrollShareInit()',3000);
		}
	});
}

function UpdateUserFollow(obj,result)
{
	if(result.status == 1)
	{
		$(obj).before('<img src="'+ TPL_PATH +'images/best_follow_ok.gif" class="ufollow">');
		$(obj).remove();
	}
}