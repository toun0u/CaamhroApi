<?php

require_once DIMS_APP_PATH.'modules/system/class_country.php';
require_once DIMS_APP_PATH."modules/system/activity/class_type.php";

$dims = dims::getInstance();
$db = $dims->getDb();

if ($this->new) {
	$date_from = '';
	$hour_from = '08';
	$mins_from = '00';

	$date_to = '';
	$hour_to = '18';
	$mins_to = '00';

    if ($this->fields['datejour'] != '0000-00-00') {
        $a_df = explode('-', $this->fields['datejour']);
        $date_from = $a_df[2].'/'.$a_df[1].'/'.$a_df[0];
    }
    else {
        $date_from = '';
    }
    $a_hf = explode(':', $this->fields['heuredeb']);


    if ($this->fields['datefin'] != '0000-00-00') {
        $a_dt = explode('-', $this->fields['datefin']);
        $date_to = $a_dt[2].'/'.$a_dt[1].'/'.$a_dt[0];
    }
    else {
        $date_to = '';
    }
	// First type is default type
	$resultacts=activity_type::getAllTypes();
	if (!empty($resultacts) && is_array($resultacts)) {
		$default_type = current($resultacts);

		$type_id = $default_type->getId();
	}
		$day = dims_load_securvalue('day',dims_const::_DIMS_CHAR_INPUT,true,false,false);

		if ($day!='') {
			$elem=explode('-',$day);
			$date_from=$elem[2].'/'.$elem[1].'/'.$elem[0];
			$date_to=$elem[2].'/'.$elem[1].'/'.$elem[0];
		}
}
else {
	if ($this->fields['datejour'] != '0000-00-00') {
		$a_df = explode('-', $this->fields['datejour']);
		$date_from = $a_df[2].'/'.$a_df[1].'/'.$a_df[0];
	}
	else {
		$date_from = '';
	}
	$a_hf = explode(':', $this->fields['heuredeb']);
	$hour_from = $a_hf[0];
	$mins_from = $a_hf[1];

	if ($this->fields['datefin'] != '0000-00-00') {
		$a_dt = explode('-', $this->fields['datefin']);
		$date_to = $a_dt[2].'/'.$a_dt[1].'/'.$a_dt[0];
	}
	else {
		$date_to = '';
	}
	$a_ht = explode(':', $this->fields['heurefin']);
	$hour_to = $a_ht[0];
	$mins_to = $a_ht[1];

	$type_id = $this->fields['activity_type_id'];
}
?>

<div class="title_new_activity">
	<h1><?php echo $_SESSION['cste']['_SYSTEM_MANAGE_EVENTS']; ?></h1>
</div>

<h2><?php echo $_SESSION['cste']['NEW_EVENT']; ?></h2>

<div class="form_activity">
	<form name="f_activity" id="f_activity" action="<?php echo dims::getInstance()->getScriptEnv(); ?>" method="post" enctype="multipart/form-data">
		<?
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("redirection",	"0");
			$token->field("action",			"save");
			$token->field("mode",			"activity");
			$token->field("submenu",			"1");
			$token->field("activity_id",	$this->fields['id']);
			$token->field("activity_type_id");
			$token->field("activity_private", "1");
			$token->field("activity_responsable");
			$token->field("activity_date_from");
			$token->field("activity_hour_from");
			$token->field("activity_mins_from");
			$token->field("activity_date_to");
			$token->field("activity_hour_to");
			$token->field("activity_mins_to");
			$token->field("activity_label");
			$token->field("activity_description");
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>
		<input type="hidden" id="redirection" name="redirection" value="0" />
		<input type="hidden" name="action" value="save" />
		<input type="hidden" name="mode" value="activity" />
		<input type="hidden" name="submenu" value="1" />
		<input type="hidden" name="activity_id" value="<?php echo $this->fields['id']; ?>" />

		<table class="w100 bb1">
			<tr>
				<td><h3><?= $_SESSION['cste']['_DIMS_LABEL_DESCRIPTION']; ?></h3></td>
				<td class="txtright">
					<img class="clickable" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/replier_menu.png" alt="Replier le bloc" onclick="javascript:$('#activity_general').slideToggle('fast',flip_flop($('div.zone_functions'),$(this),'<?php echo _DESKTOP_TPL_PATH; ?>'));" />
				</td>
			</tr>
		</table>
		<div id="activity_general">
			<fieldset>
				<table class="w100">
					<tr>
						<td class="vatop">
							<table>
								<tr>
									<td><label class="title">Type</label></td>
									<td>
										<select class="w100" id="activity_type_id" name="activity_type_id" data-placeholder="Sélectionnez un type d'activité">
											<option value="-1"><?php echo $_SESSION['cste']['_DIMS_ALLS']; ?></option>
											<?php
											foreach(activity_type::getAllTypes() as $type) {
												?>
												<option value="<?php echo $type->getId(); ?>" <?php if ($type_id == $type->getId()) echo 'selected="selected"'; ?>><?php echo $type->fields['label']; ?></option>
												<?php
											}
											?>
										</select>
									</td>
								</tr>
								<tr>
									<td><label class="title" for="activity_responsable">Responsable</label></td>
									<td>
										<select class="w100" id="activity_responsable" name="activity_responsable" data-placeholder="Sélectionnez un responsable">
											<?php
											$sel = (!$this->fields['id_responsible'] || $this->fields['id_responsible'] == $_SESSION['dims']['userid']) ? ' selected="selected"' : '';
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
												$sel = ($row['id'] == $this->fields['id_responsible']) ? ' selected="selected"' : '';
												echo '<option value="'.$row['id'].'"'.$sel.'>'.$row['firstname'].' '.$row['lastname'].'</option>';
											}
											?>
										</select>
									</td>
								</tr>
								<tr>
									<td><label class="title" for="activity_date_from">Date du</label></td>
									<td>
										<input type="text" id="activity_date_from" name="activity_date_from" value="<?php echo $date_from; ?>" />
										<img style="vertical-align:middle" src="./common/img/calendar.png" alt="Date de début" onclick="javascript:dims_calendar_open('activity_date_from', event);" /> à
										<input type="text" name="activity_hour_from" value="<?php echo $hour_from; ?>" class="w20p txtcenter" /> :
										<input type="text" name="activity_mins_from" value="<?php echo $mins_from; ?>" class="w20p txtcenter" />
									</td>
								</tr>
								<tr>
									<td><label class="title" for="activity_date_to">au</label></td>
									<td>
										<input type="text" id="activity_date_to" name="activity_date_to" value="<?php echo $date_to; ?>" />
										<img style="vertical-align:middle" src="./common/img/calendar.png" alt="Date de fin" onclick="javascript:dims_calendar_open('activity_date_to', event);" /> à
										<input type="text" name="activity_hour_to" value="<?php echo $hour_to; ?>" class="w20p txtcenter" /> :
										<input type="text" name="activity_mins_to" value="<?php echo $mins_to; ?>" class="w20p txtcenter" />
									</td>
								</tr>
								<tr>
									<td><label class="title" for="activity_private"><?= $_SESSION['cste']['PUBLISHED']; ?></label></td>
									<td>
										<input type="checkbox" name="activity_private" id="activity_private" <?= ($this->get("private"))?'checked=true':""; ?> value="1" />
									</td>
								</tr>
							</table>
						</td>
						<td class="vatop">
							<table><tr>
									<td class="vatop"><label class="title" for="activity_label">Libellé</label></td>
									<td><input type="text" style="width:292px;" id="activity_label" name="activity_label" value="<?php echo stripslashes($this->fields['libelle']); ?>" /></td>
								</tr>
								<tr>
									<td class="vatop"><label class="title" for="activity_description">Complément</label></td>
									<td><textarea id="activity_description" name="activity_description"><?php echo $this->getDescriptionRaw(); ?></textarea></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</fieldset>
		</div>

		<table class="w100 bb1">
			<tr>
				<td><h3><?= $_SESSION['cste']['_DIMS_PARTICIP']; ?></h3></td>
				<td class="txtright">
					<img class="clickable" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/replier_menu.png" alt="Replier le bloc" onclick="javascript:$('#activity_search_contact').slideToggle('fast',flip_flop($('div.zone_functions'),$(this),'<?php echo _DESKTOP_TPL_PATH; ?>'));" />
				</td>
			</tr>
		</table>
		<?php
		$this->display(_DESKTOP_TPL_LOCAL_PATH.'/activity/edit_activity_contacts.tpl.php');
		if (!defined('_ACTIVE_OPPORTUNITY') || _ACTIVE_OPPORTUNITY) {
			?>
			<table class="w100 bb1">
				<tr>
					<td><h3>Opportunités liées</h3></td>
					<td class="txtright">
						<img class="clickable" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/replier_menu.png" alt="Replier le bloc" onclick="javascript:$('#activity_search_opportunity').slideToggle('fast',flip_flop($('div.zone_functions'),$(this),'<?php echo _DESKTOP_TPL_PATH; ?>'));" />
					</td>
				</tr>
			</table>
			<div id="activity_search_opportunity">
				<fieldset>
					<table class="w100">
						<tr>
							<td class="w200p vatop">
								<input class="w150 search-field" type="text" id="opportunitySearch" name="opportunitySearch" placeholder="Recherchez une opportunité" />
								<a href="javascript:void(0);" onclick="javascript:activitySearchOpportunity($('#opportunitySearch').val(), '<?php echo _DESKTOP_TPL_PATH; ?>');" title="Lancer la recherche"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/activity_loupe.png" alt="Recherchez une opportunité" /></a>
								<div id="searchOpportunityResults"></div>
							</td>
							<td class="vatop bdleft">
								<div id="opportunitiesList"></div>
							</td>
						</tr>
					</table>
				</fieldset>
			</div>
			<?php
		}
		?>
		<table class="w100 bb1">
			<tr>
				<td><h3><?= $_SESSION['cste']['_LOCATION']; ?></h3></td>
				<td class="txtright">
					<img class="clickable" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/deplier_menu.png" alt="Replier le bloc" onclick="javascript:$('#activity_localisation').slideToggle('fast',flip_flop($('div.zone_functions'),$(this),'<?php echo _DESKTOP_TPL_PATH; ?>'));" />
				</td>
			</tr>
		</table>
		<div id="activity_localisation" class="desktop-hidden">
			<fieldset>
				<table class="w100">
					<tr>
						<td class="w50p">
							<label class="title" for="activity_address"><?= $_SESSION['cste']['_DIMS_LABEL_ADDRESS']; ?></label>
						</td>
						<td colspan="5">
							<input class="w100" type="text" id="activity_address" name="activity_address" value="<?php echo $this->fields['address']; ?>" />
						</td>
					</tr>
					<tr>
						<td>
							<label class="title" for="activity_cp">CP</label>
						</td>
						<td>
							<input class="w90" type="text" id="activity_cp" name="activity_cp" value="<?php echo $this->fields['cp']; ?>" />
						</td>
						<td>
							<label class="title" for="activity_city_id"><?= $_SESSION['cste']['_DIMS_LABEL_CITY']; ?></label>
						</td>
						<td id="activity_rech_add_city">
							<?php
							$sel_Country = null;
							if(isset($this->fields['id_country']) && $this->fields['id_country'] != '' && $this->fields['id_country'] > 0){
								$sel_Country = $this->fields['id_country'];
							}else
								$sel_Country = _DIMS_DEFAULT_COUNTRY;
							?>
							<select id="activity_city_id" type="text" name="activity_city_id" <?php echo ($sel_Country != null && $sel_Country > 0) ? '' : 'disabled="disabled"'; ?> style="width: 200px;" data-placeholder="<?php echo $_DIMS['cste']['_DIMS_SELECT_CITY']; ?>">
								<option value=""></option>
								<?php
								if ($sel_Country != null && $sel_Country > 0){
									$city = city::find_by(array('id'=>$this->get('id_city')),null,1);
									if(!empty($city)){
										echo '<option value="'.$city->get('id').'" selected=true>'.$city->get('label').'</option>';
									}
								}
								?>
							</select>
						</td>
						<td>
							<label class="title" for="activity_country_id"><?= $_SESSION['cste']['_DIMS_LABEL_COUNTRY']; ?></label>
						</td>
						<td>
							<select name="activity_country_id" id="activity_country_id" style="width: 200px;" data-placeholder="<?php echo $_DIMS['cste']['_DIMS_SELECT_COUNTRY']; ?>">
								<option value=""></option>
								<?php
								$a_countries = country::getAllCountries();
								$sel_Country = null;
								if (sizeof($a_countries)) {
									foreach ($a_countries as $country) {
										$sel = '';
										if (isset($this->fields['id_country']) && $country->fields['id'] == $this->fields['id_country']){
											$sel = "selected=true";
											$sel_Country = $country;
										} elseif ($country->fields['id'] == _DIMS_DEFAULT_COUNTRY){
											$sel = "selected=true";
											$sel_Country = $country;
										}
										echo '<option value="'.$country->fields['id'].'"'.$sel.'>'.stripslashes($country->fields['printable_name']).'</option>';
									}
								}
								?>
							</select>
						</td>
					</tr>
				</table>
			</fieldset>
		</div>

		<table class="w100 bb1">
			<tr>
				<td><h3>Documents associés</h3></td>
				<td class="txtright">
					<img class="clickable" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/deplier_menu.png" alt="Replier le bloc" onclick="javascript:$('#activity_documents').slideToggle('fast',flip_flop($('div.zone_functions'),$(this),'<?php echo _DESKTOP_TPL_PATH; ?>'));" />
				</td>
			</tr>
		</table>
		<div id="activity_documents" class="desktop-hidden">
			<fieldset>
				<table class="w100">
					<tr>
						<td class="w200p vatop">
							<input class="w150 search-field" type="text" id="documentSearch" name="documentSearch" placeholder="Recherchez un document" />
							<a href="javascript:void(0);" onclick="javascript:activitySearchDocument($('#documentSearch').val(), '<?php echo _DESKTOP_TPL_PATH; ?>');" title="Lancer la recherche"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/activity_loupe.png" alt="Recherchez un document" /></a>
							<div id="searchDocumentResults"></div>
						</td>
						<td class="vatop bdleft">
							<div id="documentsList"></div>
							<p>Vous n'avez pas trouvé le document recherché - <a href="javascript:void(0);" onclick="javascript:addDocUploadField('<?php echo _DESKTOP_TPL_PATH; ?>');">ajoutez-le</a></p>
						</td>
					</tr>
				</table>
			</fieldset>
		</div>

		<?php
		// chargement des alertes (1 seule par activité)
		$a_alerts = array();
		if ($this->fields['id_globalobject'] != '') {
			$a_alerts = dims_alert::getAllByGOOrigin($this->fields['id_globalobject']);
		}

		if (sizeof($a_alerts)) {
			$alert = $a_alerts[0];
			if ($alert->fields['mode'] == dims_alert::MODE_RELATIVE) {
				$nb_period = $alert->fields['nb_period'];
				$period = $alert->fields['period'];
				$date_alert = '';
				$hour_alert = '';
				$mins_alert = '';
			}
			else {
				$nb_period = '';
				$period = '';
				$a_da = dims_timestamp2local($alert->fields['timestp_alert']);
				$date_alert = $a_da['date'];
				$hour_alert = substr($a_da['time'], 0, 2);
				$mins_alert = substr($a_da['time'], 3, 2);
			}
		}
		else {
			$alert = new dims_alert();
			$alert->init_description();
			$nb_period = '';
			$period = '';
			$date_alert = '';
			$hour_alert = '';
			$mins_alert = '';
		}
		?>
		<table class="w100 bb1">
			<tr>
				<td><h3>Programmer une alerte email interne</h3></td>
				<td class="txtright">
					<img class="clickable" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/deplier_menu.png" alt="Replier le bloc" onclick="javascript:$('#activity_email_alert').slideToggle('fast',flip_flop($('div.zone_functions'),$(this),'<?php echo _DESKTOP_TPL_PATH; ?>'));" />
				</td>
			</tr>
		</table>
		<div id="activity_email_alert" class="desktop-hidden">
			<fieldset>
				<table>
					<tr>
						<td><input type="radio" name="activity_alert_mode" value="0" <?php if (!$alert->fields['mode']) { echo 'checked="checked"'; } ?> /></td>
						<td><label class="title" for="activity_alert_nb_period">Aucune</label></td>
					</tr>
					<tr>
						<td><input type="radio" name="activity_alert_mode" value="1" <?php if ($alert->fields['mode'] == dims_alert::MODE_RELATIVE) { echo 'checked="checked"'; } ?> /></td>
						<td>
							<label class="title" for="activity_alert_nb_period">Alerte</label>
						</td>
						<td>
							<input class="w20p" type="text" id="activity_alert_nb_period" name="activity_alert_nb_period" value="<?php echo $nb_period; ?>" />
							<select name="activity_alert_period">
								<option value="i" <?php if ($period == 'i') { echo 'selected="selected"'; } ?>>Minute(s) avant</option>
								<option value="H" <?php if ($period == 'H') { echo 'selected="selected"'; } ?>>Heure(s) avant</option>
								<option value="d" <?php if ($period == 'd') { echo 'selected="selected"'; } ?>>Jour(s) avant</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><input type="radio" name="activity_alert_mode" value="2" <?php if ($alert->fields['mode'] == dims_alert::MODE_ABSOLUTE) { echo 'checked="checked"'; } ?> /></td>
						<td>
							<label class="title" for="activity_alert_date">Le</label>
						</td>
						<td>
							<input type="text" id="activity_alert_date" name="activity_alert_date" value="<?php echo $date_alert; ?>" />
							<img style="vertical-align: bottom;" class="clickable" src="./common/img/calendar.png" alt="Date de début" onclick="javascript:dims_calendar_open('activity_alert_date', event);" /> à
							<input type="text" name="activity_alert_hour" value="<?php echo $hour_alert; ?>" class="w20p txtcenter" /> :
							<input type="text" name="activity_alert_mins" value="<?php echo $mins_alert; ?>" class="w20p txtcenter" />
						</td>
					</tr>
				</table>
			</fieldset>
		</div>

		<p class="mt2 txtright">
			<input type="button" value="Enregistrer l'activité" onclick="javascript:$('#redirection').val(0);document.f_activity.submit();" />
		<span> <?php echo $_SESSION['cste']['_DIMS_OR']; ?> </span>
			<input type="button" value="Enregistrer l'activité et continuer" onclick="javascript:$('#redirection').val(1);document.f_activity.submit();" />
		<span> <?php echo $_SESSION['cste']['_DIMS_OR']; ?> </span>
		<a href="javascript:void(0)" onclick="javascript:history.go(-1)">Annuler</a>
		</p>

	</form>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		<?php
		if ($this->fields['id_globalobject']) {
			?>
			// recherche des objets liés
			$.ajax({
				type: 'GET',
				url: 'admin.php',
				data: {
					'dims_op' : 'desktopv2',
					'action' : 'activity_get_linked_objects',
					'activity_id_go' : <?php echo $this->fields['id_globalobject']; ?>
				},
				dataType: 'json',
				async: false,
				success: function(data) {
					// contacts
					if (data.contacts.length) {
						for (i = 0; i < data.contacts.length; i++) {
							$('#contactsList').append(
								'<table id="added_ct_' + data.contacts[i].c.id_globalobject + '" class="w100 bb1"><tr>' +
								'<td class="w20p txtcenter"><img src="' + data.contacts[i].c.photoPath + '" alt="' + data.contacts[i].c.lastname + ' ' + data.contacts[i].c.firstname + '" title="' + data.contacts[i].c.lastname + ' ' + data.contacts[i].c.firstname + '" /></td>' +
								'<td>' + data.contacts[i].c.lastname + ' ' + data.contacts[i].c.firstname + '<br/><em>' + data.contacts[i].t.intitule + '</em></td>' +
								'<td class="w20p txtcenter"><a href="javascript:void(0);" onclick="javascript:activityRemoveContact(' + data.contacts[i].c.id_globalobject + ');" title="Enlever ce contact"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/supprimer20.png" /></a></td></tr></table>');
						}
					}
					// opportunités
					if (data.opportunities.length) {
						for (i = 0; i < data.opportunities.length; i++) {
							$('#opportunitiesList').append(
								'<table id="added_opp_' + data.opportunities[i].id_globalobject + '" class="w100 bb1"><tr>' +
								'<td>' + data.opportunities[i].libelle + '</td>' +
								'<td class="w20p txtcenter"><a href="javascript:void(0);" onclick="javascript:activityRemoveOpportunity(' + data.opportunities[i].id_globalobject + ');" title="Enlever cette opportunité"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/supprimer20.png" /></a></td></tr></table>');
						}
					}
					// documents
					if (data.docs.length) {
						for (i = 0; i < data.docs.length; i++) {
							$('#documentsList').append(
								'<table id="added_doc_' + data.docs[i].id_globalobject + '" class="w100 bb1"><tr>' +
								'<td class="w20p txtcenter"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/doc32.png" alt="' + data.docs[i].name + '" title="' + data.docs[i].name + '" /></td>' +
								'<td>' + data.docs[i].name + '</td>' +
								'<td class="w20p txtcenter"><a href="javascript:void(0);" onclick="javascript:preview_docfile(\'' + data.docs[i].md5id + '\');" title="Prévisualiser ce document"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/previsu.png" /></a></td>' +
								'<td class="w20p txtcenter"><a href="javascript:void(0);" onclick="javascript:activityRemoveDocument(' + data.docs[i].id_globalobject + ');" title="Enlever ce document"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/supprimer20.png" /></a></td></tr></table>');
						}
					}
				}
			});
			<?php
		}
		?>

		// plugin chosen sur les listes déroulantes
		$('#activity_type_id').chosen({no_results_text: "No results matched"});
		$('#activity_responsable').chosen({no_results_text: "No results matched"});
		$('#activity_opportunity').chosen({no_results_text: "No results matched"});

		var prevV = '<?= $this->get('cp'); ?>';
		$('input#activity_cp').focusout(function(){
			var v = jQuery.trim($(this).val());
			if(v.length >= 5 && v != prevV){
				$.ajax({
					url: '<?= dims::getInstance()->getScriptEnv(); ?>',
					type: "POST",
					data: {
						'dims_op': 'desktopv2',
						'action': 'searchCity',
						'val': v,
						'id_country': $('select#activity_country_id').val(),
					},
					dataType: 'html',
					success: function(data) {
						$('select#activity_city_id').html(data).trigger("liszt:updated");
					},
				});
				prevV = v;
			}
		});

		var tempo = null;
		if(window['refreshVille'] == undefined){
			window['refreshVille'] = function refreshVille(elem,id_country){
				tmp = $('div.chzn-search input',$(elem).parent('td:first')).val();
				if(jQuery.trim(tmp) != ''){
					$.ajax({
						url: '<?= dims::getInstance()->getScriptEnv(); ?>',
						type: "POST",
						data: {
							'dims_op': 'desktopv2',
							'action': 'searchCity',
							'val': tmp,
							'id_country': id_country,
						},
						dataType: 'html',
						success: function(data) {
							$(elem).html(data).trigger("liszt:updated");
							$('div.chzn-search input',$(elem).parent('td:first')).focus().val(tmp);
						},
					});
				}
				clearInterval(tempo);
				tempo = null;
			}
		}

		// pays et ville de l'activité
		$("select#activity_city_id").chosen()
		.parent().append('<img src="<?= _DESKTOP_TPL_PATH.'/gfx/common/add.png'; ?>" style="cursor:pointer;" class="add-city-address" />')
		.ready(function(){
			var idCountry = $('form#f_activity select#activity_country_id').val();
			$('div.chzn-search input:first',$('form#f_activity select#activity_city_id').parent('td:first')).keyup(function(event){
				idCountry = $('form#f_activity select#activity_country_id').val();
				if(event.keyCode != null){
					if (event.keyCode != 16 && event.keyCode != 38 && event.keyCode != 40 && event.keyCode != 39 &&
						event.keyCode != 37 && event.keyCode != 20 && event.keyCode != 17 && event.keyCode != 18 &&
						event.keyCode != 13){
						if ($(this).val().length >= 2){
							if (tempo != null)
								clearInterval(tempo);
							tempo = setInterval("refreshVille('form#f_activity select#activity_city_id',"+idCountry+")",1200);
						}
					}/*else if(event.keyCode == 13){
						if (tempo != null)
							clearInterval(tempo);
						tempo = null
						refreshVille('form#f_activity select#activity_city_id',idCountry);
					}*/
				}
			});
		});
		$("form#f_activity").delegate('img.add-city-address','click',function(){
			var td = $(this).parent();
			$(this).replaceWith('<input type="text" style="width:175px;" class="adr-add-city" /><img style="cursor:pointer;" src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/valid.png" class="city-address-valid" /><img style="cursor:pointer;" src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/icon_suppresion.png" class="city-address-undo" />');
			$('input',td).focus();
		});
		$("form#f_activity").delegate('img.city-address-valid','click',function(){
			var td = $(this).parent(),
				country = $("form#f_activity select#activity_country_id").val(),
				value = $('input:last',$(this).parent()).val();
			$.ajax({
				type: "POST",
				url: '<?= dims::getInstance()->getScriptEnv(); ?>',
				data: {
					dims_op: 'desktopv2',
					action: 'add_new_city_addr',
					val: value,
					id_country: country,
				},
				async: false,
				dataType: "json",
				success: function(data){
					var options = "";
					for(var i=0; i<data.length; i++){
						if(data[i]['selected'])
							options = options+'<option value="'+data[i]['id']+'" selected=true>'+data[i]['label']+'</option>';
						else
							options = options+'<option value="'+data[i]['id']+'">'+data[i]['label']+'</option>';
					}
					$('select#activity_city_id',td).html(options).trigger("liszt:updated");
					$('img.city-address-valid',td).remove();
					$('input:last',td).remove();
					td.append('<img style="cursor:pointer;" src="<?= _DESKTOP_TPL_PATH.'/gfx/common/add.png'; ?>" class="add-city-address" />');
					$('img.city-address-undo',td).remove();

					$('select#activity_city_id').each(function(){
						if($(this).parent() != td && $('select#activity_country_id', $(this).parents('form:first')).val() == country){
							var val = $(this).val(),
								options = "";
							for(var i=0; i<data.length; i++){
								if(data[i]['id'] == val)
									options = options+'<option value="'+data[i]['id']+'" selected=true>'+data[i]['label']+'</option>';
								else
									options = options+'<option value="'+data[i]['id']+'">'+data[i]['label']+'</option>';
							}
							$(this).html(options).trigger("liszt:updated");
						}
					});
				}
			});
		}).delegate('img.city-address-undo','click',function(){
			$('img.city-address-valid',$(this).parent()).remove();
			$('input:last',$(this).parent()).remove();
			$(this).parent().append('<img style="cursor:pointer;" src="<?= _DESKTOP_TPL_PATH.'/gfx/common/add.png'; ?>" class="add-city-address" />');
			$(this).remove();
		}).delegate('input.adr-add-city','keydown',function(event){
			var keycode = event.keyCode;
			if(keycode == 13){ // enter
				event.preventDefault();
			}
		}).delegate('input.adr-add-city','keyup',function(event){
			var keycode = event.keyCode;
			if(keycode == 13){ // enter
				event.preventDefault();
				$("form#f_activity img.city-address-valid").trigger('click');
			}
		});
		$("form#f_activity select#activity_city_id").change(function(){
			var option = $('option[value="'+$(this).val()+'"]',$(this));
			if(option.attr('dims-data-value') != undefined && option.attr('dims-data-value') != '' && option.attr('dims-data-value') != '0'){
				$('form#f_activity input#activity_cp').val(option.attr('dims-data-value'));
			}
		});
		$("select#activity_country_id")
			.chosen({no_results_text: "<?php echo addslashes($_SESSION['cste']['NO_RESULT']);?>"})
			.change(function(){
				if($(this).val() != '') {
					$('#activity_city_id').removeAttr('disabled');
				}
				else {
					$('#activity_city_id').attr('disabled','disabled');
				}
				$("select#activity_city_id").html('<option value=""></option>').trigger("liszt:updated");
				//refreshCityOfCountry($(this).val(),'activity_city_id');
		});

		$('div.button_add_city').live('click',function(){
			$(this).die('click');
			addNewCity('activity_rech_add_city','activity_country_id');
		});

		// pays et ville du nouveau contact
		$("select#city_id").chosen({
			allow_single_deselect:true,
			no_results_text: "<div class=\"button_add_city_user\" style=\"float:right;color:#690;cursor:pointer;\"><img style=\"float:left;\" src=\"<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/add.png\" /><div style=\"float:right;margin-top:3px;\"><?php echo addslashes($_SESSION['cste']['ADD_IT_LA']); ?></div></div><?php echo addslashes($_SESSION['cste']['NO_RESULT']);?>"
		});
		$("select#country_id")
			.chosen({no_results_text: "<?php echo addslashes($_SESSION['cste']['NO_RESULT']);?>"})
			.change(function(){
				if($(this).val() != '') {
					$('#city_id').removeAttr('disabled');
				}
				else {
					$('#city_id').attr('disabled','disabled');
				}
				refreshCityOfCountry($(this).val(),'city_id');
		});

		$('div.button_add_city_user').live('click',function(){
			$(this).die('click');
			addNewCity('activity_rech_add_city_user','country_id');
		});

		// aide a la saisie de la recherche
		$('#contactSearch').focus(function() {
			if ($('#contactSearch').val() == 'Recherchez un contact') { $('#contactSearch').val(''); }
		});
		$('#contactSearch').blur(function() {
			if ($('#contactSearch').val() == '') { $('#contactSearch').val('Recherchez un contact'); }
		});
	})
</script>
