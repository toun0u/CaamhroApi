<?
// own mailinglist
$ownmailinglist = $workspace->getMailingList();
$_SESSION['dims']['current']['workspaceid']=$workspace->fields['id'];

echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_AVAILABLE_MAILINGLIST'],'100%');

if (dims_isadmin() || $_SESSION['dims']['adminlevel'] >= dims_const::_DIMS_ID_LEVEL_GROUPMANAGER && in_array($_SESSION['userid'],$workspace->getusers()) )	{
		echo dims_create_button('Ajouter une liste','plus','Javascript: document.href.location="'.$scriptenv.'?op=mailinglist_addnew";');
	}
?>

<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0">
<TR>
	<TD>
	<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1">
	<?
	echo 	"
		<TR CLASS=\"title\">
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_NAME']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_PROTECTED']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_DIMS_LABEL_PUBLIC']."</TD>
			<TD ALIGN=\"CENTER\">".$_DIMS['cste']['_LABEL_ACTION']."</TD>
		</TR>
		";

	foreach ($ownmailinglist AS $index => $mailinglist) {
		$protected = $public = "<img border=\"0\" src=\"{$_SESSION['dims']['template_path']}/img/system/p_red.png\" align=\"middle\">";

		$p_green = "<img border=\"0\" src=\"{$_SESSION['dims']['template_path']}/img/system/p_green.png\" align=\"middle\">";

		if ($mailinglist['protected']) $protected = $p_green;
		if ($mailinglist['public']) $public = $p_green;

		$modify =  "<a href=\"$scriptenv?tab=mailinglist&op=modify&mailinglistid={$mailinglist['id']}#modify\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/btn_edit.png\" align=\"middle\" border=\"0\"></a>" ;
		$delete = "<a href=\"javascript:dims_confirmlink('$scriptenv?op=delete&mailinglistid={$mailinglist['id']}','".$_DIMS['cste']['_SYSTEM_MSG_CONFIRMMAILINGLISTDELETE']."')\"><img src=\"{$_SESSION['dims']['template_path']}/img/system/btn_delete.png\" align=\"middle\" border=\"0\"></a>";

		echo 	"
			<TR>
				<TD ALIGN=\"CENTER\">".$mailinglist['label']."</TD>
				<TD ALIGN=\"CENTER\"><a href=\"?op=switch_protected&mailinglistid=".$mailinglist['id']."\">$protected</a></TD>
				<TD ALIGN=\"CENTER\"><a href=\"?op=switch_public&mailinglistid=".$mailinglist['id']."\">$public</a></TD>
				<TD ALIGN=\"CENTER\">".$mailinglist['cpte']."</TD>
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
$mailinglistid=dims_load_securvalue('mailinglistid',dims_const::_DIMS_NUM_INPUT,true,false,false);

if (($op == 'modify' && $mailinglistid>0) || $op == "mailinglist_addnew") {
	$mailinglist = new mailinglist();
	if ($mailinglistid>0) {
		$mailinglist->open($mailinglistid);
		$workspaceid=$mailinglist->fields['id_workspace'];
	}
	else {
		$mailinglist->init_description();
		$workspaceid=$_SESSION['DIMS']['currentworkspaceid'];
	}

	echo '<A NAME="modify">';
	echo $skin->open_simplebloc(str_replace('<MAILINGLIST>','<U>'.$mailinglist->fields['label'].'</U>',$_DIMS['cste']['_DIMS_LABEL_MAILINGLIST_PROPERTIES']),'100%');
	?>
		<FORM NAME="form_modify_module" ACTION="<? echo $scriptenv; ?>" METHOD="POST">
		<?
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("op",				"save_mailinglist_props");
			$token->field("mailinglistid",	$mailinglist->fields['id']);
			$token->field("mailinglist_label");
			$token->field("mailinglist_protected");
			$token->field("mailinglist_public");
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>
		<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1">
			<INPUT TYPE="HIDDEN" NAME="op" VALUE="save_mailinglist_props">
			<INPUT TYPE="HIDDEN" NAME="mailinglistid" VALUE="<? echo $mailinglist->fields['id']; ?>">
			<TR>
				<TD ALIGN="RIGHT"><B><? echo $_DIMS['cste']['_DIMS_LABEL_NAME']; ?>:&nbsp;</B></TD>
				<TD ALIGN="LEFT"><INPUT class="text" TYPE="Text" SIZE=30 MAXLENGTH=100 NAME="mailinglist_label" VALUE="<? echo $mailinglist->fields['label']; ?>"></TD>
				<TD ALIGN="LEFT"><I><? echo $_DIMS['cste']['_SYSTEM_EXPLAIN_MAILINGLISTNAME']; ?></I></TD>
			</TR>
			<TR>
				<TD ALIGN="RIGHT"><B><? echo $_DIMS['cste']['_DIMS_LABEL_PROTECTED']; ?>:&nbsp;</B></TD>
				<TD>
				<INPUT TYPE="Radio" <? if ($mailinglist->fields['protected']) echo "checked" ?> VALUE="1" NAME="mailinglist_protected"><? echo $_DIMS['cste']['_DIMS_YES']; ?>
				<INPUT TYPE="Radio" <? if (!$mailinglist->fields['protected']) echo "checked" ?> VALUE="0" NAME="mailinglist_protected"><? echo $_DIMS['cste']['_DIMS_NO']; ?>
				<TD ALIGN="LEFT"><I><? echo $_DIMS['cste']['_SYSTEM_EXPLAIN_MAILINGLIST_PROTECTED']; ?></I></TD>
			</TR>
			<TR>
				<TD ALIGN="RIGHT"><B><? echo $_DIMS['cste']['_DIMS_LABEL_PUBLIC']; ?>:&nbsp;</B></TD>
				<TD>
				<INPUT TYPE="Radio" <? if ($mailinglist->fields['publi']) echo "checked" ?> VALUE="1" NAME="mailinglist_public"><? echo $_DIMS['cste']['_DIMS_YES']; ?>
				<INPUT TYPE="Radio" <? if (!$mailinglist->fields['public']) echo "checked" ?> VALUE="0" NAME="mailinglist_public"><? echo $_DIMS['cste']['_DIMS_NO']; ?>
				</TD>
				<TD ALIGN="LEFT"><I><? echo $_DIMS['cste']['_SYSTEM_EXPLAIN_MAILINGLIST_PUBLIC']; ?></I></TD>
			</TR>
			<TR>
				<TD ALIGN="RIGHT" COLSPAN="3">
                    <?php
                    echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],'disk','Javascript: forms.form_modify_module.submit();');
                    ?>
				</TD>
			</TR>
		</TABLE>
		</FORM>
	<?
	echo $skin->close_simplebloc();
}
echo $skin->close_simplebloc();
