<div class="dims_form" style="float:left; width:100%;padding-top:10px;">
    <div id="shareblock1" style="padding-top:5px;clear:both;float:left;width:100%;">
        <?
        //// zone de recherche
        echo "<div style=\"float:left;width:49%;display:block;\">";
        ?>
		<span style="width:10%;display:block;float:left;height:30px;">
                <img src="/common/modules/system/img/users.png">
        </span>
		<span style="width:90%;display:block;float:left;font-size:20px;color:#BABABA;font-weight:bold;margin-top:15px;height:30px;">
                <? echo $_DIMS['cste']['_DIMS_DEST']; ?>
        </span>
        <span style="clear:both;width:100%;display:block;float:left;font-size:16px;color:#BABABA;font-weight:bold;">
        <? echo $_DIMS['cste']['_DIMS_LABEL_INTERNAL_SOURCES'];?>
        </span>
        <?
        if (isset($_SESSION['project']['currentsearch'])) $nomsearch=$_SESSION['project']['currentsearch'];
		else $nomsearch="";
        ?>
        <input value="<? echo $nomsearch;?>" type="text" onkeyup="javascript:searchUserProject();" id="nomsearch" name="nomsearch" size="16">
        <img style="cursor: pointer;" onclick="javascript:searchUserProject();" src="./common/img/search.png" border="0">
        &nbsp;<img style="cursor: pointer;" onclick="javascript:searchProjectInitSearch();" src="./common/img/delete.png" border="0">
        <div id="lst_tempuser" style="width:90%;overflow:auto;display:block;float:left;"></div>
        </div>
        <?
        // personne sélectionnée
        echo "<div id=\"lstselectedusers\" style=\"float:left;width:49%;display:block;\">";
        echo "</div>";
        ?>
        </div>
    </div>
    <div style="padding-top:20px;clear:both;float:left;width:100%;">
            <span style="width:50%;display:block;float:left;text-align:right;"><a style="text-decoration:none;padding-right:50px;" href="<? echo dims_urlencode($dims->getScriptEnv()."?op=add_project&etape=1"); ?>"><img style="border:0px;" src="./common/modules/sharefile/img/back.png" alt="<? echo $_DIMS['cste']['_DIMS_PREVIOUS']; ?>"></a></span>
            <span id="project_button"  style="width:50%;display:block;float:left;display:none;"><a style="text-decoration:none;" href="<? echo dims_urlencode($dims->getScriptEnv()."?op=add_project&etape=3"); ?>"><img style="padding-left:50px;border:0px;" src="./common/modules/sharefile/img/forward.png" alt="<? echo $_DIMS['cste']['_DIMS_NEXT']; ?>"></a></span>
    </div>
</div>

<script language="JavaScript" type="text/JavaScript">
var timerdisplayresult;
function activeAddContact() {
    $("project_addcontact").setStyle({display:'block'});
}

function activeProjectButton(val) {
    if (val) $("project_button").setStyle({display:'block'});
    else $("project_button").setStyle({display:'none'});
}

function searchProjectInitSearch() {
    $("nomsearch").value="";
    $("nomsearch").focus();
    dims_xmlhttprequest_tofunction('<? echo $dims->getScriptEnv(); ?>','op=project_initsearch',searchUserProjectExec);
}
function searchUserProject() {
    clearTimeout(timerdisplayresult);
    timerdisplayresult = setTimeout("searchUserProjectExec()", 300);
}

function searchUserProjectExec() {
    var nomsearch=$("nomsearch").value;
    dims_xmlhttprequest_todiv('<? echo $dims->getScriptEnv(); ?>','op=project_search_user&nomsearch='+nomsearch,'||',"lst_tempuser","lstselectedusers");
}

function updateUserActionFromSelected(op,id_user,input) {
    dims_xmlhttprequest_tofunction('<? echo $dims->getScriptEnv(); ?>','op='+op+'&id_user='+id_user,searchUserProjectExec);
}

function updateContactActionFromSelected(op,id_contact,input) {
    dims_xmlhttprequest_tofunction('<? echo $dims->getScriptEnv(); ?>','op='+op+'&id_contact='+id_contact,searchUserProjectExec);
}

function updateGroupActionFromSelected(op,id_grp,input) {
    dims_xmlhttprequest_tofunction('<? echo $dims->getScriptEnv(); ?>','op='+op+'&id_grp='+id_grp,searchUserProjectExec);
}

window.onload=function(){
	$("nomsearch").focus();
	searchUserProjectExec();
}
</script>
