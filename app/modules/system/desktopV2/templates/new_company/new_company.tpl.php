<div class="creat_new_business">
	<div id="new_company_form">
		<span><?php echo $_SESSION['cste']['_SEARCH_BEFORE_CREATING']; ?></span>
		<div class="zone_search">
			<table style="width: 100%;">
				<tbody>
					<tr>
						<td>
							<span><? echo $_SESSION['cste']['COMPANIES_CONTACTS_LABEL']; ?></span>
						</td>
						<td>
							<div class="searchform">
								<form action="#" method="post" name="formsearch" id="formsearch_company">
									<?
										// Sécurisation du formulaire par token
										require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
										$token = new FormToken\TokenField;
										$token->field("button_search_x"); // Le nom des input de type image sont modifiés par les navigateur en ajoutant _x et _y
										$token->field("button_search_y");
										$token->field("editbox_search");
										$token->field("typesearch");
										$token->field("redirection");
										$token->field("action");
										$token->field("submenu");
										$token->field("mode");
										$token->field("activity_id");
										$token->field("activity_type_id");
										$token->field("activity_responsable");
										$token->field("activity_date_from");
										$token->field("activity_hour_from");
										$token->field("activity_mins_from");
										$token->field("activity_date_to");
										$token->field("activity_hour_to");
										$token->field("activity_mins_to");
										$token->field("activity_label");
										$token->field("activity_private");
										$token->field("activity_description");
										$token->field("company_intitule");
										$token->field("photo_path_company");
										$token->field("country");
										$token->field("city");
										$token->field("company_adresse");
										$token->field("company_codepostal");
										$token->field("company_telephone");
										$token->field("company_telecopie");
										$token->field("company_mel");
										$token->field("company_site_web");
										$token->field("opportunitySearch");
										$token->field("activity_address");
										$token->field("activity_cp");
										$token->field("activity_country_id");
										$token->field("activity_city_id");
										$token->field("documentSearch");
										$token->field("activity_alert_mode");
										$token->field("activity_alert_nb_period");
										$token->field("activity_alert_period");
										$token->field("activity_alert_date");
										$token->field("activity_alert_hour");
										$token->field("activity_alert_mins");

										$token->field("redir");
										$token->field("app_offer_id");
										$token->field("app_offer_label");

										$token->field("app_offer_alert_mode");
										$token->field("app_offer_alert_date");
										$token->field("app_offer_alert_hour");
										$token->field("app_offer_alert_mins");
										$token->field("app_offer_address");
										$token->field("app_offer_cp");
										$token->field("app_offer_country_id");
										$token->field("app_offer_city_id");
										$token->field("auto_add");
										$token->field("app_offer_private");

										$token->field("tiers_selected");
										$token->field("title");
										$token->field("lastname");
										$token->field("firstname");
										$token->field("mobile");
										$token->field("fax");
										$token->field("email");
										$token->field("comment");

										$tokenHTML = $token->generate();
										echo $tokenHTML;
									?>
									<span>
										<input id="button_image_search_company" type="image" class="button_search" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_gauche.png" name="button_search" style="float:left;" />
										<input autocomplete="off" onkeyup="javascript:searchOpportunityTiersContact('<?php echo addslashes($_SESSION['cste']['LOOKING_EXISTING_CONTACT']); ?>', '<?php echo _DESKTOP_TPL_PATH; ?>');" type="text" name="editbox_search" class="editbox_search" id="editbox_search_company" maxlength="80" placeholder="<?php echo $_SESSION['cste']['LOOKING_EXISTING_CONTACT']; ?>" />
										<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_droite.png" style="float:left;"  />
									</span>
								</form>
							</div>
						</td>
					</tr>
					<tr>
					<td></td>
					<td>
						<div class="radio_filters">
							<?php
							$cp = true;
							if(!isset($_SESSION['dims']['opportunity']['filter_search']) || (isset($_SESSION['dims']['opportunity']['filter_search']) && $_SESSION['dims']['opportunity']['filter_search']= 'cmp' )){
								$cp = true;
							}
							else{
								$cp = false;
							}
							?>
							<input type='radio' name="typesearch" id="radio_cmp" value="cmp" <?php if($cp) echo 'checked="checked"';?> onchange="javascript:searchOpportunityTiersContact('<?php echo addslashes($_SESSION['cste']['LOOKING_EXISTING_CONTACT']); ?>','<?php echo _DESKTOP_TPL_PATH; ?>');"/><label for="radio_cmp"><?php echo $_SESSION['cste']['_DIMS_LABEL_ENTERPRISES']; ?></label>
							<input type='radio' name="typesearch" id="radio_cts" value="cts" <?php if(!$cp) echo 'checked="checked"';?> onchange="javascript:searchOpportunityTiersContact('<?php echo addslashes($_SESSION['cste']['LOOKING_EXISTING_CONTACT']); ?>','<?php echo _DESKTOP_TPL_PATH; ?>');"/><label for="radio_cts"><?php echo $_SESSION['cste']['_DIMS_LABEL_CONTACTS']; ?></label>
							<input type='radio' name="typesearch" id="radio_grs" value="grs" <?php if(!$cp) echo 'checked="checked"';?> onchange="javascript:searchOpportunityTiersContact('<?php echo addslashes($_SESSION['cste']['LOOKING_EXISTING_CONTACT']); ?>','<?php echo _DESKTOP_TPL_PATH; ?>');"/><label for="radio_grs"><?php echo $_SESSION['cste']['_GROUP']; ?></label>

						</div>
					</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<div class="new_company_result" id="new_company_result"></div>

	<div id="zone_form_selected_company">
		<?php
		$tiers = new tiers();
		$tiers->init_description();
		$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/new_company/new_company_form.tpl.php');
		?>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		desactiveEnterSubmit('editbox_search_company');
		desactiveClicSubmit('button_image_search_company');
	});
</script>
