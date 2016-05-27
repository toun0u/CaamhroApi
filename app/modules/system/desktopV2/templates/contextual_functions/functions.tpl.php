<link type="text/css" rel="stylesheet" href="<?php echo _DESKTOP_TPL_PATH; ?>/contextual_functions/css/styles.css" media="screen" />
<?
if ( $mode == 'appointment_offer' || ($mode == 'planning' && isset($_SESSION['desktopv2']['appointment_offer'])) )
	$_SESSION['desktopV2']['content_droite']['zone_functions'] = 1;

$functions = $desktop->getContextualFunctions();
if(count($functions)){
?>
	<div class="title_functions" style="overflow: hidden;">
		<h2 class="functions_h2" style="float:left;padding-top: 10px;"><?php echo $_SESSION['cste']['CONTEXTUAL_FUNCTIONS']; ?></h2>
		<img style="float: right;padding-top: 10px;" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/<?php echo (isset($_SESSION['desktopV2']['content_droite']['zone_functions']) && $_SESSION['desktopV2']['content_droite']['zone_functions'] == 0) ? 'deplier_menu.png' : 'replier_menu.png'; ?>" border="0" onclick="javascript:$('div.zone_functions').slideToggle('fast',flip_flop($('div.zone_functions'),$(this),'<?php echo _DESKTOP_TPL_PATH; ?>'));" />
	</div>
	<div class="zone_functions" <?php if(isset($_SESSION['desktopV2']['content_droite']['zone_functions']) && $_SESSION['desktopV2']['content_droite']['zone_functions'] == 0) echo 'style="display:none;"'; ?>>
		<?php
		foreach ($functions as $function) {
			?>
			<p>
				<a style="line-height: 24px;" href="<?php echo $function['link']; ?>" title="<?php echo $function['title']; ?>"><img style="float: left;" src="<?php echo $function['img']; ?>" alt="<?php echo $function['title']; ?>" /><?php echo $function['title']; ?></a>
			</p>
			<?php
		}
		if ( ($mode == 'appointment_offer' || ($mode == 'planning' && isset($_SESSION['desktopv2']['appointment_offer']))) && $action != 'manage' ) {
			unset($_SESSION['desktopv2']['appointment_offer']['days']);
			?>
			<div id="appointmentOfferSelection">
				<h4>
					Dates s&eacute;lectionn&eacute;es :
				</h4>
				<div id="appointmentOfferSelectedDays" style="margin-bottom:10px;"></div>
				<?php
				if ($mode == 'planning') {
					$backLink = $dims->getScriptEnv().'?submenu='.dims_const_desktopv2::DESKTOP_V2_DESKTOP.'&mode=appointment_offer&action=edit';
					if (!empty($_SESSION['desktopv2']['appointment_offer']['id'])) {
						$backLink .= '&app_offer_id='.$_SESSION['desktopv2']['appointment_offer']['id'];
					}
					?>
					<input type="button" value="Valider les dates et confirmer" onclick="javascript:document.location.href='<?php echo $dims->getScriptEnv().'?submenu='.dims_const_desktopv2::DESKTOP_V2_DESKTOP.'&mode=appointment_offer&action=save'; ?>';" />
					ou <a href="javascript:void();" onclick="javascript:document.location.href='<?php echo $backLink; ?>';">Annuler</a>
					<?php
				}
				?>
			</div>
			<?php
		}
		?>
	</div>
<?php
}
?>
