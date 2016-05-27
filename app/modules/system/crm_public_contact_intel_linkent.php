<script language="JavaScript" type="text/JavaScript">


</script>

<?php
$workspace = new workspace();
$workspace->open($_SESSION['dims']['workspaceid']);
$lstworkpaces=$workspace->getWorkspaceShareObject(dims_const::_SYSTEM_OBJECT_CONTACT);
$inworkspace = '';
foreach($lstworkpaces as $key => $tab) {
	$inworkspace .= "'".$key."',";
}
$inworkspace .= "'".$_SESSION['dims']['workspaceid']."'";
$ent_filter='';

if (!isset($_SESSION['dims']['ent_filter'])  || (isset($_POST['ent_filter']) && $_POST['ent_filter']=='')) $_SESSION['dims']['ent_filter']='';
$ent_filter = dims_load_securvalue('ent_filter', dims_const::_DIMS_CHAR_INPUT, true, true,false,$_SESSION['dims']['ent_filter']);
$uptiers = dims_load_securvalue('uptiers', dims_const::_DIMS_NUM_INPUT, true, true, false);

if (!isset($_SESSION['dims']['search_linkent_type'])  || (isset($_POST['search_linkent_type']) && $_POST['search_linkent_type']=='')) $_SESSION['dims']['search_linkent_type']='';
$search_linkent_type = dims_load_securvalue('search_linkent_type', dims_const::_DIMS_CHAR_INPUT, true, true,false,$_SESSION['dims']['search_linkent_type']);


$tab_link = array();
$tab_other_lk_ent = array();
//recherche des liens avec une entreprise
$sql_li_ent = "	SELECT	e.intitule,
						le.*,
						ct.firstname, ct.lastname
				FROM	dims_mod_business_tiers e
				INNER JOIN dims_mod_business_tiers_contact le
				ON		le.id_tiers = e.id
				AND		le.id_workspace IN (".$inworkspace.")
				LEFT JOIN dims_mod_business_contact ct
				ON		ct.id = le.id_ct_user_create
				WHERE	le.id_contact = :idcontact ";
$param = array();
$param[':idcontact'] = $contact_id;
if ($ent_filter!='') {
	$sql_li_ent.=" AND ( e.intitule like :entfilter OR e.ville like :entfilter ) ";
	$param[':entfilter'] = "%".$ent_filter."%";
}

if(isset($uptiers) && $uptiers == 1 ) {
	$sql_li_ent .= " ORDER BY		e.intitule DESC,  le.link_level, le.type_lien";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($uptiers) && $uptiers == -1) {
	$sql_li_ent .= " ORDER BY		e.intitule ASC,  le.link_level, le.type_lien";
	$opt_trip = 1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($uptiers) && $uptiers == 2) {
	$sql_li_ent .= " ORDER BY		le.type_lien DESC, le.link_level, e.intitule ";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($uptiers) && $uptiers == -2) {
	$sql_li_ent .= " ORDER BY		le.type_lien ASC, le.link_level, e.intitule ";
	$opt_trip = -1;
	$opt_trit = 2;
	$opt_tric = -3;
}
elseif(isset($uptiers) && $uptiers == 3) {
	$sql_li_ent .= " ORDER BY		ct.lastname DESC, ct.firstname DESC, le.link_level ";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($uptiers) && $uptiers == -3) {
	$sql_li_ent .= " ORDER BY		ct.lastname ASC, ct.firstname ASC, le.link_level ";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = 3;
}
else {
	$sql_li_ent .= " ORDER BY		le.date_create DESC, e.intitule";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}

$res_li_ent = $db->query($sql_li_ent, $param);

while($tab_lie = $db->fetchrow($res_li_ent)) {
	if($tab_lie['link_level'] == '1' && ($search_linkent_type==0 || $search_linkent_type==1)) {
		$tab_lie['type']='public';
		$tab_link['ent_pers'][$tab_lie['id_tiers']] = $tab_lie;
	}
	if($tab_lie['link_level'] == '2' && $tab_lie['id_workspace'] == $_SESSION['dims']['workspaceid'] && ($search_linkent_type==0 || $search_linkent_type==2)) {
		$tab_lie['type']='workspace';
		$tab_link['ent_pers'][$tab_lie['id_tiers']] = $tab_lie;
	}
	elseif($tab_lie['link_level'] == 2 && ($search_linkent_type==0 || $search_linkent_type==2)) {
		//on cré un tableau pour indiquer les autres workspaces ayant des valeurs
		//if(!isset($tab_other_lk_ent[$tab_lie['id_workspace']])) $tab_other_lk_ent[$tab_lie['id_workspace']]['nb_link'] = 0;
		//$tab_other_lk_ent[$tab_lie['id_workspace']]['nb_link']++;
		//$tab_other_lk_ent[$tab_lie['id_workspace']]['id_user'] = $tab_lie['id_user'];
		//if(!isset($_SESSION['contact']['current_last_modify'][$tab_lie['id_workspace']])) $_SESSION['contact']['current_last_modify'][$tab_lie['id_workspace']] = $tab_lie['id_user'];
		$tab_lie['type_lien']='';
		$tab_lie['type']='workspace';
		$tab_link['ent_pers'][$tab_lie['id_tiers']] = $tab_lie;
	}
}
if(!isset($_SESSION['contact']['current_last_modify'])) {
	$_SESSION['contact']['current_last_modify']=array();
	$_SESSION['contact']['current_last_modify']=$tab_other_lk_ent;
}

$list_wkspcent = $dims->getWorkspaces();

$count = 0;
if(!empty($tab_link['ent_pers'])) $count = count($tab_link['ent_pers']);

echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_LINK_ENT']." (".$count.")", "", "padding-left:15px;", "./common/img/widget_view.png", "26", "26", "-15px", "0px", "javascript:void(0);", "javascript:affiche_div('lkent');", "");
?>

<div id="button_addent" style="display:block;width:100%;text-align:center;">
<?
echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_ADDLINK'],"./common/img/add.gif","javascript:affiche_div('button_addent');affiche_div('zone_addent');document.getElementById('dispres_searcht').focus();","","");
?>
</div>
<div id="zone_addent" style="display:none;width:100%;">
	<?php require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_intel_add_linkent.php'); ?>
</div>

<div id="lkent" style="display:block;">
<table width="100%" cellpadding="0" cellspacing="3">
	<tr>
		<td align="center">
			<?
			$url = dims_urlencode("admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_CONTACT_INTELL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$ct);
			?>
			<form name="form_filter" action="<? echo $url; ?>" method="post">
				<?
					// Sécurisation du formulaire par token
					require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
					$token = new FormToken\TokenField;
					$token->field("ent_filter");
					$token->field("search_linkent_type");
					$tokenHTML = $token->generate();
					echo $tokenHTML;
				?>
				<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td align="left" width="50%">
					<?
					echo $_DIMS['cste']['_SEARCH'].'&nbsp;<input type="text" id="ent_filter" name="ent_filter" value="'.$ct_filter.'"><a href="javascript:void(0);" onclick="javascript:document.form_filter.submit();"><img src="./common/img/search.png" border="0"></a>';
					?>
					</td>
					<td align="left" width="50%">
						<? echo $_DIMS['cste']['_DIMS_FILTER'] ?>&nbsp;
						<select id="search_linkct_type" name="search_linkent_type" onchange="javascript:document.form_filter.submit();">
							<option value="" <?php if($search_linkent_type == '') echo 'selected="selected"'; ?>>--</option>
							<option value="1" <?php if($search_linkent_type == '1') echo 'selected="selected"'; ?>><? echo $_DIMS['cste']['_DIMS_LABEL_PUBLIC'] ?></option>
							<option value="2" <?php if($search_linkent_type == '2') echo 'selected="selected"'; ?>><? echo $_DIMS['cste']['_WORKSPACE']; ?></option>
						</select>
					</td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
	<tr>
		<td>
		<div id="affiche_link_4" style="display:block;width:100%;height:180px;overflow:auto;">
			<table cellspacing="0" cellpadding="0" width="100%" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;">
				<tbody>
					<?php
						if(!empty($tab_link['ent_pers'])) {
					?>
						<tr class="trl1" style="font-size:12px;">
							<td style="width: 1%;"/>
							<td style="width: 25%;"><a href="<? echo "admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_CONTACT_INTELL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$ct."&uptiers=".$opt_trip; ?>"><? echo $_DIMS['cste']['_DIMS_LABEL_GROUP_LIST']; ?></a></td>
							<td style="width: 20%;"><a href="<? echo "admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_CONTACT_INTELL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$ct."&uptiers=".$opt_trit; ?>"><? echo $_DIMS['cste']['_TYPE']; ?></a></td>
							<td style="width: 10%;"><? echo $_DIMS['cste']['_DIMS_LABEL_VIEWMODE']; ?></td>
							<td style="width: 25%;"><a href="<? echo "admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_CONTACT_INTELL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$ct."&uptiers=".$opt_tric; ?>"><? echo $_DIMS['cste']['_DIMS_LABEL_FROM']  ?></a></td>
							<td style="width: 19%;"><? echo $_DIMS['cste']['_DIMS_OPTIONS']; ?></td>
						</tr>
					<?php
					$class_col = 'trl1';

					foreach($tab_link['ent_pers'] as $id_enttoview => $tab_ent) {
						if ($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
						$date_c = dims_timestamp2local($tab_ent['date_create']);
						if(!empty($tab_ent['date_deb'])) $date_deb = dims_timestamp2local($tab_ent['date_deb']); else $date_deb['date'] = "-";
						if(!empty($tab_ent['date_fin'])) $date_fin = dims_timestamp2local($tab_ent['date_fin']); else $date_fin['date'] = "-";
							echo '	<tr class="'.$class_col.'">
										<td></td>
										<td style="cursor: default;" id="tickets_title_3">
											<a href="admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_ENT_FORM.'&id_ent='.$tab_ent['id_tiers'].'" title="Voir la fiche de ce contact.">'.$tab_ent['intitule'].'</a>
										</td>
										<td style="cursor: default;" id="tickets_title_3">
											'.stripslashes($tab_ent['type_lien']).'
										</td>
										<td style="cursor: default;" id="tickets_title_3">';
										switch ($tab_ent['type']) {
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

										/*
										echo '</td>
										<td style="cursor: default;" id="tickets_title_3">
											'.$tab_ent['function'].'
										</td>';*/
										echo '</td>
										<td style="cursor: default;" id="tickets_title_3">
											<a href="'.dims_urlencode('admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_FORM.'&contact_id='.$tab_ent['id_ct_user_create']).'" title="Voir la fiche de ce contact.">'.$tab_ent['firstname'].'&nbsp;'.$tab_ent['lastname'].'</a>
										</td>';

										if (($tab_ent['type']=='workspace' && $tab_ent['id_workspace']==$_SESSION['dims']['workspaceid']) || ($tab_ent['type']=='public' && ($tab_ent['id_user'] == $_SESSION['dims']['userid'] || $tab_ent['id_ct_user_create'] == $_SESSION['dims']['user']['id_contact']))) {
											echo '
												<td align="center" style="cursor: default;" id="tickets_title_3">
													<a href="javascript:void(0);" onclick="javascript:affiche_div(\'div_inf_ent_'.$tab_ent['id'].'\');"><img src="./common/img/view.png" style="border:0px;" title="'.$_DIMS['cste']['_DIMS_OBJECT_DISPLAY'].'"/></a>
													 / <a href="javascript:void(0);" onclick="javascript:modLinkEnt('.$tab_ent['id'].','.$contact_id.');"><img src="./common/img/edit.gif" style="border:0px;" title="'.$_DIMS['cste']['_MODIFY'].'"/></a>
													 / <a href="javascript:void(0);" onclick="javascript:deleteLinkEnt('.$tab_ent['id'].');"><img src="./common/modules/system/img/delete.png" style="border:0px;" title="'.$_DIMS['cste']['_BUSINESS_LEGEND_CUT'].'"/></a>
												</td>
											</tr>
											<tr class="'.$class_col.'"><td colspan="5">
											<div id="div_inf_ent_'.$tab_ent['id'].'" style="display:none;">
												<table width="100%">
													<tr>
														<td align="right" width="25%">'.$_DIMS['cste']['_DIMS_LABEL_CREATE_ON'].' :
														</td>
														<td align="left">'.$date_c['date'].'
														</td>
													</tr>
													<tr>
														<td align="right">'.$_DIMS['cste']['_DIMS_LABEL_FROM'].' :
														</td>
														<td align="left">
															<a href="admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_FORM.'&contact_id='.$tab_ent['id_ct_user_create'].'" title="Voir la fiche de ce contact.">'.$tab_ent['firstname'].'&nbsp;'.$tab_ent['lastname'].'</a>
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
														<td align="right">'.ucfirst(strtolower($_DIMS['cste']['_DIMS_LABEL_DEPARTEMENT'])).' :
														</td>
														<td align="left">'.$tab_ent['departement'].'
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
										else {
											echo '<td align="center" style="cursor: default;" id="tickets_title_3">&nbsp;</td></tr>';
										}
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
<? echo $skin->close_simplebloc(); ?>
