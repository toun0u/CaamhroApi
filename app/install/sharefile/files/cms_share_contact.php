<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<form name="form_etape1" method="post" action="<? echo dims_urlencode($dims->getScriptEnv()."?op=sharefile_valid_contact"); ?>">
<div  style="float:left; width:80%;padding-top:20px;">
	<div style="padding:2px;">
		<span style="width:10%;display:block;float:left;">
			<img src="/modules/sharefile/img/users.png">
		</span>
		<span style="width:90%;display:block;float:left;font-size:20px;color:#BABABA;font-weight:bold;">
			<?
			if ($op=="sharefile_new_contact") {
				echo $_DIMS['cste']['_DIRECTORY_ADDNEWCONTACT'];
			}
			else echo $_DIMS['cste']['_DIMS_LABEL_MODIFY'];
			?>
		</span>
	</div>
	<table>
		<tr><td>
			<? echo $_DIMS['cste']['_DIMS_LABEL_NAME']; ?>
			</td>
			<td>
				<input class="text" type="text" style="width:350px;float:left;" id="ct_lastname" name="ct_lastname" value="<? echo $contact->fields['lastname']; ?>" tabindex="2" />
			</td>
		</tr>
		<tr>
			<td>
			<? echo $_DIMS['cste']['_DIMS_LABEL_FIRSTNAME']; ?></td>
			<td><input class="text" type="text" style="width:350px;" id="ct_firstname" name="ct_firstname" value="<? echo $contact->fields['firstname']; ?>" tabindex="2" />
			</td>
		</tr>

		<tr>
			<td><? echo $_DIMS['cste']['_DIMS_LABEL_EMAIL']; ?></td>
			<td><input class="text" type="text" style="width:350px;float:left;" id="ct_lastname" name="ct_email" value="<? echo $contact->fields['email']; ?>" tabindex="3" /></td>
		</tr>
	</table>
	</div>
	<div id="sharefile_button" style="padding:2px;clear:both;float:left;width:100%;">
		<span style="width:50%;display:block;float:left;">&nbsp;</span>
		<span style="width:50%;display:block;float:left;"><input type="submit" value="Enregistrer"></span>
	</div>
</div>
</form>
