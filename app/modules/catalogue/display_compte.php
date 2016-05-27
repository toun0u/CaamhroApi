<?php

if ($_SESSION['dims']['connected']) {

	$smarty->assign('tpl_name', 'compte');

	// label paniers types / listes scolaires
	$navElem = array();
	if($oCatalogue->getParams('panier_type')) $navElem[] = _LABEL_MESPANIERSTYPES;
	if($oCatalogue->getParams('school_lists')) $navElem[] = _LABEL_MESLISTESSCOLAIRE;
	$ptLabel = implode(' / ', $navElem);

	$links = array(
		0 => array(
			"label" => _LABEL_SAISIERAPIDE,
			"href" => "/index.php?op=saisierapide",
			"img" => "stopwatch",
			"comment" => _DESC_SAISIERAPIDE,
			"cond" => $oCatalogue->getParams('saisie_rapide') && ($_SESSION['session_adminlevel'] <= dims_const::_DIMS_ID_LEVEL_GROUPMANAGER)
			),
		1 => array(
			"label" => _LABEL_MONPANIER,
			"href" => "/index.php?op=panier",
			"img" => "",
			"text" => "1",
			"comment" => _DESC_MONPANIER,
			"cond" => $oCatalogue->getParams('active_cart') && ($_SESSION['session_adminlevel'] <= dims_const::_DIMS_ID_LEVEL_GROUPMANAGER)
			),
		2 => array(
			"label" => $ptLabel,
			"href" => "/index.php?op=panierstype",
			"img" => "",
			"text" => "",
			"comment" => _DESC_MESPANIERSTYPES,
			"cond" => ( $oCatalogue->getParams('panier_type') || $oCatalogue->getParams('school_lists') ) && ($_SESSION['session_adminlevel'] <= dims_const::_DIMS_ID_LEVEL_GROUPMANAGER)
			),
		3 => array(
			"label" => _LABEL_COMMANDESENATTENTECHIFFRAGE,
			"href" => "/index.php?op=commandes&view=awaiting_costing",
			"img" => "",
			"text" => "2",
			"comment" => _DESC_COMMANDESENCOURS,
			"cond" => $oCatalogue->getParams('wait_commandes') && ($_SESSION['session_adminlevel'] <= cata_const::_DIMS_ID_LEVEL_STATISTICS)
			),
		20 => array(
			"label" => _LABEL_COMMANDESENCOURS,
			"href" => "/index.php?op=commandes&view=awaiting_validation",
			"img" => "",
			"text" => "3",
			"comment" => _DESC_COMMANDESENCOURS,
			"cond" => $oCatalogue->getParams('wait_commandes') && ($_SESSION['session_adminlevel'] <= cata_const::_DIMS_ID_LEVEL_STATISTICS)
			),
		4 => array(
			"label" => _LABEL_HISTORIQUE,
			"href" => "/index.php?op=historique",
			"img" => "",
			"text" => "4",
			"comment" => _DESC_HISTORIQUE,
			"cond" => $oCatalogue->getParams('history_cmd') && ($_SESSION['session_adminlevel'] <= cata_const::_DIMS_ID_LEVEL_STATISTICS)
			),
		17 => array(
			"label" => "Bons de livraison",
			"href" => "/index.php?op=bonslivraison",
			"img" => "",
			"text" => "5",
			"comment" => "Vous permet de consulter vos bons de livraison",
			"cond" => $oCatalogue->getParams('bon_livraison') && ($_SESSION['session_adminlevel'] <= cata_const::_DIMS_ID_LEVEL_STATISTICS)
			),
		18 => array(
			"label" => "Reliquats",
			"href" => "/index.php?op=reliquats",
			"img" => "paste2",
			"comment" => "Voir les reliquats",
			"cond" => $oCatalogue->getParams('remainings') && ($_SESSION['session_adminlevel'] <= cata_const::_DIMS_ID_LEVEL_STATISTICS)
			),
		13 => array(
			"label" => "Factures",
			"href" => "/index.php?op=factures",
			"img" => "",
			"text" => "6",
			"comment" => "Voir l'historique des factures",
			"cond" => $oCatalogue->getParams('invoices')
			),
		5 => array(
			"label" => _LABEL_HORSCATALOGUE,
			"href" => "/index.php?op=hors_catalogue",
			"img" => "copy2",
			"comment" => _DESC_HORSCATALOGUE,
			"cond" => $oCatalogue->getParams('exceptional_orders')
			),
		15 => array(
			"label" => "Informations personnelles",
			"href" => "/index.php?op=infospersos",
			"img" => "address-book",
			"text" => '',
			"comment" => "Edition de vos param&egrave;tres personnels",
			"cond" => ($oCatalogue->getParams('personal_informations'))
			),
		6 => array(
			"label" => _LABEL_ADMINISTRATION,
			"href" => "/index.php?op=administration",
			"img" => "/modules/catalogue/img/administration.png",
			"comment" => _DESC_ADMINISTRATION,
			"cond" => ($_SESSION['session_adminlevel'] >= dims_const::_DIMS_ID_LEVEL_GROUPMANAGER)
			),
		7 => array(
			"label" => _LABEL_STATISTIQUES,
			"href" => "/index.php?op=statistiques",
			"img" => "/modules/catalogue/img/statistiques.png",
			"comment" => _DESC_STATISTIQUES,
			"cond" => ($oCatalogue->getParams('statistics') && $_SESSION['session_adminlevel'] >= dims_const::_DIMS_ID_LEVEL_GROUPMANAGER)
			),
		8 => array(
			"label" => _LABEL_IMPORTSELECTION,
			"href" => "/index.php?op=import_sel_form",
			"img" => "/modules/catalogue/img/import_sel.png",
			"comment" => _DESC_IMPORTSELECTION,
			"cond" => (false)
			),
		9 => array(
			"label" => _LABEL_IMPRIMERSELECTION_PDF,
			"href" => "/index.php?op=imprimer_cata",
			"img" => "/modules/catalogue/img/imprimer_cata.png",
			"comment" => _DESC_IMPRIMERSELECTION_PDF,
			"cond" => ($_SESSION['catalogue']['imprimer_selection'] && $_SESSION['catalogue']['utiliser_selection'] && $_SESSION['session_adminlevel'] <= dims_const::_DIMS_ID_LEVEL_GROUPMANAGER)
			),
		10 => array(
			"label" => _LABEL_EXPORTERCATALOGUE_XLS,
			"href" => "/index.php?op=export_prixnets&format=xls",
			"img" => "/modules/catalogue/img/exporter.png",
			"comment" => _DESC_EXPORTERCATALOGUE_XLS,
			"cond" => ($_SESSION['catalogue']['afficher_prix'] && _SHOW_PRICES && $_SESSION['catalogue']['export_catalogue'])
			),
		11 => array(
			"label" => _LABEL_EXPORTERCATALOGUE_CSV,
			"href" => "/index.php?op=export_prixnets&format=csv",
			"img" => "/modules/catalogue/img/exporter.png",
			"comment" => _DESC_EXPORTERCATALOGUE_CSV,
			"cond" => ($_SESSION['catalogue']['afficher_prix'] && _SHOW_PRICES && $_SESSION['catalogue']['export_catalogue'])
			),
		14 => array(
			"label" => "Promotions",
			"href" => "/index.php?op=promotions",
			"img" => "/modules/catalogue/img/promotions.png",
			"comment" => "Liste des articles en promotion",
			"cond" => (isset($_SESSION['catalogue']['promo']) && sizeof($_SESSION['catalogue']['promo']['unlocked']))
			),
		16 => array(
			"label" => "Extraits de compte",
			"href" => "/index.php?op=extraits_compte",
			"img" => "drawer3",
			"comment" => "Voir les extraits de compte",
			"cond" => $oCatalogue->getParams('account_statements')
			),
		12 => array(
			"label" => _LABEL_RETOURADMINISTRATION,
			"href" => "/index.php?op=return_to_admin",
			"img" => "/modules/catalogue/img/back.png",
			"comment" => _DESC_RETOURADMINISTRATION,
			"cond" => (isset($_SESSION['catalogue']['iwasadmin']) && $_SESSION['catalogue']['iwasadmin'] == 1)
			),
		19 => array(
			"label" => 'Retour a la liste des clients',
			"href" => '/index.php?op=vrp_retour',
			"img" => '/modules/catalogue/img/back.png',
			"comment" => 'Consulter la liste des clients.',
			"cond" => (!empty($_SESSION['catalogue']['vrp']['id_commercial']))
			)
	);

	$smarty->assign('links', $links);
}
else {
	$_SESSION['catalogue']['connexion']['oldquery'] = $_SERVER['QUERY_STRING'];
	dims_redirect($dims->getScriptEnv().'?op=connexion');
}
