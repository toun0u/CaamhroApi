<script language="JavaScript" type="text/JavaScript">
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

	function changeReceptionDoc(idfile, idetape) {
		dims_xmlhttprequest_todiv("admin.php", "dims_mainmenu=events&submenu=<?php echo dims_const::_DIMS_SUBMENU_EVENT; ?>&action=message_annule_doc&id_file="+idfile, '', "popup_valid");
	}

	function validate_inscription(id_inscript, id_evt)
	{
		dims_xmlhttprequest_todiv('admin.php', 'dims_mainmenu=events&submenu=<?php echo dims_const::_DIMS_SUBMENU_EVENT; ?>&action=verif_valid&id_evt='+id_evt+'&id_inscrip='+id_inscript, '', 'popup_valid');
	}

</script>
<div id="popup_valid" style="position: fixed; top: 5%; left: 25%; width: 550px;"></div>
<?php
$todo_view = dims_load_securvalue('todo_view', dims_const::_DIMS_CHAR_INPUT, true, false);

if ($enableeventsteps) {
	require_once DIMS_APP_PATH . '/modules/events/public_events_summary_fairs.php';
}
else {


	$nb_docs = 0;
	$nb_insc = 0;

	if ($enabledAdminEvent || $enableeventsteps) {
		//liste des demandes d'inscription a valider
		$lim_view = '';
		if($enabledAdminEvent && $enableeventsteps== false) {
			$lim_view = " AND a.typeaction NOT LIKE '_DIMS_PLANNING_FAIR_STEPS' ";
		}
		if($enabledAdminEvent == false && $enableeventsteps) {
			$lim_view = " AND a.typeaction LIKE '_DIMS_PLANNING_FAIR_STEPS' ";
		}

		$sql_in = "SELECT		ei.*,
								a.libelle,
								a.datejour,
								a.datefin

				   FROM			dims_mod_business_event_inscription ei

				   INNER JOIN	dims_mod_business_action a
				   ON			a.id = ei.id_action
				   AND			a.id_workspace = :idworkspace
				   AND			a.datejour >= CURDATE()
				   ".$lim_view."

				   WHERE		ei.id_contact IS NULL
				   AND			ei.validate != -1
				   ORDER BY		a.datejour ASC";
				/*
				AND			 (a.id_user = {$_SESSION['dims']['userid']}
						OR		 a.id_responsible = {$_SESSION['dims']['userid']}
						OR		 a.id_organizer = {$_SESSION['dims']['userid']})

				*/
		$res_in = $db->query($sql_in, array(':idworkspace' => $_SESSION['dims']['workspaceid']) );

		if($db->numrows($res_in) > 0) {
			$tab_insc = array();
			while($tab_in = $db->fetchrow($res_in)) {

				if(!isset($tab_insc[$tab_in['id_action']])) {
					$tab_insc[$tab_in['id_action']] = array();
					$tab_insc[$tab_in['id_action']]['libelle']	= $tab_in['libelle'];
					$tab_insc[$tab_in['id_action']]['datejour'] = $tab_in['datejour'];
					$tab_insc[$tab_in['id_action']]['datefin']	= $tab_in['datefin'];
				}
				if(!isset($tab_insc[$tab_in['id_action']]['contact'][$tab_in['id']])) {
					$nb_insc++;
					$tab_insc[$tab_in['id_action']]['contact'][$tab_in['id']]['name'] = $tab_in['lastname'].' '.$tab_in['firstname'];
					$tab_insc[$tab_in['id_action']]['contact'][$tab_in['id']]['adresse'] = $tab_in['address'].' '.$tab_in['postalcode'].' '.$tab_in['city'].' '.$tab_in['country'];
					$tab_insc[$tab_in['id_action']]['contact'][$tab_in['id']]['phone'] = $tab_in['phone'];
					$tab_insc[$tab_in['id_action']]['contact'][$tab_in['id']]['email'] = $tab_in['email'];
					$tab_insc[$tab_in['id_action']]['contact'][$tab_in['id']]['company'] = $tab_in['company'];
					$tab_insc[$tab_in['id_action']]['contact'][$tab_in['id']]['fonction'] = $tab_in['function'];
				}
			}
		}
	}

	if($enableeventsteps) {
		//Liste des documents à valider
		$sql_doc = "SELECT		fu.id,
								fu.id_etape,
								fu.id_contact,
								fu.id_action,
								fu.id_doc_frontoffice,
								fu.date_reception,
								a.libelle,
								a.datejour,
								a.datefin,
								ee.label,
								ee.date_fin,
								c.lastname,
								c.firstname

					FROM		dims_mod_business_event_etap_file_user fu

					INNER JOIN	dims_mod_business_action a
					ON			a.id = fu.id_action
					AND			a.typeaction LIKE '_DIMS_PLANNING_FAIR_STEPS'
					AND			a.id_parent = 0
					AND			a.niveau = 2

					INNER JOIN	dims_mod_business_event_etap ee
					ON			ee.id = fu.id_etape

					INNER JOIN	dims_mod_business_contact c
					ON			c.id = fu.id_contact

					WHERE		fu.valide = 0
					AND			fu.id_doc_frontoffice != 0
					ORDER BY	a.datejour DESC, c.lastname ASC, c.firstname ASC, ee.position ASC";

		$res_doc = $db->query($sql_doc);
		$tab_doc = array();
		if($db->numrows($res_doc) > 0) {
			while($tab_evt = $db->fetchrow($res_doc)) {
				//on construit le tableau de resultats
				if(!isset($tab_doc[$tab_evt['id_action']])) {
					$tab_doc[$tab_evt['id_action']] = array();
					$tab_doc[$tab_evt['id_action']]['libelle'] = $tab_evt['libelle'];
					$tab_doc[$tab_evt['id_action']]['datejour'] = $tab_evt['datejour'];
					$tab_doc[$tab_evt['id_action']]['datefin'] = $tab_evt['datefin'];
				}

				if(!isset($tab_doc[$tab_evt['id_action']]['contact'][$tab_evt['id_contact']])) {
					$tab_doc[$tab_evt['id_action']]['contact'][$tab_evt['id_contact']]['name'] = $tab_evt['lastname'].' '.$tab_evt['firstname'];
				}

				if(!isset($tab_doc[$tab_evt['id_action']]['contact'][$tab_evt['id_contact']]['steps'][$tab_evt['id_etape']])) $nb_docs++;
				$tab_doc[$tab_evt['id_action']]['contact'][$tab_evt['id_contact']]['steps'][$tab_evt['id_etape']]['label'] = $tab_evt['label'];
				$tab_doc[$tab_evt['id_action']]['contact'][$tab_evt['id_contact']]['steps'][$tab_evt['id_etape']]['date_fin'] = $tab_evt['date_fin'];

				$tab_doc[$tab_evt['id_action']]['contact'][$tab_evt['id_contact']]['steps'][$tab_evt['id_etape']]['docs'][$tab_evt['id']]['date_recep'] = $tab_evt['date_reception'];
				$tab_doc[$tab_evt['id_action']]['contact'][$tab_evt['id_contact']]['steps'][$tab_evt['id_etape']]['docs'][$tab_evt['id']]['id_doc'] = $tab_evt['id_doc_frontoffice'];
			}
		}
	}
	?>

	<table width="100%" cellpadding="0" cellspacing="0" style="border:#3B567E 1px solid;">
		<?php
			if($enableeventsteps) {
				$style_a = "";
				$style_b = "";
				switch($todo_view) {
					default:
					case 'manage_inscr':
						$style_a = "text-decoration:underline;";
						$style_b = "text-decoration:none;";
						break;
					case 'manage_docs':
						$style_b = "text-decoration:underline;";
						$style_a = "text-decoration:none;";
						break;
				}

		?>
		<tr style="height:35px;">
			<td style="border-bottom:#3B567E 1px solid;padding-left:10px;background-color:#FFFFFF;">
				<a style="<?php echo $style_a; ?>" href="<?php echo $scriptenv; ?>?dims_mainmenu=events&dims_desktop=block&dims_action=public&action=view_events_summary&ssubmenu=<?php echo _DIMS_ADMIN_EVENTS_SUMMARY; ?>&todo_view=manage_inscr"><?php echo $_DIMS['cste']['_DIMS_NEWSLETTER_DMDINSC'].' ('.$nb_insc.')'; ?></a>&nbsp;|
				<a style="<?php echo $style_b; ?>" href="<?php echo $scriptenv; ?>?dims_mainmenu=events&dims_desktop=block&dims_action=public&action=view_events_summary&ssubmenu=<?php echo _DIMS_ADMIN_EVENTS_SUMMARY; ?>&todo_view=manage_docs"><?php echo $_DIMS['cste']['_DIMS_LABEL_EVT_DOC_REQ'].' ('.$nb_docs.')'; ?></a>
			</td>
		</tr>
		<?php
			}
		?>
		<tr>
			<td>
				<?php
					switch($todo_view) {
						default:
						case 'manage_inscr':
							?>
							<div style="margin-top:15px;">
								<table width="100%" style="border-collapse: collapse;">
								<?php
								if(count($tab_insc) > 0) {

									foreach($tab_insc as $idact => $tabact) {
										$dated = explode('-', $tabact['datejour']);
										$datej = $dated[2].'/'.$dated[1].'/'.$dated[0];

										$datef = explode('-', $tabact['datefin']);
										$dateff = $datef[2].'/'.$datef[1].'/'.$datef[0];

										echo	'<tr class="trl2">
													<td style="font-size:13px;font-weight:bold;">'.$tabact['libelle'].' ('.$datej.' - '.$dateff.')</td>
												</tr>';

										$cl='trl2';
										foreach($tabact['contact'] as $id_insc => $tab) {
											if($cl == 'trl1') $cl = 'trl2';
											else $cl = 'trl1';

											echo '<tr>
													<td align="center">
														<table width="100%" cellpadding="0" cellspacing="0">
															<tr class="'.$cl.'">
																<td width="95%">
																	<a href="javascript:void(0);" onclick="javascript:validate_inscription('.$id_insc.', '.$idact.');">
																	<table width="100%" cellpadding="0" cellspacing="0">
																		<tr>
																			<td align="right" width="20%">'.$_DIMS['cste']['_DIMS_LABEL_NAME'].' : </td>
																			<td align="left" style="font-weight:bold;" width="25%">&nbsp;'.$tab['name'].'</td>
																			<td align="right" width="20%">'.$_DIMS['cste']['_DIMS_LABEL_COMPANY'].' : </td>
																			<td align="left" width="25%">&nbsp;'.$tab['company'].'</td>
																		</tr>
																		<tr>
																			<td align="right" width="20%">'.$_DIMS['cste']['_DIMS_LABEL_ADDRESS'].' : </td>
																			<td align="left" width="25%">&nbsp;'.$tab['adresse'].'</td>
																			<td align="right" width="20%">'.$_DIMS['cste']['_PHONE'].' : </td>
																			<td align="left" width="25%">&nbsp;'.$tab['phone'].'</td>
																		</tr>
																		<tr >
																			<td align="right" width="20%">'.$_DIMS['cste']['_DIMS_LABEL_EMAIL'].' : </td>
																			<td align="left" width="25%">&nbsp;'.$tab['email'].'</td>
																			<td align="right" width="20%">'.$_DIMS['cste']['_DIMS_LABEL_FUNCTION'].': </td>
																			<td align="left" width="25%">&nbsp;'.$tab['fonction'].'</td>
																		</tr>
																	</table>
																	</a>
																</td>
																<td align="center">
																	<a href="javascript:void(0);" onclick="javascript:validate_inscription('.$id_insc.', '.$idact.');">
																		<img src="./common/img/checkdo.png"/>
																	</a>&nbsp;<a href="admin.php?dims_mainmenu=events&action=cancel_insc&id_evt='.$idact.'&id_insc='.$id_insc.'">
																		<img alt="'.$_DIMS['cste']['_DELETE'].'" src="./common/img/delete.png"/>
																	</a>
																</td>
															</tr>

														</table>
													</td>
												 </tr>';
										}
										echo '<tr><td>&nbsp;</td></tr>';
									}
								}
								echo '</table>';
							break;

						case 'manage_docs':
							?>
							<div style="margin-top:15px;">
								<table width="100%" style="border-collapse: collapse;">
								<?php
								if(count($tab_doc) > 0) {
									$class='trl1';
									foreach($tab_doc as $idact => $tabact) {

										$dated = explode('-', $tabact['datejour']);
										$datej = $dated[2].'/'.$dated[1].'/'.$dated[0];

										$datef = explode('-', $tabact['datefin']);
										$dateff = $datef[2].'/'.$datef[1].'/'.$datef[0];

										echo '<tr class="trl2">
													<td style="font-size:13px;font-weight:bold;width:25%;">'.$tabact['libelle'].' ('.$datej.' - '.$dateff.')</td>
													<td align="left" width="30%">'.$_DIMS['cste']['_DIMS_MILESTONE'].'</td>
													<td align="left" width="30%">'.$_DIMS['cste']['_DOCS'].'</td>
													<td align="left" width="10%">'.$_DIMS['cste']['_DIMS_LABEL_DATE_RECEPTION_DOC'].'</td>
													<td align="left" width="5%">'.$_DIMS['cste']['_DIMS_ACTIONS'].'</td>
											</tr>';

										foreach($tabact['contact'] as $id_ct => $tabcont) {
											echo '<tr class="'.$class.'">

																<td align="left" style="vertical-align:top;">'.$tabcont['name'].'</td>
																<td align="left" colspan="4">
																	<table width="100%" cellpadding="0" cellspacing="0">';
											$cl = 'trl2';
											foreach($tabcont['steps'] as $id_step => $tab_step) {
												foreach($tab_step['docs'] as $idfu => $tab_doc ) {

													$docinf = new docfile();
													$docinf->open($tab_doc['id_doc']);

													//dims_print_r($docinf->fields);
													$doc_url = $docinf->getwebpath();

													$dater = dims_timestamp2local($tab_doc['date_recep']);

													if($cl == 'trl1') $cl = 'trl2';
													else $cl = 'trl1';
													echo			   '<tr class="'.$cl.'">
																			<td width="40%">'.$tab_step['label'].'</td>
																			<td width="40%"><a target="_blank" href="'.$doc_url.'">'.$docinf->fields['name'].'</a></td>
																			<td width="14%">'.$dater['date'].'</td>
																			<td width="6%">
																				<a href="javascript:void(0);" onclick="javascript:validateDoc(\'\',\''.$idfu.'\',\''.$id_step.'\');">
																					<img src="./common/img/checkdo2.png"/>
																				</a> &nbsp;';

																				echo '<a href="javascript:void(0);" onclick="javascript:changeReceptionDoc(\''.$idfu.'\', 0)">
																					<img src="./common/img/delete.png" title="'.$_DIMS['cste']['_DIMS_LABEL_CANCEL'].'"/>
																				</a>';

													echo '						  </td>
																		</tr>';
												}
											}
											echo					'</table>
																</td>

												  </tr>';
										}
										echo '<tr><td colspan="2">&nbsp;</td></tr>';
									}
								}
								else {
									echo '<tr><td align="center" style="font-size:13px;">'.$_DIMS['cste']['_DIMS_LABEL_NO_DOCS_TO_VALID'].'</td></tr>';
								}
								?>
								</table>
							</div>
							<?php
						break;
					}
				?>
			</td>
		</tr>
	</table>
<?
}
?>
