<?php
include_once './common/modules/catalogue/include/class_article.php';
include_once './common/modules/catalogue/include/class_article_ratt.php';

$a_types = array('ind' => 'Individuelle', 'grp' => 'Group&eacute;e');
if (isset($_REQUEST['id_grp'])) {
	$id_grp = $_REQUEST['id_grp'];
}


$action = dims_load_securvalue('action', _DIMS_CHAR_INPUT, true, true);
$ref = dims_load_securvalue('ref', _DIMS_CHAR_INPUT, true, true);
$id_grp = dims_load_securvalue('id_grp', _DIMS_CHAR_INPUT, true, true);
$ratt_id_grp = dims_load_securvalue('ratt_id_grp', _DIMS_CHAR_INPUT, true, true);
$ratt_type = dims_load_securvalue('ratt_type', _DIMS_CHAR_INPUT, true, true);
$ratt_rattachements = dims_load_securvalue('ratt_rattachements', _DIMS_CHAR_INPUT, true, true);

if ($action == '') $action = 'search';


switch ($action) {
	case 'search':
		echo $skin->open_simplebloc('Edition des rattachements');
		?>
		<form name="f_search" action="<?php echo $dims->getScriptEnv(); ?>" method="post">
		<input type="hidden" name="part" value="rattachements" />
		<input type="hidden" name="action" value="search" />

		<table cellpadding="4" cellspacing="0">
		<tr>
			<td><label for="ref">Référence</label></td>
			<td><input type="text" id="ref" name="ref" value="<?php echo $ref; ?>" /></td>
			<td><input type="submit" value="Cherche" /></td>
		</tr>
		</table>
		</form>
		<?php
		if (!empty($ref)) {
			$article = new article();
			if ($article->open($ref)) {
				dims_redirect($dims->getScriptEnv().'?part=rattachements&action=edit_ratt&ref='.$article->fields['PREF']);
			}
			else {
				echo "Référence introuvable.";
			}
		}
		?>
		<script type="text/javascript">
			document.f_search.ref.focus();
		</script>
		<?php
		echo $skin->close_simplebloc();
		break;
	case 'edit':
	case 'edit_ratt':
		// ouverture de l'article
		$article = new article();
		$article->open($ref);

		// recuperation des rattachements
		$a_ratt = array();
		$rs = $db->query("SELECT * FROM dims_mod_vpc_article_ratt WHERE ref_article = '{$article->fields['PREF']}'");
		while ($row = $db->fetchrow($rs)) {
			$a_ratt[$row['id_grp']] = $row;
		}

		// recuperation des groupes
		$a_grp = array();
		$rs = $db->query("SELECT * FROM dims_mod_vpc_article_ratt_grp");
		while ($row = $db->fetchrow($rs)) {
			$a_grp[$row['id']] = $row;
		}

		echo $skin->open_simplebloc('Modifier les rattachements de l\'article');
		?>
		<form name="f_ratt" action="<?php echo $dims->getScriptEnv(); ?>" method="post"  onsubmit="javascript:return field_validate(this);">
		<input type="Hidden" name="part" value="rattachements" />
		<input type="Hidden" name="action" value="save" />
		<input type="Hidden" name="ref" value="<?php echo $ref; ?>" />
		<input type="Hidden" name="ratt_id_grp" value="<?php echo $id_grp; ?>" />
		<input type="hidden" name="ratt_rattachements" value="<?=$article_ratt->fields['rattachements'];?>" />

		<table cellpadding="4" cellspacing="0">
		<tr>
			<td width="50%" valign="top">
				<table cellpadding="2" cellspacing="1" width="100%">
				<tr>
					<td align="right">Rappel de la r&eacute;f&eacute;rence : </td>
					<td><?php echo $article->fields['PREF']; ?></td>
				</tr>
				<tr>
					<td align="right">Ajouter pour le groupe : </td>
					<td>
						<select name="id_grp" onchange="javascript:document.location.href='<?php echo $dims->getScriptEnv(); ?>?part=rattachements&action=edit&ref=<?php echo $ref; ?>&id_grp='+document.f_ratt.id_grp.value;">
							<option value="">--- Choisissez ---</option>
							<?php
							foreach ($a_grp as $grp) {
								echo "<option value=\"{$grp['id']}\">{$grp['label']}</option>";
							}
							?>
						</select>
					</td>
				</tr>
				<?php
				if (sizeof($a_ratt)) {
					?>
					<tr>
						<td colspan="2" align="center">
							<table cellpadding="2" cellspacing="1">
							<tr bgcolor="<?php echo $skin->values['bgline2']; ?>">
								<th>Groupe</th>
								<th>Type</th>
								<th>Articles rattach&eacute;s</th>
								<th colspan="2">&nbsp;</th>
							</tr>
							<?php
							foreach ($a_ratt as $ratt) {
								$color = ($color == $skin->values['bgline1']) ? $skin->values['bgline2'] : $skin->values['bgline1'];

								echo "
									<tr bgcolor=\"$color\">
										<td>{$a_grp[$ratt['id_grp']]['label']}</td>
										<td>{$a_types[$ratt['type']]}</td>
										<td>{$ratt['rattachements']}</td>
										<td>&nbsp;<a href=\"".$dims->getScriptEnv()."?part=rattachements&action=edit&ref=$ref&id_grp={$ratt['id_grp']}\"><img src=\"./common/modules/catalogue/img/ico_modify.gif\" alt=\"Modifier les rattachements\" border=\"0\"/></a>&nbsp;</td>
										<td>&nbsp;<a href=\"javascript:dims_confirmlink('".$dims->getScriptEnv()."?part=rattachements&action=delete&ref=$ref&id_grp={$ratt['id_grp']}','Etes-vous s&ucirc;r(e) ?');\"><img src=\"./common/modules/catalogue/img/ico_delete.gif\" alt=\"Supprimer les rattachements\" border=\"0\"/></a>&nbsp;</td>
									</tr>";
							}
							?>
							</table>
						</td>
					</tr>
					<?php
				}
				if (!empty($action) && $action == 'edit') {
					$article_ratt = new article_ratt();
					if (!empty($id_grp) && is_numeric($id_grp)) {
						$article_ratt->open($article->fields['PREF'], $id_grp);
					}
					?>
					<tr>
						<td colspan="2" align="center">
							<br/>
							<table cellpadding="2" cellspacing="0">
							<tr><td align="center" colspan="2"><strong>Rattachements pour le groupe "<?php echo $a_grp[$id_grp]['label']; ?>"</strong></td></tr>
							<tr>
								<td align="center" colspan="2">
									<input type="radio" name="ratt_type" id="ratt_type_ind" value="ind" <?php if ($article_ratt->fields['type'] == 'ind') echo 'checked'; ?>/> <label for="ratt_type_ind">Individuelle</label>
									<input type="radio" name="ratt_type" id="ratt_type_grp" value="grp" <?php if ($article_ratt->fields['type'] == 'grp') echo 'checked'; ?>/> <label for="ratt_type_grp">Group&eacute;e</label>
								</td>
							</tr>
							<!--<tr><td align="center" colspan="2"><textarea name="ratt_rattachements" cols="50" rows="3"><?php echo $article_ratt->fields['rattachements']; ?></textarea></td></tr>-->
							<tr>
								<td valign="top">
									<br/>
									<input style="width:25px;" type="button" class="button" value="+" onclick="javascript:move_value(document.f_ratt.v_valeurs, 1);">
									<br/>
									<input style="width:25px;margin-top:4px;" type="button" class="button" value="-" onclick="javascript:move_value(document.f_ratt.v_valeurs, -1);">
								</td>
								<td>
									<select id="v_valeurs" name="v_valeurs" class="select" size="12" style="width:250px" onclick="sel_value(this);">
									<?php
									$a_vals = array();
									foreach (explode(';', $article_ratt->fields['rattachements']) as $value) {
										if ($value != '') {
											$color = ($color == $skin->values['bgline2']) ? $skin->values['bgline1'] : $skin->values['bgline2'];
											echo "<option value=\"$value\" style=\"background-color:$color;\">$value</option>";
										}
									}
									?>
									</select>
								</td>
							</tr>
							<tr>
								<td></td>
								<td>
									<input style="width:250px;" name="v_newvalue" type="text" class="text">
								</td>
							</tr>
							<tr>
								<td></td>
								<td>
									<input type="button" class="button" value="Ajouter" onclick="javascript:add_value(document.f_ratt.v_valeurs, document.f_ratt.v_newvalue);">
									<input type="button" class="button" value="Supprimer" onclick="javascript:delete_value(document.f_ratt.v_valeurs);">
								</td>
							</tr>
							<tr>
								<td colspan="2">&nbsp;</td>
							</tr>
							</table>
						</td>
					</tr>
					<?
				}
				?>
				<tr>
					<td colspan="2" align="center">
						<table width="100%">
						<tr>
							<td align="right"><input class="Button" type="Button" value="Retour" onClick="javascript:document.location.href='<?php echo $dims->getScriptEnv().'?part=rattachements&action=search'; ?>';"></td>
							<td align="left"><input class="Button" type="Submit" value="Enregistrer"></td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</form>
		</table>

		<script language="JavaScript">
			function field_validate(form) {
				form.ratt_rattachements.value = '';

				for (i = 0; i < form.v_valeurs.length; i++) {
					if (form.ratt_rattachements.value != '') form.ratt_rattachements.value += ';';
					form.ratt_rattachements.value += form.v_valeurs[i].value;
				}

				return(true);
			}

			function sel_value(src) {
				srcField = document.f_ratt.v_newvalue;
				srcField.value = src.value;
				srcField.focus();
			}

			function add_value(lst, val) {
				if (val.value != '') {
					lst.options[lst.length] = new Option(val.value, val.value);
				}
				val.value = '';
				val.focus();
			}

			function modify_value(lst, val) {
				sel = lst.selectedIndex;
				if (sel > -1) {
					lst.options[sel].value = val.value;
					lst.options[sel].text = val.value;
				}
				val.focus();
			}

			function delete_value(lst) {
				sel = lst.selectedIndex;

				if (sel < lst.length - 1) {
					lst[sel] = lst[sel + 1];
					lst.selectedIndex = sel;
				}
				else lst.length--;
			}

			function move_value(lst, mv) {
				sel = lst.selectedIndex;
				if (sel - mv >= 0 && sel - mv < lst.length) {
					var tmp;
					tmp = lst[sel - mv].value;

					lst[sel - mv].text = lst[sel].value;
					lst[sel - mv].value = lst[sel].value;

					lst[sel].text = tmp;
					lst[sel].value = tmp;

					lst.selectedIndex = lst.selectedIndex - mv;
				}
			}
		</script>

		<?php
		echo $skin->close_simplebloc();
		break;
	case 'save':
		if (!empty($ref) && !empty($ratt_id_grp) && !empty($ratt_type) && !empty($ratt_rattachements)) {
			$article_ratt = new article_ratt();
			$article_ratt->open($ref, $ratt_id_grp);
			$article_ratt->old_rattachements = $article_ratt->fields['rattachements'];
			$article_ratt->fields['type'] = $ratt_type;
			$article_ratt->fields['rattachements'] = trim($ratt_rattachements);
			$article_ratt->save();
		}
		dims_redirect($dims->getScriptEnv()."?part=rattachements&action=edit_ratt&ref=$ref");
		break;
	case 'delete':
		if (!empty($ref) && !empty($id_grp)) {
			$article_ratt = new article_ratt();
			$article_ratt->open($ref, $id_grp);
			$article_ratt->delete();
		}
		dims_redirect($dims->getScriptEnv()."?part=rattachements&action=edit_ratt&ref=$ref");
		break;
}
