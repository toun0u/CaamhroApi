<?
$lstGr = module_wiki::getGrDispo();
$title = $_SESSION['cste']['_OEUVRE_CREATION_OF_AN_ACCOUNT'];
if($this->fields['id'] != '' && $this->fields['id'] > 0){
	$grU = $this->getGroupsLabeled($lstGr,array($_SESSION['dims']['workspaceid']));
	$title = $_SESSION['cste']['_OEUVRE_EDITION_OF_AN_ACCOUNT']." : ".$this->fields['firstname']." ".$this->fields['lastname'];
}
if (empty($grU)) $grU[0]['id_group'] = 0;
?>
<h4><? echo $title; ?></h4>
<div style="margin-top:10px;">
	<div style="margin-top:10px;">
		<form method="POST" action="<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_COLLAB."&subsub=".module_wiki::_SUB_SUB_COLLAB_LIST_C."&action=".module_wiki::_ACTION_SAVE_COLLAB); ?>" enctype="multipart/form-data" name="save_user" id="save_user">
			<input type="hidden" name="id_user" value="<? echo $this->fields['id']; ?>" />
			<input type="hidden" id="dims_login_reference" value="<? echo $this->fields['login']?>" />
			<table cellpadding="0" cellspacing="0" class="form_user">
				<tr>
					<td rowspan="7" class="photo">
						<?
						if ($this->fields['id_contact'] != '' && $this->fields['id_contact'] > 0){
							$ct = new contact();
							$ct->open($this->fields['id_contact']);
							if ($ct->getPhotoWebPath(40) != '' && file_exists($ct->getPhotoPath(40)))
								echo '<img src="'.$ct->getPhotoWebPath(40).'" border="0" />';
							else
								echo '<img src="'.module_wiki::getTemplateWebPath().'/gfx/human40.png" border="0" />';
						}else
							echo '<img src="'.module_wiki::getTemplateWebPath().'/gfx/human40.png" border="0" />';
						?>
					</td>
					<td class="label">
						<? echo $_SESSION['cste']['_DIMS_LABEL_FIRSTNAME']; ?>&nbsp;:
					</td>
					<td>
						<input rel="requis" type="text" name="ct_firstname" value="<? echo $this->fields['firstname']; ?>" />
						<span class="display_errors" id="def_ct_firstname"></span>
					</td>
					<td class="label">
						<? echo $_SESSION['cste']['_DIMS_LABEL_NAME']; ?>&nbsp;:
					</td>
					<td>
						<input rel="requis" type="text" name="ct_lastname" value="<? echo $this->fields['lastname']; ?>" />
						<span class="display_errors" id="def_ct_lastname"></span>
					</td>
				</tr>
				<tr>
					<td class="label">
						<? echo $_SESSION['cste']['_DIMS_LABEL_EMAIL']; ?>&nbsp;:
					</td>
					<td>
						<input rel="requis" rev="email" type="text" name="ct_email" value="<? echo $this->fields['email']; ?>" />
						<span class="display_errors" id="def_ct_email"></span>
					</td>
					<td class="label">
						<? echo $_SESSION['cste']['_PHONE']; ?>&nbsp;:
					</td>
					<td>
						<input type="text" name="ct_phone" value="<? echo $this->fields['phone']; ?>" />
					</td>
				</tr>
				<tr>
					<td class="label" style="vertical-align: top;">
						<? echo $_SESSION['cste']['_SERVICE']; ?>&nbsp;:
					</td>
					<td>
						<!--<select	name="services[]" multiple="multiple" size=4>-->
						<select	name="services">
							<option value="0"></option>
							<?
							foreach($lstGr as $gr){
								if (isset($grU[$gr->fields['id']]['id_group']) && $grU[$gr->fields['id']]['id_group'] == $gr->fields['id'])
									echo '<option value="'.$gr->fields['id'].'" selected=true>'.$gr->fields['label'].'</option>';
								else
									echo '<option value="'.$gr->fields['id'].'">'.$gr->fields['label'].'</option>';
							}
							?>
						</select>
					</td>
					<td class="label">
						<? echo $_SESSION['cste']['_DIMS_LABEL_FUNCTION']; ?>&nbsp;:
					</td>
					<td>
						<input type="text" name="user_function" value="<? echo $this->fields['function']; ?>" />
					</td>
				</tr>
				<tr>
					<td class="label">
						<? echo $_SESSION['cste']['_LOGIN']; ?>&nbsp;:
					</td>
					<td colspan="3">
						<input rel="requis" rev="dims_login" autocomplete=off type="text" name="login" value="<? echo $this->fields['login']; ?>" />
						<span class="display_errors" id="def_login"></span>
					</td>
				</tr>
				<tr>
					<td class="label">
						<? echo $_SESSION['cste']['_DIMS_LABEL_PASSWORD']; ?>&nbsp;:
					</td>
					<td>
						<input autocomplete=off type="password" name="password" value="" <?php if(!$this->isNew()) echo 'rel="non_requis"'; else echo 'rel="requis"'; ?> rev="dims_pwd" />
						<span class="display_errors" id="def_password"></span>
					</td>
					<td class="label">
						<? echo $_SESSION['cste']['_DIMS_LABEL_PASSWORD_CONFIRM']; ?>&nbsp;:
					</td>
					<td>
						<input autocomplete=off type="password" name="password2" value="" <?php if(!$this->isNew()) echo 'rel="non_requis"'; else echo 'rel="requis"'; ?> rev="dims_pwd_confirm" />
						<span class="display_errors" id="def_password2"></span>
					</td>
				</tr>
				<tr>
					<td class="label">
						<? echo $_SESSION['cste']['_DIMS_LABEL_PHOTO']; ?>&nbsp;:
					</td>
					<td colspan="3">
						<input type="file" name="photo" accept="image/*" />
					</td>
				</tr>
				<tr>
					<td></td>
					<td colspan="4" class="button_form" style="padding-top:10px;">
						<span class="display_errors" id="champs_obligatoires"></span>
						<input type="submit" value="<? echo $_SESSION['cste']['_DIMS_SAVE']; ?>" />
						<input onclick="javascript:document.location.href='<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_COLLAB."&subsub=".module_wiki::_SUB_SUB_COLLAB_LIST_C); ?>';" type="button" value="<? echo $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>" />
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>

<script type="text/javascript" src="/common/js/dims_validForm.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$("form#save_user").dims_validForm({messages: {	defaultError: '<? echo $_SESSION['cste']['_OEUVRE_THIS_FIELD_IS_COMPULSORY']; ?>',
														formatMail: '<? echo $_SESSION['cste']['_OEUVRE_ERROR_FORMAT_EMAIL']; ?>',
														login: '<? echo $_SESSION['cste']['_OEUVRE_LOGIN_ALREADY_USED']; ?>',
														password: '<? echo $_SESSION['cste']['_OEUVRE_ERROR_PASSWORDS_NOT_CORRESPOND']; ?>',
														globalMessage: '<? echo $_SESSION['cste']['_OEUVRE_ERROR_FIELDS_SEIZED']; ?>'
														},
										   displayMessages: true,
										   refId: 'def',
										   globalId: 'champs_obligatoires',
										   classInput: 'dims_error_input'});
	});
</script>
