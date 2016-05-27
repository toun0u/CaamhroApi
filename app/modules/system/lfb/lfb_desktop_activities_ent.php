<?php
require_once(DIMS_APP_PATH . '/modules/system/class_tag.php');

$resettag = dims_load_securvalue('resettag', dims_const::_DIMS_NUM_INPUT, true, true, false);
$upname = dims_load_securvalue('upname', dims_const::_DIMS_NUM_INPUT, true, true, false);
if (isset($resettag) && $resettag==1) {
	$_SESSION['dims']['tag_filter']=0;
}

if (!isset($_SESSION['dims']['tag_filter'])) $_SESSION['dims']['tag_filter']=0;

$tagfilter = dims_load_securvalue('tagfilter', dims_const::_DIMS_NUM_INPUT, true, true, false,$_SESSION['dims']['tag_filter']);

$params = array();
$sqltagfilter="";
if ($tagfilter>0) {
	// construction de la liste
	$sqltagfilter=" INNER JOIN dims_tag_index as ti on ti.id_workspace= :workspaceid
					and ti.id_tag= :tagfilter and ti.id_record=t.id and ti.id_object= :idobject and ti.id_module=1";
	$params[':workspaceid'] = $_SESSION['dims']['workspaceid'];
	$params[':tagfilter'] = $tagfilter;
	$params[':idobject'] = dims_const::_SYSTEM_OBJECT_TIERS;
	$tab = new tag();
	$tab->open($tagfilter);
}

if ($action=='ent_new') {
	//NOUVELLES FICHES : selection des personnes et de leurs entreprises
	$sql_p = "	SELECT		t.intitule, t.id as id_tiers,t.id, t.ville, t.date_creation,  t.timestp_modify as dates,
							c.id as id_creator, c.lastname as name_creator, c.firstname as pren_creator
			FROM			dims_mod_business_tiers t
			LEFT JOIN		dims_user u
			ON				u.id = t.id_user_create

			INNER JOIN		dims_mod_business_contact c
			ON				c.id = u.id_contact
			".$sqltagfilter."
			WHERE			t.date_creation >= :datesince2
			AND				t.inactif != 1";
	$params[':datesince2'] = $date_since2."000000";

			if(isset($upname) && $upname == 1 ) {
				$sql_p .= " ORDER BY		t.intitule DESC, t.date_creation";
				$opt_tri = -1;
				$opt_trid = -2;
			}
			elseif(isset($upname) && $upname == -1) {
				$sql_p .= " ORDER BY		t.intitule ASC, t.date_creation";
				$opt_tri = 1;
				$opt_trid = -2;
			}
			elseif(isset($upname) && $upname == 2) {
				$sql_p .= " ORDER BY		t.date_creation DESC, t.intitule ";
				$opt_trid = -2;
				$opt_tri = -1;
			}
			elseif(isset($upname) && $upname == -2) {
				$sql_p .= " ORDER BY		t.date_creation ASC, t.intitule ";
				$opt_trid = 2;
				$opt_tri = -1;
			}
			else {
				$sql_p .= " ORDER BY		t.date_creation DESC, t.intitule";
				$opt_tri = -1;
				$opt_trid = -2;
			}
	$res_p = $db->query($sql_p, $params);
	//$nb_resp = $db->numrows($res_p);

}
else {
	if ($action=='ent_modify') {
		//FICHES MODIFIEES : selection des personnes et de leurs entreprises
		$sql_pmod = "	SELECT	t.intitule, t.id as id_tiers,t.id, t.timestp_modify as dates, t.ville,
								u.id as id_creator, u.lastname as name_creator, u.firstname as pren_creator
				FROM			dims_mod_business_tiers t
				LEFT JOIN		dims_user u
				ON				u.id = t.id_user_create

				INNER JOIN		dims_mod_business_contact c
				ON				c.id = u.id_contact
				".$sqltagfilter."
				WHERE			t.timestp_modify >= :datesince2
				AND				t.inactif != 1 ";
		$params[':datesince2'] = $date_since2."000000";

		if(isset($upname) && $upname == 1 ) {
			$sql_pmod .= " ORDER BY		t.intitule DESC, t.timestp_modify";
			$opt_tri = -1;
			$opt_trid = -2;
		}
		elseif(isset($upname) && $upname == -1) {
			$sql_pmod .= " ORDER BY		t.intitule ASC, t.timestp_modify";
			$opt_tri = 1;
			$opt_trid = -2;
		}
		elseif(isset($upname) && $upname == 2) {
			$sql_pmod .= " ORDER BY		t.timestp_modify DESC, t.intitule ";
			$opt_trid = -2;
			$opt_tri = -1;
		}
		elseif(isset($upname) && $upname == -2) {
			$sql_pmod .= " ORDER BY		t.timestp_modify ASC, t.intitule ";
			$opt_trid = 2;
			$opt_tri = -1;
		}
		else {
			$sql_pmod .= " ORDER BY		t.timestp_modify DESC, t.intitule";
			$opt_tri = -1;
			$opt_trid = -2;
		}
		$res_p = $db->query($sql_pmod, $params);
		//$nb_resp = $db->numrows($res_p);
	}
	else {
		//recherche des fiches en veille
		$sql_f = "	SELECT distinct		w.*
					FROM				dims_mod_business_ct_watch w
					INNER JOIN			dims_mod_business_tiers t
					ON					t.id = w.id_tiers
					WHERE				w.id_user = :userid ";
		$params['userid'] = $_SESSION['dims']['userid'];

		$res_f = $db->query($sql_f, $params);
		$tab_veille = array();
		$tab_veille['tiers']['liste']="";

		while($tab_res = $db->fetchrow($res_f)) {
			if($tab_res['id_tiers'] != "") {
				$tab_veille['tiers'][$tab_res['id_tiers']] = $tab_res;
				$tab_veille['tiers']['liste'] .= $tab_res['id_tiers'].", ";
			}
		}
			$trs = substr($tab_veille['tiers']['liste'], 0, -2);
			if($trs == "") $trs = "0";

			//$nb_resp = $db->numrows($res_p);

			//selection des informations concernant les entreprises
			$sql_pmod = "  SELECT			distinct t.intitule, t.id as id_tiers, t.ville, t.date_creation, t.timestp_modify as dates,
										u.id as id_creator, u.lastname as name_creator, u.firstname as pren_creator, u.id_contact as id_ct_user
						FROM			dims_mod_business_tiers t
						LEFT JOIN		dims_user u
						ON				u.id = t.id_user_create
						WHERE			t.id IN ( :trs )
						AND				t.inactif != 1";
			$params[':trs'] = $trs;

		if(isset($upname) && $upname == 1 ) {
			$sql_pmod .= " ORDER BY		t.intitule DESC, t.timestp_modify";
			$opt_tri = -1;
			$opt_trid = -2;
		}
		elseif(isset($upname) && $upname == -1) {
			$sql_pmod .= " ORDER BY		t.intitule ASC, t.timestp_modify";
			$opt_tri = 1;
			$opt_trid = -2;
		}
		elseif(isset($upname) && $upname == 2) {
			$sql_pmod .= " ORDER BY		t.timestp_modify DESC, t.intitule ";
			$opt_trid = -2;
			$opt_tri = -1;
		}
		elseif(isset($upname) && $upname == -2) {
			$sql_pmod .= " ORDER BY		t.timestp_modify ASC, t.intitule ";
			$opt_trid = 2;
			$opt_tri = -1;
		}
		else {
			$sql_pmod .= " ORDER BY		t.timestp_modify DESC, t.intitule";
			$opt_tri = -1;
			$opt_trid = -2;
		}

		$res_p = $db->query($sql_pmod, $params);
		//$nb_resp = $db->numrows($res_p);
	}
}

$class_col = 'trl1';

$old_id = '';
$nb_resp = 0;
while($tab_p = $db->fetchrow($res_p)) {
	if($old_id != $tab_p['id_tiers']) {
		$nb_resp++;
		$old_id = $tab_p['id_tiers'];
		$tab_rep[$tab_p['id_tiers']] = $tab_p;
	}
}

echo $skin->open_simplebloc($title." : ".$nb_resp, '', '', '');

?>

<table cellspacing="0" cellpadding="2" style="width:100%;margin-top:5px;margin-bottom:10px;">
	<tbody>
	<?php
	if($nb_resp > 0) {
		?>
		<tr>
			<td colspan="4" align="center">
			<?php
				if (isset($_SESSION['dims']['tag_filter']) && $_SESSION['dims']['tag_filter']>0) {
					echo $tab->fields['tag']."<a href=\"".$dims->getUrlPath()."?resettag=1&action=".$action."\"><img src=\"./common/img/delete.png\" alt=\"".$_DIMS['cste']['_DELETE']."\">";
				}
			?>
			</td>
		</tr>
		<?php
	?>
		<tr style="font-size:11px;background-color:#ffffff;color:#777777;">
			<td style="width: 35%;"><a href="<? echo $dims->getUrlPath()."?action=".$action."&upname=".$opt_tri; ?>"><?php echo $_DIMS['cste']['_DIMS_LABEL_ENT_NAME']; ?></a></td>

			<!--<td style="width: 20%;"><? //echo _DIMS_LABEL_MOD_CHAMP; ?></td>-->
			<td style="width: 30%;"><a href="<? echo $dims->getUrlPath()."?action=".$action."&upname=".$opt_trid; ?>"><?php echo $_DIMS['cste']['_DIMS_LABEL_MODIF_ON_FEM']; ?></a></td>
			<td style="width: 35%;"><?php echo $_DIMS['cste']['_DIMS_LABEL_FROM']; ?></td>
		</tr>
	<?php
		$old_id = '';
				foreach($tab_rep as $id_pers => $tab_p) {
				/*while($tab_p = $db->fetchrow($res_p)) {
					if($old_id != $tab_p['id_tiers']) {*/
					$tab_date_c = dims_timestamp2local($tab_p['dates']);
						$date_c = $tab_date_c['date'];

						if ($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
						echo '	<tr class="'.$class_col.'">
						<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="cursor: default;" id="tickets_title_3">';
						if($tab_p['timestp_modify'] >= $tab_veille['tiers'][$tab_p['id_tiers']]['time_lastseen']) {
								echo		'<img src="./common/templates/backoffice/dims/img/system/p_red.png"/>';
							}
							else {
								echo		'<img src="./common/templates/backoffice/dims/img/system/p_green.png"/>';
							}
							echo '

										<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat=0&dims_desktop=block&dims_action=public&action='._BUSINESS_TAB_ENT_FORM.'&part=0&id_ent='.$tab_p['id_tiers'].'&id_cont=indefini" onclick="javascript:add_visit(\''.$tab_p['id_tiers'].'\', \'tiers\');" title="Voir la fiche de cette entreprise.">'.$tab_p['intitule'].'</a>
									</td>
									<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="cursor: default;" id="tickets_title_3">
										'.$date_c.'
									</td>
									<td>
										<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat=0&dims_desktop=block&dims_action=public&action='._BUSINESS_TAB_CONTACT_FORM.'&contact_id='.$tab_p['id_ct_user'].'&id_cont=indefini" onclick="javascript:add_visit(\''.$tab_p['id_ct_user'].'\', \'personne\');" title="Voir la fiche de ce contact.">'.$tab_p['pren_creator'].'&nbsp;'.$tab_p['name_creator'].'</a>
									</td>
								</tr>';
					//}
				}


	}
	else {
		echo '<tr><td width="100%">'.$_DIMS['cste']['_DIMS_LABEL_NO_ENTERPRISE'].'</td></tr>';
	}
	?>
   </tbody>
</table>
<?
echo $skin->close_simplebloc();

?>
