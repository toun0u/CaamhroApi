<?php

$artid = dims_load_securvalue('artid', dims_const::_DIMS_NUM_INPUT, true, false, true);

// unité de vente
$uventeField = 'uvente';
if (!empty($_SESSION['catalogue']['client_id']) && defined('_CATA_VARIANTE') && file_exists(DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/class_client_cplmt.php')) {
	include_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/class_client_cplmt.php';
	$obj_cli_cplmt = new cata_client_cplmt();
	if ($obj_cli_cplmt->open($_SESSION['catalogue']['client_id'])) {
		if ($obj_cli_cplmt->fields['soldeur'] == 'Oui') {
			$uventeField = 'uventesolde';
		}
	}
}

$article = array();
if (!empty($artid)) {
	// si un marché est en cours, on regarde si il est restrictif
	// et si l'article fait partie de la liste
	if (isset($_SESSION['catalogue']['market'])) {
		$market = cata_market::getByCode($_SESSION['catalogue']['market']['code']);
		if ($market->hasRestrictions()) {
			$a_restrictions = $market->getRestrictions();
			// si c'est pas le cas, on redirige sur la page d'accueil
			if (!isset($a_restrictions[$artid])) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptEnv().'?op=home');
			}
		}
	}

	$art = new article();
	$art->open($artid);
	$article = $art->fields;

	// On vérifie qu'on peut consulter l'article en fonction des règles de filtrage
	// Soit l'article est tenu en stock, soit c'est un SPE mais avec du stock
	if ($_SESSION['dims']['connected']) {
		if (isset($_SESSION['catalogue']['id_company'])) {
			if ( !$art->isHeldInStock($_SESSION['catalogue']['id_company']) && $art->getStockTotal($_SESSION['catalogue']['id_company']) <= 0 ) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptEnv().'?op=home');
			}
		}
		else {
			if ( !$art->isHeldInStock() && $art->getStockTotal() <= 0 ) {
				dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptEnv().'?op=home');
			}
		}
	}

	// Dans le cas d'un article phytosanitaire, on vérifie que le client a bien un certiphyto
	if ( $art->get('certiphyto') && isset($_SESSION['catalogue']['enr_certiphyto']) && $_SESSION['catalogue']['enr_certiphyto'] == 0 ) {
		dims_redirect($dims->getProtocol().$dims->getHttpHost().'/'.$dims->getScriptEnv().'?op=home');
	}


	$pu_ht      = catalogue_getprixarticle($art) * (1 - $remises[1] / 100);

	if (isset($a_tva[$art->fields['ctva']])) {
		$pu_ttc = $pu_ht * (1 + $a_tva[$art->fields['ctva']] / 100);
	} else {
		$pu_ttc = $pu_ht;
	}

	$article['prix']        		= catalogue_formateprix($pu_ht);
	$article['prix_ttc']    		= catalogue_formateprix($pu_ttc);
	$article['taxe_certiphyto'] 	= catalogue_formateprix($art->get('taxe_certiphyto'));

	// Mettre le prix net en avant
	if (isset($_SESSION['catalogue']['prix_net_c'][$art->getReference()])) {
		$article['prix_net'] = true;
	}

	// article en promo ?
	if ($article['pupromo_1'] > 0 && $article['ddpromo'] < $ts && $article['dfpromo'] > $ts) {
		$article['prix_brut'] = catalogue_formateprix(catalogue_getprixarticle($art, 1, true));
		if ($article['prix_brut'] > $article['prix']) {
			$article['promotion'] = true;
		}
	}

	// tarif dégressif ?
	if (isset($_SESSION['catalogue']['prix_qte_m'][$art->fields['reference']])) {
		$article['degressifs'] = array();
		foreach ($_SESSION['catalogue']['prix_qte_m'][$art->fields['reference']] as $qte_degr => $pu_degr) {
			if ($pu_degr < $pu_ht) {
				$article['degressifs'][$qte_degr] = catalogue_formateprix($pu_degr);
			}
		}
		ksort($article['degressifs']);
	}
	elseif (isset($_SESSION['catalogue']['prix_qte_c'][$art->fields['reference']])) {
		$article['degressifs'] = array();
		foreach ($_SESSION['catalogue']['prix_qte_c'][$art->fields['reference']] as $qte_degr => $pu_degr) {
			if ($pu_degr < $pu_ht) {
				$article['degressifs'][$qte_degr] = catalogue_formateprix($pu_degr);
			}
		}
		ksort($article['degressifs']);
	}
	elseif (isset($_SESSION['catalogue']['prix_qte'][$art->fields['reference']])) {
		$article['degressifs'] = array();
		foreach ($_SESSION['catalogue']['prix_qte'][$art->fields['reference']] as $qte_degr => $pu_degr) {
			if ($pu_degr < $pu_ht) {
				$article['degressifs'][$qte_degr] = catalogue_formateprix($pu_degr);
			}
		}
		ksort($article['degressifs']);
	}


	// conditionnement
	$article['cond'] = $art->getConditionnement();

	// unité de vente
	$article['uvente'] = $art->fields[$uventeField];

	// marque
	$article['marque'] = $art->getMarqueLabel();

	$smarty->assign('jquery', '');

	// on s'assure de la presence de la photo
	$article['image'] = '/assets/images/frontoffice/'.$_SESSION['dims']['front_template_name'].'/gfx/empty_300x300.png';
	if (file_exists(realpath('.').'/photos/300x300/'.$art->getReference().'.jpg')) {
		$article['image'] = '/photos/300x300/'.$art->getReference().'.jpg';
	}

	$fams = $art->getFamilles();
	$fams_keys = array_keys($fams);
	$article['categ'] = $familys['list'][$fams_keys[0]]['label'];

	// couleurs de la famille de l'article
	$colorfamily = "#A78EB6";
	$colorfamily2 = "#805F94";
	$colorfamily3 = "#E3D5EC";
	$colorfamily4 = "#E3D5EC";

	if ($familys['list'][$fams_keys[0]]['color'] != '') {
		$colorfamily = $familys['list'][$fams_keys[0]]['color'];
	}
	if ($familys['list'][$fams_keys[0]]['color2'] != '') {
		$colorfamily2 = $familys['list'][$fams_keys[0]]['color2'];
	}
	if ($familys['list'][$fams_keys[0]]['color3'] != '') {
		$colorfamily3 = $familys['list'][$fams_keys[0]]['color3'];
	}
	if ($familys['list'][$fams_keys[0]]['color4'] != '') {
		$colorfamily4 = $familys['list'][$fams_keys[0]]['color4'];
	}

	$smarty->assign('colorfamily', $colorfamily);
	$smarty->assign('colorfamily2', $colorfamily2);
	$smarty->assign('colorfamily3', $colorfamily3);
	$smarty->assign('colorfamily4', $colorfamily4);

	$article['champsDyn'] = $art->getChampsDyn();

	$article['ispack'] = ($article['type'] == article::TYPE_KIT);

	$article['webask'] = ($art->fields['shipping_costs'] == article::SHIPPING_COST_DEVIS);
	if($article['webask']) {
		require_once(DIMS_APP_PATH . "/modules/wce/include/classes/class_wce_site.php");
		$wcesite = new wce_site($db);
		$extraparams = '';
		$webasklink = $wcesite->getArticleByObject('gescom', 'Gescom - demande de devis', $extraparams);
		$webasklink.= '/index.php?t='.article::MY_GLOBALOBJECT_CODE;

		if(!empty($extraparams)) {
			$webasklink .= $extraparams;
		}

		$webasklink .= '&o='.$art->get('id');

		$article['webasklink'] = $webasklink;
	}

	foreach($art->getPackagedArticle() as $packagedArt) {
		$article['packagedArticle'][] = $packagedArt->fields;
	}

	// Champs libres
	$champs_libres = array();
	foreach($fams as $fam){
		$champs_libres += $fam->getChampsLibre("",true); #Le += permet de garder les clefs numériques telles quelles
	}
	$champs_libres = cata_champ::completeListOfValuesFor($champs_libres);

	$a_cl = array();
	foreach ($champs_libres as $cl) {
		$a_cl[$cl->get('id')] = $cl->fields;
		if (!is_null($cl->getLightAttribute('values'))) {
			$a_cl[$cl->get('id')]['values'] = $cl->getLightAttribute('values');
		}
	}
	$smarty->assign('champs_libres', $a_cl);


	// Sous-articles
	$subs = article::find_by(array(
		'id_article' 	=> $art->get('id'),
		'id_lang' 		=> $art->get('id_lang'),
		'status' 		=> article::STATUS_OK
		)," ORDER BY label ");

	if (!is_null($subs)) {
		// On ajoute par défaut l'article courant
		$a_sub_articles = array(
			$art->getId() => array(
				'id' 			=> $art->getId(),
				'article_id' 	=> $art->get('id'),
				'reference' 	=> $art->get('reference'),
				'label'		 	=> $art->get('label'),
				'price'		 	=> catalogue_formateprix(catalogue_getprixarticle($art)).' €',
				'stock'		 	=> $art->get('qte'),
				'description'	=> str_replace("\n", "", addslashes($art->get('description')))
				));
		// Ajout des champs libres
		foreach ($a_cl as $champ) {
			$a_sub_articles[$art->getId()]['fields'.$champ['id']] = $art->get('fields'.$champ['id']);
		}

		foreach ($subs as $sub_article_id => $sub_article) {
			$a_sub_articles[$sub_article_id] = array(
				'id'		 	=> $sub_article_id,
				'article_id'	=> $sub_article->get('id'),
				'reference' 	=> $sub_article->fields['reference'],
				'label' 		=> $sub_article->fields['label'],
				'price' 		=> catalogue_formateprix(catalogue_getprixarticle($sub_article)).' €',
				'stock' 		=> $sub_article->fields['qte'],
				'description' 	=> str_replace("\n", "", addslashes($sub_article->fields['description']))
				);
			// Ajout des champs libres
			foreach ($a_cl as $champ) {
				$a_sub_articles[$sub_article_id]['fields'.$champ['id']] = $sub_article->get('fields'.$champ['id']);
			}
		}
		$smarty->assign('subArticles', $a_sub_articles);
	}

	// Chargement de la liste des types de liens
	$a_link_types = array();
	foreach (link_type::all() as $link_type) {
		$a_link_types[$link_type->get('code')] = $link_type->fields;
	}
	$smarty->assign('a_link_types', $a_link_types);

	// Articles rattachés
	$linked_articles = $art->getLinkedArticles();

	foreach ($linked_articles as $linkType => $linkedArticles) {
		foreach ($linkedArticles as $idx => $linkedArticle) {
			// Remove priceless linked articles
			if (catalogue_getprixarticle($linkedArticle) == 0) {
				unset($linked_articles[$linkType][$idx]);
			}
		}
	}

	// Tri des articles rattachés par position de type de lien
	uksort($linked_articles, function($a, $b) use ($a_link_types) {
		if ($a_link_types[$a]['position'] == $a_link_types[$b]['position']) {
			return 0;
		}
		return $a_link_types[$a]['position'] < $a_link_types[$b]['position'] ? -1 : 1;
	});

	// Photos des articles rattachés
	foreach ($linked_articles as $link_type => $articles) {
		foreach ($articles as $id_article => $linked_article) {
			if (file_exists(realpath('.').'/photos/50x50/'.$linked_article->getReference().'.jpg')) {
				$linked_articles[$link_type][$id_article]->image = '/photos/50x50/'.$linked_article->getReference().'.jpg';
			}
			else {
				$linked_articles[$link_type][$id_article]->image = '/assets/images/frontoffice/'.$_SESSION['dims']['front_template_name'].'/gfx/empty_50x50.png';
			}
		}
	}

	$article['linked_articles'] = $linked_articles;

	reset($fams);
	$family = current($fams);
	$a_parents = explode(';', $family->fields['parents']);
	$a_parents[] = $family->fields['id'];
	if (!empty($a_parents)) {
		$ariane = array();
		foreach($a_parents as $elemfamariane) {
			if(isset($familys['list'][$elemfamariane]) && $familys['list'][$elemfamariane]['depth'] > 1) {
				$ariane[] = array(
					'id'    => $familys['list'][$elemfamariane]['id'],
					'type'  => 3,
					'label' => $familys['list'][$elemfamariane]['label'],
					'link'  => "/".$familys['list'][$elemfamariane]['urlrewrite'],
				);
			}
		}
		$_SESSION['dims']['tpl_page']['ARIANE'] = $ariane;
	}

	// Infos de débug du filtrage des articles (que pour le compte de Stéphan : 218589)
	if ($_SESSION['dims']['connected'] && $_SESSION['dims']['userid'] == 14610) {
		$debug_text = '';

		require_once DIMS_APP_PATH.'modules/catalogue/include/class_company.php';

		if (isset($_SESSION['catalogue']['id_company'])) {
			$company = new cata_company();
			$company->open($_SESSION['catalogue']['id_company']);

			$debug_text .= "Code société du client : ".$company->get('code')."<br>";
			$stock_detail = $art->getStockDetail($_SESSION['catalogue']['id_company']);
		}
		else {
			$debug_text .= "Code société du client : Aucun<br>";
			$stock_detail = $art->getStockDetail();
		}
		foreach ($stock_detail as $stock) {
			$company = new cata_company();
			$company->open($stock->fields['id_company']);

			$debug_text .= '<h6>'.$company->get('code').'</h6>';
			$debug_text .= '- Tenu en stock : '.(($stock->fields['held_in_stock']) ? 'OUI' : 'NON');
			$debug_text .= '<br>- Quantité en stock : '.$stock->fields['stock'];
		}

		$smarty->assign('debug_text', $debug_text);
	}
}

$references = array();
foreach ($art->getReferences() as $reference) {
	$references[] = array(
		'id'        => $reference->fields['id'],
		'label'     => $reference->fields['name'],
		'link'      => $reference->getLink(),
		'video'     => ($reference->fields['type'] == article_reference::TYPE_VIDEO),
		'pdf'       => ($reference->fields['type'] == article_reference::TYPE_DOC && substr($reference->getLink(), -3) == 'pdf'),
		'image'     => (in_array(substr($reference->getLink(), -4), array('.png', '.jpg', 'jpeg'))),
	);
}

$article['references'] = $references;

if ($art->get('meta_title') != '') {
	$_SESSION['dims']['tpl_page']['TITLE'] = $art->get('meta_title');
}
else {
	$_SESSION['dims']['tpl_page']['TITLE'] = $art->getLabel().' '.$art->getReference();
}
if ($art->get('meta_description') != '') {
	$_SESSION['dims']['tpl_page']['META_DESCRIPTION'] = $art->get('meta_description');
}
else {
	$_SESSION['dims']['tpl_page']['META_DESCRIPTION'] = $art->getLabel().' '.$art->getReference();
}
$_SESSION['dims']['tpl_page']['META_KEYWORDS'] = 'fiche, produits, articles / '.$art->getReference();
$_SESSION['dims']['tpl_page']['CONTENT'] = '';

$smarty->assign('article', $article);

// selection de l'onglet courant
$tab = dims_load_securvalue('tab', dims_const::_DIMS_CHAR_INPUT, true, false);
$smarty->assign('tab', $tab);
