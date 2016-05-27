<table style="width: 100%;">
	<tbody>
		<tr>
			<td style="width:36px;height:36px;">
				<?
				if (file_exists($this->getPhotoPath()))
					echo '<img class="activity_img_tiers2" style="width:24px;height:24px;" src="'.$this->getPhotoWebPath().'" />';
				else
					echo '<img class="activity_img_tiers2" style="width:24px;height:24px;" src="'._DESKTOP_TPL_PATH.'/gfx/common/company.png" />';
				?>
			</td>
			<td style="width: 600px;line-height:36px;">
				<? echo $this->fields['intitule']; ?>
			</td>
			<td style="line-height: 36px;">
				<img onclick="javascript:$('#id_tier').val(<? echo $this->fields['id']; ?>).closest('form').submit();" style="float:right;cursor:pointer;" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/add.png" />
			</td>
		</tr>
	</tbody>
</table>
