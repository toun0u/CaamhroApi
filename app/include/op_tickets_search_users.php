<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$listgroup = array();
require_once DIMS_APP_PATH . '/modules/system/class_group.php';
require_once DIMS_APP_PATH . '/modules/system/class_workspace.php';
require_once DIMS_APP_PATH . '/include/functions/tickets.php';
$group = new group();
$workspace = new workspace();
$list = array();
$list['work'] = array();
$list['org'] = array();

// construction de la liste des groupes de travail et des groupes d'utilisateurs rattachés (pour l'utilisateur courant)
$workspaces = $dims->getWorkspaces();
foreach ($workspaces as $grp) {// pour chaque groupe de travail
	if (isset($grp['adminlevel']) && $grp['admin']) {
		$list['work'][$grp['id']]['label'] = $grp['label'];
		$list['work'][$grp['id']]['org'] = array();
		$list['work'][$grp['id']]['users'] = array();
		$workspace->fields['id'] = $grp['id'];
		foreach ($workspace->getgroups() as $orgrp) {
			$list['work'][$grp['id']]['org'][] = $orgrp['id'];
			$list['org'][$orgrp['id']]['label'] = $orgrp['label'];
		}
	}
}
// recherche des utilisateurs attachés aux espaces precedemment selectionnes
$query_workspaces = "
		SELECT	u.*,
			wu.id_workspace

		FROM	dims_user u,
			dims_workspace w,
			dims_workspace_user wu

		WHERE	u.id = wu.id_user
		AND	w.id = wu.id_workspace
		AND	wu.id_workspace IN (" . implode(',', array_fill(0, count($list['work']), '?')) . ")
		AND	(u.lastname LIKE ?
		OR	u.firstname LIKE ?)

			";
$res = $db->query($query_workspaces, array_merge(
	array_keys($list['work']),
	array(
		array('type' => PDO::PARAM_STR, 'value' => '%'.dims_load_securvalue('dims_ticket_userfilter', dims_const::_DIMS_CHAR_INPUT, true, true, true).'%'),
		array('type' => PDO::PARAM_STR, 'value' => '%'.dims_load_securvalue('dims_ticket_userfilter', dims_const::_DIMS_CHAR_INPUT, true, true, true).'%'),
	)
));

if (!$db->numrows($res)) {
?>
<div class="system_tickets_select_empty">
	<p class="dims_va"><img src="<? echo $_SESSION['dims']['template_path']; ?>/img/system/btn_noway.png"><span>aucun espace de travail trouv�</span></p>
</div>
<?
} else {
?>
<div class="system_tickets_select">
<?php
	// affectation des utilisateurs à leurs groupes de rattachement
	while ($fields = $db->fetchrow($res)) {
		$list['work'][$fields['id_workspace']]['users'][$fields['id']] = array('id' => $fields['id'], 'login' => $fields['login'], 'lastname' => $fields['lastname'], 'firstname' => $fields['firstname']);
	}

	// pour chaque espace de travail
	foreach ($list['work'] as $id_workgrp => $workgrp) {
?>
		<div class="system_tickets_select_workgroup">
			<a href="javascript:void(0);" onclick="javascript:dims_xmlhttprequest_todiv('admin.php','dims_op=tickets_select_user&workspace_id=<? echo $id_workgrp; ?>','','div_ticket_users_selected');">
				<p class="dims_va"><img src="<? echo $_SESSION['dims']['template_path']; ?>/img/system/ico_workgroup.png"/><span><? echo $workgrp['label']; ?></span></p>
			</a>
		</div>
<?
		if (!empty($workgrp['users'])) {
			foreach ($workgrp['users'] as $id_user => $user) {
?>
				<a class="system_tickets_select_user" href="javascript:void(0);" onclick="javascript:dims_xmlhttprequest_todiv('admin.php','dims_op=tickets_select_user&user_id=<? echo $id_user; ?>','','div_ticket_users_selected');">
					<p class="dims_va"><img src="<? echo $_SESSION['dims']['template_path']; ?>/img/system/ico_user.png"><span><? echo "{$user['firstname']} {$user['lastname']} ({$user['login']})"; ?></span></p>
				</a>
<?
			}
		}

		$query_groups = "SELECT	u.*,
					wg.id_group
				FROM	dims_user u,
					dims_group g,
					dims_group_user gu,
					dims_workspace w,
					dims_workspace_group wg
				WHERE	u.id = gu.id_user
				AND	w.id = :idworkspace
				AND	wg.id_workspace = w.id
				AND	g.id = wg.id_group
				AND	g.id = gu.id_group
				AND	gu.id_group = wg.id_group
				AND	u.login LIKE :login'
			";


		$res = $db->query($query_groups, array(
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $id_workgrp),
			':login' => array('type' => PDO::PARAM_STR, 'value' => '%'.dims_load_securvalue('dims_ticket_userfilter', dims_const::_DIMS_CHAR_INPUT, true, true, true).'%'),
		));
		$listgroup = array();
		while ($fields = $db->fetchrow($res)) {
			$listgroup[$fields['id_group']]['users'][$fields['id']] = array('id' => $fields['id'], 'login' => $fields['login'], 'lastname' => $fields['lastname'], 'firstname' => $fields['firstname']);
		}

		foreach ($listgroup as $id_orggrp => $group) {
			if (!empty($group['users'])) {
?>
				<div class="system_tickets_select_usergroup">
					<a href="javascript:void(0);" onclick="javascript:dims_xmlhttprequest_todiv('admin.php','dims_op=tickets_select_user&group_id=<? echo $id_orggrp; ?>','','div_ticket_users_selected');">
						<p class="dims_va"><img src="<? echo $_SESSION['dims']['template_path']; ?>/img/system/ico_group.png"><span><? echo $list['org'][$id_orggrp]['label']; ?></span></p>
					</a>
				</div>
<?
				foreach ($group['users'] as $id_user => $user) {
?>
						<a class="system_tickets_select_usergroup_user" href="javascript:void(0);" onclick="javascript:dims_xmlhttprequest_todiv('admin.php','dims_op=tickets_select_user&user_id=<? echo $id_user; ?>','','div_ticket_users_selected');">
							<p class="dims_va"><img src="<? echo $_SESSION['dims']['template_path']; ?>/img/system/ico_user.png"><span><? echo "{$user['firstname']} {$user['lastname']} ({$user['login']})"; ?></span></p>
						</a>
<?
				}
			}
		}
	}
?>
	</div>
	<div class="system_tickets_select_legend">
		<p class="dims_va">
			<img src="<? echo $_SESSION['dims']['template_path']; ?>/img/system/ico_workgroup.png"><span>Espace de Travail</span>
			<img src="<? echo $_SESSION['dims']['template_path']; ?>/img/system/ico_group.png"><span>Groupe d'Utilisateur</span>
			<img src="<? echo $_SESSION['dims']['template_path']; ?>/img/system/ico_user.png"><span>Utilisateur</span>
		</p>
	</div>

<?
}

?>
