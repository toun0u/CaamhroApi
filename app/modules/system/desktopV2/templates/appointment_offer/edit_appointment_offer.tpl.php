<?php
require_once DIMS_APP_PATH.'modules/system/class_country.php';
require_once DIMS_APP_PATH."modules/system/activity/class_type.php";

$dims = dims::getInstance();
$db = $dims->getDb();
?>
<style type="text/css">
    img.ui-datepicker-trigger{
        cursor: pointer;
    }
</style>
<div class="title_new_activity">
    <h1><?php echo $_SESSION['cste']['_ORGANISE_MEETINGS']; ?></h1>
</div>
<div class="action" style="float: right;margin-top: 3px;">
    <a href="?mode=appointment_offer&action=manage">
        <img src="<?php echo _DESKTOP_TPL_PATH ; ?>/gfx/common/icon_back.png" />
        <span>
            <?php echo $_DIMS['cste']['_DIMS_LINK_BACK_LIST']; ?>
        </span>
    </a>
</div>
<h2><?php echo $_SESSION['cste']['NEW_APPOINTMENT_OFFER']; ?></h2>

<div class="form_activity">
	<form name="f_app_offer" action="<?php echo dims::getInstance()->getScriptEnv(); ?>" method="post" enctype="multipart/form-data">
		<?
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("redirection",	"0");
			$token->field("mode",			"appointment_offer");
			$token->field("action",			"select_dates");
			$token->field("redir");
			$token->field("app_offer_id",	$this->fields['id']);
			$token->field("app_offer_label");
			$token->field("app_offer_private");
			$token->field("contactSearch");
			$token->field("contentAddContact");
			$token->field("lastname");
			$token->field("firstname");
			$token->field("phone");
			$token->field("mobile");
			$token->field("email");
			$token->field("address");
			$token->field("postalcode");
			$token->field("country_id");
			$token->field("city_id");
			$token->field("auto_add");
			$token->field("app_offer_address");
			$token->field("app_offer_cp");
			$token->field("app_offer_country_id");
			$token->field("app_offer_city_id");
			$token->field("documentSearch");
			$token->field("app_offer_alert_mode");
			$token->field("app_offer_alert_date");
			$token->field("app_offer_alert_hour");
			$token->field("app_offer_alert_mins");
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>
		<input type="hidden" id="redirection" name="redirection" value="0" />
		<input type="hidden" name="mode" value="appointment_offer" />
		<input type="hidden" name="action" value="select_dates" />
		<input type="hidden" name="redir" value="planning" />
		<input type="hidden" name="app_offer_id" value="<?php echo $this->fields['id']; ?>" />

		<table class="w100 bb1">
			<tr>
				<td><h3><? echo $_SESSION['cste']['_DIMS_LABEL_DESCRIPTION']; ?></h3></td>
				<td class="txtright">
					<img class="clickable" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/replier_menu.png" alt="Replier le bloc" onclick="javascript:$('#app_offer_general').slideToggle('fast',flip_flop($('#app_offer_general'),$(this),'<?php echo _DESKTOP_TPL_PATH; ?>'));" />
				</td>
			</tr>
		</table>
		<div id="app_offer_general">
			<fieldset>
				<table class="w100">
					<tr>
						<td class="w50p"><label class="title" for="app_offer_label"><? echo $_SESSION['cste']['_DIMS_LABEL_LABEL']; ?></label></td>
						<td><input type="text" style="width:292px;" id="app_offer_description" name="app_offer_label" value="<?php echo stripslashes($this->fields['libelle']); ?>" /></td>
					</tr>
					<tr>
						<td class="w50p"><label class="title" for="app_offer_private"><? echo $_SESSION['cste']['_PRIVATE']; ?></label></td>
						<td>
							<input type="checkbox" <? if($this->fields['private']) echo 'checked=true'; ?> id="app_offer_private" name="app_offer_private" value="1" />
						</td>
					</tr>
				</table>
			</fieldset>
		</div>

		<table class="w100 bb1">
			<tr>
				<td><h3>Participants</h3></td>
				<td class="txtright">
					<img class="clickable" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/replier_menu.png" alt="Replier le bloc" onclick="javascript:$('#app_offer_search_contact').slideToggle('fast',flip_flop($('#app_offer_search_contact'),$(this),'<?php echo _DESKTOP_TPL_PATH; ?>'));" />
				</td>
			</tr>
		</table>

		<div id="app_offer_search_contact">
			<? if ($this->isNew()){ ?>
			<fieldset>
				<table class="w100">

                    <tr>
                        <td class="w50p">
                            <label class="title" for="auto_add"><? echo $_SESSION['cste']['_I_PARTICIPE_APPOINTMENT']; ?></label>
                        </td>
                        <td colspan="2">
                            <input type="checkbox" name="auto_add" id="auto_add" value="1" />
                        </td>
                    </tr>
				</table>
			</fieldset>
			<?php
        	}
			$this->display(_DESKTOP_TPL_LOCAL_PATH.'/activity/edit_activity_contacts.tpl.php');
			?>
		</div>
		<?php
		//$this->display(_DESKTOP_TPL_LOCAL_PATH.'/activity/edit_activity_contacts.tpl.php');
		/*
		?>
		<div id="app_offer_search_contact">
			<fieldset>
				<table class="w100">
					<tr>
						<td class="w200p vatop">
							<input class="w150 search-field" type="text" onkeyup="javascript:appointmentOfferSearchContactKey($('#contactSearch').val(), '<?php echo _DESKTOP_TPL_PATH; ?>');" id="contactSearch" name="contactSearch" value="Recherchez un contact" />
							<a href="javascript:void(0);" onclick="javascript:appointmentOfferSearchContact($('#contactSearch').val(), '<?php echo _DESKTOP_TPL_PATH; ?>');" title="Lancer la recherche"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/activity_loupe.png" alt="Recherchez un contact" /></a>
							<div id="searchContactResults"></div>
						</td>
                        <td class="vatop" style="width:250px;">
                            <div id="contentAddContact" name="contentAddContact" style="display:none;visibility:hidden;">
                                <table cellspacing="10" cellpadding="0">
                                    <tbody>
                                        <tr>
                                            <td class="text" name="lastname">
                                                <? echo $_SESSION['cste']['_DIMS_LABEL_NAME']; ?> <span style="color:#DF1D31">*</span>
                                            </td>
                                            <td>
                                                <input type="text" style="width: 98%;" id="lastname" name="lastname" value=""/>
                                            </td>
                                        </tr>
                                                        <tr>
                                            <td class="text">
                                                <? echo $_SESSION['cste']['_FIRSTNAME']; ?> <span style="color:#DF1D31">*</span>
                                            </td>
                                            <td>
                                                <input type="text" style="width: 98%;" name="firstname"  value=""/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text">
                                                <? echo $_SESSION['cste']['_PHONE']; ?>
                                            </td>
                                            <td>
                                                <input type="text" class="email" name="phone" style="width: 98%;" value=""/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text">
                                                <? echo $_SESSION['cste']['_MOBILE']; ?>
                                            </td>
                                            <td>
                                                <input type="text" class="email" name="mobile" style="width: 98%;" value=""/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text">
                                                <? echo $_SESSION['cste']['_DIMS_LABEL_EMAIL']; ?> <span style="color:#DF1D31">*</span>
                                            </td>
                                            <td>
                                                <input type="text" class="email" id="email" name="email" style="width: 98%;" value=""/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text">
                                                <? echo $_SESSION['cste']['_DIMS_LABEL_ADDRESS']; ?>
                                            </td>
                                            <td>
                                                <input type="text" class="email" name="address" style="width: 98%;" value=""/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text">
                                                <? echo $_SESSION['cste']['_DIMS_LABEL_CP']; ?>
                                            </td>
                                            <td>
                                                <input type="text" class="email" name="postalcode" style="width: 98%;" value=""/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text">
                                                <? echo $_SESSION['cste']['_DIMS_LABEL_COUNTRY']; ?>
                                            </td>
                                            <td>
												<select name="country_id" id="country_id" style="width: 200px;" data-placeholder="<?php echo $_DIMS['cste']['_DIMS_SELECT_COUNTRY']; ?>">
													<option value=""></option>
													<?php
													$a_countries = country::getAllCountries();
													$sel_Country = null;
													if (sizeof($a_countries)) {
														foreach ($a_countries as $country) {
															$sel = '';
															if (stripslashes($country->fields['printable_name']) == 'France'){
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
                                        <tr>
                                            <td class="text">
                                                <? echo $_SESSION['cste']['_DIMS_LABEL_CITY']; ?>
                                            </td>
                                            <td id="app_offer_rech_add_city_user">
												<select id="city_id" type="text" name="city_id" <?php echo ($sel_Country != null && $sel_Country->fields['id'] > 0) ? '' : 'disabled="disabled"'; ?> style="width: 200px;" data-placeholder="<?php echo $_DIMS['cste']['_DIMS_SELECT_CITY']; ?>">
													<option value=""></option>
													<?php
													if ($sel_Country != null && $sel_Country->fields['id'] > 0){
														$citys = $sel_Country->getAllCity();
														foreach($citys as $city){
															echo '<option value="'.$city->fields['id'].'">'.$city->fields['label'].'</option>';
														}
													}
													?>
												</select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <div class="zone_contact_opportunity_enregistrement">
                                                    <input type="button" value="<? echo $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>" onclick="javascript:appointmentOfferAddUndoNewcontact();" />

                                                        <input onclick="javascript:appointmentOfferSaveNewContact();" type="button" value="<? echo $_SESSION['cste']['_DIMS_SAVE']; ?>" />
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
						<td class="vatop bdleft">
							<div id="contactsList"></div>
							<p>Vous n'avez pas trouvé le contact recherché - <img src="./common/img/add.gif" alt="" /> <a href="javascript:void(0);" onclick="javascript:appointmentOfferAddNewContact('<?php echo _DESKTOP_TPL_PATH; ?>');">ajoutez-le</a></p>
						</td>
					</tr>
		*/
		?>

		<?php
		// si on a de l'info dans le bloc, on l'affiche
		if ( $this->fields['address'] != '' || $this->fields['cp'] != '' || !empty($this->fields['lieu']) ) {
			$blocImg = 'replier_menu.png';
			$blocClasses = '';
		}
		else {
			$blocImg = 'deplier_menu.png';
			$blocClasses = 'desktop-hidden';
		}
		?>
		<table class="w100 bb1">
			<tr>
				<td><h3><?= $_SESSION['cste']['_LOCATION']; ?></h3></td>
				<td class="txtright">
					<img class="clickable" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/<?php echo $blocImg; ?>" alt="Replier le bloc" onclick="javascript:$('#app_offer_localisation').slideToggle('fast',flip_flop($('#app_offer_localisation'),$(this),'<?php echo _DESKTOP_TPL_PATH; ?>'));" />
				</td>
			</tr>
		</table>
		<div id="app_offer_localisation" class="<?php echo $blocClasses; ?>">
			<fieldset>
				<table class="w100">
					<tr>
						<td class="w50p">
							<label class="title" for="app_offer_address"><?= $_SESSION['cste']['_DIMS_LABEL_ADDRESS']; ?></label>
						</td>
						<td colspan="5">
							<input class="w100" type="text" id="app_offer_address" name="app_offer_address" value="<?php echo $this->fields['address']; ?>" />
						</td>
					</tr>
					<tr>
						<td>
							<label class="title" for="app_offer_cp">CP</label>
						</td>
						<td>
							<input class="w90" type="text" id="app_offer_cp" name="app_offer_cp" value="<?php echo $this->fields['cp']; ?>" />
						</td>
						<td>
							<label class="title" for="app_offer_city_id"><?= $_SESSION['cste']['_DIMS_LABEL_CITY']; ?></label>
						</td>
						<td id="app_offer_rech_add_city">
							<?php
							$sel_Country = null;
							if(isset($this->fields['id_country']) && $this->fields['id_country'] != '' && $this->fields['id_country'] > 0){
								$sel_Country = $this->fields['id_country'];
							}else
								$sel_Country = _DIMS_DEFAULT_COUNTRY;
							?>
							<select id="app_offer_city_id" type="text" name="app_offer_city_id" <?php echo ($sel_Country != null && $sel_Country > 0) ? '' : 'disabled="disabled"'; ?> style="width: 200px;" data-placeholder="<?php echo $_DIMS['cste']['_DIMS_SELECT_CITY']; ?>">
								<option value=""></option>
								<?php
								if($this->get('id_city') != '' && $this->get('id_city') > 0){
									$city = city::find_by(array('id'=>$this->get('id_city')),null,1);
									if(!empty($city)){
										echo '<option value="'.$city->get('id').'" selected=true>'.$city->get('label')." (".substr($city->get('insee'),0,2).")</option>";
									}
								}
								?>
							</select>
						</td>
						<td>
							<label class="title" for="app_offer_country_id"><?= $_SESSION['cste']['_DIMS_LABEL_COUNTRY']; ?></label>
						</td>
						<td>
							<select name="app_offer_country_id" id="app_offer_country_id" style="width: 200px;" data-placeholder="<?php echo $_DIMS['cste']['_DIMS_SELECT_COUNTRY']; ?>">
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
										} elseif ((!isset($this->fields['id_country']) || $this->fields['id_country'] == 0) && $country->fields['id'] == _DIMS_DEFAULT_COUNTRY){
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
					<img id="docs_bloc_img" class="clickable" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/deplier_menu.png" alt="Replier le bloc" onclick="javascript:$('#app_offer_documents').slideToggle('fast',flip_flop($('#app_offer_documents'),$(this),'<?php echo _DESKTOP_TPL_PATH; ?>'));" />
				</td>
			</tr>
		</table>
		<div id="app_offer_documents" class="desktop-hidden">
			<fieldset>
				<table class="w100">
					<tr>
						<td class="w200p vatop">
							<input class="w150 search-field" type="text" id="documentSearch" name="documentSearch" value="Recherchez un document" onkeyup="javascript:appointmentOfferSearchDocument($('#documentSearch').val(), '<?php echo _DESKTOP_TPL_PATH; ?>');" />
							<a href="javascript:void(0);" onclick="javascript:appointmentOfferSearchDocument($('#documentSearch').val(), '<?php echo _DESKTOP_TPL_PATH; ?>');" title="Lancer la recherche"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/activity_loupe.png" alt="Recherchez un document" /></a>
							<div id="searchDocumentResults"></div>
						</td>
						<td class="vatop bdleft">
							<div id="documentsList"></div>
							<p>Vous n'avez pas trouvé le document recherché - <img src="./common/img/add.gif" alt="" /> <a href="javascript:void(0);" onclick="javascript:addDocUploadField('<?php echo _DESKTOP_TPL_PATH; ?>');">ajoutez-le</a></p>
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

		// si on a de l'info dans le bloc, on l'affiche
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

			$blocImg = 'replier_menu.png';
			$blocClasses = '';
		}
		else {
			$alert = new dims_alert();
			$alert->init_description();
			$nb_period = '';
			$period = '';
			$date_alert = '';
			$hour_alert = '';
			$mins_alert = '';

			$blocImg = 'deplier_menu.png';
			$blocClasses = 'desktop-hidden';
		}
		?>
		<table class="w100 bb1">
			<tr>
				<td><h3>Programmer une alerte email interne</h3></td>
				<td class="txtright">
					<img class="clickable" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/<?php echo $blocImg; ?>" alt="Replier le bloc" onclick="javascript:$('#app_offer_email_alert').slideToggle('fast',flip_flop($('#app_offer_email_alert'),$(this),'<?php echo _DESKTOP_TPL_PATH; ?>'));" />
				</td>
			</tr>
		</table>
		<div id="app_offer_email_alert" class="<?php echo $blocClasses; ?>">
			<fieldset>
				<table>
					<tr>
						<td><input type="radio" id="app_offer_alert_mode_0" name="app_offer_alert_mode" value="0" <?php if (!$alert->fields['mode']) { echo 'checked="checked"'; } ?> /></td>
						<td><label class="title" for="app_offer_alert_mode_0">Aucune</label></td>
					</tr>
					<tr>
						<td><input type="radio" id="app_offer_alert_mode_2" name="app_offer_alert_mode" value="2" <?php if ($alert->fields['mode'] == dims_alert::MODE_ABSOLUTE) { echo 'checked="checked"'; } ?> /></td>
						<td>
							<label class="title" for="app_offer_alert_date">Le</label>
						</td>
						<td>
							<input type="text" id="app_offer_alert_date" name="app_offer_alert_date" value="<?php echo $date_alert; ?>" /> à
							<input type="text" id="app_offer_alert_hour" name="app_offer_alert_hour" value="<?php echo $hour_alert; ?>" class="w20p txtcenter" /> :
							<input type="text" id="app_offer_alert_mins" name="app_offer_alert_mins" value="<?php echo $mins_alert; ?>" class="w20p txtcenter" />
						</td>
					</tr>
				</table>
			</fieldset>
		</div>

		<p class="mt2 txtright">
			<?php if (!$this->new) {
				?>
				<input type="button" value="Enregistrer les modifications" onclick="javascript:valideAppForm('list');" />
		        <span> <?php echo $_SESSION['cste']['_DIMS_OR']; ?> </span>
				<?php
			}
			?>
			<input type="button" value="Sélectionner les plages horaires" onclick="javascript:valideAppForm();" />
	        <span> <?php echo $_SESSION['cste']['_DIMS_OR']; ?> </span>
	        <a href="javascript:void(0)" onclick="javascript:document.location.href='<?php echo $dims->getScriptEnv().'?submenu='.dims_const_desktopv2::DESKTOP_V2_DESKTOP.'&mode=appointment_offer&action=manage' ?>';">Annuler</a>
		</p>

	</form>
</div>
<?
global $dims_agenda_months;
global $dims_agenda_days;

//initialisation des tableaux utilisés par le datepicker
$full_months = '[';
$full_days = '[';

$min_month = '[';
$min_days = '[';
$mega_min_days = '[';

$i=0;
foreach($dims_agenda_months as $m){
    $full_months .= "'".$m."'";
    $min_month .= "'".utf8_encode(substr(html_entity_decode(utf8_decode($m)),0,3))."'";
    if($i< 11){
        $full_months .= ',';
        $min_month .= ',';
    }
    $i++;
}

$i=0;
foreach($dims_agenda_days as $d){
    $full_days .= "'".$d."'";
    $min_days .= "'".utf8_encode(substr(utf8_decode($d),0,3))."'";
    $mega_min_days .= "'".utf8_encode(substr(utf8_decode($d),0,2))."'";
    if($i< 6){
        $full_days .= ',';
        $mega_min_days .= ',';
        $min_days .= ',';
    }
    $i++;
}

$min_month .= ']';
$min_days .= ']';
$mega_min_days .= ']';
$full_months .= ']';
$full_days .= ']';
?>

<div id="planning_popup"></div>

<script type="text/javascript">
    function valideAppForm(redir){
        if(redir == null) redir = 'planning';
        document.f_app_offer.redir.value = redir;
        var sub = true;
        if($('input[type="radio"][name="app_offer_alert_mode"]:checked').val() == "2"){
            if($('input#app_offer_alert_hour').val() == '' || parseInt($('input#app_offer_alert_hour').val()) < 0 || parseInt($('input#app_offer_alert_hour').val()) > 23){
                $('input#app_offer_alert_hour').css('background', 'rgba(223, 29, 49, 0.5)');
                sub = false;
            }else{
                $('input#app_offer_alert_hour').css('background', '');
            }
            if($('input#app_offer_alert_mins').val() == '' || parseInt($('input#app_offer_alert_mins').val()) < 0 || parseInt($('input#app_offer_alert_mins').val()) > 59){
                $('input#app_offer_alert_mins').css('background', 'rgba(223, 29, 49, 0.5)');
                sub = false;
            }else{
                $('input#app_offer_alert_mins').css('background', '');
            }
            if($('input#app_offer_alert_date').val() == ''){
                $('input#app_offer_alert_date').css('background', 'rgba(223, 29, 49, 0.5)');
                sub = false;
            }else{
                $('input#app_offer_alert_date').css('background', '');
            }
        }
        if(sub)
            document.f_app_offer.submit();
    }
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
					'action' : 'appointment_offer_get_linked_objects',
					'app_offer_id_go' : <?php echo $this->fields['id_globalobject']; ?>
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
								'<td class="w20p txtcenter"><a href="javascript:void(0);" onclick="javascript:appointmentOfferRemoveContact(' + data.contacts[i].c.id_globalobject + ');" title="Enlever ce contact"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/supprimer20.png" /></a></td></tr></table>');
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
								'<td class="w20p txtcenter"><a href="javascript:void(0);" onclick="javascript:appointmentOfferRemoveDocument(' + data.docs[i].id_globalobject + ');" title="Enlever ce document"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/supprimer20.png" /></a></td></tr></table>');
						}
						$('#app_offer_documents').slideToggle('fast',flip_flop($('#app_offer_documents'),$('#docs_bloc_img'),'<?php echo _DESKTOP_TPL_PATH; ?>'));
					}
				}
			});
			// chargement des dates existantes
			appointmentOfferLoadDates(<?php echo $this->fields['id']; ?>);
			<?php
		}
		?>
		var prevV = '<?= $this->get('cp'); ?>';
		$('input#app_offer_cp').focusout(function(){
			var v = jQuery.trim($(this).val());
			if(v.length >= 5 && v != prevV){
				$.ajax({
					url: '<?= dims::getInstance()->getScriptEnv(); ?>',
					type: "POST",
					data: {
						'dims_op': 'desktopv2',
						'action': 'searchCity',
						'val': v,
						'id_country': $('select#app_offer_country_id').val(),
					},
					dataType: 'html',
					success: function(data) {
						$('select#app_offer_city_id').html(data).trigger("liszt:updated");
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
		$("select#app_offer_city_id").chosen({
			allow_single_deselect:true,
			no_results_text: "<?= addslashes($_SESSION['cste']['NO_RESULT']); ?>"
		})
		.ready(function(){
			var idCountry = $('select#app_offer_country_id').val();
			$('div.chzn-search input:first',$('select#app_offer_city_id').parent('td:first')).keyup(function(event){
				idCountry = $('select#app_offer_country_id').val();
				if(event.keyCode != null){
					if (event.keyCode != 16 && event.keyCode != 38 && event.keyCode != 40 && event.keyCode != 39 &&
						event.keyCode != 37 && event.keyCode != 20 && event.keyCode != 17 && event.keyCode != 18 &&
						event.keyCode != 13){
						if ($(this).val().length >= 2){
							if (tempo != null)
								clearInterval(tempo);
							tempo = setInterval("refreshVille('select#app_offer_city_id',"+idCountry+")",1200);
						}
					}else if(event.keyCode == 13){
						if (tempo != null)
							clearInterval(tempo);
						tempo = null
						refreshVille('select#app_offer_city_id',idCountry);
					}
				}
			});
		});
		$("select#app_offer_country_id")
		.chosen({no_results_text: "<?php echo addslashes($_SESSION['cste']['NO_RESULT']);?>"})
		.change(function(){
			if($(this).val() != '') {
				$('#app_offer_city_id').removeAttr('disabled');
			}
			else {
				$('#app_offer_city_id').attr('disabled','disabled');
			}
			$('select#app_offer_city_id').html('<option value=""></option>').trigger("liszt:updated");
		});

		$('div.button_add_city').live('click',function(){
			$(this).die('click');
			addNewCity('app_offer_rech_add_city','app_offer_country_id');
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
			addNewCity('app_offer_rech_add_city_user','country_id');
		});

		// aide a la saisie de la recherche
		$('#contactSearch').focus(function() {
			if ($('#contactSearch').val() == 'Recherchez un contact') { $('#contactSearch').val(''); }
		});
		$('#contactSearch').blur(function() {
			if ($('#contactSearch').val() == '') { $('#contactSearch').val('Recherchez un contact'); }
		});

		$('#documentSearch').focus(function() {
			if ($('#documentSearch').val() == 'Recherchez un document') { $('#documentSearch').val(''); }
		});
		$('#documentSearch').blur(function() {
			if ($('#documentSearch').val() == '') { $('#documentSearch').val('Recherchez un document'); }
		});
        $("#app_offer_alert_date").datepicker({
            buttonImage: '<?php echo _DESKTOP_TPL_PATH;?>/gfx/common/calendar.png',
            buttonImageOnly: true,
            showOn: 'button',
            constrainInput: true,
            defaultDate: 0,
            changeYear: true,
            dateFormat: 'dd/mm/yy',
            monthNames: <?php echo $full_months; ?>,
            monthNamesShort: <?php echo $min_month; ?>,
            dayNames: <?php echo $full_days; ?>,
            dayNamesShort: <?php echo $min_days; ?>,
            dayNamesMin: <?php echo $mega_min_days; ?>,
            minDate: 0
        });
        $('#app_offer_alert_date').change(function() {
        	$('#app_offer_alert_mode_0').removeAttr('checked');
        	$('#app_offer_alert_mode_2').attr('checked', 'checked');
        })
        $('#app_offer_alert_hour').change(function() {
        	$('#app_offer_alert_mode_0').removeAttr('checked');
        	$('#app_offer_alert_mode_2').attr('checked', 'checked');
        })
        $('#app_offer_alert_mins').change(function() {
        	$('#app_offer_alert_mode_0').removeAttr('checked');
        	$('#app_offer_alert_mode_2').attr('checked', 'checked');
        })
	});
</script>
