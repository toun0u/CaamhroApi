<?php
$lesmois = array("01" => "Janvier","02" => "Février","03" => "Mars","04" => "Avril","05" => "Mai","06" => "Juin","07" => "Juillet","08" => "Août","09" => "Septembre","10" => "Octobre","11" => "Novembre","12" => "Décembre");

$sql = "
	SELECT DISTINCT LEFT(timestp,8) AS date
	FROM dims_mod_vpc_budget_log
	WHERE id_group = $groupid";
$rs = $db->query($sql);
if ($db->numrows($rs)) {
	$ensjours = array();
	while ($fields = $db->fetchrow($rs)) {
		ereg("([0-9]{4})([0-9]{2})([0-9]{2})", $fields['date'], $reg);
		$ensjours[$reg[1]][$reg[2]][] = $reg[3];
	}

	/*
	* Si une date est passée en paramètre,
	* on prend chaque partie de la date et on regarde s'il y a une commande a cette date.
	* Si on en trouve pas, on prend les valeurs d'une date qui est dans la liste des commandes.
	*/
	if (isset($date)) {
		$reg = array();
		ereg("([0-9]{4})([0-9]{0,2})([0-9]{0,2})",$date,$reg);

		if (isset($reg[1]) && $reg[1] != "" && in_array($reg[1],array_keys($ensjours))) $annee = $reg[1];
		if (isset($reg[2]) && $reg[2] != "" && isset($annee) && in_array($reg[2],array_keys($ensjours[$annee]))) $mois = $reg[2];
	}

	/*
	* Si aucune date est passée en paramètre,
	* on prend par défaut la date de la dernière commande.
	*/
	if (!isset($annee)) $annee = max(array_keys($ensjours));
	if (!isset($mois)) $mois = max(array_keys($ensjours[$annee]));

	$sql = "
		SELECT *
		FROM dims_mod_vpc_budget_log
		WHERE id_group = $groupid
		AND LEFT(timestp,6) = {$annee}{$mois}";
	$rs2 = $db->query($sql);
	if ($db->numrows($rs2)) {
		$budgets = array();
		while ($fields = $db->fetchrow($rs2)) {
			$budgets[$fields['timestp']][] = $fields;
		}
	}
	krsort($budgets);

	echo $skin->open_simplebloc("Historique des budgets","100%");
		?>
		<form name="form" action="<?php echo $dims->getScriptEnv(); ?>" method="post">
		<INPUT TYPE='Hidden' NAME='groupinterid' VALUE='<?php echo $_SESSION['catalogue']['root_group']; ?>' />

		<table cellpadding="2" cellspacing="0">
			<tr>
				<td> Voir les budgets de :</td>
				<td>
					<select class="WebText" name="selannee" style='width:60px' onChange="javascript:document.location.href='<?php echo $dims->getScriptEnv(); ?>?date=' + document.form.selannee.value;">
						<?php
						foreach ($ensjours as $lannee => $moisjour) {
							($lannee == $annee) ? $selected = " selected" : $selected = "";
							echo "<option value=\"$lannee\"$selected>{$lannee}</option>";
						}
						?>
					</select>
				</td>
				<td>
					<table cellpadding="2" cellspacing="0">
						<tr>
							<?php
							foreach ($ensjours[$annee] as $lemois => $lejour) {
								($lemois == $mois) ? $libellemois = "<b>{$lesmois[$lemois]}</b>" : $libellemois = "<a href=\"".$dims->getScriptEnv()."?date={$annee}{$lemois}\">{$lesmois[$lemois]}</a>";
								echo "<td align=\"center\" style=\"padding-left:10;padding-right:10\">$libellemois</td>";
							}
							?>
						</tr>
					</table>
				</td>
			</tr>
			</form>
		</table>

		<br>
		<table cellpadding="2" cellspacing="1" width="100%">
			<tr class="Title" bgcolor="<? echo $skin->values['bgline2']; ?>">
				<td>&nbsp;Date - Heure&nbsp;</td>
				<td>&nbsp;Utilisateur&nbsp;</td>
				<td>&nbsp;Action&nbsp;</td>
				<td>&nbsp;Code&nbsp;</td>
				<td align="right">&nbsp;Valeur&nbsp;</td>
				<td width="10" align="center">&nbsp;Non&nbsp;<br>&nbsp;bloquant&nbsp;</td>
			</tr>
			<?php
			$color = $skin->values['bgline2'];
			foreach ($budgets as $budget) {
				foreach ($budget as $modif) {
					$color = ($color == $skin->values['bgline1']) ? $skin->values['bgline2'] : $skin->values['bgline1'];
					$datetime = dims_timestamp2local($modif['timestp']);
					$bbloquant = ($modif['non_bloquant'] == 0) ? "<img src=\"./common/modules/catalogue/img/ico_point_red.gif\" alt=\"Budget bloquant\">" : "<img src=\"./common/modules/catalogue/img/ico_point_green.gif\" alt=\"Budget non bloquant\">";

					echo "
						<tr bgcolor=\"$color\">
							<td>{$datetime['date']} - {$datetime['time']}</td>
							<td>{$modif['user_name']}</td>
							<td>{$modif['action']}</td>
							<td>{$modif['code']}</td>
							<td align=\"right\">". catalogue_formateprix($modif['valeur']) ."</td>
							<td align=\"center\">$bbloquant</td>
						</tr>";
				}
			}
			?>
		</table>
		<?php
	echo $skin->close_simplebloc();
}
else {
	echo $skin->open_simplebloc("Historique des budgets","100%");
		?>
		<table cellpadding="2" cellspacing="0" width="100%" bgcolor="<? echo $skin->values['bgline1']; ?>">
			<tr>
				<td>L'historique des budgets est vide.</td>
			</tr>
		</table>
		<?php
	echo $skin->close_simplebloc();
}
