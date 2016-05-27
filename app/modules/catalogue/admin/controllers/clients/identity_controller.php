<?php
$view = view::getInstance();

$sub_action = $view->get('sa');

switch($sub_action){
	default:
	case 'edit':
		include_once DIMS_APP_PATH.'modules/system/class_city.php';
		include_once DIMS_APP_PATH.'modules/system/class_country.php';

		// chargement de la liste des pays
		$a_countries = country::getAllCountries();
		$a_countries_list = array();
		foreach ($a_countries as $country) {
			$a_countries_list[$country->get('id')] = $country->getLabel();
		}
		$view->assign('a_countries', $a_countries_list);

		// on crée la ville du client si elle n'existe pas déjà
		$id_country = $client->getCountryId();
		if ($client->getCity() != null && city::getByLabel($client->getCity(), $id_country) == null) {
			$city = new city();
			$city->setLabel($client->getCity());
			$city->setIdCountry($id_country);
			$city->save();
		}

		// chargement de la liste des villes
		if ($id_country > 0) {
			$a_cities_list = $a_countries[$id_country]->getcitieslist();
			$view->assign('a_cities', $a_cities_list);
		}

		// ouverture du 1er utilisateur
		$mainuser = $client->getMainUser();
		$view->assign('mainuser', $mainuser);

		// Chargement des niveaux utilisateurs
		$levels = array();
		$params = cata_param::initComptesClients();
		if ($params['is_user_with_valid']->getValue()) {
			$levels[dims_const::_DIMS_ID_LEVEL_USER] = $params['user_with_valid']->getValue();
		}
		if ($params['is_user_without_valid']->getValue()) {
			$levels[cata_const::_DIMS_ID_LEVEL_USERSUP] = $params['user_without_valid']->getValue();
		}
		if ($params['is_service_manager']->getValue()) {
			$levels[cata_const::_DIMS_ID_LEVEL_SERVICERESP] = $params['service_manager']->getValue();
		}
		if ($params['is_purchasing_manager']->getValue()) {
			$levels[cata_const::_DIMS_ID_LEVEL_PURCHASERESP] = $params['purchasing_manager']->getValue();
		}
		if ($params['is_account_admin']->getValue()) {
			$levels[dims_const::_DIMS_ID_LEVEL_GROUPMANAGER] = $params['account_admin']->getValue();
		}

		$view->assign('levels', $levels);

		if ($mainuser->isNew()) {
			$view->assign('selected_level', current($levels));
		}
		else {
			$view->assign('selected_level', $mainuser->getgroupadminlevel(array($client->fields['dims_group'])));
		}

		$view->render('clients/show/identity_edit.tpl.php');
		break;
	case 'save':
		break;
}
