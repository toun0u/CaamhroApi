<?php
$dims = dims::getInstance();
$db = $dims->getDb();

if ($this->new) {
	$date_to = '';
}
else {
	if ($this->fields['datefin'] != '0000-00-00') {
		$a_dt = explode('-', $this->fields['datefin']);
		$date_to = $a_dt[2].'/'.$a_dt[1].'/'.$a_dt[0];
	}
	else {
		$date_to = '';
	}
}
?>

<div class="title_new_lead">
	<h1><?php echo $_SESSION['cste']['_SYSTEM_MANAGE_OPPORTUNITIES']; ?></h1>
</div>

<h2><?php echo $_SESSION['cste']['NEW_OPPORTUNITY']; ?></h2>

<div class="form_lead">
	<form name="f_lead" action="<?php echo dims::getInstance()->getScriptEnv(); ?>" method="post" enctype="multipart/form-data">
		<?
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("redirection");
			$token->field("action");
			$token->field("lead_id");
			$token->field("lead_status_id");
			$token->field("lead_tiers_id");
			$token->field("lead_partner_id");
			$token->field("lead_product_id");
			$token->field("lead_responsable");
			$token->field("lead_budget");
			$token->field("lead_date_to");
			$token->field("lead_libelle");
			$token->field("lead_description");
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
			$token->field("documentSearch");
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>
		<input type="hidden" id="redirection" name="redirection" value="0" />
		<input type="hidden" name="action" value="save" />
		<input type="hidden" name="lead_id" value="<?php echo $this->fields['id']; ?>" />

		<table class="w100 bb1">
			<tr>
				<td><h3>Description</h3></td>
				<td class="txtright">
					<img class="clickable" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/replier_menu.png" alt="Replier le bloc" onclick="javascript:$('#lead_general').slideToggle('fast',flip_flop($('#lead_general'),$(this),'<?php echo _DESKTOP_TPL_PATH; ?>'));" />
				</td>
			</tr>
		</table>
		<div id="lead_general">
			<fieldset>
				<table class="w100">
					<tr>
						<td class="vatop">
							<table>
								<tr>
									<td><label class="title" for="lead_status_id"><?php echo $_SESSION['cste']['STATUS']; ?></label></td>
									<td>
										<select class="w100" id="lead_status_id" name="lead_status_id" data-placeholder="Sléctionnez un statut">
											<option value="<?php echo dims_lead::STATUS_IN_PROGRESS; ?>" <?php if ($this->getStatus() == dims_lead::STATUS_IN_PROGRESS) echo 'selected="selected"'; ?>>En cours</option>
											<option value="<?php echo dims_lead::STATUS_LOST; ?>" <?php if ($this->getStatus() == dims_lead::STATUS_LOST) echo 'selected="selected"'; ?>>Perdu</option>
											<option value="<?php echo dims_lead::STATUS_ABANDONED; ?>" <?php if ($this->getStatus() == dims_lead::STATUS_ABANDONED) echo 'selected="selected"'; ?>>Abandonné</option>
											<option value="<?php echo dims_lead::STATUS_WON; ?>" <?php if ($this->getStatus() == dims_lead::STATUS_WON) echo 'selected="selected"'; ?>>Gagné</option>
										</select>
									</td>
								</tr>
								<tr>
									<td><label class="title" for="lead_tiers_id">Prospect / Client</label></td>
									<td>
										<select class="w100" id="lead_tiers_id" name="lead_tiers_id" data-placeholder="Sléctionnez un prospect / client">
											<option value=""></option>
											<?php
											// tous les tiers du workspace
											$rs = $db->query('SELECT id, intitule FROM dims_mod_business_tiers WHERE id_workspace = :idworkspace ORDER BY intitule', array(
												':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
											));
											while ($row = $db->fetchrow($rs)) {
												$sel = ($row['id'] == $this->fields['tiers_id']) ? ' selected="selected"' : '';
												echo '<option value="'.$row['id'].'"'.$sel.'>'.$row['intitule'].'</option>';
											}
											?>
										</select>
									</td>
								</tr>
								<tr>
									<td><label class="title" for="lead_partner_id">Partenaire</label></td>
									<td>
										<select class="w100" id="lead_partner_id" name="lead_partner_id" data-placeholder="Sélectionnez un partenaire">
											<option value=""></option>
											<?php
											// tous les tiers du workspace
											$rs = $db->query('SELECT id, intitule FROM dims_mod_business_tiers WHERE id_workspace = :idworkspace ORDER BY intitule', array(
												':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
											));
											while ($row = $db->fetchrow($rs)) {
												$sel = ($row['id'] == $this->fields['opportunity_partner_id']) ? ' selected="selected"' : '';
												echo '<option value="'.$row['id'].'"'.$sel.'>'.$row['intitule'].'</option>';
											}
											?>
										</select>
									</td>
								</tr>
								<tr>
									<td><label class="title" for="lead_product_id">Produit</label></td>
									<td>
										<select class="w100" id="lead_product_id" name="lead_product_id" data-placeholder="Sélectionnez un produit">
											<option value=""></option>
											<?php
											// tous les produits du workspace
											$rs = $db->query('SELECT id, libelle FROM dims_mod_business_produit WHERE id_workspace = :idworkspace ORDER BY libelle', array(
												':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
											));
											while ($row = $db->fetchrow($rs)) {
												$sel = ($row['id'] == $this->fields['opportunity_product_id']) ? ' selected="selected"' : '';
												echo '<option value="'.$row['id'].'"'.$sel.'>'.$row['libelle'].'</option>';
											}
											?>
										</select>
									</td>
								</tr>
								<tr>
									<td><label class="title" for="lead_responsable">Responsable</label></td>
									<td>
										<select class="w100" id="lead_responsable" name="lead_responsable" data-placeholder="Sélectionnez un responsable">
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
									<td><label class="title" for="lead_budget">Budget</label></td>
									<td><input type="text" id="lead_budget" name="lead_budget" value="<?php echo $this->fields['opportunity_budget']; ?>" /> €</td>
								</tr>
								<tr>
									<td><label class="title" for="lead_date_to">Echéance</label></td>
									<td>
										<input type="text" id="lead_date_to" name="lead_date_to" value="<?php echo $date_to; ?>" />
										<img style="vertical-align:bottom;" src="./common/img/calendar.png" alt="Date d'échéance" onclick="javascript:dims_calendar_open('lead_date_to', event);" />
									</td>
								</tr>
							</table>
						</td>
						<td class="vatop">
							<table>
								<tr>
									<td><label class="title" for="lead_libelle">Libellé</label></td>
									<td><input class="w100" type="text" id="lead_libelle" name="lead_libelle" value="<?php echo stripslashes($this->fields['libelle']); ?>" /></td>
								</tr>
								<tr>
									<td class="vatop"><label class="title" for="lead_description">Description</label></td>
									<td><textarea id="lead_description" name="lead_description"><?php echo $this->getDescriptionRaw(); ?></textarea></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</fieldset>
		</div>

		<table class="w100 bb1">
			<tr>
				<td><h3>Contacts impliqués</h3></td>
				<td class="txtright">
					<img id="contacts_bloc_img" class="clickable" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/deplier_menu.png" alt="Déplier le bloc" onclick="javascript:$('#lead_search_contact').slideToggle('fast',flip_flop($('#lead_search_contact'),$(this),'<?php echo _DESKTOP_TPL_PATH; ?>'));" />
				</td>
			</tr>
		</table>
		<div id="lead_search_contact" style="display: none;">
			<fieldset>
				<table class="w100">
					<tr>
						<td class="w200p vatop">
							<input class="w150 search-field" type="text" onkeyup="javascript:leadSearchContactKey($('#contactSearch').val(), '<?php echo _DESKTOP_TPL_PATH; ?>');" id="contactSearch" name="contactSearch" value="Recherchez un contact" />
							<a href="javascript:void(0);" onclick="javascript:leadSearchContact($('#contactSearch').val(), '<?php echo _DESKTOP_TPL_PATH; ?>');" title="Lancer la recherche"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/activity_loupe.png" alt="Recherchez un contact" /></a>
							<div id="searchContactResults"></div>
						</td>
						<td class="vatop" style="width:250px;">
							<div id="contentAddContact" name="contentAddContact" style="display:none;visibility:hidden;">
								<table cellspacing="10" cellpadding="0">
									<tbody>
										<tr>
											<td class="text" name="lastname">
												<? echo $_SESSION['cste']['_DIMS_LABEL_NAME']; ?>
											</td>
											<td>
												<input type="text" style="width: 98%;" id="lastname" name="lastname" value=""/>
											</td>
										</tr>
										<tr>
											<td class="text">
												<? echo $_SESSION['cste']['_FIRSTNAME']; ?>
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
												<? echo $_SESSION['cste']['_DIMS_LABEL_EMAIL']; ?>
											</td>
											<td>
												<input type="text" class="email" name="email" style="width: 98%;" value=""/>
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
											<td id="activity_rech_add_city_user">
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
													<input type="button" value="<? echo$_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>" onclick="javascript:leadAddUndoNewcontact();" />

														<input onclick="javascript:leadSaveNewContact();" type="button" value="<? echo $_SESSION['cste']['_DIMS_SAVE']; ?>" />
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</td>
						<td class="vatop bdleft">
							<div id="contactsList"></div>
						</td>
					</tr>
				</table>
			</fieldset>
		</div>

		<table class="w100 bb1">
			<tr>
				<td><h3>Documents associés</h3></td>
				<td class="txtright">
					<img class="clickable" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/deplier_menu.png" alt="Déplier le bloc" onclick="javascript:$('#lead_documents').slideToggle('fast',flip_flop($('#lead_documents'),$(this),'<?php echo _DESKTOP_TPL_PATH; ?>'));" />
				</td>
			</tr>
		</table>
		<div id="lead_documents" style="display: none;">
			<fieldset>
				<table class="w100">
					<tr>
						<td class="w200p vatop">
							<input class="w150 search-field" type="text" id="documentSearch" name="documentSearch" value="Recherchez un document" />
							<a href="javascript:void(0);" onclick="javascript:leadSearchDocument($('#documentSearch').val(), '<?php echo _DESKTOP_TPL_PATH; ?>');" title="Lancer la recherche"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/activity_loupe.png" alt="Recherchez un document" /></a>
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
		<p class="mt2 txtright">
			<input type="button" value="Enregistrer l'activité" onclick="javascript:$('#redirection').val(0);document.f_lead.submit();" />
			<span> <?php echo $_SESSION['cste']['_DIMS_OR']; ?> </span>
			<input type="button" value="Enregistrer l'activité et continuer" onclick="javascript:$('#redirection').val(1);document.f_lead.submit();" />
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
					'action' : 'lead_get_linked_objects',
					'lead_id_go' : <?php echo $this->fields['id_globalobject']; ?>
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
								'<td class="w20p txtcenter"><a href="javascript:void(0);" onclick="javascript:leadRemoveContact(' + data.contacts[i].c.id_globalobject + ');" title="Enlever ce contact"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/supprimer20.png" /></a></td></tr></table>');
						}
						$('#lead_search_contact').slideToggle('fast',flip_flop($('#lead_search_contact'),$('#contacts_bloc_img'),'<?php echo _DESKTOP_TPL_PATH; ?>'));
					}
					// documents
					if (data.docs.length) {
						for (i = 0; i < data.docs.length; i++) {
							$('#documentsList').append(
								'<table id="added_doc_' + data.docs[i].id_globalobject + '" class="w100 bb1"><tr>' +
								'<td class="w20p txtcenter"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/doc32.png" alt="' + data.docs[i].name + '" title="' + data.docs[i].name + '" /></td>' +
								'<td>' + data.docs[i].name + '</td>' +
								'<td class="w20p txtcenter"><a href="javascript:void(0);" onclick="javascript:preview_docfile(\'' + data.docs[i].md5id + '\');" title="Prévisualiser ce document"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/previsu.png" /></a></td>' +
								'<td class="w20p txtcenter"><a href="javascript:void(0);" onclick="javascript:leadRemoveDocument(' + data.docs[i].id_globalobject + ');" title="Enlever ce document"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/supprimer20.png" /></a></td></tr></table>');
						}
					}
				}
			});
			<?php
		}
		?>

		// plugin chosen sur les listes déroulantes
		$('#lead_status_id').chosen({ no_results_text: "Aucun résultat pour " });
		$('#lead_tiers_id').chosen({ no_results_text: "<div class=\"button_add_company\" style=\"float:right;color:#690;cursor:pointer;\"><img style=\"float:left;\" src=\"<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/add.png\" /><div style=\"float:right;margin-top:3px;\"><?php echo addslashes($_SESSION['cste']['ADD_IT_LE']); ?></div></div><?php echo addslashes($_SESSION['cste']['NO_RESULT']);?>" });
		$('#lead_partner_id').chosen({ no_results_text: "<div class=\"button_add_company\" style=\"float:right;color:#690;cursor:pointer;\"><img style=\"float:left;\" src=\"<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/add.png\" /><div style=\"float:right;margin-top:3px;\"><?php echo addslashes($_SESSION['cste']['ADD_IT_LE']); ?></div></div><?php echo addslashes($_SESSION['cste']['NO_RESULT']);?>" });
		$('#lead_product_id').chosen({ no_results_text: "<div class=\"button_add_product\" style=\"float:right;color:#690;cursor:pointer;\"><img style=\"float:left;\" src=\"<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/add.png\" /><div style=\"float:right;margin-top:3px;\"><?php echo addslashes($_SESSION['cste']['ADD_IT_LE']); ?></div></div><?php echo addslashes($_SESSION['cste']['NO_RESULT']);?>" });
		$('#lead_responsable').chosen({no_results_text: "Aucun résultat pour "});

		// ajout aux listes
		$('div.button_add_company').live('click',function(){
			$(this).die('click');
			addNewCompany('lead_tiers_id');
		});
		$('div.button_add_company').live('click',function(){
			$(this).die('click');
			addNewCompany('lead_partner_id');
		});
		$('div.button_add_product').live('click',function(){
			$(this).die('click');
			addNewProduct('lead_product_id');
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
	})
</script>
