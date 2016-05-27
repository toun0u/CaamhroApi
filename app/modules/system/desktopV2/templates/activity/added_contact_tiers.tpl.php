<?
if (isset($this->contacts)){
?>
<div id="added_<? echo $this->fields['id']; ?>">
	<div class="title_looking_existing_contact">
		<?
		if (file_exists($this->getPhotoPath(24)))
			echo '<img class="activity_img_tiers" src="'.$this->getPhotoWebPath(24).'" style="float:left;" />';
		else
			echo '<img class="activity_img_tiers" src="'._DESKTOP_TPL_PATH.'/gfx/common/company.png" style="float:left;" />';
		?>
		<span style="padding-left:5px;"><? echo dims_strcut($this->fields['intitule'],25); ?></span>
	</div>
	<div class="existing_contact">
		<?
		foreach($this->contacts as $ct){
			$ct->setLightAttribute('idtiers',$this->fields['id']);
			$ct->display(_DESKTOP_TPL_LOCAL_PATH.'/activity/added_contact.tpl.php');
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
