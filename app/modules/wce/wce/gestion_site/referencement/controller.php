<?
$action = dims_load_securvalue('action',dims_const::_DIMS_CHAR_INPUT,true,true);
switch($action){
	default:
	case module_wce::_GEST_REF_DEF:
		$articleid = dims_load_securvalue('articleid',dims_const::_DIMS_NUM_INPUT,true,true,false);
		if ($articleid != '' && $articleid > 0){
			$article = new wce_article();
			$article->open($articleid,$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);
			$article->display(module_wce::getTemplatePath("gestion_site/referencement/edit_ref.tpl.php"));
		}else
			dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PROPERTIES."&action=".module_wce::_PROPERTIES_DEF);
		break;
	case module_wce::_GEST_REF_SAVE:
		$articleid = dims_load_securvalue('articleid',dims_const::_DIMS_NUM_INPUT,true,true,false);
		$id_lang = dims_load_securvalue('lang',dims_const::_DIMS_NUM_INPUT,true,true,false);
		if ($articleid != '' && $articleid > 0){
			$article = new wce_article();
			$article->open($articleid,$id_lang);
			$article->setvalues($_POST,'art_');
			$article->save();
			dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_REF."&action=".module_wce::_GEST_REF_DEF."&headingid=".$article->fields['id_heading']."&articleid=".$article->fields['id']);
		}else
			dims_redirect(module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PROPERTIES."&action=".module_wce::_PROPERTIES_DEF);
		break;
}