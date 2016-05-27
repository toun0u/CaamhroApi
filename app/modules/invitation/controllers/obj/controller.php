<?php
$view = view::getInstance();

switch ($a) {
	default:
	case 'view':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$invit = invitation::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid'],'type'=>dims_const::_PLANNING_ACTION_INVITATION,'id_parent'=>0),null,1);
		if(!empty($invit)){
			$view->render('headers/view_header.tpl.php', 'header');
			$view->assign('obj',$invit);
			$view->render('content/view.tpl.php');
		}else{
			dims_redirect(dims::getInstance()->getScriptEnv()."?c=obj&a=add");
		}
		break;
	case 'add':
		$view->render('headers/edit_header.tpl.php', 'header');
		$invit = new invitation();
		$invit->init_description();
		$view->assign('obj',$invit);
		$view->render('content/edit.tpl.php');
		break;
	case 'edit':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$invit = invitation::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid'],'type'=>dims_const::_PLANNING_ACTION_INVITATION,'id_parent'=>0),null,1);
		if(!empty($invit)){
			$view->render('headers/edit_header.tpl.php', 'header');
			$view->assign('obj',$invit);
			$view->render('content/edit.tpl.php');
		}else{
			dims_redirect(dims::getInstance()->getScriptEnv()."?c=obj&a=add");
		}
		break;
	case 'save':
		$go = dims_load_securvalue('id_globalobject',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$parent = new invitation();
		if($go != '' && $go > 0){
			$parent = invitation::find_by(array('id_globalobject'=>$go,'id_workspace'=>$_SESSION['dims']['workspaceid'],'type'=>dims_const::_PLANNING_ACTION_INVITATION,'id_parent'=>0),null,1);
			if(empty($parent)){
				$parent = new invitation();
				$parent->init_description();
				$parent->setugm();
			}
		}else{
			$parent->init_description();
			$parent->setugm();
		}
		$parent->setvalues($_POST,'obj_');
		$parent->save();

		/* Gestion des horaires déjà existants */
		$dates = dims_load_securvalue('dates',dims_const::_DIMS_CHAR_INPUT,true,true,true);
		$date1 = dims_load_securvalue('date1',dims_const::_DIMS_CHAR_INPUT,true,true,true);
		$heure1 = dims_load_securvalue('heure1',dims_const::_DIMS_CHAR_INPUT,true,true,true);
		$date2 = dims_load_securvalue('date2',dims_const::_DIMS_CHAR_INPUT,true,true,true);
		$heure2 = dims_load_securvalue('heure2',dims_const::_DIMS_CHAR_INPUT,true,true,true);

		$lstDates = $childs = $parent->getDatesLink();
		// Update des fils déjà existant
		foreach($lstDates as $k => $d){
			if(in_array($d->get('id'),$dates)){
				$k2 = array_search($d->get('id'), $dates);
				if(isset($date1[$k2]) && $date1[$k2] != '' && isset($heure1[$k2]) && isset($date2[$k2]) && isset($heure2[$k2])){
					$d->set('datejour',implode('-',array_reverse(explode('/',$date1[$k2]))));
					$d->set('heuredeb',$heure1[$k2].":00");
					$d->set('datefin',($date2[$k2]!='')?implode('-',array_reverse(explode('/',$date2[$k2]))):$d->get('datedebut'));
					$d->set('heurefin',$heure2[$k2].":00");

					if(empty($d->fields['datejour']) && !empty($d->fields['datefin'])){
						$d->fields['datejour'] = $d->fields['datefin'];
					}elseif(!empty($d->fields['datejour']) && empty($d->fields['datefin'])){
						$d->fields['datefin'] = $d->fields['datejour'];
					}

					$d->save();
					unset($childs[$k]);
					unset($date1[$k2]);
				}
			}
		}
		// Suppression des fils supprimés
		foreach($childs as $c){
			$c->delete();
		}

		/* Gestion des nouveaux horaires */
		if(count($date1)){
			foreach($date1 as $k => $d1){
				if($d1 != '' && isset($heure1[$k]) && isset($date2[$k]) && isset($heure2[$k])){
					$child = new invitation();
					$child->init_description();
					$child->setugm();
					$child->set('datejour',implode('-',array_reverse(explode('/',$d1))));
					$child->set('heuredeb',$heure1[$k].":00");
					$child->set('datefin',($date2[$k]!='')?implode('-',array_reverse(explode('/',$date2[$k]))):$child->get('datedebut'));
					$child->set('heurefin',$heure2[$k].":00");
					$child->set('id_parent',$parent->get('id'));

					if(empty($child->fields['datejour']) && !empty($child->fields['datefin'])){
						$child->fields['datejour'] = $child->fields['datefin'];
					}elseif(!empty($d->fields['datejour']) && empty($child->fields['datefin'])){
						$child->fields['datefin'] = $child->fields['datejour'];
					}

					$child->save();
				}
			}
		}

		/* Gestion des contacts */
		$contacts = array_filter(explode(';',trim(dims_load_securvalue('contacts', dims_const::_DIMS_CHAR_INPUT, true, true))),function($var){ return $var != ''; });
		$emails = array();
		if(count($contacts)){
			require_once(DIMS_APP_PATH.'modules/system/class_matrix.php');
			require_once(DIMS_APP_PATH.'modules/system/class_ct_group.php');
			foreach($contacts as $idct){
				if(substr($idct, 0,2) == 'gr'){
					$gr = ct_group::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'],'id'=>substr($idct, 2)),null,1);
					if(!empty($gr)){
						$ctsGr = $gr->getContactsGroup(contact::MY_GLOBALOBJECT_CODE);
						foreach ($ctsGr as $c) {
							$lk = matrix::find_by(array('id_action'=>$parent->get('id'),'id_contact'=>$c->get('id_globalobject'), 'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
							if(empty($lk)){
								$lk = new matrix();
								$lk->init_description();
								$lk->setugm();
								$lk->set('id_action',$parent->get('id_globalobject'));
								$lk->set('id_contact',$c->get('id_globalobject'));
								$lk->save();
								$emails[] = $c->get('id');
							}
						}
					}
				}else{
					$lk = matrix::find_by(array('id_action'=>$parent->get('id'),'id_contact'=>$idct, 'id_workspace'=>$_SESSION['dims']['workspaceid']),null,1);
					if(empty($lk)){
						$lk = new matrix();
						$lk->init_description();
						$lk->setugm();
						$lk->set('id_action',$parent->get('id_globalobject'));
						$lk->set('id_contact',$idct);
						$lk->save();
						$emails[] = $idct;
					}
				}
			}
		}
		foreach($emails as $e){
			// on envoit des mails pour les nouveaux contacts ajoutés
			$parent->sendMailInvitation($e);
		}
		$view->flash($_SESSION['cste']['_DATA_HAVE_BEEN_SAVED']);
		dims_redirect(dims::getInstance()->getScriptEnv()."?c=obj&a=view&id=".$parent->get('id'));
		break;
	case 'delete':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$invit = invitation::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid'],'type'=>dims_const::_PLANNING_ACTION_INVITATION,'id_parent'=>0),null,1);
		if(!empty($invit)){
			$invit->delete();
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?c=list&a=view");
		break;
	case 'search_ct':
		ob_clean();
		$val = trim(dims_load_securvalue('val', dims_const::_DIMS_CHAR_INPUT, true, true));
		$not = array_filter(explode(';',trim(dims_load_securvalue('not', dims_const::_DIMS_CHAR_INPUT, true, true))),function($var){ return $var != ''; });
		$return = array();
		if($val != ''){
			require_once(DIMS_APP_PATH . "modules/system/class_search.php");
			$dimsearch = new search(dims::getInstance());
			$dimsearch->addSearchObject(dims_const::_DIMS_MODULE_SYSTEM, contact::MY_GLOBALOBJECT_CODE,$_SESSION['cste']['_DIMS_LABEL_CONTACT']);
			$dimsearch->initSearchObject();

			$kword					= dims_load_securvalue('kword', dims_const::_DIMS_CHAR_INPUT, true, true);
			$idmodule				= dims_load_securvalue('idmodule', dims_const::_DIMS_CHAR_INPUT, true, true);
			$idobj					= dims_load_securvalue('idobj', dims_const::_DIMS_CHAR_INPUT, true, true);
			$idmetafield			= dims_load_securvalue('idmetafield', dims_const::_DIMS_CHAR_INPUT, true, true);
			$sens					= dims_load_securvalue('sens', dims_const::_DIMS_CHAR_INPUT, true, true);

			$dimsearch->executeSearch2($val, $kword,$_SESSION['dims']['moduleid'], $idobj, $idmetafield, $sens,0,null,$_SESSION['dims']['workspaceid']);

			$ids = array();
			foreach($dimsearch->tabresultat as $idmodule => $tab_objects){
				foreach($tab_objects as $idobjet => $tab_ids){
					foreach($tab_ids as $kid => $id){
						if(!in_array($id['id_go'], $not))
							$ids[$id['id_go']] = $id['id_go'];
					}
				}
			}
			if(count($ids)){
				$db = dims::getInstance()->getDb();
				$params = array(':idw'=>array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT));
				if(count($ids)){
					$sel = "SELECT		*
							FROM		".contact::TABLE_NAME."
							WHERE		id_globalobject IN (".$db->getParamsFromArray($ids, 'goct', $params).")
							AND 		id_workspace = :idw
							ORDER BY 	firstname, lastname ASC";
					$res = $db->query($sel, $params);
					while($r = $db->fetchrow($res)){
						$ct = new contact();
						$ct->openFromResultSet($r);
						$photo = "/common/modules/invitation/contacts40.png";
						if(file_exists($ct->getPhotoPath(40))){
							$photo = $ct->getPhotoWebPath(40);
						}
						$elem = array(
							'id' => $ct->get('id_globalobject'),
							'val' => $ct->get('firstname')." ".$ct->get('lastname'),
							'img' => $photo,
						);
						$return[] = $elem;
					}
				}
			}
			require_once(DIMS_APP_PATH . "modules/system/class_ct_group.php");
			$groups = ct_group::conditions(array('label' => array('op' => 'LIKE', 'value' => "%".$val."%"), 'id_workspace'=>array('op'=>'=','value'=>$_SESSION['dims']['workspaceid'])))->order("label")->run();
			foreach($groups as $g){
				$elem = array(
					'id' => "gr".$g->get('id'),
					'val' => $g->get('label')." (".$_SESSION['cste']['_GROUP'].")",
					'img' => "/common/modules/invitation/groupe20.png",
				);
				$return[] = $elem;
			}
		}
		echo json_encode($return);
		die();
		break;
	case 'param':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$invit = invitation::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid'],'type'=>dims_const::_PLANNING_ACTION_INVITATION,'id_parent'=>0),null,1);
		if(!empty($invit)){
			$view->render('headers/params_header.tpl.php', 'header');
			$view->assign('obj',$invit);
			$view->render('content/params.tpl.php');
		}else{
			dims_redirect(dims::getInstance()->getScriptEnv()."?c=list&a=view");
		}
		break;
	case 'save_param':
		$go = dims_load_securvalue('id_globalobject',dims_const::_DIMS_NUM_INPUT,true,true,true);
		if($go != '' && $go > 0){
			$parent = invitation::find_by(array('id_globalobject'=>$go,'id_workspace'=>$_SESSION['dims']['workspaceid'],'type'=>dims_const::_PLANNING_ACTION_INVITATION,'id_parent'=>0),null,1);
			if(!empty($parent)){
				// Action save params
				$parent->setvalues($_POST,'obj_');
				$parent->set('datefin',implode('-',array_reverse(explode('/',dims_load_securvalue('datefin',dims_const::_DIMS_CHAR_INPUT,true,true,true)))));
				$parent->save();
				$view->flash($_SESSION['cste']['_YOUR_SETTINGS_HAVE_BEEN_SAVED']);
				dims_redirect(dims::getInstance()->getScriptEnv()."?c=obj&a=param&id=".$parent->get('id'));
			}else{
				dims_redirect(dims::getInstance()->getScriptEnv()."?c=list&a=view");
			}
		}else{
			dims_redirect(dims::getInstance()->getScriptEnv()."?c=list&a=view");
		}
		break;
	case 'send':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$invit = invitation::find_by(array('id'=>$id,'id_workspace'=>$_SESSION['dims']['workspaceid'],'type'=>dims_const::_PLANNING_ACTION_INVITATION,'id_parent'=>0),null,1);
		if(!empty($invit)){
			$ct = dims_load_securvalue('ct',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$invit->sendMailInvitation($ct);
			dims_redirect(dims::getInstance()->getScriptEnv()."?c=obj&a=view&id=$id");
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?c=list&a=view");
		break;
}
