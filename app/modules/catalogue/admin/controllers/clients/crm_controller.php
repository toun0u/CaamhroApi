<?php
require_once DIMS_APP_PATH.'modules/system/class_country.php';
require_once DIMS_APP_PATH.'modules/system/class_city.php';
require_once DIMS_APP_PATH.'modules/system/class_address.php';
require_once DIMS_APP_PATH.'modules/system/class_tag.php';
require_once DIMS_APP_PATH.'modules/system/class_tag.php';
require_once DIMS_APP_PATH.'modules/system/class_ct_group.php';
require_once DIMS_APP_PATH."modules/system/activity/class_activity.php";
require_once DIMS_APP_PATH.'modules/system/class_matrix.php';

$view = view::getInstance();
$subAction = $view->get('sa');

$contact->setLightAttribute('catalogue_wrapped', true);
// Wrapped by catalogue - Should add/remove shome elements in template.

switch ($subAction) {
	default :
	case 'show':
		$adr = dims_load_securvalue('adr', dims_const::_DIMS_NUM_INPUT, true, true, true);

		$contact->setLightAttribute('adr', $adr); // permet l'ajout d'une adresse à un tiers nouvellement créé

		$contact->setLightAttribute('edit_url', get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'crm', 'sa' => 'edit')));
		$tier = $client->getTiers();

		$view->assign('contact', $contact);
		$view->assign('tier', $tier);

		$view->render('clients/show/crm.tpl.php');
		break;
	case 'new':
	case 'edit':
		$contact->setLightAttribute('save_url', get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'crm', 'sa' => 'save')));
		$contact->setLightAttribute('back_url', get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'crm', 'sa' => 'show')));

		$view->assign('contact', $contact);

		$view->render('clients/show/crm_edit_contact.tpl.php');
		break;
	case 'save':
		$contact->setvalues($_POST, 'ct_');
		$contact->set('id_user', $_SESSION['dims']['userid']);

		$isNew = $contact->isNew();

		if ($contact->save()) {
			$id_contact = $contact->getId();
			require_once DIMS_APP_PATH.'modules/system/crm_contact_add_photo.php';

			require_once DIMS_APP_PATH.'modules/system/class_tag.php';
			require_once DIMS_APP_PATH.'modules/system/class_tag_globalobject.php';

			$tags = dims_load_securvalue('tags', dims_const::_DIMS_NUM_INPUT, true, true);
			if (empty($tags)) {
				$tags = array();
			}
			$myTags = $contact->getMyTags();
			foreach ($myTags as $t) {
				if (in_array($t->get('id'), $tags)) {
					unset($tags[array_search($t->get('id'), $tags)]);
				} else {
					$lk = new tag_globalobject();
					$lk->openWithCouple($t->get('id'), $contact->get('id_globalobject'));
					if (!$lk->isNew()) {
						$lk->delete();
					}
				}
			}
			if (!empty($tags)) {
				foreach ($tags as $t) {
					$lk = new tag_globalobject();
					$lk->init_description();
					$lk->set('id_tag', $t);
					$lk->set('id_globalobject', $contact->get('id_globalobject'));
					$lk->set('timestp_modify', dims_createtimestamp());
					$lk->save();
				}
			}

			$addresses = dims_load_securvalue('addresses', dims_const::_DIMS_NUM_INPUT, true, true, true);
			$address = new address();
			if ($addresses != '' && $addresses > 0) {
				$address->open($addresses);
				if (!$address->isNew() && $address->get('id_workspace') == $_SESSION['dims']['workspaceid'] && is_null($address->getLinkCt($contact->get('id_globalobject')))) {
					$address->addLink($contact->get('id_globalobject'));
				}
			}

			// groupes
			require_once DIMS_APP_PATH.'modules/system/class_ct_group.php';
			$groups = ct_group::find_by(array('id_workspace' => $_SESSION['dims']['workspaceid']), ' ORDER BY label ');
			$LstGr = $lstGrUsed = array();
			foreach ($groups as $gr) {
				$LstGr[$gr->get('id')] = $gr->get('id');
			}
			require_once DIMS_APP_PATH.'modules/system/class_ct_group_link.php';
			$groupsUsed = ct_group_link::find_by(array('id_globalobject' => $contact->get('id_globalobject'), 'type_contact' => contact::MY_GLOBALOBJECT_CODE));
			foreach ($groupsUsed as $gr) {
				$lstGrUsed[$gr->get('id_group_ct')] = $gr;
			}
			$groups = dims_load_securvalue('groups', dims_const::_DIMS_NUM_INPUT, true, true, true);
			foreach ($groups as $g) {
				if (in_array($g, $LstGr)) {
					if (isset($lstGrUsed[$g])) {
						unset($lstGrUsed[$g]);
					} else {
						$lk = new ct_group_link();
						$lk->init_description();
						$lk->set('id_globalobject', $contact->get('id_globalobject'));
						$lk->set('type_contact', contact::MY_GLOBALOBJECT_CODE);
						$lk->set('id_group_ct', $g);
						$lk->set('date_create', dims_createtimestamp());
						$lk->save();
					}
				}
			}
			foreach ($lstGrUsed as $l) {
				$l->delete();
			}

			dims_redirect(get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'crm', 'sa' => 'show')));
		} else {
			// TODO : gérer le ré-import des data dans le form
			dims_redirect(get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'crm', 'sa' => 'edit')));
		}
		break;
	case 'edit_todo':
		require_once DIMS_APP_PATH.'include/class_todo.php';
		$id_todo = dims_load_securvalue('id_todo', dims_const::_DIMS_NUM_INPUT, true, true, true);
		if (!empty($id_todo)) {
			$todo = todo::find_by(array('id_globalobject' => $id_todo, 'id_workspace' => $_SESSION['dims']['workspaceid']), null, 1);
			if (empty($todo)) {
				$todo = new todo();
				$todo->init_description();
			}
		} else {
			$todo = new todo();
			$todo->init_description();
		}

		$todo->setLightAttribute('id_ct', $contact->getId());
		$todo->setLightAttribute('save_url',  get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'crm', 'sa' => 'add_todo')));
		$todo->setLightAttribute('back_url',  get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'crm', 'sa' => 'show')));
		$todo->setLightAttribute('in_catalogue', true);
		$todo->setLightAttribute('client', $client);

		ob_clean();
		$todo->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/todo/edit_todo.tpl.php');
		die();
		break;
	case 'delete_todo':
		require_once DIMS_APP_PATH.'include/class_todo.php';

		$id_todo = dims_load_securvalue('id_todo', dims_const::_DIMS_NUM_INPUT, true, true, true);

		$todo = todo::find_by(array('id' => $id_todo, 'id_workspace' => $_SESSION['dims']['workspaceid']), null, 1);

		if (!empty($todo)) {
			$todo->delete();
		}

		dims_redirect(get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'crm', 'sa' => 'show')));
		break;
	case 'add_todo':
		require_once DIMS_APP_PATH.'include/class_todo.php';

		$todo = new todo();
		$id_todo = dims_load_securvalue('id_todo', dims_const::_DIMS_NUM_INPUT, true, true, true);
		$create = dims_createtimestamp();

		if ($id_todo != '' && $id_todo > 0) {
			$todo->open($id_todo);
			if ($todo->isNew() || $todo->get('id_workspace') != $_SESSION['dims']['workspaceid']) {
				$todo = new todo();
				$todo->init_description();
				$todo->setugm();
				$todo->set('timestp_create', $create);
				$todo->set('user_from', $_SESSION['dims']['userid']);
				$todo->set('id_globalobject_ref', $contact->get('id_globalobject'));
			}
		} else {
			$todo->init_description();
			$todo->setugm();
			$todo->set('timestp_create', $create);
			$todo->set('user_from', $_SESSION['dims']['userid']);
			$todo->set('id_globalobject_ref', $contact->get('id_globalobject'));
		}
		$todo->setvalues($_POST, 'todo_');
		$date = dims_load_securvalue('todo_date', dims_const::_DIMS_CHAR_INPUT, true, true, true);
		$dd = explode('/', $date);
		if (count($dd) == 3) {
			$todo->set('date', $dd[2].'-'.$dd[1].'-'.$dd[0].' 00:00:00');
		}
		$todo->set('timestp_modify', $create);
		$todo->save();

		$todo->initDestinataires();
		$lstDest = $todo->getListDestinataires();

		$user_id = dims_load_securvalue('user_id', dims_const::_DIMS_NUM_INPUT, true, true, true);
		if (!in_array($_SESSION['dims']['workspaceid'], $user_id)) {
			unset($lstDest[$_SESSION['dims']['userid']]);
			$todo->addDestinataire($_SESSION['dims']['userid'], $_SESSION['dims']['userid']);
		}
		foreach ($user_id as $id) {
			unset($lstDest[$id]);
			$todo->addDestinataire($id, $_SESSION['dims']['userid']);
		}
		foreach ($lstDest as $lk) {
			$lk->delete();
		}

		dims_redirect(get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'crm', 'sa' => 'show')));
		break;
	case 'add_tmp_tag':
		include_once DIMS_APP_PATH.'modules/system/class_tag.php';
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true, true);
		$id_tag = dims_load_securvalue('id_tag', dims_const::_DIMS_NUM_INPUT, true, true, true);
		$ct = contact::find_by(array('id' => $id, 'id_workspace' => $_SESSION['dims']['workspaceid']), null, 1);
		$tag = tag::find_by(array('id' => $id_tag, 'id_workspace' => $_SESSION['dims']['workspaceid'], 'type' => tag::TYPE_DURATION), null, 1);
		if (!empty($ct) && !empty($tag)) {
			$year = dims_load_securvalue('year', dims_const::_DIMS_NUM_INPUT, true, true, true);
			$month = dims_load_securvalue('month', dims_const::_DIMS_NUM_INPUT, true, true, true);
			include_once DIMS_APP_PATH.'modules/system/class_matrix.php';
			$m = matrix::find_by(array(
				'id_contact' => $ct->get('id_globalobject'),
				'id_tag' => $tag->get('id'),
				'year' => $year,
				'id_workspace' => $_SESSION['dims']['workspaceid'],
			), null, 1);
			if (empty($m)) {
				$m = new matrix();
				$m->init_description();
				$m->set('id_workspace', $_SESSION['dims']['workspaceid']);
				$m->set('id_contact', $ct->get('id_globalobject'));
				$m->set('id_tag', $tag->get('id'));
				$m->set('year', $year);
				$m->set('month', $month);
				if ($year < date('Y') - 1 || ($year == date('Y') - 1 && $month <= date('m'))) {
					$m->set('timestp_end', $year.$month.'01000000');
				} else {
					include_once DIMS_APP_PATH.'modules/system/class_tag_globalobject.php';
					$lk = tag_globalobject::find_by(array('id_tag' => $tag->get('id'), 'id_globalobject' => $ct->get('id_globalobject')), null, 1);
					if (empty($lk)) {
						$lk = new tag_globalobject();
						$lk->init_description();
						$lk->set('id_tag', $tag->get('id'));
						$lk->set('id_globalobject', $ct->get('id_globalobject'));
						$lk->set('timestp_modify', dims_createtimestamp());
						$lk->save();
					}
				}
				$m->save();
			} elseif ($year >= date('Y') && $m->get('timestp_end') == 0) {
				// On peux l'éditer si le lien n'est pas passé et non fermé
				$m->set('year', $year);
				$m->set('month', $month);
				$m->save();
			}
			unset($_SESSION['dims']['advanced_search']['available_years']);
		}
		dims_redirect(dims::getInstance()->getScriptEnv().'?submenu=1&mode=contact&action=show&id='.$id);
		break;
	case 'del_tmp_tag':
		include_once DIMS_APP_PATH.'modules/system/class_tag.php';
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true, true);
		$id_tag = dims_load_securvalue('id_tag', dims_const::_DIMS_NUM_INPUT, true, true, true);
		$ct = contact::find_by(array('id' => $id, 'id_workspace' => $_SESSION['dims']['workspaceid']), null, 1);
		$tag = tag::find_by(array('id' => $id_tag, 'id_workspace' => $_SESSION['dims']['workspaceid'], 'type' => tag::TYPE_DURATION), null, 1);
		if (!empty($ct) && !empty($tag)) {
			$year = dims_load_securvalue('year', dims_const::_DIMS_NUM_INPUT, true, true, true);
			include_once DIMS_APP_PATH.'modules/system/class_matrix.php';
			$m = matrix::find_by(array(
				'id_contact' => $ct->get('id_globalobject'),
				'id_tag' => $tag->get('id'),
				'year' => $year,
				'id_workspace' => $_SESSION['dims']['workspaceid'],
			), null, 1);
			if (!empty($m)) {
				$m->delete();
			}
			$m = matrix::find_by(array(
				'id_contact' => $ct->get('id_globalobject'),
				'id_tag' => $tag->get('id'),
				'id_workspace' => $_SESSION['dims']['workspaceid'],
			), null, 1);
			if (empty($m)) {
				include_once DIMS_APP_PATH.'modules/system/class_tag_globalobject.php';
				$lk = tag_globalobject::find_by(array('id_tag' => $tag->get('id'), 'id_globalobject' => $ct->get('id_globalobject')), null, 1);
				if (!empty($lk)) {
					$lk->delete();
				}
			}
		}
		dims_redirect(dims::getInstance()->getScriptEnv().'?submenu=1&mode=contact&action=show&id='.$id);
		break;
	case 'add_activity':
		$tier = $client->getTiers();

		$activity_type_id			= dims_load_securvalue('activity_type_id',          dims_const::_DIMS_NUM_INPUT,    false, true);
		$activity_responsable		= dims_load_securvalue('activity_responsable',      dims_const::_DIMS_NUM_INPUT,    false, true);
		$activity_date_from			= dims_load_securvalue('activity_date_from',        dims_const::_DIMS_CHAR_INPUT,   false, true);
		$activity_hour_from			= dims_load_securvalue('activity_hour_from',        dims_const::_DIMS_CHAR_INPUT,   false, true);
		$activity_mins_from			= dims_load_securvalue('activity_mins_from',        dims_const::_DIMS_CHAR_INPUT,   false, true);
		$activity_date_to			= dims_load_securvalue('activity_date_to',          dims_const::_DIMS_CHAR_INPUT,   false, true);
		$activity_hour_to			= dims_load_securvalue('activity_hour_to',          dims_const::_DIMS_CHAR_INPUT,   false, true);
		$activity_mins_to			= dims_load_securvalue('activity_mins_to',          dims_const::_DIMS_CHAR_INPUT,   false, true);
		$activity_opportunity_id	= dims_load_securvalue('activity_opportunity_id',   dims_const::_DIMS_NUM_INPUT,    false, true);
		$activity_label				= dims_load_securvalue('activity_label',            dims_const::_DIMS_CHAR_INPUT,   false, true);
		$activity_description		= dims_load_securvalue('activity_description',      dims_const::_DIMS_CHAR_INPUT,   false, true);

		$activity = new dims_activity();
		$activity->init_description();
		$activity->setugm();

		$activity->fields['tiers_id'] = $tier->fields['id'];

		$activity->fields['type'] = dims_const::_PLANNING_ACTION_ACTIVITY;
		$activity->fields['typeaction'] = dims_activity::TYPE_ACTION;

		$activity->fields['activity_type_id'] = $activity_type_id;
		$activity->fields['id_responsible'] = $activity_responsable;
		$activity->fields['libelle'] = str_replace('"', "'", $activity_label);
		$activity->fields['description'] = $activity_description;

		if ($activity_date_to=="") $activity_date_to=$activity_date_from;
		$activity->fields['datejour'] = sprintf("%04d-%02d-%02d", substr($activity_date_from, 6, 4), substr($activity_date_from, 3, 2), substr($activity_date_from, 0, 2));
		$activity->fields['datefin'] = sprintf("%04d-%02d-%02d", substr($activity_date_to, 6, 4), substr($activity_date_to, 3, 2), substr($activity_date_to, 0, 2));
		$activity->fields['heuredeb'] = $activity_hour_from.':'.$activity_mins_from.':00';
		$activity->fields['heurefin'] = $activity_hour_to.':'.$activity_mins_to.':00';

		$activity->save(dims_const::_SYSTEM_OBJECT_ACTIVITY);

		$datestart_year = substr($activity_date_from, 6, 4);
		$datestart_month = substr($activity_date_from, 3, 2);

		$matrice = new matrix();
		$matrice->fields['id_tiers'] = $tier->fields['id_globalobject'];
		$matrice->fields['year'] = $datestart_year;
		$matrice->fields['month'] = $datestart_month;
		$matrice->fields['id_activity'] = $activity->fields['id_globalobject'];
		$matrice->fields['timestp_modify'] = dims_createtimestamp();
		$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
		$matrice->save();

		dims_redirect(get_path('clients', 'show', array('id' => $client->get('id_client'), 'sc' => 'crm', 'sa' => 'show')));
		break;
}
