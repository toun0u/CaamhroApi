<?php
$last = $this->getLightAttribute('last');
?>
<tr class="content_details <?php if(isset($last) && $last) echo ' last'; ?>">
	<td style="width:10%;">
		<img style="width:36px;" class="activity_img_ct" src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/groupe20.png" border="0" />
	</td>
	<td style="width:50%;">
		<span><?= $this->get('label'); ?></span>
	</td>
	<td  style="width:37%;">
		<?php
		// NB contacts attachés
		echo $_SESSION['cste']['_DIMS_LABEL_PROJ_PERS_ATTACHED']." : ".$this->getNbObjLinked(contact::MY_GLOBALOBJECT_CODE);
		?>
	</td>
	<td  style="width:3%;">
		<img onclick="javascript:addGroupInActivity(<?= $this->fields['id']; ?>);" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/add.png" style="float:left;cursor:pointer;" />
	</td>
</tr>
