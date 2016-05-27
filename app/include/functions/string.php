<?php

##############################################################################
#
# string functions
#
##############################################################################

function dims_nl2br($text) {
   return preg_replace("/\\\\r\\\\n|\\\\n|\\\\r/", "<br />", $text);
}

/**
* ! description !
*
* @param string string to cut
* @param int length of string to keep
* @return string param'd string first n chars
*
* @version 2.09
* @since 0.1
*
* @category string manipulations
*/
function dims_strcut($str,$len = 30, $mode = 'left') {
	// mode = 'left' / 'middle'
	if (strlen($str)>$len) {
		switch($mode) {
			case 'left':
				$str = mb_substr($str,0,$len,mb_detect_encoding($str)).'...';
			break;

			case 'middle':
				$str = mb_substr($str,0,($len-3)/2,mb_detect_encoding($str)).'...'.mb_substr($str,-($len-3)/2,($len-3)/2,mb_detect_encoding($str));
			break;
		}

	}
	return($str);
}

/**
* ! description !
*
* @param string $str
* @param string $car
* @param int $nb
* @param int $right
* @return string
*
* @version 2.09
* @since 0.1
*
* @category string manipulations
*/
function dims_fillstring($str, $car, $nb, $right=false) {
	$l=strlen($str);
	for ($i=$l;$i<$nb;$i++) {
		if ($right) $str = $str.$car;
		else $str = $car.$str;
	}
	return $str;
}

function dims_convertaccents($content) {

	$accents = array("¥" => "Y", "µ" => "u", "À" => "A", "Á" => "A",
			"Â" => "A", "Ã" => "A", "Ä" => "A", "Å" => "A",
			"Æ" => "A", "Ç" => "C", "È" => "E", "É" => "E",
			"Ê" => "E", "Ë" => "E", "Ì" => "I", "Í" => "I",
			"Î" => "I", "Ï" => "I", "Ð" => "D", "Ñ" => "N",
			"Ò" => "O", "Ó" => "O", "Ô" => "O", "Õ" => "O",
			"Ö" => "O", "Ø" => "O", "Ù" => "U", "Ú" => "U",
			"Û" => "U", "Ü" => "U", "Ý" => "Y", "ß" => "s",
			"à" => "a", "á" => "a", "â" => "a", "ã" => "a",
			"ä" => "a", "å" => "a", "æ" => "a", "ç" => "c",
			"è" => "e", "é" => "e", "ê" => "e", "ë" => "e",
			"ì" => "i", "í" => "i", "î" => "i", "ï" => "i",
			"ð" => "o", "ñ" => "n", "ò" => "o", "ó" => "o",
			"ô" => "o", "õ" => "o", "ö" => "o", "ø" => "o",
			"ù" => "u", "ú" => "u", "û" => "u", "ü" => "u",
			"ý" => "y", "ÿ" => "y", "Œ" => "OE", "œ" => "oe");

	return(strtr($content, $accents));
}


function dims_urlrewrite($content) {
	$cars = array("&" => "_", "'" => "_", "\"" => "_", " " => "_", "/" => "_");
	return (urlencode(strtr(dims_convertaccents(strtolower($content)), $cars)));
}


function dims_urlencode($url,$force=false) {
	if	((defined('_DIMS_URL_ENCODE') && _DIMS_URL_ENCODE) || $force) {
		if (strstr($url,'?')) list($script, $params) = explode('?', $url, 2);
		else {$script = $url; $params = '';}

		return("$script?dims_url=".urlencode(base64_encode($params)));
	}
	else return($url);
}

// parse buffer to get sql queries in an array
function dims_parsesql($buffer) {
	// this script comes from phpmyadmin
	// get the original script in ./libraries/import/sql.php
	// copyright(c) phpMyAdmin Devel team
	$i=0;
	$finished= false;
	$sql = '';
	$start_pos = 0;

	$res = array();
	$len = strlen($buffer);

	while ($i < $len) {
		// Find first interesting character, several strpos seem to be faster than simple loop in php:
		//while (($i < $len) && (strpos('\'";#-/', $buffer[$i]) === FALSE)) $i++;
		//if ($i == $len) break;
		$oi = $i;
		$p1 = strpos($buffer, '\'', $i);
		if ($p1 === FALSE) {
			$p1 = 2147483647;
		}
		$p2 = strpos($buffer, '"', $i);
		if ($p2 === FALSE) {
			$p2 = 2147483647;
		}
		$p3 = strpos($buffer, ';', $i);
		if ($p3 === FALSE) {
			$p3 = 2147483647;
		}
		$p4 = strpos($buffer, '#', $i);
		if ($p4 === FALSE) {
			$p4 = 2147483647;
		}
		$p5 = strpos($buffer, '--', $i);
		if ($p5 === FALSE || $p5 >= ($len - 2) || $buffer[$p5 + 2] > ' ') {
			$p5 = 2147483647;
		}
		$p6 = strpos($buffer, '/*', $i);
		if ($p6 === FALSE) {
			$p6 = 2147483647;
		}
		$p7 = strpos($buffer, '`', $i);
		if ($p7 === FALSE) {
			$p7 = 2147483647;
		}
		$i = min ($p1, $p2, $p3, $p4, $p5, $p6, $p7);
		if ($i == 2147483647) {
			$i = $oi;
			if (!$finished) {
				break;
			}
			// at the end there might be some whitespace...
			if (trim($buffer) == '') {
				$buffer = '';
				$len = 0;
				break;
			}
			// We hit end of query, go there!
			$i = strlen($buffer) - 1;
		}

		// Grab current character
		$ch = $buffer[$i];

		// Quotes
		if (!(strpos('\'"`', $ch) === FALSE)) {
			$quote = $ch;
			$endq = FALSE;
			while (!$endq) {
				// Find next quote
				$pos = strpos($buffer, $quote, $i + 1);
				// No quote? Too short string
				if ($pos === FALSE) {
					// We hit end of string => unclosed quote, but we handle it as end of query
					if ($finished) {
						$endq = TRUE;
						$i = $len - 1;
					}
					break;
				}
				// Was not the quote escaped?
				$j = $pos - 1;
				while ($buffer[$j] == '\\') $j--;
				// Even count means it was not escaped
				$endq = (((($pos - 1) - $j) % 2) == 0);
				// Skip the string
				$i = $pos;
			}
			if (!$endq) {
				break;
			}
			$i++;
			// Aren't we at the end?
			if ($finished && $i == $len) {
				$i--;
			} else {
				continue;
			}
		}

		// Not enough data to decide
		if ((($i == ($len - 1) && ($ch == '-' || $ch == '/'))
			|| ($i == ($len - 2) && (($ch == '-' && $buffer[$i + 1] == '-') || ($ch == '/' && $buffer[$i + 1] == '*')))
			) && !$finished) {
			break;
		}

		// Comments
		if ($ch == '#'
				|| ($i < ($len - 1) && $ch == '-' && $buffer[$i + 1] == '-' && (($i < ($len - 2) && $buffer[$i + 2] <= ' ') || ($i == ($len - 1) && $finished)))
				|| ($i < ($len - 1) && $ch == '/' && $buffer[$i + 1] == '*')
				) {
			// Copy current string to SQL
			if ($start_pos != $i) {
				$sql .= substr($buffer, $start_pos, $i - $start_pos);
			}
			// Skip the rest
			$i = strpos($buffer, $ch == '/' ? '*/' : "\n", $i);
			// didn't we hit end of string?
			if ($i === FALSE) {
				if ($finished) {
					$i = $len - 1;
				} else {
					break;
				}
			}
			// Skip *
			if ($ch == '/') {
				$i++;
			}
			// Skip last char
			$i++;
			// Next query part will start here
			$start_pos = $i;
			// Aren't we at the end?
			if ($i == $len) {
				$i--;
			} else {
				continue;
			}
		}

		// End of SQL
		if ($ch == ';' || ($finished && ($i == $len - 1))) {
			$tmp_sql = $sql;
			if ($start_pos < $len) {
				$tmp_sql .= substr($buffer, $start_pos, $i - $start_pos + 1);
			}
			// Do not try to execute empty SQL
			if (!preg_match('/^([\s]*;)*$/', trim($tmp_sql))) {
				$sql = $tmp_sql;
				$res[] = $sql;

				$buffer = substr($buffer, $i + 1);
				// Reset parser:
				$len = strlen($buffer);
				$sql = '';
				$i = 0;
				$start_pos = 0;
				// Any chance we will get a complete query?
				if ((strpos($buffer, ';') === FALSE) && !$finished) {
					break;
				}
			} else {
				$i++;
				$start_pos = $i;
			}
		}
	} // End of parser loop

	return($res);
}

// marten_berglund at hotmail dot com (php.net)
function dims_make_links($text) {
	$text = preg_replace(
				array(
						'!(^|([^\'"]\s*))([hf][tps]{2,4}:\/\/[^\s<>"\'()]{4,})!mi',
						'!<a href="([^"]+)[\.:,\]]">!',
						'!([\.:,\]])</a>!'
					),
				array(
						'$2<a href="$3">$3</a>',
						'<a href="$1">',
						'</a>$1'
					),
				$text);
	$text = preg_replace_callback('/(([_A-Za-z0-9-]+)(\\.[_A-Za-z0-9-]+)*@([A-Za-z0-9-]+)(\\.[A-Za-z0-9-]+)*)/ix',
								function($m){
									return "stripslashes((strlen('".$m[2]."')>0?'<a href=\"mailto:".$m[0]."\">$m[0]</a>':'$m[0]'))";
								},
								$text);
	return $text;
}

function dims_convert_encoding($str, $toenc, $fromenc) {
	$str = mb_convert_encoding($str, $toenc, $fromenc);

	if ($fromenc == 'UTF-8') {
		$str = html_entity_decode(htmlentities($str." ", ENT_COMPAT, 'UTF-8'));
		$str = substr($str, 0, strlen($str)-1);
	}

	return($str);
}

function dims_xmlencode($str) {
	return str_replace(array("&", ">", "<", "\""), array("&amp;", "&gt;", "&lt;", "&quot;"), $str);
}

function dims_str_replace_once($search, $replace, $subject) {
	$firstChar = strpos($subject, $search);
	if($firstChar !== false) {
		$beforeStr = substr($subject,0,$firstChar);
		$afterStr = substr($subject, $firstChar + strlen($search));
		return $beforeStr.$replace.$afterStr;
	} else {
		return $subject;
	}
}

function dims_dynamic_replace(&$content,$arrayval) {
	$array_from=array();
	$array_to=array();

	preg_match_all('/{([^}]+)/i', $content, $resultat);

	foreach($resultat[1] as $res) {

		if (isset($arrayval[$res])) {
			$array_from[]="{".$res."}";
			$array_to[]=$arrayval[$res];
		}
		else {
			$array_from[]="{".$res."}";
			$array_to[]="";
		}
	}
	$content= str_replace($array_from,$array_to,$content);
}

function dims_format_template($str, $template) {

	$kt = strlen($template);
	$ks = strlen($str);
	$res = '';
	$j = 0;
	$startoption=false;
	$finishoption=false;
	$isoption=false;
	$choption='';
	$startoblig=false;
	$finishoblig=false;
	$isoblig=false;
	$choblig='';

	for($i=0; $i<$kt; $i++) {
		if ($j==$ks) break;
		switch ($c = $template{$i}) {
			case '#':
				$res .= $str{$j++};
				break;

			case '!':
				$res .= strtoupper($str{$j++}) ;
				break;

			case '(':
				$startoption=true;
				$finishoption=false;
				$isoption=false;

				if ($c==$str{$j}) {
					$isoption=true;
					$choption='(';
					$j++;
				}
				break;
			case '[':
				$startoblig=true;
				$finishoblig=false;
				$isoblig=false;

				if ($c==$str{$j} || $str{$j}=='(') {
					$isoblig=true;
					$choblig='';
					$j++;
				}
				break;
			case ']':
				$startoblig=false;
				$finishoblig=false;
				if ($isoblig) {
					$res .= $choblig;
					$j++;
				}
				$choblig='';
				break;

			case ')':
				$finishoption=false;
				$startoption=false;
				if ($isoption) {
					$choption.=")";
					$res .= $choption;
					$j++;
				}
				$choption='';
				break;

			default:
				if ($startoption) {
					if ($isoption) {
						if (is_numeric($c)) {
							$choption.=$str{$j++};
						}
						else {
							$isoption=false;
							$j-=strlen($choption);
						}
						$startoption=false;
					}
				}
				elseif ($startoblig) {
					if ($isoblig) {
						if (is_numeric($c)) {
							$choblig.=$str{$j++};
						}
						else {
							$isoblig=false;
							$j-=strlen($choblig);
						}
						$startoblig=false;
					}
				}
				else {
					$res .= $c;
				}
				break;
		}
	}
	return $res;
}

function dims_format_phone($str,$lang='') {
	$db = dims::getInstance()->getDb();
	$original=$str;

	$result=$str;
	//echo $str." -> ";
	$str = str_replace(array(' ','-','.'), '', $str);

	// remplacement du +
	if (substr($str,0,2)=="00") {
		$str="+".substr($str,2);
	}

	// suppression du premier code si prefix entre parenth�se
	$strcompare=str_replace(array('(',')'), '', $str);

	$c='';
	$cc='';
	$codeprefix='';
	$mask='';			// mask d'affichage
	$indic=false;		// presence d'indicatif
	$len=strlen($str);	// longueur de chaine
	$c=$str[0];

	if ($c=='+') {
		// on recherche l'indicatif
		$indic=true;
	}

	if ($lang=='') {
		// recherche du pays par analyse du debut de la chaine
		if (!isset($_SESSION['dims']['phone'])) {
			$sql = "SELECT		iso,phoneprefix
				FROM		dims_country ";

			$rs_fields=$db->query($sql);

			while ($fields = $db->fetchrow($rs_fields)) {
				$_SESSION['dims']['phone'][$fields['phoneprefix']]=$fields['iso'];
			}
		}
		// on test jusqu'a 4 prefix
		for($j=1;$j<=4;$j++) {
			$prefix=substr($strcompare,$indic,$j);
			if (isset($_SESSION['dims']['phone'][$prefix])) {
				$lang=$_SESSION['dims']['phone'][$prefix];
				$codeprefix=$prefix;
			}
		}
	}

	$mask="+";
	$convert=true;
	if (substr($str,0,strlen($codeprefix)+2)=="(".$codeprefix.")") {
		// on supprime les parenth�se du prefix
		$str=str_replace("(".$codeprefix.")",$codeprefix,$str);
	}

	if ($indic) {
		$prefix=substr($str,1,strlen($codeprefix));
	}
	else {
		$prefix=substr($str,0,strlen($codeprefix));
	}

	if ($prefix==$codeprefix) {
		$str=substr($str,strlen($codeprefix)+($indic)); // on tronque
	}

	switch($lang) {
		case 'IT':
			$codeprefix.=" [0]### ### #####";
			$mask.=$codeprefix;
		case 'CH':
			$codeprefix.=" ## ## ## ####";
			$mask.=$codeprefix;
			break;
		case 'MA':
			$codeprefix.=" ###.###.####";
			$mask.=$codeprefix;
			break;
		case 'FR':
			// +33383676289
			// 0033383676289
			// 0383676289
			// traitement du prefix
			$codeprefix.=" #.##.##.##.####";
			$mask.=$codeprefix;
			//echo $mask."<br>";
			break;
		case 'LU':
			$codeprefix.=" ####-";
			$mask.=$codeprefix;
			$mask=str_pad($mask,strlen($mask)+strlen($str)-4,"#");
			break;

		case 'US':
			$codeprefix.=" ####-";
			$mask.=$codeprefix;
			$mask=str_pad($mask,strlen($mask)+strlen($str)-4,"#");
			break;

		default:

			if (($prefix=='' || $prefix=="0") && strlen($str)==9) {
				$str=$prefix.$str;
				$codeprefix="##.##.##.##.##";
				$mask=$codeprefix;
			}
			else {
				$convert=false;
			}
			break;
	}

	if ($convert) {
		$result=dims_format_template($str,$mask);
	}

	if ($result=="" && $original!='') {
		$result=$original;
	}

	return $result;
}

/*
 * Fonction de conversion de chaine à l'aide de htmlentities
 */
function dimsEncodeString($text='') {
	return htmlentities($text,ENT_COMPAT,_DIMS_ENCODING);
}

/*
 * Cyril 16 Déc. 2011 > Fonction permettant de restituer des extraits d'un texte autour de mots recherchés
 * $text : la source dans à partir de laquelle on travaille
 * $needles : mots recherchés dans la source
 * $nb_words : nombre de à prendre autour de ceux recherché (gauche et droite)
 */
function dims_getExtract($text, $needles, $nb_words){
	if(isset($needles)){
		$text = str_replace( array( '<br>', '<br />', "\n", "\r" ), array( '', '', '', '' ), $text );
		//$needle = ;
		$words = explode(' ', $text);

		$i=0;
		$positions = array();
		foreach($words as $w){
			foreach($needles as $needle){
				if(strpos(strtolower(dims_convertaccents($w)), $needle) !== false){
					$positions[] = $i;
				}
			}
			$i++;
		}
		$nb_founds = count($positions);
		$idx = 0;
		$result = '[...] ';
		for($i=0;$i<count($words); $i++){
			if($idx < $nb_founds && $i >=$positions[$idx]-$nb_words && $i < $positions[$idx]){//avant
				$result .= $words[$i]. ' ';
			}
			else if($idx < $nb_founds && $i >= $positions[$idx] && $i < $positions[$idx] + $nb_words){
				$result .= $words[$i]. ' ';
			}
			else if($idx < $nb_founds && $i == $positions[$idx] + $nb_words){
				$idx ++;
				if($idx < $nb_founds && $i < $positions[$idx] - $nb_words ) $result .= ' [...] ';
			}
		}
		if(substr($result, strlen($result)-6, strlen($result)) != '[...] ')	$result .= ' [...]';
		return $result;
	}
	else return $text;
}

/*
 * Cyril 19 Déc. 2011 > Fonction permettant de restituer des extraits d'un texte autour de mots recherchés
 * $text : la source dans à partir de laquelle on travaille
 * $needles : mots recherchés dans la source
 * $tag_start : balise html à placer à gauche de chaque terme recherché
 * $tag_end : balise html à placer à droite de chaque terme recherché
 */
function dims_getManifiedWords($text, $needles, $tag_start, $tag_end){
	//tri du tableau de mots par ordre décroissant de taille - pour gérer ceux qui subsument les autres
	$nb_words = count($needles);
	if($nb_words > 0){
		$max = strlen($needles[0]);
		$max_idx = 0;
		for($i=1; $i<$nb_words; $i++){
			$taille = strlen($needles[$i]);
			if($taille > $max ){
				//échange de position pour le tri
				$tmp = $needles[$max_idx];
				$needles[$max_idx] = $needles[$i];
				$needles[$i] = $tmp;
				$max_idx = $i;
				$max =	$taille;
			}
		}

		$added_characters = strlen($tag_start) + strlen($tag_end);
		$text_size = strlen($text);
		$copy = $text;
		$text = utf8_decode($text);
		foreach($needles as $needle){
			$start = -1;
			if($needle != '' && strpos($tag_start, $needle) === false && strpos($tag_end, $needle) === false){//sécurité --> pour éviter de tomber dans une boucle infinie en cherchant du texte que l'on rajoute par les tags html
				while($start < $text_size && ($start = strpos(strtolower(dims_convertaccents($copy)), strtolower($needle), $start+1)) !== false ){
					$text = substr($text , 0, $start). $tag_start . substr($text , $start, strlen($needle)) . $tag_end . substr($text , $start + strlen($needle)) ;
					$text_size = strlen($text);
					$copy = utf8_encode($text);
					$start += $added_characters;
				}
			}
		}
	}
	return utf8_encode($text);
}

?>
