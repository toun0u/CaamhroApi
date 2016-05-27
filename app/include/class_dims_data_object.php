<?php
/**
 * Generic data object class. Allow object-oriented database records manipulation.
 *
 * @version 3.0
 * @since 0.1
 *
 * @access public
 * @abstract
 *
 * @package includes
 * @subpackage data object
 *
 * @author Netlor Concept
 * @copyright � 2003 Netlor Concept
 * @license http://www.netlorconcept.com
 */

require_once DIMS_APP_PATH.'include/class_dims_globalobject.php';
require_once DIMS_APP_PATH.'include/class_object_historic.php';
require_once DIMS_APP_PATH.'modules/system/class_mb_object_relation.php';

class dims_data_object {
	public $extend			= false;
	protected $autre_base	= false;
	protected $tablename	= false;
	protected $database		= _DIMS_DB_DATABASE;

	public $fields		= array();

	//----- champs spécifiques à la synchro XML ------------
	public $matrice; //matrice de structure de la synchro
	private $prefixe;//péfixe inclut dans le nom du fichier
	private $synchronized = false;

	const DDO_SYNC_MODE_CLASSIC		= 1;
	const DDO_SYNC_MODE_AUTOID	 = 2;
	private $mode_synchro;
	private $from_import = false;
	private $extended = false;
	private $force_adding = false;
	//------------------------------------------------------

	//----- champs spécifiques au suivi de l'activité  ------------
	private $isActionsFollowed =false;//permet d'indiquer si on suit les actions réalisées sur un objet
	private $action_comment = '';
	private $action_type = '';//Create / Update / Delete
	private $action_code = '';//code de réunification. C'est quand plusieurs objets sont créés par la même action, ça fera plusieurs lignes d'histo avec le m^me code
	private $action_go_reference = 0;//pour l'historique, c'est pour indiquer que la ligne sert d'historique à l'objet en go_reference
	private $already_historised = false;//permet d'indiquer au système si la version courrante de la matrice est déjà historisé
	private $isMatriceStandalone = false;
	private $controlChangesBeforeIndex = false;//Indique s'il faut contrôler les changements sur les champs
	//------------------------------------------------------
	private $dev_attributes = array(); //permet d'ajouter des attributs utile au tpl
	//------------------------------------------------------
	private $prepared_bulk_rows =array();//permet de stocker les lignes en attente d'un bulk insert
	private $my_description = array();

	private $skip_creation_historic = false; //indique si on doit historiser la création des objet suivis
	private $skip_deletion_historic = false; //indique si on doit historiser la suppression des objet suivis

	//Cyril - 26/09/2013 -- Pilotage de l'index par l'objet
	private $indexed_fields = array();


	public $new = true ;

	private $force_timestp_modify = true; //permet de désactiver ou d'activer la modification du timestp_modify dans certains cas bien précis comme des scripts de récupération de données

	private $myGlobalObject = null;


	protected $actionType = array();

	private $id_dimsowner = 0;//id du dims auquel l'objet appartient d'un point de vue métier - lié fortement à business_tiers

	/* ------------------- PARAMETRE POUR ABSTRACTION DES REQUÊTES SQLs ----------------- */
	public static $pick = ' * ';
	public static $pick_cpt = 0;
	public static $pick_index = null;
	public static $pick_single = false;
	public static $limit = ' ';
	public static $q_params = array();
	public static $conditions = ' ';
	public static $order = ' ';
	public static $group = ' ';
	public static $joins = ' ';

	function dims_data_object() {
		$this->setid_object();
		$numargs = func_num_args();
		$this->classname = get_class($this) ;
		if ($numargs>0)
		$this->tablename = func_get_arg(0);

		$this->idfields = array();
		$this->fields = array();

		if ($numargs == 1) {// special case
			$this->idfields[0] = 'id';
			$this->fields['id'] = null;
		}
		else {
			for ($i = 1; $i < $numargs; $i++) {
				if (!is_null(func_get_arg($i))) {
					$this->idfields[$i-1] = func_get_arg($i);
					$this->fields[$this->idfields[$i-1]] = null;
				}
			}
		}

		if ($this->autre_base == false) {
			$db = dims::getInstance()->getDb();
			$this->setdb($db);
		}

		$this->new = TRUE;
		//------------- spécifique à la synchro xml -----------------------------
		if($this->isSync() || $this->isActionsFollowed() || $this->isMatriceStandalone() || $this->needControlChanges() ) {
			$this->matrice = array();
			$this->defineMatrice();//définit la structure de réception de la matrice de synchro

			if($this->isSync()) {
				if(!empty($this->tablename)) {
					if(empty($this->prefixe)) //sinon c'est qu'on lui a forcé une valeur
					{
						$subwords = explode('_', $this->tablename);
						foreach($subwords as $word) {
							$this->prefixe .= strtoupper(substr($word, 0, 1));
						}
					}
				} else $this->prefixe = 'DDO';
			}

		}
		//------------------------------------------------------------------------
	}

	function disableTimestpModification(){
		$this->force_timestp_modify = false;
	}

	function enableTimestpModification(){
		$this->force_timestp_modify = true;
	}

	function isTimestpModificationForced(){
		return $this->force_timestp_modify;
	}

	function getid_object() {
		return ( isset($this->id_globalobject) ) ? $this->id_globalobject : 0;
	}

	 // défini la variable de l'id_object
	function setid_object() {
	   // echo get_class($this) . '<br>' ;
		switch (get_class($this)) {
			case 'dims_activity':
				$this->id_globalobject = dims_const::_SYSTEM_OBJECT_ACTIVITY;
				break;
			case 'dims_opportunity':
				$this->id_globalobject = dims_const::_SYSTEM_OBJECT_OPPORTUNITY;
				break;
			case 'dims_appointment_offer':
				$this->id_globalobject = dims_const::_SYSTEM_OBJECT_APPOINTMENT_OFFER;
				break;
			case 'action' :
				$this->id_globalobject = dims_const::_SYSTEM_OBJECT_EVENT;
				break;
			case 'notaire_contact' :
			case 'courrier_contact' :
			case 'assurance_assure' :
			case 'contact' :
			case 'cpe_contact':
				$this->id_globalobject = dims_const::_SYSTEM_OBJECT_CONTACT;
				break;
			case 'newsletter' :
				$this->id_globalobject = dims_const::_SYSTEM_OBJECT_NEWSLETTER;
				break;
			case 'courrier_client_entreprise' :
			case 'notaire_client_entreprise' :
			case 'assurance_client_entreprise' :
			case 'assurance_assureur' :
			case 'tiers' :
				$this->id_globalobject = dims_const::_SYSTEM_OBJECT_TIERS;
				break;
			case 'webmail_email' :
				$this->id_globalobject = dims_const::_SYSTEM_OBJECT_MAIL;
				break;

			case 'rssfeed' :
				$this->id_globalobject = dims_const::_SYSTEM_OBJECT_RSS;
				break;
			case 'article_object' :
				$this->id_globalobject = dims_const::_SYSTEM_OBJECT_WCE_OBJECT;
				break;
			case 'rsscache' :
				$this->id_globalobject = dims_const::_SYSTEM_OBJECT_RSS_ARTICLE;
				break;

			case 'mailinglist_send' :
				$this->id_globalobject = dims_const::_SYSTEM_OBJECT_WCE_MAILLING_SEND;
				break;
			case 'mailinglist_mail' :
				$this->id_globalobject = dims_const::_SYSTEM_OBJECT_WCE_MAILLING_MAIL;
				break;
			case 'immo_object' :
				$this->id_globalobject = dims_const::_SYSTEM_OBJECT_IMMO_BIEN;
				break;
			case 'immo_client_object' :
				$this->id_globalobject = dims_const::_SYSTEM_OBJECT_IMMO_LINKS;
				break;
			default :
				if(is_subclass_of($this, 'contact')) {
					$this->id_globalobject = dims_const::_SYSTEM_OBJECT_CONTACT;
				}
				break;
			case 'cpe_client' :
				$this->id_globalobject = dims_const::_SYSTEM_OBJECT_CPE_CLIENT;
				break;
			case 'cpe_version_contrat':
				$this->id_globalobject = dims_const::_SYSTEM_OBJECT_CPE_VERSION_CONTRAT;
				break;
			case 'cpe_document':
				$this->id_globalobject = dims_const::_SYSTEM_OBJECT_CPE_DOCUMENT;
				break;
			case 'category' :
				$this->id_globalobject = dims_const::_SYSTEM_OBJECT_CATEGORY;
				break;
			case 'dims_case' :
				$this->id_globalobject = dims_const::_SYSTEM_OBJECT_CASE;
				break;
			case 'dims_faq' :
				$this->id_globalobject = dims_const::_SYSTEM_OBJECT_FAQ;
				break;
			case 'dims_glossaire' :
				$this->id_globalobject = dims_const::_SYSTEM_OBJECT_GLOSSAIRE;
				break;
			case 'suivi' :
				$this->id_globalobject = dims_const::_SYSTEM_OBJECT_SUIVI;
				break;
			case 'suividetail' :
				$this->id_globalobject = dims_const::_SYSTEM_OBJECT_SUIVI_DETAIL;
			case 'task' :
				$this->id_globalobject = dims_const::_SYSTEM_OBJECT_TASK;
				break;
		}
	}

	// défini la variable contenant le title de l'object (ex : contact => firstname lastname) pour l'enregistrement dans la table dims_globalobject
	function settitle() {
		switch (get_class($this)) {
			case 'dims_activity':
			case 'dims_appointment_offer':
			case 'action' :
				$this->title = $this->fields['libelle']; // $this->fields['typeaction'].' - '.
				break;
			case 'notaire_contact' :
			case 'contact' :
			case 'cpe_contact':
			case 'assurance_assure' :
				$this->title = $this->fields['firstname'].' '.$this->fields['lastname'];
				break;
			case 'newsletter' :
				$this->title = $this->fields['label'];
				break;
			case 'notaire_client_entreprise' :
			case 'assurance_client_entreprise' :
			case 'assurance_assureur' :
			case 'tiers' :
				$this->title = $this->fields['intitule'];
				break;
			case 'webmail_email' :
				$this->title = $this->fields['subject'];
				break;
			case 'rssfeed' :
				$this->title = $this->fields['title'];
				break;
			case 'article_object' :
				$this->title = $this->fields['label'];
				break;
			case 'rsscache' :
				$this->title = $this->fields['title'];
				break;

			case 'mailinglist_send' :
				$this->title = 'mailing list send';
				break;
			case 'mailinglist_mail' :
				$this->title = $this->fields['mail'];
				break;
			case 'immo_object' :
				$this->title = $this->fields['ref_object'];
				break;
			case 'immo_client_object' :
				$this->title = $this->fields['id_prospect'].' '.$this->fields['id_object'];
				break;
			case 'immo_dossier': // TODO : à supprimer
				$this->title = $this->fields['id_contact'].' '.$this->fields['id_object'];
				break;
			default :
				if(is_subclass_of($this, 'contact')) {
					$this->title = $this->fields['firstname'].' '.$this->fields['lastname'];
				}
				break;
			case 'cpe_client' :
				$this->title = $this->fields['intitule'];
				break;
			case 'category' :
				$this->title = $this->fields['label'];
				break;
			case 'dims_case' :
			case 'task':
				$this->title = $this->fields['label'];
				break;
			case 'dims_faq' :
			case 'dims_glossaire' :
				$this->title = $this->fields['title'];
			case 'cpe_version_contrat':
				$this->title = $this->fields['numero'];
				break;
			case 'cpe_document':
				$this->title = $this->fields['type'];
				break;
			case 'suivi':
				$this->title = $this->fields['libelle'];
				break;
			case 'suividetail' :
				$this->title = $this->fields['libelle'];
				break;
		}
	}

	function setdb($db) {
		$this->db = $db;
	}

	/**
	 * set value for each field in recordset
	 *
	 * @param array $values array of field values as prefixed_fieldname => $value
	 * @param string $prefix prefix of field names
	 *
	 * @access private
	 **/

	function setvalues($values, $prefix) {
		require_once DIMS_APP_PATH.'include/import_gpr.php';//Sécurise les entrées $_GET  / $_POST / $_FILES
		// par d�faut on r�cup�re les champs du formulaire ($values)
		$longueurprefixe = strlen($prefix);
		foreach ($values AS $key => $value) {
			$pref = substr($key,0,$longueurprefixe);
			if ($pref==$prefix) {
				$prop = substr($key,$longueurprefixe);
				$this->fields[$prop] = $value;
			}
		}
	}

	//Cyril - méthode plus simple pour faire un setvalues
	public function merge($params){
		require_once DIMS_APP_PATH.'include/import_gpr.php';//Sécurise les entrées $_GET  / $_POST / $_FILES
		$this->fields = array_merge($this->fields, $params);
		return $this;
	}

	/**
	 * Allow to open the data contains in the mysql data
	 *
	 * @param int none, one or more field to test.
	 * @return int number of records corresponding to the query
	 *
	 * @global object $db low level database access object
	 *
	 * @access private
	 */

	function open() {
		$dims = dims::getInstance();
		// id0, id1, id2, etc...
		$numargs = func_num_args();
		for ($i = 0; $i < $numargs; $i++) $id[$i] = func_get_arg($i);

		if ($numargs > 0 and $id[0]) {
			$sql = "SELECT * FROM `{$this->tablename}` WHERE `{$this->idfields[0]}` = ?";
			for ($i = 1; $i < $numargs; $i++) $sql = $sql." AND `{$this->idfields[$i]}` = ?";
			$this->resultid = $this->db->query($sql, $id);
			$this->numrows = $this->db->numrows($this->resultid);
			$this->fields = $this->db->fetchrow($this->resultid);

			for ($i = 0; $i < $numargs; $i++) $this->fields[$this->idfields[$i]] = $id[$i];

			if ($this->numrows>0) $this->new = FALSE;
			$this->updateGOOnOpenedRow();
		}
		else {
			$sql = "SELECT * FROM `{$this->tablename}`";
			$this->resultid = $this->db->query($sql);
			$this->numrows = $this->db->numrows($this->resultid);
			$count = 0;
			while ($row = $this->db->fetchrow($this->resultid)) {
				$this->fields[$count++] = $row;
			}
		}

		if($this->isSync() || $this->isActionsFollowed() || $this->isMatriceStandalone() || $this->needControlChanges() ) {
			$this->initMatriceValues();//initialise les valeurs de la matrice de synchronisation
		}
		return $this->numrows;
	}

	 //Cyril - 16/03/2012 - autoregistering du mb_object sur la base du globalobject courant
	public function checkGORegistration(){
		$id_module_type = $this->getMyModuleTypeID();
		if(array_key_exists('id_globalobject', $this->fields) && !empty($this->fields['id_module'])){
			//echo $this->id_globalobject;
			if(isset($this->id_globalobject)) {
				//dims_print_r($this->fields);
				dims_globalobject::checkRegistration($this->id_globalobject, $id_module_type, $this);
			}
		}
		else{ //c'est pas parce qu'on a pas d'id_globalobject qu'il faut renier la notion de mb_object pour $this
			//on contrôle d'abord qu'il existe pas déjà dans les mbojects connus
			if(isset($this->id_globalobject)){
				$dims = dims::getInstance();
				$mbo_registered = $dims->getMBObjects();
				if(!isset($mbo_registered[$id_module_type][$this->id_globalobject])){
					//dans ce cas on le crée
					$mbo = mb_object::create($id_module_type, $this->id_globalobject, $this);
					$dims->addNewMBObject($id_module_type, $this->id_globalobject, $mbo->fields);
				}
			}
		}
	}

	//Cyril - 16/03/2012- retourne le tablename de l'objet
	public function getTablename(){
		return $this->tablename;
	}

	function updateGOOnOpenedRow($go_object_value = null){
		// PERF_UPDATE du 28/12/2013
		// annulation du test de l'existence, consomme trop de ressource et fait appel a bcp de describe
		// + chargement de données de description des champs pour rien a ce niveau
		//if(dims::getInstance()->tableexist($this->tablename, $this->database)) {
			if($go_object_value != null ) $this->id_globalobject = $go_object_value;
			if (isset($this->id_globalobject) && isset($this->fields['id_module']) && array_key_exists('id_globalobject', $this->fields) ) {
				if ($this->id_globalobject > 0 && $this->fields['id_module'] > 0) {
					$this->settitle();
					$this->checkGORegistration();
					if (isset($this->title) && empty($this->fields['id_globalobject']) ) {
						require_once(DIMS_APP_PATH . "/include/class_dims_globalobject.php");
						$gobject = new dims_globalobject();
						$gobject->init_description();

						if (isset($dims)) {
							$mod = $dims->getModule($this->fields['id_module']);
							$id_module_type = $mod['id_module_type'];
						}
						else {
							require_once(DIMS_APP_PATH . "/modules/system/class_module.php");
							$mod = new module();
							$mod->open($this->fields['id_module']);
							$id_module_type = $mod->fields['id_module_type'];
						}

						if (!is_null($id_module_type)) {
							$gobject->fields['id_module_type'] = $id_module_type;
							$gobject->fields['id_module'] = $this->fields['id_module'];
							$gobject->fields['id_object'] = $this->id_globalobject;
							$gobject->fields['id_record'] = $this->fields[$this->idfields[0]];
							$gobject->fields['title'] = $this->title;
							$gobject->fields['id_dims_owner'] = (is_null($this->get_dimsowner()))?1:$this->get_dimsowner();
							$sql_timestp = '';
							$date_go = date('YmdHis');
							if(array_key_exists('timestp_modify', $this->fields)) {
								if(!empty($this->fields['timestp_modify'])) {
									$date_go = $this->fields['timestp_modify'];
								}
								else {
									$sql_timestp = ', timestp_modify = :timestampmodify';
									$params[':timestampmodify'] = array('type' => PDO::PARAM_INT, 'value' => $date_go);
								}
							}
							$gobject->fields['timestamp'] = $date_go;

							$id_gobject = $gobject->save();

							$id_gobject = $gobject->fields['id'];
							$this->fields['id_globalobject'] = $id_gobject;

							$sql = "UPDATE `{$this->database}`.`$this->tablename` SET `$this->tablename`.`id_globalobject` = :idglobalobject ".$sql_timestp. " WHERE `$this->tablename`.`{$this->idfields[0]}` = :idfield";
							$params[':idfield'] = array('type' => PDO::PARAM_INT, 'value' => $this->fields[$this->idfields[0]]);
							$params[':idglobalobject'] = array('type' => PDO::PARAM_INT, 'value' => $id_gobject);
							$this->db->query($sql, $params);
						}
						else {
							// TODO on doit supprimer cette ligne normalement car module non reconnu
						}
					}
				}
			}
		//}
		}

	function save() {
		$dims = dims::getInstance();
		$this->checkGORegistration();
		$id_object="";
		$execute_sql=true;
		$numargs = func_num_args();
		$this->wasnew = false;

		if($this->db == null) {
			$this->db = dims::getInstance()->getDb();
		}

		for ($i = 0; $i < $numargs; $i++) {
			if ($i==0) $id_object=func_get_arg($i);
			else $execute_sql=func_get_arg($i);
		}

		if (isset($this->id_globalobject) && isset($this->fields['id_module']) && array_key_exists('id_globalobject', $this->fields)) {
			if ($this->id_globalobject > 0 && $this->fields['id_module'] > 0
					&& isset($this->fields[$this->idfields[0]]) && $this->fields[$this->idfields[0]]>0 ) {
				$this->settitle();
				if (isset($this->title)) {
					require_once(DIMS_APP_PATH."include/class_dims_globalobject.php");
					$mod = $dims->getModule($this->fields['id_module']);
					$gobject = new dims_globalobject();
					$gobject->init_description();
					if ($this->fields['id_globalobject'] > 0) {
						$gobject->open($this->fields['id_globalobject']);
					}

					if (isset($mod['id_module_type']) && !is_null($mod['id_module_type'])) {
						$gobject->fields['id_module_type'] = $mod['id_module_type'];
						$gobject->fields['id_module'] = $this->fields['id_module'];
						$gobject->fields['id_object'] = $this->id_globalobject;
						$gobject->fields['id_record'] = $this->getId();
						$gobject->fields['title'] = $this->title;
						$gobject->fields['id_dims_owner'] = (is_null($this->get_dimsowner()))?1:$this->get_dimsowner();
						$sql_timestp = '';
						$date_go = date('YmdHis');
						$params = array();
						if(array_key_exists('timestp_modify', $this->fields)) {
							if(!empty($this->fields['timestp_modify'])) {
								$date_go = $this->fields['timestp_modify'];
							}
							else {
								$sql_timestp = ', timestp_modify = :timestampmodify';
								$params[':timestampmodify'] = array('type' => PDO::PARAM_INT, 'value' => $date_go);
							}
						}
						$gobject->fields['timestamp'] = $date_go;
						$gobject->save();
						$id_gobject = $gobject->fields['id'];
						$this->fields['id_globalobject'] = $id_gobject;
						$sql = "UPDATE `$this->tablename` SET `$this->tablename`.`id_globalobject` = :idglobalobject ".$sql_timestp. " WHERE `$this->tablename`.`{$this->idfields[0]}` = :idrowset";
						$params[':idglobalobject'] = array('type' => PDO::PARAM_INT, 'value' => $id_gobject);
						$params[':idrowset'] = array('type' => PDO::PARAM_INT, 'value' => $this->fields[$this->idfields[0]]);
						$this->db->query($sql, $params);
					}
				}
			}
		}

	$tabledescription = $this->getTableDescription();

	$fieldsname = array();
	foreach($tabledescription as $field) {
		$fieldsname[] = $field['Field'];
	}

		if ($this->new) {// insert
			$syncadd = 'add';//pour la synchro xml, pour dire qu'on doit exporter en xml en mode ajout
			$listvalues='';
			$creation_date = dims_createtimestamp();
			if (isset($this->fields['timestp_modify']) && empty($this->fields['timestp_modify'])) $this->fields['timestp_modify']=$creation_date;
			if (isset($this->fields['timestp_create']) && empty($this->fields['timestp_create'])) $this->fields['timestp_create']=$creation_date; //Cyril : modification apportée pour faciliter la gestion des dates dans schooltraining (projet LFB)

			// build insert
			foreach ($this->fields as $key => $value) {
				if(in_array($key, $fieldsname)) {
					if (!empty($listvalues)) $listvalues .= ', ';

					$listvalues .= "`$this->tablename`.`$key` = :".str_replace("-","_",$key);
				}
				else {
					unset($this->fields[$key]);
				}
			}

			if ($listvalues != '') $listvalues = "SET $listvalues";

			$sql = "INSERT INTO `$this->tablename` $listvalues;"; // construction de la requete

			if($execute_sql || $id_object!="") {
				$preparedfields = array();

				foreach($this->fields as $key => $field) {
					$preparedfields[':'.str_replace("-","_",$key)] = $field;
				}
				//cyril - 30/03/2012 - Traitement des erreurs SQL
				$exec = $this->db->query($sql, $preparedfields);
				if(is_array($exec)) //alors on a une erreur catchée
					return $exec;
			}
			$this->sql = $sql;

			// get insert id from insert (if 1 field primary key and autokey)
			if (sizeof($this->idfields) == 1 ) $this->fields[$this->idfields[0]] = $this->db->insertid();
			elseif(in_array('id',$this->idfields)) {
				$key = array_search('id',$this->idfields);
				$this->fields[$this->idfields["$key"]] = $this->db->insertid();
			}elseif(count($this->idfields) > 1){
				$extras = array();
				foreach($tabledescription as $field) {
					$extras[$field['Field']] = $field['Extra'];
				}
				foreach($this->idfields as $f){
					if(isset($this->fields[$f]) && empty($this->fields[$f]) && isset($extras[$f]) && $extras[$f] == 'auto_increment'){
						$this->fields[$f] = $this->db->insertid();
						break;
					}
				}
			}
			$this->new = FALSE;
			$this->wasnew = true;
		}
		else {// update
			$syncadd = ( ! $this->isForcedAdding() ) ? 'update' : 'add' ;//pour la synchro xml, pour dire qu'on est en mode update
			if (isset($this->fields['timestp_modify']) && $this->isTimestpModificationForced()) $this->fields['timestp_modify']=dims_createtimestamp();

			$listvalues='';
			foreach ($this->fields AS $key => $value) {
				if(in_array($key, $fieldsname)) {
					if (!in_array($key,$this->idfields)) { // field is not a key
						if (!empty($listvalues)) $listvalues .= ', ';
						$listvalues .= "`$this->tablename`.`$key` = :".str_replace("-","_",$key);
					}
				}
			}
			//$id = $this->fields[$this->idfields[0]];

			// build request
			$sql = "UPDATE `$this->tablename` SET " . $listvalues . " WHERE `$this->tablename`.`{$this->idfields[0]}` = :".$this->idfields[0];
			for ($i = 1; $i < sizeof($this->idfields); $i++) $sql = $sql." AND `$this->tablename`.`{$this->idfields[$i]}` = :".$this->idfields[$i];

			$sql.=";";
			if($execute_sql || $id_object!="") {
				$preparedfields = array();

				foreach($this->fields as $key => $field) {
					$preparedfields[':'.str_replace("-","_",$key)] = $field;
				}

				//cyril - 30/03/2012 - Traitement des erreurs SQL
				//echo $sql;
				//dims_print_r($preparedfields);
				//die();
				$exec = $this->db->query($sql, $preparedfields);
				if(is_array($exec)) //alors on a une erreur catchée
					return $exec;
			}
			$this->sql = $sql;
		}

		// update global object
		if (array_key_exists('id_globalobject', $this->fields) && isset($this->fields['id_module']) && $this->fields['id_module'] > 0
				&& isset($this->idfields[0]) && $this->idfields[0]!='' && $id_object>0) {

			// correction Pat lors de l'enregistrement d'un objet n'ayant pas d'id (pas encore enregistre)
			$this->settitle();
			if (isset($this->title)) {
				require_once DIMS_APP_PATH."/include/class_dims_globalobject.php";
				$gobject = new dims_globalobject();
				$gobject->init_description();
				if ($this->fields['id_globalobject'] > 0) {
					$gobject->open($this->fields['id_globalobject']);

					if ($gobject->fields['title'] !=$this->title) {
						$gobject->fields['title'] = $this->title;
					}//correction Cyril, la mise à jour du timestamp doit se faire quoi qu'il arrive, même si le titre n'a pas changé

					$sql_timestp = '';
					$date_go = date('YmdHis');
					if(array_key_exists('timestp_modify', $this->fields)) {
						if(!empty($this->fields['timestp_modify'])) {
							$date_go = $this->fields['timestp_modify'];
						}
					}
					$gobject->fields['timestamp'] = $date_go;
					$gobject->save();

				}
				else {
					$mod = $dims->getModule($this->fields['id_module']);
					if(isset($mod) && !empty($mod)){
						$id_module_type = $mod['id_module_type'];
					}
					else{
						require_once DIMS_APP_PATH."/modules/system/class_module.php";
						$mod = new module();
						$mod->open($this->fields['id_module']);
						$id_module_type = $mod->fields['id_module_type'];
					}

					$gobject->fields['id_module_type'] = $id_module_type;

					$gobject->fields['id_module'] = $this->fields['id_module'];
					$gobject->fields['id_object'] = $id_object;//$this->id_globalobject;
					$gobject->fields['id_record'] = $this->fields[$this->idfields[0]];
					$gobject->fields['title'] = $this->title;
					$gobject->fields['id_dims_owner'] = (is_null($this->get_dimsowner()))?1:$this->get_dimsowner();
					$sql_timestp = '';
					$date_go = date('YmdHis');
			$params = array();
					if(array_key_exists('timestp_modify', $this->fields)) {
						if(!empty($this->fields['timestp_modify'])) {
							$date_go = $this->fields['timestp_modify'];
						}
						else {
							$sql_timestp = ', timestp_modify = :timestampmodify';
				$params[':timestampmodify'] = array('type' => PDO::PARAM_INT, 'value' => $date_go);
						}
					}
					$gobject->fields['timestamp'] = $date_go;
					$gobject->save();
					$id_gobject = $gobject->fields['id'];
					$this->fields['id_globalobject'] = $id_gobject;
					//$this->save(); // enregistrement de l'id object
					// avoid recursion
					$sql = "UPDATE `$this->tablename` SET `$this->tablename`.`id_globalobject` = :idglobalobject ".$sql_timestp. " WHERE `$this->tablename`.`{$this->idfields[0]}` = :idrowset";
					$params[':idglobalobject'] = array('type' => PDO::PARAM_INT, 'value' => $id_gobject);
					$params[':idrowset'] = array('type' => PDO::PARAM_INT, 'value' => $this->fields[$this->idfields[0]]);
					//echo $sql;
					//dims_print_r($params);
					//die();
					$this->db->query($sql, $params);
				}
			}
		}

		//Cyril 20/09/2013 -- refactorisation de l'indexation
		$this->prepareindex();

		//-----------------------------------gestion de l'historique des action sur cet objet ---------------
		//Cyril - 17/10/2013 - avant on historisait que si on était en update. Maintenant on le fait aussi en création
		if($this->isActionsFollowed() && !$this->isAlreadyHistorised()) {
			if($this->wasnew) $this->action_type = 'C';//create
			else $this->action_type = 'U';//update
			if($this->action_type != 'C' || ! $this->skip_creation_historic){
				$this->historiseMatrice();
				if(!$this->isSync())$this->initMatriceValues(); //sinon on laisse la main à à la synchro qui gérera l'initialisation de la matrice
			}
		}

		//-----------------------------------gestion de l'export XML ----------------------------------------
		if($this->isSync())//on ne génère les fichiers de synchro que si on est synchronisé
		{
			//pas besoin d'exporter le fichier XML si ça provient d'un fichier XML. Dans le cas où on est en autoid,
			//le from_import dépend de l'émetteur. Il faut permettre la redistribution aux autres dims mais pas à l'émetteur
			if($this->mode_synchro == self::DDO_SYNC_MODE_AUTOID || !$this->from_import) //selon le mode de synchro le from_import contiendra soit true/false, soit false/id_dims_destinataire
			{
				//attention au mode :
				if($this->mode_synchro == self::DDO_SYNC_MODE_CLASSIC)
					$this->syncToXML($syncadd);
				else $this->syncToXML_AutoID($syncadd);
				//ensuite on réactualise la matrice, pour un éventuel futur save, sauf si on est extended :
				//explication : si un objet surchage la fonction d'import, il peut lancer d'abord parent::syncFromXML puis faire des traitements
				//complémentaires. Le premier appel fait un save, donc écrase la matrice, ce qui fait que le développeur ne pourra pas tester des
				//différences avec la matrice dans ses traitements complémentaires. Ex d'utilisation: modules/elisath/include/class_elisath_compte.php
				if(!$this->extended) $this->initMatriceValues();
			}
		}

		if($this->isMatriceStandalone() || $this->needControlChanges() ) {
			$this->initMatriceValues();
		}
		//----------------------------------------------------------------------------------------------------

		// return key (array if multiple key);
		if (sizeof($this->idfields)==1) {
			return ($this->fields[$this->idfields[0]]);
		}
		else {
			$res = array();
			foreach($this->idfields as $idfield) {
				$res[] = $this->fields[$idfield];
			}
			return ($res);
		}
	}

	//Cyril - 26/09/2013 - Méthode déclarative des champs que l'on veut indexer sur this.
	//A appeler dans le constructeur de l'objet
	public function to_index($fields){
		$checksum = md5(serialize($fields));
		$done = dims::getInstance()->getToIndexDoneOn(get_class($this));
		if( is_null($done) || $done != $checksum){//soit on l'a pas encore fait une seule fois depuis le début de la session, soit le developpeur a changé le tableau à indexer
			require_once DIMS_APP_PATH . 'modules/system/class_mb_field.php';
			//1. on met à jour le tableau des champs en mémoire
			$this->init_description();//attention que c'est en session, il faut se déco/reco si on ajoute un nouveau fields

			foreach($fields as $f){
				if(array_key_exists($f, $this->fields)){
					$this->indexed_fields[$f] = $f;
				}
			}
			//On met à jour, si nécessaire la métabase
			if(!empty($this->indexed_fields) &&
				array_key_exists('id', $this->fields) &&
				array_key_exists('id_user', $this->fields) &&
				array_key_exists('id_module', $this->fields) &&
				array_key_exists('id_workspace', $this->fields) &&
				array_key_exists('id_globalobject', $this->fields)
				)
			{

				$mbt = dims::getInstance()->getMBTable($this->tablename);
				if(empty($mbt)){
					mb_table::getTableID($this->tablename);//ça va créer la ligne dans la metabase et ajouter les données dans la session dims
					$mbt = dims::getInstance()->getMBTable($this->tablename);
				}
				$existing_fields = dims::getInstance()->getMetafieldsOf($this->tablename);
				$was_empty = empty($existing_fields);

			if($was_empty){//seconde chance - peut-être un un oubli d'init_metabase
				$all_metafields = dims::getInstance()->getMetadata();
				if(empty($all_metafields)){//c'est que c'est trop louche, on lance l'init_metabase pour être sûr que personne n'a rien oublié
					dims::getInstance()->init_metabase();
					//on retente
					$mbt = dims::getInstance()->getMBTable($this->tablename);
					$existing_fields = dims::getInstance()->getMetafieldsOf($this->tablename);
					$was_empty = empty($existing_fields);
				}
			}

				//Si ce tableau contient le moindre champ c'est que à coup sûr on a déjà id, id_user, id_...
				//Parce que l'algo de prepare_index ne charge le tableau qui si ces champs sont déjà présents
				//Par contre il ne restituera que les champs indexés, c'est pourquoi on doit conditionner les lignes
				//suivantes par un non empty sur le tableau
				if($was_empty){
					$this->logDeletionMbFields();
					//Juste pour être sûr de pas du tout avoir de doublon, on delete sur la table. ça pourrait arriver si par une erreur de manipulation
					//quelqu'un ait supprimer une des lignes mb_field de id, id_user, id ...
					if(defined('_TO_INDEX_DEBUG') && _TO_INDEX_DEBUG){
						$fp = fopen(DIMS_ROOT_PATH."tmp/toindex.log", "a");
						$traces = debug_backtrace();
						$url = ' | ';
						if(!empty($_SERVER)) $url = $_SERVER['SCRIPT_URI'].'?'.$_SERVER['QUERY_STRING'];
						fputs($fp, "#".date('d-m-Y H:i:s')." - ".get_class($this)."[".$url."] : \n");
						for($i = count($traces) - 1 ; $i > 0 ; $i--){
							fputs($fp, 'in '.$traces[$i]['file']. ' line '.$traces[$i]['line'] .' with '.$traces[$i]['function']."\n");
						}
						fputs($fp, "\n------------------------------\n");
						fclose ($fp);
					}

					$stmt = $this->db->query('DELETE FROM '.mb_field::TABLE_NAME.' WHERE id_table = '.$mbt['id']);
					if($stmt->rowCount() > 0){
						$this->db->query('TRUNCATE dims_keywords');//parce que y'a potentiellement des indexs sur ces anciens metafields
					}

					//comme ça c'est propre on peut repartir à 0
					//On intègre dans la boucle les champs qui sont quoi qu'il arrive requis à l'indexation
					$this->indexed_fields['id'] = 'id';
					$this->indexed_fields['id_user'] = 'id_user';
					$this->indexed_fields['id_module'] = 'id_module';
					$this->indexed_fields['id_workspace'] = 'id_workspace';
					$this->indexed_fields['id_globalobject'] = 'id_globalobject';
				}

				foreach($this->indexed_fields as $f){
					$id = dims::getInstance()->getMetaFieldID($this->tablename, $f);
					if(is_null($id)){//Il faut le créer
						$type = 'VARCHAR';
						$indexed = 1;
						$need_adding = true;
						if(in_array($f, array('id', 'id_user', 'id_module', 'id_workspace', 'id_globalobject'))){
							$type = 'INT';
							$indexed = 0;
							$need_adding = false;
						}
						$mbf = mb_field::build(array(
							'id_table'		=> $mbt['id'],
							'name'			=> $f,
							'label'			=> $f,
							'type'			=> $type,
							'visible'		=> 1,
							'indexed'		=> $indexed,
							'protected'		=> 1,
							'level'			=> 1,
							'from_model'	=> 1
							));

						$mbf->save();
						//il faut l'ajouter en session, mais pas les champs id, etc...
						if($need_adding) dims::getInstance()->addMetaField($this->tablename, $mbf);
					}
					else{
						//on checke juste s'il est déjà en mode from_model ou pas, si c'est pas le cas on mettra à jour
						if( ! dims::getInstance()->isMetaFieldFromModel($this->tablename, $f)){
							$mbf = mb_field::find_by(array('id' => $id), null, 1);
							$mbf->set('from_model', 1);
							$mbf->save();
							dims::getInstance()->setMetaFieldFromModel($this->tablename, $f, 1);//on met à jour la session
						}
						//pour le post-traitement
						if(!empty($existing_fields)){
							foreach($existing_fields as $idx => $name){
								if($name == $f){
									unset($existing_fields[$idx]);
									break;
								}
							}
						}
					}
				}
				//Tous les champs qui restent ne sont plus indexés
				if(!empty($existing_fields)){
					foreach($existing_fields as $name){
						$id = dims::getInstance()->getMetaFieldID($this->tablename, $name);
						$mbf = mb_field::find_by(array('id' => $id), null, 1);
						if(!empty($mbf)) $mbf->delete();
						dims::getInstance()->dropMetaField($this->tablename, $name);//pour mettre à jour la session
					}
				}
			}
			dims::getInstance()->setToIndexDoneOn(get_class($this), $checksum);
		}
		else {
			$this->init_description();
			foreach($fields as $f){
				if(array_key_exists($f, $this->fields)){
					$this->indexed_fields[$f] = $f;
				}
			}
			dims::getInstance()->setToIndexDoneOn(get_class($this), $checksum);
		}
	}

	private function logDeletionMbFields(){
		if(defined('_TO_INDEX_DEBUG') && _TO_INDEX_DEBUG){
			$fp = fopen(DIMS_ROOT_PATH."tmp/toindex.log", "a");
			$traces = debug_backtrace();
			$url = ' | ';
			if(!empty($_SERVER)) $url = $_SERVER['SCRIPT_URI'].'?'.$_SERVER['QUERY_STRING'];
			fputs($fp, "#".date('d-m-Y H:i:s')." - ".get_class($this)."[".$url."] : \n");
			for($i = count($traces) - 1 ; $i >= 0 ; $i--){
				fputs($fp, 'in '.$traces[$i]['file']. ' line '.$traces[$i]['line'] .' with '.$traces[$i]['function']."\n");
			}
			fputs($fp, "\n------------------------------\n");
			fclose ($fp);
		}
	}

	public function prepareindex(){
		//----------------------------------- CYRIL - 18/04/2012 - Refonte de la partie indexation avec nouvelle structure de la meta-base
		//on a besoin quoi qu'il arrive d'un id_mb_object + du module_type_id
		//on teste maintenant si on a quelque chose à indexer
		if( !empty( $this->tablename ) ){
			$dims = dims::getInstance();
			$my_fields = $dims->getMetafieldsOf($this->tablename);
			if( !empty($my_fields) ){//si on a des champs
				//Cyril -- 28/02/2012 - Contrôle sur les changements de l'objet qui conditionne le fait qu'on va enregistrer ou non dans pre_index. Inutile d'enregistrer si aucun changement
				$can_preindex = true;

				//Cyril 26/09/2013 -- Tests de nettoyage si jamais on est passé par un to_index et que y'a plus de to_index (le développeur à supprimer par exemple son instruction ->to_index dans le modèle)
				$fields_from_model = $dims->getMetaFieldFromModel($this->tablename);
				if(empty($this->indexed_fields) && !empty($fields_from_model)){
					include_once DIMS_APP_PATH."modules/system/class_mb_field.php";
					//alors là c'est pas normal, il faut nettoyer cela et on peut pas indexer
					$mbt = dims::getInstance()->getMBTable($this->tablename);
					$this->logDeletionMbFields();
					$this->db->query('DELETE FROM '.mb_field::TABLE_NAME.' WHERE id_table = '.$mbt['id']);
					$dims->cleanMetaFieldsOf($this->tablename);//on supprime de la session pour plus repasser dedans
					$can_preindex = false;
				}

				if( ! $this->wasnew && $this->needControlChanges() ){
					//on regarde si on a des changements
					$changes = $this->changes();
					$can_preindex = false;
					//On parcours les champs indexés.
					foreach($my_fields as $f){
						if(isset($changes[$f])){
							$can_preindex = true;
							break;
						}
					}
					//On parcours les éventuels champs non indexés en direct, mais avec impact des relations
					$external = $dims->getExternalIndexedRelation($dims->getMBClasseID(get_class($this)));
					if(!empty($external)){
						foreach($external as $ext){
							if(isset($changes[$ext])){
								$can_preindex = true;
								break;
							}
						}
					}
				}

				if($can_preindex){
					$id_mt = $this->getMyModuleTypeID();
					$id_mbo = $this->getid_object();

					if( !empty($id_mt)	&& !empty($id_mbo)){
						//parcours des clef primaires pour s'assurer qu'elles sont bien toutes settées
						$primary_ok = true;
						foreach($this->idfields as $p_key){
							if( ! isset($this->fields[$p_key])){
								$primary_ok = false;
								break;
							}
						}

						if(isset($this->fields['id_module']) && isset($this->fields['id_workspace']) && isset($this->fields['id_user']) && $primary_ok){
							$id_module=$this->fields['id_module'];
							$id_workspace=$this->fields['id_workspace'];
							$id_user=$this->fields['id_user'];
							$id_record=$this->fields[$this->idfields[0]];
							//on supprime les lignes présentes que si on a au moins l'id_module_type et l'id_mbo / avant on le faisait quoi qu'il arrive
							if ($id_record>0 && $id_mbo>0 && $id_module>0 && $id_workspace > 0 && $id_user >= 0) { //>= 0 pour le user parce que lorsqu'un compte se crée on passerait pas

								$script="DELETE FROM dims_keywords_preindex WHERE id_record=:idrecord AND id_object=:idobject AND id_module=:idmodule";
								$res=$this->db->query($script, array(
									':idrecord' => array('type' => PDO::PARAM_INT, 'value' => $id_record),
									':idobject' => array('type' => PDO::PARAM_INT, 'value' => $id_mbo),
									':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $id_module),
									));


								foreach($my_fields as $nom){
									if(!empty($this->fields[$nom])){
									$content=$this->fields[$nom]; //on va s'assurer que le champ contient bien des données
										if ($content!='') {
											$script="INSERT INTO dims_keywords_preindex
												VALUES (
													:idrecord,
													:idmetafield,
													:idobject,
													:iduser,
													:idworkspace,
													:idmodule,
													:idmoduletype,
													:name,
													:content,
													1,
													:idglobalobject
												)";
											$this->db->query($script, array(
												':idrecord'		=> array('type' => PDO::PARAM_INT, 'value' => $id_record),
												':idmetafield'		=> array('type' => PDO::PARAM_INT, 'value' => $dims->getMetaFieldID($this->tablename, $nom)),
												':idobject'		=> array('type' => PDO::PARAM_INT, 'value' => $id_mbo),
												':iduser'		=> array('type' => PDO::PARAM_INT, 'value' => $id_user),
												':idworkspace'		=> array('type' => PDO::PARAM_INT, 'value' => $id_workspace),
												':idmodule'		=> array('type' => PDO::PARAM_INT, 'value' => $id_module),
												':idmoduletype'		=> array('type' => PDO::PARAM_INT, 'value' => $id_mt),
												':idglobalobject'	=> array('type' => PDO::PARAM_INT, 'value' => (!(isset($this->fields['id_globalobject']) && $this->fields['id_globalobject'] != '' && $this->fields['id_globalobject'] > 0))?'0':$this->fields['id_globalobject']),
												':name'			=> array('type' => PDO::PARAM_STR, 'value' => $nom),
												':content'		=> array('type' => PDO::PARAM_STR, 'value' => $content),
											));
										}
									}
								}
							}
						}

						#Cyril - 14/12/2012 - Gestion de l'indexation étendue
						$relations = $dims->getMBObjectRelationsOn(get_class($this));#Retourne toutes les relations connues de l'objets
						if( isset($relations)){
							#$table[$fields['id_class_on']][$fields['id_class_to']][$fields['on']][$fields['to']] = $fields;
							foreach($relations as $id_class_to => $tab){
								foreach($tab as $col_on => $tab2){
									foreach($tab2 as $col_to => $rel){
										if($rel['type'] == mb_object_relation::MB_RELATION_BELONGS_TO){
											#Récupération de la classe distante
											$foreign_class = $dims->getMBClassDataFromID($id_class_to);
											if(isset($rel['extended_indexation'])){
												switch($rel['extended_indexation']){
													case mb_object_relation::MB_RELATION_NO_INDEX:
														break;
													case mb_object_relation::MB_RELATION_ON_ME_INDEX:
														#On teste si cette table est dans Metafield sinon ça sert à rien d'aller plus loin
														$remote_fields = $dims->getMetafieldsOf($foreign_class['tablename']);
														if( ! empty($remote_fields)){
															#On peut ouvrir le globalobject distant
															$go_relations = $this->getGOSFromRelation($foreign_class['classname'], $foreign_class['tablename'], $this->fields[$col_on], $col_to);
															if( ! empty($go_relations) ){
																foreach($go_relations as $go){
																	if( ! $go->isNew() ){
																		#Dans ce cas on ouvre une instance de l'objet distant
																		#Mais on utilise une instance de DDO pour ne bénéficier que de la couche générique KeRneL
																		$ddo = new $foreign_class['classname'];
																		$ddo->openWithGB($go->getId());
																		if( isset($ddo) && ! $ddo->isNew() && !empty($remote_fields) ){
																			#Dans ce cas, on doit enregistrer les champs indexés de l'objet remote dans pre-index dans ceux de $this
																			foreach($remote_fields as $nom){
																				if(!empty($ddo->fields[$nom])){
																					$content=$ddo->fields[$nom]; //on va s'assurer que le champ contient bien des données
																					if ($content!='') {
																						$script="INSERT INTO dims_keywords_preindex
																							VALUES (
																								:idrecord,
																								:idmetafield,
																								:idobject,
																								:iduser,
																								:idworkspace,
																								:idmodule,
																								:idmoduletype,
																								:name,
																								:content,
																								1,
																								:idglobalobject
																							)";
																						$this->db->query($script, array(
																						':idrecord'		=> array('type' => PDO::PARAM_INT, 'value' => $id_record),
																						':idmetafield'		=> array('type' => PDO::PARAM_INT, 'value' => $dims->getMetaFieldID($foreign_class['tablename'], $nom)),
																						':idobject'		=> array('type' => PDO::PARAM_INT, 'value' => $id_mbo),
																						':iduser'		=> array('type' => PDO::PARAM_INT, 'value' => $id_user),
																						':idworkspace'		=> array('type' => PDO::PARAM_INT, 'value' => $id_workspace),
																						':idmodule'		=> array('type' => PDO::PARAM_INT, 'value' => $id_module),
																						':idmoduletype'		=> array('type' => PDO::PARAM_INT, 'value' => $id_mt),
																						':idglobalobject'	=> array('type' => PDO::PARAM_INT, 'value' => (!(isset($this->fields['id_globalobject']) && $this->fields['id_globalobject'] != '' && $this->fields['id_globalobject'] > 0))?'0':$this->fields['id_globalobject']),
																						':name'			=> array('type' => PDO::PARAM_STR, 'value' => $nom),
																						':content'		=> array('type' => PDO::PARAM_STR, 'value' => $content),
																						));
																					}
																				}
																			}
																		}
																	}
																}
															}
														}
														break;
													case mb_object_relation::MB_RELATION_ON_REMOTE_INDEX:
														if( ! is_null($my_fields)){
															#On peut ouvrir le globalobject distant
															$go_relations = $this->getGOSFromRelation($foreign_class['classname'], $foreign_class['tablename'], $this->fields[$col_on], $col_to);
															if( ! empty($go_relations) ){
																foreach($go_relations as $go){
																	if( ! $go->isNew() ){
																		#Dans ce cas on ouvre une instance de l'objet distant
																		#Mais on utilise une instance de DDO pour ne bénéficier que de la couche générique KeRneL
																		$ddo = new $foreign_class['classname'];
																		$ddo->openWithGB($go->getId());
																		if( isset($ddo) && ! $ddo->isNew() && !empty($my_fields) ){
																			#Dans ce cas, on doit enregistrer les champs indexés de l'objet remote dans pre-index dans ceux de $this
																			foreach($my_fields as $nom){
																				if(!empty($this->fields[$nom])){
																					$content=$this->fields[$nom]; //on va s'assurer que le champ contient bien des données
																					if ($content!='') {
																						$script="INSERT INTO dims_keywords_preindex
																							VALUES (
																								:idrecord,
																								:idmetafield,
																								:idobject,
																								:iduser,
																								:idworkspace,
																								:idmodule,
																								:idmoduletype,
																								:name,
																								:content,
																								1,
																								:idglobalobject
																							)";
																						$this->db->query($script, array(
																						':idrecord'		=> array('type' => PDO::PARAM_INT, 'value' => $go->fields['id_record']),
																						':idmetafield'		=> array('type' => PDO::PARAM_INT, 'value' => $dims->getMetaFieldID($this->tablename, $nom)),
																						':idobject'		=> array('type' => PDO::PARAM_INT, 'value' => $go->fields['id_object']),
																						':iduser'		=> array('type' => PDO::PARAM_INT, 'value' => $ddo->fields['id_user']),
																						':idworkspace'		=> array('type' => PDO::PARAM_INT, 'value' => $ddo->fields['id_workspace']),
																						':idmodule'		=> array('type' => PDO::PARAM_INT, 'value' => $ddo->fields['id_module']),
																						':idmoduletype'		=> array('type' => PDO::PARAM_INT, 'value' => $go->fields['id_module_type']),
																						':idglobalobject'	=> array('type' => PDO::PARAM_INT, 'value' => (!(isset($ddo->fields['id_globalobject']) && $ddo->fields['id_globalobject'] != '' && $ddo->fields['id_globalobject'] > 0))?'0':$ddo->fields['id_globalobject']),
																						':name'			=> array('type' => PDO::PARAM_STR, 'value' => $nom),
																						':content'		=> array('type' => PDO::PARAM_STR, 'value' => $content),
																						));
																					}
																				}
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
				}
			}
		}
	}

	/**
	 * Allow to erase data in database
	 *
	 * @return void
	 *
	 * @access private
	 */

	function delete() {
		global $dims;
		$id_object="";
		$numargs = func_num_args();
		for ($i = 0; $i < $numargs; $i++) {
			if ($i==0) $id_object=func_get_arg($i);
		}

		$sql = "DELETE FROM $this->tablename WHERE $this->tablename.{$this->idfields[0]} = ?";
		for ($i = 1; $i < sizeof($this->idfields); $i++) $sql = $sql." AND $this->tablename.{$this->idfields[$i]} = ?";

		//gesiton de la synchro XML ----
		if(!$this->from_import)//pas besoin d'exporter le fichier XML si ça provient d'un fichier XML
		{
			if($this->isSync())//on ne génère les fichiers de synchro que si on est synchronisé
			{
				if(isset($mode_synchro) && $mode_synchro == self::DDO_SYNC_MODE_CLASSIC)
					$this->syncToXML('delete');
				else $this->syncToXML_AutoID('delete');
			}
		}

	if($id_object!="") {
		// we can index content
		// retrieve information
		// first retrieve id_module_type value
		if ($this->fields['id_module'] == '')
			$id_module = $_SESSION['dims']['moduleid'];
		else
			$id_module=$this->fields['id_module'];

		$id_record=$this->fields['id'];
		if (isset($_SESSION['dims']['modules'][$id_module]['id_module_type']))
			$id_module_type=$_SESSION['dims']['modules'][$id_module]['id_module_type'];

		// you can index now
		dims_index_drop($id_record,$id_object,$id_module);
		// suppression des informations du mur
		//$dims->deleteActions($id_module,$id_object,$id_record);

		// suppression dans les tables dims_globalobject et dims_globalobjectlink

		// -|-|-|-|-|-|-|-|-|-|-|-|-|-|--|-|- CYRIL : 26/04/2012 ->je pense sincèrement que le bloc ci-dessous ne sert à rien. Les requête delete s'appuie sur un test
		//sur $id_global > 0 alors que cette varialbe n'est pas settée plus haut ... En plus on a les mêmes requêtes 20 lignes plus bas
		if (isset($this->fields['id_object']) && isset($this->fields['id_module'])) {
			if ($this->fields['id_object'] > 0 && $this->fields['id_module'] > 0) {
				require_once(DIMS_APP_PATH . "/include/class_dims_globalobject.php");
				$gobject = new dims_globalobject(/*$this->db*/);
				$id_gobject=$gobject->getObject($this->fields['id_module'],$this->fields['id_object'],$this->fields[$this->idfields[0]],$this->title);
				if ($id_global > 0) {
					$del1 = "DELETE FROM	dims_globalobject
						WHERE		id_module_type = :idmoduletype
						AND		id_object = :idobject
						AND		id_record = :idrecord";
					$this->db->query($del1, array(
						':idmoduletype' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_module']),
						':idobject' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_object']),
						':idrecord' => array('type' => PDO::PARAM_INT, 'value' => $this->fields[$this->idfields[0]]),
					));

					$del2 = "DELETE FROM	dims_globalobject_link
						WHERE		(id_module_type_to = :idglobalobject AND id_globalobject_from = :idobject)
						OR		(id_module_type_from = :idglobalobject AND id_globalobject_to = :idobject)";
					$this->db->query($del2, array(
						':idobject' => array('type' => PDO::PARAM_INT, 'value' => $id_global),
						':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $id_global),
					));

					$del3 = "DELETE FROM	dims_sync_corresp
						WHERE		id_local_object = :idobject";
					$this->db->query($del3, array(
						':idobject' => array('type' => PDO::PARAM_INT, 'value' => $id_global),
					));
				}
			}
		}
	}

		// ajout d'un test si l'id_globalobject existe on supprime les références de toute facon
		// suppression dans les tables dims_globalobject et dims_globalobjectlink
		if (isset($this->fields['id_globalobject']) && isset($this->fields['id_globalobject'])>0) {
			// suppression de l'objet id_globalobject
			$del1 = "DELETE FROM	dims_globalobject
				WHERE			id = :idglobalobject";
			$this->db->query($del1, array(':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_globalobject'])));

			$del2 = "DELETE FROM	dims_globalobject_link
				WHERE		(id_globalobject_from = :idglobalobject)
				OR		(id_globalobject_to = :idglobalobject)";
			$this->db->query($del2, array(
				':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_globalobject']),
			));

			$del3 = "DELETE FROM	dims_sync_corresp
				WHERE		   id_local_object = :idglobalobject";
			$this->db->query($del3, array(
				':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_globalobject']),
			));
		}

		$params=array();
		if (isset($this->idfields) && is_array($this->idfields)) {
			foreach($this->idfields as $key) {

				if (isset($this->fields[$key])) {
					$params[]=$this->fields[$key];
				}
			}
		}

		if($this->isActionsFollowed() && !$this->isAlreadyHistorised() && !$this->skip_deletion_historic) {
			$this->action_type = 'D';//delete
			$this->historiseMatrice();
		}
		$this->db->query($sql, $params);
		$this->sql = $sql;
	}

	/**
	 * get table description from the MySQL server
	 *
	 * @return void
	 *
	 * @param bool $defaultValue Use mysql default value instead of empty string
	 * @global object $db low level database access object
	 *
	 * @access private
	 */
	function init_description($defaultValue = false) {
		$class = get_called_class();
		// Optimisation sur le nombre d'appel depuis un même cycle
		if (!isset($_SESSION['dims']['init_description'][$class])) {

		$description = $this->getTableDescription();
		$init_date = dims_createtimestamp();

		foreach ($description as $fields) {
			// Assure une comptatibilité avec l'ancien système
			if(!$defaultValue || $fields['Field']=='id') // modif Pat : ne doit pas mettre de valeur pour la cle
				$this->fields[$fields['Field']] = '';
			else {
				$this->fields[$fields['Field']] = $fields['Default'];//cyril : pour tenir compte des valeurs par défaut de mysql
				if($fields['Field'] == 'timestp_modify' || $fields['Field'] == 'timestp_create') {
					$this->fields[$fields['Field']] = $init_date;
				}
			}
		}
			$_SESSION['dims']['init_description'][$class]=$this->fields;
	}
		else {
			$this->fields=$_SESSION['dims']['init_description'][$class];
		}
	}

	/*cyril - optimisation - fonction qui s'appuie sur la session avec $dims pour retrouver le descriptif de la table*/
	function getTableDescription(){
		$dims = dims::getInstance();
		return $dims->getTableDescription($this->tablename);
	}

	//-----------------------------------------------------------------------------------------------------------------------
	/*
	 * Cyril - 09/12/2011 - Obtenir juste la tables des fields dans l'ordre sans les valeurs
	 * Utile si on a pas encore fait d'open
	*/

	function describeFieldsName() {
		if(empty($this->my_description)) {
			$sql = "DESCRIBE $this->tablename";
			$result = $this->db->query($sql);
			while ($fields = $this->db->fetchrow($result)) {
				$this->my_description[] = $fields['Field'];
			}
		}
		return $this->my_description;
	}

	/*
	 * Cyril 09/12/2011 - réinitialisation de la table des lignes préparées pour une insertion massive
	*/
	function initBulkRows() {
		$this->prepared_bulk_rows = array();
	}

	/*
	 * Cyril 09/12/2011 - ajoute une ligne en attente d'insertion massive
	*/
	function addBulkRow($fields) {//le métier assume que le tableau de fields est dans l'ordre
		$this->prepared_bulk_rows[] = $fields;
	}
	/*
	 * Cyril 09/12/2011 - réalise la requête sql pour l'insertion massive
	*/
	function bulkInsert() {
		if(count($this->prepared_bulk_rows) > 0) {
			//construction de l'insert
			$insert = 'INSERT INTO '.$this->tablename.' (';

			$cols = '';
			$i = 0;
			$my_fields = $this->describeFieldsName();
			$nb_fields = count($my_fields);
			foreach($my_fields as $name) {
				$cols .= $name;
				if($i < $nb_fields -1) $cols .= ', ';
				$i++;
			}

			$insert .= $cols. ') VALUES ';
			$params = array();
			$o = 0;
			$nb_rows = count($this->prepared_bulk_rows);
			foreach($this->prepared_bulk_rows as $row) {
				$o++;
				$insert .= '('.$this->db->getParamsFromArray($row, 'valuesrow'.$o, $params).')';
				if($o < $nb_rows) $insert .= ', ';
			}
		}

		if(!empty($insert)) {
			$this->db->query($insert, $params);
			$this->initBulkRows();//on remet à 0 les lignes à insérer
		}
	}

	/*
	 * Cyril 09/12/2011 - lance une optimisation sur la table
	 */
	function optimize() {
		if(dims::getInstance()->tableexist($this->tablename)) {
			$this->db->query('OPTIMIZE TABLE '.$this->tablename);
		}
	}

	//-----------------------------------------------------------------------------------------------------------------------

	/**
	 * set user/group/module ids
	 *
	 * @return void
	 *
	 * @access public
	 */
	function setugm() {
		$this->fields['id_user'] = $_SESSION['dims']['userid'] ;
		$this->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
		$this->fields['id_module'] = $_SESSION['dims']['moduleid'];
	}

	/**
	 * set	date create/user create or date modify/user modify
	 *
	 * @return void
	 *
	 * @access public
	 */
	function setcm() {
		if($this->new) {
			$this->fields['date_create'] = date("YmdHis");
			$this->fields['user_create'] = $_SESSION['dims']['userid'];
		}
		else {
			$this->fields['date_modify'] = date("YmdHis");
			$this->fields['user_modify'] = $_SESSION['dims']['userid'];
		}
	}

	/**
	 * sql dump of object content
	 *
	 * @return string
	 *
	 * @access private
	 */

	function dump($with_database = true) {
		$listvalues='';
		$dbname = "`{$this->database}`.";
		if( ! $with_database ) $dbname = '';
		// build insert
		foreach ($this->fields as $key => $value) {
			if ($key != '' && !is_null($value)) {
				if ($listvalues != '') $listvalues .= ', ';
				//$listvalues .= "`{$this->tablename}`.`$key` = '".dims_sql_filter($value)."'";
				$listvalues .= "`{$this->tablename}`.`$key` = ".$this->db->getPdo()->quote($value);
			}
		}
		return ("INSERT INTO {$dbname}`{$this->tablename}` SET {$listvalues};");
	}


	/**
	 * template export of object content
	 *
	 * @return string
	 *
	 * @access private
	 * @deprecated
	 */

	function totemplate(&$tpl, $prefix) {
		$array_vars = array();
		foreach($this->fields as $key => $value) $array_vars[strtoupper("{$prefix}{$key}")] = $value;
		$tpl->assign_vars($array_vars);
	}

	/**
	 * get object sql structure
	 *
	 * @return string
	 *
	 * @access private
	 */

	function getsqlstructure() {
		$sql = "CREATE TABLE `{$this->tablename}` (";

		$fields = '';

		foreach ($this->fields as $key => $value) {
			if ($key != '') {
				if ($fields != '') $fields .= ",\n";
				$fields .= "`{$key}` varchar(255) NOT NULL default ''";
			}
		}

		$sql .= $fields.") TYPE=MyISAM;";

		return($sql);
	}

	public function recherche($objet_recherche, $limit=false) {
		return $this->search($objet_recherche, $limit);
	}

	public function search($objet_recherche, $limit=false) {
		if(dims::getInstance()->tableexist($this->tablename)) {
			if (!is_array($objet_recherche)) {
				//Gestion des erreurs
				throw new Error_class(array("message" => "objet_recherche is not array"));
			}

			$params = array();
			$strwhere = '';
			foreach ($objet_recherche as $key => $value) {
				if($this->db->fieldexist($this->tablename, $key)) {
					//Recherche la condition
					if ($value === "IS NOT NULL" or $value === "IS NULL") {
						$condition = $value;
					} else if (is_array($value)) {
						$condition = 'IN ('.$this->db->getParamsFromArray($value, $key, $params).')';
					} else {
						$condition = '= :'.$key;
						$params[':'.$key] = array(
							'type' => is_numeric($value) ? PDO::PARAM_INT : PDO::PARAM_STR,
							'value' => $value
						);
					}

					if (!empty($strwhere)) $strwhere .= ' AND ';
					$where_sql = $key.' '.$condition;
				}
			}

			//LIMIT SQL
			$strlimit = '';
			if (is_numeric($limit)) {
				$strlimit = ' LIMIT 0, '.$limit;
				$params[':limit'] = array('type' => PDO::PARAM_INT, 'value' => $limit);
			}

			$resu=$this->db->query("SELECT	".$this->idfields[0]."
						FROM	".$this->tablename."
						WHERE ".$where_sql
						.$strlimit,
						$params
			);

			return array('count' => $this->db->numrows($resu), 'result' => $this->db->getarray($resu));
		}
		return array('count' => 0, 'result' => array());
	}

	public function show_columns () {
		if(dims::getInstance()->tableexist($this->tablename)) {
			$query	= 'SHOW COLUMNS FROM '.$this->tablename;
			$rep	= $this->db->query($query);
			return $this->db->getarray($rep);
		}
		return array();
	}

	//Used by unserialize
	public function __wakeup() {
		$this->setdb(dims::getInstance()->getDb());
	}

	public function __sleep() {
		$this->setdb(null);
		return array();
	}

	public function getId() {
		if(count($this->idfields) > 1){
			$res = array();
			foreach($this->idfields as $id){
				$res[] = $this->fields[$id];
			}
			return implode('-',$res);
		}
		elseif(isset($this->fields[$this->idfields[0]])) {
			return $this->fields[$this->idfields[0]];
		}
		elseif(isset($this->fields['id'])) {
			return $this->fields['id'];
		}
		else {
			return 0;
		}
	}

	//---------------------------------------------------- SYNCHRO XMLs - Cyril - 09/05/2011 --------------------------

	/*
	 * Fonction surchargeable. Permet de construire la matrice des champs
	 * Par défaut : prend tous les champs de la table
	 *
	*/
	public function defineMatrice() {
		$classname=self::getClassObject();

		if (!isset($_SESSION['dims']['matrix_objects'][$classname])) {
		$this->matrice = array();
		$description = $this->getTableDescription();

		foreach ($description as $fields) {
			$name = $fields['Field'];
			//indique si le champ est un id de la table
			$is_id = false;
			if($fields['Key']=='PRI') $is_id = true;
			//détermine si le champ est requis (soit id, soit not null sans default_value)
			$requis = false;
			if($is_id  || (($fields['Null']=='NO') && (is_null($fields['Default'])))) $requis = true;

			//gestion des énumérations !important pour checker les données lors de l'import xml
			$enumeres = array();
			if(strpos($fields['Type'], 'enum(')===0) {
				$enumeres = $this->getEnumeres(substr($fields['Type'], 5, strlen($fields['Type'])-6));
			}

			$this->matrice[$name]['primary'] = $is_id;
			$this->matrice[$name]['required'] = $requis;
			$this->matrice[$name]['type'] = $fields['Type'];

			if($this->isActionsFollowed()) {
				$this->matrice[$name]['action_followed'] = true;
				$this->matrice[$name]['label'] = $name;
			}
			if($this->needControlChanges() || $this->isMatriceStandalone()){
				$this->matrice[$name]['change_detection'] = true;
			}
			if(count($enumeres)) $this->matrice[$name]['enumeres'] = $enumeres;
		}

		//suppression du champ timestp_modify sinon on va détecter des changements tout le temps
		if($this->isActionsFollowed() && isset($this->matrice['timestp_modify'])) $this->matrice['timestp_modify']['action_followed'] = false;
		if($this->needControlChanges() && isset($this->matrice['timestp_modify'])) $this->matrice['timestp_modify']['change_detection'] = false;

			// on affecte en session
			$_SESSION['dims']['matrix_objects'][$classname]=$this->matrice;
	}
		else {
			$this->matrice=$_SESSION['dims']['matrix_objects'][$classname];
		}
	}

	private function getEnumeres($enum)//implique qu'il ne peut pas y avoir d'apostrophe dans un enuméré
	{
		$res = array();
		$curWord = '';
		$mode = 'closed';
		for($i=0;$i < strlen($enum); $i++) {
			if($enum[$i]=="'") {
				if($mode=='opened') {
					$res[] = $curWord;
					$curWord = '';
					$mode = 'closed';
				}
				else {
					$mode='opened';
				}
			}
			else if($enum[$i]!="," || ($enum[$i]=="," && $mode=='opened') ) $curWord .= $enum[$i];
		}
		return $res;
	}

	//permet d'initialiser les valeurs de la matrice avec les données courantes de l'objet
	private function initMatriceValues() {
		foreach($this->matrice as $name => $fields) {
			if(array_key_exists($name, $this->fields)){
				$this->matrice[$name]['value'] = $this->fields[$name];
			}
		}
		if($this->isActionsFollowed()) $this->setAlreadyHistorised (false);//on réinitialise pour qu'au prochain save ça historise à nouveau
	}

	//force à une certaine valeur la synchronisation de l'objet
	public function setSynchronizable($value, $mode = self::DDO_SYNC_MODE_CLASSIC) {
		$this->synchronized = ($value && dims::getInstance()->isDimsSync());
		$this->mode_synchro = $mode;
	}

	public function setFromImport($value) {
		$this->from_import = $value;
	}

	//fonction permettant de personnaliser son préfixe
	public function setPrefixe($pref) {
		$this->prefixe = $pref;
	}


	public function isFromImport() {
		return $this->from_import;
	}

	public function setSyncExtended($value) {
		$this->extended = $value;
	}

	//indique si l'objet est synchronisé
	public function isSync() {
		return $this->synchronized;
	}

	//indique si la synchro est forcée en ajout
	public function isForcedAdding(){
		return $this->force_adding;
	}

	//permet de contraindre la synchro en mode ajout même si l'objet n'est pas nouveau
	public function setForcedAdding($val){
		$this->force_adding = $val;
	}

	/*
	 * Export XML de l'objet
	 * @param add : détermine si on est en mode création ou modification (par défaut : création)
	*/
	public function syncToXML($mode = 'add') {
		//détermine s'il y a eu une modification
		if($mode== "add") $add=true;
		else $add = false;
		$modif = false;
		foreach($this->matrice as $name => $attribut) {
			if($this->fields[$name] != $attribut['value']) {
				$modif=true;
				break;
			}
		}

		if($mode=='add' || ($mode=='update' && $modif)) {
			//création du chemin du fichier qui sera généré
			if (!defined('_DIMS_SYNC_EXPORT_PATH')) define('_DIMS_SYNC_EXPORT_PATH',"");
			if (!defined('_DIMS_SYNC_OUT_OK')) define('_DIMS_SYNC_OUT_OK',"1");

			$path = _DIMS_SYNC_EXPORT_PATH.$this->prefixe.date('YmdHis').'_'.$this->fields['id'].'.xml';
			//racine du document XML
			$dom = new DOMDocument();
			$racine = $dom->createElement('ddo');

			//gestion des attributs
			$racine->setAttribute('mode', ($add==true)?'add':'update');
			$racine->setAttribute('object',$this->tablename);


			$identity = $dom->createElement('identity');
			$fields = $dom->createElement('fields');

			foreach($this->matrice as $name => $tab) {

				if($tab['primary'] || (!$tab['primary'] && ($add || $tab['value'] != $this->fields[$name]) )) {
					$field = $dom->createElement(($tab['primary'])?'id':'field');
					$field->setAttribute('name', $name);
					$tnode = $dom->createTextNode($this->fields[$name]);
					$field->appendChild($tnode);
					$field->setAttribute('requis', ($tab['required'])?"1":"0");//toujours requis - c'est un id

					//--ajout au bloc identity
					if($tab['primary']) $identity->appendChild($field);
					else $fields->appendChild($field);
				}
			}

			$racine->appendChild($identity);
			$racine->appendChild($fields);
			$dom->appendChild($racine);
			$dom->save($path);
			return _DIMS_SYNC_OUT_OK;
		}
		else if($mode=='delete') {
			//création du chemin du fichier qui sera généré
			$path = _DIMS_SYNC_EXPORT_PATH.$this->prefixe.date('YmdHis').'_'.$this->fields['id'].'.xml';
			//racine du document XML
			$dom = new DOMDocument();
			$racine = $dom->createElement('ddo');

			//gestion des attributs
			$racine->setAttribute('mode', 'delete');
			$racine->setAttribute('object',$this->tablename);


			$identity = $dom->createElement('identity');
			foreach($this->matrice as $name => $tab) {
				if($tab['primary']) {
					$field = $dom->createElement('id');
					$field->setAttribute('name', $name);
					$tnode = $dom->createTextNode($this->fields[$name]);
					$field->appendChild($tnode);
					$field->setAttribute('requis', '1');
					$identity->appendChild($field);
				}
			}
			$racine->appendChild($identity);
			$dom->appendChild($racine);
			$dom->save($path);
			return _DIMS_SYNC_OUT_OK;
		}
		else return _DIMS_SYNC_OUT_NOTHING_TO_EXPORT;
	}

	/*
	 * Gestion XML de la synchro multi-dims / avec intégration des relations objets + autonomie des ids tant id que go_id
	 * Stockage DB
	*/
	public function syncToXML_AutoID($mode = 'add', $forced_dims_dest = null) {
		if( !empty ($this->fields['id_globalobject']) ){ //sans globalobject on a rien
			//détermine s'il y a eu une modification
			if($mode== "add") $add=true;
			else $add = false;
			$modif = false;

			if($mode == 'update' ){
				foreach($this->matrice as $name => $attribut) {
					if( isset($attribut['value']) && $this->fields[$name] != $attribut['value'] ) {
						$modif=true;
						break;
					}
				}
			}

			//on récupère notre global objet
			$dims = dims::getInstance();
			$go = new dims_globalobject();
			$go->open($this->fields['id_globalobject']);
			$obj_relations = $dims->getMBObjectRelationsOn(get_class($this));
			$known_relations_data = array(); //c'est pour ne pas avoir à refaire les requêtes pour chaque destinataire

			/*
				on récupère les destinataires selon le contexte
				Il faut distinguer quelques cas particuliers :
					* selon qu'on est le dims RACINE
					* selon que la nature de la relation permet de redistribuer l'info si le owner_id == dims_current
					* selon que l'objet courant appartient à un dims d'un tiers ou au dims racine
			*/

			$current_dims = new dims_sync();
			$id_cur_dims = $dims->getCurrentDimsID();

			$current_dims_fields = $dims->getSynchronizedDimsFields($id_cur_dims);
			$id_owner = $go->fields['id_dims_owner'];
			if( ! is_null($current_dims_fields) ){//si on connait même pas notre dims_courant ça sert à rien d'aller plus loin

				$lst_destinataires = array();//le tableau qui contiendra tous les id_dest_dims

				if( ! is_null($forced_dims_dest) ){//si on impose à l"objet un destinataire, pas besoin de tout calculer
					$lst_destinataires[$forced_dims_dest] = $forced_dims_dest;
				}
				else{
					$current_dims->openFromResultSet( $current_dims_fields );
					//il faut gérer la descente et la remontée de l'info

					//d'abord la redescente - modèle non distribué, y'a que la racine qui redesend de l'information
					if($id_cur_dims == $dims->getCurrentRootDimsID()){//si je suis la racine
						if($id_owner == $id_cur_dims){//si l'objet m'appartient je le redescends à tous mes fils
							$lst_destinataires = $dims->getDestsFor($id_cur_dims);//tient compte du statut
						}
						else{//sinon c'est qu'on a un destinataire ciblé
							$lst_destinataires[$id_owner] = $id_owner; //la cible est elle-même destinataire
							$lst_sharing = $dims->getDestsFor($id_owner);
							if( !is_null($lst_sharing) && !empty($lst_sharing)) $lst_destinataires = $lst_destinataires + $lst_sharing; // merge qui concerve les indexs
							if(isset($lst_destinataires[$id_cur_dims])) unset($lst_destinataires[$id_cur_dims]);
						}
					}
					else{ //là le dims courant est un dims SLAVE, l'info remonte avec un cas spécifique sur les infos locales
						if($id_owner != $id_cur_dims || $current_dims->isPersonalSharingAvailable()){
							$lst_destinataires = $dims->getDestsFor($id_cur_dims);
						}
					}

					//gestion du from import - Ici, si from_import est supérieur à 0 alors c'est que le save vient d'un import, et que l'émetteur ne doit pas recevoir ce qu'il vient d'envoyer
					if(!is_null($lst_destinataires) && $this->from_import){
						if( isset($lst_destinataires[$this->from_import]) ) unset($lst_destinataires[$this->from_import]);
					}
				}

				if( !is_null($lst_destinataires) && !empty($lst_destinataires)){
					//Cyril - 05/04/2013 - Alimentation d'un timestp dédié à la synchro sur le globalobject pour éviter les effets de bord au moment de l'import
					$local_timestp = date('YmdHis');
					foreach($lst_destinataires as $id_dest){
						if($mode == 'delete' || $mode == 'add' || ($mode=='update' && $modif)){ //on initialise le contexte parce que dans tous les aura au moins le bloc de contexte
							//création du chemin du fichier qui sera généré
							//racine du document XML
							$sync = new sync_data();

							$dom = new DOMDocument();
							$racine = $dom->createElement('ddo');
							$context = $dom->createElement('context');
							$identity = $dom->createElement('identity');

							$id_sync = $sync->create('', $go->getId(), get_class($this), $id_dest); //1er enregistrement pour obtenir l'id de sync qui est passé dans l'entête de l'xml

							$curr_dims = new dims_sync();
							$curr_dims->open($dims->getCurrentDimsID());

							$context->appendChild( $this->createXMLElem($dom, 'id_sync',			$id_sync) );
							$context->appendChild( $this->createXMLElem($dom, 'dims_key',			$curr_dims->getKey() ) );

							$context->appendChild( $this->createXMLElem($dom, 'id_dims_local',		$curr_dims->getId() ) );
							$context->appendChild( $this->createXMLElem($dom, 'local_goid',			$go->getId()));
							$context->appendChild( $this->createXMLElem($dom, 'local_record_id',	$go->fields['id_record']));
							$context->appendChild( $this->createXMLElem($dom, 'local_timestp',		$local_timestp ) );

							$context->appendChild( $this->createXMLElem($dom, 'id_dims_origin',		$go->fields['id_dims_origin']) );
							$context->appendChild( $this->createXMLElem($dom, 'id_object_origin',	$go->fields['id_object_origin']) );
							$context->appendChild( $this->createXMLElem($dom, 'id_dims_owner',		$go->fields['id_dims_owner']) );

							if($mode != 'add'){//pas de correspondance si on est en création, forcément le remote ne le connait pas, donc ça optimise un peu
								$corresp = sync_corresp::getCorrespFor($go->getId(), $id_dest);
								if( ! $corresp->isNew() ){
									$context->appendChild( $this->createXMLElem( $dom, 'corresp_id', $corresp->getRemoteObject() ) );
								}
							}

							$context->appendChild( $this->createXMLElem($dom, 'id_module_type',   $go->fields['id_module_type']) );
							$context->appendChild( $this->createXMLElem($dom, 'id_object',		$go->fields['id_object']) );
						}

						if($mode=='add' || ($mode=='update' && $modif)) {
							//gestion des attributs
							$racine->setAttribute('mode', ($add==true)?'add':'update');
							$racine->setAttribute('tablename',$this->tablename);
							$racine->setAttribute('classname',get_class($this));

							$fields_description = $dom->createElement('fields');
							$relations = $dom->createElement('relations');

							foreach($this->matrice as $name => $tab){
								if($tab['primary'] || (!$tab['primary'] && ($add || $tab['value'] != $this->fields[$name]) )) {
									$node_name = ($tab['primary'])?'id':'field';
									if($name == 'id_globalobject') $val = 0; //permet de donner la main au dims distant pour créer son propre globalobject
									else $val = $this->fields[$name];
									$field = $this->createXMLElem($dom, $node_name, $val, array('name' => $name , 'requis' => ($tab['required'])?"1":"0" ), $tab['type'] == 'blob' );

									if($tab['primary']) $identity->appendChild($field);
									else $fields_description->appendChild($field);
								}
							}
							//Traitement des relations
							if( empty($known_relations_data)){ //pour ne le faire qu'une seule fois même si  y'a 20 destinataires

								foreach($obj_relations as $dest => $columns){
									foreach($columns as $col_on => $to)
									{
										foreach($to as $col_to => $fields)
										{
											//Cyril - 25/03/2013 - intégration d'un contrôle sur le fait qu'on veut voir ou non la relation dans le fichier de synchro
											if( $fields['type'] == mb_object_relation::MB_RELATION_BELONGS_TO && array_key_exists($col_on, $this->fields) && $fields['sync_behavior'] == mb_object_relation::MB_RELATION_INTO_SYNC_DATA ){


												//on récupère les infos de la classe distante
												$foreign_class = $dims->getMBClassDataFromID($dest);
												//et on choppe le globalobject distant
												$go_relations = $this->getGOSFromRelation($foreign_class['classname'], $foreign_class['tablename'], $this->fields[$col_on], $col_to);

												foreach($go_relations as $go_relation){
							if( ! $go_relation->isNew() ){
								$known_relations_data[$fields['id']]['fields'] = $fields;
								$known_relations_data[$fields['id']]['id_object'] = $go_relation->fields['id'];
								$known_relations_data[$fields['id']]['id_record'] = $go_relation->fields['id_record'];
								$known_relations_data[$fields['id']]['id_dims_origin'] = $go_relation->fields['id_dims_origin'];
								$known_relations_data[$fields['id']]['id_object_origin'] = $go_relation->fields['id_object_origin'];
							}
							}
											}
										}
									}
								}
							}

							foreach($known_relations_data as $kn_relation){
								$fields =$kn_relation['fields'];
								$rel = $this->createXMLElem($dom, 'go_relation', null, array('type' => $fields['type'] ,
																							 'class_on' => $fields['id_class_on'] ,
																							 'on' => $fields['on'],
																							 'class_to' => $fields['id_class_to'],
																							 'to' => $fields['to'],
																							 'data_type' => $fields['data_type']
																							 ));

								$rel->appendChild($this->createXMLElem($dom, 'id_object', $kn_relation['id_object']));
								$rel->appendChild($this->createXMLElem($dom, 'id_record', $kn_relation['id_record']));
								$rel->appendChild($this->createXMLElem($dom, 'id_dims_origin', $kn_relation['id_dims_origin']));
								$rel->appendChild($this->createXMLElem($dom, 'id_object_origin', $kn_relation['id_object_origin']));

								//on traite l'éventuelle correspondance si on la connait
								$corresp = sync_corresp::getCorrespFor($kn_relation['id_object'], $id_dest);
								if( ! $corresp->isNew() ){
									if($fields['data_type'] == mb_object_relation::MB_RELATION_ID_TYPE) $value = $corresp->getRemoteRecord();
									else $value = $corresp->getRemoteObject();
									$rel->appendChild( $this->createXMLElem( $dom, 'corresp_id', $value ) );
								}
								$relations->appendChild($rel);
							}

							$racine->appendChild($context);
							$racine->appendChild($identity);
							$racine->appendChild($fields_description);
							$racine->appendChild($relations);
							$dom->appendChild($racine);
						}
						else if($mode=='delete') {

							//gestion des attributs
							$racine->setAttribute('mode', 'delete');
							$racine->setAttribute('tablename',$this->tablename);
							$racine->setAttribute('classname',get_class($this));

							$identity = $dom->createElement('identity');
							foreach($this->matrice as $name => $tab) {
								if($tab['primary']) {
									$field = $dom->createElement('id');
									$field->setAttribute('name', $name);
									$tnode = $dom->createTextNode($this->fields[$name]);
									$field->appendChild($tnode);
									$field->setAttribute('requis', '1');
									$identity->appendChild($field);
									//pas de break parce qu'il peut y avoir plusieurs colonnes en primary key
								}
							}
							$racine->appendChild($context);
							$racine->appendChild($identity);
							$dom->appendChild($racine);
						}
						if(isset($dom) && isset($sync)){
							$sync->setXMLData($dom->saveXML());
							$sync->save();
						}
					}
				}
			}
		}
	}

	private function createXMLElem($dom, $node_name, $value = null, $attributes = array(), $blob = false){
		$field = $dom->createElement($node_name);

		if( !is_null($value) ) {
			if( ! $blob ) $tnode = $dom->createTextNode($value);
			else{
				$tnode = $dom->createCDATASection(base64_encode($value));
			}
			$field->appendChild($tnode);
		}

		foreach($attributes as $nom => $val){
			$field->setAttribute($nom, $val);
		}
		return $field;
	}

	private function getGOSFromRelation($classname, $tablename, $on_value, $to_column){
		$obj = new $classname;
		$obj->init_description();
		$gos = array();
		if(dims::getInstance()->tableexist($this->tablename)) {
			if(array_key_exists('id_globalobject', $obj->fields) && array_key_exists($to_column, $obj->fields)){//Pour éviter un vieux FATAL ERROR au milieu de la synchro
				//Cyril - 11/03/2013 - Ajout couche de contrôle sur le classname --> fait que l'objet est bien celui prétendu être. utile pour le polymorphisme caché avec les globalobjects comme dans dims_action et la col globalobject_origin
				$sql = "SELECT go.*
					FROM ".$tablename." my_table
					INNER JOIN dims_globalobject go ON go.id = my_table.id_globalobject
					INNER JOIN dims_mb_object mbo ON go.id_module_type = mbo.id_module_type AND go.id_object = mbo.id
					INNER JOIN dims_mb_classes mbc ON mbo.id_class = mbc.id AND mbc.classname = :classname
					WHERE my_table.".$to_column." = :tocolumn";

				$res = $this->db->query($sql, array(':classname' => array('type' => PDO::PARAM_STR, 'value' => $classname), ':tocolumn' => array('type' => PDO::PARAM_INT, 'value' => $on_value)));
				if($this->db->numrows($res)){
					$go = new dims_globalobject();
					$fields = $this->db->fetchrow();
					$go->openFromResultSet($fields);
					$gos[] = $go;
				}
			}
		}
		return $gos;
	}


	/*
	 * Import XML de l'objet
	 * @param file : fournit le nom du fichier à importer
	 * @param extended : permet de retourner le tableau des champs xml plutôt qu'un tag OK en cas de surcharge
	*/
	public function syncFromXML($file, $extended =false) {
		$dom = new DOMDocument();
		$dom->load($file);
		if($dom->schemaValidate(_DIMS_SYNC_XSD_SCHEMA))//le fichier fourni est-il conforme à la définition DDO ?
		{
			$racine = $dom->documentElement;
			//récupération des arguments du fichier de synchro
			$mode = $racine->getAttribute('mode');
			$object = $racine->getAttribute('object');

			if(!empty($object) && $object==$this->tablename) {
				//gestion des identifiants
				$identity = $dom->getElementsByTagName("id");
				$tab_ids = array();
				$id_pos = 0;

				if($identity->length >= 1) {
					foreach($identity as $id) {//présuppose que les ids sont rangés dans l'ordre
						if(isset($this->matrice[$id->getAttribute('name')]) && $this->matrice[$id->getAttribute('name')]['primary']) {
							if($this->idfields[$id_pos] == $id->getAttribute('name')) {
								$tab_ids[] = $id->nodeValue;
								$id_pos++;
							}
							else return _DIMS_SYNC_WRONG_IDS_POSITION;
						}
						else return _DIMS_SYNC_UNKNOWN_PRIMARY_COLUMN;
					}

					eval("\$this->open(".implode(',',$tab_ids).");");

					//récupération de la structure des fields ----------------------------
					$myfields = $dom->getElementsByTagName("field");
					if($myfields->length >= 1 || $mode=='delete') {
						$xml_fields = array();
						foreach($myfields as $field) {
							$xml_fields[$field->getAttribute('name')] = $field->nodeValue;
						}
						switch($mode) {
							case 'add':
								if($this->new) {
									if($this->checkRequiredFields($xml_fields)) {
										foreach($xml_fields as $name=>$value) {
											if(isset($this->matrice[$name])) {
												//il faut vérifier que la valeur fournie par le xml est valide
												if(isset($this->matrice[$name]['enumeres']) && !$this->checkInEnumeres($value,$this->matrice[$name]['enumeres'])) {
													return _DIMS_SYNC_IN_UNKNOWN_ENUMERE;
												}
												$this->fields[$name] = $value;
											}//pas de else, les autres champs on les ignorera pour ne pas bloquer l'import
										}
										$this->setFromImport(true);
										$this->setSyncExtended($extended);
										$this->save();
										$this->setFromImport(false);
										$this->setSyncExtended(false);
										if(!$extended) {
											return _DIMS_SYNC_IN_OK;
										}
										else {
											$xml_fields['ddo_syncmode'] = 'add';
											return $xml_fields;
										}
									}
									else return _DIMS_SYNC_IN_REQUIRED_FIELD_MISSING;
								}
								else return _DIMS_SYNC_IN_ADD_OBJECT_ALREADY_EXISTING;
								break;
							case 'update':
								if(!$this->new) {
									//en update= pas besoin de checker les champs requis
									foreach($xml_fields as $name=>$value) {
										if(isset($this->matrice[$name])) {
											//il faut vérifier que la valeur fournie par le xml est valide
											if(isset($this->matrice[$name]['enumeres']) && !$this->checkInEnumeres($value,$this->matrice[$name]['enumeres'])) {
												return _DIMS_SYNC_IN_UNKNOWN_ENUMERE;
											}
											$this->fields[$name] = $value;
										}//pas de else, les autres champs on les ignorera pour ne pas bloquer l'import
									}
									$this->setFromImport(true);
									$this->setSyncExtended($extended);
									$this->save();
									$this->setFromImport(false);
									$this->setSyncExtended(false);
									if(!$extended) {
										return _DIMS_SYNC_IN_OK;
									}
									else {
										$xml_fields['ddo_syncmode'] = 'update';
										return $xml_fields;
									}
								}
								else return _DIMS_SYNC_IN_UNKNOWN_OBJECT;
								break;
							case 'delete':
								if(!$this->new)//sinon y'a rien à supprimer
								{
									$this->setFromImport(true);
									$this->delete();
									$this->setFromImport(false);
									if(!$extended) {
										return _DIMS_SYNC_IN_OK;
									}
									else {
										$xml_fields['ddo_syncmode'] = 'delete';
										return $xml_fields;
									}
								}
								else return _DIMS_SYNC_IN_UNKNOWN_OBJECT;
								break;
							default:
								return _DIMS_SYNC_IN_UNKNOWN_MODE;
								break;
						}
					}
					else return _DIMS_SYNC_NOTHING_TO_DO;

				}
				else return _DIMS_SYNC_IDS_MISSING;
			}
			else return _DIMS_SYNC_WRONG_DATA_OBJECT;
		}
		else return _DIMS_SYNC_IN_MALFORMED;
	}

	/* Fonction qui permet de récupérer la noeud  unique, parce que c'est un peu capilotracté ça évite de rédiger beaucoup de code source dans la fonction d'import*/
	private function getSimpleNode(DOMElement $node, $child_name){
		$lst = $node->getElementsByTagName($child_name);
		if($lst->length == 1){
			return $lst->item(0);
		}
		else return null;
	}

	/* Fonction qui permet de récupérer la valeur du noeud	unique, parce que c'est un peu capilotracté ça évite de rédiger beaucoup de code source dans la fonction d'import*/
	private function getSimpleNodeValue(DOMElement $node, $child_name){
		$child = $this->getSimpleNode($node, $child_name);
		if( !is_null($child) ){
			return $child->nodeValue;
		}
		else return null;
	}

	/*
	* Fonction d'import - qui ne conserve pas les id mais laisse la main au dims. Un calcul de correspondance est généré grâce aux globalobjects
	* On part du principe que $this possède déjà la bonne classe, c'est à dire que le script d'ouverture a été cherché la classe dans le code XML
	* Note : contrairement à syncFromXML(), on ne passe pas un fichier mais directement le code XML en chaîne de caractères
	*/

	public function syncFromXML_AutoID($xml, $extended =false) {
		$dom = new DOMDocument();
		$dom->loadXML($xml);
		$dims = dims::getInstance();
		if($dom->schemaValidate(_DIMS_SYNC_AUTO_ID_XSD_SCHEMA))//le fichier fourni est-il conforme à la définition DDO ?
		{
			$racine = $dom->documentElement;
			//récupération des arguments du fichier de synchro
			$mode = $racine->getAttribute('mode');
			$tablename = $racine->getAttribute('tablename');
			$classname = $racine->getAttribute('classname');

			$context = $this->getSimpleNode($racine, 'context');

			$id_sync			= $this->getSimpleNodeValue($context, 'id_sync');			  //id de synchro de l'émetteur
			$id_dims_from		= $this->getSimpleNodeValue($context, 'id_dims_local');		  //id du dims emmetteur
			$from_key			= $this->getSimpleNodeValue($context, 'dims_key');			  //key d'authentification de l'émetteur

			$id_go_from			= $this->getSimpleNodeValue($context, 'local_goid');		  //id globalobject de l'objet chez l'émetteur
			$id_record_from		= $this->getSimpleNodeValue($context, 'local_record_id');	  //id record chez l'émetteur

			$timestp_object		= $this->getSimpleNodeValue($context, 'local_timestp');		  //timestp de l'objet chez l'émetteur
			$id_dims_origin		= $this->getSimpleNodeValue($context, 'id_dims_origin');	  //id du dims à l'origine de la création de l'objet
			$id_object_origin	= $this->getSimpleNodeValue($context, 'id_object_origin');	  //id de l'objet dans le dims à l'origine de la création de l'objet
			$id_corresp			= $this->getSimpleNodeValue($context, 'corresp_id');		  //id local de correspondance de l'objet
			$id_dims_owner		= $this->getSimpleNodeValue($context, 'id_dims_owner');		  //id du dims auquel appartient l'objet
			$id_module_type		= $this->getSimpleNodeValue($context, 'id_module_type');	  //id module type du global object
			$id_object			= $this->getSimpleNodeValue($context, 'id_object');			  //id du mb object

			/*
			* GESTION DES CRITÈRES D'ACCEPTATION DU FICHIER :
			*	-> il faut que la relation FROM --> TO dims courrant existe et soit active pour ne pas recevoir que des informations de dims connus
			*	->il faut que la clef du dims FROM soit identique à celle que le dims LOCAL connaît
			*	-> il faut que le timestp de l'objet soit postérieur à celui que l'on possède déjà sur un objet existant (delete / update uniquement)
			*/

			if(!empty($tablename) && $tablename==$this->tablename && !empty($classname) && $classname == get_class($this)) {

				if( $dims->isSyncExists($id_dims_from, $dims->getCurrentDimsID()) ){

					//on teste la clef du dims FROM pour s'assurer que c'est bien la même que l'on possède en local
					$fields = $dims->getSynchronizedDimsFields($id_dims_from);
					if($fields['key'] == $from_key){

						//création / récupération du global object local

						$go = new dims_globalobject();
						if($mode != 'add'){ //si on est pas en ajout on est censé connaître l'objet
							if(!empty($id_corresp)){
								$go->open($id_corresp);
							}
							else{
								$go->openFromOrigin($id_dims_origin, $id_object_origin);
							}
							if( $go->isNew() ) return _DIMS_SYNC_UNKNOWN_GLOBALOBJECT;
							else $this->open($go->fields['id_record']);

							if( $this->isNew() ) return _DIMS_SYNC_IN_UNKNOWN_OBJECT;
						}

						//récupération de la structure des fields ----------------------------
						$myfields = $dom->getElementsByTagName("field");
						if($myfields->length >= 1 || $mode=='delete') {
							$xml_fields = array();
							foreach($myfields as $field) {
								$xml_fields[$field->getAttribute('name')] = $field->nodeValue;
							}
							switch($mode) {
								   case 'add':
									if( $this->isNew() ) {
										if( $this->checkRequiredFields($xml_fields) ) {
											foreach($xml_fields as $name=>$value) {
												if(isset($this->matrice[$name])) {
													//il faut vérifier que la valeur fournie par le xml est valide
													if(isset($this->matrice[$name]['enumeres']) && !$this->checkInEnumeres($value,$this->matrice[$name]['enumeres'])) {
														return _DIMS_SYNC_IN_UNKNOWN_ENUMERE;
													}
													 //si on a un champ id_globalobject, il faut que la valeur passe à 0, c'est une sécurité, même si l'export est censé écraser cette valeur
													if($name == 'id_globalobject' && $value>0)	$this->fields[$name] = 0;
													else{
														if($this->matrice[$name]['type'] != 'blob') $this->fields[$name] = $value;
														else{
															$this->fields[$name] = base64_decode($value);
														}
													}
												}//pas de else, les autres champs on les ignorera pour ne pas bloquer l'import
											}

											//on doit aller chercher les relations pour mettre à jours les ids locaux
											$relations = $dom->getElementsByTagName("go_relation");
											foreach($relations as $rel){
												$on = $rel->getAttribute('on'); //donne la colonne de this sur laquelle porte la relation
												$data_type = $rel->getAttribute('data_type'); //dit si c'est de nature classique (id) ou si ça porte sur un globalobject
												$rel_corresp_id = $this->getSimpleNodeValue($rel, 'corresp_id');
												if(!empty($rel_corresp_id)) $this->fields[$on] = $rel_corresp_id;
												else{
													//il faut trouver l'objet correspondant en local
													$rel_id_dims_origin = $this->getSimpleNodeValue($rel, 'id_dims_origin');
													$rel_id_object_origin = $this->getSimpleNodeValue($rel, 'id_object_origin');
													if( ! empty($rel_id_dims_origin) && ! empty($rel_id_object_origin) ){
														$rel_go = new dims_globalobject();
														$rel_go->openFromOrigin($rel_id_dims_origin, $rel_id_object_origin);
														if( ! $rel_go->isNew()){
															if($data_type == mb_object_relation::MB_RELATION_ID_TYPE){ //id classique
																$this->fields[$on] = $rel_go->fields['id_record'];
															}
															else $this->fields[$on] = $rel_go->getId();
														}
														else return _DIMS_SYNC_UNKNOWN_RELATED_GLOBALOBJECT;
													}
													else return _DIMS_SYNC_RELATED_OBJECT_ORIGIN_MISSING;
												}
											}

											$this->setFromImport($id_dims_from);
											$this->setSyncExtended($extended);
											$this->save();
											$go->open($this->fields['id_globalobject']);
											$go->fields['id_dims_origin'] = $id_dims_origin;
											$go->fields['id_object_origin'] = $id_object_origin;
											$go->fields['id_dims_owner'] = (is_null($id_dims_owner))?1:$id_dims_owner;
											$go->fields['last_import'] = $timestp_object; // très important de conserver le timestamp tel qu'il a été envoyé pour piloter les futurs synchro
											$go->save();
											//on doit stocker en local la correspondance pour gagner du temps pour la prochaine fois
											$corresp = new sync_corresp();
											$corresp->create($go->getID(), $id_dims_from, $id_go_from, $id_record_from);
											$this->setFromImport(false);
											$this->setSyncExtended(false);
											if(!$extended) {
												return $go->fields;//ça permet de retourner toutes les infos utiles pour créer chez l'émetteur la correspondance
											}
											else {
												$xml_fields['ddo_syncmode'] = 'add';
												return $xml_fields;
											}
										}
										else return _DIMS_SYNC_IN_REQUIRED_FIELD_MISSING;
									}
									else return _DIMS_SYNC_IN_ADD_OBJECT_ALREADY_EXISTING;
									break;

								case 'update':
									if( !$this->isNew() ) {
										if( $timestp_object >= $go->getLastImportTimestamp() ){//on ne fait la modif que si l'objet en xml est plus récent que celui qui est localement présent

											//en update= pas besoin de checker les champs requis
											$updated_columns = array();
											foreach($xml_fields as $name=>$value) {
												if(isset($this->matrice[$name])) {
													$updated_columns[$name] = $name;
													//il faut vérifier que la valeur fournie par le xml est valide
													if(isset($this->matrice[$name]['enumeres']) && !$this->checkInEnumeres($value, $this->matrice[$name]['enumeres'])) {
														return _DIMS_SYNC_IN_UNKNOWN_ENUMERE;
													}
													if($name != 'id_globalobject'){
														if($this->matrice[$name]['type'] != 'blob') $this->fields[$name] = $value;
														else{
															//die($value);
															$this->fields[$name] = base64_decode($value);
														}
													}

												}//pas de else, les autres champs on les ignorera pour ne pas bloquer l'import
											}

											//on doit aller chercher les relations pour mettre à jours les ids locaux
											$relations = $dom->getElementsByTagName("go_relation");
											foreach($relations as $rel){
												$on = $rel->getAttribute('on'); //donne la colonne de this sur laquelle porte la relation

												if(isset($updated_columns[$on])){ //on ne fait la recherche de l'objet joint que s'il a été modifié

													$data_type = $rel->getAttribute('data_type'); //dit si c'est de nature classique (id) ou si ça porte sur un globalobject
													$rel_corresp_id = $this->getSimpleNodeValue($rel, 'corresp_id');
													if(!empty($rel_corresp_id)) $this->fields[$on] = $rel_corresp_id;
													else{
														//il faut trouver l'objet correspondant en local
														$rel_id_dims_origin = $this->getSimpleNodeValue($rel, 'id_dims_origin');
														$rel_id_object_origin = $this->getSimpleNodeValue($rel, 'id_object_origin');
														if( ! empty($rel_id_dims_origin) && ! empty($rel_id_object_origin) ){
															$rel_go = new dims_globalobject();
															$rel_go->openFromOrigin($rel_id_dims_origin, $rel_id_object_origin);
															if( ! $rel_go->isNew()){
																if($data_type == mb_object_relation::MB_RELATION_ID_TYPE){ //id classique
																	$this->fields[$on] = $rel_go->fields['id_record'];
																}
																else $this->fields[$on] = $rel_go->getId();
															}
															else return _DIMS_SYNC_UNKNOWN_RELATED_GLOBALOBJECT;
														}
														else return _DIMS_SYNC_RELATED_OBJECT_ORIGIN_MISSING;
													}
												}
											}

											$this->setFromImport($id_dims_from);
											$this->setSyncExtended($extended);
											$this->save();
											$go->open($this->fields['id_globalobject']);//on réouvre le go parce que le title a peut-être été modifié en cours de route (ex : le title)
											$go->fields['last_import'] = $timestp_object; // très important de conserver le timestamp tel qu'il a été envoyé pour piloter les futurs synchro
											$go->save();
											$this->setFromImport(false);
											$this->setSyncExtended(false);
											if(!$extended) {
												return $go->fields;
											}
											else {
												$xml_fields['ddo_syncmode'] = 'update';
												return $xml_fields;
											}
										}
										else return _DIMS_SYNC_NOT_UPTODATE;
									}
									else return _DIMS_SYNC_IN_UNKNOWN_OBJECT;
									break;
								case 'delete':
									if(!$this->new)//sinon y'a rien à supprimer
									{
										$this->setFromImport($id_dims_from);
										$this->delete();
										$this->setFromImport(false);
										if(!$extended) {
											return _DIMS_SYNC_IN_OK;
										}
										else {
											$xml_fields['ddo_syncmode'] = 'delete';
											return $xml_fields;
										}
									}
									else return _DIMS_SYNC_IN_UNKNOWN_OBJECT;
									break;
								default:
									return _DIMS_SYNC_IN_UNKNOWN_MODE;
									break;
							}
						}
						else return _DIMS_SYNC_NOTHING_TO_DO;
					}
					else return _DIMS_SYNC_UNKNOWN_DIMS_KEY;
				}
				else return _DIMS_SYNC_UNAUTHORIZED;
			}
			else return _DIMS_SYNC_WRONG_DATA_OBJECT;
		}
		else return _DIMS_SYNC_IN_MALFORMED;
	}

	/*
	 * Fonction qui permet de checker dans les champs du fichiers XML si les champs requis sont présents
	*/
	private function checkRequiredFields($xml) {
		$erreur = false;
		foreach($this->matrice as $name => $tab) {
			if($tab['required'] && !isset($xml[$name])) {
				$erreur = true;
				break;
			}
		}
		return $erreur;
	}

	/*
	 * Fonction permettant de checker si la valeur fournie pour un champ xml fait partie de ses énumérés
	*/
	private function checkInEnumeres($value,$enum) {
		$trouve = false;
		foreach($enum as $val) {
			if($value==$val) {
				$trouve = true;
				break;
			}
		}
		return $trouve;
	}

	//-------------------------------- Fonctions pour le suivi des actions réalisées sur l'objet  --------------------------------

	public function isActionsFollowed() {
		return $this->isActionsFollowed;
	}

	public function setActionsFollowed($follow) {
		$this->isActionsFollowed = $follow;
	}

	public function skipCreationHistoric($bool){
		$this->skip_creation_historic = $bool;
	}

	public function skipDeletionHistoric($bool){
		$this->skip_deletion_historic = $bool;
	}

	//fonction qui retourne l'historique des actions réalisées sur cet objet
	public function getHistoricActions() {
		if( ! $this->isNew() && $this->isActionsFollowed() && array_key_exists('id_globalobject', $this->fields)) {
			//construction de la chaîne pour les ids de l'objet
			$inner_ids = "";
			$i = 0;
			$params = array();

			$sql = "SELECT a.id, u.lastname, u.firstname
				FROM dims_object_historic_action a
				LEFT JOIN dims_user u ON a.id_user = u.id
				WHERE a.ref_globalobject = :ref
				ORDER BY a.date DESC ";
			$params[':ref'] = array('type' => PDO::PARAM_INT, 'value' => $this->get('id_globalobject'));

			$res = $this->db->query($sql, $params);

			$historic = array();
			while($tab = $this->db->fetchrow($res)) {
				$action = new dims_object_historic();
				$action->open($tab['id']);
				//raccourci pour la mémoire et surtout pour pas avoir à faire un open sur un dims_user
				$action->setUserName($tab['lastname'], $tab['firstname']);
				$historic[] = $action;
			}
			return $historic;
		}
		else return array();//on est pas en mode suivi des actions donc pas la peine d'aller chercher des actions
	}

	public function historiseMatrice() {
		if($this->isActionsFollowed() && array_key_exists('id_globalobject', $this->fields)) {
			$action = new dims_object_historic();
			$need_historisation=true;

			foreach($this->matrice as $field => $tab) {
				if($tab['action_followed'] && ( ( isset($tab['value']) && $tab['value'] != $this->fields[$field] ) || ( !isset($tab['value']) && isset($this->fields[$field]) ) ) ) {//si on suit ce champ d'un point de vue hsitorique des actions

					if($need_historisation) {//pour la première création si y'a au moins un champ qui est différent
						$action->historiseAction($this->get('id_globalobject'), $this->action_comment, $this->action_type, $this->action_code, $this->action_go_reference);
						$this->setCurrentComment("");//on le réinitialise pour le prochain save
						$this->action_type = '';
						$this->action_code = '';
						$need_historisation = false;
					}
					$action->addChange($field, isset($tab['value']) ? $tab['value'] : null, $this->fields[$field]);
				}
			}//fin foreach sur la matrice
			if($need_historisation && ( !empty($this->action_comment) || in_array($this->action_type, array('C', 'D') ) ) )//c'est qu'on a détecté aucune modification
			{
				$action->historiseAction($this->get('id_globalobject'), $this->action_comment, $this->action_type, $this->action_code, $this->action_go_reference);
				$this->action_type = '';
				$this->action_code = '';
			}
			$this->setAlreadyHistorised(true);
			return $action;
		}
		else return false;
	}

	/*
	 * Permet de définir le prochain commentaire qui sera enregistré dans l'action, si un save se fait
	*/
	public function setCurrentComment($comment) {
		$this->action_comment = $comment;
	}

	public function setHistoCode($code) {
		$this->action_code = $code;
	}

	public function setHistoGoReference($ref){
		$this->action_go_reference = $ref;
	}
	private function setAlreadyHistorised($already) {
		$this->already_historised = $already;
	}

	private function isAlreadyHistorised() {
		return $this->already_historised;
	}

	public function setMatriceStandalone($m_standalone) {
		$this->isMatriceStandalone = $m_standalone;
	}

	public function isMatriceStandalone() {
		return $this->isMatriceStandalone;
	}

	//Cyril - 28/02/2013 - Fonction permettant de détecter s'il y a eu des changements sur l'objet
	public function changed(){
		$changed = false;
		foreach($this->fields as $field => $val){
			if((isset($this->matrice[$field]['change_detection']) && $this->matrice[$field]['change_detection']) && ( (isset($this->matrice[$field]['value'] ) && $this->matrice[$field]['value'] != $val) || (array_key_exists($field, $this->fields) && !isset( $this->matrice[$field]['value']) && isset($val))) ){//la seconde partie permet de gérer si la valeur était à null avant
				$changed = true;
			break;
			}
		}
		return $changed;
	}
	public function changes(){
		$changes = array();
		foreach($this->fields as $field => $val){
			if((isset($this->matrice[$field]['change_detection']) && $this->matrice[$field]['change_detection']) && ( (isset($this->matrice[$field]['value'] ) && $this->matrice[$field]['value'] != $val) || (array_key_exists($field, $this->fields) && !isset( $this->matrice[$field]['value']) && isset($val))) ){//la seconde partie permet de gérer si la valeur était à null avant
				$changes[$field]['before'] = isset($this->matrice[$field]['value']) ? $this->matrice[$field]['value'] : null;
				$changes[$field]['after'] = $val;
			}
		}
		return $changes;
	}
	public function controlChangesBeforeIndex($val){
	   $this->controlChangesBeforeIndex = $val;
	}

	public function needControlChanges(){
	   return $this->controlChangesBeforeIndex;
	}


	const TYPE_ATTRIBUT_NUMERIC = 0;
	const TYPE_ATTRIBUT_KEY = 1;
	const TYPE_ATTRIBUT_STRING = 2;
	const TYPE_ATTRIBUT_BOOLEAN_TINYINT = 3;

	public function getAttribut($name_attribut, $type_attribut) {
		if(isset($this->fields[$name_attribut])) {
			if($type_attribut == self::TYPE_ATTRIBUT_BOOLEAN_TINYINT) {
				if($this->fields[$name_attribut] == 0) {
					return false ;
				}
				else {
					return true ;
				}
			}
			else {
				return $this->fields[$name_attribut] ;
			}
		}
		else {
			switch($type_attribut) {
				case self::TYPE_ATTRIBUT_NUMERIC :
					return 0 ;
					break;
				case self::TYPE_ATTRIBUT_KEY :
					return 0;
					break;
				case self::TYPE_ATTRIBUT_STRING :
					return "";
					break;
				case self::TYPE_ATTRIBUT_BOOLEAN_TINYINT :
					return false;
					break;
			}
		}
	}

	public function setAttribut($name_attribut, $type_attribut, $value_attribut, $save = false) {
		$changed = false;
		switch($type_attribut) {
			case self::TYPE_ATTRIBUT_NUMERIC :
				if($value_attribut !== null && is_numeric($value_attribut)) {
					$this->fields[$name_attribut] = $value_attribut ;
					$changed = true;
				}
				break;
			case self::TYPE_ATTRIBUT_KEY :
				if($value_attribut !== null && is_numeric($value_attribut) && $value_attribut >= 0) {
					$this->fields[$name_attribut] = $value_attribut ;
					$changed = true;
				}
				break;
			case self::TYPE_ATTRIBUT_STRING :
				if($value_attribut !== null) {
					$this->fields[$name_attribut] = $value_attribut ;
					$changed = true;
				}
				break;
			case self::TYPE_ATTRIBUT_BOOLEAN_TINYINT :
				if($value_attribut !== null) {
					if(is_numeric($value_attribut) && ($value_attribut == 0 || $value_attribut == 1)) {
						$this->fields[$name_attribut] = $value_attribut ;
						$changed = true;
					}
					elseif(is_bool($value_attribut)) {
						if($value_attribut) {
							$this->fields[$name_attribut] = 1 ;
						}
						else {
							$this->fields[$name_attribut] = 0 ;
						}
						$changed = true;
					}
					elseif(is_string($value_attribut)) {
						if($value_attribut == "true") {
							$this->fields[$name_attribut] = 1 ;
							$changed = true;
						}
						elseif($value_attribut == "false") {
							$this->fields[$name_attribut] = 0 ;
							$changed = true;
						}
					}
				}
				break;
		}
		if($changed) {
			if($save) {
				$this->save();
			}
		}
	}

	/*
	 * fonction laxiste permettant d'ajouter autant d'attribut que recquiert le module métier qui utilise la classe todo
	 */
	public function setLightAttribute($key, $value) {
		$this->dev_attributes[$key] = $value;
	}

	/* permet de supprimer un light attribute */
	public function deleteLightAttribute($key){
		if(isset($this->dev_attributes[$key])) {
			unset($this->dev_attributes[$key]);
		}
	}

	/*
	 * fonction qui retourne la valeur associé à la clef métier envoyée
	 */
	public function getLightAttribute($key) {
		return (isset($this->dev_attributes[$key])) ? $this->dev_attributes[$key] : null;
	}

	/* Fonction qui retourne tous les attributs lights */
	public function getAllLightAttributes(){
		return $this->dev_attributes;
	}

	public function initMyLightAttributes(){
		if(isset($this->dev_attributes)) unset($this->dev_attributes);
	}

	public function openWithFields($fields, $unset_db = false) {
		$this->fields = $fields ;
		$this->setNew(false);
		if($unset_db) {
			unset($this->db);
		}
	}

	// idem à openWithFields
	public function openFromResultSet($fields, $unset_db = false, $go_object_value = null) {
		if(isset($fields) && !empty($fields)) {
			$this->fields = $fields ;
			$this->setNew(false);
			if($unset_db) {
				unset($this->db);
			}

			if($this->isSync() || $this->isActionsFollowed() || $this->isMatriceStandalone() || $this->needControlChanges()) {
				$this->initMatriceValues();//initialise les valeurs de la matrice de synchronisation
			}
			$this->updateGOOnOpenedRow($go_object_value);
		}
	}


	public function setNew($isNew) {
		$this->new = $isNew;
	}

	public function isNew() {
		return $this->new;
	}

	public function getMyGlobalObject() {
		if(is_null($this->myGlobalObject) && !empty($this->fields['id_globalobject'])) {
			$this->myGlobalObject = new dims_globalobject();
			$this->myGlobalObject->open($this->fields['id_globalobject']);
		} else if(empty($this->fields['id_globalobject'])) {
			$this->myGlobalObject = null;
		}
		return $this->myGlobalObject;
	}

	// recherche tous les liens avec cet objet : retourne une liste => id_object => liste id_globalobject
	public function searchGbLink($pref = 0) {
		if (isset($this->id_globalobject) && isset($this->fields['id_module'])) {
			$gb = $this->getMyGlobalObject();
			if (!is_null($gb) && $gb->fields['id'] > 0) {
				return $gb->searchLink($pref);
			}
		}
		return array() ;
	}

	// ouvre l'objet avec l'id_globalobject
	public function openWithGB($id) {
		$this->init_description();
		if (isset($this->id_globalobject) && isset($this->fields['id_module'])) {
			$sql = "SELECT * FROM `{$this->tablename}` WHERE `id_globalobject` = :idglobalobject";

			$this->resultid = $this->db->query($sql, array(
				':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $id),
			));
			$this->numrows = $this->db->numrows($this->resultid);
			$this->fields = $this->db->fetchrow($this->resultid);
			$this->setNew(!(bool)$this->numrows);

			if($this->isSync() || $this->isActionsFollowed() || $this->isMatriceStandalone() || $this->needControlChanges() ) {
				$this->initMatriceValues();//initialise les valeurs de la matrice de synchronisation
			}
				return $this;
		} else {
			return false;
		}
	}

	public function getGlobalobjectId() {
		return (!empty($this->fields['id_globalobject'])) ? $this->fields['id_globalobject'] : -1;
	}


	public function display($tpl_path) {
		global $_DIMS;

		if (file_exists($tpl_path)) include ($tpl_path);
		else {
			$path=realpath('.');
			if (file_exists($path.$tpl_path)) {
				include $path.$tpl_path;
			}
		}
	}

	public function getMyTags($type = null) {
		$tags=array();
		if(!empty($this->fields['id_globalobject'])) {
			$params = array(
				':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_globalobject']),
				':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
			);
			if(!is_null($type)){
				$params[':type'] = array('type' => PDO::PARAM_INT, 'value' => $type);
			}
			$res = $this->db->query("
				SELECT t.*
				FROM dims_tag_globalobject tg
				INNER JOIN dims_tag t ON t.id = tg.id_tag
				WHERE tg.id_globalobject=:idglobalobject
				AND (t.id_workspace=:idworkspace or t.shared=1)
				".((!is_null($type))?"AND type = :type":"")."
				GROUP BY t.id
				ORDER BY t.tag ASC",$params);
			require_once DIMS_APP_PATH."modules/system/class_tag.php";
			while($table = $this->db->fetchrow($res)) {
				$t = new tag();
				$t->openFromResultSet($table);
				$tags[] = $t;
			}
		}
		return $tags;
	}

	public function getMyNewsletters() {
		require_once(DIMS_APP_PATH . '/modules/system/class_newsletter.php');
		$newsletters=array();
		$newslid=array();

		if(isset($this->fields['id_globalobject'])) {
			$sql = "SELECT n.*
				FROM dims_mod_newsletter as n
				INNER JOIN dims_mod_newsletter_subscribed as ns ON n.id = ns.id_newsletter
				INNER JOIN	dims_mod_business_contact as c on c.id=ns.id_contact
				AND c.id_globalobject=:idglobalobject
				AND n.id_workspace=:idworkspace
				ORDER BY n.label ASC";
			$res = $this->db->query($sql, array(
				':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_globalobject']),
				':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
			));

			while($table = $this->db->fetchrow($res)) {
				$t = new newsletter();
				$t->openFromResultSet($table);
				$newsletters[] = $t;
				$newslid[]=$t->fields['id'];
			}
		}
		$_SESSION['dims']['mynewsletters']=$newslid;
		return $newsletters;
	}

	public function isFavorite($user = 0) {
		if ($user == 0) $user = $_SESSION['dims']['userid'];
		$isFav = false;
		if ($this->id_globalobject > 0) {
			require_once DIMS_APP_PATH.'modules/system/class_favorite.php';
			$fav = new favorite();
			if ($fav->open($user,$this->fields['id_globalobject']))
				$isFav = ($fav->fields['status'] == favorite::Favorite);
		}
		return $isFav;
	}

	public function getAnnotations() {
		$lstAnno = array();
		$params = array();
		global $dims;
		if (!isset($this->id_globalobject)) $this->setid_object();
		$sql = "SELECT		a.*, u.*, ct.*
			FROM		dims_annotation a
			INNER JOIN	dims_user u
			ON			u.id = a.id_user
			INNER JOIN	dims_mod_business_contact ct
			ON			u.id_contact = ct.id
			WHERE		a.id_object = :idobject
			AND			a.id_parent = 0
			AND			a.id_record = :idrecord
			AND			a.id_workspace IN (".$this->db->getParamsFromArray(explode(',', $dims->getListWorkspaces()), 'idworkspace', $params).")
			ORDER BY	a.date_annotation DESC";

		$params[':idobject'] = array('type' => PDO::PARAM_INT, 'value' => $this->id_globalobject);
		$params[':idrecord'] = array('type' => PDO::PARAM_INT, 'value' => $this->fields[$this->idfields[0]]);

		$res = $this->db->query($sql, $params);

		require_once DIMS_APP_PATH.'modules/system/class_annotation.php';
		require_once DIMS_APP_PATH.'modules/system/class_user.php';
		foreach($this->db->split_resultset($res) as $r) {
			$anno = new annotation();
			$anno->openWithFields($r['a']);
			$us = new user();
			$us->openWithFields($r['u']);
			$anno->setLightAttribute('user', $us);
			$ct = new contact();
			$ct->openWithFields($r['ct']);
			$anno->setLightAttribute('contact', $ct);
			$lstAnno[] = $anno;
		}
		return $lstAnno;
	}

	/**
	* Utile pour la version jquery mobile
	* @param type $id
	* @param type $order
	* @param type $fav
	* @return type
	*/
	function openIn($id, $order, $fav=false) {
		if(dims::getInstance()->tableexist($this->tablename)) {
			$id_user = $_SESSION['dims']['userid'];
			$joint = "";
			$params = array();
			if ($fav) {
				$joint = "LEFT JOIN (
					SELECT id_contact2,id_contact1,link_level,type_link
					FROM `dims_mod_business_ct_link` l INNER JOIN `dims_user` u on l.id_contact1=u.id_contact
					WHERE u.id=:iduser
					) as l2 ON c.id=id_contact2";
				$params[':iduser'] = array('type' => PDO::PARAM_INT, 'value' => $id_user);
			}
			if (isset($id)) {
				$sql = "SELECT * FROM `{$this->tablename}` c $joint WHERE `{$this->idfields[0]}` IN (" . $this->db->getParamsFromArray($id, 'id', $params) . ") ORDER BY :orderby" . $order;
				$params[':orderby'] = array('type' => PDO::PARAM_STR, 'value' => $order);
				$this->resultid = $this->db->query($sql, $params);
				$this->numrows = $this->db->numrows($this->resultid);
				$this->fields = $this->db->getarray($this->resultid);
				for ($i = 0; $i < $numargs; $i++)
					$this->fields[$this->idfields[$i]] = $id[$i];

				if ($this->numrows > 0)
					$this->new = FALSE;

				if (isset($this->id_globalobject) && isset($this->fields['id_module'])) {
					if ($this->id_globalobject > 0 && $this->fields['id_module'] > 0) {
						$this->settitle();
						if (isset($this->title)) {
							require_once(DIMS_APP_PATH . "/include/class_dims_globalobject.php");
							$gobject = new dims_globalobject(/* $this->db */);
							$id_gobject =
							$gobject->getObject($this->fields['id_module'],
							$this->id_globalobject, $this->fields[$this->idfields[0]],
							$this->title);

							if (!($id_gobject > 0)) {
								$mod = $dims->getModule($this->fields['id_module']);
								$gobject = new dims_globalobject(/* $this->db */);
								$gobject->fields['id_module_type'] = $mod['id_module_type'];
								$gobject->fields['id_module'] = $this->fields['id_module'];
								$gobject->fields['id_object'] = $this->id_globalobject;
								$gobject->fields['id_record'] = $this->fields[$this->idfields[0]];
								$gobject->fields['title'] = $this->title;
								$id_gobject = $gobject->save();
							}

							$sql = "UPDATE `$this->tablename` SET
								`$this->tablename`.`id_globalobject` = :idglobalobject WHERE
								`$this->tablename`.`{$this->idfields[0]}` =
								:id";

							$this->db->query($sql, array(
								':idglobalobject' =>  array('type' => PDO::PARAM_INT, 'value' => $id_globalobject),
								':id' =>  array('type' => PDO::PARAM_INT, 'value' => $this->fields[$this->idfields[0]]),
							));
						}
					}
				}
			} else {
				$sql = "SELECT * FROM `{$this->tablename}` c $joint ORDER BY :orderby";
				$params[':orderby'] = array('type' => PDO::PARAM_STR, 'value' => $order);
				$this->resultid = $this->db->query($sql, $params);
				$this->numrows = $this->db->numrows($this->resultid);
				$count = 0;
				while ($row = $this->db->fetchrow($this->resultid)) {
					$this->fields[$count++] = $row;
				}
			}
		}

		return $this->numrows;
	}

	/*
	 *
	 * name: all
	 * @static
	 * @return array objects list (using late static binding)
	 */
	public static function all($conditions = '', $params = array()) {
		$objectList = array();

		$db = dims::getInstance()->getDb();

		$sql = 'SELECT * FROM '.static::TABLE_NAME.' '.$conditions;

		$res = $db->query($sql, $params);

		while($data = $db->fetchrow($res)) {
			$obj = new static();
			$obj->openFromResultSet($data);

			$objectList[$obj->get('id')] = $obj;
		}
		return $objectList;
	}

	/*
	 *
	 * name: openlist
	 * @static
	 * @return array objects list (using late static binding)
	 */
	public static function openlist($idsobject) {
		$objectlist = array();
		if(!empty($idsobject) && is_array($idsobject)) {
			$db = dims::getInstance()->getDb();

			$params = array();
			// FIXME : 'id' field SHOULD NOT be hardcoded
			$sql = 'SELECT * FROM '.static::TABLE_NAME.' WHERE id IN ('.dims_db::getParamsFromArray($idsobject, 'idsobject', $params).')';

			$res = $db->query($sql, $params);

			while($data = $db->fetchrow($res)) {
				$object = new static();
				$object->openFromResultSet($data);

				$objectlist[$object->getId()] = $object;
			}
		}
		return $objectlist;
	}

	public static function getNewObject(){
		$class = get_called_class();
		return new $class;
	}

	public static function getClassObject() {
		return get_called_class();
	}

	/*
	* Cyril 16/04/2012 - fonctions de metabase pour les clefs étrangères entre objets ----------------------
	*/
	public function getMyModuleTypeID(){
		if(!empty($this->fields['id_module'])){
			$dims = dims::getInstance();
			$mod = $dims->getModule($this->fields['id_module']);
			if(isset($mod) && !empty($mod)){
				$id_module_type = $mod['id_module_type'];
			}
			else{
				require_once(DIMS_APP_PATH . "/modules/system/class_module.php");
				$mod = new module();
				$mod->open($this->fields['id_module']);
				if(!empty($mod->fields['id_module_type']))	$id_module_type = $mod->fields['id_module_type'];
				else $id_module_type = -1;
			}
			return $id_module_type;
		}
		else{
			$current_mti = dims::getInstance()->getCurrentModuleTypeID();
			return (!empty($current_mti)) ? $current_mti : 0;
		}
	}

	//enregistre une relation de nature 'has_one' entre les objets de type this  et ceux donné par la class_to.
	public function has_one($class_to, $tablename, $on, $to, $params = array()){
		$default_params = array();
		$default_params['mode'] = mb_object_relation::MB_RELATION_ID_TYPE;
		$default_params['ext_index'] = mb_object_relation::MB_RELATION_NO_INDEX;
		$default_params['alias'] = $class_to;

		$p = array_merge($default_params, $params);//Merge des paramètres
		$this->store_relation(mb_object_relation::MB_RELATION_HAS_ONE, $class_to, $tablename, $on, $to, $p['mode'], $p['ext_index'], mb_object_relation::MB_RELATION_NOT_IN_SYNC_DATA, $p['alias']);
	}

	//enregistre une relation de nature 'has_many' entre les objets de type this  et ceux donné par la class_to.
	public function has_many($class_to, $tablename, $on, $to, $params = array()){
		$default_params = array();
		$default_params['mode'] = mb_object_relation::MB_RELATION_ID_TYPE;
		$default_params['ext_index'] = mb_object_relation::MB_RELATION_NO_INDEX;
		$default_params['alias'] = $class_to;

		$p = array_merge($default_params, $params);//Merge des paramètres
		$this->store_relation(mb_object_relation::MB_RELATION_HAS_MANY, $class_to, $tablename, $on, $to, $p['mode'], $p['ext_index'], mb_object_relation::MB_RELATION_NOT_IN_SYNC_DATA, $p['alias']);
	}

	//enregistre une relation de nature 'has_many_through'. Ici le on et $to ont peut d'importance. Le param through est requis
	public function has_many_through($class_to, $tablename, $on, $to, $params = array()){
		if(!empty($params['through'])){
			$default_params = array();
			$default_params['mode'] = mb_object_relation::MB_RELATION_ID_TYPE;
			$default_params['ext_index'] = mb_object_relation::MB_RELATION_NO_INDEX;
			$default_params['alias'] = $class_to;

			$p = array_merge($default_params, $params);//Merge des paramètres
			$this->store_relation(mb_object_relation::MB_RELATION_HAS_MANY_THROUGH, $class_to, $tablename, $on, $to, $p['mode'], $p['ext_index'], mb_object_relation::MB_RELATION_NOT_IN_SYNC_DATA, $p['alias'], $p['through']);
		}
	}

	//enregistre une relation de nature 'belongs_to' entre les objets de type this	et ceux donné par la class_to.
	public function belongs_to($class_to, $tablename, $on, $to, $params = array()){
		$default_params = array();
		$default_params['mode'] = mb_object_relation::MB_RELATION_ID_TYPE;
		$default_params['ext_index'] = mb_object_relation::MB_RELATION_NO_INDEX;
		$default_params['alias'] = $class_to;
		$default_params['sync_behavior'] = mb_object_relation::MB_RELATION_INTO_SYNC_DATA;

		$p = array_merge($default_params, $params);//Merge des paramètres
		$this->store_relation(mb_object_relation::MB_RELATION_BELONGS_TO, $class_to, $tablename, $on, $to, $p['mode'], $p['ext_index'], $p['sync_behavior'], $p['alias']);
	}

	//méthode qui permet d'enregistrer une relation - le tablename permet d'enregistrer la classe et/ou la table si elle(s) n'existe(nt) pas déjà dans la meta_base
	private function store_relation($type, $class_to, $tablename, $on, $to, $mode, $ext_index, $sync_behavior, $alias, $through = null){

		$dims = dims::getInstance();
		//isRelationExisting($id_class_on, $id_class_to, $on, $to){
		$my_class_fields = $dims->getMBClasse(get_class($this));
		$to_class_fields= $dims->getMBClasse($class_to);
		if( empty($my_class_fields)  ||  empty($to_class_fields) || ! $dims->isRelationExisting($my_class_fields['id'], $to_class_fields['id'], $on, $to) ){
			//on stocke la relation
			$join = new mb_object_relation();
			if($join->create(get_class($this), $this->tablename, $on, $class_to, $tablename, $to, $type, $mode, $ext_index, $sync_behavior, $alias, $through)){
				$dims->addNewMBObjectRelation($join->fields['id_class_on'], $join->fields['id_class_to'], $on, $to, $join->fields);
			}
		}
	}

	//permet éventuellement de surcharger id_dimsowner (ce sera utile pour la synchro quand on avalera les données)
	public function set_dimsowner($owner_id){
		$this->id_dimsowner = $owner_id;
	}

	//fonction qui retourne toutes les relations de l'objet sur la table fournie en paramètre
	//Les relations peuvent être retournées selon le type donné en paramètre.
	public function getRelationsWith($tablename, $type=null){
		return mb_object_relation::getTableRelations(get_class($this), $tablename, $type);
	}

	//permet de déterminer à quel dims appartient l'objet - FORTEMENT CORRELÉ AUX TIERS
	public function get_dimsowner(){
		if(empty($this->id_dimsowner)){
			//on regarde si éventuellement c'est pas déjà la class tiers en live
			require_once DIMS_APP_PATH.'modules/system/class_tiers.php';
			if($this->tablename == tiers::TABLE_NAME){
				$this->id_dimsowner = dims_sync::getIDForTiers($this->getId());
			}
			else{//on teste si j'ai une relation sur business_tiers de type "belongs_to"
				$relations = $this->getRelationsWith(tiers::TABLE_NAME, mb_object_relation::MB_RELATION_BELONGS_TO);
				foreach($relations as $rel){
					$on = $rel->getColumnOn();
					$to = $rel->getColumnTo();
					if(!empty($on) && !empty($to) && $to == 'id' && array_key_exists($on, $this->fields) && !empty($this->fields[$on]) ){
						$this->id_dimsowner = dims_sync::getIDForTiers($this->fields[$on]);
						break;
					}
				}
			}

			if($this->id_dimsowner == 0){//si c'est encore 0, on file le dims courrant
			   $this->id_dimsowner = dims::getInstance()->getCurrentDimsID();
			}
		}
		return $this->id_dimsowner;
	}

	/***** Fonctions d'historisation *****/
	function addHistory($actiontype, $tab_globalid = array(), $commentaire = null, $classname = 'dims_action'){
		if(count($this->actionType) > 0 && isset($this->id_globalobject) && isset($this->actionType[$actiontype])){
			require_once DIMS_APP_PATH."include/class_dims_action.php";
			require_once DIMS_APP_PATH."include/class_dims_action_matrix.php";
			$nbcomment = (empty($commentaire)) ? 0 : 1;
			$action = new $classname;
			if($this->isFromImport()) $action->setSynchronizable(false);//Si on vient de la synchro, inutile de resynchroniser l'action elle-même
			$action->init_description();
			$action->fields['id_parent'] = 0 ;
			$action->fields['type'] = $actiontype ;
			$action->fields['comment'] = $this->actionType[$actiontype];
			$action->fields['nbcomment'] = $nbcomment;
			$action->setugm();
			$action->fields['globalobject_origin'] = $this->fields['id_globalobject'];
			$action->save();

			if($nbcomment != 0) {
				$action_comment = new $classname ;
				if($this->isFromImport()) $action_comment->setSynchronizable(false);//Si on vient de la synchro, inutile de resynchroniser l'action elle-même
				$action_comment->init_description();
				$action_comment->fields['id_parent'] = $action->getID();
				$action_comment->fields['type'] = dims_const::_ACTION_COMMENT ;
				$action_comment->fields['comment'] = $commentaire ;
				$action_comment->fields['nbcomment'] = 0 ;
				$action_comment->setugm();
				$action_comment->save() ;
			}

			foreach ($tab_globalid as $global_id) {
				$action_matrix = new dims_action_matrix($this->db);
				$action_matrix->fields['id_globalobject'] = $global_id ;
				$action_matrix->setugm();
				if(isset($action->fields['timestp_modify'])){
					$action_matrix->set_iddate_from_timestp($action->fields['timestp_modify']);
				}else{
					$action_matrix->set_iddate_from_timestp(null);
				}
				$action_matrix->fields['id_tag'] = 0;

				$action_matrix->save();
			}

			return $action->getID();
		}else
			return false;
	}

	function getHistory(){
		 $tab_action = array() ;

		if(isset($this->fields['id_globalobject'])) {
			if($this->fields['id_globalobject'] != 0) {
				require_once DIMS_APP_PATH."include/dims_controller_historique.php";
				$tab_action = dims_controller_historique::build_historique($this->fields['id_globalobject'], $this->db);
			}
			else {
				throw new Exception('ERROR : id_globalobject = 0');
				//TODO ERROR ID_globalobject = 0
			}
		}
		else {
			throw new Exception('ERROR : id_globalobject is unset.');
			//TODO ERROR ID_globalobject is unset
		}

		return $tab_action ;
	}

	public function getLink(){
		return "javascript:void(0);";
	}

	public function getLabel(){
		return "";
	}

	public function getPhotoWebPathOrDefault($size = 60){
		return "./common/img/blank.gif";
	}

	public function getTypeObject(){
		return get_class($this);
	}

	public function getDatabase(){
		return $this->database;
	}

	public function setDatabase($dataBase){
		$this->database = $dataBase;
	}

	/*
	Cyril - 15/04/2013 - Helper de sélection d'objets dans la base de données
	$searh est un tableau clef valeur. Si la valeur est un tableau, alors DDO appliquera une clause IN (..., ..., ...)

	Important : si il n'y a qu'un seul résultat et qu'on a mis une limite à 1, la méthode retourne l'objet et pas une liste de 1 élément
	*/
	public static function find_by($search, $order_by = null, $limit_start = null, $limit_qte = null) {
		if( !empty($search) ){
			$p = array();
			$where = ' WHERE 1=1 ';
			$columns = array();
			foreach($search as $key => $value){
				//génération du nom de la variable pdo
				if( ! isset($columns[$key])){
					$columns[$key] = 0;
				}
				else $columns[$key] ++;

				if( ! is_array($value) ){
					$where .= ' AND `'.$key.'` = :'.$key.$columns[$key].' ';
					$p[':'.$key.$columns[$key]] = $value;
				}
				else{
					if(!empty($value)){
						$impl = dims_db::getParamsFromArray($value, $key.$columns[$key], $p);
						$where .= ' AND `'.$key.'` IN ('.$impl.') ';
					}
					else{
						$where .= ' AND `'.$key.'` IN (0) ';
					}
				}
			}

			$order = '';
			if( ! is_null($order_by) && !empty($order_by)){
				$order = ' '.$order_by.' ';
			}

			$limit = '';
			$single = false;
			if( ! (empty($limit_start) && empty($limit_qte) ) ){
				if( ! empty($limit_start) && ! empty($limit_qte)){
					$limit = ' LIMIT :l_start, :l_qte ';
					$p[':l_start'] = array( 'value' => $limit_start, 'type' => PDO::PARAM_INT );
					$p[':l_qte'] = array( 'value' => $limit_qte, 'type' => PDO::PARAM_INT );
					if($limit_qte == 1) $single = true;
				}
				else if( ! empty($limit_start) && empty($limit_qte)){#Petit hack, on utilise la donnée comme si c'était la quantité
					$limit = ' LIMIT :l_qte ';
					$p[':l_qte'] = array( 'value' => $limit_start, 'type' => PDO::PARAM_INT );
					if($limit_start == 1) $single = true;
				}
				else if( empty($limit_start) && ! empty($limit_qte)){
					$limit = ' LIMIT :l_qte ';
					$p[':l_qte'] = array( 'value' => $limit_qte, 'type' => PDO::PARAM_INT );
					if($limit_qte == 1) $single = true;
				}
			}
			$db = dims::getInstance()->getDb();
			$obj = self::getNewObject();
			$res = $db->query("SELECT * FROM ".$obj->getDatabase().'.'.$obj->tablename.$where.$order_by.$limit, $p);
			$lst = array();


			$classname=self::getClassObject();

			while($fields = $db->fetchrow($res)){
				// $o = self::getNewObject();
				$o = new $classname;
				$o->openFromResultSet($fields);
				$lst[$o->getId()] = $o;
			}

			if(count($lst) == 1 && $single) $lst = array_shift($lst);//Retourne l'objet tel quel
			return $lst;
		}
		else return null;
	}

	public static function init(){
		static::$pick = ' * ';
		static::$pick_cpt = 0;
		static::$pick_index = null;
		static::$pick_single = false;
		static::$q_params = array();
		static::$conditions = ' ';
		static::$order = ' ';
		static::$limit = ' ';
		static::$group = ' ';
		static::$joins = ' ';

		return new static;
	}

	/*
	@cols : liste des colonnes à selectionner
	@pivot : clef optionnelle à utiliser parmi les cols pour indexer le tableau de résultat final
	*/
	public static function pick($cols, $pivot = null, $single = false){
		static::$pick_index = $pivot;
		static::$pick_single = $single;
		if(!empty($cols)) static::$pick = ' ';
		if( ! is_array($cols) ){//le nom d'une colonne direct
			static::$pick = $cols;
			$tab = explode(',', $cols);
			static::$pick_cpt = count($tab);
		}
		else{
			$first = true;
			static::$pick_cpt = count($cols);
			foreach($cols as $c){
				if(!$first) static::$pick .= ', ';
				static::$pick .= $c;
				if($first) $first = false;
			}
		}
		return new static;
	}

	public function get_static_joins(){
		return self::$joins;
	}

	public static function joins($joins){
		if(!empty($joins) && is_array($joins)){
			$me = self::getNewObject();
			$first = $joins[0];
			if( ! is_array($first)){//c'est que c'est direct une relation
				if(in_array(count($joins), array(2,3) ) ){//Alias, kind[left, inner, outer], et joins optionnel en cascade
					$alias = $first;
					$type = $joins[1];
					$rel = dims::getInstance()->getRelationAs(get_called_class(), $alias);
					if(!empty($rel) && in_array(strtolower($type), array('inner', 'left', 'outer'))){
						$mbc = dims::getInstance()->getMBClassDataFromID($rel['id_class_to']);
						$remote = new $mbc['classname'];
						if($rel['type'] != mb_object_relation::MB_RELATION_HAS_MANY_THROUGH){
							static::$joins .= " ".strtoupper($type)." JOIN ".$remote->tablename." ON ".$remote->tablename.".".$rel['to']." = ".$me->tablename.".".$rel['on']." ";
							if(isset($joins[2]) && is_array($joins[2])){//récursion sur l'include interne
								//dans ce cas on doit intégrer en cascade les relations sur l'objet concernés
								$temp = static::$joins;
								static::$joins = ' ';
								$join = $remote->joins($joins[2]);
								$temp .= $join->get_static_joins();
								static::$joins = $temp;
							}
						}
					}
				}
			}
			else{
				foreach($joins as $incl){
					$temp = static::$joins;
					static::$joins = ' ';
					$join = static::joins($incl);
					$temp .= $join->get_static_joins();
					static::$joins = $temp;
				}
			}
		}
		return new static;
	}

	public static function conditions($conditions, $params = array()){
		static::$conditions = ' WHERE ';
		if(empty($conditions)) static::$conditions = '';
		else{
			if( ! is_array($conditions) ){//c'est qu'on envoie le contenu du WHERE tel quel
				static::$conditions .= $conditions;
				foreach($params as $key => $val){
					static::$q_params[$key] = $val;
				}
			}
			else{
				$first = true;
				$columns = array();

				foreach($conditions as $col => $cond){
					//génération du nom de la variable pdo
					if( ! isset($columns[$col])){
						$columns[$col] = 0;
					}
					else $columns[$col] ++;
					$pdo_param = ':'.str_replace('.', '__',$col).$columns[$col];

					if(!$first) static::$conditions .= ' AND ';
					if( ! is_array($cond)){
						static::$conditions .= $col.' = '.$pdo_param;
						static::$q_params[$pdo_param] = $cond;
					}
					else{// intégration d'un opérateur et de valeurs
						$op = $cond['op'];
						$val = $cond['value'];//peut être un tableau
						if(isset($op)){//Cyril : on ne teste pas $val parce qu'on pourrait vouloir tester IS NULL ou IS NOT NULL
							switch(strtolower($op)){
								default://ex LIKE
									static::$conditions .= $col .' '.$op.' '.$pdo_param;
									static::$q_params[$pdo_param] = $val;
									break;
								case 'in':
									$impl = dims_db::getParamsFromArray($val, str_replace('.', '__',$col).$columns[$col], static::$q_params);
									static::$conditions .= $col .' IN ('.$impl.')';
									break;
								case 'not in':
									$impl = dims_db::getParamsFromArray($val, str_replace('.', '__',$col).$columns[$col], static::$q_params);
									static::$conditions .= $col .' NOT IN ('.$impl.')';
									break;
								case 'between':
									static::$conditions .= $col . ' BETWEEN '.$pdo_param.'1 AND '.$pdo_param.'2';
									static::$q_params[$pdo_param.'1'] = $val[0];
									static::$q_params[$pdo_param.'2'] = $val[1];
									break;
							}
						}
					}
					static::$conditions .= ' ';//pour les éventuelles erreurs de syntaxes
					if($first) $first = false;
				}
			}
		}
		return new static;
	}

	public static function order($order){
		if(!empty($order)){
			static::$order = ' ORDER BY '.$order;
		}
		return new static;
	}

	public static function group($group){
		if(!empty($group)){
			static::$group = ' GROUP BY '.$group;
		}
		return new static;
	}

	public static function limit($start = null, $qte = null){
		if( ! empty($start) && ! empty($qte)){
			static::$limit = ' LIMIT :l_start, :l_qte ';
			static::$q_params[':l_start'] = array( 'value' => $start, 'type' => PDO::PARAM_INT );
			static::$q_params[':l_qte'] = array( 'value' => $qte, 'type' => PDO::PARAM_INT );
		}
		else if( ! empty($start) && empty($qte)){#Petit hack, on utilise la donnée comme si c'était la quantité
			static::$limit = ' LIMIT :l_qte ';
			static::$q_params[':l_qte'] = array( 'value' => $start, 'type' => PDO::PARAM_INT );
		}
		else if( empty($start) && ! empty($qte)){
			static::$limit = ' LIMIT :l_qte ';
			static::$q_params[':l_qte'] = array( 'value' => $qte, 'type' => PDO::PARAM_INT );
		}
		return new static;
	}

	/* execution de la requête */
	public static function run($only_resultset = false, $pivot = 'id', $unique_as_object = false, $debug = false){
		$db = dims::getInstance()->getDb();
		$obj = self::getNewObject();
		$sel = "SELECT ".static::$pick." FROM ".$obj->getDatabase().'.'.$obj->tablename.' '.static::$joins.' '.static::$conditions.' '.static::$group.' '.static::$order.' '.static::$limit;
		if($debug){
			dims_print_r($sel);
			dims_print_r(static::$q_params);
		}
		$res = $db->query($sel, static::$q_params);
		if( $only_resultset ){
			static::init();
			return $res;
		}
		else{
			$lst = array();
			while($fields = $db->fetchrow($res)){
				if(trim(static::$pick) != '*' && trim(static::$pick) != $obj->tablename.'.*'){
					if(static::$pick_cpt > 1){
						if(!empty(static::$pick_index) && isset($fields[static::$pick_index])){
							if(!static::$pick_single) $lst[$fields[static::$pick_index]][] = $fields;
							else $lst[$fields[static::$pick_index]] = $fields; //le tableau n'est plus indexé 0 .. 1 .. N
						}
						else $lst[] = $fields;
					}
					else{//on a demandé qu'une seule colonne, donc on peut renvoyer directement la valeur sans la clef
						$first = current($fields);
						if(!empty(static::$pick_index) && isset($fields[static::$pick_index])){
							$lst[$first] = $first; //c'est forcément la colonne en question
						}
						else $lst[] = $first;
					}
				}
				else{
					//$o = self::getNewObject();
					// Pat 29/12/2013, gros gain de performance
					$o = clone $obj;
					$o->openFromResultSet($fields);
					if($pivot == 'id') $lst[$o->getId()] = $o;
					else $lst[$o->get($pivot)] = $o;//Note : si la colonne n'est pas unique, ça va s'auto écraser à la manière d'un group by. A utiliser avec précaution donc
				}
			}
			static::init();
			if($unique_as_object && count($lst) == 1){
				return current($lst);
			}
			else return $lst;
		}
	}

	//Cyril - 29/08/2013 - Méthode permettant de préparer un objet new sans l'enregistrer
	public static function build($params = array()){
		$obj = self::getNewObject();
		$obj->init_description(true);
		$obj->setugm();

		if(!empty($params)){
			foreach($params as $field => $value){
				if(array_key_exists($field, $obj->fields)){
					$obj->set($field, $value);
				}
			}
		}
		return $obj;
	}

	//Cyril - 19/04/2013 - Méthode qui permet de retrouver un objet ou de le créer s'il n'existe pas
	public static function find_or_create_by($search){
		$obj = self::find_by($search, null, 1);//on recherche 1 objet
		if( !empty($obj) && ! is_null($obj)){
			return $obj;
		}
		else{
			$obj = self::getNewObject();
			$obj->init_description(true);
			$obj->setugm();
			foreach($search as $key => $value){
				if( ! is_array($value) ){
					$obj->set($key, $value);
				}
				else return false;
			}
			$obj->save();
			return $obj;
		}
	}

	//Cyril - 04/09/2013 - Méthode qui retourne le ou les objets liés par une relation à $his
	public function rel($relation_alias, $params = array()){
		$defaults = array();
		$defaults['where'] = array();
		$defaults['order'] = null;
		$defaults['limit_start'] = null;
		$defaults['limit_end'] = null;

		$p = array_merge($defaults, $params);

		$dims = dims::getInstance();
		$rel = $dims->getRelationAs(get_class($this), $relation_alias);
		if( ! empty($rel) ){
			$mbc = $dims->getMBClassDataFromID($rel['id_class_to']);
			$obj = new $mbc['classname'];
			switch($rel['type']){
				case mb_object_relation::MB_RELATION_HAS_MANY:
					$search = array_merge(array($rel['to'] => $this->get($rel['on'])) , $p['where']);
					return $obj->find_by($search, $p['order'], $p['limit_start'], $p['limit_end']);
					break;
				case mb_object_relation::MB_RELATION_HAS_ONE:
				case mb_object_relation::MB_RELATION_BELONGS_TO:
					$search = array_merge(array($rel['to'] => $this->get($rel['on'])) , $p['where']);
					return $obj->find_by($search, $p['order'], 1);
					break;
				case mb_object_relation::MB_RELATION_HAS_MANY_THROUGH:
					//d'abord on chope la relation many sur le through
					$intermediaires = $this->rel($rel['through']);

					if(!empty($intermediaires)){
						//pour éviter un parcours des objets
						if(is_array($intermediaires) && $rel['on'] == 'id'){
							$values = array_keys($intermediaires);
						}
						else{
							$values = array();
							if(is_array($intermediaires)){
								foreach($intermediaires as $id => $inter){
									$values[$inter->get($rel['on'])] = $inter->get($rel['on']);
								}
							}
							else $values[$intermediaires->get($rel['on'])] = $intermediaires->get($rel['on']);
						}
						$search = array_merge(array($rel['to'] => $values) , $p['where']);
						return $obj->find_by($search, $p['order'], $p['limit_start'], $p['limit_end']);
					}
					else return null;
					break;
				default:
					return null;
					break;
			}
		}
		else return null;

	}

	public function get($field){
		return (array_key_exists($field, $this->fields))?$this->fields[$field]:null;
	}

	public function set($field, $value = ""){
		if( array_key_exists($field, $this->fields) )
			$this->fields[$field] = $value;
	}

	public function __clone() {
		$this->setNew(true);
		if(empty($this->idfields)) {
			$this->fields['id'] = 0;
		}
		else {
			foreach ($this->idfields as $idField) {
				$this->fields[$idField] = 0;
			}
		}
	}

	/*
	 * Fonction permettant de collecter les adresses provenant du contact rattaches
	*/
	public function getAllAdresses() {
		$listadr = array();
		if(isset($this->id_globalobject) && array_key_exists('id_globalobject', $this->fields)){
			require_once DIMS_APP_PATH.'modules/system/class_address.php';
			require_once DIMS_APP_PATH.'modules/system/class_address_type.php';

			$sel = "SELECT		a.*, t.*, lk.default
					FROM		".address::TABLE_NAME." a
					INNER JOIN	".address_link::TABLE_NAME." lk
					ON			lk.id_goaddress = a.id_globalobject
					INNER JOIN	".address_type::TABLE_NAME." t
					ON			t.id = lk.id_type
					WHERE		lk.id_goobject = :idgo";
			$params = array(
				':idgo' => array('type' => PDO::PARAM_INT, 'value' => $this->get('id_globalobject')),
			);
			$db = dims::getInstance()->getDb();
			$res = $db->query($sel,$params);
			$rows = $db->split_resultset($res);
			foreach ($rows as $r) {
				if(!isset($listadr[$r['t']['id']])){
					$listadr[$r['t']['id']]['obj'] = new address_type();
					$listadr[$r['t']['id']]['obj']->openFromResultSet($r['t']);
				}
				$add = new address();
				$add->openFromResultSet($r['a']);
				$add->setIsDefault($r['lk']['default']);
				$listadr[$r['t']['id']]['add'][] = $add;
			}
		}

		return $listadr;
	}
	public function getDefaultAddress() {
		$listadr = array();
		if(isset($this->id_globalobject) && array_key_exists('id_globalobject', $this->fields)){
			require_once DIMS_APP_PATH.'modules/system/class_address.php';
			require_once DIMS_APP_PATH.'modules/system/class_address_type.php';

			$sel = "SELECT		a.*, t.*, lk.default
					FROM		".address::TABLE_NAME." a
					INNER JOIN	".address_link::TABLE_NAME." lk
					ON			lk.id_goaddress = a.id_globalobject
					INNER JOIN	".address_type::TABLE_NAME." t
					ON			t.id = lk.id_type
					WHERE		lk.id_goobject = :idgo
					AND			lk.default = 1";
			$params = array(
				':idgo' => array('type' => PDO::PARAM_INT, 'value' => $this->get('id_globalobject')),
			);
			$db = dims::getInstance()->getDb();
			$res = $db->query($sel,$params);
			$rows = $db->split_resultset($res);
			foreach ($rows as $r) {
				if(!isset($listadr[$r['t']['id']])){
					$listadr[$r['t']['id']]['obj'] = new address_type();
					$listadr[$r['t']['id']]['obj']->openFromResultSet($r['t']);
				}
				$add = new address();
				$add->openFromResultSet($r['a']);
				$add->setIsDefault($r['lk']['default']);
				$listadr[$r['t']['id']]['add'][] = $add;
			}
		}

		return $listadr;
	}

	//méthode qui permet de réouvrir un objet pour recharger les données de la base
	public function reload(){
		$conditions = array();
		foreach($this->idfields as $idx => $field){
			$conditions[$field] = $this->get($field);
		}
		$obj = self::find_by($conditions, null, 1);
		$this->openFromResultSet($obj->fields);
		return $this;
	}

	public static function extractfield($ddocollection, $field) {
		$fields = array();

		if(is_array($ddocollection)) {
			foreach($ddocollection as $object) {
				if($object instanceof self) {
					if(isset($object->fields[$field])) {
						$fields[$object->fields[$field]] = $object->fields[$field];
					}
				}
			}
		}

		return $fields;
	}
}
