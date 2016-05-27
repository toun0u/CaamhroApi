<?php
/* dims_user form */
$errors = $this->getLightAttribute('errors');
$success = $this->getLightAttribute('success');

$subscribedmailinglists = $this->getLightAttribute('subscribedmailinglists');
$subscribedoptions = $this->getLightAttribute('subscribedoptions');

if(!empty($errors)) {
	?>
	<div class="alert alert-danger">
		<?php
		foreach($errors as $error) {
			?>
			<?= $error; ?><br />
			<?php
		}
		?>
	</div>
	<?php
}

if($success) {
	?>
	<div class="alert alert-success">
		<?= dims_constant::getVal('THE_MODIFICATIONS_HAVE_BEEN_DONE_SUCCESSFULLY'); ?>
	</div>
	<?php
}
?>
<ul class ="nav nav-tabs">
	<li class="active">
		<a href="?action=informations">
			<?= dims_constant::getVal('_INFOS_LABEL'); ?>
		</a>
	</li>
	<li>
		<a href="?action=addresses">
			<?= dims_constant::getVal('_ADDRESSES'); ?>
		</a>
	</li>
	<?php
	if(!empty($subscribedmailinglists)) {
		?>
		<li>
			<a href="?action=mailinglists">
				<?= dims_constant::getVal('_DIMS_LABEL_MAILINGLIST'); ?>
			</a>
		</li>
		<?php
	}
	?>
</ul>
<form method="post" action="?action=saveinformations" role="form" class="cointainer">
	<div class="row">
		<div class="col-md-6">
			<h3><?= dims_constant::getVal('_INFOS_LABEL'); ?></h3>
			<div class="form-group">
				<label for="u_firstname">
					<?= dims_constant::getVal('_FIRSTNAME'); ?>
				</label>
				<input class="form-control" type="text" name="u_firstname" id="u_firstname" value="<?= $this->get('firstname'); ?>" />
			</div>
			<div class="form-group">
				<label for="u_lastname">
					<?= dims_constant::getVal('_DIMS_LABEL_NAME'); ?>
				</label>
				<input class="form-control" type="text" name="u_lastname" id="u_lastname" value="<?= $this->get('lastname'); ?>" />
			</div>
			<div class="form-group">
				<label for="u_email">
					<?= dims_constant::getVal('_DIMS_LABEL_EMAIL'); ?>
				</label>
				<input class="form-control" type="text" name="u_email" id="u_email" value="<?= $this->get('email'); ?>" />
			</div>
			<div class="form-group">
				<label for="u_mobile">
					<?= dims_constant::getVal('_PHONE'); ?>
				</label>
				<input class="form-control" type="text" name="u_mobile" id="u_mobile" value="<?= $this->get('mobile'); ?>" />
			</div>
			<div class="form-group">
				<label for="u_function">
					<?= dims_constant::getVal('_DIMS_LABEL_FUNCTION'); ?>
				</label>
				<input class="form-control" type="text" name="u_function" id="u_function" value="<?= $this->get('function'); ?>" />
			</div>
			<div class="form-group">
				<label for="u_service">
					<?= dims_constant::getVal('_SERVICE'); ?>
				</label>
				<input class="form-control" type="text" name="u_service" id="u_service" value="<?= $this->get('service'); ?>" />
			</div>
		</div>

		<div class="col-md-6">
			<h3><?= dims_constant::getVal('IDENTIFICATION'); ?></h3>
			<div class="form-group <?= isset($errors['password']) ? 'has-error' : ''; ?>">
				<label for="password">
					<?= dims_constant::getVal('_DIMS_LABEL_PASSWORD'); ?>
				</label>
				<input class="form-control" type="password" name="password" id="password" value="" />
			</div>
			<div class="form-group <?= isset($errors['password']) ? 'has-error' : ''; ?>">
				<label for="password_confirm">
					<?= dims_constant::getVal('_DIMS_LABEL_PASSWORD_CONFIRM'); ?>
				</label>
				<input class="form-control" type="password" name="password_confirm" id="password_confirm" value="" />
			</div>
		</div>

	</div>
	<div class="row">
		<div class="col-md-12">
			<button type="submit" class="btn btn-default"><?= dims_constant::getVal('_DIMS_VALID'); ?></button>
		</div>
	</div>
</form>
