<h3>
	<?= ucfirst(strtolower($_SESSION['cste']['ADD_ADDRESS'])); ?>
</h3>
<form method="POST" action="<?= dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=address&action=save_merge" name="merge_addr" style="margin-top:10px;">
	<input type="hidden" name="id_ct" value="<?= $this->getLightAttribute('id_ct'); ?>" />
	<input type="hidden" name="adr_address" value="<?= $this->get('address'); ?>" />
	<input type="hidden" name="adr_address2" value="<?= $this->get('address2'); ?>" />
	<input type="hidden" name="adr_address3" value="<?= $this->get('address3'); ?>" />
	<input type="hidden" name="adr_postalcode" value="<?= $this->get('postalcode'); ?>" />
	<input type="hidden" name="adr_id_city" value="<?= $this->get('id_city'); ?>" />
	<input type="hidden" name="adr_id_country" value="<?= $this->get('id_country'); ?>" />
	<input type="hidden" name="address_type" value="<?= $this->getLightAttribute('address_type'); ?>" />
	<input type="hidden" name="lk_phone" value="<?= $this->getLightAttribute('lk_phone'); ?>" />
	<input type="hidden" name="lk_email" value="<?= $this->getLightAttribute('lk_email'); ?>" />
	<input type="hidden" name="lk_fax" value="<?= $this->getLightAttribute('lk_fax'); ?>" />
	<input type="hidden" name="go_tiers" value="<?= $this->getLightAttribute('go_tiers'); ?>" />
	<input type="hidden" name="type" value="<?= $this->getLightAttribute('type'); ?>" />
	<input type="hidden" name="old_adr" value="<?= $this->get('id'); ?>" />
	<input type="hidden" name="link_to_contacts" value="<?= $this->getLightAttribute('link_to_contacts'); ?>" />
	<?php
	$leven = $this->getLightAttribute('leven');
	foreach($leven as $adr){
		$adr->setLightAttribute('adr_id',$this->get('id'));
		$adr->setLightAttribute('obj_to_link',$this->getLightAttribute('obj_to_link'));
		$adr->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/address/similar_address.tpl.php');
	}
	?>
	<table style="margin-left:15px;">
		<?php if(!isset($leven[$this->get('id')])){ ?>
		<tr>
			<td colspan="2" style="font-weight:bold;">
				<?= $_SESSION['cste']['_NONE_PREVIOUS_ADDRESSES_MATCH']; ?>
			</td>
		</tr>
		<tr>
			<td style="width: 10px;">
				<input type="radio" name="chose_action" value="0" id="new" checked="true" />
			</td>
			<td>
				<label for="new">
					<?= $_SESSION['cste']['_CREATE_NEW_ADDRESS']; ?>
				</label>
			</td>
		</tr>
		<tr>
			<td colspan="2">
		<?php }else{ ?>
		<tr>
			<td>
		<?php } ?>
				<input type="submit" value="<?= $_SESSION['cste']['_DIMS_SAVE']; ?>" />
				<?= $_SESSION['cste']['_DIMS_OR']; ?>
				<a href="javascript:void(0);" onclick="javascript:window.location.reload();">
					<?= $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>
				</a>
			</td>
		</tr>
	</table>
</form>