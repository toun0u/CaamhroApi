<?
if (isset($this->contacts)){
?>
<div id="search_<? echo $this->fields['id']; ?>">
	<div class="title_looking_existing_contact">
		<?
		if (file_exists($this->getPhotoPath(24)))
			echo '<img class="opportunity_img_tiers" src="'.$this->getPhotoWebPath(24).'" style="float:left;" />';
		else
			echo '<img class="opportunity_img_tiers" src="'._DESKTOP_TPL_PATH.'/gfx/common/company.png" style="float:left;" />';
		?>
		<span style="padding-left:5px;"><? echo $this->fields['intitule']; ?></span>
	</div>
	<div class="existing_contact">
		<?
		foreach($this->contacts as $ct) {
			$ct->setLightAttribute('idtiers',$this->fields['id']);
			$ct->display(_DESKTOP_TPL_LOCAL_PATH.'/opportunity/search_contact.tpl.php');
		}
		?>
	</div>
</div>
<?
}
?>