<?php
$o = dims_load_securvalue('o', dims_const::_DIMS_CHAR_INPUT, true, true,true);

// TODO : ajouter des onglets si besoin
?>
<ul class="onglet">
	<li class="<?= ($o == "ctag")?"selected":""; ?>" onclick="javascript:document.location.href='<?= dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=admin&o=ctag';">
		<?= $_SESSION['cste']['_TAG_CATEGORIES']; ?>
	</li>
	<li class="<?= ($o == "tag")?"selected":""; ?>" onclick="javascript:document.location.href='<?= dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=admin&o=tag';">
		<?= $_SESSION['cste']['_DIMS_LABEL_TAGS']; ?>
	</li>
	<li class="<?= ($o == "geo")?"selected":""; ?>" onclick="javascript:document.location.href='<?= dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=admin&o=geo';">
		<?= $_SESSION['cste']['_GEOGRAPHICAL_TAGS']; ?>
	</li>
	<li class="<?= ($o == "tmp")?"selected":""; ?>" onclick="javascript:document.location.href='<?= dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=admin&o=tmp';">
		<?= $_SESSION['cste']['_TEMPORAL_TAGS']; ?>
	</li>
	<li class="<?= ($o == "planning")?"selected":""; ?>" onclick="javascript:document.location.href='<?= dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=admin&o=planning';">
		<?= $_SESSION['cste']['_CATEGORY_PLANNING']; ?>
	</li>
	<li class="<?= ($o == "grct")?"selected":""; ?>" onclick="javascript:document.location.href='<?= dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=admin&o=grct';">
		<?= $_SESSION['cste']['_DIMS_LABEL_CT_GROUP']; ?>
	</li>
	<?php if (defined('_ACTIVE_GESCOM') && _ACTIVE_GESCOM): ?>
	<li class="<?= ($o == "suivis")?"selected":""; ?>" onclick="javascript:document.location.href='<?= dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=admin&o=suivis';">
		<?= $_SESSION['cste']['_MONITORINGS']; ?>
	</li>
	<?php endif; ?>
</ul>
<?php

// Permet l'update des villes (id_globalobject)
require_once DIMS_APP_PATH.'/modules/system/class_city.php';
$lst = city::find_by(array('id_globalobject'=>0),null,1000);
require_once DIMS_APP_PATH.'/modules/system/class_region.php';
$lst = region::find_by(array('id_globalobject'=>0),null,1000);
require_once DIMS_APP_PATH.'/modules/system/class_departement.php';
$lst = departement::find_by(array('id_globalobject'=>0),null,1000);
require_once DIMS_APP_PATH.'/modules/system/class_canton.php';
$lst = canton::find_by(array('id_globalobject'=>0),null,1000);
require_once DIMS_APP_PATH.'/modules/system/class_arrondissement.php';
$lst = arrondissement::find_by(array('id_globalobject'=>0),null,1000);

foreach($lst as $l){
	$l->save();
}

switch($o){
	case 'ctag':
		require_once _DESKTOP_TPL_LOCAL_PATH.'/admin/categ_tag/controller.php';
		break;
	case 'tag':
		require_once _DESKTOP_TPL_LOCAL_PATH.'/admin/tag/controller.php';
		break;
	case 'geo';
		require_once _DESKTOP_TPL_LOCAL_PATH.'/admin/geo/controller.php';
		break;
	case 'tmp';
		require_once _DESKTOP_TPL_LOCAL_PATH.'/admin/tmp/controller.php';
		break;
	case 'planning':
		require_once _DESKTOP_TPL_LOCAL_PATH.'/admin/planning/controller.php';
		break;
	case 'grct':
		require_once _DESKTOP_TPL_LOCAL_PATH.'/admin/group_ct/controller.php';
		break;
	default:
		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=admin&o=ctag");
		break;
	case 'suivis':
		if (defined('_ACTIVE_GESCOM') && _ACTIVE_GESCOM) {
			require_once _DESKTOP_TPL_LOCAL_PATH.'/admin/suivi/controller.php';
		}else{
			dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=admin&o=ctag");
		}
		break;
}
