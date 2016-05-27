<?
// USERS
if (isset($_GET['user_id']))
	$_SESSION['dims']['tickets']['users_selected'][dims_load_securvalue('user_id', dims_const::_DIMS_NUM_INPUT, true, true, true)] = dims_load_securvalue('user_id', dims_const::_DIMS_NUM_INPUT, true, true, true);
if (isset($_GET['remove_user_id']))
	unset($_SESSION['dims']['tickets']['users_selected'][dims_load_securvalue('remove_user_id', dims_const::_DIMS_NUM_INPUT, true, true, true)]);
// WORKSPACES
if (isset($_GET['workspace_id']))
	$_SESSION['dims']['tickets']['workspaces_selected'][dims_load_securvalue('workspace_id', dims_const::_DIMS_NUM_INPUT, true, true, true)] = dims_load_securvalue('workspace_id', dims_const::_DIMS_NUM_INPUT, true, true, true);
if (isset($_GET['remove_workspace_id']))
	unset($_SESSION['dims']['tickets']['workspaces_selected'][dims_load_securvalue('remove_workspace_id', dims_const::_DIMS_NUM_INPUT, true, true, true)]);
// GROUPS
if (isset($_GET['group_id']))
	$_SESSION['dims']['tickets']['groups_selected'][dims_load_securvalue('group_id', dims_const::_DIMS_NUM_INPUT, true, true, true)] = dims_load_securvalue('group_id', dims_const::_DIMS_NUM_INPUT, true, true, true);
if (isset($_GET['remove_group_id']))
	unset($_SESSION['dims']['tickets']['groups_selected'][dims_load_securvalue('remove_group_id', dims_const::_DIMS_NUM_INPUT, true, true, true)]);

if (isset($_SESSION['dims']['tickets']['workspaces_selected'])) {

	foreach ($_SESSION['dims']['tickets']['workspaces_selected'] as $workspace_id) {
		require_once(DIMS_APP_PATH . '/modules/system/class_workspace.php');

		$workspace = new workspace();
		$workspace->open($workspace_id);

		$color = (!isset($color) || $color == $skin->values['bgline2']) ? $skin->values['bgline1'] : $skin->values['bgline2'];
?>
		<a class="system_tickets_delete_workspace" href="javascript:void(0);" onclick="dims_xmlhttprequest_todiv('admin.php','dims_op=tickets_select_user&remove_workspace_id=<? echo $workspace->fields['id']; ?>','','div_ticket_users_selected');">
			<img src="./common/img/del.png"/>
			<span><? echo "{$workspace->fields['label']} "; ?></span>
			<img src="<? echo $_SESSION['dims']['template_path']; ?>/img/system/ico_workgroup.png">
		</a><br/>
<?php
	}
	echo "</p>";
}

if (isset($_SESSION['dims']['tickets']['groups_selected'])) {
	echo "<p class=\"dims_va\" style=\"padding:2px;\">";
	foreach ($_SESSION['dims']['tickets']['groups_selected'] as $group_id) {
		require_once(DIMS_APP_PATH . '/modules/system/class_group.php');

		$group = new group();
		$group->open($group_id);

		$color = (!isset($color) || $color == $skin->values['bgline2']) ? $skin->values['bgline1'] : $skin->values['bgline2'];
?>
		<a class="system_tickets_delete_group" href="javascript:void(0);" onclick="dims_xmlhttprequest_todiv('admin.php','dims_op=tickets_select_user&remove_group_id=<? echo $group->fields['id']; ?>','','div_ticket_users_selected');">
			<img src="./common/img/del.png"/>
			<span><? echo "{$group->fields['label']} "; ?></span>
			<img src="<? echo $_SESSION['dims']['template_path']; ?>/img/system/ico_group.png">
		</a><br/>
<?
	}
	echo "</p>";
}

if (isset($_SESSION['dims']['tickets']['users_selected'])) {
	echo "<p class=\"dims_va\" style=\"padding:2px;\">";
	foreach ($_SESSION['dims']['tickets']['users_selected'] as $user_id) {
		require_once(DIMS_APP_PATH . '/modules/system/class_user.php');

		$user = new user();
		$user->open($user_id);

		$color = (!isset($color) || $color == $skin->values['bgline2']) ? $skin->values['bgline1'] : $skin->values['bgline2'];
?>
		<a class="system_tickets_delete_user" href="javascript:void(0);" onclick="dims_xmlhttprequest_todiv('admin.php','dims_op=tickets_select_user&remove_user_id=<? echo $user->fields['id']; ?>','','div_ticket_users_selected');">
			<img src="./common/img/del.png"/>
			<span><? echo "{$user->fields['firstname']} {$user->fields['lastname']} ({$user->fields['login']})"; ?></span>
		</a><br/>
<?
	}
	echo "</p>";
}
die();
