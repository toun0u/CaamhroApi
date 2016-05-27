<?php
ob_start();

$libelle = '';

$user = new user();
$user->open($_SESSION['dims']['userid']);
$groups = $user->getgroups(true);

//recherche des groupes enfants
$group = new group();
$group->open(key($groups));
$lstgroups = implode(',', array_merge($groups, $group->getgroupchildrenlite()));

$id_cmd = dims_load_securvalue('id_cmd', dims_const::_DIMS_NUM_INPUT, true, false);
echo $sql = "
	SELECT	cmd.libelle,
			cmd.etat,
			cmd.hors_cata,
			article.*,
			cmd_det.*,
			cmd_det_hc.reference AS hc_ref,
			cmd_det_hc.designation AS hc_des,
			cmd_det_hc.pu AS hc_pu,
			cmd_det_hc.qte AS hc_qte,
			cmd.commentaire,
			cmd.id_user,
			cmd.id_group,
			cmd.CLNO,
			cmd.CNOML,
			cmd.CRUEL,
			cmd.CAUXL,
			cmd.CPPTLL,
			cmd.CVILL,
			cli.CVARB1 AS mode_paiement

	FROM	dims_mod_vpc_cmd cmd

	INNER JOIN	dims_mod_vpc_client cli
	ON			cli.CREF = cmd.ref_client

	LEFT JOIN	dims_mod_vpc_cmd_detail cmd_det
	ON			cmd.id = cmd_det.id_cmd

	LEFT JOIN	dims_mod_vpc_cmd_detail_hc cmd_det_hc
	ON			cmd.id = cmd_det_hc.id_cmd

	LEFT JOIN	dims_mod_cata_article article
	ON			cmd_det.ref_article = article.reference

	WHERE	cmd.id = $id_cmd
	AND		cmd.id_group IN ($lstgroups)
	AND		cmd.etat <> 'validee'";
$rs = $db->query($sql);
$articles = array();
while ($fields = $db->fetchrow($rs)) {
	// On ne valide pas une commande sans adresse de livraison
	if (trim($fields['CPPTLL'] == '')) dims_redirect("/index.php?op=valider_commande&id_cmd=$id_cmd&err=3");

	//$adr['CLNO'] = stripslashes($fields['CLNO']);
	$adr['CNOML'] = html_entity_decode($fields['CNOML']);
	$adr['CRUEL'] = html_entity_decode($fields['CRUEL']);
	$adr['CAUXL'] = html_entity_decode($fields['CAUXL']);
	$adr['CPPTLL'] = html_entity_decode($fields['CPPTLL']);
	$adr['CVILL'] = html_entity_decode($fields['CVILL']);

	$mode_paiement = $fields['mode_paiement'];

	if (!$fields['hors_cata']) {
		$hors_cata = 0;
		$articles[$fields['reference']] = $fields;
		$libelle = $fields['libelle'];
		$etat = $fields['etat'];
	}
	else {
		$hors_cata = 1;
		$articles[] = $fields;
		$libelle = $fields['libelle'];
		$etat = $fields['etat'];
	}

	$id_user = $fields['id_user'];
	$id_group = $fields['id_group'];

	dims_print_r($fields);
}

dims_print_r($id_user);
die();

// recherche le credit dispo
// On regarde le credit de la personne qui a passe la commande
if ($id_user != $_SESSION['dims']['userid']) {
	$budget = catalogue_getbudget_user($id_user, $id_group);
}
else {
	catalogue_getbudget();
	$budget = $_SESSION['catalogue']['budget'];
}

switch ($etat) {
	case 'en_cours':
	case 'refusee':
		$modifiable = true;
		break;
	case 'en_cours1':
		$modifiable = ($_SESSION['session_adminlevel'] >= _DIMS_ID_LEVEL_USER) ? true : false;
		break;
	case 'en_cours2':
		$modifiable = ($_SESSION['session_adminlevel'] >= _DIMS_ID_LEVEL_SERVICERESP) ? true : false;
		break;
}

// Calcul des frais de port
get_fraisport(76, $adr['CPPTLL']);

if ($modifiable) {
	?>

	<table width="100%" cellpadding="0" cellspacing="0">
		<tr bgcolor="#dddddd" height="1"><td></td></tr>
		<tr bgcolor="#eeeeee">
			<td width="100%" align="center">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td align="center">
						<table>
						<tr>
							<td class="WebNavTitlepuce" rowspan="2"><img src="/modules/catalogue/img/front_24.png" alt="Etape 1" border="0"></td>
							<td class="WebNavTitlepuce"><a class="WebNavTitle" href="/index.php?op=valider_commande&id_cmd=<?=$id_cmd;?>">Etape 1</a></td>
						</tr>
						<tr>
							<td><a class="stdNb" href="/index.php?op=valider_commande&id_cmd=<?=$id_cmd;?>">&nbsp;R&eacute;capitulatif</a></td>
						</tr>
						</table>
					</td>
					<td align="center">
						<table>
						<tr>
							<td class="WebNavTitlepuce" rowspan="2"><img src="/modules/catalogue/img/front_24.png" alt="Etape 2" border="0"></td>
							<td class="WebNavTitlepuce"><a class="WebNavTitle" href="/index.php?op=valider_commande&id_cmd=<?=$id_cmd;?>&etape=2">Etape 2</a></td>
						</tr>
						<tr>
							<td><a class="stdNb" href="/index.php?op=valider_commande&id_cmd=<?=$id_cmd;?>&etape=2">&nbsp;Livraison</a></td>
						</tr>
						</table>
					</td>
					<td align="center">
						<table>
						<tr>
							<td class="WebNavTitlepuce" rowspan="2"><img src="/modules/catalogue/img/front_24.png" alt="Etape 3" border="0"></td>
							<td class="WebNavTitlepuce"><a class="WebNavTitle" href="/index.php?op=valider_commande&id_cmd=<?=$id_cmd;?>&etape=3">Etape 3</a></td>
						</tr>
						<tr>
							<td><a class="stdNb" href="/index.php?op=valider_commande&id_cmd=<?=$id_cmd;?>&etape=3">&nbsp;Commentaire</a></td>
						</tr>
						</table>
					</td>
					<td align="center">
						<table>
						<tr>
							<td class="WebNavTitlepuce" rowspan="2"><img src="/modules/catalogue/img/front_24.png" alt="Etape 4" border="0"></td>
							<td class="WebNavTitlepuce">Etape 4</td>
						</tr>
						<tr>
							<td>&nbsp;Mode de paiement</td>
						</tr>
						</table>
					</td>
					<td align="center">
						<table>
						<tr>
							<td class="WebNavTitle" rowspan="2"><img src="/modules/catalogue/img/front_24_nb.png" alt="Etape 5" border="0"></td>
							<td class="WebNavTitleNb">Etape 5</td>
						</tr>
						<tr>
							<td class="stdNb">&nbsp;Confirmation</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr bgcolor="#dddddd" height="1"><td></td></tr>
		<tr>
			<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td class="WebNavTitlepuce">&nbsp;&nbsp;Etape 4 : Frais de port et mode de r&egrave;glement</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td width="100%" align="left" valign="top">
				<br/>
				<table cellpadding="6" cellspacing="0" width="100%">
					<tr>
						<td>
							<table width="100%">
								<tr>
									<td>
										<?php
										// Mise a jour des totaux dans la commande
										$total_commande = 0;
										$a_total_tva = array();

										if (!$hors_cata) {
											foreach ($articles as $detail) {
												$article = new article();
												$article->fields = $detail;
												$prix = catalogue_getprixarticle($article, $detail['qte']);
												$total = sprintf("%.2f", round($detail['qte'] * $prix, 2));
												$prix = sprintf("%.2f", round($prix, 2));
												$total_commande += $total;

												if (!isset($a_total_tva[$article->fields['ctva']])) $a_total_tva[$article->fields['ctva']] = 0;
												$a_total_tva[$article->fields['ctva']] += $total * $a_tva[$article->fields['ctva']] / 100;
											}
										} else {
											foreach ($articles as $detail) {
												$total = sprintf("%.2f", round($detail['hc_qte'] * $detail['hc_pu'] ,2));
												$prix = sprintf("%.2f", round($detail['hc_pu'] ,2));
												$total_commande += $total;
											}
										}

										$total_commande = round($total_commande, 2);
										$fp_montant = ($total_commande >= $_SESSION['catalogue']['frais_port']['fp_franco']) ? 0 : $_SESSION['catalogue']['frais_port']['fp_montant'];
										$total_commande_port = $total_commande + $fp_montant*1.196;

										$tva = 0;
										foreach ($a_total_tva as $totaltva) {
											$tva += $totaltva;
										}
										$tva = round($tva, 2);
										$total_commande_ttc = round($total_commande_port + $tva, 2);

										// Enregistrement de la commande
										$db->query("
											UPDATE dims_mod_vpc_cmd
											SET	total_ht = $total_commande,
												port = '$fp_montant',
												total_tva = $tva,
												total_ttc = $total_commande_ttc
											WHERE id = $id_cmd");

										$total_commande_disp = catalogue_formateprix($total_commande);
										$port_disp = catalogue_formateprix($fp_montant);
										$tva_disp = catalogue_formateprix($tva);
										$total_commande_ttc_disp = catalogue_formateprix($total_commande_ttc);
										?>
										<form name="form" action="<?=$scriptenv;?>" method="post">
										<input type="hidden" name="op" value="confirmer_commande" />
										<input type="hidden" name="id_cmd" value="<?=$id_cmd;?>" />

										<?php if ($_SESSION['catalogue']['afficher_prix'] && _SHOW_PRICES): ?>
											<div class="blocJaune">
												<table>
												<tr>
													<td width="24px" align="center"><img src="/modules/catalogue/img/alacarte.png" alt="Frais de port" /></td>
													<td class="WebNavTitlepuce">Frais de port</td>
												</tr>
												</table>

												<table width="100%">
												<tr>
													<td valign="top">
														<table cellpadding="0" cellspacing="0">
														<tr>
															<td colspan="2">
																Cette commande contient <?=sizeof($articles);?> r&eacute;f&eacute;rence(s).<br/>
																Le montant de vos frais de port s'&eacute;l&egrave;ve &agrave; <strong><?=$port_disp;?> &euro; HT</strong>.
																<?php if ($fp_montant > 0): ?>
																<br/><br/>Pour ne pas payer de frais de port, il vous reste <strong><?=catalogue_formateprix($_SESSION['catalogue']['frais_port']['fp_franco'] - $total_commande);?> &euro; HT</strong> de produits &agrave; commander.
																<?php endif; ?>
															</td>
														</tr>
														</table>
													</td>
													<td align="right" valign="top">
														<table cellpadding="0" cellspacing="0">
														<tr>
															<td align="right"><b>Total HT:&nbsp;</b></td>
															<td><b><?=$total_commande_disp;?>&nbsp;&euro;&nbsp;</b></td>
														</tr>
														<tr>
															<td align="right">Port HT:&nbsp;</td>
															<td><?=$port_disp;?>&nbsp;&euro;&nbsp;</td>
														</tr>
														<tr>
															<td align="right">TVA:&nbsp;</td>
															<td><?=$tva_disp;?>&nbsp;&euro;&nbsp;</td>
														</tr>
														<tr>
															<td align="right"><b>Total TTC:&nbsp;</b></td>
															<td><b><?=$total_commande_ttc_disp;?>&nbsp;&euro;&nbsp;</b></td>
														</tr>
														</table>
													</td>
												</tr>
												</table>
											</div>
										<?php endif; ?>

										<br/><br/>
										<div class="blocBleu">
											<table>
											<tr>
												<td width="24px" align="center"><img src="/modules/catalogue/img/imprimer.png" alt="Impression de la commande" /></td>
												<td class="WebNavTitlepuce">Impression de la commande</td>
											</tr>
											</table>

											<table>
											<tr>
												<td>Edition de votre commande avant validation :&nbsp;</td>
												<td><?=catalogue_makegfxbutton(_LABEL_PDF_BUTTON,'<img src="'.$template_path.'/gfx/pdf.gif">',"document.form.op.value='imprimer_commande'; document.form.submit();",'*');?></td>
											</tr>
											</table>
										</div>

										<?php if (!isset($_SESSION['catalogue']['params']['nocodepromo'])): ?>
										<br/><br/>
										<div class="blocOrange">
											<table>
											<tr>
												<td width="24px" align="center"><img src="/modules/catalogue/img/reduction.png" alt="Code promo" /></td>
												<td class="WebNavTitlepuce">Code promo</td>
											</tr>
											</table>

											<table>
											<tr>
												<td>
													Si vous avez un code promo, saisissez-le ici : <input class="WebText" type="text" name="code_promo" maxlength="255" />
												</td>
											</tr>
											</table>
										</div>
										<?php endif; ?>

										<br/><br/>
										<div class="blocVert">
											<table>
											<tr>
												<td width="24px" align="center"><img src="/modules/catalogue/img/promotions.png" alt="Mode de r&egrave;glement" /></td>
												<td class="WebNavTitlepuce">Mode de r&egrave;glement</td>
											</tr>
											</table>

											<table width="100%">
											<tr>
												<td>Choisissez votre mode de r&egrave;glement :&nbsp;</td>
												<?php //if (!in_array('paiem', array_keys($_SESSION['catalogue']['params']))): ?>


												<?php
												$a_modes_paiement = array(
													'cb' => array(
														'id'		=> 'mode_paiement_cb',
														'label'		=> 'Carte bleue',
														'message'	=> 'Un paiement par carte bleue implique la pr&eacute;paration imm&eacute;diate de votre commande.',
														'cond'		=> (_PLUGIN_EPAYMENT)
													),
													'cheque' => array(
														'id'		=> 'mode_paiement_chq',
														'label'		=> 'Ch&egrave;que &agrave; la commande',
														'message'	=> 'Votre commande sera trait&eacute;e d&egrave;s r&eacute;ception<br/>de votre ch&egrave;que &eacute;tabli &agrave; l\'ordre de <b>officebureau.biz</b>.<br/>Vous devez adresser votre ch&egrave;que &agrave; l\'adresse suivante :<br/><b>Office bureau - 168 rue L&eacute;on Jouhaux - 78500 Sartrouville</b>',
														'cond'		=> (true)
													),
													'compte' => array(
														'id'		=> 'mode_paiement_cpt',
														'label'		=> 'Encours client autoris&eacute;',
														'message'	=> 'Pour les clients n\'ayant pas d\'encours, seules les commandes pay&eacute;es par carte bleue ou par ch&egrave;que seront prises en compte.',
														'cond'		=> (true || $mode_paiement == 'differe')
													)
												);
												?>

												<?php foreach ($a_modes_paiement as $val => $mp): ?>
													<?php if (!in_array('paiem', array_keys($_SESSION['catalogue']['params'])) || $_SESSION['catalogue']['params']['paiem'] == $val): ?>
														<?php if ($mp['cond']): ?>
															<td align="right"><input class="WebRadio" type="radio" id="<?=$mp['id'];?>" name="mode_paiement" value="<?=$val;?>" onclick="javascript: showMsg();" /></td><td><label for="<?=$mp['id'];?>"><?=$mp['label'];?></label></td>
														<?php endif; ?>
													<?php endif; ?>
												<?php endforeach; ?>

											</tr>
											</table>

											<br/><div id="paiem_msg" style="text-align: center;"></div>
										</div>

										<br/><br/>
										<table width="100%">
										<tr>
                                            <td>
                                                <table cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <?php if ($id_user == $_SESSION['dims']['userid']): ?>
                                                        <td><?=catalogue_makegfxbutton("Retour &agrave; la commande",'<img src="/modules/catalogue/img/panier_min.png">',"document.location.href='$scriptenv?op=modifier_commande&id_cmd=$id_cmd';",'*');?></td>
                                                        <?php else: ?>
                                                        <td><?=catalogue_makegfxbutton("Retour &agrave; la liste",'<img src="/modules/catalogue/img/retour.gif">',"document.location.href='$scriptenv?op=commandes&id_cmd=$id_cmd#$id_cmd';",'*');?></td>
                                                        <?php endif; ?>
                                                    </tr>
                                                </table>
                                            </td>
											<td align="right">
												<table cellpadding="0" cellspacing="0">
												<tr>
													<td><?=catalogue_makegfxbutton('Précédent', '<img src="/modules/catalogue/img/back2.png">', "document.location.href='$scriptenv?op=valider_commande&id_cmd=$id_cmd&etape=3';", '*');?></td>
													<td width="12px">&nbsp;</td>
													<td><div style="float: right;"><?=catalogue_makegfxbutton("Confirmer la commande",'<img src="/modules/catalogue/img/button_ok.png">',"if (validateModePaiem()) { document.form.op.value='confirmer_commande'; document.form.submit(); } else { alert('Vous devez choisir un mode de r&egrave;glement.'); }",'*', false, 'positive');?></div></td>
												</tr>
												</table>
											</td>
										</tr>
										</table>

										<table width="100%">
										<tr>
											<td align="center">
												<? include DIMS_APP_PATH."/modules/catalogue/menu_compte.php"; ?>
											</td>
										</tr>
										</table>

										</form>

									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<?php
}
else {
	dims_redirect('/index.php?op=commandes');
}
?>

<script type="text/javascript">
	var msgs = new Array(
		<?php
		$a_msgs = array();
		foreach ($a_modes_paiement as $val => $mp) {
			if (!in_array('paiem', array_keys($_SESSION['catalogue']['params'])) || $_SESSION['catalogue']['params']['paiem'] == $val) {
				if ($mp['cond']) {
					$a_msgs[] = "'".addslashes($mp['message'])."'";
				}
			}
		}
		echo implode(',', $a_msgs);
		?>
	);

	function showMsg() {
		for (i = 0; i <= <?=(sizeof($a_msgs)-1);?>; i++) {
			if (document.getElementsByName('mode_paiement')[i].checked) {
				if (msgs[i] != '') {
					document.getElementById('paiem_msg').innerHTML = '<table width="100%" align="center"><tr><td align="center"><table><tr><td valign="top"><img src="/modules/catalogue/img/important.png" alt="Important" /></td><td>' + msgs[i] + '</td></tr></table></td></tr></table>';
				} else {
					document.getElementById('paiem_msg').innerHTML = '';
				}
			}
		}
	}

	function validateModePaiem () {
		var res = false;
		for (i = 0; i <= <?=(sizeof($a_modes_paiement)-1);?>; i++) {
			if (document.getElementsByName('mode_paiement')[i].checked) {
				res = res || true;
			}
		}
		return res;
	}

	showMsg();
</script>

<?php
$smarty->assign('catalogue', array('CONTENT' => ob_get_contents()));

$page['TITLE'] = 'Frais de ports';
$page['META_DESCRIPTION'] = '';
$page['META_KEYWORDS'] = 'Commandes, frais, port';
$page['CONTENT'] = '';

ob_end_clean();
