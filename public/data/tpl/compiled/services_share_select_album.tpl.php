<? if(!defined('IN_FANWE')) exit('Access Denied'); ?>
<div class="album_pr">
<div class="my_album_list" style="left:0px;">
<ul class="album_ul"><? if(is_array($list)) { foreach($list as $album) { ?><li album="<?=$album['id']?>"><input type="radio" name="album_box" class="m_i" aid="<?=$album['id']?>"<? if($album['id'] == $select_aid) { ?> checked="checked"<? } ?>><label class="m_a"><?=$album['title']?></label></li>
<? } } ?>
</ul>
</div>
<div class="create">
<input type="text" value="输入新杂志社名" class="album_name" albumName="输入新杂志社名">
<select class="album_cid"><? if(is_array($_FANWE['cache']['albums']['category'])) { foreach($_FANWE['cache']['albums']['category'] as $category) { ?><option value="<?=$category['id']?>"><?=$category['name']?></option>
<? } } ?>
</select>
<a class="blue_button to_create r3" href="javascript:;" onclick="$.Save_Album(this,publishSaveAlbumHadnler);">创建</a>
</div>
<div class="page_slide">
<? if($pager['page_count'] > 1) { ?>
<span class="cu_page"><?=$pager['page']?></span>/<span class="all_page"><?=$pager['page_count']?></span>
<a class="left r3" href="javascript:;">&lt;</a>
<a class="right r3" href="javascript:;">&gt;</a>
<? } ?>
</div>
</div>