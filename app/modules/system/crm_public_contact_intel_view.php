<script language="JavaScript" type="text/JavaScript">
	function deleteLink(idlink) {
		var retour = dims_xmlhttprequest("admin.php","dims_mainmenu=<?php echo dims_const::_DIMS_MENU_CONTACT; ?>&cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_CONTACTSSEEK;?>&op=deletelinkbetweencts&id_link="+idlink,"");
		window.location.reload();
	}

	function modLink(idlink, id_ct) {
		//alert("ici");
		dims_xmlhttprequest_todiv("admin.php","dims_mainmenu=<?php echo dims_const::_DIMS_MENU_CONTACT; ?>&cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_CONTACTSSEEK;?>&op=modlinkbetweencts&id_link="+idlink+"&id_cont="+id_ct,"", 'dims_popup');
		dims_showcenteredpopup("popup", "550", "350", '',"dims_popup");
	}

	function deleteLinkEnt(idlink) {
		var retour = dims_xmlhttprequest("admin.php","dims_mainmenu=<?php echo dims_const::_DIMS_MENU_CONTACT; ?>&cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_CONTACTSSEEK;?>&op=dellkentct&id_lk="+idlink,"");
		window.location.reload();
	}

	function modLinkEnt(idlink, id_ent) {
		//alert("ici");
		dims_xmlhttprequest_todiv("admin.php","dims_mainmenu=<?php echo dims_const::_DIMS_MENU_CONTACT; ?>&cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_CONTACTSSEEK;?>&op=modlkentct&id_link="+idlink+"&id_ent="+id_ent,"", 'dims_popup');
		dims_showcenteredpopup("popup", "450", "300", '',"dims_popup");
	}

	function affiche_div(id_div) {
		var div_tochange = dims_getelem(id_div);
		if(div_tochange.style.display == 'block') div_tochange.style.display = 'none';
		else {
			div_tochange.style.display = 'block';
		}
	}

	function sendRequestInfo(id_record, type, rubcour) {
		var retour = dims_xmlhttprequest_todiv("admin.php","dims_mainmenu=<?php echo dims_const::_DIMS_MENU_CONTACT; ?>&cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_CONTACTSSEEK;?>&op=add_ticket&id_record="+id_record+"&type="+type+"&rubcour="+rubcour,"", 'dims_popup');
		dims_showcenteredpopup("popup", "450", "300", '',"dims_popup");
	}
	function contLink(id_pers, type) {
		var retour = dims_xmlhttprequest("admin.php","dims_mainmenu=<?php echo dims_const::_DIMS_MENU_CONTACT; ?>&cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_CONTACTSSEEK;?>&op=savelinkcts&id_cont="+id_pers+"&type="+type,"");
		alert(retour);
	}
</script>
<?php

$cat = dims_load_securvalue('cat',dims_const::_DIMS_NUM_INPUT,true,true);
//$dims_mainmenu = dims_load_securvalue('dims_mainmenu',dims_const::_DIMS_NUM_INPUT,true,true);
$tabscriptenv = "admin.php?cat=".$cat; //dims_mainmenu=".$dims_mainmenu."&

$part = dims_load_securvalue('part',dims_const::_DIMS_NUM_INPUT,true,true);
if(empty($part)) $part= _BUSINESS_TAB_CONTACT_IDENTITE;

$contact_id = dims_load_securvalue('contact_id',dims_const::_DIMS_NUM_INPUT,true,true);
if($contact_id == "") $contact_id = $_SESSION['business']['contact_id'];
////echo "contact_id : ".$contact_id;
$contact= new contact();
if ($contact_id>0) {
	$contact->open($contact_id);
	$_SESSION['business']['contact_id']=$contact_id;
//	//on ajoute la date de visite a la liste des fiches en veille
//	//on verifie si la fiche selectionnee fait partie des fiches en veille
//	$sql_t = "SELECT time_lastseen FROM dims_mod_business_ct_watch WHERE id_user = ".$_SESSION['dims']['userid']." AND id_personne = ".$contact_id;
//	//echo $sql_t;
//	$res_t = $db->query($sql_t);
//	$nb_res = $db->numrows($res_t);
//
//	if($nb_res > 0) {
//		$sql_maj = "UPDATE `dims_mod_business_ct_watch` SET `time_lastseen` = '".dims_createtimestamp()."' WHERE id_user = ".$_SESSION['dims']['userid']." AND id_personne = ".$contact_id;
//		$db->query($sql_maj);
//	}
}


?>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td style="width:25%;vertical-align:top;">
			<?php
				require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_bloc_profil.php');
				require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_bloc_network.php');
				echo $skin->open_simplebloc('','100%', 'font-size:13px;');
				echo dims_create_button($_DIMS['cste']['_DIMS_BACK'],"./common/img/undo.gif","","","",dims_urlencode("$tabscriptenv&action="._BUSINESS_TAB_CONTACT_FORM."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$ct));
				echo $skin->close_simplebloc();
			?>
		</td>
		<td align="center" style="vertical-align:top;padding-left:5px;">
			<? //echo $skin->open_widgetbloc($title,'font-weight:bold;width:100%','',''); ?>
			<table width="100%" cellpadding="0" cellspacing="2" >
				<tr>
					<td style="vertical-align:top;">

							<table width="100%" cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td width="100%" style="vertical-align:top;">
									<? require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_intel_linkp.php'); ?>
									</td>
								</tr>
								<tr>
									<td style="vertical-align:top;" width="100%">
									<? require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_intel_linkent.php'); ?>
									</td>
								</tr>
								<tr>
									<td width="100%" style="vertical-align:top;">
									<? require_once(DIMS_APP_PATH . '/modules/system/lfb/lfb_public_contact_intel_linkevt.php'); ?>
									</td>
								</tr>
							</table>
					</td>
				</tr>
			</table>
		</td>

	</tr>
</table>
