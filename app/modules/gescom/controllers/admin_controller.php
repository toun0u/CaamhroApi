<?php
$current = null;
if(!empty($_SESSION['dims_tabs'])) {
	if(count($_SESSION['dims_tabs']->get('tabs')) != 0) {
		$manager = $_SESSION['dims_tabs'];
		$current = $manager->findOneByState(1);
		$current->set('link', Gescom\get_path(array('c'=>$c,'a'=>$a)));
	}
}

switch($a) {
	default :
	case 'form' :
		if(!is_null($current))
			$current->set('label', $_SESSION['cste']['_DIMS_LABEL_ADMIN']);

		// on charge tous les forms du module form
		$forms = forms::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'])," ORDER BY label");
		$fs = array(
			0 => '-- Non défini --',
		);
		foreach($forms as $f){
			$fs[$f->get('id')] = $f->get('label');
		}
		$view->assign('forms',$fs);

		// on récupère la liste des types d'objet dispo
		global $listTypeForms;
		$view->assign('listTypeForms',$listTypeForms);

		// on récupère les liens entre objet & form
		$links = gescom_form::find_by(array('id_module'=>$_SESSION['dims']['moduleid'],'type'=>array_keys($listTypeForms)));
		$lk = array();
		foreach($links as $l){
			$lk[$l->get('type')] = $l->get('id_form');
		}
		$view->assign('links',$lk);

		$view->render('administration/_form.tpl.php');
		break;
	case 'save_form':
		$forms_post = dims_load_securvalue('forms',dims_const::_DIMS_NUM_INPUT,true,true);

		$forms = forms::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'])," ORDER BY label");
		$links = gescom_form::find_by(array('id_module'=>$_SESSION['dims']['moduleid'],'type'=>array_keys($listTypeForms)));
		$lk = array();
		foreach($links as $l){
			$lk[$l->get('type')] = $l;
		}
		global $listTypeForms;

		foreach($forms_post as $k => $v){
			if(isset($listTypeForms[$k]) && (isset($forms[$v]) || $v === 0)){
				if(isset($lk[$k])){
					$link = $lk[$k];
					unset($lk[$k]);
				}else{
					$link = new gescom_form();
					$link->init_description();
					$link->setugm();
				}
				$link->set('id_form',$v);
				$link->set('type',$k);
				$link->save();
			}
		}

		foreach($lk as $l){
			$l->delete();
		}

		dims_redirect(Gescom\get_path(array('c'=>'admin','a'=>'form')));
		break;
	case 'workflow':
		if(!is_null($current))
			$current->set('label', $_SESSION['cste']['_DIMS_LABEL_ADMIN']);

		$workflows = gescom_workflow::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid']), " ORDER BY label ");
		$view->assign('workflows',$workflows);

		$view->render('administration/_workflow.tpl.php');
		break;
	case 'add_workflow':
		if(!is_null($current))
			$current->set('label', $_SESSION['cste']['_DIMS_LABEL_ADMIN']);
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		$workflow = gescom_workflow::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
		if(empty($workflow)){
			$workflow = new gescom_workflow();
			$workflow->init_description();
		}

		$view->assign('workflow',$workflow);

		$view->render('administration/_workflow_edit.tpl.php');
		break;
	case 'save_workflow':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		$workflow = gescom_workflow::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
		if(empty($workflow)){
			$workflow = new gescom_workflow();
			$workflow->init_description();
			$workflow->setugm();
		}
		$workflow->setvalues($_POST,'w_');
		$workflow->save();
		dims_redirect(Gescom\get_path(array('c'=>'admin','a'=>'workflow')));
		break;
	case 'show_workflow':
		if(!is_null($current))
			$current->set('label', $_SESSION['cste']['_DIMS_LABEL_ADMIN']);

		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		$workflow = gescom_workflow::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
		if(!empty($workflow)){
			$view->assign('workflow',$workflow);
			$view->assign('steps',gescom_workflow_step::find_by(array('id_workflow'=>$workflow->get('id'),'id_workspace'=>$_SESSION['dims']['workspaceid'])," ORDER BY position "));

			$view->render('administration/_workflow_show.tpl.php');
		}else{
			$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
			dims_redirect(Gescom\get_path(array('c'=>'admin','a'=>'workflow')));
		}
		break;
	case 'add_workflow_step':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		$workflow = gescom_workflow::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
		if(!empty($workflow)){
			$ids = dims_load_securvalue('ids',dims_const::_DIMS_NUM_INPUT,true,true);
			$step = gescom_workflow_step::find_by(array('id'=>$ids,'id_workflow'=>$workflow->get('id'),'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);

			$nbSteps = count(gescom_workflow_step::find_by(array('id_workflow'=>$workflow->get('id'),'id_workspace'=>$_SESSION['dims']['workspaceid'])," ORDER BY position "));

			if(empty($step)){
				$step = new gescom_workflow_step();
				$step->init_description();
				$step->set('id_workflow',$workflow->get('id'));
				$nbSteps ++;
				$step->set('position',$nbSteps);
			}

			$flash = array();
			$v = new view($flash);
			$v->set_tpl_webpath('modules/gescom/views/');
			$v->setLayout('layouts/empty.tpl.php');
			$v->assign('id_popup',dims_load_securvalue('id_popup',dims_const::_DIMS_CHAR_INPUT,true,true));
			$v->assign('workflow',$workflow);
			$v->assign('step',$step);
			$v->assign('nbSteps',$nbSteps);
			$v->render('administration/_workflow_step_edit.tpl.php');
			die($v->compile());
		}else{
			$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
			dims_redirect(Gescom\get_path(array('c'=>'admin','a'=>'workflow')));
		}
		break;
	case 'save_workflow_step':
		$ws_id_workflow = dims_load_securvalue('ws_id_workflow',dims_const::_DIMS_NUM_INPUT,true,true);
		$workflow = gescom_workflow::find_by(array('id'=>$ws_id_workflow,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
		if(!empty($workflow)){
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
			$step = gescom_workflow_step::find_by(array('id'=>$id,'id_workflow'=>$workflow->get('id'),'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
			if(empty($step)){
				$step = new gescom_workflow_step();
				$step->init_description();
				$step->setugm();
				$step->set('state',gescom_workflow_step::_STATE_ENABLED);
			}
			$step->setvalues($_POST,'ws_');
			$step->save();

			$view->flash("La modification s'est effectuée avec succès", 'bg-success');
			dims_redirect(Gescom\get_path(array('c'=>'admin','a'=>'show_workflow','id'=>$workflow->get('id'))));
		}else{
			$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
			dims_redirect(Gescom\get_path(array('c'=>'admin','a'=>'workflow')));
		}
		break;
	case 'switch_workflow_step':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		$workflow = gescom_workflow::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
		if(!empty($workflow)){
			$ids = dims_load_securvalue('ids',dims_const::_DIMS_NUM_INPUT,true,true);
			$step = gescom_workflow_step::find_by(array('id'=>$ids,'id_workflow'=>$workflow->get('id'),'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
			if(!empty($step)){
				switch ($step->get('state')) {
					case gescom_workflow_step::_STATE_DISABLED:
						$step->set('state',gescom_workflow_step::_STATE_ENABLED);
						break;
					case gescom_workflow_step::_STATE_ENABLED:
					default:
						$step->set('state',gescom_workflow_step::_STATE_DISABLED);
						break;
				}
				$step->save();
				$view->flash("La modification s'est effectuée avec succès", 'bg-success');
				dims_redirect(Gescom\get_path(array('c'=>'admin','a'=>'show_workflow','id'=>$workflow->get('id'))));
			}else{
				$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
				dims_redirect(Gescom\get_path(array('c'=>'admin','a'=>'show_workflow', 'id'=>$workflow->get('id'))));
			}
		}else{
			$view->flash("Une erreur s'est produite, veuillez recommencer", 'bg-danger');
			dims_redirect(Gescom\get_path(array('c'=>'admin','a'=>'workflow')));
		}
		break;
}
