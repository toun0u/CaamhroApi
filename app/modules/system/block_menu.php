<?php

if (isset($_SESSION['dims']['moduleid'])) {
	echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_NAVIGATE'],'100%','','',false);
	$toolbar=array();
	$toolbar[_SYSTEM_TOOLBAR_NAVIGATE] = array(
											'title' 	=> $_DIMS['cste']['_DIMS_LABEL_TOOLBARNAVIGATE'],
											'url'		=> "$scriptenv?dims_modulemenuicon="._SYSTEM_TOOLBAR_NAVIGATE,
											'icon'	=> "./common/img/icons/browser-32x32.png"
										);
	$toolbar[_SYSTEM_TOOLBAR_SEARCH] = array(
											'title' 	=> $_DIMS['cste']['_SEARCH'],
											'url'		=> "$scriptenv?dims_modulemenuicon="._SYSTEM_TOOLBAR_SEARCH,
											'icon'	=> "./common/img/icons/search-32x32.png"
										);

	$toolbar[_SYSTEM_TOOLBAR_NEWS] = array(
											'title' 	=> $_DIMS['cste']['_DIMS_LABEL_TOOLBARNEWS'],
											'url'		=> "$scriptenv?dims_modulemenuicon="._SYSTEM_TOOLBAR_NEWS,
											'icon'	=> "./common/img/icons/infobox-32x32.png"
										);

	$toolbar[_SYSTEM_TOOLBAR_ANNOT] = array(
											'title' 	=> $_DIMS['cste']['_DIMS_LABEL_TOOLBARSHARE'],
											'url'		=> "$scriptenv?dims_modulemenuicon="._SYSTEM_TOOLBAR_ANNOT,
											'icon'	=> "./common/img/icons/chat-32x32.png"
										);

	$toolbar[_SYSTEM_TOOLBAR_BOOKMARK] = array(
											'title' 	=> $_DIMS['cste']['_FAVORITES'],
											'url'		=> "$scriptenv?dims_modulemenuicon="._SYSTEM_TOOLBAR_BOOKMARK,
											'icon'	=> "./common/img/icons/favorite-32x32.png"
										);

	$dims_modulemenuicon=dims_load_securvalue('dims_modulemenuicon',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['modulemenuicon']);	// ('field',type=num,get,post,sqlfilter=false)
	echo $skin->create_toolbar($toolbar,$_SESSION['dims']['modulemenuicon'],true,false,"menu");
	$globaltabresult=array();
	$globaldimsop='';
	$moduleid=$_SESSION['dims']['moduleid'];
	switch($_SESSION['dims']['modulemenuicon']) {

		case _SYSTEM_TOOLBAR_NAVIGATE:
			break;

		case _SYSTEM_TOOLBAR_SEARCH:
			$dims_op='search';
			$globaltitle=$_DIMS['cste']['_SEARCH'];
			$dims_displaysearch="";
			break;
		case _SYSTEM_TOOLBAR_NEWS:
			$dims_op='searchnewscontent';
			$globaltitle=_DIMS_LABEL_NEWS;
			break;

		case _SYSTEM_TOOLBAR_ANNOT:
			$dims_op='searchannot';
			$globaltitle=$_DIMS['cste']['_DIMS_LABEL_ANNOTATION'];
			break;

		case _SYSTEM_TOOLBAR_BOOKMARK:
			$globaltitle=$_DIMS['cste']['_FAVORITES'];
			break;
	}
	echo $skin->close_simplebloc();
}
?>
