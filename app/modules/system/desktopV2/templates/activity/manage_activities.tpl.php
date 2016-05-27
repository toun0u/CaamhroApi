<?php
require_once DIMS_APP_PATH."modules/system/leads/class_lead.php";
require_once DIMS_APP_PATH."include/class_skin_common.php";
$activity = new dims_activity();
$oldestActivity = $activity->getOldest();
$newestActivity = $activity->getNewest();

// période d'activité
$oldest = mktime(0, 0, 0, substr($oldestActivity['datejour'], 5, 2), substr($oldestActivity['datejour'], 8, 2), substr($oldestActivity['datejour'], 0, 4));
if ( mktime(0, 0, 0, substr($newestActivity['datejour'], 5, 2), substr($newestActivity['datejour'], 8, 2), substr($newestActivity['datejour'], 0, 4)) < time() ) {
	$newest = time();
}
else {
	$newest = mktime(0, 0, 0, substr($newestActivity['datejour'], 5, 2), substr($newestActivity['datejour'], 8, 2), substr($newestActivity['datejour'], 0, 4));
}

$dims = dims::getInstance();
$db = $dims->getDb();

// filtres
if (!isset($_SESSION['desktopv2']['activity']['filters']['slider_date'])) {
	$_SESSION['desktopv2']['activity']['filters']['slider_date'] = time();
}
if (!isset($_SESSION['desktopv2']['activity']['filters']['whole_period'])) {
	$_SESSION['desktopv2']['activity']['filters']['whole_period'] = 0;
}

$_SESSION['desktopv2']['activity']['filters']['status']				= dims_load_securvalue('status', dims_const::_DIMS_NUM_INPUT, false, true, true, $_SESSION['desktopv2']['activity']['filters']['status']);
$_SESSION['desktopv2']['activity']['filters']['responsible']		= dims_load_securvalue('responsible', dims_const::_DIMS_NUM_INPUT, false, true, true, $_SESSION['desktopv2']['activity']['filters']['responsible']);
$_SESSION['desktopv2']['activity']['filters']['type']				= dims_load_securvalue('type', dims_const::_DIMS_NUM_INPUT, false, true, true, $_SESSION['desktopv2']['activity']['filters']['type']);
$_SESSION['desktopv2']['activity']['filters']['keywords']			= dims_load_securvalue('keywords', dims_const::_DIMS_CHAR_INPUT, false, true, true, $_SESSION['desktopv2']['activity']['filters']['keywords']);
$_SESSION['desktopv2']['activity']['filters']['opportunity']		= dims_load_securvalue('opportunity', dims_const::_DIMS_NUM_INPUT, false, true, true, $_SESSION['desktopv2']['activity']['filters']['opportunity']);
$_SESSION['desktopv2']['activity']['filters']['slider_date']		= dims_load_securvalue('slider_date', dims_const::_DIMS_NUM_INPUT, false, true, true, $_SESSION['desktopv2']['activity']['filters']['slider_date']);
$_SESSION['desktopv2']['activity']['filters']['whole_period']		= dims_load_securvalue('whole_period_val', dims_const::_DIMS_NUM_INPUT, false, true, true, $_SESSION['desktopv2']['activity']['filters']['whole_period']);
$_SESSION['desktopv2']['activity']['filters']['nb_jours_avt_aps']	= dims_load_securvalue('nb_jours_avt_aps', dims_const::_DIMS_NUM_INPUT, false, true, true, $_SESSION['desktopv2']['activity']['filters']['nb_jours_avt_aps']);

// valeur par défaut
if ($_SESSION['desktopv2']['activity']['filters']['nb_jours_avt_aps'] == 0) {
	$_SESSION['desktopv2']['activity']['filters']['nb_jours_avt_aps'] = 15;
}

// dims_print_r($_POST);
// dims_print_r(date('d/m/Y', $_SESSION['desktopv2']['activity']['filters']['slider_date']));
// dims_print_r(date('d/m/Y', $_SESSION['desktopv2']['activity']['filters']['slider_date'] - $_SESSION['desktopv2']['activity']['filters']['nb_jours_avt_aps']*86400));
// dims_print_r(date('d/m/Y', $_SESSION['desktopv2']['activity']['filters']['slider_date'] + $_SESSION['desktopv2']['activity']['filters']['nb_jours_avt_aps']*86400));
?>

<div>
	<div class="title_activities">
		<img src="<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/manage_activities.png"/>
		<h2><?php echo $_SESSION['cste']['_SYSTEM_MANAGE_EVENTS']; ?></h2>
	</div>
	<div>
		<form id="filter_form" name="filter_form" action="<?php echo $dims->getScriptEnv(); ?>" method="post">
			<?
				// Sécurisation du formulaire par token
				require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
				$token = new FormToken\TokenField;
				$token->field("submenu",	_DESKTOP_V2_DESKTOP);
				$token->field("mode",		"activity");
				$token->field("action",		"manage");
				$token->field("slider_date");
				$token->field("whole_period_val");
				$token->field("status");
				$token->field("responsible");
				$token->field("type");
				$token->field("keywords");
				$token->field("contact");
				$token->field("opportunity");
				$token->field("nb_jours_avt_aps");
				$tokenHTML = $token->generate();
				echo $tokenHTML;
			?>
			<input type="hidden" name="submenu" value="<?php echo _DESKTOP_V2_DESKTOP; ?>" />
			<input type="hidden" name="mode" value="activity" />
			<input type="hidden" name="action" value="manage" />
			<input id="slider_date" type="hidden" name="slider_date" value="" />
			<input id="whole_period_val" type="hidden" name="whole_period_val" value="0" />

			<div class="filters">
				<table>
					<tr>
						<td>
							<label class="status" for="status" ><?php echo $_SESSION['cste']['STATUS']; ?></label>
							<select id="status" name="status">
								<option value="-1">Toutes</option>
								<option value="<?php echo dims_activity::STATUS_TO_COME; ?>" <?php if ($_SESSION['desktopv2']['activity']['filters']['status'] == dims_activity::STATUS_TO_COME) echo 'selected="selected"'; ?>>À venir</option>
								<option value="<?php echo dims_activity::STATUS_PASSED; ?>" <?php if ($_SESSION['desktopv2']['activity']['filters']['status'] == dims_activity::STATUS_PASSED) echo 'selected="selected"'; ?>>Passées</option>
								<option value="<?php echo dims_activity::STATUS_CLOSED; ?>" <?php if ($_SESSION['desktopv2']['activity']['filters']['status'] == dims_activity::STATUS_CLOSED) echo 'selected="selected"'; ?>>Fermées</option>
							</select>
						</td>
						<td>
							<label class="status" for="responsible"><?php echo $_SESSION['cste']['_DIMS_LABEL_RESPONSIBLE']; ?></label>
							<select id="responsible" name="responsible">
								<option value="0">Tous</option>
								<?php
								$sel = ($_SESSION['desktopv2']['activity']['filters']['responsible'] == $_SESSION['dims']['userid']) ? ' selected="selected"' : '';
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
									$sel = ($row['id'] == $_SESSION['desktopv2']['activity']['filters']['responsible']) ? ' selected="selected"' : '';
									echo '<option value="'.$row['id'].'"'.$sel.'>'.$row['firstname'].' '.$row['lastname'].'</option>';
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<label class="status" for="type"><?php echo $_SESSION['cste']['_TYPE']; ?></label>
							<select id="type" name="type">
								<option value="-1"><?php echo $_SESSION['cste']['_DIMS_ALLS']; ?></option>
								<?php
								foreach(activity_type::getAllTypes() as $type) {
									?>
									<option value="<?php echo $type->getId(); ?>" <?php if ($_SESSION['desktopv2']['activity']['filters']['type'] == $type->getId()) echo 'selected="selected"'; ?>><?php echo $type->fields['label']; ?></option>
									<?php
								}
								?>
							</select>
						</td>
						<td>
							<label class="status" for="keywords"><?php echo ucfirst($_SESSION['cste']['_DIMS_LABEL_KEYWORDS']); ?></label>
							<input type="text" name="keywords" value="<?php echo htmlspecialchars($_SESSION['desktopv2']['activity']['filters']['keywords']); ?>" />
						</td>
					</tr>
					<tr>
						<td>
							<label class="status" for="contact"><?php echo $_SESSION['cste']['_DIMS_LABEL_CONTACT']; ?></label>
							<select id="contact" name="contact">
								<option value=""><?php echo $_SESSION['cste']['_DIMS_ALLS']; ?></option>
								<?php
									$query = 	"SELECT distinct(c.id), c.firstname, c.lastname
												FROM dims_mod_business_action_detail as d
												LEFT JOIN dims_mod_business_action as a
												ON a.id=d.action_id
												LEFT JOIN dims_mod_business_contact as c
												ON d.contact_id=c.id
												WHERE a.typeaction= :typeaction
												AND a.id_user= :iduser AND a.id_workspace= :idworkspace";
									$res = $this->db->query($query, array(
										':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
										':typeaction' => array('type' => PDO::PARAM_INT, 'value' => dims_activity::TYPE_ACTION),
										':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
									));

									while($row = $this->db->fetchrow($res)) {
									?>
										<option value="<?php echo $row['id'] ?>"><?php echo $row['firstname']." ".$row['lastname']; ?></option>
									<?php
									}
								?>
							</select>
						</td>
						<td>
							<?php
							if (!defined('_ACTIVE_OPPORTUNITY') || _ACTIVE_OPPORTUNITY) {
								?>
								<label class="status" for="opportunity"><?php echo ucfirst($_SESSION['cste']['OPPORTUNITY']); ?></label>
								<select id="opportunity" name="opportunity">
									<option value=""><?php echo $_SESSION['cste']['_DIMS_ALLS']; ?></option>
									<?php
									$query = 	"SELECT id, libelle
												FROM dims_mod_business_action
												WHERE typeaction = :typeaction AND id_workspace = :idworkspace";
									$res = $db->query($query, array(
										':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
										':typeaction' => array('type' => PDO::PARAM_INT, 'value' => dims_lead::TYPE_ACTION),
									));
									while ($row = $db->fetchrow($res)) {
										$sel = ($row['id'] == $_SESSION['desktopv2']['activity']['filters']['opportunity']) ? ' selected="selected"' : '';
										echo '<option value="'.$row['id'].'"'.$sel.'>'.stripslashes($row['libelle']).'</option>';
									}
									?>
								</select>
								<?php
							}
							?>
						</td>
					</tr>
				</table>
				<div class="sub_form">
					<div class="form_buttons_float_right">
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
			<div class="time_filters">
				<div id="slider"></div>
				<div id="date"></div>
				<div>
					<a id="today" href="javascript:void(0);"><?php echo ucfirst($_SESSION['cste']['_DIMS_LABEL_DAY']); ?></a>
					|
					<a id="whole_period" href="javascript:void(0);"><?php echo ucfirst($_SESSION['cste']['TOUTE_LA_PERIODE']); ?></a>
				</div>
				<div class="nb_days"><?php echo $_SESSION['cste']['NB_DAYS_BEFORE_AFTER']; ?> :
					<select name="nb_jours_avt_aps" onchange="javascript:$('#filter_form').submit();">
						<option value="15"<?php if ($_SESSION['desktopv2']['activity']['filters']['nb_jours_avt_aps'] == 15) echo ' selected'; ?>>15</option>
						<option value="30"<?php if ($_SESSION['desktopv2']['activity']['filters']['nb_jours_avt_aps'] == 30) echo ' selected'; ?>>30</option>
						<option value="60"<?php if ($_SESSION['desktopv2']['activity']['filters']['nb_jours_avt_aps'] == 60) echo ' selected'; ?>>60</option>
						<option value="90"<?php if ($_SESSION['desktopv2']['activity']['filters']['nb_jours_avt_aps'] == 90) echo ' selected'; ?>>90</option>
					</select>
				</div>
			</div>
		</form>

		<div class="activities_table">
			<?php
			$tabl = array();
			$tabl['headers'][] = '';
			$tabl['headers'][] = $_SESSION['cste']['_TYPE'];
			$tabl['headers'][] = $_SESSION['cste']['DATES'];
			$tabl['headers'][] = $_SESSION['cste']['_DIMS_LABEL_LABEL'];
			$tabl['headers'][] = $_SESSION['cste']['CONTACT(S)'];
			if (!defined('_ACTIVE_OPPORTUNITY') || _ACTIVE_OPPORTUNITY) {
				$tabl['headers'][] = ucfirst($_SESSION['cste']['OPPORTUNITY']);
			}
			$tabl['headers'][] = $_SESSION['cste']['_DIMS_ACTIONS'];
			$tabl['data']['aasorting']['num'] = 2;
			$tabl['data']['aasorting']['order'] = 'desc';
			$tabl['data']['bSortable'][6] = false;
			$tabl['data']['classes'][0] = 'w20p';
			$tabl['data']['classes'][1] = 'w20p';
			$tabl['data']['classes'][6] = 'w20p';
			$skin = skin_common::getInstance();
			echo $skin->displayArray($tabl, 'activities', 'admin.php?dims_op=desktopv2&action=load_activities');
			?>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		var changed = $();

		var updateDate = function() {
			$("#date").text(new Date($("#slider").slider("value")*1000).toLocaleDateString());
		};

		$("#filter_form div.filters table td select").change(function() {
			changed.add($(this));
		});

		// $("#filter_form").submit(function(e) {
		//	e.preventDefault();
		//	var table = $("#activities").dataTable();

		//	changed.each(function() {
		//		table.fnFilter($(this).val());
		//	});
		// });

		$("#slider").click(function() {
			$(this).slider("enable");
		});

		$( "#slider" ).slider({
			value: <?php echo $_SESSION['desktopv2']['activity']['filters']['slider_date']; ?>,
			min: <?php echo $oldest; ?>,
			max: <?php echo $newest; ?>,
			slide: function(event, ui) {
				updateDate();
			},
			stop: function(event, ui) {
				$("#slider_date").val($('#slider').slider('value'));
				$("#filter_form").submit();
			}
		});

		updateDate();

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
				$("#slider_date").val($('#slider').slider('value'));
				$("#filter_form").submit();
			}
		});

		$('#whole_period').click(function() {
			$("#slider").slider("disable");
			$("#whole_period_val").val(1);
			$("#filter_form").submit();
		});
	});

	// plugin chosen sur les listes déroulantes
	$('#status').chosen({no_results_text: "No results matched"});
	$('#responsible').chosen({no_results_text: "No results matched"});
	$('#type').chosen({no_results_text: "No results matched"});
	$('#opportunity').chosen({no_results_text: "No results matched"});
	$('#contact').chosen({no_results_text: "No results matched"});
</script>
