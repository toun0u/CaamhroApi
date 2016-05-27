<?php
dims_init_module('catalogue');

//die('into the cms.php of catalogue');

include_once DIMS_APP_PATH.'modules/catalogue/include/params_catalogue.php';

//Class
include_once DIMS_APP_PATH.'modules/catalogue/include/class_context.php';
include_once DIMS_APP_PATH.'modules/catalogue/include/class_article.php';
include_once DIMS_APP_PATH.'modules/catalogue/include/class_famille.php';
include_once DIMS_APP_PATH.'modules/catalogue/include/class_filter.php';
include_once DIMS_APP_PATH.'modules/catalogue/include/class_catalogue.php';

if (defined('_CATA_VARIANTE') && file_exists(DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/class_catalogue.php')) {
	include_once DIMS_APP_PATH.'modules/catalogue/'._CATA_VARIANTE.'/include/class_catalogue.php';
}

$db->query("SET NAMES 'UTF8'");//temporaire ? qui dure ?


$mods = $dims->getModuleByType('catalogue');
$catalogue_moduleid = $mods[0]['instanceid'];
$_SESSION['catalogue']['moduleid'] = $catalogue_moduleid;

$oCatalogue = new catalogue();
$oCatalogue->open($mods[0]['instanceid']);
$oCatalogue->loadParams();

// chargement des paramètres utilisateur
if ($_SESSION['dims']['connected'] && empty($_SESSION['catalogue']['code_client'])) {
	$envReturn = cata_context::loadUserEnvironment($oCatalogue);
	if ($envReturn['response']) {
		dims_redirect('/index.php?dims_logout=1&msg='.$envReturn);
	}
}
// else if(empty($_SESSION['catalogue']['context_loaded'])) {
// 	cata_context::loadDefaultEnvironment();
// }

// chargement des familles
$familys = cata_getfamilys();

$smartyobject = new Smarty();

// affichage / masquage des prix
if (!isset($_SESSION['catalogue']['aff_prix'])) {
	$_SESSION['catalogue']['aff_prix'] = true;
}
if (isset($_SESSION['catalogue']['aff_prix'])) {
	$smartyobject->assign('aff_prix', $_SESSION['catalogue']['aff_prix']);
}

// base de calcul (HT / TTC ?)
$smartyobject->assign('cata_base_ttc', $oCatalogue->getParams('cata_base_ttc'));

// user connected
if ($_SESSION['dims']['connected']) {
	$smartyobject->assign('switch_user_logged_in','');
}

switch($op) {
	case 'slideshow':
		include DIMS_APP_PATH.'modules/catalogue/cms_slideshow.php';
		break;
	case 'cloud':
		include DIMS_APP_PATH.'modules/catalogue/cms_cloud.php';
		break;
	case 'promo':
		include DIMS_APP_PATH.'modules/catalogue/cms_promotions.php';
		break;
	case 'eco':
		include DIMS_APP_PATH.'modules/catalogue/cms_ecoprod.php';
		break;
	case 'slidart':
		include DIMS_APP_PATH.'modules/catalogue/cms_slidart.php';
		break;
	case 'sitemap':
		include DIMS_APP_PATH.'modules/catalogue/cms_sitemap.php';
		break;
	case 'image_promo':
		include DIMS_APP_PATH.'modules/catalogue/cms_image_promo.php';
		break;
	case 'promos_sliders':
		include DIMS_APP_PATH.'modules/catalogue/cms_promos_sliders.php';
		break;
	case 'billetterie':
	case 'espace_client':
		include DIMS_APP_PATH.'modules/catalogue/front/controller.php';
		break;
	case 'article_sheet':
		include DIMS_APP_PATH . 'modules/catalogue/cms_article_sheet.php';
		break;
	case 'global_filters':
		include DIMS_APP_PATH . 'modules/catalogue/cms_global_filters.php';
		break;
}
