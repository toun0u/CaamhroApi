<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$nbdays=60;
$date_start=date('d/m/Y');
$maxtoday = mktime(0,0,0,date('n'),date('j')+$nbdays,date('Y'));
$date_end=date('d/m/Y',$maxtoday);
?>
<div id="desktopproject" style="background: #FFFFFF none repeat scroll 0% 0%; float: left; width: 100%;">
<form name="form_etape1" method="post" action="<? echo dims_urlencode($dims->getScriptEnv()."?op=project_valid_etape1"); ?>">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("project_label");
	$token->field("project_description");
	$token->field("project_date_start");
	$token->field("project_date_end");
	$token->field("project_state");
	$token->field("nomsearch");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<div class="dims_form" style="float:left; width:99%;padding-top:20px;">
	<div style="padding:2px;">
		<span style="width:10%;display:block;float:left;">
			<img src="/common/modules/system/img/properties.png">
		</span>
		<span style="width:90%;display:block;float:left;font-size:20px;color:#BABABA;font-weight:bold;">
			<? echo $_DIMS['cste']['_DIMS_PROPERTIES']; ?>
		</span>
	</div>
	<div style="padding:2px;float:left;width:100%;">
		<p>
			<label><?php echo $_DIMS['cste']['_DIMS_LABEL_TITLE'] ?></label>
			<input class="text" type="text" onkeyup="javascript:projectFileCheck();" style="width:350px;" id="project_label" name="project_label" value="<? if(!empty($_SESSION['project']['label'])) echo $_SESSION['project']['label']; ?>" tabindex="1" />
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_DIMS_LABEL_DESCRIPTIF'] ?></label>
			<textarea class="text" style="width:350px;height:60px"  name="project_description" tabindex="2"><? if(!empty($_SESSION['project']['description']))  echo $_SESSION['project']['description']; ?></textarea>
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_INFOS_START_DATE']; ?></label>
				<input style="width:100px;" class="text" type="text" name="project_date_start" value="<? echo $date_start; ?>" id="project_date_start" value="" tabindex="3"/>
				<a href="javascript:void(0);" onclick="javascript:dims_calendar_open('project_date_start', event,'');"><img src="./common/img/calendar/calendar.gif" width="31" height="18" align="top" border="0"></a>
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_INFOS_END_DATE']; ?></label>
				<input style="width:100px;" class="text" type="text" name="project_date_end" value="<? echo $date_end; ?>" id="project_date_end" value="" tabindex="4" />
				<a href="javascript:void(0);" onclick="javascript:dims_calendar_open('project_date_end', event,'');"><img src="./common/img/calendar/calendar.gif" width="31" height="18" align="top" border="0"></a>
		</p>
		<p>
			<label><?php echo $_DIMS['cste']['_DIMS_LABEL_ACTIVE'] ?> ? </label>
				<?php echo $_DIMS['cste']['_DIMS_YES'] ?><input type="radio" name="project_state" id="project_state" value="1" checked="checked"/>
				<?php echo $_DIMS['cste']['_DIMS_NO'] ?><input type="radio" name="project_state" id="project_state" value="0"/>
		</p>
		<div style="100%">
			<div id="project_msg_calendar" style="width:350px;margin:0 auto;color:#FF0000;"></div>
		</div>
		<p>
			<label><?php echo $_DIMS['cste']['_DIMS_LABEL_RESPONSIBLE'] ?></label>
			<input class="text" type="text" onkeyup="javascript:projectpersonsearch();" style="width:350px;" id="nomsearch" name="nomsearch" value="" />
		</p>
	</div>
	<div id="lstselectedusers" name="lstselectedusers" style="padding:2px;float:left;width:100%;"></div>
	<div id="projectfile_button" style="padding:2px;clear:both;float:left;width:100%;display:block;display:none;">
		<span style="width:50%;display:block;float:left;">&nbsp;</span>
		<span style="width:50%;display:block;float:left;"><?php echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"./common/img/add.gif","javascript:document.form_etape1.submit();"); ?></span>
	</div>
</div>
</form>
</div>
<script language="JavaScript" type="text/JavaScript">
function projectCheckDate() {
	if ($('#project_date_start').val()!="") {
		dims_xmlhttprequest_todiv('<? echo $dims->getScriptEnv(); ?>','op=projectfile_check&datefinished='+$('#project_timestp_finished').val(),'||',"project_msg_calendar");
	}
}

function projectFileCheck() {
	if ($('#project_label').val()!="") $('#projectfile_button').css('display','block');
	else $('#projectfile_button').css('display', 'none');

}

function projectpersonsearch() {
    clearTimeout(timerdisplayresult);
    timerdisplayresult = setTimeout("searchUserResponsibleProject()", 300);
}

function searchUserResponsibleProject() {
    var nomsearch=$("nomsearch").value;
    dims_xmlhttprequest_todiv('<? echo $dims->getScriptEnv(); ?>','op=project_search_user_resp&nomsearch='+nomsearch,'||',"lstselectedusers");
}

$("project_label").focus();
projectFileCheck();
</script>
