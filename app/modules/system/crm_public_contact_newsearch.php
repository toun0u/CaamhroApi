<?
// initialisation du module de recherche sur
require_once(DIMS_APP_PATH . "/modules/system/class_search.php");
$dimsearch = new search($dims);

// ajout des objects sur lequel la recherche va se baser
$dimsearch->addSearchObject(dims_const::_DIMS_MODULE_SYSTEM, dims_const::_SYSTEM_OBJECT_CONTACT,$_DIMS['cste']['_DIMS_LABEL_CONTACTS']);
$dimsearch->addSearchObject(dims_const::_DIMS_MODULE_SYSTEM, dims_const::_SYSTEM_OBJECT_TIERS,$_DIMS['cste']['_DIMS_LABEL_GROUP_LIST']);
// reinitialise la recherche sur ce module courant, n'efface pas le cache result
$dimsearch->initSearchObject();
?>
<script type="text/javascript" src="/js/dims_searchbar.js"></script>

<table style="width:99%;" cellpadding="0" cellspacing="2">
	<tr>
		<td valign="top" style="width:76%;">
			<div id="searchBar_obj_container" style="height:65px;">
				<div id="searchBar_obj" style="position:relative;">
					<input id="searchBar_obj_bar" name="searchBar_obj_bar" onkeyup="javascript:dimsContactSearch();" type="text"/>
					<input id="searchBar_obj_sub" type="submit" value="" onclick="dimsContactSearch(event)" />
				</div>
			</div>
			<div id="content_result" style="border-bottom:0px;">
				<div id="searchresult" style="width:100%;clear:both;min-height:350px;"><span style="height:100px"></span></div>
			</div>
		</td>
		<td>
			<?
			echo '<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td align="left">';
			echo $skin->open_widgetbloc($_DIMS['cste']['_DIMS_LABEL_LAST_SEARCH'], 'width:100%;', 'padding-bottom:1px;padding-left:10px;vertical-align:bottom;color:#cccccc;', '','26px', '26px', '-15px', '-7px', '', '', '');
			// recherche des derni
