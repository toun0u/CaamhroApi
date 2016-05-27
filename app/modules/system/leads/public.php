<?php
require_once DIMS_APP_PATH.'modules/system/class_matrix.php';
require_once DIMS_APP_PATH.'modules/system/class_country.php';
require_once DIMS_APP_PATH.'modules/system/class_dims_alert.php';
require_once DIMS_APP_PATH.'modules/system/leads/class_lead.php';
// require_once DIMS_APP_PATH.'modules/system/leads/class_sector.php';
// require_once DIMS_APP_PATH.'modules/system/leads/class_type.php';

$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, true);

switch ($action) {
	case 'manage':
		$lead = new dims_lead();
		break;
	case 'view':
		if ( $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_LEAD_VIEW_OWNS) || $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_LEAD_VIEW_OTHERS) ) {
			$lead_id = dims_load_securvalue('lead_id', dims_const::_DIMS_NUM_INPUT, true, false);
			$lead = new dims_lead();
			$lead->open($lead_id);
		}
		break;
	case 'edit':
		if ( $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_LEAD_CREATE) || $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_LEAD_MODIFY_OWNS) || $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_LEAD_MODIFY_OTHERS) ) {
			unset($_SESSION['desktopv2']['lead']);
			$lead_id = dims_load_securvalue('lead_id', dims_const::_DIMS_NUM_INPUT, true, false);
			$lead = new dims_lead();
			$datestart = array();
			$dateend = array();
			$location = array();
			$added_contact = array();

			if ($lead_id > 0) {
				if ( $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_LEAD_MODIFY_OWNS) || $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_LEAD_MODIFY_OTHERS) ) {
					$lead->open($lead_id);

					// verif des permissions
					$bModify = false;
					if (
						($lead->fields['id_user'] == $_SESSION['dims']['userid'] && $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_LEAD_VIEW_OWNS))
						|| ($lead->fields['id_user'] != $_SESSION['dims']['userid'] && $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_LEAD_VIEW_OTHERS))
					) {
						$bModify = true;
					}
					if (!$bModify) {
						unset($lead);
					}
					else {
						if ($lead->fields['type'] == dims_const::_PLANNING_ACTION_OPPORTUNITY) {
							$datestart = explode('-', $lead->fields['datejour']);
							$dateend = explode('-', $lead->fields['datefin']);
							$location = explode(',',$lead->fields['lieu']);
							$event = 0;
							$sel = "SELECT		DISTINCT dims_mod_business_action.id
									FROM		dims_mod_business_action
									INNER JOIN	dims_matrix
									ON			dims_matrix.id_activity = :idactivity
									AND			dims_matrix.id_action > 0
									AND			dims_matrix.id_action = dims_mod_business_action.id_globalobject

									GROUP BY	dims_matrix.id_action";

							$res = $db->query($sel, array(
								':idactivity' => $lead->fields['id_globalobject']
							));
							if ($r = $db->fetchrow($res))
								$event = $r['id'];
							$lead->setLightAttribute('event',$event);
							$added_contact = array(); // TODO : récupérer la liste des contacts liés array(tiers => fields, contacts => array(contact), ...)
						}
						else {
							$lead->init_description();
						}
					}
				}
			}
			else {
				$lead->init_description();
			}

			if (isset($lead)) {
				$lead->setLightAttribute('location',$location);
				$lead->setLightAttribute('datestart',$datestart);
				$lead->setLightAttribute('dateend',$dateend);
				$lead->setLightAttribute('added_contact',$added_contact);

				// chargement des pays
				$lead->setLightAttribute('a_countries', country::getAllCountries());

				// chargement des secteurs
				// $lead->setLightAttribute('a_sectors', activity_sector::getAllSectors());

				// chargement des types
				// $lead->setLightAttribute('a_types', activity_type::getAllTypes());
			}
		}
		break;
	case 'save':
		$lead_id					= dims_load_securvalue('lead_id', dims_const::_DIMS_NUM_INPUT, false, true);
		$lead_status_id				= dims_load_securvalue('lead_status_id', dims_const::_DIMS_NUM_INPUT, false, true);
		$lead_tiers_id				= dims_load_securvalue('lead_tiers_id', dims_const::_DIMS_NUM_INPUT, false, true);
		$lead_partner_id			= dims_load_securvalue('lead_partner_id', dims_const::_DIMS_NUM_INPUT, false, true);
		$lead_product_id			= dims_load_securvalue('lead_product_id', dims_const::_DIMS_NUM_INPUT, false, true);
		$lead_responsable			= dims_load_securvalue('lead_responsable', dims_const::_DIMS_NUM_INPUT, false, true);
		$lead_budget				= dims_load_securvalue('lead_budget', dims_const::_DIMS_CHAR_INPUT, false, true);
		$lead_date_to				= dims_load_securvalue('lead_date_to', dims_const::_DIMS_CHAR_INPUT, false, true);
		$lead_libelle				= dims_load_securvalue('lead_libelle', dims_const::_DIMS_CHAR_INPUT, false, true);
		$lead_description			= dims_load_securvalue('lead_description', dims_const::_DIMS_CHAR_INPUT, false, true);


		$lead = new dims_lead();
		$lead->init_description();
		if ($lead_id > 0) {
			$lead->open($lead_id);
		}
		else {
			$lead->setugm();
		}


		$lead->fields['status'] = $lead_status_id;
		$lead->fields['tiers_id'] = $lead_tiers_id;
		$lead->fields['opportunity_partner_id'] = $lead_partner_id;
		$lead->fields['opportunity_product_id'] = $lead_product_id;
		$lead->fields['id_responsible'] = $lead_responsable;
		$lead->fields['opportunity_budget'] = $lead_budget;
		$lead->fields['libelle'] = $lead_libelle;
		$lead->fields['description'] = $lead_description;
		$lead->fields['type'] = dims_const::_PLANNING_ACTION_OPPORTUNITY;
		$lead->fields['typeaction'] = dims_lead::TYPE_ACTION;
		$lead->fields['datefin'] = sprintf("%04d-%02d-%02d", substr($lead_date_to, 6, 4), substr($lead_date_to, 3, 2), substr($lead_date_to, 0, 2));


		// documents ajoutés
		if (sizeof($_FILES)) {
			// création du dossier si inexistant
			require_once DIMS_APP_PATH.'modules/doc/class_docfolder.php';
			$folder = new docfolder();
			if (!$lead->fields['dossier_id'] || !$folder->open($lead->fields['dossier_id']) ) {
				$folder->fields['name'] = 'lead_'.$lead->fields['id'];
				$folder->fields['foldertype'] = 'public';
				$folder->fields['readonly'] = 0;
				$folder->fields['readonly_content'] = 0;
				$folder->fields['parents'] = 0;
				$folder->fields['nbelements'] = 0;
				$folder->fields['published'] = 1;
				$folder->fields['id_folder'] = 0;
				$folder->setugm();
				$folder->save();

				$lead->fields['dossier_id'] = $folder->fields['id'];
			}

			// enregistrement des documents
			$files = $_FILES['newDocs'];
			for ($i = 0; $i < sizeof($files['name']); $i++) {
				if (!$files['error'][$i]) {
					$doc = new docfile();
					$doc->fields['name'] = $files['name'][$i];
					move_uploaded_file($files['tmp_name'][$i], DIMS_TMP_PATH . $files['name'][$i]);
					$doc->tmpuploadedfile = DIMS_TMP_PATH.'/'.$files['name'][$i];
					$doc->fields['size'] = $files['size'][$i];
					$doc->fields['id_folder'] = $lead->fields['dossier_id'];
					$doc->setugm();
					$doc->save();

					$_SESSION['desktopv2']['lead']['doc_added'][$doc->fields['id_globalobject']] = $doc->fields['id_globalobject'];
				}
			}
		}

		$lead->save();

		// purge de la matrice avant insertions
		$matrice = new matrix();
		$matrice->purgeData('id_opportunity', $lead->fields['id_globalobject']);

		$matrice->init_description();
		$matrice->fields['year'] = date('Y');
		$matrice->fields['month'] = date('m');
		$matrice->fields['id_opportunity'] = $lead->fields['id_globalobject'];
		$matrice->fields['timestp_modify'] = dims_createtimestamp();
		$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
		$matrice->save();

		// liens dans la matrice avec les contacts liés
		if (!empty($_SESSION['desktopv2']['lead']['ct_added'])) {
			foreach ($_SESSION['desktopv2']['lead']['ct_added'] as $contact_id_go) {
				$matrice = new matrix();
				$matrice->init_description();
				$matrice->fields['year'] = date('Y');
				$matrice->fields['month'] = date('m');
				$matrice->fields['id_opportunity'] = $lead->fields['id_globalobject'];
				$matrice->fields['id_contact'] = $contact_id_go;
				$matrice->fields['timestp_modify'] = dims_createtimestamp();
				$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
				$matrice->save();
			}
			unset($_SESSION['desktopv2']['lead']['ct_added']);
		}

		// liens dans la matrice avec les documents liés
		if (!empty($_SESSION['desktopv2']['lead']['doc_added'])) {
			foreach ($_SESSION['desktopv2']['lead']['doc_added'] as $doc_id_go) {
				$matrice = new matrix();
				$matrice->init_description();
				$matrice->fields['year'] = date('Y');
				$matrice->fields['month'] = date('m');
				$matrice->fields['id_opportunity'] = $lead->fields['id_globalobject'];
				$matrice->fields['id_doc'] = $doc_id_go;
				$matrice->fields['timestp_modify'] = dims_createtimestamp();
				$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
				$matrice->save();
			}
			unset($_SESSION['desktopv2']['lead']['doc_added']);
		}

		$redi = dims_load_securvalue('redirection',dims_const::_DIMS_NUM_INPUT,true,true,true);
		switch($redi){
			default:
			case 0 :
				dims_redirect($dims->getScriptEnv().'?mode=leads&action=view&lead_id='.$lead->getId());
				break;
			case 1 :
				dims_redirect($dims->getScriptEnv().'?mode=leads&action=edit');
				break;
		}
		break;
	case 'abandon':
		$lead_id = dims_load_securvalue('lead_id', dims_const::_DIMS_NUM_INPUT, true, false);
		$lead = new dims_lead();
		$lead->open($lead_id);

		// vérif permissions
		if ( $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_LEAD_CANCEL_OTHERS) ) {
			$lead->abandon();
		}
		elseif ( $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_LEAD_CANCEL_OWNS) ) {
			if ($lead->fields['id_user'] == $_SESSION['dims']['userid']) {
				$lead->abandon();
			}
		}

		dims_redirect($dims->getScriptEnv().'?submenu='._DESKTOP_V2_DESKTOP.'&mode=leads&action=manage');
		break;
	case 'delete':
		$lead_id = dims_load_securvalue('lead_id', dims_const::_DIMS_NUM_INPUT, true, false);
		$lead = new dims_lead();
		$lead->open($lead_id);

		// vérif permissions
		if ( $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_LEAD_DELETE_OTHERS) ) {
			$lead->delete();
		}
		elseif ( $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_LEAD_DELETE_OWNS) ) {
			if ($lead->fields['id_user'] == $_SESSION['dims']['userid']) {
				$lead->delete();
			}
		}

		dims_redirect($dims->getScriptEnv().'?submenu='._DESKTOP_V2_DESKTOP.'&mode=leads&action=manage');
		break;
}
