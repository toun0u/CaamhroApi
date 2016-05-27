<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="0">
<?
$ownmodules = $workspace->getmodules();

if (sizeof($ownmodules) == 0)
{
	echo	"
		<TR CLASS=\"title\">
			<TD COLSPAN=\"2\">
			".$_DIMS['cste']['_DIMS_LABEL_NO_ROLE_DEFINED']."
			</TD>
		</TR>
		";
}
else
{
	$listeToken = array();
	echo 	"
		<FORM ACTION=\"$scriptenv\" METHOD=\"POST\">
		<INPUT TYPE=\"HIDDEN\" NAME=\"op\" VALUE=\"save_roles\">
		<INPUT TYPE=\"HIDDEN\" NAME=\"workspace_user_role_id_user\" VALUE=\"-1\">
		<INPUT TYPE=\"HIDDEN\" NAME=\"workspace_user_role_id_workspace\" VALUE=\"$workspaceid\">
		<INPUT TYPE=\"HIDDEN\" NAME=\"workspace_workspace_role_id_group\" VALUE=\"-1\">
		<INPUT TYPE=\"HIDDEN\" NAME=\"workspace_workspace_role_id_workspace\" VALUE=\"$workspaceid\">
		<TR CLASS=\"title\">
			<TD COLSPAN=\"2\">
			".$_DIMS['cste']['_DIMS_LABEL_ROLE_LIST']."
			</TD>
		</TR>
		";

	$listeToken[] = "op";
	$listeToken[] = "workspace_user_role_id_user";
	$listeToken[] = "workspace_user_role_id_workspace";
	$listeToken[] = "workspace_workspace_role_id_group";
	$listeToken[] = "workspace_workspace_role_id_workspace";

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
			<INPUT TYPE=\"Submit\" class=\"flatbutton\" VALUE=\"".$_DIMS['cste']['_DIMS_LABEL_ASSIGN']."\">
			</TD>
		</TR>
		</FORM>
		";
}
?>
</TABLE>
