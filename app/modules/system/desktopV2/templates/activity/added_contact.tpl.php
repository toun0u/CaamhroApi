<?
$_SESSION['desktopv2']['activity']['ct_added'][$this->fields['id']]['id'] = $this->fields['id'];
?>
<table style="width: 100%;border-bottom:1px solid #D6D6D6;">
	<tbody>
		<tr>
			<td style="width:10%;">
				<?
				if ($this->getPhotoWebPath(36) != '' && file_exists($this->getPhotoPath(36)))
					echo '<img class="activity_img_ct" src="'.$this->getPhotoWebPath(36).'" border="0" />';
				else
					echo '<img class="activity_img_ct" src="'._DESKTOP_TPL_PATH.'/gfx/common/human40.png" border="0" />';
				?>
			</td>
			<td style="width:50%;">
				<!-- <img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/connected.png" /> -->
				<span><? echo $this->fields['firstname']." ".$this->fields['lastname']; ?></span>
			</td>
			<td  style="width:3%;">
				<img onclick="javascript:displayDetailContactInActivity2(<? echo $this->fields['id']; ?>,<? echo $this->getLightAttribute('idtiers'); ?>);" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/previsu.png" style="float:left;cursor:pointer;" />
			</td>
			<td  style="width:3%;">
				<img onclick="javascript:delContactInActivity(<? echo $this->fields['id']; ?>,<? echo $this->getLightAttribute('idtiers'); ?>);" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/close.png" style="float:left;cursor:pointer;" />
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<div id="detail_search_ct2_<? echo $this->fields['id']; ?>" class="detail_search_ct2"></div>
			</td>
		</tr>
	</tbody>
</table>
