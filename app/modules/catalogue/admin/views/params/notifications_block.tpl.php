<div class="sub_bloc">
	<?php
	$title = $this->getTitle();
	if (!empty($title)) {
		?>
		<h3>
			<?= $title; ?>
			<span id="active_notif_mail">
				<?= $this->get_field_html('active_notif_mail', '0'); ?>
				<label for="<?php echo $this->get_field_id('active_notif_mail', '0'); ?>">
					<?= $this->get_field_label('active_notif_mail', '0'); ?>
				</label>

				<?= $this->get_field_html('active_notif_mail', '1'); ?>
				<label for="<?php echo $this->get_field_id('active_notif_mail', '1'); ?>">
					<?= $this->get_field_label('active_notif_mail', '1'); ?>
				</label>
			</span>

			<span id="info_active_notif_mail" class="info_link">
				<img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" />
			</span>
			<span id="popup_active_notif_mail" class="info_popup">
				<p><strong><?= $title; ?> :</strong></p>
				<p>
					Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
					<a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_active_notif_mail').fadeOut();"><?= dims_constant::getVal('_DIMS_CLOSE'); ?></a>
				</p>
			</span>
		</h3>
		<?php
	}
	?>
	<div class="sub_bloc_form">
		<table>
			<tr>
				<td class="label_field w30">
					<label for="<?= $this->get_field_id('notif_send_mail'); ?>"><?= $this->get_field_label('notif_send_mail'); ?></label>
				</td>
				<td class="value_field">
					<?= $this->get_field_html('notif_send_mail'); ?>
					<span id="info_<?= $this->get_field_id('notif_send_mail'); ?>" class="info_link">
						<img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" />
					</span>
					<span id="popup_<?= $this->get_field_id('notif_send_mail'); ?>" class="info_popup">
						<p><strong><?= $this->get_field_label('notif_send_mail'); ?> :</strong></p>
						<p>
							Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
							<a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_<?= $this->get_field_id('notif_send_mail'); ?>').fadeOut();"><?= dims_constant::getVal('_DIMS_CLOSE'); ?></a>
						</p>
					</span>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><div id="def_<?= $this->get_field_id('notif_send_mail'); ?>" class="mess_error"></div></td>
			</tr>
			<tr>
				<td class="label_field w30">
					<label for="<?= $this->get_field_id('reception_cmd_mail'); ?>"><?= $this->get_field_label('reception_cmd_mail'); ?></label>
				</td>
				<td class="value_field">
					<?= $this->get_field_html('reception_cmd_mail'); ?>
					<span id="info_<?= $this->get_field_id('reception_cmd_mail'); ?>" class="info_link">
						<img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" />
					</span>
					<span id="popup_<?= $this->get_field_id('reception_cmd_mail'); ?>" class="info_popup">
						<p><strong><?= $this->get_field_label('reception_cmd_mail'); ?> :</strong></p>
						<p>
							Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
							<a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_<?= $this->get_field_id('reception_cmd_mail'); ?>').fadeOut();"><?= dims_constant::getVal('_DIMS_CLOSE'); ?></a>
						</p>
					</span>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><div id="def_<?= $this->get_field_id('reception_cmd_mail'); ?>" class="mess_error"></div></td>
			</tr>
			<tr>
				<td class="label_field w30">
					<label for="<?= $this->get_field_id('reception_retour_mail'); ?>"><?= $this->get_field_label('reception_retour_mail'); ?></label>
				</td>
				<td class="value_field">
					<?= $this->get_field_html('reception_retour_mail'); ?>
					<span id="info_<?= $this->get_field_id('reception_retour_mail'); ?>" class="info_link">
						<img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" />
					</span>
					<span id="popup_<?= $this->get_field_id('reception_retour_mail'); ?>" class="info_popup">
						<p><strong><?= $this->get_field_label('reception_retour_mail'); ?> :</strong></p>
						<p>
							Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
							<a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_<?= $this->get_field_id('reception_retour_mail'); ?>').fadeOut();"><?= dims_constant::getVal('_DIMS_CLOSE'); ?></a>
						</p>
					</span>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><div id="def_<?= $this->get_field_id('reception_retour_mail'); ?>" class="mess_error"></div></td>
			</tr>
			<tr>
				<td class="label_field w30">
					<label for="<?= $this->get_field_id('alert_notif_mail'); ?>"><?= $this->get_field_label('alert_notif_mail'); ?></label>
				</td>
				<td class="value_field">
					<?= $this->get_field_html('alert_notif_mail'); ?>
					<span id="info_<?= $this->get_field_id('alert_notif_mail'); ?>" class="info_link">
						<img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" />
					</span>
					<span id="popup_<?= $this->get_field_id('alert_notif_mail'); ?>" class="info_popup">
						<p><strong><?= $this->get_field_label('alert_notif_mail'); ?> :</strong></p>
						<p>
							Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
							<a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_<?= $this->get_field_id('alert_notif_mail'); ?>').fadeOut();"><?= dims_constant::getVal('_DIMS_CLOSE'); ?></a>
						</p>
					</span>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><div id="def_<?= $this->get_field_id('alert_notif_mail'); ?>" class="mess_error"></div></td>
			</tr>
			<tr>
				<td class="label_field w30">
					<label for="<?= $this->get_field_id('logistic_dept_email'); ?>"><?= $this->get_field_label('logistic_dept_email'); ?></label>
				</td>
				<td class="value_field">
					<?= $this->get_field_html('logistic_dept_email'); ?>
					<span id="info_<?= $this->get_field_id('logistic_dept_email'); ?>" class="info_link">
						<img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" />
					</span>
					<span id="popup_<?= $this->get_field_id('logistic_dept_email'); ?>" class="info_popup">
						<p><strong><?= $this->get_field_label('logistic_dept_email'); ?> :</strong></p>
						<p>
							Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
							<a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_<?= $this->get_field_id('logistic_dept_email'); ?>').fadeOut();"><?= dims_constant::getVal('_DIMS_CLOSE'); ?></a>
						</p>
					</span>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><div id="def_<?= $this->get_field_id('logistic_dept_email'); ?>" class="mess_error"></div></td>
			</tr>
			<tr>
				<td class="label_field w30">
					<label for="<?= $this->get_field_id('logistic_dept_email_copy'); ?>"><?= $this->get_field_label('logistic_dept_email_copy'); ?></label>
				</td>
				<td class="value_field">
					<?= $this->get_field_html('logistic_dept_email_copy'); ?>
					<span id="info_<?= $this->get_field_id('logistic_dept_email_copy'); ?>" class="info_link">
						<img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" />
					</span>
					<span id="popup_<?= $this->get_field_id('logistic_dept_email_copy'); ?>" class="info_popup">
						<p><strong><?= $this->get_field_label('logistic_dept_email_copy'); ?> :</strong></p>
						<p>
							Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
							<a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_<?= $this->get_field_id('logistic_dept_email_copy'); ?>').fadeOut();"><?= dims_constant::getVal('_DIMS_CLOSE'); ?></a>
						</p>
					</span>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><div id="def_<?= $this->get_field_id('logistic_dept_email_copy'); ?>" class="mess_error"></div></td>
			</tr>
			<tr>
				<td class="label_field w30">
					<label for="<?= $this->get_field_id('logistic_dept_email_copy_copy'); ?>"><?= $this->get_field_label('logistic_dept_email_copy_copy'); ?></label>
				</td>
				<td class="value_field">
					<?= $this->get_field_html('logistic_dept_email_copy_copy'); ?>
					<span id="info_<?= $this->get_field_id('logistic_dept_email_copy_copy'); ?>" class="info_link">
						<img src="/common/modules/catalogue/admin/views/gfx/info16.png" alt="Info" />
					</span>
					<span id="popup_<?= $this->get_field_id('logistic_dept_email_copy_copy'); ?>" class="info_popup">
						<p><strong><?= $this->get_field_label('logistic_dept_email_copy_copy'); ?> :</strong></p>
						<p>
							Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
							<a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_<?= $this->get_field_id('logistic_dept_email_copy_copy'); ?>').fadeOut();"><?= dims_constant::getVal('_DIMS_CLOSE'); ?></a>
						</p>
					</span>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><div id="def_<?= $this->get_field_id('logistic_dept_email_copy_copy'); ?>" class="mess_error"></div></td>
			</tr>
		</table>
	</div>
</div>
