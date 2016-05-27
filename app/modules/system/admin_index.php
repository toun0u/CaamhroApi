<?

switch ($_SESSION['system_level']) {
	case dims_const::_SYSTEM_GROUPS :
		$group = new group();
		/////////////////////////////////////////////
		// security update
		// v�rification de l'appartenance � ce groupe
		$groupid=dims_load_securvalue('groupid',dims_const::_DIMS_NUM_INPUT,true,true,true,$_SESSION['system_groupid']);
		//dims_print_r($_POST);die();
				if ($groupid>0)
			$group->open($groupid);
		else die();

		if ($_SESSION['dims']['adminlevel'] < dims_const::_DIMS_ID_LEVEL_GROUPMANAGER && !in_array($_SESSION['userid'],$group->getusers()) ) die();

		$currentgroup = '';
		$childgroup = '';

		if (isset($_SESSION['dims']['modules'][dims_const::_DIMS_MODULE_SYSTEM]["system_groupdepth{$group->fields['depth']}_label"]) && $_SESSION['dims']['modules'][dims_const::_DIMS_MODULE_SYSTEM]["system_groupdepth{$group->fields['depth']}_label"] != '') {
			$currentgroup = "(" . $_SESSION['dims']['modules'][dims_const::_DIMS_MODULE_SYSTEM]["system_groupdepth{$group->fields['depth']}_label"] . ")";
		}

		if (isset($_SESSION['dims']['modules'][dims_const::_DIMS_MODULE_SYSTEM]["system_groupdepth".($group->fields['depth']+1)."_label"]) && $_SESSION['dims']['modules'][dims_const::_DIMS_MODULE_SYSTEM]["system_groupdepth".($group->fields['depth']+1)."_label"] != '') {
			$childgroup = "(" . $_SESSION['dims']['modules'][dims_const::_DIMS_MODULE_SYSTEM]["system_groupdepth".($group->fields['depth']+1)."_label"] . ")";
		}
			$toolbar[_SYSTEM_ICON_GROUP] = array(
												'title'		=> $_DIMS['cste']['_GROUP']."<br />$currentgroup",
												'url'		=> "$scriptenv?dims_moduleicon="._SYSTEM_ICON_GROUP,
												'icon'	=> "{$_SESSION['dims']['template_path']}/img/system/icons/tab_group.png"
											);

			$toolbar[_SYSTEM_ICON_USERS] = array(
												'title'		=> $_DIMS['cste']['_USERS'],
												'url'		=> "$scriptenv?dims_moduleicon="._SYSTEM_ICON_USERS,
												'icon'	=> "{$_SESSION['dims']['template_path']}/img/system/icons/tab_user.png"
											);


		echo $skin->create_toolbar($toolbar,$_SESSION['dims']['moduleicon']);
		//echo $skin->close_toolbar();

			switch($_SESSION['dims']['moduleicon']) {
				// ---------------
				// ONGLET "GROUPE"
				// ---------------
				case _SYSTEM_ICON_GROUP:
					switch($op) {
						case 'save_group' :
							$group = new group();
							$group_id_group=dims_load_securvalue('group_id_group',dims_const::_DIMS_NUM_INPUT,true,true,true);

							if ($group_id_group>0) {
								$parent_group = new group();
								$parent_group->open($group_id_group);
								$group->fields['parents'] = $parent_group->fields['parents'].';'.$group_id_group;

							}
							else {
								if ($groupid>0) $group->open($groupid);
							}

							$group->setvalues($_POST,'group_');
							if (empty($_POST['group_shared'])) $group->fields['shared'] = 0;

							dims_create_user_action_log(_SYSTEM_ACTION_MODIFYGROUP, "{$group->fields['label']} ($group_id)");


							$group_id = $group->save();

							dims_redirect("$scriptenv?groupid=$group_id&reloadsession");
						break;

						case 'child' :
							include DIMS_APP_PATH . '/modules/system/admin_index_group_add.php';
						break;

						case 'clone' :
							$clone = $group->createclone();
							$groupid = $clone->save();
							dims_create_user_action_log(_SYSTEM_ACTION_CLONEGROUP, "{$clone->fields['label']} ($groupid)");

							// get father

							dims_redirect("$scriptenv?groupid=$groupid");
						break;

						case 'delete' :
							$sizeof_groups = sizeof($group->getgroupchildrenlite());
							//$sizeof_users = sizeof($group->getusers());
							$sizeof_users = $group->getNbUsers();

							if (!$sizeof_groups && !$sizeof_users) {
								$idfather = $group->fields['id_group'];
								dims_create_user_action_log(_SYSTEM_ACTION_DELETEGROUP, "{$group->fields['label']} ({$group->fields['id_group']})");
								$group->delete();
								dims_redirect("$scriptenv?groupid=$idfather");
							}
							dims_redirect($scriptenv);
						break;

						default :
							require_once DIMS_APP_PATH . '/modules/system/admin_index_group.php';
						break;
					}
				BREAK;
				// ---------------------
				// USER MANAGEMENT
				// ---------------------
				case _SYSTEM_ICON_USERS:
					require_once DIMS_APP_PATH . '/modules/system/admin_index_users.php';
				break;
			} // switch

				echo $skin->close_toolbar();
		break;

	case dims_const::_SYSTEM_WORKSPACES :
		$workspace = new workspace();

		/////////////////////////////////////////////
		// security update
		// verification de l'appartenance a ce groupe
		$workspaceid=dims_load_securvalue('workspaceid',dims_const::_DIMS_NUM_INPUT,true,true,true,$_SESSION['system_workspaceid']);
		if ($workspaceid>0)
			$workspace->open($workspaceid);
		else die();

		if ($_SESSION['dims']['adminlevel'] < dims_const::_DIMS_ID_LEVEL_GROUPMANAGER && !in_array($_SESSION['userid'],$workspace->getusers()) ) die();

		$currentworkspace = '';
		$childworkspace = '';

		if (isset($_SESSION['dims']['modules'][dims_const::_DIMS_MODULE_SYSTEM]["system_workspacedepth{$workspace->fields['depth']}_label"]) && $_SESSION['dims']['modules'][dims_const::_DIMS_MODULE_SYSTEM]["system_workspacedepth{$workspace->fields['depth']}_label"] != '') {
			$currentworkspace = "(" . $_SESSION['dims']['modules'][dims_const::_DIMS_MODULE_SYSTEM]["system_workspacedepth{$workspace->fields['depth']}_label"] . ")";
		}

		if (isset($_SESSION['dims']['modules'][dims_const::_DIMS_MODULE_SYSTEM]["system_workspacedepth".($workspace->fields['depth']+1)."_label"]) && $_SESSION['dims']['modules'][dims_const::_DIMS_MODULE_SYSTEM]["system_workspacedepth".($workspace->fields['depth']+1)."_label"] != '') {
			$childworkspace = "(" . $_SESSION['dims']['modules'][dims_const::_DIMS_MODULE_SYSTEM]["system_workspacedepth".($group->fields['depth']+1)."_label"] . ")";
		}


		$toolbar[_SYSTEM_ICON_GROUP] = array(
											'title'		=> $_DIMS['cste']['_WORKSPACE']."<br />$currentworkspace",
											'url'		=> "$scriptenv?dims_moduleicon="._SYSTEM_ICON_GROUP,
											'icon'	=> "{$_SESSION['dims']['template_path']}/img/system/icons/tab_workspace.png"
										);

		$toolbar[_SYSTEM_ICON_USERS] = array(
											'title'		=> $_DIMS['cste']['_USERS'],
											'url'		=> "$scriptenv?dims_moduleicon="._SYSTEM_ICON_USERS,
											'icon'	=> "{$_SESSION['dims']['template_path']}/img/system/icons/tab_user.png"
										);

		if ($_SESSION['dims']['adminlevel'] >= dims_const::_DIMS_ID_LEVEL_GROUPADMIN) {

			$toolbar[_SYSTEM_ICON_MAILINGLIST] = array(
												'title'		=> $_DIMS['cste']['_DIMS_LABEL_MAILINGLIST'],
												'url'		=> "$scriptenv?dims_moduleicon="._SYSTEM_ICON_MAILINGLIST,
												'icon'	=> "{$_SESSION['dims']['template_path']}/img/system/icons/tab_mailinglist.png"
											);

			$toolbar[_SYSTEM_ICON_MODULES] = array(
												'title'		=> $_DIMS['cste']['_DIMS_LABEL_MODULES'],
												'url'		=> "$scriptenv?dims_moduleicon="._SYSTEM_ICON_MODULES,
												'icon'	=> "{$_SESSION['dims']['template_path']}/img/system/icons/tab_module.png"
											);
/*
			$toolbar[_SYSTEM_ICON_PARAMS] = array(
												'title'		=> $_DIMS['cste']['_SYSTEM_LABELICON_PARAMS'],
												'url'		=> "$scriptenv?dims_moduleicon="._SYSTEM_ICON_PARAMS,
												'icon'	=> "{$_SESSION['dims']['template_path']}/img/system/icons/tab_systemparams.png"
											);
*/
			$toolbar[_SYSTEM_ICON_ROLES] = array(
												'title'		=> $_DIMS['cste']['_SYSTEM_LABELICON_ROLES'],
												'url'		=> "$scriptenv?dims_moduleicon="._SYSTEM_ICON_ROLES,
												'icon'	=> "{$_SESSION['dims']['template_path']}/img/system/icons/tab_role.png"
											);

			//if ($_SESSION['dims']['modules'][dims_const::_DIMS_MODULE_SYSTEM]['system_use_profiles'])
			//{
				$toolbar[_SYSTEM_ICON_PROFILES] = array(
													'title'		=> $_DIMS['cste']['_SYSTEM_LABELICON_PROFILES'],
													'url'		=> "$scriptenv?dims_moduleicon="._SYSTEM_ICON_PROFILES,
													'icon'	=> "{$_SESSION['dims']['template_path']}/img/system/icons/tab_profile.png"
												);
			//}
		}

		//echo $skin->create_onglet($toolbar,$_SESSION['dims']['moduleicon'],'','','onglet');
		echo $skin->create_toolbar($toolbar,$_SESSION['dims']['moduleicon']);
		?>

					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
							<td align="left" valign="top">
							<?
							$moduleid=dims_load_securvalue('moduleid',dims_const::_DIMS_NUM_INPUT,true,true,false);
							//$workspaceid = $_SESSION['system_workspaceid'];
	//dims_print_r($_POST);
	//			die();
							switch($_SESSION['dims']['moduleicon']) {
									// ---------------
									// ONGLET "ESPACE DE TRAVAIL"
									// ---------------

									case _SYSTEM_ICON_GROUP:
											switch($op) {
													case 'save_group' :
															$group = new group();

															$group->setvalues($_POST,'group_');
															if (empty($_POST['group_shared'])) $group->fields['shared'] = 0;
															$group->fields['id_group'] = 1;
															$group->fields['id_workspace'] = $workspaceid;
															$group->fields['parents'] = '0;1';

															$group_id = $group->save();

															dims_create_user_action_log(_SYSTEM_ACTION_CREATEGROUP, "{$group->fields['label']} ($group_id)");

															dims_redirect("$scriptenv?groupid=$group_id&reloadsession");
													break;

													case 'save_workspace_events':
													case 'save_workspace_newsletter':
															$workspace = new workspace();
															$updated_workspace = dims_load_securvalue('workspace_id',dims_const::_DIMS_NUM_INPUT,true,true);
															$workspace->open($updated_workspace);
															$workspace->setvalues($_POST,'workspace_');
															$workspace_id = $workspace->save();

															dims_redirect("$scriptenv?workspaceid=$workspace_id");
													break;

													case 'save_workspace' :
															$workspace = new workspace();
															$updated_workspace = dims_load_securvalue('workspace_id',dims_const::_DIMS_NUM_INPUT,true,true);

															if (isset($updated_workspace) && $updated_workspace != '') $workspace->open($updated_workspace);

															$parent_id=dims_load_securvalue('parent_id',dims_const::_DIMS_NUM_INPUT,true,true);
															if ($parent_id>0) {
																	$parent_workspace = new workspace();
																	$parent_workspace->open($parent_id);
																	$workspace->fields['id_workspace'] = $parent_id;
																	$parent_parents=$parent_workspace->fields['parents'];
																	$workspace->fields['parents'] = $parent_parents.';'.$parent_id;
															}

															$workspace->setvalues($_POST,'workspace_');

															if (empty($_POST['workspace_admin'])) $workspace->fields['admin'] = 0;
															if (empty($_POST['workspace_web'])) $workspace->fields['web'] = 0;
															if (empty($_POST['workspace_frontaccess_limited'])) $workspace->fields['frontaccess_limited'] = 0;
															if (empty($_POST['workspace_mustdefinerule'])) $workspace->fields['mustdefinerule'] = 0;
															if (empty($_POST['workspace_project'])) $workspace->fields['project'] = 0;
															if (empty($_POST['workspace_planning'])) $workspace->fields['planning'] = 0;
															if (empty($_POST['workspace_share_info'])) $workspace->fields['share_info'] = 0;
															if (empty($_POST['workspace_ssl'])) $workspace->fields['ssl'] = 0;
															if (empty($_POST['workspace_switchuser'])) $workspace->fields['switchuser'] = 0;

															if (empty($_POST['workspace_contact'])) {
																	$workspace->fields['contact'] = 0;
																	$workspace->fields['contact_intel'] = 0;
																	$workspace->fields['contact_docs'] = 0;
																	$workspace->fields['contact_tags'] = 0;
																	$workspace->fields['contact_comments'] = 0;
																	$workspace->fields['contact_activeent'] = 0;
																	$workspace->fields['contact_outlook'] = 0;
																	if (empty($_POST['workspace_planning'])) $workspace->fields['planning'] = 0;
																	// on supprime les associations
																	$workspace->deleteShareObject(dims_const::_SYSTEM_OBJECT_CONTACT);
															}
															else {
																	if (empty($_POST['workspace_contact_intel'])) $workspace->fields['contact_intel'] = 0;
																	if (empty($_POST['workspace_contact_docs'])) $workspace->fields['contact_docs'] = 0;
																	if (empty($_POST['workspace_contact_tags'])) $workspace->fields['contact_tags'] = 0;
																	if (empty($_POST['workspace_contact_comments'])) $workspace->fields['contact_comments'] = 0;
																	if (empty($_POST['workspace_contact_activeent'])) $workspace->fields['contact_activeent'] = 0;
																	if (empty($_POST['workspace_contact_outlook'])) $workspace->fields['contact_outlook'] = 0;
															}

															if (empty($_POST['workspace_tickets'])) $workspace->fields['tickets'] = 0;
															if (empty($_POST['workspace_newsletter'])) $workspace->fields['newsletter'] = 0;

															if (isset($_FILES) && !empty($_FILES)) $workspace->createBackground();

															$workspace_id = $workspace->save();
															dims_create_user_action_log(_SYSTEM_ACTION_MODIFYGROUP, "{$workspace->fields['label']} ($workspace_id)");
															system_updateparents();
															$heritedmodule = dims_load_securvalue('heritedmodule', dims_const::_DIMS_CHAR_INPUT, false, true);//Cyril : fix mega bug, on le récupérait pas de $_POST
															if (isset($heritedmodule)) {
																	foreach($heritedmodule as $instance) {
																			$data = explode(',',$instance);
																			$instancetype = $data[0];
																			if ($instancetype == 'NEW') {
																					$moduletype_id = $data[1];
																					$module_type = new module_type();
																					$module_type->open($moduletype_id);

																					dims_create_user_action_log(_SYSTEM_ACTION_USEMODULE, $module_type->fields['label']);

																					$module = $module_type->createinstance($workspace_id);
																					$module_id = $module->save();

																					$module_workspace = new module_workspace();
																					$module_workspace->fields['id_module'] = $module_id;
																					$module_workspace->fields['id_workspace'] = $workspace_id;
																					$module_workspace->fields['visible'] = 1;
																					$module_workspace->save();
																			}
																			elseif ($instancetype == 'SHARED') {
																					$module_id = $data[1];
																					$module = new module();
																					$module->open($module_id);

																					dims_create_user_action_log(_SYSTEM_ACTION_USEMODULE, $module->fields['label']);

																					$module_workspace = new module_workspace();
																					$module_workspace->fields['id_module'] = $module_id;
																					$module_workspace->fields['id_workspace'] = $workspace_id;
																					$module_workspace->fields['visible'] = 1;
																					$module_workspace->save();
																			}
																	}
															}
															if ($workspace->fields['id_lang']>0 && isset($_SESSION['dims']['lang'][$workspace->fields['id_lang']])) {
																	if ($_SESSION['dims']['currentlang'] != $workspace->fields['id_lang']) {
																			$_DIMS['cste']=$dims->loadLanguage($workspace->fields['id_lang']);
																	}
															}
															dims_redirect("$scriptenv?workspaceid=$workspace_id&reloadsession");
													break;

													case 'groupchild':
															include DIMS_APP_PATH . '/modules/system/admin_index_group_add.php';
													break;

													case 'child' :
															/*
															$child = $group->createchild();
															$groupid = $child->save();
															dims_create_user_action_log(_SYSTEM_ACTION_CREATEGROUP, "{$child->fields['label']} ($groupid)");

															$modules = $group->getsharedmodules(TRUE);

															foreach($modules as $moduleid => $module)
															{
																	$module_workspace = new module_workspace();
																	$module_workspace->fields['id_group'] = $groupid;
																	$module_workspace->fields['id_module'] = $moduleid;
																	$module_workspace->save();
															}

															dims_redirect("$scriptenv?groupid=$groupid");
															*/

															include DIMS_APP_PATH . '/modules/system/admin_index_workspace_add.php';
													break;

													case 'clone' :
															$clone = $workspace->createclone();
															$workspaceid = $clone->save();
															dims_create_user_action_log(_SYSTEM_ACTION_CLONEGROUP, "{$clone->fields['label']} ($workspaceid)");

															// get father

															if ($father = $workspace->getfather())
															{
																	$modules = $father->getsharedmodules(TRUE);

																	// inherit shared modules from father to clone (brother) of current group
																	foreach($modules as $moduleid => $module)
																	{
																			$module_workspace = new module_workspace();
																			$module_workspace->fields['id_workspace'] = $workspaceid;
																			$module_workspace->fields['id_module'] = $moduleid;
																			$module_workspace->fields['visible'] = 1;
																			$module_workspace->save();
																	}
															}

															dims_redirect("$scriptenv?workspaceid=$workspaceid");
													break;

													case 'delete' :
															$sizeof_workspaces = sizeof($workspace->getworkspacechildrenlite());
															$sizeof_users = sizeof($workspace->getusers());
															if (!$sizeof_workspaces && !$sizeof_users) {
																	$modules = $workspace->getmodules();

																	foreach ($modules AS $moduleid => $moduleinfos)
																	{
																			$module = new module();
																			$module->open($moduleid);

																			// Si le module appartient au groupe, on supprime le module
																			if ($moduleinfos['instanceworkspace'] == $workspaceid)
																			{
																					$module->delete();
																			}
																			else
																			{
																					$module->unlink($workspaceid);
																			}
																	}

																	$idfather = $workspace->fields['id_workspace'];
																	dims_create_user_action_log(_SYSTEM_ACTION_DELETEGROUP, "{$workspace->fields['label']} ({$workspace->fields['id_workspace']})");
																	$workspace->delete();

																	dims_redirect("$scriptenv?workspaceid=$idfather");
															}
															else dims_redirect($scriptenv);
													break;

													default :
															require_once DIMS_APP_PATH . '/modules/system/admin_index_workspace.php';
													break;
											}
									BREAK;

									CASE _SYSTEM_ICON_MAILINGLIST:
											if(!isset($_SESSION['dims']['current']['mailinglist']['id'])) $_SESSION['dims']['current']['mailinglist']['id'] = 0;
											switch ($op) {
													case 'switch_protected':
													case 'switch_public':
															$mailinglistid=dims_load_securvalue('mailinglistid',dims_const::_DIMS_NUM_INPUT,true,true,false);
															$mailinglist = new mailinglist();
															if ($mailinglistid>0) {
																	$mailinglist->open($mailinglistid);

																	if ($op == 'switch_protected') $mailinglist->fields['protected'] = ($mailinglist->fields['protected']+1)%2;
																	if ($op == 'switch_public') $mailinglist->fields['public'] = ($mailinglist->fields['public']+1)%2;

																	$mailinglist->save();
															}
															dims_redirect("$scriptenv");
													break;

													case 'save_mailinglist_props' :
															$mailinglistid=dims_load_securvalue('mailinglistid',dims_const::_DIMS_NUM_INPUT,true,true,false);
															$mailinglist = new mailinglist();
															if ($mailinglistid>0) {
																	$mailinglist->open($mailinglistid);
															}
															else {
																	$mailinglist->init_description();
																	$mailinglist->fields['id_workspace'] = $workspaceid;
															}

															$mailinglist->setvalues($_POST,'mailinglist_');
															if (!$mailinglist->fields['protected']) $module->fields['protected'] = 0;
															if (!$mailinglist->fields['public']) $module->fields['public'] = 0;
															$mailinglist->save();

															dims_redirect("$scriptenv?mailinglistid=$mailinglistid&reloadsession");
													break;
													case 'delete' :
															global $admin_redirect;
															$admin_redirect	= true;

															$mailinglistid=dims_load_securvalue('mailinglistid',dims_const::_DIMS_NUM_INPUT,true,true,false);
															$mailinglist = new mailinglist();
															if ($mailinglistid>0) {
																	$mailinglist->open($mailinglistid);
																	$mailinglist->delete();
															}
															dims_redirect("$scriptenv");
													break;
													case  "mailinglist_addnew_attach":
															$mailinglistid=dims_load_securvalue('mailinglistid',dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['dims']['current']['mailinglist']['id']);

															$mailinglist = new mailinglist();
															$mailinglist->open($mailinglistid);

															require_once DIMS_APP_PATH . '/modules/system/admin_index_mailinglist_attach.php';
													break;
													case "modify_attach":
															$mailinglistid=dims_load_securvalue('mailinglistid',dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['dims']['current']['mailinglist']['id']);
															$mailinglist_attachid=dims_load_securvalue('ml_attach_id',dims_const::_DIMS_NUM_INPUT,true,true,false);

															$mailinglist = new mailinglist();
															$mailinglist->open($mailinglistid);

															if ($mailinglist_attachid>0) {
																	require_once DIMS_APP_PATH . '/modules/system/admin_index_mailinglist_attach.php';
															}
													break;
													case "delete_attach":
															$ml_attach_id=dims_load_securvalue('ml_attach_id',dims_const::_DIMS_NUM_INPUT,true,true,false);
															$mailinglist_attach = new mailinglist_attach();
															if ($ml_attach_id>0) {
																	$mailinglist_attach->open($ml_attach_id);
																	$mailinglist_attach->delete();
																	dims_redirect("$scriptenv?mailinglistid=".$_SESSION['dims']['current']['mailinglist']['id']."&op=viewlist");
															}
													break;
													case "save_attach":
															if ($_SESSION['dims']['current']['mailinglist']['id']>0) {
																	$mailinglist_attachid=dims_load_securvalue('ml_attach_id',dims_const::_DIMS_NUM_INPUT,true,true,false);
																	$mailinglist_attach = new mailinglist_attach();
																	if ($mailinglist_attachid>0) {
																			$mailinglist_attach->open($mailinglist_attachid);
																	}
																	else {
																			$mailinglist_attach->init_description();
																			$mailinglist_attach->fields['id_user'] =0;
																			$mailinglist_attach->fields['id_group'] =0;
																	}

																	$mailinglist_attach->setvalues($_POST,'mailinglist_attach_');
																	$mailinglist_attach->fields['id_mailinglist'] =$_SESSION['dims']['current']['mailinglist']['id'];
																	$mailinglist_attach->save();
																	dims_print_r($mailinglist_attach);
																	dims_redirect("$scriptenv?mailinglistid=".$_SESSION['dims']['current']['mailinglist']['id']."&op=viewlist");
															}
															else dims_redirect("$scriptenv");
															break;
													case 'viewlist':
															$mailinglistid=dims_load_securvalue('mailinglistid',dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['dims']['current']['mailinglist']['id']);
															$mailinglist = new mailinglist();
															if ($mailinglistid>0) {
																	$mailinglist->open($mailinglistid);
																	$_SESSION['dims']['current']['mailinglist']['id']=$mailinglistid;
																	require_once DIMS_APP_PATH . '/modules/system/admin_index_mailinglist_attach.php';
															}
													break;
													default :
															require_once DIMS_APP_PATH . '/modules/system/admin_index_mailinglist.php';
													break;
											}
									break;
									CASE _SYSTEM_ICON_MODULES:
											switch ($op) {
													case 'refreshsharedmodules':

															ob_end_clean();
															if (isset($_SESSION['dims']['current']['workspaceid'])) {

																	$workspace=new workspace();
																	$workspace->open($_SESSION['dims']['current']['workspaceid']);

																	// shared modules
																	$sharedmodules = $workspace->getsharedmodules();

																	// own modules
																	$ownmodules = $workspace->getmodules();
																	echo "<TABLE WIDTH=\"100%\" CELLPADDING=\"2\" CELLSPACING=\"1\">";

																	$color = '';
																	echo	"
																			<TR CLASS=\"Title\" BGCOLOR=\"".$color."\">
																					<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_TYPE']."</TD>
																					<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_DESCRIPTION']."</TD>
																					<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_LABEL_ACTION']."</TD>
																			</TR>
																			";

																	  foreach ($sharedmodules AS $instanceid => $instance)	{
																			//if (!array_key_exists($instanceid,$ownmodules)) {


																					echo	"
																							<TR BGCOLOR=\"".$color."\">
																									<TD ALIGN=\"CENTER\">".$instance['label']."</TD>
																									<TD ALIGN=\"CENTER\">".$instance['description']."</TD>
																									<TD ALIGN=\"CENTER\"><A HREF=\"$scriptenv?op=add&instance=SHARED,$workspaceid,$instanceid\">".$_DIMS['cste']['_FORM_USER_OK']."</a></TD>
																							</TR>
																							";
																			//}
																	  }
																	echo "</table>";

															}
															die();
															break;
													case 'add' :
															global $admin_redirect;
															$admin_redirect	= true;

															//	create new instance or attach existing instance to current group
															$instance=dims_load_securvalue('instance', dims_const::_DIMS_CHAR_INPUT, true, true, true);
															$data = explode(',',$instance);

															$instancetype = $data[0];
															$workspace_id = $data[1];

															if ($instancetype == 'NEW') {
																	$moduletype_id = $data[2];
																	$module_type = new module_type();
																	$module_type->open($moduletype_id);

																	dims_create_user_action_log(_SYSTEM_ACTION_USEMODULE, $module_type->fields['label']);

																	echo $skin->open_simplebloc(str_replace('<LABEL>',$module_type->fields['label'],$_DIMS['cste']['_DIMS_LABEL_MODULEINSTANCIATION']),'100%');
																	?>
																	<TABLE CELLPADDING="2" CELLSPACING="1"><TR><TD>
																	<?
																	$module = $module_type->createinstance($workspaceid);
																	$module->save();
																	$module_id=$module->fields['id'];
																	$module_workspace = new module_workspace();
																	$module_workspace->fields['id_module'] = $module_id;
																	$module_workspace->fields['id_workspace'] = $workspaceid;
																	$module_workspace->fields['visible'] = 1;;
																	$module_workspace->save();

																	if ($admin_redirect) dims_redirect("$scriptenv?tab=modules&op=modify&moduleid=$module_id#modify");
																	else {
																			?>
																							</TD>
																					</TR>
																					<TR>
																							<TD ALIGN="RIGHT">
																							<INPUT TYPE="Button" CLASS="FlatButton" VALUE="<? echo $_DIMS['cste']['_DIMS_CONTINUE']; ?>" OnClick="javascript:document.location.href='<? echo "$scriptenv?reloadsession&tab=modules&op=modify&moduleid=$module_id#modify"; ?>'">
																							</TD>
																					</TR>
																					</TABLE>
																			<?
																			echo $skin->close_simplebloc();
																	}
															}
															elseif ($instancetype == 'SHARED')
															{
																	$module = new module();
																	$module_id=$data[2];

																	if ($module_id>0) {
																			$module->open($module_id);

																			dims_create_user_action_log(_SYSTEM_ACTION_USEMODULE, $module->fields['label']);

																			$module_workspace = new module_workspace();
																			$module_workspace->fields['id_module'] = $module_id;
																			$module_workspace->fields['id_workspace'] = $workspace_id;
																			$module_workspace->fields['visible'] = 1;

																			$module_workspace->save();

																			dims_print_r($module_workspace->fields['id_workspace']);
																	}
																	if ($admin_redirect) dims_redirect("$scriptenv?reloadsession");
															}
															else dims_redirect("$scriptenv?reloadsession");
													break;

													case 'switch_active':
													case 'switch_public':
													case 'switch_shared':
													case 'switch_herited':
													case 'switch_visible':
													case 'switch_autoconnect':
															$module = new module();
															if ($moduleid>0) {
																	$module->open($moduleid);
																	dims_create_user_action_log(_SYSTEM_ACTION_PARAMMODULE, $module->fields['label']);

																	if ($op == 'switch_visible') {
																			$module_workspace = new module_workspace();
																			$module_workspace->open($workspaceid,$moduleid);
																			$module_workspace->fields['visible'] = ($module_workspace->fields['visible']+1)%2;
																			$module_workspace->save();
																	}

																	if ($op == 'switch_autoconnect') {
																			if ($workspaceid==$module->fields['id_workspace']) {
																					$module->fields['autoconnect']=($module->fields['autoconnect']+1)%2;
																					/*
																					$module_workspace = new module_workspace();
																					$module_workspace->open($workspaceid,$moduleid);
																					$module_workspace->fields['autoconnect'] = ($module_workspace->fields['autoconnect']+1)%2;
																					$module_workspace->save();
																					*/
																			}
																			else {
																					$module_workspace = new module_workspace();
																					$module_workspace->open($workspaceid,$moduleid);
																					$module_workspace->fields['autoconnect'] = ($module_workspace->fields['autoconnect']+1)%2;
																					$module_workspace->save();
																			}
																	}

																	if ($op == 'switch_active') $module->fields['active'] = ($module->fields['active']+1)%2;
																	if ($op == 'switch_public') $module->fields['public'] = ($module->fields['public']+1)%2;
																	if ($op == 'switch_shared') {
																			if ($module->fields['shared'] && $module->fields['viewmode']==dims_const::_DIMS_VIEWMODE_GLOBAL) {
																					// on restreint par d�faut la vue en priv�e car on supprime les partages
																					$module->fields['viewmode']=dims_const::_DIMS_VIEWMODE_PRIVATE;
																			}

																			$module->fields['shared'] = ($module->fields['shared']+1)%2;
																			// verification des droits sur le partage des modules
																			$module->verifyShares();

																	}
																	if ($op == 'switch_herited') $module->fields['herited'] = ($module->fields['herited']+1)%2;

																	$module->save();
															}
															dims_redirect("$scriptenv?reloadsession");
													break;

													case 'moveleft' :
															$module_workspace = new module_workspace();
															$module_workspace->open($workspaceid,$moduleid);
															$module_workspace->fields['blockposition'] = 'left';
															$module_workspace->save();
															dims_redirect("$scriptenv?reloadsession");
													break;

													case 'moveright' :
															$module_workspace = new module_workspace();
															$module_workspace->open($workspaceid,$moduleid);
															$module_workspace->fields['blockposition'] = 'right';
															$module_workspace->save();
															dims_redirect("$scriptenv?reloadsession");
													break;

													case 'moveup' :
															$module_workspace = new module_workspace();
															$module_workspace->open($workspaceid,$moduleid);
															$module_workspace->changeposition('up');
															dims_redirect("$scriptenv?reloadsession");
													break;

													case 'movedown' :
															$module_workspace = new module_workspace();
															$module_workspace->open($workspaceid,$moduleid);
															$module_workspace->changeposition('down');
															dims_redirect("$scriptenv?reloadsession");
													break;

													case 'unlinkinstance' :

															$module = new module();
															if ($moduleid>0) {
																	$module->open($moduleid);
																	dims_create_user_action_log(_SYSTEM_ACTION_UNLINKMODULE, $module->fields['label']);

																	$module_workspace = new module_workspace();
																	$module_workspace->open($workspaceid,$moduleid);
																	$module_workspace->delete();
															}
															dims_redirect("$scriptenv?reloadsession");
													break;

													case 'save_module_props' :
															$module = new module();

															if ($moduleid>0) {
																	$module->open($moduleid);
																	dims_create_user_action_log(_SYSTEM_ACTION_CONFIGUREMODULE, $module->fields['label']);

																	$module->setvalues($_POST,'module_');
																	if (!isset($_POST['module_transverseview'])) $module->fields['transverseview'] = 0;
																	if (!$module->fields['shared']) $module->fields['herited'] = 0;
																	$module->save();

																	$module_workspace = new module_workspace();
																	$module_workspace->open($workspaceid,$moduleid);

																	if (!isset($_POST['moduleworkspace_visible'])) $module_workspace->fields['visible'] = 0;
																	else  $module_workspace->fields['visible'] = dims_load_securvalue('moduleworkspace_visible', dims_const::_DIMS_NUM_INPUT, true, true, true);

																	$module_workspace->save();
															}
															dims_redirect("$scriptenv?moduleid=$moduleid&reloadsession");
													break;

													case 'save_module_params' :
															$module = new module();
															if ($moduleid>0) {
																	$module->open($moduleid);
																	dims_create_user_action_log(_SYSTEM_ACTION_PARAMMODULE, $module->fields['label']);

																	$param_module = new param();
																	$param_module->open($moduleid);
																	$param_module->setvalues($HTTP_POST_VARS);
																	$param_module->save();
															}

															dims_redirect("$scriptenv?moduleid=$moduleid&reloadsession");
													break;

													case 'delete' :
															global $admin_redirect;
															$admin_redirect	= true;

															$module = new module();

															if ($moduleid>0) {
																	$module->open($moduleid);

																	dims_create_user_action_log(_SYSTEM_ACTION_DELETEMODULE, $module->fields['label']);

																	echo $skin->open_simplebloc(str_replace('<LABEL>',$module->fields['label'],$_DIMS['cste']['_MODULEDELETE']),'100%');

																	echo "<TABLE CELLPADDING=\"2\" CELLSPACING=\"1\"><TR><TD>";

																	$module->delete();

																	if ($admin_redirect) dims_redirect("$scriptenv?reloadsession");
																	else
																	{
																			echo "
																							</TD>
																					</TR>
																					<TR>
																							<TD ALIGN=\"RIGHT\">";
																					?>
																							<INPUT TYPE="Button" CLASS="FlatButton" VALUE="<? echo $_DIMS['cste']['_DIMS_CONTINUE']; ?>" OnClick="javascript:document.location.href='<? echo "$scriptenv?reloadsession"; ?>'">
																					<?
																					echo "	</TD>
																					</TR>
																					</TABLE>";

																			echo $skin->close_simplebloc();
																	}
															}
													break;

													case 'apply_heritage' :
															$children = $workspace->getworkspacechildrenlite();

															foreach($children as $idchildren)
															{
																	$module_workspace = new module_workspace();
																	$module_workspace->open($idchildren,$moduleid);
																	$module_workspace->save();
															}
															dims_redirect("$scriptenv?op=modify&moduleid=$moduleid#modify");

													break;

													default :
															require_once DIMS_APP_PATH . '/modules/system/admin_index_modules.php';
													break;

											}
									break;

									case _SYSTEM_ICON_PARAMS :
											$param_module = new param();

											switch($op)
											{
													case "save":
															$module = new module();
															$module->open($idmodule);
															dims_create_user_action_log(_SYSTEM_ACTION_PARAMMODULE, $module->fields['label']);

															$param_module->open($idmodule, $workspaceid);
															$param_module->setvalues($_POST);
															$param_module->save($module->fields['id_module_type']);

															dims_redirect("$scriptenv?idmodule=$idmodule&reloadsession");
													break;

													default:
															require_once DIMS_APP_PATH . '/modules/system/admin_index_param.php';
													break;
											}
									break;

									case _SYSTEM_ICON_ROLES:
											require_once DIMS_APP_PATH . '/modules/system/admin_index_roles.php';
									break;


									// ----------------
									// PROFILE MANAGEMENT
									// ----------------
									CASE _SYSTEM_ICON_PROFILES:
											require_once DIMS_APP_PATH . '/modules/system/admin_index_profiles.php';
									break;

									// ---------------------
									// USER MANAGEMENT
									// ---------------------
									case _SYSTEM_ICON_USERS:
											require_once DIMS_APP_PATH . '/modules/system/admin_index_users.php';
									break;

							} // switch
							echo "
							</td>
							</tr>
							</table>";
							//</div></div></div>
					echo $skin->close_toolbar();
					break;
}//switch
?>
