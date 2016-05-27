<div style="float:left;width:40%;text-align:center;margin:10px;">
	<span style="margin-top:24px;width:100%;display:block;"><img alt="" src="/assets/images/frontoffice/upsl/design/connexion.jpg"></span>
</div>
<div style="float:left;width:40%;margin:10px;">
	<form method="post" class="form-signin" name="formsignin">
			<h2 class="form-signin-heading signin"><?= dims_constant::getVal('IDENTIFICATION'); ?></h2>
			<hr>
			<input type="text" placeholder="<?= dims_constant::getVal('_LOGIN'); ?>" name="dims_login" class="input-block-level">
			<input type="password" placeholder="<?= dims_constant::getVal('_DIMS_LABEL_PASSWORD'); ?>" name="dims_password" class="input-block-level">
			<button type="submit" class="btn btn-large btn-primary"><?= dims_constant::getVal('_DIMS_VALID'); ?></button>
		</form>
</div>
