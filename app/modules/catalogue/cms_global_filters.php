<?php

if ($oCatalogue->getParams('cata_visible_not_connected') || $_SESSION['dims']['connected']) {
	include_once DIMS_APP_PATH.'/modules/catalogue/include/class_champ.php';

	foreach (cata_champ::allGlobalFilters() as $filter) {
		if ( !isset($_SESSION['catalogue']['global_filter']) || $_SESSION['catalogue']['global_filter']['filter_id'] != $filter->get('id') ) {
			$title = 'Accéder à l\'espace "'.$filter->getGlobalFilterLabel($_SESSION['dims']['currentlang']).'"';
			$link = cata_addParamToURI($_SERVER['REQUEST_URI'], 'global_filter', $filter->get('id'));
			echo '<h3 class="nomargin"><a class="btn btn-primary-green"href="'.$link.'" title="'.$title.'" style="color: #333;">'.$title.'</a></h3>';
		}
	}
}
