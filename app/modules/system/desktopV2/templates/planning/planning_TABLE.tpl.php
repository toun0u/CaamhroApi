<div id="body_planning">
	<script type="text/javascript" language="Javascript">
		$(document).ready(function() {
			$("#radio_selector_period").buttonset();

			<?php
			if (isset($_SESSION['desktopv2']['appointment_offer']['id'])) {
				// chargement des dates des porpositions de rendez-vous
				?>
				appointmentOfferLoadDates(<?php echo $_SESSION['desktopv2']['appointment_offer']['id']; ?>, 'planning');
				<?php
			}
			?>
		});
	</script>

	<div class="header">
		<div id="radio_selector_period">
			<input type="image" src="./common/img/previous.png" name="previous" onClick="javascript:affiche_planning('<?php echo $planning->getPreviousStep(); ?>');"/>
			<input type="radio" id="radio1" name="radio" onClick="javascript:changeViewMode('day');" <?php echo ($planning->getVisuMode()=='day')?'checked="checked"':''; ?>/><label for="radio1">Jour</label>
			<input type="radio" id="radio2" name="radio" onClick="javascript:changeViewMode('week');" <?php echo ($planning->getVisuMode()=='week')?'checked="checked"':''; ?> /><label for="radio2">Semaine</label>
			<input type="radio" id="radio3" name="radio" onClick="javascript:changeViewMode('month');" <?php echo ($planning->getVisuMode()=='month')?'checked="checked"':''; ?>/><label for="radio3">Mois</label>
			<input type="image" src="./common/img/next.png" name="next" onClick="javascript:affiche_planning('<?php echo $planning->getNextStep(); ?>');" />
		</div>

		<div class="current_moment">
			<?php
				global $dims_agenda_months;
				global $dims_agenda_days;
				if($planning->getVisuMode()=='month')
					echo '<span class="month">'.$dims_agenda_months[date('n', $planning->getDateDebTimestp())].'</span> <span class="year">'.date('Y',$planning->getDateDebTimestp()).'</span>';
				else if($planning->getVisuMode()=='week')
					echo '<span class="year">Semaine </span> <span class="month">'.date('W',$planning->getDateDebTimestp()).'</span>';
				else if($planning->getVisuMode()=='day'){
					$dayFR = date('d/m/Y', $planning->getDateDebTimestp());
					echo '<span class="year">'.$dims_agenda_days[date('N',$planning->getDateDebTimestp())].' </span> <span class="month">'.$dayFR.'</span>';
				}
			?>
		</div>
	</div>

	<div class="content">
		<?php
		if($planning->getVisuMode()=='month'){
		?>
		<table class="table_planning">
			<?php
			//calcul du jour de démarrage de la semaine
			$d_deb = date('w', $planning->getDateDebTimestp());
			if ($d_deb == 0) $d_deb = 7;

			$d_fin = date('w', $planning->getDateFinTimestp());
			if ($d_fin == 0) $d_fin = 7;

			$deb_table = $planning->getDateDebTimestp() - (($d_deb-1)*24*60*60);
			$fin_table = $planning->getDateFinTimestp() + ((7-$d_fin)*24*60*60);

			if($planning->getVisuMode()=='month'){
				?>
				<tr>
					<td class="td_plan_week td_plan_corner">&nbsp;</td>
					<?php
					foreach($dims_agenda_days as $d){
						echo '<td class="td_plan_header td_plan_day">'.$d.'</td>';
					}
				?>
				</tr>
				<?php
				$j=0;
				for($i=$deb_table; $i<=$fin_table;$i+=24*60*60){
					$day = date('Y-m-d', $i);
					$dayFR = date('d/m/Y', $i);
					if($j==0) echo '<tr><td class="td_week_number">S '.date('W', $i).'</td>';
					//affichage du contenu de la case
					$bgc = '#FFFFFF';
					$allowed = false;
					$onclick = '';

					if($planning->isCoveredDay($day)){
						$bgc		= $planning->getDayBGColor($day);
						$allowed	= $planning->isDayCreationAllowed($day);
						$onclick	= $planning->getDayCreationOnClick($day);
					}
					?>
					<td <?php if($planning->isCoveredDay($day) && $planning->isToday($day)) echo 'style="border: 2px solid '.$planning->getDefaultTodayBGColor().'"'; ?> style="background-color:<?php echo $bgc;?>">
						<div class="div_content_day">
							<div class="div_header_day <?php echo ((!$planning->isCoveredDay($day))?'div_header_notinview':'');?>">
								<div class="header_day_date"><?php echo $dayFR; ?></div>
								<?php
								if($allowed)
								{
									?>
									<div class="header_add_event"><a href="javascript:void(0);" <?php echo $onclick; ?>><img src="./common/img/add.gif" title="Ajouter un créneau"/></a></div>
									<?php
								}
								?>
							</div>
							<div class="list_events">
								<?php
								$liste_events = $planning->getListEvents($day);
								if (!empty($liste_events)) {
									foreach($liste_events as $event){
										$color = '';
										if(!empty($event['activity_type_id'])) {
											$type = new activity_type();
											$type->open($event['activity_type_id']);

											$color = isset($type->fields['color'])?$type->fields['color']:"";
										}
										?>
										<div class="event <?php if(isset($event['selected']) && $event['selected'] == true) echo 'event_selected';?>" <?php echo (!empty($color)) ? 'style="background-color: '.$color.'"' : ''; ?>>
											<a href="javascript:void(0);" <?php echo $event['onclick'];?> <?php echo $event['ondblclick'];?> >
												<?php
												echo $event['text'];
												?>
											</a><br />
											<?php
											echo $event['participants'];
											?>
										</div>
										<?php
									}
								}
								?>
							</div>
						</div>
					</td>
					<?php
					$j++;
					if($j==7)
					{
						$j=0;
						echo '</tr>';
					}
				}
			}
			?>
		</table>
		<?php
		}
		//------------------------------- MODE SEMAINE / JOUR -------------------------------------------------------
		else{
			//gestion particulière pour IE
			if(strpos($_SERVER['HTTP_USER_AGENT'],'MSIE'))
					require_once('modules/elisath/templates/backoffice/planning_weekIE.tpl.php');
			else{

			?>

		<div id="header_week_planning">
			<table>
				<tr>
					<td class="td_plan_corner">&nbsp;</td>
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
			</table>
		</div>
		<div id="content_week_planning">
		<table>
			<tbody>
				<tr class="lignes">
					<td class="td_lignes_corner td_plan_corner">
						<div class="relative_hours">
							<div class="abs_hours">
								<div class="imagenow">
									<img src="/common/modules/system/desktopV2/templates/gfx/planning/now.png"/>
								</div>
							</div>
						</div>
					</td>
					<td colspan="7">
						<div class="relative_hours">

							<div class="abs_hours">
								<div class="now"><div class="linenow"></div></div>
							<?php
							for($i=0;$i<24;$i++){
							?>
								<div class="one_markerbloc">
									<div class="raw_line"></div>
								</div>
							<?php
							}
							?>
							</div>
						</div>
					</td>
				</tr>
				<tr class="bloc_tr_events">
					<td class="td_plan_corner">
						<?php
												$hour=0;
						for($i=0;$i<24;$i++){
							$dispH = ($i<10)?'0'.$i.':00':$i.':00';
							?>
							<div class="corner_hour">
								<div class="corner_in_hour">
									<?php echo $dispH; $hour++; ?>
								</div>
							</div>
						<?php
						}
						?>
					</td>
					<?php
					$a=0;
					for($i=$planning->getDateDebTimestp(); $i<=$planning->getDateFinTimestp();$i+=24*60*60){
						$day = date('Y-m-d', $i);
						$bgc = $planning->getDayBGColor($day);
						?>
						<td class="td_plan_day" <?php if($planning->isToday($day)) echo 'style="border-left: 2px solid '.$planning->getDefaultTodayBGColor().';border-right: 2px solid '.$planning->getDefaultTodayBGColor().'"'; ?>>

							<div class="colday" style="background-color:<?php echo $bgc; ?>">
								<?php
									$liste_events = $planning->getListEvents($day);
									foreach($liste_events as $event){
										//calcul des heures au format numérique
										$num_deb	= getNumericHour($event['heuredeb']);
										$num_fin	= getNumericHour($event['heurefin']);
										$height		= ($num_fin-$num_deb)*42;
										if($height <18) $height= 18;
										$top		= $num_deb*42;
										$chevauch	= getChevauchement($liste_events, $event, $num_deb);

										$style = 'top:'.$top.'px;
												  height:'.$height.'px;
												  width:'.$chevauch[1].'%;
												  left:'.($chevauch[0]*($chevauch[1])).'%;
												  z-index:'.$chevauch[2].';';
										if(!empty($event['activity_type_id'])) {
											$type = new activity_type();
											$type->open($event['activity_type_id']);

											$style .= 'background-color: '.$type->fields['color'].';';
										}
										?>
										<div id="evt_<?php echo $a;?>" class="event_week <?php if(isset($event['selected']) && $event['selected'] == true) echo 'event_selected';?>" style="<?php echo $style;?>" >
											<div class="evt_interne">
												<a href="javascript:void(0);" <?php echo $event['onclick'];?> <?php echo $event['ondblclick'];?> >
													<div class="event_week_hour">
														<?php
															echo substr($event['heuredeb'],0,2).":".substr($event['heuredeb'],3,2).'-'.substr($event['heurefin'],0,2).":".substr($event['heurefin'],3,2);
														?>
													</div>
													<div class="event_week_activite">
														<?php
														echo (($event['creneau_title']!='')?'<span>'. $event['creneau_title']:'</span>');
														?>
													</div>
													<?php
													echo $event['participants'];
													?>
												</a>
											</div>
										</div>
										<?php
										$a++;
									}
								?>
							</div>
						</td>
					<?php
					}
					?>
				</tr>
			</tbody>
		</table>
		<script type="text/javascript">
			$('#body_planning').ready(function(){
				// ----------------------------- calcul des tailles pour la largeur des td
				var globalW = $('#body_planning').width();
				var diff = globalW - 60;//60 = taille du corner fixe à 60 px;
				var reste = diff%7;
				<?php if($planning->getVisuMode()=='week'){
					?>
						var td_day_w = (diff-reste) / 7;
					<?php
				}
				else if($planning->getVisuMode()=='day'){
					?>
						var td_day_w = diff-reste;
					<?php
				}
				?>
				$('div#header_week_planning td.td_plan_day').width(td_day_w);
				$('div#content_week_planning td.td_plan_day').width(td_day_w);
				$('div#content_week_planning tbody td:last-child.td_plan_day').width(td_day_w-15);//-15 pour l'ascenseur

				//-------------------------------------- positionnement de la barre de l'heure courante
				var now = new Date();
				var now_num = now.getHours()+(now.getMinutes()/60);
				//alert(now_num*42);
				$('div.now').css('top', now_num*42);
				$('div.imagenow').css('top', (now_num*42)-7);
				$('div.imagenow').css('left', 60-10);

				//-------------------------------------- positionnement de l'ascenseur
				$("div#content_week_planning").scrollTop(<?php echo $_SESSION['dims']['planning_scroll']; ?>);//par défaut la vu commencera à 8h
				timer_launched = false;
				$("div#content_week_planning").scroll(handleScrollPlanning);

			});

			function handleScrollPlanning() {
				if(!timer_launched){
					timer_launched = true;
					setTimeout(function(){
						dims_xmlhttprequest('admin.php', 'op=planning_scroll&value='+($("div#content_week_planning").scrollTop()));
						timer_launched = false;
					}, 1000);
				}
			}

		</script>
			<?php
			}
		}
		?>
	</div>
</div>


<div id="planning_popup"></div>


