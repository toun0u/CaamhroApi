<?php

ini_set('memory_limit', -1);

if(!isset($_SESSION['catalogue']['param'])) $_SESSION['catalogue']['param'] = 0;
$rubriques = dims_load_securvalue('param', dims_const::_DIMS_NUM_INPUT, true, true, true,$_SESSION['catalogue']['param']);

if(!isset($_SESSION['catalogue']['display']['mode'])) $_SESSION['catalogue']['display']['mode'] = '';
$mode = dims_load_securvalue('mode', dims_const::_DIMS_CHAR_INPUT, true, true, true, $_SESSION['catalogue']['display']['mode'], 'icons');
if(!isset($_SESSION['catalogue']['display']['num'])) $_SESSION['catalogue']['display']['num'] = 0;
$num = dims_load_securvalue('num', dims_const::_DIMS_NUM_INPUT, true, true, true, $_SESSION['catalogue']['display']['num'], 5);

if(!isset($_SESSION['catalogue']['display']['aff_cata'])) $_SESSION['catalogue']['display']['aff_cata'] = $oCatalogue->getParams('cata_default_show_families');
$aff_cata = dims_load_securvalue('aff_cata', dims_const::_DIMS_NUM_INPUT, true, false, false, $_SESSION['catalogue']['display']['aff_cata']);
if(!isset($_SESSION['catalogue']['filtres'][$rubriques]['marque'])) $_SESSION['catalogue']['filtres'][$rubriques]['marque'] = 0;
$marque = dims_load_securvalue('marque', dims_const::_DIMS_NUM_INPUT, true, false, false, $_SESSION['catalogue']['filtres'][$rubriques]['marque']);

if(!isset($_SESSION['catalogue']['display']['tri'])) $_SESSION['catalogue']['display']['tri'] = 'stock';
$tri = dims_load_securvalue('tri', dims_const::_DIMS_CHAR_INPUT, true, true, true, $_SESSION['catalogue']['display']['tri']);

if (!isset($_SESSION['catalogue']['pagination']['articles']['catalogue']['nbElems'])) {
	$_SESSION['catalogue']['pagination']['articles'][$rubriques]['nbElems'] = $a_pagination_per_page[1];
	$_SESSION['catalogue']['pagination']['articles'][$rubriques]['page'] = 0;
}

// on envoie le mode de tri à smarty pour pouvoir le mettre en évidence
$smarty->assign('tri', $tri);

$catalogue = array('fam1' => array(), 'fam2' => array(), 'fam3' => array(), 'articles' => array(), 'nav' => array(), 'display' => array(), 'marques' => array('selected' => '', 'values' => array()), 'sliders' => array());

$nav = 'Catalogue';

$articles = array();

$profondeur = 0;
$filters_from_current_rubriques=array();

// Ajout des filtres permanents
$rs = $db->query('SELECT `id` FROM `dims_mod_cata_champ` WHERE `permanent` = 1');
while ($row = $db->fetchrow($rs)) {
	$filters_from_current_rubriques[$row['id']] = $row['id'];
}

if ($rubriques > 0) {
	$cata_famille = new cata_famille();
	$cata_famille->open($rubriques);

	if ($oCatalogue->getParams('cata_nav_style') == 'finder') {
		$smarty->assign('tpl_name', 'famille_liste');
	}
	elseif ($oCatalogue->getParams('cata_nav_style') == 'arbo') {
		$smarty->assign('tpl_name', 'catalogue_arbo');
	}

	// on va recuperer les filtres de la famille avec heritage des familles parentes
	$sql = '
		SELECT 	DISTINCT(id_champ)
		FROM 	dims_mod_cata_champ_famille
		WHERE 	id_famille in ('.str_replace(';',',', $cata_famille->fields['parents']) .','. $rubriques.')';

	$rs = $db->query($sql);
	while ($row = $db->fetchrow($rs)) {
		$filters_from_current_rubriques[$row['id_champ']]=$row['id_champ'];
	}
}

if (!empty($_SESSION['catalogue']['rubriques'])) {
	$rubriques = $_SESSION['catalogue']['rubriques'];
	$a_rubriques = explode(';', $familys['list'][$rubriques]['parents'] .';'. $rubriques);
	$profondeur = sizeof($a_rubriques);

	$rub0 = isset($a_rubriques[1]) ? $a_rubriques[1] : '';
	$rub1 = isset($a_rubriques[2]) ? $a_rubriques[2] : '';
	$rub2 = isset($a_rubriques[3]) ? $a_rubriques[3] : '';
	$rub3 = isset($a_rubriques[4]) ? $a_rubriques[4] : '';

	// famille racine pour le mode de visu arborescence
	$smarty->assign('cata_root_fam', $rub1);
}

// onglets
$famille_menu = 0;
$a_parents = explode(';',$familys['list'][$rubriques]['parents']);

if(count($a_parents) >= 3)
	$famille_menu = $a_parents[2];
else
	$famille_menu = $rubriques;

if (isset($familys['list'][$famille_menu]['color']) && $familys['list'][$famille_menu]['color']!='') {
	$colorfamily = $cata_famille->fields['color'];
	$colorfamily2 = $cata_famille->fields['color2'];
	$colorfamily3 = $cata_famille->fields['color3'];
	$colorfamily4 = $cata_famille->fields['color4'];
	$bg_image = $cata_famille->fields['bg_image'];
}
else {
	$colorfamily = "#A78EB6";
	$colorfamily2 = "#805F94";
	$colorfamily3 = "#F8EDFF";
	$colorfamily4 = "#805F94";
	$bg_image = "none";
}

$smarty->assign('colorfamily', $colorfamily);
$smarty->assign('colorfamily2', $colorfamily2);
$smarty->assign('colorfamily3', $colorfamily3);
$smarty->assign('colorfamily4', $colorfamily4);
$smarty->assign('bg_image', $bg_image);


if (!empty($rub0) && isset($familys['tree'][$rub0]) && is_array($familys['tree'][$rub0])) {
	foreach ($familys['tree'][$rub0] as $id_fam) {
		if (isset($familys['list'][$id_fam])) {
			$fam['id']	= $familys['list'][$id_fam]['id'];
			$fam['label']	= $familys['list'][$id_fam]['label'];
			if(!empty($familys['list'][$id_fam]['urlrewrite']))
				$fam['lien']	= $root_path.'/'.$familys['list'][$id_fam]['urlrewrite'];
			else
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

if (!empty($rub1) && isset($familys['tree'][$rub1]) && is_array($familys['tree'][$rub1])) {
	foreach ($familys['tree'][$rub1] as $id_fam) {
		if (isset($familys['list'][$id_fam])) {
			$fam['id']		= $familys['list'][$id_fam]['id'];
			$fam['label']	= $familys['list'][$id_fam]['label'];
			if(!empty($familys['list'][$id_fam]['urlrewrite']))
				$fam['lien']	= $root_path.'/'.$familys['list'][$id_fam]['urlrewrite'];
			else
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

if (!empty($rub2) && isset($familys['tree'][$rub2]) && is_array($familys['tree'][$rub2])) {
	foreach ($familys['tree'][$rub2] as $id_fam) {
		if (isset($familys['list'][$id_fam])) {
			$fam['id']		= $familys['list'][$id_fam]['id'];
			$fam['label']	= $familys['list'][$id_fam]['label'];
			if(!empty($familys['list'][$id_fam]['urlrewrite']))
				$fam['lien']	= $root_path.'/'.$familys['list'][$id_fam]['urlrewrite'];
			else
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

if (!empty($rub3) && isset($familys['tree'][$rub3]) && is_array($familys['tree'][$rub3])) {
	foreach ($familys['tree'][$rub3] as $id_fam) {
		if (isset($familys['list'][$id_fam])) {
			$fam['id']		= $familys['list'][$id_fam]['id'];
			$fam['label']	= $familys['list'][$id_fam]['label'];
			if(!empty($familys['list'][$id_fam]['urlrewrite']))
				$fam['lien']	= $root_path.'/'.$familys['list'][$id_fam]['urlrewrite'];
			else
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

// ----------------------------------------------------------------------------
// mode de visu 'arbo' avec des familles a afficher
// ----------------------------------------------------------------------------
if ( $oCatalogue->getParams('cata_nav_style') == 'arbo' && !empty($familys['tree'][$rubriques]) ) {
	// si on a des familles en-dessous, on les affiche, sinon on affiche les articles
	$smarty->assign('showRubs', true);

	// on construit la liste de toutes les sous-familles de chaque famille a afficher
	$famPhotos = array();
	foreach ($familys['tree'][$rubriques] as $idFam) {
		$a_famPhoto = array($idFam);

		$c = $idFam;
		while (isset($familys['tree'][$c])) {
			foreach ($familys['tree'][$c] as $f) {
				$a_famPhoto[] = $f;
				$c = $f;
			}
		}

		// on recherche une photo d'un article au hasard
		$photo = 'default.jpg';
		$sql = '
			SELECT 	DISTINCT(a.image)
			FROM 	dims_mod_cata_article a
			INNER JOIN 	dims_mod_cata_article_famille af
			ON 			af.id_article = a.id
			AND 		af.id_famille IN ('.implode(',', $a_famPhoto).')
			WHERE 	a.image != \'\'
			AND 	a.status = \''.article::STATUS_OK.'\'
			ORDER BY RAND() LIMIT 1';
		$rs = $db->query($sql);
		$row = $db->fetchrow($rs);
		if (file_exists(realpath('.').'/photos/100x100/'.$row['image'])) {
			$photo = $row['image'];
		}

		$famPhotos[] = array(
			'libelle' 	=> $familys['list'][$idFam]['label'],
			'url' 		=> $dims->getProtocol().$http_host.'/'.$familys['list'][$idFam]['urlrewrite'],
			'photo' 	=> $photo
			);
	}

	$smarty->assign('famPhotos', $famPhotos);

	ob_flush();
}
else {
	// on affiche le catalogue si on change d'onglet
	// if ( isset($_SESSION['catalogue']['onglet']) && $_SESSION['catalogue']['onglet'] != $a_rubriques[2] ) {
	// 	$_SESSION['catalogue']['display']['aff_cata'] = 1;
	// }
	if (isset($a_rubriques[2])) {
		$_SESSION['catalogue']['onglet'] = $a_rubriques[2];
	}
	// fin onglets

	$catalogue['display']['mode'] = $mode;
	$catalogue['display']['num'] = $num;
	$catalogue['display']['aff_cata'] = $aff_cata;

	// unité de vente
	$uventeField = 'uvente';
	if (!empty($_SESSION['catalogue']['client_id'])) {
		if (defined('_CATA_VARIANTE') && file_exists(DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/class_client_cplmt.php')) {
			include_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/class_client_cplmt.php';
			$obj_cli_cplmt = new client_cplmt();
			if ($obj_cli_cplmt->open($_SESSION['catalogue']['client_id'])) {
				if ($obj_cli_cplmt->fields['soldeur'] == 'Oui') {
					$uventeField = 'uventesolde';
				}
			}
		}
	}

	$a_keywords = array();
	$a_similar = array();
	$a_kwdref = array();


	$sql = "SELECT a.`id`, af.`id_famille`

		FROM 	`dims_mod_cata_famille` f

		INNER JOIN 	`dims_mod_cata_article_famille` af
		ON 			af.`id_famille` = f.`id`

		INNER JOIN 	`dims_mod_cata_article` a
		ON 			a.`id` = af.`id_article`
		AND 		a.`id_lang` = 1
		AND 		a.`published` = ".article::ARTICLE_PUBLISHED;

	if (isset($_SESSION['catalogue']['global_filter'])) {
		$sql .= ' AND a.`fields'.$_SESSION['catalogue']['global_filter']['filter_id'].'` = '.$_SESSION['catalogue']['global_filter']['filter_value'];
	}

	// Filtre sur les articles certiphyto
	if ( isset($_SESSION['catalogue']['enr_certiphyto']) && $_SESSION['catalogue']['enr_certiphyto'] == 0 ) {
		$sql .= ' AND a.`certiphyto` = 0';
	}

	$sql .= "
		WHERE 	(f.id=".$rubriques." OR f.`parents` LIKE '".$familys['list'][$rubriques]['parents'].";".$rubriques."%')
		AND 	f.`id_lang` = 1";

	$lst_art=0;
	$correspArtFam=array();

	$rs = $db->query($sql);
	while ($fields = $db->fetchrow($rs)) {
		$lst_art.=','.$fields['id'];

		$correspArtFam[$fields['id']]=$fields['id_famille'];
	}

	// on ne prend uniquement que les champs de filtres dont on a besoin
	// si pas de filtre possible, grande optimisation (à voir pour ne prendre que les filtres de la famille courante

	$list_champs_filters="";
	foreach ($filters_from_current_rubriques as $id_champ_filter) {
		$list_champs_filters.=", fields".$id_champ_filter;
	}

	$sql = "
		SELECT		a.id,
					a.label,
					a.reference,
					a.putarif_1,
					a.putarif_0,
					a.urlrewrite,
					a.marque,
					a.ctva,
					a.fam,
					a.ssfam,
					a.uvente,
					a.pupromo_1,
					a.type,
					a.cond,
					a.taxe_certiphyto,
					m.libelle AS marque_label".$list_champs_filters;

	if ( $_SESSION['dims']['connected'] && !empty($_SESSION['catalogue']['id_company']) ) {
		$sql .= ", s.`held_in_stock`, s.`stock`";
	}

	$sql .= "
		FROM		dims_mod_cata_article a";

	if ( $_SESSION['dims']['connected'] && !empty($_SESSION['catalogue']['id_company']) ) {
		$sql .= '
			INNER JOIN 	`dims_mod_cata_stocks` s
			ON 			s.`id_company` = '.$_SESSION['catalogue']['id_company'].'
			AND 		s.`id_article` = a.`id`';
	}

	$sql .= "
		LEFT JOIN	dims_mod_cata_marque m
		ON			m.id = a.marque

		WHERE		a.id IN(".$lst_art.")
		AND 		a.status = '".article::STATUS_OK."'";


	// si un marché est en cours, on regarde si il est restrictif
	if (isset($_SESSION['catalogue']['market'])) {
		$market = cata_market::getByCode($_SESSION['catalogue']['market']['code']);
		if ($market->hasRestrictions()) {
			$sql .= " AND a.id IN (".implode(',', $market->getRestrictions()).")";
		}
	}

	/*echo $sql;die();
	$dims_timer=dims::getinstance()->getTimer();
	$dims_stats=dims::getinstance()->getStats($db,$dims_timer,$dims_content='');
	dims_print_r($dims_stats);*/
	$rs = $db->query($sql);
	$a_idArt = array();
	/*$dims_timer=dims::getinstance()->getTimer();
	$dims_stats=dims::getinstance()->getStats($db,$dims_timer,$dims_content='');
	dims_print_r($dims_stats);*/
//die('r');

	$a_famFiltre = array_merge(cata_getAllChildren($familys, array($rubriques)), array($rubriques));

	$sliders = array();

	// Valeurs de filtres
	$filter_values = array();

	$condition_connected_OR_notBTOC=!$_SESSION['dims']['connected'] && !$oCatalogue->getParams('cata_mode_B2C');

	$article_model = new article();
	$article = new article();

	while ($fields = $db->fetchrow($rs)) {
		//$article = new article();
		//$article = clone $article_model;

		if (isset($correspArtFam[$fields['id']]))
			$fields['id_famille']=$correspArtFam[$fields['id']];

		$article->fields = $fields;

		// On vérifie qu'on peut consulter l'article en fonction des règles de filtrage
		// Soit l'article est tenu en stock, soit c'est un SPE mais avec du stock
		if ( $_SESSION['dims']['connected'] && !empty($_SESSION['catalogue']['id_company']) ) {
			if ( !$article->fields['held_in_stock'] && $article->fields['stock'] <= 0 ) {
				continue;
			}
		}

		// on arrete de calculer les prix pour tous les articles, on le fait au fur et a mesure de la navigation ou appel
		//$prix = catalogue_getprixarticle($article);
		$prix=$article->getprix(); // on veut savoir si on a un prix

		//echo $prix."<br>";
		//if ($prix > 0 || $condition_connected_OR_notBTOC) {
		if ($prix > 0 || $condition_connected_OR_notBTOC) {
			$a_idArt[$fields['id']] = $fields['id'];
			$fields['urlencode'] = urlencode($fields['reference']);
			$articles[$fields['id']] = $fields;
			$articles[$fields['id']]['puht'] = $prix;

			// marque
			if ( !empty($fields['marque']) && !isset($catalogue['marques']['values'][$fields['marque']]) ) {
				$catalogue['marques']['values'][$fields['marque']]['label'] = $fields['marque_label'];
				if ($fields['marque'] == $_SESSION['catalogue']['filtres'][$rubriques]['marque']) {
					$catalogue['marques']['selected'] = $fields['marque'];
				}
			}
			$articles[$fields['id']]['marque_label'] = !empty($catalogue['marques']['values'][$fields['marque']]) ? $catalogue['marques']['values'][$fields['marque']]['label'] : '';

			if (!is_null($fields['id_famille'])) {
				if (!isset($a_artFam[$fields['id_famille']])) $a_artFam[$fields['id_famille']] = 0;
				$a_artFam[$fields['id_famille']]++;
			}

			// on desactive les promos, on va le faire aussi sur tous les articles alors qu'on en affiche que qq dizaines
			// en plus les promos sont calcules plus bas à l'affichage...
			/*
			// article en promo ?
			if ($article->fields['pupromo_1'] > 0 && $article->fields['ddpromo'] < $ts && $article->fields['dfpromo'] > $ts) {
				$prix = catalogue_formateprix($prix);
				$prixaff = catalogue_formateprix($prixaff);

				$fields['prix'] 	= $prix;
				$fields['prixaff'] = $prixaff;
				$fields['promotion'] = true;
				$fields['prix_brut'] = catalogue_formateprix(catalogue_getprixarticle($article, 1, true));

				if (empty($article->fields['image']) || !file_exists(realpath('.').'/photos/100x100/'.$article->fields['image'])) {
					$fields['image'] = '/assets/images/frontoffice/'.$_SESSION['dims']['front_template_name'].'/gfx/empty_100x100.png';
				}
				else {
					$fields['image'] = '/photos/100x100/'.$article->fields['image'];
				}

				$fields['ispack'] = ($fields['type'] == article::TYPE_KIT);

				$sliders[$fields['id']] = $fields;
			}
			*/
			/*
			// Recherche des filtres en commun pour tous les articles
			$current_filters = array();

			for ($i = 1; $i <= 150; $i++) {
				if (!is_null($article->fields['fields'.$i])) {
					$current_filters[] = $i;
					$filter_values[$i][$article->fields['fields'.$i]] = 1;
				}
			}

			// Merge avec les filtres des autres articles
			if (isset($filters)) {
				$filters = array_unique(array_merge($filters, $current_filters));
			}
			else {
				$filters = $current_filters;
			}*/

			if (!empty($filters_from_current_rubriques)) {
				foreach ($filters_from_current_rubriques as $id_champ_filter) {
					if (!is_null($article->fields['fields'.$id_champ_filter])) {
						$filter_values[$id_champ_filter][$article->fields['fields'.$id_champ_filter]] = 1;
					}
				}
			}
		}

	}
	uasort($catalogue['marques']['values'], 'cata_orderByLabel');
	//die();
	$filters=$filters_from_current_rubriques;

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


	// filtrage des articles - epuration de la liste d'articles
	$search_filters = array();

	if (sizeof($_GET)) {
		foreach ($_GET as $key => $value) {
			if (preg_match('/^filter(?P<field_id>[\d]+)$/', $key, $matches)) {
				$$key = dims_load_securvalue($key, dims_const::_DIMS_CHAR_INPUT, true, false);
				if ($$key > 0) {
					$search_filters[$matches['field_id']] = $$key;
				}
			}
		}
	}

	$callback = function ($var) use ($search_filters) {
		$res = true;

		if (!empty($search_filters))
			foreach ($search_filters as $filter_id => $filter_values) {
				$res &= in_array($var['fields'.$filter_id], $filter_values);
			}

		return $res;
	};


	// on ne l'applique que si on a des filtres
	if (!empty($search_filters)) {
		$articles = array_filter($articles, $callback);
	}
	// FIN - filtrage des articles - epuration de la liste d'articles

	$a_filters = array();
	// Ouverture des filtres
	if (!empty($filters)) {
		require_once DIMS_APP_PATH.'modules/catalogue/include/class_champ.php';

		$rs = $db->query('SELECT * FROM `'.cata_champ::TABLE_NAME.'` WHERE `id` IN ('.implode(',', $filters).') AND `filtre` = 1');
		while ($row = $db->fetchrow($rs)) {
			$champ = new cata_champ();
			$champ->openFromResultSet($row);

			if ($champ->fields['type'] == cata_champ::TYPE_LIST) {
				// On n'affiche que les champs utilisables comme filtre
				if (isset($filter_values[$champ->get('id')])) {
					$tab_filter_values_ids = $filter_values[$champ->get('id')];
					if (!empty($tab_filter_values_ids)) {
						$filter_values_ids = array_keys($tab_filter_values_ids);
						$a_filter_values = array();

						foreach ($champ->getvaleurs($_SESSION['dims']['currentlang'], false, $filter_values_ids,true) as $champ_valeur) {
							//dims_print_r($champ_valeur);die();
							// Par défaut, toutes les valeurs de filtres sont désactivées
							// Elles seront récativées en fonction des articles filtrés
							// Les options cochées sont automatiquement activés
							$selected = isset($search_filters[$champ->get('id')]) && in_array($champ_valeur['id'], $search_filters[$champ->get('id')]);

							$a_filter_values[$champ_valeur['id']] = array(
								'label' 	=> $champ_valeur['valeur'],
								'selected' 	=> $selected,
								'disabled' 	=> !$selected
								);
						}

						if(count($a_filter_values) > 1){
							$a_filters[$champ->get('id')] = array(
								'filter' 	=> $champ->fields,
								'values' 	=> $a_filter_values
								);
						}
					}
				}
			}
			else {
				$tab_filter_values_ids = $filter_values[$champ->get('id')];
				if (!empty($tab_filter_values_ids)) {
					$filter_values_ids = array_keys($tab_filter_values_ids);
					$a_filter_values = array();

					foreach ($filter_values_ids as $champ_valeur) {
						// Par défaut, toutes les valeurs de filtres sont désactivées
						// Elles seront récativées en fonction des articles filtrés
						// Les options cochées sont automatiquement activés
						$selected = isset($search_filters[$champ->get('id')]) && in_array($champ_valeur, $search_filters[$champ->get('id')]);

						$a_filter_values[$champ_valeur] = array(
							'label' 	=> $champ_valeur,
							'selected' 	=> $selected,
							'disabled' 	=> !$selected
							);
					}

					if(count($a_filter_values) > 1){
						$a_filters[$champ->get('id')] = array(
							'filter' 	=> $champ->fields,
							'values' 	=> $a_filter_values
							);
					}
				}
			}

		}
	}


	// Recherche des filtres en commun pour tous les articles **filtrés**
	$filter_values_ok = array();

	foreach ($articles as $article) {
		/*
		for ($i = 1; $i <= 150; $i++) {
			if ( isset($a_filters[$i]) && !is_null($article['fields'.$i]) && isset($a_filters[$i]['values'][$article['fields'.$i]]) ) {
				$a_filters[$i]['values'][$article['fields'.$i]]['disabled'] = 0;
			}
		}*/
		foreach ($filters_from_current_rubriques as $id_champ_filter) {

			if ( isset($a_filters[$id_champ_filter]) && !is_null($article['fields'.$id_champ_filter]) && isset($a_filters[$id_champ_filter]['values'][$article['fields'.$id_champ_filter]]) ) {
				$a_filters[$id_champ_filter]['values'][$article['fields'.$id_champ_filter]]['disabled'] = 0;
			}
		}
	}

	if (sizeof($a_filters)) {
		$smarty->assign('a_filters', $a_filters);
	}


	$nbarticles = sizeof($articles);
	//die("i".$nbarticles);
	$smarty->assign('nb_total_articles', $nbarticles);

	$articles_tri = array();

	// tri des articles (on devrait le faire au debut de la selection
	if (isset($_SESSION['catalogue']['display']['tri']) &&
		$_SESSION['catalogue']['display']['tri']!='reference') {

		foreach ($articles as $id => $art) {
			switch ($_SESSION['catalogue']['display']['tri']) {
				/*case 'reference':
					$articles_tri[$art['reference']] = $art;
					break;*/
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

	}
	else {
		$articles_tri=$articles;
	}

	unset ($articles);
	$nbarticles = sizeof($articles_tri);
	// echo $nbarticles;
	// die();

	// redirection dans la fiche article si 1 seul resultat en recherche exacte
	if ($nbarticles == 1) {
		foreach ($articles_tri as $art) {
			if (isset($a_kwdref[$art['reference']])) {
				dims_redirect('/index.php?op=fiche_article&ref='.$art['reference']);
			}
		}
	}

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
			$p = dims_load_securvalue('p', dims_const::_DIMS_NUM_INPUT, true, false) - 1;
			$_SESSION['catalogue']['pagination']['articles'][$rubriques]['page'] = $p;
		}
		$articles_tri = cata_paginate('articles', $rubriques, $articles_tri);

		$smarty->assign('pagination_nbElem', $_SESSION['catalogue']['pagination']['articles'][$rubriques]['nbElems']);

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
		//echo sizeof($articles_tri);
		//dims_print_r($articles_tri);die();
		$article_model = new article();
		// envoi des articles au template
		foreach ($articles_tri as $detail) {
			//$article = new article();
			$article = clone $article_model;
			//dims_print_r($article);die();
			$article->fields = $tab_art = $detail;

			$prix = catalogue_getprixarticle($article);

			if ($prix > 0 || (!$_SESSION['dims']['connected'] && !$oCatalogue->getParams('cata_mode_B2C'))) {
				if (isset($a_tva[$article->fields['ctva']]))
					$prixaff = catalogue_afficherprix($prix, $a_tva[$article->fields['ctva']]);
				else
					$prixaff = catalogue_afficherprix($prix);

				$prix = catalogue_formateprix($prix);
				$prixaff = catalogue_formateprix($prixaff);

				// degressif
				if (isset($_SESSION['catalogue']['prix_qte'][$article->fields['reference']])) {
					$tab_art['degressif'] = true;
					if (isset($_SESSION['catalogue']['prix_qte'][$article->fields['reference']]['seuil_1'])) $tab_art['qte_degressif'] = intval($_SESSION['catalogue']['prix_qte'][$article->fields['reference']]['seuil_1']);
					if (isset($_SESSION['catalogue']['prix_qte'][$article->fields['reference']]['pv_1'])) $tab_art['prix_degressif'] = $_SESSION['catalogue']['prix_qte'][$article->fields['reference']]['pv_1'];
				}

				$tab_art['prix'] 	= $prix;
				$tab_art['prixaff'] = $prixaff;

				// Mettre le prix net en avant
				if (isset($_SESSION['catalogue']['prix_net_c'][$article->getReference()])) {
					$tab_art['prix_net'] = true;
				}

				$tab_art['ispack'] = ($tab_art['type'] == article::TYPE_KIT);

				// article en promo ?
				if ($article->fields['pupromo_1'] > 0 && $article->fields['ddpromo'] < $ts && $article->fields['dfpromo'] > $ts) {
					$tab_art['prix_brut'] = catalogue_formateprix(catalogue_getprixarticle($article, 1, true));

					// On n'affiche le prix barré que si son prix brut est supérieur au prix du client
					if ($tab_art['prix_brut'] > $prix) {
						$tab_art['promotion'] = true;
					}
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
				$tab_art['image'] = '/assets/images/frontoffice/'.$_SESSION['dims']['front_template_name'].'/gfx/empty_100x100.png';

				if (file_exists(realpath('.').'/photos/100x100/'.$article->getReference().'.jpg')) {
					$tab_art['image'] = '/photos/100x100/'.$article->getReference().'.jpg';
				}

				// conditionnement
				$tab_art['cond'] = $article->getConditionnement();

				// unité de vente
				$tab_art['uvente'] = $article->fields[$uventeField];

				// article favori ?
				// $tab_art['favori'] = $article->isFav();

				// lien vers la fiche article
				$tab_art['href'] = '/article/'.$article->fields['urlrewrite'].'.html';

				$catalogue['articles'][$tab_art['id']] = $tab_art;
				if(!$havePromo) {
					$sliders[$tab_art['id']] = $tab_art;
				}
				$i++;
			}
		}
	}
}

$data['CONTENT'] = ob_get_contents();
ob_end_clean();

if (isset($sliders)) {
	shuffle($sliders);
	$sliders = array_slice($sliders, 0, 15);

	$catalogue['sliders'] = $sliders;
}

$smarty->assign('catalogue', $catalogue);

/*************************************************/
/* Traitement du fils d'ariane					 */
/* Modif Pat du 22/08/2014, pas de trace de fils */
/* d'ariane en front							 */
/*************************************************/

if (isset($a_parents) && !empty($a_parents)) {
	$ariane=array();
	foreach ($a_parents as $elemfamariane) {
		if (isset($familys['list'][$elemfamariane]) && $familys['list'][$elemfamariane]['depth']>1) {

			$elem=array();
			$elem['id']		= $familys['list'][$elemfamariane]['id'];
			$elem['type']	= 3;
			$elem['label']	= $familys['list'][$elemfamariane]['label'];
			$elem['link']	= "/".$familys['list'][$elemfamariane]['urlrewrite'];
			$ariane[]=$elem;
		}
	}

	// on ajoute le courant
	if (isset($familys['list'][$rubriques]) && $familys['list'][$rubriques]['depth']>1) {
		$elem=array();
		$elem['id']		= $familys['list'][$rubriques]['id'];
		$elem['type']	= 3;
		$elem['label']	= $familys['list'][$rubriques]['label'];
		$elem['link']	= "/".$familys['list'][$rubriques]['urlrewrite'];
		$ariane[]=$elem;
	}
	$_SESSION['dims']['tpl_page']['ARIANE'] = $ariane;

}

if( isset($cata_famille) && $cata_famille->get('meta_title') != '' ) {
	$_SESSION['dims']['tpl_page']['TITLE'] = $cata_famille->get('meta_title');
}
else  {
	$_SESSION['dims']['tpl_page']['TITLE'] = $familys['list'][$rubriques]['label'];
}
if ( isset($cata_famille) && $cata_famille->get('meta_description') != '') {
	$_SESSION['dims']['tpl_page']['META_DESCRIPTION'] = $cata_famille->get('meta_description');
}
else {
	$_SESSION['dims']['tpl_page']['META_DESCRIPTION'] = 'Visualiser notre catalogue de produits a travers nos différentes familles : '.$familys['list'][$rubriques]['label'];
}
$_SESSION['dims']['tpl_page']['META_KEYWORDS'] = 'Catalogue, produits, articles, '.$familys['list'][$rubriques]['label'];
$_SESSION['dims']['tpl_page']['CONTENT'] = '';

// insertion du javascript jQuery
$smarty->assign('jquery', '');
