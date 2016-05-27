<?php
/* --------------------- Gestion des dernières commandes ----*/
function store_lastcommande($id, $nb_elems){
	$last_commandes = &get_sessparam($_SESSION['cata']['commandes']['last_commandes'], array() );
	if(in_array($id,$last_commandes)){
		unset($last_commandes[array_search($id, $last_commandes)]);
	}elseif(count($last_commandes) >= $nb_elems){
		array_splice($last_commandes,$nb_elems-1);
	}
	array_unshift($last_commandes,$id);
}

function get_lastcommandes(){
	$last = &get_sessparam($_SESSION['cata']['commandes']['last_commandes'], array() );;
	return $last;

}

function export_csv_commandes($lst){
	header("Cache-control: private");
	header("Content-type: application/csv");
	header("Content-Disposition: inline; filename=commandes.csv");
	header("Pragma: public");

	ob_clean();
	ob_start();

	echo "\"".dims_constant::getVal('_STATE')."\",\"HC\",\"".dims_constant::getVal('_PAYMENT_MEAN')."\",\"".dims_constant::getVal('_NUMBER')."\",\"".dims_constant::getVal('CLIENT')."\",\"".dims_constant::getVal('_DIMS_DATE')."\",\"".dims_constant::getVal('_DUTY_FREE_AMOUNT')." (€)\"\n";

	$states = commande::getStates();
	foreach($lst as $cde){
		$cli = $cde->getClient();
		$d = dims_timestamp2local($cde->fields['date_cree']);
		echo "\"".(isset($states[$cde->fields['etat']])?$states[$cde->fields['etat']]:"")."\",\"".(($cde->fields['hors_cata'])?dims_constant::getVal('_DIMS_YES'):dims_constant::getVal('_DIMS_NO'))."\",\"".moyen_paiement::getTypeLabel($cde->fields['mode_paiement'])."\",\""."BC".$cde->get('id')."\",\"".$cli->fields['nom']."\",\"".$d['date']."\",\"".$cde->fields['total_ht']."\"\n";
	}

	ob_end_flush();
	die();
}
?>
