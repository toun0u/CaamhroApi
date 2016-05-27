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
				<td class="label_field">
					<label for="<?= $this->get_field_id('tiers_bank'); ?>">
						<?= $this->get_field_label('tiers_bank'); ?>
					</label>
					<span class="required">*</span>
				</td>
				<td class="value_field" colspan="4">
					<?= $this->get_field_html('tiers_bank'); ?>
				</td>
			</tr>
			<tr>
				<td></td>
				<td colspan="4"><div id="def_<?= $this->get_field_id('tiers_bank'); ?>" class="mess_error"></div></td>
			</tr>
			<tr>
				<td class="label_field">
					<label for="<?= $this->get_field_id('tiers_bank_domici'); ?>">
						<?= $this->get_field_label('tiers_bank_domici'); ?>
					</label>
					<span class="required">*</span>
				</td>
				<td colspan="4" class="value_field">
					<?= $this->get_field_html('tiers_bank_domici'); ?>
				</td>
			</tr>
			<tr>
				<td></td>
				<td colspan="4"><div id="def_<?= $this->get_field_id('tiers_bank_domici'); ?>" class="mess_error"></div></td>
			</tr>

			<tr>
				<td></td>
				<td class="label_field" style="text-align:left;width:110px;">
					<label for="<?= $this->get_field_id('tiers_rib_b'); ?>">
						<?= $this->get_field_label('tiers_rib_b'); ?>
					</label>
				</td>
				<td class="label_field" style="text-align:left;width:110px;">
					<label for="<?= $this->get_field_id('tiers_rib_g'); ?>">
						<?= $this->get_field_label('tiers_rib_g'); ?>
					</label>
				</td>
				<td class="label_field" style="text-align:left;width:160px;">
					<label for="<?= $this->get_field_id('tiers_rib_c'); ?>">
						<?= $this->get_field_label('tiers_rib_c'); ?>
					</label>
				</td>
				<td class="label_field" style="text-align:left;">
					<label for="<?= $this->get_field_id('tiers_rib_r'); ?>">
						<?= $this->get_field_label('tiers_rib_r'); ?>
					</label>
				</td>
			</tr>
			<tr>
				<td class="label_field">
					<label>
						<?= dims_constant::getVal('_RIB'); ?>
					</label>
					<span class="required">*</span>
				</td>
				<td class="value_field">
					<?= $this->get_field_html('tiers_rib_b'); ?>
				</td>
				<td class="value_field">
					<?= $this->get_field_html('tiers_rib_g'); ?>
				</td>
				<td class="value_field">
					<?= $this->get_field_html('tiers_rib_c'); ?>
				</td>
				<td class="value_field">
					<?= $this->get_field_html('tiers_rib_r'); ?>
				</td>
			</tr>
			<tr>
				<td></td>
				<td colspan="4"><div id="def_rib" class="mess_error"></div></td>
			</tr>
			<tr>
				<td class="label_field">
					<label for="<?= $this->get_field_id('tiers_iban'); ?>">
						<?= $this->get_field_label('tiers_iban'); ?>
					</label>
					<span class="required">*</span>
				</td>
				<td colspan="4" class="value_field">
					<?= $this->get_field_html('tiers_iban'); ?>
				</td>
			</tr>
			<tr>
				<td></td>
				<td colspan="4"><div id="def_<?= $this->get_field_id('tiers_iban'); ?>" class="mess_error"></div></td>
			</tr>
			<tr>
				<td class="label_field">
					<label for="<?= $this->get_field_id('tiers_bics'); ?>">
						<?= $this->get_field_label('tiers_bics'); ?>
					</label>
					<span class="required">*</span>
				</td>
				<td colspan="4" class="value_field">
					<?= $this->get_field_html('tiers_bics'); ?>
				</td>
			</tr>
			<tr>
				<td></td>
				<td colspan="4"><div id="def_<?= $this->get_field_id('tiers_bics'); ?>" class="mess_error"></div></td>
			</tr>
		</table>
	</div>
</div>
