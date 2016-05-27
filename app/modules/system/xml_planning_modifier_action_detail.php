<form name="form_action" action="admin.php" method="post">
	<?
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("op",				"xml_planning_action_enregistrer");
		$token->field("action_id",		$action->fields['id']);
		$token->field("action_type",	$type);
		$token->field("action_typeaction");
		$token->field("fck_action_libelle");
		$token->field("action_datejour");
		$token->field("datefin");
		$token->field("actionx_heuredeb_h");
		$token->field("actionx_heuredeb_m");
		$token->field("actionx_heurefin_h");
		$token->field("actionx_heurefin_m");
		$token->field("actionx_duree");
		$token->field("daterelease");
		$token->field("supportrelease");
		$token->field("evt_display_hp");
		$token->field("evt_allow_fo");
		$token->field("dateopen");
		$token->field("datefin_insc");
		$token->field("evt_conditions");
		$token->field("form_level");
		$token->field("evt_prix");
		$token->field("form_lang");
		$token->field("datefin");
		$token->field("action_personnel");
		$token->field("action_conges");
		$token->field("action_interne");
		$token->field("fck_evt_lieu");
		$token->field("fck_action_description");
		$token->field("fck_action_teaser");
		$token->field("nomsearchplanning");
		$token->field("partsearchplanning");
		$token->field("nomsearchplanning");
		$token->field("search_contact");
		$token->field("action_is_model");
	?>
	<input type="hidden" name="op" value="xml_planning_action_enregistrer">
	<input type="hidden" name="action_id" value="<?php echo $action->fields['id']; ?>">
	<input type="hidden" name="action_type" value="<?php echo $type; ?>">
	<div id="block_content0" style="width:99%;">
	<?php

	echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_PROPERTIES'],'width:100%; margin-left:10px; margin-right:10px; float:left;');
	/*** Categorie evenement lfb ***/
	echo '<table cellpadding="0" cellspacing="5" width="100%">';
	if($type == dims_const::_PLANNING_ACTION_EVT ) {
	?>
		<tr>
			<td width="50%">
				<table cellpadding="0" cellspacing="5" width="100%">
					<tr>
						<td align="right"><?php echo $_DIMS['cste']['_TYPE']; ?></td>
						<td align="left">
							<select class="select" name="action_typeaction" onchange="javascript:document.form_action.submit();">

								<?php
									$listenum = array();

									if($type == dims_const::_PLANNING_ACTION_EVT)
										$listenum = business_getlistenum('typeaction_evt');
									else
										$listenum = business_getlistenum('typeaction');

									foreach($listenum as $id_enum => $enum) {
										if ($enum['libelle'] != ''){
											$sel = ($enum['libelle'] == $action->fields['typeaction']) ? ' selected' : '';
											echo "<option$sel value=\"".html_entity_decode($enum['libelle'])."\">{$enum['libelle']}</option>";
										}
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="right"><?php echo $_DIMS['cste']['_DIMS_LABEL_LABEL']; ?></td>
						<td align="left">
							<input type="text" class="text" size="30" id="fck_action_libelle" name="fck_action_libelle" value="<? echo $action->fields['libelle']; ?>">
						</td>
					</tr>
					<tr>
						<td align="right" width="30%">
							<label ><?php echo $_DIMS['cste']['_INFOS_START_DATE']; ?>&nbsp;</label>
						</td>
						<td width="50%">
							<?php
								if ($id==0) {
									$action->fields['datejour']=business_datefr2us($datejour);
									$action->fields['datefin']=business_datefr2us($datejour);
								}
							?>
							<input class="text" type="text" id="action_datejour" name="action_datejour" value="<? echo business_dateus2fr($action->fields['datejour']); ?>">
							<a href="#" onclick="javascript:dims_calendar_open('action_datejour', event,'updateDate()');"><img src="./common/img/calendar/calendar.gif" alt="" width="31" height="18" align="top" border="0"></a>
						</td>
					</tr>
					<tr>
						<td align="right" width="30%">
							<label ><?php echo $_DIMS['cste']['_INFOS_END_DATE']; ?>&nbsp;</label>
						</td>
						<td width="50%">
							<input class="text" type="text" id="datefin" onchange="updateDate();" name="datefin" value="<? echo business_dateus2fr($action->fields['datefin']); ?>">
							<a href="#" onclick="javascript:dims_calendar_open('datefin', event,'updateDate()');">
								<img src="./common/img/calendar/calendar.gif" alt="" width="31" height="18" align="top" border="0">
							</a>
						</td>
					</tr>

				</table>

			</td>
			<td>
				<?
					$disp = ($action->fields['typeaction'] == '_DIMS_PLANNING_FAIR_STEPS') ? 'none' : 'block';
				?>
				<div style="width:100%;display:<? echo $disp; ?>">
				<table cellpadding="0" cellspacing="5" width="100%">
					<tr>
						<td align="right" width="30%">
							<label ><?php echo $_DIMS['cste']['_DIMS_LABEL_HEUREDEB']; ?>&nbsp;</label>
						</td>
						<td width="50%">
							<select class="select" name="actionx_heuredeb_h" style="width:50px;">
							<?php
							//if(isset($heure_dispo_deb)) {
							//	$heure = substr($heure_dispo_deb, 0, 2);
							//	$minute = substr($heure_dispo_deb, 3, 2);
							//}
							//else {
								if ($action->fields['heuredeb'] != '') { //isset($id)
									$heure_split = split(':',$action->fields['heuredeb']);
									$heure = $heure_split[0];
									$minute = $heure_split[1];
									$minute = $minute - ($minute%5);
								}
								else {
									//if($action->fields['typeaction'] == 'Foire') {
										$heure = "09";
										$minute = 0;//date('i');
									//}
									//else {
										//$heure = date('H');
										//$minute = 0;//date('i');
										//$minute = $minute - ($minute%5);
									//}
								}
							//}
							for ($h=dims_const::_PLANNING_H_START;$h<=dims_const::_PLANNING_H_END;$h++) {
									$sel = ($heure==$h) ? 'selected' : '';
									printf("<option %s value=\"%02d\">%02d</option>",$sel,$h,$h);
							}
							?>
							</select> h
							<select class="select" name="actionx_heuredeb_m" style="width:50px;">
									<?php
									for ($m=0;$m<4;$m++) {
											$sel = ($minute==$m*15) ? 'selected' : '';
											printf("<option %s value=\"%02d\">%02d</option>",$sel, $m*15, $m*15);
									}
									?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="right" width="30%">
							<label ><?php echo $_DIMS['cste']['_DIMS_LABEL_HEUREFIN']; ?>&nbsp;</label>
						</td>
						<td width="50%">
							<select class="select" name="actionx_heurefin_h" style="width:50px;">
								<?php

								//if(isset($heure_dispo_fin)) {
								//	$heure = substr($heure_dispo_fin, 0, 2);
								//	$minute = substr($heure_dispo_fin, 3, 2);
								//}
								//else {
									if ($action->fields['heurefin']) //isset($id)
									{
										$heure_split = split(':',$action->fields['heurefin']);
										$heure = $heure_split[0];
										$minute = $heure_split[1];
										$minute = $minute - ($minute%5);
									}
									else {
										//if($action->fields['typeaction'] == 'Foire') {
											$heure = "18";
											$minute = 0;//date('i');
										//}
										//else {
										//	$heure = date('H')+1;
										//	if ($heure>(_business_H_END+1)) $heure=0;
										//	$minute = 0;//date('i');
										//	$minute = $minute - ($minute%5);
										//}
									}
								//}

								for ($h=dims_const::_PLANNING_H_START;$h<=dims_const::_PLANNING_H_END;$h++)
								{
									$sel = ($heure==$h) ? 'selected' : '';
									printf("<option %s value=\"%02d\">%02d</option>",$sel,$h,$h);
								}
								?>
							</select> h
							<select class="select" name="actionx_heurefin_m" style="width:50px;">
								<?php
								for ($m=0;$m<4;$m++)
								{
									$sel = ($minute==$m*15) ? 'selected' : '';
									printf("<option %s value=\"%02d\">%02d</option>",$sel, $m*15, $m*15);
								}
								?>
							</select>
							<input type="hidden" name="actionx_duree" value=""/>
						</td>
					</tr>
				</table>
				</div>
			</td>
		</tr>
	</table>
	<?php
	echo $skin->close_simplebloc();

	echo $skin->open_simplebloc($_DIMS['cste']['_WCE_ARTICLE_PUBLISH'],'width:100%; margin-left:10px; margin-right:10px; float:left;');
	echo '<table cellpadding="0" cellspacing="5" width="100%">';
	?>
		<tr>
			<td width="50%" valign="top">
				<table cellpadding="0" cellspacing="5" width="100%">
					<tr>
						<td align="right" width="30%" valign="top">
							<label for="daterelease"><?php echo $_DIMS['cste']['_DIMS_EVT_RELEASING_DATE']; ?>&nbsp;</label>
						</td>
						<td width="50%">
							<?php
								if(!empty($action->fields['timestamp_release']))
									$date_release =  dims_timestamp2local($action->fields['timestamp_release']);
								else
									$date_release = null;
							?>
							<input class="text" type="text" id="daterelease" name="daterelease" value="<?php echo $date_release['date']; ?>">
							<a href="#" onclick="javascript:dims_calendar_open('daterelease', event,'updateDate()');"><img src="./common/img/calendar/calendar.gif" alt="" width="31" height="18" align="top" border="0"></a>
						</td>
					</tr>
					<?php
						if($action->fields['typeaction'] != '_DIMS_PLANNING_FAIR_STEPS') {
					?>
					<tr>
						<td align="right" width="30%">
							<?php echo $_DIMS['cste']['_DIMS_EVENT_LABEL_PUBLISHED']; ?>&nbsp;
						</td>
						<td width="50%">
							<table cellpadding="0" cellspacing="0">
							<tr>
								<td align="right"><label for="supportrelease_yes"><?php echo $_DIMS['cste']['_DIMS_YES']; ?>&nbsp;</label></td>
								<td align="left">
									<input type="radio" name="supportrelease" id="supportrelease_yes" value="1" <?php echo ($action->fields['supportrelease'] == 1) ? 'checked' : ''; ?>>
								</td>
								<td align="right"><label for="supportrelease_no"><?php echo $_DIMS['cste']['_DIMS_NO']; ?>&nbsp;</label></td>
								<td align="left">
									<input type="radio" name="supportrelease" id="supportrelease_no" value="0" <?php echo ($action->fields['supportrelease'] == 0) ? 'checked' : ''; ?>>
								</td>
							</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td align="right" width="30%">
							<?php echo $_DIMS['cste']['_DIMS_EVENT_LABEL_DISP_HP']; ?>&nbsp;
						</td>
						<td width="50%">
							<table cellpadding="0" cellspacing="0">
							<tr>
								<td align="right"><label for="affiche_yes"><?php echo $_DIMS['cste']['_DIMS_YES']; ?>&nbsp;</label></td>
								<td align="left">
									<input type="radio" name="evt_display_hp" id="evt_display_hp" value="1" <?php echo ($action->fields['display_hp'] == 1) ? 'checked' : ''; ?>>
								</td>
								<td align="right"><label for="affiche_no"><?php echo $_DIMS['cste']['_DIMS_NO']; ?>&nbsp;</label></td>
								<td align="left">
									<input type="radio" name="evt_display_hp" id="evt_display_hp" value="0" <?php echo ($action->fields['display_hp'] == 0) ? 'checked' : ''; ?>>
								</td>
							</tr>
							</table>
						</td>
					</tr>
					<?
						}
					?>
					<tr>
						<td align="right" width="30%">
							<?php echo $_DIMS['cste']['_DIMS_EVT_ALLOW_FO']; ?>&nbsp;
						</td>
						<td width="50%">
							<table cellpadding="0" cellspacing="0">
							<tr>
								<td align="right"><label for="fo_yes"><?php echo $_DIMS['cste']['_DIMS_YES']; ?>&nbsp;</label></td>
								<td align="left">
									 <input type="radio" name="evt_allow_fo" id="fo_yes" value="1" <?php if($action->fields['allow_fo']) echo 'checked'; ?> onclick="javascript:document.getElementById('div_dateopen').style.display='block';document.getElementById('div_dateopen2').style.display='block';"/>
								</td>
								<td align="right"><label for="fo_no"><?php echo $_DIMS['cste']['_DIMS_NO']; ?>&nbsp;</label></td>
								<td align="left">
									<input type="radio" name="evt_allow_fo" id="fo_no" value="0" <?php if(!$action->fields['allow_fo']) echo 'checked'; ?> onclick="javascript:document.getElementById('div_dateopen').style.display='none';document.getElementById('div_dateopen2').style.display='none';"/>
								</td>
							</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td align="right" width="30%">
							<?php if(!$action->fields['allow_fo']) $disp = 'none'; else $disp = 'block';  ?>
							<div id="div_dateopen" style="display:<?php echo $disp; ?>"><label for="dateopen"><?php echo $_DIMS['cste']['_DIMS_EVT_DATE_OPEN_REGISTER']; ?>&nbsp;</label></div>
						</td>
						<td width="50%">
							<?php
								if(!empty($action->fields['timestp_open']))
									$dateOpen =  dims_timestamp2local($action->fields['timestp_open']);
								else
									$dateOpen = null;
							?>
							<div id="div_dateopen2" style="display:<?php echo $disp; ?>">
								<input class="text" type="text" id="dateopen" name="dateopen" value="<?php echo $dateOpen['date']; ?>">
								<a href="#" onclick="javascript:dims_calendar_open('dateopen', event,'updateDate()');"><img src="./common/img/calendar/calendar.gif" alt="" width="31" height="18" align="top" border="0"></a>
							</div>
						</td>
					</tr>
					<tr>
						<td align="right" width="30%">
							<?php if(!$action->fields['allow_fo']) $disp = 'none'; else $disp = 'block';  ?>
							<div id="div_dateend" style="display:<?php echo $disp; ?>"><label for="dateclose"><?php echo $_DIMS['cste']['_DIMS_EVT_DATE_CLOSE_REGISTER']; ?>&nbsp;</label></div>
						</td>
						<td width="50%">
							<?php
								if(!empty($action->fields['datefin_insc']))
									$dateEnd =	business_dateus2fr($action->fields['datefin_insc']);
								else
									$dateEnd = null;
							?>
							<div id="div_dateend2" style="display:<?php echo $disp; ?>">
								<input class="text" type="text" id="datefin_insc" name="datefin_insc" value="<?php echo $dateEnd; ?>">
								<a href="#" onclick="javascript:dims_calendar_open('datefin_insc', event,'updateDate()');"><img src="./common/img/calendar/calendar.gif" alt="" width="31" height="18" align="top" border="0"></a>
							</div>
						</td>
					</tr>

					<?php

					/*if(!empty($action->fields['target']) && !$action->new && $action->fields['typeaction'] != '_DIMS_PLANNING_FAIR_STEPS') {
					?>
					<tr>
						<td align="right" width="30%">
							<label for="evt_target"><?php echo $_DIMS['cste']['_DIMS_EVT_TARGET']; ?>&nbsp;</label>
						</td>
						<td width="50%">
							<input type="text" name="evt_target" id="evt_target" class="text" maxlength="50" value="<?php echo ($action->fields['target']); ?>" />
						</td>
					</tr>
					<?php
					}*/
					if(!empty($action->fields['conditions']) && !$action->new) {
					?>
					<tr>
						<td align="right" width="30%">
							<label for="evt_conditions"><?php echo $_DIMS['cste']['_DIMS_EVT_CONDITION']; ?>&nbsp;</label>
						</td>
						<td width="50%">
							<textarea name="evt_conditions" id="evt_conditions" rows="2" cols="30" class="text"><?php echo ($action->fields['conditions']); ?></textarea>
						</td>
					</tr>
					<?php
					}
					?>
					<tr>
						<td align="right" width="30%">
							<label for="form_level"><?php echo $_DIMS['cste']['_EVENT_FORM_2LEVEL']; ?>&nbsp;</label>
						</td>
						<td width="50%">
							<input type="checkbox" name="form_level" id="form_level" class="text" <?php echo ($action->fields['niveau'] == 2) ? 'checked="checked"' : '' ?> />
						</td>
					</tr>
					<?php
						//Dans le cas d'une foire, le prix est affich� directement dans la page
						//alors que dans les autres cas il s'agit d'une option
						if($action->fields['typeaction'] == '_DIMS_PLANNING_FAIR_STEPS') {
					?>
					<tr>
						<td align="right" width="30%">
							<label for="evt_prix"><?php echo $_DIMS['cste']['_DIMS_EVT_PRIX']; ?>&nbsp;</label>
						</td>
						<td width="50%">
							<input type="text" name="evt_prix" id="evt_prix" class="text" value="<?php echo $action->fields['prix']; ?>" />
						</td>
					</tr>
					<?php }

						if($action->fields['typeaction'] != '_DIMS_PLANNING_FAIR_STEPS') {
					?>
					<tr>
						<td colspan="2">
							<div><a style="text-decoration: underline;" href="javascript: dims_switchdisplay('evtMoreOptions');" ><?php echo $_DIMS['cste']['_DIMS_MORE_OPTIONS'] ?> +</a></div>
						</td>
					</tr>
					<tr>
						<td colspan="2">

							<div id="evtMoreOptions" style="display: none;">
								<table width="100%">
									<tr>
																			<td align="right" width="30%">
																					<label for="evt_prix"><?php echo $_DIMS['cste']['_DIMS_EVT_PRIX']; ?>&nbsp;</label>
																			</td>
																			<td width="50%">
																					<input type="text" name="evt_prix" id="evt_prix" class="text" value="<?php echo $action->fields['prix']; ?>" />
																			</td>
									</tr>
									<?/*<tr>
										<td align="right">
											<label for="evt_target"><?php echo $_DIMS['cste']['_DIMS_EVT_TARGET']; ?>&nbsp;</label>
										</td>
										<td width="50%">
											<input type="text" name="evt_target" id="evt_target" class="text" maxlength="50" value="<?php echo $action->fields['target']; ?>" />
										</td>
									</tr>*/?>
									<tr>
										<td align="right">
											<label for="evt_conditions"><?php echo $_DIMS['cste']['_DIMS_EVT_CONDITION']; ?>&nbsp;</label>
										</td>
										<td width="50%">
											<textarea name="evt_conditions" id="evt_conditions" rows="2" cols="30" class="text"><?php echo $action->fields['condition']; ?></textarea>
										</td>
									</tr>
								</table>
							</div>
						</td>
					</tr>
						<?
						if	($action->fields['niveau'] == 2) {
						?>
					<tr>
						<td align="right" width="30%">
							<label for="form_lang"><?php echo $_DIMS['cste']['_DIMS_LABEL_LANG']; ?>&nbsp;</label>
						</td>
						<td width="50%">
							<input type="text" name="form_lang" id="form_level" class="text" value="<?php echo $action->fields['language'];?>"/>
						</td>
					</tr>

					<?
						}
					}
					?>
				</table>
			</td>
			<td  valign="top">
				<table cellpadding="0" cellspacing="5" width="100%" >
					<tr>
						<td colspan="2">
						<?
						if (!$action->new) {
							$lst=$action->getResps();
						}
						else {
							$lst['users']=array();
							$lst['groups']=array();
						}

						// int�gration des �l�ments de l'event
						$element=dims_const::_SYSTEM_OBJECT_ACTION;
						$_SESSION['obj'][$element]['labeldest']=$_SESSION['cste']['_DIMS_LABEL_ORGANIZER'];
						// boucle sur les users
						foreach ($lst['users'] as $id_usr) {
							$_SESSION['obj'][$element]['users'][$id_usr]=$id_usr;
						}

						// boucle sur les groupes
						foreach ($lst['groups'] as $id_grp) {
							$_SESSION['obj'][$element]['groups'][$id_grp]=$id_grp;
						}

						require_once(DIMS_APP_PATH . '/modules/system/form_searchusers.php');
						?>
						</td>
					</tr>
				</table>
			</td>
		</tr>

		<?php
		}
		if($type == dims_const::_PLANNING_ACTION_RDV || $type == dims_const::_PLANNING_ACTION_RCT) {
		?>
										<tr>
											<td width="50%">
													<table cellpadding="0" cellspacing="5" width="100%">
						<td align="right"><?php echo $_DIMS['cste']['_TYPE']; ?></td>
						<td align="left">
							<select class="select" name="action_typeaction" >

								<?php
									$listenum = array();

									if($type == dims_const::_PLANNING_ACTION_EVT)
										$listenum = business_getlistenum('typeaction_evt');
									else
										$listenum = business_getlistenum('typeaction');

									foreach($listenum as $id_enum => $enum) {
										if ($enum['libelle'] != ''){
																																												$title=$enum['libelle'];
																																												if (isset($_DIMS['cste'][$enum['libelle']])) $title=$_DIMS['cste'][$enum['libelle']];
											$sel = ($enum['libelle'] == $action->fields['typeaction']) ? ' selected' : '';
											echo "<option$sel value=\"".html_entity_decode($enum['libelle'])."\">{$title}</option>";
										}
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="right"><?php echo $_DIMS['cste']['_DIMS_LABEL_LABEL']; ?></td>
						<td align="left">
							<input type="text" class="text" size="30" id="fck_action_libelle" name="fck_action_libelle" value="<? echo $action->fields['libelle']; ?>">
						</td>
					</tr>
												</table>
											</td>
											<td>&nbsp;</td>
										<tr>
				<td width="50%">
					<table cellpadding="0" cellspacing="5" width="100%">
						<tr>
							<td align="right" width="30%">
								<label ><?php echo $_DIMS['cste']['_INFOS_START_DATE']; ?>&nbsp;</label>
							</td>
							<td width="50%">
								<?php
									if ($id==0) {
										$action->fields['datejour']=business_datefr2us($datejour);
										$action->fields['datefin']=business_datefr2us($datejour);
									}
								?>
								<input class="text" type="text" id="action_datejour" name="action_datejour" value="<? echo business_dateus2fr($action->fields['datejour']); ?>">
								<a href="#" onclick="javascript:dims_calendar_open('action_datejour', event,'updateDate()');"><img src="./common/img/calendar/calendar.gif" alt="" width="31" height="18" align="top" border="0"></a>
							</td>
						</tr>
						<tr>
							<td align="right" width="30%">
								<label ><?php echo $_DIMS['cste']['_INFOS_END_DATE']; ?>&nbsp;</label>
							</td>
							<td width="50%">
								<input class="text" type="text" id="datefin" onchange="updateDate();" name="datefin" value="<? echo business_dateus2fr($action->fields['datefin']); ?>">
								<a href="#" onclick="javascript:dims_calendar_open('datefin', event,'updateDate()');">
									<img src="./common/img/calendar/calendar.gif" alt="" width="31" height="18" align="top" border="0">
								</a>
							</td>
						</tr>

					</table>

				</td>
				<td>
					<table cellpadding="0" cellspacing="5" width="100%">
						<tr>
							<td align="right" width="30%">
								<label ><?php echo $_DIMS['cste']['_DIMS_LABEL_HEUREDEB']; ?>&nbsp;</label>
							</td>
							<td width="50%">
								<select class="select" name="actionx_heuredeb_h" style="width:50px;">
								<?php
								if(isset($heure_dispo_deb)) {
									$heure = substr($heure_dispo_deb, 0, 2);
									$minute = substr($heure_dispo_deb, 3, 2);
								}
								else {
									if (isset($id)) {
										$heure_split = split(':',$action->fields['heuredeb']);
										$heure = $heure_split[0];
										$minute = $heure_split[1];
										$minute = $minute - ($minute%5);
									}
									else {
										$heure = date('H');
										$minute = 0;//date('i');
										$minute = $minute - ($minute%5);
									}
								}

								for ($h=dims_const::_PLANNING_H_START;$h<=dims_const::_PLANNING_H_END;$h++) {
										$sel = ($heure==$h) ? 'selected' : '';
										printf("<option %s value=\"%02d\">%02d</option>",$sel,$h,$h);
								}
								?>
								</select> h
								<select class="select" name="actionx_heuredeb_m" style="width:50px;">
										<?php
										for ($m=0;$m<4;$m++) {
												$sel = ($minute==$m*15) ? 'selected' : '';
												printf("<option %s value=\"%02d\">%02d</option>",$sel, $m*15, $m*15);
										}
										?>
								</select>
							</td>
						</tr>
						<tr>
							<td align="right" width="30%">
								<label ><?php echo $_DIMS['cste']['_DIMS_LABEL_HEUREFIN']; ?>&nbsp;</label>
							</td>
							<td width="50%">
								<select class="select" name="actionx_heurefin_h" style="width:50px;">
									<?php

									if(isset($heure_dispo_fin)) {
										$heure = substr($heure_dispo_fin, 0, 2);
										$minute = substr($heure_dispo_fin, 3, 2);
									}
									else {
										if (isset($id))
										{
											$heure_split = split(':',$action->fields['heurefin']);
											$heure = $heure_split[0];
											$minute = $heure_split[1];
											$minute = $minute - ($minute%5);
										}
										else {
											$heure = date('H')+1;
											if ($heure>(_business_H_END+1)) $heure=0;
											$minute = 0;//date('i');
											$minute = $minute - ($minute%5);
										}
									}

									for ($h=dims_const::_PLANNING_H_START;$h<=dims_const::_PLANNING_H_END;$h++)
									{
										$sel = ($heure==$h) ? 'selected' : '';
										printf("<option %s value=\"%02d\">%02d</option>",$sel,$h,$h);
									}
									?>
								</select> h
								<select class="select" name="actionx_heurefin_m" style="width:50px;">
									<?php
									for ($m=0;$m<4;$m++)
									{
										$sel = ($minute==$m*15) ? 'selected' : '';
										printf("<option %s value=\"%02d\">%02d</option>",$sel, $m*15, $m*15);
									}
									?>
								</select>
								<input type="hidden" name="actionx_duree" value=""/>
							</td>
						</tr>

					</table>
				</td>
			</tr>
			<?php

			if ($type == dims_const::_PLANNING_ACTION_RDV) {

			?>
			</table>
			<?php
			echo $skin->close_simplebloc();

			echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_PROPERTIES'],'width:100%; margin-left:10px; margin-right:10px; float:left;');
			?>
			<table>
				<tr>
					<td align="left" colspan="2">
						<table cellpadding="0" cellspacing="0">
						<tr>
							<td align="right">&nbsp;&nbsp;&nbsp;<?php echo $_DIMS['cste']['_DIMS_LABEL_PERSO']; ?></td>
							<td align="left">
									<input type="checkbox" name="action_personnel" <?php if ($action->fields['personnel']) echo 'checked'; ?> value="1">
							</td>
							<td align="right">&nbsp;&nbsp;&nbsp;<?php echo $_DIMS['cste']['_DIMS_LABEL_CONGE']; ?></td>
							<td align="left">
									<input type="checkbox" name="action_conges" <?php if ($action->fields['conges']) echo 'checked'; ?> value="1">
							</td>
						</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="2">
					<?php
						echo "<span style=\"text-align:center;width:100%;\">".$_DIMS['cste']['_DIMS_LABEL_LIMIT_ACTION']."&nbsp;<input type=\"checkbox\" name=\"action_interne\" ".(($action->fields['interne']) ? 'checked' : "")."value=\"1\"></span>";
					?>
					</td>
				</tr>
			<?php
			}
		}
		?>
		</table>
		<?php
		echo $skin->close_simplebloc();

		/*echo $skin->open_simplebloc("Participations");
		dims_fckeditor('action_participations',$action->fields['participations'], '100%', '200', true);
		echo $skin->close_simplebloc();

		echo $skin->open_simplebloc("Bookings");
		dims_fckeditor('action_booking',$action->fields['booking'], '100%', '300', true);
		echo $skin->close_simplebloc();

		if($id==0 || $id == ""){
			echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_NEXT_MILESTONE'],'./common/img/publish.png','javascript:change_menu(1);');
		}*/

		echo '<div style="witdh:450px;float:right;">';
		if($type == dims_const::_PLANNING_ACTION_RDV)
			echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"./common/img/save.gif","javascript:if (!business_verif_action(document.form_action)) {alert('Vous devez sélectionner un client et un dossier');return(false)}","enreg","width:150px");
		else
			echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"./common/img/save.gif","javascript:form_action.submit();","enreg","width:150px;float:left;");

		//if($id==0 || $id == ""){
			echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_NEXT_MILESTONE'],'./common/img/go-next.png','javascript:change_menu(1);','','width:110px;float:left;');
		//}
		echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_CANCEL'],'./common/img/delete.png',"location.href='admin.php'",'cancel1','width:90px;float:right;');
		echo '</div>';
		?>
	</div>

	<div id="block_content1" style="display:none;width:100%;float:left;">
		<table cellpadding="0" cellspacing="5" width="100%">
			<tr>
				<td>
					<table cellpadding="0" cellspacing="5" width="100%">
						<?php

						if($type == dims_const::_PLANNING_ACTION_EVT) {
						?>
						<tr>
							<td align="right" width="30%">
								<label for="evt_lieu"><?php echo $_DIMS['cste']['_LOCATION']; ?>&nbsp;</label>
							</td>
							<td width="50%">
								<input type="text" name="fck_evt_lieu" id="fck_evt_lieu" class="text" value="<?php echo $action->fields['lieu']; ?>" />
							</td>
						</tr>
							<?php
							}
							?>
					</table>
				</td>
				<td>
					<?php
					if($type == dims_const::_PLANNING_ACTION_EVT) {
					?>
					<table cellpadding="0" cellspacing="5" width="100%">
						<tr>
							<td align="right" width="30%" valign="top">
								<label for="evt_teaser"><?php echo $_DIMS['cste']['_DIMS_OBJECT_RESUME']; ?>&nbsp;</label>
							</td>
							<td width="70%">
								<textarea style="width: 300px;" name="fck_action_teaser" id="fck_action_teaser" rows="5" cols="30" class="text"><?php echo html_entity_decode($action->fields['teaser']); ?></textarea>
							</td>
						</tr>
					</table>
					<?php
					}
					?>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<p style="color:#ff0000;font-size:13px;"><img src="./common/img/important_small.png"/> <?php echo $_DIMS['cste']['_DIMS_LABEL_DONT_USE_WORD']; ?></p>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<table cellpadding="0" cellspacing="5" width="100%">
						<td align="right" valign="top"><?php echo $_DIMS['cste']['_DIMS_LABEL_DESCRIPTION']; ?></td>
						<td align="left">
							<?php
								dims_fckeditor('action_description',$action->fields['description'], '100%', '450', true);

								/*require_once(DIMS_APP_PATH . '/FCKeditor/fckeditor.php') ;

								$oFCKeditor = new FCKeditor('fck_') ;

								$basepath = dirname($_SERVER['HTTP_REFERER']); // compatible with proxy rewrite
								if ($basepath == '/') $basepath = '';

								$oFCKeditor->BasePath = "{$basepath}/FCKeditor/";

								// width & height
								$oFCKeditor->Width='100%';
								$oFCKeditor->Height='200';

								$oFCKeditor->Config['CustomConfigurationsPath'] = "{$basepath}/modules/system/fckeditor/fckconfig.js"  ;
								//$oFCKeditor->Config['ToolbarLocation'] = 'Out:xToolbar' ;
								$oFCKeditor->Config['SkinPath'] = "{$basepath}/modules/system/fckeditor/skins/default/" ;
								$oFCKeditor->Config['EditorAreaCSS'] = "{$basepath}/modules/system/fckeditor/fck_editorarea.css" ;
								$oFCKeditor->Config['BaseHref'] = "http://{$_SERVER['HTTP_HOST']}{$basepath}/";
								$oFCKeditor->Value= ();
								$oFCKeditor->Create();*/
							?>
						</td>
					</tr>
					</table>
				</td>
			</tr>
			</table>
			<?php
				echo '<div style="witdh:450px;float:right;">';

				echo dims_create_button($_DIMS['cste']['_DIMS_BACK'],'./common/img/go-previous.png','javascript:change_menu(0);','','width:90px;float:left;');

				if($type == dims_const::_PLANNING_ACTION_RDV)
					echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"./common/img/save.gif","javascript:if (!business_verif_action(document.form_action)) {alert('Vous devez sélectionner un client et un dossier');return(false)}","enreg","width:150px");
				else
					echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"./common/img/save.gif","javascript:form_action.submit();","enreg","width:100px;float:left;");

				if($type == dims_const::_PLANNING_ACTION_RCT) {
					echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_NEXT_MILESTONE'],'./common/img/go-next.png','javascript:change_menu(3);','','width:110px;float:left;');
				} elseif($action->fields['typeaction'] == '_DIMS_PLANNING_FAIR' || $action->fields['typeaction'] == '_DIMS_PLANNING_FAIR_STEPS') {
					echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_NEXT_MILESTONE'],'./common/img/go-next.png','javascript:change_menu(5);','','width:110px;float:left;');
				}
				else {
					echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_NEXT_MILESTONE'],'./common/img/go-next.png','javascript:change_menu(2);','','width:110px;float:left;');
				}
				echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_CANCEL'],'./common/img/delete.png',"location.href='admin.php'",'cancel1','width:90px;float:right;');
				echo '</div>';
			?>
	</div>
	<div id="block_content2" style="display:none;width:99%;float:left;margin-top:10px;margin-left:10px;margin-right:10px;">
		<?php
		//BLOC DE DESCRIPTION
		if($type == dims_const::_PLANNING_ACTION_RDV) {

			$_SESSION['dims']['planning']['currentactionusers']=array();
			$_SESSION['dims']['planning']['currentactionusersPart']=array(); // type de participation courante

			$_SESSION['dims']['planning']['currentactionusers'][$_SESSION['dims']['userid']]=$_SESSION['dims']['userid'];
			if (isset($action->utilisateurs)) $_SESSION['dims']['planning']['currentactionusers']=$action->utilisateurs;

			// traitement par défaut des user sélectionnés =>
			// si action existante il faut remonter les données
			if ($action->fields['id']>0) {
				$params = array();
				$params[':idaction'] = array('type' => PDO::PARAM_INT, 'value' => $action->getId());
				$res=$db->query("select user_id,participate from dims_mod_business_action_utilisateur where action_id=:idaction and user_id in (".$db->getParamsFromArray($_SESSION['dims']['planning']['currentactionusers'], 'iduser', $params).")", $params);

				if ($db->numrows($res)>0) {
					while ($f=$db->fetchrow($res)) {
							$_SESSION['dims']['planning']['currentactionusersPart'][$f['user_id']]=$f['participate']; // current Participate
					}
				}
			}
			else {
				foreach ($_SESSION['dims']['planning']['currentactionusers'] as $id_user=>$value) {
					$_SESSION['dims']['planning']['currentactionusersPart'][$id_user]=1; // default Participate
				}
			}

			// zone de recherche
			echo "<div style=\"float:left;width:38%;height:230px;display:block;\">";
			if (!isset($nomsearch)) $nomsearch="";
			?>
			<input value="<? echo $nomsearch;?>" type="text" onkeyup="javascript:searchUserActionPlanning();" id="nomsearchplanning" name="nomsearchplanning" size="16">
			<img style="cursor: pointer;" alt="" onclick="javascript:searchUserActionPlanning();" src="./common/img/search.png" border="0">
			<div id="lst_planningtempuser" style="width:100%;overflow:auto;height:205px;display:block;float:left;">
			<?php

			$arraylist=array();
			require_once(DIMS_APP_PATH . "/modules/system/class_user.php");
			$dims_user= new user();
			$dims_user->open($_SESSION['dims']['userid']);

			$arraylist=$dims_user->getusersgroup($nomsearch,$_SESSION['dims']['planning']['currentworkspacesearch'],$_SESSION['dims']['planning']['currentprojectsearch'],$lstusers,'','dims_workspace_group.id_group>0');

			if (isset($_SESSION['business']['usersselected']) && !empty($_SESSION['business']['usersselected'])) $arraylist+=$_SESSION['business']['usersselected'];
			if (isset($_SESSION['dims']['planning']['currentusertemp']) && !empty($_SESSION['dims']['planning']['currentusertemp'])) $arraylist+=$_SESSION['dims']['planning']['currentusertemp'];

			$lstuserssel=array();
			if (!empty($_SESSION['dims']['planning']['currentactionusers'])) $lstuserssel+=$_SESSION['dims']['planning']['currentactionusers'];

			// on affiche par défaut les personnes sélectionnées et préselect
			if (!empty($arraylist)) {
				$params = array();
				$res=$db->query("select u.* from dims_user as u where id in (".$db->getParamsFromArray($arraylist, 'iduser', $params).")", $params);
				if ($db->numrows($res)>0) {
					echo "<table style=\"width:100%;\">";
					while ($f=$db->fetchrow($res)) {
						if (!in_array($f['id'],$lstuserssel)) {
							$icon="<img src=\"./data/users/icon_".str_replace("#","",$f['color']).".png\" alt=\"\" border=\"0\">";
							echo "<tr><td width=\"80%\">".$icon."&nbsp;".strtoupper(substr($f['firstname'],0,1)).". ".$f['lastname']."</td><td>";
							echo "<td><a href=\"javascript:void(0);\" onclick=\"updateUserActionFromSelectedPlanning('addActionUserPlanning',".$f['id'].");\"><img src=\"./common/img/add.gif\" border=\"0\"></a></td></tr>";
						}
					}
					echo "</table>";
				}
			}
			?>
			</div>
			<?php
			echo "</div>";
			echo "<div id=\"selectedusers\" style=\"float:left;width:60%;height:230px;display:block;overflow:auto;\">";
			echo "<span style=\"text-align:center;width:100%;\">".$_DIMS['cste']['_DIMS_LABEL_CONCERNED']."</span>";

			$lstusers=array();
			if (!empty($_SESSION['dims']['planning']['currentactionusers'])) $lstusers+=$_SESSION['dims']['planning']['currentactionusers'];
			if (!empty($lstusers)) {
				$params = array();
				$res=$db->query("select u.* from dims_user as u where id in (".$db->getParamsFromArray($lstusers, 'iduser', $params).")", $params);
				if ($db->numrows($res)>0) {
					echo "<table style=\"width:100%;\">";
					while ($f=$db->fetchrow($res)) {
						//calcul de l'icon
						$icon="<img src=\"./data/users/icon_".str_replace("#","",$f['color']).".png\" alt=\"\" border=\"0\">";
						echo "<tr><td width=\"5%\">
						<a href=\"javascript:void(0);\" onclick=\"updateUserActionFromSelectedPlanning('deleteSelActionUserPlanning',".$f['id'].");\"><img src=\"./common/img/delete.png\" border=\"0\"></td>";
						echo "<td  width=\"20%\">";
						// calcul si participe ou non
						if ($_SESSION['dims']['planning']['currentactionusersPart'][$f['id']]) {
							$chselected="selected=\"selected\"";
							$chunselected="";
						}
						else {
							$chselected="";
							$chunselected="selected=\"selected\"";
						}

						echo "<select onchange=\"updateUserActionFromSelectedPlanning('updatePartActionUserPlanning',".$f['id'].");\" name=\"part".$f['id'].".\">";
						echo "<option $chselected value=\"0\">".$_DIMS['cste']['_DIMS_PARTICIPATES']."</option>";
						echo "<option $chunselected value=\"1\">".$_DIMS['cste']['_DIMS_TOINFO']."</option>";
						echo "</select></td><td>";
						echo $icon."&nbsp;".strtoupper(substr($f['firstname'],0,1)).". ".$f['lastname']."</td></tr>";
					}
					echo "</table>";
				}
			}
			echo "</div>";
		}
		//Type : Evenement. Selection des contacts
		elseif($type == dims_const::_PLANNING_ACTION_EVT) {
			/**** Gestion des partenaires de l'evt ****/

			$_SESSION['dims']['planning']['currentactionpartner']=array();

			if (isset($action->partenaires))
				$_SESSION['dims']['planning']['currentactionpartner'] = $action->partenaires;
			// traitement par défaut des partenaire sélectionnés =>
			// si action existante il faut remonter les données
			if ($action->fields['id']>0 && !empty($_SESSION['dims']['planning']['currentactionpartner']))
			{
				$params = array();
				$params[':idaction'] = array('type' => PDO::PARAM_INT, 'value' => $action->getId());
				$res=$db->query("select tiers_id,participate FROM dims_mod_business_action_detail WHERE action_id=:idaction AND tiers_id in (".$db->getParamsFromArray($_SESSION['dims']['planning']['currentactionpartner'], 'idtiers', $params).")", $params);

				if ($db->numrows($res)>0)
					while ($f=$db->fetchrow($res))
						$_SESSION['dims']['planning']['currentactionpartner'][$f['tiers_id']]=$f['tiers_id']; // current Partner
			}

			echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_PARTNER'],'width:100%; margin-left:10px; margin-right:10px; float:left;');

			echo '<p></p>';

			// zone de recherche
			echo "<div style=\"float:left;width:38%;height:230px;display:block;\">";
			if (!isset($partsearch)) $partsearch="";
			?>
			<input value="<?php echo $partsearch;?>" type="text" onkeyup="javascript:searchPartnerActionPlanning();" id="partsearchplanning" name="partsearchplanning" size="16">
			<img style="cursor: pointer;" alt="" onclick="javascript:searchPartnerActionPlanning();" src="./common/img/search.png" border="0">
			<div id="lst_planningtemppartner" style="width:100%;overflow:auto;height:175px;display:block;float:left;"></div>
			<?php
			echo "</div>";
			echo "<div id=\"selectedpartner\" style=\"float:left;width:60%;height:230px;display:block;overflow:auto;\">";
			echo "<span style=\"text-align:center;width:100%;\">".$_DIMS['cste']['_DIMS_EVT_PARTNER']."</span>";

			$lstusers=array();
			if (!empty($_SESSION['dims']['planning']['currentactionpartner'])) $lstusers+=$_SESSION['dims']['planning']['currentactionpartner'];
			if (!empty($lstusers)) {
				$params = array();
				$res=$db->query("select t.* from dims_mod_business_tiers as t where id in (".$db->getParamsFromArray($lstusers, 'idtiers', $params).")", $params);
				if ($db->numrows($res)>0) {
					echo "<table style=\"width:100%;\">";
					while ($f=$db->fetchrow($res)) {
						//calcul de l'icon
						$icon = "<img src=\"./common/img/partenaire.png\" alt=\"\" border=\"0\">";
						echo "<tr><td width=\"5%\">
						<a href=\"javascript:void(0);\" onclick=\"updatePartnerActionFromSelectedPlanning('deleteSelActionPartnerPlanning',".$f['id'].");\"><img src=\"./common/img/delete.png\" border=\"0\"></td><td>";
						echo $icon."&nbsp;".$f['intitule']."</td></tr>";
					}
					echo "</table>";
				}
			}
			echo "</div>";

			echo $skin->close_simplebloc();
		}
		//Type : Rencontre. Selection des contacts
		elseif($type == dims_const::_PLANNING_ACTION_RCT) {

			$_SESSION['dims']['planning']['currentactionusers']=array();
			$_SESSION['dims']['planning']['currentactionusersPart']=array(); // type de participation courante

			//$_SESSION['dims']['planning']['currentactionusers'][$_SESSION['dims']['userid']]=$_SESSION['dims']['userid'];
			if (isset($action->utilisateurs))
				$_SESSION['dims']['planning']['currentactionusers']=$action->utilisateurs;

			// traitement par défaut des user sélectionnés =>
			// si action existante il faut remonter les données
			if ($action->fields['id']>0 && !empty($_SESSION['dims']['planning']['currentactionusers']))
			{
				$params = array();
				$params[':idaction'] = array('type' => PDO::PARAM_INT, 'value' => $action->fields['id']);
				$res=$db->query("select contact_id,participate FROM dims_mod_business_action_detail WHERE action_id=:idaction AND contact_id in (".$db->getParamsFromArray($_SESSION['dims']['planning']['currentactionusers'], 'idcontact', $params).")", $params);

				if ($db->numrows($res)>0)
				{
					while ($f=$db->fetchrow($res))
					{
						$_SESSION['dims']['planning']['currentactionusersPart'][$f['contact_id']]=$f['participate']; // current Participate
					}
				}
			}

			// zone de recherche
			echo "<div style=\"float:left;width:38%;height:230px;display:block;\">";
			if (!isset($nomsearch)) $nomsearch="";
			?>
			<input value="<? echo $nomsearch;?>" type="text" onkeyup="javascript:searchUserActionPlanning();" id="nomsearchplanning" name="nomsearchplanning" size="16">
			<img style="cursor: pointer;" alt="" onclick="javascript:searchUserActionPlanning();" src="./common/img/search.png" border="0">
			<div id="lst_planningtempuser" style="width:100%;overflow:auto;height:105px;display:block;float:left;">
			</div>
			<?php
			echo "</div>";
			echo "<div id=\"selectedusers\" style=\"float:left;width:60%;height:230px;display:block;overflow:auto;\">";
			echo "<span style=\"text-align:center;width:100%;\">".$_DIMS['cste']['_DIMS_EVT_CONTACT_PARTICIPATE']."</span>";

			$lstusers=array();
			if (!empty($_SESSION['dims']['planning']['currentactionusers'])) $lstusers+=$_SESSION['dims']['planning']['currentactionusers'];
			if (!empty($lstusers)) {
				$params = array();
				$res=$db->query("select c.* from dims_mod_business_contact as c where id in (".$db->getParamsFromArray($lstusers, 'idcontact', $params).")", $params);
				if ($db->numrows($res)>0) {
					echo "<table style=\"width:100%;\">";
					while ($f=$db->fetchrow($res)) {
						//calcul de l'icon
						$icon="<img src=\"./data/users/icon_EFEFEF.png\" alt=\"\" border=\"0\">";
						echo "<tr><td width=\"5%\">
						<a href=\"javascript:void(0);\" onclick=\"updateUserActionFromSelectedPlanning('deleteSelActionUserPlanning',".$f['id'].");\"><img src=\"./common/img/delete.png\" border=\"0\"></td>";
						echo "<td  width=\"20%\">";
						// calcul si participe ou non
						if ($_SESSION['dims']['planning']['currentactionusersPart'][$f['id']] == 0) {
							$chselected_part ="selected=\"selected\"";
							$chselected_orga = '';
						}
						elseif ($_SESSION['dims']['planning']['currentactionusersPart'][$f['id']] == 1) {
							$chselected_part = '';
							$chselected_orga ="selected=\"selected\"";
						}

						echo '<select onchange="updateUserActionFromSelectedPlanning(\'updatePartActionUserPlanning\','.$f['id'].',\'part'.$f['id'].'\');" name="part'.$f['id'].'" id="part'.$f['id'].'">';
						echo "<option $chselected_part value=\"0\">".$_DIMS['cste']['_DIMS_MEETED']."</option>";
						echo "<option $chselected_orga value=\"1\">".$_DIMS['cste']['_DIMS_ACCOMPANY']."</option>";
						echo "</select></td><td>";
						echo $icon."&nbsp;".strtoupper(substr($f['firstname'],0,1)).". ".$f['lastname']."</td></tr>";
					}
					echo "</table>";
				}
			}
			echo "</div>";
		}
		echo '<div style="witdh:450px;float:right;">';
		if($type == dims_const::_PLANNING_ACTION_RDV)
			echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"./common/img/save.gif","javascript:if (!business_verif_action(document.form_action)) {alert('Vous devez sélectionner un client et un dossier');return(false)}","enreg","width:150px");
		else
			echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"./common/img/save.gif","javascript:form_action.submit();","enreg","width:150px;float:left;");

		if($action->fields['typeaction'] != '_DIMS_PLANNING_FAIR_STEPS') {
			if($type != dims_const::_PLANNING_ACTION_RDV)
				echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_NEXT_MILESTONE'],'./common/img/go-next.png','javascript:change_menu(3);','','width:110px;float:left;');
		}
		else {
			echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_NEXT_MILESTONE'],'./common/img/go-next.png','javascript:change_menu(1);','','width:110px;float:left;');
		}
		echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_CANCEL'],'./common/img/delete.png',"location.href='admin.php'",'cancel1','width:90px;float:right;');
		echo '</div>';
		?>
	</div>
<script language="javascript">
	var timersearch;

	function upKeysearch() {
		clearTimeout(timersearch);
		timersearch = setTimeout("execSearchLink()", 500);
	}

	function execSearchLink() {
		clearTimeout(timersearch);

		var nomsearch = dims_getelem('search_contact').value;
		var divtoaffich = dims_getelem('searchArea');

		if(nomsearch.length>=2) {
			dims_xmlhttprequest_todiv("admin.php", "op=search_ct&search="+nomsearch+"&id=<? echo $action->fields['id']; ?>", "", 'searchArea' );
			divtoaffich.style.display = "block";
		}
	}
</script>
	<div id="block_content3" style="display:none;width:99%;">
				<?php
				/**** Affichage des inscriptions ****/
				//echo '</td><td valign="top">';
				echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_EVT_CONTACT_REGISTER'],'width:100%; margin-left:10px; margin-right:10px; float:left;');


				echo '<table width="100%" cellpadding="4" cellspacing="0">
							<tr>
								<td width="50%" align="right">'.$_DIMS['cste']['_SEARCH'].'
								</td>
								<td align="left"><input type="text" id="search_contact" name="search_contact" onkeyup="javascript:upKeysearch()"/>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<div id="searchArea" style="overflow: auto; display: block; max-height: 130px;">
									</div>
								</td>
							</tr>
						</table>';

				$sql = 'SELECT
							*
						FROM
							dims_mod_business_event_inscription
						WHERE
							id_action = :idaction
						AND
							validate = 2';

				$ress = $db->query($sql, array(
					':idaction' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['currentaction']),
				));

				if ($db->numrows($ress)>0) {
						echo "<table style=\"width:100%;\">";
						$class = 'trl1';
						while($result = $db->fetchrow($ress)) {
								//calcul de l'icon
								$icon = '<img src="./common/img/contact.png" alt="" border="0">';
								echo '<tr class='.$class.'><td align="left">';
								echo $icon."&nbsp;&nbsp;".strtoupper(substr($result['firstname'],0,1)).". ".$result['lastname'].'</td></tr>';

								$class = ($class == 'trl1') ? 'trl2' : 'trl1';
						}
						echo "</table>";
				} else {
					echo "<span style='padding:20px;'>".$_DIMS['cste']['_DIMS_LABEL_NO_REGISTRATION']."</span>";
				}
				echo $skin->close_simplebloc();
				?>
	</div>
<?php if($action->fields['typeaction'] != '_DIMS_PLANNING_FAIR_STEPS') { ?>
<?
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
</form>
<? } ?>
<div id="block_content5" style="display:none;width:100%;">
	<?php

		if($typeaction == '_DIMS_PLANNING_FAIR_STEPS' && $action->fields['niveau'] == 2) {
			//on propose l'application d'un mod�le
			//on s�lectionne les mod�les disponibles
			$sql_m = "SELECT id, libelle FROM dims_mod_business_action WHERE is_model = 1 AND id_workspace = :idworkspace";

			$res_m = $db->query($sql_m, array(
				':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
			));
			if($db->numrows($res_m) > 0) {
				?>
				<table cellpadding="10">
				<tr>
					<td align="right" width="45%"><?php echo $_DIMS['cste']['_DIMS_FAIRS_USE_MODEL']; ?></td>
					<td align="left">
						<select class="select" name="action_is_model" onchange="javascript:document.form_action.submit();">
							<option value="0">--</option>
				<?
				while($list_mod = $db->fetchrow($res_m)) {
					$sel = '';
					if($action->fields['is_model'] == $list_mod['id']) $sel = 'selected="selected"';
					echo '<option value="'.$list_mod['id'].'" '.$sel.'>'.$list_mod['libelle'].'</option>';
				}
				?>
						</select>
					</td>
				</tr>
				</table>
				<?
					$tokenHTML = $token->generate();
					echo $tokenHTML;
				?>
				</form>
				<?
			}
		}

		if(!$action->new && $niveau == 2)
			require_once(DIMS_APP_PATH . '/modules/system/xml_planning_event_etap.php');

		if($type != dims_const::_PLANNING_ACTION_EVT) {
			echo "<div id=\"detaildisplay2\" style=\"text-align:center;\">";
			echo "<p style=\"margin:0px auto;text-align:center;width:180px;float:left;\">";
			echo dims_create_button("Envoyer un ticket","./common/img/go-down.png","document.getElementById('ticketdetail').style.visibility='visible';document.getElementById('ticketdetail').style.display='block';document.getElementById('detaildisplay2').style.visibility='hidden';document.getElementById('detaildisplay2').style.display='none';document.getElementById('detailhide2').style.visibility='visible';document.getElementById('detailhide2').style.display='block';document.getElementById('dims_ticket_message').focus();","","");
			echo "</p></div><div id=\"detailhide2\"  style=\"text-align:center;visibility:hidden;display:none;\">";
			echo "<p style=\"margin:0px auto;text-align:center;width:180px;float:left;\">";
			echo dims_create_button("Pas de ticket","./common/img/go-up.png","document.getElementById('ticketdetail').style.visibility='hidden';document.getElementById('ticketdetail').style.display='none';document.getElementById('detaildisplay2').style.visibility='visible';document.getElementById('detaildisplay2').style.display='block';document.getElementById('detailhide2').style.visibility='hidden';document.getElementById('detailhide2').style.display='none';document.getElementById('dims_ticket_message').value='';","","");
			echo "</p></div></div>";

			echo "<div id=\"ticketdetail\" style=\"visibility:hidden;display:none;clear:both;\">";
				?>
				<div style="padding:4px;">
				<?php
				require_once DIMS_APP_PATH . '/include/functions/tickets.php';
				dims_tickets_selectusers(true,array(),370);
				?>
				</div>
				<?php
			echo "</div>";
		}

		echo '<div style="witdh:450px;float:right;">';

		//echo dims_create_button($_DIMS['cste']['_DIMS_BACK'],'./common/img/go-previous.png','javascript:change_menu(1);','','width:90px;float:left;');

		//echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"./common/img/save.gif","javascript:form_action.submit();","enreg","width:100px;float:left;");

		//echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_CANCEL'],'./common/img/delete.png',"location.href='admin.php'",'cancel1','width:90px;float:right;');
		echo '</div>';

		?>
</div>

<div id="block_content6" style="display:none;width:99%;">
	<?php
		if($type == dims_const::_PLANNING_ACTION_EVT) {

			if(isset($_FILES) && !empty($_FILES)) {

				$path_folder = DIMS_APP_PATH . '/data/event_file/';

				if(!file_exists($path_folder)) {
					$last_path	= '';
					$array_path = explode('/', $path_folder);

					foreach($array_path as $folder)	{
						if(!empty($folder))	{
							$path = $last_path.'/'.$folder;
							if(!file_exists($path))	{
								mkdir($path);
							}

							$last_path = $path;
						}
					}
				}

				$extensions_valides = array( 'jpg' , 'jpeg' , 'gif' , 'png' );

				if(isset($_FILES['banner']['error']) && $_FILES['banner']['error'] == 0) {
					if (isset($_FILES['banner']['name'])) {
						$extension_upload = strtolower(  substr(  strrchr($_FILES['banner']['name'], '.')  ,1)	);
						if ( in_array($extension_upload,$extensions_valides) ) {
							$nom = $_FILES['banner']['name'];

							$nom = substr($nom, 0, strrpos($nom, '.'));
							$nom.= date('YmdHis').'.'.$extension_upload;
							$nom = $path_folder.$nom;

							if(move_uploaded_file($_FILES['banner']['tmp_name'],$nom)) {
								$action->fields['banner_path'] = $nom;
							}

							$nom = '';
						}
					}
				}

				if(isset($_FILES['preview']) && $_FILES['preview']['error'] == 0) {
					$extension_upload = strtolower(  substr(  strrchr($_FILES['preview']['name'], '.')	,1)  );
					if ( in_array($extension_upload,$extensions_valides) ) {
						$nom = $_FILES['preview']['name'];

						$nom = substr($nom, 0, strrpos($nom, '.'));
						$nom.= date('YmdHis').'.'.$extension_upload;
						$nom = $path_folder.$nom;

						if(move_uploaded_file($_FILES['preview']['tmp_name'],$nom)) {
							$action->fields['preview_path'] = $nom;
						}

						$nom = '';
					}

				}

				$extensions_valides = array( 'pdf' , 'xls' , 'docx' , 'doc', 'xlsx' );
				if(isset($_FILES['matchmaking']) && $_FILES['matchmaking']['error'] == 0) {
					$extension_upload = strtolower(  substr(  strrchr($_FILES['matchmaking']['name'], '.')	,1)  );
					if ( in_array($extension_upload,$extensions_valides) ) {
						$nom = $_FILES['matchmaking']['name'];

						$nom = substr($nom, 0, strrpos($nom, '.'));
						$nom.= date('YmdHis').'.'.$extension_upload;
						$nom = $path_folder.$nom;

						if(move_uploaded_file($_FILES['matchmaking']['tmp_name'],$nom)) {
							$action->fields['matchmaking_path'] = $nom;
						}

						$nom = '';
					}

				}
				$action->save();
				$action->open($_SESSION['dims']['currentaction']);
			}
			?>
			<form name='ress_front' action='admin.php?op=xml_planning_modifier_action&id=<?php echo $action->fields['id']; ?>&subtab=6' method='post' enctype="multipart/form-data" style="margin-left: 20px;">
			<?
				// Sécurisation du formulaire par token
				require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
				$token = new FormToken\TokenField;
				$token->field("MAX_FILE_SIZE",	"20097152");
				$token->field("banner");
				$token->field("preview");
				$token->field("matchmaking");
				$token->field("");
				$tokenHTML = $token->generate();
				echo $tokenHTML;
			?>
			<input type="hidden" name="MAX_FILE_SIZE" value="20097152" />
				<? echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_BANNER']); ?>
				<!--<p>
					<label for="banner">
						<?php
						//echo $_DIMS['cste']['_DIMS_LABEL_BANNER'];
						?>
					</label>
				</p>-->
				<p>
					<?php
					if(!empty($action->fields['banner_path'])) {
						$file_name = explode('/',$action->fields['banner_path']);
						foreach($file_name as $key => $part) {
							$dot = strpos($part,'.');
							if($dot > 0) $name_banner = $part;
						}
						echo '<a href="Javascript: void(0);" onclick="delete_banner('.$action->fields['id'].');"><img src="./common/img/delete.png" alt="'.$_DIMS['cste']['_DELETE'].'" /></a>&nbsp;<a href="'.$action->fields['banner_path'].'" target="blank">'.$name_banner.'</a>';
					}
					elseif(isset($_FILES['banner']['error']) && $_FILES['banner']['error'] != 0 && $_FILES['banner']['name'] != '') {
						echo '<p style="font-size:13px;color:#FF0000;">'.$_DIMS['cste']['_DIMS_LABEL_ERROR_TOLARGE_IMG'].'</p>
						<input type="file" name="banner" id="banner" />&nbsp;
							<input type="submit" value="'.$_DIMS['cste']['_DIMS_SEND'].'"/>';
					}
					else {
					?>
						<input type="file" name="banner" id="banner" />&nbsp;
							<input type="submit" value="<?php echo $_DIMS['cste']['_DIMS_SEND']; ?>" />
					<?php
						}
					?>
				</p>
				<?
					echo $skin->close_simplebloc();
					echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_EVT_DOC_ANNONCE'].' & '.$_DIMS['cste']['_DIMS_EVENT_LABEL_IMAGE_GALLERY']);
				?>
				<p>
					<label for="evt_doc">
						<?php
						echo $_DIMS['cste']['_DIMS_EVT_DOC_ANNONCE'];
						echo ' & '.$_DIMS['cste']['_DIMS_EVENT_LABEL_IMAGE_GALLERY'].' (Max 2Mo) : ';
						?>
					</label>
				</p>
				<p>
					<?php
					if ($action->fields['id']>0)
					{
						$id_module=$_SESSION['dims']['moduleid'];
						$id_object=dims_const::_SYSTEM_OBJECT_EVENT;
						$id_record=$action->fields['id'];
												require_once DIMS_APP_PATH.'include/functions/files.php';
						// collecte des fichiers deja insérés
						$lstfiles=dims_getFiles($dims,$id_module,$id_object,$id_record);
						if(count($lstfiles) < 4) {
							echo dims_createAddFileLink($id_module,$id_object,$id_record,'float:left;clear:both;');
						}
						echo '<div style="float:left;clear:both;width:100%;"><table>';
						if (!empty($lstfiles)) {
							echo "<tr class=\"trl1\">
								<td style=\"width:38%;padding-left:10px;\">".$_DIMS['cste']['_DOCS']."</td>
								<td style=\"width:25%;\">".$_DIMS['cste']['_DIMS_LABEL_MODIF_ON_FEM']."</td>
								<td style=\"width:37%;\">".$_DIMS['cste']['_DIMS_LABEL_FROM']."</td>
								<td></td>
							</tr>";
							$licolor=2;
							foreach ($lstfiles as $file) {
								if ($licolor==1) $licolor=2;
								else $licolor=1;
								$ldate = dims_timestamp2local($file['timestp_modify']);
								echo "<tr class=\"trl2\">
									<td style=\"cursor: default;padding-left:10px;\" onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;\">
									<a href=".$file['downloadlink']." title=\"Voir le document.\">".$file['name']."</a></td>
									<td onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;font-weight:normal;\">".$ldate['date']."</td>
									<td onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;font-weight:normal;\">".$file['firstname']." ".$file['lastname']."</td>
									<td onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;font-weight:normal;\"><a href=\"javascript:void(0);\" onclick=\"javascript:delete_doc('".$file['id']."')\"><img src=\"./common/img/delete.png\"/></a></td>
									</tr>";
							}
						}

						echo '</table></div>';
					}
					else
						echo $_DIMS['cste']['_DIMS_EVT_NO_DOC'];
					?>
				</p>
				<span style="float:left;width:100%;margin: 10px 0 0 0;">
					<label for="preview">
						<?php
						echo $_SESSION['cste']['_DIMS_EVENT_LABEL_ADERVTISE_DOC_PREVIEW'];
						?>
					</label>
				</span>
				<span style="float:left;width:100%;margin: 10px 0 10px 0;">
					<?php
					if(!empty($action->fields['preview_path'])) {
						$file_name = explode('/',$action->fields['preview_path']);
						foreach($file_name as $key => $part) {
							$dot = strpos($part,'.');
							if($dot > 0) $name_prev = $part;
						}
						echo '<a href="Javascript: void(0);" onclick="delete_preview('.$action->fields['id'].');"><img src="./common/img/delete.png" alt="'.$_DIMS['cste']['_DELETE'].'" /></a>&nbsp;<a href="'.$action->fields['preview_path'].'" target="blank">'.$name_prev.'</a>';
					}
					elseif(isset($_FILES['preview']['error']) && $_FILES['preview']['error'] != 0  && $_FILES['preview']['name'] != '') {
						echo '<p style="font-size:13px;color:#FF0000;">'.$_DIMS['cste']['_DIMS_LABEL_ERROR_TOLARGE_IMG'].'</p>
						<input type="file" name="preview" id="preview" />&nbsp;
							<input type="submit" value="'.$_DIMS['cste']['_DIMS_SEND'].'"/>';
					}
					else {
					?>
					<input type="file" name="preview" id="preview" />&nbsp;
					<input type="submit" value="<?php echo $_DIMS['cste']['_DIMS_SEND']; ?>" />
					<? } ?>
				</span>
				<?	echo $skin->close_simplebloc();
					echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_EVENT_MATCHMAKING']);
				?>
				<p>
					<?php
					if(!empty($action->fields['matchmaking_path'])) {
						$file_name = explode('/',$action->fields['matchmaking_path']);
						foreach($file_name as $key => $part) {
							$dot = strpos($part,'.');
							if($dot > 0) $name_match = $part;
						}
						echo '<a href="Javascript: void(0);" onclick="delete_match('.$action->fields['id'].');"><img src="./common/img/delete.png" alt="'.$_DIMS['cste']['_DELETE'].'" /></a>&nbsp;<a href="'.$action->fields['matchmaking_path'].'" target="blank">'.$name_match.'</a>';
					}
					elseif(isset($_FILES['matchmaking']['error']) && $_FILES['matchmaking']['error'] != 0 && $_FILES['matchmaking']['name'] != '') {
						echo '<p style="font-size:13px;color:#FF0000;">'.$_DIMS['cste']['_DIMS_LABEL_ERROR_TOLARGE_IMG'].'</p>
						<input type="file" name="matchmaking" id="matchmaking" />&nbsp;
							<input type="submit" value="'.$_DIMS['cste']['_DIMS_SEND'].'"/>';
					}
					else {
					?>
						<input type="file" name="matchmaking" id="matchmaking" />&nbsp;
							<input type="submit" value="<?php echo $_DIMS['cste']['_DIMS_SEND']; ?>" />
					<?php
						}
					?>
				</p>
				<?	echo $skin->close_simplebloc(); ?>
			</form>
			<?php
		}
		?>
</div>

<div style="width:100 %; clear: both; text-align: right;">
	<?php
	if(isset($action->fields['id']) && !empty($action->fields['id']) && ($action->fields['id_user']==$_SESSION['dims']['userid'] || dims_isadmin())){
		echo dims_create_button($_DIMS['cste']['_DELETE'],'./common/img/del.png',"javascript:document.form_action.op.value='xml_planning_action_supprimer';dims_confirmform(document.form_action,'Etes-vous certain ?');",'delete1','width:100px');
	}
	?>
</div>
