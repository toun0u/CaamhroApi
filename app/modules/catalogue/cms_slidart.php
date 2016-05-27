<?php

// recherche du template
$lstwcemods = $dims->getWceModules();
$wce_module_id = (!empty($lstwcemods)) ? current($lstwcemods) : 0;

$headings = wce_getheadings($wce_module_id);
$headingid = (isset($headings['tree'][0][0])) ? $headings['tree'][0][0] : 0;

$site_template_name = (!empty($headings['list'][$headingid]['template'])) ? $headings['list'][$headingid]['template'] : 'default';
$smartyobject->assign('site_template_name', $site_template_name);
// FIN - recherche du template

include_once DIMS_APP_PATH.'modules/catalogue/include/class_slidart.php';
include_once DIMS_APP_PATH.'modules/catalogue/include/class_slidart_element.php';

global $a_tva;

$slidart = new slidart();
$slidart->open($obj['object_id']);

$art_list = array();
$slider = array();
$slider['id'] = $slidart->fields['id'];
$slider['nom'] = $slidart->fields['nom'];

foreach ($slidart->getElements() as $slide) {
	$art = new article();

	$art->open($slide->fields['id_article']);

	// si article pas epuise
	if ($art->fields['qte'] != 0) {
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

		$art->fields['image'] = '/assets/images/frontoffice/'.$_SESSION['dims']['front_template_name'].'/gfx/empty_100x100.png';
		if (file_exists(realpath('.').'/photos/100x100/'.$art->getReference().'.jpg')) {
			$art->fields['image'] = '/photos/100x100/'.$art->getReference().'.jpg';
		}

		if ($prix > 0) {
			$prixaff = catalogue_afficherprix($prix, $a_tva[$art->fields['ctva']]);

			$prix = catalogue_formateprix($prix);
			$prixaff = catalogue_formateprix($prixaff);

			$art->fields['prix']     = $prix;
			$art->fields['prixaff'] = $prixaff;

			if (isset($_SESSION['catalogue']['promo']['unlocked']["'".$art->fields['reference']."'"])) {
				$fields['promotion'] = true;
				$fields['prix_brut'] = catalogue_formateprix(catalogue_getprixarticle($art, 1, true));
			}

			$art_list[$art->fields['reference']] = $art->fields;
		}
	}
}


$template_name='slidart';

if (!isset($_SESSION['dims']['smarty_path']) || $_SESSION['dims']['smarty_path']=='') {
	$_SESSION['dims']['smarty_path']=realpath('.')."/smarty";
}
$smartypath=$_SESSION['dims']['smarty_path'];

$smartyobject->cache_dir = $smartypath.'/cache';
$smartyobject->config_dir = $smartypath.'/configs';
$smartyobject->template_dir = DIMS_APP_PATH.'/templates/frontoffice/'.$site_template_name.'/catalogue/slidart/';

$smartyobject->assign('slider',$slider);
$smartyobject->assign('articles',$art_list);

// fonctionnement du site en B2C ?
$smartyobject->assign('cata_mode_B2C', $oCatalogue->getParams('cata_mode_B2C'));

if (file_exists($smartyobject->template_dir[0].$template_name.'.tpl')) {
	if (!file_exists($smartypath.'/templates_c/'.$template_name)) mkdir ($smartypath."/templates_c/".$template_name."/");
	$smartyobject->compile_dir = $smartypath."/templates_c/".$template_name."/";

	$smartyobject->display($template_name.'.tpl');
}
else echo 'ERREUR : '.$template_name.'.tpl manquant !';

?>
