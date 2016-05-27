<?php
if (!isset($_SESSION['catalogue']['errors'])) {
	$_SESSION['catalogue']['errors'] = array();
}

$_SESSION['catalogue']['inscription']['values'] = $_POST;

// verif login
$login = dims_load_securvalue('login', dims_const::_DIMS_CHAR_INPUT, false, true);
$rs = $db->query('SELECT id FROM dims_user WHERE login = \''.$login.'\' LIMIT 0,1');
if (!$db->numrows($rs)) {
	// verif password
	$password1 = dims_load_securvalue('password1', dims_const::_DIMS_CHAR_INPUT, false, true);
	$password2 = dims_load_securvalue('password2', dims_const::_DIMS_CHAR_INPUT, false, true);

	if (trim($password1) == '') {
		$_SESSION['catalogue']['errors'][2] = 'Vous devez saisir un mot de passe.';
	}
	elseif ($password1 != $password2) {
		$_SESSION['catalogue']['errors'][2] = 'Les mots de passe ne correspondent pas.';
	}

	// verification des donnees transmises
	$fact_nom       = dims_load_securvalue('fact_nom', dims_const::_DIMS_CHAR_INPUT, false, true);
	$fact_prenom    = dims_load_securvalue('fact_prenom', dims_const::_DIMS_CHAR_INPUT, false, true);
	$fact_raisoc    = dims_load_securvalue('fact_raisoc', dims_const::_DIMS_CHAR_INPUT, false, true);
	$fact_adresse   = dims_load_securvalue('fact_adresse', dims_const::_DIMS_CHAR_INPUT, false, true);
	$fact_cp        = dims_load_securvalue('fact_cp', dims_const::_DIMS_CHAR_INPUT, false, true);
	$fact_ville     = dims_load_securvalue('fact_ville', dims_const::_DIMS_CHAR_INPUT, false, true);
	$fact_pays      = dims_load_securvalue('fact_pays', dims_const::_DIMS_NUM_INPUT, false, true);
	$fact_tel       = dims_load_securvalue('fact_tel', dims_const::_DIMS_CHAR_INPUT, false, true);
	$fact_fax       = dims_load_securvalue('fact_fax', dims_const::_DIMS_CHAR_INPUT, false, true);
	$fact_email     = dims_load_securvalue('fact_email', dims_const::_DIMS_CHAR_INPUT, false, true);
	$idem           = dims_load_securvalue('idem', dims_const::_DIMS_CHAR_INPUT, false, true);
	$liv_nom        = dims_load_securvalue('liv_nom', dims_const::_DIMS_CHAR_INPUT, false, true);
	$liv_prenom     = dims_load_securvalue('liv_prenom', dims_const::_DIMS_CHAR_INPUT, false, true);
	$liv_raisoc     = dims_load_securvalue('liv_raisoc', dims_const::_DIMS_CHAR_INPUT, false, true);
	$liv_adresse    = dims_load_securvalue('liv_adresse', dims_const::_DIMS_CHAR_INPUT, false, true);
	$liv_cp         = dims_load_securvalue('liv_cp', dims_const::_DIMS_CHAR_INPUT, false, true);
	$liv_ville      = dims_load_securvalue('liv_ville', dims_const::_DIMS_CHAR_INPUT, false, true);
	$liv_pays       = dims_load_securvalue('liv_pays', dims_const::_DIMS_NUM_INPUT, false, true);
	$newsletter     = dims_load_securvalue('newsletter', dims_const::_DIMS_CHAR_INPUT, false, true);
	$visucode       = dims_load_securvalue('visucode', dims_const::_DIMS_CHAR_INPUT, false, true);


	if (empty($fact_nom)) {
		unset($_SESSION['catalogue']['inscription']['values']['fact_nom']);
		$_SESSION['catalogue']['errors'][3] = 'Le nom de facturation n\'est pas renseigné.';
	}
	if (empty($fact_prenom)) {
		unset($_SESSION['catalogue']['inscription']['values']['fact_prenom']);
		$_SESSION['catalogue']['errors'][4] = 'Le prénom de facturation n\'est pas renseigné.';
	}
	if (empty($fact_adresse)) {
		unset($_SESSION['catalogue']['inscription']['values']['fact_adresse']);
		$_SESSION['catalogue']['errors'][5] = 'L\'adresse de facturation n\'est pas renseignée.';
	}
	if (empty($fact_cp)) {
		unset($_SESSION['catalogue']['inscription']['values']['fact_cp']);
		$_SESSION['catalogue']['errors'][6] = 'Le code postal de facturation n\'est pas renseigné.';
	}
	if (empty($fact_ville)) {
		unset($_SESSION['catalogue']['inscription']['values']['fact_ville']);
		$_SESSION['catalogue']['errors'][7] = 'La ville de facturation n\'est pas renseignée.';
	}
	if (empty($fact_pays)) {
		unset($_SESSION['catalogue']['inscription']['values']['fact_pays']);
		$_SESSION['catalogue']['errors'][15] = 'Le pays de facturation n\'est pas renseigné.';
	}
	if (empty($fact_tel)) {
		unset($_SESSION['catalogue']['inscription']['values']['fact_tel']);
		$_SESSION['catalogue']['errors'][8] = 'Le numéro de téléphone n\'est pas renseignée.';
	}
	if (empty($fact_email)) {
		unset($_SESSION['catalogue']['inscription']['values']['fact_email']);
		$_SESSION['catalogue']['errors'][9] = 'L\'adresse email n\'est pas renseignée.';
	}
	elseif (!dims_verifyemail($fact_email)) {
		unset($_SESSION['catalogue']['inscription']['values']['fact_email']);
		$_SESSION['catalogue']['errors'][9] = 'L\'adresse email n\'est pas correctement renseignée.';
	}

	if (!$idem) {
		if (empty($liv_nom)) {
			unset($_SESSION['catalogue']['inscription']['values']['liv_nom']);
			$_SESSION['catalogue']['errors'][10] = 'Le nom de livraison n\'est pas renseigné.';
		}
		if (empty($liv_prenom)) {
			unset($_SESSION['catalogue']['inscription']['values']['liv_prenom']);
			$_SESSION['catalogue']['errors'][11] = 'Le prénom de livraison n\'est pas renseigné.';
		}
		if (empty($liv_adresse)) {
			unset($_SESSION['catalogue']['inscription']['values']['liv_adresse']);
			$_SESSION['catalogue']['errors'][12] = 'L\'adresse de livraison n\'est pas renseignée.';
		}
		if (empty($liv_cp)) {
			unset($_SESSION['catalogue']['inscription']['values']['liv_cp']);
			$_SESSION['catalogue']['errors'][13] = 'Le code postal de livraison n\'est pas renseigné.';
		}
		if (empty($liv_ville)) {
			unset($_SESSION['catalogue']['inscription']['values']['liv_ville']);
			$_SESSION['catalogue']['errors'][14] = 'La ville de livraison n\'est pas renseignée.';
		}
		if (empty($liv_pays)) {
			unset($_SESSION['catalogue']['inscription']['values']['liv_pays']);
			$_SESSION['catalogue']['errors'][16] = 'Le pays de livraison n\'est pas renseigné.';
		}
	}
	else {
		$liv_nom        = $fact_nom;
		$liv_prenom     = $fact_prenom;
		$liv_raisoc     = $fact_raisoc;
		$liv_adresse    = $fact_adresse;
		$liv_cp         = $fact_cp;
		$liv_ville      = $fact_ville;
		$liv_pays       = $fact_pays;
	}

	// // Vérification du captcha
	// require DIMS_APP_PATH.'/lib/cryptographp/cryptographp.fct.php';

	// if (!chk_crypt($visucode)) {
	//     $_SESSION['catalogue']['errors'][15] = 'La confirmation visuelle est erronnée.';
	// }

	if (sizeof($_SESSION['catalogue']['errors'])) {
		dims_redirect('/index.php?op=creer_compte');
	}

	// SI TOUT EST OK
	include_once DIMS_APP_PATH.'/modules/catalogue/include/class_client.php';

	$client = new client();
	$client->init_description();
	$client->setWorkspace($_SESSION['dims']['workspaceid']);
	$client->setModule($_SESSION['dims']['moduleid']);

	$client->generateCode();

	// client particulier ou pro ?
	if (trim($fact_raisoc) != '') {
		$raisoc = $fact_raisoc;
	}
	else {
		$raisoc = $fact_nom.' '.$fact_prenom;
	}

	// Tous les clients qui créent leur compte sont définis en particuliers
	$client->setParticular();


	$client->setUserinfos(array('lastname' => $fact_nom, 'firstname' => $fact_prenom));

	$f_adr = explode('\r\n', $fact_adresse);
	$l_adr = explode('\r\n', $liv_adresse);

	$client->setName($raisoc);
	$client->setEmail($fact_email);
	$client->setLogin($login);
	$client->setPassword($password1);

	if (!empty($f_adr[0]) && trim($f_adr[0]) != '') {
		$client->setAddress1($f_adr[0]);
	}
	if (!empty($f_adr[1]) && trim($f_adr[1]) != '') {
		$client->setAddress2($f_adr[1]);
	}
	if (!empty($f_adr[2]) && trim($f_adr[2]) != '') {
		$client->setAddress3($f_adr[2]);
	}
	$client->setPostalCode($fact_cp);
	$client->setCityLabel($fact_ville);
	$client->setCountry($fact_pays);

	$client->setLivName($liv_nom);
	if (!empty($l_adr[0]) && trim($l_adr[0]) != '') {
		$client->setLivAddress1($l_adr[0]);
	}
	if (!empty($l_adr[1]) && trim($l_adr[1]) != '') {
		$client->setLivAddress2($l_adr[1]);
	}
	if (!empty($l_adr[2]) && trim($l_adr[2]) != '') {
		$client->setLivAddress3($l_adr[2]);
	}
	$client->setLivCP($liv_cp);
	$client->setLivCity($liv_ville);
	$client->setLivCountry($liv_pays);

	$client->save();

	// envoi du mail au client avec ses identifiants
	$from[0]['name'] = $oCatalogue->getParams('cata_website_url');
	$from[0]['address'] = $oCatalogue->getParams('notif_send_mail');


	$to[0]['name'] = $client->getName();
	$to[0]['address'] = $client->getEmail();

	// require_once DIMS_APP_PATH.'templates/frontoffice/'.$template_name.'/catalogue/mails/client_creation_confirmation.tpl.php';
	// $content = str_replace(array('<LOGIN>', '<PASSWD>'), array($login, $password1), $message);
	// dims_send_mail($from, $to, $subject, $content);


	// if ($oCatalogue->getParams('active_notif_mail') && $oCatalogue->getParams('notif_send_mail') != '') {
	//     // envoi du mail a l'adherent avec les coordonnees du client
	//     require_once DIMS_APP_PATH.'templates/frontoffice/'.$template_name.'/catalogue/mails/client_creation_notification.tpl.php';

	//     foreach (explode(',', $oCatalogue->getParams('notif_send_mail')) as $notif_mail) {
	//         $to[0]['name'] = $notif_mail;
	//         $to[0]['address'] = $notif_mail;
	//         dims_send_mail($from, $to, $subject, $message);
	//     }
	// }

	// lien de connexion
	unset($_SESSION['catalogue']['inscription']['values']);
	if (empty($_SESSION['catalogue']['panier']['articles'])) {
		// Recherche de la racine du catalogue
		$rootFam = cata_famille::getRootFam();
		$useLink = '/'.$rootFam->getUrlrewrite().'.html?dims_url='.urldecode(base64_encode('dims_login='.$client->fields['login'].'&dims_password='.$client->fields['password'].'&already_hashed=1'));
	}
	else {
		$useLink = '/index.php?dims_url='.urldecode(base64_encode('dims_login='.$client->fields['login'].'&dims_password='.$client->fields['password'].'&already_hashed=1&op=valider_panier&etape=1'));
	}
	dims_redirect($useLink);
}
else {
	unset($_SESSION['catalogue']['inscription']['values']['login']);
	$_SESSION['catalogue']['errors'][1] = 'Identifiant déjà utilisé.';
	dims_redirect('/index.php?op=creer_compte');
}
