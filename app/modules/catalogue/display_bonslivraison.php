<?php
if ($_SESSION['dims']['connected']) {
	$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, false);
	$numbl = dims_load_securvalue('numbl', dims_const::_DIMS_NUM_INPUT, true, false);
	$numcde = dims_load_securvalue('numcde', dims_const::_DIMS_NUM_INPUT, true, false);

	// liste des BLs
	if (empty($numbl)) {
		ob_start();

		// BL
		$sql = "
			SELECT	b.*, t.id AS id_trp, IF (t.id > 0, t.raisoc, 'N.C.') AS trp_raisoc
			FROM	dims_mod_cata_bl b
			LEFT JOIN	dims_mod_cata_transporteur t
			ON			t.id = b.transporteur
			WHERE	b.code_client = ".$_SESSION['catalogue']['code_client'];
		if (!empty($numcde)) {
			$sql .= " AND b.numcde_web = $numcde";
		}
		if (isset($_GET['uniq']) && $_GET['uniq'] == 1 ) {
			$sql .= " AND b.numbl = $numbl";
		}
		$sql .= " ORDER BY b.numbl DESC";

		$rs_cmd = $db->query($sql);
		if ($db->numrows($rs_cmd)) {
			$i = 0;

			while ($row = $db->fetchrow($rs_cmd)) {
				// si pas d'access complet, on verifie la date
				$ok = true;
				// $ok = false;
				// if (in_array(getenv('REMOTE_ADDR'), $authorized_ips)) {
				// 	$ok = true;
				// }
				// else {
				// 	$ts = cata_ts2unixts($row['date']);
				// 	if ( (time() - $ts) < ($history_months * 30 * 86400) ) {
				// 		$ok = true;
				// 	}
				// 	$catalogue['historique'] = $history_months;
				// }

				if ($ok) {
					$i++;
					$date = dims_timestamp2local($row['date'].'000000');

					$viewlink = 'op=bonslivraison&action=view&numbl='.$row['numbl'];
					if (!empty($numcde)) {
						$viewlink .= '&numcde='.$numcde;
					}

					$catalogue['bonslivraison'][$row['id']] = array(
						'ID'			=> $row['id'],
						'NUMBL'			=> $row['numbl'],
						'NUMCDE'		=> $row['numcde'],
						'NUMCDEWEB'		=> $row['numcde_web'],
						'DATE'			=> $date['date'],
						'TRANSPORTEUR'	=> ($row['id_trp']) ? $row['trp_raisoc'] : '<i>Aucune information disponible</i>',
						'MTHT'			=> catalogue_formateprix($row['mtht']),
						'MTPORT'		=> catalogue_formateprix($row['mtport']),
						'MTTTC'			=> catalogue_formateprix($row['mtttc']),
						'VIEWLINK'		=> $viewlink,
						'CDELINK'		=> 'op=historique&action=view&id_cde='.$row['numcde_web'],
						'CLASS'			=> 'ligne'.($i % 2)
						);
				}
			}
			$smarty->assign('catalogue', $catalogue);
		}
		$smarty->assign('tpl_name', 'bonslivraison');

		$page['TITLE'] = 'Bons de livraison';
		$page['META_DESCRIPTION'] = 'Bons de livraison';
		$page['META_KEYWORDS'] = 'historique, bons livraison';
		$page['CONTENT'] = '';

		ob_end_clean();
	}
	// detail du BL
	else {
		switch ($action) {
			case 'view':
				@ob_end_clean();
				header('Content-type: text/html; charset='._DIMS_ENCODING);
				header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
				header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date dans le passé
				ob_start();

				$rs_bl = $db->query('
					SELECT	b.*, c.escompte, t.id AS id_trp, IF (t.id > 0, t.raisoc, \'N.C.\') AS trp_raisoc
					FROM	dims_mod_cata_bl b
					INNER JOIN 	dims_mod_cata_client c
					ON 			c.code_client = b.code_client
					LEFT JOIN	dims_mod_cata_transporteur t
					ON			t.id = b.transporteur
					WHERE	b.numbl = '.$numbl.'
					AND		b.code_client = '.$_SESSION['catalogue']['code_client']);
				if ($db->numrows($rs_bl)) {
					$row = $db->fetchrow($rs_bl);
					$date = dims_timestamp2local($row['date'].'000000');
					$numcde = $row['numcde'];
					$port = $row['mtport'];
					$total_ht = $row['mtht'];
					$total_ttc = $row['mtttc'];
					$transporteur = ($row['id_trp']) ? $row['trp_raisoc'] : '<em>Aucune information disponible</em>';
					$escompte = ($row['escompte'] != '0.00') ? '<strong>Escompte : </strong>'.$row['escompte'].'%' : '&nbsp;';
				}

					// <a id="msgboxclose" class="msgboxclose"></a>
				echo '

					<div style="display: block;">
					<table style="width:100%;">
						<tr>
							<td><strong>Bon de livraison : </strong>'.$numbl.'</td>
							<td class="right"><strong>Port : </strong>'.$port.' &euro;</td>
						</tr>
						<tr>
							<td><strong>Bon de commande : </strong>'.$numcde.'</td>
							<td class="right"><strong>Total HT : </strong>'.$total_ht.' &euro;</td>
						</tr>
						<tr>
							<td><strong>Date : </strong>'.$date['date'].'</td>
							<td class="right"><strong>Total TTC : </strong>'.$total_ttc.' &euro;</td>
						</tr>
						<tr>
							<td><strong>Transporteur : </strong>'.$transporteur.'</td>
							<td class="right">'.$escompte.'</td>
						</tr>
					</table>
					<br/>
					<table class="tableauRecap">';

				$rs_det = $db->query('
					SELECT	bl.*
					FROM	dims_mod_cata_bl_lignes bl
					INNER JOIN	dims_mod_cata_bl b
					ON			b.numbl = bl.numbl
					AND			b.code_client = '.$_SESSION['catalogue']['code_client'].'
					WHERE	bl.numbl = '.$numbl.'
					GROUP BY bl.ref_article');

				if ($db->numrows($rs_det)) {
					echo '
					<tr>
						<th>Réf</th>
						<th>Désignation</th>
						<th>PU HT</th>
						<th>Qté Cdé</th>
						<th>Qté Liv</th>
						<th>Qté Rel</th>
						<th>Total HT</th>
					</tr>';

					while ($row_det = $db->fetchrow($rs_det)) {
						echo '
						<tr>
							<td>'.$row_det['ref_article'].'</td>
							<td>'.$row_det['designation'].'</td>
							<td style="text-align:right">'.catalogue_formateprix($row_det['puht']).' &euro;</td>
							<td style="text-align:right">'.intval($row_det['qte_cde']).'</td>
							<td style="text-align:right">'.intval($row_det['qte_liv']).'</td>
							<td style="text-align:right">'.intval($row_det['qte_rel']).'</td>
							<td style="text-align:right">'.catalogue_formateprix($row_det['puht'] * $row_det['qte_liv']).' &euro;</td>
						</tr>';


					}
				}

				echo '</table></div>';

				$content = preg_replace('/[\t\r\n]/', '', ob_get_contents());
		//		$content = ob_get_contents();
				ob_end_clean();
				die($content);
				break;
			case 'print':
				@ob_end_clean();
				header('Content-type: text/html; charset='._DIMS_ENCODING);
				header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
				header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date dans le passé
				ob_start();

				$rs_bl = $db->query('
					SELECT	b.*, t.id AS id_trp, IF (t.id > 0, t.raisoc, \'N.C.\') AS trp_raisoc
					FROM	dims_mod_cata_bl b
					LEFT JOIN	dims_mod_cata_transporteur t
					ON			t.id = b.transporteur
					WHERE	b.numbl = '.$numbl.'
					AND		b.code_client = '.$_SESSION['catalogue']['code_client']);
				if ($db->numrows($rs_bl)) {
					$row = $db->fetchrow($rs_bl);
					$date = dims_timestamp2local($row['date'].'000000');
					$numcde = $row['numcde'];
					$port = $row['mtport'];
					$total_ht = $row['mtht'];
					$total_ttc = $row['mtttc'];
					$transporteur = ($row['id_trp']) ? $row['trp_raisoc'] : '<em>Aucune information disponible</em>';
				}

				echo '
					<html>
					<head>
						<link href="/templates/frontoffice/artifetes/styles.css" rel="stylesheet" type="text/css" />
					</head>
					<body onload="javascript:window.print();">

					<div style="display: block;">
					<table style="width:100%;">
						<tr>
							<td><strong>Bon de livraison : </strong>'.$numbl.'</td>
							<td class="right"><strong>Port : </strong>'.$port.' &euro;</td>
						</tr>
						<tr>
							<td><strong>Bon de commande : </strong>'.$numcde.'</td>
							<td class="right"><strong>Total HT : </strong>'.$total_ht.' &euro;</td>
						</tr>
						<tr>
							<td><strong>Date : </strong>'.$date['date'].'</td>
							<td class="right"><strong>Total TTC : </strong>'.$total_ttc.' &euro;</td>
						</tr>
						<tr>
							<td><strong>Transporteur : </strong>'.$transporteur.'</td>
							<td>&nbsp;</td>
						</tr>
					</table>
					<br/>
					<table class="tableauRecap">';

				$rs_det = $db->query('
					SELECT	bl.*
					FROM	dims_mod_cata_bl_lignes bl
					INNER JOIN	dims_mod_cata_bl b
					ON			b.numbl = bl.numbl
					AND			b.code_client = '.$_SESSION['catalogue']['code_client'].'
					WHERE	bl.numbl = '.$numbl.'
					GROUP BY bl.ref_article');

				if ($db->numrows($rs_det)) {
					echo '
					<tr>
						<th>Réf</th>
						<th>Désignation</th>
						<th>PU HT</th>
						<th>Qté Cdé</th>
						<th>Qté Liv</th>
						<th>Qté Rel</th>
						<th>Total HT</th>
					</tr>';

					while ($row_det = $db->fetchrow($rs_det)) {
						echo '
						<tr>
							<td>'.$row_det['ref_article'].'</td>
							<td>'.$row_det['designation'].'</td>
							<td style="text-align:right">'.catalogue_formateprix($row_det['puht']).' &euro;</td>
							<td style="text-align:right">'.intval($row_det['qte_cde']).'</td>
							<td style="text-align:right">'.intval($row_det['qte_liv']).'</td>
							<td style="text-align:right">'.intval($row_det['qte_rel']).'</td>
							<td style="text-align:right">'.catalogue_formateprix($row_det['puht'] * $row_det['qte_liv']).' &euro;</td>
						</tr>';


					}
				}

				echo '</table></div></body></html>';

				$content = preg_replace('/[\t\r\n]/', '', ob_get_contents());
		//		$content = ob_get_contents();
				ob_end_clean();
				die($content);
				break;
			case 'excel':
				ob_clean();

				//recuperation des infos sur la commande
				$rs_bl = $db->query('
					SELECT	b.*, t.id AS id_trp, IF (t.id > 0, t.raisoc, \'N.C.\') AS trp_raisoc
					FROM	dims_mod_cata_bl b
					LEFT JOIN	dims_mod_cata_transporteur t
					ON			t.id = b.transporteur
					WHERE	b.numbl = '.$numbl.'
					AND		b.code_client = '.$_SESSION['catalogue']['code_client']);
				if ($db->numrows($rs_bl)) {
					$row = $db->fetchrow($rs_bl);
					$date = dims_timestamp2local($row['date'].'000000');
					$numcde = $row['numcde'];
					$port = $row['mtport'];
					$total_ht = $row['mtht'];
					$total_ttc = $row['mtttc'];
					$transporteur = ($row['id_trp']) ? $row['trp_raisoc'] : '<em>Aucune information disponible</em>';
				}

				$rs_det = $db->query('
					SELECT	bl.*, a.gencode
					FROM	dims_mod_cata_bl_lignes bl
					INNER JOIN	dims_mod_cata_bl b
					ON			b.numbl = bl.numbl
					AND			b.code_client = '.$_SESSION['catalogue']['code_client'].'
					LEFT JOIN 	dims_mod_cata_article a
					ON 			a.reference = bl.ref_article
					WHERE	bl.numbl = '.$numbl.'
					GROUP BY bl.ref_article');

				require_once 'Spreadsheet/Excel/Writer.php';

				// Creating a workbook
				$workbook = new Spreadsheet_Excel_Writer();

				// sending HTTP headers
				$workbook->send("bon_livraison.xls");

				// formats d'écriture
				$format_big_title =& $workbook->addFormat( array( 'Align' => 'center', 'Bold'  => 1, 'Color'  => 'black', 'Size'  => 14, 'FgColor' => 'silver'));
				$format_title =& $workbook->addFormat( array( 'Align' => 'center', 'Bold'  => 1, 'Color'  => 'black', 'Size'  => 10, 'FgColor' => 'silver'));
				$format =& $workbook->addFormat(array('TextWrap' => 1, 'Align' => 'left', 'Bold'  => 0, 'Color'  => 'black', 'Size'  => 10));

				$worksheet =& $workbook->addWorksheet("Bon de livraison");

				// taille des colonnes
				$worksheet->setColumn(0, 0, 50);
				$worksheet->setColumn(0, 1, 15);
				$worksheet->setColumn(0, 2, 15);
				$worksheet->setColumn(0, 3, 15);
				$worksheet->setColumn(0, 4, 15);
				$worksheet->setColumn(0, 5, 15);
				$worksheet->setColumn(0, 5, 15);

				//titre du doc
				$worksheet->writeString(0, 0, 'LIVRAISON no '.$numbl, $format_big_title);

				//tableau relatif a la commande
				$worksheet->writeString(2, 0, 'Bon de livraison', $format_title);
				$worksheet->writeString(2, 1, 'Bon de commande', $format_title);
				$worksheet->writeString(2, 2, 'Date', $format_title);
				$worksheet->writeString(2, 3, 'Montant HT', $format_title);
				$worksheet->writeString(2, 4, 'Port HT', $format_title);
				$worksheet->writeString(2, 5, 'Montant TTC', $format_title);

				$worksheet->writeString(3, 0, $numbl, $format);
				$worksheet->writeString(3, 1, $numcde, $format);
				$worksheet->writeString(3, 2, $date['date'], $format);
				$worksheet->writeString(3, 3, $total_ht, $format);
				$worksheet->writeString(3, 4, $port, $format);
				$worksheet->writeString(3, 5, $total_ttc, $format);

				//tableau contenant les lignes de commande
				$worksheet->writeString(5, 0, 'Designation', $format_title);
				$worksheet->writeString(5, 1, 'Ref', $format_title);
				$worksheet->writeString(5, 2, 'Gencode', $format_title);
				$worksheet->writeString(5, 3, 'PU HT', $format_title);
				$worksheet->writeString(5, 4, 'Qte cde', $format_title);
				$worksheet->writeString(5, 5, 'Qte liv', $format_title);
				$worksheet->writeString(5, 6, 'Qte rel', $format_title);
				$worksheet->writeString(5, 7, 'Total HT', $format_title);

				$l = 6;
				while($row_det = $db->fetchrow($rs_det)) {
					$worksheet->writeString($l, 0, $row_det['designation'], $format);
					$worksheet->writeString($l, 1, $row_det['ref_article'], $format);
					$worksheet->writeString($l, 2, $row_det['gencode'], $format);
					$worksheet->writeString($l, 3, catalogue_formateprix($row_det['puht']), $format);
					$worksheet->writeString($l, 4, intval($row_det['qte_cde']), $format);
					$worksheet->writeString($l, 5, intval($row_det['qte_liv']), $format);
					$worksheet->writeString($l, 6, intval($row_det['qte_rel']), $format);
					$worksheet->writeString($l, 7, catalogue_formateprix($row_det['puht'] * $row_det['qte_liv']), $format);
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
