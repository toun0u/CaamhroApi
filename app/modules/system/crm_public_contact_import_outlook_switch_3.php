<?php

$sim = dims_load_securvalue('sim', dims_const::_DIMS_CHAR_INPUT, true);
//Il existe des contacts avec des similitudes dans le nom et le prenom
if($op == 3 && $sim == '') {
	//on est dans le cas ou on importe depuis la table d'import
	require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_import_similarity_case.php');
}
elseif($op == 3 && $sim == 'wsim') {
	//on est dans le cas ou on fait des rapprochements entre les entreprises
	//require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_import_similarity_ent.php');
	dims_redirect("/admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_IMPORT_OUTLOOK."&part="._BUSINESS_TAB_IMPORT_OUTLOOK);
}

elseif($_SESSION['dims']['import_count_similar'] > 0) {
	if(!isset($_SESSION['dims']['IMPORT_ENT_SIMILARITY']) || count($_SESSION['dims']['IMPORT_ENT_SIMILARITY']) == 0 ) {

		$content_contact_import = '<div style="text-align:center;margin:10px;">'.$_SESSION['dims']['import_count_similar'].'&nbsp;'.$_DIMS['cste']['_IMPORT_SIMILAR_CT'].'</div>';

		$content_contact_import .= '<div style="text-align:center;">
											'.dims_create_button($_DIMS['cste']['_DIMS_CONTINUE'], "./common/img/public.png", "dims_redirect('./admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_IMPORT_OUTLOOK."&part="._BUSINESS_TAB_IMPORT_OUTLOOK."&op=4');").'
										</div>';
	}
	else {
		//si on a des entreprises similaires, on passe dans le cas '3s'
		$content_contact_import = '<div style="text-align:center;margin:10px;">'.$_SESSION['dims']['import_count_similar'].'&nbsp;'.$_DIMS['cste']['_IMPORT_SIMILAR_CT'].'</div>';
		$content_contact_import .= '<div style="text-align:center;">
											'.dims_create_button($_DIMS['cste']['_DIMS_CONTINUE'], "./common/img/public.png", "dims_redirect('./admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_IMPORT_OUTLOOK."&part="._BUSINESS_TAB_IMPORT_OUTLOOK."&op=4');").'
										</div>';
		// &op=3&sim=wsim
	}
}else {
	if(!isset($_SESSION['dims']['IMPORT_ENT_SIMILARITY']) || count($_SESSION['dims']['IMPORT_ENT_SIMILARITY']) == 0 ) {
		$content_contact_import = '<div style="text-align:center;margin:10px;">'.$_DIMS['cste']['_IMPORT_NO_SIMILAR_CT'].'</div>';
		$content_contact_import .= '<div style="text-align:center;">
											'.dims_create_button($_DIMS['cste']['_DIMS_CONTINUE'], "./common/img/public.png", "dims_redirect('./admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_IMPORT_OUTLOOK."&part="._BUSINESS_TAB_IMPORT_OUTLOOK."&op=4');").'
										</div>';
	}
	else {
		//si on a des entreprises similaires, on passe dans le cas '3s'
		$content_contact_import = '<div style="text-align:center;margin:10px;">'.$_DIMS['cste']['_IMPORT_NO_SIMILAR_CT'].'</div>';
		$content_contact_import .= '<div style="text-align:center;">
											'.dims_create_button($_DIMS['cste']['_DIMS_CONTINUE'], "./common/img/public.png", "dims_redirect('./admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_IMPORT_OUTLOOK."&part="._BUSINESS_TAB_IMPORT_OUTLOOK."&op=3&sim=wsim');").'
										</div>';
	}
}

?>
