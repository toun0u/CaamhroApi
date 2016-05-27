<?php
require_once DIMS_APP_PATH.'modules/system/class_country.php';
require_once DIMS_APP_PATH.'modules/system/class_city.php';
switch($action){
	default:
	case 'new':
		$tiers = new tiers();
		$tiers->init_description();
		$tiers->setugm();

		$success = dims_load_securvalue('success', dims_const::_DIMS_CHAR_INPUT, true, true);//cette variable permet de savoir si l'enregistrement précédent (dans le cas du bouton enregistrer et continuer) s'est bien passé
		if( ! empty($success) && $success == 'ok'){//simulation d'un message flash
			$_SESSION['dims']['form_scratch']['companies']['success'] = true;
			dims_redirect($dims->getScriptEnv()."?mode=new_company&action=new");//j'aime moyennement le redirect
		}

		$tiers->setLightAttribute('back_op', $dims->getScriptEnv().'?submenu=1&force_desktop=1&mode=default');
		$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/form_from_scratch/companies/new.tpl.php');
		break;
	case 'save':
		$tiers = new tiers();
		$go_tiers = dims_load_securvalue('id_globalobject', dims_const::_DIMS_NUM_INPUT, true, true);
		if(empty($go_tiers)){
			$tiers->init_description();//pas de true sinon ça pète la date_create - faudra m'expliquer pourquoi y'a pas la colonne timestp_create ... ?
			$tiers->setugm();
			$new = true;
		}
		else{
			$tiers->openWithGB($go_tiers);
			$new = false;
		}
		$tiers->setvalues($_POST, 'tiers_');
		//gestion de la ville
		$city = new city();
		if (isset($tiers->fields['id_city']) && $tiers->fields['id_city'] > 0) {
			$city->open($tiers->fields['id_city']);
		}
		else {
			$city->init_description();
		}

		$tiers->fields['ville'] = $city->fields['label'];
		unset($tiers->fields['id_city']);

		//gestion du pays
		$country = new country();
		$country->open($tiers->fields['id_country']);
		$tiers->fields['pays'] = $country->fields['name'];

		//gestion des light attributes
		$back_op = dims_load_securvalue('back_op', 	dims_const::_DIMS_CHAR_INPUT, true, true);


		if($tiers->save()){
			// enregistrement du lien dans la matrice
			$matrice = new matrix();
			$matrice->addLink(array(
				'id_tiers' => $tiers->getId(),
				'id_tiers2' => $tiers->fields['id_tiers']));
			$matrice->addLink(array(
				'id_tiers' => $tiers->fields['id_tiers'],
				'id_tiers2' => $tiers->getId()));

			//gestion de la photo
			$id_ent = $tiers->getId();
			require_once(DIMS_APP_PATH.'modules/system/crm_public_ent_add_photo.php');

			if(empty($_POST['continue'])) dims_redirect($dims->getScriptEnv().'?submenu='._DESKTOP_V2_CONCEPTS.'&id='.$tiers->fields['id'].'&type='.dims_const::_SYSTEM_OBJECT_TIERS.'&init_filters=1&from=desktop');
			else dims_redirect($dims->getScriptEnv()."?mode=new_company&action=new&success=ok");
		}
		else{
			$tiers->setLightAttribute("back_op", $back_op);
			$tiers->setLightAttribute("global_error", $_SESSION['cste']['ERROR_THROWN']);
			$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/form_from_scratch/companies/new.tpl.php');
		}
		break;
}
?>
