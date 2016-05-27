<?php
$retour = "";
$tab_vall = array();
$nom = dims_load_securvalue('nom', dims_const::_DIMS_CHAR_INPUT, true);
$prenom = dims_load_securvalue('prenom', dims_const::_DIMS_CHAR_INPUT, true);

$nom = strtolower(trim($nom));
$prenom = strtolower(trim($prenom));

//on test si on a une correspondance exacte au niveau du nom
$sql_verif = "SELECT * FROM dims_mod_business_contact WHERE lastname LIKE :nom ";
$res_v = $db->query($sql_verif, array(
	':nom'	=> $nom
));

if($db->numrows($res_v) > 0) {
	//on verifie au niveau du prenom
	//on met les donnees dans un tableau en vue de leur comparaison
	$tab_vpn = array();
	while($tab_ct = $db->fetchrow($res_v)) {
		$tab_vpn[] = $tab_ct;
	}
	//pour chaque element retenu, on va comparer a l'aide d'un coefficient d'acceptabilite
	foreach($tab_vpn as $key => $similar_ct) {
		$similar_ct['firstname'] = strtolower(trim($similar_ct['firstname']));
		$lev_p = levenshtein($prenom, $similar_ct['firstname']);

		if($lev_p == 0) {
			//on est dans le cas ou on a une correspondance exacte (nom et prenom)
			if(!isset($tab_vall['exact_all'])) $tab_vall['exact_all'] = array();
			$tab_vall['exact_all'][$similar_ct['id']] = $similar_ct;
		}
		else {
			//on calcul un coefficient d'acceptabilite de la similarite
			$coeff_sim = '';
			$mod_coeff_sim = '';
			//le coefficient d'acceptabilite est proportionnel a la taille de la chaine de caract�re
			//si le resultat n'est pas un entier, on arrondi toujours � l'entier superieur
			$coeff_sim = ceil(strlen($similar_ct['firstname'])/4);
			if($lev_p <= $coeff_sim) {
				if(!isset($tab_vall['exact_name'])) $tab_vall['exact_name'] = array();
				$tab_vall['exact_name'][$similar_ct['id']] = $similar_ct;
				//$retour .= $similar_ct['id']." similarite : ".$lev_p."<br/>";
			}
		}
	}
}
//on va utiliser levenshtein et comparer avec l'ensemble des contacts presents dans la base
$sql_all = "SELECT * FROM dims_mod_business_contact WHERE lastname NOT LIKE :nom ";
$res_all = $db->query($sql_all, array(
	':nom'	=> $nom
));

$tab_simname = array();
while($tab_ct = $db->fetchrow($res_all)) {
	//on met les champs au bon format
	$tab_ct['lastname'] = strtolower(trim($tab_ct['lastname']));
	$tab_ct['firstname'] = strtolower(trim($tab_ct['firstname']));
	//on calcul la distance par rapport au nom de la recherche
	$lev_n = levenshtein($nom, $tab_ct['lastname']);
	//on calcul un coefficient d'acceptabilite de la similarite puis on compare avec le levenshtein
	$coeff_sim = '';
	$mod_coeff_sim = '';
	$coeff_sim = ceil(strlen($tab_ct['lastname'])/4);
	if($lev_n <= $coeff_sim) $tab_simname[$tab_ct['id']] = $tab_ct;
}

foreach($tab_simname as $id_ct => $tab_simct) {
	//on calcul la distance par rapport au prenom de la recherche
	$lev_pre = levenshtein($prenom, $tab_simct['firstname']);
	//on calcul un coefficient d'acceptabilite de la similarite puis on compare avec le levenshtein
	$coeff_sim_pre = '';
	$mod_coeff_sim_pre = '';
	$coeff_sim_pre = ceil(strlen($tab_ct['firstname'])/4);
	if($lev_pre <= $coeff_sim_pre) {
		//on est dans le cas ou on a nom et prenom proches
		if(!isset($tab_vall['prox_np'])) $tab_vall['prox_np'] = array();
		$tab_vall['prox_np'][$id_ct] = $tab_simct;
	}
	else {
		//on est dans le cas ou on a nom proche mais prenom quelconque
		if(!isset($tab_vall['less_prox'])) $tab_vall['less_prox'] = array();
		$tab_vall['less_prox'][$id_ct] = $tab_simct;
	}
}

if(count($tab_vall) > 0) {
	//affichage des resultats
	//4 cas possibles :
	//nom et prenom exacts : $tab_vall['exact_all']
	//nom exact, prenom != : $tab_vall['exact_name']
	//nom et prenom proches: $tab_vall['prox_np']
	//nom proche prenom != : $tab_vall['less_prox']

	$retour .= '<div class="actions"></div><h2>'.$_DIMS['cste']['_DIMS_LABEL_SIMILAR_CT'].'</h2>';
	$retour .= '<div style="overflow-x:auto;height:400px;margin-right:1px;">';
	$retour .= '<table width="100%" cellpadding="0" cellspacing="0">';
	if(!empty($tab_vall['exact_all'])) {
		$class_col = 'trl1';
		$nb_exact = count($tab_vall['exact_all']);
		$retour .= '<tr style="padding-bottom:10px;">
						<td style="padding:15px;">
							<table width="100%" cellpadding="0" cellspacing="0" style="border:#58698B 1px solid">
								<tr class="trl1">
									<td align="center" colspan="3">';
		if($nb_exact > 1)	$retour .= $_DIMS['cste']['_DIMS_LABEL_EXACT_CORRESP_PLUR'].' ('.$nb_exact.')';
		else				$retour .= $_DIMS['cste']['_DIMS_LABEL_EXACT_CORRESP_SING'].' ('.$nb_exact.')';
		$retour .=					'</td>
								</tr>';
		foreach($tab_vall['exact_all'] as $id_ct => $inf_ct) {
			if ($class_col == 'trl1') $class_col = 'trl2'; else $class_col = 'trl1';
			$retour .= '		<tr class="'.$class_col.'">
									<td align="left" style="padding-left:15px;">
										<a style="cursor:pointer;" onclick="javascript:displayInfoCtCrm('.$id_ct.');" target="_blank" title="'.$_DIMS['cste']['_DIMS_LABEL_AFFICH_INF_CT'].'">
											'.strtoupper($inf_ct['lastname']).' '.$inf_ct['firstname'].'
										</a>
									</td>
									<td align="left" style="padding-left:15px;">
										'.$inf_ct['city'].'
									</td>
									<td>';
			if($inf_ct['inactif'] == 1) {
				$retour .= '			<img src="./common/img/important_small.png" title="'.$_DIMS['cste']['_DIMS_LABEL_FICHE_SUPPR'].'"/>';
			}
			$retour .=				'</td>
								</tr>';
		}
		$retour .= '		</table>
						</td>
					</tr>';
	}
	if(!empty($tab_vall['exact_name'])) {
		$class_col = 'trl1';
		$nb_exact = count($tab_vall['exact_name']);
		$retour .= '<tr>
						<td style="padding:15px;">
							<table width="100%" cellpadding="0" cellspacing="0" style="border:#58698B 1px solid">
								<tr class="trl1">
									<td align="center" colspan="2">'.$_DIMS['cste']['_DIMS_LABEL_EXACT_NAME'].' ('.$nb_exact.')
									</td>
								</tr>';
		foreach($tab_vall['exact_name'] as $id_ct => $inf_ct) {
			if ($class_col == 'trl1') $class_col = 'trl2'; else $class_col = 'trl1';
			$retour .= '		<tr class="'.$class_col.'">
									<td align="left" style="padding-left:15px;">
										<a style="cursor:pointer;" onclick="javascript:displayInfoCtCrm('.$id_ct.');" target="_blank" title="'.$_DIMS['cste']['_DIMS_LABEL_AFFICH_INF_CT'].'">
											'.strtoupper($inf_ct['lastname']).' '.$inf_ct['firstname'].'
										</a>
									</td>
									<td align="left" style="padding-left:15px;">
										'.$inf_ct['city'].'
									</td>
									<td>';
			if($inf_ct['inactif'] == 1) {
				$retour .= '			<img src="./common/img/important_small.png" title="'.$_DIMS['cste']['_DIMS_LABEL_FICHE_SUPPR'].'"/>';
			}
			$retour .=				'</td>
								</tr>';
		}
		$retour .= '		</table>
						</td>
					</tr>';
	}
	if(!empty($tab_vall['prox_np'])) {
		$class_col = 'trl1';
		$nb_exact = count($tab_vall['prox_np']);
		$retour .= '<tr>
						<td style="padding:15px;">
							<table width="100%" cellpadding="0" cellspacing="0" style="border:#58698B 1px solid">
								<tr class="trl1">
									<td align="center" colspan="2">'.$_DIMS['cste']['_DIMS_LABEL_PROX_NP'].' ('.$nb_exact.')
									</td>
								</tr>';
		foreach($tab_vall['prox_np'] as $id_ct => $inf_ct) {
			if ($class_col == 'trl1') $class_col = 'trl2'; else $class_col = 'trl1';
			$retour .= '		<tr class="'.$class_col.'">
									<td align="left" style="padding-left:15px;">
										<a style="cursor:pointer;" onclick="javascript:displayInfoCtCrm('.$id_ct.');" target="_blank" title="'.$_DIMS['cste']['_DIMS_LABEL_AFFICH_INF_CT'].'">
											'.strtoupper($inf_ct['lastname']).' '.$inf_ct['firstname'].'
										</a>
									</td>
									<td align="left" style="padding-left:15px;">
										'.$inf_ct['city'].'
									</td>
									<td>';
			if($inf_ct['inactif'] == 1) {
				$retour .= '			<img src="./common/img/important_small.png" title="'.$_DIMS['cste']['_DIMS_LABEL_FICHE_SUPPR'].'"/>';
			}
			$retour .=				'</td>
								</tr>';
		}
		$retour .= '		</table>
						</td>
					</tr>';
	}
	if(!empty($tab_vall['less_prox'])) {
		$class_col = 'trl1';
		$nb_exact = count($tab_vall['less_prox']);
		$retour .= '<tr>
						<td style="padding:15px;">
							<table width="100%" cellpadding="0" cellspacing="0" style="border:#58698B 1px solid">
								<tr class="trl1">
									<td align="center" style="padding-left:15px;" colspan="2">';
		if($nb_exact > 1)	$retour .= $_DIMS['cste']['_DIMS_LABEL_LESS_PROX_PLUR'].' ('.$nb_exact.')';
		else				$retour .= $_DIMS['cste']['_DIMS_LABEL_LESS_PROX_SING'].' ('.$nb_exact.')';
		$retour .=					'</td>
								</tr>';
		foreach($tab_vall['less_prox'] as $id_ct => $inf_ct) {
			if ($class_col == 'trl1') $class_col = 'trl2'; else $class_col = 'trl1';
			$retour .= '		<tr class="'.$class_col.'">
									<td align="left" style="padding-left:15px;">
										<a style="cursor:pointer;" onclick="javascript:displayInfoCtCrm('.$id_ct.');" target="_blank" title="'.$_DIMS['cste']['_DIMS_LABEL_AFFICH_INF_CT'].'">
											'.strtoupper($inf_ct['lastname']).' '.$inf_ct['firstname'].'
										</a>
									</td>
									<td align="left" style="padding-left:15px;">
										'.$inf_ct['city'].'
									</td>
								</tr>';
		}
		$retour .= '		</table>
						</td>
					</tr>';
	}
	$retour .= '	<tr>
						<td align="center" style="padding:10px;">
							<input type="button" class="flatbutton" value="'.$_DIMS['cste']['_DIMS_LABEL_FORCE_SAVE'].'" onclick="javascript:validateFormCRM();"/>
							<input type="button" class="flatbutton" value="'.$_DIMS['cste']['_DIMS_LABEL_CANCEL'].'" onclick="javascript:document.location.reload();"/>
						</td>
					</tr>
				</table></div>';
}
else {
	$retour .= '<div class="actions"></div><h2>'.$_DIMS['cste']['_DIMS_LABEL_SIMILAR_CT'].'</h2>';
	$retour .= "<br/><p style=\"font-size:12px;background-color:#fff;font-weight:normal;\"><img src=\"./common/img/publish.png\"/>".$_DIMS['cste']['_DIMS_LABEL_NO_SIMILAR'].'</p>';
	$retour .= '<p style="margin-left:192px;margin-top:10px;margin-bottom:10px;"><input type="button" class="flatbutton" value="'.$_DIMS['cste']['_DIMS_SAVE'].'" onclick="javascript:validateFormCRM();"/></p>
				<p style="margin-left:200px;margin-top:10px;margin-bottom:10px;"><input type="button" class="flatbutton" value="'.$_DIMS['cste']['_DIMS_LABEL_CANCEL'].'" onclick="javascript:document.location.reload();"/></p>';

}

echo $retour;
?>
