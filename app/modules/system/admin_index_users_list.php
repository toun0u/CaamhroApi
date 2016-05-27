<?
$alpha=dims_load_securvalue('alpha',dims_const::_DIMS_NUM_INPUT,true,true);
if (isset($reset)) {
	$pattern = '';
	unset($_SESSION['system_alphasel']);
}

if (isset($alpha) && $alpha!='') $_SESSION['system_alphasel'] = $alpha;

if (!isset($_SESSION['system_alphasel'])) {

	switch ($_SESSION['system_level']) {
		case dims_const::_SYSTEM_GROUPS :
			$params = array( ':groupid' => $groupid );
			$select =	"
						SELECT		count(dims_user.id) as nbuser
						FROM		dims_user

						INNER JOIN	dims_group_user ON dims_group_user.id_user = dims_user.id
						AND			dims_group_user.id_group = :groupid
						";
			break;
		case dims_const::_SYSTEM_WORKSPACES :
			$params = array( ':workspaceid' => $workspaceid );
			$select =	"
						SELECT		count(dims_user.id) as nbuser
						FROM		dims_user

						INNER JOIN	dims_workspace_user ON dims_workspace_user.id_user = dims_user.id
						AND			dims_workspace_user.id_workspace = :workspaceid
						";
			break;

	}

	$res=$db->query($select, $params);

	$_SESSION['system_alphasel'] = 1;

	if ($fields = $db->fetchrow($res))
	{
		if ($fields['nbuser'] < 25) $_SESSION['system_alphasel'] = 99;
	}
}

$pattern=dims_load_securvalue('pattern',dims_const::_DIMS_CHAR_INPUT,true,true);
if (!isset($pattern)) $pattern = '';

if ($pattern != '') $_SESSION['system_alphasel'] = 99; // tous
?>

<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1">
<TR>
	<TD>
	<?
	$tabs_char = array();

	for($i=1;$i<27;$i++) {
		$tabs_char[$i]['title'] = chr($i+64);
		$tabs_char[$i]['url'] = "$scriptenv?alpha=$i";
	}
	$tabs_char[99]['title'] = "&nbsp;tous&nbsp;";
	$tabs_char[99]['url'] = "$scriptenv?alpha=99";

	echo $skin->create_tabs('',$tabs_char,$_SESSION['system_alphasel']);
	?>
	</TD>
<TR>
<form method="post" action="<? echo "$scriptenv" ?>" name="form_users_list">
<?
// Sécurisation du formulaire par token
require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
$token = new FormToken\TokenField;
$token->field("pattern");
$tokenHTML = $token->generate();
echo $tokenHTML;

$where = array();

$params = array();
if ($_SESSION['system_alphasel'] != 99) {// tous ou recherche
	$where[] = "dims_user.lastname LIKE :alphasel";
	$params[':alphasel'] = array('type' => PDO::PARAM_STR, 'value' => chr($_SESSION['system_alphasel']+96).'%');
}
if ($pattern != '') {
	$where[] .=  "(dims_user.lastname LIKE :pattern OR dims_user.firstname LIKE :pattern OR dims_user.login LIKE :pattern)";
	$params[':pattern'] = array('type' => PDO::PARAM_STR, 'value' => '%'.$pattern.'%');
}

$where = (empty($where)) ? '' : 'WHERE '.implode(' AND ', $where);

switch ($_SESSION['system_level']) {
	case dims_const::_SYSTEM_WORKSPACES :
		$workspace = new workspace();
		$workspace->open($workspaceid);
		$workspace->page_courant = dims_load_securvalue('page', dims_const::_DIMS_NUM_INPUT, true, true, true, $currentvar, 0);
		$listusers=$workspace->getListUsers($where, $params);
		break;

	case dims_const::_SYSTEM_GROUPS :
		$group = new group();
		$group->open($groupid);
		$group->page_courant = dims_load_securvalue('page', dims_const::_DIMS_NUM_INPUT, true, true, true, $currentvar, 0);
		$listusers=$group->getListUsers($where, $params);
		break;
}
?>
<TR>
	<TD>
	<TABLE CELLPADDING="2" CELLSPACING="0">
	<TR>
		<TD><? echo $_DIMS['cste']['_DIMS_LABEL_USER']; ?> :</TD>
		<TD>
			<input class="text" ID="system_user" name="pattern" type="text" size="15" maxlength="255" value="<? echo $pattern; ?>">
		</TD>
		<TD>
			<? echo dims_create_button($_DIMS['cste']['_DIMS_FILTER'],"","","","form_users_list.submit();"); ?>
		</TD>
		<!--<TD>-->
		<!--	<? echo dims_create_button($_DIMS['cste']['_DIMS_RESET'],"","","","form_users_list.submit();"); ?>-->
		<!--</TD>-->
	</TR>
	</TABLE>
	</TD>
</TR>
<TR>
	<TD>
	<TABLE WIDTH=100% CELLPADDING=2 CELLSPACING=1>
	<TR CLASS="title">
		<TD ALIGN="CENTER"><? echo $_DIMS['cste']['_DIMS_LABEL_NAME']; ?></TD>
		<TD ALIGN="CENTER"><? echo $_DIMS['cste']['_FIRSTNAME']; ?></TD>
		<TD ALIGN="CENTER"><? echo $_DIMS['cste']['_LOGIN']; ?></TD>
			<TD ALIGN="CENTER"><? echo $_DIMS['cste']['_DIMS_LABEL_LEVEL']; ?></TD>
		<?
		if ($_SESSION['system_level'] == dims_const::_SYSTEM_WORKSPACES) {
			?>

			<TD ALIGN="CENTER"><? echo $_DIMS['cste']['_PROFIL']; ?></TD>
			<?
		}
		?>
		<TD ALIGN="CENTER"><? echo $_DIMS['cste']['_DIMS_LABEL_ORIGIN']; ?></TD>
		<?
		if ($_SESSION['system_level'] == dims_const::_SYSTEM_GROUPS) {
			?>
			<TD ALIGN="CENTER"><? echo $_DIMS['cste']['_DIMS_LABEL_FUNCTION']; ?></TD>
			<?
		}
		?>
		<TD ALIGN="CENTER"><? echo $_DIMS['cste']['_SERVICE']; ?></TD>
		<TD width="15%" ALIGN="CENTER"><? echo $_DIMS['cste']['_LABEL_ACTION']; ?></TD>
	</TR>
	<?

	$user = new user();
	$tabgroups=$user->getFirstGroups(array_keys($listusers));

	foreach ($listusers  as $id=> $fields) {
		$user->fields['id'] = $fields['id'];
		//$groups = $user->getgroupsadmin();
		if (isset($tabgroups[$fields['id']])) $currentgroup = current($tabgroups[$fields['id']]);
		else $currentgroup=0;

		/*
		 * if($currentgroup['id'] == $groupid) $action = "<A HREF=\"javascript:dims_confirmlink('$scriptenv?op=delete_user&user_id=$fields[id]','".$_DIMS['cste']['_SYSTEM_MSG_CONFIRMUSERDELETE']."')\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/btn_delete.png\" ALT=\"".$_DIMS['cste']['_DELETE']."\" border=\"0\"></A>";
		else $action = "<A HREF=\"javascript:dims_confirmlink('$scriptenv?op=detach_user&user_id=$fields[id]','".$_DIMS['cste']['_SYSTEM_MSG_CONFIRMUSERDETACH']."')\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/btn_cut.png\" ALT=\"".$_DIMS['cste']['_DIMS_LABEL_DETACH']."\" border=\"0\"></A>";
		*/
		if ($_SESSION['system_level']==dims_const::_SYSTEM_WORKSPACES)
			$action = "<A HREF=\"javascript:dims_confirmlink('$scriptenv?op=detach_user&user_id=$fields[id]','".$_DIMS['cste']['_SYSTEM_MSG_CONFIRMUSERDETACH']."')\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/btn_cut.png\" ALT=\"".$_DIMS['cste']['_DIMS_LABEL_DETACH']."\" border=\"0\"></A>";
		else
			$action = "<A HREF=\"javascript:dims_confirmlink('$scriptenv?op=detach_user&user_id=$fields[id]','".$_DIMS['cste']['_SYSTEM_MSG_CONFIRMUSERDETACH']."')\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/btn_cut.png\" ALT=\"".$_DIMS['cste']['_DIMS_LABEL_DETACH']."\" border=\"0\"></A>&nbsp;<A HREF=\"javascript:dims_confirmlink('$scriptenv?op=delete_user&user_id=$fields[id]','".$_DIMS['cste']['_SYSTEM_MSG_CONFIRMUSERDELETE']."')\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/btn_delete.png\" ALT=\"".$_DIMS['cste']['_DELETE']."\" border=\"0\"></A>";

		switch ($_SESSION['system_level']) {
			case dims_const::_SYSTEM_WORKSPACES :
				if (isset($dims_system_levels[$fields['adminlevel']])) $level=$dims_system_levels[$fields['adminlevel']];
				else $level="";

				if ($_SESSION['dims']['adminlevel'] >= $fields['adminlevel'])
					$manage_user =	"<TD ALIGN=\"CENTER\"><A HREF=\"$scriptenv?op=modify_user&user_id=$fields[id]\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/btn_edit.png\" border=\"0\" ALT=\"".$_DIMS['cste']['_MODIFY']."\"></A>&nbsp;$action</TD>";
				else
					$manage_user =	"<TD ALIGN=\"CENTER\"><img src=\"./common/modules/system/img/ico_noway.gif\" ALT=\"\">&nbsp;&nbsp;<img src=\"{$_SESSION['dims']['template_path']}/img/system/btn_noway.png\" ALT=\"\"></TD>";

				echo	"
						<TR>
							<TD ALIGN=\"CENTER\">{$fields['lastname']}</TD>
							<TD ALIGN=\"CENTER\">{$fields['firstname']}</TD>
							<TD ALIGN=\"CENTER\">{$fields['login']}</TD>
							<TD ALIGN=\"CENTER\">$level</TD>
							<TD ALIGN=\"CENTER\">{$fields['profile']}</TD>
							<TD ALIGN=\"CENTER\"><A HREF=\"$scriptenv?system_level=org&dims_moduleicon="._SYSTEM_ICON_USERS."&system_usertabid="._SYSTEM_TAB_USERLIST."&groupid=$currentgroup[id]&alpha=".(ord(strtolower($fields['lastname']))-96)."\">$currentgroup[label]</A></TD>
							<TD ALIGN=\"CENTER\">{$fields['service']}</TD>
							$manage_user
						</TR>
						";
			break;

			case dims_const::_SYSTEM_GROUPS :
				if (isset($dims_system_levels[$fields['adminlevel']])) $level=$dims_system_levels[$fields['adminlevel']];
				else $level="";

				echo	"
						<TR>
							<TD ALIGN=\"CENTER\">{$fields['lastname']}</TD>
							<TD ALIGN=\"CENTER\">{$fields['firstname']}</TD>
							<TD ALIGN=\"CENTER\">{$fields['login']}</TD>
							<TD ALIGN=\"CENTER\">$level</TD>
							<TD ALIGN=\"CENTER\"><A HREF=\"$scriptenv?system_level=org&dims_moduleicon="._SYSTEM_ICON_USERS."&system_usertabid="._SYSTEM_TAB_USERLIST."&groupid=$currentgroup[id]&alpha=".(ord(strtolower($fields['lastname']))-96)."\">$currentgroup[label]</A></TD>
							<TD ALIGN=\"CENTER\">{$fields['function']}</TD>
							<TD ALIGN=\"CENTER\">{$fields['service']}</TD>
							<TD ALIGN=\"CENTER\"><A HREF=\"$scriptenv?op=modify_user&user_id=$fields[id]\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/btn_edit.png\" border=\"0\" ALT=\"".$_DIMS['cste']['_MODIFY']."\"></A>&nbsp;$action</TD>
						</TR>
						";
			break;
		}
	}

	?>
	</TABLE>
	</TD>
</TR>
<tr>
	<td>
	<?php
	switch ($_SESSION['system_level']) {
		case dims_const::_SYSTEM_WORKSPACES :
			$workspace->show_pagination();
		break;
		case dims_const::_SYSTEM_GROUPS :
			$group->show_pagination();
		break;
	}

	?>
	</td>
</tr>
</TABLE>
