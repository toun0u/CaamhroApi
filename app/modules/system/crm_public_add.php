<?php

$tabscriptenv = "$scriptenv?cat="._BUSINESS_CAT_CONTACT;

// par defaut a zero si no definit
$case = dims_load_securvalue('case',dims_const::_DIMS_NUM_INPUT,true,true);

$c_tabs[0]['title'] = $_DIMS['cste']['_DIMS_LABEL_CONT_CREATE'];
$c_tabs[0]['url'] = "$tabscriptenv&action="._BUSINESS_TAB_CONTACTSTIERS."&part="._BUSINESS_TAB_CONTACTSTIERS."&case=1&id_from=".$user->fields['id_contact']."&type_from=cte";
$c_tabs[0]['icon'] = "./common/templates/backoffice/dims/media/contact64.png";
$c_tabs[0]['width'] = 200;
$c_tabs[0]['position'] = 'left';

$c_tabs[1]['title'] = $_DIMS['cste']['_DIMS_LABEL_ENT_CREATE'];
$c_tabs[1]['url'] = "$tabscriptenv&action="._BUSINESS_TAB_CONTACTSTIERS."&part="._BUSINESS_TAB_CONTACTSTIERS."&case=2&id_from=".$user->fields['id_contact']."&type_from=ent";
$c_tabs[1]['icon'] = "./common/templates/backoffice/dims/media/factory_64.png";
$c_tabs[1]['width'] = 200;
$c_tabs[1]['position'] = 'right';

?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="95%" style="vertical-align:top;">
		<?
			switch($case) {
				default:
					echo '<table width="100%" cellpadding="10" cellspacing="10">
								<tr>
									<td style="vertical-align:top;" align="center">
										<table width="70%" cellpadding="10" cellspacing="10">
											<tr>
												<td>';

					echo $skin->create_toolbar($c_tabs);
					echo						'</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>';
				break;
				case 1:
					// initialisation
					$contact=new contact();
					$contact_id = dims_load_securvalue('contact_id',dims_const::_DIMS_NUM_INPUT,true,true);
					if ($contact_id>0) {
						$contact->open($contact_id);
						$_SESSION['business']['contact_id']=$contact_id;
					}
					else {
						$contact->init_description(); // met les champs a vides
						unset($_SESSION['business']['contact_id']);
					}
										unset($_SESSION['dims']['crm_newcontact_actionform']);
										unset($_SESSION['dims']['crm_newcontact_saveredirect']);

					require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_form.php'); //_addct
					break;
				case 2:
					$ent = new tiers();
					$ent_id = dims_load_securvalue('id_ent',dims_const::_DIMS_NUM_INPUT,true,true);
					if ($ent_id>0) {
						$ent->open($ent_id);
						$_SESSION['business']['tiers_id']=$ent_id;
					}
					else {
						$ent->init_description(); // met les champs a vides
						unset($_SESSION['business']['tiers_id']);
					}


					require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_form.php');//contact_add_entreprise
					break;
			}
		?>
		</td>
	</tr>
</table>
