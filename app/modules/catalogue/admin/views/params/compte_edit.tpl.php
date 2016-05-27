<?php
$view = view::getInstance();
$lstParam = $view->get('param_compte');

$additional_js = <<< ADDITIONAL_JS
// boutons ON / OFF
$('#auto_codif').buttonset();
$('#services_validation').buttonset();

$('#auto_codif input').change(function(){
	if($(this).val() == 1)
		$("#codif_auto input").removeAttr('disabled');
	else
		$("#codif_auto input").attr('disabled',true);
});

$('#services_validation input').change(function(){
	if($(this).val() == 1){
		$("#serv_valid input").removeAttr('disabled');
		$("#serv_valid select").removeAttr('disabled');
	}else{
		$("#serv_valid input").attr('disabled',true);
		$("#serv_valid select").attr('disabled',true);
	}
});

// fermeture de tous les popups
function closeAllPopups() {
	$('#popup_auto_codif').fadeOut();
	$('#popup_base_codif').fadeOut();
	$('#popup_services_validation').fadeOut();
	$('#popup_user_without_valid').fadeOut();
	$('#popup_user_with_valid').fadeOut();
	$('#popup_service_manager').fadeOut();
	$('#popup_purchasing_manager').fadeOut();
	$('#popup_account_admin').fadeOut();
	$('#popup_default_lvl_registration').fadeOut();
}
// popups info
$('#info_auto_codif').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_auto_codif'));
	$('#popup_auto_codif').fadeToggle('fast');
});
$('#info_services_validation').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_services_validation'));
	$('#popup_services_validation').fadeToggle('fast');
});
$('#info_base_codif').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_base_codif'));
	$('#popup_base_codif').fadeToggle('fast');
});
$('#info_user_without_valid').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_user_without_valid'));
	$('#popup_user_without_valid').fadeToggle('fast');
});
$('#info_user_with_valid').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_user_with_valid'));
	$('#popup_user_with_valid').fadeToggle('fast');
});
$('#info_service_manager').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_service_manager'));
	$('#popup_service_manager').fadeToggle('fast');
});
$('#info_purchasing_manager').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_purchasing_manager'));
	$('#popup_purchasing_manager').fadeToggle('fast');
});
$('#info_account_admin').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_account_admin'));
	$('#popup_account_admin').fadeToggle('fast');
});
$('#info_default_lvl_registration').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_default_lvl_registration'));
	$('#popup_default_lvl_registration').fadeToggle('fast');
});
ADDITIONAL_JS;

$form = new Dims\form(array(
	'name'              => 'compte_params',
	'action'            => get_path('params', 'save_compte'),
	'validation'        => false,
	'back_name'         => dims_constant::getVal('REINITIALISER'),
	'back_url'          => get_path('params', 'compte'),
	'submit_value'      => dims_constant::getVal('_DIMS_SAVE'),
	'include_actions'   => false,
	'additional_js'     => $additional_js
));

$form->addBlock('codif_auto', dims_constant::getVal('_AUTOMATIC_CODING'));

$codifAuto = $lstParam['auto_codif'];
$form->add_radio_field(array(
	'block'     => 'codif_auto',
	'id'        => 'auto_codif_0',
	'name'      => 'auto_codif',
	'value'     => 1,
	'label'     => 'On',
	'checked'   => $codifAuto->getValue()
));
$form->add_radio_field(array(
	'block'     => 'codif_auto',
	'id'        => 'auto_codif_1',
	'name'      => 'auto_codif',
	'value'     => 0,
	'label'     => 'Off',
	'checked'   => !$codifAuto->getValue()
));

$baseCodif = $lstParam['base_codif'];
$form->add_text_field(array(
	'block'     => 'codif_auto',
	'id'        => 'base_codif',
	'name'      => 'base_codif',
	'value'     => $baseCodif->getValue(),
	'label'     => dims_constant::getVal('_BASED_CODING'),
	'additionnal_attributes' => (!$codifAuto->getValue())?"disabled=true":"",
	'classes'   => 'w80p'
));

$form->addBlock('serv_val', dims_constant::getVal('_SERVICES_AND_VALIDATION'));

$servValid = $lstParam['services_validation'];
$form->add_radio_field(array(
	'block'     => 'serv_val',
	'id'        => 'services_validation_0',
	'name'      => 'services_validation',
	'value'     => 1,
	'label'     => 'On',
	'checked'   => $servValid->getValue()
));
$form->add_radio_field(array(
	'block'     => 'serv_val',
	'id'        => 'services_validation_1',
	'name'      => 'services_validation',
	'value'     => 0,
	'label'     => 'Off',
	'checked'   => !$servValid->getValue()
));

$is_user_without_valid = $lstParam['is_user_without_valid'];
$form->add_checkbox_field(array(
	'block'     => 'serv_val',
	'id'        => 'is_user_without_valid',
	'name'      => 'is_user_without_valid',
	'value'     => 1,
	'label'     => dims_constant::getVal('_USER_WITHOUT_VALIDATION'),
	'checked'   => $is_user_without_valid->getValue(),
	'additionnal_attributes' => (!$servValid->getValue())?"disabled=true":"",
));

$user_without_valid = $lstParam['user_without_valid'];
$form->add_text_field(array(
	'block'     => 'serv_val',
	'id'        => 'user_without_valid',
	'name'      => 'user_without_valid',
	'value'     => $user_without_valid->getValue(),
	'additionnal_attributes' => (!$servValid->getValue())?"disabled=true":"",
	'classes'   => 'w150p'
));

$is_user_with_valid = $lstParam['is_user_with_valid'];
$form->add_checkbox_field(array(
	'block'     => 'serv_val',
	'id'        => 'is_user_with_valid',
	'name'      => 'is_user_with_valid',
	'value'     => 1,
	'label'     => dims_constant::getVal('_USER_WITH_VALIDATION'),
	'checked'   => $is_user_with_valid->getValue(),
	'additionnal_attributes' => (!$servValid->getValue())?"disabled=true":"",
));

$user_with_valid = $lstParam['user_with_valid'];
$form->add_text_field(array(
	'block'     => 'serv_val',
	'id'        => 'user_with_valid',
	'name'      => 'user_with_valid',
	'value'     => $user_with_valid->getValue(),
	'additionnal_attributes' => (!$servValid->getValue())?"disabled=true":"",
	'classes'   => 'w150p'
));

$is_service_manager = $lstParam['is_service_manager'];
$form->add_checkbox_field(array(
	'block'     => 'serv_val',
	'id'        => 'is_service_manager',
	'name'      => 'is_service_manager',
	'value'     => 1,
	'label'     => dims_constant::getVal('_SERVICE_MANAGER'),
	'checked'   => $is_service_manager->getValue(),
	'additionnal_attributes' => (!$servValid->getValue())?"disabled=true":"",
));

$service_manager = $lstParam['service_manager'];
$form->add_text_field(array(
	'block'     => 'serv_val',
	'id'        => 'service_manager',
	'name'      => 'service_manager',
	'value'     => $service_manager->getValue(),
	'additionnal_attributes' => (!$servValid->getValue())?"disabled=true":"",
	'classes'   => 'w150p'
));

$is_purchasing_manager = $lstParam['is_purchasing_manager'];
$form->add_checkbox_field(array(
	'block'     => 'serv_val',
	'id'        => 'is_purchasing_manager',
	'name'      => 'is_purchasing_manager',
	'value'     => 1,
	'label'     => dims_constant::getVal('_PURCHASING_MANAGER'),
	'checked'   => $is_purchasing_manager->getValue(),
	'additionnal_attributes' => (!$servValid->getValue())?"disabled=true":"",
));

$purchasing_manager = $lstParam['purchasing_manager'];
$form->add_text_field(array(
	'block'     => 'serv_val',
	'id'        => 'purchasing_manager',
	'name'      => 'purchasing_manager',
	'value'     => $purchasing_manager->getValue(),
	'additionnal_attributes' => (!$servValid->getValue())?"disabled=true":"",
	'classes'   => 'w150p'
));

$is_account_admin = $lstParam['is_account_admin'];
$form->add_checkbox_field(array(
	'block'     => 'serv_val',
	'id'        => 'is_account_admin',
	'name'      => 'is_account_admin',
	'value'     => 1,
	'label'     => dims_constant::getVal('_ACCOUNT_ADMINISTRATOR'),
	'checked'   => $is_account_admin->getValue(),
	'additionnal_attributes' => (!$servValid->getValue())?"disabled=true":"",
));

$account_admin = $lstParam['account_admin'];
$form->add_text_field(array(
	'block'     => 'serv_val',
	'id'        => 'account_admin',
	'name'      => 'account_admin',
	'value'     => $account_admin->getValue(),
	'additionnal_attributes' => (!$servValid->getValue())?"disabled=true":"",
	'classes'   => 'w150p'
));

$lstReg = array(
	dims_const::_DIMS_ID_LEVEL_USER             => dims_constant::getVal('_USER_WITH_VALIDATION'),
	cata_const::_DIMS_ID_LEVEL_USERSUP          => dims_constant::getVal('_USER_WITHOUT_VALIDATION'),
	cata_const::_DIMS_ID_LEVEL_SERVICERESP      => dims_constant::getVal('_SERVICE_MANAGER'),
	cata_const::_DIMS_ID_LEVEL_PURCHASERESP     => dims_constant::getVal('_PURCHASING_MANAGER'),
	dims_const::_DIMS_ID_LEVEL_GROUPMANAGER     => dims_constant::getVal('_ACCOUNT_ADMINISTRATOR'));

$default_lvl_registration = $lstParam['default_lvl_registration'];
$form->add_select_field(array(
	'block'     => 'serv_val',
	'id'        => 'default_lvl_registration',
	'name'      => 'default_lvl_registration',
	'value'     => $default_lvl_registration->getValue(),
	'label'     => dims_constant::getVal('_DEFAULT_LVL_REGISTRATION'),
	'options'   => $lstReg,
	'additionnal_attributes' => (!$servValid->getValue())?"disabled=true":""
));


echo $form->get_header();

$block1 = $form->getBlock('codif_auto');
$block2 = $form->getBlock('serv_val');
?>
<div class="form_object_block">
	<div class="sub_bloc">
		<?php
		$title = $block1->getTitle();
		if (!empty($title)) {
			?>
			<h3>
				<?= $title; ?>
				<span id="auto_codif">
					<?= $block1->get_field_html('auto_codif', '0'); ?>
					<label for="<?= $block1->get_field_id('auto_codif', '0'); ?>">
						<?= $block1->get_field_label('auto_codif', '0'); ?>
					</label>

					<?= $block1->get_field_html('auto_codif', '1'); ?>
					<label for="<?= $block1->get_field_id('auto_codif', '1'); ?>">
						<?= $block1->get_field_label('auto_codif', '1'); ?>
					</label>
				</span>
				<span id="info_auto_codif" class="info_link">
					<img src="<?= $this->getTemplateWebPath("/gfx/info16.png"); ?>" title="<?= dims_constant::getVal('_DIMS_TOINFO'); ?>" alt="<?= dims_constant::getVal('_DIMS_TOINFO'); ?>" />
				</span>
				<span id="popup_auto_codif" class="info_popup">
					<p><strong><?= dims_constant::getVal('_AUTOMATIC_CODING'); ?> :</strong></p>
					<p>
						Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
						<a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_auto_codif').fadeOut();">
							<?= dims_constant::getVal('_DIMS_CLOSE'); ?>
						</a>
					</p>
				</span>
			</h3>
			<?php
		}
		?>
		<div class="sub_bloc_form" id="codif_auto">
			<table>
				<tr>
					<td class="label_field">
						<label for="<?= $block1->get_field_id('base_codif'); ?>">
							<?= $block1->get_field_label('base_codif'); ?>
						</label>
						<span class="required">*</span>
					</td>
					<td class="value_field">
						<?= $block1->get_field_html('base_codif'); ?>
						<span id="info_base_codif" class="info_link">
							<img src="<?= $this->getTemplateWebPath("/gfx/info16.png"); ?>" title="<?= dims_constant::getVal('_DIMS_TOINFO'); ?>" alt="<?= dims_constant::getVal('_DIMS_TOINFO'); ?>" />
						</span>
						<span id="popup_base_codif" class="info_popup">
							<p><strong><?= dims_constant::getVal('_BASED_CODING'); ?> :</strong></p>
							<p>
								Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
								<a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_base_codif').fadeOut();">
									<?= dims_constant::getVal('_DIMS_CLOSE'); ?>
								</a>
							</p>
						</span>
					</td>
				</tr>
			</table>
		</div>
		<?php
		$title = $block2->getTitle();
		if (!empty($title)) {
			?>
			<h3>
				<?= $title; ?>
				<span id="services_validation">
					<?= $block2->get_field_html('services_validation', '0'); ?>
					<label for="<?= $block2->get_field_id('services_validation', '0'); ?>">
						<?= $block2->get_field_label('services_validation', '0'); ?>
					</label>

					<?= $block2->get_field_html('services_validation', '1'); ?>
					<label for="<?= $block2->get_field_id('services_validation', '1'); ?>">
						<?= $block2->get_field_label('services_validation', '1'); ?>
					</label>
				</span>
				<span id="info_services_validation" class="info_link">
					<img src="<?= $this->getTemplateWebPath("/gfx/info16.png"); ?>" title="<?= dims_constant::getVal('_DIMS_TOINFO'); ?>" alt="<?= dims_constant::getVal('_DIMS_TOINFO'); ?>" />
				</span>
				<span id="popup_services_validation" class="info_popup">
					<p><strong><?= dims_constant::getVal('_SERVICES_AND_VALIDATION'); ?> :</strong></p>
					<p>
						Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
						<a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_services_validation').fadeOut();">
							<?= dims_constant::getVal('_DIMS_CLOSE'); ?>
						</a>
					</p>
				</span>
			</h3>
			<?php
		}
		?>
		<div class="sub_bloc_form" id="serv_valid">
			<table>
				<tr>
					<td class="label_field">
						<label for="<?= $block2->get_field_id('is_user_without_valid'); ?>">
							<?= $block2->get_field_label('is_user_without_valid'); ?>
						</label>
					</td>
					<td class="value_field">
						<?= $block2->get_field_html('is_user_without_valid'); ?>
					</td>
					<td class="value_field">
						<?= $block2->get_field_html('user_without_valid'); ?>
						<span id="info_user_without_valid" class="info_link">
							<img src="<?= $this->getTemplateWebPath("/gfx/info16.png"); ?>" title="<?= dims_constant::getVal('_DIMS_TOINFO'); ?>" alt="<?= dims_constant::getVal('_DIMS_TOINFO'); ?>" />
						</span>
						<span id="popup_user_without_valid" class="info_popup">
							<p><strong><?= dims_constant::getVal('_USER_WITHOUT_VALIDATION'); ?> :</strong></p>
							<p>
								Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
								<a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_user_without_valid').fadeOut();">
									<?= dims_constant::getVal('_DIMS_CLOSE'); ?>
								</a>
							</p>
						</span>
					</td>
				</tr>
				<tr>
					<td class="label_field">
						<label for="<?= $block2->get_field_id('is_user_with_valid'); ?>">
							<?= $block2->get_field_label('is_user_with_valid'); ?>
						</label>
					</td>
					<td class="value_field">
						<?= $block2->get_field_html('is_user_with_valid'); ?>
					</td>
					<td class="value_field">
						<?= $block2->get_field_html('user_with_valid'); ?>
						<span id="info_user_with_valid" class="info_link">
							<img src="<?= $this->getTemplateWebPath("/gfx/info16.png"); ?>" title="<?= dims_constant::getVal('_DIMS_TOINFO'); ?>" alt="<?= dims_constant::getVal('_DIMS_TOINFO'); ?>" />
						</span>
						<span id="popup_user_with_valid" class="info_popup">
							<p><strong><?= dims_constant::getVal('_USER_WITH_VALIDATION'); ?> :</strong></p>
							<p>
								Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
								<a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_user_with_valid').fadeOut();">
									<?= dims_constant::getVal('_DIMS_CLOSE'); ?>
								</a>
							</p>
						</span>
					</td>
				</tr>
				<tr>
					<td class="label_field">
						<label for="<?= $block2->get_field_id('is_service_manager'); ?>">
							<?= $block2->get_field_label('is_service_manager'); ?>
						</label>
					</td>
					<td class="value_field">
						<?= $block2->get_field_html('is_service_manager'); ?>
					</td>
					<td class="value_field">
						<?= $block2->get_field_html('service_manager'); ?>
						<span id="info_service_manager" class="info_link">
							<img src="<?= $this->getTemplateWebPath("/gfx/info16.png"); ?>" title="<?= dims_constant::getVal('_DIMS_TOINFO'); ?>" alt="<?= dims_constant::getVal('_DIMS_TOINFO'); ?>" />
						</span>
						<span id="popup_service_manager" class="info_popup">
							<p><strong><?= dims_constant::getVal('_SERVICE_MANAGER'); ?> :</strong></p>
							<p>
								Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
								<a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_service_manager').fadeOut();">
									<?= dims_constant::getVal('_DIMS_CLOSE'); ?>
								</a>
							</p>
						</span>
					</td>
				</tr>
				<tr>
					<td class="label_field">
						<label for="<?= $block2->get_field_id('is_purchasing_manager'); ?>">
							<?= $block2->get_field_label('is_purchasing_manager'); ?>
						</label>
					</td>
					<td class="value_field">
						<?= $block2->get_field_html('is_purchasing_manager'); ?>
					</td>
					<td class="value_field">
						<?= $block2->get_field_html('purchasing_manager'); ?>
						<span id="info_purchasing_manager" class="info_link">
							<img src="<?= $this->getTemplateWebPath("/gfx/info16.png"); ?>" title="<?= dims_constant::getVal('_DIMS_TOINFO'); ?>" alt="<?= dims_constant::getVal('_DIMS_TOINFO'); ?>" />
						</span>
						<span id="popup_purchasing_manager" class="info_popup">
							<p><strong><?= dims_constant::getVal('_PURCHASING_MANAGER'); ?> :</strong></p>
							<p>
								Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
								<a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_purchasing_manager').fadeOut();">
									<?= dims_constant::getVal('_DIMS_CLOSE'); ?>
								</a>
							</p>
						</span>
					</td>
				</tr>
				<tr>
					<td class="label_field">
						<label for="<?= $block2->get_field_id('is_account_admin'); ?>">
							<?= $block2->get_field_label('is_account_admin'); ?>
						</label>
					</td>
					<td class="value_field">
						<?= $block2->get_field_html('is_account_admin'); ?>
					</td>
					<td class="value_field">
						<?= $block2->get_field_html('account_admin'); ?>
						<span id="info_account_admin" class="info_link">
							<img src="<?= $this->getTemplateWebPath("/gfx/info16.png"); ?>" title="<?= dims_constant::getVal('_DIMS_TOINFO'); ?>" alt="<?= dims_constant::getVal('_DIMS_TOINFO'); ?>" />
						</span>
						<span id="popup_account_admin" class="info_popup">
							<p><strong><?= dims_constant::getVal('_ACCOUNT_ADMINISTRATOR'); ?> :</strong></p>
							<p>
								Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
								<a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_account_admin').fadeOut();">
									<?= dims_constant::getVal('_DIMS_CLOSE'); ?>
								</a>
							</p>
						</span>
					</td>
				</tr>
				<tr>
					<td class="label_field">
						<label for="<?= $block2->get_field_id('default_lvl_registration'); ?>">
							<?= $block2->get_field_label('default_lvl_registration'); ?>
						</label>
					</td>
					<td></td>
					<td class="value_field">
						<?= $block2->get_field_html('default_lvl_registration'); ?>
						<span id="info_default_lvl_registration" class="info_link">
							<img src="<?= $this->getTemplateWebPath("/gfx/info16.png"); ?>" title="<?= dims_constant::getVal('_DIMS_TOINFO'); ?>" alt="<?= dims_constant::getVal('_DIMS_TOINFO'); ?>" />
						</span>
						<span id="popup_default_lvl_registration" class="info_popup">
							<p><strong><?= dims_constant::getVal('_DEFAULT_LVL_REGISTRATION'); ?> :</strong></p>
							<p>
								Indique si votre catalogue en ligne est synchronisé avec votre ERP. Si oui, la gestion des familles, articles, tarifs, stocks, commandes sera automatisée.
								<a title="<?= dims_constant::getVal('_DIMS_CLOSE'); ?>" href="javascript:void(0);" onclick="javascript:$('#popup_default_lvl_registration').fadeOut();">
									<?= dims_constant::getVal('_DIMS_CLOSE'); ?>
								</a>
							</p>
						</span>
					</td>
				</tr>
			</table>
		</div>
		<?= $form->displayActionsBlock(); ?>
	</div>
</div>
<?= $form->close_form(); ?>
