<?php
define('_PLANNING_H_HEIGHT',		24);	// hauteur en px d'une heure
define('_PLANNING_LEFTCOL_WIDTH',	30);	// largeur en px de la colonne des heures
define('_PLANNING_CAL_DAY_WIDTH',	140);	// largeur en px d'une journ�e (affichage mois)
define('_PLANNING_CAL_DAY_HEIGHT',	80);	// hauteur en px d'une journ�e (affichage mois)

$userid=dims_load_securvalue('userid',dims_const::_DIMS_NUM_INPUT,true,true);
$groupid=dims_load_securvalue('groupid',dims_const::_DIMS_NUM_INPUT,true,true);

$weekadd=dims_load_securvalue('weekadd',dims_const::_DIMS_CHAR_INPUT,true,true);
$dayadd=dims_load_securvalue('dayadd',dims_const::_DIMS_CHAR_INPUT,true,true);
$monthadd=dims_load_securvalue('monthadd',dims_const::_DIMS_CHAR_INPUT,true,true);
$viewmode=dims_load_securvalue('viewmode',dims_const::_DIMS_CHAR_INPUT,true,true);
$minimonthadd=dims_load_securvalue('minimonthadd',dims_const::_DIMS_CHAR_INPUT,true,true);

// ceux de l'espace courant
if ($groupid>0) {
	$_SESSION['business']['business_groupid'] = dims_load_securvalue('groupid', dims_const::_DIMS_NUM_INPUT, true, true, true);
	$_SESSION['business']['business_userid'] = 0;
}

if ($userid>0) $_SESSION['business']['business_userid'] = dims_load_securvalue('userid', dims_const::_DIMS_NUM_INPUT, true, true, true);
if ($weekadd!="") {
	$_SESSION['business']['business_viewmode']='week';
	$_SESSION['business']['business_monthadd'] = 0;
	 $_SESSION['business']['business_dayadd'] = 0;
	$_SESSION['business']['business_weekadd'] = $weekadd;
	if ($weekadd==0) $_SESSION['business']['business_minimonthadd']=0;
}

if ($dayadd!="") $_SESSION['business']['business_dayadd'] = $dayadd;
if ($monthadd!="") $_SESSION['business']['business_monthadd'] = $monthadd;
if ($viewmode!="") $_SESSION['business']['business_viewmode'] = $viewmode;

if (!isset($_SESSION['business']['business_groupid'])) $_SESSION['business']['business_groupid'] = 0;
if (!isset($_SESSION['business']['business_userid'])) $_SESSION['business']['business_userid'] = $_SESSION['dims']['userid'];
if (!isset($_SESSION['business']['business_monthadd'])) $_SESSION['business']['business_monthadd'] = 0; // mois courant
if (!isset($_SESSION['business']['business_weekadd'])) $_SESSION['business']['business_weekadd'] = 0; // semaine courante
if (!isset($_SESSION['business']['business_dayadd'])) $_SESSION['business']['business_dayadd'] = 0; // jour courant
if(!isset($_SESSION['business']['business_viewmode']))$_SESSION['business']['business_viewmode']='month';//mode d'affichage
if ($_SESSION['business']['business_dayadd'] != 0) {
	$_SESSION['business']['business_weekadd'] += ($_SESSION['business']['business_dayadd'] - $_SESSION['business']['business_dayadd'] % 7) / 7;
	$_SESSION['business']['business_dayadd'] = $_SESSION['business']['business_dayadd'] % 7;
}

$currentworkspacesearch=$_SESSION['dims']['planning']['currentworkspacesearch'];
$currentprojectsearch=$_SESSION['dims']['planning']['currentprojectsearch'];
$currenttypeactionsearch=$_SESSION['dims']['planning']['currenttypeactionsearch'];

if (!isset($_SESSION['business']['business_minimonthadd'])) $_SESSION['business']['business_minimonthadd']=0;
if ($minimonthadd!="") $_SESSION['business']['business_minimonthadd'] = $minimonthadd;

if (isset($effacer_action)) {
	$action = new action();
	$action->open($effacer_action);
	$action->delete();
}

$tabfilter=array();
$res=$db->query("select * from dims_mod_business_enum where type='typeaction'");
if ($db->numrows($res)>0) {
	while ($f=$db->fetchrow($res)) {
		$tabfilter[$f['id']]=$f['libelle'];
	}
}

// get color for type actions
$coloractions=getColorTypeActions();

/* choix de l'intervalle de dates pour la requ�te
 *
 * d�pend du mode d'affichage (jour/semaine/mois/ann�e)
 *
 */
$today = mktime(0,0,0,date('n'),date('j'),date('Y'));

switch($_SESSION['business']['business_viewmode']) {
	default:
	case 'week':
		$wscreen= dims_load_securvalue("wscreen",dims_const::_DIMS_NUM_INPUT,true,false,false);
		$wscreen=floor($wscreen);
		if ($wscreen==0) $wscreen=120;
		define('_PLANNING_DAY_WIDTH',	$wscreen);	// largeur en pix d'une journ�e (mode semaine)
		// calcul du premier lundi qu correspond � la date
		if ($_SESSION['business']['business_dayadd']>0) {

		}
		$datedeb_timestp = mktime(0,0,0,date('n')+$_SESSION['business']['business_monthadd'],date('j')-date('N')+($_SESSION['business']['business_weekadd']*7)+1,date('Y'));
		$datefin_timestp = mktime(0,0,0,date('n')+$_SESSION['business']['business_monthadd'],date('j')-date('N')+($_SESSION['business']['business_weekadd']*7)+7,date('Y'));

		$datedeb = date('Y-m-d',$datedeb_timestp);
		$datefin = date('Y-m-d',$datefin_timestp);

		$jmax = 7;

		$prev = "&weekadd=".($_SESSION['business']['business_weekadd']-1);
		$next = "&weekadd=".($_SESSION['business']['business_weekadd']+1);
	break;

	case 'day':
		define('_PLANNING_DAY_WIDTH',	650);	// largeur en pix d'une journ�e (mode semaine)

		$datefin_timestp = $datedeb_timestp = mktime(0,0,0,date('n')+$_SESSION['business']['business_monthadd'],date('j')+($_SESSION['business']['business_weekadd']*7)+$_SESSION['business']['business_dayadd'],date('Y'));

		$datedeb = date('Y-m-d',$datedeb_timestp);
		$datefin = date('Y-m-d',$datefin_timestp);

		$jmax = 1;

		$prev = "&dayadd=".($_SESSION['business']['business_dayadd']-1);
		$next = "&dayadd=".($_SESSION['business']['business_dayadd']+1);
	break;

	case 'month':
		$wscreen= dims_load_securvalue("wscreen",dims_const::_DIMS_NUM_INPUT,true,false,false);
		$wscreen=floor($wscreen);
		if ($wscreen==0) $wscreen=120;
		define('_PLANNING_DAY_WIDTH',	$wscreen);	// largeur en pix d'une journ�e (mode semaine)

		$datedeb_timestp = mktime(0,0,0,date('n')+$_SESSION['business']['business_monthadd'],1,date('Y'));
		$datefin_timestp = mktime(0,0,0,date('n')+$_SESSION['business']['business_monthadd']+1,0,date('Y'));

		$datedeb = date('Y-m-d',$datedeb_timestp);
		$datefin = date('Y-m-d',$datefin_timestp);

		$jmax = date('t',$datefin_timestp);

		$prev = "&monthadd=".($_SESSION['business']['business_monthadd']-1);
		$next = "&monthadd=".($_SESSION['business']['business_monthadd']+1);
	break;
}

// D�termination du filtre utilisateur
$_SESSION['business']['users']=array();

// test si userid appartient a la liste ou non
$res=$db->query("select distinct(u.id) from dims_user as u inner join dims_mod_business_user_planning as up on up.id_user_sel=.u.id and up.id_user=:userid and	up.id_user_sel =:userid", array(
	':userid' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
));
if ($db->numrows($res)==0) {
	// on insert la personne
	$res=$db->query("insert into dims_mod_business_user_planning set id_user=:userid, id_user_sel=:userid, display=1", array(
		':userid' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
	));
}

$res=$db->query("select distinct(u.id) from dims_user as u inner join dims_mod_business_user_planning as up on up.id_user_sel=.u.id and up.id_user=:userid and up.display=1", array(
	':userid' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
));

if ($db->numrows($res)>0) {
	while ($f=$db->fetchrow($res)) {
		$_SESSION['business']['users'][$f['id']]=$f['id'];
	}
}
// on fusionne avec la liste temporaire
if (isset($_SESSION['dims']['planning']['currentusertemp'])) $_SESSION['business']['users']+=$_SESSION['dims']['planning']['currentusertemp'];

$userfilterparams = array();
$user_filter = ' AND au.user_id IN ('.$db->getParamsFromArray(array_keys($_SESSION['business']['users']), 'userid', $userfilterparams).')';

if ($currenttypeactionsearch>0 &  isset($tabfilter[$currenttypeactionsearch])) {
	$user_filter .= ' AND a.typeaction like :typeaction';
	$userfilterparams[':typeaction'] = array('type' => PDO::PARAM_STR, 'value' => $tabfilter[$currenttypeactionsearch]);
}

//on regarde d'abord quel droit de partage on a
$cur_wksp = new workspace();
$cur_wksp->open($_SESSION['dims']['workspaceid']);

/* requete principale :
 *
 * recherche des actions pour la p�riode d'affichage en cours
 *
 */

$sql = "
		 SELECT		a.*,
					au.user_id AS acteur,
					au.participate,
					t.id AS tiers_id,
					t.intitule AS tiers_intitule,
					d.id AS dossier_id,
					d.objet_dossier AS dossier_intitule,
					i.lastname as nomcontact, i.firstname as prenomcontact,
					u.color,
					u.login,
					u.firstname,
					u.lastname,
					count(au2.user_id) as cpte

		FROM		dims_mod_business_action a

		INNER JOIN	dims_mod_business_action_detail ad on a.id = ad.action_id
		AND			a.datejour BETWEEN :datestart AND :dateend
		AND			a.id_module = :idmodule
		INNER JOIN	dims_mod_business_action_utilisateur au on a.id = au.action_id
		$user_filter
		INNER JOIN	dims_user u on a.id_user = u.id

		LEFT JOIN	dims_mod_business_tiers t ON ad.tiers_id = t.id
		LEFT JOIN	dims_mod_business_dossier d ON ad.dossier_id = d.id
		LEFT JOIN	dims_mod_business_contact i ON ad.contact_id = i.id
		LEFT JOIN	dims_mod_business_action_utilisateur as au2 on a.id = au2.action_id
		WHERE		a.type = :typerdv
		AND			a.id_workspace = :idworkspace
		AND			a.is_model != 1
		GROUP BY	a.id
		ORDER BY	a.heuredeb, a.heurefin
		";

$params = (!empty($userfilterparams)) ? $userfilterparams : array();
$params[':datestart'] = array('type' => PDO::PARAM_STR, 'value' => $datedeb);
$params[':dateend'] = array('type' => PDO::PARAM_STR, 'value' => $datefin);
$params[':idmodule'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['moduleid']);
$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']);
$params[':typerdv'] = array('type' => PDO::PARAM_INT, 'value' => dims_const::_PLANNING_ACTION_RDV);

$planning = array();
$actionmulti = array();
$result = $db->query($sql, $params);
while ($fields = $db->fetchrow($result)) {
	// calcul du num�ro de journ�e par rapport au d�but de l'intervalle de recherche
	// si datejour = datedeb alors jour = 0, etc.
	$jour = business_datediff($datedeb,$fields['datejour']);
	if (!isset($planning[$jour+1])) $planning[$jour+1] = array();

	$fields['intersec'] = array(); // nb intersections
	$fields['intersec'][] = sizeof($planning[$jour+1]);

	// recherche d'intersections entre rdv (pour calculer le nb de colonnes � afficher sur une journ�e)
	if (!empty($planning[$jour+1])) {
		foreach($planning[$jour+1] as $a => $action) {
			$a_hd = $action['heuredeb'];
			$a_hf = $action['heurefin'];

			$f_hd = $fields['heuredeb'];
			$f_hf = $fields['heurefin'];

			// m�me action
			if ($action['id'] == $fields['id'])  {
				$fields['liee'][] = $a;
				$planning[$jour+1][$a]['liee'][] = sizeof($planning[$jour+1]);
			}

			if (($f_hd >= $a_hd && $f_hd <= $a_hf) || ($f_hf >= $a_hd && $f_hf <= $a_hf) || ($a_hd >= $f_hd && $a_hd <= $f_hf) || ($a_hf >= $f_hd && $a_hf <= $f_hf)) {
				$fields['intersec'][] = $a;
				$planning[$jour+1][$a]['intersec'][] = sizeof($planning[$jour+1]);
			}
		}
	}

	//test si couleur existe
	if ($fields['typeaction']=='' || !isset($coloractions[$fields['typeaction']])) $fields['coloraction']="#dcdcdc";
	else {
		$fields['coloraction']=$coloractions[$fields['typeaction']];
	}

	//calcul de l'icon
	$usericon=DIMS_APP_PATH."data/users/icon_".str_replace("#","",$fields['color']).".png";
	$icon="";
	if (!file_exists($usericon) || $fields['color']=="" || $fields['color']!=strtoupper($fields['color'])) {
		// on g�n�re
		$user = new user();
		$user->open($fields['id']);
		if ($user->fields['color']=="") {
			$user->fields['color']="#EFEFEF";
		}
		elseif ($fields['color']!=strtoupper($fields['color'])) {
			$user->fields['color']=strtoupper($fields['color']);
		}
		$fields['color']=$user->fields['color'];
		// generation du logo
		$user->createPicto();
		// save color
		$user->save();
	}
	$fields['icon']="<img src=\"./data/users/icon_".str_replace("#","",$fields['color']).".png\" alt=\"\" border=\"0\">";

	if ($fields['cpte']>1) array_push($actionmulti,$fields['id']);
	// affectation des actions par jour de semaine (1=lundi, etc...)
	$planning[$jour+1][] = $fields;
}

/* requete secondaire :
 *
 * recherche des actions (Type Evenement) pour la p�riode d'affichage en cours
 *
 */

$sql = "
		 SELECT		a.*,
					u.id AS acteur,
					au.participate,
					t.id AS tiers_id,
					t.intitule AS tiers_intitule,
					d.id AS dossier_id,
					d.objet_dossier AS dossier_intitule,
					i.lastname as nomcontact, i.firstname as prenomcontact,
					u.color,
					u.login,
					u.firstname,
					u.lastname

		FROM		dims_mod_business_action a

		LEFT JOIN	dims_mod_business_action_detail ad on a.id = ad.action_id
		AND			a.datejour BETWEEN :datestart AND :dateend
		AND			a.id_module = :idmodule

		LEFT JOIN	dims_mod_business_action_utilisateur au
		ON			a.id = au.action_id

		LEFT JOIN	dims_user u
		ON			a.id_user = u.id

		LEFT JOIN	dims_mod_business_tiers t
		ON			ad.tiers_id = t.id

		LEFT JOIN	dims_mod_business_dossier d
		ON			ad.dossier_id = d.id

		LEFT JOIN	dims_mod_business_contact i
		ON			ad.contact_id = i.id

		WHERE		a.id_workspace = :idworkspace
		AND			(
						a.type = :typeevent
			OR			a.type = :typemeet
			OR			(
							a.type = :typetask
						AND a.id_workspace = :idworkspace
						)
					)
		AND			a.is_model != 1

		GROUP BY	a.id
		ORDER BY	a.heuredeb, a.heurefin
		";
		$params = array();
		$params[':datestart'] = array('type' => PDO::PARAM_STR, 'value' => $datedeb);
		$params[':dateend'] = array('type' => PDO::PARAM_STR, 'value' => $datefin);
		$params[':idmodule'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['moduleid']);
		$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']);
		$params[':typeevent'] = array('type' => PDO::PARAM_INT, 'value' => dims_const::_PLANNING_ACTION_EVT);
		$params[':typemeet'] = array('type' => PDO::PARAM_INT, 'value' => dims_const::_PLANNING_ACTION_RCT);
		$params[':typetask'] = array('type' => PDO::PARAM_INT, 'value' => dims_const::_PLANNING_ACTION_TSK);

//Limitation de la vue pour les actions de type dims_const::_PLANNING_ACTION_TSK (Projet)
//on recherche tous les users rattaches aux actions
$sqlp = "SELECT			pu.id_ref as id_user,
						a.id as id_action,
						p.id_create,
						p.id_resp,
						p.id_resp2,
						p.id_resp3

		FROM			dims_project p

		INNER JOIN		dims_task t
		ON						t.id_project = p.id

		INNER JOIN		dims_mod_business_action a
		ON						a.id_task = t.id
		AND						a.datejour BETWEEN :datestart AND :dateend
		AND				a.id_workspace = :idworkspace

		LEFT JOIN		dims_project_user pu
		ON						p.id = pu.id_project";

//echo $sqlp;
$resp = $db->query($sqlp, array(
	':datestart' => array('type' => PDO::PARAM_STR, 'value' => $datedeb),
	':dateend' => array('type' => PDO::PARAM_STR, 'value' => $datefin),
	':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
));
$tab_viewers = array();
//on construit le tableau de resultats
while($tab_r = $db->fetchrow($resp)) {
		if(!isset($tab_viewers[$tab_r['id_action']])) $tab_viewers[$tab_r['id_action']] = array();
		if($tab_r['id_user'] != '') $tab_viewers[$tab_r['id_action']][$tab_r['id_user']] = $tab_r['id_user'];
		if($tab_r['id_create'] != '') $tab_viewers[$tab_r['id_action']][$tab_r['id_create']] = $tab_r['id_create'];
		if($tab_r['id_resp'] != '') $tab_viewers[$tab_r['id_action']][$tab_r['id_resp']] = $tab_r['id_resp'];
		if($tab_r['id_resp2'] != '') $tab_viewers[$tab_r['id_action']][$tab_r['id_resp2']] = $tab_r['id_resp2'];
		if($tab_r['id_resp3'] != '') $tab_viewers[$tab_r['id_action']][$tab_r['id_resp3']] = $tab_r['id_resp3'];
}

$result = $db->query($sql, $params);
while ($fields = $db->fetchrow($result)) {

	if($fields['type'] == dims_const::_PLANNING_ACTION_TSK) {
		//on verifie si le user courant est li� � l'action courante (si non, on change d'it�ration (continue))
		if(!in_array($_SESSION['dims']['userid'], $tab_viewers[$fields['id']])) continue;
	}
	// calcul du num�ro de journ�e par rapport au d�but de l'intervalle de recherche
	// si datejour = datedeb alors jour = 0, etc.
	$jour = business_datediff($datedeb,$fields['datejour']);
	if (!isset($planning[$jour+1])) $planning[$jour+1] = array();

	$fields['intersec'] = array(); // nb intersections
	$fields['intersec'][] = sizeof($planning[$jour+1]);

	// recherche d'intersections entre rdv (pour calculer le nb de colonnes � afficher sur une journ�e)
	if (!empty($planning[$jour+1])) {
		foreach($planning[$jour+1] as $a => $action) {
			$a_hd = $action['heuredeb'];
			$a_hf = $action['heurefin'];

			$f_hd = $fields['heuredeb'];
			$f_hf = $fields['heurefin'];

			// m�me action
			if ($action['id'] == $fields['id'])  {
				$fields['liee'][] = $a;
				$planning[$jour+1][$a]['liee'][] = sizeof($planning[$jour+1]);
			}

			if (($f_hd >= $a_hd && $f_hd <= $a_hf) || ($f_hf >= $a_hd && $f_hf <= $a_hf) || ($a_hd >= $f_hd && $a_hd <= $f_hf) || ($a_hf >= $f_hd && $a_hf <= $f_hf)) {
				$fields['intersec'][] = $a;
				$planning[$jour+1][$a]['intersec'][] = sizeof($planning[$jour+1]);
			}
		}
	}

	//test si couleur existe
	if ($fields['typeaction']=='' || !isset($coloractions[$fields['typeaction']])) $fields['coloraction']="#dcdcdc";
	else {
		$fields['coloraction']=$coloractions[$fields['typeaction']];
	}

	//calcul de l'icon
	$usericon=DIMS_APP_PATH."data/users/icon_".str_replace("#","",$fields['color']).".png";
	$icon="";
	if (!file_exists($usericon) || $fields['color']=="" || $fields['color']!=strtoupper($fields['color'])) {
		// on g�n�re
		$user = new user();
		$user->open($fields['id']);
		if ($user->fields['color']=="") {
			$user->fields['color']="#EFEFEF";
		}
		elseif ($fields['color']!=strtoupper($fields['color'])) {
			$user->fields['color']=strtoupper($fields['color']);
		}
		$fields['color']=$user->fields['color'];
		// generation du logo
		$user->createPicto();
		// save color
		$user->save();
	}
	$fields['icon']="<img src=\"./data/users/icon_".str_replace("#","",$fields['color']).".png\" alt=\"\" border=\"0\">";
	$fields['cpte']=1;
	//if ($fields['cpte']>1) array_push($actionmulti,$fields['id']);
	// affectation des actions par jour de semaine (1=lundi, etc...)
	$planning[$jour+1][] = $fields;
}

$tabcorrespmulti=array();
$tabparticipate=array();
// requete pour r�cup�rer la liste des personnes rattachees au rdv pris � plusieurs
if (!empty($actionmulti)) {
	$params = array();
	$sql = "
		SELECT		action_id,user_id,u.firstname,u.lastname,participate
		FROM		dims_user as u
		INNER JOIN	dims_mod_business_action_utilisateur as au
		on			au.user_id = u.id
		AND			au.action_id in (".$db->getParamsFromArray($actionmulti, 'idaction', $params).")";

	$res=$db->query($sql, $params);

	while ($f=$db->fetchrow($res)) {
		$tabcorrespmulti[$f['action_id']][$f['user_id']]=strtoupper(substr($f['firstname'],0,1)).". ".$f['lastname'];
		$tabparticipate[$f['action_id']][$f['user_id']]=$f['participate'];
	}
}

foreach($planning as $jour => $planning_jr) {
	foreach($planning_jr as $a => $fields) {
		// test si on a le droit de voir le d�tail du rdv ou non
		if ($fields['cpte']>1) $planning[$jour][$a]['icon'] = "<img src=\"./common/img/icon_group.gif\" />";
		else {
			$planning[$jour][$a]['icon'] = $fields['icon'];
		}

		if (($fields['participate']==0 && !isset($tabparticipate[$fields['id']])) || isset($tabparticipate[$fields['id']][$fields['id_user']]) && $tabparticipate[$fields['id']][$fields['id_user']]==0) {
			$planning[$jour][$a]['icon'] = "<img src=\"./common/img/properties.png\" />";
		}

		$planning[$jour][$a]['detail_datejour'] = $datedeb_timestp + 86400 * ($jour-1);
		/*
		$detail = "<div style=\"float:left;margin:0 6px 0 0;\">{$planning[$jour][$a]['icon']}</div><div>";

		$detail .= sprintf("<div><b>%s</b></div><div><b>de %s � %s</b></div>",business_datefr_planning($planning[$jour][$a]['detail_datejour']),substr($fields['heuredeb'],0,5),substr($fields['heurefin'],0,5));
		if ($fields['libelle'] != '') {
			$detail .= '<div><b>Libell&eacute;</b> : '.addslashes($fields['libelle']).'</div>';
		}

		if ($fields['typeaction'] != '') {
			$detail .= '<div><b>Type</b> : '.addslashes($fields['typeaction']).'</div>';
		}
		if ($fields['description'] != '') {
			$detail .= '<div><b>Description</b> : '.addslashes($fields['description']).'</div>';
		}

		if (!is_null($fields['tiers_intitule']) && !is_null($fields['dossier_intitule']))
		{
			$detail .= "<div><b>"._planning_LABEL_TIERS."</b> : {$fields['tiers_intitule']}</div><div><b>Dossier</b> : {$fields['dossier_intitule']}</div>";
		}

		if ($fields['personnel']) {
			$detail .= "<div><b>Personnel</b></div>";
		}

		if ($fields['conges']) {
			$detail .= "<div><b>Cong&eacute;s</b></div>";
		}
		$detailinfo="";

		if ($fields['cpte']>1) {
			$pers = array();
			$persinfo = array();
			foreach($tabcorrespmulti[$fields['id']] as $iduser=>$nom) {
				// test si participe ou pour info
				if (isset($tabparticipate[$fields['id']][$iduser]) && $tabparticipate[$fields['id']][$iduser]==0) $persinfo[]=$nom;
				else $pers[]=$nom;
			}

			if (sizeof($pers)>0) $detail .= '<div><b>'.$_DIMS['cste']['_DIMS_PARTICIP'].'</b> : '.implode(', ',$pers).'</div>';
			// calcul du pour info
			if (sizeof($pers)>0) $detail .= '<div><b>'.$_DIMS['cste']['_DIMS_TOINFO'].'</b> : '.implode(', ',$persinfo).'</div>';
		}
		$detail = '<div class="planning_planning_popup" style="background-color:'.$fields['coloraction'].';">'.$detail.'</div></div>';

		$planning[$jour][$a]['detail'] = $detail;
		*/
	}
}

		$ajax = dims_load_securvalue("ajax", dims_const::_DIMS_CHAR_INPUT, true, false, false);
		if($ajax != "1"){
			?><div id="block_ajax_calendar" style="display:none;"></div>
			<div style="overflow:hidden;width:100%" id="block_calendar">
		<?php
		}
			$widthcur=0;
			if ($_SESSION['business']['business_viewmode']=='month') {
				$widthcur=_PLANNING_LEFTCOL_WIDTH+((_PLANNING_DAY_WIDTH+4)*7)+15;
				echo "<div style=\"float:left;width:".$widthcur."px;\">";
			}
			else {
				$widthcur=_PLANNING_LEFTCOL_WIDTH+((_PLANNING_DAY_WIDTH+4)*$jmax)+15;
				echo "<div style=\"float:left;width:".$widthcur."px;\">";
			}

		?>
		<?php
								//echo $skin->open_simplebloc('', '');
								?>
			<div class="" style="width:<? echo ($widthcur-200)?>px;float:left;">
				<div style="width:370px;margin: 0 auto;display:block;">
					<table style="width:100%"><tr><td width="50%"><table  style="width:100%"><tr>
					<?php
					echo "<td width=\"20%\">".dims_create_button("<<","","javascript:affiche_planning('".$prev."')","","","")."</td>";
					echo "<td width=\"30%\">".dims_create_button($_DIMS['cste']['_DIMS_LABEL_DAY'],"","javascript:affiche_planning('&monthadd=0&weekadd=0&dayadd=0')","","")."</td>";
					echo "<td width=\"20%\">".dims_create_button(">>","","javascript:affiche_planning('".$next."')","","")."</td>";
					?></tr></table>
					</td>
					<td align="center">
					<?php
					echo $_DIMS['cste']['_DIMS_FILTER']."&nbsp;";
					if (!empty($tabfilter)) {
						echo "<select style=\"width:100px\" id=\"currenttypeactionsearch\" name=\"currenttypeactionsearch\" onchange=\"javascript:document.location.href='admin.php?currenttypeactionsearch='+document.getElementById('currenttypeactionsearch').options[document.getElementById('currenttypeactionsearch').selectedIndex].value;\"></span>";
						if ($currenttypeactionsearch==0) $chselected="selected=\"selected\"";
						else $chselected="";
						echo "<option value=\"0\" $chselected>".$_DIMS['cste']['_DIMS_ALL']."</option>";
						foreach ($tabfilter as $idf=>$f) {
							if ($currenttypeactionsearch==$idf) $chselected="selected=\"selected\"";
							else $chselected="";
							echo "<option value=\"".$idf."\" $chselected>".$_DIMS['cste'][$f]."</option>";
						}
						echo "</select>";
					}
					?>
					</td>
					</tr></table>
				</div>
			</div>
			<div class="" style="width:155px;float:right;">
				<span style="width:155px;margin: 0 auto;display:block;">
						<table><tr><td><?echo $_DIMS['cste']['_DIMS_MODE']; ?></td><td>
						<?php
						$business_planning_viewmode['week']		= $_DIMS['cste']['_DIMS_WEEK'];
						$business_planning_viewmode['month']	= $_DIMS['cste']['_DIMS_MONTH'];
						foreach($business_planning_viewmode as $value => $text) {
							?>
							<a title="<?php echo $text; ?>" href="#" onclick="javascript:affiche_planning('&viewmode=<? echo $value; ?>');"><img style="border:0;" alt="<? echo $text; ?>" src="./common/modules/system/img/planning_<? echo $value; ?><? echo ($_SESSION['business']['business_viewmode'] != $value) ? '_notsel' : ''; ?>.png"></a>
							<?php
						}

						if (dims_isadmin()) {
							echo "<span style=\"display:inline;width:20px;\">&nbsp;<a href=\"".dims_urlencode("admin.php?op=admin_actions")."\"><img src=\"./common/img/configure.png\" border=\"0\" alt=\"\"></a>";
						}
						?>
						</td></tr></table>
				</span>
			</div>
		<?php
			if(isset($_SESSION['dims']['alert_user']) && $_SESSION['dims']['alert_user'] != '') {

				echo '<div id="view_message" style="width:100%;overflow:hidden;float:left;color:#FF0000;font-size:14px;font-weight:bold;">';
				switch($_SESSION['dims']['alert_user']) {
					case 1:
						echo $_DIMS['cste']['_DIMS_LABEL_DMD_WAITING'];
						break;
					case 2:
						echo $_DIMS['cste']['_DIMS_LABEL_DMD_REGISTERED'];
						break;
				}
				echo '</div>';
				$_SESSION['dims']['alert_user'] = "";
			}
		?>
		<div style="width:100%;float:left;display:block;">
		<?php
		switch($_SESSION['business']['business_viewmode']) {
			case 'day':
			case 'week':
				?>
				<div style="float:left;width:<?php echo _PLANNING_LEFTCOL_WIDTH; ?>px;height:450px;position:relative;">
				<?php
				// affichage des heures sur le c�t� du planning
				for ($i=dims_const::_PLANNING_H_START;$i<=dims_const::_PLANNING_H_END;$i++) {
					?>
					<div style="position:absolute;top:<?php echo 37+($i-dims_const::_PLANNING_H_START)*_PLANNING_H_HEIGHT; ?>px;width:100%;text-align:right;color:#000000"><? echo $i; ?>h&nbsp;</div>
					<div style="position:absolute;top:<?php echo 49+($i-dims_const::_PLANNING_H_START)*_PLANNING_H_HEIGHT; ?>px;width:100%;text-align:right;font-size:0.8em;color:#5E5E5E;;">30&nbsp;</div>
					<?php
				}
				?>
				</div>
				<?php
				$colord="";
				for ($j=1; $j<=$jmax; $j++) {
					$cols = 1;

					if (isset($planning[$j])) {
						// traitement de l'affichage
						foreach($planning[$j] as $a => $action) {
							$column_used = array();
							foreach($action['intersec'] as $b) {
								if ($b<$a) $column_used[$planning[$j][$b]['column']] = 1;
							}

							for ($column=1;$column<=$cols;$column++) {
								if (!isset($column_used[$column])) break;
							}

							$planning[$j][$a]['column'] = $column;

							if ($column>$cols) $cols = $column;
						}
					}

					$colwidth = 100/$cols;

					// d�termination de la date du jour en fonction du d�but de l'intervalle de recherche
					$datejour = $datedeb_timestp + 86400 * ($j-1);
					$jour = date('j',$datejour);
					$mois = date('n',$datejour);
					$annee = date('y',$datejour);
					$joursem = date('w',$datejour);
					$datejour_fr = date('d/m/Y',$datejour); // pour le bouton "ajouter"

					$gras = ($today == $datejour);

					$colord = (!isset($colord) || $colord == $skin->values['bgline2']) ? $skin->values['bgline1'] : $skin->values['bgline2'];

					?>
					<div style="border: 1px solid rgb(192, 192, 192);margin-left:0px;background-color:<? echo $colord; ?>;float:left;width:<? echo ($_SESSION['business']['business_viewmode'] == 'day') ?_PLANNING_DAY_WIDTH-15 : _PLANNING_DAY_WIDTH; ?>px;height:450px;position:relative;">
						<?php
						// affichage des s�parateurs d'heures (traits horizontaux)
						for ($i=dims_const::_PLANNING_H_START;$i<=dims_const::_PLANNING_H_END;$i++) {
							?>
							<div style="position:absolute;top:<?php echo 45+($i-dims_const::_PLANNING_H_START)*_PLANNING_H_HEIGHT; ?>px;border-top:1px solid #c0c0c0;width:100%;"></div>
							<div style="position:absolute;top:<?php echo 57+($i-dims_const::_PLANNING_H_START)*_PLANNING_H_HEIGHT; ?>px;border-top:1px solid #d0d0d0;width:100%;"></div>
							<?php
						}
						?>
						<div class="midb16" style="<? if ($gras) echo 'font-weight:bold;' ?>;">
						<?php printf("%s %02d/%02d/%02d",$business_jour[$joursem],$jour,$mois,$annee); ?>
						</div>
						<div style="text-align:center;margin-top:2px;">
						<?php

							$url = null;

							//Creation des option d'ajout au planning
							dims_createOptions($_SESSION['dims']['workspaceid'],$_SESSION['dims']['moduleid'],_PLANNING_OBJECT_ACTION,$j,$business_jour[$joursem]);

							//Option : Rendez-vous
							$url = dims_urlencode("admin.php?op=xml_planning_modifier_action&datejour=".$datejour_fr."&type=".dims_const::_PLANNING_ACTION_RDV."&id=0");
							dims_addOptions($_SESSION['dims']['workspaceid'],$_SESSION['dims']['moduleid'],_PLANNING_OBJECT_ACTION,$j,$url,"",'Ajouter un &eacute;v&egrave;nement priv&eacute;',"","./common/img/contact.png");
							//Option : Evenement
							$url = dims_urlencode("admin.php?op=xml_planning_modifier_action&datejour=".$datejour_fr."&type=".dims_const::_PLANNING_ACTION_EVT."&id=0");
							dims_addOptions($_SESSION['dims']['workspaceid'],$_SESSION['dims']['moduleid'],_PLANNING_OBJECT_ACTION,$j,$url,"",'Ajouter un &eacute;v&egrave;nement public',"","./common/modules/system/img/tasks.png");
							//Option : Rencontre
							//$url = dims_urlencode("admin.php?op=xml_planning_modifier_action&datejour=".$datejour_fr."&type=".dims_const::_PLANNING_ACTION_RCT."&id=0");
							//dims_addOptions($_SESSION['dims']['workspaceid'],$_SESSION['dims']['moduleid'],_PLANNING_OBJECT_ACTION,$j,$url,"",$_DIMS['cste']['_DIMS_PLANNING_ADD_RCT'],"","./common/img/icon_group.gif");

							//Affichage des options
							echo dims_create_button($_DIMS['cste']['_DIMS_ADD'],"./common/img/add.gif","javascript:displayOptions(event,'".$_SESSION['dims']['workspaceid']."','".$_SESSION['dims']['moduleid']."','"._PLANNING_OBJECT_ACTION."','".$j."','0');","","position:absolute;z-index:5;top:22px;left:2px;right:2px;width:90px");

							//Old version
							//echo dims_create_button($_DIMS['cste']['_DIMS_ADD'],"./common/img/add.gif","dims_openwin('index-light.php?op=xml_planning_modifier_action&datejour=".$datejour_fr."',450,620)","","position:absolute;z-index:5;top:22px;left:2px;right:2px;width:90px");

						?>
						</div>
						<?php
						// affichage du d�tail de la journ�e
						if (isset($planning[$j])) {
							foreach($planning[$j] as $a => $action) {
								if($action['id_task'] == 0 || $action['id_workspace'] == $_SESSION['dims']['workspaceid']) {
									$column = $planning[$j][$a]['column'];

									$left = intval( 1+(($column-1)*$colwidth*_PLANNING_DAY_WIDTH)/100 );
									$width = intval( ($colwidth*_PLANNING_DAY_WIDTH)/100 ) - 3;

									$h_height = _PLANNING_H_HEIGHT;

									$hd = ($action['heuredeb'] < dims_const::_PLANNING_H_START) ? dims_const::_PLANNING_H_START : $action['heuredeb'];
									$hf = ($action['heurefin'] > dims_const::_PLANNING_H_END) ? dims_const::_PLANNING_H_END : $action['heurefin'];

									$top = intval(45 + ( substr($hd,0,2) + substr($hd,3,2)/60 - dims_const::_PLANNING_H_START)*$h_height);
									$height = (substr($hf,0,2) - substr($hd,0,2) + (substr($hf,3,2)-substr($hd,3,2))/60) * $h_height - 2;
									$border="";
									$cursor="";
									if (isset($_SESSION['business']['business_actionid']) && $_SESSION['business']['business_actionid']>0 && $_SESSION['business']['business_actionid']==$action['id']) {
										$color="#f0fbcc";
										$border="border-style:dotted;border-color:#9A9A9A;border-width:2px;";
									}
									else $color=$action['coloraction'];
									// modification pour administrateur ayant des droits de voir

									if ($action['typeaction ']!='' && $action['personnel']==0 && (isset($tabcorrespmulti[$action['id']][$_SESSION['dims']['userid']]) || $_SESSION['dims']['adminlevel']>=dims_const::_DIMS_ID_LEVEL_SYSTEMADMIN) || $action['acteur']==$_SESSION['dims']['userid'] || $action['type'] == dims_const::_PLANNING_ACTION_TSK) {
										if($action['id_task'] != 0 && $action['id_workspace'] == $_SESSION['dims']['workspaceid']) {
											$url_to = dims_urlencode($dims->getScriptEnv().'/admin.php?dims_mainmenu='.dims_const::_DIMS_SUBMENU_EVENT.'&cat='._BUSINESS_CAT_ACCUEIL.'&dims_desktop=block&dims_action=public&op=add_dmd_insc&id_user='.$_SESSION['dims']['userid'].'&id_action='.$action['id'].'');
											$onclick = "onclick=\"javascript:dims_confirmlink('".$url_to."', '".addslashes($_DIMS['cste']['_DIMS_LABEL_PROJ_DMD_INSC'])."');\"";
										}
										//elseif($action['id_task'] == 0 && isset($action['detail']) && !empty($action['detail'])) {
										elseif($action['id_task'] == 0) {
											$detailpopup=dims_strcut($action['detail'],120);
											$onclick="onclick=\"javascript:location.href='admin.php?op=xml_planning_modifier_action&id=".$action['id']."'\"";
											$cursor="pointer";
										}
										else {
											$onclick = "";
										}


										// cas admin
										if (!isset($tabcorrespmulti[$action['id']][$_SESSION['dims']['userid']]) && $action['acteur']!=$_SESSION['dims']['userid']) {
											$action['icon']="<img src=\"./common/img/view.png\" alt=\"\">";
										}
									}
									else {
										$detailpopup="<br>".$_DIMS['cste']['_DIMS_LABEL_NOT_AVAILABLE'];
										$onclick="";
										$cursor="";
									}

																		?>
									<a class="planning_planning_action" <?php echo $onclick; ?> style="position:absolute;width:<? echo $width; ?>px;left:<? echo $left; ?>px;top:<? echo $top ?>px;height:<? echo $height; ?>px;background-color:<? echo $color; ?>;<? echo $border; ?>"onmouseover="javascript:displayActionDetail(event,<?php echo $action['id']; ?>,0)">
										<div style="width:100%" class="planning_planning_heure">
											<p class="planning_planning_heure"><span style="float:left;margin-right:2px;"><? echo $action['icon']; ?></span>
												<span class="planning_planning_heure"><? echo substr($action['heuredeb'],0,2); ?>:<? echo substr($action['heuredeb'],3,2); ?></span> <span class="planning_planning_heure"><? echo substr($action['heurefin'],0,2); ?>:<? echo substr($action['heurefin'],3,2); ?><br>
												<?php
												$pers=array();
												if (isset($tabcorrespmulti[$action['id']])) {
													$sizetab=sizeof($tabcorrespmulti[$action['id']]);

													foreach($tabcorrespmulti[$action['id']] as $iduser=>$nom) {
														if (!isset($tabparticipate[$action['id']][$iduser]) || $tabparticipate[$action['id']][$iduser]==1) {
															if ($sizetab>2) {
																// on filtre pour ne pas nous mettre en affichage
																if ($iduser!=$_SESSION['dims']['userid']) $pers[]=$nom;
															}
															else $pers[]=$nom;
														}
													}
													if (sizeof($pers)>0) {
														echo implode(', ',$pers);
														if (sizeof($pers)>2) echo ", ...";
													}
												}
												else echo strtoupper(substr($action['firstname'],0,1)).". ".$action['lastname'];

												?></span>
											</p>
										</div>
										<div class="planning_planning_detail">
										<?php
										$detail = array();
										if ($action['type'] == dims_const::_PLANNING_ACTION_TSK || $action['typeaction']!='' &&  ($action['type']==2 || ($action['personnel']==0 && (isset($tabcorrespmulti[$action['id']][$_SESSION['dims']['userid']]) || $_SESSION['dims']['adminlevel']>=dims_const::_DIMS_ID_LEVEL_SYSTEMADMIN) || $action['acteur']==$_SESSION['dims']['userid']))) {
											if (!is_null($action['tiers_intitule'])) $detail[] = '<div style="padding-top:2px;"><b>'.($action['tiers_intitule']).'</b></div>';
											if (!is_null($action['dossier_intitule'])) $detail[] = '<div style="padding-top:2px;"><b>'.($action['dossier_intitule']).'</b></div>';
											if ($action['interne']) $detail[] = '<div style="padding-top:2px;font-weight:bold;">Interne</div>';
											if ($action['personnel']) $detail[] = '<div style="padding-top:2px;font-weight:bold;">Personnel</div>';
											if ($action['conges']) $detail[] = '<div style="padding-top:2px;font-weight:bold;">Cong�s</div>';
											if (!is_null($action['libelle'])) {
												if (!is_null($action['typeaction'])) $typea=$_DIMS['cste'][$action['typeaction']]." - ";
												else $typea="";
												$detail[] = '<div style="padding-top:2px;">'.$typea.($action['libelle']).'</div>';
											}
											echo implode('',$detail);
										}
										else echo $_DIMS['cste']['_DIMS_LABEL_NOT_AVAILABLE'];
										?>
										</div>
										<!--br><input type="button" class="flatbutton" value="Modif" class="planning-btn" onclick="javascript:dims_openwin('index-light.php?op=xml_planning_modifier_action&id=<? echo $action['id']; ?>',400,620)">&nbsp;<input type="button" class="flatbutton" value="Supp" class="planning-btn" onclick="javascript:if (confirm('�tes-vous certain de vouloir supprimer cette action ?')) affiche_planning('&effacer_action=<? echo $action['id']; ?>')"-->
									</a>
								<?php
								}
							}
						}
						?>
					</div>
					<?php
				}

			break;

			case 'month':

				$weekday = date('w', $datedeb_timestp);
				if ($weekday == 0) $weekday = 7;
				?>

				<div style="float:left;">
					<div class="planning_calendar_row">
					<div style="float:left;overflow:hidden;width:<?php echo _PLANNING_LEFTCOL_WIDTH; ?>px;text-align:center;padding:0px;">
						<?php echo substr($planning_mois[date('n',$datedeb_timestp)],0,3); ?>
					</div>
					<?php
					foreach($business_jour as $d) {
						?>
						<div style="float:left;overflow:hidden;">
							<div style="margin:0px;width:<?php echo _PLANNING_DAY_WIDTH; ?>px;font-size:9px;height:16px;text-align:center;">
							<?php echo $d; ?>
							</div>
						</div>
						<?php
					}
					?>
					</div>
					<?php
					if ($weekday > 1) {
						?>
						<div class="planning_calendar_row">
							<div style="float:left;overflow:hidden;width:<?php echo _PLANNING_LEFTCOL_WIDTH; ?>px;text-align:center;padding-top:2px;">
								<?php

																echo $_DIMS['cste']['_WEEK'].date('W',$datedeb_timestp); ?>
							</div>
						<?php
						for ($j = 1; $j < $weekday; $j++) {
						?>
							<div style="float:left;overflow:hidden;">
								<div class="planning_calendar_day_title">
									<div style="padding:1px 1px;">&nbsp;</div>
								</div>
								<div class="planning_calendar_day" style="width:<?php echo _PLANNING_DAY_WIDTH; ?>px;height:<? echo _PLANNING_CAL_DAY_HEIGHT; ?>px;">
								&nbsp;
								</div>
							</div>
						<?php
						}
					}

					for ($j = 1; $j <= $jmax ; $j++) {
						//$datejour = $datedeb_timestp + 86400 * ($j-1);
						$datejour = mktime(0,0,0,date('n')+$_SESSION['business']['business_monthadd'],$j,date('Y'));
						$jour = date('j',$datejour);
						$mois = date('n',$datejour);
						$annee = date('y',$datejour);
						$joursem = date('w',$datejour);
						$gras = ($today == $datejour);

						if ($weekday == 8) $weekday = 1;

						if ($weekday == 1) {
							?>
							<div class="planning_calendar_row">
								<div style="float:left;overflow:hidden;width:<?php echo _PLANNING_LEFTCOL_WIDTH; ?>px;text-align:center;padding-top:2px;">
									<?php
									echo $_DIMS['cste']['_WEEK'].date('W',$datejour);
									?>
								</div>
							<?php
							//echo $sem = date('W',$datejour);
						}

						?>
							<div style="float:left;overflow:hidden;">
								<div class="midb16">
									<div style=";width:<?php echo _PLANNING_DAY_WIDTH; ?>px;padding:1px 1px;<?php if ($gras) echo 'font-weight:bold;'; ?>">
									<?php
									printf("%02d/%02d/%02d",$jour,$mois,$annee);
									?>
									<?php
									$datejour_fr = date('d/m/Y',$datejour); // pour le bouton "ajouter"

									$url = null;

									//Creation des option d'ajout au planning
									dims_createOptions($_SESSION['dims']['workspaceid'],$_SESSION['dims']['moduleid'],_PLANNING_OBJECT_ACTION,$j,$business_jour[$joursem]);

									//Option : Rendez-vous
									$url = dims_urlencode("admin.php?op=xml_planning_modifier_action&datejour=".$datejour_fr."&type=".dims_const::_PLANNING_ACTION_RDV."&id=0");
									dims_addOptions($_SESSION['dims']['workspaceid'],$_SESSION['dims']['moduleid'],_PLANNING_OBJECT_ACTION,$j,$url,"",'Ajouter un &eacute;v&egrave;nement priv&eacute;',"","./common/img/contact.png");
									//Option : Evenement
									$url = dims_urlencode("admin.php?op=xml_planning_modifier_action&datejour=".$datejour_fr."&type=".dims_const::_PLANNING_ACTION_EVT."&id=0");
									dims_addOptions($_SESSION['dims']['workspaceid'],$_SESSION['dims']['moduleid'],_PLANNING_OBJECT_ACTION,$j,$url,"",'Ajouter un &eacute;v&egrave;nement public',"","./common/modules/system/img/tasks.png");
									//Option : Rencontre
									//$url = dims_urlencode("admin.php?op=xml_planning_modifier_action&datejour=".$datejour_fr."&type=".dims_const::_PLANNING_ACTION_RCT."&id=0");
									//dims_addOptions($_SESSION['dims']['workspaceid'],$_SESSION['dims']['moduleid'],_PLANNING_OBJECT_ACTION,$j,$url,"",$_DIMS['cste']['_DIMS_PLANNING_ADD_RCT'],"","./common/img/icon_group.gif");

									//Affichage des options
									echo "&nbsp;<a href=\"javascript:void(0);\" onclick=\"javascript:displayOptions(event,'".$_SESSION['dims']['workspaceid']."','".$_SESSION['dims']['moduleid']."','"._PLANNING_OBJECT_ACTION."','".$j."','0');\"><img src=\"./common/img/add.gif\"></a>";

									//Old version
									//echo "&nbsp;<a href=\"javascript:void(0);\" onclick=\"dims_openwin('index-light.php?op=xml_planning_modifier_action&datejour=".$datejour_fr."',900,620)\"><img src=\"./common/img/add.gif\"></a>";
									?>
									</div>
								</div>
								<div class="planning_calendar_day<? if ($gras) echo '_selected'; ?>" style="width:<? echo _PLANNING_DAY_WIDTH; ?>px;height:<? echo _PLANNING_CAL_DAY_HEIGHT; ?>px;<? if ($gras) echo 'border-top-color:#808080;'; ?>">
								<?php
								$cols = 1;

								if (isset($planning[$j])) {
									foreach($planning[$j] as $a => $action) {
										$column_used = array();
										foreach($action['intersec'] as $b) {
											if ($b<$a) $column_used[$planning[$j][$b]['column']] = 1;
										}

										for ($column=1;$column<=$cols;$column++) {
											if (!isset($column_used[$column])) break;
										}

										$planning[$j][$a]['column'] = $column;

										if ($column>$cols) $cols = $column;
									}
								}

								$colwidth = (100-1)/$cols;

								// d�termination de la date du jour en fonction du d�but de l'intervalle de recherche
								$gras = ($today == $datejour);
								$color = (!isset($color) || $color == $skin->values['bgline2']) ? $skin->values['bgline1'] : $skin->values['bgline2'];

									// affichage du d�tail de la journ�e
									if (isset($planning[$j])) {
										foreach($planning[$j] as $a => $action) {
											if($action['id_task'] == 0 || $action['id_workspace'] == $_SESSION['dims']['workspaceid']) {
												$column = $planning[$j][$a]['column'];

												$left = intval( 1+(($column-1)*$colwidth*_PLANNING_DAY_WIDTH)/100 );
												$width = intval( ($colwidth*_PLANNING_DAY_WIDTH)/100 ) ;
												$h_height = _PLANNING_CAL_DAY_HEIGHT / (dims_const::_PLANNING_H_END - dims_const::_PLANNING_H_START);

												$hd = ($action['heuredeb'] < dims_const::_PLANNING_H_START) ? dims_const::_PLANNING_H_START : $action['heuredeb'];
												$hf = ($action['heurefin'] > dims_const::_PLANNING_H_END) ? dims_const::_PLANNING_H_END : $action['heurefin'];

												$top = intval(1 + ( substr($hd,0,2) + substr($hd,3,2)/60 - dims_const::_PLANNING_H_START)*$h_height);
												$height = (substr($hf,0,2) - substr($hd,0,2) + (substr($hf,3,2)-substr($hd,3,2))/60) * $h_height - 2;
												$cursor="";
												$pointer="";
												//if (isset($tabcorrespmulti[$action['id']][$_SESSION['dims']['userid']]) || $action['acteur']==$_SESSION['dims']['userid']) {
												if (($action['personnel']==0 && (isset($tabcorrespmulti[$action['id']][$_SESSION['dims']['userid']])) || $_SESSION['dims']['adminlevel']>=dims_const::_DIMS_ID_LEVEL_SYSTEMADMIN) || $action['acteur']==$_SESSION['dims']['userid'] || $action['type'] == dims_const::_PLANNING_ACTION_TSK) {
													if(isset($action['detail']) && !empty($action['detail']))
														$detailpopup=dims_strcut ($action['detail'],120);
													if($action['id_task'] != 0 && $action['id_workspace'] == $_SESSION['dims']['workspaceid']) {
														$url_to = dims_urlencode($dims->getScriptEnv().'/admin.php?dims_mainmenu='.dims_const::_DIMS_SUBMENU_EVENT.'&cat='._BUSINESS_CAT_ACCUEIL.'&dims_desktop=block&dims_action=public&op=add_dmd_insc&id_user='.$_SESSION['dims']['userid'].'&id_action='.$action['id'].'');
														$onclick = "onclick=\"javascript:dims_confirmlink('".$url_to."', '".addslashes($_DIMS['cste']['_DIMS_LABEL_PROJ_DMD_INSC'])."');\"";
													}
													elseif($action['id_task'] == 0) {
														$onclick = "onclick=\"javascript:location.href='admin.php?op=xml_planning_modifier_action&id=".$action['id']."'\"";
													}
													else {
														$onclick = "";
													}

													$cursor="pointer";
												}
												else {
													$detailpopup=strtoupper(substr($action['firstname'],0,1)).". ".$action['lastname']."<br>".$_DIMS['cste']['_DIMS_LABEL_NOT_AVAILABLE'];
													$onclick="";
													$cursor="";
												}

												if (isset($_SESSION['business']['business_actionid']) && $_SESSION['business']['business_actionid']>0 && $_SESSION['business']['business_actionid']==$action['id']) {
													$color="#f0fbcc";
												}
												else $color=$action['coloraction'];
												?>
												<a class="planning_action" style="background-color:<?php echo $color; ?>;" <?php echo $onclick; ?> onmouseover="javascript:displayActionDetail(event,<?php echo $action['id']; ?>,0)">
														<div class="planning_planning_heure">
															<?php echo substr($action['heuredeb'],0,2).":".substr($action['heuredeb'],3,2)."&nbsp;".substr($action['heurefin'],0,2).":".substr($action['heurefin'],3,2); ?>
														</div>
													<div class="planning_info">
														<?php
														$detail = array();
														if ($action['type']==2 ||
															!is_null($action['libelle']) &&
															(isset($tabcorrespmulti[$action['id']][$_SESSION['dims']['userid']]) ||
															 $action['acteur']==$_SESSION['dims']['userid'])
															|| $action['type'] == dims_const::_PLANNING_ACTION_TSK
															) {

															if (!empty($action['typeaction'])) {
																																																													if (isset($_DIMS['cste'][$action['typeaction']]))
																																																														$typea=$_DIMS['cste'][$action['typeaction']]." - ";
																																																													else
																																																														$typea = $action['typeaction'];
																																																												}
																																																												else $typea="";

															echo " ".substr($typea.($action['libelle']), 0, 12);
														}
														else echo $_DIMS['cste']['_DIMS_LABEL_NOT_AVAILABLE'];
														?>
													</div>
												</a>
										<?php
											}
										}
									}
								?>
								</div>
							</div>
						<?php

						if ($weekday == 7) echo '</div>';
						$weekday++;
					}

					if ($weekday <= 7) {
						for ($j = $weekday; $j <= 7 ; $j++) {
						?>
							<div style="float:left;overflow:hidden;">
								<div class="planning_calendar_day_title">
									<div style="padding:1px 1px;">&nbsp;</div>
								</div>
								<div class="planning_calendar_day" style="width:<? echo _PLANNING_DAY_WIDTH; ?>px;height:<? echo _PLANNING_CAL_DAY_HEIGHT; ?>px;">
								&nbsp;
								</div>
							</div>
					<?php
						}
						echo '</div>';
					}
					?>
				</div>
				<?php

			break;
		}
		//echo $skin->close_simplebloc();

		echo "</div></div>";
		?>
			<div class="" style="width:175px;float:right;text-align:center;">
				<?php echo $skin->open_simplebloc('', 'width:100%;'); ?>
				<span id="monthview" style="width:100%;padding:0px 0px;">
				<?php
				require_once(DIMS_APP_PATH . "/modules/system/xml_planning_month.php"); ?>
				</span>
				<?
								// see with Carole for option
								?>
								<div class="midb16" style="width:100%"><? echo $_DIMS['cste']['_DIMS_FAVORITE_PLANNING']; ?></div>
				<div width="100%" style="padding:0px 0px;background:#FFFFFF;">
				<?php
				$_SESSION['business']['usersselected']=array();
				$res=$db->query("select u.*,up.display from dims_user as u inner join dims_mod_business_user_planning as up on up.id_user_sel=.u.id and up.id_user=:iduser", array(
					':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
				));

				if ($db->numrows($res)>0) {
					echo "<table width=\"100%\">";
					while ($f=$db->fetchrow($res)) {
						//calcul de l'icon
						$usericon=DIMS_APP_PATH."data/users/icon_".str_replace("#","",$f['color']).".png";
						$icon="";
						if (!file_exists($usericon) || $f['color']=="") {
							// on g�n�re
							$user = new user();
							$user->open($f['id']);
							if ($user->fields['color']=="") {
								$user->fields['color']="#EFEFEF";
								$user->save();
							}
							// generation du logo
							$user->createPicto();
						}
						$icon="<img src=\"./data/users/icon_".str_replace("#","",$f['color']).".png\" alt=\"\" border=\"0\">";

						// affichage des options
						if ($f['display']) $check="checked=\"checked\"";
						else $check="";

						echo "<tr><td width=\"10%\"><input type=\"checkbox\" $check onclick=\"updateUserFromSelectedPlanning('displayUserPlanning',".$f['id'].")\"></td>
						<td width=\"10%\">".$icon."</td>
						<td align=\"left\" width=\"70%\">".strtoupper(substr($f['firstname'],0,1)).". ".$f['lastname']."</td>";

						// test si propre profil
						if ($f['id']!=$_SESSION['dims']['userid'])
							echo "<td width=\"10%\"><a href=\"javascript:void(0);\" onclick=\"updateUserFromSelectedPlanning('deleteUserPlanning',".$f['id'].");\"><img src=\"./common/img/delete.png\" border=\"0\"></a></td></tr>";

						$_SESSION['business']['usersselected'][$f['id']]=$f['id'];
					}
					echo "</table>";
				}
				?>
				</div>
				<?
								?>
								<div class="midb16" style="width:100%"><? echo $_DIMS['cste']['_DIMS_SEARCH_PLANNING']; ?></div>
				<?php
				// filtre sur un espace de travail
				$user= new user();
				$user->open($_SESSION['dims']['userid']);
				//$lstworkspace=$user->getworkspaces();
				global $dims;
				$lstworkspace=array();
				foreach ($dims->getWorkspaces() as $key => $vwork) {
					$lstworkspace[$key]=substr(ucfirst(strtolower(trim($vwork['label']))),0,22);
				}

				asort($lstworkspace);

				echo "<span style=\"padding-top:1px;width:100%;margin:0px;background:#FFFFFF;\">";
				echo "<span style=\"float:right;\"><select style=\"width:155px\" id=\"currentworkspacesearch\" name=\"currentworkspacesearch\" onchange=\"javascript:document.location.href='admin.php?currentworkspacesearch='+document.getElementById('currentworkspacesearch').options[document.getElementById('currentworkspacesearch').selectedIndex].value;\"></span>";

				if ($currentworkspacesearch==0) $chselected="selected=\"selected\"";
				else $chselected="";

				echo "<option value=\"0\" $chselected>".$_DIMS['cste']['_DIMS_ALL_WORKSPACES']."</option>";
				foreach ($lstworkspace as $idw => $label) {
					if ($currentworkspacesearch==$idw) $chselected="selected=\"selected\"";
					else $chselected="";
					echo "<option value=\"".$idw."\" $chselected>".$label."</option>";
				}
				echo "</select></span>";

				// construction de la liste des projets en cours
				$workspace = new workspace();
				$workspace->open($_SESSION['dims']['workspaceid']);

				if ($workspace->fields['project']) {
					$lstprojects=$user->getProjects();
					echo "<span style=\"padding-top:1px;width:100%;margin:0px;background:#FFFFFF;\">";
					echo "<span style=\"float:left;width:45px\">".$_DIMS['cste']['_LABEL_PROJECTS']."</span><span style=\"float:right;\"><select style=\"width:105px\" id=\"currentprojectsearch\" name=\"currentprojectsearch\" onchange=\"javascript:document.location.href='admin.php?currentprojectsearch='+document.getElementById('currentprojectsearch').options[document.getElementById('currentprojectsearch').selectedIndex].value;\">";

					if ($currentprojectsearch==0) $chselected="selected=\"selected\"";
					else $chselected="";

					echo "<option value=\"0\" $chselected>".$_DIMS['cste']['_DIMS_PROJECTS']."</option>";
					foreach ($lstprojects as $idp => $project) {
						if ($currentprojectsearch==$project['id']) $chselected="selected=\"selected\"";
						else $chselected="";
						echo "<option value=\"".$project['id']."\" $chselected>".$project['label']."</option>";
					}
					echo "</select></span>";
				}

				// construction de la liste des workspaces possibles
				if (isset($_SESSION['dims']['planning']['currentusersearch'])) $nomsearch=$_SESSION['dims']['planning']['currentusersearch'];
				else $nomsearch="";
				?>
				<input value="<? echo $nomsearch;?>" type="text" onkeyup="javascript:searchUserPlanning();" id="nomsearchplanning" name="nomsearchplanning" size="14">
				<img style="cursor: pointer;" onclick="javascript:searchUserPlanning();" src="./common/img/search.png" border="0">
				<img style="cursor: pointer;" onclick="javascript:document.location.href='admin.php?resetsearch=1';" src="./common/img/delete.png" border="0">
				<div id="lst_planningtempuser" style="width:145px;display:block;float:left;background:#FFFFFF;">
				<?php
				// construction de la liste �ventuelle des r�sultats
				// compteur des users temp selected
				$cptetempselected=0;
				if (isset($_SESSION['dims']['planning']['currentusertemp']) && !empty($_SESSION['dims']['planning']['currentusertemp'])) {
					$params = array();
					$res=$db->query("select u.* from dims_user as u where id in (".$db->getParamsFromArray($_SESSION['dims']['planning']['currentusertemp'], 'iduser', $params).")", $params);
					if ($db->numrows($res)>0) {
						echo "<table width=\"100%\">";
						while ($f=$db->fetchrow($res)) {
							$cptetempselected++;
							//calcul de l'icon
							$usericon=DIMS_APP_PATH."data/users/icon_".str_replace("#","",$f['color']).".png";
							$icon="";
							if (!file_exists($usericon) || $f['color']=="") {
								// on g�n�re
								$user = new user();
								$user->open($f['id']);
								if ($user->fields['color']=="") {
									$user->fields['color']="#EFEFEF";
									$user->save();
								}
								// generation du logo
								$user->createPicto();
							}
							$icon="<img src=\"./data/users/icon_".str_replace("#","",$f['color']).".png\" alt=\"\" border=\"0\">";

							echo "<tr><td width=\"10%\">".$icon."</td>
								<td align=\"left\" width=\"70%\">".strtoupper(substr($f['firstname'],0,1)).". ".$f['lastname']."</td>";
							// affichage des options
							if ($f['display']) $icon="./common/img/view.png";
							else $icon="./common/img/notview.png";

							echo "<td width=\"10%\"><a href=\"javascript:void(0);\" onclick=\"updateUserFromSelectedPlanning('addUserPlanning',".$f['id'].");\"><img src=\"./common/img/add.gif\" border=\"0\"></a></td>";
							echo "<td width=\"10%\"><a href=\"javascript:void(0);\" onclick=\"updateUserFromSelectedPlanning('deleteUserTempPlanning',".$f['id'].");\"><img src=\"./common/img/delete.png\" border=\"0\"></a></td></tr>";
						}
						echo "</table>";
					}
				}
				$_SESSION['dims']['planning']['cptetempsekected']=$cptetempselected;
				?>
				</div>
				<div id="lst_planninguser" style="width:145px;display:block;float:left;">
					<?php
					$cptetemp=0;
					// construction de la liste �ventuelle des r�sultats
					if (isset($_SESSION['dims']['planning']['currentuserresp']) && !empty($_SESSION['dims']['planning']['currentuserresp'])) {
						// requete pour les noms
						$params = array();
						$res=$db->query("select id,firstname,lastname,color from dims_user where id in (".$db->getParamsFromArray($_SESSION['dims']['planning']['currentuserresp'], 'iduser', $params).")", $params);

						if ($db->numrows($res)>1) {
							// display link to active all elements
							echo "<span style=\"padding-top:2px;width:100%;margin:0px;text-align:left;\">";
							echo "<a href=\"javascript:void(0);\" onclick=\"updateUserFromSelectedPlanning('addAllUserTempPlanning',0);\">".$_DIMS['cste']['__ALLCHECK']."</a>";
							if ($_SESSION['dims']['planning']['cptetempsekected']>1) echo "&nbsp;/&nbsp;<a href=\"javascript:void(0);\" onclick=\"updateUserFromSelectedPlanning('deleteAllUserTempPlanning',0);\">".$_DIMS['cste']['_ALLUNCHECK']."</a>";
							echo "</span>";
						}
						echo "<span style=\"width:100%;\">";
						// requete pour les noms

						if ($db->numrows($res)>0) {
							echo "<table width=\"100%\" >";
							$resalreadysel="";
							while($f=$db->fetchrow($res)) {
								//calcul de l'icon
								$usericon=DIMS_APP_PATH."data/users/icon_".str_replace("#","",$f['color']).".png";
								$icon="";
								if (!file_exists($usericon) || $f['color']=="") {
									// on g�n�re
									$user = new user();
									$user->open($f['id']);
									if ($user->fields['color']=="") {
										$user->fields['color']="#EFEFEF";
										$user->save();
									}
									// generation du logo
									$user->createPicto();
								}
								$icon="<img src=\"./data/users/icon_".str_replace("#","",$f['color']).".png\" alt=\"\" border=\"0\">";
								$restemp="<tr>";
								if (!isset($_SESSION['dims']['planning']['currentusertemp'])) $_SESSION['dims']['planning']['currentusertemp']=array();
								if (!in_array($f['id'],$_SESSION['dims']['planning']['currentusertemp']) && !in_array($f['id'],$_SESSION['business']['usersselected']) && $f['id']!=$_SESSION['dims']['userid']) {
									$cptetemp++;
									$color="";

									$restemp.="<td width=\"2%\"><input style=\"margin:0px;padding:0px;\" type=\"checkbox\" onclick=\"updateUserFromSelectedPlanning('addUserTempPlanning',".$f['id'].")\"></td>
									<td width=\"8%\">".$icon."</td>";
									$restemp.="<td align=\"left\" width=\"74%\">";
									$restemp.= strtoupper(substr($f['firstname'],0,1)).". ".$f['lastname']."</td>";

									$restemp.= "
										<td width=\"8%\"><a href=\"javascript:void(0);\" onclick=\"updateUserFromSelectedPlanning('addUserPlanning',".$f['id'].");\"><img src=\"./common/img/add.gif\" border=\"0\"></a></td></tr>";
									echo $restemp;
								 }
								 else {
									$restemp.="<td>-</td>
									<td width=\"10%\">".$icon."</td>";
									$restemp.= "<td align=\"left\" width=\"74%\"><font class=\"fontgray\">".strtoupper(substr($f['firstname'],0,1)).". ".$f['lastname']."</font></td>
									<td width=\"8%\">-</td></tr>";
									$resalreadysel.=$restemp;
								 }

							}
							// display users already selected
							echo $resalreadysel;
							echo "</table>";
						}
						echo "</span>";
					}

				?>
				</div>
				<div style="clear:both;"></div>
			</div>
		</div>
		<?php echo $skin->close_simplebloc(); ?>
<?php
	if($ajax != "1"){
		?></div><?php
	}
?>
