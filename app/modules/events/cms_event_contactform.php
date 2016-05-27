<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
//gestion des informations personnelles
require_once(DIMS_APP_PATH . '/modules/system/class_contact.php');
$ct = new contact();
$ct->open($_SESSION['dims']['user']['id_contact']);
?>
	<script language="javascript">
	function verif_form(form_id) {
		if(dims_validatefield('<?php echo $_DIMS['cste']['_DIMS_LABEL_NAME']; ?>',document.getElementById("ct_lastname"), 'string') &&
			dims_validatefield('<?php echo $_DIMS['cste']['_FIRSTNAME']; ?>',document.getElementById("ct_firstname"), 'string')) {
			document.getElementById(form_id).submit();
		}
		else {
			return false;
		}
	}
	</script>
	<div id="form_1">
		<?
		if (true) { //!$is_dims_user -> tous les inscrits deviennent des users, ils n'ont cependant pas accès à I-net pour modifier leurs infos
		?>

		<p style="font-size:13px;"><?php echo $_DIMS['cste']['_DIMS_LABEL_PERSONNAL_INFOS']; ?></p>

		<form action="<?php echo dims_urlencode('index.php?action=modif_ct',true); ?>" method="POST" id="pers_data" name="pers_data">
		<input type="hidden" name="id_ct" value="<?php echo $_SESSION['dims']['user']['id_contact']; ?>"/>
		<div class="inscriptions">
			<div class="info_oblig" style="width:50%">
			<?php

			echo '
				<table width="50%" cellpadding="1" cellspacing="8" border="0">
					<tr>
						<td align="left" width="20%">
							<label for="ct_lastname">'.$_DIMS['cste']['_DIMS_LABEL_NAME'].'</label>
						</td>
					</tr>
					<tr>
						<td align="left" style="font-size:13px;">
							'.strtoupper($ct->fields['lastname']).'
						</td>
					</tr>
					<tr>
						<td align="left">
							<label for="ct_firstname">'.$_DIMS['cste']['_FIRSTNAME'].'</label>
						</td>
					</tr>
					<tr>
						<td align="left" style="font-size:13px;">
							'.$ct->fields['firstname'].'
						</td>
					</tr>
					<tr>
						<td align="left">
							<label for="ct_email">'.$_DIMS['cste']['_DIMS_LABEL_EMAIL'].'</label>
						</td>
					</tr>
					<tr>
						<td align="left">
							<input type="text" name="ct_email" id="ct_email" value="'.$ct->fields['email'].'" class="content"/>
						</td>
					</tr>
					<tr>
						<td align="left">
							<label for="ct_phone">'.$_DIMS['cste']['_PHONE'].'</label>
						</td>
					</tr>
					<tr>
						<td align="left">
							<input type="text" name="ct_phone" id="ct_phone" value="'.$ct->fields['phone'].'" class="content"/>
						</td>
					</tr>
				</table>
			</div>
			<div class="info_compl">
				<table>
					<tr>
						<td align="left">
							<label for="ct_mobile">'.$_DIMS['cste']['_MOBILE'].'</label>
						</td>
					</tr>
					<tr>
						<td align="left">
							<input type="text" name="ct_mobile" id="ct_mobile" value="'.$ct->fields['mobile'].'" class="content"/>
						</td>
					</tr>
					<tr>
						<td align="left">
							<label for="ct_address">'.$_DIMS['cste']['_DIMS_LABEL_ADDRESS'].'</label>
						</td>
					</tr>
					<tr>
						<td align="left">
							<input type="text" name="ct_address" id="ct_address" value="'.$ct->fields['address'].'" class="content"/>
						</td>
					</tr>
					<tr>
						<td align="left">
							<label for="ct_city">'.$_DIMS['cste']['_DIMS_LABEL_CITY'].'</label>
						</td>
					</tr>
					<tr>
						<td align="left">
							<input type="text" name="ct_city" id="ct_city" value="'.$ct->fields['city'].'" class="content"/>
						</td>
					</tr>
					<tr>
						<td align="left">
							<label for="ct_postalcode">'.$_DIMS['cste']['_DIMS_LABEL_CP'].'</label>
						</td>
					</tr>
					<tr>
						<td align="left">
							<input type="text" name="ct_postalcode" id="ct_postalcode" value="'.$ct->fields['postalcode'].'" class="content"/>
						</td>
					</tr>
					<tr>
						<td align="left">
							<label for="ct_country">'.$_DIMS['cste']['_DIMS_LABEL_COUNTRY'].'</label>
						</td>
					</tr>
					<tr>
						<td align="left">
							<input type="text" name="ct_country" id="ct_country" value="'.$ct->fields['country'].'" class="content"/>
						</td>
					</tr>
				</table>
				</form>
				<div class="save">
					<input type="submit" value="'.$_DIMS['cste']['_DIMS_SAVE'].' >" class="submit" />
				</div>
			</div>';
		}
		else {
			echo '<table width="100%"><tr><td width="100%" style="font-size:14px;" align="center">
					<div style="margin-left: 15px;">
					'.$_DIMS['cste']['_DIMS_FRONT_TEXT_NO_EVENT'].'
					</div>
					</td></tr></table>';
		}
?>
