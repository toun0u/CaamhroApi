<?
echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_ADDNEWMODULE'],'100%');

$install_path = realpath('.')._DIMS_SEP.'install';
$install_path_writable = is_writable($install_path);
?>
<FORM ACTION="<? echo $scriptenv; ?>" METHOD="POST" ENCTYPE="multipart/form-data">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op", "uploadmodule");
	$token->field("system_modulefile");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<INPUT TYPE="hidden" NAME="op" VALUE="uploadmodule">
<TABLE CELLPADDING="2" CELLSPACING="1">
<TR>
	<TD><? echo $_DIMS['cste']['_DIMS_LABEL_ADDNEWMODULE_DESC']; ?></TD>
</TR>
<?
if (is_writable($install_path))
{
	?>
	<TR>
		<TD><INPUT class="text" TYPE="File" NAME="system_modulefile"></TD>
	</TR>
	<TR>
		<TD align="right"><INPUT TYPE="submit" CLASS="FlatButton" VALUE="<? echo $_DIMS['cste']['_DIMS_SEND']; ?>"></TD>
	</TR>
	<?
}
else
{
	?>
	<TR>
		<TD><? echo $_DIMS['cste']['_DIMS_LABEL_ADDNEWMODULE_WARNING']; ?></TD>
	</TR>
	<?
}
?>
</TABLE>
</FORM>
<?
echo $skin->close_simplebloc();
?>