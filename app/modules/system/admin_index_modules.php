<script language="javascript">
function showSharedMod() {
		dims_xmlhttprequest_todiv('admin.php','op=refreshsharedmodules','','detailcontentsharedmod');
}
</script>

<?php
require_once DIMS_APP_PATH . '/include/functions/shares.php';

// all available modules
$installedmodules = system_getinstalledmodules();

// own modules
$ownmodules = $workspace->getmodules();
//dims_print_r($ownmodules);die();
$_SESSION['dims']['current']['workspaceid']=$workspace->fields['id'];

echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_GROUP_AVAILABLE_MODULES'],'100%');
?>

<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0">
<TR>
	<TD>
	<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1">
	<?php
	$color='';
	echo	"
		<TR CLASS=\"title\">
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_MODULEPOSITION']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_TYPE']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_NAME']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_VIEWMODE']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_ACTIVE']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_VISIBLE']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_AUTOCONNECT']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_ACCES']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_SHARE']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_HERITED']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_LABEL_ACTION']."</TD>
		</TR>
		";

	foreach ($ownmodules AS $index => $module) {
		$visible = $active = $public = $shared = $herited = $autoconnect = "<img border=\"0\" src=\"{$_SESSION['dims']['template_path']}/img/system/p_red.png\" align=\"middle\">";

		$p_green = "<img border=\"0\" src=\"{$_SESSION['dims']['template_path']}/img/system/p_green.png\" align=\"middle\">";

		if ($module['visible']) $visible = $p_green;
		if ($module['active']) $active = $p_green;
		if ($module['public']) $public = $p_green;
		if ($module['shared']) $shared = $p_green;
		if ($module['herited']) $herited = $p_green;
		if ($module['autoconnect']) $autoconnect = $p_green;

		$lstrules=dims_shares_get(0,dims_const::_SYSTEM_OBJECT_GROUP,$module['instanceid'],dims_const::_DIMS_MODULE_SYSTEM,0);

		if (sizeof($lstrules)>0)
			$rules=sizeof($lstrules);
		else
			$rules=$_DIMS['cste']['_DIMS_LABEL_UNDEFINED'];

		// owner
		if ($module['instanceworkspace'] == $workspaceid) {
			$modify =  "<a href=\"$scriptenv?tab=modules&op=modify&moduleid={$module['instanceid']}#modify\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/btn_edit.png\" align=\"middle\" border=\"0\"></a>" ;
			$delete = "<a href=\"javascript:dims_confirmlink('$scriptenv?op=delete&moduleid={$module['instanceid']}','".$_DIMS['cste']['_SYSTEM_MSG_CONFIRMMODULEDELETE']."')\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/btn_delete.png\" align=\"middle\" border=\"0\"></a>";
		}
		else {
			$modify = "<img src=\"{$_SESSION['dims']['template_path']}/img/system/btn_noway.png\" align=\"middle\" border=\"0\">";
			//if ($module['adminrestricted']) $delete = '<img src="./common/modules/system/img/ico_noway.gif" align="middle" border="0">';
			$delete = "<a href=\"javascript:dims_confirmlink('$scriptenv?op=unlinkinstance&moduleid={$module['instanceid']}','".$_DIMS['cste']['_SYSTEM_MSG_CONFIRMMODULEDETACH']."')\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/btn_cut.png\" align=\"middle\" border=\"0\"></a>";
		}

		$updown =	"
				<table border=\"0\" cellspacing=\"1\" cellpadding=\"1\">
				<tr align=\"center\" valign=\"middle\">
						<td><a href=\"$scriptenv?op=movedown&moduleid={$module['instanceid']}\"><img border=\"0\" src=\"{$_SESSION['dims']['template_path']}/img/system/arrow_down.png\"></a></td>
						<td><a href=\"$scriptenv?op=moveup&moduleid={$module['instanceid']}\"><img border=\"0\" src=\"{$_SESSION['dims']['template_path']}/img/system/arrow_up.png\"></a></td>
				</tr>
				</table>
				";

		$viewmode = $dims_viewmodes[$module['viewmode']];

		if ($module['transverseview']) $viewmode .= ' '.$_DIMS['cste']['_DIMS_LABEL_TRANSVERSE'];

		echo	"
			<TR>
				<TD ALIGN=\"CENTER\">$updown</TD>
				<TD ALIGN=\"CENTER\">".$module['label']."</TD>
				<TD ALIGN=\"CENTER\">".dims_strcut($module['instancename'],35)."</TD>
				<TD ALIGN=\"CENTER\"><div id=\"adminviewmod_".$module['instanceid']."\">$viewmode</div></TD>
				";

		if ($module['instanceworkspace'] == $workspaceid) {
			echo "
				<TD ALIGN=\"CENTER\"><a href=\"?op=switch_active&moduleid={$module['instanceid']}\">$active</a></TD>
				<TD ALIGN=\"CENTER\"><a href=\"?op=switch_visible&moduleid={$module['instanceid']}\">$visible</a></TD>
				<TD ALIGN=\"CENTER\"><a href=\"?op=switch_autoconnect&moduleid={$module['instanceid']}\">$autoconnect</a></TD>
				<TD ALIGN=\"CENTER\"><div id=\"adminmod_".$module['instanceid']."\" onmouseover=\"javascript:this.style.cursor='pointer';\" onclick=\"displaySharesModules(event,".dims_const::_SYSTEM_OBJECT_GROUP.",".$module['instanceid'].",".dims_const::_DIMS_MODULE_SYSTEM.");\">$rules</div></TD>
				<TD ALIGN=\"CENTER\"><a href=\"?op=switch_shared&moduleid={$module['instanceid']}\">$shared</a></TD>
				<TD ALIGN=\"CENTER\"><a href=\"?op=switch_herited&moduleid={$module['instanceid']}\">$herited</a></TD>
				";
		}
		else {
			echo "
				<TD ALIGN=\"CENTER\"><a href=\"?op=switch_active&moduleid={$module['instanceid']}\">$active</a></TD>
				<TD ALIGN=\"CENTER\"><a href=\"?op=switch_visible&moduleid={$module['instanceid']}\">$visible</a></TD>
				<TD ALIGN=\"CENTER\"><a href=\"?op=switch_autoconnect&moduleid={$module['instanceid']}\">$autoconnect</a></TD>
				<TD ALIGN=\"CENTER\"></TD>
				<TD ALIGN=\"CENTER\">$shared</TD>
				<TD ALIGN=\"CENTER\">$herited</TD>
				";
		}

		echo	"

				<TD ALIGN=\"CENTER\">$modify&nbsp;&nbsp;$delete</TD>
			</TR>
			";

	}
	?>
	</TABLE>
	</TD>
</TR>
</TABLE>

<?php
$moduleid=dims_load_securvalue('moduleid',dims_const::_DIMS_NUM_INPUT,true,false,false);


if ($op == 'modify' && $moduleid>0) {
	$module = new module();
	$module->open($moduleid);
	$workspaceid=$module->fields['id_workspace'];

	echo '<A NAME="modify">';
	echo $skin->open_simplebloc(str_replace('<MODULE>','<U>'.$module->fields['label'].'</U>',$_DIMS['cste']['_DIMS_PROPERTIES']),'100%');

	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op",			"save_module_props");
	$token->field("moduleid",	$module->fields['id']);
	$token->field("module_label");
	$token->field("module_active");
	$token->field("moduleworkspace_visible");
	$token->field("module_autoconnect");
	$token->field("module_shared");
	$token->field("module_herited");
	$token->field("module_adminrestricted");
	$token->field("module_viewmode");
	$token->field("module_transverseview");
	$tokenHTML = $token->generate();

	?>
	<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0">
	<TR>
		<TD ALIGN="CENTER">
		<FORM NAME="form_modify_module" ACTION="<?php echo $scriptenv; ?>" METHOD="POST">
		<? echo $tokenHTML; ?>
		<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1">
		<INPUT TYPE="HIDDEN" NAME="op" VALUE="save_module_props">
		<INPUT TYPE="HIDDEN" NAME="moduleid" VALUE="<?php echo $module->fields['id']; ?>">
		<TR>
			<TD ALIGN="RIGHT"><B><?php echo $_DIMS['cste']['_DIMS_LABEL_NAME']; ?>:&nbsp;</B></TD>
			<TD ALIGN="LEFT"><INPUT class="text" TYPE="Text" SIZE=30 MAXLENGTH=100 NAME="module_label" VALUE="<?php echo $module->fields['label']; ?>"></TD>
			<TD ALIGN="LEFT"><I><?php echo $_DIMS['cste']['_SYSTEM_EXPLAIN_MODULENAME']; ?></I></TD>
		</TR>
		<TR>
			<TD ALIGN="RIGHT"><B><?php echo $_DIMS['cste']['_DIMS_LABEL_ACTIVE']; ?>:&nbsp;</B></TD>
			<TD>
			<INPUT TYPE="Radio" <?php if ($module->fields['active']) echo "checked" ?> VALUE="1" NAME="module_active"><?php echo $_DIMS['cste']['_DIMS_YES']; ?>
			<INPUT TYPE="Radio" <?php if (!$module->fields['active']) echo "checked" ?> VALUE="0" NAME="module_active"><?php echo $_DIMS['cste']['_DIMS_NO']; ?>
			<TD ALIGN="LEFT"><I><?php echo $_DIMS['cste']['_SYSTEM_EXPLAIN_ACTIVE']; ?></I></TD>
		</TR>
		<?php
			$module_workspace = new module_workspace();
			$module_workspace->open($workspaceid,$module->fields['id']);
		?>
		<TR>
			<TD ALIGN="RIGHT"><B><?php echo $_DIMS['cste']['_DIMS_LABEL_VISIBLE']; ?>:&nbsp;</B></TD>
			<TD>
			<INPUT TYPE="Radio" <?php if ($module_workspace->fields['visible']) echo "checked" ?> VALUE="1" NAME="moduleworkspace_visible"><?php echo $_DIMS['cste']['_DIMS_YES']; ?>
			<INPUT TYPE="Radio" <?php if (!$module_workspace->fields['visible']) echo "checked" ?> VALUE="0" NAME="moduleworkspace_visible"><?php echo $_DIMS['cste']['_DIMS_NO']; ?>
			<TD ALIGN="LEFT"><I><?php echo $_DIMS['cste']['_SYSTEM_EXPLAIN_VISIBLE']; ?></I></TD>
		</TR>
		<TR>
			<TD ALIGN="RIGHT"><B><?php echo $_DIMS['cste']['_DIMS_LABEL_AUTOCONNECT']; ?>:&nbsp;</B></TD>
			<TD>
			<INPUT TYPE="Radio" <?php if ($module->fields['autoconnect']) echo "checked" ?> VALUE="1" NAME="module_autoconnect"><?php echo $_DIMS['cste']['_DIMS_YES']; ?>
			<INPUT TYPE="Radio" <?php if (!$module->fields['autoconnect']) echo "checked" ?> VALUE="0" NAME="module_autoconnect"><?php echo $_DIMS['cste']['_DIMS_NO']; ?>
			</TD>
			<TD ALIGN="LEFT"><I><?php echo $_DIMS['cste']['_SYSTEM_EXPLAIN_AUTOCONNECT']; ?></I></TD>
		</TR>
		<TR>
			<TD ALIGN="RIGHT"><B><?php echo $_DIMS['cste']['_WORKSPACE']; ?>:&nbsp;</B></TD>
			<TD>
			<INPUT TYPE="Radio" <?php if ($module->fields['shared']) echo "checked" ?> VALUE="1" NAME="module_shared"><?php echo $_DIMS['cste']['_DIMS_YES']; ?>
			<INPUT TYPE="Radio" <?php if (!$module->fields['shared']) echo "checked" ?> VALUE="0" NAME="module_shared"><?php echo $_DIMS['cste']['_DIMS_NO']; ?>
			</TD>
			<TD ALIGN="LEFT"><I><?php echo $_DIMS['cste']['_SYSTEM_EXPLAIN_SHARED']; ?></I></TD>
		</TR>
		<TR>
			<TD ALIGN="RIGHT"><B><?php echo $_DIMS['cste']['_DIMS_LABEL_HERITED']; ?>:&nbsp;</B></TD>
			<TD>
			<INPUT TYPE="Radio" <?php if ($module->fields['herited']) echo "checked" ?> VALUE="1" NAME="module_herited"><?php echo $_DIMS['cste']['_DIMS_YES']; ?>
			<INPUT TYPE="Radio" <?php if (!$module->fields['herited']) echo "checked" ?> VALUE="0" NAME="module_herited"><?php echo $_DIMS['cste']['_DIMS_NO']; ?>
			</TD>
			<TD ALIGN="LEFT"><I><?php echo $_DIMS['cste']['_SYSTEM_EXPLAIN_HERITED']; ?></I><br><a href="<?php echo "$scriptenv?op=apply_heritage&moduleid=$moduleid"; ?>"><?php echo $_DIMS['cste']['_SYSTEM_APPLYHERITAGE']; ?></a></TD>
		</TR>
		<TR>
			<TD ALIGN="RIGHT"><B><?php echo $_DIMS['cste']['_DIMS_LABEL_ADMINRESTRICTED']; ?>:&nbsp;</B></TD>
			<TD>
			<INPUT TYPE="Radio" <?php if ($module->fields['adminrestricted']) echo "checked" ?> VALUE="1" NAME="module_adminrestricted"><?php echo $_DIMS['cste']['_DIMS_YES']; ?>
			<INPUT TYPE="Radio" <?php if (!$module->fields['adminrestricted']) echo "checked" ?> VALUE="0" NAME="module_adminrestricted"><?php echo $_DIMS['cste']['_DIMS_NO']; ?>
			</TD>
			<TD ALIGN="LEFT"><I><?php echo $_DIMS['cste']['_SYSTEM_EXPLAIN_ADMINRESTRICTED']; ?></I></TD>
		</TR>
		<TR>
			<TD ALIGN="RIGHT"><B><?php echo $_DIMS['cste']['_DIMS_LABEL_VIEWMODE']; ?>:&nbsp;</B></TD>
			<TD>
			<TABLE CELLPADDING=0 CELLSPACING=0>
			<TR>
				<TD VALIGN="MIDDLE">
				<SELECT class="select" NAME="module_viewmode">
				<?php
				foreach($dims_viewmodes as $id => $viewmode)
				{
					if ($module->fields['viewmode'] == $id) $sel = 'selected';
					else $sel = '';
					echo "<OPTION $sel VALUE=\"$id\">$viewmode</OPTION>";
				}
				?>
				</SELECT>
				</TD>
				<TD VALIGN="MIDDLE" NOWRAP>&nbsp;<INPUT TYPE="Checkbox" <?php if ($module->fields['transverseview']) echo "checked" ?> VALUE="1" NAME="module_transverseview"></TD>
				<TD VALIGN="MIDDLE"><?php echo $_DIMS['cste']['_DIMS_LABEL_TRANSVERSE']; ?></TD>
			</TR>
			</TABLE>
			</TD>
			<TD ALIGN="LEFT"><I><?php echo $_DIMS['cste']['_SYSTEM_EXPLAIN_VIEWMODE']; ?></I></TD>
		</TR>
		<TR>
			<TD ALIGN="RIGHT" COLSPAN="3">
				<?php
					echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],'disk',"Javascript:forms.form_modify_module.submit();");
				?>
			</TD>
		</TR>
		</TABLE>
		</FORM>
		</TD>
	</TR>
	</TABLE>
	<?php
	echo $skin->close_simplebloc();
}
echo $skin->close_simplebloc();
echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_GROUP_USABLE_MODULES']);
?>

<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0">
<TR>
	<TD>
	<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1">
	<?php
	echo	"
		<TR CLASS=\"title\">
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_TYPE']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_DESCRIPTION']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_LABEL_ACTION']."</TD>
		</TR>
		";


	  foreach ($installedmodules AS $index => $moduletype) {
		echo	"
			<TR>
				<TD ALIGN=\"CENTER\">$moduletype[label]</TD>
				<TD ALIGN=\"CENTER\">$moduletype[description]</TD>
				<TD ALIGN=\"CENTER\"><A HREF=\"$scriptenv?op=add&instance=NEW,$workspaceid,$moduletype[id]\">".$_DIMS['cste']['_DIMS_LABEL_INSTANCIATE']."</A></TD>
			</TR>
			";
	  }
	?>
	</TABLE>
	</TD>
</TR>
<TR>
	<TD>
		<div id="detaildisplaysharedmod" style="width:100%;float:left;text-align:center;visibility:visible;display:block"><a href="#" onclick="dims_getelem('detailcontentsharedmod').style.visibility='visible';dims_getelem('detailcontentsharedmod').style.display='block';dims_getelem('detaildisplaysharedmod').style.visibility='hidden';dims_getelem('detaildisplaysharedmod').style.display='none';dims_getelem('detailhidesharedmod').style.visibility='visible';dims_getelem('detailhidesharedmod').style.display='block';showSharedMod();"><?php echo $_DIMS['cste']['_DIMS_SHAREDMOD_DISPLAY']; ?></a></div>
		<div id="detailhidesharedmod"  style="width:100%;float:left;text-align:center;visibility:hidden;display:none"><a href="#" onclick="dims_getelem('detailcontentsharedmod').style.visibility='hidden';dims_getelem('detailcontentsharedmod').style.display='none';dims_getelem('detaildisplaysharedmod').style.visibility='visible';dims_getelem('detaildisplaysharedmod').style.display='block';dims_getelem('detailhidesharedmod').style.visibility='hidden';dims_getelem('detailhidesharedmod').style.display='none';"><?php echo $_DIMS['cste']['_DIMS_SHAREDMOD_HIDE']; ?></a></div>
		<div id="detailcontentsharedmod" style=width:100%;float:left;"text-align:justify;padding:10px;visibility:hidden;display:none"><font style="font-weight:bold;">
		</div>
	</td>
</tr>
</table>
<?php echo $skin->close_simplebloc(); ?>
