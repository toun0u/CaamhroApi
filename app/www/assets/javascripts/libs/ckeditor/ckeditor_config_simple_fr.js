/*
Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.toolbar = 'DimsToolbar';

	config.toolbar_DimsToolbar =
	[

		{ name: 'document', items : [ 'Source','Ajaxsave','Ajaxclose' ] },
		{ name: 'clipboard', items : [ 'Scayt', 'Cut','Copy','Paste','PasteText','PasteFromWord' ] },
		//{ name: 'insert', items : ['dimsobjects','Image','Flash','Table','Rule','SpecialChar']},
		{ name: 'insert', items : [ 'Image','Flash','Table','dimsobjects']},
				'/',
		{ name: 'styles', items : [ 'Styles', 'Font', 'FontSize', 'TextColor'] },
		{ name: 'basicstyles', items : [ 'RemoveFormat','-' ,'Bold','Italic','Underline','Strike'] },
		{ name: 'justifyClasses', items : ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'] },
		{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent'] },
		{ name: 'links', items : [ 'Link','Unlink' ]}
	];
	config.removePlugins = 'elementspath';

	config.extraPlugins = 'ajax,wiki,removespace,scayt,dimsobjects';

	config.filebrowserBrowseUrl = "../../../admin-light.php?dims_op=doc_selectfile";
	config.filebrowserFlashBrowseUrl = "../../../admin-light.php?dims_op=doc_selectflash";
	config.filebrowserImageBrowseUrl = "../../../admin-light.php?dims_op=doc_selectimage&img=1";
	config.filebrowserVideoBrowseUrl  = "../../../admin-light.php?dims_op=doc_selectvideo";
	config.filebrowserWindowWidth = CKEDITOR.config.width*0.7;
	config.filebrowserWindowHeight = CKEDITOR.config.height*0.7;
	config.resize_maxWidth = CKEDITOR.config.width;

	config.scayt_autoStartup = true;
	config.scayt_sLang ="fr_FR";
	config.scayt_maxSuggestions = 4;

	config.enterMode = CKEDITOR.ENTER_BR;
	config.shiftEnterMode = CKEDITOR.ENTER_P;
	//config.tabSpaces = 8;
	// config.allowedContent = true; // pour youtube
};
