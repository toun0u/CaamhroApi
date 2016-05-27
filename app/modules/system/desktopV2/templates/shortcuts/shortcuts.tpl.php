<div class="title_shortcuts title_zone_droite">
	<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/<?php echo (isset($_SESSION['desktopV2']['content_droite']['zone_shortcuts']) && $_SESSION['desktopV2']['content_droite']['zone_shortcuts'] == 0) ? 'deplier_menu.png' : 'replier_menu.png'; ?>" border="0" onclick="javascript:$('div.zone_shortcuts').slideToggle('fast',flip_flop($('div.zone_shortcuts'),$(this),'<?php echo _DESKTOP_TPL_PATH; ?>'));" />
	<!--<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/shortcuts_params.png" border="0" />-->
	<h2 class="shortcuts_h2"><?php echo $_SESSION['cste']['_DIMS_LABEL_SHORCUTS']; ?></h2>
</div>
<div class="zone_shortcuts" <?php if(isset($_SESSION['desktopV2']['content_droite']['zone_shortcuts']) && $_SESSION['desktopV2']['content_droite']['zone_shortcuts'] == 0) echo 'style="display:none;"'; ?>>
	<?
	$lstShort = $desktop->getShortcuts();
	$classshort="classshortcut";

	foreach ($lstShort as $shortcut) {

		if (isset($shortcut['sep']) && $shortcut['sep']) $style="clear:both;";
		else $style='';

		echo '<div class="'.$classshort.'" style="'.$style.'"><a href="'.$shortcut['link'].'"><img src="'.$shortcut['img'].'" border="0"><div>'.$shortcut['title'].'</div></a></div>';
	}
	?>

</div>
