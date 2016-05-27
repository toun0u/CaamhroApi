<?php
if ($_SESSION['dims']['connected']) {
	require_once DIMS_APP_PATH.'modules/catalogue/include/class_commande.php';
	require_once DIMS_APP_PATH.'modules/catalogue/include/class_client.php';
	// require_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/class_client_cplmt.php';

	$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, true);
	$id_cde = dims_load_securvalue('id_cde', dims_const::_DIMS_NUM_INPUT, true, true);

	$oClient = new client();
	$oClient->openByCode($_SESSION['catalogue']['code_client']);

	// support des codes clients alphanumériques
	$liste_clients = '';
	if (isset($_SESSION['catalogue']['liste_clients'])) {
		foreach ($_SESSION['catalogue']['liste_clients'] as $code_client) {
			$liste_clients .= "'".$code_client."', ";
		}
		$liste_clients = substr($liste_clients, 0, -2);
	}
	else {
		$liste_clients = "'".$_SESSION['catalogue']['code_client']."'";
	}

	$uventeField = 'uvente';
	// $oClientCplmt = new cata_client_cplmt();
	// if ($oClientCplmt->open($oClient->fields['id_client'])) {
	// 	if ($oClientCplmt->fields['soldeur'] == 'Oui') {
	// 		$uventeField = 'uventesolde';
	// 	}
	// }

	if(isset($_SESSION['catalogue']['cde_operation'])) {
		if(!empty($_SESSION['catalogue']['cde_operation']['cmd_fusion'])) {
			if(!empty($_SESSION['catalogue']['cde_operation']['cmd_fusion']['error']))
				$catalogue['operations']['error'] = $_SESSION['catalogue']['cde_operation']['cmd_fusion']['error'];
			if(!empty($_SESSION['catalogue']['cde_operation']['cmd_fusion']['success']))
				$catalogue['operations']['success'] = $_SESSION['catalogue']['cde_operation']['cmd_fusion']['success'];
		}

		if(!empty($_SESSION['catalogue']['cde_operation']['cmd_group'])) {
			if(!empty($_SESSION['catalogue']['cde_operation']['cmd_group']['error']))
				$catalogue['operations']['error'] = $_SESSION['catalogue']['cde_operation']['cmd_group']['error'];
			if(!empty($_SESSION['catalogue']['cde_operation']['cmd_group']['success']))
				$catalogue['operations']['success'] = $_SESSION['catalogue']['cde_operation']['cmd_group']['success'];
		}
		if (isset($_SESSION['catalogue']['cde_operation']['cmd_validate']['error'])) {
			$catalogue['operations']['error'] = $_SESSION['catalogue']['cde_operation']['cmd_validate']['error'];
		}

		unset($_SESSION['catalogue']['cde_operation']);
	}

	switch ($action) {
		default:
			ob_start();

			switch ($orders_view) {
				case 'awaiting_costing':
					$states = "'".commande::_STATUS_AWAITING_COSTING."'";
					$smarty->assign('title', _LABEL_COMMANDESENATTENTECHIFFRAGE);
					break;
				case 'awaiting_validation':
					$states = "'".commande::_STATUS_PROGRESS."', '".commande::_STATUS_AWAITING_VALIDATION1."', '".commande::_STATUS_AWAITING_VALIDATION2."', '".commande::_STATUS_AWAITING_VALIDATION3."', '".commande::_STATUS_REFUSED."'";
					$smarty->assign('title', _LABEL_COMMANDESENCOURS);
					break;
				case 'all':
				default:
					$states = "'".commande::_STATUS_PROGRESS."', '".commande::_STATUS_AWAITING_VALIDATION1."', '".commande::_STATUS_AWAITING_VALIDATION2."', '".commande::_STATUS_AWAITING_VALIDATION3."', '".commande::_STATUS_REFUSED."', '".commande::_STATUS_AWAITING_COSTING."'";
					$smarty->assign('title', _LABEL_COMMANDESENCOURS);
					break;
			}

			// liste des commandes
			$a_commandes = array();

			// Commandes de l'utilisateur
			$sql = "
				SELECT		cde.*,
							col.TR,
							col.DEPART,
							col.ARRIVE,
							col.POIDS,
							col.NCOLIS_DEB,
							col.NCOLIS_FIN,
							col.TYPE,
							col.REF_BL

				FROM		dims_mod_cata_cde cde

				LEFT JOIN	dims_mod_cata_colis col
				ON			cde.id_cde = col.CDE_WEB

				WHERE	cde.id_user = {$_SESSION['dims']['userid']}
				AND		cde.etat IN (".$states.")
				ORDER BY cde.etat DESC, cde.date_cree ASC";
			$rs_cmd = $db->query($sql);
			if ($db->numrows($rs_cmd)) {
				while ($row = $db->fetchrow($rs_cmd)) {
					$a_commandes[] = $row;
				}
			}

			// Commandes que le  responsable des achats doit valider
			if ($_SESSION['session_adminlevel'] >= cata_const::_DIMS_ID_LEVEL_PURCHASERESP) {
				$sql = "
					SELECT		cde.*,
								col.TR,
								col.DEPART,
								col.ARRIVE,
								col.POIDS,
								col.NCOLIS_DEB,
								col.NCOLIS_FIN,
								col.TYPE,
								col.REF_BL

					FROM		dims_mod_cata_cde cde

					INNER JOIN 	dims_group_user gu_admin
					ON 			gu_admin.id_user = {$_SESSION['dims']['userid']}

					INNER JOIN 	dims_group_user gu_user
					ON 			gu_user.id_user != {$_SESSION['dims']['userid']}
					AND 		gu_user.id_user = cde.id_user

					LEFT JOIN	dims_mod_cata_colis col
					ON			cde.id_cde = col.CDE_WEB

					WHERE	(
								( cde.etat IN ('".commande::_STATUS_AWAITING_VALIDATION1."', '".commande::_STATUS_AWAITING_VALIDATION2."') AND cde.code_client IN (".$liste_clients.") )
								OR ( cde.etat = '".commande::_STATUS_AWAITING_VALIDATION3."' AND cde.adrfact = 'C{$_SESSION['catalogue']['code_client']}' )
						)

					ORDER BY cde.etat DESC, cde.date_cree ASC";
				$rs_cmd = $db->query($sql);
				if ($db->numrows($rs_cmd)) {
					while ($row = $db->fetchrow($rs_cmd)) {
						$a_commandes[] = $row;
					}
				}
			}


			if (sizeof($a_commandes)) {
				$i = 0;
				foreach ($a_commandes as $row) {
					// si pas d'access complet, on verifie la date
					$ok = true;
					// $ok = false;
					// if (in_array(getenv('REMOTE_ADDR'), $authorized_ips)) {
					// 	$ok = true;
					// }
					// else {
					// 	$ts = cata_ts2unixts($row['date_cree']);
					// 	if ( (time() - $ts) < ($history_months * 30 * 86400) ) {
					// 		$ok = true;
					// 	}
					// 	$catalogue['historique'] = $history_months;
					// }

					if ($ok) {
						$cde = new commande();
						$cde->fields = $row;

						$i++;
						$date_cree = dims_timestamp2local($row['date_cree']);

						if (!isset($catalogue['etats'][$row['etat']])) {
							switch ($row['etat']) {
								case commande::_STATUS_PROGRESS:
									$catalogue['etats'][$row['etat']]['label'] = 'Demandes d\'achat en attente';
									$catalogue['etats'][$row['etat']]['bgcolor'] = '#e5e8ff';
									break;
								case commande::_STATUS_AWAITING_VALIDATION1:
									$catalogue['etats'][$row['etat']]['label'] = 'Demandes d\'achat en attente de validation';
									$catalogue['etats'][$row['etat']]['bgcolor'] = '#ffe0cc';
									break;
								case commande::_STATUS_AWAITING_VALIDATION2:
									$catalogue['etats'][$row['etat']]['label'] = 'Demandes d\'achat en attente de validation';
									$catalogue['etats'][$row['etat']]['bgcolor'] = '#fffdc0';
									break;
								case commande::_STATUS_AWAITING_VALIDATION3:
									$catalogue['etats'][$row['etat']]['label'] = 'Demandes d\'achat en attente de validation';
									$catalogue['etats'][$row['etat']]['bgcolor'] = '#ffe0cc';
									break;
								case commande::_STATUS_REFUSED:
									$catalogue['etats'][$row['etat']]['label'] = 'Demandes d\'achat refusées';
									$catalogue['etats'][$row['etat']]['bgcolor'] = '#ffd0d0';
									break;
								case commande::_STATUS_AWAITING_COSTING:
									$catalogue['etats'][$row['etat']]['label'] = 'Demandes d\'achat en attente de chiffrage';
									$catalogue['etats'][$row['etat']]['bgcolor'] = '#fffdc0';
									break;
							}
						}

						if (!isset($catalogue['etats'][$row['etat']]['group'][$row['id_regroupement']]) && !empty($row['id_regroupement'])) {
							$catalogue['etats'][$row['etat']]['group'][$row['id_regroupement']]['ID'] = $row['id_regroupement'];
							$catalogue['etats'][$row['etat']]['group'][$row['id_regroupement']]['LABEL'] = 'Commandes groupées : N°'.$row['id_regroupement'];
							$catalogue['etats'][$row['etat']]['group'][$row['id_regroupement']]['MODIFIABLE'] = $cde->isModifiable();
							$catalogue['etats'][$row['etat']]['group'][$row['id_regroupement']]['VALIDABLE'] = $cde->isValideable();
						}

						$catalogue['etats'][$row['etat']]['group'][$row['id_regroupement']]['commandes'][$row['id_cde']] = array(
							'ID'			=> $row['id_cde'],
							'LIBELLE'		=> $row['libelle'],
							'NUM'			=> $row['numcde'],
							'REF_BL'		=> $row['REF_BL'],
							'DATE'			=> "{$date_cree['date']} - {$date_cree['time']}",
							'OWNER'			=> $row['user_name'],
							'CLASSROOM'		=> stripslashes($row['classroom']),
							'PORT'			=> catalogue_formateprix($row['port']),
							'TOTAL_HT'		=> catalogue_formateprix($row['total_ht']),
							'TOTAL_TTC'		=> catalogue_formateprix($row['total_ttc']),
							'VIEWLINK'		=> 'op=commandes&action=view&id_cde='.$row['id_cde'],
							'PRINTLINK'		=> 'op=commandes&action=print&id_cde='.$row['id_cde'],
							'REFUSELINK'	=> 'op=commandes&action=refuser&id_cde='.$row['id_cde'],
							'ADDREFLINK'	=> 'op=commandes&action=getReferenceForm&id_cde='.$row['id_cde'],
							'MODIFIABLE'	=> $cde->isModifiable(),
							'VALIDABLE'		=> $cde->isValideable(),
							'REFUSABLE'		=> $cde->isRefusable(),
							'HORS_CATA' 	=> $cde->fields['hors_cata'],
							'CLASS'			=> 'ligne'.($i % 2),
							'GROUPED'		=> !empty($row['id_regroupement']),
							'STATE_LABEL'   => $cde->getStateLabel(),
							'REQUIRE_COSTING'   => $cde->get('require_costing') || $cde->get('cli_liv_cp') == '',
						);
					}
				}

				// Some action to apply on a selected order list
				$catalogue['orders_action']['fusion'] = true; // TODO : Condition to disable (B2C ?)
				$catalogue['orders_action']['valid_multiple'] = false; // TODO : Condition to disable (B2C ?)
				$catalogue['orders_action']['group'] = false;

				$smarty->assign('catalogue', $catalogue);

				// si recolisage, on avertit l'utilisateur
				if (isset($_SESSION['catalogue']['recolisage']) && $_SESSION['catalogue']['recolisage']) {
					$smarty->assign('recolisage', true);
					unset($_SESSION['catalogue']['recolisage']);
				}
			}
			$smarty->assign('tpl_name', 'commandes');

			$page['TITLE'] = 'Mes commandes en attente';
			$page['META_DESCRIPTION'] = 'Mes commandes en attente';
			$page['META_KEYWORDS'] = 'commandes';
			$page['CONTENT'] = '';

			ob_end_clean();
			break;
		case 'view':
			// detail de la commande
			ob_clean();
			header('Content-type: text/html; charset='._DIMS_ENCODING);
			header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
			header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date dans le passé
			ob_start();

			$cde_ouverte = false;

			// ouverture de la commande par l'initiateur
			$rs_cde = $db->query('
				SELECT	*
				FROM	dims_mod_cata_cde
				WHERE	id_user = '.$_SESSION['dims']['userid'].'
				AND		id_cde = '.$id_cde.'
				LIMIT 0, 1');
			if ($db->numrows($rs_cde)) {
				$row = $db->fetchrow($rs_cde);
				$cde_ouverte = true;
				$date_cree = dims_timestamp2local($row['date_cree']);
				$total_ht = $row['total_ht'];
				$port = $row['port'];
				$total_ttc = $row['total_ttc'];
			}
			// si pas de résultat, ouverture de la commande par le responsable des achats
			elseif ($_SESSION['session_adminlevel'] >= cata_const::_DIMS_ID_LEVEL_PURCHASERESP) {
				$rs_cde = $db->query('
					SELECT	cde.*
					FROM	dims_mod_cata_cde cde

					INNER JOIN 	dims_group_user gu_admin
					ON 			gu_admin.id_user = '.$_SESSION['dims']['userid'].'

					INNER JOIN 	dims_group_user gu_user
					ON 			gu_user.id_user != '.$_SESSION['dims']['userid'].'
					AND 		gu_user.id_user = cde.id_user

					WHERE	id_cde = '.$id_cde.'
					AND 	cde.code_client IN ('.$liste_clients.')
					LIMIT 0, 1');
				if ($db->numrows($rs_cde)) {
					$row = $db->fetchrow($rs_cde);
					$cde_ouverte = true;
					$date_cree = dims_timestamp2local($row['date_cree']);
					$total_ht = $row['total_ht'];
					$port = $row['port'];
					$total_ttc = $row['total_ttc'];
				}
			}

			if ($cde_ouverte) {
				if ($row['etat'] != commande::_STATUS_PROGRESS && $row['etat'] != commande::_STATUS_REFUSED) {
					// adresse de livraison
					$adrLiv = $row['cli_liv_nom'].'<br/>';
					if ($row['cli_liv_adr1'] != '') {
						$adrLiv .= $row['cli_liv_adr1'].'<br/>';
					}
					if ($row['cli_liv_adr2'] != '') {
						$adrLiv .= $row['cli_liv_adr2'].'<br/>';
					}
					if ($row['cli_liv_adr3'] != '') {
						$adrLiv .= $row['cli_liv_adr3'].'<br/>';
					}
					$adrLiv .= $row['cli_liv_cp'].' '.$row['cli_liv_ville'];

					// adresse de facturation
					$adrFact = $row['cli_nom'].'<br/>';
					if ($row['cli_adr1'] != '') {
						$adrFact .= $row['cli_adr1'].'<br/>';
					}
					if ($row['cli_adr2'] != '') {
						$adrFact .= $row['cli_adr2'].'<br/>';
					}
					if ($row['cli_adr3'] != '') {
						$adrFact .= $row['cli_adr3'].'<br/>';
					}
					$adrFact .= $row['cli_cp'].' '.$row['cli_ville'];
				}

				echo '
					<div style="display: block;">
					<table style="width:100%;">
						<tr>
							<td><strong>Bon de commande : </strong>'.$id_cde.'</td>
							<td class="right"><strong>Total HT : </strong>'.catalogue_formateprix($total_ht).' &euro;</td>
						</tr>
						<tr>
							<td><strong>Date : </strong>'.$date_cree['date'].' - '.$date_cree['time'].'</td>
							<td class="right"><strong>Port HT : </strong>'.catalogue_formateprix($port).' &euro;</td>
						</tr>
						<tr>
							<td></td>
							<td class="right"><strong>Total TTC : </strong>'.catalogue_formateprix($total_ttc).' &euro;</td>
						</tr>
					</table>';

				if ($row['etat'] != commande::_STATUS_PROGRESS && $row['etat'] != commande::_STATUS_REFUSED) {
					echo '
						<br/>
						<div style="float: left;"><strong>Facturé à :</strong><br/>'.$adrFact.'</div>
						<div style="float: right;"><strong>Livré à :</strong><br/>'.$adrLiv.'</div>
						<div style="clear: both;"></div>';
				}
				if ($row['etat'] == commande::_STATUS_REFUSED) {
					echo '
						<br/>
						<div style="float: left;"><strong>Motif du refus :</strong><br/>'.stripslashes($row['refus_motif']).'</div>
						<div style="clear: both;"></div>';
				}

				echo '
					<br/>
					<form id="f_detailCde" name="f_detailCde" action="/index.php" method="post">
					<input type="hidden" name="op" value="commandes" />
					<input type="hidden" name="action" value="saveDetailCde" />

					<!--a href="javascript: void(0);" title="Modifier les quantités" id="modifQte">
						<img src="/modules/catalogue/img/modifier.png" alt="Modifier les quantités" />
						<span>Modifier les quantités</span>
					</a-->
					<table class="tableauRecap">';

				$cde_ouverte = false;

				$hc = ($row['hors_cata']) ? '_hc' : '';

				// ouverture de la commande par l'initiateur
				$rs_det = $db->query('
					SELECT	l.*, c.total_ht, c.total_ttc, c.date_cree
					FROM	dims_mod_cata_cde_lignes'.$hc.' l

					INNER JOIN	dims_mod_cata_cde c
					ON			c.id_cde = l.id_cde

					AND			c.id_user = '.$_SESSION['dims']['userid'].'
					WHERE	l.qte > 0
					AND 	l.id_cde = '.$id_cde);
				if ($db->numrows($rs_det)) {
					$cde_ouverte = true;
				}
				// si pas de résultat, ouverture de la commande par le responsable des achats
				elseif ($_SESSION['session_adminlevel'] >= cata_const::_DIMS_ID_LEVEL_PURCHASERESP) {
					$rs_det = $db->query('
						SELECT	l.*, c.total_ht, c.total_ttc, c.date_cree, c.hors_cata
						FROM	dims_mod_cata_cde_lignes'.$hc.' l

						INNER JOIN	dims_mod_cata_cde c
						ON			c.id_cde = l.id_cde

						INNER JOIN 	dims_group_user gu_admin
						ON 			gu_admin.id_user = '.$_SESSION['dims']['userid'].'

						INNER JOIN 	dims_group_user gu_user
						ON 			gu_user.id_user != '.$_SESSION['dims']['userid'].'
						AND 		gu_user.id_user = c.id_user

						WHERE	l.qte > 0
						AND 	l.id_cde = '.$id_cde.'
						AND 	c.code_client IN ('.$liste_clients.')

						GROUP BY l.id_cde_ligne');
					if ($db->numrows($rs_det)) {
						$cde_ouverte = true;
					}
				}

				echo '<thead>';
				if ($cde_ouverte) {
					// base de calcul (HT / TTC ?)
					if ($oCatalogue->getParams('cata_base_ttc')) {
						echo '
							<tr>
								<th style="width:75px;">Réf</th>
								<th style="width:400px;">Désignation</th>
								<th style="width:75px;">PU TTC</th>
								<th style="width:50px;">Qté</th>
								<th>Total TTC</th>
							</tr></thead>';
					}
					else {
						echo '
							<tr>
								<th style="width:75px;">Réf</th>
								<th style="width:400px;">Désignation</th>
								<th style="width:75px;">PU HT</th>
								<th style="width:50px;">Qté</th>
								<th>Total HT</th>
							</tr>';
					}
					echo '</thead><tbody style="height:300px;overflow:auto;display: block;width:756px;margin-left: -1px;">';
					$i = 0;
					while ($row_det = $db->fetchrow($rs_det)) {
						$i++;

						if (!$row['hors_cata']) {
							if ($oCatalogue->getParams('cata_base_ttc')) {
								echo '
									<tr id="row'.$i.'" style="display: inline-table; width: 100%;height:50px;">
										<td style="width:75px;padding:4px;">'.$row_det['ref'].'</td>
										<td style="width:400px;padding:4px;">'.$row_det['label'].'</td>
										<td style="text-align:right;width:75px;padding:4px;">'.catalogue_formateprix($row_det['pu_ttc']).' &euro;</td>
										<td style="width:50px;text-align:right;padding:4px;">'.$row_det['qte'].'</td>
										<td style="text-align:right;padding:4px;">'.catalogue_formateprix($row_det['pu_ttc'] * $row_det['qte']).' &euro;</td>
									</tr>';
							}
							else {
								echo '
									<tr id="row'.$i.'" style="display: inline-table; width: 100%;height:50px;">
										<td style="width:75px;padding:4px;">'.$row_det['ref'].'</td>
										<td style="width:400px;padding:4px;">'.$row_det['label'].'</td>
										<td style="text-align:right;width:75px;padding:4px;">'.catalogue_formateprix($row_det['pu_remise']).' &euro;</td>
										<td style="width:50px;text-align:right;padding:4px;">'.$row_det['qte'].'</td>
										<td style="text-align:right;padding:4px;">'.catalogue_formateprix($row_det['pu_remise'] * $row_det['qte']).' &euro;</td>
									</tr>';
							}
						}
						else {
							echo '
								<tr id="row'.$i.'" style="display: inline-table; width: 100%;height:50px;">
									<td style="width:75px;padding:4px;">'.$row_det['reference'].'</td>
									<td style="width:400px;padding:4px;">'.$row_det['designation'].'</td>
									<td style="text-align:right;width:75px;padding:4px;">'.catalogue_formateprix($row_det['pu']).' &euro;</td>
									<td style="width:50px;text-align:right;padding:4px;">'.$row_det['qte'].'</td>
									<td style="text-align:right;padding:4px;">'.catalogue_formateprix($row_det['pu'] * $row_det['qte']).' &euro;</td>
								</tr>';
						}
					}
				}

				echo '</tbody></table></form></div>';

				echo '
					<script type="text/javascript">
						$("#modifQte").click(function() {
							$("#modifQte").remove();
							for (i = 1; i <= '.$i.'; i++) {
								$("#row"+i+" > td:nth-child(4)").html(\'<input class="detailCdeQte" type="text" name="qte_\'+i+\'" value="\'+$("#row"+i+" > td:nth-child(4)").text()+\'" />\');
								$("#f_detailCde").append(\'<input type="hidden" name="ref_\'+i+\'" value="\'+$("#row"+i+" > td:first-child").text()+\'" />\');
							}
							$("#f_detailCde").append(\'<input type="hidden" name="id_cde" value="'.$id_cde.'" />\');
							$("#f_detailCde").append(\'<input type="hidden" name="nbArt" value="'.$i.'" />\');
							$("#f_detailCde").append(\'<input type="button" value="Mettre à jour" onclick="javascript: document.f_detailCde.submit();" />\');
						});
					</script>';

				$content = preg_replace('/[\t\r\n]/', '', ob_get_contents());
				ob_end_clean();
				die($content);
			}
			else {
				die();
			}

			break;
		case 'print':
			ob_clean();
			header('Content-type: text/html; charset='._DIMS_ENCODING);
			header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
			header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date dans le passé
			ob_start();

			$sql = '
				SELECT	*
				FROM	dims_mod_cata_cde
				WHERE	code_client = \''.$_SESSION['catalogue']['code_client'].'\'';
			if ($_SESSION['session_adminlevel'] < cata_const::_DIMS_ID_LEVEL_PURCHASERESP) {
				$sql .= ' AND id_user = '.$_SESSION['dims']['userid'];
			}
			$sql .= '
				AND		id_cde = '.$id_cde.'
				LIMIT 0, 1';
			$rs_cde = $db->query($sql);
			if ($db->numrows($rs_cde)) {
				$row = $db->fetchrow($rs_cde);
				$date_cree = dims_timestamp2local($row['date_cree']);
				$total_ht = $row['total_ht'];
				$total_ttc = $row['total_ttc'];
			}

			echo '
				<html>
				<head>
					<link href="'.$template_web_path.'/styles.css" rel="stylesheet" type="text/css" />
				</head>
				<body style="background: none;" onload="javascript:window.print();">
				<div style="display: block; width: 95%;">
				<table style="width:100%;">';

			$work = new workspace();
			$work->open($_SESSION['dims']['workspaceid']);
			$tiers = $work->getTiers();

			if ($tiers->getPhotoPath(400)) {
				echo '
						<tr>
							<td colspan="2" align="center">
								<img src="'.$tiers->getPhotoWebPath(400).'" alt="" />
							</td>
						</tr>';
			}

			// Si la commande est en attente de validation, on le précise
			if (
				$row['etat'] == commande::_STATUS_AWAITING_VALIDATION1
				|| $row['etat'] == commande::_STATUS_AWAITING_VALIDATION2
				|| $row['etat'] == commande::_STATUS_AWAITING_VALIDATION3
			) {
				echo '
					<tr><td colspan="2" align="center" style="background-color: #CCCCCC;">Commande en attente de validation</td></tr>
					<tr><td colspan="2">&nbsp;</td></tr>';
			}

			echo '
					<tr>
						<td><strong>Bon de commande : </strong>'.$id_cde.'</td>
						<td class="right"><strong>Total HT : </strong>'.$total_ht.' &euro;</td>
					</tr>
					<tr>
						<td><strong>Date : </strong>'.$date_cree['date'].' - '.$date_cree['time'].'</td>
						<td class="right"><strong>Total TTC : </strong>'.$total_ttc.' &euro;</td>
					</tr>
				</table>

				<br/>
				<div style="clear: both;"></div>

				<p><strong>Commentaire :</strong> '.stripslashes(str_replace('\r\n', '<br/>', $row['commentaire'])).'</p>

				<br/>
				<table class="tableauRecap">';

			$hc = ($row['hors_cata']) ? '_hc' : '';

			$sql = '
				SELECT	l.*, c.total_ht, c.total_ttc, c.date_cree
				FROM	dims_mod_cata_cde_lignes'.$hc.' l
				INNER JOIN	dims_mod_cata_cde c
				ON			c.id_cde = l.id_cde
				AND 		c.code_client = \''.$_SESSION['catalogue']['code_client'].'\'';
			if ($_SESSION['session_adminlevel'] < cata_const::_DIMS_ID_LEVEL_PURCHASERESP) {
				$sql .= ' AND c.id_user = '.$_SESSION['dims']['userid'];
			}
			$sql .= ' WHERE	l.qte > 0 AND l.id_cde = '.$id_cde;
			$rs_det = $db->query($sql);
			if ($db->numrows($rs_det)) {

				echo '
					<tr>
						<th class="txtBlack">Réf</th>
						<th class="txtBlack">Désignation</th>
						<th class="txtBlack">PU HT</th>
						<th class="txtBlack">Qté</th>
						<th class="txtBlack">Total HT</th>
					</tr>';

				if (!$row['hors_cata']) {
					while ($row_det = $db->fetchrow($rs_det)) {
						echo '
							<tr>
								<td>'.$row_det['ref'].'</td>
								<td>'.$row_det['label'].'</td>
								<td style="text-align:right">'.catalogue_formateprix($row_det['pu_remise']).' &euro;</td>
								<td style="text-align:right">'.$row_det['qte'].'</td>
								<td style="text-align:right">'.catalogue_formateprix($row_det['pu_remise'] * $row_det['qte']).' &euro;</td>
							</tr>';
					}
				}
				else {
					while ($row_det = $db->fetchrow($rs_det)) {
						echo '
							<tr>
								<td>'.$row_det['reference'].'</td>
								<td>'.$row_det['designation'].'</td>
								<td style="text-align:right">'.catalogue_formateprix($row_det['pu']).' &euro;</td>
								<td style="text-align:right">'.$row_det['qte'].'</td>
								<td style="text-align:right">'.catalogue_formateprix($row_det['pu'] * $row_det['qte']).' &euro;</td>
							</tr>';
					}
				}
			}

			echo '</table></div></body></html>';

			$content = preg_replace('/[\t\r\n]/', '', ob_get_contents());
			ob_end_clean();
			die($content);
			break;
		case 'getReferenceForm':
			ob_clean();
			header('Content-type: text/html; charset='._DIMS_ENCODING);
			header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
			header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date dans le passé

			echo '<form name="f_addReference" action="/index.php" method="post">';
			echo '<input type="hidden" name="op" value="commandes" />';
			echo '<input type="hidden" name="action" value="addReference" />';
			echo '<input type="hidden" name="id_cde" value="'.$id_cde.'" />';
			echo '<label for="ref_to_add">Référence :</label><input type="text" id="ref_to_add" name="ref_to_add" />&nbsp;&nbsp;';
			echo '<label for="qte_to_add">Quantité :</label><input type="text" id="qte_to_add" name="qte_to_add" size="5" value="1" />';
			echo '<input type="submit" value="Ajouter" />';
			echo '</form>';
			echo '<script type="text/javascript">$("#ref_to_add").focus();</script>';
			die();
			break;
		case 'addReference':
			include_once DIMS_APP_PATH.'modules/catalogue/include/class_commande_ligne.php';

			$obj_cde = new commande();
			// on s'assure que la commande appartient bien à l'utilisateur
			if ($obj_cde->open($id_cde) && $obj_cde->fields['code_client'] == $_SESSION['catalogue']['code_client']) {
				if ($obj_cde->isValideable()) {
					$ref_to_add = strToUpper(dims_load_securvalue('ref_to_add', dims_const::_DIMS_CHAR_INPUT, false, true));
					$qte_to_add = dims_load_securvalue('qte_to_add', dims_const::_DIMS_NUM_INPUT, false, true);
					$qte = (empty($qte_to_add)) ? 1 : $qte_to_add;

					$article = new article();
					if ($article->findByRef($ref_to_add)) {
						// recolisage
						$moduloQte = $qte % $article->fields[$uventeField];
						if ($moduloQte > 0) {
							$_SESSION['catalogue']['recolisage'] = true;
							$qte = $qte + ($article->fields[$uventeField] - $moduloQte);
						}

						// remise sur les commandes web
						$remises = array(0, $oCatalogue->getParams('tx_remise_cde_web'));

						$total_panier = 0;
						// base de calcul (HT / TTC ?)
						// TTC
						if ($oCatalogue->getParams('cata_base_ttc')) {
							$pu_brut        	= catalogue_getprixarticle($article, $qte, true);
							$pu_ttc         	= catalogue_getprixarticle($article, $qte) * (1 - $remises[1] / 100);
							$pu_ht          	= $pu_ttc / (1 + $a_tva[$article->fields['ctva']] / 100);
							$total_ttc      	= $pu_ttc * $qte;
							$total_panier 		+= $total_ttc;
						}
						// HT
						else {
							$pu_brut        	= catalogue_getprixarticle($article, $qte, true);
							$pu_ht          	= catalogue_getprixarticle($article, $qte) * (1 - $remises[1] / 100);
							$pu_ttc         	= $pu_ht * (1 + $a_tva[$article->fields['ctva']] / 100);
							$total_ht       	= $pu_ht * $qte;
							$total_panier 		+= $total_ht;
						}

						$lignes = $obj_cde->getlignes('ref');

						//Enregistrement de la ligne de commande
						$obj_cde_li = new commande_ligne();

						if (isset($lignes[$article->fields['reference']])) {
							$obj_cde_li->open($lignes[$article->fields['reference']]->fields['id_cde_ligne']);
							$obj_cde_li->fields['qte'] += $qte;
						}
						else {
							$obj_cde_li->fields['id_cde']			= $obj_cde->fields['id_cde'];
							$obj_cde_li->fields['qte']				= $qte;
							$obj_cde_li->fields['id_article']		= $article->fields['id'];
							$obj_cde_li->fields['ref']				= $article->fields['reference'];
							$obj_cde_li->fields['label']			= $article->fields['label'];
							$obj_cde_li->fields['label_default']	= $article->fields['label'];
						}
						$obj_cde_li->fields['pu_ht'] = $pu_ht;
						$obj_cde_li->fields['tx_tva'] = $a_tva[$article->fields['ctva']];
						$obj_cde_li->fields['pu_ttc'] = $pu_ttc;
						$obj_cde_li->save();

						$lignes = $obj_cde->getlignes('ref');

						// recalcul du total de la commande
						$total_panier = 0;
						foreach ($lignes as $ligne) {
							$ref = $ligne->fields['ref'];
							$detail = $ligne->fields;

							$article = new article();
							$article->findByRef($ref);

							$qte = $detail['qte'];

							// base de calcul (HT / TTC ?)
							// TTC
							if ($oCatalogue->getParams('cata_base_ttc')) {
								$pu_brut        	= catalogue_getprixarticle($article, $qte, true);
								$pu_ttc         	= catalogue_getprixarticle($article, $qte) * (1 - $remises[1] / 100);
								$pu_ht          	= $pu_ttc / (1 + $a_tva[$article->fields['ctva']] / 100);
								$total_ttc      	= $pu_ttc * $qte;
								$total_panier 		+= $total_ttc;

								if (!isset($a_total_tva[$article->fields['ctva']])) {
									$a_total_tva[$article->fields['ctva']] = 0;
								}
								$a_total_tva[$article->fields['ctva']] += $total_ttc - ($total_ttc / (1 + $a_tva[$article->fields['ctva']] / 100));
							}
							// HT
							else {
								$pu_brut        	= catalogue_getprixarticle($article, $qte, true);
								$pu_ht          	= catalogue_getprixarticle($article, $qte) * (1 - $remises[1] / 100);
								$pu_ttc         	= $pu_ht * (1 + $a_tva[$article->fields['ctva']] / 100);
								$total_ht       	= $pu_ht * $qte;
								$total_panier 		+= $total_ht;

								if (!isset($a_total_tva[$article->fields['ctva']])) {
									$a_total_tva[$article->fields['ctva']] = 0;
								}
								$a_total_tva[$article->fields['ctva']] += $total_ht * $a_tva[$article->fields['ctva']] / 100;
							}
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
							$total_panier_ttc = $total_panier;
							$total_panier_ht = $total_panier_ttc - $total_tva;
						}
						else {
							$total_panier_ht = $total_panier;
							$total_panier_ttc = $total_panier_ht + $total_tva;
						}

						$obj_cde->fields['total_ht']	= $total_panier_ht;
						$obj_cde->fields['total_tva']	= $total_tva;
						$obj_cde->fields['port']		= 0;
						$obj_cde->fields['port_tx_tva']	= 19.6;
						$obj_cde->fields['total_ttc']	= $total_panier_ttc;
						$obj_cde->save();
					}
				}
			}
			dims_redirect('/index.php?op=commandes');
			break;
		case 'saveDetailCde':
			include_once DIMS_APP_PATH.'modules/catalogue/include/class_commande_ligne.php';

			$obj_cde = new commande();
			// on s'assure que la commande appartient bien à l'utilisateur
			if ($obj_cde->open($id_cde) && $obj_cde->fields['code_client'] == $_SESSION['catalogue']['code_client']) {
				if ($obj_cde->isValideable()) {
					$lignes = $obj_cde->getlignes('ref');

					$nbArt = dims_load_securvalue('nbArt', dims_const::_DIMS_NUM_INPUT, false, true);
					for ($i = 1; $i <= $nbArt; $i++) {
						${'ref_'.$i} = dims_load_securvalue('ref_'.$i, dims_const::_DIMS_CHAR_INPUT, false, true);
						${'qte_'.$i} = dims_load_securvalue('qte_'.$i, dims_const::_DIMS_CHAR_INPUT, false, true);

						$article = new article();
						if ($article->findByRef(${'ref_'.$i})) {
							$qte = ${'qte_'.$i};

							// recolisage
							$moduloQte = $qte % $article->fields[$uventeField];
							if ($moduloQte > 0) {
								$_SESSION['catalogue']['recolisage'] = true;
								$qte = $qte + ($article->fields[$uventeField] - $moduloQte);
							}

							// remise sur les commandes web
							$remises = array(0, $oCatalogue->getParams('tx_remise_cde_web'));

							// base de calcul (HT / TTC ?)
							// TTC
							if ($oCatalogue->getParams('cata_base_ttc')) {
								$pu_brut        	= catalogue_getprixarticle($article, $qte, true);
								$pu_ttc         	= catalogue_getprixarticle($article, $qte) * (1 - $remises[1] / 100);
								$pu_ht          	= $pu_ttc / (1 + $a_tva[$article->fields['ctva']] / 100);
								$total_ttc      	= $pu_ttc * $qte;
							}
							// HT
							else {
								$pu_brut        	= catalogue_getprixarticle($article, $qte, true);
								$pu_ht          	= catalogue_getprixarticle($article, $qte) * (1 - $remises[1] / 100);
								$pu_ttc         	= $pu_ht * (1 + $a_tva[$article->fields['ctva']] / 100);
								$total_ht       	= $pu_ht * $qte;
							}

							//Enregistrement de la ligne de commande
							if (isset($lignes[${'ref_'.$i}])) {
								$obj_cde_li = new commande_ligne();
								$obj_cde_li->open($lignes[${'ref_'.$i}]->fields['id_cde_ligne']);
								$obj_cde_li->fields['qte'] = $qte;
								$obj_cde_li->fields['pu_ht'] = $pu_ht;
								$obj_cde_li->fields['tx_tva'] = $a_tva[$article->fields['ctva']];
								$obj_cde_li->fields['pu_ttc'] = $pu_ttc;
								$obj_cde_li->save();

								$lignes[${'ref_'.$i}]->fields['qte'] = $qte;
							}
						}
					}

					// recalcul du total de la commande
					$total_panier = 0;
					foreach ($lignes as $ligne) {
						$ref = $ligne->fields['ref'];
						$qte = $ligne->fields['qte'];

						$article = new article();
						$article->findByRef($ref);

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
						}
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
					$obj_cde->fields['port']		= 0;
					$obj_cde->fields['port_tx_tva']	= 19.6;
					$obj_cde->fields['total_ttc']	= $total_panier_ttc;
					$obj_cde->save();
				}
			}
			dims_redirect('/index.php?op=commandes');
			break;
		case 'refuser':
			ob_clean();
			header('Content-type: text/html; charset='._DIMS_ENCODING);
			header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
			header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date dans le passé

			$obj_cde = new commande();
			$obj_cde->open($id_cde);
			if ($obj_cde->isRefusable()) {
				echo '<form name="f_refus" action="/index.php" method="post">';
				echo '<input type="hidden" name="op" value="commandes" />';
				echo '<input type="hidden" name="action" value="saveRefus" />';
				echo '<input type="hidden" name="id_cde" value="'.$id_cde.'" />';
				echo '<label for="motif_refus">Motif du refus :</label><textarea id="motif_refus" name="motif_refus" cols="50" rows="5"></textarea> <input type="submit" value="Refuser" />';
				echo '</form>';
				echo '<script type="text/javascript">$("#motif_refus").focus();</script>';
			}
			die();
			break;
		case 'saveRefus':
			$obj_cde = new commande();
			// on s'assure que la commande appartient bien à l'utilisateur
			if ($obj_cde->open($id_cde) && $obj_cde->isRefusable()) {
				$obj_cde->fields['refus_motif'] = dims_load_securvalue('motif_refus', dims_const::_DIMS_CHAR_INPUT, false, true);
				$obj_cde->fields['refus_user_id'] = $_SESSION['dims']['userid'];
				$obj_cde->fields['refus_user_name'] = $_SESSION['dims']['user']['firstname'].' '.$_SESSION['dims']['user']['lastname'];
				$obj_cde->fields['etat'] = commande::_STATUS_REFUSED;
				$obj_cde->save();

				$user = new user();
				$user->open($obj_cde->fields['id_user']);

				// Envoi du mail de notification de refus à l'initiateur de la commande
				if ($user->fields['email'] != '') {
					$obj_cde->sendRefusMail($user->fields['email'], $template_name);
				}
			}
			dims_redirect('/index.php?op=commandes');
			break;
	}
}
else {
	$_SESSION['catalogue']['connexion']['oldquery'] = $_SERVER['QUERY_STRING'];
	dims_redirect($dims->getScriptEnv().'?op=connexion');
}
