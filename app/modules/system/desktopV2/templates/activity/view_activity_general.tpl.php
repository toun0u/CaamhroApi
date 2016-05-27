<?php
$a_df = explode('-', $this->fields['datejour']);
?>

<div id="activityDates">
	<table class="ro_calendar clear">
		<tr>
			<td class="bloc_calendar">
				<table cellspacing="0" cellpadding="0" width="100%">
					<tbody>
						<tr>
							<td align="center" class="calendar_top"><?php echo date('M', mktime(0, 0, 0, $a_df[1])); ?>. <?php echo $a_df[0]; ?></td>
						</tr>
						<tr>
							<td align="center" class="calendar_bot"><?php echo $a_df[2]; ?></td>
						</tr>
						</tbody>
				</table>
			</td>
			<?php
			if ($this->fields['heuredeb'] != '00:00:00') {
				$a_hf = explode(':', $this->fields['heuredeb']);
				?>
				<td align="center">
					<?php
					echo $a_hf[0].':'.$a_hf[1];
					if ( $this->fields['heurefin'] != '00:00:00' && $this->fields['datefin'] == '0000-00-00' ) {
						$a_ht = explode(':', $this->fields['heurefin']);
						echo '<br/>-<br/>'.$a_ht[0].':'.$a_ht[1];
					}
					?>
				</td>
				<?php
			}
			?>
		</tr>
	</table>

	<?php
	if ($this->fields['datefin'] != '0000-00-00') {
		$a_dt = explode('-', $this->fields['datefin']);
		?>
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
				<?php
				if ($this->fields['heurefin'] != '00:00:00') {
					$a_ht = explode(':', $this->fields['heurefin']);
					?>
					<td align="center">
						<?php
						echo $a_ht[0].':'.$a_ht[1];
						?>
					</td>
					<?php
				}
				?>
			</tr>
		</table>
		<?php
	}
	?>
</div>

<h3><?= $_SESSION['cste']['_DIMS_LABEL_DESCRIPTION']; ?></h3>
<p class="frame"><?php echo $this->getDescriptionHTML(); ?></p>

<?php
if (!empty($linkedObjectsIds['distribution']['opportunities'])) {
	?>
	<h3>Opportunité(s) liée(s)</h3>

	<?php
	$params = array();
	$rs = $db->query('
		SELECT a.*, c.*
		FROM dims_mod_business_action a
		INNER JOIN dims_mod_business_contact c
		ON c.id = a.id_responsible
		WHERE a.id_globalobject IN ('.$db->getParamsFromArray(array_keys($linkedObjectsIds['distribution']['opportunities']), 'idglobalobject', $params).')', $params);
	if ($db->numrows($rs)) {
		foreach ($separation = $db->split_resultset($rs) as $sep) {
			?>
			<p class="frame">
				<?php echo stripslashes($sep['a']['libelle']); ?><br/>
				<strong>Responsable</strong> : <?php echo $sep['c']['firstname'].' '.$sep['c']['lastname']; ?> - <strong>Budget</strong> : <?php echo $sep['a']['opportunity_budget']; ?> €
			</p>
			<?php
		}
	}
	?>
	<?php
}
?>


<h3>Participants</h3>

<div class="frame">
	<?php
	if (!empty($linkedObjectsIds['distribution']['contacts'])) {
		$params = array();
		$rs = $db->query('
			SELECT	c.*, t.*
			FROM	dims_mod_business_contact c
			LEFT JOIN	dims_mod_business_tiers_contact tc
			ON			tc.id_contact = c.id
			AND		tc.type_lien = \'employer\'
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
					if(!file_exists($sep['c']['photoPath']))
						$sep['c']['photoPath'] = _DESKTOP_TPL_PATH.'/gfx/common/human40.png';
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
						<?php /*
						<a href="<?php echo $dims->getScriptEnv().'?submenu='._DESKTOP_V2_CONCEPTS.'&id='.$sep['c']['id'].'&type='.dims_const::_SYSTEM_OBJECT_CONTACT.'&init_filters=1'; ?>" title="Suivre l'activité de cette personne">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/cube20.png" alt="Suivre l'activité de cette personne" />
						</a>
						*/ ?>
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
	if (!empty($linkedObjectsIds['distribution']['tiers'])) {
		$params = array();
		$rs = $db->query('
			SELECT	t.*
			FROM	dims_mod_business_tiers t
			WHERE	t.id_globalobject IN ('.$db->getParamsFromArray(array_keys($linkedObjectsIds['distribution']['tiers']), 'idglobalobject', $params).')
			GROUP BY t.id', $params
		);

		if ($db->numrows($rs)) {
			while ($data = $db->fetchrow($rs)) {
				?>
				<table class="w100 bb1">
				<tr>
					<td class="w20p txtcenter">
						<img src="<?= _DESKTOP_TPL_PATH.'/gfx/common/human40.png'; ?>" alt="<?= $data['intitule']; ?>" title="<?= $data['intitule']; ?>" />
					</td>
					<td><?= $data['intitule']; ?></td>
					<td class="w20p txtcenter">
						<?php /*
						<a href="<?= $dims->getScriptEnv().'?submenu='._DESKTOP_V2_CONCEPTS.'&id='.$data['id'].'&type='.dims_const::_SYSTEM_OBJECT_TIERS.'&init_filters=1'; ?>" title="Suivre l'activité de cette entreprise">
							<img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/cube20.png" alt="Suivre l'activité de cette entreprise" />
						</a>
						*/ ?>
					</td>
					<td class="w20p txtcenter"> </td>
				</tr>
				</table>

				<?php
			}
		}
	}
	?>
</div>

<h3>Localisation</h3>

<table>
<tr>
	<td valign="top" style="width: 32px;">
		<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/gmap32.png" />
	</td>
	<td valign="top">
		<?php
		$address = '';
		if ($this->fields['address'] != '') {
			$address .= $this->fields['address'];
		}
		if ($this->fields['cp'] != '') {
			if ($address != '') $address .= '<br/>';
			$address .= $this->fields['cp'];
		}
		if ($this->fields['lieu'] != '') {
			if ($this->fields['cp'] != '') $address .= ' ';
			$address .= $this->fields['lieu'];
		}
		if ($this->fields['id_country'] > 0) {
			$country = new country();
			$country->open($this->fields['id_country']);
			if ($address != '') $address .= ', ';
			$address .= $country->fields['printable_name'];
		}
		if ($address != '') {
			echo $address;
		}
		?>
	</td>
</tr>
<tr>
	<td colspan="2">
		<div id="activity_map" style="width: 600px; height: 400px;"></div>
	</td>
</tr>
</table>

<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
	<!--//<![CDATA[
	var default_zoom = 8;
	var default_lat = 48.623;
	var default_lon = 6.26;

	var myLat = null;
	var myLon = null;

	var map = null;
	var infowindow = null;

	var geocoder = new google.maps.Geocoder();
	if (geocoder) {
	geocoder.geocode({'address': "<?php echo $this->fields['cp'].' '.$this->fields['lieu']; ?>"}, function (results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
		var strg_location = results[0].geometry.location;
		strg_location += "";
		var tab_string = strg_location.split(",");
		myLat = tab_string[0].replace("(", "");
		myLon = tab_string[1].replace(")", "");

			var myZoom = default_zoom;
			if (myLat != null && myLon != null){
				var myLatlng = new google.maps.LatLng(myLat, myLon);
				myZoom = 10;
			}
			else {
				var myLatlng = new google.maps.LatLng(default_lat, default_lon);
			}

				var mapOptions = {
					scrollwheel: false,
					zoom: myZoom,
					center: myLatlng,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
				var map = new google.maps.Map(document.getElementById("activity_map"), mapOptions);

				var marker = new google.maps.Marker({
					position: myLatlng,
					map: map,
					title:"Hello World!"
				});
		}
	});
	}
	//]]>-->
</script>
