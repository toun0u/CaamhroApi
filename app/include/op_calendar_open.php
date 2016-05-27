<?php
$month = date('n');
$year = date('Y');
$funct = dims_load_securvalue('funct', dims_const::_DIMS_CHAR_INPUT, true, false, false);

if (isset($_GET['selected_date'])) {
	$sel_day = $sel_month = $sel_year = 0;

	switch (dims_const::DIMS_DATEFORMAT) {
		case dims_const::DIMS_DATEFORMAT_US:
			if (preg_match(dims_const::DIMS_DATEFORMAT_EREG_US, dims_load_securvalue('selected_date', dims_const::_DIMS_CHAR_INPUT, true, true, true), $regs)) {
				$sel_day = $regs[3];
				$sel_month = $regs[2];
				$sel_year = $regs[1];

				$month = $sel_month;
				$year = $sel_year;
			}
			break;

		case dims_const::DIMS_DATEFORMAT_FR:
			if (preg_match(dims_const::DIMS_DATEFORMAT_EREG_FR, dims_load_securvalue('selected_date', dims_const::_DIMS_CHAR_INPUT, true, true, true), $regs)) {
				$sel_day = $regs[1];
				$sel_month = $regs[2];
				$sel_year = $regs[3];

				$month = $sel_month;
				$year = $sel_year;
			}
			break;
	}

	$_SESSION['calendar'] = array(
		'selected_month' => $sel_month,
		'selected_day' => $sel_day,
		'selected_year' => $sel_year
	);
}
elseif (isset($_GET['calendar_month']) && isset($_GET['calendar_year'])) {
	$month = dims_load_securvalue('calendar_month', dims_const::_DIMS_NUM_INPUT, true, true, true);
	$year = dims_load_securvalue('calendar_year', dims_const::_DIMS_NUM_INPUT, true, true, true);
}

settype($day, 'integer');
settype($month, 'integer');
settype($year, 'integer');

if (isset($_GET['inputfield_id'])) {
	$_SESSION['calendar']['inputfield_id'] = dims_load_securvalue('inputfield_id', dims_const::_DIMS_CHAR_INPUT, true, true, true);
}
elseif ( isset($_GET['inputfield1_id']) && isset($_GET['inputfield2_id']) && isset($_GET['inputfield3_id']) ) {
	$_SESSION['calendar']['inputfield1_id'] = dims_load_securvalue('inputfield1_id', dims_const::_DIMS_CHAR_INPUT, true, true, true);
	$_SESSION['calendar']['inputfield2_id'] = dims_load_securvalue('inputfield2_id', dims_const::_DIMS_CHAR_INPUT, true, true, true);
	$_SESSION['calendar']['inputfield3_id'] = dims_load_securvalue('inputfield3_id', dims_const::_DIMS_CHAR_INPUT, true, true, true);
}

$selectedday = mktime(0, 0, 0, $_SESSION['calendar']['selected_month'], $_SESSION['calendar']['selected_day'], $_SESSION['calendar']['selected_year']);
$today = mktime(0, 0, 0, date('n'), date('j'), date('Y'));

$firstday = mktime(0, 0, 0, $month, 1, $year);

$weekday = date('w', $firstday);
if ($weekday == 0) {
	$weekday = 7;
}

$prev_month = ($month - 1) % 12 + (($month - 1) % 12 == 0) * 12;
$next_month = ($month + 1) % 12 + (($month + 1) % 12 == 0) * 12;

$prev_year = $year - ($prev_month == 12);
$next_year = $year + ($next_month == 1);
?>

<div id="calendar">
<div class="calendar_row">
	<div class="calendar_arrow">
		<a href="javascript:void(0);" onclick="javascript:dims_xmlhttprequest_todiv('admin-light.php','dims_op=calendar_open&calendar_month=<? echo $prev_month; ?>&calendar_year=<? echo $prev_year; ?>&funct=<? echo $funct; ?>','','dims_popup');"><img style="border:0;" src="./common/img/calendar/prev.gif"></a>
	</div>
	<div class="calendar_month">
		<? echo "{$dims_agenda_months[$month]} $year"; ?>
	</div>
	<div class="calendar_arrow">
		<a href="javascript:void(0);" onclick="javascript:dims_xmlhttprequest_todiv('admin-light.php','dims_op=calendar_open&calendar_month=<? echo $next_month; ?>&calendar_year=<? echo $next_year; ?>&funct=<? echo $funct; ?>','','dims_popup');"><img style="border:0;" src="./common/img/calendar/next.gif"></a>
	</div>
</div>
<div class="calendar_row">
<?
foreach ($dims_agenda_days as $d) {
	?>
	<div class="calendar_day"><? echo $d[0]; ?></div>
	<?
}
?>
</div>
<?
if ($weekday > 1) {
	?>
	<div class="calendar_row">
		<?
		for ($d = 1; $d < $weekday; $d++) {
			?>
			<div class="calendar_day"><div>&nbsp;</div></div>
			<?
		}
	}

	for ($d = 1; $d <= date('t', $firstday); $d++) {
		if ($weekday == 8) {
			$weekday = 1;
		}

		if ($weekday == 1) {
			?>
			<div class="calendar_row">
			<?
		}

		$class = '';
		$currentday = mktime(0, 0, 0, $month, $d, $year);
		if ($currentday == $selectedday) {
			$class = 'class="calendar_day_selected"';
		}
		elseif ($currentday == $today) {
			$class = 'class="calendar_day_today"';
		}

		if (isset($_SESSION['calendar']['inputfield_id'])) {
			$localdate = dims_timestamp2local(sprintf("%04d%02d%02d000000", $year, $month, $d));
			?>
			<div class="calendar_day"><a <? echo $class; ?> href="javascript:void(0);" onclick="javascript:dims_getelem('<? echo $_SESSION['calendar']['inputfield_id']; ?>').value='<? echo $localdate['date']; ?>';dims_hidepopup();<? echo $funct; ?>"><? echo $d; ?></a></div>
			<?
		}
		elseif ( isset($_SESSION['calendar']['inputfield1_id']) && isset($_SESSION['calendar']['inputfield2_id']) && isset($_SESSION['calendar']['inputfield3_id']) ) {
			$localyear = sprintf("%04d", $year);
			$localmonth = sprintf("%02d", $month);
			$localday = sprintf("%02d", $d);
			?>
			<div class="calendar_day"><a <? echo $class; ?> href="javascript:void(0);" onclick="javascript:dims_getelem('<? echo $_SESSION['calendar']['inputfield1_id']; ?>').value='<? echo $localyear; ?>';dims_getelem('<? echo $_SESSION['calendar']['inputfield2_id']; ?>').value='<? echo $localmonth; ?>';dims_getelem('<? echo $_SESSION['calendar']['inputfield3_id']; ?>').value='<? echo $localday; ?>';dims_hidepopup();<? echo $funct; ?>"><? echo $d; ?></a></div>
			<?
		}

		if ($weekday == 7) {
			echo '</div>';
		}
		$weekday++;
	}

	if ($weekday <= 7) {
		for ($d = $weekday; $d <= 7; $d++) {
			?>
			<div class="calendar_day"><div>&nbsp;</div></div>
			<?
		}
		echo '</div>';
	}

	if (isset($_SESSION['calendar']['inputfield_id'])) {
		$localdate = dims_timestamp2local(sprintf("%04d%02d%02d000000", date('Y'), date('n'), date('j')));
		?>
		<div class="calendar_row" style="height:1.2em;">
			<a style="display:block;float:left;line-height:1.2em;height:1.2em;" href="javascript:void(0);" onclick="javascript:dims_getelem('<? echo $_SESSION['calendar']['inputfield_id']; ?>').value='<? echo $localdate['date']; ?>';dims_hidepopup();<? echo $funct; ?>"><?php echo ucfirst($_SESSION['cste']['_DIMS_LABEL_DAY']); ?></a>
			<a style="display:block;float:right;line-height:1.2em;height:1.2em;" href="javascript:void(0);" onclick="javascript:dims_hidepopup();"><?php echo $_SESSION['cste']['_DIMS_CLOSE']; ?></a>
		</div>
		<?
	}
	elseif ( isset($_SESSION['calendar']['inputfield1_id']) && isset($_SESSION['calendar']['inputfield2_id']) && isset($_SESSION['calendar']['inputfield3_id']) ) {
		$localyear = sprintf("%04d", date('Y'));
		$localmonth = sprintf("%02d", date('n'));
		$localday = sprintf("%02d", date('j'));
		?>
		<div class="calendar_row" style="height:1.2em;">
			<a style="display:block;float:left;line-height:1.2em;height:1.2em;" href="javascript:void(0);" onclick="javascript:dims_getelem('<? echo $_SESSION['calendar']['inputfield1_id']; ?>').value='<? echo $localyear; ?>';dims_getelem('<? echo $_SESSION['calendar']['inputfield2_id']; ?>').value='<? echo $localmonth; ?>';dims_getelem('<? echo $_SESSION['calendar']['inputfield3_id']; ?>').value='<? echo $localday; ?>';dims_hidepopup();<? echo $funct; ?>"><?php echo ucfirst($_SESSION['cste']['_DIMS_LABEL_DAY']); ?></a>
			<a style="display:block;float:right;line-height:1.2em;height:1.2em;" href="javascript:void(0);" onclick="javascript:dims_hidepopup();"><?php echo $_SESSION['cste']['_DIMS_CLOSE']; ?></a>
		</div>
		<?
	}
	?>
</div>
<?
die();
