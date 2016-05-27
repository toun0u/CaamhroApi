<?php
$max_tri_cols_number = 2;

$_SESSION['desktopv2']['concepts']['suivi_search'] = dims_load_securvalue('suivi_search',dims_const::_DIMS_CHAR_INPUT,true,true,true,$_SESSION['desktopv2']['concepts']['suivi_search']);

// initialisation des filtres
$init_suivi_search = dims_load_securvalue('init_suivi_search', dims_const::_DIMS_NUM_INPUT, true, true);
if ($init_suivi_search) {
	$_SESSION['desktopv2']['concepts']['suivi_search'] = '';
}

// texte du champ de recherche
if ($_SESSION['desktopv2']['concepts']['suivi_search'] != '') {
	$text_suivi_search = $_SESSION['desktopv2']['concepts']['suivi_search'];
	$button['class'] = 'searching';
	$button['href'] = '/admin.php?init_suivi_search=1';
	$button['onclick'] = '';
}
else {
	$text_suivi_search = $_SESSION['cste']['LOOKING_FOR_A_DOCUMENT']. ' ?';
	$button['class'] = '';
	$button['href'] = 'Javascript: void(0);';
	$button['onclick'] = 'Javascript: if($(\'input#editbox_search_suivi\').val() != \''.$text_suivi_search.'\') $(this).closest(\'form\').submit();';
}

if(!isset($_SESSION['desktopv2']['concepts']['suivi']['page']))
	$_SESSION['desktopv2']['concepts']['suivi']['page'] = 0;

$page = dims_load_securvalue('page', dims_const::_DIMS_NUM_INPUT, true, true, true, $_SESSION['desktopv2']['concepts']['suivi']['page'], 0);
$suivi = new suivi();

?>

<div class="bloc_suivi">
	<div class="title_bloc_suivi"><h2><?php echo $_SESSION['cste']['_MONITORINGS']; ?></h2></div>
	<div class="bloc_zone_search_suivi bloc_zone_search">
	<div class="bloc_searchform_suivi">
		<form action="admin.php" method="post" name="formsearch" id="bloc_formsearch_suivi">
		<?
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("button_search_x"); // Le nom des input de type image sont modifiés par les navigateur en ajoutant _x et _y
			$token->field("button_search_y");
			$token->field("suivi_search");
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>
		<span>
			<input type="image" class="button_search" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_gauche.png" name="button_search" style="float:left">
			<input type="text" name="suivi_search" id="bloc_editbox_search_suivi" class="bloc_editbox_search editbox_search<? if ($button['class'] == 'searching') echo ' working'; ?>" maxlength="80" value="<?php echo $text_suivi_search; ?>" <? if ($button['class'] != 'searching') echo 'onfocus="Javascript:this.value=\'\'; $(this).addClass(\'working\');"'; ?> onblur="Javascript:if (this.value==''){ $(this).removeClass('working'); this.value='<?php echo $text_suivi_search; ?>'; }">
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_droite.png" style="float:left">

					<a class="<?php echo $button['class']; ?>" href="<?php echo $button['href']; ?>" onclick="<?php echo $button['onclick']; ?>"></a>
				</span>
		</form>
	</div>
	</div>
	<?php
	if ($_SESSION['desktopv2']['concepts']['sel_type'] != dims_const::_SYSTEM_OBJECT_SUIVI) {
		?>
		<div class="add_suivi">
			<a href="Javascript: void(0);" onclick="javascript:addSuiviConcepts();">
				<span><?php echo $_SESSION['cste']['ADD_DOCUMENT']; ?></span>
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/plus_newtype.png">
			</a>
		</div>
		<?php
	}

	$exercice = dims_load_securvalue("exercice", dims_const::_DIMS_NUM_INPUT, true, true, true, $_SESSION['desktopv2']['concepts']['suivi']['exercice']);
	$type = dims_load_securvalue("type", dims_const::_DIMS_CHAR_INPUT, false, true, true, $_SESSION['desktopv2']['concepts']['suivi']['type'], suivi::TYPE_TOUS);
	$accepte = dims_load_securvalue("accepte", dims_const::_DIMS_NUM_INPUT, true, true, true, $_SESSION['desktopv2']['concepts']['suivi']['accepte'], suivi::SUIVI_TOUS);
	$solde = dims_load_securvalue("solde", dims_const::_DIMS_NUM_INPUT, true, true, true, $_SESSION['desktopv2']['concepts']['suivi']['solde'], suivi::SUIVI_TOUS);

	$reset = dims_load_securvalue("reset_suivis_filters", dims_const::_DIMS_NUM_INPUT, true, true);
	$change_tri_order = dims_load_securvalue("change_tri_order", dims_const::_DIMS_NUM_INPUT, true, true, true, $change_tri_order, -1);
	$valider_devis = dims_load_securvalue("valider_devis", dims_const::_DIMS_NUM_INPUT, true, true);
	$cols = suivi::$cols;

	if($valider_devis > 0) {
		$query = "UPDATE dims_mod_business_suivi SET valide=1, datevalide = CURDATE() WHERE id_suivi=:idsuivi";
		$this->db->query($query, array(
			':idsuivi' => array('type' => PDO::PARAM_INT, 'value' => $valider_devis),
		));
	}

	if($change_tri_order != -1 && $change_tri_order < count($cols)) {
		if(isset($_SESSION['desktopv2']['concepts']['suivi']['tri'][$cols[$change_tri_order]])) {
			if($_SESSION['desktopv2']['concepts']['suivi']['tri'][$cols[$change_tri_order]]) {
				unset($_SESSION['desktopv2']['concepts']['suivi']['tri'][$cols[$change_tri_order]]);
				$_SESSION['desktopv2']['concepts']['suivi']['tri'][$cols[$change_tri_order]] = suivi::SUIVI_TRI_DESC;
			} else {
				unset($_SESSION['desktopv2']['concepts']['suivi']['tri'][$cols[$change_tri_order]]);
			}
		} else {
			if(isset($_SESSION['desktopv2']['concepts']['suivi']['tri']) && count($_SESSION['desktopv2']['concepts']['suivi']['tri']) == $max_tri_cols_number) {
				array_shift($_SESSION['desktopv2']['concepts']['suivi']['tri']);
			}

			$_SESSION['desktopv2']['concepts']['suivi']['tri'][$cols[$change_tri_order]] = suivi::SUIVI_TRI_ASC;
		}
	}

	$_SESSION['desktopv2']['concepts']['suivi']['exercice'] = $exercice;

	$suivi->page_courant = $page;
	$suivi->isPageLimited = true;
	$suivi->setPaginationParams(10, 10, false, $_SESSION['cste']['_FIRST'], $_SESSION['cste']['_LAST'], $_SESSION['cste']['_PREVIOUS'], $_SESSION['cste']['_NEXT']);

	if($reset != 0) {
		unset($_SESSION['desktopv2']['concepts']['suivi']);
		$exercice = 0;
		$type = suivi::TYPE_TOUS;
		$accepte = suivi::SUIVI_TOUS;
		$solde = suivi::SUIVI_TOUS;
		$suivi->page_courant = 0;
		unset($_SESSION['desktopv2']['concepts']['suivi']['tri']);
	}

	if(!isset($_SESSION['desktopv2']['concepts']['suivi']['tri']))
		$_SESSION['desktopv2']['concepts']['suivi']['tri'][$cols[5]] = false;

	/*
	if(isset($_SESSION['desktopv2']['concepts']['suivi']['tri']))
		dims_print_r($_SESSION['desktopv2']['concepts']['suivi']['tri']);
	*/
	?>
	<div class="add_suivi_content">
		<div class="filters">
			<form action="admin.php" method="POST">
				<?
					// Sécurisation du formulaire par token
					require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
					$token = new FormToken\TokenField;
					$token->field("exercice");
					$token->field("type");
					$token->field("accepte");
					$token->field("solde");
					$tokenHTML = $token->generate();
					echo $tokenHTML;
				?>
				<label for="exercice"><?php echo $_SESSION['cste']['_DUTY']; ?></label>
				<select name="exercice">
					<option value="<?php echo suivi::SUIVI_TOUS; ?>" <?php if($exercice == suivi::SUIVI_TOUS) echo "selected"; ?>><?php echo $_SESSION['cste']['_DIMS_ALLS']; ?></option>
					<?php
					$query = "SELECT distinct exercice FROM dims_mod_business_suivi ORDER BY exercice DESC";
					$res = $this->db->query($query);
					$tab = array();
					require_once DIMS_APP_PATH."modules/system/desktopV2/include/class_gescom_param.php";
					$params = class_gescom_param::getAllParams();

					while($row = $this->db->fetchrow($res))
							$tab[] = $row['exercice'];

					if($exercice == 0)
						// $exercice = isset($params['exercice']) ? $params['exercice'] : max($tab);
						$exercice = suivi::SUIVI_TOUS;

					foreach($tab as $ex) {
						?>
						<option value="<?php echo $ex; ?>" <?php if($exercice == $ex) echo "selected"; ?>><?php echo $ex; ?></option>
						<?php
					}
					?>
				</select>
				<label for="type"><?php echo $_SESSION['cste']['_TYPE']; ?></label>
				<select name="type">
					<option value="<?php echo suivi::TYPE_TOUS; ?>" <?php if($type == suivi::TYPE_TOUS) echo "selected"; ?>><?php echo $_SESSION['cste']['_DIMS_ALLS']; ?></option>
					<?php
					$query = "SELECT distinct type FROM dims_mod_business_suivi";
					$res = $this->db->query($query);

					while($row = $this->db->fetchrow($res)) {
						?>
						<option value="<?php echo $row['type']; ?>" <?php if($row['type'] == $type) echo "selected"; ?>>
						<?php
							switch($row['type']) {
								case suivi::TYPE_DEVIS:
									echo $_SESSION['cste']['QUOTATION'];
									break;

								case suivi::TYPE_FACTURE:
									echo $_SESSION['cste']['INVOICE'];
									break;

								case suivi::TYPE_AVOIR:
									echo $_SESSION['cste']['ASSET'];
									break;
							}
						?>
						</option>
						<?php
					}
					?>
				</select>
				<label for="accepte"><?php echo $_SESSION['cste']['_ACCEPTED']; ?></label>
				<select name="accepte">
					<option value="<?php echo suivi::SUIVI_TOUS; ?>" <?php if($accepte == suivi::SUIVI_TOUS) echo "selected"; ?>><?php echo $_SESSION['cste']['_DIMS_ALLS']; ?></option>
					<option value="<?php echo suivi::SUIVI_OUI; ?>" <?php if($accepte == suivi::SUIVI_OUI) echo "selected"; ?>><?php echo $_SESSION['cste']['_DIMS_YES']; ?></option>
					<option value="<?php echo suivi::SUIVI_NON; ?>" <?php if($accepte == suivi::SUIVI_NON) echo "selected"; ?>><?php echo $_SESSION['cste']['_DIMS_NO']; ?></option>
				</select>
				<label for="solde"><?php echo $_SESSION['cste']['_CLEARED']; ?></label>
				<select name="solde">
					<option value="<?php echo suivi::SUIVI_TOUS; ?>" <?php if($solde == suivi::SUIVI_TOUS) echo "selected"; ?>><?php echo $_SESSION['cste']['_DIMS_ALLS']; ?></option>
					<option value="<?php echo suivi::SUIVI_OUI; ?>" <?php if($solde == suivi::SUIVI_OUI) echo "selected"; ?>><?php echo $_SESSION['cste']['_DIMS_YES']; ?></option>
					<option value="<?php echo suivi::SUIVI_NON; ?>" <?php if($solde == suivi::SUIVI_NON) echo "selected"; ?>><?php echo $_SESSION['cste']['_DIMS_NO']; ?></option>
				</select>
				<div class="submit">
					<input type="submit" value="<?php echo $_SESSION['cste']['_FORMS_FILTER']; ?>"/> <?php echo $_SESSION['cste']['_DIMS_OR']; ?> <a href="admin.php?reset_suivis_filters=1"><?php echo $_SESSION['cste']['_DIMS_RESET']; ?></a>
				</div>
			</form>
		</div>
		<?php
			$asc = _DESKTOP_TPL_PATH."/gfx/common/img_tri_haut.png";
			$desc = _DESKTOP_TPL_PATH."/gfx/common/img_tri_bas.png";
			$non_trie = _DESKTOP_TPL_PATH."/gfx/common/img_tri_non_trie.png";
		?>
		<table style="width: 100%;">
			<thead>
				<tr>
					<td>
						<div><?php echo $_SESSION['cste']['_ACCEPTED']; ?></div>
						<a href="admin.php?change_tri_order=0">
							<img src="<?php echo isset($_SESSION['desktopv2']['concepts']['suivi']['tri'][$cols[0]]) ? (($_SESSION['desktopv2']['concepts']['suivi']['tri'][$cols[0]] == suivi::SUIVI_TRI_ASC) ? $asc : $desc) : $non_trie ?>"/>
						</a>
					</td>
					<td>
						<div><?php echo $_SESSION['cste']['_TYPE']; ?></div>
						<a href="admin.php?change_tri_order=1">
							<img src="<?php echo isset($_SESSION['desktopv2']['concepts']['suivi']['tri'][$cols[1]]) ? (($_SESSION['desktopv2']['concepts']['suivi']['tri'][$cols[1]] == suivi::SUIVI_TRI_ASC) ? $asc : $desc) : $non_trie ?>"/>
						</a>
					</td>
					<td>
						<div><?php echo $_SESSION['cste']['_NUMBER']; ?></div>
						<a href="admin.php?change_tri_order=2">
							<img src="<?php echo isset($_SESSION['desktopv2']['concepts']['suivi']['tri'][$cols[2]]) ? (($_SESSION['desktopv2']['concepts']['suivi']['tri'][$cols[2]] == suivi::SUIVI_TRI_ASC) ? $asc : $desc) : $non_trie ?>"/>
						</a>
					</td>
					<td>
						<div><?php echo $_SESSION['cste']['_AGENDA_LABEL_LABEL']; ?></div>
						<a href="admin.php?change_tri_order=3">
							<img src="<?php echo isset($_SESSION['desktopv2']['concepts']['suivi']['tri'][$cols[3]]) ? (($_SESSION['desktopv2']['concepts']['suivi']['tri'][$cols[3]] == suivi::SUIVI_TRI_ASC) ? $asc : $desc) : $non_trie ?>"/>
						</a>
					</td>
					<td>
						<div><?php echo $_SESSION['cste']['_DUTY']; ?></div>
						<a href="admin.php?change_tri_order=4">
							<img src="<?php echo isset($_SESSION['desktopv2']['concepts']['suivi']['tri'][$cols[4]]) ? (($_SESSION['desktopv2']['concepts']['suivi']['tri'][$cols[4]] == suivi::SUIVI_TRI_ASC) ? $asc : $desc) : $non_trie ?>"/>
						</a>
					</td>
					<td>
						<div><?php echo $_SESSION['cste']['_DIMS_DATE']; ?></div>
						<a href="admin.php?change_tri_order=5">
							<img src="<?php echo isset($_SESSION['desktopv2']['concepts']['suivi']['tri'][$cols[5]]) ? (($_SESSION['desktopv2']['concepts']['suivi']['tri'][$cols[5]] == suivi::SUIVI_TRI_ASC) ? $asc : $desc) : $non_trie ?>"/>
						</a>
					</td>
					<td>
						<div><?php echo $_SESSION['cste']['_DUTY_FREE_AMOUNT'];?></div>
						<a href="admin.php?change_tri_order=6">
							<img src="<?php echo isset($_SESSION['desktopv2']['concepts']['suivi']['tri'][$cols[6]]) ? (($_SESSION['desktopv2']['concepts']['suivi']['tri'][$cols[6]] == suivi::SUIVI_TRI_ASC) ? $asc : $desc) : $non_trie ?>"/>
						</a>
					</td>
					<td>
						<div><?php echo $_SESSION['cste']['_DISCOUNT'];?></div>
						<a href="admin.php?change_tri_order=7">
							<img src="<?php echo isset($_SESSION['desktopv2']['concepts']['suivi']['tri'][$cols[7]]) ? (($_SESSION['desktopv2']['concepts']['suivi']['tri'][$cols[7]] == suivi::SUIVI_TRI_ASC) ? $asc : $desc) : $non_trie ?>"/>
						</a>
					</td>
					<td>
						<div><?php echo $_SESSION['cste']['_TOTAL_DUTY_FREE_AMOUNT'];?></div>
						<a href="admin.php?change_tri_order=8">
							<img src="<?php echo isset($_SESSION['desktopv2']['concepts']['suivi']['tri'][$cols[8]]) ? (($_SESSION['desktopv2']['concepts']['suivi']['tri'][$cols[8]] == suivi::SUIVI_TRI_ASC) ? $asc : $desc) : $non_trie ?>"/>
						</a>
					</td>
					<td>
						<div><?php echo $_SESSION['cste']['_TOTAL_AMOUNT_WITH_DUTY'];?></div>
						<a href="admin.php?change_tri_order=9">
							<img src="<?php echo isset($_SESSION['desktopv2']['concepts']['suivi']['tri'][$cols[9]]) ? (($_SESSION['desktopv2']['concepts']['suivi']['tri'][$cols[9]] == suivi::SUIVI_TRI_ASC) ? $asc : $desc) : $non_trie ?>"/>
						</a>
					</td>
					<td>
						<div><?php echo $_SESSION['cste']['_CLEARED'];?></div>
						<a href="admin.php?change_tri_order=10">
							<img src="<?php echo isset($_SESSION['desktopv2']['concepts']['suivi']['tri'][$cols[10]]) ? (($_SESSION['desktopv2']['concepts']['suivi']['tri'][$cols[10]] == suivi::SUIVI_TRI_ASC) ? $asc : $desc) : $non_trie ?>"/>
						</a>
					</td>
					<td>
						<div><?php echo $_SESSION['cste']['_DIMS_ACTIONS']; ?></div>
					</td>
				</tr>
			</thead>
			<tbody>
				<?php
				$search = isset($_SESSION['desktopv2']['concepts']['suivi_search']) ? $_SESSION['desktopv2']['concepts']['suivi_search'] : '';

				switch($_SESSION['desktopv2']['concepts']['sel_type']) {
					case dims_const::_SYSTEM_OBJECT_TIERS:
						$filters = "tiers_id=".$_SESSION['desktopv2']['concepts']['sel_id'];
						break;
					case dims_const::_SYSTEM_OBJECT_CONTACT:
						$filters = "contact_id=".$_SESSION['desktopv2']['concepts']['sel_id'];
						break;
					default:
						$filters = "0=1";
						break;
				}

				$filters .= (($exercice != suivi::SUIVI_TOUS) ? " AND s.exercice=".$exercice : "")
				.(($type != suivi::TYPE_TOUS) ? " AND s.type='".$type."'" : "")
				.(($accepte != suivi::SUIVI_TOUS) ? " AND s.valide=".$accepte : "")
				.(($solde != suivi::SUIVI_TOUS) ? (" AND s.solde".(($solde == suivi::SUIVI_NON) ? "!=" : "=")."0") : "");

				/*
				dims_print_r($_SESSION['desktopv2']['concepts']['sel_id']);
				dims_print_r($filters);
				*/

				$suivi_liste = new suivi();
				$suivi_liste->sql_debut = 0;
				$suivi_liste->limite_key = $suivi_liste->getAll($search, true, $filters, $_SESSION['desktopv2']['concepts']['suivi']['tri']);
				$_SESSION['desktopv2']['concepts']['liste_suivis'] = array();

				foreach($suivi_liste->getAll($search, false, $filters, $_SESSION['desktopv2']['concepts']['suivi']['tri']) as $res) {
					$_SESSION['desktopv2']['concepts']['liste_suivis'][] = $res->fields['id_suivi'];
				}

				foreach($suivi->getAll($search, false, $filters, $_SESSION['desktopv2']['concepts']['suivi']['tri']) as $res) {
					$res->setLightAttribute('concept_not_event', $this->getLightAttribute('concept_not_event'));
					$res->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/bloc_suivi/fiche_bloc_suivi.tpl.php');
				}

				/*
				global $lstObj;
				if (isset($lstObj['suivis'])) {
					foreach ($lstObj['suivis'] as $suivi) {
						if ( $_SESSION['desktopv2']['concepts']['suivi_search'] == '' || stristr($suivi->fields['libelle'], $_SESSION['desktopv2']['concepts']['suivi_search']) ) {
							$suivi->setLightAttribute('concept_not_event', $this->getLightAttribute('concept_not_event'));
							$suivi->setLightAttribute('type', $suivi->getType());
							$suivi->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/bloc_suivi/fiche_bloc_suivi.tpl.php');
						}
					}
				}
				*/
				?>
			</tbody>
		</table>
			<div class="pagination">
			<?php
				$suivi->rewrite = true;
				$pages = $suivi->getPagination();

				//dims_print_r($pages);
				if(count($pages) > 1) {
			?>
					<span class="label"><?php echo $_SESSION['cste']['_DIMS_LABEL_PAGE']; ?> :</span>
			<?php
					foreach($pages as $k=>$p){
						if(!empty($p['url'])) {
							$p_url = substr($p['url'], strpos($p['url'], "?")+1);
							//dims_print_r($p_url);
							$p_array = explode("&", $p_url);

							foreach($p_array as $key => $val) {
								$p_array[$key] = explode("=", $val);
							}

							//dims_print_r($p_array);
							$p['url'] = "admin.php?";

							foreach($p_array as $var) {
								if($var[0] != "change_tri_order")
									$p['url'] .= $var[0]."=".$var[1]."&";
							}

							//dims_print_r($p['url']);

							$p['url'] = substr($p['url'], 0, strlen($p['url'])-1);
							echo '<a href="'.$p['url'].'" title="'.$p['title'].'">'.$p['label'].'</a>';
						}
						else echo '<span class="current">'.$p['label'].'</span>';
					}
				} else {
			?>
				<span class="label"><?php echo $_SESSION['cste']['_DIMS_LABEL_PAGE']; ?> :</span><span class="current">1</span>
			<?php
				}
			?>
		</div>
	</div>
</div>
<script language="JavaScript" type="text/JavaScript">
<!--
	$(document).ready(function() {
		var change_filters = function() {
			var accepte_select = $('div.filters select[name="accepte"]:first');
			var solde_select = $('div.filters select[name="solde"]:first');
			var accepte = $('div.filters select[name="accepte"]:first, div.filters label[for="accepte"]:first');
			var solde = $('div.filters select[name="solde"]:first, div.filters label[for="solde"]:first');

			switch($('div.filters select[name="type"]:first option:selected').attr('value')) {
				case '<?php echo suivi::TYPE_DEVIS; ?>':
					accepte.show();
					solde.hide();
					solde_select.val(<?php echo suivi::SUIVI_TOUS ?>);
					break;

				case '<?php echo suivi::TYPE_FACTURE; ?>':
					accepte.hide();
					accepte_select.val(<?php echo suivi::SUIVI_TOUS ?>);
					solde.show();
					break;
				default:
					accepte.show();
					solde.show();
					break;
			}
		};

		$('div.filters select[name="type"]:first').change(function() { change_filters(); });
		change_filters();
	});
-->
</script>
