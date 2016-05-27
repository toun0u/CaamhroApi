<?php
if ($_SESSION['dims']['connected']) {
	$a_errors = array(
		1 => 'Le mot de passe et sa confirmation ne sont pas identiques.',
		2 => 'Le mot de passe est trop court. Il doit faire au moins 4 caract&egrave;res.',
		3 => 'L\'adresse e-mail est invalide.'
	);

	$error = dims_load_securvalue('error', dims_const::_DIMS_NUM_INPUT, true, false);
	if ($error != '') {
		$smarty->assign('error', $a_errors[$error]);
	}

	$infos = array();

	$user = new user();
	$user->open($_SESSION['dims']['userid']);

	$infos['user'] = array(
		'lastname'	=> $user->fields['lastname'],
		'firstname'	=> $user->fields['firstname'],
		'email'		=> $user->fields['email']
		);

	include_once DIMS_APP_PATH.'/modules/catalogue/include/class_client.php';
	$client = new client();
	$client->openByCode($_SESSION['catalogue']['code_client']);
	$infos['client'] = array(
		'name'			=> $client->getName(),
		'adr1'			=> $client->getAddress(),
		'adr2'			=> $client->getAddress2(),
		'cp'			=> $client->getPostalCode(),
		'city'			=> $client->getCity(),
		'id_country' 	=> $client->getCountryId()
		);

	foreach ($client->getDepots() as $depot) {
		foreach ($depot->fields as $k => $v) {
			$depot->fields[$k] = nl2br($v);
		}
		$infos['depots'][] = $depot->fields;
	}

	$infos['buttons'] = array(
		'btn_back'	=> catalogue_makegfxbutton('<i class="icon2-reply orange title-icon"></i>','Retour',"document.location.href='/index.php?op=compte';","*", false, "nounderline"),
		'btn_save'	=> catalogue_makegfxbutton('Enregistrer','<i class="icon2-checkmark orange title-icon"></i>',"javascript:void(0);\" onclick=\"$('#form_info_perso').submit();","*", false, "nounderline")
		);
	// Les infos sont éditables si la fonction est activée en backoffice, ou que le client est un particulier
	$smarty->assign('infos_persos_editables', $oCatalogue->getParams('cata_infos_persos_editable') || $client->isParticular());

	$smarty->assign('infos', $infos);
	$smarty->assign('tpl_name', 'infospersos');

	$smarty->assign('a_countries', country::getAllCountries());

	$page['TITLE'] = 'Vos informations personnelles';
	$page['META_DESCRIPTION'] = 'Visualiser et modifier vos informations personnelles';
	$page['META_KEYWORDS'] = 'informations, infos, personnelles, modifier';
	$page['CONTENT'] = '';
}
else {
	$_SESSION['catalogue']['connexion']['oldquery'] = $_SERVER['QUERY_STRING'];
	dims_redirect($dims->getScriptEnv().'?op=connexion');
}
