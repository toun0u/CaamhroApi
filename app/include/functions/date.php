<?php
##############################################################################
#
# Date / Time functions
#
##############################################################################


/**
* ! description !
*
* @return string returns current DATE in localized format according to the DIMS_DATEFORMAT constant
*
* @version 2.09
* @since 0.1
*
* @category date/time manipulations
*/
function dims_getdate() {return date(dims_const::DIMS_DATEFORMAT);}

/**
* ! description !
*
* @return string returns current TIME in localized format according to the DIMS_DATEFORMAT constant
*
* @version 2.09
* @since 0.1
*
* @category date/time manipulations
*/
function dims_gettime() {return date(dims_const::_DIMS_TIMEFORMAT);}

/**
* ! description !
*
* @return string returns current TIME in MySQL timestamp format
*
* @version 2.09
* @since 0.1
*
* @category date/time manipulations
*/
function dims_getdatetime() {return date(dims_const::_DIMS_DATETIMEFORMAT_MYSQL);}

/**
* ! description !
*
* @param string date in localized format
* @return string returns the param date in localized format according to the DIMS_DATEFORMAT constant
*
* @version 2.09
* @since 0.1
*
* @category date/time manipulations
*/
function dims_dateverify($mydate, $format = dims_const::DIMS_DATEFORMAT) {

	switch($format) {
		case dims_const::DIMS_DATEFORMAT_FR:
			return preg_match(dims_const::DIMS_DATEFORMAT_EREG_FR, $mydate, $regs);
			//return ereg(dims_const::DIMS_DATEFORMAT_EREG_FR, $mydate, $regs);
		break;
		case dims_const::DIMS_DATEFORMAT_US:
			return preg_match(dims_const::DIMS_DATEFORMAT_EREG_US, $mydate, $regs);
			//return ereg(dims_const::DIMS_DATEFORMAT_EREG_US, $mydate, $regs);
		break;
		default:
			return false;
		break;
	}
}

/**
* ! description !
*
* @param string time
* @return string returns the param time in localized format according to the DIMS_DATEFORMAT constant
*
* @version 2.09
* @since 0.1
*
* @category date/time manipulations
*/
function dims_timeverify($mytime) {
	return preg_match(dims_const::_DIMS_TIMEFORMATDISP_PREG, $mytime, $regs);
}

/**
* ! description !
*
* @param string date
* @param string time
* @return string returns param'd date and time in "DATETIME" format
*
* @version 2.09
* @since 0.1
*
* @category date/time manipulations
*
* @uses dims_dateverify()
*/
function dims_local2datetime($mydate,$mytime)
{
	// verify local format
	if (dims_dateverify($mydate) && dims_timeverify($mytime))
	{
		preg_match(dims_const::_DIMS_TIMEFORMAT_PREG, $mytime, $timeregs);
		switch(DIMS_DATEFORMAT)
		{
			case dims_const::DIMS_DATEFORMAT_FR:
				//ereg(dims_const::DIMS_DATEFORMAT_EREG_FR, $mydate, $dateregs);
				preg_match(dims_const::DIMS_DATEFORMAT_EREG_FR, $mydate, $dateregs);
				$mydatetime = date(dims_const::_DIMS_DATETIMEFORMAT_MYSQL, mktime($timeregs[1],$timeregs[2],$timeregs[3],$dateregs[2],$dateregs[1],$dateregs[3]));
			break;
			case dims_const::DIMS_DATEFORMAT_US:
				//ereg(dims_const::DIMS_DATEFORMAT_EREG_US, $mydate, $dateregs);
				preg_match(dims_const::DIMS_DATEFORMAT_EREG_US, $mydate, $dateregs);
				$mydatetime = date(dims_const::_DIMS_DATETIMEFORMAT_MYSQL, mktime($timeregs[1],$timeregs[2],$timeregs[3],$dateregs[2],$dateregs[3],$dateregs[1]));
			break;
		}

		return($mydatetime);
	}
	else return(false);
}

/**
* ! description !
*
* @param string date
* @return string returns param'd "DATETIME" in localized human readable form
*
* @version 2.09
* @since 0.1
*
* @category date/time manipulations
*/
function dims_datetime2local($mydatetime) {
	$mydate = Array();

	// verify mysql format

	//if (ereg(dims_const::_DIMS_DATETIMEFORMAT_MYSQL_EREG, $mydatetime, $regs)) {
	if (preg_match(dims_const::_DIMS_DATETIMEFORMAT_MYSQL_EREG, $mydatetime, $regs)) {
		$mydate['date'] = date(DIMS_DATEFORMAT, mktime($regs[4],$regs[5],$regs[6],$regs[2],$regs[3],$regs[1]));
		$mydate['time'] = date(dims_const::_DIMS_TIMEFORMAT, mktime($regs[4],$regs[5],$regs[6],$regs[2],$regs[3],$regs[1]));
		return($mydate);
	}
	else return(false);
}

//
/*
$regs[dims_const::_DIMS_DATE_YEAR] => Year
$regs[dims_const::_DIMS_DATE_MONTH] => Month
$regs[dims_const::_DIMS_DATE_DAY] => Day
$regs[dims_const::_DIMS_DATE_HOUR] => Hour
$regs[dims_const::_DIMS_DATE_MINUTE] => Minute
$regs[dims_const::_DIMS_DATE_SECOND] => Second
*/

/**
* Get detailled datetime in a tab
*
* @return array returns current date/time details in an array
*
* @version 2.09
* @since 0.1
*
* @category date/time manipulations
*/
function dims_getdatetimedetail()
{
	ereg(dims_const::_DIMS_DATETIMEFORMAT_MYSQL_EREG, date(dims_const::_DIMS_DATETIMEFORMAT_MYSQL), $regs);
	return $regs;
}

/**
* ! description !
*
* @param string timestamp
* @return array returns param'd timestamp details in an array
*
* @version 2.09
* @since 0.1
*
* @category date/time manipulations
*/
function dims_gettimestampdetail($mytimestamp) {
	//ereg(dims_const::_DIMS_TIMESTAMPFORMAT_MYSQL_EREG, $mytimestamp, $regs);
	preg_match(dims_const::_DIMS_TIMESTAMPFORMAT_MYSQL_PREG, $mytimestamp, $regs);
	return $regs;
}

/**
* ! description !
*
* @return string returns current timestamp
*
* @version 2.09
* @since 0.1
*
* @category date/time manipulations
*/
function dims_createtimestamp() {
	return date("YmdHis");
}

/**
* ! description !
*
* @param string timestamp
* @return array returns a 2 dimensions array with param'd timestamp converted to localized date/time format
*
* @version 2.09
* @since 0.1
*
* @category date/time manipulations
*
* @uses dims_gettimestampdetail()
*/
function dims_timestamp2local($mytimestamp, $format = dims_const::DIMS_DATEFORMAT) {
	// Output array declaration
	$mydate = array();

	// Trimming
	$mytimestamp = trim($mytimestamp);

	// Exploding MySQL timestamp into human readable values
	$timestamparray = dims_gettimestampdetail($mytimestamp);

	if (isset($timestamparray[dims_const::_DIMS_DATE_YEAR])) {
		$year = $timestamparray[dims_const::_DIMS_DATE_YEAR];
		$month = $timestamparray[dims_const::_DIMS_DATE_MONTH];
		$day = $timestamparray[dims_const::_DIMS_DATE_DAY];
		$hour = $timestamparray[dims_const::_DIMS_DATE_HOUR];
		$minute = $timestamparray[dims_const::_DIMS_DATE_MINUTE];
		$second = $timestamparray[dims_const::_DIMS_DATE_SECOND];
	}
	else {
		$year=$month=$day=$hour=$minute=$second=0;
	}

	// Re-constucting date depending on the $format parameter
	switch ($format)
	{
		CASE dims_const::DIMS_DATEFORMAT_FR:
		{
			$localedate = $day . '/' .$month . '/' . $year;
		}
		BREAK;

		CASE dims_const::DIMS_DATEFORMAT_US:
		{
			$localedate = $year . '/' . $month . '/' . $day;
		}
		BREAK;
	}

	$localetime = $hour . ':' . $minute . ':' . $second;

	// Constructing output array
	$mydate['date'] = $localedate;
	$mydate['time'] = $localetime;

	// returning the output array
	return $mydate;
}

/**
* Convert local date & time to datetime mysql
*
* @param string date
* @param string time
* @return string returns param'd date and time in a MySQL datetime format
*
* @version 2.09
* @since 0.1
*
* @category date/time manipulations
*
* @uses dims_dateverify()
*/
function dims_local2timestamp($mydate,$mytime = '00:00:00', $format = dims_const::DIMS_DATEFORMAT)
{
	// verify local format
	if (dims_dateverify($mydate,$format)) {// && dims_timeverify($mytime))
		preg_match(dims_const::_DIMS_TIMEFORMAT_PREG, $mytime, $timeregs);
		switch($format)
		{
			CASE dims_const::DIMS_DATEFORMAT_FR:
				//ereg(dims_const::DIMS_DATEFORMAT_EREG_FR, $mydate, $dateregs);
				preg_match(dims_const::DIMS_DATEFORMAT_EREG_FR, $mydate, $dateregs);
				$mydatetime = date(dims_const::_DIMS_TIMESTAMPFORMAT_MYSQL, mktime($timeregs[1],$timeregs[2],$timeregs[3],$dateregs[2],$dateregs[1],$dateregs[3]));
			BREAK;
			CASE dims_const::DIMS_DATEFORMAT_US:
				//ereg(dims_const::DIMS_DATEFORMAT_EREG_US, $mydate, $dateregs);
				preg_match(dims_const::DIMS_DATEFORMAT_EREG_US, $mydate, $dateregs);
				$mydatetime = date(dims_const::_DIMS_TIMESTAMPFORMAT_MYSQL, mktime($timeregs[1],$timeregs[2],$timeregs[3],$dateregs[2],$dateregs[3],$dateregs[1]));
			BREAK;
		}

		return($mydatetime);
	}
	else return(false);
}


function dims_timestamp_add($timestp, $h=0, $mn=0, $s=0, $m=0, $d=0, $y=0)
{
	$timestp_array = dims_gettimestampdetail($timestp);

	return date(dims_const::_DIMS_TIMESTAMPFORMAT_MYSQL, mktime(	$timestp_array[dims_const::_DIMS_DATE_HOUR]+$h,
														$timestp_array[dims_const::_DIMS_DATE_MINUTE]+$mn,
														$timestp_array[dims_const::_DIMS_DATE_SECOND]+$s,
														$timestp_array[dims_const::_DIMS_DATE_MONTH]+$m,
														$timestp_array[dims_const::_DIMS_DATE_DAY]+$d,
														$timestp_array[dims_const::_DIMS_DATE_YEAR]+$y
													));
}

function dims_timestp2local($date) {
	$annee = substr($date, 0, 4); // on récupère le jour
	$mois = substr($date, 4, 2); // puis le mois
	$jour = substr($date, 6, 2);
	$hour = substr($date, 8, 2);
	$minute = substr($date, 10, 2);
	$second = substr($date, 12, 2);

	switch(dims_const::DIMS_DATEFORMAT) {
		case dims_const::DIMS_DATEFORMAT_FR:
			$localedate = $jour . '/' .$mois . '/' . $annee;
			break;
		case dims_const::DIMS_DATEFORMAT_US:
			$localedate = $annee . '/' . $mois . '/' . $jour;
			break;
	}

	$localetime = $hour . ':' . $minute . ':' . $second;

	// Constructing output array
	$mydate['date'] = $localedate;
	$mydate['time'] = $localetime;

	// returning the output array
	return $mydate;
	return($mydatetime);
}

/**
* ! description !
*
* @param string timestamp
* @return int Unix timestamp
*
* @version 2.09
* @since 0.1
*
* @category date/time manipulations
*
* @uses dims_gettimestampdetail()
*/
function dims_timestamp2unix($mytimestamp) {
	// Output array declaration
	$unixTimeStamp = 0;

	// Trimming
	$mytimestamp = trim($mytimestamp);

	// Exploding MySQL timestamp into human readable values
	$timestamparray = dims_gettimestampdetail($mytimestamp);
	if (isset($timestamparray[dims_const::_DIMS_DATE_YEAR])) {
		$year = $timestamparray[dims_const::_DIMS_DATE_YEAR];
		$month = $timestamparray[dims_const::_DIMS_DATE_MONTH];
		$day = $timestamparray[dims_const::_DIMS_DATE_DAY];
		$hour = $timestamparray[dims_const::_DIMS_DATE_HOUR];
		$minute = $timestamparray[dims_const::_DIMS_DATE_MINUTE];
		$second = $timestamparray[dims_const::_DIMS_DATE_SECOND];
	}
	else {
		$year=$month=$day=$hour=$minute=$second=0;
	}

	$unixTimeStamp = mktime($hour,$minute,$second,$month,$day,$year);

	return $unixTimeStamp;
}

/**
* ! description !
*
* @param int $utimestp Unix timestamp
* @return int dims timestamp
*
* @category date/time manipulations
*
*/
function dims_unix2timestp($utimestp) {
	return date('YmdHis', $utimestp);
}

function dims_unix2local($timestamp) {
	return dims_timestp2local(dims_unix2timestp($timestamp));
}

function dims_diffdate($date1,$date2) {
	if ($date2>$date1) {
		$datedeb		 = strtotime($date1);
		$datefin		 = strtotime($date2);
	}
	else {
		$datedeb		 = strtotime($date2);
		$datefin		 = strtotime($date1);
	}

	return($datefin-$datedeb);
}

function dims_nicetime($date,$lang='fr') {
	global $_DIMS;

	if(empty($date)) {
		return "No date provided";
	}

	$periods		 = array($_DIMS['cste']['_DIMS_LABEL_SECONDS'], $_DIMS['cste']['_DIMS_LABEL_MINUTES'], $_DIMS['cste']['_DIMS_LABEL_HOURS'], $_DIMS['cste']['_DIMS_LABEL_DAYS'], $_DIMS['cste']['_DIMS_LABEL_WEEKS'], $_DIMS['cste']['_DIMS_LABEL_MONTHS'], $_DIMS['cste']['_DIMS_LABEL_YEARS'], "decade");
	$lengths		 = array("60","60","24","7","4.35","12","10");

	$now			 = time();
	$unix_date		   = strtotime($date);

	   // check validity of date
	if(empty($unix_date)) {
		return "Bad date";
	}

	// is it future date or past date
	if($now > $unix_date) {
		$difference		= $now - $unix_date;
		$deb			= $_DIMS['cste']['_DIMS_LABEL_THERE_IS'];
		$tense			= $_DIMS['cste']['_DIMS_LABEL_AGO'];

	} else {
		$difference		= $unix_date - $now;
		$deb			=  $_DIMS['cste']['_DIMS_LABEL_IN'];
		$tense			= $_DIMS['cste']['_DIMS_LABEL_FROM_NOW'];
	}

	for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
		$difference /= $lengths[$j];
	}

	$difference = round($difference);

	//if($difference != 1) {
	  //$periods[$j].= "s";
	//}

	if ($j==3 && $difference<=2) {
		$difference=24*$difference;
		$j=2;
	}
	if ($difference ==1) {
		return $deb." $difference ".substr($periods[$j],0,strlen($periods[$j])-1)." {$tense}";
	}
	else {
		return $deb." $difference $periods[$j] {$tense}";
	}
}

?>
