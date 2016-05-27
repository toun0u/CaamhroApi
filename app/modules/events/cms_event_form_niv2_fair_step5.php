<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$date_p = '';
$date_f = '';

echo 	'<div>
			<table cellpadding="10">
				<tr>';
if(!empty($etap_selected['date_facturation'])) {
	$tab_date_f = dims_timestamp2local($etap_selected['date_facturation']);
	$date_f = $tab_date_f['date'];

	echo '<td align="right">'.$_DIMS['cste']['_DIMS_LABEL_FAIRS_DATE_FAC'].' : </td>
		  <td align="left">'.$date_f.'</td>';
}
if(!empty($etap_selected['date_paiement'])) {
	$tab_date_p = dims_timestamp2local($etap_selected['date_paiement']);
	$date_p = $tab_date_p['date'];

	echo '<td align="right">'.$_DIMS['cste']['_DIMS_LABEL_FAIRS_DATE_PAIEMENT'].' : </td>
		  <td align="left">'.$date_p.'</td>';

}

if($etap_selected['paiement']) {
	//paiement validé
	echo '<td align="center">'.$_DIMS['cste']['_DIMS_FAIR_VALIDATED_PAIEMENT'].'</td>';
}
else {
	//paiement non validé
	echo '<td align="center">'.$_DIMS['cste']['_DIMS_FAIR_NO_PAIEMENT'].'</td>';
}
echo '		</tr>
		</table>
	</div>';
?>
