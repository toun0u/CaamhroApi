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
	<li>
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
		<li class="active">
			<a href="?action=mailinglists">
				<?= dims_constant::getVal('_DIMS_LABEL_MAILINGLIST'); ?>
			</a>
		</li>
		<?php
	}
	?>
</ul>
<form method="post" action="?action=savemailinglists" role="form" class="cointainer">
	<div class="row">
		<div class="col-md-12">
			<?php
			if(!empty($subscribedmailinglists)) {
				?>
				<h3><?= dims_constant::getVal('_DIMS_LABEL_MAILINGLIST'); ?></h3>
				<table class="table">
					<tr>
						<th><?= dims_constant::getVal('_DIMS_LABEL_MAILINGLIST'); ?></th>
						<th><?= dims_constant::getVal('DEACTIVATE_EMAILS_RECEPTION'); ?></th>
					</tr>
					<?php
					foreach($subscribedmailinglists as $mailinglist) {
						if(isset($subscribedoptions[$mailinglist->getId()])) {
							$mailinglistoptions = $subscribedoptions[$mailinglist->getId()];
						} else {
							$mailinglistoptions = new newsletter_subscribed_options();
							$mailinglistoptions->init_description();
						}
						?>
						<tr>
							<td>
								<?= $mailinglist->fields['label']; ?>
							</td>
							<td>
								<input type="hidden" name="mailinglistoptions[<?= $mailinglist->getId(); ?>][nomail]" value="0" />
								<input type="checkbox" name="mailinglistoptions[<?= $mailinglist->getId(); ?>][nomail]" value="1" <?= $mailinglistoptions->fields['nomail'] ? 'checked="checked"' : ''; ?> />
							</td>
						</tr>
						<?php
					}
					?>
				</table>
				<?php
			}
			?>
		</div>

	</div>
	<div class="row">
		<div class="col-md-12">
			<button type="submit" class="btn btn-default"><?= dims_constant::getVal('_DIMS_VALID'); ?></button>
		</div>
	</div>
</form>
