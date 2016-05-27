<?php
include './common/modules/catalogue/include/class_promotion.php';

$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, true);
$id_promo = dims_load_securvalue('id_promo', dims_const::_DIMS_NUM_INPUT, true, true);
$ref_article = dims_load_securvalue('ref_article', dims_const::_DIMS_CHAR_INPUT, true, true);
$old_article = dims_load_securvalue('old_article', dims_const::_DIMS_CHAR_INPUT, false, true);
$prix = dims_load_securvalue('prix', dims_const::_DIMS_CHAR_INPUT, false, true);
$articles_keep = dims_load_securvalue('articles_keep', dims_const::_DIMS_NUM_INPUT, false, true);
$date_debut = dims_load_securvalue('date_debut', dims_const::_DIMS_CHAR_INPUT, false, true);
$date_fin = dims_load_securvalue('date_fin', dims_const::_DIMS_CHAR_INPUT, false, true);

if (!isset($action)) $action = '';

switch ($action) {
	case 'enregistrer':
		include_once './common/modules/catalogue/class_promotion.php';
		$promo = new promotion();
		if (!empty($id_promo)) $promo->open($id_promo);
		$promo->setvalues($_POST, 'promo_');
		$promo->fields['date_debut'] = dims_local2timestamp($date_debut,'00:00:00');
		$promo->fields['date_fin'] = dims_local2timestamp($date_fin,'23:59:59');
		$promo->save();

		dims_redirect($dims->getScriptEnv()."?part=promotions&action=modifier&id_promo={$promo->fields['id']}");
		break;
	case 'enregistrer_article':
		if (!empty($id_promo)) {
			include_once './common/modules/catalogue/class_promotion.php';
			$promo = new promotion();
			$promo->open($id_promo);

			// Chargement d'un fichier
			if ($_FILES['articles_file']['name'] != '') {
				// On vide si on conserve pas les articles
				if (!$articles_keep) $promo->articles = array();

				$handle = fopen ($_FILES['articles_file']['tmp_name'], "r");
				while ($line = trim(fgets($handle, 4096))) {
					$fields = explode(';', $line);
					$promo->articles[$fields[0]] = str_replace(',', '.', $fields[1]);
				}
				fclose($handle);
			}
			else {
				if (!empty($old_article)) unset($promo->articles[$old_article]);
				$promo->articles[$ref_article] = str_replace(',', '.', $prix);
			}

			$promo->save();
			dims_redirect($dims->getScriptEnv()."?part=promotions&action=modifier&id_promo={$promo->fields['id']}");
		}
		else {
			dims_redirect($dims->getScriptEnv()."?part=promotions");
		}
		break;
	case 'supprimer':
		if (!empty($id_promo)) {
			$promo = new promotion();
			$promo->open($id_promo);
			$promo->delete();
		}
		dims_redirect($dims->getScriptEnv()."?part=promotions");
		break;
	case 'supprimer_article':
		if (!empty($id_promo)) {
			if (!empty($ref_article)) {
				include_once './common/modules/catalogue/class_promotion.php';
				$promo = new promotion();
				$promo->open($id_promo);
				unset($promo->articles[$ref_article]);
				$promo->save();
			}
			dims_redirect($dims->getScriptEnv()."?part=promotions&action=modifier&id_promo=$id_promo");
		}
		else {
			dims_redirect($dims->getScriptEnv()."?part=promotions");
		}
		break;
	case 'activer':
		if (!empty($id_promo)) {
			$promo = new promotion();
			$promo->open($id_promo);
			$promo->fields['active'] = !$promo->fields['active'];
			$promo->save();
		}
		dims_redirect($dims->getScriptEnv()."?part=promotions");
		break;
	case 'modifier':
		if (!empty($id_promo)) {
			include_once './common/modules/catalogue/class_promotion.php';
			$promo = new promotion();
			$promo->open($id_promo);
			$date_debut = dims_timestamp2local($promo->fields['date_debut']);
			$date_fin = dims_timestamp2local($promo->fields['date_fin']);

			echo $skin->open_simplebloc("Modifier une promotion","100%");
				?>
				<form name="form" action="<?php echo $dims->getScriptEnv(); ?>" method="post">
				<input type="hidden" name="part" value="promotions">
				<input type="hidden" name="action" value="enregistrer">
				<input type="hidden" name="id_promo" value="<?php echo $id_promo; ?>">

				<table cellpadding="0" cellspacing="0" width="100%">
				<tr bgcolor="<?php echo $skin->values['bgline2']; ?>">
					<td width="60%">
						<table cellpadding="2" cellspacing="0">
						<tr>
							<td class="title">Libellé</td>
							<td><input class="text" type="text" name="promo_libelle" value="<?php echo $promo->fields['libelle']; ?>" maxlength="255"></td>
							<td width="50">&nbsp;</td>
							<td class="title">Code</td>
							<td><input class="text" type="text" name="promo_code" value="<?php echo $promo->fields['code']; ?>" maxlength="50"></td>
						</tr>
						<tr>
							<td class="title">Début</td>
							<td><input class="text" type="text" name="date_debut" value="<?php echo $date_debut['date']; ?>" maxlength="10" size="10"></td>
							<td width="50">&nbsp;</td>
							<td class="title">Fin</td>
							<td><input class="text" type="text" name="date_fin" value="<?php echo $date_fin['date']; ?>" maxlength="10" size="10"></td>
						</tr>
						</table>
					</td>
					<td width="40%" colspan="2">
						<input class="button" type="button" value="Retour" onclick="javascript:document.location.href='<?php echo $dims->getScriptEnv(); ?>?part=promotions'">
						<input class="button" type="submit" value="Modifier">
					</td>
				</tr>
				</form>
				</table>

				<script language="JavaScript">
					document.form.promo_libelle.focus();
				</script>
				<?php
			echo $skin->close_simplebloc();

			echo $skin->open_simplebloc("Articles concernés","100%");
				?>
				<table cellpadding="2" cellspacing="0" width="100%" bgcolor="<?php echo $skin->values['bgline2']; ?>">
				<tr bgcolor="<?php echo $skin->values['bgline2']; ?>">
					<td class="title" width="50%" align="center">Articles</td>
					<td width="50%">&nbsp;</td>
				</tr>
				<tr bgcolor="<? echo $skin->values['bgline2']; ?>">
					<td width="50%" valign="top">
						<?php /* ARTICLES */ ?>
						<?php
						if (empty($ref_article)) {
							echo $skin->open_simplebloc("Ajouter un article","100%");
								?>
								<form action="<?php echo $dims->getScriptEnv(); ?>" method="Post" enctype="multipart/form-data">
								<input type="hidden" name="part" value="promotions">
								<input type="hidden" name="action" value="enregistrer_article">
								<input type="hidden" name="id_promo" value="<?php echo $id_promo; ?>">

								<table cellpadding="1" cellspacing="0" bgcolor="<?php echo $skin->values['bgline2']; ?>" width="100%">
								<tr>
									<td class="title" align="right">Référence&nbsp;</td>
									<td><input class="text" type="text" name="ref_article"></td>
									<td rowspan="5">&nbsp;<input class="button" type="submit" value="Ajouter"></td>
								</tr>
								<tr>
									<td class="title" align="right">Prix&nbsp;</td>
									<td><input class="text" type="text" name="prix"></td>
								</tr>
								<tr><td colspan="2">&nbsp;</td></tr>
								<tr>
									<td class="title" align="center">Importer un fichier</td>
									<td><input class="text" type="file" name="articles_file"></td>
								</tr>
								<tr>
									<td class="title" colspan="2"><input type="checkbox" id="articles_keep" class="checkbox" name="articles_keep" value="1" checked="checked"> <label for="articles_keep">Conserver les articles actuels</label></td>
								</tr>
								</form>
								</table>
								<?php
							echo $skin->close_simplebloc();

							if (sizeof($promo->articles)) {
								echo $skin->open_simplebloc("Liste des articles","100%");
								?>
								<table cellpadding="1" cellspacing="0" bgcolor="<? echo $skin->values['bgline2']; ?>" width="100%">
								<tr>
									<td class="title" align="center">&nbsp;Référence&nbsp;</td>
									<td class="title" align="center">&nbsp;Prix&nbsp;</td>
									<td colspan="2">&nbsp;</td>
								</tr>
								<?php
								$color = $skin->values['bgline2'];
								foreach ($promo->articles as $ref => $prix) {
									$color = ($color == $skin->values['bgline2']) ? $skin->values['bgline1'] : $skin->values['bgline2'];

									echo "
										<tr bgcolor=\"$color\">
											<td>&nbsp;$ref&nbsp;</td>
											<td align=\"right\">&nbsp;". catalogue_formateprix($prix) ."&nbsp;</td>
											<td align=\"center\"><a href=\"".$dims->getScriptEnv()."?part=promotions&action=modifier&id_promo=$id_promo&ref_article=$ref\"><img src=\"./common/modules/catalogue/img/modifier.gif\" border=\"0\"></a></td>
											<td align=\"center\"><a href=\"".$dims->getScriptEnv()."?part=promotions&action=supprimer_article&id_promo=$id_promo&ref_article=$ref\"><img src=\"./common/modules/catalogue/img/supprimer.gif\" border=\"0\"></a></td>
										</tr>";
								}
								?>
								</table>
								<?php
								echo $skin->close_simplebloc();
							}
						}
						else {
							echo $skin->open_simplebloc("Modifier un article","100%");
								?>
								<form action="<?php echo $dims->getScriptEnv(); ?>" method="post">
								<input type="hidden" name="part" value="promotions">
								<input type="hidden" name="action" value="enregistrer_article">
								<input type="hidden" name="id_promo" value="<?php echo $id_promo; ?>">
								<input type="hidden" name="old_article" value="<?php echo $ref_article; ?>">

								<table cellpadding="1" cellspacing="0" bgcolor="<?php echo $skin->values['bgline2']; ?>" width="100%">
								<tr>
									<td class="title" align="right">Référence&nbsp;</td>
									<td><input class="text" type="text" name="ref_article" value="<?php echo $ref_article; ?>"></td>
									<td rowspan="2">&nbsp;<input class="button" type="button" value="Retour" onclick="javascript:document.location.href='<?php echo $dims->getScriptEnv(); ?>?part=promotions&action=modifier&id_promo=<?php echo $id_promo; ?>'"></td>
									<td rowspan="2">&nbsp;<input class="button" type="submit" value="Enregistrer"></td>
								</tr>
								<tr>
									<td class="title" align="right">Prix&nbsp;</td>
									<td><input class="text" type="text" name="prix" value="<?php echo $promo->articles[$ref_article]; ?>"></td>
								</tr>
								</form>
								</table>
								<?php
							echo $skin->close_simplebloc();
						}
						?>
					</td>
				</tr>
				</table>
				<?php
			echo $skin->close_simplebloc();
		}
		else {
			dims_redirect($dims->getScriptEnv()."?part=admin_promotions");
		}
		break;
	default:
		echo $skin->open_simplebloc("Ajouter une promotion","100%");
			?>
			<form name="form" action="<?php echo $dims->getScriptEnv(); ?>" method="post">
			<input type="hidden" name="part" value="promotions">
			<input type="hidden" name="action" value="enregistrer">

			<table cellpadding="0" cellspacing="0" width="100%">
			<tr bgcolor="<?php echo $skin->values['bgline2']; ?>">
				<td width="60%">
					<table cellpadding="2" cellspacing="0">
					<tr>
						<td class="title">Libellé</td>
						<td><input class="text" type="text" name="promo_libelle" maxlength="255"></td>
						<td width="50">&nbsp;</td>
						<td class="title">Code</td>
						<td><input class="text" type="text" name="promo_code" maxlength="50"></td>
					</tr>
					<tr>
						<td class="title">Début</td>
						<td><input class="text" type="text" name="date_debut" maxlength="10" size="10"></td>
						<td width="50">&nbsp;</td>
						<td class="title">Fin</td>
						<td><input class="text" type="text" name="date_fin" maxlength="10" size="10"></td>
					</tr>
					</table>
				</td>
				<td width="40%">
					<input class="button" type="submit" value="Ajouter">
				</td>
			</tr>
			</form>
			</table>

			<script language="JavaScript">
				document.form.promo_libelle.focus();
			</script>
			<?php
		echo $skin->close_simplebloc();

		$sql = "SELECT * FROM dims_mod_vpc_promotion";
		$db->query($sql);
		if ($db->numrows()) {
			echo $skin->open_simplebloc("Liste des promotions","100%");
				?>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr bgcolor="<?php echo $skin->values['bgline2']; ?>">
					<td>
						<table cellpadding="2" cellspacing="0">
						<tr>
							<td class="title" align="center">&nbsp;Libellé&nbsp;</td>
							<td class="title" align="center">&nbsp;Code&nbsp;</td>
							<td class="title" align="center">&nbsp;Début&nbsp;</td>
							<td class="title" align="center">&nbsp;Fin&nbsp;</td>
							<td class="title" align="center">&nbsp;Active&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<?php
						$color = $skin->values['bgline2'];
						while ($row = $db->fetchrow()) {
							$color = ($color == $skin->values['bgline1']) ? $skin->values['bgline2'] : $skin->values['bgline1'];
							$date_debut = ($row['date_debut'] != '') ? dims_timestamp2local($row['date_debut']) : "-";
							$date_fin = ($row['date_fin'] != "") ? dims_timestamp2local($row['date_fin']) : "-";
							$remise = ($row['type_remise'] == 'pourcentage') ? "{$row['valeur']} %" : "{$row['valeur']} &euro;";
							$active = ($row['active']) ? "green" : "red";

							echo "
								<tr bgcolor=\"$color\">
									<td>&nbsp;<a href=\"".$dims->getScriptEnv()."?part=promotions&action=modifier&id_promo={$row['id']}\">{$row['libelle']}</a>&nbsp;</td>
									<td>&nbsp;<a href=\"".$dims->getScriptEnv()."?part=promotions&action=modifier&id_promo={$row['id']}\">{$row['code']}</a>&nbsp;</td>
									<td align=\"center\">&nbsp;<a href=\"".$dims->getScriptEnv()."?part=promotions&action=modifier&id_promo={$row['id']}\">{$date_debut['date']}</a>&nbsp;</td>
									<td align=\"center\">&nbsp;<a href=\"".$dims->getScriptEnv()."?part=promotions&action=modifier&id_promo={$row['id']}\">{$date_fin['date']}</a>&nbsp;</td>
									<td align=\"center\"><a href=\"".$dims->getScriptEnv()."?part=promotions&action=activer&id_promo={$row['id']}\"><img src=\"./common/modules/catalogue/img/ico_point_{$active}.gif\" border=\"0\"></a></td>
									<td align=\"center\">
										&nbsp;<a href=\"".$dims->getScriptEnv()."?part=promotions&action=modifier&id_promo={$row['id']}\"><img src=\"./common/modules/catalogue/img/modifier.gif\" border=\"0\"></a>
										&nbsp;<a href=\"javascript:dims_confirmlink('".$dims->getScriptEnv()."?part=promotions&action=supprimer&id_promo={$row['id']}','Etes-vous sûr(e) de vouloir supprimer cette promotion ?');\"><img src=\"./common/modules/catalogue/img/supprimer.gif\" border=\"0\"></a>
									</td>
								</tr>";
						}
						?>
						</table>
					</td>
				</tr>
				</table>
				<?php
			echo $skin->close_simplebloc();
		}
		break;
}
