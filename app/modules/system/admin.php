<?
if (!dims_ismanager()) {
	dims_error("unauthorized area");
}
else {
	//dims_print_r($_POST);die();
	dims_init_module('system');
	$v = $view::getInstance();
	$m = $v->getStylesManager();
	$m->loadRessource('modules', 'system', true);

	require_once DIMS_APP_PATH.'modules/system/class_group.php';
	require_once DIMS_APP_PATH.'modules/system/class_user.php';
	require_once DIMS_APP_PATH.'modules/system/class_group_user.php';
	require_once DIMS_APP_PATH.'modules/system/class_module_type.php';
	require_once DIMS_APP_PATH.'modules/system/class_module_group.php';
	require_once DIMS_APP_PATH.'modules/system/class_domain.php';
	require_once DIMS_APP_PATH.'modules/system/class_mailinglist.php';
	require_once DIMS_APP_PATH.'modules/system/class_mailinglist_attach.php';

	//echo $skin->open_backgroundbloc("ADMINISTRATION SYSTEME", '', '', '');

	// security update
	$op = '';
	$op=dims_load_securvalue('op',dims_const::_DIMS_CHAR_INPUT,true,true,false);
	$system_level=dims_load_securvalue('system_level',dims_const::_DIMS_CHAR_INPUT,true,true,false);
	if (!empty($system_level)) $_SESSION['system_level']=$system_level;

	switch($_SESSION['system_level']) {
		case dims_const::_SYSTEM_GROUPS:
		case dims_const::_SYSTEM_WORKSPACES:

			$workspaces = system_getworkspaces();
			$groups = system_getgroups();

			$wid=(isset($_SESSION['system_workspaceid'])) ? $_SESSION['system_workspaceid'] : 0;
			$gid=(isset($_SESSION['system_groupid'])) ? $_SESSION['system_groupid'] : 0;

			$g=dims_load_securvalue('groupid',dims_const::_DIMS_NUM_INPUT,true,true,false);
			$w=dims_load_securvalue('workspaceid',dims_const::_DIMS_NUM_INPUT,true,true,false);

			if ($g>0) {
				// a verifier plus tard les droits de modification ou d'accès
				// 09/02/08 à 7H30
				//if (in_array($_GET['groupid'],$grp->getgroupchildrenlite()) || dims_isadmin()) {
				$_SESSION['system_groupid'] = $g;
				unset($_SESSION['system_workspaceid']);
				$_SESSION['system_level'] = dims_const::_SYSTEM_GROUPS;
				//unset($workspaceid);
			}

			if (isset($w) && $w>0) {
				$_SESSION['system_workspaceid'] = $w;
				$_SESSION['system_level'] = dims_const::_SYSTEM_WORKSPACES;
				unset($_SESSION['system_groupid']);
			}
			//if ($wid>0 && $workspaceid==0) $workspaceid=$wid;
			//if ($gid>0 && $groupid==0) $groupid=$wid;

			// recherche workspaceid si aucun groupe sélectionné
			if (empty($_SESSION['system_workspaceid']) && empty($_SESSION['system_groupid'])) {
				if ($_SESSION['dims']['adminlevel'] >= dims_const::_DIMS_ID_LEVEL_GROUPMANAGER && $_SESSION['dims']['adminlevel'] < dims_const::_DIMS_ID_LEVEL_SYSTEMADMIN) $_SESSION['system_workspaceid'] = $_SESSION['dims']['workspaceid'];
				else $_SESSION['system_workspaceid'] = $workspaces['tree'][1][0];

			}

			if (isset($workspaceid) && $workspaceid>0 && ($workspaceid == 1 || !isset($workspaces['list'][$workspaceid]))) {// group is not allowed here !
				if ($_SESSION['dims']['adminlevel'] >= dims_const::_DIMS_ID_LEVEL_GROUPMANAGER && $_SESSION['dims']['adminlevel'] < dims_const::_DIMS_ID_LEVEL_SYSTEMADMIN) $_SESSION['system_workspaceid'] = $_SESSION['dims']['workspaceid'];
				else $_SESSION['system_workspaceid'] = $workspaces['tree'][1][0];
			}

			if (!empty($_SESSION['system_workspaceid'])) {
				$workspaceid = $_SESSION['system_workspaceid'];
				$_SESSION['system_level'] = dims_const::_SYSTEM_WORKSPACES;
			}

			if (!empty($_SESSION['system_groupid']))
			{
				$groupid = $_SESSION['system_groupid'];
				$_SESSION['system_level'] = dims_const::_SYSTEM_GROUPS;
			}

			if ($op == 'xml_detail_group' ) {
				ob_end_clean();
				if (is_numeric($_GET['gid'])) {
					$gid = dims_load_securvalue('gid', dims_const::_DIMS_NUM_INPUT, true, true, true);
					$str = dims_load_securvalue('str', dims_const::_DIMS_CHAR_INPUT, true, true, true);
					switch($_GET['typetree']) {
						case 'workspaces':
							echo system_build_tree('workspaces',$workspaces,$groups, $gid, 0, $str);
						break;

						case 'groups':
							echo system_build_tree('groups',$workspaces,$groups, 0, $gid, $str);
						break;
					}
				}
				die();
			}
			/*
			echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_ADMIN_WORKSPACES'], 'width:100%;', 'text-transform:uppercase;color:#cccccc;');
			?>
			<div style="background:#FFFFFF">
				<div class="system_tree" style="background:#FFFFFF">
					<div class="system_tree_padding">
						<?
						if ($_SESSION['dims']['adminlevel'] >= dims_const::_DIMS_ID_LEVEL_SYSTEMADMIN) echo system_build_tree('workspaces',$workspaces,$groups);
						else echo system_build_tree('workspaces',$workspaces,$groups, $workspaces['list'][$_SESSION['dims']['workspaceid']]['id_workspace']);

						?>
					</div>
				</div>
				<div class="system_main" style="background:#FFFFFF;overflow:auto;">
					<? require_once DIMS_APP_PATH.'modules/system/admin_index.php'; ?>
				</div>
				<div style="clear:both"></div>
			</div>
			<?
			echo $skin->close_simplebloc();
                         */
                        echo $skin->open_simplebloc("Arbo", 'width:29%;float:left;', 'text-transform:uppercase;color:#cccccc;');

                        echo '<div class="system_tree_padding" style="">';
                                if ($_SESSION['dims']['adminlevel'] >= dims_const::_DIMS_ID_LEVEL_SYSTEMADMIN) echo system_build_tree('workspaces',$workspaces,$groups);
				else echo system_build_tree('workspaces',$workspaces,$groups, $workspaces['list'][$_SESSION['dims']['workspaceid']]['id_workspace']);
                        echo '</div>';
                        echo $skin->close_simplebloc();

                        //echo $skin->open_simplebloc("", 'float:right;width:70%;overflow:auto;', 'text-transform:uppercase;color:#cccccc;');
                        echo '<div style="float:right;width:70%;">';
                        require_once DIMS_APP_PATH.'modules/system/admin_index.php';
                        echo "</div>";
                        //echo $skin->close_simplebloc();
                        echo '<div style="clear:both"></div>';
		break;

		case 'system':
			//echo $skin->create_pagetitle($_DIMS['cste']['_GENERAL_ADMINISTRATION']);
			//echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_SYSTEM']);
			?>
			<div class="system_main">
			<? require_once DIMS_APP_PATH.'modules/system/admin_system.php'; ?>
			</div>
			<?
		break;
	}

	//echo $skin->close_simplebloc();
	//echo $skin->close_backgroundbloc();
}
?>
