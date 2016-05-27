<?php
if ($_SESSION['dims']['connected']) {
	require_once DIMS_APP_PATH.'/modules/catalogue/include/class_facture.php';

	$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, false);
	$id_cde = dims_load_securvalue('id_cde', dims_const::_DIMS_NUM_INPUT, true, false);

	// liste des commandes
	if (empty($id_cde)) {
		ob_start();

		// Commandes
		$sql = "
			SELECT		f.*
			FROM		".cata_facture::TABLE_NAME." f
			WHERE	f.code_client = ".$_SESSION['catalogue']['code_client'];
		if ($_SESSION['session_adminlevel'] < cata_const::_DIMS_ID_LEVEL_PURCHASERESP) {
			$sql .= " AND f.id_user = {$_SESSION['dims']['userid']}";
		}
		$sql .= "
			AND		f.etat = '".cata_facture::STATE_VALIDATED."'
			AND		f.type = '".cata_facture::TYPE_DELIVERYORDER."'
			ORDER BY f.date_cree DESC";

		$rs_cmd = $db->query($sql);
		if ($db->numrows($rs_cmd)) {
			$i = 0;

			while ($row = $db->fetchrow($rs_cmd)) {
				// // si pas d'access complet, on verifie la date
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

				$ok = true;
				if ($ok) {
					$i++;

					$date_cree = dims_timestamp2local($row['date_cree']);

					$catalogue['documents'][$row['id']] = array(
						'ID'			=> $row['id'],
						'NUM'			=> $row['num_document'],
						'DATE'			=> "{$date_cree['date']}",
						'PORT'			=> catalogue_formateprix($row['port']),
						'TOTAL_HT'		=> catalogue_formateprix($row['total_ht']),
						'TOTAL_TTC'		=> catalogue_formateprix($row['total_ttc']),
						'PRINTLINK'		=> $dims->getScriptEnv().'?op=imprimer_facture&id='.$row['id']
					);
				}
			}
			$smarty->assign('catalogue', $catalogue);
		}
		$smarty->assign('tpl_name', 'factures');
		$smarty->assign('title', 'Historique de vos bons de livraison');

		// $page['TITLE'] = 'Historique des commandes';
		// $page['META_DESCRIPTION'] = 'Historique des commandes';
		// $page['META_KEYWORDS'] = 'historique, commandes';
		// $page['CONTENT'] = '';

		ob_end_clean();
	}
	else {
		switch ($action) {
			case 'view':
				// detail de la commande
				ob_clean();
				header('Content-type: text/html; charset='._DIMS_ENCODING);
				header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
				header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date dans le passé
				ob_start();

				$sql = '
					SELECT	*
					FROM	dims_mod_cata_cde
					WHERE	code_client IN ('.$liste_clients.')';
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

				echo '
					<div style="display: block;">
					<table style="width:100%;">
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
					<div style="float: left;"><strong>Facturé à :</strong><br/>'.$adrFact.'</div>
					<div style="float: right;"><strong>Livré à :</strong><br/>'.$adrLiv.'</div>
					<div style="clear: both;"></div>

					<p><strong>Classe :</strong> '.stripslashes(str_replace('\r\n', '<br/>', $row['classroom'])).'</p>
					<p><strong>Commentaire :</strong> '.stripslashes(str_replace('\r\n', '<br/>', $row['commentaire'])).'</p>

					<br/>
					<div id="f_detailCde">
					<table class="tableauRecap">';

				$hc = ($row['hors_cata']) ? '_hc' : '';

				$sql = '
					SELECT	l.*, c.total_ht, c.total_ttc, c.date_cree
					FROM	dims_mod_cata_cde_lignes'.$hc.' l
					INNER JOIN	dims_mod_cata_cde c
					ON			c.id_cde = l.id_cde
					AND 		c.code_client IN ('.$liste_clients.')';
				if ($_SESSION['session_adminlevel'] < cata_const::_DIMS_ID_LEVEL_PURCHASERESP) {
					$sql .= ' AND c.id_user = '.$_SESSION['dims']['userid'];
				}
				$sql .= ' WHERE	 l.qte > 0 AND l.id_cde = '.$id_cde;

				$rs_det = $db->query($sql);
				if ($db->numrows($rs_det)) {

					echo '
						<tr>
							<th>Réf</th>
							<th>Désignation</th>
							<th>PU HT</th>
							<th>Qté</th>
							<th>Total HT</th>
						</tr>';

					if (!$row['hors_cata']) {
						while ($row_det = $db->fetchrow($rs_det)) {
							echo '
								<tr>
									<td>'.$row_det['ref'].'</td>
									<td>'.$row_det['label'].'</td>
									<td style="text-align:right">'.catalogue_formateprix($row_det['pu_ht']).' &euro;</td>
									<td style="text-align:right">'.$row_det['qte'].'</td>
									<td style="text-align:right">'.catalogue_formateprix($row_det['pu_ht'] * $row_det['qte']).' &euro;</td>
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

				echo '</table></div></div>';

				$content = preg_replace('/[\t\r\n]/', '', ob_get_contents());
				ob_end_clean();
				die($content);
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
					WHERE	code_client IN ('.$liste_clients.')';
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
					<div style="float: left;"><strong>Facturé à :</strong><br/>'.$adrFact.'</div>
					<div style="float: right;"><strong>Livré à :</strong><br/>'.$adrLiv.'</div>
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
					AND 		c.code_client IN ('.$liste_clients.')';
				if ($_SESSION['session_adminlevel'] < cata_const::_DIMS_ID_LEVEL_PURCHASERESP) {
					$sql .= ' AND c.id_user = '.$_SESSION['dims']['userid'];
				}
				$sql .= ' WHERE	 l.qte > 0 AND l.id_cde = '.$id_cde;
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
									<td style="text-align:right">'.catalogue_formateprix($row_det['pu_ht']).' &euro;</td>
									<td style="text-align:right">'.$row_det['qte'].'</td>
									<td style="text-align:right">'.catalogue_formateprix($row_det['pu_ht'] * $row_det['qte']).' &euro;</td>
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
			case 'excel':
				ob_clean();

				//recuperation des infos sur la commande
				$sql_cmd = 'SELECT 		cde.*
							FROM 		dims_mod_cata_cde cde
							WHERE 		cde.id_cde = '.$id_cde;

				$res_cmd = $db->query($sql_cmd);

				//infos sur les lignes de commande
				$sql_li = 'SELECT 		li.*, ar.gencode
							FROM 		dims_mod_cata_cde_lignes li
							LEFT JOIN	dims_mod_cata_article ar
							ON 			ar.id = li.id_article
							WHERE 		li.id_cde = '.$id_cde.'
							ORDER BY ref';

				$res_li = $db->query($sql_li);


				require_once 'Spreadsheet/Excel/Writer.php';

				// Creating a workbook
				$workbook = new Spreadsheet_Excel_Writer();

				// sending HTTP headers
				$workbook->send("commande.xls");

				// formats d'écriture
				$format_big_title =& $workbook->addFormat( array( 'Align' => 'center', 'Bold'  => 1, 'Color'  => 'black', 'Size'  => 14, 'FgColor' => 'silver'));
				$format_title =& $workbook->addFormat( array( 'Align' => 'center', 'Bold'  => 1, 'Color'  => 'black', 'Size'  => 10, 'FgColor' => 'silver'));
				$format =& $workbook->addFormat(array('TextWrap' => 1, 'Align' => 'left', 'Bold'  => 0, 'Color'  => 'black', 'Size'  => 10));

				$worksheet =& $workbook->addWorksheet("Commande");

				// taille des colonnes
				$worksheet->setColumn(0, 0, 50);
				$worksheet->setColumn(0, 1, 15);
				$worksheet->setColumn(0, 2, 15);
				$worksheet->setColumn(0, 3, 15);
				$worksheet->setColumn(0, 4, 15);
				$worksheet->setColumn(0, 5, 15);
				$worksheet->setColumn(0, 5, 15);

				//titre du doc
				$worksheet->writeString(0, 0, 'COMMANDE no '.$id_cde, $format_big_title);

				//tableau relatif a la commande
				$worksheet->writeString(2, 0, 'Bon de commande', $format_title);
				$worksheet->writeString(2, 1, 'Num commande', $format_title);
				$worksheet->writeString(2, 2, 'Date', $format_title);
				$worksheet->writeString(2, 3, 'Montant HT', $format_title);
				$worksheet->writeString(2, 4, 'Port HT', $format_title);
				$worksheet->writeString(2, 5, 'Montant TTC', $format_title);

				while($tab_cde = $db->fetchrow($res_cmd)) {
					$date = dims_timestamp2local($tab_cde['date_cree']);

					$worksheet->writeString(3, 0, $tab_cde['id_cde'], $format);
					$worksheet->writeString(3, 1, $tab_cde['numcde'], $format);
					$worksheet->writeString(3, 2, $date['date'], $format);
					$worksheet->writeString(3, 3, $tab_cde['total_ht'], $format);
					$worksheet->writeString(3, 4, $tab_cde['port'], $format);
					$worksheet->writeString(3, 5, $tab_cde['total_ttc'], $format);
				}

				//tableau contenant les lignes de commande
				$worksheet->writeString(5, 0, 'Designation', $format_title);
				$worksheet->writeString(5, 1, 'Ref', $format_title);
				$worksheet->writeString(5, 2, 'Gencode', $format_title);
				$worksheet->writeString(5, 3, 'PU HT', $format_title);
				$worksheet->writeString(5, 4, 'Qte', $format_title);
				$worksheet->writeString(5, 5, 'Total HT', $format_title);

				$l = 6;
				while($tab_li = $db->fetchrow($res_li)) {
					if($tab_li['id_article'] == 0 && !is_numeric($tab_li['ref'])) {
						$prem = 100 - $tab_li['remise'];
						$title = "REMISE".$prem."%";
						$worksheet->writeString($l, 0, $title, $format);
						$worksheet->writeString($l, 1, $tab_li['ref'], $format);
						$worksheet->writeString($l, 2, $tab_li['gencode'], $format);
						$worksheet->writeString($l, 3, $tab_li['pu_remise'], $format);
						$worksheet->writeString($l, 4, $tab_li['qte'], $format);
						$worksheet->writeString($l, 5, $tab_li['pu_remise']*$tab_li['qte'], $format);
					}
					else {
						$total = round($tab_li['pu_ht']*$tab_li['qte'], 2);
						$worksheet->writeString($l, 0, $tab_li['label'], $format);
						$worksheet->writeString($l, 1, $tab_li['ref'], $format);
						$worksheet->writeString($l, 2, $tab_li['gencode'], $format);
						$worksheet->writeString($l, 3, $tab_li['pu_ht'], $format);
						$worksheet->writeString($l, 4, $tab_li['qte'], $format);
						$worksheet->writeString($l, 5, $total, $format);
					}

					$l++;
				}

				$workbook->close();
				die();
				break;
		}
	}

}
else {
    $_SESSION['catalogue']['connexion']['oldquery'] = $_SERVER['QUERY_STRING'];
    dims_redirect($dims->getScriptEnv().'?op=connexion');
}
