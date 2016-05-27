<?php
if($a != 'load'){
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
	case 'load':
		$start = dims_load_securvalue('start', dims_const::_DIMS_NUM_INPUT, true, true);
		$nb = dims_load_securvalue('nb', dims_const::_DIMS_NUM_INPUT, true, true);
		$db = dims::getInstance()->getDb();
		// TODO: le lien sur un client peux être ?
		$sel = "SELECT 		*
				FROM 		".web_ask::TABLE_NAME." a
				INNER JOIN 	".user::TABLE_NAME." u
				ON 			u.id = a.id_account
				WHERE 		a.id_workspace = :idw
				AND 		a.id_case = 0
				AND 		a.state = :state
				ORDER BY 	a.timestp_create DESC
				LIMIT 		:start, :end";
		$params = array(
			':idw' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION["dims"]['workspaceid']),
			':start' => array('type' => PDO::PARAM_INT, 'value' => $start),
			':end' => array('type' => PDO::PARAM_INT, 'value' => $nb),
			':state' => array('type' => PDO::PARAM_INT, 'value' => web_ask::_STATE_WAITING),
		);
		$res = $db->query($sel,$params);
		$separation = $db->split_resultset($res);
		$return = array();
		foreach($separation as $sep){
			$dd = dims_timestp2local($sep['a']['timestp_create']);
			$return[] = array(
				'id' => $sep['a']['id'],
				'label' => $sep['a']['id'],
				'date' => $dd['date']." - ".$dd['time'],
				'authorPicture' => "icon-user",
				'user' => $sep['u']['firstname']." ".$sep['u']['lastname'],
				'emailLink' => $sep['u']['email'],
				'phone' => $sep['u']['phone'],
				'web-ask-lk' => Gescom\get_path(array('c'=>'web_ask','a'=>'show','id'=>$sep['a']['id'])),
			);
		}
		ob_clean();
		die(json_encode($return));
		break;
	case 'show':
		include_once DIMS_APP_PATH . 'modules/catalogue/include/class_client.php';
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true);
		$web_ask = web_ask::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid'],'state'=>web_ask::_STATE_WAITING),null,1);
		if(!empty($web_ask)){
			if(!is_null($current))
				$current->set('label', 'Demandes web #'.$web_ask->get('id'));

			$user = user::find_by(array('id'=>$web_ask->get('id_account')),null,1);

			$cataclient = client::findbyuser($user);

			$view->assign('web_ask',$web_ask);
			$view->assign('webaskuser', $user);
			$view->assign('cataclient', $cataclient);
			$view->assign('id_go',$web_ask->get('id_globalobject'));
			$view->assign('nbTodos',todo::countNbTasks($_SESSION['dims']['userid'],null,null,$web_ask->get('id_globalobject')));
			$view->assign('nbElem',_DASHBOARD_NB_ELEMS_DISPLAY);
			$view->render('demandes_web/show.tpl.php');
		}else{
			$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
			dims_redirect(Gescom\get_path(array('c'=>'dashboard','a'=>"index")));
		}
		break;
	case 'valide':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true);
		$web_ask = web_ask::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid'],'state'=>web_ask::_STATE_WAITING),null,1);
		if(!empty($web_ask)){
			$dossier = new dims_case();
			$dossier->init_description();
			$dossier->setugm();
			$dossier->setvalues($_POST,'case_');
			$dossier->set('datestart',date('YmdHis'));

			$step = gescom_workflow_step::find_by(array('id_workflow'=>$dossier->get('id_workflow'),'state'=>gescom_workflow_step::_STATE_ENABLED)," ORDER BY position ",1);
			$dossier->set('status',$step->get('id'));

			$dossier->save();

			$web_ask->set('id_case',$dossier->get('id'));
			$web_ask->set('state',web_ask::_STATE_VALIDATED);
			$web_ask->save();

			$contact = contact::find_by(array('account_id'=>$web_ask->get('id_account')),null,1);
			if(!empty($contact)){
				if (!matrix::exists(array( 'id_contact' => $contact->get('id_globalobject'), 'id_case' => $dossier->get('id_globalobject')))) {
					$matrix = new matrix();
					$matrix->addLink(array( 'id_contact' => $contact->get('id_globalobject'), 'id_case' => $dossier->get('id_globalobject')));
				}
			}

			$view->flash("La demande web a bien été validée", 'bg-success');
			dims_redirect(Gescom\get_path(array('c'=>'dossier','a'=>"show", 'id'=>$dossier->get('id'))));
		}else{
			$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
			dims_redirect(Gescom\get_path(array('c'=>'dashboard','a'=>"index")));
		}
		break;
	case 'delete':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true);
		$web_ask = web_ask::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid'],'state'=>web_ask::_STATE_WAITING),null,1);
		if(!empty($web_ask)){
			$web_ask->set('state', web_ask::_STATE_DELETED);
			$web_ask->save();
			$view->flash("La demande web a bien été marquée comme indésirable", 'bg-success');
		}else{
			$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
		}
		dims_redirect(Gescom\get_path(array('c'=>'dashboard','a'=>"index")));
		break;
}
