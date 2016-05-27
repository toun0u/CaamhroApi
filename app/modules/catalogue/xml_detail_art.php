<?php
ob_clean();
header('Content-type: text/html; charset='._DIMS_ENCODING);
header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date dans le passé
ob_start();

$pref = dims_load_securvalue('pref', dims_const::_DIMS_CHAR_INPUT, true, false);

// suppression des espaces pour une recherche par ref
//cyril : ligne commentée parce que ça supprime les - or il y a des références qui en contienne, on fait juste un remplacement des espaces
//$pref = preg_replace('/\W/', '', trim($pref));
$pref =str_replace(' ', '', trim($pref));

if (isset($pref) && $pref != '') {
	$scol = '';
	if ($_SESSION['catalogue']['cata_restreint'] && $_SESSION['session_adminlevel'] < _DIMS_ID_LEVEL_PURCHASERESP) {
		$sql = '
			SELECT	a.*, m.libelle as marqueLibelle
			FROM	dims_mod_cata_article a

			INNER JOIN	dims_mod_vpc_selection	sel
			ON			sel.ref_article = a.reference
			AND			sel.ref_client = \''.$_SESSION['catalogue']['code_client'].'\'

			LEFT JOIN	dims_mod_cata_marque m
			ON			m.id = a.marque

			WHERE	a.reference = \''.$pref.'\'
			GROUP BY a.reference';
	}
	else {
		$sql = '
			SELECT	a.*, m.libelle as marqueLibelle
			FROM	dims_mod_cata_article a

			LEFT JOIN	dims_mod_cata_marque m
			ON			m.id = a.marque

			WHERE	a.reference = \''.$pref.'\'';

		// si un marché est en cours, on regarde si il est restrictif
		if (isset($_SESSION['catalogue']['market'])) {
			$market = cata_market::getByCode($_SESSION['catalogue']['market']['code']);
			if ($market->hasRestrictions()) {
				$sql .= " AND a.id IN (".implode(',', $market->getRestrictions()).")";
			}
		}

		$sql .= '
			GROUP BY a.reference';
	}
	$db->query($sql);

	if ($db->numrows()) {
		$detail = $db->fetchrow();

		require_once DIMS_APP_PATH.'/modules/catalogue/include/class_article.php';
		$article = new article();
		$article->fields = $detail;

		$photo = '';
		$photo_detail = '';
		$popupRempl = '';
		$imagefile = '';

		$vignette = $article->getVignette(100);
		if ($vignette != null) {
			$photo = '<img src="'.$vignette.'" alt="" />';
		}

		// Developpement durable
		if ($detail['dev_durable']) {
			$dev_durable = "
				<br>
				<table cellpadding=\"2\" cellspacing=\"0\">
				<tr>
				<td><img src=\"$template_path/gfx/logo_ace2.gif\" alt=\"Eco produit\"></td>
				<td>Cet article fait partie de la gamme éco-produits.</td>
				</tr>
				</table>";
		}
		else {
			$dev_durable = '';
		}

		$detail_produit = catalogue_detailproduit($detail);
		$logo = catalogue_logoproduit($detail);

		$degressif = "";
		if (_PLUGIN_AUTOCONNECT) {
			if ($detail['degressif']) {
				$degressif = "
					<br>
					<table cellpadding=\"2\" cellspacing=\"0\">
					<tr>
						<td nowrap colspan=\"2\">&nbsp;<b><u>Tarif dégressif :</u></b>&nbsp;</td>
					</tr>
					<tr>
						<td nowrap>&nbsp;- Pour <b>{$detail['qte1']}</b> :&nbsp;</td>
						<td nowrap align=\"right\">&nbsp;<b>". catalogue_formateprix($detail['remise1']) ."</b> &euro;&nbsp;</td>
					</tr>";
				if ($detail['qte2'] != '' && $detail['qte2'] != 0 && $detail['remise2'] != '' && $detail['remise2'] != 0) $degressif .= "
					<tr>
						<td nowrap>&nbsp;- Pour <b>{$detail['qte2']}</b> :&nbsp;</td>
						<td nowrap align=\"right\">&nbsp;<b>". catalogue_formateprix($detail['remise2']) ."</b> &euro;&nbsp;</td>
					</tr>";
				if ($detail['qte3'] != '' && $detail['qte3'] != 0 && $detail['remise3'] != '' && $detail['remise3'] != 0) $degressif .= "
					<tr>
						<td nowrap>&nbsp;- Pour <b>{$detail['qte3']}</b> :&nbsp;</td>
						<td nowrap align=\"right\">&nbsp;<b>". catalogue_formateprix($detail['remise3']) ."</b> &euro;&nbsp;</td>
					</tr>";
				if ($detail['qte4'] != '' && $detail['qte4'] != 0 && $detail['remise4'] != '' && $detail['remise4'] != 0) $degressif .= "
					<tr>
						<td nowrap>&nbsp;- Pour <b>{$detail['qte4']}</b> :&nbsp;</td>
						<td nowrap align=\"right\">&nbsp;<b>". catalogue_formateprix($detail['remise4']) ."</b> &euro;&nbsp;</td>
					</tr>
					</table>";
			}
		}


		// affichage d'un popup pour presenter les articles de remplacement
		$artRempl = $article->getArticlesRempl();

		if ($article->fields['qte'] <= 0) {
			$remplDisplay = 'block';
		}
		else {
			$remplDisplay = 'none';
		}

		if (sizeof($artRempl)) {
			$popupRempl = '
				<a name="popupRempl">
				<div id="div_popupRempl" style="display: '.$remplDisplay.'">
					<table cellpadding="5" cellspacing="0" style="border: 1px dashed #ddd; background-color: #ffc; width: 600px;">
					<tr>
					<td>
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td><font class=\"size5\" size=\"3\" color=\"#336699\"><b>Articles de remplacement</b></font></td>
					</tr>';
			foreach ($artRempl as $id => $ref_article) {
				$artRatt = new article();
				$artRatt->findByRef($ref_article);

				$prix = catalogue_getprixarticle($artRatt);
				$prixaff = catalogue_afficherprix($prix,$a_tva[$article->fields['ctva']]);
				$prixaff = catalogue_formateprix($prixaff);

				$popupRempl .= '
					<tr>
						<td>
							<table cellpadding="0" cellspacing="0" width="100%" height="28px" style="border-top: 1px solid #ddd;">
							<tr>
								<td align="center" width="40px">'.$artRatt->fields['reference'].'</td>
								<td align="left" width="250px"><b>'.$artRatt->fields['label'].'</b></td>
								<td align="right" width="25px">'.$artRatt->fields['qte'].'</td>
								<td align="right" width="40px" nowrap>'.$prixaff.' &euro;</td>
								<td align="center" width="60px">
									<table cellpadding="0" cellspacing="0">
									<tr>
										<td rowspan="2"><input type="text" name="rqte'.$id.'" class="WebInput" size="5" value="1"></td>
										<td><img OnMouseOut="javascript:this.style.cursor=\'default\'" OnMouseOver="this.style.cursor=\'pointer\'" onclick="javascript:document.getElementsByName(\'rqte'.$id.'\')[0].value++;" border="0" src="./common/modules/catalogue/img/caddy_plus.gif"></td>
									</tr>
									<tr>
										<td><img OnMouseOut="javascript:this.style.cursor=\'default\'" OnMouseOver="this.style.cursor=\'pointer\'" onclick="javascript:if (document.getElementsByName(\'rqte'.$id.'\')[0].value>1) document.getElementsByName(\'rqte'.$id.'\')[0].value--;" border="0" src="./common/modules/catalogue/img/caddy_moins.gif"></td>
									</tr>
									</table>
								</td>
								<td align="center" width="25px">
									<img onclick="javascript: ajouter_artRattSR(\''.$artRatt->fields['reference'].'\', '.$id.');" OnMouseOut="javascript:this.style.cursor=\'default\'" OnMouseOver="this.style.cursor=\'pointer\'" border="0" src="./common/modules/catalogue/img/caddy.gif">
								</td>
							</tr>
							</table>
						</td>
					</tr>';
			}
			$popupRempl .= '
				</table>
				</td>
				</tr>
				</table>
				</div>';
		}

		if ($oCatalogue->getParams('cata_show_stocks')) {
			if ($detail['stock'] > 0) {
				$stock = "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td bgcolor=\"#00B300\"><font color=\"white\">&nbsp;{$detail['qte']}&nbsp;</font></td></tr></table>";
			}
			else {
				$stock = "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td bgcolor=\"#ffc\"><font color=\"#4E779A\"><b>&nbsp;0&nbsp;</b></font></td></tr></table>";
			}
		}
		else {
			if ($detail['qte'] <= 0) {
				$stock = "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td bgcolor=\"#00B300\"><img src=\"/templates/frontoffice/$template_name/gfx/puce_rouge.png\" alt=\"Dispo. sous 48/72h\" /></td></tr></table>";
			}
			elseif ($detail['qte'] < $detail['qte_mini']) {
				$stock = "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td bgcolor=\"#00B300\"><img src=\"/templates/frontoffice/$template_name/gfx/puce_orange.png\" alt=\"En stock\" /></td></tr></table>";
			}
			else {
				$stock = "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td bgcolor=\"#00B300\"><img src=\"/templates/frontoffice/$template_name/gfx/puce_verte.png\" alt=\"En stock\" /></td></tr></table>";
			}
		}

		$artRattLink = '';
		if (sizeof($artRempl) && $article->fields['qte'] > 0) {
			$artRattLink = '<br/><br/><a href="#popupRempl" onclick="javascript: document.getElementById(\'div_popupRempl\').style.display=\'block\';"><font style="font-size: 0.8em; font-weight: bold; color: #d00;">Voir les articles de remplacement</font></a>';
		}

		$lignePrix = '<br/><p class="important">Prix net : '.catalogue_getprixarticle($article).' &euro;</p>';

		echo $stock.'|<h3>D&eacute;tail de l\'article '. $pref."</h3>
		<table cellpadding=\"2\" cellspacing=\"0\" width=\"100%\">
		<tr>
			<td valign=\"top\"><b>". catalogue_cleanstring($detail['label']) ."</b><br>{$detail_produit}{$artRattLink}{$dev_durable}{$degressif}{$lignePrix}</td>
			<td valign=\"top\" align=\"right\">$photo</td>
			<td valign=\"top\" align=\"right\">$photo_detail</td>
		</tr>
		</table>
		|";
		echo $popupRempl;
	}
	else {
		if ($oCatalogue->getParams('cata_show_stocks')) {
			echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td bgcolor=\"#BF0000\"><font color=\"white\">&nbsp;-&nbsp;</font></td></tr></table>|";
		}
		else {
			echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td bgcolor=\"#BF0000\"><!-- - --><font color=\"white\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td></tr></table>|";
		}
		echo '<h3>D&eacute;tail de l\'article '. $pref.'</h3>';
		echo "
			<table cellpadding=\"2\" cellspacing=\"0\" width=\"100%\">
				<tr>
					<td><font style='font-size:12px;font-weight:bold;color:#BF0000'>La référence que vous avez saisie n'existe pas.</font></td>
				</tr>
			</table>
			|";
	}
}
@ob_end_flush();

$_SESSION['catalogue']['op']='';
die();
