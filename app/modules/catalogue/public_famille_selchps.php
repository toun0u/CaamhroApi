<?php
if (isset($_POST['action']) && $_POST['action'] == 'save') {
	// suppression de l'ancien modele
	$db->query("DELETE FROM dims_mod_cata_modele WHERE id_famille = $famId");

	// enregistrement du modele
	$a_sql = array();
	for ($i = 1; $i < 200; $i++) {
		$a_sql[] = (isset($_POST["field{$i}"]) && $_POST["field{$i}"]) ? "field{$i}=1" : "field{$i}=0";
	}
	$s_sql = "INSERT INTO dims_mod_cata_modele SET id_famille = $famId, ". implode(',', $a_sql);
	$db->query($s_sql);
}

$obj_famille = new cata_famille();
$obj_famille->open($famId);
$a_modele = $obj_famille->getmodele();

$a_champs = array();
$db->query("SELECT id, libelle FROM dims_mod_cata_champ ORDER BY libelle");
while ($row = $db->fetchrow()) {
	$a_champs[$row['id']] = $row;
}

$nb_col = 4; // Nombre de colonnes
echo $skin->open_simplebloc('Sélection des champs', '100%');
	?>
	<form action="<?php echo $dims->getScriptEnv(); ?>" method="post">
	<input type="hidden" name="action" value="save" />

	<table cellpadding="2" cellspacing="1" width="100%"><tr bgcolor="<?php echo $skin->values['bgline2']; ?>">
		<?php
		$i = 0;
		foreach ($a_champs as $id_chp => $row) {
			if ($a_modele["field{$id_chp}"]) {
				echo "<td><input class=\"checkbox\" type=\"checkbox\" id=\"field{$id_chp}\" name=\"field{$id_chp}\" value=\"1\" checked> <label for=\"field{$id_chp}\"><b>{$row['libelle']}</b></label></td>";
			} else {
				echo "<td><input class=\"checkbox\" type=\"checkbox\" id=\"field{$id_chp}\" name=\"field{$id_chp}\" value=\"1\"> <label for=\"field{$id_chp}\">{$row['libelle']}</label></td>";
			}

			$i++;
			if ($i % $nb_col == 0) {
				$color = ($color == $skin->values['bgline1']) ? $skin->values['bgline2'] : $skin->values['bgline1'];
				echo "</tr><tr bgcolor=\"$color\">";
			}
		}
		?>
	</tr>
	<tr>
		<td colspan="<?php echo $nb_col; ?>" align="center">
			<input class="button" type="submit" value="Enregistrer">
		</td>
	</tr>
	</form></table>
	<?php
echo $skin->close_simplebloc();
