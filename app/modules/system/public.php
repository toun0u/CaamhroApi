<?php
dims_init_module('system');


require_once(DIMS_APP_PATH . '/modules/system/include/global.php');
require_once(DIMS_APP_PATH . '/modules/system/include/business.php');
require_once(DIMS_APP_PATH . '/modules/system/include/metatype.php');

switch($_SESSION['dims']['mainmenu']) {
	case dims_const::_DIMS_MENU_HOME:
		if (defined('_ACTIVE_DESKTOP_V2') && _ACTIVE_DESKTOP_V2 && defined('_DESKTOP_TPL_PATH')) {
			require_once(DIMS_APP_PATH . '/modules/system/desktopV2/public.php');
		}
		else{
			// recherche du module par defaut
			$autoconnect_modules = $dims->getAutoConnectModules($_SESSION['dims']['workspaceid']);

			foreach($autoconnect_modules as $id => $struct) {
				$autoconnect_module_id  = $struct['instanceid'];
				$moduleType             = $struct['label'];

				if ($_SESSION['dims']['connected']) {
					if ($dims->getEnabledBackoffice()) {
						dims_redirect("/admin.php?dims_mainmenu=".$moduleType."&dims_moduleid=".$autoconnect_module_id."&dims_desktop=block&dims_action=public");
					}
				}
			}
		}
	break;

	case dims_const::_DIMS_MENU_PLANNING:
		$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, true);
		if ($action=="view_admin_events") {
			$_SESSION['dims']['mainmenu']=dims_const::_DIMS_MENU_HOME;
			require_once(DIMS_APP_PATH . '/modules/system/class_block_user.php');

			$submenu=dims_load_securvalue('submenu',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['submainmenu'],$_SESSION['dims']['submainmenu']);
			switch($submenu) {
				case dims_const::_DIMS_SUBMENU_DIFFUSION_LIST:
					require_once(DIMS_APP_PATH . '/modules/system/desktop_mailinglist_admin.php');
					break;
				default:
					require_once(DIMS_APP_PATH . '/modules/system/desktop.php');
					break;
			}
		}
		else {
			require_once(DIMS_APP_PATH . '/modules/system/public_planning.php');
		}
	break;

	case dims_const::_DIMS_MENU_PROJECTS:
		//echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_PROJECT'],'100%');
		require_once(DIMS_APP_PATH . '/modules/system/include/business.php');
		require_once DIMS_APP_PATH . '/modules/system/public_projects.php';
		//echo $skin->close_simplebloc();
	break;
	case dims_const::_DIMS_MENU_NEWSLETTER:

		include(DIMS_APP_PATH . '/modules/system/lfb/lfb_public_newsletter.php');
		break;
	case dims_const::_DIMS_MENU_CONTACT:
		$reset=1;
		require_once(DIMS_APP_PATH . '/modules/system/include/business.php');
		include(DIMS_APP_PATH . '/modules/system/crm_public_contacts.php');
	break;

	case dims_const::_DIMS_MENU_TICKETS:
		require_once 'public_tickets.php';
	break;

	case dims_const::_DIMS_MENU_ANNOTATIONS:
		require_once 'public_annotations.php';
	break;

	case dims_const::_DIMS_MENU_PROFILE:
		require_once(DIMS_APP_PATH . '/include/class_param.php');

		$param_module = new param($db->connection_id);

		if (!isset($op)) $op = '';
		if ($op=='') $op='user';

		switch($op) {
			case 'reset':
				$delete = "DELETE FROM dims_param_user WHERE id_module = ? AND id_user = ?";
				$res=$db->query($delete, array($idmodule, $_SESSION['session_userid']));
				include(DIMS_APP_PATH . '/include/load_param.php');
				dims_redirect("$scriptenv?module=$module");
			break;

			case 'save':
				$param_module->open($idmodule,null,$_SESSION['dims']['userid'],1);
				$param_module->setvalues($_POST);
				$param_module->save();

				// reload all module params of current user in session
				include(DIMS_APP_PATH . '/include/load_param.php');
				dims_redirect($scriptenv."?idmodule=$idmodule");
			break;

			case 'param':
				include(DIMS_APP_PATH . '/modules/system/public_module_param.php');
			break;

			case 'actions':
				include(DIMS_APP_PATH . '/modules/system/public_actions.php');
			break;

			case 'user':
				$user = new user();
				$user->open($_SESSION['dims']['userid']);
				include(DIMS_APP_PATH . '/modules/system/public_user.php');
			break;

			case 'save_user':
				$user = new user();
				$user->open($_SESSION['dims']['userid']);
				$olcolor=$user->fields['color'];

				$user->setvalues($_POST,'user_');
				if ($user->fields['color']!=$olcolor) {
					$user->createPicto();
					$_SESSION['dims']['user']['color'] = $user->fields['color'];
				}
				if (isset($_FILES) && !empty($_FILES)) $user->createBackground();
				$passwordok = true;

				$userx_password=dims_load_securvalue('userx_password',dims_const::_DIMS_CHAR_INPUT,true,true);
				$userx_passwordconfirm=dims_load_securvalue('userx_passwordconfirm',dims_const::_DIMS_CHAR_INPUT,true,true);

				if (preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $user->fields['email'])){
					// L'email est valide
				} else {
					// L'email n'est pas valide
					dims_redirect("$scriptenv?op=user&error=email");
					break;
				}

				if (!isset($_POST['user_ticketsbyemail'])) $user->fields['ticketsbyemail'] = 0;
				if ($userx_password!='' && $userx_password == $userx_passwordconfirm) {

					$dims->getPasswordHash($userx_password,$user->fields['password'],$user->fields['salt']);
					//$user->fields['password'] = dims-> dims_getPasswordHash($userx_password);
				}
				elseif ($user->fields['password'] == '' || $userx_password != $userx_passwordconfirm) $passwordok = false;
				$user->save();

				// modify profile/adminlevel for current group/user
				$workspaceid=$_SESSION['dims']['workspaceid'];
				$workspace_user = new workspace_user();
				$workspace_user->open($workspaceid,$user->fields['id']);

				$workspace_user->setvalues($_POST,'userworkspace_');
				if (!isset($_POST['userworkspace_activesearch'])) $workspace_user->fields['activesearch'] = 0;
				else $workspace_user->fields['activesearch'] = 1;
				if (!isset($_POST['userworkspace_activeticket'])) $workspace_user->fields['activeticket'] = 0;
				else $workspace_user->fields['activeticket'] = 1;
				if (!isset($_POST['userworkspace_activeprofil'])) $workspace_user->fields['activeprofil'] = 0;
				else $workspace_user->fields['activeprofil'] = 1;
				if (!isset($_POST['userworkspace_activeannot'])) $workspace_user->fields['activeannot'] = 0;
				else $workspace_user->fields['activeannot'] = 1;
				// save current config
				$workspace_user->save();

				if ($passwordok) dims_redirect("$scriptenv?op=user");
				else dims_redirect("$scriptenv?op=user&error=password");
			break;

			case 'save_user_interests':
				$user = new user();
				$user->open($_SESSION['dims']['userid']);
				$user->setInterests(dims_load_securvalue('user_interests', dims_const::_DIMS_NUM_INPUT, true, true, true));
				dims_redirect($scriptenv);
				break;
			case 'save_user_works':
				$user = new user();
				$user->open($_SESSION['dims']['userid']);
				$user->setWorks(dims_load_securvalue('user_works', dims_const::_DIMS_NUM_INPUT, true, true, true));
				dims_redirect($scriptenv);
				break;
		}
	break;

	case dims_const::_DIMS_MENU_ABOUT:
		switch($op) {
			default:
				echo $skin->open_simplebloc("DIMS ".dims_const::_DIMS_VERSION,'100%');
				?>
				<TABLE CELLPADDING="2" CELLSPACING="1">
				<TR>
					<TD>
					<? echo $_DIMS['cste']['_SYSTEM_EXPLAIN_ABOUT']; ?>
					</TD>
				</TR>
				<?
				if (file_exists('./whatsnew.txt'))
				{
					?>
					<TR>
						<TD>
						<br>
						<b>Changelog : </b>
						<?
						$handle = fopen('./whatsnew.txt','r');
						$contents = '';
						while (!feof($handle)) {
							$contents .= fread($handle, 8192);
						}
						echo nl2br($contents);
						?>
						</TD>
					</TR>
					<?
				}
				?>
				</TABLE>
				<?
				echo $skin->close_simplebloc();
			break;
		}
		break ;
}
?>
