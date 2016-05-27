<script language="JavaScript" type="text/JavaScript">
	function affiche_block_gen() {
		var div_to_open = dims_getelem('affiche_link_gen');
		if(div_to_open.style.display=="block") div_to_open.style.display='none';
		else div_to_open.style.display = 'block';
	}

	function affiche_block_met() {
		var div_to_open = dims_getelem('affiche_link_met');
		if(div_to_open.style.display=="block") div_to_open.style.display='none';
		else div_to_open.style.display = 'block';
	}

	function affiche_block_pers() {
		var div_to_open = dims_getelem('affiche_link_pers');
		if(div_to_open.style.display=="block") div_to_open.style.display='none';
		else div_to_open.style.display = 'block';
	}
</script>

<?php
if (!isset($search_linkct_type)) $search_linkct_type=0;
$tab_link = array();
$tab_other_lk = array();

$upname = dims_load_securvalue('upname', dims_const::_DIMS_NUM_INPUT, true, true, false);

if (!isset($_SESSION['dims']['ent_filter'])  || (isset($_POST['ent_filter']) && $_POST['ent_filter']=='')) $_SESSION['dims']['ent_filter']='';
$ct_filter = dims_load_securvalue('ent_filter', dims_const::_DIMS_CHAR_INPUT, true, true,false,$_SESSION['dims']['ent_filter']);

$workspace = new workspace();
$workspace->open($_SESSION['dims']['workspaceid']);
$lstworkpaces=$workspace->getWorkspaceShareObject(dims_const::_SYSTEM_OBJECT_CONTACT);
$inworkspace = '';
foreach($lstworkpaces as $key => $tab) {
	$inworkspace .= "'".$key."',";
}
$inworkspace .= "'".$_SESSION['dims']['workspaceid']."'";

//recherche des liens avec une entreprise
$sql_li_ent = "	SELECT	DISTINCT p.lastname, p.firstname,
						tc.*,
						byc.lastname as by_lastname, byc.firstname as by_firstname
				FROM	dims_mod_business_contact p
				INNER JOIN dims_mod_business_tiers_contact tc
				ON		tc.id_contact = p.id
				AND		tc.id_tiers = ".$ent_id."
				AND		tc.id_workspace IN (".$inworkspace.")
				INNER JOIN dims_mod_business_contact byc
				ON		byc.id = tc.id_ct_user_create";

if ($ct_filter!='') {
	$sql_li_ent.=" AND ( p.lastname like '%".$ct_filter."%' OR p.firstname like '%".$ct_filter."%') ";
}
if(isset($upname) && $upname == 1 ) {
	$sql_li_ent .= " ORDER BY		p.lastname DESC, p.firstname, tc.link_level, tc.type_lien";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($upname) && $upname == -1) {
	$sql_li_ent .= " ORDER BY		p.lastname ASC, p.firstname, tc.link_level, tc.type_lien";
	$opt_trip = 1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($upname) && $upname == 2) {
	$sql_li_ent .= " ORDER BY		tc.type_lien DESC, p.lastname, p.firstname ";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($upname) && $upname == -2) {
	$sql_li_ent .= " ORDER BY		tc.type_lien ASC, p.lastname, p.firstname ";
	$opt_trip = -1;
	$opt_trit = 2;
	$opt_tric = -3;
}
elseif(isset($upname) && $upname == 3) {
	$sql_li_ent .= " ORDER BY		by_lastname DESC, by_firstname DESC, tc.link_level ";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($upname) && $upname == -3) {
	$sql_li_ent .= " ORDER BY		by_lastname ASC, by_firstname ASC, tc.link_level ";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = 3;
}
else {
	$sql_li_ent .= " ORDER BY		tc.date_create DESC, p.lastname, p.firstname";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}
//echo $sql_li_ent;
$res_li_ent = $db->query($sql_li_ent);

while($tab_lie = $db->fetchrow($res_li_ent)) {
	if($tab_lie['link_level'] == '1' && ($search_linkct_type==0 || $search_linkct_type==1)) {
		$tab_lie['type']='public';
		if (!isset($tab_link['ent_pers'][$tab_lie['id_contact']])) {
			$tab_link['ent_pers'][$tab_lie['id_contact']] = $tab_lie;
		}
	}
	if($tab_lie['link_level'] == '2'  && ($search_linkct_type==0 || $search_linkct_type==2)) {
		$tab_lie['type']='workspace';
		if (!isset($tab_link['ent_pers'][$tab_lie['id_contact']])) {
			$tab_link['ent_pers'][$tab_lie['id_contact']] = $tab_lie;
		}
	}
}

$_SESSION['contact']['current_last_modify']=array();
$_SESSION['contact']['current_last_modify']=$tab_other_lk;
$nb_res = count($tab_link['ent_pers']);
?>
<? echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_LINK_CONT']." (".$nb_res.")", "", "padding-left:15px;", "./common/img/widget_view.png", "26", "26", "-15px", "0px", "javascript:void(0);", "javascript:affiche_div('lkp');", ""); ?>
<div id="button_add" style="display:block;width:100%;text-align:center;">
<?
echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_ADDLINK'],"./common/img/add.gif","javascript:affiche_div('button_add');affiche_div('zone_add');document.getElementById('search_pers').focus();","","");
if (isset($_GET['add_linkct'])) {
	echo '<script language="JavaScript" type="text/JavaScript">';
	echo "window.onload= function() {affiche_div('zone_add');document.getElementById('search_pers').focus();}";
	echo '</script>';
}
?>
</div>
<div id="zone_add" style="display:none;width:100%;">
	<? require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_intel_add_link.php'); ?>
</div>
<div id="lkp" style="display:block;">
<table width="100%" cellpadding="0" cellspacing="3">
	<tr>
		<td align="center">
			<form name="form_filter_pers" action="<? echo $url; ?>" method="post">
			<?
				// Sécurisation du formulaire par token
				require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
				$token = new FormToken\TokenField;
				$token->field("ent_filter");
				$token->field("search_linkct_type");
				$tokenHTML = $token->generate();
				echo $tokenHTML;
			?>
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td align="left" width="50%">
					<?
					echo $_DIMS['cste']['_SEARCH'].'&nbsp;<input type="text" id="ent_filter" name="ent_filter" value="'.$ct_filter.'"><a href="javascript:void(0);" onclick="javascript:document.form_filter_pers.submit();"><img src="./common/img/search.png" border="0"></a>';
					?>
					</td>
					<td align="left" width="50%">
						<? echo $_DIMS['cste']['_DIMS_FILTER'] ?>&nbsp;
						<select id="search_linkct_type" name="search_linkct_type" onchange="javascript:document.form_filter_pers.submit();">
							<option value="" <?php if($search_linkct_type == '') echo 'selected="selected"'; ?>>--</option>
							<option value="1" <?php if($search_linkct_type == '1') echo 'selected="selected"'; ?>><? echo $_DIMS['cste']['_DIMS_LABEL_PUBLIC'] ?></option>
							<option value="2" <?php if($search_linkct_type == '2') echo 'selected="selected"'; ?>><? echo $_DIMS['cste']['_WORKSPACE']; ?></option>
							<option value="3" <?php if($search_linkct_type == '3') echo 'selected="selected"'; ?>><? echo $_DIMS['cste']['_PRIVATE']; ?></option>
						</select>
					</td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
	<tr>
		<td>
			<div id="affiche_link_1" style="display:block;width:100%;height:250px;overflow:auto;">
				<table cellspacing="0" cellpadding="0" width="100%" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;">
					<tbody>
						<?
						if(!empty($tab_link['ent_pers'])) {
						?>
							<tr class="trl1" style="font-size:12px;">
								<td style="width: 1%;"/>
								<td style="width: 25%;"><a href="<? echo "admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_ENT_INTELL."&part="._BUSINESS_TAB_ENT_IDENTITE."&id_ent=".$ct."&upname=".$opt_trip; ?>"><? echo $_DIMS['cste']['_DIMS_LABEL_PERSONNE']; ?></a></td>
								<td style="width: 20%;"><a href="<? echo "admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_ENT_INTELL."&part="._BUSINESS_TAB_ENT_IDENTITE."&id_ent=".$ct."&upname=".$opt_trit; ?>"><? echo $_DIMS['cste']['_TYPE']; ?></a></td>
								<td style="width: 10%;"><? echo $_DIMS['cste']['_DIMS_LABEL_VIEWMODE']; ?></td>
								<td style="width: 25%;"><a href="<? echo "admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_ENT_INTELL."&part="._BUSINESS_TAB_ENT_IDENTITE."&id_ent=".$ct."&upname=".$opt_tric; ?>"><? echo $_DIMS['cste']['_DIMS_LABEL_FROM'] ?></a></td>
								<td style="width: 19%;"><? echo $_DIMS['cste']['_DIMS_OPTIONS']; ?></td>
							</tr>
						<?
							$class_col = 'trl1';
							foreach($tab_link['ent_pers'] as $id_perstoview => $tab_pers) {
								if ($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
								$date_c = dims_timestamp2local($tab_pers['date_create']);
								if(!empty($tab_pers['date_deb'])) $date_deb = dims_timestamp2local($tab_pers['date_deb']); else $date_deb['date'] = "-";
								if(!empty($tab_pers['date_fin'])) $date_fin = dims_timestamp2local($tab_pers['date_fin']); else $date_fin['date'] = "-";
								echo '	<tr class="'.$class_col.'">
									<td></td>
									<td style="cursor: default;" id="tickets_title_3">
										<a href="admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_FORM.'&contact_id='.$tab_pers['id_contact'].'" title="Voir la fiche de ce contact.">'.$tab_pers['firstname'].'&nbsp;'.$tab_pers['lastname'].'</a>
									</td>
									<td style="cursor: default;" id="tickets_title_3">
										'.$tab_pers['type_lien'].'
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
										<a href="'.dims_urlencode('admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_FORM.'&contact_id='.$tab_pers['id_ct_user_create']).'" title="Voir la fiche de ce contact.">'.$tab_pers['by_firstname'].'&nbsp;'.$tab_pers['by_lastname'].'</a>
									</td>';

									/*
									 <td style="cursor: default;" id="tickets_title_3">
										'.$tab_pers['function'].'
									 </td>
									 */
									if ($tab_pers['type']=='workspace' && $tab_pers['id_workspace']==$_SESSION['dims']['workspaceid']) {
									echo '
										<td align="center" style="cursor: default;" id="tickets_title_3">
											 <a href="javascript:void(0);" onclick="javascript:affiche_div(\'div_inf_'.$tab_pers['id'].'\');"><img src="./common/img/view.png" style="border:0px;" title="'.$_DIMS['cste']['_DIMS_OBJECT_DISPLAY'].'"/></a>
											 / <a href="javascript:void(0);" onclick="javascript:modLinkEnt('.$tab_pers['id'].','.$ent_id.',\'from_tiers\')"><img src="./common/img/edit.gif" style="border:0px;" title="'.$_DIMS['cste']['_MODIFY'].'"/></a>
											 / <a href="javascript:void(0);" onclick="javascript:deleteLinkEnt('.$tab_pers['id'].')"><img src="./common/modules/system/img/delete.png" style="border:0px;" title="'.$_DIMS['cste']['_BUSINESS_LEGEND_CUT'].'"/></a>
										</td>
									</tr>
									<tr class="'.$class_col.'"><td colspan="5">
									<div id="div_inf_'.$tab_pers['id'].'" style="display:none;">
										<table width="100%">
											<tr>
												<td align="right" width="25%">'.$_DIMS['cste']['_DIMS_LABEL_CREATE_ON'].' :
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
												<td align="right">'.ucfirst(strtolower($_DIMS['cste']['_DIMS_LABEL_DEPARTEMENT'])).' :
												</td>
												<td align="left">'.$tab_pers['departement'].'
												</td>
											</tr>
											<tr>
												<td align="right">'.$_DIMS['cste']['_DIMS_LABEL_FROM'].' :
												</td>
												<td align="left">
													<a href="admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_FORM.'&contact_id='.$tab_pers['id_ct_user_create'].'" title="Voir la fiche de ce contact.">'.$tab_pers['by_firstname'].'&nbsp;'.$tab_pers['by_lastname'].'</a>
												</td>
											</tr>
											<tr>
												<td align="right">'.$_DIMS['cste']['_DIMS_COMMENTS'].' :
												</td>
												<td align="left">'.$tab_pers['commentaire'].'
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
