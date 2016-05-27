<?php
$retour = "";
$tab_vall = array();
$raisoc = dims_load_securvalue('raisoc', dims_const::_DIMS_CHAR_INPUT, true);

$raisoc = strtolower(trim($raisoc));

//on test si on a une correspondance exacte au niveau du nom de l'ent
$sql_verif = "SELECT * FROM dims_mod_business_tiers WHERE intitule LIKE '".$raisoc."'";
$res_v = $db->query($sql_verif);

if($db->numrows($res_v) > 0) {
	//on met les donnees dans un tableau en vue de leur comparaison
	if(!isset($tab_vall['exact_all'])) $tab_vall['exact_all'] = array();

	while($tab_ct = $db->fetchrow($res_v)) {
		$tab_vall['exact_all'][$tab_ct['id']] = $tab_ct;
	}
}

//on va utiliser levenshtein et comparer avec l'ensemble des tiers presents dans la base
$sql_all = "SELECT * FROM dims_mod_business_tiers WHERE intitule NOT LIKE '".$raisoc."'";
$res_all = $db->query($sql_all);

$tab_simname = array();
while($tab_ct = $db->fetchrow($res_all)) {
	//on met les champs au bon format
	$tab_ct['intitule'] = strtolower(trim($tab_ct['intitule']));
	//on calcul la distance par rapport au nom de la recherche
	$lev_n = levenshtein($raisoc, $tab_ct['intitule']);
	//on calcul un coefficient d'acceptabilite de la similarite puis on compare avec le levenshtein
	$coeff_sim = '';
	$mod_coeff_sim = '';
	$coeff_sim = ceil(strlen($tab_ct['intitule'])/4);
	$coeff_sim2 = ceil(strlen($tab_ct['intitule'])/2);
	if($lev_n <= $coeff_sim) {
		if(!isset($tab_vall['prox_np'])) $tab_vall['prox_np'] = array();
		$tab_vall['prox_np'][$tab_ct['id']] = $tab_ct;
	}
	elseif($lev_n <= $coeff_sim2) {
		if(!isset($tab_vall['less_prox'])) $tab_vall['less_prox'] = array();
		$tab_vall['less_prox'][$tab_ct['id']] = $tab_ct;
	}
}

if(count($tab_vall) > 0) {
	//affichage des resultats
	//3 cas possibles :
	//intitule exact : $tab_vall['exact_all']
	//intitule proche (petit coeff acceptabilite) : $tab_vall['prox_np']
	//intitule eloigne (grand coeff acceptabilite) : $tab_vall['less_prox']
	$retour .= '<div class="actions"></div><h2>'.$_DIMS['cste']['_DIMS_LABEL_SIMILAR_TIERS'].'</h2>';
	$retour .= $skin->open_simplebloc('','font-weight:bold;width:100%','','');
	$retour .= '<div style="overflow-x:auto;height:400px;margin-right:1px;">';
	$retour .= '<table width="100%" cellpadding="0" cellspacing="0">';
	if(!empty($tab_vall['exact_all'])) {
		$class_col = 'trl1';
		$nb_exact = count($tab_vall['exact_all']);
		$retour .= '<tr style="padding-bottom:10px;">
						<td style="padding:15px;">
							<table width="100%" cellpadding="0" cellspacing="0" style="border:#58698B 1px solid">
								<tr class="trl1">
									<td align="center" colspan="2">';
		if($nb_exact > 1)	$retour .= $_DIMS['cste']['_DIMS_LABEL_EXACT_CORRESP_PLUR'].' ('.$nb_exact.')';
		else				$retour .= $_DIMS['cste']['_DIMS_LABEL_EXACT_CORRESP_SING'].' ('.$nb_exact.')';
		$retour .=					'</td>
								</tr>';
		foreach($tab_vall['exact_all'] as $id_ct => $inf_ct) {
			if ($class_col == 'trl1') $class_col = 'trl2'; else $class_col = 'trl1';
			$retour .= '		<tr class="'.$class_col.'">
									<td align="left" style="padding-left:15px;">
										<a style="cursor:pointer;" onclick="javascript:displayInfoEntCrm('.$id_ct.');" target="_blank" title="'.$_DIMS['cste']['_DIMS_LABEL_AFFICH_INF_CT'].'">
											'.strtoupper($inf_ct['intitule']).'
										</a>
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
									<td align="center" colspan="2">';
		if($nb_exact > 1)	$retour .= $_DIMS['cste']['_DIMS_LABEL_PROX_CORRESP_PLUR'].' ('.$nb_exact.')';
		else				$retour .= $_DIMS['cste']['_DIMS_LABEL_PROX_CORRESP_SING'].' ('.$nb_exact.')';
		$retour .=					'</td>
								</tr>';
		foreach($tab_vall['prox_np'] as $id_ct => $inf_ct) {
			if ($class_col == 'trl1') $class_col = 'trl2'; else $class_col = 'trl1';
			$retour .= '		<tr class="'.$class_col.'">
									<td align="left" style="padding-left:15px;">
										<a style="cursor:pointer;" onclick="javascript:displayInfoEntCrm('.$id_ct.');" target="_blank" title="'.$_DIMS['cste']['_DIMS_LABEL_AFFICH_INF_CT'].'">
											'.strtoupper($inf_ct['intitule']).'
										</a>
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
										<a style="cursor:pointer;" onclick="javascript:displayInfoEntCrm('.$id_ct.');" target="_blank" title="'.$_DIMS['cste']['_DIMS_LABEL_AFFICH_INF_CT'].'">
											'.strtoupper($inf_ct['intitule']).'
										</a>
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
	$retour .= '	<tr>
						<td align="center" style="padding:10px;">
							<input type="button" class="flatbutton" value="'.$_DIMS['cste']['_DIMS_LABEL_FORCE_SAVE'].'" onclick="javascript:document.getElementById(\'form_ent\').submit();"/>
							<input type="button" class="flatbutton" value="'.$_DIMS['cste']['_DIMS_LABEL_CANCEL'].'" onclick="javascript:document.location.reload();"/>
						</td>
					</tr>
				</table></div>';
	$retour .= $skin->close_simplebloc();
}
else {
	$retour .= $skin->open_widgetbloc($_DIMS['cste']['_DIMS_LABEL_SIMILAR_TIERS'],'font-weight:bold;width:100%','','');
	$retour .= "<br/><p style=\"font-size:12px;background-color:#fff;font-weight:normal;\"><img src=\"./common/img/publish.png\"/>".$_DIMS['cste']['_DIMS_LABEL_NO_SIMILAR'].'</p>';
	$retour .= '<p style="margin-left:200px;margin-top:10px;margin-bottom:10px;"><input type="button" class="flatbutton" value="'.$_DIMS['cste']['_DIMS_SAVE'].'" onclick="javascript:document.form_ent.submit();"/></p>
				<p style="margin-left:200px;margin-top:10px;margin-bottom:10px;"><input type="button" class="flatbutton" value="'.$_DIMS['cste']['_DIMS_LABEL_CANCEL'].'" onclick="javascript:document.location.reload();"/></p>';
	$retour .= $skin->close_widgetbloc();
}

echo $retour;
?>
