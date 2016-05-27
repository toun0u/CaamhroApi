<?php

// Erciture du fichier SQL pour insertion des documents dans la BDD CAAHMRO
function variante_write_cmd_file($id_cde) {

	require_once DIMS_APP_PATH.'modules/catalogue/include/class_client.php';
	require_once DIMS_APP_PATH.'modules/catalogue/include/class_commande.php';
	require_once DIMS_APP_PATH.'modules/catalogue/include/class_article.php';

	if (!is_dir(_CATA_CMD_DIR)) {
		dims_makedir(_CATA_CMD_DIR);
	}

	// Ouverture de la commande
	$cde = new commande();
	$cde->open($id_cde);

	if ($cde->get('erp_id_adr_liv') > 0 || $cde->get('cli_liv_cp') == -1) {
		$client = new client();
		$client->openByCode($cde->fields['code_client']);

		$filename = _CATA_CMD_DIR.'/'.$cde->getId().'.sql';


		// -----------------------------------
		// Ecriture de la commande
		// -----------------------------------

		// Ouverture du fichier en écriture
		$fp = fopen($filename, "w");
		flock($fp, LOCK_EX);

		// Gabarit
		$gauge_id = 0;
		switch ($client->get('id_company')) {
			// GROUPE
			case 3:
				$gauge_id = 1315749;
				break;
			// COOP
			case 1:
				$gauge_id = 1315763;
				break;
		}

		// Frais de port
		$charge_id = 1324872;

		switch ($cde->get('cli_liv_cp')) {
			case -1:
				$incoterm = '3EXW'; // Enlèvement sur site
				$shipping_conditions = "NULL";
				break;
			default:
				$incoterm = '1LPS'; // Livré, port en sus
				$shipping_conditions = $cde->get('shipping_conditions_id');
				break;
		}


		// On écrase le document précédent
		fwrite($fp, 'DELETE FROM `IND_DIMS_DOC_CREA_ENTETE` WHERE `DIMS_ORDER_ID` = '.$cde->getId().';'."\n");
		fwrite($fp, 'DELETE FROM `IND_DIMS_DOC_CREA_POSITION` WHERE `DIMS_ORDER_ID` = '.$cde->getId().';'."\n");
		fwrite($fp, 'DELETE FROM `IND_DIMS_DOC_CREA_PIED` WHERE `DIMS_ORDER_ID` = '.$cde->getId().';'."\n");

		$representativeValidator = 'client';
		if (!empty($cde->fields['representative_validator'])) {
			$vrp = new user();
			$vrp->open($cde->fields['representative_validator']);

			$representativeValidator = $vrp->fields['login'];
		}

		// Entete
		$sql = 'INSERT INTO `IND_DIMS_DOC_CREA_ENTETE` (
				`PAC_PERSON_ID`,
				`DATE_DOCUMENT`,
				`DIMS_ORDER_ID`,
				`FAC_PAC_ADDRESS_ID`,
				`LIV_PAC_ADDRESS_ID`,
				`PAC_PAYMENT_CONDITION_ID`,
				`ACS_PAYMENT_METHOD_ID`,
				`PAC_SENDING_CONDITION_ID`,
				`COMMENTAIRE_CLIENT`,
				`DOC_GAUGE_ID`,
				`PRIX_TOTAL_CTRL_INTERFACE`,
				`C_INCOTERMS`,
				`date_enlevement`,
				`heure_enlevement`,
				`date_livraison`,
				`semi_remorque_possible`,
				`hauteur_maxi`,
				`hayon_necessaire`,
				`tire_pal_necessaire`,
				`camion_autre`,
				`impossibilites_lirvaison`,
				`contact_nom`,
				`contact_prenom`,
				`contact_tel`,
				`contact_autre`,
				`CREATEUR_DOC`
			) VALUES (
				'.$client->fields['erp_id'].',
				"'.timestamp_to_datetime($cde->fields['date_validation']).'",
				"'.$cde->getId().'",
				'.$cde->fields['erp_id_adr_fac'].',
				'.(is_null($cde->fields['erp_id_adr_liv']) ? 0 : $cde->fields['erp_id_adr_liv']).',
				'.$cde->fields['payment_conditions_id'].',
				'.$cde->fields['mode_paiement_id'].',
				'.(is_null($shipping_conditions) ? 'NULL' : $shipping_conditions).',
				"'.mysql_escape_mimic($cde->fields['commentaire']).'",
				'.$gauge_id.',
				'.$cde->fields['total_ht'].',
				"'.$incoterm.'",
				'.(is_null($cde->fields['date_enlevement']) 			? 'NULL' : '"'.$cde->fields['date_enlevement'].'"').',
				'.(is_null($cde->fields['heure_enlevement']) 			? 'NULL' : '"'.$cde->fields['heure_enlevement'].'"').',
				'.(is_null($cde->fields['date_livraison']) 				? 'NULL' : '"'.$cde->fields['date_livraison'].'"').',
				'.(is_null($cde->fields['semi_remorque_possible']) 		? 'NULL' : $cde->fields['semi_remorque_possible']).',
				'.(is_null($cde->fields['hauteur_maxi']) 				? 'NULL' : '"'.$cde->fields['hauteur_maxi'].'"').',
				'.(is_null($cde->fields['hayon_necessaire']) 			? 'NULL' : $cde->fields['hayon_necessaire']).',
				'.(is_null($cde->fields['tire_pal_necessaire']) 		? 'NULL' : $cde->fields['tire_pal_necessaire']).',
				'.(is_null($cde->fields['camion_autre']) 				? 'NULL' : '"'.$cde->fields['camion_autre'].'"').',
				'.(is_null($cde->fields['impossibilites_lirvaison']) 	? 'NULL' : '"'.$cde->fields['impossibilites_lirvaison'].'"').',
				'.(is_null($cde->fields['contact_nom']) 				? 'NULL' : '"'.$cde->fields['contact_nom'].'"').',
				'.(is_null($cde->fields['contact_prenom']) 				? 'NULL' : '"'.$cde->fields['contact_prenom'].'"').',
				'.(is_null($cde->fields['contact_tel']) 				? 'NULL' : '"'.$cde->fields['contact_tel'].'"').',
				'.(is_null($cde->fields['contact_autre']) 				? 'NULL' : '"'.$cde->fields['contact_autre'].'"').',
				"'.$representativeValidator.'"
			);';
		fwrite($fp, str_replace(array("\n", "\t"), '', $sql)."\n");

		$a_lignes = $cde->getlignes();
		foreach ($a_lignes as $ligne) {
			$article = new article();
			$article->open($ligne->fields['id_article']);

			// Lignes
			$sql = 'INSERT INTO `IND_DIMS_DOC_CREA_POSITION` (
					`DIMS_POSITION_ID`,
					`DIMS_ORDER_ID`,
					`GCO_GOOD_ID`,
					`QUANTITY`,
					`PRIX_NET_HT`,
					`POS_NET_TARIFF`
				) VALUES (
					"'.$ligne->fields['id_cde_ligne'].'",
					"'.$cde->getId().'",
					'.$article->fields['erp_id'].',
					'.$ligne->fields['qte'].',
					'.$ligne->fields['pu_remise_erp'].',
					'.$ligne->fields['forced_price'].'
				);';
					// '.round($ligne->fields['pu_remise'] * $ligne->fields['qte'], 2).'
			fwrite($fp, str_replace(array("\n", "\t"), '', $sql)."\n");
		}

		// Frais de port
		$sql = 'INSERT INTO `IND_DIMS_DOC_CREA_PIED` (
				`DIMS_ORDER_ID`,
				`PTC_CHARGE_ID`,
				`MONTANT_HT`,
				`DIMS_CHARGE_ID`
			) VALUES (
				"'.$cde->getId().'",
				"'.$charge_id.'",
				"'.$cde->get('port').'",
				1
			);';
		fwrite($fp, str_replace(array("\n", "\t"), '', $sql)."\n");

		// fermeture du fichier
		flock($fp, LOCK_UN);
		fclose($fp);


		// -----------------------------------
		// Ecriture du fichier checksum
		// -----------------------------------

		$fp = fopen($filename.'.md5', "w");
		flock($fp, LOCK_EX);
		fwrite($fp, md5_file($filename));
		flock($fp, LOCK_UN);
		fclose($fp);
	}
}

// Renvoie la date au format datetime MySQL
function timestamp_to_datetime($timestamp) {
	return 	substr($timestamp, 0, 4).'-'.
			substr($timestamp, 4, 2).'-'.
			substr($timestamp, 6, 2).' '.
			substr($timestamp, 8, 2).':'.
			substr($timestamp, 10, 2).':'.
			substr($timestamp, 12, 2);
}

// Fait la même chose que mysql_real_escape_string
// mais sans avoir besoin d'une connexion à Mysql
function mysql_escape_mimic($inp) {
	if(is_array($inp))
		return array_map(__METHOD__, $inp);

	if(!empty($inp) && is_string($inp)) {
		return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
	}

	return $inp;
}

// Synchronisation des adresses
function variante_sync_address($params = array()) {
	$db = dims::getInstance()->getDb();

	$client = $params['client'];
	$depot 	= $params['depot'];


	if (!is_dir(_CATA_CMD_DIR)) {
		dims_makedir(_CATA_CMD_DIR);
	}

	$filename = _CATA_CMD_DIR.'/'.$params['address_type'].'-'.$depot->get('id').'.sql';

	// -----------------------------------
	// Ecriture de l'adresse
	// -----------------------------------

	// Ouverture du fichier en écriture
	$fp = fopen($filename, "w");
	flock($fp, LOCK_EX);


	switch ($params['address_type']) {
		case 'liv':
			$address_type = 'Liv';
			break;
	}

	// Recherche de l'id du pays dans la structure CAAHMRO
	$rs = $db->query('SELECT p.`PC_CNTRY_ID`
		FROM `dims_country` c
		INNER JOIN `IND_DIMS_DICO_PAYS` p
		ON p.`INI` = c.`ISO`
		WHERE c.`id` = '.$depot->get('id_country'));
	if ($db->numrows($rs)) {
		$row = $db->fetchrow($rs);
		$id_country = $row['PC_CNTRY_ID'];
	}

	switch ($params['action']) {
		case 'insert':
			$sql = 'INSERT INTO `IND_DIMS_CRE_ADD_LIV` (
					`PAC_PERSON_ID`,
					`DIC_ADDRESS_TYPE_ID`,
					`ADD_ADDRESS1`,
					`ADD_ZIPCODE`,
					`ADD_CITY`,
					`PC_CNTRY_ID`,
					`MOD_TYPE`,
					`ID_DIMS_LIV`,
					`TRAITE`
				) VALUES (
					'.$client->get('erp_id').',
					"'.$address_type.'",
					"'.$depot->get('adr1').'",
					"'.$depot->get('cp').'",
					"'.$depot->get('ville').'",
					'.$id_country.',
					"C",
					'.$depot->get('id').',
					0
				) ;';

			// fwrite($fp, str_replace(array("\r", "\n", "\t"), array(' ', '', ''), $sql)."\n");
			fwrite($fp, str_replace("\t", '', $sql)."\n");
			break;
		case 'update':
		case 'delete':
			$action = ($params['action'] == 'update') ? 'U' : 'D';

			$sql = 'INSERT INTO `IND_DIMS_CRE_ADD_LIV` (
					`PAC_ADDRESS_ID`,
					`PAC_PERSON_ID`,
					`DIC_ADDRESS_TYPE_ID`,
					`ADD_ADDRESS1`,
					`ADD_ZIPCODE`,
					`ADD_CITY`,
					`PC_CNTRY_ID`,
					`MOD_TYPE`,
					`ID_DIMS_LIV`,
					`TRAITE`
				) VALUES (
					IF ('.$depot->get('erp_id').' = 0, NULL, '.$depot->get('erp_id').'),
					'.$client->get('erp_id').',
					"'.$address_type.'",
					"'.$depot->get('adr1').'",
					"'.$depot->get('cp').'",
					"'.$depot->get('ville').'",
					'.$id_country.',
					"'.$action.'",
					'.$depot->get('id').',
					0 )
				ON DUPLICATE KEY UPDATE
					`PAC_ADDRESS_ID` 		= IF ('.$depot->get('erp_id').' = 0, NULL, '.$depot->get('erp_id').'),
					`PAC_PERSON_ID` 		= '.$client->get('erp_id').',
					`DIC_ADDRESS_TYPE_ID` 	= "'.$address_type.'",
					`ADD_ADDRESS1` 			= "'.$depot->get('adr1').'",
					`ADD_ZIPCODE` 			= "'.$depot->get('cp').'",
					`ADD_CITY` 				= "'.$depot->get('ville').'",
					`PC_CNTRY_ID` 			= '.$id_country.',';
			if ($action == 'update') {
				$sql .= '
						`MOD_TYPE` 				= IF (`MOD_TYPE` = "C" AND `TRAITE` = 0, "C", "'.$action.'"),';
			}
			else {
				$sql .= '
						`MOD_TYPE` 				= "'.$action.'",';
			}
			$sql .= '
					`ID_DIMS_LIV` 			= '.$depot->get('id').',
					`TRAITE` 				= 0 ;';

			// fwrite($fp, str_replace(array("\r", "\n", "\t"), array(' ', '', ''), $sql)."\n");
			fwrite($fp, str_replace("\t", '', $sql)."\n");
			break;
	}

	// fermeture du fichier
	flock($fp, LOCK_UN);
	fclose($fp);


	// -----------------------------------
	// Ecriture du fichier checksum
	// -----------------------------------

	$fp = fopen($filename.'.md5', "w");
	flock($fp, LOCK_EX);
	fwrite($fp, md5_file($filename));
	flock($fp, LOCK_UN);
	fclose($fp);
}

function variante_catalogue_getprixarticle($article, $artqte = 1, $brut = 0, $id_cmd = -1, $taxe_phyto = true) {
	// Chargement des données en session
	if (!isset($_SESSION['catalogue']['prix_net'])) {
		catalogue_load_prices();
	}

	$db = dims::getInstance()->getDb();
	//$ts = dims_createtimestamp();

	if ($brut) {
		$prix = $article->getPrix();
	}
	else {
		// Calcul du prix du client

		// Recherche de la remise
		$remise = catalogue_getRemise($article);
		$prix = $article->getprix() * (1 - $remise / 100);

		switch ($_SESSION['catalogue']['regles_remises']) {
			case cata_param::REGLE_CALCUL_PRIORITE_MOINS_CHER:
				// recherche de prix net
				// On teste les 2 prix nets
				if (
						isset($_SESSION['catalogue']['prix_net_c'][$article->getReference()])
					&&	$_SESSION['catalogue']['prix_net_c'][$article->getReference()] < $prix
				) {
					$prix = $_SESSION['catalogue']['prix_net_c'][$article->getReference()];
				}
				if (
						isset($_SESSION['catalogue']['prix_net_m'][$article->getReference()])
					&&	$_SESSION['catalogue']['prix_net_m'][$article->getReference()] < $prix
				) {
					$prix = $_SESSION['catalogue']['prix_net_m'][$article->getReference()];
				}

				// Recherche d'un prix quantitatif
				if ( $artqte > 1 && isset($_SESSION['catalogue']['prix_qte'][$article->fields['reference']]) ) {
					foreach ($_SESSION['catalogue']['prix_qte'][$article->fields['reference']] as $qte => $puqte) {
						if ($artqte >= $qte && $puqte < $prix) {
							$prix = $puqte;
							break;
						}
					}
				}
				if ( $artqte > 1 && isset($_SESSION['catalogue']['prix_qte_c'][$article->fields['reference']]) ) {
					foreach ($_SESSION['catalogue']['prix_qte_c'][$article->fields['reference']] as $qte => $puqte) {
						if ($artqte >= $qte && $puqte < $prix) {
							$prix = $puqte;
							break;
						}
					}
				}
				if ( $artqte > 1 && isset($_SESSION['catalogue']['prix_qte_m'][$article->fields['reference']]) ) {
					foreach ($_SESSION['catalogue']['prix_qte_m'][$article->fields['reference']] as $qte => $puqte) {
						if ($artqte >= $qte && $puqte < $prix) {
							$prix = $puqte;
							break;
						}
					}
				}
				break;
			case cata_param::REGLE_CALCUL_PRIORITE_MARCHE:
				// recherche de prix net
				// On teste d'abord le prix net du marché
				if ( isset($_SESSION['catalogue']['prix_net_m'][$article->getReference()]) ) {
					$prix = $_SESSION['catalogue']['prix_net_m'][$article->getReference()];
				}
				elseif ( isset($_SESSION['catalogue']['prix_net_c'][$article->getReference()]) ) {
					$prix = $_SESSION['catalogue']['prix_net_c'][$article->getReference()];
				}

				// Recherche d'un prix quantitatif
				if ( $artqte > 1 && isset($_SESSION['catalogue']['prix_qte_m'][$article->fields['reference']]) ) {
					foreach ($_SESSION['catalogue']['prix_qte_m'][$article->fields['reference']] as $qte => $puqte) {
						if ($artqte >= $qte && $puqte < $prix) {
							$prix = $puqte;
							break;
						}
					}
				}
				elseif ( $artqte > 1 && isset($_SESSION['catalogue']['prix_qte_c'][$article->fields['reference']]) ) {
					foreach ($_SESSION['catalogue']['prix_qte_c'][$article->fields['reference']] as $qte => $puqte) {
						if ($artqte >= $qte && $puqte < $prix) {
							$prix = $puqte;
							break;
						}
					}
				}
				elseif ( $artqte > 1 && isset($_SESSION['catalogue']['prix_qte'][$article->fields['reference']]) ) {
					foreach ($_SESSION['catalogue']['prix_qte'][$article->fields['reference']] as $qte => $puqte) {
						if ($artqte >= $qte && $puqte < $prix) {
							$prix = $puqte;
							break;
						}
					}
				}
				break;
			case cata_param::REGLE_CALCUL_PRIORITE_CLIENT:
				// recherche de prix net
				// On teste d'abord le prix net du client
				if ( isset($_SESSION['catalogue']['prix_net_c'][$article->getReference()]) ) {
					$prix = $_SESSION['catalogue']['prix_net_c'][$article->getReference()];
				}
				elseif ( isset($_SESSION['catalogue']['prix_net_m'][$article->getReference()]) ) {
					$prix = $_SESSION['catalogue']['prix_net_m'][$article->getReference()];
				}

				// Recherche d'un prix quantitatif
				if ( $artqte > 1 && isset($_SESSION['catalogue']['prix_qte_c'][$article->fields['reference']]) ) {
					foreach ($_SESSION['catalogue']['prix_qte_c'][$article->fields['reference']] as $qte => $puqte) {
						if ($artqte >= $qte && $puqte < $prix) {
							$prix = $puqte;
							break;
						}
					}
				}
				elseif ( $artqte > 1 && isset($_SESSION['catalogue']['prix_qte_m'][$article->fields['reference']]) ) {
					foreach ($_SESSION['catalogue']['prix_qte_m'][$article->fields['reference']] as $qte => $puqte) {
						if ($artqte >= $qte && $puqte < $prix) {
							$prix = $puqte;
							break;
						}
					}
				}
				elseif ( $artqte > 1 && isset($_SESSION['catalogue']['prix_qte'][$article->fields['reference']]) ) {
					foreach ($_SESSION['catalogue']['prix_qte'][$article->fields['reference']] as $qte => $puqte) {
						if ($artqte >= $qte && $puqte < $prix) {
							$prix = $puqte;
							break;
						}
					}
				}
				break;
		}
	}

	// // Application des promotions
	// if (!$brut) {
	// 	if (
	// 		isset($_SESSION['catalogue']['promo']['unlocked']["'".strtoupper($article->getReference())."'"]) &&
	// 		$_SESSION['catalogue']['promo']['unlocked']["'".strtoupper($article->getReference())."'"] < $prix
	// 	) {
	// 		$prix = $_SESSION['catalogue']['promo']['unlocked']["'".strtoupper($article->getReference())."'"];
	// 	}
	// }

	if ($taxe_phyto) {
		$prix += $article->get('taxe_certiphyto');
	}

	return round($prix, 2);
}

// Calcule les frais de port en fonction du code postal,
// du poids et de la dangerosité des produits
// Les frais de port ne doivent plus être calculés à partir de 50 Kg
// Les produits sont considérés dangereux si :
// - ils sont sous la famille 101 (Produits de traitement) et le code de dangerosité est différent de NULL ou "NC"
// - ils sont sous la famille 109 (Produits chimiques) et le code de dangerosité est "5.1" ou "8"
function variante_get_fraisport($id_pays = -1, $codepostal = -1, $total = 0, $transporteur_id = 0, $articles = array(), $hayon_necessaire) {
    $voluminous         = false;
    $missingWeight      = false;
    $totalWeight        = 0;
    $weight101          = 0;
    $weight109          = 0;

	$fp_montant 		= 0;
	$fp_franco 			= 0;
	$require_costing 	= 1;

	foreach ($articles as $article) {
        $missingWeight  |= ($article['weight'] == 0);
        $voluminous     |= (bool)$article['voluminous'];
        $totalWeight    += $article['weight'] * $article['qte_cde'];
        $weight101      += ($article['fam'] == '101' && !empty($article['dangerousness']) && $article['dangerousness'] != 'NC') ? $article['weight'] * $article['qte_cde'] : 0;
        $weight109      += ($article['fam'] == '109' && ($article['dangerousness'] == 5.1 || $article['dangerousness'] == 8)) ? $article['weight'] * $article['qte_cde'] : 0;
	}

	if ($totalWeight > 0 && $transporteur_id > 0) {
		require_once DIMS_APP_PATH.'modules/catalogue/include/class_carrier.php';

		$carrier = new cata_carrier();
		$carrier->open($transporteur_id);
		if ($carrier->fields['id']) {
			$county = (int)substr($codepostal, 0, 2);
			$fp_montant = $carrier->getCarriageAmount($county, $totalWeight);

			// Si on trouve une correspondance dans la grille des tarifs
			// on n'a pas besoin de demander un chiffrage
			if (!is_null($fp_montant)) {
				$require_costing = 0;

				// Si un hayon est nécessaire, on ajoute le coût du hayon
				if ($hayon_necessaire) {
					if (!empty($_SESSION['catalogue']['moduleid'])) {
						$catalogue_moduleid = $_SESSION['catalogue']['moduleid'];
					}
					else {
						$dims = dims::getInstance();
						$mods = $dims->getModuleByType('catalogue');
						$catalogue_moduleid = $mods[0]['instanceid'];
					}

					require_once DIMS_APP_PATH.'modules/catalogue/include/class_catalogue.php';
					$oCatalogue = new catalogue();
					$oCatalogue->open($catalogue_moduleid);
					$oCatalogue->loadParams();

					$fp_montant += $oCatalogue->getParams('supplement_hayon');
				}
			}
		}
	}

	if ($voluminous) {
	    $fp_montant         = 0;
	    $require_costing    = 1;
	}

	if ($missingWeight) {
	    $fp_montant         = 0;
	    $require_costing    = 1;
	}

	if (($weight101 + $weight109) >= DANGEROUSNESS_WEIGHT_LIMIT) {
	    $fp_montant         = 0;
	    $require_costing    = 1;
	}

	return array(
		'fp_montant' 		=> number_format($fp_montant, 2, '.', ''),
		'fp_franco' 		=> number_format($fp_franco, 2, '.', ''),
		'fp_codepostal' 	=> $codepostal,
		'require_costing' 	=> $require_costing
		);
}
