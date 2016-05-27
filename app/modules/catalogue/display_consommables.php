<?php
ob_start();

if(!isset($_SESSION['catalogue']['param'])) $_SESSION['catalogue']['param'] = 0;
$rubriques = dims_load_securvalue('param', dims_const::_DIMS_NUM_INPUT, true, true, true,$_SESSION['catalogue']['param']);

if(!isset($_SESSION['catalogue']['display']['mode'])) $_SESSION['catalogue']['display']['mode'] = '';
$mode = dims_load_securvalue('mode', dims_const::_DIMS_CHAR_INPUT, true, true, true, $_SESSION['catalogue']['display']['mode'], 'icons');
if(!isset($_SESSION['catalogue']['display']['num'])) $_SESSION['catalogue']['display']['num'] = 0;
$num = dims_load_securvalue('num', dims_const::_DIMS_NUM_INPUT, true, true, true, $_SESSION['catalogue']['display']['num'], 5);

if(!isset($_SESSION['catalogue']['display']['aff_cata'])) $_SESSION['catalogue']['display']['aff_cata'] = $oCatalogue->getParams('cata_default_show_families');
$aff_cata = dims_load_securvalue('aff_cata', dims_const::_DIMS_NUM_INPUT, true, false, false, $_SESSION['catalogue']['display']['aff_cata']);
$marque = dims_load_securvalue('marque', dims_const::_DIMS_NUM_INPUT, true, false);


$catalogue = array('fam1' => array(), 'fam2' => array(), 'fam3' => array(), 'articles' => array(), 'nav' => array(), 'display' => array(), 'marques' => array('selected' => '', 'values' => array()), 'sliders' => array());

// liste des types d'imprimante
$a_types = array('je' => 'Jet d\'encre', 'laser' => 'Laser');

$conso_marque	= dims_load_securvalue('conso_marque', dims_const::_DIMS_NUM_INPUT, true, true, true);
$conso_type		= dims_load_securvalue('conso_type', dims_const::_DIMS_CHAR_INPUT, true, true, true);
$conso_modele	= dims_load_securvalue('conso_modele', dims_const::_DIMS_NUM_INPUT, true, true, true);

$a_marques = array();
$rs = $db->query('
	SELECT	m.`id`, m.`libelle`, COUNT(DISTINCT(i.`type`)) AS nb_types
	FROM	`dims_mod_cart_marques` m
	INNER JOIN	`dims_mod_cart_imprimantes` i
	ON			i.`marque_id` = m.`id`
	INNER JOIN	`dims_mod_cart_imprimantes_cartouches` ic
	ON			ic.`imprimante_id` = i.`id`
	INNER JOIN	`dims_mod_cart_cartouches` c
	ON			c.`id` = ic.`cartouche_id`
	INNER JOIN	`dims_mod_cata_article` a
	ON			a.`reference` = c.`ref_unifob`
	GROUP BY m.`id`
	ORDER BY m.`libelle`');
if ($db->numrows($rs)) {
	while ($row = $db->fetchrow($rs)) {
		$row['selected'] = ($conso_marque == $row['id']) ? ' selected' : '';
		$a_marques[] = $row;
	}
}

if (!empty($conso_marque)) {
	$catalogue['consommables']['marque_id'] = $conso_marque;

	$types = array();
	$rs = $db->query('
		SELECT	DISTINCT(i.`type`)
		FROM	`dims_mod_cart_imprimantes` i
		INNER JOIN	`dims_mod_cart_marques` m
		ON			m.`id` = i.`marque_id`
		INNER JOIN	`dims_mod_cart_imprimantes_cartouches` ic
		ON			ic.`imprimante_id` = i.`id`
		INNER JOIN	`dims_mod_cart_cartouches` c
		ON			c.`id` = ic.`cartouche_id`
		INNER JOIN	`dims_mod_cata_article` a
		ON			a.`reference` = c.`ref_unifob`
		WHERE	i.`marque_id` = '.$conso_marque);
	if ($db->numrows($rs)) {
		while ($row = $db->fetchrow($rs)) {
			$types[] = array('id' => $row['type'], 'libelle' => $a_types[$row['type']], 'selected' => ($conso_type == $row['type']) ? ' selected' : '');
		}
		if (sizeof($types) == 1) {
			$conso_type = $types[0]['id'];
			$types[0]['selected'] = ' selected';
		}
	}

	if (!empty($conso_type)) {
		$catalogue['consommables']['type_id'] = $conso_type;

		$a_modeles = array();
		$sql = '
			SELECT	i.*, m.`libelle` AS marque
			FROM	`dims_mod_cart_imprimantes` i
			INNER JOIN	`dims_mod_cart_marques` m
			ON			m.`id` = i.`marque_id`
			INNER JOIN	`dims_mod_cart_imprimantes_cartouches` ic
			ON			ic.`imprimante_id` = i.`id`
			INNER JOIN	`dims_mod_cart_cartouches` c
			ON			c.`id` = ic.`cartouche_id`
			INNER JOIN	`dims_mod_cata_article` a
			ON			a.`reference` = c.`ref_unifob`
			WHERE	i.`marque_id` = '.$conso_marque;
		if ($conso_type != 'all') {
			$sql .= ' AND i.`type` = \''.$conso_type.'\'';
		}
		$sql .= ' GROUP BY i.`id` ORDER BY i.`ref`';
		$rs = $db->query($sql);
		while ($row = $db->fetchrow($rs)) {
			$row['selected'] = ($conso_modele == $row['id']) ? ' selected' : '';
			$a_modeles[] = $row;
		}
		if (sizeof($a_modeles) == 1) {
			$conso_modele = $a_modeles[0]['id'];
			$a_modeles[0]['selected'] = ' selected';
		}

		if (!empty($conso_modele)) {
			$catalogue['consommables']['modele_id'] = $conso_modele;
		}
	}
}


$catalogue['consommables']['marques'] = $a_marques;
$catalogue['consommables']['types'] = $types;
$catalogue['consommables']['modeles'] = $a_modeles;


$nav = 'Catalogue';

$articles = array();

$profondeur = 0;

if (!empty($_SESSION['catalogue']['rubriques'])) {
	$rubriques = $_SESSION['catalogue']['rubriques'];
	$a_rubriques = explode(';', $familys['list'][$rubriques]['parents'] .';'. $rubriques);
	$profondeur = sizeof($a_rubriques);

	$rub0 = isset($a_rubriques[1]) ? $a_rubriques[1] : '';
	$rub1 = isset($a_rubriques[2]) ? $a_rubriques[2] : '';
	$rub2 = isset($a_rubriques[3]) ? $a_rubriques[3] : '';
	$rub3 = isset($a_rubriques[4]) ? $a_rubriques[4] : '';
}

// onglets
$famille_menu = 0;
$a_parents = explode(';',$familys['list'][$rubriques]['parents']);

if(count($a_parents) >= 3)
	$famille_menu = $a_parents[2];
else
	$famille_menu = $rubriques;

if (isset($familys['list'][$famille_menu]['color']) && $familys['list'][$famille_menu]['color']!='') {
	$cata_famille= new cata_famille();
	$cata_famille->open($famille_menu);
	$colorfamily = $cata_famille->fields['color'];
	$colorfamily2 = $cata_famille->fields['color2'];
	$colorfamily3 = $cata_famille->fields['color3'];
	$colorfamily4 = $cata_famille->fields['color4'];
}
else {
	$colorfamily = "#323232";
	$colorfamily2 = "#323232";
	$colorfamily3 = "#323232";
	$colorfamily4 = "#323232";
}

$smarty->assign('colorfamily', $colorfamily);
$smarty->assign('colorfamily2', $colorfamily2);
$smarty->assign('colorfamily3', $colorfamily3);
$smarty->assign('colorfamily4', $colorfamily4);
// fin onglets

// on affiche le catalogue si on clique sur le premier niveau de famille
// sauf si on clique sur le bouton 'masquer le catalogue'
// if ($familys['list'][$rubriques]['depth'] == 2 && !isset($_GET['aff_cata'])) {
// 	$aff_cata = 1;
// 	$_SESSION['catalogue']['display']['aff_cata'] = 1;
// }

$catalogue['display']['mode'] = $mode;
$catalogue['display']['num'] = $num;
$catalogue['display']['aff_cata'] = $aff_cata;

$a_keywords = array();
$a_similar = array();
$a_kwdref = array();

if ($_SESSION['catalogue']['cata_restreint'] && $_SESSION['session_adminlevel'] < _DIMS_ID_LEVEL_PURCHASERESP) {
	$sql = "
		SELECT	a.id,
				a.reference,
				al.label,
				a.page AS numpage,
				a.image,
				a.marque,
				a.qte,
				a.putarif_1,
				a.degressif,
				a.dev_durable,
				s.selection,
				af.id_famille,
				m.libelle AS marque_label

		FROM	dims_mod_cata_article a

		INNER JOIN	dims_mod_cata_article_lang al
		ON			al.id_article_1 = a.id

		INNER JOIN	dims_mod_vpc_selection s
		ON			s.ref_article = a.reference
		AND			s.ref_client = '{$_SESSION['catalogue']['code_client']}'

		LEFT JOIN	dims_mod_cata_article_famille af
		ON			af.id_article = a.id

		INNER JOIN	dims_mod_cart_cartouches c
		ON			c.ref_unifob = a.reference";

	if (!empty($conso_marque)) {
		$sql .= "
			INNER JOIN	dims_mod_cart_imprimantes_cartouches ic
			ON			ic.cartouche_id = c.id

			INNER JOIN	dims_mod_cart_imprimantes i
			ON			i.id = ic.imprimante_id
			AND			i.marque_id = ".$conso_marque;
		if (!empty($conso_type)) {
			$sql .= " AND i.type = '".$conso_type."'";
			if (!empty($conso_modele)) {
				$sql .= " AND i.id = ".$conso_modele;
			}
		}
	}

	$sql .= "
		LEFT JOIN	dims_mod_cata_marque m
		ON			m.id = a.marque

		WHERE	a.published = 1
		GROUP BY a.reference";
}
else {
	$sql = "
		SELECT	a.id,
				a.reference,
				al.label,
				a.page AS numpage,
				a.image,
				a.marque,
				a.qte,
				a.putarif_1,
				a.degressif,
				a.dev_durable,
				s.selection,
				af.id_famille,
				m.libelle AS marque_label

		FROM	dims_mod_cata_article a

		INNER JOIN	dims_mod_cata_article_lang al
		ON			al.id_article_1 = a.id

		LEFT JOIN	dims_mod_cata_article_famille af
		ON			af.id_article = a.id

		INNER JOIN	dims_mod_cart_cartouches c
		ON			c.ref_unifob = a.reference";

	if (!empty($conso_marque)) {
		$sql .= "
			INNER JOIN	dims_mod_cart_imprimantes_cartouches ic
			ON			ic.cartouche_id = c.id

			INNER JOIN	dims_mod_cart_imprimantes i
			ON			i.id = ic.imprimante_id
			AND			i.marque_id = ".$conso_marque;
		if (!empty($conso_type)) {
			$sql .= " AND i.type = '".$conso_type."'";
			if (!empty($conso_modele)) {
				$sql .= " AND i.id = ".$conso_modele;
			}
		}
	}

	$sql .= "
		LEFT JOIN	dims_mod_vpc_selection s
		ON			s.ref_article = a.reference
		AND			s.ref_client = '{$_SESSION['catalogue']['code_client']}'

		LEFT JOIN	dims_mod_cata_marque m
		ON			m.id = a.marque

		WHERE	a.published = 1";

	// si un marché est en cours, on regarde si il est restrictif
	if (isset($_SESSION['catalogue']['market'])) {
		$market = cata_market::getByCode($_SESSION['catalogue']['market']['code']);
		if ($market->hasRestrictions()) {
			$sql .= " AND a.id IN (".implode(',', $market->getRestrictions()).")";
		}
	}

	$sql .= "
		GROUP BY a.reference";
}

$rs = $db->query($sql);
$a_idArt = array();
$a_famFiltre = array_merge(cata_getAllChildren($familys, array($rubriques)), array($rubriques));

$sliders = array();

while ($fields = $db->fetchrow($rs)) {
	// si article pas epuise
	if ($fields['qte'] > 0) {
		// filtre sur la famille selectionnee
		if	( ( !empty($rubriques) && in_array($fields['id_famille'], $a_famFiltre) ) || empty($rubriques) ) {
			$article = new article();
			$article->fields = $fields;

			$prix = catalogue_getprixarticle($article);

			if ($prix > 0) {
				$a_idArt[$fields['id']] = $fields['id'];
				$fields['urlencode'] = urlencode($fields['reference']);
				$articles[$fields['id']] = $fields;

				// marque
				if ( !empty($fields['marque']) && !isset($catalogue['marques']['values'][$fields['marque']]) ) {
					$catalogue['marques']['values'][$fields['marque']]['label'] = $fields['marque_label'];
					if ($fields['marque'] == $marque) {
						$catalogue['marques']['selected'] = $fields['marque'];
					}
				}

				if (!is_null($fields['id_famille'])) {
					if (!isset($a_artFam[$fields['id_famille']])) $a_artFam[$fields['id_famille']] = 0;
					$a_artFam[$fields['id_famille']]++;
				}

				// article en promo ?
				if (isset($_SESSION['catalogue']['promo']['unlocked']["'".$fields['reference']."'"]) && $fields['qte'] > 0) {
					$prixaff = catalogue_afficherprix($prix, $a_tva[$article->fields['ctva']]);

					$prix = catalogue_formateprix($prix);
					$prixaff = catalogue_formateprix($prixaff);

					$fields['prix'] 	= $prix;
					$fields['prixaff'] = $prixaff;
					$fields['promotion'] = true;
					$fields['prix_brut'] = catalogue_formateprix(catalogue_getprixarticle($article, 1, true));

					$sliders[$fields['id']] = $fields;
				}
			}
		}
	}
}
uasort($catalogue['marques']['values'], 'cata_orderByLabel');

if(!empty($sliders)) {
	$havePromo = true;
}
else {
	$havePromo = false;
}

$nav = array();

if ($profondeur) {
	foreach ( explode(';', $familys['list'][$rubriques]['parents']) as $idParent ) {
		$elem = array();
		if ($idParent > 1) {
			$elem['id'] = $idParent;
			$elem['label'] = $familys['list'][$idParent]['label'];
			$elem['link'] = cata_makeLinkLabel($familys['list'][$idParent]['label']).'/'.$idParent;

			$nav[] = $elem;
		}
	}

	$elem = array();

	$elem['id'] = $rubriques;
	$elem['label'] = $familys['list'][$rubriques]['label'];
	$elem['link'] = cata_makeLinkLabel($familys['list'][$rubriques]['label']).'/'.$rubriques;

	$nav[] = $elem;
}

$catalogue['nav'] = $nav;

// recuperation des familles de base
$a_fams = cata_getRootFams($familys, $a_artFam, $profondeur + 1);

// recherche des filtres
$a_filters = array();
if (!empty($a_fams)) {
	$a_filters = cata_getCommonFilters(array_intersect($a_famFiltre, array_keys($a_fams)));
}

$post = $_GET;

// recuperation des valeurs de filtres + filtrage des articles
cata_getFilterValues($a_filters, $a_idArt, $post);

foreach (array_keys($a_filters) as $id_filter) {
	if (sizeof($a_filters[$id_filter]['values']) > 0) {

		${'field'.$id_filter} = dims_load_securvalue('field'.$id_filter, dims_const::_DIMS_CHAR_INPUT, true, false);

		$filter = new cata_filter();
		$filter->open($id_filter);
		$filter->values = $a_filters[$id_filter]['values'];
		// recuperation des libelles des filtres
		$filter->getValuesLabels();
		natcasesort($filter->values);

		$a_filters[$id_filter]['values'] = $filter->values;
		$a_filters[$id_filter]['label'] = $filter->fields['libelle'];
		$a_filters[$id_filter]['selected'] = '';

		foreach ($filter->values as $k => $v) {
			if (${"field{$filter->fields['id']}"} == $k) {
				$a_filters[$id_filter]['selected'] = $k;
			}
		}
	}
}

$smarty->assign('filters', $a_filters);


// filtrage des articles - epuration de la liste d'articles
foreach ($articles as $idArt => $fields) {
	// familles
	if (!array_key_exists($idArt, $a_idArt)) {
		$a_artFam[$articles[$idArt]['id_famille']]--;
		unset($articles[$idArt]);
	}
	// marques
	if (!empty($marque) && $fields['marque'] != $marque) {
		$a_artFam[$articles[$idArt]['id_famille']]--;
		unset($articles[$idArt]);
	}
}
$nbarticles = sizeof($articles);

// tri des articles => stock a 0 a la fin du tableau
$articles_tri = array();
$articles_photo = array();
foreach ($articles as $id => $art) {
	if ($art['image'] != '') {
		$articles_photo[] = $art;
	}
	if ($art['qte'] > 0) {
		$articles_tri[$id] = $art;
		unset($articles[$id]);
	}
}
$articles_tri = array_merge($articles_tri, $articles);
unset ($articles);

// redirection dans la fiche article si 1 seul resultat en recherche exacte
if ($nbarticles == 1) {
	foreach ($articles_tri as $art) {
		if (isset($a_kwdref[$art['reference']])) {
			dims_redirect('/index.php?op=fiche_article&ref='.$art['reference']);
		}
	}
}

if (!empty($rub0) && is_array($familys['tree'][$rub0])) {
	foreach ($familys['tree'][$rub0] as $id_fam) {
		if (isset($familys['list'][$id_fam])) {
			$fam['id']		= $familys['list'][$id_fam]['id_famille'];
			$fam['label']	= $familys['list'][$id_fam]['label'];
			$fam['lien']	= '?op=catalogue&param='.$fam['id'];
			$fam['sel']		= '';
			$fam['in_path']	= '';

			if ($id_fam == $rubriques) {
				$fam['sel'] = 'selected';
			}

			if (!empty($rub1) && $id_fam == $rub1) {
				$fam['in_path']	= 'selected';
			}

			$catalogue['fam0'][$fam['id']] = $fam;
		}
	}
}

if (!empty($rub1) && is_array($familys['tree'][$rub1])) {
	foreach ($familys['tree'][$rub1] as $id_fam) {
		if (isset($familys['list'][$id_fam])) {
			$fam['id']		= $familys['list'][$id_fam]['id_famille'];
			$fam['label']	= $familys['list'][$id_fam]['label'];
			$fam['lien']	= '?op=catalogue&param='.$fam['id'];
			$fam['sel']		= '';
			$fam['in_path']	= '';

			if ($id_fam == $rubriques) {
				$fam['sel'] = 'selected';
			}

			if (!empty($rub2) && $id_fam == $rub2) {
				$fam['in_path']	= 'selected';
			}
			$catalogue['fam1'][$fam['id']] = $fam;
		}
	}
}

if (!empty($rub2) && is_array($familys['tree'][$rub2])) {
	foreach ($familys['tree'][$rub2] as $id_fam) {
		if (isset($familys['list'][$id_fam])) {
			$fam['id']		= $familys['list'][$id_fam]['id_famille'];
			$fam['label']	= $familys['list'][$id_fam]['label'];
			$fam['lien']	= '?op=catalogue&param='.$fam['id'];
			$fam['sel']		= '';
			$fam['in_path']	= '';

			if ($id_fam == $rubriques) {
				$fam['sel'] = 'selected';
			}

			if (!empty($rub3) && $id_fam == $rub3) {
				$fam['in_path']	= 'selected';
			}

			$catalogue['fam2'][$fam['id']] = $fam;
		}
	}
}

if (!empty($rub3) && is_array($familys['tree'][$rub3])) {
	foreach ($familys['tree'][$rub3] as $id_fam) {
		if (isset($familys['list'][$id_fam])) {
			$fam['id']		= $familys['list'][$id_fam]['id_famille'];
			$fam['label']	= $familys['list'][$id_fam]['label'];
			$fam['lien']	= '?op=catalogue&param='.$fam['id'];
			$fam['sel']		= '';
			$fam['in_path']	= '';

			if ($id_fam == $rubriques) {
				$fam['sel'] = 'selected';
			}

			$catalogue['fam3'][$fam['id']] = $fam;
		}
	}
}

$nbRubs = (sizeof($a_fams)) ? sizeof($a_fams) : 1;

$nbArticles = sizeof($articles_tri);
$smarty->assign('nb_articles', $nbArticles);

if (!empty($articles_tri)) {
	$tab_art = array();

	$id = 0;
	$bandeau = false;
	$rub4 = '';

	// pagination des articles
	if (isset($_GET['elemsPerPage'])) {
		$elemsPerPage = dims_load_securvalue('elemsPerPage', dims_const::_DIMS_NUM_INPUT, true, false);
		$_SESSION['catalogue']['pagination']['articles'][$rubriques]['nbElems'] = $elemsPerPage;
		$_SESSION['catalogue']['pagination']['articles'][$rubriques]['page'] = 0;
	}
	if (isset($_GET['p'])) {
		$page = dims_load_securvalue('p', dims_const::_DIMS_NUM_INPUT, true, false) - 1;
		$_SESSION['catalogue']['pagination']['articles'][$rubriques]['page'] = $page;
	}
	$articles_tri = cata_paginate('articles', $rubriques, $articles_tri);

	$smarty->assign('pagination_nbElem', $_SESSION['catalogue']['pagination']['articles'][$motscles.$rubriques]['nbElems']);

	$smarty->assign('a_pagination_per_page', $a_pagination_per_page);

	// calcul du nombre de pages
	$nbPages = ceil($nbArticles / $_SESSION['catalogue']['pagination']['articles'][$rubriques]['nbElems']);

	$a_paginationLiens = cata_getPaginationLinks('articles', $rubriques, $nbPages);
	$smarty->assign('pagination_liens', $a_paginationLiens);

	// numero des premier et dernier articles
	$smarty->assign('pagination_deb', $_SESSION['catalogue']['pagination']['articles'][$rubriques]['page'] * $_SESSION['catalogue']['pagination']['articles'][$rubriques]['nbElems'] + 1);
	if ($_SESSION['catalogue']['pagination']['articles'][$rubriques]['page'] == $nbPages - 1) {
		$smarty->assign('pagination_fin', $nbArticles);
	}
	else {
		$smarty->assign('pagination_fin', ($_SESSION['catalogue']['pagination']['articles'][$rubriques]['page'] + 1) * $_SESSION['catalogue']['pagination']['articles'][$rubriques]['nbElems']);
	}
	// FIN - pagination des articles

	$i = 0;

	// envoi des articles au template
	foreach ($articles_tri as $detail) {
		$article = new article();
		$article->fields = $tab_art = $detail;

		// articles de remplacement
		//$artRempl = $article->getArticlesRempl();

		$prix = catalogue_getprixarticle($article);

		if ($prix > 0) {
			$prixaff = catalogue_afficherprix($prix, $a_tva[$article->fields['ctva']]);

			$prix = catalogue_formateprix($prix);
			$prixaff = catalogue_formateprix($prixaff);

			$tab_art['prix'] 	= $prix;
			$tab_art['prixaff'] = $prixaff;

			// article en promo ?
			if (isset($_SESSION['catalogue']['promo']['unlocked']["'".$article->fields['reference']."'"])) {
				$tab_art['promotion'] = true;
				$tab_art['prix_brut'] = catalogue_formateprix(catalogue_getprixarticle($article, 1, true));
			}

			$tab_art['class'] = ($i % 2 == 0) ? 'ligne1' : 'ligne2';

			// on s'assure de la presence de la photo
			switch ($catalogue['display']['mode']) {
				case 'list':
					$dim = 50;
					break;
				case 'icons':
					$dim = 100;
					break;
			}
			$tab_art['image'] = $template_web_path.'gfx/empty_'.$dim.'x'.$dim.'.png';
			if ($article->fields['image'] != '') {
				$vignette = $article->getVignette($dim);
				if ($vignette != null) {
					$tab_art['image'] = $vignette;
				}
			}

			$catalogue['articles'][$tab_art['id']] = $tab_art;
			if(!$havePromo) {
				$sliders[$tab_art['id']] = $tab_art;
			}
			$i++;
		}
	}
	?>
	</table>
	<?php
}

$data['CONTENT'] = ob_get_contents();
ob_end_clean();

shuffle($sliders);
$sliders = array_slice($sliders, 0, 15);

$catalogue['sliders'] = $sliders;
$smarty->assign('catalogue', $catalogue);

$page['TITLE'] = 'Consommables informatique';
$page['META_DESCRIPTION'] = 'Consommables informatique';
$page['META_KEYWORDS'] = 'Consommables';
$page['CONTENT'] = '';

// insertion du javascript jQuery
$smarty->assign('jquery', '');
