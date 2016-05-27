<?php
// own mailinglist
$ownmailinglist = $workspace->getMailingList();

$_SESSION['dims']['current']['workspaceid']=$workspace->fields['id'];

echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_AVAILABLE_MAILINGLIST'],'width:100%;');
?>

<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0">
<TR>
	<TD>
        <?
        if (dims_isadmin() || $_SESSION['dims']['adminlevel'] >= dims_const::_DIMS_ID_LEVEL_GROUPMANAGER && in_array($_SESSION['userid'],$workspace->getusers()) )	{
            echo dims_create_button($_DIMS['cste']['_DIMS_ADD'],"circle-plus","javascript:location.href='".$scriptenv."?op=mailinglist_addnew'");
        }
        ?>
	</td>
</tr>
<tr>
	<td>
	<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1">
	<?
	echo 	"
		<TR CLASS=\"title\">
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_LABEL']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_PROTECTED']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_PUBLIC']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_NBATTACH']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_LABEL_ACTION']."</TD>
		</TR>
		";

	foreach ($ownmailinglist AS $index => $mailinglistl) {
		$protected = $public = "<img border=\"0\" src=\"{$_SESSION['dims']['template_path']}/img/system/p_red.png\" align=\"middle\">";

		$p_green = "<img border=\"0\" src=\"{$_SESSION['dims']['template_path']}/img/system/p_green.png\" align=\"middle\">";

		if ($mailinglistl['protected']) $protected = $p_green;
		if ($mailinglistl['public']) $public = $p_green;

		$modify =  "<a href=\"$scriptenv?tab=mailinglist&op=modify&mailinglistid={$mailinglistl['id']}#modify\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/btn_edit.png\" align=\"middle\" border=\"0\"></a>" ;
		$delete = "<a href=\"javascript:dims_confirmlink('$scriptenv?op=delete&mailinglistid={$mailinglistl['id']}','".$_DIMS['cste']['_SYSTEM_MSG_CONFIRMMAILINGLISTDELETE']."')\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/btn_delete.png\" align=\"middle\" border=\"0\"></a>";
		$viewlist="<a href=\"$scriptenv?tab=mailinglist&op=viewlist&mailinglistid={$mailinglistl['id']}\"><img src=\"./common/img/view.png\" align=\"middle\" border=\"0\"></a>" ;
		echo 	"
			<TR>
			<TD ALIGN=\"CENTER\">{$mailinglistl['label']}</td>
			<TD ALIGN=\"CENTER\"><a href=\"?op=switch_protected&mailinglistid={$mailinglistl['id']}\">$protected</a></TD>
			<TD ALIGN=\"CENTER\"><a href=\"?op=switch_public&mailinglistid={$mailinglistl['id']}\">$public</a></TD>
			<TD ALIGN=\"CENTER\">{$mailinglistl['cpte']}&nbsp;$viewlist</td>
			<TD ALIGN=\"CENTER\">$modify&nbsp;&nbsp;$delete</TD>";
		echo  "</TR>";
	}
	?>
	</TABLE>
	</TD>
</TR>
</TABLE>

<?
$mailinglistid=dims_load_securvalue('mailinglistid',dims_const::_DIMS_NUM_INPUT,true,false,false);

if (($op == 'modify' && $mailinglistid>0) || $op == "mailinglist_addnew") {
	$mailinglist = new mailinglist();
	if ($mailinglistid>0) {
		$mailinglist->open($mailinglistid);
		$workspaceid=$mailinglist->fields['id_workspace'];
	}
	else {
		$mailinglist->init_description();
		$workspaceid=$workspaceid;
	}

$token = new FormToken\TokenField;
$token->field("op",				"save_mailinglist_props");
$token->field("mailinglistid",	$mailinglist->fields['id']);
$token->field("mailinglist_label");
$token->field("mailinglist_protected");
$token->field("mailinglist_public");
$token->field("fck_mailinglist_query");
$token->field("fck_mailinglist_query_delete");


	echo '<A NAME="modify">';
	echo $skin->open_simplebloc(str_replace('<MAILINGLIST>','<U>'.$mailinglist->fields['label'].'</U>',$_DIMS['cste']['_DIMS_LABEL_MAILINGLIST_PROPERTIES']),'100%');
	?>
		<FORM NAME="form_modify_module" ACTION="<? echo $scriptenv; ?>" METHOD="POST">
		<?php echo $token->generate(); ?>
		<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1">
			<INPUT TYPE="HIDDEN" NAME="op" VALUE="save_mailinglist_props">
			<INPUT TYPE="HIDDEN" NAME="mailinglistid" VALUE="<? echo $mailinglist->fields['id']; ?>">
			<TR>
				<TD ALIGN="RIGHT"><B><? echo $_DIMS['cste']['_DIMS_LABEL']; ?>:&nbsp;</B></TD>
				<TD ALIGN="LEFT"><INPUT class="text" TYPE="Text" SIZE=30 MAXLENGTH=100 NAME="mailinglist_label" VALUE="<? echo $mailinglist->fields['label']; ?>"></TD>
				<TD ALIGN="LEFT"><I><? echo $_DIMS['cste']['_SYSTEM_EXPLAIN_MAILINGLISTNAME']; ?></I></TD>
			</TR>
			<TR>
				<TD ALIGN="RIGHT"><B><? echo $_DIMS['cste']['_DIMS_LABEL_PROTECTED']; ?>:&nbsp;</B></TD>
				<TD>
				<INPUT TYPE="Radio" <? if ($mailinglist->fields['protected']) echo "checked=\"checked\"" ?> VALUE="1" NAME="mailinglist_protected"><? echo $_DIMS['cste']['_DIMS_YES']; ?>
				<INPUT TYPE="Radio" <? if (!$mailinglist->fields['protected']) echo "checked=\"checked\"" ?> VALUE="0" NAME="mailinglist_protected"><? echo $_DIMS['cste']['_DIMS_NO']; ?>
				<TD ALIGN="LEFT"><I><? echo $_DIMS['cste']['_SYSTEM_EXPLAIN_MAILINGLIST_PROTECTED']; ?></I></TD>
			</TR>
			<TR>
				<TD ALIGN="RIGHT"><B><? echo $_DIMS['cste']['_DIMS_LABEL_PUBLIC']; ?>:&nbsp;</B></TD>
				<TD>
				<INPUT TYPE="Radio" <? if ($mailinglist->fields['public']) echo "checked=\"checked\"" ?> VALUE="1" NAME="mailinglist_public"><? echo $_DIMS['cste']['_DIMS_YES']; ?>
				<INPUT TYPE="Radio" <? if (!$mailinglist->fields['public']) echo "checked=\"checked\"" ?> VALUE="0" NAME="mailinglist_public"><? echo $_DIMS['cste']['_DIMS_NO']; ?>
				</TD>
				<TD ALIGN="LEFT"><I><? echo $_DIMS['cste']['_SYSTEM_EXPLAIN_MAILINGLIST_PUBLIC']; ?></I></TD>
			</TR>
			<TR>
				<TD ALIGN="RIGHT"><B>SQL : &nbsp;</B></TD>
				<TD>
				    <textarea style="width:280px;height: 140px;" name="fck_mailinglist_query"><? echo $mailinglist->fields['query']; ?></textarea>
				</TD>
				<TD ALIGN="LEFT"><I></I></TD>
			</TR>
			<TR>
				<TD ALIGN="RIGHT"><B>SQL Unsubscribe: &nbsp;</B></TD>
				<TD>
				    <textarea style="width:280px;height: 140px;" name="fck_mailinglist_query_delete"><? echo $mailinglist->fields['query_delete']; ?></textarea>
				</TD>
				<TD ALIGN="LEFT"><I></I></TD>
			</TR>
			<TR>
				<TD ALIGN="RIGHT" COLSPAN="3">
					<?
					echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"disk","form_modify_module.submit();");
					?>
				</TD>
			</TR>
		</TABLE>
		</FORM>
	<?
	echo $skin->close_simplebloc();
}
echo $skin->close_simplebloc();
