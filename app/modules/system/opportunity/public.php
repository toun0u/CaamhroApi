<?php
require_once DIMS_APP_PATH.'modules/system/class_country.php';
require_once DIMS_APP_PATH.'modules/system/opportunity/class_opportunity.php';
require_once DIMS_APP_PATH.'modules/system/opportunity/class_sector.php';
require_once DIMS_APP_PATH.'modules/system/opportunity/class_type.php';

$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, true);

switch ($action) {
	case 'manage':
		$opp = new dims_opportunity();
		break;
	case 'edit':
		unset($_SESSION['desktopv2']['opportunity']);
		$opp_id = dims_load_securvalue('opp_id', dims_const::_DIMS_NUM_INPUT, true, false);
		$opp = new dims_opportunity();
		$datestart = array();
		$dateend = array();
		$location = array();
		$added_contact = array();
		if ($opp_id > 0) {
			$opp->open($opp_id);
			if ($opp->fields['type'] == dims_const::_PLANNING_ACTION_OPPORTUNITY) {
				$datestart = explode('-', $opp->fields['datejour']);
				$dateend = explode('-', $opp->fields['datefin']);
				$location = explode(',',$opp->fields['lieu']);
				$event = 0;
				$sel = "SELECT		DISTINCT dims_mod_business_action.id
						FROM		dims_mod_business_action
						INNER JOIN	dims_matrix
						ON			dims_matrix.id_opportunity = :idopportunity
						AND			dims_matrix.id_action > 0
						AND			dims_matrix.id_action = dims_mod_business_action.id_globalobject

						GROUP BY	dims_matrix.id_action";

				$res = $db->query($sel, array(
					':idopportunity' => $this->fields['id_globalobject']
				));
				if ($r = $db->fetchrow($res))
					$event = $r['id'];
				$this->setLightAttribute('event',$event);
				$added_contact = array(); // TODO : récupérer la liste des contacts liés array(tiers => fields, contacts => array(contact), ...)
			}
			else {
				$opp->init_description();
			}
		}
		else {
			$opp->init_description();
		}
		$opp->setLightAttribute('location',$location);
		$opp->setLightAttribute('datestart',$datestart);
		$opp->setLightAttribute('dateend',$dateend);
		$opp->setLightAttribute('added_contact',$added_contact);

		// chargement des pays
		$opp->setLightAttribute('a_countries', country::getAllCountries());

		// chargement des secteurs
		$opp->setLightAttribute('a_sectors', opportunity_sector::getAllSectors());

		// chargement des types
		$opp->setLightAttribute('a_types', opportunity_type::getAllTypes());
		break;
	case 'save':
		require_once DIMS_APP_PATH."modules/system/class_matrix.php";

		$opp_id				= dims_load_securvalue('opp_id', dims_const::_DIMS_NUM_INPUT, false, true);
		$label				= dims_load_securvalue('label', dims_const::_DIMS_CHAR_INPUT, false, true);
		$description		= dims_load_securvalue('description', dims_const::_DIMS_CHAR_INPUT, false, true);
		$id_country			= dims_load_securvalue('opportunity_country', dims_const::_DIMS_NUM_INPUT, false, true);
		$city				= dims_load_securvalue('opportunity_city', dims_const::_DIMS_NUM_INPUT, false, true);
		$sector_id			= dims_load_securvalue('sector_id', dims_const::_DIMS_NUM_INPUT, false, true);
		$type_id			= dims_load_securvalue('type_id', dims_const::_DIMS_NUM_INPUT, false, true);
		$datestart_day		= dims_load_securvalue('datestart_day', dims_const::_DIMS_NUM_INPUT, false, true);
		$datestart_month	= dims_load_securvalue('datestart_month', dims_const::_DIMS_NUM_INPUT, false, true);
		$datestart_year		= dims_load_securvalue('datestart_year', dims_const::_DIMS_NUM_INPUT, false, true);
		$dateend_day		= dims_load_securvalue('dateend_day', dims_const::_DIMS_NUM_INPUT, false, true);
		$dateend_month		= dims_load_securvalue('dateend_month', dims_const::_DIMS_NUM_INPUT, false, true);
		$dateend_year		= dims_load_securvalue('dateend_year', dims_const::_DIMS_NUM_INPUT, false, true);

		$country = new country();
		$country->open($id_country);

		$opp = new dims_opportunity();
		$opp->init_description();
		if ($opp_id > 0) {
			$opp->open($opp_id);
		}
		$opp->setugm();

		$opp->fields['libelle'] = $label;
		$opp->fields['description'] = $description;
		$opp->fields['id_country'] = $id_country;
		$opp->fields['lieu'] = $country->fields['name'];
		if ($city != '') $opp->fields['lieu'] .= ', '.$city;
		$opp->fields['type'] = dims_const::_PLANNING_ACTION_OPPORTUNITY;
		$opp->fields['typeaction'] = '_DIMS_EVENT_OPPORTUNITIES';
		$opp->fields['opportunity_sector_id'] = $sector_id;
		$opp->fields['opportunity_type_id'] = $type_id;

		// pour la date, seule l'année est obligatoire
		// le champ dateextended nous dit ce que le champ datejour contient réellement :
		// DATE_FORMAT_JJMMAAAA pour tenir compte de la date complete
		// DATE_FORMAT_MMAAAA pour tenir compte du mois et de l'année
		// DATE_FORMAT_AAAA pour tenir compte seulement de l'année
		if ($datestart_month == 0) {
			$opp->fields['dateextended'] = dims_opportunity::DATE_FORMAT_AAAA;
		}
		elseif ($datestart_day == 0) {
			$opp->fields['dateextended'] = dims_opportunity::DATE_FORMAT_MMAAAA;
		}
		else {
			$opp->fields['dateextended'] = dims_opportunity::DATE_FORMAT_JJMMAAAA;
		}

		$opp->fields['datejour'] = sprintf("%04d-%02d-%02d", $datestart_year, $datestart_month, $datestart_day);
		$opp->fields['datefin'] = sprintf("%04d-%02d-%02d", $dateend_year, $dateend_month, $dateend_day);
		$opp->save(dims_const::_SYSTEM_OBJECT_OPPORTUNITY);

		// events
		$link = dims_load_securvalue('link', dims_const::_DIMS_NUM_INPUT, true, true, true);
		if ($link != '' && $link > 0){
			$action = new action();
			if ($action->open($link)){
				$opp->fields['datejour'] = $action->fields['datejour'];
				// $opp->fields['datefin'] = $action->fields['datefin'];
				$opp->save();

				$date = explode('-',$opp->fields['datejour']);
				$matrice = new matrix();
				$matrice->init_description();
				$matrice->fields['id_country'] = $id_country;
				$matrice->fields['year'] = $date[0];
				$matrice->fields['month'] = $date[1];
				$matrice->fields['id_opportunity'] = $opp->fields['id_globalobject'];
				$matrice->fields['id_action'] = $action->fields['id_globalobject'];
				$matrice->fields['timestp_modify'] = dims_createtimestamp();
				$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
				$matrice->save();
			}
		}

		// tags
		$db = dims::getInstance()->getDb();
		$tmspt = dims_createtimestamp();

		if (isset($_POST['tags'])) {
			$tags = dims_load_securvalue('tags', dims_const::_DIMS_NUM_INPUT, true, true, true);
			foreach ($tags as $idt){
					$db->query("INSERT INTO dims_tag_globalobject VALUES ( :idt , :idglobalobject , :tmspt )", array(
						':idt'				=> $idt,
						':idglobalobject'	=> $opp->fields['id_globalobject'],
						':tmspt'			=> $tmspt
					));
			}
		}
		// avatar
		if (!empty($_FILES['avatar']) && !$_FILES['avatar']['error']) {
			$file = $_FILES['avatar'];
			if (!file_exists(_OPPORTUNITY_AVATAR_FILE_PATH))
				dims_makedir(_OPPORTUNITY_AVATAR_FILE_PATH);

			if ($file['size'] <= _OPPORTUNITY_AVATAR_MAX_SIZE) {
        	    require_once DIMS_APP_PATH."include/class_input_validator.php";
        	    $valid = new \InVal\FileValidator('avatar');
                $valid->rule(new \InVal\Rule\Image(true));

                if ($valid->validate()) {
    				// suppression de l'ancien si présent
    				if ($opp->fields['banner_path'] != '' && file_exists(_OPPORTUNITY_AVATAR_FILE_PATH.$opp->fields['banner_path'])) {
    					unlink(_OPPORTUNITY_AVATAR_FILE_PATH.$opp->fields['banner_path']);
    				}

    				// ajout du nouveau
    				$file_type = explode('/', $file['type']);
    				move_uploaded_file($file['tmp_name'], _OPPORTUNITY_AVATAR_FILE_PATH.$opp->fields['id'].'_tmp.'.$file_type[1]);
    				dims_resizeimage(_OPPORTUNITY_AVATAR_FILE_PATH.$opp->fields['id'].'_tmp.'.$file_type[1], 0, _OPPORTUNITY_AVATAR_MAX_WIDTH, _OPPORTUNITY_AVATAR_MAX_HEIGHT, '', 0, _OPPORTUNITY_AVATAR_FILE_PATH.$opp->fields['id'].'.'.$file_type[1]);
    				// suppression du fichier temporaire
    				unlink(_OPPORTUNITY_AVATAR_FILE_PATH.$opp->fields['id'].'_tmp.'.$file_type[1]);

    				$opp->fields['banner_path'] = "."._OPPORTUNITY_AVATAR_WEB_PATH.$opp->fields['id'].'.'.$file_type[1];
                }
				$opp->save();
			}
		}

		$matrice = new matrix();
		$matrice->init_description();
		$matrice->fields['id_country'] = $country;
		$matrice->fields['year'] = $datestart_year;
		$matrice->fields['month'] = $datestart_month;
		$matrice->fields['id_opportunity'] = $opp->fields['id_globalobject'];
		$matrice->fields['timestp_modify'] = dims_createtimestamp();
		$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
		$matrice->save();

		// lien dans la matrice avec les doc liés
		$sid = session_id();
		$upload_dir = realpath('./data/uploads/'.$sid).'/';
		if (is_dir( realpath('./data/uploads/'.$sid)) && is_dir($upload_dir)) {
			if ($dh = opendir($upload_dir)) {
				while (($filename = readdir($dh)) !== false) {
					if ($filename!="." && $filename!="..") {
						$finfo = mime_content_type($upload_dir.$filename);
						/*$finfo = @new finfo(FILEINFO_MIME);
						$fres = @$finfo->file($upload_dir.$filename);*/
						$exte = explode('/',$finfo);
						$path = pathinfo($upload_dir.$filename);
						$docfile = new docfile();
						$docfile->init_description();
						$docfile->setugm();
						$docfile->fields['id_module'] = $_SESSION['dims']['moduleid'];
						$docfile->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
						$docfile->fields['id_folder'] = -1;
						$docfile->fields['id_user_modify'] = $_SESSION['dims']['userid'];
						$docfile->tmpuploadedfile = $upload_dir.$filename;
						$docfile->fields['name'] = $filename;
						$docfile->fields['size'] = filesize($upload_dir.$filename);
						$docfile->fields['version'] = 0;
						$docfile->save();

						// lien dans la matrice
						$matrice = new matrix();
						$matrice->fields['id_country'] = $country;
						$matrice->fields['year'] = $datestart_year;
						$matrice->fields['month'] = $datestart_month;
						$matrice->fields['id_opportunity'] = $opp->fields['id_globalobject'];
						$matrice->fields['timestp_modify'] = dims_createtimestamp();
						$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
						$matrice->fields['id_doc'] = $docfile->fields['id_globalobject'];
						//$matrice->fields['id_action'] = $action->fields['id_globalobject'];
						$matrice->save();
					}
				}
				closedir($dh);
			}
			rmdir($upload_dir);
		}

		// on va se rajouter à tous ces contacts et entreprises que l'on vient d'enregistrer
		$u = new contact();
		$u->open($_SESSION['dims']['user']['id_contact']);

		$listCompanies=$u->getAllCompaniesLinked();
		$listcontacts=$u->getAllContactsLinked();

		if (!isset($_SESSION['desktopv2']['opportunity']['ct_added']))
			$_SESSION['desktopv2']['opportunity']['ct_added'] = array();
		if (!isset($_SESSION['desktopv2']['opportunity']['tiers_added']))
			$_SESSION['desktopv2']['opportunity']['tiers_added'] = array();

		// lien dans matrice avec les contacts
		$id_link = dims_load_securvalue('link',dims_const::_DIMS_NUM_INPUT, false, true);
		$action = new action();
		$action->fields['id_globalobject'] = 0;
		if ($id_link != '' && $id_link > 0)
			$action->open($id_link);

		require_once DIMS_APP_PATH."modules/system/desktopV2/include/class_desktopv2.php";
		require_once DIMS_APP_PATH.'modules/system/class_ct_link.php';
		require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';

		$lstTiers = $desktop->constructLstTiersFromCt(array(),$_SESSION['desktopv2']['opportunity']['ct_added'],$_SESSION['desktopv2']['opportunity']['tiers_added']);

		$lstContactsMatrice = array();

		foreach($lstTiers as $tiers){
			if (isset($tiers->contacts)){
				foreach($tiers->contacts as $ct){
					$matrice = new matrix();
					$matrice->fields['id_tiers'] = $tiers->fields['id_globalobject'];
					$matrice->fields['id_contact'] = $ct->fields['id_globalobject'];
					$matrice->fields['id_country'] = $country;
					$matrice->fields['year'] = $datestart_year;
					$matrice->fields['month'] = $datestart_month;
					$matrice->fields['id_opportunity'] = $opp->fields['id_globalobject'];
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
						$matrice->fields['id_country'] = $country;
						$matrice->fields['year'] = $datestart_year;
						$matrice->fields['month'] = $datestart_month;
						$matrice->fields['id_opportunity'] = $opp->fields['id_globalobject'];
						$matrice->fields['timestp_modify'] = dims_createtimestamp();
						$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
						$matrice->fields['id_action'] = $action->fields['id_globalobject'];
						$matrice->save();

						$matrice = new matrix();
						$matrice->fields['id_tiers'] = 0;
						$matrice->fields['id_contact'] = $ct->fields['id_globalobject'];
						$matrice->fields['id_contact2'] = $u->fields['id_globalobject'];
						$matrice->fields['id_country'] = $country;
						$matrice->fields['year'] = $datestart_year;
						$matrice->fields['month'] = $datestart_month;
						$matrice->fields['id_opportunity'] = $opp->fields['id_globalobject'];
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
					$matrice->fields['id_country'] = $country;
					$matrice->fields['year'] = $datestart_year;
					$matrice->fields['month'] = $datestart_month;
					$matrice->fields['id_opportunity'] = $opp->fields['id_globalobject'];
					$matrice->fields['timestp_modify'] = dims_createtimestamp();
					$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
					$matrice->fields['id_action'] = $action->fields['id_globalobject'];
					$matrice->save();

					$tiersct = new tiersct();
					$tiersct->fields['id_tiers'] = $tiers->fields['id'];
					$tiersct->fields['id_contact'] = $_SESSION['dims']['user']['id_contact'];
					$tiersct->fields['type_lien'] = 'Other';
					$tiersct->fields['link_level'] = 2;
					$tiersct->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
					$tiersct->fields['id_user'] = $_SESSION['dims']['userid'];
					$tiersct->fields['date_create'] = dims_createtimestamp();
					$tiersct->save();

				}
			}
		}


		$elem = array();
		$elem['id_ct'] = $u->fields['id_globalobject'];
		$elem['account'] = true;
		$lstContactsMatrice[] = $elem;
		foreach($lstContactsMatrice as $ct){
			if($ct['account'])
				foreach($lstContactsMatrice as $ct2){
					if ($ct['id_ct'] != $ct2['id_ct'] && $ct['account']){
						$matrice = new matrix();
						$matrice->fields['id_contact'] = $ct['id_ct'];
						$matrice->fields['id_contact2'] = $ct2['id_ct'];
						$matrice->fields['id_country'] = $country;
						$matrice->fields['year'] = $datestart_year;
						$matrice->fields['month'] = $datestart_month;
						$matrice->fields['id_opportunity'] = $opp->fields['id_globalobject'];
						$matrice->fields['timestp_modify'] = dims_createtimestamp();
						$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
						$matrice->fields['id_action'] = $action->fields['id_globalobject'];
						$matrice->save();
					}
				}
		}

		unset($_SESSION['desktopv2']['opportunity']['ct_added']);
		unset($_SESSION['desktopv2']['opportunity']['tiers_added']);

		$redi = dims_load_securvalue('redirection',dims_const::_DIMS_NUM_INPUT,true,true,true);
		switch($redi){
			default:
			case 0 :
				dims_redirect($dims->getScriptEnv().'?submenu='.dims_const_desktopv2::DESKTOP_V2_DESKTOP.'&force_desktop=1');
				break;
			case 1 :
				dims_redirect($dims->getScriptEnv().'?mode=opportunity&action=edit');
				break;
			case 2 :
				dims_redirect($dims->getScriptEnv().'?submenu='.dims_const_desktopv2::DESKTOP_V2_DESKTOP.'&mode=planning');
				break;
			case 3 :
				$planning_day = dims_load_securvalue('planning_day', dims_const::_DIMS_CHAR_INPUT, false, true);
				dims_redirect($dims->getScriptEnv().'?mode=opportunity&action=edit&from=planning&day='.$planning_day);
				break;
		}
		break;
	case 'update_opportunities':
		include DIMS_APP_PATH."modules/system/opportunity/update_opportunity.php";
		break;
}
