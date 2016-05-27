<?php

$id_creneau = dims_load_securvalue('id_creneau',_DIMS_NUM_INPUT, true, true, true);
$date = dims_load_securvalue('date_creneau', _DIMS_CHAR_INPUT, true, true, true);

$creneau = new elisath_creneau();
if($id_creneau > 0) {
	$creneau->open($id_creneau);
	$action = $creneau->getAction();
}
else {
	$creneau->init_description();
	$creneau->setugm();

	$creneau->setTimestpDeb(dims_local2timestamp($date));

	$action = $creneau->getAction();
}

$time_create = dims_timestp2local($creneau->fields['timestp_create']);
$time_modify = dims_timestp2local($creneau->fields['timestp_modify']);

$time_action = dims_timestp2local($creneau->fields['timestp_modify']);


$past = false;
if(!$creneau->new && time() > strtotime($creneau->getAction()->fields['datejour'].' '.$creneau->getAction()->fields['heuredeb']))
	$past = true;

$c_inactif = $creneau->fields['state']==elisath_creneau::STATE_INACTIF;

$already_reserved = false;
if(count($creneau->getUsersReservations(elisath_reservation::RESA_VALIDEE)) > 0)
	$already_reserved = true;

$delete_available = false;
if(count($creneau->getUsersReservations()) == 0)
	$delete_available = true;


?>
<form id="detail_creneau" name="detail_creneau" method="post" action="" class="dims_form_controlled">
	<?
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("op",			"save_creneau");
		$token->field("id_creneau",	$creneau->fields['id']);
		$token->field("creneau_activite_id");
		$token->field("action_datejour");
		$token->field("action_heuredeb");
		$token->field("action_heurefin");
		$token->field("creneau_trainer_id");
		$token->field("creneau_placemax");
		$token->field("creneau_place_web");
		$token->field("creneau_prix");
		$token->field("creneau_label");
		$token->field("creneau_description");
		$tokenHTML = $token->generate();
		echo $tokenHTML;
	?>
	<input type="hidden" name="op" value="save_creneau" />
	<input type="hidden" name="id_creneau" value="<?php echo $creneau->fields['id']; ?>" />
	<table class="haut_creneau">
		<tr>
			<td rowspan="2" style="width: 49px;">
				<img src="./common/modules/elisath/img/type_act.png">
			</td>
			<td>
				<?php
				if(!$creneau->new) {
					?>
					<h2>Créneau #<?php echo $creneau->fields['id']; ?> - <?php echo $creneau->fields['label']; ?></h2>
				<?php
				}
				else {
					?>
					<h2>Nouveau Créneau</h2>
					<?php
				}
				?>
			</td>
		</tr>
		<tr>
			<td class="creneau_maj" colspan="2">
				<?php
				if(!$creneau->new) {
					?>
					Créé le <?php echo $time_create['date']; ?> - Modifié le <?php echo $time_modify['date']; ?>
					<?php
					if(!dims_isactionallowed(elisath_contexte::CREATE_UPDATE_CRENEAU)){
						echo '- <span class="interdiction">Vous n\'êtes pas autorisé à modifier la fiche de ce créneau</span>';
					}
					elseif ($past){
						echo '- <span class="interdiction">Vous ne pouvez plus modifier cette fiche (date antérieure)</span>';
					}
					else if ($c_inactif){
						echo '- <span class="creneau_cancel">Ce créneau a été désactivé</span>';
					}

					if(!empty($creneau->fields['repeat_id']))
						echo '- <span class="avertissement">Ce créneau est issue d\'une répétition</span>';

				}
				?>
			</td>
		</tr>
	</table>
	<table class="fermeture">
		<tr>
			<td rowspan="2" style="width: 49px; text-align: right;">
				<img src="./common/modules/elisath/img/fermer.png" onclick="Javascript: closeDetailCreneau();">
			</td>
		</tr>
	</table>
	<div class="title_actions">
		<h2>Actions</h2>
	</div>
	<table class="annul_imprim">
		<tr>
			<?php
			if(!$creneau->new && dims_isactionallowed(elisath_contexte::CREATE_UPDATE_CRENEAU) && !$past) {
				if(!$c_inactif) {
					?>
					<td class="icons">
						<img src="./common/modules/elisath/img/annuler.png">
					</td>
					<td class="icons_liens">
						<a href="Javascript: void(0);" onclick="dims_confirmlink('<?php echo $dims->getScriptEnv() ?>?op=cancel_session&id_session=<?php echo $creneau->fields['id']; ?>','Voulez-vous vraiment annuler ce créneau ?');">Annuler ce créneau</a>
					</td>
					<?php

				}
				else {
					?>
					<td class="icons">
						<img src="./common/modules/elisath/img/activer.png">
					</td>
					<td class="icons_liens">
						<a href="<?php echo $dims->getScriptEnv() ?>?op=activate_session&id_session=<?php echo $creneau->fields['id']; ?>">Ré-activer ce créneau</a>
					</td>
					<?php
				}

				if($delete_available){
					?>
					<td class="icons">
						<img src="./common/modules/elisath/img/poubelle.png">
					</td>
					<td class="icons_liens">
						<a href="Javascript: void(0);" onclick="dims_confirmlink('<?php echo $dims->getScriptEnv() ?>?op=delete_session&id_session=<?php echo $creneau->fields['id']; ?>','Voulez-vous vraiment supprimer ce créneau ?');">Supprimer ce créneau</a>
					</td>
					<?php
				}
			}
			?>
			<td class="icons">
				<img src="./common/modules/elisath/img/imprimer.png">
			</td>
			<td>
				<a id="imprimCreneau" href="javascript:popupPrintableCreneau(<?php echo $creneau->fields['id']; ?>);">Imprimer la fiche de ce créneau</a>
			</td>
		</tr>
	</table>
	<div class="title_actions">
		<h2>Planification - Détail du créneau</h2>
		<a href="#"><img class="icons_actions" src="./common/modules/elisath/img/deplier.png"></a>
	</div>

	<div class="detail_creneau">
		<table class="detail_gauche">
			<tr>
				<td class="title_detail">
					<label class="field_required" for="creneau_activite_id">Type :</label>
				</td>
				<td colspan="3">
					<select name="creneau_activite_id" id="creneau_activite_id" rel="requis" <?php if($creneau->new) echo 'onchange="changeActivite(this.value);"'; ?> <?php if($past || $c_inactif || $already_reserved || !dims_isactionallowed(elisath_contexte::CREATE_UPDATE_CRENEAU)) echo 'disabled="disabled"'; ?>>
					<option value="dims_nan">Veuillez Sélectionner un type d'activité</option>
					<?php
						$actiList = elisath_activite::getActiList(elisath_activite::STATE_ACTIF);
						foreach($actiList as $acti) {
							$sel = '';
							if($acti['id'] == $creneau->fields['activite_id'])
								$sel = ' selected="selected"';
							?>
							<option <?php echo $sel; ?> value="<?php echo $acti['id']; ?>"><?php echo $acti['label']; ?></option>
							<?php
						}
						?>
					</select>
					<div id="def_creneau_activite_id" class="dims_error_field" style="display:none;"></div>
				</td>
			</tr>
			<tr>
				<td class="title_detail">
					<label class="field_required" for="action_datejour">Date :</label>
				</td>
				<td colspan="3">
					<input type="text" readonly class="date datepicker" rel="requis" rev="date_jj/mm/yyyy" name="action_datejour" id="action_datejour" value="<?php echo date('d/m/Y',strtotime($action->fields['datejour'])); ?>" <?php if($past || $c_inactif || $already_reserved  || !dims_isactionallowed(elisath_contexte::CREATE_UPDATE_CRENEAU)) echo 'readonly'; ?> />
					<div id="def_action_datejour" class="dims_error_field" style="display:none;"></div>
				</td>
			</tr>
			<tr>
				<td class="title_detail">
					<label class="field_required" for="action_heuredeb">Début :</label>
				</td>
				<td class="dmy">
					<input type="text" class="date" name="action_heuredeb" id="action_heuredeb" rel="requis" rev="heure_hh:mm" value="<?php echo substr($action->fields['heuredeb'],0,5); ?>" <?php if($past || $c_inactif || $already_reserved  || !dims_isactionallowed(elisath_contexte::CREATE_UPDATE_CRENEAU)) echo 'readonly'; ?> />
					<div id="def_action_heuredeb" class="dims_error_field" style="display:none;"></div>
				</td>
				<td class="title_detail">
					<label class="field_required" for="action_heurefin">Fin :</label>
				</td>
				<td class="dmy">
					<input type="text" class="date" name="action_heurefin" id="action_heurefin" rel="requis" rev="heure_hh:mm" value="<?php echo substr($action->fields['heurefin'],0,5); ?>" <?php if($past || $c_inactif || $already_reserved  || !dims_isactionallowed(elisath_contexte::CREATE_UPDATE_CRENEAU)) echo 'readonly'; ?> />
					<div id="def_action_heurefin" class="dims_error_field" style="display:none;"></div>
				</td>
			</tr>
			<tr>
				<td class="title_detail">
					<label class="field_required" for="creneau_trainer_id">Entraineur :</label>
				</td>
				<td colspan="3">
					<?php
					$trainerList = elisath_user::getTrainerList(elisath_user::STATE_ACTIF);
					?>
					<select name="creneau_trainer_id" id="creneau_trainer_id" rel="requis" <?php if($past  || $c_inactif || !dims_isactionallowed(elisath_contexte::CREATE_UPDATE_CRENEAU)) echo 'disabled="disabled"'; ?> >
						<?php

						foreach($trainerList as $trainer) {
							$sel = '';
							if($trainer['id'] == $creneau->fields['trainer_id'])
								$sel = ' selected="selected"';
							?>
							<option <?php echo $sel; ?> value="<?php echo $trainer['id']; ?>"><?php echo $trainer['firstname'].' '.$trainer['lastname']; ?></option>
							<?php
						}
						?>
					</select>
					<div id="def_creneau_trainer_id" class="dims_error_field" style="display:none;"></div>
				</td>
			</tr>
			<tr>
				<td class="title_detail">
					<label class="field_required" for="creneau_placemax">Places :</label>
				</td>
				<td class="dmy">
					<input type="text" class="date" id="creneau_placemax" name="creneau_placemax" rel="requis" rev="number" value="<?php echo $creneau->fields['placemax']; ?>" <?php if($past  || $c_inactif || !dims_isactionallowed(elisath_contexte::CREATE_UPDATE_CRENEAU)) echo 'disabled="disabled"'; ?> />
					<div id="def_creneau_placemax" class="dims_error_field" style="display:none;"></div>
				</td>
				<td class="title_detail">
					<label class="field_required" for="creneau_place_web">dont web :</label>
				</td>
				<td class="dmy" colspan="2">
					<input type="text" class="date" id="creneau_place_web" name="creneau_place_web" rel="requis" rev="number" value="<?php echo $creneau->fields['place_web']; ?>" <?php if($past  || $c_inactif || !dims_isactionallowed(elisath_contexte::CREATE_UPDATE_CRENEAU)) echo 'disabled="disabled"'; ?> />
					<div id="def_creneau_place_web" class="dims_error_field" style="display:none;"></div>
				</td>
			</tr>
			<tr>
				<td class="title_detail">
					<label class="field_required" for="creneau_prix">Prix :</label>
				</td>
				<td class="dmy">
					<input type="text" class="date" id="creneau_prix" name="creneau_prix" rel="requis" rev="number" value="<?php echo $creneau->fields['prix']; ?>" <?php if($past || $already_reserved  || $c_inactif || !dims_isactionallowed(elisath_contexte::CREATE_UPDATE_CRENEAU)) echo 'disabled="disabled"'; ?> />
					<div id="def_creneau_prix" class="dims_error_field" style="display:none;"></div>
				</td>
				<td colspan="2">&nbsp;</td>
			</tr>
		</table>

		<table class="detail_droite">
			<tr>
				<td class="title_detail">
					<label class="field_required" for="creneau_label">Libellé :</label>
				</td>
				<td colspan="3">
					<input type="text" name="creneau_label" id="creneau_label" rel="requis" value="<?php echo $creneau->fields['label']; ?>" <?php if($past  || $c_inactif || !dims_isactionallowed(elisath_contexte::CREATE_UPDATE_CRENEAU)) echo 'disabled="disabled"'; ?> >
					<div id="def_creneau_label" class="dims_error_field" style="display:none;"></div>
				</td>
			</tr>
			<tr>
				<td class="title_detail_up">
					<span class="title_date">Supplément d'information :</span>
				</td>
				<td class="dmy" colspan="3">
					<textarea name="creneau_description" rows="5" cols="20" <?php if($past  || $c_inactif || !dims_isactionallowed(elisath_contexte::CREATE_UPDATE_CRENEAU)) echo 'disabled="disabled"'; ?>><?php echo $creneau->fields['description']; ?></textarea>
				</td>
			</tr>
			<tr>

			</tr>
		</table>
	</div>


	<div class="title_actions">
		<?php
		if($creneau->fields['place_web']>0){?>
			<h2>Réservations (<strong><span id="pl_restantes"><?php echo ($creneau->getPlacesDispoBorne()+$creneau->getPlacesDispoWeb()); ?></span></strong> place(s) restante(s) dont <strong><span id="pl_restantes_web"><?php echo $creneau->getPlacesDispoWeb(); ?></span></strong> sur le web)</h2>
		<?php
		}
		else{?>
			<h2>Réservations (<strong><span id="pl_restantes"><?php echo $creneau->getPlacesDispoBorne(); ?></span></strong> place(s) restante(s))</h2>
		<?php
		}
		?>
		<a href="#"><img class="icons_actions" src="./common/modules/elisath/img/deplier.png"></a>
	</div>
	<div id="content_reservation_guichet">
		<div id="reservation_guichet">
			<h3>Enregistrer une réservation :</h3>
			<table class="table_reservation_guichet">
				<tr>
					<td>
						<span class="sous_titre_filtres">Carte :</span>
						<input autocomplete="off" onkeyup="Javascript: suggest(this);" type="text" id="resa_carte" <?php if($past || $c_inactif || $creneau->new || !dims_isactionallowed(elisath_contexte::CREATE_UPDATE_CRENEAU) || !dims_isactionallowed(elisath_contexte::CREATE_RESERVATION)) echo 'disabled="disabled"'; ?>>
						<?php echo dims_create_button('OK', 'check', 'Javascript:reserveByCard('.$creneau->fields['id'].');'); ?>
					</td>
				</tr>
				<tr>
					<td>
						<div id="message-resa"></div>
					</td>
				</tr>
			</table>
		</div>
		<div id="table_reservation_enrg">
			<table>
				<tbody>
					<tr>
						<td class="cadre_tableau">Nom</td>
						<td class="cadre_tableau">Date</td>
						<td class="cadre_tableau">Sexe</td>
						<td class="cadre_tableau">Type résa.</td>
						<td class="cadre_tableau">Présence</td>
						<td class="cadre_tableau">Action</td>
					</tr>
					<?php
					$resaList = $creneau->getUsersReservations(elisath_reservation::RESA_VALIDEE);

					if(empty($resaList)) {
						?>
						<tr>
							<td colspan="6" class="cadre">Aucune réservation.</td>
						</tr>
						<?php
					}
					else {
						foreach($resaList as $resa) {
							?>
							<tr>
								<td class="cadre"><?php echo $resa['firstname'].' '.$resa['lastname']; ?></td>
								<td class="cadre"><?php $date_resa = dims_timestamp2local($resa['date_resa']); echo  $date_resa['date'].' - '.$date_resa['time'];?></td>
								<td class="cadre"><?php echo ($resa['sexe'] == elisath_compte::COMPTE_SEXE_M) ? 'H' : 'F'; ?></td>
								<td class="cadre">
									<?php
									switch($resa['type']) {
										case _DIMS_ELISATH_MODULE_BORNE:
											echo 'Borne';
											break;
										case _DIMS_ELISATH_MODULE_WEB:
											echo 'Web';
											break;
										case _DIMS_ELISATH_MODULE_GUICHET:
										echo 'Guichet';
										break;
									}
									?>
								</td>
								<td class="cadre">
									<?php
									$curJ = date('YmdHis');
									if($curJ < $creneau->getTimestpDeb()){
										$pastille = 'creneau_non_passe.png';
										$formated = 'Créneau non passé';
									}
									else
									{
										$z1 = $resa['timestp_zone1'];
										if(isset($z1))$formated1 = date('H:i:s', dims_timestamp2unix($z1));

										$z2 = $resa['timestp_zone2'];
										if(isset($z2))$formated2 = date('H:i:s', dims_timestamp2unix($z2));
										if($z1 == 0 && $z2 ==0){
											$pastille = "jamais_venu.png";
											$formated = 'Cet abonné ne s\'est pas rendu à la séance';
										}
										else if($z1 != 0 && $z2 == 0){
											$pastille = 'premier_tourniquet.png';
											$formated = 'Cet abonné est passé à '.$formated1.' dans la zone 1 mais n\'a pas participé à la séance';
										}
										else if($z1 != 0 && $z2 != 0){
											$pastille = 'deuxieme_tourniquet.png';
											$formated = 'Cet abonné a bien participé à la séance. Heures de passage : Zone 1 > '.$formated1.' - Zone 2 > '.$formated2 ;
										}
									}
									?>
									<img src="./common/modules/elisath/img/<?php echo $pastille; ?>" title="<?php echo $formated;?> "/>
								</td>
								<td class="cadre">
									<a href="mailto:<?php echo $resa['email']; ?>">
										<img src="./common/modules/elisath/img/email_petit.png">
									</a>

									<a href="Javascript: void(0);" onclick="Javascript: dims_confirmlink('Javascript: DetachResa(<?php echo $resa['reservation_id']; ?>, <?php echo $resa['reservation_source_id']; ?>, <?php echo $creneau->fields['id']; ?>);', 'Voulez vous annuler cette reservation ?');">
										<img src="./common/modules/elisath/img/cancel_petit.png">
									</a>
								</td>
							</tr>
							<?php
						}
					}
					?>
				</tbody>
			</table>
			<div style="clear:both;"></div>
		</div>
	</div>
	<div class="bloc_comment"></div>
	<div class="buttons">
		<?php echo dims_create_button('Ajouter un commentaire', 'comment', 'Javascript:addComment();'); ?>
		<?php echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_CANCEL'], 'cancel', 'Javascript: closeDetailCreneau();'); ?>
		<?php echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'], '', "javascript:if(dims_controlform('detail_creneau', 'error_global', 'Veuillez contrôler les valeurs renseignées') && checkTime('action_datejour', 'action_heuredeb', 'action_heurefin', ".(($past) ? 'true' : 'false')."))document.detail_creneau.submit();else return false;"); ?>
	</div>
	<div id="error_global" style="float:right;padding-top: 4px;margin-right:4px;display:none;"></div>
</form>
<div class="title_actions">
	<h2>Activité / Commentaires</h2>
</div>
<div id="table_activ_comm">
	<?php
		$historic = $creneau->getHistoricActions();
		foreach($historic as $activity){
			?>
				<div class="activity">
					<div class="changes">
						<div class="liste">
							<ul>
							<?php

								foreach($activity->getChanges() as $update){
									$field = $update->getFieldName();
									switch($field)
									{
										case 'trainer_id':
											$before_user = new elisath_user();
											$before_user->open($update->getPreviousValue());
											$b_user = $before_user->getUser();
											$bef = $b_user->fields['firstname']." ".$b_user->fields['lastname'];

											$after_user = new elisath_user();
											$after_user->open($update->getNextValue());
											$a_user = $after_user->getUser();
											$aft = $a_user->fields['firstname']." ".$a_user->fields['lastname'];
										break;
										case 'activite_id':
											$before_activite = new elisath_activite();
											$before_activite->open($update->getPreviousValue());
											$bef = $before_activite->fields['label'];

											$after_activite = new elisath_activite();
											$after_activite->open($update->getNextValue());
											$aft = $after_activite->fields['label'];
										break;
										case 'state':

											$bef = ($update->getPreviousValue()==elisath_creneau::STATE_ACTIF)?'Actif':'Inactif';
											$aft = ($update->getNextValue()==elisath_creneau::STATE_ACTIF)?'Actif':'Inactif';
										break;
										case 'timestp_deb':
										case 'timestp_fin':
											$b_date = dims_timestp2local($update->getPreviousValue());
											$a_date = dims_timestp2local($update->getNextValue());
											$bef = ($update->getPreviousValue()!=0)?($b_date['date'].' - '.$b_date['time']):'vide';
											$aft = ($update->getNextValue()!=0)?($a_date['date'].' - '.$a_date['time']):'vide';
										break;
										default:
											$bef = $update->getPreviousValue();
											$aft = $update->getNextValue();
										break;
									}
									echo "<li><span class=\"field\">".$creneau->matrice[$update->getFieldName()]['label']."</span> est passé(e) de <span class=\"before\">".(($bef!='')?$bef:'vide')."</span> à <span class=\"after\">".$aft."</span></li>";
								}
							?>
							</ul>
						</div>
						<?php
						$com = $activity->getComment();
						if(!empty($com))
						{
							?>

						<div class="commentaire">
							<div class="title"><p><img src="./common/modules/elisath/img/bulle.png"/>Commentaires</p></div>
							<div class="content"><?php echo $com; ?></div>
						</div>
						<?php
						}
						?>
					</div>
					<div class="infos_activity">
						<div class="activity_date">
							<?php
							$date = dims_timestp2local($activity->getDateAction());
							echo $date['date']. " - " .$date['time'];
							?>
						</div>
						<div class="activity_user">
							<?php
								if(!is_null($activity->getUserFirstName()) || !is_null($activity->getUserLastName()))
								{
									echo $activity->getUserFirstName(). ' ' .$activity->getUserLastName();
								}
							?>
						</div>
						<div style="clear:both;"></div>
					</div>
				</div>
			<?php
		}
	?>
</div>
<script type="text/Javascript" language="Javascript">
<?php
if(!$past && !$c_inactif && !$already_reserved  && dims_isactionallowed(elisath_contexte::CREATE_UPDATE_CRENEAU)) {
	?>
	$("#action_datejour").datepicker({
		buttonImage: './common/img/calendar/calendar.gif',
		buttonImageOnly: true,
		showOn: 'button',
		constrainInput: true,
		minDate: 0,
		defaultDate: 0,
		onClose: function(dateText, inst) {
			$( "#date_end_repeat" ).datepicker( "option", "minDate", this.value );
		}
	});
	<?php
}
?>

$("#detail_creneau").ready(function() {

	$('#resa_carte').keypress(function(event) {
		if (event.which == '13') {
			event.preventDefault();
			setTimeout('reserveByCard(<?php echo $creneau->fields['id']; ?>);',100);
		}
	});

	$('#carte_submit').click(function() {
		reserveByCard(<?php echo $creneau->fields['id']; ?>);
	});
});


//fonction de contrôle d'un champs d'un formulaire dims après perte du focus -----
$('form.dims_form_controlled').ready(function(){
		dims_form_submitted = false;
		full_error = false;
	$('form.dims_form_controlled input').blur(valideField);
});


</script>
