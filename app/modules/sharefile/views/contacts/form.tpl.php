<?php
$contact = $this->get('contact');
$errors = $this->get('errors');
?>
<form name="form_etape1" method="post" action="<?= dims_urlencode($this->get('urlbase')->addParams(array('op' => 'contacts', 'action' => 'save'))); ?>">
<input type="hidden" name="contact_id" value="<?= $contact['id']; ?>" />
<div  style="float:left; width:80%;padding-top:20px;">
	<div style="padding:2px;">
		<span style="width:10%;display:block;float:left;">
			<img src="/common/modules/sharefile/img/gestion_contact.png">
		</span>
		<span style="width:90%;display:block;float:left;font-size:20px;color:#BABABA;font-weight:bold;">
			<?= (!empty($contact['id'])) ? dims_constant::getVal('_DIMS_LABEL_MODIFY') : dims_constant::getVal('_DIRECTORY_ADDNEWCONTACT'); ?>
		</span>
	</div>
	<?php if (!empty($errors)) : ?>
		<div class="errors">
			<?= $errors; ?>
		</div>
	<?php endif; ?>
	<table>
		<tr>
			<td>
				<?= dims_constant::getVal('_DIMS_LABEL_NAME'); ?>
			</td>
			<td>
				<input class="text" type="text" style="width:350px;float:left;" id="ct_lastname" name="ct_lastname" value="<?= $contact['lastname']; ?>" tabindex="2" />
			</td>
		</tr>
		<tr>
			<td>
				<?= dims_constant::getVal('_DIMS_LABEL_FIRSTNAME'); ?></td>
			<td>
				<input class="text" type="text" style="width:350px;" id="ct_firstname" name="ct_firstname" value="<?= $contact['firstname']; ?>" tabindex="2" />
			</td>
		</tr>

		<tr>
			<td>
				<?= dims_constant::getVal('_DIMS_LABEL_EMAIL'); ?>
			</td>
			<td>
				<input class="text" type="text" style="width:350px;float:left;" id="ct_lastname" name="ct_email" value="<?= $contact['email']; ?>" tabindex="3" />
			</td>
		</tr>
	</table>
	</div>
	<div id="sharefile_button" style="padding:2px;clear:both;float:left;width:100%;">
		<span style="width:50%;display:block;float:left;">&nbsp;</span>
		<span style="width:50%;display:block;float:left;"><input type="submit" value="<?= dims_constant::getVal('_DIMS_SAVE'); ?>"></span>
	</div>
</div>
</form>
