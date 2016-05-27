<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<form name="form_etape1" method="post" action="<? echo dims_urlencode($dims->getScriptEnv()."?op=sharefile_valid_etape1"); ?>">
<div class="dims_form" style="float:left; width:80%;padding-top:20px;">
	<div style="padding:2px;">
		<span style="width:10%;display:block;float:left;">
			<img src="/modules/sharefile/img/properties.png">
		</span>
		<span style="width:90%;display:block;float:left;font-size:20px;color:#BABABA;font-weight:bold;">
			Configuration du partage
		</span>
	</div>
	<div style="padding:2px;clear:both;float:left;width:100%;">
		<p>
			<label>Titre</label>
			<input class="text" type="text" onkeyup="javascript:shareFileCheck();" style="width:350px;" id="share_title" name="share_title" value="<? echo $_SESSION['share']['title']; ?>" tabindex="1" />

		</p>
		<p>
			<label>Descriptif</label>
			<textarea class="text" style="width:350px;height:60px"  name="share_descriptif" tabindex="2"><? echo $_SESSION['share']['descriptif']; ?></textarea>
		</p>
		<p>
			<label>Date de fin</label>
				<input style="width:100px;" class="text" type="text" name="share_timestp_finished" id="share_timestp_finished" value="<? echo $_SESSION['share']['timestp_finished']; ?>" tabindex="3" onchange="javascript:shareCheckDate();"/>
				<a href="javascript:void(0);" onclick="javascript:dims_calendar_open('share_timestp_finished', event,'shareCheckDate();');"><img src="./common/img/calendar/calendar.gif" width="31" height="18" align="top" border="0"></a>

		</p>
		<div style="width:100%;">
			<div id="share_msg_calendar" style="width:350px;margin:0 auto;color:#FF0000;"></div>
		</div>
		<?
		// test si activation code unique ou non
		if ($sharefile_param->fields['uniquecode']) {
		?>
		<p style="margin-top:10px;color:#8A8A8A;"><img src="./common/img/lock.gif" alt="">&nbsp;Si vous souhaitez prot&eacute;ger l'acc&egrave;s &agrave; ces fichiers, saisissez un code pour activer la consultation.</p>
		<p>
			<label>Code</label>
			<input class="text" type="text" name="share_code" style="width:100px" value="<? echo $_SESSION['share']['code']; ?>" tabindex="4">
			<? echo "Maximum ".$sharefile_param->fields['nbcar']." caract&egrave;res."; ?>
		</p>
		<?
		}
		?>
	</div>
	<div name="sharefile_button" id="sharefile_button" style="padding:2px;clear:both;float:left;width:100%;display:none;">
		<span style="width:50%;display:block;float:left;">&nbsp;</span>
		<span style="width:50%;display:block;float:left;"><a style="text-decoration:none;" href="javascript:void(0);" onclick="javascript:document.form_etape1.submit();"><img style="padding-left:50px;border:0px;" src="./common/modules/sharefile/img/forward.png" alt="<? echo $_DIMS['cste']['_DIMS_NEXT']; ?>"></a></span>
	</div>
</div>
</form>
<script language="JavaScript" type="text/JavaScript">
function shareCheckDate() {
	if ($('#share_timestp_finished').value!="") {
		dims_xmlhttprequest_todiv('<? echo $dims->getScriptEnv(); ?>','op=sharefile_check&datefinished='+$('#share_timestp_finished').value,'||',"share_msg_calendar");
	}
}

function shareFileCheck() {

	if ($('#share_title').val()!="") $('#sharefile_button').css('display','block');
	else $('#sharefile_button').css('display', 'none');

}

$('#share_title').focus();
shareFileCheck();
</script>
