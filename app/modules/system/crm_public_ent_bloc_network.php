<?php
	//echo $skin->open_simplebloc(_DIMS_LABEL_OPROFIL.' : ','100%');
	//$action_b = dims_load_securvalue('action', dims_const::_DIMS_NUM_INPUT, true, true);
	if(!empty($ent_id))		{
		$ct = $ent_id;
		$url = "admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_ENT_INTELL."&part="._BUSINESS_TAB_ENT_IDENTITE."&id_ent=".$ct;
	}
	if(!empty($contact_id)) {
		$ct = $contact_id;
		$url = "admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_CONTACT_INTELL."&part="._BUSINESS_TAB_CT_INTEL_PERS."&contact_id=".$ct;
	}

	//echo $skin->open_simplebloc(_DIMS_LABEL_OPROFIL.' : ','100%');
	//$action_b = dims_load_securvalue('action', dims_const::_DIMS_NUM_INPUT, true, true);


	//require_once(DIMS_APP_PATH . '/modules/system/class_ct_link.php');
	//require_once(DIMS_APP_PATH . '/modules/system/class_ent_contact.php');
	require_once(DIMS_APP_PATH . '/modules/system/class_contact.php');
	require_once(DIMS_APP_PATH . '/modules/system/class_tiers.php');

	$count_pp = 0;
	$count_pe = 0;

		$tiersnetwork = new tiers();
		$tiersnetwork->open($ent_id);
	$workspace = new workspace();
	$workspace->open($_SESSION['dims']['workspaceid']);
	$lstworkpaces=$workspace->getWorkspaceShareObject(dims_const::_SYSTEM_OBJECT_CONTACT);
	$inworkspace = '';
	foreach($lstworkpaces as $key => $tab) {
		$inworkspace .= "'".$key."',";
	}
	$inworkspace .= "'".$_SESSION['dims']['workspaceid']."'";

	//on va chercher le dernier lien tiers / pers
	/*$sql_pp = "	SELECT	DISTINCT tc.id
				FROM	dims_mod_business_tiers_contact tc
				INNER JOIN dims_mod_business_contact byc
				ON		byc.id = tc.id_ct_user_create
				AND		tc.id_tiers = ".$ent_id."
				AND		tc.id_workspace IN (".$inworkspace.")";*/
	$sql_pp ="	SELECT	DISTINCT p.lastname, p.firstname,
						tc.*,
						byc.lastname as by_lastname, byc.firstname as by_firstname
				FROM	dims_mod_business_contact p
				INNER JOIN dims_mod_business_tiers_contact tc
				ON		tc.id_contact = p.id
				AND		tc.id_tiers = :idtiers
				AND		tc.id_workspace IN (".$inworkspace.")
				LEFT JOIN dims_mod_business_contact byc
				ON		byc.id = tc.id_ct_user_create";
//echo $sql_pp;
	$res_pp = $db->query($sql_pp, array(
		':idtiers' => $ent_id
	));
	$count_pp = $db->numrows($res_pp);
	//if($db->numrows($res_pp) > 0) {
	//	$tab_pp = $db->fetchrow($res_pp);
	//	$date_cree = dims_timestamp2local($tab_pp['time_create']);
	//
	//	if($tab_pp['id_contact1'] == $ct)  {
	//		$lk_to_pp = $tab_pp['id_contact2'];
	//	}else {
	//		$lk_to_pp = $tab_pp['id_contact1'];
	//	}
	//
	//	$ent2 = new tiers();
	//	$ent2->open($lk_to_pp);
	//}

	//dernier lien pers / ent
	$sql_ppg = "SELECT	DISTINCT l.id_contact2
				FROM	dims_mod_business_ct_link l
				INNER	JOIN dims_mod_business_tiers t
				ON		t.id = id_contact2
				WHERE	l.id_workspace IN (".$inworkspace.")
				AND		l.id_contact1 = :ct
				AND		l.id_object = :idobject ";
//echo $sql_ppg."<br/>";
	$res_ppg = $db->query($sql_ppg, array(
		':ct'		=> $ct,
		':idobject' => dims_const::_SYSTEM_OBJECT_TIERS
	));
	$not_in = '0';
	$nb_ct1 = 0;
	while($tab_ppg = $db->fetchrow($res_ppg)) {
		$not_in .= ",".$tab_ppg['id_contact2'];
		$nb_ct1++;
	}

	//on prend ensuite tous les liens avec le ct2
	$sql_ppm = "SELECT	DISTINCT l.id_contact1
				FROM	dims_mod_business_ct_link l
				INNER JOIN dims_mod_business_tiers t
				ON		t.id = id_contact1
				WHERE	l.id_workspace IN (".$inworkspace.")
				AND		l.id_contact2 = :ct
				AND		l.id_contact1 NOT IN (".$not_in.")
				AND		l.id_object = :idobject "; //AND		id_workspace = ".$_SESSION['dims']['workspaceid']
//echo $sql_ppm;
	$res_ppm = $db->query($sql_ppm, array(
		':ct'		=> $ct,
		':idobject' => dims_const::_SYSTEM_OBJECT_TIERS
	));
	$nb_ct2 = $db->numrows($res_ppm);

	$count_pe = $nb_ct1 + $nb_ct2;
	//if($db->numrows($res_pe) > 0) {
	//	$tab_pe = $db->fetchrow($res_pe);
	//
	//	$date_cree2 = dims_timestamp2local($tab_pe['date_create']);
	//
	//
	//	$pers = new contact();
	//	$pers->open($tab_pe['id_contact']);
	//}

	//dernier lien pers/evt
	//a faire

	echo $skin->open_widgetbloc($_DIMS['cste']['_DIMS_LABEL_OPROFIL'], 'width:100%;', 'font-weight:bold;padding-bottom:2px;padding-left:10px;vertical-align:bottom;', './common/img/contacts.png','26px', '26px', '-17px', '-5px', $url, '', '');
?>
					<table width="100%" cellpadding="1" cellspacing="0" style="margin-top:2px;margin-bottom:2px;">
						<tbody>
							<tr class="trl1">
								<td style="width: 15%;padding-left:3px;"></td>
								<td style="width: 85%;"><?
								// $_SESSION['business']['lastent']
								echo "<span style='display:block;'>".dims_create_button($_DIMS['cste']['_ADD_CT'],'./common/img/add.gif','javascript:document.location.href=\'admin.php?cat=0&action=407&part=402&id_ent='.$ent_id.'&add_linkct=1\';return false;')."</span>";
								?></td>

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
							</tr>
							<tr class="trl1">
								<td style="cursor: default;padding-left:3px;" onmouseout="javascript:this.style.cursor='default';" onmouseover="javascript:this.style.cursor='pointer';" style="font-weight: bold; cursor: default;">
									<img src="./common/img/mini_ent.png"/>
								</td>
								<td style="cursor: default;padding-left:3px;" onmouseout="javascript:this.style.cursor='default';" onmouseover="javascript:this.style.cursor='pointer';" style="font-weight: bold; cursor: default;">
								<?
									echo "<a href=\"".$url."\">".$count_pe."&nbsp;";
									if($count_pe > 1) echo strtolower($_DIMS['cste']['_DIMS_LABEL_LINKS'])." ".$_DIMS['cste']['_DIMS_LABEL_GROUP_LIST'];
									else echo strtolower($_DIMS['cste']['_DIMS_LABEL_URL'])." ".$_DIMS['cste']['_DIMS_LABEL_COMPANY'];
									echo "</a>";
								?>
								</td>
							</tr>
							<tr class="trl1">
								<td colspan="4" align="right">
									<?
									// calcul du host � utiliser
									// base64_encode
									$url= urlencode(base64_encode($dims->getProtocol().$dims->getHttpHost()."/admin-light.php?dims_op=socialbrowser&xml_id="));
									//$logo=urlencode(base64_encode($dims->getProtocol().$dims->getHttpHost()."/lfb.png"));
									$src_w=0;
									$src_h=0;
									$logo='';
									$path=realpath('./')."./common/templates/backoffice".$_SESSION['dims']['currentworkspace']['admin_template']."/logo/socialnetwork_".$_SESSION['dims']['currentlang'].".png";

									if (file_exists($path)) {
										$size = GetImageSize($path);
										$src_w = $size[0];
										$src_h = $size[1];
										$logo=urlencode(base64_encode($dims->getProtocol().$dims->getHttpHost()."./common/templates/backoffice".$_SESSION['dims']['currentworkspace']['admin_template']."/logo/socialnetwork_".$_SESSION['dims']['currentlang'].".png"));
									}

									echo '<a href="admin-light.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_GRAPH.'&max_visible=1000&max_ops=10&logowidth='.$src_w.'&logoheight='.$src_h.'&logo='.$logo.'&xml_id=ent_'.$ct.'&url='.$url.'" target="_blank"><img src="./common/img/public.png" alt="" border="0">&nbsp;
																			'.$_DIMS['cste']['_DIMS_LABEL_SOCIAL_NETWORK']." ".dims_strcut($tiersnetwork->fields['intitule'],30).'</a>';?>
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
									echo '<a href="admin-light.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_GRAPH.'&max_visible=1000&max_ops=10&logowidth=152&logoheight=68&logo='.$logo.'&xml_id=ent_'.$ct.'&url='.$url.'" target="_blank"><img src="./common/img/public.png" alt="" border="0">&nbsp;Voir ses contacts</a>';?>
								</td>
							</tr>
							*/
							?>
						</tbody>
					</table>
<?
	echo $skin->close_widgetbloc();
?>
