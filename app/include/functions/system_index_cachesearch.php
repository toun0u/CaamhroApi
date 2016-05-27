<?php

require_once(DIMS_APP_PATH . "/include/functions/string.php");
ini_set('memory_limit','512M');

$workspaces = $dims->getWorkspaces();

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
if (isset($_GET['word'])) $wordindex=dims_load_securvalue('word', dims_const::_DIMS_CHAR_INPUT, true, true, true);
elseif (isset($wordcampaign)) $wordindex=$wordcampaign;

//$word=dims_load_securvalue('word',dims_const::_DIMS_CHAR_INPUT,true,false,true);		// ('field',type=num,get,post,sqlfilter=false)
$key=array_search($wordindex,$_SESSION['dims']['search']['listuniqueword']);

unset($_SESSION['dims']['search']['cacheresult'][$key]);

if ($key>0) {
		// detection d'un ou plusieurs espaces
		// traitement identique que l'indexation
		$wordsearch=dims_convertaccents(html_entity_decode(strip_tags($wordindex)));
		$wordsearch=str_replace (array("+","<","&","=","[","*","{","}","(",")","\\","\r","\n","\t","-","_","]",",","'","\"","`","|","~",":","?","!",";","")," ",$wordsearch);


		$tabword=explode(" ",$wordsearch);
		$tabkey=array();
		$tabwords=array();

		if (sizeof($tabword)==1) {
				$c=0;

				$rs=$db->query('select distinct id,word from dims_keywords where ucase(word) like ?', array($wordsearch));
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
								$rs=$db->query('select distinct id,word from dims_keywords where length = ? and ucase(word) like ?', array(strlen($word), $word));
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
		// on nettoie éventuellement le cache user
		$rs=$db->query("delete from dims_keywords_usercache where id_user = ? and id_exp = ?", array($_SESSION['dims']['userid'], $key));
		//echo "ici".sizeof($tabkey)." ".sizeof($tabword);die();
		if (sizeof($tabkey)>0 && sizeof($tabkey)==sizeof($tabword)) {
				if (sizeof($tabkey)==1) {
						// on a qu'un mot cle positionné
						$params = array();
						$sql="select distinct id_module,id_object,id_record,id_sentence,sum(count) as cpte from dims_keywords_index where
										(id_workspace in (".$db->getParamsFromArray($workspaces, 'work', $params).") or id_module_type=1)
										and id_keyword = :id_keyword
										group by id_module,id_object,id_record,id_sentence order by count desc ";


						$params[':id_keyword'] = $tabkey[0];
						$rs=$db->query($sql, $params);
						$result=array();
						unset($_SESSION['dims']['search']['cacheresult'][$key]);

						if ($db->numrows($rs)>0) {

								while ($fields = $db->fetchrow($rs)) {
										// on filtre les id_sentences communs ds un premier temps !
										$result[]=$fields;
										if (!isset($_SESSION['dims']['search']['cacheresult'][$key][$_SESSION['dims']['workspaceid']][$fields['id_module']][$fields['id_object']][$fields['id_record']])) {
												$_SESSION['dims']['search']['cacheresult'][$key][$_SESSION['dims']['workspaceid']][$fields['id_module']][$fields['id_object']][$fields['id_record']]=$fields['cpte'];
										}
								}
						}
				}
				else {
						// on traite le cas de plusieurs éléments
						for($c=0;$c<sizeof($tabkey)-1;$c++) {
								// Deux cas : si nb motscles <3 on effectue une recherche sur dims_kw_index pr le 1er et approximatif
								$params = array();
								$sql="select distinct id_sentence from dims_keywords_corresp where
										(id_workspace in (".$db->getParamsFromArray($workspaces, 'work', $params).") or id_module_type=1)
										and k1 = :k1
										and k2 = :k2";

								$params[':k1'] = $tabkey[$c];
								$params[':k2'] = $tabkey[$c+1];

								$rs=$db->query($sql, $params);
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

						$params = array();
						$sql="	select	distinct id_module,id_object,id_record,id_sentence,sum(count) as cpte
								from	dims_keywords_index
								where	(id_workspace in (".$db->getParamsFromArray($workspaces, 'work', $params).") or id_module_type=1)
										and id_keyword in (".$db->getParamsFromArray($tabkey, 'key', $params).")
										and id_sentence in (".$db->getParamsFromArray($result, 'sen', $params).")
										group by id_module,id_object,id_record,id_sentence";

						$result=array();
						$rs=$db->query($sql, $params);
						$res=array();
						if ($db->numrows($rs)>0) {
								while ($fields = $db->fetchrow($rs)) {
										// on filtre les id_sentences communs ds un premier temps !
										$result[]=$fields;
										if (!isset($_SESSION['dims']['search']['cacheresult'][$key][$_SESSION['dims']['workspaceid']][$fields['id_module']][$fields['id_object']][$fields['id_record']])) {
												$_SESSION['dims']['search']['cacheresult'][$key][$_SESSION['dims']['workspaceid']][$fields['id_module']][$fields['id_object']][$fields['id_record']]=$fields['cpte'];
										}
								}
						}
				}

				/****************************************************************************************************************/
				/* on  a un ensemble de sentences qu'il faut maintenant répartir dans chaque module								*/
				/****************************************************************************************************************/

				$chres="";

				if (sizeof($result)>0) {

					   $script="INSERT INTO dims_keywords_usercache VALUES ";
					   $i=0;
						$line="";

					   foreach($result as $field) {
								$i++;
								if ($line!="") $line.=",";
								$line.=" (".$_SESSION['dims']['userid'].",".$key.",".$field['id_module'].",".$field['id_object'].",".$field['id_record'].",".$field['id_sentence'].",".$field['cpte'].")";

								if ($i>200) {
										$db->query($script.$line);
										$line="";
										$i=0;
								}
						}

						if (!empty($line)) {
								//echo $script.$line;
								$db->query($script.$line);
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
