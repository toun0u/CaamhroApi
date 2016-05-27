<?php
	require_once(DIMS_APP_PATH . '/modules/doc/class_docfile.php');
	echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_EVT_MANAGE_LEVEL_TWO']);
	//on va chercher les informations sur les étapes et leurs documents
	$sql =	'SELECT
				ee.*,
				eect.id AS id_ee_ct,
				eect.valide_etape,
				eect.date_validation_etape,
				efile.id AS id_file_etap,
				efile.id_doc AS id_file_doc,
				efile.label AS input_label,
				efile.content AS input_content,
				euser.id AS id_file_ct,
				euser.id_contact,
				euser.id_doc AS id_ct_doc,
				euser.valide,
				euser.id_doc_frontoffice,
				euser.provenance,
				euser.date_reception,
				euser.date_validation
			FROM
				dims_mod_business_event_etap ee
			LEFT JOIN
				dims_mod_business_event_etap_file efile
				ON
					ee.id = efile.id_etape
			LEFT JOIN
				dims_mod_business_event_etap_file_user euser
				ON
					ee.id = euser.id_etape
				AND
					euser.id_doc = efile.id
				AND
					euser.id_contact = :idcontact1
			LEFT JOIN
				dims_mod_business_event_etap_user eect
				ON
					ee.id = eect.id_etape
				AND
					eect.id_ee_contact = :idcontact2
			WHERE
				ee.id_action = :idevt
			ORDER BY
				ee.position,
				efile.id ASC,
				euser.date_reception DESC';

	$res=$db->query($sql, array(':idcontact1' => $tab_ins['id_contact'], ':idcontact2' => $tab_ins['id_contact'], ':idevt' => $tab_evt['id_evt']) );
	$nb_res = $db->numrows($res);
	if ($nb_res>0) {
		$id_etap_selected = 0;
		$class="trl1";

		$tab_etap = array();
		$tab_file = array();
		$cpt_etap_valid = 0;

		//Tableau des entrées de documents, et doc versionnés
		$tabInput = array();

		while ($value=$db->fetchrow($res)) {
			if($value['type_etape'] != 1) {
				$cpt_valid = 0;
				//construction des tableaux de donnees

				//donnees concernant les etapes
				$tab_etap[$value['id']]['id']			= $value['id'];
				$tab_etap[$value['id']]['id_action']	= $value['id_action'];
				$tab_etap[$value['id']]['label']		= $value['label'];
				$tab_etap[$value['id']]['position']		= $value['position'];
				$tab_etap[$value['id']]['description']	= $value['description'];
				$tab_etap[$value['id']]['paiement']		= $value['paiement'];
				$tab_etap[$value['id']]['type_etape']	= $value['type_etape'];
				//donnees concernant les etapes rattachees au contact courant
				if(isset($value['id_ee_ct']) && !empty($value['id_ee_ct'])) {
					$tab_etap[$value['id']]['id_ee_ct']			= $value['id_ee_ct'];
					$tab_etap[$value['id']]['valide_etape']		= $value['valide_etape'];
					$tab_etap[$value['id']]['date_valid_etape'] = $value['date_validation_etape'];
				}
				else {
					//si on ne recupère rien dans le left join, il faut initialiser les valeurs

					//require_once(DIMS_APP_PATH . '/modules/events/class_action_etap_ct.php');
					//on verifie d'abord si les etapes existent pour le contact courant (cela evite les doublons)
					$sql_eect = "SELECT id FROM dims_mod_business_event_etap_user WHERE id_etape = :idetape AND id_ee_contact = :idcontact";
					$res_eect = $db->query($sql_eect, array(':idetape' => $value['id'], ':idcontact' => $tab_ins['id_contact']) );

					if($db->numrows($res_eect) == 0) {
						$etap_ct = new action_etap_ct();
						$etap_ct->init_description();
						$etap_ct->fields['id_etape'] = $value['id'];
						$etap_ct->fields['id_ee_contact'] = $tab_ins['id_contact'];
						$id_eect = $etap_ct->save();
					}
					else {
						$tab_eect = $db->fetchrow($res_eect);
						$id_eect = $tab_eect['id'];
					}
					$tab_etap[$value['id']]['id_ee_ct']			= $id_eect;
					$tab_etap[$value['id']]['valide_etape']		= 0;
					$tab_etap[$value['id']]['date_valid_etape'] = '';
				}

				//On set par défaut l'etape selectionné a la derniere "non validé"
				//if(empty($id_etap_selected) && $tab_etap[$value['id']]['valide_etape'] != 2)
				//	$id_etap_selected = $value['id'];

				//on compte le nombre d'etapes valides
				if($value['valide_etape'] == 2) $cpt_etap_valid++;

				//donnees concernant les docs rattaches aux etapes
				if(isset($value['id_file_etap']) && !empty($value['id_file_etap'])) {
					$tab_file[$value['id']][$value['id_file_doc']]['id']	= $value['id_file_etap'];
					$tab_file[$value['id']][$value['id_file_doc']]['id_doc']= $value['id_file_doc'];
				}
				//donnees concernant les docs rattaches aux etapes et au contact courant
				if(isset($value['id_file_ct']) && !empty($value['id_file_ct']) && !empty($value['id_file_doc'])) {
					$tab_ct[$value['id']][$value['id_file_etap']]['id_doc_frontoffice']	= $value['id_doc_frontoffice'];
					$tab_ct[$value['id']][$value['id_file_etap']]['provenance']			= $value['provenance'];
					$tab_ct[$value['id']][$value['id_file_etap']]['valide']				= $value['valide'];
					$tab_ct[$value['id']][$value['id_file_etap']]['date_reception']		= $value['date_reception'];
					$tab_ct[$value['id']][$value['id_file_etap']]['date_validation']	= $value['date_validation'];
					$tab_ct[$value['id']][$value['id_file_etap']]['id_contact']			= $value['id_contact'];
					$tab_ct[$value['id']][$value['id_file_etap']]['id_ct_doc']			= $value['id_ct_doc'];
					$tab_ct[$value['id']][$value['id_file_etap']]['id']					= $value['id_file_ct'];

					if($value['valide']!=0) $cpt_valid++;
				}
				elseif(!empty($value['id_file_doc'])) {
					//si on ne recupère rien dans le left join, il faut initialiser les valeurs
					//require_once(DIMS_APP_PATH . '/modules/system/class_action_etap_file_ct.php');
					$file_ct = new etap_file_ct();
					$file_ct->init_description();
					$file_ct->fields['id_etape'] = $value['id'];
					$file_ct->fields['id_contact'] = $tab_ins['id_contact'];
					$file_ct->fields['id_action'] = $value['id_action'];
					$file_ct->fields['id_doc'] = $value['id_file_doc'];
					$id_newfile = $file_ct->save();

					$tab_ct[$value['id']][$value['id_file_etap']]['id'] = $id_newfile;

					$tab_ct[$value['id']][$value['id_file_etap']]['id_doc_frontoffice']	= '';
					$tab_ct[$value['id']][$value['id_file_etap']]['provenance']			= '';
					$tab_ct[$value['id']][$value['id_file_etap']]['valide']				= '';
					$tab_ct[$value['id']][$value['id_file_etap']]['date_reception']		= '';
					$tab_ct[$value['id']][$value['id_file_etap']]['date_validation']	= '';
					$tab_ct[$value['id']][$value['id_file_etap']]['id_contact']			= $tab_ins['id_contact'];
					$tab_ct[$value['id']][$value['id_file_etap']]['id_ct_doc']			= $value['id_file_doc'];

				}
				elseif(empty($value['id_file_doc']) && !empty($value['input_label'])) {
					$tabInput[$value['id']][$value['id_file_etap']]['label'] = $value['input_label'];
					$tabInput[$value['id']][$value['id_file_etap']]['content'] = $value['input_content'];

					if($value['valide'] == 1)
						$tabInput[$value['id']][$value['id_file_etap']]['validated'] = $value['valide'];

					if(!empty($value['id_doc_frontoffice'])) {
						$tabInput[$value['id']][$value['id_file_etap']]['user_doc'][$value['id_file_ct']]['id_file_ct'] = $value['id_file_ct'];
						$tabInput[$value['id']][$value['id_file_etap']]['user_doc'][$value['id_file_ct']]['id_doc']		= $value['id_doc_frontoffice'];
						$tabInput[$value['id']][$value['id_file_etap']]['user_doc'][$value['id_file_ct']]['state']		= $value['valide'];
						$tabInput[$value['id']][$value['id_file_etap']]['user_doc'][$value['id_file_ct']]['send_time']	= $value['date_reception'];
						if(isset($value['invalid_content'])) $tabInput[$value['id']][$value['id_file_etap']]['user_doc'][$value['id_file_ct']]['comment']	= $value['invalid_content'];
					}
				}
				//on met la valeur a 1 si elle n'est pas encore enregistrée mais que l'on a deja un ou pls docs valides
				//ce qui permettra d'avoir la puce de la bonne couleur
				if($value['valide_etape'] == 0 && $cpt_valid > 0) $tab_etap[$value['id']]['valide_etape'] = 1;
			}
		}

		$id_etap_selected = dims_load_securvalue('id_etap', dims_const::_DIMS_NUM_INPUT, true, true, false, $id_etap_selected);

		echo '<div id="etapes">
		<div id="onglets">
		<ul>';

		foreach($tab_etap as $etap) {

			switch($etap['valide_etape']) {
				default:
				case 0 : //rien n'est valide
					$img_state = '<img title="'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_WAIT'].'" alt="'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_WAIT'].'" src="./common/modules/system/img/ico_point_grey.gif" />&nbsp;';
					break;
				case 1 : //au moins 1 doc valide
					$img_state = '<img title="'.$_DIMS['cste']['_DIMS_LABEL_RUNNING_REGISTRATION'].'" alt="'.$_DIMS['cste']['_DIMS_LABEL_RUNNING_REGISTRATION'].'" src="./common/modules/system/img/ico_point_orange.gif" />&nbsp;';
					break;
				case 2 : //tous les docs valides ou validation manuelle
					$img_state = '<img title="'.$_DIMS['cste']['_DIMS_LABEL_VALIDATED_STATE'].'" alt="'.$_DIMS['cste']['_DIMS_LABEL_VALIDATED_STATE'].'" src="./common/modules/system/img/ico_point_green.gif" />&nbsp;';
					break;
				case -1 : //etape invalidee
					$img_state = '<img title="'.$_DIMS['cste']['_DIMS_LABEL_CANCELED_STATE'].'" alt="'.$_DIMS['cste']['_DIMS_LABEL_CANCELED_STATE'].'" src="./common/modules/system/img/ico_point_red.gif" />&nbsp;';
					break;
			}

			$class = '';
			if(empty($id_etap_selected) || $id_etap_selected == $etap['id']) {
				$id_etap_selected = $etap['id'];
				$class = 'selected';
			}

			$href = $dims->getScriptEnv();
			$href.= '?dims_mainmenu=events';
			$href.= '&submenu='.dims_const::_DIMS_SUBMENU_EVENT;
			$href.= '&action=adm_insc';
			$href.= '&id_evt='.$tab_evt['id_evt'];
			$href.= '&id_insc='.$id_insc;
			$href.= '&id_etap='.$etap['id'];

			echo '<li class='.$class.'>';
			echo '<a href="'.$href. '">';
			echo $img_state;
			echo $etap['label'];
			echo '</a></li>';
		}

		echo '		</ul>
		</div>
		<div id="details">';

		$etap_selected = array();

		if(isset($tab_etap[$id_etap_selected]) &&
		!empty($tab_etap[$id_etap_selected]) &&
		is_array($tab_etap[$id_etap_selected])) {
			switch($tab_etap[$id_etap_selected]['type_etape']) {
				case 5:

					//on regarde ce que l'on a comme info sur l'inscription
					$sqli = "SELECT id, date_facturation, date_paiement FROM dims_mod_business_event_inscription WHERE id_action = :idaction AND id_contact = :idcontact";
					$resi = $db->query($sqli, array(':idaction' => $tab_evt['id_evt'], ':idcontact' => $tab_ins['id_contact']) );
					$tab_pay = $db->fetchrow($resi);

					$date_p = 'jj/mm/aaaa';
					$date_f = 'jj/mm/aaaa';

					if(!empty($tab_pay['date_paiement'])) {
						$tab_date_p = dims_timestamp2local($tab_pay['date_paiement']);
						$date_p = $tab_date_p['date'];
					}
					if(!empty($tab_pay['date_facturation'])) {
						$tab_date_f = dims_timestamp2local($tab_pay['date_facturation']);
						$date_f = $tab_date_f['date'];
					}


					echo	'<div>
								<table>
									<tr>
										<td>'.$_DIMS['cste']['_DIMS_LABEL_FAIRS_DATE_FAC'].'</td>
										<td><input name="date_f" id="date_f" value="'.$date_f.'"/></td>
									</tr>
									<tr>
										<td>'.$_DIMS['cste']['_DIMS_LABEL_FAIRS_DATE_PAIEMENT'].'</td>
										<td><input name="date_p" id="date_p" value="'.$date_p.'"/></td>
									</tr>
									<tr>
										<td colspan="2">
											<a href="javascript:void(0);" onclick="javascript:date_f = dims_getelem(\'date_f\').value; date_p = dims_getelem(\'date_p\').value; validPaiement('.$tab_ins['id_insc'].', date_f, date_p, \''.$id_etap_selected.'\');""><img src="./common/img/checkdo.png"/>'.$_DIMS['cste']['_DIMS_SAVE'].'</a>
										</td>
									</tr>
								</table>
							</div>';

					break;
				case 4:
					$etap_selected = $tab_etap[$id_etap_selected];

					$sql_d = "SELECT * FROM dims_mod_business_event_etap_delegue WHERE id_action = :idaction AND id_etap = :idetape AND id_contact = :idcontact";
					$res_d = $db->query($sql_d, array(':idaction' => $tab_evt['id_evt'], ':idetape' => $id_etap_selected, ':idcontact' => $tab_ins['id_contact']) );
					if($db->numrows($res_d) > 0) {
						//on affiche le tableau de resultats
						$color = "#EEEEEE";
						echo '<table width="100%" cellpadding="0" cellspacing="0" style="font-size:12px;">
								<tr style="background-color:#EEEEEE;height:20px;">
									<th align="left">'.$_DIMS['cste']['_DIMS_LABEL_NAME'].'</th>
									<th align="left">'.$_DIMS['cste']['_DIMS_LABEL_FIRSTNAME'].'</th>
									<th align="left">'.$_DIMS['cste']['_DIMS_LABEL_EMAIL'].'</th>
									<th align="left">'.$_DIMS['cste']['_DIMS_LABEL_MOBILE'].'</th>
									<th align="left">'.$_DIMS['cste']['_DIMS_LABEL_DATE_REGISTRATION'].'</th>
								</tr>';
						while($tab_deg = $db->fetchrow($res_d)) {
							if($color == "#EEEEEE") $color = "#FFFFFF";
							else $color = "#EEEEEE";
							//$date = dims_timestamp2local($tab_deg['date_inscr']);
							$date_deb = dims_timestamp2local($tab_deg['date_presence']);
							if($tab_deg['date_presence_fin'] != '') {
								$date_fin = dims_timestamp2local($tab_deg['date_presence_fin']);
								$date = $_DIMS['cste']['_FROM']." ".$date_deb['date']." ".$_DIMS['cste']['_DIMS_LABEL_A']." ".$date_fin['date'];
							}
							else {
								$date = $_DIMS['cste']['_AT']." ".$date_deb['date'];
							}
							echo '<tr style="background-color:'.$color.';height:20px;">
									<td>'.$tab_deg['lastname'].'</td>
									<td>'.$tab_deg['firstname'].'</td>
									<td>'.$tab_deg['email'].'</td>
									<td>'.$tab_deg['mobile'].'</td>
									<td>'.$date.'</td>
								</tr>';
						}
						echo '<tr>
								<td colspan="5" align="center" style="padding-top:10px;">';
						if($etap_selected['valide_etape'] != 2) echo $_DIMS['cste']['_DIMS_LABEL_ACTIONS_MILESTONE'].' '.dims_create_button_nofloat($_DIMS['cste']['_DIMS_VALID'],'./common/img/checkdo.png',"validEtapeFairs('".$etap_selected['id_ee_ct']."')");
						else echo $_DIMS['cste']['_DIMS_LABEL_ACTIONS_MILESTONE'].' '.dims_create_button_nofloat($_DIMS['cste']['_DIMS_LABEL_CANCEL'],'./common/img/checkdo.png',"unvalidEtapeFairs('".$etap_selected['id_ee_ct']."')");
						echo	'</td>
							  </tr>
							</table>';
					}
					else {
						echo "Aucun d&eacute;l&eacute;gu&eacute; n'est inscrit.";
					}



					break;
				default:
				case 3:
				case 2:
				case 1:
					$etap_selected = $tab_etap[$id_etap_selected];

					echo '<div class="description_etap">';
					echo $etap_selected['description'];
					echo '</div>';
					echo '<div class="validate">';

					if($etap_selected['valide_etape'] == 2) {
						$date_str = dims_timestamp2local($etap_selected['date_valid_etape']);

						echo $_DIMS['cste']['_DIMS_LABEL_VALIDATE_ON'].
						' '.$date_str['date'];
					}

					echo '</div>';

					if(isset($tabInput[$etap_selected['id']]) &&
					is_array($tabInput[$etap_selected['id']]) &&
					(count($tabInput[$etap_selected['id']]) > 0)) {

						$etapInput = $tabInput[$etap_selected['id']];

						echo '<div class="doc"><table style="width:100%;">';
						echo '<tr>';
						echo '<td>'.$_DIMS['cste']['_DIMS_LABEL'].'</td>';
						echo '<td>'.$_DIMS['cste']['_DIMS_LABEL_RECEPTION'].'</td>';
						echo '</tr>';

						$doc_etape = new docfile;

						foreach($etapInput as $idInput => $input) {
							//information et gestion du document reçu de l'utilisateur
							$col_doc_user = '';
							if(!empty($input['user_doc'])) {
								foreach($input['user_doc'] as $userDoc) {
									//on a un document recu via le front office
									$doc_user = new docfile;
									$doc_user->open($userDoc['id_doc']);

									$date_recept = dims_timestamp2local($doc_user->fields['timestp_create']);

									$class = '';

									if($userDoc['state'] == 1) {
										$class ='validate';
									}
									if($userDoc['state'] == 0) {
										$class ='pending';
									}
									if($userDoc['state'] == -1) {
										$class ='refused';
									}

									$col_doc_user .= '<a href="'.$doc_user->getwebpath().'" class="'.$class.'" target="_blank">';
									$col_doc_user .=  $doc_user->fields['name'];
									$col_doc_user .= '</a> ';
									$col_doc_user .= '('.$date_recept['date'].')';

									if($userDoc['state'] != -1) {
										$col_doc_user .= '<a href="javascript:void(0);" onclick="javascript:changeReceptionDoc(\''.$userDoc['id_file_ct'].'\', \''.$etap_selected['id'].'\')">
										<img src="./common/img/delete.png" title="'.$_DIMS['cste']['_DIMS_LABEL_CANCEL'].'"/>
										</a>';
									}

									if($userDoc['state']  == 0) {
										$col_doc_user .= '<a href="javascript:void(0);" onclick="javascript:validateDoc(\'date_validation_doc_'.$userDoc['id_file_ct'].'\',\''.$userDoc['id_file_ct'].'\',\''.$etap_selected['id'].'\');">
										<img src="./common/img/checkdo2.png" title="'.$_DIMS['cste']['_DIMS_LABEL_STOP_DOC_VALIDATION'].'"/>
										</a>';
									}

									$col_doc_user .= '<br />';
								}
							}

							//informations concernant la validation du document
							$ico_valid = '';
							if(isset($input['validated']) && $input['validated'] == 1) {
								$ico_valid = '<img alt="'.$_DIMS['cste']['_DIMS_LABEL_VALIDATED_STATE'].'" src="./common/modules/system/img/ico_point_green.gif" />';
							}
							elseif(isset($input['validated']) && $input['validated'] == 0) {
								$ico_valid = '<img alt="'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_WAIT'].'" src="./common/modules/system/img/ico_point_grey.gif" />';
							}
							else {
								$ico_valid = '<img alt="'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_WAIT'].'" src="./common/modules/system/img/ico_point_red.gif" />';
							}

							echo '	<tr>
							<td width="30%" style="border-bottom:#666666 1px dotted" valign="top">
							'.$ico_valid.'&nbsp;&nbsp;';
							echo $input['label'];
							echo '
							</td>
							<td width="30%" style="border-bottom:#666666 1px dotted">'.$col_doc_user.'</td>
							</tr>';
						}
						echo '</table></div>';
					}
					else {
						echo '<p style="padding-left:50px;color:#FF0000;font-size:13px;">'.$_DIMS['cste']['_DIMS_LABEL_EVENT_STEP_NODOC'].'</p>';
						//echo '<a href="admin.php?dims_mainmenu=8&dims_desktop=block&dims_action=public&cat=-1&op=xml_planning_modifier_action&id=27">Ajouter des documents</a>';
					}

					echo '<div class="actions">';

					echo $_DIMS['cste']['_DIMS_LABEL_ACTIONS_MILESTONE'];

					switch($etap_selected['valide_etape']) {
						case 0 : //rien n'est valide
							echo dims_create_button_nofloat($_DIMS['cste']['_DIMS_VALID'],'./common/img/checkdo.png',"validEtape('".$etap_selected['id_ee_ct']."')");
							break;
						case 1 : //au moins 1 doc valide
							echo dims_create_button_nofloat($_DIMS['cste']['_DIMS_LABEL_CANCEL'],'./common/img/delete.png',"unvalidEtape('".$etap_selected['id_ee_ct']."')");
							echo dims_create_button_nofloat($_DIMS['cste']['_DIMS_VALID'],'./common/img/checkdo.png',"validEtape('".$etap_selected['id_ee_ct']."')");
							break;
						case 2 : //tous les docs valides ou validation manuelle
							echo dims_create_button_nofloat($_DIMS['cste']['_DIMS_LABEL_CANCEL'],'./common/img/delete.png',"unvalidEtape('".$etap_selected['id_ee_ct']."')");
							break;
						case -1 : //etape invalidee
							echo dims_create_button_nofloat($_DIMS['cste']['_DIMS_VALID'],'./common/img/checkdo.png',"validEtape('".$etap_selected['id_ee_ct']."')");
							break;
					}
					echo '</div>';
					break;
			}
		}

		echo '	</div>
		</div>';
	}
	$nb_res--; //on enlève 1 car le resume ne peut être validé
	if($cpt_etap_valid == $nb_res && $tab_ins['validate'] != 2) {
		echo '	<table width="100%" style="border-top: 1px solid rgb(203, 204, 207); margin-top: 15px;">';
		echo '<tr><td width="100%" align="center">';
		echo dims_create_button($_DIMS['cste']['_DIMS_VALID_REGISTER'],'./common/img/publish.png',"javascript:validateInscriptionNiv2('".$tab_ins['id_insc']."');","","");
		echo '</td></tr>';
		echo '</table>';
	}

	echo $skin->close_simplebloc();
?>
