<?php
switch($op) {
	default:
		$include = DIMS_APP_PATH . "/modules/system/lfb/lfb_admin_contact_import_ent_view.php";
	break;

	case 'search_impcreator':
		ob_start();

		$imp_name = dims_load_securvalue('imp_name', dims_const::_DIMS_CHAR_INPUT, true);

		$sql_pimp = "	SELECT	id,
								id_contact,
								lastname,
								firstname
						FROM	dims_user
						WHERE	1
						AND		(lastname LIKE '".$imp_name."%' OR firstname LIKE '".$imp_name."%')";
		$res_imp = $db->query($sql_pimp);
		$retour = '';
		$retour .= '<table width="100%" cellpadding="0" cellspacing="0">';
		if($db->numrows($res_imp) > 0) {
			$retour .= '<tr>
							<td width="100%" align="center">
								<select id="imp_creator" name="imp_creator" style="width:220px;" size="10">';
			while($tab_pimp = $db->fetchrow($res_imp)) {
					$retour .= '<option value="'.$tab_pimp['id_contact'].'">'.$tab_pimp['lastname'].' '.$tab_pimp['firstname'].'</option>';
			}
			$retour .= '		</select>
							</td>
						</tr>';
		}else {
			$retour .= '<tr><td>'.$_DIMS['cste']['_DIMS_LABEL_NO_RESP_CONT_SEARCH'].'</td></tr>';
		}
		$retour .= '</table>';
		echo $retour;
		ob_end_flush();
		die();
		break;

	case "droptableimport_ent" :
		$sql = "TRUNCATE TABLE `dims_mod_business_tiers_import`";
		$db->query($sql);
		dims_redirect("admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_IMPORT."&part="._BUSINESS_TAB_IMPORT."");
		break;

	case 'import_ent':
	$include = DIMS_APP_PATH . '/modules/system/lfb/lfb_admin_index_ent_import.php';
	//dims_redirect('admin.php?cat='._BUSINESS_CAT_CONTACT.'&action='._BUSINESS_TAB_IMPORT.'&part='._BUSINESS_TAB_IMPORT.'');
	break;

	case 'del_imp_ent':
		require_once(DIMS_APP_PATH . "/modules/system/class_tiers_import.php");

		$id_imp = dims_load_securvalue('id_imp', dims_const::_DIMS_NUM_INPUT, true, true);

		$imp = new tiers_import();
		$imp->open($id_imp);
		$imp->delete($id_imp);

	break;

	case 'add_imp_ent_old':

		require_once(DIMS_APP_PATH . "/modules/system/class_tiers.php");
		require_once(DIMS_APP_PATH . "/modules/system/class_tiers_import.php");

		$id_imp = dims_load_securvalue('id_imp', dims_const::_DIMS_NUM_INPUT, true, true);
		$id_tiers = dims_load_securvalue('id_tiers', dims_const::_DIMS_NUM_INPUT, true, true);

		$imp = new tiers_import();
		$imp->open($id_imp);

		$old_ent = new tiers();
		$old_ent->open($id_tiers);

		if(isset($imp->fields['adresse']))					$old_ent->fields['adresse']					= $imp->fields['adresse'];
		if(isset($imp->fields['codepostal']))				$old_ent->fields['codepostal']				= $imp->fields['codepostal'];
		if(isset($imp->fields['ville']))					$old_ent->fields['ville']					= $imp->fields['ville'];
		if(isset($imp->fields['telephone']))				$old_ent->fields['telephone']				= $imp->fields['telephone'];
		if(isset($imp->fields['telecopie']))				$old_ent->fields['telecopie']				= $imp->fields['telecopie'];
		if(isset($imp->fields['mel']))						$old_ent->fields['mel']						= $imp->fields['mel'];
		if(isset($imp->fields['ent_capital']))				$old_ent->fields['ent_capital']				= $imp->fields['ent_capital'];
		if(isset($imp->fields['ent_codenace']))				$old_ent->fields['ent_activiteprincipale']	= $imp->fields['ent_codenace'];
		if(isset($imp->fields['ent_effectif']))				$old_ent->fields['ent_effectif']			= $imp->fields['ent_effectif'];
		if(isset($imp->fields['ent_datecreation']))			$old_ent->fields['ent_datecreation']		= $imp->fields['ent_datecreation'];
		if(isset($imp->fields['pays']))						$old_ent->fields['pays']					= $imp->fields['pays'];
		if(isset($imp->fields['ent_codenace']))				$old_ent->fields['ent_codenace']			= $imp->fields['ent_codenace'];
		if(isset($imp->fields['site_web']))					$old_ent->fields['site_web']				= $imp->fields['site_web'];
		if(isset($imp->fields['dirigeant']))				$old_ent->fields['dirigeant']				= $imp->fields['dirigeant'];
		if(isset($imp->fields['presentation']))				$old_ent->fields['presentation']			= $imp->fields['presentation'];

		$old_ent->save();
		$imp->delete();

		break;
	case 'add_imp_ent':

		require_once(DIMS_APP_PATH . "/modules/system/class_tiers.php");
		require_once(DIMS_APP_PATH . "/modules/system/class_tiers_import.php");

		$id_imp = dims_load_securvalue('id_imp', dims_const::_DIMS_NUM_INPUT, true, true);

		$imp = new tiers_import();
		$imp->open($id_imp);

		$new_ent = new tiers();
		$new_ent->init_description();

		$new_ent->fields['intitule'] = $imp->fields['intitule'];
		$new_ent->fields['intitule_search'] = $imp->fields['intitule_search'];
		if(isset($imp->fields['adresse']))					$new_ent->fields['adresse']					= $imp->fields['adresse'];
		if(isset($imp->fields['codepostal']))				$new_ent->fields['codepostal']				= $imp->fields['codepostal'];
		if(isset($imp->fields['ville']))					$new_ent->fields['ville']					= $imp->fields['ville'];
		if(isset($imp->fields['telephone']))				$new_ent->fields['telephone']				= $imp->fields['telephone'];
		if(isset($imp->fields['telecopie']))				$new_ent->fields['telecopie']				= $imp->fields['telecopie'];
		if(isset($imp->fields['mel']))						$new_ent->fields['mel']						= $imp->fields['mel'];
		if(isset($imp->fields['ent_capital']))				$new_ent->fields['ent_capital']				= $imp->fields['ent_capital'];
		if(isset($imp->fields['ent_codenace']))				$new_ent->fields['ent_activiteprincipale']	= $imp->fields['ent_codenace'];
		if(isset($imp->fields['ent_effectif']))				$new_ent->fields['ent_effectif']			= $imp->fields['ent_effectif'];
		if(isset($imp->fields['ent_datecreation']))			$new_ent->fields['ent_datecreation']		= $imp->fields['ent_datecreation'];
		if(isset($imp->fields['pays']))						$new_ent->fields['pays']					= $imp->fields['pays'];
		if(isset($imp->fields['ent_codenace']))				$new_ent->fields['ent_codenace']			= $imp->fields['ent_codenace'];
		if(isset($imp->fields['site_web']))					$new_ent->fields['site_web']				= $imp->fields['site_web'];
		if(isset($imp->fields['dirigeant']))				$new_ent->fields['dirigeant']				= $imp->fields['dirigeant'];
		if(isset($imp->fields['presentation']))				$new_ent->fields['presentation']			= $imp->fields['presentation'];
		$new_ent->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
		$new_ent->fields['id_user'] = $_SESSION['dims']['userid'];

		$new_ent->save();
		$imp->delete();

	break;
	case 'view_new_ent':
		require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_import_view_new.php');
	break;

	case 'view_old_ent':
		require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_import_view_old.php');
	break;

	case "view_ent_oldct" :
		ob_start();

		require_once(DIMS_APP_PATH . "/modules/system/class_contact.php");

		$retour = '';
		$sql_v1 = 'SELECT * FROM dims_mod_business_contact_import WHERE exist NOT LIKE \'0\' AND exist NOT LIKE \'-1\' AND exist_ent LIKE \'0\'';
		$res_v1 = $db->query($sql_v1);

		$retour .= $skin->open_simplebloc($_DIMS['cste']['_LABEL_ADMIN_NEW_ENT_OLDCT'],'width:100%;float:left;clear:none;','','');
		$retour .= '<table width="100%" cellpadding="0" cellspacing="0" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;">
					<tr class="trl1" style="font-size:12px;">
						<td style="width: 5%;"></td>
						<td style="width: 30%;">'.$_DIMS['cste']['_DIMS_LABEL_PERSONNE'].'</td>
						<td style="width: 30%;">'.$_DIMS['cste']['_DIMS_LABEL_GROUP_LIST'].'</td>
						<td style="width: 20%;">'.$_DIMS['cste']['_DIMS_LABEL_FUNCTION'].'</td>
						<td style="width: 15%;">'.$_DIMS['cste']['_DIMS_OPTIONS'].'</td>
					</tr>';
		$i = 0;
		$class_col = 'trl1';
		while($tab_imp = $db->fetchrow($res_v1)) {
			$bdd_ct = new contact();
			$bdd_ct->open($tab_imp['exist']);

			if ($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
			$retour .= '<tr class="'.$class_col.'">
							<td><input type="checkbox" id="ent_imp_'.$i.'" value="'.$tab_imp['id'].'"/></td>
							<td><a href="admin.php?action='._BUSINESS_TAB_CONTACT_FORM.'&part='._BUSINESS_TAB_CONTACT_IDENTITE.'&contact_id='.$bdd_ct->fields['id'].'" target="_BLANK">'.$tab_imp['firstname'].' '.$tab_imp['lastname'].'</a></td>
							<td>'.$tab_imp['company'].'</a></td>
							<td>'.$tab_imp['jobtitle'].'</td>
							<td>
								<a href="javascript:void(0);" onclick="javascript:view_detail_imp(\'detail_imp_'.$tab_imp['id'].'\');" title="'.$_DIMS['cste']['_DIMS_OBJECT_DISPLAY'].'"><img src="./common/img/view.png" style="border:0px;"/></a>
								 / <a href="javascript:void(0);" onclick="javascript:add_impct_oldpers(\''.$tab_imp['id'].'\', \'ent_old_ct_import\', \'view_ent_oldct\', \'\',\'\')" title="'.$_DIMS['cste']['_DIMS_LABEL_ADD_OLDPERS_ONLY'].'"><img src="./common/img/add_user.png" style="border:0px;"/></a>
								 / <a href="javascript:void(0);" onclick="javascript:add_impct_ent(\''.$tab_imp['id'].'\', \'ent_old_ct_import\', \'view_ent_oldct\')" title="'.$_DIMS['cste']['_DIMS_LABEL_ADD_ENT_ONLY'].'"><img src="./common/img/add_factory.png" style="border:0px;"/></a>
								 / <a href="javascript:void(0);" onclick="javascript:add_impct_all_oldct(\''.$tab_imp['id'].'\', \'ent_old_ct_import\', \'view_ent_oldct\')" title="'.$_DIMS['cste']['_DIMS_LABEL_ADD_ALL'].'"><img src="./common/img/add.gif" style="border:0px;"/></a>
								 / <a href="javascript:void(0);" onclick="javascript:del_impct(\''.$tab_imp['id'].'\', \'ent_old_ct_import\', \'view_ent_oldct\')" title="'.$_DIMS['cste']['_DELETE'].'"><img src="./common/modules/system/img/del.png" style="border:0px;"/></a>
							</td>
						</tr>
						<tr class="'.$class_col.'">
							<td colspan="5">
								<div id="detail_imp_'.$tab_imp['id'].'" style="width:100%;display:none;">
									<table width="100%">
										<tr>
											<td width="100%" style="font-weight:bold;font-size:13px;">'.$_DIMS['cste']['_DIMS_TITLE_INF_PERS_IMP'].'</td>
										</tr>
										<tr>
											<td width="100%" align="center">
												<table width="90%" cellpadding="0" cellspacing="0" style="border: 1px solid rgb(115, 140, 173);margin-top:10px;margin-bottom:10px;">
													<tr class="trl1">
														<td width="20%" align="right"></td>
														<td width="40%" align="left">Donn&eacute;es import&eacute;es</td>
														<td width="40%" align="left">Fiche existante</td>
													</tr>
													<tr class="trl2">
														<td width="20%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_ADDRESS'].' : </td>
														<td width="40%" align="left">'.$tab_imp['address'].'</td>
														<td width="40%" align="left">'.$bdd_ct->fields['address'].'</td>
													</tr>
													<tr class="trl1">
														<td width="20%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_CP'].' : </td>
														<td width="40%" align="left">'.$tab_imp['cp'].'</td>
														<td width="40%" align="left">'.$bdd_ct->fields['postalcode'].'</td>
													</tr>
													<tr class="trl2">
														<td width="20%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_CITY'].' : </td>
														<td width="40%" align="left">'.$tab_imp['city'].'</td>
														<td width="40%" align="left">'.$bdd_ct->fields['city'].'</td>
													</tr>
													<tr class="trl1">
														<td width="20%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_COUNTRY'].' : </td>
														<td width="40%" align="left">'.$tab_imp['country'].'</td>
														<td width="40%" align="left">'.$_DIMS['cste'][$bdd_ct->fields['country']].'</td>
													</tr>
													<tr class="trl2">
														<td width="20%" align="right">'.$_DIMS['cste']['_DIRECTORY_PHONE'].' : </td>
														<td width="40%" align="left">'.$tab_imp['phone'].'</td>
														<td width="40%" align="left">'.$bdd_ct->fields['phone'].'</td>
													</tr>
													<tr class="trl1">
														<td width="20%" align="right">'.$_DIMS['cste']['_MOBILE'].' : </td>
														<td width="40%" align="left">'.$tab_imp['mobile'].'</td>
														<td width="40%" align="left">'.$bdd_ct->fields['mobile'].'</td>
													</tr>
													<tr class="trl2">
														<td width="20%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_FAX'].' : </td>
														<td width="40%" align="left">'.$tab_imp['fax'].'</td>
														<td width="40%" align="left">'.$bdd_ct->fields['fax'].'</td>
													</tr>
													<tr class="trl1">
														<td width="20%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_EMAIL'].' : </td>
														<td width="40%" align="left">'.$tab_imp['email'].'</td>
														<td width="40%" align="left">'.$bdd_ct->fields['email'].'</td>
													</tr>
												</table>
											</td>
										</tr>
										<tr>
											<td width="100%" style="font-weight:bold;font-size:13px;">'.$_DIMS['cste']['_DIMS_TITLE_COMP_ENT_IMP'].'</td>
										</tr>
										<tr>
											<td width="100%" align="center">
												<table width="100%">
													<tr>
														<td width="20%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_ADDRESS'].' : </td>
														<td width="30%" align="left">'.$tab_imp['address'].'</td>
														<td width="20%" align="right"></td>
														<td width="30%" align="left"></td>
													</tr>
													<tr>
														<td width="20%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_CP'].' : </td>
														<td width="30%" align="left">'.$tab_imp['cp'].'</td>
														<td width="20%" align="right"></td>
														<td width="30%" align="left"></td>
													</tr>
													<tr>
														<td width="20%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_CITY'].' : </td>
														<td width="30%" align="left">'.$tab_imp['city'].'</td>
														<td width="20%" align="right"></td>
														<td width="30%" align="left"></td>
													</tr>
													<tr>
														<td width="20%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_COUNTRY'].' : </td>
														<td width="30%" align="left">'.$tab_imp['country'].'</td>
														<td width="20%" align="right"></td>
														<td width="30%" align="left"></td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</div>
							</td>
						</tr>';
			$i++;
		}
		$retour .= '</table>';
		$retour .= '<div style="float: left;"><img border="0" alt="0" src="./common/img/arrow_ltr.png"/></div>';
		$retour .= '<div style="float: left; margin-top: 4px;"><a onclick="javascript:selectAll();" style="color: rgb(115, 140, 173);" href="javascript:void(0);">Tout cocher</a> /�<a onclick="javascript:unselectAll();" style="color: rgb(115, 140, 173);" href="javascript:void(0);">Tout d�cocher</a></div>';
		$retour .= '<div style="float:right;margin-top:4px;"><select id="ent_action"><option value="suppr">Supprimer</option></select>';
		$retour .= $skin->close_simplebloc();
		echo $retour;
		ob_end_flush();
		die();
		break;
}
?>
