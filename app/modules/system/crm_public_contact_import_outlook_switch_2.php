<?php

if($op == 1){
	$content_contact_import = "<p style='text-align:center;'>".count($_SESSION['dims']['IMPORT_IGNORED_CONTACT'])." ".$_SESSION['cste']['_IMPORT_COUNT_OF_IGNORED_CONTACTS'].".<br/><br/>
		<a href='javascript:void(0);' onclick='javascript:dims_switchdisplay(\"list_ignored_contacts\");'><img src='./common/img/view.png' alt='view'/>&nbsp;".$_DIMS['cste']['_IMPORT_VIEW_LISTE']."</a>";

	$content_contact_import .= "<div id='list_ignored_contacts' style='display:none;'>";
	$content_contact_import .= '<table width="100%" cellpadding="0" cellspacing="0" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;">
				<tr class="trl1" style="font-size:12px;">
					 <td style="width: 20%;font-weight:bold;">'.$_DIMS['cste']['_FIRSTNAME'].'</td>
					<td style="width: 20%;font-weight: bold;">'.$_DIMS['cste']['_DIMS_LABEL_NAME'].'</td>
					<td style="width: 20%;font-weight: bold;">'.$_DIMS['cste']['_DIMS_LABEL_EMAIL'].'</td>
					<td style="width: 20%;font-weight: bold;">'.$_DIMS['cste']['_DIMS_LABEL_COMPANY'].'</td>
				</tr>';
	$i = 0;
	$class_col = 'trl1';
	foreach($_SESSION['dims']['IMPORT_IGNORED_CONTACT'] AS $tab_imp){
		if ($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
		$content_contact_import .= '<tr class="'.$class_col.'">
						<td>'.$tab_imp['firstname'].'</td>
						<td>'.$tab_imp['lastname'].'</td>
						<td>'.$tab_imp['email'].'</td>
						<td>'.$tab_imp['company'].'</td>
		</tr>';
		$i++;
	}
	$content_contact_import .= '</table>';
	$content_contact_import .= "</div>";
	$content_contact_import .= "</p>";

	$content_contact_import .=	"<p style='text-align:center;'>";

	$content_contact_import .= count($_SESSION['dims']['IMPORT_KNOWN_CONTACTS'])." ".$_DIMS['cste']['_IMPORT_CONTACTS_ALREADY_EXISTS']." ".str_replace('[x]', $_SESSION['dims']['IMPORT_COUNT_UPDATE'] ,$_SESSION['cste']['_IMPORT_UPDATED_CONTACTS'])."<br/><br/>
		<a href='javascript:void(0);' onclick='javascript:dims_switchdisplay(\"list_exist_contacts\");'><img src='./common/img/view.png' alt='view'/>&nbsp;".$_DIMS['cste']['_IMPORT_VIEW_LISTE']."</a>";

	$content_contact_import .= "<div id='list_exist_contacts' style='display:none;'>";
	$content_contact_import .= '<table width="100%" cellpadding="0" cellspacing="0" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;">
				<tr class="trl1" style="font-size:12px;">
					<td style="width: 30%;font-weight:bold;">'.$_DIMS['cste']['_FIRSTNAME'].'</td>
					<td style="width: 30%;font-weight: bold;">'.$_DIMS['cste']['_DIMS_LABEL_NAME'].'</td>
					<td style="width: 40%;font-weight: bold;">'.$_DIMS['cste']['_DIMS_LABEL_EMAIL'].'</td>
				</tr>';
	$i = 0;
	$class_col = 'trl1';
	foreach($_SESSION['dims']['IMPORT_KNOWN_CONTACTS'] AS $tab_imp['id'] => $tab_imp){
		if($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
		if(isset($tab_imp['modif']))
			$modif = "<img src='./common/img/icon_model.gif' alt=''/>";
		else
			$modif = "&nbsp;";
		$content_contact_import .= '<tr class="'.$class_col.'">
						<td>'.$modif.' '.$tab_imp['firstname'].'</td>
						<td>'.$tab_imp['lastname'].'</td>
						<td>'.$tab_imp['email'].'</td>
		</tr>';
		$i++;
	}
	$content_contact_import .= '</table>';
	$content_contact_import .= "</div>";
	$content_contact_import .= "</p>";
	$content_contact_import .= dims_create_button($_DIMS['cste']['_DIMS_BACK'], "./common/img/undo.gif", "dims_redirect('./admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_IMPORT_OUTLOOK."&part="._BUSINESS_TAB_IMPORT_OUTLOOK."&op=1');");
	$content_contact_import .= "<p style='text-align:center;'>";
	$content_contact_import .= '<div style="text-align:center;">
										  '.dims_create_button($_DIMS['cste']['_DIMS_CONTINUE'], "./common/img/public.png", "dims_redirect('./admin.php?cat="._BUSINESS_CAT_CONTACT."&action="._BUSINESS_TAB_IMPORT_OUTLOOK."&part="._BUSINESS_TAB_IMPORT_OUTLOOK."&op=2');").'
								</div>';
	$content_contact_import .= "</p>";

	//break;
}

if($op == 2){
	///////// Calcul des similitudes ///////////
	$lev_nom = 0;
	$lev_pre = 0;
	$coef_nom = 0;
	$coef_pre = 0;
	$coef_tot = 0;
	$count_similariy = 0;
	$count_ignored_similarity = 0;
	$_SESSION['dims']['import_contact_similar'] = array();

	foreach($_SESSION['dims']['IMPORT_CONTACT'] AS $ct_id => $tab_contact_new){
		foreach($_SESSION['dims']['DB_CONTACT'] AS $tab_contact){

			$lev_nom = levenshtein(strtoupper($tab_contact_new['lastname']), strtoupper($tab_contact['lastname']));
			$coef_nom = $lev_nom - (ceil(strlen($tab_contact_new['lastname'])/4));

			$lev_pre = levenshtein(strtoupper($tab_contact_new['firstname']), strtoupper($tab_contact['firstname']));
			$coef_pre = $lev_pre - (ceil(strlen($tab_contact_new['firstname'])/4));

			$coef_tot = $coef_nom + $coef_pre;
//				  echo "levenshtein : ".$tab_contact_new['lastname']." ".$tab_contact_new['firstname']." et ".$tab_contact['lastname']." ".$tab_contact['firstname']." le total : ".$coef_tot."<br/>";
//				  echo "les variables : lev du nom = ".$lev_nom." ; lev du prenom = ".$lev_pre." ; coef nom = ".$coef_nom." ; coef prenom = ".$coef_pre."<br/><br/>";

			if($coef_nom<=1 && $coef_tot < 2 && $tab_contact_new['lastname']!='' && $tab_contact_new['firstname'] !='') {

				//On verifie qu'il n'y ait pas déjà ce contact dans la table d'import
				$sqlv = "SELECT		*
						 FROM		dims_mod_business_contact_import
						 WHERE		firstname LIKE :firstname
						 AND		lastname LIKE :lastname
						 AND		company LIKE :company
						 AND		email LIKE :email
						 AND		(id_user_create = :userid OR id_importer = :userid ) ";

				$resv = $db->query($sqlv, array(
					':firstname'	=> $tab_contact_new['firstname'],
					':lastname'		=> $tab_contact_new['lastname'],
					':company'		=> $tab_contact_new['company'],
					':email'		=> $tab_contact_new['email'],
					':userid'		=> $_SESSION['dims']['userid']
				));
				if($db->numrows($resv) > 0) {
					$count_ignored_similarity++;
				} else {

				//on stock les contacts similaires en base
				$imp = new contact_import();
				$imp->init_description();

				//donnees obligatoires
				$imp->fields['lastname'] = $tab_contact_new['lastname'];
				$imp->fields['firstname'] = $tab_contact_new['firstname'];
				$imp->fields['email'] = $tab_contact_new['email'];
				$imp->fields['id_user_create'] = $_SESSION['dims']['import_id_user'];
				$imp->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
				$imp->fields['id_importer'] = $_SESSION['dims']['userid'];

				//donnees facultatives
				if(!empty($tab_contact_new['company']))			$imp->fields['company'] = $tab_contact_new['company'];
				if(!empty($tab_contact_new['address']))			$imp->fields['address'] = $tab_contact_new['address'];
				if(!empty($tab_contact_new['address2']))		$imp->fields['address'] .= $tab_contact_new['address2'];
				if(!empty($tab_contact_new['address3']))		$imp->fields['address'] .= $tab_contact_new['address3'];
				if(!empty($tab_contact_new['cp']))				$imp->fields['cp'] = $tab_contact_new['cp'];
				if(!empty($tab_contact_new['ville']))			$imp->fields['city'] = $tab_contact_new['ville'];
				if(!empty($tab_contact_new['phone']))			$imp->fields['phone'] = $tab_contact_new['phone'];
				if(!empty($tab_contact_new['phone2']))			$imp->fields['phone2'] = $tab_contact_new['phone2'];
				if(!empty($tab_contact_new['fax']))				$imp->fields['fax'] = $tab_contact_new['fax'];
				if(!empty($tab_contact_new['mobile']))			$imp->fields['mobile'] = $tab_contact_new['mobile'];
				if(!empty($tab_contact_new['civilite']))		$imp->fields['titre'] = $tab_contact_new['civilite'];
				if(!empty($tab_contact_new['country']))			$imp->fields['country'] = $tab_contact_new['country'];
				if(!empty($tab_contact_new['professional']))	$imp->fields['jobtitle'] = $tab_contact_new['professional'];
				if(!empty($tab_contact_new['email2']))			$imp->fields['email2'] = $tab_contact_new['email2'];
				if(!empty($tab_contact_new['email3']))			$imp->fields['email3'] = $tab_contact_new['email3'];
				if(!empty($tab_contact_new['comment']))			$imp->fields['comment'] = $tab_contact_new['comment'];

				//on verifie si l'entreprise rattachee existe
				if(!empty($tab_contact_new['company'])) {
					$sql_vent = "SELECT id FROM dims_mod_business_tiers WHERE intitule_search LIKE :intitule ";
					$res_vent = $db->query($sql_vent, array(
						':intitule' => strtoupper($tab_contact_new['company'])
					));
					if($db->numrows($res_vent) == 1) {
						$tab_vent = $db->fetchrow($res_vent);
						$imp->fields['exist_ent'] = $tab_vent['id'];
					}
					elseif($db->numrows($res_vent) == 0 ) {
						//on va chercher des similitudes eventuelles sur les entreprises
						unset($_SESSION['dims']['ALL_ENTS']);
						// on recharge pour prendre en compte les nouvelles entreprises
						if(!isset($_SESSION['dims']['ALL_ENTS'])) {
							//si la session n'existe pas, on y place toutes les entreprises en vu de la comparaison
							$sql_e = "SELECT id, intitule FROM dims_mod_business_tiers ORDER BY intitule";
							$res_e = $db->query($sql_e);
							while($tab_e = $db->fetchrow($res_e)) {
								$_SESSION['dims']['ALL_ENTS'][$tab_e['id']] = $tab_e;
							}
						}
						//on compare la valeur courante avec les entreprises en session
						foreach($_SESSION['dims']['ALL_ENTS'] as $id_entc => $tab_ent) {
							if ($tab_ent['intitule']!="" && isset($current_line)) {
								$lev_nom = levenshtein(strtoupper($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['company']), strtoupper($tab_ent['intitule']));
								$coef_nom = $lev_nom - (ceil(strlen($_SESSION['dims']['IMPORT_CONTACT'][$current_line]['company'])/4));

								$coef_tot = $coef_nom;

								if($coef_tot < 3) {
									//on stock les entreprises similaires en base
									$ent_sim = new tiers_similar();
									$ent_sim->init_description();
									if ($current_line>0) {
										$res_tmp=$db->query("SELECT id
															FROM dims_mod_business_contact_import_ent_similar
															WHERE id_contact= :idcontact
															AND id_ent_similar= :identsimilar
															AND id_user= :iduser
															AND id_workspace= :idworkspace ", array(
												':idcontact'	=> $current_line,
												':identsimilar'	=> $id_entc,
												':iduser'		=> $_SESSION['dims']['userid'],
												':idworkspace'	=> $_SESSION['dims']['workspaceid']
										));
										if ($db->numrows($res_tmp)==0) {
											$ent_sim->fields['id_contact'] = $current_line;
											$ent_sim->fields['ent_intitule'] = $_SESSION['dims']['IMPORT_CONTACT'][$current_line]['company'];
											$ent_sim->fields['id_ent_similar'] = $id_entc;
											$ent_sim->fields['intitule_ent_similar'] = $tab_ent['intitule'];
											$ent_sim->fields['id_user'] = $_SESSION['dims']['userid'];
											$ent_sim->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];

											$ent_sim->save();
										}
									}
								}
							}
						}
					}
				}

				$imp->save();

				$count_similariy++;
				}

				//on ote de le contact de la session
				unset($_SESSION['dims']['IMPORT_CONTACT'][$ct_id]);

				//on a trouve au moins 1 contact similaire, on peut stopper le foreach
				//on se souhaite pas enregistrer plusieurs fois le méme contact s'il y a plusieurs similitudes
				break;
				//$_SESSION['dims']['import_contact_similar'][$ct_id][] = $tab_contact['id'];

			}
		}
	}
	//dims_print_r($_SESSION['dims']['import_contact_similar']);
	//break;
	$_SESSION['dims']['import_count_similar_ignored'] = $count_ignored_similarity;
	$_SESSION['dims']['import_count_similar'] = $count_similariy;
	$_SESSION['dims']['import_contact_similar_count'] = 1;
	require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_import_outlook_switch_3.php');
}
?>
