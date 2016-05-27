<?php
include_once './common/modules/catalogue/include/class_article.php';
$obj_art = new article();
$edition = false;

// enregistrement du retour vers la fiche produit
// sert pour les articles rattachés, quand on veut en créer un nouveau
$artratt_retour = dims_load_securvalue('artratt_retour', dims_const::_DIMS_NUM_INPUT, true);
if ($artratt_retour != '') {
	$_SESSION['catalogue']['artratt_retour'] = $artratt_retour;
}

switch ($op) {
	case 'create':
		$obj_art->init_description();
		$titre = "Cr&eacute;ation d'un article";
		$save_btn = "Cr&eacute;er cet article";
		$subtab = 'fiche';
		break;
	case 'edit':
		$edition = true;
		$id_article = dims_load_securvalue('id_article', dims_const::_DIMS_NUM_INPUT, true, true);
		if (empty($id_article)) {
			dims_redirect($scriptenv);
		} else {
			if ($obj_art->findById($id_article)) {
				$titre = "Edition de l'article {$obj_art->fields['reference']}";
				$save_btn = "Sauvegarder les modifications";
			} else {
				dims_redirect($scriptenv);
			}
		}

		$subtab = dims_load_securvalue('subtab', dims_const::_DIMS_CHAR_INPUT,true,true,false, $_SESSION['catalogue']['subtab'], 'fiche');

		// creation des onglets
		$sub_tabs['fiche']['title'] = 'Fiche article';
		$sub_tabs['fiche']['url'] = $dims->getScriptEnv().'?subtab=fiche&op=edit&id_article='.$obj_art->fields['id_article'];
		$sub_tabs['fiche']['icon'] = './common/modules/catalogue/img/sliders.png';
		$sub_tabs['fiche']['width'] = 150;
		$sub_tabs['fiche']['position'] = 'left';

		$sub_tabs['artratt']['title'] = 'Articles rattachés';
		$sub_tabs['artratt']['url'] = $dims->getScriptEnv().'?subtab=artratt&op=edit&id_article='.$obj_art->fields['id_article'];
		$sub_tabs['artratt']['icon'] = './common/modules/catalogue/img/nuage.png';
		$sub_tabs['artratt']['width'] = 150;
		$sub_tabs['artratt']['position'] = 'left';

		echo '<div style="padding: 0 10px;">'.$skin->create_toolbar($sub_tabs, $subtab,$subtab,'0',"onglet").'</div>';
		break;
}

switch($subtab) {
	case 'fiche':
		// Messages d'erreur upload photo
		$a_errors = array(
			_CATA_PHOTO_TRANSFERT_ERROR	=> 'Une erreur est survenue pendant le transfert.',
			_CATA_PHOTO_EMPTY_DOC		=> 'Le document est vide.',
			_CATA_PHOTO_HUGE_DOC		=> 'Le document est trop volumineux ('. _CATA_PHOTO_MAX_UPLOAD_SIZE / 1024 / 1024 .' Mo max).',
			_CATA_PHOTO_ALREADY_EXISTS	=> 'Un document portant ce nom existe d&eacute;j&agrave;.<br/><br/>Cette photo est rattach&eacute;e aux produits suivants :<LSTART><br/><br/>Veuillez changer le nom de votre document et le renvoyer, ou cliquer sur ce lien : <a href="'.$dims->getScriptEnv().'?op=set_photo&id_article='.$obj_art->fields['id_article'].'&filename='.$_SESSION['catalogue']['erreur_photos']['filename'].'">Utiliser la photo de ces produits</a>',
			_CATA_PHOTO_COPY_ERROR		=> 'Une erreur est survenue pendant l\'&eacute;criture du fichier.'
			);

		// Chargement du modele de la famille
		include_once './common/modules/catalogue/include/class_modele.php';
		$obj_modele = new cata_modele();
		$obj_modele->open($_SESSION['catalogue']['familyId']);

		// Chargement des champs dynamiques du modele
		$a_fields = array();
		for ($i = 1; $i <= 200; $i++) {
			if (isset($obj_modele->fields["field{$i}"]) && $obj_modele->fields["field{$i}"] == 1) {
				$a_fields[] = $i;
			}
		}
		$a_champs = array();

		if (sizeof($a_fields)) {
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
			$a_marques[$row['libelle']] = $row;
		}
		uksort($a_marques, 'strcasecmp');

		// Chargement des conditionnements
		$a_conditionnements = array();
		$rs = $db->query("
			SELECT	*
			FROM	dims_mod_cata_conditionnement
			ORDER BY libelle");
		while ($row = $db->fetchrow($rs)) {
			$a_conditionnements[] = $row;
		}

		$retour = dims_load_securvalue('retour', dims_const::_DIMS_CHAR_INPUT, true, false);

		echo $skin->open_simplebloc($titre, '100%');
			?>
			<form name="f_article" action="<?php echo $dims->getScriptEnv(); ?>" method="post" enctype="multipart/form-data">
			<input type="hidden" name="op" value="save" />
			<input type="hidden" name="id_article" value="<?=$obj_art->fields['id_article'];?>" />
			<input type="hidden" name="article_dev_durable" value="0" />
			<?php if (!empty($retour)): ?>
			<input type="hidden" name="retour" value="<?php echo $retour; ?>" />
			<?php endif ?>

			<fieldset class="fieldset">
				<legend><b>G&eacute;n&eacute;ralit&eacute;s</b></legend>
				<table cellpadding="2" cellspacing="0">
				<tr>
					<td align="right"><label for="article_reference">R&eacute;f&eacute;rence :</label></td>
					<td><input class="text" type="text" id="article_reference" name="article_reference" value="<?=$obj_art->fields['reference'];?>"/></td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td align="right"><label for="article_designation">D&eacute;signation Internet :</label></td>
					<td><input class="text" type="text" id="article_designation" name="article_designation" value="<?=$obj_art->fields['label'];?>" size="78"/></td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td align="right" valign="top"><label for="article_description">Description :</label></td>
					<td><?=dims_fckeditor('article_description', $obj_art->fields['description'], '800','350');?></td>
				</tr>
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
							<td><input class="text" type="text" id="article_cond" name="article_cond" value="<?=$obj_art->fields['cond'];?>" size="5"/></td>
							<td align="right" style="padding-left:30px;"><label for="article_page">Page du catalogue :&nbsp;</label></td>
							<td><input class="text" type="text" id="article_page" name="article_page" value="<?=$obj_art->fields['page'];?>" size="5"/></td>
							<td align="right" style="padding-left:30px;">Stock :&nbsp;</td>
							<td><strong><?=$obj_art->fields['qte'];?></strong></td>
						</tr>
						</table>
					</td>
				</tr>
				<?php
				if (isset($_SESSION['catalogue']['erreur_images'])) {
					?><tr><td colspan="2" class="dims_error"><b>Erreur :</b> <?=str_replace('<LSTART>', $_SESSION['catalogue']['erreur_images']['lstart'], $a_errors[$_SESSION['catalogue']['erreur_images']['erreur']]);?></td></tr><?
					unset($_SESSION['catalogue']['erreur_images']);
				}
				if ($obj_art->fields['image'] != '') {
					if (file_exists(realpath('.')."/photos/100x100/{$obj_art->fields['image']}")) {
						?>
						<tr>
							<td align="right"><label for="article_image">Photo :</label></td>
							<td>
								<table cellpadding="2" cellspacing="0">
								<tr>
									<td align="center">
										<img src="./photos/100x100/<?=$obj_art->fields['image'];?>" alt="<?=$obj_art->fields['reference'];?>" id="article_image" style="padding-top:20px;" /><br/>
										<a href="javascript:dims_confirmlink('<?php echo $dims->getScriptEnv()."?op=drop_photo&id_article={$obj_art->fields['id_article']}";?>', 'Etes-vous s&ucirc;r(e)');" style="text-decoration:underline;">Enlever cette photo</a>
									</td>
								</tr>
								</table>
							</td>
						</tr>
						<?php
					}
					else {
						$obj_art->fields['image'] = '';
						$obj_art->save();
						?>
						<tr>
							<td align="right"><label for="article_image">Photo :</label></td>
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
						<?php
					}
				}
				else {
					?>
					<tr>
						<td align="right"><label for="article_image">Photo :</label></td>
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
					<?
				}
				?>
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
								natcasesort($chp['valeurs']);
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
				<?php endif; ?>

			<!--fieldset class="fieldset">
				<legend><b>Ecoproduit</b></legend>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td colspan="2">
						<input class="checkbox" type="checkbox" id="article_dev_durable" name="article_dev_durable" value="1" <?php if ($obj_art->fields['dev_durable']) echo 'checked'; ?> onclick="javascript: sw_disp('dd_detail');"/>
						<label for="article_dev_durable">Cet article fait partie de la gamme "&eacute;coproduit"</label>
					</td>
				</tr>
				</table>
			</fieldset-->

			<fieldset class="fieldset">
				<legend><b>Prix d'achat</b></legend>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td>
						<table cellpadding="2" cellspacing="0">
						<tr>
							<td>
								<select class="select" id="pa_cond" name="pa_cond">
									<option value="-1">-- Conditionnement --</option>
									<?php
									foreach ($a_conditionnements as $conditionnement) {
										echo "<option value=\"{$conditionnement['id']}\">{$conditionnement['libelle']}</option>";
									}
									?>
								</select>
							</td>
							<td><label for="pa_prix">Prix unitaire :&nbsp;</label></td>
							<td><input class="text" type="text" id="pa_prix" name="pa_prix" size="5" />&nbsp;&euro;&nbsp;</td>
							<td><input class="button" type="button" value="Ajouter le prix d'achat" onclick="javascript:refreshPrixAchat();" /></td>
						</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td><div id="div_prixachat"></div></td>
				</tr>
				</table>
			</fieldset>

			<table cellpadding="5" cellspacing="0" width="100%">
			<tr>
				<td align="center">
					<?php if (isset($_SESSION['catalogue']['artratt_retour'])): ?>
					<input class="button" type="button" value="Retour" onclick="javascript:document.location.href='<?php echo $dims->getScriptEnv(); ?>?subtab=artratt&op=edit&id_article=<?php echo $_SESSION['catalogue']['artratt_retour']; ?>';"/>
					<?php else: ?>
					<input class="button" type="button" value="Retour" onclick="javascript:document.location.href='<?php echo $dims->getScriptEnv(); ?>';"/>
					<?php endif ?>
					<input class="button" type="submit" value="<?php echo $save_btn; ?>"/>
				</td>
			</tr>
			</table>

			</form>

			<script language="JavaScript">
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
				function refreshPrixAchat() {
					var prix = dims_getelem('pa_prix');
					var cond = dims_getelem('pa_cond');

					dims_xmlhttprequest_todiv('admin.php', 'op=httpr_refresh_prixachat&id_article='+document.f_article.id_article.value+'&id_cond='+cond.value+'&pu='+prix.value, '', 'div_prixachat');
				}
				function sw_disp(elemId) {
					var elem = dims_getelem(elemId);
					if (elem.style.display == 'block') {
						elem.style.display = 'none';
					} else {
						elem.style.display = 'block';
					}
				}

				if (window.attachEvent) {
					window.attachEvent('onload', refreshPrixAchat());
				}
				else {
					window.onload = refreshPrixAchat();
				}
			</script>
			<?php
		echo $skin->close_simplebloc();
		break;
	case 'artratt':
		// suppression du retour vers l'édition des rattachements d'articles au retour
		if (isset($_SESSION['catalogue']['artratt_retour'])) {
			unset($_SESSION['catalogue']['artratt_retour']);
		}

		// chargement des groupes de rattachement
		if (!isset($_SESSION['ratt_grps'])) {
			$_SESSION['ratt_grps'] = array();
			$rs = $db->query('SELECT * FROM dims_mod_vpc_article_ratt_grp ORDER BY label');
			while ($row = $db->fetchrow($rs)) {
				$_SESSION['ratt_grps'][$row['id']] = $row['label'];
			}
		}


		$action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, true);

		switch ($action) {
			case 'httpr_liste_rattachements':
				while (@ob_end_clean());
				ob_start();

				$rs = $db->query('
					SELECT	ar.*, al.label, g.label AS grp_label
					FROM	dims_mod_vpc_article_ratt ar
					INNER JOIN	dims_mod_vpc_article_ratt_grp g
					ON			g.id = ar.id_grp
					INNER JOIN	dims_mod_cata_article a
					ON			a.reference = ar.rattachements
					INNER JOIN	dims_mod_cata_article_lang al
					ON			al.id_article_1 = a.id_article
					WHERE	ar.ref_article = \''.$obj_art->fields['reference'].'\'
					ORDER BY ar.id_grp, ar.rattachements');
				if ($db->numrows($rs)) {
					echo '
						<table class="tableauData">
						<tr>
							<th>Référence</th>
							<th>Désignation</th>
							<th>Type</th>
							<th>Actions</th>
						</tr>';
					while ($row = $db->fetchrow($rs)) {
						echo '
							<tr>
								<td>'.$row['rattachements'].'</td>
								<td>'.$row['label'].'</td>
								<td>
									<select name="ratt_grp_'.$row['rattachements'].'" onchange="javascript: modifier_rattachement(\''.$row['rattachements'].'\', '.$row['id_grp'].', this.value);">';
						foreach ($_SESSION['ratt_grps'] as $id => $label) {
							$selected = ($id == $row['id_grp']) ? ' selected="selected"' : '';
							echo '<option value="'.$id.'"'.$selected.'>'.$label.'</option>';
						}
						echo '
									</select>
								</td>
								<td class="center">
									<a href="javascript:void(0);" onclick="javascript:detacher_article(\''.$row['rattachements'].'\', '.$row['id_grp'].');"><img src="./common/modules/catalogue/img/supprimer.png" alt="Supprimer" /></a>
								</td>
							</tr>';
					}
					echo '</table>';
				}
				ob_end_flush();
				die();
				break;
			case 'httpr_recherche_article':
				while (@ob_end_clean());
				ob_start();

				$search_expr = dims_load_securvalue('search_expr', dims_const::_DIMS_CHAR_INPUT, true, false);
				if (trim($search_expr) != '') {
					$rs = $db->query('
						SELECT a.id_article, a.reference, al.label
						FROM dims_mod_cata_article a
						INNER JOIN	dims_mod_cata_article_lang al
						ON			al.id_article_1 = a.id_article
						WHERE ( a.reference = \''.$search_expr.'\' OR al.label LIKE \'%'.$search_expr.'%\' )
						AND		a.id_article != '.$obj_art->fields['id_article'].'
						AND		a.reference NOT IN (
							SELECT rattachements FROM dims_mod_vpc_article_ratt WHERE ref_article = \''.$obj_art->fields['reference'].'\'
						)');
					if ($db->numrows($rs)) {
						echo '
							<table class="tableauData">
							<tr>
								<th>Référence</th>
								<th>Désignation</th>
								<th>Type</th>
								<th>Actions</th>
							</tr>';
						while ($row = $db->fetchrow($rs)) {
							echo '
								<tr>
									<td>'.$row['reference'].'</td>
									<td>'.$row['label'].'</td>
									<td>
										<select name="ratt_grp_'.$row['reference'].'">
											<option value="0">Choisir un type de rattachement</option>';
							foreach ($_SESSION['ratt_grps'] as $id => $label) {
								echo '<option value="'.$id.'">'.$label.'</option>';
							}
							echo '
										</select>
									</td>
									<td class="center">
										<a href="javascript:void(0);" onclick="javascript:rattacher_article(\''.$row['reference'].'\');"><img src="./common/modules/catalogue/img/ajouter.png" alt="AJouter" /></a>
									</td>
								</tr>';
						}
						echo '</table>';
					}
					else {
						echo 'Aucun résultat.';
					}
				}

				ob_end_flush();
				die();
				break;
			case 'httpr_ajouter_rattachement':
				while (@ob_end_clean());
				ob_start();
				$ref = dims_load_securvalue('ref', dims_const::_DIMS_CHAR_INPUT, true, false);
				$id_grp = dims_load_securvalue('id_grp', dims_const::_DIMS_NUM_INPUT, true, false);
				if ($ref != '' && $id_grp > 0) {
					$db->query('INSERT INTO dims_mod_vpc_article_ratt VALUES(\''.$obj_art->fields['reference'].'\', \''.$ref.'\', '.$id_grp.', \'ind\')');
				}
				ob_end_flush();
				die();
				break;
			case 'httpr_modifier_rattachement':
				while (@ob_end_clean());
				ob_start();
				$ref = dims_load_securvalue('ref', dims_const::_DIMS_CHAR_INPUT, true, false);
				$id_grp_old = dims_load_securvalue('id_grp_old', dims_const::_DIMS_NUM_INPUT, true, false);
				$id_grp_new = dims_load_securvalue('id_grp_new', dims_const::_DIMS_NUM_INPUT, true, false);
				if ($ref != '' && $id_grp_old > 0 && $id_grp_new > 0) {
					$db->query('UPDATE dims_mod_vpc_article_ratt SET id_grp = '.$id_grp_new.' WHERE ref_article = \''.$obj_art->fields['reference'].'\' AND rattachements = \''.$ref.'\' AND id_grp = '.$id_grp_old);
				}
				ob_end_flush();
				die();
				break;
			case 'httpr_supprimer_rattachement':
				while (@ob_end_clean());
				ob_start();
				$ref = dims_load_securvalue('ref', dims_const::_DIMS_CHAR_INPUT, true, false);
				$id_grp = dims_load_securvalue('id_grp', dims_const::_DIMS_NUM_INPUT, true, false);
				if ($ref != '' && $id_grp > 0) {
					$db->query('DELETE FROM dims_mod_vpc_article_ratt WHERE ref_article = \''.$obj_art->fields['reference'].'\' AND rattachements = \''.$ref.'\' AND id_grp = '.$id_grp);
				}
				ob_end_flush();
				die();
				break;
			default:
				echo $skin->open_simplebloc('Rattachements de l\'article', '100%');
					?>
					<p>Rappel de l'article : <strong><?php echo $obj_art->fields['label']; ?></strong></p>
					<div id="div_listeRattachements"></div>

					<p>
						<form action="#" method="get" onsubmit="javascript: rechercheArticle(); return false;">
						<label for="search_expr">Rajouter un article :</label>
						<input type="text" id="search_expr" name="search_expr" />
						<input type="submit" value="Rechercher" onclick="javascript: rechercheArticle();" />
						</form>
					</p>
					<div id="div_listeResultats"></div>

					<script type="text/javascript">
						function refreshListeRattachements() {
							dims_xmlhttprequest_todiv('admin.php', 'op=edit&id_article=<?php echo $obj_art->fields['id_article']; ?>&action=httpr_liste_rattachements', '', 'div_listeRattachements');
						}

						function rechercheArticle() {
							if (document.getElementById('search_expr').value != '') {
								dims_xmlhttprequest_todiv('admin.php', 'op=edit&id_article=<?php echo $obj_art->fields['id_article']; ?>&action=httpr_recherche_article&search_expr='+document.getElementById('search_expr').value, '', 'div_listeResultats');
							}
						}

						function rattacher_article(ref) {
							// on vérifie si le type de rattachement est renseigné
							if (document.getElementsByName('ratt_grp_'+ref)[0].value == 0) {
								alert('Vous devez sélectionner un type de rattachement.');
							}
							else {
								dims_xmlhttprequest('admin.php', 'op=edit&id_article=<?php echo $obj_art->fields['id_article']; ?>&action=httpr_ajouter_rattachement&ref='+ref+'&id_grp='+document.getElementsByName('ratt_grp_'+ref)[0].value);
								refreshListeRattachements();
								rechercheArticle();
							}

						}

						function modifier_rattachement(ref, id_grp_old, id_grp_new) {
//							alert('ref='+ref+'\nid_grp_old='+id_grp_old+'\nid_grp_new='+id_grp_new);
							dims_xmlhttprequest('admin.php', 'op=edit&id_article=<?php echo $obj_art->fields['id_article']; ?>&action=httpr_modifier_rattachement&ref='+ref+'&id_grp_old='+id_grp_old+'&id_grp_new='+id_grp_new);
							refreshListeRattachements();
						}

						function detacher_article(ref, id_grp) {
							dims_xmlhttprequest('admin.php', 'op=edit&id_article=<?php echo $obj_art->fields['id_article']; ?>&action=httpr_supprimer_rattachement&ref='+ref+'&id_grp='+id_grp);
							refreshListeRattachements();
							rechercheArticle();
						}

/*
						if (window.attachEvent) {
							window.attachEvent('onload', refreshListeRattachements());
						}
						else {*/
							window.onload = function() {
								refreshListeRattachements();
								document.getElementById('search_expr').focus();
							}
/*						}*/
					</script>
					<?php
				echo $skin->close_simplebloc();
				break;
		}
		break;
}
