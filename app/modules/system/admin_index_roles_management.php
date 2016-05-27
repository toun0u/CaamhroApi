<?
// all available modules
$ownmodules = $workspace->getmodules();


?>
<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="0">
<?
if (sizeof($ownmodules) == 0) {
	echo	"
		<TR CLASS=\"title\">
			<TD COLSPAN=\"2\">
			".$_DIMS['cste']['_DIMS_LABEL_NO_MODULE_DEFINED']."
			</TD>
		</TR>
		";
}
else {

	foreach ($ownmodules AS $index => $module) {
		//if ($color==$skin->values['bgline2']) $color=$skin->values['bgline1'];
		//else $color=$skin->values['bgline2'];

		$owner = ($module['instanceworkspace'] == $workspaceid);

		$roleadd = '';
		if ($owner || !$module['adminrestricted']) $roleadd = "<A HREF=\"$scriptenv?op=add_role&moduleid=".$module['instanceid']."\">".$_DIMS['cste']['_DIMS_LABEL_ROLEADD']."</A>";

		$module_workspace = new module_workspace();
		$module_workspace->fields['id_module'] = $module['instanceid'];
		$module_workspace->fields['id_workspace'] = $workspaceid;
		$roles = $module_workspace->getroles();

		//$title = str_replace('<MODULE>',$module['instancename']." [".$module['label']."]",$_DIMS['cste']['_DIMS_LABEL_MODULE_ROLES']);
		$title =$module['instancename']." [".$module['label']."]";
		echo 	"
			<TR CLASS=\"title\">
				<TD>
				&#149; $title
				</TD>
				<TD ALIGN=\"RIGHT\">
				$roleadd
				</TD>
			</TR>
			";

		if (($op == 'add_role' || $op == 'modify_role') && $moduleid == $module['instanceid'])
		{
			?>

			<SCRIPT LANGUAGE=javascript>
			function role_validate(form)
			{
				if (dims_validatefield("<? echo $_DIMS['cste']['_DIMS_LABEL_LABEL']; ?>",form.role_label,"string"))
					return true;

				return false;
			}
			</SCRIPT>

			<?
			$module_type = new module_type();
			$module_type->open($module['id_module_type']);
			$actions = $module_type->getactions();

			$roleid=dims_load_securvalue("roleid",dims_const::_DIMS_NUM_INPUT,true,true,true);
			$role = new role();
			if (isset($roleid))
			{
				$role->open($roleid);
				$actions_checked = $role->getactions();
			}
			else
			{
				$role->fields['shared']="";
				$role->fields['label']="";
				$role->fields['description']="";
			}

			if (isset($role->fields['shared']) && $role->fields['shared']) $checked = 'checked';
			else $checked = '';

			echo 	"
				<TR>
					<TD COLSPAN=\"2\">
				";

			if (!isset($role->fields['label'])) $role->fields['label']="";
			if (!isset($role->fields['description'])) $role->fields['description']="";

			echo $skin->open_simplebloc('','100%');
			echo 	"
					<TABLE CELLPADDING=\"2\" CELLSPACING=\"1\" WIDTH=\"100%\">
					<FORM ACTION=\"$scriptenv\" METHOD=\"POST\" OnSubmit=\"javascript:return role_validate(this)\">";

			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("op");
			$token->field("roleid");
			$token->field("id_module_type");
			$token->field("role_id_module");
			$token->field("role_id_workspace");
			$token->field("role_label");
			$token->field("role_label");
			$token->field("role_shared");
			$token->field("role_description");
			$token->field("id_action");
			$tokenHTML = $token->generate();
			echo $tokenHTML;

			echo	"
					<INPUT TYPE=\"HIDDEN\" NAME=\"op\" VALUE=\"save_role\">
					<INPUT TYPE=\"HIDDEN\" NAME=\"roleid\" VALUE=\"{$role->fields['id']}\">
					<INPUT TYPE=\"HIDDEN\" NAME=\"id_module_type\" VALUE=\"$module[id_module_type]\">
					<INPUT TYPE=\"HIDDEN\" NAME=\"role_id_module\" VALUE=\"$module[instanceid]\">
					<INPUT TYPE=\"HIDDEN\" NAME=\"role_id_workspace\" VALUE=\"$workspaceid\">
					<TR>
						<TD WIDTH=\"1%\" ALIGN=\"RIGHT\" NOWRAP>".$_DIMS['cste']['_DIMS_LABEL_LABEL'].":</TD>
						<TD><INPUT TYPE=\"Text\" class=\"text\" VALUE=\"{$role->fields['label']}\" NAME=\"role_label\"></TD>
					</TR>
					<TR>
						<TD ALIGN=\"RIGHT\" NOWRAP>".$_DIMS['cste']['_DIMS_LABEL_DESCRIPTION'].":</TD>
						<TD><INPUT TYPE=\"Text\" class=\"text\" VALUE=\"{$role->fields['description']}\" NAME=\"role_description\" SIZE=\"50\"></TD>
					</TR>
					<TR>
						<TD ALIGN=\"RIGHT\" NOWRAP>".$_DIMS['cste']['_SHARE'].":</TD>
						<TD><INPUT TYPE=\"checkbox\" $checked VALUE=\"1\" NAME=\"role_shared\" ></TD>
					</TR>
					</TABLE>

					<TABLE CELLPADDING=\"2\" CELLSPACING=\"1\" WIDTH=\"100%\">
					<TR>
						<TD COLSPAN=2 CLASS=\"title\">Actions disponibles</TD>
					</TR>
				";

			if (isset($actions_checked[0])) $checked = 'checked';
			else $checked = '';
			echo 	"
				<TR>
					<TD WIDTH=\"1%\"><INPUT NAME=\"id_action[]\" $checked VALUE=\"0\" TYPE=\"checkbox\"></TD>
					<TD><B>0 - ". $_DIMS['cste']['_DIMS_LABEL_MODULE_ADMINISTRATOR'] ."</B></TD>
				</TR>
				";

			foreach ($actions as $id => $action) {
				if (isset($actions_checked[$id])) $checked = 'checked';
				else $checked = '';

				echo 	"<TR>
						<TD WIDTH=\"1%\"><INPUT NAME=\"id_action[]\" $checked VALUE=\"$action[id_action]\" TYPE=\"checkbox\"></TD>
						<TD>{$action['id_action']} - {$action['label']}</TD>
					</TR>
					";
			}

			echo 	"<TR>
						<TD COLSPAN=\"2\" ALIGN=\"RIGHT\">
						<INPUT TYPE=\"Submit\" class=\"flatbutton\" VALUE=\"".$_DIMS['cste']['_DIMS_SAVE']."\">
						</TD>
					</TR>
					</FORM>
					</TABLE>
				";


			echo $skin->close_simplebloc();
			echo 	"
					</TD>
				</TR>
				";
		}
		if (sizeof($roles)) {
			echo 	"
				<TR>
					<TD COLSPAN=\"2\">
					<TABLE WIDTH=\"100%\" CELLPADDING=\"2\" CELLSPACING=\"1\">
					<TR CLASS=\"title\" >
						<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_LABEL']."</TD>
						<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_DESCRIPTION']."</TD>
						<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_ORIGIN']."</TD>
						<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_SHARE']."</TD>
						<TD ALIGN=\"CENTER\" WIDTH=\"1%\">".$_DIMS['cste']['_LABEL_ACTION']."</TD>
					</TR>
					";

			foreach ($roles as $id => $role) {
				if ($role['id_workspace'] == $workspaceid) {
					$actions = 	"
							<A HREF=\"$scriptenv?op=modify_role&moduleid=$module[instanceid]&roleid=$id\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/btn_edit.png\" border=\"0\" alt=\"".$_DIMS['cste']['_MODIFY']."\"></A>
							&nbsp;<A HREF=\"javascript:dims_confirmlink('$scriptenv?op=delete_role&roleid=$id','".$_DIMS['cste']['_SYSTEM_MSG_CONFIRMROLEDELETE']."')\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/btn_delete.png\" alt=\"".$_DIMS['cste']['_DELETE']."\" border=\"0\"></A>
							";
				}
				else $actions = '-';

				if ($role['shared']) $shared = "<img src=\"{$_SESSION['dims']['template_path']}/img/system/p_green.png\" align=\"middle\">";
				else $shared = "<img src=\"{$_SESSION['dims']['template_path']}/img/system/p_red.png\" align=\"middle\">";

				echo 	"
						<TR>
							<TD ALIGN=\"CENTER\">$role[label]</TD>
							<TD ALIGN=\"CENTER\">$role[description]</TD>
							<TD ALIGN=\"CENTER\"><A HREF=\"$scriptenv?workspaceid=$role[id_workspace]\">$role[labelworkspace]</A></TD>
							<TD ALIGN=\"CENTER\">$shared</TD>
							<TD ALIGN=\"CENTER\" NOWRAP><A HREF=\"\">$actions</A></TD>
						</TR>
					";
			}

			echo	"
					</TABLE>
					</TD>
				</TR>
				";
		}
		else echo "<TR><TD COLSPAN=\"2\">".$_DIMS['cste']['_DIMS_LABEL_NO_ROLE_DEFINED']."</TD></TR>";
	}
}
?>
</TABLE>
