<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$ch=DIMS_APP_PATH."modules/wce/wce_sitemap.tpl";

//echo $_SESSION['dims']['smarty_path'];;
if (!isset($_SESSION['dims']['smarty_path']) || $_SESSION['dims']['smarty_path']=='') {
	$_SESSION['dims']['smarty_path']=realpath('.')."/smarty";
}
$template_name=$_SESSION['dims']['front_template_name'];
$smartypath=$_SESSION['dims']['smarty_path'];

$smartyobject = new Smarty();
$smartyobject->cache_dir = $smartypath.'/cache';
$smartyobject->config_dir = $smartypath.'/configs';
$smartyobject->template_dir = DIMS_APP_PATH."modules/wce/";

//$template_forms = new Template("./common/templates/frontoffice$template_name");
if (file_exists($ch)) {
	if (!file_exists($smartypath.'/templates_c/'.$template_name)) mkdir ($smartypath."/templates_c/".$template_name."/");
	$smartyobject->compile_dir = $smartypath."/templates_c/".$template_name."/";
	// construction de la liste

	$result=wce_getSiteStructure(dims::getInstance()->getDb(),$obj['module_id']);
	$smartyobject->assign('sitemap_elements',$result);
	$smartyobject->display('wce_sitemap.tpl');
}
else echo "ERREUR : template wce_sitemap.tpl manquant !";
?>
