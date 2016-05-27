<?php
include_once DIMS_APP_PATH.'/modules/catalogue/include/class_config.php';
include_once DIMS_APP_PATH.'/modules/catalogue/include/class_param.php';

$view = view::getInstance();

// infos contexuelles
$view->assign('a', $a);

$view->setLayout('layouts/param_layout.tpl.php');
$view->render('params/sidebar.tpl.php', 'params_sidebar');

switch ($a) {
	// Identité de votre société
	case 'identity':
		include_once DIMS_APP_PATH.'modules/system/class_country.php';

		$work = new workspace();
		$work->open($_SESSION['dims']['workspaceid']);
		$tiers = $work->getTiers();
		$view->assign('tiers',$tiers);
		$lst = country::getAllCountries(false);
		$countries = array();
		foreach($lst as $ct){
			$countries[$ct->get('id')] = $ct->get('printable_name');
		}
		$view->assign('lst_country',$countries);

		$view->render('params/identity.tpl.php');
		break;
	case 'save_identity':
		$work = new workspace();
		$work->open($_SESSION['dims']['workspaceid']);
		$tiers = $work->getTiers();
		$tiers->setvalues($_POST,'tiers_');
		$tiers->save();
		$work->setTiers($tiers);

		$id_ent = $tiers->get('id');
		include_once(DIMS_APP_PATH.'modules/system/crm_public_ent_add_photo.php');

		dims_redirect(get_path('params', 'identity'));
		break;
	case 'dellogo':
		$work = new workspace();
		$work->open($_SESSION['dims']['workspaceid']);
		$tiers = $work->getTiers();
		$tiers->removePhoto();
		dims_redirect(get_path('params', 'identity'));
		break;

	// Données bancaire (RIB)
	case 'rib':
		$work = new workspace();
		$work->open($_SESSION['dims']['workspaceid']);
		$tiers = $work->getTiers();
		$view->assign('tiers',$tiers);

		$isoCountry = "";
		if($tiers->fields['id_country'] != '' && $tiers->fields['id_country'] > 0){
			include_once DIMS_APP_PATH.'modules/system/class_country.php';
			$country = new country();
			$country->open($tiers->fields['id_country']);
			$isoCountry = $country->fields['iso'];
		}
		$view->assign('isoCountry',$isoCountry);

		$view->render('params/rib.tpl.php');
		break;
	case 'save_rib':
		$work = new workspace();
		$work->open($_SESSION['dims']['workspaceid']);
		$tiers = $work->getTiers();
		$tiers->setvalues($_POST,'tiers_');
		$tiers->save();
		dims_redirect(get_path('params', 'rib'));
		break;

	// Configuration du catalogue
	case 'edit':
		$config = cata_config::get($_SESSION['dims']['moduleid']);
		$view->assign('config', $config);
		$view->render('params/edit.tpl.php');
		break;
	case 'save':
		foreach ($_POST as $key => $value) {
			$$key = dims_load_securvalue($key, dims_const::_DIMS_CHAR_INPUT, false, true, true);

			$param = new cata_param();

			if ($param->getByName($key)) {
				$param->setValue($$key);
				$param->save();
			}
		}

		// Insertion des fichiers dans la GED
		if (isset($_FILES) && isset($_FILES['cata_documents_template']) && !$_FILES['cata_documents_template']['error']) {
			// on attache le document au dossier
			$file = $_FILES['cata_documents_template'];

			$doc = new docfile();
			$doc->fields['name'] 			= $file['name'];
			$doc->tmpuploadedfile 			= $file['tmp_name'];
			$doc->fields['size'] 			= $file['size'];
			$doc->fields['id_folder'] 		= 0; // racine
			$doc->setugm();
			$doc->save();

			$param = new cata_param();
			if ($param->getByName('cata_documents_template')) {
				$param->setValue($doc->get('id'));
				$param->save();
			}
		}

		dims_redirect(get_path('params', 'edit'));
		break;

	// Espaces clients
	case 'espace':
		$view->assign('espace_client',cata_param::initEspacesClients());
		$view->render('params/espace_client.tpl.php');
		break;
	case 'espace_save':
		$lstParam = cata_param::initEspacesClients();
		foreach($_POST as $key => $val){
			if(isset($lstParam[$key])){
				$lstParam[$key]->setValue(dims_load_securvalue($key,dims_const::_DIMS_NUM_INPUT,true,true,true));
				$lstParam[$key]->save();
			}
		}
		dims_redirect(get_path('params', 'espace'));
		break;

	// Notifications email
	case 'notif':
		$view->assign('notif_mail', cata_param::initNotifMail());
		$view->render('params/notifications.tpl.php');
		break;
	case 'save_notif':
		$lstParam = cata_param::initNotifMail();
		foreach($_POST as $key => $val){
			$key = dims_sql_filter($key);
			$val = dims_sql_filter($val);
			if(isset($lstParam[$key])){
				$lstParam[$key]->setValue(dims_load_securvalue($key,dims_const::_DIMS_CHAR_INPUT,true,true,true));
				$lstParam[$key]->save();
			}
		}
		dims_redirect(get_path('params', 'notif'));
		break;

	// Tarifs / Gestion de la vente
	case 'tarif':
		$view->assign('tarifs',cata_param::initTarifGestVente());
		$view->render('params/tarifs.tpl.php');
		break;
	case 'save_tarif':
		$lstParam = cata_param::initTarifGestVente();
		foreach($_POST as $key => $val){
			if(isset($lstParam[$key])){
				$lstParam[$key]->setValue(dims_load_securvalue($key,dims_const::_DIMS_CHAR_INPUT,true,true,true));
				$lstParam[$key]->save();
			}
		}

		// Mise à jour du tarif du transporteur
		if (isset($_FILES) && isset($_FILES['tarif_transporteur']) && !$_FILES['tarif_transporteur']['error']) {
			require_once DIMS_APP_PATH.'modules/catalogue/include/class_carrier.php';
			require_once DIMS_APP_PATH.'modules/catalogue/include/class_carrier_rate.php';

			// Ouverture du transporteur
			// Pour le moement, on en n'a qu'un en dur
			$carrier = cata_carrier::find_by(array('id' => 1), null, 1);
			if (!is_null($carrier)) {
				$carrier->setRateFromFilename($_FILES['tarif_transporteur']['tmp_name']);
			}
		}

		dims_redirect(get_path('params', 'tarif'));
		break;

	// Filtres & champs libres
	case 'champs':
		include_once DIMS_APP_PATH."modules/catalogue/include/class_champ.php";
		$view->assign('lst_champs',cata_champ::getAll());
		$view->render('params/champs.tpl.php');
		break;
	case 'editchamp':
		include_once DIMS_APP_PATH."modules/catalogue/include/class_champ.php";
		include_once DIMS_APP_PATH."modules/system/class_category.php";
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		$elem = new cata_champ();
		if($id != '' && $id > 0)
			$elem->open($id);
		else{
			$elem->init_description();
		}
		$view->assign('languages',cata_param::getActiveLang());
		$rootCat = cata_champ::getRootCategory();
		$lstCateg = $rootCat->getDirectChilds(); // on prend uniquement le 1er niveau
		$view->assign('lst_cat',$lstCateg);
		$myCateg = current($elem->searchGbLink(category::MY_GLOBALOBJECT_CODE));
		$view->assign('my_categ',$myCateg);
		$view->assign('add_categ',$dims->getScriptEnv().'?c=params&a=addCategChp');
		$view->assign('action_path',get_path('params', 'savechamp'));
		$view->assign('back_path',get_path('params', 'champs'));
		$view->assign('champ',$elem);
		$view->render('params/edit_champs.tpl.php');
		break;
	case 'savechamp':
		include_once DIMS_APP_PATH."modules/catalogue/include/class_champ.php";
		include_once DIMS_APP_PATH."modules/system/class_category.php";
		$id = dims_load_securvalue('id_chp',dims_const::_DIMS_NUM_INPUT,true,true);
		$elem = new cata_champ();
		if($id != '' && $id > 0)
			$elem->open($id);
		else{
			$elem->init_description();
			$elem->setugm();
		}
		$elem->fields['fiche'] = 0;
		$elem->fields['filtre'] = 0;
		$elem->fields['allow_unique_option'] = 0;
		$elem->fields['global_filter'] = 0;
		$elem->fields['sub_article'] = 0;
		$elem->fields['libelle'] = dims_load_securvalue("libelle_".$elem->getDefaultLang(),dims_const::_DIMS_CHAR_INPUT,true,true);
		$elem->setvalues($_POST, 'chp_');
		$elem->save();

		// Catégorie
		$goElem = $elem->getMyGlobalObject();
		$goElem->deleteLink($goElem->searchLink(category::MY_GLOBALOBJECT_CODE));
		$goCat = dims_load_securvalue('categorie',dims_const::_DIMS_NUM_INPUT,true,true);
		if($goCat != "" && $goCat > 0){
			$goElem->addLink($goCat);
		}

		foreach(cata_param::getActiveLang() as $id => $lg){
			if(isset($_POST["libelle_$id"])){
				$elem->addLibelle($id,dims_load_securvalue("libelle_$id",dims_const::_DIMS_CHAR_INPUT,true,true));
			}

			if(isset($_POST["libelle_$id"]) && isset($_POST["values_$id"])){
				$values = dims_load_securvalue("values_$id",dims_const::_DIMS_CHAR_INPUT,true,true);
				$elem->addValues($id,explode("\r\n",$values));
			}

			// Global filter
			if ($elem->fields['global_filter']) {
				if(isset($_POST["global_filter_label_$id"])){
					$elem->addGlobalFilterLabel($id,dims_load_securvalue("global_filter_label_$id",dims_const::_DIMS_CHAR_INPUT,true,true));
				}
			}
		}
		dims_redirect(get_path('params', 'champs'));
		break;
	case 'deletechamp':
		include_once DIMS_APP_PATH."modules/catalogue/include/class_champ.php";
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		$elem = new cata_champ();
		if($id != '' && $id > 0) {
			$elem->open($id);
			$elem->delete();
		}
		dims_redirect(get_path('params', 'champs'));
		break;
	case 'addCategChp':
		ob_clean();
		$return = array('selected' => 0, 'lst' => array(array('id' => 0, 'label' => ucfirst(dims_constant::getVal('NO_ELEMENT_FEMININ')))));
		$val = trim(dims_load_securvalue('val',dims_const::_DIMS_CHAR_INPUT,true,true,true));
		include_once DIMS_APP_PATH."modules/catalogue/include/class_champ.php";
		$rootCat = cata_champ::getRootCategory();
		if($val != ''){
			$sub = $rootCat->addSubCategory($val,category::DIMS_CATEGORY_PUPLIC);
			$return['selected'] = $sub->fields['id_globalobject'];
		}
		foreach($rootCat->getDirectChilds() as $cat){
			$return['lst'][] = array('id' => $cat->fields['id_globalobject'], 'label' => $cat->getLabel());
		}
		echo json_encode($return);
		die();
		break;

	case 'tva_index':
		#Récupération des taux de tva existants
		include_once DIMS_APP_PATH."modules/catalogue/include/class_tva.php";
		$view->assign('taux', tva::all());
		$view->render('params/tva_index.tpl.php');
		break;

	case 'tva_edit':
		$id_tva 	= dims_load_securvalue('id_tva',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$id_pays 	= dims_load_securvalue('id_pays',dims_const::_DIMS_NUM_INPUT,true,true,true);
		include_once DIMS_APP_PATH."modules/catalogue/include/class_tva.php";
		$tva = new tva();
		if( ! empty($id_tva) && ! empty($id_pays)){
			$tva->open($id_tva, $id_pays);
		}
		if($tva->isNew()){
			$tva->init_description(true);
			$tva->setugm();
		}
		$view->assign('tva', $tva);

		#Récupération des pays de la base de données
		$view->assign('countries', get_formated_countries());

		$view->render('params/tva_edit.tpl.php');
		break;

	case 'tva_save':
		include_once DIMS_APP_PATH."modules/catalogue/include/class_tva.php";
		$init_id_tva = $init_id_pays = 0;
		$init_id_tva 	= dims_load_securvalue('init_id_tva',dims_const::_DIMS_NUM_INPUT,true,true,true, $init_id_tva);
		$init_id_pays 	= dims_load_securvalue('init_id_pays',dims_const::_DIMS_NUM_INPUT,true,true,true, $init_id_pays);
		$tva = new tva();
		if( ! empty($init_id_tva) && ! empty($init_id_pays)){
			$tva->open($init_id_tva, $init_id_pays);
		}
		if( $tva->isNew() ){
			$new = true;
			$tva->init_description(true);
			$tva->setugm();
		}
		else $new = false;

		$error = false;

		$new_id_tva 	= dims_load_securvalue('tva_id_tva',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$new_id_pays 	= dims_load_securvalue('tva_id_pays',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$new_taux		= dims_load_securvalue('tva_tx_tva',dims_const::_DIMS_NUM_INPUT,true,true,true);

		if( ! empty($new_id_tva) && ! empty($new_id_pays) && $new_id_pays != 'dims_nan' && ! empty($new_taux)){
			#il faut qu'on contrôle si on est pas en train de piquer le code TVA + code Pays à un taux déjà existant
			if ($init_id_tva != $new_id_tva || $init_id_pays != $new_id_pays){
				$existing_taux = new tva();
				$existing_taux->open($new_id_tva, $new_id_pays);
				if( ! $existing_taux->isNew() ){
					$error = true;
					$message = dims_constant::getVal('CODE_ALREADY_TAKEN');
				}
			}
			if( ! $error ){
				if($new_taux >= 0){
					$tva->setvalues($_POST, 'tva_');
					if( $tva->save() ){
						if($new) $view->flash(dims_constant::getVal('TAXE_HAS_BEEN_ADDED'), 'success');
						else $view->flash(dims_constant::getVal('TAXE_HAS_BEEN_UPDATED'), 'success');
						if(empty($_POST['continue'])) dims_redirect(get_path('params', 'tva_index'));
						else dims_redirect(get_path('params', 'tva_edit'));
					}
					else{
						$error = true;
						$message = dims_constant::getVal('ERROR_THROWN');
					}
				}
				else{
					$error = true;
					$message = dims_constant::getVal('TVA_MUST_BE_POSITIVE');
				}
			}

		}
		else{
			$error = true;
			$message = dims_constant::getVal('CHECK_MANDATORY_FIELDS');
		}

		if($error){
			$tva->setLightAttribute('global_error', $message);
			$view->assign('tva', $tva);
			$view->assign('countries', get_formated_countries());
			$view->render('params/tva_edit.tpl.php');
		}
		break;

	case 'tva_delete':
		$id_tva 	= dims_load_securvalue('id_tva',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$id_pays 	= dims_load_securvalue('id_pays',dims_const::_DIMS_NUM_INPUT,true,true,true);
		include_once DIMS_APP_PATH."modules/catalogue/include/class_tva.php";
		$tva = new tva();
		if( ! empty($id_tva) && ! empty($id_pays)){
			$tva->open($id_tva, $id_pays);
			if( ! $tva->isNew() ){
				$tva->delete();#Note : on s'est posé la question de savoir s'il fallait proposer un code tva de substitution pour les articles qui avaient ce code
							   #mais c'est inutile, le taux de TVA par défaut s'appliquera dans ce cas
				$view->flash(dims_constant::getVal('TAXE_DELETED'), 'success');
				dims_redirect(get_path('params', 'tva_index'));
			}
			else $error = true;
		}
		else $error = true;

		if( $error ){
			dims_redirect(get_path('params', 'tva_index'));
		}
		break;
	// Langues
	case 'lg_index':
		include_once DIMS_APP_PATH."modules/system/class_lang.php";
		$view->assign('active_lg',cata_param::isActiveParamLang());

		$view->assign('default_lg',cata_param::getDefaultLang());

		$view->assign('langues',lang::all());

		$view->assign('active_langues',cata_param::getActiveLang());

		$view->render('params/langue_index.tpl.php');
		break;
	case 'active_lg':
		cata_param::setActiveParamLangVal(dims_load_securvalue('cata_active_lg',dims_const::_DIMS_NUM_INPUT,true,true,true));
		dims_redirect(get_path('params', 'lg_index'));
		break;
	case 'default_lg':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		if($id != '' && $id > 0){
			cata_param::setActiveLang($id);
			cata_param::setDefaultLang($id);
		}
		dims_redirect(get_path('params', 'lg_index'));
		break;
	case 'switch_lg':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		if($id != '' && $id > 0){
			if(cata_param::isActiveLang($id))
				cata_param::setUnactiveLang($id);
			else
				cata_param::setActiveLang($id);
		}
		dims_redirect(get_path('params', 'lg_index'));
		break;
	case 'lg_add':
		include_once DIMS_APP_PATH."modules/system/class_lang.php";
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$lang = new lang();
		if($id != '' && $id > 0){
			$lang->open($id);
		}else{
			$lang->init_description();
		}
		$view->assign('lang',$lang);
		$view->assign('langues',lang::all());
		$view->render('params/langue_edit.tpl.php');
		break;
	case 'lg_save':
		include_once DIMS_APP_PATH."modules/system/class_lang.php";
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$lang = new lang();
		if($id != '' && $id > 0){
			$lang->open($id);
		}else{
			$lang->init_description();
		}
		$lang->setvalues($_POST,'lg_');
		$lang->save();
		cata_param::setActiveLang($lang->get('id'));
		dims_redirect(get_path('params', 'lg_index'));
		break;
	// GESTION DES PARAMS DES COMPTES CLIENTS
	case 'compte':
		include_once DIMS_APP_PATH.'modules/catalogue/include/class_const.php';
		$view->assign('param_compte',cata_param::initComptesClients());
		$view->render('params/compte_edit.tpl.php');
		break;
	case 'save_compte':
		$lstParam = cata_param::initComptesClients();
		$lstParam['is_user_without_valid']->setValue(0);
		$lstParam['is_user_without_valid']->save();
		$lstParam['is_user_with_valid']->setValue(0);
		$lstParam['is_user_with_valid']->save();
		$lstParam['is_service_manager']->setValue(0);
		$lstParam['is_service_manager']->save();
		$lstParam['is_purchasing_manager']->setValue(0);
		$lstParam['is_purchasing_manager']->save();
		$lstParam['is_account_admin']->setValue(0);
		$lstParam['is_account_admin']->save();
		foreach($_POST as $key => $val){
			if(isset($lstParam[$key])){
				$lstParam[$key]->setValue(dims_load_securvalue($key,dims_const::_DIMS_CHAR_INPUT,true,true,true));
				$lstParam[$key]->save();
			}
		}
		dims_redirect(get_path('params', 'compte'));
		break;
	case 'formexport':
		$params = exportparams::openbymodule($_SESSION['dims']['moduleid']);

		$view->assign('params', $params);

		$view->render('params/export.tpl.php');
		break;
	case 'saveexport':
		$params = exportparams::openbymodule($_SESSION['dims']['moduleid']);

		$colseparator		= dims_load_securvalue('colseparator', dims_const::_DIMS_NUM_INPUT, true, true, true);
		$colseparator_other	= dims_load_securvalue('colseparator_other', dims_const::_DIMS_CHAR_INPUT, true, true, true);
		$separatorendline	= dims_load_securvalue('separatorendline', dims_const::_DIMS_NUM_INPUT, true, true, true);
		$repeatheaders		= dims_load_securvalue('repeatheaders', dims_const::_DIMS_NUM_INPUT, true, true, true);
		$selectedheaders	= dims_load_securvalue('selectedheaders', dims_const::_DIMS_NUM_INPUT, true, true, true);
		$selectedrows		= dims_load_securvalue('selectedrows', dims_const::_DIMS_NUM_INPUT, true, true, true);

		$params->fields['colseparator']			= $colseparator == exportparams::COLSEP_TAB ? exportparams::COLSEP_TAB : exportparams::COLSEP_OTHER;
		$params->fields['colseparator_other']	= $colseparator_other;
		$params->fields['separatorendline']		= $separatorendline == exportparams::ENDLINE_SEP_NO ? exportparams::ENDLINE_SEP_NO : exportparams::ENDLINE_SEP_YES;
		$params->fields['repeatheaders']		= $repeatheaders;

		$params->setheaderfields($selectedheaders);
		$params->setrowfields($selectedrows);

		$params->save();

		dims_redirect(get_path('params', 'formexport'));
		break;
	// Gestion des modes de règlement
	case 'payment_means':
		include_once DIMS_APP_PATH.'modules/catalogue/include/class_moyen_paiement.php';
		$view->assign('a_mp', moyen_paiement::getAll());
		$view->render('params/payment_means_index.tpl.php');
		break;
	case 'payment_mean_switch_active':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, false);
		if ($id) {
			include_once DIMS_APP_PATH.'modules/catalogue/include/class_moyen_paiement.php';
			$mp = new moyen_paiement();
			if ($mp->open($id)) {
				$mp->switchActive();
				$mp->save();
			}
		}
		dims_redirect(get_path('params', 'payment_means'));
		break;
	case 'payment_mean_edit':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, false);
		if ($id) {
			include_once DIMS_APP_PATH.'modules/catalogue/include/class_moyen_paiement.php';
			$mp = new moyen_paiement();
			if ($mp->open($id)) {
				$view->assign('mp', $mp);
				$view->render('params/payment_mean_edit.tpl.php');
			}
		}
		break;
	case 'payment_mean_save':
		$id 			= dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, false, true);
		$description 	= dims_load_securvalue('description', dims_const::_DIMS_CHAR_INPUT, false, true, true);

		if ($id) {
			include_once DIMS_APP_PATH.'modules/catalogue/include/class_moyen_paiement.php';
			$mp = new moyen_paiement();
			if ($mp->open($id)) {
				$mp->setDescription($description);
				$mp->save();
			}
		}
		dims_redirect(get_path('params', 'payment_means'));
		break;

	// Gestion des templates de sélections pour les familles
	case 'selections_templates':
		require DIMS_APP_PATH.'modules/catalogue/include/class_selection_template.php';

		$sa = dims_load_securvalue('sa',dims_const::_DIMS_CHAR_INPUT,true,true);
		switch ($sa) {
			default:
			case 'index':
				$view->assign('templates', cata_selection_template::getAll($_SESSION['dims']['moduleid']));
				$view->render('params/selections_templates_index.tpl.php');
				break;
			case 'edit':
				$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, false);
				$template = new cata_selection_template();
				if ($id > 0) {
					$template->open($id);
				}
				else {
					$template->init_description();
				}

				$view->assign('languages', cata_param::getActiveLang());
				$view->assign('template', $template);
				$view->assign('translations', $template->getTranslations());
				$view->render('params/selections_templates_edit.tpl.php');
				break;
			case 'save':
				$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, false, true);

				$first_lang = true;
				$languages = cata_param::getActiveLang();
				foreach ($languages as $id_lang => $lang) {
					$template = new cata_selection_template();

					if ($id > 0) {
						$template->open($id, $id_lang);
					}
					else {
						$template->init_description();
						$template->setugm();
						$template->setLang($id_lang);
					}

					if ($first_lang) {
						// Fichier de template
						$file = $_FILES['template_doc'];
						if ($file['name'] != '' && !$file['error']) {
							move_uploaded_file($file['tmp_name'], DIMS_ROOT_PATH.'tmp/'.$file['name']);

							$doc = $template->getDoc();
							if (is_null($doc)) {
								$doc = new docfile();
								$doc->init_description();
								$doc->fields['id_folder'] = 0;
								$doc->setugm();
							}

							$doc->fields['name'] = $file['name'];
							$doc->tmpuploadedfile = DIMS_ROOT_PATH.'tmp/'.$file['name'];
							$doc->fields['size'] = $file['size'];
							$doc->save();
						}
						$first_lang = false;
					}

					$template->setTemplateTitle(dims_load_securvalue('title_'.$id_lang, dims_const::_DIMS_CHAR_INPUT, false, true));
					if (isset($doc)) {
						$template->setDoc($doc);
					}
					$template->save();
				}

				if (isset($_POST['continue'])) {
					dims_redirect(get_path('params', 'selections_templates', array('sa' => 'edit')));
				}
				else {
					dims_redirect(get_path('params', 'selections_templates'));
				}
				break;
			case 'delete':
				$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, false);
				if ($id > 0) {
					$template = new cata_selection_template();
					$template->open($id);
					$template->delete();
				}
				dims_redirect(get_path('params', 'selections_templates'));
				break;
		}
		break;
	// Gestion des revendeurs
	case 'resellers':
		require DIMS_APP_PATH.'modules/catalogue/include/class_reseller.php';

		$sa = dims_load_securvalue('sa',dims_const::_DIMS_CHAR_INPUT,true,true);
		switch ($sa) {
			default:
			case 'index':
				$resellers = cata_reseller::find_by(array('id_module' => $_SESSION['dims']['moduleid']), 'ORDER BY name');
				$view->assign('resellers', $resellers);
				$view->render('params/resellers_index.tpl.php');
				break;
			case 'edit':
				$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, false);
				$reseller = new cata_reseller();
				if ($id > 0) {
					$reseller->open($id);
				}
				else {
					$reseller->init_description();
				}

				// chargement de la liste des pays
				$a_countries = country::getAllCountries();
				$a_countries_list = array();
				foreach ($a_countries as $country) {
					$a_countries_list[$country->get('id')] = $country->getLabel();
				}

				$view->assign('a_countries', $a_countries_list);
				$view->assign('reseller', $reseller);
				$view->render('params/resellers_edit.tpl');
				break;
			case 'save':
				$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, false, true);

				$reseller = new cata_reseller();

				if ($id > 0) {
					$reseller->open($id);
				}
				else {
					$reseller->init_description();
					$reseller->setugm();
				}

				$reseller->setName(dims_load_securvalue('name', dims_const::_DIMS_CHAR_INPUT, false, true, true));
				$reseller->setAddress1(dims_load_securvalue('address1', dims_const::_DIMS_CHAR_INPUT, false, true, true));
				$reseller->setAddress2(dims_load_securvalue('address2', dims_const::_DIMS_CHAR_INPUT, false, true, true));
				$reseller->setAddress3(dims_load_securvalue('address3', dims_const::_DIMS_CHAR_INPUT, false, true, true));
				$reseller->setPostalCode(dims_load_securvalue('postal_code', dims_const::_DIMS_CHAR_INPUT, false, true, true));
				$reseller->setCity(dims_load_securvalue('city', dims_const::_DIMS_CHAR_INPUT, false, true, true));
				$reseller->setCountry(dims_load_securvalue('id_country', dims_const::_DIMS_NUM_INPUT, false, true, true));
				$reseller->setWebSite(dims_load_securvalue('website', dims_const::_DIMS_CHAR_INPUT, false, true, true));
				$reseller->setEmail(dims_load_securvalue('email', dims_const::_DIMS_CHAR_INPUT, false, true, true));
				$reseller->setPhone(dims_load_securvalue('phone', dims_const::_DIMS_CHAR_INPUT, false, true, true));
				$reseller->setFax(dims_load_securvalue('fax', dims_const::_DIMS_CHAR_INPUT, false, true, true));

				// Fichier du logo
				$file = $_FILES['logo'];
				if ($file['name'] != '' && !$file['error']) {
					move_uploaded_file($file['tmp_name'], DIMS_ROOT_PATH.'tmp/'.$file['name']);

					$logo = $reseller->getLogo();
					if (is_null($logo)) {
						$logo = new docfile();
						$logo->init_description();
						$logo->fields['id_folder'] = 0;
						$logo->setugm();
					}

					$logo->fields['name'] = $file['name'];
					$logo->tmpuploadedfile = DIMS_ROOT_PATH.'tmp/'.$file['name'];
					$logo->fields['size'] = $file['size'];
					$logo->save();
				}
				if (isset($logo)) {
					$reseller->setLogo($logo);
				}

				$reseller->save();

				if (isset($_POST['continue'])) {
					dims_redirect(get_path('params', 'resellers', array('sa' => 'edit')));
				}
				else {
					dims_redirect(get_path('params', 'resellers'));
				}
				break;
			case 'delete':
				$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, false);
				if ($id > 0) {
					$reseller = new cata_reseller();
					$reseller->open($id);
					$reseller->delete();
				}
				dims_redirect(get_path('params', 'resellers'));
				break;
		}
		break;
}
