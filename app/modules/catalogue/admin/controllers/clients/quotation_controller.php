<?php

include_once DIMS_APP_PATH . 'modules/catalogue/include/class_client.php';
include_once DIMS_APP_PATH . 'modules/catalogue/include/class_facture.php';
include_once DIMS_APP_PATH . 'modules/catalogue/include/class_config.php';

include_once DIMS_APP_PATH . 'modules/system/suivi/class_print_model.php';
include_once DIMS_APP_PATH . 'modules/system/suivi/class_suivi_type.php';

$view = view::getInstance();
$sub_action = $view->get('sa');

$view->setLayout('layouts/clients_quotation_layout.tpl.php');

switch($sub_action) {
	default:
	case 'list':
		$actions = $view->get('actions');
		$actions[1]['picto'] = 'gfx/ajouter16.png';
		$actions[1]['text'] = dims_constant::getVal('ADD_QUOTATION');
		$actions[1]['link'] = get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'quotations', 'sa' => 'new'));;
		$view->assign('actions', $actions);

		$client = $view->get('client');

		$view->assign('quotations', $client->getquotations());

		$view->render('clients/show/quotation.tpl.php');
		break;
	case 'new':
	case 'editheader':
		$client = $view->get('client');

		$actions = $view->get('actions');

		// Overwrite return to clients link.
		$actions[0]['picto'] = 'gfx/retour20.png';
		$actions[0]['text'] = dims_constant::getVal('RETURN_TO_QUOTATIONS_LIST');
		$actions[0]['link'] = get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'quotations', 'sa' => 'list'));;
		$view->assign('actions', $actions);

		$quotationid = dims_load_securvalue('quotationid', dims_const::_DIMS_NUM_INPUT, true, true);

		$quotation = new cata_facture();

		if(!empty($quotationid)) {
			$quotation->open($quotationid);
		} else {
			$quotation->init_description();
			$quotation->setugm();
		}

		$clientslist = array();

		$clientslist[$client->getId()] = $client->get('nom');

		$view->assign('clientslist',    $clientslist);
		$view->assign('quotation',      $quotation);

		$view->render('clients/show/quotation_edit.tpl.php');
		break;
	case 'save':
		$quotationid = dims_load_securvalue('quotationid', dims_const::_DIMS_NUM_INPUT, true, true);

		$quotation = new cata_facture();
		if(!empty($quotationid)) {
			$quotation->open($quotationid);
		} else {
			$quotation->init_description();
			$quotation->setugm();

			$quotation->fields['type'] = cata_facture::TYPE_QUOTATION;
		}

		$quotation->setvalues($_POST, 'quotation_');

		$quotation->set('id_client', $client->get('id_client'));

		$date = dims_load_securvalue('date', dims_const::_DIMS_CHAR_INPUT, true, true);
		$quotation->fields['date_cree'] = dims_local2timestamp($date);

		$quotation->save();
		$client = $view->get('client');
		dims_redirect(get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'quotations', 'sa' => 'show', 'quotationid' => $quotation->get('id'))));
		break;
	case 'duplicate':
		$quotationid = dims_load_securvalue('quotationid', dims_const::_DIMS_NUM_INPUT, true, true);
		$client = $view->get('client');

		if(!empty($quotationid)) {
			$fromquotation = new cata_facture();
			$fromquotation->open($quotationid);

			$newquotation = clone $fromquotation;

			dims_redirect(get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'quotations', 'sa' => 'show', 'quotationid' => $newquotation->get('id'))));
		}

		dims_redirect(get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'quotations', 'sa' => 'list')));
		break;
	case 'validate':
		$quotationid = dims_load_securvalue('quotationid', dims_const::_DIMS_NUM_INPUT, true, true);
		$client = $view->get('client');

		if(!empty($quotationid)) {
			$quotation = new cata_facture();
			$quotation->open($quotationid);

			$quotation->validate();

			dims_redirect(get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'quotations', 'sa' => 'show', 'quotationid' => $quotation->get('id'))));
		}

		dims_redirect(get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'quotations', 'sa' => 'list')));
		break;
	case 'refuse':
		$quotationid = dims_load_securvalue('quotationid', dims_const::_DIMS_NUM_INPUT, true, true);
		$client = $view->get('client');

		if(!empty($quotationid)) {
			$quotation = new cata_facture();
			$quotation->open($quotationid);

			$quotation->refuse();

			dims_redirect(get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'quotations', 'sa' => 'show', 'quotationid' => $quotation->get('id'))));
		}

		dims_redirect(get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'quotations', 'sa' => 'list')));
		break;
	case 'delete':
		$quotationid = dims_load_securvalue('quotationid', dims_const::_DIMS_NUM_INPUT, true, true);
		$client = $view->get('client');

		if(!empty($quotationid)) {
			$quotation = new cata_facture();
			$quotation->open($quotationid);

			$quotation->delete();

			dims_redirect(get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'quotations', 'sa' => 'list', )));
		}

		dims_redirect(get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'quotations', 'sa' => 'list')));
		break;
	case 'show':
	case 'editquoteline':
		$quotationid = dims_load_securvalue('quotationid', dims_const::_DIMS_NUM_INPUT, true, true);
		$client = $view->get('client');

		if(empty($quotationid)) {
			dims_redirect(get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'quotations', 'sa' => 'list')));
		}

		$actions = $view->get('actions');

		// Overwrite return to clients link.
		$actions[0]['picto'] = 'gfx/retour20.png';
		$actions[0]['text'] = dims_constant::getVal('RETURN_TO_QUOTATIONS_LIST');
		$actions[0]['link'] = get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'quotations', 'sa' => 'list'));;
		$view->assign('actions', $actions);

		$quotation = new cata_facture();
		$quotation->open($quotationid);

		$quotelines = $quotation->getlines();

		$articles = array();
		if(!empty($quotelines)) {
			foreach(article::find_by(array('reference' => dims_data_object::extractfield($quotelines, 'ref'))) as $article) {
				$articles[$article->fields['reference']] = $article;
			}
		}

		$quotelineedition = new cata_facture_detail();
		if($sub_action == 'editquoteline') {
			$quotelineid = dims_load_securvalue('quotelineid', dims_const::_DIMS_NUM_INPUT, true, true);

			$quotelineedition->open($quotelineid);
		} else {
			$quotelineedition->init_description();
		}

		$listsuivitypes = array_map(function($suivitype) {
			return $suivitype->getId();
		}, suivi_type::find_by(array('type_doc' => $quotation->get('type'))));

		$docmodels = print_model::find_by(array('id_type' => $listsuivitypes), ' ORDER BY label');

		$creator = new user();
		$creator->open($quotation->get('id_user'));

		$case = new dims_case();
		if(!empty($quotation->fields['id_case'])) {
			$case->open($quotation->fields['id_case']);
		}

		$config = cata_config::get($_SESSION['dims']['moduleid']);

		$view->assign('quotation',          $quotation);
		$view->assign('quotelines',         $quotelines);
		$view->assign('articles',           $articles);
		$view->assign('quotelineedition',   $quotelineedition);
		$view->assign('caneditlines',       $config->geteditquotelines());
		$view->assign('creator',            $creator);
		$view->assign('docmodels',          $docmodels);
		$view->assign('case',               $case);

		$view->render('clients/show/quotation_show.tpl.php');
		break;
	case 'printout':
		$client = $view->get('client');

		$quotationid = dims_load_securvalue('idquotation', dims_const::_DIMS_NUM_INPUT, true, true);

		if(!empty($quotationid)) {
			$suivi_modele   = dims_load_securvalue('suivi_modele', dims_const::_DIMS_NUM_INPUT, true, true);
			$format         = dims_load_securvalue('format', dims_const::_DIMS_CHAR_INPUT, true, true);

			$quotation = new cata_facture();
			$quotation->open($quotationid);

			ob_clean();
			$quotation->printout($suivi_modele,$format);
			die();
		}

		dims_redirect(get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'quotations', 'sa' => 'list')));
		break;
	case 'savequoteline':
		$quotationid    = dims_load_securvalue('idquotation',       dims_const::_DIMS_NUM_INPUT,    true, true, true);
		$idarticle      = dims_load_securvalue('idarticle',         dims_const::_DIMS_NUM_INPUT,    true, true, true);
		$quotelineid    = dims_load_securvalue('idquoteline',       dims_const::_DIMS_NUM_INPUT,    true, true, true);
		$searchedref    = dims_load_securvalue('searchreference',   dims_const::_DIMS_CHAR_INPUT,   true, true, true);
		$quantity       = dims_load_securvalue('quantity',          dims_const::_DIMS_NUM_INPUT,    true, true, true);

		$config = cata_config::get($_SESSION['dims']['moduleid']);

		if(!empty($searchedref) && !empty($quantity)) {
			$quotation = new cata_facture();
			$quotation->open($quotationid);

			$article = new article();

			if(!empty($idarticle)) {
				$article->open($idarticle);
			} else {
				$article->init_description();
			}

			$quoteline = new cata_facture_detail();

			if(!empty($quotelineid)) {
				$quoteline->open($quotelineid);
			} else {
				$quoteline->init_description();
				$quoteline->setugm();
			}

			$quoteline->fields['id_facture'] = $quotationid;

			if(!$article->isNew()) {
				$quoteline->fields['id_article']    = $article->fields['id'];
				$quoteline->fields['ref']           = $article->fields['reference'];
			} else {
				$quoteline->fields['id_article']    = 0;
				$quoteline->fields['ref']           = $searchedref;
			}

			$quoteline->fields['qte']           = $quantity;
			$quoteline->fields['description']   = dims_load_securvalue('description',   dims_const::_DIMS_CHAR_INPUT,   true, true, true);

			if($config->geteditquotelines()) {
				$quoteline->fields['label']     = dims_load_securvalue('libelle',   dims_const::_DIMS_CHAR_INPUT,   true, true, true);
				$quoteline->fields['pu_ht']     = dims_load_securvalue('puht',      dims_const::_DIMS_CHAR_INPUT,   true, true, true);
				$quoteline->fields['remise']    = dims_load_securvalue('discount',  dims_const::_DIMS_CHAR_INPUT,   true, true, true);
				$quoteline->fields['tx_tva']    = dims_load_securvalue('tva',       dims_const::_DIMS_CHAR_INPUT,   true, true, true);
			} else {
				$quoteline->fields['label']     = $article->fields['label'];
				$quoteline->fields['pu_ht']     = $article->fields['putarif_0'];
				$quoteline->fields['remise']    = $quotation->fields['discount'];
				$quoteline->fields['tx_tva']    = $article->getTauxTVA();
			}

			$quoteline->save();

			$quotation->computetotals();
		}

		dims_redirect(get_path(
			'clients',
			'show',
			array(
				'id' => $client->get('id_client'),
				'sc' => 'quotations',
				'sa' => 'show',
				'quotationid' => $quotationid,
			)
		));
		break;
	case 'quoteline_up':
	case 'quoteline_down':
		$quotelineid = dims_load_securvalue('quotelineid', dims_const::_DIMS_NUM_INPUT, true, true, true);
		$quotationid = dims_load_securvalue('quotationid', dims_const::_DIMS_NUM_INPUT, true, true, true);

		$quoteline = new cata_facture_detail();
		$quoteline->open($quotelineid);

		if($quoteline->fields['id_facture'] == $quotationid) {
			if($sub_action == 'quoteline_up') {
				$quoteline->moveup();
			} else {
				$quoteline->movedown();
			}
		}

		dims_redirect(get_path(
			'clients',
			'show',
			array(
				'id' => $client->get('id_client'),
				'sc' => 'quotations',
				'sa' => 'show',
				'quotationid' => $quotationid,
			)
		));
		break;
	case 'deletequoteline':
		$quotelineid = dims_load_securvalue('quotelineid', dims_const::_DIMS_NUM_INPUT, true, true, true);
		$quotationid = dims_load_securvalue('quotationid', dims_const::_DIMS_NUM_INPUT, true, true, true);

		$quotation = new cata_facture();
		$quotation->open($quotationid);

		$quoteline = new cata_facture_detail();
		$quoteline->open($quotelineid);

		if($quoteline->fields['id_facture'] == $quotationid) {
			$quoteline->delete();
		}

		$quotation->computetotals();

		dims_redirect(get_path(
			'clients',
			'show',
			array(
				'id' => $client->get('id_client'),
				'sc' => 'quotations',
				'sa' => 'show',
				'quotationid' => $quotationid,
			)
		));
		break;
}
