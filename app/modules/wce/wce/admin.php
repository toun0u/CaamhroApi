<?php
/*$view = view::getInstance();
$view->set_tpl_webpath('modules/wce/');
$view->set_static_version(module_wce::STATIC_FILES_VERSION);

$manager = $view->getStylesManager();
$manager->loadRessource('modules', 'wce');

// Inclusion des styles (par défaut) propres au module
/*$manager = $view->getScriptsManager();
$manager->loadRessource('modules', 'wce');*/

require_once DIMS_APP_PATH."modules/wce/wiki/include/class_wce_lang.php";
wce_lang::initLangs();
if(! isset($_SESSION['dims']['wce']['sub'])) $_SESSION['dims']['wce']['sub'] = module_wce::_SUB_HOMEPAGE;
$_SESSION['dims']['wce']['sub'] = dims_load_securvalue('sub2',dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['dims']['wce']['sub'], module_wce::_SUB_HOMEPAGE);

$wce_site = new wce_site (dims::getInstance()->db,$_SESSION['dims']['moduleid']);
$id_lang = $wce_site->getDefaultLanguage();
if ($id_lang == 0){
	$lang = new wce_lang();
	$lang->init_description();
	$lang->setugm();
	$lang->fields['default'] = 1;
	$lang->fields['ref'] = 'fr';
	$lang->fields['label'] = $_SESSION['cste']['_DIMS_LABEL_FRENCH'];
	$lang->save();
	$id_lang = $lang->fields['id'];
}
?>
<script type="text/javascript">
<?
require_once DIMS_APP_PATH."modules/wce/include/javascript.php";
?>
</script>

<link href="<? echo module_wce::getTemplateWebPath('/styles.css'); ?>" rel="stylesheet" type="text/css">
<!--link href="/common/css/bootstrap.min.css?<?= module_wce::STATIC_FILES_VERSION; ?>" rel="stylesheet" type="text/css">-->
<?
require_once module_wce::getTemplatePath("header.tpl.php");
?>
<div class="content">
<?
switch($_SESSION['dims']['wce']['sub']){
	default:
	case module_wce::_SUB_HOMEPAGE:
		require_once module_wce::getTemplatePath("homepage/controller.php");
		break;
	case module_wce::_SUB_PARAM:
		require_once module_wce::getTemplatePath("parameters/controller.php");
		break;
	case module_wce::_SUB_SITE:
		require_once module_wce::getTemplatePath("gestion_site/controller.php");
		break;
	/*case module_wce::_SUB_ARTICLE:

		break;*/
	case module_wce::_SUB_DYN:
		require_once module_wce::getTemplatePath("obj_dynamics/controller.php");
		break;
	case module_wce::_SUB_STATS:
		require_once module_wce::getTemplatePath("statistiques/controller.php");
		break;
	case module_wce::_SUB_LIST:
		require_once module_wce::getTemplatePath("list_articles/controller.php");
		break;
}
?>
</div>
<script type="text/javascript" language="javascript">
	$(document).ready(function() {
		$('.datepicker').datepicker({	dateFormat: 'dd/mm/yy',
										changeMonth: true,
										changeYear: true,
										buttonImage: '<?php echo module_wce::getTemplateWebPath('gfx/calendar.png'); ?>',
										showOn: 'both',
										buttonImageOnly: true,
										buttonText: '<? echo $_SESSION['cste']['_OEUVRE_SELECT_DATE']; ?>'});
	});
</script>