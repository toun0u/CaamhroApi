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
								euser.id_doc = efile.id_doc
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
							position';

				$res=$db->query($sql, array(':idcontact1' => $tab_ins['id_contact'], ':idcontact2' => $tab_ins['id_contact'], ':idevt'  => $tab_evt['id_evt']));
				$nb_res = $db->numrows($res);
				if ($nb_res>0) {
					$id_etap_selected = 0;
					$class="trl1";

					$tab_etap = array();
					$tab_file = array();
					$cpt_etap_valid = 0;
					while ($value=$db->fetchrow($res)) {
						$cpt_valid = 0;
						//construction des tableaux de donnees

						//donnees concernant les etapes
						$tab_etap[$value['id']]['id']			= $value['id'];
						$tab_etap[$value['id']]['id_action']	= $value['id_action'];
						$tab_etap[$value['id']]['label']		= $value['label'];
						$tab_etap[$value['id']]['position']		= $value['position'];
						$tab_etap[$value['id']]['description']	= $value['description'];
						//donnees concernant les etapes rattachees au contact courant
						if(isset($value['id_ee_ct']) && !empty($value['id_ee_ct'])) {
							$tab_etap[$value['id']]['id_ee_ct']			= $value['id_ee_ct'];
							$tab_etap[$value['id']]['valide_etape']		= $value['valide_etape'];
							$tab_etap[$value['id']]['date_valid_etape'] = $value['date_validation_etape'];
						}
						else {
							//si on ne recupère rien dans le left join, il faut initialiser les valeurs
							require_once(DIMS_APP_PATH . '/modules/system/class_action_etap_ct.php');
							//on verifie d'abord si les etapes existent pour le contact courant (cela evite les doublons)
							$sql_eect = "SELECT id FROM dims_mod_business_event_etap_user WHERE id_etape = :idetape AND id_ee_contact = :idcontact";
							$res_eect = $db->query($sql_eect,array(':idetape' => $value['id'], ':idcontact' => $tab_ins['id_contact']));

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
						if(empty($id_etap_selected) && $tab_etap[$value['id']]['valide_etape'] != 2)
							$id_etap_selected = $value['id'];

						//on compte le nombre d'etapes valides
						if($value['valide_etape'] == 2) $cpt_etap_valid++;

						//donnees concernant les docs rattaches aux etapes
						if(isset($value['id_file_etap']) && !empty($value['id_file_etap']))
						{
							$tab_file[$value['id']][$value['id_file_doc']]['id']					= $value['id_file_etap'];
							$tab_file[$value['id']][$value['id_file_doc']]['id_doc']				= $value['id_file_doc'];
						}
						//donnees concernant les docs rattaches aux etapes et au contact courant
						if(isset($value['id_file_ct']) && !empty($value['id_file_ct']))
						{
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
						else {
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
						//on met la valeur a 1 si elle n'est pas encore enregistrée mais que l'on a deja un ou pls docs valides
						//ce qui permettra d'avoir la puce de la bonne couleur
						if($value['valide_etape'] == 0 && $cpt_valid > 0) $tab_etap[$value['id']]['valide_etape'] = 1;
					}

					$id_etap_selected = dims_load_securvalue('id_etap', _DIMS_NUM_INPUT, true, true, false, $id_etap_selected);

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
						$href.= '?dims_mainmenu='._DIMS_MENU_HOME;
						$href.= '&submenu='._DIMS_SUBMENU_EVENT;
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
						$etap_selected = $tab_etap[$id_etap_selected];

						if(isset($tab_file[$etap_selected['id']]) &&
						   is_array($tab_file[$etap_selected['id']]) &&
						   (count($tab_file[$etap_selected['id']]) > 0)) {

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

							echo '<div class="doc"><table>';
							echo '<tr>';
							echo '<td>'.$_DIMS['cste']['_DIMS_LABEL_DOC_REFERENCE'].'</td>';
							echo '<td>'.$_DIMS['cste']['_DIMS_LABEL_DOC_FROM'].'</td>';
							echo '<td>'.$_DIMS['cste']['_DIMS_LABEL_RECEPTION'].'</td>';
							echo '<td>'.$_DIMS['cste']['_DIMS_LABEL_VALIDATION'].'</td>';
							echo '</tr>';


							$doc_etape = new docfile;

							foreach($tab_ct[$etap_selected['id']] as $file)
							{
								if($file['id_ct_doc'] != 0) $doc_etape->open($tab_file[$etap_selected['id']][$file['id_ct_doc']]['id_doc']);
								//information et gestion du document reçu de l'utilisateur
								$col_doc_user = '';
								if(!empty($file['id_doc_frontoffice'])) {
									//on a un document recu via le front office
									$doc_user = new docfile;
									$doc_user->open($file['id_doc_frontoffice']);

									$date_recept = dims_timestamp2local($doc_user->fields['timestp_create']);

									$col_doc_user .= '<a href="'.$doc_user->getwebpath().'" target="_blank">';
									$col_doc_user .=  $doc_user->fields['name'];
									$col_doc_user .= '</a> ';
									$col_doc_user .= '('.$date_recept['date'].')';
									$col_doc_user .= '<a href="javascript:void(0);" onclick="javascript:changeReceptionDoc(\''.$file['id'].'\', \''.$etap_selected['id'].'\')">
																  <img src="./common/img/delete.png" title="'.$_DIMS['cste']['_DIMS_LABEL_CANCEL'].'"/>
															  </a>';
								}
								else {

									if($file['provenance'] != '_DIMS_LABEL_INET') {
										//le document vient d'une autre provenance
										if(!empty($file['date_reception'])) {
											//cas où la reception est deja validee
											$d_recept = dims_timestamp2local($file['date_reception']);
											$col_doc_user .= $d_recept['date'];
											$col_doc_user .= '<a href="javascript:void(0);" onclick="javascript:changeReceptionDoc(\''.$file['id'].'\', \''.$etap_selected['id'].'\')">
																  <img src="./common/img/delete.png" title="'.$_DIMS['cste']['_DIMS_LABEL_CANCEL'].'"/>
															  </a>';
										}
										else {
											//cas où le document n'est pas encore reçu
											$col_doc_user .= '<input class="text" type="text" id="date_reception_doc_'.$file['id'].'" value="">
																<a href="javascript:void(0);" onclick="javascript:dims_calendar_open(\'date_reception_doc_'.$file['id'].'\', event,\'\');">
																	<img src="./common/img/calendar/calendar.gif" width="31" height="18" align="top" border="0">
																</a>';
																//<input type="button" class="flatbutton" value="OK" onclick="javascript:validateReceptionDoc(\'date_reception_doc_'.$file['id'].'\',\''.$file['id'].'\',\''.$etap_selected['id'].'\');"/>';
											$col_doc_user .= dims_create_button_nofloat($_DIMS['cste']['_DIMS_VALID'], './common/img/checkdo.png','javascript:validateReceptionDoc(\'date_reception_doc_'.$file['id'].'\',\''.$file['id'].'\',\''.$etap_selected['id'].'\');');
										}
									}
									else {
										//on ne peut rien faire d'autre que d'attendre le doc ... -_-' Merci Flo ... :o)
										$col_doc_user .= $_DIMS['cste']['_DIMS_LABEL_WAIT_FOR_DOC'].'';
									}
								}

								//informations concernant la validation du document
								$col_valid_doc = '';
								$ico_valid = '';
								if($file['valide'] != 0) {
									//le document est validé
									$date_val_doc = dims_timestamp2local($file['date_validation']);

									$col_valid_doc .= $date_val_doc['date'];
									$col_valid_doc .= '<a href="javascript:void(0);" onclick="javascript:unvalidateDoc(\''.$file['id'].'\',\''.$etap_selected['id'].'\')">
															<img src="./common/img/delete.png" title="'.$_DIMS['cste']['_DIMS_LABEL_STOP_DOC_VALIDATION'].'"/>
														</a>';

									$ico_valid = '<img alt="'.$_DIMS['cste']['_DIMS_LABEL_VALIDATED_STATE'].'" src="./common/modules/system/img/ico_point_green.gif" />';
								}
								else {
									//on propose la validation (la date de validation est la date du jour)
									$col_valid_doc .= '<input class="text" type="text" id="date_validation_doc_'.$file['id'].'" value="">
															<a href="javascript:void(0);" onclick="javascript:dims_calendar_open(\'date_validation_doc_'.$file['id'].'\', event,\'\');">
																<img src="./common/img/calendar/calendar.gif" width="31" height="18" align="top" border="0">
															</a>';
															//<input type="button" class="flatbutton" value="OK" onclick="javascript:validateDoc(\'date_validation_doc_'.$file['id'].'\',\''.$file['id'].'\',\''.$etap_selected['id'].'\');"/>';
									$col_valid_doc .= dims_create_button_nofloat($_DIMS['cste']['_DIMS_VALID'], './common/img/checkdo.png','javascript:validateDoc(\'date_validation_doc_'.$file['id'].'\',\''.$file['id'].'\',\''.$etap_selected['id'].'\');');

									$ico_valid = '<img alt="'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_WAIT'].'" src="./common/modules/system/img/ico_point_grey.gif" />';
								}

								echo '	<tr>
											<td width="30%" style="border-bottom:#666666 1px dotted">
												'.$ico_valid.'&nbsp;&nbsp;
												<a href="'.$doc_etape->getwebpath().'" target="_blank">';
													echo $doc_etape->fields['name'];
								echo '			</a>
											</td>
											<td width="15%" style="border-bottom:#666666 1px dotted">
												<select id="doc_provenance_'.$file['id'].'" onchange="javascript:saveDocProvenance(\''.$file['id'].'\', \''.$etap_selected['id'].'\')" style="width:120px;">
													<option value=""></option>
													<option value="_DIMS_LABEL_INET"';
														if($file['provenance'] == '_DIMS_LABEL_INET') echo ' selected="selected"';
													echo '">'.$_DIMS['cste']['_DIMS_LABEL_INET'].'</option>
													<option value="_DIMS_LABEL_EMAIL"';
														if($file['provenance'] == '_DIMS_LABEL_EMAIL') echo ' selected="selected"';
													echo '">'.$_DIMS['cste']['_DIMS_LABEL_EMAIL'].'</option>
													<option value="_DIMS_LABEL_COURRIER"';
														if($file['provenance'] == '_DIMS_LABEL_COURRIER') echo ' selected="selected"';
													echo '">'.$_DIMS['cste']['_DIMS_LABEL_COURRIER'].'</option>
													<option value="_DIMS_LABEL_FAX"';
														if($file['provenance'] == '_DIMS_LABEL_FAX') echo ' selected="selected"';
													echo '">'.$_DIMS['cste']['_DIMS_LABEL_FAX'].'</option>
													<option value="_DIMS_LABEL_USB_KEY"';
														if($file['provenance'] == '_DIMS_LABEL_USB_KEY') echo ' selected="selected"';
													echo '">'.$_DIMS['cste']['_DIMS_LABEL_USB_KEY'].'</option>
													<option value="_DIMS_LABEL_CD_DVD"';
														if($file['provenance'] == '_DIMS_LABEL_CD_DVD') echo ' selected="selected"';
													echo '">'.$_DIMS['cste']['_DIMS_LABEL_CD_DVD'].'</option>
												</select>
											</td>
											<td width="30%" style="border-bottom:#666666 1px dotted">'.$col_doc_user.'</td>
											<td width="25%" style="border-bottom:#666666 1px dotted">'.$col_valid_doc.'</td>
										</tr>';
							}
							echo '</table></div>';

							echo '<div class="actions">';

							echo $_DIMS['cste']['_LABEL_ACTIONS_MILESTONE'];

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
						}
						else {
							echo '<p style="padding-left:50px;color:#FF0000;font-size:13px;">'.$_DIMS['cste']['_DIMS_LABEL_EVENT_STEP_NODOC'].'</p>';
							//echo '<a href="admin.php?dims_mainmenu=8&dims_desktop=block&dims_action=public&cat=-1&op=xml_planning_modifier_action&id=27">Ajouter des documents</a>';
						}
					}

					echo '	</div>
						</div>';
				}

				if($cpt_etap_valid == $nb_res && $tab_ins['validate'] != 2) {
					echo '	<table width="100%" style="border-top: 1px solid rgb(203, 204, 207); margin-top: 15px;">';
					echo '<tr><td width="100%" align="center">';
					echo dims_create_button($_DIMS['cste']['_DIMS_VALID_REGISTER'],'./common/img/publish.png',"javascript:validateInscriptionNiv2('".$tab_ins['id_insc']."');","","");
					echo '</td></tr>';
					echo '</table>';
				}

				echo $skin->close_simplebloc();
?>
