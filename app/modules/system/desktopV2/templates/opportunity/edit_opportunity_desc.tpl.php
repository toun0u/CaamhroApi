<?php
$datestart = $this->getLightAttribute('datestart');
$dateend = $this->getLightAttribute('dateend');
$location = $this->getLightAttribute('location');
$event = $this->getLightAttribute('event');
$a_countries = $this->getLightAttribute('a_countries');
$a_sectors = $this->getLightAttribute('a_sectors');
$a_types = $this->getLightAttribute('a_types');

// si on vient du planning
if ($this->getLightAttribute('from') == 'planning') {
	$planning_day = dims_load_securvalue('day', dims_const::_DIMS_CHAR_INPUT, true, false);
	// si on a une date en paramètre, on se positionne dessus
	if ($planning_day != '') {
		$datestart = explode('-', $planning_day);
		echo '<input type="hidden" name="planning_day" value="'.$planning_day.'" />';
	}
}

$lindedEvent = new action();
$lindedEvent->init_description();
if ($event != '' && $event > 0)
	$lindedEvent->open($event);
$myScript = '';
switch($lindedEvent->fields['typeaction']){
	case '_DIMS_MISSIONS' :
		$myScript = '$("input#type_event_trade_missions").click();';
		break;
	case '_DIMS_PLANNING_FAIR' :
		$myScript = '$("input#type_event_trade_fairs").click();';
		break;
}
?>


<div class="title_description">
	<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/home_cube.png" border="0" />
	<h2><? echo $_SESSION['cste']['_DIMS_LABEL_STEP']; ?> 1 - <?php echo $_SESSION['cste']['EVENT_LINKED_TO_THE_BUSINESS_OPPORTUNITY']; ?></h2>
</div>
<table class="date_of_opportunity" cellspacing="6" cellpadding="0">
	<tbody>
		<tr>
			<td class="text">
				<input type="hidden" id="selected_type_event" name="selected_type_event" value="none" />
				<input type="radio" id="type_event_trade_missions" name="type_event" value="trade_missions" /> <label class="radio_label" for="type_event_trade_missions"><?php echo $_SESSION['cste']['EVENTS_OPPORTUNITIES']; ?></label>
				<input type="radio" id="type_event_none" name="type_event" value="none" checked="checked" /> <label class="radio_label" for="type_event_none"><?php echo $_SESSION['cste']['_NEW_OPPORTUNITY_WITHOUT_LINK_TO_EXISTING_EVENT']; ?></label>
			</td>
		</tr>
		<tr>
			<td class="text">
				<div id="events_list">
					<?php echo $_SESSION['cste']['_DIMS_FILTER']; ?>
					<input type="text" class="opportunity_field" value="" onkeyUp="javascript:filterEventOpportunity();" id="filtereventlabel" />

					<select name="link" id="link" class="link opportunity_field" onchange="javascript:addEventInOpportunity($(this).val());">
						<option value=""></option>
					</select>
					<a id="removeEventLink" class="button" href="Javascript: void(0);" onclick="javascript:removeEventFromOpportunity();">
						<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/close.png" alt="<?php echo $_DIMS['cste']['_DIMS_CANCEL']; ?>" title="<?php echo $_DIMS['cste']['_DIMS_CANCEL']; ?>" />
					</a>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div id="div_list_searchevent"></div>
			</td>
		</tr>
	</tbody>
</table>

<div class="title_description">
	<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/home_cube.png" border="0" />
	<h2><? echo $_SESSION['cste']['_DIMS_LABEL_STEP']; ?> 2 - <?php echo $_SESSION['cste']['DATE_OF_THE_OPPORTUNITY']; ?></h2>
</div>
<div class="date_of_opportunity">
	<div class="table_description_gauche">
		<p id="opportunity_date_from">
			<span class="date_label"><? echo $_SESSION['cste']['_DIMS_DATE']; ?> <font style="color:#df1d31;"> *</font></span>
			<input maxlength="2" name="datestart_day" id="datestart_day" class="opportunity_field" style="width: 32px;" value="<?php if (!empty($datestart)) echo $datestart[2]; else echo date('d'); ?>" /> /
			<input maxlength="2" name="datestart_month" id="datestart_month" class="opportunity_field" style="width: 32px;" value="<?php if (!empty($datestart)) echo $datestart[1]; else echo date('m'); ?>" /> /
			<input rel="requis" maxlength="4" name="datestart_year" id="datestart_year" class="opportunity_field" style="width: 50px;" value="<?php if (!empty($datestart)) echo $datestart[0]; else echo date('Y'); ?>" />
			<a href="javascript:void(0)" onclick="javascript:dims_calendar_open_3('datestart_year', 'datestart_month', 'datestart_day', event);">
				<img border="0" align="top" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/calendar.png" alt="calendar" class="img_calendar">
			</a>
		</p>
	</div>
</div>

<div class="title_description">
	<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/home_cube.png" border="0" />
	<h2><? echo $_SESSION['cste']['_DIMS_LABEL_STEP']; ?> 3 - <?php echo $_SESSION['cste']['LOCATION_OF_THE_OPPORTUNITY']; ?></h2>
</div>
<div class="location_of_opportunity">
	<table cellspacing="10" cellpadding="0" style="width:90%;">
		<tbody>
			<tr>
				<td class="text">
					<? echo $_SESSION['cste']['_DIMS_LABEL_COUNTRY']; ?><font style="color:#df1d31;"> *</font>
				</td>
				<td style="width: 300px;">
					<select rel="requis" style="width: 300px;" name="opportunity_country" class="opportunity_country" id="opportunity_country" data-placeholder="<?php echo $_DIMS['cste']['_DIMS_SELECT_COUNTRY']; ?>">
						<option value=""></option>
						<?php
						$sel_Country = null;
						if (sizeof($a_countries)) {
							foreach ($a_countries as $country) {
								$sel = '';
								if (isset($location[0]) && $country->fields['id'] == $location[0]){
									$sel = "selected=true";
									$sel_Country = $country;
								}
								echo '<option value="'.$country->fields['id'].'"'.$sel.'>'.stripslashes($country->fields['printable_name']).'</option>';
							}
						}
						?>
					</select>
				</td>
				<td class="text" style="width: 150px">
					<? echo $_SESSION['cste']['_DIMS_LABEL_CITY']; ?>
				</td>
				<td class="opportunity_rech_add_city" id="opportunity_rech_add_city" style="width: 204px">
					<select id="city_opportunity" type="text" name="opportunity_city" class="city_opportunity" <?php echo ($sel_Country != null && $sel_Country->fields['id'] > 0) ? '' : 'disabled="disabled"'; ?> style="width:198px;" data-placeholder="<?php echo $_DIMS['cste']['_DIMS_SELECT_CITY']; ?>">
						<option value=""></option>
						<?
						if ($sel_Country != null && $sel_Country->fields['id'] > 0){
							$citys = $sel_Country->getAllCity();
							foreach($citys as $city){
								if (isset($location[1]) && $location[1] == $city->fields['id'])
									echo '<option value="'.$city->fields['id'].'" selected=true>'.$city->fields['label'].'</option>';
								else
									echo '<option value="'.$city->fields['id'].'">'.$city->fields['label'].'</option>';
							}
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="enter_title"><i><?php echo $_SESSION['cste']['_ENTER_A_COUNTRY']; ?></i></div>
				</td>
				<td colspan="2">
					<div class="enter_title"><i><?php echo $_SESSION['cste']['_SELECT_A_CITY']; ?></i></div>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<div class="title_description">
	<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/home_cube.png" border="0" />
	<h2><? echo $_SESSION['cste']['_DIMS_LABEL_STEP']; ?> 4 - <? echo $_SESSION['cste']['_DIMS_LABEL_DESCRIPTION']; ?></h2>
</div>
<div class="table_description">
	<div class="table_description_gauche">
		<table cellspacing="10" cellpadding="0" style="float:left;">
			<tbody>
				<tr>
					<td class="type_ie7">
						<?php echo $_SESSION['cste']['LABEL_OF_THE_OPPORTUNITY']; ?>
					</td>
					<td style="text-align:left;">
						<input id="label" type="text" name="label" value="<?php echo stripslashes($this->fields['libelle']); ?>" style="width: 240px;" class="opportunity_field" />
					</td>
				</tr>
                <tr>
					<td class="type_ie7">
						<? echo $_SESSION['cste']['_TYPE']; ?>
					</td>
					<td id="opportunity_rech_add_type" style="text-align:left;">
						<select style="width: 240px;" id="type_id" name="type_id" class="type" data-placeholder="<?php echo $_DIMS['cste']['_DIMS_SELECT_TYPE']; ?>">
							<option value=""></option>
							<?php
							if (sizeof($a_types)) {
								foreach ($a_types as $type) {
									$sel = ($type->fields['id'] == $this->fields['opportunity_type_id']) ? ' selected' : '';
									echo '<option value="'.$type->fields['id'].'"'.$sel.'>'.stripslashes($type->fields['label']).'</option>';
								}
							}
							?>
						</select>
					</td>
				</tr>
<?php /*
				<tr>
					<td class="type_ie7" style="text-align:right;">
						<?php echo $_SESSION['cste']['SECTOR']; ?>
					</td>
					<td style="text-align:left;float: left; margin-top: 4px; width:100%;" id="opportunity_rech_add_sector">
						<select data-placeholder="<?php echo $_DIMS['cste']['_DIMS_SELECT_SECTOR']; ?>" id="sector_id" name="sector_id" style="width: 240px;" class="sector">
							<option value=""></option>
							<?php
							if (sizeof($a_sectors)) {
								foreach ($a_sectors as $sector) {
									$sel = ($sector->fields['id'] == $this->fields['opportunity_sector_id']) ? ' selected' : '';
									echo '<option value="'.$sector->fields['id'].'"'.$sel.'>'.stripslashes($sector->fields['label']).'</option>';
								}
							}
							?>
						</select>
					</td>
				</tr>
*/ ?>
				<tr>
					<td colspan="2">
					    <div class="zone_avatar">
							<table>
							<tr>
								<td>
									<div class="cadre_add_picture">
									<?php
									if ($this->fields['banner_path'] != '' && file_exists($this->fields['banner_path']))
										echo '<img class="opportunity_img" src="'.$this->fields['banner_path'].'" alt="'.$_SESSION['cste']['_AVATAR'].'" title="'.stripslashes($this->fields['libelle']).'" />';
									?>
									</div>
								</td>
								<td>
									<div class="text-avatar">Avatar</div>
								</td>
								<td>
									<input type="file" name="avatar" class="file" />
									<span style="float: left; width: 100%;font-size:10px;font-weight:normal;">
										<i><?php echo $_SESSION['cste']['PREFERABLE_TO_UPLOAD_A_PICTURE_WITH_DIMENSIONS']; ?> 60x60px</i>
									</span>
								</td>
							</tr>
							</table>
					    </div>
    				</td>
    			</tr>
			</tbody>
		</table>
	</div>
	<div class="table_description_droite">
        <table cellspacing="10" cellpadding="0" style="width:90%;float:right;">
            <tbody>
                <tr>
                    <td style="width:20%;vertical-align:top;">
						<div class="title_area">
							<? echo $_SESSION['cste']['_DIMS_COMMENTS']; ?>
						</div>
					</td>
                    <td colspan="2" style="width:80%;">
                        <div style="float:right; width: 100%;">
                            <textarea id="description" name="description" class="opportunity_field opportunity_textarea"><?php echo stripslashes(str_replace('\r\n', "\r\n", $this->fields['description'])); ?></textarea>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
	</div>
</div>

<div class="separator_mandatory">
	<span><font style="color:#df1d31;">* </font><? echo $_SESSION['cste']['_DIMS_LABEL_MANDATORY_FIELDS']; ?></span>
</div>

<script language="javascript" type="text/javascript">
    $(document).ready(function(){
        $("#date_range").click(function() {
			$("#opportunity_date_to").fadeToggle('fast',function(){
				if($("#opportunity_date_from span:first").html() == '<? echo $_SESSION['cste']['_DIMS_DATE']; ?> <font style="color:#df1d31"> *</font>')
					$("#opportunity_date_from span:first").html('<? echo $_SESSION['cste']['_FROM']; ?> <font style="color:#df1d31"> *</font>');
				else
					$("#opportunity_date_from span:first").html('<? echo $_SESSION['cste']['_DIMS_DATE']; ?> <font style="color:#df1d31"> *</font>');
			});
        });

		if ($('img.opportunity_img').length){
			$('img.opportunity_img').css("margin-left",2-($('img.opportunity_img').css("width").substr(0,$('img.opportunity_img').css("width").length-2))+"px");
			$('img.opportunity_img').css("margin-top","-2px");
		}
		$("select.city_opportunity").chosen({allow_single_deselect:true, no_results_text: "<div onclick=\"javascript:addNewCity('opportunity_rech_add_city','opportunity_country');\" style=\"float:right;color:#E21C2C;cursor:pointer;\"><img style=\"float:left;\" src=\"<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/add.png\" /><div style=\"float:right;margin-top:3px;\">Add it !</div></div>No results matched"});
		$("select.opportunity_country")
			.chosen({no_results_text: "No results matched"})
			.change(function(){
				if($(this).val() != '') {
					$('#city_opportunity').removeAttr('disabled');
				}
				else {
					$('#city_opportunity').attr('disabled','disabled');
				}
				refreshCityOfCountry($(this).val(),'city_opportunity');
			});
		$("select#sector_id").chosen({allow_single_deselect:true, no_results_text: "<div onclick=\"javascript:addNewSector('opportunity_rech_add_sector');\" style=\"float:right;color:#E21C2C;cursor:pointer;\"><img style=\"float:left;\" src=\"<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/add.png\" /><div style=\"float:right;margin-top:3px;\">New sector ?</div></div>No results matched"});
		$("select#type_id").chosen({allow_single_deselect:true, no_results_text: "<div onclick=\"javascript:addNewType('opportunity_rech_add_type');\" style=\"float:right;color:#E21C2C;cursor:pointer;\"><img style=\"float:left;\" src=\"<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/add.png\" /><div style=\"float:right;margin-top:3px;\">New type ?</div></div>No results matched"});

		$("#type_event_trade_missions").click(function() {
			if ($('#selected_type_event').val() != 'trade_missions') {
				// on conserve la sélection pour ne pas recharger si on clique sur l'élément déjà sélectionné
				$('#selected_type_event').val('trade_missions');

				removeEventFromOpportunity();
				$("#events_list").fadeIn('200');
				updateEventsList('trade_missions','<? echo $event; ?>');
	    		if ($("#filtereventlabel").val() != '') {
	    			filterEventOpportunity();
	    		}
	    		$("#removeEventLink").css({ visibility: 'visible'});
			}
		});

		$("#type_event_trade_fairs").click(function() {
			if ($('#selected_type_event').val() != 'trade_event') {
				// on conserve la sélection pour ne pas recharger si on clique sur l'élément déjà sélectionné
				$('#selected_type_event').val('trade_event');

				removeEventFromOpportunity();
				$("#events_list").fadeIn('200');
				updateEventsList('trade_fairs','<? echo $event; ?>');
	    		if ($("#filtereventlabel").val() != '') {
	    			filterEventOpportunity();
	    		}
	    		$("#removeEventLink").css({ visibility: 'visible'});
	    	}
		});

		$("#type_event_none").click(function() {
			if ($('#selected_type_event').val() != 'none') {
				// on conserve la sélection pour ne pas recharger si on clique sur l'élément déjà sélectionné
				$('#selected_type_event').val('none');

				$("#events_list").fadeOut('200');

	    		$("#filtereventlabel").val('');
				removeEventFromOpportunity();
				$("#datestart_day").val('<?php echo date("d"); ?>');
				$("#datestart_month").val('<?php echo date("m"); ?>');
				$("#datestart_year").val('<?php echo date("Y"); ?>');
				$("#div_list_searchevent").hide('fast');
	    		$("#removeEventLink").css({ visibility: 'hidden'});
	    	}
		});
		<? echo $myScript; ?>
    });
</script>
