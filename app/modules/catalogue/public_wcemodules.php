<?php

$subtab = dims_load_securvalue('subtab', dims_const::_DIMS_CHAR_INPUT,true,true,false, $_SESSION['catalogue']['subtab'], 'slideshow');

// creation des onglets
$sub_tabs['slideshow']['title'] = 'Slideshow';
$sub_tabs['slideshow']['url'] = $dims->getScriptEnv().'?subtab=slideshow';
$sub_tabs['slideshow']['icon'] = './common/modules/catalogue/img/sliders.png';
$sub_tabs['slideshow']['width'] = 110;
$sub_tabs['slideshow']['position'] = 'left';

$sub_tabs['cloud']['title'] = 'Nuage de tags';
$sub_tabs['cloud']['url'] = $dims->getScriptEnv().'?subtab=cloud';
$sub_tabs['cloud']['icon'] = './common/modules/catalogue/img/nuage.png';
$sub_tabs['cloud']['width'] = 150;
$sub_tabs['cloud']['position'] = 'left';

$sub_tabs['slidart']['title'] = 'Slider articles';
$sub_tabs['slidart']['url'] = $dims->getScriptEnv().'?subtab=slidart';
$sub_tabs['slidart']['icon'] = './common/modules/catalogue/img/sliders.png';
$sub_tabs['slidart']['width'] = 150;
$sub_tabs['slidart']['position'] = 'left';


echo $skin->open_simplebloc('Gestion de modules WCE');
echo '<div id="cata_wcemodule">
		<div style="padding: 0 10px;">'.$skin->create_toolbar($sub_tabs, $subtab,$subtab,'0',"onglet").'</div>
		<div style="clear: both;">';

switch($subtab) {
	case 'slideshow':
		include_once DIMS_APP_PATH.'modules/catalogue/public_wcemodules_slideshow.php';
		break;
	case 'cloud':
		include_once DIMS_APP_PATH.'modules/catalogue/public_wcemodules_cloud.php';
		break;
	case 'slidart':
		include_once DIMS_APP_PATH.'modules/catalogue/public_wcemodules_slidart.php';
		break;
}
echo '</div></div>'.$skin->close_simplebloc();
?>
