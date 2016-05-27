<?php
$view = view::getInstance();
$view->setLayout('layouts/commande_layout.tpl.php');

include_once DIMS_APP_PATH."modules/catalogue/include/class_commande.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_moyen_paiement.php";
include_once DIMS_APP_PATH."modules/catalogue/admin/helpers/commandes_web_helpers.php";

// infos contexuelles
$view->assign('a', $a);

$view->assign('path_lateral_search',get_path('commandes','index'));
$view->assign('default_search',dims_constant::getVal('_SEARCH_COMMAND'));
$view->assign('keywords',"");

switch ($a) {
	default:
	case 'index':
		$actions = array();
		$actions[0]['picto'] = 'gfx/info16.png';
		$actions[0]['text'] = dims_constant::getVal('_EXPORT_ORDERS');
		$actions[0]['link'] = dims::getInstance()->getScriptEnv().'?c=commandes&a=allcsv';
		$view->assign('actions', $actions);

		// Display des filtres
		if(!isset($_SESSION['cata']['commandes']['filters_mode'])) $_SESSION['cata']['commandes']['filters_mode'] = true;
		$view->assign('filters_mode',$_SESSION['cata']['commandes']['filters_mode']);

		#Gestion des filtres
		$init_filter        = dims_load_securvalue('filter_init',   dims_const::_DIMS_NUM_INPUT, true,true, true);

		if(isset($init_filter) && $init_filter){
			unset($_SESSION['cata']['commandes']['index']);
		}

		$dd = date('01/m/Y');
		$df = date('t/m/Y');

		$cur_date_deb   = &get_sessparam($_SESSION['cata']['commandes']['index']['date_deb'], $dd);
		$cur_date_fin   = &get_sessparam($_SESSION['cata']['commandes']['index']['date_fin'], $df);
		$cur_state      = &get_sessparam($_SESSION['cata']['commandes']['index']['state'], 0);
		$cur_payment    = &get_sessparam($_SESSION['cata']['commandes']['index']['payment'], 0);
		$cur_keywords   = &get_sessparam($_SESSION['cata']['commandes']['index']['keywords'], '');
		$cur_client     = &get_sessparam($_SESSION['cata']['commandes']['index']['client'], 0);
		$cur_page       = &get_sessparam($_SESSION['cata']['commandes']['index']['page'], 0);
		$cur_sort_by    = &get_sessparam($_SESSION['cata']['commandes']['index']['sort_by'], 'date_cree');
		$cur_sort_way   = &get_sessparam($_SESSION['cata']['commandes']['index']['sort_way'], 'ASC');

		$date_deb       = dims_load_securvalue('date_deb', dims_const::_DIMS_CHAR_INPUT, true,true, true, $cur_date_deb, $dd, true);
		$date_fin       = dims_load_securvalue('date_fin', dims_const::_DIMS_CHAR_INPUT, true,true, true, $cur_date_fin, $df, true);
		$state          = dims_load_securvalue('state', dims_const::_DIMS_CHAR_INPUT, true,true, true, $cur_state, 0, true);
		$payment        = dims_load_securvalue('payment', dims_const::_DIMS_NUM_INPUT, true,true, true, $cur_payment, 0, true);
		$keywords       = dims_load_securvalue('keywords', dims_const::_DIMS_NUM_INPUT, true,true, true, $cur_keywords, '', true);
		$client         = dims_load_securvalue('client', dims_const::_DIMS_NUM_INPUT, true,true, true, $cur_client, '', true);
		$page           = dims_load_securvalue('page', dims_const::_DIMS_CHAR_INPUT, true,true, true, $cur_page, 0, true);
		$client         = dims_load_securvalue('client', dims_const::_DIMS_NUM_INPUT, true,true, true, $cur_client, '', true);
		$page           = dims_load_securvalue('page', dims_const::_DIMS_CHAR_INPUT, true,true, true, $cur_page, 0, true);
		$sort_by        = dims_load_securvalue('sort_by', dims_const::_DIMS_CHAR_INPUT, true,true, true, $cur_sort_by, 'date_cree', true);
		$sort_way       = dims_load_securvalue('sort_way', dims_const::_DIMS_CHAR_INPUT, true,true, true, $cur_sort_way, 'ASC', true);

		$view->assign('date_deb', $date_deb);
		$view->assign('date_fin', $date_fin);
		$view->assign('state', $state);
		$view->assign('payment', $payment);
		$view->assign('keywords', $keywords);
		$view->assign('client', $client);
		$view->assign('page', $page);
		$view->assign('sort_by', $sort_by);
		$view->assign('sort_way', $sort_way);

		$cde = new commande();
		$cde->page_courant = $page;
		$cde->setPaginationParams(10, 5, false, '<<', '>>', '<', '>');
		$commandes = $cde->build_index( $state, $client, $date_deb, $date_fin, $payment, $keywords, $sort_by, $sort_way);
		$view->assign('total_commandes', $cde->total_index);

		#assignation du contenu de la pagination
		$view->assign('pagination', $cde->getPagination());
		#assignation des articles à la vue
		$view->assign('commandes', $commandes);

		$view->assign('lst_clients',client::getAllSelect());
		$view->assign('states', commande::getStatesSelect());
		$view->assign('paiements', moyen_paiement::getPaiementsSelect());

		$view->assign('action_path',get_path('commandes','index'));
		$view->assign('back_path',get_path('commandes','index', array('filter_init' => 1) ));
		$view->render('commandes/index.tpl.php','info_cde');
		break;
	case 'show':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		if ($id != '' && $id > 0) {

			store_lastcommande($id,3);

			$cde = new commande();
			$cde->open($id);
			$view->assign('commande',$cde);

			$oCatalogue = new catalogue();
			$oCatalogue->open($_SESSION['dims']['moduleid']);//$catalogue_moduleid);
			$oCatalogue->loadParams();
			$view->assign('oCatalogue',$oCatalogue);


			// Actions
			$actions = array();
			$actions[0]['picto'] = 'gfx/retour20.png';
			$actions[0]['text'] = dims_constant::getVal('_BACK_LIST_COMMANDS');
			$actions[0]['link'] = dims::getInstance()->getScriptEnv().'?c=commandes&a=index';
			$actions[1]['picto'] = 'gfx/info16.png';
			$actions[1]['text'] = dims_constant::getVal('_EXPORT_THIS_COMMAND');
			$actions[1]['link'] = dims::getInstance()->getScriptEnv().'?c=commandes&a=csv&id='.$id;
			$actions[2]['picto'] = 'gfx/info16.png';
			$actions[2]['text'] = dims_constant::getVal('_PRINT_TO_PDF');
			$actions[2]['link'] = dims::getInstance()->getScriptEnv().'?c=commandes&a=pdf&id='.$id;

			if ($cde->fields['etat'] == commande::_STATUS_VALIDATED) {
				$actions[3]['picto'] = 'gfx/info16.png';
				$actions[3]['text'] = dims_constant::getVal('GENERATE_SYNCHRO_FILE');
				$actions[3]['link'] = dims::getInstance()->getScriptEnv().'?c=commandes&a=generatesyncfile&id='.$id;
				$actions[4]['picto'] = 'gfx/info16.png';
				$actions[4]['text'] = dims_constant::getVal('RESEND_CONFIRMATION_ORDER_EMAIL');
				$actions[4]['link'] = dims::getInstance()->getScriptEnv().'?c=commandes&a=resendemail&idorder='.$id;
			}
			elseif ($cde->fields['etat'] == commande::_STATUS_PROGRESS) {
				$actions[] = array(
					'picto' => 'gfx/info16.png',
					'text' => dims_constant::getVal('RESEND_COSTING_CONFIRMATION_ORDER_EMAIL'),
					'link' => dims::getInstance()->getScriptEnv().'?c=commandes&a=resend_constingdone_email&idorder='.$id
				);
			}

			if ($cde->fields['etat'] == commande::_STATUS_AWAITING_COSTING) {
				$actions[] = array(
					'picto' => 'gfx/pastille_orange16.png',
					'text'  => dims_constant::getVal('CONFIRM_COSTING'),
					'link'  => dims::getInstance()->getScriptEnv().'?c=commandes&a=confirm_costing&id_cde='.$id,
				);
			}
			if ($cde->isValideable()) {
				$actions[] = array(
					'picto' => 'gfx/pastille_verte16.png',
					'text'  => dims_constant::getVal('_CONFIRM_SETTLEMENT'),
					'link'  => dims::getInstance()->getScriptEnv().'?c=commandes&a=confirm&id='.$id,
				);
			}
			$view->assign('actions', $actions);

			$view->assign('lst_bases',$cde->getBases());

			$view->assign('states', commande::getStatesSelect());
			$user = new user();
			$user->open($cde->fields['id_user']);
			$view->assign('user_create',$user);

			$view->render('commandes/show.tpl.php','info_cde');

			$ca = dims_load_securvalue('ca',dims_const::_DIMS_CHAR_INPUT,true,true,false);
			switch ($ca) {
				case 'detail':
				default:
					$ca = 'detail';
					$subMenu = "commandes/detail.tpl.php";
					$view->assign('lignes',$cde->getlignes());
					break;
				case 'livraison':
					$view->assign('_DEFAULT_ZOOM',9);
					$view->assign('_DEFAULT_LAT',48.623);
					$view->assign('_DEFAULT_LON',6.26);

					$work = new workspace();
					$work->open($_SESSION['dims']['workspaceid']);
					$tiers = $work->getTiers();
					$address = "";
					if(!$tiers->isNew()){
						$country = new country();
						$country->open($tiers->fields['id_country']);
						$pays = (isset($country->fields['name']))?" (".$country->fields['name'].")":"";
						$address = $tiers->fields['adresse'].", ".$tiers->fields['codepostal']." ".$tiers->fields['ville'].", ".$pays;
					}
					$view->assign('origine',$address);

					$subMenu = "commandes/livraison.tpl.php";
					break;
			}
			$view->assign('ca', $ca);
			$view->render($subMenu,'sub_menu');
		} else {
			dims_redirect(get_path('commandes', 'index'));
		}
		break;
	case 'switch_filters':
		ob_clean();
		$_SESSION['cata']['commandes']['filters_mode'] = !$_SESSION['cata']['commandes']['filters_mode'];
		die();
		break;
	case 'allcsv': // export csv filtre
		$dd = date('01/m/Y');
		$df = date('t/m/Y');

		$date_deb   = &get_sessparam($_SESSION['cata']['commandes']['index']['date_deb'], $dd);
		$date_fin   = &get_sessparam($_SESSION['cata']['commandes']['index']['date_fin'], $df);
		$state      = &get_sessparam($_SESSION['cata']['commandes']['index']['state'], 0);
		$payment    = &get_sessparam($_SESSION['cata']['commandes']['index']['payment'], 0);
		$keywords   = &get_sessparam($_SESSION['cata']['commandes']['index']['keywords'], '');
		$client     = &get_sessparam($_SESSION['cata']['commandes']['index']['client'], 0);
		$sort_by    = &get_sessparam($_SESSION['cata']['commandes']['index']['sort_by'], 'date_cree');
		$sort_way   = &get_sessparam($_SESSION['cata']['commandes']['index']['sort_way'], 'ASC');

		$cde = new commande();
		$cde->activePagination(false);
		$commandes = $cde->build_index( $state, $client, $date_deb, $date_fin, $payment, $keywords, $sort_by, $sort_way);
		export_csv_commandes($commandes);
		break;
	case 'csv': // export csv
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		if($id != '' && $id > 0){
			$cde = new commande();
			$cde->open($id);
			$cde->exportCommande('csv');
		}
		break;
	case 'pdf': // export pdf
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		if($id != '' && $id > 0){
			$cde = new commande();
			$cde->open($id);
			$cde->exportCommande('pdf');
		}
		break;
	case 'confirm_costing':
		$idCde = dims_load_securvalue('id_cde',dims_const::_DIMS_NUM_INPUT,true,true);
		if (!empty($idCde)) {
			$cde = new commande();
			$cde->open($idCde);

			$cde->confirmCosting();

			$view->flash(dims_constant::getVal('_ORDER_CONFIRMED'), 'success');

			// Envoi de la notification à l'utilisateur qui a passé la commande
			$user = new user();
			$user->open($cde->get('id_user'));
			if ($user->get('email') != "") {
				$template_name = gettemplatename();
				$cde->sendCostingDoneMail($user->get('email'), $template_name);
			}
			else {
				$view->flash(dims_constant::getVal('_CATA_NO_MAIL_COSTING'), 'error');
			}

			dims_redirect(get_path('commandes', 'show', array('id' => $cde->get('id_cde'))));
		}
		dims_redirect(get_path('commandes', 'index'));
		break;
	case 'confirm': // confirmer le règlement
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		if($id != '' && $id > 0){
			$cde = new commande();
			$cde->open($id);
			$cde->validate();
			$view->flash(dims_constant::getVal('_ORDER_CONFIRMED'), 'success');
			dims_redirect(get_path('commandes', 'show',array('id'=>$id)));
		}
		dims_redirect(get_path('commandes', 'index'));
		break;
	case 'generatesyncfile':
		$idorder = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true, true);

		if(!empty($idorder)) {
			write_cmd_file($idorder);
			$view->flash(dims_constant::getVal('SYNC_FILE_GENERATED'), 'success');
			dims_redirect(get_path('commandes', 'show', array('id' => $idorder)));
		}
		dims_redirect(get_path('commandes', 'index'));
		break;
	case 'resendemail':
		$idorder = dims_load_securvalue('idorder',dims_const::_DIMS_NUM_INPUT,true,true);

		$oCatalogue = new catalogue();
		$oCatalogue->open($_SESSION['dims']['moduleid']);//$catalogue_moduleid);
		$oCatalogue->loadParams();

		if(!empty($idorder)){
			$templatename = gettemplatename();

			$cde = new commande();
			$cde->open($idorder);
			$cde->sendConfirmationMail($cde->fields['cli_email'], $templatename);

			if (isset($oCatalogue)) {
				foreach (explode(',', $oCatalogue->getParams('notif_send_mail')) as $email) {
					$cde->sendConfirmationMail(trim($email), $templatename);
				}
			}

			$view->flash(dims_constant::getVal('CONFIRMATION_ORDER_EMAIL_RESENT'), 'success');
			dims_redirect(get_path('commandes', 'show',array('id'=>$idorder)));
		}
		dims_redirect(get_path('commandes', 'index'));
		break;
	case 'resend_constingdone_email':
		$idorder = dims_load_securvalue('idorder',dims_const::_DIMS_NUM_INPUT,true,true);
		if(!empty($idorder)){
			$templatename = gettemplatename();

			$cde = new commande();
			$cde->open($idorder);

			$user = new user();
			$user->open($cde->get('id_user'));
			if ($user->get('email') != "") {
				$template_name = gettemplatename();
				$cde->sendCostingDoneMail($user->get('email'), $template_name);
				$view->flash(dims_constant::getVal('CONFIRMATION_ORDER_EMAIL_RESENT'), 'success');
			}
			else {
				$view->flash(dims_constant::getVal('_CATA_NO_MAIL_COSTING'), 'error');
			}

			dims_redirect(get_path('commandes', 'show',array('id'=>$idorder)));
		}
		dims_redirect(get_path('commandes', 'index'));
		break;
	case 'edit_head':
		$idCde = dims_load_securvalue('id_cde', dims_const::_DIMS_NUM_INPUT, true, true);
		$portCde = dims_load_securvalue('cde_port', dims_const::_DIMS_NUM_INPUT, true, true);

		if (!empty($idCde)) {
			$cde = new commande();
			$cde->open($idCde);

		if ($portCde<0){
			$view->flash(dims_constant::getVal('_PORT_NOT_NEGATIVE'), 'error');
			dims_redirect(get_path('commandes', 'show', array('id' => $cde->get('id_cde'))));
		}
			$oldPort = floatval($cde->fields['port']);
			$cde->fields['port'] = str_replace(',', '.', dims_load_securvalue('cde_port', dims_const::_DIMS_CHAR_INPUT, true, true, true));
			$newPort = floatval($cde->fields['port']);

			if($cde->fields['cli_liv_cp'] != -1){
				if(true || $oldPort != $newPort){
					$oCatalogue = new catalogue();
					$oCatalogue->open($_SESSION['dims']['moduleid']);//$catalogue_moduleid);
					$oCatalogue->loadParams();
					$isTTC = $oCatalogue->getParams('cata_base_ttc');

					// On recalcule le total de la commande
					$a_articles = $cde->getlignes();

					if($isTTC){
						$cde->fields['total_ttc'] = $cde->fields['total_ttc']-$oldPort+$newPort;
						$cde->fields['total_ht'] = $cde->fields['total_ht']-($oldPort/(1+($cde->fields['port_tx_tva']/100)))+($newPort/(1+($cde->fields['port_tx_tva']/100)));
						$cde->fields['total_tva'] = $cde->fields['total_ttc']-$cde->fields['total_ht'];
					}else{
						$total_ht = 0;
						$a_total_tva = array();

						foreach ($a_articles as $ligne) {
							$total_ligne = $ligne->fields['pu_remise'] * $ligne->fields['qte'];
							$total_ht += $total_ligne;

							$tx_tva = round(floatval($ligne->fields['tx_tva']), 2);

							if (!isset($a_total_tva[$tx_tva])) {
								$a_total_tva[$tx_tva] = 0;
							}
							$a_total_tva[$tx_tva] += $total_ligne * $tx_tva / 100;
						}

						$default_tva = round(floatval($oCatalogue->getParams('default_tva')), 2);
						if (!isset($a_total_tva['PORT'])) {
							$a_total_tva['PORT'] = 0;
						}
						$a_total_tva['PORT'] += round($cde->fields['port'] * $default_tva / 100, 2);

						$total_tva = 0;
						foreach ($a_total_tva as $tva) {
							$total_tva += round($tva, 2);
						}

						$cde->fields['total_ht'] = $total_ht;
						$cde->fields['total_tva'] = $total_tva;
						$cde->fields['total_ttc'] = $cde->fields['total_ht'] + $cde->fields['port'] + $cde->fields['total_tva'];
					}
				}
			}

			$cde->save();

			$view->flash(dims_constant::getVal('THE_MODIFICATIONS_HAVE_BEEN_DONE_SUCCESSFULLY'), 'success');

			dims_redirect(get_path('commandes', 'show', array('id' => $cde->get('id_cde'))));
		}

		dims_redirect(get_path('commandes', 'index'));
		break;
}
$view->assign('last_commandes',get_lastcommandes());
$view->render('commandes/lateral.tpl.php', 'lateral');
?>
