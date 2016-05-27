<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_REGISTRATION_S']);

$id_evt = dims_load_securvalue('id_evt', dims_const::_DIMS_NUM_INPUT, true,true);
$ftype_insc = dims_load_securvalue('type_insc', dims_const::_DIMS_CHAR_INPUT, true, true);

//Verification qu'il y a bien un id_evt
if($id_evt != null AND !empty($id_evt)) {
	$sql    = null;
	$tab_evt= array();
	$tab_ins= array();
	$option = '';
	switch($ftype_insc) {
		case 'wait':
			$option = ' AND ei.validate = 0 ';
			$s1 = 'selected="selected"';
			$s2 = '';
			$s3 = '';
			$s4 = '';
			break;
		case 'step1':
			$option = ' AND ei.validate = 1 ';
			$s1 = '';
			$s2 = 'selected="selected"';
			$s3 = '';
			$s4 = '';
			break;
		case 'valid':
			$option = ' AND ei.validate = 2 ';
			$s1 = '';
			$s2 = '';
			$s3 = 'selected="selected"';
			$s4 = '';
			break;
		case 'cancelled':
			$option = ' AND ei.validate = -1 ';
			$s1 = '';
			$s2 = '';
			$s3 = '';
			$s4 = 'selected="selected"';
			break;
		default:
			$option='';
			$s1 = '';
			$s2 = '';
			$s3 = '';
			$s4 = '';
			break;
	}

	//Recherche de l'evt + infos insc liées (verification que l'evt appartient bien a l'user)
	$sql = 'SELECT
				a.id AS id_evt,
				a.typeaction,
				a.libelle,
				a.description,
				a.datejour,
				a.heuredeb,
				a.heurefin,
				a.timestp_modify,
				a.timestamp_release,
				a.supportrelease,
				a.rub_nl,
				a.allow_fo,
				a.target,
				a.teaser,
				a.lieu,
				a.prix,
				a.conditions,
				a.close,
				a.niveau,
				a.alert_modif,
				ei.id AS id_insc,
				ei.id_contact,
				ei.validate,
				ei.lastname,
				ei.firstname,
				ei.address,
				ei.city,
				ei.postalcode,
				ei.country,
				ei.phone,
				ei.email,
				ei.company,
				ei.function
			FROM
				dims_mod_business_action a
			INNER JOIN
				dims_user u
				ON
					u.id = a.id_user
			LEFT JOIN
				dims_mod_business_event_inscription ei
				ON
					ei.id_action = a.id
				'.$option.'
			WHERE
				a.id = :idevt
				ORDER BY ei.validate DESC';


	$ressource  = $db->query($sql, array(
		':idevt' => $id_evt
	));
	if(!isset($_SESSION['business']['event_export_insc'])) $_SESSION['business']['event_export_insc'] = '';
	$_SESSION['business']['event_export_insc'] = $sql;
	$_SESSION['business']['exportdata'] = array();
	//Si on a un evt [+ infos users]
	if($db->numrows($ressource) > 0) {
		//utilise pour condition de l'affiche (Comprend id_evt bon et evt existant)
		$nb_evt = 1;
		$nb_niv2 = 0;
		while($info = $db->fetchrow($ressource)) {
			//Construction du tableau récpitulatif de l'evt
			$tab_evt['id_evt']              = $info['id_evt'];
			$tab_evt['libelle']             = $info['libelle'];
			$tab_evt['typeaction']          = $info['typeaction'];
			$tab_evt['description']         = $info['description'];
			$tab_evt['datejour']            = $info['datejour'];
			$tab_evt['heuredeb']            = $info['heuredeb'];
			$tab_evt['heurefin']            = $info['heurefin'];
			$tab_evt['timestp_modify']      = $info['timestp_modify'];
			$tab_evt['timestamp_release']   = $info['timestamp_release'];
			$tab_evt['supportrelease']      = $info['supportrelease'];
			$tab_evt['rub_nl']              = $info['rub_nl'];
			$tab_evt['allow_fo']            = $info['allow_fo'];
			$tab_evt['target']              = $info['target'];
			$tab_evt['teaser']              = $info['teaser'];
			$tab_evt['lieu']                = $info['lieu'];
			$tab_evt['prix']                = $info['prix'];
			$tab_evt['conditions']          = $info['conditions'];
			$tab_evt['niveau']              = $info['niveau'];
			$tab_evt['close']				= $info['close'];
			$tab_evt['alert_modif']			= $info['alert_modif'];
			if($info['niveau'] == 2) $nb_niv2++;
			//Si on a une inscription (ou plus) sur l'evt
			if(isset($info['id_insc']) && !empty($info['id_insc'])) {
				//Construction du tableau des inscriptions (Id_ins en clé premier niveau)
				$tab_ins[$info['id_insc']]['id_insc']   = $info['id_insc'];
				$tab_ins[$info['id_insc']]['id_contact']= $info['id_contact'];
				$tab_ins[$info['id_insc']]['validate']  = $info['validate'];
				$tab_ins[$info['id_insc']]['lastname']  = $info['lastname'];
				$tab_ins[$info['id_insc']]['firstname'] = $info['firstname'];
				$tab_ins[$info['id_insc']]['address']   = $info['address'];
				$tab_ins[$info['id_insc']]['city']      = $info['city'];
				$tab_ins[$info['id_insc']]['postalcode']= $info['postalcode'];
				$tab_ins[$info['id_insc']]['country']   = $info['country'];
				$tab_ins[$info['id_insc']]['phone']     = $info['phone'];
				$tab_ins[$info['id_insc']]['email']     = $info['email'];
				$tab_ins[$info['id_insc']]['company']   = $info['company'];
				$tab_ins[$info['id_insc']]['function']  = $info['function'];

			}
		}
		$_SESSION['business']['exportdata'] = $tab_ins;
	}
}
echo '<table style="width: 100%; border-collapse: collapse; text-align: center;">';
if(count($tab_ins) > 0) {
	echo   '<tr class="trl1">
				<th>'.$_DIMS['cste']['_DIMS_LABEL_NAME'].'</th>
				<th>'.$_DIMS['cste']['_FIRSTNAME'].'</th>
				<th>'.$_DIMS['cste']['_PHONE'].'</th>
				<th>'.$_DIMS['cste']['_DIMS_LABEL_EMAIL'].'</th>
				<th>'.$_DIMS['cste']['_INFOS_STATE'].'</th>';
	echo '  </tr>';
}

	$class = 'trl2';

	//Verif inscription existante sur l'evt
	if(count($tab_ins) == 0)
		echo '<tr><td colspan="11" style="padding-top:25px;font-size:13px;">'.$_DIMS['cste']['_DIMS_LABEL_NO_REGISTRATION'].'</td></tr>';
	else {

		foreach($tab_ins as $inscrit) {
			$onclick = '';
			if($event_etap_ok == 1) $onclick = 'onclick="location.href=\'admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_HOME.'&submenu='.dims_const::_DIMS_SUBMENU_EVENT.'&action=adm_insc&id_evt='.$tab_evt['id_evt'] .'&id_insc='.$inscrit['id_insc'].'\'"';
			echo '<tr class="'.$class.'" '.$onclick.'>';
				echo '<td>';
					echo (!empty($inscrit['lastname'])) ? $inscrit['lastname'] : 'n/a';
				echo '</td>';
				echo '<td>';
					echo (!empty($inscrit['firstname'])) ? $inscrit['firstname'] : 'n/a';
				echo '</td>';

				echo '<td>';
					echo (!empty($inscrit['phone'])) ? $inscrit['phone'] : 'n/a';
				echo '</td>';

				echo '<td>';
					echo (!empty($inscrit['email'])) ? $inscrit['email'] : 'n/a';
				echo '</td>';
				echo '<td>';
					if($inscrit['validate'] == -1)
					{
						//Invalide
						echo '<img alt="'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_CANCELED'].'" src="./common/modules/system/img/ico_point_red.gif" />';
					}
					elseif($inscrit['validate'] == 2)
					{
						//Valide totalement
						echo '<img alt="'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_VALIDATED'].'" src="./common/modules/system/img/ico_point_green.gif" />';
					}
					else
					{
						if($form_niv == 1)
						{
							//en attente de validation
							echo '<img alt="'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_WAIT'].'" src="./common/modules/system/img/ico_point_grey.gif" />';
						}
						elseif($form_niv == 2)
						{
							if($inscrit['validate'] == 0)
							{
								//en attente niv1 (et niv2)
								echo '<img alt="'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_WAIT'].'" src="./common/modules/system/img/ico_point_grey.gif" />';
							}
							elseif($inscrit['validate'] == 1)
							{
								//niv1 valide, en attente niv2
								echo '<img alt="'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_WAIT_FOR_2'].'" src="./common/modules/system/img/ico_point_orange.gif" />';
							}
						}
					}
				echo '</td>';
				echo '<td>';
					if(!empty($inscrit['id_contact']))
					{
						echo '<img border="0" src="./common/img/user.png" alt="Fiche contact"/>';
					}
				echo '</td>';
			echo '</tr>';
			$class = ($class == 'trl2') ? 'trl1' : 'trl2';
		}
	}
echo '</table>';
echo '<div>
	<p>
		'.$_DIMS['cste']['_DIMS_LEGEND'].' :
	</p>
	<p>
		<ul style="list-style:none inside none;">
			<li>
				<img alt="'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_WAIT'].'" src="./common/modules/system/img/ico_point_grey.gif" />
				'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_WAIT'].'
			</li>';


echo 		'<li>
				<img alt="'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_VALIDATED'].'" src="./common/modules/system/img/ico_point_green.gif" />
				'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_VALIDATED'].'
			</li>
			<li>
				<img alt="'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_CANCELED'].'" src="./common/modules/system/img/ico_point_red.gif" />
				'.$_DIMS['cste']['_DIMS_LABEL_REGISTRATION_CANCELED'].'
			</li>
		</ul>
	</p>
</div>';

echo $skin->close_simplebloc();
?>
