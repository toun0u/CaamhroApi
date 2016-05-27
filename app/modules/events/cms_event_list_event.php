<?
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$_SESSION['event']['etape_sel']=0;
//on ne selectionne que les events de niveau 2 dont les inscriptions ne sont pas closes
$sql_evt = "SELECT		a.*,
						ei.id_action,
						ei.validate
			FROM		dims_mod_business_action a
			INNER JOIN	dims_mod_business_event_inscription ei
			ON			a.id = ei.id_action
			INNER JOIN	dims_user u
			ON			u.id_contact = ei.id_contact
			AND			u.id = ".$_SESSION['dims']['userid']."
			WHERE		a.close != 1
			AND			a.niveau = 2
			AND			a.is_model!=1
			AND			a.libelle NOT LIKE '%_model'
			AND			a.id_parent = 0
			ORDER BY	ei.validate DESC";

$webworkspace= $dims->getWebWorkspaces($_SESSION['dims']['webworkspaceid']);
if ((isset($webworkspace['activeevent']) && $webworkspace['activeevent'] || isset($webworkspace['activeeventstep']) && $webworkspace['activeeventstep']) && $_SESSION['dims']['user']['id_contact']>0) { //ok pour un admin mais pas pour un user lambda

	$sql_evt = "SELECT		distinct a.*,
									   ei.id_action,
									   ei.validate
						FROM		   dims_mod_business_action a
						LEFT JOIN	   dims_mod_business_event_inscription ei
						ON			   a.id = ei.id_action
						AND			   ei.id_contact = ".$_SESSION['dims']['user']['id_contact']."
						WHERE		   a.close != 1
						AND			   a.niveau = 2
						AND			   a.is_model!=1
						AND			   a.libelle NOT LIKE '%_model'
						AND			   a.id_parent = 0
						ORDER BY	   ei.validate DESC";

	$is_dims_user=true;
}
elseif($_SESSION['dims']['user']['id_contact']>0) {
	// on selectionne tous les events ou il est deja inscrit

	$sql_evt = "SELECT	distinct a.*,
						ei.id_action,
						ei.validate
			FROM		dims_mod_business_action a
			INNER JOIN	dims_mod_business_event_inscription ei
			ON			a.id = ei.id_action
			AND			ei.id_contact = :idcontact
			WHERE		a.close != 1
			AND			a.niveau = 2
			AND			is_model!=1
			AND			a.id_parent = 0
			ORDER BY	ei.validate DESC";
	$is_dims_user=true;
}

$res_evt = $db->query($sql_evt, array(':idcontact' => $_SESSION['dims']['user']['id_contact']) );

$datejour=time();
global $business_mois;
?>
<div id="content2_2">
	<div class="title"><? echo $_DIMS['cste']['_DIMS_LABEL_EVENTS']; ?></div>
	<div class="historic"></div>
</div>
<div id="contener2">
	<div id="content2_3">
		<table cellpadding="0" cellspacing="0">
				<tr>
					<td class="separation1" style="width:450px;border-right:1px solid #acacac">
						<span><? echo $_DIMS['cste']['_DIMS_EVT_INSCRIPT_SELVES']; ?></span>
						<table width="100%" cellpadding="0" cellspacing="0">
							<?
							if($db->numrows($res_evt) > 0) {
								while($tab_evt = $db->fetchrow($res_evt)) {

									$a = explode('-',$tab_evt['datejour']);
									$dateevt = mktime(0, 0, 0, $a[1], $a[2], $a[0]);

									if($tab_evt['validate'] > 0) {
										 $date_evt = array();
										 preg_match('/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/', $tab_evt['datejour'], $date_evt);
										 ?>

									<tr>
										<td width="100%" colspan="2">
											<div class="bloc_calendar">
												<table width="100%" cellpadding="0" cellspacing="0">
													<tr>
														<td align="center" class="calendar_top"><? echo substr($business_mois[intval($date_evt[2])],0,3).". ".$date_evt[1]; ?></td>
													</tr>
													<tr>
														<td align="center" class="calendar_bot"><? echo $date_evt[3]; ?></td>
													</tr>
												</table>
											</div>
											<div class="bloc_calendar_text">
											<?
											if($tab_evt['validate'] > 0) {
												echo '<a class="lien" href="index.php?id_event='.$tab_evt['id_action'].'" style="font-weight:bold;font-family:Trebuchet MS,Arial,Helvetica,sans-serif;font-style:italic;font-size:14px;">';
											}
											echo $tab_evt['libelle'];
											if($tab_evt['validate'] > 0) {
												echo "</a>";
											}
											?>
											<br>
											<?
											if($tab_evt['close'] == 1) {
												echo $_DIMS['cste']['_DIMS_LABEL_REGISTRATION_REFUSED'];
											}
											else {
												switch($tab_evt['validate']) {
													case 0:
															if ($is_dims_user) {
																echo '<a style="margin-left:30px;" href="/index.php?op=fairs&action=sub_eventinscription&id_event='.$tab_evt['id'].'&id_contact='.$_SESSION['dims']['user']['id_contact'].'">'.$_DIMS['cste']['_DIMS_LABEL_REGISTER'].'</a>';
															}
															else {
																echo $_DIMS['cste']['_DIMS_LABEL_REGISTRATION_REFUSED'];
															}
														break;
													case 1:
															echo $_DIMS['cste']['_DIMS_LABEL_REGISTRATION_CURRENT'];
														break;
													case 2:
															echo $_DIMS['cste']['_DIMS_LABEL_REGISTRATION_VALIDATED'];
														break;
												}
											}

											?>

											</div>
											<div style="float:right;width:100px;margin-top: 20px;">
												<?
												require_once(DIMS_APP_PATH . '/modules/events/classes/class_event_inscription.php');
												$ei = new event_insc();
												$res=$ei->statusInscription($tab_evt['id'],$_SESSION['dims']['user']['id_contact']);
												$color="grey";

												if (isset($res['status'])) {
													switch ($res['status']) {
														case -1 :
															$color="red";
															break;
														case 0 :
															$color="orange";
															break;
														case 1:
															$color="green";
															break;
													}
												}
												echo "<font style=\"color:".$color.";\">".$res['valide']." / ".$res['total']." <img width=\"12\" height=\"12\" src=\"./common/modules/system/img/ico_point_".$color.".gif\">";
												?>
											</div>
										</td>
									</tr>
									<?
									}
								}
							}
							?>

						</table>

					</td>
					<td class="separation3" style="width:480px;">
						<span><? echo $_DIMS['cste']['_DIMS_LABEL_EVT_COMING']; ?></span>
						<?
						$res_fut='';

						//on selectionne tous les events disponibles
						$sql_evt2 = "SELECT	distinct a.*
								FROM		dims_mod_business_action a
								WHERE		a.close != 1
								AND			a.niveau = 2
								AND			is_model!=1
								AND			a.id_parent = 0
								AND			a.timestp_open <= ".date("YmdHis")."
								AND			a.datejour >= CURDATE()
								AND			a.id NOT IN (SELECT id_action FROM dims_mod_business_event_inscription WHERE id_contact = :idcontact )
								ORDER BY	timestp_open DESC";

						$cpte_event=0;
						$res_fut = $db->query($sql_evt2,array(':idcontact' => $_SESSION['dims']['user']['id_contact'] ));

						if($db->numrows($res_fut) > 0) {

							while($tab_fut = $db->fetchrow($res_fut)) {
								$a = explode('-',$tab_fut['datejour']);
								$dateevt = mktime(0, 0, 0, $a[1], $a[2], $a[0]);

								if($dateevt>$datejour) {
									$date_fut = array();
									preg_match('/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/', $tab_fut['datejour'], $date_fut);
									$cpte_event++;
								?>
									<table width="100%" cellpadding="0" cellspacing="0">
										<tr>
											<td width="100%" colspan="2">
												<div class="bloc_calendar">
													<table width="100%" cellpadding="0" cellspacing="0">
														<tr>
															<td align="center" class="calendar_top"><? echo substr($business_mois[intval($date_fut[2])],0,3).". ".$date_fut[1]; ?></td>
														</tr>
														<tr>
															<td align="center" class="calendar_bot"><? echo $date_fut[3]; ?></td>
														</tr>
													</table>
												</div>
												<div class="bloc_calendar_text"><? echo $tab_fut['libelle']; ?></div>
												<div style="float:right;width:150px;margin-top:20px;">
																								  <?
																								  echo '<a class="lien" style="margin-left:30px;" href="'.dims_urlencode('/index.php?op=fairs&action=sub_eventdetail&id_event='.$tab_fut['id'],true).'">'.$_DIMS['cste']['_EVENT_DETAILS'].'</a>';
																								  ?>
																								</div>
												<div style="float:right;width:150px;margin-top:20px;">
													<?
													/*if($tab_fut['close'] == 1) {
														   echo $_DIMS['cste']['_DIMS_LABEL_REGISTRATION_REFUSED'];
													   }
													   else {
														  echo '<a class="lien" style="margin-left:30px;" href="/index.php?op=fairs&action=sub_eventinscription&id_event='.$tab_fut['id'].'&id_contact='.$_SESSION['dims']['user']['id_contact'].'">'.$_DIMS['cste']['_DIMS_LABEL_REGISTER'].'</a>';

													   }*/
													   ?>
												</div>
											</td>
										</tr>
									</table>
								<?
								}
							}

							//if ($cpte_event>0) {
							//	echo '<div class="historic" style="text-align:right;margin-right:10px;"><img src="'.$_SESSION['dims']['front_template_path'].'/gfx/historic.png" border="0"><a style="float:right;height:24px;line-height:24px;" href="/index.php?op=fairs&action=view_all_registration">'.$_DIMS['cste']['_DIMS_LABEL_EVT_COMING'].'</a></div>';
							//}
						}
						?>
						<br>
						<!--<a class="lien" style="font-weight:bold;font-family:Trebuchet MS,Arial,Helvetica,sans-serif;font-style:italic;font-size:14px;" href="mailto:Andre.Hansen@eco.etat.lu">
						Contribute to our survey<br> Your suggestions are welcome here !<br> Give us your feedback
						</a>-->
					</td>
					<!--<td class="separation3">
						<span>Informations</span>
						<div class="logo_profil"></div>
						<div class="bulle_texte">
							<a href="#"><img src="./gfx/close_bulle.png" border="0" style="float:right"></a>
							<span>05/11/2010</span>
							<span>Be careful, it remains you only three days to send us files for you brochure !</span>
						</div>
						<div class="logo_profil"></div>
						<div class="bulle_texte">
							<a href="#"><img src="./gfx/close_bulle.png" border="0" style="float:right"></a>
							<span>05/11/2010</span>
							<span>Be careful, it remains you only three days to send us files for you brochure !</span>
						</div>
					</td>-->
				</tr>
			</table>

	</div>
</div>
<?php

if($db->numrows($res_evt) > 0) {

	while($tab_evt = $db->fetchrow($res_evt)) {

		$a = explode('-',$tab_evt['datejour']);
		$dateevt = mktime(0, 0, 0, $a[1], $a[2], $a[0]);

		if($tab_evt['validate'] > 0 || $dateevt>$datejour) {
			 $date_evt = array();
			 preg_match('/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/', $tab_evt['datejour'], $date_evt);

			echo '<tr style="border-bottom:1px solid #ACACAC;"><td style="width:20%"><table><tr><td class="title">';
			if($tab_evt['validate'] > 0) {
				echo $tab_evt['libelle'].'</td></tr>';
				echo '<tr><td class="date"><span class="red">'.$date_evt[3].'</span> <span class="mois">'.$business_mois[intval($date_evt[2])].'</span><span class="red"> '.$date_evt[1].'</span>';
			}
			else {
				echo $tab_evt['libelle'];
			}

			echo '</td></tr></table></td>';

			// on ecrit le descriptif
			echo '<td class="text">'.$tab_evt['description'];



			if($tab_evt['validate'] > 0) {
				echo '<a href="index.php?id_event='.$tab_evt['id_action'].'" style="margin-left:30px;font-weight:bold;font-family:Trebuchet MS,Arial,Helvetica,sans-serif;font-style:italic;font-size:14px;">'.$_DIMS['cste']['_DIMS_LABEL_GO'].'></a>';
			}
			echo '		</td>
					</tr>';
		}
	}
}
?>
</table>
