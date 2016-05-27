<?php
$id_cmd = dims_load_securvalue('id_cmd', dims_const::_DIMS_CHAR_INPUT, true, true);
$code_promo = dims_load_securvalue('code_promo', dims_const::_DIMS_CHAR_INPUT, true, true);
$mode_paiement = dims_load_securvalue('mode_paiement', dims_const::_DIMS_CHAR_INPUT, true, true);

if (empty($mode_paiement)) {
	dims_redirect('/index.php?op=afficher_fraisport&id_cmd='.$id_cmd);
}

if (!empty($id_cmd)) {
	include_once './include/functions/mail.php';

	include_once DIMS_APP_PATH.'/modules/catalogue/include/class_client.php';
	$client = new client();
	$client->open($_SESSION['catalogue']['code_client']);

	include_once DIMS_APP_PATH.'/modules/catalogue/include/class_commande.php';
	$commande = new commande();
	$commande->open($id_cmd);

	// reouverture du bon client si validation d'une commande via un validateur multi-compte
	if (in_array($commande->fields['ref_client'], $_SESSION['catalogue']['liste_clients'])) {
		$client = new client();
		$client->open($commande->fields['ref_client']);
	}

	if (trim($commande->fields['CPPTLL']) == '') dims_redirect('/index.php?op=valider_commande&id_cmd='.$id_cmd);

	$commande->articles = $commande->getarticles();

	$fp_montant = 0;
	get_fraisport(76, $commande->fields['CPPTLL']);
	$fp_montant = ($commande->fields['total_ht'] >= $_SESSION['catalogue']['frais_port']['fp_franco']) ? 0 : $_SESSION['catalogue']['frais_port']['fp_montant'];

	$commande->fields['code_promo'] = $code_promo;

	$commande->fields['port'] = number_format($fp_montant, 2, '.', '');
	$commande->fields['total_ttc'] = round($commande->fields['total_ht'] + $commande->fields['port'] + $commande->fields['total_tva'], 2);

	$commande->save();


	if ($commande->fields['mail'] != 1) {
		if ($_SESSION['session_adminlevel'] >= _DIMS_ID_LEVEL_PURCHASERESP) { // Si on est responsable des achats
			// On regarde le credit de la personne qui a passela commande
			if ($commande->fields['id_user'] != $_SESSION['dims']['userid']) {
				$budget = catalogue_getbudget_user($commande->fields['id_user'], $commande->fields['id_group']);
			}
			else {
				catalogue_getbudget();
				$budget = $_SESSION['catalogue']['budget'];
			}

			if ($budget['credit'] == -1 || $budget['credit'] >= $commande->fields['total_ht'] || $client->fields['budget_non_bloquant']) {
				if ($budget['id'] != '') $id_budget = $budget['id'];
				else $id_budget = 0;

				// Si la personne souhaite payer par carte bleue
				if ($mode_paiement == 'cb') {
					//------------------------------
					// Enregistrement de la commande
					//------------------------------
					$commande->fields['id_budget'] = $id_budget;
					$commande->fields['date_validation'] = dims_createtimestamp();
					$commande->fields['mode_paiement'] = $mode_paiement;
					$commande->save_lite();

					dims_redirect('/index.php?op=call_request&id_cmd='.$commande->fields['id']);
				}
				else {
					// Si la personne ne paie pas par carte bleue

					//------------------------------
					// Enregistrement de la commande
					//------------------------------
					$commande->fields['etat'] = 'validee';
					$commande->fields['id_budget'] = $id_budget;
					$commande->fields['date_validation'] = dims_createtimestamp();
					if (isset($mode_paiement)) $commande->fields['mode_paiement'] = $mode_paiement;

					if (!empty($commande->fields['nb_dest'])) {
						if ($commande->fields['commentaire'] != '') {
							$commande->fields['commentaire'] .= '\r\n\r\n';
						}
						$commande->fields['commentaire'] .= 'Cette commande est destinée à '.$commande->fields['nb_dest'].' personne(s).';
					}

					$commande->save_lite();

					//--------------------------------
					// Creation du mail au format HTML
					//--------------------------------
					$title = ($commande->fields['hors_cata'] == 1) ? 'Commande Hors Catalogue' : 'Commande';
					$date = dims_gettimestampdetail($commande->fields['date_validation']);

					// Recherche du nom d'utilisateur et du service
					$sql = "
						SELECT  user.login,
								user.email,
								user.lastname,
								user.firstname,
								grp.code AS service,
								grp.label
						FROM    dims_user user
						INNER JOIN  dims_group_user gu
						ON          user.id = gu.id_user
						INNER JOIN  dims_group grp
						ON          gu.id_group = grp.id
						AND         grp.id = {$commande->fields['id_group']}
						WHERE   user.id = {$commande->fields['id_user']}";
					$rs = $db->query($sql);
					$user_fields = $db->fetchrow($rs);

					$commande_html = "
						<style>
							body
							{
								font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif;
								font-size:10;
								font-weight:none;
								margin: 0px 0px 0px 0px;
							}
						</style>

						<table cellpadding=\"2\" cellspacing=\"1\">
						<tr><td><b>$title</b></td></tr>
						<tr><td>&nbsp;</td></tr>
						<tr><td>N&deg; Commande Internet :</td><td>$id_cmd</td></tr>
						<tr><td>R&eacute;f&eacute;rence de Commande :</td><td>{$commande->fields['libelle']}</td></tr>
						<tr><td>Code Client :</td><td>{$client->fields['CREF']}</td></tr>
						<tr><td>Code Service :</td><td>{$user_fields['service']}</td></tr>
						<tr><td>Nom Client :</td><td>{$client->fields['CNOM']}</td></td>
						<tr><td>Utilisateur :</td><td>{$user_fields['firstname']} {$user_fields['lastname']} ({$user_fields['login']})</td></tr>
						<tr><td>Service :</td><td>{$user_fields['label']}</td></tr>
						<tr><td colspan=\"2\">&nbsp;</td></tr>
						<tr><td>N&deg; Adresse de Livraison :</td><td>$CLNO</td></tr>
						<tr><td VALIGN=\"top\">Adresse de Livraison :</td><td>".html_entity_decode($commande->fields['CRUEL'])."<br>".html_entity_decode($commande->fields['CAUXL'])."<br>{$commande->fields['CPPTLL']}<br>".html_entity_decode($commande->fields['CVILL'])."</td></tr>
						<tr><td>Date :</td><td>$date[3]/$date[2]/$date[1] $date[4]:$date[5]</td></tr>
						<tr><td valign=\"top\">Commentaire 1 :</td><td>". stripslashes(str_replace('\r\n','<br>',$commande->fields['commentaire2'])) ."</td></tr>
						<tr><td valign=\"top\">Commentaire 2 :</td><td>". stripslashes(str_replace('\r\n','<br>',$commande->fields['commentaire'])) ."</td></tr>";
					if (_PLUGIN_AUTOCONNECT) {
						global $a_modes_paiement;
						$commande_html .= "
							<tr><td>Code promo :</td><td>{$commande->fields['code_promo']}</td></tr>
							<tr><td>Mode de paiement :</td><td>{$a_modes_paiement[$mode_paiement]}</td></tr>";
					}
					$commande_html .= "
						</table>
						<br>
						<table cellpadding=\"2\" cellspacing=\"1\" border=\"1\" bgcolor=\"#999999\">
						<tr bgcolor=\"$lcolor\">
							<td><b>Ref Article</b></td>
							<td><b>Description</b></td>
							<td><b>PU</b></td>
							<td><b>Qt&eacute;</b></td>
							<td><b>Total</b></td>
						</tr>";
					$sql = "
						SELECT  cmd.libelle,
								cmd.etat,
								cmd.hors_cata,
								cmd.commentaire,
								article.*,
								cmd_det.*,
								cmd_det_hc.reference AS hc_ref,
								cmd_det_hc.reference AS ref_article,
								cmd_det_hc.designation AS hc_des,
								cmd_det_hc.pu AS hc_pu,
								cmd_det_hc.qte AS hc_qte,
								IF(ISNULL(cmd_det.ref_article),
								cmd_det_hc.reference,
								cmd_det.ref_article) AS refarticle,
								al.label
						FROM    dims_mod_vpc_cmd cmd
						LEFT JOIN   dims_mod_vpc_cmd_detail cmd_det
						ON          cmd.id = cmd_det.id_cmd
						LEFT JOIN   dims_mod_vpc_cmd_detail_hc cmd_det_hc
						ON          cmd.id = cmd_det_hc.id_cmd
						LEFT JOIN   dims_mod_cata_article article
						ON          cmd_det.ref_article = article.reference
						LEFT JOIN   dims_mod_cata_article_lang al
						ON          al.id_article_1 = article.id
						WHERE   cmd.id = $id_cmd
						GROUP BY refarticle";
					$rs = $db->query($sql);

					$articles = array();
					$lcolor = "#eeeeee";
					while ($fields = $db->fetchrow($rs)) {
						($lcolor == "#eeeeee") ? $lcolor = "#ffffff" : $lcolor = "#eeeeee";

						if (!$fields['hors_cata']) {
							$commande_html .= "
								<tr bgcolor=\"$lcolor\">
									<td>&nbsp;{$fields['refarticle']}&nbsp;</td>
									<td>&nbsp;{$fields['label']}&nbsp;</td>
									<td align=\"right\">&nbsp;". catalogue_formateprix($fields['pu']) ."&nbsp;</td>
									<td align=\"right\">&nbsp;{$fields['qte']}&nbsp;</td>
									<td align=\"right\">&nbsp;". catalogue_formateprix(sprintf("%.2f",round($fields['qte'] * $fields['pu'],2))) ."&nbsp;</td>
								</tr>";
							$articles[$fields['refarticle']] = $fields;
						} else {
							$commande_html .= "
								<tr bgcolor=\"$lcolor\">
									<td>&nbsp;{$fields['hc_ref']}&nbsp;</td>
									<td>&nbsp;{$fields['hc_des']}&nbsp;</td>
									<td align=\"right\">&nbsp;". catalogue_formateprix($fields['hc_pu']) ."&nbsp;</td>
									<td align=\"right\">&nbsp;{$fields['hc_qte']}&nbsp;</td>
									<td align=\"right\">&nbsp;". catalogue_formateprix(round($fields['hc_pu'] * $fields['hc_qte'],2)) ."&nbsp;</td>
								</tr>";
							$articles[$fields['hc_ref']] = $fields;
						}
					}

					$commande_html .= "
						<tr bgcolor=\"white\">
							<td colspan=\"5\" align=\"right\">
								Montant HT: <b>".catalogue_formateprix($commande->fields['total_ht'])."</b><br>";
					if (_PLUGIN_AUTOCONNECT) $commande_html .= "Port HT: <b>".catalogue_formateprix($commande->fields['port'])."</b><br>";
					$commande_html .= "
								Montant TVA: <b>".catalogue_formateprix($commande->fields['total_tva'])."</b><br>
								Montant TTC: <b>".catalogue_formateprix($commande->fields['total_ttc'])."</b>
							</td>
						</tr>
						</table>";
					$commande_html_client = $commande_html ."
						<br><br>
						<table>
						<tr>
							<td>Nous vous remercions pour votre commande sur notre site internet.</td>
						</tr>
						</table>";

					//-----------------------------
					// Envoi d'un mail a l'adherent
					//-----------------------------
					$from[0]['name'] = '';
					$from[0]['address'] = $oCatalogue->getParams('notif_send_mail');

					$subject = _MAIL_SUBJECT;
					if (($commande->fields['hors_cata'] || $oCatalogue->getParams('active_notif_mail')) && $oCatalogue->getParams('notif_send_mail') != '') {
						$to = array();
						$adresses = explode(',', $oCatalogue->getParams('notif_send_mail'));
						foreach ($adresses as $id_adr => $addr_mail) {
							$to[$id_adr]['name'] = "";
							$to[$id_adr]['address'] = $addr_mail;
						}

						dims_send_mail($from, $to, $subject, $commande_html, null, $bcc);

						$commande->fields['mail'] = 1;
						$commande->save_lite();
					}

					//--------------------------
					// Envoi d'un mail au client
					//--------------------------
					$from[0]['name'] = '';
					$from[0]['address'] = $oCatalogue->getParams('notif_send_mail');

					$subject = _MAIL_SUBJECT;
					if ($oCatalogue->getParams('active_notif_mail')) {
						include_once './modules/system/class_user.php';
						$user = new user();
						$user->open($_SESSION['dims']['userid']);
						if (trim($user->fields['email']) != '' && trim($user->fields['email']) != 'null' && trim($user->fields['email']) != 'NULL') {
							$to = array();
							$to[0]['name'] = "{$user->fields['firstname']} {$user->fields['lastname']}";
							$to[0]['address'] = $user->fields['email'];

							dims_send_mail($from, $to, "Confirmation de votre commande N°$id_cmd", $commande_html_client);
						}

						// Si le resp des achats valide la cmd de qqn, on envoie un mail a cette personne aussi
						if ($_SESSION['dims']['userid'] != $commande->fields['id_user']) {
							$user = new user();
							$user->open($commande->fields['id_user']);
							if (trim($user->fields['email']) != '' && trim($user->fields['email']) != 'null' && trim($user->fields['email']) != 'NULL') {
								$to = array();
								$to[0]['name'] = "{$user->fields['firstname']} {$user->fields['lastname']}";
								$to[0]['address'] = $user->fields['email'];

								dims_send_mail($from, $to, "Confirmation de votre commande N°$id_cmd", $commande_html_client);
							}
						}
					}

					//-----------------------------------------
					// Ecriture du fichier texte de la commande
					//-----------------------------------------
					if (!is_dir(_CATA_CMD_DIR)) {
						dims_makedir(_CATA_CMD_DIR);
					}
					//die();
					if (!$commande->fields['hors_cata']) {
						$nblignes_th = 0; // Nb de lignes theorique

						$filename = get_cmdfilename($id_cmd);
						if (!file_exists(_CATA_CMD_DIR . $filename)) {
							$size_gen = 0;
							if (!is_dir(_CATA_CMD_DIR."temp/")) {
								dims_makedir(_CATA_CMD_DIR."temp/");
							}

							$cmdfile = _CATA_CMD_DIR ."temp/$filename";

							write_cmd_file($id_cmd, $client, $adr, $sel_adr, $commande->fields['libelle'], $nblignes_th, $commentaire, $articles, $cmdfile, $commande, $user_fields);

							// On verifie que le fichier compte bien le bon nombre de lignes
							// pour s'assurer que toutes les informations sont bien dedans
							$nblignes_reel = 0;
							if ($nblignes_th && file_exists($cmdfile)) {
								$fp = fopen($cmdfile, "r");
								while (!feof($fp)) {
									$buffer = fgets($fp,4096);
									$nblignes_reel++;
								}
								fclose($fp);
								$nblignes_reel--;

								if ($nblignes_th == $nblignes_reel) {
									rename($cmdfile, _CATA_CMD_DIR . $filename);
									$size_gen = filesize(_CATA_CMD_DIR . $filename);
								} else {
									// Envoi d'un mail pour prevenir que le fichier n'est pas correct
									$subject = 'Commande incorrecte sur le site internet';
									$message = "La commande $id_cmd ne s'est pas enregistr&eacute;e correctement.\nV&eacute;rifiez cette commande et reg&eacute;n&eacute;rez le fichier.";
									if ($oCatalogue->getParams('cata_send_mail')) {
										dims_send_mail($mail_from, $mail_incident, $subject, $message);
									}
								}
							}
							else {

								// Envoi d'un mail pour prevenir que le fichier n'est pas correct
								$subject = 'Erreur d\'ecriture de commande sur le site internet';
								$message = "La commande $id_cmd ne s'est pas enregistr&eacute;e correctement.\nV&eacute;rifiez cette commande et reg&eacute;n&eacute;rez le fichier.";
								$message .= $cmdfile;

								if ($oCatalogue->getParams('cata_send_mail')) {
									dims_send_mail($mail_from, $mail_incident, $subject, $message);
								}
							}

							$commande->fields['date_gen'] = dims_createtimestamp();
							$commande->fields['nblignes_th'] = $nblignes_th;
							$commande->fields['nblignes_gen'] = $nblignes_reel;
							$commande->fields['size_gen'] = $size_gen;
							$commande->save_lite();
						}
					}
					$msg = 0;

					// On met a jour le nb de commandes a valider
					$_SESSION['catalogue']['nb_cmd_val']--;
				}
			} else {
				$msg = 4;
			}
		} elseif($_SESSION['session_adminlevel'] == _DIMS_ID_LEVEL_SERVICERESP) { // Si on est responsable de service
			// On verifie qu'il y a bien un responsable des achats
			if ($_SESSION['catalogue']['achat_id'] == -1) {
				$msg = 5; // Si pas de responsable des achats
			} else {
				// On enregistre
				$commande->fields['etat'] = 'en_cours2';
				$commande->fields['date_validation'] = dims_createtimestamp();
				$commande->fields['mail'] = 0;

				// Envoi du mail
				$user = new user();
				$user->open($_SESSION['catalogue']['achat_id']);

				if (trim($user->fields['email']) != '' && trim($user->fields['email']) != 'null' && trim($user->fields['email']) != 'NULL') {
					$to[0]['name'] = $user->fields['firstname'] ." ". $user->fields['lastname'];
					$to[0]['address'] = $user->fields['email'];
					$login = $user->fields['login'];
					$passwd = $user->fields['password'];

					$cmd = new commande();
					$cmd->open($id_cmd);

					if ($cmd->fields['id_user'] != '') {
						$user = new user();
						$user->open($cmd->fields['id_user']);
						$from[0]['name'] = $user->fields['firstname'] ." ". $user->fields['lastname'];
						$from[0]['address'] = $user->fields['email'];

						$subject = "Demande de validation de commande";

						$message = "{$from[0]['name']} a cr&eacute;&eacute; la commande num&eacute;ro $id_cmd";
						if ($cmd->fields['libelle'] != '') {
							$message .= " intitul&eacute;e '{$cmd->fields['libelle']}'";
						}
						$message .= " qui attend votre validation.";
						$message .= '<br/><br/>Pour visualiser cette commande, cliquez <a href="'.dims_urlencode($dims->getProtocol().$_SERVER['HTTP_HOST'].'/index.php?op=commandes&id_cmd='.$id_cmd.'&dims_login='.$login.'&dims_password='.$passwd.'&dims_pwdmd5=1').'#'.$id_cmd.'">ici</a>.<br/><br/>';

						if ($oCatalogue->getParams('cata_send_mail')) {
							dims_send_mail($from, $to, $subject, $message);
						}
					}
				}
				$msg = 2;

				$commande->save_lite();

				// On met a jour le nb de commandes a valider
				$_SESSION['catalogue']['nb_cmd_val']--;
			}
		} elseif($_SESSION['session_adminlevel'] == _DIMS_ID_LEVEL_USER) { // Si on est responsable de rien du tout
			// On verifie qu'il y a bien un responsable des achats
			if ($_SESSION['catalogue']['achat_id'] == -1) {
				$msg = 5; // Si pas de responsable des achats
			} else {
				// On enregistre
				$commande->fields['etat'] = 'en_cours1';
				$commande->fields['date_validation'] = dims_createtimestamp();

				// Envoi du mail
				$user = new user();
				if ($_SESSION['catalogue']['service_id'] != -1) {
					$user->open($_SESSION['catalogue']['service_id']);
					$msg = 1;
				} else {
					$user->open($_SESSION['catalogue']['achat_id']);
					$msg = 2;
					$commande->fields['etat'] = 'en_cours2';
				}

				// Commandes classiques
				if ( !($commande->fields['exceptionnelle'] && $commande->fields['hors_cata']) ) {
					if (trim($user->fields['email']) != '' && trim($user->fields['email']) != 'null' && trim($user->fields['email']) != 'NULL') {
						$to[0]['name'] = $user->fields['firstname'] ." ". $user->fields['lastname'];
						$to[0]['address'] = $user->fields['email'];
						$login = $user->fields['login'];
						$passwd = $user->fields['password'];

						$cmd = new commande();
						$cmd->open($id_cmd);

						if ($cmd->fields['id_user'] != '') {
							$user = new user();
							$user->open($cmd->fields['id_user']);
							$from[0]['name'] = $user->fields['firstname'] ." ". $user->fields['lastname'];
							$from[0]['address'] = $user->fields['email'];

							$subject = "Demande de validation de commande";

							$message = "{$from[0]['name']} a cr&eacute;&eacute; la commande num&eacute;ro $id_cmd";
							if ($cmd->fields['libelle'] != '') {
								$message .= " intitul&eacute;e '{$cmd->fields['libelle']}'";
							}
							$message .= " qui attend votre validation.";
							$message .= '<br/><br/>Pour visualiser cette commande, cliquez <a href="'.dims_urlencode($dims->getProtocol().$_SERVER['HTTP_HOST'].'/index.php?op=commandes&id_cmd='.$id_cmd.'&dims_login='.$login.'&dims_password='.$passwd.'&dims_pwdmd5=1').'#'.$id_cmd.'">ici</a>.<br/><br/>';
							//$message .= '<br/><br/>Pour visualiser cette commande, cliquez <a href="http://'.$_SERVER['HTTP_HOST'].'/p35.html">ici</a>.<br/><br/>';

							if ($oCatalogue->getParams('cata_send_mail')) {
								dims_send_mail($from, $to, $subject, $message);
								$commande->fields['mail'] = 0;
							}
						}
					}
				}
				else {
					// Commandes exceptionnelles
					$adresses = explode(',', $oCatalogue->getParams('notif_send_mail'));
					foreach ($adresses as $id_adr => $addr_mail) {
						$to[$id_adr]['name'] = '';
						$to[$id_adr]['address'] = $addr_mail;
					}

					$cmd = new commande();
					$cmd->open($id_cmd);

					if ($cmd->fields['id_user'] != '') {
						$user = new user();
						$user->open($cmd->fields['id_user']);
						$from[0]['name'] = $user->fields['firstname'] ." ". $user->fields['lastname'];
						$from[0]['address'] = $user->fields['email'];

						$subject = "Demande de validation de commande";

						$message = "{$from[0]['name']} ({$commande->fields['ref_client']}) a cr&eacute;&eacute; la commande num&eacute;ro $id_cmd";
						if ($cmd->fields['libelle'] != '') {
							$message .= " intitul&eacute;e '{$cmd->fields['libelle']}'";
						}
						$message .= ' qui attend votre validation.';

						if ($oCatalogue->getParams('cata_send_mail')) {
							dims_send_mail($from, $to, $subject, $message);
							$commande->fields['mail'] = 0;
						}
					}
				}

				$commande->save_lite();

				// On met a jour le nb de commandes a valider
				$_SESSION['catalogue']['nb_cmd_val']--;
			}
		}
	}
} else {
	$msg = 3;
}

dims_redirect("/index.php?op=confirm_cmd&id_cmd=$id_cmd&msg=$msg");
