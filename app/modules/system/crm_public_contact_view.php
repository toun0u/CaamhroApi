<script language="JavaScript" type="text/JavaScript">
	function affich_blocinfo(id_div) {
		var div_to_open = dims_getelem(id_div);
		div_to_open.style.display="block";
	}

	function close_blocinfo(id_div) {
		var div_to_open = dims_getelem(id_div);
		div_to_open.style.display="none";
	}

	function contLink(id_pers, type) {
		var retour = dims_xmlhttprequest("admin.php","dims_mainmenu=<?php echo dims_const::_DIMS_MENU_CONTACT; ?>&cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_CONTACTSSEEK;?>&op=savelinkcts&id_cont="+id_pers+"&type="+type,"");
		alert(retour);
	}
</script>
<?php
require_once(DIMS_APP_PATH . "/modules/system/class_tiers.php");
require_once(DIMS_APP_PATH . "/modules/system/class_contact.php");

$cat = dims_load_securvalue('cat',dims_const::_DIMS_NUM_INPUT,true,true);
//$dims_mainmenu = dims_load_securvalue('dims_mainmenu',dims_const::_DIMS_NUM_INPUT,true,true);
$tabscriptenv = "admin.php?cat=".$cat; //dims_mainmenu=".$dims_mainmenu."&

//$part = dims_load_securvalue('part',dims_const::_DIMS_NUM_INPUT,true,true);
//if(empty($part)) $part= _BUSINESS_TAB_CONTACT_IDENTITE;

$contact_id = dims_load_securvalue('contact_id',dims_const::_DIMS_NUM_INPUT,true,true);

$contact= new contact();

if ($contact_id==0 && $_SESSION['business']['contact_id']>0) {
	$contact_id=$_SESSION['business']['contact_id'];
}
if ($contact_id>0) {
	if (isset($_SESSION['dims']['current_object']['id_record']) && $_SESSION['dims']['current_object']['id_record']!=$contact_id) {
		unset($_SESSION['dims']['current_object']['id_record']);
	}

	//unset($_SESSION['dims']['current_object']['id_record']);
	$contact->open($contact_id);
	$_SESSION['business']['contact_id']=$contact_id;
	//on ajoute la date de visite a la liste des fiches en veille
	//on verifie si la fiche selectionnee fait partie des fiches en veille
	$sql_t = "SELECT time_lastseen FROM dims_mod_business_ct_watch WHERE id_user = :iduser AND id_personne = :idpersonne ";
	//echo $sql_t;
	$res_t = $db->query($sql_t, array(
		':iduser'		=> $_SESSION['dims']['userid'],
		':idpersonne'	=> $contact_id
	));
	$nb_res = $db->numrows($res_t);

	if($nb_res > 0) {
		$sql_maj = "UPDATE `dims_mod_business_ct_watch` SET `time_lastseen` = '".dims_createtimestamp()."' WHERE id_user = :iduser AND id_personne = :idpersonne ";
		$db->query($sql_maj, array(
			':iduser'		=> $_SESSION['dims']['userid'],
			':idpersonne'	=> $contact_id
		));
	}
}
else {
	//dims_redirect('/admin.php?cat=0&action=41&part=41&view=1');
}
$workspace = new workspace();
$workspace->open($_SESSION['dims']['workspaceid']);
$lstworkpaces=$workspace->getWorkspaceShareObject(dims_const::_SYSTEM_OBJECT_CONTACT);
$ct=$contact_id;
//die("ici");

?>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td style="width:25%;vertical-align:top;">
			<?php
				require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_bloc_profil.php');
				switch($part) {
					default:
						if($contact->fields['inactif'] != 1) {
							if ($workspace->fields['contact_intel']) {
								require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_bloc_network.php');
							}

							if (!empty($lstworkpaces)) {
								require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_bloc_share.php');
							}

							if ($workspace->fields['contact_comments']) {
								require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_bloc_news.php');
							}

							require_once DIMS_APP_PATH.'modules/system/crm_public_contact_bloc_mail.php';

							if ($workspace->fields['contact_docs']) {
								require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_bloc_docs.php');
							}
							if ($workspace->fields['newsletter'] && ($_SESSION['dims']['user']['id_contact'] == $contact_id || dims_ismanager())) {
								require_once DIMS_APP_PATH.'modules/system/lfb/lfb_public_contact_bloc_newsletter.php';
							}

						}
						break;
					case 1:
						if($contact->fields['inactif'] != 1) {
							require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_bloc_news.php');

							if ($workspace->fields['contact_docs']) {
								require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_bloc_docs.php');
							}

							require_once DIMS_APP_PATH.'modules/system/crm_public_contact_bloc_mail.php';
							if ($workspace->fields['newsletter'] && ($_SESSION['dims']['user']['id_contact'] == $contact_id || dims_ismanager())) {
								require_once DIMS_APP_PATH.'modules/system/lfb/lfb_public_contact_bloc_newsletter.php';
							}
						}
						break;
					case 2:
						if($contact->fields['inactif'] != 1) {
							if ($workspace->fields['contact_intel']) {
								require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_bloc_network.php');
							}

							echo $skin->open_simplebloc('','100%', 'font-size:13px;');
							echo dims_create_button($_DIMS['cste']['_DIMS_BACK'],"./common/img/undo.gif","","","",dims_urlencode("$tabscriptenv&action=".$action."&part="._BUSINESS_TAB_ENT_IDENTITE."&id_ent=".$ct));
							echo $skin->close_simplebloc();
						}
						break;
					case 3:
						if($contact->fields['inactif'] != 1) {
							if ($workspace->fields['contact_intel']) {
								require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_bloc_network.php');
							}

							echo $skin->open_simplebloc('','100%', 'font-size:13px;');
							echo dims_create_button($_DIMS['cste']['_DIMS_BACK'],"./common/img/undo.gif","","","",dims_urlencode("$tabscriptenv&action=".$action."&part="._BUSINESS_TAB_ENT_IDENTITE."&id_ent=".$ct));
							echo $skin->close_simplebloc();
						}
						break;
				}

				if($contact->fields['inactif'] != 1) {
					//insertion du bloc de gestion des groupes
					require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_bloc_group_ct.php');

					if ($workspace->fields['contact_tags']) {
						echo $skin->open_widgetbloc($_DIMS['cste']['_DIMS_LABEL_TAGS'], 'width:100%;', 'padding-bottom:1px;padding-left:10px;vertical-align:bottom;color:#FFFFFF;font-weight: bold;', '','26px', '26px', '-15px', '-7px', '', '', '');

						// selection des tags
						echo "<div id=\"tagblockdisplay\">";
						echo dims_getBlockTag($dims, $_DIMS, $_SESSION['dims']['moduleid'], dims_const::_SYSTEM_OBJECT_CONTACT, $contact->fields['id']);
						echo "</div>";
						echo $skin->close_widgetbloc();
					}
					//informations sur la fiche
					$ct_create = new contact();
					$ct_create->open($contact->fields['id_user_create']);
					$datecreate=dims_timestamp2local($contact->fields['date_create']);
					//border:#536689 1px solid;
					echo '<div style="width:100%;">
								<table width="100%" cellpadding="0" cellspacing="0">
									<tr class="trl1">
										<td style="padding-left:5px;" align="left">';
											echo $_DIMS['cste']['_SYSTEM_LABEL_FICHCREATED']." : ";
					echo '				</td>
										<td style="padding-left:5px;" align="left">';
											echo $datecreate['date'];
											if ($ct_create->fields['id']>0 && isset($ct_create->fields['lastname'])) {
												$titleusr=$ct_create->fields['firstname']." ".$ct_create->fields['lastname'];
												echo " &nbsp;&nbsp;&nbsp;".$_DIMS['cste']['_DIMS_LABEL_FROM']." : ";
												echo "<a href=\"admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_CONTACT_FORM."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$ct_create->fields['id']."\">".$titleusr."</a>";
											}
					echo '					</td>
									</tr>';

					if ($contact->fields["timestp_modify"] != $contact->fields["date_create"]) {

					$datemodt = dims_timestamp2local($contact->fields["timestp_modify"]);
					$usr = new user();

					$usr->open($contact->fields["id_user"]);
					if (isset($usr->fields['lastname'])) {
						echo '			<tr class="trl1">
											<td style="padding-left:5px;" align="left">';
												echo $_DIMS['cste']['_DIMS_DATE_MODIFY']." : ";
						echo '				</td>
											<td style="padding-left:5px;" align="left">';
												echo $datemodt['date'];
												$titleusr=$usr->fields['firstname']." ".$usr->fields['lastname'];
												echo " &nbsp;&nbsp;&nbsp;".$_DIMS['cste']['_DIMS_LABEL_FROM']." : ";
												echo "<a href=\"admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_CONTACT_FORM."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$usr->fields['id_contact']."\">".$titleusr."</a>";
						echo '				</td>
										</tr>';
						}
					}
					echo '		</table>';
					echo '</div>';
					if(dims_ismanager()) {
						echo '<div style="width:100%;padding-right:10%;margin-top:10px;">';
						echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_DESACTIV_SHEET'],"./common/img/check.png","javascript:desactiveFiche(".$contact->fields['id'].", 'pers');","","");
						echo '</div>';
					}
				}
				else {
					echo '<div style="width:100%;padding-right:10%;margin-top:10px;">';
					echo '<span style="height:20px;"><img src="./common/img/warning.png"/></span><span style="height:20px;padding-left:10px;">'.$_DIMS['cste']['_DIMS_LABEL_FICHE_SUPPR'].'</span>';
					echo '</div>';
					if(dims_ismanager()) {
						echo '<div style="width:100%;padding-right:10%;margin-top:10px;">';
						echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_ACTIV_SHEET'],"./common/img/checkdo.png","javascript:activeFiche(".$contact->fields['id'].", 'pers');","","");
						echo '</div>';
					}
				}
				if(dims_ismanager()) {
					echo '<div style="width:100%;padding-right:10%;margin-top:10px;">';
					//echo '<input type="button" class="flatbutton" onclick="javascript:document.location.href=\'admin.php?dims_mainmenu=9&cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_HISTO.'&contact_id='.$contact->fields['id'].'\';" value="Historique de la fiche"/>';
					echo dims_create_button($_DIMS['cste']['_DIMS_HISTORY'],"./common/modules/doc/img/ico_archive.png",'document.location.href=\'admin.php?dims_mainmenu=9&cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_HISTO.'&contact_id='.$contact->fields['id'].'\';',"","");
					echo '</div>';
				}
				if(dims_isadmin()) {
					echo '<div style="width:100%;padding-right:10%;margin-top:10px;">';
					echo dims_create_button($_DIMS['cste']['_DELETE'],"./common/img/delete.png","javascript:dims_confirmlink('admin.php?dims_mainmenu=9&cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_CONTACT_DELETE_ADMIN."&contact_id=".$contact->fields['id']."&type=pers', '".$_DIMS['cste']['_DIMS_LABEL_CONFIRM_DELETE']."');","","");
					echo '</div>';
				}
			?>
		</td>
				<?
				if (isset($_SESSION['dims']['_DIMS_SPECIFIC']) && $_SESSION['dims']['_DIMS_SPECIFIC']) {
					echo '<td style="width:50%;vertical-align:top;text-align:center">';
				}
				else {
					echo '<td style="width:70%;vertical-align:top;text-align:center">';
				}
				?>
			<?php //echo $skin->open_widgetbloc($title,'font-weight:bold;width:100%','','');
			/*<table width="100%" cellpadding="0" cellspacing="2" >*/?>
							<?php
							unset($_SESSION['dims']['crm_newcontact_actionform']);
							unset($_SESSION['dims']['crm_newcontact_saveredirect']);

							if($part == _BUSINESS_TAB_CONTACT_IDENTITE) $part = 0;
							switch($part) {

								default:
								case 0:
									echo '<div id="formCRM">';
									require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_form.php');
									$_SESSION['dims']['current_object']['id_record']=$contact->fields['id'];
									$_SESSION['dims']['current_object']['id_module']=1;
									$_SESSION['dims']['current_object']['id_object']=dims_const::_SYSTEM_OBJECT_CONTACT;
									echo $skin->open_widgetbloc($_DIMS['cste']['_DIMS_LABEL_TAGS'], 'width:100%;clear:both;height:600px;overflow:auto;', 'padding-bottom:1px;padding-left:10px;vertical-align:bottom;font-weight: bold;', '','26px', '26px', '-15px', '-7px', '', '', '');
									require_once(DIMS_APP_PATH . '/modules/system/desktop_bloc_tag.php');
									echo $skin->close_simplebloc();
									//include(DIMS_APP_PATH . '/modules/system/desktop_bloc_tag.php');
									break;
								case 1:
									require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_view_network.php');
									break;
								case 2:
									require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_view_news.php');
									break;
								case 3:
									require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_view_docs.php');
									break;
								}
						   /*</table>*/ ?>
		</td>
		<?
		if (isset($_SESSION['dims']['_DIMS_SPECIFIC']) && $_SESSION['dims']['_DIMS_SPECIFIC']) {
			// test si module specific
			echo '<td width="25%" style="vertical-align:top;">';
			require_once (DIMS_APP_PATH . "/modules/system/".$_SESSION['dims']['_PREFIX']."/contact_menuright.php");
			echo '</td>';
		}
		?>
	</tr>
</table>
