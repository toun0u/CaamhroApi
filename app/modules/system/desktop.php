<script type="text/javascript" src="/js/desktop_search.js"></script>
<?php

if (isset($_SESSION['dims']['_DIMS_SPECIFIC']) && $_SESSION['dims']['_DIMS_SPECIFIC']) {
	require_once (DIMS_APP_PATH . "/modules/system/".$_SESSION['dims']['_PREFIX']."/desktop_javascript.php");
}
$currentworkspace=$dims->getWorkspaces($_SESSION['dims']['workspaceid']);
$op=dims_load_securvalue('op',dims_const::_DIMS_CHAR_INPUT,true,true,false);

$workspace=$dims->getWorkspaces($_SESSION['dims']['workspaceid']);

if (!isset($_SESSION['dims']['selectedsearch'])) {
	$_SESSION['dims']['selectedsearch']=array();
}

if (!isset($_SESSION['dims']['nbselectedsearch'])) {
	$_SESSION['dims']['nbselectedsearch']=0;
}

if (isset($_SESSION['dims']['modsearch']['expression_brut'])) {
	$expression_brute=$_SESSION['dims']['modsearch']['expression_brut'];
}
else {
	$expression_brute="";
}

$reset_object=dims_load_securvalue('reset_object',dims_const::_DIMS_NUM_INPUT,true,true);
$resetunique_object=dims_load_securvalue('resetunique_object',dims_const::_DIMS_NUM_INPUT,true,true);

$_SESSION['dims']['current_object']['mustview']=false;
//dims_print_r($_SESSION['dims']['current_object']);die();

if ($reset_object || $resetunique_object) {
	unset($_SESSION['dims']['current_object']['id_record']);
	unset($_SESSION['dims']['current_object']['id_object']);
	unset($_SESSION['dims']['current_object']['id_module']);
	if ($reset_object) dims_redirect('/admin.php');
}

if (!isset($_SESSION['dims']['desktop_view_workspace'])) $_SESSION['dims']['desktop_view_workspace']=0;
$desktop_view_workspace=dims_load_securvalue('desktop_view_workspace',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['desktop_view_workspace'],0);

if (!isset($_SESSION['dims']['desktop_view_connexion'])) $_SESSION['dims']['desktop_view_connexion']=0;
$desktop_view_connexion=dims_load_securvalue('desktop_view_connexion',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['desktop_view_connexion'],0);

$typetag = dims_load_securvalue('typetag', dims_const::_DIMS_NUM_INPUT, true, true);

if (isset($_GET['typetag'])) {
	$_SESSION['dims']['current_typetag']=$typetag;
}
?>
<div style="float: left; width: 100%; display: block;margin:0px;">
	<div style="margin:5px auto 10px;display:block;width:630px;clear:both;">
		<input style="width:450px;" id="searchBar_obj_bar" name="searchBar_obj_bar" class="ui-button ui-autocomplete" type="text" onkeypress="javascript:dims_word_keyupExec(event);"
		 onkeyup="javascript:dims_word_keyup(event);" value="<?
			   if (mb_detect_encoding($expression_brute, "UTF-8") == "UTF-8") {
				   $expression_brute = utf8_encode($expression_brute);
			   }
			   $expr=stripslashes($expression_brute);
			   echo str_replace('"','&quot;',$expr);
			   ?>" />
		<button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" onclick="searchWord();">
			<span class="ui-icon ui-icon-search"></span>
			<span class="ui-button-text">Search</span>
		</button>

		<?
		if ($expression_brute!='') {
			$styledelete='visibility:visible';
		}
		else {
			$styledelete='visibility:hidden;';
		}
			echo '<button id="zonebuttondelete" style="'.$styledelete.'" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-secondary" onclick="deleteSelected()" title="'.$_DIMS['cste']['_LABEL_DELETE_QUERY'].'">
			<span class="ui-button-text">'.$_DIMS['cste']['_DIMS_LABEL_CANCEL'].'</span>
			<span class="ui-button-icon-secondary ui-icon ui-icon-cancel"></span>
		</button>';

		?>
	</div>
</div>
<div style="float: left; width: 100%; display: block;margin:0px;">

	<div style="width:80%;clear:both;float:left;">
			<? echo $dims->getHistoryObject(); ?>
	</div>

</div>
<div style="float: left; width: 48%; display: block;margin:1px;"  id="result_content" class="ui-widget ui-widget-content ui-corner-all">
<?
if ($expression_brute!='') {
	require_once(DIMS_APP_PATH . '/modules/system/desktop_search.php');
}
else {
	require_once(DIMS_APP_PATH . '/modules/system/desktop_activities.php');
}
?>
</div>
<div style="display:block;float:left;width:51%;margin:1px;" id="desktop_right_side" class="ui-widget ui-widget-content ui-corner-all">
	<div style="position:relative;margin-top: 4px;" id="object_container">
		<div id="object_onglet" style="width:100%;float:left;"></div>
				<div id="object_content" style="padding:0px;width:100%;display:block;float:left;">
		<?

				//if (!$_SESSION['dims']['current_object']['mustview']) {
					//unset($_SESSION['dims']['current_object']);
				//}

		// verification si pas en train de consulter un objet
		/*dims_xmlhttprequest_todiv('admin-light.php','<? echo $_SERVER['QUERY_STRING'];?>&dims_op=initDesktop&block_id='+i,"||",'object_onglet','object_content');*/
				//if (isset($_SESSION['dims']['current_object']['mustview']) && $_SESSION['dims']['current_object']['mustview'] && isset($_SESSION['dims']['current_object']['id_record']) && isset($_SESSION['dims']['current_object']['id_object']) && isset($_SESSION['dims']['current_object']['id_module'])) {
				 if (isset($_SESSION['dims']['current_object']['id_record']) && isset($_SESSION['dims']['current_object']['id_object']) && isset($_SESSION['dims']['current_object']['id_module'])) {
					echo '<script type="text/javascript">
							$(document).ready(function(){
								viewPropertiesObject('.$_SESSION['dims']['current_object']['id_object'].','.$_SESSION['dims']['current_object']['id_record'].','.$_SESSION['dims']['current_object']['id_module'].',1);
							});
						</script>';
		}
		else {
			require_once(DIMS_APP_PATH . '/modules/system/desktop_connexion.php');
			require_once(DIMS_APP_PATH . '/modules/system/desktop_tags_search.php');
			require_once(DIMS_APP_PATH . '/modules/system/desktop_shortcuts.php');
		}
		?>


		</div>
	</div>

</div>

<?
// javascript
require_once(DIMS_APP_PATH . "/modules/system/desktop_javascript.php");
?>
