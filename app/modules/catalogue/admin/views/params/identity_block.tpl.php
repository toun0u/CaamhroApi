<div class="sub_bloc">
	<?php
	$title = $this->getTitle();
	if (!empty($title)) {
		?>
		<h3><?= $title; ?></h3>
		<?php
	}
	?>
	<div class="sub_bloc_form">
		<table>
			<tr>
				<td valign="top">
					<table>
 						<tr>
							<td class="label_field">
								<label for="<?= $this->get_field_id('tiers_intitule'); ?>"><?= $this->get_field_label('tiers_intitule'); ?></label>
							</td>
							<td class="value_field">
								<?= $this->get_field_html('tiers_intitule'); ?>
							</td>
						</tr>
 						<tr>
							<td class="label_field">
								<label for="<?= $this->get_field_id('tiers_ent_siren'); ?>"><?= $this->get_field_label('tiers_ent_siren'); ?></label>
							</td>
							<td class="value_field">
								<?= $this->get_field_html('tiers_ent_siren'); ?>
							</td>
							<td class="label_field">
								<label for="<?= $this->get_field_id('tiers_ent_nic'); ?>"><?= $this->get_field_label('tiers_ent_nic'); ?></label>
							</td>
							<td class="value_field">
								<?= $this->get_field_html('tiers_ent_nic'); ?>
							</td>
						</tr>
 						<tr>
							<td class="label_field">
								<label for="<?= $this->get_field_id('tiers_ent_ape'); ?>"><?= $this->get_field_label('tiers_ent_ape'); ?></label>
							</td>
							<td class="value_field" colspan="3">
								<?= $this->get_field_html('tiers_ent_ape'); ?>
							</td>
						</tr>
 						<tr>
							<td class="label_field">
								<label for="<?= $this->get_field_id('tiers_adresse'); ?>"><?= $this->get_field_label('tiers_adresse'); ?></label>
							</td>
							<td class="value_field">
								<?= $this->get_field_html('tiers_adresse'); ?>
							</td>
						</tr>
 						<tr>
							<td class="label_field">
								<label for="<?= $this->get_field_id('tiers_adresse2'); ?>"><?= $this->get_field_label('tiers_adresse2'); ?></label>
							</td>
							<td class="value_field">
								<?= $this->get_field_html('tiers_adresse2'); ?>
							</td>
						</tr>
 						<tr>
							<td class="label_field">
								<label for="<?= $this->get_field_id('tiers_adresse3'); ?>"><?= $this->get_field_label('tiers_adresse3'); ?></label>
							</td>
							<td class="value_field">
								<?= $this->get_field_html('tiers_adresse3'); ?>
							</td>
						</tr>
 						<tr>
							<td class="label_field">
								<label for="<?= $this->get_field_id('tiers_id_country'); ?>"><?= $this->get_field_label('tiers_id_country'); ?></label>
							</td>
							<td class="value_field">
								<?= $this->get_field_html('tiers_id_country'); ?>
							</td>
						</tr>
 						<tr>
							<td class="label_field">
								<label for="<?= $this->get_field_id('tiers_codepostal'); ?>"><?= $this->get_field_label('tiers_codepostal'); ?></label>
							</td>
							<td class="value_field">
								<?= $this->get_field_html('tiers_codepostal'); ?>
							</td>
							<td class="label_field">
								<label for="<?= $this->get_field_id('tiers_id_city'); ?>"><?= $this->get_field_label('tiers_id_city'); ?></label>
							</td>
							<td class="value_field">
								<?= $this->get_field_html('tiers_id_city'); ?>
								<img src="/assets/images/common/modules/system/desktopV2/templates/gfx/common/add.png" style="cursor:pointer;" class="add-city" />
							</td>
						</tr>
						<tr>
							<td class="label_field">
								<label for="<?= $this->get_field_id('photo'); ?>"><?= $this->get_field_label('photo'); ?></label>
							</td>
							<td class="value_field">
								<?= $this->get_field_html('photo'); ?>
							</td>
							<td class="value_field" colspan="2">
								<?php
								$tiers = view::getInstance()->get('tiers');
								if(file_exists($tiers->getPhotoPath(60))){
									?>
									<img src="<?= $tiers->getPhotoWebPath(60); ?>" />
									<a href="<?= get_path('params', 'dellogo'); ?>">
										<img style="margin-left:10px;margin-bottom: 10px;" alt="<?= dims_constant::getVal('_DELETE'); ?>" title="<?= dims_constant::getVal('_DELETE'); ?>" src="<?= view::getInstance()->getTemplateWebPath('gfx/supprimer16.png'); ?>" />
									</a>
									<?php
								}
								?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
</div>
