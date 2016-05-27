<?php
require_once DIMS_APP_PATH.'modules/system/activity/class_type.php';

$weekadd=dims_load_securvalue('weekadd',dims_const::_DIMS_CHAR_INPUT,true,true);
$dayadd=dims_load_securvalue('dayadd',dims_const::_DIMS_CHAR_INPUT,true,true);
$monthadd=dims_load_securvalue('monthadd',dims_const::_DIMS_CHAR_INPUT,true,true);
$viewmode=dims_load_securvalue('viewmode',dims_const::_DIMS_CHAR_INPUT,true,true);

if(!isset($_SESSION['dims']['planning_scroll']) || $_SESSION['dims']['planning_scroll']== null){
	$_SESSION['dims']['planning_scroll'] = 8 * 42 + 1;//8h sur le planning
}
if ($weekadd!="")
	$_SESSION['dims']['planning']['weekadd'] = $weekadd;
else if(!isset($_SESSION['dims']['planning']['weekadd'])) $_SESSION['dims']['planning']['weekadd'] = 0;

if ($dayadd!="")
	$_SESSION['dims']['planning']['dayadd'] = $dayadd;
else if(!isset($_SESSION['dims']['planning']['dayadd'])) $_SESSION['dims']['planning']['dayadd'] = 0;

if ($monthadd!="")
	$_SESSION['dims']['planning']['monthadd'] = $monthadd;
else if(!isset($_SESSION['dims']['planning']['monthadd'])) $_SESSION['dims']['planning']['monthadd'] = 0;

if ($viewmode!="")
	$_SESSION['dims']['planning']['viewmode'] = $viewmode;
else if(!isset($_SESSION['dims']['planning']['viewmode'])) $_SESSION['dims']['planning']['viewmode'] = 'month';

$plan = new dims_planning();

$plan->setVisuMode($_SESSION['dims']['planning']['viewmode']);
$plan->setWeekAdd($_SESSION['dims']['planning']['weekadd']);
$week_updated = $plan->setDayAdd($_SESSION['dims']['planning']['dayadd']);
if($plan->getVisuMode()=='day'){
	$_SESSION['dims']['planning']['weekadd'] = $week_updated;
	$plan->setWeekAdd($_SESSION['dims']['planning']['weekadd'], true);
}

$plan->setMonthAdd($_SESSION['dims']['planning']['monthadd']);

$plan->definePlanningBornes();//permet de définir les bornes du planning (jour min et jour max)

//---------------- GESTION DES FILTRES D'AFFICHAGE SUR LEs CRENEAUx -------------------------------------------

$user_filter='';

//on regarde d'abord quel droit de partage on a
$cur_wksp = new workspace();
$cur_wksp->open($_SESSION['dims']['workspaceid']);

/*
 * CONSTRUCTION DU MODELE DE DONNEES A ENVOYER AU PLANNING
 */
for($i=$plan->getDateDebTimestp();$i<=$plan->getDateFinTimestp();$i+=(24*60*60)){//le pas est de 24H
	$curdate = date('Y-m-d', $i);
	$datejour_fr = date('d/m/Y', $i);
	// $typeJour = (isset($dayTypes[$i])) ? $dayTypes[$i] : $default_type->fields['id'];
	// $backgroundcolor = $types[$typeJour]['color'];
	$allowed = true; //($dims->isAdmin() || dims_isactionallowed(elisath_contexte::CREATE_UPDATE_CRENEAU)) && $curdate >= date('Y-m-d') && !$types[$typeJour]['blocking'];

	// proposition de rendez-vous
	if (isset($_SESSION['desktopv2']['appointment_offer'])) {
		$onclick = 'onclick="javascript:appointmentOfferSelectDay(\''.$curdate.'\');"';
	}
	else {
		$onclick = 'onclick="javascript:document.location.href=\'admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=activity&action=edit&from=planning&day='.$curdate.'\';"';
	}

	$plan->setDayAttributes($curdate, $allowed, $onclick); //, $backgroundcolor);
}

$params = array();

if (isset($_SESSION['desktopv2']['appointment_offer'])) {
	$sql = "
			SELECT		a.*
			FROM		dims_mod_business_action a
			WHERE		a.datejour BETWEEN :datestart AND :dateend
			AND			a.id_module = :idmodule
			AND			a.id_workspace = :idworkspace
			AND			a.is_model != 1
			AND			a.typeaction = :typeaction
			 ".$user_filter."
			GROUP BY	a.id
			ORDER BY	a.heuredeb, a.heurefin";
			$params[':typeaction'] = array('type' => PDO::PARAM_STR, 'value' => dims_const::_SYSTEM_OBJECT_APPOINTMENT_OFFER);
}
else {
	require_once DIMS_APP_PATH.'modules/system/activity/class_activity.php';
	$sql = "
			SELECT		a.*
			FROM		dims_mod_business_action a
			WHERE		a.datejour BETWEEN :datestart AND :dateend
			AND			a.id_module = :idmodule
			AND			a.id_workspace = :idworkspace
			AND			a.is_model != 1
			AND			a.typeaction LIKE :typeaction
			 ".$user_filter."
			GROUP BY	a.id
			ORDER BY	a.heuredeb, a.heurefin";
			$params[':typeaction'] = array('type' => PDO::PARAM_STR, 'value' => dims_activity::TYPE_ACTION);
}

$params[':datestart'] = array('type' => PDO::PARAM_STR, 'value' => $plan->getSimpleDateDeb());
$params[':dateend'] = array('type' => PDO::PARAM_STR, 'value' => $plan->getSimpleDateFin());
$params[':idmodule'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['moduleid']);
$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']);

$result = $db->query($sql, $params);
while ($fields = $db->fetchrow($result)) {
	require_once DIMS_APP_PATH.'modules/system/class_search.php';
	$matrix = new search();
	$linkedObjectsIds = $matrix->exploreMatrice($_SESSION['dims']['workspaceid'], null, array($fields['id_globalobject']));

	$a_objects = array( 'contacts' => array(), 'docs' => array() );
	if (!empty($linkedObjectsIds['distribution']['contacts'])) {
		$params = array();
		$rs = $db->query('
			SELECT		c.*, t.*
			FROM		dims_mod_business_contact c
			LEFT JOIN	dims_mod_business_tiers_contact tc
			ON			tc.id_contact = c.id
			AND			tc.type_lien = \'employer\'
			LEFT JOIN	dims_mod_business_tiers t
			ON			t.id = tc.id_tiers
			WHERE	c.id_globalobject IN ('.$db->getParamsFromArray(array_keys($linkedObjectsIds['distribution']['contacts']), 'idglobalobject', $params).')
			GROUP BY c.id', $params);
		if ($db->numrows($rs)) {
			foreach ($separation = $db->split_resultset($rs) as $sep) {
				if ($sep['c']['photo'] != '') {
					$contact = new contact();
					$contact->openFromResultSet($sep['c']);
					$sep['c']['photoPath'] = $contact->getPhotoWebPath(40);
				}
				else {
					$sep['c']['photoPath'] = _DESKTOP_TPL_PATH.'/gfx/common/human40.png';
				}

				if ($sep['t']['intitule'] == null) {
					$sep['t']['intitule'] = '';
				}

				// on ajoute le contact a la liste
				if (!isset($_SESSION['desktopv2']['activity']['ct_added'])) {
					$_SESSION['desktopv2']['activity']['ct_added'] = array();
				}
				$_SESSION['desktopv2']['activity']['ct_added'][$sep['c']['id_globalobject']] = $sep['c']['id_globalobject'];

				$a_objects['contacts'][] = $sep;
			}
		}
	}

	// valeurs arbitraires des heures si non renseignées
	if ($fields['heuredeb'] == '00:00:00') $fields['heuredeb'] = '08:00:00';
	if ($fields['heurefin'] == '00:00:00') $fields['heurefin'] = '18:00:00';

	$creneau = array();
	$creneau['heuredeb'] = $fields['heuredeb'];
	$creneau['datefin'] = $fields['datefin'];
	$creneau['heurefin'] = $fields['heurefin'];
	$creneau['creneau_title'] = $fields['libelle'];
	$creneau['creneau_id'] = $fields['id'];
	$creneau['activity_type_id'] = $fields['activity_type_id'];

	$status_pict = '<span style="font-style: italic;" class="sample circle">&nbsp;</span>&nbsp;';
	$creneau['text'] = $fields['libelle'].' > '.substr($fields['heuredeb'],0,2).":".substr($fields['heuredeb'],3,2)."&nbsp;".substr($fields['heurefin'],0,2).":".substr($fields['heurefin'],3,2);

	$creneau['participants'] = '';
	if(!empty($a_objects['contacts'])) {
		$totalContacts = count($a_objects['contacts']);

		$chunk_contact = array_slice($a_objects['contacts'], 0, 3); // Hardcoded value : number of element to show

		foreach($chunk_contact as $contact) {
			$creneau['participants'] .= '&nbsp;'.substr(ucfirst($contact['c']['firstname']), 0, 1).'. '.ucfirst($contact['c']['lastname']).'<br />';
		}
		if($totalContacts > count($chunk_contact)) {
			$creneau['participants'] .= '&nbsp; + '.($totalContacts - count($chunk_contact)).' '.$_SESSION['cste']['_DIMS_LABEL_OTHERS'];
		}
	}
	$creneau['desc'] = '';
	$creneau['dims_actionid'] = $fields['id'];

	// if(isset($_SESSION['desktopV2']['planning']['id_creneau']) && $_SESSION['desktopV2']['planning']['id_creneau'] == $creneau['creneau_id']) {
	//	$creneau['onclick'] = "onclick=\"javascript:openDetailCreneau(".$creneau['creneau_id'].", true);\"";
	//	$creneau['selected'] = true;
	// }
	// else $creneau['onclick'] = "onclick=\"javascript:openQuickView(".$creneau['creneau_id'].");\"";
	// $creneau['ondblclick'] ="ondblclick=\"javascript:openDetailCreneau(".$creneau['creneau_id'].", true);\"";
	$creneau['onclick'] = 'onclick="javascript:document.location.href=\'admin.php?submenu='._DESKTOP_V2_DESKTOP.'&mode=activity&action=view&activity_id='.$fields['id'].'&type='.dims_const::_SYSTEM_OBJECT_ACTIVITY.'&init_filters=1&from=planning\';"';
	$creneau['ondblclick'] = $creneau['onclick'];

	$plan->addDisplayedEvent($fields['datejour'], $creneau);
}

//----------------------------------------------------------------------------------
//dims_print_r($plan);


$plan->setDefaultTodayBGColor('#EAD671');
$plan->displayPlanning(DIMS_APP_PATH.'modules/system/desktopV2/templates/planning/planning_TABLE.tpl.php');
die();
