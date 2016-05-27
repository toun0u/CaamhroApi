<?php
require_once(DIMS_APP_PATH . '/modules/system/class_tag.php');


$resettag = dims_load_securvalue('resettag', dims_const::_DIMS_NUM_INPUT, true, true, false);
$upname = dims_load_securvalue('upname', dims_const::_DIMS_NUM_INPUT, true, true, false);

if (isset($resettag) && $resettag==1) {
	$_SESSION['dims']['tag_filter']=0;
}

if (!isset($_SESSION['dims']['tag_filter'])) $_SESSION['dims']['tag_filter']=0;

$params = array();
$tagfilter = dims_load_securvalue('tagfilter', dims_const::_DIMS_NUM_INPUT, true, true, false,$_SESSION['dims']['tag_filter']);
$sqltagfilter="";
if ($tagfilter>0) {
	// construction de la liste
	$sqltagfilter=" INNER JOIN	dims_tag_index AS ti
					ON			ti.id_workspace = :idworkspace
					AND			ti.id_tag = :tagfilter
					AND			ti.id_record = c.id
					AND			ti.id_object = :idobject
					AND			ti.id_module = 1";
	$params[':idworkspace'] = $_SESSION['dims']['workspaceid'];
	$params[':idtag'] = $tagfilter;
	$params[':idobject'] = dims_const::_SYSTEM_OBJECT_CONTACT;

	$tab = new tag();
	$tab->open($tagfilter);
}

if ($action=='contact_new') {
	//NOUVELLES FICHES : selection des personnes et de leurs entreprises
	$sql_p = "	SELECT			distinct c.firstname, c.lastname, c.id as id_pers, c.date_create as dates,
								t.intitule, t.id as id_tiers,
								u.id as id_creator, u.lastname as name_creator, u.firstname as pren_creator
				FROM			dims_mod_business_contact c
				LEFT JOIN		dims_mod_business_tiers_contact tc
				ON				tc.id_contact = c.id
				LEFT JOIN		dims_mod_business_tiers t
				ON				t.id = tc.id_tiers
				INNER JOIN		dims_mod_business_contact u
				ON				u.id = c.id_user_create
				".$sqltagfilter."
				WHERE			c.date_create >= :datesince2
				AND				c.inactif != 1";
	$params[':datesince2'] = $date_since2."000000";

	if(isset($upname) && $upname == 1 ) {
		$sql_p .= " ORDER BY		c.lastname DESC, c.firstname, c.date_create";
		$opt_tri = -1;
		$opt_trid = -2;
	}
	elseif(isset($upname) && $upname == -1) {
		$sql_p .= " ORDER BY		c.lastname ASC, c.firstname, c.date_create";
		$opt_tri = 1;
		$opt_trid = -2;
	}
	elseif(isset($upname) && $upname == 2) {
		$sql_p .= " ORDER BY		c.date_create DESC, c.lastname, c.firstname ";
		$opt_trid = -2;
		$opt_tri = -1;
	}
	elseif(isset($upname) && $upname == -2) {
		$sql_p .= " ORDER BY		c.date_create ASC, c.lastname, c.firstname ";
		$opt_trid = 2;
		$opt_tri = -1;
	}
	else {
		$sql_p .= " ORDER BY		c.date_create DESC, c.lastname, c.firstname";
		$opt_tri = -1;
		$opt_trid = -2;
	}
//echo $sql_p; die();
	$res_p = $db->query($sql_p, $params);
	//S$nb_resp = $db->numrows($res_p);

}
else {
	if ($action=='contact_modify') {
		//FICHES MODIFIEES : selection des personnes et de leurs entreprises
		$sql_pmod = "	SELECT			distinct c.firstname, c.lastname, c.id as id_pers, c.timestp_modify as dates,
										t.intitule, t.id as id_tiers,
										u.id as id_creator, u.lastname as name_creator, u.firstname as pren_creator
						FROM			dims_mod_business_contact c
						LEFT JOIN		dims_mod_business_tiers_contact tc
						ON				tc.id_contact = c.id
						LEFT JOIN		dims_mod_business_tiers t
						ON				t.id = tc.id_tiers
						LEFT JOIN		dims_mod_business_contact u
						ON				u.id = c.id_user_create
						".$sqltagfilter."
						WHERE			c.timestp_modify >= :datesince2

						AND				c.inactif != 1";
	$params[':datesince2'] = $date_since2."000000";

		if(isset($upname) && $upname == 1 ) {
			$sql_pmod .= " ORDER BY		c.lastname DESC, c.firstname, c.timestp_modify";
			$opt_tri = -1;
			$opt_trid = -2;
		}
		elseif(isset($upname) && $upname == -1) {
			$sql_pmod .= " ORDER BY		c.lastname ASC, c.firstname, c.timestp_modify";
			$opt_tri = 1;
			$opt_trid = -2;
		}
		elseif(isset($upname) && $upname == 2) {
			$sql_pmod .= " ORDER BY		c.timestp_modify DESC, c.lastname, c.firstname ";
			$opt_trid = -2;
			$opt_tri = -1;
		}
		elseif(isset($upname) && $upname == -2) {
			$sql_pmod .= " ORDER BY		c.timestp_modify ASC, c.lastname, c.firstname ";
			$opt_trid = 2;
			$opt_tri = -1;
		}
		else {
			$sql_pmod .= " ORDER BY		c.timestp_modify DESC, c.lastname, c.firstname";
			$opt_tri = -1;
			$opt_trid = -2;
		}
		$res_p = $db->query($sql_pmod);
		//$nb_resp = $db->numrows($res_p);
	}
	else {
		//en veille
		$sql_f = "	SELECT	w.*
					FROM	dims_mod_business_ct_watch w
					INNER	JOIN dims_mod_business_contact c
					ON		c.id = w.id_personne
					WHERE	w.id_user = :userid ";
		$params[':userid'] = $_SESSION['dims']['userid'];


		$res_f = $db->query($sql_f, $params);
		$tab_veille = array();
		$tab_veille['personne']['liste']="";
		$tab_veille['tiers']['liste']="";

		while($tab_res = $db->fetchrow($res_f)) {
			if($tab_res['id_personne'] != "") {
				$tab_veille['personne'][$tab_res['id_personne']] = $tab_res;
				//on construit une liste des personnes pour l'ins�rer dans la requete
				$tab_veille['personne']['liste'] .= $tab_res['id_personne'].", ";
			}

		}

		$per = substr($tab_veille['personne']['liste'], 0, -2);
		if($per == "") $per = "0";

		//selection des informations concernant les personnes
		$sql_p = "	SELECT			c.firstname, c.lastname, c.id as id_pers,  c.timestp_modify as dates,
									t.intitule, t.id as id_tiers,
									u.id as id_creator, u.lastname as name_creator, u.firstname as pren_creator
					FROM			dims_mod_business_contact c
					LEFT JOIN		dims_mod_business_tiers_contact tc
					ON				tc.id_contact = c.id
					AND				(
										tc.type_lien LIKE 'emploi'
									OR	tc.type_lien LIKE 'employeur'
									)
					AND				tc.date_fin = 0
					LEFT JOIN		dims_mod_business_tiers t
					ON				t.id = tc.id_tiers
					LEFT JOIN		dims_mod_business_contact u
					ON				u.id = c.id_user_create
					WHERE			c.id IN ( :per )
					AND				c.inactif != 1
					";
		$params[':per'] = $per;
		if(isset($upname) && $upname == 1 ) {
			$sql_p .= " ORDER BY		c.lastname DESC, c.firstname, c.timestp_modify";
			$opt_tri = -1;
			$opt_trid = -2;
		}
		elseif(isset($upname) && $upname == -1) {
			$sql_p .= " ORDER BY		c.lastname ASC, c.firstname, c.timestp_modify";
			$opt_tri = 1;
			$opt_trid = -2;
		}
		elseif(isset($upname) && $upname == 2) {
			$sql_pmod .= " ORDER BY		c.timestp_modify DESC, c.lastname, c.firstname ";
			$opt_trid = -2;
			$opt_tri = -1;
		}
		elseif(isset($upname) && $upname == -2) {
			$sql_pmod .= " ORDER BY		c.timestp_modify ASC, c.lastname, c.firstname ";
			$opt_trid = 2;
			$opt_tri = -1;
		}
		else {
			$sql_p .= " ORDER BY		c.timestp_modify DESC, c.lastname, c.firstname";
			$opt_tri = -1;
			$opt_trid = -2;
		}

		$res_p = $db->query($sql_p, $params);
		//$nb_resp = $db->numrows($res_p);
	}
}

$old_id = '';
$nb_resp = 0;
while($tab_p = $db->fetchrow($res_p)) {
	if($old_id != $tab_p['id_pers']) {
		$nb_resp++;
		$old_id = $tab_p['id_pers'];
		$tab_rep[$tab_p['id_pers']] = $tab_p;
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
		<tr style="font-size:12px;background-color:#ffffff;color:#777777;">
			<td style="width: 25%;"><a href="<? echo $dims->getUrlPath()."?action=".$action."&upname=".$opt_tri; ?>"><?php echo $_DIMS['cste']['_DIMS_LABEL_CONTACT']; ?></a></td>
			<td style="width: 30%;"><?php echo $_DIMS['cste']['_DIMS_LABEL_CONT_ENTRAT']; ?></td>
			<td style="width: 20%;"><a href="<? echo $dims->getUrlPath()."?action=".$action."&upname=".$opt_trid; ?>"><?php
			if ($action=='contact_new') echo $_DIMS['cste']['_DIMS_LABEL_CREATE_ON'];
			else echo $_DIMS['cste']['_DIMS_LABEL_MODIF_ON_FEM'];
			?></a></td>

			<td style="width: 25%;"><?php echo $_DIMS['cste']['_DIMS_LABEL_FROM']; ?></td>
		</tr>
	<?php
		$old_id = '';
		$i = 0;

		$class_col = 'trl1';
		/*while($tab_p = $db->fetchrow($res_p)) {
			if($old_id != $tab_p['id_pers']) {*/
		foreach($tab_rep as $id_pers => $tab_p) {

				$date_c = dims_timestamp2local($tab_p['dates']);

				if ($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
				echo '	<tr class="'.$class_col.'">
				<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="cursor: default;" id="tickets_title_3">';
				if($tab_p['timestp_modify'] >= $tab_veille['personne'][$tab_p['id_pers']]['time_lastseen']) {
					echo		'<img src="./common/templates/backoffice/dims/img/system/p_red.png"/>';
				}
				else {
					echo		'<img src="./common/templates/backoffice/dims/img/system/p_green.png"/>';
				}
				echo '
								<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat=0&dims_desktop=block&dims_action=public&action='._BUSINESS_TAB_CONTACT_FORM.'&contact_id='.$tab_p['id_pers'].'" title="Voir la fiche de ce contact.">'.$tab_p['firstname'].'&nbsp;'.$tab_p['lastname'].'</a>
							</td>
							<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="cursor: default;" id="tickets_title_3">
								<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat=0&dims_desktop=block&dims_action=public&action='._BUSINESS_TAB_ENT_FORM.'&part=0&id_ent='.$tab_p['id_tiers'].'" title="Voir la fiche de cette entreprise.">'.$tab_p['intitule'].'</a>
							</td>
							<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="cursor: default;" id="tickets_title_3">
								'.$date_c['date'].'
							</td>
							<td>
								<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat=0&dims_desktop=block&dims_action=public&action='._BUSINESS_TAB_CONTACT_FORM.'&contact_id='.$tab_p['id_creator'].'&id_cont=indefini" title="Voir la fiche de ce contact.">'.$tab_p['pren_creator'].'&nbsp;'.$tab_p['name_creator'].'</a>
							</td>
						</tr>';
				$old_id = $tab_p['id_pers'];
			//}
		}

		?>
		<tr>
			<td colspan="4" align="right">
								<?
				if ($action=='contact_new') {
					echo '<a href="'.$dims->getScriptEnv().'?admin.php&dims_action=public&dims_mainmenu=0&submenu='.dims_const::_DIMS_SUBMENU_CONTACT.'&action=see_ct&type=new">';
				}
				else {
					echo '<a href="'.$dims->getScriptEnv().'?admin.php&dims_action=public&dims_mainmenu=0&submenu='.dims_const::_DIMS_SUBMENU_CONTACT.'&action=see_ct&type=mod">';
				}
				?>

					<img src="./common/img/view.png" alt="view"><span>&nbsp;<?php echo $_DIMS['cste']['_DIMS_LABEL_SEE_ALL_SHEET']; ?></span>
				</a>
			</td>
		</tr>
		<?php
	}
	else {
		echo '<tr><td width="100%">'.$_DIMS['cste']['_DIMS_LABEL_NO_RESP'].'</td></tr>';
	}
	?>
   </tbody>
</table>
<?
echo $skin->close_simplebloc();
?>
