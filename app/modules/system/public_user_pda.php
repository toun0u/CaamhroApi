<script language="javascript">
function user_validate(form)
{
	if (dims_validatefield("<? echo $_DIMS['cste']['_DIMS_LABEL_NAME']; ?>",form.user_lastname,"string"))
	if (dims_validatefield("<? echo $_DIMS['cste']['_FIRSTNAME']; ?>",form.user_firstname,"string"))
	{
		<?
		if ($user->fields['id'] == -1)
		{
			?>
			rep = dims_xmlhttprequest('admin.php', 'dims_op=dims_checkpasswordvalidity&password='+form.userx_password.value)
			if (rep == 0)
			{
				alert('<? echo $_SESSION['cste']['_DIMS_INVALID_PASSWORD']; ?>');
			}
			else
			{
				if (form.userx_passwordconfirm.value != form.userx_password.value) alert('<? echo $_DIMS['cste']['_SYSTEM_MSG_PASSWORDERROR']; ?>');
				else return true;
			}
			<?
		}
		else
		{
			?>
			if (form.userx_passwordconfirm.value == form.userx_password.value && form.userx_password.value == '') return true;
			else
			{
				rep = dims_xmlhttprequest('admin.php', 'dims_op=dims_checkpasswordvalidity&password='+form.userx_password.value)
				if (rep == 0)
				{
					alert('<? echo $_SESSION['cste']['_DIMS_INVALID_PASSWORD']; ?>');
				}
				else
				{
					if (form.userx_passwordconfirm.value != form.userx_password.value) alert('<? echo $_DIMS['cste']['_SYSTEM_MSG_PASSWORDERROR']; ?>');
					else return true;
				}
			}
			<?
		}
		?>
	}
	return false;
}
</script>

<? echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_MYACCOUNT'],'100%'); ?>

<form name="form_modify_user" action="<? echo $scriptenv ?>" method="POST" enctype="multipart/form-data" onsubmit="javascript:return user_validate(this)">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op","save_user");
	$token->field("user_lastname");
	$token->field("user_firstname");
	$token->field("user_service");
	$token->field("user_function");
	$token->field("user_phone");
	$token->field("user_mobile");
	$token->field("user_fax");
	$token->field("user_address");
	$token->field("user_postalcode");
	$token->field("user_city");
	$token->field("user_country");
	$token->field("user_email");
	$token->field("user_comments");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<input type="hidden" name="op" value="save_user">
<div>
<?

if (isset($error))
{
	switch($error)
	{
		case 'password':
			$error = nl2br($_DIMS['cste']['_SYSTEM_MSG_PASSWORDERROR']);
		break;

		case 'passrejected':
			$error = nl2br($_DIMS['cste']['_SYSTEM_MSG_LOGINPASSWORDERROR']);
		break;

		case 'login':
			$error = nl2br($_DIMS['cste']['_SYSTEM_MSG_LOGINERROR']);
		break;
	}
	?>
	<table>
	<TR>
		<TD ALIGN=CENTER COLSPAN=2>
		<FONT CLASS="Error"><? echo $error; ?></FONT>
		</TD>
	</TR>
	</table>
	<?
}
?>
	<div class="dims_form" style="float:left;width:100%;">
		<div style="padding:2px;">
			<div style="font-size:8px;width:100%;float:left;display:block">
				<div style="font-size:8px;float:left;display:block;padding-top:4px;">
					<? echo $_DIMS['cste']['_DIMS_LABEL_NAME']; ?>:
				</div>
				<div style="font-size:8px;width:70%;float:right;display:block;">
				<input type="text" class="text" style="width:130px" name="user_lastname"  value="<? echo $user->fields['lastname']; ?>">
				</div>
			</div>
			<div style="font-size:8px;width:100%;float:left;display:block">
				<div style="font-size:8px;float:left;display:block">
				<? echo $_DIMS['cste']['_FIRSTNAME']; ?>:
				</div>
				<div style="font-size:8px;width:70%;float:right;display:block">
				<input type="text" class="text" style="width:130px" name="user_firstname"  value="<? echo $user->fields['firstname']; ?>">
				</div>
			</div>
			<div style="font-size:8px;width:30%;float:left;display:block">
				<label style="width:80px"><? echo $_DIMS['cste']['_SERVICE']; ?>:</label>
			</div>
			<div style="font-size:8px;width:70%;float:right;">
				<input type="text" class="text" style="width:130px" name="user_service"  value="<? echo $user->fields['service']; ?>">
			</div>
			<div style="font-size:8px;width:30%;float:left;">
				<label style="width:80px"><? echo $_DIMS['cste']['_DIMS_LABEL_FUNCTION']; ?>:</label>
			</div>
			<div style="font-size:8px;width:70%;float:left;">
				<input type="text" class="text" style="width:130px" name="user_function"  value="<? echo $user->fields['function']; ?>">
			</div>
			<div style="font-size:8px;width:40%;float:left;">
				<label style="width:80px"><? echo $_DIMS['cste']['_PHONE']; ?>:</label>
			</div>
			<div style="font-size:8px;width:60%;float:left;">
				<input type="text" class="text" style="width:130px" name="user_phone"  value="<? echo $user->fields['phone']; ?>">
			</div>
			<div style="font-size:8px;width:40%;float:left;">
				<label style="width:80px"><? echo $_DIMS['cste']['_MOBILE']; ?>:</label>
			</div>
			<div style="font-size:8px;width:60%;float:left;">
				<input type="text" class="text" style="width:130px" name="user_mobile"  value="<? echo $user->fields['mobile']; ?>">
			</div>
			<div style="font-size:8px;width:40%;float:left;">
				<label style="width:80px"><? echo $_DIMS['cste']['_DIMS_LABEL_FAX']; ?>:</label>
			</div>
			<div style="font-size:8px;width:60%;float:right;">
				<input type="text" class="text" name="user_fax"  value="<? echo $user->fields['fax']; ?>">
			</div>
			<div style="font-size:8px;width:40%;float:left;">
				<label style="width:80px"><? echo $_DIMS['cste']['_DIMS_LABEL_ADDRESS']; ?>:</label>
			</div>
			<div style="font-size:8px;width:60%;float:right;">
				<textarea class="text" name="user_address"><? echo $user->fields['address']; ?></textarea>
			</div>
			<div style="font-size:8px;width:40%;float:left;">
				<label style="width:80px"><? echo $_DIMS['cste']['_DIMS_LABEL_CP']; ?>:</label>
			</div>
			<div style="font-size:8px;width:60%;float:right;">
				<input type="text" class="text" name="user_postalcode"  value="<? echo $user->fields['postalcode']; ?>">
			</div>
			<div style="font-size:8px;width:40%;float:left;">
				<label style="width:80px"><? echo $_DIMS['cste']['_DIMS_LABEL_CITY']; ?>:</label>
			</div>
			<div style="font-size:8px;width:60%;float:right;">
				<input type="text" class="text" name="user_city"  value="<? echo $user->fields['city']; ?>">
			</div>
			<div style="font-size:8px;width:40%;float:left;">
				<label style="width:80px"><? echo $_DIMS['cste']['_DIMS_LABEL_COUNTRY']; ?>:</label>
			</div>
			<div style="font-size:8px;width:60%;float:right;">
				<input type="text" class="text" name="user_country"  value="<? echo $user->fields['country']; ?>">
			</div>
			<div style="font-size:8px;width:40%;float:left;">
				<label style="width:80px"><? echo $_DIMS['cste']['_DIMS_LABEL_EMAIL']; ?>:</label>
			</div>
			<div style="font-size:8px;width:60%;float:right;">
				<input type="text" class="text" name="user_email"  value="<? echo $user->fields['email']; ?>">
			</div>
			<div style="font-size:8px;width:40%;float:left;">
				<label style="width:80px"><? echo $_DIMS['cste']['_DIMS_COMMENTS']; ?>:</label>
			</div>
			<div style="font-size:8px;width:60%;float:right;">
				<textarea class="text" name="user_comments"><? echo $user->fields['comments']; ?></textarea>
			</div>

		</div>
	</div>
</div>
<div style="clear:both;float:right;padding:4px;">
	<input type="submit" class="flatbutton" value="<? echo $_DIMS['cste']['_DIMS_SAVE']; ?>">
</div>
</form>
<? echo $skin->close_simplebloc(); ?>
