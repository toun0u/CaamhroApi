<?php
global $data;
?>
<div class="use_existing_vcard" style="float:none;width:100%;">
	<table cellspacing="10" cellpadding="0" style="width: 100%;">
		<tbody>
			<tr>
				<td>
					<?
					if (isset($data['photo']) && file_exists($data['photo']))
						echo '<img src="'.$data['photo'].'" style="float:left;width:70px;" />';
					else{
					?>
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/img_image_vcard.png" style="float:left;" />
					<? } ?>
				</td>
				<td style="width: 70%;">
					<img style="float:left;" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/human_picto_noir.png" />
					<span class="use_existing_vcard_name">
						<? if (isset($data['title'])) echo $data['title']." "; echo $data['prenom']." ".$data['nom']; ?>
					</span>
					<?
					if(isset($data['email']) && count($data['email']) > 0){
					?>
					<span class="use_existing_vcard_email">
						<?
						foreach($data['email'] as $email)
							echo $email."<br />";
						?>
					</span>
					<?
					}
					if (isset($data['tel'])){
						?>
						<span class="use_existing_vcard_name" style="width:100%;">
						<?
						foreach($data['tel'] as $tel){
							echo "$tel<br />";
						}
						?>
						</span>
						<?
					}
					if (isset($data['adr'])){
						?>
						<span class="use_existing_vcard_name" style="width:100%;">
						<?
						foreach($data['adr'] as $adr){
							echo $adr['rue']."<br />".$adr['cp']." ".$adr['city']." ".$adr['pays'];
						}
						?>
						</span>
						<?
					}
					?>
				</td>
			</tr>
		</tbody>
	</table>
</div>