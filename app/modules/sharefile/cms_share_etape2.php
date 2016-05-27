<div class="dims_form" style="float:left; width:100%;padding-top:10px;">
    <div id="shareblock1" style="padding-top:5px;clear:both;float:left;width:100%;">
        <?
        //// zone de recherche
        echo "<div style=\"float:left;width:49%;display:block;\">";
        ?>
		<span style="width:10%;display:block;float:left;height:30px;">
                <img src="/common/modules/sharefile/img/users.png">
        </span>
		<span style="width:90%;display:block;float:left;font-size:20px;color:#BABABA;font-weight:bold;margin-top:15px;height:30px;">
                <? echo $_DIMS['cste']['_DIMS_LABEL_TICKET_DEST']; ?>
        </span>
        <span style="clear:both;width:100%;display:block;float:left;font-size:16px;color:#BABABA;font-weight:bold;">
        Utilisateurs
		<? //echo $_DIMS['cste']['_DIMS_LABEL_INTERNAL_SOURCES'];
		?>
        </span>
        <?
        if (isset($_SESSION['share']['currentsearch'])) $nomsearch=$_SESSION['share']['currentsearch'];
        ?>
        <input value="<? echo $nomsearch;?>" type="text" onkeyup="javascript:searchUserShare();" id="nomsearch" name="nomsearch" size="16">
        <img style="cursor: pointer;" onclick="javascript:searchFileInitSearch();" src="./common/img/delete.png" border="0">
        <div id="lst_tempuser" style="width:90%;display:block;float:left;"></div>
        </div>

        <?
        // personne selectionnee
        echo "<div id=\"lstselectedusers\" style=\"float:left;width:49%;display:block;\">";
        echo "</div>";
        ?>
        </div>
    </div>
    <div style="padding-top:20px;clear:both;float:left;width:100%;">
            <span style="width:50%;display:block;float:left;text-align:right;"><a style="text-decoration:none;padding-right:50px;" href="<? echo dims_urlencode($dims->getScriptEnv()."?op=add_share&etape=1"); ?>"><img style="border:0px;" src="./common/modules/sharefile/img/back.png" alt="<? echo $_DIMS['cste']['_DIMS_PREVIOUS']; ?>"></a></span>
            <span id="sharefile_button"  style="width:50%;display:block;float:left;display:none;"><a style="text-decoration:none;" href="<? echo dims_urlencode($dims->getScriptEnv()."?op=add_share&etape=3"); ?>"><img style="padding-left:50px;border:0px;" src="./common/modules/sharefile/img/forward.png" alt="<? echo $_DIMS['cste']['_DIMS_NEXT']; ?>"></a></span>
    </div>
</div>

<script language="JavaScript" type="text/JavaScript">
var timerdisplayresult;
function activeAddContact() {
    $('#sharefile_addcontact').css('display','block');
}

function activeSharefileButton(val) {
    if (val) $('#sharefile_button').css('display','block');
    else $('#sharefile_button').css('display','none');
}

function searchFileInitSearch() {
    $('#nomsearch').value="";
    $('#nomsearch').focus();
    dims_xmlhttprequest_tofunction('<? echo $dims->getScriptEnv(); ?>','op=sharefile_initsearch',searchUserShareExec);
}
function searchUserShare() {
    clearTimeout(timerdisplayresult);
    timerdisplayresult = setTimeout("searchUserShareExec()", 300);
}

function searchUserShareExec() {
    var nomsearch=$('#nomsearch').val();
    dims_xmlhttprequest_todiv('<? echo $dims->getScriptEnv(); ?>','op=sharefile_search_user&nomsearch='+nomsearch,'||',"lst_tempuser","lstselectedusers");
}

function updateUserActionFromSelected(op,id_user,input) {
    dims_xmlhttprequest_tofunction('<? echo $dims->getScriptEnv(); ?>','op='+op+'&id_user='+id_user,searchUserShareExec);
}

function updateContactActionFromSelected(op,id_contact,input) {
    dims_xmlhttprequest_tofunction('<? echo $dims->getScriptEnv(); ?>','op='+op+'&id_contact='+id_contact,searchUserShareExec);
}

function updateGroupActionFromSelected(op,id_grp,input) {
    dims_xmlhttprequest_tofunction('<? echo $dims->getScriptEnv(); ?>','op='+op+'&id_grp='+id_grp,searchUserShareExec);
}

window.onload=function(){
	$('#nomsearch').focus();
	searchUserShareExec();
}
</script>

