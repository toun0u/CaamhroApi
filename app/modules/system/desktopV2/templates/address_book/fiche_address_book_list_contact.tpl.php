<table cellspacing="0" cellpadding="0" onclick="javascript: addressBookSelect(<? echo $this->fields['id']; ?>,<? echo dims_const::_SYSTEM_OBJECT_CONTACT; ?>,'<? echo _DESKTOP_TPL_PATH; ?>',this);">
	<tbody>
		<tr>
			<td class="icon_fiche_contacts_AB">
				<?
				if ($this->getPhotoWebPath(40) != '' && file_exists($this->getPhotoPath(40)))
					echo '<img class="image_address_book" src="'.$this->getPhotoWebPath(40).'" border="0" />';
				else
					echo '<img src="'._DESKTOP_TPL_PATH.'/gfx/common/human40.png" border="0" />';

				?>
			</td>
			<td class="text_fiche_text_AB">
				<span class="title_text_fiche_text_AB">
					<? echo $this->fields['lastname']." ".$this->fields['firstname']; ?>
				</span>
				<span class="desc_text_fiche_text_AB">
					<?
					$employeur = $this->getLightAttribute('employeur');
					echo $employeur['intitule'];
					?>
				</span>
			</td>
			<td class="icon_fiche_nav_AB">
				<?
				$t = isset($_SESSION['desktopv2']['adress_book']['sel_type']) && $_SESSION['desktopv2']['adress_book']['sel_type'] == dims_const::_SYSTEM_OBJECT_CONTACT && isset($_SESSION['desktopv2']['adress_book']['sel_id']) && $_SESSION['desktopv2']['adress_book']['sel_id'] == $this->fields['id'];
				?>
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/<? if ($t) echo 'selected_'; ?>item_fleche.png" border="0">
			</td>
		</tr>
	</tbody>
</table>

