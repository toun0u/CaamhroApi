<?php
ini_set('memory_limit','512M');

require_once(DIMS_APP_PATH . "/include/functions/string.php");

$workspaces = $dims->getWorkspaces();

// detection d'un ou plusieurs espaces
// traitement identique que l'indexation
$wordsearch=dims_convertaccents(html_entity_decode(strip_tags(dims_load_securvalue('word', dims_convertaccents::_DIMS_CHAR_INPUT, true, true, true)))); //,"\""

$wordsearch=str_replace (array("+","<","&","=","[","*","{","}","(",")","\\","\r","\n","\t","\"","-","_","]",",","'","`","|","~",":","?","!",";","")," ",$wordsearch);
if (substr($wordsearch,strlen($wordsearch)-1,1)==" ") {
	$wordsearch=trim($wordsearch)." ";
}
else {
	$wordsearch=trim($wordsearch);
}

$tabword=explode(" ",$wordsearch);

// test if last element is define or not :
if (empty($tabword[sizeof($tabword)-1])) {
	$predict=true;
}
else $predict=false;


// comparaison de la mise en mémoire des résultats précalculés
if (!isset($_SESSION['dims']['search']['querycache'])) $_SESSION['dims']['search']['querycache']=array();

// element supprimé par le user, on vide la colonne correspondante
for ($i=sizeof($tabword)-1;$i<sizeof($_SESSION['dims']['search']['querycache']);$i++) {
	unset($_SESSION['dims']['search']['querycache'][$i]);
}

if (sizeof($tabword)==1 && !$predict) {
		$tabword[0]=str_replace("\"","",$tabword[0]);
		$wordsearch=$tabword[0];
		if (sizeof($_SESSION['dims']['search']['querycache'])>0) {
			unset($_SESSION['dims']['search']['querycache']);
			$_SESSION['dims']['search']['querycache']=array();
		}

		$c=0;

		$rs=$db->query("select distinct id,word from dims_keywords where ucase(word) like :word order by count desc limit 0,5", array(
			':word' => array('type' => PDO::PARAM_STR, 'value' => $wordsearch.'%'),
		));
		if ($db->numrows($rs)>0) {
			while ($fields = $db->fetchrow($rs)) {
				if ($c++) echo '|';
				echo "{$fields['word']}";
			}
		}
		else {
			// on levenstein
			$rs=$db->query("select distinct id,word from dims_keywords where left(word,1)=:firstletter AND length(word)>=:wordlength  order by count desc limit 0,50", array(
				':firstletter' => array('type' => PDO::PARAM_STR, 'value' => substr($wordsearch,0,1)),
				':wordlength' => array('type' => PDO::PARAM_INT, 'value' => strlen($wordsearch)-1),
			));
			if ($db->numrows($rs)>0) {
				$curpourcent=0;
				$tabresultpercent=array();
				$tabcorresp=array();
				while ($fields = $db->fetchrow($rs)) {
					$res= similar_text	( $wordsearch , $fields['word'], $percent);
					//$pourcent= levenshtein($wordsearch , $fields['word']);
					$tabresultpercent[$fields['id']]=$percent;
					$tabcorresp[$fields['id']]=$fields['word'];
				}
				arsort($tabresultpercent);
				//dims_print_r($tabresultpercent);
				//asort($tabresultpercent);
				$c=0;
				foreach($tabresultpercent as $k=>$val) {
					if ($val>="50" && $c<5) {
						if ($c++) echo '|';
						echo $tabcorresp[$k];
						$c++;
					}
				}
			}
		}
}
else {
	$tabkey=array();
	$tabwords=array();
	$searchword="";
	$c=0;
	if (!strpos($wordsearch,"\"")===false) {
		$expression=true;
	}
	else {
		$expression=false;
	}

	// on traite les phrases : bcp plus difficile
	// 1er : contruction des premiers éléments
	foreach($tabword as $word) {

		$word=str_replace("\"","",$word);

		if ($c<sizeof($tabword)) {
			// verification du cache deja existant
			// si rempli pas besoin de verifier ce que l'on a deja
			if ($c==sizeof($tabword)-1) {
				$rs=$db->query("select distinct id,word from dims_keywords where ucase(word) like :word", array(
					':word' => array('type' => PDO::PARAM_STR, 'value' => $word.'%'),
				));
			} else {
				$rs=$db->query("select distinct id,word from dims_keywords where length=:wordlength and ucase(word) like :word", array(
					':wordlength' => array('type' => PDO::PARAM_INT, 'value' => strlen($word)),
					':word' => array('type' => PDO::PARAM_STR, 'value' => $word),
				));
			}

			if ($db->numrows($rs)>0) {
				if ($fields = $db->fetchrow($rs)) {
					if ($c<sizeof($tabword)-1) {
						$tabkey[]=$fields['id'];
						$tabwords[]=$fields['word'];
					}
					elseif ($c==sizeof($tabword)-1	&& $db->numrows($rs)==1) {
						// on verifie si le dernier existe exactement : si oui on ouvre
						$tabkey[]=$fields['id'];
						$tabwords[]=$fields['word'];
						$tabword[]="";
						$predict=true;
					}
				}
			}
		}
		//else $searchword=$word;

		$c++;
	}

$searchword=$tabword[sizeof($tabword)-1];

// verification que tous les mots cle de saisie existent ou non
// si oui :  on testera sur valeur reelle et unique
// si non : on collecte les mots approximatifs, on les utilise pour k2 in (...liste des mots approxi.)

// on va traiter maintenant du tableau des clés en croisant sur correp (k1,k2) et tester les couples 2 a 2
// on recupere les id_sentences du mot courant, on fusionnera apres
$result=array();
require_once DIMS_APP_PATH . '/include/class_timer.php' ;
$lastword=$tabword[sizeof($tabword)-1];
$lastkeyword=$tabkey[sizeof($tabkey)-1];
// execution timer
$dims_timer = new timer();
$dims_timer->start();
$time = round($dims_timer->getexectime(),3);
$timedeb = sprintf("%d",$time*1000);

if (sizeof($tabkey)>0) {
$params = array();
if (sizeof($tabkey)==1) {
	if (!$predict) {
		// verification du cache existant
		$tabkeytemp=array();
		$sql="select distinct id,word from dims_keywords where ucase(word) like :word order by count desc";

		$rs=$db->query($sql, array(':word' => array('type' => PDO::PARAM_STR, 'value' => $lastword.'%')));
		if ($db->numrows($rs)>0) {
			while ($fields = $db->fetchrow($rs)) {
				$tabkeytemp[]=$fields['id'];
			}
		}
		else $tabkeytemp[]="0";
		// on a qu'un mot cle positionné
		//$sql="select distinct id_sentence from dims_keywords_index where
		//				   id_workspace=".$_SESSION['dims']['workspaceid']."
		//				  and id_keyword = ".$tabkey[0];

		// on recherche sur les nouveaux mots approximatifs
		$sql="select distinct id,word,count(k2) as cpte from dims_keywords as k
				inner join `dims_keywords_corresp` as ki on ki.k2=k.id
				and id_workspace in (".$db->getParamsFromArray($workspaces, 'idworkspace', $params).")
				and k1= :keyword1
				and k2 in (".$db->getParamsFromArray($tabkeytemp, 'keyword2', $params).")
				group by id order by cpte desc limit 0,10";

		$params[':keyword'] = array('type' => PDO::PARAM_INT, 'value' => $tabkey[0]);
	}
	else {
		// on recherche sur les nouveaux mots approximatifs
		$sql="select distinct id,word,count(k2) as cpte from dims_keywords as k
				inner join `dims_keywords_corresp` as ki on ki.k2=k.id
				and id_workspace in (".$db->getParamsFromArray($workspaces, 'idworkspace', $params).")
				and k1=:keyword
				group by id order by cpte desc limit 0,10";

		$params[':keyword'] = array('type' => PDO::PARAM_INT, 'value' => $tabkey[0]);
	}
} else {
	// on traite le cas de plusieurs éléments
	for($c=0;$c<sizeof($tabkey)-1;$c++) {
		// Deux cas : si nb motscles <3 on effectue une recherche sur dims_kw_index pr le 1er et approximatif
		$params2 = array();
		$sql="select distinct id_sentence from dims_keywords_corresp where
			id_workspace in (".$db->getParamsFromArray($workspaces, 'idworkspace', $params2).")
			and k1 = :keyword1
			and k2 = :keyword2";

		$params2[':keyword1'] = array('type' => PDO::PARAM_INT, 'value' => $tabkey[$c]);
		$params2[':keyword2'] = array('type' => PDO::PARAM_INT, 'value' => $tabkey[$c+1]);

		$rs=$db->query($sql, $params2);
		$res=array();
		if ($db->numrows($rs)>0) {
			while ($fields = $db->fetchrow($rs)) {
				// on filtre les id_sentences communs ds un premier temps !
				$res[]=$fields['id_sentence'];
			}
		}

		if ($c==0) $result=$res;
		else $result= array_intersect($result,$res) ;

	}

	if (sizeof($result)==0) $result[]=0;

	if (!$predict) {
		$sql="select distinct id,word,count(k2) as cpte from dims_keywords as k
			inner join `dims_keywords_corresp` as ki on ki.k2=k.id
			and id_workspace in (".$db->getParamsFromArray($workspaces, 'idworkspace', $params).")
			and k1= :keyword
			and id_sentence in (".$db->getParamsFromArray($result, 'idsentence', $params).")
			and ucase(word) like :word
			group by id order by cpte desc limit 0,10";

		$params[':keyword'] = array('type' => PDO::PARAM_INT, 'value' => $lastkeyword);
		$params[':word'] = array('type' => PDO::PARAM_STR, 'value' => dims_convertaccents($searchword).'%');
	} else {
		$sql="select distinct id,word,count(k2) as cpte from dims_keywords as k
			inner join `dims_keywords_corresp` as ki on ki.k2=k.id
			and id_workspace in (".$db->getParamsFromArray($workspaces, 'idworkspace', $params).")
			and k1= :keyword
			and id_sentence in (".$db->getParamsFromArray($result, 'idsentence', $params).")
			group by id order by cpte desc limit 0,10";

		$params[':keyword'] = array('type' => PDO::PARAM_INT, 'value' => $lastkeyword);
	}
}

		/************************************************************************************************************/
		// on a peut etre des correspondances pour les premiers mots cles insérés, on regarde ce que l'on peut trouver
		// sur la base
		// calcul des correspondances sur la dernière occurence des mots clés saisis
		// on  utilise une recherche exacte ou éventuellement on levenshteinise !
		/************************************************************************************************************/
		$chres="";

	foreach($tabwords as $word) {
		if ($expression) $word="\"".$word;
		if ($chres!="") $chres.=" ".$word;
		else $chres=$word;
	}

				$rs = $db->query($sql, $params);
				$c=0;
				$continue=true;

				if ($db->numrows($rs)>0) {
						while ($row = $db->fetchrow($rs)) {
								if ($continue) {
				if ($expression) $word="\"".$word;
				if ($c++) echo '|';
				echo "".$chres." ".$row['word']."";

				if ($wordsearch==$chres." ".$row['word']) $continue=false;
								}

						}
				}
				else {
						 if (!$predict) {
								// on levenstein
								$rs=$db->query("select distinct id,word from dims_keywords where left(word,1)=:firstletter AND length(word)>=:wordlength  order by count desc limit 0,50", array(
				':firstletter' => array('type' => PDO::PARAM_STR, 'value' => substr($searchword,0,1)),
				':wordlength' => array('type' => PDO::PARAM_INT, 'value' => strlen($searchword)-2),
			));
								if ($db->numrows($rs)>0) {
										$curpourcent=0;
										$tabresultpercent=array();
										$tabcorresp=array();
										$tabcorrespkey=array();

										while ($fields = $db->fetchrow($rs)) {
												$res= similar_text($searchword, $fields['word'], $percent);
												//$pourcent= levenshtein($wordsearch , $fields['word']);
												if ($percent>50) {
														$tabresultpercent[$fields['word']]=$percent;
														$tabcorresp[$fields['id']]=$fields['word'];
														$tabcorrespkey[]=$fields['id'];
												}
										}
										arsort($tabresultpercent);

				$params = array();
				$params[':k1'] = array('type' => PDO::PARAM_INT, 'value' => $lastkeyword);
										// on recherche sur les nouveaux mots approximatifs
										$sql="select distinct id,word,count(k2) as cpte from dims_keywords as k
												inner join `dims_keywords_corresp` as ki on ki.k2=k.id
												and id_workspace in (".$db->getParamsFromArray($workspaces, 'idworkspace', $params).")
												and k1=".$lastkeyword;

										if (sizeof($result)>0) {
					$sql.=" and id_sentence in (".$db->getParamsFromArray($result, 'idsentence', $params).")";
				}

				if (empty($tabcorrespkey)) {
					$tabcorrespkey[]=0;
				}

										$sql .=" and k2 in (".$db->getParamsFromArray($tabcorrespkey, 'k2', $params).")
												group by id limit 0,10";

										$rs = $db->query($sql, $params);
										$c=0;

										if ($db->numrows($rs)>0) {
												while ($row = $db->fetchrow($rs)) {
														if ($continue) {
																if ($c++) echo '|';
																if ($expression) $row['word']="\"".$row['word'];
																echo "".$chres." ".$row['word']."";
																if ($wordsearch==$chres." ".$row['word']) $continue=false;
														}

												}
										}
								}
						} // !predict
				}
		//}
	}

}
?>
