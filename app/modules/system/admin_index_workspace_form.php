<FORM NAME="form_modify_group" ACTION="<? echo $scriptenv ?>" METHOD="POST">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op",		"save_group");
	$token->field("org_id",	$org_id);
	$token->field("workspacegroup_adminlevel");
	$token->field("workspacegroup_id_profile");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<INPUT TYPE="HIDDEN" NAME="op" VALUE="save_group">
<INPUT TYPE="HIDDEN" NAME="org_id" VALUE="<? echo $org_id; ?>">
<TABLE CELLPADDING="2" CELLSPACING="1" ALIGN="CENTER">
<TR>
	<TD ALIGN=RIGHT><? echo $_DIMS['cste']['_DIMS_LABEL_LEVEL']; ?>:&nbsp;</TD>
	<TD ALIGN=LEFT>
	<SELECT class="select" NAME="workspacegroup_adminlevel">
	<?
	foreach ($dims_system_levels as $id => $label) {
		if ($id <= $_SESSION['dims']['adminlevel']) {
			$sel = ($workspace_group->fields['adminlevel'] == $id) ? 'selected' : '';
			echo "<option $sel value=\"$id\">$label</option>";
		}
	}
	?>
	</SELECT>
	</TD>
</TR>
<TR>
	<TD ALIGN=RIGHT><? echo $_DIMS['cste']['_DIMS_LABEL_USER_PROFILE']; ?>:&nbsp;</TD>
	<TD ALIGN=LEFT>
		<SELECT class="select" NAME="workspacegroup_id_profile">
		<?
		$ownprofiles = $group->getprofiles();

		echo "<option value=\"-1\" >".$_DIMS['cste']['_DIMS_LABEL_UNDEFINED']."</option>";

		foreach ($ownprofiles as $id => $profile) {
			$sel = ($workspace_group->fields['id_profile'] == $profile['id']) ? 'selected' : '';
			echo "<option $sel value=\"{$id}\">{$profile['label']}</option>";
		}
		?>
	</SELECT>
	</TD>
</TR>

<TR>
	<TD ALIGN=RIGHT COLSPAN=2>
		<INPUT TYPE="Submit" class="flatbutton" VALUE="<? echo $_DIMS['cste']['_DIMS_SAVE']; ?>">
	</TD>
</TR>
</TABLE>
</FORM>