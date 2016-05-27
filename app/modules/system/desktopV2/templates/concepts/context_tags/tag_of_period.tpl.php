<?php
global $lstObj;
?>

<div class="title_concept_droite">
	<h2 class="title_period_activity">
		<? echo $_SESSION['cste']['PERIOD_OF_ACTIVITY_OF']; ?> <? echo dims_strcut(($_SESSION['desktopv2']['concepts']['sel_type'] == dims_const::_SYSTEM_OBJECT_CONTACT) ? substr($this->fields['firstname'],0,1).". ".$this->fields['lastname'] : (isset($this->title) ? $this->title : ""), 25); ?>
	</h2>
</div>
<div class="context_tag">
	<?php
	if (isset($lstObj['years'])) {
		foreach ($lstObj['years'] as $year) {
			?>
			<a href="javascript:void(0);" onclick="javascript:document.location.href='/admin.php?action=add_filter&filter_type=year&filter_value=<?php echo $year; ?>';">
				<span><?php echo $year; ?></span>
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/focus_on_activity16.png" />
			</a>
			<?php
		}
	}
	?>
</div>
