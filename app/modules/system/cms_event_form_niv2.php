<script language="javascript" src="./include/portal.js"></script>
<script type="text/javascript" language="javascript">
	<?php
			$sid = session_id();

			$temp_dir = _DIMS_TEMPORARY_UPLOADING_FOLDER;
			$session_dir = $temp_dir.$sid;
			$upload_size_file = $session_dir."/upload_size";
			$upload_finished_file = $session_dir."/upload_finished";

			if (file_exists($upload_size_file)) unlink($upload_size_file);
			if (file_exists($upload_finished_file)) unlink($upload_finished_file);
			$http_host = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '';
		?>
		var uploads = new Array();
		var upload_cell, file_name;
		var count=0;
		var checkCount = 0;
		var check_file_extentions = true;
		var sid = '<? echo $sid; ?>';
		var page_elements = ["toolbar","page_status_bar"];
		var img_path = "../common/img/";
		var path = "";
		var bg_color = false;
		var status;
		var debug = false;
		var param1= '<? echo ($op == 'file_add') ? 'true;' : 'false;'; ?>';
		var param2= '<? echo (!empty($wfusers) && !$wf_validator) ? 'true' : 'false'; ?>';

	//id_evt, id_contact, id_doc (le doc_vierge), id_etape
	function showUploadDoc(id_evt, id_ct, id_doc, id_etape) {
		var retour = dims_xmlhttprequest("index.php", 'action=show_upload&id_evt='+id_evt+'&id_ct='+id_ct+'&id_doc='+id_doc+'&id_etape='+id_etape);
		dims_getelem('dims_popup').innerHTML = retour;
		status = document.getElementById('status');
		<?
		global $dims;
		$rootpath=$dims->getProtocol().$http_host;
		?>
		setVariables("<? echo $rootpath; ?>","<? echo $_DIMS['cste']['_DOC_MSG_UPLOAD_FILE']; ?>","<? echo $_DIMS['cste']['_DOC_MSG_UPLOAD_WAITING']; ?>","<? echo $_DIMS['cste']['_DOC_MSG_COPY_FILE']; ?>","<? $_DIMS['cste']['_DOC_MSG_UPLOAD_ERROR']; ?>","<? echo $_DIMS['cste']['_DOC_MSG_UPLOAD_ERROREXT']; ?>");

		createFileInput();
		//dims_showcenteredpopup("",700,310,'dims_popup');
		var pop = document.getElementById('dims_popup');
		pop.style.display = "block";
		pop.style.visibility = "visible";
		pop.style.width = "690px";
		pop.style.height = "330px";
		pop.style.top = "10%";
		pop.style.left = "10%";
		pop.style.position = "absolute";
	}

</script>
<script type="text/javascript" src="/js/upload/javascript/uploader.js"></script>
<?php

require_once(DIMS_APP_PATH . '/modules/system/class_action.php');

$id_evt = dims_load_securvalue('id_event', dims_const::_DIMS_NUM_INPUT, true);

//affichage du resume de l'evt
$evt = new action();
$evt->open($id_evt);
echo '<div id="dims_popup"></div>';
echo '<div id="descript_evt" style="font-size:16px;">';
if ($evt->fields['libelle']!="") {
	echo '<h1>'.$evt->fields['libelle'].'</h1>';
}
//echo '<p>'.$evt->fields['description'].'</p>';
echo '</div>';

//dims_print_r($evt->fields);

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
				euser.date_validation,
				euser.invalid_content
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
					euser.id_contact = :idcontact
			LEFT JOIN
				dims_mod_business_event_etap_user eect
				ON
					ee.id = eect.id_etape
				AND
					eect.id_ee_contact = :idcontact
			WHERE
				ee.id_action = :idaction
			ORDER BY
				position';
	$res=$db->query($sql, array(
		':idcontact' 	=> $_SESSION['dims']['user']['id_contact'],
		':idaction' 	=> $id_evt
	));
	$nb_res = $db->numrows($res);

	if ($nb_res>0) {
		$id_etap_selected = 0;
		$class="trl1";

		$tab_etap = array();
		$tab_ct = array();
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
				$sql_eect = "SELECT id FROM dims_mod_business_event_etap_user WHERE id_etape = :idetape AND id_ee_contact = :idcontact ";
				$res_eect = $db->query($sql_eect, array(
					':idetape' 		=> $value['id'],
					':idcontact' 	=> $_SESSION['dims']['user']['id_contact']
				));

				if($db->numrows($res_eect) == 0) {
					$etap_ct = new action_etap_ct();
					$etap_ct->init_description();
					$etap_ct->fields['id_etape'] = $value['id'];
					$etap_ct->fields['id_ee_contact'] = $_SESSION['dims']['user']['id_contact'];
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
				$tab_ct[$value['id']][$value['id_file_etap']]['id_ct_doc']			= $value['id_ct_doc']; //doc non complete
				$tab_ct[$value['id']][$value['id_file_etap']]['id']					= $value['id_file_ct'];
				$tab_ct[$value['id']][$value['id_file_etap']]['invalid_content']	= $value['invalid_content'];


				if($value['valide']!=0) $cpt_valid++;
			}
			else {
				//si on ne recupère rien dans le left join, il faut initialiser les valeurs
				require_once(DIMS_APP_PATH . '/modules/system/class_action_etap_file_ct.php');
				$file_ct = new etap_file_ct();
				$file_ct->init_description();
				$file_ct->fields['id_etape'] = $value['id'];
				$file_ct->fields['id_contact'] = $_SESSION['dims']['user']['id_contact'];
				$file_ct->fields['id_action'] = $value['id_action'];
				$file_ct->fields['id_doc'] = $value['id_file_doc'];
				$id_newfile = $file_ct->save();

				$tab_ct[$value['id']][$value['id_file_etap']]['id'] = $id_newfile;

				$tab_ct[$value['id']][$value['id_file_etap']]['id_doc_frontoffice']	= '';
				$tab_ct[$value['id']][$value['id_file_etap']]['provenance']			= '';
				$tab_ct[$value['id']][$value['id_file_etap']]['valide']				= '';
				$tab_ct[$value['id']][$value['id_file_etap']]['date_reception']		= '';
				$tab_ct[$value['id']][$value['id_file_etap']]['date_validation']	= '';
				$tab_ct[$value['id']][$value['id_file_etap']]['id_contact']			= $_SESSION['dims']['user']['id_contact'];
				$tab_ct[$value['id']][$value['id_file_etap']]['id_ct_doc']			= $value['id_file_doc'];

			}
			//on met la valeur a 1 si elle n'est pas encore enregistrée mais que l'on a deja un ou pls docs valides
			//ce qui permettra d'avoir la puce de la bonne couleur
			if($value['valide_etape'] == 0 && $cpt_valid > 0) $tab_etap[$value['id']]['valide_etape'] = 1;
		}

		echo '<div id="form_2">';
		//echo '<h2>'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_VALIDATION'].'</h2>';
		echo '<p>'.$_DIMS['cste']['_DIMS_TEXT_REGISTRATION_FORM_2'].'</p>';

		$id_etap_selected = dims_load_securvalue('id_etap', dims_const::_DIMS_NUM_INPUT, true, true, false, $id_etap_selected);

		if(count($tab_etap) > 0) {
			$previous_etap_state = 0;
			$previous_etap = array();

			echo '<div id="etapes">
					<div id="onglets">
						<ul>';

			foreach($tab_etap as $etap) {

				if(empty($id_etap_selected))
					$id_etap_selected = $etap['id'];

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
					$previous_etap_state = (isset($previous_etap['valide_etape'])) ? $previous_etap['valide_etape'] : 2 ;
					$class = 'selected';
				}

				echo '<li class='.$class.'>';
				echo '<a href="?id_event='.$id_evt.'&id_etap='.$etap['id'].'">';
				echo $img_state;
				echo $etap['label'];
				echo '</a></li>';

				$previous_etap = $etap;
			}

			echo '</ul>
				&nbsp;
				</div>
				<div id="details">';

			$etap_selected = array();

			if(isset($tab_etap[$id_etap_selected]) &&
			!empty($tab_etap[$id_etap_selected]) &&
			is_array($tab_etap[$id_etap_selected])) {

			   $etap_selected = $tab_etap[$id_etap_selected];

				if($etap_selected['position'] == 1 ||
					($etap_selected['position'] > 1 &&
					$previous_etap_state == 2)) {

					if(isset($tab_file[$etap_selected['id']]) &&
					   is_array($tab_file[$etap_selected['id']]) &&
					   (count($tab_file[$etap_selected['id']]) > 0)) {

						echo '<div class="description_etap">';
						echo $etap_selected['description'];
						echo '</div>';

						$doc_etape = new docfile;

						echo '<div class="doc"><table>';
						foreach($tab_ct[$etap_selected['id']] as $file) {
							$doc_etape->open($tab_file[$etap_selected['id']][$file['id_ct_doc']]['id_doc']);

							$col_doc_user = '';

							//information et gestion du document reçu de l'utilisateur
							if(!empty($file['id_doc_frontoffice'])) {
								//on a un document recu via le front office
								$doc_user = new docfile;
								$doc_user->open($file['id_doc_frontoffice']);

								$date_recept = dims_timestamp2local($doc_user->fields['timestp_create']);

								$col_doc_user .= $_DIMS['cste']['_DIMS_LABEL_RECEIVED_DOC'].' ';
								$col_doc_user .= $_DIMS['cste']['_AT'].' ';
								$col_doc_user .= $date_recept['date'].':<br/>';
								$col_doc_user .= '<a href="'.$doc_user->getwebpath().'" target="_blank" style="color:#6699CC">';
								$col_doc_user .=  $doc_user->fields['name'];
								$col_doc_user .= '</a>&nbsp;&nbsp;&nbsp;';
								if($file['valide'] != 2) {
									$col_doc_user .= '<a href="javascript:void(0);" onclick="javascript:dims_confirmlink(\'index.php?id_event='.$id_evt.'&action=delete_doc_user&id_doc_fo='.$doc_user->fields['id'].'&id_file_u='.$file['id'].'\',\''.$_DIMS['cste']['_DIMS_ARE_YOU_SURE_TO_DEL'].' ?\');">';
									$col_doc_user .= '<img src="./common/img/delete.png" title="'.$_DIMS['cste']['_DELETE'].'" alt="'.$_DIMS['cste']['_DELETE'].'" />';
									$col_doc_user .= '</a>';
								}
							}
							elseif($file['provenance'] != '_DIMS_LABEL_INET' && $file['provenance'] != '') {

								if(!empty($file['date_reception'])) {
									//cas où la reception est deja validee
									//on indique la provenance et la date de reception
									$d_recept = dims_timestamp2local($file['date_reception']);
									$col_doc_user .= $_DIMS['cste']['_DIMS_LABEL_RECEIVED_DOC'].' '.$_DIMS['cste']['_AT'].' '.$d_recept['date'];

								}
								else {
									//cas où le document n'est pas encore reçu
									$col_doc_user .= ' '.$_DIMS['cste']['_DIMS_LABEL_DOCUMENT_WAIT'].'.';
								}
							}
							elseif($file['valide'] == 0) {
								//on propose le telechargment du document
								//on renvoie id_evt, id_contact, id_doc (le doc_vierge), id_etape
								$col_doc_user .= '<a href="javascript:void(0);" onclick="javascript:showUploadDoc(\''.$id_evt.'\',\''.$_SESSION['dims']['user']['id_contact'].'\',\''.$file['id_ct_doc'].'\',\''.$etap_selected['id'].'\');" style="color:#6699CC">'.$_DIMS['cste']['_DIMS_LABEL_DOCUMENT_SEND'].'</a>';
							}

							//informations concernant la validation du document
							$col_valid_doc = '';
							$ico_valid = '';
							if($file['valide'] != 0) {
								//le document est validé
								$date_val_doc = dims_timestamp2local($file['date_validation']);

								$col_valid_doc .= $_DIMS['cste']['_DIMS_LABEL_VALIDATE_ON'].' : '.$date_val_doc['date'];
								$ico_valid = '<img alt="'.$_DIMS['cste']['_DIMS_LABEL_VALIDATED_DOC'].'" title="'.$_DIMS['cste']['_DIMS_LABEL_VALIDATED_DOC'].'" src="./common/modules/system/img/ico_point_green.gif" />';
							}
							else {
								//
								if(!empty($file['date_reception'])) {
									$col_valid_doc .= $_DIMS['cste']['_DIMS_DOC_VALIDATION_IN_PROGRESS'].'.';
									$ico_valid = '<img alt="'.$_DIMS['cste']['_DIMS_LABEL_DOCUMENT_WAIT'].'" title="'.$_DIMS['cste']['_DIMS_LABEL_DOCUMENT_WAIT'].'" src="./common/modules/system/img/ico_point_orange.gif" />';
								}
								else {
									if(!empty($file['invalid_content'])) {
										$col_valid_doc .= $file['invalid_content'];
										$ico_valid = '<img alt="'.$_DIMS['cste']['_DIMS_LABEL_DOCUMENT_WAIT'].'" title="'.$_DIMS['cste']['_DIMS_LABEL_DOCUMENT_WAIT'].'" src="./common/modules/system/img/ico_point_grey.gif" />';
									}
									else {
										$col_valid_doc .= ' ';
										$ico_valid = '<img alt="'.$_DIMS['cste']['_DIMS_LABEL_DOCUMENT_WAIT'].'" title="'.$_DIMS['cste']['_DIMS_LABEL_DOCUMENT_WAIT'].'" src="./common/modules/system/img/ico_point_grey.gif" />';
									}
								}
							}


							$file_extension = strrchr($doc_etape->fields['name'], '.');

							$file_img = '';
							switch($file_extension) {
								case '.doc':
								case '.docx':
								case '.docm':
									$file_img = '<img src="./common/img/file_types/icon_doc_32x32.gif" />';
									break;
								case '.xls':
								case '.xlsx':
								case '.xlsmw':
									$file_img = '<img src="./common/img/file_types/icon_xls_32x32.gif" />';
									break;
								case '.pdf':
									$file_img = '<img src="./common/img/file_types/icon_pdf_32x32.gif" />';
									break;

							}

							echo '	<tr>
										<td width="30%" nowrap>
											'.$ico_valid.'&nbsp;&nbsp;
											<a href="'.$doc_etape->getwebpath().'" target="_blank" style="color:#6699CC">';
												echo $doc_etape->fields['name'].' '.$file_img;
							echo '			</a>
										</td>
										<td width="45%" style="font-size:13px;">'.$col_doc_user.'</td>
										<td width="25%" style="font-size:13px;">'.$col_valid_doc.'</td>
									</tr>';
						}
						echo '</table></div>';

						if($etap_selected['valide_etape'] == 2) {
							$date_str = dims_timestamp2local($etap_selected['date_valid_etape']);

							echo '<div class="validate">'.$_DIMS['cste']['_DIMS_LABEL_STEP'].' '.strtolower($_DIMS['cste']['_DIMS_LABEL_VALIDATE_ON']).
							' '.$date_str['date'].'</div>';
						}
					}
				}
			}
			echo '</div>';
		}
?>
</div>
<?php
	}

	echo '<div id="back_home">';
	echo '<input type="button" class="submit" value="'.$_DIMS['cste']['_DIMS_BACK'].' >" onclick="javascript: location.href=\'index.php\';" />';
	//echo dims_create_button_nofloat($_DIMS['cste']['_DIMS_BACK_TO_HOME'],'./common/img/undo.gif','javascript: href.location="index.php";');
	echo '</div></div>';

?>
