<?php

$op = dims_load_securvalue('op', dims_const::_DIMS_CHAR_INPUT, true, false);

switch ($op) {
	default:
		$date = dims_load_securvalue('date', dims_const::_DIMS_NUM_INPUT, true, true);
		$idcmd = dims_load_securvalue('idcmd', dims_const::_DIMS_NUM_INPUT, true, true);

		$lesmois = array("01" => "Janvier","02" => "Février","03" => "Mars","04" => "Avril","05" => "Mai","06" => "Juin","07" => "Juillet","08" => "Août","09" => "Septembre","10" => "Octobre","11" => "Novembre","12" => "Décembre");

		$sql = "
			SELECT  DISTINCT LEFT(date_validation,8) AS date
			FROM    dims_mod_cata_cde
			WHERE   NOT ISNULL(date_validation)
			AND     etat = 'validee'
			AND		NOT ISNULL(id_user)
			ORDER BY date_validation ASC";
		$db->query($sql);

		if ($db->numrows()) {
			$ensjours = array();
			while ($fields = $db->fetchrow()) {
				preg_match("/([0-9]{4})([0-9]{2})([0-9]{2})/", $fields['date'], $reg);
				$ensjours[$reg[1]][$reg[2]][] = $reg[3];
			}

			/*
			 * Si une date est passée en paramètre,
			 * on prend chaque partie de la date et on regarde s'il y a une commande a cette date.
			 * Si on en trouve pas, on prend les valeurs d'une date qui est dans la liste des commandes.
			 */
			if (isset($date)) {
				$reg = array();
				preg_match("/([0-9]{4})([0-9]{0,2})([0-9]{0,2})/",$date,$reg);

				if (isset($reg[1]) && $reg[1] != "" && in_array($reg[1],array_keys($ensjours))) { $annee = $reg[1]; $mois = "00"; $jour = "00"; }
				if ((isset($reg[2]) && $reg[2] != "" && isset($annee) && in_array($reg[2],array_keys($ensjours[$annee]))) || (isset($reg[2]) && $reg[2] == "00") ) { $mois = $reg[2]; $jour = "00"; }
				if ((isset($reg[3]) && $reg[3] != "" && isset($annee) && isset($mois) && in_array($reg[3],$ensjours[$annee][$mois])) || (isset($reg[2]) && $reg[3] == "00") ) $jour = $reg[3];
			}

			/*
			 * Si aucune date est passée en paramètre,
			 * on prend par défaut la date de la dernière commande.
			 */
			if (!isset($annee)) $annee = max(array_keys($ensjours));
			if (!isset($mois)) $mois = max(array_keys($ensjours[$annee]));
			if (!isset($jour)) $jour = max($ensjours[$annee][$mois]);
			if (!isset($date)) $date = $annee . $mois . $jour;

			/*
			 * On définit les périodes de début et de fin
			 */
			if ($mois == "00") {
				$debut = "{$annee}0000";
				$fin = "{$annee}9999";
			}
			elseif ($jour == "00") {
				$debut = "{$annee}{$mois}00";
				$fin = "{$annee}{$mois}31";
			}
			else {
				$debut = $fin = "{$annee}{$mois}{$jour}";
			}

			$sql = "
				SELECT  cmd.id_cde,
						cmd.id_client,
						cmd.id_user,
						cmd.user_name,
						cmd.date_validation,
						cmd.total_ht,
						cmd.date_gen,
						cmd.mail,
						cmd.hors_cata,
						client.code_client,
						client.nom,
						user.login,
						COUNT(det.id_cde_ligne) as nb_art,
						COUNT(det_hc.id_cde_ligne) as nb_art_hc
				FROM    dims_mod_cata_cde cmd
				INNER JOIN	dims_mod_cata_client client
				ON			cmd.id_client = client.id_client
				LEFT JOIN   dims_mod_cata_cde_lignes det
				ON          cmd.id_cde = det.id_cde
				LEFT JOIN   dims_mod_cata_cde_lignes_hc det_hc
				ON          cmd.id_cde = det_hc.id_cde
				LEFT JOIN   dims_user user
				ON          cmd.id_user = user.id
				WHERE   cmd.date_validation BETWEEN {$debut}000000 AND {$fin}999999
				AND     cmd.etat = 'validee'
				AND		NOT ISNULL(cmd.id_user)
				GROUP BY cmd.id_cde
				ORDER BY cmd.date_validation DESC";

			// On garde la requete en session pour l'export excel
			$_SESSION['catalogue']['monitor_request'] = $sql;

			$enscmds = array();
			$db->query($sql);
			while ($fields = $db->fetchrow()) {
				$enscmds[$fields['id_cde']] = $fields;
			}

			echo $skin->open_simplebloc("Liste des commandes","100%");
				?>
				<form name="form" action="<?php echo $dims->getScriptEnv(); ?>" method="post">
				<input type="Hidden" name="part" value="monitor">

				<table cellpadding="2" cellspacing="0" width="100%">
					<tr>
						<td>
							<table cellpadding="2" cellspacing="0">
								<tr>
									<td> Année :</td>
									<td>
										<select class="WebText" name="selannee" style='width:60px' onChange="javascript:document.location.href='<?php echo $dims->getScriptEnv(); ?>?part=monitor&date=' + document.form.selannee.value;">
											<?php
											foreach ($ensjours as $lannee => $moisjour) {
												($lannee == $annee) ? $selected = " selected" : $selected = "";
												echo "<option value=\"$lannee\"$selected>{$lannee}</option>";
											}
											?>
										</select>
									</td>
								</tr>
								<tr>
									<td>Mois :</td>
									<td>
										<select class="WebText" name="selmois" style='width:80px' onChange="javascript:document.location.href='<?php echo $dims->getScriptEnv(); ?>?part=monitor&date=' + document.form.selmois.value;">
											<option value="<?php echo "{$annee}"; ?>00"> --- </option>
											<?php
											foreach ($ensjours[$annee] as $lemois => $lejour) {
												($lemois == $mois) ? $selected = " selected" : $selected = "";
												echo "<option value=\"{$annee}{$lemois}\"$selected>{$lesmois[$lemois]}</option>";
											}
											?>
										</select>
									</td>
								</tr>
								<tr>
									<td>Jour :</td>
									<td>
										<select class="WebText" name="seljour" style="width:120px" onChange="javascript:document.location.href='<?php echo $dims->getScriptEnv(); ?>?part=monitor&date=' + document.form.seljour.value;">
											<option value="<?php echo "{$annee}{$mois}"; ?>00"> --- </option>
											<?php
											if (isset($ensjours[$annee][$mois])) {
												foreach ($ensjours[$annee][$mois] as $lejour) {
													($lejour == $jour) ? $selected = " selected" : $selected = "";
													echo "<option value=\"{$annee}{$mois}{$lejour}\"$selected>{$lejour} {$lesmois[$mois]} {$annee}</option>";
												}
											}
											?>
										</select>
									</td>
								</tr>
							</table>
						</td>
						<td align="right" valign="top">
							<table cellpadding="2" cellspacing="0">
								<tr>
									<td><?php echo catalogue_makegfxbutton('Export XLS','',"document.location.href='".$dims->getScriptEnv()."?op=export&format=xls'",'*'); ?></td>
									<td><?php echo catalogue_makegfxbutton('Export CSV','',"document.location.href='".$dims->getScriptEnv()."?op=export&format=csv'",'*'); ?></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>

				<table cellpadding="2" cellspacing="0" width="100%">
					<tr>
						<td colspan="2">
							<br>
							<table cellpadding="2" cellspacing="1" width="100%">
								<tr bgcolor="<?php echo $skin->values['colsec']; ?>">
									<td class="Title" align="center" width="10">N°</td>
									<td class="Title" width="70">Ref Client</td>
									<td class="Title">Client</td>
									<td class="Title" width="70">Login</td>
									<td class="Title">Utilisateur</td>
									<td class="Title" width="70">Montant</td>
									<td class="Title" width="40">Nb. Art.</td>
									<td class="Title" align="center" width="50">Mail</td>
								</tr>
								<?php
								$nb_cmd = 0;
								$nb_art = 0;
								$total_cmds = 0;
								$color = $skin->values['bgline2'];

								foreach ($enscmds as $id_cmd => $cmd) {
									$date_val = dims_timestamp2local($cmd['date_validation']);
									$color = ($color == $skin->values['bgline1']) ? $skin->values['bgline2'] : $skin->values['bgline1'];

									if ($cmd['hors_cata'] == 0) {
										$nb_cmd++;
										$nb_art += $cmd['nb_art'];
										$total_cmds += $cmd['total_ht'];

										$status_img = array(
											'mail' => array(
												'green' => "<img src=\"./common/modules/catalogue/img/ico_point_green.gif\" alt=\"Envoi de mail réussi\" border=\"0\">",
												'red' => "<img src=\"./common/modules/catalogue/img/ico_point_red.gif\" alt=\"Envoi de mail échoué\" border=\"0\">"
											),
											'gen' => array(
												'green' => "<a href=\"".$dims->getScriptEnv()."?op=recreer_cmd&id_cmd=$id_cmd\"><img src=\"./common/modules/catalogue/img/ico_point_green.gif\" alt=\"Génération réussie\" border=\"0\"></a>",
												'grey' => "<a href=\"".$dims->getScriptEnv()."?op=recreer_cmd&id_cmd=$id_cmd\"><img src=\"./common/modules/catalogue/img/ico_point_grey.gif\" alt=\"Pas de génération\" border=\"0\"></a>",
												'red' => "<a href=\"".$dims->getScriptEnv()."?op=recreer_cmd&id_cmd=$id_cmd\"><img src=\"./common/modules/catalogue/img/ico_point_red.gif\" alt=\"Génération échouée\" border=\"0\"></a>"
											),
											'trans' => array(
												'green' => "<img src=\"./common/modules/catalogue/img/ico_point_green.gif\" alt=\"Transfert réussie\" border=\"0\">",
												'grey' => "<img src=\"./common/modules/catalogue/img/ico_point_grey.gif\" alt=\"Transfert en attente\" border=\"0\">",
												'red' => "<img src=\"./common/modules/catalogue/img/ico_point_red.gif\" alt=\"Transfert échoué\" border=\"0\">"
											),
											'syg' => array(
												'green' => "<img src=\"./common/modules/catalogue/img/ico_point_green.gif\" alt=\"Intégration SYG réussie\" border=\"0\">",
												'grey' => "<img src=\"./common/modules/catalogue/img/ico_point_grey.gif\" alt=\"Intégration SYG en attente\" border=\"0\">",
												'red' => "<img src=\"./common/modules/catalogue/img/ico_point_red.gif\" alt=\"Intégration SYG échouée\" border=\"0\">"
											)
										);

										$img_mail = ($cmd['mail'] != null) ? $status_img['mail']['green'] : $status_img['mail']['red'];


										echo "
											<a name=\"$id_cmd\"></a>
											<tr bgcolor=\"$color\">
												<td>&nbsp;<a href=\"".$dims->getScriptEnv()."?part=monitor&date=$date&idcmd=$id_cmd#$id_cmd\">$id_cmd</a>&nbsp;</td>
												<td>&nbsp;<a href=\"".$dims->getScriptEnv()."?part=monitor&date=$date&idcmd=$id_cmd#$id_cmd\">{$cmd['code_client']}</a>&nbsp;</td>
												<td>&nbsp;<a href=\"".$dims->getScriptEnv()."?part=monitor&date=$date&idcmd=$id_cmd#$id_cmd\">{$cmd['nom']}</a>&nbsp;</td>
												<td>&nbsp;<a href=\"".$dims->getScriptEnv()."?part=monitor&date=$date&idcmd=$id_cmd#$id_cmd\">{$cmd['login']}</a>&nbsp;</td>
												<td>&nbsp;<a href=\"".$dims->getScriptEnv()."?part=monitor&date=$date&idcmd=$id_cmd#$id_cmd\">({$date_val['date']} à {$date_val['time']}) {$cmd['user_name']}</a>&nbsp;</td>
												<td align=\"right\" nowrap>&nbsp;<a href=\"".$dims->getScriptEnv()."?part=monitor&date=$date&idcmd=$id_cmd#$id_cmd\">". catalogue_formateprix($cmd['total_ht']) ."</a>&nbsp;</td>
												<td align=\"right\">&nbsp;{$cmd['nb_art']}&nbsp;</td>
												<td align=\"center\">&nbsp;<a href=\"".$dims->getScriptEnv()."?op=renvoyer_mail_cmd&id_cmd=$id_cmd\">$img_mail</a>&nbsp;</td>
											</tr>";

										// Détail de la commande
										if (isset($idcmd) && $idcmd == $id_cmd) {
											$ensdetcmd = array();

											$sql = "
												SELECT  	det.id_cde_ligne,
															det.id_article,
															det.qte,
															art.reference,
															art_lang.label
												FROM    	dims_mod_cata_cde_lignes det
												INNER JOIN 	dims_mod_cata_article art ON art.id_article = det.id_article
												INNER JOIN 	dims_mod_cata_article_lang art_lang ON art_lang.id_article_1 = art.id_article
												WHERE   id_cde = ".$idcmd;
											$rs = $db->query($sql);
											while ($row = $db->fetchrow($rs)) {
												$ensdetcmd[$row['id_cde_ligne']] = $row;
											}

											if (sizeof($ensdetcmd)) {
												$color = $skin->values['bgline2'];
												echo "
													<tr>
														<td colspan=\"11\">
															<table bgcolor=\"{$skin->values['colsec']}\" cellpadding=\"4\" cellspacing=\"1\" width=\"100%\">
																<tr>
																	<td class=\"Title\" width=\"75\">&nbsp;Ref Article&nbsp;</td>
																	<td class=\"Title\">&nbsp;Désignation&nbsp;</td>
																	<td class=\"Title\" align=\"left\" width=\"75\">&nbsp;Quantité&nbsp;</td>
																</tr>";
												foreach ($ensdetcmd as $id_detail => $detail) {
													$color = ($color == $skin->values['bgline1']) ? $skin->values['bgline2'] : $skin->values['bgline1'];
													echo "
														<tr bgcolor=\"$color\">
															<td>&nbsp;{$detail['reference']}&nbsp;</td>
															<td>&nbsp;{$detail['label']}&nbsp;</td>
															<td align=\"right\">&nbsp;{$detail['qte']}&nbsp;</td>
														</tr>";
												}
												echo "
															</table>
														</td>
													</tr>";
											}
										}
									}
									// Commandes hors catalogue
									else {
										$status_img = array(
											'mail' => array(
												'green' => "<img src=\"./common/modules/catalogue/img/ico_point_green.gif\" alt=\"Envoi de mail réussi\" border=\"0\">",
												'red' => "<img src=\"./common/modules/catalogue/img/ico_point_red.gif\" alt=\"Envoi de mail échoué\" border=\"0\">"
											)
										);

										$img_mail = ($cmd['mail'] != null) ? $status_img['mail']['green'] : $status_img['mail']['red'];

										echo "
											<a name=\"$id_cmd\"></a>
											<tr bgcolor=\"$color\">
												<td>&nbsp;<a href=\"".$dims->getScriptEnv()."?part=monitor&date=$date&idcmd=$id_cmd#$id_cmd\">$id_cmd</a>&nbsp;</td>
												<td>&nbsp;<a href=\"".$dims->getScriptEnv()."?part=monitor&date=$date&idcmd=$id_cmd#$id_cmd\">{$cmd['ref_client']}</a>&nbsp;</td>
												<td>&nbsp;<a href=\"".$dims->getScriptEnv()."?part=monitor&date=$date&idcmd=$id_cmd#$id_cmd\">{$cmd['CNOM']}</a>&nbsp;</td>
												<td>&nbsp;<a href=\"".$dims->getScriptEnv()."?part=monitor&date=$date&idcmd=$id_cmd#$id_cmd\">{$cmd['login']}</a>&nbsp;</td>
												<td>&nbsp;<a href=\"".$dims->getScriptEnv()."?part=monitor&date=$date&idcmd=$id_cmd#$id_cmd\">({$date_val['date']} à {$date_val['time']}) {$cmd['user_name']}</a>&nbsp;</td>
												<td align=\"right\" nowrap>&nbsp;<a href=\"".$dims->getScriptEnv()."?part=monitor&date=$date&idcmd=$id_cmd#$id_cmd\">". catalogue_formateprix($cmd['total_ht']) ."</a>&nbsp;</td>
												<td align=\"right\">&nbsp;{$cmd['nb_art_hc']}&nbsp;</td>
												<td align=\"center\">&nbsp;<a href=\"".$dims->getScriptEnv()."?op=renvoyer_mail_cmd&id_cmd=$id_cmd\">$img_mail</a>&nbsp;</td>
												<td align=\"center\" colspan=\"3\">&nbsp;<a href=\"".$dims->getScriptEnv()."?part=monitor&date=$date&idcmd=$id_cmd#$id_cmd\">HORS CATALOGUE</a>&nbsp;</td>
											</tr>";

										// Détail de la commande
										if (isset($idcmd) && $idcmd == $id_cmd) {
											$ensdetcmd = array();

											$sql = "
												SELECT  *
												FROM    dims_mod_cata_cde_lignes_hc det
												WHERE   id_cmd = $idcmd";
											$rs = $db->query($sql);
											while ($row = $db->fetchrow($rs)) {
												$ensdetcmd[$row['id']] = $row;
											}

											if (sizeof($ensdetcmd)) {
												$color = $skin->values['bgline2'];
												echo "
													<tr>
														<td colspan=\"11\">
															<table bgcolor=\"{$skin->values['colsec']}\" cellpadding=\"4\" cellspacing=\"1\" width=\"100%\">
																<tr>
																	<td class=\"Title\" width=\"75\">&nbsp;Ref Article&nbsp;</td>
																	<td class=\"Title\">&nbsp;Désignation&nbsp;</td>
																	<td class=\"Title\" align=\"left\" width=\"75\">&nbsp;Quantité&nbsp;</td>
																</tr>";
												foreach ($ensdetcmd as $id_detail => $detail) {
													$color = ($color == $skin->values['bgline1']) ? $skin->values['bgline2'] : $skin->values['bgline1'];
													echo "
														<tr bgcolor=\"$color\">
															<td>&nbsp;{$detail['reference']}&nbsp;</td>
															<td>&nbsp;{$detail['designation']}&nbsp;</td>
															<td align=\"right\">&nbsp;{$detail['qte']}&nbsp;</td>
														</tr>";
												}
												echo "
															</table>
														</td>
													</tr>";
											}
										}
									}
								}
								?>
								<tr>
									<td colspan="11" align="center">
										<br/>Il y a <b><? echo $nb_cmd; ?> commandes</b> comprenant <b><?php echo $nb_art; ?> lignes de saisie</b> pour un total de <b><?php echo catalogue_formateprix($total_cmds); ?> &euro;</b><br/><br/>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</form>
				</table>
				<?php
			echo $skin->close_simplebloc();
		}
		else {
			echo "Il n'y a aucune commande à monitorer.";
		}
		break;
	case 'export':
		$format = dims_load_securvalue('format', dims_const::_DIMS_CHAR_INPUT, true, false);

		switch ($format) {
			case 'xls':
				ob_clean();
				ob_start();

				require_once 'Spreadsheet/Excel/Writer.php';

				// Creating a workbook
				$workbook = new Spreadsheet_Excel_Writer();

				// sending HTTP headers
				$workbook->send("monitor.xls");

				$format_title =& $workbook->addFormat( array( 'Align' => 'center', 'Bold'  => 1, 'Color'  => 'black', 'Size'  => 10, 'FgColor' => 'silver'));
				$format =& $workbook->addFormat( array( 'Align' => 'left', 'Bold'  => 0, 'Color'  => 'black', 'Size'  => 10));
				$format_prix =& $workbook->addFormat( array( 'Align' => 'right', 'Bold'  => 0, 'Color'  => 'black', 'Size'  => 10));

				$format_green =& $workbook->addFormat( array( 'FgColor' => 'green'));
				$format_grey =& $workbook->addFormat( array( 'FgColor' => 'grey'));
				$format_red =& $workbook->addFormat( array( 'FgColor' => 'red'));

				$worksheet =& $workbook->addWorksheet("monitor");



				$l=$c=0;
				$columns = array("Numero","Ref Client","Client","Login","Utilisateur","Date","Heure","Montant","Nb. Art.","Mail");
				foreach ($columns as $column) {
					$worksheet->write($l, $c++, $column, $format_title);
				}

				$db->query($_SESSION['catalogue']['monitor_request']);
				while ($fields = $db->fetchrow()) {
					$l++;
					$date_val = dims_timestamp2local($fields['date_validation']);
					$format_mail = ($fields['mail'] != null) ? $format_green : $format_red;

					if (!$fields['hors_cata']) {
						$worksheet->write($l, 0, $fields['id_cde'], $format);
						$worksheet->write($l, 1, $fields['code_client'], $format);
						$worksheet->write($l, 2, $fields['nom'], $format);
						$worksheet->write($l, 3, $fields['login'], $format);
						$worksheet->write($l, 4, $fields['user_name'], $format);
						$worksheet->write($l, 5, "{$date_val['date']}", $format);
						$worksheet->write($l, 6, "{$date_val['time']}", $format);
						$worksheet->write($l, 7, $fields['total_ht'], $format_prix);
						$worksheet->write($l, 8, $fields['nb_art'], $format_prix);
						$worksheet->write($l, 9, "", $format_mail);
					}
					else {
						$worksheet->write($l, 0, $fields['id_cde'], $format);
						$worksheet->write($l, 1, $fields['code_client'], $format);
						$worksheet->write($l, 2, $fields['nom'], $format);
						$worksheet->write($l, 3, $fields['login'], $format);
						$worksheet->write($l, 4, $fields['user_name'], $format);
						$worksheet->write($l, 5, "{$date_val['date']}", $format);
						$worksheet->write($l, 6, "{$date_val['time']}", $format);
						$worksheet->write($l, 7, $fields['total_ht'], $format_prix);
						$worksheet->write($l, 8, $fields['nb_art'], $format_prix);
						$worksheet->write($l, 9, "", $format_mail);
						$worksheet->write($l, 10, "HORS CATALOGUE", $format);
					}
				}

				$workbook->close();

				ob_end_flush();
				die();
				break;
			case 'csv':
				header("Cache-control: private");
				header("Content-type: application/csv");
				header("Content-Disposition: inline; filename=monitor.csv");
				header("Pragma: public");

				ob_clean();
				ob_start();

				$format_green = "O";
				$format_grey = "";
				$format_red = "N";

				echo "\"Numero\",\"Ref Client\",\"Client\",\"Login\",\"Utilisateur\",\"Date\",\"Heure\",\"Montant\",\"Nb. Art.\",\"Mail.\"\n";

				$db->query($_SESSION['catalogue']['monitor_request']);
				while ($fields = $db->fetchrow()) {
					$date_val = dims_timestamp2local($fields['date_validation']);
					$format_mail = ($fields['mail'] != null) ? $format_green : $format_red;

					if (!$fields['hors_cata']) {
						echo "\"{$fields['id_cde']}\",\"{$fields['code_client']}\",\"{$fields['nom']}\",\"{$fields['login']}\",\"{$fields['user_name']}\",\"{$date_val['date']}\",\"{$date_val['time']}\",\"{$fields['total_ht']}\",\"{$fields['nb_art']}\",\"{$format_mail}\"\n";
					}
					else {
						echo "\"{$fields['id_cde']}\",\"{$fields['code_client']}\",\"{$fields['nom']}\",\"{$fields['login']}\",\"{$fields['user_name']}\",\"{$date_val['date']}\",\"{$date_val['time']}\",\"{$fields['total_ht']}\",\"{$fields['nb_art']}\",\"{$format_mail}\",\"HORS CATALOGUE\"\n";
					}
				}

				ob_end_flush();
				die();
				break;
		}
		break;
}

