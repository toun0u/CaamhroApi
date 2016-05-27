<?php
	if(!isset($recherche_dispo_users)) {
		die();
	}

	function getMinute($heure) {
		$nb_H = substr($heure, 0, 2);
		$nb_M = substr($heure, 3, 2);

		return (($nb_H * 60) + $nb_M);
	}

	function indice2minute($indice) {
		$minutes = ($indice * 15) + (_PLANNING_H_START*60);

		return $minutes;
	}

	function minute2Heure($minutes) {
		$nb_H = round($minutes / 60);
		if($nb_H<10) $nb_H = "0".$nb_H;
		$nb_M = $minutes % 60;

		if($nb_M == 0) {
			return $nb_H."h".$nb_M."0";
		}
		else {
			return $nb_H."h".$nb_M;
		}
	}

	/* Nombre de cases pour le tableau = Heure Max en journée (19h-7h = 10h) (10h * 60min = 600min) 600 / Tps intermédiaire MIN (15min) */
	$interval = 15;
	$max_cases = (((_PLANNING_H_END+1)*60)-((_PLANNING_H_START)*60)) / $interval;

	/* Temps recherché div par l interval */
	$interval_search = $recherche_dispo_duree / $interval;
	$tab_found = array();
	$date_found = 0;

	$datejour = date('Y-m-d');
	$datetimestamp = mktime(0,0,0,date('m'),date('j'),date('Y'));
	$idate = 0;

	$jour_recherche = array();
	for($j = 0; $j < 7; $j++) {
		$jour_recherche[$j] = 0;
	}

	$i = 0;
	while(isset($recherche_dispo_jour[$i])) {
		$jour_recherche[$recherche_dispo_jour[$i]] = 1;
		$i++;
	}

	$heure_avap_1 = getMinute($recherche_dispo_avap_heure_1.':'.$recherche_dispo_avap_minute_1) - (_PLANNING_H_START*60);
	$heure_avap_interval_1 = $heure_avap_1 / $interval;
	$heure_avap_2 = getMinute($recherche_dispo_avap_heure_2.':'.$recherche_dispo_avap_minute_2) - (_PLANNING_H_START*60);
	$heure_avap_interval_2 = $heure_avap_2 / $interval;

	$nb_users = 0;
	$lien_users = '';
	foreach($recherche_dispo_users as $value) {
		$lien_users .= "&dispo_users[]=".$value;
		$nb_users++;
	}
	$lien_users .= '&nb_users='.$nb_users;

	/* dims_print_r($jour_recherche); */

	if(!isset($recherche_dispo_nombre_date) or ($recherche_dispo_nombre_date == 0)) $recherche_dispo_nombre_date = 1;

	while($date_found < $recherche_dispo_nombre_date) {
		if(($jour_recherche[date('w', $datetimestamp)])) {

			$tab_day = array();

			for($j = 0; $j < $max_cases; $j++) {
				$tab_day[$j] = 0;
			}

			$params = array();
			$sql = "
				 SELECT		a.temps_prevu, a.heuredeb, a.heurefin,
							u.lastname, u.login

				FROM		dims_mod_business_action a

				INNER JOIN	dims_mod_business_action_detail ad on a.id = ad.action_id
				INNER JOIN	dims_mod_business_action_utilisateur au on a.id = au.action_id
				INNER JOIN	dims_user u on au.user_id = u.id

				LEFT JOIN dims_mod_business_tiers t ON ad.tiers_id = t.id
				LEFT JOIN dims_mod_business_dossier d ON ad.dossier_id = d.id
				LEFT JOIN dims_mod_business_interlocuteur i ON ad.interlocuteur_id = i.id

				WHERE a.id_module = :idmodule
				AND au.user_id IN (".$db->getParamsFromArray($recherche_dispo_users, 'iduser', $params).")
				AND a.datejour = :datejour
				ORDER BY	a.heuredeb, a.heurefin
				";
			$params[':datejour'] = array('type' => PDO::PARAM_STR, 'value' => $datejour);
			$params[':idmodule'] = array('type' => PDO::PARAM_STR, 'value' => $_SESSION['dims']['moduleid']);

			$result = $db->query($sql, $params);

			/* echo $sql."<br />"; */

			if($db->numrows() > 0) {
				while ($fields = $db->fetchrow($result)) {
					/* On cherche le nombre de minutes pour heuredeb */
					$min_HD = getMinute($fields['heuredeb']);

					/* On retire 540m, soit 9h */
					$min_HD = $min_HD - (_PLANNING_H_START*60);
					$min_HF = $min_HD + $fields['temps_prevu'];

					$j_min = floor($min_HD / $interval);
					$j_max = round($min_HF / $interval);

					for($j = $j_min; $j < $j_max; $j++) {
						$tab_day[$j] = 1;
					}
				}
			}

			/* dims_print_r($tab_day); */

			$count = 0;
			$j = 0;
			while(($j < $max_cases) and ($date_found < $recherche_dispo_nombre_date)) {
				/* On empêche les rendez-vous de commencer à 12h mais autorise de poursuivre un rendez-vous du matin.
				   Si on enlève $count == 0 alors on n'autorise ni debut ni fin entre 12h(indice 12) et 13h (indice 16) */
				if(($j == 12 or $j == 13 or $j == 14 or $j == 15) and $count == 0) {
					$j++;
				}
				else {
					if($j >= $heure_avap_interval_1 and $j < $heure_avap_interval_2) {
						if($tab_day[$j] == 0) {
							$count++;
							if($count == $interval_search) {
								$tab_found[$date_found]['date'] = $datejour;
								$minute_heuredeb = indice2Minute($j-($interval_search-1));
								$tab_found[$date_found]['heuredeb'] = minute2Heure($minute_heuredeb);
								$tab_found[$date_found]['heurefin'] = minute2Heure($minute_heuredeb + $recherche_dispo_duree);
								$tab_found[$date_found]['dispo_duree'] = $recherche_dispo_duree;
								$date_found++;
								$count = 0;
							}
						}
						elseif($tab_day[$j] == 1) {
							$count = 0;
						}
						$j++;
					}
					else {
						$j++;
					}
				}
			}
		}

		$idate++;
		$datejour = date('Y-m-d', mktime(0,0,0,date('m'),date('j')+$idate,date('Y')));
		$datetimestamp = mktime(0,0,0,date('m'),date('j')+$idate,date('Y'));

	}

	/* dims_print_r($tab_found); */
?>
<?php echo $skin->open_simplebloc('','width:300px;padding:5px;'); ?>
<table cellpadding="0" cellspacing="0" border="0" style="width:300px;">
	<tr>
		<th colspan="5">
			Créneaux disponibles :
		</th>
	</tr>
	<tr>
		<td colspan="5">
			&nbsp;
		</td>
	</tr>
		<?php
			foreach($tab_found as $value) {
				$date = mktime(0,0,0,substr($value['date'], 5, 2),substr($value['date'], 8, 2),substr($value['date'], 0, 4));
				echo "<tr>";
					echo "<td style='text-align: center'>".business_datefr_planning($date)."</td>";
					echo "<td>".$value['heuredeb']."</td>";
					echo "<td>".$value['heurefin']."</td>";
					echo "<td>";
				?>
					<a style="cursor: pointer;" onclick="dims_openwin('index-light.php?op=xml_planning_modifier_action&datejour=<?php echo business_dateus2fr($value['date']); ?>&heure_dispo_deb=<?php echo $value['heuredeb']; ?>&heure_dispo_fin=<?php echo $value['heurefin']; ?>&dispo_duree=<?php echo $value['dispo_duree']; ?><?php echo $lien_users; ?>',450,600)">
				<?php
				echo "<img src='./common/modules/business/img/ok/next.gif' /></a></td>";
				echo "</tr>";
			}
		?>
</table>
<?php echo $skin->close_simplebloc(); ?>
