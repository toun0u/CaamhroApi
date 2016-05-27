<?php

// recherche du template
$lstwcemods = $dims->getWceModules();
$wce_module_id = (!empty($lstwcemods)) ? current($lstwcemods) : 0;

$headings = wce_getheadings($wce_module_id);
$headingid = (isset($headings['tree'][0][0])) ? $headings['tree'][0][0] : 0;

$site_template_name = (!empty($headings['list'][$headingid]['template'])) ? $headings['list'][$headingid]['template'] : 'default';
$smartyobject->assign('site_template_name', $site_template_name);
$template_web_path = '/templates/frontoffice/'.$site_template_name.'/';
// FIN - recherche du template


include_once DIMS_APP_PATH.'/modules/catalogue/include/class_promotion.php';

$uventeField = 'uvente';
if (defined('_CATA_VARIANTE') && file_exists(DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/class_client_cplmt.php')) {
	include_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/class_client_cplmt.php';
	$isSoldeur = false;
	if(isset($_SESSION['catalogue']['client_id'])) {
		$obj_cli_cplmt = new cata_client_cplmt();
		if ($obj_cli_cplmt->open($_SESSION['catalogue']['client_id'])) {
			$isSoldeur = ($obj_cli_cplmt->fields['soldeur'] == 'Oui');
			if ($isSoldeur) {
				$uventeField = 'uventesolde';
			}
		}
	}
}


foreach (cata_promotion::allActives('date_fin') as $promo) {
	global $a_tva;

	$art_list = array();
	$slider = array();
	$slider['id'] = md5(uniqid(time()));
	$slider['nom'] = 'promos';

	$today = dims_createtimestamp();

	if (isset($_SESSION['catalogue']['cata_restreint']) && $_SESSION['catalogue']['cata_restreint'] && $_SESSION['session_adminlevel'] < _DIMS_ID_LEVEL_PURCHASERESP) {
	    $sql = "
	        SELECT  a.*,
	                a.page AS numpage,
	                s.selection,
	                af.id_famille,
	                m.libelle AS marque_label

	        FROM    dims_mod_cata_article a

	        INNER JOIN 	dims_mod_cata_promotion_article pa
	        ON 			pa.ref_article = a.reference
	        AND 		pa.id_promo = ".$promo->get('id')."

	        INNER JOIN 	dims_mod_cata_promotions p
	        ON 			p.id = pa.id_promo
	        AND 		p.active = 1
	        AND 		p.date_debut < $today
	        AND 		p.date_fin > $today

	        INNER JOIN  dims_mod_vpc_selection s
	        ON          s.ref_article = a.reference
	        AND         s.ref_client = '{$_SESSION['catalogue']['code_client']}'

            LEFT JOIN   dims_mod_cata_article_famille af
            ON          af.id_article = a.id

	        LEFT JOIN   dims_mod_cata_marque m
	        ON          m.id = a.marque

	        WHERE   promo  = 'oui'
	        AND     $today > ddpromo
	        AND     $today < dfpromo
	        AND     a.published = 1
	        GROUP BY a.reference
	        ORDER BY RAND()";
	}
	else {
	    $sql = "
	        SELECT  a.*,
	                a.page AS numpage,
	                s.selection,
	                af.id_famille,
	                m.libelle AS marque_label

	        FROM    dims_mod_cata_article a

	        INNER JOIN 	dims_mod_cata_promotion_article pa
	        ON 			pa.ref_article = a.reference
	        AND 		pa.id_promo = ".$promo->get('id')."

	        INNER JOIN 	dims_mod_cata_promotions p
	        ON 			p.id = pa.id_promo
	        AND 		p.active = 1
	        AND 		p.date_debut < $today
	        AND 		p.date_fin > $today

            LEFT JOIN   dims_mod_cata_article_famille af
            ON          af.id_article = a.id

	        LEFT JOIN   dims_mod_vpc_selection s
	        ON          s.ref_article = a.reference
	        AND         s.ref_client = '{$_SESSION['catalogue']['code_client']}'

	        LEFT JOIN   dims_mod_cata_marque m
	        ON          m.id = a.marque

	        WHERE   promo  = 'oui'
	        AND     $today > ddpromo
	        AND     $today < dfpromo
	        AND     a.published = 1
	        GROUP BY a.reference
	        ORDER BY RAND()";
	}
	$res = $db->query($sql);

	while ($fields = $db->fetchrow($res)) {
	    $art = new article();

	    $art->open($fields['id']);

	    // si article pas epuise
	    $a_idArt[$art->fields['id']] = $art->fields['id'];
	    $articles[$art->fields['id']] = $art->fields;

	    // marque
	    if ( !empty($art->fields['marque']) && !empty($art->fields['marque_label']) && !isset($catalogue['marques']['values'][$art->fields['marque']]) ) {
	        $catalogue['marques']['values'][$art->fields['marque']]['label'] = $art->fields['marque_label'];
	        if ($art->fields['marque'] == $marque) {
	            $catalogue['marques']['selected'] = $art->fields['marque'];
	        }
	    }

	    $prix = catalogue_getprixarticle($art);

	    if ($prix > 0) {
	        $prixaff = catalogue_afficherprix($prix, $a_tva[$art->fields['ctva']]);

	        $prix = catalogue_formateprix($prix);
	        $prixaff = catalogue_formateprix($prixaff);

	        $art->fields['prix']     = $prix;
	        $art->fields['prixaff'] = $prixaff;

	        if (isset($_SESSION['catalogue']['prix_cplmt'][$art->fields['reference']]['puttc_plein'])) {
	            $art->fields['promotion'] = true;
	            $art->fields['prix_brut'] = $_SESSION['catalogue']['prix_cplmt'][$art->fields['reference']]['puttc_plein'];
	        }

			// on s'assure de la presence de la photo
			$dim = 50;
			$art->fields['image'] = $template_web_path.'gfx/empty_'.$dim.'x'.$dim.'.png';

			$vignette = $art->getVignette($dim);
			if ($vignette != null) {
				$art->fields['image'] = $vignette;
			}

	        // unité de vente
	        $art->fields['uvente'] = $art->fields[$uventeField];

			// lien vers la fiche article
			$art->fields['href'] = '/article/'.$art->fields['urlrewrite'].'.html';

	        $art_list[$art->fields['reference']] = $art->fields;

	        $fams = $art->getFams();

	        // couleurs de la famille de l'article
	        $colorfamily = "#A78EB6";
	        $colorfamily2 = "#805F94";
	        $colorfamily3 = "#323232";
	        $colorfamily4 = "#323232";

	        $smartyobject->assign('colorfamily', $colorfamily);
	        $smartyobject->assign('colorfamily2', $colorfamily2);
	        $smartyobject->assign('colorfamily3', $colorfamily3);
	        $smartyobject->assign('colorfamily4', $colorfamily4);
	    }
	}


	// titre du bloc
    $smartyobject->assign('bloc_label', $promo->getLibelle());

	$template_name = 'promo';

	if (!isset($_SESSION['dims']['smarty_path']) || $_SESSION['dims']['smarty_path']=='') {
	    $_SESSION['dims']['smarty_path']=realpath('.')."/smarty";
	}
	$smartypath=$_SESSION['dims']['smarty_path'];

	$smartyobject->cache_dir = $smartypath.'/cache';
	$smartyobject->config_dir = $smartypath.'/configs';
	$smartyobject->template_dir = "./templates/frontoffice/".$site_template_name."/catalogue/promotions/";

	$smartyobject->assign('slider',$slider);
	$smartyobject->assign('articles',$art_list);

	// fonctionnement du site en B2C ?
	$smartyobject->assign('cata_mode_B2C', $oCatalogue->getParams('cata_mode_B2C'));

	if (file_exists($smartyobject->template_dir.'/'.$template_name.'.tpl')) {
	    if (!file_exists($smartypath.'/templates_c/'.$template_name)) mkdir ($smartypath."/templates_c/".$template_name."/");
	    $smartyobject->compile_dir = $smartypath."/templates_c/".$template_name."/";

	    $smartyobject->display($template_name.'.tpl');
	}
	else echo 'ERREUR : '.$template_name.'.tpl manquant !';
}
