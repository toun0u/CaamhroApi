<?php defined('AUTHORIZED_ENTRY_POINT') or exit;
ob_start();
ini_set('session.gc_probability', 0);

require_once(DIMS_APP_PATH . "modules/system/class_tab.php");
require_once(DIMS_APP_PATH . "modules/system/class_tab_manager.php"); // Avant le session start
session_start();
require_once(DIMS_APP_PATH . "modules/system/class_dims.php");
require_once(DIMS_APP_PATH . "include/class_assets_manager.php");
require_once(DIMS_APP_PATH . "include/class_style_manager.php");
require_once(DIMS_APP_PATH . "include/class_script_manager.php");
require_once(DIMS_APP_PATH . "modules/system/class_view.php");
require_once(DIMS_APP_PATH . "modules/system/class_form.php");
require_once(DIMS_APP_PATH . "modules/system/class_form_block.php");

// Set include PATH to DIMS_APP_PATH
set_include_path(get_include_path() . PATH_SEPARATOR . DIMS_APP_PATH);

if (!file_exists(DIMS_ROOT_PATH.'/config.php')) {
	if (file_exists(DIMS_APP_PATH.'install.php')) require_once(DIMS_APP_PATH.'install.php');
	die();
}
$view = new view($_SESSION['dims']['view']['flash']);
view::setInstance($view);

// INITIALIZE DIMS OBJECT + SETTING AS AN INSTANCE TO FAKE A SINGLETON BEHAVIOUR
$dims = new dims();
dims::setInstance($dims);
// Path ENV
// for mac use
if (is_dir("/opt/local/bin/")) {
	if (!isset($_ENV["PATH"]))
		putenv("PATH=/opt/local/bin/");
	else
		putenv("PATH=" .$_ENV["PATH"]. ":/opt/local/bin/");
}

// SMARTY ENGINE
if (!file_exists(_SMARTY_API)) {
	echo _SMARTY_API.' ==> '._DIMS_SMARTY_CONFIG_ERROR;	die();
}
else require(_SMARTY_API);

// INITIALIZE SMARTY OBJECT
$smarty = new Smarty();
$smartypath=_SMARTY_PATH;
$smarty->cache_dir = $smartypath.'/cache';
$smarty->config_dir = $smartypath.'/configs';

if( defined('_DIMS_CONNEXION_COLOR')){
	$smarty->assign('connexion_color', _DIMS_CONNEXION_COLOR);
	$view->assign('connexion_color', _DIMS_CONNEXION_COLOR);
}

if (defined ('_SMARTY_DEBUG'))
	$smarty->debugging = _SMARTY_DEBUG;

require_once DIMS_APP_PATH.'include/class_timer.php' ;
$dims_timer = new timer(); // START DIMS TIMER
$dims_timer->start();

//Affectation du timer à l'objet Dims
$dims->setTimer($dims_timer);

$_SESSION['dims']['smarty_path']=$smartypath;

// LOAD GLOBALS, VARS & FUNCTIONS
include DIMS_APP_PATH.'include/global.php';
include DIMS_APP_PATH.'include/class_dims_data_object_dynamic.php';
include DIMS_APP_PATH.'include/class_user_action_log.php' ;
include DIMS_APP_PATH.'include/class_error.php';
include DIMS_APP_PATH.'include/class_connecteduser.php';
include DIMS_APP_PATH.'include/class_security_filter.php';
include DIMS_APP_PATH.'include/class_formulaire.php';
include DIMS_APP_PATH.'include/class_debug.php';
include DIMS_APP_PATH.'modules/system/class_user.php';
include DIMS_APP_PATH.'modules/system/class_profile.php';
include DIMS_APP_PATH.'modules/system/class_group.php';
include DIMS_APP_PATH.'modules/system/class_workspace.php';
include_once DIMS_APP_PATH.'modules/system/class_pagination.php';

include DIMS_APP_PATH.'modules/system/class_mb_table.php';
include_once DIMS_APP_PATH.'modules/system/class_mb_class.php';
include_once DIMS_APP_PATH.'modules/system/class_mb_object.php';
include_once DIMS_APP_PATH.'modules/system/class_mb_object_relation.php';
include DIMS_APP_PATH.'modules/system/class_dims_sync.php';
include DIMS_APP_PATH.'modules/system/class_dims_sync_matrix.php';
include DIMS_APP_PATH.'modules/system/class_dims_sync_data.php';
include DIMS_APP_PATH.'modules/system/class_dims_sync_corresp.php';

include DIMS_APP_PATH.'modules/system/class_share.php';
// error reporting level
error_reporting(_DIMS_ERROR_REPORTING);

// retrieve name server
if (!isset($_DIMS['_DIMS_NAME_SERVER'])) {
	$_DIMS['_DIMS_NAME_SERVER'] = "Dims";//shell_exec("uname -n");
}
else define ('_DIMS_NAME_SERVER', $_DIMS['_DIMS_NAME_SERVER']);

// set default header
$dims->loadHeader();

// check logout
$dims->checkLogout();

if (file_exists(DIMS_APP_PATH.'/include/db/class_db_'._DIMS_SQL_LAYER.'.php')) require_once DIMS_APP_PATH.'/include/db/class_db_'._DIMS_SQL_LAYER.'.php';

// GLOBALS
$dims->setInitSession(false);
global $scriptenv;

// INIT VARIABLES
$db = new dims_db(_DIMS_DB_SERVER, _DIMS_DB_LOGIN, _DIMS_DB_PASSWORD, _DIMS_DB_DATABASE);
if(!$db->isconnected()) {
	trigger_error(dims_const::_DIMS_MSG_DBERROR, E_USER_ERROR);
}
$dims->setDb($db);
//dims_print_r($dims->getTableDescription('dims_constant'));
// SYNCHRO : check if synchro Dims activated
$dims->loadTablesSynchro($_DIMS,true); // pour l'instant on recalcul √† chaque fois

// check IDS
if (_DIMS_IDS) {
	//set_include_path(_IDS_API);
	ini_set('include_path', ini_get('include_path').':'._IDS_API);
	require_once _IDS_API.'IDS/Init.php';
	//'REQUEST' => $_REQUEST,
	$request = array(

		'GET' => $_GET,
		'POST' => $_POST,
		'COOKIE' => $_COOKIE
	);

	$init = IDS_Init::init(_IDS_API.'IDS/Config/Config.ini');
	//$init->config['General']['use_base_path'] = true;
	$init->config['Caching']['caching'] = 'none';
	$ids = new IDS_Monitor($request, $init);
	$resultids = $ids->run();

	if (!$resultids->isEmpty()) {
		// Take a look at the result object
		if ($resultids->getImpact()>_DIMS_IDS_SECURITY_LEVEL) {
			$dims->setError(2);
			securityCheck(dims_const::_DIMS_SECURITY_LEVEL_CRITICAL,'',$resultids);
		}
	}
}
//Cyril -> initialisation de la metabase (mb_object, table, classes, relations, etc...)
$dims->init_metabase();
// inputs filter
require_once DIMS_APP_PATH.'include/import_gpr.php';

if (empty($_SESSION) || (isset($_SESSION['dims']['host']) && $_SESSION['dims']['host'] != $_SERVER['HTTP_HOST']))  $dims->sessionReset();
$dims->sessionUpdate();

// check logout
$dims->checkLogout(); //Cyril - on le refait après l'import GPR au cas où y'ait eu un url_encode

$scriptenv=$dims->getScriptEnv();
///////////////////////////////////////////////////////////////////////////
// INITIALIZE ERROR HANDLER
///////////////////////////////////////////////////////////////////////////
require_once DIMS_APP_PATH.'include/errors.php';

////////////////////////////////////////////////////////////////////////////
// SECURITY CHECK
////////////////////////////////////////////////////////////////////////////
$dims->verifSecurityLevel();

///////////////////////////////////////////////////////////////////////////
// LOGIN REQUEST
///////////////////////////////////////////////////////////////////////////
unset($dims_login);
unset($dims_password);

// Cas particulier (catalogue) de connexion depuis un mail, ou la fonction "utiliser" sur un client
$already_hashed = dims_load_securvalue('already_hashed', dims_const::_DIMS_NUM_INPUT, true, false, false);
if ($already_hashed) {
	//Dans ce cas les paramètres sont passé en get et non post
	$dims_login=dims_load_securvalue('dims_login',dims_const::_DIMS_CHAR_INPUT,true,false,true); // ('field',type=num,get,post,sqlfilter=false)
	$dims_password=dims_load_securvalue('dims_password',dims_const::_DIMS_CHAR_INPUT,true,false,false); // ('field',type=num,get,post,sqlfilter=false)
}
else {
	$dims_login=dims_load_securvalue('dims_login',dims_const::_DIMS_CHAR_INPUT,false,true,true); // ('field',type=num,get,post,sqlfilter=false)
	$dims_password=dims_load_securvalue('dims_password',dims_const::_DIMS_CHAR_INPUT,false,true,false); // ('field',type=num,get,post,sqlfilter=false)
}
$dims_rfid=dims_load_securvalue('rfid-auth',dims_const::_DIMS_CHAR_INPUT,false,true,true);
if ((!empty($dims_login) && !empty($dims_password)) || !empty($dims_rfid)) $dims->verifyConnect($dims_login,$dims_password,$already_hashed,$dims_rfid);
else $dims->updateConnect();

///////////////////////////////////////////////////////////////////////////
// reload setting if needed
///////////////////////////////////////////////////////////////////////////
//$reloadsession=dims_load_securvalue('reloadsession',dims_const::_DIMS_NUM_INPUT,true,true,true);
$reloadsession=dims_load_securvalue('reloadsession',dims_const::_DIMS_NUM_INPUT,true,true,true);
if ($reloadsession) $dims->setInitSession(true);

///////////////////////////////////////////////////////////////////////////
// GET OR SET LANGUAGE
///////////////////////////////////////////////////////////////////////////
if (!isset($_SESSION['dims']['code_of_conduct'])) $_SESSION['dims']['code_of_conduct']=false;

$dimslang=dims_load_securvalue('dimslang',dims_const::_DIMS_NUM_INPUT,true,true,true);
unset($_DIMS['cste']);
if (!isset($_SESSION['dims']['lang']) || $dimslang>0) {
	unset($_SESSION['dims']['lang']);
	$select = "select * from dims_lang order by id";
	$res=$db->query($select);
	while ($fields = $db->fetchrow($res)) {
		$_SESSION['dims']['lang'][$fields['id']]=$fields['label'];
		if ($fields['code_of_conduct']!='') {
			$_SESSION['dims']['code_of_conduct']=true;
		}
	}
	// langue courante
	if ($dimslang>0 && isset($_SESSION['dims']['lang'][$dimslang])) $_SESSION['dims']['currentlang'] = $dimslang;
	else $_SESSION['dims']['currentlang'] = 1;

	// define currentlang for dims object
	$dims->setLang($_SESSION['dims']['currentlang']);
}

//dims_print_r($_SESSION['dims']['index']);

// GET WORKSPACES (FOR THIS DOMAIN)
// on en profite pour appliquer l'heritage implicite des domaines pour les sous-espaces de travail
$dims->initWorkspaces();

// check if mobile version or not
$dims->checkMobile();
$load_language = false;

// LOAD USER PROFILE
if ($dims->getUserId()>0) {

	$dims_user = new user();
		if (isset($_SESSION['dims']['userfromid']) && $_SESSION['dims']['userfromid']>0) {
			$dims_user->open($_SESSION['dims']['userfromid']);
		}
		else {
			$dims_user->open($_SESSION['dims']['userid']);
		}

	$dims_user->updateState();
	$_SESSION['dims']['user'] = $dims_user->fields;
	$_SESSION['dims']['actions'] = array();

	$profile = new profile();
	$workspace = new workspace();
	$workspace_allowed = 0;
	$actions=array();
	// chargement des actions du user
	$dims_user->getactions($actions);
	$_SESSION['dims']['actions']=$actions;
	$dims->setActions($actions);

	// fusion des workspaces avec ceux du user
	$dims->intersectUserWorkspaces($dims_user->getworkspaces());

	if ($dims_user->fields['lang']>0 && isset($_SESSION['dims']['lang'][$dims_user->fields['lang']])) {
		//if (!isset($_SESSION['cste'])) {
			$_DIMS['cste']=$dims->loadLanguage($dims_user->fields['lang']);
			$load_language = true;
		//}
	}

	//chargement des modules concernes par les workspaces disponibles
	$dims->initUserModules();

	// chargement du profile du user
	$dims->initUserProfile();

	if (sizeof($dims->getWorkspaces())==0) {

		session_destroy();
		$_SESSION = array();
		if (!_DIMS_DEBUGMODE) dims_redirect("./index.php");
		else dims_redirect("{$scriptenv}?dims_errorcode=NOWORKSPACE");
	}

	// sorting workspaces by depth
	//uksort ($_SESSION['dims']['workspaces'], 'dims_workspace_sort');
	if (!isset($_GET['reloadsession'])) $dims_mainmenu = dims_const::_DIMS_MENU_HOME;

	// verify if redirect to url called
	if (isset($_SESSION['REQUEST_URI'])) {
		$urlto=$_SESSION['REQUEST_URI'];
		unset($_SESSION['REQUEST_URI']);
		dims_redirect($urlto,false);
	}

	// on reswitche sur le user courant pour le reste du traitement
	if (isset($_SESSION['dims']['userfromid']) && $_SESSION['dims']['userfromid']>0) {
		$dims_user->open($_SESSION['dims']['userid']);
		$_SESSION['dims']['user'] = $dims_user->fields;
	}
}
else {
	//chargement des modules concernes par les workspaces disponibles
	$dims->initUserModules();
}

///////////////////////////////////////////////////////////////////////////
// LOAD LANGUAGE CONSTANTE
///////////////////////////////////////////////////////////////////////////
$changelang = false;
$lang = dims_load_securvalue('lang', dims_const::_DIMS_CHAR_INPUT, true, false, true);
if ($lang != '') {
	require_once DIMS_APP_PATH.'modules/system/class_lang.php';
	if (($dims_lang = lang::getByRef($lang)) !== null) {
		$changelang = true;
		$_DIMS['cste'] = $dims->loadLanguage($dims_lang->getId());
	}
}
if (!$changelang) {
	$_DIMS['cste'] = $dims->loadLanguage();
}

///////////////////////////////////////////////////////////////////////////
// SWITCH FRONT/BACK ON script filename
///////////////////////////////////////////////////////////////////////////

switch($dims->getScriptEnv()) {
	case 'index.php':
		if (isset($_SESSION['REQUEST_URI'])) unset($_SESSION['REQUEST_URI']);

		if (!empty($_GET['wce_mode'])) {
			// cas secial du mode de rendu public du module WCE (on utilise le rendu frontoffice sans activer tout le processus)
			$newmode = 'web';
		}
		else {
			$lstwebworkspaces=$dims->getWebWorkspaces();

			$newmode = (_DIMS_FRONTOFFICE && is_dir(DIMS_APP_PATH.'modules/wce/') && !empty($lstwebworkspaces)) ? 'web' : 'admin';

			if ($_SESSION['dims']['mode'] != $newmode && $newmode == 'web') {
				// on prend le premier du tableau
				/*reset($_SESSION['dims']['hosts']['web']);
				$cur=current($_SESSION['dims']['hosts']['web']);

				$_SESSION['dims']['workspaceid'] = $cur;

				foreach($_SESSION['dims']['hosts']['web'] as $wid) {
					$workspace = new workspace();
					$workspace->open($wid);
					$_SESSION['dims']['currentworkspace']=$workspace;
					$_SESSION['dims']['workspaces'][$wid] = array_merge($_SESSION['dims']['workspaces'][$wid], $workspace->fields);
				}

				if (isset($_SESSION['dims']['currentworkspace']['wcemoduleid'])) {
					$_SESSION['dims']['wcemoduleid'] = $_SESSION['dims']['currentworkspace']['wcemoduleid'];
					// TEST A VALIDER
					$_SESSION['dims']['moduleid'] = $_SESSION['dims']['wcemoduleid'];
					require_once(DIMS_APP_PATH.'modules/system/class_module.php');
					$currentmod= new module();
					$currentmod->open($_SESSION['dims']['moduleid']);
					$_SESSION['dims']['currentmodule']=$currentmod->fields;
				}
				*/
				reset($lstwebworkspaces);
				$cur=current($lstwebworkspaces);
				$mods=$dims->getWceModules();
				$curmod="";

				if (sizeof($mods) >0) {
					$curmod=current($mods);
					$_SESSION['dims']['currentworkspace']['wcemoduleid']=$curmod;
					$_SESSION['dims']['wcemoduleid'] = $curmod;

					//$_SESSION['dims']['wcemoduleid'] = $curmod;
				}
				else {
					echo "Erreur de parametre";die();
				}
				// save current workspace
				$_SESSION['dims']['back_workspaceid'] = $_SESSION['dims']['workspaceid'];
				$_SESSION['dims']['back_moduleid'] = $_SESSION['dims']['moduleid'];
				$_SESSION['dims']['back_currentworkspace']=$_SESSION['dims']['currentworkspace'];

				$_SESSION['dims']['workspaceid'] = $cur['id'];
				$_SESSION['dims']['currentworkspace']=$cur;
			}
		}
		$_SESSION['dims']['mode'] = $newmode;
		if (isset($_SESSION['dims']['workspaceid']) && $_SESSION['dims']['workspaceid']>0 && $dims->isConnected()) {
			if (isset($_SESSION['dims']['currentworkspace']['frontaccess_limited']) && $_SESSION['dims']['currentworkspace']['frontaccess_limited']) {
				// on test si on peut se connecter
				$workgroup = new workspace();
				$workgroup->open($_SESSION['dims']['workspaceid']);
				if (!$workgroup->isUserEnabled()) {
					// on arrete et on deconnecte la personne
					session_destroy();
					setcookie ("cookie", "", time() - 3600);
					$_SESSION = array();
					header("location: /index.php");
				}
			}

		}

	break;

}

/* loading operation action */
// security filter
$op=dims_load_securvalue('op',dims_const::_DIMS_CHAR_INPUT,true,true,false);

if (!isset($_SESSION['dims']['submainmenu'])) $_SESSION['dims']['submainmenu']=array();

///////////////////////////////////////////////////////////////////////////
// ADMIN SWITCHES
///////////////////////////////////////////////////////////////////////////

if ($_SESSION['dims']['mode'] == 'admin') {
	$dims_mainmenu=dims_load_securvalue('dims_mainmenu',dims_const::_DIMS_CHAR_INPUT,true,true,true);		// ('field',type=num,get,post,sqlfilter=false)

	$dims_submainmenu=dims_load_securvalue('dims_submainmenu',dims_const::_DIMS_CHAR_INPUT,true,true,true,$_SESSION['dims']['submainmenu'],'');
	//$dims_webworkspaceid=dims_load_securvalue('dims_webworkspaceid',dims_const::_DIMS_NUM_INPUT,true,true,false);				// ('field',type=num,get,post,sqlfilter=false)
	$dims_moduletabid=dims_load_securvalue('dims_moduletabid',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['moduletabid']);	// ('field',type=num,get,post,sqlfilter=false)

	$dims_moduleicon=dims_load_securvalue('dims_moduleicon',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['moduleicon']);	// ('field',type=num,get,post,sqlfilter=false)

	$lstworkspaces=$dims->getAdminWorkspaces();
	$lstw=current($lstworkspaces);

	///////////////////////////////////////////////////////////////////////////
	// SWITCH MAIN MENU (Workspaces, Profile, etc.)
	///////////////////////////////////////////////////////////////////////////
	if (isset($_SESSION['dims']['workspaceid']) && empty($_SESSION['dims']['workspaceid'])) {
		if(isset($dims_user->fields['defaultworkspace']) && $dims_user->fields['defaultworkspace']>0 && isset($lstworkspaces[$dims_user->fields['defaultworkspace']]))
			$_SESSION['dims']['workspaceid'] = $dims_user->fields['defaultworkspace'];
		else {
			if (isset($lstw['id'])) {
				$_SESSION['dims']['workspaceid'] = $lstw['id'];
			}
		}

		//dims_loadparams();
		$_SESSION['dims']['moduleid'] = '';
		$_SESSION['dims']['currentmodule']=array();
		$_SESSION['dims']['action'] = 'public';
		$_SESSION['dims']['moduletabid'] = '';
		$_SESSION['dims']['moduleicon'] = '';
		$_SESSION['dims']['moduletype'] = '';
		$_SESSION['dims']['moduletypeid'] = '';
		$_SESSION['dims']['modulelabel'] = '';
				$_SESSION['dims']['userfromid'] = ''; // on reset le choix d'une personne
		$_SESSION['dims']['urlpath']= $dims->getUrlPath();

		$lg_workspace = new workspace();
		$lg_workspace->open($_SESSION['dims']['workspaceid']);

		if (!$load_language && isset($lg_workspace->fields['id_lang']) && $lg_workspace->fields['id_lang']>0 && isset($_SESSION['dims']['lang'][$lg_workspace->fields['id_lang']])) {
			if ($_SESSION['dims']['currentlang'] != $lg_workspace->fields['id_lang']) {
				$_DIMS['cste']=$dims->loadLanguage($lg_workspace->fields['id_lang']);
			}

		}
	}

	///////////////////////////////////////////////////////////////////////////
	// SWITCH WORKSPACE
	///////////////////////////////////////////////////////////////////////////
	$reloadworkspace=false;
	$ancienworkspaceid=$_SESSION['dims']['workspaceid'];
	// verification des droits d'acces a cet espace
	if (isset($_SESSION['dims']['workspaceid']))
		$dims_workspaceid=dims_load_securvalue('dims_workspaceid',dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['dims']['workspaceid'],$lstw['id']);
	else
		$dims_workspaceid=dims_load_securvalue('dims_workspaceid',dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['dims']['workspaceid'],0);

	if ($dims_workspaceid>0 && $dims->verifyWorkspaceExists($dims_workspaceid)) {
		if ($ancienworkspaceid!=$_SESSION['dims']['workspaceid']) {
			$_SESSION['dims']['mainmenu'] = dims_const::_DIMS_MENU_HOME;
			$_SESSION['dims']['moduleid'] = '';
			$_SESSION['dims']['currentmodule']=array();
			$_SESSION['dims']['action'] = 'public';
			$_SESSION['dims']['moduletabid'] = '';
			$_SESSION['dims']['moduleicon'] = '';
			$_SESSION['dims']['moduletype'] = '';
			$_SESSION['dims']['moduletypeid'] = '';
			$_SESSION['dims']['modulelabel'] = '';
			$_SESSION['dims']['userfromid'] = ''; // on reset le choix d'une personne
			$_SESSION['dims']['urlpath']= $dims->getUrlPath();
			$_SESSION['dims']['action'] = 'public';

			$lg_workspace = new workspace();
			$lg_workspace->open($_SESSION['dims']['workspaceid']);

			if ($lg_workspace->fields['id_lang']>0 && isset($_SESSION['dims']['lang'][$lg_workspace->fields['id_lang']])) {
				if ($_SESSION['dims']['currentlang'] != $lg_workspace->fields['id_lang']) {
					$_DIMS['cste']=$dims->loadLanguage($lg_workspace->fields['id_lang']);
				}
			}
			// init WCE
			unset($_SESSION['dims']['wce']);
			// init Desktop
			unset($_SESSION['dims']['search']);
			unset($_SESSION['dims']['advanced_search']);
			unset($_SESSION['desktopv2']);
			unset($_SESSION['desktop']);
			unset($_SESSION['dims']['modsearch']);
		}
		$_SESSION['dims']['workspaceid']=$dims_workspaceid;
		// reinit du back quand on switch d'espace de travail
		$_SESSION['dims']['back_workspaceid']=$_SESSION['dims']['workspaceid'];

	}
	else $dims_workspaceid = $_SESSION['dims']['workspaceid'];

	$mods=$dims->getModules();
	if (isset($mods[$_SESSION['dims']['workspaceid']])) $_SESSION['dims']['currentworkspace']['modules']=$mods[$_SESSION['dims']['workspaceid']];

	if ($reloadworkspace && isset($_SESSION['dims']['currentworkspace']['adminlevel']) && $_SESSION['dims']['currentworkspace']['admin']) {// new group selected

		$_SESSION['dims']['mainmenu'] = dims_const::_DIMS_MENU_HOME;
		$_SESSION['dims']['workspaceid'] = $dims_workspaceid;
		$_SESSION['dims']['moduleid'] = '';
		$_SESSION['dims']['currentmodule']=array();
		$_SESSION['dims']['action'] = 'public';
		$_SESSION['dims']['moduletabid'] = '';
		$_SESSION['dims']['moduleicon'] = '';
		$_SESSION['dims']['moduletype'] = '';
		$_SESSION['dims']['moduletypeid'] = '';
		$_SESSION['dims']['modulelabel'] = '';
				$_SESSION['dims']['userfromid'] = ''; // on reset le choix d'une personne
		$_SESSION['dims']['urlpath']= $urlpath;
		// load params
		//dims_loadparams();
	}

	if (isset($lstworkspaces[$_SESSION['dims']['workspaceid']])) {
			$_SESSION['dims']['currentworkspace']=$lstworkspaces[$_SESSION['dims']['workspaceid']];
		}

		// check for switching profilz
		$dims_switchuserfromid=dims_load_securvalue('dims_switchuserfromid',dims_const::_DIMS_NUM_INPUT,true,false,false);

		// check for swichting user id
		if ($dims_switchuserfromid>0 && isset($_SESSION['dims']['currentworkspace']['activeswitchuser']) && $_SESSION['dims']['currentworkspace']['activeswitchuser']) {
			$_SESSION['dims']['userfromid'] = $_SESSION['dims']['userid'];
			$_SESSION['dims']['userid'] = $dims_switchuserfromid;
			dims_redirect("/admin.php");
		}

		/////////////////////////////////////
	// SWITCH MODULE
	// verification des droits d'acces a ce module
	$tempmoduleid=dims_load_securvalue('dims_moduleid',dims_const::_DIMS_NUM_INPUT,true,true,false);
	if ($dims->isModuleEnabled($tempmoduleid,$_SESSION['dims']['workspaceid'])) $dims_moduleid = $tempmoduleid;

	///////////////////////////////////////////////////////////////////////////
	// CHOOSE TEMPLATE
	///////////////////////////////////////////////////////////////////////////
	$_SESSION['dims']['defaultskin']=$dims->getAdminTemplate();

	if (isset($_SESSION['dims']['currentworkspace']['admin_template']) && $_SESSION['dims']['currentworkspace']['admin_template'] != '') $_SESSION['dims']['template_name'] = $_SESSION['dims']['currentworkspace']['admin_template'];
	elseif ($_SESSION['dims']['defaultskin'] != '') $_SESSION['dims']['template_name'] = $_SESSION['dims']['defaultskin'];
	else $_SESSION['dims']['template_name']=_DIMS_DEFAULT_TEMPLATE;

	$_SESSION['dims']['template_path'] = "./common/templates/backoffice/{$_SESSION['dims']['template_name']}";

		$dims_action=dims_load_securvalue('dims_action',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['action'],'public');			// ('field',type=num,get,post,sqlfilter=false)
	///////////////////////////////////////////////////////////////////////////
	// LOOK FOR AUTOCONNECT MODULE
	///////////////////////////////////////////////////////////////////////////
	// get module for this workspace
	//echo $dims_moduleid. " ".$_SESSION['dims']['moduleid'];die();
	if ((!isset($dims_moduleid) || $dims_moduleid==0) && $_SESSION['dims']['moduleid'] == '' ) {
		$autoconnect_modules = $dims->getAutoConnectModules($_SESSION['dims']['workspaceid']);

		foreach($autoconnect_modules as $id => $struct) {
			$autoconnect_module_id=$struct['instanceid'];

			if ($_SESSION['dims']['connected'] && (!isset($dims_moduleid) || $dims_moduleid==0)  && $_SESSION['dims']['moduleid'] == '') {
				$dims_moduleid = $autoconnect_module_id;

				$_SESSION['dims']['mainmenu']=$struct['contenttype'];

				//if ($_SESSION['dims']['browser']['pda']) {
					// on redirige vers la connexion par defaut
				//	dims_redirect("admin.php?dims_moduleid=".$dims_moduleid."&moduleid=".$dims_moduleid."&dims_desktop=block&dims_action=public&op=viewpda");
				//}
				//else
				if ($dims->getEnabledBackoffice()) {
					dims_redirect("admin.php?dims_moduleid=".$dims_moduleid."&moduleid=".$dims_moduleid."&dims_desktop=block&dims_action=".$dims_action);
				}
			}
			//}
		}
	}

	if ($dims_mainmenu!="") {

		$_SESSION['dims']['mainmenu']=$dims_mainmenu;

		switch($_SESSION['dims']['mainmenu']) {
			case dims_const::_DIMS_MENU_PROFILE:
			case dims_const::_DIMS_MENU_ANNOTATIONS:
			case dims_const::_DIMS_MENU_TICKETS:
			case dims_const::_DIMS_MENU_PROJECTS:
			case dims_const::_DIMS_MENU_ABOUT:
			case dims_const::_DIMS_MENU_PLANNING:
			case dims_const::_DIMS_MENU_CONTACT:
			case dims_const::_DIMS_MENU_HOME:
			case dims_const::_DIMS_MENU_NEWSLETTER:
				$dims_moduleid = dims_const::_DIMS_MODULE_SYSTEM;
			break;
		}
	}

	///////////////////////////////////////////////////////////////////////////
	// SWITCH MODULE
	///////////////////////////////////////////////////////////////////////////
	if (isset($dims_moduleid) && $dims_moduleid>0 && $dims_moduleid != $_SESSION['dims']['moduleid']) {// new module selected
		//die($dims_moduleid);
		$_SESSION['dims']['moduleid']	= $dims_moduleid;
		require_once(DIMS_APP_PATH.'modules/system/class_module.php');
		$currentmod= new module();
		$currentmod->open($_SESSION['dims']['moduleid']);
		$_SESSION['dims']['currentmodule']=$currentmod->fields;

		$_SESSION['dims']['moduletabid']	= '';
		$_SESSION['dims']['moduleicon']		= '';

		$_SESSION['dims']['module_inter_id'] = '';

		/**
		* New module selected
		* => Load module informations
		*/

		$select =
		'SELECT dims_module.id, dims_module.id_module_type, dims_module.label, dims_module_type.label AS module_type
		FROM dims_module, dims_module_type
		WHERE dims_module.id_module_type = dims_module_type.id
		AND dims_module.id = :idmodule';

		$answer = $db->query($select, array(':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['moduleid'])));
		if ($fields = $db->fetchrow($answer)) {
			/* IMPORTANT */
			/* USE IT TO KNOW INFORMATION ABOUT CURRENT SELECTED MODULE */
			$_SESSION['dims']['moduletype'] = $fields['module_type'];
			$_SESSION['dims']['moduletypeid'] = $fields['id_module_type'];
			$_SESSION['dims']['modulelabel'] = $fields['label'];
		}
	}
	else {
				// on redirige si connexion
		if ($dims->getInitSession() && strlen($dims_login)>0) dims_redirect("admin.php?dims_mainmenu=".dims_const::_DIMS_MENU_HOME."&dims_desktop=block&dims_action=public");
	}
}

if ($dims->getUserId()>0) {
	//$dims_user->updateSystemTask();
}

//$dims_agenda_days[0]=$_DIMS['cste']['_SUNDAY'];
$dims_agenda_days[1]=$_DIMS['cste']['_MONDAY'];
$dims_agenda_days[2]=$_DIMS['cste']['_THUESDAY'];
$dims_agenda_days[3]=$_DIMS['cste']['_WEDNESDAY'];
$dims_agenda_days[4]=$_DIMS['cste']['_THIRDAY'];
$dims_agenda_days[5]=$_DIMS['cste']['_FRIDAY'];
$dims_agenda_days[6]=$_DIMS['cste']['_SATURDAY'];
$dims_agenda_days[7]=$_DIMS['cste']['_SUNDAY'];

$dims_agenda_months[1]=$_DIMS['cste']['_JANUARY'];
$dims_agenda_months[2]=$_DIMS['cste']['_FEBRUARY'];
$dims_agenda_months[3]=$_DIMS['cste']['_MARCH'];
$dims_agenda_months[4]=$_DIMS['cste']['_APRIL'];
$dims_agenda_months[5]=$_DIMS['cste']['_MAY'];
$dims_agenda_months[6]=$_DIMS['cste']['_JUNE'];
$dims_agenda_months[7]=$_DIMS['cste']['_JULY'];
$dims_agenda_months[8]=$_DIMS['cste']['_AUGUST'];
$dims_agenda_months[9]=$_DIMS['cste']['_SEPTEMBER'];
$dims_agenda_months[10]=$_DIMS['cste']['_OCTOBER'];
$dims_agenda_months[11]=$_DIMS['cste']['_NOVEMBER'];
$dims_agenda_months[12]=$_DIMS['cste']['_DECEMBER'];

// View modes for modules
$dims_viewmodes = array(	dims_const::_DIMS_VIEWMODE_UNDEFINED	=> $_DIMS['cste']['_DIMS_LABEL_UNDEFINED'],
							dims_const::_DIMS_VIEWMODE_PRIVATE		=> $_DIMS['cste']['_DIMS_LABEL_VIEWMODE_PRIVATE'],
							dims_const::_DIMS_VIEWMODE_DESC			=> $_DIMS['cste']['_LABEL_VIEWMODE_DESC'],
							dims_const::_DIMS_VIEWMODE_ASC			=> $_DIMS['cste']['_LABEL_VIEWMODE_ASC'],
							dims_const::_DIMS_VIEWMODE_GLOBAL		=> $_DIMS['cste']['_LABEL_VIEWMODE_GLOBAL']
						);

$dims_system_levels = array(	dims_const::_DIMS_ID_LEVEL_USER			=> $_DIMS['cste']['_DIMS_LABEL_USER'],
								dims_const::_DIMS_ID_LEVEL_GROUPMANAGER => $_DIMS['cste']['_DIMS_LEVEL_GROUPMANAGER'],
								dims_const::_DIMS_ID_LEVEL_GROUPADMIN	=> $_DIMS['cste']['_DIMS_LEVEL_GROUPADMIN'],
								dims_const::_DIMS_ID_LEVEL_SYSTEMADMIN	=> $_DIMS['cste']['_DIMS_LEVEL_SYSTEMADMIN']
							);

// kind of shortcuts for admin & workspaceid
if (isset($_SESSION['dims']['currentworkspace']['adminlevel']))
	$_SESSION['dims']['adminlevel'] = $_SESSION['dims']['currentworkspace']['adminlevel'];
else
	$_SESSION['dims']['adminlevel'] = 0;



///////////////////////////////////////////////////////////////////////////
// SOME SECURITY TESTS
///////////////////////////////////////////////////////////////////////////
$dims_errornum = 0;
if (!$_SESSION['dims']['connected']) {

	// can't be admin and not connected
	//if ($_SESSION['dims']['action'] == 'admin') {
	//	$_SESSION['dims']['action'] = 'public';
	//	$dims_errornum = 1;
	//}
	//if (!$dims_errornum && ($_SESSION['dims']['moduleid']!= '' && !isset($_SESSION['dims']['currentmodule']))) $dims_errornum = 3;
	//if (!$dims_errornum && ($_SESSION['dims']['moduleid']!= '' && !$_SESSION['dims']['currentmodule']['active'])) $dims_errornum = 5;
}
else {

	// update connected users
	$dims->updateLiveStats();

	// test moduleid
	//if (!$dims_errornum && ($_SESSION['dims']['moduleid']!= '' && !isset($_SESSION['dims']['currentmodule']))) $dims_errornum = 3;
	// test if module is active
	if (!$dims_errornum && ($_SESSION['dims']['moduleid']!= '' && isset($_SESSION['dims']['currentmodule']) &&	!$_SESSION['dims']['currentmodule']['active'])) $dims_errornum = 5;
	// test workspaceid
	if (!$dims_errornum && ($_SESSION['dims']['workspaceid']!= '' && !isset($_SESSION['dims']['currentworkspace']))) $dims_errornum = 6;
}

if ($dims_errornum) {
	session_destroy();
	$_SESSION=Array();
	echo "<html><body><div style=\"text-align:center;\"><br /><br /><h1>"._DIMS_ERROR."</h1>"._DIMS_ERROR_SYSTEMCONTACT."<br /><br /><b>erreur : $dims_errornum</b><br /><br /><a href=\"$scriptenv\">continuer</a></div></body></html>";
	die();
}

?>
