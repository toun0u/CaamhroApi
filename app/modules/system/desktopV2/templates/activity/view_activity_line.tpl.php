<?php
$a_df = explode('-', $this->fields['datejour']);
?>

<div id="activityDates">
	<table class=" clear">
		<tr>
			<td class="bloc_calendar">
				<table cellspacing="0" cellpadding="0" width="100%">
					<tbody>
						<tr>
							<td align="center" class="calendar_top"><?php echo date('M', mktime(0, 0, 0, $a_df[1])); ?>. <?php echo $a_df[0]; ?></td>
						</tr>
						<tr>
							<td align="center" class="calendar_bot"><?php echo $a_df[2]; ?></td>
						</tr>
						</tbody>
				</table>
			</td>
			<td valign="top">
				<h5><?= $this->getLibelle(); ?></h5>
				<?= $this->getDescriptionHTML(); ?>
			</td>
		</tr>
	</table>

	<?php
	/*
	if ($this->fields['datefin'] != '0000-00-00') {
		$a_dt = explode('-', $this->fields['datefin']);
		?>
		<table class="ro_calendar clear">
			<tr>
				<td class="bloc_calendar">
					<table cellspacing="0" cellpadding="0" width="100%">
						<tbody>
							<tr>
								<td align="center" class="calendar_top"><?php echo date('M', mktime(0, 0, 0, $a_dt[1])); ?>. <?php echo $a_dt[0]; ?></td>
							</tr>
							<tr>
								<td align="center" class="calendar_bot"><?php echo $a_dt[2]; ?></td>
							</tr>
							</tbody>
					</table>
				</td>
				<?php
				if ($this->fields['heurefin'] != '00:00:00') {
					$a_ht = explode(':', $this->fields['heurefin']);
					?>
					<td align="center">
						<?php
						echo $a_ht[0].':'.$a_ht[1];
						?>
					</td>
					<?php
				}
				?>
			</tr>
		</table>
		<?php
	}
	*/
	?>
</div>
