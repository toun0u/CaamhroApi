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
<div id="accordion" class="ui-accordion ui-widget ui-helper-reset ui-accordion-icons">
	<h3><a href="#"><? echo $_DIMS['cste']['_PROFIL']; ?></a></h3>
	<div>
		<? require_once(DIMS_APP_PATH . "/modules/system/desktop_user_profil.php"); ?>
	</div>
	<h3><a href="#"><? echo $_DIMS['cste']['_DIMS_CV_FOR_PROFILE']; ?></a></h3>
	<div>
		<? require_once(DIMS_APP_PATH . "/modules/system/desktop_user_presentation.php"); ?>
	</div>
	<h3><a href="#">Skin</a></h3>
	<div>
		<? require_once(DIMS_APP_PATH . "/modules/system/desktop_user_skin.php"); ?>
	</div>
</div>
<? echo $skin->close_simplebloc(); ?>
<script language="javascript"> $("#accordion").accordion({ autoHeight: false }); </script>
