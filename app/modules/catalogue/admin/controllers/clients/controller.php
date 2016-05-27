<?php
include_once DIMS_APP_PATH."modules/catalogue/include/class_client.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_moyen_paiement.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_client_moyen_paiement.php";
include_once DIMS_APP_PATH."modules/catalogue/admin/helpers/clients_helpers.php";
include_once DIMS_APP_PATH."modules/catalogue/admin/helpers/clients_helpers.php";
$view = view::getInstance();
$view->setLayout('layouts/lateralized_layout.tpl.php');

// infos contexuelles
$view->assign('a', $a);

$default_search = dims_constant::getVal('SEARCH_A_CLIENT');
$view->assign('default_search', $default_search); #permet d'indiquer le texte qui va se placer dans le champ de recherche de la barre latérale
$view->assign('path_lateral_search', get_path('clients', 'index'));

switch ($a) {
	default:
	case 'index':
		$init_filter = dims_load_securvalue('filter_init', dims_const::_DIMS_NUM_INPUT, true, true, true);

		#Filtres
		if(isset($init_filter) && $init_filter){
			unset($_SESSION['cata']['clients']['index']);
		}
		$cur_keywords		= &get_sessparam($_SESSION['cata']['clients']['index']['keywords'], '');
		$keywords			= dims_load_securvalue('keywords', dims_const::_DIMS_CHAR_INPUT, true, true, true, $cur_keywords, '', true);

		$cur_status			= &get_sessparam($_SESSION['cata']['clients']['index']['status'], client::STATUS_OK);
		$status				= dims_load_securvalue('status', dims_const::_DIMS_NUM_INPUT, true, true, true, $cur_status);

		if( ! empty($keywords) && $keywords == $default_search) $keywords = '';

		$view->assign('keywords', $keywords);
		$view->assign('status', $status);

		#options de tri
		$sort_by	= &get_sessparam($_SESSION['cata']['clients']['index']['sort_by'], 'code');
		$sort_way	= &get_sessparam($_SESSION['cata']['clients']['index']['sort_way'], 'ASC');

		$sb			= dims_load_securvalue('sort_by',	dims_const::_DIMS_CHAR_INPUT,	true,true, true, $sort_by);
		$sw			= dims_load_securvalue('sort_way',	dims_const::_DIMS_CHAR_INPUT,	true,true, true, $sort_way);

		$view->assign('sort_by', $sb);
		$view->assign('sort_way', $sw);

		#pagination
		$page = dims_load_securvalue('page',dims_const::_DIMS_NUM_INPUT,true,true, true, $cur_page);

		$cli = new client();
		$cli->page_courant = $page;
		$cli->setPaginationParams(30, 5, false, '<<', '>>', '<', '>');
		$clients = $cli->build_index($status, $keywords, false, $sb, $sw, dims_isactionallowed(catalogue::ACTION_VIEW_ALL_CLIENTS));
		$view->assign('total_clients', $cli->total_index);

		#assignation du contenu de la pagination
		$view->assign('pagination', $cli->getPagination());
		#assignation des articles à la vue
		$view->assign('clients', $clients);

		#Affichage ou non des filtres
		$cur_mode			= &get_sessparam($_SESSION['cata']['clients']['filters_mode'], 'show');
		$view->assign('filters_mode', $cur_mode);

		#Actions contextuelles
		$actions = array();
		$actions[0]['picto'] = 'gfx/ajouter20.png';
		$actions[0]['text'] = dims_constant::getVal('CREATE_A_CLIENT');
		$actions[0]['link'] = get_path('clients', 'new');


		$view->assign('actions', $actions);
		$view->render('clients/index.tpl.php');
		break;
	case 'new':
		$cli = new client();
		$cli->init_description();
		$cli->setugm();

		$actions = array();
		$actions[0]['picto'] = 'gfx/retour20.png';
		$actions[0]['text'] = dims_constant::getVal('RETURN_TO_THE_LIST_OF_CLIENTS');
		$actions[0]['link'] = get_path('clients', 'index');

		// chargement de la liste des pays
		$a_countries = country::getAllCountries();
		$a_countries_list = array();
		foreach ($a_countries as $country) {
			$a_countries_list[$country->get('id')] = $country->getLabel();
		}
		$view->assign('a_countries', $a_countries_list);

		// chargement de la liste des villes
		if (isset($id_country) && $id_country > 0) {
			$a_cities = $a_countries[$id_country]->getAllCity();
			$a_cities_list = array();
			foreach ($a_cities as $city) {
				$a_cities_list[$city->get('id')] = $city->getLabel();
			}
			$view->assign('a_cities', $a_cities_list);
		}

		$means_of_payment = array();
		foreach (moyen_paiement::getActivePaiement() as $mp) {
			$means_of_payment[$mp->get('id')] = $mp->getLabel();
		}
		$view->assign('means_of_payment', $means_of_payment);

		$view->assign('actions', $actions);
		$view->assign('client', $cli);
		$view->render('clients/new.tpl.php');
		break;
	case 'show':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true, true);

		if ($id > 0) {
			store_lastclient($id,3);
			$view->setLayout('layouts/clients_layout.tpl.php');

			$client = new client();
			$client->open($id);
			$view->assign('client', $client);

			$actions = array();
			$actions[0]['picto'] = 'gfx/retour20.png';
			$actions[0]['text'] = dims_constant::getVal('RETURN_TO_THE_LIST_OF_CLIENTS');
			$actions[0]['link'] = get_path('clients', 'index');
			$view->assign('actions', $actions);

			$sub_control = dims_load_securvalue('sc',dims_const::_DIMS_CHAR_INPUT,true,true);
			if ($sub_control == '') $sub_control = 'identity';
			$view->assign('sc', $sub_control);
			$view->assign('sa',dims_load_securvalue('sa', dims_const::_DIMS_CHAR_INPUT, true, true));

			switch ($sub_control) {
				case 'identity':
					include DIMS_APP_PATH.'modules/catalogue/admin/controllers/clients/identity_controller.php';
					break;
				case 'tarification':
					include DIMS_APP_PATH.'modules/catalogue/admin/controllers/clients/tarification_controller.php';
					break;
				case 'addresses':
					include DIMS_APP_PATH.'modules/catalogue/admin/controllers/clients/address_controller.php';
					break;
				case 'services':
					include DIMS_APP_PATH.'modules/catalogue/admin/controllers/clients/service_controller.php';
					break;
				case 'quotations':
					include DIMS_APP_PATH . 'modules/catalogue/admin/controllers/clients/quotation_controller.php';
					break;
				case 'crm':
				    $contact = $client->getMainUser()->getContact();
					include DIMS_APP_PATH . 'modules/catalogue/admin/controllers/clients/crm_controller.php';
					break;
			}
		} else {
			dims_redirect(get_path('clients', 'index'));
		}
		break;
	case 'save':
		// dims_print_r($_POST);die();

		$id_client					= dims_load_securvalue('id_client', dims_const::_DIMS_NUM_INPUT, false, true, true);
		$code_client				= dims_load_securvalue('code_client', dims_const::_DIMS_CHAR_INPUT, false, true, true);
		$type						= dims_load_securvalue('type', dims_const::_DIMS_NUM_INPUT, false, true, true);
		$blocked					= dims_load_securvalue('blocked', dims_const::_DIMS_NUM_INPUT, false, true, true);
		$commentaire				= dims_load_securvalue('commentaire', dims_const::_DIMS_CHAR_INPUT, false, true, true);

		$company_name				= dims_load_securvalue('company_name', dims_const::_DIMS_CHAR_INPUT, false, true, true);
		$company_email				= dims_load_securvalue('company_email', dims_const::_DIMS_CHAR_INPUT, false, true, true);
		$number_siren				= dims_load_securvalue('company_number_siren', dims_const::_DIMS_CHAR_INPUT, false, true, true);
		$nic						= dims_load_securvalue('company_nic', dims_const::_DIMS_CHAR_INPUT, false, true, true);
		$ape_code					= dims_load_securvalue('company_ape_code', dims_const::_DIMS_CHAR_INPUT, false, true, true);
		$address_1					= dims_load_securvalue('company_address_1', dims_const::_DIMS_CHAR_INPUT, false, true, true);
		$address_2					= dims_load_securvalue('company_address_2', dims_const::_DIMS_CHAR_INPUT, false, true, true);
		$address_3					= dims_load_securvalue('company_address_3', dims_const::_DIMS_CHAR_INPUT, false, true, true);
		$id_country					= dims_load_securvalue('company_country', dims_const::_DIMS_NUM_INPUT, false, true, true);
		$postalcode					= dims_load_securvalue('company_postalcode', dims_const::_DIMS_NUM_INPUT, false, true, true);
		$id_city					= dims_load_securvalue('company_city', dims_const::_DIMS_NUM_INPUT, false, true, true);

		$liv_same_as_facturation 	= dims_load_securvalue('liv_same_as_facturation', dims_const::_DIMS_NUM_INPUT, false, true);
		$liv_company_name			= dims_load_securvalue('company_liv_name', dims_const::_DIMS_CHAR_INPUT, false, true, true);
		$liv_address_1				= dims_load_securvalue('company_liv_address_1', dims_const::_DIMS_CHAR_INPUT, false, true, true);
		$liv_address_2				= dims_load_securvalue('company_liv_address_2', dims_const::_DIMS_CHAR_INPUT, false, true, true);
		$liv_address_3				= dims_load_securvalue('company_liv_address_3', dims_const::_DIMS_CHAR_INPUT, false, true, true);
		$liv_id_country				= dims_load_securvalue('company_liv_country', dims_const::_DIMS_NUM_INPUT, false, true, true);
		$liv_postalcode				= dims_load_securvalue('company_liv_postalcode', dims_const::_DIMS_NUM_INPUT, false, true, true);
		$liv_id_city				= dims_load_securvalue('company_liv_city', dims_const::_DIMS_NUM_INPUT, false, true, true);

		$user_firstname				= dims_load_securvalue('user_firstname', dims_const::_DIMS_CHAR_INPUT, false, true, true);
		$user_lastname				= dims_load_securvalue('user_lastname', dims_const::_DIMS_CHAR_INPUT, false, true, true);
		$user_email					= dims_load_securvalue('user_email', dims_const::_DIMS_CHAR_INPUT, false, true, true);
		$user_phone					= dims_load_securvalue('user_phone', dims_const::_DIMS_CHAR_INPUT, false, true, true);
		$user_login					= dims_load_securvalue('user_login', dims_const::_DIMS_CHAR_INPUT, false, true, true);
		$user_fax					= dims_load_securvalue('user_fax', dims_const::_DIMS_CHAR_INPUT, false, true, true);
		$user_password				= dims_load_securvalue('user_password', dims_const::_DIMS_CHAR_INPUT, false, true, true);
		$user_password_confirmation	= dims_load_securvalue('user_password_confirmation', dims_const::_DIMS_CHAR_INPUT, false, true, true);

		$escompte					= dims_load_securvalue('escompte', dims_const::_DIMS_NUM_INPUT, false, true, true);
		$minimum_order				= dims_load_securvalue('minimum_order', dims_const::_DIMS_NUM_INPUT, false, true, true);
		$franco						= dims_load_securvalue('franco', dims_const::_DIMS_NUM_INPUT, false, true, true);

		$a_paymentMeans 			= dims_load_securvalue('means_of_payment', dims_const::_DIMS_NUM_INPUT, false, true, true);

		$client = new client();
		if ($id_client > 0) {
			$client->open($id_client);

			// mise à jour du 1er contact
			if (!empty($client->fields['dims_user'])) {
				$user = new user();
				$user->open($client->fields['dims_user']);
				$user->fields['firstname'] = $user_firstname;
				$user->fields['lastname'] = $user_lastname;
				$user->fields['login'] = $user_login;
				$user->fields['email'] = $user_email;
				$user->fields['phone'] = $user_phone;
				$user->fields['fax'] = $user_fax;
				if ($user_password != '' && $user_password == $user_password_confirmation) {
					$user->fields['password'] = dims_getPasswordHash($user_password);
				}
				$user->save();
			}
		}
		else {
			$client->init_description();
			$client->setugm();
			$client->setCode($code_client);
		}

		if ($blocked > 0) {
			$client->block();
		}
		else {
			$client->unblock();
		}

		$client->setType($type);
		$client->setCommentaire($commentaire);

		$client->setName($company_name);
		$client->setEmail($company_email);
		$client->setNic($nic);
		$client->setAPE($ape_code);
		$client->setSiren($number_siren);
		$client->setAddress1($address_1);
		$client->setAddress2($address_2);
		$client->setAddress3($address_3);
		$client->setPostalCode($postalcode);
		$client->setCity($id_city);
		$client->setCountry($id_country);

		if ($liv_same_as_facturation) {
			$client->setLivName($company_name);
			$client->setLivAddress1($address_1);
			$client->setLivAddress2($address_2);
			$client->setLivAddress3($address_3);
			$client->setLivCP($postalcode);
			$client->setLivCityId($id_city);
			$client->setLivCountry($id_country);
		}
		else {
			$client->setLivName($liv_company_name);
			$client->setLivAddress1($liv_address_1);
			$client->setLivAddress2($liv_address_2);
			$client->setLivAddress3($liv_address_3);
			$client->setLivCP($liv_postalcode);
			$client->setLivCityId($liv_id_city);
			$client->setLivCountry($liv_id_country);
		}

		$client->setLogin($user_login);
		$client->setEmail($company_email);
		$client->setTel1($user_phone);
		$client->setFax($user_fax);

		$client->setEscompte($escompte);
		$client->setMinimumCde($minimum_order);
		$client->setFranco($franco);

		if($user_password == $user_password_confirmation && !empty($user_password)) {
			$client->setPassword($user_password);
		}

		$client->setUserinfos(array('lastname' => $user_lastname, 'firstname' => $user_firstname));

		$client->save();

		$client->setPaymentMeans($a_paymentMeans);

		$user = $client->getMainUser();
		$contact = $user->getContact();

		$contact->setFirstname($user_firstname);
		$contact->setLastname($user_lastname);
		$contact->setEmail($user_email);
		$contact->save();

		$clienttier = $client->getTiers();

		include_once DIMS_APP_PATH . 'modules/system/class_tiers_contact.php';
		$lk = tiersct::find_by(array(
			'id_tiers' =>       $clienttier->get('id'),
			'id_contact' =>     $contact->getId(),
			'id_workspace' =>   $_SESSION['dims']['workspaceid'],
		), null, 1);

		if(empty($lk)) {
			$lk = new tiersct();
			$lk->init_description();

			$lk->set('id_tiers',    $clienttier->get('id'));
			$lk->set('id_contact',  $contact->getId());
			$lk->set('link_level',  2);
			$lk->set('date_deb',    dims_createtimestamp());
			$lk->set('type_lien',   $_SESSION['cste']['_DIMS_LABEL_EMPLOYEUR']);
			$lk->set('date_fin',    0);
			$lk->set('function',    '');
		}

		$lk->save();

		dims_redirect(get_path('clients', 'show', array('id' => $client->get('id_client'))));
		break;
	case 'switch_filters':
		ob_clean();
		$cur_mode	= &get_sessparam($_SESSION['cata']['clients']['filters_mode'], 'show');
		$mode		= dims_load_securvalue('mode', dims_const::_DIMS_CHAR_INPUT, true,true, true, $cur_mode);
		die();
		break;
	case 'block':
		ob_clean();
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, false, true);
		if ($id > 0) {
			$client = new client();
			$client->open($id);
			$client->block();
			$client->save();
		}
		dims_redirect(get_path('clients', 'index'));
		break;
	case 'unblock':
		ob_clean();
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, false, true);
		if ($id > 0) {
			$client = new client();
			$client->open($id);
			$client->unblock();
			$client->save();
		}
		dims_redirect(get_path('clients', 'index'));
		break;

	case 'ac_clients':#AC = auto-complete (utilisé dans le formulaire d'édition des prix nets d'un article)
		ob_clean();
		$text = dims_load_securvalue('text', dims_const::_DIMS_CHAR_INPUT, true,true, true);
		if(!empty($text)){
			$cli = new client();
			$cli->setPaginated(false);
			$lst_clients = $cli->build_index(client::STATUS_OK, $text);
			$lst = array();
			$i = 0;
			foreach($lst_clients as $client){
				$lst[$i]['id'] = $client->getCode();#Cyril - La table des prix nets stocke le code du client et non l'article
				$lst[$i]['label'] = $client->getCode(). ' - ' .$client->getName();
				$i++;
			}
			echo json_encode($lst);
		}
		die();
		break;
}

$view->assign('last_clients',get_lastclients());
$view->render('clients/lateral.tpl.php', 'lateral');
?>
