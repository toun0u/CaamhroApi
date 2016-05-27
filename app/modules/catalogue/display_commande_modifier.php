<?php
if ($_SESSION['dims']['connected']) {
	ob_start();

	$id_cmd = dims_load_securvalue('id_cmd', dims_const::_DIMS_NUM_INPUT, true, false);

	$user = new user();
	$user->open($_SESSION['dims']['userid']);
	$groups = $user->getgroups(true);

	$group = new group();
	$group->open(key($groups));
	$lstgroups = implode(',', array_merge($groups, $group->getgroupchildrenlite()));

	//liste des articles dans la commande
	$articles = array();
	$libelle = '';
	$modifiable = false;

	$sql = "
		SELECT	cmd.libelle,
				cmd.etat,
				cmd.hors_cata,
				article.*,
				cmd_det.*,
				cmd_det_hc.reference AS hc_ref,
				cmd_det_hc.designation AS hc_des,
				cmd_det_hc.pu AS hc_pu,
				cmd_det_hc.qte AS hc_qte

		FROM	dims_mod_vpc_cmd cmd

		LEFT JOIN	dims_mod_vpc_cmd_detail cmd_det
		ON			cmd.id = cmd_det.id_cmd

		LEFT JOIN	dims_mod_vpc_cmd_detail_hc cmd_det_hc
		ON			cmd.id = cmd_det_hc.id_cmd

		LEFT JOIN	dims_mod_cata_article article
		ON			cmd_det.ref_article = article.reference

		LEFT JOIN	dims_mod_cata_article_lang al
		ON			al.id_article_1 = article.id

		WHERE	cmd.id = $id_cmd
		AND		cmd.id_group IN ($lstgroups)
		AND		cmd.etat <> 'validee'
		AND		cmd.exceptionnelle = 0";
	$rs = $db->query($sql);
	$articles = array();
	while ($fields = $db->fetchrow($rs)) {
		if (!$fields['hors_cata']) {
			$hors_cata = 0;
			$articles[$fields['reference']] = $fields;
			$libelle = $fields['libelle'];
			$etat = $fields['etat'];
		} else {
			$hors_cata = 1;
			$articles[] = $fields;
			$libelle = $fields['libelle'];
			$etat = $fields['etat'];
		}
	}

	switch ($etat) {
		case 'en_cours':
		case 'refusee':
			$modifiable = true;
			break;
		case 'en_cours1':
			($_SESSION['session_adminlevel'] >= _DIMS_ID_LEVEL_USER) ? $modifiable = true : $modifiable = false;
			break;
		case 'en_cours2':
			($_SESSION['session_adminlevel'] >= _DIMS_ID_LEVEL_SERVICERESP) ? $modifiable = true : $modifiable = false;
			break;
	}


	if ($modifiable) {
		$nav = "<a href=\"/index.php?op=commandes\" class=\"WebNavTitle\">". _LABEL_COMMANDESENCOURS ."</a> &raquo; <a href=\"/index.php?op=commandes&id_cmd=$id_cmd\" class=\"WebNavTitle\">$libelle ($id_cmd)</a>";
		?>

		<table width="100%" cellpadding="0" cellspacing="0">
		<tr bgcolor="#eeeeee">
			<td class="WebNavTitle">&nbsp;<? echo $nav; ?></td>
		</tr>
		<tr bgcolor="#dddddd" height="1"><td></td></tr>
		<tr>
			<td width="772" align="center" valign="top">
				<table cellpadding="6" cellspacing="0" width="100%">
				<tr>
					<td>
						<form name="form" method="post">
						<input type="hidden" name="op" value="modifier_commande_fin">
						<input type="hidden" name="id_cmd" value="<? echo $id_cmd; ?>">

						<table cellpadding="0" cellspacing="0" width="100%">
						<tr><td colspan="9" height="1" bgcolor="#dddddd"></td></tr>
						<tr>
							<td bgcolor="#f8f8f8" width="10">&nbsp;</td>
							<td bgcolor="#f8f8f8" width="50"><b>Ref.</b></td>
							<td bgcolor="#f8f8f8" width="362"><b>Désignation</b></td>
							<td bgcolor="#f8f8f8" width="60" align="center"><b>&nbsp;&nbsp;Paquetage&nbsp;&nbsp;<br>&nbsp;&nbsp;Indicatif&nbsp;&nbsp;</b></td>
							<td bgcolor="#f8f8f8" width="70" align="center"><b>&nbsp;&nbsp;Unité&nbsp;&nbsp;<br>&nbsp;&nbsp;de Vente&nbsp;&nbsp;</b></td>
							<?
							if ($_SESSION['catalogue']['afficher_prix'] && _SHOW_PRICES) {
								?><td bgcolor="#f8f8f8" width="60"><b>Prix Net</b></td><?
							}
							?>
							<td bgcolor="#f8f8f8" width="60"><b>Quantité</b></td>
							<?
							if ($_SESSION['catalogue']['afficher_prix'] && _SHOW_PRICES) {
								?><td bgcolor="#f8f8f8" width="50" align="right"><b>Total</b></td><?
							}
							?>
							<td bgcolor="#f8f8f8" width="50" align="right"><b>Effacer</b></td>
						</tr>
						<?
						$id = 0;
						$total_commande = 0;
						$a_total_tva = array();

						if (!$hors_cata) {
							foreach ($articles as $detail) {
								$id++;

								// On vérifie que l'article existe encore
								$article_temp = new article();
								$article_temp->open($detail['reference']);

								if ($article_temp->fields['reference']) {
									echo "<tr><td colspan=\"9\" height=\"1\" bgcolor=\"#f8f8f8\" ></td></tr>";
									echo "<tr><td colspan=\"9\" height=\"1\" bgcolor=\"#dddddd\" ></td></tr>";
									echo "<tr><td colspan=\"9\" height=\"5\"></td></tr>";

									$article = new article();
									$article->fields = $detail;
									$prix = catalogue_getprixarticle($article, $detail['qte']);
									$prixaff = catalogue_afficherprix($prix, $a_tva[$article->fields['ctva']]);

									if (!isset($a_total_tva[$article->fields['ctva']])) $a_total_tva[$article->fields['ctva']] = 0;
									$a_total_tva[$article->fields['ctva']] += $prix * $detail['qte'] * $a_tva[$article->fields['ctva']] / 100;

									// photo
									$photo = '';
									$refimage = substr($detail['image'], 0, -4);
									$imagefile = "./photos/$refimage.jpg";
									if (file_exists($imagefile)) {
										$photo = "<img border=\"0\" src=\"./modules/catalogue/miniature.php?ref=$refimage&size=30\">";
									}

									$detail_produit = catalogue_detailproduit($article->fields);
									$logo = catalogue_logoproduit($article->fields);

									$onclick = "javascript:(detail{$id}.style.display=='none') ? detail{$id}.style.display='block' : detail{$id}.style.display='none'";

									$qte = $detail['qte'];
									$total = round($qte * $prix,2);
									$totalaff = round($qte * $prixaff,2);

									$total_commande += $total;

									$prixaff = catalogue_formateprix($prixaff);
									$totalaff = catalogue_formateprix($totalaff);

									($detail['cond'] == '') ? $cond = 1 : $cond = $detail['cond'];

									if (isset($_SESSION['catalogue']['promo']['unlocked'][$article->fields['reference']]))
									{
										$style = "<font style=\"color:#00cc00\">";
										$endstyle = "</font>";
									}
									else $style = $endstyle = "";

									// ligne de produit
									echo "
										<input type=\"hidden\" name=\"id[]\" value=\"{$id}\">
										<input type=\"hidden\" name=\"ref{$id}\" value=\"{$detail['ref_article']}\">
										<input type=\"hidden\" name=\"del{$id}\" value=\"0\">
										<tr>";

									if ($photo != '') {
										echo "<td OnMouseOver=\"dims_showpopup('<img border=\'0\' src=\'./modules/catalogue/miniature.php?ref=$refimage&size=200\'>',200,event);this.style.cursor='pointer';\" OnMouseOut=\"javascript:dims_hidepopup();this.style.cursor='default';\" width=\"30\" align=\"center\" valign=\"top\">$photo</td>";
									} else {
										echo "<td width=\"30\">&nbsp;</td>";
									}

									echo "
											<td valign=\"top\" width=\"50\" OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" onclick=\"$onclick\"><a>{$detail['reference']}</a></td>
											<td valign=\"top\" width=\"362\" OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" onclick=\"$onclick\"><a><b>{$detail['label']}</b></a></td>
											<td valign=\"top\" width=\"50\" OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" onclick=\"$onclick\" align=\"center\">{$cond}</td>
											<td valign=\"top\" width=\"50\" OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" onclick=\"$onclick\" align=\"center\">1</td>";
									if ($_SESSION['catalogue']['afficher_prix'] && _SHOW_PRICES) {
										echo "<td valign=\"top\" width=\"60\" OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" onclick=\"$onclick\">{$style}{$prixaff}&nbsp;&euro;&nbsp;{$endstyle}</td>";
									}
									echo "
											<td valign=\"top\" width=\"60\">
												<table cellpadding=\"0\" cellspacing=\"0\">
												<tr>
													<td rowspan=\"2\"><input type=\"text\" name=\"qte{$id}\" class=\"WebInput\" size=\"5\" value=\"$qte\"></td>
													<td><img OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" onclick=\"javascript:document.form.qte{$id}.value++;\" border=\"0\" src=\"./common/modules/catalogue/img/caddy_plus.gif\"></td>
												</tr>
												<tr>
													<td><img OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" onclick=\"javascript:if (document.form.qte{$id}.value>1) document.form.qte{$id}.value--;\" border=\"0\" src=\"./common/modules/catalogue/img/caddy_moins.gif\"></td>
												</tr>
												</table>
											</td>";
									if ($_SESSION['catalogue']['afficher_prix'] && _SHOW_PRICES) {
										echo "<td valign=\"top\" width=\"60\" align=\"right\">{$style}{$totalaff}&nbsp;&euro;&nbsp;{$endstyle}</td>";
									}
									echo "
											<td valign=\"top\" width=\"40\" align=\"center\"><a href=\"javascript:if(confirm('Etes-vous sûr(e) de vouloir enlever cet article ?')) { document.form.del{$id}.value=1; document.form.submit(); }\"><img src=\"./common/modules/catalogue/img/trash.png\" alt=\"Enlever cet article\" border=\"0\"></a></td>
										</tr>
										<tr>
											<td></td>
											<td colspan=\"9\" valign=\"top\">
											</td>
										</tr>";
									echo "<tr><td colspan=\"9\" height=\"2\"></td></tr>";
								} else {
									echo "
										<input type=\"hidden\" name=\"id[]\" value=\"{$id}\">
										<input type=\"hidden\" name=\"ref{$id}\" value=\"{$detail['reference']}\">
										<input type=\"hidden\" name=\"del{$id}\" value=\"0\">";
									echo "<tr><td colspan=\"9\" height=\"1\" bgcolor=\"#f8f8f8\" ></td></tr>";
									echo "<tr><td colspan=\"9\" height=\"1\" bgcolor=\"#dddddd\" ></td></tr>";
									echo "<tr><td colspan=\"9\" height=\"5\"></td></tr>";
									echo "
										<tr>
											<td valign=\"top\" width=\"50\">{$detail['ref_article']}</td>
											<td valign=\"top\" colspan=\"6\"><font style=\"color:#cc0000\"><b>Cet article n'existe plus</b></font></td>
											<td valign=\"top\" width=\"40\" align=\"center\"><a href=\"javascript:if(confirm('Etes-vous sûr(e) de vouloir enlever cet article ?')) { document.form.del{$id}.value=1; document.form.submit(); }\"><img src=\"./common/modules/catalogue/img/trash.png\" alt=\"Enlever cet article\" border=\"0\"></a></td>
										</tr>";
									echo "<tr><td colspan=\"9\" height=\"2\"></td></tr>";
								}
							}
						} else {
							foreach ($articles as $detail) {
								$id++;
								echo "<tr><td colspan=\"9\" height=\"1\" bgcolor=\"#f8f8f8\" ></td></tr>";
								echo "<tr><td colspan=\"9\" height=\"1\" bgcolor=\"#dddddd\" ></td></tr>";
								echo "<tr><td colspan=\"9\" height=\"5\"></td></tr>";

								$prix = $detail['hc_pu'];
								$qte = $detail['hc_qte'];
								$total = sprintf("%.2f",round($qte * $prix,2));

								if (!isset($a_total_tva[2])) $a_total_tva[2] = 0;
								$a_total_tva[2] += $prix * $qte * $a_tva[2] / 100;

								$total_commande += $total;

								$prix = catalogue_formateprix($prix);
								$total = catalogue_formateprix($total);

								// ligne de produit
								echo "
									<input type=\"hidden\" name=\"id[]\" value=\"{$id}\">
									<input type=\"hidden\" name=\"ref{$id}\" value=\"{$detail['hc_ref']}\">
									<input type=\"hidden\" name=\"des{$id}\" value=\"{$detail['hc_des']}\">
									<input type=\"hidden\" name=\"prix{$id}\" value=\"{$detail['hc_pu']}\">
									<input type=\"hidden\" name=\"del{$id}\" value=\"0\">
									<tr>
										<td valign=\"top\" width=\"50\">{$detail['hc_ref']}</td>
										<td valign=\"top\" width=\"362\"><b>{$detail['hc_des']}</b></td>
										<td valign=\"top\" width=\"50\" align=\"center\">1</td>
										<td valign=\"top\" width=\"50\" align=\"center\">1</td>
								";
								if ($_SESSION['catalogue']['afficher_prix'] && _SHOW_PRICES) echo "<td valign=\"top\" width=\"60\">{$prix}&nbsp;&euro;&nbsp;</td>";
								echo "
									<td valign=\"top\" width=\"60\">
										<table cellpadding=\"0\" cellspacing=\"0\">
										<tr>
											<td rowspan=\"2\"><input type=\"text\" name=\"qte{$id}\" class=\"WebInput\" size=\"5\" value=\"$qte\"></td>
											<td><img OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" onclick=\"javascript:document.form.qte{$id}.value++;\" border=\"0\" src=\"./common/modules/catalogue/img/caddy_plus.gif\"></td>
										</tr>
										<tr>
											<td><img OnMouseOut=\"javascript:this.style.cursor='default'\" OnMouseOver=\"this.style.cursor='pointer'\" onclick=\"javascript:if (document.form.qte{$id}.value>1) document.form.qte{$id}.value--;\" border=\"0\" src=\"./common/modules/catalogue/img/caddy_moins.gif\"></td>
										</tr>
										</table>
									</td>
								";
								if ($_SESSION['catalogue']['afficher_prix'] && _SHOW_PRICES) echo "<td valign=\"top\" width=\"60\" align=\"right\">{$total}&nbsp;&euro;&nbsp;</td>";
								echo "
										<td valign=\"top\" width=\"40\" align=\"center\"><a href=\"javascript:if(confirm('Etes-vous vûr(e) de vouloir enlever cet article ?')) { document.form.del{$id}.value=1; document.form.submit(); }\"><img src=\"./common/modules/catalogue/img/trash.png\" alt=\"Enlever cet article\" border=\"0\"></a></td>
									</tr>
								";
								echo "<tr><td colspan=\"9\" height=\"2\"></td></tr>";
							}
						}

						$tva = 0;
						foreach ($a_total_tva as $total_tva) {
							$tva += $total_tva;
						}

						$total_commande = round($total_commande, 2);
						$tva = round($tva, 2);
						$total_commande_ttc = round($total_commande + $tva, 2);

						$total_commande_disp = catalogue_formateprix($total_commande);
						$tva_disp = catalogue_formateprix($tva);
						$total_commande_ttc_disp = catalogue_formateprix($total_commande_ttc);

						if ($_SESSION['catalogue']['afficher_prix'] && _SHOW_PRICES) {
							if ($_SESSION['catalogue']['ttc']) {
								?>
								<tr><td colspan="9" height="1" bgcolor="#dddddd"></td></tr>
								<tr height="16">
									<td bgcolor="#f8f8f8" colspan="7" align="right"><b>Total TTC:&nbsp;<b></td>
									<td bgcolor="#f8f8f8" width="60" align="right"><b><?=$total_commande_ttc_disp;?>&nbsp;&euro;</b></td>
									<td bgcolor="#f8f8f8" width="40"></td>
								</tr>
								<tr height="16">
									<td bgcolor="#f8f8f8" colspan="7" align="right">Dont TVA:&nbsp;</td>
									<td bgcolor="#f8f8f8" width="60" align="right"><?=$tva_disp;?>&nbsp;&euro;</td>
									<td bgcolor="#f8f8f8" width="40"></td>
								</tr>
								<?
							} else {
								?>
								<tr><td colspan="9" height="1" bgcolor="#dddddd"></td></tr>
								<tr height="16">
									<td bgcolor="#f8f8f8" colspan="7" align="right"><b>Total HT:&nbsp;<b></td>
									<td bgcolor="#f8f8f8" width="60" align="right"><b><?=$total_commande_disp;?>&nbsp;&euro;</b></td>
									<td bgcolor="#f8f8f8" width="40"></td>
								</tr>
								<tr height="16">
									<td bgcolor="#f8f8f8" colspan="7" align="right">TVA:&nbsp;</td>
									<td bgcolor="#f8f8f8" width="60" align="right"><?=$tva_disp;?>&nbsp;&euro;</td>
									<td bgcolor="#f8f8f8" width="40"></td>
								</tr>
								<tr height="16">
									<td bgcolor="#f8f8f8" colspan="7" align="right"><b>Total TTC:&nbsp;<b></td>
									<td bgcolor="#f8f8f8" width="60" align="right"><b><?=$total_commande_ttc_disp;?>&nbsp;&euro;</b></td>
									<td bgcolor="#f8f8f8" width="40"></td>
								</tr>
								<?
							}
						}
						?>
						<tr><td colspan="9" height="1" bgcolor="#dddddd"></td></tr>
						<tr height="25">
							<td bgcolor="#f8f8f8" colspan="9" width="100%">
								<table cellpadding="2" cellspacing="0" width="100%">
								<tr>
									<td><?=catalogue_makegfxbutton('Retour &agrave; la liste', '<img src="./common/modules/catalogue/img/retour.gif">', "document.location.href='/index.php?op=commandes&id_cmd=$id_cmd#$id_cmd'", '*');?></td>
									<td align="right">
										<table cellpadding="0" cellspacing="0">
										<tr>
											<td><?=catalogue_makegfxbutton('Recalculer ma commande', '<img src="./common/modules/catalogue/img/maj.gif">', "document.form.submit()", '*');?></td>
											<td><?=catalogue_makegfxbutton('Valider ma commande', '<img src="./common/modules/catalogue/img/button_ok.png">', "document.location.href='/index.php?op=valider_commande&id_cmd=$id_cmd'", '*', false, 'positive');?></td>
										</tr>
										</table>
									</td>
								</tr>
								</table>
							</td>
						</tr>
						<tr><td colspan="9" height="1" bgcolor="#dddddd"></td></tr>
						</form>
						</table>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
		<?php
		$smarty->assign('catalogue', array('CONTENT' => ob_get_contents()));

        $page['TITLE'] = 'Modifier une commande';
        $page['META_DESCRIPTION'] = 'Modificer une commande';
        $page['META_KEYWORDS'] = 'commandes, corriger, modifier';
        $page['CONTENT'] = '';

		ob_end_clean();
	}
	else {
		dims_redirect('/index.php?op=commandes');
	}
}
else {
    $_SESSION['catalogue']['connexion']['oldquery'] = $_SERVER['QUERY_STRING'];
    dims_redirect($dims->getScriptEnv().'?op=connexion');
}
