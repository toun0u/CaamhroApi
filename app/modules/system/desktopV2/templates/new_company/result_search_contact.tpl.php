<?php
$last = $this->getLightAttribute('last');
?>
<tr class="content_details <?php if(isset($last) && $last) echo ' last'; ?>">
	<td style="width:10%;">
		<?
		if ($this->getPhotoWebPath(36) != '' && file_exists($this->getPhotoPath(36)))
			echo '<img class="activity_img_ct" src="'.$this->getPhotoWebPath(36).'" border="0" />';

		else
			echo '<img style="width:36px;" class="activity_img_ct" src="'._DESKTOP_TPL_PATH.'/gfx/common/contact_default_search.png" border="0" />';
		?>
	</td>
	<td style="width:50%;">
		<!-- <img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/connected.png" /> -->
		<span><? echo $this->fields['firstname']." ".$this->fields['lastname']; ?></span>
	</td>
	<td  style="width:3%;">
		<?php
		//récupération des entreprises qui emploient le contact
		$company = current($this->getCompaniesLinkedByType('_DIMS_LABEL_EMPLOYEUR'));
		if(isset($company) && !empty($company)){
			$idtiers = $company['id'];
		}
		else $idtiers = 0;
		?>
		<img onclick="javascript:displayDetailContactInActivity(<? echo $this->fields['id']; ?>,<? echo $idtiers; ?>);" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/previsu.png" style="float:left;cursor:pointer;" />
	</td>
	<td  style="width:3%;">
		<img onclick="javascript:addContactInActivity(<? echo $this->fields['id']; ?>,<? echo $idtiers; ?>);" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/add.png" style="float:left;cursor:pointer;" />
	</td>
</tr>
<tr class="prepared_tr">
	<td colspan="4">
		<div id="detail_search_ct_<? echo $this->fields['id']; ?>"></div>
	</td>
</tr>
