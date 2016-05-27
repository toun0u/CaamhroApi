<link type="text/css" rel="stylesheet" href="<?php echo _DESKTOP_TPL_PATH; ?>/contextual_infos/css/styles.css" media="screen" />

<div class="title_infos">
    <h2 class="infos_h2" style="float:left">Responsable de l'activité</h2>
	<img style="float:right;" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/<?php echo (isset($_SESSION['desktopV2']['content_droite']['zone_infos_1']) && $_SESSION['desktopV2']['content_droite']['zone_infos_1'] == 0) ? 'deplier_menu.png' : 'replier_menu.png'; ?>" border="0" onclick="javascript:$('div.zone_infos_1').slideToggle('fast',flip_flop($('div.zone_infos_1'),$(this),'<?php echo _DESKTOP_TPL_PATH; ?>'));" />
</div>
<div class="zone_infos_1" <?php if(isset($_SESSION['desktopV2']['content_droite']['zone_infos_1']) && $_SESSION['desktopV2']['content_droite']['zone_infos_1'] == 0) echo 'style="display:none;"'; ?>>
	<?php
	if ($_SESSION['desktopv2']['mode'] == 'activity' && isset($activity->fields)) {
		if ($activity->fields['id_responsible']) {
			$resp = new contact();
			$resp->getByIdUser($activity->fields['id_responsible']);
		}
	}
	elseif ($_SESSION['desktopv2']['mode'] == 'leads' && isset($lead->fields)) {
		if ($lead->fields['id_responsible']) {
			$resp = new contact();
			$resp->getByIdUser($lead->fields['id_responsible']);
		}
	}

	if (isset($resp)) {
		if ($resp->fields['photo'] != '') {
			$photoPath = $resp->getPhotoWebPath(60);
		}
		else {
			$photoPath = _DESKTOP_TPL_PATH.'/gfx/common/human60.png';
		}
		?>
		<table>
		<tr>
			<td valign="top"><img src="<?php echo $photoPath; ?>" alt="<?php echo $resp->fields['firstname'].' '.$resp->fields['lastname']; ?>" /></td>
			<td valign="top">
				<table>
				<tr>
					<td class="infos_resp_name" colspan="2"><?php echo $resp->fields['firstname'].' '.$resp->fields['lastname']; ?></td>
				</tr>
				<tr>
					<td align="right">Email :</td>
					<td><a href="mailto:<?php echo $resp->fields['email']; ?>"><?php echo $resp->fields['email']; ?></a></td>
				</tr>
				<tr>
					<td align="right">Téléphone :</td>
					<td><strong>06 12 34 56 78</strong></td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
		<?php
	}
	?>
</div>

<?php
if (isset($activity)) {
	// chargement des alertes (1 seule par activité)
	$a_alerts = dims_alert::getAllByGOOrigin($activity->fields['id_globalobject']);
	if (sizeof($a_alerts)) {
		$alert = $a_alerts[0];
		$a_da = dims_timestamp2local($alert->fields['timestp_alert']);
		?>
		<div class="title_infos">
		    <h2 class="infos_h2" style="float:left">Alerte programmée</h2>
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/<?php echo (isset($_SESSION['desktopV2']['content_droite']['zone_infos_2']) && $_SESSION['desktopV2']['content_droite']['zone_infos_2'] == 0) ? 'deplier_menu.png' : 'replier_menu.png'; ?>" border="0" onclick="javascript:$('div.zone_infos_2').slideToggle('fast',flip_flop($('div.zone_infos_2'),$(this),'<?php echo _DESKTOP_TPL_PATH; ?>'));" />
		</div>

		<div class="zone_infos_2" <?php if(isset($_SESSION['desktopV2']['content_droite']['zone_infos_2']) && $_SESSION['desktopV2']['content_droite']['zone_infos_2'] == 0) echo 'style="display:none;"'; ?>>
			<p>Alerte programmée le <strong><?php echo $a_da['date']; ?></strong> à <strong><?php echo substr($a_da['time'], 0, -3); ?></strong></p>
		</div>
		<?php
	}
}
