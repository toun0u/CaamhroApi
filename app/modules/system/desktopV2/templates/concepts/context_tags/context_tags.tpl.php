<a href="javascript:void(0);">
	<span>
	<?
		if (isset($_SESSION['cste'][$this->fields['tag']]))
			echo $_SESSION['cste'][$this->fields['tag']];
		else
			echo $this->fields['tag'];
	?>
	</span>
	<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/focus_on_activity16.png" />
</a>
