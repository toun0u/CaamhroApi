<?php

if ($_SESSION['dims']['connected']) {
	include_once DIMS_APP_PATH.'modules/catalogue/include/class_article.php';
	include_once DIMS_APP_PATH.'modules/catalogue/include/class_client.php';
	include_once DIMS_APP_PATH.'modules/catalogue/include/class_commande.php';
	include_once DIMS_APP_PATH.'modules/catalogue/include/class_commande_ligne.php';
	include_once DIMS_APP_PATH.'modules/catalogue/include/class_commande_ligne_hc.php';
	include_once DIMS_APP_PATH."modules/catalogue/include/class_cata_depot.php";

	ob_start();

	//recherche du id_client a partir de la session
	if (empty($_SESSION['catalogue']['client_id'])) {
		dims_redirect($dims->getScriptEnv().'?op=panier');
	}

	// si c'est une commande, on vérifie que l'utilisateur a bien le droit
	$cde = new commande();
	if ($op == 'valider_commande') {
		$cde_opened = false;
		$id_cmd = dims_load_securvalue('id_cmd', dims_const::_DIMS_NUM_INPUT, true, true);

		if (!empty($id_cmd)) {
			$cde->open($id_cmd);
			$_SESSION['catalogue']['id_cde'] = $id_cmd;
			$cde_opened = true;

			// On recherche l'adresse en fonction de son numéro de depot
			$depot = cata_depot::find_by(array('depot' => $cde->get('num_depot')), null, null, 1);

			if (!empty($depot)) {
				$_SESSION['catalogue']['numdepot'] = $depot->get('depot');
				$_SESSION['catalogue']['date_livraison'] = $cde->fields['date_livraison'];
			} else {
				$_SESSION['catalogue']['numdepot'] = 0;
				// Default delivery address
			}
		}
		elseif (!empty($_SESSION['catalogue']['id_cde'])) {
			$cde->open($_SESSION['catalogue']['id_cde']);
			$cde_opened = true;
		}

		if ($cde->isNew() || !$cde->isValideable()) {
			dims_redirect($dims->getScriptEnv().'?op=commandes');
		}
	}

	// si on valide une commande, on ouvre le client de la commande
	// sinon, on prend le client en cours
	// sert uniquement dans le cas d'un validateur sur plusieurs comptes client
	if (!$cde->isNew()) {
		$obj_client = new client();
		$obj_client->open($cde->fields['id_client']);

		$user = new user();
		$user->open($cde->fields['id_user']);
	}
	else {
		$obj_client = new client();
		$obj_client->open($_SESSION['catalogue']['client_id']);

		$user = new user();
		$user->open($_SESSION['dims']['userid']);
	}


	// chargement des modes de paiement
	$a_mp = $obj_client->getPaymentMeans();
	$a_modes_paiement = array();
	foreach ($a_mp as $mp) {
		$a_modes_paiement[$mp->get('id')] = array(
			'value' 		=> $mp->get('id'),
			'label' 		=> $mp->getLabel(),
			'description' 	=> $mp->getDescription(),
			// si un seul moyen de paiement, on le coche par défaut
			'checked'		=> (sizeof($a_mp) == 1) ? true : false,
			// si client bloqué, le paiement différé est grisé
			'disabled'		=> ($obj_client->isBlocked() && $mp->getType() == moyen_paiement::_TYPE_DIFFERE),
			);
	}


	$etape = dims_load_securvalue('etape', dims_const::_DIMS_CHAR_INPUT, true, true);
	if (empty($etape)) $etape = 1;

	if (!isset($_SESSION['catalogue']['delivery_conditions']['semi_remorque_possible']))    $_SESSION['catalogue']['delivery_conditions']['semi_remorque_possible'] = '';
	if (!isset($_SESSION['catalogue']['delivery_conditions']['hauteur_maxi']))              $_SESSION['catalogue']['delivery_conditions']['hauteur_maxi'] = '';
	if (!isset($_SESSION['catalogue']['delivery_conditions']['hayon_necessaire']))          $_SESSION['catalogue']['delivery_conditions']['hayon_necessaire'] = '';
	if (!isset($_SESSION['catalogue']['delivery_conditions']['tire_pal_necessaire']))       $_SESSION['catalogue']['delivery_conditions']['tire_pal_necessaire'] = '';
	if (!isset($_SESSION['catalogue']['delivery_conditions']['camion_autre']))              $_SESSION['catalogue']['delivery_conditions']['camion_autre'] = '';
	if (!isset($_SESSION['catalogue']['delivery_conditions']['impossibilites_lirvaison']))  $_SESSION['catalogue']['delivery_conditions']['impossibilites_lirvaison'] = '';
	if (!isset($_SESSION['catalogue']['delivery_conditions']['contact_nom']))               $_SESSION['catalogue']['delivery_conditions']['contact_nom'] = '';
	if (!isset($_SESSION['catalogue']['delivery_conditions']['contact_prenom']))            $_SESSION['catalogue']['delivery_conditions']['contact_prenom'] = '';
	if (!isset($_SESSION['catalogue']['delivery_conditions']['contact_tel']))               $_SESSION['catalogue']['delivery_conditions']['contact_tel'] = '';
	if (!isset($_SESSION['catalogue']['delivery_conditions']['contact_autre']))             $_SESSION['catalogue']['delivery_conditions']['contact_autre'] = '';
	if (!isset($_SESSION['catalogue']['delivery_conditions']['libelle']))               	$_SESSION['catalogue']['delivery_conditions']['libelle'] = '';
	if (!isset($_SESSION['catalogue']['delivery_conditions']['commentaire']))               $_SESSION['catalogue']['delivery_conditions']['commentaire'] = '';


	switch ($etape) {
		case 1:
			// on tient compte des qtés modifiées
			$devis = false;
			$contentDevis = "";
			$totalttc = 0;
			if (isset($_POST['id'])) {
				$old_panier = $_SESSION['catalogue']['panier'];

				$_SESSION['catalogue']['panier']['articles'] = array();
				$_SESSION['catalogue']['panier']['montant'] = 0;

				foreach ($_POST['id'] as $id_article) {
					$qte = dims_load_securvalue('qte'.$id_article, dims_const::_DIMS_NUM_INPUT, false, true);
					$ref = dims_load_securvalue('ref'.$id_article, dims_const::_DIMS_CHAR_INPUT, false, true);
					$del = dims_load_securvalue('del'.$id_article, dims_const::_DIMS_NUM_INPUT, false, true);

					$article = new article();
					$article->findByRef($ref);

					// Prix forcés
					if (!empty($_SESSION['dims']['previous_user'])) {
						$forced_price = dims_load_securvalue('price'.$id_article, dims_const::_DIMS_CHAR_INPUT, false, true, true);
						if ($forced_price == '' && isset($old_panier['articles'][$ref]['forced_price'])) {
							$forced_price = $old_panier['articles'][$ref]['forced_price'];
						}
						$prix = $forced_price;
					}
					else {
						$prix = catalogue_getprixarticle($article, $qte);
					}

					$prixaff = catalogue_afficherprix($prix, $a_tva[$article->fields['ctva']]);

					// reference a ajouter au panier
					if (isset($ref) && isset($qte) && isset($prixaff) && empty($del)) {
						if($article->get('shipping_costs') == article::SHIPPING_COST_DEVIS){
							$devis = true;
						}
						if (!isset($_SESSION['catalogue']['panier']['articles'][$ref]['qte'])) $_SESSION['catalogue']['panier']['articles'][$ref]['qte'] = 0;
						$_SESSION['catalogue']['panier']['articles'][$ref]['qte'] += $qte;

						if (isset($forced_price) && $forced_price != '') {
							$_SESSION['catalogue']['panier']['articles'][$ref]['forced_price'] = round(floatval(str_replace(',', '.', $forced_price)), 2);
						}

						if ($oCatalogue->getParams('cata_base_ttc')) {
							$_SESSION['catalogue']['panier']['montant'] += $qte*$prixaff;
						}
						else {
							$_SESSION['catalogue']['panier']['montant'] += $qte*$prix;
						}
						$totalttc += $qte*$article->calculate_PUTTC();
						$contentDevis .= '
						<tr>
							<td>'.$article->get('reference').'</td>
							<td>'.$article->get('label').'</td>
							<td>'.$qte.'</td>
							<td>'.catalogue_formateprix($article->getPUHT()).'</td>
							<td>'.catalogue_formateprix($qte*$article->getPUHT()).'</td>
							<td>'.(($article->get('shipping_costs') == article::SHIPPING_COST_DEVIS)?dims_constant::getVal('_DIMS_YES'):dims_constant::getVal('_DIMS_NO')).'</td>
						</tr>';

						// On vérifie que les quantités sont disponibles
						if ($oCatalogue->getParams('block_if_not_enough_stock')) {
							if (isset($_SESSION['catalogue']['id_company'])) {
								$stock_total = $article->getStockTotal($_SESSION['catalogue']['id_company']);
							}
							else {
								$stock_total = $article->getStockTotal();
							}

							if ($qte > $stock_total) {
								dims_redirect($_SERVER['HTTP_REFERER']);
							}
						}
					}
				}
			} elseif (!empty($_SESSION['catalogue']['panier']['articles'])) {
				foreach ($_SESSION['catalogue']['panier']['articles'] as $ref => $v) {
					$article = new article();
					$article->findByRef($ref);
					if ($article->get('shipping_costs') == article::SHIPPING_COST_DEVIS) {
						$devis = true;
					}
					$totalttc += $v['qte']*$article->calculate_PUTTC();
					$contentDevis .= '
					<tr>
						<td>'.$article->get('reference').'</td>
						<td>'.$article->get('label').'</td>
						<td>'.$v['qte'].'</td>
						<td>'.catalogue_formateprix($article->getPUHT()).'</td>
						<td>'.catalogue_formateprix($v['qte']*$article->getPUHT()).'</td>
						<td>'.(($article->get('shipping_costs') == article::SHIPPING_COST_DEVIS)?dims_constant::getVal('_DIMS_YES'):dims_constant::getVal('_DIMS_NO')).'</td>
					</tr>';

					// On vérifie que les quantités sont disponibles
					if ($oCatalogue->getParams('block_if_not_enough_stock')) {
						if (isset($_SESSION['catalogue']['id_company'])) {
							$stock_total = $article->getStockTotal($_SESSION['catalogue']['id_company']);
						}
						else {
							$stock_total = $article->getStockTotal();
						}

						if ($v['qte'] > $stock_total) {
							dims_redirect($_SERVER['HTTP_REFERER']);
						}
					}
				}
			}

			if ($devis) {
				// on envoie le mail pour le devis (c'est fini)
				$contentDevis = '
				<p>
					<strong>'.dims_constant::getVal('_COMPANY_CT').' / '.dims_constant::getVal('_DIMS_LABEL_LASTNAME').' / '.dims_constant::getVal('_DIMS_LABEL_FIRSTNAME').' :</strong> '.$obj_client->fields['nom'].'<br />
					<strong>'.dims_constant::getVal('_DIMS_LABEL_EMAIL').' :</strong> '.$obj_client->fields['email'].'<br />
					<strong>'.dims_constant::getVal('_DIRECTORY_PHONE').' :</strong> '.catalogue_display_tel($obj_client->fields['tel1']).($obj_client->fields['tel2']!=''?(' / '.catalogue_display_tel($obj_client->fields['tel2'])):'').'<br />
					<strong>'.dims_constant::getVal('_MOBILE').' :</strong> '.catalogue_display_tel($obj_client->fields['port']).'<br />
					<strong>'.dims_constant::getVal('_DIMS_LABEL_FAX').' :</strong> '.catalogue_display_tel($obj_client->fields['fax']).'<br />
					<strong>'.dims_constant::getVal('_DIMS_LABEL_ADDRESS').' :</strong> <br />'.$obj_client->fields['adr1'].'<br />
					'.($obj_client->fields['adr2']!=''?($obj_client->fields['adr2'].'<br />'):'').'
					'.($obj_client->fields['adr3']!=''?($obj_client->fields['adr3'].'<br />'):'').'
					'.$obj_client->fields['cp'].' '.$obj_client->fields['ville'].'<br />
					'.$obj_client->getCountry().'
				</p>
				<table style="width:100%;">
					<tr>
						<th>'.dims_constant::getVal('_REF_ARTICLE').'</th>
						<th>'.dims_constant::getVal('_DIMS_LABEL').'</th>
						<th>'.dims_constant::getVal('QUANTITY').'</th>
						<th>'.dims_constant::getVal('CATA_UNIT_PRICE_HT').'</th>
						<th>'.dims_constant::getVal('PRICE_TTC').'</th>
						<th>'.dims_constant::getVal('_SHIPPING_COSTS_ON_QUOTE').'</th>
					</tr>
					'.$contentDevis.'
					<tr>
						<td style="text-align:right;" colspan="6">'.dims_constant::getVal('CATA_TOTAL_TTC').' : '.catalogue_formateprix($totalttc).'</td>
					</tr>
				</table>';

				$to = array();
				$adresses = explode(',', $oCatalogue->getParams('notif_send_mail'));
				foreach ($adresses as $addr_mail) {
					$to[] = array(
						'name'=>$obj_client->fields['nom'],
						'address'=>$addr_mail,
					);
				}
				dims_send_mail($obj_client->fields['email'], $to, 'Demande de devis', $contentDevis);
				/* TODO :
				* - faire le lien avec la gescom
				* - vider le panier
				* - que fait-on de la commande ?
				*/
				$_SESSION['catalogue']['msg_confirm'] = "La demande de devis a été transmise. Nous vous répondrons dans les plus brefs délais";
				unset($_SESSION['catalogue']['panier']);
				dims_redirect($dims->getScriptEnv().'?op=valider_panier&etape=5');
			}

			// on vérifie le minimum de commande
			if ($op == 'valider_commande') {
				if ($cde->fields['total_ht'] < $obj_client->fields['minimum_cde']) {
					$_SESSION['catalogue']['cde_operation']['cmd_validate']['error'] = 'Montant HT des marchandises insuffisant.';
					dims_redirect($dims->getScriptEnv().'?op=commandes');
				}
			}
			else {
				if ($_SESSION['catalogue']['panier']['montant'] < $obj_client->fields['minimum_cde']) {
					if (!isset($_SESSION['catalogue']['errors'])) $_SESSION['catalogue']['errors'] = array();
					$_SESSION['catalogue']['errors'][] = 'Montant HT des marchandises insuffisant.';
					dims_redirect($dims->getScriptEnv().'?op=panier');
				}
			}

			$catalogue['op'] = $op;
			if ($op == 'valider_commande') {
				$id_cmd = dims_load_securvalue('id_cmd', dims_const::_DIMS_NUM_INPUT, true, false);
				if ($id_cmd == '' && $_SESSION['catalogue']['id_cde'] > 0) {
					$id_cmd = $_SESSION['catalogue']['id_cde'];
				}
				else {
					$_SESSION['catalogue']['id_cde'] = $id_cmd;
				}
				$catalogue['id_cmd'] = $id_cmd;
			}

			$catalogue['client'] = array(
					'NOM'			=> $obj_client->fields['nom'],
					'ADR1'			=> $obj_client->fields['adr1'],
					'ADR2'			=> $obj_client->fields['adr2'],
					'ADR3'			=> $obj_client->fields['adr3'],
					'CP'			=> $obj_client->fields['cp'],
					'VILLE'			=> $obj_client->fields['ville'],
					'ID_PAYS'		=> $obj_client->fields['id_pays'],
					'PAYS'			=> $obj_client->getCountry(),
					'TEL1'			=> catalogue_display_tel($obj_client->fields['tel1']),
					'TEL2'			=> catalogue_display_tel($obj_client->fields['tel2']),
					'PORT'			=> catalogue_display_tel($obj_client->fields['port']),
					'FAX'			=> catalogue_display_tel($obj_client->fields['fax']),
					'EMAIL'			=> (!empty($user->fields['email']) ? $user->fields['email'] : $obj_client->fields['email']),
					// 'NEWSLETTER'		=> $newsletter
				);

			// Adresses de livraison
			$a_depots = $obj_client->getDepots();
			foreach ($a_depots as $depot) {
				$adr = trim($depot->fields['adr1']);
				if (!empty($depot->fields['adr2'])) {
					if (!empty($adr)) $adr .= '<br/>';
					$adr .= trim($depot->fields['adr2']);
				}
				if (!empty($depot->fields['adr3'])) {
					if (!empty($adr)) $adr .= '<br/>';
					$adr .= trim($depot->fields['adr3']);
				}

				$catalogue['client']['depots'][] = array(
					'ID'		=> $depot->fields['id'],
					'NUMDEPOT'	=> $depot->fields['depot'],
					'NUMADR'	=> $depot->fields['depot'] + 1,
					'CHECKED'	=> ($depot->fields['depot'] == 0) ? 'checked' : '',
					'CIVILITE'	=> $depot->fields['civilite'],
					'NOM'		=> $depot->fields['nomlivr'],
					'ADR'		=> str_replace("\n", '<br>', $adr),
					'CP'		=> $depot->fields['cp'],
					'VILLE'		=> $depot->fields['ville'],
					'ID_PAYS' 	=> $depot->fields['id_pays'],
					'PAYS'	 	=> $depot->getCountryLabel()
					);
			}

			// Adresses de facturation
			$a_adrFact = $obj_client->getAdrFact();

			// on fait le tour des adresses une 1e fois
			// pour savoir si on affiche toutes les adresses ou pas
			foreach ($a_adrFact as $id => $adrFact) {
				if ($adrFact['force']) {
					$a_adrFact = array_intersect_key($a_adrFact, array($id => $adrFact));
					break;
				}
			}

			foreach($a_adrFact as $id => $adrFact) {
				$adr = $adrFact['nom'].'<br/>';
				if (!empty($adrFact['adr1'])) {
					$adr .= $adrFact['adr1'].'<br/>';
				}
				if (!empty($adrFact['adr2'])) {
					$adr .= $adrFact['adr2'].'<br/>';
				}
				if (!empty($adrFact['adr3'])) {
					$adr .= $adrFact['adr3'].'<br/>';
				}
				$adr .= $adrFact['cp'].' '.$adrFact['ville'];

				$catalogue['client']['adresses_facturation'][] = array(
					'ID'		=> $id,
					'ADDRESS'	=> $adr,
					'SELECTED'	=> ( (!empty($cde->fields['adrfact']) && $cde->fields['adrfact'] == $id) || (sizeof($a_adrFact) == 1) )
					);
			}

			$smarty->assign('missingaddr',dims_load_securvalue('missingaddr',dims_const::_DIMS_NUM_INPUT,true,true,true));
			$smarty->assign('missingemail',dims_load_securvalue('missingemail',dims_const::_DIMS_NUM_INPUT,true,true,true));

			$smarty->assign('tpl_name', 'coordonnees');
			$smarty->assign('catalogue', $catalogue);
			break;
		case 2:
			// Vérification de l'adresse de livraison
			if (!isset($_POST['numdepot'])) {
				dims_redirect('/index.php?op='.$op."&missingaddr=1");
			}

			// verification des coordonnees
			$obj_client->setvalues($_POST,'data_');
			$obj_client->save();

			// Enregistrement de l'adresse email
			$user_email = dims_load_securvalue('user_email', dims_const::_DIMS_MAIL_INPUT, false, true, true);
			if ($user_email == '') {
				dims_redirect('/index.php?op='.$op."&missingemail=1");
			}

			$user->fields['email'] = $user_email;
			$user->save();

			$_SESSION['catalogue']['numdepot'] = dims_load_securvalue('numdepot', dims_const::_DIMS_NUM_INPUT, true, true, true);
			$_SESSION['catalogue']['adrfact'] = dims_load_securvalue('adr_facturation', dims_const::_DIMS_CHAR_INPUT, false, true);

			if ($_SESSION['catalogue']['numdepot'] == -1) {
				$_SESSION['catalogue']['date_enlevement'] 	= dims_load_securvalue('date_enlevement', dims_const::_DIMS_CHAR_INPUT, false, true);
				$_SESSION['catalogue']['heure_enlevement'] 	= dims_load_securvalue('heure_enlevement', dims_const::_DIMS_CHAR_INPUT, false, true);

				if ($op == 'valider_commande') {
					$cde->set('require_costing', 0);
					$cde->save();
				}
			}
			else {
				$_SESSION['catalogue']['date_livraison'] 	= dims_load_securvalue('date_livraison', dims_const::_DIMS_CHAR_INPUT, false, true);

				if ($op == 'valider_commande') {
					$cde->set('require_costing', 1);
					$cde->save();
				}
			}

			dims_redirect('/index.php?op='.$op.'&etape=3');
			break;
		case 3:
			if (!isset($_SESSION['catalogue']['numdepot'])) {
				dims_redirect('/index.php?op=valider_panier&etape=1');
			}

			// Frais de port renseignés par le commercial
			$pricefp = dims_load_securvalue('pricefp', dims_const::_DIMS_CHAR_INPUT, false, true, true);
			if (!empty($_SESSION['dims']['previous_user']) && $pricefp > 0) {
				$_SESSION['catalogue']['panier']['forced_frais_port'] = str_replace(',', '.', $pricefp);
			}

			$requireCosting = 1;

			$frais_port = array(
				'fp_montant'    => 0,
				'fp_franco'     => 0,
				'fp_codepostal' => 0,
			);

			$i = 0;
			$total_panier_ht = 0;
			$total_panier_ttc = 0;
			$a_total_tva = array();

			$catalogue['articles'] = array();

			switch ($op) {
				case 'valider_panier':
					$cde_libelle = $commentaire = '';

					// Si enlèvement magasin, pas de calcul ni de chiffrage du port
					if ($_SESSION['catalogue']['numdepot'] === -1) {
						$requireCosting = 0;
					}

					// Si frais de port forcé par le commercial, pas de calcul ni chiffrage du port
					if (!empty($_SESSION['dims']['previous_user']) && isset($_SESSION['catalogue']['panier']['forced_frais_port'])) {
						$requireCosting = 0;
						$frais_port['fp_montant'] = $_SESSION['catalogue']['panier']['forced_frais_port'];
					}

					foreach ($_SESSION['catalogue']['panier']['articles'] as $ref => $detail) {
						$i++;
						$article = new article();
						$article->findByRef($ref);

						$qte = $detail['qte'];

						// remise sur les commandes web
						$remises = array(0, $oCatalogue->getParams('tx_remise_cde_web'));

						// base de calcul (HT / TTC ?)
						// TTC
						if ($oCatalogue->getParams('cata_base_ttc')) {
							if (isset($detail['forced_price'])) {
								$pu_ttc 		= $detail['forced_price'];
							}
							else {
								$pu_ttc         = round(catalogue_getprixarticle($article, $qte) * (1 - $remises[1] / 100), 2);
							}
							$pu_brut        	= round(catalogue_getprixarticle($article, $qte, true), 2);
							$pu_ht          	= round($pu_ttc / (1 + $a_tva[$article->fields['ctva']] / 100), 2);
							$total_ligne      	= $pu_ttc * $qte;
							$total_panier_ttc   += $total_ligne;

							if (!isset($a_total_tva[$article->fields['ctva']])) {
								$a_total_tva[$article->fields['ctva']] = 0;
							}
							$a_total_tva[$article->fields['ctva']] += $total_ligne - ($total_ligne / (1 + $a_tva[$article->fields['ctva']] / 100));
						}
						// HT
						else {
							if (isset($detail['forced_price'])) {
								$pu_ht 			= $detail['forced_price'];
							}
							else {
								$pu_ht          = round(catalogue_getprixarticle($article, $qte) * (1 - $remises[1] / 100), 2);
							}
							$pu_brut        	= round(catalogue_getprixarticle($article, $qte, true), 2);
							$pu_ttc         	= round($pu_ht * (1 + $a_tva[$article->fields['ctva']] / 100), 2);
							$total_ligne       	= $pu_ht * $qte;
							$total_panier_ht    += $total_ligne;

							if (!isset($a_total_tva[$article->fields['ctva']])) {
								$a_total_tva[$article->fields['ctva']] = 0;
							}
							$a_total_tva[$article->fields['ctva']] += $total_ligne * $a_tva[$article->fields['ctva']] / 100;
						}

						// qtes
						if ($oCatalogue->getParams('cata_aff_livrable') && $obj_client->isProfessional()) {
							$qte_liv = ($article->fields['qte'] >= $qte) ? $qte : $article->fields['qte'];
							$qte_rel = ($article->fields['qte'] >= $qte) ? 0 : $qte - $article->fields['qte'];
						}
						else {
							$qte_liv = $qte_rel = 0;
						}

						$catalogue['articles'][] = array(
							'reference'		=> $ref,
							'designation'	=> $article->fields['label'],
							'fam'	        => $article->fields['fam'],
							'dangerousness'	=> $article->fields['dangerousness_code'],
							'weight'	    => $article->fields['poids'],
							'voluminous'	=> $article->fields['volume'],
							'qte_cde'		=> $qte,
							'qte_liv'		=> $qte_liv,
							'qte_rel'		=> $qte_rel,
							'pu_brut'		=> catalogue_formateprix($pu_brut),
							'pu_net_ht'		=> catalogue_formateprix($pu_ht),
							'pu_net_ttc'	=> catalogue_formateprix($pu_ttc),
							'remise'		=> $remises[1],
							'total_ligne'	=> catalogue_formateprix($total_ligne),
							'class'			=> 'ligne'.($i % 2),
							'tot_tva'		=> catalogue_formateprix($pu_ht * $qte * $a_tva[$article->fields['ctva']] / 100),
							'tva'			=> $a_tva[$article->fields['ctva']],
						);
					}
					break;
				case 'valider_commande':
					$id_cmd = $_SESSION['catalogue']['id_cde'];

					$cde = new commande();
					if ($cde->open($id_cmd)) {
						if (!empty($_SESSION['dims']['previous_user']) && isset($_SESSION['catalogue']['panier']['forced_frais_port'])) {
							$cde->set('require_costing', 0);
							$cde->set('port', $_SESSION['catalogue']['panier']['forced_frais_port']);
							$cde->save();
						}

						// On vérifie que les quantités sont disponibles
						if ( $cde_opened && $oCatalogue->getParams('block_if_not_enough_stock') ) {
							// Contenu de la commande pour envoi de mail de notification si stocks insuffisants
							$order_lines = array();

							$lignes = $cde->getlignes();
							foreach ($lignes as $ligne) {
								$detail = $ligne->fields;

								$article = new article();
								$article->findByRef($detail['ref']);

								if (isset($_SESSION['catalogue']['id_company'])) {
									$stock_total = $article->getStockTotal($_SESSION['catalogue']['id_company']);
									$end_of_life = $article->isEndOfLife($_SESSION['catalogue']['id_company']);
								}
								else {
									$stock_total = $article->getStockTotal();
									$end_of_life = $article->isEndOfLife();
								}

								$order_lines[] = array(
									'ref' 			=> $detail['ref'],
									'label' 		=> $article->getLabel(),
									'qte' 			=> $detail['qte'],
									'stock' 		=> $stock_total,
									'end_of_life' 	=> $end_of_life
									);

								if ($detail['qte'] > $stock_total) {
									$_SESSION['catalogue']['errors'][0] = 'La quantité demandée est indisponible, vous pouvez <a href="/index.php?op=reprendre_commande&id_cmd='.$id_cmd.'">modifier vos quantités dans votre panier</a> :';
									$_SESSION['catalogue']['errors'][] = " - ".$article->getLabel().' ('.$stock_total.' disponibles)';
								}
							}

							// Envoi de mail de notification au commercial si stock insuffisant
							if ( isset($_SESSION['catalogue']['errors']) && !empty($_SESSION['catalogue']['code_client']) ) {
								if ($obj_client->fields['representative_id'] > 0) {
									$representative = user::find_by(array('representative_id' => $obj_client->fields['representative_id']), null, 1);
									if ( !is_null($representative) && substr($representative->get('lastname'), 0, 8) != 'CELLULE_' && $representative->get('email') != '' ) {

										// recherche du template
										$lstwcemods = $dims->getWceModules();
										$wce_module_id = (!empty($lstwcemods)) ? current($lstwcemods) : 0;

										$headings = wce_getheadings($wce_module_id);
										$headingid = (isset($headings['tree'][0][0])) ? $headings['tree'][0][0] : 0;

										$template_name = (!empty($headings['list'][$headingid]['template'])) ? $headings['list'][$headingid]['template'] : 'default';
										// FIN - recherche du template

										require_once DIMS_APP_PATH.'/modules/catalogue/include/class_commande.php';
										commande::sendNotEnoughStockMail($representative, $obj_client, $order_lines, $template_name);
									}
								}
							}

						}

						$requireCosting = $cde->get('require_costing');
						$commentaire 	= $cde->fields['commentaire'];
						$_SESSION['catalogue']['delivery_conditions']['libelle'] = $cde->fields['libelle'];
						$_SESSION['catalogue']['delivery_conditions']['hayon_necessaire'] = $cde->fields['hayon_necessaire'];

						$frais_port['fp_montant']       = $cde->fields['port'];
						$frais_port['fp_codepostal']    = $cde->fields['cli_liv_cp'];

						if (!$cde->fields['hors_cata']) {
							foreach ($cde->getlignes() as $ligne) {
								$i++;
								$detail = $ligne->fields;

								$article = new article();
								$article->findByRef($detail['ref']);

								$ref = $detail['ref'];
								$qte = $detail['qte'];

								// remise sur les commandes web
								$remises = array(0, $oCatalogue->getParams('tx_remise_cde_web'));

								// base de calcul (HT / TTC ?)
								// TTC
								if ($oCatalogue->getParams('cata_base_ttc')) {
									if ($detail['forced_price']) {
										$pu_ttc 		= $detail['pu_remise'];
									}
									else {
										$pu_ttc         = round(catalogue_getprixarticle($article, $qte) * (1 - $remises[1] / 100), 2);
									}
									$pu_brut        	= round(catalogue_getprixarticle($article, $qte, true), 2);
									$pu_ht          	= round($pu_ttc / (1 + $a_tva[$article->fields['ctva']] / 100), 2);
									$total_ligne      	= $pu_ttc * $qte;
									$total_panier_ttc   += $total_ligne;

									if (!isset($a_total_tva[$article->fields['ctva']])) {
										$a_total_tva[$article->fields['ctva']] = 0;
									}
									$a_total_tva[$article->fields['ctva']] += $total_ligne - ($total_ligne / (1 + $a_tva[$article->fields['ctva']] / 100));
								}
								// HT
								else {
									if ($detail['forced_price']) {
										$pu_ht 			= $detail['pu_remise'];
									}
									else {
										$pu_ht          = round(catalogue_getprixarticle($article, $qte) * (1 - $remises[1] / 100), 2);
									}
									$pu_brut        	= round(catalogue_getprixarticle($article, $qte, true), 2);
									$pu_ttc         	= round($pu_ht * (1 + $a_tva[$article->fields['ctva']] / 100), 2);
									$total_ligne       	= $pu_ht * $qte;
									$total_panier_ht    += $total_ligne;

									if (!isset($a_total_tva[$article->fields['ctva']])) {
										$a_total_tva[$article->fields['ctva']] = 0;
									}
									$a_total_tva[$article->fields['ctva']] += $total_ligne * $a_tva[$article->fields['ctva']] / 100;
								}

								// qtes
								$qte_liv = $qte_rel = 0;
								if ($oCatalogue->getParams('cata_aff_livrable') && $obj_client->isProfessional()) {
									$qte_liv = ($article->fields['qte'] >= $qte) ? $qte : $article->fields['qte'];
									$qte_rel = ($article->fields['qte'] >= $qte) ? 0 : $qte - $article->fields['qte'];
								}

								$catalogue['articles'][] = array(
									'reference'		=> $ref,
									'designation'	=> $article->fields['label'],
									'fam'	        => $article->fields['fam'],
									'dangerousness'	=> $article->fields['dangerousness_code'],
									'weight'	    => $article->fields['poids'],
									'voluminous'	=> $article->fields['volume'],
									'qte_cde'		=> $qte,
									'qte_liv'		=> $qte_liv,
									'qte_rel'		=> $qte_rel,
									'pu_brut'		=> catalogue_formateprix($pu_brut),
									'pu_net_ht'		=> catalogue_formateprix($pu_ht),
									'pu_net_ttc'	=> catalogue_formateprix($pu_ttc),
									'remise'		=> $remises[1],
									'total_ligne'	=> catalogue_formateprix($total_ligne),
									'class'			=> 'ligne'.($i % 2),
									'tot_tva'		=> catalogue_formateprix($pu_ht * $qte * $a_tva[$article->fields['ctva']] / 100),
									'tva'			=> $a_tva[$article->fields['ctva']],
								);
							}
						}
						else {
							foreach ($cde->getlignes() as $ligne) {
								$detail = $ligne->fields;
								$i++;

								$ref = $detail['reference'];
								$qte = $detail['qte'];

								// remise sur les commandes web
								$remises = array(0, $oCatalogue->getParams('tx_remise_cde_web'));

								$total_panier_ht += $detail['pu'] * $qte;
								$total_panier_ttc += $detail['pu'] * $qte;

								$catalogue['articles'][] = array(
									'reference'		=> $ref,
									'designation'	=> $detail['designation'],
									'fam'	        => $detail['fam'],
									'dangerousness'	=> $detail['dangerousness_code'],
									'weight'	    => $detail['poids'],
									'voluminous'	=> $detail['volume'],
									'qte_cde'		=> $qte,
									'pu_net_ht'		=> $detail['pu'],
									'remise'		=> $remises[1],
									'total'			=> $detail['pu'] * $qte,
									'class'			=> 'ligne'.($i % 2)
								);
							}
						}
					}
					else {
						dims_redirect($dims->getScriptEnv().'?op=panier');
					}
					break;
			}
			$remise = '';
			$rem = 0;

			// calcul de la remise
			if (isset($_SESSION['catalogue']['remgen'])) {
				if ($_SESSION['catalogue']['remgen'] > 0){
					$remise = $_SESSION['catalogue']['remgen']."%";
				}
				$total_panier_ht = $total_panier_ht - ($total_panier_ht * ($_SESSION['catalogue']['remgen']/100));
				$rem += $_SESSION['catalogue']['remgen'];
			}

			if (isset($_SESSION['catalogue']['seuilrem1']) && isset($_SESSION['catalogue']['seuilrem2'])) {
				if ($total_panier_ht > $_SESSION['catalogue']['seuilrem1'] && $total_panier_ht < $_SESSION['catalogue']['seuilrem2']){
					$remise = $_SESSION['catalogue']['pourcrem1']."%";
					$total_panier_ht = $total_panier_ht - ($total_panier_ht * ($_SESSION['catalogue']['pourcrem1']/100));
					$rem += $_SESSION['catalogue']['pourcrem1'];
				}
				if($total_panier_ht > $_SESSION['catalogue']['seuilrem2']){
					$remise = $_SESSION['catalogue']['pourcrem2']."%";
					$total_panier_ht = $total_panier_ht - ($total_panier_ht * ($_SESSION['catalogue']['pourcrem2']/100));
					$rem += $_SESSION['catalogue']['pourcrem2'];
				}
			}

			if ($oCatalogue->getParams('cata_base_ttc')) {
				$total_panier = $total_panier_ttc;
			}
			else {
				$total_panier = $total_panier_ht;
			}

			// Si on ne connait pas le montant des frais de port, on essaie de les calculer
			$autoCosting = false;
			if ($requireCosting) {
				$a_depots = $obj_client->getDepots();
				$frais_port = get_fraisport(73, $a_depots[$_SESSION['catalogue']['numdepot']]->fields['cp'], $total_panier, 1, $catalogue['articles'], $_SESSION['catalogue']['delivery_conditions']['hayon_necessaire']);
				$requireCosting = $frais_port['require_costing'];
				$autoCosting = true;
			}

			if ($oCatalogue->getParams('cata_base_ttc')) {
				$a_total_tva['fp'] = $frais_port['fp_montant'] - ( $frais_port['fp_montant'] / (1 + _DEFAULT_TVA));
			}
			else {
				$a_total_tva['fp'] = $frais_port['fp_montant'] * _DEFAULT_TVA;
			}

			$smarty->assign('frais_port', array(
				'franco'		=> catalogue_formateprix($frais_port['fp_franco']),
				'franco_val'	=> $frais_port['fp_franco'],
				'to_franco'		=> catalogue_formateprix($frais_port['fp_franco'] - $total_panier),
				'to_franco_val'	=> $frais_port['fp_franco'] - $total_panier
			));

			$total_tva = 0;
			foreach ($a_total_tva as $key => $totaltva) {
				if($key != 'fp') {
					$total_tva += round($totaltva * (1 - $rem / 100), 2);
				} else {
					$total_tva += round($totaltva, 2);
				}
			}

			if ($oCatalogue->getParams('cata_base_ttc')) {
				$total_ttc = $total_panier_ttc + $frais_port['fp_montant'];
			}
			else {
				$total_ttc = $total_panier_ht + $frais_port['fp_montant'] + $total_tva;
			}

			$VATs = array();
			foreach($a_total_tva as $VATCode => $VATvalue) {
				if ($VATCode != 'fp') {
					$VAT = current(tva::findByCode($VATCode));

					$VATs[$VATCode] = array(
						'code'  => $VAT->fields['id_tva'],
						'label' => $VAT->fields['description'],
						'rate'  => catalogue_formateprix($VAT->fields['tx_tva']),
						'value' => catalogue_formateprix($VATvalue),
					);
				} else {
					$VATs[$VATCode] = array(
						'code'  => 'FP',
						'label' => dims_constant::getVal('_SHIPPING_FEES'),
						'rate'  => catalogue_formateprix(round(floatval($oCatalogue->getParams('default_tva')), 2)),
						'value' => catalogue_formateprix($VATvalue),
					);
				}
			}

			// adresse de facturation
			$pays_fact = new country();
			$pays_fact->open($obj_client->fields['id_pays']);

			$adr_fact = $obj_client->getName().'<br/>';
			$adr = trim($obj_client->getAddress());
			if ($obj_client->getAddress2() != '') {
				if (!empty($adr)) $adr .= '<br/>';
				$adr .= trim($obj_client->getAddress2());
			}
			if ($obj_client->getAddress3() != '') {
				if (!empty($adr)) $adr .= '<br/>';
				$adr .= trim($obj_client->getAddress3());
			}
			$adr_fact .= $adr.'<br/>'.$obj_client->getPostalCode().' '.$obj_client->getCity().'<br/>';
			$adr_fact .= $pays_fact->get('label');

			// adresse de livraison
			if ($_SESSION['catalogue']['numdepot'] == -1) {
				$adr_liv = '<strong>Enlèvement au magasin</strong>';
				$delivery = false;
			}
			else {
				$a_depots = $obj_client->getDepots();
				$depot = $a_depots[$_SESSION['catalogue']['numdepot']];
				$adr_liv = $depot->fields['nomlivr'].'<br/>';
				$adr = trim($depot->fields['adr1']);
				if (!empty($depot->fields['adr2'])) {
					if (!empty($adr)) $adr .= '<br/>';
					$adr .= trim($depot->fields['adr2']);
				}
				if (!empty($depot->fields['adr3'])) {
					if (!empty($adr)) $adr .= '<br/>';
					$adr .= trim($depot->fields['adr3']);
				}
				$adr_liv .= $adr.'<br/>'.$depot->fields['cp'].' '.$depot->fields['ville'];
				$delivery = true;
			}

			$catalogue['commande'] = array(
				'libelle'		=> stripslashes($cde_libelle),
				'commentaire'	=> stripslashes(str_replace('\r\n', "\r\n", $commentaire)),
				'ss_total_ht'	=> catalogue_formateprix($total_panier_ht),
				'ss_total_ttc'	=> catalogue_formateprix($total_panier_ttc),
				'mt_port_ht'	=> catalogue_formateprix($frais_port['fp_montant']),
				'total_ht'		=> catalogue_formateprix($total_panier_ht + $frais_port['fp_montant']),
				'total_tva'		=> catalogue_formateprix($total_tva),
				'total_ttc'		=> catalogue_formateprix($total_ttc),
				'adr_liv'		=> str_replace("\n", '<br>', $adr_liv),
				'adr_fact'		=> $adr_fact,
				'remise'		=> $remise,
				'username'		=> (!$cde->isNew()) ? $cde->fields['user_name'] : $user->fields['firstname'].' '.$user->fields['lastname'],
				'classroom' 	=> (!$cde->isNew()) ? $cde->fields['classroom'] : $user->fields['comments'],
				'VATs'          => $VATs,
				'delivery'		=> $delivery
			);

			// ------------------ OPTIONS ------------------
			// on rend possible l'enregsitrement de la commande
			// si les commandes en cours sont activées
			$catalogue['bouton_pause'] = $oCatalogue->getParams('wait_commandes');
			// affichage des livrables
			$catalogue['cata_aff_livrable'] = $oCatalogue->getParams('cata_aff_livrable') && $obj_client->isProfessional();
			// remise sur les commandes web
			$catalogue['tx_remise_cde_web'] = ($oCatalogue->getParams('tx_remise_cde_web') > 0);
			// affichage des prix sur une base TTC
			$catalogue['cata_base_ttc'] = $oCatalogue->getParams('cata_base_ttc');
			// modes de paiement
			// on coche le premier bouton radio disponible
			if (sizeof($a_modes_paiement)) {
				// $a_modes_paiement[0]['checked'] = true;
			}
			$catalogue['modes_paiement'] = $a_modes_paiement;

			$catalogue['require_costing'] = $requireCosting;
			$catalogue['auto_costing'] = $autoCosting;
			// $catalogue['require_costing'] = $frais_port['require_costing'];

			$catalogue['supplement_hayon'] = $oCatalogue->getParams('supplement_hayon');

			// nombre de colonnes total
			$colspan = 4;
			if ($oCatalogue->getParams('cata_aff_livrable') && $obj_client->isProfessional()) {
				$colspan += 2;
			}
			if ($oCatalogue->getParams('tx_remise_cde_web') > 0) {
				$colspan += 2;
			}
			$catalogue['colspan'] = $colspan;
			$catalogue['op'] = $op;

			$catalogue['delivery_conditions'] = $_SESSION['catalogue']['delivery_conditions'];

			// Possibilité de modifier les prix ?
			$smarty->assign('editables_prices', !empty($_SESSION['dims']['previous_user']));

			$smarty->assign('client', $obj_client->fields);
			$smarty->assign('tpl_name', 'recapitulatif');
			$smarty->assign('catalogue', $catalogue);
			break;
		case 3.5:
			// Informations spécifiques à la livraison
			$op 							= dims_load_securvalue('op', dims_const::_DIMS_CHAR_INPUT, false, true);
			$semi_remorque_possible 		= dims_load_securvalue('semi_remorque_possible',    dims_const::_DIMS_NUM_INPUT, false, true, true, $_SESSION['catalogue']['delivery_conditions']['semi_remorque_possible'], null, true);
			$hauteur_maxi 					= dims_load_securvalue('hauteur_maxi',              dims_const::_DIMS_CHAR_INPUT, false, true, true, $_SESSION['catalogue']['delivery_conditions']['hauteur_maxi']);
			$hayon_necessaire 				= dims_load_securvalue('hayon_necessaire',          dims_const::_DIMS_NUM_INPUT, false, true, true, $_SESSION['catalogue']['delivery_conditions']['hayon_necessaire'], null, true);
			$tire_pal_necessaire 			= dims_load_securvalue('tire_pal_necessaire',       dims_const::_DIMS_NUM_INPUT, false, true, true, $_SESSION['catalogue']['delivery_conditions']['tire_pal_necessaire'], null, true);
			$camion_autre 					= dims_load_securvalue('camion_autre',              dims_const::_DIMS_CHAR_INPUT, false, true, true, $_SESSION['catalogue']['delivery_conditions']['camion_autre']);
			$impossibilites_lirvaison 		= dims_load_securvalue('impossibilites_lirvaison',  dims_const::_DIMS_CHAR_INPUT, false, true, true, $_SESSION['catalogue']['delivery_conditions']['impossibilites_lirvaison']);
			$contact_nom 					= dims_load_securvalue('contact_nom',               dims_const::_DIMS_CHAR_INPUT, false, true, true, $_SESSION['catalogue']['delivery_conditions']['contact_nom']);
			$contact_prenom 				= dims_load_securvalue('contact_prenom',            dims_const::_DIMS_CHAR_INPUT, false, true, true, $_SESSION['catalogue']['delivery_conditions']['contact_prenom']);
			$contact_tel 					= dims_load_securvalue('contact_tel',               dims_const::_DIMS_CHAR_INPUT, false, true, true, $_SESSION['catalogue']['delivery_conditions']['contact_tel']);
			$contact_autre 					= dims_load_securvalue('contact_autre',             dims_const::_DIMS_CHAR_INPUT, false, true, true, $_SESSION['catalogue']['delivery_conditions']['contact_autre']);

			dims_redirect('/index.php?op='.$op.'&etape=3');
			break;
		case 4.1:
		case 4.2:
			$askCosting = dims_load_securvalue('ask_costing', dims_const::_DIMS_NUM_INPUT, true, true);
			$frais_port = array(
				'fp_montant'    => 0,
				'fp_franco'     => 0,
				'fp_codepostal' => 0,
			);

			$obj_cde = new commande();
			$obj_cde->init_description();
			if (!empty($_SESSION['catalogue']['id_cde'])) {
				$obj_cde->open($_SESSION['catalogue']['id_cde']);
			}

			$orderWasNew = $obj_cde->isNew();

			$frais_port['fp_montant']       = $obj_cde->fields['port'];
			$frais_port['fp_codepostal']    = $obj_cde->fields['cli_liv_cp'];

			if ($etape == 4.1) {
				// on vérifie le mode de paiement
				$mp_ok = false;
				$mode_paiement = dims_load_securvalue('mode_paiement', dims_const::_DIMS_CHAR_INPUT, false, true);
				foreach ($a_modes_paiement as $mp) {
					if ($mp['value'] == $mode_paiement && !$mp['disabled']) {
						$mp_ok = true;
						break;
					}
				}
				if (!$mp_ok) {
					dims_redirect($_SERVER['HTTP_REFERER']);
				}

				$obj_cde->fields['teachername'] 	= dims_load_securvalue('teachername', dims_const::_DIMS_CHAR_INPUT, true, true, true);
				$obj_cde->fields['classroom'] 		= dims_load_securvalue('classroom', dims_const::_DIMS_CHAR_INPUT, true, true, true);
			}
			$waiting_panier = dims_load_securvalue('waiting_panier',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($etape != 4.2 || $askCosting) {
				// On vérifie que les quantités sont disponibles
				if ($oCatalogue->getParams('block_if_not_enough_stock')) {
					switch ($op) {
						case 'valider_panier':
							if(!$waiting_panier){
								foreach ($_SESSION['catalogue']['panier']['articles'] as $ref => $detail) {
									$article = new article();
									$article->findByRef($ref);

									if (isset($_SESSION['catalogue']['id_company'])) {
										$stock_total = $article->getStockTotal($_SESSION['catalogue']['id_company']);
									}
									else {
										$stock_total = $article->getStockTotal();
									}

									if ($detail['qte'] > $stock_total) {
										$_SESSION['catalogue']['errors'][0] = 'La quantité demandée est indisponible, vous pouvez <a href="/index.php?op=panier">modifier vos quantités dans votre panier</a> :';
										$_SESSION['catalogue']['errors'][] = " - ".$article->getLabel().' ('.$stock_total.' disponibles)';
									}
								}
							}
							break;
						case 'valider_commande':
							$lignes = $obj_cde->getlignes();
							foreach ($lignes as $ligne) {
								$detail = $ligne->fields;

								$article = new article();
								$article->findByRef($detail['ref']);

								if (isset($_SESSION['catalogue']['id_company'])) {
									$stock_total = $article->getStockTotal($_SESSION['catalogue']['id_company']);
								}
								else {
									$stock_total = $article->getStockTotal();
								}

								if ($detail['qte'] > $stock_total) {
									$_SESSION['catalogue']['errors'][0] = 'La quantité demandée est indisponible, vous pouvez <a href="/index.php?op=panier">modifier vos quantités dans votre panier</a> :';
									$_SESSION['catalogue']['errors'][] = " - ".$article->getLabel().' ('.$stock_total.' disponibles)';
								}
							}
							break;
					}
					if(!empty($_SESSION['catalogue']['errors'])){
						dims_redirect($_SERVER['HTTP_REFERER']);
					}
				}
			}

			$commentaire = dims_load_securvalue('commentaire', dims_const::_DIMS_CHAR_INPUT, false, true, true, $_SESSION['catalogue']['delivery_conditions']['commentaire']);
			$cde_libelle = dims_load_securvalue('cde_libelle', dims_const::_DIMS_CHAR_INPUT, false, true, true, $_SESSION['catalogue']['delivery_conditions']['libelle']);

			if (isset($_SESSION['catalogue']['numdepot']) && $_SESSION['catalogue']['numdepot'] != -1) {
				$a_depots = $obj_client->getDepots();
				$depot = $a_depots[$_SESSION['catalogue']['numdepot']];
			}

			// recherche du groupe de l'utilisateur
			$user = new user();
			$user->open($_SESSION['dims']['userid']);
			$groups = $user->getgroups(false);

			if ($op == 'valider_panier') {
				$obj_cde->fields['id_client']			= $obj_client->fields['id_client'];
				$obj_cde->fields['code_client']			= $obj_client->fields['code_client'];
				$obj_cde->fields['date_cree']			= dims_createtimestamp();
				$obj_cde->fields['user_name']			= $_SESSION['dims']['user']['firstname'].' '.$_SESSION['dims']['user']['lastname'];
				//$obj_cde->fields['cli_pays']			= get_pays($obj_client->fields['id_pays']);
				$obj_cde->fields['cli_tel1']			= $obj_client->fields['tel1'];
				$obj_cde->fields['cli_tel2']			= $obj_client->fields['tel2'];
				$obj_cde->fields['cli_fax']				= $obj_client->fields['fax'];
				$obj_cde->fields['cli_port']			= $obj_client->fields['port'];
				$obj_cde->fields['cli_email']			= $_SESSION['dims']['user']['email'];
			}

			// on réenregistre l'adresse de livraison si pas renseigné à la 1e validation
			if (isset($_SESSION['catalogue']['numdepot'])) {
				if ($_SESSION['catalogue']['numdepot'] == -1) {
					$obj_cde->fields['erp_id_adr_liv']			= 0;
					$obj_cde->fields['cli_liv_nom']				= 'Enlèvement magasin';
					$obj_cde->fields['cli_liv_adr1']			= '';
					$obj_cde->fields['cli_liv_adr2']			= '';
					$obj_cde->fields['cli_liv_adr3']			= '';
					$obj_cde->fields['cli_liv_cp']				= '-1';
					$obj_cde->fields['cli_liv_ville']			= '__NOPORT__';
					$obj_cde->fields['cli_liv_id_pays']			= $obj_client->fields['liv_id_pays'];
					$obj_cde->fields['shipping_conditions_id']	= null;
					$obj_cde->fields['date_enlevement'] 		= $_SESSION['catalogue']['date_enlevement'];
					$obj_cde->fields['heure_enlevement'] 		= $_SESSION['catalogue']['heure_enlevement'];
					$delivery = false;
				}
				else {
					$obj_cde->fields['num_depot']				= $depot->fields['depot'];
					$obj_cde->fields['erp_id_adr_liv']			= $depot->fields['erp_id'];
					$obj_cde->fields['cli_liv_nom']				= $depot->fields['nomlivr'];
					$obj_cde->fields['cli_liv_adr1']			= $depot->fields['adr1'];
					$obj_cde->fields['cli_liv_adr2']			= $depot->fields['adr2'];
					$obj_cde->fields['cli_liv_adr3']			= $depot->fields['adr3'];
					$obj_cde->fields['cli_liv_cp']				= $depot->fields['cp'];
					$obj_cde->fields['cli_liv_ville']			= $depot->fields['ville'];
					$obj_cde->fields['cli_liv_id_pays']			= $obj_client->fields['liv_id_pays'];
					$obj_cde->fields['shipping_conditions_id']	= $obj_client->fields['shipping_conditions_id'];
					$obj_cde->fields['date_livraison'] 			= $_SESSION['catalogue']['date_livraison'];
					$delivery = true;
				}
			}

			// Informations spécifiques à la livraison
			$semi_remorque_possible 		= dims_load_securvalue('semi_remorque_possible',    dims_const::_DIMS_NUM_INPUT, false, true, true, $_SESSION['catalogue']['delivery_conditions']['semi_remorque_possible'], null, true);
			$hauteur_maxi 					= dims_load_securvalue('hauteur_maxi',              dims_const::_DIMS_CHAR_INPUT, false, true, true, $_SESSION['catalogue']['delivery_conditions']['hauteur_maxi']);
			$hayon_necessaire 				= dims_load_securvalue('hayon_necessaire',          dims_const::_DIMS_NUM_INPUT, false, true, true, $_SESSION['catalogue']['delivery_conditions']['hayon_necessaire'], null, true);
			$tire_pal_necessaire 			= dims_load_securvalue('tire_pal_necessaire',       dims_const::_DIMS_NUM_INPUT, false, true, true, $_SESSION['catalogue']['delivery_conditions']['tire_pal_necessaire'], null, true);
			$camion_autre 					= dims_load_securvalue('camion_autre',              dims_const::_DIMS_CHAR_INPUT, false, true, true, $_SESSION['catalogue']['delivery_conditions']['camion_autre']);
			$impossibilites_lirvaison 		= dims_load_securvalue('impossibilites_lirvaison',  dims_const::_DIMS_CHAR_INPUT, false, true, true, $_SESSION['catalogue']['delivery_conditions']['impossibilites_lirvaison']);
			$contact_nom 					= dims_load_securvalue('contact_nom',               dims_const::_DIMS_CHAR_INPUT, false, true, true, $_SESSION['catalogue']['delivery_conditions']['contact_nom']);
			$contact_prenom 				= dims_load_securvalue('contact_prenom',            dims_const::_DIMS_CHAR_INPUT, false, true, true, $_SESSION['catalogue']['delivery_conditions']['contact_prenom']);
			$contact_tel 					= dims_load_securvalue('contact_tel',               dims_const::_DIMS_CHAR_INPUT, false, true, true, $_SESSION['catalogue']['delivery_conditions']['contact_tel']);
			$contact_autre 					= dims_load_securvalue('contact_autre',             dims_const::_DIMS_CHAR_INPUT, false, true, true, $_SESSION['catalogue']['delivery_conditions']['contact_autre']);

			if ($semi_remorque_possible != '') 		$obj_cde->fields['semi_remorque_possible'] 		= $semi_remorque_possible;
			if ($hauteur_maxi != '') 				$obj_cde->fields['hauteur_maxi'] 				= $hauteur_maxi;
			if ($hayon_necessaire != '') 			$obj_cde->fields['hayon_necessaire'] 			= $hayon_necessaire;
			if ($tire_pal_necessaire != '') 		$obj_cde->fields['tire_pal_necessaire'] 		= $tire_pal_necessaire;
			if ($camion_autre != '') 				$obj_cde->fields['camion_autre'] 				= $camion_autre;
			if ($impossibilites_lirvaison != '') 	$obj_cde->fields['impossibilites_lirvaison'] 	= $impossibilites_lirvaison;
			if ($contact_nom != '') 				$obj_cde->fields['contact_nom'] 				= $contact_nom;
			if ($contact_prenom != '') 				$obj_cde->fields['contact_prenom'] 				= $contact_prenom;
			if ($contact_tel != '') 				$obj_cde->fields['contact_tel'] 				= $contact_tel;
			if ($contact_autre != '') 				$obj_cde->fields['contact_autre'] 				= $contact_autre;

			$obj_cde->fields['erp_id_adr_fac']			= $obj_client->fields['erp_id_adr'];
			$obj_cde->fields['cli_nom']					= $obj_client->fields['nom'];
			$obj_cde->fields['cli_adr1']				= $obj_client->fields['adr1'];
			$obj_cde->fields['cli_adr2']				= $obj_client->fields['adr2'];
			$obj_cde->fields['cli_adr3']				= $obj_client->fields['adr3'];
			$obj_cde->fields['cli_cp']					= $obj_client->fields['cp'];
			$obj_cde->fields['cli_ville']				= $obj_client->fields['ville'];
			$obj_cde->fields['cli_id_pays']				= $obj_client->fields['id_pays'];
			$obj_cde->fields['date_validation']			= dims_createtimestamp();
			$obj_cde->fields['libelle']					= str_replace(';', '', $cde_libelle);
			// $obj_cde->fields['liv_num_depot']	= $_SESSION['catalogue']['numdepot'];
			$obj_cde->fields['commentaire']				= str_replace(';', '', $commentaire);
			$obj_cde->fields['mode_paiement_id']		= $obj_client->fields['mode_paiement_id'];
			$obj_cde->fields['payment_conditions_id']	= $obj_client->fields['payment_conditions_id'];
			$obj_cde->fields['id_service']				= array_shift($groups);

			if ($etape == 4.1) {
				$obj_cde->fields['mode_paiement']		= $mode_paiement;
			}

			// enregistrement du commercial qui a passé commande le cas échéant
			if (isset($_SESSION['catalogue']['vrp']['id_commercial'])) {
				if ($obj_cde->fields['commentaire'] != '') $obj_cde->fields['commentaire'] .= "\n";
				$obj_cde->fields['commentaire'] .= 'Commande passée par '.$_SESSION['catalogue']['vrp']['nom'];
			}

			$obj_cde->save();

			$_SESSION['catalogue']['id_cde'] = $obj_cde->fields['id_cde'];

			$poids_total = 0;
			$total_panier_ht = 0;
			$total_panier_ttc = 0;

			$requireCosting = 1;

			// List needed by get_fraisport().
			$articleList = array();

			switch ($op) {
				case 'valider_panier':
					// Si enlèvement magasin, pas de calcul ni de chiffrage du port
					if ($_SESSION['catalogue']['numdepot'] === -1) {
						$requireCosting = 0;
					}

					// Si frais de port forcé par le commercial, pas de calcul ni chiffrage du port
					if (isset($_SESSION['catalogue']['panier']['forced_frais_port'])) {
						$requireCosting = 0;
						$frais_port['fp_montant'] = $_SESSION['catalogue']['panier']['forced_frais_port'];
						$obj_cde->fields['forced_port'] = 1;
						$obj_cde->fields['require_costing'] = $requireCosting;
					}

					// Lignes de commande
					$rs = $db->query('DELETE FROM dims_mod_cata_cde_lignes WHERE id_cde = '.$obj_cde->fields['id_cde']);

					foreach ($_SESSION['catalogue']['panier']['articles'] as $ref => $detail) {
						$article = new article();
						$article->findByRef($ref);

						$qte = $detail['qte'];

						// remise sur les commandes web
						$remises = array(0, $oCatalogue->getParams('tx_remise_cde_web'));

						// base de calcul (HT / TTC ?)
						// TTC
						if ($oCatalogue->getParams('cata_base_ttc')) {
							if (isset($detail['forced_price'])) {
								$pu_remise		= $detail['forced_price'];
								$pu_remise_erp	= $detail['forced_price'] - $article->get('taxe_certiphyto');
							}
							else {
								$pu_remise		= round(catalogue_getprixarticle($article, $qte) * (1 - $remises[1] / 100), 2);
								$pu_remise_erp	= round(catalogue_getprixarticle($article, $qte, 0, -1, false) * (1 - $remises[1] / 100), 2);
							}
							$pu_ttc				= round(catalogue_getprixarticle($article, $qte, true), 2);
							$pu_ht				= round($pu_ttc / (1 + $a_tva[$article->fields['ctva']] / 100), 2);
							$remise				= round(100 - ($pu_remise / $pu_ttc * 100), 2);
							$total_ttc			= $pu_remise * $qte;
							$total_panier_ttc	+= $total_ttc;

							if (!isset($a_total_tva[$article->fields['ctva']])) {
								$a_total_tva[$article->fields['ctva']] = 0;
							}
							$a_total_tva[$article->fields['ctva']] += $total_ttc - ($total_ttc / (1 + $a_tva[$article->fields['ctva']] / 100));
						}
						// HT
						else {
							if (isset($detail['forced_price'])) {
								$pu_remise 		= $detail['forced_price'];
								$pu_remise_erp	= $detail['forced_price'] - $article->get('taxe_certiphyto');
							}
							else {
								$pu_remise		= round(catalogue_getprixarticle($article, $qte) * (1 - $remises[1] / 100), 2);
								$pu_remise_erp	= round(catalogue_getprixarticle($article, $qte, 0, -1, false) * (1 - $remises[1] / 100), 2);
							}
							$pu_ht				= round(catalogue_getprixarticle($article, $qte, true), 2);
							$pu_ttc				= round($pu_ht * (1 + $a_tva[$article->fields['ctva']] / 100), 2);
							$remise				= round(100 - ($pu_remise / $pu_ht * 100), 2);
							$total_ht			= $pu_remise * $qte;
							$total_panier_ht	+= $total_ht;

							if (!isset($a_total_tva[$article->fields['ctva']])) {
								$a_total_tva[$article->fields['ctva']] = 0;
							}
							$a_total_tva[$article->fields['ctva']] += $total_ht * $a_tva[$article->fields['ctva']] / 100;
						}


						// qtes
						if ($oCatalogue->getParams('cata_aff_livrable') && $obj_client->isProfessional()) {
							$qte_liv = ($article->fields['qte'] >= $qte) ? $qte : $article->fields['qte'];
							$qte_rel = ($article->fields['qte'] >= $qte) ? 0 : $qte - $article->fields['qte'];
						}

						// Struct for get_fraisport().
						$articleList[] = array(
							'fam'	        => $article->fields['fam'],
							'dangerousness'	=> $article->fields['dangerousness_code'],
							'weight'	    => $article->fields['poids'],
							'voluminous'	=> $article->fields['volume'],
							'qte_cde'		=> $qte,
						);

						//Enregistrement de la ligne de commande
						$obj_cde_li = new commande_ligne();
						$obj_cde_li->fields['id_cde']			= $obj_cde->fields['id_cde'];
						$obj_cde_li->fields['id_article']		= $article->fields['id'];
						$obj_cde_li->fields['ref']				= $article->fields['reference'];
						$obj_cde_li->fields['label']			= $article->fields['label'];
						$obj_cde_li->fields['label_default']	= $article->fields['label'];
						$obj_cde_li->fields['qte']				= $qte;
						$obj_cde_li->fields['poids'] 			= $article->fields['poids'];
						$obj_cde_li->fields['pu_ht']			= $pu_ht;
						$obj_cde_li->fields['pu_remise']		= $pu_remise;
						$obj_cde_li->fields['pu_remise_erp']	= $pu_remise_erp;
						$obj_cde_li->fields['remise']			= $remise.'%';
						$obj_cde_li->fields['pu_ttc']			= $pu_ttc;
						$obj_cde_li->fields['tx_tva']			= (isset($a_tva[$article->fields['ctva']])) ? $a_tva[$article->fields['ctva']] : 0;
						$obj_cde_li->fields['ctva']				= $article->fields['ctva'];
						$obj_cde_li->fields['forced_price'] 	= (isset($detail['forced_price'])) ? 1 : 0;
						$obj_cde_li->save();
					}
					break;
				case 'valider_commande':
					if ($obj_cde->fields['forced_port']) {
						$requireCosting = 0;
						$frais_port['fp_montant'] = $obj_cde->fields['port'];
					}

					$lignes = $obj_cde->getlignes();

					// Lignes de commande
					if (!$obj_cde->fields['hors_cata']) {
						$rs = $db->query('DELETE FROM dims_mod_cata_cde_lignes WHERE id_cde = '.$obj_cde->fields['id_cde']);

						foreach ($lignes as $ligne) {
							$detail = $ligne->fields;

							$article = new article();
							$article->findByRef($detail['ref']);

							$ref = $detail['ref'];
							$qte = $detail['qte'];

							// remise sur les commandes web
							$remises = array(0, $oCatalogue->getParams('tx_remise_cde_web'));

							// base de calcul (HT / TTC ?)
							// TTC
							if ($oCatalogue->getParams('cata_base_ttc')) {
								if ($detail['forced_price']) {
									$pu_remise 		= $detail['pu_remise'];
									$pu_remise_erp 	= $detail['pu_remise'] - $article->get('taxe_certiphyto');
								}
								else {
									$pu_remise		= round(catalogue_getprixarticle($article, $qte) * (1 - $remises[1] / 100), 2);
									$pu_remise_erp	= round(catalogue_getprixarticle($article, $qte, 0, -1, false) * (1 - $remises[1] / 100), 2);
								}
								$pu_ttc				= round(catalogue_getprixarticle($article, $qte, true), 2);
								$pu_ht				= round($pu_ttc / (1 + $a_tva[$article->fields['ctva']] / 100), 2);
								$remise				= round(100 - ($pu_remise / $pu_ttc * 100), 2);
								$total_ttc			= $pu_remise * $qte;
								$total_panier_ttc	+= $total_ttc;

								if (!isset($a_total_tva[$article->fields['ctva']])) {
									$a_total_tva[$article->fields['ctva']] = 0;
								}
								$a_total_tva[$article->fields['ctva']] += $total_ttc - ($total_ttc / (1 + $a_tva[$article->fields['ctva']] / 100));
							}
							// HT
							else {
								if ($detail['forced_price']) {
									$pu_remise 		= $detail['pu_remise'];
									$pu_remise_erp 	= $detail['pu_remise'] - $article->get('taxe_certiphyto');
								}
								else {
									$pu_remise		= round(catalogue_getprixarticle($article, $qte) * (1 - $remises[1] / 100), 2);
									$pu_remise_erp	= round(catalogue_getprixarticle($article, $qte, 0, -1, false) * (1 - $remises[1] / 100), 2);
								}
								$pu_ht				= round(catalogue_getprixarticle($article, $qte, true), 2);
								$pu_ttc				= round($pu_ht * (1 + $a_tva[$article->fields['ctva']] / 100), 2);
								$remise				= round(100 - ($pu_remise / $pu_ht * 100), 2);
								$total_ht			= $pu_remise * $qte;
								$total_panier_ht	+= $total_ht;

								if (!isset($a_total_tva[$article->fields['ctva']])) {
									$a_total_tva[$article->fields['ctva']] = 0;
								}
								$a_total_tva[$article->fields['ctva']] += $total_ht * $a_tva[$article->fields['ctva']] / 100;
							}

							// qtes
							if ($oCatalogue->getParams('cata_aff_livrable') && $obj_client->isProfessional()) {
								$qte_liv = ($article->fields['qte'] >= $qte) ? $qte : $article->fields['qte'];
								$qte_rel = ($article->fields['qte'] >= $qte) ? 0 : $qte - $article->fields['qte'];
							}

							// Struct for get_fraisport().
							$articleList[] = array(
								'fam'	        => $article->fields['fam'],
								'dangerousness'	=> $article->fields['dangerousness_code'],
								'weight'	    => $article->fields['poids'],
								'voluminous'	=> $article->fields['volume'],
								'qte_cde'		=> $qte,
							);

							//Enregistrement de la ligne de commande
							$obj_cde_li = new commande_ligne();
							$obj_cde_li->fields['id_cde']			= $obj_cde->fields['id_cde'];
							$obj_cde_li->fields['id_article']		= $article->fields['id'];
							$obj_cde_li->fields['ref']				= $article->fields['reference'];
							$obj_cde_li->fields['label']			= $article->fields['label'];
							$obj_cde_li->fields['label_default']	= $article->fields['label'];
							$obj_cde_li->fields['qte']				= $qte;
							$obj_cde_li->fields['qpoids']			= $article->fields['poids'];
							$obj_cde_li->fields['pu_ht']			= $pu_ht;
							$obj_cde_li->fields['pu_remise']		= $pu_remise;
							$obj_cde_li->fields['pu_remise_erp']	= $pu_remise_erp;
							$obj_cde_li->fields['remise']			= $remise.'%';
							$obj_cde_li->fields['pu_ttc']			= $pu_ttc;
							$obj_cde_li->fields['tx_tva']			= (isset($a_tva[$article->fields['ctva']])) ? $a_tva[$article->fields['ctva']] : 0;
							$obj_cde_li->fields['ctva']				= $article->fields['ctva'];
							$obj_cde_li->fields['forced_price'] 	= $detail['forced_price'];
							$obj_cde_li->save();
						}
					}
					else {
						$rs = $db->query('DELETE FROM dims_mod_cata_cde_lignes_hc WHERE id_cde = '.$obj_cde->fields['id_cde']);

						foreach ($lignes as $ligne) {
							$detail = $ligne->fields;
							$ref = $detail['reference'];
							$qte = $detail['qte'];

							// remise sur les commandes web
							$remises = array(0, $oCatalogue->getParams('tx_remise_cde_web'));

							$total_panier_ht += $detail['pu'] * $qte;
							$total_panier_ttc += $detail['pu'] * $qte;

							// Struct for get_fraisport().
							$articleList[] = array(
								'fam'	        => $detail->fields['fam'],
								'dangerousness'	=> $detail->fields['dangerousness_code'],
								'weight'	    => $detail->fields['poids'],
								'voluminous'	=> $detail->fields['volume'],
								'qte_cde'		=> $qte,
							);

							//Enregistrement de la ligne de commande
							$obj_cde_li = new commande_ligne_hc();
							$obj_cde_li->fields['id_cde']		= $obj_cde->fields['id_cde'];
							$obj_cde_li->fields['reference']	= $ref;
							$obj_cde_li->fields['designation']	= $detail['designation'];
							$obj_cde_li->fields['qte']			= $qte;
							$obj_cde_li->fields['pu']			= $detail['pu'];
							$obj_cde_li->save();
						}
					}
					break;
			}
			// calcul de la remise
			$rem = 0;

			if (isset($_SESSION['catalogue']['remgen'])) {
				if ($_SESSION['catalogue']['remgen'] > 0 && $_SESSION['catalogue']['remgen'] != "") {
					$obj_cde_li = new commande_ligne();
					$obj_cde_li->fields['id_cde'] = $obj_cde->fields['id_cde'];
					$obj_cde_li->fields['id_article'] = 0;
					$obj_cde_li->fields['ref'] = $_SESSION['catalogue']['rem0'];
					$obj_cde_li->fields['label'] = $_SESSION['catalogue']['rem0'];
					$obj_cde_li->fields['label_default'] = $_SESSION['catalogue']['rem0'];
					$obj_cde_li->fields['qte'] = -1;
					$obj_cde_li->fields['pu_ht'] = $total_panier_ht;
					$obj_cde_li->fields['remise'] = 100 - $_SESSION['catalogue']['remgen'];
					$obj_cde_li->fields['pu_remise'] = ($total_panier_ht - ($total_panier_ht * ($obj_cde_li->fields['remise'] / 100)));
					$obj_cde_li->fields['pu_remise_erp'] = ($total_panier_ht - ($total_panier_ht * ($obj_cde_li->fields['remise'] / 100)));
					$obj_cde_li->fields['ctva'] = 1;
					if($obj_cde_li->fields['remise'] != 100)
					$obj_cde_li->save();
					$rem += $_SESSION['catalogue']['remgen'];
				}

				$total_panier_ht = $total_panier_ht - ($total_panier_ht * ($_SESSION['catalogue']['remgen'] / 100));
			}

			if (isset($_SESSION['catalogue']['seuilrem1']) && $_SESSION['catalogue']['seuilrem2']) {
				if($total_panier_ht > $_SESSION['catalogue']['seuilrem1'] && $total_panier_ht < $_SESSION['catalogue']['seuilrem2']){
					$obj_cde_li = new commande_ligne();
					$obj_cde_li->fields['id_cde'] = $obj_cde->fields['id_cde'];
					$obj_cde_li->fields['id_article'] = 0;
					$obj_cde_li->fields['ref'] = $_SESSION['catalogue']['rem1'];
					$obj_cde_li->fields['label'] = $_SESSION['catalogue']['rem1'];
					$obj_cde_li->fields['label_default'] = $_SESSION['catalogue']['rem1'];
					$obj_cde_li->fields['qte'] = -1;
					$obj_cde_li->fields['pu_ht'] = $total_panier_ht;
					$obj_cde_li->fields['remise'] = 100 - $_SESSION['catalogue']['pourcrem1'];
					$obj_cde_li->fields['pu_remise'] = ($total_panier_ht - ($total_panier_ht * ($obj_cde_li->fields['remise']/100)));
					$obj_cde_li->fields['pu_remise_erp'] = ($total_panier_ht - ($total_panier_ht * ($obj_cde_li->fields['remise']/100)));
					$obj_cde_li->fields['ctva'] = 1;
					$total_panier_ht = $total_panier_ht - $obj_cde_li->fields['pu_remise'];
					$obj_cde_li->save();
					$rem += $_SESSION['catalogue']['pourcrem1'];
				}
				if($total_panier_ht > $_SESSION['catalogue']['seuilrem2']){
					$obj_cde_li = new commande_ligne();
					$obj_cde_li->fields['id_cde'] = $obj_cde->fields['id_cde'];
					$obj_cde_li->fields['id_article'] = 0;
					$obj_cde_li->fields['ref'] = $_SESSION['catalogue']['rem2'];
					$obj_cde_li->fields['label'] = $_SESSION['catalogue']['rem2'];
					$obj_cde_li->fields['label_default'] = $_SESSION['catalogue']['rem2'];
					$obj_cde_li->fields['qte'] = -1;
					$obj_cde_li->fields['pu_ht'] = $total_panier_ht;
					$obj_cde_li->fields['remise'] = 100 - $_SESSION['catalogue']['pourcrem2'];
					$obj_cde_li->fields['pu_remise'] = ($total_panier_ht - ($total_panier_ht * ($obj_cde_li->fields['remise']/100)));
					$obj_cde_li->fields['pu_remise_erp'] = ($total_panier_ht - ($total_panier_ht * ($obj_cde_li->fields['remise']/100)));
					$obj_cde_li->fields['ctva'] = 1;
					$total_panier_ht = $total_panier_ht - $obj_cde_li->fields['pu_remise'];
					$obj_cde_li->save();
					$rem += $_SESSION['catalogue']['pourcrem2'];
				}
			}


			if ($oCatalogue->getParams('cata_base_ttc')) {
				$total_panier = $total_panier_ttc;
			}
			else {
				$total_panier = $total_panier_ht;
			}

			// Si on ne connait pas le montant des frais de port, on essaie de les calculer
			if ($requireCosting) {
				$a_depots = $obj_client->getDepots();
				$frais_port = get_fraisport(73, $a_depots[$_SESSION['catalogue']['numdepot']]->fields['cp'], $total_panier, 1, $articleList, $obj_cde->fields['hayon_necessaire']);
				$requireCosting = $frais_port['require_costing'];
				$obj_cde->fields['require_costing'] = $requireCosting;
			}

			// Vérification des infos de livraison
			if(!$waiting_panier && $delivery && $requireCosting){
				if (!isset($_POST['semi_remorque_possible'])) {
					$_SESSION['catalogue']['errors'][] = 'Vous n\'avez pas indiqué si un semi-remorque est possible.';
					dims_redirect('/index.php?op=valider_panier&etape=3');
				}

				if (!isset($_POST['hayon_necessaire'])) {
					$_SESSION['catalogue']['errors'][] = 'Vous n\'avez pas indiqué si un hayon est nécessaire.';
					dims_redirect('/index.php?op=valider_panier&etape=3');
				}

				if (!isset($_POST['tire_pal_necessaire'])) {
					$_SESSION['catalogue']['errors'][] = 'Vous n\'avez pas indiqué si un tire-palette est nécessaire.';
					dims_redirect('/index.php?op=valider_panier&etape=3');
				}

				if (empty($obj_cde->fields['contact_nom'])) {
					$_SESSION['catalogue']['errors'][] = 'Vous n\'avez pas renseigné le nom de la personne à contacter.';
					dims_redirect('/index.php?op=valider_panier&etape=3');
				}

				if (empty($obj_cde->fields['contact_prenom'])) {
					$_SESSION['catalogue']['errors'][] = 'Vous n\'avez pas renseigné le prénom de la personne à contacter.';
					dims_redirect('/index.php?op=valider_panier&etape=3');
				}

				if (empty($obj_cde->fields['contact_tel'])) {
					$_SESSION['catalogue']['errors'][] = 'Vous n\'avez pas renseigné le téléphone de la personne à contacter.';
					dims_redirect('/index.php?op=valider_panier&etape=3');
				}
			}

			unset($_SESSION['catalogue']['delivery_conditions']);


			if ($oCatalogue->getParams('cata_base_ttc')) {
				$a_total_tva['fp'] = $frais_port['fp_montant'] - ( $frais_port['fp_montant'] / (1 + _DEFAULT_TVA));
			}
			else {
				$a_total_tva['fp'] = $frais_port['fp_montant'] * _DEFAULT_TVA;
			}

			// calcul des totaux
			$total_tva = 0;
			foreach ($a_total_tva as $key => $totaltva) {
				if($key != 'fp')
					$total_tva += $totaltva * (1 - $rem / 100);
				else
					$total_tva += $totaltva;
			}
			$total_tva = round($total_tva, 2);

			if ($oCatalogue->getParams('cata_base_ttc')) {
				$total_panier_ttc += $frais_port['fp_montant'];
				$total_panier_ht = $total_panier_ttc - $total_tva;
			}
			else {
				$total_panier_ttc = $total_panier_ht + $frais_port['fp_montant'] + $total_tva;
			}

			$obj_cde->fields['poids_total'] = $poids_total;

			$obj_cde->fields['total_ht']	= $total_panier_ht;
			$obj_cde->fields['port']		= $frais_port['fp_montant'];
			$obj_cde->fields['total_tva']	= $total_tva;
			$obj_cde->fields['port_tx_tva']	= _DEFAULT_TVA * 100;
			$obj_cde->fields['total_ttc']	= $total_panier_ttc;

			if ($etape == 4.1) {
				// Si on est responsable des achats
				// ou enseignant sans validation
				if ( $_SESSION['session_adminlevel'] >= cata_const::_DIMS_ID_LEVEL_PURCHASERESP || $_SESSION['session_adminlevel'] >= cata_const::_DIMS_ID_LEVEL_USERSUP ) {
					// on vérifie que l'adresse de facturation n'est pas une mairie avec validation
					if (
						isset($a_adrFact[$_SESSION['catalogue']['adrfact']]['valid_oblig'])
						&& $a_adrFact[$_SESSION['catalogue']['adrfact']]['valid_oblig']
						&& $obj_cde->fields['code_client'] == $_SESSION['catalogue']['code_client']
					) {
						$obj_cde->fields['etat'] = commande::_STATUS_AWAITING_VALIDATION3;
						$obj_cde->save();

						// message de confirmation
						$_SESSION['catalogue']['msg_confirm'] = dims_constant::getVal('_CATA_MSG_CONFIRM_1');

						// Envoi d'un mail à la mairie
						$rs = $db->query('
							SELECT	u.email
							FROM	dims_mod_cata_client c
							INNER JOIN	dims_user u
							ON			u.id = c.dims_user
							WHERE	c.code_client = \''.substr($obj_cde->fields['adrfact'], 1).'\'
							LIMIT 0, 1');
						if ($db->numrows($rs)) {
							$row = $db->fetchrow($rs);
							$to = $row['email'];

							// envoi du mail
							include DIMS_APP_PATH.'modules/catalogue/mail_validation.php';
						}
					}
					else {
						// commande validée
						if ( $mode_paiement == moyen_paiement::_TYPE_DIFFERE || $mode_paiement == moyen_paiement::_TYPE_CHEQUE || $mode_paiement == moyen_paiement::_TYPE_VIREMENT) {
							$obj_cde->fields['etat'] = commande::_STATUS_VALIDATED;
							$obj_cde->save();

							// message de confirmation
							$_SESSION['catalogue']['msg_confirm'] = dims_constant::getVal('_CATA_MSG_CONFIRM_2');

							// On génère le fichier de commande que si le client est un client et non un prospect
							// On vérifie donc le mode de paiement qui permet de différencier l'un de l'autre
							if (!empty($obj_cde->fields['mode_paiement_id'])) {
								write_cmd_file($_SESSION['catalogue']['id_cde']);
							}

							// envoi des mails
							include DIMS_APP_PATH.'/modules/catalogue/mail_cde.php';
						}
						// en attente du paiement
						else {
							$obj_cde->fields['etat'] = commande::_STATUS_WAIT_PAYMENT;
							$obj_cde->save();

							if ($mode_paiement == moyen_paiement::_TYPE_CB) {
								$_SESSION['catalogue']['id_cde'] = $obj_cde->fields['id_cde'];
								$_SESSION['catalogue']['total_ttc'] = $obj_cde->fields['total_ttc'];
								dims_redirect('/index.php?op=valider_commande&etape=4.3');
							}

							if ($mode_paiement == moyen_paiement::_TYPE_PAYPAL) {
								$_SESSION['catalogue']['id_cde'] = $obj_cde->fields['id_cde'];
								$_SESSION['catalogue']['total_ttc'] = $obj_cde->fields['total_ttc'];
								dims_redirect('/index.php?op=valider_commande&etape=4.4');
							}
						}
					}
				}
				// Si on est responsable de service ou qu'on a pas de responsable de service
				elseif($_SESSION['session_adminlevel'] == cata_const::_DIMS_ID_LEVEL_SERVICERESP || $_SESSION['catalogue']['service_id'] == -1) {
					$obj_cde->fields['etat'] = commande::_STATUS_AWAITING_VALIDATION2;
					$obj_cde->save();

					// message de confirmation
					$_SESSION['catalogue']['msg_confirm'] = dims_constant::getVal('_CATA_MSG_CONFIRM_1');

					$validator = new user();
					$validator->open($_SESSION['catalogue']['achat_id']);
					$to = $validator->fields['email'];

					// envoi du mail
					include DIMS_APP_PATH.'/modules/catalogue/mail_validation.php';
				}
				// Si on est responsable de rien du tout
				elseif($_SESSION['session_adminlevel'] == dims_const::_DIMS_ID_LEVEL_USER) {
					$obj_cde->fields['etat'] = commande::_STATUS_AWAITING_VALIDATION1;
					$obj_cde->save();

					// message de confirmation
					$_SESSION['catalogue']['msg_confirm'] = dims_constant::getVal('_CATA_MSG_CONFIRM_1');

					$validator = new user();
					$validator->open($_SESSION['catalogue']['service_id']);
					$to = $validator->fields['email'];

					// envoi du mail
					include DIMS_APP_PATH.'/modules/catalogue/mail_validation.php';
				}

				// suppression de la sauvegarde du panier
				if ($op == 'valider_panier') {
					if ($oCatalogue->getParams('cart_management') == 'cookie') {
						$_SESSION['catalogue']['panier']['articles'] = array();
						$_SESSION['catalogue']['panier']['montant'] = 0;
						panier2cookie();
					}
					if ($oCatalogue->getParams('cart_management') == 'bdd') {
						include_once DIMS_APP_PATH.'/modules/catalogue/include/class_panier.php';
						$panier = new cata_panier();
						$panier->open($_SESSION['dims']['userid']);
						$panier->delete();
						unset($_SESSION['catalogue']['panier']);
					}
				}

				dims_redirect('/index.php?op=valider_panier&etape=5');
			}
			elseif ($etape == 4.2 && $oCatalogue->getParams('wait_commandes')) {

				if (dims_load_securvalue('ask_costing', dims_const::_DIMS_CHAR_INPUT, true, true) == 1) {
					// seul diif entre retour et demande de chiffrage
					$_SESSION['catalogue']['msg_confirm'] = dims_constant::getVal('_CATA_MSG_CONFIRM_7');
				}

				if ($orderWasNew && !isset($_SESSION['catalogue']['panier']['forced_frais_port'])) {
					$obj_cde->set('require_costing', 1);
				}
				// on remet en cours la commande que si c'est le propriétaire qui le fait
				if ($obj_cde->fields['id_user'] == $_SESSION['dims']['userid']) {
					$obj_cde->fields['etat'] = commande::_STATUS_PROGRESS;
				}

				if ($askCosting) {
					$obj_cde->fields['etat'] = commande::_STATUS_AWAITING_COSTING;

					// Mail service logistique
					$logisticDeptEmail = new cata_param();
					$logisticDeptEmail->getByName('logistic_dept_email', $catalogue_moduleid);
					if (!$logisticDeptEmail->isNew() && $logisticDeptEmail->getValue() != '') {
						$obj_cde->sendRequireCostingEmail($logisticDeptEmail->getValue(), $template_name);
					}
					$logisticDeptEmail = new cata_param();
					$logisticDeptEmail->getByName('logistic_dept_email_copy', $catalogue_moduleid);
					if (!$logisticDeptEmail->isNew() && $logisticDeptEmail->getValue() != '') {
						$obj_cde->sendRequireCostingEmail($logisticDeptEmail->getValue(), $template_name);
					}
					$logisticDeptEmail = new cata_param();
					$logisticDeptEmail->getByName('logistic_dept_email_copy_copy', $catalogue_moduleid);
					if (!$logisticDeptEmail->isNew() && $logisticDeptEmail->getValue() != '') {
						$obj_cde->sendRequireCostingEmail($logisticDeptEmail->getValue(), $template_name);
					}
				}

				$obj_cde->save();

				unset($_SESSION['catalogue']['id_cde']);

				// suppression de la sauvegarde du panier
				if ($op == 'valider_panier') {
					if ($oCatalogue->getParams('cart_management') == 'cookie') {
						$_SESSION['catalogue']['panier']['articles'] = array();
						$_SESSION['catalogue']['panier']['montant'] = 0;
						panier2cookie();
					}
					if ($oCatalogue->getParams('cart_management') == 'bdd') {
						include_once DIMS_APP_PATH.'/modules/catalogue/include/class_panier.php';
						$panier = new cata_panier();
						$panier->open($_SESSION['dims']['userid']);
						$panier->delete();
						unset($_SESSION['catalogue']['panier']);
					}
				}

				dims_redirect('/index.php?op=commandes');
			}
			break;
		// Carte bancaire
		case 4.3:
			// si mode de paiement cb pas activé
			$mp = moyen_paiement::getByType(moyen_paiement::_TYPE_CB, $catalogue_moduleid);
			if ($mp == null) {
				dims_redirect($_SERVER['HTTP_REFERER']);
			}

			// recherche du kit de paiement de la banque
			if ($mp->fields['cb_module'] == '') {
				dims_redirect($_SERVER['HTTP_REFERER']);
			}

			// Si on a bien un montant a régler
			if (isset($_SESSION['catalogue']['total_ttc']) && $_SESSION['catalogue']['total_ttc'] > 0) {
				switch ($mp->fields['cb_module']) {
					case 'kwixo':
						require DIMS_APP_PATH.'/lib/kwixo/lib/kwixo/process/call_request.php';
						break;
					case 'sips':
						require DIMS_APP_PATH.'/modules/catalogue/include/cb/sips/call_request.php';
						break;
				}
			}

			$smarty->assign('tpl_name', 'paiement_cb');
			break;
		// paypal
		case 4.4:
			// si total ttc pas défini
			if (!(isset($_SESSION['catalogue']['total_ttc']) && $_SESSION['catalogue']['total_ttc'] > 0)) {
				dims_redirect($_SERVER['HTTP_REFERER']);
			}

			// si mode de paiement paypal pas activé
			$mp = moyen_paiement::getByType(moyen_paiement::_TYPE_PAYPAL, $catalogue_moduleid);
			if ($mp == null) {
				dims_redirect($_SERVER['HTTP_REFERER']);
			}

			// si l'adresse email du compte du commercant est pas renseignée
			if ($mp->getPaypalEmail() == '') {
				dims_redirect($_SERVER['HTTP_REFERER']);
			}

			// recheche du logo
			$logo_url = $mp->getLogoUrl();
			?>

			<html>
			<head>
			<title>Redirection vers la plateforme de paiement PayPal</title>
			</head>
			<body>

			Vous allez &ecirc;tre redirig&eacute; sur le site de paiement Paypal...<br/>
			Si vous n'&ecirc;tes pas redirig&eacute; dans les 10 secondes, cliquez sur le bouton ci-dessous pour passer au paiement :<br/><br/>

			<form name="f_paiement_paypal" action="<?= $mp->getPaypalUrl(); ?>" method="POST">
				<input type="hidden" name="cmd" value="_ext-enter"/>
				<input type="hidden" name="redirect_cmd" value="_xclick"/>
				<input type="hidden" name="charset" value="utf-8"/>
				<input type="hidden" name="business" value="<?= $mp->getPaypalEmail(); ?>"/>
				<input type="hidden" name="item_name" value="Commande n° <?= $_SESSION['catalogue']['id_cde']; ?>"/>
				<input type="hidden" name="item_number" value="<?= $_SESSION['catalogue']['id_cde']; ?>"/>
				<input type="hidden" name="amount" value="<?= $_SESSION['catalogue']['total_ttc']; ?>"/>
				<input type="hidden" name="currency_code" value="EUR"/>
				<input type="hidden" name="no_note" value="1"/>
				<input type="hidden" name="no_shipping" value="1"/>
				<?php
				if ($logo_url != '') {
					?><input type="hidden" name="image_url" value="<?= $logo_url; ?>"/><?php
				}
				?>
				<input type="hidden" name="return" value="<?= $mp->getPaypalSuccessURL(); ?>"/>
				<input type="hidden" name="cancel_return" value="<?= $mp->getPaypalCancelURL(); ?>"/>
				<input type="hidden" name="address1" value="<?= $obj_client->getAddress(); ?>"/>
				<input type="hidden" name="address2" value="<?= $obj_client->getAddress2(); ?>"/>
				<input type="hidden" name="zip" value="<?= $obj_client->getPostalCode(); ?>"/>
				<input type="hidden" name="city" value="<?= $obj_client->getCity(); ?>"/>
				<input type="hidden" name="country" value="FR"/>
				<input type="hidden" name="last_name" value="<?= $obj_client->getName(); ?>"/>
				<input type="hidden" name="email" value="<?= $obj_client->getEmail(); ?>"/>
				<input type="hidden" name="lc" value="FR"/>

				<input type="image" src=" https://www.paypal.com/fr_FR/i/bnr/horizontal_solution_PP.gif" border="0" name="submit" alt="Paiement sécurisé par carte bancaire"/>
			</form>

			<script type="text/javascript">
				document.f_paiement_paypal.submit();
			</script>
			</body>
			</html>

			<?php
			die();
			break;
		case 5:
			switch ($cde->fields['mode_paiement']) {
				case moyen_paiement::_TYPE_CB:
					switch ($a_mp[1]->fields['cb_module']) {
						case 'kwixo':
							require DIMS_APP_PATH.'/lib/kwixo/lib/kwixo/process/call_response.php';
							break;
						case 'sips':
							require DIMS_APP_PATH.'/modules/catalogue/include/cb/sips/call_response.php';
							break;
					}
					break;
			}

			$smarty->assign('textclass', $textclass);
			$smarty->assign('msg_confirm', $_SESSION['catalogue']['msg_confirm']);
			$smarty->assign('tpl_name', 'confirmation');
			unset($_SESSION['catalogue']['id_cde']);
			break;
	}

	// Affichage des messages d'erreur
	if (!empty($_SESSION['catalogue']['errors'])) {
		$smarty->assign('errors', $_SESSION['catalogue']['errors']);
		unset($_SESSION['catalogue']['errors']);
	}

	$page['TITLE'] = 'Commander';
	$page['META_DESCRIPTION'] = 'Finaliser votre commande';
	$page['META_KEYWORDS'] = 'commande, rapide';
	$page['CONTENT'] = '';

	ob_end_clean();
}
else {
	$_SESSION['catalogue']['connexion']['oldquery'] = 'op=valider_panier&etape=1';
	dims_redirect($dims->getScriptEnv().'?op=connexion');
}
