<?php

	//echo $skin->open_simplebloc(_DIMS_LABEL_OPROFIL.' : ','100%');
	//$action_b = dims_load_securvalue('action', dims_const::_DIMS_NUM_INPUT, true, true);
	if(!empty($id_ent))		{
		$ct = $id_ent;
		$url = "admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_ENT_INTELL."&part="._BUSINESS_TAB_ENT_IDENTITE."&id_ent=".$ct;
	}
	if(!empty($contact_id)) {
		$ct = $contact_id;
		$url = "admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_CONTACT_INTELL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$ct;
	}

	//require_once(DIMS_APP_PATH . '/modules/system/class_ct_link.php');
	//require_once(DIMS_APP_PATH . '/modules/system/class_ent_contact.php');
	require_once(DIMS_APP_PATH . '/modules/system/class_contact.php');
	require_once(DIMS_APP_PATH . '/modules/system/class_tiers.php');
	require_once DIMS_APP_PATH . '/modules/system/class_action.php';

	//on va compter les liens pers / pers
	$count_pp = 0;
	$count_ppg = 0;
	$count_ppm = 0;
	$count_pe = 0;
	$count_peg = 0;
	$count_pem = 0;
	$count_evt = 0;

	//$sql_pp = "	SELECT	*
	//			FROM	dims_mod_business_ct_link
	//			WHERE	1
	//			AND		(id_contact1 = $ct OR id_contact2 = $ct)
	//			AND		id_object = ".dims_const::_SYSTEM_OBJECT_CONTACT."
	//			ORDER BY time_create DESC
	//			LIMIT 0,1";


	$workspace = new workspace();
	$workspace->open($_SESSION['dims']['workspaceid']);
	$lstworkpaces=$workspace->getWorkspaceShareObject(dims_const::_SYSTEM_OBJECT_CONTACT);
	$inworkspace = '';
	foreach($lstworkpaces as $key => $tab) {
		$inworkspace .= "'".$key."',";
	}
	$inworkspace .= "'".$_SESSION['dims']['workspaceid']."'";

	//on compte les liens (qqsoit le niveau)
	//on prend d'abord tous les liens avec le ct1
	$sql_ppg = "SELECT	DISTINCT ct.id_contact2
				FROM	dims_mod_business_ct_link ct
				INNER JOIN dims_mod_business_contact c
				ON		c.id = ct.id_contact2
				WHERE	ct.id_workspace IN (".$inworkspace.")
				AND		ct.id_contact1 = :idcontact
				AND		ct.id_object = :idobject ";

	/*$sql_ppg = "	SELECT	COUNT(id) as nb_link_pers_gen
				FROM	dims_mod_business_ct_link
				WHERE	link_level = 1
				AND		id_workspace IN (".$inworkspace.")
				AND		((id_contact1 = $ct AND id_contact2 != $ct) OR (id_contact2 = $ct AND id_contact1 != $ct))
				AND		id_object = ".dims_const::_SYSTEM_OBJECT_CONTACT;*/

	$res_ppg = $db->query($sql_ppg, array(
		':idcontact' 	=> $ct,
		':idobject' 	=> dims_const::_SYSTEM_OBJECT_CONTACT
	));
	$not_in = '0';
	$nb_ct1 = 0;
	while($tab_ppg = $db->fetchrow($res_ppg)) {
		$not_in .= ",".$tab_ppg['id_contact2'];
		$nb_ct1++;
	}
	//on prend ensuite tous les liens avec le ct2
	$sql_ppm = "SELECT	DISTINCT ct.id_contact1
				FROM	dims_mod_business_ct_link ct
				INNER JOIN dims_mod_business_contact c
				ON		c.id = ct.id_contact1
				WHERE	ct.id_workspace IN (".$inworkspace.")
				AND		ct.id_contact2 = :idcontact
				AND		ct.id_contact1 NOT IN (".$not_in.")
				AND		ct.id_object = :idobject "; //AND	id_workspace = ".$_SESSION['dims']['workspaceid']
//echo $sql_ppm;
	$res_ppm = $db->query($sql_ppm, array(
		':idcontact' 	=> $ct,
		':idobject' 	=> dims_const::_SYSTEM_OBJECT_CONTACT
	));
	$nb_ct2 = $db->numrows($res_ppm);

	$count_pp = $nb_ct1 + $nb_ct2;

	//$date_cree = dims_timestamp2local($tab_pp['time_create']);

	//if($tab_pp['id_contact1'] == $ct)  {
	//	$lk_to_pp = $tab_pp['id_contact2'];
	//}else {
	//	$lk_to_pp = $tab_pp['id_contact1'];
	//}
	//
	//$pers = new contact();
	//$pers->open($lk_to_pp);

	//compte des liens pers / ent
	//on compte les liens generiques
	/*$sql_peg = "SELECT	COUNT(id) as nb_link_ent_gen
				FROM	dims_mod_business_tiers_contact
				WHERE	id_contact = $ct
				AND		id_workspace IN (".$inworkspace.")";*/

	$sql_peg = "	SELECT	COUNT(DISTINCT le.id_tiers) as nb_link_ent_gen

				FROM	dims_mod_business_tiers e
				INNER JOIN dims_mod_business_tiers_contact le
				ON		le.id_tiers = e.id
				AND		le.id_workspace IN (".$inworkspace.")
				AND		le.id_contact = :idcontact ";

	/*$sql_peg = "SELECT	COUNT(le.id_tiers) as nb_link_ent_gen
				FROM	dims_mod_business_tiers e
				INNER JOIN dims_mod_business_tiers_contact le
				ON		le.id_tiers = e.id
				AND		le.id_workspace IN (".$inworkspace.")
				INNER JOIN dims_mod_business_contact ct
				ON		ct.id = le.id_ct_user_create
				WHERE	le.id_contact = ".$contact_id;*/

	$res_peg = $db->query($sql_peg, array(
		':idcontact' => $contact_id
	));
	$tab_peg = $db->fetchrow($res_peg);
	$count_peg = $tab_peg['nb_link_ent_gen'];

	//on compte les liens metiers
//	$sql_pem = "SELECT	COUNT(id) as nb_link_ent_met
//				FROM	dims_mod_business_tiers_contact
//				WHERE	id_contact = $ct
//				AND		link_level = 2"; //AND	id_workspace = ".$_SESSION['dims']['workspaceid']
//
//	$res_pem = $db->query($sql_pem);
//	  $tab_pem = $db->fetchrow($res_pem);
//	$count_pem = $tab_pem['nb_link_ent_met'];

	$count_pe = $count_pem + $count_peg;

	//if(isset($tab_pe['id_tiers']) && $tab_pe['id_tiers'] != 0){
	//	$date_cree2 = dims_timestamp2local($tab_pe['date_create']);
	//	$ent = new tiers();
	//	$ent->open($tab_pe['id_tiers']);
	//}
	//else{
	//	unset($tab_pe);
	//	$date_cree2="";
	//}


	//compte des liens pers/evt
	$sql_evt = "SELECT DISTINCT
					a.id,
					a.libelle,
					u.lastname,
					u.firstname,
					u.id_contact as id_ct
				FROM
					dims_mod_business_action a
				INNER JOIN
					dims_user u
					ON
						a.id_user = u.id
				INNER JOIN
					dims_mod_business_event_inscription ev_ins
					ON
						ev_ins.id_action = a.id
				WHERE
					a.type = :type
				AND
					(u.id_contact = :idcontact
					OR
						(ev_ins.id_contact = :idcontact
						AND
						ev_ins.validate = 2))";

	$res_evt = $db->query($sql_evt, array(
		':idcontact' 	=> $contact->fields['id'],
		':type' 		=> dims_const::_PLANNING_ACTION_EVT
	));
	$count_evt = $db->numrows($res_evt);

	echo $skin->open_widgetbloc($_DIMS['cste']['_DIMS_LABEL_OPROFIL'], 'width:100%;', 'font-weight:bold;padding-bottom:2px;padding-left:10px;vertical-align:bottom;', './common/img/contacts.png','26px', '26px', '-17px', '-5px', $url, '', '');
?>
					<table width="100%" cellpadding="1" cellspacing="0" style="margin-top:2px;margin-bottom:2px;">
						<tbody>
							<tr class="trl1">
								<td style="width: 15%;padding-left:3px;"></td>
								<td style="width: 85%;"><? //echo $_DIMS['cste']['_DIMS_LABEL_CT_LINKED'] ?></td>
								<!--<td style="width: 25%;"><? //echo $_DIMS['cste']['_TYPE'] ?></td>
								<td style="width: 25%;"></td>-->
							</tr>
							<tr class="trl2">
								<td style="cursor: default;padding-left:3px;" onmouseout="javascript:this.style.cursor='default';" onmouseover="javascript:this.style.cursor='pointer';" style="font-weight: bold; cursor: default;">
									<img src="./common/img/user.png"/>
								</td>
								<td style="cursor: default;padding-left:3px;" onmouseout="javascript:this.style.cursor='default';" onmouseover="javascript:this.style.cursor='pointer';" style="font-weight: bold; cursor: default;">
								<?
									echo "<a href=\"".$url."\">".$count_pp."&nbsp;";
									if($count_pp > 1) echo strtolower($_DIMS['cste']['_DIMS_LABEL_LINKS'])." ".$_DIMS['cste']['_DIMS_LABEL_PERSONNE'];
									else echo strtolower($_DIMS['cste']['_DIMS_LABEL_URL'])." ".$_DIMS['cste']['_DIMS_LABEL_CONTACT'];
									echo "</a>";
								?>
								</td>
								<!--<td onmouseout="javascript:this.style.cursor='default';" onmouseover="javascript:this.style.cursor='pointer';" style=" cursor: default;font-weight:normal;" id="tickets_title_3">
									<? //if(!empty($tab_pp['type_link'])) echo $tab_pp['type_link']; else echo "--"; ?>
								</td>
								<td onmouseout="javascript:this.style.cursor='default';" onmouseover="javascript:this.style.cursor='pointer';" style=" cursor: default;font-weight:normal;" id="tickets_title_3">
									<? //if(!empty($tab_pp['time_create'])) echo $date_cree['date']; else echo "--"; ?>
								</td>-->
							</tr>
							<tr class="trl1">
								<td style="cursor: default;padding-left:3px;" onmouseout="javascript:this.style.cursor='default';" onmouseover="javascript:this.style.cursor='pointer';" style="font-weight: bold; cursor: default;">
									<img src="./common/img/mini_ent.png"/>
								</td>
								<td style="cursor: default;padding-left:3px;" onmouseout="javascript:this.style.cursor='default';" onmouseover="javascript:this.style.cursor='pointer';" style="font-weight: bold; cursor: default;">
								<?
									echo "<a href=\"".$url."\">".$count_pe."&nbsp;";
									if($count_pe > 1) echo strtolower($_DIMS['cste']['_DIMS_LABEL_LINKS'])." ".$_DIMS['cste']['_DIMS_LABEL_COMPANY'];
									else echo strtolower($_DIMS['cste']['_DIMS_LABEL_URL'])." ".$_DIMS['cste']['_DIMS_LABEL_COMPANY'];
									echo "</a>";
								?>
								</td>
								<!--<td onmouseout="javascript:this.style.cursor='default';" onmouseover="javascript:this.style.cursor='pointer';" style=" cursor: default;font-weight:normal;" id="tickets_title_3">
									<? //if(isset($tab_pe)) echo stripslashes($tab_pe['type_lien']); else echo "--"; ?>
								</td>
								<td onmouseout="javascript:this.style.cursor='default';" onmouseover="javascript:this.style.cursor='pointer';" style=" cursor: default;font-weight:normal;" id="tickets_title_3">
									<? //if(isset($tab_pe)) echo $date_cree2['date']; else echo "--"; ?>
								</td>-->
							</tr>
							<tr class="trl2">
								<td style="cursor: default;padding-left:3px;" onmouseout="javascript:this.style.cursor='default';" onmouseover="javascript:this.style.cursor='pointer';" style="font-weight: bold; cursor: default;">
									<img src="./common/img/event.png"/>
								</td>
								<td style="cursor: default;padding-left:3px;" onmouseout="javascript:this.style.cursor='default';" onmouseover="javascript:this.style.cursor='pointer';" style="font-weight: bold; cursor: default;">
									<?
										//if(!empty($tab_evt['libelle'])) echo $tab_evt['libelle']; else echo "--";
										echo "<a href=\"".$url."\">".$count_evt."&nbsp;";
										if($count_evt > 1) echo strtolower($_DIMS['cste']['_DIMS_LABEL_LINKS'])." ".$_DIMS['cste']['_DIMS_LABEL_EVENTS'];
										else echo strtolower($_DIMS['cste']['_DIMS_LABEL_URL'])." ".$_DIMS['cste']['_DIMS_LABEL_EVENT'];
										echo "</a>";
									?>
								</td>
								<!--<td onmouseout="javascript:this.style.cursor='default';" onmouseover="javascript:this.style.cursor='pointer';" style=" cursor: default;font-weight:normal;" id="tickets_title_3" colspan="2">
									<? //if(!empty($tab_evt['lastname'])) echo '<a href="admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_FORM.'&part='._BUSINESS_TAB_CONTACT_IDENTITE.'&contact_id='.$tab_evt['id_ct'].'">'.$tab_evt['firstname']." ".$tab_evt['lastname'].'</a>'; else echo "--"; ?>
								</td>-->
							</tr>
							<tr class="trl1">
								<td colspan="4" align="right">
									<?
									// calcul du host � utiliser
									// base64_encode
									$url= urlencode(base64_encode($dims->getProtocol().$dims->getHttpHost()."/admin-light.php?dims_op=socialbrowser&xml_id="));
									$src_w=0;
									$logo='';
									$src_h=0;
									$path=realpath('./')."./common/templates/backoffice".$_SESSION['dims']['currentworkspace']['admin_template']."/logo/socialnetwork_".$_SESSION['dims']['currentlang'].".png";

									if (file_exists($path)) {
										$size = GetImageSize($path);
										$src_w = $size[0];
										$src_h = $size[1];
										$logo=urlencode(base64_encode($dims->getProtocol().$dims->getHttpHost()."./common/templates/backoffice".$_SESSION['dims']['currentworkspace']['admin_template']."/logo/socialnetwork_".$_SESSION['dims']['currentlang'].".png"));
									}

									echo '<a href="admin-light.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_GRAPH.'&max_visible=1000&max_ops=10&logowidth='.$src_w.'&logoheight='.$src_h.'&logo='.$logo.'&xml_id=ct_'.$ct.'&url='.$url.'" target="_blank"><img src="./common/img/public.png" alt="" border="0">&nbsp;
																			'.$_DIMS['cste']['_DIMS_LABEL_SOCIAL_NETWORK']." ".$contact->fields['firstname']." ".$contact->fields['lastname'].'</a>';?>
								</td>
							</tr>
							<?
							/*
							<tr class="trl1">
								<td colspan="4" align="right">
									<?
									// calcul du host � utiliser
									// base64_encode
									$url= urlencode(base64_encode($dims->getProtocol().$dims->getHttpHost()."/admin-light.php?dims_op=socialbrowser&explorercontact=1&xml_id="));
									$logo=urlencode(base64_encode($dims->getProtocol().$dims->getHttpHost()."/lfb.png"));
									echo '<a href="admin-light.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_GRAPH.'&max_visible=1000&max_ops=10&logowidth=152&logoheight=68&logo='.$logo.'&xml_id=ct_'.$ct.'&url='.$url.'" target="_blank"><img src="./common/img/public.png" alt="" border="0">&nbsp;Voir ses contacts</a>';?>
								</td>
							</tr>
							 */
							?>
						</tbody>
					</table>
<?
	echo $skin->close_widgetbloc();
?>
