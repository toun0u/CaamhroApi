<?php
require_once(DIMS_APP_PATH . '/modules/system/class_user.php');
require_once(DIMS_APP_PATH . '/modules/system/class_user_type.php');
require_once(DIMS_APP_PATH . '/modules/system/class_rule.php');
require_once(DIMS_APP_PATH . '/modules/system/class_workspace_group.php');

if (isset($_GET['system_usertabid'])) $_SESSION['system_usertabid'] = dims_load_securvalue('system_usertabid', dims_const::_DIMS_CHAR_INPUT, true, true, true);
if (isset($_POST['system_usertabid'])) $_SESSION['system_usertabid'] = dims_load_securvalue('system_usertabid', dims_const::_DIMS_CHAR_INPUT, true, true, true);

if (!isset($_SESSION['system_usertabid'])) $_SESSION['system_usertabid'] = '';

$tabs[_SYSTEM_TAB_USERLIST] = array('title' => $_DIMS['cste']['_DIMS_LIST'], 'url' => "$scriptenv?system_usertabid="._SYSTEM_TAB_USERLIST);
if ($_SESSION['system_level'] == dims_const::_SYSTEM_GROUPS)
{
	$tabs[_SYSTEM_TAB_USERADD] = array('title' => $_DIMS['cste']['_DIMS_ADD'], 'url' => "$scriptenv?system_usertabid="._SYSTEM_TAB_USERADD);
}

if ($_SESSION['dims']['adminlevel'] >= dims_const::_DIMS_ID_LEVEL_GROUPADMIN) $tabs[_SYSTEM_TAB_USERATTACH] = array('title' => $_DIMS['cste']['_DIMS_LABEL_ATTACH'], 'url' => "$scriptenv?system_usertabid="._SYSTEM_TAB_USERATTACH);

if ($_SESSION['system_level'] == dims_const::_SYSTEM_WORKSPACES)
{
	$tabs[_SYSTEM_TAB_GROUPLIST] = array('title' => $_DIMS['cste']['_DIMS_LABEL_GROUP_LIST'], 'url' => "$scriptenv?system_usertabid="._SYSTEM_TAB_GROUPLIST);
	if ($_SESSION['dims']['adminlevel'] >= dims_const::_DIMS_ID_LEVEL_GROUPADMIN) $tabs[_SYSTEM_TAB_GROUPATTACH] = array('title' => $_DIMS['cste']['_SYSTEM_LABELTAB_GROUPATTACH'], 'url' => "$scriptenv?system_usertabid="._SYSTEM_TAB_GROUPATTACH);
}

if ($_SESSION['system_level'] == dims_const::_SYSTEM_GROUPS) $tabs[_SYSTEM_TAB_USERIMPORT] = array('title' => $_DIMS['cste']['_SYSTEM_LABELTAB_USERIMPORT'], 'url' => "$scriptenv?system_usertabid="._SYSTEM_TAB_USERIMPORT);

echo $skin->create_tabs('',$tabs,$_SESSION['system_usertabid']);
echo $skin->open_simplebloc('','width:100%;');

switch($_SESSION['system_usertabid']) {
	case _SYSTEM_TAB_GROUPLIST:
		switch($op) {
			case 'modify_group':
				$org_id=dims_load_securvalue('org_id',dims_const::_DIMS_NUM_INPUT,true,false);
				$workspace_group = new workspace_group();
				$workspace_group->open($workspaceid,$org_id);

				include(DIMS_APP_PATH . '/modules/system/admin_index_group_form.php');
				break;

			case 'save_group':
				// modify profile/adminlevel for current group/user
				$group_id=dims_load_securvalue('group_id',dims_const::_DIMS_NUM_INPUT,false,true);
				$workspace_group = new workspace_group();
				$workspace_group->open($workspaceid,$group_id);
				$workspace_group->setvalues($_POST,'workspacegroup_');

				if (!isset($_POST['workspacegroup_activesearch'])) $workspace_group->fields['activesearch'] = 0;
				else $workspace_group->fields['activesearch'] = 1;
				if (!isset($_POST['workspacegroup_activeticket'])) $workspace_group->fields['activeticket'] = 0;
				else $workspace_group->fields['activeticket'] = 1;
				if (!isset($_POST['workspacegroup_activeprofil'])) $workspace_group->fields['activeprofil'] = 0;
				else $workspace_group->fields['activeprofil'] = 1;
				if (!isset($_POST['workspacegroup_activeannot'])) $workspace_group->fields['activeannot'] = 0;
				else $workspace_group->fields['activeannot'] = 1;
				if (!isset($_POST['workspacegroup_activecontact'])) $workspace_group->fields['activecontact'] = 0;
				else $workspace_group->fields['activecontact'] = 1;
				if (!isset($_POST['workspacegroup_activeproject'])) $workspace_group->fields['activeproject'] = 0;
				else $workspace_group->fields['activeproject'] = 1;
				if (!isset($_POST['workspacegroup_activeplanning'])) $workspace_group->fields['activeplanning'] = 0;
				else $workspace_group->fields['activeplanning'] = 1;
								if (!isset($_POST['workspacegroup_activenewsletter'])) $workspace_group->fields['activenewsletter'] = 0;
				else $workspace_group->fields['activenewsletter'] = 1;

				if (!isset($_POST['workspacegroup_activeevent'])) {
					$workspace_group->fields['activeevent'] = 0;
					$workspace_group->fields['activeeventemail'] = 0;
				}
				else {
					$workspace_group->fields['activeevent'] = 1;
					if (!isset($_POST['workspacegroup_activeeventemail'])) $workspace_group->fields['activeeventemail'] = 0;
					else $workspace_group->fields['activeeventemail'] = 1;
				}

								if (!isset($_POST['workspacegroup_activeeventstep'])) $workspace_group->fields['activeeventstep'] = 0;
				else $workspace_group->fields['activeeventstep'] = 1;

								if (!isset($_POST['workspacegroup_activeswitchuser'])) $workspace_group->fields['activeswitchuser'] = 0;
								else $workspace_group->fields['activeswitchuser'] = 1;

				// save current config
				$workspace_group->save();
				dims_redirect("$scriptenv?reloadsession");
				break;

			case 'detach_group':
				$org_id=dims_load_securvalue('org_id',dims_const::_DIMS_NUM_INPUT,true,false);

				$workspace_group = new workspace_group();
				$workspace_group->open($workspaceid,$org_id);
				$workspace_group->delete();

				dims_redirect("$scriptenv?reloadsession");
				break;

			default:
				include(DIMS_APP_PATH . '/modules/system/admin_index_users_grouplist.php');
				break;
		}
		break;

	case _SYSTEM_TAB_GROUPATTACH:
		switch($op) {
			case 'attach_group':
			case dims_const::_SYSTEM_GROUPS :
				$orgid=dims_load_securvalue('orgid',dims_const::_DIMS_NUM_INPUT,true,false);
				$org = new group();
				$org->open($orgid);
				$org->attachtogroup($workspaceid);

				// update
				$dims->updateActionIntercom(dims_const::_SYSTEM_OBJECT_WORKSPACE,$workspaceid);

				dims_create_user_action_log(_SYSTEM_ACTION_ATTACHGROUP, "{$org->fields['label']} (id:{$org->fields['id']}) => {$workspace->fields['label']} (id:$workspaceid)");
				dims_redirect("$scriptenv?reloadsession");
				break;

			default:
				include(DIMS_APP_PATH . '/modules/system/admin_index_users_attachgroup.php');
				break;
		}
		break;

	case _SYSTEM_TAB_USERATTACH:
		switch($op) {
			case 'attach_user':
				switch ($_SESSION['system_level']) {
					case dims_const::_SYSTEM_GROUPS :
						$userid=dims_load_securvalue('userid',dims_const::_DIMS_NUM_INPUT,true,false);

						$user = new user();
						$user->open($userid);
						$user->attachtogroup($groupid);
						dims_create_user_action_log(_SYSTEM_ACTION_ATTACHUSER, "{$user->fields['login']} - {$user->fields['lastname']} {$user->fields['firstname']} (id:{$user->fields['id']}) => {$group->fields['label']} (id:$groupid)");
						dims_redirect("$scriptenv?reloadsession");
						break;

					case dims_const::_SYSTEM_WORKSPACES :
						$userid=dims_load_securvalue('userid',dims_const::_DIMS_NUM_INPUT,true,false);
						$user = new user();
						$user->open($userid);
						$user->attachtoworkspace($workspaceid);
						dims_create_user_action_log(_SYSTEM_ACTION_ATTACHUSER, "{$user->fields['login']} - {$user->fields['lastname']} {$user->fields['firstname']} (id:{$user->fields['id']}) => {$workspace->fields['label']} (id:$workspaceid)");
						dims_redirect("$scriptenv?reloadsession");
						break;
				}
				break;

			default:
				include(DIMS_APP_PATH . '/modules/system/admin_index_users_attachlist.php');
				break;
		}
		break;

	case _SYSTEM_TAB_USERMOVE:
		switch($op) {
			case 'move_user':
				$userid=dims_load_securvalue('userid',dims_const::_DIMS_NUM_INPUT,true,false);

				$user = new user();
				$user->open($userid);
				$user->movetogroup($groupid);
				dims_create_user_action_log(_SYSTEM_ACTION_MOVEUSER, "{$user->fields['login']} - {$user->fields['lastname']} {$user->fields['firstname']} (id:{$user->fields['id']}) => {$group->fields['label']} (id:$groupid)");
				dims_redirect("$scriptenv?reloadsession");
				break;

			default:
				include(DIMS_APP_PATH . '/modules/system/admin_index_users_movelist.php');
				break;
		}
		break;

	case _SYSTEM_TAB_USERLIST:
		$user = new user();

		switch($op) {
			case "search_contact":
				@ob_end_clean();
				ob_start();
					$user_lastname	=dims_load_securvalue('user_lname',dims_const::_DIMS_CHAR_INPUT,true,false);
					$user_firstname	=dims_load_securvalue('user_fname',dims_const::_DIMS_CHAR_INPUT,true,false);
					$tab_corresp	= array();

					//Recherche d'un contact similaire
					$sql = 'SELECT
								ct.id as id_contact,
								ct.lastname,
								ct.firstname
							FROM
								dims_mod_business_contact ct';

					$ress = $db->query($sql);

					if($db->numrows($ress) > 0) {
						$nom	= strtoupper($user_lastname);
						$prenom = strtoupper($user_firstname);

						while($rslt = $db->fetchrow($ress)) {
							$lev_nom = 0;
							$lev_pre = 0;

							$coef_nom = 0;
							$coef_pre = 0;

							$coef_tot = 0;

							$lev_nom = levenshtein($nom, strtoupper($rslt['lastname']));
							$coef_nom = $lev_nom - (ceil(strlen($nom)/4));

							$lev_pre = levenshtein($prenom, strtoupper($rslt['firstname']));
							$coef_pre = $lev_pre - (ceil(strlen($prenom)/4));

							$coef_tot = $coef_nom + $coef_pre;

							if($coef_tot < 2) {
								$tab_corresp[$rslt['id_contact']]['coef']		= $coef_tot;
								$tab_corresp[$rslt['id_contact']]['id_contact'] = $rslt['id_contact'];
								$tab_corresp[$rslt['id_contact']]['lastname']	= $rslt['lastname'];
								$tab_corresp[$rslt['id_contact']]['firstname']	= $rslt['firstname'];
							}

						}
						sort($tab_corresp);
					}

					echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LINK_CONTACT']);
					echo '<p>';
						if(count($tab_corresp) > 0) {
							echo $_DIMS['cste']['_DIMS_USER_FILE_EXIST'].'.<br />
							'.$_DIMS['cste']['_DIMS_USER_WISH_RATTACH_TO_FILE'].' :<br />';

							foreach($tab_corresp as $corresp) {
								echo '<input type="radio" name="contact_rattach" value="'.$corresp['id_contact'].'" />';
								echo $corresp['lastname'].' '.$corresp['firstname'].'&nbsp;';
								echo '<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_FORM.'&dims_desktop=block&dims_action=public&contact_id='.$corresp['id_contact'].'" target="_BLANK">';
								echo 'voir la fiche</a>.<br />';
							}
							echo $_DIMS['cste']['_DIMS_OR'].' :<br />';

							echo '<input type="radio" name="contact_rattach" value="-1" checked="checked" /> '.$_DIMS['cste']['_DIMS_USER_CREATE_NEW_FILE'].'.<br />';
							echo '<input type="button" value="'.$_DIMS['cste']['_DIMS_VALID'].'" onclick="javascript:
									for( i=0; i<document.getElementsByName(\'contact_rattach\').length; i++)
									{
										elem = document.getElementsByName(\'contact_rattach\').item(i);
										if(elem.checked)
											this.form.contact_id.value = elem.value;
									} document.form_modify_user.submit();" />&nbsp;';
							echo '<input type="button" value="'.$_DIMS['cste']['_DIMS_LABEL_CANCEL'].'" onclick="javascript:document.location.href=\'/admin.php\';" />';

						}
						else
							echo '<script language="javascript">
									document.form_modify_user.submit();
								</script>';
					echo '</p>';
					echo $skin->close_simplebloc();
				ob_end_flush();
				die();
				break;
			case "save_user":
				$user_id=dims_load_securvalue('user_id',dims_const::_DIMS_NUM_INPUT,true,true);
				$user_login=dims_load_securvalue('user_login',dims_const::_DIMS_CHAR_INPUT,true,true);
				$userx_password=dims_load_securvalue('userx_password',dims_const::_DIMS_CHAR_INPUT,true,true);
				$userx_passwordconfirm=dims_load_securvalue('userx_passwordconfirm',dims_const::_DIMS_CHAR_INPUT,true,true);

				if ($user_id>0) $user->open($user_id);
				/*
				// If identical logins are allowed
				if($_SESSION['dims']['modules'][dims_const::_DIMS_MODULE_SYSTEM]['system_same_login']) {
					$res=$db->query("SELECT id FROM dims_user WHERE id <> $user_id AND login = '$user_login' AND password = '". dims_getPasswordHash($userx_password) ."'");
					if($db->numrows($res)) dims_redirect("$scriptenv?op=modify_user&user_id={$user_id}&error=passrejected");
				}
				else {*/

				$params = array(
					':user_id' => $user_id,
					':user_login' => $user_login);
				$res=$db->query("SELECT id FROM dims_user WHERE id <> :user_id AND login = :user_login", $params);
				if($db->numrows($res)>0) dims_redirect("$scriptenv?op=modify_user&user_id={$user_id}&error=login");
				//}

				if (!isset($_POST['user_ticketsbyemail'])) $user->fields['ticketsbyemail'] = 0;

				$user->setvalues($_POST,'user_');

				$user->fields['color']=strtoupper($user->fields['color']);

				$user->createPicto();
				// set new password if not blank
				$passwordok = true;

				if ($userx_password!='' && $userx_password == $userx_passwordconfirm) {
									$dims->getPasswordHash($userx_password,$user->fields['password'],$user->fields['salt']);
					//$user->fields['password'] = dims_getPasswordHash($userx_password);
					//if ($_SESSION['dims']['modules'][dims_const::_DIMS_MODULE_SYSTEM]['system_generate_htpasswd']) system_generate_htpasswd($user->fields['login'], $userx_password);
				}
				elseif ($user->fields['password'] == '' || $userx_password != $userx_passwordconfirm) $passwordok = false;

				$user->save();

				dims_create_user_action_log(_SYSTEM_ACTION_MODIFYUSER, "{$user->fields['login']} - {$user->fields['lastname']} {$user->fields['firstname']} (id:{$user->fields['id']})");

				if ($_SESSION['system_level'] == dims_const::_SYSTEM_WORKSPACES && !empty($workspaceid)) {
					// modify profile/adminlevel for current group/user
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
					if (!isset($_POST['userworkspace_activecontact'])) $workspace_user->fields['activecontact'] = 0;
					else $workspace_user->fields['activecontact'] = 1;
					if (!isset($_POST['userworkspace_activeplanning'])) $workspace_user->fields['activeplanning'] = 0;
					else $workspace_user->fields['activeplanning'] = 1;
					if (!isset($_POST['userworkspace_activeproject'])) $workspace_user->fields['activeproject'] = 0;
					else $workspace_user->fields['activeproject'] = 1;
										if (!isset($_POST['userworkspace_activenewsletter'])) $workspace_user->fields['activenewsletter'] = 0;
					else $workspace_user->fields['activenewsletter'] = 1;
					if (!isset($_POST['userworkspace_activeevent'])) {
											$workspace_user->fields['activeevent'] = 0;
											$workspace_user->fields['activeeventemail'] = 0;
										}
					else {
											$workspace_user->fields['activeevent'] = 1;

											if (!isset($_POST['userworkspace_activeeventemail'])) $workspace_user->fields['activeeventemail'] = 0;
											else $workspace_user->fields['activeeventemail'] = 1;
										}
										if (!isset($_POST['userworkspace_activeeventstep'])) $workspace_user->fields['activeeventstep'] = 0;
					else $workspace_user->fields['activeeventstep'] = 1;

										if (!isset($_POST['userworkspace_activeswitchuser'])) $workspace_user->fields['activeswitchuser'] = 0;
					else $workspace_user->fields['activeswitchuser'] = 1;

					// save current config
					$workspace_user->save();;
				}
				elseif($_SESSION['system_level'] == dims_const::_SYSTEM_GROUPS && !empty($groupid)) {
					$group_user = new group_user();
					$group_user->open($groupid,$user->fields['id']);
					$group_user->setvalues($_POST,'usergroup_');
					$group_user->save();
				}

				if ($passwordok) dims_redirect("$scriptenv?reloadsession");
				else dims_redirect("$scriptenv?op=modify_user&user_id=".$user->fields['id']."&error=password");
				break;

			case 'modify_user':
				$user_id=dims_load_securvalue('user_id',dims_const::_DIMS_NUM_INPUT,true,false);
				$user->open($user_id);
				$group_user = new group_user();
				$group_user->open($groupid,$user_id);
				include(DIMS_APP_PATH . '/modules/system/admin_index_users_form.php');
				break;

			case "delete_user":
				$user_id=dims_load_securvalue('user_id',dims_const::_DIMS_NUM_INPUT,true,false);
				global $admin_redirect;
				$admin_redirect	= true;

				$user->open($user_id);
				//if ($_SESSION['dims']['modules'][dims_const::_DIMS_MODULE_SYSTEM]['system_generate_htpasswd']) system_generate_htpasswd($user->fields['login'], '', true);

				echo $skin->open_simplebloc(str_replace('<LABEL>',$user->fields['login'],$_DIMS['cste']['_DIMS_LABEL_USERDELETE']),'100%');
				?>
				<TABLE CELLPADDING="2" CELLSPACING="1"><TR><TD>
				<?php
				dims_create_user_action_log(_SYSTEM_ACTION_DELETEUSER, "{$user->fields['login']} - {$user->fields['lastname']} {$user->fields['firstname']} (id:{$user->fields['id']})");

				$user->delete();
				if ($admin_redirect) dims_redirect("$scriptenv");
				else {
					?>
						</TD>
					</TR>
					<TR>
						<TD ALIGN="RIGHT">
						<INPUT TYPE="Button" CLASS="FlatButton" VALUE="<?php echo $_DIMS['cste']['_DIMS_CONTINUE']; ?>" OnClick="javascript:document.location.href='<?php echo "$scriptenv"; ?>'">
						</TD>
					</TR>
					</TABLE>
					<?php
					echo $skin->close_simplebloc();
				}
				break;

			case 'detach_user':
				switch ($_SESSION['system_level']) {
					case dims_const::_SYSTEM_GROUPS :

						$user_id=dims_load_securvalue('user_id',dims_const::_DIMS_NUM_INPUT,true,false);

						global $admin_redirect;
						$admin_redirect	= true;

						$user = new user();
						$user->open($user_id);

						$group = new group();
						$group->open($groupid);

						dims_create_user_action_log(_SYSTEM_ACTION_DETACHUSER, "{$user->fields['login']} - {$user->fields['lastname']} {$user->fields['firstname']} (id:{$user->fields['id']}) => {$group->fields['label']} (id:$groupid)");

						echo $skin->open_simplebloc(str_replace('<LABELGROUP>',$group->fields['label'],str_replace('<LABELUSER>',$user->fields['login'],$_DIMS['cste']['_DIMS_LABEL_USERDETACH'])),'100%');
						?>
						<TABLE CELLPADDING="2" CELLSPACING="1"><TR><TD>
						<?php
						$group_user = new group_user();
						$group_user->open($groupid,$user_id);
						$group_user->delete();

						dims_redirect("$scriptenv?reloadsession");
						break;

					case dims_const::_SYSTEM_WORKSPACES :
						$user_id=dims_load_securvalue('user_id',dims_const::_DIMS_NUM_INPUT,true,false);

						global $admin_redirect;
						$admin_redirect	= true;

						$user = new user();
						$user->open($user_id);

						$workspace = new workspace();
						$workspace->open($workspaceid);

						dims_create_user_action_log(_SYSTEM_ACTION_DETACHUSER, "{$user->fields['login']} - {$user->fields['lastname']} {$user->fields['firstname']} (id:{$user->fields['id']}) => {$workspace->fields['label']} (id:$workspaceid)");

						echo $skin->open_simplebloc(str_replace('<LABELGROUP>',$workspace->fields['label'],str_replace('<LABELUSER>',$user->fields['login'],$_DIMS['cste']['_DIMS_LABEL_USERDETACH'])),'100%');
						?>
						<TABLE CELLPADDING="2" CELLSPACING="1"><TR><TD>
						<?php
						$workspace_user = new workspace_user();
						$workspace_user->open($workspaceid,$user_id);
						$workspace_user->delete();

						dims_redirect("$scriptenv?reloadsession");

						break;
				}
				break;

			default:
				include(DIMS_APP_PATH . '/modules/system/admin_index_users_list.php');
				break;

		}
		break;

	case _SYSTEM_TAB_USERADD:
		$user = new user();

		switch($op) {
			case "search_contact":
				@ob_end_clean();
				ob_start();
					$user_lastname	=dims_load_securvalue('user_lname',dims_const::_DIMS_CHAR_INPUT,true,false);
					$user_firstname	=dims_load_securvalue('user_fname',dims_const::_DIMS_CHAR_INPUT,true,false);
					$tab_corresp	= array();

					//Recherche d'un contact similaire
					$sql = 'SELECT
								ct.id as id_contact,
								ct.lastname,
								ct.firstname
							FROM
								dims_mod_business_contact ct';

					$ress = $db->query($sql);

					if($db->numrows($ress) > 0) {
						$nom	= strtoupper($user_lastname);
						$prenom = strtoupper($user_firstname);

						while($rslt = $db->fetchrow($ress)) {
							$lev_nom = 0;
							$lev_pre = 0;

							$coef_nom = 0;
							$coef_pre = 0;

							$coef_tot = 0;

							$lev_nom = levenshtein($nom, strtoupper($rslt['lastname']));
							$coef_nom = $lev_nom - (ceil(strlen($nom)/4));

							$lev_pre = levenshtein($prenom, strtoupper($rslt['firstname']));
							$coef_pre = $lev_pre - (ceil(strlen($prenom)/4));

							$coef_tot = $coef_nom + $coef_pre;

							if($coef_tot < 2) {
								$tab_corresp[$rslt['id_contact']]['coef']		= $coef_tot;
								$tab_corresp[$rslt['id_contact']]['id_contact'] = $rslt['id_contact'];
								$tab_corresp[$rslt['id_contact']]['lastname']	= $rslt['lastname'];
								$tab_corresp[$rslt['id_contact']]['firstname']	= $rslt['firstname'];
							}

						}
						sort($tab_corresp);
					}

					echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LINK_CONTACT']);
					echo '<p>';
						if(count($tab_corresp) > 0) {
							echo $_DIMS['cste']['_DIMS_USER_FILE_EXIST'].'.<br />
							'.$_DIMS['cste']['_DIMS_USER_WISH_RATTACH_TO_FILE'].' :<br />';

							foreach($tab_corresp as $corresp) {
								echo '<input type="radio" name="contact_rattach" value="'.$corresp['id_contact'].'" />';
								echo $corresp['lastname'].' '.$corresp['firstname'].'&nbsp;';
								echo '<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_FORM.'&dims_desktop=block&dims_action=public&contact_id='.$corresp['id_contact'].'" target="_BLANK">';
								echo 'voir la fiche</a>.<br />';
							}
							echo $_DIMS['cste']['_DIMS_OR'].' :<br />';

							echo '<input type="radio" name="contact_rattach" value="-1" checked="checked" /> '.$_DIMS['cste']['_DIMS_USER_CREATE_NEW_FILE'].'.<br />';
							echo '<input type="button" value="'.$_DIMS['cste']['_DIMS_VALID'].'" onclick="javascript:
									for( i=0; i<document.getElementsByName(\'contact_rattach\').length; i++)
									{
										elem = document.getElementsByName(\'contact_rattach\').item(i);
										if(elem.checked)
											this.form.contact_id.value = elem.value;
									} document.form_modify_user.submit();" />&nbsp;';
							echo '<input type="button" value="'.$_DIMS['cste']['_DIMS_LABEL_CANCEL'].'" onclick="javascript:document.location.href=\'/admin.php\';" />';

						}
						else
							echo '<script language="javascript">
									document.form_modify_user.submit();
								</script>';
					echo '</p>';
					echo $skin->close_simplebloc();
				ob_end_flush();
				die();
				break;

			case "save_user":

				$newuser = false;
				$user_id=dims_load_securvalue('user_id',dims_const::_DIMS_NUM_INPUT,false,true);
				$contact_id=dims_load_securvalue('contact_id',dims_const::_DIMS_NUM_INPUT,true,true);
				$contact_rattach=dims_load_securvalue('contact_rattach',dims_const::_DIMS_NUM_INPUT,true,true);
				$user_login=dims_load_securvalue('user_login',dims_const::_DIMS_CHAR_INPUT,true,true);

				$params = array();
				$params[':user_login']=$user_login;

				if ($user_id>0) {
					$user->open($user_id);
					$testuser = $user_id;
					$params[':testuser']=$testuser;
					$res=$db->query("SELECT id FROM dims_user WHERE id = :testuser AND login = :user_login", $params);
				}
				else {
					$newuser = true;
					$res=$db->query("SELECT id FROM dims_user WHERE login = :user_login", $params);
				}

				if($db->numrows($res)>0) dims_redirect("$scriptenv?op=manage_account&error=login");

				$user->setvalues($_POST,'user_');
				$user->fields['color']=strtoupper($user->fields['color']);
				$user->fields['id_group'] = $_SESSION['system_groupid'];
				// set new password if not blank
				$passwordok = true;

				$userx_password=dims_load_securvalue('userx_password',dims_const::_DIMS_CHAR_INPUT,true,true);
				$userx_passwordconfirm=dims_load_securvalue('userx_passwordconfirm',dims_const::_DIMS_CHAR_INPUT,true,true);

				if ($userx_password!='' && $userx_password == $userx_passwordconfirm) {
									$dims->getPasswordHash($userx_password,$user->fields['password'],$user->fields['salt']);
				//$user->fields['password'] = dims_getPasswordHash($userx_password);
					//if ($_SESSION['dims']['modules'][dims_const::_DIMS_MODULE_SYSTEM]['system_generate_htpasswd']) system_generate_htpasswd($user->fields['login'], $userx_password);
				}
				elseif ($userx_password != $userx_passwordconfirm) $passwordok = false;

				if ($newuser) dims_create_user_action_log(_SYSTEM_ACTION_CREATEUSER, "{$user->fields['login']} - {$user->fields['lastname']} {$user->fields['firstname']} (id:{$user->fields['id']})");
				else dims_create_user_action_log(_SYSTEM_ACTION_MODIFYUSER, "{$user->fields['login']} - {$user->fields['lastname']} {$user->fields['firstname']} (id:{$user->fields['id']})");

				$user->save($contact_id);

				if ($_SESSION['system_level'] == dims_const::_SYSTEM_WORKSPACES && !empty($workspaceid)) {
					// modify profile/adminlevel for current workspace/user
					$workspace_user = new workspace_user();
					$workspace_user->open($workspaceid, $user->fields['id']);
					$workspace_user->setvalues($_POST,'userworkspace_');
					$workspace_user->save();
				}

				if ($_SESSION['system_level'] == dims_const::_SYSTEM_GROUPS && !empty($groupid)) {
					$group_user = new group_user();
					$group_user->open($groupid, $user->fields['id']);
					$group_user->setvalues($_POST,'usergroup_');
					$group_user->save();
				}

				if ($passwordok) dims_redirect("$scriptenv?system_usertabid="._SYSTEM_TAB_USERLIST."&alpha=".(ord(strtolower($user->fields['lastname']))-96));
				else dims_redirect("$scriptenv?op=modify_user&user_id=".$user->fields['id']."&error=password");
				break;

			case 'modify_user':
				include(DIMS_APP_PATH . '/modules/system/admin_index_users_form.php');
				break;

			default:
				$user->init_description();
				$user->fields['id']=-1;
				include(DIMS_APP_PATH . '/modules/system/admin_index_users_form.php');
				break;
		}
		break;

	case _SYSTEM_TAB_RULELIST:
		$rule = new rule();

		switch($op)
		{
			case "save_rule":
				$newrule = false;
				$rule_id=dims_load_securvalue('rule_id',dims_const::_DIMS_NUM_INPUT,false,true);

				if (isset($rule_id) && $rule_id!='') {
					$rule->open($rule_id);
				}
				else $newrule = true;

				$rule->setvalues($_POST,'rule_');
				$rule->save();

				dims_redirect("$scriptenv?system_usertabid="._SYSTEM_TAB_RULELIST);
				break;

			case 'modify_rule':
				$user_id= $_SESSION["dims"]["userid"];
				$rule_id=dims_load_securvalue('rule_id',dims_const::_DIMS_NUM_INPUT,true,false);
				if (isset($rule_id) && $rule_id!='')
				{
					$rule->open($rule_id);
				}
				include(DIMS_APP_PATH . '/modules/system/admin_index_rules_form.php');
				break;

			case "delete_rule":
				$rule_id=dims_load_securvalue('rule_id',dims_const::_DIMS_NUM_INPUT,false,true);
				$rule->open($rule_id);
				$rule->delete();
				dims_redirect("$scriptenv?system_usertabid="._SYSTEM_TAB_RULELIST);
				break;

			default:
				include(DIMS_APP_PATH . '/modules/system/admin_index_rules_list.php');
				break;
		}
		break;

	case _SYSTEM_TAB_RULEADD:
		$rule = new rule();
		$rule->init_description();
		switch($op)
		{
			case 'add_rule':
				$group_id=dims_load_securvalue('group_id',dims_const::_DIMS_NUM_INPUT,true,false);
				$user_id= $_SESSION["dims"]["userid"];
				$rule->fields['id_group']=$group_id;
				include(DIMS_APP_PATH . '/modules/system/admin_index_rules_form.php');
				break;

			case "save_rule":
				$rule->setvalues($_POST,'rule_');
				$rule->save();

				dims_redirect("$scriptenv?system_usertabid="._SYSTEM_TAB_RULELIST);
				break;
		}
		break;

	case _SYSTEM_TAB_USERIMPORT:
		switch($op)
		{
			case 'import':
				require_once DIMS_APP_PATH . '/modules/system/admin_index_users_import.php';
				break;

			default:
				require_once DIMS_APP_PATH . '/modules/system/admin_index_users_import_form.php';
				break;
		}
		break;
}

echo $skin->close_simplebloc();
?>
