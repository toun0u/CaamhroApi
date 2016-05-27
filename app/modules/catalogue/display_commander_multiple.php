<?php
if ($_SESSION['dims']['connected']) {
	require_once DIMS_APP_PATH.'modules/catalogue/include/class_article.php';
	require_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/class_client.php';
	require_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/class_client_cplmt.php';
	require_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/class_cde.php';
	require_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/class_cde_ligne.php';
	require_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/class_cde_ligne_hc.php';

	ob_start();

	//recherche du id_client a partir de la session
	if (empty($_SESSION['catalogue']['client_id'])) {
		dims_redirect($dims->getScriptEnv().'?op=panier');
	}

	$obj_client = new cata_client();
	$obj_client->open($_SESSION['catalogue']['client_id']);

	$obj_cli_cplmt = new cata_client_cplmt();
	$obj_cli_cplmt->open($_SESSION['catalogue']['client_id']);

	// si c'est une commande, on vérifie que l'utilisateur a bien le droit
	$list_cde = array();
	if(!empty($_POST['cde_sel'])) {
		unset($_SESSION['catalogue']['cde_operation']);
		foreach($_POST['cde_sel'] as $id_cmd) {
			$id_cmd = dims_sql_filter($id_cmd);

			$cde = new cata_cde();
			$cde->open($id_cmd);

			if($cde->isValideable()) {
				$list_cde[] = $cde;
				$_SESSION['catalogue']['cde_operation']['valid_multiple']['list_cmd'][] = $cde->get('id');
			}
			else {
				$_SESSION['catalogue']['cde_operation']['valid_multiple']['error'][$cde->get('id')] = 'Vous ne pouvez pas valider cette commande.';
			}
		}

		if(empty($list_cde)) {
			dims_redirect($dims->getScriptEnv().'?op=commandes');
		}
	}
	elseif(!empty($_SESSION['catalogue']['cde_operation']['valid_multiple']['list_cmd'])) {
		foreach($_SESSION['catalogue']['cde_operation']['valid_multiple']['list_cmd'] as $id_cmd) {
			$cde = new cata_cde();
			$cde->open($id_cmd);

			$list_cde[] = $cde;
		}
	}
	else {
		dims_redirect($dims->getScriptEnv().'?op=commandes');
	}

	// chargement des modes de paiement
	$a_modes_paiement = array();
	if ($obj_client->fields['type'] == client::TYPE_PROFESSIONAL) {
		$a_modes_paiement[] = array('value' => 'CPT', 'label' => 'Encours client autorisé', 'checked' => false);
	}
	if (_CATA_CB_ACTIVE) {
		$a_modes_paiement[] = array('value' => 'CB', 'label' => 'Carte bancaire', 'checked' => false);
	}

	$etape = dims_load_securvalue('etape', dims_const::_DIMS_NUM_INPUT, true, true);
	if (empty($etape)) $etape = 1;

	switch ($etape) {
		case 1:
			$a_depots = $obj_client->getDepots();

			$catalogue['op'] = $op;
			if ($op == 'valider_commande') {
				$id_cmd = dims_load_securvalue('id_cmd', dims_const::_DIMS_NUM_INPUT, true, false);
				$catalogue['id_cmd'] = $id_cmd;
				$_SESSION['catalogue']['id_cde'] = $id_cmd;
			}

			$catalogue['client'] = array(
					'NOM'			=> $obj_client->fields['nom'],
					'ADR1'			=> $obj_client->fields['adr1'],
					'ADR2'			=> $obj_client->fields['adr2'],
					'ADR3'			=> $obj_client->fields['adr3'],
					'CP'			=> $obj_client->fields['cp'],
					'VILLE'			=> $obj_client->fields['ville'],
					'ID_PAYS'		=> $obj_client->fields['id_pays'],
					'TEL1'			=> variante_display_tel($obj_client->fields['tel1']),
					'TEL2'			=> variante_display_tel($obj_client->fields['tel2']),
					'PORT'			=> variante_display_tel($obj_client->fields['port']),
					'FAX'			=> variante_display_tel($obj_client->fields['fax']),
					'EMAIL'			=> $obj_client->fields['email'],
					'NEWSLETTER'	=> $newsletter
				);

			// Adresses de livraison
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
					'NUMDEPOT'	=> $depot->fields['depot'],
					'NUMADR'	=> $depot->fields['depot'] + 1,
					'CHECKED'	=> ($depot->fields['depot'] == 0) ? 'checked' : '',
					'CIVILITE'	=> $depot->fields['civilite'],
					'NOM'		=> $depot->fields['nomlivr'],
					'ADR'		=> $adr,
					'CP_VILLE'	=> $depot->fields['cp_ville']
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

			$smarty->assign('tpl_name', 'coordonnees');
			$smarty->assign('catalogue', $catalogue);
			break;
		case 2:
			// verification des coordonnees
			$obj_client->setvalues($_POST,'data_');
			$obj_client->save();

			$_SESSION['catalogue']['numdepot'] = dims_load_securvalue('numdepot', dims_const::_DIMS_NUM_INPUT, true, true, true);

			$_SESSION['catalogue']['adrfact'] = dims_load_securvalue('adr_facturation', dims_const::_DIMS_CHAR_INPUT, false, true);

			dims_redirect('/index.php?op='.$op.'&etape=3');
			break;
		case 3:
			$i = 0;
			$total_panier_ht = 0;
			$total_panier_ttc = 0;
			$a_total_tva = array();

			$catalogue['articles'] = array();

			$list_id_cmd = $_SESSION['catalogue']['cde_operation']['valid_multiple']['list_cmd'];

			if (!empty($list_id_cmd)) {
				foreach($list_id_cmd as $id_cmd) {
					$cde = new cata_cde();
					$cde->open($id_cmd);
					$commentaire = $cde->fields['commentaire'];
					$total_panier_ht = 0;

					foreach ($cde->getlignes() as $detail) {
						$i++;
						$article = new article();
						$article->open($detail['ref']);

						$ref = $detail['ref'];
						$qte = $detail['qte'];

						// remise sur les commandes web
						$remises = array(0, $oCatalogue->getParams('tx_remise_cde_web'));

						// base de calcul (HT / TTC ?)
						// TTC
						if ($oCatalogue->getParams('cata_base_ttc')) {
							$pu_brut        	= catalogue_getprixarticle($article, $qte, true);
							$pu_ttc         	= catalogue_getprixarticle($article, $qte) * (1 - $remises[1] / 100);
							$pu_ht          	= $pu_ttc / (1 + $a_tva[$article->fields['ctva']] / 100);
							$total_ttc      	= $pu_ttc * $qte;
							$total_panier_ttc   += $total_ttc;

							if (!isset($a_total_tva[$article->fields['ctva']])) {
								$a_total_tva[$article->fields['ctva']] = 0;
							}
							$a_total_tva[$article->fields['ctva']] += $total_ttc - ($total_ttc / (1 + $a_tva[$article->fields['ctva']] / 100));

							// formatage pour affichage
							$pu_brut	= catalogue_formateprix($pu_brut);
							$pu 		= catalogue_formateprix($pu_ttc);
							$total 		= catalogue_formateprix($total_ttc);
						}
						// HT
						else {
							$pu_brut        	= catalogue_getprixarticle($article, $qte, true);
							$pu_ht          	= catalogue_getprixarticle($article, $qte) * (1 - $remises[1] / 100);
							$pu_ttc         	= $pu_ht * (1 + $a_tva[$article->fields['ctva']] / 100);
							$total_ht       	= $pu_ht * $qte;
							$total_panier_ht    += $total_ht;

							if (!isset($a_total_tva[$article->fields['ctva']])) {
								$a_total_tva[$article->fields['ctva']] = 0;
							}
							$a_total_tva[$article->fields['ctva']] += $total_ht * $a_tva[$article->fields['ctva']] / 100;

							// formatage pour affichage
							$pu_brut	= catalogue_formateprix($pu_brut);
							$pu 		= catalogue_formateprix($pu_ht);
							$total 		= catalogue_formateprix($total_ht);
						}

						// qtes
						if ($oCatalogue->getParams('cata_aff_livrable') && $obj_client->isProfessional()) {
							$qte_liv = ($article->fields['qte'] >= $qte) ? $qte : $article->fields['qte'];
							$qte_rel = ($article->fields['qte'] >= $qte) ? 0 : $qte - $article->fields['qte'];
						}
						else {
							$qte_liv = $qte_rel = 0;
						}

						$catalogue['articles'][$cde->get('id')][] = array(
							'reference'		=> $ref,
							'designation'	=> $article->fields['label'],
							'qte_cde'		=> $qte,
							'qte_liv'		=> $qte_liv,
							'qte_rel'		=> $qte_rel,
							'pu_brut'		=> $pu_brut,
							'pu_net'		=> $pu,
							'remise'		=> $remises[1],
							'total'			=> $total,
							'class'			=> 'ligne'.($i % 2)
						);
					}

					$remise = '';

					// calcul de la remise
					switch ($_SESSION['catalogue']['remcum']) {
						case true:
							if($_SESSION['catalogue']['remgen']>0){
								$remise = $_SESSION['catalogue']['remgen']."%";
							}
							$total_panier_ht = $total_panier_ht - ($total_panier_ht * ($_SESSION['catalogue']['remgen']/100));

							if($total_panier_ht > $_SESSION['catalogue']['seuilrem1'] && $total_panier_ht < $_SESSION['catalogue']['seuilrem2']){
								$remise = $_SESSION['catalogue']['pourcrem1']."%";
							   $total_panier_ht = $total_panier_ht - ($total_panier_ht * ($_SESSION['catalogue']['pourcrem1']/100));
							}
							if($total_panier_ht > $_SESSION['catalogue']['seuilrem2']){
								$remise = $_SESSION['catalogue']['pourcrem2']."%";
								$total_panier_ht = $total_panier_ht - ($total_panier_ht * ($_SESSION['catalogue']['pourcrem2']/100));
							}
						break;

						case false:
							if($total_panier_ht < $_SESSION['catalogue']['seuilrem1']){

								$total_panier_ht = $total_panier_ht - ($total_panier_ht * ($_SESSION['catalogue']['remgen']/100));

								if($_SESSION['catalogue']['remgen'] > 0){
									$remise = $_SESSION['catalogue']['remgen'].'%';
								}
							}
							if($total_panier_ht > $_SESSION['catalogue']['seuilrem1'] && $total_panier_ht < $_SESSION['catalogue']['seuilrem2']){
								$total_panier_ht = $total_panier_ht - ($total_panier_ht * ($_SESSION['catalogue']['pourcrem1']/100));
								if($_SESSION['catalogue']['pourcrem1'] > 0){
									$remise = $_SESSION['catalogue']['pourcrem1']."%";
								}
							}
							if($total_panier_ht > $_SESSION['catalogue']['seuilrem2']){
								$total_panier_ht = $total_panier_ht - ($total_panier_ht * ($_SESSION['catalogue']['pourcrem2']/100));
								if($_SESSION['catalogue']['pourcrem2'] > 0){
									$remise = $_SESSION['catalogue']['pourcrem2']."%";
								}
							}
						break;
					}

					$total_tva = 0;
					foreach ($a_total_tva as $key => $totaltva) {
						if($key != 'fp')
							$total_tva += $totaltva * (1 - $rem / 100);
						else
							$total_tva += $totaltva;
					}
					$total_tva = round($total_tva, 2);

					if ($oCatalogue->getParams('cata_base_ttc')) {
						$total_panier_ht = $total_panier_ttc - $total_tva;
					}
					else {
						$total_panier_ttc = $total_panier_ht + $total_tva;
					}

					// adresse de facturation
					$adr_fact = "<b>{$obj_client->fields['nom']} {$obj_client->fields['prenom']}</b><br/>";
					$adr = trim($obj_client->fields['adr1']);
					if (!empty($obj_client->fields['adr2'])) {
						if (!empty($adr)) $adr .= '<br/>';
						$adr .= trim($obj_client->fields['adr2']);
					}
					if (!empty($obj_client->fields['adr3'])) {
						if (!empty($adr)) $adr .= '<br/>';
						$adr .= trim($obj_client->fields['adr3']);
					}
					$adr_fact .= "{$adr}<br/>{$obj_client->fields['cp']} {$obj_client->fields['ville']}";

					// adresse de livraison
					$a_depots = $obj_client->getDepots();
					$depot = $a_depots[$_SESSION['catalogue']['numdepot']];
					$adr_liv = '<strong>'.$depot['nomlivr'].'</strong><br/>';
					$adr = trim($depot->fields['adr1']);
					if (!empty($depot->fields['adr2'])) {
						if (!empty($adr)) $adr .= '<br/>';
						$adr .= trim($depot->fields['adr2']);
					}
					if (!empty($depot->fields['adr3'])) {
						if (!empty($adr)) $adr .= '<br/>';
						$adr .= trim($depot->fields['adr3']);
					}
					$adr_liv .= $adr.'<br/>'.$depot->fields['cp_ville'];


					$catalogue['commande'][$cde->get('id')] = array(
						'remise'		=> $remise,
						'total_ht'		=> catalogue_formateprix($total_panier_ht),
						'total_tva'		=> catalogue_formateprix($total_tva),
						'total_ttc'		=> catalogue_formateprix($total_panier_ttc),
					);
				}

				$catalogue['livraison']= array(
					'commentaire'	=> '',
					'adr_liv'		=> $adr_liv,
					'adr_fact'		=> $adr_fact,
				);
			}
			else {
				dims_redirect($dims->getScriptEnv().'?op=commandes');
			}

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
				//$a_modes_paiement[0]['checked'] = true;
			}
			$catalogue['modes_paiement'] = $a_modes_paiement;

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

			$smarty->assign('tpl_name', 'recapitulatif_multiple');
			$smarty->assign('catalogue', $catalogue);
			break;
		case 4.1:
		case 4.2:
			if ($etape == 4.1) {
				// on vérifie le mode de paiement
				$mp_ok = false;
				$mode_paiement = dims_load_securvalue('mode_paiement', dims_const::_DIMS_CHAR_INPUT, false, true);
				foreach ($a_modes_paiement as $mp) {
					if ($mp['value'] == $mode_paiement) {
						$mp_ok = true;
						break;
					}
				}
				if (!$mp_ok) {
					dims_redirect($_SERVER['HTTP_REFERER']);
				}
			}

			$commentaire = dims_load_securvalue('commentaire', dims_const::_DIMS_CHAR_INPUT, false, true);

			$a_depots = $obj_client->getDepots();
			$depot = $a_depots[$_SESSION['catalogue']['numdepot']];

			$a_adrFact = $obj_client->getAdrFact();
			$f = $a_adrFact[$_SESSION['catalogue']['adrfact']];

			$list_id_cmd = $_SESSION['catalogue']['cde_operation']['valid_multiple']['list_cmd'];

			if (!empty($list_id_cmd)) {
				foreach($list_id_cmd as $id_cmd) {
					$obj_cde = new cata_cde();
					$obj_cde->open($id_cmd);

					$obj_cde->fields['date_validation']	= dims_createtimestamp();
					$obj_cde->fields['commentaire']		= str_replace(';', '', $commentaire);
					$obj_cde->fields['mode_paiement']	= 'CPT';

					// enregistrement du commercial qui a passé commande le cas échéant
					if (isset($_SESSION['catalogue']['vrp']['id_commercial'])) {
						if ($obj_cde->fields['commentaire'] != '') $obj_cde->fields['commentaire'] .= "\n";
						$obj_cde->fields['commentaire'] .= 'Commande passée par '.$_SESSION['catalogue']['vrp']['nom'];
					}

					$obj_cde->save();

					$total_panier_ht = 0;
					$total_panier_ttc = 0;

					$lignes = $obj_cde->getlignes();

					$a_adrFact = $obj_client->getAdrFact();

					// Lignes de commande
					$rs = $db->query('DELETE FROM dims_mod_cata_cde_lignes WHERE id_cde = '.$obj_cde->fields['id_cde']);

					foreach ($lignes as $detail) {
						$i++;
						$article = new article();
						$article->open($detail['ref']);

						$ref = $detail['ref'];
						$qte = $detail['qte'];

						// remise sur les commandes web
						$remises = array(0, $oCatalogue->getParams('tx_remise_cde_web'));

						// base de calcul (HT / TTC ?)
						// TTC
						if ($oCatalogue->getParams('cata_base_ttc')) {
							$pu_brut        	= catalogue_getprixarticle($article, $qte, true);
							$pu_ttc         	= catalogue_getprixarticle($article, $qte) * (1 - $remises[1] / 100);
							$pu_ht          	= $pu_ttc / (1 + $a_tva[$article->fields['ctva']] / 100);
							$remise 			= round(100 - ($pu_brut / $pu_ttc * 100), 2);
							$total_ttc      	= $pu_ttc * $qte;
							$total_panier_ttc   += $total_ttc;

							if (!isset($a_total_tva[$article->fields['ctva']])) {
								$a_total_tva[$article->fields['ctva']] = 0;
							}
							$a_total_tva[$article->fields['ctva']] += $total_ttc - ($total_ttc / (1 + $a_tva[$article->fields['ctva']] / 100));

							// formatage pour affichage
							// $pu_brut	= catalogue_formateprix($pu_brut);
							// $pu 		= catalogue_formateprix($pu_ttc);
							// $total 		= catalogue_formateprix($total_ttc);
						}
						// HT
						else {
							$pu_brut        	= catalogue_getprixarticle($article, $qte, true);
							$pu_ht          	= catalogue_getprixarticle($article, $qte) * (1 - $remises[1] / 100);
							$pu_ttc         	= $pu_ht * (1 + $a_tva[$article->fields['ctva']] / 100);
							$remise 			= round(100 - ($pu_brut / $pu_ht * 100), 2);
							$total_ht       	= $pu_ht * $qte;
							$total_panier_ht    += $total_ht;

							if (!isset($a_total_tva[$article->fields['ctva']])) {
								$a_total_tva[$article->fields['ctva']] = 0;
							}
							$a_total_tva[$article->fields['ctva']] += $total_ht * $a_tva[$article->fields['ctva']] / 100;

							// formatage pour affichage
							// $pu_brut	= catalogue_formateprix($pu_brut);
							// $pu 		= catalogue_formateprix($pu_ht);
							// $total 		= catalogue_formateprix($total_ht);
						}


						// qtes
						if ($oCatalogue->getParams('cata_aff_livrable') && $obj_client->isProfessional()) {
							$qte_liv = ($article->fields['qte'] >= $qte) ? $qte : $article->fields['qte'];
							$qte_rel = ($article->fields['qte'] >= $qte) ? 0 : $qte - $article->fields['qte'];
						}

						//Enregistrement de la ligne de commande
						$obj_cde_li = new cata_cde_ligne();
						$obj_cde_li->fields['id_cde']			= $obj_cde->fields['id_cde'];
						$obj_cde_li->fields['id_article']		= $article->fields['id'];
						$obj_cde_li->fields['ref']				= $article->fields['reference'];
						$obj_cde_li->fields['label']			= $article->fields['label'];
						$obj_cde_li->fields['label_default']	= $article->fields['label'];
						$obj_cde_li->fields['qte']				= $qte;
						$obj_cde_li->fields['pu_ht']			= $pu_brut;
						$obj_cde_li->fields['pu_remise']		= $pu_ht;
						$obj_cde_li->fields['remise']			= $remise.'%';
						$obj_cde_li->fields['pu_ttc']			= $pu_ttc;
						$obj_cde_li->fields['tx_tva']			= $a_tva[$article->fields['ctva']];
						$obj_cde_li->fields['ctva']				= $article->fields['ctva'];
						dims_print_r($obj_cde_li);
						$obj_cde_li->save();
					}

					// calcul de la remise
					if($_SESSION['catalogue']['remgen']>0 && $_SESSION['catalogue']['remgen'] != ""){
						$obj_cde_li = new cata_cde_ligne();
						$obj_cde_li->fields['id_cde'] = $obj_cde->fields['id_cde'];
						$obj_cde_li->fields['id_article'] = 0;
						$obj_cde_li->fields['ref'] = $_SESSION['catalogue']['rem0'];
						$obj_cde_li->fields['label'] = $_SESSION['catalogue']['rem0'];
						$obj_cde_li->fields['label_default'] = $_SESSION['catalogue']['rem0'];
						$obj_cde_li->fields['qte'] = -1;
						$obj_cde_li->fields['pu_ht'] = $total_panier_ht;
						$obj_cde_li->fields['remise'] = 100 - $_SESSION['catalogue']['remgen'];
						$obj_cde_li->fields['pu_remise'] = ($total_panier_ht - ($total_panier_ht * ($obj_cde_li->fields['remise']/100)));
						$obj_cde_li->fields['ctva'] = 1;
						if($obj_cde_li->fields['remise'] != 100)
						$obj_cde_li->save();
					}

					$total_panier_ht = $total_panier_ht - ($total_panier_ht*($_SESSION['catalogue']['remgen']/100));

					if($total_panier_ht > $_SESSION['catalogue']['seuilrem1'] && $total_panier_ht < $_SESSION['catalogue']['seuilrem2']){
						$obj_cde_li = new cata_cde_ligne();
						$obj_cde_li->fields['id_cde'] = $obj_cde->fields['id_cde'];
						$obj_cde_li->fields['id_article'] = 0;
						$obj_cde_li->fields['ref'] = $_SESSION['catalogue']['rem1'];
						$obj_cde_li->fields['label'] = $_SESSION['catalogue']['rem1'];
						$obj_cde_li->fields['label_default'] = $_SESSION['catalogue']['rem1'];
						$obj_cde_li->fields['qte'] = -1;
						$obj_cde_li->fields['pu_ht'] = $total_panier_ht;
						$obj_cde_li->fields['remise'] = 100 - $_SESSION['catalogue']['pourcrem1'];
						$obj_cde_li->fields['pu_remise'] = ($total_panier_ht - ($total_panier_ht * ($obj_cde_li->fields['remise']/100)));
						$obj_cde_li->fields['ctva'] = 1;
						$total_panier_ht = $total_panier_ht - $obj_cde_li->fields['pu_remise'];
						$obj_cde_li->save();
					}
					if($total_panier_ht > $_SESSION['catalogue']['seuilrem2']){
						$obj_cde_li = new cata_cde_ligne();
						$obj_cde_li->fields['id_cde'] = $obj_cde->fields['id_cde'];
						$obj_cde_li->fields['id_article'] = 0;
						$obj_cde_li->fields['ref'] = $_SESSION['catalogue']['rem2'];
						$obj_cde_li->fields['label'] = $_SESSION['catalogue']['rem2'];
						$obj_cde_li->fields['label_default'] = $_SESSION['catalogue']['rem2'];
						$obj_cde_li->fields['qte'] = -1;
						$obj_cde_li->fields['pu_ht'] = $total_panier_ht;
						$obj_cde_li->fields['remise'] = 100 - $_SESSION['catalogue']['pourcrem2'];
						$obj_cde_li->fields['pu_remise'] = ($total_panier_ht - ($total_panier_ht * ($obj_cde_li->fields['remise']/100)));
						$obj_cde_li->fields['ctva'] = 1;
						$total_panier_ht = $total_panier_ht - $obj_cde_li->fields['pu_remise'];
						$obj_cde_li->save();
					}

					// Si on met la commande en attente depuis le panier,
					// on calcule les de frais de port depuis l'adresse de livraison par défaut
					if ($etape == 4.2 && $obj_cde->fields['cli_liv_cp'] == '') {
						$frais_port = get_fraisport(73, $a_depots[0]['cp'], $total_panier_ht);
						$a_total_tva['fp'] = $frais_port['fp_montant'] * _DEFAULT_TVA;
					}
					else {
						$frais_port = get_fraisport(73, $obj_cde->fields['cli_liv_cp'], $total_panier_ht);
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
						$total_panier_ht = $total_panier_ttc - $total_tva;
					}
					else {
						$total_panier_ttc = $total_panier_ht + $total_tva;
					}

					$obj_cde->fields['total_ht']	= $total_panier_ht;
					$obj_cde->fields['total_tva']	= $total_tva;
					$obj_cde->fields['port']		= ($total_panier_ht < $frais_port['fp_franco']) ? $frais_port['fp_montant'] : 0;
					$obj_cde->fields['port_tx_tva']	= $frais_port['fp_tx_tva'];
					$obj_cde->fields['total_ttc']	= $total_panier_ttc;

					if ($etape == 4.1) {
						if ($_SESSION['session_adminlevel'] >= cata_const::_DIMS_ID_LEVEL_PURCHASERESP) { // Si on est responsable des achats
							// on vérifie que l'adresse de facturation n'est pas une mairie avec validation
							if ($a_adrFact[$_SESSION['catalogue']['adrfact']]['valid_oblig']) {
								$obj_cde->fields['etat'] = commande::_STATUS_AWAITING_VALIDATION3;
								$obj_cde->save();

								// message de confirmation
								$_SESSION['catalogue']['msg_confirm'] = _MSG_CONFIRM_1;

								// Envoi d'un mail à la mairie
								$rs = $db->query('
									SELECT 	u.email
									FROM 	dims_mod_cata_client c
									INNER JOIN 	dims_user u
									ON 			u.id = c.dims_user
									WHERE 	c.code_client = \''.substr($obj_cde->fields['adrfact'], 1).'\'
									LIMIT 0, 1');
								if ($db->numrows($rs)) {
									$row = $db->fetchrow($rs);
									$to = $row['email'];

									// envoi du mail
									include DIMS_APP_PATH.'/modules/catalogue/'._CATA_VARIANTE.'/mail_validation.php';
								}
							}
							else {
								// commande validée
								if ($mode_paiement == 'CPT') {
									$obj_cde->fields['etat'] = commande::_STATUS_VALIDATED;
									$obj_cde->save();

									// message de confirmation
									$_SESSION['catalogue']['msg_confirm'] = _MSG_CONFIRM_2;

									variante_write_cmd_file($_SESSION['catalogue']['id_cde']);

									// envoi des mails
									include DIMS_APP_PATH.'/modules/catalogue/'._CATA_VARIANTE.'/mail_cde.php';
								}
								// en attente du paiement
								else {
									$obj_cde->fields['etat'] = commande::_STATUS_WAIT_PAYMENT;
									$obj_cde->save();

									if ($mode_paiement == 'CB') {
										$_SESSION['catalogue']['id_cde'][] = $obj_cde->fields['id_cde'];
										$_SESSION['catalogue']['total_ttc'] += $obj_cde->fields['total_ttc'];
									}
								}
							}
						}
						// Si on est responsable de service ou qu'on a pas de responsable de service
						elseif($_SESSION['session_adminlevel'] == cata_const::_DIMS_ID_LEVEL_SERVICERESP || $_SESSION['catalogue']['service_id'] == -1) {
							$obj_cde->fields['etat'] = commande::_STATUS_AWAITING_VALIDATION2;
							$obj_cde->save();

							// message de confirmation
							$_SESSION['catalogue']['msg_confirm'] = _MSG_CONFIRM_1;

							$validator = new user();
							$validator->open($_SESSION['catalogue']['achat_id']);
							$to = $validator->fields['email'];

							// envoi du mail
							include DIMS_APP_PATH.'/modules/catalogue/'._CATA_VARIANTE.'/mail_validation.php';
						}
						elseif($_SESSION['session_adminlevel'] == dims_const::_DIMS_ID_LEVEL_USER) { // Si on est responsable de rien du tout
							$obj_cde->fields['etat'] = commande::_STATUS_AWAITING_VALIDATION1;
							$obj_cde->save();

							// message de confirmation
							$_SESSION['catalogue']['msg_confirm'] = _MSG_CONFIRM_1;

							$validator = new user();
							$validator->open($_SESSION['catalogue']['service_id']);
							$to = $validator->fields['email'];

							// envoi du mail
							include DIMS_APP_PATH.'/modules/catalogue/'._CATA_VARIANTE.'/mail_validation.php';
						}
					}
					elseif ($etape == 4.2 && $oCatalogue->getParams('wait_commandes')) {
						// on remet en cours la commande que si c'est le propriétaire qui le fait
						if ($obj_cde->fields['id_user'] == $_SESSION['dims']['userid']) {
							$obj_cde->fields['etat'] = commande::_STATUS_PROGRESS;
						}
						$obj_cde->save();
					}
				}

				if ($etape == 4.1 && $mode_paiement == 'CB') {
					dims_redirect('/index.php?op=valider_commande&etape=4.3');
				}
			}

			if ($etape == 4.1) {
				dims_redirect('/index.php?op=validation_multiple&etape=5');
			}
			elseif ($etape == 4.2 && $oCatalogue->getParams('wait_commandes')) {
				unset($_SESSION['catalogue']['cde_operation']['valid_multiple']['list_cmd']);

				dims_redirect('/index.php?op=commandes');
			}
			break;
		case 4.3:
			if (isset($_SESSION['catalogue']['total_ttc']) && $_SESSION['catalogue']['total_ttc'] > 0) {
				$key = _CATA_CB_CERTIFICATE;

				// Initialisation des paramètres
				$params = array();	// tableau des paramètres du formulaire

				$params['vads_site_id'] 		= _CATA_CB_SITE_ID;

				$montant_en_euro = 42.78;
				$params['vads_amount']			= 100*$_SESSION['catalogue']['total_ttc'];	// en cents
				$params['vads_currency']		= "978";		// norme ISO 4217
				$params['vads_ctx_mode']		= _CATA_CB_CTX_MODE;
				$params['vads_page_action']		= "PAYMENT";
				$params['vads_action_mode']		= "INTERACTIVE";	// saisie de carte réalisée par la plateforme
				$params['vads_payment_config']	= "SINGLE";
				$params['vads_version']			= "V2";
				$params['vads_trans_date']		= date('YmdHis');
				$params['vads_return_mode']		= 'POST';
				$params['vads_order_id']		= $_SESSION['catalogue']['id_cde'];

				// TEMPORAIRE !!!
				// $params['vads_url_return']		= 'http://artifetes.dimsonline.com/autoresponse.php';

				//-------------------------------------------------------
				// Exemple de génération de trans_id basé sur un compteur.
				// La valeur du compteur est stocké dans un fichier count.txt
				// ouverture/lock
				$filename = DIMS_APP_PATH."count.txt"; // il faut ici indiquer le chemin du fichier.
				$fp = fopen($filename, 'r+');
				flock($fp, LOCK_EX);

				// lecture/incrémentation
				$count = (int)fread($fp, 6);    // (int) = conversion en entier.
				$count++;
				if($count < 0 || $count > 899999) {
					$count = 0;
				}

				// on revient au début du fichier
				fseek($fp, 0);
				ftruncate($fp,0);

				// écriture/fermeture/Fin du lock
				fwrite($fp, $count);
				flock($fp, LOCK_UN);
				fclose($fp);

				// le trans_id : on rajoute des 0 au début si nécessaire
				$trans_id = sprintf("%06d",$count);
				// ----------------------------------------------------------

				$params['vads_trans_id'] = $trans_id;


				// Génération de la signature
				ksort($params); // tri des paramètres par ordre alphabétique
				$contenu_signature = "";
				foreach ($params as $nom => $valeur) {
					$contenu_signature .= $valeur."+";
				}
				$contenu_signature .= $key;	// On ajoute le certificat à la fin
				$params['signature'] = sha1($contenu_signature);
				?>
				<html>
				<head>
				<title>Redirection vers la plateforme de paiement</title>
				</head>
				<body>

				Vous allez &ecirc;tre redirig&eacute; sur le site de paiement bancaire...<br/>
				Si vous n'&ecirc;tes pas redirig&eacute; dans les 10 secondes, cliquez sur le bouton ci-dessous pour passer au paiement :<br/><br/>

				<form name="f_paiement_cb" method="POST" action="https://systempay.cyberpluspaiement.com/vads-payment/">
				<?php
				foreach($params as $nom => $valeur) {
					echo '<input type="hidden" name="' . $nom . '" value="' . $valeur . '" />';
				}
				?>
				<input type="submit" name="payer" value="Acc&eacute;der &agrave; la page de paiement" />
				</form>

				<script type="text/javascript">
					document.f_paiement_cb.submit();
				</script>
				</body>
				</html>

				<?php
				die();
			}
			else {
				dims_redirect($_SERVER['HTTP_REFERER']);
			}
			break;
		case 5:
			$catalogue['numcdes'] = implode(', ', $_SESSION['catalogue']['cde_operation']['valid_multiple']['list_cmd']);
			$smarty->assign('msg_confirm', $_SESSION['catalogue']['msg_confirm']);
			$smarty->assign('tpl_name', 'confirmation_multiple');
			$smarty->assign('catalogue', $catalogue);
			unset($_SESSION['catalogue']['cde_operation']['valid_multiple']['list_cmd']);
			break;
	}

	$page['TITLE'] = 'Commander';
	$page['META_DESCRIPTION'] = 'Finaliser votre commande';
	$page['META_KEYWORDS'] = 'commande, rapide';
	$page['CONTENT'] = '';

	ob_end_clean();
}
else {
	$_SESSION['catalogue']['connexion']['oldquery'] = $_SERVER['QUERY_STRING'];
	dims_redirect($dims->getScriptEnv().'?op=connexion');
}
