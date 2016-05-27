<?php
// --------------------------------------------------------------
// MAIL DE CONFIRMATION DE COMMANDE
// --------------------------------------------------------------

$obj_cde->sendConfirmationMail($obj_cde->fields['cli_email'], $template_name);


// si le validateur n'est pas le propriétaire de la commande,
// on envoie un mail au validateur
if (isset($_SESSION['dims']['userid']) && $_SESSION['dims']['userid'] != $obj_cde->fields['id_user']) {
	$obj_cde->sendConfirmationMail($_SESSION['dims']['user']['email'], $template_name);
}

// on envoie aussi à toutes les adresses paramétrées pour recevoir les notifications
if (isset($oCatalogue)) {
	foreach (explode(',', $oCatalogue->getParams('notif_send_mail')) as $email) {
		$obj_cde->sendConfirmationMail(trim($email), $template_name);
	}
}

// copie au commercial
if ($obj_client->fields['representative_id'] > 0) {
	$representative = user::find_by(array('representative_id' => $obj_client->fields['representative_id']), null, 1);
	if ( !is_null($representative) && substr($representative->get('lastname'), 0, 8) != 'CELLULE_' ) {
		$obj_cde->sendConfirmationMail($representative->get('email'), $template_name);
	}
}
