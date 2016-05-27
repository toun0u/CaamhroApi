<?php
ob_start();

if(!isset($_SESSION['catalogue']['display']['mode'])) $_SESSION['catalogue']['display']['mode'] = '';
$mode = dims_load_securvalue('mode', dims_const::_DIMS_CHAR_INPUT, true, true, true, $_SESSION['catalogue']['display']['mode'], 'icons');
if(!isset($_SESSION['catalogue']['display']['num'])) $_SESSION['catalogue']['display']['num'] = 0;
$num = dims_load_securvalue('num', dims_const::_DIMS_NUM_INPUT, true, true, true, $_SESSION['catalogue']['display']['num'], 5);

if(!isset($_SESSION['catalogue']['display']['aff_cata'])) $_SESSION['catalogue']['display']['aff_cata'] = 0;
$aff_cata = dims_load_securvalue('aff_cata', dims_const::_DIMS_NUM_INPUT, true, false, false, $_SESSION['catalogue']['display']['aff_cata']);

$motscles = dims_load_securvalue('motscles', dims_const::_DIMS_CHAR_INPUT, true, false);
$rubriques = dims_load_securvalue('rubriques', dims_const::_DIMS_CHAR_INPUT, true, false);

if(!isset($_SESSION['catalogue']['filtres'][$rubriques]['marque'])) $_SESSION['catalogue']['filtres'][$rubriques]['marque'] = 0;
$marque = dims_load_securvalue('marque', dims_const::_DIMS_NUM_INPUT, true, false, false, $_SESSION['catalogue']['filtres'][$rubriques]['marque']);

$smarty->assign('rubriques', '');
$smarty->assign('rubparam', '');

if (!empty($rubriques)) {
	$smarty->assign('rubriques', $rubriques);
	$smarty->assign('rubparam', '&rubriques='.$rubriques);
}

$catalogue = array('fam1' => array(), 'fam2' => array(), 'fam3' => array(), 'articles' => array(), 'nav' => array(), 'display' => array(), 'marques' => array('selected' => '', 'values' => array()), 'sliders' => array());


$nav = 'ecoprod';

$articles = array();

// chargement des correspondaces de mots cles (ex: rtte => ramette)
loadCorrespKeywords();

// on nettoie la recherche
$motscles = str_replace("'", ' ', trim(catalogue_cleanstring($motscles)));
// on bloque les tentatives de recherche sur "%"
if (strstr($motscles, '%')) $motscles = '';

$a_rubriques = explode(';', $familys['list'][$rubriques]['parents'] .';'. $rubriques);
$profondeur = sizeof($a_rubriques);

// onglets
$famille_menu = 0;
$a_parents = explode(';',$familys['list'][$rubriques]['parents']);

if(count($a_parents) >= 3)
    $famille_menu = $a_parents[2];
else
    $famille_menu = $rubriques;

$colorindex     = "#93c645";
$colorfamily    = "#93c645";



if (isset($familys['list'][$famille_menu]['color']) && $familys['list'][$famille_menu]['color']!='') {
	$cata_famille= new cata_famille();
	$cata_famille->open($famille_menu);
//    $colorindex = $cata_famille->fields['color'];
	$colorfamily2 = $cata_famille->fields['color2'];
}

$smarty->assign('colorindex', $colorindex);
$smarty->assign('colorfamily', $colorfamily);
$smarty->assign('colorfamily2', $colorfamily2);

// on affiche le catalogue si on change d'onglet
// if ( isset($_SESSION['catalogue']['onglet']) && $_SESSION['catalogue']['onglet'] != 'ecoprod' ) {
// 	$_SESSION['catalogue']['display']['aff_cata'] = 1;
// }
$_SESSION['catalogue']['onglet'] = 'ecoprod';
// fin onglets

$catalogue['display']['mode'] = $mode;
$catalogue['display']['num'] = $num;
$catalogue['display']['aff_cata'] = $aff_cata;

$a_similar = array();
$a_kwdref = array();

$sqlart = ' AND a.dev_durable = 1';

if ($_SESSION['catalogue']['cata_restreint'] && $_SESSION['session_adminlevel'] < _DIMS_ID_LEVEL_PURCHASERESP) {
    $sql = "SELECT	a.id,
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

            LEFT JOIN	dims_mod_cata_marque m
            ON			m.id = a.marque

            WHERE	1
                    $sqlart
                    $sqlpage
            GROUP BY a.reference";
}
else {
    $sql = "SELECT	a.id,
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

            LEFT JOIN	dims_mod_vpc_selection s
            ON			s.ref_article = a.reference
            AND			s.ref_client = '{$_SESSION['catalogue']['code_client']}'

            LEFT JOIN	dims_mod_cata_marque m
            ON			m.id = a.marque

            WHERE	1
                    $sqlart
                    $sqlpage
            GROUP BY a.reference";
}
$res = $db->query($sql);
$a_idArt = array();
$a_famFiltre = array_merge(cata_getAllChildren($familys, array($rubriques)), array($rubriques));

$sliders = array();

while ($fields = $db->fetchrow($res)) {
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
				if ($fields['marque'] == $_SESSION['catalogue']['filtres'][$rubriques]['marque']) {
					$catalogue['marques']['selected'] = $fields['marque'];
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

foreach ($a_fams as $rubid => $nbart) {
	foreach (array_merge(explode(';', $familys['list'][$rubid]['parents']), array($rubid)) as $idParent) {
		switch ($familys['list'][$idParent]['depth']) {
			case 2:
				$catalogue['fam1'][$idParent] = array(
					'id'		=> $familys['list'][$idParent]['id_famille'],
					'label'		=> $familys['list'][$idParent]['label'],
					'lien'		=> '?op=ecoprod&rubriques='.$idParent,
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
						'lien'		=> '?op=ecoprod&rubriques='.$idParent,
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
						'lien'		=> '?op=ecoprod&rubriques='.$idParent,
						'sel'		=> ($idParent == $rubriques) ? 'selected' : '',
						'in_path'	=> (in_array($idParent, explode(';', $familys['list'][$rubriques]['parents']))) ? 'selected' :''
						);
				}
				break;
		}
	}
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
	if (!empty($_SESSION['catalogue']['filtres'][$rubriques]['marque']) && $fields['marque'] != $_SESSION['catalogue']['filtres'][$rubriques]['marque']) {
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
			dims_redirect("/index.php?op=fiche_article&ref={$art['reference']}");
		}
	}
}

$nbRubs = (sizeof($a_fams)) ? sizeof($a_fams) : 1;

$nbArticles = sizeof($articles_tri);
$smarty->assign('nb_articles', $nbArticles);

if (!empty($articles_tri)) {
	$tab_art = array();

	// pagination des articles
	if (isset($_GET['elemsPerPage'])) {
		$elemsPerPage = dims_load_securvalue('elemsPerPage', dims_const::_DIMS_NUM_INPUT, true, false);
		$_SESSION['catalogue']['pagination']['articles']['ecoprod'.$rubriques]['nbElems'] = $elemsPerPage;
		$_SESSION['catalogue']['pagination']['articles']['ecoprod'.$rubriques]['page'] = 0;
	}
	if (isset($_GET['p'])) {
		$page = dims_load_securvalue('p', dims_const::_DIMS_NUM_INPUT, true, false) - 1;
		$_SESSION['catalogue']['pagination']['articles']['ecoprod'.$rubriques]['page'] = $page;
	}
	$articles_tri = cata_paginate('articles', 'ecoprod'.$rubriques, $articles_tri);

    $smarty->assign('pagination_nbElem', $_SESSION['catalogue']['pagination']['articles']['ecoprod'.$rubriques]['nbElems']);

	// calcul du nombre de pages
	$nbPages = ceil($nbArticles / $_SESSION['catalogue']['pagination']['articles']['ecoprod'.$rubriques]['nbElems']);

	$a_paginationLiens = cata_getPaginationLinks('articles', 'ecoprod'.$rubriques, $nbPages);
	$smarty->assign('pagination_liens', $a_paginationLiens);

	// numero des premier et dernier articles
	$smarty->assign('pagination_deb', $_SESSION['catalogue']['pagination']['articles']['ecoprod'.$rubriques]['page'] * $_SESSION['catalogue']['pagination']['articles']['ecoprod'.$rubriques]['nbElems'] + 1);
	if ($_SESSION['catalogue']['pagination']['articles']['ecoprod'.$rubriques]['page'] == $nbPages - 1) {
		$smarty->assign('pagination_fin', $nbArticles);
	}
	else {
		$smarty->assign('pagination_fin', ($_SESSION['catalogue']['pagination']['articles']['ecoprod'.$rubriques]['page'] + 1) * $_SESSION['catalogue']['pagination']['articles']['ecoprod'.$rubriques]['nbElems']);
	}
	// FIN - pagination des articles

	foreach ($articles_tri as $detail) {
		$article = new article();
		$article->fields = $tab_art = $detail;

		// articles de remplacement
		//$artRempl = $article->getArticlesRempl();

		$prix = catalogue_getprixarticle($article);

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

		// article en promo ?
		if (isset($_SESSION['catalogue']['promo']['unlocked']["'".$article->fields['reference']."'"])) {
			$tab_art['promotion'] = true;
			$tab_art['prix_brut'] = catalogue_formateprix(catalogue_getprixarticle($article, 1, true));
		}

		if ($prix > 0) {
			$prixaff = catalogue_afficherprix($prix, $a_tva[$article->fields['ctva']]);

			$prix = catalogue_formateprix($prix);
			$prixaff = catalogue_formateprix($prixaff);

			$tab_art['prix'] 	= $prix;
			$tab_art['prixaff'] = $prixaff;

			if (empty($article->fields['image']) || !file_exists(realpath('.').'/photos/100x100/'.$article->fields['image'])) {
				$tab_art['image'] = 'empty.jpg';
			}

            if($tab_art['qte'] > 0) {
                $sliders[$tab_art['id']] = $tab_art;
            }

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
$smarty->assign('ecoprod', 1);

$page['TITLE'] = 'Nos éco-produit';
$page['META_DESCRIPTION'] = 'Soucieux de l\'environnement ? Profitez de nos éco-produit';
$page['META_KEYWORDS'] = 'écologique, produits, articles';
$page['CONTENT'] = '';

// insertion du javascript jQuery
$smarty->assign('jquery', '');
