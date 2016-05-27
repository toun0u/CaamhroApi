<?php

require_once DIMS_APP_PATH.'modules/system/class_matrix.php';
require_once DIMS_APP_PATH.'modules/system/class_country.php';
require_once DIMS_APP_PATH.'modules/system/class_city.php';
require_once DIMS_APP_PATH.'modules/system/class_dims_alert.php';
require_once DIMS_APP_PATH.'modules/system/activity/class_activity.php';
require_once DIMS_APP_PATH.'modules/system/activity/class_sector.php';
require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';

$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, true);

switch ($action) {
	default:
	case 'manage':
		$activity = new dims_activity();
		break;
	case 'view':
		$activity_id = dims_load_securvalue('activity_id', dims_const::_DIMS_NUM_INPUT, true, false);
		if ($activity_id > 0) {
			$activity = new dims_activity();
			$activity->open($activity_id);
		}
		break;
	case 'edit':
		if ( $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_ACTIVITY_CREATE) || $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_ACTIVITY_MODIFY_OWNS) || $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_ACTIVITY_MODIFY_OTHERS) ) {
			unset($_SESSION['desktopv2']['activity']);
            unset($_SESSION['desktopv2']['opportunity']);
			$activity_id    = dims_load_securvalue('activity_id',   dims_const::_DIMS_NUM_INPUT, true, false);
			$tiers_id       = dims_load_securvalue('tiers_id',      dims_const::_DIMS_NUM_INPUT, true, false);
			$activity = new dims_activity();
			$datestart = array();
			$dateend = array();
			$location = array();
			$added_contact = array();

			if ($activity_id > 0) {
				if ( $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_ACTIVITY_MODIFY_OWNS) || $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_ACTIVITY_MODIFY_OTHERS) ) {
					$activity->open($activity_id);

					// verif des permissions
					$bModify = false;
					if (
						($activity->fields['id_user'] == $_SESSION['dims']['userid'] && $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_ACTIVITY_VIEW_OWNS))
						|| ($activity->fields['id_user'] != $_SESSION['dims']['userid'] && $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_ACTIVITY_VIEW_OTHERS))
					) {
						$bModify = true;
					}
					if (!$bModify) {
						unset($activity);
					}
					else {
						if ($activity->fields['type'] == dims_const::_PLANNING_ACTION_ACTIVITY) {
							$datestart = explode('-', $activity->fields['datejour']);
							$dateend = explode('-', $activity->fields['datefin']);
							$location = explode(',',$activity->fields['lieu']);
							$event = 0;

							$params = array( ':id_activity' => $activity->fields['id_globalobject'] );
							$sel = "SELECT		DISTINCT dims_mod_business_action.id
									FROM		dims_mod_business_action
									INNER JOIN	dims_matrix
									ON			dims_matrix.id_activity = :id_activity
									AND			dims_matrix.id_action > 0
									AND			dims_matrix.id_action = dims_mod_business_action.id_globalobject

									GROUP BY	dims_matrix.id_action";

							$res = $db->query($sel, $params);
							if ($r = $db->fetchrow($res)) {
								$event = $r['id'];
							}
							$activity->setLightAttribute('event',$event);
							$added_contact = array(); // TODO : récupérer la liste des contacts liés array(tiers => fields, contacts => array(contact), ...)
							require_once DIMS_APP_PATH.'modules/system/class_search.php';
							$matrix = new search();
							$my_context = $matrix->exploreMatrice($_SESSION['dims']['workspaceid'], array(), array($activity->fields['id_globalobject']), array(), array(),array(), array(), array(), array(), array());

							if (!empty($my_context['distribution']['tiers'])) {
								foreach ($my_context['distribution']['tiers'] as $globalobjectId => $ata) {
									$tier = new tiers();
									$tier->openWithGB($globalobjectId);

									$_SESSION['desktopv2']['opportunity']['tiers_added'][$tier->fields['id']] = $tier->fields['id'];
								}
							}
						}
						else {
							$activity->init_description();
						}
					}
				}
			}
			else {
				if ($dims->isActionAllowed(dims_const::_SYSTEM_ACTION_ACTIVITY_CREATE)) {
					$_SESSION['desktopv2']['opportunity']['tiers_added'][$tiers_id] = $tiers_id;
					$activity->init_description();
                    $activity->fields["private"]=1;

                    $activity->fields['datejour'] = sprintf("%04d-%02d-%02d", date("Y"), date("m"), date("d"));
                    $activity->fields['datefin'] = sprintf("%04d-%02d-%02d", date("Y"), date("m"), date("d"));
                    $datestart = explode('-', $activity->fields['datejour']);
                    $dateend = explode('-', $activity->fields['datefin']);

				}
			}

			if (isset($activity)) {
				$activity->setLightAttribute('location',$location);
				$activity->setLightAttribute('datestart',$datestart);
				$activity->setLightAttribute('dateend',$dateend);
				$activity->setLightAttribute('added_contact',$added_contact);

				// chargement des pays
				$activity->setLightAttribute('a_countries', country::getAllCountries());

				// chargement des secteurs
				$activity->setLightAttribute('a_sectors', activity_sector::getAllSectors());

				// chargement des types
				$activity->setLightAttribute('a_types', activity_type::getAllTypes());
			}
		}
		break;
	case 'save':

		require_once DIMS_APP_PATH.'include/class_input_validator.php';
		$val = new InVal\ValidatorSet();

		$val->add('activity_date_from', 'date')		->rule(new InVal\Rule\Required);
		//$val->add('activity_date_to',   'date')		->rule(new InVal\Rule\Required);
		$val->add('activity_hour_from', 'integer')	->rule(new InVal\Rule\Required)->rule(new InVal\Rule\Range('[0;23]'));
		//$val->add('activity_hour_to',    'integer')	->rule(new InVal\Rule\Required)->rule(new InVal\Rule\Range('[0;23]'));
		$val->add('activity_mins_from', 'integer')	->rule(new InVal\Rule\Required)->rule(new InVal\Rule\Range('[0;59]'));
		//$val->add('activity_mins_to',   'integer')	->rule(new InVal\Rule\Required)->rule(new InVal\Rule\Range('[0;59]'));

		$valRadio = new InVal\IntegerValidator('activity_alert_mode');
		$valRadio->rule(new InVal\Rule\InList(0, 1, 2));

		$valAct1 = new \InVal\IntegerValidator('activity_alert_nb_period');
		$valAct1->rule(new InVal\Rule\Range('[0'));

		$valAct2 = new \InVal\DateValidator('activity_alert_date');
		$valAct2->rule(new InVal\Rule\Range('[0;23]'));

		$valAct3 = new \InVal\IntegerValidator('activity_alert_hour');

		switch ($valRadio->getInput()) {
			case 0:
				$valAct1->rule(new InVal\Rule\Required);
				$val->add($valAct1);
				break;
			case 1:
				$valAct2->rule(new InVal\Rule\Required);
				$val->add($valAct2);
				break;
			case 2:
				$valAct3->rule(new InVal\Rule\Required);
				$val->add($valAct3);
				break;
			default:
				break;
		}

		$activity_id				= dims_load_securvalue('activity_id', dims_const::_DIMS_NUM_INPUT, false, true);
		$activity_type_id			= dims_load_securvalue('activity_type_id', dims_const::_DIMS_NUM_INPUT, false, true);
		$activity_responsable		= dims_load_securvalue('activity_responsable', dims_const::_DIMS_NUM_INPUT, false, true);
		$activity_date_from			= dims_load_securvalue('activity_date_from', dims_const::_DIMS_CHAR_INPUT, false, true);
		$activity_hour_from			= dims_load_securvalue('activity_hour_from', dims_const::_DIMS_CHAR_INPUT, false, true);
		$activity_mins_from			= dims_load_securvalue('activity_mins_from', dims_const::_DIMS_CHAR_INPUT, false, true);
		$activity_date_to			= dims_load_securvalue('activity_date_to', dims_const::_DIMS_CHAR_INPUT, false, true);
		$activity_hour_to			= dims_load_securvalue('activity_hour_to', dims_const::_DIMS_CHAR_INPUT, false, true);
		$activity_mins_to			= dims_load_securvalue('activity_mins_to', dims_const::_DIMS_CHAR_INPUT, false, true);
		$activity_opportunity_id	= dims_load_securvalue('activity_opportunity_id', dims_const::_DIMS_NUM_INPUT, false, true);
		$activity_label				= dims_load_securvalue('activity_label', dims_const::_DIMS_CHAR_INPUT, false, true);
		$activity_description		= dims_load_securvalue('activity_description', dims_const::_DIMS_CHAR_INPUT, false, true);
		$activity_address			= dims_load_securvalue('activity_address', dims_const::_DIMS_CHAR_INPUT, false, true);
		$activity_cp				= dims_load_securvalue('activity_cp', dims_const::_DIMS_CHAR_INPUT, false, true);
		$activity_city_id			= dims_load_securvalue('activity_city_id', dims_const::_DIMS_NUM_INPUT, false, true);
		$activity_country_id		= dims_load_securvalue('activity_country_id', dims_const::_DIMS_CHAR_INPUT, false, true);
		$activity_alert_mode		= dims_load_securvalue('activity_alert_mode', dims_const::_DIMS_NUM_INPUT, false, true);
		$activity_alert_nb_period	= dims_load_securvalue('activity_alert_nb_period', dims_const::_DIMS_NUM_INPUT, false, true);
		$activity_alert_period		= dims_load_securvalue('activity_alert_period', dims_const::_DIMS_CHAR_INPUT, false, true);
		$activity_alert_date		= dims_load_securvalue('activity_alert_date', dims_const::_DIMS_CHAR_INPUT, false, true);
		$activity_alert_hour		= dims_load_securvalue('activity_alert_hour', dims_const::_DIMS_CHAR_INPUT, false, true);
		$activity_alert_mins		= dims_load_securvalue('activity_alert_mins', dims_const::_DIMS_CHAR_INPUT, false, true);
		$activity_private			= dims_load_securvalue('activity_private', dims_const::_DIMS_NUM_INPUT, false, true);

		$country = new country();
		$country->open($activity_country_id);

		$activity = new dims_activity();
		$activity->init_description();

		if ($activity_id > 0) {
			$activity->open($activity_id);
		}
		else {
			$activity->setugm();
		}

		if ($valRadio->validate() && $val->validate()) {
			// ville
			if ($activity_city_id) {
				$city = new city();
				$city->open($activity_city_id);
				$activity->fields['lieu'] = $city->fields['label'];
				$activity->fields['id_city'] = $activity_city_id;
			}
			else {
				$activity->fields['lieu'] = '';
				$activity->fields['id_city'] = 0;
			}

			$activity->fields['private'] = $activity_private;
			$activity->fields['id_responsible'] = $activity_responsable;
			$activity->fields['libelle'] = str_replace('"', "'", $activity_label);
			$activity->fields['description'] = $activity_description;
			$activity->fields['id_country'] = $country->fields['id'];
			$activity->fields['address'] = $activity_address;
			$activity->fields['cp'] = $activity_cp;
			$activity->fields['type'] = dims_const::_PLANNING_ACTION_ACTIVITY;
			$activity->fields['typeaction'] = dims_activity::TYPE_ACTION;
			$activity->fields['activity_type_id'] = $activity_type_id;
            if ($activity_date_to=="") $activity_date_to=$activity_date_from;
			$activity->fields['datejour'] = sprintf("%04d-%02d-%02d", substr($activity_date_from, 6, 4), substr($activity_date_from, 3, 2), substr($activity_date_from, 0, 2));
			$activity->fields['datefin'] = sprintf("%04d-%02d-%02d", substr($activity_date_to, 6, 4), substr($activity_date_to, 3, 2), substr($activity_date_to, 0, 2));
			$activity->fields['heuredeb'] = $activity_hour_from.':'.$activity_mins_from.':00';
			$activity->fields['heurefin'] = $activity_hour_to.':'.$activity_mins_to.':00';


			// documents ajoutés
			if (sizeof($_FILES)) {
				// création du dossier si inexistant
				require_once DIMS_APP_PATH.'modules/doc/class_docfolder.php';
				$folder = new docfolder();
				if (!$activity->fields['dossier_id'] || !$folder->open($activity->fields['dossier_id']) ) {
					$folder->fields['name'] = 'activity_'.$activity->fields['id'];
					$folder->fields['foldertype'] = 'public';
					$folder->fields['readonly'] = 0;
					$folder->fields['readonly_content'] = 0;
					$folder->fields['parents'] = 0;
					$folder->fields['nbelements'] = 0;
					$folder->fields['published'] = 1;
					$folder->fields['id_folder'] = 0;
					$folder->setugm();
					$folder->save();

					$activity->fields['dossier_id'] = $folder->fields['id'];
				}

				// enregistrement des documents
				$files = $_FILES['newDocs'];
				for ($i = 0; $i < sizeof($files['name']); $i++) {
					if (!$files['error'][$i]) {
						$doc = new docfile();
						$doc->init_description();
						$doc->fields['name'] = $files['name'][$i];
						move_uploaded_file($files['tmp_name'][$i], DIMS_TMP_PATH . $files['name'][$i]);
						$doc->tmpuploadedfile = DIMS_TMP_PATH.'/'.$files['name'][$i];
						$doc->fields['size'] = $files['size'][$i];
						$doc->fields['id_folder'] = $activity->fields['dossier_id'];
						$doc->setugm();
						$doc->save();

						$_SESSION['desktopv2']['activity']['doc_added'][$doc->fields['id_globalobject']] = $doc->fields['id_globalobject'];
					}
				}
			}

			$activity->save(dims_const::_SYSTEM_OBJECT_ACTIVITY);

			// alerte email
			$a_alerts = dims_alert::getAllByGOOrigin($activity->fields['id_globalobject']);

			$alert = new dims_alert();
			if (sizeof($a_alerts)) {
				$alert->open($a_alerts[0]->getId());
			}
			else {
				$alert->init_description(true);
			}

			if ($activity_alert_mode > 0) {
				// lien vers l'activité
				$alert->setGOOrigin($activity->fields['id_globalobject']);

				switch ($activity_alert_mode) {
					case dims_alert::MODE_RELATIVE:
						if ($activity_alert_period != '' && $activity_alert_nb_period > 0) {
							$ts_activity = mktime(
								$activity_hour_from,
								$activity_mins_from,
								0,
								substr($activity_date_from, 3, 2),
								substr($activity_date_from, 0, 2),
								substr($activity_date_from, 6, 4)
								);
							$alert->setRelative($ts_activity, $activity_alert_period, $activity_alert_nb_period);
							$alert->save();
						}
						break;
					case dims_alert::MODE_ABSOLUTE:
						if ($activity_alert_date != '' && $activity_alert_hour != '' && $activity_alert_mins != '') {
							$alert->setAbsolute($activity_alert_date, $activity_alert_hour.':'.$activity_alert_mins.':00');
							$alert->save();
						}
						break;
				}
			}
			// si plus d'alerte, on la supprime
			elseif (sizeof($a_alerts)) {
				$alert->delete();
			}


			// purge de la matrice avant insertions
			$matrice = new matrix();
			$matrice->purgeData('id_activity', $activity->fields['id_globalobject']);

			$matrice->init_description();
			$matrice->fields['id_country'] = $country->fields['id'];
			$matrice->fields['year'] = substr($activity_date_from, 6, 4);
			$matrice->fields['month'] = substr($activity_date_from, 3, 2);
			$matrice->fields['id_activity'] = $activity->fields['id_globalobject'];
			$matrice->fields['timestp_modify'] = dims_createtimestamp();
			$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
			$matrice->save();

			$listTiersassoc=array(); // not adding already attach tiers

            /*
			// liens dans la matrice avec les contacts liés
			if (!empty($_SESSION['desktopv2']['activity']['ct_added'])) {
				dims_print_r($_SESSION['desktopv2']['activity']['ct_added']);
				foreach ($_SESSION['desktopv2']['activity']['ct_added'] as $elem) {
					if (is_array($elem)) {
						if (isset($elem['id']) && $elem['id']>0
						&& isset($elem['src']) && $elem['src']>=0) {
							$ct=new contact();
							$ct->open($elem['id']);
							$contact_id_go=$ct->fields['id_globalobject'];

							$tiers_id_go=0;
							if ($elem['src']>0) {
								$t=new tiers();
								$t->open($elem['src']);
								$tiers_id_go=$t->fields['id_globalobject'];
							}

							$matrice = new matrix();
							$matrice->init_description();
							$matrice->fields['id_country'] = $country->fields['id'];
							$matrice->fields['year'] = substr($activity_date_from, 6, 4);
							$matrice->fields['month'] = substr($activity_date_from, 3, 2);
							$matrice->fields['id_activity'] = $activity->fields['id_globalobject'];
							$matrice->fields['id_contact'] = $contact_id_go;
							$matrice->fields['id_tiers'] = $tiers_id_go;
							$matrice->fields['timestp_modify'] = dims_createtimestamp();
							$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
							$matrice->save();

							// liaison avec l'entreprise
							if ($tiers_id_go>0 && !isset($listTiersassoc[$elem['src']])) {

								$matrice = new matrix();
								$matrice->init_description();
								$matrice->fields['id_country'] = $country->fields['id'];
								$matrice->fields['year'] = substr($activity_date_from, 6, 4);
								$matrice->fields['month'] = substr($activity_date_from, 3, 2);
								$matrice->fields['id_activity'] = $activity->fields['id_globalobject'];
								$matrice->fields['id_tiers'] = $tiers_id_go;
								$matrice->fields['timestp_modify'] = dims_createtimestamp();
								$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
								$matrice->save();

								// to be sure not adding already defined
								$listTiersassoc[$elem['src']]=$elem['src'];
							}
						}

					}
					else {
						$matrice = new matrix();
						$matrice->init_description();
						$matrice->fields['id_country'] = $country->fields['id'];
						$matrice->fields['year'] = substr($activity_date_from, 6, 4);
						$matrice->fields['month'] = substr($activity_date_from, 3, 2);
						$matrice->fields['id_activity'] = $activity->fields['id_globalobject'];
						$matrice->fields['id_contact'] = $elem;
						$matrice->fields['timestp_modify'] = dims_createtimestamp();
						$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
						$matrice->save();
					}
				}
				unset($_SESSION['desktopv2']['activity']['ct_added']);
			}*/
            $lstTiers = $desktop->constructLstTiersFromCt(array(),$_SESSION['desktopv2']['opportunity']['ct_added'],$_SESSION['desktopv2']['opportunity']['tiers_added']);
            $lstContactsMatrice = array();
            $datestart_year = substr($activity_date_from, 6, 4);
            $datestart_month = substr($activity_date_from, 3, 2);

            // on va se rajouter à tous ces contacts et entreprises que l'on vient d'enregistrer
            $u = new contact();
            $u->open($_SESSION['dims']['user']['id_contact']);

            if (!empty($lstTiers)) {
	            $tier = current($lstTiers);
	            $activity->fields['tiers_id'] = $tier->fields['id'];
	            $activity->save(dims_const::_SYSTEM_OBJECT_ACTIVITY);
	        }

            foreach($lstTiers as $tiers){
                if (isset($tiers->contacts)){
                    foreach($tiers->contacts as $ct){
                        $matrice = new matrix();
                        $matrice->fields['id_tiers'] = $tiers->fields['id_globalobject'];
                        $matrice->fields['id_contact'] = $ct->fields['id_globalobject'];
                        $matrice->fields['id_country'] = $country->fields['id'];
                        $matrice->fields['year'] = $datestart_year;
                        $matrice->fields['month'] = $datestart_month;
                        $matrice->fields['id_activity'] = $activity->fields['id_globalobject'];
                        $matrice->fields['timestp_modify'] = dims_createtimestamp();
                        $matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
                        $matrice->fields['id_action'] = $action->fields['id_globalobject'];
                        $matrice->save();

                        $elem = array();
                        $elem['id_ct'] = $ct->fields['id_globalobject'];
                        $elem['account'] = $ct->hasAccount();
                        $lstContactsMatrice[] = $elem;

                        // test si rattachement avec la personne courante
                        if (!isset($listcontacts[$ct->fields['id_globalobject']])) {
                            $matrice = new matrix();
                            $matrice->fields['id_tiers'] = 0;
                            $matrice->fields['id_contact'] = $u->fields['id_globalobject'];
                            $matrice->fields['id_contact2'] = $ct->fields['id_globalobject'];
                            $matrice->fields['id_country'] = $country->fields['id'];
                            $matrice->fields['year'] = $datestart_year;
                            $matrice->fields['month'] = $datestart_month;
                            $matrice->fields['id_activity'] = $activity->fields['id_globalobject'];
                            $matrice->fields['timestp_modify'] = dims_createtimestamp();
                            $matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
                            $matrice->fields['id_action'] = $action->fields['id_globalobject'];
                            $matrice->save();

                            $matrice = new matrix();
                            $matrice->fields['id_tiers'] = 0;
                            $matrice->fields['id_contact'] = $ct->fields['id_globalobject'];
                            $matrice->fields['id_contact2'] = $u->fields['id_globalobject'];
                            $matrice->fields['id_country'] = $country->fields['id'];
                            $matrice->fields['year'] = $datestart_year;
                            $matrice->fields['month'] = $datestart_month;
                            $matrice->fields['id_activity'] = $activity->fields['id_globalobject'];
                            $matrice->fields['timestp_modify'] = dims_createtimestamp();
                            $matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
                            $matrice->fields['id_action'] = $action->fields['id_globalobject'];
                            $matrice->save();

                            $ctlink = new ctlink();
                            $ctlink->fields['id_contact1'] = $_SESSION['dims']['user']['id_contact'];
                            $ctlink->fields['id_contact2'] = $ct->fields['id'];
                            $ctlink->fields['id_object'] = dims_const::_SYSTEM_OBJECT_CONTACT;
                            $ctlink->fields['type_link'] = 'business';
                            $ctlink->fields['link_level'] = 2;
                            $ctlink->fields['id_ct_user_create'] = $_SESSION['dims']['userid'];
                            $ctlink->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
                            $ctlink->fields['id_user'] = $_SESSION['dims']['userid'];
                            $ctlink->save();

                            $ctlink = new ctlink();
                            $ctlink->fields['id_contact1'] = $ct->fields['id'];
                            $ctlink->fields['id_contact2'] = $_SESSION['dims']['user']['id_contact'];
                            $ctlink->fields['id_object'] = dims_const::_SYSTEM_OBJECT_CONTACT;
                            $ctlink->fields['type_link'] = 'business';
                            $ctlink->fields['link_level'] = 2;
                            $ctlink->fields['id_ct_user_create'] = $_SESSION['dims']['userid'];
                            $ctlink->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
                            $ctlink->fields['id_user'] = $_SESSION['dims']['userid'];
                            $ctlink->save();

                        }
                    }

                    // test si rattachement avec la personne courante
                    if (!isset($listCompanies[$tiers->fields['id_globalobject']])) {
                        $matrice = new matrix();
                        $matrice->fields['id_tiers'] = $tiers->fields['id_globalobject'];
                        $matrice->fields['id_contact'] = $u->fields['id_globalobject'];
                        $matrice->fields['id_country'] = $country->fields['id'];
                        $matrice->fields['year'] = $datestart_year;
                        $matrice->fields['month'] = $datestart_month;
                        $matrice->fields['id_activity'] = $activity->fields['id_globalobject'];
                        $matrice->fields['timestp_modify'] = dims_createtimestamp();
                        $matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
                        $matrice->fields['id_action'] = 0;

                        $matrice->save();

                        $tiersct = new tiersct();
                        $tiersct->fields['id_tiers'] = $tiers->fields['id'];
                        $tiersct->fields['id_contact'] = $_SESSION['dims']['user']['id_contact'];
                        $tiersct->fields['type_lien'] = 'Other';
                        $tiersct->fields['link_level'] = 2;
                        $matrice->fields['id_activity'] = $activity->fields['id_globalobject'];
                        $tiersct->fields['id_user'] = $_SESSION['dims']['userid'];
                        $tiersct->fields['date_create'] = dims_createtimestamp();
                        $tiersct->save();

                        // on met a jour l'activity id_tiers
                        // c pas tres bien car on ne recherche pas dans la matrice mais bon
                        $activity->fields['tiers_id']=$tiers->fields['id'];
                        $activity->save();

                    }
                }
            }


			// liens dans la matrice avec les opportunités liées
			if (!empty($_SESSION['desktopv2']['activity']['opp_added'])) {
				foreach ($_SESSION['desktopv2']['activity']['opp_added'] as $opportunity_id_go) {
					$matrice = new matrix();
					$matrice->init_description();
					$matrice->fields['id_country'] = $country->fields['id'];
					$matrice->fields['year'] = substr($activity_date_from, 6, 4);
					$matrice->fields['month'] = substr($activity_date_from, 3, 2);
					$matrice->fields['id_activity'] = $activity->fields['id_globalobject'];
					$matrice->fields['id_opportunity'] = $opportunity_id_go;
					$matrice->fields['timestp_modify'] = dims_createtimestamp();
					$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
					$matrice->save();
				}
				unset($_SESSION['desktopv2']['activity']['opp_added']);
			}

			// liens dans la matrice avec les documents liés
			if (!empty($_SESSION['desktopv2']['activity']['doc_added'])) {
				foreach ($_SESSION['desktopv2']['activity']['doc_added'] as $doc_id_go) {
					if ($doc_id_go != '' && $doc_id_go > 0){
						$matrice = new matrix();
						$matrice->init_description();
						$matrice->fields['id_country'] = $country->fields['id'];
						$matrice->fields['year'] = substr($activity_date_from, 6, 4);
						$matrice->fields['month'] = substr($activity_date_from, 3, 2);
						$matrice->fields['id_activity'] = $activity->fields['id_globalobject'];
						$matrice->fields['id_doc'] = $doc_id_go;
						$matrice->fields['timestp_modify'] = dims_createtimestamp();
						$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
						$matrice->save();
					}
				}
				unset($_SESSION['desktopv2']['activity']['doc_added']);
			}
            unset($_SESSION['desktopv2']['activity']);
            unset($_SESSION['desktopv2']['opportunity']);
		} else {
				echo "Erreurs de saisie: ";
				print_r($val->getErrors());
		}


		$redi = dims_load_securvalue('redirection',dims_const::_DIMS_NUM_INPUT,true,true,true);
		switch($redi){
			default:
			case 0 :
				dims_redirect($dims->getScriptEnv().'?mode=activity&action=view&activity_id='.$activity->getId());
				break;
			case 1 :
				dims_redirect($dims->getScriptEnv().'?mode=activity&action=edit');
				break;
		}


	break;
}
