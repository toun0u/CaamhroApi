<?php
if ($_SESSION['dims']['connected'] && $oCatalogue->getParams('exceptional_orders')) {
	$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, true);

	switch ($action) {
		default:
			ob_start();

			if (isset($_SESSION['catalogue']['horscata']) && sizeof($_SESSION['catalogue']['horscata'])) {
				$cmd_libelle = $_SESSION['catalogue']['horscata']['cmd_libelle'];
				$commentaire = $_SESSION['catalogue']['horscata']['commentaire'];
				$refqte = $_SESSION['catalogue']['horscata']['refqte'];

				unset($_SESSION['catalogue']['horscata']);
			} else {
				$cmd_libelle = "";
				$commentaire = "";

				for ($i = 0; $i < 10; $i++) {
					$refqte[$i] = array();
					for ($j = 0; $j < 4; $j++) {
						$refqte[$i][$j] = "";
					}
				}
			}
			?>

			<table width="100%" cellpadding="0" cellspacing="0">
				<tr bgcolor="#E8EEFF">
					<td>
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
		                        <td colspan="0" class="image_panier"><img border="0" src="/modules/catalogue/img/hors_catalogue.png" alt="<?php echo _LABEL_HORSCATALOGUE; ?>"></td>
								<td class="WebNavTitle">&nbsp;<?php echo _LABEL_HORSCATALOGUE; ?></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr bgcolor="#dddddd" height="1"><td></td></tr>
				<tr>
					<td width="772" align="center" valign="top">
						<table cellpadding="6" cellspacing="0" width="100%">
							<tr>
								<td>
									<form name="form" method="post" onSubmit="javascript: if(document.form.op.value='') return false; else return true;">
									<input type="hidden" name="op" value="hors_catalogue" />
									<input type="hidden" name="action" value="" />

									<table cellpadding="6" cellspacing="0" width="100%">
										<tr>
											<td valign="top">
												<table cellpadding="0" cellspacing="0">
													<tr><td colspan="9" height="1" bgcolor="#dddddd"></td></tr>
													<tr>
														<td bgcolor="#f8f8f8" width="10"></td>
														<td bgcolor="#f8f8f8" width="50" align="center"><b>Référence</b></td>
														<td bgcolor="#f8f8f8" width="10"></td>
														<td bgcolor="#f8f8f8" width="50" align="center"><b>Désignation</b></td>
														<td bgcolor="#f8f8f8" width="10"></td>
														<?
														if ($_SESSION['catalogue']['afficher_prix'] && _SHOW_PRICES) {
															?>
															<td bgcolor="#f8f8f8" width="50" align="center"><b>PU</b></td>
															<td bgcolor="#f8f8f8" width="10"></td>
															<?
														}
														?>
														<td bgcolor="#f8f8f8" width="50" align="center"><b>Quantité</b></td>
														<td bgcolor="#f8f8f8" width="10"></td>
													</tr>
													<?
													for ($i = 0; $i < 10; $i++) {
														?>
														<tr><td colspan="9" height="1" bgcolor="#dddddd"></td></tr>
														<tr height="25">
															<td bgcolor="#f8f8f8" width="10"></td>
															<td bgcolor="#f8f8f8" width="40" align="center"><input id="<? echo ($i * 4 + 1); ?>" class="WebText" type="text" name="refqte[<? echo $i; ?>][0]" value="<? echo $refqte[$i][0]; ?>"></td>
															<td bgcolor="#f8f8f8" width="10"></td>
															<td bgcolor="#f8f8f8" width="40" align="center"><input id="<? echo ($i * 4 + 2); ?>" class="WebText" type="text" name="refqte[<? echo $i; ?>][1]" value="<? echo $refqte[$i][1]; ?>"></td>
															<td bgcolor="#f8f8f8" width="10"></td>
															<?
															if ($_SESSION['catalogue']['afficher_prix'] && _SHOW_PRICES) {
																?>
																<td bgcolor="#f8f8f8" width="40" align="center"><input id="<? echo ($i * 4 + 3); ?>" class="WebInput" type="text" name="refqte[<? echo $i; ?>][2]" size="5" value="<? echo $refqte[$i][2]; ?>"></td>
																<td bgcolor="#f8f8f8" width="10"></td>
																<?
															}
															?>
															<td bgcolor="#f8f8f8" width="40" align="center"><input id="<? echo ($i * 4 + 4); ?>" class="WebInput" type="text" name="refqte[<? echo $i; ?>][3]" size="5" value="<? echo $refqte[$i][3]; ?>"></td>
															<td bgcolor="#f8f8f8" width="10"></td>
														</tr>
														<?
													}
													?>
													<tr><td colspan="9" height="1" bgcolor="#dddddd"></td></tr>
												</table>
											</td>
										</tr>
									</table>
									</form>
								</td>
								<td valign="top">
									<table cellpadding="0" cellspacing="0">
										<tr>
											<td colspan="2">
												Ce formulaire vous permet de commander des articles<br>qui ne figurent pas sur le catalogue.<br><br>
												<font color="red">Attention, la saisie hors catalogue fait l'objet<br>d'une commande spéciale.</font><br><br><br><br>
											</td>
										</tr>
										<tr height="32">
											<td align="center">
												<a href="javascript:void(0);" onclick="javascript:document.form.action.value='valider_commande_hc';document.form.submit();">
													<img src="<?= $template_web_path; ?>gfx/bt_commander.png" />
												</a>
											</td>
										</tr>
										<?
										if (isset($ok)) {
											?>
											<td colspan="2">&nbsp;</td></tr><tr><td colspan="2" height="1" bgcolor="#dddddd"></td></tr>
											<?
											if($ok) echo "<tr><tr height=\"25\" bgcolor=\"f8f8f8\"><td colspan=\"2\">&nbsp;Votre commande a été enregistrée<br>&nbsp;et sera prochainement prise en compte.</td></tr><tr><td colspan=\"2\" height=\"1\" bgcolor=\"#dddddd\"></td></tr>";
											else echo "<tr height=\"25\" bgcolor=\"f8f8f8\"><td colspan=\"2\">&nbsp;Une erreur est survenue lors de l'enregistrement de votre commande.</td></tr><tr><td colspan=\"2\" height=\"1\" bgcolor=\"#dddddd\"></td></tr>";
										}

										if (isset($_SESSION['catalogue']['errors']) && count($_SESSION['catalogue']['errors'])) {
											?>
											<td colspan="2">&nbsp;</td></tr>
											<tr><td colspan="2" align="center"><b>Erreur de saisie !</b></td></tr>
											<tr><td colspan="2" height="1" bgcolor="#dddddd"></td></tr>
											<?
											foreach ($_SESSION['catalogue']['errors'] as $ref => $msg) {
												echo "
													<tr height=\"25\" bgcolor=\"f8f8f8\">
														<td align=\"right\"><b>$ref&nbsp;:&nbsp;</b></td>
														<td>$msg</td>
													</tr>
													<tr><td colspan=\"2\" height=\"1\" bgcolor=\"#dddddd\"></td></tr>";
												unset($_SESSION['catalogue']['errors'][$ref]);
											}
											?>
											</table>
											</td>
											<?
										}
										unset($_SESSION['catalogue']['errors']);
										?>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<?php
			if (!empty($_SESSION['dims']['connected'])) {
				?>
				<div id="raccourci">
					<table width="100%" cellpadding="20" cellspacing="0">
						<tr>
							<td id="espace_raccourci">
								<a href="/index.php?op=saisierapide"><img border="0" alt="Saisie rapide" src="/modules/catalogue/img/saisie_rapide.png" /><p>&nbsp;Saisie rapide</p></a>
							</td>
							<td id="espace_raccourci">
								<a href="/index.php?op=panier"><img border="0" alt="Panier" src="/modules/catalogue/img/panier.png" /><p>&nbsp;Panier</p></a>
							</td>
							<?php if ($oCatalogue->getParams('panier_type')): ?>
							<td id="espace_raccourci">
								<a href="/index.php?op=panierstype"><img border="0" alt="Paniers types" src="/modules/catalogue/img/paniers_types.png" /><p>&nbsp;Paniers types / Listes scolaire</p></a>
							</td>
							<?php endif ?>
							<td id="espace_raccourci">
								<a href="/index.php?op=commandes"><img border="0" alt="Commandes" src="/modules/catalogue/img/commandes.png" /><p>&nbsp;Commandes en cours</p></a>
							</td>
							<td id="espace_raccourci">
								<a href="/index.php?op=historique"><img border="0" alt="Historique" src="/modules/catalogue/img/historique.png" /><p>&nbsp;Historique</p></a>
							</td>
						</tr>
					</table>
				</div>
				<?php
			}

			if (isset($_SESSION['catalogue']['horscata']) && sizeof($_SESSION['catalogue']['horscata'])) unset($_SESSION['catalogue']['horscata']);
			?>

			<script language="JavaScript">
				var ie4= (navigator.appName == "Microsoft Internet Explorer")?1:0;
				var ns4= (navigator.appName=="Netscape")?1:0;
				eventSetup();

				function eventSetup() {
					if (ie4) {
						document.onkeydown = Ienterevent;
					}
					if (ns4) {
						document.captureEvents( Event.KEYDOWN );
						document.onkeydown = Nenterevent;
					}
				}

				function Ienterevent() {
					(window.Event) ? n=e.which : n=event.keyCode;
					if (n == 13) {
						var nextId = Number(event.srcElement.id) + 1;
						if(document.getElementById(event.srcElement.id).value != '') document.getElementById(nextId).focus();
					}
				}

				function Nenterevent(e) {
					if (e.keyCode == 13) {
						var nextId = Number(e.target.id) + 1;
						if(document.getElementById(e.target.id).value != '') document.getElementById(nextId).focus();
					}
				}

				function verifLength(field,maxLength) {
					if (field.value.length > maxLength) {
						field.value = field.value.substring(0,maxLength + 1);
					}
				}

				document.getElementsByTagName("input")[4].focus();
			</script>
			<?php
			$smarty->assign('catalogue', array('CONTENT' => ob_get_contents()));

		    $page['TITLE'] = 'Commandes Exceptionnelles ';
		    $page['META_DESCRIPTION'] = 'Votre interface personnelle de gestion de votre compte';
		    $page['META_KEYWORDS'] = 'compte, gestion, personnelle';
		    $page['CONTENT'] = '';

			ob_end_clean();
			break;
		case 'valider_commande_hc':

			$refqte = dims_load_securvalue('refqte', dims_const::_DIMS_CHAR_INPUT, false, true);
			$cmd_libelle = dims_load_securvalue('cmd_libelle', dims_const::_DIMS_CHAR_INPUT, false, true);

			$_SESSION['catalogue']['errors'] = array();

			if (!empty($refqte)) {
				$ensrefqte = array();

				if ($_SESSION['catalogue']['afficher_prix'] && _SHOW_PRICES) {
					$i = 0;
					foreach ($refqte as $key => $value) {

						$ref = trim($value[0]);
						$des = trim($value[1]);
						$pu = str_replace(',','.',trim($value[2]));
						$qte = trim($value[3]);

						if ($ref != '') {
							if ($qte != '') {
								if ($des != '') {
									if (is_numeric($qte)) {
										if ($qte > 0) {
											$ensrefqte[$i]['reference'] = $ref;
											$ensrefqte[$i]['designation'] = $des;
											$ensrefqte[$i]['pu'] = $pu;
											$ensrefqte[$i]['qte'] = $qte;
										}
										else {
											$_SESSION['catalogue']['errors']["'$ref'"] = "La quantité doit être positive.";
										}
									}
									else {
										$_SESSION['catalogue']['errors']["'$ref'"] = "La quantité doit être un nombre.";
									}
								}
								else {
									$_SESSION['catalogue']['errors']["'$ref'"] = "Vous devez spécifier une désignation.";
								}
							}
							else {
								$_SESSION['catalogue']['errors']["'$ref'"] = "Vous devez spécifier une quantité.";
							}
						}

						$i++;
					}
				}
				else {
					$i = 0;
					foreach ($refqte as $key => $value) {
						$ref = trim($value[0]);
						$des = trim($value[1]);
						$qte = trim($value[3]);

						if ($ref != '') {
							if ($qte != '') {
								if ($des != '') {
									if (is_numeric($qte)) {
										if ($qte > 0) {
											$ensrefqte[$i]['reference'] = $ref;
											$ensrefqte[$i]['designation'] = $des;
											$ensrefqte[$i]['qte'] = $qte;
										}
										else {
											$_SESSION['catalogue']['errors']["'$ref'"] = "La quantité doit être positive.";
										}
									}
									else {
										$_SESSION['catalogue']['errors']["'$ref'"] = "La quantité doit être un nombre.";
									}
								}
								else {
									$_SESSION['catalogue']['errors']["'$ref'"] = "Vous devez spécifier une désignation.";
								}
							}
							else {
								$_SESSION['catalogue']['errors']["'$ref'"] = "Vous devez spécifier une quantité.";
							}
						}
						$i++;
					}
				}

				// si rien de saisi, on renvoie sur la page
				if (!sizeof($ensrefqte)) {
					dims_redirect($dims->getScriptEnv()."?op=hors_catalogue");
				}

				if (sizeof($ensrefqte) && !sizeof($_SESSION['catalogue']['errors'])) {
					require DIMS_APP_PATH.'modules/catalogue/include/class_commande.php';
					$cde = new commande();
					$cde->setArticles($ensrefqte);
					$cde->fields['id_client'] = $_SESSION['catalogue']['client_id'];
					$cde->fields['code_client'] = $_SESSION['catalogue']['code_client'];
					$cde->fields['hors_cata'] = 1;
					$cde->fields['date_cree'] = dims_createtimestamp();
					$cde->fields['date_validation'] = dims_createtimestamp();
					$cde->fields['cli_email'] = $_SESSION['dims']['user']['email'];
					$cde->fields['mode_paiement'] = 'CPT';
					$cde->fields['user_name'] = $_SESSION['dims']['user']['firstname'].' '.$_SESSION['dims']['user']['lastname'];
					$cde->save();

					// On met a jour le nb de commandes a valider
					$_SESSION['catalogue']['nb_cmd_val']++;
				}

				if (sizeof($_SESSION['catalogue']['errors'])) {
					$_SESSION['catalogue']['horscata']['cmd_libelle'] = $cmd_libelle;
					$_SESSION['catalogue']['horscata']['commentaire'] = $commentaire;
					$_SESSION['catalogue']['horscata']['refqte'] = $refqte;
					dims_redirect($dims->getScriptEnv()."?op=hors_catalogue&ok=0");
				}
				else {
					unset($_SESSION['catalogue']['errors']);
					dims_redirect($dims->getScriptEnv()."?op=valider_commande&id_cmd=".$cde->get('id'));
				}
			}
			else {
				dims_redirect($dims->getScriptEnv()."?op=hors_catalogue&ok=0");
			}
			break;
	}

}
else {
	dims_redirect($dims->getScriptEnv().'?op=compte');
}
