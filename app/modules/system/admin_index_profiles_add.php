<?
$ownmodules = $workspace->getmodules();

echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_GROUP_AVAILABLE_MODULES'],'100%');

$token = new FormToken\TokenField;
$token->field("op");
$token->field("profile_id");
$token->field("profile_id_workspace");
$token->field("profile_label");
$token->field("profile_description");
$token->field("select_roles");
?>

<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0">
<TR>
	<TD>

	<?
	echo 	"
		<TABLE CELLPADDING=\"2\" CELLSPACING=\"1\" WIDTH=\"100%\">
		<FORM NAME=\"form_ajout_profil\" ACTION=\"$scriptenv\" METHOD=\"POST\">

		<INPUT TYPE=\"HIDDEN\" NAME=\"op\" VALUE=\"save_profile\">
		<INPUT TYPE=\"HIDDEN\" NAME=\"profile_id\" VALUE=\"{$profile->fields['id']}\">
		<INPUT TYPE=\"HIDDEN\" NAME=\"profile_id_workspace\" VALUE=\"$workspaceid\">
		<TR>
			<TD WIDTH=\"1%\">".$_DIMS['cste']['_DIMS_LABEL_LABEL']."</TD>
			<TD><INPUT TYPE=\"Text\" class=\"text\" VALUE=\"{$profile->fields['label']}\" NAME=\"profile_label\"></TD>
		</TR>
		<TR>
			<TD>".$_DIMS['cste']['_DIMS_LABEL_DESCRIPTION']."</TD>
			<TD><INPUT TYPE=\"Text\" class=\"text\" VALUE=\"{$profile->fields['description']}\" NAME=\"profile_description\" SIZE=\"50\"></TD>
		</TR>

		</TABLE>

		<TABLE WIDTH=\"100%\" CELLPADDING=\"2\" CELLSPACING=\"1\">
		<TR CLASS=\"title\">
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_NAME']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_TYPE']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_VIEWMODE']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_ROLECHOICE']."</TD>
		</TR>
		";

	if (isset($profile->fields['id']) && $profile->fields['id']!='') {
		$valbutton=$_DIMS['cste']['_MODIFY'];
		$roles_cour=$profile->getroles();
	}
	else $valbutton = $_DIMS['cste']['_DIMS_SAVE'];

	foreach ($ownmodules AS $index => $module)
	{
		$modu = new module();
		$modu->open($module['instanceid']);

		// owner
		if ($module['instanceworkspace'] == $workspaceid)
		{
			$liste_roles="<select name=\"select_roles[{$module['instanceid']}]\">";
			$token->field($module['instanceid']);
			$liste_roles.='<option value=\"\"></option>';
			// on boucle sur les rôles dispos
			$roles=$modu->getroles();

			foreach ($roles as $id => $role)
			{
				if (isset($roles_cour[$id])) $liste_roles.="<option value=\"".$id."\" selected>".$role['label']."</option>";
				else $liste_roles.="<option value=\"".$id."\">".$role['label']."</option>";
			}
			$liste_roles.="</select>";
		}
		else
		{
			$liste_roles="<select si  name=\"select_roles[{$module['instanceid']}]\">";
			$token->field($module['instanceid']);
			$liste_roles.='<option value=\"\"></option>';
			// on boucle sur les rôles dispos
			$lstroles=$modu->getrolesshared();
			foreach($lstroles as $id => $role)
			{
				if (isset($roles_cour[$id])) $liste_roles.="<option value=\"".$id."\" selected>".$role['label']."</option>";
				else $liste_roles.="<option value=\"".$id."\">".$role['label']."</option>";
			}
			$liste_roles.="</select>";

		}

		$viewmode = $dims_viewmodes[$module['viewmode']];

		echo 	"
			<TR>
				<TD ALIGN=\"CENTER\">$module[label]</TD>
				<TD ALIGN=\"CENTER\">".dims_strcut($module['instancename'],15)."</TD>
				<TD ALIGN=\"CENTER\">$viewmode</TD>
				<TD ALIGN=\"RIGHT\">$liste_roles</TD>
			</TR>
			";

	}

	echo 	"
			<TR>
				<TD COLSPAN=\"4\" ALIGN=\"RIGHT\">
					".dims_create_button($valbutton,"disk","form_ajout_profil.submit();")."
				</TD>
			</TR>";

		     echo $token->generate();
			echo "</FORM>
			</TABLE>
		";

	?>
	</TD>
</TR>
</TABLE>
<?
echo $skin->close_simplebloc();
?>
