<script language="JavaScript" type="text/JavaScript">
	function affich_blocinfo(id_div) {
		var div_to_open = dims_getelem(id_div);
		div_to_open.style.display="block";
	}

	function close_blocinfo(id_div) {
		var div_to_open = dims_getelem(id_div);
		div_to_open.style.display="none";
	}
</script>
<?php
require_once(DIMS_APP_PATH . "/modules/system/class_tiers.php");
require_once(DIMS_APP_PATH . "/modules/system/class_contact.php");
$cat = dims_load_securvalue('cat',dims_const::_DIMS_NUM_INPUT,true,true);
$tabscriptenv = "admin.php?cat=".$cat;
$ent_id = dims_load_securvalue('id_ent',dims_const::_DIMS_NUM_INPUT,true,true);

$ent= new tiers();
if ($ent_id>0) {
	$ent->open($ent_id);
	$_SESSION['business']['tiers_id']=$ent_id;
	//on ajoute la date de visite a la liste des fiches en veille
	//on verifie si la fiche selectionnee fait partie des fiches en veille
	$sql_t = "SELECT time_lastseen FROM dims_mod_business_ct_watch WHERE id_user = ".$_SESSION['dims']['userid']." AND id_tiers = ".$ent_id;
	//echo $sql_t;
	$res_t = $db->query($sql_t);
	$nb_res = $db->numrows($res_t);

	if($nb_res > 0) {
		$sql_maj = "UPDATE `dims_mod_business_ct_watch` SET `time_lastseen` = '".dims_createtimestamp()."' WHERE id_user = ".$_SESSION['dims']['userid']." AND id_tiers = ".$ent_id;
		$db->query($sql_maj);
	}
}
$workspace = new workspace();
$workspace->open($_SESSION['dims']['workspaceid']);
$lstworkpaces=$workspace->getWorkspaceShareObject(dims_const::_SYSTEM_OBJECT_CONTACT);
?>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td width="25%" style="vertical-align:top;">
			<?
				require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_bloc_profil.php');
				switch($part) {
					default:
					case 0:
						if($ent->fields['inactif'] != 1) {
							require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_bloc_network.php');

							if (!empty($lstworkpaces)) {
								require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_bloc_share.php');
							}

							if ($workspace->fields['contact_comments']) {
								require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_bloc_news.php');
							}

							if ($workspace->fields['contact_docs']) {
								require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_bloc_docs.php');
							}
						}
						break;
					case 1:
						//require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_bloc_news.php');
						//require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_bloc_docs.php');

						break;
					case 2:
						//require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_bloc_network.php');
						//require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_bloc_docs.php');
						break;
					case 3:
						//require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_bloc_network.php');
						//require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_bloc_news.php');
						break;
				}
				if($ent->fields['inactif'] != 1) {
					echo $skin->open_widgetbloc($_DIMS['cste']['_DIMS_LABEL_TAGS'], 'width:100%;', 'padding-bottom:1px;padding-left:10px;vertical-align:bottom;color:#cccccc;', '','26px', '26px', '-15px', '-7px', '', '', '');

					// selection des tags
					echo "<div id=\"tagblockdisplay\">";
					echo dims_getBlockTag($dims, $_DIMS, $_SESSION['dims']['moduleid'], dims_const::_SYSTEM_OBJECT_TIERS, $ent->fields['id']);
					echo "</div>";
					echo $skin->close_widgetbloc();

					//informations sur la fiche
					$datecree = str_replace("-", "", $ent->fields["date_creation"])."000000";
					$datecreate = dims_timestamp2local($datecree);
					$datecreate = explode("/",$datecreate['date']);
					$ct_create = new user();

					$ct_create->open($ent->fields["id_user_create"]);
					//border:#536689 1px solid;
					echo '<div style="width:100%;">
								<table width="100%" cellpadding="0" cellspacing="0">
									<tr class="trl1">
										<td style="padding-left:5px;" align="left">';
					echo $_DIMS['cste']['_SYSTEM_LABEL_FICHCREATED']." : </td>";
					echo '
							<td style="padding-left:5px;" align="left">';
					if (isset($ct_create->fields['firstname'])) {
											echo $datecreate[0]." ".$business_mois[$datecreate[1]*1]. " ".$datecreate[2];
											if ($ct_create->fields['id']>0) {
												$titleusr=$ct_create->fields['firstname']." ".$ct_create->fields['lastname'];
												echo " &nbsp;&nbsp;&nbsp;".$_DIMS['cste']['_DIMS_LABEL_FROM']." : ";
												echo "<a href=\"admin.php?dims_mainmenu=9&cat=0&dims_desktop=block&dims_action=public&action="._BUSINESS_TAB_CONTACT_FORM."&contact_id=".$ct_create->fields['id_contact']."\">".$titleusr."</a>";
											}
						echo '				</td>';
					}
					echo '</tr>';

					if (!empty($ent->fields["timestp_modify"]) && $ent->fields["timestp_modify"]!=$ent->fields["date_creation"]) {

						$datemodt=dims_timestamp2local($ent->fields["timestp_modify"]);
						$datemod=explode("/",$datemodt['date']);

						$usr = new user();
						$usr->open($ent->fields["id_user"]);

					echo '			<tr class="trl1">
										<td style="padding-left:5px;" align="left">';
											echo $_DIMS['cste']['_DIMS_DATE_MODIFY']." : ";
					echo '				</td>
										<td style="padding-left:5px;" align="left">';
					if (isset($usr->fields['lastname'])) {
								echo $datemod[0]." ".$business_mois[$datemod[1]*1]. " ".$datemod[2];
								$titleusr=$usr->fields['firstname']." ".$usr->fields['lastname'];
								echo " &nbsp;&nbsp;&nbsp;".$_DIMS['cste']['_DIMS_LABEL_FROM']." : ";
								echo "<a href=\"admin.php?dims_mainmenu=9&cat=0&dims_desktop=block&dims_action=public&action="._BUSINESS_TAB_CONTACT_FORM."&contact_id=".$usr->fields['id_contact']."\">".$titleusr."</a>";
								echo '</td>';
					}
					echo '</tr>';
										}
					echo '		</table>';
					echo '</div>';
					if(dims_ismanager()) {
						echo '<div style="width:100%;padding-right:10%;margin-top:10px;">';
						echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_DESACTIV_SHEET'],"./common/img/check.png","javascript:desactiveFiche(".$ent->fields['id'].", 'ent');","","");
						echo '</div>';
					}
				}
				else {
					echo '<div style="width:100%;padding-right:10%;margin-top:10px;">';
					//echo '<span style="height:20px;"><img src="./common/img/warning.png"/></span><span style="height:20px;padding-left:10px;">'.$_DIMS['cste']['_DIMS_LABEL_FICHE_SUPPR'].'</span>';
					echo '</div>';
					//if(dims_ismanager()) {
						echo '<div style="width:100%;padding-right:10%;margin-top:10px;">';
						echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_ACTIV_SHEET'],"./common/img/checkdo.png","javascript:activeFiche(".$ent->fields['id'].", 'ent');","","");
						echo '</div>';
					//}
				}
				if(dims_isadmin()) {
					echo '<div style="width:100%;padding-right:10%;margin-top:10px;">';
					echo dims_create_button($_DIMS['cste']['_DELETE'],"./common/img/delete.png","javascript:dims_confirmlink('admin.php?dims_mainmenu=9&cat="._BUSINESS_CAT_TIERS."&action="._BUSINESS_TAB_CONTACT_DELETE_ADMIN."&contact_id=".$ent->fields['id']."&type=ent', '".$_DIMS['cste']['_DIMS_LABEL_CONFIRM_DELETE']."');","","");
					echo '</div>';
				}
			?>
		</td>
		<td width="75%" align="center" valign="top">
			<? //echo $skin->open_widgetbloc($title,'font-weight:bold;width:100%','',''); ?>
			<table width="100%" cellpadding="0" cellspacing="0" >
				<tr>
				<?
					switch($part) {
						case 0:
							require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_view_mod.php');
							break;
						case 1:
							require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_view_reseau.php');
							break;
						case 2:
							require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_view_news.php');
							break;
						case 3:
							require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_view_docs.php');
							break;
						default:
							require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_form.php');
							break;

					}
					//echo $skin->close_widgetbloc();
				?>
				</tr>
			</table>
		</td>
		<?
		if (isset($_SESSION['dims']['_DIMS_SPECIFIC']) && $_SESSION['dims']['_DIMS_SPECIFIC']) {
			// test si module specific
			echo '<td width="25%" style="vertical-align:top;">';
			require_once (DIMS_APP_PATH . "/modules/system/".$_SESSION['dims']['_PREFIX']."/tiers_menuright.php");
			echo '</td>';
		}
		?>

	</tr>
</table>
