<?php
$res_fut='';

//on selectionne tous les events disponibles
$sql_evt2 = "SELECT	distinct a.*
		FROM		dims_mod_business_action a
		WHERE		a.close != 1
		AND			a.niveau = 2
		AND			is_model!=1
		AND 		a.id_parent = 0
		AND 		a.timestp_open <= ".date("YmdHis")."
		AND 		a.datejour >= CURDATE()
		AND 		a.id NOT IN (SELECT id_action FROM dims_mod_business_event_inscription WHERE id_contact = :idcontact )
		ORDER BY	timestp_open DESC";


$res_fut = $db->query($sql_evt2, array(':idcontact' => $_SESSION['dims']['user']['id_contact']) );

$datejour=time();
global $business_mois;
$cpte_event=0;
if($db->numrows($res_fut) > 0) {
	echo '<table cellpadding="5" cellspacing="5" style="border-collapse:collapse;width:100%;">';
	echo ' <tr><td><img src="'.$_SESSION['dims']['front_template_path'].'/gfx/trade_fairs.png" border="0"></td><td> ';

	while($tab_fut = $db->fetchrow($res_fut)) {
		$a = explode('-',$tab_fut['datejour']);
		$dateevt = mktime(0, 0, 0, $a[1], $a[2], $a[0]);

		if($dateevt>$datejour) {
			$date_fut = array();
			preg_match('/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/', $tab_fut['datejour'], $date_fut);
			$cpte_event++;

			echo '<table style="border-collapse:collapse;"><tr style="border-bottom:1px solid #ACACAC;"><td>
						<table
							<tr><td class="title">'.$tab_fut['libelle'].'</td></tr>
							<tr><td class="date"><span class="red">17</span> <span class="mois">November,</span> <span class="red">2010</span></td></tr>
						</table>
					</td>
					<td class="text">'.$tab_fut['description'];

		   if($tab_fut['close'] == 1) {
			   echo $_DIMS['cste']['_DIMS_LABEL_REGISTRATION_REFUSED'];
		   }
		   else {
			  echo '<a class="lien" style="margin-left:30px;" href="/index.php?op=fairs&action=sub_eventinscription&id_event='.$tab_fut['id'].'&id_contact='.$_SESSION['dims']['user']['id_contact'].'">'.$_DIMS['cste']['_DIMS_LABEL_REGISTER'].'</a>';

		   }
		   echo '</td></tr></table>';
		}
	}
	echo "</td></tr></table>";
}

if ($cpte_event==0) {
	echo "<p style='margin-top:20px;text-align:center;'>".$_DIMS['cste']['_DIMS_LABEL_NO_EVENT'].'</p>';

	echo "<p style='margin-top:20px;text-align:center;'><a href=\"/index.php\" class=\"lien\">".$_DIMS['cste']['_DIMS_BACK'].'</a></p>';

}
?>
