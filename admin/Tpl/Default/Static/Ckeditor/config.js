/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	config.skin = 'kama' ;
	
	config.toolbar_Default = [   
    	['Paste','PasteText','PasteFromWord','-','Undo','Redo'],
		['Find','Replace','SelectAll'],
		['Form','Checkbox','Radio','TextField','Textarea','Select','Button','ImageButton','HiddenField','HorizontalRule','Iframe'],
		['Bold','Italic','Underline','Strike','Subscript','Superscript','RemoveFormat'],
		['NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv'],
		['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl'],
		['Link','Unlink','Anchor'],
		['Image','Flash','Table','SpecialChar','PageBreak'],
		['Styles','Format','Font','FontSize'],
		['TextColor','BGColor'],
		['Source','ShowBlocks','Maximize']
	];
	
	config.toolbar_Basic = [   
    	['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink']
	]; 
};
