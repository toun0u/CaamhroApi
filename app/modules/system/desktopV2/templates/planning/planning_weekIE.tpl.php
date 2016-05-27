<div id="div_planning_ie">
	<table id="table_week_ie">
		<tr>
			<td class="td_plan_corner <?php if($planning->getVisuMode()=='day')echo 'td_ie_daycorner';?>">&nbsp;</td>
			<?php
			$d = 1;
			for($i=$planning->getDateDebTimestp(); $i<=$planning->getDateFinTimestp();$i+=24*60*60){
				$day = date('Y-m-d', $i);
				$dayFR = date('d/m/Y', $i);
				$allowed	= $planning->isDayCreationAllowed($day);
				$onclick	= $planning->getDayCreationOnClick($day);
				?>

				<td class="td_plan_header td_plan_day" <?php if($planning->isToday($day)) echo ' style="background-color:'.$planning->getDefaultTodayBGColor().';border: 1px solid '.$planning->getDefaultTodayBGColor().';"';?>>
					<div class="week_head_day">
						<div class="label_day">
							<?php
							if($planning->getVisuMode()=='week')
								echo $dims_agenda_days[$d];
							else{
								echo $dims_agenda_days[date('N', $i)];
							}
							?>
						</div>
						<div class="raw_date">
							<?php echo $dayFR;?>
						</div>
					</div>
					<?php
					if($allowed)
					{
					?>
						<div class="add_event_bloc">
							<a href="javascript:void(0);" <?php echo $onclick; ?>><img src="./common/img/add.gif" title="Ajouter un créneau"/></a>
						</div>
					<?php
					}
					?>
				</td>
				<?php
				$d++;
			}
			?>
		</tr>
		<?php
			for($i=0;$i<24;$i+=2){
				$hour = '';
				if($i<10)$hour= '0'.$i;
				else $hour= $i;

				$hfin = $i+1;
				if($hfin<10)$hfin= '0'.$hfin;
				?>
					<tr>
						<td class="td_week_number <?php if($planning->getVisuMode()=='day')echo 'td_ie_daycorner';?>">
							<?php
								echo $hour.':00';
							?>
						</td>
						<?php
						for($d=$planning->getDateDebTimestp(); $d<=$planning->getDateFinTimestp();$d+=24*60*60){
							$day = date('Y-m-d', $d);
							$bgc = $planning->getDayBGColor($day);
							?>
							<td style="background-color:<?php echo $bgc; ?>">
							<div class="list_events">
								<?php
								$liste_events = $planning->getListEvents($day, $hour.':00:00', $hfin.':59:59');
								foreach($liste_events as $event){
									$color = '';
									if(!empty($event['activity_type_id'])) {
										$type = new activity_type();
										$type->open($event['activity_type_id']);

										$color = $type->fields['color'];
									}
									?>
									<div class="event <?php if($event['selected'] == true) echo 'event_selected';?>" <?php echo (!empty($color)) ? 'style="background-color: '.$color.'"' : ''; ?>>
										<a href="javascript:void(0);" <?php echo $event['onclick'];?> <?php echo $event['ondblclick'];?> >
											<?php
												echo $event['text'];
											?>
										</a>
									</div>
									<?php
								}
								?>
							</div>
							</td>
							<?php
						}
						?>
					</tr>
				<?php
			}
		?>
	</table>
</div>
