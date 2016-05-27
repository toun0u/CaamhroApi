<?php
include_once './modules/system/class_user.php';
include_once './modules/system/class_user_type.php';
include_once './modules/system/class_rule.php';

include_once './include/functions/mail.php';

$system_usertabid = dims_load_securvalue('system_usertabid', dims_const::_DIMS_CHAR_INPUT, true, true, false, $_SESSION['system_usertabid']);

$tabs[_CATALOGUE_TAB_USERLIST]['title'] = _CATALOGUE_LABELTAB_USERLIST;
$tabs[_CATALOGUE_TAB_USERLIST]['url'] = $dims->getScriptEnv()."?op=administration&system_usertabid="._CATALOGUE_TAB_USERLIST;
$tabs[_CATALOGUE_TAB_USERLIST]['width'] = 80;

$tabs[_CATALOGUE_TAB_USERADD]['title'] = _CATALOGUE_LABELTAB_USERADD;
$tabs[_CATALOGUE_TAB_USERADD]['url'] = $dims->getScriptEnv()."?op=administration&system_usertabid="._CATALOGUE_TAB_USERADD;
$tabs[_CATALOGUE_TAB_USERADD]['width'] = 80;

echo $skin->create_tabs('', $tabs, $_SESSION['system_usertabid']);
echo $skin->open_simplebloc('','100%');


// Chargement des niveaux utilisateurs
$user_levels = array();
if ($oCatalogue->getParams('is_user_with_valid')) {
	$user_levels[dims_const::_DIMS_ID_LEVEL_USER] = $oCatalogue->getParams('user_with_valid');
}
if ($oCatalogue->getParams('is_user_without_valid')) {
	$user_levels[cata_const::_DIMS_ID_LEVEL_USERSUP] = $oCatalogue->getParams('user_without_valid');
}
if ($oCatalogue->getParams('is_service_manager')) {
	$user_levels[cata_const::_DIMS_ID_LEVEL_SERVICERESP] = $oCatalogue->getParams('service_manager');
}
if ($oCatalogue->getParams('is_purchasing_manager')) {
	$user_levels[cata_const::_DIMS_ID_LEVEL_PURCHASERESP] = $oCatalogue->getParams('purchasing_manager');
}
if ($oCatalogue->getParams('is_account_admin')) {
	$user_levels[dims_const::_DIMS_ID_LEVEL_GROUPMANAGER] = $oCatalogue->getParams('account_admin');
}

switch ($_SESSION['system_usertabid']) {
    case _CATALOGUE_TAB_USERLIST:
		$user = new user();

		switch ($action) {
		    case 'save_user':
				$user_id =  dims_load_securvalue('user_id', dims_const::_DIMS_NUM_INPUT, true, true);
		    	if ($switchtype) {
		    		// save form values
		    		$_SESSION['module_system'] = $_POST;
		    		dims_redirect($dims->getScriptEnv()."?op=administration&action=modify_user&user_id=$user_id&groupid=$groupid&groupinterid=". $_SESSION['catalogue']['root_group']);
		    	}

    			$newuser = false;

    			if (isset($user_id) && $user_id!='') {
    				$user->open($user_id);
    			}
    			else $newuser = true;

				$userx_password = dims_load_securvalue('userx_password', dims_const::_DIMS_CHAR_INPUT, false, true, true);
				$userx_passwordconfirm = dims_load_securvalue('userx_passwordconfirm', dims_const::_DIMS_CHAR_INPUT, false, true, true);

				// If identical logins are allowed
				// check if couple login / password is not already used
				if ($oCatalogue->getParams('system_same_login')) {
					$db->query("SELECT id FROM dims_user WHERE login = '$user_login' && password = '". md5($userx_password) ."' AND id <> $user_id");
					if($db->numrows()) dims_redirect($dims->getScriptEnv()."?op=administration&action=modify_user&user_id=$user_id&error=passrejected&groupid=$groupid&groupinterid=". $_SESSION['catalogue']['root_group']);
				}

				$user->setvalues($_POST, 'user_');

    			// set new password if not blank
    			$passwordok = true;
    			if ($userx_password!='' && $userx_password == $userx_passwordconfirm) $user->fields['password'] = md5($userx_password);
    			elseif ($user->fields['password'] == '' || $userx_password != $userx_passwordconfirm) $passwordok = false;

    			$user->save();

    			// attach user to group if new user
    			if ($newuser) $user->attachtogroup($groupid);

    			// modify profile/adminlevel for current group/user
    			$group_user = new group_user();
    			$group_user->open($groupid,$user->fields['id']);
    			$group_user->setvalues($_POST, 'usergroup_');
    			$group_user->save();

				if ($passwordok) {
					if (trim($user->fields['email']) != '') {
						$from[0]['name'] = '';
						$from[0]['address'] = $oCatalogue->getParams('notif_send_mail');

						$to[0]['name'] = "{$user->fields['firstname']} {$user->fields['lastname']}";
						$to[0]['address'] = $user->fields['email'];

						$mail_html = str_replace('<LOGIN>', $user->fields['login'], _MAIL_NEW_PASSWORD);
						$mail_html = str_replace('<PASSWD>', $userx_password, $mail_html);
						$mail_html = str_replace('<NOM>', $user->fields['lastname'], $mail_html);
						$mail_html = str_replace('<PRENOM>', $user->fields['firstname'], $mail_html);
						$mail_html = str_replace('<EMAIL>', $user->fields['email'], $mail_html);
						$mail_html = str_replace('<TELEPHONE>', $user->fields['phone'], $mail_html);
						$mail_html = str_replace('<FAX>', $user->fields['fax'], $mail_html);
						$mail_html = str_replace('<ADRESSE>', $user->fields['address'], $mail_html);

						dims_send_mail($from, $to, "Mise à jour de vos informations", $mail_html);
					}
					dims_redirect($dims->getScriptEnv()."?op=administration&reloadsession&groupid=$groupid&groupinterid=". $_SESSION['catalogue']['root_group']);
				}
				else {
					dims_redirect($dims->getScriptEnv()."?op=administration&action=modify_user&user_id=".$user->fields['id']."&error=password&groupid=$groupid&groupinterid=". $_SESSION['catalogue']['root_group']);
				}
			    break;
		    case 'modify_user':
				$user_id = dims_load_securvalue('user_id', dims_const::_DIMS_NUM_INPUT, true, false);
				$user->open($user_id);
				$group_user = new group_user();
				$group_user->open($groupid, $user_id);
		    	include DIMS_APP_PATH.'/modules/catalogue/display_admin_index_users_form.php';
			    break;
		    case 'delete_user':
				$user_id = dims_load_securvalue('user_id', dims_const::_DIMS_NUM_INPUT, true, false);
				$user->open($user_id);
				$user->delete();
				//cyril : il faudrait ptetre pensé à lui supprimer son budget pour le réatribuer au groupe
				include_once DIMS_APP_PATH.'/modules/catalogue/include/class_user_budget.php';
				$usbud = new user_budget();
				$usbud->getUserBudgetByUserId($user_id);
				if(!$usbud->new) $usbud->delete();
				dims_redirect($dims->getScriptEnv()."?op=administration&groupid=$groupid&groupinterid=". $_SESSION['catalogue']['root_group']);
				//cyril : la question que je me pose c'est si un utilisateur est rattaché à plusieurs groupes dans lesquels il a un budget différent, est-ce queça prend le bon

			    break;
		    case 'detach_user':
				$user_id = dims_load_securvalue('user_id', dims_const::_DIMS_NUM_INPUT, true, false);
				$user->open($user_id);
				$user->detachfromgroup($groupid);
				dims_redirect($dims->getScriptEnv()."?op=administration&reloadsession&groupid=$groupid&groupinterid=". $_SESSION['catalogue']['root_group']);
			    break;
			case 'save_user_budget':
				$groupid = dims_load_securvalue('groupid', dims_const::_DIMS_NUM_INPUT, true, true);
				$groupinterid = dims_load_securvalue('groupinterid', dims_const::_DIMS_NUM_INPUT, true, true);
				$budget_id_user = dims_load_securvalue('budget_id_user', dims_const::_DIMS_NUM_INPUT, true, true);
				$budget_en_cours = dims_load_securvalue('budget_en_cours', dims_const::_DIMS_NUM_INPUT, true, true);
				$id_budget = dims_load_securvalue('id_budget', dims_const::_DIMS_NUM_INPUT, true, true);
				$limite_budget = dims_load_securvalue('limite_budget', dims_const::_DIMS_NUM_INPUT, true, true);

				if (!isset($limite_budget)) $limite_budget = null;

				$user->open($budget_id_user);
				$user->fields['limite_budget'] = $limite_budget;
				$user->save();

				if ($limite_budget == 1) {
					include_once DIMS_APP_PATH.'/modules/catalogue/include/class_user_budget.php';
					$budget = new user_budget();
					if (!empty($id_budget)) $budget->open($id_budget);
					$budget->setvalues($_POST, 'budget_');
					$budget->fields['id_user'] = $user->fields['id'];

					$minimumMoney = getminimummoney_user($user->fields['id']);
					if ($budget_valeur >= $minimumMoney) {
						$budget->save();
					}
					else {
						$budget->fields['valeur'] = $minimumMoney;
						$budget->save();
						dims_redirect($dims->getScriptEnv()."?op=administration&action=modify_user&user_id={$user->fields['id']}&err=2&reloadsession&groupid=$groupid&groupinterid=". $_SESSION['catalogue']['root_group']);
					}
				}
 				dims_redirect($dims->getScriptEnv()."?op=administration&action=modify_user&user_id={$user->fields['id']}&groupid=$groupid&groupinterid=". $_SESSION['catalogue']['root_group']);
				break;
			case 'close_user_budget':
				$id_user = dims_load_securvalue('id_user', dims_const::_DIMS_NUM_INPUT, true, true);
				$id_budget = dims_load_securvalue('id_budget', dims_const::_DIMS_NUM_INPUT, true, true);
				//die("AAAAAHHHHH : ".$id_user);
				$user = new user();
				$user->open($id_user);
				$user->fields['limite_budget'] = null;
				$user->save();

				if ($id_budget) $db->query("UPDATE dims_mod_vpc_user_budget SET en_cours = 0 WHERE id = $id_budget");

				dims_redirect($dims->getScriptEnv()."?op=administration&action=modify_user&user_id=$id_user&groupid=$groupid&groupinterid=". $_SESSION['catalogue']['root_group']);
				break;
			default:
				include('./modules/catalogue/display_admin_index_users_list.php');
				break;
		}
	    break;
	case _CATALOGUE_TAB_USERADD:
		$user = new user();

		switch ($action) {
		    case 'save_user':
				$switchtype				= dims_load_securvalue('switchtype', dims_const::_DIMS_NUM_INPUT, false, true);
				$user_id				= dims_load_securvalue('user_id', dims_const::_DIMS_NUM_INPUT, false, true);
				$groupid				= dims_load_securvalue('groupid', dims_const::_DIMS_NUM_INPUT, false, true);
				$groupinterid			= dims_load_securvalue('groupinterid', dims_const::_DIMS_NUM_INPUT, false, true);
				$user_lastname			= dims_load_securvalue('user_lastname', dims_const::_DIMS_CHAR_INPUT, false, true);
				$user_firstname			= dims_load_securvalue('user_firstname', dims_const::_DIMS_CHAR_INPUT, false, true);
				$user_login				= dims_load_securvalue('user_login', dims_const::_DIMS_CHAR_INPUT, false, true);
				$userx_password			= dims_load_securvalue('userx_password', dims_const::_DIMS_CHAR_INPUT, false, true);
				$userx_passwordconfirm	= dims_load_securvalue('userx_passwordconfirm', dims_const::_DIMS_CHAR_INPUT, false, true);
				$user_date_expire		= dims_load_securvalue('user_date_expire', dims_const::_DIMS_CHAR_INPUT, false, true);
				$user_email				= dims_load_securvalue('user_email', dims_const::_DIMS_CHAR_INPUT, false, true);
				$user_phone				= dims_load_securvalue('user_phone', dims_const::_DIMS_CHAR_INPUT, false, true);
				$user_fax				= dims_load_securvalue('user_fax', dims_const::_DIMS_CHAR_INPUT, false, true);
				$user_address			= dims_load_securvalue('user_address', dims_const::_DIMS_CHAR_INPUT, false, true);
				$user_comments			= dims_load_securvalue('user_comments', dims_const::_DIMS_CHAR_INPUT, false, true);
				$usergroup_adminlevel	= dims_load_securvalue('usergroup_adminlevel', dims_const::_DIMS_NUM_INPUT, false, true);

		    	if ($switchtype) {
		    		// save form values
		    		$_SESSION['module_system'] = $_POST;
		    		dims_redirect($dims->getScriptEnv()."?op=administration&groupid=$groupid&groupinterid=". $_SESSION['catalogue']['root_group']);
		    	}

    			$newuser = false;
    			if (!empty($user_id)) {
    				$user->open($user_id);
    			}
    			else {
					$newuser = true;
				}

    			// On vérifie que le login n'existe pas deja
    			if ($newuser) {
					$db->query("SELECT id FROM dims_user WHERE login = '$user_login'");
					if ($db->numrows()) dims_redirect($dims->getScriptEnv()."?op=administration&error=login&groupinterid=". $_SESSION['catalogue']['root_group']);
    			}

    			$user->setvalues($_POST, 'user_');

    			// set new password if not blank
    			$passwordok = true;
    			if ($userx_password != '' && $userx_password == $userx_passwordconfirm) $user->fields['password'] = md5($userx_password);
    			elseif ($user->fields['password'] == '' || $userx_password != $userx_passwordconfirm) $passwordok = false;

    			$user->save();

    			// attach user to group if new user
    			if ($newuser) $user->attachtogroup($groupid);

    			// modify profile/adminlevel for current group/user
    			$group_user = new group_user();
    			$group_user->open($groupid, $user->fields['id']);
    			$group_user->setvalues($_POST, 'usergroup_');
    			$group_user->save();

				if ($passwordok) {
					if (trim($user->fields['email']) != '') {
						$from[0]['name'] = '';
						$from[0]['address'] = $oCatalogue->getParams('notif_send_mail');

						$to[0]['name'] = "{$user->fields['firstname']} {$user->fields['lastname']}";
						$to[0]['address'] = $user->fields['email'];

						$mail_html = str_replace('<LOGIN>', $user->fields['login'], _MAIL_NEW_PASSWORD);
						$mail_html = str_replace('<PASSWD>', $userx_password, $mail_html);
						$mail_html = str_replace('<NOM>', $user->fields['lastname'], $mail_html);
						$mail_html = str_replace('<PRENOM>', $user->fields['firstname'], $mail_html);
						$mail_html = str_replace('<EMAIL>', $user->fields['email'], $mail_html);
						$mail_html = str_replace('<TELEPHONE>', $user->fields['phone'], $mail_html);
						$mail_html = str_replace('<FAX>', $user->fields['fax'], $mail_html);
						$mail_html = str_replace('<ADRESSE>', $user->fields['address'], $mail_html);

						dims_send_mail($from, $to, "Création de votre compte", $mail_html);
					}
					dims_redirect($dims->getScriptEnv()."?op=administration&system_usertabid="._CATALOGUE_TAB_USERLIST."&groupid=$groupid&groupinterid=". $_SESSION['catalogue']['root_group']);
				}
				else {
					dims_redirect($dims->getScriptEnv()."?op=administration&action=modify_user&user_id=".$user->fields['id']."&error=password&groupid=$groupid&groupinterid=". $_SESSION['catalogue']['root_group']);
				}
			    break;
		    case 'modify_user':
				$user->open($user_id);
				$group_user = new group_user();
				$group_user->open($groupid, $user_id);
		    	include DIMS_APP_PATH.'/modules/catalogue/display_admin_index_users_form.php';
			    break;
		    default:
				$group_user = new group_user();
				$group_user->init_description();
				$group_user->fields['id_user'] = -1;
				$user->init_description();
				$user->fields['id'] = -1;
		    	include DIMS_APP_PATH.'/modules/catalogue/display_admin_index_users_form.php';
			    break;
		}
	    break;
}

echo $skin->close_simplebloc();
