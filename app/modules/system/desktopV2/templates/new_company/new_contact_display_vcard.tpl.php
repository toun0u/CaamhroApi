<?php
$doc = $this->getDocfile();
$datas = $this->getDatas();
?>
<div class="use_existing_vcard">
	<table cellspacing="10" cellpadding="0" style="width: 100%;">
		<tbody>
			<tr>
				<td>
					<?
					if ($this->fields['id_contact'] != '' && $this->fields['id_contact'] > 0){
						$script = "addContactInActivity(".$this->fields['id_contact'].");oppSearchVcard(document.getElementById('editbox_search_vcard').value,".$_SESSION['desktopv2']['activity']['tiers_selected'].")";
					}else{
						$script = "openDisplayVcardExisting(".$this->fields['id_docfile'].",".$this->fields['num'].")";
					}
					?>
					<img onclick="javascript:<? echo $script; ?>;" style="float:left;cursor:pointer;" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/visu_picto.png" />
					<!--<input onclick="javascript:<? echo $script; ?>;" style="float: left;" type="checkbox" class="button_checkbox" value="<? echo $this->fields['id_docfile']."-".$this->fields['num']; ?>" />-->
				</td>
				<td>
					<?
					if (isset($datas['photo']) && file_exists($datas['photo']))
						echo '<img src="'.$datas['photo'].'" style="float:left;width:70px;" />';
					else{
					?>
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/img_image_vcard.png" style="float:left;" />
					<? } ?>
				</td>
				<td style="width: 70%;">
					<img style="float:left;" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/human_picto_noir.png" />
					<span class="use_existing_vcard_name">
						<? if (isset($datas['title'])) echo $datas['title']." "; echo $datas['prenom']." ".$datas['nom']; ?>
					</span>
					<?
					if(isset($datas['email']) && count($datas['email']) > 0){
					?>
					<span class="use_existing_vcard_email"><? echo current($datas['email']); ?></span>
					<?
					}
					?>
					<span class="use_existing_vcard_date">
						Wainting since the <? $date = dims_timestamp2local($doc->fields['timestp_create']); echo $date['date']; ?>
					</span>
					<span class="use_existing_vcard_add">
						Add by <?
						if ($doc->fields['id_user'] == $_SESSION['dims']['userid'])
							echo 'yourself';
						else{
							$user = new user();
							$user->open($doc->fields['id_user']);
							echo $user->fields['lastname']." ";$user->fields['firstname'];
						}
						?>
					</span>
				</td>
				<!--<td style="vertical-align:top;">
					<img style="float:left" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/visu_picto.png" />
				</td>-->
			</tr>
			<tr>
				<td colspan="4">
					<span class="title_not_existing">
						<?
						if ($this->fields['id_contact'] != '' && $this->fields['id_contact'] > 0){
							$date = dims_timestamp2local($doc->fields['timestp_create']);
							echo "This contact already existing in I-Net since ".$date['date'].".";
						}else{
							echo "This contact is not already existing in I-Net. It will automatically be created.";
						}
						?>
					</span>
				</td>
			</tr>
		</tbody>
	</table>
</div>