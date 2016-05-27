<?php
require_once DIMS_APP_PATH."/modules/system/desktopV2/include/global.php";
require_once DIMS_APP_PATH."/modules/system/desktopV2/include/class_module_desktopv2.php";

$moduledesktopv2 = new module_desktopv2();

$desktop = new desktopv2();
$submenu=dims_load_securvalue('submenu',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['submainmenu'],$_SESSION['dims']['submainmenu']);
if (!isset($_SESSION['desktopv2']['mode'])) $_SESSION['desktopv2']['mode'] = 'default';
$mode = dims_load_securvalue('mode',dims_const::_DIMS_CHAR_INPUT,true,true,true,$_SESSION['desktopv2']['mode']);

$search = dims_load_securvalue('search',dims_const::_DIMS_CHAR_INPUT,true,true,true);
$force_desktop = dims_load_securvalue('force_desktop',dims_const::_DIMS_CHAR_INPUT,true,true,true);
$init_filters = dims_load_securvalue('init_filters', dims_const::_DIMS_NUM_INPUT, true, true);

if(!empty($force_desktop)){
	unset($_SESSION['dims']['search']['current_search']);
	unset($_SESSION['dims']['modsearch']['my_real_expression']);
	unset($_SESSION['desktop']['search']['tags']);

	unset($_SESSION['dims']['advanced_search']['filters']['count']);
	unset($_SESSION['dims']['advanced_search']['filters']);

	$_SESSION['dims']['advanced_search']['keep_opened'] = true;

	$_SESSION['dims']['search']['search_starting_by_tag'] = false;

	// forcage de l'initialisation des filtres
	$init_filters = true;
}

// initialisation des filtres
if ($init_filters) {
	$_SESSION['desktopv2']['concepts']['filters']['events']			= array();
	$_SESSION['desktopv2']['concepts']['filters']['activities']		= array();
	$_SESSION['desktopv2']['concepts']['filters']['opportunities']	= array();
	$_SESSION['desktopv2']['concepts']['filters']['companies']		= array();
	$_SESSION['desktopv2']['concepts']['filters']['contacts']		= array();
	$_SESSION['desktopv2']['concepts']['filters']['documents']		= array();
	$_SESSION['desktopv2']['concepts']['filters']['dossiers']		= array();
	$_SESSION['desktopv2']['concepts']['filters']['suivis']			= array();
	$_SESSION['desktopv2']['concepts']['filters']['years']			= array();
	$_SESSION['desktopv2']['concepts']['filters']['countries']		= array();
	$_SESSION['desktopv2']['concepts']['filters']['count']			= 0;
	$_SESSION['desktopv2']['concepts']['filters']['stack']			= array();
	$_SESSION['desktopv2']['concepts']['filters']['pivot']			= null;
	$_SESSION['desktop']['concept']['tags']							= array();

	unset($_SESSION['desktopv2']['concepts']['rech_type']);
	unset($_SESSION['desktopv2']['concepts']['contact_search']);
	unset($_SESSION['desktopv2']['concepts']['comment_search']);
	unset($_SESSION['desktopv2']['concepts']['document_search']);
	unset($_SESSION['desktopv2']['concepts']['mission_search']);
}

$action = dims_load_securvalue('action',dims_const::_DIMS_CHAR_INPUT,true,true,true);

if ($action != ''){
	switch($action){
		case 'save_comment_concepts':
			require_once DIMS_APP_PATH.'modules/system/class_annotation.php';
			if (isset($_SESSION['desktopv2']['concepts']['sel_type']) && $_SESSION['desktopv2']['concepts']['sel_type'] > 0 &&
				isset($_SESSION['desktopv2']['concepts']['sel_id']) && $_SESSION['desktopv2']['concepts']['sel_id'] > 0){
				require_once DIMS_APP_PATH.'modules/system/class_annotation.php';
				$ann = new annotation();
				$ann->init_description();
				$ann->setugm();
				$ann->setvalues($_POST,'comm_');
				$ann->fields['date_annotation'] = dims_createtimestamp();
				$ann->fields['id_object'] = $_SESSION['desktopv2']['concepts']['sel_type'];
				$ann->fields['id_record'] = $_SESSION['desktopv2']['concepts']['sel_id'];
				$ann->fields['id_module_type'] = $_SESSION['dims']['moduletypeid'];
				$ann->save();
			}
			dims_redirect($dims->getScriptenv());
			break;
		case 'del_comment_concepts':
			ob_clean();
			$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, false);
			if ($id > 0) {
				require_once DIMS_APP_PATH.'modules/system/class_annotation.php';
				$ann = new annotation();
				if ($ann->open($id)) {
					$ann->delete();
				}
			}
			dims_redirect($dims->getScriptEnv());
			die();
			break;
		case 'del_concepts_link':
			ob_clean();
			$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, false);
			$link_type = dims_load_securvalue('link_type', dims_const::_DIMS_NUM_INPUT, true, false);

			$id_action = $id_activity = $id_opportunity = $id_tiers = $id_tiers2 = $id_contact = $id_contact2 = $id_doc = 0;


			if ($id > 0 && $link_type > 0 && !empty($_SESSION['desktopv2']['concepts']['sel_type'])) {
				switch ($_SESSION['desktopv2']['concepts']['sel_type']) {
					case dims_const::_SYSTEM_OBJECT_TIERS:
						$id_tiers = $_SESSION['desktopv2']['concepts']['filters']['stack'][0][1];
						$tiers = new tiers();
						$tiers->openWithGB($id_tiers);
						$pivot = 'id_tiers';
						break;
					case dims_const::_SYSTEM_OBJECT_CONTACT:
						$id_contact = $_SESSION['desktopv2']['concepts']['filters']['stack'][0][1];
						$contact = new contact();
						$contact->openWithGB($id_contact);
						$pivot = 'id_contact';
						break;
					case dims_const::_SYSTEM_OBJECT_DOCFILE:
						// $id_doc = $_SESSION['desktopv2']['concepts']['filters']['stack'][0][1];
						$pivot = 'id_doc';
						break;
					case dims_const::_SYSTEM_OBJECT_EVENT:
						// $id_action = $_SESSION['desktopv2']['concepts']['filters']['stack'][0][1];
						$pivot = 'id_action';
						break;
					case dims_const::_SYSTEM_OBJECT_ACTIVITY:
						// $id_activity = $_SESSION['desktopv2']['concepts']['filters']['stack'][0][1];
						$pivot = 'id_activity';
						break;
					case dims_const::_SYSTEM_OBJECT_OPPORTUNITY:
						// $id_activity = $_SESSION['desktopv2']['concepts']['filters']['stack'][0][1];
						$pivot = 'id_opportunity';
						break;
					case dims_const::_SYSTEM_OBJECT_CASE:
						$pivot = 'id_case';
						break;
					case dims_const::_SYSTEM_OBJECT_SUIVI:
						$pivot = 'id_suivi';
						break;
				}

				switch ($link_type) {
					case dims_const::_SYSTEM_OBJECT_TIERS:
						if ($id_tiers > 0) {
							$id_tiers2 = $id;
							$tiers2 = new tiers();
							$tiers2->openWithGB($id_tiers2);
							$to_set = 'id_tiers2';
						}
						else {
							$id_tiers = $id;
							$tiers = new tiers();
							$tiers->openWithGB($id_tiers);
							$to_set = 'id_tiers';
						}
						break;
					case dims_const::_SYSTEM_OBJECT_CONTACT:
						if ($id_contact > 0) {
							$id_contact2 = $id;
							$contact2 = new contact();
							$contact2->openWithGB($id_contact2);
							$to_set = 'id_contact2';
						}
						else {
							$id_contact = $id;
							$contact = new contact();
							$contact->openWithGB($id_contact);
							$to_set = 'id_contact';
						}
						break;
					case dims_const::_SYSTEM_OBJECT_DOCFILE:
						// $id_doc = $id;
						$to_set = 'id_doc';
						break;
					case dims_const::_SYSTEM_OBJECT_EVENT:
						// $id_doc = $id;
						$to_set = 'id_action';
						break;
					case dims_const::_SYSTEM_OBJECT_ACTIVITY:
						// $id_doc = $id;
						$to_set = 'id_activity';
						break;
					case dims_const::_SYSTEM_OBJECT_OPPORTUNITY:
						// $id_doc = $id;
						$to_set = 'id_opportunity';
						break;
					case dims_const::_SYSTEM_OBJECT_CASE:
						$to_set = 'id_case';
						break;
					case dims_const::_SYSTEM_OBJECT_SUIVI:
						$to_set = 'id_suivi';
						break;
				}

				// Suppression du lien
				if ($id_tiers > 0 && $id_contact > 0) {
					$db->query('DELETE FROM dims_mod_business_tiers_contact WHERE id_tiers = :idtiers AND id_contact = :idcontact', array(
						':idtiers' => array('type' => PDO::PARAM_INT, 'value' => $tiers->getId()),
						':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $contact->getId())
					));
				}
				else {
					if ($id_tiers > 0 && $id_tiers2 > 0) {
						$db->query('DELETE FROM dims_mod_business_ct_link WHERE id_contact1 = :idtiers1 AND id_contact2 = :idtiers2 AND id_object =  :idobject ', array(
							':idtiers1' => array('type' => PDO::PARAM_INT, 'value' => $tiers->getId()),
							':idtiers2' => array('type' => PDO::PARAM_INT, 'value' => $tiers2->getId()),
							':idobject' => array('type' => PDO::PARAM_INT, 'value' => dims_const::_SYSTEM_OBJECT_TIERS)
						));
						$db->query('DELETE FROM dims_mod_business_ct_link WHERE id_contact1 = :idtiers1 AND id_contact2 = :idtiers2 AND id_object = :idobject ', array(
							':idtiers1' => array('type' => PDO::PARAM_INT, 'value' => $tiers2->getId()),
							':idtiers2' => array('type' => PDO::PARAM_INT, 'value' => $tiers->getId()),
							':idobject' => array('type' => PDO::PARAM_INT, 'value' => dims_const::_SYSTEM_OBJECT_TIERS)
						));
					}
					elseif ($id_contact > 0 && $id_contact2 > 0) {
						$db->query('DELETE FROM dims_mod_business_ct_link WHERE id_contact1 = :idcontact1 AND id_contact2 = :idcontact2 AND id_object = :idobject ', array(
							':idcontact1' => array('type' => PDO::PARAM_INT, 'value' => $contact->getId()),
							':idcontact2' => array('type' => PDO::PARAM_INT, 'value' => $contact2->getId()),
							':idobject' => array('type' => PDO::PARAM_INT, 'value' => dims_const::_SYSTEM_OBJECT_CONTACT)
						));
						$db->query('DELETE FROM dims_mod_business_ct_link WHERE id_contact1 = :idcontact1 AND id_contact2 = :idcontact2 AND id_object = :idobject ', array(
							':idcontact1' => array('type' => PDO::PARAM_INT, 'value' => $contact2->getId()),
							':idcontact2' => array('type' => PDO::PARAM_INT, 'value' => $contact->getId()),
							':idobject' => array('type' => PDO::PARAM_INT, 'value' => dims_const::_SYSTEM_OBJECT_CONTACT)
						));
					}
				}

				// suppression de la matrice
				$db->query('
					UPDATE dims_matrix
					SET '.$to_set.' = 0
					WHERE '.$pivot.' = :idpivot
					AND '.$to_set.' = :id', array(
						':idpivot' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['desktopv2']['concepts']['filters']['stack'][0][1]),
						':id' => array('type' => PDO::PARAM_INT, 'value' => $id),
					));

				// dans le cas du détachement d'un contact à une entreprise,
				// il faut aussi enlever le lien entre ce contact avec les autres contacts
				// qui ont le même lien dans l'entreprise (ex: employés) + symétrie
				if ( $id_tiers > 0 && $id_contact > 0 && $_SESSION['desktopv2']['concepts']['sel_type'] == dims_const::_SYSTEM_OBJECT_TIERS && $link_type == dims_const::_SYSTEM_OBJECT_CONTACT ) {
					$db->query('
						UPDATE dims_matrix
						SET id_contact = 0
						WHERE id_tiers = :idtier
						AND id_contact = :idcontact
						AND NOT ( id_action = 0 AND id_opportunity = 0 AND id_activity = 0 AND id_tiers2 = 0 AND id_doc = 0 AND id_case = 0 AND id_suivi = 0 )', array(
							':idtier' => array('type' => PDO::PARAM_INT, 'value' => $id_tiers),
							':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id_contact),
						));

					$db->query('
						UPDATE dims_matrix
						SET id_contact2 = 0
						WHERE id_tiers = :idtier
						AND id_contact2 = :idcontact
						AND NOT ( id_action = 0 AND id_opportunity = 0 AND id_activity = 0 AND id_tiers2 = 0 AND id_doc = 0 AND id_case = 0 AND id_suivi = 0 )', array(
							':idtier' => array('type' => PDO::PARAM_INT, 'value' => $id_tiers),
							':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id_contact),
						));

					// On supprime les lignes qui ont un id_contact2 et pas d'id_contact
					$db->query('DELETE FROM dims_matrix WHERE id_contact = 0 AND id_contact2 > 0');
				}
			}

			dims_redirect($dims->getScriptEnv());
			die();
			break;
		case 'save_comment_address_book' :
			require_once DIMS_APP_PATH.'modules/system/class_annotation.php';
			if (isset($_SESSION['desktopv2']['adress_book']['sel_type']) && $_SESSION['desktopv2']['adress_book']['sel_type'] > 0 &&
				isset($_SESSION['desktopv2']['adress_book']['sel_id']) && $_SESSION['desktopv2']['adress_book']['sel_id'] > 0){
				require_once DIMS_APP_PATH.'modules/system/class_annotation.php';
				$ann = new annotation();
				$ann->init_description();
				$ann->setugm();
				$ann->setvalues($_POST,'comm_');
				$ann->fields['date_annotation'] = dims_createtimestamp();
				$ann->fields['id_object'] = $_SESSION['desktopv2']['adress_book']['sel_type'];
				$ann->fields['id_record'] = $_SESSION['desktopv2']['adress_book']['sel_id'];
				$ann->fields['id_module_type'] = $_SESSION['dims']['moduletypeid'];
				$ann->save();
			}
			dims_redirect($dims->getScriptenv());
			break;
		case 'save_document_concepts':
			require_once DIMS_APP_PATH.'modules/system/class_annotation.php';
			if (isset($_SESSION['desktopv2']['concepts']['sel_type']) && $_SESSION['desktopv2']['concepts']['sel_type'] > 0 &&
				isset($_SESSION['desktopv2']['concepts']['sel_id']) && $_SESSION['desktopv2']['concepts']['sel_id'] > 0){

				if (!empty($_FILES['concept_document']) && !$_FILES['concept_document']['error']) {
					$file = $_FILES['concept_document'];

					$doc = new docfile();
					$doc->init_description();
					$doc->fields['name'] = $file['name'];
					move_uploaded_file($file['tmp_name'], DIMS_TMP_PATH . $file['name']);
					$doc->tmpuploadedfile = DIMS_TMP_PATH . $file['name'] ;
					$doc->fields['size'] = $file['size'];
					$doc->fields['id_folder'] = 0;
					$doc->setugm();
					$doc->save();

					// on attache le document au pivot
					$sql = 'INSERT INTO dims_matrix SET';
					switch ($_SESSION['desktopv2']['concepts']['filters']['stack'][0][0]) {
						case 'contact':
							$sql .= ' `id_contact` = :idcontact,';
							$params[':idcontact'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['desktopv2']['concepts']['filters']['stack'][0][1]);
							break;
						case 'company':
							$sql .= ' `id_tiers` = :idtier,';
							$params[':idtier'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['desktopv2']['concepts']['filters']['stack'][0][1]);
							break;
						case 'event':
							$sql .= ' `id_action` = :idaction,';
							$params[':idaction'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['desktopv2']['concepts']['filters']['stack'][0][1]);
							break;
						case 'activity':
							$sql .= ' `id_activity` = :idactivity,';
							$params[':idactivity'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['desktopv2']['concepts']['filters']['stack'][0][1]);
							break;
						case 'opportunity':
							$sql .= ' `id_opportunity` = :idopportunity,';
							$params[':idopportunity'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['desktopv2']['concepts']['filters']['stack'][0][1]);
							break;
						case 'dossier':
							$sql .= ' `id_case` = idcase,';
							$params[':idcase'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['desktopv2']['concepts']['filters']['stack'][0][1]);
							break;
						case 'suivi':
							$sql .= ' `id_suivi` = :idsuivi,';
							$params[':idsuivi'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['desktopv2']['concepts']['filters']['stack'][0][1]);
							break;
					}
					$sql .= ' `id_doc` = :iddoc, `year` = '.date('Y').', `month` = '.date('m').', `timestp_modify` = '.dims_createtimestamp().', `id_workspace` = :idworkspace';
					$params[':iddoc'] = array('type' => PDO::PARAM_INT, 'value' => $doc->fields['id_globalobject']);
					$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']);
					$db->query($sql, $params);
				}
			}
			dims_redirect($dims->getScriptenv());
			break;
		case 'save_new_tag':
			$tag = new tag();
			$tag->init_description();
			$tag->fields['private'] = 0;
			$tag->setvalues($_POST,'tag_');
			$tag->fields['id_user'] = $_SESSION['dims']['userid'];
			$tag->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
			$tag->fields['type'] = 0;
			$tag->fields['group'] = 0;
			$tag->fields['tag'] = trim($tag->fields['tag']);
			if ($tag->fields['tag'] != ''){
				$tag->save();
				$_SESSION['desktop']['search']['tags'][$tag->fields['id']] = $tag->fields['id']; // on ajoute automatiquement ce nouveau tag dans la recherche
			}
			dims_redirect($dims->getScriptenv());
			break;

		case 'save_categ_tag':
			require_once DIMS_APP_PATH.'modules/system/class_tag_category.php';
			$label = dims_load_securvalue('tag_label',dims_const::_DIMS_CHAR_INPUT,true,true,true);

			$tagcateg = new tag_category();
			$id_tagcateg = dims_load_securvalue('id_tagcateg',dims_const::_DIMS_NUM_INPUT,true,true,true);

			if ($id_tagcateg>=0) {
				if ($id_tagcateg==0) {
					$tagcateg->init_description ();
					$tagcateg->setugm();
					$tagcateg->fields['id']=0;
					$tagcateg->fields['label']=$label;
					$tagcateg->save();
				}
			}

			break;

		case 'edit_categ_tag':
			ob_end_clean();
			require_once DIMS_APP_PATH.'modules/system/class_tag_category.php';

			$tagcateg = new tag_category();
			$id_tagcateg = dims_load_securvalue('id_tagcateg',dims_const::_DIMS_NUM_INPUT,true,true,true);

			if ($id_tagcateg>=0) {
				if ($id_tagcateg==0) {
					$tagcateg->init_description ();
					$tagcateg->fields['id']=0;
				}
				$tagcateg->display(_DESKTOP_TPL_LOCAL_PATH.'tag/tag_form_editcateg.php');
				//require_once _DESKTOP_TPL_LOCAL_PATH.'tag/tag_form_editcateg.php';
			}
			die();
			//dims_redirect($dims->getScriptenv());
			break;
		case 'save_contacts_gr':
			$id = dims_load_securvalue('id_gr',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$label = trim(dims_load_securvalue('label_gr',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			if ($label != ''){
				$gr = new ct_group();
				if ($id != '' && $id > 0)
					$gr->open($id);
				else{
					$gr->fields['view'] = 0;
					$gr->fields['date_create'] = dims_createtimestamp();
					$gr->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
					$gr->fields['id_user_create'] = $_SESSION['dims']['userid'];
				}
				$gr->fields['label'] = $label;
				$gr->save();
			}
			dims_redirect($dims->getScriptenv());
			break;
		case 'delete_contacts_gr':
			$gr = new ct_group();
			if (isset($_SESSION['desktopv2']['adress_book']['group']) && $_SESSION['desktopv2']['adress_book']['group'] > 0) {
				$gr->open($_SESSION['desktopv2']['adress_book']['group']);
				$gr->delete();
			}
			dims_redirect($dims->getScriptenv());
			break;
		case 'detach_contact_ab':
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$type = trim(dims_load_securvalue('type',dims_const::_DIMS_NUM_INPUT,true,true,true));
			if ($id != '' && $id > 0){
				$gb = 0;
				switch($type){
					case dims_const::_SYSTEM_OBJECT_CONTACT :
						unset($_SESSION['desktopv2']['adress_book']['sel_id']);
						unset($_SESSION['desktopv2']['adress_book']['sel_type']);
						$ct = new contact();
						$ct->open($id);
						$gb = $ct->fields['id_globalobject'];
						$del = "DELETE FROM	dims_mod_business_ct_link
							WHERE		(id_contact1 = :idcontact2
							AND		id_contact2 = :idcontact1)
							OR		(id_contact2 = :idcontact1
							AND		id_contact1 = :idcontact2)";
						$delparams[':idcontact1'] = array('type' => PDO::PARAM_INT, 'value' => $id);
						$delparams[':idcontact2'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['user']['id_contact']);
						break;
					case dims_const::_SYSTEM_OBJECT_TIERS :
						unset($_SESSION['desktopv2']['adress_book']['sel_id']);
						unset($_SESSION['desktopv2']['adress_book']['sel_type']);
						$tiers = new tiers();
						$tiers->open($id);
						$gb = $tiers->fields['id_globalobject'];
						$del = "DELETE FROM	dims_mod_business_tiers_contact
							WHERE		id_tiers = :idtier
							AND		id_contact = :idcontact";
						$delparams[':idtier'] = array('type' => PDO::PARAM_INT, 'value' => $id);
						$delparams[':idcontact'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['user']['id_contact']);
						break;
				}
				if ($gb > 0){
					//suppression du lien
					$db->query($del, $delparams);
					//suppression des groupes auxquels il pourrait être ajouté
					$sel = "SELECT	id
							FROM	dims_mod_business_contact_group
							WHERE	id_user_create = :iduser";
					$lstGr = array();
					$res = $db->query($sel, array(
						':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
					));
					while($r = $db->fetchrow($res))
						$lstGr[] = $r['id'];
					if (count($lstGr) > 0){
						$params = array();
						$del = "DELETE FROM	dims_mod_business_contact_group_link
							WHERE		id_globalobject = :idglobalobject
							AND		id_group_ct IN (".$db->getParamsFromArray($lstGr, 'idgroupct', $params).")";
						$params[':idglobalobject'] = array('type' => PDO::PARAM_INT, 'value' => $gb);
						$db->query($del, $params);
					}
					//suppression éventuelle des favoris
					$fav = new favorite();
					if ($fav->open($_SESSION['dims']['userid'],$gb)){
						$fav->changeStatus(favorite::NotFavorite);
						$fav->save();
					}
					//mise à 0 des lignes de liaison dans la matrice
					require_once DIMS_APP_PATH.'modules/system/class_matrix.php';
					$user_ct = new contact();
					$user_ct->open($_SESSION['dims']['user']['id_contact']);
					$matrix = new matrix();
					if($type == dims_const::_SYSTEM_OBJECT_CONTACT){
						$matrix->cutLink(array( 'id_contact' => $user_ct->fields['id_globalobject'], 'id_contact2' => $gb ));//la fonction assume la symétrie
					}
					else if($type == dims_const::_SYSTEM_OBJECT_TIERS){
						$matrix->cutLink(array( 'id_contact' => $user_ct->fields['id_globalobject'], 'id_tiers' => $gb ));
					}
				}
				else{//à priori s'il a pas de global object on prend pas de risque, il pourra jamais être dans un groupe ou en favoris
					$db->query($del, $delparams);
				}
			}
			dims_redirect($dims->getScriptenv());
			break;
		case 'add_to_ab':
			ob_clean();

			$id_go = dims_load_securvalue('id_go', dims_const::_DIMS_NUM_INPUT, true, false);
			$type = dims_load_securvalue('type', dims_const::_DIMS_NUM_INPUT, true, false);

			if (!empty($id_go) && !empty($type)) {
				switch ($type) {
					case dims_const::_SYSTEM_OBJECT_CONTACT:
						$contact = new contact();
						$contact->openWithGB($id_go);

						require_once DIMS_APP_PATH.'modules/system/class_ct_link.php';
						$ctlink = new ctlink();
						$ctlink->fields['id_contact1'] = $_SESSION['dims']['user']['id_contact'];
						$ctlink->fields['id_contact2'] = $contact->fields['id'];
						$ctlink->fields['id_object'] = $type;
						$ctlink->fields['type_link'] = 'business';
						$ctlink->fields['link_level'] = 2;
						$ctlink->fields['id_ct_user_create'] = $_SESSION['dims']['userid'];
						$ctlink->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
						$ctlink->fields['id_user'] = $_SESSION['dims']['userid'];
						$ctlink->save();

						// insertion du lien dans la matrice (avec la symétrie)
						$myCt = new contact();
						$myCt->open($_SESSION['dims']['user']['id_contact']);

						$db->query('
							INSERT INTO dims_matrix SET
							id_action = 0,
							id_opportunity = 0,
							id_activity = 0,
							id_tiers = 0,
							id_tiers2 = 0,
							id_contact = :idcontact1,
							id_contact2 = :idcontact2,
							id_doc = 0,
							id_case = 0,
							id_suivi = 0,
							id_country = 0,
							year = '.date('Y').',
							month = '.date('m').',
							timestp_modify = '.date('YmdHis').',
							id_workspace = :idworkspace', array(
							':idcontact1' => array('type' => PDO::PARAM_INT, 'value' => $myCt->fields['id_globalobject']),
							':idcontact2' => array('type' => PDO::PARAM_INT, 'value' => $contact->fields['id_globalobject']),
							':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
						));
						$db->query('
							INSERT INTO dims_matrix SET
							id_action = 0,
							id_opportunity = 0,
							id_activity = 0,
							id_tiers = 0,
							id_tiers2 = 0,
							id_contact = :idcontact1,
							id_contact2 = :idcontact2,
							id_doc = 0,
							id_case = 0,
							id_suivi = 0,
							id_country = 0,
							year = '.date('Y').',
							month = '.date('m').',
							timestp_modify = '.date('YmdHis').',
							id_workspace = :idworkspace', array(
							':idcontact1' => array('type' => PDO::PARAM_INT, 'value' => $contact->fields['id_globalobject']),
							':idcontact2' => array('type' => PDO::PARAM_INT, 'value' => $myCt->fields['id_globalobject']),
							':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
						));
						break;
					case dims_const::_SYSTEM_OBJECT_TIERS:
						$tiers = new tiers();
						$tiers->openWithGB($id_go);
						$tiers->updateIdCountry();

						require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
						$tiersct = new tiersct();
						$tiersct->fields['id_tiers'] = $tiers->fields['id'];
						$tiersct->fields['id_contact'] = $_SESSION['dims']['user']['id_contact'];
						$tiersct->fields['type_lien'] = 'Other';
						$tiersct->fields['link_level'] = 2;
						$tiersct->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
						$tiersct->fields['id_user'] = $_SESSION['dims']['userid'];
						$tiersct->fields['date_create'] = dims_createtimestamp();
						$tiersct->save();

						// insertion du lien dans la matrice
						$myCt = new contact();
						$myCt->open($_SESSION['dims']['user']['id_contact']);

						$db->query('
							INSERT INTO dims_matrix SET
							id_action = 0,
							id_opportunity = 0,
							id_activity = 0,
							id_tiers = :idtier1,
							id_tiers2 = 0,
							id_contact = :idtier2,
							id_contact2 = 0,
							id_doc = 0,
							id_case = 0,
							id_suivi = 0,
							id_country = :idcountry,
							year = '.date('Y').',
							month = '.date('m').',
							timestp_modify = '.date('YmdHis').',
							id_workspace = :idworkspace', array(
							':idtier1' => array('type' => PDO::PARAM_INT, 'value' => $tier->fields['id_globalobject']),
							':idtier2' => array('type' => PDO::PARAM_INT, 'value' => $myCt->fields['id_globalobject']),
							':idcountry' => array('type' => PDO::PARAM_INT, 'value' => $myCt->fields['id_country']),
							':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
						));
						break;
				}
			}
			dims_redirect($dims->getScriptEnv());
			break;
		case 'remove_from_ab':
			ob_clean();
			$id_go = dims_load_securvalue('id_go', dims_const::_DIMS_NUM_INPUT, true, false);
			$type = dims_load_securvalue('type', dims_const::_DIMS_NUM_INPUT, true, false);

			if (!empty($id_go) && !empty($type)) {
				switch ($type) {
					case dims_const::_SYSTEM_OBJECT_CONTACT:
						// ouverture du contact
						$contact = new contact();
						$contact->openWithGB($id_go);

						// suppression du lien
						$db->query('
							DELETE FROM dims_mod_business_ct_link
							WHERE id_contact1 = :idcontact1
							AND id_contact2 = :idcontact2
							AND id_object = '.dims_const::_SYSTEM_OBJECT_CONTACT, array(
							':idcontact1' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['user']['id_contact']),
							':idcontact2' => array('type' => PDO::PARAM_INT, 'value' => $contact->getId()),
						));

						// suppression de tous les groupes
						$db->query('
							DELETE dims_mod_business_contact_group_link.*
							FROM dims_mod_business_contact_group_link, dims_mod_business_contact_group
							WHERE dims_mod_business_contact_group_link.id_globalobject = :idglobalobject
							AND dims_mod_business_contact_group_link.type_contact = :idobject
							AND dims_mod_business_contact_group_link.id_group_ct = dims_mod_business_contact_group.id
							AND dims_mod_business_contact_group.id_user_create = :iduser', array(
							':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $id_go),
							':idobject' => array('type' => PDO::PARAM_INT, 'value' => dims_const::_SYSTEM_OBJECT_CONTACT),
							':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid'])
						));

						// suppression du lien dans la matrice (avec la symétrie)
						$myCt = new contact();
						$myCt->open($_SESSION['dims']['user']['id_contact']);

						$db->query('
							DELETE FROM dims_matrix
							WHERE id_action = 0
							AND id_opportunity = 0
							AND id_activity = 0
							AND id_tiers = 0
							AND id_tiers2 = 0
							AND id_contact = :idcontact1
							AND id_contact2 = :idcontact2
							AND id_doc = 0
							AND id_case = 0
							AND id_suivi = 0
							AND id_country = 0', array(
							':idcontact1' => array('type' => PDO::PARAM_INT, 'value' => $myCt->fields['id_globalobject']),
							':idcontact2' => array('type' => PDO::PARAM_INT, 'value' => $contact->fields['id_globalobject']),
						));
						$db->query('
							DELETE FROM dims_matrix
							WHERE id_action = 0
							AND id_opportunity = 0
							AND id_activity = 0
							AND id_tiers = 0
							AND id_tiers2 = 0
							AND id_contact = :idcontact1
							AND id_contact2 = :idcontact2
							AND id_doc = 0
							AND id_case = 0
							AND id_suivi = 0
							AND id_country = 0', array(
							':idcontact1' => array('type' => PDO::PARAM_INT, 'value' => $contact->fields['id_globalobject']),
							':idcontact2' => array('type' => PDO::PARAM_INT, 'value' => $myCt->fields['id_globalobject']),
						));
						break;
					case dims_const::_SYSTEM_OBJECT_TIERS:
						$tiers = new tiers();
						$tiers->openWithGB($id_go);
						$tiers->updateIdCountry();

						// suppression du lien
						$db->query('DELETE FROM dims_mod_business_tiers_contact WHERE id_tiers = :idtier AND id_contact = :idcontact', array(
							':idtier' => array('type' => PDO::PARAM_INT, 'value' => $tiers->getId()),
							':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['user']['id_contact']),
						));

						// suppression de tous les groupes
						$db->query('
							DELETE dims_mod_business_contact_group_link.*
							FROM dims_mod_business_contact_group_link, dims_mod_business_contact_group
							WHERE dims_mod_business_contact_group_link.id_globalobject = :idglobalobject
							AND dims_mod_business_contact_group_link.type_contact = :idobject
							AND dims_mod_business_contact_group_link.id_group_ct = dims_mod_business_contact_group.id
							AND dims_mod_business_contact_group.id_user_create = :iduser', array(
							':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $id_go),
							':idobject' => array('type' => PDO::PARAM_INT, 'value' => dims_const::_SYSTEM_OBJECT_CONTACT),
							':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid'])
						));

						// suppression du lien dans la matrice (avec la symétrie)
						$myCt = new contact();
						$myCt->open($_SESSION['dims']['user']['id_contact']);

						$db->query('
							DELETE FROM dims_matrix
							WHERE id_action = 0
							AND id_opportunity = 0
							AND id_activity = 0
							AND id_tiers = :idtier
							AND id_tiers2 = 0
							AND id_contact = :idcontact
							AND id_contact2 = 0
							AND id_doc = 0
							AND id_case = 0
							AND id_suivi = 0
							AND id_country = :idcountry', array(
							':idtier' => array('type' => PDO::PARAM_INT, 'value' => $tiers->fields['id_globalobject']),
							':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $myCt->fields['id_globalobject']),
							':idcountry' => array('type' => PDO::PARAM_INT, 'value' => $myCt->fields['id_country'])
						));
						break;
				}
			}
			dims_redirect($dims->getScriptEnv());
			break;
		case 'add_filter':
			$filter_type = dims_load_securvalue('filter_type', dims_const::_DIMS_CHAR_INPUT, true, true);
			$filter_value = dims_load_securvalue('filter_value', dims_const::_DIMS_CHAR_INPUT, true, true);

			switch ($filter_type) {
				case 'contact':
					if (!isset($_SESSION['desktopv2']['concepts']['filters']['contacts'][$filter_value])) {
						$_SESSION['desktopv2']['concepts']['filters']['contacts'][$filter_value] = $filter_value;
						$_SESSION['desktopv2']['concepts']['filters']['stack'][] = array('contact', $filter_value);
					}
					break;
				case 'company':
					if (!isset($_SESSION['desktopv2']['concepts']['filters']['companies'][$filter_value])) {
						$_SESSION['desktopv2']['concepts']['filters']['companies'][$filter_value] = $filter_value;
						$_SESSION['desktopv2']['concepts']['filters']['stack'][] = array('company', $filter_value);
					}
					break;
				case 'event':
					if (!isset($_SESSION['desktopv2']['concepts']['filters']['events'][$filter_value])) {
						$_SESSION['desktopv2']['concepts']['filters']['events'][$filter_value] = $filter_value;
						$_SESSION['desktopv2']['concepts']['filters']['stack'][] = array('event', $filter_value);
					}
					break;
				case 'activity':
					if (!isset($_SESSION['desktopv2']['concepts']['filters']['activities'][$filter_value])) {
						$_SESSION['desktopv2']['concepts']['filters']['activities'][$filter_value] = $filter_value;
						$_SESSION['desktopv2']['concepts']['filters']['stack'][] = array('activity', $filter_value);
					}
					break;
				case 'opportunity':
					if (!isset($_SESSION['desktopv2']['concepts']['filters']['opportunities'][$filter_value])) {
						$_SESSION['desktopv2']['concepts']['filters']['opportunities'][$filter_value] = $filter_value;
						$_SESSION['desktopv2']['concepts']['filters']['stack'][] = array('opportunity', $filter_value);
					}
					break;
				case 'doc':
					if (!isset($_SESSION['desktopv2']['concepts']['filters']['documents'][$filter_value])) {
						$_SESSION['desktopv2']['concepts']['filters']['documents'][$filter_value] = $filter_value;
						$_SESSION['desktopv2']['concepts']['filters']['stack'][] = array('doc', $filter_value);
					}
					break;
				case 'dossier':
					if (!isset($_SESSION['desktopv2']['concepts']['filters']['dossiers'][$filter_value])) {
						$_SESSION['desktopv2']['concepts']['filters']['dossiers'][$filter_value] = $filter_value;
						$_SESSION['desktopv2']['concepts']['filters']['stack'][] = array('dossier', $filter_value);
					}
					break;
				case 'suivi':
					if (!isset($_SESSION['desktopv2']['concepts']['filters']['suivis'][$filter_value])) {
						$_SESSION['desktopv2']['concepts']['filters']['suivis'][$filter_value] = $filter_value;
						$_SESSION['desktopv2']['concepts']['filters']['stack'][] = array('suivi', $filter_value);
					}
					break;
				case 'year':
					if (!isset($_SESSION['desktopv2']['concepts']['filters']['years'][$filter_value])) {
						$_SESSION['desktopv2']['concepts']['filters']['years'][$filter_value] = $filter_value;
						$_SESSION['desktopv2']['concepts']['filters']['stack'][] = array('year', $filter_value);
					}
					break;
				case 'country':
					if (!isset($_SESSION['desktopv2']['concepts']['filters']['countries'][$filter_value])) {
						$_SESSION['desktopv2']['concepts']['filters']['countries'][$filter_value] = $filter_value;
						$_SESSION['desktopv2']['concepts']['filters']['stack'][] = array('country', $filter_value);
					}
					break;
			}
			dims_redirect($dims->getScriptEnv());
			break;
		case 'swap_filter':
			$filter_type = dims_load_securvalue('filter_type', dims_const::_DIMS_CHAR_INPUT, true, true);
			$filter_value = dims_load_securvalue('filter_value', dims_const::_DIMS_CHAR_INPUT, true, true);

			// replacement du pivot actuel par le nouveau
			switch ($_SESSION['desktopv2']['concepts']['filters']['stack'][0][0]) {
				case 'event':
					unset($_SESSION['desktopv2']['concepts']['filters']['events'][$_SESSION['desktopv2']['concepts']['filters']['stack'][0][1]]);
					break;
				case 'activity':
					unset($_SESSION['desktopv2']['concepts']['filters']['activities'][$_SESSION['desktopv2']['concepts']['filters']['stack'][0][1]]);
					break;
				case 'opportunity':
					unset($_SESSION['desktopv2']['concepts']['filters']['opportunities'][$_SESSION['desktopv2']['concepts']['filters']['stack'][0][1]]);
					break;
				case 'contact':
					unset($_SESSION['desktopv2']['concepts']['filters']['contacts'][$_SESSION['desktopv2']['concepts']['filters']['stack'][0][1]]);
					break;
				case 'company':
					unset($_SESSION['desktopv2']['concepts']['filters']['companies'][$_SESSION['desktopv2']['concepts']['filters']['stack'][0][1]]);
					break;
				case 'doc':
					unset($_SESSION['desktopv2']['concepts']['filters']['documents'][$_SESSION['desktopv2']['concepts']['filters']['stack'][0][1]]);
					break;
				case 'dossier':
					unset($_SESSION['desktopv2']['concepts']['filters']['dossiers'][$_SESSION['desktopv2']['concepts']['filters']['stack'][0][1]]);
					break;
				case 'suivi':
					unset($_SESSION['desktopv2']['concepts']['filters']['suivis'][$_SESSION['desktopv2']['concepts']['filters']['stack'][0][1]]);
					break;
			}
			unset($_SESSION['desktopv2']['concepts']['filters']['stack'][0]);

			switch ($filter_type) {
				case 'contact':
					// repositionnement de la fiche principale sur le nouveau pivot
					$_SESSION['desktopv2']['concepts']['filters']['pivot'] = dims_const::_SYSTEM_OBJECT_CONTACT.'-'.$filter_value;
					$rs = $db->query('SELECT id FROM dims_mod_business_contact WHERE id_globalobject = :idglobalobject AND inactif = 0 LIMIT 0, 1', array(
						':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $filter_value),
					));
					$row = $db->fetchrow($rs);
					$_SESSION['desktopv2']['concepts']['sel_id'] = $row['id'];
					$_SESSION['desktopv2']['concepts']['sel_type'] = dims_const::_SYSTEM_OBJECT_CONTACT;

					if (!isset($_SESSION['desktopv2']['concepts']['filters']['contacts'][$filter_value])) {
						$_SESSION['desktopv2']['concepts']['filters']['contacts'][$filter_value] = $filter_value;
						$_SESSION['desktopv2']['concepts']['filters']['stack'][0] = array('contact', $filter_value);
					}
					break;
				case 'company':
					// repositionnement de la fiche principale sur le nouveau pivot
					$_SESSION['desktopv2']['concepts']['filters']['pivot'] = dims_const::_SYSTEM_OBJECT_TIERS.'-'.$filter_value;
					$rs = $db->query('SELECT id FROM dims_mod_business_tiers WHERE id_globalobject = :idglobalobject AND inactif = 0 LIMIT 0, 1', array(
						':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $filter_value),
					));
					$row = $db->fetchrow($rs);
					$_SESSION['desktopv2']['concepts']['sel_id'] = $row['id'];
					$_SESSION['desktopv2']['concepts']['sel_type'] = dims_const::_SYSTEM_OBJECT_TIERS;

					if (!isset($_SESSION['desktopv2']['concepts']['filters']['companies'][$filter_value])) {
						$_SESSION['desktopv2']['concepts']['filters']['companies'][$filter_value] = $filter_value;
						$_SESSION['desktopv2']['concepts']['filters']['stack'][0] = array('company', $filter_value);
					}
					break;
				case 'event':
					// repositionnement de la fiche principale sur le nouveau pivot
					$_SESSION['desktopv2']['concepts']['filters']['pivot'] = dims_const::_SYSTEM_OBJECT_ACTION.'-'.$filter_value;
					$rs = $db->query('SELECT id FROM dims_mod_business_action WHERE id_globalobject = :idglobalobject LIMIT 0, 1', array(
						':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $filter_value),
					));
					$row = $db->fetchrow($rs);
					$_SESSION['desktopv2']['concepts']['sel_id'] = $row['id'];
					$_SESSION['desktopv2']['concepts']['sel_type'] = dims_const::_SYSTEM_OBJECT_ACTION;

					if (!isset($_SESSION['desktopv2']['concepts']['filters']['events'][$filter_value])) {
						$_SESSION['desktopv2']['concepts']['filters']['events'][$filter_value] = $filter_value;
						$_SESSION['desktopv2']['concepts']['filters']['stack'][0] = array('event', $filter_value);
					}
					break;
				case 'activity':
					// repositionnement de la fiche principale sur le nouveau pivot
					$_SESSION['desktopv2']['concepts']['filters']['pivot'] = dims_const::_SYSTEM_OBJECT_ACTIVITY.'-'.$filter_value;
					$rs = $db->query('SELECT id FROM dims_mod_business_action WHERE id_globalobject = :idglobalobject LIMIT 0, 1', array(
						':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $filter_value),
					));
					$row = $db->fetchrow($rs);
					$_SESSION['desktopv2']['concepts']['sel_id'] = $row['id'];
					$_SESSION['desktopv2']['concepts']['sel_type'] = dims_const::_SYSTEM_OBJECT_ACTIVITY;

					if (!isset($_SESSION['desktopv2']['concepts']['filters']['activities'][$filter_value])) {
						$_SESSION['desktopv2']['concepts']['filters']['activities'][$filter_value] = $filter_value;
						$_SESSION['desktopv2']['concepts']['filters']['stack'][0] = array('activity', $filter_value);
					}
					break;
				case 'opportunity':
					// repositionnement de la fiche principale sur le nouveau pivot
					$_SESSION['desktopv2']['concepts']['filters']['pivot'] = dims_const::_SYSTEM_OBJECT_OPPORTUNITY.'-'.$filter_value;
					$rs = $db->query('SELECT id FROM dims_mod_business_action WHERE id_globalobject = :idglobalobject LIMIT 0, 1', array(
						':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $filter_value),
					));
					$row = $db->fetchrow($rs);
					$_SESSION['desktopv2']['concepts']['sel_id'] = $row['id'];
					$_SESSION['desktopv2']['concepts']['sel_type'] = dims_const::_SYSTEM_OBJECT_OPPORTUNITY;

					if (!isset($_SESSION['desktopv2']['concepts']['filters']['opportunities'][$filter_value])) {
						$_SESSION['desktopv2']['concepts']['filters']['opportunities'][$filter_value] = $filter_value;
						$_SESSION['desktopv2']['concepts']['filters']['stack'][0] = array('opportunity', $filter_value);
					}
					break;
				case 'doc':
					// repositionnement de la fiche principale sur le nouveau pivot
					$_SESSION['desktopv2']['concepts']['filters']['pivot'] = dims_const::_SYSTEM_OBJECT_DOCFILE.'-'.$filter_value;
					$rs = $db->query('SELECT id FROM dims_mod_doc_file WHERE id_globalobject = :idglobalobject LIMIT 0, 1', array(
						':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $filter_value),
					));
					$row = $db->fetchrow($rs);
					$_SESSION['desktopv2']['concepts']['sel_id'] = $row['id'];
					$_SESSION['desktopv2']['concepts']['sel_type'] = dims_const::_SYSTEM_OBJECT_DOCFILE;

					if (!isset($_SESSION['desktopv2']['concepts']['filters']['documents'][$filter_value])) {
						$_SESSION['desktopv2']['concepts']['filters']['documents'][$filter_value] = $filter_value;
						$_SESSION['desktopv2']['concepts']['filters']['stack'][0] = array('doc', $filter_value);
					}
					break;
				case 'dossier':
					// repositionnement de la fiche principale sur le nouveau pivot
					$_SESSION['desktopv2']['concepts']['filters']['pivot'] = dims_const::_SYSTEM_OBJECT_CASE.'-'.$filter_value;
					$rs = $db->query('SELECT id FROM dims_case WHERE id_globalobject = :idglobalobject LIMIT 0, 1', array(
						':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $filter_value),
					));
					$row = $db->fetchrow($rs);
					$_SESSION['desktopv2']['concepts']['sel_id'] = $row['id'];
					$_SESSION['desktopv2']['concepts']['sel_type'] = dims_const::_SYSTEM_OBJECT_CASE;

					if (!isset($_SESSION['desktopv2']['concepts']['filters']['dossiers'][$filter_value])) {
						$_SESSION['desktopv2']['concepts']['filters']['dossiers'][$filter_value] = $filter_value;
						$_SESSION['desktopv2']['concepts']['filters']['stack'][0] = array('dossier', $filter_value);
					}
					break;
				case 'suivi':
					// repositionnement de la fiche principale sur le nouveau pivot
					$_SESSION['desktopv2']['concepts']['filters']['pivot'] = dims_const::_SYSTEM_OBJECT_SUIVI.'-'.$filter_value;
					$rs = $db->query('SELECT id FROM dims_mod_business_suivi WHERE id_globalobject = :idglobalobject LIMIT 0, 1', array(
						':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $filter_value),
					));
					$row = $db->fetchrow($rs);
					$_SESSION['desktopv2']['concepts']['sel_id'] = $row['id'];
					$_SESSION['desktopv2']['concepts']['sel_type'] = dims_const::_SYSTEM_OBJECT_SUIVI;

					if (!isset($_SESSION['desktopv2']['concepts']['filters']['suivis'][$filter_value])) {
						$_SESSION['desktopv2']['concepts']['filters']['suivis'][$filter_value] = $filter_value;
						$_SESSION['desktopv2']['concepts']['filters']['stack'][0] = array('suivi', $filter_value);
					}
					break;
			}
			ksort($_SESSION['desktopv2']['concepts']['filters']['stack']);
			dims_redirect($dims->getScriptEnv());
			break;
		case 'drop_filter':
			$filter_type = dims_load_securvalue('filter_type', dims_const::_DIMS_CHAR_INPUT, true, true);
			$filter_value = dims_load_securvalue('filter_value', dims_const::_DIMS_CHAR_INPUT, true, true);

			$search_new_pivot = false;
			switch ($filter_type) {
				case 'contact':
					if (isset($_SESSION['desktopv2']['concepts']['filters']['contacts'][$filter_value])) {
						// si c'est le pivot, on en prend un autre
						if ($_SESSION['desktopv2']['concepts']['filters']['pivot'] == dims_const::_SYSTEM_OBJECT_CONTACT.'-'.$_SESSION['desktopv2']['concepts']['filters']['contacts'][$filter_value]) {
							$search_new_pivot = true;
						}
						// suppression de la pile
						$key = concept_stack_search($_SESSION['desktopv2']['concepts']['filters']['stack'], array('contact', $filter_value));
						unset($_SESSION['desktopv2']['concepts']['filters']['stack'][$key]);
						// suppression des filtres
						unset($_SESSION['desktopv2']['concepts']['filters']['contacts'][$filter_value]);
					}
					break;
				case 'company':
					if (isset($_SESSION['desktopv2']['concepts']['filters']['companies'][$filter_value])) {
						// si c'est le pivot, on en prend un autre
						if ($_SESSION['desktopv2']['concepts']['filters']['pivot'] == dims_const::_SYSTEM_OBJECT_TIERS.'-'.$_SESSION['desktopv2']['concepts']['filters']['companies'][$filter_value]) {
							$search_new_pivot = true;
						}
						// suppression de la pile
						$key = concept_stack_search($_SESSION['desktopv2']['concepts']['filters']['stack'], array('company', $filter_value));
						unset($_SESSION['desktopv2']['concepts']['filters']['stack'][$key]);
						// suppression des filtres
						unset($_SESSION['desktopv2']['concepts']['filters']['companies'][$filter_value]);
					}
					break;
				case 'event':
					if (isset($_SESSION['desktopv2']['concepts']['filters']['events'][$filter_value])) {
						// si c'est le pivot, on en prend un autre
						if ($_SESSION['desktopv2']['concepts']['filters']['pivot'] == dims_const::_SYSTEM_OBJECT_EVENT.'-'.$_SESSION['desktopv2']['concepts']['filters']['events'][$filter_value]) {
							$search_new_pivot = true;
						}
						// suppression de la pile
						$key = concept_stack_search($_SESSION['desktopv2']['concepts']['filters']['stack'], array('event', $filter_value));
						unset($_SESSION['desktopv2']['concepts']['filters']['stack'][$key]);
						// suppression des filtres
						unset($_SESSION['desktopv2']['concepts']['filters']['events'][$filter_value]);
					}
					break;
				case 'activity':
					if (isset($_SESSION['desktopv2']['concepts']['filters']['activities'][$filter_value])) {
						// si c'est le pivot, on en prend un autre
						if ($_SESSION['desktopv2']['concepts']['filters']['pivot'] == dims_const::_SYSTEM_OBJECT_ACTIVITY.'-'.$_SESSION['desktopv2']['concepts']['filters']['activities'][$filter_value]) {
							$search_new_pivot = true;
						}
						// suppression de la pile
						$key = concept_stack_search($_SESSION['desktopv2']['concepts']['filters']['stack'], array('activity', $filter_value));
						unset($_SESSION['desktopv2']['concepts']['filters']['stack'][$key]);
						// suppression des filtres
						unset($_SESSION['desktopv2']['concepts']['filters']['activities'][$filter_value]);
					}
					break;
				case 'opportunity':
					if (isset($_SESSION['desktopv2']['concepts']['filters']['opportunities'][$filter_value])) {
						// si c'est le pivot, on en prend un autre
						if ($_SESSION['desktopv2']['concepts']['filters']['pivot'] == dims_const::_SYSTEM_OBJECT_OPPORTUNITY.'-'.$_SESSION['desktopv2']['concepts']['filters']['activities'][$filter_value]) {
							$search_new_pivot = true;
						}
						// suppression de la pile
						$key = concept_stack_search($_SESSION['desktopv2']['concepts']['filters']['stack'], array('opportunity', $filter_value));
						unset($_SESSION['desktopv2']['concepts']['filters']['stack'][$key]);
						// suppression des filtres
						unset($_SESSION['desktopv2']['concepts']['filters']['opportunities'][$filter_value]);
					}
					break;
				case 'doc':
					if (isset($_SESSION['desktopv2']['concepts']['filters']['documents'][$filter_value])) {
						// si c'est le pivot, on en prend un autre
						if ($_SESSION['desktopv2']['concepts']['filters']['pivot'] == dims_const::_SYSTEM_OBJECT_DOCFILE.'-'.$_SESSION['desktopv2']['concepts']['filters']['documents'][$filter_value]) {
							$search_new_pivot = true;
						}
						// suppression de la pile
						$key = concept_stack_search($_SESSION['desktopv2']['concepts']['filters']['stack'], array('doc', $filter_value));
						unset($_SESSION['desktopv2']['concepts']['filters']['stack'][$key]);
						// suppression des filtres
						unset($_SESSION['desktopv2']['concepts']['filters']['documents'][$filter_value]);
					}
					break;
				case 'dossier':
					if (isset($_SESSION['desktopv2']['concepts']['filters']['dossiers'][$filter_value])) {
						// si c'est le pivot, on en prend un autre
						if ($_SESSION['desktopv2']['concepts']['filters']['pivot'] == dims_const::_SYSTEM_OBJECT_CASE.'-'.$_SESSION['desktopv2']['concepts']['filters']['dossiers'][$filter_value]) {
							$search_new_pivot = true;
						}
						// suppression de la pile
						$key = concept_stack_search($_SESSION['desktopv2']['concepts']['filters']['stack'], array('dossier', $filter_value));
						unset($_SESSION['desktopv2']['concepts']['filters']['stack'][$key]);
						// suppression des filtres
						unset($_SESSION['desktopv2']['concepts']['filters']['dossiers'][$filter_value]);
					}
					break;
				case 'suivi':
					if (isset($_SESSION['desktopv2']['concepts']['filters']['suivis'][$filter_value])) {
						// si c'est le pivot, on en prend un autre
						if ($_SESSION['desktopv2']['concepts']['filters']['pivot'] == dims_const::_SYSTEM_OBJECT_SUIVI.'-'.$_SESSION['desktopv2']['concepts']['filters']['suivis'][$filter_value]) {
							$search_new_pivot = true;
						}
						// suppression de la pile
						$key = concept_stack_search($_SESSION['desktopv2']['concepts']['filters']['stack'], array('suivi', $filter_value));
						unset($_SESSION['desktopv2']['concepts']['filters']['stack'][$key]);
						// suppression des filtres
						unset($_SESSION['desktopv2']['concepts']['filters']['suivis'][$filter_value]);
					}
					break;
				case 'year':
					if (isset($_SESSION['desktopv2']['concepts']['filters']['years'][$filter_value])) {
						// suppression de la pile
						$key = concept_stack_search($_SESSION['desktopv2']['concepts']['filters']['stack'], array('year', $filter_value));
						unset($_SESSION['desktopv2']['concepts']['filters']['stack'][$key]);
						// suppression des filtres
						unset($_SESSION['desktopv2']['concepts']['filters']['years'][$filter_value]);
					}
					break;
				case 'country':
					if (isset($_SESSION['desktopv2']['concepts']['filters']['countries'][$filter_value])) {
						// suppression de la pile
						$key = concept_stack_search($_SESSION['desktopv2']['concepts']['filters']['stack'], array('country', $filter_value));
						unset($_SESSION['desktopv2']['concepts']['filters']['stack'][$key]);
						// suppression des filtres
						unset($_SESSION['desktopv2']['concepts']['filters']['countries'][$filter_value]);
					}
					break;
			}

			// Si on supprime le pivot alors que c'est le seul élément,
			// on retourne sur la page d'accueil
			if (!sizeof($_SESSION['desktopv2']['concepts']['filters']['stack'])) {
				dims_redirect($dims->getScriptEnv().'?submenu='._DESKTOP_V2_DESKTOP.'&mode=default&force_desktop=1');
			}

			// recherche d'un nouveau pivot par ordre de priorité (on veut le dernier ajouté)
			if ($search_new_pivot) {
				foreach (array_reverse($_SESSION['desktopv2']['concepts']['filters']['stack'], true) as $key => $filter) {
					if ( $filter[0] == 'contact' || $filter[0] == 'company' || $filter[0] == 'event' || $filter[0] == 'activity' || $filter[0] == 'opportunity' || $filter[0] == 'doc' ) {
						switch ($filter[0]) {
							case 'contact':
								$_SESSION['desktopv2']['concepts']['filters']['pivot'] = dims_const::_SYSTEM_OBJECT_CONTACT.'-'.$filter[1];
								// repositionnement de la fiche principale sur le nouveau pivot
								$_SESSION['desktopv2']['concepts']['sel_type'] = dims_const::_SYSTEM_OBJECT_CONTACT;
								$rs = $db->query('SELECT id FROM dims_mod_business_contact WHERE id_globalobject = :idglobalobject AND inactif = 0 LIMIT 0, 1', array(
									':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $filter[1]),
								));
								$row = $db->fetchrow($rs);
								$_SESSION['desktopv2']['concepts']['sel_id'] = $row['id'];
								break;
							case 'company':
								$_SESSION['desktopv2']['concepts']['filters']['pivot'] = dims_const::_SYSTEM_OBJECT_TIERS.'-'.$filter[1];
								// repositionnement de la fiche principale sur le nouveau pivot
								$_SESSION['desktopv2']['concepts']['sel_type'] = dims_const::_SYSTEM_OBJECT_TIERS;
								$rs = $db->query('SELECT id FROM dims_mod_business_tiers WHERE id_globalobject = :idglobalobject AND inactif = 0 LIMIT 0, 1', array(
									':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $filter[1]),
								));
								$row = $db->fetchrow($rs);
								$_SESSION['desktopv2']['concepts']['sel_id'] = $row['id'];
								break;
							case 'event':
								$_SESSION['desktopv2']['concepts']['filters']['pivot'] = dims_const::_SYSTEM_OBJECT_EVENT.'-'.$filter[1];
								// repositionnement de la fiche principale sur le nouveau pivot
								$_SESSION['desktopv2']['concepts']['sel_type'] = dims_const::_SYSTEM_OBJECT_EVENT;
								$rs = $db->query('SELECT id FROM dims_mod_business_action WHERE id_globalobject = :idglobalobject LIMIT 0, 1', array(
									':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $filter[1]),
								));
								$row = $db->fetchrow($rs);
								$_SESSION['desktopv2']['concepts']['sel_id'] = $row['id'];
								break;
							case 'activity':
								$_SESSION['desktopv2']['concepts']['filters']['pivot'] = dims_const::_SYSTEM_OBJECT_ACTIVITY.'-'.$filter[1];
								// repositionnement de la fiche principale sur le nouveau pivot
								$_SESSION['desktopv2']['concepts']['sel_type'] = dims_const::_SYSTEM_OBJECT_ACTIVITY;
								$rs = $db->query('SELECT id FROM dims_mod_business_action WHERE id_globalobject = :idglobalobject LIMIT 0, 1', array(
									':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $filter[1]),
								));
								$row = $db->fetchrow($rs);
								$_SESSION['desktopv2']['concepts']['sel_id'] = $row['id'];
								break;
							case 'opportunity':
								$_SESSION['desktopv2']['concepts']['filters']['pivot'] = dims_const::_SYSTEM_OBJECT_OPPORTUNITY.'-'.$filter[1];
								// repositionnement de la fiche principale sur le nouveau pivot
								$_SESSION['desktopv2']['concepts']['sel_type'] = dims_const::_SYSTEM_OBJECT_OPPORTUNITY;
								$rs = $db->query('SELECT id FROM dims_mod_business_action WHERE id_globalobject = :idglobalobject LIMIT 0, 1', array(
									':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $filter[1]),
								));
								$row = $db->fetchrow($rs);
								$_SESSION['desktopv2']['concepts']['sel_id'] = $row['id'];
								break;
							case 'doc':
								$_SESSION['desktopv2']['concepts']['filters']['pivot'] = dims_const::_SYSTEM_OBJECT_DOCFILE.'-'.$filter[1];
								// repositionnement de la fiche principale sur le nouveau pivot
								$_SESSION['desktopv2']['concepts']['sel_type'] = dims_const::_SYSTEM_OBJECT_DOCFILE;
								$rs = $db->query('SELECT id FROM dims_mod_doc_file WHERE id_globalobject = :idglobalobject LIMIT 0, 1', array(
									':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $filter[1]),
								));
								$row = $db->fetchrow($rs);
								$_SESSION['desktopv2']['concepts']['sel_id'] = $row['id'];
								break;
							case 'dossier':
								$_SESSION['desktopv2']['concepts']['filters']['pivot'] = dims_const::_SYSTEM_OBJECT_CASE.'-'.$filter[1];
								// repositionnement de la fiche principale sur le nouveau pivot
								$_SESSION['desktopv2']['concepts']['sel_type'] = dims_const::_SYSTEM_OBJECT_CASE;
								$rs = $db->query('SELECT id FROM dims_case WHERE id_globalobject = :idglobalobject LIMIT 0, 1', array(
									':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $filter[1]),
								));
								$row = $db->fetchrow($rs);
								$_SESSION['desktopv2']['concepts']['sel_id'] = $row['id'];
								break;
							case 'suivi':
								$_SESSION['desktopv2']['concepts']['filters']['pivot'] = dims_const::_SYSTEM_OBJECT_SUIVI.'-'.$filter[1];
								// repositionnement de la fiche principale sur le nouveau pivot
								$_SESSION['desktopv2']['concepts']['sel_type'] = dims_const::_SYSTEM_OBJECT_SUIVI;
								$rs = $db->query('SELECT id FROM dims_mod_business_suivi WHERE id_globalobject = :idglobalobject LIMIT 0, 1', array(
									':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $filter[1]),
								));
								$row = $db->fetchrow($rs);
								$_SESSION['desktopv2']['concepts']['sel_id'] = $row['id'];
								break;
						}

						// le pivot reprend la 1e position
						$_SESSION['desktopv2']['concepts']['filters']['stack'][0] = $_SESSION['desktopv2']['concepts']['filters']['stack'][$key];
						unset($_SESSION['desktopv2']['concepts']['filters']['stack'][$key]);
						ksort($_SESSION['desktopv2']['concepts']['filters']['stack']);
						break;
					}
				}
			}
			dims_redirect($dims->getScriptEnv());
			break;
		case 'save_object':
			$type = dims_load_securvalue('type', dims_const::_DIMS_NUM_INPUT, true, true);
			$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true);
			switch($type) {
				case dims_const::_SYSTEM_OBJECT_CONTACT:
					$_SESSION['business']['contact_id'] = $id;
					include DIMS_APP_PATH.'modules/system/crm_public_contact_save.php';
					break;
				case dims_const::_SYSTEM_OBJECT_TIERS:
					$_SESSION['business']['tiers_id'] = $id;
					include DIMS_APP_PATH.'modules/system/crm_public_ent_save.php';
					break;
				case dims_const::_SYSTEM_OBJECT_ACTIVITY :
					if ($id != '' && $id > 0){
						$setSql = array();
						$label				= dims_load_securvalue('label', dims_const::_DIMS_CHAR_INPUT, false, true);
						$description		= dims_load_securvalue('description', dims_const::_DIMS_CHAR_INPUT, false, true);
						$country			= dims_load_securvalue('country', dims_const::_DIMS_NUM_INPUT, false, true);
						$city				= dims_load_securvalue('city', dims_const::_DIMS_NUM_INPUT, false, true);
						$sector_id			= dims_load_securvalue('sector_id', dims_const::_DIMS_NUM_INPUT, false, true);
						$type_id			= dims_load_securvalue('type_id', dims_const::_DIMS_NUM_INPUT, false, true);
						$datestart_day		= dims_load_securvalue('datestart_day', dims_const::_DIMS_NUM_INPUT, false, true);
						$datestart_month	= dims_load_securvalue('datestart_month', dims_const::_DIMS_NUM_INPUT, false, true);
						$datestart_year		= dims_load_securvalue('datestart_year', dims_const::_DIMS_NUM_INPUT, false, true);
						$dateend_day		= dims_load_securvalue('dateend_day', dims_const::_DIMS_NUM_INPUT, false, true);
						$dateend_month		= dims_load_securvalue('dateend_month', dims_const::_DIMS_NUM_INPUT, false, true);
						$dateend_year		= dims_load_securvalue('dateend_year', dims_const::_DIMS_NUM_INPUT, false, true);
						require_once DIMS_APP_PATH."modules/system/activity/class_activity.php";
						$opp = new dims_activity();
						$opp->open($id);
						$opp->fields['libelle'] = $label;
						$opp->fields['description'] = $description;
						$lieu = explode(',',$opp->fields['lieu']);
						if ($lieu[0] != $country){
							$setSql[] = "id_country = $country";
						}
						$opp->fields['lieu'] = $country;
						if ($city != '') $opp->fields['lieu'] .= ', '.$city;
						$opp->fields['type'] = dims_const::_PLANNING_ACTION_ACTIVITY;
						$opp->fields['activity_sector_id'] = $sector_id;
						$opp->fields['activity_type_id'] = $type_id;
						if ($datestart_month == 0) {
							$opp->fields['dateextended'] = dims_activity::DATE_FORMAT_AAAA;
						}
						elseif ($datestart_day == 0) {
							$opp->fields['dateextended'] = dims_activity::DATE_FORMAT_MMAAAA;
						}
						else {
							$opp->fields['dateextended'] = dims_activity::DATE_FORMAT_JJMMAAAA;
						}
						$dd = sprintf("%04d-%02d-%02d", $datestart_year, $datestart_month, $datestart_day);
						if ($dd != $opp->fields['datejour']){
							$setSql[] = "year = $datestart_year, month = $datestart_month";
						}
						$opp->fields['datejour'] = $dd;
						$opp->fields['datefin'] = sprintf("%04d-%02d-%02d", $dateend_year, $dateend_month, $dateend_day);
						// avatar
						if (!empty($_FILES['avatar']) && !$_FILES['avatar']['error']) {
                            require_once DIMS_APP_PATH."include/class_input_validator.php";
						    $valid = new \InVal\FileValidator('avatar');
                            $valid->rule(new \InVal\Rule\Image(true));

                            if ($valid->validate()) {
     							$file = $_FILES['avatar'];
     							if (!file_exists(_ACTIVITY_AVATAR_FILE_PATH))
     								dims_makedir(_ACTIVITY_AVATAR_FILE_PATH);

     							if ($file['size'] <= _ACTIVITY_AVATAR_MAX_SIZE) {
     								// suppression de l'ancien si présent
     								if ($opp->fields['banner_path'] != '' && file_exists(_ACTIVITY_AVATAR_FILE_PATH.$opp->fields['banner_path'])) {
     									unlink(_ACTIVITY_AVATAR_FILE_PATH.$opp->fields['banner_path']);
     								}

     								// ajout du nouveau
     								$file_type = explode('/', $file['type']);
     								move_uploaded_file($file['tmp_name'], _ACTIVITY_AVATAR_FILE_PATH.$opp->fields['id'].'_tmp.'.$file_type[1]);
     								dims_resizeimage(_ACTIVITY_AVATAR_FILE_PATH.$opp->fields['id'].'_tmp.'.$file_type[1], 0, _ACTIVITY_AVATAR_MAX_WIDTH, _ACTIVITY_AVATAR_MAX_HEIGHT, '', 0, _ACTIVITY_AVATAR_FILE_PATH.$opp->fields['id'].'.'.$file_type[1]);
     								// suppression du fichier temporaire
     								unlink(_ACTIVITY_AVATAR_FILE_PATH.$opp->fields['id'].'_tmp.'.$file_type[1]);

     								$opp->fields['banner_path'] = "."._ACTIVITY_AVATAR_WEB_PATH.$opp->fields['id'].'.'.$file_type[1];
     								$opp->save();
     							}
                            }
						}
						$id_link = dims_load_securvalue('link',dims_const::_DIMS_NUM_INPUT, false, true);
						if ($id_link > 0 && $id_link != ''){
							$act = new action();
							$act->open($id_link);
							$id_link = $act->fields['id_globalobject'];
						}
						//die($id_link);
						$db = dims::getInstance()->db;
						$opp->save();
						$tmps = dims_createtimestamp();
						$db->query("UPDATE dims_matrix SET id_action = :idaction, timestp_modify = :timestampmodify WHERE id_activity = :idactivity AND (id_action > 0 OR id_contact > 0)", array(
							':idaction' => array('type' => PDO::PARAM_INT, 'value' => $id_link),
							':timestampmodify' => array('type' => PDO::PARAM_INT, 'value' => $tmps),
							':idactivity' => array('type' => PDO::PARAM_INT, 'value' => $opp->fields['id_globalobject']),
						));
						if (count($setSql) > 0){
							$db->query("UPDATE dims_matrix SET ".implode(',',$setSql).", timestp_modify = :timestampmodify WHERE id_activity = :idactivity", array(
								':timestampmodify' => array('type' => PDO::PARAM_INT, 'value' => $tmps),
								':idactivity' => array('type' => PDO::PARAM_INT, 'value' => $opp->fields['id_globalobject']),
							));
						}
						dims_redirect($dims->getScriptEnv());
					}
					dims_redirect($dims->getScriptEnv()."?submenu=".dims_const_desktopv2::DESKTOP_V2_DESKTOP."&mode=default");
					break;
				case dims_const::_SYSTEM_OBJECT_OPPORTUNITY:
					if ($id != '' && $id > 0){
						$setSql = array();
						$label				= dims_load_securvalue('label', dims_const::_DIMS_CHAR_INPUT, false, true);
						$description		= dims_load_securvalue('description', dims_const::_DIMS_CHAR_INPUT, false, true);
						$country			= dims_load_securvalue('country', dims_const::_DIMS_NUM_INPUT, false, true);
						$city				= dims_load_securvalue('city', dims_const::_DIMS_NUM_INPUT, false, true);
						$sector_id			= dims_load_securvalue('sector_id', dims_const::_DIMS_NUM_INPUT, false, true);
						$type_id			= dims_load_securvalue('type_id', dims_const::_DIMS_NUM_INPUT, false, true);
						$datestart_day		= dims_load_securvalue('datestart_day', dims_const::_DIMS_NUM_INPUT, false, true);
						$datestart_month	= dims_load_securvalue('datestart_month', dims_const::_DIMS_NUM_INPUT, false, true);
						$datestart_year		= dims_load_securvalue('datestart_year', dims_const::_DIMS_NUM_INPUT, false, true);
						$dateend_day		= dims_load_securvalue('dateend_day', dims_const::_DIMS_NUM_INPUT, false, true);
						$dateend_month		= dims_load_securvalue('dateend_month', dims_const::_DIMS_NUM_INPUT, false, true);
						$dateend_year		= dims_load_securvalue('dateend_year', dims_const::_DIMS_NUM_INPUT, false, true);
						require_once DIMS_APP_PATH."modules/system/opportunity/class_opportunity.php";
						$opp = new dims_opportunity();
						$opp->open($id);
						$opp->fields['libelle'] = $label;
						$opp->fields['description'] = $description;
						$lieu = explode(',',$opp->fields['lieu']);
						if ($lieu[0] != $country){
							$setSql[] = "id_country = $country";
						}
						$opp->fields['lieu'] = $country;
						if ($city != '') $opp->fields['lieu'] .= ', '.$city;
						$opp->fields['type'] = dims_const::_PLANNING_ACTION_OPPORTUNITY;
						$opp->fields['opportunity_sector_id'] = $sector_id;
						$opp->fields['opportunity_type_id'] = $type_id;
						if ($datestart_month == 0) {
							$opp->fields['dateextended'] = dims_opportunity::DATE_FORMAT_AAAA;
						}
						elseif ($datestart_day == 0) {
							$opp->fields['dateextended'] = dims_opportunity::DATE_FORMAT_MMAAAA;
						}
						else {
							$opp->fields['dateextended'] = dims_opportunity::DATE_FORMAT_JJMMAAAA;
						}
						$dd = sprintf("%04d-%02d-%02d", $datestart_year, $datestart_month, $datestart_day);
						if ($dd != $opp->fields['datejour']){
							$setSql[] = "year = $datestart_year, month = $datestart_month";
						}
						$opp->fields['datejour'] = $dd;
						$opp->fields['datefin'] = sprintf("%04d-%02d-%02d", $dateend_year, $dateend_month, $dateend_day);
						// avatar
						if (!empty($_FILES['avatar']) && !$_FILES['avatar']['error']) {
							$file = $_FILES['avatar'];
							if (!file_exists(_OPPORTUNITY_AVATAR_FILE_PATH))
								dims_makedir(_OPPORTUNITY_AVATAR_FILE_PATH);

							if ($file['size'] <= _OPPORTUNITY_AVATAR_MAX_SIZE) {
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
								$opp->save();
							}
						}
						$id_link = dims_load_securvalue('link',dims_const::_DIMS_NUM_INPUT, false, true);
						if ($id_link > 0 && $id_link != ''){
							$act = new action();
							$act->open($id_link);
							$id_link = $act->fields['id_globalobject'];
						}
						//die($id_link);
						$db = dims::getInstance()->db;
						$opp->save();
						$tmps = dims_createtimestamp();
						$db->query("UPDATE dims_matrix SET id_action = :idlink, timestp_modify = :timestampmodify WHERE id_opportunity = :idopportunity AND (id_action > 0 OR id_contact > 0)", array(
							':idlink' => array('type' => PDO::PARAM_INT, 'value' => $id_link),
							':timestampmodify' => array('type' => PDO::PARAM_INT, 'value' => $tmps),
							':idopportunity' => array('type' => PDO::PARAM_INT, 'value' => $opp->fields['id_globalobject']),
						));
						if (count($setSql) > 0){
							$params = array();
							$params[':timestampmodify'] = array('type' => PDO::PARAM_INT, 'value' => $tmps);
							$params[':idopportunity'] = array('type' => PDO::PARAM_INT, 'value' => $opp->fields['id_globalobject']);
							$db->query("UPDATE dims_matrix SET ".implode(',',$setSql).", timestp_modify = :timestampmodify WHERE id_opportunity = :idopportunity", $params);
						}
						dims_redirect($dims->getScriptEnv());
					}
					dims_redirect($dims->getScriptEnv()."?submenu=".dims_const_desktopv2::DESKTOP_V2_DESKTOP."&mode=default");
					break;
			}
			die();
			break;
	case 'delete_opportunity':
		require_once DIMS_APP_PATH.'modules/system/opportunity/class_opportunity.php';
		$opp_id = dims_load_securvalue('opp_id', dims_const::_DIMS_NUM_INPUT, true, false);
		$opp = new dims_opportunity();
		if($opp->open($opp_id)) {
			// vérif des permissions
			$bDelete = false;
			if (
				($opp->fields['id_user'] == $_SESSION['dims']['userid'] && $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_LEAD_DELETE_OWNS))
				|| ($opp->fields['id_user'] != $_SESSION['dims']['userid'] && $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_LEAD_DELETE_OTHERS))
			) {
				$bDelete = true;
			}

			if ($bDelete) {
				$sql = 'DELETE FROM '.dims_opportunity::TABLE_NAME.' WHERE id_parent = '.$opp->getId();
				$opp->delete();
			}
		}

		dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=".dims_const_desktopv2::DESKTOP_V2_DESKTOP."&mode=leads&action=manage");
		break;
	case 'close_activity':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true);
		$submenu = dims_load_securvalue('submenu', dims_const::_DIMS_NUM_INPUT, true, true);

		if ($id > 0) {
			require_once DIMS_APP_PATH.'modules/system/activity/class_activity.php';
			$activity = new dims_activity();
			$activity->open($id);
			$activity->fields['close'] = 1;
			$activity->save();
			dims_redirect(dims::getInstance()->getScriptEnv()."?submenu=".(!empty($submenu) ? $submenu : dims_const_desktopv2::DESKTOP_V2_DESKTOP)."&mode=activity&action=manage");
		}
		die();
		break;
	case 'delete_activity':
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true);
		$submenu = dims_load_securvalue('submenu', dims_const::_DIMS_NUM_INPUT, true, true);

		$dims = dims::getInstance();

		if ($id > 0) {
			require_once DIMS_APP_PATH.'modules/system/activity/class_activity.php';
			$activity = new dims_activity();
			$activity->open($id);

			// vérif des permissions
			$bDelete = false;
			if (
				($activity->fields['id_user'] == $_SESSION['dims']['userid'] && $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_ACTIVITY_DELETE_OWNS))
				|| ($activity->fields['id_user'] != $_SESSION['dims']['userid'] && $dims->isActionAllowed(dims_const::_SYSTEM_ACTION_ACTIVITY_DELETE_OTHERS))
			) {
				$bDelete = true;
			}

			if ($bDelete) {
				$activity->delete();
			}
			dims_redirect($dims->getScriptEnv()."?submenu=".(!empty($submenu) ? $submenu : dims_const_desktopv2::DESKTOP_V2_DESKTOP)."&mode=activity&action=manage");
		}

		break;
	}
}

// liste des dernières personnes connectées
//$lstConn = $desktop->getFirstRecentConnexions();
$lstConn=array();

switch($submenu){
	default:
	case dims_const_desktopv2::DESKTOP_V2_DESKTOP:
		require_once _DESKTOP_TPL_LOCAL_PATH."/desktop_content.tpl.php";
		break;
	case dims_const_desktopv2::DESKTOP_V2_CONCEPTS:
		require_once _DESKTOP_TPL_LOCAL_PATH."/concepts/concepts.tpl.php";
		break;
}
?>
