<?php
if (isset($_GET['dayadd'])) {
	unset($_SESSION['training']['school_userid']);
	unset($_SESSION['training']['school_weekadd']);
	unset($_SESSION['training']['school_dayadd']);
	unset($_SESSION['training']['school_monthadd']);

	$_SESSION['training']['school_dayadd'] = dims_load_securvalue('dayadd', dims_const::_DIMS_NUM_INPUT, true, true, true);
	$_SESSION['training']['school_viewmode'] = 'week';
}

if (isset($_GET['monthadd'])) {
	unset($_SESSION['training']['school_userid']);
	unset($_SESSION['training']['school_weekadd']);
	unset($_SESSION['training']['school_dayadd']);
	unset($_SESSION['training']['school_monthadd']);

	$_SESSION['training']['school_monthadd'] = dims_load_securvalue('monthadd', dims_const::_DIMS_NUM_INPUT, true, true, true);
	$_SESSION['training']['school_viewmode'] = 'month';
}

if (isset($actionid) && $actionid>0) {
	$tabtemp=array();
	$findme=false;
	$_SESSION['training']['school_actionid'] = $actionid;
	// recherche si la personne y participe ou non
	$sql = "
		SELECT		user_id
		FROM		dims_mod_business_action_utilisateur as au
		WHERE		au.action_id = :idaction";


	$res=$db->query($sql, array(
		':idaction' => array('type' => PDO::PARAM_INT, 'value' => $actionid),
	));

	while ($f=$db->fetchrow($res)) {
		if ($f['user_id']==$_SESSION['dims']['userid']) {
			$findme=true;
		}
		else $tabtemp[$f['user_id']]=$f['user_id'];
	}

	if (!$findme) {
		// on rajout temporairement pour voir
		foreach($tabtemp as $uid) {
			$_SESSION['dims']['training']['currentusertemp'][$uid]=$uid;
		}
	}
}

// reset usersearch
$resetsearch=dims_load_securvalue('resetsearch',dims_const::_DIMS_NUM_INPUT,true,false);
if ($resetsearch>0) {
	unset($_SESSION['dims']['training']['currentusersearch']);
	unset($_SESSION['dims']['training']['currentuserresp']);
	unset($_SESSION['dims']['training']['currentusertemp']);
}

// switch current select user
$currentuser=dims_load_securvalue('currentuser',dims_const::_DIMS_NUM_INPUT,true,false);
if ($currentuser>0) {
	$_SESSION['dims']['training']['currentuser']=$currentuser;
}
else $_SESSION['dims']['training']['currentuser']=$_SESSION['dims']['userid'];

$currentworkspacesearch=dims_load_securvalue('currentworkspacesearch',dims_const::_DIMS_CHAR_INPUT,true,false);
// switch current selected workspace
if (!isset($_SESSION['dims']['training']['currentworkspacesearch']) || $_SESSION['dims']['training']['currentworkspacesearch']=="") $_SESSION['dims']['training']['currentworkspacesearch']=$_SESSION['dims']['workspaceid'];
if ($currentworkspacesearch!="" && $currentworkspacesearch!=$_SESSION['dims']['training']['currentworkspacesearch']) {
	$_SESSION['dims']['training']['currentworkspacesearch']=$currentworkspacesearch;
	$_SESSION['dims']['training']['currentprojectsearch']=0;

	unset($_SESSION['dims']['training']['currentuserresp']);
	unset($_SESSION['dims']['training']['currentusertemp']);

	// on recalcule la recherche
	require_once(DIMS_APP_PATH . "/modules/system/class_user.php");
	$dims_user= new user();
	$dims_user->open($_SESSION['dims']['userid']);

	if (!isset($nomsearch)) $nomsearch="";
	$lstusers=$dims_user->getusersgroup($nomsearch,$_SESSION['dims']['training']['currentworkspacesearch'],$_SESSION['dims']['training']['currentprojectsearch']);
	$_SESSION['dims']['training']['currentuserresp']=$lstusers;
}

//switch sur la liste des projets
$currentprojectsearch=dims_load_securvalue('currentprojectsearch',dims_const::_DIMS_CHAR_INPUT,true,false);

// switch current selected workspace
if (!isset($_SESSION['dims']['training']['currentprojectsearch']) || $_SESSION['dims']['training']['currentprojectsearch']=="") {
	$_SESSION['dims']['training']['currentprojectsearch']=0;
}
if ($currentprojectsearch!="" && $currentprojectsearch!=$_SESSION['dims']['training']['currentprojectsearch']) {
	$_SESSION['dims']['training']['currentprojectsearch']=$currentprojectsearch;
}

/*// type action
//switch sur la liste des projets
$currenttypeactionsearch=dims_load_securvalue('currenttypeactionsearch',_DIMS_CHAR_INPUT,true,false);

// switch current selected typeaction
if (!isset($_SESSION['dims']['training']['currenttypeactionsearch']) || $_SESSION['dims']['training']['currenttypeactionsearch']=="") {
	$_SESSION['dims']['training']['currenttypeactionsearch']=0;
}
if ($currenttypeactionsearch!="" && $currenttypeactionsearch!=$_SESSION['dims']['training']['currenttypeactionsearch']) {
	$_SESSION['dims']['training']['currenttypeactionsearch']=$currenttypeactionsearch;
}*/

if(!isset($_SESSION['dims']['training']['currenttrainer'])) $_SESSION['dims']['training']['currenttrainer'] = -1;
$currenttrainer = dims_load_securvalue('currenttrainer',dims_const::_DIMS_CHAR_INPUT,true,false,true,$_SESSION['dims']['training']['currenttrainer']);

$currentstatus=dims_load_securvalue('currentstatus',dims_const::_DIMS_CHAR_INPUT,true,false);

// switch current selected typeaction
if (!isset($_SESSION['dims']['training']['currentstatus']) || $_SESSION['dims']['training']['currentstatus']=="") {
	$_SESSION['dims']['training']['currentstatus']=-1;
}
if ($currentstatus!="" && $currentstatus!=$_SESSION['dims']['training']['currentstatus']) {
	$_SESSION['dims']['training']['currentstatus']=$currentstatus;
	unset($_SESSION['dims']['schooltrain']['planning']['id_task']);
}

//gestion du type de session à afficher
$currenttype=dims_load_securvalue('currenttype',dims_const::_DIMS_CHAR_INPUT,true,false);

// switch current selected typeaction
if (!isset($_SESSION['dims']['training']['currenttype']) || $_SESSION['dims']['training']['currenttype']=="") {
	$_SESSION['dims']['training']['currenttype']=-1;
}
if ($currenttype!="" && $currenttype!=$_SESSION['dims']['training']['currenttype']) {
	$_SESSION['dims']['training']['currenttype']=$currenttype;
	unset($_SESSION['dims']['schooltrain']['planning']['id_task']);
}

if(!isset($_SESSION['dims']['elisath']['type_week'])) $_SESSION['dims']['elisath']['type_week'] = 0;
$typeweek_id	= dims_load_securvalue('type_week',dims_const::_DIMS_NUM_INPUT,true,true,true,$_SESSION['dims']['elisath']['type_week'],null,true);

if(!isset($_SESSION['dims']['elisath']['acti_type'])) $_SESSION['dims']['elisath']['acti_type'] = 0;
$actitype_id	= dims_load_securvalue('acti_type',dims_const::_DIMS_NUM_INPUT,true,true,true,$_SESSION['dims']['elisath']['acti_type'],null,true);

if(!isset($_SESSION['dims']['elisath']['trainer_id'])) $_SESSION['dims']['elisath']['trainer_id'] = 0;
$trainer_id		= dims_load_securvalue('trainer_id',dims_const::_DIMS_NUM_INPUT,true,true,true,$_SESSION['dims']['elisath']['trainer_id'],null,true);

if(!isset($_SESSION['dims']['elisath']['week_state'])) $_SESSION['dims']['elisath']['week_state'] = elisath_type_week::STATE_ACTIF;
$week_state		= dims_load_securvalue('week_state',dims_const::_DIMS_NUM_INPUT,true,true,true,$_SESSION['dims']['elisath']['week_state'],null,true);

if(!isset($_SESSION['dims']['elisath']['acti_state'])) $_SESSION['dims']['elisath']['acti_state'] = elisath_activite::STATE_ACTIF;
$acti_state		= dims_load_securvalue('acti_state',dims_const::_DIMS_NUM_INPUT,true,true,true,$_SESSION['dims']['elisath']['acti_state'],null,true);

if(!isset($_SESSION['dims']['elisath']['trainer_state'])) $_SESSION['dims']['elisath']['trainer_state'] = elisath_user::STATE_ACTIF;
$trainer_state	= dims_load_securvalue('trainer_state',dims_const::_DIMS_NUM_INPUT,true,true,true,$_SESSION['dims']['elisath']['trainer_state'],null,true);

if(!isset($_SESSION['dims']['elisath']['creneau_filter'])) $_SESSION['dims']['elisath']['creneau_filter'] = 0;
$creneau_filter	= dims_load_securvalue('creneau_filter',dims_const::_DIMS_NUM_INPUT,true,true,true,$_SESSION['dims']['elisath']['creneau_filter'],null,true);

if(!isset($_SESSION['dims']['elisath']['creneau_state'])) $_SESSION['dims']['elisath']['creneau_state'] = elisath_creneau::STATE_ACTIF;
$creneau_state	= dims_load_securvalue('creneau_state',dims_const::_DIMS_NUM_INPUT,true,true,true,$_SESSION['dims']['elisath']['creneau_state'],null,true);

require_once(DIMS_APP_PATH . '/modules/elisath/public_planning_display.php');
?>
