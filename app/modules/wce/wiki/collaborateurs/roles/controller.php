<?php
$action = dims_load_securvalue('action',dims_const::_DIMS_CHAR_INPUT,true,true);
switch($action){
	default:
		require_once module_wiki::getTemplatePath('/collaborateurs/roles/list_roles.tpl.php');
		break;
	case module_wiki::_ACTION_EDIT_ROLES:
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
		$role = new role();
		if ($id != '' && $id > 0)
			$role->open($id);
		else
			$role->init_description();
		$role->display(module_wiki::getTemplatePath('/collaborateurs/roles/edit_role.tpl.php'));
		break;
	case module_wiki::_ACTION_SAVE_ROLES:
		if (dims_isactionallowed(module_wiki::_ACTION_ADMIN_ROLES)){
			$db = dims::getInstance()->getDb();
			$id = dims_load_securvalue('id_role',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$role = new role();
			$role->init_description();
			if ($id != '' && $id > 0)
				$role->open($id);
			else{
				$role->fields['id_module'] = $_SESSION['dims']['moduleid'];
				$role->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
			}
			$role->setvalues($_POST,'role_');
			$role->save();

			$del = "DELETE FROM	dims_role_action
					WHERE		id_module_type = :id_module_type
					AND			id_role = :id_role";
			$db->query($del,array(':id_module_type'=>array('value'=>$_SESSION['dims']['moduletypeid'],'type'=>PDO::PARAM_INT),
									':id_role'=>array('value'=>$role->fields['id'],'type'=>PDO::PARAM_INT)));
			if (!empty($_POST['actions'])){
				require_once DIMS_APP_PATH."modules/system/class_role_action.php";
				$actions = dims_load_securvalue('actions', dims_const::_DIMS_NUM_INPUT, true, true, true);
				foreach($actions as $action){
					$act = new role_action();
					if (!$act->open($role->fields['id'],$action,$_SESSION['dims']['moduletypeid'])){
						$act->fields['id_role'] = $role->fields['id'];
						$act->fields['id_action'] = $action;
						$act->fields['id_module_type'] = $_SESSION['dims']['moduletypeid'];
						$act->save();
					}
				}
			}
		}
		dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_COLLAB."&subsub=".module_wiki::_SUB_SUB_COLLAB_LIST_R));
		break;
}
?>
