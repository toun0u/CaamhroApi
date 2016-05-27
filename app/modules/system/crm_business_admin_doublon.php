<script language="javascript">
function checkDoublons() {
	var divelem=document.getElementById('searchduplicate');
	divelem.innerHTML='<img src="./common/img/loading.gif">';
	dims_xmlhttprequest_todiv('/admin.php','cat=0&action=801&part=801&op=search_doublons','','searchduplicate');
}

function viewDetailDuplicate(key) {
	dims_xmlhttprequest_todiv('/admin.php','cat=0&action=801&part=801&op=detail_doublon&key='+key,'','detaildoublon');
}

function confirmDelete(id_user) {
	if (confirm("<? echo $_DIMS['cste']['_DIRECTORY_CONFIRM_DELETECONTACT']; ?>")) {
		dims_xmlhttprequest("admin.php","cat=0&action=801&part=801&op=deleteUser&iduser="+id_user);
				document.location.href="/admin.php";
	}
}
</script>
<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

echo $skin->open_simplebloc($_DIMS['cste']['_MANAGE_DOUBLONS'],'width:39%;float:left;');
// liste des elements
?>
<div style="float:left;width:50%;margin:0 auto;"><input style="width:130px;" type="button" href="javascript:void(0);" onclick="javascript:checkDoublons();" value="<? echo $_DIMS['cste']['_RSS_LABELTAB_MODIFY']; ?>"></div>

<div id="searchduplicate" style="width:100%;float:left;">
<?
require_once(DIMS_APP_PATH . '/modules/system/crm_business_admin_searchdoublons.php');
?>
</div>
<?
echo $skin->close_simplebloc();
echo $skin->open_simplebloc("",'width:59%;float:right;');
?>
<div id="detaildoublon" style="width:100%;float:left;"></div>

<div id="detailavancedoublon" style="width:100%;margin-top:20px;float:left;"></div>
<?
echo $skin->close_simplebloc();
?>
