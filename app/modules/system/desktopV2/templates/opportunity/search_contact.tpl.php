<table style="width: 100%;border-bottom:1px solid #D6D6D6;">
	<tbody>
		<tr>
			<td style="width:10%;">
				<?
				if ($this->getPhotoWebPath(36) != '' && file_exists($this->getPhotoPath(36)))
					echo '<img class="opportunity_img_ct" src="'.$this->getPhotoWebPath(36).'" border="0" />';

				else
					echo '<img style="width:30px;" class="opportunity_img_ct" src="'._DESKTOP_TPL_PATH.'/gfx/common/contact_default_search.png" border="0" />';
				?>
			</td>
			<td style="width:50%;">
				<!-- <img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/connected.png" /> -->
				<span><? echo $this->fields['firstname']." ".$this->fields['lastname']; ?></span>
			</td>
			<td  style="width:3%;">
				<img onclick="javascript:displayDetailContactInOpportunity(<? echo $this->fields['id']; ?>,<? echo $this->getLightAttribute('idtiers'); ?>);" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/previsu.png" style="float:left;cursor:pointer;" />
			</td>
			<td  style="width:3%;">
				<img onclick="javascript:addContactInOpportunity(<? echo $this->fields['id']; ?>,<? echo $this->getLightAttribute('idtiers'); ?>);" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/add.png" style="float:left;cursor:pointer;" />
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<div id="detail_search_ct_<? echo $this->fields['id']; ?>"></div>
			</td>
		</tr>
	</tbody>
</table>