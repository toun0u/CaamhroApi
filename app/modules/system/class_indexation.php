<?php
/*
 * Copyright NETLOR SAS
 * Patrick Nourrissier - 24/05/2010
 */

class indexation {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	private $db;						// connector to database abstraction layer : Mysql, Oracle
	private $error;						// code error
	private $scriptsentence;			// script contains insert sentence
	private $script;					// script contains insert keywords_index
	private $scriptword;				// script contains insert keywords
	private $scriptcorresp;				// script contains insert keywords_corresp
	private $tabwords;					// var contains array words
	private $tabsentences;				// var contains array of sentences
	private $tabwordsmetafield;			// var contains array of metafield
	private $lastinsertsentence;		// index of last insert sentence id
	private $lastinsertword;			// index of last insert word id
	private $cpte;						// nb local words
	private $cpteglobal;				// nb words
	private $cpteline;					// nb of keyword inserted line
	private $cptelinecorresp;			// nb of keyword_corresp inserted line
	private $linecorresp;				// String contains insert keyword_corresp sql code
	private $line;						// String contains insert keyword_index sql code
	private $metadata;					// array contains metadata fields description
	private $checksize;					// size of keywords in memory, depending on total memory of servers
	private	$limit;						// nb of word use, if less than value, and $checksize >, $word is stored in database
	private $execron;					// execute indexing in cron process
	private $privatedeletesentence;		// tableau de suppression de données

	function __construct($dba="",$size=5000000,$limit=2){
		$this->db=$dba;
		$this->error=0;
		$this->script="INSERT INTO dims_keywords_index VALUES ";
		$this->scriptword="INSERT INTO dims_keywords VALUES ";
		$this->scriptwordmeta="INSERT INTO dims_keywords_metafield VALUES ";
		$this->scriptwordmetaphone="INSERT INTO dims_keywords_metaphone VALUES ";
		$this->scriptcorresp="INSERT INTO dims_keywords_corresp VALUES ";
		$this->scriptwordcampaign="UPDATE dims_campaign,dims_campaign_keyword set dims_campaign.state=1 where dims_campaign_keyword.id_campaign=dims_campaign.id and dims_campaign_keyword.key in (";
		$this->scriptsentence="INSERT INTO dims_keywords_sentence VALUES ";
		$this->tabsentences=array();
		$this->tabwords=array();
		$this->tabwordsmetafield=array();
		$this->lastinsertsentence=0;
		$this->lastinsertword=0;
		$this->cpte=0;
		$this->cpteglobal=0;
		$this->cpteline=0;
		$this->cptelinecorresp=0;
		$this->linecorresp="";
		$this->line="";
		$this->metadata=array();
		$this->checksize=$size;
		$this->limit=2;
		$this->execron=true;
		$this->privatedeletesentence=array();
	}

	public function getDb() {
		return($this->db);
	}

	public function setDb($datab) {
		$this->db=$datab;
	}

	public function eraseKeywords() {
		$sql="truncate dims_keywords";
		$res=$this->db->query($sql);
	}

	public function assignMetadata($md){
		$this->metadata = $md;
	}

	/*
	 * function deletes index datas in database
	 */
	private function queryDeleteIndexData($id_record,$id_object,$id_module,$typecontent="") {
		//$tabsentence=array();
		// construction de la liste des sentences
		if ($typecontent!="") {
			$sql="SELECT distinct id_sentence from dims_keywords_index
				inner join dims_keywords_sentence on dims_keywords_index.id_sentence=dims_keywords_sentence.id
				where id_record= :idrecord and id_object= :idobject and id_module= :idmodule ";
		}
		else
			$sql="SELECT distinct id_sentence from dims_keywords_index where id_record= :idrecord and id_object= :idobject and id_module= :idmodule";

		$rs=$this->db->query($sql, array(
			':idrecord' => array('type' => PDO::PARAM_INT, 'value' => $id_record),
			':idobject' => array('type' => PDO::PARAM_INT, 'value' => $id_object),
			':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $id_module),
		));
		if ($this->db->numrows($rs)>0) {

			while ($fields = $this->db->fetchrow($rs)) {
				$this->privatedeletesentence[$fields['id_sentence']]=$fields['id_sentence'];
			}
		}
	}

	/*
	 * function deletes index datas in database
	 */
	private function queryDeleteIndexDataWithGlobalObject($id_globalobject,$typecontent="") {
		//$tabsentence=array();
		// construction de la liste des sentences
		if ($typecontent!="") {
			$sql="SELECT distinct id_sentence from dims_keywords_index
				inner join dims_keywords_sentence on dims_keywords_index.id_sentence=dims_keywords_sentence.id
				where dims_keywords_index.id_globalobject= :idglobalobject ";
		}
		else
			$sql="SELECT distinct id_sentence from dims_keywords_index where id_globalobject= :idglobalobject ";

		$rs=$this->db->query($sql, array(
			':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $id_globalobject),
		));
		if ($this->db->numrows($rs)>0) {

			while ($fields = $this->db->fetchrow($rs)) {
				$this->privatedeletesentence[$fields['id_sentence']]=$fields['id_sentence'];
			}
		}
	}

	private function updateDeleteIndexData() {

		// on met a jour corresp index et sentence
		if (sizeof($this->privatedeletesentence)>0) {
			if (isset($this->execron))	echo sizeof($this->privatedeletesentence)." a supprimer \n";ob_flush();
			$params = array();
			$res=$this->db->query("delete from dims_keywords_index where id_sentence in (".dims_db::getParamsFromArray($this->privatedeletesentence, 'idsentence', $params).")", $params);
			$params = array();
			$res=$this->db->query("DELETE from dims_keywords_corresp where id_sentence in (".dims_db::getParamsFromArray($this->privatedeletesentence, 'idsentence', $params).")", $params);
			$params = array();
			$res=$this->db->query("DELETE from dims_keywords_sentence where id in (".dims_db::getParamsFromArray($this->privatedeletesentence, 'idsentence', $params).")", $params);
		}
	}

	private function queryDeleteData() {
		// delete entries
		$res=$this->db->query("TRUNCATE TABLE dims_keywords_index");
		$res=$this->db->query("TRUNCATE TABLE dims_keywords_metafield");
		$res=$this->db->query("TRUNCATE TABLE dims_keywords_sentence");
		$res=$this->db->query("TRUNCATE TABLE dims_keywords_preindex");
		$res=$this->db->query("TRUNCATE TABLE dims_keywords_corresp");
		$res=$this->db->query("TRUNCATE TABLE dims_keywords_metaphone");
	}

	private function queryDeleteIndex() {
		// suppression des index
		$tabquery=array();
	$res=$this->db->query("SHOW INDEX FROM dims_keywords_corresp WHERE KEY_NAME = 'index2'");
	if ($this->db->numrows($res)>0)
		$tabquery[]="ALTER IGNORE TABLE `dims_keywords_corresp` DROP INDEX `index2`";

	$res=$this->db->query("SHOW INDEX FROM dims_keywords_corresp WHERE KEY_NAME = 'index3'");
	if ($this->db->numrows($res)>0)
		$tabquery[]="ALTER IGNORE TABLE `dims_keywords_corresp` DROP INDEX `index3`";

	$res=$this->db->query("SHOW INDEX FROM dims_keywords_index WHERE KEY_NAME = 'id_keyword'");
	if ($this->db->numrows($res)>0)
		$tabquery[]="ALTER IGNORE TABLE `dims_keywords_index` DROP INDEX `id_keyword`";

	$res=$this->db->query("SHOW INDEX FROM dims_keywords_index WHERE KEY_NAME = 'id_metafield'");
	if ($this->db->numrows($res)>0)
		$tabquery[]="ALTER IGNORE TABLE `dims_keywords_index` DROP INDEX `id_metafield`";

	$res=$this->db->query("SHOW INDEX FROM dims_keywords_index WHERE KEY_NAME = 'id_keyword_2'");
	if ($this->db->numrows($res)>0)
		$tabquery[]="ALTER IGNORE TABLE `dims_keywords_index` DROP INDEX `id_keyword_2`";

	$res=$this->db->query("SHOW INDEX FROM dims_keywords WHERE KEY_NAME = 'index1'");
	if ($this->db->numrows($res)>0)
		$tabquery[]="ALTER IGNORE TABLE `dims_keywords` DROP INDEX `index1`";

	$res=$this->db->query("SHOW INDEX FROM dims_keywords WHERE KEY_NAME = 'index2'");
	if ($this->db->numrows($res)>0)
		$tabquery[]="ALTER IGNORE TABLE `dims_keywords` DROP INDEX `index2`";

	$res=$this->db->query("SHOW INDEX FROM dims_keywords WHERE KEY_NAME = 'stype'");
	if ($this->db->numrows($res)>0)
		$tabquery[]="ALTER IGNORE TABLE `dims_keywords` DROP INDEX `stype`";

	$res=$this->db->query("SHOW INDEX FROM dims_keywords_metafield WHERE KEY_NAME = 'metafield'");
	if ($this->db->numrows($res)>0)
		$tabquery[]="ALTER IGNORE TABLE `dims_keywords_metafield` DROP INDEX `metafield`";

	$res=$this->db->query("SHOW INDEX FROM dims_keywords_metaphone WHERE KEY_NAME = 'metaphone'");
	if ($this->db->numrows($res)>0)
		$tabquery[]="ALTER IGNORE TABLE `dims_keywords_metaphone` DROP INDEX `metaphone`";

	$res=$this->db->query("SHOW INDEX FROM dims_keywords_sentence WHERE KEY_NAME = 'id'");
	if ($this->db->numrows($res)>0)
		$tabquery[]="ALTER IGNORE TABLE `dims_keywords_sentence` DROP INDEX `id`";

		foreach ($tabquery as $query) {
			$res=$this->db->query($query);
		}
	}

	private function queryCreateIndex() {
		$tabquery=array();
		$tabquery[]="ALTER IGNORE TABLE `dims_keywords_corresp` ADD INDEX `index2` ( `id_sentence` )";
		$tabquery[]="ALTER IGNORE TABLE `dims_keywords_corresp` ADD INDEX `index3` ( `id_workspace` , `k1` , `k2` )";
		$tabquery[]="ALTER IGNORE TABLE `dims_keywords_index` ADD INDEX `id_keyword` ( `id_keyword` )";
		$tabquery[]="ALTER IGNORE TABLE `dims_keywords_index` ADD INDEX `id_metafield` ( `id_metafield` )  ";
		$tabquery[]="ALTER IGNORE TABLE `dims_keywords_index` ADD INDEX `id_keyword_2` ( `id_keyword` , `id_metafield` )";
		$tabquery[]="ALTER IGNORE TABLE `dims_keywords` ADD INDEX `index1` ( `word` )";
		$tabquery[]="ALTER IGNORE TABLE `dims_keywords` ADD INDEX `index2`	( `length` , `word` )";
		$tabquery[]="ALTER IGNORE TABLE `dims_keywords` ADD INDEX `stype` ( `stype`,`flascii`,`code`,`metaphone`)";
		$tabquery[]="ALTER IGNORE TABLE `dims_keywords_metafield` ADD INDEX `metafield` ( `id_metafield` )";
		$tabquery[]="ALTER IGNORE TABLE `dims_keywords_sentence` ADD INDEX `id` ( `id` )";

		foreach ($tabquery as $query) {
			$res=$this->db->query($query);
		}
	}

	private function storeTempKeywords() {
		echo "On stocke\n";ob_flush();
		$line='';
		$i=0;
		$tabupdate=array();

		foreach ($this->tabwords as $word =>$newword) {
			// on ajoute en base tous les mots utilis�s que 1 ou 2 fois
			if ($newword[2]<=$limit) {
				if ($newword[1]) { // nouveau mot
					$i++;
					// calcul du soundex
					$sdex=soundex($word);
					$code=substr($sdex,1);
					$meta=metaphone($word);
					$flascii=ord(substr($word,0,1));
					if ($line!='') $line.=",";
					$line.="(\"".$newword[0]."\",\"".$word."\",".strlen($word).",".$newword[2].",\"".$sdex."\",".$code.",".$newword[3].",'".$meta."',".$flascii.")";

					if ($i>200) {
						// on exexute maintenant la mise a jour des campagnes
						$rscript=$this->db->query($this->scriptword.$line);
						$line='';
						$i=0;
					}
				}
				$this->tabwords[$word]=null;
				unset($this->tabwords[$word]);
			}
		}

		//if (isset($this->execron)) echo "Ligne $nblignes sur $totallignes : record:$id_record size :".sizeof($this->tabwords)." sizemeta :".sizeof($this->tabwordsmetafield)." memory :".intVal(memory_get_usage()/1000)."\n";ob_flush();
		if (!empty($line)) $rscript=$this->db->query($this->scriptword.$line);
		unset($line);
		unset($tabupdate);
	}

	/*
	 * Function executes indexing process
	 * Dot not return anything
	 */

	public function executeIndex() {
		require_once DIMS_APP_PATH . '/include/class_timer.php' ;

		// Utilisation d'un lock
		if(file_exists(_DIMS_TEMPORARY_UPLOADING_FOLDER."/index_running")){
			$pid = file_get_contents(_DIMS_TEMPORARY_UPLOADING_FOLDER."/index_running");
			$pids = explode(PHP_EOL, `ps -e | awk '{print $1}'`);
			if(in_array($pid, $pids)){
				echo "\n Indexation deja en cours... \n";ob_flush();
				exit();
			}else{
				echo "\n Indexation mal terminée ... \n";
				unlink(_DIMS_TEMPORARY_UPLOADING_FOLDER."/index_running");
			}
		}
		file_put_contents(_DIMS_TEMPORARY_UPLOADING_FOLDER."/index_running", getmypid());

		// execution timer
		$dims_timer = new timer();
		$dims_timer->start();

		// check for availables keywords, if exists => update, else complete insert
		$res=$this->db->query("select count(id) as cpte from dims_keywords");

		if ($this->db->numrows($res)) {
			if($f=$this->db->fetchrow($res)) {
				$this->cpte=$f['cpte'];
			}
		}

		// nombre de mots entre chaque traitement a atteindre
		$tabsizecompare=$this->checksize;

		//////////////////////////////////////////////////////////////////////////////
		// Test si procedure en live ou en cron table
		//////////////////////////////////////////////////////////////////////////////
		if ($this->cpte==0) {
			//Cyril 19/04/2012 - Refonte Métabase -> récupération de metadata à partir de la classe dims désormais en fait c'est le cronindex qui le file
			//$this->prepareMetaData();
			$dims = dims::getInstance();

			if(empty($this->metadata) ){
				$this->metadata = dims::getInstance()->prepareMetaData();
			}

			$this->queryDeleteData(); // erase data
			$this->queryDeleteIndex();// erase index for fast insert cmd

			$tabsize=0;

			$post_relations = array();#Tableau qui servira à stocker les objets qui méritent d'aller voir les relations

			foreach ($this->metadata as $tablename => $obj) {
				$sql=$obj['sql'];
				$fields=$obj['fields'];
				if ($sql!='') {

					//echo $sql."\n";
					$res=$this->db->query($sql);

					if ($this->db->numrows($res)>0) {// && $this->db->numrows($res)>37000 && $this->db->numrows($res)<38000) {
						$totallignes=intval($this->db->numrows($res));
						$nblignes=0;
						$courvalue=0;
						// boucle sur toutes les lignes de contenus
						while($fieldsfetch=$this->db->fetchrow($res)) {
							$id_workspace=($fieldsfetch['id_workspace']=="" || is_null($fieldsfetch['id_workspace'])) ? 0 : $fieldsfetch['id_workspace'];
							$id_module=($fieldsfetch['id_module']=="" || is_null($fieldsfetch['id_module'])) ? 0 : $fieldsfetch['id_module'];
							$id_user=($fieldsfetch['id_user']=="" || is_null($fieldsfetch['id_user'])) ? 0 : $fieldsfetch['id_user'];
							$id_record=$fieldsfetch[$obj['id_label']];
							$id_object=(empty($fieldsfetch['id_object'])) ? 0 : $fieldsfetch['id_object'];
							$id_module_type=(empty($fieldsfetch['id_module_type'])) ? 0 : $fieldsfetch['id_module_type'];
							$id_globalobject=($fieldsfetch['id_globalobject']=="" || is_null($fieldsfetch['id_globalobject'])) ? 0 : $fieldsfetch['id_globalobject'];
							$nbwords=0;
							$nblignes++;

							if (isset($this->execron)) {
								if (intval(($nblignes/$totallignes)*100)>$courvalue) {
									echo "$tablename: ".$courvalue."/100"."\r";ob_flush();
									$courvalue=intval(($nblignes/$totallignes)*100);
								}
							}
							foreach($fields as $key =>$fieldname) {
								if(strpos($fieldname,".") !== false){
									$ff = explode('.',$fieldname);
									$content=trim($fieldsfetch[$ff[count($ff)-1]]);
								}else
									$content=trim($fieldsfetch[$fieldname]);

								// on recupere id meta du champ
								$id_metafield=$obj['corresp'][$fieldname];

								// test si fichier ou non

								if (substr($content,0,17)=="[dimscontentfile]") {
									$indicators = $this->handle_filecontent($tabsizecompare, $id_metafield, $id_record, $id_object, $id_user, $id_workspace, $id_module, $id_module_type, $id_globalobject, $fieldname, $content);
									if(isset($indicators['cpteglobal'])) $this->cpteglobal += $indicators['cpteglobal'];
									if(isset($indicators['tabsizecompare'])) $tabsizecompare = $indicators['tabsizecompare'];
								}
								else {
									if (_DIMS_ENCODING!="UTF-8" && mb_check_encoding($content,"UTF-8")) $content=utf8_decode($content);
									if ($id_module>0 && $id_module_type>0 && $id_user>=0 && $id_object>0 && $id_record>0 && $id_metafield && strlen($content)>0) {
										$cpteglobal+=$this->indexFields($id_metafield,$id_record,$id_object,$id_user,$id_workspace,$id_module,$id_module_type,$id_globalobject,$fieldname,$content);
									}

									//traitement du volume m�moire important
									if (sizeof($this->tabwords)>$tabsizecompare) {
										$this->storeTempKeywords();
										$tabsize=sizeof($this->tabwords);
										$tabsizecompare=$tabsize+$checksize;
									}
								}
							}
							#Traitement des relations éventuelles
							##- Récupération en session des infos du mbobject courant
							$mbo_details = $dims->getMBObjectFields($id_module_type, $id_object);
							if( ! empty($mbo_details)){
								##- Récupération des relations de la classe associée
								$mb_class = $dims->getMBClassDataFromID($mbo_details['id_class']);
								if(! empty($mb_class) ){
									$relations = $dims->getMBObjectRelationsOn($mb_class['classname']);
									if( ! empty($relations) ){
										#Alors là, on doit récupérer pour chaque relation les meta_fields à indexer pour l'objet courant
										foreach($relations as $id_class_to => $tab){
											foreach($tab as $col_on => $tab2){
												foreach($tab2 as $col_to => $rel){
													if($rel['type'] == mb_object_relation::MB_RELATION_BELONGS_TO && $rel['extended_indexation'] > mb_object_relation::MB_RELATION_NO_INDEX){
														#Alimentation de la structure pour traitement à posteriori (souci d'optimisation)
														$post_relations[$id_module_type][$id_object][$id_class_to][$col_on][$col_to][$rel['extended_indexation']][$id_globalobject] = $id_globalobject;
													}
												}
											}
										}
									}
								}
							}
						} // fin de boucle sur les lignes de la table courante
						#Traitement à posteriori des lignes de relations
						if(!empty($post_relations)){
							foreach($post_relations as $id_module_type => $tab){
								foreach($tab as $id_object => $tab2){
									$mbo_details = $dims->getMBObjectFields($id_module_type, $id_object);
									$mb_class = $dims->getMBClassDataFromID($mbo_details['id_class']);
									foreach($tab2 as $id_class_to => $tab3){
										#Récupération de la classe distante
										$foreign_class = $dims->getMBClassDataFromID($id_class_to);
										foreach($tab3 as $col_on => $tab4){
											foreach($tab4 as $col_to => $tab5){
												foreach($tab5 as $type_rel => $idgos){
													if(!empty($idgos)){
														switch($type_rel){
															case mb_object_relation::MB_RELATION_ON_ME_INDEX:
																#On teste si cette table est dans Metafield sinon ça sert à rien d'aller plus loin
																$remote_fields = $dims->getMetafieldsOf($foreign_class['tablename']);
																if( ! empty($remote_fields)){
																	#construction d'une requête SQL pour éviter de faire trop d'open
																	$params = array();
																	$sql3 = "SELECT remote.*, glob.id_record, current.id_globalobject as cur_iggo
																			 FROM ".$foreign_class['tablename']." remote
																			 INNER JOIN ".$mb_class['tablename']." current ON current.".$col_on." = remote.".$col_to."
																			 INNER JOIN dims_globalobject glob ON glob.id = current.id_globalobject
																			 WHERE current.id_globalobject IN (".$this->db->getParamsFromArray($idgos, 'idglobalobject', $params).")";

																	$res3 = $this->db->query($sql3, $params);
																	while($f = $this->db->fetchrow($res3)){
																		foreach($remote_fields as $nom){
																			if(!empty($f[$nom])){
																				$content=$f[$nom]; //on va s'assurer que le champ contient bien des données
																				if ($content!='') {
																					if(substr($content,0,17)=="[dimscontentfile]"){
																						$indicators = $this->handle_filecontent($tabsizecompare, $dims->getMetaFieldID($foreign_class['tablename'], $nom), $f['id_record'], $id_object, $f['id_user'], $f['id_workspace'], $f['id_module'], $id_module_type, $f['cur_iggo'], $nom, $content);
																						if(isset($indicators['cpteglobal'])) $cpteglobal += $indicators['cpteglobal'];
																					}
																					else{
																						$this->cpteglobal+=$this->indexFields($dims->getMetaFieldID($foreign_class['tablename'], $nom),$f['id_record'],$id_object,$f['id_user'],$f['id_workspace'],$f['id_module'],$id_module_type, $f['cur_iggo'],$nom,$content);
																					}
																				}
																			}
																		}
																	}
																}
																break;
															case mb_object_relation::MB_RELATION_ON_REMOTE_INDEX:
																#On teste si cette table est dans Metafield sinon ça sert à rien d'aller plus loin
																$my_fields = $dims->getMetafieldsOf($mb_class['tablename']);
																if( ! empty($my_fields)){
																	#construction d'une requête SQL pour éviter de faire trop d'open
																	$params = array();
																	$sql3 = "SELECT current.*, glob.id as cur_idgo, glob.id_object, glob.id_module_type, glob.id_record
																			 FROM ".$mb_class['tablename']." current
																			 INNER JOIN ".$foreign_class['tablename']." remote ON current.".$col_on." = remote.".$col_to."
																			 INNER JOIN dims_globalobject glob ON glob.id = remote.id_globalobject
																			 WHERE current.id_globalobject IN (".$this->db->getParamsFromArray($idgos, 'idglobalobject', $params).")";

																	$res3 = $this->db->query($sql3, $params);
																	while($f = $this->db->fetchrow($res3)){
																		foreach($my_fields as $nom){
																			if(!empty($f[$nom])){
																				$content=$f[$nom]; //on va s'assurer que le champ contient bien des données
																				if ($content!='') {
																					if(substr($content,0,17)=="[dimscontentfile]"){
																						$indicators = $this->handle_filecontent($tabsizecompare, $dims->getMetaFieldID($mb_class['tablename'], $nom),$f['id_record'],$f['id_object'],$f['id_user'],$f['id_workspace'],$f['id_module'],$f['id_module_type'],$f['cur_idgo'],$nom,$content);
																						if(isset($indicators['cpteglobal'])) $cpteglobal += $indicators['cpteglobal'];
																					}
																					else{
																						$this->cpteglobal+=$this->indexFields($dims->getMetaFieldID($mb_class['tablename'], $nom),$f['id_record'],$f['id_object'],$f['id_user'],$f['id_workspace'],$f['id_module'],$f['id_module_type'],$f['cur_idgo'],$nom,$content);
																					}
																				}
																			}
																		}
																	}
																}
																break;
														}
													}
												}
											}
										}
									}
								}
							}
							unset($post_relations);
						}
						echo "$tablename: 100/100"."\n";ob_flush();
					}
				}
			}
		}
		else {
			//
			// on indexe par appel de la cron
			// on va traiter ce qu'il y a dans la table preindex
			// on charge la table courante des mots cles enregistres
			// on va test si présence d'un fichier temporaire

			// on met à jour la tables des mots en y insérant les nouveaux mots
			$res=$this->db->query("select * from dims_keywords_preindex");
			// on traite l'ensemble des lignes enregistrees
			if ($this->db->numrows($res)) {
				$res=$this->db->query("select * from dims_keywords");

				while($wo=$this->db->fetchrow($res)) {
					$word[0]=$wo['id'];
					$word[1]=false;
					$word[2]=$wo['count'];
					$word[3]=$wo['stype'];
					$this->tabwords[$wo['word']]=$word;
				}

				// on compte le nbre de phrases
				//$this->lastinsertword=sizeof($this->tabwords);
				// on compte le nbre de sentences pr commencer le nouveau travail
				$res=$this->db->query("select max(id) as cpte from dims_keywords");

				if($f=$this->db->fetchrow($res)) {
					$this->lastinsertword=$f['cpte'];
				}

				// on compte le nbre de sentences pr commencer le nouveau travail
				$res=$this->db->query("select max(id) as cpte from dims_keywords_sentence");

				if($f=$this->db->fetchrow($res)) {
					$this->lastinsertsentence=$f['cpte'];
				}
				else {
					$this->lastinsertsentence=0;
				}

				$this->lastinsertsentence+=1;
				// sauvegarde du premier indice de phrase pour post traitement des campagnes
				$firstnewsentence=$this->lastinsertsentence;

				// on cree la structure pour le keywords_metafield
				$res=$this->db->query("select * from dims_keywords_metafield");

				while($f=$this->db->fetchrow($res)) {
					$this->tabwordsmetafield[$f['id_keyword']][$f['id_metafield']]=false;
				}

				$time = round($dims_timer->getexectime(),3);
				$time = sprintf("%d",$time*1000);
				if (isset($this->execron)) echo "\n Chargement du dictionnaire de ".sizeof($this->tabwords)." mots et ".$this->lastinsertsentence." phrases en ".($time-$timedeb)." ms \n";ob_flush();

				$oldrecord=0;
				$oldobject=0;
				$oldmodule=0;
				$oldgo=0;
				$res=$this->db->query("select * from dims_keywords_preindex p order by id_module,id_object,id_record");

				while($f=$this->db->fetchrow($res)) {
					//$fcpte=$f['cpte'];
					$id_workspace=$f['id_workspace'];
					$id_module=$f['id_module'];
					$id_module_type=$f['id_module_type'];
					$id_user=($f['id_user']=="" || is_null($f['id_user'])) ? 0 : $f['id_user'];
					$id_record=$f['id_record'];
					$id_object=$f['id_object'];
					$id_globalobject=($f['id_globalobject']=="" || is_null($f['id_globalobject'])) ? 0 : $f['id_globalobject'];
					$fieldname=$f['typecontent'];
					$content=trim($f['content']);
					$id_metafield=0;

					// on recupere id meta du champ
					$id_metafield=$f['id_metafield'];

					//if ($oldrecord!=$id_record || $oldmodule!=$id_module || $oldobject!=$id_object) {
					if ($oldgo!=$id_globalobject) {
						if (isset($this->execron)) echo "\n Suppression de ".$id_record." sur ".$id_oject." ".$id_module."\n";ob_flush();
						$this->queryDeleteIndexDataWithGlobalObject($id_globalobject,$fieldname);
						$oldgo=$id_globalobject;
					}
					if (substr($content,0,17)=="[dimscontentfile]") {
						$indicators = $this->handle_filecontent($tabsizecompare, $id_metafield,$id_record,$id_object,$id_user,$id_workspace,$id_module,$id_module_type,$id_globalobject,$fieldname,$content);
						if(isset($indicators['cpteglobal'])) $this->cpteglobal += $indicators['cpteglobal'];
						if(isset($indicators['tabsizecompare'])) $tabsizecompare = $indicators['tabsizecompare'];

						$time = round($dims_timer->getexectime(),3);
						$time = sprintf("%d",$time*1000);
						if (isset($this->execron)) echo ($time-$timec)." ms \n";ob_flush();
					}
					// traitement de la ligne courante
					if (strlen($content)>0)  {
						$this->cpteglobal+=$this->indexFields($id_metafield,$id_record,$id_object,$id_user,$id_workspace,$id_module,$id_module_type,$id_globalobject,$fieldname,$content);
					}

					// traitement du volume m�moire important
					if (sizeof($this->tabwords)>$tabsizecompare) {
						$this->storeTempKeywords();
						$tabsize=sizeof($this->tabwords);
						$tabsizecompare=$tabsize+$checksize;
					}

					// suppression de la ligne courante
					$resu=$this->db->query("DELETE from dims_keywords_preindex where id_record= :idrecord and id_object= :idobject and id_module= :idmodule and typecontent = :fieldname", array(
						':idrecord' => array('type' => PDO::PARAM_INT, 'value' => $id_record),
						':idobject' => array('type' => PDO::PARAM_INT, 'value' => $id_object),
						':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $id_module),
						':fieldname' => array('type' => PDO::PARAM_STR, 'value' => $fieldname),
					));
				}
			}
		}

		// on met a jour la table index et corresp
		// insert rest of $line ans $this->linecorresp
		if (!empty($this->line)) $res=$this->db->query($script.$this->line);

		//echo $this->scriptcorresp.$this->linecorresp."\n";ob_flush();die();
		if (!empty($this->linecorresp)) {
			$res=$this->db->query($this->scriptcorresp.$this->linecorresp);
		}

		// on enregistre les acquisitions
		unset($this->line);
		unset($this->linecorresp);

		$time = round($dims_timer->getexectime(),3);
		$time = sprintf("%d",$time*1000);
		if (isset($this->execron)) echo ($time-$timedeb)." ms \nOn traite les mots cles : ";ob_flush();
		$line=array();
		$lineword=array();
		$i=0;
		$j=0;
		$nbmots=sizeof($this->tabwords);

		// on insert maintenant les mots cles
		foreach ($this->tabwords as $word =>$newword) {
			// on ajoute ceux que l'on n'avait pas en v�rifiant
			if ($newword[1]) {
				$i++;
				// calcul du soundex
				$sdex=soundex($word);
				$code=substr($sdex,1);
				$meta=metaphone($word);
				$flascii=ord(substr($word,0,1));
				array_push($line,"(\"".$newword[0]."\",".$this->db->getPdo()->quote($word).",".strlen($word).",".$newword[2].",\"".$sdex."\",".$code.",".$newword[3].",'".$meta."',".$flascii.")");

				if ($i>300) {
					$res=$this->db->query($this->scriptword.implode(",",$line));
					// on exexute maintenant la mise a jour des campagnes
					unset($line);
					unset($lineword);
					$line=array();
					$lineword=array();
					$i=0;
				}
			}
			else {
				// update du nombre
				$this->db->query("UPDATE dims_keywords set count= :count where id= :idkeywords ", array(
					':count' => array('type' => PDO::PARAM_INT, 'value' => $newword[2]),
					':idkeywords' => array('type' => PDO::PARAM_INT, 'value' => $newword[0]),
				));
			}
		}

		if (!empty($line)) {
			$res=$this->db->query($this->scriptword.implode(",",$line));
		}
		unset($line);

		$time = round($dims_timer->getexectime(),3);
		$time = sprintf("%d",$time*1000);
		if (isset($this->execron)) echo ($time-$timedeb)." ms fini \nOn traite maintenant les doublons\n";ob_flush();

		$this->db->query("DROP TABLE IF EXISTS `dims_keywords_temp`;");
		$this->db->query("CREATE TABLE `dims_keywords_temp` (`key_from` INT( 11 ) NOT NULL ,`key_to` INT( 11 ) NOT NULL,count INT(11) NOT NULL DEFAULT '0' ) ENGINE = MYISAM ;");

		$res=$this->db->query("SELECT id,word,count from dims_keywords order by id");
		$matrix=array();
		$matrixdelete=array();
		$line=array();
		$i=0;
		$c=0;

		if ($this->db->numrows($res)>0) {
			while ($kw=$this->db->fetchrow($res)) {
				$c++;
				if (isset($matrix[$kw['word']])) {
					$i++;
					// on a un doublon (au moins)
					array_push($line,"(".$kw['id'].",".$matrix[$kw['word']].",".$kw['count'].")");
					$matrixdelete[]=$kw['id'];

					if ($i>100) {
						$this->db->query("INSERT into dims_keywords_temp values ".implode(",",$line));
						unset($line);
						$line=array();
						$i=0;
					}
				}
				else $matrix[$kw['word']]=$kw['id'];
			}
		}
		unset($line);

		$this->db->query(" ALTER TABLE `dims_keywords_temp` ADD INDEX (`key_from`)");
		$this->db->query(" ALTER TABLE `dims_keywords_temp` ADD INDEX (`key_to`)");

		// update des tables li�es aux correspondances
		$sql="update dims_keywords_index, dims_keywords_temp
				set dims_keywords_index.id_keyword =dims_keywords_temp.key_to where dims_keywords_index.id_keyword =dims_keywords_temp.key_from";
		$this->db->query($sql);

		// update count word
		$sql="update dims_keywords, dims_keywords_temp
				set dims_keywords.count=dims_keywords.count+dims_keywords_temp.count where dims_keywords.id =dims_keywords_temp.key_to";
		$this->db->query($sql);

		// update corresp : k1 et k2
		$sql="update dims_keywords_corresp, dims_keywords_temp
				set dims_keywords_corresp.k1 =dims_keywords_temp.key_to where dims_keywords_corresp.k1 =dims_keywords_temp.key_from";
		$this->db->query($sql);

		$sql="update dims_keywords_corresp, dims_keywords_temp
				set dims_keywords_corresp.k2 =dims_keywords_temp.key_to where dims_keywords_corresp.k2 =dims_keywords_temp.key_from";
		$this->db->query($sql);

		// suppressions des doublons
		$sql="delete from dims_keywords where id in (select key_from from dims_keywords_temp)";
		$this->db->query($sql);

		if (isset($this->execron)) echo ($time-$timedeb)." ms \nOn traite les mots cles metafield:\n";ob_flush();
		$line=array();
		$i=0;
		$j=0;

		// on insert maintenant les mots cl�s
		foreach ($this->tabwordsmetafield as $key =>$elem) {
			foreach ($elem as $metafield => $value)
			if ($value) {
				$i++;
				// verification deje existant
				//$res=$this->db->query("select id from dims_keywords where id='".$newword['id']."'");
				//if ($this->db->numrows($res)==0) {
				array_push($line,"(".$key.",".$metafield.")");
				//}
				if ($i>1000) {
					$res=$this->db->query($this->scriptwordmeta.implode(",",$line));
					// on exexute maintenant la mise � jour des campagnes
					//$this->db->query($this->scriptwordcampaign.implode(",",$lineword).")");
					unset($line);
					unset($lineword);
					$line=array();
					$lineword=array();
					$i=0;
				}
			}
		}

		if (!empty($line)) {
			$res=$this->db->query($this->scriptwordmeta.implode(",",$line));
		}
		unset($line);

		if (isset($this->execron))	echo "$nbmots mots indexes et $this->cpteglobal correspondances trouvees \n";ob_flush();

		if ($this->cpte==0) {
			if (isset($this->execron))	echo "Creation des index \n";ob_flush();
			// execute Create indexes
			$this->queryCreateIndex();

		}

		if (isset($this->execron))	echo "Suppression des anciens indexes \n";ob_flush();
		$this->updateDeleteIndexData();

		if (isset($this->execron))	echo "Optimisation des tables \n";ob_flush();

		$this->db->query(" OPTIMIZE TABLE `dims_keywords` , `dims_keywords_index` , `dims_keywords_preindex` , `dims_keywords_corresp`;");
		if(file_exists(_DIMS_TEMPORARY_UPLOADING_FOLDER."/index_running")) unlink(_DIMS_TEMPORARY_UPLOADING_FOLDER."/index_running");

		if (isset($this->execron))	echo "...termine\n";ob_flush();
	}

	public function handle_filecontent($tabsizecompare, $id_metafield,$id_record,$id_object,$id_user,$id_workspace,$id_module,$id_module_type,$id_globalobject,$fieldname,$content){
		// conversion du fileindex par le path courant
		$fileindex=substr($content,17);
		$indicators = array();
		if (!file_exists($fileindex)) {
			$pos=strpos($fileindex,"/data/doc");
			$fileindex=realpath('.').substr($fileindex,$pos);
		}

		if (file_exists($fileindex)) {
			$indicators['cpteglobal'] = 0;
			$content="";
			$total=filesize($fileindex);
			$fh = fopen($fileindex, "r");
			while (!feof($fh)) {
				$content = fgets($fh);
				$len=strlen($content);
				if ($len>3000000) {
					$tot+=$len;
					$vartot=$tot;
					$pourcent = ($tot*100)/$total;

					if (_DIMS_ENCODING!="UTF-8" && mb_check_encoding($content,"UTF-8")) $content=utf8_decode($content);

					if ($id_module>0 && $id_module_type>0 && $id_user>=0 && $id_object>0 && $id_record>0 && $id_metafield && strlen($content)>0) {
						$indicators['cpteglobal'] += $this->indexFields($id_metafield,$id_record,$id_object,$id_user,$id_workspace,$id_module,$id_module_type,$id_globalobject,$fieldname,$content);
					}

					printf("%d %0.2f\n",$total,$pourcent);ob_flush();

					$vartot=0;
					$content="";
					// traitement du volume m�moire important
					if (sizeof($this->tabwords)>$tabsizecompare) {
						$this->storeTempKeywords();
						$tabsize=sizeof($this->tabwords);
						$indicators['tabsizecompare']=$tabsize+$this->checksize;
						//if (!$mustcheckbase) $mustcheckbase=true;
					}
				} // fin du while

			}// fin du test si fichier existe
		}
		// traitement de la ligne courante
		if (strlen($content)>0 && $id_metafield>0)	{
			$indicators['cpteglobal']+=$this->indexFields($id_metafield,$id_record,$id_object,$id_user,$id_workspace,$id_module,$id_module_type,$id_globalobject,$fieldname,$content);
		}
		fclose($fh);
		return $indicators;
	}

	public function indexFields($id_metafield,$id_record,$id_object,$id_user,$id_workspace,$id_module,$id_module_type,$id_globalobject,$typecontent,$content) {
		$prefix=",$id_record,$id_object,$id_user,$id_workspace,$id_module,$id_module_type,$id_globalobject,";
		$mustinsert=false;
		// Premier niveau de conversion : tags, html, accents
		$content=strtolower(dims_convertaccents(strip_tags(html_entity_decode(($content)))));
		//$content=strtolower(dims_convertaccents(strip_tags(($content))));
		$len=strlen($content);
		$word="";
		$wc=0; // nb de car courant
		$nbwords=0;
		$cpteglobal=0;
		$wordcour="";
		$wordouble="";
		$key="";
		$sentencecontent="";
		$email=false;
		$issentence=false;
		$isword=false;
		$cour=0;
		$idparag=1;
		$cptepoint=0;
		$anca=0;
		$a=0;
		$linesentence="";
		$tabwordscur=array();
		$type=0;

		//$content='sendmail consortium\'s restricted shell (smrsh) in sendmail 8.12.6, 8.11.6-15, and possibly other versions after 8.11 from 5/19/1998, allows attackers to bypass the intended restrictions of smrsh by inserting additional commands after (1) "||" sequences or (2) "/" characters, which are not properly filtered or verified.';
		// pretraitement pour gérer les '-'
		$len=strlen($content);
		//echo "$content\n";

		for($i=0;$i<=$len;$i++) {
			if ($i==$len) $car="\n";
			else $car=$content[$i];

			$anca=$a;
			$a=ord($car);

			if ($a>=48 && $a <=57 || $a>=97 && $a <=122 || $a==64 || $a==38) {
				// digits or caracteres
				$word.=$car;
				$wc++;
				//echo $word;
			}
			else {
				// cas sp�cifique : \r \n ! ?
				if ($a==10 || $a==13 || $a==33 || $a==63) {
					//if ($nbwords>0) {
					$issentence=true;
					$isword=true;
					$type=0;
					if (is_numeric($word)) {
						$type=1;
						//echo "\nInt : ".$word;ob_flush();
					}
					//else echo "\nTexte : ".$word;ob_flush();
					//}
				}
				/***************************************************************/
				elseif ($a==46 || ($a==32 && $wc>0 && is_numeric($word))) {// gestion du point ou espace pour telephone
					if ($wc>0 && $anca!=46 && $anca!=32) { // on verifie que l'on a bien un mot en cour, sinon on coupe
						// on pr�serve les points uniquement pour verifier le mot complet
						// verifions si pas un email
						$j=$i+1;
						$ssword=substr($content,$cour,$i-$cour);
						if ($a==46) {
							$cptepoint=1;
							$aspoint=true;
							$asspace=false;
						}
						else {
							$cptespace=0;
							$aspoint=false;
							$asspace=true;
						}

						$continue=true;
						$email=(strpos($ssword,"@")>0);
						$tabword=array();
						$tabword[0]=$ssword;
						$courw="";

						$isnum=is_numeric($ssword);

						while ($j<$len && $continue) {
							$a2=ord($content[$j]);
							if ($a2==64) {
								$email=true;
								$isnum=false;
							}
							else {
								$continue=($a2>=48 && $a2 <=57 || $a2>=97 && $a2 <=122 || $a2==46 || $a2==32 || $a2==38);
								if ($continue) {
									if ($a2==46 || $a2==32) {
										if ($a2==46) { // on gere le point
											$cptepoint++; // on a plusieurs points
											if ($cptepoint==1) {
												$tabword[]=$courw;
												$courw="";
											}
											// test si deja un espace, on arrete
											if ($asspace) {
												$continue=false; // on ne peut avoir des . et espaces en meme temps
												$isnum=false;
											}
										}
										else { // on gere l'espace
											if ($aspoint) {
												$continue=false;// on ne peut avoir des . et espaces en meme temps
												if (is_numeric($courw)) $tabword[]=$courw;
												if (sizeof($tabword)<5) $isnum=false;
											}
											elseif ($email) {
												$continue=false;
												$isnum=false;
											}
											else {
												$cptespace++;
												if ($cptespace==1) { // on complete la structure pour construire un numero de tel
													$tabword[]=$courw;
													$isnum=$isnum && is_numeric($courw);
													// on a un tel on arrete
													if (sizeof($tabword)==5 && $isnum) $continue=false;
													$courw="";
												}
												else {
													// on arrete avec les espaces ce n'est pas un tel
													$isnum=false;
													$continue=false;
												}
											}
										}
									}
									elseif ($cptepoint>1) {
										$continue=false; // on a repris un nouveau mot
										$isnum=false;
										//$j-=$cptepoint;
									}
									else {
										$cptespace=0;
										$cptepoint=0; // on a autre chose
										$courw.=$content[$j];
									}
								}

							}
							if ($continue) $j++;
						}
						if ($j==$len && $courw!="") {
							$isnum=$isnum && is_numeric($courw);
							$tabword[]=$courw;
						}

						if ($isnum && sizeof($tabword)==5) { // test si num�ro de telephone ok
							$ssword=substr($content,$cour,$j-$cour);
							$isword=true;
							$i=$j-1;
							$anca=ord($content[$i]);
							$word=$ssword;
							$type=4; // tel
							//echo "\nTel : ".$word;ob_flush();
						}
						else {
							//echo substr($content,$cour,$j-$cour). " ".$cptepoint."\n";ob_flush();
							$ssword=substr($content,$cour,$j-$cour-$cptepoint);

							// verifions si on a un email ou num�rique
							if ($email || is_numeric($ssword)) {
								$isword=true;
								$i=$j-1;
								$anca=ord($content[$i]);
								$word=$ssword;
								if ($email) {
									$type=3;
									//echo "\nEmail : ".$word;ob_flush();
								}
								else {
									if (strpos($word,".")>0) {
										// a t on qq chose apres le point, si non alors point comme gin de phrase
										$type=2;
										//echo "\nFloat : ".$word;ob_flush();
									}
									else {
										$type=1;
										//echo "\nInt : ".$word;ob_flush();
									}

								}
							}
							else {
								// on fait le mot
								$isword=true;
								//$i+=strlen($word);
								if (isset($content[$i])) $anca=ord($content[$i]);
								$issentence=true;

								if (is_numeric($word)) {
									if ($cptepoint>0) $type=2;
									else $type=1;
								}
								else $type=0;
								/*if ($type==0) {echo "\nTexte ".$word;ob_flush();}
								else {
									if ($type==2) {echo "\nFloat : ".$word;ob_flush();}
									else {echo "\nInt $type ".$word;ob_flush();}
								}*/
							}// fin du cas autre que email ou nombre
						} // fin de test sur numero de tel

					}
				}
				elseif ($a==44) {// virgule, gestion des nombres
					$ssword=substr($content,$cour,$i-$cour);
					if ($wc>0 && is_numeric($ssword)) {
						// on check la deuxi�me partie
						$j=$i+1;
						$continue=true;
						while ($j<$len && $continue) {
							$c2=$content[$j];
							$a2=ord($c2);
							if ($a2==64) {
								$email=true;
							}
							else {
								$continue=($a2>=48 && $a2 <=57);
								if ($continue) $ssword.=$c2;
							}
							if ($continue) $j++;
						}
						// on a un chiffre de type 2343,34
						if (is_numeric($ssword)) {
							if ($j>($i+1)) {
								$word=substr($content,$cour,$j-$cour);
								$isword=true;
								$type=2; // float
								$i=$j-1;
								//echo "\nFloat ".$word;ob_flush();
							}
							else {
								// on a un entier, la virgule etait une separation standard
								$isword=true;
								$type=1;
								//echo "\nNum ".$word;ob_flush();
							}
						}
					}
					else {
						$type=0;
						$isword=true;
					}
				}
				/***************************************************************/
				elseif (($a==47 || $a==45) && $wc>0 && is_numeric($word) && strlen($word)<=4) {// gestion du / pour les dates

					$is4len=strlen($word)==4;
					$j=$i+1;
					$continue=true;
					$cpteslash=1;
					$courw="";
					$tabword=array();
					$tabword[0]=substr($content,$cour,$i-$cour);
					$nbslash=1;

					// on avance pour analyse de la suite
					while ($j<$len && $continue) {
						$a2=ord($content[$j]);

						$continue=($a2>=48 && $a2 <=57 || $a2>=97 && $a2 <=122 || $a2==47 || $a2==45); // 45 => -
						if ($continue) {
							if ($a2==47 || $a2==45) {
								$nbslash++;

								if ($cpteslash>0) $continue=false;
								else $cpteslash=1;

								// test si entier
								if (is_numeric($courw)) {
									// on a un entier, v�rifions si pas deja eu un chiffre de 4 num.
									if (strlen($courw)==4) {
										if (!$is4len) $is4len=true;
										else $continue=false; // on ne peut avoir 2 series de 4 chiffres
									}
									elseif (strlen($courw)>4) $continue=false;

									if ($continue) {// on enregistre
										$tabword[]=$courw;
										$courw="";
									}
								}
								else {
									$continue=false;
								}
								if ($nbslash==3) {
									$continue=false;
								}
							}
							else {
								$cpteslash=0;
								$courw.=$content[$j];
							}

							if ($continue) $j++; // on avance
						}
						else {
							// on sort avec un dernier bloc
							if (is_numeric($courw)) {
								// on a un entier, v�rifions si pas deja eu un chiffre de 4 num.
								if (strlen($courw)==4) {
									if (!$is4len) $tabword[]=$courw;
								}
								else $tabword[]=$courw;
							}
							else {
								$continue=false;
							}
						}
					}

					if ($j==$len && $courw!="") {
							//$isnum=$isnum && is_numeric($courw);
							$tabword[]=$courw;
					}

					// on examine le tableau $taword
					if (sizeof($tabword)==3) {
						$ssword=substr($content,$cour,$j-$cour);
						$isword=true;
						$i=$j-1;
						$anca=ord($content[$i]);
						$word=$ssword;
						$type=5; // date
						//echo "\nDate : ".$word;ob_flush();
						$courw="";
					}
					else {
						$isword=true;
						$type=0;
						$courw="";
						//echo "\nTexte : ".$word;ob_flush();
					}
				}
				else {
					if ($a==45) {
						$isword=true;
						$type=0;

						// test si on a un entier eventuellement
						if (is_numeric($word)) {
							$type=1;
							//echo "\nInt : ".$word;ob_flush();
						}
					}
					if ($wc>0) {
						$isword=true;
						$type=0;

						// test si on a un entier eventuellement
						if (is_numeric($word)) {
							$type=1;
							//echo "\nInt : ".$word;ob_flush();
						}
						//else echo "\nTexte : ".$word;ob_flush();
					}
				}
			}

			/********************************************************************************/
			/* Traitement du mot cle a inserer												*/
			/********************************************************************************/
			if ($isword) {
				$arraywords=array();

				if (($a==45 || $a==47) && $type!=5 ) { // on traite les mots avec des - pour enregistrer avec et sans
					$continue=true;
					// on recule pour controle du '-' eventuel
					//echo "On est ici avec $i $word \n";

					$i= $i - strlen($word) -1;

					ob_flush();
					$j=$i+1;
					$itemp=$i;

					// on avance pour analyse de la suite
					while ($j<$len && $continue) {
						$a2=ord($content[$j]);

						$continue=($a2>=48 && $a2 <=126 || $a2==45 || $a2==47); // 45 => -
						if ($continue) {
							if ($a2==45 || $a2==47) {
								// on garde le sous mot
								$word=substr($content,$itemp+1,$j-$itemp-1);
								$type=0;
								$itemp=$j;
								//echo "Mot trouve : ".$word."\n";
								// test si on a un entier eventuellement
								if (is_numeric($word)) {
									$type=1;
								}

								// construction de l'element pour le mot simple
								$elem=array();
								$elem['word']=$word;
								$elem['type']=$type;
								$elem['sentence']=false;
								//affectation
								$arraywords[]=$elem;
							}
						}
						else {
							// FIXME: Hardcoded 33 & 47 values, meanings ?
							$continue = ($a2 >= 33 && $a2 <= 47);//Ex : si on ne met pas ça et qu'on a la chaîne ENGRAIS PERLESGERAN &FL-CART.12X800gr, ça boucle à l'infini
						}

						if ($continue) $j++; // on avance
					}

					// on regarde si on a un dernier mot
					if ($j>$i+1 ) {
						// on garde le sous mot
						$word=substr($content,$itemp+1,$j-$itemp-1);
						$type=0;
						//echo "Mot trouve : ".$word."\n";
						// test si on a un entier eventuellement
						if (is_numeric($word)) {
							$type=1;
						}

						// construction de l'element pour le mot simple
						$elem=array();
						$elem['word']=$word;
						$elem['type']=$type;
						$elem['sentence']=false;
						//affectation
						$arraywords[]=$elem;

						// test si on a eu un mot entier
						if ($itemp>$i) {
							// on a eu des '-', on enregistre aussi le mot complet
							$word=substr($content,$i+1,$j-$i-1);
							$type=0;
							// construction de l'element pour le mot simple
							$elem=array();
							$elem['word']=$word;
							$elem['type']=$type;
							$elem['sentence']=true;
							//affectation
							$arraywords[]=$elem;
						}

						// on decale pour ne pas a avoir a réindexer le tt
						$i=$j-1;
					}

					// on regarde le tableau final
					//dims_print_r($arraywords);
					//die();
				}
				else {
					// construction de l'element pour le mot simple
					$elem=array();
					$elem['word']=$word;
					$elem['type']=$type;
					$elem['sentence']=true;

					//affectation
					$arraywords[]=$elem;
				}

				foreach ($arraywords as $elemword) {
					$word=$elemword['word'];
					$type=$elemword['type'];

					$word=str_replace('"','',$word);
					$word=trim($word);

					if ($elemword['sentence'])
						$sentencecontent.=" ".$word; // construction de la phrase

					$lenword=strlen($word);

					if ($word!="" && $lenword>=1 && $lenword<=64) {

						// test si existe ou non
						$keyprec=$key;

						if (isset($this->tabwords[$word])) {
							$key=$this->tabwords[$word][0];
							if ($this->tabwords[$word][2]<1000) $this->tabwords[$word][2]++;
						}
						else {
							$this->lastinsertword++;
							$key=$this->lastinsertword;
							$newword=array();
							$newword[0]=$key;
							$newword[1]=true; // on parcoura plus tard ceux ayant le flag a true
							$newword[2]=1;
							$newword[3]=$type;

							$this->tabwords[$word]=$newword;
							unset($newword);
							$newword=null;
						}

						// traitement du couple keyword <=> metafield
						if (!isset($this->tabwordsmetafield[$key][$id_metafield])) {
							$this->tabwordsmetafield[$key][$id_metafield]=true;
						}

						// traitement principalement pour maj des tables index et corresp
						if ($wordcour!="") {
							// on met a jour la liaison entre le mot courant et precedent
							// on a tjs la key du mot precedent
							if (!isset($tabwordscur[$keyprec]['corresp'][$key])) $tabwordscur[$keyprec]['corresp'][$key]=1;
							else $tabwordscur[$keyprec]['corresp'][$key]++;

							$wordcour=$word;
						}
						else $wordcour=$word;

						// ajout du mot courant pour mal des tables index et corresp
						if (!isset($tabwordscur[$key])) {
							$tabwordscur[$key]=array();
							$tabwordscur[$key][0]=1;
							$tabwordscur[$key][1]=strlen($word);
							$tabwordscur[$key][2]=$this->lastinsertsentence;
							$tabwordscur[$key]['corresp']=array();
							// compteur global de mots
						}
						else $tabwordscur[$key][0]++;

						$nbwords++;
					}
				}// fin de boucle sur le ou les mots
				$cour=$i+1;
				$isword=false;
				$word="";
			}

			if ($issentence) {
				$sentencecontent=trim($sentencecontent);
				if ($sentencecontent !="") {
					//echo "\n".$sentencecontent;
					if ($linesentence!="") $linesentence.=",";
					$linesentence.=' ('.$this->lastinsertsentence.','.$id_metafield.','.$idparag.",".$this->db->getPdo()->quote(trim($sentencecontent)).",".$id_globalobject.")";
					//unset($linesentence);
					$this->lastinsertsentence+=1;
					if ($a==13) $idparag++;
				}
				unset($sentencecontent);
				$sentencecontent="";

				$nbwords=0;
				$issentence=false;
				$wordcour="";
				$wordouble="";
				$key="";

				// enregistrement des lignes courantes
				foreach($tabwordscur as $key=>$elem) { // $elem[0] :cpte, $elem[1]:lentgh, $elem[2] : id_sentence
					$this->cpteline++;

					if ($this->line!="") $this->line.=",";

					$this->line.=' ('.$key.','.$id_metafield.','.$elem[2].','.$elem[1].$prefix.$elem[0].')';
					if ($this->cpteline>100) {
						$res=$this->db->query($this->script.$this->line);
						$this->line="";
						$this->cpteline=0;
					}

					// on boucle sur les elements de ce mot cle
					foreach ($elem['corresp'] as $corresp=>$cpte) {
						$this->cptelinecorresp++;
						if ($this->linecorresp!="") $this->linecorresp.=",";
						$this->linecorresp.=' ('.$key.','.$corresp.','.$elem[2].$prefix.$elem[0].')';

						if ($this->cptelinecorresp>100) {
							$res=$this->db->query($this->scriptcorresp.$this->linecorresp);
							unset($this->linecorresp);
							$this->linecorresp="";
							$this->cptelinecorresp=0;
						}
					}
				}
				$tabwordscur=null;
				unset($tabwordscur);
			}
		}

		// insertion des phrases
		if ($linesentence!="") {
			$res=$this->db->query($this->scriptsentence.$linesentence);
			//print_r($this->db);die();
		}
		//unset($linesentence);
		$linesentence="";

		if ($this->line!='' || $this->cpteline>0) {
			$res=$this->db->query($this->script.$this->line);
			$this->line="";
			$this->cpteline=0;
		}

		if ($this->linecorresp!='' || $this->cptelinecorresp>0) {
			$res=$this->db->query($this->scriptcorresp.$this->linecorresp);
			$this->linecorresp=null;
			$this->linecorresp="";
			$this->cptelinecorresp=0;
		}

		unset($sentencecontent);
		$sentencecontent="";

		return($cpteglobal);
	}
}
