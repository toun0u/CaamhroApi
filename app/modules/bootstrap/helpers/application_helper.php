<?php
if( ! function_exists('include_dir')){
	function include_dir($path){
		if(!empty($path) && file_exists($path) && is_dir($path)){
			if ($handle = opendir($path)) {
				while (false !== ($entry = readdir($handle))) {
					if(pathinfo($entry, PATHINFO_EXTENSION) == 'php'){//en chopan le file info on a juste text/plain
						include_once $path.'/'.$entry;
					}
				}
			}
		}
	}
}

function get_path($c, $a, $params = array(), $articleid = 0){ // TODO : use http://fr2.php.net/http-build-query
	if(!empty($articleid)){
		$url =	dims::getInstance()->getScriptEnv().'?articleid='.$articleid.'&c='.$c.'&a='.$a;
	}else{
		$url =	dims::getInstance()->getScriptEnv().'?c='.$c.'&a='.$a;
	}
	if( ! empty($params) ){
		foreach($params as $param => $value){
			if( isset($value) ) $url .= '&'.$param.'='.$value;
		}
	}
	return $url;
}

function get_params($values, $prefix){
	require_once DIMS_APP_PATH.'include/import_gpr.php';//Sécurise les entrées $_GET  / $_POST / $_FILES
	// par d�faut on r�cup�re les champs du formulaire ($values)
	$longueurprefixe = strlen($prefix);
	$params = array();
	foreach ($values AS $key => $value) {
		$pref = substr($key,0,$longueurprefixe);
		if ($pref==$prefix) {
			$prop = substr($key,$longueurprefixe);
			$params[$prop] = $value;
		}
	}
	return $params;
}

function image_tag($path, $attributes = array()){
	$img = '<img src="'.dims::getInstance()->getRootPath().'/'.view::getInstance()->getTemplateWebPath($path).'" ';
	foreach($attributes as $attr => $val){
		$img .= $attr.'="'.$val.'" ';
	}
	$img .= '/>';
	return $img;
}


/*
Fonction qui prend un tableau sous la forme ci-dessous et le retourne pour pouvoir l'injecter dans un field select de la classe form
Array
(
	[0] => Array
		(
			[id] => 2
			[label] => aaa
		)

	[1] => Array
		(
			[id] => 3
			[label] => bbb
		)
	...
)
*/
function selectable($tab, $val, $label){
	$select = array();
	foreach($tab as $fields){
		$select[$fields[$val]] = $fields[$label];
	}
	return $select;
}

//part du principe que $object est un tableau indexé sur l'id de l'objet et contient une liste d'objets DDO
function pick($key, $objects, $separator = ' - '){
	$lst = array();
	foreach($objects as $o){
		if( ! is_array($key)){
			if(array_key_exists($key, $o->fields)){
				$lst[$o->get('id')] = $o->get($key);
			}
		}
		else{
			$val = '';
			$first = true;
			foreach($key as $k){
				if(!$first) $val .= $separator;
				if(array_key_exists($k, $o->fields)){
					$val .= $o->get($k);
				}
				if($first) $first = false;
			}
			$lst[$val] = $val;
		}
	}
	return $lst;
}

function float_value($val){
	return str_replace(',', '.', $val);
}

function datefr_todb($date, $format = 'dt'){
	$tab = explode('/', $date);
	if(count($tab) == 3){
		switch($format){
			case 'dt': //datetime
				return date('Y-m-d H:i:s', strtotime($tab[2].'/'.$tab[1].'/'.$tab[0]));
				break;
			case 'd': //just time
				return date('Y-m-d', strtotime($tab[2].'/'.$tab[1].'/'.$tab[0]));
				break;
		}

	}
	else return null;
}

function format_date($date){
	$tab = explode('-', $date);
	return $tab[2].'-'.$tab[1].'-'.$tab[0];
}

function format_heure($heure){
	return substr($heure, 0, -3);
}

function datetime_tofr($date){
	return date('d/m/Y à H:i', strtotime($date));
}

function daypart_fromdt($date){
	return date('d/m/Y', strtotime($date));
}

function hourpart_fromdt($date){
	return date('H:i', strtotime($date));
}

function composed_date_todt($day, $hour){
	return date('Y-m-d H:i:00', strtotime(datefr_todb($day, 'd') . ' ' . $hour));
}
?>
