<?php
$datejour = time();
?>
<div id="content2_2">
	<div class="title"><? echo $_DIMS['cste']['COMING_EVENTS']; ?></div>
	<div class="historic"></div>
</div>
<table width="98%" cellpadding="0" cellspacing="0">
	<tr>
		<td class="separation3" style="width:100%;">
			<div style="margin-bottom: 10px; overflow: hidden; float: left; width: 70%;">
				<img src="./common/modules/events/img/icon_events_mini.png" border="0" style="float:left;margin-right: 10px;" alt="events" title="events">
					<span style="float: left;line-height:33px;font-family:trebuchet MS;font-size: 16px;color: #424242">
						<? echo $_DIMS['cste']['_DIMS_LABEL_EVT_COMING']; ?>
					</span>
			</div>
			<div class="champ_select">
				<span>Status</span>
				<select name="select">
					<option value="-1">All</option>
				</select>
			</div>
			<?
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

			$cpte_event=0;
			$res_fut = $db->query($sql_evt2, array(':idcontact' => $_SESSION['dims']['user']['id_contact'] ));

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
								<td width="100%" colspan="2" style="border-top:1px solid #d6d6d6;">
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
									<div class="picture_subscriptions">
										<table width="100%" cellpadding="0" cellspacing="0">
											<tr>
												<td align="center">
													<img src="/common/modules/events/img/img_events.png" border="0" alt="events" title="picture events">
												</td>
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
	</tr>
</table>
