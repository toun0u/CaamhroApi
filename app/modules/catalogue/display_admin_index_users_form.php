<?php
if (isset($_SESSION['module_system']) && !empty($_SESSION['module_system'])) {
	$user->fields['lastname'] = $_SESSION['module_system']['user_lastname'];
	$user->fields['firstname'] = $_SESSION['module_system']['user_firstname'];
	$user->fields['login'] = $_SESSION['module_system']['user_login'];
	$user->fields['date_expire'] = $_SESSION['module_system']['user_date_expire'];
	$user->fields['email'] = $_SESSION['module_system']['user_email'];
	$user->fields['phone'] = $_SESSION['module_system']['user_phone'];
	$user->fields['fax'] = $_SESSION['module_system']['user_fax'];
	$user->fields['address'] = $_SESSION['module_system']['user_address'];
	$user->fields['comments'] = $_SESSION['module_system']['user_comments'];
	$user->fields['id_type'] = $_SESSION['module_system']['user_id_type'];
	$group_user->fields['adminlevel'] = $_SESSION['module_system']['usergroup_adminlevel'];
	$group_user->fields['id_profile'] = $_SESSION['module_system']['usergroup_id_profile'];

	//dims_print_r($user);

	$_SESSION['module_system'] = '';
	unset($_SESSION['module_system']);
}
?>


<script type="text/javascript">
	function getElem(nom) {
		var OBJdest = (document.getElementById) ? document.getElementById(nom) : eval("document.all[nom]");
		return OBJdest;
	}
	function system_switchtype() {
		document.form_modify_user.switchtype.value = '1';
		document.form_modify_user.submit();
	}
	function user_validate(form) {
		if (dims_validatefield("<? echo _CATALOGUE_LABEL_LASTNAME; ?>",form.user_lastname,"string"))
		if (dims_validatefield("<? echo _CATALOGUE_LABEL_FIRSTNAME; ?>",form.user_firstname,"string"))
		if (dims_validatefield("<? echo _CATALOGUE_LABEL_LOGIN; ?>",form.user_login,"string"))
			return true;
		return false;
	}
</script>

<?php
// login deja existant
if (isset($_GET['error'])) {
	$error = dims_load_securvalue('error', dims_const::_DIMS_CHAR_INPUT, true, false);
}
?>

<TABLE CELLPADDING=2 CELLSPACING=1 ALIGN="CENTER">
<FORM NAME="form_modify_user" ACTION="<?php echo $dims->getScriptEnv(); ?>" METHOD="POST" ENCTYPE="multipart/form-data" OnSubmit="javascript:return user_validate(this)">
<INPUT TYPE="HIDDEN" NAME="op" VALUE="administration">
<INPUT TYPE="HIDDEN" NAME="action" VALUE="save_user">
<INPUT TYPE="HIDDEN" NAME="switchtype" VALUE="0">
<?php
if ($user->fields['id']==-1) {
	echo "<INPUT TYPE=\"HIDDEN\" NAME=\"user_id\" VALUE=\"\">";
}
else {
	echo "<INPUT TYPE=\"HIDDEN\" NAME=\"user_id\" VALUE=\"".$user->fields['id']."\">";
}
?>

<INPUT TYPE="HIDDEN" NAME="groupid" VALUE="<? echo $groupid; ?>">
<INPUT TYPE="HIDDEN" NAME="groupinterid" VALUE="<? echo $_SESSION['catalogue']['root_group']; ?>">
<?php
if (isset($error)) {
	switch ($error) {
		case 'password':
			$error = _CATALOGUE_MSG_PASSWORDERROR;
			break;
		case 'passrejected':
			$error = _CATALOGUE_MSG_LOGINPASSWORDERROR;
			break;
		case 'login':
			$error = _CATALOGUE_MSG_LOGINERROR;
			break;
	}
	?>
	<TR>
		<TD ALIGN=CENTER COLSPAN=2>
		<p class="error"><?= $error; ?></p>
		</TD>
	</TR>
	<?php
}
?>
<TR>
	<TD VALIGN=TOP>
		<TABLE CELLPADDING=2 CELLSPACING=1 ALIGN="CENTER">
		<TR>
			<TD ALIGN=RIGHT><? echo _CATALOGUE_LABEL_LASTNAME; ?>:&nbsp;</TD>
			<TD ALIGN=LEFT><INPUT CLASS="Text" TYPE="Text" SIZE=30 MAXLENGTH=100 NAME="user_lastname" VALUE="<? echo $user->fields['lastname']; ?>"></TD>
		</TR>
		<TR>
			<TD ALIGN=RIGHT><? echo _CATALOGUE_LABEL_FIRSTNAME; ?>:&nbsp;</TD>
			<TD ALIGN=LEFT><INPUT CLASS="Text" TYPE="Text" SIZE=30 MAXLENGTH=100 NAME="user_firstname" VALUE="<? echo $user->fields['firstname']; ?>"></TD>
		</TR>
		<TR>
			<TD ALIGN=RIGHT><? echo _CATALOGUE_LABEL_LOGIN; ?>:&nbsp;</TD>
			<TD ALIGN=LEFT><INPUT CLASS="Text" TYPE="Text" SIZE=30 MAXLENGTH=30 NAME="user_login" autocomplete="off" VALUE="<? echo $user->fields['login']; ?>"></TD>
		</TR>
		<TR>
			<TD ALIGN=RIGHT><? echo _CATALOGUE_LABEL_PASSWORD; ?>:&nbsp;</TD>
			<TD ALIGN=LEFT><INPUT CLASS="Text" TYPE="Password" SIZE=30 MAXLENGTH=30 NAME="userx_password" autocomplete="off" VALUE=""></TD>
		</TR>
		<TR>
			<TD ALIGN=RIGHT><? echo _CATALOGUE_LABEL_PASSWORD_CONFIRM; ?>:&nbsp;</TD>
			<TD ALIGN=LEFT><INPUT CLASS="Text" TYPE="Password" SIZE=30 MAXLENGTH=30 NAME="userx_passwordconfirm" autocomplete="off" VALUE=""></TD>
		</TR>
		<TR>
			<TD ALIGN=RIGHT><? echo _CATALOGUE_LABEL_EXPIRATION_DATE; ?>:&nbsp;</TD>
			<TD ALIGN=LEFT><INPUT CLASS="Text" TYPE="Text" SIZE=30 MAXLENGTH=30 NAME="user_date_expire" VALUE="<? echo $user->fields['date_expire']; ?>"></TD>
		</TR>

		<TR>
			<TD ALIGN=RIGHT><? echo _CATALOGUE_LABEL_EMAIL; ?>:&nbsp;</TD>
			<TD ALIGN=LEFT><INPUT CLASS="Text" TYPE="Text" SIZE=30 MAXLENGTH=255 NAME="user_email" VALUE="<? echo $user->fields['email']; ?>"></TD>
		</TR>
		<TR>
			<TD ALIGN=RIGHT><? echo _CATALOGUE_LABEL_PHONE; ?>:&nbsp;</TD>
			<TD ALIGN=LEFT><INPUT CLASS="Text" TYPE="Text" SIZE=30 MAXLENGTH=32 NAME="user_phone" VALUE="<? echo $user->fields['phone']; ?>"></TD>
		</TR>
		<TR>
			<TD ALIGN=RIGHT><? echo _CATALOGUE_LABEL_FAX; ?>:&nbsp;</TD>
			<TD ALIGN=LEFT><INPUT CLASS="Text" TYPE="Text" SIZE=30 MAXLENGTH=32 NAME="user_fax" VALUE="<? echo $user->fields['fax']; ?>"></TD>
		</TR>
		<TR>
			<TD ALIGN=RIGHT><? echo _CATALOGUE_LABEL_ADDRESS; ?>:&nbsp;</TD>
			<TD ALIGN=LEFT><TEXTAREA CLASS="Text" COLS=40 ROWS=5 NAME="user_address"><? echo $user->fields['address']; ?></TEXTAREA></TD>
		</TR>
		<TR>
			<TD ALIGN=RIGHT><? echo _CATALOGUE_LABEL_COMMENTS; ?>:&nbsp;</TD>
			<TD ALIGN=LEFT><TEXTAREA CLASS="Text" COLS=40 ROWS=5 NAME="user_comments"><? echo $user->fields['comments']; ?></TEXTAREA></TD>
		</TR>
		</TABLE>
	</TD>
	<TD VALIGN=TOP>
		<TABLE CELLPADDING=2 CELLSPACING=1 ALIGN="CENTER">
		<TR>
			<TD ALIGN=RIGHT><? echo _CATALOGUE_LABEL_LEVEL; ?>:&nbsp;</TD>
			<TD ALIGN=LEFT>
				<SELECT CLASS="SELECT" NAME="usergroup_adminlevel">
					<?php
					foreach ($user_levels as $id => $label) {
						if ($id <= $_SESSION['session_adminlevel']) {
							$sel = ($group_user->fields['adminlevel'] == $id) ? 'selected' : '';
							echo "<OPTION $sel VALUE=\"$id\">$label</OPTION>";
						}
					}
					?>
				</SELECT>
			</TD>
		</TR>
		</FORM>
		<?php
		// On regarde si le client utilise les budgets
		if (isset($_SESSION['catalogue']['code_client'])) {
			$sql = "
				SELECT *
				FROM dims_mod_vpc_budget
				WHERE id_group = $groupid
				AND id_client = '{$_SESSION['catalogue']['code_client']}'
				AND en_cours = 1";
			$db->query($sql);
			($db->numrows()) ? $use_budget = true : $use_budget = false;
		}

		if ($use_budget && isset($user_id) && $user_id != '') {
			?>
			<TR>
				<TD COLSPAN="2">
					<FORM NAME='user_budget_form' ACTION='<? echo $dims->getScriptEnv(); ?>' METHOD='post'>
					<INPUT TYPE='Hidden' NAME='action' VALUE='save_user_budget'>
					<INPUT TYPE='Hidden' NAME='op' VALUE='administration'>
					<INPUT TYPE='Hidden' NAME='id_user' VALUE='administration'>
					<INPUT TYPE='Hidden' NAME='groupid' VALUE='<? echo $groupid; ?>'>
					<INPUT TYPE='Hidden' NAME='groupinterid' VALUE='<? echo $_SESSION['catalogue']['root_group']; ?>'>
					<INPUT TYPE='Hidden' NAME='budget_id_user' VALUE='<? echo $user->fields['id']; ?>'>
					<INPUT TYPE='Hidden' NAME='budget_en_cours' VALUE='1'>

					<TABLE CELLPADDING=2 CELLSPACING=1>
						<?
						// Budget
						$sql = "
							SELECT *
							FROM dims_mod_vpc_user_budget
							WHERE id_user = $user_id
							AND en_cours = 1";
						$db->query($sql);
						$budget_fields = $db->fetchrow();

						if ($user->fields['limite_budget'] === '1') {
							echo "

								<INPUT TYPE='Hidden' NAME='id_budget' VALUE='{$budget_fields['id']}'>
								<INPUT TYPE='Hidden' NAME='limite_budget' VALUE='1'>
								<TR CLASS='Title'>
									<TD COLSPAN=2>Modifier le budget</TD>
								</TR>";

							if (isset($err)) {
								echo "<TR><TD COLSPAN=2><FONT STYLE='color:red'>{$err_msg[$err]}</FONT><BR><BR></TD></TR>";
							}

							echo "
								<TR>
									<TD ALIGN=RIGHT NOWRAP>Code :</TD>
									<TD><INPUT CLASS='Text' TYPE='Text' NAME='budget_code' VALUE='{$budget_fields['code']}'></TD>
								</TR>
								<TR>
									<TD ALIGN=RIGHT NOWRAP>Montant :</TD>
									<TD><INPUT CLASS='Text' TYPE='Text' NAME='budget_valeur' VALUE='{$budget_fields['valeur']}'></TD>
								</TR>
								<TR>
									<TD COLSPAN=2 ALIGN=RIGHT NOWRAP><INPUT CLASS='Button' TYPE='Button' VALUE='Clôturer le budget' onClick=\"javascript:dims_confirmlink('$scriptenv?op=administration&action=close_user_budget&id_budget={$budget_fields['id']}&id_user={$user->fields['id']}','Etes-vous sûr(e) de vouloir clôturer le budget ?');\"></TD>
								</TR>
								<TR>
									<TD COLSPAN=2 ALIGN=CENTER NOWRAP><BR><INPUT CLASS='Button' TYPE='Submit' VALUE='Enregistrer Budget'></TD>
								</TR>";
						}
						elseif ($user->fields['limite_budget'] === '0') {
							echo "
							<INPUT TYPE='Hidden' NAME='limite_budget' VALUE='1'>
								<TR CLASS='Title'>
									<TD>Pas de limitation de budget</TD>
								</TR>
								<TR>
									<TD><INPUT CLASS='Button' TYPE='Submit' VALUE='Limiter le budget'></TD>
								</TR>";
						}
						else {
							echo "
								<INPUT TYPE='Hidden' NAME='limite_budget' VALUE='1'>
								<TR CLASS='Title'>
									<TD COLSPAN=2>Créer un nouveau budget</TD>
								</TR>
								<TR>
									<TD ALIGN=RIGHT NOWRAP>Code :</TD>
									<TD><INPUT CLASS='Text' TYPE='Text' NAME='budget_code'></TD>
								</TR>
								<TR>
									<TD ALIGN=RIGHT NOWRAP>Montant :</TD>
									<TD><INPUT CLASS='Text' TYPE='Text' NAME='budget_valeur'></TD>
								</TR>
								<TR>
									<TD COLSPAN=2><INPUT CLASS='Checkbox' TYPE='Checkbox' NAME='limite_budget' VALUE='0'>&nbsp;Pas de limitation de budget</TD>
								</TR>
								<TR>
									<TD COLSPAN=2 ALIGN=CENTER><BR><INPUT CLASS='Button' TYPE='Submit' VALUE='Enregistrer'></TD>
								</TR>";
						}
						?>
					</TABLE>
				</TD>
			</TR>
			<?
		}
		?>
		</TABLE>
	</TD>
</TR>


<TR>
	<TD ALIGN=RIGHT COLSPAN=2>
		<INPUT TYPE="Button" CLASS="Button" VALUE="Enregistrer" onClick="javascript:document.form_modify_user.submit();">
	</TD>
</TR>
</TABLE>
