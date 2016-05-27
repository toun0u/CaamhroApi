<?php
if ($_SESSION['dims']['connected']) {
	ob_end_clean();

	header("Cache-control: private");
	header("Content-type: application/pdf");
	header("Content-Disposition: attachment; filename=Edition.pdf");
	header("Pragma: public");

	$id_cmd = dims_load_securvalue('id_cmd', dims_const::_DIMS_NUM_INPUT, true, true);

	include_once DIMS_APP_PATH.'/modules/catalogue/include/class_client.php';
	include_once DIMS_APP_PATH.'/modules/catalogue/include/class_commande.php';

	$commande = new commande();
	$commande->open($id_cmd);

	if (!$commande->fields['hors_cata']) include_once DIMS_APP_PATH.'/modules/catalogue/include/class_pdf_commande.php';
	else include_once DIMS_APP_PATH.'/modules/catalogue/include/class_pdf_commande_hc.php';

	$client = new client();
	$client->open($commande->fields['ref_client']);

	$user = new user();
	$user->open($commande->fields['id_user']);

	$pdf_commande = new pdf_commande();
	$pdf_commande->commande = $commande;
	$pdf_commande->client = $client;
	$pdf_commande->user = $user;

	$pdf_commande->afficher_prix = (($_SESSION['session_adminlevel'] >= _DIMS_ID_LEVEL_PURCHASERESP || $client->fields['afficher_prix']) && _SHOW_PRICES) ? true : false;

	// Recherche du service
	$service = new group();
	$service->open($commande->fields['id_group']);
	$pdf_commande->service = $service->fields['label'];

	$pdf_commande->Open();
	$pdf_commande->AliasNbPages();
	$pdf_commande->AddPage();
	$pdf_commande->Content();
	$pdf_commande->Output('commande.pdf', 'I');

	while (@ob_end_flush());
	die();
}
else {
    $_SESSION['catalogue']['connexion']['oldquery'] = $_SERVER['QUERY_STRING'];
    dims_redirect($dims->getScriptEnv().'?op=connexion');
}
