<?php
require_once DIMS_APP_PATH . '/modules/system/class_role.php';

if (!isset($_SESSION['system_roletabid'])) $_SESSION['system_roletabid'] = _SYSTEM_TAB_ROLEMANAGEMENT;

$system_roletabid = dims_load_securvalue('system_roletabid', dims_const::_DIMS_CHAR_INPUT, true, true, false,$_SESSION['system_roletabid']);

$tabs[_SYSTEM_TAB_ROLEMANAGEMENT] = array ('title' => $_DIMS['cste']['_SYSTEM_LABELTAB_ROLEMANAGEMENT'], 'url' => "$scriptenv?system_roletabid="._SYSTEM_TAB_ROLEMANAGEMENT);
$tabs[_SYSTEM_TAB_ROLEASSIGNMENT] = array ('title' => $_DIMS['cste']['_SYSTEM_LABELTAB_ROLEASSIGNMENT'], 'url' => "$scriptenv?system_roletabid="._SYSTEM_TAB_ROLEASSIGNMENT);
$tabs[_SYSTEM_TAB_MULTIPLEROLEASSIGNMENT] = array ('title' => $_DIMS['cste']['_SYSTEM_LABELTAB_MULTIPLEROLEASSIGNMENT'], 'url' => "$scriptenv?system_roletabid="._SYSTEM_TAB_MULTIPLEROLEASSIGNMENT);

echo $skin->create_tabs('',$tabs,$_SESSION['system_roletabid']);
echo $skin->open_simplebloc('','100%');

switch($system_roletabid) {
	case _SYSTEM_TAB_ROLEMANAGEMENT:
	switch($op) {
		case 'save_role':
			$role = new role();
			$roleid=dims_load_securvalue('roleid',dims_const::_DIMS_NUM_INPUT,false,true,true);
			$id_module_type=dims_load_securvalue('id_module_type',dims_const::_DIMS_NUM_INPUT,false,true,true);
			if ($roleid>0) $role->open($roleid);

			$role->setvalues($_POST,'role_');

			if (empty($_POST['role_shared'])) $role->fields['shared'] = 0;

			if (isset($roleid) && $roleid!='') dims_create_user_action_log(_SYSTEM_ACTION_MODIFYROLE, "{$role->fields['label']} ({$role->fields['id']})");
			else dims_create_user_action_log(__SYSTEM_ACTION_CREATEROLE, "{$role->fields['label']} ({$role->fields['id']})");


			$id_action=dims_load_securvalue('id_action',dims_const::_DIMS_CHAR_INPUT,false,true,false);
			$role->save($id_action,$id_module_type);
			dims_redirect("$scriptenv?reloadsession");
		break;

		case 'delete_role':
			$role = new role();
			$roleid=dims_load_securvalue('roleid',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($roleid>0) {
				$role->open($roleid);
				dims_create_user_action_log(_SYSTEM_ACTION_DELETEROLE, "{$role->fields['label']} ({$role->fields['id']})");
				$role->delete();
			}
			dims_redirect("$scriptenv?reloadsession");
		break;

		default:
			include(DIMS_APP_PATH . '/modules/system/admin_index_roles_management.php');
		break;

	}
	break;

	case _SYSTEM_TAB_ROLEASSIGNMENT:
	switch($op) {
		case 'save_roles':
			$workspace_user_role_id_user=dims_load_securvalue('workspace_user_role_id_user',dims_const::_DIMS_NUM_INPUT,false,true,true);
			$workspace_group_role_id_group=dims_load_securvalue('workspace_group_role_id_group',dims_const::_DIMS_NUM_INPUT,false,true,true);
			$workspace_user_role_id_workspace=dims_load_securvalue('workspace_user_role_id_workspace',dims_const::_DIMS_NUM_INPUT,false,true,true);
			$workspace_group_role_id_workspace=dims_load_securvalue('workspace_group_role_id_workspace',dims_const::_DIMS_NUM_INPUT,false,true,true);
			$roles = dims_load_securvalue('roles', dims_const::_DIMS_NUM_INPUT, true, true, true);

			if (isset($workspace_user_role_id_user) && !empty($workspace_user_role_id_user)) {
				$workspace_user = new workspace_user();
				$workspace_user->open($workspace_user_role_id_workspace,$workspace_user_role_id_user);
				$workspace_user->saveroles($roles);
			}
			if (isset($workspace_group_role_id_group) && !empty($workspace_group_role_id_workspace)) {
				$workspace_group = new workspace_group();
				$workspace_group->open($workspace_group_role_id_workspace,$workspace_group_role_id_group);
				$workspace_group->saveroles($roles);
			}
			dims_redirect("$scriptenv?reloadsession");
		break;

		default:
			include(DIMS_APP_PATH . '/modules/system/admin_index_roles_assignment.php');
		break;
	}
	break;

	case _SYSTEM_TAB_MULTIPLEROLEASSIGNMENT:
	switch($op)
	{
		case 'save_roles':
			$workspace_user_role_id_workspace=dims_load_securvalue('workspace_user_role_id_workspace',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$users = $workspace	->getusers();
			foreach ($users as $id => $detail) {
				$workspace_user = new workspace_user();
				$workspace_user->open($workspace_user_role_id_workspace,$id);
				$workspace_user->saveroles($roles);
			}

			$groups = $workspace->getgroups();
			foreach ($orgs as $id => $detail) {
				$workspace_group = new workspace_group();
				$workspace_group->open($workspace_group_role_id_workspace,$id);
				$workspace_group->saveroles($roles);
			}
			dims_redirect("$scriptenv?reloadsession&system_roletabid=roleassignment");
		break;

		default:
			include(DIMS_APP_PATH . '/modules/system/admin_index_roles_multipleassignment.php');
		break;
	}
	break;

}


echo $skin->close_simplebloc();
?>
