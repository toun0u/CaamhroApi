<?php
$view = view::getInstance();
$notif_mail = $view->get('notif_mail');

$additional_js = <<< ADDITIONAL_JS
// boutons ON / OFF
$('#active_notif_mail').buttonset();
$('#active_notif_mail input').change(function(){
	if($(this).val() == 1){
		$("form#f_notif input[type='text']").removeAttr("disabled");
	}else{
		$("form#f_notif input[type='text']").attr("disabled",true);
	}
});

// fermeture de tous les popups
function closeAllPopups() {
	$('#popup_active_notif_mail').fadeOut();
	$('#popup_notif_send_mail').fadeOut();
	$('#popup_reception_cmd_mail').fadeOut();
	$('#popup_reception_retour_mail').fadeOut();
	$('#popup_alert_notif_mail').fadeOut();
}

// popups info
$('#info_active_notif_mail').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_active_notif_mail'));
	$('#popup_active_notif_mail').fadeToggle('fast');
});
$('#info_notif_send_mail').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_notif_send_mail'));
	$('#popup_notif_send_mail').fadeToggle('fast');
});
$('#info_reception_cmd_mail').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_reception_cmd_mail'));
	$('#popup_reception_cmd_mail').fadeToggle('fast');
});
$('#info_reception_retour_mail').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_reception_retour_mail'));
	$('#popup_reception_retour_mail').fadeToggle('fast');
});
$('#info_alert_notif_mail').click(function(e) {
	closeAllPopups();
	setPopupPosition(e, $('#popup_alert_notif_mail'));
	$('#popup_alert_notif_mail').fadeToggle('fast');
});
ADDITIONAL_JS;

// formulaire
$form = new Dims\form(array(
	'name'          => 'f_notif',
	'action'        => get_path('params', 'save_notif'),
	'submit_value'  => dims_constant::getVal('_DIMS_SAVE'),
	'back_name'     => dims_constant::getVal('_DIMS_RESET'),
	'back_url'      => get_path('params', 'notif'),
	'additional_js' => $additional_js,
	'validation'    => true
));
$form->addBlock('default', dims_constant::getVal('CATA_EMAIL_NOTIFICATIONS'), $this->getTemplatePath('params/notifications_block.tpl.php'));

$form->add_radio_field(array(
	'id'        => 'active_notif_mail_1',
	'name'      => 'active_notif_mail',
	'value'     => 1,
	'label'     => 'On',
	'checked'   => $notif_mail['active_notif_mail']->getValue()
));
$form->add_radio_field(array(
	'id'        => 'active_notif_mail_0',
	'name'      => 'active_notif_mail',
	'value'     => 0,
	'label'     => 'Off',
	'checked'   => !$notif_mail['active_notif_mail']->getValue()
));

$form->add_text_field(array(
	'id'        => 'notif_send_mail',
	'name'      => 'notif_send_mail',
	'value'     => $notif_mail['notif_send_mail']->getValue(),
	'label'     => dims_constant::getVal('_BROADCAST_ADDRESS_EMAILS'),
	'additionnal_attributes' => ($notif_mail['active_notif_mail']->getValue())?"":'disabled=true',
	'classes'   => 'w90',
	'revision'  => 'email'
));

$form->add_text_field(array(
	'id'        => 'reception_cmd_mail',
	'name'      => 'reception_cmd_mail',
	'value'     => $notif_mail['reception_cmd_mail']->getValue(),
	'label'     => dims_constant::getVal('_RECEIVING_ADDRESS_COMMANDS'),
	'additionnal_attributes' => ($notif_mail['active_notif_mail']->getValue())?"":'disabled=true',
	'classes'   => 'w90',
	'revision'  => 'email'
));

$form->add_text_field(array(
	'id'        => 'reception_retour_mail',
	'name'      => 'reception_retour_mail',
	'value'     => $notif_mail['reception_retour_mail']->getValue(),
	'label'     => dims_constant::getVal('_ADDRESS_RECEIPT_REQUEST_RETURN'),
	'additionnal_attributes' => ($notif_mail['active_notif_mail']->getValue())?"":'disabled=true',
	'classes'   => 'w90',
	'revision'  => 'email'
));

$form->add_text_field(array(
	'id'        => 'alert_notif_mail',
	'name'      => 'alert_notif_mail',
	'value'     => $notif_mail['alert_notif_mail']->getValue(),
	'label'     => dims_constant::getVal('_ADDRESS_ALERT_MINI_STOCK_REACHED'),
	'additionnal_attributes' => ($notif_mail['active_notif_mail']->getValue())?"":'disabled=true',
	'classes'   => 'w90',
	'revision'  => 'email'
));

$form->add_text_field(array(
	'id'        => 'logistic_dept_email',
	'name'      => 'logistic_dept_email',
	'value'     => $notif_mail['logistic_dept_email']->getValue(),
	'label'     => dims_constant::getVal('_LOGISTIC_SERVICE_EMAIL'),
	'classes'   => 'w90',
	'revision'  => 'email',
	'additionnal_attributes' => ($notif_mail['active_notif_mail']->getValue()) ? '' : 'disabled=true',
));

$form->add_text_field(array(
	'id'        => 'logistic_dept_email_copy',
	'name'      => 'logistic_dept_email_copy',
	'value'     => $notif_mail['logistic_dept_email_copy']->getValue(),
	'label'     => dims_constant::getVal('_LOGISTIC_SERVICE_EMAIL').' '.dims_constant::getVal('COPY'),
	'classes'   => 'w90',
	'revision'  => 'email',
	'additionnal_attributes' => ($notif_mail['active_notif_mail']->getValue()) ? '' : 'disabled=true',
));

$form->add_text_field(array(
	'id'        => 'logistic_dept_email_copy_copy',
	'name'      => 'logistic_dept_email_copy_copy',
	'value'     => $notif_mail['logistic_dept_email_copy_copy']->getValue(),
	'label'     => dims_constant::getVal('_LOGISTIC_SERVICE_EMAIL').' '.dims_constant::getVal('COPY').' '.dims_constant::getVal('COPY'),
	'classes'   => 'w90',
	'revision'  => 'email',
	'additionnal_attributes' => ($notif_mail['active_notif_mail']->getValue()) ? '' : 'disabled=true',
));

$form->build();
?>
