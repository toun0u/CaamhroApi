<?php
$id_usr = dims_load_securvalue('id_usr',dims_const::_DIMS_NUM_INPUT,true,true);

$params_u = array();
$params_p = array();

if($type == 'new') {

	$sql_u = "	SELECT			distinct u.id as id_creator, u.lastname as name_creator, u.firstname as pren_creator
				FROM			dims_mod_business_contact c
				LEFT JOIN		dims_mod_business_tiers_contact tc
				ON				tc.id_contact = c.id
				LEFT JOIN		dims_mod_business_tiers t
				ON				t.id = tc.id_tiers
				LEFT JOIN		dims_mod_business_contact u
				ON				u.id = c.id_user_create
				WHERE			c.date_create >= :datesince2
				AND				c.inactif != 1
				ORDER BY		c.date_create DESC, c.lastname, c.firstname";
	$params_u[':datesince2'] = $date_since2."000000";

	$sql_p = "	SELECT			c.firstname, c.lastname, c.id as id_pers, c.date_create AS date,
								t.intitule, t.id as id_tiers,
								u.id as id_creator, u.lastname as name_creator, u.firstname as pren_creator
				FROM			dims_mod_business_contact c
				LEFT JOIN		dims_mod_business_tiers_contact tc
				ON				tc.id_contact = c.id
				LEFT JOIN		dims_mod_business_tiers t
				ON				t.id = tc.id_tiers
				LEFT JOIN		dims_mod_business_contact u
				ON				u.id = c.id_user_create
				WHERE			c.date_create >= :datesince2 ";
	$params_p[':datesince2'] = $date_since2."000000";
	if ($id_usr>0) {
		$sql_p .= " and u.id= :idusr ";
		$params_p[':idusr'] = $id_usr;
	}

	$sql_p .= "
				AND				c.inactif != 1
				ORDER BY		c.date_create DESC, c.lastname, c.firstname";
}
elseif($type ==  'mod') {

$sql_u = "	SELECT			distinct u.id as id_creator, u.lastname as name_creator, u.firstname as pren_creator
			FROM			dims_mod_business_contact c
			LEFT JOIN		dims_mod_business_tiers_contact tc
			ON				tc.id_contact = c.id
			LEFT JOIN		dims_mod_business_tiers t
			ON				t.id = tc.id_tiers
			LEFT JOIN		dims_mod_business_contact u
			ON				u.id = c.id_user_create
			WHERE			c.timestp_modify >= :datesince2
			AND				c.inactif != 1
			ORDER BY		c.timestp_modify DESC, c.lastname, c.firstname";
$params_u[':datesince2'] = $date_since2."000000";

$sql_p = "	SELECT			c.firstname, c.lastname, c.id as id_pers, c.timestp_modify AS date,
							t.intitule, t.id as id_tiers,
							u.id as id_creator, u.lastname as name_creator, u.firstname as pren_creator
			FROM			dims_mod_business_contact c
			LEFT JOIN		dims_mod_business_tiers_contact tc
			ON				tc.id_contact = c.id
			LEFT JOIN		dims_mod_business_tiers t
			ON				t.id = tc.id_tiers
			LEFT JOIN		dims_mod_business_contact u
			ON				u.id = c.id_user_create
			WHERE			c.timestp_modify >= :datesince2 ";
$params_p[':datesince2'] = $date_since2."000000";

	if ($id_usr>0) {
		$sql_p .= " and u.id= :idusr ";
		$params_p[':idusr'] = $id_usr;
	}

	$sql_p .= "
			AND				c.inactif != 1
			ORDER BY		c.timestp_modify DESC, c.lastname, c.firstname";
}

$res_p = $db->query($sql_p, $params_p);
$nb_resp = $db->numrows($res_p);

$class_col = 'trl1';

?>
<form name="filter" action="<? echo $dims->getScriptEnv()."?action=see_ct&type=".$type; ?>" method="post">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("id_usr");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<div id="vertical_container">
	<h3 class="accordion_toggle">
		<table style="width:100%;">
			<tr>
				<td align="left" width="15%">&nbsp;</td>
				<td align="left" width="70%">
					<table style="width:100%;" cellpadding="0" cellspacing="0">
						<tr>
							<td class="bgb20"><img src="<?php echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
							<td class="midb20">
							<?php
							if($type == 'new') {
								echo $_DIMS['cste']['_DIMS_LABEL_NEW_SHEET_SINCE']." ".$date_since;
							}
							elseif($type == 'mod') {
								echo $_DIMS['cste']['_DIMS_LABEL_MODIFIED_SHEET_SINCE']." ".$date_since;
							}
							?>
							</td>
							<td class="bdb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
						</tr>
					</table>
				</td>
				<td  style="width:15%;text-align:right">&nbsp;</td>
			</tr>
		</table>
	</h3>
	<div class="accordion_content" style="background-color:transparent;height:500px;overflow:auto;">
		<table cellspacing="0" cellpadding="2" style="width:100%;margin-top:5px;margin-bottom:10px;">
			<tbody>
			<?php if($nb_resp > 0) {
				echo "<tr><td style=\"text-align:center;font-weight:bold;\">Count : ".$db->numrows($res_p)."</td>";
				echo "<td colspan=\"2\" style=\"text-align:center;\">";

				echo  $_DIMS['cste']['_FORMS_FILTER']."&nbsp;<select name=\"id_usr\" onchange=\"javascript:document.filter.submit();\">";

				if ($id_usr==0) $sel="selected";
				else $sel="";

				echo "<option value=\"0\">".$_DIMS['cste']['_DIMS_ALL']."</option>";
				// construction de la liste

				$res_u = $db->query($sql_u, $params_u);

				while($f = $db->fetchrow($res_u)) {
					if ($id_usr==$f['id_creator']) $sel="selected";
					else $sel="";

					$name=$f['pren_creator']." ".$f['name_creator'];

					echo "<option $sel value=\"".$f['id_creator']."\">".$name."</option>";
				}

				echo "</td><td>";
				echo dims_create_button($_DIMS['cste']['_DIMS_BACK'],'./common/img/undo.gif','','','display',"admin.php?dims_action=public&dims_mainmenu=0&submenu=".dims_const::_DIMS_SUBMENU_ACTIVITIES);
				echo "</td></tr>";
				?>
				<tr style="font-size:12px;background-color:#ffffff;color:#777777;">
					<td style="width: 25%;"><?php echo $_DIMS['cste']['_DIMS_LABEL_CONTACT']; ?></td>
					<td style="width: 30%;"><?php echo $_DIMS['cste']['_DIMS_LABEL_CONT_ENTRAT']; ?></td>
					<?php
					if($type == 'new') {
						?>
						<td style="width: 20%;"><?php echo $_DIMS['cste']['_DIMS_LABEL_CREATE_ON']; ?></td>
						<?php
					}
					elseif($type == 'mod') {
						?>
						<td style="width: 20%;"><?php echo $_DIMS['cste']['_DIMS_LABEL_MODIF_ON_FEM']; ?></td>
						<?php
					}
					?>
					<td style="width: 25%;"><?php echo $_DIMS['cste']['_DIMS_LABEL_FROM']; ?></td>
				</tr>
			<?php
				$old_id = '';
				$cpte=0;

				while($tab_p = $db->fetchrow($res_p)) {
					if($old_id != $tab_p['id_pers']) {
						$date_c = dims_timestamp2local($tab_p['date']);

						if ($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
						echo '	<tr class="'.$class_col.'">
									<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="cursor: default;" id="tickets_title_3">
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
						$cpte++;
					}
				}

			}
			else {
				echo '<tr><td width="100%">'.$_DIMS['cste']['_DIMS_LABEL_NO_RESP'].'</td></tr>';
			}
			?>
		   </tbody>
	   </table>
	</div>
</div>
</form>