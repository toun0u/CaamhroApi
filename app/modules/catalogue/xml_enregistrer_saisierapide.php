<?php
ob_clean();
header('Content-type: text/html; charset='._DIMS_ENCODING);
header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date dans le passé
ob_start();

$pref0 = dims_load_securvalue('pref0', dims_const::_DIMS_CHAR_INPUT, true, false);
$qte0 = dims_load_securvalue('qte0', dims_const::_DIMS_NUM_INPUT, true, false);
$nbart = dims_load_securvalue('nbart', dims_const::_DIMS_NUM_INPUT, true, false);

// suppression des espaces pour une recherche par ref
//cyril : ligne commentée parce que ça supprime les - or il y a des références qui en contienne, on fait juste un remplacement des espaces
//$pref = preg_replace('/\W/', '', trim($pref));
$pref0 = str_replace(' ', '', trim($pref0));

$uventeField = 'uvente';
if ($_SESSION['dims']['connected']) {
	require_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/class_client.php';
	require_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/class_client_cplmt.php';

	$oClient = new cata_client();
	$oClient->openByCode($_SESSION['catalogue']['code_client']);
	$oClientCplmt = new cata_client_cplmt();
	if ($oClientCplmt->open($oClient->fields['id_client'])) {
		if ($oClientCplmt->fields['soldeur'] == 'Oui') {
			$uventeField = 'uventesolde';
		}
	}

}


$recolisage = false;
if (isset($nbart) && $nbart > 0) {
	for ($i = 0; $i < $nbart; $i++) {
		$pref = "pref{$i}";
		$qte = "qte{$i}";

		$PREF = strtoupper($$pref);

		$article = new article();
		$article->findByRef($PREF);

		if(!$article->isNew()) {
			$moduloQte = $$qte % $article->fields[$uventeField];
			if($moduloQte > 0) {
				$recolisage = true;
				$$qte = $$qte + ($article->fields[$uventeField] - $moduloQte);
			}

			$prix = catalogue_getprixarticle($article, $$qte);

			if ($prix > 0) {
				$prixaff = catalogue_afficherprix($prix, $a_tva[$article->fields['ctva']]);

				if (!isset($_SESSION['catalogue']['panier']['articles'][$PREF]['qte'])) $_SESSION['catalogue']['panier']['articles'][$PREF]['qte'] = 0;
				if (!isset($_SESSION['catalogue']['panier']['montant'])) $_SESSION['catalogue']['panier']['montant'] = 0;

				$_SESSION['catalogue']['panier']['articles'][$PREF]['qte'] += $$qte;

				if ($oCatalogue->getParams('cata_base_ttc')) {
					$_SESSION['catalogue']['panier']['montant'] += $$qte*$prixaff;
				}
				else {
					$_SESSION['catalogue']['panier']['montant'] += $$qte*$prix;
				}
			}
		}
	}
}

$nbarticle = (isset($_SESSION['catalogue']['panier']['articles'])) ? sizeof($_SESSION['catalogue']['panier']['articles']) : 0;
$montant = (isset($_SESSION['catalogue']['panier']['montant'])) ? catalogue_formateprix($_SESSION['catalogue']['panier']['montant']) : catalogue_formateprix('0');


// remise sur les commandes web
$remises = array(0, $oCatalogue->getParams('tx_remise_cde_web'));


if ($nbarticle > 0) {
	$articles = array_reverse($_SESSION['catalogue']['panier']['articles'], true);
	$bgcolor = "bgcolor=\"". _CMD_COLOR_ENTETE ."\"";

	echo $nbarticle."|
		<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
		<tr $bgcolor><td height=\"5\" colspan=\"2\"></td></tr>
		<tr $bgcolor>
			<td><b>&nbsp;Contenu de votre saisie</b></td>
			<td align=\"right\" style=\"padding-right:5px\">
				<form name=\"form\" action=\"/index.php\" method=\"Post\"><input type=\"Hidden\" name=\"op\" value=\"valider_panier\">
				<table cellpadding=\"0\" cellspacing=\"0\">
				<tr>";
	if (!(isset($_SESSION['session_anonymous']) && $_SESSION['session_anonymous'])) {
		if ($oCatalogue->getParams('panier_type')) {
			echo "<td>". catalogue_makegfxbutton('Enregistrer comme "'. _LABEL_PANIERTYPE .'"','<img style="margin-right: 40px;" src="./common/modules/catalogue/img/enregistrer.png" />',"document.location.href='/index.php?op=enregistrer_panier&type="._CATA_PANIER_TYPE_LIST_CLASSIQUE."'", '') ."</td>";
		}
		if ($oCatalogue->getParams('school_lists')) {
			echo "<td>". catalogue_makegfxbutton('Enregistrer comme "'. _LABEL_LISTSCOLAIRE .'"','<img style="margin-right: 40px;" src="./common/modules/catalogue/img/enregistrer.png" />',"document.location.href='/index.php?op=enregistrer_panier&type="._CATA_PANIER_TYPE_LIST_SCOLAIRE."'", '') ."</td>";
		}
		echo "<td style=\"padding-left:5px\">". catalogue_makegfxbutton('Valider la demande d\'achat', '<img src="./common/modules/catalogue/img/add-to-basket.png">', "document.form.submit();", '', false, 'positive') ."</td>";
	}
	echo "
				</tr>
				</form>
				</table>
			</td>
		</tr>
		<tr $bgcolor><td height=\"5\" colspan=\"2\"></td></tr>
		<tr>
			<td colspan=\"2\">
				<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"border: ". _CMD_COLOR_BORDER ." 1px solid\">
				<tr><td colspan=\"8\" height=\"1\" bgcolor=\"#dddddd\"></td></tr>
				<tr>
					<td bgcolor=\"#f8f8f8\" align=\"center\"><b>Ref.</b></td>
					<td bgcolor=\"#f8f8f8\"><b>Désignation</b></td>
					<td bgcolor=\"#f8f8f8\" align=\"center\"><b>Unité<br>de Vente</b></td>
					<td bgcolor=\"#f8f8f8\" align=\"right\"><b>Prix Net</b></td>
					<td bgcolor=\"#f8f8f8\" align=\"center\"><b>Quantité</b></td>
					<td bgcolor=\"#f8f8f8\" align=\"right\"><b>Total</b></td>
					<td bgcolor=\"#f8f8f8\" align=\"center\"><b>Sup.</b></td>
				</tr>
				<tr><td colspan=\"8\" height=\"1\" bgcolor=\"#dddddd\"></td></tr>";
	foreach ($articles as $ref => $det) {
		$article = new article();
		$article->findByRef($ref);

        // base de calcul (HT / TTC ?)
        // TTC
        if ($oCatalogue->getParams('cata_base_ttc')) {
            $pu_brut        = catalogue_getprixarticle($article, $det['qte'], true);
            $pu_ttc         = catalogue_getprixarticle($article, $det['qte']) * (1 - $remises[1] / 100);
            $pu_ht          = $pu_ttc / (1 + $a_tva[$article->fields['ctva']] / 100);
            $total_ttc      = $pu_ttc * $det['qte'];

            if (!isset($a_total_tva[$article->fields['ctva']])) {
                $a_total_tva[$article->fields['ctva']] = 0;
            }
            $a_total_tva[$article->fields['ctva']] += $total_ttc - ($total_ttc / (1 + $a_tva[$article->fields['ctva']] / 100));

            // formatage pour affichage
            $pu_brut    = catalogue_formateprix($pu_ttc);
            $pu         = catalogue_formateprix($pu_ttc);
            $total      = catalogue_formateprix($total_ttc);
        }
        // HT
        else {
            $pu_brut        = catalogue_getprixarticle($article, $det['qte'], true);
            $pu_ht          = catalogue_getprixarticle($article, $det['qte']) * (1 - $remises[1] / 100);
            $pu_ttc         = $pu_ht * (1 + $a_tva[$article->fields['ctva']] / 100);
            $total_ht       = $pu_ht * $det['qte'];

            if (!isset($a_total_tva[$article->fields['ctva']])) {
                $a_total_tva[$article->fields['ctva']] = 0;
            }
            $a_total_tva[$article->fields['ctva']] += $total_ht * $a_tva[$article->fields['ctva']] / 100;

            // formatage pour affichage
            $pu_brut    = catalogue_formateprix($pu_ttc);
            $pu         = catalogue_formateprix($pu_ht);
            $total      = catalogue_formateprix($total_ht);
        }

		echo "
			<tr><td colspan=\"8\" height=\"2\"></td></tr>
			<tr>
				<td align=\"center\">&nbsp;{$article->fields['reference']}&nbsp;</td>
				<td>&nbsp;{$article->fields['label']}&nbsp;</td>
				<td align=\"center\">&nbsp;".$article->fields[$uventeField]."&nbsp;</td>
				<td align=\"right\"><nobr>&nbsp;{$pu}&nbsp;&euro;&nbsp;</nobr></td>
				<td align=\"center\">&nbsp;{$det['qte']}&nbsp;</td>
				<td align=\"right\"><nobr>&nbsp;{$total}&nbsp;&euro;&nbsp;</nobr></td>
				<td align=\"center\">&nbsp;<a href=\"javascript:dims_confirmlink('/index.php?op=enlever_article&pref={$article->fields['reference']}&redir=saisierapide','Etes-vous sûr(e) de vouloir supprimer cet article de votre saisie ?');\"><img src=\"./common/modules/catalogue/img/trash.png\" alt=\"Supprimer cet article de la saisie\" border=\"0\"></a>&nbsp;</td>
			</tr>
			<tr><td colspan=\"8\" height=\"2\"></td></tr>
			<tr><td colspan=\"8\" height=\"1\" bgcolor=\"#dddddd\"></td></tr>";
	}
	echo "</table></td></tr></table>";
	if($recolisage) {
		echo '<script type="text/javascript" language="javascript">
				flashPopup("La quantit\351 a \351t\351 ajust\351e au colisage sup\351rieur.");
			</script>';
	}
}

$panier = array('nb_art' => 0, 'nb_total' => 0, 'montant' => 0);

if (isset($_SESSION['catalogue']['panier'])) {
	if (is_array($_SESSION['catalogue']['panier']['articles'])) {
		$i = 0;

		foreach ($_SESSION['catalogue']['panier']['articles'] as $ref => $art) {
			$article = new article();

			if ($article->findByRef($ref)) {
				$panier['nb_art']++;
				$panier['nb_total'] += $art['qte'];

				$puht = catalogue_getprixarticle($article, $art['qte']);
				$puttc = $puht * (1 + ($a_tva[$article->fields['ctva']] / 100));

				$panier['montant'] += $puht * $art['qte'];

				$panier['articles'][$article->fields['reference']]['ref'] 		= $article->fields['reference'];
				$panier['articles'][$article->fields['reference']]['qte'] 		= $art['qte'];
				$panier['articles'][$article->fields['reference']]['label'] 	= $article->fields['label'];
				$panier['articles'][$article->fields['reference']]['photo'] 	= $article->fields['image'];
				$panier['articles'][$article->fields['reference']]['puht']		= catalogue_formateprix($puht);
				$panier['articles'][$article->fields['reference']]['totalht']	= catalogue_formateprix($puht * $art['qte']);
				$panier['articles'][$article->fields['reference']]['puttc']		= catalogue_formateprix($puttc);
				$panier['articles'][$article->fields['reference']]['totalttc']	= catalogue_formateprix($puttc * $art['qte']);
			}
		}

		if (isset($panier['articles'])) {
			$panier['articles'] = array_reverse($panier['articles']);
		}
	}

	$montant = catalogue_formateprix($_SESSION['catalogue']['panier']['montant']);
	$nbarticle = sizeof($_SESSION['catalogue']['panier']['articles']);
}
else {
	$montant = catalogue_formateprix(0);
	$nbarticle = 0;
}

// Spécifique plugin 'autoconnect'
if (_PLUGIN_AUTOCONNECT) {
	// Si nouveaux articles en session, mettre à jour le panier
	if ($oCatalogue->getParams('cart_management') == 'cookie') {
		panier2cookie();
	}
	elseif ($oCatalogue->getParams('cart_management') == 'bdd') {
		panier2bdd();
	}
}


@ob_end_flush();

$_SESSION['catalogue']['op']='';
die();
