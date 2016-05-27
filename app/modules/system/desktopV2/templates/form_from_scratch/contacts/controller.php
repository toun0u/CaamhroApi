<?php
require_once DIMS_APP_PATH.'modules/system/class_country.php';
require_once DIMS_APP_PATH.'modules/system/class_city.php';
require_once DIMS_APP_PATH.'modules/system/class_address.php';
require_once DIMS_APP_PATH.'modules/system/class_address_type.php';

switch($action){
	case 'save_adr':
		// on sauvegarde l'adresse
		ob_end_clean();
		$id_adr = dims_load_securvalue('id_adr', dims_const::_DIMS_NUM_INPUT, true, true);

		$contact = new contact();
		if (isset($_SESSION['dims']['newcontact']['id_contact']) && $_SESSION['dims']['newcontact']['id_contact']>0) {

			$contact->open($_SESSION['dims']['newcontact']['id_contact']);

			// on regarde si on a une adresse ou non
			$address = new address();

			if ($id_adr>0) {
				$address->open($id_adr);
			}
			else {
				$address->init_description();//pas de true sinon ça pète la date_create - faudra m'expliquer pourquoi y'a pas la colonne timestp_create ... ?
				$address->setugm();
			}

			$address->setvalues($_POST,"adr_");

			if ($address->fields['address']=='' || $address->fields['postalcode']=="" || $address->fields['id_city']=="") {
				$_SESSION['dims']['form_scratch']['contacts']['adr']=$address->fields;
				$_SESSION['dims']['form_scratch']['contacts']['success'] = false;
			}
			else {
				$_SESSION['dims']['form_scratch']['contacts']['success'] = true;
				$_SESSION['dims']['form_scratch']['contacts']['adr'] = array();
				// on calcule le pays
				$isNew = $address->isNew();
				$address->save();

				$type = dims_load_securvalue('type_address',dims_const::_DIMS_NUM_INPUT,true,true,true);
				if($isNew){
					$address->addLink($contact->get('id_globalobject'),$type);
				}else{
					$lk = $address->getLinkCt($contact->get('id_globalobject'));
					if(!is_null($lk)){
						$lk->set('id_type',$type);
						$lk->save();
					}else{
						$address->addLink($contact->get('id_globalobject'),$type);
					}
				}
			}
		}
		die();
		break;

	case 'add_address':
		ob_end_clean();
		if (isset($_SESSION['dims']['newcontact']['id_contact']) && $_SESSION['dims']['newcontact']['id_contact']>0) {

			$address = new address();
			$address->init_description();
			$address->setugm();
			if(isset($_POST['id_adr'])){
				$id = dims_load_securvalue('id_adr',dims_const::_DIMS_NUM_INPUT,true,true,true);
				if($id != '' && $id > 0)
					$address->open($id);
			}

			if (isset($_SESSION['dims']['form_scratch']['contacts']['success']) && !$_SESSION['dims']['form_scratch']['contacts']['success']) {
				$address->setLightAttribute("global_error", $_SESSION['cste']['ERROR_THROWN']);
			}
			$address->display(_DESKTOP_TPL_LOCAL_PATH.'/form_from_scratch/contacts/address.tpl.php');
		}else{
			$_SESSION['dims']['newcontact']['init_newcontact']=true;
			$_SESSION['dims']['newcontact']['id_contact']=0;

			dims_redirect('/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=new_contact&action=step1');
		}
		die();
		break;

	case 'list_addresses':
		ob_end_clean();
		$contact = new contact();
		if (isset($_SESSION['dims']['newcontact']['id_contact']) && $_SESSION['dims']['newcontact']['id_contact']>0) {
			$contact->open($_SESSION['dims']['newcontact']['id_contact']);

			$contact->setLightAttribute("list_adresses", $contact->getAllAdresses());
			$contact->display(_DESKTOP_TPL_LOCAL_PATH.'/form_from_scratch/contacts/new_step3_adresses.tpl.php');
		}
		die();
		break;

	case 'new':
		$_SESSION['dims']['newcontact']['init_newcontact']=true;
		$_SESSION['dims']['newcontact']['id_contact']=0;

		dims_redirect('/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=new_contact&action=step1');
		break;
	case 'step1':
		$contact = new contact();

		if (isset($_SESSION['dims']['newcontact']['id_contact']) && $_SESSION['dims']['newcontact']['id_contact']>0) {
			$contact->open($_SESSION['dims']['newcontact']['id_contact']);
		}
		else {
			$contact->init_description();
			$contact->setugm();
		}

		// on construit les elements dynamiques
		//$contact->display(_DESKTOP_TPL_LOCAL_PATH.'/form_from_scratch/contacts/new.tpl.php');
		$contact->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/contact/display_contact.tpl.php');
		break;
	case 'step2':
		$contact = new contact();

		if (isset($_SESSION['dims']['newcontact']['id_contact']) && $_SESSION['dims']['newcontact']['id_contact']>0) {
			$contact->open($_SESSION['dims']['newcontact']['id_contact']);
			// on construit les elements dynamiques
			$contact->setLightAttribute('back_op','./admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=new_contact&action=step1');
			$contact->display(_DESKTOP_TPL_LOCAL_PATH.'/form_from_scratch/contacts/new_step2.tpl.php');
		}
		else {
			dims_redirect('/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=new_contact&action=new');
		}
		break;
		break;
	case 'step3':

		//$success = dims_load_securvalue('success', dims_const::_DIMS_CHAR_INPUT, true, true);//cette variable permet de savoir si l'enregistrement précédent (dans le cas du bouton enregistrer et continuer) s'est bien passé
		//if( ! empty($success) && $success == 'ok'){//simulation d'un message flash
		//	$_SESSION['dims']['form_scratch']['contacts']['success'] = true;
		//}

		$contact = new contact();

		if (isset($_SESSION['dims']['newcontact']['id_contact']) && $_SESSION['dims']['newcontact']['id_contact']>0) {
			$contact->open($_SESSION['dims']['newcontact']['id_contact']);
			// on construit les elements dynamiques
			$contact->setLightAttribute('back_op','./admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=new_contact&action=step1');
			$contact->display(_DESKTOP_TPL_LOCAL_PATH.'/form_from_scratch/contacts/new_step3.tpl.php');
		}
		else {
			dims_redirect('/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=new_contact&action=new');
		}
		break;
	case 'save':
		$contact = new contact();
		$go_contact = dims_load_securvalue('id_globalobject', dims_const::_DIMS_NUM_INPUT, true, true);
		if(empty($go_contact)){
			$contact->init_description();//pas de true sinon ça pète la date_create - faudra m'expliquer pourquoi y'a pas la colonne timestp_create ... ?
			$contact->setugm();
			$new = true;
		}
		else{
			$contact->openWithGB($go_contact);
			$new = false;
		}
		$contact->setvalues($_POST, 'contact_');
		/*
		//gestion de la ville
		$city = new city();
		$city->open($contact->fields['id_city']);
		$contact->fields['city'] = $city->fields['label'];
		unset($contact->fields['id_city']);

		//gestion du pays
		$country = new country();
		$country->open($contact->fields['id_country']);
		$contact->fields['country'] = $country->fields['name'];
		*/

		//gestion des light attributes
		$back_op = dims_load_securvalue('back_op', 	dims_const::_DIMS_CHAR_INPUT, true, true);

		if($contact->save()){
			//gestion de la photo
			$id_contact = $contact->getId();
			require_once(DIMS_APP_PATH.'modules/system/crm_contact_add_photo.php');

			// gestion de l'entrprise
			/*$tiers_id = dims_load_securvalue('tiers_id', dims_const::_DIMS_NUM_INPUT, true, true);
			if ($tiers_id > 0) {
				$contact->linkToCompany($tiers_id);
			}*/

			//if(empty($_POST['continue'])) dims_redirect($dims->getScriptEnv().'?submenu='._DESKTOP_V2_CONCEPTS.'&id='.$contact->fields['id'].'&type='.dims_const::_SYSTEM_OBJECT_CONTACT.'&init_filters=1&from=desktop');
			//else
			require_once(DIMS_APP_PATH.'modules/system/class_tag.php');
			require_once(DIMS_APP_PATH.'modules/system/class_tag_globalobject.php');

			$tags = dims_load_securvalue('tags', dims_const::_DIMS_NUM_INPUT, true, true);
			$myTags = $contact->getMyTags();
			foreach($myTags as $t){
				if(in_array($t->get('id'), $tags)){
					unset($tags[array_search($t->get('id'), $tags)]);
				}else{
					$lk = new tag_globalobject();
					$lk->openWithCouple($t->get('id'),$contact->get('id_globalobject'));
					if(!$lk->isNew())
						$lk->delete();
				}
			}
			foreach($tags as $t){
				$lk = new tag_globalobject();
				$lk->init_description();
				$lk->set('id_tag',$t);
				$lk->set('id_globalobject',$contact->get('id_globalobject'));
				$lk->set('timestp_modify',dims_createtimestamp());
				$lk->save();
			}

			// on affecte le contact nouvellemenr cree
			$_SESSION['dims']['newcontact']['id_contact']=$id_contact;
			$_SESSION['dims']['form_scratch']['contacts']['success'] = true;
			dims_redirect($dims->getScriptEnv()."?mode=new_contact&action=step3");
		}
		else{
			$contact->setLightAttribute("back_op", $back_op);
			$contact->setLightAttribute("global_error", $_SESSION['cste']['ERROR_THROWN']);
			$contact->display(_DESKTOP_TPL_LOCAL_PATH.'/form_from_scratch/contacts/new.tpl.php');
		}
		break;
	case 'save_tiers':
		$contact = new contact();

		if (isset($_SESSION['dims']['newcontact']['id_contact']) && $_SESSION['dims']['newcontact']['id_contact']>0) {
			$contact->open($_SESSION['dims']['newcontact']['id_contact']);

			$idTiers = dims_load_securvalue('id_tiers',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$tiers = new tiers();
			if($idTiers != '' && $idTiers > 0)
				$tiers->open($idTiers);
			if($tiers->isNew()){
				$tiers->init_description();
				$tiers->setugm();
			}
			$tiers->setvalues($_POST,'tiers_');
			$tiers->save();

			$contact->linkToCompany($tiers->get('id'));
			$_SESSION['dims']['newcontact']['id_tiers'] = $tiers->get('id');

			dims_redirect('/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=new_contact&action=step3');
		}
		else {
			dims_redirect('/admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=new_contact&action=new');
		}
		break;
}
?>
