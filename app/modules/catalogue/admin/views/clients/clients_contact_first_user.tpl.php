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
			<table>
				<tr>
					<td class="label_field w100p"><label for="user_firstname"><?= $this->get_field_label('user_firstname'); ?></label><span class="required">*</span></td>
					<td class="value_field w100p"><?= $this->get_field_html('user_firstname'); ?></td>
					<td class="label_field w100p"><label for="user_lastname"><?= $this->get_field_label('user_lastname'); ?></label><span class="required">*</span></td>
					<td class="value_field w100p"><?= $this->get_field_html('user_lastname'); ?></td>
				</tr>
				<tr>
					<td></td><td><div class="mess_error" id="def_user_firstname"></div></td>
					<td></td><td><div class="mess_error" id="def_user_lastname"></div></td>
				</tr>
				<tr>
					<td class="label_field w100p"><label for="user_email"><?= $this->get_field_label('user_email'); ?></label><span class="required">*</span></td>
					<td class="value_field w100p"><?= $this->get_field_html('user_email'); ?></td>
					<td class="label_field w100p"><label for="user_phone"><?= $this->get_field_label('user_phone'); ?></label></td>
					<td class="value_field w100p"><?= $this->get_field_html('user_phone'); ?></td>
				</tr>
				<tr>
					<td></td><td><div class="mess_error" id="def_user_email"></div></td>
					<td colspan="2"></td>
				</tr>
				<tr>
					<td class="label_field w100p"><label for="user_login"><?= $this->get_field_label('user_login'); ?></label><span class="required">*</span></td>
					<td class="value_field w100p"><?= $this->get_field_html('user_login'); ?></td>
					<td class="label_field w100p"><label for="user_fax"><?= $this->get_field_label('user_fax'); ?></label></td>
					<td class="value_field w100p"><?= $this->get_field_html('user_fax'); ?></td>
				</tr>
				<tr>
					<td></td><td><div class="mess_error" id="def_user_login"></div></td>
					<td colspan="2"></td>
				</tr>
				<tr>
					<td class="label_field w100p"><label for="user_password"><?= $this->get_field_label('user_password'); ?></label><span class="required">*</span></td>
					<td class="value_field w100p"><?= $this->get_field_html('user_password'); ?></td>
					<td class="label_field w100p"><label for="user_password_confirmation"><?= $this->get_field_label('user_password_confirmation'); ?></label><span class="required">*</span></td>
					<td class="value_field w100p">
						<?= $this->get_field_html('user_password_confirmation'); ?>
						<div class="mess_error" id="def_user_password_confirmation"></div>
					</td>
				</tr>
				<tr>
					<td class="w100p right">
						<input type="button" value="<?= dims_constant::getVal('CATA_GENERATE'); ?>" style="width: auto;" onclick="javascript:generatePassword();" />
					</td>
					<td>
						<input type="text" id="visible_password" />
					</td>
					<td colspan="2"></td>
				</tr>
				<tr>
					<td class="label_field w100p"><label for="user_level"><?= $this->get_field_label('user_level'); ?></label></td>
					<td class="value_field w100p"><?= $this->get_field_html('user_level'); ?></td>
				</tr>
			</table>
		</div>
	</div>
</div>
