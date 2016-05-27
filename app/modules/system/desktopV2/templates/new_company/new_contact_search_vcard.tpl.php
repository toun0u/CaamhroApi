<table class="use_existing_vcard_search" cellspacing="10" cellpadding="0">
	<tbody>
		<tr>
			<td>
				<span class="title_existing_vcard_search">Click on the vCard(s) you want to add to this activity</span>
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
			<td>
				<div class="searchform">
					<span>
						<input id="button_image_search_vcard" type="image" class="button_search" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_gauche.png" name="button_search" style="float:left;" />
						<input autocomplete="off" onkeyup="javascript:oppSearchVcard(this.value);" type="text" name="editbox_search" class="editbox_search" id="editbox_search_vcard" maxlength="80" value="" onfocus="Javascript:if (this.value=='')this.value='';" onblur="Javascript:if (this.value=='')this.value='';" />
						<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_droite.png" style="float:left;" />
					</span>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<div id="vcard_results" style="max-height:250px;float:left;overflow-x:auto;">
	<?
	$lstVcards = $desktop->getVcard();
	foreach($lstVcards as $vcard)
		$vcard->display(_DESKTOP_TPL_LOCAL_PATH.'/new_company/new_contact_display_vcard.tpl.php');
	?>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		desactiveEnterSubmit('editbox_search_vcard');
		desactiveClicSubmit('button_image_search_vcard');
	});
</script>