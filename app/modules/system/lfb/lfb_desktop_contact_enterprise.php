<?php

//NOUVELLES FICHES : selection des personnes et de leurs entreprises
$sql_p = "	SELECT			t.intitule, t.id as id_tiers, t.ville, t.date_creation,
							c.id as id_creator, c.lastname as name_creator, c.firstname as pren_creator
			FROM			dims_mod_business_tiers t
			INNER JOIN 		dims_user u
			ON 				u.id = t.id_user_create
			INNER JOIN		dims_mod_business_contact c
			ON				c.id = u.id_contact
			WHERE			t.date_creation >= :datesince
			AND				t.inactif != 1
			ORDER BY		t.date_creation DESC, t.intitule";
			//
//echo $sql_p;
$res_p = $db->query($sql_p, array(
	':datesince' => $date_since2."000000"
));
$nb_resp = $db->numrows($res_p);

//FICHES MODIFIEES : selection des personnes et de leurs entreprises
$sql_pmod = "	SELECT			t.intitule, t.id as id_tiers, t.timestp_modify, t.ville,
								u.id as id_creator, u.lastname as name_creator, u.firstname as pren_creator
				FROM			dims_mod_business_tiers t
				INNER JOIN 		dims_user u
				ON 				u.id = t.id_user_create
				INNER JOIN		dims_mod_business_contact c
				ON				c.id = u.id_contact
				WHERE			t.timestp_modify >= DATE_SUB(CURDATE(),INTERVAL 15 DAY)
				AND				t.inactif != 1
				ORDER BY		t.timestp_modify DESC, t.intitule";
//echo $sql_pmod;
$res_pmod = $db->query($sql_pmod);
$nb_respmod = $db->numrows($res_pmod);

$class_col = 'trl1';

?>
<div id="vertical_container">
	<h3 class="accordion_toggle">
		<table style="width:100%;">
			<tr>
				<td align="left" width="15%">&nbsp;</td>
				<td align="left" width="70%">
					<table style="width:100%;" cellpadding="0" cellspacing="0">
						<tr>
							<td class="bgb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
							<td class="midb20">
							<?php echo $_DIMS['cste']['_DIMS_LABEL_NEW_SHEET_SINCE']." ".$date_since; ?>
							</td>
							<td class="bdb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
						</tr>
					</table>
				</td>
				<td  style="width:15%;text-align:right">&nbsp;</td>
			</tr>
		</table>
	</h3>
	<div class="accordion_content" style="background-color:transparent;height:200px;overflow:auto;">
		<table cellspacing="0" cellpadding="2" style="width:100%;margin-top:5px;margin-bottom:10px;">
			<tbody>
			<?php
				if($nb_resp > 0) {
					?>
					<tr>
					<td colspan="4" align="right">
						<a href="<?php $dims->getScriptEnv(); ?>?action=see_ent&type=new">
							<?php echo $_DIMS['cste']['_DIMS_LABEL_SEE_ALL_SHEET']; ?>
						</a>
						</td>
					</tr>
					<?php
			?>
			   <tr style="font-size:12px;background-color:#ffffff;color:#777777;">
					<td style="width: 35%;"><?php echo $_DIMS['cste']['_DIMS_LABEL_ENT_NAME']; ?></td>
					<td style="width: 20%;"><?php echo $_DIMS['cste']['_LOCATION']; ?></td>
					<td style="width: 20%;"><?php echo $_DIMS['cste']['_DIMS_LABEL_CREATE_ON']; ?></td>
					<td style="width: 25%;"><?php echo $_DIMS['cste']['_DIMS_LABEL_FROM']; ?></td>
				</tr>
			<?php
				$old_id = '';
				$i = 0;
				while($tab_p = $db->fetchrow($res_p)) {
					if($old_id != $tab_p['id_tiers']) {
						$tab_date_c = dims_timestamp2local($tab_p['date_creation']);
						$date_c = $tab_date_c['date'];

						if ($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
						echo '	<tr class="'.$class_col.'">
									<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="cursor: default;" id="tickets_title_3">
										<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat=0&dims_desktop=block&dims_action=public&action='._BUSINESS_TAB_ENT_FORM.'&part=0&id_ent='.$tab_p['id_tiers'].'&id_cont=indefini" onclick="javascript:add_visit(\''.$tab_p['id_tiers'].'\', \'tiers\');" title="Voir la fiche de cette entreprise.">'.$tab_p['intitule'].'</a>
									</td>
									<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="cursor: default;" id="tickets_title_3">
										'.$tab_p['ville'].'
									</td>
									<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="cursor: default;" id="tickets_title_3">
										'.$date_c.'
									</td>
									<td>
										<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat=0&dims_desktop=block&dims_action=public&action='._BUSINESS_TAB_CONTACT_FORM.'&contact_id='.$tab_p['id_creator'].'&id_cont=indefini" onclick="javascript:add_visit(\''.$tab_p['id_creator'].'\', \'personne\');" title="Voir la fiche de ce contact.">'.$tab_p['pren_creator'].'&nbsp;'.$tab_p['name_creator'].'</a>
									</td>
								</tr>';

						$i++;
						if($i == 50)
							break;
					}
					$old_id = $tab_p['id_tiers'];
				}

				?>
				<tr>
					<td colspan="4" align="right">
						<a href="<?php $dims->getScriptEnv(); ?>?action=see_ent&type=new">
							<?php echo $_DIMS['cste']['_DIMS_LABEL_SEE_ALL_SHEET']; ?>
						</a>
					</td>
				</tr>
				<?php
			}
			else {
				echo '<tr><td width="100%">'.$_DIMS['cste']['_DIMS_LABEL_NO_ENTERPRISE'].'</td></tr>';
			}
			?>
			</tbody>
		</table>
	</div>
	<h3 class="accordion_toggle">
		<table style="width:100%;">
			<tr>
				<td align="left" width="15%">&nbsp;</td>
				<td align="left" width="70%">
					<table style="width:100%;" cellpadding="0" cellspacing="0">
						<tr>
							<td class="bgb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
							<td class="midb20">
							<?php echo $_DIMS['cste']['_DIMS_LABEL_MODIFIED_SHEET_SINCE']." ".$date_since; ?>
							</td>
							<td class="bdb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
						</tr>
					</table>
				</td>
				<td  style="width:15%;text-align:right">&nbsp;</td>
			</tr>
		</table>
	</h3>
	<div class="accordion_content" style="background-color:transparent;height:200px;overflow:auto;">
		<table cellspacing="0" cellpadding="2" style="width:100%;margin-top:5px;margin-bottom:10px;">
			<tbody>
			<?php
			if($nb_respmod > 0) {
				?>
				<tr>
					<td colspan="4" align="right">
						<a href="<?php $dims->getScriptEnv(); ?>?action=see_ent&type=mod">
							<img src="./common/img/view.png" alt="view"><span>&nbsp;<?php echo $_DIMS['cste']['_DIMS_LABEL_SEE_ALL_SHEET']; ?>
						</a>
					</td>
				</tr>
				<?php
			?>
				<tr style="font-size:11px;background-color:#ffffff;color:#777777;">
					<td style="width: 35%;"><?php echo $_DIMS['cste']['_DIMS_LABEL_ENT_NAME']; ?></td>
					<td style="width: 20%;"><?php echo $_DIMS['cste']['_LOCATION']; ?></td>
					<!--<td style="width: 20%;"><? //echo _DIMS_LABEL_MOD_CHAMP; ?></td>-->
					<td style="width: 20%;"><?php echo $_DIMS['cste']['_DIMS_LABEL_MODIF_ON_FEM']; ?></td>
					<td style="width: 25%;"><?php echo $_DIMS['cste']['_DIMS_LABEL_FROM']; ?></td>
				</tr>
			<?php
				$old_id ='';
				$i = 0;
				while($tab_pmod = $db->fetchrow($res_pmod)) {
					if($old_id != $tab_pmod['id_tiers']) {
						$date_mod = dims_timestamp2local($tab_pmod['timestp_modify']);

						if ($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
						echo '	<tr class="'.$class_col.'">
									<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="cursor: default;" id="tickets_title_3">
										<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat=0&dims_desktop=block&dims_action=public&action='._BUSINESS_TAB_ENT_FORM.'&part=0&id_ent='.$tab_pmod['id_tiers'].'&id_cont=indefini" onclick="javascript:add_visit(\''.$tab_pmod['id_tiers'].'\', \'tiers\');" title="Voir la fiche de cette entreprise.">'.$tab_pmod['intitule'].'</a>
									</td>
									<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="cursor: default;" id="tickets_title_3">
										'.$tab_pmod['ville'].'
									</td>
									<td onmouseout="javascript:this.style.cursor=\'default\';" onmouseover="javascript:this.style.cursor=\'pointer\';" style="cursor: default;" id="tickets_title_3">
										'.$date_mod['date'].'
									</td>
									<td>
										<a href="admin.php?dims_mainmenu='.dims_const::_DIMS_MENU_CONTACT.'&cat=0&dims_desktop=block&dims_action=public&action='._BUSINESS_TAB_CONTACT_FORM.'&contact_id='.$tab_pmod['id_creator'].'&id_cont=indefini" onclick="javascript:add_visit(\''.$tab_pmod['id_creator'].'\', \'personne\');" title="Voir la fiche de ce contact.">'.$tab_pmod['pren_creator'].'&nbsp;'.$tab_pmod['name_creator'].'</a>
									</td>
								</tr>';

						$i++;
						if($i == 50)
							break;
					}
					$old_id = $tab_pmod['id_tiers'];
				}

				?>
				<tr>
					<td colspan="4" align="right">
						<a href="<?php $dims->getScriptEnv(); ?>?action=see_ent&type=mod">
							<img src="./common/img/view.png" alt="view"><span><?php echo $_DIMS['cste']['_DIMS_LABEL_SEE_ALL_SHEET']; ?>
						</a>
					</td>
				</tr>
				<?php

			}
			else {
				echo '<tr><td width="100%">'.$_DIMS['cste']['_DIMS_LABEL_NO_ENTERPRISE'].'</td></tr>';
			}
			?>
			</tbody>
		</table>
	</div>
</div>
