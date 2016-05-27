<?php

$code = dims_load_securvalue('search_code', dims_const::_DIMS_CHAR_INPUT, false, true, true);

// recherche d'une promo pas encore debloquee
if (isset($_SESSION['catalogue']['promo']['locked'][$code])) {
	foreach ($_SESSION['catalogue']['promo']['locked'][$code] as $art => $promo) {
		$_SESSION['catalogue']['promo']['unlocked'][$art] = $promo;
	}
	unset ($_SESSION['catalogue']['promo']['locked'][$code]);

	$smarty->assign('msg_confirm', 'La promo a bien été débloquée.<br/><a style="color: #1483CF;" href="/accueil.html">Retour à l\'accueil</a>');
}
else {
	$smarty->assign('msg_confirm', 'Aucun résultat ne correspond à votre recherche.');
}

$smarty->assign('tpl_name', 'search_promo');
