<?php

$tab_link = array();
$tab_other_lk_ent = array();

//recherche des workspaces pouvant partager leurs donnees
$workspace = new workspace();
$workspace->open($_SESSION['dims']['workspaceid']);
$lstworkpaces=$workspace->getWorkspaceShareObject(dims_const::_SYSTEM_OBJECT_CONTACT);
$inworkspace = '';
foreach($lstworkpaces as $key => $tab) {
	$inworkspace .= "'".$key."',";
}
$inworkspace .= "'".$_SESSION['dims']['workspaceid']."'";

if (!isset($_SESSION['dims']['ent_filter2'])  || (isset($_POST['ent_filter2']) && $_POST['ent_filter2']=='')) $_SESSION['dims']['ent_filter2']='';
$ent_filter = dims_load_securvalue('ent_filter2', dims_const::_DIMS_CHAR_INPUT, true, true,false,$_SESSION['dims']['ent_filter2']);

$uptiers = dims_load_securvalue('uptiers', dims_const::_DIMS_NUM_INPUT, true, true, false);

if (!isset($_SESSION['dims']['search_linkent_type'])  || (isset($_POST['search_linkent_type']) && $_POST['search_linkent_type']=='')) $_SESSION['dims']['search_linkent_type']='';
$search_linkent_type = dims_load_securvalue('search_linkent_type', dims_const::_DIMS_CHAR_INPUT, true, true,false,$_SESSION['dims']['search_linkent_type']);

//recherche des liens entre personnes
$param = array();
$sql_li1 = "SELECT			t.id as id_tiers, t.intitule, t.ville,
							l.*,
							byc.lastname as by_lastname, byc.firstname as by_firstname
			FROM			dims_mod_business_tiers t
			INNER JOIN		dims_mod_business_ct_link l
			ON				l.id_contact2 = t.id
			AND				l.id_workspace IN (".$inworkspace.")
			INNER JOIN		dims_mod_business_contact byc
			ON				byc.id = l.id_ct_user_create
			WHERE			l.id_contact1 = :idcontact
			AND				id_object = :idobject ";

$param[':idcontact'] = $ent_id;
$param[':idobject']	= dims_const::_SYSTEM_OBJECT_TIERS;

if ($ent_filter!='') {
	$sql_li1.=" AND t.intitule like :intitule ";
	$param[':intitule'] = "%".$ent_filter."%";
}
if(isset($uptiers) && $uptiers == 1 ) {
	$sql_li1 .= " ORDER BY		t.intitule DESC,  l.link_level, l.type_link";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($uptiers) && $uptiers == -1) {
	$sql_li1 .= " ORDER BY		t.intitule ASC,  l.link_level, l.type_link";
	$opt_trip = 1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($uptiers) && $uptiers == 2) {
	$sql_li1 .= " ORDER BY		l.type_link DESC, l.link_level, t.intitule ";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($uptiers) && $uptiers == -2) {
	$sql_li1 .= " ORDER BY		l.type_link ASC, l.link_level, t.intitule ";
	$opt_trip = -1;
	$opt_trit = 2;
	$opt_tric = -3;
}
elseif(isset($uptiers) && $uptiers == 3) {
	$sql_li1 .= " ORDER BY		by_lastname DESC, by_firstname DESC, l.link_level ";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($uptiers) && $uptiers == -3) {
	$sql_li1 .= " ORDER BY		by_lastname ASC, by_firstname ASC, l.link_level ";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = 3;
}
else {
	$sql_li1 .= " ORDER BY		t.intitule";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}

//echo $sql_li1;
$res_li1 = $db->query($sql_li1, $param);

while($tab_li1 = $db->fetchrow($res_li1)) {

	//lien de type m�tier (ceux que l'on fait dans la partie Intelligence d'une fiche contact)
	if($tab_li1['link_level'] == 2 && $tab_li1['id_workspace'] == $_SESSION['dims']['workspaceid'] && ($search_linkent_type==0 || $search_linkent_type==2)) {
		$tab_link['between_ent'][$tab_li1['id_contact2']] = $tab_li1;
	}
	elseif($tab_li1['link_level'] == 2	&& ($search_linkent_type==0 || $search_linkent_type==2)) {
		//on cr� un tableau pour indiquer les autres workspaces ayant des valeurs
		if(!isset($tab_other_lk_ent[$tab_li1['id_workspace']])) $tab_other_lk_ent[$tab_li1['id_workspace']]['nb_link'] = 0;
		$tab_other_lk_ent[$tab_li1['id_workspace']]['nb_link']++;
		$tab_other_lk_ent[$tab_li1['id_workspace']]['id_user'] = $tab_li1['id_user'];
		if(!isset($_SESSION['contact']['current_last_modify'][$tab_li1['id_workspace']])) $_SESSION['contact']['current_last_modify'][$tab_li1['id_workspace']]['id_user'] = $tab_li1['id_user'];
	}
	//liens de type g�n�rique (ceux que l'on fait a partir du formualire de gestion des liens dans la fiche contact)
	if($tab_li1['link_level'] == 1	&& ($search_linkent_type==0 || $search_linkent_type==1)) {
		$tab_link['between_ent'][$tab_li1['id_contact2']] = $tab_li1;
	}
}

//un lien est bidirectionnel, il faut donc rechercher � partir de contact2 aussi
$param = array();
$sql_li2 = "SELECT			t.id as id_tiers, t.intitule, t.ville,
							l.*,
							byc.lastname as by_lastname, byc.firstname as by_firstname
			FROM			dims_mod_business_tiers t
			INNER JOIN		dims_mod_business_ct_link l
			ON				l.id_contact1 = t.id
			AND				l.id_workspace IN (".$inworkspace.")
			INNER JOIN		dims_mod_business_contact byc
			ON				byc.id = l.id_ct_user_create
			WHERE			l.id_contact2 = :idcontact
			AND				id_object = :idobject ";

$param[':idcontact'] = $ent_id;
$param[':idobject']	= dims_const::_SYSTEM_OBJECT_TIERS;

if ($ent_filter!='') {
	$sql_li2.=" AND ( t.intitule like :intitule OR t.ville like :intitule ) ";
	$param[':intitule'] = "%".$ent_filter."%";
}
if(isset($uptiers) && $uptiers == 1 ) {
	$sql_li2 .= " ORDER BY		t.intitule DESC,  l.link_level, l.type_lien";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($uptiers) && $uptiers == -1) {
	$sql_li2 .= " ORDER BY		t.intitule ASC,  l.link_level, l.type_lien";
	$opt_trip = 1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($uptiers) && $uptiers == 2) {
	$sql_li2 .= " ORDER BY		l.type_lien DESC, l.link_level, t.intitule ";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($uptiers) && $uptiers == -2) {
	$sql_li2 .= " ORDER BY		l.type_lien ASC, l.link_level, t.intitule ";
	$opt_trip = -1;
	$opt_trit = 2;
	$opt_tric = -3;
}
elseif(isset($uptiers) && $uptiers == 3) {
	$sql_li2 .= " ORDER BY		by_lastname DESC, by_firstname DESC, l.link_level ";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($uptiers) && $uptiers == -3) {
	$sql_li2 .= " ORDER BY		by_lastname ASC, by_firstname ASC, l.link_level ";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = 3;
}
else {
	$sql_li2 .= " ORDER BY		t.intitule";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}


//echo $sql_li2;
$res_li2 = $db->query($sql_li2, $param);

while($tab_li2 = $db->fetchrow($res_li2)) {

	//lien de type m�tier (ceux que l'on fait dans la partie Intelligence d'une fiche contact)
	if($tab_li2['link_level'] == 2 && $tab_li2['id_workspace'] == $_SESSION['dims']['workspaceid'] && ($search_linkent_type==0 || $search_linkent_type==2)) {
		$tab_link['between_ent'][$tab_li2['id_contact1']] = $tab_li2;
	}
	//liens de type g�n�rique (ceux que l'on fait a partir du loc de gestion des liens dans la fiche contact)
	if($tab_li2['link_level'] == 1	&& ($search_linkent_type==0 || $search_linkent_type==1)) {
		if(!isset($tab_link['between_ent'][$tab_li2['id_contact1']])) $tab_link['between_ent'][$tab_li2['id_contact1']] = $tab_li2;
	}
}
if(!isset($_SESSION['contact']['current_last_modify'])) {
	$_SESSION['contact']['current_last_modify']=array();
	$_SESSION['contact']['current_last_modify']=$tab_other_lk_ent;
}

$count = 0;
if(!empty($tab_link['between_ent'])) $count = count($tab_link['between_ent']);

echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_LINK_ENT']." (".$count.")", "", "padding-left:15px;", "./common/img/widget_view.png", "26", "26", "-15px", "0px", "javascript:void(0);", "javascript:affiche_div('lkent');", ""); ?>
<div id="button_addent" style="display:block;width:100%;text-align:center;">
<?
echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_ADDLINK'],"./common/img/add.gif","javascript:affiche_div('button_addent');affiche_div('zone_addent');document.getElementById('search_pers').focus();","","");
?>
</div>
<div id="zone_addent" style="display:none;width:100%;">
	<? require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_intel_add_linkent.php'); ?>
</div>
<div id="lkent" style="display:block;">
<table width="100%" cellpadding="0" cellspacing="3">
	<tr>
		<td align="center">
			<form name="form_filter_ent" action="<? echo $url; ?>" method="post">
			<?
				// Sécurisation du formulaire par token
				require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
				$token = new FormToken\TokenField;
				$token->field("ent_filter2");
				$token->field("search_linkent_type");
				$tokenHTML = $token->generate();
				echo $tokenHTML;
			?>
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td align="left" width="50%">
					<?
					echo $_DIMS['cste']['_SEARCH'].'&nbsp;<input type="text" id="ent_filter2" name="ent_filter2" value="'.$ent_filter.'"><a href="javascript:void(0);" onclick="javascript:document.form_filter_ent.submit();"><img src="./common/img/search.png" border="0"></a>';
					?>
					</td>
					<td align="left" width="50%">
						<? echo $_DIMS['cste']['_DIMS_FILTER'] ?>&nbsp;
						<select id="search_linkent_type" name="search_linkent_type" onchange="javascript:document.form_filter_ent.submit();">
							<option value="" <?php if($search_linkent_type == '') echo 'selected="selected"'; ?>>--</option>
							<option value="1" <?php if($search_linkent_type == '1') echo 'selected="selected"'; ?>><? echo $_DIMS['cste']['_DIMS_LABEL_PUBLIC'] ?></option>
							<option value="2" <?php if($search_linkent_type == '2') echo 'selected="selected"'; ?>><? echo $_DIMS['cste']['_WORKSPACE']; ?></option>
							<option value="3" <?php if($search_linkent_type == '3') echo 'selected="selected"'; ?>><? echo $_DIMS['cste']['_PRIVATE']; ?></option>
						</select>
					</td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
	<tr>
		<td>
		<div id="affiche_link_4" style="display:block;width:100%;height:160px;overflow:auto;">
			<table cellspacing="0" cellpadding="0" width="100%" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;">
				<tbody>
					<?
						if(!empty($tab_link['between_ent'])) {
					?>
						<tr class="trl1" style="font-size:12px;">
							<td style="width: 1%;"/>
							<td style="width: 30%;"><a href="<? echo "admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_ENT_INTELL."&part="._BUSINESS_TAB_ENT_IDENTITE."&id_ent=".$ct."&uptiers=".$opt_trip; ?>"><? echo $_DIMS['cste']['_DIMS_LABEL_GROUP_LIST']; ?></a></td>
							<td style="width: 20%;"><a href="<? echo "admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_ENT_INTELL."&part="._BUSINESS_TAB_ENT_IDENTITE."&id_ent=".$ct."&uptiers=".$opt_trit; ?>"><? echo $_DIMS['cste']['_TYPE']; ?></a></td>
							<td style="width: 10%;"><? echo $_DIMS['cste']['_DIMS_LABEL_VIEWMODE']; ?></td>
							<td style="width: 20%;"><a href="<? echo "admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_ENT_INTELL."&part="._BUSINESS_TAB_ENT_IDENTITE."&id_ent=".$ct."&uptiers=".$opt_tric; ?>"><? echo $_DIMS['cste']['_DIMS_LABEL_FROM']  ?></a></td>
							<td style="width: 19%;"><? echo $_DIMS['cste']['_DIMS_OPTIONS']; ?></td>
						</tr>
					<?
								$class_col = 'trl1';
								foreach($tab_link['between_ent'] as $id_enttoview => $tab_ent) {
									if ($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
									$date_c = dims_timestamp2local($tab_ent['time_create']);
									if(!empty($tab_ent['date_deb'])) $date_deb = dims_timestamp2local($tab_ent['date_deb']); else $date_deb['date'] = "-";
									if(!empty($tab_ent['date_fin'])) $date_fin = dims_timestamp2local($tab_ent['date_fin']); else $date_fin['date'] = "-";
										echo '	<tr class="'.$class_col.'">
													<td></td>
													<td style="cursor: default;" id="tickets_title_3">
														<a href="admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_ENT_FORM.'&id_ent='.$tab_ent['id_tiers'].'" title="Voir la fiche de ce contact.">'.$tab_ent['intitule'].'</a>
													</td>
													<td style="cursor: default;" id="tickets_title_3">
														'.$tab_ent['type_link'].'
													</td>
													<td style="cursor: default;" id="tickets_title_3">';

													switch ($tab_pers['type']) {
														case 'public':
															echo "<img src=\"./common/img/all.png\">";
															break;
														case 'workspace':
															echo "<img src=\"./common/img/users.png\">";
															break;
														case 'private':
															echo "<img src=\"./common/img/user.png\">";
															break;
													}

													echo '</td>
													<td style="cursor: default;" id="tickets_title_3">
														<a href="admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_FORM.'&contact_id='.$tab_ent['id_ct_user_create'].'" title="Voir la fiche de ce contact.">'.$tab_ent['by_firstname'].'&nbsp;'.$tab_ent['by_lastname'].'</a>
													</td>
													<td align="center" style="cursor: default;" id="tickets_title_3">
														<a href="javascript:void(0);" onclick="javascript:affiche_div(\'div_inf_ent_'.$tab_ent['id'].'\');"><img src="./common/img/view.png" style="border:0px;" title="'.$_DIMS['cste']['_BUSINESS_LEGEND_CUT'].'"/></a>
														 / <a href="javascript:void(0);" onclick="javascript:modLinkEnt('.$tab_ent['id'].','.$ent_id.',\'from_tiers\');"><img src="./common/img/edit.gif" style="border:0px;" title="'.$_DIMS['cste']['_MODIFY'].'"/></a>
														 / <a href="javascript:void(0);" onclick="javascript:deleteLink('.$tab_ent['id'].');"><img src="./common/modules/system/img/delete.png" style="border:0px;" title="'.$_DIMS['cste']['_BUSINESS_LEGEND_CUT'].'"/></a>
													</td>
												</tr>
												<tr class="'.$class_col.'"><td colspan="5">
												<div id="div_inf_ent_'.$tab_ent['id'].'" style="display:none;">
													<table width="50%">
														<tr>
															<td align="right" width="20%">'.$_DIMS['cste']['_DIMS_LABEL_CREATE_ON'].' :
															</td>
															<td align="left">'.$date_c['date'].'
															</td>
														</tr>
														<tr>
															<td align="right">'.$_DIMS['cste']['_BEGIN'].' :
															</td>
															<td align="left">'.$date_deb['date'].'
															</td>
														</tr>
														<tr>
															<td align="right">'.$_DIMS['cste']['_END'].' :
															</td>
															<td align="left">'.$date_fin['date'].'
															</td>
														</tr>
														<tr>
															<td align="right">'.$_DIMS['cste']['_DIMS_COMMENTS'].' :
															</td>
															<td align="left">'.$tab_ent['commentaire'].'
															</td>
														</tr>
													</table>
												</div>
												</td></tr>
												';
											}
										}
										else {
											echo '<tr class="trl1"><td align="center">'.$_DIMS['cste']['_DIMS_LABEL_NO_LINK'].'</td></tr>';
										}
						?>
			   </tbody>
		   </table>
		</div>
		</td>
	</tr>

</table>
</div>
<? echo $skin->close_widgetbloc(); ?>
