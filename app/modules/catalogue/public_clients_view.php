<script type="text/javascript" src="./common/modules/catalogue/include/treeview.js"></script>

<?php
function treeOpenBranch($branch = '', $url = '') {
	return "\n['$branch','$url',";
}
function treeCloseBranch() {
	return "],";
}

$cref = dims_load_securvalue('cref', dims_const::_DIMS_NUM_INPUT, true, false);
$groupid = dims_load_securvalue('groupid', dims_const::_DIMS_NUM_INPUT, true, false);

if (!empty($cref)) {
	include_once './common/modules/catalogue/include/class_client.php';
	include_once './common/modules/system/class_group.php';

	$client = new client();
	if ($client->open($cref)) {
		if (!empty($client->fields['id_group'])) {
			$group = new group();
			if ($group->open($client->fields['id_group'])) {
				// ---------------------------------------------------------------------------
				// Get Users & Groups to build treeview
				// ---------------------------------------------------------------------------
				$strGroups = '';
				$grp = new group();
				$grp->open($group->fields['id']);
				$groups = array();
				// add current group => could be better
				$groups[0]['depth'] = 1;
				$groups[0]['idparent'] = $grp->fields['id_group'];
				$groups[0]['id'] = $grp->fields['id'];
				$groups[0]['label'] = $grp->fields['label'];
				cata_getallgroups_treeview($groups, $group->fields['id'], 0, 0, 2);

				$oldLevel = 0;

				// array size
				$nbItem = count($groups);

				// set default groupid selection in treeview
				if (empty($groupid) && !isset($_SESSION['vpc_groupid'])) $groupid = $groups[0]['id'];

				// get groupid
				if (!empty($groupid)) $_SESSION['vpc_groupid'] = $groupid;
				else $groupid = $_SESSION['vpc_groupid'];

				$group_allowed = false;
				foreach($groups as $index => $group) if ($groupid == $group['id']) $group_allowed = true;
				reset($groups);

				if (!$group_allowed) $groupid = $groups[0]['id'];

				$n = 0;
				$count = '';

				foreach ($groups as $index => $group) {
					$level = $group['depth'];
					$groupInfos = $group;
					$gID = $groupInfos['id'];

					// ---------------------------------
					// Mise-en-valeur du groupe en cours
					// ---------------------------------
					$label = addslashes($groupInfos['label']);
					if ($gID == $groupid) {
						$groupName = "<u><b>$label</b></u>";
					}
					else {
						$groupName = $label;
					}
					// ---------------------------------

					++$n;

					if ($level == $oldLevel) {
						$strGroups .= treeCloseBranch();
						$strGroups .= treeOpenBranch($groupName, $dims->getScriptEnv()."?part=clients&action=view&cref=$cref&groupid=$gID");
					}

					if ($level < $oldLevel) {
						for ($i = $level; $i <= $oldLevel; $i++) {
							$strGroups .= treeCloseBranch();
						}
						$strGroups .= str_repeat("\t", $level) . treeOpenBranch($groupName, $dims->getScriptEnv()."?part=clients&action=view&cref=$cref&groupid=$gID");
					}

					if ($level > $oldLevel) {
						$strGroups .= treeOpenBranch($groupName, $dims->getScriptEnv()."?part=clients&action=view&cref=$cref&groupid=$gID");
					}

					if ($n == $nbItem) {
						for ($i = 1; $i <= $level; $i++) {
							$strGroups .= treeCloseBranch();
						}
					}

					$oldLevel = $level;
				}

				// ###########################
				// # Echoing the entire tree #
				// ###########################
				echo "<script type=\"text/javascript\">var TREE_ITEMS = [$strGroups];</script>";
			}
		}
	}
}

echo '<table cellpadding="0" cellspacing="0"><tr><td valign="top">';
	echo $skin->open_simplebloc('Services');
	echo '<script type="text/javascript">root = new tree (TREE_ITEMS, tree_tpl);</script>';
	echo $skin->close_simplebloc();
echo '</td><td valign="top">';
	include_once './common/modules/catalogue/public_clients_users.php';
echo '</td></tr></table>';
