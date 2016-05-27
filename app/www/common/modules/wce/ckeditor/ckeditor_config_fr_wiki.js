/*
Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	 config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.toolbar = 'DimsToolbar';

	config.toolbar_DimsToolbar =
	[

		{ name: 'document', items : [ 'Source','Ajaxsave','Ajaxclose' ] },
		{ name: 'clipboard', items : [  'Cut','Copy','Paste','PasteText','PasteFromWord' ] },
		{ name: 'insert', items : [ 'Image','Flash','Table','dimsobjects','Youtube','MediaEmbed']},
		{ name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
		{ name: 'colors', items : [ 'TextColor','BGColor' ] },
		{ name: 'basicstyles', items : [ 'RemoveFormat','-' ,'Bold','Italic','Strike'] },
		{ name: 'justifyClasses', items : ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'] },
		{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent'] },
		{ name: 'links', items : [ 'Link','Unlink','Anchor' ]},
		{ name: 'max', items : [ 'Maximize' ]}
	];
	config.removePlugins = 'elementspath';

	config.isWiki = true;

	config.syntaxhighlightLangDefault = 'php';

	config.extraPlugins = 'ajaxsave,ajaxclose,ajax,wiki,removespace,youtube,scayt,dimsobjects,syntaxhighlight'; // ,wiki,dimsobjects,devtools

	config.filebrowserBrowseUrl = "../../../admin-light.php?dims_op=doc_selectfile";
	config.filebrowserFlashBrowseUrl = "../../../admin-light.php?dims_op=doc_selectflash";
	config.filebrowserImageBrowseUrl = "../../../admin-light.php?dims_op=doc_selectimage&img=1";
	config.filebrowserVideoBrowseUrl  = "../../../admin-light.php?dims_op=doc_selectvideo";
	config.filebrowserWindowWidth = CKEDITOR.config.width*0.7;
	config.filebrowserWindowHeight = CKEDITOR.config.height*0.7;
	config.resize_maxWidth = CKEDITOR.config.width;

	//config.scayt_autoStartup = true;
	//config.scayt_sLang ="fr_FR";
	//config.scayt_maxSuggestions = 4;

	config.enterMode = CKEDITOR.ENTER_BR;
	config.shiftEnterMode = CKEDITOR.ENTER_P;
	//config.tabSpaces = 8;
	// Se the most common block elements.
	config.format_tags = 'p;h1;h2;h3;pre';

	// Make dialogs simpler.
	config.removeDialogTabs = 'image:advanced;link:advanced';

	config.font_names =
            'Arial/Arial, Helvetica, sans-serif;' +
            'Comic Sans MS/Comic Sans MS, cursive;' +
            'Courier New/Courier New, Courier, monospace;' +
            'Georgia/Georgia, serif;' +
            'Lucida Sans Unicode/Lucida Sans Unicode, Lucida Grande, sans-serif;' +
            'Tahoma/Tahoma, Geneva, sans-serif;' +
            'Times New Roman/Times New Roman, Times, serif;' +
            'Trebuchet MS/Trebuchet MS, Helvetica, sans-serif;' +
            'Calibri/Calibri, Verdana, Geneva, sans-serif;' + /* here is your font */
            'Verdana/Verdana, Geneva, sans-serif';
	config.allowedContent = true; // pour youtube
};
