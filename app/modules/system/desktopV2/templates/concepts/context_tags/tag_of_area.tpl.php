<?php
global $lstObj;
?>

<div class="title_concept_droite">
	<h2 class="title_period_activity">
		<? echo $_SESSION['cste']['AREA_OF_ACTIVITY_OF']; ?> <?php echo dims_strcut(($_SESSION['desktopv2']['concepts']['sel_type'] == dims_const::_SYSTEM_OBJECT_CONTACT) ? substr($this->fields['firstname'],0,1).". ".$this->fields['lastname'] : (isset($this->title) ? $this->title : ""), 25); ?>
	</h2>
</div>
<div class="context_tag">
	<?php
	if (isset($lstObj['countries'])) {
		foreach ($lstObj['countries'] as $country) {
			?>
			<a href="javascript:void(0);" onclick="javascript:document.location.href='/admin.php?action=add_filter&filter_type=country&filter_value=<?php echo $country->fields['id']; ?>';">
				<span><?php echo $country->fields['printable_name']; ?></span>
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/focus_on_activity16.png" />
			</a>
			<?php
		}
	}
	?>
</div>
