<?
require_once DIMS_APP_PATH.'/modules/system/import/templates/view_import_factory.php';
require_once DIMS_APP_PATH.'/modules/system/import/templates/view_import_model.php';

if (!isset($_SESSION['desktopv2']['importa_data']['import_op'])) $_SESSION['desktopv2']['importa_data']['import_op']='';

$import_op = dims_load_securvalue('import_op',dims_const::_DIMS_NUM_INPUT,true,true,true,$_SESSION['desktopv2']['importa_data']['import_op']);
?>

<div class="zone_title_address_book">
	<div class="title_adress_book"><h1><?php echo $_SESSION['cste']['_LABEL_IMPORT']; ?></h1></div>
	<!--div class="title_setpage"><span>Set this page your home page</span></div-->
</div>
<div style="clear:both">
<?
//dims_print_r($import_op);die();
// select action to execute
switch($import_op){
	case _OP_MODULE_IMPORT:
		include _DESKTOP_TPL_LOCAL_PATH.'/import_data/models/controller.php';
		break;
	case _OP_DELETE_IMPORT:
		$id_import = dims_load_securvalue('id_import', dims_const::_DIMS_NUM_INPUT, true, true, true);

		if($id_import > 0){
			$import = new dims_import();
			$import->open($id_import);
			$import->delete();
		}
		dims_redirect('/admin.php?import_op='._OP_NEW_IMPORT);
		break;
	case _OP_NEW_IMPORT:
		include _DESKTOP_TPL_LOCAL_PATH.'/import_data/new_import.tpl.php';
		break;
	case _OP_UPLOAD_FILE:
		$id_fichier_modele = dims_load_securvalue('fichier_modele', dims_const::_DIMS_NUM_INPUT, true, true, true);
		$import= new dims_import();
		$import->setIdFichierModele($id_fichier_modele);

		$id_import = $import->loadImportFile(array(), $id_fichier_modele);
		dims_redirect('/admin.php?import_op='._OP_LOAD_IMPORT.'&id_import='.$id_import);
		break;
	case _OP_LOAD_IMPORT:
		$id_import = dims_load_securvalue('id_import', dims_const::_DIMS_NUM_INPUT, true, true, true);

		if($id_import > 0){
			$import = new dims_import();
			$import->open($id_import);
			$import->setLightAttribute('corresp',$import->executeCheckContactImport());
			$import->display(_DESKTOP_TPL_LOCAL_PATH.'/import_data/choose_corresp_fields.tpl.php');
			//controller_assurance_import_assure::loadDataFromTableTemp($import, $client);
		}else{
			dims_redirect('/admin.php?import_op='._OP_NEW_IMPORT);
		}
		break;
	case _OP_SAVE_PREFERENCE:
		$id_import = dims_load_securvalue('id_import', dims_const::_DIMS_NUM_INPUT, true, true, true);

		if($id_import > 0){
			$import = new dims_import();
			$import->open($id_import);
			$lstValues = array();
			$objects = dims_load_securvalue($_POST, dims_const::_DIMS_CHAR_INPUT, true, true, true);
			foreach($objects as $key => $val){
				if ($val != 'dims_nan') {
					switch(substr($key,0,8)){
						case 'field_ct':
							$lstValues[dims_const::_SYSTEM_OBJECT_CONTACT][substr($key,9)] = $val;
							break;
						case 'field_ti':
							$lstValues[dims_const::_SYSTEM_OBJECT_TIERS][substr($key,12)] = $val;
							break;
					}
				}
			}

			$u = new contact();
			$u->open($_SESSION['dims']['user']['id_contact']);

			$lstCtMatrice = array();
			require_once DIMS_APP_PATH."modules/system/import/class_check_fields.php";
			require_once DIMS_APP_PATH.'modules/system/class_ct_link.php';
			require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
			require_once DIMS_APP_PATH.'modules/system/class_country.php';
			require_once DIMS_APP_PATH.'modules/system/class_matrix.php';

			$statusTiers = _STATUS_IMPORT_ERR_TIERS;
			$statusContact = _STATUS_IMPORT_OK;
			$statusOkTiers = _STATUS_IMPORT_OK;
			if (isset($_POST['type_'.dims_const::_SYSTEM_OBJECT_TIERS]) && isset($lstValues[dims_const::_SYSTEM_OBJECT_TIERS]) && count($lstValues[dims_const::_SYSTEM_OBJECT_TIERS]) > 0)
				$statusContact = _STATUS_IMPORT_OK_CT;

			if (isset($_POST['type_'.dims_const::_SYSTEM_OBJECT_CONTACT]) && isset($lstValues[dims_const::_SYSTEM_OBJECT_CONTACT]) && count($lstValues[dims_const::_SYSTEM_OBJECT_CONTACT]) > 0){
				$statusTiers = _STATUS_IMPORT_ERR_CT_TIERS;
				$statusOkTiers = _STATUS_IMPORT_OK_TIERS;
				$contact = new contact();
				foreach($lstValues[dims_const::_SYSTEM_OBJECT_CONTACT] as $key => $val){
					$check = import_check_fields::create($key,$val,dims_const::_SYSTEM_OBJECT_CONTACT);
					$check->fields['nb_used']++;
					$check->save();
				}
				$lstContacts = $import->returnListObject('contact',dims_const::_SYSTEM_OBJECT_CONTACT,$lstValues[dims_const::_SYSTEM_OBJECT_CONTACT]);

				foreach($lstContacts as $contact){

					//dims_print_r($contact);die();
					$id_tmp = $contact->getLightAttribute('id_tmp');
					$tagused = $contact->getLightAttribute('tags');

					//dims_print_r($tagused);die();
					if (!empty($id_tmp) && $id_tmp > 0 && isset($contact->fields['firstname']) && $contact->fields['firstname']!="" && isset($contact->fields['lastname']) && $contact->fields['lastname']!=""){
						$res = $contact->mergeSave();
						if(is_array($res)){ // on a des doublons
							$sql = "UPDATE ".$import->getRefTmpTable()." SET status = :status WHERE id = :id";
							dims::getInstance()->db->query($sql, array(
								':status'	=> array('type' => PDO::PARAM_INT, 'value' => _STATUS_IMPORT_ERR_CT),
								':id'		=> array('type' => PDO::PARAM_INT, 'value' => $id_tmp)
							));
						}else{ // on est ok
							$sql = "UPDATE ".$import->getRefTmpTable()." SET id_contact = :idcontact, status = :status WHERE id = :id";
							dims::getInstance()->db->query($sql, array(
								':id' => array('type' => PDO::PARAM_INT, 'value' => $id_tmp),
								':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $res),
								':status' => array('type' => PDO::PARAM_INT, 'value' => $statusContact),
							));

							//dims_print_r($contact);
							//dims_print_r($u->fields);
							//die();
							$contact->updateIdCountry();
							/*if (is_null($contact->fields['id_globalobject'])) {
								dims_print_r($contact);die();
							}*/
							//dims_print_r($tagused);die();
							$import->attachTagsGo($contact->fields['id_globalobject'],$tagused);

							$lstCtMatrice[$id_tmp] = $contact;
							// liens matrice
							$country = country::getCountryFromLabel($contact->fields['country']);

							/*
							$matrice = new matrix();
							if ($contact->fields['id'] != $u->fields['id_globalobject']) {
								$matrice->fields['id_contact2'] = $contact->fields['id_globalobject'];
								$matrice->fields['id_contact'] = $u->fields['id_globalobject'];
								$matrice->fields['id_country'] = $country;
								$matrice->fields['year'] = substr($contact->getLightAttribute('date'),0,4);
								$matrice->fields['month'] = substr($contact->getLightAttribute('date'),4,2);
								$matrice->fields['timestp_modify'] = $contact->getLightAttribute('date');
								$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
								$matrice->save();
							}

							$ctlink = new ctlink();
							if ($contact->fields['id'] != $_SESSION['dims']['user']['id_contact']) {
								$ctlink->fields['id_contact1'] = $contact->fields['id'];
								$ctlink->fields['id_contact2'] = $_SESSION['dims']['user']['id_contact'];
								$ctlink->fields['id_object'] = dims_const::_SYSTEM_OBJECT_CONTACT;
								$ctlink->fields['type_link'] = 'business';
								$ctlink->fields['link_level'] = 2;
								$ctlink->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
								$ctlink->fields['id_user'] = $_SESSION['dims']['userid'];
								$ctlink->fields['date_deb'] = $contact->getLightAttribute('date');
								$ctlink->save();
							}

							$matrice = new matrix();
							if ($contact->fields['id'] != $u->fields['id_globalobject']) {
								$matrice->fields['id_contact'] = $contact->fields['id_globalobject'];
								$matrice->fields['id_contact2'] = $u->fields['id_globalobject'];
								$matrice->fields['id_country'] = $country;
								$matrice->fields['year'] = substr($contact->getLightAttribute('date'),0,4);
								$matrice->fields['month'] = substr($contact->getLightAttribute('date'),4,2);
								$matrice->fields['timestp_modify'] = $contact->getLightAttribute('date');
								$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
								$matrice->save();
							}

							$ctlink = new ctlink();
							if ($contact->fields['id'] != $_SESSION['dims']['user']['id_contact']) {
								$ctlink->fields['id_contact2'] = $contact->fields['id'];
								$ctlink->fields['id_contact1'] = $_SESSION['dims']['user']['id_contact'];
								$ctlink->fields['id_object'] = dims_const::_SYSTEM_OBJECT_CONTACT;
								$ctlink->fields['type_link'] = 'business';
								$ctlink->fields['link_level'] = 2;
								$ctlink->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
								$ctlink->fields['id_user'] = $_SESSION['dims']['userid'];
								$ctlink->fields['date_deb'] = $contact->getLightAttribute('date');
								$ctlink->save();
							}*/
						}
					}
				}
			}

			//dims_print_r($lstCtMatrice);
			//die();
			if (isset($_POST['type_'.dims_const::_SYSTEM_OBJECT_TIERS]) && isset($lstValues[dims_const::_SYSTEM_OBJECT_TIERS]) && count($lstValues[dims_const::_SYSTEM_OBJECT_TIERS]) > 0){
				foreach($lstValues[dims_const::_SYSTEM_OBJECT_TIERS] as $key => $val){
					$check = import_check_fields::create($key,$val,dims_const::_SYSTEM_OBJECT_TIERS);
					$check->fields['nb_used']++;
					$check->save();
				}
				$lstTiers = $import->returnListObject('tiers',dims_const::_SYSTEM_OBJECT_TIERS,$lstValues[dims_const::_SYSTEM_OBJECT_TIERS]);
				//dims_print_r($lstTiers);
				foreach($lstTiers as $contact){

					$id_tmp = $contact->getLightAttribute('id_tmp');
					if (!empty($id_tmp) && $id_tmp > 0){
						$res = $contact->mergeSave();
						if(is_array($res)){ // on a des doublons
							$sql = "UPDATE ".$import->getRefTmpTable()." SET status = :status WHERE id = :id";
							dims::getInstance()->db->query($sql, array(
								':status' => array('type' => PDO::PARAM_INT, 'value' => $statusTiers),
								':id' => array('type' => PDO::PARAM_INT, 'value' => $id_tmp),
							));
						}else{ // on est ok
							$sql = "UPDATE ".$import->getRefTmpTable()." SET id_tiers = :idtiers, status = :status WHERE id = :id";
							dims::getInstance()->db->query($sql, array(
								':idtiers' => array('type' => PDO::PARAM_INT, 'value' => $res),
								':status' => array('type' => PDO::PARAM_INT, 'value' => $statusOkTiers),
								':id' => array('type' => PDO::PARAM_INT, 'value' => $id_tiers),
							));

							$tagused=array();
							if (isset($lstCtMatrice[$id_tmp])) {
								// on crée le lien entre tiers & contact
								$contact2 = $lstCtMatrice[$id_tmp];
								$tagused = $contact2->getLightAttribute('tags');
							}

							$contact->updateIdCountry();
							$import->attachTagsGo($contact->fields['id_globalobject'],$tagused);

							// liens matrice
							$country = country::getCountryFromLabel($contact->fields['pays']);

							/*
							if (!isset($lstCtMatrice[$id_tmp]) ||
								($contact2->fields['id_globalobject'] != $u->fields['id_globalobject'] )) {
								$matrice = new matrix();
								$matrice->fields['id_tiers'] = $contact->fields['id_globalobject'];
								$matrice->fields['id_contact'] = $u->fields['id_globalobject'];
								$matrice->fields['id_country'] = $country;
								$matrice->fields['year'] = substr($contact->getLightAttribute('date'),0,4);
								$matrice->fields['month'] = substr($contact->getLightAttribute('date'),4,2);
								$matrice->fields['timestp_modify'] = $contact->getLightAttribute('date');
								$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
								$matrice->save();

								$tiersct = new tiersct();

								$found=false;
								$ctparams=array();
								$ctparams[':idcontact']=$_SESSION['dims']['user']['id_contact'];
								$ctparams[':idtiers']=$contact->fields['id'];

								$res= $db->query("select id from dims_mod_business_tiers_contact where id_contact=:idcontact and id_tiers=:idtiers",$ctparams);
								$idct=0;
								if ($db->numrows($res)>0) {
									if ($f=$db->fetchrow($res)) {
										$idct=$f['id'];

										$ctparams[':id']=$idct;
										$tiersct->open($idct);
										// on nettoie les autres relations
										$db->query('delete from dims_mod_business_tiers_contact where id_contact=:idcontact and id_tiers=:idtiers and id != :id',$ctparams);
									}
								}

								$tiersct->fields['id_tiers'] = $contact->fields['id'];
								$tiersct->fields['id_contact'] = $_SESSION['dims']['user']['id_contact'];

								if (isset($contact->JobTitle)) {
									$tiersct->fields['function'] = $contact->JobTitle;
									//die($contact->JobTitle);
								}

								$tiersct->fields['type_lien'] = 'Other';
								$tiersct->fields['link_level'] = 2;
								$tiersct->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
								$tiersct->fields['id_user'] = $_SESSION['dims']['userid'];
								$tiersct->fields['date_create'] = $contact->getLightAttribute('date');
								$tiersct->save();
							}*/

							if (isset($lstCtMatrice[$id_tmp])) {


								// on crée le lien entre tiers & contact
								$contact2 = $lstCtMatrice[$id_tmp];
								$tiersct = new tiersct();

								$found=false;
								$ctparams=array();
								$ctparams[':idcontact']=$contact2->fields['id'];
								$ctparams[':idtiers']=$contact->fields['id'];

								$res= $db->query("select id from dims_mod_business_tiers_contact where id_contact=:idcontact and id_tiers=:idtiers",$ctparams);
								$idct=0;
								if ($db->numrows($res)>0) {
									if ($f=$db->fetchrow($res)) {
										$idct=$f['id'];

										$ctparams[':id']=$idct;
										$tiersct->open($idct);
										// on nettoie les autres relations
										$db->query('delete from dims_mod_business_tiers_contact where id_contact=:idcontact and id_tiers=:idtiers and id != :id',$ctparams);
									}
								}


								$tiersct->fields['id_tiers'] = $contact->fields['id'];
								$tiersct->fields['id_contact'] = $contact2->fields['id'];
								$tiersct->fields['type_lien'] = 'employeur';

								if (isset($contact->JobTitle)) {
									$tiersct->fields['function'] = $contact->JobTitle;
									//die($contact->JobTitle);
								}

								$tiersct->fields['link_level'] = 2;
								$tiersct->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
								$tiersct->fields['id_user'] = $_SESSION['dims']['userid'];
								$tiersct->fields['date_create'] = $contact->getLightAttribute('date');
								$tiersct->save();

								if ($country == 0){
									$country = country::getCountryFromLabel($contact2->fields['country']);
								}
								$matrice = new matrix();
								$matrice->fields['id_tiers'] = $contact->fields['id_globalobject'];
								$matrice->fields['id_contact'] = $contact2->fields['id_globalobject'];
								$matrice->fields['id_country'] = $country;
								$matrice->fields['year'] = substr($contact->getLightAttribute('date'),0,4);
								$matrice->fields['month'] = substr($contact->getLightAttribute('date'),4,2);
								$matrice->fields['timestp_modify'] = $contact->getLightAttribute('date');
								$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
								$matrice->save();

							}
						}
					}
				}
			}
		}
		dims_redirect(dims::getInstance()->getScriptEnv()."?import_op="._OP_DEFAULT_IMPORT);
		break;

	case _OP_DEFAULT_IMPORT:
	default:
		//Si aucune action n'est définie alors on retourne à l'accueil de l'onglet.
		include _DESKTOP_TPL_LOCAL_PATH.'/import_data/accueil.tpl.php';
		break;
	case _OP_SAVE_HISTORY:
		include _DESKTOP_TPL_LOCAL_PATH.'/import_data/history.tpl.php';
		break;
	case _OP_MERGE_IMPORT:
		$id_import = dims_load_securvalue('id_import', dims_const::_DIMS_NUM_INPUT, true, true, true);

		if($id_import > 0){
			$import = new dims_import();
			$import->open($id_import);
			$import->display(_DESKTOP_TPL_LOCAL_PATH.'/import_data/display_merge_import.tpl.php');
		}else{
			dims_redirect('/admin.php?import_op='._OP_NEW_IMPORT);
		}
		break;
	case _OP_MERGE_IMPORT_SAVE:
		$id_column = dims_load_securvalue('id_column',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$id_import = dims_load_securvalue('id_import', dims_const::_DIMS_NUM_INPUT, true, true, true);
		$id_contact = dims_load_securvalue('id_contact', dims_const::_DIMS_NUM_INPUT, true, true, true);
		if (!empty($id_column) && $id_import > 0) {
			$action_save = dims_load_securvalue('action_save', dims_const::_DIMS_CHAR_INPUT, true, true);
			$page = dims_load_securvalue('page', dims_const::_DIMS_NUM_INPUT, true, true);
			$import = new dims_import();
			$import->open($id_import);
			$db = dims::getInstance()->db;

			$lstValues = array();
			$objects = dims_load_securvalue($_POST, dims_const::_DIMS_CHAR_INPUT, true, true, true);
			foreach($objects as $key => $val) {
				if ($val != 'dims_nan') {
					switch(substr($key,0,8)) {
						case 'field_ct':
							$lstValues[dims_const::_SYSTEM_OBJECT_CONTACT][substr($key,9)] = $val;
							break;
						case 'field_ti':
							$lstValues[dims_const::_SYSTEM_OBJECT_TIERS][substr($key,12)] = $val;
							break;
					}
				}
			}

			$sel = "SELECT	*
					FROM	".$import->getRefTmpTable()."
					WHERE	id = :id";
			$res = $db->query($sel, array(
				':id' => array('type' => PDO::PARAM_INT, 'value' => $id_column),
			));

			if ($r = $db->fetchrow($res)){
				require_once DIMS_APP_PATH.'modules/system/class_ct_link.php';
				require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
				require_once DIMS_APP_PATH.'modules/system/class_country.php';
				require_once DIMS_APP_PATH.'modules/system/class_matrix.php';

				$u = new contact();
				$u->open($_SESSION['dims']['user']['id_contact']);

				$ra = array_change_key_case($r, CASE_LOWER);
				$r3 = array_keys($ra);
				foreach($r3 as $k => $v)
					$r3[$k] = str_replace(array(" ","-","."),"_",$v);
				$ra = array_combine($r3,$r);

				if (isset($r['date']))
					$date = dims_local2timestamp($r["date"]);
				elseif (isset($r['Date']))
					$date = dims_local2timestamp($r["Date"]);
				else
					$date = dims_createtimestamp();
				switch($r['status']){
					case _STATUS_IMPORT_ERR_CT:
						if (!isset($_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_CONTACT])){
							$sql =	"
								SELECT		mf.*,mc.label as categlabel, mc.id as id_cat, mb.protected,mb.name as namefield,mb.label as titlefield
								FROM		dims_mod_business_meta_field as mf
								INNER JOIN	dims_mb_field as mb
								ON			mb.id=mf.id_mbfield
								RIGHT JOIN	dims_mod_business_meta_categ as mc
								ON			mf.id_metacateg=mc.id
								WHERE		mf.id_object = :idobject
								AND			mf.used=1
								ORDER BY	mc.position, mf.position
								";
							$res2 = $db->query($sql, array(
								':idobject' => dims_const::_SYSTEM_OBJECT_CONTACT
							));
							$lstChamps = array();
							$lstCateg = array();
							while ($r2 = $db->fetchrow($res2)){
								$ch = array();
								$ch['id_mtf'] = $r2['id'];
								$ch['namefield'] = $r2['namefield'];
								$ch['titlefield'] = $r2['titlefield'];
								$ch['name'] = $r2['name'];
								$ch['type'] = $r2['type'];
								$ch['format'] = $r2['format'];
								$ch['values'] = $r2['values'];
								$ch['maxlength'] = $r2['maxlength'];
								$ch['protected'] = $r2['protected'];
								$lstChamps[$r2['id']] = $ch;
							}
							$_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_CONTACT] = $lstChamps;
						}
						switch ($action_save){
							case 'import':
								if ($id_contact > 0){
									// TODO : mécanique d'import des données dans le contact existant
									$contact = new contact();
									$contact->open($id_contact);

									foreach($lstValues[dims_const::_SYSTEM_OBJECT_CONTACT] as $key => $val){
										if (isset($_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_CONTACT][$val]['namefield']) && isset($contact->fields[$_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_CONTACT][$val]['namefield']]))
											$contact->fields[$_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_CONTACT][$val]['namefield']] = $ra[$key];
									}
									$contact->save();
									$id_contact = $contact->fields['id'];




									$sql = "UPDATE ".$import->getRefTmpTable()." SET id_contact = :idcontact, status = :status WHERE id = :id";
									$db->query($sql, array(
										':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id_contact),
										':status' => array('type' => PDO::PARAM_INT, 'value' => _STATUS_IMPORT_OK),
										':id' => array('type' => PDO::PARAM_INT, 'value' => $r['id'])
									));

									$country = country::getCountryFromLabel($contact->fields['country']);

									$matrice = new matrix();
									$matrice->fields['id_contact2'] = $contact->fields['id_globalobject'];
									$matrice->fields['id_contact'] = $u->fields['id_globalobject'];
									$matrice->fields['id_country'] = $country;
									$matrice->fields['year'] = substr($date,0,4);
									$matrice->fields['month'] = substr($date,4,2);
									$matrice->fields['timestp_modify'] = $date;
									$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
									$matrice->save();

									$matrice = new matrix();
									$matrice->fields['id_contact'] = $contact->fields['id_globalobject'];
									$matrice->fields['id_contact2'] = $u->fields['id_globalobject'];
									$matrice->fields['id_country'] = $country;
									$matrice->fields['year'] = substr($date,0,4);
									$matrice->fields['month'] = substr($date,4,2);
									$matrice->fields['timestp_modify'] = $date;
									$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
									$matrice->save();

									$ctlink = new ctlink();
									$ctlink->fields['id_contact1'] = $contact->fields['id'];
									$ctlink->fields['id_contact2'] = $_SESSION['dims']['user']['id_contact'];
									$ctlink->fields['id_object'] = dims_const::_SYSTEM_OBJECT_CONTACT;
									$ctlink->fields['type_link'] = 'business';
									$ctlink->fields['link_level'] = 2;
									$ctlink->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
									$ctlink->fields['id_user'] = $_SESSION['dims']['userid'];
									$ctlink->fields['date_deb'] = $date;
									$ctlink->save();

									$ctlink = new ctlink();
									$ctlink->fields['id_contact2'] = $contact->fields['id'];
									$ctlink->fields['id_contact1'] = $_SESSION['dims']['user']['id_contact'];
									$ctlink->fields['id_object'] = dims_const::_SYSTEM_OBJECT_CONTACT;
									$ctlink->fields['type_link'] = 'business';
									$ctlink->fields['link_level'] = 2;
									$ctlink->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
									$ctlink->fields['id_user'] = $_SESSION['dims']['userid'];
									$ctlink->fields['date_deb'] = $date;
									$ctlink->save();
								}
								break;
							case 'origin':
								if ($id_contact > 0){
									$sql = "UPDATE ".$import->getRefTmpTable()." SET id_contact = :idcontact, status = :idobject WHERE id = :id";
									$db->query($sql, array(
										':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id_contact),
										':idobject' => array('type' => PDO::PARAM_INT, 'value' => _STATUS_IMPORT_OK),
										':id' => array('type' => PDO::PARAM_INT, 'value' => $r['id'])
									));

									$contact = new contact();
									$contact->open($id_contact);

									$country = country::getCountryFromLabel($contact->fields['country']);

									$matrice = new matrix();
									$matrice->fields['id_contact2'] = $contact->fields['id_globalobject'];
									$matrice->fields['id_contact'] = $u->fields['id_globalobject'];
									$matrice->fields['id_country'] = $country;
									$matrice->fields['year'] = substr($date,0,4);
									$matrice->fields['month'] = substr($date,4,2);
									$matrice->fields['timestp_modify'] = $date;
									$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
									$matrice->save();

									$matrice = new matrix();
									$matrice->fields['id_contact'] = $contact->fields['id_globalobject'];
									$matrice->fields['id_contact2'] = $u->fields['id_globalobject'];
									$matrice->fields['id_country'] = $country;
									$matrice->fields['year'] = substr($date,0,4);
									$matrice->fields['month'] = substr($date,4,2);
									$matrice->fields['timestp_modify'] = $date;
									$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
									$matrice->save();

									$ctlink = new ctlink();
									$ctlink->fields['id_contact1'] = $contact->fields['id'];
									$ctlink->fields['id_contact2'] = $_SESSION['dims']['user']['id_contact'];
									$ctlink->fields['id_object'] = dims_const::_SYSTEM_OBJECT_CONTACT;
									$ctlink->fields['type_link'] = 'business';
									$ctlink->fields['link_level'] = 2;
									$ctlink->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
									$ctlink->fields['id_user'] = $_SESSION['dims']['userid'];
									$ctlink->fields['date_deb'] = $date;
									$ctlink->save();

									$ctlink = new ctlink();
									$ctlink->fields['id_contact2'] = $contact->fields['id'];
									$ctlink->fields['id_contact1'] = $_SESSION['dims']['user']['id_contact'];
									$ctlink->fields['id_object'] = dims_const::_SYSTEM_OBJECT_CONTACT;
									$ctlink->fields['type_link'] = 'business';
									$ctlink->fields['link_level'] = 2;
									$ctlink->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
									$ctlink->fields['id_user'] = $_SESSION['dims']['userid'];
									$ctlink->fields['date_deb'] = $date;
									$ctlink->save();
								}
								break;
							case 'new':
								// on crée un nouveau contact
								$contact = new contact();
								$contact->init_description();
								$contact->setugm();

								foreach($lstValues[dims_const::_SYSTEM_OBJECT_CONTACT] as $key => $val){
									if (isset($_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_CONTACT][$val]['namefield']) && isset($contact->fields[$_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_CONTACT][$val]['namefield']]))
										$contact->fields[$_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_CONTACT][$val]['namefield']] = $ra[$key];
								}
								$contact->save();
								$id_contact = $contact->fields['id'];
								$sql = "UPDATE ".$import->getRefTmpTable()." SET id_contact = :idcontact, status = :idobject WHERE id = :id";
								$db->query($sql, array(
									':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id_contact),
									':idobject' => array('type' => PDO::PARAM_INT, 'value' => _STATUS_IMPORT_OK),
									':id' => array('type' => PDO::PARAM_INT, 'value' => $r['id'])
								));

								$country = country::getCountryFromLabel($contact->fields['country']);

								$matrice = new matrix();
								$matrice->fields['id_contact2'] = $contact->fields['id_globalobject'];
								$matrice->fields['id_contact'] = $u->fields['id_globalobject'];
								$matrice->fields['id_country'] = $country;
								$matrice->fields['year'] = substr($date,0,4);
								$matrice->fields['month'] = substr($date,4,2);
								$matrice->fields['timestp_modify'] = $date;
								$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
								$matrice->save();

								$matrice = new matrix();
								$matrice->fields['id_contact'] = $contact->fields['id_globalobject'];
								$matrice->fields['id_contact2'] = $u->fields['id_globalobject'];
								$matrice->fields['id_country'] = $country;
								$matrice->fields['year'] = substr($date,0,4);
								$matrice->fields['month'] = substr($date,4,2);
								$matrice->fields['timestp_modify'] = $date;
								$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
								$matrice->save();

								$ctlink = new ctlink();
								$ctlink->fields['id_contact1'] = $contact->fields['id'];
								$ctlink->fields['id_contact2'] = $_SESSION['dims']['user']['id_contact'];
								$ctlink->fields['id_object'] = dims_const::_SYSTEM_OBJECT_CONTACT;
								$ctlink->fields['type_link'] = 'business';
								$ctlink->fields['link_level'] = 2;
								$ctlink->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
								$ctlink->fields['id_user'] = $_SESSION['dims']['userid'];
								$ctlink->fields['date_deb'] = $date;
								$ctlink->save();

								$ctlink = new ctlink();
								$ctlink->fields['id_contact2'] = $contact->fields['id'];
								$ctlink->fields['id_contact1'] = $_SESSION['dims']['user']['id_contact'];
								$ctlink->fields['id_object'] = dims_const::_SYSTEM_OBJECT_CONTACT;
								$ctlink->fields['type_link'] = 'business';
								$ctlink->fields['link_level'] = 2;
								$ctlink->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
								$ctlink->fields['id_user'] = $_SESSION['dims']['userid'];
								$ctlink->fields['date_deb'] = $date;
								$ctlink->save();
								break;
						}
						break;
					case _STATUS_IMPORT_ERR_TIERS:
						if (!isset($_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_TIERS])){
							$sql =	"
								SELECT		mf.*,mc.label as categlabel, mc.id as id_cat, mb.protected,mb.name as namefield,mb.label as titlefield
								FROM		dims_mod_business_meta_field as mf
								INNER JOIN	dims_mb_field as mb
								ON			mb.id=mf.id_mbfield
								RIGHT JOIN	dims_mod_business_meta_categ as mc
								ON			mf.id_metacateg=mc.id
								WHERE		mf.id_object = :idobject
								AND			mf.used=1
								ORDER BY	mc.position, mf.position
								";
							$res2 = $db->query($sql, array(
								':idobject' => dims_const::_SYSTEM_OBJECT_TIERS
							));
							$lstChamps = array();
							$lstCateg = array();
							while ($r2 = $db->fetchrow($res2)){
								$ch = array();
								$ch['id_mtf'] = $r2['id'];
								$ch['namefield'] = $r2['namefield'];
								$ch['titlefield'] = $r2['titlefield'];
								$ch['name'] = $r2['name'];
								$ch['type'] = $r2['type'];
								$ch['format'] = $r2['format'];
								$ch['values'] = $r2['values'];
								$ch['maxlength'] = $r2['maxlength'];
								$ch['protected'] = $r2['protected'];
								$lstChamps[$r2['id']] = $ch;
							}
						}
						$country = 0;
						switch ($action_save){
							case 'import':
								if ($id_contact > 0){
									// TODO : mécanique d'import des données dans le tiers existant
									$tiers = new tiers();
									$tiers->open($id_contact);

									$_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_TIERS] = $lstChamps;
									foreach($lstValues[dims_const::_SYSTEM_OBJECT_TIERS] as $key => $val){
										if (isset($_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_TIERS][$val]['namefield']) && isset($tiers->fields[$_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_TIERS][$val]['namefield']]))
											$tiers->fields[$_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_TIERS][$val]['namefield']] = $ra[$key];
									}
									$tiers->save();
									$id_contact = $tiers->fields['id'];
									$sql = "UPDATE ".$import->getRefTmpTable()." SET id_tiers = :idcontact, status = :idobject WHERE id = :id";
									$db->query($sql, array(
										':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id_contact),
										':idobject' => array('type' => PDO::PARAM_INT, 'value' => _STATUS_IMPORT_OK),
										':id' => array('type' => PDO::PARAM_INT, 'value' => $r['id'])
									));

									$country = country::getCountryFromLabel($tiers->fields['pays']);

									$matrice = new matrix();
									$matrice->fields['id_tiers'] = $tiers->fields['id_globalobject'];
									$matrice->fields['id_contact'] = $u->fields['id_globalobject'];
									$matrice->fields['id_country'] = $country;
									$matrice->fields['year'] = substr($date,0,4);
									$matrice->fields['month'] = substr($date,4,2);
									$matrice->fields['timestp_modify'] = $date;
									$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
									$matrice->save();

									$tiersct = new tiersct();
									$tiersct->fields['id_tiers'] = $tiers->fields['id'];
									$tiersct->fields['id_contact'] = $_SESSION['dims']['user']['id_contact'];
									$tiersct->fields['type_lien'] = 'Other';
									$tiersct->fields['link_level'] = 2;
									$tiersct->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
									$tiersct->fields['id_user'] = $_SESSION['dims']['userid'];
									$tiersct->fields['date_create'] = $date;
									$tiersct->save();
								}
								break;
							case 'origin':
								if ($id_contact > 0){
									$sql = "UPDATE ".$import->getRefTmpTable()." SET id_tiers = :idcontact, status = :idobject WHERE id = :id";
									$db->query($sql, array(
										':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id_contact),
										':idobject' => array('type' => PDO::PARAM_INT, 'value' => _STATUS_IMPORT_OK),
										':id' => array('type' => PDO::PARAM_INT, 'value' => $r['id'])
									));

									$tiers = new contact();
									$tiers->open($id_contact);

									$country = country::getCountryFromLabel($tiers->fields['pays']);

									$matrice = new matrix();
									$matrice->fields['id_tiers'] = $tiers->fields['id_globalobject'];
									$matrice->fields['id_contact'] = $u->fields['id_globalobject'];
									$matrice->fields['id_country'] = $country;
									$matrice->fields['year'] = substr($date,0,4);
									$matrice->fields['month'] = substr($date,4,2);
									$matrice->fields['timestp_modify'] = $date;
									$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
									$matrice->save();

									$tiersct = new tiersct();
									$tiersct->fields['id_tiers'] = $tiers->fields['id'];
									$tiersct->fields['id_contact'] = $_SESSION['dims']['user']['id_contact'];
									$tiersct->fields['type_lien'] = 'Other';
									$tiersct->fields['link_level'] = 2;
									$tiersct->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
									$tiersct->fields['id_user'] = $_SESSION['dims']['userid'];
									$tiersct->fields['date_create'] = $date;
									$tiersct->save();
								}
								break;
							case 'new':
								// TODO : on crée un nouveau tiers
								$tiers = new tiers();
								$tiers->init_description();
								$tiers->setugm();

								$_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_TIERS] = $lstChamps;
								foreach($lstValues[dims_const::_SYSTEM_OBJECT_TIERS] as $key => $val){
									if (isset($_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_TIERS][$val]['namefield']) && isset($tiers->fields[$_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_TIERS][$val]['namefield']]))
										$tiers->fields[$_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_TIERS][$val]['namefield']] = $ra[$key];
								}
								$tiers->save();
								$id_contact = $tiers->fields['id'];
								$sql = "UPDATE ".$import->getRefTmpTable()." SET id_tiers = :idcontact, status = :idobject WHERE id = :id";
								$db->query($sql, array(
									':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id_contact),
									':idobject' => array('type' => PDO::PARAM_INT, 'value' => _STATUS_IMPORT_OK),
									':id' => array('type' => PDO::PARAM_INT, 'value' => $r['id'])
								));

								$country = country::getCountryFromLabel($tiers->fields['pays']);

								$matrice = new matrix();
								$matrice->fields['id_tiers'] = $tiers->fields['id_globalobject'];
								$matrice->fields['id_contact'] = $u->fields['id_globalobject'];
								$matrice->fields['id_country'] = $country;
								$matrice->fields['year'] = substr($date,0,4);
								$matrice->fields['month'] = substr($date,4,2);
								$matrice->fields['timestp_modify'] = $date;
								$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
								$matrice->save();

								$tiersct = new tiersct();
								$tiersct->fields['id_tiers'] = $tiers->fields['id'];
								$tiersct->fields['id_contact'] = $_SESSION['dims']['user']['id_contact'];
								$tiersct->fields['type_lien'] = 'Other';
								$tiersct->fields['link_level'] = 2;
								$tiersct->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
								$tiersct->fields['id_user'] = $_SESSION['dims']['userid'];
								$tiersct->fields['date_create'] = $date;
								$tiersct->save();
								break;
						}
						if ($r['id_contact'] > 0){
							$contact = new contact();
							$contact->open($r['id_contact']);

							// lien matrice tiers <-> contact
							$tiersct = new tiersct();
							$tiersct->fields['id_tiers'] = $tiers->fields['id'];
							$tiersct->fields['id_contact'] = $contact->fields['id'];
							$tiersct->fields['type_lien'] = 'employeur';
							$tiersct->fields['link_level'] = 2;
							$tiersct->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
							$tiersct->fields['id_user'] = $_SESSION['dims']['userid'];
							$tiersct->fields['date_create'] = $date;
							$tiersct->save();

							if ($country == 0){
								$country = country::getCountryFromLabel($contact->fields['country']);
							}
							$matrice = new matrix();
							$matrice->fields['id_tiers'] = $tiers->fields['id_globalobject'];
							$matrice->fields['id_contact'] = $contact->fields['id_globalobject'];
							$matrice->fields['id_country'] = $country;
							$matrice->fields['year'] = substr($date,0,4);
							$matrice->fields['month'] = substr($date,4,2);
							$matrice->fields['timestp_modify'] = $date;
							$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
							$matrice->save();
						}
						break;
					case _STATUS_IMPORT_ERR_CT_TIERS:
						if (!isset($_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_CONTACT])){
							$sql =	"
								SELECT		mf.*,mc.label as categlabel, mc.id as id_cat, mb.protected,mb.name as namefield,mb.label as titlefield
								FROM		dims_mod_business_meta_field as mf
								INNER JOIN	dims_mb_field as mb
								ON			mb.id=mf.id_mbfield
								RIGHT JOIN	dims_mod_business_meta_categ as mc
								ON			mf.id_metacateg=mc.id
								WHERE		mf.id_object = :idobject
								AND			mf.used=1
								ORDER BY	mc.position, mf.position
								";
							$res2 = $db->query($sql, array(
								':idobject' => dims_const::_SYSTEM_OBJECT_CONTACT
							));
							$lstChamps = array();
							$lstCateg = array();
							while ($r2 = $db->fetchrow($res2)){
								$ch = array();
								$ch['id_mtf'] = $r2['id'];
								$ch['namefield'] = $r2['namefield'];
								$ch['titlefield'] = $r2['titlefield'];
								$ch['name'] = $r2['name'];
								$ch['type'] = $r2['type'];
								$ch['format'] = $r2['format'];
								$ch['values'] = $r2['values'];
								$ch['maxlength'] = $r2['maxlength'];
								$ch['protected'] = $r2['protected'];
								$lstChamps[$r2['id']] = $ch;
							}
							$_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_CONTACT] = $lstChamps;
						}
						switch ($action_save){
							case 'import':
								if ($id_contact > 0){
									// TODO : mécanique d'import des données dans le contact existant
									$contact = new contact();
									$contact->open($id_contact);

									foreach($lstValues[dims_const::_SYSTEM_OBJECT_CONTACT] as $key => $val){
										if (isset($_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_CONTACT][$val]['namefield']) && isset($contact->fields[$_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_CONTACT][$val]['namefield']]))
											$contact->fields[$_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_CONTACT][$val]['namefield']] = $ra[$key];
									}
									$contact->save();
									$id_contact = $contact->fields['id'];
									$sql = "UPDATE ".$import->getRefTmpTable()." SET id_contact = :idcontact, status = :status WHERE id = :id";
									$db->query($sql, array(
										':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id_contact),
										':status' => array('type' => PDO::PARAM_INT, 'value' => _STATUS_IMPORT_ERR_TIERS),
										':id' => array('type' => PDO::PARAM_INT, 'value' => $r['id'])
									));

									$country = country::getCountryFromLabel($contact->fields['country']);

									$matrice = new matrix();
									$matrice->fields['id_contact2'] = $contact->fields['id_globalobject'];
									$matrice->fields['id_contact'] = $u->fields['id_globalobject'];
									$matrice->fields['id_country'] = $country;
									$matrice->fields['year'] = substr($date,0,4);
									$matrice->fields['month'] = substr($date,4,2);
									$matrice->fields['timestp_modify'] = $date;
									$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
									$matrice->save();

									$matrice = new matrix();
									$matrice->fields['id_contact'] = $contact->fields['id_globalobject'];
									$matrice->fields['id_contact2'] = $u->fields['id_globalobject'];
									$matrice->fields['id_country'] = $country;
									$matrice->fields['year'] = substr($date,0,4);
									$matrice->fields['month'] = substr($date,4,2);
									$matrice->fields['timestp_modify'] = $date;
									$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
									$matrice->save();

									$ctlink = new ctlink();
									$ctlink->fields['id_contact1'] = $contact->fields['id'];
									$ctlink->fields['id_contact2'] = $_SESSION['dims']['user']['id_contact'];
									$ctlink->fields['id_object'] = dims_const::_SYSTEM_OBJECT_CONTACT;
									$ctlink->fields['type_link'] = 'business';
									$ctlink->fields['link_level'] = 2;
									$ctlink->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
									$ctlink->fields['id_user'] = $_SESSION['dims']['userid'];
									$ctlink->fields['date_deb'] = $date;
									$ctlink->save();

									$ctlink = new ctlink();
									$ctlink->fields['id_contact2'] = $contact->fields['id'];
									$ctlink->fields['id_contact1'] = $_SESSION['dims']['user']['id_contact'];
									$ctlink->fields['id_object'] = dims_const::_SYSTEM_OBJECT_CONTACT;
									$ctlink->fields['type_link'] = 'business';
									$ctlink->fields['link_level'] = 2;
									$ctlink->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
									$ctlink->fields['id_user'] = $_SESSION['dims']['userid'];
									$ctlink->fields['date_deb'] = $date;
									$ctlink->save();
								}
								break;
							case 'origin':
								if ($id_contact > 0){
									$sql = "UPDATE ".$import->getRefTmpTable()." SET id_contact = :idcontact, status = :status WHERE id = :id";
									$db->query($sql, array(
										':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id_contact),
										':status' => array('type' => PDO::PARAM_INT, 'value' => _STATUS_IMPORT_ERR_TIERS),
										':id' => array('type' => PDO::PARAM_INT, 'value' => $r['id'])
									));

									$contact = new contact();
									$contact->open($id_contact);

									$country = country::getCountryFromLabel($contact->fields['country']);

									$matrice = new matrix();
									$matrice->fields['id_contact2'] = $contact->fields['id_globalobject'];
									$matrice->fields['id_contact'] = $u->fields['id_globalobject'];
									$matrice->fields['id_country'] = $country;
									$matrice->fields['year'] = substr($date,0,4);
									$matrice->fields['month'] = substr($date,4,2);
									$matrice->fields['timestp_modify'] = $date;
									$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
									$matrice->save();

									$matrice = new matrix();
									$matrice->fields['id_contact'] = $contact->fields['id_globalobject'];
									$matrice->fields['id_contact2'] = $u->fields['id_globalobject'];
									$matrice->fields['id_country'] = $country;
									$matrice->fields['year'] = substr($date,0,4);
									$matrice->fields['month'] = substr($date,4,2);
									$matrice->fields['timestp_modify'] = $date;
									$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
									$matrice->save();

									$ctlink = new ctlink();
									$ctlink->fields['id_contact1'] = $contact->fields['id'];
									$ctlink->fields['id_contact2'] = $_SESSION['dims']['user']['id_contact'];
									$ctlink->fields['id_object'] = dims_const::_SYSTEM_OBJECT_CONTACT;
									$ctlink->fields['type_link'] = 'business';
									$ctlink->fields['link_level'] = 2;
									$ctlink->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
									$ctlink->fields['id_user'] = $_SESSION['dims']['userid'];
									$ctlink->fields['date_deb'] = $date;
									$ctlink->save();

									$ctlink = new ctlink();
									$ctlink->fields['id_contact2'] = $contact->fields['id'];
									$ctlink->fields['id_contact1'] = $_SESSION['dims']['user']['id_contact'];
									$ctlink->fields['id_object'] = dims_const::_SYSTEM_OBJECT_CONTACT;
									$ctlink->fields['type_link'] = 'business';
									$ctlink->fields['link_level'] = 2;
									$ctlink->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
									$ctlink->fields['id_user'] = $_SESSION['dims']['userid'];
									$ctlink->fields['date_deb'] = $date;
									$ctlink->save();
								}
								break;
							case 'new':
								// TODO : on crée un nouveau contact
								$contact = new contact();
								$contact->init_description();
								$contact->setugm();

								foreach($lstValues[dims_const::_SYSTEM_OBJECT_CONTACT] as $key => $val){
									if (isset($_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_CONTACT][$val]['namefield']) && isset($contact->fields[$_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_CONTACT][$val]['namefield']]))
										$contact->fields[$_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_CONTACT][$val]['namefield']] = $ra[$key];
								}
								$contact->save();
								$id_contact = $contact->fields['id'];
								$sql = "UPDATE ".$import->getRefTmpTable()." SET id_contact = :idcontact, status = :status WHERE id = :id";
								$db->query($sql, array(
									':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id_contact),
									':status' => array('type' => PDO::PARAM_INT, 'value' => _STATUS_IMPORT_ERR_TIERS),
									':id' => array('type' => PDO::PARAM_INT, 'value' => $r['id'])
								));

								$country = country::getCountryFromLabel($contact->fields['country']);

								$matrice = new matrix();
								$matrice->fields['id_contact2'] = $contact->fields['id_globalobject'];
								$matrice->fields['id_contact'] = $u->fields['id_globalobject'];
								$matrice->fields['id_country'] = $country;
								$matrice->fields['year'] = substr($date,0,4);
								$matrice->fields['month'] = substr($date,4,2);
								$matrice->fields['timestp_modify'] = $date;
								$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
								$matrice->save();

								$matrice = new matrix();
								$matrice->fields['id_contact'] = $contact->fields['id_globalobject'];
								$matrice->fields['id_contact2'] = $u->fields['id_globalobject'];
								$matrice->fields['id_country'] = $country;
								$matrice->fields['year'] = substr($date,0,4);
								$matrice->fields['month'] = substr($date,4,2);
								$matrice->fields['timestp_modify'] = $date;
								$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
								$matrice->save();

								$ctlink = new ctlink();
								$ctlink->fields['id_contact1'] = $contact->fields['id'];
								$ctlink->fields['id_contact2'] = $_SESSION['dims']['user']['id_contact'];
								$ctlink->fields['id_object'] = dims_const::_SYSTEM_OBJECT_CONTACT;
								$ctlink->fields['type_link'] = 'business';
								$ctlink->fields['link_level'] = 2;
								$ctlink->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
								$ctlink->fields['id_user'] = $_SESSION['dims']['userid'];
								$ctlink->fields['date_deb'] = $date;
								$ctlink->save();

								$ctlink = new ctlink();
								$ctlink->fields['id_contact2'] = $contact->fields['id'];
								$ctlink->fields['id_contact1'] = $_SESSION['dims']['user']['id_contact'];
								$ctlink->fields['id_object'] = dims_const::_SYSTEM_OBJECT_CONTACT;
								$ctlink->fields['type_link'] = 'business';
								$ctlink->fields['link_level'] = 2;
								$ctlink->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
								$ctlink->fields['id_user'] = $_SESSION['dims']['userid'];
								$ctlink->fields['date_deb'] = $date;
								$ctlink->save();
								break;
						}
						break;
				}
			}
			dims_redirect(dims::getInstance()->getScriptEnv()."?import_op="._OP_MERGE_IMPORT."&id_import=".$id_import."&page=$page");
		}else
			dims_redirect(dims::getInstance()->getScriptEnv()."?import_op="._OP_SAVE_HISTORY);
		break;
}
?>
</div>
