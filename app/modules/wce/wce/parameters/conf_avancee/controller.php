<?php
$action = dims_load_securvalue('action',dims_const::_DIMS_CHAR_INPUT,true,true);

switch($action){
	default:
	case module_wce::_PARAM_INFOS_DEF:
		require_once module_wce::getTemplatePath("parameters/conf_avancee/lst_domains.tpl.php");
		require_once module_wce::getTemplatePath("parameters/conf_avancee/lst_templates.tpl.php");
		require_once module_wce::getTemplatePath("parameters/conf_avancee/lst_droits.tpl.php");
		break;
// --- Bloc domaines
	case module_wce::_PARAM_CONF_EDIT_DOMAIN:
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		require_once DIMS_APP_PATH."modules/system/class_domain.php";
		$dom = new domain();
		if ($id != "" && $id > 0)
			$dom->open($id);
		else
			$dom->init_description();
		$dom->display(module_wce::getTemplatePath("parameters/conf_avancee/edit_domain.tpl.php"));
		break;
	case module_wce::_PARAM_CONF_DEL_DOMAIN:
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		if ($id != "" && $id > 0){
			require_once DIMS_APP_PATH."modules/system/class_domain.php";
			$dom = new domain();
			$dom->open($id);
			$dom->delete();
		}
		dims_redirect(module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_CONF."&action=".module_wce::_PARAM_INFOS_DEF);
		break;
	case module_wce::_PARAM_CONF_SAVE_DOMAIN:
		require_once DIMS_APP_PATH."modules/system/class_domain.php";
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		$dom = new domain();
		if ($id != "" && $id > 0)
			$dom->open($id);
		else
			$dom->init_description();
		$dom->fields['ssl'] = 0;
		$dom->fields['mobile'] = 0;
		$dom->setvalues($_POST,'dom_');
		$dom->save();
		dims_redirect(module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_CONF."&action=".module_wce::_PARAM_INFOS_DEF);
		break;
// --- Bloc templates
	case module_wce::_PARAM_CONF_EDIT_TEMPL:
		require_once module_wce::getTemplatePath("parameters/conf_avancee/edit_template.tpl.php");
		break;
	case module_wce::_PARAM_CONF_SAVE_TEMPL:
		$arraytpl=array();
		$tplexists=array();
		$db = dims::getInstance()->db;

		// collecte des nouveaux coches
		if (isset($_POST['seltpl'])) {
			$seltpl = dims_load_securvalue('seltpl', dims_const::_DIMS_CHAR_INPUT, true, true, true);
			foreach ($seltpl as $tpl) {
				$arraytpl[]=$tpl;
			}
		}

		if (count($arraytpl)) {
			// construction des existants
			$res=$db->query("SELECT 	template
							FROM 		dims_workspace_template
							WHERE		id_workspace=:id_workspace",
							array(':id_workspace'=>array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT)));

			if ($db->numrows($res)>0) {
				while ($f=$db->fetchrow($res)) {
					$tplexists[$f['template']]=$f['template'];
				}
			}

			// on supprime les templates d�coches
			$params = array(':id_workspace'=>array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT));
			$res=$db->query("DELETE FROM 	dims_workspace_template
							WHERE 			id_workspace= :id_workspace
							AND 			template NOT IN (".$db->getParamsFromArray($arraytpl,'tpl',$params).")",
							$params);
		}
		else $db->query("DELETE FROM 	dims_workspace_template
						WHERE			id_workspace=:id_workspace",
						array(':id_workspace'=>array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT)));

		// on ajoute le tpl courant
		foreach ($arraytpl as $tpl) {
			if (!isset($tplexists[$tpl]))
				$res=$db->query("INSERT INTO 	dims_workspace_template
								SET 			id_workspace=:id_workspace, template=:tpl",
								array(':id_workspace'=>array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT),
									':tpl'=>array('value'=>$tpl,'type'=>PDO::PARAM_STR)));
		}
		dims_redirect(module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_CONF."&action=".module_wce::_PARAM_INFOS_DEF);
		break;
	case module_wce::_PARAM_CONF_DEL_TEMPL:
		$name = dims_load_securvalue('name',dims_const::_DIMS_CHAR_INPUT,true,true,true);
		if (!empty($name)){
			$db = dims::getInstance()->db;
			$sql = "DELETE FROM	dims_workspace_template
					WHERE		id_workspace = :id_workspace
					AND			template LIKE :template";
			$db->query($sql,array(':id_workspace'=>array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT),
									':template'=>array('value'=>$tpl,'type'=>PDO::PARAM_STR)));
		}
		dims_redirect(module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_CONF."&action=".module_wce::_PARAM_INFOS_DEF);
		break;
	case module_wce::_PARAM_CONF_TEMPL_DEFAULT:
		$name = dims_load_securvalue('name',dims_const::_DIMS_CHAR_INPUT,true,true,true);
		if (!empty($name)){
			$db = dims::getInstance()->db;
			$sql = "UPDATE	dims_workspace_template
					SET 	is_default = 0
					WHERE	id_workspace = :id_workspace";
			$db->query($sql,array(':id_workspace'=>array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT)));
			$sql = "UPDATE	dims_workspace_template
					SET 	is_default = 1
					WHERE	id_workspace = :id_workspace
					AND		template LIKE :template";
			$db->query($sql,array(':id_workspace'=>array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT),
									':template'=>array('value'=>$name,'type'=>PDO::PARAM_STR)));
		}
		dims_redirect(module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_CONF."&action=".module_wce::_PARAM_INFOS_DEF);
		break;
	case module_wce::_PARAM_CONF_TEMPL_WIKI:
		if(defined('_DISPLAY_WIKI') && _DISPLAY_WIKI){
			$name = dims_load_securvalue('name',dims_const::_DIMS_CHAR_INPUT,true,true,true);
			if (!empty($name)){
				require_once(DIMS_APP_PATH."modules/wce/wiki/include/class_module_wiki.php");
				$rootWiki = module_wiki::getRootHeading();
				$db = dims::getInstance()->db;
				$sel = "SELECT 	*
						FROM 	".wce_heading::TABLE_NAME."
						WHERE 	id = :id";
				$res = $db->query($sel,array(':id'=>array('value'=>$rootWiki->fields['id'],'type'=>PDO::PARAM_INT)));
				while($r = $db->fetchrow($res)){
					$head = new wce_heading();
					$head->openFromResultSet($r);
					$head->fields['template'] = $name;
					$head->save();
				}
			}
		}
		dims_redirect(module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_CONF."&action=".module_wce::_PARAM_INFOS_DEF);
		break;
// --- Bloc restrictions
	case module_wce::_PARAM_CONF_EDIT_RESTR:
		require_once module_wce::getTemplatePath("parameters/conf_avancee/edit_droit.tpl.php");
		break;
	case module_wce::_PARAM_CONF_DEL_RESTR:

		dims_redirect(module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_CONF."&action=".module_wce::_PARAM_INFOS_DEF);
		break;
	case module_wce::_PARAM_CONF_SAVE_RESTR:

		break;
}
?>
