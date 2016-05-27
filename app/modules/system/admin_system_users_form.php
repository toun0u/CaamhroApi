<?
$types = system_gettypes();
?>

<FORM NAME="form_modify_user" ACTION="<? echo $scriptenv; ?>" METHOD="POST">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op","save_user");
	$token->field("user_id");
	$token->field("user_lastname");
	$token->field("user_firstname");
	$token->field("user_login");
	$token->field("userx_password");
	$token->field("userx_passwordconfirm");
	$token->field("user_date_expire");
	$token->field("usergroup_adminlevel");
	$token->field("user_id_type");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<INPUT TYPE="HIDDEN" NAME="op" VALUE="save_user">
<TABLE CELLPADDING=2 CELLSPACING=1 ALIGN="CENTER">
<?
   if ($user->fields['id']==-1)
	echo "<INPUT TYPE=\"HIDDEN\" NAME=\"user_id\" VALUE=\"?>\">";
   else
	echo "<INPUT TYPE=\"HIDDEN\" NAME=\"user_id\" VALUE=\"".$user->fields['id']."\">";

if (isset($error))
{
	?>
	<TR>
		<TD ALIGN=CENTER COLSPAN=2>
		<FONT CLASS="Error"><? echo $_DIMS['cste']['_SYSTEM_MSG_PASSWORDERROR']; ?></FONT>
		</TD>
	</TR>
	<?
}
?>
<TR>
	<TD ALIGN=RIGHT><? echo $_DIMS['cste']['_DIMS_LABEL_NAME']; ?>:&nbsp;</TD>
	<TD ALIGN=LEFT><INPUT class="text" TYPE="Text" SIZE=30 MAXLENGTH=100 NAME="user_lastname" VALUE="<? echo $user->fields['lastname']; ?>"></TD>
</TR>
<TR>
	<TD ALIGN=RIGHT><? echo $_DIMS['cste']['_FIRSTNAME']; ?>:&nbsp;</TD>
	<TD ALIGN=LEFT><INPUT class="text" TYPE="Text" SIZE=30 MAXLENGTH=100 NAME="user_firstname" VALUE="<? echo $user->fields['firstname']; ?>"></TD>
</TR>
<TR>
	<TD ALIGN=RIGHT><? echo $_DIMS['cste']['_LOGIN']; ?>:&nbsp;</TD>
	<TD ALIGN=LEFT><INPUT class="text" TYPE="Text" SIZE=30 MAXLENGTH=30 NAME="user_login" VALUE="<? echo $user->fields['login']; ?>"></TD>
</TR>
<TR>
	<TD ALIGN=RIGHT><? echo $_DIMS['cste']['_DIMS_LABEL_PASSWORD']; ?>:&nbsp;</TD>
	<TD ALIGN=LEFT><INPUT class="text" TYPE="Password" SIZE=30 MAXLENGTH=30 NAME="userx_password" VALUE=""></TD>
</TR>
<TR>
	<TD ALIGN=RIGHT><? echo $_DIMS['cste']['_DIMS_LABEL_PASSWORD_CONFIRM']; ?>:&nbsp;</TD>
	<TD ALIGN=LEFT><INPUT class="text" TYPE="Password" SIZE=30 MAXLENGTH=30 NAME="userx_passwordconfirm" VALUE=""></TD>
</TR>
<TR>
	<TD ALIGN=RIGHT><? echo $_DIMS['cste']['_DIMS_LABEL_EXPIRATION_DATE']; ?>:&nbsp;</TD>
	<TD ALIGN=LEFT><INPUT class="text" TYPE="Text" SIZE=30 MAXLENGTH=30 NAME="user_date_expire" VALUE="<? echo $user->fields['date_expire']; ?>"></TD>
</TR>
<TR>
	<TD ALIGN=RIGHT><? echo $_DIMS['cste']['_DIMS_LABEL_LEVEL']; ?>:&nbsp;</TD>
	<TD ALIGN=LEFT>
	<SELECT class="select" NAME="usergroup_adminlevel">
	<?
	foreach ($dims_system_levels as $id => $label)
	{
		$sel = ($group_user->fields['adminlevel'] == $id) ? 'selected' : '';
		echo "<OPTION $sel VALUE=\"$id\">$label</OPTION>";
		// user / group admin
	}
	?>
	</SELECT>
	</TD>
</TR>
<TR>
	<TD ALIGN=RIGHT><? echo $_DIMS['cste']['_TYPE']; ?>:&nbsp;</TD>
	<TD ALIGN=LEFT>
	<SELECT class="select" NAME="user_id_type">
		<?
		foreach ($types as $typeid => $typelabel)
		{
			if ($user->fields['id'] == $typeid) $sel = 'selected';
			else $sel = '';
			echo "<option $sel value=\"$typeid\">$typelabel</option>";
		}
		?>
	</SELECT>
	</TD>
</TR>
<TR>
	<TD colspan=3>
	<div id="divfields"></div>
	</TD>
</TR>
<TR>
	<TD ALIGN=RIGHT COLSPAN=2>
		<INPUT TYPE="Submit" class="flatbutton" VALUE="<? echo $_DIMS['cste']['_DIMS_SAVE']; ?>">
	</TD>
</TR>
</TABLE>
</FORM>