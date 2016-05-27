<?php
$a_dt = explode('-', $this->fields['datefin']);
?>

<div id="activityDates">
	<table class="ro_calendar clear">
		<tr>
			<td class="bloc_calendar">
				<table cellspacing="0" cellpadding="0" width="100%">
					<tbody>
						<tr>
							<td align="center" class="calendar_top"><?php echo date('M', mktime(0, 0, 0, $a_dt[1])); ?>. <?php echo $a_dt[0]; ?></td>
						</tr>
						<tr>
							<td align="center" class="calendar_bot"><?php echo $a_dt[2]; ?></td>
						</tr>
						</tbody>
				</table>
			</td>
		</tr>
	</table>
</div>

<h3>Description</h3>
<p class="frame"><?php echo stripslashes($this->getDescriptionHTML()); ?></p>

<h3>Compte lié</h3>

<p class="frame">
	<?php
	$tiers = new tiers();
	if ($tiers->open($this->fields['tiers_id'])) {
		if ($tiers->fields['photo'] != '') {
			$photoPath = $tiers->getPhotoWebPath(60);
		}
		else {
			$photoPath = _DESKTOP_TPL_PATH.'/gfx/common/company_default_search.png';
		}
		?>

		<img class="left" src="<?php echo $photoPath; ?>" alt="<?php echo $tiers->fields['intitule']; ?>" />

		<?php echo $tiers->fields['intitule']; ?><br/>
		<?php
		if ($tiers->fields['mel'] != '') {
			?>
			Email : <a href="mailto:<?php echo $tiers->fields['mel']; ?>"><?php echo $tiers->fields['mel']; ?></a><br/>
			<?php
		}
		if ($tiers->fields['telephone'] != '') {
			?>
			Téléphone : <?php echo $tiers->fields['telephone']; ?>
			<?php
		}
	}
	?>
</p>

<?php
if ($this->fields['opportunity_partner_id'] > 0) {
	?>
	<h3>Partenaire</h3>

	<p class="frame">
		<?php
		$partner = new tiers();
		if ($partner->open($this->fields['opportunity_partner_id'])) {
			if ($partner->fields['photo'] != '') {
				$photoPath = $partner->getPhotoWebPath(60);
			}
			else {
				$photoPath = _DESKTOP_TPL_PATH.'/gfx/common/company_default_search.png';
			}
			?>

			<img class="left" src="<?php echo $photoPath; ?>" alt="<?php echo $partner->fields['intitule']; ?>" />

			<?php echo $partner->fields['intitule']; ?><br/>
			<?php
			if ($partner->fields['mel'] != '') {
				?>
				Email : <a href="mailto:<?php echo $partner->fields['mel']; ?>"><?php echo $partner->fields['mel']; ?></a><br/>
				<?php
			}
			if ($partner->fields['telephone'] != '') {
				?>
				Téléphone : <?php echo $partner->fields['telephone']; ?>
				<?php
			}
		}
		?>
	</p>
	<?php
}
?>

<h3>Participants</h3>

<div class="frame">
	<?php
	$a_contacts = array();
	if (!empty($linkedObjectsIds['distribution']['contacts'])) {
		$params = array();
		$rs = $db->query('
			SELECT	c.*, t.*
			FROM	dims_mod_business_contact c
			LEFT JOIN	dims_mod_business_tiers_contact tc
			ON			tc.id_contact = c.id
			AND			tc.type_lien = \'employer\'
			LEFT JOIN	dims_mod_business_tiers t
			ON			t.id = tc.id_tiers
			WHERE	c.id_globalobject IN ('.$db->getParamsFromArray(array_keys($linkedObjectsIds['distribution']['contacts']), 'idglobalobject', $params).')
			GROUP BY c.id', $params);
		if ($db->numrows($rs)) {
			foreach ($separation = $db->split_resultset($rs) as $sep) {
				if ($sep['c']['photo'] != '') {
					$contact = new contact();
					$contact->openFromResultSet($sep['c']);
					$sep['c']['photoPath'] = $contact->getPhotoWebPath(40);
				}
				else {
					$sep['c']['photoPath'] = _DESKTOP_TPL_PATH.'/gfx/common/human40.png';
				}
				?>

				<table class="w100 bb1">
				<tr>
					<td class="w20p txtcenter">
						<img src="<?php echo $sep['c']['photoPath']; ?>" alt="<?php echo $sep['c']['firstname'].' '.$sep['c']['lastname']; ?>" title="<?php echo $sep['c']['firstname'].' '.$sep['c']['lastname']; ?>" />
					</td>
					<td><?php echo $sep['c']['firstname'].' '.$sep['c']['lastname']; ?></td>
					<td class="w20p txtcenter">
						<a href="<?php echo $dims->getScriptEnv().'?submenu='._DESKTOP_V2_CONCEPTS.'&id='.$sep['c']['id'].'&type='.dims_const::_SYSTEM_OBJECT_CONTACT.'&init_filters=1'; ?>" title="Suivre l'activité de cette personne">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/cube20.png" alt="Suivre l'activité de cette personne" />
						</a>
					</td>
					<td class="w20p txtcenter">
						<?php
						if ($sep['c']['email'] != '') {
							?>
							<a href="mailto:<?php echo $sep['c']['email']; ?>" title="Envoyer un email à cette personne">
								<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/email20.png" alt="Envoyer un email à cette personne" />
							</a>
							<?php
						}
						?>
					</td>
				</tr>
				</table>

				<?php
			}
		}
	}
	else {
		echo 'Aucun participant.';
	}
	?>
</div>
