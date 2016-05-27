<?php
$action = dims_load_securvalue('action',dims_const::_DIMS_CHAR_INPUT,true,true);
switch($action){
	default:
		require_once module_wiki::getTemplatePath('/collaborateurs/services/list_services.tpl.php');
		break;
	case module_wiki::_ACTION_EDIT_SERVICE:
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		$role = new group();
		if ($id != '' && $id > 0)
			$role->open($id);
		else
			$role->init_description();
		$role->display(module_wiki::getTemplatePath('/collaborateurs/services/edit_service.tpl.php'));
		break;
	case module_wiki::_ACTION_SAVE_SERVICE:
		if (dims_isactionallowed(module_wiki::_ACTION_ADMIN_SERVICES)){
			$id = dims_load_securvalue('id_service',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$gr = new group();
			$gr->init_description();
			if ($id != '' && $id > 0)
				$gr->open($id);
			else{
				$grRoot = module_wiki::getGrRoot();
				if (!is_null($grRoot)) {
					$gr->fields['id_group'] = $grRoot->fields['id'];
					$gr->fields['parents'] = $grRoot->fields['parents'].";".$grRoot->fields['id'];
				} else {
					dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_COLLAB."&subsub=".module_wiki::_SUB_SUB_COLLAB_LIST_S));
				}
			}
			$gr->setvalues($_POST,'service_');
			$gr->save();

			$del = "DELETE FROM	dims_workspace_group_role
					WHERE		id_workspace = :id_workspace
					AND			id_group = :id_group";
			$db->query($del,array(':id_workspace'=>array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT),
									':id_group'=>array('value'=>$gr->fields['id'],'type'=>PDO::PARAM_INT)));
			if (!empty($_POST['roles'])){
				require_once DIMS_APP_PATH."modules/system/class_dims_workspace_group_role.php";
				$roles = dims_load_securvalue('roles', dims_const::_DIMS_NUM_INPUT, true, true, true);
				foreach($roles as $role) {
					$lk = new workspace_group_role();
					if (!$lk->open($_SESSION['dims']['workspaceid'],$gr->fields['id'],$role)){
						$lk->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
						$lk->fields['id_group'] = $gr->fields['id'];
						$lk->fields['id_role'] = $role;
						$lk->save();
					}
				}
			}
		}
		dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_COLLAB."&subsub=".module_wiki::_SUB_SUB_COLLAB_LIST_S));
		break;
}
?>
