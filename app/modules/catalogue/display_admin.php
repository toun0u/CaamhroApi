<?php
if (!$_SESSION['dims']['connected']) {
	dims_redirect($dims->getScriptEnv());
}

ob_start();

function treeOpenBranch($branch = '', $url = '') {
	return "\n['$branch','$url',";
}
function treeCloseBranch() {
	return "],";
}

include_once $_SESSION['dims']['front_template_path'].'/class_skin.php';
$skin = new skin();

$user = new user();
$user->open($_SESSION['dims']['userid']);
//$groups = $user->getgroups(true);
$groups = $user->getusersgroup('', $_SESSION['dims']['workspaceid']);

//recherche des groupes enfants
$group = new group();
//$group->open(key($groups));
$group->open($_SESSION['catalogue']['groupid']);

if (isset($_GET['groupid']) || isset($_POST['groupid'])) {
	$groupid = dims_load_securvalue('groupid', dims_const::_DIMS_NUM_INPUT, true, true);
}


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
if (!isset($groupid) && !isset($_SESSION['vpc_groupid'])) $groupid = $groups[0]['id'];

// get groupid
if (isset($groupid)) $_SESSION['vpc_groupid'] = $groupid;
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
		$strGroups .= treeOpenBranch($groupName, $dims->getScriptEnv()."?op=administration&groupid=$gID");
	}

	if ($level < $oldLevel) {
		for ($i = $level; $i <= $oldLevel; $i++) {
			$strGroups .= treeCloseBranch();
		}
		$strGroups .= str_repeat("\t", $level) . treeOpenBranch($groupName, $dims->getScriptEnv()."?op=administration&groupid=$gID");
	}

	if ($level > $oldLevel) {
		$strGroups .= treeOpenBranch($groupName, $dims->getScriptEnv()."?op=administration&groupid=$gID");
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
?>

<table cellpadding="0" cellspacing="0" width="100%">
<tr bgcolor="#8BCE44">
	<td class="WebNavTitle">&nbsp;Administration</td>
</tr>
<tr bgcolor="#dddddd" height="1"><td></td></tr>
<tr>
	<td>
		<table cellpadding="4" cellspacing="0" width="100%">
		<tr align="left" valign="top">
			<td width="150" bgcolor="#f8f8f8">
				<div id="treeview">
					<script type="text/javascript">
						root = new tree (TREE_ITEMS, tree_tpl);
					</script>
				</div>
			</td>
			<td>
				<table cellpadding="4" cellspacing="1" width="100%" class="WebSkin">
				<tr>
					<td>
						<?php
						include_once DIMS_APP_PATH.'/modules/catalogue/display_admin_index.php';
						?>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>

<script type="text/javascript">
	searchNode('<?php echo $dims->getScriptEnv()."?op=administration&groupid=$groupid"; ?>');
</script>

<?php
$smarty->assign('catalogue', array('CONTENT' => ob_get_contents()));
ob_end_clean();
$page['TITLE'] = 'Administration des services';
$page['META_DESCRIPTION'] = '';
$page['META_KEYWORDS'] = '';
$page['CONTENT'] = '';
