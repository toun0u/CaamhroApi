<?php
ob_start();

if(!isset($_SESSION['catalogue']['display']['mode'])) $_SESSION['catalogue']['display']['mode'] = '';
$mode = dims_load_securvalue('mode', dims_const::_DIMS_CHAR_INPUT, true, true, true, $_SESSION['catalogue']['display']['mode'], 'icons');
if(!isset($_SESSION['catalogue']['display']['num'])) $_SESSION['catalogue']['display']['num'] = 0;
$num = dims_load_securvalue('num', dims_const::_DIMS_NUM_INPUT, true, true, true, $_SESSION['catalogue']['display']['num'], 5);

if(!isset($_SESSION['catalogue']['display']['aff_cata'])) $_SESSION['catalogue']['display']['aff_cata'] = $oCatalogue->getParams('cata_default_show_families');
$aff_cata = dims_load_securvalue('aff_cata', dims_const::_DIMS_NUM_INPUT, true, false, false, $_SESSION['catalogue']['display']['aff_cata']);

$rubriques = dims_load_securvalue('rubriques', dims_const::_DIMS_CHAR_INPUT, true, false);
if(!isset($_SESSION['catalogue']['filtres']['goodprices']['marque'])) $_SESSION['catalogue']['filtres']['goodprices']['marque'] = 0;
$marque = dims_load_securvalue('marque', dims_const::_DIMS_NUM_INPUT, true, false, false, $_SESSION['catalogue']['filtres']['goodprices']['marque']);

if(!isset($_SESSION['catalogue']['display']['tri'])) $_SESSION['catalogue']['display']['tri'] = 'reference';
$tri = dims_load_securvalue('tri', dims_const::_DIMS_CHAR_INPUT, true, true, true, $_SESSION['catalogue']['display']['tri']);

if (!isset($_SESSION['catalogue']['pagination']['articles']['catalogue']['nbElems'])) {
	$_SESSION['catalogue']['pagination']['articles']['goodprices']['nbElems'] = $a_pagination_per_page[1];
	$_SESSION['catalogue']['pagination']['articles']['goodprices']['page'] = 0;
}

// on envoie le mode de tri à smarty pour pouvoir le mettre en évidence
$smarty->assign('tri', $tri);

$smarty->assign('rubriques', '');
$smarty->assign('rubparam', '');

if (!empty($rubriques)) {
	$smarty->assign('rubriques', $rubriques);
	$smarty->assign('rubparam', '&rubriques='.$rubriques);
}

$catalogue = array('fam1' => array(), 'fam2' => array(), 'fam3' => array(), 'articles' => array(), 'nav' => array(), 'display' => array(), 'marques' => array('selected' => '', 'values' => array()), 'sliders' => array());


$nav = 'Nouveautés';

$articles = array();

// chargement des correspondaces de mots cles (ex: rtte => ramette)
loadCorrespKeywords();

if ($rubriques != '') {
	$a_rubriques = explode(';', $familys['list'][$rubriques]['parents'] .';'. $rubriques);
}
else {
	$a_rubriques = array();
}

$profondeur = sizeof($a_rubriques);

// onglets
$famille_menu = 0;
$a_parents = explode(';',$familys['list'][$rubriques]['parents']);

if(count($a_parents) >= 3)
    $famille_menu = $a_parents[2];
else
    $famille_menu = $rubriques;

$colorindex     = "#323232";
	$colorfamily = "#A78EB6";
	$colorfamily2 = "#805F94";
	$colorfamily3 = "#F8EDFF";
	$colorfamily4 = "#805F94";

if (isset($familys['list'][$famille_menu]['color']) && $familys['list'][$famille_menu]['color']!='') {
	$cata_famille= new cata_famille();
	$cata_famille->open($famille_menu);
    $colorindex = $cata_famille->fields['color'];
	$colorfamily2 = $cata_famille->fields['color2'];
	$colorfamily3 = $cata_famille->fields['color3'];
	$colorfamily4 = $cata_famille->fields['color4'];
}

$smarty->assign('colorindex', $colorindex);
$smarty->assign('colorfamily', $colorfamily);
$smarty->assign('colorfamily2', $colorfamily2);
$smarty->assign('colorfamily3', $colorfamily3);
$smarty->assign('colorfamily4', $colorfamily4);

// on affiche le catalogue si on change d'onglet
// if ( isset($_SESSION['catalogue']['onglet']) && $_SESSION['catalogue']['onglet'] != 'goodprices' ) {
// 	$_SESSION['catalogue']['display']['aff_cata'] = 1;
// }
$_SESSION['catalogue']['onglet'] = 'goodprices';
// fin onglets

$catalogue['display']['mode'] = $mode;
$catalogue['display']['num'] = $num;
$catalogue['display']['aff_cata'] = $aff_cata;

// unité de vente
$uventeField = 'uvente';
if (!empty($_SESSION['catalogue']['client_id'])) {
	include_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/class_client_cplmt.php';
    $obj_cli_cplmt = new cata_client_cplmt();
    $obj_cli_cplmt->open($_SESSION['catalogue']['client_id']);

    if ($obj_cli_cplmt->fields['soldeur'] == 'Oui') {
        $uventeField = 'uventesolde';
    }
}

$a_keywords = array();
$a_similar = array();
$a_kwdref = array();


if ($_SESSION['catalogue']['cata_restreint'] && $_SESSION['session_adminlevel'] < _DIMS_ID_LEVEL_PURCHASERESP) {
	$sql = "
		SELECT	a.*,
				a.page AS numpage,
				al.label,
				s.selection,
				af.id_famille,
				m.libelle AS marque_label

		FROM	dims_mod_cata_article a

		INNER JOIN	dims_mod_cata_article_lang al
		ON			al.id_article_1 = a.id

		INNER JOIN	dims_mod_vpc_selection s
		ON			s.ref_article = a.reference
		AND			s.ref_client = '{$_SESSION['catalogue']['code_client']}'";
	if ($profondeur) {
		$sql .= "
			INNER JOIN	dims_mod_cata_article_famille af
			ON			af.id_article = a.id";
	} else {
		$sql .= "
			LEFT JOIN	dims_mod_cata_article_famille af
			ON			af.id_article = a.id";
	}
	$sql .= "
		LEFT JOIN	dims_mod_cata_marque m
		ON			m.id = a.marque

		WHERE	a.goodprice = 1
		AND		a.published = 1
		GROUP BY a.reference";
} else {
	$sql = "
		SELECT	a.*,
				a.page AS numpage,
				al.label,
				s.selection,
				af.id_famille,
				m.libelle AS marque_label,

				f.position

		FROM	dims_mod_cata_article a

		INNER JOIN	dims_mod_cata_article_lang al
		ON			al.id_article_1 = a.id";
	if ($profondeur) {
		$sql .= "
			INNER JOIN	dims_mod_cata_article_famille af
			ON			af.id_article = a.id
			INNER JOIN	dims_mod_cata_famille f
			ON			f.id_famille = af.id_famille";
	} else {
		$sql .= "
			LEFT JOIN	dims_mod_cata_article_famille af
			ON			af.id_article = a.id
			LEFT JOIN	dims_mod_cata_famille f
			ON			f.id_famille = af.id_famille";
	}
	$sql .= "
		LEFT JOIN	dims_mod_vpc_selection s
		ON			s.ref_article = a.reference
		AND			s.ref_client = '{$_SESSION['catalogue']['code_client']}'

		LEFT JOIN	dims_mod_cata_marque m
		ON			m.id = a.marque

		WHERE	a.goodprice = 1
		AND		a.published = 1";

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

$res = $db->query($sql);
$a_idArt = array();
$a_famFiltre = array_merge(cata_getAllChildren($familys, array($rubriques)), array($rubriques));

$sliders = array();

while ($fields = $db->fetchrow($res)) {

	// filtre sur la famille selectionnee
	if ( empty($rubriques) || in_array($fields['id_famille'], $a_famFiltre) ) {
		$article = new article();
		$article->fields = $fields;

		// pas besoin d'avoir le prix exact, il faut juste un prix
		// $prix = catalogue_getprixarticle($article);
		$prix = $article->fields['putarif_0'];

		if ($prix > 0 || (!$_SESSION['dims']['connected'] && !$oCatalogue->getParams('cata_mode_B2C'))) {
			$a_idArt[$fields['id']] = $fields['id'];
			$fields['urlencode'] = urlencode($fields['reference']);
			$articles[$fields['id']] = $fields;
			$articles[$fields['id']]['puht'] = $prix;

			// marque
			if ( !empty($fields['marque']) && !isset($catalogue['marques']['values'][$fields['marque']]) ) {
				$catalogue['marques']['values'][$fields['marque']]['label'] = $fields['marque_label'];
				if ($fields['marque'] == $_SESSION['catalogue']['filtres']['goodprices']['marque']) {
					$catalogue['marques']['selected'] = $fields['marque'];
				}
			}
		}
		else {
			if (isset($a_kwdref[$fields['ref']])) {
				unset($a_kwdref[$fields['ref']]);
			}
		}

		// article en promo ?
		if (isset($_SESSION['catalogue']['promo']['unlocked']["'".$fields['PREF']."'"]) && $fields['stock'] > 0) {
			$article = new article();
			$article->fields = $fields;

			$prix = catalogue_getprixarticle($article);

			if ($prix > 0) {
				$prixaff = catalogue_afficherprix($prix, $a_tva[$article->fields['PCTVA']]);

				$prix = catalogue_formateprix($prix);
				$prixaff = catalogue_formateprix($prixaff);

				$fields['prix'] 	= $prix;
				$fields['prixaff'] = $prixaff;
				$fields['promo'] = true;
				$fields['prix_brut'] = catalogue_formateprix(catalogue_getprixarticle($article, 1, true));

				$sliders[$fields['id']] = $fields;
			}
			else {
				if (isset($a_kwdref[$fields['ref']])) {
					unset($a_kwdref[$fields['ref']]);
				}
			}
		}
	}

	if (!is_null($fields['id_famille'])) {
		if (!isset($a_artFam[$fields['id_famille']])) $a_artFam[$fields['id_famille']] = 0;
		$a_artFam[$fields['id_famille']]++;
	}
}
uasort($catalogue['marques']['values'], 'cata_orderByLabel');


// recuperation des familles de base
$a_fams = cata_getRootFams($familys, $a_artFam, $profondeur + 1);
ksort($a_fams);

foreach ($a_fams as $rubid => $nbart) {
	foreach (array_merge(explode(';', $familys['list'][$rubid]['parents']), array($rubid)) as $idParent) {
		switch ($familys['list'][$idParent]['depth']) {
			case 2:
				$catalogue['fam1'][$idParent] = array(
					'id'		=> $familys['list'][$idParent]['id_famille'],
					'label'		=> $familys['list'][$idParent]['label'],
					'lien'		=> '?op=goodprices&rubriques='.$idParent,
					'sel'		=> ($idParent == $rubriques) ? 'selected' : '',
					'in_path'	=> (in_array($idParent, explode(';', $familys['list'][$rubriques]['parents']))) ? 'selected' :''
					);
				if ( in_array($idParent, explode(';', $familys['list'][$rubriques]['parents'])) || $idParent == $rubriques ) $fam1 = $idParent;
				break;
			case 3:
				if ( in_array($idParent, $familys['tree'][$fam1]) ) {
					$catalogue['fam2'][$idParent] = array(
						'id'		=> $familys['list'][$idParent]['id_famille'],
						'label'		=> $familys['list'][$idParent]['label'],
						'lien'		=> '?op=goodprices&rubriques='.$idParent,
						'sel'		=> ($idParent == $rubriques) ? 'selected' : '',
						'in_path'	=> (in_array($idParent, explode(';', $familys['list'][$rubriques]['parents']))) ? 'selected' :''
						);
					if ( in_array($idParent, explode(';', $familys['list'][$rubriques]['parents'])) || $idParent == $rubriques ) $fam2 = $idParent;
				}
				break;
			case 4:
				if ( in_array($idParent, $familys['tree'][$fam2]) ) {
					$catalogue['fam3'][$idParent] = array(
						'id'		=> $familys['list'][$idParent]['id_famille'],
						'label'		=> $familys['list'][$idParent]['label'],
						'lien'		=> '?op=goodprices&rubriques='.$idParent,
						'sel'		=> ($idParent == $rubriques) ? 'selected' : '',
						'in_path'	=> (in_array($idParent, explode(';', $familys['list'][$rubriques]['parents']))) ? 'selected' :''
						);
				}
				break;
		}
	}
}


$nav = array();

if(!empty($sliders)) {
    $havePromo = true;
}
else {
    $havePromo = false;
}

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
	if (!empty($_SESSION['catalogue']['filtres']['goodprices']['marque']) && $fields['marque'] != $_SESSION['catalogue']['filtres']['goodprices']['marque']) {
		$a_artFam[$articles[$idArt]['id_famille']]--;
		unset($articles[$idArt]);
	}
}

$nbarticles = sizeof($articles);

// tri des articles
$articles_tri = array();
foreach ($articles as $id => $art) {
	switch ($_SESSION['catalogue']['display']['tri']) {
                 case 'reference':
			$articles_tri[$art['reference']] = $art;
			break;
		case 'prix':
			$articles_tri[$art['putarif_0'].$art['reference']] = $art;
			break;
		case 'marque':
			$articles_tri[$art['marque_label'].$art['id_famille'].$art['reference']] = $art;
			break;
		case 'designation':
			$articles_tri[$art['id_famille'].$art['label'].$art['reference']] = $art;
			break;
		case 'stock':
			$articles_tri[$art['qte'].$art['label'].$art['reference']] = $art;
			break;
	}
}
if ($_SESSION['catalogue']['display']['tri'] == 'prix') {
	ksort($articles_tri, SORT_NUMERIC);
}
elseif ($_SESSION['catalogue']['display']['tri'] == 'stock') {
	krsort($articles_tri, SORT_NUMERIC);
}
else {
	ksort($articles_tri);
}
unset ($articles);


$nbRubs = (sizeof($a_fams)) ? sizeof($a_fams) : 1;

$nbArticles = sizeof($articles_tri);
$smarty->assign('nb_articles', $nbArticles);

if (!empty($articles_tri)) {
	$tab_art = array();

	// pagination des articles
	if (isset($_GET['elemsPerPage'])) {
		$elemsPerPage = dims_load_securvalue('elemsPerPage', dims_const::_DIMS_NUM_INPUT, true, false);
		$_SESSION['catalogue']['pagination']['articles']['goodprices']['nbElems'] = $elemsPerPage;
		$_SESSION['catalogue']['pagination']['articles']['goodprices']['page'] = 0;
	}
	if (isset($_GET['p'])) {
		$p = dims_load_securvalue('p', dims_const::_DIMS_NUM_INPUT, true, false) - 1;
		$_SESSION['catalogue']['pagination']['articles']['goodprices']['page'] = $p;
	}
	$articles_tri = cata_paginate('articles', 'goodprices', $articles_tri);

    $smarty->assign('pagination_nbElem', $_SESSION['catalogue']['pagination']['articles']['goodprices']['nbElems']);

	// calcul du nombre de pages
	$nbPages = ceil($nbArticles / $_SESSION['catalogue']['pagination']['articles']['goodprices']['nbElems']);

	$a_paginationLiens = cata_getPaginationLinks('articles', 'goodprices', $nbPages);
	$smarty->assign('pagination_liens', $a_paginationLiens);

	// numero des premier et dernier articles
	$smarty->assign('pagination_deb', $_SESSION['catalogue']['pagination']['articles']['goodprices']['page'] * $_SESSION['catalogue']['pagination']['articles']['goodprices']['nbElems'] + 1);
	if ($_SESSION['catalogue']['pagination']['articles']['goodprices']['page'] == $nbPages - 1) {
		$smarty->assign('pagination_fin', $nbArticles);
	}
	else {
		$smarty->assign('pagination_fin', ($_SESSION['catalogue']['pagination']['articles']['goodprices']['page'] + 1) * $_SESSION['catalogue']['pagination']['articles']['goodprices']['nbElems']);
	}
	// FIN - pagination des articles

	foreach ($articles_tri as $detail) {
		$article = new article();
		$article->fields = $tab_art = $detail;

		// articles de remplacement
		//$artRempl = $article->getArticlesRempl();

		// on s'assure de la presence de la photo
		switch ($catalogue['display']['mode']) {
			case 'list':
				if (empty($article->fields['image']) || !file_exists(realpath('.').'/photos/50x50/'.$article->fields['image'])) {
					$tab_art['image'] = $template_web_path.'gfx/empty_50x50.png';
				}
				else {
					$tab_art['image'] = '/photos/50x50/'.$tab_art['image'];
				}
				break;
			case 'icons':
				if (empty($article->fields['image']) || !file_exists(realpath('.').'/photos/100x100/'.$article->fields['image'])) {
					$tab_art['image'] = $template_web_path.'gfx/empty_100x100.png';
				}
				else {
					$tab_art['image'] = '/photos/100x100/'.$tab_art['image'];
				}
				break;
		}

		$prix = catalogue_getprixarticle($article);

		if ($prix > 0 || (!$_SESSION['dims']['connected'] && !$oCatalogue->getParams('cata_mode_B2C'))) {
			$prixaff = catalogue_afficherprix($prix, $a_tva[$article->fields['ctva']]);

			$prix = catalogue_formateprix($prix);
			$prixaff = catalogue_formateprix($prixaff);

			// degressif
			if (isset($_SESSION['catalogue']['prix_qte'][$article->fields['reference']])) {
				$tab_art['degressif'] = true;
				$tab_art['qte_degressif'] = intval($_SESSION['catalogue']['prix_qte'][$article->fields['reference']]['seuil_1']);
				$tab_art['prix_degressif'] = $_SESSION['catalogue']['prix_qte'][$article->fields['reference']]['pv_1'];
			}

			$tab_art['prix'] 	= $prix;
			$tab_art['prixaff'] = $prixaff;

			$tab_art['ispack'] = ($tab_art['type'] == article::TYPE_KIT);

            if(!$havePromo) {
                $sliders[$tab_art['id']] = $tab_art;
            }

			// article en promo ?
			if ($article->fields['pupromo_1'] > 0 && $article->fields['ddpromo'] < $ts && $article->fields['dfpromo'] > $ts) {
				$tab_art['promotion'] = true;
				$tab_art['prix_brut'] = catalogue_formateprix(catalogue_getprixarticle($article, 1, true));
			}

			// conditionnement
			$tab_art['cond'] = $article->getConditionnement();

			// unité de vente
			$tab_art['uvente'] = $article->fields[$uventeField];

			// article favori ?
			$tab_art['favori'] = $article->isFav();

			// lien vers la fiche article
			$tab_art['href'] = '/article/'.$article->fields['urlrewrite'].'.html';

			$catalogue['articles'][$tab_art['id']] = $tab_art;
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

$_SESSION['dims']['tpl_page']['TITLE'] = 'Good Prices';
$_SESSION['dims']['tpl_page']['META_DESCRIPTION'] = 'Good Prices';
$_SESSION['dims']['tpl_page']['META_KEYWORDS'] = 'goodprices, produits';
$_SESSION['dims']['tpl_page']['CONTENT'] = '';

// insertion du javascript jQuery
$smarty->assign('jquery', '');
