<?

require_once(DIMS_APP_PATH . "/include/functions/string.php");
/*
require_once DIMS_APP_PATH . '/include/class_timer.php' ;
// execution timer
$dims_timer = new timer();
$dims_timer->start();
$timedeb = round($dims_timer->getexectime(),3);
$timedeb = sprintf("%d",$time*1000);
*/
// recuperation de la clé
$wordindex="";

// appel soit de get[word] si recherche courante par user ou wordcampaign pour actualisation des campagnes
if (isset($wordcampaign)) $wordindex=$wordcampaign;

//$word=dims_load_securvalue('word',dims_const::_DIMS_CHAR_INPUT,true,false,true);		// ('field',type=num,get,post,sqlfilter=false)
//$key=array_search($wordindex,$_SESSION['dims']['search']['listuniqueword']);
$key=$positionid;
unset($_SESSION['dims']['search']['cacheresult'][$key]);
$_SESSION['dims']['search']['cacheresult'][$key]=array();

if ($key>0) {
		// detection d'un ou plusieurs espaces
		// traitement identique que l'indexation
		$wordsearch=dims_convertaccents(html_entity_decode(strip_tags($wordindex)));
		$wordsearch=str_replace (array("+","<","&","=","[","*","{","}","(",")","\\","\r","\n","\t","-","_","]",".",",","'","\"","`","|","~",":","?","!",";","")," ",$wordsearch);

		$tabword=explode(" ",$wordsearch);
		$tabkey=array();
		$tabwords=array();

		if (sizeof($tabword)==1) {
				$c=0;

				$rs=$db->query("select distinct id,word from dims_keywords where ucase(word) like :word", array(
			':word' => array('type' => PDO::PARAM_STR, 'value' => $word),
		));
				// le mot existe
				if ($db->numrows($rs)>0) {

						while ($fields = $db->fetchrow($rs)) {
								$tabkey[]=$fields['id'];
						}
				}
		}
		else {
				$searchword="";
				$c=0;

				// on traite les phrases : bcp plus difficile
				// 1er : contruction des premiers éléments
				foreach($tabword as $word) {
						if ($c<sizeof($tabword)) {
								// verification du cache deja existant
								// si rempli pas besoin de verifier ce que l'on a deja
								//if (!isset($_SESSION['dims']['search']['querycache'][$c])) {
								$rs=$db->query("select distinct id,word from dims_keywords where length=:wordlength and ucase(word) like :word", array(
					':wordlength' => array('type' => PDO::PARAM_INT, 'value' => strlen($word)),
					':word' => array('type' => PDO::PARAM_STR, 'value' => $word),
				));

								if ($db->numrows($rs)>0) {
										if ($fields = $db->fetchrow($rs)) {
												$tabkey[]=$fields['id'];
												$tabwords[]=$fields['word'];
										}
								}
						}
						else $searchword=$word;

						$c++;
				}
		}

		// calcul de la clé
		//$key= bin2hex(hash('md5', $wordsearch, TRUE));

		$result=array();

		if (sizeof($tabkey)>0 && sizeof($tabkey)==sizeof($tabword)) {
				if (sizeof($tabkey)==1) {
						// on a qu'un mot cle positionné
						$sql="select distinct id_module,id_object,id_record,id_workspace,id_sentence,sum(count) as cpte from dims_keywords_index where
										id_sentence > :idsentence
										and id_keyword = :keyword
										group by id_module,id_object,id_record,id_sentence order by count desc ";

			$rs=$db->query($sql, array(
				':idsentence' => array('type' => PDO::PARAM_INT, 'value' => $firstnewsentence),
				':keyword' => array('type' => PDO::PARAM_INT, 'value' => $tabkey[0]),
			));
						$result=array();
						//echo "\nSql simple :".$sql." :".$db->numrows($rs)."\n";ob_flush();
						if ($db->numrows($rs)>0) {

								while ($fields = $db->fetchrow($rs)) {
										// on filtre les id_sentences communs ds un premier temps !
										$result[]=$fields;
										$_SESSION['dims']['search']['cacheresult'][$key][$fields['id_workspace']][$fields['id_module']][$fields['id_object']][$fields['id_record']]=$fields['cpte'];
								}
						}

				} else {
						// on traite le cas de plusieurs éléments
						for($c=0;$c<sizeof($tabkey)-1;$c++) {
								// Deux cas : si nb motscles <3 on effectue une recherche sur dims_kw_index pr le 1er et approximatif
								$sql="select distinct id_sentence from dims_keywords_corresp where
										id_sentence > :idsentence
										and k1 = :keyword1
										and k2 = :keyword2";

								$rs=$db->query($sql, array(
					':idsentence' => array('type' => PDO::PARAM_INT, 'value' => $firstnewsentence),
					':keyword1' => array('type' => PDO::PARAM_INT, 'value' => $tabkey[$c]),
					':keyword2' => array('type' => PDO::PARAM_INT, 'value' => $tabkey[$c+1]),
				));
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

						if (empty($result)) $result[]=0;

			$params = array();
						$sql="	select	distinct id_module,id_object,id_record,id_workspace,id_sentence,sum(count) as cpte
								from	dims_keywords_index
								where	id_keyword in (".$db->getParamsFromArray($tabkey, 'idkeyword', $params).")
										and id_sentence in (".$db->getParamsFromArray($result, 'idsentence', $params).")
										group by id_module,id_object,id_record,id_sentence";


						$rsz=$db->query($sql, $params);
						$res=array();
						if ($db->numrows($rsz)>0) {
								while ($fields = $db->fetchrow($rsz)) {
										// on filtre les id_sentences communs ds un premier temps !
										$result[]=$fields;
										$_SESSION['dims']['search']['cacheresult'][$key][$fields['id_workspace']][$fields['id_module']][$fields['id_object']][$fields['id_record']]=$fields['cpte'];
								}
						}
				}
		}
/*
$time = round($dims_timer->getexectime(),3);
$time = sprintf("%d",$time*1000);
echo ($time-$timedeb)." ms <br>";
*/
} // fin de la récupération de la clé
?>
