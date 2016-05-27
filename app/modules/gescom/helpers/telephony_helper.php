<?php
// Lejal Simon helpers pour mod_telephony

function formatPhoneNumber($num) {
	$num = preg_replace('/[^0-9]/', '', $num);
    $len = strlen($num);

    if($len == 7) $num = preg_replace('/([0-9]{2})([0-9]{2})([0-9]{3})/', '$1 $2 $3', $num);
    elseif($len == 8) $num = preg_replace('/([0-9]{3})([0-9]{2})([0-9]{3})/', '$1 - $2 $3', $num);
    elseif($len == 9) $num = preg_replace('/([0-9]{3})([0-9]{2})([0-9]{2})([0-9]{2})/', '$1 - $2 $3 $4', $num);
    elseif($len == 10) $num = preg_replace('/([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/', '$1 $2 $3 $4 $5', $num);
    elseif($len == 13) $num = preg_replace('/([0-9]{2})([0-9]{2})([0-9]{1})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/', '+$2 $3 $4 $5 $6 $7', $num);

    return $num;
}

function objectToArray($d) {
	if (is_object($d)) {
		$d = get_object_vars($d);
	}

	if (is_array($d)) {
		return array_map(__FUNCTION__, $d);
	}
	else {
		return $d;
	}
}

?>