<script language="JavaScript" type="text/JavaScript">
	function validEtape(idetape) {
		dims_xmlhttprequest("admin.php","dims_mainmenu=events&submenu=<?php echo dims_const::_DIMS_SUBMENU_EVENT; ?>&action=valid_etape&id_etape="+idetape);
		document.location.reload();
	}

	function validEtapeFairs(idetape) {
		dims_xmlhttprequest("admin.php","dims_mainmenu=events&submenu=<?php echo dims_const::_DIMS_SUBMENU_EVENT; ?>&action=valid_etape_fairs&id_etape="+idetape);
		document.location.reload();
	}

	function unvalidEtapeFairs(idetape) {
		dims_xmlhttprequest("admin.php","dims_mainmenu=events&submenu=<?php echo dims_const::_DIMS_SUBMENU_EVENT; ?>&action=annule_etape_fairs&id_etape="+idetape);
		document.location.reload();
	}

	function unvalidEtape(idetape) {
		dims_xmlhttprequest("admin.php","dims_mainmenu=events&submenu=<?php echo dims_const::_DIMS_SUBMENU_EVENT; ?>&action=annule_etape&id_etape="+idetape);
		document.location.reload();
	}

	function saveDocProvenance(idfile, idetape) {
		var val = document.getElementById("doc_provenance_"+idfile).options[document.getElementById("doc_provenance_"+idfile).selectedIndex].value;
		dims_xmlhttprequest("admin.php","dims_mainmenu=events&submenu=<?php echo dims_const::_DIMS_SUBMENU_EVENT; ?>&action=save_doc_prov&id_file="+idfile+"&val="+val);
		document.location.reload();
	}

	function changeReceptionDoc(idfile, idetape) {
		dims_xmlhttprequest_todiv("admin.php", "dims_mainmenu=events&submenu=<?php echo dims_const::_DIMS_SUBMENU_EVENT; ?>&action=message_annule_doc&id_file="+idfile, '', "popup_valid");
	}

	function validateReceptionDoc(id_div, idfile, idetape) {
		var date_recept = document.getElementById(id_div).value;
		dims_xmlhttprequest("admin.php","dims_mainmenu=events&submenu=<?php echo dims_const::_DIMS_SUBMENU_EVENT; ?>&action=valid_doc_recept&id_file="+idfile+"&date_recept="+date_recept);
		document.location.reload();
	}

	function validateDoc(id_div, idfile, idetape) {
		if(dims_getelem(id_div))
			var date_valid = document.getElementById(id_div).value;
		else
			var date_valid = '';
		dims_xmlhttprequest("admin.php","dims_mainmenu=events&submenu=<?php echo dims_const::_DIMS_SUBMENU_EVENT; ?>&action=valid_document&id_file="+idfile+"&date_valid="+date_valid);
		document.location.reload();
	}

	function unvalidateDoc(idfile, idetape) {
		dims_xmlhttprequest("admin.php","dims_mainmenu=events&submenu=<?php echo dims_const::_DIMS_SUBMENU_EVENT; ?>&action=unvalid_document&id_file="+idfile);
		document.location.reload();
	}

	function validateInscriptionNiv2(id_insc) {
		dims_xmlhttprequest("admin.php","dims_mainmenu=events&submenu=<?php echo dims_const::_DIMS_SUBMENU_EVENT; ?>&action=valid_niv2&id_insc="+id_insc);
		document.location.reload();
	}

	function unvalidInscNiv2(id_insc) {
		dims_xmlhttprequest("admin.php","dims_mainmenu=events&submenu=<?php echo dims_const::_DIMS_SUBMENU_EVENT; ?>&action=unvalid_niv2&id_insc="+id_insc);
		document.location.reload();
	}

	function validPaiement(id_insc, date_f, date_p, id_etap) {
		dims_xmlhttprequest("admin.php","dims_mainmenu=events&submenu=<?php echo dims_const::_DIMS_SUBMENU_EVENT; ?>&action=valid_paiement&id_insc="+id_insc+"&id_etap="+id_etap+"&date_f="+date_f+"&date_p="+date_p);
		document.location.reload();
	}

	function unvalidPaiement(id_insc) {
		dims_xmlhttprequest("admin.php","dims_mainmenu=events&submenu=<?php echo dims_const::_DIMS_SUBMENU_EVENT; ?>&action=unvalid_paiement&id_insc="+id_insc);
		document.location.reload();
	}

</script>
<?php

	$id_evt = null;
	$id_insc = null;
	$nb_evt = 0;

	$id_evt = dims_load_securvalue('id_evt', dims_const::_DIMS_NUM_INPUT, true);
	$id_insc = dims_load_securvalue('id_insc', dims_const::_DIMS_NUM_INPUT, true);

	//Verification qu'il y a bien un id_evt
	if($id_evt != null AND !empty($id_evt) AND $id_insc != null AND !empty($id_insc))
	{
		$sql	= null;
		$tab_evt= array();
		$tab_ins= array();

		//Recherche de l'evt + infos insc liÃ©es (verification que l'evt appartient bien a l'user)
		$sql = 'SELECT
					a.id AS id_evt,
					a.typeaction,
					a.libelle,
					a.description,
					a.datejour,
					a.heuredeb,
					a.heurefin,
					a.timestp_modify,
					a.timestamp_release,
					a.supportrelease,
					a.rub_nl,
					a.allow_fo,
					a.target,
					a.teaser,
					a.lieu,
					a.prix,
					a.conditions,
					a.niveau,
					a.typeaction,
					ei.id AS id_insc,
					ei.id_contact,
					ei.validate,
					ei.lastname,
					ei.firstname,
					ei.address,
					ei.city,
					ei.postalcode,
					ei.country,
					ei.phone,
					ei.email,
					ei.company,
					ei.function,
					ei.paiement
				FROM
					dims_mod_business_action a
				INNER JOIN
					dims_user u
					ON
						u.id = a.id_user
				LEFT JOIN
					dims_mod_business_event_inscription ei
					ON
						ei.id_action = a.id
				WHERE
					a.id = :idevt
				AND
					ei.id = :idinsc

				ORDER BY
					ei.validate DESC';


				/*
				AND
					(
						u.id = '.$_SESSION['dims']['userid'].'
					OR
						a.id_organizer = '.$_SESSION['dims']['user']['id_contact'].'
					OR
						a.id_responsible = '.$_SESSION['dims']['user']['id_contact'].'
					)
				*/
		$ressource	= $db->query($sql, array(':idevt' => $id_evt, ':idinsc' => $id_insc) );

		//Si on a un evt + infos user
		if($db->numrows($ressource) == 1) {
			//utilise pour condition de l'affiche (Comprend id_evt bon et evt existant)
			$nb_evt = 1;
			while($info = $db->fetchrow($ressource))
			{
				//Construction du tableau rÃ©cpitulatif de l'evt
				$tab_evt['id_evt']				= $info['id_evt'];
				$tab_evt['libelle']				= $info['libelle'];
				$tab_evt['typeaction']			= $info['typeaction'];
				$tab_evt['description']			= $info['description'];
				$tab_evt['datejour']			= $info['datejour'];
				$tab_evt['heuredeb']			= $info['heuredeb'];
				$tab_evt['heurefin']			= $info['heurefin'];
				$tab_evt['timestp_modify']		= $info['timestp_modify'];
				$tab_evt['timestamp_release']	= $info['timestamp_release'];
				$tab_evt['supportrelease']		= $info['supportrelease'];
				$tab_evt['rub_nl']				= $info['rub_nl'];
				$tab_evt['allow_fo']			= $info['allow_fo'];
				$tab_evt['target']				= $info['target'];
				$tab_evt['teaser']				= $info['teaser'];
				$tab_evt['lieu']				= $info['lieu'];
				$tab_evt['prix']				= $info['prix'];
				$tab_evt['conditions']			= $info['conditions'];
				$tab_evt['niveau']				= $info['niveau'];

				//Construction du tableau de l'insc (Id_ins en clÃ© premier niveau)
				$tab_ins['id_insc']		= $info['id_insc'];
				$tab_ins['id_contact']	= $info['id_contact'];
				$tab_ins['validate']	= $info['validate'];
				$tab_ins['lastname']	= $info['lastname'];
				$tab_ins['firstname']	= $info['firstname'];
				$tab_ins['address']		= $info['address'];
				$tab_ins['city']		= $info['city'];
				$tab_ins['postalcode']	= $info['postalcode'];
				$tab_ins['country']		= $info['country'];
				$tab_ins['phone']		= $info['phone'];
				$tab_ins['email']		= $info['email'];
				$tab_ins['company']		= $info['company'];
				$tab_ins['function']	= $info['function'];
				$tab_ins['paiement']	= $info['paiement'];
			}
		}
		//dims_print_r($tab_evt);
		//dims_print_r($tab_ins);
	}

	//implicite > si id_evt est bon, et evt existant
	if($nb_evt == 1) {
		?>
		<script language="Javascript">
			function validate_inscription(id_inscript)
			{
				//dims_showcenteredpopup("",970,600,'dims_popup');
				dims_xmlhttprequest_todiv('admin.php', 'dims_mainmenu=events&submenu=<?php echo dims_const::_DIMS_SUBMENU_EVENT; ?>&action=verif_valid&id_evt=<?php echo $tab_evt['id_evt']; ?>&id_inscrip='+id_inscript, '', 'popup_valid');
			}
		</script>
		<div id="popup_valid" style="position: fixed; top: 5%; left: 25%; width: 50%;">
		</div>
		<?php
		//Type d'inscription; 1 : formulaire avec 1 niveau, 2 : formulaire a 2 niveau
		$form_niv = $tab_evt['niveau'];

		/** Mise en forme date */
		$tab_date_rel = dims_timestamp2local($tab_evt['timestamp_release']);
		$date_rel = $tab_date_rel['date'];

		$tab_date_mod = dims_timestamp2local($tab_evt['timestp_modify']);
		$date_mod = $tab_date_mod['date'];

		$date_evt = array();
		ereg('^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$',$tab_evt['datejour'],$date_evt);
		/***********************/

		echo '<div><div>';
			echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_EVENT'].': '.$tab_evt['libelle']);
				echo '<table style="margin: 10px 5px; border-collapse: collapse; width: 100%; text-align: justify;">';

					echo '<tr><th>';
						echo $_DIMS['cste']['_DIMS_LABEL_LABEL'];
					echo '</th><th>';
						echo $_DIMS['cste']['_TYPE'];
					echo '</th><th>';
						echo $_DIMS['cste']['_DIMS_DATE'];
					echo '</th><th>';
						echo $_DIMS['cste']['_DIMS_LABEL_HEURE_DEB_FIN'];
					echo '</th><th>';
						echo $_DIMS['cste']['_DIMS_LABEL_MODIF_ON_FEM'];
					echo '</th></tr>';

					echo '<tr><td valign="top">';
						echo $tab_evt['libelle'];
					echo '</td><td valign="top">';
						echo $_DIMS['cste'][$tab_evt['typeaction']];
					echo '</td><td valign="top">';
						echo $date_evt[3].'/'.$date_evt[2].'/'.$date_evt[1];
					echo '</td><td valign="top">';
						echo substr($tab_evt['heuredeb'],0,5).' &agrave; '.substr($tab_evt['heurefin'],0,5);
					echo '</td><td valign="top">';
						echo $date_mod;
					echo '</td></tr>';

				echo '</table>';
			echo dims_create_button($_DIMS['cste']['_DIMS_EVT_BACK_REGISTRATION'],'./common/img/undo.gif', 'location.href=\'admin.php?dims_mainmenu=events&submenu='.dims_const::_DIMS_SUBMENU_EVENT.'&action=adm_evt&id_evt='.$tab_evt['id_evt'].'\'');
			echo $skin->close_simplebloc();
		echo '</div>';

		echo '<div>';
			echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_REGISTRATION']);
			echo '<div style="float: left; width: 100%;">';
			echo '<table style="width: 100%; border-collapse: collapse; text-align: left;">
						<tr>
							<td align="center">';
						echo '<table style="width: 80%; border-collapse: collapse; text-align: left;">
								<tr>';
							echo '<th width="17%">'.$_DIMS['cste']['_DIMS_LABEL_NAME'].'</th>';
								echo '<td>';
									echo (!empty($tab_ins['firstname'])) ? $tab_ins['firstname'] : 'n/a';
									echo '&nbsp;';
									echo (!empty($tab_ins['lastname'])) ? $tab_ins['lastname'] : 'n/a';
								echo '</td></tr><tr>';
							echo '<th>'.$_DIMS['cste']['_DIMS_LABEL_ADDRESS'].'</th>';
								echo '<td>';
									echo (!empty($tab_ins['address']) && $tab_ins['address'] != '') ? $tab_ins['address'] : 'n/a';
								echo '</td>';
								echo '<th  width="10%">'.$_DIMS['cste']['_DIMS_LABEL_CITY'].'</th>';
									echo '<td>';
										echo (!empty($tab_ins['city'])) ? $tab_ins['city'] : 'n/a';
							echo	'</td></tr><tr>';

							echo '<th>'.$_DIMS['cste']['_DIMS_LABEL_CP'].'</th>';
								echo '<td>';
									echo (!empty($tab_ins['postalcode'])) ? $tab_ins['postalcode'] : 'n/a';
								echo '</td>';
								echo '<th>'.$_DIMS['cste']['_DIMS_LABEL_COUNTRY'].'</th>';
								echo '<td>';
									echo (!empty($tab_ins['country'])) ? $tab_ins['country'] : 'n/a';
								echo '</td></tr><tr>';
							echo '<th>'.$_DIMS['cste']['_PHONE'].'</th>';
								echo '<td>';
									echo (!empty($tab_ins['phone'])) ? $tab_ins['phone'] : 'n/a';
								echo '</td>';
								echo '<th>'.$_DIMS['cste']['_DIMS_LABEL_EMAIL'].'</th>';
								echo '<td>';
									if(!empty($tab_ins['email'])) {
										$mailto = $tab_ins['email'].'?subject=&eacute;v&eacute;nement%20'.$tab_evt['libelle'].'%20';

										if($tab_evt['niveau'] == 2 && $tab_ins['validate'] > 0) {
											$sql = 'SELECT
														e.label
													FROM
														dims_mod_business_event_etap e
													INNER JOIN
														dims_mod_business_event_etap_user eu
														ON
															eu.id_etape = e.id
													WHERE
														eu.id_ee_contact = :idcontact
													AND
														eu.valide_etape = 0
													ORDER BY
														eu.id ASC
													LIMIT 1';

											$result = $db->query($sql, array(':idcontact' => $tab_ins['id_contact']));
											if($db->numrows($result) > 0) {
												$etape = $db->fetchrow($result);
												$mailto .= '&eacute;tape%20'.$etape['label'];
											}
										}

										echo '<a href="mailto:'.$mailto.'">'.$tab_ins['email'].'</a>';
									}
									else
									{
										echo 'n/a';
									}
								echo '</td></tr><tr>';
							echo '<th>'.$_DIMS['cste']['_DIMS_LABEL_CONT_ENTACT'].'</th>';
								echo '<td>';
									echo (!empty($tab_ins['company'])) ? $tab_ins['company'] : 'n/a';
								echo '</td>';
								echo '<th>'.$_DIMS['cste']['_DIMS_LABEL_FUNCTION'].'</th>';
								echo '<td>';
									echo (!empty($tab_ins['function'])) ? $tab_ins['function'] : 'n/a';
								echo '</td></tr><tr>';
							echo '<th>'.$_DIMS['cste']['_INFOS_STATE'].'</th>';
								echo '<td colspan="3">';
									if($tab_ins['validate'] == -1)
									{
										//Invalide
										echo $_DIMS['cste']['_DIMS_LABEL_REGISTRATION_CANCELED'];
									}
									elseif($tab_ins['validate'] == 2)
									{
										//Valide totalement
										echo '<p style="font-size:14px;font-color:#FF0000;">'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_VALIDATED'].'</p>';
									}
									elseif($tab_ins['validate'] == 1 && $tab_evt['niveau'] == 2) {
										echo $_DIMS['cste']['_DIMS_LABEL_REGISTRATION_WAIT_FOR_2_2'];
									}
									else
									{
										//En attente
										echo $_DIMS['cste']['_DIMS_LABEL_REGISTRATION_WAIT'];
									}
								echo '</td>';

							if($tab_evt['niveau'] == 1)
							{
								echo '</tr><tr><th>'.$_DIMS['cste']['_DIMS_ACTIONS'].'</th><td>';

								if($tab_ins['validate'] != 2)
									echo '<a href="Javascript: void(0);" onclick="Javascript: validate_inscription('.$tab_ins['id_insc'].');">
										<img alt="'.$_DIMS['cste']['_DIMS_VALID'].'" src="./common/img/checkdo.png"/>
									</a>';

								if($tab_ins['validate'] != -1)
									echo '<a href="admin.php?dims_mainmenu=events&action=cancel_insc&id_evt='.$tab_evt['id_evt'].'&id_insc='.$tab_ins['id_insc'].'">
										<img alt="'.$_DIMS['cste']['_DELETE'].'" src="./common/img/delete.png"/>
									</a>';
								echo '</td>';
							}
							elseif($tab_evt['niveau'] == 2)
							{
								//validate = -1 -> inscription niv 1 (2 ??) annulée
								//validate = 0 -> aucune validation
								//validate = 1 -> validation niv1 dans cas ou event a 2 nivx
								//vlidate = 2 -> niveau 2 validé en entier
								//bouton de validation de niveau 1

								if($tab_ins['validate'] == 0 || $tab_ins['validate'] == '-1') {
									echo '</tr><tr><th>'.$_DIMS['cste']['_DIMS_ACTIONS'].'</th><td>';
									echo '	<a href="Javascript: void(0);" onclick="Javascript: validate_inscription('.$tab_ins['id_insc'].');">
												<img alt="'.$_DIMS['cste']['_DIMS_VALID'].'" src="./common/img/checkdo.png"/>
											</a>';
									echo '</td>';
								}
								if($tab_ins['validate'] == 2) {
										echo '</tr><tr><th>'.$_DIMS['cste']['_DIMS_ACTIONS'].'</th><td>';
										echo '	<a href="javascript:void(0);" onclick="javascript:unvalidInscNiv2(\''.$tab_ins['id_insc'].'\')">
													<img alt="'.$_DIMS['cste']['_DELETE'].'" src="./common/img/delete.png"/>
												</a>';
										echo '</td>';
								}
							}

							if(!empty($tab_ins['id_contact']))
							{
								echo '</tr><tr><th>'.$_DIMS['cste']['_DIMS_LABEL_FICHE_ATTACHED'].'</th>';

								echo '<td>';
									echo '<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat='._BUSINESS_CAT_CONTACT.'&action=adm_insc&dims_desktop=block&dims_action=public&action='._BUSINESS_TAB_CONTACT_FORM.'&contact_id='.$tab_ins['id_contact'].'" target="_BLANK">';
										echo $_DIMS['cste']['_DIMS_LABEL_CT_FICHE'];
									echo '</a>';
								echo '</td>';
							}

							echo '</tr>';
						echo '</table>
						</td>
					</tr>
				</table>';
			echo '</div>';

			echo $skin->close_simplebloc();
			if($tab_evt['niveau'] == 2 && $tab_ins['validate'] > 0 && $tab_evt['typeaction'] != '_DIMS_PLANNING_FAIR_STEPS') {
				require_once(DIMS_APP_PATH . '/modules/events/public_events_inscr_niv2.php');
			}
			elseif($tab_evt['niveau'] == 2 && $tab_ins['validate'] > 0 && $tab_evt['typeaction'] == '_DIMS_PLANNING_FAIR_STEPS') {
				require_once(DIMS_APP_PATH . '/modules/events/public_events_inscr_niv2_fair.php');
			}
	}
	else
		echo $_DIMS['cste']['_DIMS_LABEL_NO_EVENT_CORRESP'];

?>
