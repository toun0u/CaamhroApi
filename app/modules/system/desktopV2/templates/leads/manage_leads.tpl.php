<?php
require_once DIMS_APP_PATH."modules/system/opportunity/class_opportunity.php";
require_once DIMS_APP_PATH."include/class_skin_common.php";
$lead = new dims_lead();
$oldest = $lead->getOldest();
$newest = $lead->getNewest();

$dims = dims::getInstance();
$db = $dims->getDb();

// echelle de 0 au budget max par défaut
$maxBudget = dims_lead::getMaxBudget();
$limiteBudget = array(
	'min' => 0,
	'max' => $maxBudget
	);

if (!isset($_SESSION['desktopv2']['lead']['filters']['budget_min'])) {
	$_SESSION['desktopv2']['lead']['filters']['budget_min'] = $limiteBudget['min'];
	$_SESSION['desktopv2']['lead']['filters']['budget_max'] = $limiteBudget['max'];
}


// filtres
$_SESSION['desktopv2']['lead']['filters']['status']			= dims_load_securvalue('status', dims_const::_DIMS_NUM_INPUT, false, true, true, $_SESSION['desktopv2']['lead']['filters']['status']);
$_SESSION['desktopv2']['lead']['filters']['responsible']	= dims_load_securvalue('responsible', dims_const::_DIMS_NUM_INPUT, false, true, true, $_SESSION['desktopv2']['lead']['filters']['responsible']);
$_SESSION['desktopv2']['lead']['filters']['tiers']			= dims_load_securvalue('tiers', dims_const::_DIMS_NUM_INPUT, false, true, true, $_SESSION['desktopv2']['lead']['filters']['tiers']);
$_SESSION['desktopv2']['lead']['filters']['product']		= dims_load_securvalue('product', dims_const::_DIMS_NUM_INPUT, false, true, true, $_SESSION['desktopv2']['lead']['filters']['product']);
$_SESSION['desktopv2']['lead']['filters']['budget_min']		= dims_load_securvalue('budget_min', dims_const::_DIMS_NUM_INPUT, false, true, true, $_SESSION['desktopv2']['lead']['filters']['budget_min']);
$_SESSION['desktopv2']['lead']['filters']['budget_max']		= dims_load_securvalue('budget_max', dims_const::_DIMS_NUM_INPUT, false, true, true, $_SESSION['desktopv2']['lead']['filters']['budget_max']);
$_SESSION['desktopv2']['lead']['filters']['echeance_deb']	= dims_load_securvalue('echeance_deb', dims_const::_DIMS_CHAR_INPUT, false, true, true, $_SESSION['desktopv2']['lead']['filters']['echeance_deb']);
$_SESSION['desktopv2']['lead']['filters']['echeance_fin']	= dims_load_securvalue('echeance_fin', dims_const::_DIMS_CHAR_INPUT, false, true, true, $_SESSION['desktopv2']['lead']['filters']['echeance_fin']);
?>

<div>
	<div class="title_leads">
		<img src="<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/manage_opportunities.png"/>
		<h2><?php echo $_SESSION['cste']['_SYSTEM_MANAGE_OPPORTUNITIES']; ?></h2>
	</div>
	<div>
		<form id="filter_form" name="filter_form" action="<?php echo $dims->getScriptEnv(); ?>" method="post">
			<?
				// Sécurisation du formulaire par token
				require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
				$token = new FormToken\TokenField;
				$token->field("submenu",	_DESKTOP_V2_DESKTOP);
				$token->field("mode",		"leads");
				$token->field("action",		"manage");
				$token->field("budget_min");
				$token->field("budget_max");
				$token->field("status");
				$token->field("responsible");
				$token->field("tiers");
				$token->field("product");
				$token->field("echeance_deb");
				$token->field("echeance_fin");
				$tokenHTML = $token->generate();
				echo $tokenHTML;
			?>
			<input type="hidden" name="submenu" value="<?php echo _DESKTOP_V2_DESKTOP; ?>" />
			<input type="hidden" name="mode" value="leads" />
			<input type="hidden" name="action" value="manage" />
			<input type="hidden" name="budget_min" id="budget_min" value="0"/>
			<input type="hidden" name="budget_max" id="budget_max" value="<?php echo $_SESSION['desktopv2']['lead']['filters']['budget_max']; ?>"/>

			<div class="filters">
				<table>
					<tr>
						<td>
							<label class="status" for="status"><?php echo $_SESSION['cste']['STATUS']; ?></label>
							<select id="status" name="status">
								<option value="-1">Toutes</option>
								<option value="<?php echo dims_lead::STATUS_IN_PROGRESS; ?>" <?php if ($_SESSION['desktopv2']['lead']['filters']['status'] == dims_lead::STATUS_IN_PROGRESS) echo 'selected="selected"'; ?>>En cours</option>
								<option value="<?php echo dims_lead::STATUS_LOST; ?>" <?php if ($_SESSION['desktopv2']['lead']['filters']['status'] == dims_lead::STATUS_LOST) echo 'selected="selected"'; ?>>Perdu</option>
								<option value="<?php echo dims_lead::STATUS_ABANDONED; ?>" <?php if ($_SESSION['desktopv2']['lead']['filters']['status'] == dims_lead::STATUS_ABANDONED) echo 'selected="selected"'; ?>>Abandonné</option>
								<option value="<?php echo dims_lead::STATUS_WON; ?>" <?php if ($_SESSION['desktopv2']['lead']['filters']['status'] == dims_lead::STATUS_WON) echo 'selected="selected"'; ?>>Gagné</option>
							</select>
						</td>
						<td>
							<label class="responsible" for="responsible"><?php echo $_SESSION['cste']['_DIMS_LABEL_RESPONSIBLE']; ?></label>
							<select id="responsible" name="responsible">
								<option value="0">Tous</option>
								<?php
								$sel = ($_SESSION['desktopv2']['lead']['filters']['responsible'] == $_SESSION['dims']['userid']) ? ' selected="selected"' : '';
								?>
								<option value="<?php echo $_SESSION['dims']['userid']; ?>"<?php echo $sel; ?>>Vous-même</option>
								<?php
								// tous les utilisateurs du workspace sauf celui qui est connecté
								$rs = $db->query('
									SELECT u.id, u.firstname, u.lastname
									FROM dims_user u
									INNER JOIN dims_workspace_user wu
									ON wu.id_user = u.id
									AND wu.id_workspace = :idworkspace
									WHERE u.id != :iduser', array(
										':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
										':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
									));
								while ($row = $db->fetchrow($rs)) {
									$sel = ($row['id'] == $_SESSION['desktopv2']['lead']['filters']['responsible']) ? ' selected="selected"' : '';
									echo '<option value="'.$row['id'].'"'.$sel.'>'.$row['firstname'].' '.$row['lastname'].'</option>';
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<label class="tiers" for="tiers">Compte</label>
							<select id="tiers" name="tiers">
								<option value="-1">Tous</option>
								<?php
								// tous les tiers du workspace
								$rs = $db->query('SELECT id, intitule FROM dims_mod_business_tiers WHERE id_workspace = :idworkspace', array(
									':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
								));
								while ($row = $db->fetchrow($rs)) {
									$sel = ($row['id'] == $_SESSION['desktopv2']['lead']['filters']['tiers']) ? ' selected="selected"' : '';
									echo '<option value="'.$row['id'].'"'.$sel.'>'.$row['intitule'].'</option>';
								}
								?>
							</select>
						</td>
						<td>
							<label class="product" for="product">Produit</label>
							<select id="product" name="product">
								<option value="-1">Tous</option>
								<?php
								// tous les produits du workspace
								$rs = $db->query('SELECT id, libelle FROM dims_mod_business_produit WHERE id_workspace = :idworkspace', array(
									':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
								));
								while ($row = $db->fetchrow($rs)) {
									$sel = ($row['id'] == $_SESSION['desktopv2']['lead']['filters']['product']) ? ' selected="selected"' : '';
									echo '<option value="'.$row['id'].'"'.$sel.'>'.$row['libelle'].'</option>';
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<table>
								<tbody>
									<tr>
										<td>
											&nbsp;
										</td>
										<td class="compare_petit_title" id="prix_mini">
											<?php echo $_SESSION['cste']['_FROM']; ?> <?php echo $limiteBudget['min'];?> €
										</td>
										<td class="compare_petit_title_droite" id="prix_maxi">
											<?php echo $_SESSION['cste']['_DIMS_LABEL_A']; ?> <?php echo $limiteBudget['max'];?> €
										</td>
									</tr>
									<tr>
										<td class="compare_title">
											Prix
										</td>
										<td colspan="2">
											<div id="slider-range" style="width: 230px"></div>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
						<td>
							<label class="echeance_deb" for="echeance_deb">Echéance</label>
							du <input type="text" id="echeance_deb" name="echeance_deb" value="<?php echo $_SESSION['desktopv2']['lead']['filters']['echeance_deb']; ?>" />
							<img style="vertical-align:bottom" src="./common/img/calendar.png" alt="Date de début" onclick="javascript:dims_calendar_open('echeance_deb', event);" />
							au <input type="text" id="echeance_fin" name="echeance_fin" value="<?php echo $_SESSION['desktopv2']['lead']['filters']['echeance_fin']; ?>" />
							<img style="vertical-align:bottom" src="./common/img/calendar.png" alt="Date de fin" onclick="javascript:dims_calendar_open('echeance_fin', event);" />
						</td>
					</tr>
				</table>
				<div class="sub_form">
					<div class="form_buttons">
						<div>
							<a>Réinitialiser</a>
						</div>
						<div>
							<span>ou</span>
						</div>
						<div>
							<input type="submit" value="<?php echo $_SESSION['cste']['_FORMS_FILTER']; ?>"/>
						</div>
					</div>
				</div>
			</div>
		</form>

		<br/>
		<div class="opportunities_table">
			<?php
			$tabl = array();
			$tabl['headers'][] = '';
			$tabl['headers'][] = 'Echéance';
			$tabl['headers'][] = 'Budget';
			$tabl['headers'][] = 'Libellé';
			$tabl['headers'][] = 'Compte';
			$tabl['headers'][] = 'Produit';
			$tabl['headers'][] = $_SESSION['cste']['_DIMS_ACTIONS'];
			$tabl['data']['aasorting']['num'] = 2;
			$tabl['data']['aasorting']['order'] = 'desc';
			$tabl['data']['bSortable'][6] = false;
			$tabl['data']['classes'][0] = 'w20p';
			$tabl['data']['classes'][6] = 'w20p';
			$skin = skin_common::getInstance();
			echo $skin->displayArray($tabl, 'opportunities', 'admin.php?dims_op=desktopv2&action=load_leads');
			?>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		var changed = $();

		$("#filter_form div.filters table td select").change(function() {
			changed.add($(this));
		});

		$("div.time_filters select").change(function() {
			var select = $(this);

			$("#slider").slider({
				step: select.val()*86400
			});
		});

		$('#today').click(function() {
			if($("#slider").slider("option", "disabled") == false) {
				$("#slider").slider("value", <?php echo time(); ?>);
				updateDate();
			}
		});

		$('#whole_period').click(function() {
			$("#slider").slider("disable");
		});
	});

	// plugin chosen sur les listes déroulantes
	$('#status').chosen({no_results_text: "No results matched"});
	$('#responsible').chosen({no_results_text: "No results matched"});
	$('#tiers').chosen({no_results_text: "No results matched"});
	$('#product').chosen({no_results_text: "No results matched"});

	// slider budget
	$( "#slider-range" ).slider({
		range: true,
		min: <?php echo $limiteBudget['min']; ?>,
		max: <?php echo $limiteBudget['max']; ?>,
		values: [<?php echo $_SESSION['desktopv2']['lead']['filters']['budget_min'];?>, <?php echo $_SESSION['desktopv2']['lead']['filters']['budget_max'];?> ],
		create: function() {
			$("#prix_mini").html("de "+<?php echo $_SESSION['desktopv2']['lead']['filters']['budget_min']; ?>+" €");
			$("#prix_maxi").html("à "+<?php echo $_SESSION['desktopv2']['lead']['filters']['budget_max']; ?>+" €");
			$('#budget_min').val(<?php echo $limiteBudget['min']; ?>);
			$('#budget_max').val(<?php echo $limiteBudget['max']; ?>);
		},
		slide: function( event, ui ) {
			$("#prix_mini").html("de "+ui.values[ 0 ]+" €");
			$("#prix_maxi").html("à "+ui.values[ 1 ]+" €");
			$('#budget_min').val(ui.values[ 0 ]);
			$('#budget_max').val(ui.values[ 1 ]);
		}
	});
</script>
