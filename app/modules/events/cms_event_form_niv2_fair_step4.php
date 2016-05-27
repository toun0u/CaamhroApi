<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
echo '<div style="width:100%; overflow:auto;">';

$disp = "none";
/***********************************/
/* Affichage des délégués inscrits */
/***********************************/

$sql_d = 	"SELECT *
			FROM dims_mod_business_event_etap_delegue
			WHERE id_action = :idaction
			AND id_etap = :idetape
			AND id_contact = :idcontact";
$res_d = $db->query($sql_d, array(':idaction' => $id_evt, ':idetape' => $etap_selected['id'], ':idcontact' => $_SESSION['dims']['user']['id_contact']));
if($db->numrows($res_d) > 0) {
	//on affiche le tableau de resultats
	$color = "#EEEEEE";
	echo '<table width="100%" cellpadding="0" cellspacing="0" style="font-size:12px;">
			<tr style="background-color:#EEEEEE;height:20px;">
				<th align="left">'.$_DIMS['cste']['_DIMS_LABEL_NAME'].'</th>
				<th align="left">'.$_DIMS['cste']['_DIMS_LABEL_FIRSTNAME'].'</th>
				<th align="left">'.$_DIMS['cste']['_DIMS_LABEL_EMAIL'].'</th>
				<th align="left">'.$_DIMS['cste']['_DIMS_LABEL_MOBILE'].'</th>
				<th align="left">'.$_DIMS['cste']['_FAIRS_DATE_PRESENCE'].'</th>
				<th></th>
			</tr>';
	while($tab_deg = $db->fetchrow($res_d)) {
		if($color == "#EEEEEE") $color = "#FFFFFF";
		else $color = "#EEEEEE";
		//$date = dims_timestamp2local($tab_deg['date_inscr']);
		$date_deb = dims_timestamp2local($tab_deg['date_presence']);
		if($tab_deg['date_presence_fin'] != '') {
			$date_fin_step4 = dims_timestamp2local($tab_deg['date_presence_fin']);
			$date = $_DIMS['cste']['_FROM']." ".$date_deb['date']." ".$_DIMS['cste']['_DIMS_LABEL_A']." ".$date_fin_step4['date'];
		}
		else {
			$date = $_DIMS['cste']['_AT']." ".$date_deb['date'];
		}
		echo '<tr style="background-color:'.$color.';height:20px;">
				<td>'.$tab_deg['lastname'].'</td>
				<td>'.$tab_deg['firstname'].'</td>
				<td>'.$tab_deg['email'].'</td>
				<td>'.$tab_deg['mobile'].'</td>
				<td>'.$date.'</td>
				<td>
					<a href="'.dims_urlencode($dims->getUrlPath().'?action=delete_fairs_delegue&id_event='.$id_evt.'&id_etap='.$etap_selected['id'].'&id_delegue='.$tab_deg['id'],false).'">
						<img src="./common/img/close.png" alt="'.$_DIMS['cste']['_DELETE'].'" style="border:none;"/>
					</a>
				</td>
			</tr>';
	}
	echo '<tr>
			<td colspan="5" align="center" style="padding-top:10px;">
				<input type="button" class="submit" onclick="javascript:dims_switchdisplay(\'form_1\');" value="'.$_DIMS['cste']['_ADD_DELEGUE_STAND'].'"/>
			</td>
		  </tr>
		</table>';
}
else {
	$disp = "block";
}

/****************************/
/* Affichage du formulaire **/
/****************************/

if (!isset($_SESSION['dims']['tmp_nb_insc'])) $_SESSION['dims']['tmp_nb_insc']=1;
$nb_form = 1;
$nb_form = dims_load_securvalue('nb_form', dims_const::_DIMS_NUM_INPUT, false, true, false, $_SESSION['dims']['tmp_nb_insc'],$nb_form);
?>

<div id="form_1" style="display:<?php echo $disp; ?>;">
		<?php
		//Formulaire niv.1 * nb_inscrip (Pour les personnes s'inscrivant a plusieurs)
		echo  $_DIMS['cste']['_DIMS_EVT_INSCRIPT'];
		global $dims;
		?>

	<form action="<? echo dims_urlencode($dims->getUrlPath().'?action=save_fairs_delegue&id_event='.$id_evt.'&id_etap='.$etap_selected['id'],false); ?>" method="POST" id='form_inscrip_niv1' name="form_inscrip_niv1">
		<input type="hidden" name="nb_inscrip" value="<?php echo $nb_form ?>" />
		<input type="hidden" name="id_ct" value="<? echo $_SESSION['dims']['user']['id_contact']; ?>"/>
		<?php
		//Verification nombre d'inscription positif
		if($nb_form < 1)
			$nb_form = 1;

		$control=false;
		for($i = 0; $i < $nb_form; $i++) {
		?>
		<div class="inscriptions">
			<div class="info_oblig">
				<table>
					<tr>
						<td>
							<label for="del_lastname"><?php echo $_DIMS['cste']['_DIMS_LABEL_NAME']; ?> <span style="color:#FF0000">*</span></label>
						</td>
					</tr>
					<tr>
						<td>
							<input type="text" name="del_lastname" id="del_lastname" value="" class="content"/>
						</td>
					</tr>
					<tr>
						<td>
							<label for="del_firstname"><?php echo $_DIMS['cste']['_DIMS_LABEL_FIRSTNAME']; ?> <span style="color:#FF0000">*</span></label>
						</td>
					</tr>
					<tr>
						<td>
							<input type="text" name="del_firstname" id="del_firstname"  value="" class="content"/>
						</td>
					</tr>
					<tr>
						<td>
							<label for="del_firstname"><?php echo $_DIMS['cste']['_FAIRS_DATE_PRESENCE']; ?> <span style="color:#FF0000">*</span></label>
						</td>
					</tr>
					<tr>
						<td>
							<table width="100%">
								<tr>
									<td><? echo $_DIMS['cste']['_FROM']." "; ?></td>
									<td>
										<select name="del_date_presence" id="del_date_presence" class="content">
											<option value="">--</option>
											<?php
												$sql_d = 	"SELECT datejour
															FROM dims_mod_business_action
															WHERE (id = :idcontact1 OR id_parent = :idcontact2)
															ORDER BY datejour ASC";
												$res_d = $db->query($sql_d, array(':idcontact1' => $id_evt, ':idcontact2' => $id_evt));
												while($tabd = $db->fetchrow($res_d)) {

													$date_tmstp = str_replace('-','',$tabd['datejour'])."000000";
													$date_ymd = dims_timestamp2local($date_tmstp);

													echo '<option value="'.$date_tmstp.'">'.$date_ymd['date'].'</option>';
												}
											?>
										</select>
									</td>
								</tr>
								<tr>
									<td><? echo $_DIMS['cste']['_DIMS_LABEL_A']." "; ?></td>
									<td>
										<select name="del_date_presence_fin" id="del_date_presence_fin" class="content">
											<option value="">--</option>
											<?php
												$sql_d = 	"SELECT datejour
															FROM dims_mod_business_action
															WHERE (id = :idcontact1 OR id_parent = :idcontact2)
															ORDER BY datejour ASC";
												$res_d = $db->query($sql_d, array(':idcontact1' => $id_evt, ':idcontact2' => $id_evt));
												while($tabd = $db->fetchrow($res_d)) {

													$date_tmstp = str_replace('-','',$tabd['datejour'])."000000";
													$date_ymd = dims_timestamp2local($date_tmstp);

													echo '<option value="'.$date_tmstp.'">'.$date_ymd['date'].'</option>';
												}
											?>
										</select>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>
			<div class="info_compl">
				<table>
					<tr>
						<td>
							<label for="del_email"><?php echo $_DIMS['cste']['_DIMS_LABEL_EMAIL']; ?> <span style="color:#FF0000">*</span></label>
						</td>
					</tr>
					<tr>
						<td>
							<input type="text" name="del_email" id="del_email" value="" class="content"/>
						</td>
					</tr>
					<tr>
						<td>
							<label for="del_mobile"><?php echo $_DIMS['cste']['_DIMS_LABEL_MOBILE']; ?> <span style="color:#FF0000">*</span></label>
						</td>
					</tr>
					<tr>
						<td>
							<input type="text" name="del_mobile" id="del_mobile" value="" class="content"/><br />
						</td>
					</tr>
				</table>
			</div>
			<p style="clear: both;">
				<span style="color:#FF0000">* <?php echo $_SESSION['cste']['_DIMS_LABEL_MANDATORY_FIELDS']; ?></span>
			</p>
		</div>
			<?php
			}
			?>
			<div class="save">
			<?php
					echo '<input type="button" value="Submit >" class="submit" onclick="javascript:verif_form();"/>';
			?>
		</div>
	</form>
</div>
<?
echo '</div>';
?>
