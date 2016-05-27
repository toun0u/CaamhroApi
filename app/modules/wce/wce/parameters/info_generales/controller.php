<?php
$action = dims_load_securvalue('action',dims_const::_DIMS_CHAR_INPUT,true,true);

switch($action){
	default:
	case module_wce::_PARAM_INFOS_DEF:
		$work = new workspace();
		$work->open($_SESSION['dims']['workspaceid']);
		$work->display(module_wce::getTemplatePath("parameters/info_generales/referencements.tpl.php"));

		$db = dims::getInstance()->db;
		$sel = "SELECT		d.*
				FROM		dims_domain d
				INNER JOIN	dims_workspace_domain dwd
				ON			d.id = dwd.id_domain
				AND			dwd.access >= 1
				AND			dwd.id_workspace = :id_workspace";
		$res = $db->query($sel,array(':id_workspace'=>array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT)));
		$domaines = array();
		while($r = $db->fetchrow($res)){
			$domaines[] = $r;
		}
		require_once module_wce::getTemplatePath("parameters/info_generales/page_accueil.tpl.php");
		require_once module_wce::getTemplatePath("parameters/info_generales/page_accueil_connexion.tpl.php");
		break;
	case module_wce::_PARAM_INFOS_EDIT_REF:
		$work = new workspace();
		$work->open($_SESSION['dims']['workspaceid']);
		$work->display(module_wce::getTemplatePath("parameters/info_generales/edit_referencements.tpl.php"));
		break;
	case module_wce::_PARAM_INFOS_SAVE_REF:
		$work = new workspace();
		$work->open($_SESSION['dims']['workspaceid']);
		$work->setvalues($_POST,'work_');
		$work->save();
		if (isset($_FILES['favicon']) && isset($_FILES['favicon']['error']) && $_FILES['favicon']['error'] == UPLOAD_ERR_OK){
			require_once DIMS_APP_PATH."include/class_input_validator.php";

			$valid = new \InVal\FileValidator('favicon');
			$valid->rule(new \InVal\Rule\Image(true));

			if ($valid->validate()) {
				$sid = session_id();
				$dest = DIMS_TMP_PATH . '/' .$sid;
				if (!file_exists($dest))
					dims_makedir($dest);
				$dest .= "/".$_FILES['favicon']['name'];
				move_uploaded_file($_FILES['favicon']['tmp_name'], $dest);
				$work->addFrontFavicon($dest);
			}
		}
		dims_redirect(module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_INFOS."&action=".module_wce::_PARAM_INFOS_DEF);
		break;
	case module_wce::_PARAM_INFOS_EDIT_ACCUEIL:
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		if ($id != '' && $id > 0){
			require_once DIMS_APP_PATH."modules/system/class_domain.php";
			$dom = new domain();
			$dom->open($id);
			$dom->display(module_wce::getTemplatePath("parameters/info_generales/edit_page_accueil.tpl.php"));
		}else
			dims_redirect(module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_INFOS."&action=".module_wce::_PARAM_INFOS_DEF);
		break;
	case module_wce::_PARAM_INFOS_SAVE_ACCUEIL:
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		if ($id != '' && $id > 0){
			require_once DIMS_APP_PATH."modules/system/class_domain.php";
			$dom = new domain();
			$dom->open($id);
			$articleid_home=dims_load_securvalue('wce_article_id_article_link_HOME',dims_const::_DIMS_NUM_INPUT,false,true,false);
			$dom->setDefaultHomePage($articleid_home);
			$dom->save();
		}
		dims_redirect(module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_INFOS."&action=".module_wce::_PARAM_INFOS_DEF);
		break;
	case module_wce::_PARAM_INFOS_EDIT_ACCUEIL2:
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		if ($id != '' && $id > 0){
			require_once DIMS_APP_PATH."modules/system/class_domain.php";
			$dom = new domain();
			$dom->open($id);
			$dom->display(module_wce::getTemplatePath("parameters/info_generales/edit_page_accueil_connexion.tpl.php"));
		}else
			dims_redirect(module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_INFOS."&action=".module_wce::_PARAM_INFOS_DEF);
		break;
	case module_wce::_PARAM_INFOS_SAVE_ACCUEIL2:
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		if ($id != '' && $id > 0){
			require_once DIMS_APP_PATH."modules/system/class_domain.php";
			$dom = new domain();
			$dom->open($id);
			$articleid_private=dims_load_securvalue('wce_article_id_article_link_PRIVATE',dims_const::_DIMS_NUM_INPUT,false,true,false);
			$dom->setPostConnexionPage($articleid_private);
			$dom->save();
		}
		dims_redirect(module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_INFOS."&action=".module_wce::_PARAM_INFOS_DEF);
		break;
}
?>
