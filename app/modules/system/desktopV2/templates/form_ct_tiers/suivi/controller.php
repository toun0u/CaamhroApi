<?php
switch($action){
	case 'new':
		ob_clean();
		$id_popup = dims_load_securvalue('id_popup', dims_const::_DIMS_NUM_INPUT, true, false);
		$id_tiers = dims_load_securvalue('id_tiers', dims_const::_DIMS_NUM_INPUT, true, false);
		$id_contact = dims_load_securvalue('id_contact', dims_const::_DIMS_NUM_INPUT, true, false);
		if(!empty($id_popup) && (!empty($id_tiers)||!empty($id_contact))){
			$suivi = new suivi();
			$suivi->init_description();
			$suivi->setLightAttribute('id_tiers',$id_tiers);
			$suivi->setLightAttribute('id_contact',$id_contact);
			$suivi->setLightAttribute('id_popup', $id_popup);
			$suivi->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/suivi/new_suivi.tpl.php');
		}
		die();
		break;
	case 'show':
		ob_clean();
		$id_suivi	= dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, false);
		$id_popup	= dims_load_securvalue('id_popup', dims_const::_DIMS_NUM_INPUT, true, false);
		$suivi = suivi::find_by(array('id_suivi'=>$id_suivi),null,1);
		if (!empty($suivi) && !empty($id_popup)) {
			$suivi->setLightAttribute('id_popup', $id_popup);
			$suivi->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/suivi/edit_suivi.tpl.php');
		}
		die();
		break;
	case 'save':
	case 'create':
		require_once DIMS_APP_PATH.'modules/system/class_matrix.php';
		require_once DIMS_APP_PATH.'modules/system/class_search.php';

		$id_suivi = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true);
		$suivi = suivi::find_by(array('id_suivi'=>$id_suivi),null,1);
		if (empty($suivi)) {
			$suivi = new suivi();
			$suivi->init_description();
			$suivi->setugm();
			$suivi->fields['id'] = null;
		}
		$dossier_actuel = $suivi->fields['dossier_id'];
		$suivi->fields['valide'] = 0;
		$suivi->fields['datevalide'] = '';
		$suivi->setvalues($_POST,'suivi_');
		if($suivi->fields['valide']){
			$suivi->fields['datevalide'] = date('Y-m-d');
		}
		$suivi->fields['type'] = $suivi->getType();
		$suivi->fields['datejour'] = business_datefr2us($suivi->fields['datejour']);
		$suivi->save();

		if($dossier_actuel > 0 && $dossier_actuel != $suivi->fields['dossier_id']){
			// on gere le changement de dossier
			$case = dims_case::find_by(array('id'=>$dossier_actuel),null,1);
			if(!empty($case)){
				if ($suivi->fields['tiers_id'] > 0) {
					$tiers = tiers::find_by(array('id'=>$suivi->fields['tiers_id']),null,1);
					if(!empty($tiers)){
						if (matrix::exists(array( 'id_tiers' => $contact_go, 'id_case' => $case->fields['id_globalobject'], 'id_suivi' => $suivi->fields['id_globalobject']))) {
							$matrix = new matrix();
							$matrix->cutLink(array( 'id_tiers' => $contact_go, 'id_case' => $case->fields['id_globalobject'], 'id_suivi' => $suivi->fields['id_globalobject']));
						}
					}
				}elseif ($suivi->fields['contact_id'] > 0) {
					$contact = contact::find_by(array('id'=>$suivi->fields['contact_id']),null,1);
					if(!empty($contact)){
						if (matrix::exists(array( 'id_contact' => $contact_go, 'id_case' => $case->fields['id_globalobject'], 'id_suivi' => $suivi->fields['id_globalobject']))) {
							$matrix = new matrix();
							$matrix->cutLink(array( 'id_contact' => $contact_go, 'id_case' => $case->fields['id_globalobject'], 'id_suivi' => $suivi->fields['id_globalobject']));
						}
					}
				}
			}
		}

		$goCase = 0;
		$case = dims_case::find_by(array('id'=>$suivi->fields['dossier_id']),null,1);
		if(!empty($case)){
			$goCase = $case->fields['id_globalobject'];
		}

		// si on fait le rattachement à un tiers
		if ($suivi->fields['tiers_id'] > 0) {
			$tiers = tiers::find_by(array('id'=>$suivi->fields['tiers_id']),null,1);
			if(!empty($tiers)){
				// rattachement du suivi au dossier et au client dans la matrice
				$matrix = new matrix();
				$matrix->addLink(array('id_tiers' => $tiers->fields['id_globalobject'], 'id_case' => $goCase, 'id_suivi' => $suivi->fields['id_globalobject'] ));
			}
		}elseif ($suivi->fields['contact_id'] > 0) { // si on fait le rattachement à un contact
			$contact = contact::find_by(array('id'=>$suivi->fields['contact_id']),null,1);
			if(!empty($contact)){
				// rattachement du suivi au dossier et au client dans la matrice
				$matrix = new matrix();
				$matrix->addLink(array('id_contact' => $contact->fields['id_globalobject'], 'id_case' => $goCase, 'id_suivi' => $suivi->fields['id_globalobject'] ));
			}
		}

		$_SESSION['dims']['suivi']['reopen'] = $suivi->fields['id_suivi'];
		if ($suivi->fields['tiers_id'] > 0) {
			dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$suivi->fields['tiers_id']);
		}elseif ($suivi->fields['contact_id'] > 0) {
			dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$suivi->fields['contact_id']);
		}
		break;
	case 'delete':
		$id_suivi	= dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, false);
		$suivi = suivi::find_by(array('id_suivi'=>$id_suivi),null,1);
		$url = dims::getInstance()->getScriptEnv();
		if (!empty($suivi)) {
			if ($suivi->fields['tiers_id'] > 0) {
				$url .= "?submenu=1&mode=company&action=show&id=".$suivi->fields['tiers_id'];
			}elseif ($suivi->fields['contact_id'] > 0) {
				$url .= "?submenu=1&mode=contact&action=show&id=".$suivi->fields['contact_id'];
			}
			$suivi->delete();
		}
		dims_redirect($url);
		break;
	case 'add_versement':
		$id_suivi	= dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true);
		$montant	= dims_load_securvalue('montant', dims_const::_DIMS_NUM_INPUT, true, true);
		$suivi = suivi::find_by(array('id_suivi'=>$id_suivi),null,1);

		if (!empty($suivi)) {
			if($montant > 0){
				require_once DIMS_APP_PATH.'modules/system/suivi/class_versement.php';
				$versement = new versement();
				$versement->fields['date_paiement'] = dims_createtimestamp();
				$versement->fields['montant'] = $montant;
				$versement->fields['suivi_id'] = $suivi->getId();
				$versement->fields['suivi_type'] = $suivi->getType();
				$versement->fields['id_type'] = $suivi->get('id_type');
				$versement->fields['suivi_exercice'] = $suivi->getExercice();
				$versement->save();
				$suivi->save();
			}
			$_SESSION['dims']['suivi']['reopen'] = $suivi->fields['id_suivi'];
			if ($suivi->fields['tiers_id'] > 0) {
				dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$suivi->fields['tiers_id']);
			}elseif ($suivi->fields['contact_id'] > 0) {
				dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$suivi->fields['contact_id']);
			}
		}
		dims_redirect(dims::getInstance()->getScriptEnv());
		break;
	case 'del_versement':
		$id_suivi		= dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true);
		$versement_id	= dims_load_securvalue('versement_id', dims_const::_DIMS_NUM_INPUT, true, true);
		require_once DIMS_APP_PATH.'modules/system/suivi/class_versement.php';
		$suivi = suivi::find_by(array('id_suivi'=>$id_suivi),null,1);
		$versement = versement::find_by(array('id'=>$versement_id),null,1);

		if (!empty($suivi) && !empty($versement)) {
			$versement->delete();
			$suivi->save();

			$_SESSION['dims']['suivi']['reopen'] = $suivi->fields['id_suivi'];
			if ($suivi->fields['tiers_id'] > 0) {
				dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$suivi->fields['tiers_id']);
			}elseif ($suivi->fields['contact_id'] > 0) {
				dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$suivi->fields['contact_id']);
			}
		}
		dims_redirect(dims::getInstance()->getScriptEnv());
		break;
	case 'valider_devis':
		$id_suivi = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true);
		$suivi = suivi::find_by(array('id_suivi'=>$id_suivi),null,1);
		if (!empty($suivi) && $suivi->fields['type'] == suivi::TYPE_DEVIS) {
			$suivi->fields['valide'] = 1;
			$suivi->fields['datevalide'] = date('Y-m-d');
			$suivi->save();

			$_SESSION['dims']['suivi']['reopen'] = $suivi->fields['id_suivi'];
			if ($suivi->fields['tiers_id'] > 0) {
				dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$suivi->fields['tiers_id']);
			}elseif ($suivi->fields['contact_id'] > 0) {
				dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$suivi->fields['contact_id']);
			}
		}
		dims_redirect(dims::getInstance()->getScriptEnv());
		break;
	case 'solder':
		$id_suivi = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true);
		$suivi = suivi::find_by(array('id_suivi'=>$id_suivi),null,1);

		if (!empty($suivi)) {
			require_once DIMS_APP_PATH.'modules/system/suivi/class_versement.php';
			$versement = new versement();
			$versement->fields['date_paiement'] = dims_createtimestamp();
			$versement->fields['montant'] = $suivi->getSoldeTTC();
			$versement->fields['suivi_id'] = $suivi->getId();
			$versement->fields['suivi_type'] = $suivi->getType();
			$versement->fields['id_type'] = $suivi->get('id_type');
			$versement->fields['suivi_exercice'] = $suivi->getExercice();
			$versement->save();
			$suivi->save();
			$_SESSION['dims']['suivi']['reopen'] = $suivi->fields['id_suivi'];
			if ($suivi->fields['tiers_id'] > 0) {
				dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$suivi->fields['tiers_id']);
			}elseif ($suivi->fields['contact_id'] > 0) {
				dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$suivi->fields['contact_id']);
			}
		}
		dims_redirect(dims::getInstance()->getScriptEnv());
		break;
	case 'imprimer':
		ob_clean();
		$id_suivi			= dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true);
		$suivi = suivi::find_by(array('id_suivi'=>$id_suivi),null,1);
		if (!empty($suivi)) {
			$suivi_modele	= dims_load_securvalue('suivi_modele', dims_const::_DIMS_NUM_INPUT, true, true);
			$format			= dims_load_securvalue('format', dims_const::_DIMS_CHAR_INPUT, true, true);
			$suivi->generePdf($suivi_modele,$format);
		}
		die();
		break;
	case 'facture':
		$id_suivi = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true);
		$suivi = suivi::find_by(array('id_suivi'=>$id_suivi),null,1);
		if (!empty($suivi)) {
			$clone_suivi = $suivi->dupliquer('Facture');

			$_SESSION['dims']['suivi']['reopen'] = $clone_suivi->fields['id_suivi'];
			if ($suivi->fields['tiers_id'] > 0) {
				dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$suivi->fields['tiers_id']);
			}elseif ($suivi->fields['contact_id'] > 0) {
				dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$suivi->fields['contact_id']);
			}
		}
		dims_redirect(dims::getInstance()->getScriptEnv());
		break;
	case 'duplicate':
		$id_suivi = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true);
		$suivi = suivi::find_by(array('id_suivi'=>$id_suivi),null,1);
		if (!empty($suivi)) {
			$clone_suivi = $suivi->dupliquer();

			$_SESSION['dims']['suivi']['reopen'] = $clone_suivi->fields['id_suivi'];
			if ($suivi->fields['tiers_id'] > 0) {
				dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$suivi->fields['tiers_id']);
			}elseif ($suivi->fields['contact_id'] > 0) {
				dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$suivi->fields['contact_id']);
			}
		}
		dims_redirect(dims::getInstance()->getScriptEnv());
		break;
	case 'edit_detail':
		ob_clean();
		$id_suivi = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true);
		$suivi = suivi::find_by(array('id_suivi'=>$id_suivi,'type'),null,1);
		if (!empty($suivi)) {
			$suivi_detail_id = dims_load_securvalue('idd', dims_const::_DIMS_NUM_INPUT, true, true);
			$detail = suividetail::find_by(array('id'=>$suivi_detail_id,'suivi_id'=>$suivi->get('id')),null,1);
			if(empty($detail)){
				$detail = new suividetail();
				$detail->init_description();
				$detail->fields['suivi_id'] = $suivi->get('id');
				$detail->fields['suivi_type'] = $suivi->getType();
				$detail->fields['id_type'] = $suivi->get('id_type');
			}
			$detail->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/suivi/edit_suividetail.tpl.php');
		}
		die();
		break;
	case 'save_detail':
		$id_suivi = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true);
		$suivi_type = dims_load_securvalue('suivi_type', dims_const::_DIMS_CHAR_INPUT, true, true);
		$suivi = suivi::find_by(array('id'=>$id_suivi,'type'=>$suivi_type),null,1);
		if (!empty($suivi)) {
			$suivi_detail_id = dims_load_securvalue('idd', dims_const::_DIMS_NUM_INPUT, true, true);
			$detail = suividetail::find_by(array('id'=>$suivi_detail_id,'suivi_id'=>$suivi->get('id')),null,1);
			if(empty($detail)){
				$detail = new suividetail();
				$detail->init_description();
				$detail->setugm();
				$detail->fields['suivi_id'] = $suivi->fields['id'];
			}
			$detail->fields['suivi_type'] = $suivi->getType();
			$detail->fields['id_type'] = $suivi->get('id_type');
			$detail->fields['suivi_exercice'] = $suivi->getExercice();
			$detail->setvalues($_POST, 'suivi_detail_');
			$detail->save();
			$suivi->save();

			$_SESSION['dims']['suivi']['reopen'] = $suivi->fields['id_suivi'];
			if ($suivi->fields['tiers_id'] > 0) {
				dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$suivi->fields['tiers_id']);
			}elseif ($suivi->fields['contact_id'] > 0) {
				dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$suivi->fields['contact_id']);
			}
		}
		dims_redirect(dims::getInstance()->getScriptEnv());
		break;
	case 'delete_detail':
		$id_suivi = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true);
		$suivi = suivi::find_by(array('id_suivi'=>$id_suivi),null,1);
		if (!empty($suivi)) {
			$suivi_detail_id = dims_load_securvalue('idd', dims_const::_DIMS_NUM_INPUT, true, true);
			$detail = suividetail::find_by(array('id'=>$suivi_detail_id,'suivi_id'=>$suivi->get('id')),null,1);
			if(!empty($detail)){
				$detail->delete();
				$suivi->save();
			}

			$_SESSION['dims']['suivi']['reopen'] = $suivi->fields['id_suivi'];
			if ($suivi->fields['tiers_id'] > 0) {
				dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$suivi->fields['tiers_id']);
			}elseif ($suivi->fields['contact_id'] > 0) {
				dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$suivi->fields['contact_id']);
			}
		}
		dims_redirect(dims::getInstance()->getScriptEnv());
		break;
	case 'down_detail':
		$id_suivi = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true);
		$suivi = suivi::find_by(array('id_suivi'=>$id_suivi),null,1);
		if (!empty($suivi)) {
			$suivi_detail_id = dims_load_securvalue('idd', dims_const::_DIMS_NUM_INPUT, true, true);
			$detail = suividetail::find_by(array('id'=>$suivi_detail_id,'suivi_id'=>$suivi->get('id')),null,1);
			if(!empty($detail)){
				$detail->movedown();
			}

			$_SESSION['dims']['suivi']['reopen'] = $suivi->fields['id_suivi'];
			if ($suivi->fields['tiers_id'] > 0) {
				dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$suivi->fields['tiers_id']);
			}elseif ($suivi->fields['contact_id'] > 0) {
				dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$suivi->fields['contact_id']);
			}
		}
		dims_redirect(dims::getInstance()->getScriptEnv());
		break;

	case 'up_detail':
		$id_suivi = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true);
		$suivi = suivi::find_by(array('id_suivi'=>$id_suivi),null,1);
		if (!empty($suivi)) {
			$suivi_detail_id = dims_load_securvalue('idd', dims_const::_DIMS_NUM_INPUT, true, true);
			$detail = suividetail::find_by(array('id'=>$suivi_detail_id,'suivi_id'=>$suivi->get('id')),null,1);
			if(!empty($detail)){
				$detail->moveup();
			}

			$_SESSION['dims']['suivi']['reopen'] = $suivi->fields['id_suivi'];
			if ($suivi->fields['tiers_id'] > 0) {
				dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=company&action=show&id=".$suivi->fields['tiers_id']);
			}elseif ($suivi->fields['contact_id'] > 0) {
				dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=1&mode=contact&action=show&id=".$suivi->fields['contact_id']);
			}
		}
		dims_redirect(dims::getInstance()->getScriptEnv());
		break;
}
