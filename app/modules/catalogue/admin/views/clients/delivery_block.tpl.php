<?php $view = view::getInstance(); ?>
<div class="sub_bloc">
	<?php
	$title = $this->getTitle();
	if (!empty($title)) {
		?>
		<h3><?php echo $title; ?></h3>
		<?php
	}
	?>
	<div class="sub_bloc_form">
		<table>
			<tr>
				<td class="label_field">
					<label for="liv_same_as_facturation"><?= $this->get_field_label('liv_same_as_facturation'); ?></label>
				</td>
				<td class="value_field w100p">
					<?= $this->get_field_html('liv_same_as_facturation'); ?>
				</td>
			</tr>
		</table>

		<table id="delivery_address_detail" style="padding-top: 20px; display: none;">
			<tr>
				<td class="label_field w100p"><label for="company_liv_name"><?= $this->get_field_label('company_liv_name'); ?></label><span class="required">*</span></td>
				<td class="value_field w100p" colspan="3"><?= $this->get_field_html('company_liv_name'); ?></td>
			</tr>
			<tr><td></td><td colspan="3"><div class="mess_error" id="def_company_liv_name"></div></td></tr>
			<tr>
				<td class="label_field w100p"><label for="company_liv_address_1"><?= $this->get_field_label('company_liv_address_1'); ?></label></td>
				<td class="value_field w100p" colspan="3"><?= $this->get_field_html('company_liv_address_1'); ?></td>
			</tr>
			<tr>
				<td class="label_field w100p"><label for="company_liv_address_2"><?= $this->get_field_label('company_liv_address_2'); ?></label></td>
				<td class="value_field w100p" colspan="3"><?= $this->get_field_html('company_liv_address_2'); ?></td>
			</tr>
			<tr>
				<td class="label_field w100p"><label for="company_liv_address_3"><?= $this->get_field_label('company_liv_address_3'); ?></label></td>
				<td class="value_field w100p" colspan="3"><?= $this->get_field_html('company_liv_address_3'); ?></td>
			</tr>
			<tr>
				<td class="label_field w100p"><label for="company_liv_country"><?= $this->get_field_label('company_liv_country'); ?></label></td>
				<td class="value_field w100p"><?= $this->get_field_html('company_liv_country'); ?></td>
			</tr>
			<tr>
				<td class="label_field w100p"><label for="company_liv_postalcode"><?= $this->get_field_label('company_liv_postalcode'); ?></label></td>
				<td class="value_field w100p"><?= $this->get_field_html('company_liv_postalcode'); ?></td>
				<td class="value_field w200p" style="padding-left:20px;"><?= $this->get_field_html('company_liv_city'); ?></td>
				<td>&nbsp;</td>
			</tr>
		</table>
	</div>
</div>
