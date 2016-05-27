<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

if(!isset($action->fields['dateextended']) || $action->fields['dateextended']<0) $action->fields['dateextended']=0;

//echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_DATE_REGISTRATION']);
echo "<h2 style=\"padding:2px;margin-top:10px;\"><img src=\"./common/img/matching_tag_time.png\" alt=\"event\">&nbsp;".$_DIMS['cste']['_DIMS_LABEL_DATE_REGISTRATION']."</h2>";
?>
<script language="javascript">
	function updateDateExtended(cour) {
		for (i=0;i<3;i++) {
			var elem =document.getElementById('action_dateext'+i);
			if (cour==i) {
				$('#action_dateext'+i).css('display','block');
			}
			else {
				$('#action_dateext'+i).css('display','none');
			}
		}
	}
</script>
<table cellpadding="0" cellspacing="5" width="100%">
<tr>
	<td style="text-align:center;">
		<?
		for ($j=0;$j<3;$j++) {
			switch ($j) {
				case 0: $labeldateext=$_DIMS['cste']['_LABEL_UNIQUE_DATE'];break;
				case 1: $labeldateext=$_DIMS['cste']['_LABEL_SEVERAL_DATE'];break;
				case 2: $labeldateext=$_DIMS['cste']['_LABEL_ONLY_MONTH_DATE'];break;
			}
			if ($action->fields['dateextended']==$j) $checked='checked';
			else $checked='';

			echo '<input type="radio" name="action_dateextended" onchange="javascript:updateDateExtended('.$j.');" '.$checked.' value="'.$j.'">'.$labeldateext.'</input>';
		}
		?>
	</td>
</tr>
<tr>
	<td style="text-align:center;">
		<?
		$disp = ($action->fields['dateextended'] ==0) ? 'block' : 'none';
		?>
		<div id="action_dateext0" style="display:<? echo $disp;?>;">
			<div style="float:left;width:49%;display:block;">
				<table style="width:100%;">
				<tr>
					<td align="right" width="30%">
						<label ><?php echo $_DIMS['cste']['_INFOS_START_DATE']; ?>&nbsp;</label>
					</td>
					<td width="50%">
						<?php
							if ($id==0) {
								$action->fields['datejour']=business_datefr2us($datejour);
								$action->fields['datefin']=business_datefr2us($datejour);
							}
						?>
						<input class="text" type="text" id="action_datejour" name="action_datejour" value="<? echo business_dateus2fr($action->fields['datejour']); ?>">
						<a href="#" onclick="javascript:dims_calendar_open('action_datejour', event,'updateDate()');"><img src="./common/img/calendar/calendar.gif" alt="" width="31" height="18" align="top" border="0"></a>
					</td>
				</tr>
				<tr>
					<td align="right" width="30%">
						<label ><?php echo $_DIMS['cste']['_INFOS_END_DATE']; ?>&nbsp;</label>
					</td>
					<td width="50%">
						<input class="text" type="text" id="datefin" onchange="updateDate();" name="datefin" value="<? echo business_dateus2fr($action->fields['datefin']); ?>">
						<a href="#" onclick="javascript:dims_calendar_open('datefin', event,'updateDate()');">
							<img src="./common/img/calendar/calendar.gif" alt="" width="31" height="18" align="top" border="0">
						</a>
					</td>
				</tr>
				</table>
			</div>
			<?
			$disp = ($action->fields['typeaction'] == '_DIMS_PLANNING_FAIR_STEPS') ? 'none' : 'block';
			?>
			<div style="float:left;width:49%;display:<? echo $disp; ?>;">
				<table cellpadding="0" cellspacing="5" width="100%">
					<tr>
						<td align="right" width="30%">
							<label ><?php echo $_DIMS['cste']['_DIMS_LABEL_HEUREDEB']; ?>&nbsp;</label>
						</td>
						<td width="50%">
							<select class="select" name="actionx_heuredeb_h" style="width:50px;">
							<?php
							//if(isset($heure_dispo_deb)) {
							//	$heure = substr($heure_dispo_deb, 0, 2);
							//	$minute = substr($heure_dispo_deb, 3, 2);
							//}
							//else {
								if ($action->fields['heuredeb'] != '') { //isset($id)
									$heure_split = split(':',$action->fields['heuredeb']);
									$heure = $heure_split[0];
									$minute = $heure_split[1];
									$minute = $minute - ($minute%5);
								}
								else {
									//if($action->fields['typeaction'] == 'Foire') {
										$heure = "09";
										$minute = 0;//date('i');
									//}
									//else {
										//$heure = date('H');
										//$minute = 0;//date('i');
										//$minute = $minute - ($minute%5);
									//}
								}
							//}
							for ($h=dims_const::_PLANNING_H_START;$h<=dims_const::_PLANNING_H_END;$h++) {
									$sel = ($heure==$h) ? 'selected' : '';
									printf("<option %s value=\"%02d\">%02d</option>",$sel,$h,$h);
							}
							?>
							</select> h
							<select class="select" name="actionx_heuredeb_m" style="width:50px;">
									<?php
									for ($m=0;$m<4;$m++) {
																			$sel = ($minute==$m*15) ? 'selected' : '';
																			printf("<option %s value=\"%02d\">%02d</option>",$sel, $m*15, $m*15);
									}
									?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="right" width="30%">
							<label ><?php echo $_DIMS['cste']['_DIMS_LABEL_HEUREFIN']; ?>&nbsp;</label>
						</td>
						<td width="50%">
							<select class="select" name="actionx_heurefin_h" style="width:50px;">
								<?php

								//if(isset($heure_dispo_fin)) {
								//	$heure = substr($heure_dispo_fin, 0, 2);
								//	$minute = substr($heure_dispo_fin, 3, 2);
								//}
								//else {
									if ($action->fields['heurefin']) //isset($id)
									{
										$heure_split = split(':',$action->fields['heurefin']);
										$heure = $heure_split[0];
										$minute = $heure_split[1];
										$minute = $minute - ($minute%5);
									}
									else {
										//if($action->fields['typeaction'] == 'Foire') {
											$heure = "18";
											$minute = 0;//date('i');
										//}
										//else {
										//	$heure = date('H')+1;
										//	if ($heure>(_business_H_END+1)) $heure=0;
										//	$minute = 0;//date('i');
										//	$minute = $minute - ($minute%5);
										//}
									}
								//}

								for ($h=dims_const::_PLANNING_H_START;$h<=dims_const::_PLANNING_H_END;$h++)
								{
									$sel = ($heure==$h) ? 'selected' : '';
									printf("<option %s value=\"%02d\">%02d</option>",$sel,$h,$h);
								}
								?>
							</select> h
							<select class="select" name="actionx_heurefin_m" style="width:50px;">
								<?php
								for ($m=0;$m<4;$m++)
								{
									$sel = ($minute==$m*15) ? 'selected' : '';
									printf("<option %s value=\"%02d\">%02d</option>",$sel, $m*15, $m*15);
								}
								?>
							</select>
							<input type="hidden" name="actionx_duree" value=""/>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<?
		$disp = ($action->fields['dateextended'] ==1) ? 'block' : 'none';
		?>
		<div id="action_dateext1" style="display:<? echo $disp;?>;">
		<?
		require_once(DIMS_APP_PATH . '/modules/events/public_events_modifier_action_detail_dates_list.php');
		?>
		</div>
		<?
		$disp = ($action->fields['dateextended'] ==2) ? 'block' : 'none';
		?>
		<div id="action_dateext2" style="display:<? echo $disp;?>;">
		<?
		// on splite la date de debut
		$datedeb=explode("-",$action->fields['datejour']);

		echo "Month : <select name =\"month\">";
		$deb=1;
		for($j=$deb;$j<=12;$j++) {
			$selected = ($j==$datedeb[1]) ? 'selected' : '';
			if ($j<10)
				echo '<option value="0'.$j.'" '.$selected.'>'.$dims_agenda_months[$j].'</option>';
			else
			echo '<option value="'.$j.'" '.$selected.'>'.$dims_agenda_months[$j].'</option>';
		}
		echo "</select>";

		echo "&nbsp;Year : <select name =\"year\">";
		$deb=date('Y');
		for($j=$deb;$j<=$deb+20;$j++) {
			$selected = ($j==$datedeb[0]) ? 'selected' : '';
			echo '<option value="'.$j.'" '.$selected.'>'.$j.'</option>';
		}
		echo "</select>";

		?>
		</div>
	</td>
</tr>
</table>
<?
//echo $skin->close_simplebloc();
?>
