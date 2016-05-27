<?php
$user = new user();

if (!empty($user_id)) $user->open($user_id);
else $user->init_description();

if ($_SESSION['system_level'] == dims_const::_SYSTEM_WORKSPACES) {
	$workspace_user = new workspace_user();
	if (!empty($workspaceid) && !empty($user_id)) {
		$workspace_user->open($workspaceid, $user_id);
	}
	else {
		$workspace_user->init_description();
		$workspace_user->fields['id_user'] = -1;
	}
}
else {
	$group_user = new group_user();
	if (!empty($groupid) && !empty($user_id)) {
		$group_user->open($groupid, $user_id);
	}
	else {
		$group_user->init_description();
		$group_user->fields['id_user'] = -1;
	}
}

if (isset($_SESSION['module_system']) && !empty($_SESSION['module_system'])) {
	$user->fields['lastname']		= $_SESSION['module_system']['user_lastname'];
	$user->fields['firstname'] 		= $_SESSION['module_system']['user_firstname'];
	$user->fields['login']			= $_SESSION['module_system']['user_login'];
	$user->fields['date_expire']	= $_SESSION['module_system']['user_date_expire'];
	$user->fields['email']			= $_SESSION['module_system']['user_email'];
	$user->fields['phone']			= $_SESSION['module_system']['user_phone'];
	$user->fields['fax'] 			= $_SESSION['module_system']['user_fax'];
	$user->fields['address'] 		= $_SESSION['module_system']['user_address'];
	$user->fields['comments'] 		= $_SESSION['module_system']['user_comments'];
	$user->fields['id_type'] 		= $_SESSION['module_system']['user_id_type'];
	$workspace_user->fields['adminlevel'] = $_SESSION['module_system']['userworkspace_adminlevel'];
	$workspace_user->fields['id_profile'] = $_SESSION['module_system']['userworkspace_id_profile'];
	$_SESSION['module_system'] = '';
	unset($_SESSION['module_system']);
}

?>
<SCRIPT LANGUAGE=JAVASCRIPT>
function user_validate(form) {
	var return_value = false;
	if (dims_validatefield("<?php echo $_DIMS['cste']['_DIMS_LABEL_NAME']; ?>",form.user_lastname,"string"))
	if (dims_validatefield("<?php echo $_DIMS['cste']['_FIRSTNAME']; ?>",form.user_firstname,"string"))
	if (dims_validatefield("<?php echo $_DIMS['cste']['_LOGIN']; ?>",form.user_login,"string")) {
		<?php
		if ($user->fields['id'] == -1) {
			?>
			if ((form.userx_passwordconfirm.value != form.userx_password.value) || form.userx_password.value == '' || form.userx_passwordconfirm.value == '') alert('<?php echo $_DIMS['cste']['_SYSTEM_MSG_PASSWORDERROR']; ?>');
			else {
				rep = dims_xmlhttprequest('admin.php', 'dims_op=dims_checkpasswordvalidity&password='+form.userx_password.value);
				if ((rep*1) == 0) {
					alert('<? echo $_SESSION['cste']['_DIMS_INVALID_PASSWORD']; ?>');
				}
				else return_value = true;
			}
			<?php
		}
		else {
			?>
			if (form.userx_passwordconfirm.value == form.userx_password.value && form.userx_password.value == '') return_value = true;
			else {
				if (form.userx_passwordconfirm.value != form.userx_password.value) alert('<?php echo $_DIMS['cste']['_SYSTEM_MSG_PASSWORDERROR']; ?>');
				else {
					rep = dims_xmlhttprequest('admin.php', 'dims_op=dims_checkpasswordvalidity&password='+form.userx_password.value);

					if (rep*1 == 0) {
						alert('<? echo $_SESSION['cste']['_DIMS_INVALID_PASSWORD']; ?>');
					}
					else return_value = true;
				}
			}
			<?php
		}
		?>
	}
	else
		return_value = false;

	return return_value;
}

function check_contact(form) {
	var return_value = true;

	if(form.contact_id.value == -1)
		dims_xmlhttprequest_todiv('admin.php','op=search_contact&user_lname='+form.user_lastname.value+'&user_fname='+form.user_firstname.value,"",'popup_attach');
	else
		form.submit();

	return return_value;
}
</SCRIPT>

<?php
$token = new FormToken\TokenField;
$token->field("op",			"save_user");
if ($user->fields['id']!=-1) {
	$token->field("user_id",$user->fields['id']);
} else {
	$token->field("user_id","");
}
$token->field("contact_id", (!empty($user->fields['id_contact'])) ? $user->fields['id_contact'] : '-1');
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
$token->field("user_login");
$token->field("userx_password");
$token->field("userx_passwordconfirm");
$token->field("user_email");
$token->field("user_ticketsbyemail");
$token->field("user_phoneforvoip");
$token->field("user_comments");
$token->field("user_timezone");
$token->field("user_date_expire");
$token->field("user_color");
$token->field("usergroup_adminlevel");
$token->field("userworkspace_adminlevel");
$token->field("userworkspace_id_profile");
$token->field("userworkspace_activecontact");
$token->field("userworkspace_activeproject");
$token->field("userworkspace_activeplanning");
$token->field("userworkspace_activenewsletter");
$token->field("userworkspace_activeevent");
$token->field("userworkspace_activesearch");
$token->field("userworkspace_activeticket");
$token->field("userworkspace_activeprofil");
$token->field("userworkspace_activeannot");
?>
<form name="form_modify_user" action="<?php echo $scriptenv ?>" method="POST" enctype="multipart/form-data">
<input type="hidden" name="op" value="save_user">
<?php echo $token->generate(); ?>
<input type="hidden" name="user_id" value="<?php if ($user->fields['id']!=-1) echo $user->fields['id']; ?>">
<input type="hidden" name="contact_id" value="<?php echo (!empty($user->fields['id_contact'])) ? $user->fields['id_contact'] : '-1'; ?>">
<div>
<div id="popup_attach" style="position: absolute; top: 20%; width: 50%; left: 25%;"></div>
<?php
$error = dims_load_securvalue('error', dims_const::_DIMS_CHAR_INPUT, true, true);
if (isset($error)) {
	switch($error) {
		case 'password':
			$error = dims_nl2br($_DIMS['cste']['_SYSTEM_MSG_PASSWORDERROR']);
		break;

		case 'passrejected':
			$error = dims_nl2br($_DIMS['cste']['_SYSTEM_MSG_LOGINPASSWORDERROR']);
		break;

		case 'login':
			$error = dims_nl2br($_DIMS['cste']['_SYSTEM_MSG_LOGINERROR']);
		break;
	}
	?>
	<div class="error" style="padding:2px;text-align:center;font-size:14px;color:#FF0000;"><?php echo $error; ?></div>
	<?php
}
?>
	<div class="dims_form" style="float:left;width:49%;">
		<div style="padding:2px;">
			<p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_NAME']; ?>:</label>
				<input type="text" class="text" name="user_lastname"  value="<?php echo ($user->fields['lastname']); ?>">
			</p>
			<p>
				<label><?php echo $_DIMS['cste']['_FIRSTNAME']; ?>:</label>
				<input type="text" class="text" name="user_firstname"  value="<?php echo ($user->fields['firstname']); ?>">
			</p>
			<p>
				<label><?php echo $_DIMS['cste']['_SERVICE']; ?>:</label>
				<input type="text" class="text" name="user_service"  value="<?php echo ($user->fields['service']); ?>">
			</p>
			<p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_FUNCTION']; ?>:</label>
				<input type="text" class="text" name="user_function"  value="<?php echo ($user->fields['function']); ?>">
			</p>
			<p>
				<label><?php echo $_DIMS['cste']['_DIRECTORY_PHONE']; ?>:</label>
				<input type="text" class="text" name="user_phone"  value="<?php echo ($user->fields['phone']); ?>">
			</p>
			<p>
				<label><?php echo $_DIMS['cste']['_MOBILE']; ?>:</label>
				<input type="text" class="text" name="user_mobile"  value="<?php echo ($user->fields['mobile']); ?>">
			</p>
			<p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_FAX']; ?>:</label>
				<input type="text" class="text" name="user_fax"  value="<?php echo ($user->fields['fax']); ?>">
			</p>
			<p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_ADDRESS']; ?>:</label>
				<textarea class="text" name="user_address"><?php echo ($user->fields['address']); ?></textarea>
			</p>
			<p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_CP']; ?>:</label>
				<input type="text" class="text" name="user_postalcode"  value="<?php echo ($user->fields['postalcode']); ?>">
			</p>
			<p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_CITY']; ?>:</label>
				<input type="text" class="text" name="user_city"  value="<?php echo ($user->fields['city']); ?>">
			</p>
			<p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_COUNTRY']; ?>:</label>
				<input type="text" class="text" name="user_country"  value="<?php echo ($user->fields['country']); ?>">
			</p>
                        <?
                        if ($_SESSION['system_level'] == dims_const::_SYSTEM_WORKSPACES) {
                        ?>
			<p>
				<label><?php echo $_DIMS['cste']['_SEARCH']; ?>:</label>
				<input type="checkbox" name="userworkspace_activesearch" <?php echo ($workspace_user->fields['activesearch']) ? "checked" : ""; ?>>
			</p>
			<p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_TICKET']; ?>:</label>
				<input type="checkbox" name="userworkspace_activeticket" <?php echo ($workspace_user->fields['activeticket']) ? "checked" : ""; ?>>
			</p>
			<p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_PROFIL']; ?>:</label>
				<input type="checkbox" name="userworkspace_activeprofil" <?php echo ($workspace_user->fields['activeprofil']) ? "checked" : ""; ?>>
			</p>
			<p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_ANNOT']; ?>:</label>
				<input type="checkbox" name="userworkspace_activeannot" <?php echo ($workspace_user->fields['activeannot']) ? "checked" : ""; ?>>
			</p>
                            <?
                        }
                        ?>
		</div>
	</div>
	<div style="float:left;width:49%" class="dims_form">
		<div style="padding:2px;">
			<p>
				<label><?php echo $_DIMS['cste']['_LOGIN']; ?>:</label>
				<input type="text" class="text" name="user_login" autocomplete="off" value="<?php echo ($user->fields['login']); ?>">
			</p>
			<p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_PASSWORD']; ?>:</label>
				<input type="password" class="text" name="userx_password" autocomplete="off" value="">
			</p>
			<p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_PASSWORD_CONFIRM']; ?>:</label>
				<input type="password" class="text" name="userx_passwordconfirm"  value="">
			</p>
			<p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_EMAIL']; ?>:</label>
				<input type="text" class="text" name="user_email"  value="<?php echo ($user->fields['email']); ?>">
			</p>
			<p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_TICKETSBYEMAIL']; ?>:</label>
				<input style="width:16px;" type="checkbox" name="user_ticketsbyemail" value="1" <?php if ($user->fields['ticketsbyemail']) echo 'checked'; ?>>
			</p>
			<p>
				<label><?php echo $_DIMS['cste']['_DIMS_COMMENTS']; ?>:</label>
				<textarea class="text" name="user_comments"><?php echo ($user->fields['comments']); ?></textarea>
			</p>
			<p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_TIMEZONE']; ?>:</label>
				<select class="select" name="user_timezone">
				<?php
				$tz_selected = ($user->fields['timezone'] != '') ? $user->fields['timezone'] : _DIMS_DEFAULT_TIMEZONE;

				foreach ($dims_timezone as $t => $label) {
					?>
					<option value="<?php echo $t; ?>" <?php if ($t == $tz_selected) echo 'selected'; ?>><?php echo $label; ?></option>
					<?php
				}
				?>
				</select>
			</p>
			<!--p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_EXPIRATION_DATE']; ?>:</label>
				<input type="text" class="text" name="user_date_expire" value="<?php echo $user->fields['date_expire']; ?>">
			</p-->

			<p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_COLOR']; ?>:</label>
				<?php
				$usericon=DIMS_APP_PATH."data/users/icon".$user->fields['id'].".png";
				if (file_exists($usericon)) echo "<img src=\"./data/users/icon".$user->fields['id'].".png\" alt=\"\">";
				?>
				<input type="text" style="width:100px;" class="text" name="user_color" id="user_color" value="<?php echo ($user->fields['color']); ?>">
				<a href="javascript:void(0);" onclick="javascript:dims_colorpicker_open('user_color', event);"><img src="./common/img/colorpicker/colorpicker.png" align="top" border="0"></a>
			</p>
			<?php
			if ($_SESSION['system_level'] == dims_const::_SYSTEM_WORKSPACES) {
                        ?>
                        <p>
                                <label><?php echo $_DIMS['cste']['_DIMS_LABEL_LEVEL']; ?>:</label>
                                <select class="select" name="userworkspace_adminlevel">
                                <?php
                                foreach ($dims_system_levels as $id => $label) {
                                        if ($id <= $_SESSION['dims']['adminlevel']) {
                                                $sel = ($workspace_user->fields['adminlevel'] == $id) ? 'selected' : '';
                                                echo "<option $sel value=\"$id\">$label</option>";
                                        }
                                        // user / group admin
                                }
                                ?>
                                </select>
                        </p>
                        <p>
                            <label><?php echo $_DIMS['cste']['_DIMS_LABEL_USER_PROFILE']; ?>:</label>
                            <select class="select" name="userworkspace_id_profile">
                            <?php
                            $workspace = new workspace();
                            $workspace->open($workspaceid);
                            $ownprofiles = $workspace->getprofiles();

                            echo "<option value=\"-1\" >".$_DIMS['cste']['_DIMS_LABEL_UNDEFINED']."</option>";

                            foreach ($ownprofiles as $id => $profile)
                            {
                                    if ($workspace_user->fields['id_profile'] == $profile['id']) echo "<option value=\"".$id."\" selected>".$profile['label']."</option>";
                                    else echo "<option value=\"".$id."\">".$profile['label']."</option>";
                            }
                            ?>
                            </select>
                        </p>

                        <p>
                                <label><?php echo $_DIMS['cste']['_DIMS_LABEL_CONTACT']; ?>:</label>
                                <input type="checkbox" name="userworkspace_activecontact" <?php echo ($workspace_user->fields['activecontact']) ? "checked" : ""; ?>>
                        </p>
                        <p>
                                <label><?php echo $_DIMS['cste']['_DIMS_LABEL_PROJECT']; ?>:</label>
                                <input type="checkbox" name="userworkspace_activeproject" <?php echo ($workspace_user->fields['activeproject']) ? "checked" : ""; ?>>
                        </p>
                        <p>
                                <label><?php echo $_DIMS['cste']['_PLANNING']; ?>:</label>
                                <input type="checkbox" name="userworkspace_activeplanning" <?php echo ($workspace_user->fields['activeplanning']) ? "checked" : ""; ?>>
                        </p>
                        <p>
                                <label><?php echo $_DIMS['cste']['_DIMS_LABEL_NEWSLETTER']; ?>:</label>
                                <input type="checkbox" name="userworkspace_activenewsletter" <?php echo ($workspace_user->fields['activenewsletter']) ? "checked" : ""; ?>>
                        </p>
			<p>
				<label><?php echo "Admin. ".$_DIMS['cste']['_DIMS_LABEL_EVENTS']; ?>:</label>
				<input type="checkbox" name="userworkspace_activeevent" <?php echo ($workspace_user->fields['activeevent']) ? "checked" : ""; ?>>
			</p>
                        <p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_EMAIL_REGISTRATION']; ?>:</label>
				<input type="checkbox" name="userworkspace_activeeventemail" <?php echo ($workspace_user->fields['activeeventemail']) ? "checked" : ""; ?>>
			</p>
                        <p>
				<label><?php echo $_DIMS['cste']['_DIMS_EVT_ALLOW_FO']; ?>:</label>
				<input type="checkbox" name="userworkspace_activeeventstep" <?php echo ($workspace_user->fields['activeeventstep']) ? "checked" : ""; ?>>
			</p>
                        <p>
                                <label><?php echo $_DIMS['cste']['_CONNECT_WITH_OTHERS_ACCOUNTS']; ?></label>
                                <input style="width:16px;" type="checkbox" name="userworkspace_activeswitchuser" <?php if($workspace_user->fields['activeswitchuser'] == 1) echo "checked"; ?>>
                        </p>
			<?php
			}
			else {
			?>
			<p>
				<label><?php echo $_DIMS['cste']['_DIMS_LABEL_LEVEL']; ?>:</label>
				<select class="select" name="usergroup_adminlevel">
				<?php
				foreach ($dims_system_levels as $id => $label) {
					if ($id <= $_SESSION['dims']['adminlevel']) {
						$sel = (isset($group_user->fields['adminlevel']) && $group_user->fields['adminlevel'] == $id) ? 'selected' : '';
						echo "<option $sel value=\"$id\">$label</option>";
					}
					// user / group admin
				}
				?>
				</select>
			</p>
			<?
			}
			?>
		</div>
	</div>
</div>
<div style="clear:both;float:right;padding:4px;">
    <?php
        echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"disk","javascript:if(user_validate(forms.form_modify_user)) check_contact(forms.form_modify_user);");
    ?>
</div>
</form>
