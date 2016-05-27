<?php
$last = $this->getLightAttribute('last');
?>
<tr class="content_details <?php if(isset($last) && $last) echo ' last'; ?>">
	<td style="width:24px;">
		<?
		if (file_exists($this->getPhotoPath(24)))
			echo '<img class="activity_img_tiers2" src="'.$this->getPhotoWebPath(24).'" />';
		else
			echo '<img class="activity_img_tiers2" style="width:24px;height:24px;" src="'._DESKTOP_TPL_PATH.'/gfx/common/company.png" />';
		?>
	</td>
	<td style="padding-top:13px;padding-left:5px;">
		<? echo $this->fields['intitule']; ?>
	</td>
	<td style="padding-top:9px;width:26px;">
		<a class="progressive valid" href="javascript:void(0);" onclick="javascript:selectCompanyOpp(<? echo $this->fields['id']; ?>);" />
	</td>
</tr>