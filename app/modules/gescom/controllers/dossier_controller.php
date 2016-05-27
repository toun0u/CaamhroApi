<?php
if ($a != 'popup_create' && $a != 'search_ct') {
	$current = null;
	if(!empty($_SESSION['dims_tabs'])) {
		if(count($_SESSION['dims_tabs']->get('tabs')) != 0) {
			$manager = $_SESSION['dims_tabs'];
			$current = $manager->findOneByState(1);
			$current->set('link', Gescom\get_path(array('c'=>$c,'a'=>$a)));
		}
	}
}

switch($a) {
	default :
	case 'index' :
		if(!is_null($current))
			$current->set('label', "Dossiers");

		$db = dims::getInstance()->getDb();

		$label = dims_load_securvalue('l',dims_const::_DIMS_CHAR_INPUT,true,true,true);
		$type = dims_load_securvalue('t',dims_const::_DIMS_NUM_INPUT,true,true,true,$type,-1,true);
		$id_workflow = dims_load_securvalue('w',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$id_state = dims_load_securvalue('s',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$id_client = dims_load_securvalue('i',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$id_responsable = dims_load_securvalue('r',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$p = dims_load_securvalue('p',dims_const::_DIMS_NUM_INPUT,true,true,true);
		if(empty($p)){
			$p = 0;
		}

		$dossier = new dims_case();
		$dossier->setPaginationParams(30);
		$dossier->setPageLimited(true);
		$dossier->page_courant = $p;
		$dossier->nom_get = 'p';

		$dossiers = $dossier->search($label, $type, $id_workflow, $id_state, $id_client, $id_responsable);

		$view->assign('label',$label);
		$view->assign('type',$type);
		$view->assign('id_workflow',$id_workflow);
		$view->assign('id_state',$id_state);
		$view->assign('id_client',$id_client);
		$view->assign('id_responsable',$id_responsable);

		$lstWorkflows = array(""=>"Tous");
		$workflows = gescom_workflow::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid']), "ORDER BY label ");
		foreach($workflows as $w){
			$lstWorkflows[$w->get('id')] = $w->get('label');
		}
		$view->assign('workflows',$lstWorkflows);

		$lstSteps = array();
		if($id_workflow > 0 && !empty($workflows[$id_workflow])){
			$lstSteps[""] = "Tous";
			$steps = gescom_workflow_step::find_by(array('id_workflow'=>$id_workflow,'state'=>gescom_workflow_step::_STATE_ENABLED)," ORDER BY position ");
			foreach($steps as $s){
				$lstSteps[$s->get('id')] = $s->get('label');
			}
		}else{
			$id_workflow = 0;
		}
		$view->assign('steps',$lstSteps);

		$sel = "SELECT 		DISTINCT u.*
				FROM 		".user::TABLE_NAME." u
				INNER JOIN 	".workspace_user::TABLE_NAME." wu
				ON 			wu.id_user = u.id
				WHERE 		wu.id_workspace = :idw
				ORDER BY 	u.firstname, u.lastname";
		$params = array(
			':idw' => $_SESSION['dims']['workspaceid'],
		);
		$res = $db->query($sel,$params);
		$lstManagers = array(""=>"Tous");
		while($r = $db->fetchrow($res)){
			$lstManagers[$r['id']] = $r['firstname']." ".$r['lastname'];
		}
		$view->assign('managers',$lstManagers);

		$view->assign('dossiers',$dossiers);
		$view->assign('dossier',$dossier);

		$view->render('dossier/_list.tpl.php');
		break;
	case 'edit':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true);
		$dossier = dims_case::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
		if (!empty($dossier)) {
			if (!is_null($current)) {
				$current->set('label', "Dossier ".$dossier->get('label'));
			}

			$view->assign('case',$dossier);

			$form = new Dims\form(array(
				'name'          => "edit_case",
				'object'        => $dossier,
				'back_url'      => Gescom\get_path(array('c' => 'dossier', 'a' => 'show', 'id' => $dossier->get('id'))),
				'action'        => Gescom\get_path(array('c' => 'dossier', 'a' => 'save')),
				'submit_value'  => $_SESSION['cste']['_DIMS_SAVE'],
				'back_name'     => $_SESSION['cste']['_DIMS_LABEL_CANCEL'],
			));

			$view->assign('managers', Gescom\getManagers());

			$view->assign('workflows', gescom_workflow::selectorformat(
				gescom_workflow::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid']), "ORDER BY label "
			)));

			$view->assign('form', $form);

			$view->render('dossier/edit.tpl.php');
		} else {
			$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
			dims_redirect(Gescom\get_path(array('c'=>'dossier','a'=>"index")));
		}
		break;
	case 'delete':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true);
		$dossier = dims_case::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);

		if (!empty($dossier)) {
			if($dossier->delete()) {
				$view->flash('Le dossier a bien été supprimé', 'bg-success');
			} else {
				$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
			}
		}else{
			$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
		}

		dims_redirect(Gescom\get_path(array('c'=>'dossier','a'=>"index")));
		break;
	case 'show':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true);
		$dossier = dims_case::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
		if(!empty($dossier)){
			if (!is_null($current)) {
				$current->set('label', "Dossier ".$dossier->get('label'));
			}
			$view->assign('dossier',$dossier);

			$web_ask = web_ask::find_by(array('id_case' =>$dossier->get('id'), 'state'=>web_ask::_STATE_VALIDATED),null,1);
			$view->assign('web_ask',$web_ask);
			$workflow = gescom_workflow::find_by(array('id'=>$dossier->get('id_workflow')),null,1);
			$view->assign('workflow',$workflow);
			$steps = gescom_workflow_step::find_by(array('id_workflow'=>$dossier->get('id_workflow'),'state'=>gescom_workflow_step::_STATE_ENABLED)," ORDER BY position ");
			$view->assign('steps',$steps);

			$db = dims::getInstance()->getDb();
			$sel = "SELECT 		c.*
					FROM 		".contact::TABLE_NAME." c
					INNER JOIN 	".matrix::TABLE_NAME." m
					ON 			m.id_contact = c.id_globalobject
					WHERE 		m.id_case = :idcase
					AND 		m.id_contact > 0
					LIMIT 		1";
			$params = array(
				':idcase' => $dossier->get('id_globalobject'),
			);
			$res = $db->query($sel,$params);
			$ct = new contact();
			if($r = $db->fetchrow($res)){
				$ct->openFromResultSet($r);
			}else{
				$ct->init_description();
			}
			$view->assign('ct',$ct);

			$user = $ct->getUser();

			include_once DIMS_APP_PATH . 'modules/catalogue/include/class_client.php';
			$client = client::findbyuser($user);
			$view->assign('client', $client);

			include_once DIMS_APP_PATH . 'modules/catalogue/include/class_facture.php';
			$quotations = cata_facture::find_by(array('id_case' => $dossier->getId(), 'type' => cata_facture::TYPE_QUOTATION));
			$view->assign('quotations', $quotations);

			$view->render("dossier/show.tpl.php");
		}else{
			$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
			dims_redirect(Gescom\get_path(array('c'=>'dossier','a'=>"index")));
		}
		break;
	case 'save':
		$idcase = dims_load_securvalue('idcase', dims_const::_DIMS_NUM_INPUT, true, true, true);

		$case = new dims_case();

		if (!empty($idcase)) {
			$case->open($idcase);
		} else {
			$case->init_description();
			$case->setugm();
		}

		$case->setvalues($_POST, 'case_');
		$case->set('datestart', date('YmdHis'));

		$step = gescom_workflow_step::find_by(array('id_workflow' => $case->get('id_workflow'), 'state' => gescom_workflow_step::_STATE_ENABLED), " ORDER BY position ", 1);
		$case->set('status', $step->get('id'));

		$case->save();

		dims_redirect(Gescom\get_path(array('c' => 'dossier', 'a' => 'show', 'id' => $case->get('id'))));
		break;
	case 'popup_create':
		$idw = dims_load_securvalue('idw', dims_const::_DIMS_NUM_INPUT, true, true); // id web_ask
		$id_popup = dims_load_securvalue('id_popup',dims_const::_DIMS_CHAR_INPUT,true,true);
		$dossier = new dims_case();
		$dossier->init_description();

		$flash = array();
		$v = new view($flash);
		$v->set_tpl_webpath('modules/gescom/views/');
		$v->setLayout('layouts/empty.tpl.php');

		$web_ask = web_ask::find_by(array('id'=>$idw,'id_workspace'=>$_SESSION['dims']['workspaceid'],'state'=>web_ask::_STATE_WAITING),null,1);
		$urlAction = "";
		$back_url = "javascript:void(0);\" onclick=\"javascript:dims_closeOverlayedPopup('$id_popup');";
		if(!empty($web_ask)){
			$v->assign('id_web_ask',$idw);
			$dossier->set('label',"DW".$idw);
			$dossier->set('long_label',"Dossier issu de la demande web #".$idw);

			$urlAction = Gescom\get_path(array('c'=>'web_ask','a'=>'valide','id'=>$idw));
		}else{
			$urlAction = Gescom\get_path(array('c'=>'dossier','a'=>'save'));
		}
		$dossier->set('id_manager',$_SESSION['dims']['userid']);

		$v->assign('managers', Gescom\getManagers());

		$v->assign('workflows', gescom_workflow::selectorformat(
			gescom_workflow::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid']), "ORDER BY label "
		)));

		$v->assign('return',dims_load_securvalue('return',dims_const::_DIMS_CHAR_INPUT,true,true));
		$v->assign('id_popup',$id_popup);
		$v->assign('case',$dossier);

		$form = new Dims\form(array(
			'name' 			=> "create_case",
			'object'		=> $dossier,
			'action'		=> $urlAction,
			'submit_value'	=> $_SESSION['cste']['_DIMS_SAVE'],
			'back_name'		=> $_SESSION['cste']['_DIMS_LABEL_CANCEL'],
			'back_url'		=> $back_url,
		));
		$v->assign('form',$form);

		$v->render('dossier/_edit_popup.tpl.php');
		die($v->compile());
		break;
	case 'change_state':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true);
		$dossier = dims_case::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
		if(!empty($dossier)){
			$state = dims_load_securvalue('state', dims_const::_DIMS_NUM_INPUT, true, true);
			$step = gescom_workflow_step::find_by(array('id'=>$state,'id_workflow'=>$dossier->get('id_workflow'),'state'=>gescom_workflow_step::_STATE_ENABLED),null,1);
			if(!empty($step)){
				$dossier->set('status',$step->get('id'));

				switch ($step->get('type')) {
					case gescom_workflow_step::_TYPE_FINISHED:
					case gescom_workflow_step::_TYPE_CANCELLED:
						$dossier->set('dateend',date('YmdHis'));
						break;
					case gescom_workflow_step::_TYPE_WAITING:
					default:
						$dossier->set('dateend',0);
						break;
				}

				$dossier->save();
				$view->flash("La modification s'est effectuée avec succès", 'bg-success');
			}else{
				$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
			}
			dims_redirect(Gescom\get_path(array('c'=>'dossier','a'=>"show",'id'=>$dossier->get('id'))));
		}else{
			$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
			dims_redirect(Gescom\get_path(array('c'=>'dossier','a'=>"index")));
		}
		break;
	case 'search_ct':
		ob_clean();
		$s = dims_load_securvalue('s',dims_const::_DIMS_CHAR_INPUT,true,true);
		require_once(DIMS_APP_PATH . "/modules/system/class_search.php");
		$dimsearch = new search(dims::getInstance());
		$dimsearch->addSearchObject(dims_const::_DIMS_MODULE_SYSTEM, contact::MY_GLOBALOBJECT_CODE,$_SESSION['cste']['_DIMS_LABEL_CONTACT']);
		$dimsearch->initSearchObject();

		$kword					= dims_load_securvalue('kword', dims_const::_DIMS_CHAR_INPUT, true, true);
		$idmodule				= dims_load_securvalue('idmodule', dims_const::_DIMS_CHAR_INPUT, true, true);
		$idobj					= dims_load_securvalue('idobj', dims_const::_DIMS_CHAR_INPUT, true, true);
		$idmetafield			= dims_load_securvalue('idmetafield', dims_const::_DIMS_CHAR_INPUT, true, true);
		$sens					= dims_load_securvalue('sens', dims_const::_DIMS_CHAR_INPUT, true, true);

		$dimsearch->executeSearch2($s, $kword,$_SESSION['dims']['moduleid'], $idobj, $idmetafield, $sens,0,null,$_SESSION['dims']['workspaceid']);

		$ids = array();
		foreach($dimsearch->tabresultat as $idmodule => $tab_objects){
			foreach($tab_objects as $idobjet => $tab_ids){
				foreach($tab_ids as $kid => $id){
					$ids[$id['id_go']] = $id['id_go'];
				}
			}
		}
		//dims_print_r($ids);
		$result = array();
		if(count($ids)){
			$db = dims::getInstance()->getDb();
			$params = array();
			$sql = "SELECT 		c.*
					FROM 		".contact::TABLE_NAME." c
					INNER JOIN 	".matrix::TABLE_NAME." m
					ON 			m.id_contact = c.id_globalobject
					WHERE 		m.id_case > 0
					AND 		c.id_globalobject IN (".$db->getParamsFromArray($ids, 'go', $params).")
					GROUP BY    c.id
					ORDER BY 	c.firstname, c.lastname";
			$res = $db->query($sql,$params);

			while($r = $db->fetchrow($res)){
				$result[] = array(
					'id' => $r['id'],
					'text' => $r['firstname']." ".$r['lastname'],
				);
			}
		}
		die(json_encode($result));
		break;
	case 'json_cases':
		$text       = dims_load_securvalue('text',          dims_const::_DIMS_CHAR_INPUT,   true, true, true);
		$contactid  = dims_load_securvalue('contactid',     dims_const::_DIMS_NUM_INPUT,    true, true, true);
		$codeclient = dims_load_securvalue('codeclient',    dims_const::_DIMS_NUM_INPUT,    true, true, true);

		if(!empty($codeclient) && empty($contactid)) {
			include_once DIMS_APP_PATH . 'modules/catalogue/include/class_client.php';
			$client = new client();
			$client->openByCode($codeclient);

			$user = new user();
			$user->open($client->get('dims_user'));

			$contactid = $user->get('id_contact');
		}

		$case = new dims_case();
		$searchresult = $case->search($text, -1, 0, 0, $contactid);

		$caseslist = array();
		foreach($searchresult as $result) {
			$caseslist[] = array(
				'id'    => $result['dossier']['id'],
				'label' => $result['dossier']['label'],
			);
		}

		ob_clean();
		echo json_encode($caseslist);
		die();
		break;
}
