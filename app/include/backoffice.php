<?php defined('AUTHORIZED_ENTRY_POINT') or exit;

$_SESSION['dims']['MOBILE_VERSION'] = 0;

// TEMPLATE / SKIN
require_once DIMS_APP_PATH . '/include/class_block.php';
//require_once DIMS_APP_PATH . '/include/class_template.php';

if (!is_dir(DIMS_APP_PATH .str_replace('./common/', '', $_SESSION['dims']['template_path']))) $_SESSION['dims']['template_path'] = "./common/templates/backoffice/dims_v5";
if (!isset($_SESSION['dims']['template_path'])) $_SESSION['dims']['template_path']='';

$class_skin = DIMS_APP_PATH . str_replace('./common/', '', $_SESSION['dims']['template_path']) . "/class_skin.php";
if(file_exists($class_skin))
	require_once $class_skin;
else
	require_once DIMS_APP_PATH . "include/class_skin.php";

$skin = new skin();
$btn_search="";

// Baptiste
$view = view::getInstance();
if(!isset($_SESSION['cata']['articles']['flashes']))$_SESSION['cata']['articles']['flashes'] = array();
$view->initFlashStructure($_SESSION['cata']['articles']['flashes']);
$view->set_static_version('a7cb39400e01795b48c590dc2cc41269fa6bf33e');
$view->clear();//permet de réinitialiser les données en sessions liées aux tpls à utiliser

//$template_body = new Template($_SESSION['dims']['template_path']);
$template_name=$_SESSION['dims']['template_name'];
$template_path=str_replace('./common/', '', $_SESSION['dims']['template_path']);
$smarty->template_dir = DIMS_APP_PATH . $template_path;

if (!file_exists($smartypath.'/templates_c/'.$template_name)) mkdir ($smartypath."/templates_c/".$template_name."/", 0777, true);
$smarty->compile_dir = $smartypath."/templates_c/".$template_name."/";

$expression_brute=dims_load_securvalue('expression_brute',dims_const::_DIMS_CHAR_INPUT,true,true);
if ($expression_brute!='') {
	$_SESSION['dims']['modsearch']['expression_brut']=$expression_brute;
}

// si possible differencier login.tpl et index.tpl pour pouvoir disposer d'une interface de login differente
if (isset($_SESSION['dims']['moduleid']) && $_SESSION['dims']['moduleid']>0) $_SESSION['dims']['desktop']="block";

if (!isset($_SESSION['dims']['desktop']) && (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'])) {
	dims_redirect($scriptenv."?dims_mainmenu=0&dims_desktop=block&dims_action=public");
}

$_SESSION['dims']['desktop']=dims_load_securvalue('dims_desktop',dims_const::_DIMS_CHAR_INPUT,true,true,true, $_SESSION['dims']['desktop']); // ('field',type=num,get,post,sqlfilter=false)

$dims_op = dims_load_securvalue('dims_op', dims_const::_DIMS_CHAR_INPUT, true, true);

if (isset($dims_op) && $dims_op !== "") {
	require_once DIMS_APP_PATH.'include/op.php';
}

// soap server support
if (_DIMS_SOAP_SERVER) {
	include(DIMS_APP_PATH.'include/soap.php');
}

if (isset($displaysearch)) $_SESSION['dims']['active_search'][$_SESSION['dims']['workspaceid']]=1;

if (isset($_SESSION['dims']['workspaceid']) && isset($_SESSION['dims']['active_search'][$_SESSION['dims']['workspaceid']]) && $_SESSION['dims']['active_search'][$_SESSION['dims']['workspaceid']]) $dims_displaysearch="display:block;visibility:visible;";
else {
	$dims_displaysearch="display:none;visibility:hidden;";
}

$activeprofil=0;

if ($_SESSION['dims']['connected']) {
	$view->assign('switch_user_logged_in','');
	$smarty->assign('switch_user_logged_in','');

	$activeprofil	=dims_urlencode("$scriptenv?dims_mainmenu=".dims_const::_DIMS_MENU_PROFILE."&dims_desktop=block&dims_action=public");
	// calcul des droits d'admin et acces aux workspaces
	$tpl_accesgroupmanager="";
	$tpl_accesworkspaces="";
	$tpl_accesworkspaces_withlabel='';
	$tpl_accesgroupmanager_withlabel='';
	if ($_SESSION['dims']['adminlevel'] >= dims_const::_DIMS_ID_LEVEL_GROUPMANAGER) {
		if ($_SESSION['dims']['adminlevel'] >= dims_const::_DIMS_ID_LEVEL_SYSTEMADMIN) {
			$tpl_accesgroupmanager="<a href=\"".dims_urlencode("admin.php?dims_moduleid=".dims_const::_DIMS_MODULE_SYSTEM."&dims_desktop=block&dims_action=admin&system_level=system&dims_mainmenu=0")."\"><img src=\"./common/modules/system/img/system16.png\" alt=\"".$_DIMS['cste']['_DIMS_SYSTEM_ADMIN']."\" title=\"".$_DIMS['cste']['_DIMS_SYSTEM_ADMIN']."\"></a>";
			$tpl_accesgroupmanager_withlabel="<a href=\"".dims_urlencode("admin.php?dims_moduleid=".dims_const::_DIMS_MODULE_SYSTEM."&dims_desktop=block&dims_action=admin&system_level=system&dims_mainmenu=0")."\"><img src=\"./common/modules/system/img/system16.png\" alt=\"".$_DIMS['cste']['_DIMS_SYSTEM_ADMIN']."\" title=\"".$_DIMS['cste']['_DIMS_SYSTEM_ADMIN']."\">".$_DIMS['cste']['_DIMS_SYSTEM_ADMIN']."</a>";
		}
		$tpl_accesworkspaces = "<a href=\"".dims_urlencode("admin.php?dims_moduleid=".dims_const::_DIMS_MODULE_SYSTEM."&dims_desktop=block&dims_action=admin&system_level=".dims_const::_SYSTEM_WORKSPACES."&dims_mainmenu=0")."\"><img src=\"./common/modules/system/img/workspace16.png\" alt=\"".$_DIMS['cste']['_DIMS_SYSTEM_WORKSPSACE']."\" title=\"".$_DIMS['cste']['_DIMS_SYSTEM_WORKSPSACE']."\"></a>";
		$tpl_accesworkspaces_withlabel = "<a href=\"".dims_urlencode("admin.php?dims_moduleid=".dims_const::_DIMS_MODULE_SYSTEM."&dims_desktop=block&dims_action=admin&system_level=".dims_const::_SYSTEM_WORKSPACES."&dims_mainmenu=0")."\"><img src=\"./common/modules/system/img/workspace16.png\" alt=\"".$_DIMS['cste']['_DIMS_SYSTEM_WORKSPSACE']."\" title=\"".$_DIMS['cste']['_DIMS_SYSTEM_WORKSPSACE']."\">".$_DIMS['cste']['_DIMS_SYSTEM_WORKSPSACE']."</a>";
	}

	$view->assign('switch_user_logged_in','');
	$smarty->assign('switch_user_logged_in','');
	$user= new user();
	$user->open($_SESSION['dims']['userid']);
	$userlang="";

	$realLang = 'en';
	if (isset($_SESSION['dims']['lang']) && isset($_SESSION['dims']['currentlang']) && file_exists(DIMS_WEB_PATH . "./common/img/".$_SESSION['dims']['lang'][$_SESSION['dims']['currentlang']].".gif")) {
		$userlang="<img src=\"./common/img/".$_SESSION['dims']['lang'][$_SESSION['dims']['currentlang']].".gif\" alt=\"\">";
		switch(strtolower($_SESSION['dims']['lang'][$_SESSION['dims']['currentlang']])){
			case 'french':
				$realLang = 'fr';
				break;
			case 'german':
				$realLang = 'de';
				break;
			case 'japanese':
				$realLang = 'ja';
				break;
		}
	}elseif(isset($_SESSION['dims']['lang']) && isset($_SESSION['dims']['currentlang'])){
		switch(strtolower($_SESSION['dims']['lang'][$_SESSION['dims']['currentlang']])){
			case 'french':
				$realLang = 'fr';
				break;
			case 'german':
				$realLang = 'de';
				break;
			case 'japanese':
				$realLang = 'ja';
				break;
		}
	}

	if (isset($_SESSION['dims']['lang']) && isset($_SESSION['dims']['currentlang']) && file_exists(DIMS_WEB_PATH . "./common/img/".$_SESSION['dims']['lang'][$_SESSION['dims']['currentlang']].".gif")) {
		$userlang="<img src=\"./common/img/".$_SESSION['dims']['lang'][$_SESSION['dims']['currentlang']].".gif\" alt=\"\">";
	}

	$tpl_user=array(
			'LOGIN'				=> $_SESSION['dims']['login'],
			'FIRSTNAME'				=> $_SESSION['dims']['user']['firstname'],
			'LASTNAME'				=> $_SESSION['dims']['user']['lastname'],
			'EMAIL'				=> $_SESSION['dims']['user']['email'],
			'PHONEVOIP'					=> ($user->get('phoneforvoip') && !empty($user->fields['phone'])) ? $user->fields['phone'] : "null",
			'ACCESGROUPMANAGER'			=> $tpl_accesgroupmanager,
			'ACCESWORKSPACES'			=> $tpl_accesworkspaces,
			'GROUPMANAGER_WITHLABEL'		=> $tpl_accesgroupmanager_withlabel,
			'ACCESWORKSPACES_WITHLABEL'		=> $tpl_accesworkspaces_withlabel,
			'BACKGROUND'			=> $user->fields['background'],
			'LANGUAGE'				=> $userlang,
			'REAL_LANGUAGE'			=> $realLang,
	);

	$view->assign('user',$tpl_user);
	$smarty->assign('user',$tpl_user);

}
else {
	// on regarde l'url de connexion = si des arguments en param�tres en stock en session

	$_SESSION['REQUEST_URI']=$_SERVER['REQUEST_URI'];
	$view->assign('switch_user_logged_out','');
	$smarty->assign('switch_user_logged_out','');

	if (!empty($_GET['dims_errorcode'])) {
		$view->assign('switch_dimserrormsg','');
		$smarty->assign('switch_dimserrormsg','');
	}
}

/*$dims_ns_css="";
// GET ADDITIONAL CSS FROM NS
if (file_exists($_SESSION['dims']['template_path']."/NSTools.php")) {
	ob_start();
	require($_SESSION['dims']['template_path']."/NSTools.php");
	echo NS_CSS_SEGMENT($_SESSION['dims']['template_path']."/");
	$dims_ns_css = ob_get_contents();
	@ob_end_clean();
}*/

// GET MODULE ADDITIONAL JS
ob_start();
include(DIMS_APP_PATH . '/include/javascript.php');


$additional_javascript = ob_get_contents();
@ob_end_clean();

ob_start();
$array_modules = array();
$array_modules_admin = array();

if (!isset($_SESSION['dims']['action'])) $_SESSION['dims']['action']='';
if (!isset($typemod)) $typemod='';

if ($_SESSION['dims']['connected']) {

	// GET WORKSPACES
	// table des noms
	$array_workspace=array();

	foreach ($dims->getAdminWorkspaces() as $key => $value) {
		$array_workspace[$key]=$value['label']; //ucfirst(strtolower(trim($value['label'])));
	}

	asort($array_workspace);
	$tpl_workspaces=array();

	foreach( $array_workspace as $key => $label) {
		$tpl_workspaces[]=array(
			'TITLE' => $label,
			'URL' => dims_urlencode("{$scriptenv}?dims_workspaceid={$key}&dims_desktop=block&dims_mainmenu=0&force_desktop=1"),
			'SELECTED' => ( $key == $_SESSION['dims']['workspaceid']) ? 'selected' : ''
		);
	}
	$view->assign('workspaces',$tpl_workspaces);
	$smarty ->assign('workspaces',$tpl_workspaces);

	if (!isset($_SESSION['dims']['action'])) $_SESSION['dims']['action']='';
	if (!isset($typemod)) $typemod='';

	$c = 0;
	// GET MODULES
	//require_once(DIMS_APP_PATH . '/include/blocks.php');
	//dims_getBlocks($array_modules,$scriptenv,$_DIMS);
}

$tpl_module_css=array();
$tpl_module_css_ie=array();
$tpl_module_js=array();
$array_modules=$dims->getModules($_SESSION['dims']['workspaceid']);

foreach($array_modules as $blockmod) {
	$blocktype=$blockmod['label'];
	if (file_exists(DIMS_WEB_PATH . "/modules/{$blocktype}/include/styles.css")) {
		$tpl_module_css[]=array('PATH' => "./common/modules/{$blocktype}/include/styles.css");
	}

	if (file_exists(DIMS_WEB_PATH . "/modules/{$blocktype}/include/styles_ie.css")) {
		$tpl_module_css_ie[]=array('PATH' => "./common/modules/{$blocktype}/include/styles_ie.css");
	}

	if (file_exists(DIMS_WEB_PATH . "/modules/{$blocktype}/include/functions.js")) {
		$tpl_module_js[]=array('PATH' => "./common/modules/{$blocktype}/include/functions.js");
	}
}

$view->assign('modules_css',$tpl_module_css);
$view->assign('modules_css_ie',$tpl_module_css_ie);
$view->assign('modules_js',$tpl_module_js);
$smarty->assign('modules_css',$tpl_module_css);
$smarty->assign('modules_css_ie',$tpl_module_css_ie);
$smarty->assign('modules_js',$tpl_module_js);

$currentworkspace=$dims->getWorkspaces($_SESSION['dims']['workspaceid']);
// gestion des onglets principaux
$tpl_tab=array();

if ($_SESSION['dims']['connected'] && sizeof($array_modules)) {
	if (isset($array_modules[$_SESSION['dims']['moduleid']]['moduletype']))
		$_SESSION['dims']['moduletype']=$array_modules[$_SESSION['dims']['moduleid']]['moduletype'];
	// construction des options de menu
	if ($_SESSION['dims']['currentworkspace']['activesearch']){
		$view->assign('search','');
		$smarty->assign('search','');
	}
	if ($_SESSION['dims']['currentworkspace']['activeticket']){
		$view->assign('ticket','');
		$smarty->assign('ticket','');
	}
	if ($_SESSION['dims']['currentworkspace']['activeprofil']){
		$view->assign('profil','');
		$smarty->assign('profil','');
	}
	if ($_SESSION['dims']['currentworkspace']['activeannot']){
		$view->assign('annot','');
		$smarty->assign('annot','');
	}
	if (isset($_SESSION['dims']['currentworkspace']['activeswitchuser']) && $_SESSION['dims']['currentworkspace']['activeswitchuser']) {
		$arrayusersto=array();

		$workspace = new workspace();
		$workspace->open($_SESSION['dims']['workspaceid']);
		$users = $workspace->getusers();

		foreach($users as $userid => $user){
			if ($user['id']==$_SESSION['dims']['userid']) $user['SELECTED']= 'selected';
			else $user['SELECTED']= '';
			$arrayusersto[$userid]=$user;

		}

		$view->assign('dims_switchusers',$arrayusersto);
		$smarty->assign('dims_switchusers',$arrayusersto);

		unset ($arrayusersto);
	}
	// construction des projets rattaches aux espaces de travail
	/*
	$tpl_projects=dims_getProjets();

	if (!empty($tpl_projects)) {
		$smarty->assign('switch_projects','');
		$smarty->assign('projects',$tpl_projets);
	}
	*/
	// construction du bouton de recherche
	$btn_search=dims_create_button($_DIMS['cste']['_SEARCH'],"search","","idsearch","width:120px");
	if (!isset($_SESSION['dims']['mainmenu'])) $_SESSION['dims']['mainmenu']=dims_const::_DIMS_MENU_HOME;

	// recherche des autres typecontent
	//$workspace = new workspace();
	//$workspace->open($_SESSION['dims']['workspaceid']);
	//$listmod=$workspace->getmodules(false);
	$modtype_tab=array();

	// ajout du premier element vide de la liste
	$tpl_modules=array();

	$c=0;
	$found=false;

	// selection auto du premier element
	foreach($array_modules as $idmod => $mod) {
		if($mod['active'] && $mod['visible']) {
			if ($mod['contenttype']==$_SESSION['dims']['mainmenu']) {
				if ($idmod == $_SESSION['dims']['moduleid']) $found=true;
			}
		}
	}

	$change_mainmenu=true;
	//$change_mainmenu=false;
	// recherche si changement de mainmenu
	foreach($array_modules as $idmod => $mod) {
		if ($mod['contenttype']==$_SESSION['dims']['mainmenu']) {
			if ($idmod == $_SESSION['dims']['moduleid'] ) $change_mainmenu=false;
		}

		// test if module selected
		if ($_SESSION['dims']['moduleid']>0 && $_SESSION['dims']['moduleid']==$idmod) {
			if (isset($_SESSION['dims']['mainmenu']) && $_SESSION['dims']['mainmenu']!=$mod['contenttype']) {
				$change_mainmenu=true;
			}
		}
	}

	// boucle sur les modules courants
	foreach($array_modules as $idmod => $mod) {
		if($mod['active'] && $mod['visible'] && $mod['system']!=1) {
			if ($mod['contenttype']==$_SESSION['dims']['mainmenu']) {
				// test si module unique, alors s�lectionn�
				if (!$found || $change_mainmenu) {

					if ($dims->isModuleEnabled($idmod,$_SESSION['dims']['workspaceid'])) {
						$found=true;
						$change_mainmenu=false;
						$dims_moduleid = $idmod;
						$_SESSION['dims']['moduleid']=$idmod;
						require_once(DIMS_APP_PATH . '/modules/system/class_module.php');
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
						$select = "SELECT   dims_module.id,
											dims_module.id_module_type,
											dims_module.label,
											dims_module_type.label AS module_type
								   FROM     dims_module, dims_module_type
								   WHERE    dims_module.id_module_type = dims_module_type.id
								   AND      dims_module.id = :moduleid
								   AND      dims_module.id_module_type != 1";

						$answer = $db->query($select, array(
							':moduleid' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['moduleid']),
						));
						if ($fields = $db->fetchrow($answer)) {
							/* IMPORTANT */
							/* USE IT TO KNOW INFORMATION ABOUT CURRENT SELECTED MODULE */
							$_SESSION['dims']['moduletype']     = $fields['module_type'];
							$_SESSION['dims']['moduletypeid']   = $fields['id_module_type'];
							$_SESSION['dims']['modulelabel']    = $fields['label'];
						}
						//dims_redirect($scriptenv."?dims_moduleid=".$dims_moduleid);
					}
				}
			}

			// construction eventuelle de l'image attachee au module
			if (file_exists(DIMS_APP_PATH."www/".IMAGES_PATH."common/modules/".$mod['label']."/img/mod.gif")){
				$ext="/".IMAGES_PATH."common/modules/".$mod['label']."/img/mod.gif";
			}elseif(file_exists(DIMS_APP_PATH."www/common/modules/".$mod['label']."/img/mod.gif")){
				$ext="/common/modules/".$mod['label']."/img/mod.gif";
			}elseif (file_exists(DIMS_APP_PATH."www/".IMAGES_PATH."common/modules/".$mod['label']."/img/mod.png")){
				$ext="/".IMAGES_PATH."common/modules/".$mod['label']."/img/mod.png";
			}elseif(file_exists(DIMS_APP_PATH."www/common/modules/".$mod['label']."/img/mod.png")){
				$ext="/common/modules/".$mod['label']."/img/mod.png";
			}else{
				$ext="";
			}
			if (file_exists(DIMS_APP_PATH."www/".IMAGES_PATH."common/modules/".$mod['label']."/img/mod32.gif")){
				$ext32="/".IMAGES_PATH."common/modules/".$mod['label']."/img/mod32.gif";
			}elseif(file_exists(DIMS_APP_PATH."www/common/modules/".$mod['label']."/img/mod32.gif")){
				$ext32="/www/common/modules/".$mod['label']."/img/mod32.gif";
			}elseif (file_exists(DIMS_APP_PATH."www/".IMAGES_PATH."common/modules/".$mod['label']."/img/mod32.png")){
				$ext32="/".IMAGES_PATH."common/modules/".$mod['label']."/img/mod32.png";
			}elseif(file_exists(DIMS_APP_PATH."www/common/modules/".$mod['label']."/img/mod32.png")){
				$ext32="/common/modules/".$mod['label']."/img/mod32.png";
			}else{
				$ext32="";
			}

			$elem=array(
					'TITLE' => substr($mod['instancename'],0,24),
					'DESC' =>$mod['instancename'],
					'EXT' => $ext,
					'EXT32' => $ext32,
					'URL' => dims_urlencode($scriptenv."?dims_mainmenu=".$mod['contenttype']."&dims_moduleid=".$mod['instanceid']."&dims_desktop=block&dims_action=public"),
					'SELECTED' => ($idmod == $_SESSION['dims']['moduleid'] ) ? 'selected' : ''
			);

			if ($mod['contenttype']==$_SESSION['dims']['mainmenu']) {
				$tpl_modules[]=$elem;
			}

			if (!isset($modtype_tab[$mod['contenttype']])) $modtype_tab[$mod['contenttype']]=array();

			if (($mod['contenttype']=='content' && $mod['label']=='wce' && $_SESSION['dims']['currentworkspace']['web'] == 1) || $mod['label']!='wce') {
				if(!isset($modtype_tab[$mod['contenttype']][$mod['instanceid']])) $modtype_tab[$mod['contenttype']][]=$elem;
			}

			// check for wiki
			if (defined('_DISPLAY_WIKI') && _DISPLAY_WIKI && $mod['contenttype']=='content' && $mod['label']=='wce' && file_exists(DIMS_APP_PATH."modules/wce/wiki/")) {
				if (file_exists(DIMS_APP_PATH."www/".IMAGES_PATH."common/modules/".$mod['label']."/img/wiki16.png")){
					$ext="/".IMAGES_PATH."common/modules/".$mod['label']."/img/wiki16.png";
				}elseif(file_exists(DIMS_APP_PATH."www/common/modules/".$mod['label']."/img/wiki16.png")){
					$ext="/common/modules/".$mod['label']."/img/wiki16.png";
				}else{
					$ext="";
				}
				$elem=array(
					'TITLE' => substr($mod['instancename'],0,24)." / Wiki",
					'DESC' =>$mod['instancename']." / Wiki",
					'EXT' => $ext,
					'URL' => dims_urlencode($scriptenv."?dims_mainmenu=".$mod['contenttype']."&dims_moduleid=".$mod['instanceid']."&dims_desktop=block&dims_action=public&op=wiki"),
					'SELECTED' => ($idmod == $_SESSION['dims']['moduleid'] ) ? 'selected' : ''
				);

				if(!isset($modtype_tab[$mod['contenttype']][$mod['instanceid']])) {
					$modtype_tab[$mod['contenttype']][] = $elem;
				}
			}
		}
	}

	$view->assign('modules',$tpl_modules);
	$smarty->assign('modules',$tpl_modules);

	if (!isset($modtype_tab[dims_const::_DIMS_MENU_HOME])) {
		$modtype_tab[dims_const::_DIMS_MENU_HOME] = array();
	}

	$tpl_tab[] = array(
		'TITLE'     => $_DIMS['cste']['_DIMS_LABEL_HOME'],
		'IMG'       => $_SESSION['dims']['template_path']."/media/home32.png",
		'URL'       => dims_urlencode($scriptenv."?dims_mainmenu=".dims_const::_DIMS_MENU_HOME."&dims_desktop=block&dims_action=public&submenu=0&dims_moduleid=".dims_const::_DIMS_MODULE_SYSTEM."&init_desktop=1&mode=default"),
		'SELECTED'  => ($_SESSION['dims']['mainmenu'] == dims_const::_DIMS_MENU_HOME ) ? 'selected' : '',
		'MODULES'   => $modtype_tab[dims_const::_DIMS_MENU_HOME],
	);

	if ($currentworkspace['activeproject']) {
		$tpl_tab[] = array(
			'TITLE'     => $_DIMS['cste']['_LABEL_PROJECTS'],
			'IMG'       => $_SESSION['dims']['template_path']."/media/project32.png",
			'URL'       => dims_urlencode($scriptenv."?dims_mainmenu=".dims_const::_DIMS_MENU_PROJECTS."&dims_desktop=block&dims_action=public&dims_moduleid=".dims_const::_DIMS_MODULE_SYSTEM),
			'SELECTED'  => ($_SESSION['dims']['mainmenu'] == dims_const::_DIMS_MENU_PROJECTS) ? 'selected' : '',
		);
	}

	if ($currentworkspace['activeplanning'] && defined('_ACTIVE_DESKTOP_V2') && _ACTIVE_DESKTOP_V2 ) {
		require_once DIMS_APP_PATH."/modules/system/desktopV2/include/global.php";
		$tpl_tab[] = array(
			'TITLE'     => $_DIMS['cste']['_PLANNING'],
			'IMG'       => _DESKTOP_TPL_PATH."/gfx/common/calendar32.png",
			'URL'       => dims_urlencode($scriptenv."?dims_mainmenu=".dims_const::_DIMS_MENU_HOME."&dims_desktop=block&dims_action=public&dims_moduleid=".dims_const::_DIMS_MODULE_SYSTEM."&init_desktop=1&submenu="._DESKTOP_V2_DESKTOP."&mode=refreshplanning"),
			'SELECTED'  => ($_SESSION['dims']['mainmenu'] == dims_const::_DIMS_MENU_PLANNING) ? 'selected' : '');
	}

	if ($currentworkspace['activecontact']) {
		$tpl_tab[] = array(
			'TITLE'     => $_DIMS['cste']['_DIMS_LABEL_CONTACT'],
			'IMG'       => $_SESSION['dims']['template_path']."/media/contact32.png",
			'URL'       => dims_urlencode($scriptenv."?dims_mainmenu=".dims_const::_DIMS_MENU_CONTACT."&cat=0&dims_desktop=block&dims_action=public&init=1&dims_moduleid=".dims_const::_DIMS_MODULE_SYSTEM),
			'SELECTED'  => ($_SESSION['dims']['mainmenu'] == dims_const::_DIMS_MENU_CONTACT) ? 'selected' : '');
	}

	if ($currentworkspace['activenewsletter']) {
		$tpl_tab[] = array(
			'TITLE'     => $_DIMS['cste']['_DIMS_LABEL_NEWSLETTER'],
			'IMG'       => $_SESSION['dims']['template_path']."/media/news32.png",
			'URL'       => dims_urlencode($scriptenv."?dims_mainmenu=".dims_const::_DIMS_MENU_HOME."&dims_desktop=block&dims_action=public&submenu=1&mode=newsletters&news_op=1"),
			'SELECTED'  => ($_SESSION['dims']['mainmenu'] == dims_const::_DIMS_MENU_NEWSLETTER) ? 'selected' : '',
		);
	}
	$change_mainmenu=false;

	$typkown = array();
	// traitement des module de docs
	if (isset($modtype_tab[dims_const::_DIMS_MENU_MODULEDOC]) && !empty($modtype_tab[dims_const::_DIMS_MENU_MODULEDOC])){
		$tpl_tab[]=array(
			'TITLE' =>$_DIMS['cste']['_DOCS'],
			'URL' => dims_urlencode($scriptenv."?dims_mainmenu=".dims_const::_DIMS_MENU_MODULEDOC."&dims_desktop=block&dims_action=public"),
			'SELECTED' => ($_SESSION['dims']['mainmenu'] == dims_const::_DIMS_MENU_MODULEDOC) ? 'selected' : '',
			'MODULES'=>(count($modtype_tab[dims_const::_DIMS_MENU_MODULEDOC])>1)?$modtype_tab[dims_const::_DIMS_MENU_MODULEDOC]:array(),
			'IMG'=>"./common/modules/doc/img/mod32.png");
	}

	$typkown[dims_const::_DIMS_MENU_MODULEDOC] = dims_const::_DIMS_MENU_MODULEDOC;

	// traitement des modules de contenus
	if (isset($modtype_tab[dims_const::_DIMS_MENU_MODULECONTENT]) && !empty($modtype_tab[dims_const::_DIMS_MENU_MODULECONTENT])) {
		$tabarray=array();

		if (sizeof($modtype_tab[dims_const::_DIMS_MENU_MODULECONTENT]) >= 1) {
			$tabarray = $modtype_tab[dims_const::_DIMS_MENU_MODULECONTENT];
		}

		$tpl_tab[] = array(
			'TITLE'     => $_DIMS['cste']['_DIMS_LABEL_CONTENT'],
			'URL'       => dims_urlencode($scriptenv."?dims_mainmenu=".dims_const::_DIMS_MENU_MODULECONTENT."&dims_desktop=block&dims_action=public"),
			'SELECTED'  => ($_SESSION['dims']['mainmenu'] == dims_const::_DIMS_MENU_MODULECONTENT) ? 'selected' : '',
			'MODULES'   => $tabarray,
			'IMG'       => "./common/modules/wce/img/mod32.png"
		);

		$typkown[dims_const::_DIMS_MENU_MODULECONTENT] = dims_const::_DIMS_MENU_MODULECONTENT;
	}

	// traitement des modules de surveillance de contenus
	if (isset($modtype_tab[dims_const::_DIMS_MENU_MODULEWATCH]) && !empty($modtype_tab[dims_const::_DIMS_MENU_MODULEWATCH])) {
		$tpl_tab[] = array(
			'TITLE'     => $_DIMS['cste']['_DIMS_LABEL_WATCH'],
			'URL'       => dims_urlencode($scriptenv."?dims_mainmenu=".dims_const::_DIMS_MENU_MODULEWATCH."&dims_desktop=block&dims_action=public"),
			'SELECTED'  => ($_SESSION['dims']['mainmenu'] == dims_const::_DIMS_MENU_MODULEWATCH) ? 'selected' : '',
			'MODULES'   => array(),
			'IMG'       => "./common".$_SESSION['dims']['template_path']."/media/watch32.png"
		);
	}

	$typkown[dims_const::_DIMS_MENU_MODULEWATCH] = dims_const::_DIMS_MENU_MODULEWATCH;
	// traitement des autres modules differents

	// on check si parmis les workspaces backoffice l'un est en espace Feedback
	if (isset($currentworkspace['id_workspace_feedback']) && isset($array_workspace[$currentworkspace['id_workspace_feedback']])) {
		$tpl_tab[] = array(
			'TITLE'     => "Feedback",
			'URL'       => dims_urlencode($scriptenv."?dims_workspaceid=".$currentworkspace['id_workspace_feedback']."&dims_desktop=block&dims_mainmenu=0"),
			'SELECTED'  => ($_SESSION['dims']['mainmenu'] == "feedback") ? 'selected' : '',
			'MODULES'   => array(),
			'IMG'       => "./common/img/help.png"
		);
	}

	// boucle sur les types de contenus
	foreach ($modtype_tab as $mtype => $ctype) {
		if (!in_array($mtype, $typkown)) {
			// on ajoute le nouvel onglet
			if (!empty($ctype)) {
				if (sizeof($ctype)>1) $tabarray=$ctype;
				else $tabarray=array();
				if(sizeof($ctype)==1){
					$elem = current($ctype);
					$elem['IMG'] = $elem['EXT32'];
					$tpl_tab[]=$elem;
				}else{
					$tpl_tab[]=array(
						'TITLE' =>ucfirst($mtype),
						'URL' => dims_urlencode($scriptenv."?dims_mainmenu=".$mtype."&dims_desktop=block&dims_action=public"),
						'SELECTED' => ($_SESSION['dims']['mainmenu'] == $mtype) ? 'selected' : '',
						'MODULES'=>$tabarray,
						'IMG'=>"./common/modules/".$mtype."/img/mod32.png");
				}
			}
		}
	}

	if ($_SESSION['dims']['moduletype'] != '') {
		if ($_SESSION['dims']['desktop']!="portal") {
			if ($_SESSION['dims']['action'] == 'admin') {
				if (file_exists(DIMS_APP_PATH . "/modules/".$_SESSION['dims']['moduletype']."/admin.php")) {
					require_once(DIMS_APP_PATH . "/modules/".$_SESSION['dims']['moduletype']."/admin.php");
				}
			} else {
				if (file_exists(DIMS_APP_PATH . "/modules/".$_SESSION['dims']['moduletype']."/public.php")) {
					require_once(DIMS_APP_PATH . "/modules/".$_SESSION['dims']['moduletype']."/public.php");
				}
			}
		}
	}
}

// affichage du premier niveau de menus
$view->assign('tabs',$tpl_tab);
$smarty->assign('tabs',$tpl_tab);

/*
if ($_SESSION['dims']['action'] == 'admin' && isset($_SESSION['dims']['code_of_conduct']) && $_SESSION['dims']['code_of_conduct'] && $_SESSION['dims']['user_code_of_conduct']==0) {
	echo "<script type=\"text/javascript\">";
	echo "displayCodeOfConduct();";
	echo "</script>";
}*/

/*if (_DIMS_DEBUGMODE) {
	dims_debug::css();
	dims_debug::display();
}*/

if ($_SESSION['dims']['userid'] && defined('_CONSTANTIZER_TOOL') && _CONSTANTIZER_TOOL) {
	$_SESSION['dims']['constantizer'] = _CONSTANTIZER_TOOL;
}

if($_SESSION['dims']['MOBILE_VERSION']==0) {
	if (($_SESSION['dims']['userid'] > 0 && !defined('_DIMS_CHAT_ACTIVE')) || ( $_SESSION['dims']['userid'] && defined('_DIMS_CHAT_ACTIVE') && _DIMS_CHAT_ACTIVE)) {
		require_once(DIMS_APP_PATH . "/modules/system/desktop_display_chat.php");
	}
}
$main_content = ob_get_contents();
@ob_end_clean();

$conusers="";
$_SESSION['dims']['connectedusers']=1;
if ($_SESSION['dims']['connectedusers']<=1) $conusers=$_SESSION['dims']['connectedusers']." ".$_DIMS['cste']['_DIMS_CONNECTED_USER'];
else $conusers=$_SESSION['dims']['connectedusers']." ".$_DIMS['cste']['_DIMS_CONNECTED_USERS'];

if (!isset($dims_content)) $dims_content='';
$dims_stats=$dims->getStats($db,$dims_timer,$dims_content='');

$currentworkspace=$dims->getWorkspaces($_SESSION['dims']['workspaceid']);
$root_path=$dims->getRootPath();

if (empty($_SESSION['dims']['userid'])) $nom_skin = 'smoothness';
$select = 'SELECT `nom_skin` FROM `dims_user` NATURAL JOIN `dims_skin` WHERE `id` = :userid ;';

$answer = $db->query($select, array(':userid' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid'])));
if ($fields = $db->fetchrow($answer)) {
	$nom_skin = $fields['nom_skin'];
}
$WORKSPACE_TITLE = "";
if (isset($_SESSION['dims']['currentworkspace']['title'])) {
	$WORKSPACE_TITLE = $_SESSION['dims']['currentworkspace']['title'];
} elseif(isset($_SESSION['dims']['currentworkspace']['label'])) {
	$WORKSPACE_TITLE = $_SESSION['dims']['currentworkspace']['label'];
}

// securisation formulaire
require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
$token = new FormToken\TokenField;
$token->field("dims_login");
$token->field("dims_password");
$token->field("dims_email");
$token->field("rfid-auth");
$tokenHTML = $token->generate();
$smarty->assign('dims_form_token', $tokenHTML);

$token = new FormToken\TokenField;
$token->field("dims_email");
$tokenHTML = $token->generate();
$smarty->assign('dims_form_token_pass', $tokenHTML);

// Baptiste
// Inclusion des styles du backoffice
$manager = $view->getStylesManager();
$styles_modules = $manager->getStyles();
unset($manager->styles);
$manager->loadRessource('backoffice', $_SESSION['dims']['template_name']);
foreach($styles_modules as $include_me){
	$manager->styles[] = $include_me;
}
$styles = $manager->includeStyles();
$view->assign('styles',$styles);
$smarty->assign('styles',$styles);

// Inclusion des scripts du backoffice
$manager = $view->getScriptsManager();
$manager->loadRessource('backoffice', $_SESSION['dims']['template_name']);
$scripts = $manager->includeScripts();
$view->assign('scripts',$scripts);
$smarty->assign('scripts',$scripts);

$tpl_site = array(
	'TEMPLATE_PATH'                 => $root_path.'/'.str_replace(DIMS_APP_PATH . "./","/",$_SESSION['dims']['template_path']),
	'TEMPLATE_ROOT_PATH'            => $root_path.'/'.str_replace("./","/",$_SESSION['dims']['template_path']),
	'ROOT_PATH'                     => $root_path.'/common',
	'ENCODING'                      => _DIMS_ENCODING,
	// Baptiste
	'ASSETS_PATH'                   => ASSETS_PATH,
	'SCRIPTS_COMMON_PATH'           => SCRIPTS_COMMON_PATH,
	'IMAGES_COMMON_PATH'            => ASSETS_PATH.'images/common/',
	'TEMPLATE_IMG_PATH'             => ASSETS_PATH.'images/backoffice/'.$_SESSION['dims']['template_name'],
	'DIMS_NS_CSS'                   => '',
	'SCRIPT_ENV'                    => $scriptenv,
	'BACKGROUND'                    => (isset($currentworkspace['background']) && file_exists(realpath("./")."/data/workspaces/".$currentworkspace['background'])) ? $currentworkspace['background'] : "",
	'WORKSPACE_TITLE'               => $WORKSPACE_TITLE,
	'WORKSPACE_META_DESCRIPTION'    => (isset($_SESSION['dims']['currentworkspace']['meta_description'])) ? ($_SESSION['dims']['currentworkspace']['meta_description']) : "",
	'WORKSPACE_META_KEYWORDS'       => (isset($_SESSION['dims']['currentworkspace']['meta_keywords'])) ? ($_SESSION['dims']['currentworkspace']['meta_keywords']) : "",
	'WORKSPACE_META_AUTHOR'         => (isset($_SESSION['dims']['currentworkspace']['meta_author'])) ? ($_SESSION['dims']['currentworkspace']['meta_author']) : "",
	'WORKSPACE_META_COPYRIGHT'      => (isset($_SESSION['dims']['currentworkspace']['meta_copyright'])) ? ($_SESSION['dims']['currentworkspace']['meta_copyright']) : "",
	'WORKSPACE_META_ROBOTS'         => (isset($_SESSION['dims']['currentworkspace']['meta_robots'])) ? ($_SESSION['dims']['currentworkspace']['meta_robots']) : "",
	'SITE_CONNECTEDUSERS'           => (isset($_SESSION['dims']['connectedusers'])) ?  $_SESSION['dims']['connectedusers'] : 0,
	'PAGE_CONTENT'                  => $main_content,
	'ADDITIONAL_JAVASCRIPT'         => $additional_javascript,
	'ADDITIONAL_HEAD'               => '',
	'DIMS_ERROR'                    => (!empty($_GET['dims_errorcode']) && isset($dims_errormsg)) ? $dims_errormsg[dims_load_securvalue('dims_errorcode', dims_const::_DIMS_CHAR_INPUT, true, true, true)] : '',
	'DIMS_VERSION'                  => dims_const::_DIMS_VERSION,
	'DIMS_LABEL_CONNECTWORKSPACE'   => $_DIMS['cste']['_DIMS_LABEL_CONNECTWORKSPACE'],
	'DIMS_CLIPBOARD'                => dims_clipboard(),
	'DIMS_CONNECTEDUSERS'           => $conusers,
	'DIMS_PAGE_SIZE'                => sprintf("%.02f",$dims_stats['pagesize']/1024),
	'DIMS_EXEC_TIME'                => $dims_stats['total_exectime'],
	'DIMS_PHP_P100'                 => $dims_stats['php_ratiotime'],
	'DIMS_SQL_P100'                 => $dims_stats['sql_ratiotime'],
	'DIMS_NUMQUERIES'               => $dims_stats['numqueries'],
	'MAINMENU_SHOWPROFILE_URL'      => $activeprofil,
	'SHOW_BLOCKMENU'                => (!empty($_SESSION['dims']['switchdisplay']['block_modules'])) ? $_SESSION['dims']['switchdisplay']['block_modules'] : 'block',
	'USER_DECONNECT'                => dims_urlencode("$scriptenv?dims_logout=1"),
	'SEARCH_BTN'                    => $btn_search,
	'DIMS_OPTIONS'                  => $_DIMS['cste']['_DIMS_OPTIONS'],
	'CSS_FILE'                      => $nom_skin,
	'TWITTER'                       => (isset($_SESSION['dims']['currentworkspace']['twitter'])) ? ($_SESSION['dims']['currentworkspace']['twitter']) : "",
	'FACEBOOK'                      => (isset($_SESSION['dims']['currentworkspace']['facebook'])) ? ($_SESSION['dims']['currentworkspace']['facebook']) : ""
);

$smarty->assign('site',$tpl_site);
$view->assign('site',$tpl_site);

// Baptiste
// Petit hack : View avec DesktopV3, Smarty avec les autres ringards :D

// Small benchmark
// $i = 10;
// if($_SESSION['dims']['template_name'] == 'desktopV3'){

// 	$t1 = microtime();
// 	while($i > 0){

// 		$view->set_tpl_webpath('templates/backoffice/'.$_SESSION['dims']['template_name'].'/');
// 		$view->setLayout('index.tpl'); //déclaration du layout principal
// 		$view->compute();
// 		$t2 = microtime();
// 		$i--;
// 	}
// 	$t3 = $t2-$t1;
// 	die($t3);

// } else {

// 	$t1 = microtime();
// 	while($i > 0){

// 		$smarty->display('index.tpl');
// 		$t2 = microtime();
// 		$i--;
// 	}
// 	$t3 = $t2-$t1;
// 	die($t3);
// }

//if($_SESSION['dims']['template_name'] == 'desktopV3'){

if(file_exists(DIMS_APP_PATH.'templates/backoffice/'.$_SESSION['dims']['template_name'].'/index.tpl.php')) {
	$view->set_tpl_webpath('templates/backoffice/'.$_SESSION['dims']['template_name'].'/');
	$view->setLayout('index.tpl.php'); //déclaration du layout principal
	$view->compute();
}
else {
	$smarty->display("index.tpl");
}

	//$smarty->display("index.tpl");

// } else {

// 	$smarty->display('index.tpl.php');
// }

?>
