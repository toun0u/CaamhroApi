<?php
if (!empty($_SESSION['catalogue']['panier']['articles'])) {
	// TRI DES COLONNES
	if (!isset($order)) $order = 'part';
	if (!isset($reverse)) $reverse = 0;
	if ($reverse == -1) $reverse = !$reverse; // On inverse le sens

	// On met en session la combinaison op / order / reverse
	$_SESSION['catalogue']['oporder']['op'] = $op;
	$_SESSION['catalogue']['oporder']['order'] = $order;
	$_SESSION['catalogue']['oporder']['reverse'] = $reverse;
	// FIN - TRI DES COLONNES

	// On cree un tableau qui contient la position de chaque ref dans le panier
	$part = array();
	$i = 0;
	foreach ($_SESSION['catalogue']['panier']['articles'] as $ref => $fields) {
		$part[$ref] = $i;
		$i++;
	}

	$keys = array_keys($_SESSION['catalogue']['panier']['articles']);
	$liste = '"'.implode('","', $keys).'"';

	// recherche des articles du panier dans la bd
	$articles = array();
	$sql = 'SELECT *
			FROM `dims_mod_cata_article`
			WHERE `reference` IN ('.$liste.')
			AND `id_workspace` = '.$_SESSION['dims']['workspaceid'];
	$rs = $db->query($sql);
	while ($fields = $db->fetchrow($rs)) {
		$articles[$fields['reference']]                 = $fields;
		$articles[$fields['reference']]['part']         = $part[$fields['reference']];
	}

	$articles_tri = array();
	$i = 0;
	foreach ($articles as $ref_art => $art) {
		($order == '') ? $norder = $i : $norder = $articles[$ref_art][$order];
		$articles_tri[$norder][$ref_art] = $articles[$ref_art];
		$i++;
	}
	ksort($articles_tri);
	if (isset($reverse) && $reverse == 1) $articles_tri = array_reverse($articles_tri);


	$VATs = array();
	$total_ht = 0;
	$a_total_tva = array();

	// Possibilité pour les commerciaux de renseigner les frais de port depuis le panier
	$port = 0;
	if ( !empty($_SESSION['dims']['previous_user']) && isset($_SESSION['catalogue']['panier']['forced_frais_port']) ) {
		$port = $_SESSION['catalogue']['panier']['forced_frais_port'];

		$default_tva = round(floatval($oCatalogue->getParams('default_tva')), 2);
		$a_total_tva['fp'] = $port * $default_tva / 100;
	}


	$id = 0;

	// Contenu du panier pour envoi de mail de notification si stocks insuffisants
	$panier = array();

	foreach ($articles_tri as $refs) {
		foreach ($refs as $art) {
			$id++;

			$article = new article();
			$article->openFromResultSet($art);

			$art['id'] = $id;
			$ref = $art['reference'];
			$qte = $_SESSION['catalogue']['panier']['articles'][$ref]['qte'];

			if (isset($_SESSION['catalogue']['panier']['articles'][$ref]['forced_price'])) {
				$pu_ht = $_SESSION['catalogue']['panier']['articles'][$ref]['forced_price'];
			}
			else {
				$pu_ht = catalogue_getprixarticle($article, $qte, false, true);
			}
			$pu_ttc = round($pu_ht * (1 + $a_tva[$article->fields['ctva']] / 100), 2);
			$total_ligne = round($pu_ht * $qte, 2);

			$art['qte_cde'] = $qte;
			$art['pu_ht'] = catalogue_formateprix($pu_ht);
			$art['pu_ttc'] = catalogue_formateprix($pu_ttc);
			$art['somme_ht'] = catalogue_formateprix($total_ligne);

			if (!isset($a_total_tva[$article->fields['ctva']])) {
				$a_total_tva[$article->fields['ctva']] = 0;
			}
			$art['tot_tva'] = catalogue_formateprix($pu_ht * $qte * $a_tva[$article->fields['ctva']] / 100);
			$art['tva'] = $a_tva[$article->fields['ctva']];
			$a_total_tva[$article->fields['ctva']] += $pu_ht * $qte * $a_tva[$article->fields['ctva']] / 100;
			$total_ht += $total_ligne;

			if ($art['prcs'] != 0) {
			    $art['margin'] = catalogue_formateprix(round(($pu_ht * 100 / $art['prcs']) - 100)).'%';
			} else {
			    $art['margin'] = ' - ';
			}

			$commande['articles'][] = $art;

			// On vérifie que les quantités sont disponibles
			if ($oCatalogue->getParams('block_if_not_enough_stock')) {
				if (isset($_SESSION['catalogue']['id_company'])) {
					$stock_total = $article->getStockTotal($_SESSION['catalogue']['id_company']);
					$end_of_life = $article->isEndOfLife($_SESSION['catalogue']['id_company']);
				}
				else {
					$stock_total = $article->getStockTotal();
					$end_of_life = $article->isEndOfLife();
				}

				$panier[] = array(
					'ref' 			=> $ref,
					'label' 		=> $article->getLabel(),
					'qte' 			=> $qte,
					'stock' 		=> $stock_total,
					'end_of_life' 	=> $end_of_life
					);

				if ($qte > $stock_total) {
					$_SESSION['catalogue']['errors'][0] = 'La quantité demandée est indisponible :';
					$error = " - ".$article->getLabel().' ('.$stock_total.' disponibles)';
					if ($end_of_life) {
						$error .= ' - Derniers en stock, pas de réappro possible';
					}
					$_SESSION['catalogue']['errors'][] = $error;
				}
			}

		}
	}

	// Envoi de mail de notification au commercial si stock insuffisant
	if ( isset($_SESSION['catalogue']['errors']) && !empty($_SESSION['catalogue']['code_client']) ) {
		$client = new client();
		$client->openByCode($_SESSION['catalogue']['code_client']);
		if ($client->fields['representative_id'] > 0) {
			$representative = user::find_by(array('representative_id' => $client->fields['representative_id']), null, 1);
			if ( !is_null($representative) && substr($representative->get('lastname'), 0, 8) != 'CELLULE_' && $representative->get('email') != '' ) {

				// recherche du template
				$lstwcemods = $dims->getWceModules();
				$wce_module_id = (!empty($lstwcemods)) ? current($lstwcemods) : 0;

				$headings = wce_getheadings($wce_module_id);
				$headingid = (isset($headings['tree'][0][0])) ? $headings['tree'][0][0] : 0;

				$template_name = (!empty($headings['list'][$headingid]['template'])) ? $headings['list'][$headingid]['template'] : 'default';
				// FIN - recherche du template

				require_once DIMS_APP_PATH.'/modules/catalogue/include/class_commande.php';
				commande::sendNotEnoughStockMail($representative, $client, $panier, $template_name);
			}
		}
	}


	$total_tva = 0;
	foreach ($a_total_tva as $totaltva) {
		$total_tva += round($totaltva, 2);
	}
	$total_ttc = $total_ht + $port + $total_tva;

	foreach($a_total_tva as $VATCode => $VATvalue) {
		if ($VATCode != 'fp') {
			$VAT = current(tva::findByCode($VATCode));

			$VATs[$VATCode] = array(
				'code'  => $VAT->fields['id_tva'],
				'label' => $VAT->fields['description'],
				'rate'  => catalogue_formateprix($VAT->fields['tx_tva']),
				'value' => catalogue_formateprix($VATvalue),
			);
		}
		else {
			$VATs[$VATCode] = array(
				'code'  => 'FP',
				'label' => dims_constant::getVal('_SHIPPING_FEES'),
				'rate'  => catalogue_formateprix(round(floatval($oCatalogue->getParams('default_tva')), 2)),
				'value' => catalogue_formateprix($VATvalue),
			);
		}
	}

	$commande['VATs'] 		= $VATs;
	$commande['total_ht'] 	= catalogue_formateprix($total_ht);
	$commande['port'] 		= catalogue_formateprix($port);
	$commande['total_tva'] 	= catalogue_formateprix($total_tva);
	$commande['total_ttc'] 	= catalogue_formateprix($total_ttc);

	$smarty->assign('commande', $commande);

	// URL de retour "Continuer mes achats"
	if (isset($_SESSION['catalogue']['achats_url'])) {
		$achats_action = 'javascript:document.location.href=\''.$_SESSION['catalogue']['achats_url'].'\'';
	}
	else {
		$achats_action = 'javascript:document.location.href=\'/\';';
	}
	$smarty->assign('achats_action', $achats_action);

	// Possibilité de modifier les prix ?
	$smarty->assign('editables_prices', !empty($_SESSION['dims']['previous_user']));
	$smarty->assign('display_vrp_prices', !empty($_SESSION['dims']['previous_user']));


	if (!empty($_SESSION['catalogue']['errors'])) {
		$smarty->assign('errors', $_SESSION['catalogue']['errors']);
		unset($_SESSION['catalogue']['errors']);
	}

}
