<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once(DIMS_APP_PATH . "/modules/system/class_contact.php");
$contact= new contact();

if (isset($_SESSION['dims']['current_object']['id_record']) && $_SESSION['dims']['current_object']['id_record']>0) {
	$contact_id=$_SESSION['dims']['current_object']['id_record'];
}
if (!isset($disabledbloc) || $disabledbloc==false)
echo $skin->open_widgetbloc($_DIMS['cste']['_DIMS_LABEL_CURPROFIL'], 'width:100%', 'font-weight:bold;padding-bottom:2px;padding-left:10px;vertical-align:bottom;', './common/img/widget_zoom.png','26px', '26px', '-17px', '-5px', "$tabscriptenv&action=".$action."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$contact_id, '', '');

if ($contact_id>0) {
	$contact->open($contact_id);
	$_SESSION['business']['contact_id']=$contact_id;

	//r�cup�ration des informations sur l'employeur
	$sql_ct = 	"SELECT id, id_tiers, function
				FROM dims_mod_business_tiers_contact
				WHERE id_contact = :idcontact
				AND type_lien LIKE :typelien
				AND date_fin = 0";

	$res_ct = $db->query($sql_ct, array(
		':idcontact' 	=> $contact->fields['id'],
		':typelien' 	=> $_DIMS['cste']['_DIMS_LABEL_EMPLOYEUR']
	));
	$tab_lemp = $db->fetchrow($res_ct);
	if(!empty($tab_lemp)) {
		$tiers = new tiers();
		$tiers->open($tab_lemp['id_tiers']);
	}

	$sql_user = "SELECT id, presentation FROM dims_user WHERE id_contact = :idcontact ";
	$res_u = $db->query($sql_user, array(
		':idcontact' 	=> $contact->fields['id']
	));

	// filtre sur les champs importants
	$lstfield=$contact->getDynamicFields();

$contact2=new contact();
$contact2->open($contact->fields['id']);

// construction des deux autres dimensions
$contactworkspace = new contact_layer();
$contactworkspace->init_description();
$contactuser = new contact_layer();
$contactuser->init_description();

if (isset($contact->fields['id']) && $contact->fields['id']>0) {
	// requete selection layer
	$sql =	"
				SELECT		*
				FROM		dims_mod_business_contact_layer
				WHERE		id = :idrecord
				AND			((type_layer<=1 and id_layer= :idfrom )
				OR			(type_layer=2 and id_layer= :userid )";

	// a ajouter : dimension partage
	$sqlshare = "select		*
				from		dims_share
				where		id_module=1
				and			id_object= :idobject
				and			id_record= :idrecord
				and			(type_from=0
				and			id_from= :idfrom
				and			level_from=0)
				OR			(type_share=1 and id_from= :idfrom )
				OR			(type_share=2 and id_from= :userid )";

	$res=$db->query($sqlshare, array(
		':idobject' 	=> dims_const::_SYSTEM_OBJECT_CONTACT,
		':idrecord' 	=> $contact->fields['id'],
		':idfrom' 		=> $_SESSION['dims']['workspaceid'],
		':userid' 		=> $_SESSION['dims']['userid']
	));

	if ($db->numrows($res)>0) {
		while ($f=$db->fetchrow($res)) {
			// test si share actif pour l'espace courant
			if ($f['type_from']==0 && $f['id_from']==$_SESSION['dims']['workspaceid'] && $f['level_from']==0) {
				$shareactive=true;
			}
			else {
				// on peut charger les autres valeurs
				$sql .= " OR (type_share=".$f['type_share']." and id_share=".$f['id_share'].")";
			}
		}
	}

	// on termine la requete par le tri
	$sql.=")			ORDER BY	timestp_modify,type_layer";

	$rs=$db->query($sql, array(
		':idrecord' 	=> $contact->fields['id'],
		':idfrom' 		=> $_SESSION['dims']['workspaceid'],
		':userid' 		=> $_SESSION['dims']['userid']
	));

	if ($db->numrows($rs)>0) {
			while ($f=$db->fetchrow($rs)) {
				$layers[$f['type_layer']]=$f;
				$owner=false;

				// ouverture de l'objet concerne
				if ($f['type_layer']==1) {
						if ($f['id_layer']==$_SESSION['dims']['workspaceid']) {
								$contactworkspace->open($contact->fields['id'],1,$f['id_layer']);
								$owner=true;
						}
						else {

						}
				}
				else {
						if ($f['id_layer']==$_SESSION['dims']['userid']) {
								$contactuser->open($contact->fields['id'],2,$f['id_layer']);
								$owner=true;
						}
				}

				// on remplit les champs courants pour le layer concerne
				if ($owner) {
						foreach($f as $name=>$val) {
								if ($val!="" && isset($convmeta[$name])) {
										$idmeta=$convmeta[$name];
										if (!isset($_SESSION['dims']['contact_fields_view'][$idmeta]) || isset($_SESSION['dims']['contact_fields_view'][$idmeta]) && $_SESSION['dims']['contact_fields_view'][$idmeta]!=2) {
												$_SESSION['dims']['contact_fields_view'][$idmeta]=$f['type_layer']; // public
										}

										if ($f['type_layer']==1) {
												$contactworkspace->fields[$name]=$val;
										}
										else {
												$contactuser->fields[$name]=$val;
										}
								}
						} // end of foreach
				} // end of owner
			}
	}
}

?>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tbody>
				<tr>
					<td width="100%" style="vertical-align:top;">
						<table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-top:2px;margin-bottom:2px;">
							<tr >
								<td style="font-size:14px;" width="90%">
									<table style="width:100%" cellpadding="0" cellspacing="0">
										<tr>
											<td rowspan="4">
											<?php if($db->numrows($res_u) > 0) { ?>
											<a href="javascript:void(0);" onclick="javascript:affichePhoto('user_presentation');" title="<?php echo $_DIMS['cste']['_DIMS_TITLE_PRES_PERS']; ?>"><img src="./common/modules/system/img/contacts.png"/></a>
											<?php } ?>
											</td>
											<td><b><?php echo $contact->fields['firstname']." ".$contact->fields['lastname']; ?></b></td>
										</tr>
										<tr>
											<td>
											<?php if($contact->fields['partenaire'] == "1") { ?>
											<img src="./common/img/partenaire.png" title="<?php echo $_DIMS['cste']['_DIMS_TITLE_CT_PART_EVT'] ?>"/>
											<?php } ?>
											</td>
										</tr>
										<tr>
											<td>
												<?php if(!empty($tab_lemp)) echo '<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_ENT_FORM.'&part='._BUSINESS_TAB_ENT_IDENTITE.'&id_ent='.$tiers->fields['id'].'">'.$tiers->fields['intitule_search']."</a>"; ?>
											</td>
										</tr>
										<tr>
											<td>
												<?php if(!empty($tab_lemp)) echo $tab_lemp['function']; ?>
											</td>
										</tr>
									</table>
								</td>
								<td rowspan="2" align="right">
								<?php
									if($contact->fields['photo'] == "" && !file_exists(DIMS_WEB_PATH.'data/photo_cts/contact_'.$contact->fields['id'].'/photo60'.$contact->fields['photo'].'.png')) {
										echo '<img src="./common/img/photo_user.png"/>';
									}
									else {
										echo '<img src="'._DIMS_WEBPATHDATA.'photo_cts/contact_'.$contact->fields['id'].'/photo60'.$contact->fields['photo'].'.png" onclick="javascript:affichePhoto(\'big_photo\');" title="'.$_DIMS['cste']['_DIMS_TITLE_DISP_PHOTO'].'"/>';
									}
								?>
								<br/>
								<?php
										if($dims_op!='refreshDesktop') {
										$url = null;
										unset($_SESSION['dims']['options']);
										//Creation des option d'ajout au planning
										dims_createOptions($_SESSION['dims']['workspaceid'],$_SESSION['dims']['moduleid'],dims_const::_SYSTEM_OBJECT_CONTACT,$contact_id,'personne');

										//Option : Rendez-vous
										$url = dims_urlencode("javascript:contWatch('".$contact_id."', 'personne');dims_hidepopup();");
										dims_addOptions($_SESSION['dims']['workspaceid'],$_SESSION['dims']['moduleid'],dims_const::_SYSTEM_OBJECT_CONTACT,$contact_id,$url,"",$_DIMS['cste']['_DIMS_TITLE_TO_WATCH'],"","./common/img/add_view.png");
										//Option : Evenement
										$url = dims_urlencode("javascript:contLink('".$contact_id."', 'personne');document.location.reload();");
										dims_addOptions($_SESSION['dims']['workspaceid'],$_SESSION['dims']['moduleid'],dims_const::_SYSTEM_OBJECT_CONTACT,$contact_id,$url,"",$_DIMS['cste']['_DIMS_TITLE_ADD_TO_CT_PERS'],"","./common/img/add_user.png");

										//Affichage des options
										echo dims_create_button($_DIMS['cste']['_DIMS_ACTIONS'],"","javascript:displayOptions(event,'".$_SESSION['dims']['workspaceid']."','".$_SESSION['dims']['moduleid']."','".dims_const::_SYSTEM_OBJECT_CONTACT."','".$contact_id."','0');","","width:90px");
										}
								?>
								</td>
							</tr>
							<tr >
								<td style="">
									<table style="width:100%" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:22%;vertical-align:top;">
											<?php

											// recherche des champs de telephone
											foreach($lstfield as $k=>$f) {

												if ($f['format']=='phone') {

													$label=$f['labelfield'];
													if (isset($_DIMS['cste'][$label])) {
														$label=$_DIMS['cste'][$label];
													}
													else {
														$label=$f['name'];
													}

													echo "<span style='clear:both;float:left;width:80px;margin-top:3px;'>".$label."</span>";

													echo "<span style='float:left;'>";

													if (isset($contact->fields[$f['namefield']]) && $contact->fields[$f['namefield']]!='')	echo '<img src="./common/img/all.png">&nbsp;'.dims_format_phone($contact->fields[$f['namefield']])."<br>";
													if (isset($contactworkspace->fields[$f['namefield']]) && $contactworkspace->fields[$f['namefield']]!='')	echo '<img src="./common/img/users.png">&nbsp;'.dims_format_phone($contactworkspace->fields[$f['namefield']])."<br>";
													if (isset($contactuser->fields[$f['namefield']]) && $contactuser->fields[$f['namefield']]!='')	echo '<img src="./common/img/user.png">&nbsp;'.dims_format_phone($contactuser->fields[$f['namefield']])."<br>";

													echo "</span>";
												}
											}

											?>
											</td>
											<td style="vertical-align:top;">
											<?php
											// recherche des champs emails
											foreach($lstfield as $k=>$f) {

												if ($f['format']=='email') {

													$label=$f['labelfield'];
													if (isset($_DIMS['cste'][$label])) {
														$label=$_DIMS['cste'][$label];
													}
													else {
														$label=$f['name'];
													}

													echo "<span style='clear:both;float:left;width:50px;margin-top:3px;'>".$label."</span>";
													echo "<span style='float:left;'>";

													if (isset($contact->fields[$f['namefield']]) && $contact->fields[$f['namefield']]!='')	echo '<img src="./common/img/all.png">&nbsp;<a href="mailto:'.$contact->fields[$f['namefield']].'">'.$contact->fields[$f['namefield']]."</a><br>";
													if (isset($contactworkspace->fields[$f['namefield']]) && $contactworkspace->fields[$f['namefield']]!='')	echo '<img src="./common/img/users.png">&nbsp;<a href="mailto:'.$contactworkspace->fields[$f['namefield']].'">'.$contactworkspace->fields[$f['namefield']]."</a><br>";
													if (isset($contactuser->fields[$f['namefield']]) && $contactuser->fields[$f['namefield']]!='')	echo '<img src="./common/img/user.png">&nbsp;<a href="mailto:'.$contactuser->fields[$f['namefield']].'">'.$contactuser->fields[$f['namefield']]."</a><br>";

													echo "</span>";
												}
											}
											?>
											</td>
										</tr>

									</table>
								</td>
							</tr>
						</table>
					</td>
					<!--<td align="center" style="padding:3px;vertical-align:top;">
						<img src="./common/img/photo_user.png"/>
					</td>-->
				</tr>
			</tbody>
		</table>
		<div id="user_presentation" style="display:none;position:absolute;z-index:125;left:290px;top:50px;width:520px;height:320px;padding-left:10px;padding-top:10px;">
		<?php
			if($db->numrows($res_u) > 0) {
				$tab_user = $db->fetchrow($res_u);
				echo $skin->open_widgetbloc($_DIMS['cste']['_DIMS_PRESENTATION'], 'width:100%', 'font-weight:bold;padding-bottom:2px;padding-left:10px;vertical-align:bottom;', './common/img/no.png','21px', '21px', '-17px', '-5px', '', 'javascript:void(0);affichePhoto("user_presentation");', '');
				echo '	<table style="width:100%;background-color:#ffffff;" cellpadding="0" cellspacing="0">
							<tr>
								<td align="left">'.$tab_user['presentation'].'</td>
							</tr>
							<tr>
								<td align="center"><input type="button" class="flatbutton" value="'.$_DIMS['cste']['_DIMS_CLOSE'].'" onclick="javascript:void(0);affichePhoto(\'user_presentation\');"/></td>
							</tr>
						</table>';
				echo $skin->close_widgetbloc();
			}
			else {
				echo "";
			}
		?>
		</div>
		<div id="big_photo" style="display:none;position:absolute;z-index:125;left:250px;top:150px;width:320px;height:320px;padding-left:10px;padding-top:10px;">
		<?php
			if($contact->fields['photo'] == "") {
				echo '';
			}
			else {
				echo $skin->open_widgetbloc($_DIMS['cste']['_DIMS_LABEL_PHOTO'], 'width:100%', 'font-weight:bold;padding-bottom:2px;padding-left:10px;vertical-align:bottom;', './common/img/no.png','21px', '21px', '-17px', '-5px', '', 'javascript:void(0);affichePhoto(\'big_photo\');', '');
				if (file_exists(DIMS_WEB_PATH.'data/photo_cts/contact_'.$contact->fields['id'].'/photo300'.$contact->fields['photo'].'.png'))
					echo '<img src="'._DIMS_WEBPATHDATA.'photo_cts/contact_'.$contact->fields['id'].'/photo300'.$contact->fields['photo'].'.png" onclick="javascript:affichePhoto(\'big_photo\');"/>';
				elseif(file_exists(DIMS_WEB_PATH.'data/photo_cts/contact_'.$contact->fields['id'].'/photo60'.$contact->fields['photo'].'.png'))
					echo '<img src="'._DIMS_WEBPATHDATA.'photo_cts/contact_'.$contact->fields['id'].'/photo60'.$contact->fields['photo'].'.png" onclick="javascript:affichePhoto(\'big_photo\');"/>';
				else
					echo '<img src="./common/img/photo_user.png" onclick="javascript:affichePhoto(\'big_photo\');"/>';
				echo $skin->close_widgetbloc();
			}
		?>
		</div>
<?php
}

if (!isset($disabledbloc) || $disabledbloc==false)
	echo $skin->close_widgetbloc();
?>
