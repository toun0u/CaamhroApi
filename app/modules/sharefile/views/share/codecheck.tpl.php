<?php
$error = $this->get('error');
$id_share = $this->get('id_share');
?>
<form name="form_etapecheck" method="post" action="<? echo dims_urlencode(dims::getInstance()->getScriptEnv().'?op=share&action=sharefile_codecheck_form&id_share='.$id_share); ?>">
<div class="dims_form" style="float:left; width:80%;padding-top:20px;">
	<div style="padding:2px;">
		<span style="width:10%;display:block;float:left;">
			<img src="/common/modules/sharefile/img/btn_access_bg.gif">
		</span>
		<span style="width:90%;display:block;float:left;font-size:20px;color:#BABABA;font-weight:bold;">
			Code d'activation
		</span>
	</div>
	<div style="padding:2px;clear:both;float:left;width:100%;">
		<p>
			<label>Code d'activation</label>
			<input class="text" type="text" id="sharefile_codecheck" name="sharefile_codecheck" value="" />
			<?php if (!empty($error)): ?>
				<img src="./common/img/warning.png">
				<font style="color:#FF0000">
					<?= $error; ?>
				</font>
			<?php endif ;?>
		</p>
	</div>
	<div id="sharefile_button" style="padding:2px;clear:both;float:left;width:100%;">
		<span style="width:50%;display:block;float:left;">&nbsp;</span>
		<span style="width:50%;display:block;float:left;">
			<a style="text-decoration:none;" href="javascript:void(0);" onclick="javascript:document.form_etapecheck.submit();">
				<img style="padding-left:50px;border:0px;" src="./common/modules/sharefile/img/forward.png" alt="<?= dims_constant::getVal('_DIMS_VALID'); ?>">
			</a>
		</span>
	</div>
</div>
</form>
<script language="JavaScript" type="text/JavaScript">
$("sharefile_codecheck").focus();
</script>
