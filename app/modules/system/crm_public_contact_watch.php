<script language="JavaScript" type="text/JavaScript">
    function delCttoWatch(id_ct, type) {
		var retour = dims_xmlhttprequest("admin.php", "dims_mainmenu=<?php echo _DIMS_MENU_CONTACT; ?>&cat=<? echo _BUSINESS_CAT_CONTACT; ?>&dims_desktop=block&dims_action=public&action=<? echo _BUSINESS_TAB_CONTACTSSEEK; ?>&op=delcttowatch&id_ct="+id_ct+"&type="+type);
		alert(retour);
		document.location.href = "admin.php?cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_CONTACTSADD; ?>";
	}
</script>
<?

$date_d = date("YmdHis");

//recherche de la date de dernière connexion

//recherche des fiches en veille
$sql_f = "SELECT distinct * FROM dims_mod_business_ct_watch WHERE id_user = :iduser ";
$res_f = $db->query($sql_f, array(
	'iduser' => $_SESSION['dims']['userid']
));
$tab_veille = array();
$tab_veille['personne']['liste']="";
$tab_veille['tiers']['liste']="";

while($tab_res = $db->fetchrow($res_f)) {
	if($tab_res['id_personne'] != "") {
		$tab_veille['personne'][$tab_res['id_personne']] = $tab_res;
		//on construit une liste des personnes pour l'insérer dans la requete
		$tab_veille['personne']['liste'] .= $tab_res['id_personne'].", ";
	}
	else {
		$tab_veille['tiers'][$tab_res['id_tiers']] = $tab_res;
		$tab_veille['tiers']['liste'] .= $tab_res['id_tiers'].", ";
	}
}
	$trs = substr($tab_veille['tiers']['liste'], 0, -2);
	if($trs == "") $trs = "0";

	$per = substr($tab_veille['personne']['liste'], 0, -2);
	if($per == "") $per = "0";

	//selection des informations concernant les personnes
	$sql_p = "  SELECT			distinct c.firstname, c.lastname, c.id as id_pers, c.timestp_modify,
								t.intitule, t.id as id_tiers,
								u.id as id_creator, u.lastname as name_creator, u.firstname as pren_creator
				FROM			dims_mod_business_contact c
				LEFT JOIN		dims_mod_business_tiers_contact tc
				ON				tc.id_contact = c.id
				AND				(
									tc.type_lien LIKE 'emploi'
								OR  tc.type_lien LIKE 'employeur'
								)
				AND				tc.date_fin = 0
				LEFT JOIN		dims_mod_business_tiers t
				ON				t.id = tc.id_tiers
				LEFT JOIN		dims_mod_business_contact u
				ON				u.id = c.id_user_create
				WHERE			c.id IN (".$per.")
				AND				c.inactif != 1
				ORDER BY		c.firstname, c.lastname
				";

	$res_p = $db->query($sql_p);
	$nb_resp = $db->numrows($res_p);

	//selection des informations concernant les entreprises
	$sql_e = "  SELECT			distinct t.intitule, t.id as id_tiers, t.ville, t.date_creation, t.timestp_modify,
								u.id as id_creator, u.lastname as name_creator, u.firstname as pren_creator
				FROM			dims_mod_business_tiers t
				LEFT JOIN		dims_mod_business_contact u
				ON				u.id = t.id_user_create
				WHERE			t.id IN (".$trs.")
				AND				t.inactif != 1
				ORDER BY		t.intitule";
	$res_e = $db->query($sql_e);
	$nb_rese = $db->numrows($res_e);

$class_col = 'trl1';
echo $skin->open_simplebloc();
?>
<table width="100%">
    <tr>
		<td>
			<div class="accordion_content" style="background-color:transparent;">
				<table cellspacing="0" cellpadding="2" style="margin-top:5px;margin-bottom:10px;">
					<tbody>
					<?php if($nb_resp > 0) { ?>
						<tr style="background-color:#ffffff;color:#777777;">
							<td style="width: 2%;"/>
							<td style="width: 25%;"><? echo $_DIMS['cste']['_DIMS_LABEL_CONTACT']; ?></td>
							<td style="width: 35%;"><? echo $_DIMS['cste']['_DIMS_LABEL_GROUP_LIST']; ?></td>
							<td style="width: 16%;"><? echo $_DIMS['cste']['_DIMS_LABEL_MODIF_ON_FEM']; ?></td>
							<td style="width: 20%;"><? echo $_DIMS['cste']['_DIMS_LABEL_FROM']; ?></td>
							<td style="width: 2%;"></td>
						</tr>
					<?php

						while($tab_p = $db->fetchrow($res_p)) {

							$date_c = dims_timestamp2local($tab_p['timestp_modify']);

							if ($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
							echo '	<tr class="'.$class_col.'">
										<td>';
							if($tab_p['timestp_modify'] >= $tab_veille['personne'][$tab_p['id_pers']]['time_lastseen']) {
								echo		'<img src="./common/templates/backoffice/dims/img/system/p_red.png"/>';
							}
							else {
								echo		'<img src="./common/templates/backoffice/dims/img/system/p_green.png"/>';
							}
							echo		'</td>
										<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style=" cursor: default;" id="tickets_title_3">
											<a href="admin.php?dims_mainmenu='._DIMS_MENU_CONTACT.'&cat='._BUSINESS_CAT_CONTACT.'&dims_desktop=block&dims_action=public&action='._BUSINESS_TAB_CONTACT_FORM.'&contact_id='.$tab_p['id_pers'].'&part='._BUSINESS_TAB_CONTACT_IDENTITE.'" title="Voir la fiche de ce contact.">'.$tab_p['firstname'].'&nbsp;'.$tab_p['lastname'].'</a>
										</td>
										<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style=" cursor: default;" id="tickets_title_3">
											<a href="admin.php?dims_mainmenu='._DIMS_MENU_CONTACT.'&cat='._BUSINESS_CAT_CONTACT.'&dims_desktop=block&dims_action=public&action='._BUSINESS_TAB_ENT_FORM.'&part=0&id_ent='.$tab_p['id_tiers'].'&part='._BUSINESS_TAB_ENT_IDENTITE.'" title="Voir la fiche de cette entreprise.">'.$tab_p['intitule'].'</a>
										</td>
										<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style=" cursor: default;" id="tickets_title_3">
											'.$date_c['date'].'
										</td>
										<td>
											<a href="admin.php?dims_mainmenu='._DIMS_MENU_CONTACT.'&cat='._BUSINESS_CAT_CONTACT.'&dims_desktop=block&dims_action=public&action='._BUSINESS_TAB_CONTACT_FORM.'&contact_id='.$tab_p['id_creator'].'&id_cont=indefini" title="Voir la fiche de ce contact.">'.substr($tab_p['pren_creator'],0,1).'.&nbsp;'.$tab_p['name_creator'].'</a>
										</td>
										<td align="center">
											<a href="javascript:void(0);" onclick="javascript:delCttoWatch(\''.$tab_p['id_pers'].'\', \'personne\');"><img src="./common/img/del.png"/></a>
										</td>
									</tr>';
						}
					}
					else {
						echo '<tr><td width="100%">'.$_DIMS['cste']['_DIMS_LABEL_NO_RESP'].'</td></tr>';
					}
					?>
				   </tbody>
			   </table>
			</div>
			<div id="vertical_container">
				<h3 class="accordion_toggle">
					<table style="width:100%;">
						<tr>
							<td align="left" width="30%">&nbsp;</td>
							<td align="left" width="33%">
								<table style="width:100%;" cellpadding="0" cellspacing="0">
									<tr>
										<td class="bgb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
										<td class="midb20">
										<? echo $_DIMS['cste']['_DIMS_LABEL_WATCH_ENT'] ?>
										</td>
										<td class="bdb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
									</tr>
								</table>
							</td>
							<td align="left" width="30%">&nbsp;</td>
						</tr>
					</table>

				</h3>
			</div>
			<div class="accordion_content" style="background-color:transparent;">
				<table cellspacing="0" cellpadding="2" style="margin-top:5px;margin-bottom:10px;">
					<tbody>
						<? if($nb_rese > 0) { ?>
						<tr style="background-color:#ffffff;color:#777777;">
							<td style="width: 2%;"/>
							<td style="width: 25%;"><? echo $_DIMS['cste']['_DIMS_LABEL_ENT_NAME']; ?></td>
							<td style="width: 26%;"><? echo $_DIMS['cste']['_LOCATION']; ?></td>
							<td style="width: 20%;"><? echo $_DIMS['cste']['_DIMS_LABEL_CREATE_ON']; ?></td>
							<td style="width: 25%;"><? echo $_DIMS['cste']['_DIMS_LABEL_FROM']; ?></td>
							<td style="width: 2%;"></td>
						</tr>
						<?
						$class_col = 'trl1';
						while($tab_e = $db->fetchrow($res_e)) {

							$date_c = dims_timestamp2local($tab_e['timestp_modify']);

							if ($class_col == 'trl1') $class_col = 'trl2'; else $class_col = 'trl1';
							echo '	<tr class="'.$class_col.'">
										<td>';
							if($tab_e['timestp_modify'] >= $tab_veille['tiers'][$tab_e['id_tiers']]['time_lastseen']) {
								echo		'<img src="./common/templates/backoffice/dims/img/system/p_red.png"/>';
							}
							else {
								echo		'<img src="./common/templates/backoffice/dims/img/system/p_green.png"/>';
							}
							echo		'</td>
										<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style=" cursor: default;" id="tickets_title_3">
											<a href="admin.php?dims_mainmenu='._DIMS_MENU_CONTACT.'&cat='._BUSINESS_CAT_CONTACT.'&dims_desktop=block&dims_action=public&action='._BUSINESS_TAB_ENT_FORM.'&part=0&id_ent='.$tab_e['id_tiers'].'&id_cont=indefini&part='._BUSINESS_TAB_ENT_IDENTITE.'" onclick="javascript:add_visit(\''.$tab_e['id_tiers'].'\', \'tiers\');" title="Voir la fiche de cette entreprise.">'.$tab_e['intitule'].'</a>
										</td>
										<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style=" cursor: default;" id="tickets_title_3">
											'.$tab_e['ville'].'
										</td>
										<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style=" cursor: default;" id="tickets_title_3">
											'.$date_c['date'].'
										</td>
										<td>
											<a href="admin.php?dims_mainmenu='._DIMS_MENU_CONTACT.'&cat='._BUSINESS_CAT_CONTACT.'&dims_desktop=block&dims_action=public&action='._BUSINESS_TAB_CONTACT_FORM.'&contact_id='.$tab_e['id_creator'].'&id_cont=indefini" onclick="javascript:add_visit(\''.$tab_e['id_creator'].'\', \'personne\');" title="Voir la fiche de ce contact.">'.substr($tab_e['pren_creator'],0,1).'.&nbsp;'.$tab_e['name_creator'].'</a>
										</td>
										<td align="center">
											<a href="#" onclick="javascript:delCttoWatch(\''.$tab_e['id_tiers'].'\', \'tiers\');"><img src="./common/img/del.png"/></a>
										</td>
									</tr>';
						}
					}
					else {
						echo '<tr><td width="100%">'.$_DIMS['cste']['_DIMS_LABEL_NO_ENTERPRISE'].'</td></tr>';
					}
					?>
				   </tbody>
			   </table>
			</div>
		</td>
	</tr>
</table>

<?
echo $skin->close_simplebloc();
/*
<script type="text/javascript">
    var bottomAccordion = new accordion('vertical_container');

	var verticalAccordions = $$('.accordion_toggle');
	verticalAccordions.each(function(accordion){
		$(accordion.next(0)).setStyle({height: '0px'});
	});
	bottomAccordion.activate($$('#vertical_container .accordion_toggle')[0]);
</script>

 */
 ?>
