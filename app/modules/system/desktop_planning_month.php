<?

$mdatedeb_timestp = mktime(0,0,0,date('n')+$_SESSION['business']['business_minimonthadd'],1,date('Y'));
$mdatefin_timestp = mktime(0,0,0,date('n')+$_SESSION['business']['business_minimonthadd']+1,-1,date('Y'));

$mdatedeb = date('Y-m-d',$mdatedeb_timestp);
$mdatefin = date('Y-m-d',$mdatefin_timestp);
$myear= date("Y",$mdatedeb_timestp);
$curmonth = $business_mois[date('n',$mdatedeb_timestp)];
$jmax = date('t',$mdatefin_timestp);

$currentjourday= date('W',$datedeb_timestp);
$currentjourdaytime= date('W');

$sql = " SELECT 	a.*,
					au.user_id AS acteur,
					t.id AS tiers_id,
					t.intitule AS tiers_intitule,
					d.id AS dossier_id,
					d.objet_dossier AS dossier_intitule,
					i.nom, i.prenom,
					u.color,
					u.login

		FROM 		dims_mod_business_action a

		INNER JOIN	dims_mod_business_action_detail ad on a.id = ad.action_id
		INNER JOIN 	dims_mod_business_action_utilisateur au on a.id = au.action_id
		INNER JOIN 	dims_user u on au.user_id = u.id

		LEFT JOIN 	dims_mod_business_tiers t ON ad.tiers_id = t.id
		LEFT JOIN 	dims_mod_business_dossier d ON ad.dossier_id = d.id
		LEFT JOIN 	dims_mod_business_contact i ON ad.contact_id = i.id

		WHERE 		a.datejour BETWEEN :datedeb AND :datefin
		$user_filter
		AND 		a.id_module = :moduleid
		ORDER BY	a.heuredeb, a.heurefin
		";

$mplanning = array();

$result = $db->query($sql, array(
	':datedeb'	=> $mdatedeb,
	':datefin'	=> $mdatefin,
	':moduleid'	=> $_SESSION['dims']['moduleid']
));
while ($fields = $db->fetchrow($result)) {
	// calcul du numéro de journée par rapport au début de l'intervalle de recherche
	// si datejour = datedeb alors jour = 0, etc.
	$jour = business_datediff($mdatedeb,$fields['datejour']);
	if (!isset($mplanning[$jour+1])) $mplanning[$jour+1] = array();

	$fields['color'] = ($fields['color']!='') ? $fields['color'] : '#c0c0c0';

	// affectation des actions par jour de semaine (1=lundi, etc...)
	$mplanning[$jour+1][] = $fields;
}

$weekday = date('w', $mdatedeb_timestp);
if ($weekday == 0) $weekday = 7;
?>
<div style="float:left;width:100%">
<?
echo $skin->open_simplebloc($curmonth." ".$myear,'100%','','');
?>
	<span style="width:100%;margin: 0 auto;display:block;">
		<table>
		<tr>
		<?
		$miniprev = "&minimonthadd=".($_SESSION['business']['business_minimonthadd']-1);
		$mininext = "&minimonthadd=".($_SESSION['business']['business_minimonthadd']+1);
		echo "<td width=\"20%\">".dims_create_button("<<","","javascript:desktop_affiche_planning('".$miniprev."')","","","")."</td>";
		echo "<td width=\"40%\">".dims_create_button($_DIMS['cste']['_DIMS_LABEL_DAY'],"","javascript:desktop_affiche_planning('&minimonthadd=0')","","")."</td>";
		echo "<td width=\"20%\">".dims_create_button(">>","","javascript:desktop_affiche_planning('".$mininext."')","","")."</td>";
		?></tr>
		</table>
	</span>
	<div class="planning_calendar_row">
		<div style="float:left;overflow:hidden;width:16px;text-align:center;">&nbsp;
		</div>
	<?
	foreach($business_jour as $d) {
		?>
		<div style="float:left;overflow:hidden;">
			<div style="margin:0px;width:19px;font-size:9px;height:12px;text-align:center;">
			<?
			echo $d;
			?>
			</div>
		</div>
		<?
	}
	?>
	</div>
	<?
	// il faut se caler sur le lundi le + proche de la date courante
	$datecur_timestp=mktime(0,0,0,date('n'),date('d')-date("N")+1,date('Y'));
	//$diff=date('W',$datejour)-$currentjourday+$_SESSION['business']['business_weekadd'];
	$decaljour= date("N",$mdatedeb_timestp);
	$diff=$mdatedeb_timestp-($decaljour*86400)-$datecur_timestp;
	$diff=round((($diff/86400)/7),0);

	$diff=$diff;

	$onclick="onclick=\"javascript:desktop_affiche_planning('&weekadd=".$diff."');\"";

	if ($weekday > 1) {
		$datejour = $mdatedeb_timestp;
		if ($currentjourday==date('W',$mdatedeb_timestp)) $selecsemaine="background-color:#f6abab;";
		else $selecsemaine="";
		?>
		<div class="planning_calendar_row">
			<div style="<? echo $selecsemaine; ?>cursor:pointer;float:left;overflow:hidden;width:16px;height:16px;text-align:center;font-size:9px;padding-top:2px;">
				<? echo 'S'.date('W',$mdatedeb_timestp); ?>
			</div>
		<?
		for ($j = 1; $j < $weekday; $j++) {
		?>
			<div style="float:left;overflow:hidden;">
				<div <? echo $onclick; ?> class="planning_calendar_day" style="cursor:pointer;width:16px;font-size:9px;height:16px;text-align:center;">
				&nbsp;
				</div>
			</div>
		<?
		}
	}
	$diff++;
	for ($j = 1; $j <= $jmax ; $j++) {
		//$datejour = $mdatedeb_timestp + 86400 * ($j-1);
		$datejour = mktime(0,0,0,date('n')+$_SESSION['business']['business_minimonthadd'],$j,date('Y'));
		$jour = date('j',$datejour);
		$mois = date('n',$datejour);
		$annee = date('y',$datejour);
		$joursem = date('w',$datejour);
		$gras = ($today == $datejour);

		if ($weekday == 8) $weekday = 1;

		if ($weekday == 1) {
			if ($currentjourday==date('W',$datejour)) $selecsemaine="background-color:#f6abab;";
			else $selecsemaine="";

			// calcul de la diff entre datecour et datejour
			//$diff=date('W',$datejour)-$currentjourday+$_SESSION['business']['business_weekadd'];
			$onclick="onclick=\"javascript:desktop_affiche_planning('&weekadd=".$diff."');\"";
			$diff++;
			?>
			<div class="planning_calendar_row">
				<div style="<? echo $selecsemaine; ?>cursor:pointer;float:left;overflow:hidden;width:16px;height:16px;text-align:center;font-size:9px;padding-top:2px;">
					<? echo 'S'.date('W',$datejour); ?>
				</div>
			<?
		}

		?>
		<div style="float:left;overflow:hidden;">
		<?
		if (isset($mplanning[$j])) {
			$selecday="background-color:#7F83A9;";
		}
		else {
			$selecday="";
		}
		?>
		<div <? echo $onclick; ?> class="planning_calendar_day<? if ($gras) echo '_selected'; ?>" style="<? echo $selecday;?>cursor:pointer;width:16px;font-size:9px;height:16px;text-align:center;<? if ($gras) echo 'border-top-color:#808080;'; ?>">
		<? printf("%02d",$jour)
		?>
			</div>
		</div>
		<?

		if ($weekday == 7) echo '</div>';
		$weekday++;
	}

	if ($weekday <= 7) {
		for ($j = $weekday; $j <= 7 ; $j++) {
		?>
			<div style="float:left;overflow:hidden;">
				<div class="planning_calendar_day" style="width:16px;font-size:9px;height:16px;text-align:center;">
				&nbsp;
				</div>
			</div>
	<?
		}
		echo '</div>';
	}
echo $skin->close_simplebloc();
	?>
</div>