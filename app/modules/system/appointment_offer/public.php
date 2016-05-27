<?php
require_once DIMS_APP_PATH.'modules/system/class_matrix.php';
require_once DIMS_APP_PATH.'modules/system/class_country.php';
require_once DIMS_APP_PATH.'modules/system/class_city.php';
require_once DIMS_APP_PATH.'modules/system/class_dims_alert.php';
require_once DIMS_APP_PATH.'modules/system/appointment_offer/class_appointment_offer.php';
require_once DIMS_APP_PATH.'modules/system/appointment_offer/class_appointment_response.php';
require_once DIMS_APP_PATH.'modules/system/appointment_offer/class_appointment_response_value.php';

if(dims_load_securvalue('save',dims_const::_DIMS_CHAR_INPUT,true) == 'ok'){
	?>
	<script type="text/javascript" src="./common/js/jquery.slidingmessage.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$.showMessage("<? echo $_SESSION['cste']['_APPOINTMENT_SAVE_OK']; ?>", {position: 'top',
														size: 30,
														backgroundColor: '#D8FFD9',
														delay: 2000,
														speed: 2000,
														fontSize: '13px',
														border: "1px solid #00C700",
														color: "#00C700"});
		});
	</script>
	<?
}

// dossier temporaire de dépot des fichiers
$tmpDocFolder = DIMS_TMP_PATH.session_id().'/';


$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, true);

switch ($action) {
	case 'manage':
		$app_offer = new dims_appointment_offer();

		// suppression du dossier temporaire si existant
		dims_deletedir($tmpDocFolder);
		break;
	case 'delete':

		break;
	case 'valide':
		$app_offer_id = dims_load_securvalue('app_offer_id', dims_const::_DIMS_NUM_INPUT, true, true);
		$date = dims_load_securvalue('date', dims_const::_DIMS_NUM_INPUT, true, true);
		if ($app_offer_id > 0 && $date > 0) {
			$app_offer = new dims_appointment_offer();
			$app_offer->open($app_offer_id);
			$app_date = new dims_appointment_offer();
			$app_date->open($date);
			if ($app_date->fields['id_parent'] == $app_offer->fields['id']){
				$app_offer->fields['datejour'] = $app_date->fields['datejour'];
				$app_offer->fields['datefin'] = $app_date->fields['datefin'];
				$app_offer->fields['heuredeb'] = $app_date->fields['heuredeb'];
				$app_offer->fields['heurefin'] = $app_date->fields['heurefin'];
				$app_offer->fields['status'] = dims_appointment_offer::STATUS_VALIDATED;
				$app_offer->save();

				$arraymail = array();
				foreach($app_offer->getListRepCt() as $rep){
					if (trim($rep->fields['email']) != ''){
						$elem = array();
						$elem['name'] = $rep->fields['email'];
						$elem['address'] = $rep->fields['email'];
						$arraymail[] = $elem;
					}
				}

				$app_offer->sendMailValidation($arraymail);

				// TODO : faire une copie de l'event pour l'intégrer au calendrier de Dims
				$sel = "SELECT	*
						FROM	".dims_appointment_offer::TABLE_NAME."
						WHERE	id_parent = :idparent
						AND		type LIKE :type";
				$res = dims::getInstance()->db->query($sel, array(':idparent' => $app_offer->fields['id'], ':type' => dims_const::_PLANNING_ACTION_ACTIVITY) );
				require_once DIMS_APP_PATH.'modules/system/activity/class_activity.php';
				$activity = new dims_activity();
				if($r = dims::getInstance()->db->fetchrow($res))
					$activity->openFromResultSet($r);
				else{
					$activity->fields = $app_offer->fields;
					$activity->fields['id'] = 0;
					$activity->fields['id_globalobject'] = 0;
					$activity->fields['ref'] = '';
					$activity->fields['type'] = dims_const::_PLANNING_ACTION_ACTIVITY;
					$activity->fields['typeaction'] = dims_activity::TYPE_ACTION;
					$activity->fields['activity_type_id'] = -1;
					$activity->fields['id_responsible'] = $_SESSION['dims']['userid'];
					$activity->fields['id_parent'] = $app_offer->fields['id'];
				}
				$lstDoc = $app_offer->getLinkedDoc();
				if (count($lstDoc) > 0){
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
					foreach($lstDoc as $doc){
						$_SESSION['desktopv2']['activity']['doc_added'][$doc->fields['id_globalobject']] = $doc->fields['id_globalobject'];
					}
				}
				$activity->save(dims_const::_SYSTEM_OBJECT_ACTIVITY);

				$matrice = new matrix();
				$matrice->purgeData('id_activity', $activity->fields['id_globalobject']);

				$matrice->init_description();
				$matrice->fields['id_country'] = $activity->fields['id_country'];
				$matrice->fields['year'] = substr($activity->fields['datejour'], 0, 4);
				$matrice->fields['month'] = substr($activity->fields['datejour'], 5, 2);
				$matrice->fields['id_activity'] = $activity->fields['id_globalobject'];
				$matrice->fields['id_appointment_offer'] = $app_offer->fields['id_globalobject'];
				$matrice->fields['timestp_modify'] = dims_createtimestamp();
				$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
				$matrice->save();

				$sel = "SELECT		DISTINCT ct.id_globalobject
						FROM		".contact::TABLE_NAME." ct
						INNER JOIN	".dims_appointment_response::TABLE_NAME." rep
						ON			rep.id_contact = ct.id
						INNER JOIN	".dims_appointment_response_val::TABLE_NAME." val
						ON			val.id_reponse = rep.id
						WHERE		val.presence = 1
						AND			val.id_appointment = :idappointment
						AND			rep.go_appointment = :goappointment ";
				$res = dims::getInstance()->db->query($sel, array(':idappointment' => $app_date->fields['id'], ':goappointment' => $app_offer->fields['id_globalobject']) );
				while($r = dims::getInstance()->db->fetchrow($res)){
					$matrice = new matrix();
					$matrice->init_description();
					$matrice->fields['id_country'] = $activity->fields['id_country'];
					$matrice->fields['year'] = substr($activity->fields['datejour'], 0, 4);
					$matrice->fields['month'] = substr($activity->fields['datejour'], 5, 2);
					$matrice->fields['id_activity'] = $activity->fields['id_globalobject'];
					$matrice->fields['id_contact'] = $r['id_globalobject'];
					$matrice->fields['timestp_modify'] = dims_createtimestamp();
					$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
					$matrice->save();
				}

				// liens dans la matrice avec les documents liés
				if (!empty($_SESSION['desktopv2']['activity']['doc_added'])) {
					foreach ($_SESSION['desktopv2']['activity']['doc_added'] as $doc_id_go) {
						if ($doc_id_go != '' && $doc_id_go > 0){
							$matrice = new matrix();
							$matrice->init_description();
							$matrice->fields['id_country'] = $activity->fields['id_country'];
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
			}
		}
		dims_redirect($dims->getScriptEnv().'?mode=appointment_offer&action=manage');
		break;
	case 'edit':
		$_SESSION['desktopv2']['appointment_offer'] = array();
		$app_offer_id = dims_load_securvalue('app_offer_id', dims_const::_DIMS_NUM_INPUT, true, false);
		$app_offer = new dims_appointment_offer();
		$location = array();
		$added_contact = array();
		unset($_SESSION['desktopv2']['activity']['ct_added']);
		$_SESSION['desktopv2']['activity']['ct_added'] = array();

		if ($app_offer_id > 0) {
			$app_offer->open($app_offer_id);

			if ($app_offer->fields['type'] == dims_const::_PLANNING_ACTION_APPOINTMENT_OFFER) {
				$datestart = explode('-', $app_offer->fields['datejour']);
				$dateend = explode('-', $app_offer->fields['datefin']);
				$location = explode(',',$app_offer->fields['lieu']);
				$sel = "SELECT 		c.id
						FROM 		".contact::TABLE_NAME." c
						INNER JOIN 	dims_matrix m
						ON 			m.id_contact = c.id_globalobject
						WHERE 		m.id_appointment_offer = :go";
				$db = dims::getInstance()->getDb();
				$params = array(
					':go'=>array('value'=>$app_offer->get('id_globalobject'),'type'=>PDO::PARAM_INT),
				);
				$res = $db->query($sel,$params);
				while($r = $db->fetchrow($res)){
					$_SESSION['desktopv2']['activity']['ct_added'][$r['id']] = $r['id'];
				}
				$added_contact = $_SESSION['desktopv2']['activity']['ct_added']; // TODO : récupérer la liste des contacts liés array(tiers => fields, contacts => array(contact), ...)
			}
			else {
				$app_offer->init_description();
			}
		}
		else {
			$app_offer->init_description();
		}

		if (isset($app_offer)) {
			$app_offer->setLightAttribute('location',$location);
			$app_offer->setLightAttribute('added_contact',$added_contact);

			// chargement des pays
			$app_offer->setLightAttribute('a_countries', country::getAllCountries());
		}
		break;
	case 'select_dates':
		// enregistrement des fichiers dans un dossier temporaire
		if (sizeof($_FILES)) {
			$files = $_FILES['newDocs'];
			for ($i = 0; $i < sizeof($files['name']); $i++) {
				if (!$files['error'][$i]) {
					if (!is_dir($tmpDocFolder)) {
						dims_makedir($tmpDocFolder);
					}
					move_uploaded_file($files['tmp_name'][$i], $tmpDocFolder.$files['name'][$i]);
				}
			}
		}

		if(dims_load_securvalue('auto_add', dims_const::_DIMS_NUM_INPUT, true, true)){
			$ct = new contact();
			$ct->open($_SESSION['dims']['user']['id_contact']);
			if(!isset($_SESSION['desktopv2']['appointment_offer']['ct_added']))
				$_SESSION['desktopv2']['appointment_offer']['ct_added'] = array();
			if(!in_array($ct->fields['id'], $_SESSION['desktopv2']['appointment_offer']['ct_added']))
				$_SESSION['desktopv2']['appointment_offer']['ct_added'][] = $ct->fields['id'];
		}

		$_SESSION['desktopv2']['appointment_offer']['id']				= dims_load_securvalue('app_offer_id', dims_const::_DIMS_NUM_INPUT, true, true);
		$_SESSION['desktopv2']['appointment_offer']['label']			= dims_load_securvalue('app_offer_label', dims_const::_DIMS_CHAR_INPUT, true, true);
		$_SESSION['desktopv2']['appointment_offer']['address']			= dims_load_securvalue('app_offer_address', dims_const::_DIMS_CHAR_INPUT, true, true);
		$_SESSION['desktopv2']['appointment_offer']['cp']				= dims_load_securvalue('app_offer_cp', dims_const::_DIMS_CHAR_INPUT, true, true);
		$_SESSION['desktopv2']['appointment_offer']['country_id']		= dims_load_securvalue('app_offer_country_id', dims_const::_DIMS_NUM_INPUT, true, true);
		$_SESSION['desktopv2']['appointment_offer']['city_id']			= dims_load_securvalue('app_offer_city_id', dims_const::_DIMS_NUM_INPUT, true, true);
		$_SESSION['desktopv2']['appointment_offer']['alert_mode']		= dims_load_securvalue('app_offer_alert_mode', dims_const::_DIMS_NUM_INPUT, true, true);
		$_SESSION['desktopv2']['appointment_offer']['alert_nb_period']	= dims_load_securvalue('app_offer_alert_nb_period', dims_const::_DIMS_NUM_INPUT, true, true);
		$_SESSION['desktopv2']['appointment_offer']['alert_period']		= dims_load_securvalue('app_offer_alert_period', dims_const::_DIMS_CHAR_INPUT, true, true);
		$_SESSION['desktopv2']['appointment_offer']['alert_date']		= dims_load_securvalue('app_offer_alert_date', dims_const::_DIMS_CHAR_INPUT, true, true);
		$_SESSION['desktopv2']['appointment_offer']['alert_hour']		= dims_load_securvalue('app_offer_alert_hour', dims_const::_DIMS_CHAR_INPUT, true, true);
		$_SESSION['desktopv2']['appointment_offer']['alert_mins']		= dims_load_securvalue('app_offer_alert_mins', dims_const::_DIMS_CHAR_INPUT, true, true);
		$_SESSION['desktopv2']['appointment_offer']['private']			= dims_load_securvalue('app_offer_private', dims_const::_DIMS_NUM_INPUT, true, true);

		// on renvoie sur le planning que si on veut modifier les dates
		$redir = dims_load_securvalue('redir', dims_const::_DIMS_CHAR_INPUT, false, true);

		if ($redir == 'planning') {
			dims_redirect($dims->getScriptEnv().'?submenu='.dims_const_desktopv2::DESKTOP_V2_DESKTOP.'&mode=planning');
		}
		elseif ($redir == 'list') {
			dims_redirect($dims->getScriptEnv().'?submenu='.dims_const_desktopv2::DESKTOP_V2_DESKTOP.'&mode=appointment_offer&action=save');
		}

		break;
	case 'save':
		// recherche de la 1e date
		function cmp($a, $b) {
			if ($a['datefrom'] == $b['datefrom']) return 0;
			return ($a['datefrom'] < $b['datefrom']) ? -1 : 1;
		}

		usort($_SESSION['desktopv2']['appointment_offer']['days'], 'cmp');
		foreach ($_SESSION['desktopv2']['appointment_offer']['days'] as $day) {
			$_SESSION['desktopv2']['appointment_offer']['date_from'] = $day['datefrom'];
			$_SESSION['desktopv2']['appointment_offer']['date_to'] = $day['datefrom'];
			$_SESSION['desktopv2']['appointment_offer']['hour_from'] = $day['heuredeb'];
			$_SESSION['desktopv2']['appointment_offer']['hour_to'] = $day['heurefin'];
			break;
		}

		$country = new country();
		$country->open($_SESSION['desktopv2']['appointment_offer']['country_id']);

		$app_offer = new dims_appointment_offer();
		$app_offer->init_description();
		if ($_SESSION['desktopv2']['appointment_offer']['id'] > 0) {
			$app_offer->open($_SESSION['desktopv2']['appointment_offer']['id']);
		}
		else {
			$app_offer->setugm();
		}

		// ville
		if ($_SESSION['desktopv2']['appointment_offer']['city_id']) {
			$city = new city();
			$city->open($_SESSION['desktopv2']['appointment_offer']['city_id']);
			$app_offer->fields['lieu'] = $city->fields['label'];
			$app_offer->fields['id_city'] = $city->fields['id'];
		}
		else {
			$app_offer->fields['lieu'] = '';
			$app_offer->fields['id_city'] = 0;
		}

		$id_country=0;
		if (isset($country->fields['id']) && $country->fields['id']>0) $id_country=$country->fields['id'];
		if (!isset($_SESSION['desktopv2']['appointment_offer']['private'])) $_SESSION['desktopv2']['appointment_offer']['private']=false;

		$app_offer->fields['libelle'] = str_replace('"', "'", $_SESSION['desktopv2']['appointment_offer']['label']);
		$app_offer->fields['id_country'] = $id_country;
		$app_offer->fields['address'] = $_SESSION['desktopv2']['appointment_offer']['address'];
		$app_offer->fields['cp'] = $_SESSION['desktopv2']['appointment_offer']['cp'];
		$app_offer->fields['type'] = dims_const::_PLANNING_ACTION_APPOINTMENT_OFFER;
		$app_offer->fields['typeaction'] = dims_appointment_offer::TYPE_ACTION;
		$app_offer->fields['private'] = $_SESSION['desktopv2']['appointment_offer']['private'];
		$app_offer->save(dims_const::_SYSTEM_OBJECT_APPOINTMENT_OFFER);


		// documents ajoutés
		if (file_exists($tmpDocFolder)) {
			// création du dossier si inexistant
			require_once DIMS_APP_PATH.'modules/doc/class_docfolder.php';
			$folder = new docfolder();
			if (!$app_offer->fields['dossier_id'] || !$folder->open($app_offer->fields['dossier_id']) ) {
				$folder->fields['name'] = 'activity_'.$app_offer->fields['id'];
				$folder->fields['foldertype'] = 'public';
				$folder->fields['readonly'] = 0;
				$folder->fields['readonly_content'] = 0;
				$folder->fields['parents'] = 0;
				$folder->fields['nbelements'] = 0;
				$folder->fields['published'] = 1;
				$folder->fields['id_folder'] = 0;
				$folder->setugm();
				$folder->save();

				$app_offer->fields['dossier_id'] = $folder->fields['id'];
				$app_offer->save(dims_const::_SYSTEM_OBJECT_APPOINTMENT_OFFER);
			}

			// enregistrement des documents
			if ($handle = opendir($tmpDocFolder)) {
				while (false !== ($entry = readdir($handle))) {
					$entry = addslashes($entry);
					if (is_file($tmpDocFolder.$entry)) {
						$doc = new docfile();
						$doc->init_description();
						$doc->fields['name'] = $entry;
						$doc->tmpuploadedfile = $tmpDocFolder.$entry;
						$doc->fields['size'] = filesize($tmpDocFolder.$entry);
						$doc->fields['id_folder'] = $app_offer->fields['dossier_id'];
						$doc->setugm();
						$doc->save();

						$_SESSION['desktopv2']['appointment_offer']['doc_added'][$doc->fields['id_globalobject']] = $doc->fields['id_globalobject'];
					}
				}
				closedir($handle);
			}

			// suppression du dossier temporaire
			rmdir($tmpDocFolder);
		}

		// on crée une action par date proposée
		// chargement de toutes les dates proposées
		$children = $app_offer->getChildren();

		foreach ($_SESSION['desktopv2']['appointment_offer']['days'] as $day) {
			// on ne touche pas aux existantes
			if (isset($children[$day['datefrom']][$day['heuredeb']][$day['heurefin']])) {
				unset($children[$day['datefrom']][$day['heuredeb']][$day['heurefin']]);
			}
			// on crée les nouvelles
			else {
				$ss_app_offer = new dims_appointment_offer();
				$ss_app_offer->init_description();
				$ss_app_offer->setugm();
				$ss_app_offer->fields['id_parent'] = $app_offer->getId();
				$ss_app_offer->fields['libelle'] = str_replace('"', "'", $_SESSION['desktopv2']['appointment_offer']['label']);
				$ss_app_offer->fields['id_country'] = $country->fields['id'];
				$ss_app_offer->fields['address'] = $_SESSION['desktopv2']['appointment_offer']['address'];
				$ss_app_offer->fields['cp'] = $_SESSION['desktopv2']['appointment_offer']['cp'];
				$ss_app_offer->fields['type'] = dims_const::_PLANNING_ACTION_APPOINTMENT_OFFER;
				$ss_app_offer->fields['typeaction'] = dims_appointment_offer::TYPE_ACTION;
				$ss_app_offer->fields['datejour'] = $day['datefrom'];
				$ss_app_offer->fields['datefin'] = $day['datefrom'];
				$ss_app_offer->fields['heuredeb'] = $day['heuredeb'];
				$ss_app_offer->fields['heurefin'] = $day['heurefin'];
				$ss_app_offer->save(dims_const::_SYSTEM_OBJECT_APPOINTMENT_OFFER);
			}
		}

		// on supprime celles qui n'existent plus
		foreach ($children as $datefrom => $a_heuresdeb) {
			foreach ($a_heuresdeb as $heuredeb => $a_heuresfin) {
				foreach ($a_heuresfin as $heurefin => $ss_app_offer) {
					$ss_app_offer->delete();
				}
			}
		}


		// alerte email
		$a_alerts = dims_alert::getAllByGOOrigin($app_offer->fields['id_globalobject']);

		$alert = new dims_alert();
		if (sizeof($a_alerts)) {
			$alert->open($a_alerts[0]->getId());
		}
		else {
			$alert->init_description(true);
		}

		if ($_SESSION['desktopv2']['appointment_offer']['alert_mode'] > 0) {
			// lien vers l'activité
			$alert->setGOOrigin($app_offer->fields['id_globalobject']);

			switch ($_SESSION['desktopv2']['appointment_offer']['alert_mode']) {
				case dims_alert::MODE_ABSOLUTE:
					if ($_SESSION['desktopv2']['appointment_offer']['alert_date'] != '' && $_SESSION['desktopv2']['appointment_offer']['alert_hour'] != '' && $_SESSION['desktopv2']['appointment_offer']['alert_mins'] != '') {
						$alert->setAbsolute($_SESSION['desktopv2']['appointment_offer']['alert_date'], $_SESSION['desktopv2']['appointment_offer']['alert_hour'].':'.$_SESSION['desktopv2']['appointment_offer']['alert_mins'].':00');
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
		$matrice->purgeData('id_appointment_offer', $app_offer->fields['id_globalobject']);

		$matrice->init_description();
		$matrice->fields['id_country'] = $id_country;
		$matrice->fields['year'] = substr($_SESSION['desktopv2']['appointment_offer']['date_from'], 0, 4);
		$matrice->fields['month'] = substr($_SESSION['desktopv2']['appointment_offer']['date_from'], 5, 2);
		$matrice->fields['id_appointment_offer'] = $app_offer->fields['id_globalobject'];
		$matrice->fields['timestp_modify'] = dims_createtimestamp();
		$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
		$matrice->save();

		$work = new workspace();
		$work->open($_SESSION['dims']['workspaceid']);
		$domain = current($work->getFrontDomains());
		if ($domain['ssl'])
			$lk = "https://".$domain['domain'];
		else
			$lk = "http://".$domain['domain'];

		if (!isset($_SESSION['desktopv2']['appointment_offer']['ct_added']) &&
			isset($_SESSION['desktopv2']['activity']['ct_added']) ) {
			$_SESSION['desktopv2']['appointment_offer']['ct_added']=$_SESSION['desktopv2']['activity']['ct_added'];
		}

		$listTiersassoc=array(); // not adding already attach tiers

		// liens dans la matrice avec les contacts liés
		if (!empty($_SESSION['desktopv2']['appointment_offer']['ct_added'])) {
			foreach ($_SESSION['desktopv2']['appointment_offer']['ct_added'] as $elem) {
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
						$matrice->fields['id_country'] = $id_country;
						$matrice->fields['year'] = substr($_SESSION['desktopv2']['appointment_offer']['date_from'], 6, 4);
						$matrice->fields['month'] = substr($_SESSION['desktopv2']['appointment_offer']['date_from'], 3, 2);
						$matrice->fields['id_appointment_offer'] = $app_offer->fields['id_globalobject'];
						$matrice->fields['id_contact'] = $contact_id_go;
						$matrice->fields['id_tiers'] = $tiers_id_go;
						$matrice->fields['timestp_modify'] = dims_createtimestamp();
						$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
						$matrice->save();

						// liaison avec l'entreprise
						if ($tiers_id_go>0 && !isset($listTiersassoc[$elem['src']])) {

							$matrice = new matrix();
							$matrice->init_description();
							$matrice->fields['id_country'] = $id_country;
							$matrice->fields['year'] = substr($_SESSION['desktopv2']['appointment_offer']['date_from'], 6, 4);
							$matrice->fields['month'] = substr($_SESSION['desktopv2']['appointment_offer']['date_from'], 3, 2);
							$matrice->fields['id_appointment_offer'] = $app_offer->fields['id_globalobject'];
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
					$ct=new contact();
					$ct->open($elem);
					if(!$ct->isNew()){
						$contact_id_go=$ct->fields['id_globalobject'];

						$matrice = new matrix();
						$matrice->init_description();
						$matrice->fields['id_country'] = $id_country;
						$matrice->fields['year'] = substr($_SESSION['desktopv2']['appointment_offer']['date_from'], 0, 4);
						$matrice->fields['month'] = substr($_SESSION['desktopv2']['appointment_offer']['date_from'], 5, 2);
						$matrice->fields['id_appointment_offer'] = $app_offer->fields['id_globalobject'];
						$matrice->fields['id_contact'] = $contact_id_go;
						$matrice->fields['timestp_modify'] = dims_createtimestamp();
						$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
						$matrice->save();
					}
				}
			}
			$app_offer->sendMailInvitation($_SESSION['desktopv2']['appointment_offer']['ct_added']);
			unset($_SESSION['desktopv2']['appointment_offer']['ct_added']);
		}

		// liens dans la matrice avec les documents liés
		if (!empty($_SESSION['desktopv2']['appointment_offer']['doc_added'])) {
			foreach ($_SESSION['desktopv2']['appointment_offer']['doc_added'] as $doc_id_go) {
				if ($doc_id_go != '' && $doc_id_go > 0){
					$matrice = new matrix();
					$matrice->init_description();
					$matrice->fields['id_country'] = $country->fields['id'];
					$matrice->fields['year'] = substr($_SESSION['desktopv2']['appointment_offer']['date_from'], 0, 4);
					$matrice->fields['month'] = substr($_SESSION['desktopv2']['appointment_offer']['date_from'], 5, 2);
					$matrice->fields['id_appointment_offer'] = $app_offer->fields['id_globalobject'];
					$matrice->fields['id_doc'] = $doc_id_go;
					$matrice->fields['timestp_modify'] = dims_createtimestamp();
					$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
					$matrice->save();
				}
			}
			unset($_SESSION['desktopv2']['appointment_offer']['doc_added']);
		}

		$redi = dims_load_securvalue('redirection',dims_const::_DIMS_NUM_INPUT,true,true,true);
		switch($redi){
			default:
			case 0 :
				// dims_redirect($dims->getScriptEnv().'?mode=appointment_offer&action=edit&app_offer_id='.$app_offer->getId()."&save=ok");
				dims_redirect($dims->getScriptEnv().'?mode=appointment_offer&action=manage&save=ok');
				break;
			case 1 :
				dims_redirect($dims->getScriptEnv().'?mode=appointment_offer&action=edit&save=ok');
				break;
		}
		break;
	case 'send_reminder':
		$app_offer_id = dims_load_securvalue('app_offer_id', dims_const::_DIMS_NUM_INPUT, true, true);
		if ($app_offer_id > 0) {
			$app_offer = new dims_appointment_offer();
			$app_offer->open($app_offer_id);

			$dest = dims_load_securvalue('dest', dims_const::_DIMS_NUM_INPUT, true, true);
			$no_rep = ($dest == 2);

			$app_offer->sendMailRappel($app_offer->getLinkedContacts($no_rep));
		}
		dims_redirect($dims->getScriptEnv().'?mode=appointment_offer&action=edit&app_offer_id='.$app_offer_id);
		break;
}
