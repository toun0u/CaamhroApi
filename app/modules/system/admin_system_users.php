<?php
require_once(DIMS_APP_PATH . '/modules/system/class_user.php');

if (isset($system_usertabid)) $_SESSION['system_usertabid'] = $system_usertabid;

$tabs[_SYSTEM_TAB_USERLIST]['title'] = $_DIMS['cste']['_DIMS_LIST'];
$tabs[_SYSTEM_TAB_USERLIST]['url'] = "$scriptenv?system_usertabid="._SYSTEM_TAB_USERLIST;
$tabs[_SYSTEM_TAB_USERLIST]['width'] = 150;

$tabs[_SYSTEM_TAB_USERADD]['title'] = $_DIMS['cste']['_DIMS_ADD'];
$tabs[_SYSTEM_TAB_USERADD]['url'] = "$scriptenv?system_usertabid="._SYSTEM_TAB_USERADD;
$tabs[_SYSTEM_TAB_USERADD]['width'] = 150;

echo $skin->create_tabs('',$tabs,$_SESSION['system_usertabid']);
echo $skin->open_simplebloc('','100%');

switch($_SESSION['system_usertabid']) {
	case _SYSTEM_TAB_USERLIST:

	$user = new user();

	switch($op) {
		case "save_user":
			$newuser = false;
			$user_id=dims_load_securvalue('user_id',dims_const::_DIMS_NUM_INPUT,true,true);
			$user_login=dims_load_securvalue('user_login',dims_const::_DIMS_CHAR_INPUT,true,true);
			$userx_password=dims_load_securvalue('userx_password',dims_const::_DIMS_CHAR_INPUT,true,true);
			$userx_passwordconfirm=dims_load_securvalue('userx_passwordconfirm',dims_const::_DIMS_CHAR_INPUT,true,true);

			if ($user_id>0) $user->open($user_id);
			else $newuser = true;

			$user->setvalues($_POST,'user_');
			// set new password if not blank
			$passwordok = true;
			if ($userx_password!='' && $userx_password == $userx_passwordconfirm) {
							$dims->getPasswordHash($userx_password,$user->fields['password'],$user->fields['salt']);
							//$user->fields['password'] = dims_getPasswordHash($userx_password);
						}elseif ($user->fields['password'] == '' || $userx_password != $userx_passwordconfirm) $passwordok = false;

			$user->save();
			if ($newuser) $user->attachtogroup(dims_const::_SYSTEM_SYSTEMADMIN);

			// modify profile/adminlevel for current group/user
			$group_user = new group_user();
			$group_user->open(dims_const::_SYSTEM_SYSTEMADMIN,$user->fields['id']);
			$group_user->setvalues($_POST,'usergroup_');
			$group_user->save();

			if ($passwordok) dims_redirect("$scriptenv");
			else dims_redirect("$scriptenv?op=modify_user&user_id=".$user->fields['id']."&error=password");
		break;

		case 'modify_user':
			$user_id=dims_load_securvalue('user_id',dims_const::_DIMS_NUM_INPUT,true,true);
			$groupid=dims_load_securvalue('groupid',dims_const::_DIMS_NUM_INPUT,true,true);

			$user->open($user_id);
			$group_user = new group_user();
			$group_user->open($groupid,$user_id);
			include(DIMS_APP_PATH . '/modules/system/admin_system_users_form.php');
		break;

		case "delete_user":
			$user_id=dims_load_securvalue('user_id',dims_const::_DIMS_NUM_INPUT,true,true);
			$user->open($user_id);
			$user->delete();
			dims_redirect("$scriptenv");
		break;

		default:
			include(DIMS_APP_PATH . '/modules/system/admin_system_users_list.php');
		break;

	}
	break;

	case _SYSTEM_TAB_USERADD:

	$user = new user();

	switch($op) {
		case "save_user":
			$user_id=dims_load_securvalue('user_id',dims_const::_DIMS_NUM_INPUT,true,true);
			$user_login=dims_load_securvalue('user_login',dims_const::_DIMS_CHAR_INPUT,true,true);
			$userx_password=dims_load_securvalue('userx_password',dims_const::_DIMS_CHAR_INPUT,true,true);
			$userx_passwordconfirm=dims_load_securvalue('userx_passwordconfirm',dims_const::_DIMS_CHAR_INPUT,true,true);
			$newuser = false;

			if (isset($user_id) && $user_id!='') $user->open($user_id);
			else $newuser = true;

			if (isset($user_id)) $user->open($user_id);
			$user->setvalues($_POST,'user_');
			// set new password if not blank
			$passwordok = true;
			if ($userx_password!='' && $userx_password == $userx_passwordconfirm) {
							$dims->getPasswordHash($userx_password,$user->fields['password'],$user->fields['salt']);
							//$user->fields['password'] = dims_getPasswordHash($userx_password);
						}elseif ($user->fields['password'] == '' || $userx_password != $userx_passwordconfirm) $passwordok = false;

			$user->save();
			if ($newuser) $user->attachtogroup(dims_const::_SYSTEM_SYSTEMADMIN);

			// modify profile/adminlevel for current group/user
			$group_user = new group_user();
			$group_user->open(dims_const::_SYSTEM_SYSTEMADMIN,$user->fields['id']);
			$group_user->setvalues($_POST,'usergroup_');
			$group_user->save();

			if ($passwordok) dims_redirect("$scriptenv?system_usertabid="._SYSTEM_TAB_USERLIST);
			else dims_redirect("$scriptenv?op=modify_user&user_id=".$user->fields['id']."&error=password");
		break;

		case 'modify_user':
			$user_id=dims_load_securvalue('user_id',dims_const::_DIMS_NUM_INPUT,true,true);
			$groupid=dims_load_securvalue('groupid',dims_const::_DIMS_NUM_INPUT,true,true);
			if ($user_id>0 && $groupid>0) {
				$user->open($user_id);
				$group_user = new group_user();
				$group_user->open($groupid,$user_id);
			}
			include(DIMS_APP_PATH . '/modules/system/admin_system_users_form.php');
		break;

		default:
			include(DIMS_APP_PATH . '/modules/system/admin_system_users_form.php');
		break;
	}
	break;
}


echo $skin->close_simplebloc();
?>
