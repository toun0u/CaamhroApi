<?
require_once(DIMS_APP_PATH . '/modules/system/include/business.php');
require_once(DIMS_APP_PATH . '/modules/system/desktopV2/include/global.php');//permet de récupérer les constantes pour renvoyait sur la fiche de l'utilisateur
?>
<SCRIPT LANGUAGE="javascript">
function confirmDelete() {
	if (confirm('<? echo addslashes($_DIMS['cste']['_DIMS_CONFIRM']);?>')) {
		document.getElementById("user_background").value="";
		document.getElementById("div_background").innerHTML="";
	}
}

function valideFormUser(form) {
	form=document.form_modify_user;
	if (user_validate(form)) {
		document.form_modify_user.submit();
	}
}

function user_validate(form) {

	var regexp_mail = new RegExp("^[a-z0-9._-]+@[a-z0-9._-]{2,}\\.[a-z]{2,4}$","i");
	if (regexp_mail.test(form.user_email.value)){
		// L'email est valide
	} else {
		// L'email est invalide
		alert('<? echo addslashes($_SESSION['cste']['_OEUVRE_ERROR_FORMAT_EMAIL']); ?>');
		return false;
	}

<?


if ($user->fields['id'] == -1)
{
	?>
	if ((form.userx_passwordconfirm.value != form.userx_password.value) || form.userx_password.value == '' || form.userx_passwordconfirm.value == '') alert('<? echo $_DIMS['cste']['_SYSTEM_MSG_PASSWORDERROR']; ?>');
	else
	{
		rep = dims_xmlhttprequest('admin.php', 'dims_op=dims_checkpasswordvalidity&password='+form.userx_password.value)
				if (rep == 0)
		{
			alert('<? echo $_SESSION['cste']['_DIMS_INVALID_PASSWORD']; ?>');
		}
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
		if (form.userx_passwordconfirm.value != form.userx_password.value) alert('<? echo $_DIMS['cste']['_SYSTEM_MSG_PASSWORDERROR']; ?>');
		else
		{
			rep = dims_xmlhttprequest('admin.php', 'dims_op=dims_checkpasswordvalidity&password='+form.userx_password.value)
						if (rep == 0)
			{
				alert('<? echo $_SESSION['cste']['_DIMS_INVALID_PASSWORD']; ?>');
			}
			else return true;
		}
	}
	<?
}
?>

}
</SCRIPT>
<form name="form_modify_user" action="<? echo $scriptenv ?>" method="POST" enctype="multipart/form-data">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op", "save_user");
	$token->field("background");
	$token->field("userx_password");
	$token->field("userx_passwordconfirm");
	$token->field("user_email");
	$token->field("user_phone");
	$token->field("user_phoneforvoip");
	$token->field("user_ticketsbyemail");
	$token->field("user_comments");
	$token->field("user_color");
	$token->field("user_background", $user->fields['background']);
	$token->field("user_defaultworkspace");
	$token->field("user_lang");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<input type="hidden" name="op" value="save_user">
<table>
<?
$listeInput[] = "op";
require_once ('modules/system/include/business.php');
if (isset($error)) {
	switch($error) {
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
		<tr>
			<td>
				<span style="width:100%">
				<FONT CLASS="Error"><? echo $error; ?></FONT>
				</span>
			</td>
		</tr>
	<?
}
?>
	<tr>
		<td><label><? echo $_DIMS['cste']['_DIMS_GNUPG_MANAGEMENT']; ?></label></td>
		<td>
		<?
		if (_DIMS_GNUPG) {
			echo "<img src=\"/modules/system/img/ico_point_green.gif\">".$_DIMS['cste']['_DIMS_LABEL_ACTIVE'];
		}
		else {
			echo "<img src=\"/modules/system/img/ico_point_red.gif\">".$_DIMS['cste']['_DIMS_LABEL_DISABLED'];
		}
		?>
		</td>
	</tr>
	<tr>
	<?
		if (_DIMS_GNUPG) {
			// verification si possède déjà une cle
			if (isset($user->fields['gnupg_fingerprint'])) {
				echo '<p>
					<label>'.$_DIMS['cste']['_DIMS_LABEL_SIGNATURE'].'</label>';

				if ($user->fields['gnupg_fingerprint']!="") {
					echo $user->fields['gnupg_fingerprint'];
				}
				else {
					echo '<input type="file" class="text" name="background" id="background" />';
					$listeInput[] = "background";
				}
			}
		}
	?>

	</tr>
	<tr>
		<td style="text-align:right;"><label><? echo $_DIMS['cste']['_LOGIN']; ?>:</label></td>
		<td><strong><? echo $user->fields['login']; ?></strong></td>
	</tr>
	<tr>
		<td style="text-align:right;"><label><? echo $_DIMS['cste']['_DIMS_LABEL_PASSWORD']; ?>:</label></td>
		<td><input type="password" class="text" name="userx_password" autocomplete="off" value=""></td>
	</tr>
	<tr>
		<td style="text-align:right;"><label><? echo $_DIMS['cste']['_DIMS_LABEL_PASSWORD_CONFIRM']; ?>:</label></td>
		<td><input type="password" class="text" name="userx_passwordconfirm" autocomplete="off" value=""></td>
	</tr>
	<tr>
		<td style="text-align:right;"><label><? echo $_DIMS['cste']['_DIMS_LABEL_EMAIL']; ?>:</label></td>
		<td><input type="text" class="text" name="user_email"  value="<? echo htmlspecialchars($user->fields['email']); ?>"></td>
	</tr>
	<tr>
		<td style="text-align:right;"><label><? echo $_DIMS['cste']['_DIRECTORY_PHONE']; ?>:</label></td>
		<td><input type="text" class="text" name="user_phone"  value="<? echo htmlspecialchars($user->fields['phone']); ?>"></td>
	</tr>
	<tr>
		<td style="text-align:right;"><label><? echo $_DIMS['cste']['HAS_VOIP']; ?>:</label></td>
		<td>
			<input type="hidden" name="user_phoneforvoip" value="0"/>
			<input style="width:16px;" type="checkbox" name="user_phoneforvoip" value="1" <? if ($user->fields['phoneforvoip']) echo 'checked'; ?>>
		</td>
	</tr>
	<tr>
		<td style="text-align:right;"><label><? echo $_DIMS['cste']['_DIMS_LABEL_TICKETSBYEMAIL']; ?>:</label></td>
		<td><input style="width:16px;" type="checkbox" name="user_ticketsbyemail" value="1" <? if ($user->fields['ticketsbyemail']) echo 'checked'; ?>></td>
	</tr>
	<tr>
		<td style="text-align:right;"><label><? echo $_DIMS['cste']['_DIMS_COMMENTS']; ?>:</label></td>
		<td><textarea class="text" name="user_comments"><? echo $user->fields['comments']; ?></textarea></td>
	</tr>
	<?
		$listeInput[] = "userx_password";
		$listeInput[] = "userx_passwordconfirm";
		$listeInput[] = "user_email";
		$listeInput[] = "user_phone";
		$listeInput[] = "user_phoneforvoip";
		$listeInput[] = "user_ticketsbyemail";
		$listeInput[] = "user_comments";
	?>


	<tr>
		<td style="text-align:right;"><label><? echo $_DIMS['cste']['_DIMS_LABEL_COLOR']; ?>:</label>
		<?
		$usericon=DIMS_APP_PATH."data/users/icon_".str_replace("#","",$user->fields['color']).".png";
		if (file_exists($usericon)) echo "<img src=\"./data/users/icon_".str_replace("#","",$user->fields['color']).".png\">";
		?>
		</td>
		<td>
			<input type="text" style="width:100px;" class="text" name="user_color" id="user_color" value="<? echo ($user->fields['color']); ?>">
			<?php
				$listeInput[] = "user_color";
				echo dims_create_button("","pencil","javascript:dims_colorpicker_open('user_color', event);");
			?>
		</td>
	</tr>
	<tr>
		<td style="text-align:right;"><label><? echo $_DIMS['cste']['_DIMS_LABEL_BACKGROUNDIMAGE']; ?>:
		<?
		if ($user->fields['background']!="") {
			echo dims_create_button("","","trash","","confirmDelete();","div_background");
			echo '<input type="hidden" id="user_background" name="user_background" value="'.$user->fields['background'].'">';
			$listeInput[] = "user_background";
		}
		?>
		</label></td>
		<td><input type="file" class="text" name="background" id="background" /></td>
		<?
			$listeInput[] = "background";
		?>
	</tr>
	<tr>
		<td style="text-align:right;"><label><? echo $_DIMS['cste']['_DIMS_LABEL_DEFAULTWORKSPACE'];
		?>:</label></td>
		<td><select class="select" name="user_defaultworkspace">
		<?
			$listeInput[] = "user_defaultworkspace";
			$ws_selected = $user->fields['defaultworkspace'];
			$lstwork=$dims->getWorkspaces();
			foreach ($lstwork as $t => $data) {
				if (isset($lstwork[$t])) {
			?>
				<option value="<? echo $t; ?>" <? if ($t == $ws_selected) echo 'selected'; ?>><? echo $data['label']; ?></option>
			<?
				}
			}
		?>
		</select></td>
	</tr>
	<tr>
		<td style="text-align:right;"><label><? echo $_DIMS['cste']['_DIMS_LABEL_LANG']; ?></label></td>
		<td><select name="user_lang">
		<?
			$listeInput[] = "user_lang";
			$res_lg = $db->query("SELECT id, label FROM dims_lang");
			while($tab_l = $db->fetchrow($res_lg)) {
				if(isset($user->fields['lang']) && $user->fields['lang'] == $tab_l['id']) $sel = "selected=\"selected\"";
				else $sel = "";
				echo '<option value="'.$tab_l['id'].'" '.$sel.'>'.$tab_l['label'].'</option>';
			}
		?>
		</select></td>
	</tr>
	<?
			/*
			$workspace_user = new workspace_user();
			$workspaceid = (isset($_SESSION['dims']['workspaceid']) && $_SESSION['dims']['workspaceid']>0) ? $_SESSION['dims']['workspaceid'] : 0;
			$user_id = $_SESSION['dims']['userid'];

			if (!empty($workspaceid) && !empty($user_id)) {
				$workspace_user->open($workspaceid, $user_id);
			}

			<p>
					<label><? echo $_DIMS['cste']['_SEARCH']; ?>:</label>
					<input type="checkbox" name="userworkspace_activesearch" <? echo ($workspace_user->fields['activesearch']) ? "checked" : ""; ?>>
				</p>
				<p>
					<label><? echo $_DIMS['cste']['_DIMS_LABEL_TICKET']; ?>:</label>
					<input type="checkbox" name="userworkspace_activeticket" <? echo ($workspace_user->fields['activeticket']) ? "checked" : ""; ?>>
				</p>
				<p>
					<label><? echo $_DIMS['cste']['_DIMS_LABEL_PROFIL']; ?>:</label>
					<input type="checkbox" name="userworkspace_activeprofil" <? echo ($workspace_user->fields['activeprofil']) ? "checked" : ""; ?>>
				</p>
				<p>
					<label><? echo $_DIMS['cste']['_DIMS_LABEL_ANNOT']; ?>:</label>
					<input type="checkbox" name="userworkspace_activeannot" <? echo ($workspace_user->fields['activeannot']) ? "checked" : ""; ?>>
				</p>
			 *
			 */
	?>
	<tr>
		<td></td>
		<td><?php echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"","javascript:valideFormUser();","enreg"); ?></td>
<?php
	//on verifie que le module contact soit bien actif dans le workspace
	$work = new workspace();
	$work->open($_SESSION['dims']['workspaceid']);
	if($work->fields['contact'] == 1) {
		require_once(DIMS_APP_PATH . '/modules/system/class_contact.php');
		$contact = new contact();
		$contact->open($user->fields['id_contact']);
		echo '<td>'.
		dims_create_button($_DIMS['cste']['_DIMS_LABEL_ACCESS_FICHE_PERS'],"contact","javascript:location.href='admin.php?dims_mainmenu=0&dims_desktop=block&dims_action=public&submenu="._DESKTOP_V2_CONCEPTS."&id=".$contact->fields['id']."&type=".dims_const::_SYSTEM_OBJECT_CONTACT."&init_filters=1&from=desktop';","enreg")
		.'</td>';
	}

	// Sécurisation du formulaire
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	foreach ($listeInput as $input) {
		$token->field($input);
	}
	$tokenHTML = $token->generate();
	echo $tokenHTML;

?>
	</tr>
	</table>
</form>
