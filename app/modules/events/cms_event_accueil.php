	<div id="content1">
		<div id="content1_text">
			<h1><?= dims_constant::getVal('GOING_INTERNATIONAL'); ?></h1>
			<p>
				<?= dims_constant::getVal('FAIRS_SITE_DESCRIPTION'); ?>
			</p>
		</div>
		<div id="content1_connexion">
			<div id="connexion">
				<form class="login-form" id="user-login" method="post" accept-charset="UTF-8" action="/index.php">
				<a class="title_login"><?= dims_constant::getVal('_LOGIN'); ?></a><input type="text" onblur="Javascript:if (this.value=='')this.value='<?= dims_constant::getVal('PLEASE_ENTER_LOGIN'); ?>'" onfocus="Javascript:this.value=''" value="<?= dims_constant::getVal('PLEASE_ENTER_LOGIN'); ?>" name="dims_login">
				<br/><br/>
				<a class="title_password"><?= dims_constant::getVal('_DIMS_LABEL_PASSWORD'); ?></a><input type="password" onblur="Javascript:if (this.value=='')this.value='**********'" onfocus="Javascript:this.value=''" value="**********" name="dims_password">
				<input style="float: right; margin-right: 48px; margin-top: 5px; width: 50px;" type="submit" value="Go" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false"/>
				</form>
			</div>
		</div>
	</div>
