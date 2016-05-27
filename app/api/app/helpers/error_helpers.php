<?php
// TODO: pour les hrmessage, on pourrait utilisé gettext pour une traduction multi-langue ?
function message403(&$app, $code,$messageSup = ""){
	// liste de tous les messages 403 disponibles

	// EXEMPLE :
	// $messages403 = array(
	// 	40301 => array('status' => array('statusCode' => 40301, 'statusMessage' => 'Forbidden', 'hrmessage' => 'Compte non reconnu. Veuillez-vous reconnecter.')),
	// );

	if(isset($messages403[$code])){
		$messages403[$code]['status']['statusMessage'] .= $messageSup;
		$app->halt(403,json_encode($messages403[$code]));
	}else{
		$app->halt(403,json_encode(array('status' => array('statusCode' => $code, 'statusMessage' => 'Forbidden'.$messageSup, 'hrmessage' => $code))));
	}
}
