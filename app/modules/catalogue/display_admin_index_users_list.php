<?php
$pattern = dims_load_securvalue('pattern', dims_const::_DIMS_CHAR_INPUT, true, true);
$reset = dims_load_securvalue('reset', dims_const::_DIMS_CHAR_INPUT, true, true);

if (!empty($reset)) {
	$pattern = '';
	$idtype = '';
}
if (!isset($idtype)) $idtype = '';
if (!isset($pattern)) $pattern = '';
?>

<TABLE WIDTH=100% CELLPADDING=2 CELLSPACING=1>
<form method="post" action="<?php echo $dims->getScriptEnv(); ?>">
<INPUT TYPE='Hidden' NAME='groupid' VALUE='<? echo $groupid; ?>'>
<INPUT TYPE='Hidden' NAME='op' VALUE='administration'>
<INPUT TYPE='Hidden' NAME='groupinterid' VALUE='<? echo $_SESSION['catalogue']['root_group']; ?>'>
<TR>
	<TD>
	<TABLE CELLPADDING=2 CELLSPACING=0>
	<TR>
		<TD><? echo _CATALOGUE_LABEL_USER; ?> :</TD>
		<TD><input CLASS="Text" ID="system_user" name="pattern" type="text" size="15" maxlength="255" value="<? echo $pattern; ?>"></TD>
		<TD><input type="submit" value="Filtrer" class="Button"></TD>
		<TD><input name="reset" type="submit" value="Réinitialiser" class="Button"></TD>
	</TR>
	</TABLE>
	</TD>
</TR>
<TR>
	<TD>
	<TABLE WIDTH=100% CELLPADDING=2 CELLSPACING=1>
	<?
	$color=$skin->values['bgline1'];
	echo 	"
		<TR BGCOLOR=\"".$color."\" CLASS=\"Title\">
			<TD ALIGN=\"CENTER\">"._CATALOGUE_LABEL_LASTNAME."</TD>
			<TD ALIGN=\"CENTER\">"._CATALOGUE_LABEL_FIRSTNAME."</TD>
			<TD ALIGN=\"CENTER\">"._CATALOGUE_LABEL_LOGIN."</TD>
			<TD ALIGN=\"CENTER\">"._CATALOGUE_LABEL_LEVEL."</TD>
			<TD ALIGN=\"CENTER\">"._CATALOGUE_LABEL_ORIGIN."</TD>
			<TD ALIGN=\"CENTER\">"._CATALOGUE_LABEL_BUDGET."</TD>
			<TD ALIGN=\"CENTER\">"._CATALOGUE_LABEL_ACTION."</TD>
		</TR>";


	if (!empty($pattern))
		$pattern_search = "%$pattern%";
	else $pattern_search = "%";

	if (!isset($idtype)) $idtype = 0;

	if ($idtype != 0) $filter = " AND u.id_type = $idtype ";
	else $filter = '';

	if ($pattern_search=='') {
		$select = "
			SELECT 	u.id,
					u.lastname,
					u.firstname,
					u.login,
					ut.label as type,
					gu.adminlevel


			FROM	dims_group_user gu

			INNER JOIN	dims_user u
			ON		 	u.id = gu.id_user
			$filter


			LEFT JOIN	dims_user_type ut
			ON			ut.id = u.id_type

			WHERE		gu.id_group = $groupid

			ORDER BY	u.lastname, u.firstname, u.login";
	}
	else {
		$select = "
			SELECT 	u.id,
					u.lastname,
					u.firstname,
					u.login,
					ut.label as type,
					gu.adminlevel

			FROM	dims_group_user gu

			INNER JOIN 	dims_user u
			ON			u.id = gu.id_user
			AND		(
							u.lastname LIKE '$pattern_search'
						OR 	u.firstname LIKE '$pattern_search'
						OR 	u.login LIKE '$pattern_search'
			)
			$filter

			LEFT JOIN	dims_user_type ut
			ON			ut.id = u.id_type

			WHERE 	gu.id_group = $groupid

			ORDER BY	u.lastname, u.firstname, u.login";
	}
	$result = $db->query($select);
	$user = new user();

	while ($fields = $db->fetchrow($result)) {
		$user->fields['id'] = $fields['id'];
		$groups = $user->getgroupsadmin();
		$currentgroup = current($groups);

		// règle spécifique rothschild pour que le 'bon' groupe d'origine apparaisse
		if ($user->fields['id'] == 2728) {
			$currentgroup = $groups[2509];
		}

		if ($currentgroup['id'] == $groupid) {
			$action = "<A HREF=\"javascript:dims_confirmlink('".$dims->getScriptEnv()."?op=administration&action=delete_user&user_id=$fields[id]&groupid=$groupid&groupinterid=". $_SESSION['catalogue']['root_group'] ."','"._CATALOGUE_MSG_CONFIRMUSERDELETE."')\"><img src=\"./modules/system/img/ico_delete.gif\" ALT=\""._CATALOGUE_LABEL_DELETE."\" border=\"0\"></A>";
		}
		else {
			$action = "<A HREF=\"javascript:dims_confirmlink('".$dims->getScriptEnv()."?op=administration&action=detach_user&user_id=$fields[id]&groupid=$groupid&groupinterid=". $_SESSION['catalogue']['root_group'] ."','"._CATALOGUE_MSG_CONFIRMUSERDETACH."')\"><img src=\"./modules/system/img/ico_cut.gif\" ALT=\""._CATALOGUE_LABEL_DETACH."\" border=\"0\"></A>";
		}

		$color = ($color == $skin->values['bgline2']) ? $skin->values['bgline1'] : $skin->values['bgline2'];

		$budget = "Aucun";

		echo "
			<TR BGCOLOR=\"".$color."\">
				<TD ALIGN=\"CENTER\">$fields[lastname]</TD>
				<TD ALIGN=\"CENTER\">$fields[firstname]</TD>
				<TD ALIGN=\"CENTER\">$fields[login]</TD>
				<TD ALIGN=\"CENTER\">". $user_levels[$fields['adminlevel']] ."</TD>
				<TD ALIGN=\"CENTER\"><A HREF=\"".$dims->getScriptEnv()."?op=administration&groupid=$currentgroup[id]\">$currentgroup[label]</A></TD>
				<TD ALIGN=\"CENTER\">$budget</TD>
				<TD ALIGN=\"CENTER\"><A HREF=\"".$dims->getScriptEnv()."?op=administration&action=modify_user&user_id=$fields[id]\"><img src=\"./modules/system/img/crayon.gif\" border=\"0\" ALT=\""._CATALOGUE_LABEL_MODIFY."\"></A>&nbsp;&nbsp;$action</TD>
			</TR>";
	}
	?>
	</TABLE>
	</TD>
<TR>

</TABLE>
