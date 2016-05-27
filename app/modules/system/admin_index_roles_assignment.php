<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="0">
<?php

$userid = dims_load_securvalue('userid', dims_const::_DIMS_NUM_INPUT, true, false, true);
$orgid = dims_load_securvalue('orgid', dims_const::_DIMS_NUM_INPUT, true, false, true);

$params = array( ':workspaceid' => $workspaceid );

$select =	"
			SELECT 		dims_user.id,
						dims_user.lastname,
						dims_user.firstname,
						dims_user.login,
						dims_user_type.label as type

			FROM 		dims_user

			INNER JOIN	dims_workspace_user
			ON			dims_workspace_user.id_user = dims_user.id
			AND			dims_workspace_user.id_workspace = :workspaceid

			LEFT JOIN	dims_user_type
			ON			dims_user_type.id = dims_user.id_type

			ORDER BY	dims_user.lastname, dims_user.firstname, dims_user.login
			";

$result = $db->query($select, $params);

if ($db->numrows() == 0) {
	echo	"
		<TR CLASS=\"title\">
			<TD COLSPAN=\"2\">
			".$_DIMS['cste']['_DIMS_LABEL_NO_USER_DEFINED']."
			</TD>
		</TR>
		";
}
else {
	while ($fields = $db->fetchrow($result)) {
		$user = new user();
		$user->fields['id'] = $fields['id'];

		// get user roles
		$workspace_user = new workspace_user();
		$workspace_user->fields['id_user'] = $fields['id'];
		$workspace_user->fields['id_workspace'] = $workspaceid;
		$assigned_roles = $workspace_user->getroles();

		echo 	"
			<TR CLASS=\"title\">
				<TD ALIGN=\"LEFT\">&nbsp;<img src=\"{$_SESSION['dims']['template_path']}/img/system/ico_user.png\">&nbsp;$fields[lastname] $fields[firstname] ($fields[login])</TD>
				<TD ALIGN=\"RIGHT\"><A HREF=\"$scriptenv?op=assign_roles&userid=$fields[id]\">".$_DIMS['cste']['_MODIFY_ROLE_ASSIGNMENT']."</A></TD>
			</TR>
			";

		if ($op == 'assign_roles' && isset($userid) && $userid == $fields['id']) {
			$ownmodules = $workspace->getmodules();
			$listeToken = array();
			echo 	"
				<TR CLASS=\"title\">
					<TD COLSPAN=\"2\">
					<TABLE CLASS=\"Skin\" CELLPADDING=\"2\" CELLSPACING=\"0\" WIDTH=\"100%\">
					<FORM ACTION=\"$scriptenv\" METHOD=\"POST\">
					<INPUT TYPE=\"HIDDEN\" NAME=\"op\" VALUE=\"save_roles\">
					<INPUT TYPE=\"HIDDEN\" NAME=\"workspace_user_role_id_user\" VALUE=\"$userid\">
					<INPUT TYPE=\"HIDDEN\" NAME=\"workspace_user_role_id_workspace\" VALUE=\"$workspaceid\">
					<TR CLASS=\"title\">
						<TD COLSPAN=\"2\">
						".$_DIMS['cste']['_DIMS_LABEL_ROLE_LIST']."
						</TD>
					</TR>
				";
			$listeToken[] = "op";
			$listeToken[] = "workspace_user_role_id_user";
			$listeToken[] = "workspace_user_role_id_workspace";

			foreach ($ownmodules AS $index => $module) {
				$module_workspace = new module_workspace();
				$module_workspace->fields['id_module'] = $module['instanceid'];
				$module_workspace->fields['id_workspace'] = $workspaceid;
				$roles = $module_workspace->getroles();

				//dims_print_r($module);

				//getroles

				foreach ($roles as $id => $role)
				{
					if (isset($assigned_roles[$id])) $checked = 'checked';
					else $checked = '';

					echo 	"
						<TR>
							<TD WIDTH=\"1%\"><INPUT $checked NAME=\"roles[]\" VALUE=\"$role[id]\" TYPE=\"checkbox\"></TD>
							<TD><B>".$module['instancename']." (".$module['label'].")</B> - ".$role['label']."</TD>
						</TR>
						";
					$listeToken[] = "roles";

				}
			}

			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			foreach ($listeToken as $tok) {
				$token->field($tok);
			}
			$tokenHTML = $token->generate();
			echo $tokenHTML;

			echo	"
					<TR>
						<TD COLSPAN=\"2\" ALIGN=\"RIGHT\">
						<INPUT TYPE=\"Submit\" class=\"flatbutton\" VALUE=\"".$_DIMS['cste']['_DIMS_SAVE']."\">
						</TD>
					</TR>
					</FORM>
					</TABLE>
					</TD>
				</TR>
				";
		}
		else
		{
			foreach ($assigned_roles as $id => $role)
			{
				echo 	"
					<TR>
						<TD COLSPAN=\"2\"><B>".$role['modulelabel']."</B> - $role[label]</TD>
					</TR>
					";
			}
		}

		echo 	"
			<TR>
				<TD COLSPAN=\"2\"><BR></TD>
			</TR>
			";
	}
}


$params = array( ':workspaceid' => $workspaceid );
$select = 	"
			SELECT 		dims_group.id,
						dims_group.label
			FROM 		dims_group,
						dims_workspace_group
			WHERE 		dims_workspace_group.id_group = dims_group.id
			AND			dims_workspace_group.id_workspace = :workspaceid
			ORDER BY	dims_group.label
			";

$result = $db->query($select, $params);
while ($fields = $db->fetchrow($result))
{
	$workspace_group = new workspace_group();
	$workspace_group->fields['id_group'] = $fields['id'];
	$workspace_group->fields['id_workspace'] = $workspaceid;
	$assigned_roles = $workspace_group->getroles();

	echo 	"
		<TR CLASS=\"title\">
			<TD ALIGN=\"LEFT\">&nbsp;<img src=\"{$_SESSION['dims']['template_path']}/img/system/ico_group.png\">&nbsp;$fields[label]</TD>
			<TD ALIGN=\"RIGHT\"><A HREF=\"$scriptenv?op=assign_roles&orgid=$fields[id]\">".$_DIMS['cste']['_MODIFY_ROLE_ASSIGNMENT']."</A></TD>
		</TR>
		";

	if ($op == 'assign_roles' && isset($orgid) && $orgid == $fields['id'])
	{
		$ownmodules = $workspace->getmodules();
		$listeToken = array();
		echo 	"
				<TR CLASS=\"title\">
					<TD COLSPAN=\"2\">
					<TABLE CLASS=\"Skin\" CELLPADDING=\"2\" CELLSPACING=\"0\" WIDTH=\"100%\">
					<FORM ACTION=\"$scriptenv\" METHOD=\"POST\">
					<INPUT TYPE=\"HIDDEN\" NAME=\"op\" VALUE=\"save_roles\">
					<INPUT TYPE=\"HIDDEN\" NAME=\"workspace_group_role_id_group\" VALUE=\"$orgid\">
					<INPUT TYPE=\"HIDDEN\" NAME=\"workspace_group_role_id_workspace\" VALUE=\"$workspaceid\">
					<TR CLASS=\"title\">
						<TD COLSPAN=\"2\">
						".$_DIMS['cste']['_DIMS_LABEL_ROLE_LIST']."
						</TD>
					</TR>
				";
		$listeToken[] = "op";
		$listeToken[] = "workspace_group_role_id_group";
		$listeToken[] = "workspace_group_role_id_workspace";

		foreach ($ownmodules AS $index => $module)
		{
			$module_workspace = new module_workspace();
			$module_workspace->fields['id_module'] = $module['instanceid'];
			$module_workspace->fields['id_workspace'] = $workspaceid;
			$roles = $module_workspace->getroles();

			//dims_print_r($module);

			//getroles

			foreach ($roles as $id => $role)
			{
				if (isset($assigned_roles[$id])) $checked = 'checked';
				else $checked = '';

				echo 	"
					<TR>
						<TD WIDTH=\"1%\"><INPUT $checked NAME=\"roles[]\" VALUE=\"$role[id]\" TYPE=\"checkbox\"></TD>
						<TD><B>$module[instancename] ($module[label])</B> - $role[label]</TD>
					</TR>
					";
				$listeToken[] = "roles";
			}
		}

		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		foreach ($listeToken as $tok) {
			$token->field($tok);
		}
		$tokenHTML = $token->generate();
		echo $tokenHTML;

		echo	"
				<TR>
					<TD COLSPAN=\"2\" ALIGN=\"RIGHT\">
					<INPUT TYPE=\"Submit\" class=\"flatbutton\" VALUE=\"".$_DIMS['cste']['_DIMS_SAVE']."\">
					</TD>
				</TR>
				</FORM>
				</TABLE>
				</TD>
			</TR>
			";
	}
	else
	{
		foreach ($assigned_roles as $id => $role)
		{
			echo 	"
				<TR>
					<TD COLSPAN=\"2\"><B>$role[modulelabel]</B> - $role[label]</TD>
				</TR>
				";
		}
	}

	echo 	"
		<TR>
			<TD COLSPAN=\"2\"><BR></TD>
		</TR>
		";
}


?>

</TABLE>
