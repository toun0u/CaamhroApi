<div class="bloc_fond_connexion">
	<div class="border_bloc_fond_connexion">
		<p>
			<?= dims_constant::getVal('YOU_ARE_NOT_IDENTIFIED'); ?><br>
			<?= dims_constant::getVal('PLEASE_LOG_IN'); ?>
		</p>
		<form method="post" action="<?= dims_urlencode($this->get('urlbase')->addParams(array('op' => 'sharefile', 'action' => 'connection'))); ?>">
			<div style="float: left; margin-bottom: 10px; padding-left: 102px; width: 78%;">
				<label for="dims_login">
					<?= dims_constant::getVal('_LOGIN'); ?>
				</label>
				<input type="text" name="dims_login" id="dims_login" />
			</div>
			<div style="float: left; padding-left: 59px; width: 87%;">
				<label for="dims_password">
					<?= dims_constant::getVal('_DIMS_LABEL_PASSWORD'); ?>
				</label>
				<input type="password" name="dims_password" id="dims_password" />
			</div>
			<div class="validation">
				<input type="submit" value="<?= dims_constant::getVal('_SUBMIT'); ?>" />
			</div>
		</form>
	</div>
</div>
