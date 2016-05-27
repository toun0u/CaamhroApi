<script type="text/javascript">
	function addValue(idField, supValue, lst, typeValue) {
		if (typeValue == undefined) {
			typeValue = 'value';
		}

		var supField = dims_getelem('supField'+idField);
		supField.value = '';

		var d_supField = dims_getelem('d_supField'+idField);
		d_supField.style.display='none';

		dims_xmlhttprequest_tofunction('admin.php', 'op=httpr_add_'+typeValue+'&id_field='+idField+'&sup_value='+supValue+'&lst='+lst, addListValue);
	}
	function addListValue(res) {
		var values = res.split('||');
		var key = values[0];
		var val = values[1];
		var lst = dims_getelem(values[2]);

		if (key > -1) {
			lst.options[lst.length] = new Option(val, key, true);
		}
	}
</script>

<?php
$titre = "Affectation group&eacute;e";
$save_btn = "Sauvegarder les modifications";

if (empty($_POST['articles'])) {
	dims_redirect($dims->getScriptEnv());
}
else {
	$_SESSION['catalogue']['articles'] = $_POST['articles'];
}

// Chargement du modele de la famille
$a_champs = array();
include_once './common/modules/catalogue/include/class_modele.php';
$obj_modele = new cata_modele();
if ($obj_modele->open($_SESSION['catalogue']['familyId'])) {
	// Chargement des champs dynamiques du modele
	$a_fields = array();
	for ($i = 1; $i <= 200; $i++) {
		if ($obj_modele->fields["field{$i}"] == 1) {
			$a_fields[] = $i;
		}
	}
	$rs = $db->query("
		SELECT	c.id,
				c.libelle,
				c.type,
				cv.id AS id_valeur,
				cv.valeur
		FROM	dims_mod_cata_champ c

		LEFT JOIN	dims_mod_cata_champ_valeur cv
		ON			cv.id_chp = c.id

		WHERE	c.id IN (".implode(',', $a_fields).")
		ORDER BY c.id, cv.id");
	while ($row = $db->fetchrow($rs)) {
		if (!isset($a_champs[$row['id']])) {
			$a_champs[$row['id']] = array(
				'id'		=> $row['id'],
				'libelle'	=> $row['libelle'],
				'type'		=> $row['type'],
				'valeurs'	=> array()
				);
		}
		if (!empty($row['id_valeur'])) {
			$a_champs[$row['id']]['valeurs'][$row['id_valeur']] = $row['valeur'];
		}
	}
}

// Chargement des marques
$a_marques = array();
$rs = $db->query("SELECT id, libelle FROM dims_mod_cata_marque ORDER BY libelle");
while ($row = $db->fetchrow($rs)) {
	$a_marques[$row['id']] = $row;
}

// Chargement des ecolabels
$a_ecolabels = array();
$rs = $db->query("SELECT id, libelle FROM dims_mod_cata_ecolabel ORDER BY libelle");
while ($row = $db->fetchrow($rs)) {
	$a_ecolabels[$row['id']] = $row;
}

echo $skin->open_simplebloc($titre, '100%');
	?>
	<form name="f_article" action="<?php echo $dims->getScriptEnv(); ?>" method="post" enctype="multipart/form-data">
	<input type="hidden" name="op" value="save_affect_group" />
	<input type="hidden" name="article_dev_durable" value="0" />

	<fieldset class="fieldset">
		<legend><b>G&eacute;n&eacute;ralit&eacute;s</b></legend>
		<table cellpadding="2" cellspacing="0">
		<tr>
			<td align="right"><label for="article_marque">Marque :</label></td>
			<td>
				<table cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<select class="select" id="article_marque" name="article_marque">
							<option value='0'>-- Non renseign&eacute; --</option>
							<?php
							foreach ($a_marques as $marque) {
								$sel = ($marque['id'] == $obj_art->fields['marque']) ? ' selected' : '';
								echo "<option value=\"{$marque['id']}\"{$sel}>".stripslashes($marque['libelle'])."</option>";
							}
							?>
						</select>
						<input class="button" type="button" value="+" onclick="javascript:var d_supField=dims_getelem('d_supFieldMarque');d_supField.style.display='block';var supField=dims_getelem('supFieldMarque');supField.focus();">
						<div id="d_supFieldMarque" style='display:none;padding-top:2px;'>
							<input class="text" type="text" id="supFieldMarque" name="supFieldMarque">
							<input class="button" type="button" value="Ajouter" onclick="javascript:addValue('Marque', document.f_article.supFieldMarque.value, 'article_marque', 'marque');">
							<input class="button" type="button" value="X" onclick="javascript:var d_supField=dims_getelem('d_supFieldMarque');d_supField.style.display='none';">
						</div>
					</td>
					<td align="right" style="padding-left:30px;"><label for="article_cond">Conditionnement :&nbsp;</label></td>
					<td><input class="text" type="text" id="article_cond" name="article_cond" value="<? echo $obj_art->fields['cond']; ?>" size="5"/></td>
				</tr>
				</table>
			</td>
			<td>
				<table cellpadding="0" cellspacing="0">
				<tr>
					<td align="right" style="padding-left:30px;"><label for="article_image">Photo :&nbsp;</label></td>
					<td>
						<table cellpadding="0" cellspacing="0">
						<tr>
							<td><input class="text" type="file" id="article_image" name="image"/></td>
							<td width="25px">&nbsp;</td>
							<td><label for="article_imageref">Ou utiliser la m&ecirc;me r&eacute;f&eacute;rence que :&nbsp;</label></td>
							<td><input class="text" type="text" id="article_imageref" name="imageref"/></td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
	</fieldset>

	<?php if (sizeof($a_champs)): ?>
		<fieldset class="fieldset">
			<legend><b>Champs additionnels</b></legend>
			<table cellpadding="2" cellspacing="0" width="100%">
			<?php
			$nb_col = 2;
			$i = 0;
			foreach ($a_champs as $chp) {
				echo "<td align=\"right\" valign=\"top\"><label for=\"article_field{$chp['id']}\">{$chp['libelle']} :</label></td>";

				switch ($chp['type']) {
					case 'texte':
						echo "<td valign=\"top\"><input class=\"text\" type=\"text\" id=\"article_field{$chp['id']}\" name=\"article_field{$chp['id']}\" value=\"".$obj_art->fields["field{$chp['id']}"]."\"/></td>";
						break;
					case 'liste':
						echo "<td valign=\"top\"><select class=\"select\" id=\"article_field{$chp['id']}\" name=\"article_field{$chp['id']}\"><option value=\"\">-- Non renseign&eacute; --</option>";
						foreach ($chp['valeurs'] as $key => $val) {
							$sel = ($obj_art->fields["field{$chp['id']}"] == $key) ? ' selected' : '';
							echo "<option value=\"{$key}\"{$sel}>".stripslashes($val)."</option>";
						}
						echo "
								</select>
								<input class=\"button\" type=\"button\" value=\"+\" onclick=\"javascript:var d_supField=dims_getelem('d_supField{$chp['id']}');d_supField.style.display='block';var supField=dims_getelem('supField{$chp['id']}');supField.focus();\">
								<div id=\"d_supField{$chp['id']}\" style='display:none;padding-top:2px;'>
									<input class=\"text\" type=\"text\" id=\"supField{$chp['id']}\" name=\"supField{$chp['id']}\">
									<input class=\"button\" type=\"button\" value=\"Ajouter\" onclick=\"javascript:addValue({$chp['id']}, document.f_article.supField{$chp['id']}.value, 'article_field{$chp['id']}');\">
									<input class=\"button\" type=\"button\" value=\"X\" onclick=\"javascript:var d_supField=dims_getelem('d_supField{$chp['id']}');d_supField.style.display='none';\">
								</div>
							</td>";
						break;
				}

				$i++;
				if ($i % $nb_col == 0) {
					echo "</tr><tr>";
				}
			}
			?>
			</table>
		</fieldset>
	<?php endif ?>

	<fieldset class="fieldset">
		<legend><b>Ecoproduit</b></legend>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="50%" valign="top">
				<table cellpadding="2" cellspacing="0" width="100%">
				<tr>
					<td colspan="2">
						<input class="checkbox" type="checkbox" id="article_dev_durable" name="article_dev_durable" value="1" <? if ($obj_art->fields['dev_durable']) echo 'checked'; ?>/>
						<label for="article_dev_durable">Cet article fait partie de la gamme "&eacute;coproduit"</label>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
	</fieldset>

	<table cellpadding="5" cellspacing="0" width="100%">
	<tr>
		<td align="center">
			<input class="button" type="button" value="Retour" onclick="javascript:document.location.href='<?php echo $dims->getScriptEnv(); ?>';"/>
			<input class="button" type="submit" value="<?php echo $save_btn; ?>"/>
		</td>
	</tr>
	</table>

	</form>
	<?php
echo $skin->close_simplebloc();
