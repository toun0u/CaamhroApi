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
$workspace = new workspace();
$workspace->open($_SESSION['dims']['workspaceid']);
$lstworkpaces=$workspace->getWorkspaceShareObject(dims_const::_SYSTEM_OBJECT_CONTACT);
$inworkspace = '';
foreach($lstworkpaces as $key => $tab) {
	$inworkspace .= "'".$key."',";
}
$inworkspace .= "'".$_SESSION['dims']['workspaceid']."'";

$tab_link = array();
$tab_other_lk = array();
$_SESSION['contact']['current_last_modify'] = array();

if (!isset($_SESSION['dims']['ct_filter'])	|| (isset($_POST['ct_filter']) && $_POST['ct_filter']=='')) $_SESSION['dims']['ct_filter']='';
$ct_filter = dims_load_securvalue('ct_filter', dims_const::_DIMS_CHAR_INPUT, true, true,false,$_SESSION['dims']['ct_filter']);
$upname = dims_load_securvalue('upname', dims_const::_DIMS_NUM_INPUT, true, true, false);
if (!isset($_SESSION['dims']['search_linkct_type'])  || (isset($_POST['search_linkct_type']) && $_POST['search_linkct_type']=='')) $_SESSION['dims']['search_linkct_type']='';
$search_linkct_type = dims_load_securvalue('search_linkct_type', dims_const::_DIMS_CHAR_INPUT, true, true,false,$_SESSION['dims']['search_linkct_type']);

//recherche des liens entre personnes sens 1
$sql_li1 = "SELECT			c.id as id_pers, c.lastname, c.firstname,
							l.*,
							byc.lastname as by_lastname, byc.firstname as by_firstname
			FROM			dims_mod_business_contact c
			INNER JOIN		dims_mod_business_ct_link l
			ON				l.id_contact2 = c.id
			AND				l.id_workspace IN (".$inworkspace.")
			LEFT JOIN		dims_mod_business_contact byc
			ON				byc.id = l.id_ct_user_create
			WHERE			l.id_contact1 = :idcontact
			AND				id_object = :idobject ";
$param = array();
$param[':idcontact'] = $contact_id;
$param[':idobject'] = dims_const::_SYSTEM_OBJECT_CONTACT;
if ($ct_filter!='') {
	$sql_li1.=" AND ( c.lastname like :ct_filter OR c.firstname like :ct_filter ) ";
	$param[':ctfilter'] = "%".$ct_filter."%";
}

if(isset($upname) && $upname == 1 ) {
	$sql_li1 .= " ORDER BY		c.lastname DESC, c.firstname, l.link_level, l.type_link";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($upname) && $upname == -1) {
	$sql_li1 .= " ORDER BY		c.lastname ASC, c.firstname, l.link_level, l.type_link";
	$opt_trip = 1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($upname) && $upname == 2) {
	$sql_li1 .= " ORDER BY		l.type_link DESC, c.lastname, c.firstname ";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($upname) && $upname == -2) {
	$sql_li1 .= " ORDER BY		l.type_link ASC, c.lastname, c.firstname ";
	$opt_trip = -1;
	$opt_trit = 2;
	$opt_tric = -3;
}
elseif(isset($upname) && $upname == 3) {
	$sql_li1 .= " ORDER BY		by_lastname DESC, by_firstname DESC, l.link_level ";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($upname) && $upname == -3) {
	$sql_li1 .= " ORDER BY		by_lastname ASC, by_firstname ASC, l.link_level ";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = 3;
}
else {
	$sql_li1 .= " ORDER BY		l.time_create DESC, c.lastname, c.firstname";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}

$res_li1 = $db->query($sql_li1, $param);
while($tab_li1 = $db->fetchrow($res_li1)) {
	//lien de type personnel (que l'on fait � partir du bouton 'Ajouter � votre liste de contacts')
	if($tab_li1['link_level'] == 3 && ($search_linkct_type==0 || $search_linkct_type==3)) {
		$tab_li1['type']='private';
		$tab_link['between_pers'][$tab_li1['id_contact2']] = $tab_li1;
		$prout= true;
	}
	//lien de type metier (ceux que l'on fait dans la partie Intelligence d'une fiche contact)
	if($tab_li1['link_level'] == 2 && ($search_linkct_type==0 || $search_linkct_type==2)) {
		$tab_li1['type']='workspace';
		$tab_link['between_pers'][$tab_li1['id_contact2']] = $tab_li1;
		$prout= true;
	}
	/*elseif($tab_li1['link_level'] == 2) {
		//on cr� un tableau pour indiquer les autres workspaces ayant des valeurs
		if(!isset($tab_other_lk[$tab_li1['id_workspace']])) $tab_other_lk[$tab_li1['id_workspace']]['nb_link'] = 0;
		//$tab_other_lk[$tab_li1['id_workspace']]['nb_link']++;
		//$tab_other_lk[$tab_li1['id_workspace']]['id_user'] = $tab_li1['id_user'];
		$tab_li1['type']='workspace';
		$tab_link['between_pers'][$tab_li1['id_contact2']] = $tab_li1;
	}*/
	//liens de type g�n�rique (ceux que l'on fait a partir du formualire de gestion des liens dans la fiche contact)
	if($tab_li1['link_level'] == 1 && ($search_linkct_type==0 || $search_linkct_type==1)) {
		$tab_li1['type']='public';
		$tab_link['between_pers'][$tab_li1['id_contact2']] = $tab_li1;
		$prout= true;
	}
}

//echo count($tab_link['between_pers']);

//un lien est bidirectionnel, il faut donc rechercher � partir de contact2 aussi
$sql_li2 = "SELECT			c.id as id_pers, c.lastname, c.firstname,
							l.*,
							byc.lastname as by_lastname, byc.firstname as by_firstname
			FROM			dims_mod_business_contact c
			INNER JOIN		dims_mod_business_ct_link l
			ON				l.id_contact1 = c.id
			AND				l.id_workspace IN (".$inworkspace.")
			LEFT JOIN		dims_mod_business_contact byc
			ON				byc.id = l.id_ct_user_create
			WHERE			l.id_contact2 = :idcontact
			AND				id_object = :idobject ";
$param = array();
$param[':idcontact'] = $contact_id;
$param[':idobject'] = dims_const::_SYSTEM_OBJECT_CONTACT;

if ($ct_filter!='') {
	$sql_li2.=" AND ( c.lastname like :ct_filter OR c.firstname like :ct_filter ) ";
	$param[':ctfilter'] = "%".$ct_filter."%" ;
}
if(isset($upname) && $upname == 1 ) {
	$sql_li2 .= " ORDER BY		c.lastname DESC, c.firstname, l.link_level, l.type_link";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($upname) && $upname == -1) {
	$sql_li2 .= " ORDER BY		c.lastname ASC, c.firstname, l.link_level, l.type_link";
	$opt_trip = 1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($upname) && $upname == 2) {
	$sql_li2 .= " ORDER BY		l.type_link DESC, c.lastname, c.firstname ";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($upname) && $upname == -2) {
	$sql_li2 .= " ORDER BY		l.type_link ASC, c.lastname, c.firstname ";
	$opt_trip = -1;
	$opt_trit = 2;
	$opt_tric = -3;
}
elseif(isset($upname) && $upname == 3) {
	$sql_li2 .= " ORDER BY		by_lastname DESC, by_firstname DESC, l.link_level ";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}
elseif(isset($upname) && $upname == -3) {
	$sql_li2 .= " ORDER BY		by_lastname ASC, by_firstname ASC, l.link_level ";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = 3;
}
else {
	$sql_li2 .= " ORDER BY		l.time_create DESC, c.lastname, c.firstname";
	$opt_trip = -1;
	$opt_trit = -2;
	$opt_tric = -3;
}

$res_li2 = $db->query($sql_li2, $param);
while($tab_li2 = $db->fetchrow($res_li2)) {
	//lien de type personnel (que l'on fait � partir du bouton 'Ajouter � votre liste de contacts')
	if($tab_li2['link_level'] == 3 && ($_SESSION['dims']['user']['id_contact'] == $tab_li2['id_contact1']  || $_SESSION['dims']['user']['id_contact'] == $tab_li2['id_contact2']) && ($search_linkct_type==0 || $search_linkct_type==3))  {
		if(!isset($tab_link['between_pers'][$tab_li2['id_contact1']])) {
			$tab_li2['type']='private';
			$tab_link['between_pers'][$tab_li2['id_contact1']] = $tab_li2;
		}
	}
	//lien de type m�tier (ceux que l'on fait dans la partie Intelligence d'une fiche contact)
	if($tab_li2['link_level'] == 2 && $tab_li2['id_workspace'] == $_SESSION['dims']['workspaceid'] && ($search_linkct_type==0 || $search_linkct_type==2)) {
		if(!isset($tab_link['between_pers'][$tab_li2['id_contact1']])) {
			$tab_li2['type']='workspace';
			$tab_link['between_pers'][$tab_li2['id_contact1']] = $tab_li2;
		}
	}
	elseif($tab_li2['link_level'] == 2) {
		//on cr� un tableau pour indiquer les autres workspaces ayant des valeurs
		//$tab_other_lk[$tab_li2['id_workspace']]['nb_link']++;
		//$tab_other_lk[$tab_li1['id_workspace']]['id_user'] = $tab_li2['id_user'];
		$tab_li2['type_link']='';
		if(!isset($tab_link['between_pers'][$tab_li2['id_contact1']])) {
			$tab_li2['type']='workspace';
			$tab_link['between_pers'][$tab_li2['id_contact1']] = $tab_li2;
		}
	}//liens de type g�n�rique (ceux que l'on fait a partir du loc de gestion des liens dans la fiche contact)
	if($tab_li2['link_level'] == 1 && ($search_linkct_type==0 || $search_linkct_type==1)) {
		if(!isset($tab_link['between_pers'][$tab_li2['id_contact1']])) {
			$tab_li2['type']='public';
			$tab_link['between_pers'][$tab_li2['id_contact1']] = $tab_li2;
		}
	}
}

$_SESSION['contact']['current_last_modify'] = $tab_other_lk;
$list_wkspce = $dims->getWorkspaces();

$count = 0;
if(!empty($tab_link['between_pers'])) $count = count($tab_link['between_pers']);

echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_LINK_CONT']." (".$count.")", "", "padding-left:15px;", "./common/img/widget_view.png", "26", "26", "-15px", "0px", "javascript:void(0);", "javascript:affiche_div('lkp');", ""); ?>
<div id="button_add" style="display:block;width:100%;text-align:center;">
<?
echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_ADDLINK'],"./common/img/add.gif","javascript:affiche_div('button_add');affiche_div('zone_add');document.getElementById('search_pers').focus();","","");
?>
</div>
<div id="zone_add" style="display:none;width:100%;">
	<?php require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_intel_add_linkp.php'); ?>
</div>
<div id="lkp" style="display:block;">
<table width="100%" cellpadding="0" cellspacing="3">
	<tr>
		<td align="center">
			<?
			$url = dims_urlencode("admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_CONTACT_INTELL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$ct);
			?>
			<form name="form_filter_ct" action="<? echo $url; ?>" method="post">
				<?
					// Sécurisation du formulaire par token
					require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
					$token = new FormToken\TokenField;
					$token->field("ct_filter");
					$token->field("search_linkct_type");
					$tokenHTML = $token->generate();
					echo $tokenHTML;
				?>
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td align="left" width="50%">
					<?
					echo $_DIMS['cste']['_SEARCH'].'&nbsp;<input type="text" id="ct_filter" name="ct_filter" value="'.$ct_filter.'"><a href="javascript:void(0);" onclick="javascript:document.form_filter_ct.submit();"><img src="./common/img/search.png" border="0"></a>';
					?>
					</td>
					<td align="left" width="50%">
						<? echo $_DIMS['cste']['_DIMS_FILTER'] ?>&nbsp;
						<select id="search_linkct_type" name="search_linkct_type" onchange="javascript:document.form_filter_ct.submit();">
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
							if(!empty($tab_link['between_pers'])) {
						?>
							<tr class="trl1" style="font-size:12px;">
								<td style="width: 1%;"/>
								<td style="width: 25%;"><a href="<? echo "admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_CONTACT_INTELL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$ct."&upname=".$opt_trip; ?>"><? echo $_DIMS['cste']['_DIMS_LABEL_PERSONNE']; ?></a></td>
								<td style="width: 20%;"><a href="<? echo "admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_CONTACT_INTELL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$ct."&upname=".$opt_trit; ?>"><? echo $_DIMS['cste']['_TYPE']; ?></a></td>
								<td style="width: 10%;"><? echo $_DIMS['cste']['_DIMS_LABEL_VIEWMODE']; ?></td>
								<td style="width: 25%;"><a href="<? echo "admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_CONTACT_INTELL."&part="._BUSINESS_TAB_CONTACT_IDENTITE."&contact_id=".$ct."&upname=".$opt_tric; ?>"><? echo $_DIMS['cste']['_DIMS_LABEL_FROM'] ?></a></td>
								<td style="width: 19%;"><? echo $_DIMS['cste']['_DIMS_OPTIONS']; ?></td>
							</tr>
						<?
								$class_col = 'trl1';
								foreach($tab_link['between_pers'] as $id_perstoview => $tab_pers) {

									if ($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
									$date_c = dims_timestamp2local($tab_pers['time_create']);
									if(!empty($tab_pers['date_deb'])) $date_deb = dims_timestamp2local($tab_pers['date_deb']); else $date_deb['date'] = "-";
									if(!empty($tab_pers['date_fin'])) $date_fin = dims_timestamp2local($tab_pers['date_fin']); else $date_fin['date'] = "-";
										echo '	<tr class="'.$class_col.'">
													<td></td>
													<td style="cursor: default;" id="tickets_title_3">
														<a href="admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_FORM.'&contact_id='.$tab_pers['id_pers'].'" title="Voir la fiche de ce contact.">'.$tab_pers['firstname'].'&nbsp;'.$tab_pers['lastname'].'</a>
													</td>
													<td style="cursor: default;" id="tickets_title_3">
														'.$tab_pers['type_link'].'
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

										echo '		</td>
													<td style="cursor: default;" id="tickets_title_3">
														<a href="'.dims_urlencode('admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_CONTACT_FORM.'&contact_id='.$tab_pers['id_ct_user_create']).'" title="Voir la fiche de ce contact.">'.$tab_pers['by_firstname'].'&nbsp;'.$tab_pers['by_lastname'].'</a>
													</td>';

										if (($tab_pers['type']=='workspace' && $tab_pers['id_workspace']==$_SESSION['dims']['workspaceid']) || ($tab_pers['type']=='public' && ($tab_pers['id_user'] == $_SESSION['dims']['userid'] || $tab_pers['id_ct_user_create'] == $_SESSION['dims']['user']['id_contact']))) {
											echo '
												<td align="center" style="cursor: default;" id="tickets_title_3">
													 <a href="javascript:void(0);" onclick="javascript:affiche_div(\'div_inf_'.$tab_pers['id'].'\');"><img src="./common/img/view.png" style="border:0px;" title="'.$_DIMS['cste']['_DIMS_OBJECT_DISPLAY'].'"/></a>
													 / <a href="javascript:void(0);" onclick="javascript:modLink('.$tab_pers['id'].','.$contact_id.')"><img src="./common/img/edit.gif" style="border:0px;" title="'.$_DIMS['cste']['_MODIFY'].'"/></a>
													 / <a href="javascript:void(0);" onclick="javascript:deleteLink('.$tab_pers['id'].')"><img src="./common/modules/system/img/delete.png" style="border:0px;" title="'.$_DIMS['cste']['_BUSINESS_LEGEND_CUT'].'"/></a>
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
