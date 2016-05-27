<?php
// own mailinglist
$ownmailinglist_attach = $workspace->getMailingListAttach($mailinglist->fields['id']);
$_SESSION['dims']['current']['workspaceid']=$workspace->fields['id'];

echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_AVAILABLE_MAILINGLIST_ATTACH']."&nbsp;'".$mailinglist->fields['label']."'",'width:100%;');
?>

<table width="100%" cellpadding="0" cellspacing="0">
<tr>
    <td>
        <?php
        if ($op != 'modify_attach' && $op != "mailinglist_addnew_attach") {
            if (dims_isadmin() || $_SESSION['dims']['adminlevel'] >= dims_const::_DIMS_ID_LEVEL_GROUPMANAGER && in_array($_SESSION['userid'],$workspace->getusers()) )	{
                echo dims_create_button($_DIMS['cste']['_DIMS_ADD'],'circle-plus',"Javascript: document.location.href='".$scriptenv."?op=mailinglist_addnew_attach';");
            }
        }
        ?>
    </td>
</tr>
<tr>
    <td>
	<table width="100%" cellpadding="2" cellspacing="1">
	<?php
	echo 	"
		<TR CLASS=\"title\">
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_TYPE']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_EMAIL']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_PUBLIC']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_LABEL_ACTION']."</TD>
		</TR>
		";

	foreach ($ownmailinglist_attach AS $index => $ml_attach) {
		if ($color==$skin->values['bgline2']) $color=$skin->values['bgline1'];
		else $color=$skin->values['bgline2'];

		$modify =  "<a href=\"$scriptenv?tab=mailinglist&op=modify_attach&ml_attach_id={$ml_attach['id']}#modify\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/btn_edit.png\" align=\"middle\" border=\"0\"></a>" ;
		$delete = "<a href=\"javascript:dims_confirmlink('$scriptenv?op=delete_attach&ml_attach_id={$ml_attach['id']}','".$_DIMS['cste']['_SYSTEM_MSG_CONFIRMMAILINGLISTATTACHDELETE']."')\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/btn_delete.png\" align=\"middle\" border=\"0\"></a>";

		echo 	"
			<TR BGCOLOR=\"".$color."\"><TD ALIGN=\"CENTER\">";
		if ($ml_attach['id_user']>0) echo $_DIMS['cste']['_DIMS_LABEL_USER']."</td><TD ALIGN=\"CENTER\">".$ml_attach['labeluser']."</td>";
		elseif ($ml_attach['id_group']>0) echo $_DIMS['cste']['_GROUP']."</td><TD ALIGN=\"CENTER\">".$ml_attach['labelgroup']."</td>";
		else echo $_DIMS['cste']['_DIMS_LABEL_USER_WHITOUTACCOUNT'];
		echo "</td>
				<TD ALIGN=\"CENTER\">".$ml_attach['email']."</TD>";

		if ($ml_attach['email']!="") echo "<TD ALIGN=\"CENTER\">$modify&nbsp;&nbsp;$delete</TD>";
		else echo "<TD ALIGN=\"CENTER\">$modify&nbsp;&nbsp;$delete</TD>";

		echo  "</TR>";
	}
	?>
	<tr><td colspan="4">
	<?php
        echo dims_create_button("",'arrowreturnthick-1-w',"document.location.href='".$scriptenv."?op=mailinglist'");
	?>
	</td></tr>
	</table>
	</td>
</tr>
</table>

<?php
$ml_attach_id=dims_load_securvalue('ml_attach_id',dims_const::_DIMS_NUM_INPUT,true,false,false);

if (($op == 'modify_attach' && $ml_attach_id>0) || $op == "mailinglist_addnew_attach") {
	$mailinglist_attach = new mailinglist_attach();
	if ($ml_attach_id>0) {
		$mailinglist_attach->open($ml_attach_id);
		$workspaceid=$mailinglist->fields['id_workspace'];
	}
	else {
		$mailinglist_attach->init_description();
		$workspaceid=$_SESSION['DIMS']['currentworkspaceid'];
	}

	echo '<A NAME="modify">';
	echo $skin->open_simplebloc(str_replace('<MAILINGLIST>','<U>'.$mailinglist->fields['label'].'</U>',$_DIMS['cste']['_DIMS_LABEL_MAILINGLIST_PROPERTIES']),'100%');
	?>
		<FORM NAME="form_maillingattach_add" ACTION="<? echo $scriptenv; ?>" METHOD="POST">
		<?
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("op",				"save_attach");
			$token->field("ml_attach_id",	$mailinglist_attach->fields['id']);
			$token->field("mailinglist_attach_email");
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>
		<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1">
			<INPUT TYPE="HIDDEN" NAME="op" VALUE="save_attach">
			<INPUT TYPE="HIDDEN" NAME="ml_attach_id" VALUE="<? echo $mailinglist_attach->fields['id']; ?>">
			<TR BGCOLOR="<?php echo $skin->values['bgline2']; ?>">
				<TD ALIGN="RIGHT"><B><? echo $_DIMS['cste']['_DIMS_LABEL_EMAIL']; ?>:&nbsp;</B></TD>
				<TD ALIGN="LEFT"><INPUT class="text" TYPE="Text" SIZE=30 MAXLENGTH=100 NAME="mailinglist_attach_email" VALUE="<? echo $mailinglist_attach->fields['email']; ?>"></TD>
			</TR>
			<TR BGCOLOR="<?php echo $skin->values['bgline2']; ?>">
				<TD ALIGN="RIGHT" COLSPAN="2">
					<?php
						echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],'disk',"Javascript:forms.form_maillingattach_add.submit();");
					?>
				</TD>
			</TR>
		</TABLE>
		</FORM>
	<?php
	echo $skin->close_simplebloc();
}
echo $skin->close_simplebloc();
