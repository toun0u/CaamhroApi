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
		<div class="clients_price">
			<table style="padding-top:20px">
				<tr>
					<td class="label_field w100p"><label for="company_name"><?= $this->get_field_label('company_name'); ?></label><span class="required">*</span></td>
					<td class="value_field w100p" colspan="3"><?= $this->get_field_html('company_name'); ?></td>
				</tr>
				<tr><td></td><td colspan="3"><div class="mess_error" id="def_company_name"></div></td></tr>
				<tr>
					<td class="label_field w100p"><label for="company_email"><?= $this->get_field_label('company_email'); ?></label></td>
					<td class="value_field w100p" colspan="3"><?= $this->get_field_html('company_email'); ?></td>
				</tr>
				<tr>
					<td class="label_field w100p"><label for="company_number_siren"><?= $this->get_field_label('company_number_siren'); ?></label></td>
					<td class="value_field w100p"><?= $this->get_field_html('company_number_siren'); ?></td>
					<td class="label_field w100p"><label for="company_nic"><?= $this->get_field_label('company_nic'); ?></label></td>
					<td class="value_field w100p"><?= $this->get_field_html('company_nic'); ?></td>
				</tr>
				<tr>
					<td class="label_field w100p"><label for="company_ape_code"><?= $this->get_field_label('company_ape_code'); ?></label></td>
					<td class="value_field w100p"><?= $this->get_field_html('company_ape_code'); ?></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td class="label_field w100p"><label for="company_address_1"><?= $this->get_field_label('company_address_1'); ?></label></td>
					<td class="value_field w100p" colspan="3"><?= $this->get_field_html('company_address_1'); ?></td>
				</tr>
				<tr>
					<td class="label_field w100p"><label for="company_address_2"><?= $this->get_field_label('company_address_2'); ?></label></td>
					<td class="value_field w100p" colspan="3"><?= $this->get_field_html('company_address_2'); ?></td>
				</tr>
				<tr>
					<td class="label_field w100p"><label for="company_address_3"><?= $this->get_field_label('company_address_3'); ?></label></td>
					<td class="value_field w100p" colspan="3"><?= $this->get_field_html('company_address_3'); ?></td>
				</tr>
				<tr>
					<td class="label_field w100p"><label for="company_country"><?= $this->get_field_label('company_country'); ?></label></td>
					<td class="value_field w100p"><?= $this->get_field_html('company_country'); ?></td>
				</tr>
				<tr>
					<td class="label_field w100p"><label for="company_postalcode"><?= $this->get_field_label('company_postalcode'); ?></label></td>
					<td class="value_field w100p"><?= $this->get_field_html('company_postalcode'); ?></td>
					<td class="value_field w200p" style="padding-left:20px;" id="company_city_block"><?= $this->get_field_html('company_city'); ?></td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</div>
	</div>
</div>
