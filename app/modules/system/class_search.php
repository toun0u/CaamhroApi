<?php
require_once DIMS_APP_PATH . '/include/class_timer.php' ;
require_once DIMS_APP_PATH . '/modules/system/class_search_expression.php' ;
require_once DIMS_APP_PATH . '/modules/system/class_module.php' ;
require_once DIMS_APP_PATH . '/modules/system/class_search_expression_result.php';
require_once DIMS_APP_PATH . '/modules/system/class_search_expression_tag.php';
//require_once DIMS_APP_PATH . '/include/class_timer.php' ;
/*
 * Created by Patrick Nourrissier - NETLOR SAS, 02/08/2009
 * patrick[at]netlor.fr
 */
class search {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	public $db;						// connector to database abstraction layer : Mysql, Oracle
	private $userid;				// current dims userid
	private $dims;
	private $modules;
	private $modulestype;
	private $result;
	private $lstmetafields;
	private $allmetafields;
	public	$tablemetafield;
	public	$matrix;
	public	$tabpotentiel;

	//Cyril : pour ranking
	public	$pertinence;
	public	$max_date;
	public	$min_date;



	public	$tabmaxpotentiel;
	public	$tabresultat;
	public	$expression;
	public	$expression_brut;
	private $words;
	public	$tabmetacorresp;
	public	$tabfiltre;
	public	$restrictfield;
	public	$nbresult;
	public	$tabpossible;
	public	$tabtag;


	const RESULT_TYPE_COMPANY		=1;
	const RESULT_TYPE_CONTACT		=2;
	const RESULT_TYPE_ACTIVITY		=3;
	const RESULT_TYPE_MISSION		=4;
	const RESULT_TYPE_FAIR			=5;
	const RESULT_TYPE_DOCUMENT		=6;
	const RESULT_TYPE_PICTURE		=7;
	const RESULT_TYPE_MOVIE			=8;
	const RESULT_TYPE_OPPORTUNITY	=9;
	const RESULT_TYPE_SUIVI			=10;
	const RESULT_TYPE_SUIVI_DETAIL	=11;
	const RESULT_TYPE_PROJECT		=12;
	const RESULT_TYPE_ADDRESS		=13;

	const TYPE_SIMPLE_SEARCH		=1;
	const TYPE_ADVANCED_SEARCH		=2;



	public function __construct(){
		$dims = dims::getInstance();
		$this->db=$dims->getDb();
		$this->dims=$dims;
		$this->userid=$_SESSION['dims']['userid'];
		$this->result=array();
		$this->modules = array();
		$this->modulestype = array();
		$this->metafields = array();
		$this->objects = array();
		$this->matrix	= array();
		$this->tabpotentiel	= array();
		$this->tabresultat	= array();
		$this->expression = array();
		$this->words = array();
		$this->tabfiltre = array();
		$this->restrictfield =false;
		$this->allmetafields=false;
		$this->nbresult=0;
		$this->tabpossible=array();
		$this->tabtag=array();
	}

	public function initSearchObject() {
		if (!empty($this->modules)) {
			unset($_SESSION['dims']['modsearch'][$_SESSION['dims']['moduleid']]);
			$_SESSION['dims']['modsearch']['modulestype']=$this->modulestype;
			$_SESSION['dims']['modsearch']['modules']=$this->modules;
			$_SESSION['dims']['modsearch']['tablemetafield']=$this->tablemetafield;
			$_SESSION['dims']['modsearch']['objects']=$this->objects;
			$_SESSION['dims']['modsearch']['tabfiltre']=$this->tabfiltre;
			$_SESSION['dims']['modsearch']['expression_brut']=$this->expression_brut;
			$_SESSION['dims']['modsearch']['expression']=$this->expression;
			$_SESSION['dims']['modsearch']['tabpotentiel']=$this->tabpotentiel;
			$_SESSION['dims']['modsearch']['tabmaxpotentiel']=$this->tabmaxpotentiel;
			$_SESSION['dims']['modsearch']['nbresult']=$this->nbresult;
			$_SESSION['dims']['modsearch']['tabresultat']=$this->tabresultat;
			$_SESSION['dims']['modsearch']['tabtag']=$this->tabtag;
		}
	}

	public function updateSearchObject() {
		if (isset($_SESSION['dims']['modsearch']['modulestype'])) {
			$this->modulestype=$_SESSION['dims']['modsearch']['modulestype'];
			$this->modules=$_SESSION['dims']['modsearch']['modules'];
			$this->tablemetafield=$_SESSION['dims']['modsearch']['tablemetafield'];
			$this->objects=$_SESSION['dims']['modsearch']['objects'];
			if (isset($_SESSION['dims']['modsearch']['tabfiltre']))
				$this->tabfiltre=$_SESSION['dims']['modsearch']['tabfiltre'];
			else
				$this->tabfiltre=array();
			$this->expression_brut=$_SESSION['dims']['modsearch']['expression_brut'];
			$this->expression=$_SESSION['dims']['modsearch']['expression'];
			$this->tabpotentiel=$_SESSION['dims']['modsearch']['tabpotentiel'];
			$this->tabmaxpotentiel=$_SESSION['dims']['modsearch']['tabmaxpotentiel'];
			$this->nbresult=$_SESSION['dims']['modsearch']['nbresult'];
			$this->tabresultat=$_SESSION['dims']['modsearch']['tabresultat'];
			$this->tabtag=$_SESSION['dims']['modsearch']['tabtag'];
			$this->tabpossible=$_SESSION['dims']['modsearch']['tabpossible'];
		}
	}

	public function addWceObjects() {
		global $_DIMS;
		$objects=array();
		$res=$this->db->query("select * from dims_mb_object");

		// construction de la liste des objets par module_type
		if ($this->db->numrows($res)>0) {
			while ($e=$this->db->fetchrow($res)) {
				$objects[$e['id_module_type']][$e['id']]=$e;
			}
		}

		$listmod=$this->dims->getModules($_SESSION['dims']['currentworkspace']['id']);

		foreach($listmod as $idk=>$mod) {

			if($mod['label']=='doc' || $mod['label']=='wce') {
				if (isset($objects[$mod['id_module_type']])) {
					foreach ($objects[$mod['id_module_type']] as $o=>$obj) {
						if (isset($_DIMS['cste'][$obj['label']])) {
							$title=$_DIMS['cste'][$obj['label']];
						}
						else {
							$title=$obj['label'];
						}
						// ajout de l'objet courant
						$this->addSearchObject($mod['instanceid'],$obj['id'],$title,true);
					}
				}
			}
		}
	}

	public function addWceFrontObjects() {
		global $_DIMS;
		$objects=array();
		$res=$this->db->query("select * from dims_mb_object");

		// construction de la liste des objets par module_type
		if ($this->db->numrows($res)>0) {
			while ($e=$this->db->fetchrow($res)) {
				$objects[$e['id_module_type']][$e['id']]=$e;
			}
		}

		$listmod=$this->dims->getModules($_SESSION['dims']['currentworkspace']['id']);
	$notIn = array(0 => "draftcontent%");

		foreach($listmod as $idk=>$mod) {

			if($mod['label']=='doc' || $mod['label']=='wce') {
				if (isset($objects[$mod['id_module_type']])) {
					foreach ($objects[$mod['id_module_type']] as $o=>$obj) {
						if (isset($_DIMS['cste'][$obj['label']])) {
							$title=$_DIMS['cste'][$obj['label']];
						}
						else {
							$title=$obj['label'];
						}
						// ajout de l'objet courant
						$this->addSearchObject($mod['instanceid'],$obj['id'],$title,true,array(),$notIn);
					}
				}
			}
		}
	}

	public function addallObject() {
		global $_DIMS;
		$modeletypeobject=array();

		$this->allmetafields=true;
		$req ="select		f.id,
				f.name,
				f.label,
				f.level,
				o.id_module_type,
				o.id as id_object
		from		dims_mb_field as f
		inner join	dims_mb_classes as c
		on		c.id_table=f.id_table
		inner join	dims_mb_object as o
		on		o.id_class=c.id
		where		f.indexed=1 or o.id>0";

		$res=$this->db->query($req);
		//echo "select id,name,label,level,id_module_type,id_object from dims_mb_field where indexed=1 or id_object>0";
		// construction de la liste des metafields
		if ($this->db->numrows($res)>0) {
			while ($e=$this->db->fetchrow($res)) {

				if ($e['id_object']>0) {
					if (isset($_DIMS['cste'][$e['label']])) {
						$modeletypeobject[$e['id_module_type']][$e['id_object']]=$e['label'];
					}
					else {
						$modeletypeobject[$e['id_module_type']][$e['id_object']]=$e['label'];
					}
				}
				$this->tablemetafield[$e['id']]=$e;
			}
		}
		// on fusionne maintenant avec les modules
		//$mods=$this->dims->getModules($_SESSION['dims']['workspaceid']);
		$mods=$this->dims->getAllModules();

		foreach($mods as $idmod => $mod) {
			$this->modules[$mod['instanceid']]=$mod['instanceid'];
			if (isset($modeletypeobject[$mod['id_module_type']])) {
				$this->objects[$mod['instanceid']]=$modeletypeobject[$mod['id_module_type']];
			}
		}
	}

	/*
	 * Fonction permettant de positionner l'objet créé comme recherche
	 */
	public function AddObjectToResult($id_module_type,$id_object,$id_record) {

	}

	public function addSearchObject($moduleid,$object_id,$labelobject,$enlarge=false,$listfields=array(),$unListFields=array()) {
		$dims = dims::getInstance();

		$lstModType = array();
		if($moduleid == 1){
			$mods=$this->dims->getAllModules();
			foreach($mods as $idmod => $mod) {
				$this->lstmodules[$idmod]=$idmod;
				$this->modules[$mod['instanceid']]=$mod['instanceid'];
				$this->modulestype[$mod['label']][$object_id]=$labelobject;
				$this->objects[$mod['instanceid']][$object_id]=$labelobject;
				$lstModType[$mod['id_module_type']] = $mod['id_module_type'];
			}
		}else{
			$objmod=$this->dims->getModule($moduleid);
			$this->lstmodules[$moduleid]=$moduleid;
			$this->modules[$moduleid]=$moduleid;
			$this->modulestype[$objmod['label']][$object_id]=$labelobject;
			$this->objects[$moduleid][$object_id]=$labelobject;
			$lstModType[$objmod['id_module_type']] = $objmod['id_module_type'];
		}


		if (!empty($lstModType)) {
			$tablename="";
			$params = array(
				':objectid'		=> $object_id,
			);
			$sql = "SELECT		c.id_table, c.classname
					FROM		dims_mb_classes as c
					INNER JOIN	dims_mb_object as o
					ON			o.id_class=c.id
					WHERE		o.id= :objectid and o.id_module_type IN (".$this->db->getParamsFromArray($lstModType, 'idmoduletype', $params).") ";

			// construction de la liste des metafields
			$res=$this->db->query($sql,$params);
			if ($this->db->numrows($res)>0) {
				if ($f=$this->db->fetchrow($res)) {
					$id_table=$f['id_table'];
					$classname = $f['classname'];
					$rels = $dims->getMBObjectRelationsOn($classname);

					$table_ids[] = $id_table;

					if( ! empty($rels) ){
						foreach($rels as $id_class_to => $tab){
							$class = $dims->getMBClassDataFromID($id_class_to);
							if( ! empty($class) ) $table_ids[] = $class['id_table'];
						}
					}

					#Cyril - 17/12/2012 - récupération également des tables liées par relation
					// recherche de l'ensemble des champs indexables
					// nouvelle contrainte de filtre
					if (!empty($listfields)) {
						$params = array();
						$sql="select id,name,label,level from dims_mb_field where id_table IN (".$this->db->getParamsFromArray($table_ids, 'idtable', $params).")";

						$this->restrictfield=true;

						if(is_array($listfields))
							$arrayfield=$listfields;
						else
							$arrayfield=explode(",",$listfields);
						$sql.=" AND name IN (".$this->db->getParamsFromArray($arrayfield, 'namefield', $params).")";

						if(!empty($unListFields)){
							$sql.=" AND name IN (".$this->db->getParamsFromArray($unListFields, 'nameunlist', $params).")";
						}

						$res=$this->db->query($sql, $params);
					}
					elseif(!empty($unListFields)){
						$params = array();
						$sql="select id,name,label,level from dims_mb_field where id_table IN (".$this->db->getParamsFromArray($table_ids, 'idtable', $params).")";

						$this->restrictfield=true;

						$sql.=" AND name IN (".$this->db->getParamsFromArray($unListFields, 'nameunlist', $params).")";

						$res=$this->db->query($sql, $params);
					}else{
						if ($enlarge) {
							$params = array();
							$res=$this->db->query("select id,name,label,level from dims_mb_field where id_table IN ".$this->db->getParamsFromArray($table_ids, 'idtable', $params)." and indexed=1", $params);
						} else {
							$params = array();
							$res=$this->db->query("select id,name,label,level from dims_mb_field where id_table IN (".$this->db->getParamsFromArray($table_ids, 'idtable', $params).") and indexed=1", $params);
						}
					}

					// construction de la liste des metafields
					if ($this->db->numrows($res)>0) {
						while ($e=$this->db->fetchrow($res)) {
							$this->tablemetafield[$e['id']]=$e;
						}
					}
				}
			}
		}
	}


	/*
	 * Execution de la fonction de recherche
	 * typeword : 0 undefine, 1 word, 2 tag
	 */
	public function executeSearch($expression,$kword,$idmodule,$idobj,$idmetafield,$sens,$typeword=0) {
		// execution timer
		$dims_timer = new timer();
		$dims_timer->start();
		$time = round($dims_timer->getexectime(),3);
		$timedeb = sprintf("%d",$time*1000);
		global $_DIMS;

		if (empty($this->modules)) $this->modules[0]=0;

		$lstmodules=implode(",",$this->modules);

		$this->result=array();
		$this->nbresult=0;

		if (mb_check_encoding($expression,"UTF-8")) {
		$expression=utf8_decode(dims_convertaccents($expression));
		}


		$this->expression_brut=$expression;
		$_SESSION['dims']['modsearch']['expression_brut']=$this->expression_brut;

		$this->analyzeExpression($expression);

		// on sauve l'expression
		$_SESSION['dims']['modsearch']['expression']=$this->expression;

		if (isset($_SESSION['dims']['modsearch']['tabfiltre'])) {
			$this->tabfiltre=$_SESSION['dims']['modsearch']['tabfiltre'];
		}

		$filtermetafield=false;
		// on positionne la valeur courante sur l'objet et le mot concerne
		if ($kword!="" && $idmodule != "" && $idobj != "" && $idmetafield!="") {
			if ($sens==0) {
				unset($this->tabfiltre[$kword]['metafield'][$idmodule][$idobj]);
			}
			else {
				$this->tabfiltre[$kword]['metafield'][$idmodule][$idobj]=$idmetafield;

				$filtermetafield=true;
			}
			$_SESSION['dims']['modsearch']['tabfiltre']=$this->tabfiltre;
		}


		//dims_print_r($_SESSION['dims']['modsearch']['tabfiltre']);
		// on choisit de filter sur un type de mot
		if ($kword!='' && $typeword>0) {
			// reset current configuration
			//unset($this->tabfiltre[$kword]);

			//$this->tabfiltre[$kword]['type']=$typeword;
			//$this->tabfiltre[$k]['op']='AND'; // operator
			//$this->tabfiltre[$k]['ref']=0; // reference of word or tag id
			// sauvegarde en session
			//$_SESSION['dims']['modsearch']['tabfiltre']=$this->tabfiltre;
		}
		//dims_print_r($this->tabfiltre);
		// tableau de resultat
		$this->matrix	= array();
		$this->matrixresult	= array();
		$this->tabpotentiel	= array();
		$this->tabresultat	= array();

		$tabpoids=array();
		$tabres=array();

		// traitement du filtre sur les espaces de travail
		$lstworkspaces=$_SESSION['dims']['workspaceid'];

		if (isset($_SESSION['dims']['desktop_view_type']) && $_SESSION['dims']['desktop_view_type']==1) {
			$resu=$this->dims->getListWorkspaces();
			if (!empty($resu)) {
				$lstworkspaces=$resu;
			}
		}

		// position de user id
		$user_id=0;
		if (isset($_SESSION['dims']['userid']) && $_SESSION['dims']['userid']>0) {
			$user_id=$_SESSION['dims']['userid'];
		}

		foreach ($this->expression as $k=>$elemword) {
			$this->words[$k]=$elemword;
			$word=$elemword['word'];
			$type=$elemword['type'];
			$wordlength=strlen($word);
			$initnewword=false;

			if (!isset($this->tabfiltre[$k]['type'])) {
				$this->tabfiltre[$k]['type']=1; // default word
				$this->tabfiltre[$k]['op']='AND'; // operator
				$this->tabfiltre[$k]['ref']=0; // reference of word or tag id
				$this->tabfiltre[$k]['metafield']=array();
				// sauvegarde en session
				$_SESSION['dims']['modsearch']['tabfiltre']=$this->tabfiltre;
				$initnewword=true;
			}

			/*****************************************/
			// traitement des tags ressemblants
			$sql="select distinct * from dims_tag WHERE ((id_workspace = :idworkspace and private=0 and type=0) OR (id_user = :iduser and private=1  and type=0)) OR type>0";

			$rs	= $this->db->query($sql, array(
				':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
				':iduser' => array('type' => PDO::PARAM_INT, 'value' => $user_id),
			));
			$time = round($dims_timer->getexectime(),3);
			$time = sprintf("%d",$time*1000);
			$coeff=80;
			$wordtag=str_replace("_", " ", $word);

			$exptworld=explode(" ",$wordtag);

			//echo "<br>Mot courant ".$word." : ".($time-$timeb);
			//echo $sql;
			if ($this->db->numrows($rs)>0) {
				while ($fields = $this->db->fetchrow($rs)) {
					// on decoupe l'expression de tag pour prendre chaque mot
					if (isset($_DIMS['cste'][$fields['tag']])) {
						if (strpos($_DIMS['cste'][$fields['tag']]," ")>0)
							$exptg=explode(" ",$_DIMS['cste'][$fields['tag']]);
						else
							$exptg=explode("_",$_DIMS['cste'][$fields['tag']]);
					}
					else {
						if (strpos($fields['tag']," ")>0)
							$exptg=explode(" ",$fields['tag']);
						else
							$exptg=explode("_",$fields['tag']);
					}

					// on parcout les mots composants le tag courant
					foreach ($exptg as	$u=>$el) {
						// on parcourt l'ensemble des mots du tag défini
						foreach ($exptworld as	$uu=>$subword) {
							$sublength=strlen($subword);
							$res= similar_text($subword ,substr(trim($el),0,$sublength),$percent);
							// on regarde si ce que l'on vient de taper correspond exactement a ce qui est recherché
							if (strtolower($fields['tag'])==strtolower($word)) {
								$this->tabfiltre[$k]['ref']=$fields['id'];
								if ($initnewword) {
									// on a un nouveau mot et c'est un mot cle tag
									$this->tabfiltre[$k]['type']=2; // tag
									$this->tabfiltre[$k]['op']='AND'; // operator
									$this->tabfiltre[$k]['metafield']=array();
									$_SESSION['dims']['modsearch']['tabfiltre']=$this->tabfiltre;
								}

							}
							//if ($this->tabfiltre[$k]['ref']!=0)
							//	echo $this->tabfiltre[$k]['ref'];

							if ($percent>=$coeff) {
								if ($fields['id']!=$this->tabfiltre[$k]['ref'] )
									$this->tabtag[$k][$fields['id']]=$fields['tag'];
							}
						}
					}
				}
			}
			//echo "<br>Tag : ".($time-$timeb)." ms";

			/*****************************************/
			// test si filtre sur un tag
			if (isset($this->tabfiltre[$k]['type']) && $this->tabfiltre[$k]['type']==2) {
				// on recupere l'ensemble des informations attachées au tag courant
				$rs=$this->db->query("SELECT id_module,id_object,id_record from dims_tag_index where id_tag = :idtag", array(
					':idtag' => array('type' => PDO::PARAM_INT, 'value' => $this->tabfiltre[$k]['ref']),
				));
				//echo "select	id_module,id_object,id_record from dims_tag_index where id_tag =".$this->tabfiltre[$k]['ref'];
				if ($this->db->numrows($rs)>0) {
					while ($fields = $this->db->fetchrow($rs)) {
						$this->result[$fields['id_module']][$fields['id_object']][$k][$fields['id_record']]=$fields['id_record'];
					}
				}

				// on ajoute les autres tags
				$typetag=0;

				$res=$this->db->query("select type from dims_tag where id= :idtag", array(
					':idtag' => array('type' => PDO::PARAM_INT, 'value' => $this->tabfiltre[$k]['ref']),
				));
				while ($f=$this->db->fetchrow($res)) {
					$typetag=$f['type'];
				}

				//	recherhce sur le type de tag si on le prend
				if ($typetag==4) {
					// on recherche les autres tags du meme type
					$res=$this->db->query("select id,tag from dims_tag where type=4 and id<> :idtag", array(
						':idtag' => array('type' => PDO::PARAM_INT, 'value' => $this->tabfiltre[$k]['ref']),
					));
					while ($fields=$this->db->fetchrow($res)) {
						if (!isset($this->tabtag[$k][$fields['id']])) {
							$this->tabtag[$k][$fields['id']]=$fields['tag'];
						}
					}
				}
			}
			else {
				// on trait comme un mot cle
				$lstwords=array(); // on empile au fur et a mesure
				$trouve=false;

				$cpte=0;
				$tabcorresp=array();
				$chaineword=array();
				$curpourcent=0;
				$c=0;
				$coeff=75;

				$time = round($dims_timer->getexectime(),3);
				$time = sprintf("%d",$time*1000);

				$metaphone=metaphone($word);
				$metaphone2=metaphone(substr($word,0,strlen($word)-1));
				$flascii=ord(substr($word,0,1));
				$sdex=soundex($word);
				$code=substr($sdex,1);

				$params = array();
				$sql= "select	distinct	k.id,
							k.word
									from		dims_keywords as k
									where		k.stype=:type";
				$params[':type'] = array('type' => PDO::PARAM_INT, 'value' => $type);

				//switch sur taille du mot
				$le=strlen($word);

				if ($type!=1 && $type!=2)  { // texte
					if ($le<=3) {
						$sql .= " and		k.word like :word ";
						$coeff=100;
						$params[':word'] = array('type' => PDO::PARAM_INT, 'value' => $word.'%');
					} else if ($le<=5) {
						$coeff=80;
						$sql .= " and		k.word like :word ";
						$params[':word'] = array('type' => PDO::PARAM_INT, 'value' => $word.'%');
					}
					elseif($le<=7) {
						$coeff=70;
						$sql .= " and		k.word like :word ";
						$params[':word'] = array('type' => PDO::PARAM_INT, 'value' => $word.'%');
					}
					else {
						$sql.= " AND k.flascii = :flascii and (k.code = :code";
						$sql.= " OR left(k.metaphone,:lengthmetaphone) = :metaphone ";
						$sql.= ")";
						$params[':flascii'] = array('type' => PDO::PARAM_STR, 'value' => $flascii);
						$params[':code'] = array('type' => PDO::PARAM_STR, 'value' => $code);
						$params[':lengthmetaphone'] = array('type' => PDO::PARAM_STR, 'value' => strlen($metafield));
						$params[':metaphone'] = array('type' => PDO::PARAM_STR, 'value' => $metafield);
					}
				}
				else {
					$sql .= " and		k.word like :word ";
					$params[':word'] = array('type' => PDO::PARAM_INT, 'value' => $word.'%');
					$coeff=100;
				}

				$rs	= $this->db->query($sql, $params);
				$time = round($dims_timer->getexectime(),3);
				$time = sprintf("%d",$time*1000);
				//echo "<br>Simple : ".($time-$timeb);

				if ($this->db->numrows($rs)>0) {
					while ($fields = $this->db->fetchrow($rs)) {
						if (!isset($tabcorresp[$fields['id']])) {
							if ($coeff==100) {
								//$tabpoids[$k][$fields['id']]=$coeff;
								$tabcorresp[$fields['id']]=$fields['word'];
								$chaineword[]=$fields['id'];
							}
							elseif ($type==0) {
								$res= similar_text($word ,substr($fields['word'],0,$wordlength),$percent);
								if ($percent>=$coeff) {

									//$tabpoids[$k][$fields['id']]=$percent;
									$tabcorresp[$fields['id']]=$fields['word'];
									$chaineword[]=$fields['id'];
								}
							}
						}
					}
				}
				$time = round($dims_timer->getexectime(),3);
				$time = sprintf("%d",$time*1000);

				// pour chaque on interroge la table d'index
				if (!empty($tabcorresp)) {
					if ($this->allmetafields || (!$this->allmetafields && empty($this->tablemetafield))) {
						$params = array();
						$rs=$this->db->query("select  id_module,id_object,id_metafield,id_record,id_keyword from dims_keywords_index where id_keyword in (".$this->db->getParamsFromArray($chaineword, 'idkeyword', $params).") and id_module in (".$this->db->getParamsFromArray($this->modules, 'idmodule', $params).")", $params);
					}
					else {
						$params = array();
						$rs=$this->db->query("select  id_module,id_object,id_metafield,id_record,id_keyword from dims_keywords_index where id_keyword in (".$this->db->getParamsFromArray($chaineword, 'idkeyword', $params).") and id_metafield in (".$this->db->getParamsFromArray(array_keys($this->tablemetafield), 'idmetafield', $params).") and id_module in (".$this->db->getParamsFromArray($this->modules, 'idmodule', $params).")", $params);
					}

					if ($this->db->numrows($rs)>0) {
						while ($fields = $this->db->fetchrow($rs)) {
							if (isset($this->tabfiltre[$k]['metafield'][$fields['id_module']]) && sizeof($this->tabfiltre[$k]['metafield'][$fields['id_module']])>0) {
								if ($this->tabfiltre[$k]['metafield'][$fields['id_module']][$fields['id_object']]==$fields['id_metafield']) {
									$this->matrix[$k][$fields['id_module']][$fields['id_object']][$fields['id_metafield']][]=$fields['id_record'];
									$this->result[$fields['id_module']][$fields['id_object']][$k][$fields['id_record']]=$fields['id_record'];
								}
							}
							else {
								$this->matrix[$k][$fields['id_module']][$fields['id_object']][$fields['id_metafield']][]=$fields['id_record'];
								$this->result[$fields['id_module']][$fields['id_object']][$k][$fields['id_record']]=$fields['id_record'];
							}
						}
					}
					$time = round($dims_timer->getexectime(),3);
					$time = sprintf("%d",$time*1000);
				}

				// traitement des mots ressemblants lors d'une recherche sur un mot non identifié
				// faire un rapprochement plus large des mots ressemblants
				$isspresent=false;
				$word2='';
				$wordlength2=0;
				$params = array();
				$sql= "select		distinct	k.id,
								k.word
								from		dims_keywords as k
								where		k.stype=:type";
				$params[':type'] = array('type' => PDO::PARAM_INT, 'value' => $type);

				$sql.= " AND k.flascii = :flascii and (k.code = :code";
				$sql.= " OR left(k.metaphone,:lengthmetaphone)=:metaphone";
				$params[':flascii'] = array('type' => PDO::PARAM_INT, 'value' => $flascii);
				$params[':code'] = array('type' => PDO::PARAM_INT, 'value' => $code);
				$params[':metaphone'] = array('type' => PDO::PARAM_INT, 'value' => $metaphone);
				$params[':lengthmetaphone'] = array('type' => PDO::PARAM_INT, 'value' => strlen($metaphone));

				// on regarde si le mot recherche ne termine pas par un "s"
				if (strtolower(substr($metaphone,-1))=="s" && strlen($metaphone)>2) {
					// on ajoute une condition sans le s
					$metaphone2=substr($metaphone,0,strlen($metaphone)-1);
					$sql.= " OR left(k.metaphone,:lengthmetaphone2)=:metaphone2";
					$params[':metaphone2'] = array('type' => PDO::PARAM_INT, 'value' => $metaphone2);
					$params[':lengthmetaphone2'] = array('type' => PDO::PARAM_INT, 'value' => strlen($metaphone2));
					$word2=substr($word,0,strlen($word)-1);
					$wordlength2=$wordlength-1;
					$isspresent=true;
				}
				$sql.=")";

				$rs = $this->db->query($sql, $params);
				$time = round($dims_timer->getexectime(),3);
				$time = sprintf("%d",$time*1000);

				if ($this->db->numrows($rs)>0) {
					$taborder=array();
					$percent2=0;
					while ($fields = $this->db->fetchrow($rs)) {

						if ((!isset($tabcorresp[$fields['id']]) || $word!=$fields['word']) && strlen($fields['word']) < ($wordlength+4)) {
							$res= similar_text($word ,substr($fields['word'],0,$wordlength),$percent);
							if ($isspresent) {
								$res= similar_text($word2 ,substr($fields['word'],0,$wordlength2),$percent2);
							}

							if ($percent>=85 || $percent2>=85) {
								if ($percent>$percent2)
									$taborder[$percent][$fields['id']]=$fields['word'];
								else {
									$taborder[$percent2][$fields['id']]=$fields['word'];
								}
							}
						}
					}
					// on tri le tableau taborder
					krsort($taborder);

					foreach ($taborder as $pourcent => $el) {
						foreach ($el as $m => $w) {
							$this->tabpossible[$k][]=$w;
						}
					}

				}
				$time = round($dims_timer->getexectime(),3);
				$time = sprintf("%d",$time*1000);
				//echo "<br>Mot ressemblants ".$this->db->numrows($rs)." : ".($time-$timeb)." ms";
			}

		} // fin de la boucle sur les mots
		//dims_print_r($this->matrix);

		// traitement des resultats
		$nbwords=sizeof($this->words);
		//dims_print_r($_SESSION['dims']['modsearch']['tabfiltre']);
		foreach($this->objects as $id_module=>$elemtype) {
			foreach($elemtype as $idobj=>$elem) {
				// croisons les resultats
				$tabresult=array();
				$c=0;
				$operator='AND';
				if (isset($this->result[$id_module][$idobj])) {
					foreach($this->result[$id_module][$idobj] as $indice =>$resultcour) {
						if (empty($tabresult) && $indice==0)	{
							$tabresult=$resultcour;
						}
						else {
							if ($operator=='AND' || $operator=='') {
								$tabresult=array_intersect_assoc($tabresult,$resultcour);
							}
							else {
								$tabresult+=$resultcour;
							}
						}
						if (isset($this->tabfiltre[$indice]['op'])) {
							$operator=$this->tabfiltre[$indice]['op'];
						}
						else {
							$operator='AND';
						}
						$c++;
					}
				}

				if ($c<$nbwords && $operator=='AND') {
					$this->tabresultat[$id_module][$idobj]=array();
				}
				else {
					$this->tabresultat[$id_module][$idobj]=$tabresult;
				}

				// compteur de resultat
				$this->nbresult+=sizeof($tabresult);
			}
		}

		$time = round($dims_timer->getexectime(),3);
		$time = sprintf("%d",$time*1000);
		//echo "Intersect done :".($time-$timeb)." ms <br>";

		$scoring=array();
		$this->tabmaxpotentiel=array();
		// on check le resultat
		$operator='AND';

		foreach ($this->words as $k=>$word) {
			foreach($this->objects as $id_module=>$elemtype) {
				foreach($elemtype as $idobj=>$elem) {
					$totaltemp=0;
					$isselected=false;
					if (!isset($this->result[$id_module][$idobj][$k])) $this->result[$id_module][$idobj][$k]=array();

					// on tri les resultats par metafield
					if (isset($this->matrix[$k][$id_module][$idobj])) {
						foreach($this->matrix[$k][$id_module][$idobj] as $meta=>$st) {
							// on croise avec le tableau de resultat
							if ($operator=='AND' || $operator=='') {
								$this->matrix[$k][$id_module][$idobj][$meta]=array_intersect($this->matrix[$k][$id_module][$idobj][$meta],$this->tabresultat[$id_module][$idobj]);
							}
							else {
								$this->matrix[$k][$id_module][$idobj][$meta]+=$this->tabresultat[$id_module][$idobj];
							}

							$taille=sizeof($this->matrix[$k][$id_module][$idobj][$meta]);
							if ($taille>0) {
								// tri decroissant
								$this->tabpotentiel[$k][$id_module][$idobj][$meta]=$taille;
							}

							if (isset($this->tabfiltre[$k][$id_module][$idobj]) && $this->tabfiltre[$k][$id_module][$idobj]==$meta) {
								$elem=array();
								$elem['k']=$k;
								$elem['word']=$word['word'];
								$elem['metafield']=$meta;
								$isselected=true;
								$this->tabmaxpotentiel[$k][$id_module][$idobj]=$elem;
							}
							elseif ($totaltemp<$taille && !$isselected) {
								$totaltemp=$taille;
								$elem=array();
								$elem['k']=$k;
								$elem['word']=$word['word'];
								$elem['metafield']=$meta;
								$this->tabmaxpotentiel[$k][$id_module][$idobj]=$elem;
							}
						}
					}

					if (isset($indice) && (!isset($this->tabfiltre[$indice]['op']) || $this->tabfiltre[$indice]['op']=='AND')) {
						$operator=$this->tabfiltre[$indice]['op'];
					}
				}
			}
		}
		$time = round($dims_timer->getexectime(),3);
		$time = sprintf("%d",$time*1000);

		$_SESSION['dims']['modsearch']['tabpotentiel']=$this->tabpotentiel;
		$_SESSION['dims']['modsearch']['tabmaxpotentiel']=$this->tabmaxpotentiel;
		$_SESSION['dims']['modsearch']['nbresult']=$this->nbresult;
		$_SESSION['dims']['modsearch']['tabresultat']=$this->tabresultat;
		$_SESSION['dims']['modsearch']['tabpossible']=$this->tabpossible;
		$_SESSION['dims']['modsearch']['tabfiltre']=$this->tabfiltre;
		$_SESSION['dims']['modsearch']['tabtag']=$this->tabtag;
	}

	/*
	 *------------------------------------------- RECHERCHE AVEC GLOBAL OBJECT ------------------------------------------
	 */
	public function executeSearch2($expression,$kword,$idmodule,$idobj,$idmetafield,$sens,$typeword=0,$replace=null,$workspaceid = null, $ignore_type = false, $force_pre_joker = false) {
		// execution timer
		$dims_timer = new timer();
		$dims_timer->start();
		$time = round($dims_timer->getexectime(),3);
		$timedeb = sprintf("%d",$time*1000);
		global $_DIMS;

		if (empty($this->modules)) $this->modules[0]=0;

		$lstmodules=implode(",",$this->modules);

		$this->result=array();
		$this->nbresult=0;

		if (mb_check_encoding($expression,"UTF-8")) {
			$expression=utf8_decode(dims_convertaccents($expression));
		}
		// nouveau traitement
		if ($expression!=$this->expression_brut) {
			//$this->tabfiltre=array();
			//unset($_SESSION['dims']['modsearch']['tabfiltre']);
		}

		$this->expression_brut=$expression;
		$_SESSION['dims']['modsearch']['expression_brut']=$this->expression_brut;

		$this->analyzeExpression($expression);
		if(isset($replace) && count($replace) > 0){
			foreach($replace as $pos => $w){
				$this->expression[$pos]['word'] = $w;
			}

			//érasement de l'expression brute, tant pis pour les accents et les majuscules
			$_SESSION['dims']['modsearch']['expression_brut'] = '';
			$nb_words = count($this->expression);
			$i=0;
			foreach($this->expression as $k => $tab){
				$_SESSION['dims']['modsearch']['expression_brut'] .= $tab['word'];
				if($i < $nb_words - 1) $_SESSION['dims']['modsearch']['expression_brut'] .= ' ';
				$i++;
			}
		}

		// on sauve l'expression
		$_SESSION['dims']['modsearch']['expression']=$this->expression;

		if (isset($_SESSION['dims']['modsearch']['tabfiltre'])) {
			$this->tabfiltre=$_SESSION['dims']['modsearch']['tabfiltre'];
		}

		$filtermetafield=false;
		// on positionne la valeur courante sur l'objet et le mot concerne
		if ($kword!="" && $idmodule != "" && $idobj != "" && $idmetafield!="") {
			if ($sens==0) {
				unset($this->tabfiltre[$kword]['metafield'][$idmodule][$idobj]);
			}
			else {
				$this->tabfiltre[$kword]['metafield'][$idmodule][$idobj]=$idmetafield;

				$filtermetafield=true;
			}
			$_SESSION['dims']['modsearch']['tabfiltre']=$this->tabfiltre;
		}


		//dims_print_r($_SESSION['dims']['modsearch']['expression']);
		// on choisit de filter sur un type de mot
		if ($kword!='' && $typeword>0) {
			// reset current configuration
			//unset($this->tabfiltre[$kword]);

			//$this->tabfiltre[$kword]['type']=$typeword;
			//$this->tabfiltre[$k]['op']='AND'; // operator
			//$this->tabfiltre[$k]['ref']=0; // reference of word or tag id
			// sauvegarde en session
			//$_SESSION['dims']['modsearch']['tabfiltre']=$this->tabfiltre;
		}
		//dims_print_r($this->tabfiltre);
		// tableau de resultat
		$this->matrix	= array();
		$this->matrixresult	= array();
		$this->tabpotentiel	= array();
		$this->tabresultat	= array();

		$tabpoids=array();
		$tabres=array();

		// traitement du filtre sur les espaces de travail
		$lstworkspaces=$_SESSION['dims']['workspaceid'];

		if (isset($_SESSION['dims']['desktop_view_type']) && $_SESSION['dims']['desktop_view_type']==1) {
			$resu=$this->dims->getListWorkspaces();
			if (!empty($resu)) {
				$lstworkspaces=$resu;
			}
		}

		// position de user id
		$user_id=0;
		if (isset($_SESSION['dims']['userid']) && $_SESSION['dims']['userid']>0) {
			$user_id=$_SESSION['dims']['userid'];
		}

		//tableau de stockage id_go => keyword x %
		$this->pertinence = array();
		$this->min_date = date('YmdHis');
		$this->max_date = 0;

		foreach ($this->expression as $k=>$elemword){
			if(strlen($elemword['word']) > 1){
				$this->words[$k]=$elemword;
				$word=$elemword['word'];
				$type= $elemword['type'];
				$wordlength=strlen($word);
				$initnewword=false;

				if (!isset($this->tabfiltre[$k]['type'])) {
					$this->tabfiltre[$k]['type']=1; // default word
					$this->tabfiltre[$k]['op']='AND'; // operator
					$this->tabfiltre[$k]['ref']=0; // reference of word or tag id
					$this->tabfiltre[$k]['metafield']=array();
					// sauvegarde en session
					$_SESSION['dims']['modsearch']['tabfiltre']=$this->tabfiltre;
					$initnewword=true;
				}

				//echo "<br>Tag : ".($time-$timeb)." ms";


				// on trait comme un mot cle
				$lstwords=array(); // on empile au fur et a mesure
				$trouve=false;

				$cpte=0;
				$tabcorresp=array();
				$chaineword=array();
				$curpourcent=0;
				$c=0;
				$coeff=85;

				$time = round($dims_timer->getexectime(),3);
				$time = sprintf("%d",$time*1000);

				$metaphone=metaphone($word);
				$metaphone2=metaphone(substr($word,0,strlen($word)-1));
				$flascii=ord(substr($word,0,1));
				$sdex=soundex($word);
				$code=substr($sdex,1);
				$params = array();
				$paramsenlarge = array();

				if(!$ignore_type){
					$sql= "	SELECT		DISTINCT k.id,k.word
							FROM		dims_keywords as k
							WHERE		k.stype=:type";
					$params[':type'] = array('type' => PDO::PARAM_INT, 'value' => $type);
					$paramsenlarge[':type'] = array('type' => PDO::PARAM_INT, 'value' => $type);
				}
				else{
					$sql= "	SELECT		DISTINCT k.id,k.word
							FROM		dims_keywords as k
							WHERE 		1 = 1 ";
				}

				$coeff=100;
				//switch sur taille du mot
				$le=strlen($word);

				$enlargesearch="";

				//if (strpos($word,"-")>=0 || strpos($word,"/")>=0) $enlargesearch='';

				$prefixe = $force_pre_joker ? '%' : '';
				if ($type!=1 && $type!=2)  { // texte
					if( $le == 1){
						$coeff=100;
						$sql .= " and		k.word LIKE :word ";
						$params[':word'] = array('type' => PDO::PARAM_STR, 'value' => $prefixe.$word.'%');
						$paramsenlarge[':word'] = array('type' => PDO::PARAM_STR, 'value' => $prefixe.$word.'%');
					} else if ($le > 1 && $le<=3) {
						$coeff=100;
						$sql .= " and		k.word like :word ";
						$params[':word'] = array('type' => PDO::PARAM_STR, 'value' => $prefixe.$word.'%');
						$paramsenlarge[':word'] = array('type' => PDO::PARAM_STR, 'value' => $prefixe.$word.'%');
					} else if ($le<=5) {
						$coeff=80;
						$sql .= " and		k.word like :word ";
						$params[':word'] = array('type' => PDO::PARAM_STR, 'value' => $prefixe.$word.'%');
						$paramsenlarge[':word'] = array('type' => PDO::PARAM_STR, 'value' => $prefixe.$word.'%');
					}
					elseif($le<=7) {
						$coeff=80;
						$sql .= " and		k.word like :word ";
						$params[':word'] = array('type' => PDO::PARAM_STR, 'value' => $prefixe.$word.'%');
						$paramsenlarge[':word'] = array('type' => PDO::PARAM_STR, 'value' => $prefixe.$word.'%');
					}
					else {
						if($type != 3)	$coeff=85;
						else $coeff = 100;//pour les email - discussion Pidgin avec Pat le 14/01/2014
						$sql .= " and		k.word like :word ";
						$params[':word'] = array('type' => PDO::PARAM_STR, 'value' => $prefixe.$word.'%');
						$paramsenlarge[':word'] = array('type' => PDO::PARAM_STR, 'value' => $prefixe.$word.'%');

						$sql.= " AND k.flascii = :flascii and (k.code = :code ";
						$sql.= " OR left(k.metaphone,:lengthmetaphone) = :metaphone ";
						$sql.= ")";
						$params[':flascii'] = array('type' => PDO::PARAM_INT, 'value' => $flascii);
						$params[':code'] = array('type' => PDO::PARAM_INT, 'value' => $code);
						$params[':lengthmetaphone'] = array('type' => PDO::PARAM_INT, 'value' => strlen($metaphone));
						$params[':metaphone'] = array('type' => PDO::PARAM_INT, 'value' => $metaphone);

						$paramsenlarge[':flascii'] = array('type' => PDO::PARAM_INT, 'value' => $flascii);
						$paramsenlarge[':code'] = array('type' => PDO::PARAM_INT, 'value' => $code);
						$paramsenlarge[':lengthmetaphone'] = array('type' => PDO::PARAM_INT, 'value' => strlen($metaphone));
						$paramsenlarge[':metaphone'] = array('type' => PDO::PARAM_INT, 'value' => $metaphone);
					}
				}
				else {
					$coeff=100;
					$sql .= " AND		k.word LIKE :word ";
					$params[':word'] = array('type' => PDO::PARAM_STR, 'value' => $prefixe.$word.'%');
					$paramsenlarge[':word'] = array('type' => PDO::PARAM_STR, 'value' => $prefixe.$word.'%');
				}

                $sql.=" limit 0,250";
				$rs	= $this->db->query($sql, $params);

				if ($this->db->numrows($rs)==0) {
					// on elargit la recherche
					$rs	= $this->db->query($sql,$paramsenlarge);
					$coeff=85;
				}
				$time = round($dims_timer->getexectime(),3);
				$time = sprintf("%d",$time*1000);
				//echo "<br>Simple : ".($time-$timeb);

				if ($this->db->numrows($rs)>0) {
					while ($fields = $this->db->fetchrow($rs)) {
						if (!isset($tabcorresp[$fields['id']])) {
							if ($coeff==100) {
								//$tabpoids[$k][$fields['id']]=$coeff;
								$tabcorresp[$fields['id']]['word']=$fields['word'];
								$tabcorresp[$fields['id']]['percent']=100;
								$chaineword[]=$fields['id'];
							}
							elseif ($type==0) {
								$res= similar_text($word ,substr($fields['word'],0,$wordlength),$percent);
								if ($percent>=$coeff) {
									//$tabpoids[$k][$fields['id']]=$percent;
									$tabcorresp[$fields['id']]['word']=$fields['word'];
									$tabcorresp[$fields['id']]['percent']=$percent;
									$chaineword[]=$fields['id'];
								}
							}
						}
					}
				}
				$time = round($dims_timer->getexectime(),3);
				$time = sprintf("%d",$time*1000);

				// pour chaque on interroge la table d'index
				if (!empty($tabcorresp)) {
					if ($this->allmetafields || (!$this->allmetafields && empty($this->tablemetafield))) {
						$params = array();
						$f = "";
						if(!is_null($workspaceid)){
							$params[':idw'] = array('value'=>$workspaceid,'type'=>PDO::PARAM_INT);
							$f = " AND (ki.id_workspace = :idw OR ki.id_workspace = 1) ";
						}
						$rs=$this->db->query("SELECT
												ki.id_module,ki.id_object,ki.id_sentence, ki.id_metafield,ki.id_record,ki.id_globalobject,ki.id_keyword, go.timestamp
												FROM dims_keywords_index ki
												INNER JOIN dims_globalobject go ON go.id = ki.id_globalobject
												WHERE ki.id_keyword IN (".$this->db->getParamsFromArray($chaineword, 'idkeyword', $params).")
												AND ki.id_module in (".$this->db->getParamsFromArray($this->modules, 'idmodule', $params).")
												$f
											", $params);


					}
					else {
						$params = array();
						$f = "";
						if(!is_null($workspaceid)){
							$params[':idw'] = array('value'=>$workspaceid,'type'=>PDO::PARAM_INT);
							$f = " AND (ki.id_workspace = :idw OR ki.id_workspace = 1) ";
						}
						$sql = "SELECT ki.id_module,ki.id_object,ki.id_sentence, ki.id_metafield,ki.id_record,ki.id_globalobject,ki.id_keyword, go.timestamp
								FROM dims_keywords_index ki
								INNER JOIN dims_globalobject go ON go.id = ki.id_globalobject
								WHERE id_keyword in (".$this->db->getParamsFromArray($chaineword, 'idkeyword', $params).")
								AND ki.id_metafield in (".$this->db->getParamsFromArray(array_keys($this->tablemetafield), 'idmetafield', $params).")
								AND ki.id_module in (".$this->db->getParamsFromArray($this->modules, 'idmodule', $params).")
								$f";
                        /*ob_clean();
                        echo $sqk;
                        dims_print_r($params);
                        die();*/
						$rs=$this->db->query($sql,$params);
					}

					if ($this->db->numrows($rs)>0) {

						while ($fields = $this->db->fetchrow($rs)) {
								$tab_res = array();
								$tab_res['mf'] = $fields['id_metafield'];
								$tab_res['id_go'] = $fields['id_globalobject'];
								$tab_res['sentence'] = $fields['id_sentence'];
								$tab_res['date'] = $fields['timestamp'];
								if($fields['timestamp'] < $this->min_date) $this->min_date = $fields['timestamp'];
								if($fields['timestamp'] > $this->max_date) $this->max_date = $fields['timestamp'];

								if (isset($this->tabfiltre[$k]['metafield'][$fields['id_module']]) && sizeof($this->tabfiltre[$k]['metafield'][$fields['id_module']])>0) { //isset($this->tabfiltre[$k][$fields['id_module']][$fields['id_object']])) {
									if ($this->tabfiltre[$k]['metafield'][$fields['id_module']][$fields['id_object']]==$fields['id_metafield']) {
										$this->matrix[$k][$fields['id_module']][$fields['id_object']][$fields['id_metafield']][$fields['id_globalobject']]=$fields['id_globalobject'];
										$this->result[$fields['id_module']][$fields['id_object']][$k][$fields['id_globalobject']]=$tab_res;
										$this->pertinence[$fields['id_globalobject']][$k][$fields['id_keyword']] = $tabcorresp[$fields['id_keyword']]['percent'];
									}
								}
								else {
									$this->matrix[$k][$fields['id_module']][$fields['id_object']][$fields['id_metafield']][]=$fields['id_globalobject'];
									$this->result[$fields['id_module']][$fields['id_object']][$k][$fields['id_globalobject']]=$tab_res;
									$this->pertinence[$fields['id_globalobject']][$k][$fields['id_keyword']] = $tabcorresp[$fields['id_keyword']]['percent'];
								}
							//}
						}

					}
					$time = round($dims_timer->getexectime(),3);
					$time = sprintf("%d",$time*1000);
					//echo "<br>Large request done : ".($time-$timeb)." ms <br>";
				}

				// traitement des mots ressemblants lors d'une recherche sur un mot non identifié
				// faire un rapprochement plus large des mots ressemblants
                // Modification Pat : 11/07/2015 ne pas faire de ressemblance sur du numérique
                if (false) {
                    $isspresent = false;
                    $word2 = '';
                    $params = array();
                    $wordlength2 = 0;

                    $sql = "SELECT		DISTINCT k.id, k.word
                            FROM		dims_keywords as k
                            INNER JOIN 	dims_keywords_index ki
                            ON 			ki.id_keyword = k.id
                            INNER JOIN 	dims_globalobject g
                            ON 			ki.id_globalobject = g.id
                            WHERE		k.stype= :type
                            AND 		ki.id_workspace IN(:idw1, :idw2) "; //." and length>=".($le-2)." and length<=".($le+2);
                    $params[':type'] = array('type' => PDO::PARAM_INT, 'value' => $type);
                    $params[':idw1'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']);
                    $params[':idw2'] = array('type' => PDO::PARAM_INT, 'value' => 1);

                    $sql .= " AND k.flascii = :flascii and (k.code = :code";
                    $params[':flascii'] = array('type' => PDO::PARAM_STR, 'value' => $flascii);
                    $params[':code'] = array('type' => PDO::PARAM_STR, 'value' => $code);

                    //ci-dessus ^ : opitmisation pour les tab_possible sinon ça remonte tout et n'import quoi (ex : recherche sur 'for'

                    // on regarde si le mot recherche ne termine pas par un "s"
                    if (strtolower(substr($metaphone, -1)) == "s" && strlen($metaphone) > 2) {
                        // on ajoute une condition sans le s
                        $metaphone2 = substr($metaphone, 0, strlen($metaphone) - 1);
                        $sql .= " OR left(k.metaphone,:lengthmetaphone)=:metaphone";
                        $params[':metaphone'] = array('type' => PDO::PARAM_INT, 'value' => $metaphone2);
                        $params[':lengthmetaphone'] = array('type' => PDO::PARAM_INT, 'value' => strlen($metaphone2));
                        $word2 = substr($word, 0, strlen($word) - 1);
                        $wordlength2 = $wordlength - 1;
                        $isspresent = true;
                    }
                    $sql .= ")";

                    $rs = $this->db->query($sql, $params);
                    $time = round($dims_timer->getexectime(), 3);
                    $time = sprintf("%d", $time * 1000);

                    if ($this->db->numrows($rs) > 0) {
                        $taborder = array();
                        while ($fields = $this->db->fetchrow($rs)) {
                            if ((!isset($tabcorresp[$fields['id']]) || $word != $fields['word'])) {
                                $res = similar_text($word, substr($fields['word'], 0, $wordlength), $percent);

                                if ($percent >= 80) {
                                    $taborder[$percent][$fields['id']] = $fields['word'];
                                }
                            }
                        }
                        // on tri le tableau taborder
                        krsort($taborder);

                        foreach ($taborder as $pourcent => $el) {
                            foreach ($el as $m => $w) {
                                $this->tabpossible[$k][] = $w;
                            }
                        }

                    }

                    $time = round($dims_timer->getexectime(), 3);
                    $time = sprintf("%d", $time * 1000);
                }
			} // fin de la boucle sur les mots
		}


		// traitement des resultats
		$nbwords=sizeof($this->words);
		foreach($this->objects as $id_module=>$elemtype) {
			foreach($elemtype as $idobj=>$elem) {
				// croisons les resultats
				$tabresult=array();
				$c=0;
				$operator='AND';
				if (isset($this->result[$id_module][$idobj])) {
					foreach($this->result[$id_module][$idobj] as $indice =>$resultcour) {
						if (empty($tabresult) && $c==0){
							$tabresult=$resultcour;
						}
						else {
							if ($operator=='AND' || $operator=='') {
								$tabresult=array_intersect_key($tabresult,$resultcour);
							}
							else {
								$tabresult+=$resultcour;

							}
						}
						if (isset($this->tabfiltre[$indice]['op'])) {
							$operator=$this->tabfiltre[$indice]['op'];
						}
						else {
							$operator='AND';
						}
						$c++;
					}
				}
				if ($c<$nbwords && $operator=='AND') {
					$this->tabresultat[$id_module][$idobj]=array();
				}
				else {
					$this->tabresultat[$id_module][$idobj]=$tabresult;
				}

				// compteur de resultat
				$this->nbresult+=sizeof($tabresult);
			}

		}
		//dims_print_r($this->tabresultat);die();
		$time = round($dims_timer->getexectime(),3);
		$time = sprintf("%d",$time*1000);
		//echo "Intersect done :".($time-$timeb)." ms <br>";

		$scoring=array();
		$this->tabmaxpotentiel=array();
		// on check le resultat
		$operator='AND';

		foreach ($this->words as $k=>$word) {
			foreach($this->objects as $id_module=>$elemtype) {
				foreach($elemtype as $idobj=>$elem) {
					$totaltemp=0;
					$isselected=false;
					if (!isset($this->result[$id_module][$idobj][$k])) $this->result[$id_module][$idobj][$k]=array();

					// on tri les resultats par metafield
					if (isset($this->matrix[$k][$id_module][$idobj])) {
						foreach($this->matrix[$k][$id_module][$idobj] as $meta=>$st) {
							// on croise avec le tableau de resultat
							if ($operator=='AND' || $operator=='') {
								$this->matrix[$k][$id_module][$idobj][$meta]=array_intersect_assoc($this->matrix[$k][$id_module][$idobj][$meta],$this->tabresultat[$id_module][$idobj]);
							}
							else {
								$this->matrix[$k][$id_module][$idobj][$meta]+=$this->tabresultat[$id_module][$idobj];
							}

							$taille=sizeof($this->matrix[$k][$id_module][$idobj][$meta]);
							if ($taille>0) {
								// tri decroissant
								$this->tabpotentiel[$k][$id_module][$idobj][$meta]=$taille;
							}

							if (isset($this->tabfiltre[$k][$id_module][$idobj]) && $this->tabfiltre[$k][$id_module][$idobj]==$meta) {
								$elem=array();
								$elem['k']=$k;
								$elem['word']=$word['word'];
								$elem['metafield']=$meta;
								$isselected=true;
								$this->tabmaxpotentiel[$k][$id_module][$idobj]=$elem;
							}
							elseif ($totaltemp<$taille && !$isselected) {
								$totaltemp=$taille;
								$elem=array();
								$elem['k']=$k;
								$elem['word']=$word['word'];
								$elem['metafield']=$meta;
								$this->tabmaxpotentiel[$k][$id_module][$idobj]=$elem;
							}
						}
					}

					if (isset($indice) && (!isset($this->tabfiltre[$indice]['op']) || $this->tabfiltre[$indice]['op']=='AND')) {
						$operator=$this->tabfiltre[$indice]['op'];
					}
				}
			}
		}

		$time = round($dims_timer->getexectime(),3);
		$time = sprintf("%d",$time*1000);
		//dims_print_r($this->tabresultat);die();
		$_SESSION['dims']['modsearch']['tabpotentiel']=$this->tabpotentiel;
		$_SESSION['dims']['modsearch']['tabmaxpotentiel']=$this->tabmaxpotentiel;
		$_SESSION['dims']['modsearch']['nbresult']=$this->nbresult;
		$_SESSION['dims']['modsearch']['tabresultat']=$this->tabresultat;
		$_SESSION['dims']['modsearch']['tabpossible']=$this->tabpossible;
		$_SESSION['dims']['modsearch']['tabfiltre']=$this->tabfiltre;
		$_SESSION['dims']['modsearch']['tabtag']=$this->tabtag;
	}


	/*
	 * Cyril - 09/12/2011 - Insertion des résultats de recherche dans une table qui permettra d'exploiter en front les résultats de la recherche
	 */
	public function insertResultsForUser($user_id, $tabresultat,$optionalTags=array()){
		//creation de la nouvelle expression de recherche dans la base

		$s_expression = new search_expression();
		$id_se = $s_expression->create($user_id, (isset($this->expression_brut))?$this->expression_brut:'NULL', self::TYPE_SIMPLE_SEARCH);
		if(count($tabresultat)){
			$s_result_manager = new search_expression_result();
			$s_result_manager->initUserResults($user_id);
			$s_result_manager->initBulkRows();

			$tab_docs = array();//on prépare un tableau qui permettra de faire une requête subsidiaire pour stocker le type exact selon l'extension
			$tab_events = array();//idem selon le type activity ou mission ou fair

			//$curdate = date('YmdHis');
			require_once(DIMS_APP_PATH.'modules/system/class_tiers.php');
			require_once(DIMS_APP_PATH.'modules/system/class_contact.php');
			require_once(DIMS_APP_PATH.'modules/system/suivi/class_suivi.php');
			require_once(DIMS_APP_PATH.'modules/system/suivi/class_suividetail.php');
			require_once(DIMS_APP_PATH.'modules/doc/class_docfile.php');
			require_once(DIMS_APP_PATH.'modules/system/class_action.php');
			require_once(DIMS_APP_PATH.'modules/system/class_city.php');
			require_once(DIMS_APP_PATH.'modules/system/class_region.php');
			require_once(DIMS_APP_PATH.'modules/system/class_departement.php');
			require_once(DIMS_APP_PATH.'modules/system/class_canton.php');
			require_once(DIMS_APP_PATH.'modules/system/class_arrondissement.php');
			require_once(DIMS_APP_PATH.'modules/system/class_address.php');
			require_once(DIMS_APP_PATH.'modules/catalogue/include/class_client.php');

			foreach($tabresultat as $mod_id => $objects_results){//on parcourt le tableau de résultats organisé d'abord par id_module ensuite par id_mb_object puis id_record
				$module = new module();
				$module->open($mod_id);
				$module_type = $module->fields['id_module_type'];//récupération du module_type associé au module courant dans la table des résultat
				if(!empty($module_type)){
					foreach($objects_results as $mb_obj_id => $records){
						if(count($records)){
							//on récupère le label associé au couple id_mb_object + id_module_type
							$params = array();
							$sql = "SELECT 		dmt.name
									FROM 		dims_mb_table dmt
									INNER JOIN 	dims_mb_classes dmc
									ON 			dmt.id = dmc.id_table
									INNER JOIN	dims_mb_object dmo
									ON 			dmc.id = dmo.id_class
									WHERE 		dmo.id=:idmbobject
									AND 		dmo.id_module_type=:idmoduletype";
							$params[':idmbobject'] = array('type' => PDO::PARAM_INT, 'value' => $mb_obj_id);
							$params[':idmoduletype'] = array('type' => PDO::PARAM_INT, 'value' => $module_type);
							$res = $this->db->query($sql, $params);
							$label = '';

							if($this->db->numrows($res)){
								$tab = $this->db->fetchrow($res);
								$label = $tab['name'];
							}

							switch($label){//on switche sur le label pour appliquer le traitement approprié
								case tiers::TABLE_NAME://companies
									//cyril - 19/01/2012 - on filtre uniquement sur ceux qui sont actifs
									if(count($records)){
										$params = array();
										$sql = 'SELECT id_globalobject FROM dims_mod_business_tiers WHERE id_globalobject IN('.$this->db->getParamsFromArray(array_keys($records), 'idglobalobject', $params).') AND inactif='.tiers::TIERS_ACTIF;
										$res = $this->db->query($sql, $params);
										while($tab = $this->db->fetchrow($res)){
											$id = $tab['id_globalobject'];
											$config = $records[$id];
											if(isset($config['mf']))$mf = $config['mf'];
											else $mf = "''";
											$rank = $this->rankResult($this->expression,$this->pertinence[$id], self::RESULT_TYPE_COMPANY, $config['date']);
											if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_COMPANY, $id, $mf, (isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
										}
									}
									break;
								case contact::TABLE_NAME://contacts
									//cyril - 19/01/2012 - on filtre uniquement sur ceux qui sont actifs
									if(count($records)){
										$params = array();
										$sql = 'SELECT id_globalobject FROM dims_mod_business_contact WHERE id_globalobject IN('.$this->db->getParamsFromArray(array_keys($records), 'idglobalobject', $params).') AND inactif='.contact::CONTACT_ACTIF;
										$res = $this->db->query($sql, $params);
										while($tab = $this->db->fetchrow($res)){
											$id = $tab['id_globalobject'];
											$config = $records[$id];
											if(isset($config['mf']))$mf = $config['mf'];
											else $mf = "''";
											$rank = $this->rankResult($this->expression,$this->pertinence[$id], self::RESULT_TYPE_CONTACT, $config['date']);
											if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_CONTACT, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
										}
									}
									break;
								case client::TABLE_NAME:
									if (count($records)){
										$params = array(":iwd"=>$_SESSION['dims']['workspaceid'],":iwd2"=>1);
										$sel = "SELECT 		t.id_globalobject as got, c.id_globalobject as goc, cl.id_globalobject as gocl
												FROM 		".client::TABLE_NAME." cl
												LEFT JOIN 	".tiers::TABLE_NAME." t
												ON 			t.id = cl.tiers_id
												LEFT JOIN 	".contact::TABLE_NAME." c
												ON 			c.account_id = cl.dims_user
												WHERE 		cl.id_globalobject IN (".$this->db->getParamsFromArray(array_keys($records), 'idglobalobject', $params).")
												AND 		cl.id_workspace IN (:iwd, :iwd2)";
										$res = $this->db->query($sel, $params);
										while($tab = $this->db->fetchrow($res)){
											if(!empty($tab['got'])){
												$id = $tab['got'];
												$config = $records[$tab['gocl']];
												if(isset($config['mf']))$mf = $config['mf'];
												else $mf = "''";
												$rank = $this->rankResult($this->expression,$this->pertinence[$tab['gocl']], self::RESULT_TYPE_COMPANY, $config['date']);
												if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_COMPANY, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
											}
											if(!empty($tab['goc'])){
												$id = $tab['goc'];
												$config = $records[$tab['gocl']];
												if(isset($config['mf']))$mf = $config['mf'];
												else $mf = "''";
												$rank = $this->rankResult($this->expression,$this->pertinence[$tab['gocl']], self::RESULT_TYPE_CONTACT, $config['date']);
												if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_CONTACT, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
											}
										}
									}
									break;
								case address::TABLE_NAME:
									if (count($records)){
										$params = array(":iwd"=>$_SESSION['dims']['workspaceid'],":iwd2"=>1);
										$sql = 'SELECT		m.id_contact,m.id_contact2,m.id_tiers,m.id_tiers2,m.id_action, c.id_globalobject
												FROM 		dims_matrix m
												INNER JOIN 	'.address::TABLE_NAME.' c
												ON 			c.id_globalobject = m.id_address
												WHERE		c.id_globalobject IN ('.$this->db->getParamsFromArray(array_keys($records), 'idglobalobject', $params).')
												AND 		m.id_workspace IN (:iwd, :iwd2)';
										$res = $this->db->query($sql, $params);
										while($tab = $this->db->fetchrow($res)){
											if($tab['id_contact'] != '' && $tab['id_contact'] > 0){
												$id = $tab['id_contact'];
												$config = $records[$tab['id_globalobject']];
												if(isset($config['mf']))$mf = $config['mf'];
												else $mf = "''";
												$rank = $this->rankResult($this->expression,$this->pertinence[$tab['id_globalobject']], self::RESULT_TYPE_CONTACT, $config['date']);
												if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_CONTACT, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
											}elseif($tab['id_contact2'] != '' && $tab['id_contact2'] > 0){
												$id = $tab['id_contact2'];
												$config = $records[$tab['id_globalobject']];
												if(isset($config['mf']))$mf = $config['mf'];
												else $mf = "''";
												$rank = $this->rankResult($this->expression,$this->pertinence[$tab['id_globalobject']], self::RESULT_TYPE_CONTACT, $config['date']);
												if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_CONTACT, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
											}elseif($tab['id_tiers'] != '' && $tab['id_tiers'] > 0){
												$id = $tab['id_tiers'];
												$config = $records[$tab['id_globalobject']];
												if(isset($config['mf']))$mf = $config['mf'];
												else $mf = "''";
												$rank = $this->rankResult($this->expression,$this->pertinence[$tab['id_globalobject']], self::RESULT_TYPE_COMPANY, $config['date']);
												if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_COMPANY, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
											}elseif($tab['id_tiers2'] != '' && $tab['id_tiers2'] > 0){
												$id = $tab['id_tiers2'];
												$config = $records[$tab['id_globalobject']];
												if(isset($config['mf']))$mf = $config['mf'];
												else $mf = "''";
												$rank = $this->rankResult($this->expression,$this->pertinence[$tab['id_globalobject']], self::RESULT_TYPE_COMPANY, $config['date']);
												if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_COMPANY, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
											}elseif($tab['id_action'] != '' && $tab['id_action'] > 0){
												$id = $tab['id_action'];
												$config = $records[$tab['id_globalobject']];
												if(isset($config['mf']))$mf = $config['mf'];
												else $mf = "''";
												$rank = $this->rankResult($this->expression,$this->pertinence[$tab['id_globalobject']], self::RESULT_TYPE_MISSION, $config['date']);
												if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_MISSION, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
											}
										}
									}
									break;
								case city::TABLE_NAME:
									if (count($records)){
										$params = array(":iwd"=>$_SESSION['dims']['workspaceid'],":iwd2"=>1);
										$sql = 'SELECT		m.id_contact,m.id_contact2,m.id_tiers,m.id_tiers2,m.id_action, c.id_globalobject
												FROM 		dims_matrix m
												INNER JOIN 	'.city::TABLE_NAME.' c
												ON 			c.id = m.id_city
												WHERE		c.id_globalobject IN ('.$this->db->getParamsFromArray(array_keys($records), 'idglobalobject', $params).')
												AND 		m.id_workspace IN (:iwd, :iwd2)';
										$res = $this->db->query($sql, $params);
										while($tab = $this->db->fetchrow($res)){
											if($tab['id_contact'] != '' && $tab['id_contact'] > 0){
												$id = $tab['id_contact'];
												$config = $records[$tab['id_globalobject']];
												if(isset($config['mf']))$mf = $config['mf'];
												else $mf = "''";
												$rank = $this->rankResult($this->expression,$this->pertinence[$tab['id_globalobject']], self::RESULT_TYPE_CONTACT, $config['date']);
												if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_CONTACT, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
											}elseif($tab['id_contact2'] != '' && $tab['id_contact2'] > 0){
												$id = $tab['id_contact2'];
												$config = $records[$tab['id_globalobject']];
												if(isset($config['mf']))$mf = $config['mf'];
												else $mf = "''";
												$rank = $this->rankResult($this->expression,$this->pertinence[$tab['id_globalobject']], self::RESULT_TYPE_CONTACT, $config['date']);
												if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_CONTACT, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
											}elseif($tab['id_tiers'] != '' && $tab['id_tiers'] > 0){
												$id = $tab['id_tiers'];
												$config = $records[$tab['id_globalobject']];
												if(isset($config['mf']))$mf = $config['mf'];
												else $mf = "''";
												$rank = $this->rankResult($this->expression,$this->pertinence[$tab['id_globalobject']], self::RESULT_TYPE_COMPANY, $config['date']);
												if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_COMPANY, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
											}elseif($tab['id_tiers2'] != '' && $tab['id_tiers2'] > 0){
												$id = $tab['id_tiers2'];
												$config = $records[$tab['id_globalobject']];
												if(isset($config['mf']))$mf = $config['mf'];
												else $mf = "''";
												$rank = $this->rankResult($this->expression,$this->pertinence[$tab['id_globalobject']], self::RESULT_TYPE_COMPANY, $config['date']);
												if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_COMPANY, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
											}elseif($tab['id_action'] != '' && $tab['id_action'] > 0){
												$id = $tab['id_action'];
												$config = $records[$tab['id_globalobject']];
												if(isset($config['mf']))$mf = $config['mf'];
												else $mf = "''";
												$rank = $this->rankResult($this->expression,$this->pertinence[$tab['id_globalobject']], self::RESULT_TYPE_MISSION, $config['date']);
												if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_MISSION, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
											}
										}
									}
									break;
								case region::TABLE_NAME:
									if (count($records)){
										$params = array(":iwd"=>$_SESSION['dims']['workspaceid'],":iwd2"=>1);
										$sql = 'SELECT		m.id_contact,m.id_contact2,m.id_tiers,m.id_tiers2,m.id_action, r.id_globalobject
												FROM 		dims_matrix m
												INNER JOIN 	'.city::TABLE_NAME.' c
												ON 			c.id = m.id_city
												INNER JOIN 	'.region::TABLE_NAME.' r
												ON 			r.code = c.code_reg
												WHERE		r.id_globalobject IN ('.$this->db->getParamsFromArray(array_keys($records), 'idglobalobject', $params).')
												AND 		m.id_workspace IN (:iwd, :iwd2)';
										$res = $this->db->query($sql, $params);
										while($tab = $this->db->fetchrow($res)){
											if($tab['id_contact'] != '' && $tab['id_contact'] > 0){
												$id = $tab['id_contact'];
												$config = $records[$tab['id_globalobject']];
												if(isset($config['mf']))$mf = $config['mf'];
												else $mf = "''";
												$rank = $this->rankResult($this->expression,$this->pertinence[$tab['id_globalobject']], self::RESULT_TYPE_CONTACT, $config['date']);
												if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_CONTACT, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
											}elseif($tab['id_contact2'] != '' && $tab['id_contact2'] > 0){
												$id = $tab['id_contact2'];
												$config = $records[$tab['id_globalobject']];
												if(isset($config['mf']))$mf = $config['mf'];
												else $mf = "''";
												$rank = $this->rankResult($this->expression,$this->pertinence[$tab['id_globalobject']], self::RESULT_TYPE_CONTACT, $config['date']);
												if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_CONTACT, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
											}elseif($tab['id_tiers'] != '' && $tab['id_tiers'] > 0){
												$id = $tab['id_tiers'];
												$config = $records[$tab['id_globalobject']];
												if(isset($config['mf']))$mf = $config['mf'];
												else $mf = "''";
												$rank = $this->rankResult($this->expression,$this->pertinence[$tab['id_globalobject']], self::RESULT_TYPE_COMPANY, $config['date']);
												if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_COMPANY, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
											}elseif($tab['id_tiers2'] != '' && $tab['id_tiers2'] > 0){
												$id = $tab['id_tiers2'];
												$config = $records[$tab['id_globalobject']];
												if(isset($config['mf']))$mf = $config['mf'];
												else $mf = "''";
												$rank = $this->rankResult($this->expression,$this->pertinence[$tab['id_globalobject']], self::RESULT_TYPE_COMPANY, $config['date']);
												if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_COMPANY, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
											}elseif($tab['id_action'] != '' && $tab['id_action'] > 0){
												$id = $tab['id_action'];
												$config = $records[$tab['id_globalobject']];
												if(isset($config['mf']))$mf = $config['mf'];
												else $mf = "''";
												$rank = $this->rankResult($this->expression,$this->pertinence[$tab['id_globalobject']], self::RESULT_TYPE_MISSION, $config['date']);
												if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_MISSION, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
											}
										}
									}
									break;
								case departement::TABLE_NAME:
									if (count($records)){
										$params = array(":iwd"=>$_SESSION['dims']['workspaceid'],":iwd2"=>1);
										$sql = 'SELECT		m.id_contact,m.id_contact2,m.id_tiers,m.id_tiers2,m.id_action, d.id_globalobject
												FROM 		dims_matrix m
												INNER JOIN 	'.city::TABLE_NAME.' c
												ON 			c.id = m.id_city
												INNER JOIN 	'.departement::TABLE_NAME.' d
												ON 			d.code = c.code_dep
												WHERE		d.id_globalobject IN ('.$this->db->getParamsFromArray(array_keys($records), 'idglobalobject', $params).')
												AND 		m.id_workspace IN (:iwd, :iwd2)';
										$res = $this->db->query($sql, $params);
										while($tab = $this->db->fetchrow($res)){
											if($tab['id_contact'] != '' && $tab['id_contact'] > 0){
												$id = $tab['id_contact'];
												$config = $records[$tab['id_globalobject']];
												if(isset($config['mf']))$mf = $config['mf'];
												else $mf = "''";
												$rank = $this->rankResult($this->expression,$this->pertinence[$tab['id_globalobject']], self::RESULT_TYPE_CONTACT, $config['date']);
												if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_CONTACT, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
											}elseif($tab['id_contact2'] != '' && $tab['id_contact2'] > 0){
												$id = $tab['id_contact2'];
												$config = $records[$tab['id_globalobject']];
												if(isset($config['mf']))$mf = $config['mf'];
												else $mf = "''";
												$rank = $this->rankResult($this->expression,$this->pertinence[$tab['id_globalobject']], self::RESULT_TYPE_CONTACT, $config['date']);
												if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_CONTACT, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
											}elseif($tab['id_tiers'] != '' && $tab['id_tiers'] > 0){
												$id = $tab['id_tiers'];
												$config = $records[$tab['id_globalobject']];
												if(isset($config['mf']))$mf = $config['mf'];
												else $mf = "''";
												$rank = $this->rankResult($this->expression,$this->pertinence[$tab['id_globalobject']], self::RESULT_TYPE_COMPANY, $config['date']);
												if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_COMPANY, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
											}elseif($tab['id_tiers2'] != '' && $tab['id_tiers2'] > 0){
												$id = $tab['id_tiers2'];
												$config = $records[$tab['id_globalobject']];
												if(isset($config['mf']))$mf = $config['mf'];
												else $mf = "''";
												$rank = $this->rankResult($this->expression,$this->pertinence[$tab['id_globalobject']], self::RESULT_TYPE_COMPANY, $config['date']);
												if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_COMPANY, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
											}elseif($tab['id_action'] != '' && $tab['id_action'] > 0){
												$id = $tab['id_action'];
												$config = $records[$tab['id_globalobject']];
												if(isset($config['mf']))$mf = $config['mf'];
												else $mf = "''";
												$rank = $this->rankResult($this->expression,$this->pertinence[$tab['id_globalobject']], self::RESULT_TYPE_MISSION, $config['date']);
												if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_MISSION, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
											}
										}
									}
									break;
								case canton::TABLE_NAME:
									if (count($records)){
										$params = array(":iwd"=>$_SESSION['dims']['workspaceid'],":iwd2"=>1);
										$sql = 'SELECT		m.id_contact,m.id_contact2,m.id_tiers,m.id_tiers2,m.id_action, c2.id_globalobject
												FROM 		dims_matrix m
												INNER JOIN 	'.city::TABLE_NAME.' c
												ON 			c.id = m.id_city
												INNER JOIN 	'.canton::TABLE_NAME.' c2
												ON 			c2.code = c.code_canton
												AND 		c2.code_dep = c.code_dep
												AND 		c2.code_arrond = c.code_arrondissement
												AND 		c2.code_reg = c.code_reg
												WHERE		c2.id_globalobject IN ('.$this->db->getParamsFromArray(array_keys($records), 'idglobalobject', $params).')
												AND 		m.id_workspace IN (:iwd, :iwd2)';
										$res = $this->db->query($sql, $params);
										while($tab = $this->db->fetchrow($res)){
											if($tab['id_contact'] != '' && $tab['id_contact'] > 0){
												$id = $tab['id_contact'];
												$config = $records[$tab['id_globalobject']];
												if(isset($config['mf']))$mf = $config['mf'];
												else $mf = "''";
												$rank = $this->rankResult($this->expression,$this->pertinence[$tab['id_globalobject']], self::RESULT_TYPE_CONTACT, $config['date']);
												if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_CONTACT, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
											}elseif($tab['id_contact2'] != '' && $tab['id_contact2'] > 0){
												$id = $tab['id_contact2'];
												$config = $records[$tab['id_globalobject']];
												if(isset($config['mf']))$mf = $config['mf'];
												else $mf = "''";
												$rank = $this->rankResult($this->expression,$this->pertinence[$tab['id_globalobject']], self::RESULT_TYPE_CONTACT, $config['date']);
												if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_CONTACT, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
											}elseif($tab['id_tiers'] != '' && $tab['id_tiers'] > 0){
												$id = $tab['id_tiers'];
												$config = $records[$tab['id_globalobject']];
												if(isset($config['mf']))$mf = $config['mf'];
												else $mf = "''";
												$rank = $this->rankResult($this->expression,$this->pertinence[$tab['id_globalobject']], self::RESULT_TYPE_COMPANY, $config['date']);
												if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_COMPANY, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
											}elseif($tab['id_tiers2'] != '' && $tab['id_tiers2'] > 0){
												$id = $tab['id_tiers2'];
												$config = $records[$tab['id_globalobject']];
												if(isset($config['mf']))$mf = $config['mf'];
												else $mf = "''";
												$rank = $this->rankResult($this->expression,$this->pertinence[$tab['id_globalobject']], self::RESULT_TYPE_COMPANY, $config['date']);
												if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_COMPANY, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
											}elseif($tab['id_action'] != '' && $tab['id_action'] > 0){
												$id = $tab['id_action'];
												$config = $records[$tab['id_globalobject']];
												if(isset($config['mf']))$mf = $config['mf'];
												else $mf = "''";
												$rank = $this->rankResult($this->expression,$this->pertinence[$tab['id_globalobject']], self::RESULT_TYPE_MISSION, $config['date']);
												if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_MISSION, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
											}
										}
									}
									break;

								case arrondissement::TABLE_NAME:
									if (count($records)){
										$params = array(":iwd"=>$_SESSION['dims']['workspaceid'],":iwd2"=>1);
										$sql = 'SELECT		m.id_contact,m.id_contact2,m.id_tiers,m.id_tiers2,m.id_action, a.id_globalobject
												FROM 		dims_matrix m
												INNER JOIN 	'.city::TABLE_NAME.' c
												ON 			c.id = m.id_city
												INNER JOIN 	'.arrondissement::TABLE_NAME.' a
												ON 			a.code = c.code_arrondissement
												AND 		a.code_reg = c.code_reg
												AND 		a.code_dep = c.code_dep
												WHERE		a.id_globalobject IN ('.$this->db->getParamsFromArray(array_keys($records), 'idglobalobject', $params).')
												AND 		m.id_workspace IN (:iwd, :iwd2)';
										$res = $this->db->query($sql, $params);
										while($tab = $this->db->fetchrow($res)){
											if($tab['id_contact'] != '' && $tab['id_contact'] > 0){
												$id = $tab['id_contact'];
												$config = $records[$tab['id_globalobject']];
												if(isset($config['mf']))$mf = $config['mf'];
												else $mf = "''";
												$rank = $this->rankResult($this->expression,$this->pertinence[$tab['id_globalobject']], self::RESULT_TYPE_CONTACT, $config['date']);
												if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_CONTACT, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
											}elseif($tab['id_contact2'] != '' && $tab['id_contact2'] > 0){
												$id = $tab['id_contact2'];
												$config = $records[$tab['id_globalobject']];
												if(isset($config['mf']))$mf = $config['mf'];
												else $mf = "''";
												$rank = $this->rankResult($this->expression,$this->pertinence[$tab['id_globalobject']], self::RESULT_TYPE_CONTACT, $config['date']);
												if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_CONTACT, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
											}elseif($tab['id_tiers'] != '' && $tab['id_tiers'] > 0){
												$id = $tab['id_tiers'];
												$config = $records[$tab['id_globalobject']];
												if(isset($config['mf']))$mf = $config['mf'];
												else $mf = "''";
												$rank = $this->rankResult($this->expression,$this->pertinence[$tab['id_globalobject']], self::RESULT_TYPE_COMPANY, $config['date']);
												if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_COMPANY, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
											}elseif($tab['id_tiers2'] != '' && $tab['id_tiers2'] > 0){
												$id = $tab['id_tiers2'];
												$config = $records[$tab['id_globalobject']];
												if(isset($config['mf']))$mf = $config['mf'];
												else $mf = "''";
												$rank = $this->rankResult($this->expression,$this->pertinence[$tab['id_globalobject']], self::RESULT_TYPE_COMPANY, $config['date']);
												if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_COMPANY, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
											}elseif($tab['id_action'] != '' && $tab['id_action'] > 0){
												$id = $tab['id_action'];
												$config = $records[$tab['id_globalobject']];
												if(isset($config['mf']))$mf = $config['mf'];
												else $mf = "''";
												$rank = $this->rankResult($this->expression,$this->pertinence[$tab['id_globalobject']], self::RESULT_TYPE_MISSION, $config['date']);
												if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_MISSION, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
											}
										}
									}
									break;
								case suivi::TABLE_NAME:
									if (count($records)){
										$params = array();
										$sql = 'SELECT	id_globalobject
												FROM	'.suivi::TABLE_NAME.'
												WHERE	id_globalobject IN ('.$this->db->getParamsFromArray(array_keys($records), 'idglobalobject', $params).')';
										$res = $this->db->query($sql, $params);
										while($tab = $this->db->fetchrow($res)){
											$id = $tab['id_globalobject'];
											$config = $records[$id];
											if(isset($config['mf']))$mf = $config['mf'];
											else $mf = "''";
											$rank = $this->rankResult($this->expression,$this->pertinence[$id], self::RESULT_TYPE_SUIVI, $config['date']);
											if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_SUIVI, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
										}
									}
									break;
								case suividetail::TABLE_NAME:
									if (count($records)){
										$params = array();
										$sql = 'SELECT	DISTINCT s.id_globalobject, d.id_globalobject bis
												FROM	'.suivi::TABLE_NAME.' s
												INNER JOIN	'.suividetail::TABLE_NAME.' d
												ON		d.suivi_id = s.id
												AND		d.suivi_type = s.type
												AND		d.suivi_exercice = s.exercice
												WHERE	d.id_globalobject IN ('.$this->db->getParamsFromArray(array_keys($records), 'idglobalobject', $params).')';
										$res = $this->db->query($sql, $params);
										while($tab = $this->db->fetchrow($res)){
											$id = $tab['id_globalobject'];
											$config = $records[$tab['bis']];
											if(isset($config['mf']))$mf = $config['mf'];
											else $mf = "''";
											$rank = $this->rankResult($this->expression,$this->pertinence[$id], self::RESULT_TYPE_SUIVI, $config['date']);
											if(isset($id) && $id != 0) $s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_SUIVI, $id, $mf,(isset($config['sentence']))?$config['sentence']:"''", "''",$config['date'], $rank));
										}
									}
									break;
								case docfile::TABLE_NAME://documents ou pictures/videos
									//analyse sur le type de document
									if(count($records)){
										$params = array();
										$sql = 'SELECT id_globalobject, extension FROM dims_mod_doc_file WHERE id_globalobject IN('.$this->db->getParamsFromArray(array_keys($records), 'idglobalobject', $params).')';
										$res = $this->db->query($sql, $params);
										while($doc = $this->db->fetchrow($res)){
											if($doc['id_globalobject'] != 0){
												switch(strtolower($doc['extension'])){
													case 'gif':
													case 'png':
													case 'jpg':
													case 'jpeg':
													case 'tiff':
													case 'bmp':
														/*$group = self::RESULT_TYPE_PICTURE;
														break;*/
													case 'mov':
													case 'flv':
													case 'avi':
													case 'mp4':
														/*$group = self::RESULT_TYPE_MOVIE;
														break;*/
													default:
														$group = self::RESULT_TYPE_DOCUMENT;
														break;
												}
												if(isset($records[$doc['id_globalobject']]['mf']))$mf = $records[$doc['id_globalobject']]['mf'];
												else $mf = "''";

												$rank = $this->rankResult($this->expression,$this->pertinence[$doc['id_globalobject']], $group, $records[$doc['id_globalobject']]['date']);
												$s_result_manager->addBulkRow(array("''",$id_se, $user_id, $group, $doc['id_globalobject'], $mf, (isset($records[$doc['id_globalobject']]['sentence']))?$records[$doc['id_globalobject']]['sentence']:"''", "''", $records[$doc['id_globalobject']]['date'], $rank));
											}
										}
									}
									break;
								case action::TABLE_NAME://missions - fairs - activities
									//analyse sur le type de document
									if(count($records)){
										$params = array();
										$sql = 'SELECT id_globalobject, type, typeaction, id_user FROM dims_mod_business_action WHERE id_globalobject IN('.$this->db->getParamsFromArray(array_keys($records), 'idglobalobject', $params).')';
										$res = $this->db->query($sql, $params);
										while($evt = $this->db->fetchrow($res)){
											if($evt['id_globalobject'] != 0){
												if(isset($records[$evt['id_globalobject']]['mf']))$mf = $records[$evt['id_globalobject']]['mf'];
												else $mf = "''";
												switch($evt['type']){
													case dims_const::_PLANNING_ACTION_EVT: //fairs and missions
														if($evt['typeaction'] == '_DIMS_PLANNING_FAIR'){

															/*$rank = $this->rankResult($this->expression,$this->pertinence[$evt['id_globalobject']], self::RESULT_TYPE_FAIR, $records[$evt['id_globalobject']]['date']);
															$s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_FAIR, $evt['id_globalobject'], $mf, (isset($records[$evt['id_globalobject']]['sentence']))?$records[$evt['id_globalobject']]['sentence']:"''", "''", $records[$evt['id_globalobject']]['date'], $rank));*/
														}
														else { // if($evt['typeaction'] == '_DIMS_MISSIONS'){
															//dims_print_r($evt);die();
															$rank = $this->rankResult($this->expression,$this->pertinence[$evt['id_globalobject']], self::RESULT_TYPE_MISSION, $records[$evt['id_globalobject']]['date']);
															$s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_MISSION, $evt['id_globalobject'], $mf, (isset($records[$evt['id_globalobject']]['sentence']))?$records[$evt['id_globalobject']]['sentence']:"''", "''", $records[$evt['id_globalobject']]['date'], $rank));
														}
														break;
													/*case dims_const::_PLANNING_ACTION_ACTIVITY ://activities
														$dims = dims::getInstance();
														if ($dims->isActionAllowed(dims_const::_SYSTEM_ACTION_ACTIVITY_VIEW_OTHERS)){
														$rank = $this->rankResult($this->expression,$this->pertinence[$evt['id_globalobject']], self::RESULT_TYPE_ACTIVITY, $records[$evt['id_globalobject']]['date']);
														$s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_ACTIVITY, $evt['id_globalobject'], $mf, (isset($records[$evt['id_globalobject']]['sentence']))?$records[$evt['id_globalobject']]['sentence']:"''", "''", $records[$evt['id_globalobject']]['date'], $rank));
														}elseif($dims->isActionAllowed(dims_const::_SYSTEM_ACTION_ACTIVITY_VIEW_OWNS)){
															if ($evt['id_user'] == $_SESSION['dims']['userid']){
																$rank = $this->rankResult($this->expression,$this->pertinence[$evt['id_globalobject']], self::RESULT_TYPE_ACTIVITY, $records[$evt['id_globalobject']]['date']);
																$s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_ACTIVITY, $evt['id_globalobject'], $mf, (isset($records[$evt['id_globalobject']]['sentence']))?$records[$evt['id_globalobject']]['sentence']:"''", "''", $records[$evt['id_globalobject']]['date'], $rank));
															}else{
																$matrix = new search();
																$linkedObjectsIds = $matrix->exploreMatrice($_SESSION['dims']['workspaceid'], null, array($evt['id_globalobject']));
																if (!empty($linkedObjectsIds['distribution']['contacts'])) {
																	$params = array();
																	$sel = 'SELECT		u.id
																		FROM		dims_mod_business_contact c
																		INNER JOIN	dims_user u
																		ON		u.id_contact = c.id
																		WHERE		c.id_globalobject IN ('.$this->db->getParamsFromArray(array_keys($linkedObjectsIds['distribution']['contacts']), 'idglobalobject', $params).')
																		AND		u.id = :iduser';
																	$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']);
																	$res = $dims->db->query($sel, $params);
																	if ($dims->db->numrows($res) > 0){
																		$rank = $this->rankResult($this->expression,$this->pertinence[$evt['id_globalobject']], self::RESULT_TYPE_ACTIVITY, $records[$evt['id_globalobject']]['date']);
																		$s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_ACTIVITY, $evt['id_globalobject'], $mf, (isset($records[$evt['id_globalobject']]['sentence']))?$records[$evt['id_globalobject']]['sentence']:"''", "''", $records[$evt['id_globalobject']]['date'], $rank));
																	}
																}
															}
														}

														break;
													case dims_const::_PLANNING_ACTION_OPPORTUNITY ://opportunities
														$dims = dims::getInstance();
														if ($dims->isActionAllowed(dims_const::_SYSTEM_ACTION_LEAD_VIEW_OTHERS)){
														$rank = $this->rankResult($this->expression,$this->pertinence[$evt['id_globalobject']], self::RESULT_TYPE_OPPORTUNITY, $records[$evt['id_globalobject']]['date']);
														$s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_OPPORTUNITY, $evt['id_globalobject'], $mf, (isset($records[$evt['id_globalobject']]['sentence']))?$records[$evt['id_globalobject']]['sentence']:"''", "''", $records[$evt['id_globalobject']]['date'], $rank));
														}elseif($dims->isActionAllowed(dims_const::_SYSTEM_ACTION_LEAD_VIEW_OWNS)){
															if ($evt['id_user'] == $_SESSION['dims']['userid']){
																$rank = $this->rankResult($this->expression,$this->pertinence[$evt['id_globalobject']], self::RESULT_TYPE_OPPORTUNITY, $records[$evt['id_globalobject']]['date']);
																$s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_OPPORTUNITY, $evt['id_globalobject'], $mf, (isset($records[$evt['id_globalobject']]['sentence']))?$records[$evt['id_globalobject']]['sentence']:"''", "''", $records[$evt['id_globalobject']]['date'], $rank));
															}else{
																$matrix = new search();
																$linkedObjectsIds = $matrix->exploreMatrice($_SESSION['dims']['workspaceid'], null, array($evt['id_globalobject']));
																if (!empty($linkedObjectsIds['distribution']['contacts'])) {
																	$params = array();
																	$sel = 'SELECT		u.id
																		FROM		dims_mod_business_contact c
																		INNER JOIN	dims_user u
																		ON		u.id_contact = c.id
																		WHERE		c.id_globalobject IN ('.$this->db->getParamsFromArray(array_keys($linkedObjectsIds['distribution']['contacts']), 'idglobalobject', $params).')
																		AND		u.id = :iduser';
																	$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']);
																	$res = $dims->db->query($sel, $params);
																	if ($dims->db->numrows($res) > 0){
																		$rank = $this->rankResult($this->expression,$this->pertinence[$evt['id_globalobject']], self::RESULT_TYPE_OPPORTUNITY, $records[$evt['id_globalobject']]['date']);
																		$s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_OPPORTUNITY, $evt['id_globalobject'], $mf, (isset($records[$evt['id_globalobject']]['sentence']))?$records[$evt['id_globalobject']]['sentence']:"''", "''", $records[$evt['id_globalobject']]['date'], $rank));
																	}
																}
															}
														}
														break;*/
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
			$s_result_manager->bulkInsert();

			//gestion des tags -------------------
			$tags = new search_expression_tag();
			$tags->loadMatchingTags($id_se,$user_id,$optionalTags);
		}

		return $id_se;
	}

	/*
	* Cyril : Fonction permettant d'explorer la matrice selon des vecteurs de filtres qui s'appliquent sur chaque axe de la matrice
	*/
	public function searchInMatrice($user_id, $keywords = null, $current_workspace = null, $actions = null, $activities = null, $opportunities = null, $tiers = null, $contacts = null, $docs = null, $dossiers = null, $suivis = null, $years=null, $countries=null, $regions = null, $departements = null, $arrondissements = null, $cantons = null, $tags=null, $date_from='', $date_to='', $into_db = true){

		if($into_db){//si on exporte les résultats dans la base
			$s_result_manager = new search_expression_result();
			$s_result_manager->initUserResults($user_id);//vide les résultats précédents de cet utilisateur
		}
		else{
			$tab_matching = array();
		}

		if(!empty($actions) || !empty($activities) || !empty($opportunities) || !empty($tiers) || !empty($contacts) || !empty($docs) || !empty($dossiers) || !empty($suivis) || !empty($years) || !empty($countries) || !empty($date_from) || !empty($date_to) || !empty($tags) || !empty($regions) || !empty($departements) || !empty($arrondissements) || !empty($cantons)){
			$extraction = $this->exploreMatrice($current_workspace, $actions, $activities, $opportunities, $tiers, $contacts, $docs, $dossiers, $suivis, $years, $countries, $date_from, $date_to, null, $tags, $regions, $departements, $arrondissements, $cantons);
			if($into_db){
				$s_expression = new search_expression();
				$id_se = $s_expression->create($user_id, 'NULL', self::TYPE_ADVANCED_SEARCH);
			}

			if(isset($extraction) && isset($extraction['distribution'])){
				$tmp_results = $extraction['distribution'];
				$goids = $extraction['goids'];


				if(count($tmp_results)){
					if($into_db){
						$s_result_manager->initBulkRows();
					}

					//gestion des mots clefs --- RECHERCHE SIMPLIFIE UTILISANT L'INDEX ----
					if(!empty($keywords)){
						$restricted_area = array();
						if (mb_check_encoding($keywords,"UTF-8")) {
							$keywords=utf8_decode(dims_convertaccents($keywords));
						}

						$this->analyzeExpression($keywords);//cette fonction charge $this->expression avec tous les mots à plat sans les accents
						$_SESSION['dims']['modsearch']['expression']=$this->expression;
						$keyword_ids = array();
						foreach($this->expression as $tab_kw){
							$word = $tab_kw['word'];
							$type = $tab_kw['type'];

							$sql = "SELECT DISTINCT id
									FROM dims_keywords
									WHERE stype=:type
									AND word LIKE :word";
							$res = $this->db->query($sql, array(
								':type' => array('type' => PDO::PARAM_INT, 'value' => $type),
								':word' => array('type' => PDO::PARAM_INT, 'value' => $word.'%'),
							));//on chope tous les keywords qui matchent (dans le début du texte) avec les mots clefs recherchés
							while($tab = $this->db->fetchrow($res)){
								$keyword_ids[] = $tab['id'];
							}
						}
						if(empty($keyword_ids))$keyword_ids[] = -1;
						$params = array();
						$sql = "SELECT DISTINCT ki.id_globalobject, ki.id_metafield, ki.id_sentence, go.timestamp
								FROM dims_keywords_index ki
								INNER JOIN dims_globalobject go ON go.id = ki.id_globalobject
								WHERE ki.id_keyword IN (".$this->db->getParamsFromArray($keyword_ids, 'idkeyword', $params).")
								AND ki.id_globalobject IN (".$this->db->getParamsFromArray($goids, 'idglobalobject', $params).") ";
						if(isset($_SESSION['dims']['workspaceid']) && !empty($_SESSION['dims']['workspaceid'])){
							$sql .= " AND ki.id_workspace = :idw ";
							$params[':idw'] = array('value'=>$_SESSION['dims']['workspaceid'],'type'=>PDO::PARAM_INT);
						}

						$res = $this->db->query($sql, $params);//là on chope les metas infos des objects qui matchent avec les mots clefs
						$this->min_date = date('YmdHis');
						$this->max_date = 0;
						while($tab = $this->db->fetchrow($res)){
							$restricted_area[$tab['id_globalobject']]['id'] = $tab['id_globalobject'];
							$restricted_area[$tab['id_globalobject']]['mf'] = $tab['id_metafield'];
							$restricted_area[$tab['id_globalobject']]['sentence'] = $tab['id_sentence'];
							$restricted_area[$tab['id_globalobject']]['date'] = $tab['timestamp'];
							if($tab['timestamp'] < $this->min_date) $this->min_date = $tab['timestamp'];
							if($tab['timestamp'] > $this->max_date) $this->max_date = $tab['timestamp'];
						}
					}



					//traitement des cas simples
					if(!empty($tmp_results['activities'])){
						foreach($tmp_results['activities'] as $id => $context){
							if(!isset($restricted_area) || array_key_exists($id, $restricted_area) ){
								if(!isset($restricted_area)){
									$mf = $sentence = "''";
								}
								else{
									$mf = $restricted_area[$id]['mf'];
									$sentence = $restricted_area[$id]['sentence'];
								}
								//gestion de la référence sans warning ;)
								if(isset($context['ref']) && $context['ref']!=0){
									$ref=$context['ref'];
								}
								else $ref = 0;

								//pas besoin de la pertinence sur les mots-clefs, on fait une recherche 100%
								$rk_date = date('YmdHis');;
								if(isset($restricted_area[$id]['date'])) $rk_date = $restricted_area[$id]['date'];
								$rank = $this->rankResult(null,null, self::RESULT_TYPE_ACTIVITY, $rk_date);
								if($into_db){
									$s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_ACTIVITY, $id, $mf, $sentence, $ref, $context['date'], $rank));
								}
								else{
									$tab_matching[self::RESULT_TYPE_ACTIVITY][$id] = $id;
								}
							}
						}
					}
					if(!empty($tmp_results['suivis'])){
						foreach($tmp_results['suivis'] as $id => $context){
							if(!isset($restricted_area) || array_key_exists($id, $restricted_area) ){
								if(!isset($restricted_area)){
									$mf = $sentence = "''";
								}
								else{
									$mf = $restricted_area[$id]['mf'];
									$sentence = $restricted_area[$id]['sentence'];
								}
								//gestion de la référence sans warning ;)
								if(isset($context['ref']) && $context['ref']!=0){
									$ref=$context['ref'];
								}
								else $ref = 0;

								//pas besoin de la pertinence sur les mots-clefs, on fait une recherche 100%
								$rk_date = date('YmdHis');;
								if(isset($restricted_area[$id]['date'])) $rk_date = $restricted_area[$id]['date'];
								$rank = $this->rankResult(null,null, self::RESULT_TYPE_SUIVI, $rk_date);
								if($into_db){
									$s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_SUIVI, $id, $mf, $sentence, $ref, $context['date'], $rank));
								}
								else{
									$tab_matching[self::RESULT_TYPE_SUIVI][$id] = $id;
								}
							}
						}
					}
					if(!empty($tmp_results['opportunities'])){
						foreach($tmp_results['opportunities'] as $id => $context){
							if(!isset($restricted_area) || array_key_exists($id, $restricted_area) ){
								if(!isset($restricted_area)){
									$mf = $sentence = "''";
								}
								else{
									$mf = $restricted_area[$id]['mf'];
									$sentence = $restricted_area[$id]['sentence'];
								}
								//gestion de la référence sans warning ;)
								if(isset($context['ref']) && $context['ref']!=0){
									$ref=$context['ref'];
								}
								else $ref = 0;

								//pas besoin de la pertinence sur les mots-clefs, on fait une recherche 100%
								$rk_date = date('YmdHis');;
								if(isset($restricted_area[$id]['date'])) $rk_date = $restricted_area[$id]['date'];
								$rank = $this->rankResult(null,null, self::RESULT_TYPE_OPPORTUNITY, $rk_date);
								if($into_db){
									$s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_OPPORTUNITY, $id, $mf, $sentence, $ref, $context['date'], $rank));
								}
								else{
									$tab_matching[self::RESULT_TYPE_OPPORTUNITY][$id] = $id;
								}
							}
						}
					}

					if(!empty($tmp_results['tiers'])){
						$params = array();
						$sql = 'SELECT distinct id_globalobject FROM dims_mod_business_tiers WHERE id_globalobject IN('.$this->db->getParamsFromArray(array_keys($tmp_results['tiers']), 'idglobalobject', $params).') AND inactif='.tiers::TIERS_ACTIF;
						if(isset($_SESSION['dims']['workspaceid']) && !empty($_SESSION['dims']['workspaceid'])){
							$sql .= " AND id_workspace = :idw ";
							$params['idw'] = array('type'=>PDO::PARAM_INT,'value'=>$_SESSION['dims']['workspaceid']);
						}
						$res = $this->db->query($sql, $params);
						while($tiers = $this->db->fetchrow($res)){
							$id = $tiers['id_globalobject'];
							$context = $tmp_results['tiers'][$id];
							if(!isset($restricted_area) || array_key_exists($id, $restricted_area) ){
								if(!isset($restricted_area)){
									$mf = $sentence = "''";
								}
								else{
									$mf = $restricted_area[$id]['mf'];
									$sentence = $restricted_area[$id]['sentence'];
								}
								//gestion de la référence sans warning ;)
								if(isset($context['ref']) && $context['ref']!=0){
									$ref=$context['ref'];
								}
								else $ref = 0;

								$rk_date = date('YmdHis');;
								if(isset($restricted_area[$id]['date'])) $rk_date = $restricted_area[$id]['date'];
								$rank = $this->rankResult(null,null, self::RESULT_TYPE_COMPANY, $rk_date);
								if($into_db){
									$s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_COMPANY, $id, $mf, $sentence, $ref, $context['date'], $rank));
								}
								else{
									$tab_matching[self::RESULT_TYPE_COMPANY][$id] = $id;
								}
							}
						}
					}

					if(!empty($tmp_results['contacts'])){
						$params = array();
						$sql = 'SELECT  distinct id_globalobject FROM dims_mod_business_contact WHERE id_globalobject IN('.$this->db->getParamsFromArray(array_keys($tmp_results['contacts']), 'idglobalobject', $params).') AND inactif='.contact::CONTACT_ACTIF;
						if(isset($_SESSION['dims']['workspaceid']) && !empty($_SESSION['dims']['workspaceid'])){
							$sql .= " AND id_workspace = :idw ";
							$params['idw'] = array('type'=>PDO::PARAM_INT,'value'=>$_SESSION['dims']['workspaceid']);
						}

						$res = $this->db->query($sql, $params);

						while($ct = $this->db->fetchrow($res)){
							$id = $ct['id_globalobject'];
							$context = $tmp_results['contacts'][$id];

							if(!isset($restricted_area) || array_key_exists($id, $restricted_area) ){
								if(!isset($restricted_area)){
									$mf = $sentence = "''";
								}
								else{
									$mf = $restricted_area[$id]['mf'];
									$sentence = $restricted_area[$id]['sentence'];
								}
								//gestion de la référence sans warning ;)
								if(isset($context['ref']) && $context['ref']!=0){
									$ref=$context['ref'];
								}
								else $ref = 0;
								$rk_date = date('YmdHis');;
								if(isset($restricted_area[$id]['date'])) $rk_date = $restricted_area[$id]['date'];
								$rank = $this->rankResult(null,null, self::RESULT_TYPE_CONTACT, $rk_date);
								if($into_db){
									$s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_CONTACT, $id, $mf, $sentence, $ref, $context['date'], $rank));
								}
								else{
									$tab_matching[self::RESULT_TYPE_CONTACT][$id] = $id;
								}
							}
						}
					}

					//traitement spécifique pour les events et docs pour déterminer leur type (Mission, foires, pictures, movies, docs)
					if(!empty($tmp_results['events'])){

						$params = array();
						$sql = 'SELECT distinct id_globalobject,typeaction FROM dims_mod_business_action WHERE id_globalobject IN('.$this->db->getParamsFromArray(array_keys($tmp_results['events']), 'idglobalobject', $params).')';
						$res = $this->db->query($sql, $params);
						while($evt = $this->db->fetchrow($res)){
							if($evt['id_globalobject'] != 0 && (!isset($restricted_area) || array_key_exists($evt['id_globalobject'], $restricted_area))){
								if(!isset($restricted_area)){
									$mf = $sentence = "''";
								}
								else{
									$mf = $restricted_area[$evt['id_globalobject']]['mf'];
									$sentence = $restricted_area[$evt['id_globalobject']]['sentence'];
								}
								//gestion de la référence sans warning ;)
								if(isset($tmp_results['events'][$evt['id_globalobject']]['ref']) && $tmp_results['events'][$evt['id_globalobject']]['ref']!=0){
									$ref=$tmp_results['events'][$evt['id_globalobject']]['ref'];
								}
								else $ref = 0;

								$rk_date = date('YmdHis');;
								if(isset($restricted_area[$evt['id_globalobject']]['date'])) $rk_date = $restricted_area[$evt['id_globalobject']]['date'];
								if($evt['typeaction'] == '_DIMS_PLANNING_FAIR'){
									$rank = $this->rankResult(null,null, self::RESULT_TYPE_FAIR, $rk_date);
									if($into_db){
										$s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_FAIR, $evt['id_globalobject'], $mf, $sentence, $ref, $tmp_results['events'][$evt['id_globalobject']]['date'], $rank));
									}
									else{
										$tab_matching[self::RESULT_TYPE_FAIR][$evt['id_globalobject']] = $evt['id_globalobject'];
									}
								}
								else { //if($evt['typeaction'] == '_DIMS_MISSIONS'){
									$rank = $this->rankResult(null,null, self::RESULT_TYPE_MISSION, $rk_date);
									if($into_db){
										$s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_MISSION, $evt['id_globalobject'], $mf, $sentence, $ref, $tmp_results['events'][$evt['id_globalobject']]['date'], $rank));
									}
									else{
										$tab_matching[self::RESULT_TYPE_MISSION][$evt['id_globalobject']] = $evt['id_globalobject'];
									}
								}
							}
						}
					}

					//docs maintenant
					if(!empty($tmp_results['docs'])){
						$params = array();
						$sql = 'SELECT id_globalobject, extension FROM dims_mod_doc_file WHERE id_globalobject IN('.$this->db->getParamsFromArray(array_keys($tmp_results['docs']), 'idglobalobject', $params).')';
						if(isset($_SESSION['dims']['workspaceid']) && !empty($_SESSION['dims']['workspaceid'])){
							$sql .= " AND id_workspace = :idw ";
							$params['idw'] = array('type'=>PDO::PARAM_INT,'value'=>$_SESSION['dims']['workspaceid']);
						}
						$res = $this->db->query($sql, $params);
						while($doc = $this->db->fetchrow($res)){
							if($doc['id_globalobject'] != 0 && (!isset($restricted_area) || array_key_exists($doc['id_globalobject'], $restricted_area))){
								if(!isset($restricted_area)){
									$mf = $sentence = "''";
								}
								else{
									$mf = $restricted_area[$doc['id_globalobject']]['mf'];
									$sentence = $restricted_area[$doc['id_globalobject']]['sentence'];
								}
								//gestion de la référence sans warning ;)
								if(isset($tmp_results['docs'][$doc['id_globalobject']]['ref']) && $tmp_results['docs'][$doc['id_globalobject']]['ref']!=0){
									$ref=$tmp_results['docs'][$doc['id_globalobject']]['ref'];
								}
								else $ref = 0;

								$rk_date = date('YmdHis');
								if(isset($restricted_area[$doc['id_globalobject']]['date'])) $rk_date = $restricted_area[$doc['id_globalobject']]['date'];
								switch(strtolower($doc['extension'])){
									case 'gif':
									case 'png':
									case 'jpg':
									case 'jpeg':
									case 'tiff':
									case 'bmp':
										$group = self::RESULT_TYPE_PICTURE;
										break;
									case 'mov':
									case 'flv':
									case 'avi':
									case 'mp4':
										$group = self::RESULT_TYPE_MOVIE;
										break;
									default:
										$group = self::RESULT_TYPE_DOCUMENT;
										break;
								}
								$rank = $this->rankResult(null,null, $group, $rk_date);
								if($into_db){
									$s_result_manager->addBulkRow(array("''",$id_se, $user_id, $group, $doc['id_globalobject'], $mf, $sentence, $ref, $tmp_results['docs'][$doc['id_globalobject']]['date'], $rank));
								}
								else{
									$tab_matching[$group][$doc['id_globalobject']] = $doc['id_globalobject'];
								}
							}
						}
					}

					// ville => tiers / contacts
					// on suppose que c'est un tag géographique qui a remonté la ville
					if(!empty($tmp_results['cities'])){
						require_once DIMS_APP_PATH.'modules/system/class_address.php';
						$params = array();
						$sql = "SELECT 		dg.*, c.id_globalobject as gocitie
								FROM 		dims_globalobject dg
								INNER JOIN 	".address_link::TABLE_NAME." lk
								ON 			lk.id_goobject = dg.id
								INNER JOIN 	".address::TABLE_NAME." a
								ON 			lk.id_goaddress = a.id_globalobject
								INNER JOIN 	".city::TABLE_NAME." c
								ON 			a.id_city = c.id
								WHERE 		c.id_globalobject IN (".$this->db->getParamsFromArray(array_keys($tmp_results['cities']), 'cities', $params).")";
						$res = $this->db->query($sql, $params);
						$rk_date = date('YmdHis');
						$mf = $sentence = "''";
						while($evt = $this->db->fetchrow($res)){
							if(isset($tmp_results['cities'][$evt['gocitie']]['ref']) && $tmp_results['cities'][$evt['gocitie']]['ref']!=0){
								$ref=$tmp_results['cities'][$evt['gocitie']]['ref'];
							}else
								$ref = 0;
							switch ($evt['id_object']) {
								case contact::MY_GLOBALOBJECT_CODE:
									$rank = $this->rankResult(null,null, self::RESULT_TYPE_CONTACT, $rk_date);
									if($into_db){
										$s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_CONTACT, $evt['id'], $mf, $sentence, $ref, $tmp_results['cities'][$evt['gocitie']]['date'], $rank));
									}
									else{
										$tab_matching[self::RESULT_TYPE_CONTACT][$evt['id']] = $evt['id'];
									}
									break;
								case tiers::MY_GLOBALOBJECT_CODE:
									$rank = $this->rankResult(null,null, self::RESULT_TYPE_COMPANY, $rk_date);
									if($into_db){
										$s_result_manager->addBulkRow(array("''",$id_se, $user_id, self::RESULT_TYPE_COMPANY, $evt['id'], $mf, $sentence, $ref, $tmp_results['cities'][$evt['gocitie']]['date'], $rank));
									}
									else{
										$tab_matching[self::RESULT_TYPE_COMPANY][$evt['id']] = $evt['id'];
									}
									break;
							}
						}
					}
					if(!empty($tmp_results['tags'])){
						$tags = array_unique(array_merge($tags,array_keys($tmp_results['tags'])));
					}
					if($into_db || !empty($tags)){
						$s_result_manager->bulkInsert();
						//gestion des tags -------------------
						$Etags = new search_expression_tag();
						$Etags->loadMatchingTags($id_se,$user_id,$tags);
					}
				}
			}
			if($into_db){
				return $id_se;
			}
			else return $tab_matching;
		}
		return null;
	}

	/*
	* Cyril 28/12/2011 : Fonction permettant d'explorer la matrice afin de retrouver toutes les lignes matchant avec le contexte donné en param
	*/
	public function exploreMatrice($current_workspace = null, $actions = null, $activities = null, $opportunities = null, $tiers = null, $contacts = null, $docs = null, $dossiers = null, $suivis = null, $years=null, $countries=null, $date_from='', $date_to='', $appointment_offers = null, $tags=null, $regions=null, $departements=null, $arrondissements=null, $cantons=null){
		//si on a au moins un des axes renseignés
		if(!empty($actions) || !empty($activities) || !empty($opportunities) || !empty($tiers) || !empty($contacts) || !empty($docs) || !empty($dossiers) || !empty($suivis) || !empty($years) || !empty($countries) || !empty($date_from) || !empty($date_to) || !empty($appointment_offers) || !empty($tags) || !empty($regions) || !empty($departements) || !empty($arrondissements) || !empty($cantons)){
			$where = '';
			$params = array();
			if(!empty($actions)){
				// suppression de la valeur 0
				$k = array_search(0, $actions);
				if ($k !== false) {
					unset($actions[$k]);
				}
				if (sizeof($actions)) {
					$where .= ' AND id_action IN ('.$this->db->getParamsFromArray($actions, 'idaction', $params) .') ';
				}
			}

			if(!empty($activities)){
				// suppression de la valeur 0
				$k = array_search(0, $activities);
				if ($k !== false) {
					unset($activities[$k]);
				}
				if (sizeof($activities)) {
					$where .= ' AND id_activity IN ('.$this->db->getParamsFromArray($activities, 'idactivity', $params) .') ';
				}
			}

			if(!empty($opportunities)){
				// suppression de la valeur 0
				$k = array_search(0, $opportunities);
				if ($k !== false) {
					unset($opportunities[$k]);
				}
				if (sizeof($opportunities)) {
					$where .= ' AND id_opportunity IN ('.$this->db->getParamsFromArray($opportunities, 'idopportunity', $params) .') ';
				}
			}

			if(!empty($appointment_offers)){
				// suppression de la valeur 0
				$k = array_search(0, $appointment_offers);
				if ($k !== false) {
					unset($appointment_offers[$k]);
				}
				if (sizeof($appointment_offers)) {
					$where .= ' AND id_appointment_offer IN ('.$this->db->getParamsFromArray($appointment_offers, 'idappoinrtmentoffer', $params) .') ';
				}
			}

			if(!empty($tiers)){
				// suppression de la valeur 0
				$k = array_search(0, $tiers);
				if ($k !== false) {
					unset($tiers[$k]);
				}
				if (sizeof($tiers)) {
					$where .= ' AND id_tiers IN ('.$this->db->getParamsFromArray($tiers, 'idtier', $params) .') ';
				}
			}

			if(!empty($contacts)){
				// suppression de la valeur 0
				$k = array_search(0, $contacts);
				if ($k !== false) {
					unset($contacts[$k]);
				}
				if (sizeof($contacts)) {
					$where .= ' AND id_contact IN ('.$this->db->getParamsFromArray($contacts, 'idcontact', $params) .') ';
				}
			}

			if(!empty($docs)){
				// suppression de la valeur 0
				$k = array_search(0, $docs);
				if ($k !== false) {
					unset($docs[$k]);
				}
				if (sizeof($docs)) {
					$where .= ' AND id_doc IN ('.$this->db->getParamsFromArray($docs, 'iddoc', $params) .') ';
				}
			}

			if(!empty($dossiers)){
				// suppression de la valeur 0
				$k = array_search(0, $dossiers);
				if ($k !== false) {
					unset($dossiers[$k]);
				}
				if (sizeof($dossiers)) {
					$where .= ' AND id_case IN ('.$this->db->getParamsFromArray($dossiers, 'idcase', $params) .') ';
				}
			}

			if(!empty($suivis)){
				// suppression de la valeur 0
				$k = array_search(0, $suivis);
				if ($k !== false) {
					unset($suivis[$k]);
				}
				if (sizeof($suivis)) {
					$where .= ' AND id_suivi IN ('.$this->db->getParamsFromArray($suivis, 'idsuivi', $params) .') ';
				}
			}

			if(!empty($years)){
				// suppression de la valeur 0
				$k = array_search(0, $years);
				if ($k !== false) {
					unset($years[$k]);
				}
				if (sizeof($years)) {
					$where .= ' AND year IN ('.$this->db->getParamsFromArray($years, 'years', $params) .') ';
				}
			}

			if(!empty($regions)){
				// suppression de la valeur 0
				$k = array_search(0, $regions);
				if ($k !== false) {
					unset($regions[$k]);
				}
				if (sizeof($regions)) {
					$where .= ' AND id_region IN ('.$this->db->getParamsFromArray($regions, 'regions', $params) .') ';
				}
			}

			if(!empty($departements)){
				// suppression de la valeur 0
				$k = array_search(0, $departements);
				if ($k !== false) {
					unset($departements[$k]);
				}
				if (sizeof($departements)) {
					$where .= ' AND id_departement IN ('.$this->db->getParamsFromArray($departements, 'departements', $params) .') ';
				}
			}

			if(!empty($arrondissements)){
				// suppression de la valeur 0
				$k = array_search(0, $arrondissements);
				if ($k !== false) {
					unset($arrondissements[$k]);
				}
				if (sizeof($arrondissements)) {
					$where .= ' AND id_arrondissement IN ('.$this->db->getParamsFromArray($arrondissements, 'arrondissements', $params) .') ';
				}
			}

			if(!empty($cantons)){
				// suppression de la valeur 0
				$k = array_search(0, $cantons);
				if ($k !== false) {
					unset($cantons[$k]);
				}
				if (sizeof($cantons)) {
					$where .= ' AND id_canton IN ('.$this->db->getParamsFromArray($cantons, 'cantons', $params) .') ';
				}
			}

			if(!empty($countries)){
				// suppression de la valeur 0
				$k = array_search(0, $countries);
				if ($k !== false) {
					unset($countries[$k]);
				}
				if (sizeof($countries)) {
					$where .= ' AND id_country IN ('.$this->db->getParamsFromArray($countries, 'idcountry', $params) .') ';
				}
			}

			if(!empty($tags)){
				// suppression de la valeur 0
				$k = array_search(0, $tags);
				if ($k !== false) {
					unset($tags[$k]);
				}
				if (sizeof($tags)) {
					if(!empty($actions) || !empty($activities) || !empty($opportunities) || !empty($tiers) || !empty($contacts) || !empty($docs) || !empty($dossiers) || !empty($suivis) || !empty($countries) || !empty($appointment_offers) || !empty($regions) || !empty($departements) || !empty($arrondissements) || !empty($cantons)){
						$params3 = array();
						$sql = "SELECT 	*
								FROM 	dims_matrix
								WHERE 	id_tag IN (".$this->db->getParamsFromArray($tags, 'idtag', $params3).") ";
						if(isset($_SESSION['dims']['workspaceid']) && !empty($_SESSION['dims']['workspaceid'])){
							$sql .= " AND id_workspace = :idw ";
							$params3['idw'] = array('type'=>PDO::PARAM_INT,'value'=>$_SESSION['dims']['workspaceid']);
						}
						if(!empty($years)){
							if (sizeof($years)) {
								$sql .= ' AND year IN ('.$this->db->getParamsFromArray($years, 'years', $params3) .') ';
							}
						}
						if(!empty($date_from)){
							if(!empty($date_to)) {
								$sql .= ' AND timestp_modify >= :timestampmodifyfrom';
								$params3[':timestampmodifyfrom'] = array('type' => PDO::PARAM_INT, 'value' => $date_from);
							}
							else {
								$sql .= ' AND timestp_modify >= :timestampmodifyfrom AND timestp_modify <= :timestampmodifyto' ;
								$params3[':timestampmodifyfrom'] = array('type' => PDO::PARAM_INT, 'value' => $date_from);
								$params3[':timestampmodifyto'] = array('type' => PDO::PARAM_INT, 'value' => substr($date_from, 0, 8).'235959');
							}
						}
						if(!empty($date_to)){
							$sql .= ' AND timestp_modify <= :timestampmodifyto';
							$params3[':timestampmodifyto'] = array('type' => PDO::PARAM_INT, 'value' => $date_to);
						}
						$res = $this->db->query($sql,$params3);
						if($this->db->numrows($res) > 0){
							$lstRes = array();
							while($r = $this->db->fetchrow($res)){
								if($r['id_action'] != '' && $r['id_action'] > 0){
									$lstRes['id_action'][$r['id_action']] = $r['id_action'];
								}
								if($r['id_activity'] != '' && $r['id_activity'] > 0){
									$lstRes['id_activity'][$r['id_activity']] = $r['id_activity'];
								}
								if($r['id_opportunity'] != '' && $r['id_opportunity'] > 0){
									$lstRes['id_opportunity'][$r['id_opportunity']] = $r['id_opportunity'];
								}
								if($r['id_appointment_offer'] != '' && $r['id_appointment_offer'] > 0){
									$lstRes['id_appointment_offer'][$r['id_appointment_offer']] = $r['id_appointment_offer'];
								}
								if($r['id_tiers'] != '' && $r['id_tiers'] > 0){
									$lstRes['id_tiers'][$r['id_tiers']] = $r['id_tiers'];
								}
								if($r['id_tiers2'] != '' && $r['id_tiers2'] > 0){
									$lstRes['id_tiers2'][$r['id_tiers2']] = $r['id_tiers2'];
								}
								if($r['id_contact'] != '' && $r['id_contact'] > 0){
									$lstRes['id_contact'][$r['id_contact']] = $r['id_contact'];
								}
								if($r['id_contact2'] != '' && $r['id_contact2'] > 0){
									$lstRes['id_contact2'][$r['id_contact2']] = $r['id_contact2'];
								}
								if($r['id_doc'] != '' && $r['id_doc'] > 0){
									$lstRes['id_doc'][$r['id_doc']] = $r['id_doc'];
								}
								if($r['id_case'] != '' && $r['id_case'] > 0){
									$lstRes['id_case'][$r['id_case']] = $r['id_case'];
								}
								if($r['id_suivi'] != '' && $r['id_suivi'] > 0){
									$lstRes['id_suivi'][$r['id_suivi']] = $r['id_suivi'];
								}
								if($r['id_country'] != '' && $r['id_country'] > 0){
									$lstRes['id_country'][$r['id_country']] = $r['id_country'];
								}
								if($r['id_city'] != '' && $r['id_city'] > 0){
									$lstRes['id_city'][$r['id_city']] = $r['id_city'];
								}
								if($r['id_address'] != '' && $r['id_address'] > 0){
									$lstRes['id_address'][$r['id_address']] = $r['id_address'];
								}
								if($r['id_canton'] != '' && $r['id_canton'] > 0){
									$lstRes['id_canton'][$r['id_canton']] = $r['id_canton'];
								}
								if($r['id_arrondissement'] != '' && $r['id_arrondissement'] > 0){
									$lstRes['id_arrondissement'][$r['id_arrondissement']] = $r['id_arrondissement'];
								}
								if($r['id_departement'] != '' && $r['id_departement'] > 0){
									$lstRes['id_departement'][$r['id_departement']] = $r['id_departement'];
								}
								if($r['id_region'] != '' && $r['id_region'] > 0){
									$lstRes['id_region'][$r['id_region']] = $r['id_region'];
								}
								if($r['id_address'] != '' && $r['id_address'] > 0){
									$lstRes['id_address'][$r['id_address']] = $r['id_address'];
								}
							}
							if(!empty($lstRes)){
								foreach($lstRes as $k => $v){
									$where .= ' AND '.$k.' IN ('.$this->db->getParamsFromArray($v, $k, $params).') ';
								}
							}
						}
					}else{
						$where .= ' AND id_tag IN ('.$this->db->getParamsFromArray($tags, 'idtag', $params) .') ';
					}
				}
			}
			if(!empty($date_from)){
				if(!empty($date_to)) {
					$where .= ' AND timestp_modify >= :timestampmodifyfrom';
					$params[':timestampmodifyfrom'] = array('type' => PDO::PARAM_INT, 'value' => $date_from);
				}
				else {
					$where .= ' AND timestp_modify >= :timestampmodifyfrom AND timestp_modify <= :timestampmodifyto' ;
					$params[':timestampmodifyfrom'] = array('type' => PDO::PARAM_INT, 'value' => $date_from);
					$params[':timestampmodifyto'] = array('type' => PDO::PARAM_INT, 'value' => substr($date_from, 0, 8).'235959');
				}
			}
			if(!empty($date_to)){
				$where .= ' AND timestp_modify <= :timestampmodifyto';
				$params[':timestampmodifyto'] = array('type' => PDO::PARAM_INT, 'value' => $date_to);
			}

			if ($where != '') {
				if(isset($_SESSION['dims']['workspaceid']) && !empty($_SESSION['dims']['workspaceid'])){
					$where .= " AND id_workspace = :idw ";
					$params['idw'] = array('type'=>PDO::PARAM_INT,'value'=>$_SESSION['dims']['workspaceid']);
				}
				$sql = 'SELECT	distinct id_action,
								id_activity,
								id_opportunity,
								id_appointment_offer,
								id_tiers,
								id_tiers2,
								id_contact,
								id_contact2,
								id_doc,
								id_case,
								id_suivi,
								id_country,
								id_city,
								id_address,
								id_tag,
								year,
								timestp_modify,
								id_workspace
						FROM dims_matrix WHERE 1=1 '.$where.'
						AND (id_tiers>0 AND id_contact=0 OR (id_tiers=0 AND id_contact>0) OR id_activity>0 OR id_doc>0 or id_city>0) ORDER BY timestp_modify ASC';//le ASC permet d'avoir la dernière activité en référence

				$res = $this->db->query($sql, $params);
				$extraction = array();
                $stags=sizeof($tags);

				if($this->db->numrows($res) > 0){
					$tmp_results = array();
					$goids = array();
					while($fields = $this->db->fetchrow($res)){
						$ref = $fields['id_activity'];//l'activité est prioritaire sur l'action
						if($ref == 0) $ref = $fields['id_action'];

						$ref = $fields['id_opportunity'];//l'opportunité est prioritaire sur l'action
						if($ref == 0) $ref = $fields['id_action'];

						if($fields['id_action'] > 0){
							$goids[$fields['id_action']] = $fields['id_action'];
							if($ref > 0) $tmp_results['events'][$fields['id_action']]['ref'] = $ref;//on pourrait stocker un tableau d'activités de référence, mais dans les résultats on en présentera qu'un
							$tmp_results['events'][$fields['id_action']]['date'] = $fields['timestp_modify'];
						}
						if($fields['id_activity'] > 0){
							if(!isset($current_workspace) || (isset($current_workspace) && $current_workspace == $fields['id_workspace']) ){
								$goids[$fields['id_activity']] = $fields['id_activity'];
								if($fields['id_action'] > 0) $tmp_results['activities'][$fields['id_activity']]['ref'] = $fields['id_action'];//cas particulier pour les activités, on ne fait le lien que sur les actions
								$tmp_results['activities'][$fields['id_activity']]['date'] = $fields['timestp_modify'];
							}
						}
						if($fields['id_opportunity'] > 0){
							$goids[$fields['id_opportunity']] = $fields['id_opportunity'];
							if($fields['id_action'] > 0) $tmp_results['opportunities'][$fields['id_opportunity']]['ref'] = $fields['id_action'];//cas particulier pour les opportunités, on ne fait le lien que sur les actions
							$tmp_results['opportunities'][$fields['id_opportunity']]['date'] = $fields['timestp_modify'];
						}
						if($fields['id_appointment_offer'] > 0){
							$goids[$fields['id_appointment_offer']] = $fields['id_appointment_offer'];
							if($fields['id_action'] > 0) $tmp_results['appointment_offers'][$fields['id_appointment_offer']]['ref'] = $fields['id_action'];//cas particulier pour les propositions de rendez-vous, on ne fait le lien que sur les actions
							$tmp_results['appointment_offers'][$fields['id_appointment_offer']]['date'] = $fields['timestp_modify'];
						}
						if($fields['id_tiers'] > 0){
							if(!isset($current_workspace) || (isset($current_workspace) && $current_workspace == $fields['id_workspace']) ){
								$goids[$fields['id_tiers']] = $fields['id_tiers'];
								if($ref > 0) $tmp_results['tiers'][$fields['id_tiers']]['ref'] = $ref;
								$tmp_results['tiers'][$fields['id_tiers']]['date'] = $fields['timestp_modify'];

								if($fields['id_tiers2'] > 0 && $stags == 0){
									$goids[$fields['id_tiers2']] = $fields['id_tiers2'];
									$tmp_results['tiers'][$fields['id_tiers2']]['date'] = $fields['timestp_modify'];
								}
							}
						}
						if($fields['id_contact'] > 0){
							if(!isset($current_workspace) || (isset($current_workspace) && $current_workspace == $fields['id_workspace']) ){
								$goids[$fields['id_contact']] = $fields['id_contact'];
								if($ref > 0) $tmp_results['contacts'][$fields['id_contact']]['ref'] = $ref;
								$tmp_results['contacts'][$fields['id_contact']]['date'] = $fields['timestp_modify'];

								if($fields['id_contact2'] > 0 && sizeof($tags) == 0){
									$goids[$fields['id_contact2']] = $fields['id_contact2'];
									$tmp_results['contacts'][$fields['id_contact2']]['date'] = $fields['timestp_modify'];
								}
							}
						}
						if($fields['id_doc'] > 0){
							if(!isset($current_workspace) || (isset($current_workspace) && $current_workspace == $fields['id_workspace']) ){
								$goids[$fields['id_doc']] = $fields['id_doc'];
								if($ref > 0) $tmp_results['docs'][$fields['id_doc']]['ref'] = $ref;
								$tmp_results['docs'][$fields['id_doc']]['date'] = $fields['timestp_modify'];
							}
						}
						if($fields['id_case'] > 0){
							if(!isset($current_workspace) || (isset($current_workspace) && $current_workspace == $fields['id_workspace']) ){
								$goids[$fields['id_case']] = $fields['id_case'];
								if($ref > 0) $tmp_results['dossiers'][$fields['id_case']]['ref'] = $ref;
								$tmp_results['dossiers'][$fields['id_case']]['date'] = $fields['timestp_modify'];
							}
						}
						if($fields['id_suivi'] > 0){
							if(!isset($current_workspace) || (isset($current_workspace) && $current_workspace == $fields['id_workspace']) ){
								$goids[$fields['id_suivi']] = $fields['id_suivi'];
								if($ref > 0) $tmp_results['suivis'][$fields['id_suivi']]['ref'] = $ref;
								$tmp_results['suivis'][$fields['id_suivi']]['date'] = $fields['timestp_modify'];
							}
						}
						if($fields['id_country'] > 0){
							if($ref > 0) $tmp_results['countries'][$fields['id_country']]['ref'] = $ref;
							$tmp_results['countries'][$fields['id_country']]['date'] = $fields['timestp_modify'];
						}
						if($fields['year'] > 0){
							if($ref > 0) $tmp_results['years'][$fields['year']]['ref'] = $ref;
							$tmp_results['years'][$fields['year']]['date'] = $fields['timestp_modify'];
						}
						if($fields['id_city'] > 0){
							if($ref > 0) $tmp_results['cities'][$fields['id_city']]['ref'] = $ref;
							$tmp_results['cities'][$fields['id_city']]['date'] = $fields['timestp_modify'];
						}
						if($fields['id_tag'] > 0){
							if($ref > 0) $tmp_results['tags'][$fields['id_tag']]['ref'] = $ref;
							$tmp_results['tags'][$fields['id_tag']]['date'] = $fields['timestp_modify'];
						}
					}
					$extraction['distribution'] = $tmp_results;
					$extraction['goids'] = $goids;
				}

			}
			else{
				$extraction['distribution'] = array();
				$extraction['goids'] = array();
			}

			return $extraction;
		}

		return null;
	}

	/*
	* Cyril : Fonction permettant de démarrer une recherche par un tag
	*/
	public function startsWithTags($user_id, $tag){
		if(isset($tag) && !empty($tag)){
			$tags = array();
			if(!is_array($tag))
				$tags[$tag] = $tag;
			else
				$tags = $tag;
			$params = array();

			/*$sql = "SELECT DISTINCT go.id, go.id_module, go.id_object, go.timestamp
					FROM dims_tag_globalobject tg
					INNER JOIN dims_globalobject go ON go.id = tg.id_globalobject
					WHERE tg.id_tag IN (".$this->db->getParamsFromArray($tags, 'tag', $params).")";
			$res = $this->db->query($sql, $params);
			if($this->db->numrows($res) > 0){
				require_once DIMS_APP_PATH.'modules/system/class_city.php';
				require_once DIMS_APP_PATH.'modules/system/class_address.php';
				require_once DIMS_APP_PATH.'modules/system/class_address_link.php';
				$falcified_search = array();//simule la construction du tableau retourné par la recherche classique
				$this->min_date = date('YmdHis');
				$this->max_date = 0;
				$optionalTags = array();

				while($fields = $this->db->fetchrow($res)){
					if($fields['id_object'] == city::MY_GLOBALOBJECT_CODE){
						$optionalTags = $tags;
						$sel = "SELECT 		DISTINCT go.id, go.id_module, go.id_object, go.timestamp
								FROM 		dims_globalobject go
								INNER JOIN 	".address_link::TABLE_NAME." lk
								ON 			lk.id_goobject = go.id
								INNER JOIN 	".address::TABLE_NAME." a
								ON 			a.id_globalobject = lk.id_goaddress
								INNER JOIN 	".city::TABLE_NAME." c
								ON 			c.id = a.id_city
								WHERE 		c.id_globalobject = :go";
						$params2 = array(
							':go'=>array('value'=>$fields['id'],'type'=>PDO::PARAM_INT),
						);
						$res2 = $this->db->query($sel,$params2);
						while($f = $this->db->fetchrow($res2)){
							$id_module = $f['id_module'];
							$id_object = $f['id_object'];
							$id_go = $f['id'];

							if(!isset($falcified_search[$id_module][$id_object][$id_go])){
								$falcified_search[$id_module][$id_object][$id_go] = array();
							}
							$tab = array();
							$tab['id_go'] = $id_go;
							if($f['timestamp'] < $this->min_date) $this->min_date = $f['timestamp'];
							if($f['timestamp'] > $this->max_date) $this->max_date = $f['timestamp'];
							$tab['date'] = $f['timestamp'];
							$falcified_search[$id_module][$id_object][$id_go] = $tab;
						}
					}
					$id_module = $fields['id_module'];
					$id_object = $fields['id_object'];
					$id_go = $fields['id'];

					if(!isset($falcified_search[$id_module][$id_object][$id_go])){
						$falcified_search[$id_module][$id_object][$id_go] = array();
					}
					$tab = array();
					$tab['id_go'] = $id_go;
					if($fields['timestamp'] < $this->min_date) $this->min_date = $fields['timestamp'];
					if($fields['timestamp'] > $this->max_date) $this->max_date = $fields['timestamp'];
					$tab['date'] = $fields['timestamp'];
					$falcified_search[$id_module][$id_object][$id_go] = $tab;
				}
			}*/

			require_once DIMS_APP_PATH.'modules/system/class_matrix.php';
			$sql = "SELECT 		DISTINCT go.id, go.id_module, go.id_object, go.timestamp
					FROM 		dims_globalobject go
					INNER JOIN 	".matrix::TABLE_NAME." m
					ON 			(go.id = m.id_contact OR go.id = m.id_tiers OR go.id = m.id_city)
					WHERE 		m.id_tag IN (".$this->db->getParamsFromArray($tags, 'tag', $params).")
					GROUP BY 	go.id";
			$res = $this->db->query($sql, $params);
			if($this->db->numrows($res) > 0){
				require_once DIMS_APP_PATH.'modules/system/class_city.php';
				require_once DIMS_APP_PATH.'modules/system/class_address.php';
				require_once DIMS_APP_PATH.'modules/system/class_address_link.php';
				$falcified_search = array();//simule la construction du tableau retourné par la recherche classique
				$this->min_date = date('YmdHis');
				$this->max_date = 0;
				$optionalTags = $tags;

				while($fields = $this->db->fetchrow($res)){
					if($fields['id_object'] == city::MY_GLOBALOBJECT_CODE){
						$sel = "SELECT 		DISTINCT go.id, go.id_module, go.id_object, go.timestamp
								FROM 		dims_globalobject go
								INNER JOIN 	".address_link::TABLE_NAME." lk
								ON 			lk.id_goobject = go.id
								INNER JOIN 	".address::TABLE_NAME." a
								ON 			a.id_globalobject = lk.id_goaddress
								INNER JOIN 	".city::TABLE_NAME." c
								ON 			c.id = a.id_city
								WHERE 		c.id_globalobject = :go";
						$params2 = array(
							':go'=>array('value'=>$fields['id'],'type'=>PDO::PARAM_INT),
						);
						$res2 = $this->db->query($sel,$params2);
						while($f = $this->db->fetchrow($res2)){
							$id_module = $f['id_module'];
							$id_object = $f['id_object'];
							$id_go = $f['id'];

							if(!isset($falcified_search[$id_module][$id_object][$id_go])){
								$falcified_search[$id_module][$id_object][$id_go] = array();
							}
							$tab = array();
							$tab['id_go'] = $id_go;
							if($f['timestamp'] < $this->min_date) $this->min_date = $f['timestamp'];
							if($f['timestamp'] > $this->max_date) $this->max_date = $f['timestamp'];
							$tab['date'] = $f['timestamp'];
							$falcified_search[$id_module][$id_object][$id_go] = $tab;
						}
					}

					$id_module = $fields['id_module'];
					$id_object = $fields['id_object'];
					$id_go = $fields['id'];

					if(!isset($falcified_search[$id_module][$id_object][$id_go])){
						$falcified_search[$id_module][$id_object][$id_go] = array();
					}
					$tab = array();
					$tab['id_go'] = $id_go;
					if($fields['timestamp'] < $this->min_date) $this->min_date = $fields['timestamp'];
					if($fields['timestamp'] > $this->max_date) $this->max_date = $fields['timestamp'];
					$tab['date'] = $fields['timestamp'];
					$falcified_search[$id_module][$id_object][$id_go] = $tab;
				}
				return $this->insertResultsForUser($user_id, $falcified_search,$optionalTags);
			}
		}
		return null;
	}

	/*
	 * Cyril 02/01/2012 - Fonction permettant de note une note de 0 à 1 de fidélité d'un résultat de recherche
	 */

	public function rankResult($expression, $keywords, $type, $date){
		//traitement des mots clefs
		$coeff_kw = 1; // coefficient pour les keywords
		if(!empty($expression) && !empty($keywords)){
			foreach($expression as $k => $tabelem){
				//calcul du pourcentage le plus haut pour ce mot clef
				if(isset($keywords[$k])){
					$max = 0;
					foreach($keywords[$k] as $kw => $percent){
						if($percent > $max) $max = $percent;
					}

					$coeff_kw *= ($max/100);
				}
			}
		}

		if($date > 0 && $this->max_date > 0) $coeff_date = $date / $this->max_date;//$coefficient pour la date
		else $coeff_date = 1;

		//coeff sur le type de donnée - empirique

		$coeff_type = 1;

		switch($type){
			case self::RESULT_TYPE_COMPANY:
			case self::RESULT_TYPE_CONTACT:
				$coeff_type = 1;
				break;
			case self::RESULT_TYPE_ACTIVITY:
			case self::RESULT_TYPE_OPPORTUNITY:
				$coeff_type = 0.8;
				break;
			case self::RESULT_TYPE_MISSION:
				$coeff_type = 0.6;
				break;
			case self::RESULT_TYPE_FAIR:
				$coeff_type = 0.6;
				break;
			case self::RESULT_TYPE_SUIVI:
			case self::RESULT_TYPE_SUIVI_DETAIL:
				$coeff_type = 0.6;
				break;
			case self::RESULT_TYPE_ADDRESS:
				$coeff_type = 0.6;
				break;
			case self::RESULT_TYPE_DOCUMENT:
			case self::RESULT_TYPE_PICTURE:
				$coeff_type = 0.5;
				break;
			case self::RESULT_TYPE_MOVIE:
				$coeff_type = 0.4;
				break;
		}

		return $coeff_type * $coeff_date *	$coeff_kw;
	}

	public function autoSuggest($word) {
		$this->analyzeExpression($word);

		foreach ($this->expression as $k=>$elemword) {
			$this->words[$k]=$elemword;
			$word=$elemword['word'];
			$type=$elemword['type'];
			//echo "<br>".$word;
			$lstwords=array(); // on empile au fur et a mesure
			$trouve=false;
			$wordlength=strlen($word);
			$cpte=0;
			$tabcorresp=array();
			$chaineword=array();
			$curpourcent=0;
			$c=0;
			$params = array();

			$metaphone=metaphone($word);

			$metaphone2=metaphone(substr($word,0,strlen($word)-1));
			$flascii=ord(substr($word,0,1));
			$sdex=soundex($word);
			$code=substr($sdex,1);
			$coeff=75;

			$sql= "select		distinct	k.id,
								k.word,k.count
					from		dims_keywords as k";

			// on filtre sur qq champs
			if ($this->restrictfield) {
				$idmetafield = array();
				foreach ($this->tablemetafield as $elem) {
					$idmetafield[] = $elem['id'];
				}
				$sql.=" inner join dims_keywords_metafield as km on km.id_keyword=k.id and km.id_metafield in (".$this->getParamsFromArray($idmetafield, 'idmetafield', $params).")";
			}

			$sql.=" where k.stype=:type";
			$params[':type'] = array('type' => PDO::PARAM_INT, 'value' => $type);

			//switch sur taille du mot
			$le=strlen($word);

			if ($type!=1 && $type!=2)  { // texte
				if ($le<=3) {
					$sql .= " and k.word like :word ";
					$params[':word'] = array('type' => PDO::PARAM_STR, 'value' => $word.'%');
					$coeff=100;
				}
				else {
					if ($le<=5) {
						$coeff=80;
					}
					elseif($le<=7) {
						$coeff=75;
					}

					$sql.= " AND k.flascii = :flascii and (k.code = :code";
					$sql.= " OR left(k.metaphone,:lengthmetaphone)=:metaphone";
					$sql.= ")";
					$params[':flascii'] = array('type' => PDO::PARAM_STR, 'value' => $flascii);
					$params[':code'] = array('type' => PDO::PARAM_STR, 'value' => $code);
					$params[':metaphone'] = array('type' => PDO::PARAM_STR, 'value' => $metaphone);
					$params[':lengthmetaphone'] = array('type' => PDO::PARAM_INT, 'value' => $strlen(metaphone));
				}
			}
			else {
				$sql .= " and k.word like :word ";
				$params[':word'] = array('type' => PDO::PARAM_STR, 'value' => $word.'%');
				$coeff=100;
			}

			$sql.=" order by count desc";
			$rs = $this->db->query($sql);
			$arraydist=array();

			if ($this->db->numrows($rs)>0) {
				while ($fields = $this->db->fetchrow($rs)) {
					if (!isset($tabcorresp[$fields['id']])) {
						if ($coeff==100) {
							$tabcorresp[$fields['id']]=$fields;
						}
						else {
							$res= similar_text($word ,$fields['word'],$percent);
							if ($percent>=$coeff) {
								$tabcorresp[]=$fields;
							}
						}
					}
				}
			}
		}

		// on va filtrer eventuellement sur les codes
		return($tabcorresp);
	}

	private function analyzeExpression($content) {
		$content=strtolower(dims_convertaccents(html_entity_decode(($content))));

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

		for($i=0;$i<=$len;$i++) {
			if ($i==$len) $car="\n";
			else $car=$content[$i];

			$anca=$a;
			$a=ord($car);

			if ($a>=48 && $a <=57 || $a == 95 || $a>=97 && $a <=122 || $a==64 || $a==38) {
				// digits or caracteres ou &
				$word.=$car;
				$wc++;
			}
			else {
				// cas specifique : \r \n ! ?
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
						// on preserve les points uniquement pour verifier le mot complet
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

						if ($isnum && sizeof($tabword)==5) { // test si numero de telephone ok
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

							// verifions si on a un email ou numerique
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
						// on check la deuxieme partie
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
									// on a un entier, verifions si pas deja eu un chiffre de 4 num.
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
								// on a un entier, verifions si pas deja eu un chiffre de 4 num.
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

						if ($a!= 45 && $a!=47) {
							$isword=true;
							$type=0;
							$courw="";
							//echo "\nTexte : ".$word;ob_flush();
						}
						else {
							$word.=$car;
							$wc++;
						}
					}
				}
				else {
					if ($wc>0) {
						if ($a !=45 && $a !=47) {
							$isword=true;
							$type=0;

							// test si on a un entier eventuellement
							if (is_numeric($word)) {
								$type=1;
								//echo "\nInt : ".$word;ob_flush();
							}
							//else echo "\nTexte : ".$word;ob_flush();
						}
						else {
							$word.=$car;
							$wc++;
						}
					}
				}
			}

			/********************************************************************************/
			/* Traitement du mot cle a inserer												*/
			/********************************************************************************/
			if ($isword) {
				// ajout du mot et son type
				$cour=$i+1;
				$isword=false;
				$word=trim($word);
				if ($word!="") {
					if (!$email && strpos($word,"@")>0) $type=3; // modification pour debut de recherche sur email
					$elem = array();
					$elem['type']=$type;
					$elem['word']=$word;

					$this->expression[]=$elem;
				}
				$word="";
			}
		}
	}
}
