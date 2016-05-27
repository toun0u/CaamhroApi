<?
// all available modules
$installedmodules = system_getinstalledmodules();
// shared modules
$sharedmodules = $group->getsharedmodules();
// own modules
$ownmodules = $group->getmodules();

echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_SYSTEM_AVAILABLE_MODULES'],'100%');

?>

<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0">
<TR>
	<TD>
	<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1">
	<?
	$color=$skin->values['bgline1'];
	echo 	"
		<TR CLASS=\"Title\" BGCOLOR=\"".$color."\">
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_MODULEPOSITION']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_TYPE']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_NAME']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_VIEWMODE']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_ACTIVE']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_VISIBLE']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_SHARE']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_HERITED']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_LABEL_ACTION']."</TD>
		</TR>
		";

	foreach ($ownmodules AS $index => $module) {
		if ($color==$skin->values['bgline2']) $color=$skin->values['bgline1'];
		else $color=$skin->values['bgline2'];

		//$active = $public = $shared = '<img src="./common/modules/system/img/ico_no.gif" width="16" height="16" align="middle">';
		$visible = $active = $public = $shared = $herited = $autoconnect = '<img border="0" src="./common/modules/system/img/ico_point_red.gif" align="middle">';

		if ($module['visible']) {
			$herited = '<img border="0" src="./common/modules/system/img/ico_point_green.gif" align="middle">';
		}

		if ($module['active']) {
			$active = '<img border="0" src="./common/modules/system/img/ico_point_green.gif" align="middle">';
		}

		if ($module['public']) {
			$public = '<img border="0" src="./common/modules/system/img/ico_point_green.gif" align="middle">';
		}

		if ($module['shared']) {
			$shared = '<img border="0" src="./common/modules/system/img/ico_point_green.gif" align="middle">';
		}

		if ($module['herited']) {
			$herited = '<img border="0" src="./common/modules/system/img/ico_point_green.gif" align="middle">';
		}

		// owner
		if ($module['instanceworkspace'] == $groupid)
		{
			$modify =  "<a href=\"$scriptenv?tab=modules&op=modify&moduleid={$module['instanceid']}#modify\"><img src=\"./common/modules/system/img/crayon.gif\" align=\"middle\" border=\"0\"></a>" ;
			$delete = "<a href=\"javascript:dims_confirmlink('$scriptenv?op=delete&moduleid={$module['instanceid']}','".$_DIMS['cste']['_SYSTEM_MSG_CONFIRMMODULEDELETE']."')\">" . '<img src="./common/modules/system/img/ico_delete.gif" align="middle" border="0\"></a>';
		}
		else
		{
			$modify = '<img src="./common/modules/system/img/ico_noway.gif" align="middle" border="0">';
			if ($module['adminrestricted']) $delete = '<img src="./common/modules/system/img/ico_noway.gif" align="middle" border="0">';
			else $delete = "<a href=\"javascript:dims_confirmlink('$scriptenv?op=unlinkinstance&moduleid={$module['instanceid']}','".$_DIMS['cste']['_SYSTEM_MSG_CONFIRMMODULEDETACH']."')\">" . '<img src="./common/modules/system/img/ico_cut.gif" align="middle" border="0\"></a>';
		}

		if ($module['blockposition'] == 'left')
			$blockposition = "<td>&nbsp;</td><td><a href=\"$scriptenv?tab=modules&op=moveright&moduleid={$module['instanceid']}\"><IMG BORDER=0 SRC=\"".$skin->get_image('arrow_left')."\"></a></td>";
		else // right
			$blockposition = "<td>&nbsp;</td><td><a href=\"$scriptenv?tab=modules&op=moveleft&moduleid={$module['instanceid']}\"><IMG BORDER=0 SRC=\"".$skin->get_image('arrow_right')."\"></a></td>";

		$updown = 	"
				<table border=\"0\" cellspacing=\"1\" cellpadding=\"1\">
			  	<tr align=\"center\" valign=\"middle\">
			    		<td><a href=\"$scriptenv?tab=modules&op=movedown&moduleid={$module['instanceid']}\"><IMG BORDER=0 SRC=\"".$skin->get_image('arrow_bottom')."\"></a></td>
			    		<td><a href=\"$scriptenv?tab=modules&op=moveup&moduleid={$module['instanceid']}\"><IMG BORDER=0 SRC=\"".$skin->get_image('arrow_top')."\"></a></td>
			  	</tr>
				</table>
				";

		$viewmode = $dims_viewmodes[$module['viewmode']];

		if ($module['transverseview']) $viewmode .= ' '.$_DIMS['cste']['_DIMS_LABEL_TRANSVERSE'];

		echo 	"
			<TR BGCOLOR=\"".$color."\">
				<TD ALIGN=\"CENTER\">$updown</TD>
				<TD ALIGN=\"CENTER\">$module[label]</TD>
				<TD ALIGN=\"CENTER\">".dims_strcut($module['instancename'],15)."</TD>
				<TD ALIGN=\"CENTER\">$viewmode</TD>
				";

		if ($module['instanceworkspace'] == $groupid)
		{
			echo "
				<TD ALIGN=\"CENTER\"><a href=\"?op=switch_active&moduleid={$module['instanceid']}\">$active</a></TD>
				<TD ALIGN=\"CENTER\"><a href=\"?op=switch_public&moduleid={$module['instanceid']}\">$public</a></TD>
				<TD ALIGN=\"CENTER\"><a href=\"?op=switch_shared&moduleid={$module['instanceid']}\">$shared</a></TD>
				<TD ALIGN=\"CENTER\"><a href=\"?op=switch_herited&moduleid={$module['instanceid']}\">$herited</a></TD>
				";
		}
		else
		{
			echo "
				<TD ALIGN=\"CENTER\">$active</TD>
				<TD ALIGN=\"CENTER\">$public</TD>
				<TD ALIGN=\"CENTER\">$shared</TD>
				<TD ALIGN=\"CENTER\">$herited</TD>
				";
		}

		echo 	"

				<TD ALIGN=\"CENTER\">$modify&nbsp;&nbsp;$delete</TD>
			</TR>
			";

	}
	?>
	</TABLE>
	</TD>
</TR>
</TABLE>

<?

if ($op == 'modify')
{
	$module = new module();
	$module->open($moduleid);

	echo $skin->close_simplebloc();
	echo '<BR><A NAME="modify">';
	echo $skin->open_simplebloc(str_replace('<MODULE>','<U>'.$module->fields['label'].'</U>',$_DIMS['cste']['_MODULE_PROPERTIES']),'100%');

	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op",			"save_module_props");
	$token->field("moduleid",	$module->fields['id']);
	$token->field("module_label");
	$token->field("module_active");
	$token->field("module_public");
	$token->field("module_autoconnect");
	$token->field("module_shared");
	$token->field("module_herited");
	$token->field("module_adminrestricted");
	$token->field("module_transverseview");
	$tokenHTML = $token->generate();

	?>
	<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0">
	<TR BGCOLOR="<? echo $skin->values['bgline1']; ?>">
		<TD ALIGN="CENTER">
		<FORM NAME="form_modify_module" ACTION="<? echo $scriptenv; ?>" METHOD="POST">
		<? echo $tokenHTML; ?>
		<INPUT TYPE="HIDDEN" NAME="op" VALUE="save_module_props">
		<INPUT TYPE="HIDDEN" NAME="moduleid" VALUE="<? echo $module->fields['id']; ?>">
		<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1">
		<TR BGCOLOR="<? echo $skin->values['bgline2']; ?>">
			<TD ALIGN="RIGHT"><B><? echo $_DIMS['cste']['_DIMS_LABEL_NAME']; ?>:&nbsp;</B></TD>
			<TD ALIGN="LEFT"><INPUT class="text" TYPE="Text" SIZE=30 MAXLENGTH=100 NAME="module_label" VALUE="<? echo $module->fields['label']; ?>"></TD>
			<TD ALIGN="LEFT"><I><? echo $_DIMS['cste']['_SYSTEM_EXPLAIN_MODULENAME']; ?></I></TD>
		</TR>
		<TR BGCOLOR="<? echo $skin->values['bgline1']; ?>">
			<TD ALIGN="RIGHT"><B><? echo $_DIMS['cste']['_DIMS_LABEL_ACTIVE']; ?>:&nbsp;</B></TD>
			<TD>
			<INPUT TYPE="Radio" <? if ($module->fields['active']) echo "checked" ?> VALUE="1" NAME="module_active"><? echo $_DIMS['cste']['_DIMS_YES']; ?>
			<INPUT TYPE="Radio" <? if (!$module->fields['active']) echo "checked" ?> VALUE="0" NAME="module_active"><? echo $_DIMS['cste']['_DIMS_NO']; ?>
			<TD ALIGN="LEFT"><I><? echo $_DIMS['cste']['_SYSTEM_EXPLAIN_ACTIVE']; ?></I></TD>
		</TR>
		<TR BGCOLOR="<? echo $skin->values['bgline2']; ?>">
			<TD ALIGN="RIGHT"><B><? echo $_DIMS['cste']['_DIMS_LABEL_PUBLIC']; ?>:&nbsp;</B></TD>
			<TD>
			<INPUT TYPE="Radio" <? if ($module->fields['public']) echo "checked" ?> VALUE="1" NAME="module_public"><? echo $_DIMS['cste']['_DIMS_YES']; ?>
			<INPUT TYPE="Radio" <? if (!$module->fields['public']) echo "checked" ?> VALUE="0" NAME="module_public"><? echo $_DIMS['cste']['_DIMS_NO']; ?>
			<TD ALIGN="LEFT"><I><? echo $_DIMS['cste']['_SYSTEM_EXPLAIN_PUBLIC']; ?></I></TD>
		</TR>
		<TR BGCOLOR="<? echo $skin->values['bgline1']; ?>">
			<TD ALIGN="RIGHT"><B><? echo $_DIMS['cste']['_DIMS_LABEL_AUTOCONNECT']; ?>:&nbsp;</B></TD>
			<TD>
			<INPUT TYPE="Radio" <? if ($module->fields['autoconnect']) echo "checked" ?> VALUE="1" NAME="module_autoconnect"><? echo $_DIMS['cste']['_DIMS_YES']; ?>
			<INPUT TYPE="Radio" <? if (!$module->fields['autoconnect']) echo "checked" ?> VALUE="0" NAME="module_autoconnect"><? echo $_DIMS['cste']['_DIMS_NO']; ?>
			</TD>
			<TD ALIGN="LEFT"><I><? echo $_DIMS['cste']['_SYSTEM_EXPLAIN_AUTOCONNECT']; ?></I></TD>
		</TR>
		<TR BGCOLOR="<? echo $skin->values['bgline2']; ?>">
			<TD ALIGN="RIGHT"><B><? echo $_DIMS['cste']['_WORKSPACE']; ?>:&nbsp;</B></TD>
			<TD>
			<INPUT TYPE="Radio" <? if ($module->fields['shared']) echo "checked" ?> VALUE="1" NAME="module_shared"><? echo $_DIMS['cste']['_DIMS_YES']; ?>
			<INPUT TYPE="Radio" <? if (!$module->fields['shared']) echo "checked" ?> VALUE="0" NAME="module_shared"><? echo $_DIMS['cste']['_DIMS_NO']; ?>
			</TD>
			<TD ALIGN="LEFT"><I><? echo $_DIMS['cste']['_SYSTEM_EXPLAIN_SHARED']; ?></I></TD>
		</TR>
		<TR BGCOLOR="<? echo $skin->values['bgline1']; ?>">
			<TD ALIGN="RIGHT"><B><? echo $_DIMS['cste']['_DIMS_LABEL_HERITED']; ?>:&nbsp;</B></TD>
			<TD>
			<INPUT TYPE="Radio" <? if ($module->fields['herited']) echo "checked" ?> VALUE="1" NAME="module_herited"><? echo $_DIMS['cste']['_DIMS_YES']; ?>
			<INPUT TYPE="Radio" <? if (!$module->fields['herited']) echo "checked" ?> VALUE="0" NAME="module_herited"><? echo $_DIMS['cste']['_DIMS_NO']; ?>
			</TD>
			<TD ALIGN="LEFT"><I><? echo $_DIMS['cste']['_SYSTEM_EXPLAIN_HERITED']; ?></I></TD>
		</TR>
		<TR BGCOLOR="<? echo $skin->values['bgline2']; ?>">
			<TD ALIGN="RIGHT"><B><? echo $_DIMS['cste']['_DIMS_LABEL_ADMINRESTRICTED']; ?>:&nbsp;</B></TD>
			<TD>
			<INPUT TYPE="Radio" <? if ($module->fields['adminrestricted']) echo "checked" ?> VALUE="1" NAME="module_adminrestricted"><? echo $_DIMS['cste']['_DIMS_YES']; ?>
			<INPUT TYPE="Radio" <? if (!$module->fields['adminrestricted']) echo "checked" ?> VALUE="0" NAME="module_adminrestricted"><? echo $_DIMS['cste']['_DIMS_NO']; ?>
			</TD>
			<TD ALIGN="LEFT"><I><? echo _SYSTEM_EXPLAIN_ADMINRESTRICTED; ?></I></TD>
		</TR>
		<TR BGCOLOR="<? echo $skin->values['bgline1']; ?>">
			<TD ALIGN="RIGHT"><B><? echo $_DIMS['cste']['_DIMS_LABEL_VIEWMODE']; ?>:&nbsp;</B></TD>
			<TD>
			<TABLE CELLPADDING=0 CELLSPACING=0>
			<TR>
				<TD VALIGN="MIDDLE">
				<SELECT class="select" NAME="module_viewmode">
				<?
				foreach($dims_viewmodes as $id => $viewmode)
				{
					if ($module->fields['viewmode'] == $id) $sel = 'selected';
					else $sel = '';
					echo "<OPTION $sel VALUE=\"$id\">$viewmode</OPTION>";
				}
				?>
				</SELECT>
				</TD>
				<TD VALIGN="MIDDLE" NOWRAP>&nbsp;<INPUT TYPE="Checkbox" <? if ($module->fields['transverseview']) echo "checked" ?> VALUE="1" NAME="module_transverseview"></TD>
				<TD VALIGN="MIDDLE"><? echo $_DIMS['cste']['_DIMS_LABEL_TRANSVERSE']; ?></TD>
			</TR>
			</TABLE>
			</TD>
			<TD ALIGN="LEFT"><I><? echo _SYSTEM_EXPLAIN_VIEWMODE; ?></I></TD>
		</TR>
		<TR BGCOLOR="<? echo $skin->values['bgline2']; ?>">
			<TD ALIGN="RIGHT" COLSPAN="3">
				<INPUT TYPE="Submit" class="flatbutton" VALUE="<? echo $_DIMS['cste']['_DIMS_SAVE']; ?>">
			</TD>
		</TR>
		</TABLE>
		</FORM>
		</TD>
	</TR>
	</TABLE>

	<?
	echo $skin->close_simplebloc();
	echo '<BR>';
	echo $skin->open_simplebloc(str_replace('<MODULE>','<U>'.$module->fields['label'].'</U>',$_DIMS['cste']['_MODULE_PARAMS']),'100%');
	?>

	<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0">
	<TR BGCOLOR="<? echo $skin->values['bgline1']; ?>">
		<TD ALIGN="CENTER">
		<TABLE CELLPADDING="2" CELLSPACING="1">

		<?
		$param_module = new param();
		$param_module->open($moduleid);

		if (isset($param_module->tabparam)) {
			echo 	"
				<FORM  ACTION=\"$scriptenv\" METHOD=\"POST\">
				<INPUT TYPE=\"HIDDEN\" NAME=\"op\" VALUE=\"save_module_params\">
				<INPUT TYPE=\"HIDDEN\" NAME=\"moduleid\" VALUE=\"$moduleid\">
				";

			reset($param_module->tabparam);
			while (list($key,$val)=each($param_module->tabparam)) {
				// Search default values for this type of param
				$select = "SELECT * FROM dims_param_choice WHERE id_param_type = :idparamtype ";
				$answer = $db->query($select, array(':idparamtype' => $param_module->tabparamdet[$key]['id']) );
				$options = '';
				while ($champs = $db->fetchrow($answer)) {
					$sel='';

					if (isset($champs['displayed_value']) && $champs['displayed_value'] != '') $dispval = $champs['displayed_value'];
					else $dispval = $champs['value'];

					if ($champs['value']==$param_module->tabparam[$key]) $sel='selected';
					$options=$options."<OPTION $sel VALUE=\"$champs[value]\">$dispval</OPTION>";
				}

				// select label to display
				if (isset($param_module->tabparamdet[$key]['displayed_label'])) $displabel = $param_module->tabparamdet[$key]['displayed_label'];
				else $displabel = $key;

				if ($options!='')
				{
					echo 	"
						<TR>
							<TD ALIGN=RIGHT>$displabel:&nbsp;</TD>
							<TD><SELECT class=\"select\" NAME=\"$key\">$options</SELECT></TD>
						</TR>
						";
				}
				else
				{
					echo
						"
						<TR>
							<TD ALIGN=RIGHT>$displabel:&nbsp;</TD>
							<TD><INPUT class=\"text\" TYPE=\"TEXT\" NAME=\"$key\" VALUE=\"".htmlspecialchars($val)."\" SIZE=50></TD>
						</TR>
						";
				}
			}

			echo 	"
				<TR>
				<TD COLSPAN=2 ALIGN=RIGHT>
					<INPUT class=\"flatbutton\" TYPE=SUBMIT VALUE=\"".$_DIMS['cste']['_DIMS_SAVE']."\">
				</TD>
				</TR>
				</FORM>
				";
		}
		else echo '&nbsp;'.$_DIMS['cste']['_DIMS_LABEL_NOMODULEPARAM'];
		?>
		</TABLE>
		<?

		/*
		$advancedparamsfile = "./common/modules/$moduletype/param.php";
		if (file_exists($advancedparamsfile))
		{
			include($advancedparamsfile);
		}
		*/
		?>
		</TD>
	</TR>
	</TABLE>
	<?
}
?>

<?
echo $skin->close_simplebloc();
echo "<br />Les modules instanci�s au niveau 'Syst�me' sont uniquement visibles depuis l'espace 'Accueil' en mode connect� ou non.<br />Un module n'est disponible en mode d�connect� que s'il est d�fini comme 'public'.<br /><br />";
echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_SYSTEM_USABLE_MODULES'],'100%');
?>

<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0">
<TR>
	<TD>
	<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1">
	<?
	$color=$skin->values['bgline1'];
	echo 	"
		<TR CLASS=\"Title\" BGCOLOR=\"".$color."\">
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_TYPE']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_DESCRIPTION']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_LABEL_ACTION']."</TD>
		</TR>
		";



	  foreach ($sharedmodules AS $instanceid => $instance)
	  {
		if (!array_key_exists($instanceid,$ownmodules))
		{
			if ($color==$skin->values['bgline2']) $color=$skin->values['bgline1'];
			else $color=$skin->values['bgline2'];

			echo 	"
				<TR BGCOLOR=\"".$color."\">
					<TD ALIGN=\"CENTER\">$instance[label]</TD>
					<TD ALIGN=\"CENTER\">$instance[description]</TD>
					<TD ALIGN=\"CENTER\"><A HREF=\"$scriptenv?op=add&instance=SHARED,$groupid,$instanceid\">utiliser</A></TD>
				</TR>
				";
	  	}
	  	//echo "<option value=\"SHARED,$groupID,$instanceId\" class=\"listParentItem\">$instanceName</option>";
	  }

	  foreach ($installedmodules AS $index => $moduletype)
	  {
		if ($color==$skin->values['bgline2']) $color=$skin->values['bgline1'];
		else $color=$skin->values['bgline2'];

		echo 	"
			<TR BGCOLOR=\"".$color."\">
				<TD ALIGN=\"CENTER\">$moduletype[label]</TD>
				<TD ALIGN=\"CENTER\">$moduletype[description]</TD>
				<TD ALIGN=\"CENTER\"><A HREF=\"$scriptenv?op=add&instance=NEW,$groupid,$moduletype[id]\">instancier</A></TD>
			</TR>
			";
	  	// Objet temporaire
		// $obj = NEW DIMS_MODULE($db->connection_id,$moduletype['instanceid']);
		// $moduleLabel = $obj->adminGetProperty('moduleLabel');
	  	//echo "<option value=\"NEW,$groupID,{$moduletype['id']}\">{$moduletype['label']}</option>";
	  }
	?>
	</TABLE>
	</TD>
</TR>
</TABLE>

<? echo $skin->close_simplebloc(); ?>
