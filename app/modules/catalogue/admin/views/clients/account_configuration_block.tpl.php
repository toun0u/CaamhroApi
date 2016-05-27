<?= $this->get_field_html('id_client'); ?>

<div class="sub_bloc">
	<?php
	$title = $this->getTitle();
	if (!empty($title)) {
		?><h3><?= $title; ?></h3><?php
	}
	?>
	<div class="sub_bloc_form">
		<table>
			<tr>
				<td valign="top">
					<table>
						<tr>
							<td class="label_field">
								<label for="<?= $this->get_field_id('code_client'); ?>"><?= $this->get_field_label('code_client'); ?></label>
							</td>
							<td class="value_field">
								<?= $this->get_field_html('code_client'); ?>
							</td>
						</tr>
						<tr>
							<td class="label_field">
								<label><?= dims_constant::getVal('_TYPE'); ?></label>
							</td>
							<td class="value_field">
								<span id="client_type">
									<?= $this->get_field_html('type', '0'); ?>
									<label for="<?= $this->get_field_id('type', '0'); ?>"><?= $this->get_field_label('type', '0'); ?></label>

									<?php echo $this->get_field_html('type', '1'); ?>
									<label for="<?= $this->get_field_id('type', '1'); ?>"><?= $this->get_field_label('type', '1'); ?></label>
								</span>
							</td>
						</tr>
						<tr>
							<td class="label_field">
								<label for="<?= $this->get_field_id('blocked'); ?>"><?= $this->get_field_label('blocked'); ?></label>
							</td>
							<td class="value_field">
								<?= $this->get_field_html('blocked'); ?>
							</td>
						</tr>
						<tr>
							<td class="label_field" valign="top">
								<label for="<?= $this->get_field_id('commentaire'); ?>"><?= $this->get_field_label('commentaire'); ?></label>
							</td>
							<td class="value_field">
								<?= $this->get_field_html('commentaire'); ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
</div>
