<?php
if (isset($_GET['dayadd'])) {
	unset($_SESSION['business']['business_userid']);
	unset($_SESSION['business']['business_weekadd']);
	unset($_SESSION['business']['business_dayadd']);
	unset($_SESSION['business']['business_monthadd']);

	$_SESSION['business']['business_dayadd'] = dims_load_securvalue('dayadd', dims_const::_DIMS_NUM_INPUT, true, true, true);
	$_SESSION['business']['business_viewmode'] = 'week';
}

if (isset($_GET['monthadd'])) {
	unset($_SESSION['business']['business_userid']);
	unset($_SESSION['business']['business_weekadd']);
	unset($_SESSION['business']['business_dayadd']);
	unset($_SESSION['business']['business_monthadd']);

	$_SESSION['business']['business_monthadd'] = dims_load_securvalue('monthadd', dims_const::_DIMS_NUM_INPUT, true, true, true);
	$_SESSION['business']['business_viewmode'] = 'month';
}

if (isset($actionid) && $actionid>0) {
	$tabtemp=array();
	$findme=false;
	$_SESSION['business']['business_actionid'] = $actionid;
	// recherche si la personne y participe ou non
	$sql = "
		SELECT		user_id
		FROM		dims_mod_business_action_utilisateur as au
		WHERE		au.action_id = :actionid ";


	$res=$db->query($sql, array(
		':actionid' => $actionid
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
			$_SESSION['dims']['planning']['currentusertemp'][$uid]=$uid;
		}
	}
}

// reset usersearch
$resetsearch=dims_load_securvalue('resetsearch',dims_const::_DIMS_NUM_INPUT,true,false);
if ($resetsearch>0) {
	unset($_SESSION['dims']['planning']['currentusersearch']);
	unset($_SESSION['dims']['planning']['currentuserresp']);
	unset($_SESSION['dims']['planning']['currentusertemp']);
}

// switch current select user
$currentuser=dims_load_securvalue('currentuser',dims_const::_DIMS_NUM_INPUT,true,false);
if ($currentuser>0) {
	$_SESSION['dims']['planning']['currentuser']=$currentuser;
}
else $_SESSION['dims']['planning']['currentuser']=$_SESSION['dims']['userid'];

$currentworkspacesearch=dims_load_securvalue('currentworkspacesearch',dims_const::_DIMS_CHAR_INPUT,true,false);
// switch current selected workspace
if (!isset($_SESSION['dims']['planning']['currentworkspacesearch']) || $_SESSION['dims']['planning']['currentworkspacesearch']=="") $_SESSION['dims']['planning']['currentworkspacesearch']=$_SESSION['dims']['workspaceid'];
if ($currentworkspacesearch!="" && $currentworkspacesearch!=$_SESSION['dims']['planning']['currentworkspacesearch']) {
	$_SESSION['dims']['planning']['currentworkspacesearch']=$currentworkspacesearch;
	$_SESSION['dims']['planning']['currentprojectsearch']=0;

	unset($_SESSION['dims']['planning']['currentuserresp']);
	unset($_SESSION['dims']['planning']['currentusertemp']);

	// on recalcule la recherche
	require_once(DIMS_APP_PATH . "/modules/system/class_user.php");
	$dims_user= new user();
	$dims_user->open($_SESSION['dims']['userid']);

	if (!isset($nomsearch)) $nomsearch="";
	$lstusers=$dims_user->getusersgroup($nomsearch,$_SESSION['dims']['planning']['currentworkspacesearch'],$_SESSION['dims']['planning']['currentprojectsearch']);
	$_SESSION['dims']['planning']['currentuserresp']=$lstusers;
}

//switch sur la liste des projets
$currentprojectsearch=dims_load_securvalue('currentprojectsearch',dims_const::_DIMS_CHAR_INPUT,true,false);

// switch current selected workspace
if (!isset($_SESSION['dims']['planning']['currentprojectsearch']) || $_SESSION['dims']['planning']['currentprojectsearch']=="") {
	$_SESSION['dims']['planning']['currentprojectsearch']=0;
}
if ($currentprojectsearch!="" && $currentprojectsearch!=$_SESSION['dims']['planning']['currentprojectsearch']) {
	$_SESSION['dims']['planning']['currentprojectsearch']=$currentprojectsearch;
}

// type action
//switch sur la liste des projets
$currenttypeactionsearch=dims_load_securvalue('currenttypeactionsearch',dims_const::_DIMS_CHAR_INPUT,true,false);

// switch current selected typeaction
if (!isset($_SESSION['dims']['planning']['currenttypeactionsearch']) || $_SESSION['dims']['planning']['currenttypeactionsearch']=="") {
	$_SESSION['dims']['planning']['currenttypeactionsearch']=0;
}
if ($currenttypeactionsearch!="" && $currenttypeactionsearch!=$_SESSION['dims']['planning']['currenttypeactionsearch']) {
	$_SESSION['dims']['planning']['currenttypeactionsearch']=$currenttypeactionsearch;
}

//echo $skin->open_simplebloc($_DIMS['cste']['_PLANNING'],"");
require_once(DIMS_APP_PATH . '/modules/system/public_planning_display.php');
//echo $skin->close_simplebloc();
//echo $skin->close_backgroundbloc();
?>


