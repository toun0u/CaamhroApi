<?
if (isset($this->contacts)){
	$willBeLinked = false;
	if(isset($_SESSION['desktopv2']['opportunity']['tiers_tolink'][$this->fields['id']]) && $_SESSION['desktopv2']['opportunity']['tiers_tolink'][$this->fields['id']] == _TIER_LINK) {
		$willBeLinked = true;
	}
?>
<div id="added_<? echo $this->fields['id']; ?>">
	<div class="title_looking_existing_contact <?php echo ($willBeLinked) ? 'keep' : 'dontkeep'; ?>">
		<?
		if (file_exists($this->getPhotoPath(24)))
			echo '<img class="opportunity_img_tiers" src="'.$this->getPhotoWebPath(24).'" style="float:left;" />';
		else
			echo '<img class="opportunity_img_tiers" src="'._DESKTOP_TPL_PATH.'/gfx/common/company.png" style="float:left;" />';
		?>
		<?php
		if(!$this->isNew()) {
			if(!$willBeLinked) {
				?>
				<input type="checkbox" onclick="Javascript: keepCompany(<? echo $this->fields['id']; ?>);" style="float: left;" />
				<?php
			}
			else {
				?>
				<input type="checkbox" onclick="Javascript: DontkeepCompany(<? echo $this->fields['id']; ?>);" checked="checked" style="float: left;" />
				<?php
			}
		}
		?>
		<span style="padding-left:5px;"><? echo dims_strcut($this->fields['intitule'],25); ?></span>
		<img onclick="javascript:ununselectCompanyOpp(<? echo $this->fields['id']; ?>);" style="cursor: pointer;float: right;" src="<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/close.png" />
	</div>
	<div class="existing_contact">
		<?
		foreach($this->contacts as $ct) {
			$ct->setLightAttribute('idtiers',$this->fields['id']);
			$ct->display(_DESKTOP_TPL_LOCAL_PATH.'/opportunity/added_contact.tpl.php');
		}
		?>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$("div#added_<? echo $this->fields['id']; ?> div.existing_contact table:last").css("border-bottom","none");
	});
</script>
<?
}
?>
