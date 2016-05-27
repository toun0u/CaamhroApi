<?php
$view = view::getInstance();
$view->setLayout('layouts/quotation_layout.tpl.php');

include_once DIMS_APP_PATH."modules/catalogue/include/class_commande.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_moyen_paiement.php";
include_once DIMS_APP_PATH."modules/catalogue/admin/helpers/commandes_web_helpers.php";

// infos contexuelles
$view->assign('a', $a);

$view->assign('path_lateral_search', get_path('quotations', 'list'));
$view->assign('default_search', dims_constant::getVal('SEARCH_QUOTATION'));
$view->assign('keywords', '');

switch ($a) {
	default:
		dims_redirect(\get_path('quotations', 'list'));
		break;
	case 'list':
		$actions = array();
		$actions[0]['picto'] = 'gfx/ajouter16.png';
		$actions[0]['text'] = dims_constant::getVal('ADD_QUOTATION');
		$actions[0]['link'] = dims::getInstance()->getScriptEnv().'?c=quotations&a=new';
		$view->assign('actions', $actions);

		// Display des filtres
		if(!isset($_SESSION['cata']['quotations']['filters_mode'])) $_SESSION['cata']['quotations']['filters_mode'] = true;
		$view->assign('filters_mode', $_SESSION['cata']['quotations']['filters_mode']);

		#Gestion des filtres
		$init_filter = dims_load_securvalue('filter_init', dims_const::_DIMS_NUM_INPUT, true, true, true);

		if(isset($init_filter) && $init_filter){
			unset($_SESSION['cata']['quotations']['index']);
		}

		$dd = date('01/m/Y');
		$df = date('t/m/Y');

		$cur_date_deb   = &get_sessparam($_SESSION['cata']['quotations']['index']['date_deb'],  $dd);
		$cur_date_fin   = &get_sessparam($_SESSION['cata']['quotations']['index']['date_fin'],  $df);
		$cur_state      = &get_sessparam($_SESSION['cata']['quotations']['index']['state'],     -1);
		$cur_payment    = &get_sessparam($_SESSION['cata']['quotations']['index']['payment'],   0);
		$cur_keywords   = &get_sessparam($_SESSION['cata']['quotations']['index']['keywords'],  '');
		$cur_client     = &get_sessparam($_SESSION['cata']['quotations']['index']['client'],    0);
		$cur_page       = &get_sessparam($_SESSION['cata']['quotations']['index']['page'],      0);
		$cur_sort_by    = &get_sessparam($_SESSION['cata']['quotations']['index']['sort_by'],   'date_cree');
		$cur_sort_way   = &get_sessparam($_SESSION['cata']['quotations']['index']['sort_way'],  'ASC');

		$date_deb       = dims_load_securvalue('date_deb',  dims_const::_DIMS_CHAR_INPUT,   true,true, true, $cur_date_deb, $dd,            true);
		$date_fin       = dims_load_securvalue('date_fin',  dims_const::_DIMS_CHAR_INPUT,   true,true, true, $cur_date_fin, $df,            true);
		$state          = dims_load_securvalue('state',     dims_const::_DIMS_CHAR_INPUT,   true,true, true, $cur_state,    -1,             true);
		$payment        = dims_load_securvalue('payment',   dims_const::_DIMS_NUM_INPUT,    true,true, true, $cur_payment,  0,              true);
		$keywords       = dims_load_securvalue('keywords',  dims_const::_DIMS_NUM_INPUT,    true,true, true, $cur_keywords, '',             true);
		$client         = dims_load_securvalue('client',    dims_const::_DIMS_NUM_INPUT,    true,true, true, $cur_client,   '',             true);
		$page           = dims_load_securvalue('page',      dims_const::_DIMS_CHAR_INPUT,   true,true, true, $cur_page,     0,              true);
		$client         = dims_load_securvalue('client',    dims_const::_DIMS_NUM_INPUT,    true,true, true, $cur_client,   '',             true);
		$page           = dims_load_securvalue('page',      dims_const::_DIMS_CHAR_INPUT,   true,true, true, $cur_page,     0,              true);
		$sort_by        = dims_load_securvalue('sort_by',   dims_const::_DIMS_CHAR_INPUT,   true,true, true, $cur_sort_by,  'date_cree',    true);
		$sort_way       = dims_load_securvalue('sort_way',  dims_const::_DIMS_CHAR_INPUT,   true,true, true, $cur_sort_way, 'ASC',          true);

		$view->assign('date_deb',   $date_deb);
		$view->assign('date_fin',   $date_fin);
		$view->assign('state',      $state);
		$view->assign('payment',    $payment);
		$view->assign('keywords',   $keywords);
		$view->assign('client',     $client);
		$view->assign('page',       $page);
		$view->assign('sort_by',    $sort_by);
		$view->assign('sort_way',   $sort_way);

		$quotation = new cata_facture();
		$quotation->page_courant = $page;
		$quotation->setPaginationParams(50, 5, false, '<<', '>>', '<', '>');
		$quotations = $quotation->build_index($state, $client, $date_deb, $date_fin, $payment, $keywords, $sort_by, $sort_way);
		$view->assign('total_quotations', $quotation->total_index);

		#assignation du contenu de la pagination
		$view->assign('pagination', $quotation->getPagination());
		#assignation des articles à la vue
		$view->assign('quotations', $quotations);

		$view->assign('lst_clients', client::getAllSelect());
		$view->assign('states', array(-1 => 'Tous') + cata_facture::getstateslist());
		$view->assign('paiements', moyen_paiement::getPaiementsSelect());

		$view->assign('action_path', get_path('quotations', 'list'));
		$view->assign('back_path', get_path('quotations', 'list', array('filter_init' => 1) ));

		$view->render('quotations/list.tpl.php');
		break;
	case 'new':
		$actions = $view->get('actions');

		// Overwrite return to clients link.
		$actions[0]['picto'] = 'gfx/retour20.png';
		$actions[0]['text'] = dims_constant::getVal('RETURN_TO_QUOTATIONS_LIST');
		$actions[0]['link'] = get_path('quotations', 'list');
		$view->assign('actions', $actions);

		$quotationid    = dims_load_securvalue('quotationid',   dims_const::_DIMS_NUM_INPUT, true, true);
		$clientid       = dims_load_securvalue('clientid',      dims_const::_DIMS_NUM_INPUT, true, true);
		$caseid         = dims_load_securvalue('caseid',        dims_const::_DIMS_NUM_INPUT, true, true);

		$quotation = new cata_facture();

		$clientslist    = array();
		$client         = new client();
		$case           = new dims_case();

		if(!empty($quotationid)) {
			$quotation->open($quotationid);

			$client = $quotation->getclient();

			$clientslist[$client->getId()] = $client->get('nom');
		} else {
			$quotation->init_description();
			$quotation->setugm();
		}

		if(!empty($clientid)) {
			$client = new client();
			$client->open($clientid);
		} else {
			$client->init_description();
		}

		if(!empty($caseid)) {
			$case->open($caseid);
		} else {
			$case->init_description();
		}

		$view->assign('case',           $case);
		$view->assign('client',         $client);
		$view->assign('clientslist',    $clientslist);
		$view->assign('quotation',      $quotation);

		$view->render('quotations/edit.tpl.php');
		break;
	case 'save':
		$quotationid    = dims_load_securvalue('quotationid',   dims_const::_DIMS_NUM_INPUT,    true, true);
		$codeclient     = dims_load_securvalue('codeclient',    dims_const::_DIMS_CHAR_INPUT,   true, true);

		$quotation = new cata_facture();
		if(!empty($quotationid)) {
			$quotation->open($quotationid);
		} else {
			$quotation->init_description();
			$quotation->setugm();

			$quotation->fields['type'] = cata_facture::TYPE_QUOTATION;
		}

		$quotation->setvalues($_POST, 'quotation_');

		$date = dims_load_securvalue('date', dims_const::_DIMS_CHAR_INPUT, true, true);
		$quotation->fields['date_cree'] = dims_local2timestamp($date);

		if(!empty($codeclient)) {
			$client = new client();
			$client->openByCode($codeclient);

			if(!$client->isNew()) {
				$quotation->fields['id_client'] = $client->getId();
			}
		}

		$quotation->save();

		if(!empty($quotation->fields['id_client'])) {
			dims_redirect(get_path('clients', 'show', array('id' => $quotation->get('id_client'), 'sc' => 'quotations', 'sa' => 'show', 'quotationid' => $quotation->get('id'))));
		} else {
			dims_redirect(get_path('quotations', 'list'));
		}
		break;
}

$view->render('quotations/lateral.tpl.php', 'lateral');
