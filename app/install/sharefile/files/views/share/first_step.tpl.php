<?php
$share = $this->get('share');
$shareparam = $this->get('share_param');

if(empty($share['timestp_finished'])) {
	$date_finished = date('d/m/Y', mktime(0,0,0,date('n')+2));
}
else {
	$date = dims_timestamp2local($share['timestp_finished']);
	$date_finished = $date['date'];
}
?>
<form name="form_etape1" method="post" action="<?= dims_urlencode($this->get('urlbase')->addParams(array('op' => 'share', 'action' => 'save_first_step'))); ?>">
<div class="dims_form" style="float:left; width:100%;padding-top:20px;">
	<div style="padding:2px;overflow:hidden">
		<span style="width:12%;display:block;float:left;">
			<img src="/modules/sharefile/img/icon_dossier_etape.png">
		</span>
		<span style="width:80%;display:block;float:left;font-size:20px;color:#424242;font-weight:bold; margin-left: 20px; line-height:63px;">
			Configuration du partage
		</span>
	</div>
	<div style="padding:2px;clear:both;float:left;width:100%;">
		<p>
			<label>Titre</label>
			<input class="text" type="text" onchange="javascript:shareFileCheck();" style="width:350px;" id="share_label" name="share_label" value="<?= $share['label']; ?>" tabindex="1" />

		</p>
		<p>
			<label>Descriptif</label>
			<textarea class="text" style="width:350px;height:60px"  name="share_description" tabindex="2"><?= $share['description']; ?></textarea>
		</p>
		<p>
			<label>Date de fin</label>
			<input style="width:100px;" class="text datepicker" type="text" name="share_timestp_finished" id="share_timestp_finished" value="<?= $date_finished; ?>" tabindex="3"/>
		</p>
		<div style="width:100%;">
			<div id="share_msg_calendar" style="width:350px;margin:0 auto;color:#FF0000;"></div>
		</div>
		<?php if ($shareparam['uniquecode']): ?>
			<p style="margin-top:10px;color:#424242;font-weight:bold">
				<img src="/modules/sharefile/img/icon_cadenas.png" alt="securise" style="float: left; margin-left: 20px;">
				<span style="float:left;line-height:37px;margin-left: 10px;width:90%;">&nbsp;Si vous souhaitez prot&eacute;ger l'acc&egrave;s &agrave; ces fichiers, saisissez un code pour activer la consultation.</span>
			</p>
			<p>
				<label>Code</label>
				<input class="text" type="text" name="share_code" style="width:100px" value="<?= $share['code']; ?>" tabindex="4">
				<?= "Maximum ".$shareparam['nbcar']." caract&egrave;res."; ?>
			</p>
		<? endif; ?>
	</div>
	<div name="sharefile_button" id="sharefile_button" style="padding: 2px; clear: both; display: block; float: right; width: 100%;">
		<span style="display: block; float: left; width: 50%;">&nbsp;</span>
		<span style="display: block; width: 50%; float: right;">
			<a style="text-decoration:none;" href="javascript:void(0);" onclick="javascript:document.form_etape1.submit();">
				<span style="float:right;margin-left:10px;line-height:63px;">Cliquez pour passer a l'étape suivante</span><img style="padding-left:50px;border:0px;float:right" src="./common/modules/sharefile/img/puce_etape2.png" alt="<?= dims_constant::getVal('_DIMS_NEXT'); ?>">
			</a>
		</span>
	</div>
</div>
</form>
<script language="JavaScript" type="text/JavaScript">
	$('.datepicker').datepicker({dateFormat: 'dd/mm/yy'});

	function shareFileCheck() {
		if ($('#share_label').val()!="") $('#sharefile_button').css('display','block');
		else $('#sharefile_button').css('display', 'none');
	}

	$('#share_label').focus();
	shareFileCheck();
</script>
