<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: awfigq <awfigq@qq.com>
// +----------------------------------------------------------------------
/**
 +------------------------------------------------------------------------------
 * FANWE标签库解析类
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Think
 * @subpackage  Template
 +------------------------------------------------------------------------------
 */
import('TagLib');
class TagLibFanwe extends TagLib
{
	protected $tags =  array(
        // 标签定义：
        //attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'editor'=>array('attr'=>'id,name,style,width,height,content,type,toolbar','level'=>1,'close'=>0),
		'imageBtn'=>array('attr'=>'name,value,id,style,click,type','level'=>1,'close'=>0),
		'linkBtn'=>array('attr'=>'name,text,id,style,href','level'=>1,'close'=>0),
		'select'=>array('attr'=>'name,options,values,output,multiple,id,size,first,firstvalue,selected,style,dblclick,change,textfield,valuefield','level'=>1,'close'=>0),
		'checkbox'=>array('attr'=>'name,checkboxes,checked,separator','level'=>1,'close'=>0),
		'radio'=>array('attr'=>'name,radios,checked,separator','level'=>1,'close'=>0),
		'list'=>array('attr'=>'id,datasource,pk,style,name,action,checkbox,actionlist,show,action_width,action_align,nosort,attrs','level'=>1,'close'=>0),
    );
    /**
     +----------------------------------------------------------
     * editor标签解析 插入可视化编辑器
     * 格式： <fanwe:editor id="editor" name="remark" type="CKEDITOR" content="{$vo.remark}" />
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    public function _editor($attr)
    {
        $tag        =	$this->parseXmlAttr($attr,'editor');
        $id			=	!empty($tag['id']) ? $tag['id'].'_editor' : $tag['name'].'_editor';
        $name   	=	$tag['name'];
        $style   	=	!empty($tag['style'])?$tag['style']:'';
        $width		=	!empty($tag['width'])?$tag['width']: '98%';
        $height     =	!empty($tag['height'])?$tag['height'] :'320px';
        $content    =   $tag['content'];
        $type       =   $tag['type'];
		$toolbar    =   !empty($tag['toolbar']) ? $tag['toolbar'] :'Default';
		
        switch(strtoupper($type)) {
            case 'CKEDITOR':
                $parseStr   =	'<script type="text/javascript" src="__TMPL__Static/Ckeditor/ckeditor.js"></script><script type="text/javascript" src="__TMPL__Static/Ckfinder/ckfinder.js"></script><textarea id="'.$id.'" name="'.$name.'">'.$content.'</textarea><script type="text/javascript">var '.$id.' =CKEDITOR.replace("'.$id.'",{"width":"'.$width.'","height":"'.$height.'","toolbar":"'.$toolbar.'"}) ;CKFinder.setupCKEditor('.$id.',"__TMPL__Static/Ckfinder") ;</script>';
                break;
            default :
                $parseStr  =  '<textarea id="'.$id.'" style="'.$style.'" name="'.$name.'" >'.$content.'</textarea>';
        }

        return $parseStr;
    }

    /**
     +----------------------------------------------------------
     * imageBtn标签解析
     * 格式： <html:imageBtn type="" value="" />
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    public function _imageBtn($attr)
    {
        $tag        = $this->parseXmlAttr($attr,'imageBtn');
        $name       = $tag['name'];   //名称
        $value      = $tag['value'];  //文字
        $id         = $tag['id'];     //ID
        $style      = $tag['style'];  //样式名
        $click      = $tag['click'];  //点击
        $type       = empty($tag['type'])?'button':$tag['type']; //按钮类型

        if(!empty($name)) {
            $parseStr   = '<div class="img-button '.$style.'"><p><input type="'.$type.'" id="'.$id.'" name="'.$name.'" value="'.$value.'" onclick="'.$click.'" class="'.$name.'"></p></div>';
        }else {
        	$parseStr   = '<div class="img-button '.$style.'" ><p><input type="'.$type.'" id="'.$id.'"  name="'.$name.'" value="'.$value.'" onclick="'.$click.'" class="button"></p></div>';
        }

        return $parseStr;
    }
	
	/**
     +----------------------------------------------------------
     * linkBtn标签解析
     * 格式： <fanwe:linkBtn href="" text="" />
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    public function _linkBtn($attr)
    {
        $tag        = $this->parseXmlAttr($attr,'linkBtn');
        $name       = $tag['name'];   //名称
        $text      = $tag['text'];  //文字
        $id         = $tag['id'];     //ID
        $style      = $tag['style'];  //样式名
        $href      = $tag['href'];    //链接

        $parseStr   = '<div class="link-button '.$style.'"><p><a id="'.$id.'" name="'.$name.'" href="'.$href.'" class="'.$name.'">'.$text.'</a></p></div>';

        return $parseStr;
    }

    /**
     +----------------------------------------------------------
     * select标签解析
     * 格式： <html:select options="name" selected="value" />
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    public function _select($attr)
    {
        $tag        = $this->parseXmlAttr($attr,'select');
        $name       = $tag['name'];
        $options    = $tag['options'];
        $values     = $tag['values'];
        $output     = $tag['output'];
        $multiple   = $tag['multiple'];
        $id         = $tag['id'];
        $size       = $tag['size'];
        $first      = $tag['first'];
		$firstvalue = $tag['firstvalue'];
        $selected   = $tag['selected'];
        $style      = $tag['style'];
        $ondblclick = $tag['dblclick'];
		$onchange	= $tag['change'];
		$textfield  = $tag['textfield'];
        $valuefield = $tag['valuefield'];

        if(!empty($multiple)) {
            $parseStr = '<select id="'.$id.'" name="'.$name.'" ondblclick="'.$ondblclick.'" onchange="'.$onchange.'" multiple="multiple" class="'.$style.'" size="'.$size.'" >';
        }else {
        	$parseStr = '<select id="'.$id.'" name="'.$name.'" onchange="'.$onchange.'" ondblclick="'.$ondblclick.'" class="'.$style.'" >';
        }
        if(!empty($first)) {
            $parseStr .= '<option value="'.$firstvalue.'" >'.$first.'</option>';
        }
        if(!empty($options)) {
            $parseStr   .= '<?php  foreach($'.$options.' as $key=>$val) { ?>';
			
			if(!empty($valuefield))
				$parseStr   .= '<?php $key = $val[\''.$valuefield.'\']; ?>';
			
			if(!empty($textfield))
				$parseStr   .= '<?php $val = $val[\''.$textfield.'\']; ?>';
			
            if(!empty($selected))
			{
                $parseStr   .= '<?php if(!empty($'.$selected.') && ($'.$selected.' == $key || in_array($key,$'.$selected.'))) { ?>';
                $parseStr   .= '<option selected="selected" value="<?php echo $key ?>"><?php echo $val ?></option>';
                $parseStr   .= '<?php }else { ?><option value="<?php echo $key ?>"><?php echo $val ?></option>';
                $parseStr   .= '<?php } ?>';
            }
			else
			{
                $parseStr   .= '<option value="<?php echo $key ?>"><?php echo $val ?></option>';
            }
            $parseStr   .= '<?php } ?>';
        }
		else if(!empty($values))
		{
            $parseStr   .= '<?php  for($i=0;$i<count($'.$values.');$i++) { ?>';
            if(!empty($selected)) {
                $parseStr   .= '<?php if(isset($'.$selected.') && ((is_string($'.$selected.') && $'.$selected.' == $'.$values.'[$i]) || (is_array($'.$selected.') && in_array($'.$values.'[$i],$'.$selected.')))) { ?>';
                $parseStr   .= '<option selected="selected" value="<?php echo $'.$values.'[$i] ?>"><?php echo $'.$output.'[$i] ?></option>';
                $parseStr   .= '<?php }else { ?><option value="<?php echo $'.$values.'[$i] ?>"><?php echo $'.$output.'[$i] ?></option>';
                $parseStr   .= '<?php } ?>';
            }else {
                $parseStr   .= '<option value="<?php echo $'.$values.'[$i] ?>"><?php echo $'.$output.'[$i] ?></option>';
            }
            $parseStr   .= '<?php } ?>';
        }
        $parseStr   .= '</select>';
        return $parseStr;
    }

    /**
     +----------------------------------------------------------
     * checkbox标签解析
     * 格式： <html:checkbox checkboxs="" checked="" />
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    public function _checkbox($attr)
    {
        $tag        = $this->parseXmlAttr($attr,'checkbox');
        $name       = $tag['name'];
        $checkboxes = $tag['checkboxes'];
        $checked    = $tag['checked'];
        $separator  = $tag['separator'];
        $checkboxes = $this->tpl->get($checkboxes);
        $checked    = $this->tpl->get($checked)?$this->tpl->get($checked):$checked;
        $parseStr   = '';
        foreach($checkboxes as $key=>$val) {
            if($checked == $key  || in_array($key,$checked) ) {
                $parseStr .= '<lable><input type="checkbox" checked="checked" name="'.$name.'[]" value="'.$key.'"><span>'.$val.'</span></lable>'.$separator;
            }else {
                $parseStr .= '<lable><input type="checkbox" name="'.$name.'[]" value="'.$key.'"><span>'.$val.'</span></lable>'.$separator;
            }
        }
        return $parseStr;
    }

    /**
     +----------------------------------------------------------
     * radio标签解析
     * 格式： <html:radio radios="name" checked="value" />
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string|void
     +----------------------------------------------------------
     */
    public function _radio($attr)
    {
        $tag        = $this->parseXmlAttr($attr,'radio');
        $name       = $tag['name'];
        $radios     = $tag['radios'];
        $checked    = $tag['checked'];
        $separator  = $tag['separator'];
        $radios     = $this->tpl->get($radios);
        $checked    = $this->tpl->get($checked)?$this->tpl->get($checked):$checked;
        $parseStr   = '';
        foreach($radios as $key=>$val) {
            if($checked == $key ) {
                $parseStr .= '<lable><input type="radio" checked="checked" name="'.$name.'[]" value="'.$key.'"><span>'.$val.'</span></lable>'.$separator;
            }else {
                $parseStr .= '<lable><input type="radio" name="'.$name.'[]" value="'.$key.'"><span>'.$val.'</span></lable>'.$separator;
            }

        }
        return $parseStr;
    }

    /**
     +----------------------------------------------------------
     * list标签解析
     * 格式： <html:list datasource="" show="" />
     *
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $attr 标签属性
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function _list($attr)
    {
        $tag        = $this->parseXmlAttr($attr,'list');
        $id         = $tag['id'];                       //表格ID
        $datasource = $tag['datasource'];               //列表显示的数据源VoList名称
        $pk         = empty($tag['pk'])?'id':$tag['pk'];//主键名，默认为id
        $style      = $tag['style'];                    //样式名
        $name       = !empty($tag['name'])?$tag['name']:'vo';                 //Vo对象名
        $action     = $tag['action'];                   //是否显示功能操作
		$action_width     = $tag['action_width'];
		$action_align     = $tag['action_align'];
        $checkbox   = $tag['checkbox'];                 //是否显示Checkbox
		$attrs = explode(',',$tag['attrs']);
		$nosort = explode(',',$tag['nosort']);
        if(isset($tag['actionlist'])) {
            $actionlist = explode(',',trim($tag['actionlist']));    //指定功能列表
        }

        if(substr($tag['show'],0,1)=='$') {
            $show   = $this->tpl->get(substr($tag['show'],1));
        }else {
            $show   = $tag['show'];
        }
        $show       = explode(',',$show);                //列表显示字段列表

        //计算表格的列数
        $colNum     = count($show);
        if(!empty($checkbox))   $colNum++;
        if(!empty($action))     $colNum++;

        //显示开始
		$parseStr	= "<!-- Think 系统列表组件开始 -->\n";
        $parseStr  .= '<table id="'.$id.'" class="table-list '.$style.'" cellpadding="0" cellspacing="0" border="0">';
        $parseStr  .= '<thead><tr>';
        //列表需要显示的字段
        $fields = array();
        foreach($show as $key=>$val) {
        	$fields[] = explode(':',$val);
        }
		
		$first = 'class="first"';
		
        if(!empty($checkbox) && 'true'==strtolower($checkbox)) {//如果指定需要显示checkbox列
            $parseStr .='<th width="30" '.$first.'><input type="checkbox" onclick="checkAll(\''.$id.'\')"></th>';
        	$first = '';
		}
        foreach($fields as $field) {//显示指定的字段
            $property = explode('|',$field[0]);
            $showname = explode('|',$field[1]);
            if(isset($showname[1]))
			{
				$fieldattrs = explode('~',$showname[1]);
				if(!empty($fieldattrs[0]))
                	$parseStr .= '<th width="'.$fieldattrs[0].'" '.$first.'>';
				else
					$parseStr .= '<th '.$first.'>';
            }else {
                $parseStr .= '<th '.$first.'>';
            }
			$first = '';
            $showname[2] = isset($showname[2])?$showname[2]:$showname[0];
			if(in_array($property[0],$nosort))
				$parseStr .= $showname[0].'</th>';
			else
            	$parseStr .= '<a href="javascript:sortBy(\''.$property[0].'\',\'{$sort}\',\''.ACTION_NAME.'\')" title="'.L('ACCORDING').$showname[2].'{$sortType} ">'.$showname[0].'<eq name="order" value="'.$property[0].'" ><img src="__TMPL__Static/Images/{$sortImg}.gif" align="absmiddle"></eq></a></th>';
        }
		
		//如果指定显示操作功能列
        if(!empty($action))
		{
			if(empty($action_width))
            	$parseStr .= '<th >'.L('HANDLE').'</th>';
			else
				$parseStr .= '<th width="'.$action_width.'">'.L('HANDLE').'</th>';
        }

        $parseStr .= '</tr></thead>';
		$attr_html = '';
		foreach($attrs as $attr)
		{
			$attr_html.= ' '.$attr.'="{$'.$name.'.'.$attr.'}"';
		}
        $parseStr .= '<tbody><volist name="'.$datasource.'" id="'.$name.'" mod="2"><tr'.$attr_html.' class="<eq name="mod" value="0">even</eq>">';	//支持鼠标移动单元行颜色变化 具体方法在js中定义
		
		$first = 'class="first"';
		
        if(!empty($checkbox)) {//如果需要显示checkbox 则在每行开头显示checkbox
            $parseStr .= '<td '.$first.'><input type="checkbox" name="key"	value="{$'.$name.'.'.$pk.'}"></td>';
			$first = '';
        }
        foreach($fields as $field) {
            //显示定义的列表字段
			$td_attrs = '';
			$showname = explode('|',$field[1]);
			if(isset($showname[1]))
			{
				$fieldattrs = explode('~',$showname[1]);
				if(isset($fieldattrs[1]))
					$td_attrs.=' align="'.$fieldattrs[1].'"';
			}
			
            $parseStr   .=  '<td'.$td_attrs.' '.$first.'>';
			$first = '';
            if(!empty($field[2])) {
                // 支持列表字段链接功能 具体方法由JS函数实现
                $href = explode('|',$field[2]);
				$field_names = explode('|',$field[0]);
				$field_name = $field_names[0];
				
                if(count($href)>1) {
                    //指定链接传的字段值
                    // 支持多个字段传递
                    $array = explode('^',$href[1]);
                    if(count($array)>1) {
						$temp = array();
                        foreach ($array as $a){
                            $temp[] =  '\'{$'.$name.'.'.$a.'|addslashes}\'';
                        }
                        $parseStr .= '<span class="pointer" module="'.MODULE_NAME.'" onclick="'.$href[0].'(this,'.implode(',',$temp).',\''.$field_name.'\')">';
                    }else{
                        $parseStr .= '<span class="pointer" module="'.MODULE_NAME.'" href="javascript:;" onclick="'.$href[0].'(this,\'{$'.$name.'.'.$href[1].'|addslashes}\',\''.$field_name.'\')">';
                    }
                }else {
                    //如果没有指定默认传编号值
                    $parseStr .= '<span class="pointer" module="'.MODULE_NAME.'" href="javascript:;" onclick="'.$field[2].'(this,\'{$'.$name.'.'.$pk.'|addslashes}\',\''.$field_name.'\')">';
                }
            }
            if(strpos($field[0],'^')) {
                $property = explode('^',$field[0]);
                foreach ($property as $p){
                    $unit = explode('|',$p);
                    if(count($unit)>1) {
                        $parseStr .= '{$'.$name.'.'.$unit[0].'|'.$unit[1].'} ';
                    }else {
                        $parseStr .= '{$'.$name.'.'.$p.'} ';
                    }
                }
            }else{
                $property = explode('|',$field[0]);
                if(count($property)>1) {
                    $parseStr .= '{$'.$name.'.'.$property[0].'|'.$property[1].'}';
                }else {
                    $parseStr .= '{$'.$name.'.'.$field[0].'}';
                }
            }
            if(!empty($field[2])) {
                $parseStr .= '</span>';
            }
            $parseStr .= '</td>';

        }
        if(!empty($action)) {//显示功能操作
            if(!empty($actionlist[0])) {//显示指定的功能项
				if(!empty($action_align))
                	$parseStr .= '<td align="'.$action_align.'">';
				else
					$parseStr .= '<td>';
				
                foreach($actionlist as $val) {
					if(strpos($val,':')) {
						$a = explode(':',$val);
						$b = explode('|',$a[1]);
						if(count($b)>1) {
							$c = explode('|',$a[0]);
							if(count($c)>1) {
								$parseStr .= '<a href="javascript:;" onclick="'.$c[1].'(this,\'{$'.$name.'.'.$pk.'}\',\''.$pk.'\')"><?php if(0== (is_array($'.$name.')?$'.$name.'["status"]:$'.$name.'->status)){ ?>'.$b[1].'<?php } ?></a><a href="javascript:;" onclick="'.$c[0].'(this,{$'.$name.'.'.$pk.'},\''.$pk.'\')"><?php if(1== (is_array($'.$name.')?$'.$name.'["status"]:$'.$name.'->status)){ ?>'.$b[0].'<?php } ?></a>&nbsp;&nbsp;';
							}else {
								$parseStr .= '<a href="javascript:;" onclick="'.$a[0].'(this,\'{$'.$name.'.'.$pk.'}\',\''.$pk.'\')"><?php if(0== (is_array($'.$name.')?$'.$name.'["status"]:$'.$name.'->status)){ ?>'.$b[1].'<?php } ?><?php if(1== (is_array($'.$name.')?$'.$name.'["status"]:$'.$name.'->status)){ ?>'.$b[0].'<?php } ?></a>&nbsp;&nbsp;';
							}

						}else {
							$parseStr .= '<a href="javascript:;" onclick="'.$a[0].'(this,\'{$'.$name.'.'.$pk.'}\',\''.$pk.'\')">'.$a[1].'</a>&nbsp;&nbsp;';
						}
					}
					else
					{
						$array	=	explode('|',$val);
						if(count($array)>2) {
							$parseStr	.= ' <a href="javascript:;" onclick="'.$array[1].'(this,\'{$'.$name.'.'.$array[0].'}\',\''.$pk.'\')">'.$array[2].'</a>&nbsp;&nbsp;';
						}else{
							$parseStr .= ' {$'.$name.'.'.$val.'}&nbsp;&nbsp;';
						}
					}
                }
                $parseStr .= '</td>';
            }
        }
        $parseStr	.= '</tr></volist></tbody></table>';
        $parseStr	.= "\n<!-- Think 系统列表组件结束 -->\n";
        return $parseStr;
    }
}
?>