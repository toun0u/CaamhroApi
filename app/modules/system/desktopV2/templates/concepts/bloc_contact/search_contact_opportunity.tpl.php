<table style="width: 100%; border-collapse: collapse;border-bottom:1px solid #D6D6D6">
	<tbody>
		<tr>
			<td style="width: 36px">
				<?
				if ($this->getPhotoWebPath() != '' && file_exists($this->getPhotoPath()))
					echo '<img class="opportunity_img_ct" src="'.$this->getPhotoWebPath().'" border="0" />';
				else
					echo '<img class="opportunity_img_ct" src="'._DESKTOP_TPL_PATH.'/gfx/common/contact_default_search.png" border="0" />';
				?>
			</td>
			<td style="width: 600px;">
				<!-- <img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/connected.png" /> -->
				<span><? echo $this->fields['firstname']." ".$this->fields['lastname']; ?></span>
			</td>
			<td>
				<img onclick="javascript:linkExistingContact(<?php echo $this->fields['id']; ?>, <?php echo $_SESSION['desktopv2']['opportunity']['tiers_selected']; ?>)" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/add.png" style="float:left;cursor:pointer;" />
			</td>
	</tbody>
</table>
