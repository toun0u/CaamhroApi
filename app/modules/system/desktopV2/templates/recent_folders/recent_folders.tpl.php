<link type="text/css" rel="stylesheet" href="<?php echo _DESKTOP_TPL_PATH; ?>/recent_folders/css/styles.css" media="screen" />
<div class="title_zone_droite">
	<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/<?php echo (isset($_SESSION['desktopV2']['content_droite']['zone_folders']) && $_SESSION['desktopV2']['content_droite']['zone_folders'] == 0) ? 'deplier_menu.png' : 'replier_menu.png'; ?>" border="0" onclick="javascript:$('div.zone_folders').slideToggle('fast',flip_flop($('div.zone_folders'),$(this),'<?php echo _DESKTOP_TPL_PATH; ?>'));" />
	<h2><?php echo $_SESSION['cste']['_RECENT_FOLDERS']; ?></h2>
</div>
<div class="zone_folders" <?php if(isset($_SESSION['desktopV2']['content_droite']['zone_folders']) && $_SESSION['desktopV2']['content_droite']['zone_folders'] == 0) echo 'style="display:none;"'; ?>>
	<?
	$lstFolders = $desktop->getRecentFolders();
	?>
    <table cellspacing="10" cellpadding="0">
		<?
		$i = 0;
		foreach($lstFolders as $folder){
			switch($i){
				case 0:
					echo '<tr>';
					break;
				case 3;
					echo '</tr>';
					$i = 0;
					break;
			}
			$i++;
			?>
			<td>
				<img src="<?php echo $folder['img']; ?>" border="0">
			</td>
			<td>
                <a href="<? echo $folder['link']; ?>">
					<div>
						<? echo $folder['title']; ?>
					</div>
				</a>
            </td>
			<?
		}
		?>
    </table>
</div>