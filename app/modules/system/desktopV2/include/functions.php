<?php
// function permettant le tri d'une liste entre un contact et un tiers
function sortCtTiers($a,$b){
	$vala = '';
	$valb = '';
	switch(get_class($a)){
		case 'tiers':
			$vala = $a->fields['intitule'];
			break;
		case 'contact':
			$vala = $a->fields['lastname']." ".$a->fields['firstname'];
			break;
	}
	switch(get_class($b)){
		case 'tiers':
			$valb = $b->fields['intitule'];
			break;
		case 'contact':
			$valb = $b->fields['lastname']." ".$b->fields['firstname'];
			break;
	}
	return strcmp($vala, $valb);
}

// function permettant le tri d'une liste entre un contact et un tiers par date de création
function sortCtTiersByCreate($a,$b){
	$vala = '';
	$valb = '';
	switch(get_class($a)){
		case 'tiers':
			$vala = $a->get('date_creation');
			break;
		case 'contact':
			$vala = $a->get('date_create');
			break;
	}
	switch(get_class($b)){
		case 'tiers':
			$valb = $b->get('date_creation');
			break;
		case 'contact':
			$valb = $b->get('date_create');
			break;
	}
	return strcmp($vala, $valb);
}

// recherche d'un élément dans la pile de filtres des concepts
function concept_stack_search($array, $elem) {
	if (empty($elem) || empty($array)) {
		return -1;
	}

	foreach ($array as $key => $value) {
		$exists = true;
		foreach ($elem as $skey => $svalue) {
			$exists = ($exists && isset($array[$key][$skey]) && $array[$key][$skey] == $svalue);
		}
		if($exists){ return $key; }
	}

	return -1;
}

// mets le mot @word au pluriel en fonction du nombre @count
function pluralize($word, $count) {
    if (
            intval(strip_tags($count)) > 1
        &&	strtoupper(substr($word, -1)) != 'X'
    ) {
        $word .= 's';
    }
    return $count.' '.$word;
}

//--------- pour le planning mode jour et semaine
function getNumericHour($h){
	$dh = substr($h,0,2);
	$dm = substr($h,3,2);
	return $dh+($dm/60);
}

function getChevauchement($liste, $event, $deb){
	$cpt		= 1;
	$pos		= 0;
	$zindex		= 2;
	$trouve		= false;

	foreach($liste as $e){
		if($e['creneau_id'] != $event['creneau_id']){
			$hd = getNumericHour($e['heuredeb']);
			$hf = getNumericHour($e['heurefin']);
			if($deb >= $hd && $deb < $hf){
				$hcompare = floor($hd);
				$hparam = floor($deb);
				if($hparam==$hcompare){
					$cpt++;
					if($trouve==false) $pos++;
				}
				$zindex++;
			}
		}
		else{
			$trouve = true;
		}
	}
	$res = array();
	$res[0] = $pos;
	$res[1] = floor(100/$cpt);
	$res[2] = $zindex;
	return $res;
}
