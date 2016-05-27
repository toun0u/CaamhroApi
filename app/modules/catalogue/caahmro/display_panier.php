<?php
if (!empty($_SESSION['catalogue']['panier']['articles'])) {
	// TRI DES COLONNES
	if (!isset($order)) $order = 'part';
	if (!isset($reverse)) $reverse = 0;
	if ($reverse == -1) $reverse = !$reverse; // On inverse le sens

	// On met en session la combinaison op / order / reverse
	$_SESSION['catalogue']['oporder']['op'] = $op;
	$_SESSION['catalogue']['oporder']['order'] = $order;
	$_SESSION['catalogue']['oporder']['reverse'] = $reverse;
	// FIN - TRI DES COLONNES

	// On cree un tableau qui contient la position de chaque ref dans le panier
	$part = array();
	$i = 0;
	foreach ($_SESSION['catalogue']['panier']['articles'] as $ref => $fields) {
		$part[$ref] = $i;
		$i++;
	}

	$keys = array_keys($_SESSION['catalogue']['panier']['articles']);
	$liste = '"'.implode('","', $keys).'"';

	// recherche des articles du panier dans la bd
	$articles = array();
	$sql = 'SELECT *
			FROM `dims_mod_cata_article`
			WHERE `reference` IN ('.$liste.')
			AND `id_workspace` = '.$_SESSION['dims']['workspaceid'];
	$rs = $db->query($sql);
	while ($fields = $db->fetchrow($rs)) {
		$articles[$fields['reference']]                 = $fields;
		$articles[$fields['reference']]['part']         = $part[$fields['reference']];
	}

	$articles_tri = array();
	$i = 0;
	foreach ($articles as $ref_art => $art) {
		($order == '') ? $norder = $i : $norder = $articles[$ref_art][$order];
		$articles_tri[$norder][$ref_art] = $articles[$ref_art];
		$i++;
	}
	ksort($articles_tri);
	if (isset($reverse) && $reverse == 1) $articles_tri = array_reverse($articles_tri);

	$total_ht = 0;
	$total_taxe_phyto = 0;
	$a_total_tva = array();

	$id = 0;

	foreach ($articles_tri as $refs) {
		foreach ($refs as $art) {
			$id++;

			$art['id'] = $id;
			$ref = $art['reference'];
			$qte = $_SESSION['catalogue']['panier']['articles'][$ref]['qte'];

			$article = new article();
			$article->openFromResultSet($art);

			$pu_ht = catalogue_getprixarticle($article, $qte, false, true, false);

			// On calcule la TVA avant d'ajouter la taxe pyhto
			if (!isset($a_total_tva[$article->fields['ctva']])) {
				$a_total_tva[$article->fields['ctva']] = 0;
			}
			$a_total_tva[$article->fields['ctva']] += $pu_ht * $qte * $a_tva[$article->fields['ctva']] / 100;

			$pu_ht = catalogue_getprixarticle($article, $qte, false, true);
			$pu_ttc = round($pu_ht * (1 + $a_tva[$article->fields['ctva']] / 100), 2);
			$total_ligne = round($pu_ht * $qte, 2);


			// Taxe phyto
			$total_taxe_phyto += $article->get('taxe_certiphyto') * $qte;

			$total_ht += $total_ligne;

			$art['qte_cde'] = $qte;
			$art['pu_ht'] = catalogue_formateprix($pu_ht);
			$art['pu_ttc'] = catalogue_formateprix($pu_ttc);
			$art['somme_ht'] = catalogue_formateprix($total_ligne);

			$commande['articles'][] = $art;
		}

		$total_tva = 0;
		foreach ($a_total_tva as $totaltva) {
			$total_tva += $totaltva;
		}

		$total_tva 			= round($total_tva, 2);
		$total_taxe_phyto 	= round($total_taxe_phyto, 2);

		$total_ttc 			= $total_ht + $total_tva;

		$commande['total_ht'] 			= catalogue_formateprix($total_ht);
		$commande['total_tva'] 			= catalogue_formateprix($total_tva);
		$commande['total_taxe_phyto'] 	= catalogue_formateprix($total_taxe_phyto);
		$commande['total_ttc'] 			= catalogue_formateprix($total_ttc);
	}

	$smarty->assign('commande', $commande);

	// URL de retour "Continuer mes achats"
	if (isset($_SESSION['catalogue']['achats_url'])) {
		$achats_action = 'javascript:document.location.href=\''.$_SESSION['catalogue']['achats_url'].'\'';
	}
	else {
		$achats_action = 'javascript:document.location.href=\'/\';';
	}
	$smarty->assign('achats_action', $achats_action);
}
