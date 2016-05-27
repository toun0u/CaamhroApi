<?php
require_once DIMS_APP_PATH . '/include/class_block.php';

if (!is_dir($_SESSION['dims']['template_path'])) $_SESSION['dims']['template_path']=DIMS_APP_PATH . "./common/templates/backoffice/dims";
require_once DIMS_APP_PATH."{$_SESSION['dims']['template_path']}/class_skin.php";

$skin = new skin();
$btn_search="";

//$template_body = new Template($_SESSION['dims']['template_path']);
$template_name=$_SESSION['dims']['template_name'];
$template_path=$_SESSION['dims']['template_path'];
$smarty->template_dir = $template_path;

if (!file_exists($smartypath.'/templates_c/'.$template_name)) mkdir ($smartypath."/templates_c/".$template_name."/", 0777, true);
$smarty->compile_dir = $smartypath."/templates_c/".$template_name."/";


/*
 * Récupération du tableau des mobules actifs pour le workspace
*/
$array_modules=$dims->getModules($_SESSION['dims']['workspaceid']);


/*
 * Récupération des  informations du workspace
*/
$currentworkspace=$dims->getWorkspaces($_SESSION['dims']['workspaceid']);

ob_start();


$content_mobile ='';
if (file_exists(realpath('.')."/modules/mobile/index.php")) {

	require_once realpath('.').'/modules/mobile/index.php';
}

$main_content = ob_get_contents();
@ob_end_clean();
$root_path=$dims->getRootPath();

$WORKSPACE_TITLE = "";
if (isset($_SESSION['dims']['currentworkspace']['title']))
	$WORKSPACE_TITLE = $_SESSION['dims']['currentworkspace']['title'];
elseif(isset($_SESSION['dims']['currentworkspace']['label']))
	$WORKSPACE_TITLE = $_SESSION['dims']['currentworkspace']['label'];

$tpl_site=array(
		'TEMPLATE_PATH'					=> $root_path.str_replace("./","/",$_SESSION['dims']['template_path']),
		'TEMPLATE_ROOT_PATH'							=> $root_path.str_replace("./","/",$_SESSION['dims']['template_path']),
		'ROOT_PATH'					=> $root_path,
		'ENCODING'					=>	_DIMS_ENCODING,
		'WORKSPACE_TITLE'				=> $WORKSPACE_TITLE,
		'PAGE_CONTENT'					=> $main_content,
		'DIMS_ERROR'					=> (!empty($_GET['dims_errorcode']) && isset($dims_errormsg)) ? $dims_errormsg[dims_load_securvalue('dims_errorcode', dims_const::_DIMS_CHAR_INPUT, true, true, true)] : '',
		'DIMS_VERSION'					=> dims_const::_DIMS_VERSION,
		'DIMS_LABEL_CONNECTWORKSPACE'					=> $_DIMS['cste']['_DIMS_LABEL_CONNECTWORKSPACE']
);

$smarty->assign('site',$tpl_site);

$smarty->display('mobile.tpl');
?>
