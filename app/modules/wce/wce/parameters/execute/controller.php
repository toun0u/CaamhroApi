<?php
$action = dims_load_securvalue('action',dims_const::_DIMS_CHAR_INPUT,true,true);

switch($action){
	default:
	case module_wce::_PARAM_EXEC_DEF:
		require_once module_wce::getTemplatePath("parameters/execute/accueil.tpl.php");
		break;
	case module_wce::_PARAM_EXEC_PUBLISH_ALL:
		$sel = "SELECT	*
				FROM	".wce_article::TABLE_NAME."
				WHERE	id_module = :id_module
				AND		id_workspace = :id_workspace
				GROUP BY id";
		$db = dims::getInstance()->getDb();
		$res = $db->query($sel,array(':id_workspace'=>array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT),
									':id_module'=>array('value'=>$_SESSION['dims']['moduleid'],'type'=>PDO::PARAM_INT)));
		while($r = $db->fetchrow($res)){
			set_time_limit(0);
			ini_set('memory_limit', '-1');
			$art = new wce_article();
			$art->openFromResultSet($r);
			$lstLang = $art->getListArticleLangVersion();
			if (count($lstLang) > 0) {
				foreach($lstLang as $lang) {
					$art->publish($lang->fields['id']);
					$art->valider($lang->fields['id']);
				}
			} else {
				$art->publish();
				$art->valider($art->fields['id_lang']);
			}
		}
		dims_redirect(module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_EXECUTE."&action=".module_wce::_PARAM_EXEC_DEF);
		break;
	case module_wce::_PARAM_EXEC_GENERATE_URL:
		$sel = "SELECT	*
				FROM	".wce_article::TABLE_NAME."
				WHERE	id_module = :id_module
				AND		id_workspace = :id_workspace
				AND		urlrewrite = ''";
		$db = dims::getInstance()->getDb();
		$res = $db->query($sel,array(':id_workspace'=>array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT),
									':id_module'=>array('value'=>$_SESSION['dims']['moduleid'],'type'=>PDO::PARAM_INT)));
		while($r = $db->fetchrow($res)){
			$art = new wce_article();
			$art->openFromResultSet($r);
			$art->fields['urlrewrite'] = generateValideUrl($art->fields['title']);
			$art->save();
		}
		dims_redirect(module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_EXECUTE."&action=".module_wce::_PARAM_EXEC_DEF);
		break;
	case module_wce::_PARAM_EXEC_STR_REPLACE:
		$search = dims_load_securvalue('search',dims_const::_DIMS_CHAR_INPUT,true,true,true);
		$replace = dims_load_securvalue('replace',dims_const::_DIMS_CHAR_INPUT,true,true,true);
		if ($search != ''){
			$sel = "SELECT	*
					FROM	".wce_article::TABLE_NAME."
					WHERE	id_module = :id_module
					AND		id_workspace = :id_workspace";
			$db = dims::getInstance()->getDb();
			$res = $db->query($sel,array(':id_workspace'=>array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT),
									':id_module'=>array('value'=>$_SESSION['dims']['moduleid'],'type'=>PDO::PARAM_INT)));
			while($r = $db->fetchrow($res)){
				$art = new wce_article();
				$art->openFromResultSet($r);
				$art->str_replace($search,$replace);
			}
		}
		dims_redirect(module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_EXECUTE."&action=".module_wce::_PARAM_EXEC_DEF);
		break;
}
?>
