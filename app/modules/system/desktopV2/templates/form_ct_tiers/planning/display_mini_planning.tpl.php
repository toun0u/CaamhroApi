<?php
global $dims_agenda_days;
$currentDay = date('N');
$firstDay = date('N',mktime(0,0,0,date("n"),1));
$nbJours = date('t');
?>
<div class="mini-planning">
	<table cellpadding="0"cellspacing="0">
		<tr>
			<th colspan="7">
				<?= date('F Y'); ?>
			</th>
		</tr>
		<tr>
			<?php
			foreach($dims_agenda_days as $nd => $d){
				?>
				<td class="label-day<?= ($nd == $currentDay)?' current':''; ?>">
					<?= substr($d, 0,1); ?>
				</td>
				<?php
			}
			?>
		</tr>
		<tr>
			<?php
			// Première ligne avec éventuellement le mois précédent
			if($firstDay > 1){
				$nbJoursPrev = date('t',mktime(0,0,0,-1));
				$startPrev = $nbJoursPrev-$currentDay+2;
				for($i=$startPrev;$i<=$nbJoursPrev;$i++){
					?>
					<td class="num-day other">
						<?= $i; ?>
					</td>
					<?php
				}
			}
			$restFirstSem = 7-$firstDay+1;
			for($i=1; $i<=$restFirstSem;$i++){
				?>
				<td class="num-day">
					<?= $i; ?>
				</td>
				<?php
			}
			?>
		</tr>
		<?php
		$y = 1;
		$restFirstSem2 = $restFirstSem+1;
		for($i=$restFirstSem2;$i<=$nbJours;$i++){
			if($y == 1){
				?>
				<tr>
				<?php
			}
			?>
			<td class="num-day">
				<?= $i; ?>
			</td>
			<?php
			if($y == 7){
				?>
				</tr>
				<?php
				$y = 1;
			}else
				$y++;
		}
		// Dernière ligne avec éventuellement le mois suivant
		if($y < 7){
			$numLastDay = date('N',mktime(0,0,0,date("n"),$nbJours));
			$nextDays = 7-$numLastDay;
			//for($i=1;$i)
		}
		?>
	</table>
</div>
